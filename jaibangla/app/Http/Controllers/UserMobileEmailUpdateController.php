<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Designation;
use App\Department;
use App\District;
use App\SubDistrict;
use App\UrbanBody;
use App\Block;
use App\Schemetype;
use App\Scheme;
use App\MapLavel;
use App\Service_designation;
use App\User_level;
use App\Configduty;
use App\Users_audit_trail;
use Auth;
use Config;
use Exception;
use Carbon;
use DB;
use Validator;
class UserMobileEmailUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
                    $designation_id_old = Auth::user()->designation_id_old;
                    $has_role=0;
                    if($designation_id_old=='Admin'){
                        $has_role=1;
                        $role_loop=0;
                    }
                    else{
                        $roleArray=$request->session()->get('role');
                        if(count($roleArray)>0){
                        $has_role=1;
                        $role_loop=1;
                        }
                        else
                        $has_role=0;
                    }
                        if($has_role){
                            if($role_loop){
                                foreach ($roleArray as $roleObj) { 
                                    $mapping_level=  $roleObj['mapping_level'];  
                                    $is_urban=  $roleObj['is_urban'];          
                                    if($roleObj['mapping_level'] == 'District'){
                                        $district_code=$roleObj['district_code'];
                                    }
                                    
                                    if($district_code!=''){
                                    break;
                                    }
                                }
                        }
                        }
                        $this->designation_id_old=$designation_id_old;
                        $this->has_role=$has_role;
                        $this->mapping_level=$mapping_level;
                        $this->is_urban=$is_urban;
                        $this->role_loop=$role_loop;
                        $this->district_code=$district_code;
                  return $next($request);
        });
        
       
    }

    public function index(Request $request)
    {
        return redirect("/")->with('success', 'Not Allowded');
        if($this->has_role){
            
            $where = [];
            if(!empty($this->district_code))
            $districts = District::where('district_code', $this->district_code)->get();
            else
             $districts = District::get();
           
            
            
            return view('userMobileEmailUpdate/index', ['districts' => $districts]);
        }   
        else{
            return redirect("/")->with('success', 'No Duty Assignment assigned yet');
        }
        
       
    }   
    public function userManagementSearch(Request $request)
    {
        $where = ''; 
        $whereCount='';
        $orwhere = '';  
        $orwhereCount=[];         
        $limit = $request->input('length');
        if(!$limit)
        $limit=10;
        $offset = $request->input('start');
        if(!$offset)
        $offset=0;
        $designation_id_old = $this->designation_id_old;
        $district_code = $this->district_code;
        $userArray = array();    
        $totalRecords = 0;
        $filterRecords = 0;
       // $where[] = ['B.mapping_level', 'Block'];
        if ($district_code) {
        $whereDistrict = $district_code;
        }
        $stake_level = $request->get('stake_level');
        if ($stake_level){
            $whereMapping = "'$stake_level'";
        }
        else{
            $whereMapping = "'Subdiv','Block'";
            
        }
        $search_value = $request->get('search_value');
        if($search_value){
         if (is_numeric($search_value)){
                   $where = " where B.mobile_no like '%".$search_value."%'";
        }
        else{
                      $where = " where B.username like '%".$search_value."%' or B.email like '%".$search_value."%'" ;
                     
                     // $orwhere =  $orwhere .'B.email', 'like', '%' . $search_value . '%';
         }
        }
       else
       $where='where 1=1';
        $sql = "select A.user_id,B.username,B.email,B.mobile_no,B.designation_id_old from (
            SELECT distinct(user_id) as user_id	
            FROM duty_assignement  where mapping_level IN (".$whereMapping.") and district_code=".$whereDistrict." 
            ) as A JOIN users as B On A.user_id=B.id $where";
            $sql1 =  $sql." offset ".$offset ." limit ".$limit;
            //$sql =  $sql." offset ".$offset ." limit ".$limit;
            $userArray  = DB::select(DB::raw($sql1));
            $userArrayCount  = DB::select(DB::raw($sql));
            $totalRecords = count($userArrayCount);
            $filterRecords = count($userArray);
            return datatables()
            ->of($userArray)
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
            
           
            ->addColumn('action', function ($userArray) {
                // $action = '<a href="javascript:void(0)" class="btn btn-warning col-md-3 btn-margin" onClick="CreatemenuForm('.$menuArray->id.')">Update</a>';
                $action ='<button class="btn btn-primary" style="cursor:pointer" onClick="addUpdateUserForm('.$userArray->user_id.')" title="Update">Update</button>';


                /*$action ='<button class="btn btn-warning ben_view_button" onClick="addUpdateUserForm('.$userArray->id.')">Update</button>
                <button class="btn btn-success" onClick="dutyAssignment('.$userArray->id.',\''.$userArray->username.'\')">Duty Assignment</button>';*/
                return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
       
    }
    public function getUserInfo(Request $request)
    {   
         $id = $request->id; 
         $user = User::where('id', $id)->first()->toArray();
         return response()->json($user); 
    } 
    
    public function updateMobileOrEmail(Request $request)
    {
        return response()->json(['return_status'=>0,'return_msg'=>'Not Allowded']);
        $rules = array(
            'email' => 'required|email',
            'mobile_no' => 'required|size:10'
        );  
        $attributes = [
            
            'email' => 'Email Address',
            'mobile_no' => 'Mobile Number'
        ];  
        $messages = [
            'required' => 'The :attribute field is required.',
            'integer' => 'Only integer allowed for :attribute',
            'max' => 'Maximum of :size characters allowed for :attribute',
            'size' => 'The :attribute must be exactly :size.',
        ];
        $validator = Validator::make($request->all(),$rules,$messages, $attributes);
        if ($validator->passes()) {
            $user_audit_trail_codearr=Config::get('constants.user_audit_trail_code');
            $id=$request['id'];
            if($id){
                $duPlicate = User::where([
                    ['id', '!=', $id],
                    ['mobile_no', '=', $request['mobile_no']],
                ])->count();
                if($duPlicate==0){
                    $userArr = User::where('id', $id)->first()->toArray();
                    DB::beginTransaction();
                    $updatearrmain=array(
                       
                        'email'=>trim($request['email']),
                        'mobile_no'=>trim($request['mobile_no'])
                       );
                    $affected=User::where('id', $id)->update($updatearrmain);
                    $inserttrail=array(
                        'old_username'=>trim($userArr['username']),
                        'old_email'=>trim($userArr['email']),
                        'old_mobile_no'=>trim($userArr['mobile_no']),
                        'old_designation_id_old'=>intval($userArr['designation_id_old']),
                        'operation_type'=>$user_audit_trail_codearr['Update'],
                        'operate_by'=>$id,
                        'operate_by_stake_level'=>trim($this->mapping_level),
                        'operate_by_ruralurbancode'=>intval($this->is_urban),
                        'ip_address'=>request()->ip(),
                        'user_agent'=>$request->header('User-Agent'),
                        'operation_time'=>date('Y-m-d H:i:s', time())
                        );
                    $trailSave=Users_audit_trail::create($inserttrail);
                    $trail_id=$trailSave->id;
                    if($affected && $trail_id){
                        DB::commit();
                        $return_status=$id;
                        $return_msg="Successfully Updated";
                    }
                    else{
                        DB::rollback();
                        $return_status=0;
                        $return_text="Error Occur .. Please try again";
                        $return_msg=array("".$return_text);
                        //Session::flash('error',$return_text);
                     }
                }
                else{
                    $return_status=0;
                    $return_text="Duplicate Mobile Number";
                    $return_msg=array("".$return_text);
                }
                
                
            }
            else{
                $return_status=0;
                $return_text="No User found";
                $return_msg=array("".$return_text);
            }            
                
                    
    }else{
        $return_status=0;
        $return_msg=$validator->errors()->all();
    }
        
            
    
        return response()->json(['return_status'=>$return_status,'return_msg'=>$return_msg]);
    }
    
}