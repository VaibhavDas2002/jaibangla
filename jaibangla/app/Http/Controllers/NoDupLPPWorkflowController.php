<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use App\Configduty;
use App\UrbanBody;
use App\District;
use App\Taluka;
use App\Ward;
use App\SchemeGenSetting;
use App\GP;
use App\Scheme;
use App\Helpers\AuthChecker;
use App\AcceptRejectInfo;
use App\BenEntry;
use App\BenFailedPaymentDetails;
use App\BenRejectRequest;
use App\BenDupBankCodePayemntDetails;
use App\BankDetails;
use App\Helpers\DataInsert;
use App\Helpers\DupCheck;
use App\BenPaymentDetails;
// use 


class NoDupLPPWorkflowController extends Controller
{
    private $aadhar_doc; // For Aadhar Document
    private $bank_doc; // For Bank Document
    private $disability_doc; // For Disability Document
    private $caste_doc; // For caste Document
    private $epic_doc; // For EPIC Document
    private $ration_doc; // For Ration Document
    private $husband_doc; // For Husband Death Certificate Document
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

    public function index(Request $request)
    {
        $auth = AuthChecker::ApproverPermission();
        if ($auth) {

            $user_id = AuthChecker::getUserId();
            $dutyObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
            $distCode = $dutyObj->district_code;
            $incomplete_types = DB::table('public.m_incomplete_type')->where('is_active', true)->get();
            $scheme = DB::connection('pgsql_mis')->select(
                'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
                $user_id .
                ' and is_active=1) and id  in (8,9) order by scheme_name'
            );
            if (AuthChecker::ApproverPermission()) {
                $levels = [
                    2 => 'Rural',
                    1 => 'Urban'
                ];
            }
            $incomplete_types = DB::table('public.m_incomplete_type')->where('is_active', true)->get();
            return view('No-Dup-Update-lpp/linelisting_approver', [
                'levels' => $levels,
                'schemes' => $scheme,
                'dist_code' => $distCode,
                'incomplete_types' => $incomplete_types,
            ]);
        }
    }

    public function getNoDupLPPList(Request $request)
    {
        // dd($request->all());
        $user_id = AuthChecker::getUserId();
        $dutyObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
        $distCode = $dutyObj->district_code;
        $rural_urban = $request->filter_1;
        $local_body_code = $request->filter_2;
        $scheme_id = $request->scheme_id;
        $filter_type = $request->filter_type;
        // $failed_type_id = $request->failed_type_id;
        $pay_validated = $request->failed_type_id;
        if ($request->ajax()) {
            // dd($request->all());
            if (AuthChecker::ApproverPermission() && !empty($scheme_id)) {
                $query = '';
                $query = "SELECT * FROM pension.beneficiaries WHERE created_by_dist_code = '" . $distCode . "' AND  next_level_clean_id = 0 AND is_rejected = 0 AND is_clean in (1,2) AND scheme_id = " . $scheme_id . "";
                if ($filter_type == 0) {
                    $query .= "AND is_incomplete = 1 ";
                } else if ($filter_type == 1) {
                    $query .= "AND dup_aadhar = 1 ";
                } else if ($filter_type == 2) {
                    $query .= "AND no_aadhar = 1 ";
                } else if ($filter_type == 3) {
                    $query .= "AND dup_bank = 1 ";
                } else if ($filter_type == 4) {
                    $query .= "AND dup_mobile = 1 ";
                } else if ($filter_type == 5) {
                    $query .= "AND no_mobile = 1 ";
                } else if ($filter_type == 6) {
                    $query .= "AND no_ration_card = 1 ";
                } else if ($filter_type == 8) {
                    $query .= "AND no_epic_voter = 1 ";
                } else if ($filter_type == 11) {
                    $query .= "AND is_bank_failed = 2 ";
                } else if ($filter_type == 12) {
                    $query .= "AND is_bank_failed = 3 ";
                }

                if (!empty($rural_urban)) {
                    $query .= " AND rural_urban_id =" . $rural_urban . "";
                }
                if (!empty($local_body_code)) {
                    $query .= " AND created_by_local_body_code = " . $local_body_code . "";
                }
                // dd($query);
                if ($filter_type == 10 || isset($pay_validated)) {
                    $query .= "AND is_bank_failed = 1 ";
                    // $query .= "AND id IN (";
                    if ($pay_validated == 3) {

                        $query .= "AND pay_validated = 3 ";
                    }
                    if ($pay_validated == 4) {

                        $query .= "AND pay_validated = 4 ";
                    }
                    if ($pay_validated == 5) {

                        $query .= "AND pay_validated = 5 ";

                    }
                    // $query .= ") ";
                }
                // dd($query);

                $data = DB::connection('pgsql_mis')->select($query);

                $data = collect($data)->map(function ($item) use ($scheme_id) {
                    $pay_validation = DB::connection('pgsql_paywrite')
                        ->table('payment.ben_payment_details')
                        ->where('ben_id', $item->id)
                        ->where('scheme_id', $scheme_id)
                        ->select('pay_validated')
                        ->first();

                    $item->pay_validated = $pay_validation->pay_validated ?? null;
                    return $item;
                });
            } else {
                $data = collect([]);
            }
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    $name = $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                    return $name;
                })
                ->addColumn('view', function ($data) {
                    return '<a href="' . route('editApplicantDetailsLPP', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit </a>';
                })
                ->addColumn('status', function ($data) {
                    $sl = 1;
                    // $sl++;
                    $status = '';
                    if ($data->is_incomplete == 1) {
                        $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Incomplete Data</b></span> <br>';
                    }
                    if ($data->dup_aadhar == 1) {
                        $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Duplicate Aadhar</b></span> <br>';
                    }
                    if ($data->dup_mobile == 1) {
                        $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Duplicate Mobile</b></span> <br>';
                    }
                    if ($data->dup_bank == 1) {
                        $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Duplicate Bank</b></span> <br>';
                    }
                    if ($data->no_aadhar == 1) {
                        $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. No Aadhar</b></span> <br>';
                    }
                    if ($data->no_mobile == 1) {
                        $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. No Mobile</b></span> <br>';
                    }

                    if ($data->is_bank_failed == 1) {
                        if ($data->pay_validated == 3) {
                            $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment Failure SBI</b></span>';
                        } elseif ($data->pay_validated == 4) {
                            $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment Failure RBI</b></span>';
                        } elseif ($data->pay_validated == 5) {
                            $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment Failure IFMS</b></span>';
                        } else {
                            $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment Failure </b></span>';
                        }
                    }
                    if ($data->is_bank_failed == 2) {
                        $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Name Validation Failure</b></span> <br>';
                    }
                    if ($data->is_bank_failed == 3) {
                        $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. A/c Validation Failure</b></span> <br>';
                    }

                    // $sl++;
                    return $status;
                })
                ->rawColumns(['name', 'view', 'status'])
                ->make(true);
        }
    }
    public function editApplicantDetailsLPP(Request $request)
    {
        try {
            $is_approver = AuthChecker::ApproverPermission();
            $user_id = AuthChecker::getUserId();
            // $doc_type_id_arr = array($this->aadhar_doc_type_id, $this->bank_doc_type_id);
            // $supporting = DocumentType::select('id', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->whereIn("id", $doc_type_id_arr)->get();
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
            if (AuthChecker::ApproverPermission()) {
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
            $getAadharDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 6)->count();
            $getBankDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 10)->count();
            $getDisabilityDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 4)->count();
            $getHusbandDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 105)->count();
            $getCasteDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 3)->count();
            $getEpicDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 7)->count();
            $getRationDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 5)->count();
            // dd($getBankDoc);

            $PaymentErrorType = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->first();
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
            // print_r($enable_validation[0] == 'Bank');die;
            $op_type = 1;
            $type = 1;

            $manabilk_visible = 0;
            $wcd_wp_visible = 0;
            $sc_visible = 0;
            $st_visible = 0;
            $farmer_visible = 0;

            $wcd_wp_visible = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where('scheme_id', 10)->where('is_incomplete', 1)->count();
            $manabilk_visible = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where('scheme_id', 2)->where('is_incomplete', 1)->count();
            $sc_visible = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where('scheme_id', 3)->where('is_incomplete', 1)->count();
            $st_visible = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where('scheme_id', 1)->where('is_incomplete', 1)->count();
            // $farmer_visible = DB::connection('pgsql')->table('pension.beneficiaires')->where('id', $ben_id)->where('scheme_id', 13)->where('process_code', 1)->count();

            $field_arrays = array();
            $field_arrays_payment = array();

            $query = "SELECT id, scheme_id, is_incomplete, dup_aadhar, no_aadhar, dup_bank, dup_mobile, no_mobile, is_bank_failed, no_ration_card, no_epic_voter
            FROM pension.beneficiaries 
            WHERE id = ? AND scheme_id = ?";

            // Execute the query with parameterized bindings
            $data = DB::connection('pgsql')->select($query, [$row->id, $row->scheme_id]);
            $data = $data[0];
            if ($data->dup_aadhar == 1) {
                array_push($field_arrays, 'Duplicate Aadhar');
            }
            if ($data->no_aadhar == 1) {
                array_push($field_arrays, 'No Aadhar');
            }
            if ($data->dup_bank == 1) {
                array_push($field_arrays, 'Duplicate Bank Account Number');
            }
            if ($data->dup_mobile == 1) {
                array_push($field_arrays, 'Duplicate Mobile Number');
            }
            if ($data->no_mobile == 1) {
                array_push($field_arrays, 'No Mobile Number');
            }
            if ($data->is_bank_failed == 1) {
                array_push($field_arrays, 'Bank Transaction Failed');
            }
            if ($data->is_bank_failed == 2) {
                array_push($field_arrays, 'Name Validation Failed');
            }
            if ($data->is_bank_failed == 3) {
                array_push($field_arrays, 'A/c Validation Failed');
            }
            if ($data->no_ration_card == 1) {
                array_push($field_arrays, 'No Ration Card');
            }
            if ($data->no_epic_voter == 1) {
                array_push($field_arrays, 'No Epic Voter Card');
            }

            if ($data->is_incomplete == 1 && ($data->scheme_id == 1 || $data->scheme_id == 3)) {
                // Adding fields to the array
                array_push($field_arrays, 'Caste Category');
                array_push($field_arrays, 'Caste Certificate Number');
                array_push($field_arrays, 'Caste Certificate Document');
            }
            if ($data->is_incomplete == 1 && ($data->scheme_id == 2)) {
                array_push($field_arrays, 'Disability Type');
                array_push($field_arrays, 'Percentage of Disability');
                array_push($field_arrays, 'Authority Name');
                array_push($field_arrays, 'Authority Designation');
                array_push($field_arrays, 'Disability Certificate from Appropriate Authority');
            }
            if ($data->is_incomplete == 1 && ($data->scheme_id == 11)) {
                array_push($field_arrays, 'Husband Death Document');
                array_push($field_arrays, 'Husband Name');
            }
            $failed_type_id = 0;
            // dd($row);
            // if($row->is_bank_failed == 2 || $row->is_bank_failed == 3)
            // {
            if ($PaymentErrorType) {
                $failed_type_id = $PaymentErrorType->pay_validated;
            } else {
                $failed_type_id = null;
            }

            $invalid_status = '';

            if ($row->is_bank_failed == 1 || $row->is_bank_failed == 2 || $row->is_bank_failed == 3) {
                $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw('payment.failed_payment_details as f'))
                    ->join('payment.ben_payment_details as ben', 'f.ben_id', '=', 'ben.ben_id')
                    ->where('f.failed_type', $failed_type_id)->where('f.edited_status', 0)->where('ben.is_eligible', true)
                    ->where('ben.ben_status', 1)->where('f.scheme_id', $scheme_id)
                    ->where('f.ben_id', $row->id)
                    ->first();
                if ($failed_type_id == 4 || $failed_type_id == 5) {
                    $invalid_status = $ben_details->remarks;
                } elseif ($failed_type_id == 3) {
                    $remarks = DB::connection('pgsql_paywrite')->table('sbi.credit_transaction_code')->where(
                        'code',
                        trim($ben_details->status_code)
                    )->get(['description'])
                        ->first();
                    $invalid_status = $remarks->description;
                }
            }
            $av_name_response = null;

            if ($row->is_bank_failed == 2) {
                $response = DB::connection('pgsql_paywrite')
                    ->table('payment.failed_payment_details')
                    ->where('ben_id', $ben_id)
                    ->where('edited_status', 0)
                    ->select('av_name_response')
                    ->first();

                // Check if response exists and assign the value
                if ($response) {
                    $av_name_response = $response->av_name_response;
                }
            }

            $schemeSetting = SchemeGenSetting::where('scheme_id', $scheme_id)->first();
            $required_fields = DB::table('public.m_fields')
                ->where('is_active', 1)
                ->pluck('id')
                ->toArray();

            $name_opts = DB::table('public.m_scheme_gen_setting')
                ->where('scheme_id', $scheme_id)
                ->pluck('name_valid_opt');
            $name_opts = $name_opts->first();
            $array = explode(',', trim($name_opts, '{}'));
            $array = array_map('intval', $array);
            // dd($array); 
            $name_options = DB::table('public.m_name_valid_option')->wherein('id', $array)->get();
            // dd($name_options);

            $schemeSetting = SchemeGenSetting::where('scheme_id', $scheme_id)->first();
            $canBankupdate = 0;
            if ($schemeSetting->allow_bank_failed_update == 1 || $schemeSetting->allow_bank_name_update == 1 || $schemeSetting->allow_bank_ac_update == 1) {
                $canBankupdate = 1;
            }


            return view(
                'No-Dup-Update-lpp/ben_details_update_LPP',
                [
                    'scheme_id' => $scheme_id,
                    'row' => $row,
                    'district_name' => $district_name,
                    'block_name' => $block_name,
                    'gp_name' => $gp_name,
                    'district_code' => $district_code,
                    'enable_validation' => $enable_validation,
                    'getAadharDoc' => $getAadharDoc,
                    'getBankDoc' => $getBankDoc,
                    'PaymentErrorType' => $PaymentErrorType,
                    'getHusbandDoc' => $getHusbandDoc,
                    'getCasteDoc' => $getCasteDoc,
                    'getEpicDoc' => $getEpicDoc,
                    'getRationDoc' => $getRationDoc,
                    'invalid_status' => $invalid_status,
                    'name_options' => $name_options,
                    'av_name_response' => $av_name_response,
                    'canBankupdate' => $canBankupdate,
                    'is_approver' => $is_approver,
                    'type' => $type,
                    'op_type' => $op_type,
                    'getDisabilityDoc' => $getDisabilityDoc,
                    'field_arrays' => $field_arrays,
                    'field_arrays_payment' => $field_arrays_payment,
                    'required_fields' => $required_fields,
                    'schemeSetting' => $schemeSetting
                ]
            );
        } catch (\Exception $e) {
            dd($e);
            //throw $th;
        }
    }

    public function aadharDupCheck(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $aadhar_no = $request->aadhar_no;
            // $aadhar_no = "828017774867";
            $aadharSameVal = $request->aadharSameVal;
            if ($aadharSameVal == 1) {
                $checkAadharCount = DB::connection('pgsql')->table('pension.beneficiaries')->where(DB::raw("TRIM(aadhar_no)"), $aadhar_no)->whereIn('is_clean', [1, 2])->count();
                // dump($aadhar_no);
                // dump($checkAadharCount); die;
                if ($checkAadharCount > 1) {
                    return $response = [
                        'status' => 1,
                        'msg' => 'Aadhar number already exist. Please modify or reject the duplicate one.',
                        'type' => 'red',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ];
                } else {
                    return $response = [
                        'status' => 0,
                        'msg' => 'No Duplicate Found',
                        'type' => 'green',
                        'icon' => 'fa fa-success',
                        'title' => 'Success!!',
                    ];
                }
            }
        } catch (\Exception $e) {
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

    public function bankDupCheck(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $bank_code = $request->bank_code;
            $bank_ifsc = $request->bank_ifsc;
            $bankSameVal = $request->bankSameVal;
            if ($bankSameVal == 2) {
                $checkBankCount = DB::connection('pgsql')->table('pension.beneficiaries')->where(DB::raw("TRIM(bank_code)"), $bank_code)->whereIn('is_clean', [1, 2])->count();
                // dump($aadhar_no);
                // dump($checkBankCount); die;
                if ($checkBankCount > 1) {
                    return $response = [
                        'status' => 1,
                        'msg' => 'Bank A/c already exist!! Please modify or reject the duplicate one.',
                        'type' => 'red',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ];
                } else {
                    return $response = [
                        'status' => 0,
                        'msg' => 'No Duplicate Found',
                        'type' => 'green',
                        'icon' => 'fa fa-success',
                        'title' => 'Success!!',
                    ];
                }
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

    public function mobileDupCheck(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $mobile_no = $request->mobile_no;
            $mobileSameVal = $request->mobileSameVal;
            if ($mobileSameVal == 3) {
                $checkMobileCount = DB::connection('pgsql')->table('pension.beneficiaries')->where('mobile_no', $mobile_no)->whereIn('is_clean', [1, 2])->count();
                // dump($aadhar_no);
                // dump($checkMobileCount); die;
                if ($checkMobileCount > 1) {
                    return $response = [
                        'status' => 1,
                        'msg' => 'Mobile No. already exist!! Please modify or reject the duplicate one.',
                        'type' => 'red',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ];
                } else {
                    return $response = [
                        'status' => 0,
                        'msg' => 'No Duplicate Found',
                        'type' => 'green',
                        'icon' => 'fa fa-success',
                        'title' => 'Success!!',
                    ];
                }
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

    public function updateApplicantDetailsLPP(Request $request)
    {
        // dd($request->all());
        $auth = AuthChecker::ApproverPermission();
        if ($auth) {
            try {
                $user_id = Auth::user()->id;
                $scheme_id = (int) $request->scheme_id;
                $ben_id = (int) $request->id;
                $old_bank_ifsc = $request->old_bank_ifsc;
                // dd($request->new_bank_code);
                $isSame = !empty($request->new_bank_code) && ($request->new_bank_code != $request->old_bank_code) ? 0 : 1;


                $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
                if (empty($duty_obj)) {
                    return redirect("/")->with('danger', 'Not Allowed');
                }
                $district_code = $duty_obj->district_code;
                $mapping_level = $duty_obj->mapping_level;
                if ($duty_obj->mapping_level == "Block") {
                    $urban_body_code = $duty_obj->taluka_code;
                } else if ($duty_obj->mapping_level == "Subdiv") {
                    $urban_body_code = $duty_obj->urban_body_code;
                } else {
                    $urban_body_code = NULL;
                }



                $bankCodeRow = null;
                $row = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where(
                    'scheme_id',
                    $scheme_id
                )->first();
                $paymentErrorType = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where(
                    'ben_id',
                    $ben_id
                )->where('scheme_id', $scheme_id)->first();
                $BenFailedPayment = BenFailedPaymentDetails::where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->orderBy('created_at', 'desc')->first();
                $bankCodeRow = DB::connection('pgsql')->table('ifsc.bank_details')->where('ifsc', $old_bank_ifsc)->first();
                $bankDupRow = DB::connection('pgsql')->table('pension.ben_payment_details_bank_code_dup')->where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', -97)->count();
                // dd($bankDupRow);



                if (!empty($request->bank_ifsc_code)) {
                    $bank_ifsc_db = BankDetails::where('ifsc', $request->bank_ifsc_code)
                        ->where('is_active', 1)
                        ->first();

                    if (!$bank_ifsc_db) {
                        $return_text = "Bank Details not Available in Jai Bangla Portal";
                        return redirect()->route('editApplicantDetailsLPP', [
                            'id' => $ben_id,
                            'scheme_id' => $scheme_id
                        ])->with('error', $return_text);
                    }
                }




                $rules = [];
                $attributes = [];

                if ($row->dup_bank == 1 || in_array($row->is_bank_failed, [1, 3])) {
                    $bankRules = [
                        'old_bank_code' => 'required|max:20',
                        'old_bank_ifsc' => 'required|max:11',
                        'old_bank_name' => 'required',
                        'old_bank_branch' => 'required',
                        'new_bank_branch' => 'required',
                        'new_bank_name' => 'required',
                        'new_bank_code' => 'required|max:20',
                        'bank_ifsc_code' => 'required|max:11',
                    ];
                    $bankAttributes = [
                        'old_bank_code' => 'Old Bank Code',
                        'old_bank_ifsc' => 'Old Bank IFSC',
                        'new_bank_branch' => 'New Bank Branch',
                        'new_bank_name' => 'New Bank Name',
                        'new_bank_code' => 'New Bank Code',
                        'bank_ifsc_code' => 'Bank IFSC Code',
                    ];
                    $rules = array_merge($rules, $bankRules);
                    $attributes = array_merge($attributes, $bankAttributes);
                }

                if (in_array($row->is_bank_failed, [2])) {
                    $bankRules = [
                        'old_bank_code' => 'required|max:20',
                        'old_bank_ifsc' => 'required|max:11',
                        'process_type' => 'required',
                        'old_bank_name' => 'required',
                        'old_bank_branch' => 'required',

                    ];
                    $bankAttributes = [
                        'old_bank_code' => 'Old Bank Code',
                        'old_bank_ifsc' => 'Old Bank IFSC',
                        'process_type' => 'Process Type',
                        'old_bank_name' => 'Old Bank Name',
                        'old_bank_branch' => 'Old Bank Branch',
                    ];
                    $rules = array_merge($rules, $bankRules);
                    $attributes = array_merge($attributes, $bankAttributes);
                }



                if ($row->no_aadhar == 1) {
                    $aadharRules = [
                        'new_aadhar_no' => 'required|digits:12',
                        'old_aadhar' => 'nullable|digits:12',
                        'new_aadhar_doc' => 'required',
                    ];
                    $aadharAttributes = [
                        'old_aadhar' => 'Old Aadhar Number',
                        'new_aadhar_doc' => 'Aadhar Document',
                        'new_aadhar_no' => 'New Aadhar Number',
                    ];
                    $rules = array_merge($rules, $aadharRules);
                    $attributes = array_merge($attributes, $aadharAttributes);
                } elseif ($row->dup_aadhar == 1) {
                    $aadharRules = [
                        'new_aadhar_no' => 'required|digits:12',
                    ];
                    $aadharAttributes = [
                        'new_aadhar_no' => 'New Aadhar Number',
                    ];
                    $rules = array_merge($rules, $aadharRules);
                    $attributes = array_merge($attributes, $aadharAttributes);
                }

                if ($row->dup_mobile == 1 || in_array($row->is_bank_failed, [1])) {
                    $mobileRules = [
                        'old_mobile_no' => 'required',
                        'new_mobile_no' => 'required|digits:10',
                    ];
                    $mobileAttributes = [
                        'old_mobile_no' => 'Old Mobile Number',
                        'new_mobile_no' => 'New Mobile Number',
                    ];
                    $rules = array_merge($rules, $mobileRules);
                    $attributes = array_merge($attributes, $mobileAttributes);
                } elseif ($row->no_mobile == 1) {
                    $mobileRules = [
                        'new_mobile_no' => 'required|digits:10',
                    ];
                    $mobileAttributes = [
                        'new_mobile_no' => 'New Mobile Number',
                    ];
                    $rules = array_merge($rules, $mobileRules);
                    $attributes = array_merge($attributes, $mobileAttributes);
                }
                $messages = [
                    'required' => 'The :attribute field is required.',
                    'max' => 'Total :max characters allowed for :attribute.',
                    'digits' => 'The :attribute must be exactly :digits digits.',
                ];




                // Fetch document details using the helper function
                $bank_doc_count = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('document_type', $this->bank_doc)->count();
                $doc_aadhar_arr = DataInsert::getDocumentDetails($this->aadhar_doc);
                $doc_bank_arr = DataInsert::getDocumentDetails($this->bank_doc);
                $bank_doc_count = 1;

                // dd($bank_doc_count);

                if ($doc_aadhar_arr && $row->no_aadhar == 1) {
                    $required = 'required';
                    $rules['new_aadhar_doc'] = $required . '|mimes:' . $doc_aadhar_arr['doc_type'] . '|max:' .
                        $doc_aadhar_arr['doc_size_kb'] . ',';
                    $messages['new_aadhar_doc.max'] = "The file uploaded for " . $doc_aadhar_arr['doc_name'] . " size must be less than " .
                        $doc_aadhar_arr['doc_size_kb'] . " KB";
                    $messages['new_aadhar_doc.mimes'] = "The file uploaded for " . $doc_aadhar_arr['doc_name'] . " must be of type " .
                        $doc_aadhar_arr['doc_type'];
                    $messages['new_aadhar_doc.required'] = "Document for " . $doc_aadhar_arr['doc_name'] . " must be uploaded";
                }


                if ($doc_bank_arr && $bank_doc_count == 0) {
                    $required = 'required';
                    $rules['new_bank_doc'] = $required . '|mimes:' . $doc_bank_arr['doc_type'] . '|max:' . $doc_bank_arr['doc_size_kb'] .
                        ',';
                    $messages['new_bank_doc.max'] = "The file uploaded for " . $doc_bank_arr['doc_name'] . " size must be less than " .
                        $doc_bank_arr['doc_size_kb'] . " KB";
                    $messages['new_bank_doc.mimes'] = "The file uploaded for " . $doc_bank_arr['doc_name'] . " must be of type " .
                        $doc_bank_arr['doc_type'];
                    $messages['new_bank_doc.required'] = "Document for " . $doc_bank_arr['doc_name'] . " must be uploaded";
                }

                if ($doc_bank_arr && in_array($row->is_bank_failed, [1, 3])) {
                    $required = 'required';
                    $rules['new_bank_doc'] = $required . '|mimes:' . $doc_bank_arr['doc_type'] . '|max:' . $doc_bank_arr['doc_size_kb'] .
                        ',';
                    $messages['new_bank_doc.max'] = "The file uploaded for " . $doc_bank_arr['doc_name'] . " size must be less than " .
                        $doc_bank_arr['doc_size_kb'] . " KB";
                    $messages['new_bank_doc.mimes'] = "The file uploaded for " . $doc_bank_arr['doc_name'] . " must be of type " .
                        $doc_bank_arr['doc_type'];
                    $messages['new_bank_doc.required'] = "Document for " . $doc_bank_arr['doc_name'] . " must be uploaded";
                }
                $updateArray = $this->getUpdateCode($row, $request, 2);
                $validator = Validator::make($request->all(), $rules, $messages, $attributes);
                // dd($validator);
                if (!$validator->passes()) {
                    // dd($validator->errors()->all());
                    return redirect()->route('editApplicantDetailsLPP', ['id' => $ben_id, 'scheme_id' => $scheme_id])
                        ->withErrors($validator->errors()->all())
                        ->withInput();

                } else {

                    // dd($updateArray);

                    if (AuthChecker::ApproverPermission()) {


                        $old_values = [];
                        $new_values = [];



                        // Old values
                        $old_values['aadhar_no'] = $row->aadhar_no;
                        $old_values['mobile_no'] = $row->mobile_no;
                        $old_values['bank_code'] = trim($row->bank_code);
                        $old_values['bank_ifsc'] = trim($row->bank_ifsc);
                        $old_values['bank_name'] = trim($row->bank_name);
                        $old_values['branch_name'] = trim($row->branch_name);
                        $old_values['caste'] = trim($row->caste);
                        $old_values['caste_certificate_no'] = trim($row->caste_certificate_no);
                        $old_values['type_disability'] = trim($row->type_disability);
                        $old_values['percentage_disability'] = trim($row->percentage_disability);
                        $old_values['certifying_auth'] = trim($row->certifying_auth);
                        $old_values['disability_designation'] = trim($row->disability_designation);
                        $old_values['husband_fname'] = trim($row->husband_fname);
                        $old_values['husband_mname'] = trim($row->husband_mname);
                        $old_values['husband_lame'] = trim($row->husband_lname);
                        // New values
                        $new_values['aadhar_no'] = $request->new_aadhar_no;
                        $new_values['mobile_no'] = $request->new_mobile_no;
                        $new_values['bank_code'] = trim($request->new_bank_code);
                        $new_values['bank_ifsc'] = $request->bank_ifsc_code;
                        $new_values['bank_name'] = trim($request->new_bank_name);
                        $new_values['branch_name'] = trim($request->new_bank_branch);
                        $new_values['caste'] = trim($request->new_caste_category);
                        $new_values['caste_certificate_no'] = trim($request->new_caste_certificate_no);
                        $new_values['type_disability'] = trim($request->new_disablity_type);
                        $new_values['percentage_disability'] = trim($request->new_disablity_type_percentage);
                        $new_values['certifying_auth'] = trim($request->new_certifying_authority);
                        $new_values['disability_designation'] = trim($request->new_disability_designation);
                        $new_values['husband_fname'] = trim($request->new_husband_first_name);
                        $new_values['husband_mname'] = trim($request->new_husband_middle_name);
                        $new_values['husband_lname'] = trim($request->new_husband_last_name);




                        // dd($request->all());

                        $benEntry_model = BenEntry::find($ben_id);

                        $benEntry_model->no_aadhar = 0;
                        $benEntry_model->dup_aadhar = 0;
                        $benEntry_model->no_mobile = 0;
                        $benEntry_model->dup_mobile = 0;
                        $benEntry_model->is_incomplete = 0;
                        if ($benEntry_model->is_bank_failed = 1 && ($benEntry_model->pay_validated == 3 || $benEntry_model->pay_validated == 4 || $benEntry_model->pay_validated == 5)) {
                            $benEntry_model->is_bank_failed = 0;
                            $benEntry_model->pay_validated = 0;
                        } else {
                            $benEntry_model->is_bank_failed = 0;
                        }
                        $benEntry_model->next_level_clean_id = 1;
                        $benEntry_model->is_clean = 1;
                        $benEntry_model->action_by = $user_id;
                        $benEntry_model->action_ip_address = request()->ip();
                        $benEntry_model->action_type = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();

                        if (($row->no_aadhar == 1 || $row->dup_aadhar == 1) && !empty($request->new_aadhar_no) && ($request->old_aadhar_no != $request->new_aadhar_no)) {
                            $benEntry_model->aadhar_no = $request->new_aadhar_no;
                        }
                        if (($row->no_mobile == 1 || $row->dup_mobile == 1) && !empty($request->new_mobile_no) && ($request->old_mobile_no != $request->new_mobile_no)) {
                            $benEntry_model->mobile_no = $request->new_mobile_no;

                        }
                        if (($row->dup_bank == 1) && !empty($request->new_bank_code) && ($request->new_bank_code != $request->old_bank_code)) {
                            $benEntry_model->bank_code = $request->new_bank_code;
                        }
                        if (($row->is_bank_failed == 1) && !empty($request->new_bank_code) && ($request->new_bank_code != $request->old_bank_code)) {
                            $benEntry_model->bank_code = $request->new_bank_code;
                            $benEntry_model->bank_ifsc = $request->bank_ifsc_code;
                            if (!empty($request->new_mobile_no) && ($request->old_mobile_no != $request->new_mobile_no)) {
                                $benEntry_model->mobile_no = $request->new_mobile_no;
                            }
                        }

                        if ($row->is_bank_failed == 2 && !empty($request->process_type) && ($request->process_type == 2) && !empty($request->new_bank_code) && ($request->old_bank_code != $request->new_bank_code)) {

                            $benEntry_model->bank_code = $request->new_bank_code;
                            $benEntry_model->bank_ifsc = $request->bank_ifsc_code;
                            $benEntry_model->branch_name = $request->new_bank_branch;
                            $benEntry_model->bank_name = $request->new_bank_name;
                        }

                        if (($row->is_bank_failed == 3) && !empty($request->new_bank_code) && ($request->new_bank_code != $request->old_bank_code)) {
                            $benEntry_model->bank_code = $request->new_bank_code;
                            $benEntry_model->bank_ifsc = $request->bank_ifsc_code;
                            $benEntry_model->branch_name = $request->new_bank_branch;
                            $benEntry_model->bank_name = $request->new_bank_name;
                        }

                        if ($row->is_bank_failed == 2 && !empty($request->process_type) && ($request->process_type == 3)) {
                            $benEntry_model->next_level_role_id = -1;
                            // $benEntry_model->next_level_clean_id = 1;
                            $benEntry_model->is_rejected = 1;
                            $benEntry_model->is_verified = 2;
                            $benEntry_model->is_approved = 2;
                            $benEntry_model->is_clean = 10;
                            $benEntry_model->rejected_by = Auth::user()->id;
                            $benEntry_model->rejected_date = date('Y-m-d H:i:s');
                        }




                        $ben_failed_payment_details_model = BenFailedPaymentDetails::where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->where('edited_status', 0)->whereIn('failed_type', [1, 2, 3, 4, 5])->first();

                        if ($row->is_bank_failed == 1 || $row->is_bank_failed == 2 || $row->is_bank_failed == 3) {
                            if (
                                $paymentErrorType->pay_validated == 3 || $paymentErrorType->pay_validated == 4 || $paymentErrorType->pay_validated
                                == 5 || $paymentErrorType->acc_validated == 3 || $paymentErrorType->acc_validated == 4
                            ) {
                                $newPaymentDetails = [];
                                $getNpciBankCode = BankDetails::where('ifsc', $request->bank_ifsc_code)->first();
                                $newPaymentDetails['new_bank_name'] = trim($request->new_bank_name);
                                $newPaymentDetails['new_bank_branch'] = trim($request->new_bank_branch);
                                $newPaymentDetails['new_bank_ifsc'] = $request->bank_ifsc_code;
                                $newPaymentDetails['new_bank_code'] = trim($request->new_bank_code);
                                $newPaymentDetails['npci_bank_code'] = $getNpciBankCode->bank_code;
                                $ben_failed_payment_details_model->updated_details = json_encode($newPaymentDetails);
                                if (in_array($row->is_bank_failed, [2, 3])) {
                                    $ben_failed_payment_details_model->edited_status = 2;
                                }
                                $ben_failed_payment_details_model->updated_at = date('Y-m-d H:i:s');
                            }
                        }


                        // dd('ok');




                        $is_bank_upload = 0;
                        $is_aadhar_upload = 0;




                        DB::connection('pgsql')->beginTransaction();
                        DB::connection('pgsql_encwrite')->beginTransaction();
                        DB::connection('pgsql_paywrite')->beginTransaction();


                        /////////////////////////////////Document Upload START//////////////////////////////////////////////// 





                        // Upload Aadhar Card
                        if ($request->hasFile('new_aadhar_doc')) {
                            $doc_file = $request->file('new_aadhar_doc');
                            $img_data = file_get_contents($doc_file);
                            // echo $img_data;die;
                            $u_extension = $doc_file->getClientOriginalExtension();
                            $mime_type = $doc_file->getMimeType();
                            // dd($mime_type);
                            if (strtolower($mime_type) == 'image/jpeg') {
                                if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
                                    $extension = $u_extension;
                                } else {
                                    $errorMsg = "You are trying to upload an incorrect file for " . $doc_aadhar_arr['doc_name'];
                                    return $response = array(
                                        'status' => 0,
                                        'msg' => $errorMsg,
                                        'type' => 'red',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!'
                                    );
                                }
                            } else if (strtolower($mime_type) == 'image/png') {
                                $extension = 'png';
                            } else if (strtolower($mime_type) == 'image/gif') {
                                $extension = 'gif';
                            } else if (strtolower($mime_type) == 'application/pdf') {
                                $extension = 'pdf';
                            } else {
                                $errorMsg = "You are trying to upload an incorrect file for " . $doc_aadhar_arr['doc_name'];
                                return $response = array(
                                    'status' => 0,
                                    'msg' => $errorMsg,
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!'
                                );
                            }
                            if ($u_extension != $extension) {
                                $errorMsg = "You are trying to upload an incorrect file for " . $doc_aadhar_arr['doc_name'];
                                return $response = array(
                                    'status' => 0,
                                    'msg' => $errorMsg,
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!'
                                );
                            }

                            $base64 = base64_encode($img_data);
                            $ip_address = request()->ip();
                            $c_datetime = date('Y-m-d H:i:s', time());
                            $user_id = AuthChecker::getUserId();

                            // $is_aadhar_upload = DataInsert::insertBenAttachDocuments($ben_id, $scheme_id, $this->aadhar_doc, $base64, $mapping_level, $user_id, $ip_address, $extension, $mime_type, $beneficiaryRow->created_by_dist_code, $beneficiaryRow->created_by_local_body_code, $doc_aadhar_arr['doc_name'], $c_datetime);
                            $is_aadhar_upload = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                  in_beneficiary_id => " . $ben_id . ",
                  in_scheme_id => " . $scheme_id . ",
                  in_document_type => " . $this->aadhar_doc . ",
                  in_attched_document => '" . $base64 . "',
                  in_created_by_level => '" . $mapping_level . "',
                  in_created_by => " . Auth::user()->id . ",
                  in_ip_address => '" . request()->ip() . "',
                  in_document_extension => '" . $extension . "',
                  in_document_mime_type => '" . $mime_type . "',
                  in_created_by_dist_code => " . $row->created_by_dist_code . ",
                  in_created_by_local_body_code => " . $row->created_by_local_body_code . ",
                  in_doc_type_name => '" . $doc_aadhar_arr['doc_name'] . "',
                  in_datetime => '" . $c_datetime . "'
                  );"
                            );
                            $is_aadhar_upload = $is_aadhar_upload[0]->ben_docs_insert_archive;
                        }


                        // Upload Bank Document
                        if ($request->hasFile('new_bank_doc')) {
                            $doc_file = $request->file('new_bank_doc');
                            $img_data = file_get_contents($doc_file);
                            // echo $img_data;die;
                            $u_extension = $doc_file->getClientOriginalExtension();
                            $mime_type = $doc_file->getMimeType();

                            if (strtolower($mime_type) == 'image/jpeg') {
                                if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
                                    $extension = $u_extension;
                                } else {
                                    $errorMsg = "You are trying to upload an incorrect file for " . $doc_bank_arr['doc_name'];
                                    return $response = array(
                                        'status' => 0,
                                        'msg' => $errorMsg,
                                        'type' => 'red',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!'
                                    );
                                }
                            } else if (strtolower($mime_type) == 'image/png') {
                                $extension = 'png';
                            } else if (strtolower($mime_type) == 'image/gif') {
                                $extension = 'gif';
                            } else if (strtolower($mime_type) == 'application/pdf') {
                                $extension = 'pdf';
                            } else {
                                $errorMsg = "You are trying to upload an incorrect file for " . $doc_bank_arr['doc_name'];
                                return $response = array(
                                    'status' => 0,
                                    'msg' => $errorMsg,
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!'
                                );
                            }
                            if ($u_extension != $extension) {
                                $errorMsg = "You are trying to upload an incorrect file for " . $doc_bank_arr['doc_name'];
                                return $response = array(
                                    'status' => 0,
                                    'msg' => $errorMsg,
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!'
                                );
                            }

                            $base64 = base64_encode($img_data);
                            $ip_address = request()->ip();
                            $c_datetime = date('Y-m-d H:i:s', time());
                            $user_id = AuthChecker::getUserId();


                            // $is_bank_upload = DataInsert::insertBenAttachDocuments($ben_id, $scheme_id, $this->bank_doc, $base64, $mapping_level, $user_id, $ip_address, $extension, $mime_type, $beneficiaryRow->created_by_dist_code, $beneficiaryRow->created_by_local_body_code, $doc_bank_arr['doc_name'], $c_datetime);
                            $is_bank_upload = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                  in_beneficiary_id => " . $ben_id . ",
                  in_scheme_id => " . $scheme_id . ",
                  in_document_type => " . $this->bank_doc . ",
                  in_attched_document => '" . $base64 . "',
                  in_created_by_level => '" . $mapping_level . "',
                  in_created_by => " . Auth::user()->id . ",
                  in_ip_address => '" . request()->ip() . "',
                  in_document_extension => '" . $extension . "',
                  in_document_mime_type => '" . $mime_type . "',
                  in_created_by_dist_code => " . $row->created_by_dist_code . ",
                  in_created_by_local_body_code => " . $row->created_by_local_body_code . ",
                  in_doc_type_name => '" . $doc_bank_arr['doc_name'] . "',
                  in_datetime => '" . $c_datetime . "'
                  );"
                            );
                            $is_bank_upload = $is_bank_upload[0]->ben_docs_insert_archive;

                        }
                        // dd($request->all());
                        /////////////////////////////////Document Upload START////////////////////////////////////////////////
                        $benAadharCheck = 0;
                        $benMobileCheck = 0;
                        $benBankCheck = 0;
                        $benAadharCheckCross = 0;
                        $benMobileCheckCross = 0;
                        $benBankCheckCross = 0;


                        if ($row->no_aadhar == 1 || $row->dup_aadhar == 1 && ($request->old_aadhar != $request->new_aadhar)) {
                            $benAadharCheck = DupCheck::dupAadharCheckSame($scheme_id, $request->new_aadhar_no, $ben_id);
                            $benAadharCheckCross = DupCheck::dupAadharCheckCross($scheme_id, $request->new_aadhar_no, $ben_id);

                        }
                        if ($row->no_mobile == 1 || $row->dup_mobile == 1 && ($request->old_mobile_no != $request->new_mobile_no)) {
                            $benMobileCheck = DupCheck::dupMobileCheckSame($scheme_id, $request->new_mobile_no, $ben_id);
                            $benMobileCheckCross = DupCheck::dupMobileCheckCross($scheme_id, $request->new_mobile_no, $ben_id);

                        }
                        if ($row->dup_bank == 1 && !empty($request->new_bank_code) && $request->new_bank_code != $request->old_bank_code) {
                            $benBankCheck = DupCheck::dupBankCheckSame($scheme_id, $request->new_bank_code, $ben_id);
                            $benBankCheckCross = DupCheck::dupBankCheckCross($scheme_id, $request->new_bank_code, $ben_id);
                        }

                        if ($row->is_bank_failed == 1 && !empty($request->new_bank_code) && $request->new_bank_code != $request->old_bank_code) {
                            $benBankCheck = DupCheck::dupBankCheckSame($scheme_id, $request->new_bank_code, $ben_id);
                            $benBankCheckCross = DupCheck::dupBankCheckCross($scheme_id, $request->new_bank_code, $ben_id);
                        }


                        if ($benAadharCheck > 0) {
                            $errorMsg = "This Aadhar No: " . $request->new_aadhar . " is already exist.";
                            return redirect()->route('editApplicantDetailsLPP', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
                        } elseif ($benMobileCheck > 0) {
                            $errorMsg = "This Mobile No: " . $request->new_mobile_no . " is already exist.";
                            return redirect()->route('editApplicantDetailsLPP', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
                        } elseif ($benBankCheck > 0) {
                            $errorMsg = "This Bank Account: " . $request->bank_code . " is already exist.";
                            return redirect()->route('editApplicantDetailsLPP', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
                        } elseif ($benAadharCheckCross > 0) {
                            $errorMsg = "This Aadhar No: " . $request->new_aadhar . " is already exist on Cross Scheme.";
                            return redirect()->route('editApplicantDetailsLPP', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
                        } elseif ($benMobileCheckCross > 0) {
                            $errorMsg = "This Mobile No: " . $request->new_mobile_no . " is already exist on Cross Scheme.";
                            return redirect()->route('editApplicantDetailsLPP', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
                        } elseif ($benBankCheckCross > 0) {
                            $errorMsg = "This Bank Account: " . $request->bank_code . " is already exist on Cross Scheme.";
                            return redirect()->route('editApplicantDetailsLPP', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
                        }


                        if (
                            $benAadharCheck == 0 && $benMobileCheck == 0 && $benBankCheck == 0 && $benAadharCheckCross == 0 && $benMobileCheckCross == 0 && $benBankCheckCross == 0 && $benAadharCheckCross == 0 || ($is_aadhar_upload == 1 || $is_bank_upload == 1)
                        ) {
                            $updateBenDetailsAction = 0;

                            $i = 0;
                            foreach ($updateArray as $updatecode) {
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
                                $accept_reject_info_model->save();
                                $i++;
                            }
                            if ($i == count($updateArray)) {
                                $updateBenDetailsAction = 1;
                            } else {
                                $updateBenDetailsAction = 0;
                            }

                            $ben_payment_details_model = BenPaymentDetails::where('ben_id', $ben_id)
                                ->where('scheme_id', $scheme_id)
                                ->first();

                            if ($isSame == 0 && in_array($row->is_bank_failed, [2, 3])) {
                                $ben_payment_details_model->last_accno = trim($request->new_bank_code);
                                $ben_payment_details_model->last_ifsc = $request->bank_ifsc_code;
                                $ben_payment_details_model->npci_bank_code = $bankCodeRow->bank_code;
                                if ($row->is_bank_failed == 1) {
                                    $ben_payment_details_model->pay_validated = 1;
                                }
                                $updateBenPaymentTable = $ben_payment_details_model->save();
                            } else {
                                $updateBenPaymentTable = 1;
                            }

                            if ($row->is_bank_failed == 2 && !empty($request->process_type) && ($request->process_type == 3)) {
                                $ben_payment_details_model->dup_bank = 0;
                                $ben_payment_details_model->ben_status = 0;
                                $ben_payment_details_model->acc_validated = 0;
                                $ben_payment_details_model->rejected_at = date('Y-m-d H:i:s');
                                $ben_payment_details_model->rejected_by = Auth::user()->id;
                                $ben_payment_details_model->is_rejected = 1;
                                $ben_payment_details_model->is_eligible = false;
                                $updateBenPaymentTable = $ben_payment_details_model->save();
                            }


                            if ($ben_failed_payment_details_model) {
                                if ($row->dup_bank == 1 || (!empty($request->is_bank_failed) && in_array($request->is_bank_failed, [1, 2, 3]))) {
                                    if (
                                        $paymentErrorType->pay_validated == 3 || $paymentErrorType->pay_validated == 4 || $paymentErrorType->pay_validated
                                        == 5 || $paymentErrorType->acc_validated == 3 || $paymentErrorType->acc_validated == 4
                                    ) {
                                        $updateFailedPayment = $ben_failed_payment_details_model->save();
                                    }
                                } else {
                                    $updateFailedPayment = 1;
                                }
                            } else {
                                $updateFailedPayment = 1;
                            }


                            // dd('ok');


                            $updateBenTable = $benEntry_model->save();

                            //  dump($updateBenTable);



                            if ($bankDupRow > 0) {
                                $ben_dup_bank_code_details = BenDupBankCodePayemntDetails::where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', -97)->first();
                                if (!empty($request->process_type) && ($request->process_type == 3)) {
                                    // $ben_dup_bank_code_details->revert_remarks = $request->reject_remarks;
                                    // $ben_dup_bank_code_details->is_approved = 2;
                                    $ben_dup_bank_code_details->rejected_date = date('Y-m-d H:i:s');
                                    $ben_dup_bank_code_details->rejected_by = Auth::user()->id;
                                    $ben_dup_bank_code_details->next_level_role_id = -200;
                                } else {
                                    // dd($ben_dup_bank_code_details);
                                    $ben_dup_bank_code_details->bank_code = trim($request->new_bank_code);
                                    $ben_dup_bank_code_details->bank_name = trim($request->new_bank_name);
                                    $ben_dup_bank_code_details->branch_name = trim($request->new_bank_branch);
                                    $ben_dup_bank_code_details->bank_ifsc = $request->bank_ifsc_code;
                                    if ($isSame == 1) {
                                        $ben_dup_bank_code_details->next_level_role_id = 200;
                                    } else {
                                        $ben_dup_bank_code_details->next_level_role_id = 101;
                                    }
                                }
                                $updateBankDup = $ben_dup_bank_code_details->save();
                            } else {
                                $updateBankDup = 1;
                            }

                            $is_final_update = 0;

                            if (in_array($row->is_bank_failed, [1])) {
                                $failed_type_id = $row->pay_validated;
                                if (!empty($failed_type_id)) {
                                    $is_final_update = DB::connection('pgsql_paywrite')->select("Select payment.failed_update_bank(in_ben_id => ARRAY[" . $ben_id . "], in_scheme_id => " . $scheme_id . ", in_failed_type_id => " . $failed_type_id . ")");
                                    $is_final_update = $is_final_update[0]->failed_update_bank;
                                }
                            } else {
                                $is_final_update = 1;
                            }

                        } else {

                            DB::connection('pgsql')->rollback();
                            DB::connection('pgsql_encwrite')->rollback();
                            DB::connection('pgsql_paywrite')->rollback();
                            $return_text = "Something Went Wrong.";
                            return redirect()->route('editApplicantDetailsLPP', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $return_text);
                        }


                        if ($updateBenDetailsAction && $updateBenTable && $updateBenPaymentTable && $updateBankDup && $updateFailedPayment && $is_final_update) {

                            DB::connection('pgsql')->commit();
                            DB::connection('pgsql_encwrite')->commit();
                            DB::connection('pgsql_paywrite')->commit();
                            $return_text = "Beneficiary(" . $row->id . ") has successfully Updated and Approved.";
                            // dd($return_text);
                            return redirect()->route('no-Dup-Beneficiaries-LPP')->with('success', $return_text);
                        } else {
                            DB::connection('pgsql')->rollback();
                            DB::connection('pgsql_encwrite')->rollback();
                            DB::connection('pgsql_paywrite')->rollback();
                            $return_text = "Beneficiary can't be updated.Something Went Wrong.";
                            return redirect()->route('no-Dup-Beneficiaries-LPP')->with('error', $return_text);
                        }
                    }

                }
            } catch (\Exception $e) {
                // 
                // dd($e);
                return redirect()->route('no-Dup-Beneficiaries-LPP')->with('error', 'Something Went Wrong');
            }
        } else {
            return redirect()->route('no-Dup-Beneficiaries-LPP')->with('error', 'Not Allowded');
        }

    }


    public function rejectApplicantDetailsLPP(Request $request)
    {
        // dd('ok');
        // dd($request->all());
        // var_dump($request->all());

        $auth = Authchecker::ApproverPermission();
        if ($auth) {

            try {
                $user_id = Auth::user()->id;
                if (empty($request->scheme_id)) {
                    return redirect()->route('no-Dup-Beneficiaries-LPP')->with('error', 'Invalid Scheme');
                }

                if (empty($request->id)) {
                    return redirect()->route('no-Dup-Beneficiaries-LPP')->with('error', 'Invalid Beneficiary');
                }
                $ben_id = (int) $request->id;
                $scheme_id = (int) $request->scheme_id;
                $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();

                if (empty($duty_obj)) {
                    return redirect("/")->with('danger', 'Not Allowed');
                }
                $district_code = $duty_obj->district_code;
                $mapping_level = $duty_obj->mapping_level;
                if ($duty_obj->mapping_level == "Block") {
                    $urban_body_code = $duty_obj->taluka_code;
                } else if ($duty_obj->mapping_level == "Subdiv") {
                    $urban_body_code = $duty_obj->urban_body_code;
                } else {
                    $urban_body_code = NULL;
                }

                $row = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where(
                    'scheme_id',
                    $scheme_id
                )->first();
                $bankDupRow = DB::table('pension.ben_payment_details_bank_code_dup')->where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', -97)->count();

                $updateArray = $this->getUpdateCode($row, $request, 2);



                $benEntry_model = BenEntry::find($ben_id);


                $benEntry_model->next_level_clean_id = 1;
                $benEntry_model->next_level_role_id = -1;
                $benEntry_model->is_clean = 10;
                $benEntry_model->is_rejected = 1;
                $benEntry_model->is_verified = 2;
                $benEntry_model->is_approved = 2;
                $benEntry_model->rejected_by = Auth::user()->id;
                $benEntry_model->rejected_date = date('Y-m-d H:i:s');

                $benEntry_model->action_by = $user_id;
                $benEntry_model->action_ip_address = request()->ip();
                $benEntry_model->action_type = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();

                DB::connection('pgsql')->beginTransaction();
                DB::connection('pgsql_paywrite')->beginTransaction();

                $updateBenTable = $benEntry_model->save();

                $cnt = 0;
                foreach ($updateArray as $updatecode) {
                    $accept_reject_info_model = new AcceptRejectInfo;
                    $accept_reject_info_model->scheme_id = $scheme_id;
                    $accept_reject_info_model->reason = $request->reject_remarks;
                    $accept_reject_info_model->created_by_dist_code = $district_code;
                    $accept_reject_info_model->created_by_local_body_code = $urban_body_code;
                    $accept_reject_info_model->created_at = date('Y-m-d H:i:s');
                    $accept_reject_info_model->updated_at = date('Y-m-d H:i:s');
                    $accept_reject_info_model->op_type = $updatecode->code;
                    $accept_reject_info_model->ip_address = request()->ip();
                    $accept_reject_info_model->user_id = $user_id;
                    $accept_reject_info_model->application_id = $ben_id;
                    $accept_reject_info_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
                    $accept_reject_info_model->remarks = $updatecode->description;
                    $accept_reject_info_model->save();
                    $cnt++;
                }


                // dd($updateArray);
                if ($cnt == count($updateArray)) {
                    $updateBenDetailsAction = 1;
                } else {
                    $updateBenDetailsAction = 0;
                }



                $ben_failed_payment_details_model = BenFailedPaymentDetails::where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->where('edited_status', 0)->whereIn('failed_type', [1, 2, 3, 4, 5])->first();
                if ($ben_failed_payment_details_model) {
                    if ($row->is_bank_failed == 1 || $row->is_payment_failed == 2 || $row->is_payment_failed == 3) {
                        if ($ben_failed_payment_details_model) {
                            $ben_failed_payment_details_model->edited_status = 2;
                            $ben_failed_payment_details_model->updated_at = date('Y-m-d H:i:s');
                            $updateFailedPayment = $ben_failed_payment_details_model->save();

                        }
                    }
                    $updateFailedPayment = 1;
                } else {
                    $updateFailedPayment = 1;
                }




                $benPaymentModel = BenPaymentDetails::where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->first();

                $benPaymentModel->is_rejected = 1;
                $benPaymentModel->rejected_at = date('Y-m-d H:i:s');
                $benPaymentModel->is_eligible = false;

                $updateBenpayemnt = $benPaymentModel->save();

                $bankDupRow = DB::connection('pgsql')->table('pension.ben_payment_details_bank_code_dup')->where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', -97)->count();

                if ($bankDupRow > 0) {
                    $benDupbankModel = BenDupBankCodePayemntDetails::where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', -97)->first();
                    $benDupbankModel->rejected_date = date('Y-m-d H:i:s');
                    $benDupbankModel->rejected_by = Auth::user()->id;
                    $benDupbankModel->next_level_role_id = -200;
                    $updateBankDup = $benDupbankModel->save();
                } else {
                    $updateBankDup = 1;
                }


                if ($updateFailedPayment && $updateBenDetailsAction && $updateBenTable && $updateBenpayemnt && $updateBankDup) {

                    DB::connection('pgsql')->commit();
                    DB::connection('pgsql_paywrite')->commit();
                    $return_text = "Beneficiary(" . $row->id . ") has Rejected successfully ";
                    // dd($return_text);
                    return redirect()->route('no-Dup-Beneficiaries-LPP')->with('success', $return_text);

                } else {
                    DB::connection('pgsql')->rollBack();
                    DB::connection('pgsql_paywrite')->rollBack();
                    return redirect()->route('no-Dup-Beneficiaries-LPP')->with('error', 'Error: Failed to reject beneficiary');
                }


            } catch (\Exception $e) {
                // dd($e);
                DB::connection('pgsql')->rollBack();
                DB::connection('pgsql_paywrite')->rollBack();
                return redirect()->route('no-Dup-Beneficiaries-LPP')->with('error', 'Error: ' . $e->getMessage());
            }

        } else {
            return redirect()->route('no-Dup-Beneficiaries-LPP')->with('error', 'Not Allowded');

        }
    }



    private function getUpdateCode($row, $request, $role)
    {
        $updateArray = array();



        // Approver
        if ($row->no_aadhar == 1 && !empty($request->new_aadhar_no) && $request->new_aadhar_no != $row->aadhar_no && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 165)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }

        // Approver Reject
        if ($row->no_aadhar == 1 && !empty($request->op_type) && $request->op_type == 'R' && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 184)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }






        if ($row->dup_aadhar == 1 && !empty($request->new_aadhar_no) && $request->new_aadhar_no == $row->aadhar_no && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 168)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }

        if ($row->dup_aadhar == 1 && !empty($request->new_aadhar_no) && $request->new_aadhar_no != $row->aadhar_no && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 167)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }

        // Approver Reject
        if ($row->dup_aadhar == 1 && !empty($request->op_type) && $request->op_type == 'R' && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 183)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }




        if ($row->no_mobile == 1 && !empty($request->new_mobile_no) && $request->new_mobile_no != $row->mobile_no && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 166)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }

        // Approver Reject
        if ($row->no_mobile == 1 && !empty($request->op_type) && $request->op_type == 'R' && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 182)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }



        if ($row->dup_mobile == 1 && !empty($request->new_mobile_no) && $request->new_mobile_no == $row->mobile_no && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 170)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }

        if ($row->dup_mobile == 1 && !empty($request->new_mobile_no) && $request->new_mobile_no != $row->mobile_no && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 169)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }

        // Approver Reject
        if ($row->dup_mobile == 1 && !empty($request->op_type) && $request->op_type == 'R' && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 181)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }


        if ($row->dup_bank == 1 && !empty($request->new_bank_code) && trim($request->new_bank_code) != trim($row->bank_code) && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 178)
                ->select('code', 'description')
                ->first();
        }

        if ($row->dup_bank == 1 && !empty($request->new_bank_code) && trim($request->new_bank_code) == trim($row->bank_code) && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 179)
                ->select('code', 'description')
                ->first();
        }

        // Approver Reject
        if ($row->dup_bank == 1 && !empty($request->op_type) && $request->op_type == 'R' && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 180)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }




        $pay_validated = 0;

        // Payment Failed Correction
        $pay_validated = $row->pay_validated;
        // dd($pay_validated);

        // Check for new bank code mismatch and update logic
        if ($row->is_bank_failed == 1 && $role == 2) {
            // dd($pay_validated);
            if ($pay_validated == 3) {
                if (empty($request->op_type)) {
                    $updateArray[] = DB::connection('pgsql')
                        ->table('m_update_code')
                        ->where('id', 171)
                        ->select('code', 'description')
                        ->first();
                }


                // Verifier Reject
                if (!empty($request->op_type) && $request->op_type == 'R' && $role == 2) {
                    $updateArray[] = DB::connection('pgsql')
                        ->table('m_update_code')
                        ->where('id', 189)
                        ->select('code', 'description')
                        ->first();
                    // ->toArray();
                }

            }

            if ($pay_validated == 4) {
                if (empty($request->op_type)) {
                    $updateArray[] = DB::connection('pgsql')
                        ->table('m_update_code')
                        ->where('id', 172)
                        ->select('code', 'description')
                        ->first();
                }
                // Verifier Reject
                if (!empty($request->op_type) && $request->op_type == 'R' && $role == 2) {
                    $updateArray[] = DB::connection('pgsql')
                        ->table('m_update_code')
                        ->where('id', 188)
                        ->select('code', 'description')
                        ->first();
                    // ->toArray();
                }
            }
            if ($pay_validated == 5) {
                if (empty($request->op_type)) {
                    $updateArray[] = DB::connection('pgsql')
                        ->table('m_update_code')
                        ->where('id', 173)
                        ->select('code', 'description')
                        ->first();
                }

                // Verifier Reject
                if (!empty($request->op_type) && $request->op_type == 'R' && $role == 2) {
                    $updateArray[] = DB::connection('pgsql')
                        ->table('m_update_code')
                        ->where('id', 187)
                        ->select('code', 'description')
                        ->first();
                    // ->toArray();
                }
            }
        }




        if ($row->is_bank_failed == 2 && !empty($request->process_type) && ($request->process_type == 1) && $role == 2) {

            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 175)
                ->select('code', 'description')
                ->first();


        }

        if ($row->is_bank_failed == 2 && !empty($request->process_type) && ($request->process_type == 2) && $role == 2) {

            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 176)
                ->select('code', 'description')
                ->first();


        }

        if ($row->is_bank_failed == 2 && !empty($request->process_type) && ($request->process_type == 3) && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 177)
                ->select('code', 'description')
                ->first();
        }

        // Verifier Reject
        if ($row->is_bank_failed == 2 && !empty($request->op_type) && $request->op_type == 'R' && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 186)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }






        if ($row->is_bank_failed == 3 && !empty($request->new_bank_code) && ($request->new_bank_code != $request->old_bank_code) && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 174)
                ->select('code', 'description')
                ->first();
        }

        // Verifier Reject
        if ($row->is_bank_failed == 2 && !empty($request->op_type) && $request->op_type == 'R' && $role == 2) {
            $updateArray[] = DB::connection('pgsql')
                ->table('m_update_code')
                ->where('id', 185)
                ->select('code', 'description')
                ->first();
            // ->toArray();
        }
        return $updateArray;

    }

}
