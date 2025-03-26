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
use App\BankDetails;
use App\UrbanBody;
use App\Ward;
use App\GP;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Helpers\AuthChecker;
use App\Configduty;
use Illuminate\Support\Facades\Route;


class DuplicateBanklppController extends Controller
{
    protected $source_type;
    protected $ben_status;
    public function __construct()
    {
        // $this->scheme_id = 20;
        $this->source_type = 'ss_nfsa';
        $this->ben_status = -97;
        //return redirect("/")->with('error', '');
    }
    public function dupList(Request $request)
    {
        $this->middleware('auth');
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        $errormsg = Config::get('constants.errormsg');
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                $urban_body_code = NULL;
        $district_code= NULL;
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == 8 || $roleObj['scheme_id'] == 9) {
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
        if ($designation_id == 'Approver') {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if ($designation_id == 'Verifier') {
            $verifier_condition = ' and p.created_by_local_body_code=' . $urban_body_code;
        } else {
            $verifier_condition = '';
        }
        if ($is_active == 0 || empty($district_code)) {
            return redirect("/")->with('error', 'User Disabled. ');
        }
        $query = "select A.bank_code,A.cnt
        from
        (
        select bank_code,count(1) as cnt
        from pension.ben_payment_details_bank_code_dup where scheme_id in(8,9) and  next_level_role_id=" . $this->ben_status . " 
        group  by bank_code 
        ) as A WHERE EXISTS
            (SELECT 1
             FROM pension.ben_payment_details_bank_code_dup p
             WHERE  p.bank_code = A.bank_code
               AND p.created_by_dist_code=" . $district_code . " $verifier_condition) order by cnt desc";
        $rows = DB::select($query);
        $errormsg = Config::get('constants.errormsg');
        return view(
            'DuplicateBankLPP.dupListlpp',
            [
                'district_code' => $district_code,
                'data' => $rows,
                'sessiontimeoutmessage' => $errormsg['sessiontimeOut']
            ]
        );
    }
    public function listView(Request $request)
    {
        $this->middleware('auth');
        $urban_body_code = NULL;
        $district_code= NULL;
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        $errormsg = Config::get('constants.errormsg');
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == 8 || $roleObj['scheme_id'] == 9) {
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
        if ($designation_id == 'Approver') {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if ($designation_id == 'Verifier') {
            $verifier_condition = ' and p.created_by_local_body_code=' . $urban_body_code;
        } else {
            $verifier_condition = '';
        }
        if ($is_active == 0 || empty($district_code)) {
            return redirect("/")->with('error', 'User Disabled. ');
        }
        if (empty($request->bank_code)) {
            return redirect("/deDupBankListLPP")->with('error', 'Account No.not found');
        }
        $reject_revert_reason = RejectRevertReason::where('status', true)->get();
        $block = Taluka::get();
        $UrbanBody = UrbanBody::get();
        $Ward = Ward::get();
        $GP = GP::get();
        $query = "select B.scheme_name,A.* FROM pension.ben_payment_details_bank_code_dup A join m_scheme B  on A.scheme_id=B.id
        WHERE A.scheme_id in(8,9) and A.next_level_role_id=" . $this->ben_status . "  and trim(A.bank_code) = '" . trim($request->bank_code) . "' order by A.ben_fname";
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
            $ben_list[$i]['scheme_name'] = $arr->scheme_name;
            $ben_list[$i]['scheme_id'] = $arr->scheme_id;
            $local_body = '';
            if (!empty($arr->rural_urban_id)) {
                if ($arr->rural_urban_id == 1) {
                    $local_body = $UrbanBody->where('urban_body_code', $arr->block_ulb_code)->first();
                    if (!empty($local_body)) {
                        $ben_list[$i]['local_body_name'] =   trim($local_body->urban_body_name);
                    } else {
                        $ben_list[$i]['local_body_name'] =   'NA';
                    }
                    $gp_ward = $Ward->where('urban_body_ward_code', $arr->gp_ward_code)->first();
                    if (!empty($gp_ward)) {
                        $ben_list[$i]['gp_ward_name'] =   trim($gp_ward->urban_body_ward_name);
                    } else {
                        $ben_list[$i]['gp_ward_name'] =   'NA';
                    }
                } else {
                    $local_body = $block->where('block_code', $arr->block_ulb_code)->first();
                    $gp_ward = $GP->where('gram_panchyat_code', $arr->gp_ward_code)->first();
                    if (!empty($local_body)) {
                        $ben_list[$i]['local_body_name'] =   trim($local_body->block_name);
                    } else {
                        $ben_list[$i]['local_body_name'] =   'NA';
                    }
                    if (!empty($gp_ward)) {
                        $ben_list[$i]['gp_ward_name'] =   trim($gp_ward->gram_panchyat_name);
                    } else {
                        $ben_list[$i]['gp_ward_name'] =   'NA';
                    }
                }
            } else {
                $ben_list[$i]['local_body_name'] =   'NA';
                $ben_list[$i]['gp_ward_name'] =  'NA';
            }

            if ($designation_id = 'Approver') {
                if ($arr->created_by_dist_code == $district_code) {
                    $allowed = 1;
                } else {
                    $allowed = 0;
                }
            } else if ($designation_id = 'Verifier') {
                if ($arr->created_by_dist_code == $district_code && $arr->created_by_local_body_code == $urban_body_code) {
                    $allowed = 1;
                } else {
                    $allowed = 0;
                }
            }
            $ben_list[$i]['allowed'] = $allowed;
            $i++;
        }
        // dd($ben_list);
        $errormsg = Config::get('constants.errormsg');
        return view(
            'DuplicateBankLPP.dupListViewLPP',
            [
                'reject_revert_reason' => $reject_revert_reason,
                'district_code' => $district_code,
                'data' => $ben_list,
                'bank_ifsc' => $request->bank_ifsc,
                'bank_code' => $request->bank_code,
                'designation_id' => $designation_id,
                'sessiontimeoutmessage' => $errormsg['sessiontimeOut']
            ]
        );

    }
    public function dedupBankUpdateLPP(Request $request)
    {
        $this->middleware('auth');
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        $errormsg = Config::get('constants.errormsg');
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == 8 || $roleObj['scheme_id'] == 9) {
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
        if ($designation_id == 'Approver') {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if ($designation_id == 'Verifier') {
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
            return redirect("/deDupBankListLPP")->with('error', 'Bank Account No.Not Found');
        }
        if (empty($application_id)) {
            return redirect("/deDupBankViewList?bank_code=" . $bank_code)->with('error', 'Application ID Not Found');
        }
        if (!ctype_digit($application_id)) {
            return redirect("/deDupBankViewList?bank_code=" . $bank_code)->with('error', 'Application ID Not Valid');
        }
        $scheme_id=$request->scheme_id;
        $scheme_row = Scheme::where('id', $scheme_id)->first();
        if (!empty($scheme_row->short_code)) {
            $schema = $scheme_row->short_code;
        } else {
            $schema = "pension";
        }
        $row = DB::table($schema . '.beneficiary')->where('dup_bank',1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code','next_level_role_id')->first();
        if(!empty($row))
        {
            if($row->next_level_role_id <0)
            {
                return redirect("/deDupBankViewList?bank_code=" . $bank_code)->with('error', 'Application is Already rejected');
            }
       }
        // dd($row);
        // $row_pen = DB::table('lokprasar_pensioner.beneficiary')->where('dup_bank',1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code');
        // $row = DB::table('lokprasar_retainer.beneficiary')->where('dup_bank',1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code')
        // ->union($row_pen)->first();
        if (empty($row->id)) {
            return redirect("/deDupBankListLPP")->with('error', ' Application Id Not found in Db');
        }
        $docs_uploaded= DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('created_by_dist_code',$district_code)->where('beneficiary_id',$row->id)->select('id','beneficiary_id','document_type','doc_type_name','attched_document')->get()->pluck('document_type');
        $doc_profile = DocumentType::select('id')->where('is_profile_pic', TRUE)->first();
        // if (!empty($docs)) {
        //     $encolserCount = 1;
        // }
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->whereIn('scheme_code', [8,9])->first();
        
        if (!empty($doc_id_list)) {
            $doc_id_list = $doc_id_list->toArray();
        }
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
      
            if (count($doc_list) > 0) {
            $encloser_list[$i]['can_download'] = 0;
            $doc_bank = DocumentType::where('id', 10)->first();
            $encloser_list[$i]['id'] = $doc_bank->id;
            $encloser_list[$i]['is_profile_pic'] = 0;
            $encloser_list[$i]['doc_size_kb'] =  $doc_bank->doc_size_kb;
            $encloser_list[$i]['doc_name'] =  $doc_bank->doc_name;
            $encloser_list[$i]['doc_type'] =  $doc_bank->doc_type;
            $encloser_list[$i]['required'] = 1;
            }
    return view(
        'DuplicateBankLPP.dupBankUpdateLPP',
        [
            'application_id' => $application_id,
            'row' => $row,
            'is_faulty' => $is_faulty,
            'bank_code' => $bank_code,
            'encloser_list' => $encloser_list,
        ]
    );
    }
    public function dedupBankUpdatePostLPP(Request $request)
    {
        $this->middleware('auth');
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        $urban_body_code = NULL;
        $district_code= NULL;
        $mapping_level=NULL;
        $errormsg = Config::get('constants.errormsg');
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == 8 || $roleObj['scheme_id'] == 9) {
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
        if ($designation_id == 'Approver') {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if ($designation_id == 'Verifier') {
            $verifier_condition = ' and p.created_by_local_body_code=' . $urban_body_code;
        } else {
            $verifier_condition = '';
        }
        if ($is_active == 0 || empty($district_code)) {
            return redirect("/")->with('error', 'User Disabled. ');
        }
        $old_bank_code = trim($request->old_bank_code);
      
        $bank_ifsc = trim($request->bank_ifsc_code);
        $bank_code = trim($request->bank_account_number);
        $application_id = $request->application_id;
        $is_faulty = $request->is_faulty;
        if (empty($old_bank_code)) {
            return redirect("/deDupBankListLPP")->with('error', 'Bank Account No.Not Found');
        }
        if (empty($application_id)) {
            return redirect("/deDupBankViewList?bank_code=" . $old_bank_code)->with('error', 'Application ID Not Found');
        }
        if (!ctype_digit($application_id)) {
            return redirect("/deDupBankViewList?&bank_code=" . $old_bank_code)->with('error', 'Application ID Not Valid');
        }
        $doc_profile = DocumentType::select('id')->where('is_profile_pic', TRUE)->first();
        
        $row_pen = DB::table('lokprasar_pensioner.beneficiary')->where('dup_bank',1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code', 'payment_count', 'last_paid_yymm','scheme_id','created_by_dist_code','created_by_local_body_code');
        $row= DB::table('lokprasar_retainer.beneficiary')->where('dup_bank',1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code', 'payment_count', 'last_paid_yymm','scheme_id','created_by_dist_code','created_by_local_body_code')
        ->union($row_pen)->first();
        if (empty($row->id)) {
            return redirect("/deDupBankViewList?bank_code=" . $old_bank_code)->with('error', 'Application Id Not found in Db');
        }
        $i=0;
            $encloser_list[$i]['can_download'] = 0;
            $doc_bank = DocumentType::where('id', 10)->first();
            $encloser_list[$i]['id'] = $doc_bank->id;
            $encloser_list[$i]['is_profile_pic'] = 0;
            $encloser_list[$i]['doc_size_kb'] =  $doc_bank->doc_size_kb;
            $encloser_list[$i]['doc_name'] =  $doc_bank->doc_name;
            $encloser_list[$i]['doc_type'] =  $doc_bank->doc_type;
            $encloser_list[$i]['required'] = 1;
            // dd($encloser_list);
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
                foreach ($encloser_list as  $value) {
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
                $check_ifsc_count = BankDetails::where('ifsc', trim($request->bank_ifsc_code))->where('is_active',1)->count();
                $bank_details = BankDetails::whereraw("trim(ifsc)='$request->bank_ifsc_code'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
                $new_bank_code=$bank_details->bank_code;
                //dd($check_ifsc_count);
                if ($check_ifsc_count == 0) {
                    $return_text = 'IFSC not Found in our System..Please try different';
                    return redirect('dedupBankLPPUpdate?bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
                }
                
                // $row_count = DB::table($schema . '.beneficiary')->where('is_rejected',0)->whereraw("trim(bank_code)='$bank_code'")->count();
                $benDuplicateAcCount1 = DB::table("lokprasar_retainer.beneficiary")->select('id')
                    ->whereRaw("trim(bank_code)='$bank_code'")->where('is_rejected',0);
        
                $row_count = DB::table("lokprasar_pensioner.beneficiary")->select('id')
                    ->whereRaw("trim(bank_code)='$bank_code'")->where('is_rejected',0)
                    ->union($benDuplicateAcCount1)->get()
                    ->count('id'); 
              
                
                if ($row_count > 0) {
                    $return_text = 'Duplicate Bank Account Details.';
                    return redirect('dedupBankLPPUpdate?bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
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
                }
                else{
                    $dup_bank_pending=0;
                }
               // dd($dup_bank_pending);
                $today = date("Y-m-d h:i:s");
                $new_value = [];
                try {
    
                    if ($row->payment_count == 0) {
                        $new_last_paid_yymm = 0;
                    } else {
                        $new_last_paid_yymm = $row->last_paid_yymm;
                    }
                    $is_lot_update=0;
                    DB::beginTransaction();
                    DB::connection('pgsql_encwrite')->beginTransaction();
                    $new_value['bank_name'] = trim($request->name_of_bank);
                    $new_value['branch_name'] = trim($request->bank_branch);
                    $new_value['bank_ifsc'] =  trim($request->bank_ifsc_code);
                    $new_value['bank_code'] = trim($request->bank_account_number);
                    $modelmainArch = array();
                    $modelmainArch['op_type']  = 101;
                    $modelmainArch['application_id']  = $application_id;
                    $modelmainArch['old_data']  =  json_encode($row);
                    $modelmainArch['new_data']  =  json_encode($new_value);
                    $modelmainArch['scheme_id']  = $row->scheme_id;
                    $modelmainArch['created_at']  =  $today;
                    $modelmainArch['user_id']  =  $user_id;
                    $modelmainArch['ip_address']  =  $request->ip();
                    $modelmainArch['module_name'] = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod() ;
                    // dd($modelmainArch);
                    $modelmainArchStatus = DB::table('ben_accept_reject_info')->insert($modelmainArch);
                    $pension_details_bank_arr = array();
                    $pension_details_bank_arr['bank_name']  = trim($request->name_of_bank);
                    $pension_details_bank_arr['branch_name']    = trim($request->bank_branch);
                    $pension_details_bank_arr['bank_code']    = trim($request->bank_account_number);
                    $pension_details_bank_arr['bank_ifsc']   = trim($request->bank_ifsc_code);
                    $pension_details_bank_arr['npci_bank_code']   = trim($new_bank_code);
                    $pension_details_bank_arr['dup_bank'] = 0;
                    $pension_details_bank_arr['dup_bank_pending'] = $dup_bank_pending;
                    $pension_details_bank_arr['lot_generated'] = 0;
                    $pension_details_bank_arr['bank_edited'] = 0;
                    $pension_details_bank_arr['last_paid_yymm'] = $new_last_paid_yymm;
                    $scheme_row = Scheme::where('id', $row->scheme_id)->first();
                    if (!empty($scheme_row->short_code)) {
                        $schema = $scheme_row->short_code;
                    } else {
                        $schema = "pension";
                    }
                    
                    $is_saved_bank = DB::table($schema . '.beneficiary')->where('dup_bank',1)->where('created_by_dist_code', $district_code)->where('id', $application_id)->update($pension_details_bank_arr);
                    $payments_arr_new = array();
                    $payments_arr_new['new_bank_code']    = trim($request->bank_account_number);
                    $payments_arr_new['new_bank_ifsc']   = trim($request->bank_ifsc_code);
                    $payments_arr_new['next_level_role_id']    = 101;
                    $is_delete_bank_payment = DB::table('pension.ben_payment_details_bank_code_dup')->where('created_by_dist_code', $district_code)->where('next_level_role_id', $this->ben_status)->where('id', $application_id)->update($payments_arr_new);
                    // dd($is_delete_bank_payment);
                    $k = 0;
                    $doc_type_in = array();
                    $file_copy_count = 0;
                    $c = 0;
                    $uploaded_doc = array();
                    if (count($encloser_list) > 0) 
                    {
                        foreach ($encloser_list as $enc_row)
                        {
                            if ($request->hasFile('doc_' . $enc_row['id'])) 
                            {
                                
                                $c++;
                                $doc_file = $request->file('doc_' . $enc_row['id']);
                                $img_data = file_get_contents($doc_file);
                                // dd($img_data);
                                $u_extension = $doc_file->getClientOriginalExtension();
                                $mime_type = $doc_file->getMimeType();
                                if(strtolower($mime_type)=='image/jpeg'){
                                    if($u_extension=='jpg' || $u_extension=='jpeg'){
                                      $extension=$u_extension;
                                    }
                                    else{
                                        $errors = array();
                                        $errorMsg = "You are trying to upload an incorrect file for ".$doc_bank->doc_name;
                                        array_push($errors, $errorMsg);
                                        DB::rollback();
                                        DB::connection('pgsql_encwrite')->rollback();
                                        return redirect('dedupBankLPPUpdate?bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $errorMsg);  
                                    }
                                  }
                                else if(strtolower($mime_type)=='image/png'){
                                    $extension='png';
                                }else if(strtolower($mime_type)=='image/gif'){
                                    $extension='gif';
                                }else if(strtolower($mime_type)=='application/pdf'){
                                    $extension='pdf';
                                }
                                else{
                                    $errors = array();
                                    $errorMsg = "You are trying to upload an incorrect file for ".$doc_bank->doc_name;
                                    array_push($errors, $errorMsg);
                                    DB::rollback();
                                    DB::connection('pgsql_encwrite')->rollback();
                                    return redirect('dedupBankLPPUpdate?bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $errorMsg);
                                }
                                if($u_extension!=$extension){
                                    $errors = array();
                                    $errorMsg = "You are trying to upload an incorrect file for ".$doc_bank->doc_name;
                                    array_push($errors, $errorMsg);
                                    DB::rollback();
                                    DB::connection('pgsql_encwrite')->rollback();
                                    return redirect('dedupBankLPPUpdate?bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $errorMsg);
                                }
                                $base64 = base64_encode($img_data);
                                $ip_address = request()->ip();
                                $c_datetime = date('Y-m-d H:i:s', time());
                                if ($request->hasFile('doc_' . $enc_row['id'])) 
                                {
                                    $fun_call = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                                        in_beneficiary_id => ".$application_id.",
                                        in_scheme_id => ".$row->scheme_id.",
                                        in_document_type => ".$enc_row['id'].",
                                        in_attched_document => '".$base64."',
                                        in_created_by_level => '".$mapping_level."',
                                        in_created_by => ".$user_id.",
                                        in_ip_address => '".$ip_address."',
                                        in_document_extension => '".$extension."',
                                        in_document_mime_type => '".$mime_type."',
                                        in_created_by_dist_code => ".$district_code.",
                                        in_created_by_local_body_code => ".$row->created_by_local_body_code.",
                                        in_doc_type_name => '".$enc_row['doc_name']."',
                                        in_datetime => '". $c_datetime ."'
                                        );"
                                    );       
                                    if ( $fun_call[0]->ben_docs_insert_archive == 1) {
                                    
                                    $file_copy_count++;
                                   }
                                }
                                
                            }
                        }
                    }
                    
    
                        if ($c != $file_copy_count) {
                            $return_text = 'File Uploading problem.please try later';
                            return redirect('dedupBankLPPUpdate?bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
                        }
                             
    
                            if($is_lot_update){
                                $lot_update_arr=array();
                                $lot_update_arr['is_active']=-97;
                                $lot_update = DB::table('sbi.transaction_lot_details')->where('id',$max_lot_id)->where('pension_id', $application_id)->update($lot_update_arr);
                            }
                            else
                            $lot_update=1;
                         
                    $dup_pass_bank=1;
                   
                    if ($dup_pass_bank && $modelmainArchStatus &&  $is_saved_bank  && $is_delete_bank_payment && $lot_update ) {
                        
                        DB::commit();
                        DB::connection('pgsql_encwrite')->commit();
                        $return_text = "Beneficiary informations successfully updated with Beneficiary Id:" . $row->id;
                        return redirect('deDupBankViewList?bank_code=' . $old_bank_code)->with('success', $return_text);
                    } else {
                        DB::rollback();
                        DB::connection('pgsql_encwrite')->rollback();
                        //  dd('ok');
                        $return_text = 'Error Occur . Please try later.';
                        return redirect('dedupBankLPPUpdate?bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
                    }
                
                } catch (\Exception $e) {
                    DB::rollback();
                    DB::connection('pgsql_encwrite')->rollback();
                     dd($e);
                    $return_text = 'Error Occur . Please try later..';
                    return redirect('dedupBankLPPUpdate?bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
                }
            }  

    }
    public function dedupBankSameLPP(Request $request)
    {
        $this->middleware('auth');
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        $urban_body_code = NULL;
        $district_code= NULL;
        $mapping_level=NULL;
        $errormsg = Config::get('constants.errormsg');
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == 8 || $roleObj['scheme_id'] == 9) {
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
        if ($designation_id == 'Approver') {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if ($designation_id == 'Verifier') {
            $verifier_condition = ' and p.created_by_local_body_code=' . $urban_body_code;
        } else {
            $verifier_condition = '';
        }
        if ($is_active == 0 || empty($district_code)) {
            return redirect("/")->with('error', 'User Disabled. ');
        }
        $old_bank_ifsc = trim($request->old_bank_ifsc);
        $old_bank_code = trim($request->old_bank_code);
        $application_id = $request->application_id;
        if (empty($old_bank_ifsc)) {
            return redirect("/deDupBankListLPP")->with('error', 'Bank IFSC Not Found');
        }
        if (empty($old_bank_code)) {
            return redirect("/deDupBankListLPP")->with('error', 'Bank Account No.Not Found');
        }
        if (empty($application_id)) {
            return redirect("/deDupBankViewList?last_accno=" . $old_bank_code)->with('error', 'Application ID Not Found');
        }
        if (!ctype_digit($application_id)) {
            return redirect("/deDupBankViewList?last_accno=" . $old_bank_code)->with('error', 'Application ID Not Valid');
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
            $check_ifsc_count = BankDetails::where('ifsc', trim($request->old_bank_ifsc))->count();
            $check_ifsc_count = 1;

            $bank_details = BankDetails::whereraw("trim(ifsc)='$request->old_bank_ifsc'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
            $new_bank_code=$bank_details->bank_code;
            if ($check_ifsc_count == 0) {
                $return_text = 'IFSC not Found in our System..Please try different';
                return redirect('dedupBankLPPUpdate?bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
            }
            
            
            // $row_count = DB::table($schema . '.beneficiary')->where('is_rejected',0)->where('id', '!=', $application_id)->whereraw("trim(bank_code)='$old_bank_code'")->count();

            $benDuplicateAcCount1 = DB::table("lokprasar_retainer.beneficiary")->select('id')
                    ->whereRaw("trim(bank_code)='$old_bank_code'")->where('id', '!=', $application_id)->where('is_rejected',0);
        
                $row_count = DB::table("lokprasar_pensioner.beneficiary")->select('id')
                    ->whereRaw("trim(bank_code)='$old_bank_code'")->where('id', '!=', $application_id)->where('is_rejected',0)
                    ->union($benDuplicateAcCount1)->get()
                    ->count('id'); 

            if ($row_count > 0) {
                $return_text = 'Duplicate Bank Account Details.';
                return redirect('dedupBankLPPUpdate?bank_code=' . $old_bank_code . '&application_id=' . $application_id)->with('error', $return_text);
            }
            // $row = DB::table($schema . '.beneficiary')->where('dup_bank',1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code', 'payment_count', 'last_paid_yymm','scheme_id','created_by_dist_code')->first();
            $row_pen = DB::table('lokprasar_pensioner.beneficiary')->where('dup_bank',1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code', 'payment_count', 'last_paid_yymm','scheme_id','created_by_dist_code');
            $row= DB::table('lokprasar_retainer.beneficiary')->where('dup_bank',1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code', 'payment_count', 'last_paid_yymm','scheme_id','created_by_dist_code')
            ->union($row_pen)->first();

            if (empty($row)) {
                return redirect("/deDupBankViewList?bank_code=" . $old_bank_code)->with('error', 'Application Id Not found in Db');
            }
            $scheme_capacity_arr = Helper::getCapacity($row->scheme_id, $row->created_by_dist_code);
            if ($scheme_capacity_arr['visible'] == 1) {
                
                if ($scheme_capacity_arr['total_data'] < $scheme_capacity_arr['capacity']) {
                    $dup_bank_pending = 0;
                  } else {
                    $dup_bank_pending = 1;
                  }
            }
            else{
                $dup_bank_pending=0;
            }
            
            if ($row->payment_count == 0) {
                $new_last_paid_yymm = 0;
            } else {
                $new_last_paid_yymm = $row->last_paid_yymm;
            }
            $today = date("Y-m-d h:i:s");
            try {
                
                      $is_lot_update=0;
            

                DB::beginTransaction();
                $modelmainArch = array();
                $modelmainArch['op_type']  = 200;
                $modelmainArch['application_id']  = $application_id;
                $modelmainArch['scheme_id']  = $row->scheme_id;
                $modelmainArch['created_at']  =  $today;
                $modelmainArch['user_id']  =  $user_id;
                $modelmainArch['ip_address']  =  $request->ip();
                $modelmainArch['module_name'] = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod() ;
                $modelmainArchStatus = DB::table('ben_accept_reject_info')->insert($modelmainArch);
                $pension_details_bank_arr = array();
                $pension_details_bank_arr['dup_bank']=0;
                $pension_details_bank_arr['dup_bank_pending']=$dup_bank_pending;
                $pension_details_bank_arr['lot_generated'] = 0;
                $pension_details_bank_arr['bank_edited'] = 0;
                $pension_details_bank_arr['npci_bank_code']   = trim($new_bank_code);
                $pension_details_bank_arr['last_paid_yymm'] = $new_last_paid_yymm;
                $scheme_row = Scheme::where('id', $row->scheme_id)->first();
                    if (!empty($scheme_row->short_code)) {
                        $schema = $scheme_row->short_code;
                    } else {
                        $schema = "pension";
                    }

                $is_saved_bank = DB::table($schema . '.beneficiary')->where('dup_bank',1)->where('created_by_dist_code', $district_code)->where('id', $application_id)->update($pension_details_bank_arr);
                $payments_arr_new = array();
                $payments_arr_new['next_level_role_id']    = 200;
                $is_delete_bank_payment = DB::table('pension.ben_payment_details_bank_code_dup')->where('created_by_dist_code', $district_code)->where('next_level_role_id', $this->ben_status)->where('id', $application_id)->update($payments_arr_new);
                if($is_lot_update){
                    $lot_update_arr=array();
                    $lot_update_arr['is_active']=-97;
                    $lot_update = DB::table('sbi.transaction_lot_details')->where('id',$max_lot_id)->where('pension_id', $application_id)->update($lot_update_arr);
                }
                else
                $lot_update=1;

                
                if ($modelmainArchStatus && $is_saved_bank && $is_delete_bank_payment && $lot_update) {
                    DB::commit();
                    $return_text = "Beneficiary informations successfully updated with Application Id:" . $application_id;
                    return redirect("/deDupBankViewList?bank_ifsc=" . $old_bank_ifsc . "&bank_code=" . $old_bank_code)->with('success', $return_text);
                } else {
                    DB::rollback();
                    $return_text = $errormsg['roolback'];
                    return redirect('dedupBankLPPUpdate?bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id . '&is_faulty=' . $is_faulty)->with('error', $return_text);
                }
            } catch (\Exception $e) {
                
                DB::rollback();
                //dd($e);
                $return_text = $errormsg['roolback'];
                return redirect('dedupBankLPPUpdate?bank_ifsc=' . $old_bank_ifsc . '&bank_code=' . $old_bank_code . '&application_id=' . $application_id . '&is_faulty=' . $is_faulty)->with('error', $return_text);
            }
        }
    }
    public function dupBankRejectLPP(Request $request)
    {
        $this->middleware('auth');
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        $urban_body_code = NULL;
        $district_code= NULL;
        $errormsg = Config::get('constants.errormsg');
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == 8 || $roleObj['scheme_id'] == 9) {
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
        if ($designation_id == 'Approver' ) {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        if ($designation_id == 'Verifier') {
            $verifier_condition = ' and p.created_by_local_body_code=' . $urban_body_code;
        } else {
            $verifier_condition = '';
        }
        if ($is_active == 0 || empty($district_code)) {
            return redirect("/")->with('error', 'User Disabled. ');
        }
        $bank_code = $request->bank_code;
        // dd($bank_code);
        $application_id = $request->application_id;
         $is_bulk = $request->is_bulk;
        //  dd($is_bulk);
        $applicant_id_post = request()->input('applicantId');
        $comments = $request->comments;
        $rejected_cause = $request->reject_cause;
        if (empty($comments))
            $comments = NULL;
        
        if (empty($bank_code)) {
            return redirect("/deDupBankListLPP")->with('error', 'Bank Account No.Not Found');
        }
        if (empty($rejected_cause)) {
            return redirect("/deDupBankListLPP")->with('error', 'Rejected Cause Not Valid');
        }
        if (!ctype_digit($rejected_cause)) {
            return redirect("/deDupBankListLPP")->with('error', 'Rejected Cause Not Valid');
        }
        $today = date("Y-m-d h:i:s");
        if ($is_bulk == 0) {
            // dd($scheme_id);
            if (empty($application_id)) {
                return redirect("/deDupBankListLPP")->with('error', ' Application Id Not Found');
            }
            if (!ctype_digit($application_id)) {
                return redirect("/deDupBankListLPP")->with('error', ' Application Id Not Valid');
            }
            
            // $row_count = DB::table($schema . '.beneficiary')->where('dup_bank',1)->where('id', $application_id)->first();
            $row_pen = DB::table('lokprasar_pensioner.beneficiary')->where('dup_bank',1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code', 'payment_count', 'last_paid_yymm','scheme_id','created_by_dist_code');
            $row_count = DB::table('lokprasar_retainer.beneficiary')->where('dup_bank',1)->where('id', $application_id)->select('id', 'bank_ifsc', 'bank_name', 'branch_name', 'bank_code', 'payment_count', 'last_paid_yymm','scheme_id','created_by_dist_code')
            ->union($row_pen)->first();

            if (empty($row_count)) {
                return redirect("/deDupBankListLPP")->with('error', ' Application Id Not found in Db');
            }
            $scheme_row = Scheme::where('id', $row_count->scheme_id)->first();
            if (!empty($scheme_row->short_code)) {
                $schema = $scheme_row->short_code;
            } else {
                $schema = "pension";
            }

            try {
                DB::beginTransaction();

                $update_arr = array();
                $update_arr['next_level_role_id'] = -200;
                $update_arr['rejected_date'] = $today;
                $update_arr['rejected_by'] = $user_id;
                $input = ['dup_bank'=>0,'dup_bank_pending'=>0,'rejected_by' =>  $user_id,'rejected_date' =>  $today, 'next_level_role_id' => -200, 'is_rejected' =>1,'is_clean' =>10,'rejected_cause' => $rejected_cause, 'comments' => $comments];
                // dd($district_code);
                $is_saved1 = DB::table($schema . '.beneficiary')->where('created_by_dist_code', $district_code)->where('id', $application_id)->update($input);
                $is_saved2 = DB::table('pension.ben_payment_details_bank_code_dup')->whereraw("trim(bank_code)='$bank_code'")->where('id', $application_id)->where('created_by_dist_code', $district_code)->update($update_arr);
                $modelmainArch = array();
                $modelmainArch['op_type']  = -200;
                $modelmainArch['application_id']  = $application_id;
                $modelmainArch['scheme_id']  = $row_count->scheme_id;
                $modelmainArch['created_at']  =  $today;
                $modelmainArch['user_id']  =  $user_id;
                $modelmainArch['ip_address']  =  $request->ip();
                $modelmainArch['module_name'] = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod() ;
                $modelmainArchStatus = DB::table('ben_accept_reject_info')->insert($modelmainArch);
                $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
                if (in_array($row_count->scheme_id, $scheme_dedup_list)) {
                    $free_pending_bank_duplicate_arr = DB::select("select ".$schema.".free_pending_bank_duplicate_data(in_scheme_id => ".$row_count->scheme_id.", in_district_code => ".$district_code.")");
                    $free_pending_bank_duplicate_data=$free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
                    if(!empty(trim($row_count->mobile_no))){
                        $sp_mobile=$row_count->mobile_no;
                    }
                    else{
                        $sp_mobile=0;  
                    }
                    $reject_dup_adjustment_arr = DB::select("select ".$schema.".reject_dup_adjustment(
                    in_old_bank_ifsc => '".$row_count->bank_ifsc."', 
                    in_old_bank_code => '".$row_count->bank_code."', 
                    in_old_aadhar_no => '".$row_count->aadhar_no."', 
                    in_old_mobile_no => ".$sp_mobile."
                    )");
                    $reject_dup_adjustment=$reject_dup_adjustment_arr[0]->reject_dup_adjustment;
                }
                else{
                    $reject_dup_adjustment=1;
                    $free_pending_bank_duplicate_data=1;
                }
                //dd($free_pending_bank_duplicate_arr);
                
                //  dump($is_saved1);dump($is_saved2);dump($modelmainArchStatus);dump($free_pending_bank_duplicate_data);dd($is_saved1);
                if ($is_saved1 && $is_saved2 &&  $modelmainArchStatus && $free_pending_bank_duplicate_data && $reject_dup_adjustment) {
                    // dd('okk');
                    DB::commit();
                    $return_text = "Beneficiary informations successfully rejected with Beneficiary Id:" . $application_id;
                    return redirect("/deDupBankViewList?bank_code=" . $bank_code)->with('success', $return_text);
                } else {
                    DB::rollback();
                    return redirect("/deDupBankViewList?bank_code=" . $bank_code)->with('error', $errormsg['roolback']);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return redirect("/deDupBankViewList?bank_code=" . $bank_code)->with('error', $errormsg['roolback']);
            }
        } 
    }
    public function generate_excel_listLPP(Request $request)
    {
        try {
            $this->middleware('auth');
            $is_active = 0;
            $designation_id = Auth::user()->designation_id;
            $urban_body_code = NULL;
            $district_code= NULL;
            $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                        foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == 8 || $roleObj['scheme_id'] == 9) {
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
            if ($designation_id == 'Approver' ) {
                $is_active = 1;
            } else {
                $is_active = 0;
            }
            if ($designation_id == 'Verifier') {
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
            from pension.ben_payment_details_bank_code_dup where scheme_id in(8,9) 
            group  by bank_code 
            ) as A WHERE EXISTS
                (SELECT 1
                 FROM pension.ben_payment_details_bank_code_dup p
                 WHERE p.scheme_id in(8,9) and  p.bank_code = A.bank_code
                   AND p.created_by_dist_code=" . $district_code . " $verifier_condition) order by cnt desc
                   ) as P JOIN  pension.ben_payment_details_bank_code_dup Q 
                   ON   trim(P.bank_code)=trim(Q.bank_code) where Q.scheme_id in(8,9)  order by Q.bank_code,Q.ben_fname";
            $rows = DB::connection('pgsql_mis')->select($query);
            // dd($rows);
            foreach ($rows as $arr) {

                $ben_list[$i]['bank_code'] = $arr->bank_code;
                $ben_list[$i]['local_body_code'] = $arr->created_by_local_body_code;
                $ben_list[$i]['application_id'] = $arr->id;
                $ben_list[$i]['full_name'] = $arr->full_name;
                $ben_list[$i]['mobile_no'] = $arr->mobile_no;
                $district_row = $district->where('district_code', $arr->created_by_dist_code)->first();
                $ben_list[$i]['district_name'] =  $district_row->district_name;
                $local_body = '';
                if (strlen($arr->created_by_local_body_code) == 5) {
                    $local_body = $subdistrict->where('sub_district_code', $arr->created_by_local_body_code)->first();
                    $ben_list[$i]['local_body_name'] =  'SubDivision-' . $local_body->sub_district_name;
                } else {
                    $local_body = $block->where('block_code', $arr->created_by_local_body_code)->first();
                    $ben_list[$i]['local_body_name'] =  'Block-' . $local_body->block_name;
                }
                if ($designation_id = 'Approver') {
                    if ($arr->created_by_dist_code == $district_code) {
                        $allowed = 1;
                    } else {
                        $allowed = 0;
                    }
                } else if ($designation_id = 'Verifier') {
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
            // $scheme_row = Scheme::where('id', $scheme_id)->first();
            $filename = "Bank_Account_Duplicate_" .  'LPP Pensioner & Retainer' . "_" . date('d/m/Y') . ".xls";
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
             dd($e);
            return redirect("/deDupBankListLPP")->with('error', 'Some Error.. please try later');
        }
    }
}
