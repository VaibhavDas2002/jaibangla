<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Configduty;
use Illuminate\Support\Facades\Auth;
use App\Designation;
use App\District;
use App\Taluka;
use App\UrbanBody;
use App\Employee;
use App\Ward;
use App\GP;
use Illuminate\Support\Facades\Log;
use App\Users_audit_trail;
use App\Helpers\AuthChecker;

class EmployeeUserDutyController extends Controller
{

    public function index()
    {
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        ////////////////////////////29-06-2020 sd start/////////////////////////////////////////
        $dutys = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->get(); //first();

        if ($dutys->isEmpty()) {
            return redirect("/")->with('success', 'User Disabled');
        } else {
            if ($designation_id != 'Approver') {
                return redirect("/")->with('success', 'User Disabled');
            }
            $dist_code = $dutys[0]->district_code;
            $results = Configduty::where('district_code', $dist_code)->whereIn('mapping_level', array('Block', 'Subdiv'))
                ->where(function ($query) use ($dutys) {
                    foreach ($dutys as $duty) {
                        $query->orWhere('scheme_id', '=', $duty->scheme_id);
                    }
                })->whereHas('user', function ($q) {
                    $q->whereIn('is_active', array(1));
                })->get();
            //$results=Configduty::where('district_code',$duty->district_code)->whereIn('scheme_id',$schemes)->get();
            //Log::info(json_encode($results));
            return view('emp-user-duty.index')->with('results', $results);
        }
        ////////////////////////////29-06-2020 sd end/////////////////////////////////////////
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $designation_id = Auth::user()->designation_id;
        if ($designation_id != 'Approver') {
            return redirect("/")->with('success', 'User Disabled');
        }
        ////////////////////////////26-06-2020 sd start/////////////////////////////////////////
        /* $schemes=DB::table('m_scheme')->orderby('id')->get();*/
        $user_id = AuthChecker::getUserId();
        /*$duty = Configduty::where('user_id','=',$user_id)->first();*/
        $dutys = Configduty::where('user_id', '=', $user_id)->get();
        //$is_active = $duty->is_active;
        $dist_code = $dutys[0]->district_code; //dd($dutys);
        $schemes = DB::table('m_scheme')->where(function ($query) use ($dutys) {
            foreach ($dutys as $duty) {
                $query->orWhere('id', '=', $duty->scheme_id);
            }
        })->where('is_active', 1)->get();
        //dd($schemes);

        //////////////////////////////26-06-2020 sd end////////////////////////////////////////
        $levels = [
            2 => 'Rural',
            1 => 'Urban',

        ];
        $designations = Designation::where('visible_at_dist_level', 1)->get();
        return view('emp-user-duty.create')
            ->with('dist_code', $dist_code)
            ->with('schemes', $schemes)
            ->with('levels', $levels)
            ->with('designations', $designations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $designation_id = Auth::user()->designation_id;
        if ($designation_id != 'Approver') {
            return redirect("/")->with('success', 'User Disabled');
        }
        $created_by = Auth::user()->id;
        $this->validateInput($request);
        $designation = Designation::where('id', $request['designation_id'])->where('visible_at_dist_level', 1)->first();
        $keys = ['lastname', 'firstname', 'middlename', 'designation_id'];
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
                'designation_id' => $designation->name,
                'user_scheme_id' => $request['scheme_name'],
                'mobile_no' => $request['mobile'],
                'password' => bcrypt('User@123'),
                'login_otp' => 123456,
                'created_by' => $created_by
            ];
            try {
                $user = User::create($user_input);
            } catch (\Exception $e) {
                $msg = 'Duplicate Mobile Number or Email';
                return redirect('emp-user-duty')->with('error', $msg);
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
            $msg = 'The User with Mobile Number ' . $request['mobile'] . ' Succesfully Added';
            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            $msg = 'Some Error.. Please try later';
            return redirect('emp-user-duty')->with('error', $msg);
        }
        return redirect('emp-user-duty')->with('message', $msg);
    }

    private function validateInput($request)
    {
        $this->validate($request, [
            'firstname' => 'required|max:60',
            'designation_id' => 'required|numeric',
            //'mobile' => 'required|numeric|size:10',
            'username' => 'required',
            'email' => 'required|email',
            'schemelist' => 'required',
            'dist_code' => 'required',
            'urban_code' => 'required',
            'body_code'  => 'required'
        ]);
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    ////////////////////////////26-06-2020 sd start/////////////////////////////////////////
    public function enabledisable(Request $request)
    {
        $designation_id = Auth::user()->designation_id;
        if ($designation_id != 'Approver') {
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
        } else {
            DB::rollback();
            $msg = 'Some Error.. Please try later';
        }
        if ($data == 1) {
            return redirect('/emp-user-duty')->with('error', $msg);
        } else {
            return redirect('/emp-user-duty')->with('message', $msg);
        }
    }
    ////////////////////////////26-06-2020 sd end/////////////////////////////////////////
}
