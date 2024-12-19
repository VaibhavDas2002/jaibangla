<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Configduty;
use Auth;
use App\Employee;
use App\Scheme;
use Illuminate\Support\Facades\Log;
use DB;
use App\Users_audit_trail;
use App\Helpers\AuthChecker;


class AddSchemeToExistingUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    ALTER TABLE designation ADD COLUMN visible_at_dist_level integer DEFAULT 0;
    UPDATE designation SET visible_at_dist_level = 1 WHERE id=14;
    UPDATE designation SET visible_at_dist_level = 1 WHERE id=13;
     */
    public function index(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $dutys = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->get(); //first();
        foreach ($dutys as $duty) {
            if ($duty->is_active == 1) {
                $schemes[] = $duty->scheme_id;
            }
        }

        $schemes = Scheme::whereIn('id', $schemes)->get();

        if ($dutys->isEmpty()) {
            return redirect("/")->with('success', 'User Disabled');
        } else {
            if ($designation_id_old != 'Approver' && $designation_id_old != 'HOD' &&  $designation_id_old != 'Admin') {
                return redirect("/")->with('success', 'User Disabled');
            }
            if (request()->ajax()) { //DB::enableQueryLog();
                if (!empty($request->filter_1)) {
                    if (Auth::user()->designation_id_old == 'Approver') {
                        $data = User::leftJoin('employees', 'employees.id', 'users.emp_id')
                            ->leftJoin('duty_assignement', 'duty_assignement.user_id', 'users.id')
                            ->where('duty_assignement.district_code', $dutys[0]->district_code)
                            ->where('mobile_no', $request->filter_1)->where('users.is_active', 1)->limit(1)
                            ->get(['users.username as username', 'employees.firstname as firstname', 'employees.middlename as middlename', 'employees.lastname as lastname', 'users.designation_id_old as designation_id_old', 'users.email', 'users.mobile_no', 'users.id as userid', 'duty_assignement.district_code as district_code', 'duty_assignement.mapping_level as mapping_level', 'duty_assignement.urban_body_code as urban_body_code', 'duty_assignement.is_urban as is_urban', 'duty_assignement.taluka_code as taluka_code']);

                        // New 06-07-2021
                        $all_map_scheme = DB::select(DB::raw("select distinct m.scheme_name from users u join duty_assignement d on u.id=d.user_id join m_scheme m on m.id=d.scheme_id 
                        where d.is_active=1 and m.is_active=1 and d.district_code=" . $dutys[0]->district_code . " and u.mobile_no='" . $request->filter_1 . "'"));
                    } else if (Auth::user()->designation_id_old == 'HOD' || Auth::user()->designation_id_old == 'Admin') {
                        $data = User::leftJoin('employees', 'employees.id', 'users.emp_id')
                            ->leftJoin('duty_assignement', 'duty_assignement.user_id', 'users.id')
                            //->where('duty_assignement.district_code',$dutys[0]->district_code)
                            ->where('mobile_no', $request->filter_1)->where('users.is_active', 1)->limit(1)
                            ->get(['users.username as username', 'employees.firstname as firstname', 'employees.middlename as middlename', 'employees.lastname as lastname', 'users.designation_id_old as designation_id_old', 'users.email', 'users.mobile_no', 'users.id as userid', 'duty_assignement.district_code as district_code', 'duty_assignement.mapping_level as mapping_level', 'duty_assignement.urban_body_code as urban_body_code', 'duty_assignement.is_urban as is_urban', 'duty_assignement.taluka_code as taluka_code']);

                        // New 06-07-2021
                        $all_map_scheme = DB::select(DB::raw("select distinct m.scheme_name from users u join duty_assignement d on u.id=d.user_id join m_scheme m on m.id=d.scheme_id 
                        where d.is_active=1 and m.is_active=1 and u.mobile_no='" . $request->filter_1 . "'"));
                    }

                    // dd(DB::getQueryLog(),$all_map_scheme); 
                } else {
                    $data = collect([]);
                    $all_map_scheme = collect([]);
                }
                return datatables()->of($data, $schemes, $all_map_scheme)
                    ->addColumn('scheme_list', function ($data) use ($schemes) {
                        $options = '';
                        foreach ($schemes as $scheme) {
                            $options .= '<option value=' . '"' . $scheme->id . '"' . '>' . $scheme->scheme_name . '</option>';
                        }
                        $return = '<form class="form-inline" method="post" action="' . route('add-scheme-existing-user-map') . '">
                    <select name="schemelist[]" multiple="multiple" class="form-control select2" required autofocus>' . $options . ' 
                    </select>
                    <input type="hidden" name= "userid" value="' . $data->userid . '">' . csrf_field() . '
                    <input type="hidden" name="dist_code" value="' . $data->district_code . '">
                    <input type="hidden" name="urban_code" value="' . $data->is_urban . '">
                    <input type="hidden" name="mapping_level" value="' . $data->mapping_level . '">
                    <input type="hidden" name="urban_body_code" value="' . $data->urban_body_code . '">
                    <input type="hidden" name="taluka_code" value="' . $data->taluka_code . '">
                    <button type="submit" btn btn-primary btn-md style="margin-top:3%">Map</button>
                    </form>';
                        return $return;
                    })
                    ->addColumn('emp_name', function ($data) {
                        return $data->firstname . ' ' . $data->middlename . ' ' . $data->lastname;
                    })
                    ->addColumn('existing_scheme', function ($data) use ($all_map_scheme) {
                        $html = '';
                        $html .= '<ol style="list-style-type: decimal;">';
                        foreach ($all_map_scheme as $val) {
                            $html .= '<li>' . $val->scheme_name . '</li>';
                        }
                        $html .= '</ol>';
                        return $html;
                    })
                    ->rawColumns(['emp_name', 'existing_scheme', 'scheme_list'])
                    ->make(true);
            }

            return view('add-scheme-existing-user.index'); //->with('results',$results);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function map(Request $request)
    {
        $designation_id_old = Auth::user()->designation_id_old;
        if ($designation_id_old != 'Approver' && $designation_id_old != 'HOD' &&  $designation_id_old != 'Admin') {
            return redirect("/")->with('success', 'User Disabled');
        }
        $user_id = AuthChecker::getUserId();
        $dutys = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->get(); //first();

        $this->validateInput($request);
        $map_duty_data = Configduty::where('user_id', $request->userid)->where('district_code', $dutys[0]->district_code)->first(); //fetching the data of the employee to whom new scheme is mapped

        $scheme_inputs = request()->input('schemelist'); //dd($scheme_inputs);
        DB::beginTransaction();
        try {
            $c_time = date('Y-m-d H:i:s', time());
            foreach ($scheme_inputs as $input) {
                $Configduty = new Configduty;
                $Configduty->created_at = $c_time;
                $Configduty->created_by = $user_id;
                $Configduty->user_id = $request->userid;
                $Configduty->is_urban = $request->urban_code;
                $Configduty->district_code = $request->dist_code;
                //if($request->urban_code) == 1){
                $Configduty->urban_body_code = $request->urban_body_code;
                $Configduty->mapping_level = $request->mapping_level;
                //}else{
                $Configduty->taluka_code = $request->taluka_code;
                //$Configduty->mapping_level = "Block";
                //}
                $Configduty->is_active = 1;
                $Configduty->scheme_id = $input;
                $result = $Configduty->save(); //dd($result);
                if ($request) {
                    $is_saved = true;
                }
            }
            $inserttrail = array(
                'operation_type' => 3,
                'operate_by' => $user_id,
                'operate_to_user_id' => $request->userid,
                'ip_address' => request()->ip(),
                'user_agent' => $request->header('User-Agent'),
                'operation_time' => $c_time
            );
            $trailSave = Users_audit_trail::create($inserttrail);
            $trail_id = $trailSave->id;
            DB::commit();
            return redirect('/add-scheme-existing-user')->with('success', 'Scheme(s) Added To User Successfuly');
        } catch (\Exception $e) {
            DB::rollback();
            $msg = 'Some Error.. Please try later';
            return redirect('/add-scheme-existing-user')->with('error', 'Failure');
        }
    }

    private function validateInput($request)
    {
        $this->validate($request, [
            'userid' => 'required',
            'schemelist' => 'required',
            'dist_code' => 'required',
            //'urban_code' => 'required',
            //'body_code'  => 'required',
            'mapping_level' => 'required',
            //'urban_body_code'=>'required',
            //'taluka_code'=>'required'
        ]);
    }
}
