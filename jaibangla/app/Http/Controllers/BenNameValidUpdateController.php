<?php

namespace App\Http\Controllers;

use App\Helpers\AuthChecker;
use App\Http\Middleware\Verifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Configduty;
use Illuminate\Support\Facades\Auth;
use App\Scheme;
use App\District;
use App\UrbanBody;
use App\GP;
use App\Ward;
use App\Taluka;
use App\Helpers\DataInsert;
use App\BenEntry;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use App\BenFailedPaymentDetails;
use App\BankDetails;
use App\AcceptRejectInfo;
use App\BenNameFailedlog;
use App\BenPaymentDetails;






class BenNameValidUpdateController extends Controller
{
    private $aadhar_doc; // For Aadhar Document
    private $bank_doc; // For Bank Document
    private $disability_doc; // For Disability Document
    private $caste_doc; // For caste Document
    private $epic_doc; // For EPIC Document
    private $ration_doc; // For Ration Document
    private $husband_doc; // For Husband Death Certificate Document

    private $min_matching_score = 90;
    private $max_matching_score = 100;

    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
        $this->aadhar_doc = 6;
        $this->bank_doc = 10;
        $this->disability_doc = 4;
        $this->caste_doc = 3;
        $this->epic_doc = 7;
        $this->ration_doc = 5;
        $this->husband_doc = 105;
    }

    public function Verifierindex()
    {
        $auth = AuthChecker::VerifierPermission();
        if ($auth) {
            return view('NameValidation_90_100/Verifier_landing');
        } else {
            return redirect()->route('/')->with('error', 'Not Allowded');
        }
    }
    public function index(Request $request)
    {
        $auth = AuthChecker::VerifierPermission();
        if ($auth) {
            try {
                $user_id = AuthChecker::getUserId();
                $mapObj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
                $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where scheme_id in (2,10,11) and is_active=1 and user_id=" . $user_id . " )"));

                if (empty($mapObj)) {
                    return redirect('/')->with('error', 'No Duty Assigned');
                }
                $is_urban = $mapObj->is_urban;
                $district_code = $mapObj->district_code;
                $blockUlbCode = null;
                $urban_bodys = null;
                $talukas = null;
                $gps = null;
                if ($is_urban == 1) {
                    $blockUlbCode = $mapObj->urban_body_code;
                    $urban_bodys = UrbanBody::where('sub_district_code', $blockUlbCode)->select('urban_body_code', 'urban_body_name')->get();
                } elseif ($is_urban == 2) {
                    $blockUlbCode = $mapObj->taluka_code;
                    $gps = GP::where('block_code', $blockUlbCode)->get();
                }
                return view('NameValidation_90_100/Name_Verifier_index', ['schemes' => $scheme, 'dist_code' => $mapObj->district_code, 'is_urban' => $is_urban, 'urban_bodys' => $urban_bodys, 'talukas' => $talukas, 'gps' => $gps]);

            } catch (\Exception $e) {

            }

        } else {
            return redirect()->route('/')->with('error', 'Not Allowded');
        }
    }


    //getDataNameValidationFailed90to100
    public function getBenNameList(Request $request)
    {

        // dd($request->all());
        if ($request->ajax()) {
            $user_id = Auth::user()->id;
            $scheme_id = $request->scheme_id;
            $dutyObj = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
            $is_verifier = Authchecker::VerifierPermission();
            $mapping_level = $dutyObj->mapping_level;
            $urban_body = $request->munc;
            $gp_ward = $request->gp_ward;
            if ($is_verifier && $mapping_level == 'Block') {
                $query = "select * from
                (select T.* from
                (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                  edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                 JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                 LEFT JOIN 
                     (
                       select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                         union all
                       select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                      ) bu ON bu.block_ulb_code=b.block_ulb_code  
                LEFT JOIN 
                      (
                        select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                         union all
                        select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                        ) gw ON gw.gp_ward_code=b.gp_ward_code 
                         WHERE b.dup_bank = 0 and f.if_previous_approve=0 and  f.local_body_code=" . $dutyObj->taluka_code . " and f.scheme_id = " . $scheme_id . " and b.legacy_validation = 0 and  f.edited_status in (0,1,2) and b.ben_status=1 AND f.failed_type IN(2) AND f.is_minor_mismatch = 1 AND b.is_eligible = true AND b.acc_validated = 4";
                if (!empty($request->gp_ward)) {
                    // $old_query .= " AND b.gp_ward_code=" . $request->filter_1 . "";
                    //  $completequery .= " AND gp_ward_code=".$request->filter_1."";
                    $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                     edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                     JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                     LEFT JOIN 
                         (
                           select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                             union all
                           select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                          ) bu ON bu.block_ulb_code=b.block_ulb_code  
                    LEFT JOIN 
                          (
                            select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                             union all
                            select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                            ) gw ON gw.gp_ward_code=b.gp_ward_code 
                              WHERE b.dup_bank = 0 and f.if_previous_approve=0 and b.legacy_validation = 0 and f.local_body_code=" . $dutyObj->taluka_code . "  and f.scheme_id = " . $scheme_id . " and  f.edited_status in (0,1,2) and b.ben_status=1 AND f.failed_type IN(2) AND f.is_minor_mismatch = 1 AND b.gp_ward_code=" . $request->gp_ward . "AND b.is_eligible = true AND b.acc_validated = 4";
                }
                // if (!empty($request->pay_mode)) {
                //     $query .= " AND f.pmt_mode=" . $request->pay_mode . "";
                // }
                $query .= "  order by b.ben_id";
            } elseif ($is_verifier && $mapping_level == 'Subdiv') {

                $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                    edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                     JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                     LEFT JOIN 
                         (
                           select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                             union all
                           select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                          ) bu ON bu.block_ulb_code=b.block_ulb_code  
                    LEFT JOIN 
                          (
                            select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                             union all
                            select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                            ) gw ON gw.gp_ward_code=b.gp_ward_code 
                             WHERE b.dup_bank = 0 and f.if_previous_approve=0 and  f.local_body_code=" . $dutyObj->urban_body_code . " and f.scheme_id = " . $scheme_id . " and  b.legacy_validation = 0 and f.edited_status in (0,1,2)  and b.ben_status=1 AND f.failed_type IN(2) AND f.is_minor_mismatch = 1 AND b.is_eligible = true AND b.acc_validated = 4";
                if (!empty($request->munc) && empty($request->gp_ward)) {
                    // $old_query .= " AND b.block_ulb_code=" . $request->filter_1 . " ";
                    // $completequery .=" AND block_ulb_code=".$request->filter_1." ";
                    $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                    edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                     JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                     LEFT JOIN 
                         (
                           select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                             union all
                           select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                          ) bu ON bu.block_ulb_code=b.block_ulb_code  
                    LEFT JOIN 
                          (
                            select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                             union all
                            select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                            ) gw ON gw.gp_ward_code=b.gp_ward_code 
                             WHERE  b.dup_bank = 0 and f.if_previous_approve=0  and b.legacy_validation = 0 and f.local_body_code=" . $dutyObj->urban_body_code . " and  f.edited_status in (0,1,2) and f.scheme_id = " . $scheme_id . " and b.ben_status=1 AND f.failed_type IN(2) AND f.is_minor_mismatch = 1  AND b.block_ulb_code=" . $request->munc . " AND b.is_eligible = true AND b.acc_validated = 4";
                } else if (!empty($request->munc) && !empty($request->gp_ward)) {
                    $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                    edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                     JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                     LEFT JOIN 
                         (
                           select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                             union all
                           select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                          ) bu ON bu.block_ulb_code=b.block_ulb_code  
                    LEFT JOIN 
                          (
                            select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                             union all
                            select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                            ) gw ON gw.gp_ward_code=b.gp_ward_code 
                              WHERE b.dup_bank = 0 and f.if_previous_approve=0 and b.legacy_validation = 0 and f.local_body_code=" . $dutyObj->urban_body_code . " and  f.edited_status in (0,1,2) and f.scheme_id = " . $scheme_id . " and b.ben_status=1 AND f.failed_type IN(2) AND f.is_minor_mismatch = 1  AND b.block_ulb_code=" . $request->munc . " AND b.gp_ward_code = " . $request->gp_ward . " AND b.is_eligible = true AND b.acc_validated = 4";
                }


                // if (!empty($request->pay_mode)) {
                //     $query .= " AND f.pmt_mode=" . $request->pay_mode . "";
                // }
                $query .= "  order by b.ben_id";
            }
            // $complete = DB::connection('pgsql_appread')->select($completequery);
            // dd($query);
            $data = DB::connection('pgsql_paywrite')->select($query);

            // print_r($data);die;
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('view', function ($data) {
                    if ($data->edited_status == 0) {
                        return '<a href="' . route('ApplicantDetailsNameView', ['id' => $data->ben_id, 'scheme_id' => $data->scheme_id]) . '"
                    class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit </a>';
                    } else if ($data->edited_status == 1) {
                        return '<span class="label label-primary"> Approval Pending </span>';
                    } else if ($data->edited_status == 2) {
                        return '<span class="label label-success"> Approved </span>';
                    }
                })
                ->addColumn('id', function ($data) {
                    return $data->ben_id;
                })
                ->addColumn('name', function ($data) {
                    return $data->ben_name;
                })
                ->addColumn('block_ulb_name', function ($data) {
                    return $data->block_ulb_name;
                })
                ->addColumn('gp_ward_name', function ($data) {
                    return $data->gp_ward_name ?? Null;
                })

                ->addColumn('bank_code', function ($data) {
                    return $data->last_accno;
                })
                ->addColumn('bank_ifsc', function ($data) {
                    return $data->last_ifsc;
                })

                ->addColumn('status', function ($data) {
                    return '<span class="label label-success">Matching score ' . ($data->matching_score ?? 'N/A') . '</span>';
                })

                // ->with('completed', $complete)
                ->rawColumns(['id', 'name', 'block_ulb_name', 'gp_ward_name', 'bank_code', 'bank_ifsc', 'status', 'view'])
                ->make(true);
        }

    }

    public function editApplicantDetailsName(Request $request)
    {
        // dd($request->all()); 

        try {
            $is_verifier = AuthChecker::VerifierPermission();
            $user_id = AuthChecker::getUserId();

            $user_id = AuthChecker::getUserId();
            $scheme_id = $request->scheme_id;
            $ben_id = $request->id;
            if (!ctype_digit($scheme_id)) {
                return redirect("/")->with('error', 'Scheme Not Valid');
            }
            if (empty($ben_id)) {
                return redirect("/")->with('danger', 'Applicant ID Not Found');
            }
            if (!is_numeric($ben_id)) {
                return redirect("/")->with('danger', 'Applicant ID Not Valid');
            }
            $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
            if (empty($scheme_obj)) {
                return redirect("/")->with('danger', 'Scheme Not Found');
            }
            $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
            if (empty($duty_obj)) {
                return redirect("/")->with('danger', 'Not Allowed.');
            }
            $district_code = $duty_obj->district_code;
            if (AuthChecker::VerifierPermission()) {
                $query = DB::connection('pgsql')->table('pension.beneficiaries')
                    ->where('created_by_dist_code', $district_code)
                    ->where('id', $ben_id)
                    ->where('scheme_id', $scheme_id);
                // ->where('is_clean', 2);
            } else {
                return redirect("/")->with('danger', 'Not Allowed..');
            }
            $row = $query->first();
            if (empty($row)) {
                return redirect("/")->with('danger', 'Not Allowed...');
            }
            // Get District Name
            if ($row->dist_code != "") {
                $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
                $district_name = $district->district_name;
            }
            // Get Bank Name
            $block_name = "";
            if ($row->block_ulb_code != "") {
                if ($row->rural_urban_id == 1) {
                    $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
                    if (!empty($block)) {
                        $block_name = $block->urban_body_name;
                    }
                } else {
                    if (!empty($row->block_ulb_code)) {
                        $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
                        if (!empty($block)) {
                            $block_name = $block->block_name;
                        } else {
                            $block_name = '';
                        }
                    } else {
                        $block_name = '';
                    }
                }
            }
            $row->block_name = $block_name;
            // Get GP Name
            $gp_name = "";
            if ($row->gp_ward_code != "") {
                if ($row->rural_urban_id == 1) {
                    $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
                    if (!empty($gp_ward)) {
                        $gp_name = $gp_ward->urban_body_ward_name;
                    }
                } else {
                    $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
                    if (!empty($gp)) {
                        $gp_name = $gp->gram_panchyat_name;
                    }
                }
            }
            $row->gp_name = $gp_name;
            // dd($row);



            $PaymentErrorType = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->first();
            $BenFailedDetails = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->orderBy('id', 'desc')->first();
            // var_dump($PaymentErrorType);
            $enable_validation = array();
            $enableArr = [];
            if ($row->dup_bank == 1) {
                $enable = 'Bank';
                array_push($enable_validation, $enable);
            }
            if ($row->dup_aadhar == 1 || $row->no_aadhar == 1) {
                $enable = 'aadhar';
                array_push($enable_validation, $enable);
            }
            if ($row->dup_mobile == 1 || $row->no_mobile == 1) {
                $enable = 'Mobile';
                array_push($enable_validation, $enable);
            }
            $op_type = 1;
            $type = 1;



            $field_arrays_payment = array();



            $invalid_status = '';


            $av_name_response = null;

            if ($row->is_bank_failed == 2 && $BenFailedDetails->failed_type == 2) {
                $response = DB::connection('pgsql_paywrite')
                    ->table('payment.failed_payment_details')
                    ->where('ben_id', $ben_id)
                    ->where('edited_status', 0)
                    ->select('av_name_response')
                    ->orderBy('id', 'desc')
                    ->first();

                // Check if response exists and assign the value
                if ($response) {
                    $av_name_response = $response->av_name_response;
                }
            }

            return view(
                'NameValidation_90_100/ben_name_update_verifier',
                [
                    'scheme_id' => $scheme_id,
                    'row' => $row,
                    'district_name' => $district_name,
                    'block_name' => $block_name,
                    'gp_name' => $gp_name,
                    'district_code' => $district_code,
                    'enable_validation' => $enable_validation,
                    'invalid_status' => $invalid_status,
                    'av_name_response' => $av_name_response,
                    'is_verifier' => $is_verifier,
                    'type' => $type,
                    'op_type' => $op_type,
                    'field_arrays_payment' => $field_arrays_payment,

                ]
            );
        } catch (\Exception $e) {
            // dd($e);
            //throw $th;
        }
    }

    public function updateApplicantDetailsName(Request $request)
    {
        //   dd($request->all());
        $auth = AuthChecker::VerifierPermission();
        if ($auth) {
            try {
                $user_id = AuthChecker::getUserId();
                if (empty($request->scheme_id)) {
                    return redirect()->route('ApplicantDetailsNameView')->with('error', 'Scheme is Unavaliable');
                }
                $scheme_id = $request->scheme_id;
                $ben_id = $request->id;
                if (empty($request->id)) {
                    return redirect()->route('ApplicantDetailsNameView')->with('error', 'Applicant is Unavaliable');
                }

                $old_bank_ifsc = $request->old_bank_ifsc;
                // dd($request->new_bank_code);
                $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
                if (empty($duty_obj)) {
                    return redirect("/")->with('danger', 'Not Allowed');
                }
                $district_code = $duty_obj->district_code;
                $mapping_level = $duty_obj->mapping_level;
                if ($duty_obj->mapping_level == "Block") {
                    $urban_body_code = $duty_obj->taluka_code;
                }
                if ($duty_obj->mapping_level == "Subdiv") {
                    $urban_body_code = $duty_obj->urban_body_code;
                }

                $row = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where(
                    'scheme_id',
                    $scheme_id
                )->where('next_level_role_id', 0)->first();
                $paymentErrorType = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where(
                    'ben_id',
                    $ben_id
                )->where('scheme_id', $scheme_id)->first();
                $Benfailed_model = DB::connection('pgsql_paywrite')
                    ->table('payment.failed_payment_details')
                    ->where('ben_id', $row->id)
                    ->where('scheme_id', $scheme_id)->orderBy('id', 'desc')->first();
                $rules = [];
                $attributes = [];
                if (in_array($row->is_bank_failed, [2])) {
                    $bankRules = [
                        'process_type' => 'required',

                    ];
                    $bankAttributes = [
                        'process_type' => 'Process Type',
                    ];
                    $rules = array_merge($rules, $bankRules);
                    $attributes = array_merge($attributes, $bankAttributes);
                }
                $messages = [
                    'required' => 'The :attribute field is required.',
                    'max' => 'Total :max characters allowed for :attribute.',
                    'digits' => 'The :attribute must be exactly :digits digits.',
                ];
                // dd($updateArray);
                $validator = Validator::make($request->all(), $rules, $messages, $attributes);
                // dd($validator);
                if (!$validator->passes()) {
                    // dd($validator->errors()->all());
                    return redirect()->route('ApplicantDetailsNameView', ['id' => $ben_id, 'scheme_id' => $scheme_id])
                        ->withErrors($validator->errors()->all())
                        ->withInput();

                } else {
                    if (AuthChecker::VerifierPermission()) {

                        $old_values = [];
                        $new_values = [];

                        $benEntry_model = BenEntry::find($ben_id);
                        $benEntry_model->action_by = $user_id;
                        $benEntry_model->action_ip_address = request()->ip();
                        $benEntry_model->action_type = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();


                        $ben_failed_payment_details_model = BenFailedPaymentDetails::where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->where('edited_status', 0)->whereIn('failed_type', [2])->orderBy('id', 'desc')->first();
                        if ($row->is_bank_failed == 2 && $ben_failed_payment_details_model->failed_type == 2) {

                            $newPaymentDetails = [];
                            $getNpciBankCode = BankDetails::where('ifsc', $request->bank_ifsc_code)->first();
                            // $insertUpdateBenDetails = ['next_level_role_id' => 5];
                            $newPaymentDetails['new_bank_name'] = trim($request->new_bank_name);
                            $newPaymentDetails['new_bank_branch'] = trim($request->new_bank_branch);
                            $newPaymentDetails['new_bank_ifsc'] = $request->bank_ifsc_code;
                            $newPaymentDetails['new_bank_code'] = trim($request->new_bank_code);
                            $newPaymentDetails['npci_bank_code'] = $getNpciBankCode->bank_code;
                            $newPaymentDetails['ben_name'] = trim($row->ben_fname . ' ' . ($row->ben_mname) . ' ' . $row->ben_lname);
                            $newPaymentDetails['av_name_response'] = $Benfailed_model->av_name_response;
                            $newPaymentDetails['process_type'] = $request->process_type;

                            // $ben_payment_details_model->new_bank_name = trim($request->new_bank_name);

                            $ben_failed_payment_details_model->updated_details = json_encode($newPaymentDetails);
                            $ben_failed_payment_details_model->edited_status = 1;
                            $ben_failed_payment_details_model->updated_at = date('Y-m-d H:i:s');

                        } else {
                            return redirect()->route('ApplicantDetailsNameView', ['id' => $ben_id, 'scheme_id' => $scheme_id])
                                ->with('error', 'Beneficiary is Not Present');
                        }

                        $is_bank_upload = 0;
                        DB::connection('pgsql')->beginTransaction();
                        DB::connection('pgsql_paywrite')->beginTransaction();

                        if ($request->process_type == 1) {
                            $updatecode = DB::table('m_update_code')->where('id', 18)->first();
                        } else if ($request->process_type == 3) {
                            $updatecode = DB::table('m_update_code')->where('id', 20)->first();
                        }
                        if ($row->is_bank_failed == 2) {

                            $updateBenDetailsAction = 0;
                            $accept_reject_info_model = new AcceptRejectInfo;
                            $accept_reject_info_model->scheme_id = $scheme_id;
                            $accept_reject_info_model->created_by_dist_code = $district_code;
                            $accept_reject_info_model->created_by_local_body_code = $urban_body_code;
                            $accept_reject_info_model->created_at = date('Y-m-d H:i:s');
                            $accept_reject_info_model->updated_at = date('Y-m-d H:i:s');
                            $accept_reject_info_model->op_type = $updatecode->code;
                            $accept_reject_info_model->ip_address = request()->ip();
                            $accept_reject_info_model->user_id = $user_id;
                            $accept_reject_info_model->application_id = $ben_id;
                            $accept_reject_info_model->old_data = json_encode($old_values);
                            $accept_reject_info_model->new_data = json_encode($new_values);
                            $accept_reject_info_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
                            $accept_reject_info_model->remarks = $updatecode->description;
                            $updateBenDetailsAction = $accept_reject_info_model->save();

                            $benNameLogArray = [
                                'ben_id' => $row->id,
                                'scheme_id' => $scheme_id,
                                'name' => trim($row->ben_fname . ' ' . ($row->ben_mname) . ' ' . $row->ben_lname),
                                'response_name' => $Benfailed_model->av_name_response,
                                'process_type' => $request->process_type,
                                'created_at' => date('Y-m-d H:i:s'),
                                'matching_score' => $Benfailed_model->matching_score,
                                'next_level_name_failed_id' => 1,
                            ];

                            // Insert into DB
                            $benNameLogModel = new BenNameFailedlog;
                            $benNameLogModel->ben_id = $row->id;
                            $benNameLogModel->scheme_id = $scheme_id;
                            $benNameLogModel->name = trim($row->ben_fname . ' ' . ($row->ben_mname) . ' ' . $row->ben_lname);
                            $benNameLogModel->response_name = $Benfailed_model->av_name_response;
                            $benNameLogModel->process_type = $request->process_type;
                            $benNameLogModel->created_at = date('Y-m-d H:i:s');
                            $benNameLogModel->matching_score = $Benfailed_model->matching_score;
                            $benNameLogModel->next_level_name_failed_id = 1;
                            $benNameLogUpdate = $benNameLogModel->save();


                            if ($row->is_bank_failed == 2 && $Benfailed_model->failed_type == 2) {

                                $updateFailedPayment = $ben_failed_payment_details_model->save();

                            } else {
                                $updateFailedPayment = 1;
                            }
                            // dd($updateFailedPayment);
                            $updateBenTable = $benEntry_model->save();

                            // dd($updateBenTable);
                        } else {
                            DB::connection('pgsql')->rollback();
                            DB::connection('pgsql_encwrite')->rollback();
                            DB::connection('pgsql_paywrite')->rollback();

                            $return_text = "Something Went Wrong.";
                            return redirect()->route(' ApplicantDetailsNameView')->with('error', $return_text);
                        }
                    } else {
                        return redirect()->route('ApplicantDetailsNameView', ['id' => $ben_id, 'scheme_id' => $scheme_id])
                            ->with('error', 'User is not allowed');
                    }
                }


                // dump($updateBenDetailsAction);dump($updateBenTable);dump($updateBenPaymentTable);dump($updateFailedPayment);dump($benNameLogUpdate);die();

                if ($updateBenDetailsAction && $updateBenTable && $updateFailedPayment && $benNameLogUpdate) {
                    DB::connection('pgsql')->commit();
                    DB::connection('pgsql_paywrite')->commit();
                    $return_text = " Beneficiary (" . $row->id . ")  Information has been  forwarded to approver for approval. ";
                    // dd($return_text);
                    return redirect()->route('ben-name-90-update')->with('success', $return_text);
                } else {
                    DB::connection('pgsql')->rollback();
                    DB::connection('pgsql_paywrite')->rollback();
                    $return_text = "Beneficiary can't be forwarded.Something Went Wrong.";
                    return redirect()->route('ben-name-90-update')->with('error', $return_text);
                }
            } catch (\Exception $e) {
                // dd($e);
                return redirect()->route('ben-name-90-update')->with('error', 'Something Went Wrong');
            }

        } else {
            return redirect()->route('ben-name-90-update')->with('error', 'Not Allowded');
        }
    }

    public function Approverindex()
    {
        $auth = AuthChecker::ApproverPermission();
        if ($auth) {
            return view('NameValidation_90_100/Approver_landing');
        } else {
            return redirect()->route('/')->with('error', 'Not Allowded');
        }
    }

    public function indexApprover(Request $request)
    {
        $auth = AuthChecker::ApproverPermission();
        if ($auth) {
            $user_id = Auth::user()->id;
            $dutyObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
            $distCode = $dutyObj->district_code;
            $scheme = DB::connection('pgsql_mis')->select(
                'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where scheme_id in (2,10,11) and  user_id=' .
                $user_id .
                ' and is_active=1) order by scheme_name'
            );
            if (AuthChecker::ApproverPermission()) {
                $levels = [
                    2 => 'Rural',
                    1 => 'Urban'
                ];
            }
            return view('NameValidation_90_100/Approver_index', [
                'levels' => $levels,
                'schemes' => $scheme,
                'dist_code' => $distCode,
            ]);

        } else {
            return redirect()->route('/')->with('error', 'Not Allowded');
        }
    }


    public function getBenNameListApprover(Request $request)
    {
        $user_id = Auth::user()->id;
        $dutyObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
        $distCode = $dutyObj->district_code;
        $rural_urban = $request->filter_1;
        $local_body_code = $request->filter_2;
        $scheme_id = $request->scheme_id;

        $pay_validated = $request->failed_type_id;
        if ($request->ajax()) {
            // dd($request->all());
            if (AuthChecker::ApproverPermission() && !empty($scheme_id)) {
                $query = '';
                $query = "SELECT 
                b.created_by_local_body_code,b.rural_urban_id,  b.id,b.scheme_id,block_ulb_name,gp_ward_name, b.ben_fname, b.ben_mname, b.ben_lname, a.response_name, a.matching_score   
            FROM pension.ben_name_failed_log a  
            JOIN pension.beneficiaries b  
                ON a.ben_id = b.id  where a.next_level_name_failed_id= 1 and  b.created_by_dist_code = '" . $distCode . "' AND b.is_rejected = 0 AND b.scheme_id = " . $scheme_id . "";

                if (!empty($rural_urban)) {
                    $query .= " AND b.rural_urban_id =" . $rural_urban . "";
                }
                if (!empty($local_body_code)) {
                    $query .= " AND b.created_by_local_body_code = " . $local_body_code . "";
                }
                if (!empty($request->process_type)) {
                    $query .= " AND a.process_type = " . $request->process_type . "";
                }
                // dd($query);

                // dd($query);

                $data = DB::connection('pgsql_mis')->select($query);


            } else {
                $data = collect([]);
            }
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    $name = $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                    return $name;
                })

                ->addColumn('response_name', function ($data) {
                    $name = $data->response_name;
                    return $name;
                })
                ->addColumn('view', function ($data) {
                    $action = '<button class="btn btn-primary btn-xs ben_view_button" value="' . $data->id . '_' . $data->scheme_id . '"><i class="glyphicon glyphicon-edit"></i>View</button>';
                    return $action;
                })
                ->addColumn('check', function ($data) {
                    return '<input type="checkbox"  name="chkbx" class="all_checkbox"  onclick="controlCheckBox();" value="' . $data->id . '">';
                })
                ->addColumn('status', function ($data) {
                    return '<span class="label label-success">Matching score ' . ($data->matching_score ?? 'N/A') . '</span>';
                })

                ->rawColumns(['name', 'view', 'check', 'status'])
                ->make(true);
        }
    }

    public function NameModalView(Request $request)
    {
        // dd($request->all());
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
            $ben_details = BenEntry::where('scheme_id', $scheme_id)->where('id', $id)->first();
            $name_log = DB::table('pension.ben_name_failed_log')->where('scheme_id', $scheme_id)->where('ben_id', $id)->first(); //
            //   dd($name_log);
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
                    'ben_name' => $ben_details->ben_fname . ' ' . $ben_details->ben_mname . ' ' . $ben_details->ben_lname,
                    'id' => $ben_details->id,
                    'mobile_no' => $ben_details->mobile_no,
                    'aadhar_no' => $ben_details->aadhar_no,
                    'bank_code' => trim($ben_details->bank_code),
                    'bank_ifsc' => trim($ben_details->bank_ifsc),
                    'branch_name' => trim($ben_details->branch_name),
                    'bank_name' => trim($ben_details->bank_name),
                    'scheme_id' => $scheme_id,
                    'caste' => trim($ben_details->caste),
                    'name_response' => trim($name_log->response_name),
                    'matching_score' => trim($name_log->matching_score),
                    'process_type' => trim($name_log->process_type),
                    'is_lb_imported' => trim($ben_details->is_lb_imported),

                ];
                // dd($response);
                $response = array_merge($ben_arr, [
                    'status' => 2,
                ]);
            }
        } catch (\Exception $e) {
            // dd($e);
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


    public function approveNameApplicant(Request $request)
    {
        $auth = AuthChecker::ApproverPermission();
        if ($auth) {
            $response = [];
            $statusCode = 200;
            if (!$request->ajax()) {
                $statusCode = 400;
                $response = ['error' => 'Error occured in form submit.'];
                return response()->json($response, $statusCode);
            }
            // dd($request->all());
            $user_id = AuthChecker::getUserId();
            $is_bulk = $request->is_bulk;
            $applicant_id = $request->applicantId;
            // dd($applicant_id);
            $opreation_type = $request->opreation_type;
            $failed_type = $request->failed_type_id;
            $revart_remarks = $request->accept_reject_comments;
            $c_time = date('Y-m-d H:i:s');
            $dutyObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
            $distCode = $dutyObj->district_code;

            if ($is_bulk == 0) {
                $single_app_id = $request->single_app_id;
                $parts = explode('_', $single_app_id);
                $id = $parts[0];
                $scheme_id = $parts[1];
                if ($opreation_type == 'A') {
                    try {
                        $benDetails = BenEntry::where('scheme_id', $scheme_id)->where('id', $id)->first();
                        $nameLog = BenNameFailedlog::where('scheme_id', $scheme_id)->where('ben_id', $id)->first();
                        $ben_failed_model = BenFailedPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->orderBy('id', 'desc')->first();

                        //   dd($nameLog);


                        // dd($benDetails);
                        if ($benDetails == null) {
                            return $response = [
                                'status' => 1,
                                'msg' => 'No Beneficiary Found.',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        } else {

                            // dd($request->opreation_type);

                            // $updateArray = $this->getUpdateCode($benDetails, $request, $role = 2);
                            // $updateBenDetailsTable['']

                            DB::connection('pgsql')->beginTransaction();
                            DB::connection('pgsql_paywrite')->beginTransaction();
                            $benEntry_Model = BenEntry::where('scheme_id', $scheme_id)->where('id', $id)->first();
                            if ($nameLog->process_type == 1) {
                                $benEntry_Model->ben_fname = $nameLog->response_name;
                                $benEntry_Model->ben_mname = null;
                                $benEntry_Model->ben_lname = null;
                            }
                            if ($nameLog->process_type == 3) {
                                $benEntry_Model->is_rejected = 1;
                                $benEntry_Model->rejected_date = date('Y-m-d H:i:s');
                                $benEntry_Model->is_approved = 2;
                                $benEntry_Model->is_verified = 2;
                                $benEntry_Model->next_level_role_id = -1;

                            }

                            $is_ben_update = $benEntry_Model->save();
                            $updateArray = $this->getUpdateCode($benDetails, $request, $role = 2, $nameLog);
                            // dd($updateArray);
                            // $is_update_ben_details = DB::connection('pgsql')->table('public.ben_accept_reject_info')->insert($updateBenDetailsTable);

                            foreach ($updateArray as $updatecode) {
                                $accept_reject_info_model = new AcceptRejectInfo;
                                $accept_reject_info_model->scheme_id = $scheme_id;
                                $accept_reject_info_model->created_by_dist_code = $distCode;
                                $accept_reject_info_model->created_at = date('Y-m-d H:i:s');
                                $accept_reject_info_model->updated_at = date('Y-m-d H:i:s');
                                $accept_reject_info_model->op_type = $updatecode->code;
                                $accept_reject_info_model->ip_address = request()->ip();
                                $accept_reject_info_model->user_id = $user_id;
                                $accept_reject_info_model->application_id = $id;
                                $accept_reject_info_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
                                $accept_reject_info_model->remarks = $updatecode->description;
                                $accept_reject_info_model->reason = $revart_remarks;
                                $is_update_ben_details = $accept_reject_info_model->save();

                            }

                            $BenNameModel = BenNameFailedlog::where('ben_id', $id)
                                ->where('scheme_id', $scheme_id)->first();
                            $BenNameModel->next_level_name_failed_id = 2;
                            $is_name_log = $BenNameModel->save();
                            // ->update(['next_level_name_failed_id' => 2]);




                            if ($ben_failed_model->failed_type == 2 && $nameLog->process_type == 1) {

                                $ben_Payment_Details = BenPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->first();
                                if ($nameLog->process_type == 1) {
                                    $ben_Payment_Details->ben_name = $nameLog->response_name;
                                }
                                $ben_Payment_Details->acc_validated = 0;

                                $is_update_ben_payment = $ben_Payment_Details->save();
                            } else {
                                $is_update_ben_payment = 1;
                            }

                            if ($ben_failed_model->failed_type == 2 && $nameLog->process_type == 3) {
                                $ben_Payment_Details = BenPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->first();
                                $ben_Payment_Details->is_eligible = false;
                                $ben_Payment_Details->is_rejected = 1;
                                $ben_Payment_Details->rejected_at = date('Y-m-d H:i:s');
                                $is_update_ben_payment = $ben_Payment_Details->save();
                            } else {
                                $is_update_ben_payment = 1;
                            }


                            if ($ben_failed_model->failed_type == 2) {
                                $ben_failed_payment_details = BenFailedPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->orderBy('id', 'desc')->first();
                                $ben_failed_payment_details->edited_status = 2;
                                $is_update_failed_payment = $ben_failed_payment_details->save();
                            } else {
                                $is_update_failed_payment = 1;
                            }

                            // dump($is_ben_update);
                            // dump($is_update_ben_details);
                            // dump($is_update_failed_payment);
                            // dump($is_name_log);
                            // dump($is_update_ben_payment);
                            // die();

                            if ($is_ben_update && $is_update_ben_details && $is_update_ben_payment && $is_update_failed_payment && $is_name_log) {
                                DB::connection('pgsql')->commit();
                                DB::connection('pgsql_paywrite')->commit();
                                return $response = [
                                    'status' => 1,
                                    'msg' => 'Beneficiary Updated Successfully.',
                                    'type' => 'green',
                                    'icon' => 'fa fa-success',
                                    'title' => 'Success',
                                ];
                            } else {
                                DB::connection('pgsql')->rollback();
                                DB::connection('pgsql_paywrite')->rollback();

                                return $response = [
                                    'status' => 1,
                                    'msg' => 'Somethimg went wrong..',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        }
                    } catch (\Exception $e) {
                        // dd($e);
                        DB::connection('pgsql')->rollback();
                        DB::connection('pgsql_paywrite')->rollback();
                        $response = [
                            'exception' => true,
                            'exception_message' => $e->getMessage(),
                        ];
                        $statusCode = 400;
                    } finally {
                        // dd($response);
                        return response()->json($response, $statusCode);
                    }
                } elseif ($opreation_type == 'T') {
                    try {
                        $benDetails = BenEntry::where('scheme_id', $scheme_id)->where('id', $id)->first();
                        // dd($benDetails);
                        if ($benDetails == null) {
                            return $response = [
                                'status' => 1,
                                'msg' => 'No Beneficiary Found.',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        } else {


                            $updateArray = $this->getUpdateCode($benDetails, $request, $role = 2);

                            $benEntry_Model = BenEntry::where('scheme_id', $scheme_id)->where('id', $id)->first();


                            $ben_failed_payment_details = BenFailedPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->orderBy('id', 'desc')->first();
                            $ben_failed_payment_details->edited_status = 0;
                            $ben_failed_payment_details->updated_details = null;

                            $is_ben_failed_update = $ben_failed_payment_details->save();


                            $updateArray = $this->getUpdateCode($benDetails, $request, $role = 2);

                            foreach ($updateArray as $updatecode) {
                                $accept_reject_info_model = new AcceptRejectInfo;
                                $accept_reject_info_model->scheme_id = $scheme_id;
                                $accept_reject_info_model->created_by_dist_code = $distCode;
                                $accept_reject_info_model->created_at = date('Y-m-d H:i:s');
                                $accept_reject_info_model->updated_at = date('Y-m-d H:i:s');
                                $accept_reject_info_model->op_type = $updatecode->code;
                                $accept_reject_info_model->ip_address = request()->ip();
                                $accept_reject_info_model->user_id = $user_id;
                                $accept_reject_info_model->application_id = $id;
                                $accept_reject_info_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
                                $accept_reject_info_model->remarks = $updatecode->description;
                                $is_update_ben_details = $accept_reject_info_model->save();

                            }


                            $is_name_log = BenNameFailedlog::where('ben_id', $id)
                                ->where('scheme_id', $scheme_id)
                                ->delete();

                            DB::connection('pgsql')->beginTransaction();
                            DB::connection('pgsql_paywrite')->beginTransaction();

                            $is_update_ben_details = $accept_reject_info_model->save();


                            if ($is_update_ben_details && $is_ben_failed_update && $is_name_log) {
                                DB::connection('pgsql')->commit();
                                return $response = [
                                    'status' => 1,
                                    'msg' => 'Beneficiary Reverted Successfully.',
                                    'type' => 'green',
                                    'icon' => 'fa fa-success',
                                    'title' => 'Success',
                                ];
                            } else {
                                DB::connection('pgsql')->rollback();
                                return $response = [
                                    'status' => 1,
                                    'msg' => 'Somethimg went wrong..',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }

                        }
                    } catch (\Exception $e) {
                        DB::connection('pgsql')->rollback();
                        $response = [
                            'exception' => true,
                            'exception_message' => $e->getMessage(),
                        ];
                        $statusCode = 400;
                    } finally {
                        return response()->json($response, $statusCode);
                    }
                } else if ($opreation_type == 'O') {
                    try {
                        if (empty($request->process_type)) {
                            return $response = [
                                'status' => 1,
                                'msg' => 'Please select the Process Type..',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }
                        $process_type = (int) $request->process_type;
                        // var_dump($request->process_type);
                        $benDetails = BenEntry::where('scheme_id', $scheme_id)->where('id', $id)->first();
                        $nameLog = BenNameFailedlog::where('scheme_id', $scheme_id)->where('ben_id', $id)->first();
                        $ben_failed_model = BenFailedPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->orderBy('id', 'desc')->first();

                        if ($benDetails == null) {
                            return $response = [
                                'status' => 1,
                                'msg' => 'No Beneficiary Found.',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        } else {
                            DB::connection('pgsql')->beginTransaction();
                            DB::connection('pgsql_paywrite')->beginTransaction();
                            $benEntry_Model = BenEntry::where('scheme_id', $scheme_id)->where('id', $id)->first();
                            if ($process_type == 1) {
                                $benEntry_Model->ben_fname = $nameLog->response_name;
                                $benEntry_Model->ben_mname = null;
                                $benEntry_Model->ben_lname = null;
                                $benEntry_Model->action_by = $user_id;
                                $benEntry_Model->action_ip_address = $request->ip();
                                $benEntry_Model->action_type = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod() . '@' . $opreation_type;
                            }
                            if ($process_type == 3) {
                                // dump('inside Reject');
                                $benEntry_Model->is_rejected = 1;
                                $benEntry_Model->rejected_date = date('Y-m-d H:i:s');
                                $benEntry_Model->is_approved = 2;
                                $benEntry_Model->is_verified = 2;
                                $benEntry_Model->next_level_role_id = -1;
                                $benEntry_Model->rejected_by = Auth::user()->id;
                                $benEntry_Model->is_clean = 10;
                                $benEntry_Model->action_by = $user_id;
                                $benEntry_Model->action_ip_address = $request->ip();
                                $benEntry_Model->action_type = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod() . '@' . $opreation_type;
                            }

                            $is_ben_update = $benEntry_Model->save();
                            $updatecode = null;
                            // dump($opreation_type);
                            // dump($process_type);
                            if ($opreation_type == 'O') {
                                if ($process_type == 1) {
                                    $updatecode = [
                                        'code' => 'MMOverRuleAP',
                                        'description' => 'Over Rule - Minor Mismatch - From Approver',
                                    ];
                                } elseif ($process_type == 3) {
                                    $updatecode = [
                                        'code' => 'REOverRuleAP',
                                        'description' => 'Reject - Major Mismatch - From Approver',
                                    ];
                                }
                            } else {
                                $updatecode = null;

                            }
                            // dump($updatecode);

                            if (!empty($updatecode)) {
                                $accept_reject_info_model = new AcceptRejectInfo;
                                $accept_reject_info_model->scheme_id = $scheme_id;
                                $accept_reject_info_model->created_by_dist_code = $distCode;
                                $accept_reject_info_model->created_at = date('Y-m-d H:i:s');
                                $accept_reject_info_model->updated_at = date('Y-m-d H:i:s');
                                $accept_reject_info_model->op_type = $updatecode['code'];
                                $accept_reject_info_model->ip_address = request()->ip();
                                $accept_reject_info_model->user_id = $user_id;
                                $accept_reject_info_model->application_id = $id;
                                $accept_reject_info_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
                                $accept_reject_info_model->remarks = $updatecode['description'];
                                $accept_reject_info_model->reason = $revart_remarks;
                                $is_update_ben_details = $accept_reject_info_model->save();
                            }

                            $BenNameModel = BenNameFailedlog::where('ben_id', $id)
                                ->where('scheme_id', $scheme_id)->first();
                            $BenNameModel->next_level_name_failed_id = 2;
                            $is_name_log = $BenNameModel->save();

                            if ($ben_failed_model->failed_type == 2 && $process_type == 1) {
                                $ben_Payment_Details = BenPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->first();
                                $ben_Payment_Details->ben_name = $nameLog->response_name;
                                $ben_Payment_Details->acc_validated = 0;
                                $is_update_ben_payment = $ben_Payment_Details->save();
                            } else {
                                $is_update_ben_payment = 1;
                            }

                            if ($ben_failed_model->failed_type == 2 && $process_type == 3) {
                                $ben_Payment_Details = BenPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->first();
                                $ben_Payment_Details->is_eligible = false;
                                $ben_Payment_Details->is_rejected = 1;
                                $ben_Payment_Details->rejected_at = date('Y-m-d H:i:s');
                                $is_update_ben_payment = $ben_Payment_Details->save();
                            } else {
                                $is_update_ben_payment = 1;
                            }

                            if ($ben_failed_model->failed_type == 2) {
                                $ben_failed_payment_details = BenFailedPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->orderBy('id', 'desc')->first();
                                $ben_failed_payment_details->edited_status = 2;
                                $is_update_failed_payment = $ben_failed_payment_details->save();
                            } else {
                                $is_update_failed_payment = 1;
                            }

                            // dump($is_update_ben_details);
                            // dump($is_ben_update);
                            // dump($is_name_log);
                            // dump($is_update_failed_payment);
                            // dump($is_update_ben_payment);
                            // die();

                            if ($is_ben_update && $is_update_ben_details && $is_update_ben_payment && $is_update_failed_payment && $is_name_log) {
                                DB::connection('pgsql')->commit();
                                DB::connection('pgsql_paywrite')->commit();
                                return $response = [
                                    'status' => 1,
                                    'msg' => 'Beneficiary Over-ruled Successfully.',
                                    'type' => 'green',
                                    'icon' => 'fa fa-success',
                                    'title' => 'Success',
                                ];
                            } else {
                                DB::connection('pgsql')->rollback();
                                DB::connection('pgsql_paywrite')->rollback();

                                return $response = [
                                    'status' => 1,
                                    'msg' => 'Somethimg went wrong..',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }



                        }

                    } catch (\Exception $e) {
                        DB::connection('pgsql')->rollback();
                        $response = [
                            'exception' => true,
                            'exception_message' => $e->getMessage(),
                        ];
                        $statusCode = 400;
                    } finally {
                        return response()->json($response, $statusCode);

                    }

                    // dump($request->all());


                }
            }
            if ($is_bulk == 1) {
                if ($opreation_type == 'A') {
                    $bulk_id_arr = explode(',', $applicant_id);
                    $scheme_id = $request->scheme_id;
                    DB::beginTransaction();
                    DB::connection('pgsql_paywrite')->beginTransaction();
                    try {
                        $count = 0;
                        $i = 0;
                        foreach ($bulk_id_arr as $key => $value) {
                            $count++;
                            $ip_address = request()->ip();
                            $today = date("Y-m-d h:i:s");
                            $query = '';

                            // dd($value);
                            $ben_failed_model = BenFailedPaymentDetails::where('scheme_id', $scheme_id)->where('ben_id', $value)->orderBy('id', 'desc')->first();
                            $benDetails = BenEntry::where('scheme_id', $scheme_id)->where('id', $value)->first();
                            $nameLog = BenNameFailedlog::where('scheme_id', $scheme_id)->where('ben_id', $value)->first();
                            $av_name_response = $nameLog->response_name;

                            if ($benDetails == null) {
                                return $response = [
                                    'status' => 1,
                                    'msg' => 'No Beneficiary Found.',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            } else {

                                $updateArray = $this->getUpdateCode($benDetails, $request, $role = 2);


                                $benEntry_Model = BenEntry::where('scheme_id', $scheme_id)->where('id', $value)->first();
                                if ($nameLog->process_type == 1) {
                                    $benEntry_Model->ben_fname = $nameLog->response_name;
                                    $benEntry_Model->ben_mname = null;
                                    $benEntry_Model->ben_lname = null;
                                }
                                if ($nameLog->process_type == 3) {
                                    $benEntry_Model->is_rejected = 1;
                                    $benEntry_Model->rejected_date = date('Y-m-d H:i:s');
                                    $benEntry_Model->is_approved = 2;
                                    $benEntry_Model->is_verified = 2;
                                    $benEntry_Model->next_level_role_id = -1;
                                    $benEntry_Model->is_clean = 10;

                                }

                                $is_ben_update = $benEntry_Model->save();

                                foreach ($updateArray as $updatecode) {
                                    $accept_reject_info_model = new AcceptRejectInfo;
                                    $accept_reject_info_model->scheme_id = $scheme_id;
                                    $accept_reject_info_model->created_by_dist_code = $distCode;
                                    $accept_reject_info_model->created_at = date('Y-m-d H:i:s');
                                    $accept_reject_info_model->updated_at = date('Y-m-d H:i:s');
                                    $accept_reject_info_model->op_type = $updatecode->code;
                                    $accept_reject_info_model->ip_address = request()->ip();
                                    $accept_reject_info_model->user_id = $user_id;
                                    $accept_reject_info_model->application_id = $value;
                                    $accept_reject_info_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
                                    $accept_reject_info_model->remarks = $updatecode->description;
                                    $accept_reject_info_model->reason = $revart_remarks;
                                    // $accept_reject_info_model->next_level_name_failed_id = 2;
                                    $is_update_ben_details = $accept_reject_info_model->save();

                                }


                                $BenNameModel = BenNameFailedlog::where('ben_id', $value)
                                    ->where('scheme_id', $scheme_id)->first();
                                $BenNameModel->next_level_name_failed_id = 2;
                                $is_name_log = $BenNameModel->save();





                                if ($ben_failed_model->failed_type == 2 && $nameLog->process_type == 1) {

                                    $ben_Payment_Details = BenPaymentDetails::where('ben_id', $value)->where('scheme_id', $scheme_id)->first();
                                    if ($nameLog->process_type == 1) {
                                        $ben_Payment_Details->ben_name = $nameLog->response_name;
                                    }
                                    $is_update_ben_payment = $ben_Payment_Details->save();
                                } else {
                                    $is_update_ben_payment = 1;
                                }

                                if ($ben_failed_model->failed_type == 2 && $nameLog->process_type == 3) {
                                    $ben_Payment_Details = BenPaymentDetails::where('ben_id', $value)->where('scheme_id', $scheme_id)->first();
                                    $ben_Payment_Details->is_eligible = false;
                                    $ben_Payment_Details->is_rejected = 1;
                                    $ben_Payment_Details->rejected_at = date('Y-m-d H:i:s');
                                    $is_update_ben_payment = $ben_Payment_Details->save();
                                } else {
                                    $is_update_ben_payment = 1;
                                }


                                if ($ben_failed_model->failed_type == 2) {
                                    $ben_failed_payment_details = BenFailedPaymentDetails::where('ben_id', $value)->where('scheme_id', $scheme_id)->orderBy('id', 'desc')->first();
                                    if ($nameLog->process_type == 3)
                                        $ben_failed_payment_details->edited_status = -2;
                                    else
                                        $ben_failed_payment_details->edited_status = 2;
                                    $is_update_failed_payment = $ben_failed_payment_details->save();
                                } else {
                                    $is_update_failed_payment = 1;
                                }


                                // dump($is_update_ben_details); dump($is_ben_update); dump($is_update_ben_payment); dump($is_update_failed_payment); dump($is_name_log);
                                // die();
                                if ($is_update_ben_details && $is_ben_update && $is_update_ben_payment && $is_update_failed_payment && $is_name_log) {
                                    $i++;
                                }
                            }
                        }
                        if ($i == $count) {
                            DB::commit();
                            DB::connection('pgsql_paywrite')->commit();
                            $response = [
                                'status' => 1,
                                'msg' => 'Beneficiaries Approved Successfully',
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
                        //dd($e);
                        DB::rollback();
                        DB::connection('pgsql')->rollback();
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
            }
        }
    }




    private function getUpdateCode($row, $request, $role, $nameLog = null)
    {
        $updateArray = array();






        //Approver  Approve 
        if ($row->is_bank_failed == 2 && $request->opreation_type == 'A' && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 57)
                ->where('role_id', 2)
                ->select('code', 'description')
                ->first();
        }

        //Approver Revert
        if ($row->is_bank_failed == 2 && $request->opreation_type == 'T' && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 58)
                ->where('role_id', 2)
                ->select('code', 'description')
                ->first();
        }



        return $updateArray;

    }


    public function indexMisReport(Request $request)
    {
        // $ds_phase_list = DsPhase::get();
        // $base_date  = '2020-01-01';
        // $c_time = Carbon::now();
        // $c_date = $c_time->format("Y-m-d");
        $is_active = 0;
        $user_id = Auth::user()->id;
        $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where scheme_id in (2,10,11) and is_active=1 and user_id=" . $user_id . " )"));

        $roleArray = $request->session()->get('role');
        $designation_id = Auth::user()->designation_id;
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        if ($designation_id == 'Admin' || $designation_id == 'HOD' || $designation_id == 'HOP' || $designation_id == 'MisState' || $designation_id == 'Dashboard') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if (AuthChecker::ApproverPermission()) {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                //   if ($roleObj['scheme_id'] == $this->scheme_id) {
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
                //   }
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
        $districts = District::get();
        // dd($ds_phase_list);
        return view(
            'NameValidation_90_100.90_to_100_mis_report',
            [
                'districts' => $districts,
                'district_visible' => $district_visible,
                'district_code_fk' => $district_code_fk,
                'is_urban_visible' => $is_urban_visible,
                'rural_urban_fk' => $rural_urban_fk,
                'block_visible' => $block_visible,
                'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
                'municipality_visible' => $municipality_visible,
                'gp_ward_visible' => $gp_ward_visible,
                'is_urban_visible' => $is_urban_visible,
                'scheme' => $scheme,
                // 'base_date' => $base_date,
                // 'c_date' => $c_date,
                'gpList' => $gpList,
                'muncList' => $muncList,
                // 'ds_phase_list' => $ds_phase_list
            ]
        );
    }

    public function getMis90to100(Request $request)
    {

        // dd($request->all());
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        $gp_ward = $request->gp_ward;
        $scheme_id = (int) $request->scheme_id;
        $minor_mismatch = $request->minor_mismatch;
        if ($minor_mismatch == 1) {
            $userMsgTitle = '90% - 100%';
        }
        // dd($gp_ward);
        // $from_date = $request->from_date;
        // $to_date = $request->to_date;
        // $ds_phase_list = DsPhase::get()->pluck('phase_des', 'phase_code');
        // dd($ds_phase);
        // $base_date  = '2025-01-24';
        // $c_time = Carbon::now();
        // $c_date = $c_time->format("Y-m-d");
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
            'district' => 'nullable|integer',
            'urban_code' => 'nullable|integer',
            'block' => 'nullable|integer',
            'muncid' => 'nullable|integer',
            'gp_ward' => 'nullable|integer',
        ];
        $data = array();
        $column = "";
        $attributes = array();
        $messages = array();
        $attributes['district'] = 'District';
        $attributes['urban_code'] = 'Rural/ Urban';
        $attributes['block'] = 'Block/Sub Division';
        $attributes['muncid'] = 'Municipality';
        $attributes['gp_ward'] = 'GP/Ward';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $user_msg = "Minor Mismatch MIS Report(" . $userMsgTitle . ")";
            $title = $user_msg;
            //dd($title);

            $data = array();
            $return_status = 1;
            $return_msg = '';
            $heading_msg = '';
            $external = 0;
            $external_arr = array();
            $external_filter = array();
            $is_address = 0;
            if (!empty($gp_ward)) {
                if ($urban_code == 1) {
                    $is_address = 1;
                    $column = "Ward";
                    $heading_msg = $user_msg . ' of the Ward ' . $gp_ward_name;
                    $data = $this->getMonorMismatch90to100WardWise($scheme_id, $district, $block, $muncid, $gp_ward, $minor_mismatch);
                } else {
                    $is_address = 1;
                    $column = "GP";
                    $heading_msg = $user_msg . ' of the GP ' . $gp_ward_name;
                    $data = $this->getMonorMismatch90to100GpWise($scheme_id, $district, $block, NULL, $gp_ward, $minor_mismatch);
                }
            } else if (!empty($muncid)) {
                $is_address = 1;
                $column = "Ward";
                $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
                $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
                $data = $this->getMonorMismatch90to100WardWise($scheme_id, $district, $block, $muncid, NULL, $minor_mismatch);
            } else if (!empty($block)) {
                if ($urban_code == 1) {
                    $is_address = 1;
                    $column = "Municipality";
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getMonorMismatch90to100MuncWise($scheme_id, $district, $block, NULL, NULL, $minor_mismatch);
                } else if ($urban_code == 2) {
                    $is_address = 1;
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getMonorMismatch90to100GpWise($scheme_id, $district, $block, NULL, $gp_ward, $minor_mismatch);
                }
            } else {

                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getMonorMismatch90to100SubDivWise($scheme_id, $district, NULL, NULL, NULL, $minor_mismatch);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getMonorMismatch90to100BlockWise($scheme_id, $district, NULL, NULL, NULL, $minor_mismatch);
                        //   dump($data);
                        //   die();
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getMonorMismatch90to100BlockWise($scheme_id, $district, NULL, NULL, NULL, $minor_mismatch);
                        $data2 = $this->getMonorMismatch90to100SubDivWise($scheme_id, $district, NULL, NULL, NULL, $minor_mismatch);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getMonorMismatch90to100DistrictWise($scheme_id, $district, NULL, NULL, NULL, $minor_mismatch);
                    $external = 0;
                }
            }
            if ($is_address == 1) {
                $heading_msg = $heading_msg . "<span class='text-danger'> (According to Applicant’s Address)</span>";
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


    public function getMonorMismatch90to100BlockWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $minor_mismatch)
    {
        $whereCon = "dist_code =" . $district_code;
        $whereMain = " WHERE district_code =" . $district_code;
        $query = "select A.location_id AS created_by_local_body_code,
          A.location_name AS block_subdiv_name,
          A.created_by_dist_code,
          COALESCE(C.total, 0::bigint) AS total,
          COALESCE(C.yet_to_action, 0::bigint) AS yet_to_action,
          COALESCE(C.approval_pending, 0::bigint) AS approval_pending,
          COALESCE(C.approved, 0::bigint) AS approved,
          COALESCE(C.rejected, 0::bigint) AS rejected
      from(
      SELECT m_block.block_code AS location_id,
          m_block.block_name AS location_name,
          m_block.district_code AS created_by_dist_code
        FROM public.m_block  " . $whereMain . ") as A  
      LEFT JOIN
      (
          SELECT failed.local_body_code,
          COUNT(1)  AS total,
          COUNT(1) FILTER (WHERE edited_status = 0 AND ben.is_eligible = true) AS yet_to_action,
          COUNT(1) FILTER (WHERE edited_status = 1 AND ben.is_eligible = true) AS approval_pending,
          COUNT(1) FILTER (WHERE edited_status = 2 AND ben.is_eligible = true) AS approved,
          COUNT(1) FILTER (WHERE edited_status = 2  AND ben.is_eligible = false ) AS rejected
          from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id where failed.scheme_id=" . $scheme_id . "  AND ben.scheme_id=" . $scheme_id . "   AND failed.failed_type = 2 AND ben.legacy_validation = 0  AND failed.is_minor_mismatch = 1 AND failed.if_previous_approve = 0 AND ben.ben_status =1 AND  ben.acc_validated = 4
       group by failed.local_body_code) as C ON A.location_id=C.local_body_code
";
        $result = DB::connection('pgsql_paywrite')->select($query);
        return $result;
    }

    public function getMonorMismatch90to100SubDivWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $minor_mismatch)
    {
        $whereCon = "dist_code =" . $district_code;
        $whereMain = " WHERE district_code =" . $district_code;

        $query = "select A.location_id AS created_by_local_body_code,
          A.location_name AS block_subdiv_name,
          A.created_by_dist_code,
          COALESCE(C.total, 0::bigint) AS total,
          COALESCE(C.yet_to_action, 0::bigint) AS yet_to_action,
          COALESCE(C.approval_pending, 0::bigint) AS approval_pending,
          COALESCE(C.approved, 0::bigint) AS approved,
          COALESCE(C.rejected, 0::bigint) AS rejected
      from(
      SELECT m_sub_district.sub_district_code AS location_id,
              m_sub_district.sub_district_name AS location_name,
              m_sub_district.district_code AS created_by_dist_code
          FROM public.m_sub_district " . $whereMain . " ) as A  
      LEFT JOIN
      (
          SELECT failed.local_body_code,
          COUNT(1)  AS total,
          COUNT(1) FILTER (WHERE edited_status = 0 AND ben.is_eligible = true) AS yet_to_action,
          COUNT(1) FILTER (WHERE edited_status = 1 AND ben.is_eligible = true) AS approval_pending,
          COUNT(1) FILTER (WHERE edited_status = 2 AND ben.is_eligible = true) AS approved,
          COUNT(1) FILTER (WHERE edited_status = 2  AND ben.is_eligible = false ) AS rejected
          from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id where failed.scheme_id=" . $scheme_id . " AND ben.scheme_id=" . $scheme_id . "  AND failed.failed_type = 2 AND ben.legacy_validation = 0  AND failed.is_minor_mismatch = 1 AND failed.if_previous_approve = 0 AND ben.ben_status =1 and   ben.acc_validated = 4
       group by failed.local_body_code) as C ON A.location_id=C.local_body_code
";
        $result = DB::connection('pgsql_paywrite')->select($query);
        return $result;
    }

    public function getMonorMismatch90to100DistrictWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $minor_mismatch)
    {
        $whereCon = "dist_code =" . $district_code;
        $whereMain = " WHERE district_code =" . $district_code;


        $query = "select A.location_id AS created_by_local_body_code,
          A.location_name AS block_subdiv_name,
          A.created_by_dist_code,
          COALESCE(C.total, 0::bigint) AS total,
          COALESCE(C.yet_to_action, 0::bigint) AS yet_to_action,
          COALESCE(C.approval_pending, 0::bigint) AS approval_pending,
          COALESCE(C.approved, 0::bigint) AS approved,
          COALESCE(C.rejected, 0::bigint) AS rejected
      from(
      select m_district.district_code AS location_id,
                m_district.district_name AS location_name,
                m_district.district_code AS created_by_dist_code
            FROM public.m_district ) as A  
      LEFT JOIN
      (
          SELECT failed.dist_code,
          COUNT(1)  AS total,
          COUNT(1) FILTER (WHERE edited_status = 0 AND ben.is_eligible = true) AS yet_to_action,
          COUNT(1) FILTER (WHERE edited_status = 1 AND ben.is_eligible = true) AS approval_pending,
          COUNT(1) FILTER (WHERE edited_status = 2 AND ben.is_eligible = true) AS approved,
          COUNT(1) FILTER (WHERE edited_status = 2  AND ben.is_eligible = false ) AS rejected
          from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id where failed.scheme_id=" . $scheme_id . " AND ben.scheme_id=" . $scheme_id . "  AND failed.failed_type = 2 AND ben.legacy_validation = 0  AND failed.is_minor_mismatch = 1 AND failed.if_previous_approve = 0 AND ben.ben_status =1 AND   ben.acc_validated = 4
       group by failed.dist_code) as C ON A.location_id=C.dist_code";
        $result = DB::connection('pgsql_paywrite')->select($query);
        return $result;
    }


}