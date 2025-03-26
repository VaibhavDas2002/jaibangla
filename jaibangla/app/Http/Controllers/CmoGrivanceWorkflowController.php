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
use App\Traits\TraitCMOValidate;
use Illuminate\Support\Facades\Route;

class CmoGrivanceWorkflowController extends Controller
{
    use TraitCMOValidate;
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
    }


    public function SchemeSelect(Request $request)
    {
        $auth = AuthChecker::VerifierPermission();
        if ($auth) {
            $schemes = Scheme::whereIn('id', [2, 10, 11])->where('is_active', 1)->orderBy('id')->get();
            return view('cmo-grievance/scheme_selectionCMO', ['schemes' => $schemes]);
        } else {
            return redirect('/')->with('error', 'User Disabled. ');
        }
    }
    public function index(Request $request)
    {
        $scheme_id = $request->scheme_id;
        $cmoCheck = PermissionManagement::CmoCheck($scheme_id);
        // $cmoCheck =1;
        if ($cmoCheck) {
            $user_id = AuthChecker::getUserId();
            $is_verifer = AuthChecker::VerifierPermission();
            $is_approver = AuthChecker::ApproverPermission();
            $is_hod = AuthChecker::HODChecker();

            $mapObj = DB::connection('pgsql_mis')
                ->table('public.duty_assignement')
                ->where('user_id', $user_id)
                ->where('is_active', 1)
                ->first();
            $scheme = DB::connection('pgsql_mis')->select(
                'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
                $user_id .
                ' and is_active=1 and scheme_id in(2,10,11)) order by scheme_name'
            );
            $scheme_name = Scheme::where('id', $scheme_id)->value('scheme_name');
            if (AuthChecker::VerifierPermission()) {
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
            } else if (AuthChecker::ApproverPermission()) {
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
        } else {
            return redirect('cmo-scheme-selection')->with('success', 'Scheme is Not Allowed for CMO - Grievance');
        }
    }
    public function listing(Request $request)
    {
        if ($request->ajax()) {
            //  dd($request->all());
            $scheme_id = $request->scheme_id;
            $local_body_code = $request->local_body;
            $dist_code = $request->district_code;
            $process_type = $request->process_type;
            $mapLevel = $request->mapLevel;
            $filter_1 = $request->filter_1;
            $filter_2 = $request->filter_2;
            $district_code = $request->district_code;
            if ($mapLevel == 'Block') {
               
                $query = "Select * from cmo.cmo_sm_data where  scheme_id= " . $scheme_id . " and lgd_block='" . $local_body_code . "'";
            
            } elseif ($mapLevel == 'Subdiv') {
               $munlist=UrbanBody::where('sub_district_code', $local_body_code)->get()->toArray();
            //    dd($munlist);
               $munlist_ids = array_column($munlist, 'urban_body_code');
                $query = "Select * from cmo.cmo_sm_data where  lgd_muni IN ('" . implode("','", $munlist_ids) . "') and scheme_id= " . $scheme_id ;
            
             } elseif ($mapLevel == 'District') {
                $query = " Select * from cmo.cmo_sm_data where  scheme_id= " . $scheme_id . " and lgd_dist='" . $district_code . "' and lgd_block is null and lgd_muni is null";
            } elseif ($mapLevel == 'Department') {
                $query = " Select * from cmo.cmo_sm_data where  scheme_id= " . $scheme_id . " and lgd_dist is null";
            } else {
                return redirect('/')->with('success', 'UnAuthorized');
            }
            if ($process_type == 1) {
                $query .= " and is_processed = 0 and is_redressed = 0";
            } else if ($process_type == 2) {
                $query .= " and is_processed = 1 and is_redressed = 0 and is_mark = 1 ";
            } else if ($process_type == 3) {
                $query .= " and is_processed = 2 and is_redressed = 0";
            } else if ($process_type == 4) {
                $query .= " and is_processed = 2 and is_redressed = 1";
            } else if ($process_type == 5) {
                $query .= " and is_change_block = 1 and is_processed = 0";
            }
            $data = DB::select($query);
            // dd($data);
            return datatables()
                ->of($data)
                ->addColumn('view', function ($data) use ($scheme_id) {
                    $action = '';
                    if ($data->is_processed == 0) {
                        // href="CMO-grievance-find?id=' . $data->jb_beneficiary_id  . '&scheme_id=' . $data->scheme_id . '&sm_mobile_no='.$data->sm_mobile_no.'"
                        $action = '<button value="' . $data->grievance_id . '_' . $scheme_id . '_' . $data->pri_cont_no . '" class="btn btn-xs btn-info find_applicant"><i class="glyphicon glyphicon-edit"></i>Find</button>';
                    } else if ($data->is_processed == 1 && $data->is_redressed == 0 && $data->is_mark == 1) {
                        $action = 'Verifier Marked';
                    } else if ($data->is_processed == 2 && $data->is_redressed == 0) {
                        $action = 'Marked & Send back to CMO';
                    } else if ($data->is_processed == 2 && $data->is_redressed == 1) {
                        $action = 'Redressed';
                    }
                    return $action;
                })
                ->addColumn('grievance_id', function ($data) {
                    return trim($data->grievance_id);
                })
                ->addColumn('grievance_no', function ($data) {
                    return trim($data->grievance_no);
                })
                ->addColumn('grievance_name', function ($data) {
                    return trim($data->applicant_name);
                })
                ->addColumn('sm_mobile_no', function ($data) {
                    return $data->pri_cont_no;
                })
                ->addColumn('cmo_receive_date', function ($data) {
                    return Carbon::parse($data->grievance_generate_date)->toDateString();
                })
                // ->addColumn('gp_ward_name', function ($data) {
                //     if(!empty($data->gp_id)){
                //         $gp_ward_name = DB::connection('pgsql_mis')
                //         ->table('cmo.m_gp_ward')
                //         ->where('gp_ward_code', $data->gp_id)
                //         ->first();
                //         return $gp_ward_name->gp_ward_name;
                //     }else if(!empty($data->ward_id)){
                //         $gp_ward_name = DB::connection('pgsql_mis')
                //         ->table('cmo.m_gp_ward')
                //         ->where('gp_ward_code', $data->ward_id)
                //         ->first();
                //         return $gp_ward_name->gp_ward_name;
                //     }else{
                //         return 'Not Available';
                //     }
                // })
                // ->addColumn('description', function ($data) {
                //     return $data->complain_description;
                // })
                ->rawColumns(['view', 'grievance_id', 'grievance_no', 'grievance_name', 'sm_mobile_no', 'cmo_receive_date'])
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
        $row = DB::connection('pgsql_mis')->table('cmo.cmo_sm_data')->where('grievance_id', $grievance_id)->where('scheme_id', $scheme_id)->where('pri_cont_no', $grievance_mobile_no)->first();
        $atr = DB::connection('pgsql_mis')->select(
            'select atn_id,atr_desc from cmo.m_cmo_atr order by atn_id,atr_desc'
        );
        if (AuthChecker::VerifierPermission() || AuthChecker::ApproverPermission() || AuthChecker::HODChecker()) {
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
                $atr = DB::connection('pgsql_mis')->select(
                    "select  atn_id,atr_desc from cmo.m_cmo_atr where atn_id = '" . $atr_type . "'"
                );
                DB::beginTransaction();
                $updateDetails = [];
                $updateDetails['is_redressed'] = 1;
                $updateDetails['is_processed'] = 1;
                $updateDetails['atr_type'] = $atr_type;
                $updateDetails['atr_desc'] = trim($atr[0]->atr_desc);
                $updateDetails['redressed_by'] = $user_id;
                $updateDetails['redressed_date'] = date('Y-m-d H:i:s');
                $updateDetails['remarks'] = $remarks;
                // dd($updateDetails);
                $is_update = DB::table('cmo.cmo_sm_data')
                    ->where('grievance_id', $grievance_id)
                    ->where('pri_cont_no', $grievance_mobile_no)
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
            $designation = Auth::user()->designation_id;
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
            if (AuthChecker::VerifierPermission()) {
                if ($mapObj->is_urban == 1) {
                    $local_body_code = $mapObj->urban_body_code;
                } else {
                    $local_body_code = $mapObj->taluka_code;
                }
            } else if (AuthChecker::ApproverPermission()) {
                $district_code = $mapObj->district_code;
            }
            if ($new_process_id == 5) {
                $input_value = strtolower(str_replace(' ', '', $input_value));
            }
            //   dd($input_value);
            // $table_name = $this->getSchemaName($scheme_id);
            $table_name = 'pension.beneficiaries';
            $query = "Select b.*,md.district_name, bl_div.block_subdiv_name,ms.scheme_name from $table_name b join public.m_district md ON md.district_code=b.created_by_dist_code 
                JOIN public.m_scheme ms ON ms.id=b.scheme_id 
                JOIN (SELECT block_code AS block_subdiv_code,block_name AS block_subdiv_name FROM public.m_block 
	  			    UNION ALL
      		    SELECT sub_district_code AS block_subdiv_code, sub_district_name AS block_subdiv_name FROM 	public.m_sub_district
      	        ) bl_div ON bl_div.block_subdiv_code=b.created_by_local_body_code 
                where b.scheme_id=" . $scheme_id . "";
            if (AuthChecker::VerifierPermission()) {
                $query .= " and b.created_by_local_body_code=" . $local_body_code . "";
            }
            if (AuthChecker::ApproverPermission()) {
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
                    if ($data->next_level_role_id == 0 && $data->is_approved == 1) {
                        $action = '<b>Approved</b>';
                    } else if ($data->is_verified == 1 && $data->is_approved == 0) {
                        $action = '<b>Verified</b>';
                    } else if ($data->next_level_role_id < 0 && $data->is_rejected == 1) {
                        $action = '<b>Rejected</b>';
                    } else if ($data->is_verified == 0 && $data->is_approved == 0 && $data->is_rejected == 0) {
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
                $designation = Auth::user()->designation_id;
                $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
                if ($duty_obj->mapping_level == "Department") {
                    $created_by_local_body_code = NULL;
                    $created_by_dist_code = NULL;
                } else {
                    $created_by_dist_code = $duty_obj->district_code;
                    if ($duty_obj->mapping_level == "Subdiv") {
                        $created_by_local_body_code = $duty_obj->urban_body_code;
                    } else if ($duty_obj->mapping_level == "Block") {
                        $created_by_local_body_code = $duty_obj->taluka_code;
                    } else if ($duty_obj->mapping_level == "District") {
                        $created_by_local_body_code = NULL;
                    }
                }

                $next_level_role_id = Workflow::getParentId($scheme_id, Auth::user()->designation_id);
                $ben_id = $request->ben_id;
                $grievance_id = $request->grievance_id;
                $grievance_mobile_no = $request->grievance_mobile_no;
                $atr_type = $request->atr_type;
                $remarks = $request->remarks;
                // $table = $this->getSchemaName($scheme_id);
                $table = 'pension.beneficiaries';
                $c_time = date('Y-m-d H:i:s', time());
                $ben_details = DB::table($table)->where('id', $ben_id)->where('scheme_id', $scheme_id)->first();
                $benDetails = [];
                // $benDetails['sm_flag'] = 1;
                $benDetails['cmo_mark'] = 1;
                // if($designation == 'Verifier'){
                //     if($ben_details->next_level_role_id == NULL){
                //         $benDetails['next_level_role_id'] = $next_level_role_id;
                //         $benDetails['is_verified'] = 1;
                //         $benDetails['verified_by'] = $user_id;
                //         $benDetails['verification_date'] = $c_time;
                //     }
                // }
                $atr = DB::connection('pgsql_mis')->select(
                    "select  atn_id,atr_desc from cmo.m_cmo_atr where atn_id = '" . $atr_type . "'"
                );
                DB::beginTransaction();
                $updateDetails = [];
                $updateDetails['jb_id'] = $ben_id;
                $updateDetails['scheme_id'] = $scheme_id;
                $updateDetails['atr_type'] = $atr_type;
                $updateDetails['atr_desc'] = trim($atr[0]->atr_desc);
                $updateDetails['remarks'] = $remarks;
                $updateDetails['is_processed'] = 1;
                $updateDetails['is_mark'] =1 ; 
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



                $accept_reject_model = new AcceptRejectInfo;
                $accept_reject_model->created_at = $c_time;
                $accept_reject_model->application_id = $ben_id;
                $accept_reject_model->scheme_id = $scheme_id;
                $accept_reject_model->user_id = $user_id;
                $accept_reject_model->comment_message = $remarks;
                $accept_reject_model->user_id = $user_id;
                $accept_reject_model->created_by_dist_code = $created_by_dist_code;
                $accept_reject_model->created_by_local_body_code = $created_by_local_body_code;
                $accept_reject_model->ip_address = request()->ip();
                $accept_reject_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod() . 'AV';
                $accept_reject_model->op_type = 'SMATAG';
                $is_saved_log = $accept_reject_model->save();
                // dump($is_update); dd($ben_update);         
                if ($is_update == 1 && $ben_update == 1 && $is_saved_log == 1) {
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




    public function sendOperator(Request $request)
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
                $designation = Auth::user()->designation_id;
                $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
                if ($duty_obj->mapping_level == "Department") {
                    $created_by_local_body_code = NULL;
                    $created_by_dist_code = NULL;
                } else {
                    $created_by_dist_code = $duty_obj->district_code;
                    if ($duty_obj->mapping_level == "Subdiv") {
                        $created_by_local_body_code = $duty_obj->urban_body_code;
                    } else if ($duty_obj->mapping_level == "Block") {
                        $created_by_local_body_code = $duty_obj->taluka_code;
                    } else if ($duty_obj->mapping_level == "District") {
                        $created_by_local_body_code = NULL;
                    }
                }

                // $next_level_role_id = Workflow::getParentId($scheme_id, Auth::user()->designation_id);
                // $ben_id = $request->ben_id;
                $scheme_id = $request->scheme_id;
                $grievance_id = $request->grievance_id;
                $grievance_mobile_no = $request->grievance_mobile_no;
                $atr_type = $request->atr_type;
                $remarks = $request->remarks;
                // $table = $this->getSchemaName($scheme_id);
                $table = 'pension.beneficiaries';
                $c_time = date('Y-m-d H:i:s', time());
                // $ben_details = DB::table($table)->where('id', $ben_id)->where('scheme_id', $scheme_id)->first();
                // $benDetails = [];
                // // $benDetails['sm_flag'] = 1;
                // $benDetails['cmo_mark'] = 1;
                // if($designation == 'Verifier'){
                //     if($ben_details->next_level_role_id == NULL){
                //         $benDetails['next_level_role_id'] = $next_level_role_id;
                //         $benDetails['is_verified'] = 1;
                //         $benDetails['verified_by'] = $user_id;
                //         $benDetails['verification_date'] = $c_time;
                //     }
                // }
                $atr = DB::connection('pgsql_mis')->select(
                    "select  atn_id,atr_desc from cmo.m_cmo_atr where atn_id = '" . $atr_type . "'"
                );
                DB::beginTransaction();
                $updateDetails = [];
                // $updateDetails['jb_id'] = $ben_id;
                $updateDetails['scheme_id'] = $scheme_id;
                $updateDetails['atr_type'] = $atr_type;
                $updateDetails['atr_desc'] = trim($atr[0]->atr_desc);
                $updateDetails['remarks'] = $remarks;
                // $updateDetails['is_processed'] = 1;
                $updateDetails['send_to_op'] =1 ; 
                $updateDetails['send_to_op_by'] = $user_id;
                $updateDetails['send_to_op_date'] = date('Y-m-d H:i:s');
                $is_update = DB::table('cmo.cmo_sm_data')
                    ->where('grievance_id', $grievance_id)
                    // ->where('scheme_id', $scheme_id)
                    // ->where('grievance_mobile', $grievance_mobile_no)
                    ->where('is_processed', 0) //Temporary Code
                    ->where('is_redressed', 0)
                    ->where('send_to_op', 0)
                    ->update($updateDetails);

                // $ben_update = DB::table($table)
                //     ->where('id', $ben_id)
                //     ->where('scheme_id', $scheme_id)
                //     ->update($benDetails);



                // $accept_reject_model = new AcceptRejectInfo;
                // $accept_reject_model->created_at = $c_time;
                // $accept_reject_model->application_id = $ben_id;
                // $accept_reject_model->scheme_id = $scheme_id;
                // $accept_reject_model->user_id = $user_id;
                // $accept_reject_model->comment_message = $remarks;
                // $accept_reject_model->user_id = $user_id;
                // $accept_reject_model->created_by_dist_code = $created_by_dist_code;
                // $accept_reject_model->created_by_local_body_code = $created_by_local_body_code;
                // $accept_reject_model->ip_address = request()->ip();
                // $accept_reject_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod() . 'AV';
                // $accept_reject_model->op_type = 'SMATAG';
                // $is_saved_log = $accept_reject_model->save();
                // dump($is_update); dd($ben_update);         
                if ($is_update == 1) {
                    DB::commit();
                    $response = [
                        'status' => 1,
                        'msg' => 'Beneficiary has been send to Operator Successfully',
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
        //  dd($request->all());
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
                $atr = DB::connection('pgsql_mis')->select(
                    "select  atn_id,atr_desc from cmo.m_cmo_atr where atn_id = '" . $atr_type . "'"
                );
                // $table = $this->getSchemaName($scheme_id);
                DB::beginTransaction();
                $is_insert = DB::statement("INSERT INTO cmo.cmo_sm_data_archive(grievance_id, 
             grievance_no,grievance_source,receipt_mode,received_at,reference_no,applicant_name,pri_cont_no,alt_cont_no,cont_email,applicant_gender,applicant_age,applicant_caste,applicant_reigion,applicant_address,state_id,district_id,block_id,municipality_id,gp_id,ward_id,police_station_id,assembly_const_id,postoffice_id,employment_type,employment_status,grievance_category,grievance_description,action_requested,usb_unique_id,parent_grievance_id,status,atr_recv_cmo_flag,emergency_flag,created_by,updated_by,sub_division_id,uploaded_doc_id,created_by_position,updated_by_position,assigned_to_id,assigned_to_position,educational_qualification_id,professional_qualification_id,skill_id,address_type,action_taken_note,atn_id,force_closure_2020,closure_reason_id,deo_phone_no,assigned_by_office_id,assigned_to_office_id,assigned_by_office_cat,assigned_to_office_cat,atr_submit_by_lastest_office_id,direct_close,jb_next_level_role_id,marked_date,marked_by,jb_name,jb_id,scheme_id,is_processed,level_type,remarks,jb_dist_code,jb_local_body_code,jb_gp_ward_code,redressed_by,redressed_date,is_redressed,jb_rural_urban_id,is_change_block,change_block_by,change_block_date,response_back_date,response_back_by,api_fetching_date,atr_recv_cmo_date,grievence_close_date,created_on,updated_on,grievance_generate_date,current_atr_date,lgd_dist,lgd_block,lgd_muni,atr_type,atr_desc
            ) (SELECT grievance_id, 
             grievance_no,grievance_source,receipt_mode,received_at,reference_no,applicant_name,pri_cont_no,alt_cont_no,cont_email,applicant_gender,applicant_age, applicant_caste, applicant_reigion,applicant_address,state_id,district_id,block_id,municipality_id,gp_id,ward_id,police_station_id,assembly_const_id,postoffice_id,employment_type,employment_status,grievance_category,grievance_description,action_requested,usb_unique_id,parent_grievance_id,status,atr_recv_cmo_flag,emergency_flag,created_by,updated_by,sub_division_id,uploaded_doc_id,created_by_position,updated_by_position,assigned_to_id,assigned_to_position,educational_qualification_id,professional_qualification_id,skill_id,address_type,action_taken_note,atn_id,force_closure_2020,closure_reason_id,deo_phone_no,assigned_by_office_id,assigned_to_office_id,assigned_by_office_cat,assigned_to_office_cat,atr_submit_by_lastest_office_id,direct_close,jb_next_level_role_id,marked_date,marked_by,jb_name,jb_id,scheme_id,is_processed,level_type,remarks,jb_dist_code,jb_local_body_code,jb_gp_ward_code,redressed_by,redressed_date,is_redressed,jb_rural_urban_id,is_change_block,change_block_by,change_block_date,response_back_date,response_back_by,api_fetching_date,atr_recv_cmo_date,grievence_close_date,created_on,updated_on,grievance_generate_date,current_atr_date,lgd_dist,lgd_block,lgd_muni,atr_type,atr_desc
              from cmo.cmo_sm_data where grievance_id='" . $grievance_id . "')");
                $updateDetails = [];
                $updateDetails['atr_type'] = $atr_type;
                $updateDetails['atr_desc'] = trim($atr[0]->atr_desc);
                $updateDetails['remarks'] = $remarks;
                $updateDetails['lgd_dist'] = $district;
                if ($rural_urban == 1) {
                    $updateDetails['lgd_muni'] = $block;
                    $updateDetails['lgd_block'] = null;
                } else {
                    $updateDetails['lgd_block'] = $block;
                    $updateDetails['lgd_muni'] = null;
                }
                $updateDetails['is_change_block'] = 1;
                $updateDetails['change_block_by'] = $user_id;
                $updateDetails['change_block_date'] = date('Y-m-d H:i:s');
                $is_update = DB::table('cmo.cmo_sm_data')
                    ->where('grievance_id', $grievance_id)
                    // ->where('scheme_id', $scheme_id)
                    ->where('pri_cont_no', $grievance_mobile_no)
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
            dd($e);
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
        // dd('ok');
        $user_id = AuthChecker::getUserId();
        $designation = Auth::user()->designation_id;
        $mapObj = DB::connection('pgsql_mis')
            ->table('public.duty_assignement')
            ->where('user_id', $user_id)
            ->where('is_active', 1)
            ->first();
        $scheme = DB::connection('pgsql_mis')->select(
            'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
            $user_id .
            ' and is_active=1 and scheme_id in (2,10,11)) order by scheme_name'
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
        $user_id = Auth::user()->id;
        if ($request->ajax()) {
            // dd($request->all());
            $scheme_id = $request->scheme_id;  
            $mapObj = DB::connection('pgsql_mis')
            ->table('public.duty_assignement')
            ->where('scheme_id', $scheme_id)
            ->where('user_id', $user_id)
            ->where('is_active', 1)
            ->first();
            if ($mapObj->mapping_level == 'Block') {
                $local_body_code = $mapObj->taluka_code;
                $query = " Select * from cmo.cmo_sm_data where is_redressed =0 and is_processed = 0 and  scheme_id = '" . $scheme_id . "' and  lgd_block='" . $local_body_code . "' and send_to_op=1 ";
            }elseif ($mapObj->mapping_leve == 'Subdiv') {
                $local_body_code = $mapObj->urban_body_code;
                $munlist=UrbanBody::where('sub_district_code', $local_body_code)->get()->toArray();
             
                 $query = "Select * from cmo.cmo_sm_data where is_redressed =0 and is_processed = 0 and lgd_muni IN ('" . implode("','", $munlist) . "') and scheme_id= " . $scheme_id." and send_to_op=0  ";
              }else {
                return redirect('/')->with('success', 'UnAuthorized');
            }

            //  dd($query);
            $data = DB::select($query);

            return datatables()
                ->of($data)
                ->addColumn('view', function ($data) use ($scheme_id) {
                    if ($data->is_processed == 0     && $data->send_to_op == 1) {
                        // href="CMO-grievance-find?id=' . $data->jb_beneficiary_id  . '&scheme_id=' . $data->scheme_id . '&sm_mobile_no='.$data->sm_mobile_no.'"
                        $action = '<a href="' . route('jb-pension', ['scheme_id' => encrypt($data->scheme_id), 'type' => 4, 'grievance_id' => $data->grievance_id]) . '">
                        <button class="btn btn-xs btn-info find_applicant">
                            <i class="glyphicon glyphicon-edit"></i> Entry
                        </button>
                    </a>';
         
                    }else{
                        $action = '';
                    }
                    return $action;
                })
                ->addColumn('grievance_id', function ($data) {
                    return $data->grievance_id;
                })
                ->addColumn('grievance_name', function ($data) {
                    return $data->applicant_name;
                })
                ->addColumn('sm_mobile_no', function ($data) {
                    return $data->pri_cont_no;
                })
                ->addColumn('cmo_receive_date', function ($data) {
                    return $data->created_on;
                })
               
                // ->addColumn('description', function ($data) {
                //     return $data->complain_description;
                // })
                ->rawColumns(['view', 'grievance_id', 'grievance_name', 'sm_mobile_no', 'cmo_receive_date', ])
                ->make(true);
        }
    }
    public function hodIndex(Request $request)
    {
        $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        // echo '<pre>'; print_r($roleArray);die();
        $designation_id = Auth::user()->designation_id;
        $user_id = AuthChecker::getUserId();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") AND id in(2,10,11) order by rank"));
        // echo '<pre>';print_r($schemes);die();
        if ($designation_id == 'Admin' || $designation_id == 'HOD') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if (AuthChecker::ApproverPermission()) {
            // echo 1;die();
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                // echo $designation_id;die();
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
        //    dd($request->all());
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        $scheme_code = $request->scheme_code;
        $district = $request->district;
        $operation_type = $request->operation_type;
        $process_type = $request->process_type;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $gp_ward = $request->gp_ward;
        $muncid = $request->muncid;
        // $table = $this->getSchemaName($scheme_code);
        $table = 'pension.beneficiaries';
        if ($request->ajax()) {
            if (AuthChecker::HODChecker()) {
                $query = '';
                if (!empty($district)) {
                    if ($district == '100') {
                        $query .= "SELECT grievance_id,grievance_no,pri_cont_no,applicant_name,is_processed,is_redressed FROM cmo.cmo_sm_data where  is_processed = " . $operation_type . " AND scheme_id = " . $scheme_code . "  AND jb_dist_code is null";
                    } else {
                        $query .= "SELECT grievance_id,grievance_no,pri_cont_no,applicant_name,is_processed,is_redressed FROM cmo.cmo_sm_data where is_processed = " . $operation_type . " AND scheme_id = " . $scheme_code . " AND lgd_dist = '" . $district . "' ";
                    }
                } else {
                    $query .= "SELECT grievance_id,grievance_no,pri_cont_no,applicant_name,is_processed,is_redressed FROM cmo.cmo_sm_data  where is_processed = " . $operation_type . " AND scheme_id = " . $scheme_code . " ";
                }
                if (!empty($process_type)) {
                    if ($process_type == 1) {
                        $query .= " and is_redressed = 0 and is_mark = 1 ";
                    } elseif ($process_type == 2) {
                        $query .= " and is_redressed = 1";
                    }
                }
                //    dd($query);
                $data = DB::connection('pgsql')->select($query);

                return datatables()->of($data)
                    ->addIndexColumn()

                    // ->addColumn('address', function ($data) {
                    //     $districts = District::where('district_code', $data->created_by_dist_code)->first();
                    //     $address = '';
                    //     if (!empty($district)) {
                    //         $address = 'District - ' . $districts->district_name . '<br>';
                    //     }
                    //     if ($data->rural_urban_id == 1) {
                    //         $address .= 'Municipality - ' . $data->block_ulb_name . '<br>';
                    //         $address .= 'Ward - ' . $data->gp_ward_name;
                    //     } else {
                    //         $address .= 'Block - ' . $data->block_ulb_name . '<br>';
                    //         $address .= 'GP - ' . $data->gp_ward_name;
                    //     }
                    //     return $address;
                    // })
                    // ->addColumn('cmo_address', function ($data) {
                    //     $cmo_address = '';
                    //     $cmo_address .= 'Block/Municipality - ' . $data->block_ulb_name . '<br>';
                    //     $cmo_address .= 'GP/Ward - ' . $data->gp_ward_name;
                    //     return $cmo_address;
                    // })
                    ->addColumn('action', function ($data) use ($scheme_code) {
                        $action = '<button class="btn btn-primary btn-xs ben_view_button" value="' . $data->grievance_id . '_' . $scheme_code . '_' . $data->is_redressed . '"><i class="glyphicon glyphicon-edit"></i>View</button>';
                        if ($data->is_processed == 3) {
                            $action = '<b>Pushed to CMO</b>';
                        }
                        return $action;
                    })
                    ->addColumn('process_type', function ($data) {
                        $html = '';
                        if ($data->is_processed == 2 && $data->is_redressed == 0) {
                            $html = '<span class="text-dafault"><b>Map Applicant</b></span>';
                        } else if ($data->is_processed == 2 && $data->is_redressed == 1) {
                            $html = '<span class="text-dafault"><b>Redressed</b></span>';
                        }
                        return $html;
                    })
                    ->addColumn('check', function ($data) use ($scheme_code) {
                        // if ($data->is_processed == 1) {
                            return '<input type="checkbox"  name="chkbx" class="all_checkbox"  onclick="controlCheckBox();" value="' . $data->grievance_id . '_' . $scheme_code . '_' . $data->is_redressed . '">';
                        // } 
                    })
                    ->rawColumns(['action', 'process_type', 'check'])
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
            // dd($request->all());
            $grievance_id = $request->grievance_id;
            $scheme_id = $request->scheme_id;
            $is_redressed = $request->is_redressed;
            // $table = $this->getSchemaName($scheme_id);
            $table = 'pension.beneficiaries';
            $query = '';
            if ($is_redressed == 0) {
                $query .= "SELECT * FROM cmo.cmo_sm_data cmo  join $table ben on cmo.jb_id=ben.id AND cmo.is_processed = 2 AND cmo.grievance_id= '" . $grievance_id . "' AND cmo.scheme_id = " . $scheme_id . "  and ben.cmo_mark = 1";
            }
            if ($is_redressed == 1) {
                $query .= "SELECT * FROM cmo.cmo_sm_data where is_processed = 2 AND grievance_id= '" . $grievance_id . "' AND scheme_id = " . $scheme_id . "  and is_redressed = 1";
            }
            $data = DB::connection('pgsql')->select($query);
            //  dd($data);
            // $districts = District::where('district_code', $data[0]->jb_dist_code)->first();
            $atr = DB::connection('pgsql_mis')->select(
                "select  atn_id,atr_desc from cmo.m_cmo_atr where atn_id = '" . $data[0]->atr_type . "' order by atr_code,atr_desc"
            );
            // dd($data);
            if ($data == NULL) {
                return $response = [
                    'status' => 3,
                    'msg' => 'Somethimg went wrong.',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            } else {

                $response = $data;
            }

        } catch (\Exception $e) {
            //throw $th;
            dd($e);
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
        // dd($request->all());
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
        $grievance_id = (int) $request->grievance_id;
        // dd($grievance_id);
        if ($is_bulk == 0) {
            if ($opreation_type == 'A') {
                try {
                    $legacy_validation_update = DB::connection('pgsql')->table('cmo.cmo_sm_data')->where('grievance_id', $grievance_id)->where('scheme_id', $scheme_id)->where('is_processed', 2)->first();
                    if ($legacy_validation_update == NULL) {
                        return $response = [
                            'status' => 3,
                            'msg' => 'Somethimg went wrong.',
                            'type' => 'red',
                            'icon' => 'fa fa-warning',
                            'title' => 'Warning!!',
                        ];
                    } else {
                        $data = array(
                            "data" => array(
                                array(
                                    "position_id" => 1,
                                    "grievance_status" => "GM014",
                                    "grievance_id" => null,
                                    "comment" => $legacy_validation_update->remarks,
                                    "bulk_grivance_id" => [
                                            $grievance_id
                                        ],
                                    "assign_comment" => null,
                                    "action_proposed" => null,
                                    "urgency_flag" => null,
                                    "addl_doc_id" => array(),
                                    "atn_id" => (int) $legacy_validation_update->atr_type,
                                    "atn_reason_master_id" => null,
                                    "action_taken_note" => $legacy_validation_update->atr_desc,
                                    "contact_date" => null,
                                    "tentative_date" => null,
                                    "atr_doc_id" => array(),
                                    "action" => "TA"
                                )
                            )
                        );

                        $cmo_data = $this->submitNewATR($data);
                        //  dd($cmo_data);
                        $status = $cmo_data['status'];
                        $message = $cmo_data['message'];
                        // $exception = $cmo_data['exception'];
                        $updateBenDetails = [];
                        $updateBenDetails['is_processed'] = 3;
                        $updateBenDetails['response_back_by'] = $user_id;
                        $updateBenDetails['response_back_date'] = date('Y-m-d H:i:s');
                        DB::beginTransaction();
                        if ($status == 200 && $message == 'Grievance status updated successfully') {
                            $is_update = DB::table('cmo.cmo_sm_data')
                                ->where('grievance_id', $grievance_id)
                                ->where('scheme_id', $scheme_id)
                                ->where('is_processed', 2) //Temporary Code
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
                                'msg' => 'API Calling Problem. Please try again!!',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    dd($e);
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
                // dd($bulk_id_arr);
                try {
                    $grievance_array = array();
                    foreach ($bulk_id_arr as $key => $value) {
                        $bulk_single_id_arr = explode('_', $value);
                        $griv_id = $bulk_single_id_arr[0];
                        $grievance_array[] = $griv_id;
                        $scheme_id = $bulk_single_id_arr[1];
                        $legacy_validation_update = DB::connection('pgsql')->table('cmo.cmo_sm_data')->where('grievance_id', $griv_id)->where('scheme_id', $scheme_id)->where('is_processed', 2)->first();
                        if ($legacy_validation_update == NULL) {
                            return $response = [
                                'status' => 3,
                                'msg' => 'Somethimg went wrong.',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        } else {
                            $data_array["data"][] = [
                                "position_id" => 1,
                                "grievance_status" => "GM014",
                                "grievance_id" => null,
                                "comment" => $legacy_validation_update->remarks, // Set to null as per required format
                                "bulk_grivance_id" => [(int) $griv_id], // Convert to integer
                                "assign_comment" => null,
                                "action_proposed" => null,
                                "urgency_flag" => null,
                                "addl_doc_id" => [],
                                "atn_id" => (int) $legacy_validation_update->atr_type, // Ensure it's an integer
                                "atn_reason_master_id" => null,
                                "action_taken_note" => $legacy_validation_update->atr_desc,
                                "contact_date" => null,
                                "tentative_date" => null,
                                "atr_doc_id" => [],
                                "action" => "TA"
                            ];
                        }
                        $data = $data_array;
                    }
                    // dd($data);
                    $cmo_data = $this->submitNewATR($data);
                    $status = $cmo_data['status'];
                    $message = $cmo_data['message'];
                    // $exception = $cmo_data['exception'];
                    $updateBenDetails = [];
                    $updateBenDetails['is_processed'] = 3;
                    $updateBenDetails['response_back_by'] = $user_id;
                    $updateBenDetails['response_back_date'] = date('Y-m-d H:i:s');
                    DB::beginTransaction();
                    if ($status == 200 && $message == 'Grievance status updated successfully') {
                        $is_update = DB::table('cmo.cmo_sm_data')
                            ->whereIn('grievance_id', $grievance_array)
                            ->where('scheme_id', $scheme_id)
                            ->where('is_processed', 2) //Temporary Code
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
    public function cmoReport(Request $request)
    {
        $this->middleware('auth');
        $base_date = '2020-01-01';
        date_default_timezone_set('Asia/Kolkata');
        $c_time = Carbon::now();
        $c_date = $c_time->format("Y-m-d");
        $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        // $designation_id = AuthChecker::getDesignationId();
        $userId = AuthChecker::getUserId();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        if (AuthChecker::AdminChecker() || AuthChecker::HODChecker() || AuthChecker::HOPChecker()) {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if (AuthChecker::ApproverPermission() || AuthChecker::VerifierChecker()) {
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

