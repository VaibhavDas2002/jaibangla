<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DupliacteApproveReject;
use App\Scheme;
use App\District;
use App\UrbanBody;
use App\GP;
use App\BeneficiaryPensions;
use App\PensionSc;
use App\PensionSt;
use App\Manabik;
use App\UpdateBenDetails;
use App\Configduty;
use App\DocumentType;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\AcceptRejectInfo;
use App\Helpers\AuthChecker;
use App\Helpers\DupCheck;
class CrossSchemeBankDupListController extends Controller
{
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
    }
    // Verifier
    public function bankDupVerifierIndex(Request $request)
    {
        $is_active = 0;
        // echo '<pre>'; print_r($roleArray);die();
        $user_id = Auth::user()->id;
        $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        // $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where scheme_id in (1,3,2,10,11) and  is_active=1 and user_id=" . $user_id . ")  order by rank"));
        // echo '<pre>';print_r($schemes);die();
        if (AuthChecker::AdminChecker() || AuthChecker::HODChecker() || AuthChecker::HOPChecker() || AuthChecker::MisStateChecker() || AuthChecker::DashboardChecker()) {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if (AuthChecker::ApproverPermission()) {
            // echo $designation_id;die();
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                // echo $designation_id;die();
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
                return redirect("/")->with('danger', 'User Disabled. ');
        } else {
            return redirect("/")->with('danger', 'User Disabled. ');
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
        return view(
            'CrossSchemeDupList/cross_scheme_bank_dup_verifier_list',
            [
                'schemes' => $schemes,
                'districts' => $districts,
                'district_visible' => $district_visible,
                'district_code_fk' => $district_code_fk,
                'is_urban_visible' => $is_urban_visible,
                'rural_urban_fk' => $rural_urban_fk,
                'block_visible' => $block_visible,
                'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
                'municipality_visible' => $municipality_visible,
                'gp_ward_visible' => $gp_ward_visible,
                'gpList' => $gpList,
                'muncList' => $muncList,
                // 'ds_phase_list' => $ds_phase_list
            ]
        );
    }

    public function crossSchemeBankDupVerifierList(Request $request)
    {
        if ($request->ajax()) {
            // dd($request->all());
            $crossSchemeType = $request->cross_scheme;
            $aadhar_filter = $request->aadhar_filter;
            $gp_ward = $request->gp_ward;

            $scheme = [];
            if ($crossSchemeType == 1) { //LB & Taposilsi Bandhu
                $schemes = "scheme_id IN (3, 20)";
                $scheme = [3, 20];
            } elseif ($crossSchemeType == 2) { // LB & Jai Johar
                $schemes = "scheme_id IN (1, 20)";
                $scheme = [1, 20];
            } elseif ($crossSchemeType == 3) { // LB & OAP
                $schemes = "scheme_id IN (10, 20)";
                $scheme = [10, 20];
            } elseif ($crossSchemeType == 4) { // Jai Johar & Taposili Bandhu
                $schemes = "scheme_id IN (1, 3)";
                $scheme = [1, 3];
            }
            //  dd($schemes);
            $query = $this->getBankDupRows($crossSchemeType, $schemes, $gp_ward, $scheme, $aadhar_filter);
            $result = DB::connection('pgsql_mis')->select($query);
            return datatables()->of($result)
                ->addColumn('aadhar_no', function ($result) {
                    $mask_aadhar = '';
                    $aadhar = trim($result->aadhar_no);
                    if (strlen($aadhar) >= 12 && strlen($aadhar) != '') {
                        $mask_aadhar = '********' . substr($aadhar, 8, 4);
                    } else {
                        $mask_aadhar = $aadhar;
                    }
                    return $mask_aadhar;
                })
                ->addColumn('payment_status', function ($result) {
                    $html = '';
                    if ($result->is_pause_resume_reject == 1) {
                        $html = '<span class="text-danger"><b>Payment Autostopped</b></span>';
                    }
                    if ($result->is_pause_resume_reject == -99) {
                        $html = '<span class="text-danger"><b>Rejected</b></span>';
                    }
                    return $html;
                })
                ->addColumn('action', function ($result) {
                    $action = '<div style="display: flex; gap: 5px;">';
                    if ($result->is_pause_resume_reject >= 0) {
                        $action .= '<button class="btn btn-danger btn-xs ben_view_details" value="' . $result->beneficiary_id . '_' . $result->scheme_id . '_1"><i class="glyphicon glyphicon-edit"></i>Reject</button>';
                    }
                    if ($result->is_pause_resume_reject == 1) {
                        $action .= '<button class="btn btn-primary btn-xs ben_view_details" value="' . $result->beneficiary_id . '_' . $result->scheme_id . '_2"><i class="glyphicon glyphicon-edit"></i>Resume</button>';
                    }
                    if ($result->is_pause_resume_reject == 0) {
                        $action .= '<button class="btn btn-info btn-xs ben_view_details" value="' . $result->beneficiary_id . '_' . $result->scheme_id . '_3"><i class="glyphicon glyphicon-edit"></i>Pause</button>';
                    }
                    $action .= '</div>';
                    return $action;
                })
                ->rawColumns(['aadhar_no', 'payment_status', 'action'])
                ->make(true);
        }
    }
    public function crossSchemeBankDupView(Request $request)
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
            $ben_details = $request->benid;
            $parts = explode('_', $ben_details);
            $beneficiary_id = $parts[0];
            $scheme_id = $parts[1];
            $type = $parts[2];
            $response = array_merge([
                'beneficiary_id' => $beneficiary_id,
                'scheme_id' => $scheme_id,
                'type' => $type
            ]);
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

    public function crossSchemeBankDupPost(Request $request)
    {
        //  dd($request->all());
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $user_id = Auth::user()->id;
            $designation_id = Auth::user()->designation_id;
            $mapObj = DB::connection('pgsql_mis')
                ->table('public.duty_assignement')
                ->where('user_id', $user_id)
                ->where('is_active', 1)
                ->first();
            $c_time = date('Y-m-d H:i:s', time());
            $ben_id = $request->beneficiary_id;
            $scheme_id = $request->scheme_id;
            $type = $request->type;
            $accept_reject_comments = $request->accept_reject_comments;
            $district_code = $mapObj->district_code;
            // $block_code = $request->block_code;
            $accept_reject_model = new AcceptRejectInfo;
            $accept_reject_model->created_at = $c_time;
            $accept_reject_model->application_id = $ben_id;
            $accept_reject_model->scheme_id = $scheme_id;
            $accept_reject_model->user_id = $user_id;
            $accept_reject_model->created_by_dist_code = $district_code;
            // $accept_reject_model->created_by_local_body_code = $block_code;
            $accept_reject_model->ip_address = request()->ip();
            $accept_reject_model->comment_message = $accept_reject_comments;
            if ($type == 1) {
                $accept_reject_model->op_type = 'CROSSAR';
            } else if ($type == 2) {
                $accept_reject_model->op_type = 'CROSSARESUME';
            } else if ($type == 3) {
                $accept_reject_model->op_type = 'CROSSAPAUSE';
            }
            $scheme_obj = Scheme::where('id', $request->scheme_id)->where('is_active', 1)->first();
            if (!empty($scheme_obj->short_code)) {
                $schema = $scheme_obj->short_code;
                $scheme_length = $scheme_obj->scheme_length;
                $id_length = $scheme_obj->id_length;
            } else {
                $schema = "pension";
                $scheme_length = NULL;
                $id_length = NULL;
            }
            if ($scheme_id == 20) {
                if ($type == 1) {
                    $op_type = 'CROSSAR';
                } else if ($type == 2) {
                    $op_type = 'CROSSARESUME';
                } else if ($type == 3) {
                    $op_type = 'CROSSAPAUSE';
                }
                $lb_ben_details = DB::table('data_check.cross_scheme_bank_details')->where('beneficiary_id', $ben_id)->where('scheme_id', 20)->first();
                $lb_application_id = $lb_ben_details->application_id;
                $update_ben_details = [
                    'ben_id' => $ben_id,
                    'scheme_id' => 20,
                    'application_id' => $lb_application_id,
                    'designation_id' => $designation_id,
                    'user_id' => $user_id,
                    'created_by' => $user_id,
                    'created_by_level' => $mapObj->mapping_level,
                    'created_by_dist_code' => $district_code,
                    'comment_message' => $accept_reject_comments,
                    'op_type' => $op_type,
                    'ip_address' => request()->ip(),
                    'mapping_level' => $mapObj->mapping_level
                ];
            }
            // dd($update_ben_details);
            DB::beginTransaction();
            DB::connection('pgsql_paywrite')->beginTransaction();
            if ($scheme_id == 20) {
                DB::connection('pgsql_lb_encwrite')->beginTransaction();
            }
            if ($scheme_id == 10) {
                $is_saved_log = $accept_reject_model->save();
            } else if ($scheme_id == 20) {
                $is_saved_log = DB::connection('pgsql_lb_encwrite')->table('lb_scheme.ben_accept_reject_info')->insert($update_ben_details);
            }
            if ($is_saved_log) {
                if ($type == 1) {
                    if ($scheme_id == 10) {
                        $fun_call = DB::connection('pgsql_paywrite')->select("SELECT payment.reject_payment_beneficiary(
                            in_application_id => " . $ben_id . ", 
                            in_scheme_id => " . $scheme_id . "
                        );");
                        $is_update = $fun_call[0]->reject_payment_beneficiary;
                    }

                    // dd($is_update);
                    if ($scheme_id == 20) {
                        $serverip = 'http://10.176.100.12';
                        $post_url = $serverip . '/api/reject-beneficiaryLB';
                        $curl = curl_init($post_url);
                        $headers = array(
                            'Content-Type: application/json'
                        );
                        $data = array("application_id" => $lb_application_id, "accept_reject_comments" => $accept_reject_comments);
                        $data_string = json_encode($data);
                        header("Access-Control-Allow-Origin: *");
                        curl_setopt($curl, CURLOPT_URL, $post_url);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
                        $post_response = curl_exec($curl);
                        // dd($post_response);
                        if ($post_response) {
                            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            curl_close($curl);
                            if ($httpcode == 200) {
                                $post_response_lb = json_decode($post_response);
                                $is_update = $post_response_lb->is_reject;
                            } else {
                                return $response = [
                                    'status' => 3,
                                    'msg' => 'Somethimg went wrong',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        } else {
                            return $response = [
                                'status' => 3,
                                'msg' => 'Somethimg went wrong !',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }

                    }
                    $pause_resume_update = DB::table('data_check.cross_scheme_bank_details')->where('beneficiary_id', $ben_id)->update(['is_pause_resume_reject' => -99]);
                } else if ($type == 2) {
                    $dup_ben_details = DB::table('data_check.cross_scheme_bank_details')->where('beneficiary_id', $ben_id)->first();
                    $dup_bank_details = $dup_ben_details->bank_code;
                    $dupBenCount = DB::table('data_check.cross_scheme_bank_details')
                        ->where('bank_code', $dup_bank_details)
                        ->where('is_pause_resume_reject', 0)
                        ->where('beneficiary_id', '<>', $ben_id)
                        ->count();
                    if ($dupBenCount > 0) {
                        return $response = [
                            'status' => 3,
                            'msg' => 'The bank details already exist with another beneficiary.Please pause/reject that beneficiary then it will be resume',
                            'type' => 'red',
                            'icon' => 'fa fa-warning',
                            'title' => 'Warning!!',
                        ];
                    }
                    // dd($response);
                    if ($scheme_id == 10) {
                        $fun_call = DB::connection('pgsql_paywrite')->select("SELECT payment.resume_payment_beneficiary(
                            in_application_id => " . $ben_id . ", 
                            in_scheme_id => " . $scheme_id . "
                        );");
                        $is_update = $fun_call[0]->resume_payment_beneficiary;
                    }
                    if ($scheme_id == 20) {
                        $serverip = 'http://10.176.100.12';
                        $post_url = $serverip . '/api/resume-beneficiary';
                        $curl = curl_init($post_url);
                        $headers = array(
                            'Content-Type: application/json'
                        );
                        $data = array("ben_id" => $ben_id);
                        $data_string = json_encode($data);
                        header("Access-Control-Allow-Origin: *");
                        curl_setopt($curl, CURLOPT_URL, $post_url);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
                        $post_response = curl_exec($curl);
                        // dd($post_response);
                        if ($post_response) {
                            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            curl_close($curl);
                            if ($httpcode == 200) {
                                $post_response_lb = json_decode($post_response);
                                $is_update = $post_response_lb->is_pause;
                            } else {
                                return $response = [
                                    'status' => 3,
                                    'msg' => 'Somethimg went wrong',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        } else {
                            return $response = [
                                'status' => 3,
                                'msg' => 'Somethimg went wrong !',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }
                    }
                    $pause_resume_update = DB::table('data_check.cross_scheme_bank_details')->where('beneficiary_id', $ben_id)->update(['is_pause_resume_reject' => 0]);
                } else if ($type == 3) {
                    if ($scheme_id == 20) {
                        $serverip = 'http://10.176.100.12';
                        $post_url = $serverip . '/api/pause-beneficiary';
                        $curl = curl_init($post_url);
                        $headers = array(
                            'Content-Type: application/json'
                        );
                        $data = array("ben_id" => $ben_id);
                        $data_string = json_encode($data);
                        header("Access-Control-Allow-Origin: *");
                        curl_setopt($curl, CURLOPT_URL, $post_url);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
                        $post_response = curl_exec($curl);
                        // dd($post_response);
                        if ($post_response) {
                            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            curl_close($curl);
                            if ($httpcode == 200) {
                                $post_response_lb = json_decode($post_response);
                                $is_update = $post_response_lb->is_pause;
                            } else {
                                return $response = [
                                    'status' => 3,
                                    'msg' => 'Somethimg went wrong !!',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        } else {
                            return $response = [
                                'status' => 3,
                                'msg' => '1 Somethimg went wrong !!',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }
                    }
                    if ($scheme_id == 10) {
                        $fun_call = DB::connection('pgsql_paywrite')->select("SELECT payment.pause_payment_beneficiary(
                            in_application_id => " . $ben_id . ", 
                            in_scheme_id => " . $scheme_id . "
                        );");
                        $is_update = $fun_call[0]->pause_payment_beneficiary;
                    }
                    $pause_resume_update = DB::table('data_check.cross_scheme_bank_details')->where('beneficiary_id', $ben_id)->update(['is_pause_resume_reject' => 1]);
                }
            }
            //  dump($is_saved_log);dump($is_update); dd($pause_resume_update);
            if ($is_saved_log && $is_update && $pause_resume_update) {
                DB::commit();
                DB::connection('pgsql_paywrite')->commit();
                if ($scheme_id == 20) {
                    DB::connection('pgsql_lb_encwrite')->commit();
                }
                if ($type == 1) {
                    $msg = 'Beneficiary Rejected Successfully';
                } else if ($type == 2) {
                    $msg = 'Beneficiary Resume Successfully';
                } else if ($type == 3) {
                    $msg = 'Beneficiary Paused Successfully';
                }
                return $response = [
                    'status' => 1,
                    'msg' => $msg,
                    'type' => 'green',
                    'icon' => 'fa fa-check',
                    'title' => 'Success',
                ];
            } else {
                DB::rollback();
                DB::connection('pgsql_paywrite')->rollback();
                if ($scheme_id == 20) {
                    DB::connection('pgsql_lb_encwrite')->rollback();
                }
                return $response = [
                    'status' => 3,
                    'msg' => '3 Somethimg went wrong!!',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            }
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            DB::connection('pgsql_paywrite')->rollback();
            if ($scheme_id == 20) {
                DB::connection('pgsql_lb_encwrite')->rollback();
            }
            $response = [
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' =>
                    'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
            //throw $th;
        } finally {
            // dd($response);
            return response()->json($response, $statusCode);
        }
    }


    // HOD
    public function index()
    {
        $designation_id = Auth::user()->designation_id;
        if ($designation_id == 'HOD' || $designation_id == 'Admin') {
            $is_active = 1;
        } else {
            $is_active = 0;
        }
        $distList = District::orderBy('district_name')->get();
        if ($is_active == 1) {
            return view('CrossSchemeDupList/cross_scheme_bank_dup_list', [
                'districts' => $distList
            ]);
        } else {
            return redirect("/")->with('success', 'UnAuthorized');
        }
    }

   

    public function crossSchemeBankDupList(Request $request)
    {
        try {
            $cross_scheme_id = $request->cross_scheme;
            $scheme_id = $request->scheme;
            $gp_ward = $request->gp_ward;
            $muncid = $request->muncid;
            $aadhar_filter = $request->aadhar_filter;

            if (!empty($scheme_id) && !empty($cross_scheme_id)) {
                $schemeCon = "scheme_id in ($scheme_id, $cross_scheme_id)";
            }
            $scheme = Scheme::where('id', $scheme_id)->first();
            $cross_scheme = Scheme::where('id', $cross_scheme_id)->first();
            
            $schemeNames = "{$scheme->scheme_name} & {$cross_scheme->scheme_name}";
            
            $user_msg = 'Cross Scheme Bank Duplicate List of ' . $schemeNames;

            $query = $this->getBankDupRows($cross_scheme_id, $scheme_id, $gp_ward, $aadhar_filter, $muncid);

            // dd($query);
            $result = DB::connection('pgsql_mis')->select($query);
            return datatables()->of($result)
                ->addColumn('aadhar_no', function ($result) {
                    $mask_aadhar = '';
                    $aadhar = trim($result->aadhar_no);
                    if (strlen($aadhar) >= 12 && strlen($aadhar) != '') {
                        $mask_aadhar = '********' . substr($aadhar, 8, 4);
                    } else {
                        $mask_aadhar = $aadhar;
                    }
                    return $mask_aadhar;
                })
                ->addColumn('payment_status', function ($result) {
                    $html = '';
                    if ($result->is_pause_resume_reject == 1) {
                        $html = '<span class="text-danger"><b>Payment Autostopped</b></span>';
                    }
                    if ($result->is_pause_resume_reject == -99) {
                        $html = '<span class="text-danger"><b>Rejected</b></span>';
                    }
                    return $html;
                })
                ->addColumn('action', function ($result) {
                    $action = '<div style="display: flex; gap: 5px;">';
                    if ($result->is_pause_resume_reject >= 0) {
                        $action .= '<button class="btn btn-danger btn-xs ben_view_details" value="' . $result->beneficiary_id . '_' . $result->scheme_id . '_1"><i class="glyphicon glyphicon-edit"></i>Reject</button>';
                    }
                    if ($result->is_pause_resume_reject == 1) {
                        $action .= '<button class="btn btn-primary btn-xs ben_view_details" value="' . $result->beneficiary_id . '_' . $result->scheme_id . '_2"><i class="glyphicon glyphicon-edit"></i>Resume</button>';
                    }
                    if ($result->is_pause_resume_reject == 0) {
                        $action .= '<button class="btn btn-info btn-xs ben_view_details" value="' . $result->beneficiary_id . '_' . $result->scheme_id . '_3"><i class="glyphicon glyphicon-edit"></i>Pause</button>';
                    }
                    $action .= '</div>';
                    return $action;
                })
                ->rawColumns(['aadhar_no', 'payment_status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            // dd($e);
        }
    }


    private function getBankDupRows($cross_scheme_id, $scheme_id, $gp_ward, $aadhar_filter, $muncid)
    {
        $user_id = Auth::user()->id;
        $designation = Auth::user()->designation_id;
        $mapObj = DB::connection('pgsql_mis')
            ->table('public.duty_assignement')
            ->where('user_id', $user_id)
            ->where('is_active', 1)
            ->first();
        // dd($mapObj);    
        if (Auth::user()->designation_id == 'Verifier') {
            if ($mapObj->is_urban == 1) {
                $local_body_code = $mapObj->urban_body_code;
            } else {
                $local_body_code = $mapObj->taluka_code;
            }
        }
        if ($local_body_code) {
            $local_body_code = " AND created_by_local_body_code =" . $local_body_code;
        }
        if ($gp_ward) {
            $gp_ward_code = " AND gp_ward_code =" . $gp_ward;
        } else {
            $gp_ward_code = "";
        }


        if ($aadhar_filter == 1) {
            $data = "SELECT d.district_name, scheme_name, application_id, beneficiary_id, ben_name, block_ulb_name, gp_ward_name, mobile_no, bank_code, aadhar_no, cross_bank_aadhar_dup, scheme_id,is_pause_resume_reject FROM
            (SELECT dist_code, scheme_id, application_id, beneficiary_id, ben_name, block_ulb_name, gp_ward_name, mobile_no, bank_code, aadhar_no, cross_bank_aadhar_dup,is_pause_resume_reject FROM data_check.cross_scheme_bank_details WHERE bank_code IN
            (
                SELECT bank_code FROM data_check.cross_scheme_bank_details WHERE bank_code IN
                (
                    SELECT bank_code FROM data_check.cross_scheme_bank_details WHERE scheme_id = $scheme_id  AND next_level_role_id = 0
                    INTERSECT
                    SELECT bank_code FROM data_check.cross_scheme_bank_details WHERE scheme_id = $cross_scheme_id
                ) AND  scheme_id in ($scheme_id,$cross_scheme_id)
            )  AND scheme_id in ($scheme_id,$cross_scheme_id) AND cross_bank_dup = 1 AND cross_bank_aadhar_dup = 0 " . $local_body_code . " " . $gp_ward_code . " ORDER BY bank_code) a
            JOIN
            (SELECT scheme_name, id FROM m_scheme) b ON a.scheme_id = b.id
            JOIN
            (SELECT district_code, district_name FROM m_district) d ON d.district_code = a.dist_code";
        } else if ($aadhar_filter == 2) {
            $data = "SELECT d.district_name, scheme_name, application_id, beneficiary_id, ben_name, block_ulb_name, gp_ward_name, mobile_no, bank_code, aadhar_no, cross_bank_aadhar_dup, scheme_id,is_pause_resume_reject FROM
                (SELECT dist_code, scheme_id, application_id, beneficiary_id, ben_name, block_ulb_name, gp_ward_name, mobile_no, bank_code, aadhar_no, cross_bank_aadhar_dup,is_pause_resume_reject FROM data_check.cross_scheme_bank_details WHERE bank_code IN
                (
                    SELECT bank_code FROM data_check.cross_scheme_bank_details WHERE bank_code IN
                    (
                        SELECT bank_code FROM data_check.cross_scheme_bank_details WHERE scheme_id = $scheme_id AND next_level_role_id = 0
                        INTERSECT
                        SELECT bank_code FROM data_check.cross_scheme_bank_details WHERE scheme_id = $cross_scheme_id
                    ) AND aadhar_no IN
                    (
                        SELECT aadhar_no FROM data_check.cross_scheme_bank_details WHERE scheme_id = $scheme_id AND next_level_role_id = 0
                        INTERSECT
                        SELECT aadhar_no FROM data_check.cross_scheme_bank_details WHERE scheme_id = $cross_scheme_id 
                    ) AND scheme_id in ($scheme_id, $cross_scheme_id)
                )  AND scheme_id in  ($scheme_id, $cross_scheme_id) AND cross_bank_aadhar_dup = 1 " . $local_body_code . " " . $gp_ward_code . " ORDER BY bank_code) a
                JOIN
                (SELECT scheme_name, id FROM m_scheme) b ON a.scheme_id = b.id
                JOIN
                (SELECT district_code, district_name FROM m_district) d ON d.district_code = a.dist_code";
        }

        return $data;

    }



  



    public function crossSchemeBankDupExcel(Request $request)
    {
        //  dd($request->all());
        $crossSchemeType = $request->cross_scheme;
        $aadhar_filter = $request->aadhar_filter;
        $gp_ward = $request->gp_ward;

        $scheme = [];
        if ($crossSchemeType == 1) { //LB & Taposilsi Bandhu
            $schemes = "scheme_id IN (3, 20)";
            $scheme = [3, 20];
            $schemeNames = "LB & Taposili Bandhu";
        } elseif ($crossSchemeType == 2) { // LB & Jai Johar
            $schemes = "scheme_id IN (1, 20)";
            $scheme = [1, 20];
            $schemeNames = "LB & Jai Johar";
        } elseif ($crossSchemeType == 3) { // LB & OAP
            $schemes = "scheme_id IN (10, 20)";
            $scheme = [10, 20];
            $schemeNames = "LB & Old Age Pension";
        } elseif ($crossSchemeType == 4) { // Jai Johar & Taposili Bandhu
            $schemes = "scheme_id IN (1, 3)";
            $scheme = [1, 3];
            $schemeNames = "Jai Johar & Taposili Bandhu";
        }
        $user_msg = 'Cross Scheme Bank Duplicate List of ' . $schemeNames;
        // dd($crossSchemeType);
        $query = $this->getBankDupRows($crossSchemeType, $schemes, $gp_ward, $scheme, $aadhar_filter);
        $result = DB::connection('pgsql_mis')->select($query);
        $excelarr[] = array(
            'Scheme',
            'Application ID',
            'Beneficiary ID',
            'Name',
            'Block/Municipality',
            'GP/Ward',
            'Mobile No.',
            'Account No.',
            'Aadhar',
            'Payment Status'
        );
        foreach ($result as $arr) {
            if ($arr->is_pause_resume_reject == 1) {
                $payment_status = 'Payment Autostopped';
            } else if ($arr->is_pause_resume_reject == -99) {
                $payment_status = 'Rejected';
            } else {
                $payment_status = '';
            }
            $excelarr[] = array(
                'Scheme' => trim($arr->scheme_name),
                'Application ID' => trim($arr->application_id),
                'Beneficiary ID' => trim($arr->beneficiary_id),
                'Name' => trim($arr->ben_name),
                'Block/Municipality' => trim($arr->block_ulb_name),
                'GP/Ward' => trim($arr->gp_ward_name),
                'Mobile No.' => trim($arr->mobile_no),
                'Account No.' => trim($arr->bank_code),
                'Aadhar' => trim($arr->aadhar_no),
                'Payment Status' => $payment_status
            );
        }
        $file_name = $user_msg . ' ' . date('d/m/Y');
        Excel::create($file_name, function ($excel) use ($excelarr) {
            $excel->setTitle('Bank Duplicate List');
            $excel->sheet('Bank Duplicate List', function ($sheet) use ($excelarr) {
                $sheet->fromArray($excelarr, null, 'A1', false, false);
            });
        })->download('xlsx');
    }


}
