<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UpdateBenDetails;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Helpers\APICurl;
use App\Helpers\JWTToken;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Configduty;
use App\getModelFunc;
use App\UrbanBody;
use App\GP;
use App\MapLavel;
use Maatwebsite\Excel\Facades\Excel;
use App\DocumentType;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;
use Carbon\Carbon;
use App\District;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use App\DsPhase;
use App\Scheme;
use App\RejectRevertReason;
use App\AcceptRejectInfo;
use App\Helpers\PermissionManagement;
use App\Helpers\AuthChecker;
use App\Workflow;

class CmoGrivanceWorkflowController extends Controller
{
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
    }
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
            $table_name = 'pension.beneficiaries';
        }
        return $table_name;
    }
    public function index(Request $request)
    {
        $scheme_id = $request->scheme_id;
        $cmoCheck = PermissionManagement::CmoCheck($scheme_id);
        if ($cmoCheck) {
            $user_id = AuthChecker::getUserId();
            $is_verifer = AuthChecker::VerifierChecker();
            $is_approver = AuthChecker::ApproverChecker();
            $is_hod = AuthChecker::HODChecker();

            $mapObj = DB::connection('pgsql_mis')
                ->table('public.duty_assignement')
                ->where('user_id', $user_id)
                ->where('is_active', 1)
                ->first();
            $scheme = DB::connection('pgsql_mis')->select(
                'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
                $user_id .
                ' and is_active=1 and scheme_id=10) order by scheme_name'
            );
            $scheme_name = Scheme::where('id', $scheme_id)->value('scheme_name');
            if (AuthChecker::VerifierChecker()) {
                if (count($scheme) > 0) {
                    if ($mapObj->is_urban == 1) {
                        $urban_body_code = $mapObj->urban_body_code;
                        $urban_bodys = UrbanBody::where(
                            'sub_district_code',
                            $urban_body_code
                        )
                            ->select('urban_body_code', 'urban_body_name')
                            ->get();
                        return view('cmo-grievance/index', [
                            'schemes' => $scheme,
                            'mapLevel' => $mapObj->mapping_level,
                            'urban_bodys' => $urban_bodys,
                            'local_body_code' => $urban_body_code,
                            'district_code' => $mapObj->district_code,
                            'scheme_id' => $scheme_id,
                            'scheme_name' => $scheme_name,
                            'is_verifier' => $is_verifer,
                            'is_approver' => $is_approver,
                            'is_hod' => $is_hod,

                        ]);
                    } else {
                        $taluka_code = $mapObj->taluka_code;
                        $gps = GP::where('block_code', $taluka_code)
                            ->select('gram_panchyat_code', 'gram_panchyat_name')
                            ->get();
                        return view('cmo-grievance/index', [
                            'schemes' => $scheme,
                            'mapLevel' => $mapObj->mapping_level,
                            'gps' => $gps,
                            'local_body_code' => $taluka_code,
                            'district_code' => $mapObj->district_code,
                            'scheme_id' => $scheme_id,
                            'scheme_name' => $scheme_name,
                            'is_verifier' => $is_verifer,
                            'is_approver' => $is_approver,
                            'is_hod' => $is_hod,
                        ]);
                    }
                } else {
                    return redirect('/')->with(
                        'success',
                        'User disabled. No scheme assign to this user'
                    );
                }
            } else if (AuthChecker::ApproverChecker()) {
                if (count($scheme) > 0) {
                    $district_code = $mapObj->district_code;
                    return view('cmo-grievance/index', [
                        'schemes' => $scheme,
                        'mapLevel' => $mapObj->mapping_level,
                        'district_code' => $district_code,
                        'scheme_id' => $scheme_id,
                        'scheme_name' => $scheme_name,
                        'is_verifier' => $is_verifer,
                        'is_approver' => $is_approver,
                        'is_hod' => $is_hod,
                    ]);
                } else {
                    return redirect('/')->with(
                        'success',
                        'User disabled. No scheme assign to this user'
                    );
                }
            } else if (AuthChecker::HODChecker()) {
                if (count($scheme) > 0) {
                    return view('cmo-grievance/index', [
                        'schemes' => $scheme,
                        'mapLevel' => $mapObj->mapping_level,
                        'scheme_id' => $scheme_id,
                        'scheme_name' => $scheme_name,
                        'is_verifier' => $is_verifer,
                        'is_approver' => $is_approver,
                        'is_hod' => $is_hod,
                    ]);
                } else {
                    return redirect('/')->with(
                        'success',
                        'User disabled. No scheme assign to this user'
                    );
                }
            } else {
                return redirect('/')->with('success', 'UnAuthorized');
            }
        }
    }
    public function listing(Request $request)
    {
        if ($request->ajax()) {
            // dd($request->all());
            $scheme_id = $request->scheme_id;
            $local_body_code = $request->local_body;
            $dist_code = $request->district_code;
            $process_type = $request->process_type;
            $mapLevel = $request->mapLevel;
            $filter_1 = $request->filter_1;
            $filter_2 = $request->filter_2;
            $district_code = $request->district_code;
            if ($mapLevel == 'Block') {
                if (!empty($request->filter_1)) {
                    $query = " Select * from cmo.cmo_sm_data where jb_local_body_code=" . $local_body_code . " and jb_gp_ward_code=" . $filter_1 . "";
                } else {
                    $query = " Select * from cmo.cmo_sm_data where jb_local_body_code=" . $local_body_code . "";
                }
            } elseif ($mapLevel == 'Subdiv') {
                if (!empty($request->filter_1) && empty($request->filter_2)) {
                    $query = " Select * from cmo.cmo_sm_data where jb_local_body_code=" . $local_body_code . " and jb_block_ulb_code=" . $filter_1 . "";
                } elseif (!empty($request->filter_1) && !empty($request->filter_2)) {
                    $query = " Select * from cmo.cmo_sm_data where jb_local_body_code=" . $local_body_code . " and jb_block_ulb_code=" . $filter_1 . " and jb_gp_ward_code=" . $filter_2 . " ";
                } else {
                    $query = " Select * from cmo.cmo_sm_data where  jb_local_body_code=" . $local_body_code . "";
                }
            } elseif ($mapLevel == 'District') {
                $query = " Select * from cmo.cmo_sm_data where jb_dist_code=" . $district_code . " and jb_local_body_code is null";
            } elseif ($mapLevel == 'Department') {
                $query = " Select * from cmo.cmo_sm_data where  jb_dist_code is null and jb_local_body_code is null";
            } else {
                return redirect('/')->with('success', 'UnAuthorized');
            }
            if ($process_type == 1) {
                $query .= " and is_processed = 0 and is_redressed = 0";
            } else if ($process_type == 2) {
                $query .= " and is_processed = 1 and is_redressed = 0";
            } else if ($process_type == 3) {
                $query .= " and is_processed = 2 and is_redressed = 0";
            } else if ($process_type == 4) {
                $query .= " and is_processed = 1 and is_redressed = 1";
            } else if ($process_type == 5) {
                $query .= " and is_change_block = 1 and is_processed = 0";
            }
            //   dd($query);
            $data = DB::select($query);
            // dd($data);
            return datatables()
                ->of($data)
                ->addColumn('view', function ($data) use ($scheme_id) {
                    $action = '';
                    if ($data->is_processed == 0) {
                        // href="CMO-grievance-find?id=' . $data->jb_beneficiary_id  . '&scheme_id=' . $data->scheme_id . '&sm_mobile_no='.$data->sm_mobile_no.'"
                        $action = '<button value="' . $data->grievance_id . '_' . $scheme_id . '_' . $data->grievance_mobile . '" class="btn btn-xs btn-info find_applicant"><i class="glyphicon glyphicon-edit"></i>Find</button>';
                    } else if ($data->is_processed == 1 && $data->is_redressed == 0) {
                        $action = 'Marked';
                    } else if ($data->is_processed == 2 && $data->is_redressed == 0) {
                        $action = 'Marked & Send back to CMO';
                    } else if ($data->is_processed == 1 && $data->is_redressed == 1) {
                        $action = 'Redressed';
                    }
                    return $action;
                })
                ->addColumn('grievance_id', function ($data) {
                    return $data->grievance_id;
                })
                ->addColumn('grievance_name', function ($data) {
                    return $data->caller_name;
                })
                ->addColumn('sm_mobile_no', function ($data) {
                    return $data->grievance_mobile;
                })
                ->addColumn('cmo_receive_date', function ($data) {
                    return $data->complain_date;
                })
                ->addColumn('gp_ward_name', function ($data) {
                    return $data->g_gp_ward_name;
                })
                // ->addColumn('description', function ($data) {
                //     return $data->complain_description;
                // })
                ->rawColumns(['view', 'grievance_id', 'grievance_name', 'sm_mobile_no', 'cmo_receive_date', 'g_gp_ward_name'])
                ->make(true);
        }
    }
    public function find(Request $request)
    {
        //   dd($request->all());
        $user_id = AuthChecker::getUserId();
        // $designation = AuthChecker::getDesignationId();
        $grievance_id = $request->grievance_id;
        $scheme_id = $request->scheme_id;
        $grievance_mobile_no = $request->grievance_mobile_no;
        $districtList = District::get();
        $mapObj = DB::connection('pgsql_mis')->table('public.duty_assignement')->where('user_id', $user_id)->where('is_active', 1)->first();
        $row = DB::connection('pgsql_mis')->table('cmo.cmo_sm_data')->where('grievance_id', $grievance_id)->where('grievance_mobile', $grievance_mobile_no)->first();
        $atr = DB::connection('pgsql_mis')->select(
            'select distinct atr_code,atr_desc from public.m_cmo_atr order by atr_code,atr_desc'
        );
        //    dd($row->atr_type);
        // $scheme = DB::connection('pgsql_mis')->select('select id,scheme_name from public.m_scheme where id in (select scheme_id from public.duty_assignement where user_id=' . $user_id . ' and is_active=1) order by scheme_name');
        if (AuthChecker::VerifierChecker() || AuthChecker::ApproverChecker() || AuthChecker::HODChecker()) {
            return view('cmo-grievance/find_applicant', ['scheme_id' => $scheme_id, 'grievance_id' => $grievance_id, 'grievance_mobile_no' => $grievance_mobile_no, 'row' => $row, 'atr' => $atr, 'districtList' => $districtList]);
        } else {
            return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
        }
    }
    public function redress(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $rules = [
                'atr_type' => 'required',
                'remarks' => 'required',
            ];
            $attributes = [
                'atr_type' => 'ATR Type',
                'remarks' => 'Remarks',
            ];
            $messages = [
                'required' => 'The :attribute field is required.',
            ];
            $validator = Validator::make(
                $request->all(),
                $rules,
                $messages,
                $attributes
            );
            if ($validator->passes()) {
                $user_id = AuthChecker::getUserId();
                $scheme_id = $request->scheme_id;
                $grievance_mobile_no = $request->grievance_mobile_no;
                $grievance_id = $request->grievance_id;
                $atr_type = $request->atr_type;
                $remarks = $request->remarks;
                DB::beginTransaction();
                $updateDetails = [];
                $updateDetails['is_redressed'] = 1;
                $updateDetails['is_processed'] = 1;
                $updateDetails['atr_type'] = $atr_type;
                $updateDetails['redressed_by'] = $user_id;
                $updateDetails['redressed_date'] = date('Y-m-d H:i:s');
                $updateDetails['remarks'] = $remarks;
                // dd($updateDetails);
                $is_update = DB::table('cmo.cmo_sm_data')
                    ->where('grievance_id', $grievance_id)
                    ->where('grievance_mobile', $grievance_mobile_no)
                    ->where('is_processed', 0)
                    ->where('is_redressed', 0)
                    ->update($updateDetails);
                if ($is_update) {
                    DB::commit();
                    $response = [
                        'status' => 1,
                        'msg' => 'Grievance redressed Successfully',
                        'type' => 'green',
                        'icon' => 'fa fa-check',
                        'title' => 'Success',
                    ];
                } else {
                    DB::rollback();
                    $response = [
                        'status' => 3,
                        'msg' => '3 Somethimg went wrong!!',
                        'type' => 'red',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ];
                }
            } else {
                $return_status = 0;
                $return_msg = $validator->errors()->all();
                $response = [
                    'status' => $return_status,
                    'msg' => $return_msg,
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            }
        } catch (\Exception $e) {
            //  dd($e);
            DB::rollback();
            $response = [
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' =>
                    'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }
    public function benlisting(Request $request)
    {
        if ($request->ajax()) {
            $user_id = AuthChecker::getUserId();
            $designation = Auth::user()->designation_id_old;
            $scheme_id = $request->scheme_id;
            $grivence_mobile = $request->grivence_mobile;
            $grievance_id = $request->grievance_id;
            $new_process_id = $request->new_process_id;
            $input_value = $request->input_value;
            $mapObj = DB::connection('pgsql_mis')
                ->table('public.duty_assignement')
                ->where('user_id', $user_id)
                ->where('is_active', 1)
                ->first();
            if (AuthChecker::VerifierChecker()) {
                if ($mapObj->is_urban == 1) {
                    $local_body_code = $mapObj->urban_body_code;
                } else {
                    $local_body_code = $mapObj->taluka_code;
                }
            } else if (AuthChecker::ApproverChecker()) {
                $district_code = $mapObj->district_code;
            }
            if ($new_process_id == 5) {
                $input_value = strtolower(str_replace(' ', '', $input_value));
            }
            //   dd($input_value);
            $table_name = $this->getSchemaName($scheme_id);
            $query = "Select b.*,md.district_name, bl_div.block_subdiv_name,ms.scheme_name from $table_name b join public.m_district md ON md.district_code=b.created_by_dist_code 
                JOIN public.m_scheme ms ON ms.id=b.scheme_id 
                JOIN (SELECT block_code AS block_subdiv_code,block_name AS block_subdiv_name FROM public.m_block 
	  			    UNION ALL
      		    SELECT sub_district_code AS block_subdiv_code, sub_district_name AS block_subdiv_name FROM 	public.m_sub_district
      	        ) bl_div ON bl_div.block_subdiv_code=b.created_by_local_body_code 
                where b.scheme_id=" . $scheme_id . "";
            if ($designation == 'Verifier') {
                $query .= " and b.created_by_local_body_code=" . $local_body_code . "";
            }
            if ($designation == 'Approver') {
                $query .= " and b.created_by_dist_code=" . $district_code . "";
            }
            if ($new_process_id == 1) {
                $query .= " and b.id=" . $input_value . "";
            } else if ($new_process_id == 2) {
                $query .= " and b.mobile_no=" . $input_value . "";
            } else if ($new_process_id == 3) {
                $query .= " and b.aadhar_no='" . $input_value . "'";
            } else if ($new_process_id == 4) {
                $query .= " and b.bank_code='" . $input_value . "'";
            } else if ($new_process_id == 5) {
                $query .= " and LOWER(REPLACE(CONCAT(TRIM(COALESCE(ben_fname, '')), TRIM(COALESCE(ben_mname, '')), TRIM(COALESCE(ben_lname, ''))), ' ', ''))='" . $input_value . "'";
            }

            //    dd($query);
            $data = DB::select($query);
            return datatables()
                ->of($data)
                ->addColumn('view', function ($data) use ($grievance_id) {
                    $action = '<button value="' . $data->id . '_' . $data->scheme_id . '_' . $data->mobile_no . '_' . $grievance_id . '" class="btn btn-xs btn-info process_applicant"><i class="glyphicon glyphicon-edit"></i> Process</button>';
                    return $action;
                })
                ->addColumn('id', function ($data) {
                    return $data->id;
                })
                ->addColumn('scheme_name', function ($data) {
                    return $data->scheme_name;
                })
                ->addColumn('name', function ($data) {
                    return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                })
                ->addColumn('father_name', function ($data) {
                    return $data->father_fname . ' ' . $data->father_mname . ' ' . $data->father_lname;
                })
                ->addColumn('address', function ($data) {
                    $address = '';
                    $address = 'District - ' . $data->district_name . '<br>';
                    if ($data->rural_urban_id == 1) {
                        $address .= 'Sub-division - ' . $data->block_subdiv_name . '<br>';
                        $address .= 'Municipality - ' . $data->block_ulb_name . '<br>';
                        $address .= 'Ward - ' . $data->gp_ward_name;
                    } else {
                        $address .= 'Block - ' . $data->block_subdiv_name . '<br>';
                        $address .= 'GP - ' . $data->gp_ward_name;
                    }
                    return $address;
                })
                // ->addColumn('bank_info', function ($data) {
                //     $bank = '';
                //     if (!is_null($data->bank_name)) {
                //       $bank .= 'Bank Name - ' . $data->bank_name . '<br>';
                //     }
                //     if (!is_null($data->branch_name)) {
                //       $bank .= 'Branch - ' . $data->branch_name . '<br>';
                //     }
                //     $bank .= 'A/c No - ' . $data->bank_code . '<br>';
                //     $bank .= 'IFSC - ' . $data->bank_ifsc;
                //     return $bank;
                // })
                ->addColumn('mobile_no', function ($data) {
                    return $data->mobile_no;
                })
                ->addColumn('status', function ($data) {
                    if ($data->next_level_role_id == 0) {
                        $action = '<b>Approved</b>';
                    } else if ($data->next_level_role_id > 0) {
                        $action = '<b>Verified</b>';
                    } else if ($data->next_level_role_id < 0) {
                        $action = '<b>Rejected</b>';
                    } else if ($data->next_level_role_id == NULL) {
                        $action = '<b>Non Verified</b>';
                    }
                    return $action;
                })
                ->rawColumns(['view', 'id', 'scheme_name', 'name', 'father_name', 'mobile_no', 'address', 'status'])
                ->make(true);
        }
    }
    public function processPost(Request $request)
    {
        //   dd($request->all());
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $rules = [
                'atr_type' => 'required',
                'remarks' => 'required',
            ];
            $attributes = [
                'atr_type' => 'ATR Type',
                'remarks' => 'Remarks',
            ];
            $messages = [
                'required' => 'The :attribute field is required.',
            ];
            $validator = Validator::make(
                $request->all(),
                $rules,
                $messages,
                $attributes
            );
            if ($validator->passes()) {
                $user_id = AuthChecker::getUserId();
                $scheme_id = $request->scheme_id;
                $designation = Auth::user()->designation_id_old;
                $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
               
                $next_level_role_id = Workflow::getParentId($scheme_id , Auth::user()->designation_id_old);
                $ben_id = $request->ben_id;
                $grievance_id = $request->grievance_id;
                $grievance_mobile_no = $request->grievance_mobile_no;
                $atr_type = $request->atr_type;
                $remarks = $request->remarks;
                $table = $this->getSchemaName($scheme_id);
                $c_time = date('Y-m-d H:i:s', time());
                $ben_details = DB::table($table)->where('id', $ben_id)->where('scheme_id', $scheme_id)->first();
                $benDetails = [];
                $benDetails['sm_flag'] = 1;
                // if($designation == 'Verifier'){
                //     if($ben_details->next_level_role_id == NULL){
                //         $benDetails['next_level_role_id'] = $next_level_role_id;
                //         $benDetails['is_verified'] = 1;
                //         $benDetails['verified_by'] = $user_id;
                //         $benDetails['verification_date'] = $c_time;
                //     }
                // }
                DB::beginTransaction();
                $updateDetails = [];
                $updateDetails['jb_id'] = $ben_id;
                $updateDetails['scheme_id'] = $scheme_id;
                $updateDetails['atr_type'] = $atr_type;
                $updateDetails['remarks'] = $remarks;
                $updateDetails['is_processed'] = 1;
                $updateDetails['marked_by'] = $user_id;
                $updateDetails['marked_date'] = date('Y-m-d H:i:s');
                $updateDetails['jb_next_level_role_id'] = $ben_details->next_level_role_id;
                $is_update = DB::table('cmo.cmo_sm_data')
                    ->where('grievance_id', $grievance_id)
                    // ->where('scheme_id', $scheme_id)
                    // ->where('grievance_mobile', $grievance_mobile_no)
                    ->where('is_processed', 0) //Temporary Code
                    ->where('is_redressed', 0)
                    ->update($updateDetails);

                $ben_update = DB::table($table)
                    ->where('id', $ben_id)
                    ->where('scheme_id', $scheme_id)
                    ->update($benDetails);
                // dump($is_update); dd($ben_update);         
                if ($is_update == 1 && $ben_update == 1) {
                    DB::commit();
                    $response = [
                        'status' => 1,
                        'msg' => 'Beneficiary Marked Successfully',
                        'type' => 'green',
                        'icon' => 'fa fa-check',
                        'title' => 'Success',
                    ];
                } else {
                    DB::rollback();
                    $response = [
                        'status' => 3,
                        'msg' => '3 Somethimg went wrong!!',
                        'type' => 'red',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ];
                }
            } else {
                $return_status = 0;
                $return_msg = $validator->errors()->all();
                $response = [
                    'status' => $return_status,
                    'msg' => $return_msg,
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            }
        } catch (\Exception $e) {
            //    dd($e);
            DB::rollback();
            $response = [
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' =>
                    'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }
    public function transfar(Request $request)
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
            $rules = [
                'atr_type' => 'required',
                'remarks' => 'required',
                'district' => 'required',
                'rural_urban' => 'required',
                'block' => 'required',
            ];
            $attributes = [
                'atr_type' => 'ATR Type',
                'remarks' => 'Remarks',
                'district' => 'District',
                'rural_urban' => 'Rural/Urban',
                'block' => 'Block',
            ];
            $messages = [
                'required' => 'The :attribute field is required.',
            ];
            $validator = Validator::make(
                $request->all(),
                $rules,
                $messages,
                $attributes
            );
            if ($validator->passes()) {
                $user_id = AuthChecker::getUserId();
                $scheme_id = $request->scheme_id;
                $grievance_id = $request->grievance_id;
                $grievance_mobile_no = $request->grievance_mobile_no;
                $atr_type = $request->atr_type;
                $remarks = $request->remarks;
                $district = $request->district;
                $rural_urban = $request->rural_urban;
                $block = $request->block;

                // $table = $this->getSchemaName($scheme_id);
                DB::beginTransaction();
                $is_insert = DB::statement("INSERT INTO cmo.cmo_sm_data_archive(grievance_id, 
             g_dist_code,g_dist_name,g_block_code,g_block_ulb_name,g_gp_ward_no,g_gp_ward_name,g_ps_code,g_ps_name,complain_date,caller_name,complain_address,g_description,grievance_mobile,usbid,lgd_dist,lgd_block,is_processed,atr_type,jb_dist_code,jb_local_body_code,jb_gp_ward_code
            ) (SELECT grievance_id, 
             g_dist_code,g_dist_name,g_block_code,g_block_ulb_name,g_gp_ward_no,g_gp_ward_name,g_ps_code,g_ps_name,complain_date,caller_name,complain_address, g_description, grievance_mobile,usbid,lgd_dist,lgd_block,is_processed,atr_type,jb_dist_code,jb_local_body_code,jb_gp_ward_code
              from cmo.cmo_sm_data where grievance_id='" . $grievance_id . "')");
                $updateDetails = [];
                $updateDetails['atr_type'] = $atr_type;
                $updateDetails['remarks'] = $remarks;
                $updateDetails['jb_dist_code'] = $district;
                $updateDetails['jb_local_body_code'] = $block;
                $updateDetails['is_change_block'] = 1;
                $updateDetails['change_block_by'] = $user_id;
                $updateDetails['change_block_date'] = date('Y-m-d H:i:s');
                $is_update = DB::table('cmo.cmo_sm_data')
                    ->where('grievance_id', $grievance_id)
                    // ->where('scheme_id', $scheme_id)
                    ->where('grievance_mobile', $grievance_mobile_no)
                    ->where('is_processed', 0) //Temporary Code
                    ->update($updateDetails);
                if ($is_update && $is_insert) {
                    DB::commit();
                    $response = [
                        'status' => 1,
                        'msg' => 'Block Transfer Successfully',
                        'type' => 'green',
                        'icon' => 'fa fa-check',
                        'title' => 'Success',
                    ];
                } else {
                    DB::rollback();
                    $response = [
                        'status' => 3,
                        'msg' => '3 Somethimg went wrong!!',
                        'type' => 'red',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ];
                }
            } else {
                $return_status = 0;
                $return_msg = $validator->errors()->all();
                $response = [
                    'status' => $return_status,
                    'msg' => $return_msg,
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            }
        } catch (\Exception $e) {
            //  dd($e);
            DB::rollback();
            $response = [
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' =>
                    'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function opListCmo()
    {
        $user_id = AuthChecker::getUserId();
        $designation = Auth::user()->designation_id_old;
        $mapObj = DB::connection('pgsql_mis')
            ->table('public.duty_assignement')
            ->where('user_id', $user_id)
            ->where('is_active', 1)
            ->first();
        $scheme = DB::connection('pgsql_mis')->select(
            'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
            $user_id .
            ' and is_active=1 and scheme_id=10) order by scheme_name'
        );
        if (AuthChecker::OperatorChecker()) {
            if (count($scheme) > 0) {
                if ($mapObj->is_urban == 1) {
                    $urban_body_code = $mapObj->urban_body_code;
                    $urban_bodys = UrbanBody::where(
                        'sub_district_code',
                        $urban_body_code
                    )
                        ->select('urban_body_code', 'urban_body_name')
                        ->get();
                    return view('cmo-grievance/cmo-op-list', [
                        'schemes' => $scheme,
                        'mapLevel' => $mapObj->mapping_level . $designation,
                        'urban_bodys' => $urban_bodys,
                        'local_body_code' => $urban_body_code,
                        'district_code' => $mapObj->district_code,
                    ]);
                } else {
                    $taluka_code = $mapObj->taluka_code;
                    $gps = GP::where('block_code', $taluka_code)
                        ->select('gram_panchyat_code', 'gram_panchyat_name')
                        ->get();
                    return view('cmo-grievance/cmo-op-list', [
                        'schemes' => $scheme,
                        'mapLevel' => $mapObj->mapping_level . $designation,
                        'gps' => $gps,
                        'local_body_code' => $taluka_code,
                        'district_code' => $mapObj->district_code,
                    ]);
                }
            } else {
                return redirect('/')->with(
                    'success',
                    'User disabled. No scheme assign to this user'
                );
            }
        }
    }
    public function cmoEntryList(Request $request)
    {
        if ($request->ajax()) {
            // dd($request->all());
            $scheme_id = $request->scheme_id;
            $local_body_code = $request->local_body;
            $dist_code = $request->district_code;
            $process_type = $request->process_type;
            $mapLevel = $request->mapLevel;
            $filter_1 = $request->filter_1;
            $filter_2 = $request->filter_2;
            $district_code = $request->district_code;
            if ($mapLevel == 'BlockOperator') {
                $query = " Select * from cmo.cmo_sm_data where is_redress =0 and jb_local_body_code=" . $local_body_code . " and send_to_op=1";
            } else {
                return redirect('/')->with('success', 'UnAuthorized');
            }

            //  dd($query);
            $data = DB::select($query);

            return datatables()
                ->of($data)
                ->addColumn('view', function ($data) use ($scheme_id) {
                    if ($data->is_processed == 0) {
                        // href="CMO-grievance-find?id=' . $data->jb_beneficiary_id  . '&scheme_id=' . $data->scheme_id . '&sm_mobile_no='.$data->sm_mobile_no.'"
                        $action = '<button value="' . $data->grievance_id . '_' . $scheme_id . '_' . $data->grievance_mobile . '" class="btn btn-xs btn-info find_applicant"><i class="glyphicon glyphicon-edit"></i>Find</button>';
                    } else if ($data->is_processed == 1) {
                        $action = 'Verified';
                    } else if ($data->is_processed == 2) {
                        $action = 'Approved';
                    }
                    return $action;
                })
                ->addColumn('grievance_id', function ($data) {
                    return $data->grievance_id;
                })
                ->addColumn('grievance_name', function ($data) {
                    return $data->caller_name;
                })
                ->addColumn('sm_mobile_no', function ($data) {
                    return $data->grievance_mobile;
                })
                ->addColumn('cmo_receive_date', function ($data) {
                    return $data->complain_date;
                })
                ->addColumn('gp_ward_name', function ($data) {
                    return $data->g_gp_ward_name;
                })
                // ->addColumn('description', function ($data) {
                //     return $data->complain_description;
                // })
                ->rawColumns(['view', 'grievance_id', 'grievance_name', 'sm_mobile_no', 'cmo_receive_date', 'g_gp_ward_name'])
                ->make(true);
        }
    }
    public function hodIndex(Request $request)
    {
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        // echo '<pre>'; print_r($roleArray);die();
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = AuthChecker::getUserId();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") AND id = 10 order by rank"));
        // echo '<pre>';print_r($schemes);die();
        if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if (AuthChecker::ApproverChecker()) {
            // echo 1;die();
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                // echo $designation_id_old;die();
                if ($roleObj['scheme_id']) { // == 11 || $roleObj['scheme_id'] == 13
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
                return redirect("/")->with('success', 'User Disabled.. ');
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
        return view(
            'cmo-grievance/hod_linelisting',
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
            ]
        );

    }
    public function hodList(Request $request)
    {
        //   dd($request->all());
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme_code = $request->scheme_code;
        $district = $request->district;
        $operation_type = $request->operation_type;
        $current_status = $request->current_status;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $gp_ward = $request->gp_ward;
        $muncid = $request->muncid;
        $table = $this->getSchemaName($scheme_code);
        if ($request->ajax()) {
            if (AuthChecker::HODChecker()) {
                $query = '';
                if (!empty($district)) {
                    if ($district == '100') {
                        $query .= "SELECT * FROM cmo.cmo_sm_data cmo  join $table ben on cmo.jb_id=ben.id  AND cmo.is_processed = " . $operation_type . " AND cmo.scheme_id = " . $scheme_code . "  AND cmo.jb_dist_code is null and ben.sm_flag = 1";
                    } else {
                        $query .= "SELECT * FROM cmo.cmo_sm_data cmo  join $table ben on cmo.jb_id=ben.id and cmo.jb_dist_code = " . $district . " AND cmo.is_processed = " . $operation_type . " AND cmo.scheme_id = " . $scheme_code . " and ben.sm_flag = 1";
                    }
                } else {
                    $query .= "SELECT * FROM cmo.cmo_sm_data cmo  join $table ben on cmo.jb_id=ben.id AND cmo.is_processed = " . $operation_type . " AND cmo.scheme_id = " . $scheme_code . " and ben.sm_flag = 1";
                }
                if (!empty($current_status)) {
                    if ($current_status == 1) {
                        $query .= " and ben.next_level_role_id = 0";
                    } elseif ($current_status == 2) {
                        $query .= " and ben.next_level_role_id is null";
                    } elseif ($current_status == 3) {
                        $query .= " and ben.next_level_role_id > 0 ";
                    } elseif ($current_status == 4) {
                        $query .= " and ben.next_level_role_id < 0 ";
                    }
                }
                //  dd($query);
                $data = DB::connection('pgsql')->select($query);
                $districts = District::where('district_code', $district)->first();
                return datatables()->of($data)
                    ->addIndexColumn()
                    ->addColumn('name', function ($data) {
                        return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                    })
                    ->addColumn('address', function ($data) use ($districts) {
                        $address = '';
                        if (!empty($district)) {
                            $address = 'District - ' . $districts->district_name . '<br>';
                        }
                        if ($data->rural_urban_id == 1) {
                            $address .= 'Municipality - ' . $data->block_ulb_name . '<br>';
                            $address .= 'Ward - ' . $data->gp_ward_name;
                        } else {
                            $address .= 'Block - ' . $data->block_ulb_name . '<br>';
                            $address .= 'GP - ' . $data->gp_ward_name;
                        }
                        return $address;
                    })
                    ->addColumn('cmo_address', function ($data) {
                        $cmo_address = '';
                        $cmo_address .= 'Block/Municipality - ' . $data->block_ulb_name . '<br>';
                        $cmo_address .= 'GP/Ward - ' . $data->gp_ward_name;
                        return $cmo_address;
                    })
                    ->addColumn('action', function ($data) use ($scheme_code) {
                        $action = '<button class="btn btn-primary btn-xs ben_view_button" value="' . $data->grievance_id . '_' . $scheme_code . '"><i class="glyphicon glyphicon-edit"></i>View</button>';
                        if ($data->is_processed == 2) {
                            $action = '<b>Pushed to CMO</b>';
                        }
                        return $action;
                    })
                    ->addColumn('current_status', function ($data) {
                        $html = '';
                        if ($data->next_level_role_id == 0) {
                            $html = '<span class="text-dafault"><b>Approved</b></span>';
                        } else if ($data->next_level_role_id > 0) {
                            $html = '<span class="text-dafault"><b>Verified</b></span>';
                        } else if ($data->next_level_role_id = NULL) {
                            $html = '<span class="text-dafault"><b>Non Verified</b></span>';
                        } else if ($data->next_level_role_id < 0) {
                            $html = '<span class="text-dafault"><b>Rejected</b></span>';
                        }
                        return $html;
                    })
                    ->addColumn('check', function ($data) use ($scheme_code) {
                        if ($data->is_processed == 1) {
                            return '<input type="checkbox"  name="chkbx" class="all_checkbox"  onclick="controlCheckBox();" value="' . $data->grievance_id . '_' . $scheme_code . '">';
                        } else {
                            return '';
                        }
                    })
                    ->rawColumns(['action', 'name', 'address', 'cmo_address', 'current_status', 'check'])
                    ->make(true);
            }
        }
    }
    public function hodView(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $grievance_id = $request->grievance_id;
            $scheme_id = $request->scheme_id;
            $table = $this->getSchemaName($scheme_id);
            $query = '';
            $query .= "SELECT * FROM cmo.cmo_sm_data cmo  join $table ben on cmo.jb_id=ben.id AND cmo.is_processed = 1 AND cmo.grievance_id= '" . $grievance_id . "' AND cmo.scheme_id = " . $scheme_id . "  and ben.sm_flag = 1";
            $data = DB::connection('pgsql')->select($query);
            // dd($data);
            $districts = District::where('district_code', $data[0]->jb_dist_code)->first();
            $atr = DB::connection('pgsql_mis')->select(
                "select  atr_code,atr_desc from public.m_cmo_atr where atr_code = '" . $data[0]->atr_type . "' order by atr_code,atr_desc"
            );
            if ($data == NULL) {
                return $response = [
                    'status' => 3,
                    'msg' => 'Somethimg went wrong.',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            } else {
                $ben_arr = [
                    'grievance_id' => trim($data[0]->grievance_id),
                    'grievance_mobile' => trim($data[0]->grievance_mobile),
                    'cmo_dist_name' => trim($data[0]->g_dist_name),
                    'cmo_block_name' => trim($data[0]->g_block_ulb_name),
                    'cmo_gp_ward_name' => trim($data[0]->g_gp_ward_name),
                    'cmo_age' => trim($data[0]->g_age),
                    'complain_date' => trim($data[0]->complain_date),
                    'caller_name' => trim($data[0]->caller_name),
                    'complain_description' => trim($data[0]->g_description),
                    'atr' => trim($atr[0]->atr_desc),
                    'remarks' => trim($data[0]->remarks),
                    'jb_id' => $data[0]->id,
                    'jb_name' => $data[0]->ben_fname . ' ' . $data[0]->ben_mname . ' ' . $data[0]->ben_lname,
                    'jb_mobile' => trim($data[0]->mobile_no),
                    'jb_caste' => trim($data[0]->caste),
                    'jb_dist_name' => trim($districts->district_name),
                    'jb_block_ulb_name' => trim($data[0]->block_ulb_name),
                    'next_level_role_id' => (trim($data[0]->next_level_role_id) == 0 ? 'Approved' : (trim($data[0]->next_level_role_id) > 0 ? 'Verified' : (trim($data[0]->next_level_role_id) == null ? 'Non Verified' : 'Rejected')))
                ];
                $response = array_merge($ben_arr, [
                    'status' => 2,
                ]);
            }

        } catch (\Exception $e) {
            //throw $th;
            // dd($e);
            $response = [
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' =>
                    'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }
    public function sendBackToCmo(Request $request)
    {
        //    dd($request->all());
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        $user_id = AuthChecker::getUserId();
        $dutyObj = Configduty::where('user_id', '=', $user_id)
            ->where('is_active', 1)
            ->first();
        $opreation_type = $request->opreation_type;
        $accept_reject_comments = $request->accept_reject_comments;
        $scheme_id = $request->scheme_id;
        $is_bulk = $request->is_bulk;
        $grievance_id = $request->grievance_id;
        if ($is_bulk == 0) {
            if ($opreation_type == 'A') {
                try {
                    $legacy_validation_update = DB::connection('pgsql')->table('cmo.cmo_sm_data')->where('grievance_id', $grievance_id)->where('scheme_id', $scheme_id)->where('is_processed', 1)->first();
                    // dd($legacy_validation_update);
                    if ($legacy_validation_update == NULL) {
                        return $response = [
                            'status' => 3,
                            'msg' => 'Somethimg went wrong.',
                            'type' => 'red',
                            'icon' => 'fa fa-warning',
                            'title' => 'Warning!!',
                        ];
                    } else {
                        $data = [
                            'Griev_ID' => trim($grievance_id),
                            'atr_date' => $legacy_validation_update->marked_date,
                            'benificiary_id' => (string) $legacy_validation_update->jb_id,
                            'ATR_descriptions' => trim($legacy_validation_update->remarks),
                            'Action_Taken_Code' => trim($legacy_validation_update->atr_type),
                        ];
                        // dd($data);
                        $data_array[] = $data;
                        $header = array(
                            "typ" => "JWT",
                            "alg" => "HS512"

                        );
                        $formattedDate = 'y' . date('Y') . 'm' . date('m') . 'd' . date('d');
                        $payload = array(
                            "username" => "cmo",
                            "password" => $formattedDate
                        );
                        $secret_key = 'CMO@2023';
                        $token = JWTToken::getJWTToken($header, $payload, $secret_key);
                        // dd($token);
                        $post_url = 'http://172.25.140.14:9091/wcd_get_atr';
                        $headers = array(
                            'Content-Type: application/json'
                        );
                        $api_payload = array(
                            "token" => $token,
                            "data" => $data_array
                        );
                        $api_response = APICurl::callingAPI($post_url, $headers, json_encode($api_payload));
                        //  dd($api_response);
                        $updateBenDetails = [];
                        $updateBenDetails['is_processed'] = 2;
                        $updateBenDetails['response_back_by'] = $user_id;
                        $updateBenDetails['response_back_date'] = date('Y-m-d H:i:s');
                        DB::beginTransaction();
                        if (!empty($api_response['result'])) {
                            $cleanResult = trim($api_response['result']);
                            $cleanResult = str_replace(["\n", "\r"], "", $cleanResult);
                            $decodedResult = json_decode($cleanResult, true);
                            if (isset($decodedResult['status']) && $decodedResult['status'] == 'All data successfully received.') {
                                $is_update = DB::table('cmo.cmo_sm_data')
                                    ->where('grievance_id', $grievance_id)
                                    ->where('scheme_id', $scheme_id)
                                    ->where('is_processed', 1) //Temporary Code
                                    ->update($updateBenDetails);
                                if ($is_update) {
                                    DB::commit();
                                    $response = [
                                        'status' => 1,
                                        'msg' => 'ATR Response Back To CMO Successfully',
                                        'type' => 'green',
                                        'icon' => 'fa fa-check',
                                        'title' => 'Success',
                                    ];
                                } else {
                                    DB::rollback();
                                    $response = [
                                        'status' => 3,
                                        'msg' => 'Somethimg went wrong!!',
                                        'type' => 'red',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];
                                }
                            } else {
                                DB::rollback();
                                $response = [
                                    'status' => 3,
                                    'msg' => 'Somethimg went wrong. Please try again!!',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        } else {
                            DB::rollback();
                            $response = [
                                'status' => 3,
                                'msg' => 'API Calling Problem. Please try again!!',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    //  dd($e);
                    $response = [
                        'exception' => true,
                        // 'exception_message' => $e->getMessage(),
                        'exception_message' =>
                            'Something went wrong. May be session time out logout and login again.',
                    ];
                    $statusCode = 400;
                } finally {
                    return response()->json($response, $statusCode);
                }
            }
        }
        if ($is_bulk == 1) {
            if ($opreation_type == 'A') {
                $applicantId = $request->applicantId;
                $bulk_id_arr = explode(',', $applicantId);
                try {
                    $grievance_array = array();
                    foreach ($bulk_id_arr as $key => $value) {
                        $bulk_single_id_arr = explode('_', $value);
                        $griv_id = $bulk_single_id_arr[0];
                        $grievance_array[] = $griv_id;
                        $scheme_id = $bulk_single_id_arr[1];
                        $legacy_validation_update = DB::connection('pgsql')->table('cmo.cmo_sm_data')->where('grievance_id', $griv_id)->where('scheme_id', $scheme_id)->where('is_processed', 1)->first();
                        if ($legacy_validation_update == NULL) {
                            return $response = [
                                'status' => 3,
                                'msg' => 'Somethimg went wrong.',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        } else {
                            $data = [
                                'Griev_ID' => trim($griv_id),
                                'atr_date' => $legacy_validation_update->marked_date,
                                'benificiary_id' => (string) $legacy_validation_update->jb_id,
                                'ATR_descriptions' => trim($legacy_validation_update->remarks),
                                'Action_Taken_Code' => trim($legacy_validation_update->atr_type),
                            ];
                            $data_array[] = $data;
                        }
                    }
                    // dd($p);
                    $header = array(
                        "typ" => "JWT",
                        "alg" => "HS512"

                    );
                    $formattedDate = 'y' . date('Y') . 'm' . date('m') . 'd' . date('d');
                    $payload = array(
                        "username" => "cmo",
                        "password" => $formattedDate
                    );
                    $secret_key = 'CMO@2023';
                    $token = JWTToken::getJWTToken($header, $payload, $secret_key);
                    $post_url = 'http://172.25.140.14:9091/wcd_get_atr';
                    $headers = array(
                        'Content-Type: application/json'
                    );
                    $api_payload = array(
                        "token" => $token,
                        "data" => $data_array
                    );
                    $api_response = APICurl::callingAPI($post_url, $headers, json_encode($api_payload));
                    DB::beginTransaction();
                    if (!empty($api_response['result'])) {
                        $updateBenDetails = [];
                        $cleanResult = trim($api_response['result']);
                        $cleanResult = str_replace(["\n", "\r"], "", $cleanResult);
                        $decodedResult = json_decode($cleanResult, true);
                        if (isset($decodedResult['status']) && $decodedResult['status'] == 'All data successfully received.') {
                            $updateBenDetails['is_processed'] = 2;
                            $updateBenDetails['response_back_by'] = $user_id;
                            $updateBenDetails['response_back_date'] = date('Y-m-d H:i:s');
                            $is_update = DB::table('cmo.cmo_sm_data')
                                ->whereIn('grievance_id', $grievance_array)
                                ->where('scheme_id', $scheme_id)
                                ->where('is_processed', 1) //Temporary Code
                                ->update($updateBenDetails);
                            if ($is_update) {
                                DB::commit();
                                $response = [
                                    'status' => 1,
                                    'msg' => 'ATR Response Back To CMO Successfully',
                                    'type' => 'green',
                                    'icon' => 'fa fa-check',
                                    'title' => 'Success',
                                ];
                            } else {
                                DB::rollback();
                                $response = [
                                    'status' => 3,
                                    'msg' => 'Somethimg went wrong!!',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        } else {
                            DB::rollback();
                            $response = [
                                'status' => 3,
                                'msg' => 'Somethimg went wrong. Please try again!!',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }
                    } else {
                        DB::rollback();
                        $response = [
                            'status' => 3,
                            'msg' => 'API Calling Problem. Please try again!!',
                            'type' => 'red',
                            'icon' => 'fa fa-warning',
                            'title' => 'Warning!!',
                        ];
                    }
                } catch (\Exception $e) {
                    // dd($e);
                    $response = [
                        'exception' => true,
                        // 'exception_message' => $e->getMessage(),
                        'exception_message' =>
                            'Something went wrong. May be session time out logout and login again.',
                    ];
                    $statusCode = 400;
                } finally {
                    return response()->json($response, $statusCode);
                }
            }
        }
    }
    public function callbackapi()
    {

        ini_set('memory_limit', '-1');
        $header = array(
            "typ" => "JWT",
            "alg" => "HS512"
        );
        $formattedDate = 'y' . date('Y') . 'm' . date('m') . 'd' . date('d');
        $payload = array(
            "username" => "cmo",
            "password" => $formattedDate
        );
        $secret_key = 'CMO@2023';
        $token = JWTToken::getJWTToken($header, $payload, $secret_key);
        $post_url = 'http://172.25.140.14:9091/wcd_push_api';
        $headers = array(
            'Content-Type: application/json'
        );
        $data = array("token" => $token);
        $data_string = json_encode($data);
        $api_response = APICurl::cmoFetchCurl($post_url, $data_string);
        // dd($api_response);
        if ($api_response['errorCurl'] == '') {
            $decodedJson = json_decode($api_response['result'], true);
            $dataArray = json_decode($decodedJson['data'], true);


            DB::beginTransaction();
            $insert = DB::table('cmo.cmo_response_json')->insert(['fetch_request_token' => $token, 'received_data' => json_encode($dataArray)]);
            if ($insert) {
                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Applications Updated Successfully',
                ]);
            } else {
                DB::rollback();
                return response()->json([
                    'status' => 200,
                    'errors' => 'No record found',
                ]);
            }
        } else {
            dd($api_response['errorCurl']);
        }
    }
    public function cmoReport(Request $request)
    {
        $this->middleware('auth');
        $base_date = '2020-01-01';
        date_default_timezone_set('Asia/Kolkata');
        $c_time = Carbon::now();
        $c_date = $c_time->format("Y-m-d");
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        $designation_id_old = AuthChecker::getDesignationId();
        $userId = AuthChecker::getUserId();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        if (AuthChecker::AdminChecker() || AuthChecker::HODChecker() || AuthChecker::HOPChecker()) {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
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
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 and id=10 order by scheme_name"));
        //dd($scheme_list);
        return view(
            'cmo-grievance.cmo-mis',
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
    public function getReport(Request $request)
    {
        //  dd($request->all());
        $scheme_id = $request->scheme_id;
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        $gp_ward = $request->gp_ward;
        // dd($gp_ward);
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
            $user_msg = "CMO Grievance Report For Scheme Old Age Pension";
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
                    $data = $this->getWardWise($district, $block, $muncid, $gp_ward, $from_date, $to_date);
                } else {
                    $column = "GP";
                    $heading_msg = $user_msg . ' of the GP ' . $gp_ward_name;
                    $data = $this->getGpWise($district, $block, NULL, $gp_ward, $from_date, $to_date);
                }
            } else if (!empty($muncid)) {
                $column = "Ward";
                $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
                $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
                $data = $this->getWardWise($district, $block, $muncid, NULL, $from_date, $to_date);
            } else if (!empty($block)) {
                if ($urban_code == 1) {
                    $column = "Municipality";
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getMuncWise($district, $block, NULL, NULL, $from_date, $to_date);
                } else if ($urban_code == 2) {
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getGpWise($district, $block, NULL, $gp_ward, $from_date, $to_date);
                    $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                    $column = "Block";
                    $data = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date);
                }
            } else {

                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date);
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date);
                        $data2 = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date);
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
        $query = "select A.location_id,A.location_name,
        COALESCE(cmo.total_grievance,0) as total_grievance, 
        COALESCE(cmo.total_verified,0) as total_verified, 
        COALESCE(cmo.total_redressed,0) as total_redressed,
        COALESCE(cmo.total_grievance_back,0) as total_grievance_back
        from(
        select district_code as location_id,district_name as location_name
         from public.m_district ) as A  
        LEFT JOIN
        (select  count(1)  as total_grievance,
	    count(1) filter(where is_processed = 1) as total_verified,
	    count(1) filter(where is_redressed = 1) as total_redressed,
	    count(1) filter(where is_processed = 2) as total_grievance_back,
	    jb_dist_code 
        from cmo.cmo_sm_data group by jb_dist_code) as cmo ON A.location_id=cmo.jb_dist_code

        UNION ALL

        SELECT
        '999' AS location_id,
        'Not Available' AS location_name,
        COUNT(1) AS total_grievance,
        COUNT(1) FILTER (WHERE is_processed = 1) AS total_verified,
        COUNT(1) FILTER (WHERE is_redressed = 1) AS total_redressed,
        COUNT(1) FILTER (WHERE is_processed = 2) AS total_grievance_back
        FROM
        cmo.cmo_sm_data
        WHERE
        jb_dist_code is null
ORDER BY location_id,location_name;";

        //  echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
    public function getSubDivWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL)
    {
        $whereMain = "where  district_code=" . $district_code;
        $query = "select 	A.location_id,
    	A.location_name,
		COALESCE(cmo.total_grievance, 0) AS total_grievance, 
		COALESCE(cmo.total_verified, 0) AS total_verified, 
		COALESCE(cmo.total_redressed, 0) AS total_redressed,
		COALESCE(cmo.total_grievance_back, 0) AS total_grievance_back
        from(
            select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
            from public.m_sub_district " . $whereMain . "
         )
         as A  
        LEFT JOIN
        (select 
            COUNT(1) AS total_grievance,
            COUNT(1) FILTER (WHERE is_processed = 1) AS total_verified,
            COUNT(1) FILTER (WHERE is_redressed = 1) AS total_redressed,
            COUNT(1) FILTER (WHERE is_processed = 2) AS total_grievance_back,
            jb_local_body_code
            from cmo.cmo_sm_data  where  jb_dist_code= " . $district_code . " and btype in('M','C')
            group by jb_local_body_code) as cmo ON A.location_id=cmo.jb_local_body_code
		 UNION ALL
		 SELECT
            '999999' AS location_id,
            'Not Available Subdiv' AS location_name,
            COUNT(1) AS total_grievance,
            COUNT(1) FILTER (WHERE is_processed = 1) AS total_verified,
            COUNT(1) FILTER (WHERE is_redressed = 1) AS total_redressed,      
            COUNT(1) FILTER (WHERE is_processed = 2) AS total_grievance_back
        FROM
        cmo.cmo_sm_data
        WHERE
        jb_dist_code= " . $district_code . " and jb_local_body_code is null and btype in('M','C')
        ORDER BY location_id,location_name;";
        // dd($query);
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
    public function getBlockWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL)
    {
        //  dd('ok2');
        $whereMain = "where  district_code=" . $district_code;
        $query = "select 
		A.location_id,
    	A.location_name,
		COALESCE(cmo.total_grievance, 0) AS total_grievance, 
		COALESCE(cmo.total_verified, 0) AS total_verified, 
		COALESCE(cmo.total_redressed, 0) AS total_redressed,
		COALESCE(cmo.total_grievance_back, 0) AS total_grievance_back
        from(
            select block_code as location_id,'Block-'||block_name as location_name
           from public.m_block " . $whereMain . "
         )
         as A  
        LEFT JOIN
        (select   
                   COUNT(1) AS total_grievance,
					COUNT(1) FILTER (WHERE is_processed = 1) AS total_verified,
					COUNT(1) FILTER (WHERE is_redressed = 1) AS total_redressed,
					COUNT(1) FILTER (WHERE is_processed = 2) AS total_grievance_back,
                    jb_local_body_code
                    from cmo.cmo_sm_data where jb_dist_code= " . $district_code . " and btype in('B')
         group by jb_local_body_code) as cmo ON A.location_id=cmo.jb_local_body_code
		 
		 UNION ALL
		 
		 SELECT
		'999999' AS location_id,
		'Not Available Block' AS location_name,
		COUNT(1) AS total_grievance,
		COUNT(1) FILTER (WHERE is_processed = 1) AS total_verified,
		COUNT(1) FILTER (WHERE is_redressed = 1) AS total_redressed,
		COUNT(1) FILTER (WHERE is_processed = 2) AS total_grievance_back
	FROM
    cmo.cmo_sm_data
WHERE
    jb_dist_code= " . $district_code . " and jb_local_body_code is null and btype in('B')
ORDER BY location_id,location_name;";
        // dd($query);
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
}

