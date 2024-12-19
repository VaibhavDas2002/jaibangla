<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Designation;
use App\Department;
use App\District;
use App\SubDistrict;
use App\UrbanBody;
use App\Taluka;
use App\Schemetype;
use App\Scheme;
use App\MapLavel;
use App\Service_designation;
use App\User_level;
use App\Configduty;
use App\Users_audit_trail;
use App\Employee;
use Config;
use Exception;
use Carbon;
use DB;
use Validator;
use Auth;
use Excel;
use App\Helpers\AuthChecker;




class userDutymanagementController  extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $designation_id_old = Auth::user()->designation_id_old;
            $has_role = 0;
            $mapping_level_duty = NULL;
            $mapping_level = $district_code = $is_urban = $urban_body_code = $taluka_code = NULL;
            if ($designation_id_old == 'Admin') {
                $has_role = 1;
                $role_loop = 0;
                $mapping_level = NULL;
                $is_urban =  NULL;
                $district_code = NULL;
                $urban_body_code = NULL;
                $taluka_code = NULL;
                $mapping_level_duty = 'State';
            }            else if ($designation_id_old == 'HOD' || $designation_id_old == 'HOP') {
                $has_role = 1;
                $role_loop = 0;
                $mapping_level = NULL;
                $is_urban =  NULL;
                $district_code = NULL;
                $urban_body_code = NULL;
                $taluka_code = NULL;
                $mapping_level_duty = 'State';
            } else {
                $roleArray = $request->session()->get('role');
                //dd($roleArray);
                if (count($roleArray) > 0) {
                    $has_role = 1;
                    $role_loop = 1;
                } else {
                    $has_role = 0;
                    $role_loop = 0;
                }
            }
            if ($has_role) {
                if ($role_loop) {

                    $mapping_level_duty = $roleArray[0]['mapping_level'];
                    $mapping_level =  $roleArray[0]['mapping_level'];
                    if ($roleArray[0]['mapping_level'] == 'State') {
                        $is_urban =  NULL;
                        $district_code = NULL;
                        $urban_body_code = NULL;
                        $taluka_code = NULL;
                    } else if ($roleArray[0]['mapping_level'] == 'District') {
                        $mapping_level = NULL;
                        $is_urban =  NULL;
                        $district_code = $roleArray[0]['district_code'];
                        $urban_body_code = NULL;
                        $taluka_code = NULL;
                    } else if ($roleArray[0]['mapping_level'] == 'Subdiv') {
                        $mapping_level = NULL;
                        $is_urban =  1;
                        $district_code = $roleArray[0]['district_code'];
                        //$urban_body_code =  $roleArray[0]['taluka_code'];
                        $urban_body_code = $roleArray[0]['urban_body_code'];
                        $request->session()->put('subdiv_code', $urban_body_code);

                        $taluka_code = NULL;
                    } else if ($roleArray[0]['mapping_level'] == 'Block') {
                        $mapping_level = 'Block';
                        $is_urban =  2;
                        $district_code = $roleArray[0]['district_code'];
                        $urban_body_code = NULL;
                        $taluka_code = $roleArray[0]['taluka_code'];
                        $request->session()->put('block_munc_corp_code', $taluka_code);
                    }
                }
            }
            $this->designation_id_old = $designation_id_old;
            $request->session()->put('designation_id_old', $designation_id_old);
            $this->has_role = $has_role;
            $this->mapping_level = $mapping_level;
            $this->mapping_level_duty = $mapping_level_duty;
            $this->is_urban = $is_urban;
            $this->role_loop = $role_loop;
            //dd($district_code);
            $this->district_code = $district_code;
            $this->urban_body_code = $urban_body_code;
            $this->taluka_code = $taluka_code;
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        if ($this->has_role) {
            $designation_id_old = $this->designation_id_old;
            if ($designation_id_old == 'Verifier'){
                return redirect("/")->with('success', 'Not Allowded');
            }
            $user_id = AuthChecker::getUserId();

            $errormsg = Config::get('constants.errormsg');
            $departments = Department::where('is_active', 1)->get();
            $district_visible=1;
            $is_urban_visible=0;
            $block_visible=0;
            $subdiv_visible=0;
            $mapping_visible=1;
            $stake_level_home='';
            $role_visible=1;
            $designation_id_old_home='';
            if ($designation_id_old == 'Admin'){
                $schemes = Scheme::where('is_active', 1)->where('is_active', 1)->get();
                $mapping_visible=1;
                $role_visible=1;
            }
            else if ($designation_id_old == 'HOD' || $designation_id_old == 'HOP'){
                $schme_id_in = Configduty::select('scheme_id')->where('user_id', $user_id)->get()->pluck('scheme_id')->toArray();
                $schemes = Scheme::where('is_active', 1)->whereIn('id', $schme_id_in)->get();
                $mapping_visible=1;
                $role_visible=1;
            }

            else {
                $schme_id_in = Configduty::select('scheme_id')->where('user_id', $user_id)->get()->pluck('scheme_id')->toArray();
                $schemes = Scheme::where('is_active', 1)->whereIn('id', $schme_id_in)->get();
                if ($designation_id_old == 'Approver') {
                    $mapping_visible=1;
                    $role_visible=1;
                    $stake_level_home='';
                    $designation_id_old_home='';
                }
                else if ($designation_id_old == 'Verifier') {
                    $mapping_visible=0;
                    $role_visible=0;
                    if($this->is_urban ==1)
                    $stake_level_home='Subdiv';
                    if($this->is_urban ==2)
                    $stake_level_home='Block';
                    $designation_id_old_home='Operator';
                }
                else if ($designation_id_old == 'Operator') {
                    $mapping_visible=1;
                    $role_visible=1;
                }
            }
            $role_arr = Designation::where('is_active', 1)->get();
            $where = [];
            if (!empty($this->district_code))
                $districts = District::where('district_code', $this->district_code)->get();
            else
                $districts = District::get();

            $user_level = User_level::where('is_active', 1)->orderby('rank')->get();
            $levels =Config::get('constants.rural_urban');
            $mapping_level_duty = $this->mapping_level_duty;
            //dd($mapping_level_duty);
            if ($mapping_level_duty == 'State') {
                $is_urban_visible=1;
                $block_visible=1;
                if ($designation_id_old == 'HOD' || $designation_id_old == 'HOP') {
                    $role_arr = $role_arr->whereIn('rank', array(30, 35, 40));
                    //dd($role_arr);
                    $user_level = $user_level;
                } else {
                    $role_arr = $role_arr;
                    $user_level = $user_level;
                }
            } else if ($mapping_level_duty == 'District') {
                $district_visible=0;
                $is_urban_visible=1;
                $block_visible=1;
               
                if ($designation_id_old == 'Approver') {
                    $role_arr = $role_arr->filter(function ($item) {
                        return ($item->name == 'Verifier' || $item->name == 'Operator' || $item->name == 'MIS User');
                    });
                    $user_level = $user_level->filter(function ($item) {
                        return ($item->rank > 20);
                    });
                } else if ($designation_id_old == 'Verifier') {
                    $role_arr = $role_arr->filter(function ($item) {
                        return ($item->name == 'Operator');
                    });
                    $user_level = $user_level->filter(function ($item) {
                        return ($item->id > 2);
                    });
                } else if ($designation_id_old == 'Operator') {
                    $role_arr = collect([]);
                    $user_level = collect([]);
                }
            } else if ($mapping_level_duty == 'Subdiv') {
                $district_visible=0;
                $is_urban_visible=0;
                $block_visible=0;
                
                if ($designation_id_old == 'Approver') {
                    $role_arr = $role_arr->filter(function ($item) {
                        return ($item->name == 'Verifier' || $item->name == 'Operator' || $item->name == 'MIS User');
                    });
                    $user_level = $user_level->filter(function ($item) {
                        return ($item->rank >= 30);
                    });
                } else if ($designation_id_old == 'Verifier') {
                    $role_arr = $role_arr->filter(function ($item) {
                        return ($item->name == 'Operator');
                    });
                    $user_level = $user_level->filter(function ($item) {
                        return ($item->rank >= 30 && $item->rank != 40);
                    });
                } else if ($designation_id_old == 'Operator') {
                    $role_arr = collect([]);
                    $user_level = collect([]);
                }
            } else if ($mapping_level_duty == 'Block') {
                $district_visible=0;
                $is_urban_visible=0;
                $block_visible=0;
                if ($designation_id_old == 'Approver') {
                    $role_arr = $role_arr->filter(function ($item) {
                        return ($item->name == 'Verifier' || $item->name == 'Operator' || $item->name == 'MIS User');
                    });
                    $user_level = $user_level->filter(function ($item) {
                        return ($item->rank >= 40);
                    });
                } else if ($designation_id_old == 'Verifier') {
                    $role_arr = $role_arr->filter(function ($item) {
                        return ($item->name == 'Operator');
                    });
                    $user_level = $user_level->filter(function ($item) {
                        return ($item->rank >= 40);
                    });
                } else if ($designation_id_old == 'Operator') {
                    $role_arr = collect([]);
                    $user_level = collect([]);
                }
            }
            return view('userDutymgmt/index', [
                'districts' => $districts,
                'departments' => $departments,
                'user_levels' => $user_level,
                'levels' => $levels,
                'designation_id_old' => $designation_id_old,
                'roles' => $role_arr,
                'schemes' => $schemes,
                'sessiontimeoutmessage' => $errormsg['sessiontimeOut'],
                'mapping_visible' => $mapping_visible,
                'stake_level_home' => $stake_level_home,
                'role_visible' => $role_visible,
                'designation_id_old_home' => $designation_id_old_home,
                'district_visible' => $district_visible,
                'is_urban_visible' => $is_urban_visible,
                'block_visible' => $block_visible,
                'district_code' =>$this->district_code
            ]);
        } else {
            return redirect("/")->with('success', 'No Duty Assignment assigned yet');
        }
    }
    public function Search(Request $request)
    {
            $user_id = AuthChecker::getUserId();
            $designation_id_old = $this->designation_id_old;
            if($designation_id_old=='Admin'){
                $schme_id_in = Scheme::select('id')->where('is_active',1)->get()->pluck('id')->toArray();
            }
            else
            $schme_id_in = Configduty::select('scheme_id')->where('user_id', $user_id)->where('is_active',1)->get()->pluck('scheme_id')->toArray();
            $schemes = Scheme::where('is_active', 1)->whereIn('id', $schme_id_in)->get();
            $limit = $request->input('length');
            $offset = $request->input('start');
            $totalRecords = 0;
            $filterRecords = 0;

            if ($designation_id_old == 'HOD' || $designation_id_old == 'HOP') {
                $userQuery = User::with(['employee', 'duty'])->whereNotNull('mobile_no');
                $userQuery = $userQuery->whereHas('duty', function ($query1) use ($schme_id_in) {
                    $query1->whereIn('scheme_id',$schme_id_in);
                });
            } else {
                $designation_rank_arr = Designation::where('name', $designation_id_old)->first();
                $designation_id_old_in = Designation::where('is_active', 1)->where('rank', '>', $designation_rank_arr->rank)->get()->pluck('name')->toArray();
                $userQuery = User::with(['employee', 'duty'])->whereNotNull('mobile_no');
                $userQuery = $userQuery->whereHas('duty', function ($query1) use ($schme_id_in) {
                    $query1->whereIn('scheme_id',$schme_id_in);
                });
                if (!empty($this->mapping_level)) {
                    $mapping_level = $this->mapping_level;
                    $userQuery = $userQuery->whereHas('duty', function ($query1) use ($mapping_level) {
                        $query1->where('mapping_level', '=', $mapping_level);
                    });
                }

                if (!empty($this->is_urban)) {
                    $is_urban = $this->is_urban;
                    $userQuery = $userQuery->whereHas('duty', function ($query1) use ($is_urban) {
                        $query1->where('is_urban', '=', $is_urban);
                    });
                }
                if (!empty($this->district_code)) {
                    $district_code = $this->district_code;
                    $userQuery = $userQuery->whereHas('duty', function ($query1) use ($district_code) {
                        $query1->where('district_code', '=', $district_code);
                    });
                }
                if (!empty($this->urban_body_code)) {
                    $urban_body_code = $this->urban_body_code;
                    $userQuery = $userQuery->whereHas('duty', function ($query1) use ($urban_body_code) {
                        $query1->where('urban_body_code', '=', $urban_body_code);
                    });
                }
                if (!empty($this->taluka_code)) {
                    $taluka_code = $this->taluka_code;
                    $userQuery = $userQuery->whereHas('duty', function ($query1) use ($taluka_code) {
                        $query1->where('taluka_code', '=', $taluka_code);
                    });
                }
            }
            $mapping_level = $request->get('mapping_level');
            $scheme_id = $request->get('scheme_id');
            $district_code = $request->get('district_code');
            $is_urban = $request->get('is_urban');
            $block_code = $request->get('block_code');
            $designation_id_old_post = $request->get('designation_id_old');
            if (!empty($mapping_level)) {
                $userQuery = $userQuery->whereHas('duty', function ($query1) use ($mapping_level) {
                    $query1->where('mapping_level', '=', $mapping_level);
                });
            }
            if (!empty($designation_id_old_post)) {
                $userQuery->where('designation_id_old', '=', trim($designation_id_old_post));
            }
            if (!empty($designation_id_old_in)) {
                $userQuery->wherein('designation_id_old', $designation_id_old_in);
            }
            if (!empty($scheme_id)) {
                $userQuery->whereHas('duty', function ($query1) use ($scheme_id) {
                    $query1->where('scheme_id', $scheme_id);
                });
            }
            if (!empty($district_code)) {
                $userQuery->whereHas('duty', function ($query1) use ($district_code) {
                    $query1->where('district_code', $district_code);
                });
            }
            if (!empty($is_urban)) {
                $userQuery->whereHas('duty', function ($query1) use ($is_urban) {
                    $query1->where('is_urban', $is_urban);
                });
            }
            if (!empty($block_code)) {
                $userQuery->whereHas('duty', function ($query1) use ($block_code,$is_urban) {
                    if($is_urban==1)
                    $query1->where('urban_body_code', $block_code);
                    if($is_urban==2)
                    $query1->where('taluka_code', $block_code);
                });
            }
           
            $serachvalue = $request->search['value'];
            if (!empty($serachvalue)) {
                if (is_numeric($serachvalue)) {
                    $userQuery->where('mobile_no', '=',  $serachvalue);
                } 
            }
            $totalRecords = $userQuery->count();
            $data = $userQuery->orderBy('username')->offset($offset)->limit($limit)->get();
            $filterRecords = count($data);
            return datatables()
                ->of($data)
                ->setTotalRecords($totalRecords)
                ->setFilteredRecords($filterRecords)
                ->skipPaging()
                ->addColumn('username', function ($userArray) {
                    return $userArray->username;
                }) ->addColumn('id', function ($userArray) {
                    return $userArray->id;
                })->addColumn('is_active_db', function ($userArray) {
                    return $userArray->is_active;
                })
                ->addColumn('designation_id_old', function ($userArray) {
                    return $userArray->designation_id_old;
                })
                ->addColumn('mobile_no', function ($userArray) {
                    return $userArray->mobile_no;
                })
                ->addColumn('email', function ($userArray) {
                    return $userArray->email;
                })
                ->addColumn('location', function ($userArray) use($designation_id_old) {
                   
                    if(!empty($userArray->duty))
                    {
                       
                            $district_in=array();
                            $block_in=array();
                            $sdo_in=array();
                            $location_text='';
                        
                            foreach($userArray->duty as $location_item){
                                if($location_item->mapping_level=='State' || $location_item->mapping_level=='Department'){
                                    $location_text='NA'; 
                                    break;

                                }
                                else{
                                    if($location_item->mapping_level=='District' || $location_item->mapping_level=='Block' || $location_item->mapping_level=='Subdiv'){
                                        if(!empty($location_item->district_code)){
                                            array_push( $district_in,$location_item->district_code);
                                        }
                                    }
                                    if($location_item->mapping_level=='Block'){
                                        if(!empty($location_item->taluka_code)){
                                            array_push( $block_in,$location_item->taluka_code);
                                        }
                                    }
                                    if($location_item->mapping_level=='Subdiv'){
                                        if(!empty($location_item->urban_body_code)){
                                            array_push( $sdo_in,$location_item->urban_body_code);
                                        }
                                   }
                                }
                            }
                            
                            
                            //dd($district_in);
                            if(count($district_in)>0){
                                $district_in=array_unique($district_in);
                                $district_list=District::whereIn('district_code',$district_in)->pluck('district_name');
                                $district_list_implode= $district_list->implode(',');
                                $location_text= $location_text. ' District: '.$district_list_implode;
                                $location_text= $location_text."<br/>";
                            }
                            if(count($block_in)>0){
                                $block_in=array_unique($block_in);
                                $block_list=Taluka::whereIn('block_code',$block_in)->pluck('block_name');
                                $block_list_implode= $block_list->implode(',');
                                $location_text= $location_text. ' Block: '.$block_list_implode;
                                $location_text= $location_text."<br/>";
                            }
                            if(count($sdo_in)>0){
                                $sdo_in=array_unique($sdo_in);
                                $sdo_list=SubDistrict::whereIn('sub_district_code',$sdo_in)->pluck('sub_district_name');
                                $sdo_list_implode= $sdo_list->implode(',');
                                $location_text= $location_text. ' Sub Div: '.$sdo_list_implode;
                                $location_text= $location_text."<br/>";
                            }
                        
                        
                   
               
            }
                    return $location_text;
                }) ->addColumn('is_active', function ($userArray) {
                    return ($userArray->is_active == 1) ? '<button class="glyphicon glyphicon-ok toggleStatus" id="toggleActivate_' . $userArray->id . '"></button>'
                        : '<button class="glyphicon glyphicon-remove toggleStatus"  id="toggleActivate_' . $userArray->id . '"></button>';
                })->addColumn('schemes', function ($userArray) use($schme_id_in, $schemes,$designation_id_old) {
                    
                    $scheme_text='';
                    $scheme_in_add=array();
                    $scheme_in_u_a=array();
                    $scheme_in_u_d=array();
                    $scheme_text1='';
                    $scheme_text2='';
                    if(!empty($userArray->duty)){
                        $i=0;
                        foreach($userArray->duty as $scheme_item){
                            if(in_array($scheme_item->scheme_id,$schme_id_in)){
                                if($scheme_item->is_active==1){
                                    array_push($scheme_in_u_a,$scheme_item->scheme_id);
                                }
                                else{
                                    array_push($scheme_in_u_d,$scheme_item->scheme_id);
                                }
                            }
                            else{
                                array_push($scheme_in_add,$scheme_item->scheme_id);
                            }
                          $i++;
                        }
                        $duty_scheme=array_merge($scheme_in_u_a,$scheme_in_u_d);
                        $scheme_in_add=array_diff($schme_id_in,$duty_scheme);
                        if(count($scheme_in_add)>0){
                            $scheme_in_add=array_unique($scheme_in_add);
                            $scheme_list=Scheme::whereIn('id',$scheme_in_add)->where('is_active',1)->get();
                            $options = '';
                            foreach ($scheme_list as $scheme) {
                                $options .= '<option value=' . '"' . $scheme->id . '"' . '>' . $scheme->scheme_name . '</option>';
                            }
                            $scheme_text1= $scheme_text1.'<select name="schemelist[]" id="schemelistAdd_'.$userArray->id.'" style="width:300px;" multiple="multiple" class="form-control select2">' . $options . ' </select>&nbsp;<button class="btn btn-success btnMap" user_id="'.$userArray->id.'" id="btnmap_'.$userArray->id.'">Add</button><button type="button"  id="btnmap_submitting_'.$userArray->id.'" class="btn btn-success btnMap1" disabled >Submitting please wait</button><br/><br/>';
                        }
                        if(count($scheme_in_u_a)>0){
                            $scheme_in_u_a=array_unique($scheme_in_u_a);
                            $scheme_list=Scheme::whereIn('id',$scheme_in_u_a)->where('is_active',1)->get();
                            foreach ($scheme_list as $scheme) {
                                $scheme_text2= $scheme_text2."<button  class='glyphicon glyphicon-ok toggleDuty' display_name='".$userArray->username."' scheme_id='".$scheme->id."' scheme_name='".$scheme->scheme_name."' pre_status='Active' new_status='Inactive' user_id='".$userArray->id."' duty_id='".$scheme->id."'></button>&nbsp;&nbsp;".$scheme->scheme_name."<br/><br/>"; 
                            }
                        }
                        if(count($scheme_in_u_d)>0){
                            $scheme_in_u_d=array_unique($scheme_in_u_d);
                            $scheme_list=Scheme::whereIn('id',$scheme_in_u_d)->where('is_active',1)->get();
                            foreach ($scheme_list as $scheme) {
                                $scheme_text2= $scheme_text2."<button class='glyphicon glyphicon-remove toggleDuty'  display_name='".$userArray->username."' scheme_id='".$scheme->id."' scheme_name='".$scheme->scheme_name."' pre_status='Inactive' new_status='Active' user_id='".$userArray->id."' duty_id='".$scheme->id."'></button>&nbsp;&nbsp;".$scheme->scheme_name."<br/><br/>"; 
                            }
                        }
                    }
                    else{
                        $options = '';
                        foreach ($schemes as $scheme) {
                            $options .= '<option value=' . '"' . $scheme->id . '"' . '>' . $scheme->scheme_name . '</option>';
                        }
                        $scheme_text1= $scheme_text1.'<select name="schemelist[]" style="width:300px;" multiple="multiple" class="form-control select2">' . $options . ' </select>&n<button class="btn btn-success">Add</button><br/><br/>';
                    }
                    $scheme_text= $scheme_text1.$scheme_text2;
                   
                    return $scheme_text;
                }) ->addColumn('CanUpdate', function ($userArray) use($schme_id_in, $schemes,$designation_id_old) {
                    //dd($designation_id_old);
                    if($designation_id_old=='Admin'){
                        $canUpdate=1;
                    }
                    else{
                    $user_update_list=array();
                    $canUpdate=0;
                    $scheme_in_active_list=array();
                    if(!empty($userArray->duty)){
                        $i=0;
                        foreach($userArray->duty as $scheme_item){
                            if($scheme_item->is_active==1){
                                array_push($scheme_in_active_list,$scheme_item->scheme_id);
                            }
                           array_push($user_update_list,$scheme_item->scheme_id);
                               
                        }
                       
                    }
                    else{
                        $canUpdate=1;      
                    }
                    
                    if(count($user_update_list)>0){
                        $user_update_list_u=array_unique($user_update_list);
                        $user_update_list_s = Scheme::select('id')->whereIn('id', $user_update_list_u)->where('is_active',1)->get()->pluck('id')->toArray();
                        $result=array_intersect($schme_id_in,$user_update_list_s);
                        
                        if (array_diff($user_update_list_s, $result) == []) {
                            
                            $canUpdate=1;
                        }
                        else{
                            if(count($scheme_in_active_list)==0){
                                $canUpdate=1; 
                            }
                            else{
                            $canUpdate=0; 
                            } 
                        }
                    }
                    else{
                        $canUpdate=1;   
                    }
                }
                    return $canUpdate;
                }) ->addColumn('action', function ($userArray) {
                    // $action = '<a href="javascript:void(0)" class="btn btn-warning col-md-3 btn-margin" onClick="CreatemenuForm('.$menuArray->id.')">Update</a>';
                    $action = '<button class="btn btn-info"  style="cursor:pointer" onClick="return UpdateUserForm(' . $userArray->id . ')" title="Update">Update</button>';
                    return $action;
                })
                ->rawColumns(['is_active','location','schemes','action'])
                ->make(true);
       
    }
  
    public function toggleActivate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        
        if ($validator->passes()) {
            $errormsg = Config::get('constants.errormsg');
            $designation_id_old = $this->designation_id_old;
            $user_id_session = Auth::user()->id;
            $c_time = date('Y-m-d H:i:s', time());
            if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'Verifier' || $designation_id_old == 'Approver') {
                $id = $request[trim('id')];
                $mytime = Carbon\Carbon::now();
                $user_audit_trail_codearr = Config::get('constants.user_audit_trail_code');
                $userArr = User::where('id', $id)->first();
                $dutyarr = Configduty::where('user_id', $id)->get();
                $dutyCount = count($dutyarr);
                if ($userArr->id) {
                    $toggleStatus = TRUE;
                    $toggleMsg = "";
                    if ($userArr->is_active) {
                        $toggleStatus = FALSE;
                        $toggleMsg = "Inactive";
                        $duPlicate = 0;
                    } else {
                        $toggleMsg = "Active";
                        $duPlicate = User::where([
                            ['id', '!=', $id],
                            ['is_active', '=', 1],
                            ['mobile_no', '=', $userArr->mobile_no]
                        ])->count();
                    }
                    if ($duPlicate == 0) {
                        DB::beginTransaction();
                        $affetced1 = User::where('id', $id)->update(['is_active' => $toggleStatus, 'updated_at' => $c_time, 'updated_by' => $user_id_session]);
                        if ($dutyCount) {
                           // $affetced2 = Configduty::where('user_id', $id)->update(['is_active' => $toggleStatus, 'decative_date' => $mytime, 'updated_at' => $c_time, 'updated_by' => $user_id_session]);
                           $affetced2=1;
                        } else {
                            $affetced2 = 1;
                        }
                        $inserttrail = array(
                            'old_user_data' => json_encode($userArr->toArray()),
                            'old_duty_data' => json_encode($dutyarr->toArray()),
                            'operation_type' => $user_audit_trail_codearr['Update'],
                            'unique_id' => $id,
                            'operate_by' => Auth::user()->id,
                            'operate_by_stake_level' => trim($this->mapping_level),
                            'operate_by_ruralurbancode' => intval($this->is_urban),
                            'ip_address' => request()->ip(),
                            'user_agent' => $request->header('User-Agent'),
                            'operation_time' => $mytime
                        );
                        $trailSave = Users_audit_trail::create($inserttrail);
                        $trail_id = $trailSave->id;
                        if ($affetced1 && $affetced2 && $trail_id) {
                            DB::commit();
                            $return_status = 1;
                            $return_msg = $userArr->username . " status successfully changed";
                        } else {
                            DB::rollback();
                            $return_status = 0;
                            $return_text = $errormsg['roolback'];
                            $return_msg = array("" . $return_text);
                        }
                    } else {
                        $return_status = 0;
                        $return_text = "Duplicate Mobile Number";
                        $return_msg = array("" . $return_text);
                    }
                } else {
                    $return_status = 0;
                    $return_text = "No User Exist with this Id";
                    $return_msg = array("" . $return_text);
                }
            } else {
                $return_status = 0;
                $return_text = $errormsg['notauthorized'];
                $return_msg = array("" . $return_text);
            }
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }
        return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    public function adduser(Request $request)
    {
        
            $designation_id_old = $this->designation_id_old;
            if ($designation_id_old == 'Verifier'){
                return redirect("/")->with('success', 'Not Allowded');
            }
            $user_id = AuthChecker::getUserId();
            $errormsg = Config::get('constants.errormsg');
            $district_visible=1;
            $is_urban_visible=0;
            $block_visible=0;
            $subdiv_visible=0;
            $mapping_visible=1;
            $stake_level='';
            $role_visible=1;
            $designation_id_old_sel='';
            $selected_role='Approver';
            if ($designation_id_old == 'Admin'){
                $schemes = Scheme::where('is_active', 1)->get();
                $selected_role='HOD';
                $district_visible=0;
                $is_urban_visible=0;
                $block_visible=0;
            }
            if ($designation_id_old == 'HOD' || $designation_id_old == 'HOP'){
                $schme_id_in = Configduty::select('scheme_id')->where('user_id', $user_id)->get()->pluck('scheme_id')->toArray();
                $schemes = Scheme::where('is_active', 1)->whereIn('id', $schme_id_in)->get();
            }
            else {
                $schme_id_in = Configduty::select('scheme_id')->where('user_id', $user_id)->get()->pluck('scheme_id')->toArray();
                $schemes = Scheme::where('is_active', 1)->whereIn('id', $schme_id_in)->get();
                if ($designation_id_old == 'Approver') {
                    $mapping_visible=1;
                    $role_visible=1;
                    $stake_level='';
                    $designation_id_old_sel='';
                }
                else if ($designation_id_old == 'Verifier') {
                    $mapping_visible=0;
                    $role_visible=0;
                    if($this->is_urban ==1)
                    $stake_level='Subdiv';
                    if($this->is_urban ==2)
                    $stake_level='Block';
                    $designation_id_old_sel='Operator';
                }
                else if ($designation_id_old == 'Operator') {
                    $mapping_visible=1;
                    $role_visible=1;
                }
            }
            $role_arr = Designation::where('is_active', 1)->get();
            $where = [];
            if (!empty($this->district_code))
                $districts = District::where('district_code', $this->district_code)->get();
            else
                $districts = District::get();

            $user_level = User_level::where('is_active', 1)->orderby('rank')->get();
            $levels =Config::get('constants.rural_urban');
            $mapping_level_duty = $this->mapping_level_duty;
            //dd($mapping_level_duty);
            if ($mapping_level_duty == 'State') {
                $is_urban_visible=1;
                $block_visible=1;
                if($designation_id_old == 'Admin'){
                    $selected_role='HOD';
                    $district_visible=0;
                    $is_urban_visible=0;
                    $block_visible=0;
                }
                else
                $selected_role='Approver';
                if ($designation_id_old == 'HOD' || $designation_id_old == 'HOP') {
                    $role_arr = $role_arr->whereIn('rank', array(30, 35, 40));
                    //dd($role_arr);
                    $user_level = $user_level;
                } else {
                    $role_arr = $role_arr;
                    $user_level = $user_level;
                }
            } else if ($mapping_level_duty == 'District') {
                $district_visible=0;
                $is_urban_visible=1;
                $block_visible=1;
                $selected_role='Verifier';
                if ($designation_id_old == 'Approver') {
                    $role_arr = $role_arr->filter(function ($item) {
                        return ($item->name == 'Verifier' || $item->name == 'Operator' || $item->name == 'MIS User');
                    });
                    $user_level = $user_level->filter(function ($item) {
                        return ($item->rank > 20);
                    });
                } else if ($designation_id_old == 'Verifier') {
                    $role_arr = $role_arr->filter(function ($item) {
                        return ($item->name == 'Operator');
                    });
                    $user_level = $user_level->filter(function ($item) {
                        return ($item->id > 2);
                    });
                } else if ($designation_id_old == 'Operator') {
                    $role_arr = collect([]);
                    $user_level = collect([]);
                }
            } else if ($mapping_level_duty == 'Subdiv') {
                $district_visible=0;
                $is_urban_visible=0;
                $block_visible=0;
                //dd('ok');
                if ($designation_id_old == 'Approver') {
                    $role_arr = $role_arr->filter(function ($item) {
                        return ($item->name == 'Verifier' || $item->name == 'Operator');
                    });
                    $user_level = $user_level->filter(function ($item) {
                        return ($item->rank >= 30);
                    });
                } else if ($designation_id_old == 'Verifier') {
                    $selected_role='Operator';
                    $role_arr = $role_arr->filter(function ($item) {
                        return ($item->name == 'Operator');
                    });
                    $user_level = $user_level->filter(function ($item) {
                        return ($item->rank >= 30 && $item->rank != 40);
                    });
                } else if ($designation_id_old == 'Operator') {
                    $role_arr = collect([]);
                    $user_level = collect([]);
                }
            } else if ($mapping_level_duty == 'Block') {
                $district_visible=0;
                $is_urban_visible=0;
                $block_visible=0;
                if ($designation_id_old == 'Approver') {
                    $role_arr = $role_arr->filter(function ($item) {
                        return ($item->name == 'Verifier' || $item->name == 'Operator');
                    });
                    $user_level = $user_level->filter(function ($item) {
                        return ($item->rank >= 40);
                    });
                } else if ($designation_id_old == 'Verifier') {
                    $selected_role='Operator';
                    $role_arr = $role_arr->filter(function ($item) {
                        return ($item->name == 'Operator');
                    });
                    $user_level = $user_level->filter(function ($item) {
                        return ($item->rank >= 40);
                    });
                } else if ($designation_id_old == 'Operator') {
                    $role_arr = collect([]);
                    $user_level = collect([]);
                }
            }
           if($this->is_urban==1){
            $new_block_ulb_code= $this->urban_body_code;
           }
           else if($this->is_urban==2){
            $new_block_ulb_code= $this->taluka_code;
           }
           else{
            $new_block_ulb_code='';
           }
          //dd($selected_role);
        return view('userDutymgmt/adduser', [
            'selected_role' => $selected_role,
            'districts' => $districts,
            'levels' => $levels,
            'designation_id_old' => $designation_id_old,
            'roles' => $role_arr,
            'schemes' => $schemes,
            'sessiontimeoutmessage' => $errormsg['sessiontimeOut'],
            'district_visible' => $district_visible,
            'is_urban_visible' => $is_urban_visible,
            'is_urban' => $this->is_urban,
            'block_visible' => $block_visible,
            'block_code' => $new_block_ulb_code,
            'district_code' =>$this->district_code,
            'mapping_visible' => $mapping_visible,
            'stake_level' => $stake_level,
            'role_visible' => $role_visible,
            'designation_id_old_sel' => $designation_id_old_sel,
        ]);
    }
    public function adduserpost(Request $request)
    {
        $designation_id_old = Auth::user()->designation_id_old;
        if ($designation_id_old == 'Verifier'){
            return redirect("/")->with('success', 'Not Allowded');
        }
        $user_id = AuthChecker::getUserId();
        $assign_designation_id_old = trim($request->designation_id_old);
        if (!in_array($designation_id_old,array('Admin','HOD','Approver','Verifier'))){
            $msg = 'Not Allowded.';
            return redirect('/adduser')->with('error', $msg);
        }
        if ($designation_id_old == 'Admin'){
            if (!in_array($assign_designation_id_old,array('Special LAO','StatusCheckerDistrict','DDO','HOD','HOP','Approver','Verifier','Operator','SpecialStatusCheck'))){
                $msg = 'Not Allowded..';
                return redirect('/adduser')->with('error', $msg);
            }
        }
        if ($designation_id_old == 'HOD'){
            if (!in_array($assign_designation_id_old,array('Approver','Verifier','Operator'))){
                $msg = 'Not Allowded..';
                return redirect('/adduser')->with('error', $msg);
            }
        }
        else if ($designation_id_old == 'Approver'){
            if (!in_array($assign_designation_id_old,array('Verifier','Operator','MIS User'))){
                $msg = 'Not Allowded...';
                return redirect('/adduser')->with('error', $msg);
            }
        }
        else if ($designation_id_old == 'Verifier'){
            if (!in_array($assign_designation_id_old,array('Operator'))){
                $msg = 'Not Allowded....';
                return redirect('/adduser')->with('error', $msg);
            }
        }
        $attributes = array();
        $messages = array();
        $rules = [
            'firstname' => 'required|max:60',
            'lastname' => 'required|max:60',
            'designation_id_old' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|digits:10',
            'schemelist' => 'required',
        ];
        $attributes['firstname'] = 'First Name';
        $attributes['lastname'] = 'Last Name';
        $attributes['designation_id_old'] = 'Role';
        $attributes['username'] = 'Display Name';
        $attributes['email'] = 'Email';
        $attributes['mobile'] = 'Mobile No';
        $attributes['schemelist'] = 'Scheme';
        $attributes['dist_code'] = 'District';
        if (in_array($assign_designation_id_old,array('HOD'))){
           
        }
        if (in_array($assign_designation_id_old,array('Approver'))){
            $rules['dist_code'] = 'required';
            $attributes['dist_code'] = 'District';
            
        }
        if (in_array($assign_designation_id_old,array('Verifier','Operator','MIS User'))){
            $rules['is_urban'] = 'required';
            $rules['block_code'] = 'required';
            $attributes['is_urban'] = 'Rural/Urban';
            $attributes['block_code'] = 'Block/Sub Div';
            $rules['dist_code'] = 'required';
            $attributes['dist_code'] = 'District';
            
        }
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if (!$validator->passes()) {
            $error_msg = array();
            foreach ($validator->errors()->all() as $error) {
                array_push($error_msg, $error);
            }
           //dd( $error_msg);
            return redirect('/adduser')->with('errors', $error_msg);
        } 
        if (in_array($assign_designation_id_old,array('Approver','Verifier','Operator','MIS User'))){
            $district_check=District::where('district_code', trim($request->dist_code))->count();
            if ($district_check==0){
                $msg = 'District Code is invalid';
                return redirect('/adduser')->with('error', $msg);
            }
        }
        if (in_array($assign_designation_id_old,array('Verifier','Operator','MIS User'))){
            if (!in_array(trim($request->is_urban),array_keys(Config::get('constants.rural_urban')))){
                $msg = 'Rural/Urban is invalid';
                return redirect('/adduser')->with('error', $msg);
            }
            if(trim($request->is_urban)==1){
                $ulb_code_check=SubDistrict::where('sub_district_code', trim($request->block_code))->count();
                if ($ulb_code_check==0){
                    $msg = 'Sub District Code is invalid';
                    return redirect('/adduser')->with('error', $msg);
                }
            }
            else if(trim($request->is_urban)==1){
                $ulb_code_check=Taluka::where('block_code', trim($request->block_code))->count();
                if ($ulb_code_check==0){
                    $msg = 'Block Code is invalid';
                    return redirect('/adduser')->with('error', $msg);
                }
            }
        }
        if ($designation_id_old == 'Admin'){
        }
        else{
        $scheme_inputs = request()->input('schemelist');
        foreach ($scheme_inputs as $scheme_id) {
            $duty_count=Configduty::where('user_id',$user_id)->where('scheme_id',$scheme_id)->count();
            if($duty_count==0){
                $msg = 'Not Allowded.....';
                return redirect('/adduser')->with('error', $msg);
                break;

            }
        }
      }
        $designation_arr = Designation::where('name', $assign_designation_id_old)->first();
        if (empty($designation_arr)){
            $msg = 'Designation Code is invalid';
            return redirect('/adduser')->with('error', $msg);
        }
        try {
            $mobile_count=User::where('is_active',1)->where('mobile_no',trim($request['mobile']))->count();
            if ($mobile_count>0){
                $msg = 'Mobile No.. with '.$request['mobile'].' already exists.. please try different';
                return redirect('/adduser')->with('error', $msg);
            }
            $email_count=User::where('is_active',1)->where('email',trim($request['email']))->count();
            if ($email_count>0){
                $msg = 'Email  with '.$request['email'].' already exists.. please try different';
                return redirect('/adduser')->with('error', $msg);
            }
            $c_time = date('Y-m-d H:i:s', time());
            DB::beginTransaction();
            $emp_arr=array();
            $emp_arr['created_by'] = $user_id;
            $emp_arr['firstname'] = trim($request->firstname);
            $emp_arr['middlename'] = trim($request->middlename);
            $emp_arr['lastname'] = trim($request->lastname);
            $emp_arr['designation_id_old'] = $designation_arr->id;
            $emp = Employee::create($emp_arr); 
            if(!empty($emp->id)){
                    $user_input = [
                        'is_active' =>1,
                        'username' => trim($request['username']),
                        'email' => trim($request['email']),
                        'emp_id' => $emp->id,
                        'designation_id_old' => $assign_designation_id_old,
                        'mobile_no' => trim($request['mobile']),
                        'password' => bcrypt('User@123'),
                        'login_otp' => 123456,
                        'created_by' => $user_id
                    ];
            try {
                     $user = User::create($user_input); 
            }
            catch (\Exception $e) {
               // dd($e);
                DB::rollback();
                $msg = 'Duplicate Mobile No or Email.. Please try different.';
                return redirect('/adduser')->with('error', $msg);
            }
             if(!empty($user->id)){
                $i=0;
                $scheme_inputs = request()->input('schemelist');
                    foreach ($scheme_inputs as $input) {
                        $Configduty = new Configduty;
                        $Configduty->created_at = $c_time;
                        $Configduty->created_by = $user_id;
                        $Configduty->user_id = $user->id;
                        if (in_array($assign_designation_id_old,array('HOD','DDO','SpecialStatusCheck'))){
                            $mapping_level='Department';
                        }
                        if (in_array($assign_designation_id_old,array('Approver','Special LAO'))){
                            $mapping_level='District';
                        }
                        if (in_array($assign_designation_id_old,array('Approver','Verifier','Operator','MIS User'))){
                         $Configduty->district_code = $request->input('dist_code');
                        }
                        if (in_array($assign_designation_id_old,array('Verifier','Operator','MIS User'))){
                            $Configduty->is_urban = $request->input('is_urban');
                            if( $request->input('is_urban')==1){
                                $Configduty->urban_body_code = $request->input('block_code');
                                $mapping_level='Subdiv';
                            }
                            else if($request->input('is_urban')==2){
                                $Configduty->taluka_code = $request->input('block_code');
                                $mapping_level='Block';
                            }
                            
                        }
                        $Configduty->mapping_level =  $mapping_level;
                        $Configduty->is_active = 1;
                        $Configduty->scheme_id = $input;
                        if($Configduty->save()){
                         $i++;
                        }
                    }
                    if(count($scheme_inputs)==$i){
                        $inserttrail = array(
                            'operation_type' => 3,
                            'operate_by' => $user_id,
                            'operate_to_user_id' => $user->id,
                            'ip_address' => request()->ip(),
                            'user_agent' => $request->header('User-Agent'),
                            'operation_time' => $c_time
                        );
                        $trailSave = Users_audit_trail::create($inserttrail);
                        $trail_id = $trailSave->id;
                        if($trail_id){
                            $msg = 'The User with Mobile Number ' . $request['mobile'] . ' Succesfully Added';
                            DB::commit();
                            return redirect('userDutymanagement')->with('success', $msg); 

                        }
                    }
                    else{
                        DB::rollback();
                        $msg = 'Some Error.. Please try later';
                        return redirect('/adduser')->with('error', $msg);   
                    }
             }
             else{
                DB::rollback();
                $msg = 'Some Error.. Please try later';
                return redirect('/adduser')->with('error', $msg); 
              }
          }
          else{
            DB::rollback();
            $msg = 'Some Error.. Please try later';
            return redirect('/adduser')->with('error', $msg); 
          }
        }
        catch (\Exception $e) {
           // dd($e);
            DB::rollback();
            $msg = 'Some Error.. Please try later';
            return redirect('/adduser')->with('error', $msg);
        }
      
       
          
    }
    public function getUserInfo(Request $request)
    {
        $id = $request->id;
        $userarrList = array();
        $rules = array(
            'id' => 'required|integer'
        );
        $attributes = [
            'id' => 'User Id',
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
            'integer' => 'Only integer allowed for :attribute'
        ];
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $id = $request->id;
            $userarr = DB::table('users as A')
                ->leftJoin('employees as B', 'A.emp_id', '=', 'B.id')
                ->select(
                    'A.username',
                    'A.designation_id_old',
                    'B.firstname',
                    'B.middlename',
                    'B.lastname',
                    'B.address',
                    'B.department_id',
                    'A.mobile_no',
                    'B.picture',
                    'A.email'
                )->where('A.id', '=', $id)->first();
            if (empty($userarr->username)) {
                $return_status = 0;
                $return_text = "No user Found";
                $return_msg = array("" . $return_text);
                $userarrList = array();
            } else {
                $return_status = 1;
                $return_msg = '';
                $userarrList = json_decode(json_encode($userarr), true);
            }
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }
        return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg, 'userarr' => $userarrList]);
    }
    public function Update(Request $request)
    {
        //$request->merge(array_map('trim', $request->all()));
        $rules = array(
            'firstname' => 'required|max:200',
            'middlename' => 'nullable | max:200',
            'lastname' => 'required|max:200',
            'username' => 'required|max:200',
            'email' => 'required|email',
            'mobile_no' => 'required|size:10'
        );
        $attributes = [
            'firstname' => 'First Name',
            'middlename' => 'Middle Name',
            'lastname' => 'Last Name',
            'email' => 'Email Address',
            'mobile_no' => 'Mobile Number'
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
            'integer' => 'Only integer allowed for :attribute',
            'max' => 'Maximum of :size characters allowed for :attribute',
            'size' => 'The :attribute must be exactly :size.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $errormsg = Config::get('constants.errormsg');
            $designation_id_old = $this->designation_id_old;
            $user_id_session = Auth::user()->id;
            $c_time = date('Y-m-d H:i:s', time());
            if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'Verifier' || $designation_id_old == 'Approver' ) {
                $id = $request['id'];
                $schemeArray = $request['schemeArray'];
                $roleArray = $request['roleArray'];
                // dump($schemeArray);
                $mappinglevelArray = $request['mappinglevelArray'];
                $districtArray = $request['districtArray'];
                $subdivArray = $request['subdivArray'];
                $isurbanArray = $request['isurbanArray'];
                $blockmunccorpArray = $request['blockmunccorpArray'];
                if (empty($request['department_id']))
                    $department_id = NULL;
                else
                    $department_id = $request['department_id'];
                $duPlicateChek = User::where('mobile_no', $request['mobile_no'])->first();
                if ($id) {
                    $userArr = User::where('id', $id)->first();
                    //dd($userArr);
                    if (!empty($userArr->username)) {
                        $duPlicate = User::where([
                            ['id', '!=', $id],
                            ['mobile_no', '=', $request['mobile_no']],
                        ])->count();

                        $dupemail=User::where('is_active',1)->where('id', '!=', $id)->where('email',trim($request['email']))->count();
                        // dd($dupemail);
                        if ($dupemail>0){
                            $return_msg = 'Email  with '.$request['email'].' already exists.. please try different';
                            $return_status = 0;
                            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
                        }


                        if ($duPlicate == 0) {
                            $is_acces=1;
                            if($designation_id_old=='Admin'){
                                $is_acces=1;
                            }
                            else if($designation_id_old=='HOD' && $userArr->designation_id_old=='HOD'){
                                $is_acces=0;
                            }
                            else if($designation_id_old=='Approver' && ($userArr->designation_id_old=='Approver' || $userArr->designation_id_old=='HOD')){
                                $is_acces=0;
                            }
                            else if($designation_id_old=='Verifier' && ($userArr->designation_id_old=='Verifier' || $userArr->designation_id_old=='Approver' || $userArr->designation_id_old=='HOD')){
                                $is_acces=0;
                            }
                            else if($designation_id_old=='Operator' && ($userArr->designation_id_old=='Operator' || $userArr->designation_id_old=='Verifier' || $userArr->designation_id_old=='Approver' || $userArr->designation_id_old=='HOD')){
                                $is_acces=0;
                            }
                            if($is_acces==0){
                            $return_status = 0;
                            $return_text = "Not Allowded";
                            $return_msg = array("" . $return_text);
                            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
                            }
                            $is_final = 0;
                            if ($request['parent_id'] == 0) {
                                $is_final = 1;
                            }
                            $updatearremp = array(
                                'firstname' => trim($request['firstname']),
                                'middlename' => trim($request['middlename']),
                                'lastname' => trim($request['lastname']),
                                'address' => trim($request['address']),
                                'department_id' => $department_id,
                                'updated_by' => $user_id_session,
                                'updated_at' => $c_time
                            );
                            $updatearrmain = array(
                                'username' => trim($request['username']),
                                'email' => trim($request['email']),
                                'mobile_no' => trim($request['mobile_no']),
                                'updated_by' => $user_id_session,
                                'updated_at' => $c_time

                            );
                            if ($request['password'] != null && strlen($request['password']) > 0) {
                                $updatearrmain['password'] = bcrypt($request['password']);
                            }
                            $user_affected = User::where('id', $id)->update($updatearrmain);
                            if (!empty($userArr->emp_id))
                                $emp_affected = Employee::where('id', $userArr->emp_id)->update($updatearremp);
                            else
                                $emp_affected = 1;
                            $user_affected = User::where('id', $id)->update($updatearrmain);
                            $user_audit_trail_codearr = Config::get('constants.user_audit_trail_code');
                            $mytime = Carbon\Carbon::now();
                            $inserttrail = array(
                                'old_user_data' => json_encode($userArr->toArray()),
                                'operation_type' => $user_audit_trail_codearr['Update'],
                                'unique_id' => $id,
                                'operate_by' => Auth::user()->id,
                                'operate_by_stake_level' => trim($this->mapping_level),
                                'operate_by_ruralurbancode' => intval($this->is_urban),
                                'ip_address' => request()->ip(),
                                'user_agent' => $request->header('User-Agent'),
                                'operation_time' => $mytime
                            );
                            $trailSave = Users_audit_trail::create($inserttrail);
                            $trail_id = $trailSave->id;
                            if ($user_affected && $emp_affected) {
                                $return_status = $id;
                                $return_msg = "User with mobile number ". $userArr->mobile_no." Successfully Updated";
                            } else {
                                $return_status = 0;
                                $return_text = $errormsg['roolback'];
                                //$return_text = "Error Occur .. Please try again";
                                $return_msg = array("" . $return_text);
                                //Session::flash('error',$return_text);
                            }
                        } else {
                            $return_status = 0;
                            $return_text = "Mobile Number with " . $request['mobile_no'] . " already tagged with the user " . $duPlicateChek->username;
                            $return_msg = array("" . $return_text);
                        }
                    } else {
                        $return_status = 0;
                        $return_text = "User Not Found";
                        $return_msg = array("" . $return_text);
                    }
                }
            } else {
                $return_status = 0;
                $return_text = $errormsg['notauthorized'];
                $return_msg = array("" . $return_text);
            }
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }
        return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    public function toggleDuty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'scheme_id' => 'required|integer',
        ]);
        if ($validator->passes()) {
            $errormsg = Config::get('constants.errormsg');
            $designation_id_old = $this->designation_id_old;
            $user_id_session = Auth::user()->id;
            $c_time = date('Y-m-d H:i:s', time());
            if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD'  || $designation_id_old == 'HOP' || $designation_id_old == 'Verifier' || $designation_id_old == 'Approver' || $designation_id_old == 'Operator') {
                $user_id = $request[trim('user_id')];
                $scheme_id = $request[trim('scheme_id')];
                $user_audit_trail_codearr = Config::get('constants.user_audit_trail_code');
                $mytime = Carbon\Carbon::now();
                $DutyArr = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
                if (!empty( $DutyArr)) {
                    $toggleStatus = TRUE;
                    $toggleMsg = "";
                    $duplicate = false;
                    if ($DutyArr->is_active) {
                        $toggleStatus = FALSE;
                        $toggleMsg = "Inactive";
                    } else {
                        $toggleMsg = "Active";
                        $chk = array();
                        $chk['user_id'] = $DutyArr->user_id;
                        $chk['is_active'] = 1;
                        $chk['designation_id_old'] = $DutyArr->user->designation_id_old;
                        $chk['scheme_id'] = $DutyArr->scheme_id;
                        $chk['district_code'] = $DutyArr->district_code;
                        $chk['mapping_level'] = $DutyArr->mapping_level;
                        $chk['urban_body_code'] = $DutyArr->urban_body_code;
                        $chk['taluka_code'] = $DutyArr->taluka_code;
                        $chk['is_urban'] = $DutyArr->is_urban;
                        $check = $this->checkDuplicate($chk);
                        if ($check) {
                            $duplicate = false;
                        } else {
                            $duplicate = true;
                        }
                    }
                    if ($duplicate == false) {
                        // DB::beginTransaction();
                        $affetced = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->update(['is_active' => $toggleStatus, 'decative_date' => $mytime, 'updated_at' => $c_time, 'updated_by' => $user_id_session]);
                        $inserttrail = array(
                            'old_duty_data' => json_encode($DutyArr->toArray()),
                            'operation_type' => $user_audit_trail_codearr['Update'],
                            'unique_id' => $DutyArr->id,
                            'operate_by' => Auth::user()->id,
                            'operate_by_stake_level' => trim($this->mapping_level),
                            'operate_by_ruralurbancode' => intval($this->is_urban),
                            'ip_address' => request()->ip(),
                            'user_agent' => $request->header('User-Agent'),
                            'operation_time' => $mytime
                        );
                        $trailSave = Users_audit_trail::create($inserttrail);
                        $trail_id = $trailSave->id;
                        if ($affetced &&  $trail_id) {
                            DB::commit();
                            $return_status = 1;
                            $return_msg = "Duty status successfully changed";
                        } else {
                            DB::rollback();
                            $return_status = 0;
                            $return_text = $errormsg['roolback'];
                            $return_msg = array("" . $return_text);
                        }
                    } else {
                        $return_status = 0;
                        $return_text = "Duplicate Data";
                        $return_msg = array("" . $return_text);
                    }
                } else {
                    $return_status = 0;
                    $return_text = "No Duty Exist with this Id";
                    $return_msg = array("" . $return_text);
                }
            } else {
                $return_status = 0;
                $return_text = $errormsg['notauthorized'];
                $return_msg = array("" . $return_text);
            }
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }
        return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    function checkDuplicate($arr)
    {
        $where = [];
        if ($arr['user_id'] != '')
            $where[] = ['user_id', '=', $arr['user_id']];
        if ($arr['is_active'] != '')
            $where[] = ['is_active', '=', $arr['is_active']];
        if ($arr['scheme_id'] != '')
            $where[] = ['scheme_id', '=', $arr['scheme_id']];
        if ($arr['district_code'] != '')
            $where[] = ['district_code', '=', $arr['district_code']];
        if ($arr['mapping_level'] != '')
            $where[] = ['mapping_level', '=', $arr['mapping_level']];
        if ($arr['urban_body_code'] != '')
            $where[] = ['urban_body_code', '=', $arr['urban_body_code']];
        if ($arr['taluka_code'] != '')
            $where[] = ['taluka_code', '=', $arr['taluka_code']];
        if ($arr['is_urban'] != '')
            $where[] = ['is_urban', '=', $arr['is_urban']];
        $query = Configduty::with(['user'])->where($where);
        $designation_id_old =  $arr['designation_id_old'];

        if (!empty($designation_id_old)) {
            $query = $query->whereHas('user', function ($query1) use ($designation_id_old) {
                $query1->where('designation_id_old', $designation_id_old);
            });
        }
        //dd($designation_id_old);
        if ($query->count() == 0)
            return true;
        else
            return false;
    }
    public function mapNewScheme(Request $request)
    {
        $return_status=0;
        $return_msg='';
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'scheme_id_list' => 'required'
        ]);
        if ($validator->passes()) {
            $designation_id_old = $this->designation_id_old;
            $user_id_session = Auth::user()->id;
            $scheme_id=$request->scheme_id_list;
            $post_user_id=$request->user_id;
            //$scheme_id_in=explode (",", $scheme_id); 
            $scheme_id_in=$scheme_id; 

           
            //dd($scheme_id_in);
            $cnt = Configduty::where('user_id', $user_id_session)
            ->whereIn('scheme_id', $scheme_id_in)
            ->count(DB::raw('DISTINCT scheme_id'));

            // if($post_user_id=='3713')
            // {

            //     dd($cnt, $scheme_id_in);
            // }
            if($cnt!==count($scheme_id_in)){
                return response()->json(['return_status' => 0, 'return_msg' => 'Not Allowded1']);
            }
            $user_row=User::where('id',$post_user_id)->first();
            if(empty($user_row)){
                return response()->json(['return_status' => 0, 'return_msg' => 'User Not Found']);
            }
            if($user_row->is_active==0){
                return response()->json(['return_status' => 0, 'return_msg' => 'User is inActive']);
            }
            $assign_designation_id_old=$user_row->designation_id_old;
            if ($designation_id_old == 'HOD'){
                if (!in_array($assign_designation_id_old,array('Approver','Verifier','Operator'))){
                    return response()->json(['return_status' => 0, 'return_msg' => 'Not Allowded2']);
                }

            } 
            elseif ($designation_id_old == 'Approver'){
                if (!in_array($assign_designation_id_old,array('Verifier','Operator'))){
                    return response()->json(['return_status' => 0, 'return_msg' => 'Not Allowded3']);
                }
                
            } 
           elseif ($designation_id_old == 'Verifier'){
              if (!in_array($assign_designation_id_old,array('Operator'))){
                return response()->json(['return_status' => 0, 'return_msg' => 'Not Allowded4']);
              }
            } 
            $duty_row=Configduty::where('user_id',$post_user_id)->first();
            $errormsg = Config::get('constants.errormsg');
            $c_time = date('Y-m-d H:i:s', time());
            DB::beginTransaction();
            $i=0;
            foreach ($scheme_id_in as $input) {
                $Configduty = new Configduty;
                $Configduty->created_at = $c_time;
                $Configduty->created_by = $user_id_session;
                $Configduty->user_id = $user_row->id;
                if (in_array($assign_designation_id_old,array('HOD'))){
                    $mapping_level='Department';
                }
                if (in_array($assign_designation_id_old,array('Approver'))){
                    $mapping_level='District';
                }
                if (in_array($assign_designation_id_old,array('Approver','Verifier','Operator'))){
                 $Configduty->district_code = $duty_row->district_code;
                }
                if (in_array($assign_designation_id_old,array('Verifier','Operator'))){
                    $Configduty->is_urban = $duty_row->is_urban;
                    if( $duty_row->is_urban==1){
                        $Configduty->urban_body_code = $duty_row->urban_body_code;
                        $mapping_level='Subdiv';
                    }
                    else if($duty_row->is_urban==2){
                        $Configduty->taluka_code = $duty_row->taluka_code;
                        $mapping_level='Block';
                    }
                    
                }
                $Configduty->mapping_level =  $mapping_level;
                $Configduty->is_active = 1;
                $Configduty->scheme_id = $input;
                if($Configduty->save()){
                 $i++;
                }
            }
            if(count($scheme_id_in)==$i){
                $inserttrail = array(
                    'operation_type' => 3,
                    'operate_by' => $user_id_session,
                    'operate_to_user_id' => $user_row->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'operation_time' => $c_time
                );
                $trailSave = Users_audit_trail::create($inserttrail);
                $trail_id = $trailSave->id;
                if($trail_id){
                    $msg = 'Schemes has been added to the Duty for User with Mobile Number ' . $user_row->mobile_no;
                    DB::commit();
                    return response()->json(['return_status' => 1, 'return_msg' => $msg]);

                }
            }
            else{
                DB::rollback();
                $msg = 'Some Error.. Please try later';
                return response()->json(['return_status' => $return_status, 'return_msg' => $msg]);
            }
        }
        else{
            $return_status = 0;
            $return_msg = $validator->errors()->all();
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
}
