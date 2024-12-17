<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\programmeHeadMaster;
//use App\nhm_employee_details;
//use App\designationMaster;
//use App\nhm_service_category;
use App\NHMEmployee;
use App\Configduty;
use App\District;
use App\nhm_posting_level;
use App\nhm_level_place;
use App\nhm_health_facility;
use App\nhm_salary;

use Redirect;
use Auth;

class DDOControllerNHM extends Controller
{
   public function index(){

    $user_id = Auth::user()->id;
    
     $nhm_employee_details = NHMEmployee::paginate(10);//->get();
     //return view('verify_nhm_employee_details');
     //print_r($nhm_employee_details);

     return view('ddo_show_employee_list',['nhm_employee_details' => $nhm_employee_details]);
}


 public function generatePayView(Request $request){

    $user_id = Auth::user()->id;
    $id=$request->id;
   
       
    $single_employee_details = NHMEmployee::where('application_id','=',$id)->first();
    //dd($single_employee_details);    
    
    return view('ddo_generate_employee_pay',['single_employee_details' => $single_employee_details]);

     
}

public function SaveSalary(Request $request){

    $user_id = Auth::user()->id;
    $emp_code=$request->emp_code;
   

   $this->validateInput($request);
        $nhm_employee_salary=new nhm_salary();
        $nhm_employee_salary->application_id=$request->application_id;
        $nhm_employee_salary->emp_code=$request->emp_code;
        //$nhm_employee_salary->ddo_code=$request->ddo_code;
        $nhm_employee_salary->user_id=$user_id;
        $nhm_employee_salary->salary_start_date=$request->salary_start_date;
        $nhm_employee_salary->salary_end_date=$request->salary_end_date;
        $nhm_employee_salary->financial_year=$request->financial_year;
        $nhm_employee_salary->month=$request->month;
        $nhm_employee_salary->consolidation_date_time=$request->consolidation_date_time;
        $nhm_employee_salary->monthly_consolidated_remuneration=$request->monthly_consolidated_remuneration;
        $nhm_employee_salary->arrear_salary=$request->arrear_salary;
        $nhm_employee_salary->bonus=$request->bonus;
        $nhm_employee_salary->other_allowance=$request->other_allowance;
        $nhm_employee_salary->advances=$request->advances;
        $nhm_employee_salary->employers_share_of_epf=$request->employers_share_of_epf;
        $nhm_employee_salary->professional_tax=$request->professional_tax;
        $nhm_employee_salary->income_tax=$request->income_tax;
        $nhm_employee_salary->house_rent=$request->house_rent;
        $nhm_employee_salary->deduction_against_advances=$request->deduction_against_advances;
        $nhm_employee_salary->epf_deductions=$request->epf_deductions;
        $nhm_employee_salary->gross_salary=$request->gross_salary;
        $nhm_employee_salary->net_salary=$request->net_salary;


        $is_saved=$nhm_employee_salary->save();
        //print_r($is_saved);
        $id=$nhm_employee_salary->id;

         $status="Payslip Generated";
         $input = [
            'pay_generation_status' => $status
        ];

        $is_status_updated=NHMEmployee::where('emp_code', $emp_code)->update($input);  
        //dd($nhm_employee_details->id);
        // dd($id);
       // echo($id);
        if($is_saved){
            
            return redirect("ddoemployeelist")->with('success', 'Payslip Generated Successfully')->with('id', 
                $id);
        }

     
}

 private function validateInput($request) {
        $this->validate($request, [
            'application_id' => 'required',
            'emp_code' => 'required|max:60',
            'user_id' => 'required|max:60',
            'salary_start_date' => 'required',
            'salary_end_date' => 'required',
            'financial_year' => 'required',
            'month' => 'required',
            'consolidation_date_time' => 'required',
            'monthly_consolidated_remuneration' => 'required',
            'arrear_salary' => 'required',
            'bonus' => 'required',
            'other_allowance' => 'required',
            'advances' => 'required',
            'employers_share_of_epf' => 'required',
            'professional_tax' => 'required',
            'income_tax' => 'required',
            'house_rent' => 'required',
            'deduction_against_advances' => 'required',
            'epf_deductions' => 'required',
            'gross_salary' => 'required',
            'net_salary' => 'required'
        ]);
    }

}
