<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\nhm_employee_details;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\Helpers\AuthChecker;


class EmployeeReportGeneration extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

         if(Auth::user()->designation_id == 'Admin'){
        
        $report = DB::table('nhm_employee_details')->paginate(5);
        
        }
        else
        {

                $user_id = AuthChecker::getUserId();
                $duty = Configduty::where('user_id','=',$user_id)->first();

                if($duty->mapping_level=="State HQ"){
                    $body_code = 1;
                }else if($duty->mapping_level=="District HQ"){
                    $body_code = $duty->district_code;
                }else{
                    //$nhm_employee_details->is_urban = $duty->is_urban;
                    if($duty->is_urban==1){
                        $body_code = $duty->urban_body_code;
                    }else{
                        $body_code = $duty->taluka_code;
                    }   
                } 
            if(Auth::user()->designation_id == 'Operator')
            {
             $report = nhm_employee_details::where('body_code','=',$body_code)->paginate(10);//->get();
            }
            if(Auth::user()->designation_id == 'Verifier')
            {
             $report = nhm_employee_details::where('body_code','=',$body_code)->paginate(10);//->get();
            }
            if(Auth::user()->designation_id == 'Approver')
            {
             $report = nhm_employee_details::where('body_code','=',$body_code)->where('verification_status','=','Verified')->paginate(10);//->get();
            }
        }
   //dd($report);
     //return view('verify_nhm_employee_details');
     //print_r($nhm_employee_details);

    // return view('verify_nhm_employee_details',['nhm_employee_details' => $nhm_employee_details]);

     //$report = nhm_employee_details::All();
        //$report = DB::table('nhm_employee_details')->paginate(5);
        return view('employee-report/index', ['reports' => $report]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
