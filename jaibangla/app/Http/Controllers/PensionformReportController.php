<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configduty;
use App\District;
use App\UrbanBody;
use App\Scheme;
use App\GP;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\AcceptRejectInfo;
use App\DsPhase;
use App\Helpers\AuthChecker;
use App\Workflow;
use App\SchemeStepRank;
class PensionformReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function isValidPostgresInteger($value)
    {
        // Check if the value is numeric and an integer
        if (is_numeric($value) && (int) $value == $value) {
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
        $auth = AuthChecker::ReportCheckerCommon();
        $user_id = AuthChecker::getUserId();

        if ($auth) {
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
                } else {
                    return redirect('/')->with('error', 'Error: Report type invalid');
                }
            } else {
                return redirect('/')->with('error', 'Signature Error: Report Type not selected');
            }
            $scheme_not_re = array(4, 12, 14, 15, 16, 18);
            $return_arr = array();
            $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
            $i = 0;
            foreach ($schemes as $scheme_arr) {
              if (in_array($scheme_arr->id, $scheme_not_re)) {
                  continue;
                }
              $return_arr[$i]['id'] = $scheme_arr->id;
              $return_arr[$i]['display_name'] = $scheme_arr->display_name;
               $i++;
              }
             //dd($return_arr);
            return view('pensionreport.scheme', ['scheme_list' => $return_arr, 'type' => $report_type, 'report_type_name' => $report_type_name]);

        }

    }
    public function schemeSessionCheck(Request $request)
    {
        $scheme_id = 0;
        $ben_table = "";

        $pr1 = $request->get('pr1');
        $user_id = AuthChecker::getUserId();
        $scheme_code_map = Config::get('constants.scheme_code_map');
        $scheme_details = '';
        if (array_key_exists($pr1, $scheme_code_map)) {
            $scheme_details = $scheme_code_map[$pr1];

            $scheme_id = $scheme_details['scheme_id'];
            $scheme_row = Scheme::where('id', $scheme_id)->first();
            $scheme_model = 'App\\' . $scheme_details['model_name'];

            $is_active = 0;
            $roleArray = $request->session()->get('role');
            $designation_id = Auth::user()->designation_id;
            // dd($designation_id);
            if ($designation_id == 'HOD' || $designation_id == 'SpecialStatusCheck' || $designation_id == 'AuditOfficer' || $designation_id == 'Special LAO') {
                if ($scheme_id == 2 || $scheme_id == 1 || $scheme_id == 11 || $scheme_id == 13 || $scheme_id == 17 || $scheme_id == 18 || $scheme_id == 8 || $scheme_id == 9 || $scheme_id == 5 || $scheme_id == 10) {
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
        $user_id = AuthChecker::getUserId();
        $phase_list = DsPhase::orderBy('id')->get();
        $designation_id = Auth::user()->designation_id;
        $scheme_id=$request->scheme_id;
        if (!$request->has('scheme_id')) {
            return redirect('/')->with('error', 'Signature Error: Scheme Type not selected');
        }
        $scheme_row=Scheme::where('id',$scheme_id)->where('is_active',1)->first();
        if (AuthChecker::ReportCheckerCommon()) {
            $is_active = 1;
          } else {
            $is_active = 0;
          }
          if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disabled. ');
          }
          $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
          $distCode = $duty_obj->district_code;
          $is_urban = $duty_obj->is_urban;
          $mapping_level = $duty_obj->mapping_level;
          $blockCode = $duty_obj->is_urban == 1 ? $duty_obj->urban_body_code : $duty_obj->taluka_code;
          $download_excel = 0;
          $district_visible = 0;
          $is_rural_visible = 0;
          $urban_visible = 0;
          $munc_visible = 0;
          $gp_ward_visible = 0;
          $districtList = collect([]);
          $muncList = collect([]);
          $gpwardList = collect([]);
          if($mapping_level=='District'){
            $is_urban = $request->rural_urbanid;
            $district_code = $distCode;
            $urban_body_code = $request->urban_body_code;
            $block_ulb_code = $request->block_ulb_code;
            $is_rural_visible = 1;
            $urban_visible = 1;
            $munc_visible = 1;
            $gp_ward_visible = 1;
            $download_excel = 1;
          }else if ($mapping_level=='Block' || $mapping_level=='Subdiv'){
            $district_code = $distCode;
            if ($mapping_level == 'Block') {
                $block_ulb_code = NULL;
                $is_rural_visible = 0;
                $is_urban = 2;
                $munc_visible = 0;
                $urban_body_code = $blockCode;
                $block_ulb_code = NULL;
                $gpwardList = GP::where('block_code', $urban_body_code)->get();
                $gp_ward_visible = 1;
            } else if ($mapping_level == 'Subdiv') {
                $block_ulb_code = $request->block_ulb_code;
                $urban_body_code = $blockCode;
                $is_rural_visible = 0;
                $is_urban = 1;
                $munc_visible = 1;
                $gp_ward_visible = 1;
                $muncList = UrbanBody::where('sub_district_code', $urban_body_code)->get();
                $block_ulb_code = $request->block_ulb_code;
            }
            $download_excel = 1;
          }else {
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
            if ($scheme_id == 5) {
                $download_excel = 1;
            }
        }
        $condition = array();
        $report_type = 'N';

            $report_type_name = 'Beneficiary List';
            if ($request->has('type')) {
                $report_type = $request->get('type');
                if ($report_type == 'F') {
                    $report_type_name = 'Yet to be Verified and Yet to be Approved Beneficiary List';
                    // $condition['next_level_role_id']='is not null';
                } else if ($report_type == 'V') {
                    $report_type_name = 'Verified but Yet to be Approved Beneficiary List';
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
                } else {
                    return redirect('/')->with('error', 'Error: Report type invalid');
                }
            } else {
                return redirect('/')->with('error', 'Signature Error: Report Type not selected');
            }
            if (request()->ajax()) {
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
                $next_level_role_id_operator=SchemeStepRank::getSchemeParentId($scheme_id, 1);
                $query = DB::connection('pgsql_mis')->table('pension.beneficiaries')->where($condition)->where('scheme_id', $scheme_id);
                if ($report_type == 'F') { // Fresh List
                    
                    $query = $query->where('next_level_role_id',$next_level_role_id_operator)->where('is_rejected', 0);
                    
                }
                if ($report_type == 'T') {
                    $query = $query->where('is_rejected', 1);
                }
                if ($report_type == 'V') { //Verified List
                    
                    $query = $query->where('is_verified', 1)->where('is_approved', 0)->where('is_rejected', 0);
                    
                }
                

                // if (!empty($request->phase_code))  {
                //     $query = $query->where('ds_phase', $request->phase_code);
                // }
                if (!empty($request->sm_ds_flag)) {
                    $query = $query->where('sm_flag', 1);
                }
                if (!empty($request->phase_code) && $request->phase_code>0) {
                    $query = $query->whereRaw(' (ds_phase=' . $request->phase_code . ' or  cur_mark_ds_phase=' . $request->phase_code . ') ');
                }
                if ($request->phase_code==-1) {
                    $query = $query->whereRaw(' ds_phase IS NULL and sm_ds_mark IS NULL');
                }
           
                if (!empty($request->caste)) {
                    $query = $query->where('caste', $request->caste);
                }
                if (!empty($request->from_date)) {
                    $query = $query->whereraw(" date(created_at)>='$request->from_date'");
                }
                if (!empty($request->to_date)) {
                    $query = $query->whereraw(" date(created_at)<='$request->to_date'");
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
                ->addColumn('application_id', function ($data)  {


                    return $data->id;
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
                    } else {
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



                ->addColumn('first_payment_pushed_at', function ($data) use ($report_type, $scheme_id) {
                    if ($report_type == 'A' && ($scheme_id == '1' || $scheme_id == '3')) {
                        if (!empty($data->first_payment_pushed_at)) {

                            $date = date("d-m-Y", strtotime($data->first_payment_pushed_at));
                        } else {
                            $date = '';
                        }
                        return trim($date);
                    } else {
                        return '';
                    }
                })
                ->addColumn('first_payment_success_at', function ($data) use ($report_type, $scheme_id) {

                    if ($report_type == 'A' && ($scheme_id == '1' || $scheme_id == '3')) {
                        if (!empty($data->first_payment_success_at)) {

                            $date = date("d-m-Y", strtotime($data->first_payment_success_at));
                        } else {
                            $date = '';
                        }
                        return trim($date);
                    } else {
                        return '';

                    }
                })


                ->addColumn('action', function ($data) use ($scheme_id, $report_type) {
                    $val = '<a href="processApplicationDetailsCommon/' . $data->id . '/' . $scheme_id . '" class="btn btn-primary ben_view_button" role="button" target="_blank">View</a>';
                    if (($report_type == 'A') && ($data->lot_generated == 1) && ($data->payment_count > 0)) {
                        $val = $val . '<span class="badge badge-danger">Payment has been initiated</span>';
                    }
                    if ($report_type == 'C') {
                        if ($scheme_id == 11) {
                            if (!is_null($data->next_level_role_id) && $data->next_level_role_id == 0) {
                                if ($data->dup_bank == 1) {
                                    $val = '<span class="badge badge-danger">Approved but due to Duplicate Bank A/c, payment has been stopped</span>';
                                } else {
                                    $val = '<span class="badge badge-danger">Approved</span>';
                                }
                            } else if ($data->is_verified == 1 and $data->is_approved == 0 and $data->is_rejected == 0) {
                                $val = '<span class="badge badge-danger">Verified</span>';
                            }  else if ($data->is_rejected == 1) {
                                $val = $val . '<span class="badge badge-danger">Rejected</span>';
                            }
                            else{
                                $val = '<span class="badge badge-danger">Fresh</span>';
                            }
                        } else {
                             if ($data->next_level_role_id == 0) {
                                $val = $val . '<span class="badge badge-success">Approved</span>';
                            } else if ($data->is_rejected == 1) {
                                $val = $val . '<span class="badge badge-danger">Rejected</span>';
                            } else if ($data->next_level_role_id == 106 && $scheme_id == 17) {
                                $val = $val . '<span class="badge badge-info">Reverted</span>';
                            } else if ($data->is_verified == 1 && $data->is_approved == 0 && $data->is_rejected == 0) {
                                $val = $val . '<span class="badge badge-dark">Verified</span>';
                            }else if ($data->is_verified == 0 && $data->is_approved == 0 && $data->is_rejected == 0) {
                                $val = $val . '<span class="badge badge-dark">Fresh</span>';
                            }
                        }
                    }
                
                    
                    return $val;
                })
                ->rawColumns(['ben_id', 'ben_name', 'ben_age', 'gender', 'caste', 'bank_ifsc', 'bank_code', 'village_town_city', 'action', 'is_state_des', 'house_premise_no', 'mobile_no', 'post_office', 'pincode'])
                ->make(true);

            }
            else{
                if($request->get('type')=='V'){
                    $download_excel=1;
                }
                else if($request->get('type')=='F'){
                    $download_excel=1;
                }
                else if($request->get('type')=='A' && ($scheme_id!=10 )){
                    $download_excel=1;
                }
                else
                $download_excel=0;
                return view('pensionreport.index')->with('phase_list', $phase_list)->with('district_name', 'Test')->with('district_code', $distCode)->with('scheme', $scheme_id)
                // ->with('schemetype','$schemetype')
                ->with('report_type_name', $report_type_name)->with('type', $request->get('type'))
                ->with('scheme_id', $request->get('scheme_id'))
                ->with('scheme_name', $scheme_row->scheme_name)
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
                ->with('mappingLevel', $mapping_level)
                ->with('designation_id', $designation_id)
                ->with('download_excel', $download_excel);
            }
       
    }

    public function rejectApplication(Request $request)
    {
        $user_id = AuthChecker::getUserId();
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
        $accept_reject_model->op_type = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod() . '@AR';

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
            $scheme_length = $scheme_obj->scheme_length;
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
            $input_update = ['rejected_date' => $c_time, 'rejected_by' => $user_id, 'next_level_role_id' => -1, 'is_approved' => 2, 'is_verified' => 2, 'is_rejected' => 1, 'comments' => $revert_reason, 'is_clean' => 10];
            $update = $model_name::where('id', $ben_id)->where('lot_generated', 0)->where('payment_count', 0)->update($input_update);
            $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
            if (in_array($scheme_id, $scheme_dedup_list)) {
                $free_pending_bank_duplicate_arr = DB::select("select " . $schema . ".free_pending_bank_duplicate_data(in_scheme_id => " . $scheme_id . ", in_district_code => " . $district_code . ")");
                //dd($free_pending_bank_duplicate_arr);
                $free_pending_bank_duplicate_data = $free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
                if (!empty(trim($row->mobile_no))) {
                    $sp_mobile = $row->mobile_no;
                } else {
                    $sp_mobile = 0;
                }
                $reject_dup_adjustment_arr = DB::select("select " . $schema . ".reject_dup_adjustment(
                in_old_bank_ifsc => '" . $row->bank_ifsc . "', 
                in_old_bank_code => '" . $row->bank_code . "', 
                in_old_aadhar_no => '" . $row->aadhar_no . "', 
                in_old_mobile_no => " . $sp_mobile . "
                )");
                $reject_dup_adjustment = $reject_dup_adjustment_arr[0]->reject_dup_adjustment;
            } else {
                $reject_dup_adjustment = 1;
                $free_pending_bank_duplicate_data = 1;
            }
            $is_saved2 = 1;
            if ($update && $is_saved2 && $is_saved_log && $free_pending_bank_duplicate_data && $reject_dup_adjustment) {
                DB::commit();
            } else {
                DB::rollback();
            }
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
        }

    }

    public function revertApplication(Request $request)
    {


        $user_id = AuthChecker::getUserId();
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
        $accept_reject_model->op_type = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod() . '@AE';

        $model_name = $request->session()->get('model_name');
        //$reject_reason = $request->reject_reason;
        $revert_reason = 'Reverted by user: ' . $user_id;

        $next_level_role_id_operator=SchemeStepRank::getSchemeParentId($scheme_id, 1);
        DB::beginTransaction();
        try {
            $is_saved_log = $accept_reject_model->save();
            $input_update = ['approval_rejected' => 3, 'next_level_role_id' => $next_level_role_id_operator, 'is_verified' => 0, 'is_approved' => 0, 'comments' => $revert_reason];
            $model_name::where('id', $ben_id)->where('lot_generated', 0)->where('payment_count', 0)->where('is_rejected', 0)->update($input_update);




        } catch (\Exception $e) {

            DB::rollback();
        }
        DB::commit();
    }

    public function reject_duplicates(Request $request)
    {

        $scheme_code = $request->input('scheme_code');
        $user_id = AuthChecker::getUserId();
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
        dd(123);
        $scheme_code = $request->rej_scheme_code;
        $user_id = AuthChecker::getUserId();
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
                'Block/Municipality' => $row->Block_Municipality,
                'Beneficiary_Id' => $row->Beneficiary_Id,
                'Name' => $row->Name,
                'Caste' => $row->Caste,
                'Account_No' => $row->Account_No,
                'IFSC' => $row->IFSC
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
