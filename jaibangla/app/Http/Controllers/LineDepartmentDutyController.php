<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
use App\Configduty;
use Auth;
use App\Designation;
use App\District;
use App\Taluka;
use App\UrbanBody;
use App\Employee;
use App\Ward;
use App\GP;
use App\Department;
use Illuminate\Support\Facades\Log;

class LineDepartmentDutyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    ALTER TABLE designation ADD COLUMN visible_at_dist_level integer DEFAULT 0;
    UPDATE designation SET visible_at_dist_level = 1 WHERE id=14;
    UPDATE designation SET visible_at_dist_level = 1 WHERE id=13;
     */
    public function index()
    {
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();
        //Log::info($duty->district_code);
        $is_active = $duty->is_active;
        $results=Configduty::where('urban_body_code',$duty->urban_body_code)->get();
        Log::info(json_encode($results));
        return view('line-dept-duty.index')->with('results',$results);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        //$schemes=DB::table('m_scheme')->orderby('id')->get();
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();
        //$is_active = $duty->is_active;
        //$dist_code = $duty->district_code;
        $dept_code = $duty->urban_body_code;
        $schemes = Configduty::where('user_id','=',$user_id)->get();

               
        $departments=Department::where('id',$dept_code)->get();
        $designations= Designation::where('visible_at_dist_level',2)->get();       
        return view('line-dept-duty.create')
            //->with('dist_code',$dist_code)
            ->with('schemes',$schemes)
            //->with('levels',$levels)
            ->with('departments',$departments)
            ->with('designations',$designations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    
        $this->validateInput($request);
        $designation = Designation::where('id',$request['designation_id_old'])->where('visible_at_dist_level',2)->first();
        $keys = ['lastname', 'firstname', 'middlename', 'designation_id_old'];
        $input = $this->createQueryInput($keys, $request);        
        $emp = Employee::create($input);
        $is_saved=false;
        if($emp->id){
            $user_input = [            
            'username' => $request['username'],
            'email' => $request['email'],
            'emp_id' => $emp->id,
            'designation_id_old' => $designation->name,
            'user_scheme_id' => $request['scheme_name'],
            'mobile_no' => $request['mobile'],
            'password' => bcrypt('User@123'),
            'login_otp' => 123456
            ];
            $user = User::create($user_input);
            if($user->id){
                $scheme_inputs=request()->input('schemelist');
                foreach($scheme_inputs as $input){
                    $Configduty = new Configduty;
                    $Configduty->user_id = $user->id;                
                    //$Configduty->is_urban = $request->input('urban_code');                    
                    //$Configduty->district_code = $request->input('dist_code');
                    //if($request->input('urban_code') == 1){
                        $Configduty->urban_body_code = $request->input('department');
                        $Configduty->mapping_level = "Department";
                    //}else{
                      //  $Configduty->taluka_code = $request->input('body_code');
                      //  $Configduty->mapping_level = "Block";
                    //}
                    $Configduty->is_active = 1;
                    $Configduty->scheme_id=$input;
                    $result = $Configduty->save();
                    if($request){
                        $is_saved = true;
                    }
                }
            }
        }
        if($is_saved == false){
            Employee::where('id', $emp->id)->delete();
            User::where('id', $user->id)->delete();
        }
        
        return redirect('line-dept-duty');
        
    }

    private function validateInput($request) {
        $this->validate($request, [            
            'firstname' => 'required|max:60',            
            'designation_id_old' => 'required|numeric',
            //'mobile' => 'required|numeric|size:10',
            'username' => 'required',
            'email' => 'required|email',
            'schemelist' => 'required',
            //'dist_code' => 'required',
            //'urban_code' => 'required',
            'department'  => 'required'
        ]);
    }

    private function createQueryInput($keys, $request) {
        $queryInput = [];
        for($i = 0; $i < sizeof($keys); $i++) {
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
}
