<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
use App\Configduty;
use Auth;
use App\Designation;
use App\District;
use App\SubDistrict;
use App\Taluka;
use App\UrbanBody;
use App\Employee;
use App\Ward;
use App\GP;
use Illuminate\Support\Facades\Log;
use App\Users_audit_trail;
use Validator;

class BSKEmployeeUserDutyManagement extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    public function index() {
        $user_id = Auth::user()->id;
        $designation_id_old = Auth::user()->designation_id_old;
        $dutys = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->get(); 
        if ($dutys->isEmpty()) {
            return redirect("/")->with('success', 'User Disabled');
        } else {
            if ($designation_id_old != 'Approver') {
                return redirect("/")->with('success', 'User Disabled');
            }
            $dutyObj = Configduty::where('user_id', '=', $user_id)->get();
            $dist_code = $dutyObj[0]->district_code; 
            $schemes = DB::table('m_scheme')->where(function ($query) use ($dutyObj) {
                foreach ($dutyObj as $duty) {
                    $query->orWhere('id', '=', $duty->scheme_id);
                }
            })->where('is_active', 1)->whereIn('id', [2])->get();
            $levels = [
                2 => 'Rural',
                1 => 'Urban',
            ];
            $designations = Designation::where('name', 'BSKOperator')->get();
            return view('BSK_emp_user_duty.index')
            ->with('dist_code', $dist_code)
            ->with('schemes', $schemes)
            ->with('levels', $levels)
            ->with('designations', $designations);
        }
    }
    public function bskEmpUserGetData(Request $request) {
        if ($request->ajax()) {
            // dd($request->all());
            $user_id = Auth::user()->id;
            $designation_id_old = Auth::user()->designation_id_old;
            $dutys = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->get();

            if ($dutys->isEmpty()) {
                return redirect("/")->with('success', 'User Disabled');
            } else {
                if ($designation_id_old != 'Approver') {
                    return redirect("/")->with('success', 'User Disabled');
                }
                $dist_code = $dutys[0]->district_code;
                // dd($dist_code);
                $data = DB::connection('pgsql_mis')->select("SELECT u.username,u.mobile_no,u.email,u.designation_id_old,d.* ,s.scheme_name FROM public.users u JOIN public.duty_assignement d ON u.id=d.user_id 
                    JOIN public.m_scheme s ON s.id=d.scheme_id 
                    WHERE u.designation_id_old='BSKOperator' 
                    AND d.district_code=". $dist_code);

                // dd($data);

                return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    if($data->is_active == 1) {
                        $html = '<a class="btn btn-danger btn-sm" id="click_to_disabled" onclick="clickToDisabled(' . $data->id .')">Click to Disable</a>'; 
                    }
                    else {
                        $html = '<a class="btn btn-success btn-sm" id="click_to_enabled"  onclick="clickToEnabled(' . $data->id .')">Click to Enable</a>'; 
                    }
                    return $html;
                })
                ->addColumn('mapping_level', function ($data) {
                    return $data->mapping_level;
                })
                ->addColumn('location', function ($data) {
                    $district = ''; $block = ''; $subdiv = '';
                    $district = District::where('district_code', $data->district_code)->first();
                    $block = Taluka::where('block_code', $data->taluka_code)->first();
                    $subdiv = SubDistrict::where('sub_district_code', $data->urban_body_code)->first();
                    if ($data->mapping_level == "Block") {
                        $msg = "District : " . $district->district_name . " , Block: " . $block->block_name . ",
                            Scheme : ".$data->scheme_name;
                    }
                    else if ($data->mapping_level == "Subdiv") {
                      if($data->is_urban == 1) {
                        $msg = "District : " . $district->district_name . " , Sub Div: " . $subdiv->sub_district_name . ",
                        Scheme: " . $data->scheme_name;                          
                      }
                    }
                    return $msg;
                })
                ->addColumn('designation', function ($data) {
                    return $data->designation_id_old;
                })
                ->addColumn('username', function ($data) {
                    return $data->username;
                })
                ->addColumn('mobile_no', function ($data) {
                    return $data->mobile_no;
                })
                ->addColumn('email', function ($data) {
                    return $data->email;
                })
                ->addColumn('current_status', function ($data) {
                    return $data->is_active == 1 ? '<span class="text-success"><b>Enabled</b></span>' : '<span class="text-danger"><b>Disabled</b></span>';
                })
                ->rawColumns(['action', 'mapping_level', 'location', 'designation', 'username', 'mobile_no', 'email', 'current_status'])
                ->make(true);
            }
        }
    }
    public function bskAddUserEmp(Request $request) {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
          $statusCode = 400;
          $response = array('error' => 'Error occured in form submit.');
          return response()->json($response, $statusCode);
        }
        try { 
            // echo 'Start Fun'; 
            /*=========== Validation Section ==========*/
            $rules = array(
                'firstname' => 'required|max:60',
                'username' => 'required',
                'email' => 'required|unique:users,email',
                'mobile_no' => 'required|digits:10|unique:users,mobile_no',
                'designation_id_old' => 'required',
                'urban_code' => 'required',
                'body_code'  => 'required',
                'schemelist' => 'required'
              );  
              $attributes = [
                'firstname' => 'First name',
                'username' => 'User Name',
                'email' => 'Email',
                'mobile_no' => 'Mobile No',
                'designation_id_old' => 'Role',
                'urban_code' => 'Rural/ Urban',
                'body_code'  => 'Block/ Sub-division',
                'Scheme' => 'Scheme'
              ];  
              $messages = [
                'required' => 'The :attribute field is required.',
                'integer' => 'Only integer allowed for :attribute',
                'max' => 'Maximum of :size characters allowed for :attribute',
                'size' => 'The :attribute must be exactly :size.',
              ];
            $validator = Validator::make($request->all(),$rules,$messages, $attributes);
            if ($validator->passes()) {
                $designation_id_old = Auth::user()->designation_id_old;
                if ($designation_id_old != 'Approver') {
                    return redirect("/")->with('success', 'User Disabled');
                }
                $created_by = Auth::user()->id;
                $designation = Designation::where('id', $request['designation_id_old'])->first();
                $keys = ['lastname', 'firstname', 'middlename', 'designation_id_old'];
                $input = $this->createQueryInput($keys, $request);
                $input['created_by'] = $created_by;
                DB::beginTransaction();
                try {
                    $emp = Employee::create($input);
                    $is_saved = false;
                    $c_time = date('Y-m-d H:i:s', time());
                    $user_input = [
                        'username' => $request['username'],
                        'email' => $request['email'],
                        'emp_id' => $emp->id,
                        'designation_id_old' => $designation->name,
                        // 'user_scheme_id' => $request['scheme_name'],
                        'mobile_no' => $request['mobile_no'],
                        'password' => bcrypt('User@123'),
                        'login_otp' => 123456,
                        'created_by' => $created_by,
                        'mob_encrypt' => $request['mobile_no']
                    ];
                    try {
                        $user = User::create($user_input);
                    } catch (\Exception $e) {
                        $msg = 'Duplicate Mobile Number or Email';
                        $response = array(
                          'status' => 0, 'msg' => $msg,
                          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                        );
                    }

                    $scheme_inputs = request()->input('schemelist');
                    foreach ($scheme_inputs as $input) {
                        $Configduty = new Configduty;
                        $Configduty->created_at = $c_time;
                        $Configduty->created_by = $created_by;
                        $Configduty->user_id = $user->id;
                        $Configduty->is_urban = $request->input('urban_code');
                        $Configduty->district_code = $request->input('dist_code');
                        if ($request->input('urban_code') == 1) {
                            $Configduty->urban_body_code = $request->input('body_code');
                            $Configduty->mapping_level = "Subdiv";
                        } else {
                            $Configduty->taluka_code = $request->input('body_code');
                            $Configduty->mapping_level = "Block";
                        }
                        $Configduty->is_active = 1;
                        $Configduty->scheme_id = $input;
                        $result = $Configduty->save();
                    }

                    $inserttrail = array(
                        'operation_type' => 3,
                        'operate_by' => $created_by,
                        'operate_to_user_id' => $user->id,
                        'ip_address' => request()->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'operation_time' => $c_time
                    );
                    $trailSave = Users_audit_trail::create($inserttrail);
                    $trail_id = $trailSave->id;
                    $msg = 'The ' . $user->designation_id_old . ' with Mobile Number ' . $request['mobile_no'] . ' Succesfully Added';
                    $response = array(
                      'status' => 1, 'msg' => $msg,
                      'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                    );
                    DB::commit();
                } catch (\Exception $e) {
                    dd($e);
                    DB::rollback();
                    $msg = 'Some Error.. Please try later';
                    $response = array(
                      'status' => 0, 'msg' => $msg,
                      'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                    );
                }
                return redirect('emp-user-duty')->with('message', $msg);
            }
            else{
                $return_status=0;
                $return_msg=$validator->errors()->all();
                $response = array(
                  'status' => $return_status, 'msg' => $return_msg,
                  'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                );
            }
        } catch(\Exception $e) {
            // dd($e);
          $response = array(
            'exception' => true,
            'status' => 0,
            'msg' => $e->getMessage(),
            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
          );
          $statusCode = 400;
        } finally {
          return response()->json($response, $statusCode);
        }
    }

    public function enabledDisabled(Request $request) {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
          $statusCode = 400;
          $response = array('error' => 'Error occured in form submit.');
          return response()->json($response, $statusCode);
        }
        try { 
            $designation_id_old = Auth::user()->designation_id_old;
            if ($designation_id_old != 'Approver') {
                return redirect("/")->with('success', 'User Disabled');
            }
            $created_by = Auth::user()->id;
            $id = $request->id;
            $configduty = Configduty::findOrFail($id);
            $data = $configduty->is_active;
            $c_time = date('Y-m-d H:i:s', time());
            if ($data == 1) {
                $input = [
                    'is_active' => 0,
                    'updated_at' =>  $c_time,
                    'updated_by' => $created_by,
                ];
                $msg = 'The User has been Disabled Succesfully';
            } else {
                $input = [
                    'is_active' => 1,
                    'updated_at' =>  $c_time,
                    'updated_by' => $created_by
                ];
                $msg = 'The User has been Enabled Succesfully';
            }
            DB::beginTransaction();
            $inserttrail = array(
                'operation_type' => 2,
                'operate_by' => $created_by,
                'operate_to_user_id' => $id,
                'ip_address' => request()->ip(),
                'user_agent' => $request->header('User-Agent'),
                'operation_time' => date('Y-m-d H:i:s', time())
            );
            $trailSave = Users_audit_trail::create($inserttrail);
            $trail_id = $trailSave->id;
            $affected = Configduty::where('id', $id)
                ->update($input);
            if ($affected && $trail_id) {
                DB::commit();
                $response = array(
                  'status' => 1, 'msg' => $msg,
                  'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                );
            } else {
                DB::rollback();
                $msg = 'Some Error.. Please try later';
                $response = array(
                  'status' => 0, 'msg' => $msg,
                  'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                );
            }
        } catch(\Exception $e) {
            // dd($e);
          $response = array(
            'exception' => true,
            'status' => 0,
            'exception_message' => $e->getMessage(),
            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
          );
          $statusCode = 400;
        } finally {
          return response()->json($response, $statusCode);
        }
    }

    private function createQueryInput($keys, $request)
    {
        $queryInput = [];
        for ($i = 0; $i < sizeof($keys); $i++) {
            $key = $keys[$i];
            $queryInput[$key] = $request[$key];
        }

        return $queryInput;
    }
}
