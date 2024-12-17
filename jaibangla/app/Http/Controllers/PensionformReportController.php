<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Http\Controllers\Redirect;
use App\designationMaster;
use App\Configduty;
use App\District;
use App\UrbanBody;
use App\SubDistrict;

use App\SchemecodeStatic;
use App\DocumentType;
use App\SchemeDocMap;
use App\Scheme;
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\AcceptRejectInfo;
use App\DsPhase;

class PensionformReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function isValidPostgresInteger($value) {
        // Check if the value is numeric and an integer
        if (is_numeric($value) && (int)$value == $value) {
            // Check if it falls within the PostgreSQL integer range
            if ($value >= -2147483648 && $value <= 2147483647) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    public function schemeSelection(Request $request)
    {
        $report_type = '';

        if ($request->has('type')) {
            $report_type = $request->get('type');
            if ($report_type == 'F') {
                $report_type_name = 'Fresh Beneficiary List Report';
            } else if ($report_type == 'V') {
                $report_type_name = 'Verified Beneficiary List Report';
            } else if ($report_type == 'A') {
                $report_type_name = 'Approved Beneficiary List Report';
            } else if ($report_type == 'R') {
                $report_type_name = 'Recomended Beneficiary List Report';
            } else if ($report_type == 'T') {
                $report_type_name = 'Rejected Beneficiary List Report';
            } else if ($report_type == 'C') {
                $report_type_name = 'Complete Beneficiary List Report';
            } else if ($report_type == 'NSAP') {
                $report_type_name = 'NSAP Mark Beneficiary List';
            }else {
                return redirect('/')->with('error', 'Error: Report type invalid');
            }
        } else {
            return redirect('/')->with('error', 'Signature Error: Report Type not selected');
        }
        $user_id = Auth::user()->id;
        $duty_schemes = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->get()->pluck('scheme_id')->toArray();
        $scheme_list_constants = Config::get('constants.scheme_code_map');
        $scheme_list = array();
        foreach ($scheme_list_constants as $key => $arr) {
            $list_arr = array();
            if (in_array($key, $duty_schemes)) {
                $list_arr['scheme_id'] = $arr['scheme_id'];
                $list_arr['model_name'] = $arr['model_name'];
                $list_arr['scheme_name'] = $arr['scheme_name'];
                array_push($scheme_list, $list_arr);
            }
        }
        $mod_list = array_values($scheme_list);
        // dd($mod_list);
        return view('pensionreport.scheme', ['scheme_list' => $mod_list, 'type' => $report_type, 'report_type_name' => $report_type_name]);
    }
    public function schemeSessionCheck(Request $request)
    {
        $scheme_id = 0;
        $ben_table = "";

        $pr1 = $request->get('pr1');

        $scheme_code_map = Config::get('constants.scheme_code_map');
        $scheme_details = '';
        if (array_key_exists($pr1, $scheme_code_map)) {
            $scheme_details = $scheme_code_map[$pr1];

            $scheme_id = $scheme_details['scheme_id'];
            $scheme_row = Scheme::where('id', $scheme_id)->first();
            $scheme_model = 'App\\' . $scheme_details['model_name'];

            $is_active = 0;
            $roleArray = $request->session()->get('role');
            $designation_id_old = Auth::user()->designation_id_old;
            // dd($designation_id_old);
            if ($designation_id_old == 'HOD' || $designation_id_old == 'SpecialStatusCheck' || $designation_id_old == 'AuditOfficer' || $designation_id_old == 'Special LAO') {
                if ($scheme_id == 2 || $scheme_id == 1|| $scheme_id == 11 || $scheme_id == 13 || $scheme_id == 17 || $scheme_id == 18 || $scheme_id == 8 || $scheme_id == 9 || $scheme_id == 5 || $scheme_id == 10) {
                    $request->session()->put('scheme_id', $scheme_id);
                    $request->session()->put('scheme_name', $scheme_row['scheme_name']);
                    $request->session()->put('model_name', $scheme_model);
                    $is_active = 1;
                }
            } else {
                foreach ($roleArray as $roleObj) {
                    if ($roleObj['scheme_id'] == $scheme_id) {
                        $is_active = 1;
                        //dd($roleObj);
                        $request->session()->put('scheme_id', $scheme_id);
                        $request->session()->put('scheme_name', $scheme_row['scheme_name']);
                        $request->session()->put('model_name', $scheme_model);

                        $request->session()->put('is_first', $roleObj['is_first']);
                        $request->session()->put('role_id', $roleObj['id']);
                        $request->session()->put('role_name', $roleObj['role_name']);


                        $request->session()->put('level', $roleObj['mapping_level']);
                        $request->session()->put('distCode', $roleObj['district_code']);
                        $request->session()->put('is_urban', $roleObj['is_urban']);
                        if ($roleObj['is_urban'] == 1) {
                            $request->session()->put('bodyCode', $roleObj['urban_body_code']);
                        } else {
                            $request->session()->put('bodyCode', $roleObj['taluka_code']);
                        }
                        break;
                    }
                }
            }
            return $is_active;
        }
        return -1;
    }

    public function applicationStatusList(Request $request)
    {
        //  dd($request->all());
        $phase_list = DsPhase::orderBy('id')->get();
        $designation_id_old = Auth::user()->designation_id_old;
        if (!$request->has('pr1')) {
            return redirect('/')->with('error', 'Signature Error: Scheme Type not selected');
        }
        // $scheme_id= $request->get('pr1');
        // dd($scheme_id);
        if ($status = $this->schemeSessionCheck($request)) {
            if ($status == -1) {
                return redirect('/')->with('error', 'Error: Scheme Not Configured');
            }
            $role_name = $request->session()->get('role_name');
            $scheme_id = $request->session()->get('scheme_id');
            
            $mappingLevel = $request->session()->get('level');
            $download_excel = 0;
            $district_visible = 0;
            $is_rural_visible = 0;
            $urban_visible = 0;
            $munc_visible = 0;
            $gp_ward_visible = 0;
            $districtList = collect([]);
            $muncList = collect([]);
            $gpwardList = collect([]);
            if ($role_name == 'Approver' || $role_name == 'StatusCheckerDistrict' ) {
                $is_urban = $request->rural_urbanid;
                $district_code = $request->session()->get('distCode');
                $urban_body_code = $request->urban_body_code;
                $block_ulb_code = $request->block_ulb_code;
                $is_rural_visible = 1;
                $urban_visible = 1;
                $munc_visible = 1;
                $gp_ward_visible = 1;
                $download_excel = 1;
            } else if ($role_name == 'Verifier' || $role_name == 'Operator' || $role_name == 'StatusCheckerField' || $role_name == 'MIS User') {
                $district_code = $request->session()->get('distCode');
                if ($mappingLevel == 'Block') {
                    $block_ulb_code = NULL;
                    $is_rural_visible = 0;
                    $is_urban = 2;
                    $munc_visible = 0;
                    $urban_body_code = $request->session()->get('bodyCode');
                    $block_ulb_code = NULL;
                    $gpwardList = GP::where('block_code', $urban_body_code)->get();
                    $gp_ward_visible = 1;
                } else if ($mappingLevel == 'Subdiv') {
                    $block_ulb_code = $request->block_ulb_code;
                    $urban_body_code = $request->session()->get('bodyCode');
                    $is_rural_visible = 0;
                    $is_urban = 1;
                    $munc_visible = 1;
                    $gp_ward_visible = 1;
                    $muncList = UrbanBody::where('sub_district_code', $urban_body_code)->get();
                    $block_ulb_code = $request->block_ulb_code;
                }
                $download_excel = 1;
            } else {
                $district_visible = 1;
                $is_urban = NULL;
                $districtList = District::get();
                $district_code = $request->district_code;
                $urban_body_code = $request->urban_body_code;
                $block_ulb_code = $request->block_ulb_code;
                $is_rural_visible = 1;
                $munc_visible = 1;
                $gp_ward_visible = 1;
                $urban_visible = 1;
                if($scheme_id==5){
                    $download_excel = 1;
                }
            }
            $condition = array();
            //$download_excel = 0;
            //$report_type N - Total List, V-Verified List, R-Recomender List, A-Approved List, T- Rejected List 
            $report_type = 'N';

            $report_type_name = 'Beneficiary List';
            if ($request->has('type')) {
                $report_type = $request->get('type');
                if ($report_type == 'F') {
                    $report_type_name = 'Fresh Beneficiary List';
                    // $condition['next_level_role_id']='is not null';
                } else if ($report_type == 'V') {
                    $report_type_name = 'Verified Beneficiary List';
                    // $condition['next_level_role_id']='is not null';
                } else if ($report_type == 'A') {
                    $report_type_name = 'Approved Beneficiary List';
                    $condition['next_level_role_id'] = 0;
                } else if ($report_type == 'R') {
                    $report_type_name = 'Recomended Beneficiary List';
                    //Only For Purohit Scheme
                    $condition['next_level_role_id'] = 106;
                } else if ($report_type == 'T') {
                    $report_type_name = 'Rejected Beneficiary List';
                    //      $condition['next_level_role_id'] = '-1';
                } else if ($report_type == 'C') {
                    $report_type_name = 'Complete Beneficiary List';
                } else if ($report_type == 'NSAP') {
                    $report_type_name = 'NSAP Mark Beneficiary List';
                }else {
                    return redirect('/')->with('error', 'Error: Report type invalid');
                }
            } else {
                return redirect('/')->with('error', 'Signature Error: Report Type not selected');
            }
            if (request()->ajax()) {
                // dd($request->all());
                $scheme_id = $request->session()->get('scheme_id');
                // dd($scheme_id);
                if (!empty($district_code)) {
                    $condition["created_by_dist_code"] = $district_code;
                }
                if (!empty($is_urban)) {
                    // $condition[$contact_table . ".rural_urban_id"] = $is_urban;
                    if ($is_urban == 2) {
                        if (!empty($urban_body_code)) {
                            //$condition["rural_urban_id"] = 2;
                            $condition["created_by_local_body_code"] = $urban_body_code;
                        }
                    }
                    //'Urban'
                    if ($is_urban == 1) {
                        if (!empty($urban_body_code)) {
                            //$condition["rural_urban_id"] = 1;
                            $condition["created_by_local_body_code"] = $urban_body_code;
                            //$download_excel = 1;
                        }
                        if (!empty($block_ulb_code)) {
                            $condition["block_ulb_code"] = $block_ulb_code;
                            //$download_excel = 1;
                        }
                    }
                }
                if (!empty($request->gp_ward_code)) {
                    $condition["gp_ward_code"] = $request->gp_ward_code;
                    $download_excel = 1;
                }







                $serachvalue = $request->search['value'];
                $limit = $request->input('length');
                $offset = $request->input('start');

                $totalRecords = 0;
                $filterRecords = 0;
                $data = array();
                $scheme_length = NULL;
                $id_length = NULL;
                if (!empty($scheme_id)) {
                    $scheme_row = Scheme::where('id', $scheme_id)->first();
                    // dd($scheme_row->toArray());
                    if (!empty($scheme_row)) {
                        $scheme_schema = $scheme_row->short_code;
                        if (!empty($scheme_schema)) {
                            $table = $scheme_schema;
                            //dd($table);
                            $query = DB::connection('pgsql5')->table('' . $table . '.beneficiaries')->where($condition)->where('scheme_id',$scheme_id);
                            // $query = DB::table::on('pgsql_mis')->where($condition);
                        } else {
                            $model_name = $request->session()->get('model_name');
                            $query = $model_name::where($condition);
                        }
                        $scheme_length =  $scheme_row->scheme_length;
                        $id_length = $scheme_row->id_length;
                    } else {
                        $model_name = $request->session()->get('model_name');
                        $query = $model_name::where($condition);
                        $scheme_length = NULL;
                        $id_length = NULL;
                    }
                } else {
                    $model_name = $request->session()->get('model_name');
                    $query = $model_name::where($condition);
                    $scheme_length = NULL;
                    $id_length = NULL;
                }


                //Report Type Filter
                if ($report_type == 'F') { // Fresh List
                    $query = $query->whereNull('next_level_role_id')->where('is_rejected',0);
                    if( $scheme_id==11){
                        $query =$query->whereNull('process_nsap_flag');
                    }
                }
                if ($report_type == 'T') {
                    $query = $query->where('is_rejected',1);
                }
                if ($report_type == 'V') { //Verified List
                    if ($scheme_id == 17) { //For Purohit
                        $query = $query->where('next_level_role_id', 107);
                    } else {
                        $query = $query->where('is_verified',1)->where('is_approved',0)->where('is_rejected',0);
                    }
                }
                if ($report_type == 'NSAP') {
                    $query = $query->where('process_nsap_flag', 1);
                }

                // if (!empty($request->phase_code))  {
                //     $query = $query->where('ds_phase', $request->phase_code);
                // }
                if($scheme_id==10){
                    if(!empty($request->phase_code))
                    {
                        $query = $query->whereRaw('(ds_phase='. $request->phase_code. ' or (mark_ds_phase='. $request->phase_code. ' and sm_ds_mark=1))');
                    }
                    if(!empty($request->sm_ds_flag))
                    {
                        $query = $query->where('sm_flag', 1);
                    }
                }else{
                    if (!empty($request->phase_code)) {
                             $query = $query->where('ds_phase', $request->phase_code);
                    }
                }
                if(!empty($request->caste)){
                    $query = $query->where('caste', $request->caste);
                }
                if (empty($serachvalue)) {
                    $totalRecords = $query->count();
                    $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
                } else {
                    if (is_numeric($serachvalue)) {
                        $ben_id = $serachvalue;
                        $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                            if ($this->isValidPostgresInteger($serachvalue)) {
                                $query1->where('id', $ben_id)
                                ->orWhere('bank_code', $serachvalue);
                            } else {
                                $query1->where('bank_code', $serachvalue);
                            }
                            // $query1->where('id', $ben_id)
                            //     ->orWhere('bank_code', $serachvalue);
                        });
                        $totalRecords = $query->count('id');
                        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
                    } else {
                        $query = $query->where(function ($query1) use ($serachvalue) {
                            if (strtoupper(trim($serachvalue)) == 'STATE') {
                                $query1->where('is_state', TRUE);
                            } else {
                                $query1->where('ben_fname', 'like', $serachvalue . '%')
                                    ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                                    ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                                    ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                            }
                        });
                       
                        $totalRecords = $query->count('id');
                        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
                    }
                    $filterRecords = count($data);
                }

                return datatables()
                    ->of($data)
                    ->setTotalRecords($totalRecords)
                    ->setFilteredRecords($filterRecords)
                    ->skipPaging()
                    ->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

                        $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

                        return $app_id;
                    })

                    
                    ->addColumn('ben_name', function ($data) {
                        // return $data->getName();
                        return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                    })
                    ->addColumn('benf_name', function ($data) {
                        return "Father Name";
                    })
                    ->addColumn('ben_age', function ($data) {
                        return $data->ben_age;
                    })
                    ->addColumn('gender', function ($data) {
                        return $data->gender;
                    })
                    ->addColumn('caste', function ($data) {
                        $caste = '';
                        if ($data->caste == 1 || $data->caste == 2 || $data->caste == 3 || $data->caste == 4 || $data->caste == 5) {
                            $caste = 'Not Defined';
                        }else {
                            $caste = $data->caste;
                        }
                        return $caste;
                    })
                    ->addColumn('bank_ifsc', function ($data) {
                        return $data->bank_ifsc;
                    })
                    ->addColumn('bank_code', function ($data) {
                        return $data->bank_code;
                    })
                    ->addColumn('village_town_city', function ($data) {
                        return trim($data->village_town_city);
                    })
                    ->addColumn('house_premise_no', function ($data) {
                        return trim($data->house_premise_no);
                    })
                    ->addColumn('mobile_no', function ($data) {
                        return trim($data->mobile_no);
                    })
                    ->addColumn('post_office', function ($data) {
                        return trim($data->post_office);
                    })
                    ->addColumn('pincode', function ($data) {
                        return trim($data->pincode);
                    })

                    
                
                    // add 20 march
                    ->addColumn('is_state_des', function ($data) {
                        if (($data->is_state == TRUE)) {
                            $val = '<span class="badge badge-danger">State Entry</span>';
                        } else {
                            $val = '';
                        }
                        return $val;
                    })
                    
                  

                    // ->addColumn('acc_validation_pushed_at', function ($data) use ($report_type) {

                    //         if ($report_type == 'A')
                    //         {
                    //             if(!empty($data->acc_validation_pushed_at))
                    //             {

                    //             $date=date("d-m-Y", strtotime($data->acc_validation_pushed_at));
                    //             }
                    //             else{
                    //             $date='';
                    //             }

                    //             return trim($date);
                    //             }
                    //         else{
                    //         return '';
                    //         }
                        
                    // })
                    // ->addColumn('acc_validation_success_at', function ($data) use ($report_type) {

                    //     // $timestamp=$data->acc_validation_success_at;          
                    //     // $datetime = explode(" ",$timestamp);
                    //     // $date = $datetime[0];
                    //         if ($report_type == 'A')
                    //         {
                    //             if(!empty($data->acc_validation_success_at))
                    //             {

                    //             $date=date("d-m-Y", strtotime($data->acc_validation_success_at));
                    //             }
                    //             else{
                    //             $date='';
                    //             }


                    //         return trim($date);
                    //         }
                    //         else{
                    //         return '';

                    //         }
                    // })
                    ->addColumn('first_payment_pushed_at', function ($data) use ($report_type,$scheme_id) {
                            if ($report_type == 'A' && ($scheme_id=='1' ||$scheme_id=='3' ))
                            {
                                if(!empty($data->first_payment_pushed_at))
                                {

                                $date=date("d-m-Y", strtotime($data->first_payment_pushed_at));
                                }
                                else{
                                $date='';
                                }
                                return trim($date);
                                }
                            else{
                            return ''; 
                            }
                    })
                    ->addColumn('first_payment_success_at', function ($data) use ($report_type,$scheme_id) {
                       
                        if ($report_type == 'A' && ($scheme_id=='1' ||$scheme_id=='3' ))
                            {
                                if(!empty($data->first_payment_success_at))
                                {

                                $date=date("d-m-Y", strtotime($data->first_payment_success_at));
                                }
                                else{
                                $date='';
                                }
                                return trim($date);
                            }
                            else{
                                return '';

                            }
                    })
                    

                    ->addColumn('action', function ($data) use ($scheme_id, $report_type, $role_name) {
                        $val = '<a href="application-details-read_only/' . $data->id . '?scheme_id=' . $scheme_id . '" class="btn btn-primary ben_view_button" role="button" target="_blank">View</a>';
                        if (($report_type == 'A') && ($data->lot_generated == 1) && ($data->payment_count > 0)) {
                            $val = $val . '<span class="badge badge-danger">Payment has been initiated</span>';
                        }
                        if ($report_type == 'C') {
                            if($scheme_id==11){
                                if(!is_null($data->next_level_role_id) && $data->next_level_role_id==0){
                                    if($data->dup_bank==1){
                                    $val = '<span class="badge badge-danger">Approved but due to Duplicate Bank A/c, payment has been stopped</span>';
                                    }
                                    else{
                                        $val = '<span class="badge badge-danger">Approved</span>';
                                    }
                                   }
                                else if($data->is_verified==1 and $data->is_approved==0 and $data->is_rejected==0){
                                    $val = '<span class="badge badge-danger">Verified</span>';
                                }
                                else if(is_null($data->next_level_role_id)){
                                    if($data->process_nsap_flag==1){
                                     $val = '<span class="badge badge-danger">NSAP Marked</span>';  
                                    }
                                    else
                                    $val = '<span class="badge badge-danger">Fresh</span>';
                                }
                                else if ($data->is_rejected==1) {
                                    $val = $val . '<span class="badge badge-danger">Rejected</span>';
                                }  
                            }
                            else{
                                        if (!isset($data->next_level_role_id)) {
                                            $val = $val . '<span class="badge badge-primary">Fresh</span>';
                                        } else if ($data->next_level_role_id == 0) {
                                            $val = $val . '<span class="badge badge-success">Approved</span>';
                                        } else if ($data->is_rejected==1) {
                                            $val = $val . '<span class="badge badge-danger">Rejected</span>';
                                        } else if ($data->next_level_role_id == 106 && $scheme_id == 17) {
                                            $val = $val . '<span class="badge badge-info">Reverted</span>';
                                        } else if ($data->is_verified==1 and $data->is_approved==0 and $data->is_rejected==0) {
                                            $val = $val . '<span class="badge badge-dark">Verified</span>';
                                        }
                           }
                        }
                        if ($report_type == 'NSAP') {
                            if(!is_null($data->next_level_role_id) && $data->next_level_role_id==0){
                                $val = '<span class="badge badge-danger">Approved</span>';
                               }
                            else if($data->is_verified==1 and $data->is_approved==0 and $data->is_rejected==0){
                                $val = '<span class="badge badge-danger">Verified</span>';
                            }
                            else if(is_null($data->next_level_role_id)){
                                if($data->process_nsap_flag==1){
                                 $val = '<span class="badge badge-danger">NSAP Marked</span>';  
                                }
                                else
                                $val = '<span class="badge badge-danger">Fresh</span>';
                            }
                            else if ($data->is_rejected==1) {
                                $val = $val . '<span class="badge badge-danger">Rejected</span>';
                            }  
                        }
                        if ($role_name == 'Approver' || $role_name == 'HOD') {
                            if (($report_type == 'A' || $report_type == 'R') && ($data->lot_generated == 0) && ($data->payment_count == 0)) {
                                // $val = $val . '<button class="btn btn-warning ben_reject_button">Reject</button>';
                                // $val = $val . '<button class="btn btn-success ben_revert_button">Revert</button>';
                             } 
                            
                            //else if ($report_type == 'T') {
                            //     $val = $val . '<button class="btn btn-success ben_revert_button">Revert</button>';
                            // }
                        }
                        return $val;
                    })
                    ->rawColumns(['ben_id', 'ben_name', 'ben_age', 'gender', 'caste', 'bank_ifsc', 'bank_code', 'village_town_city', 'action', 'is_state_des', 'house_premise_no', 'mobile_no', 'post_office', 'pincode'])
                    ->make(true);
            } else {
                //Goto view
                return view('pensionreport.index')->with('phase_list', $phase_list)->with('district_name', 'Test')->with('district_code', $request->session()->get('distCode'))->with('scheme', $request->session()->get('scheme_id'))
                    // ->with('schemetype','$schemetype')
                    ->with('report_type_name', $report_type_name)->with('type', $request->get('type'))
                    ->with('pr1', $request->get('pr1'))
                    ->with('scheme_name', $request->session()->get('scheme_name'))
                    ->with('district_visible', $district_visible)
                    ->with('districtList', $districtList)
                    ->with('is_rural_visible', $is_rural_visible)
                    ->with('is_urban', $is_urban)
                    ->with('urban_visible', $urban_visible)
                    ->with('urban_body_code', $urban_body_code)
                    ->with('urban_visible', $urban_visible)
                    ->with('munc_visible', $munc_visible)
                    ->with('gp_ward_visible', $gp_ward_visible)
                    ->with('muncList', $muncList)
                    ->with('gpwardList', $gpwardList)
                    ->with('mappingLevel', $mappingLevel)
                    ->with('designation_id_old',$designation_id_old)
                    ->with('download_excel',  $download_excel);
            }
        } else {
            return redirect('/')->with('error', 'User not Authorized for this scheme');
        }
    }

    public function rejectApplication(Request $request)
    {
        $user_id = Auth::user()->id;
        $role_id = $request->session()->get('role_id');
        $scheme_id = $request->session()->get('scheme_id');
        $district_code = $request->session()->get('distCode');
        $ben_id = $request->ben_id;
        $c_time = date('Y-m-d H:i:s', time());
        $accept_reject_model = new AcceptRejectInfo;
        $accept_reject_model->created_at = $c_time;
        $accept_reject_model->application_id = $ben_id;
        $accept_reject_model->scheme_id = $scheme_id;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->ip_address = request()->ip();
        $accept_reject_model->op_type = 'AR';
        $model_name = $request->session()->get('model_name');
        //$reject_reason = $request->reject_reason;
        $revert_reason = 'Rejected by user: ' . $user_id;
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (empty($scheme_obj)) {
          return redirect("/")->with('danger', 'Scheme Not Found');
        }
        if (empty($request->ben_id)) {
            return redirect("/")->with('danger', 'Applicant ID Not Found');
          }
        if (!is_numeric($request->ben_id)) {
            return redirect("/")->with('danger', 'Applicant ID Not Valid');
        }
        if (!empty($scheme_obj->short_code)) {
            $schema = $scheme_obj->short_code;
            $scheme_length =  $scheme_obj->scheme_length;
            $id_length = $scheme_obj->id_length;
          } else {
            $schema = "pension";
            $scheme_length = NULL;
            $id_length = NULL;
        }
       
        $row = DB::table($schema . '.beneficiary')
        ->where('created_by_dist_code', $district_code)->where('id', $ben_id)->where('lot_generated', 0)->where('payment_count', 0)->first();
     
        DB::beginTransaction();
        try {
            $is_saved_log = $accept_reject_model->save();
            $input_update = ['rejected_date' => $c_time, 'rejected_by' => $user_id,'next_level_role_id' => -1,'is_approved' => 2,'is_verified' => 2, 'is_rejected' => 1,'comments' => $revert_reason,'is_clean' => 10];
            $update=$model_name::where('id', $ben_id)->where('lot_generated', 0)->where('payment_count', 0)->update($input_update);
            $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
                if (in_array($scheme_id, $scheme_dedup_list)) {
                $free_pending_bank_duplicate_arr = DB::select("select ".$schema.".free_pending_bank_duplicate_data(in_scheme_id => ".$scheme_id.", in_district_code => ".$district_code.")");
                        //dd($free_pending_bank_duplicate_arr);
                $free_pending_bank_duplicate_data=$free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
                if(!empty(trim($row->mobile_no))){
                    $sp_mobile=$row->mobile_no;
                }
                else{
                    $sp_mobile=0;  
                }
                $reject_dup_adjustment_arr = DB::select("select ".$schema.".reject_dup_adjustment(
                in_old_bank_ifsc => '".$row->bank_ifsc."', 
                in_old_bank_code => '".$row->bank_code."', 
                in_old_aadhar_no => '".$row->aadhar_no."', 
                in_old_mobile_no => ".$sp_mobile."
                )");
                $reject_dup_adjustment=$reject_dup_adjustment_arr[0]->reject_dup_adjustment;
                }
                else{
                $reject_dup_adjustment=1;
                $free_pending_bank_duplicate_data=1;
                }
                $is_saved2=1;
                if ($update && $is_saved2 &&  $is_saved_log && $free_pending_bank_duplicate_data && $reject_dup_adjustment) {
                    DB::commit();
                }
                else{
                    DB::rollback();   
                }
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
        }
        
    }

    public function revertApplication(Request $request)
    {

        
        $user_id = Auth::user()->id;
        $role_id = $request->session()->get('role_id');
        $scheme_id = $request->session()->get('scheme_id');
        $ben_id = $request->ben_id;
        $c_time = date('Y-m-d H:i:s', time());
        $accept_reject_model = new AcceptRejectInfo;
        $accept_reject_model->created_at = $c_time;
        $accept_reject_model->application_id = $ben_id;
        $accept_reject_model->scheme_id = $scheme_id;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->ip_address = request()->ip();
        $accept_reject_model->op_type = 'AE';
        $model_name = $request->session()->get('model_name');
        //$reject_reason = $request->reject_reason;
        $revert_reason = 'Reverted by user: ' . $user_id;

       
        DB::beginTransaction();
        try {
            $is_saved_log = $accept_reject_model->save();
            $input_update = ['approval_rejected' => 3, 'next_level_role_id' => null, 'is_verified' => 0,'is_approved' => 0,'comments' => $revert_reason];
            $model_name::where('id', $ben_id)->where('lot_generated', 0)->where('payment_count', 0)->where('is_rejected',0)->update($input_update);

            

            
        } catch (\Exception $e) {
            
            DB::rollback();
        }
        DB::commit();
    }

    public function reject_duplicates(Request $request)
    {

        $scheme_code = $request->input('scheme_code');
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $dist_code = $duty->district_code;

        $scheme_prefix = "";
        if ($scheme_code == 10)
            $scheme_prefix = "oap_wcd";
        else if ($scheme_code == 11)
            $scheme_prefix = "wp_wcd";
        $query = "update " . $scheme_prefix . ".beneficiary set next_level_role_id=-3, av_status=-3 where id = ANY(
            select id from " . $scheme_prefix . ".mv_beneficiary_duplicate where rk>1 and lot_generated=0 and payment_count=0 
            and created_by_dist_code=" . $dist_code . ") and lot_generated=0 and payment_count=0";

        DB::connection('pgsql')->select($query);

        return "true";
    }

    public function generate_excel(Request $request)
    {
        // dd(123);
        $scheme_code =  $request->rej_scheme_code;
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $dist_code = $duty->district_code;

        $district_name = District::where('district_code', $dist_code)->pluck('district_name')->first();
        $scheme_name_row = Scheme::where('id', $scheme_code)->select('scheme_name', 'short_code')->first();
        $scheme_name = $scheme_name_row->scheme_name;
        $scheme_schema_name = $scheme_name_row->short_code;

        $title = $district_name . "_" . $scheme_name . "_Rejected Duplicates";

        $data = array();

        $query = "select block_ulb_name || case when rural_urban_id=1 then ' Municipality' else '' end  \"Block_Municipality\"
        ,id as \"Beneficiary_Id\"
        , ben_fname ||' '|| coalesce(ben_mname||' ','') || coalesce(ben_lname,'') as \"Name\"
        , b.bank_code  as \"Account_No\"
        , b.bank_ifsc as \"IFSC\"
        from " . $scheme_schema_name . ".beneficiary b where next_level_role_id = -3 and created_by_dist_code=" . $dist_code;

        $data_part = DB::connection('pgsql')->select($query);
        $data = array_merge($data, $data_part);

        $excel_data[] = array('Block/Municipality', 'Beneficiary_Id', 'Name', 'Caste', 'Account_No', 'IFSC');
        foreach ($data as $row) {
            $excel_data[] = array(
                'Block/Municipality'  => $row->Block_Municipality,
                'Beneficiary_Id'  => $row->Beneficiary_Id,
                'Name'  => $row->Name,
                'Caste' => $row->Caste,
                'Account_No'  => $row->Account_No,
                'IFSC'  => $row->IFSC
            );
        }

        Excel::create('' . $title, function ($excel) use ($excel_data, $title, $scheme_name) {
            $excel->setTitle('' . $title);
            $excel->sheet('' . $scheme_name, function ($sheet) use ($excel_data) {
                $sheet->fromArray($excel_data, null, 'A1', false, false);
            });
        })->download('xlsx');
    }
}
