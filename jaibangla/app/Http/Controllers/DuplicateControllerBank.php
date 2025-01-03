<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\District;
use App\Scheme;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;
use App\DataSourceCommon;
use App\getModelFunc;
use Illuminate\Support\Facades\Crypt;
use App\RejectRevertReason;
use App\AadharDuplicateTrail;
use App\SubDistrict;
use App\Taluka;
use App\DocumentType;
use Illuminate\Support\Facades\Storage;
use App\SchemeDocMap;
use File;
use App\UpdateBenDetails;
use App\BankDetails;
use App\Configduty;
use App\UrbanBody;
use App\Ward;
use App\GP;
use App\Helpers\AuthChecker;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Helpers\DupCheck;
use Illuminate\Support\Facades\Route;


class DuplicateControllerBank extends Controller
{
    public $source_type;
    public $ben_status;
    public $scheme_id;

    public function __construct()
    {
        // $this->scheme_id = 20;
        $this->source_type = 'ss_nfsa';
        $this->ben_status = -97;
        //return redirect("/")->with('error', '');
    }
    function dedupBankCron(Request $request)
    {
        $logmessage = "";
        $fileLocation = 'DuplicateBank/log.txt';
        try {

            $logmessage .= "Duplicate Bank Controller Cron has been started on " . date("Y-m-d h:i:s") . "." . "\n";
            $query = "insert into pension.ben_payment_details_bank_code_dup(id, dist_code, ben_fname, ben_mname, ben_lname, gender, dob, 
            ben_age, caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
            mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, 
            ration_card_no, ahl_tin, aadhar_no, epic_voter_id, block_ulb_code, block_ulb_name, 
            gp_ward_code, gp_ward_name, house_premise_no, pincode, residency_period, mobile_no, 
            email, bank_code, bank_name, branch_name, bank_ifsc, created_by, created_by_level, 
            created_by_dist_code, created_by_local_body_code, scheme_id, lot_generated, payment_count, 
            last_paid_yymm, next_level_role_id,dup_bank,dup_bank_pending,m_date)
         select id, dist_code, ben_fname, ben_mname, ben_lname, gender, dob, 
         ben_age, caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
         mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, 
         ration_card_no, ahl_tin, aadhar_no, epic_voter_id, block_ulb_code, block_ulb_name, 
         gp_ward_code, gp_ward_name, house_premise_no, pincode, residency_period, mobile_no, 
         email, bank_code, bank_name, branch_name, bank_ifsc, created_by, created_by_level, 
         created_by_dist_code, created_by_local_body_code, scheme_id, lot_generated, payment_count, 
         last_paid_yymm, '-97', dup_bank,dup_bank_pending,'" . date("Y-m-d h:i:s") . "'
         from pension.beneficiary where dup_bank=1 on conflict(id) do nothing";
            DB::statement($query);
            $logmessage .= " Duplicate Bank Controller Cron has been completed on " . date("Y-m-d h:i:s") . "." . "\n";
            Storage::append($fileLocation, $logmessage);
            Storage::put($fileLocation);
        } catch (\Exception $e) {
            $logmessage .= " Exception:- " . $e->getMessage() . " on " . date("Y-m-d h:i:s") . "." . "\n";
            Storage::append($fileLocation, $logmessage);
            Storage::put($fileLocation);
        }
    }
    function dedupBankSelectScheme(Request $request)
    {
        $this->middleware('auth');
        $is_verifier = AuthChecker::VerifierChecker();
        $is_approver = AuthChecker::ApproverChecker();
        // $designation_id = Auth::user()->designation_id;
        $userId = AuthChecker::getUserId();
        if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disabled. ');
        }
        $sceme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id  in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
        $errormsg = Config::get('constants.errormsg');
        return view(
            'DuplicateBank.dedupBankSelectScheme',
            [
                'sceme_list' => $sceme_list,
                'sessiontimeoutmessage' => $errormsg['sessiontimeOut'],
                'is_verifier' => $is_verifier,
            ]
        );
    }
    function dedupBankListView(Request $request)
    {
        $this->middleware('auth');
        $scheme_id = $request->scheme_id;
        if ($scheme_id == 8 || $scheme_id == 9) {
            return redirect("/deDupBankListLPP");
        }
        if (empty($scheme_id)) {
            return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
        }
        if (!ctype_digit($scheme_id)) {
            return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
        }
        $urban_body_code = NULL;
        $district_code = NULL;
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        $errormsg = Config::get('constants.errormsg');
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $is_urban = $roleObj['is_urban'];
                $district_code = $roleObj['district_code'];
                $mapping_level = $roleObj['mapping_level'];
                if ($roleObj['is_urban'] == 1) {
                    $urban_body_code = $roleObj['urban_body_code'];
                } else {
                    $urban_body_code = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if (AuthChecker::VerifierChecker()) {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if (AuthChecker::VerifierChecker()) {
            $verifier_condition = ' and p.created_by_local_body_code=' . $urban_body_code;
        } else {
            $verifier_condition = '';
        }
        if ($is_active == 0 || empty($district_code)) {
            return redirect("/")->with('error', 'User Disabled. ');
        }

        $scheme_row = Scheme::where('id', $scheme_id)->first();

        $query = "select A.bank_code,A.cnt
            from
            (
            select bank_code,count(1) as cnt
            from pension.beneficiaries where scheme_id=" . $scheme_id . " and  next_level_role_id=" . $this->ben_status . " 
            group  by bank_code 
            ) as A WHERE EXISTS
                (SELECT 1
                 FROM pension.beneficiaries p
                 WHERE p.is_approved IN (0,2) AND p.bank_code = A.bank_code
                   AND p.created_by_dist_code=" . $district_code . " $verifier_condition) order by cnt desc";
        $rows = DB::select($query);
        $errormsg = Config::get('constants.errormsg');
        return view(
            'DuplicateBank.duplicateBankListView',
            [
                'district_code' => $district_code,
                'data' => $rows,
                'scheme_id' => $scheme_id,
                'scheme_name' => $scheme_row->scheme_name,
                'sessiontimeoutmessage' => $errormsg['sessiontimeOut']
            ]
        );
    }
    public function dedupBankView(Request $request)
    {
        $this->middleware('auth');
        $scheme_id = $request->scheme_id;

        if (empty($scheme_id)) {
            return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
        }
        if (!ctype_digit($scheme_id)) {
            return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
        }
        // dd($scheme_id);
        $scheme_row = Scheme::where('id', $scheme_id)->first();
        $designation_id = Auth::user()->designation_id;
        $urban_body_code = NULL;
        $district_code = NULL;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $is_urban = $roleObj['is_urban'];
                $district_code = $roleObj['district_code'];
                if ($roleObj['is_urban'] == 1) {
                    $urban_body_code = $roleObj['urban_body_code'];
                } else {
                    $urban_body_code = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if (AuthChecker::VerifierChecker()) {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if (AuthChecker::VerifierChecker()) {
            $verifier_condition = ' and p.created_by_local_body_code=' . $urban_body_code;
        } else {
            $verifier_condition = '';
        }
        if ($is_active == 0 || empty($district_code)) {
            return redirect("/")->with('error', 'User Disabled. ');
        }
        // dd($request->bank_code);
        if (empty($request->bank_code)) {
            return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', 'Account No.not found');
        }
        $reject_revert_reason = RejectRevertReason::where('status', true)->get();
        //$reject_revert_reason = array();
        $block = Taluka::get();
        $UrbanBody = UrbanBody::get();
        $Ward = Ward::get();
        $GP = GP::get();

        $query = "select * FROM pension.ben_payment_details_bank_code_dup A
                 WHERE scheme_id=" . $scheme_id . " and next_level_role_id=" . $this->ben_status . "  and trim(A.bank_code) = '" . trim($request->bank_code) . "' order by ben_fname";

        $rows = DB::connection('pgsql_mis')->select($query);

        $ben_list = array();
        $i = 0;
        foreach ($rows as $arr) {
            $allowed = 0;
            $ben_list[$i]['dist_code'] = $arr->created_by_dist_code;
            $ben_list[$i]['local_body_code'] = $arr->created_by_local_body_code;
            $ben_list[$i]['application_id'] = $arr->id;
            $ben_list[$i]['ben_name'] = $arr->ben_fname . ' ' . $arr->ben_mname . ' ' . $arr->ben_lname;
            $ben_list[$i]['mobile_no'] = $arr->mobile_no;
            $ben_list[$i]['revert_remarks'] = $arr->revert_remarks;
            $local_body = '';
            if (!empty($arr->rural_urban_id)) {
                if ($arr->rural_urban_id == 1) {
                    $local_body = $UrbanBody->where('urban_body_code', $arr->block_ulb_code)->first();
                    if (!empty($local_body)) {
                        $ben_list[$i]['local_body_name'] = trim($local_body->urban_body_name);
                    } else {
                        $ben_list[$i]['local_body_name'] = 'NA';
                    }
                    $gp_ward = $Ward->where('urban_body_ward_code', $arr->gp_ward_code)->first();
                    if (!empty($gp_ward)) {
                        $ben_list[$i]['gp_ward_name'] = trim($gp_ward->urban_body_ward_name);
                    } else {
                        $ben_list[$i]['gp_ward_name'] = 'NA';
                    }
                } else {
                    $local_body = $block->where('block_code', $arr->block_ulb_code)->first();
                    $gp_ward = $GP->where('gram_panchyat_code', $arr->gp_ward_code)->first();
                    if (!empty($local_body)) {
                        $ben_list[$i]['local_body_name'] = trim($local_body->block_name);
                    } else {
                        $ben_list[$i]['local_body_name'] = 'NA';
                    }
                    if (!empty($gp_ward)) {
                        $ben_list[$i]['gp_ward_name'] = trim($gp_ward->gram_panchyat_name);
                    } else {
                        $ben_list[$i]['gp_ward_name'] = 'NA';
                    }
                }
            } else {
                $ben_list[$i]['local_body_name'] = 'NA';
                $ben_list[$i]['gp_ward_name'] = 'NA';
            }

            if (AuthChecker::ApproverChecker()) {
                if ($arr->created_by_dist_code == $district_code) {
                    $allowed = 1;
                } else {
                    $allowed = 0;
                }
            } else if (AuthChecker::VerifierChecker()) {
                if ($arr->created_by_dist_code == $district_code && $arr->created_by_local_body_code == $urban_body_code) {
                    $allowed = 1;
                } else {
                    $allowed = 0;
                }
            }
            $ben_list[$i]['allowed'] = $allowed;
            $i++;
        }
        //dd($ben_list);
        $errormsg = Config::get('constants.errormsg');
        return view(
            'DuplicateBank.dedupBankView',
            [
                'reject_revert_reason' => $reject_revert_reason,
                'revert_reason' => $rows[0]->revert_remarks,
                'district_code' => $district_code,
                'data' => $ben_list,
                'bank_ifsc' => $request->bank_ifsc,
                'bank_code' => $request->bank_code,
                'scheme_id' => $scheme_id,
                'scheme_name' => $scheme_row->scheme_name,
                'designation_id' => $designation_id,
                'sessiontimeoutmessage' => $errormsg['sessiontimeOut']
            ]
        );
    }
    public function dupBankReject(Request $request)
    {
        $this->middleware('auth');
        $scheme_id = $request->scheme_id;

        if (empty($scheme_id)) {
            return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
        }
        if (!ctype_digit($scheme_id)) {
            return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
        }
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        $urban_body_code = NULL;
        $district_code = NULL;
        $errormsg = Config::get('constants.errormsg');
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $is_urban = $roleObj['is_urban'];
                $district_code = $roleObj['district_code'];
                $mapping_level = $roleObj['mapping_level'];
                if ($roleObj['is_urban'] == 1) {
                    $urban_body_code = $roleObj['urban_body_code'];
                } else {
                    $urban_body_code = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if (AuthChecker::VerifierChecker()) {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if (AuthChecker::VerifierChecker()) {
            $verifier_condition = ' and p.created_by_local_body_code=' . $urban_body_code;
        } else {
            $verifier_condition = '';
        }
        if ($is_active == 0 || empty($district_code)) {
            return redirect("/")->with('error', 'User Disabled. ');
        }
        $bank_code = $request->bank_code;
        $application_id = $request->application_id;
        $is_bulk = $request->is_bulk;
        $applicant_id_post = request()->input('applicantId');
        $comments = $request->comments;
        $rejected_cause = $request->reject_cause;
        if (empty($comments))
            $comments = NULL;

        if (empty($bank_code)) {
            return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', 'Bank Account No.Not Found');
        }
        if (empty($rejected_cause)) {
            return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', 'Rejected Cause Not Valid');
        }
        if (!ctype_digit($rejected_cause)) {
            return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', 'Rejected Cause Not Valid');
        }
        $scheme_row = Scheme::where('id', $scheme_id)->first();
        if (!empty($scheme_row->short_code)) {
            $schema = $scheme_row->short_code;
        } else {
            $schema = "pension";
        }
        $today = date("Y-m-d h:i:s");
        if ($is_bulk == 0) {
            // dd($scheme_id);
            if (empty($application_id)) {
                return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', ' Application Id Not Found');
            }
            if (!ctype_digit($application_id)) {
                return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', ' Application Id Not Valid');
            }

            $row_count = DB::table($schema . '.beneficiary')->where('dup_bank', 1)->where('id', $application_id)->first();
            if (empty($row_count)) {
                return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', ' Application Id Not found in Db');
            }
            //dd($scheme_id);

            try {
                DB::beginTransaction();

                $update_arr = array();
                $update_arr['next_level_role_id'] = -200;
                $update_arr['rejected_date'] = $today;
                $update_arr['rejected_by'] = $user_id;
                $update_arr['is_approved'] = 0;
                // $input = ['dup_bank'=>0,'dup_bank_pending'=>0,'rejected_by' =>  $user_id,'rejected_date' =>  $today, 'next_level_role_id' => -200, 'is_rejected' =>1,'rejected_cause' => $rejected_cause, 'comments' => $comments];
                // dd($district_code);
                // $is_saved1 = DB::table($schema . '.beneficiary')->where('created_by_dist_code', $district_code)->where('id', $application_id)->update($input);
                $is_saved2 = DB::table('pension.ben_payment_details_bank_code_dup')->whereraw("trim(bank_code)='$bank_code'")->where('id', $application_id)->where('created_by_dist_code', $district_code)->update($update_arr);
                $modelmainArch = array();
                $modelmainArch['update_code'] = -200;
                $modelmainArch['original_application_id'] = $application_id;
                $modelmainArch['scheme_id'] = $scheme_id;
                $modelmainArch['created_at'] = $today;
                $modelmainArch['user_id'] = $user_id;
                $modelmainArch['ip_address'] = $request->ip();
                $modelmainArchStatus = DB::table('update_ben_details')->insert($modelmainArch);
                $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
                if (in_array($scheme_id, $scheme_dedup_list)) {
                    $free_pending_bank_duplicate_arr = DB::select("select " . $schema . ".free_pending_bank_duplicate_data(in_scheme_id => " . $scheme_id . ", in_district_code => " . $district_code . ")");
                    $free_pending_bank_duplicate_data = $free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
                    if (!empty(trim($row_count->mobile_no))) {
                        $sp_mobile = $row_count->mobile_no;
                    } else {
                        $sp_mobile = 0;
                    }
                    $reject_dup_adjustment_arr = DB::select("select " . $schema . ".reject_dup_adjustment(
                    in_old_bank_ifsc => '" . $row_count->bank_ifsc . "', 
                    in_old_bank_code => '" . $row_count->bank_code . "', 
                    in_old_aadhar_no => '" . $row_count->aadhar_no . "', 
                    in_old_mobile_no => " . $sp_mobile . "
                    )");
                    $reject_dup_adjustment = $reject_dup_adjustment_arr[0]->reject_dup_adjustment;
                } else {
                    $reject_dup_adjustment = 1;
                    $free_pending_bank_duplicate_data = 1;
                }
                //dd($free_pending_bank_duplicate_arr);

                //    dump($is_saved2);dump($modelmainArchStatus);dump($free_pending_bank_duplicate_data);
                if ($is_saved2 && $modelmainArchStatus && $free_pending_bank_duplicate_data && $reject_dup_adjustment) {
                    DB::commit();
                    $return_text = "Beneficiary informations successfully rejected with Beneficiary Id:" . $application_id;
                    return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('success', $return_text);
                } else {
                    DB::rollback();
                    return redirect("/dedupBankView?scheme_id=" . $scheme_id . "&bank_code=" . $bank_code)->with('error', $errormsg['roolback']);
                }
            } catch (\Exception $e) {
                // if ($application_id == 420188) {
                //     dd($e);
                // }
                // dd($e);
                DB::rollback();
                //DB::connection('pgsql_payment')->rollback();
                //dd($e);
                return redirect("/dedupBankView?scheme_id=" . $scheme_id . "&bank_code=" . $bank_code)->with('error', $errormsg['roolback']);
            }
        } else if ($is_bulk == 1) {
            $applicant_id_post = request()->input('applicantId');

            $applicant_id_in = explode(',', $applicant_id_post);
            //dd($applicant_id_in);
            $arry_list = array();
            $i = 0;
            $faulty_arr = array();
            $main_arr = array();
            $all_arr = array();
            foreach ($applicant_id_in as $app) {
                array_push($all_arr, $app);
                if (!ctype_digit($app)) {
                    return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', ' Application Id Not Valid');
                }
                array_push($main_arr, $app);

                $row_count = DB::table($schema . '.beneficiary')->where('dup_bank', 1)->where('id', $app)->count('id');
                if ($row_count == 0) {
                    return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', ' Application Id Not found in Db');
                }
                $i++;
            }
            // $input = ['dup_bank'=>0,'dup_bank_pending'=>0,'rejected_by' =>  $user_id,'rejected_date' =>  $today,'next_level_role_id' => -200, 'is_rejected' =>1,'rejected_cause' => $rejected_cause, 'comments' => $comments];

            try {
                DB::beginTransaction();

                if (count($main_arr) > 0) {
                    $update_arr = array();
                    $update_arr['next_level_role_id'] = -200;
                    $update_arr['rejected_date'] = $today;
                    $update_arr['rejected_by'] = $user_id;
                    $update_arr['is_approved'] = 0;
                    // $is_saved1 = DB::table($schema . '.beneficiary')->where('dup_bank',1)->whereraw("trim(bank_code)='$bank_code'")->whereIn('id', $main_arr)->where('created_by_dist_code', $district_code)->update($input);
                    $is_saved2 = DB::table('pension.ben_payment_details_bank_code_dup')->whereraw("trim(bank_code)='$bank_code'")->whereIn('id', $main_arr)->where('created_by_dist_code', $district_code)->update($update_arr);
                }
                foreach ($all_arr as $app_row) {
                    $modelmainArch = array();
                    $modelmainArch['update_code'] = -200;
                    $modelmainArch['original_application_id'] = $app_row;
                    $modelmainArch['scheme_id'] = $scheme_id;
                    $modelmainArch['created_at'] = $today;
                    $modelmainArch['user_id'] = $user_id;
                    $modelmainArch['ip_address'] = $request->ip();
                    $modelmainArchStatus = DB::table('update_ben_details')->insert($modelmainArch);
                    $free_pending_bank_duplicate_arr = DB::select("select " . $schema . ".free_pending_bank_duplicate_data(in_scheme_id => " . $scheme_id . ", in_district_code => " . $district_code . ")");
                    $free_pending_bank_duplicate_data = $free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
                }
                DB::commit();
                return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('success', 'Applications  has been successfully rejected');
            } catch (\Exception $e) {
                //dd($e);
                DB::rollback();
                return redirect("/dedupBankView?scheme_id=" . $scheme_id . "&bank_ifsc=" . $bank_ifsc . "&bank_code=" . $bank_code)->with('error', $errormsg['roolback']);
            }
        }
    }
    public function generate_excel_list(Request $request)
    {
        try {
            $this->middleware('auth');
            $scheme_id = $request->scheme_id;
            if (empty($scheme_id)) {
                return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
            }
            if (!ctype_digit($scheme_id)) {
                return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
            }
            $is_active = 0;
            $designation_id = Auth::user()->designation_id;
            $urban_body_code = NULL;
            $district_code = NULL;
            $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == $scheme_id) {
                    $is_active = 1;
                    $mapping_level = $roleObj['mapping_level'];
                    $district_code = $roleObj['district_code'];
                    if ($roleObj['is_urban'] == 1) {
                        $urban_body_code = $roleObj['urban_body_code'];
                    } else {
                        $urban_body_code = $roleObj['taluka_code'];
                    }
                    break;
                }
            }
            if (AuthChecker::VerifierChecker()) {
                $is_active = 1;
            } else {
                $is_active = 0;
            }
            if (AuthChecker::VerifierChecker()) {
                $verifier_condition = ' and p.created_by_local_body_code=' . $urban_body_code;
            } else {
                $verifier_condition = '';
            }
            if ($is_active == 0 || empty($district_code)) {
                return redirect("/")->with('error', 'User Disabled. ');
            }
            $subdistrict = SubDistrict::get();
            $block = Taluka::get();
            $district = District::get();
            $ben_list = array();
            $i = 0;
            $query = "select P.bank_code,Q.created_by_dist_code,Q.created_by_local_body_code,
        Q.id,CONCAT(Q.ben_fname , ' ' ,Q.ben_mname , ' ' ,Q.ben_lname) 
        AS full_name,Q.mobile_no,Q.next_level_role_id from 
        (select A.bank_code,A.cnt
            from
            (
            select bank_code,count(1) as cnt
            from pension.ben_payment_details_bank_code_dup where scheme_id=" . $scheme_id . "   
            group  by bank_code 
            ) as A WHERE EXISTS
                (SELECT 1
                 FROM pension.ben_payment_details_bank_code_dup p
                 WHERE p.scheme_id=" . $scheme_id . " and  p.bank_code = A.bank_code
                   AND p.created_by_dist_code=" . $district_code . " $verifier_condition) order by cnt desc
                   ) as P JOIN  pension.ben_payment_details_bank_code_dup Q 
                   ON   trim(P.bank_code)=trim(Q.bank_code) where Q.scheme_id=" . $scheme_id . "  order by Q.bank_code,Q.ben_fname";
            $rows = DB::connection('pgsql_mis')->select($query);
            // dd($rows);
            foreach ($rows as $arr) {

                $ben_list[$i]['bank_code'] = $arr->bank_code;
                $ben_list[$i]['local_body_code'] = $arr->created_by_local_body_code;
                $ben_list[$i]['application_id'] = $arr->id;
                $ben_list[$i]['full_name'] = $arr->full_name;
                $ben_list[$i]['mobile_no'] = $arr->mobile_no;
                $district_row = $district->where('district_code', $arr->created_by_dist_code)->first();
                $ben_list[$i]['district_name'] = $district_row->district_name;
                $local_body = '';
                if (strlen($arr->created_by_local_body_code) == 5) {
                    $local_body = $subdistrict->where('sub_district_code', $arr->created_by_local_body_code)->first();
                    $ben_list[$i]['local_body_name'] = 'SubDivision-' . $local_body->sub_district_name;
                } else {
                    $local_body = $block->where('block_code', $arr->created_by_local_body_code)->first();
                    $ben_list[$i]['local_body_name'] = 'Block-' . $local_body->block_name;
                }
                if (AuthChecker::ApproverChecker()) {
                    if ($arr->created_by_dist_code == $district_code) {
                        $allowed = 1;
                    } else {
                        $allowed = 0;
                    }
                } else if (AuthChecker::VerifierChecker()) {
                    if ($arr->created_by_dist_code == $district_code && $arr->created_by_local_body_code == $urban_body_code) {
                        $allowed = 1;
                    } else {
                        $allowed = 0;
                    }
                }
                $ben_list[$i]['allowed'] = $allowed;
                if ($allowed == 1) {
                    if ($arr->next_level_role_id == -200) {
                        $ben_list[$i]['status_des'] = 'rejected';
                    } else if ($arr->next_level_role_id == 101) {
                        $ben_list[$i]['status_des'] = 'Bank Information has been updated with new one';
                    } else if ($arr->next_level_role_id == 200) {
                        $ben_list[$i]['status_des'] = 'Bank Information has been updated with old one';
                    } else {
                        $ben_list[$i]['status_des'] = 'Need to modify';
                    }
                } else {
                    $ben_list[$i]['status_des'] = 'related to other.no action required.';
                }
                $i++;
            }
            $scheme_row = Scheme::where('id', $scheme_id)->first();
            $filename = "Bank_Account_Duplicate_" . $scheme_row->scheme_name . "_" . date('d/m/Y') . ".xls";
            header("Content-Type: application/xls");
            header("Content-Disposition: attachment; filename=" . $filename);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo '<table border="1">';
            echo '<tr><th>Bank Account No.</th><th>Beneficiary Id</th><th>Beneficiary name</th><th>Mobile No.</th><th>District</th><th>Block/SubDivision</th><th>Status</th></tr>';
            if (count($ben_list) > 0) {
                foreach ($ben_list as $row) {
                    $bank_code = (string) $row['bank_code'];
                    if (!empty($bank_code))
                        $f_bank_code = "'$bank_code'";
                    else
                        $f_bank_code = $bank_code;
                    echo "<tr><td>" . $f_bank_code . "</td><td>" . $row['application_id'] . "</td><td>" . $row['full_name'] . "</td><td>" . $row['mobile_no'] . "</td><td>" . $row['district_name'] . "</td><td>" . $row['local_body_name'] . "</td><td>" . $row['status_des'] . "</td></tr>";
                }
            } else {
                echo '<tr><td colspan="8">No Records found</td></tr>';
            }
            echo '</table>';
        } catch (\Exception $e) {
            //dd($e);
            return redirect("/dedupBankSelectScheme")->with('error', 'Some Error.. please try later');
        }
    }
    public function generate_excel_list_state(Request $request)
    {
        $this->middleware('auth');
        $scheme_id = $this->scheme_id;
        $designation_id = Auth::user()->designation_id;
        if ($designation_id != 'HOD') {
            return redirect("/")->with('error', 'User Disabled. ');
        }
        $districts = District::get();
        return view(
            'DuplicateBank.excelState',
            [
                'districts' => $districts,
                'scheme_id' => $this->scheme_id
            ]
        );
    }
    public function generate_excel_list_state_download(Request $request)
    {
        $this->middleware('auth');
        $scheme_id = $this->scheme_id;
        // $designation_id = Auth::user()->designation_id;
        if (!AuthChecker::HODChecker()) {
            return redirect("/")->with('error', 'User Disabled. ');
        }
        $districts = District::get();
        $district_code = $request->district_code;
        if (empty($district_code)) {
            return redirect("/DupBankAccounttExcelState")->with('error', 'District Code Not Found');
        }
        if (!ctype_digit($district_code)) {
            return redirect("/DupBankAccounttExcelState")->with('error', 'District Not Valid');
        }
        $district_row = District::where('district_code', $district_code)->first();
        if (empty($district_row)) {
            return redirect("/dedupBankListView")->with('error', 'District Not Valid');
        }
        $getModelFunc = new getModelFunc();
        $schemaname = $getModelFunc->getSchemaDetails();
        $subdistrict = SubDistrict::get();
        $block = Taluka::get();
        $ben_list = array();
        $i = 0;
        $query = "select P.last_ifsc,P.last_accno,Q.dist_code,Q.local_body_code,Q.ben_id,Q.application_id,Q.ben_name,Q.mobile_no,Q.ss_card_no,Q.faulty_status from 
            (select A.last_ifsc,A.last_accno,A.cnt
                from
                (
                select last_ifsc,last_accno,count(1) as cnt
                from " . $schemaname . ".ben_payment_details_bank_code_dup where ben_status=" . $this->ben_status . "
                group  by last_ifsc,last_accno having(count(1)>1)
                ) as A WHERE EXISTS
                    (SELECT 1
                     FROM " . $schemaname . ".ben_payment_details_bank_code_dup p
                     WHERE p.last_ifsc = A.last_ifsc and p.last_accno = A.last_accno
                       AND p.dist_code=" . $district_code . ") order by cnt desc
                       ) as P JOIN  " . $schemaname . ".ben_payment_details_bank_code_dup Q 
                       ON  trim(P.last_ifsc)=trim(Q.last_ifsc) and trim(P.last_accno)=trim(Q.last_accno) where Q.ben_status=" . $this->ben_status . " order by Q.last_ifsc,Q.last_accno,Q.ben_name";
        $rows = DB::connection('pgsql_payment')->select($query);

        foreach ($rows as $arr) {

            $ben_list[$i]['bank_code'] = $arr->last_accno;
            $ben_list[$i]['bank_ifsc'] = $arr->last_ifsc;
            $ben_list[$i]['local_body_code'] = $arr->local_body_code;
            $ben_list[$i]['application_id'] = $arr->application_id;
            $ben_list[$i]['ben_id'] = $arr->ben_id;
            $ben_list[$i]['ben_name'] = $arr->ben_name;
            $ben_list[$i]['mobile_no'] = $arr->mobile_no;
            $ben_list[$i]['ss_card_no'] = $arr->ss_card_no;
            $ben_list[$i]['faulty_status'] = intval($arr->faulty_status);
            $district_row = $districts->where('district_code', $arr->dist_code)->first();
            $ben_list[$i]['district_name'] = $district_row->district_name;
            $local_body = '';
            if (strlen($arr->local_body_code) == 5) {
                $local_body = $subdistrict->where('sub_district_code', $arr->local_body_code)->first();
                $ben_list[$i]['local_body_name'] = 'SubDivision-' . $local_body->sub_district_name;
            } else {
                $local_body = $block->where('block_code', $arr->local_body_code)->first();
                $ben_list[$i]['local_body_name'] = 'Block-' . $local_body->block_name;
            }
            $i++;
        }

        $filename = "Bank_Account_Duplicate" . "-" . trim($district_row->district_name) . "-" . date('d/m/Y') . ".xls";
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Pragma: no-cache");
        header("Expires: 0");
        echo '<table border="1">';
        echo '<tr><th>Bank IFSC</th><th>Bank Account No.</th><th>Applicant Id</th><th>Beneficiary Id</th><th>Beneficiary name</th><th>Mobile No.</th><th>District</th><th>Block/SubDivision</th><th>Swasthyasathi Card No.</th></tr>';
        if (count($ben_list) > 0) {
            foreach ($ben_list as $row) {
                $sws_card_no = (string) $row['ss_card_no'];
                if (!empty($sws_card_no))
                    $ss_card_no = "'$sws_card_no'";
                else
                    $ss_card_no = $sws_card_no;

                $bank_code = (string) $row['bank_code'];
                if (!empty($bank_code))
                    $f_bank_code = "'$bank_code'";
                else
                    $f_bank_code = $bank_code;
                echo "<tr><td>" . $row['bank_ifsc'] . "</td><td>" . $f_bank_code . "</td><td>" . $row['application_id'] . "</td><td>" . $row['ben_id'] . "</td><td>" . $row['ben_name'] . "</td><td>" . $row['mobile_no'] . "</td><td>" . $row['district_name'] . "</td><td>" . $row['local_body_name'] . "</td><td>" . $ss_card_no . "</td></tr>";
            }
        } else {
            echo '<tr><td colspan="9">No Records found</td></tr>';
        }
        echo '</table>';
    }
    public function dedupBankUpdate(Request $request)
    {

        $this->middleware('auth');
        $scheme_id = $request->scheme_id;
        if (empty($scheme_id)) {
            return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
        }
        if (!ctype_digit($scheme_id)) {
            return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
        }
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        $errormsg = Config::get('constants.errormsg');
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $is_urban = $roleObj['is_urban'];
                $district_code = $roleObj['district_code'];
                $mapping_level = $roleObj['mapping_level'];
                if ($roleObj['is_urban'] == 1) {
                    $urban_body_code = $roleObj['urban_body_code'];
                } else {
                    $urban_body_code = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if (AuthChecker::VerifierChecker()) {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if (AuthChecker::VerifierChecker()) {
            $verifier_condition = ' and p.created_by_local_body_code=' . $urban_body_code;
        } else {
            $verifier_condition = '';
        }
        if ($is_active == 0 || empty($district_code)) {
            return redirect("/")->with('error', 'User Disabled. ');
        }
        $bank_code = $request->bank_code;
        $application_id = $request->application_id;

        $is_faulty = $request->is_faulty;

        if (empty($bank_code)) {
            return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', 'Bank Account No.Not Found');
        }
        if (empty($application_id)) {
            return redirect("/dedupBankView?scheme_id=" . $scheme_id . "&bank_code=" . $bank_code)->with('error', 'Application ID Not Found');
        }
        if (!ctype_digit($application_id)) {
            return redirect("/dedupBankView?scheme_id=" . $scheme_id . "&bank_code=" . $bank_code)->with('error', 'Application ID Not Valid');
        }

        $scheme_row = Scheme::where('id', $scheme_id)->first();
        if (!empty($scheme_row->short_code)) {
            $schema = $scheme_row->short_code;
        } else {
            $schema = "pension";
        }

        $row = DB::table($schema . '.beneficiary')->where('dup_bank', 1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code')->first();

        $dupTableRow = DB::table('pension.ben_payment_details_bank_code_dup')->where('next_level_role_id', $this->ben_status)->where('id', $application_id)->first();

        if (empty($row->id)) {
            return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', ' Application Id Not found in Db');
        }
        if (in_array($scheme_id, array(8, 9))) {
            $docs_uploaded = collect([]);
        } else {
            // $docs_uploaded = DB::table($schema . '.ben_docs')->where('ben_id', $row->id)->select('id', 'ben_id', 'doc_type_id', 'doc_name', 'doc_type_name')->get()->pluck('doc_type_id');
            $docs_uploaded = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('created_by_dist_code', $district_code)->where('beneficiary_id', $row->id)->select('id', 'beneficiary_id', 'document_type', 'doc_type_name', 'attched_document')->get()->pluck('document_type');
        }

        $doc_profile = DocumentType::select('id')->where('is_profile_pic', TRUE)->first();
        //$profileImagedata = $docs->where('doc_type_id', $doc_profile->id)->where('ben_id', $application_id)->first();
        //$encolserdata = $docs->where('application_id', $application_id)->get()->pluck('doc_type_id');
        //dd($profileImagedata->toArray());
        if (!empty($docs)) {
            $encolserCount = 1;
        }
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->first();

        if (!empty($doc_id_list)) {
            $doc_id_list = $doc_id_list->toArray();
        }
        // dd($doc_id_list['doc_list_man']);
        if (isset($doc_id_list['doc_list_man']) && $doc_id_list['doc_list_man'] != 'null') {
            // dd($doc_id_list);
            $doc_list_man = DocumentType::selectRaw('\'1\' as required,id,is_profile_pic,doc_size_kb,doc_name,doc_type,doucument_group')->whereIn("id", json_decode($doc_id_list['doc_list_man']))->get()->toArray();

        } else
            $doc_list_man = array();
        if (isset($doc_id_list['doc_list_opt']) && $doc_id_list['doc_list_opt'] != 'null') {
            $doc_list_opt = DocumentType::selectRaw('\'0\' as required,id,is_profile_pic,doc_size_kb,doc_name,doc_type,doucument_group')->whereIn("id", json_decode($doc_id_list['doc_list_opt']))->get()->toArray();
        } else
            $doc_list_opt = array();
        if (count($doc_list_man) > 0 || count($doc_list_opt) > 0) {
            $doc_list = array_merge($doc_list_man, $doc_list_opt);

        } else {
            $doc_list = array();
        }
        $encloser_list = array();
        $i = 0;
        $bankEncloserCount = 0;
        if (in_array($scheme_id, array(8, 9))) {
            if (count($doc_list) > 0) {
                $encloser_list[$i]['can_download'] = 0;
                $doc_bank = DocumentType::where('id', 10)->first();
                $encloser_list[$i]['id'] = $doc_bank->id;
                $encloser_list[$i]['is_profile_pic'] = 0;
                $encloser_list[$i]['doc_size_kb'] = $doc_bank->doc_size_kb;
                $encloser_list[$i]['doc_name'] = $doc_bank->doc_name;
                $encloser_list[$i]['doc_type'] = $doc_bank->doc_type;
                $encloser_list[$i]['required'] = 1;
            }

        } else {
            if (count($doc_list) > 0) {
                foreach ($doc_list as $doc) {
                    $encloser_list[$i]['application_id'] = $application_id;
                    $encloser_list[$i]['id'] = $doc['id'];
                    $encloser_list[$i]['is_profile_pic'] = intval($doc['is_profile_pic']);
                    $encloser_list[$i]['doc_size_kb'] = $doc['doc_size_kb'];
                    $encloser_list[$i]['doc_name'] = $doc['doc_name'];
                    $encloser_list[$i]['doc_type'] = $doc['doc_type'];

                    if ($doc_profile->id == $doc['id']) {
                        if (in_array($doc['id'], $docs_uploaded->toArray())) {
                            $encloser_list[$i]['can_download'] = 1;
                            $encloser_list[$i]['required'] = 0;
                        } else {
                            $encloser_list[$i]['can_download'] = 0;
                            if ($doc['required'] == 1 && $is_faulty == 0) {
                                $encloser_list[$i]['required'] = 1;
                            } else
                                $encloser_list[$i]['required'] = 0;
                        }
                    } else {

                        if (in_array($doc['id'], $docs_uploaded->toArray())) {

                            $encloser_list[$i]['can_download'] = 1;
                            if ($doc['id'] == 10) {
                                $encloser_list[$i]['required'] = 1;
                            } else {
                                $encloser_list[$i]['required'] = 0;
                            }
                        } else {
                            $encloser_list[$i]['can_download'] = 0;

                            if ($is_faulty == 1) {
                                if ($doc['id'] == 10) {
                                    $encloser_list[$i]['required'] = 1;
                                } else {
                                    $encloser_list[$i]['required'] = 0;
                                }
                            } else {
                                if ($doc['required'] == 1) {
                                    $encloser_list[$i]['required'] = 1;
                                } else {
                                    if ($doc['id'] == 2 && (trim($row->caste) == 'SC' || trim($row->caste) == 'ST')) {
                                        $encloser_list[$i]['required'] = 1;
                                    } else
                                        $encloser_list[$i]['required'] = 0;
                                }
                            }
                        }
                    }
                    $i++;
                }
            }
        }
        // dd($row->toArray());
        return view(
            'DuplicateBank.dedupBankUpdate',
            [
                'application_id' => $application_id,
                'revert_reason' => $dupTableRow,
                'is_faulty' => $is_faulty,
                'row' => $row,
                'bank_code' => $bank_code,
                'scheme_id' => $scheme_id,
                'scheme_name' => $scheme_row->scheme_name,
                'encloser_list' => $encloser_list,
            ]
        );
    }
    public function dedupBankUpdatePost(Request $request)
    {
        $this->middleware('auth');
        $scheme_id = $request->scheme_id;
        if (empty($scheme_id)) {
            return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
        }
        if (!ctype_digit($scheme_id)) {
            return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
        }
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        $urban_body_code = NULL;
        $district_code = NULL;
        $mapping_level = NULL;
        $errormsg = Config::get('constants.errormsg');
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $is_urban = $roleObj['is_urban'];
                $district_code = $roleObj['district_code'];
                $mapping_level = $roleObj['mapping_level'];
                if ($roleObj['is_urban'] == 1) {
                    $urban_body_code = $roleObj['urban_body_code'];
                } else {
                    $urban_body_code = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if (AuthChecker::VerifierChecker()) {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if (AuthChecker::VerifierChecker()) {
            $verifier_condition = ' and p.created_by_local_body_code=' . $urban_body_code;
        } else {
            $verifier_condition = '';
        }
        if ($is_active == 0 || empty($district_code)) {
            return redirect("/")->with('error', 'User Disabled. ');
        }

        $old_bank_code = trim($request->old_bank_code);
        //   dd($old_bank_code);
        $bank_ifsc = trim($request->bank_ifsc_code);
        $bank_code = trim($request->bank_account_number);
        // dd($bank_code);
        $application_id = $request->application_id;
        $is_faulty = $request->is_faulty;

        if (empty($old_bank_code)) {
            return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', 'Bank Account No.Not Found');
        }
        if (empty($application_id)) {
            return redirect("/dedupBankView?scheme_id=" . $scheme_id . "&bank_code=" . $old_bank_code)->with('error', 'Application ID Not Found');
        }
        if (!ctype_digit($application_id)) {
            return redirect("/dedupBankView?scheme_id=" . $scheme_id . "&bank_code=" . $old_bank_code)->with('error', 'Application ID Not Valid');
        }
        $scheme_row = Scheme::where('id', $scheme_id)->first();
        if (!empty($scheme_row->short_code)) {
            $schema = $scheme_row->short_code;
        } else {
            $schema = "pension";
        }
        $doc_profile = DocumentType::select('id')->where('is_profile_pic', TRUE)->first();

        $row = DB::table($schema . '.beneficiary')->where('dup_bank', 1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code', 'payment_count', 'last_paid_yymm', 'scheme_id', 'created_by_dist_code', 'created_by_local_body_code')->first();

        if (empty($row->id)) {
            return redirect("/dedupBankView?scheme_id=" . $scheme_id . "&bank_code=" . $old_bank_code)->with('error', 'Application Id Not found in Db');
        }
        if (AuthChecker::ApproverChecker()) {
            $urban_body_code = $row->created_by_local_body_code;
        }
        if (in_array($scheme_id, array(8, 9))) {
            $i = 0;
            $encloser_list[$i]['can_download'] = 0;
            $doc_bank = DocumentType::where('id', 10)->first();
            $encloser_list[$i]['id'] = $doc_bank->id;
            $encloser_list[$i]['is_profile_pic'] = 0;
            $encloser_list[$i]['doc_size_kb'] = $doc_bank->doc_size_kb;
            $encloser_list[$i]['doc_name'] = $doc_bank->doc_name;
            $encloser_list[$i]['doc_type'] = $doc_bank->doc_type;
            $encloser_list[$i]['required'] = 1;
        } else {
            $docs_uploaded = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('created_by_dist_code', $district_code)->where('beneficiary_id', $row->id)->select('id', 'beneficiary_id', 'document_type', 'doc_type_name', 'attched_document')->get()->pluck('document_type');
            // dd($docs_uploaded);
            $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->first();

            if (!empty($doc_id_list)) {
                $doc_id_list = $doc_id_list->toArray();
            }
            // dd($doc_id_list['doc_list_man']);
            if (isset($doc_id_list['doc_list_man']) && $doc_id_list['doc_list_man'] != 'null') {
                // dd($doc_id_list);
                $doc_list_man = DocumentType::selectRaw('\'1\' as required,id,is_profile_pic,doc_size_kb,doc_name,doc_type,doucument_group')->whereIn("id", json_decode($doc_id_list['doc_list_man']))->get()->toArray();
            } else
                $doc_list_man = array();
            if (isset($doc_id_list['doc_list_opt']) && $doc_id_list['doc_list_opt'] != 'null') {
                $doc_list_opt = DocumentType::selectRaw('\'0\' as required,id,is_profile_pic,doc_size_kb,doc_name,doc_type,doucument_group')->whereIn("id", json_decode($doc_id_list['doc_list_opt']))->get()->toArray();
            } else
                $doc_list_opt = array();
            if (count($doc_list_man) > 0 || count($doc_list_opt) > 0) {
                $doc_list = array_merge($doc_list_man, $doc_list_opt);

            } else {
                $doc_list = array();
            }
            $encloser_list = array();
            $i = 0;

            if (count($doc_list) > 0) {
                foreach ($doc_list as $doc) {
                    $encloser_list[$i]['application_id'] = $application_id;
                    $encloser_list[$i]['id'] = $doc['id'];
                    $encloser_list[$i]['is_profile_pic'] = intval($doc['is_profile_pic']);
                    $encloser_list[$i]['doc_size_kb'] = $doc['doc_size_kb'];
                    $encloser_list[$i]['doc_name'] = $doc['doc_name'];
                    $encloser_list[$i]['doc_type'] = $doc['doc_type'];

                    if ($doc_profile->id == $doc['id']) {
                        if (in_array($doc['id'], $docs_uploaded->toArray())) {
                            $encloser_list[$i]['required'] = 0;
                            $encloser_list[$i]['can_download'] = 1;
                        } else {
                            $encloser_list[$i]['can_download'] = 0;
                            if ($doc['required'] == 1 && $is_faulty == 0) {
                                $encloser_list[$i]['required'] = 1;
                            } else
                                $encloser_list[$i]['required'] = 0;
                        }
                    } else {
                        //dd($encolserdata);

                        if (in_array($doc['id'], $docs_uploaded->toArray())) {
                            $encloser_list[$i]['can_download'] = 1;
                            if ($doc['id'] == 10) {
                                if ($is_faulty == 1) {
                                    $encloser_list[$i]['required'] = 1;
                                } else {
                                    $encloser_list[$i]['required'] = 1;
                                }
                            } else {
                                $encloser_list[$i]['required'] = 0;
                            }
                        } else {
                            $encloser_list[$i]['can_download'] = 0;
                            if ($doc['id'] == 10) {
                                if ($is_faulty == 1) {
                                    $encloser_list[$i]['required'] = 1;
                                } else {
                                    $encloser_list[$i]['required'] = 1;
                                }
                            } else {
                                if ($is_faulty == 1) {
                                    $encloser_list[$i]['required'] = 0;
                                } else {
                                    if ($doc['required'] == 1) {
                                        $encloser_list[$i]['required'] = 1;
                                    } else {
                                        $encloser_list[$i]['required'] = 0;
                                    }
                                }
                            }
                        }
                    }
                    $i++;
                }
            }
        }
        //dd($scheme_id);
        $rules = [
            'bank_ifsc_code' => 'required',
            'name_of_bank' => 'required|string|max:200',
            'bank_branch' => 'required|string|max:200',
            'bank_account_number' => 'required|numeric|required_with:confirm_bank_account_number|same:confirm_bank_account_number',
            'confirm_bank_account_number' => 'required|numeric',
        ];
        $attributes = array();
        $messages = array();
        $attributes['aadhar_no'] = 'Applicant Aadhaar Number';
        $attributes['mobile_no'] = 'Mobile Number';
        $attributes['bank_ifsc_code'] = 'IFS Code';
        $attributes['name_of_bank'] = 'Bank Name';
        $attributes['bank_branch'] = 'Bank Branch Name';
        $attributes['bank_account_number'] = 'Bank Account Number';
        if (count($encloser_list) > 0) {
            foreach ($encloser_list as $value) {
                if ($value['required'] == 1) {
                    $required = 'required';
                } else
                    $required = 'nullable';
                $rules['doc_' . $value['id']] = $required . '|mimes:' . $value['doc_type'] . '|max:' . $value['doc_size_kb'] . ',';
                $messages['doc_' . $value['id'] . '.max'] = "The file uploaded for " . $value['doc_name'] . " size must be less than " . $value['doc_size_kb'] . " KB";
                $messages['doc_' . $value['id'] . '.mimes'] = "The file uploaded for " . $value['doc_name'] . " must be of type " . $value['doc_type'];
                $messages['doc_' . $value['id'] . '.required'] = "Document for " . $value['doc_name'] . " must be uploaded";
            }
        }
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if (!$validator->passes()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {
            $check_ifsc_count = BankDetails::where('ifsc', trim($request->bank_ifsc_code))->where('is_active', 1)->count();

            $bank_details = BankDetails::where('ifsc', trim($request->bank_ifsc_code))->where('is_active', 1)->get(['bank', 'branch', 'bank_code'])->first();
            $new_bank_code = $bank_details->bank_code;
            //dd($check_ifsc_count);
            if ($check_ifsc_count == 0) {
                $return_text = 'IFSC not Found in our System..Please try different';
                return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
            }

            $row_count = DB::table($schema . '.beneficiary')->where('is_rejected', 0)->whereraw("trim(bank_code)='$bank_code'")->count();
            // dd($row_count);
            if ($row_count > 0) {
                $return_text = 'Duplicate Bank Account Details.';
                return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
            }

            if ($scheme_id == 10) {
                if (!empty($new_bank_code)) {
                    $DupCheckBankWP = DupCheck::getDupCheckBank(11, $new_bank_code);
                    if (!empty($DupCheckBankWP)) {
                        $return_text = "Duplicate Bank Account Number present in Widow Pension Scheme with Beneficiary ID- $DupCheckBankWP";
                        return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
                    }
                    $DupCheckBankLB = DupCheck::getDupCheckBank(20, $new_bank_code);
                    if (!empty($DupCheckBankLB)) {
                        $return_text = "Duplicate Bank Account Number present in Lakshmir Bhandar Scheme with Application ID- $DupCheckBankLB";
                        return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
                    }
                }
            }
            if ($scheme_id == 11) {
                if (!empty($new_bank_code)) {
                    $DupCheckBankOAP = DupCheck::getDupCheckBank(10, $new_bank_code);
                    if (!empty($DupCheckBankOAP)) {
                        $return_text = "Duplicate Bank Account Number present in Old Age Pension Scheme with Beneficiary ID- $DupCheckBankOAP";
                        return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
                    }
                }
            }
            if ($scheme_id == 1 || $scheme_id == 3) {
                if (!empty($new_bank_code)) {
                    $DupCheckBankLB = DupCheck::getDupCheckBank(20, $new_bank_code);
                    if (!empty($DupCheckBankLB)) {
                        $return_text = "Duplicate Bank Account Number present in Lakshmir Bhandar Scheme with Application ID- $DupCheckBankLB";
                        return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);

                    }
                    $DupCheckBankOAP = DupCheck::getDupCheckBank(10, $new_bank_code);
                    if (!empty($DupCheckBankOAP)) {
                        $return_text = "Duplicate Bank Account Number present in Old Age Pension Scheme with Beneficiary ID- $DupCheckBankOAP";
                        return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
                    }
                }
            }
            $scheme_capacity_arr = Helper::getCapacity($row->scheme_id, $row->created_by_dist_code);
            if ($scheme_capacity_arr['visible'] == 1) {
                // dump($scheme_capacity_arr['total_data']);
                //dump($scheme_capacity_arr['capacity']);
                if ($scheme_capacity_arr['total_data'] < $scheme_capacity_arr['capacity']) {
                    $dup_bank_pending = 0;
                } else {
                    $dup_bank_pending = 1;
                }
            } else {
                $dup_bank_pending = 0;
            }
            // dd($dup_bank_pending);
            $today = date("Y-m-d h:i:s");
            $new_value = [];
            try {

                // if ($row->payment_count == 0) {
                //     $new_last_paid_yymm = 0;
                // } else {
                //     $new_last_paid_yymm = $row->last_paid_yymm;
                // }
                // if($scheme_id==3){

                //     $max_lot_id=DB::table('sbi.transaction_lot_details')->where('pension_id', $application_id)->max('id');
                //     if(!empty($max_lot_id)){
                //         $is_lot_update=1;
                //     }  
                //     else{
                //         $is_lot_update=0;
                //     }       
                //  }
                //  else{

                //      $is_lot_update=0;
                //  }

                DB::beginTransaction();
                DB::connection('pgsql_encwrite')->beginTransaction();
                $new_value['bank_name'] = trim($request->name_of_bank);
                $new_value['branch_name'] = trim($request->bank_branch);
                $new_value['bank_ifsc'] = trim($request->bank_ifsc_code);
                $new_value['bank_code'] = trim($request->bank_account_number);
                $modelmainArch = array();
                $modelmainArch['update_code'] = 101;
                $modelmainArch['original_application_id'] = $application_id;
                $modelmainArch['old_data'] = json_encode($row);
                $modelmainArch['new_data'] = json_encode($new_value);
                $modelmainArch['scheme_id'] = $scheme_id;
                $modelmainArch['created_at'] = $today;
                $modelmainArch['user_id'] = $user_id;
                $modelmainArch['ip_address'] = $request->ip();
                $modelmainArchStatus = DB::table('update_ben_details')->insert($modelmainArch);
                $pension_details_bank_arr = array();
                $pension_details_bank_arr['bank_name'] = trim($request->name_of_bank);
                $pension_details_bank_arr['branch_name'] = trim($request->bank_branch);
                $pension_details_bank_arr['bank_code'] = trim($request->bank_account_number);
                $pension_details_bank_arr['bank_ifsc'] = trim($request->bank_ifsc_code);
                $pension_details_bank_arr['npci_bank_code'] = trim($new_bank_code);
                $pension_details_bank_arr['dup_bank'] = 0;
                $pension_details_bank_arr['dup_bank_pending'] = $dup_bank_pending;
                $pension_details_bank_arr['lot_generated'] = 0;
                $pension_details_bank_arr['bank_edited'] = 0;
                //$pension_details_bank_arr['last_paid_yymm'] = $new_last_paid_yymm;

                // $is_saved_bank = DB::table($schema . '.beneficiary')->where('dup_bank',1)->where('created_by_dist_code', $district_code)->where('id', $application_id)->update($pension_details_bank_arr);
                $payments_arr_new = array();
                $payments_arr_new['new_bank_code'] = trim($request->bank_account_number);
                $payments_arr_new['new_bank_ifsc'] = trim($request->bank_ifsc_code);
                $payments_arr_new['new_bank_name'] = trim($request->name_of_bank);
                $payments_arr_new['new_branch_name'] = trim($request->bank_branch);
                $payments_arr_new['next_level_role_id'] = 101;
                $payments_arr_new['is_approved'] = 0;

                $is_delete_bank_payment = DB::table('pension.ben_payment_details_bank_code_dup')->where('created_by_dist_code', $district_code)->where('next_level_role_id', $this->ben_status)->where('id', $application_id)->update($payments_arr_new);
                $k = 0;
                $doc_type_in = array();
                $file_copy_count = 0;
                $c = 0;
                $uploaded_doc = array();
                if (count($encloser_list) > 0) {
                    foreach ($encloser_list as $enc_row) {
                        if ($request->hasFile('doc_' . $enc_row['id'])) {

                            $c++;
                            $doc_file = $request->file('doc_' . $enc_row['id']);
                            $img_data = file_get_contents($doc_file);
                            // dd($img_data);
                            $u_extension = $doc_file->getClientOriginalExtension();
                            $mime_type = $doc_file->getMimeType();
                            if (strtolower($mime_type) == 'image/jpeg') {
                                if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
                                    $extension = $u_extension;
                                } else {
                                    $errors = array();
                                    $errorMsg = "You are trying to upload an incorrect file for " . $doc_bank->doc_name;
                                    array_push($errors, $errorMsg);
                                    DB::rollback();
                                    DB::connection('pgsql_encwrite')->rollback();
                                    return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $errorMsg);
                                }
                            } else if (strtolower($mime_type) == 'image/png') {
                                $extension = 'png';
                            } else if (strtolower($mime_type) == 'image/gif') {
                                $extension = 'gif';
                            } else if (strtolower($mime_type) == 'application/pdf') {
                                $extension = 'pdf';
                            } else {
                                $errors = array();
                                $errorMsg = "You are trying to upload an incorrect file for " . $doc_bank->doc_name;
                                array_push($errors, $errorMsg);
                                DB::rollback();
                                DB::connection('pgsql_encwrite')->rollback();
                                return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $errorMsg);
                            }
                            if ($u_extension != $extension) {
                                $errors = array();
                                $errorMsg = "You are trying to upload an incorrect file for " . $doc_bank->doc_name;
                                array_push($errors, $errorMsg);
                                DB::rollback();
                                DB::connection('pgsql_encwrite')->rollback();
                                return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $errorMsg);
                            }
                            $base64 = base64_encode($img_data);
                            $ip_address = request()->ip();
                            $c_datetime = date('Y-m-d H:i:s', time());
                            if ($request->hasFile('doc_' . $enc_row['id'])) {
                                $fun_call = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                                    in_beneficiary_id => " . $application_id . ",
                                    in_scheme_id => " . $scheme_id . ",
                                    in_document_type => " . $enc_row['id'] . ",
                                    in_attched_document => '" . $base64 . "',
                                    in_created_by_level => '" . $mapping_level . "',
                                    in_created_by => " . $user_id . ",
                                    in_ip_address => '" . $ip_address . "',
                                    in_document_extension => '" . $extension . "',
                                    in_document_mime_type => '" . $mime_type . "',
                                    in_created_by_dist_code => " . $district_code . ",
                                    in_created_by_local_body_code => " . $urban_body_code . ",
                                    in_doc_type_name => '" . $enc_row['doc_name'] . "',
                                    in_datetime => '" . $c_datetime . "'
                                    );"
                                );
                                if ($fun_call[0]->ben_docs_insert_archive == 1) {

                                    $file_copy_count++;
                                }
                            }

                        }
                    }
                }


                if ($c != $file_copy_count) {
                    $return_text = 'File Uploading problem.please try later';
                    return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
                }


                // if($is_lot_update){
                //     $lot_update_arr=array();
                //     $lot_update_arr['is_active']=-97;
                //     $lot_update = DB::table('sbi.transaction_lot_details')->where('id',$max_lot_id)->where('pension_id', $application_id)->update($lot_update_arr);
                // }
                // else
                // $lot_update=1;

                $dup_pass_bank = 1;
                if ($dup_pass_bank && $modelmainArchStatus &&  /*$is_saved_bank  &&*/ $is_delete_bank_payment) {

                    DB::commit();
                    DB::connection('pgsql_encwrite')->commit();
                    $return_text = "Beneficiary informations successfully updated with Beneficiary Id:" . $row->id;
                    return redirect('dedupBankView?scheme_id=' . $scheme_id . '&bank_code=' . $old_bank_code)->with('success', $return_text);
                } else {
                    DB::rollback();
                    DB::connection('pgsql_encwrite')->rollback();
                    //  dd('ok');
                    $return_text = 'Error Occur . Please try later.';
                    return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
                }

            } catch (\Exception $e) {
                DB::rollback();
                DB::connection('pgsql_encwrite')->rollback();
                //   dd($e);
                $return_text = 'Error Occur . Please try later..';
                return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
            }
        }
    }
    function ajaxGetEncloser(Request $request)
    {
        $return_status = 0;
        $return_msg = '';
        $html = '';
        $scheme_id = $request->scheme_id;
        if (empty($scheme_id)) {
            // return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
            $return_status = 0;
            $return_msg = 'Scheme Not Valid';
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        if (!ctype_digit($scheme_id)) {
            //return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
            $return_status = 0;
            $return_msg = 'Scheme Not Valid';
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        // $designation_id = Auth::user()->designation_id;
        $distCode = NULL;
        $user_id = AuthChecker::getUserId();
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $mapping_level = $roleObj['mapping_level'];
                $distCode = $roleObj['district_code'];
                $is_urban = $roleObj['is_urban'];
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                } else {
                    $blockCode = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if ($is_active == 0 || empty($distCode)) {
            // return redirect("/")->with('error', 'User Disabled');
            $return_status = 0;
            $return_msg = 'User Disabled';
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        $doc_type = $request->doc_type;
        // dd($doc_type);
        $application_id = $request->application_id;
        if (empty($doc_type) || !ctype_digit($doc_type)) {
            $return_status = 0;
            $return_msg = 'Document Type Not Valid';
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        if (empty($application_id)) {
            //$return_text = 'Parameter Not Valid3';
            // return redirect("/")->with('error',  $return_text);
            $return_status = 0;
            $return_msg = 'Application Id  Not Valid';
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        $scheme_row = Scheme::where('id', $scheme_id)->first();
        if (!empty($scheme_row->short_code)) {
            $schema = $scheme_row->short_code;
        } else {
            $schema = "pension";
        }
        $docs_uploaded = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('created_by_dist_code', $distCode)->where('document_type', $doc_type)->where('beneficiary_id', $application_id)->select('id', 'beneficiary_id', 'document_type', 'doc_type_name', 'attched_document', 'document_extension', 'document_mime_type')->first();

        if (empty($docs_uploaded->attched_document)) {
            // $return_status = 0;
            //$return_msg = 'Parameter Not Valid3';
            // return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
            $return_status = 0;
            $return_msg = 'No Document Uploaded previuosly.';
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        // //dd($docs_uploaded->doc_name);
        // $o_file_name = explode('/', $docs_uploaded->doc_name);
        // // dd($o_file_name);
        // $o_file_name = end($o_file_name);
        // // dd($o_file_name);
        // if (empty($o_file_name)) {
        //     //$return_text = 'Parameter Not Valid3';
        //     // return redirect("/")->with('error',  $return_text);
        //     $return_status = 0;
        //     $return_msg = 'Invalid Document Format..Please Upload again.';
        //     return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        // }
        // $o_file_name = explode('.', $o_file_name);
        //   dd($o_file_name);
        // if (empty($o_file_name) || count($o_file_name) == 0) {
        //     $return_status = 0;
        //     $return_msg = 'Invalid Document Format..Please Upload again.';
        //     return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        // }
        // $file_name = $o_file_name[0] . '.' . $o_file_name[1];
        // dd($file_name);
        // $path = storage_path() . "/app/keep_wcd/" . $file_name;
        $file_extension = $docs_uploaded->document_extension;
        $file_content = $docs_uploaded->attched_document;
        $mime_type = $docs_uploaded->document_mime_type;

        if ($file_extension != 'png' && $file_extension != 'jpg' && $file_extension != 'jpeg' && $file_extension != 'pdf') {
            if ($mime_type == 'image/png') {
                $file_extension = 'png';
            } else if ($mime_type == 'image/jpeg') {
                $file_extension = 'jpg';
            } else if ($mime_type == 'application/pdf') {
                $file_extension = 'pdf';
            }
        }
        try {
            if (strtoupper($file_extension) == 'PNG' || strtoupper($file_extension) == 'JPG' || strtoupper($file_extension) == 'JPEG') {
                $htmlText = '<image id="image" width="100%" height="100%" src="data:image/' . $file_extension . ';base64, ' . $file_content . '">';
                //echo $htmlText;
            } else if (strtoupper($file_extension) == 'PDF') {
                //dd($encolserData->attched_document);
                $htmlText = '<embed type="text/html" width="100%" height="100%" src="data:application/pdf;base64, ' . $file_content . ' ">';


                //echo $htmlText;
            }
            $return_status = 1;
            return response()->json(['return_status' => $return_status, 'htmlText' => $htmlText]);
        } catch (\Exception $e) {
            $return_status = 0;
            $return_msg = 'Some error.please try again ......';
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
            //return redirect("/")->with('error',  'Some error.please try again ......');
        }
    }
    public function dedupBankSamePost(Request $request)
    {
        $this->middleware('auth');
        $scheme_id = $request->scheme_id;
        if (empty($scheme_id)) {
            return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
        }
        if (!ctype_digit($scheme_id)) {
            return redirect("/dedupBankSelectScheme")->with('error', 'Scheme Not Valid');
        }
        $user_id = AuthChecker::getUserId();
        // $designation_id = Auth::user()->designation_id;
        $urban_body_code = NULL;
        $district_code = NULL;
        $errormsg = Config::get('constants.errormsg');
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $is_urban = $roleObj['is_urban'];
                $district_code = $roleObj['district_code'];
                $mapping_level = $roleObj['mapping_level'];
                if ($roleObj['is_urban'] == 1) {
                    $urban_body_code = $roleObj['urban_body_code'];
                } else {
                    $urban_body_code = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if (AuthChecker::VerifierChecker()) {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if (AuthChecker::VerifierChecker()) {
            $verifier_condition = ' and p.local_body_code=' . $urban_body_code;
        } else {
            $verifier_condition = '';
        }
        if ($is_active == 0 || empty($district_code)) {
            return redirect("/")->with('error', 'User Disabled. ');
        }

        $old_bank_ifsc = trim($request->old_bank_ifsc);
        $old_bank_code = trim($request->old_bank_code);
        // dd($old_bank_code);
        $application_id = $request->application_id;
        if (empty($old_bank_ifsc)) {
            return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', 'Bank IFSC Not Found');
        }
        if (empty($old_bank_code)) {
            return redirect("/dedupBankListView?scheme_id=" . $scheme_id)->with('error', 'Bank Account No.Not Found');
        }
        if (empty($application_id)) {
            return redirect("/dedupBankView?scheme_id=" . $scheme_id . "&last_accno=" . $old_bank_code)->with('error', 'Application ID Not Found');
        }
        if (!ctype_digit($application_id)) {
            return redirect("/dedupBankView?scheme_id=" . $scheme_id . "&last_accno=" . $old_bank_code)->with('error', 'Application ID Not Valid');
        }



        $rules = [
            'old_bank_ifsc' => 'required',
            'old_bank_code' => 'required|numeric',
        ];
        $attributes = array();
        $messages = array();
        $attributes['old_bank_ifsc'] = 'IFS Code';
        $attributes['old_bank_code'] = 'Bank Account Number';

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if (!$validator->passes()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {
            // dd('ok');
            $check_ifsc_count = BankDetails::where('ifsc', trim($request->old_bank_ifsc))->where('is_active', 1)->count();
            // $check_ifsc_count = 1;
            // $bank_details = BankDetails::whereraw("trim(ifsc)='$request->old_bank_ifsc'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
            // $new_bank_code=$bank_details->bank_code;
            if ($check_ifsc_count == 0) {
                $return_text = 'IFSC not Found in our System..Please try different';
                return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
            }
            $bank_details = BankDetails::where('ifsc', trim($request->old_bank_ifsc))->where('is_active', 1)->get(['bank', 'branch', 'bank_code'])->first();
            $new_bank_code = $bank_details->bank_code;
            $scheme_row = Scheme::where('id', $scheme_id)->first();
            if (!empty($scheme_row->short_code)) {
                $schema = $scheme_row->short_code;
            } else {
                $schema = "pension";
            }

            $row_count = DB::table($schema . '.beneficiaries')->where('is_rejected', 0)->where('id', '!=', $application_id)->whereraw("trim(bank_code)='$old_bank_code'")->count();

            if ($row_count > 0) {
                $return_text = 'Duplicate Bank Account Details.';
                return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
            }
            $row = DB::table($schema . '.beneficiaries')->where('dup_bank', 1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code', 'payment_count', 'last_paid_yymm', 'scheme_id', 'created_by_dist_code')->first();
            if (empty($row)) {
                return redirect("/dedupBankView?scheme_id=" . $scheme_id . "&bank_code=" . $old_bank_code)->with('error', 'Application Id Not found in Db');
            }
            $scheme_capacity_arr = Helper::getCapacity($row->scheme_id, $row->created_by_dist_code);
            if ($scheme_capacity_arr['visible'] == 1) {

                if ($scheme_capacity_arr['total_data'] < $scheme_capacity_arr['capacity']) {
                    $dup_bank_pending = 0;
                } else {
                    $dup_bank_pending = 1;
                }
            } else {
                $dup_bank_pending = 0;
            }

            if ($row->payment_count == 0) {
                $new_last_paid_yymm = 0;
            } else {
                $new_last_paid_yymm = $row->last_paid_yymm;
            }
            $today = date("Y-m-d h:i:s");
            try {
                if ($scheme_id == 3) {
                    //$application_id=4848;
                    $max_lot_id = DB::table('sbi.transaction_lot_details')->where('pension_id', $application_id)->max('id');
                    if (!empty($max_lot_id)) {
                        $is_lot_update = 1;
                    } else {
                        $is_lot_update = 0;
                    }
                } else {
                    $is_lot_update = 0;
                }

                DB::beginTransaction();
                $modelmainArch = array();
                $modelmainArch['update_code'] = 200;
                $modelmainArch['original_application_id'] = $application_id;
                $modelmainArch['scheme_id'] = $scheme_id;
                $modelmainArch['created_at'] = $today;
                $modelmainArch['user_id'] = $user_id;
                $modelmainArch['ip_address'] = $request->ip();
                $modelmainArchStatus = DB::table('update_ben_details')->insert($modelmainArch);
                $pension_details_bank_arr = array();
                $pension_details_bank_arr['dup_bank'] = 0;
                $pension_details_bank_arr['dup_bank_pending'] = $dup_bank_pending;
                $pension_details_bank_arr['lot_generated'] = 0;
                $pension_details_bank_arr['bank_edited'] = 0;
                $pension_details_bank_arr['npci_bank_code'] = trim($new_bank_code);
                $pension_details_bank_arr['last_paid_yymm'] = $new_last_paid_yymm;

                // $is_saved_bank = DB::table($schema . '.beneficiary')->where('dup_bank',1)->where('created_by_dist_code', $district_code)->where('id', $application_id)->update($pension_details_bank_arr);
                $payments_arr_new = array();
                $payments_arr_new['next_level_role_id'] = 200;
                $payments_arr_new['is_approved'] = 0;
                $is_delete_bank_payment = DB::table('pension.ben_payment_details_bank_code_dup')->where('created_by_dist_code', $district_code)->where('next_level_role_id', $this->ben_status)->where('id', $application_id)->update($payments_arr_new);
                if ($is_lot_update) {
                    $lot_update_arr = array();
                    $lot_update_arr['is_active'] = -97;
                    $lot_update = DB::table('sbi.transaction_lot_details')->where('id', $max_lot_id)->where('pension_id', $application_id)->update($lot_update_arr);
                } else
                    $lot_update = 1;
                // if ($applicant_id == 4635374) {
                //     dump($modelmainArchStatus); dump($is_delete_bank_payment); dump($lot_update); die;
                // }

                if ($modelmainArchStatus && /*$is_saved_bank &&*/ $is_delete_bank_payment && $lot_update) {
                    DB::commit();
                    $return_text = "Beneficiary informations successfully updated with Application Id:" . $application_id;
                    return redirect("/dedupBankView?scheme_id=" . $scheme_id . "&bank_ifsc=" . $old_bank_ifsc . "&bank_code=" . $old_bank_code)->with('success', $return_text);
                } else {
                    DB::rollback();
                    $return_text = $errormsg['roolback'];
                    return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id . '&is_faulty=' . $is_faulty)->with('error', $return_text);
                }
            } catch (\Exception $e) {

                DB::rollback();
                // dd($e);
                $return_text = $errormsg['roolback'];
                return redirect('dedupBankUpdate?scheme_id=' . $scheme_id . '&bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id . '&is_faulty=' . $is_faulty)->with('error', $return_text);
            }
        }
    }
    function dedupBankMis(Request $request)
    {
        $this->middleware('auth');
        $base_date = '2020-01-01';
        date_default_timezone_set('Asia/Kolkata');
        $c_time = Carbon::now();
        $c_date = $c_time->format("Y-m-d");
        $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        // $designation_id = Auth::user()->designation_id;
        $userId = AuthChecker::getUserId();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        if (AuthChecker::ReportCheckerCommon()) {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if (AuthChecker::VerifierChecker() || AuthChecker::ApproverChecker()) {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                if (in_array($roleObj['scheme_id'], array(3, 2, 10, 11, 8, 9, 17, 19, 1))) {
                    $is_urban = $roleObj['is_urban'];
                    $district_code = $roleObj['district_code'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                        $muncList = UrbanBody::select('urban_body_code', 'urban_body_name')->where('sub_district_code', $blockCode)->get();
                        $municipality_visible = 1;
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                        $gpList = GP::select('gram_panchyat_code', 'gram_panchyat_name')->where('block_code', $blockCode)->get();
                    }
                    break;
                }
            }

            if (empty($district_code))
                return redirect("/")->with('success', 'User Disabled. ');
        } else {
            return redirect("/")->with('success', 'User Disabled. ');
        }
        //dd($district_code);
        if (!empty($district_code)) {
            $district_visible = 0;
            $district_code_fk = $district_code;
        } else {
            $district_code_fk = NULL;
        }
        if (!empty($is_urban)) {
            $is_urban_visible = 0;
            $rural_urban_fk = $is_urban;
        } else {
            $rural_urban_fk = NULL;
        }
        if (!empty($blockCode)) {
            $block_visible = 0;
            $block_munc_corp_code_fk = $blockCode;
            $gp_ward_visible = 1;
        } else {
            $block_munc_corp_code_fk = NULL;
            $gp_ward_visible = 0;
        }
        $gp_ward_visible = 0;
        $municipality_visible = 0;
        $districts = District::get();
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
        //dd($scheme_list);
        return view(
            'DuplicateBank.misreport',
            [
                'districts' => $districts,
                'scheme_list' => $scheme_list,
                'district_visible' => $district_visible,
                'district_code_fk' => $district_code_fk,
                'is_urban_visible' => $is_urban_visible,
                'rural_urban_fk' => $rural_urban_fk,
                'block_visible' => $block_visible,
                'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
                'municipality_visible' => $municipality_visible,
                'gp_ward_visible' => $gp_ward_visible,
                'base_date' => $base_date,
                'c_date' => $c_date,
                'gpList' => $gpList,
                'muncList' => $muncList
            ]
        );
    }
    public function getData(Request $request)
    {
        $scheme_id = $request->scheme_id;
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        $gp_ward = $request->gp_ward;
        // dd($gp_ward);
        $caste = $request->caste_category;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $base_date = '2020-08-16';
        $c_time = Carbon::now();
        $c_date = $c_time->format("Y-m-d");
        $heading_msg = '';
        $title = "";
        //$block_condition = "";
        if (!empty($district)) {
            $district_row = District::where('district_code', $district)->first();
        }

        if (!empty($block)) {

            if ($urban_code == 1) {
                $block_ulb = SubDistrict::where('sub_district_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->sub_district_name;
                //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
            } else {
                $block_ulb = Taluka::where('block_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->block_name;
                // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
            }
        } else {
            // $block_condition = "";
        }
        if (!empty($gp_ward)) {

            if ($urban_code == 1) {
                $gp_ward_row = Ward::where('urban_body_ward_code', '=', $gp_ward)->first();
                $gp_ward_name = $gp_ward_row->urban_body_ward_name;
                //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
            } else {
                $gp_ward_row = GP::where('gram_panchyat_code', '=', $gp_ward)->first();
                $gp_ward_name = $gp_ward_row->gram_panchyat_name;
                // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
            }
        }
        $rules = [
            'scheme_id' => 'required|integer',
            'district' => 'nullable|integer',
            'urban_code' => 'nullable|integer',
            'block' => 'nullable|integer',
            'muncid' => 'nullable|integer',
            'gp_ward' => 'nullable|integer'
        ];
        $data = array();
        $column = "";
        $attributes = array();
        $messages = array();
        $attributes['scheme_id'] = 'Scheme';
        $attributes['district'] = 'District';
        $attributes['urban_code'] = 'Rural/ Urban';
        $attributes['block'] = 'Block/Sub Division';
        $attributes['muncid'] = 'Municipality';
        $attributes['gp_ward'] = 'GP/Ward';
        $attributes['from_date'] = 'From Date';
        $attributes['to_date'] = 'To Date';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $scheme_row = Scheme::where('id', $scheme_id)->first();
            $user_msg = "Duplicate Bank Account No. and IFSC Report for the Scheme " . $scheme_row->scheme_name;
            $title = $user_msg;
            //dd($title);

            $data = array();
            $return_status = 1;
            $return_msg = '';
            $heading_msg = '';
            $external = 0;
            $external_arr = array();
            $external_filter = array();
            if (!empty($gp_ward)) {
                if ($urban_code == 1) {
                    $column = "Ward";
                    $heading_msg = $user_msg . ' of the Ward ' . $gp_ward_name;
                    $data = $this->getWardWise($district, $block, $muncid, $gp_ward, $from_date, $to_date, $caste);
                } else {
                    $column = "GP";
                    $heading_msg = $user_msg . ' of the GP ' . $gp_ward_name;
                    $data = $this->getGpWise($district, $block, NULL, $gp_ward, $from_date, $to_date, $caste);
                }
            } else if (!empty($muncid)) {
                $column = "Ward";
                $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
                $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
                $data = $this->getWardWise($district, $block, $muncid, NULL, $from_date, $to_date, $caste);
            } else if (!empty($block)) {
                if ($urban_code == 1) {
                    $column = "Municipality";
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getMuncWise($district, $block, NULL, NULL, $from_date, $to_date, $caste);
                } else if ($urban_code == 2) {
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getGpWise($district, $block, NULL, $gp_ward, $from_date, $to_date, $caste);
                    $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                    $column = "Block";
                    $data = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
                }
            } else {

                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
                        $data2 = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getDistrictWise($scheme_id, NULL, NULL, NULL, NULL, $from_date, $to_date);

                    $external = 0;
                }
            }

            if (!empty($from_date)) {
                $form_date_formatted = Carbon::parse($from_date)->format('d-m-Y');
                $heading_msg = $heading_msg . " from " . $form_date_formatted;
            }
            if (!empty($to_date)) {
                $to_date_formatted = Carbon::parse($to_date)->format('d-m-Y');
                $heading_msg = $heading_msg . " to  " . $to_date_formatted;
            }
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }
        return response()->json([
            'return_status' => $return_status,
            'return_msg' => $return_msg,
            'row_data' => $data,
            'column' => $column,
            'title' => $title,
            'heading_msg' => $heading_msg
        ]);
    }
    public function getDistrictWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL)
    {
        //$dateFromat = 'DD/MM/YYYY';
        $dateFromat = 'YYYY/MM/DD';
        $whereCon = "where 1=1";
        $query = "select A.location_id,A.location_name,COALESCE(C.total_dup,0) as total_dup, 
        COALESCE(C.total_edit_differ,0) as total_edit_differ,
        COALESCE(C.total_edit_same,0) as total_edit_same,
        COALESCE(C.total_rejected,0) as total_rejected
        from(
        select district_code as location_id,district_name as location_name
         from public.m_district 
         )
         as A  
        LEFT JOIN
        (select
                   count(1) as total_dup,
                   count(1) filter(where next_level_role_id=101 AND is_approved in(0,1)) as total_edit_differ,
                    count(1) filter(where next_level_role_id=200 AND is_approved in(0,1) ) as total_edit_same,
                    count(1) filter(where next_level_role_id IN (-200) AND is_approved in(0,1)) as total_rejected,
                    created_by_dist_code
                    from pension.ben_payment_details_bank_code_dup where scheme_id=" . $scheme_id . "   
         group by created_by_dist_code) as C ON A.location_id=C.created_by_dist_code";

        // echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
    public function getSubDivWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL)
    {
        //$whereCon = "where A.dist_code=" . $district_code;
        $whereMain = "where  district_code=" . $district_code;

        $query = "select A.location_id,A.location_name,COALESCE(C.total_dup,0) as total_dup, 
        COALESCE(C.total_edit_differ,0) as total_edit_differ,
        COALESCE(C.total_edit_same,0) as total_edit_same,
        COALESCE(C.total_rejected,0) as total_rejected
        from(
            select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
            from public.m_sub_district  " . $whereMain . " 
         )
         as A  
        LEFT JOIN
        (select 
                    count(1) total_dup,
                    count(1) filter(where next_level_role_id=101  AND is_approved in(0,1)) as total_edit_differ,
                    count(1) filter(where next_level_role_id=200  AND is_approved in(0,1)) as total_edit_same,
                    count(1) filter(where next_level_role_id IN (-200) AND is_approved in(0,1)) as total_rejected,
                    created_by_local_body_code
                    from pension.ben_payment_details_bank_code_dup  where scheme_id=" . $scheme_id . " and  created_by_dist_code= " . $district_code . "   
         group by created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
    public function getBlockWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL)
    {
        $whereMain = "where  district_code=" . $district_code;
        $query = "select A.location_id,A.location_name,COALESCE(C.total_dup,0) as total_dup, 
        COALESCE(C.total_edit_differ,0) as total_edit_differ,
        COALESCE(C.total_edit_same,0) as total_edit_same,
        COALESCE(C.total_rejected,0) as total_rejected
        from(
            select block_code as location_id,'Block-'||block_name as location_name
           from public.m_block  " . $whereMain . " 
         )
         as A  
        LEFT JOIN
        (select   
                    count(1) total_dup,
                    count(1) filter(where next_level_role_id= 101 AND is_approved in(0,1)) as total_edit_differ,
                    count(1) filter(where next_level_role_id= 200 AND is_approved in(0,1) ) as total_edit_same,
                    count(1) filter(where next_level_role_id = -200 AND is_approved in(0,1)) as total_rejected,
                    created_by_local_body_code
                    from pension.ben_payment_details_bank_code_dup  where scheme_id=" . $scheme_id . " and  created_by_dist_code= " . $district_code . "   
         group by created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }

    // Approver End
    private function getSchemaName($scheme_id)
    {
        if (!is_null($scheme_id)) {
            $sObj = Scheme::select('id', 'short_code')
                ->where('id', '=', $scheme_id)
                ->first();
            //$parameter['scheme_id'] = $scheme_id;
            $schema_name = $sObj->short_code;
            //dd($schema_name);
            if (empty($schema_name)) {
                $schema_name = 'pension';
            }
            $table_name = strtolower($schema_name) . '.beneficiary';
        } else {
            $table_name = 'pension.beneficiary';
        }
        return $table_name;
    }
    public function dedupBankApprover(Request $request)
    {
        // dd('ok');
        $user_id = AuthChecker::getUserId();
        $dutyObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
        $distCode = $dutyObj->district_code;
        $scheme = DB::connection('pgsql_mis')->select(
            'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
            $user_id .
            ' and is_active=1) order by scheme_name'
        );
        if (Auth::user()->designation_id == 'Approver') {
            $levels = [
                2 => 'Rural',
                1 => 'Urban'
            ];
            return view('DuplicateBank.dedupBankList', ['levels' => $levels, 'schemes' => $scheme, 'dist_code' => $distCode]);
        }
    }
    public function dedupBankList(Request $request)
    {
        // dd('ok');
        $user_id = AuthChecker::getUserId();
        $dutyObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
        $distCode = $dutyObj->district_code;
        $rural_urban = $request->filter_1;
        $local_body_code = $request->filter_2;
        // $block_ulb_code = $request->block_ulb_code;
        // $gp_ward_code = $request->gp_ward_code;
        $scheme_id = $request->scheme_id;
        $search_for = $request->search_for;
        if ($request->ajax()) {
            $scheme_row = Scheme::where('id', $scheme_id)->first();
            if (!empty($scheme_row->short_code)) {
                $schema = $scheme_row->short_code;
            } else {
                $schema = "pension";
            }
            if (Auth::user()->designation_id == 'Approver' && !empty($scheme_id)) {
                // $data = DB::connection('pgsql')->table($table_name)
                //     ->where('next_level_role_id', 0)
                //     ->where('lot_generated', $lot_generated)
                //     ->where('scheme_id', $scheme_id)
                //     ->where('created_by_dist_code', $distCode)
                //     ->where('bank_edited', 0) //Temporary Code

                //     ->orderBy('id', 'desc')
                //     ->get();
                $query = '';
                $query = "SELECT id AS beneficiary_id, CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) AS name, bank_code AS old_acc_no, bank_ifsc AS old_ifsc, new_bank_code AS new_acc_no, new_bank_ifsc AS new_ifsc, block_ulb_name, scheme_id FROM pension.ben_payment_details_bank_code_dup WHERE is_approved = 0 AND scheme_id = " . $scheme_id . " ";
                if ($search_for == 1) {
                    $query .= "AND next_level_role_id = 101";
                }
                if ($search_for == 2) {
                    $query .= "AND next_level_role_id = 200";
                }
                if ($search_for == 3) {
                    $query .= "AND next_level_role_id = -200";
                }
                if (!empty($rural_urban)) {
                    $query .= "and rural_urban_id=" . $rural_urban . " ";
                }
                if (!empty($local_body_code)) {
                    $query .= "and created_by_local_body_code=" . $local_body_code . " ";
                }
                // dd($query);
                $data = DB::connection('pgsql_mis')->select($query);
                //   dd( $data->old_data);
            } else {
                $data = collect([]);
            }
            //  dd($data);
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('view', function ($data) {
                    $action = '<button class="btn btn-primary btn-xs ben_view_button" value="' . $data->beneficiary_id . '_' . $data->scheme_id . '"><i class="glyphicon glyphicon-edit"></i>View</button>';
                    return $action;
                })
                ->addColumn('check', function ($data) {
                    return '<input type="checkbox"  name="chkbx" class="all_checkbox"  onclick="controlCheckBox();" value="' . $data->beneficiary_id . '">';
                })
                ->rawColumns(['view', 'check'])
                ->make(true);
        }
    }
    public function getModalView(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $benid = $request->benid;
            $parts = explode('_', $benid);
            $id = $parts[0];
            $scheme_id = $parts[1];
            // $this->checkSchemeSession($scheme_id);
            $mappingLevel = $request->session()->get('level');
            $district_code = $request->session()->get('distCode');
            $is_first = $request->session()->get('is_first');
            $is_urban = $request->session()->get('is_urban');
            $body_code = $request->session()->get('bodyCode');
            $role_id = $request->session()->get('role_id');

            // Get Dynamic Schema Name scheme wise
            $table_name = $this->getSchemaName($scheme_id);

            $query = '';
            $query = "SELECT id AS beneficiary_id, CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) AS name,gender,caste,mobile_no,branch_name, bank_name, bank_code, bank_ifsc, new_bank_code, new_bank_ifsc, block_ulb_name, scheme_id FROM pension.ben_payment_details_bank_code_dup WHERE id = " . $id . " AND scheme_id = " . $scheme_id . " ";
            // if (!empty($rural_urban)) {
            //   $query .= "and bp.rural_urban_id=" . $rural_urban . " ";
            // }
            // if (!empty($local_body_code)) {
            //   $query .= "and bp.created_by_local_body_code=" . $local_body_code . " ";
            // }
            // dd($query);
            $ben_details = DB::connection('pgsql_mis')->select($query);
            if ($ben_details == null) {
                return $response = [
                    'status' => 1,
                    'msg' => 'Somethimg went wrong.',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            } else {
                $ben_arr = [
                    'ben_name' => $ben_details[0]->name,
                    'id' => $ben_details[0]->beneficiary_id,
                    'gender' => $ben_details[0]->gender,
                    'caste' => $ben_details[0]->caste,
                    'mobile_no' => $ben_details[0]->mobile_no,
                    'scheme_id' => $ben_details[0]->scheme_id,
                    'bank_code' => trim($ben_details[0]->bank_code),
                    'bank_ifsc' => trim($ben_details[0]->bank_ifsc),
                    'new_bank_code' => trim($ben_details[0]->new_bank_code),
                    'new_bank_ifsc' => trim($ben_details[0]->new_bank_ifsc),
                    'branch_name' => trim($ben_details[0]->branch_name),
                    'bank_name' => trim($ben_details[0]->bank_name),
                    'application_id' => $ben_details[0]->beneficiary_id,
                ];
                // dd($ben_arr);
                $response = array_merge($ben_arr, [
                    'status' => 2,
                    // 'pay_mode' => $pay_mode
                ]);
            }
        } catch (\Exception $e) {
            //    dd($e);
            $response = [
                'exception' => true,
                'exception_message' => $e->getMessage(),
                // 'exception_message' =>
                //     'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }
    public function updateDeduplicateBankApprove(Request $request)
    {
        $user_id = AuthChecker::getUserId();

        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        $is_bulk = $request->is_bulk;
        $accept_reject_comments = $request->accept_reject_comments;
        $opreation_type = $request->opreation_type;
        $applicant_id = $request->applicantId;

        if ($is_bulk == 0) {
            $single_app_id = $request->single_app_id;
            $parts = explode('_', $single_app_id);
            $id = $parts[0];
            $scheme_id = $parts[1];
            if ($opreation_type == 'A') {
                try {
                    $user_id = AuthChecker::getUserId();
                    $ip_address = request()->ip();
                    $mappingLevel = $request->session()->get('level');
                    $district_code = $request->session()->get('distCode');
                    $is_first = $request->session()->get('is_first');
                    $is_urban = $request->session()->get('is_urban');
                    $body_code = $request->session()->get('bodyCode');
                    $role_id = $request->session()->get('role_id');
                    $table_name = $this->getSchemaName($scheme_id);
                    $today = date("Y-m-d h:i:s");
                    $query = "SELECT id AS beneficiary_id, CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) AS name, bank_code, branch_name, bank_name,new_bank_name,new_branch_name, bank_ifsc, new_bank_code, new_bank_ifsc, block_ulb_name, created_by_dist_code,next_level_role_id FROM pension.ben_payment_details_bank_code_dup WHERE id = " . $id . " AND scheme_id = " . $scheme_id . "";
                    $ben_details = DB::connection('pgsql_mis')->select($query);
                    // dd($ben_details);
                    if ($ben_details == null) {
                        return $response = [
                            'status' => 1,
                            'msg' => 'Somethimg went wrong.',
                            'type' => 'red',
                            'icon' => 'fa fa-warning',
                            'title' => 'Warning!!',
                        ];
                    } else {
                        DB::beginTransaction();
                        DB::connection('pgsql_paywrite')->beginTransaction();
                        $updateBenTable = [];
                        $old_data = [];
                        $new_data = [];
                        $ben_payment = [];
                        if ($ben_details[0]->next_level_role_id == 101) {
                            $bank_details = BankDetails::where('ifsc', trim($ben_details[0]->new_bank_ifsc))->where('is_active', 1)->get(['bank', 'branch', 'bank_code'])->first();
                            $new_npci_code = $bank_details->bank_code;

                            $updateBenTable['bank_code'] = trim($ben_details[0]->new_bank_code);
                            $updateBenTable['bank_ifsc'] = trim($ben_details[0]->new_bank_ifsc);
                            $updateBenTable['bank_name'] = trim($ben_details[0]->new_bank_name);
                            $updateBenTable['branch_name'] = trim($ben_details[0]->new_branch_name);
                            $updateBenTable['npci_bank_code'] = trim($new_npci_code);
                            $updateBenTable['dup_bank'] = 0;
                            $updateBenTable['lot_generated'] = 0;
                            $updateBenTable['bank_edited'] = 0;
                            $updateBenTable['acc_validated'] = 0;
                            // $updateBenTable['last_paid_yymm'] = $new_last_paid_yymm;

                            $old_data['bank_code'] = trim($ben_details[0]->bank_code);
                            $old_data['bank_ifsc'] = trim($ben_details[0]->bank_ifsc);
                            $new_data['bank_code'] = trim($ben_details[0]->new_bank_code);
                            $new_data['bank_ifsc'] = trim($ben_details[0]->new_bank_ifsc);

                            $ben_payment['last_accno'] = trim($ben_details[0]->new_bank_code);
                            $ben_payment['last_ifsc'] = trim($ben_details[0]->new_bank_ifsc);
                            $ben_payment['npci_bank_code'] = trim($new_npci_code);
                            $ben_payment['acc_validated'] = 0;
                            $ben_payment['legacy_validation'] = 0;
                            $ben_payment['dup_bank'] = 0;
                            $ben_payment['updated_at'] = date('Y-m-d H:i:s');

                            $updateBenDetailsData1 = ['next_level_role_id' => 0];

                        }
                        // dd($ben_payment);
                        if ($ben_details[0]->next_level_role_id == 200) {
                            $updateBenTable['dup_bank'] = 0;
                            $updateBenTable['lot_generated'] = 0;
                            $updateBenTable['bank_edited'] = 0;
                            $updateBenTable['acc_validated'] = 0;

                            $ben_payment['acc_validated'] = 0;
                            $ben_payment['legacy_validation'] = 0;
                            $ben_payment['dup_bank'] = 0;
                            $ben_payment['updated_at'] = date('Y-m-d H:i:s');
                            $updateBenDetailsData1 = ['next_level_role_id' => 1];
                        }
                        if ($ben_details[0]->next_level_role_id == -200) {
                            $updateBenTable['next_level_role_id'] = -200;
                            $updateBenTable['is_rejected'] = 1;
                            $updateBenTable['is_verified'] = 2;
                            $updateBenTable['is_approved'] = 2;
                            $updateBenTable['dup_bank'] = 0;
                            $updateBenTable['dup_bank_pending'] = 0;
                            $updateBenTable['rejected_by'] = $user_id;
                            $updateBenTable['rejected_date'] = $today;

                            $ben_payment['is_eligible'] = false;
                            $ben_payment['is_rejected'] = 1;
                            $ben_payment['rejected_at'] = $today;
                            $ben_payment['dup_bank'] = 0;
                            $ben_payment['updated_at'] = date('Y-m-d H:i:s');

                            $updateBenDetailsData1 = ['next_level_role_id' => 2];

                        }
                        $updateDupTable = [];
                        // $updateDupTable['next_level_role_id'] = 0;
                        $updateDupTable['revert_remarks'] = $accept_reject_comments;
                        $updateDupTable['is_approved'] = 1;

                        $updateBenDetailsData = [
                            'original_application_id' => $ben_details[0]->beneficiary_id,
                            'dist_code' => $ben_details[0]->created_by_dist_code,
                            'scheme_id' => $scheme_id,
                            'remarks' => $accept_reject_comments,
                            'old_data' => json_encode($old_data),
                            'new_data' => json_encode($new_data),
                            'user_id' => $user_id,
                            'update_code' => 100, //Approved De duplicate Bank details.
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'ip_address' => $ip_address,
                        ];
                        $updateBenDetailsData = array_merge($updateBenDetailsData, $updateBenDetailsData1);
                        $ben_main = DB::table($table_name)->where('id', $id)->first();
                        $ben_details = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('scheme_id', $scheme_id)->where('ben_id', $id)->first();
                        if ($ben_main->next_level_role_id == 0) {
                            if ($ben_details == NULL) {
                                return $response = [
                                    'status' => 1,
                                    'msg' => 'No data available in payment DB.',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        }
                        $is_insert = UpdateBenDetails::insert($updateBenDetailsData);
                        if ($is_insert) {
                            $is_ben_update = DB::table($table_name)->where('id', $id)->update($updateBenTable);
                            if ($is_ben_update) {
                                if ($ben_main->next_level_role_id == 0) {
                                    $is_failed_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $id)->where('scheme_id', $scheme_id)->update($ben_payment);
                                } else {
                                    $is_failed_update = 1;
                                }
                                if ($is_failed_update) {
                                    $is_dup_update = DB::table('pension.ben_payment_details_bank_code_dup')->where('id', $id)->where('scheme_id', $scheme_id)->update($updateDupTable);
                                    if ($is_dup_update) {
                                        DB::commit();
                                        DB::connection('pgsql_paywrite')->commit();
                                        $response = [
                                            'status' => 1,
                                            'msg' => 'Bank Details Approved Successfully',
                                            'type' => 'green',
                                            'icon' => 'fa fa-check',
                                            'title' => 'Success',
                                        ];
                                    } else {
                                        DB::rollback();
                                        DB::connection('pgsql_paywrite')->rollback();
                                        $response = [
                                            'status' => 3,
                                            'msg' => '3 Somethimg went wrong!!',
                                            'type' => 'red',
                                            'icon' => 'fa fa-warning',
                                            'title' => 'Warning!!',
                                        ];
                                    }
                                } else {
                                    DB::rollback();
                                    DB::connection('pgsql_paywrite')->rollback();
                                    $response = [
                                        'status' => 3,
                                        'msg' => '4 Somethimg went wrong!!',
                                        'type' => 'red',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];
                                }
                            } else {
                                DB::rollback();
                                DB::connection('pgsql_paywrite')->rollback();
                                $response = [
                                    'status' => 3,
                                    'msg' => '4 Somethimg went wrong!!',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        } else {
                            DB::rollback();
                            DB::connection('pgsql_paywrite')->rollback();
                            $response = [
                                'status' => 3,
                                'msg' => '2 Somethimg went wrong!!',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    // dd($e);
                    DB::rollback();
                    DB::connection('pgsql_paywrite')->rollback();
                    $response = [
                        'exception' => true,
                        'exception_message' => $e->getMessage(),
                        // 'exception_message' =>
                        //     'Something went wrong. May be session time out logout and login again.',
                    ];
                    //throw $th;
                    $statusCode = 400;
                } finally {
                    // dd($response);
                    return response()->json($response, $statusCode);
                }
            } elseif ($opreation_type == 'T') {
                try {
                    $user_id = AuthChecker::getUserId();
                    $ip_address = request()->ip();
                    $mappingLevel = $request->session()->get('level');
                    $district_code = $request->session()->get('distCode');
                    $is_first = $request->session()->get('is_first');
                    $is_urban = $request->session()->get('is_urban');
                    $body_code = $request->session()->get('bodyCode');
                    $role_id = $request->session()->get('role_id');
                    $table_name = $this->getSchemaName($scheme_id);
                    $query = "SELECT id AS beneficiary_id, CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) AS name, bank_code, branch_name, bank_name, bank_ifsc, new_bank_code, new_bank_ifsc, block_ulb_name, created_by_dist_code FROM pension.ben_payment_details_bank_code_dup WHERE id = " . $id . " AND scheme_id = " . $scheme_id . "";
                    // dd($query);
                    $ben_details = DB::connection('pgsql_mis')->select($query);
                    if ($ben_details == null) {
                        return $response = [
                            'status' => 1,
                            'msg' => 'Somethimg went wrong.',
                            'type' => 'red',
                            'icon' => 'fa fa-warning',
                            'title' => 'Warning!!',
                        ];
                    } else {
                        $updateBenDetailsData = [
                            'original_application_id' => $ben_details[0]->beneficiary_id,
                            'dist_code' => $ben_details[0]->created_by_dist_code,
                            'scheme_id' => $scheme_id,
                            'remarks' => $accept_reject_comments,
                            'user_id' => $user_id,
                            'update_code' => 400,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'ip_address' => $ip_address,
                            'action_by' => $user_id,
                            'action_ip_address' => $request->ip(),
                            'action_type' => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod()
                        ];
                        $updateDupTable = [];
                        $updateDupTable['revert_remarks'] = $accept_reject_comments;
                        $updateDupTable['next_level_role_id'] = -97;
                        $updateDupTable['is_approved'] = 2;
                        $is_insert = UpdateBenDetails::insert($updateBenDetailsData);
                        if ($is_insert) {
                            $is_dup_update = DB::table('pension.ben_payment_details_bank_code_dup')->where('id', $id)->update($updateDupTable);
                            if ($is_dup_update) {
                                $response = [
                                    'status' => 1,
                                    'msg' => 'Bank Details Reverted To Verifier',
                                    'type' => 'green',
                                    'icon' => 'fa fa-check',
                                    'title' => 'Success',
                                ];
                            } else {
                                $response = [
                                    'status' => 3,
                                    'msg' => '3 Somethimg went wrong!!',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        }
                    }
                    // dd($updateBenTable);
                } catch (\Exception $e) {
                    // dd($e);
                    DB::rollback();
                    $response = [
                        'exception' => true,
                        'exception_message' => $e->getMessage(),
                        // 'exception_message' =>
                        //     'Something went wrong. May be session time out logout and login again.',
                    ];
                    //throw $th;
                    $statusCode = 400;
                } finally {
                    // dd($response);
                    return response()->json($response, $statusCode);
                }
            }
        }
        if ($is_bulk == 1) {
            if ($opreation_type == 'A') {
                $bulk_id_arr = explode(',', $applicant_id);
                // dd($bulk_id_arr);
                $scheme_id = $request->scheme_id;
                try {
                    $count = 0;
                    DB::beginTransaction();
                    DB::connection('pgsql_paywrite')->beginTransaction();
                    foreach ($bulk_id_arr as $key => $value) {

                        $count++;
                        $table_name = $this->getSchemaName($scheme_id);
                        $ip_address = request()->ip();
                        $today = date("Y-m-d h:i:s");
                        $query = '';
                        $query = "SELECT id AS beneficiary_id, CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) AS name, bank_code, branch_name, bank_name,new_bank_name,new_branch_name, bank_ifsc, new_bank_code, new_bank_ifsc, block_ulb_name, created_by_dist_code,next_level_role_id FROM pension.ben_payment_details_bank_code_dup WHERE id = " . $value . " AND scheme_id = " . $scheme_id . "";
                        $ben_details = DB::connection('pgsql_mis')->select($query);
                        if ($ben_details == null) {
                            return $response = [
                                'status' => 1,
                                'msg' => 'Somethimg went wrong.',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        } else {
                            // DB::beginTransaction();
                            $updateBenTable = [];
                            $old_data = [];
                            $new_data = [];
                            if ($ben_details[0]->next_level_role_id == 101) {
                                $bank_details = BankDetails::where('ifsc', trim($ben_details[0]->new_bank_ifsc))->where('is_active', 1)->get(['bank', 'branch', 'bank_code'])->first();
                                $new_bank_code = $bank_details->bank_code;
                                $updateBenTable['bank_code'] = trim($ben_details[0]->new_bank_code);
                                $updateBenTable['bank_ifsc'] = trim($ben_details[0]->new_bank_ifsc);
                                $updateBenTable['bank_name'] = trim($ben_details[0]->new_bank_name);
                                $updateBenTable['branch_name'] = trim($ben_details[0]->new_branch_name);
                                $updateBenTable['npci_bank_code'] = trim($new_bank_code);
                                $updateBenTable['dup_bank'] = 0;
                                $updateBenTable['lot_generated'] = 0;
                                $updateBenTable['bank_edited'] = 0;
                                $updateBenTable['acc_validated'] = 0;
                                // $updateBenTable['last_paid_yymm'] = $new_last_paid_yymm;

                                $old_data['bank_code'] = trim($ben_details[0]->bank_code);
                                $old_data['bank_ifsc'] = trim($ben_details[0]->bank_ifsc);
                                $new_data['bank_code'] = trim($ben_details[0]->new_bank_code);
                                $new_data['bank_ifsc'] = trim($ben_details[0]->new_bank_ifsc);

                                $ben_payment['last_accno'] = trim($ben_details[0]->new_bank_code);
                                $ben_payment['last_ifsc'] = trim($ben_details[0]->new_bank_ifsc);
                                $ben_payment['npci_bank_code'] = trim($new_npci_code);
                                $ben_payment['acc_validated'] = 0;
                                $ben_payment['dup_bank'] = 0;
                                $ben_payment['updated_at'] = date('Y-m-d H:i:s');
                                $updateBenDetailsData1 = ['next_level_role_id' => 0];
                            }
                            if ($ben_details[0]->next_level_role_id == 200) {
                                $updateBenTable['dup_bank'] = 0;
                                $updateBenTable['lot_generated'] = 0;
                                $updateBenTable['bank_edited'] = 0;
                                $updateBenTable['acc_validated'] = 0;

                                $ben_payment['acc_validated'] = 0;
                                $ben_payment['dup_bank'] = 0;
                                $ben_payment['updated_at'] = date('Y-m-d H:i:s');
                                $updateBenDetailsData1 = ['next_level_role_id' => 1];
                            }
                            if ($ben_details[0]->next_level_role_id == -200) {
                                $updateBenTable['next_level_role_id'] = -200;
                                $updateBenTable['is_rejected'] = 1;
                                $updateBenTable['is_verified'] = 2;
                                $updateBenTable['is_approved'] = 2;
                                $updateBenTable['dup_bank'] = 0;
                                $updateBenTable['dup_bank_pending'] = 0;
                                $updateBenTable['rejected_by'] = $user_id;
                                $updateBenTable['rejected_date'] = $today;

                                $ben_payment['is_eligible'] = false;
                                $ben_payment['is_rejected'] = 1;
                                $ben_payment['rejected_at'] = $today;
                                $ben_payment['dup_bank'] = 0;
                                $ben_payment['updated_at'] = date('Y-m-d H:i:s');
                                $updateBenDetailsData1 = ['next_level_role_id' => 2];
                            }


                            $updateDupTable = [];
                            $updateDupTable['is_approved'] = 1;
                            $updateDupTable['revert_remarks'] = $accept_reject_comments;

                            $updateBenDetailsData = [
                                'original_application_id' => $ben_details[0]->beneficiary_id,
                                'dist_code' => $ben_details[0]->created_by_dist_code,
                                'scheme_id' => $scheme_id,
                                'remarks' => $accept_reject_comments,
                                'old_data' => json_encode($old_data),
                                'new_data' => json_encode($new_data),
                                'user_id' => Auth::user()->id,
                                'update_code' => 100, //Approved De duplicate Bank details.
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'ip_address' => $ip_address,
                                'action_by' => $user_id,
                                'action_ip_address' => $request->ip(),
                                'action_type' => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod()
                            ];
                            $updateBenDetailsData = array_merge($updateBenDetailsData, $updateBenDetailsData1);
                            $ben_main = DB::table($table_name)->where('id', $id)->first();
                            $ben_details = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('scheme_id', $scheme_id)->where('ben_id', $value)->where('dist_code', $district_code)->first();
                            if ($ben_main->next_level_role_id == 0) {
                                if ($ben_details == NULL) {
                                    return $response = [
                                        'status' => 1,
                                        'msg' => 'No data available in payment DB.',
                                        'type' => 'red',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];
                                }
                            }
                            $is_insert = UpdateBenDetails::insert($updateBenDetailsData);
                            if ($is_insert) {
                                $is_ben_update = DB::table($table_name)->where('id', $value)->update($updateBenTable);
                                if ($ben_main->next_level_role_id == 0) {
                                    $is_failed_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('id', $value)->update($ben_payment);
                                } else {
                                    $is_failed_update = 1;
                                }
                                $is_dup_update = DB::table('pension.ben_payment_details_bank_code_dup')->where('id', $value)->update($updateDupTable);

                                if ($is_ben_update && $is_dup_update && $is_failed_update) {
                                    // DB::commit();
                                    $response = [
                                        'status' => 1,
                                        'msg' => 'Bank Details Approve Successfully',
                                        'type' => 'green',
                                        'icon' => 'fa fa-check',
                                        'title' => 'Success',
                                    ];
                                } else {
                                    DB::rollback();
                                    DB::connection('pgsql_paywrite')->rollback();
                                    $response = [
                                        'status' => 3,
                                        'msg' => '3 Somethimg went wrong!!',
                                        'type' => 'red',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];
                                }
                            }
                        }
                    }
                    if ($i == $count) {
                        DB::commit();
                        DB::connection('pgsql_paywrite')->commit();
                        $response = [
                            'status' => 1,
                            'msg' => 'Bank Details Updated Successfully',
                            'type' => 'green',
                            'icon' => 'fa fa-check',
                            'title' => 'Success',
                        ];
                    } else {
                        DB::rollback();
                        DB::connection('pgsql_paywrite')->rollback();
                        $response = [
                            'exception' => true,
                            // 'exception_message' => $e->getMessage(),
                            'exception_message' =>
                                'Something went wrong. May be session time out logout and login again.',
                        ];
                    }
                } catch (\Exception $e) {
                    // dd($e);
                    DB::rollback();
                    DB::connection('pgsql_paywrite')->rollback();
                    $response = [
                        'exception' => true,
                        'exception_message' => $e->getMessage(),
                        // 'exception_message' =>
                        // 'Something went wrong. May be session time out logout and login again.',
                    ];
                    $statusCode = 400;
                } finally {
                    // dd($response);
                    return response()->json($response, $statusCode);
                }
            }
            if ($opreation_type == 'T') {
                $bulk_id_arr = explode(',', $applicant_id);
                // dd($bulk_id_arr);
                $scheme_id = $request->scheme_id;
                try {
                    $count = 0;
                    DB::beginTransaction();
                    foreach ($bulk_id_arr as $key => $value) {
                        $count++;
                        $user_id = AuthChecker::getUserId();
                        $ip_address = request()->ip();
                        $mappingLevel = $request->session()->get('level');
                        $district_code = $request->session()->get('distCode');
                        $is_first = $request->session()->get('is_first');
                        $is_urban = $request->session()->get('is_urban');
                        $body_code = $request->session()->get('bodyCode');
                        $role_id = $request->session()->get('role_id');
                        $table_name = $this->getSchemaName($scheme_id);
                        $query = "SELECT id AS beneficiary_id, CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) AS name, bank_code, branch_name, bank_name, bank_ifsc, new_bank_code, new_bank_ifsc, block_ulb_name, created_by_dist_code FROM pension.ben_payment_details_bank_code_dup WHERE id = " . $value . " AND scheme_id = " . $scheme_id . "";
                        // dd($query);
                        $ben_details = DB::connection('pgsql_mis')->select($query);
                        if ($ben_details == null) {
                            return $response = [
                                'status' => 1,
                                'msg' => 'Somethimg went wrong.',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        } else {
                            $updateBenDetailsData = [
                                'original_application_id' => $ben_details[0]->beneficiary_id,
                                'dist_code' => $ben_details[0]->created_by_dist_code,
                                'scheme_id' => $scheme_id,
                                'remarks' => $accept_reject_comments,
                                'user_id' => $user_id,
                                'update_code' => 400,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'ip_address' => $ip_address,
                                'action_by' => $user_id,
                                'action_ip_address' => $request->ip(),
                                'action_type' => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod()
                            ];

                            $updateDupTable = [];
                            $updateDupTable['revert_remarks'] = $accept_reject_comments;
                            $updateDupTable['next_level_role_id'] = -97;
                            $updateDupTable['is_approved'] = 2;
                            $is_insert = UpdateBenDetails::insert($updateBenDetailsData);
                            if ($is_insert) {
                                $is_dup_update = DB::table('pension.ben_payment_details_bank_code_dup')->where('id', $value)->update($updateDupTable);
                                if ($is_dup_update) {
                                    $response = [
                                        'status' => 1,
                                        'msg' => 'Bank Details Reverted To Verifier',
                                        'type' => 'green',
                                        'icon' => 'fa fa-check',
                                        'title' => 'Success',
                                    ];
                                } else {
                                    $response = [
                                        'status' => 3,
                                        'msg' => '3 Somethimg went wrong!!',
                                        'type' => 'red',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];
                                }
                            }

                        }
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    // dd($e);
                    DB::rollback();
                    $response = [
                        'exception' => true,
                        'exception_message' => $e->getMessage(),
                        // 'exception_message' =>
                        // 'Something went wrong. May be session time out logout and login again.',
                    ];
                    $statusCode = 400;
                } finally {
                    // dd($response);
                    return response()->json($response, $statusCode);
                }
            }
        }
    }
}
