<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\nhm_employee_details;
use App\District;
use App\UrbanBody;
use App\Taluka;
use App\nhm_health_facility;
use App\SubDistrict;
use App\NHMEmployee;


class NHMEmployeeDrilldownReportController extends Controller
{
    
	    public function index()
    {
        //$report = nhm_employee_details::All();
        $reports = DB::table('nhm_employee_details')->paginate(5);
        $districts = District::all();
        //$flag=0;
        return view('employee-report-drilldown/index', ['reports' => $reports,'districts'=>$districts]);

       // return view('employee-report-drilldown/index');
    }


    public function loadlevel2(Request $request, $level_name){
        //dd($level_name);
        if ($level_name=="State"){
             $list=[array("id"=>1,"name"=>"West Bengal")];

        }elseif ($level_name=="District") {
             //$list=District::where('is_revenue_district','=','1')->get(['district_code AS id','district_name AS name']);
            $list=DB::table('m_district')->whereNotIn('district_name', ['Others'])->get(['district_code AS id','district_name AS name']);
            
        }elseif($level_name=="ULB"){

            $list=DB::table('m_urban_body')->whereNotIn('urban_body_code',[801715,801739,801670,801673,801672])->get(['urban_body_code AS id','urban_body_name AS name']);
            //$list=UrbanBody::get(['urban_body_code AS id','urban_body_name AS name']);
            
        }else{
            $list=Taluka::get(['taluka_code AS id','taluka_name AS name']);
             
        }
       

        return response()->json($list);
    }

    public function loadlevel2d(Request $request,$id,$level_name){
        //dd($level_name);
        
        if($level_name=="ULB"){
            $list=DB::table('m_urban_body')->where('district_code','=',$id)->whereNotIn('urban_body_code',[801715,801739,801670,801673,801672])->get(['urban_body_code AS id','urban_body_name AS name']);
        }elseif($level_name=="Block"){
             $list=DB::table('m_taluka')->where('district_code','=',$id)->get(['taluka_code AS id','taluka_name AS name']);
            // Taluka::get(['taluka_code AS id','taluka_name AS name']);
        }

        
       

        return response()->json($list);
    }


    public function loadlevel3(Request $request, $level_name){
        //dd($level_name);
        if ($level_name=="State"){
             $list=array("SPMU","MCH","SSH","Other Hospital","State Institute of Health and Family Welfare","State Drug Store");

        }elseif ($level_name=="District") {
             $list=array("DPMU","Hospital","DH","SDH","PHC","ULB","ACMOH Office","SGH","State Drug Store","SSH","Other Hospital","MCH","CHC");
        }elseif($level_name=="ULB"){
             $list=array("CPMU","UPHC","ULB");
            
        }else{
            $list=array("BPMU","Subcenter","PHC","CHC");
             
        }
       

        return response()->json($list);
    }

     // public function loadlevel4(Request $request,$reprotlevel1_data,$reprotlevel2_data,$reprotlevel2d_data,$reprotlevel3_data){
    public function loadlevel4(Request $request){
        //dd($level_name);
        $level1=$request->reportlevel1_data;
        $level2=$request->reportlevel2_data;
        $level2d=$request->reportlevel2d_data;
        $level3=$request->reportlevel3_data;
        //dd($request->$reprotlevel1_data);echo($level2);echo($level2d);echo($level3);
        if ($level1=="State"){
            if($level3=="SPMU"){            
                $postingPlaces=[array("id"=>-1,"name"=>"No Data")]; 
            }elseif($level3=="MCH"){
                $facility_type=["MCH"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',342)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="SSH"){
                $facility_type=["SSH"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',342)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="Other Hospital"){
                $facility_type=["Others"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',342)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="State Drug Store"){
                $postingPlaces=[array("id"=>-1,"name"=>"No Data")];
            }elseif($level3=="State Institute of Health and Family Welfare"){
                $postingPlaces=[array("id"=>-1,"name"=>"No Data")];
            }





        }elseif ($level1=="District") {
            if($level3=="DPMU"){            
                $postingPlaces=[array("id"=>-1,"name"=>"No Data")]; 
            }elseif($level3=="Hospital"){

                 $facility_type=["DH","MCH","SDH","SGH"];
                 $postingPlaces = nhm_health_facility::where('district_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="DH"){

                 $facility_type=["DH"];
                 $postingPlaces = nhm_health_facility::where('district_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="SDH"){

                 $facility_type=["SDH"];
                 $postingPlaces = nhm_health_facility::where('district_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="PHC"){

                 $facility_type=["PHC"];
                 $postingPlaces = nhm_health_facility::where('district_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="ULB"){

                 //$facility_type=["ULB"];
                 $postingPlaces =UrbanBody::where('district_code','=',$level2)->get(['urban_body_code as id','urban_body_name as name']);
                 //$postingPlaces = nhm_health_facility::where('district_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="ACMOH Office"){

                 $postingPlaces=SubDistrict::where('district_code','=',$level2)->get(['sub_district_code as id','sub_district_name as name']);
                 // $facility_type=["PHC"];
                 // $postingPlaces = nhm_health_facility::where('district_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="SGH"){

                 $facility_type=["SGH"];
                 $postingPlaces = nhm_health_facility::where('district_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="SSH"){

                 $facility_type=["SSH"];
                 $postingPlaces = nhm_health_facility::where('district_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="Other Hospital"){

                 $facility_type=["Others"];
                 $postingPlaces = nhm_health_facility::where('district_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="MCH"){

                 $facility_type=["MCH"];
                 $postingPlaces = nhm_health_facility::where('district_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="CHC"){

                 $facility_type=["CH"];
                 $postingPlaces = nhm_health_facility::where('district_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="State Drug Store"){
                $postingPlaces=[array("id"=>-1,"name"=>"No Data")];
            }


        }elseif($level1=="ULB"){
             if($level3=="CPMU"){            
                $postingPlaces=[array("id"=>-1,"name"=>"No Data")]; 
            }elseif($level3=="UPHC"){
                 $facility_type=["UPHC"];  
                 $postingPlaces = nhm_health_facility::where('district_code','=',$level2d)->where('taluka_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="ULB"){
                 $postingPlaces=[array("id"=>-1,"name"=>"No Data")];
                 //$postingPlaces =UrbanBody::where('district_code','=',$level2d)->get(['urban_body_code as id','urban_body_name as name']);
                 //$facility_type=["UPHC"];  
                 //$postingPlaces = nhm_health_facility::where('taluka_code', '=', $level2)->where('district_code','=',$level2d)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }
          
        }else{
           
             if($level3=="BPMU"){            
                $postingPlaces=[array("id"=>-1,"name"=>"No Data")]; 
            }elseif($level3=="Subcenter"){
                 $facility_type=["SC"];
                 $postingPlaces = nhm_health_facility::where('district_code','=',$level2d)->where('taluka_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="PHC"){
                 $facility_type=["PHC"];
                 $postingPlaces = nhm_health_facility::where('district_code','=',$level2d)->where('taluka_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }elseif($level3=="CHC"){
                 $facility_type=["CH"];
                 $postingPlaces = nhm_health_facility::where('district_code','=',$level2d)->where('taluka_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }
        }
       

        return response()->json($postingPlaces);
    }



public function loadreportafterlevel2(Request $request){


        $level1=$request->level1;
        //dd($level1);
        $level2=$request->level2;
        
        $flag=1;
        $constraints = [
            'level1' => $request['level1'],
            'level2' => $request['level2'],
            ];
        
       //dd($constraints);
        if($level1=='State'){

            $applications_received=DB::table('nhm_employee_details')->count();
            $applications_verified=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->count();
            $applications_rejected=DB::table('nhm_employee_details')->where('verification_status','=','Rejected')->count();
            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('approval_status','=','Approved')->count();
            $applications_pending_for_approval=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();
            $applications_rejected_at_approval=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();
           
           $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_approval,
            'applications_rejected_at_approval'=>$applications_rejected_at_approval,
           ];
            

        //dd($result);

        }
         elseif($level1=='District'){

            $district_code=$level2;
            // $sub_district_codes=DB::table('m_sub_district')->where('district_code','=',$district_code)->pluck('sub_district_code');
            $urban_body_codes=DB::table('m_urban_body')->where('district_code','=',$district_code)->pluck('urban_body_code');
            $taluka_codes=DB::table('m_taluka')->where('district_code','=',$district_code)->pluck('taluka_code');
            
            /******************************APPLICATIONS RECEIVED************************************/
            $applications_received_district=DB::table('nhm_employee_details')->Where('body_code','=',$district_code)->count();


            $total_applications_received_taluka=0;
            foreach ($taluka_codes as $taluka_code)
            {
                $applications_received_taluka=DB::table('nhm_employee_details')->Where('body_code','=',$taluka_code)->count();
                 //print_r($applications_received);
                $total_applications_received_taluka=$total_applications_received_taluka+$applications_received_taluka;
            }

            //dd($total_applications_received_taluka);

            $total_applications_received_urbanbody=0;
            foreach ($urban_body_codes as $urban_body_code)
            {
                $applications_received_urbanbody=DB::table('nhm_employee_details')->Where('body_code','=',$urban_body_code)->count();
                 //print_r($applications_received);
                $total_applications_received_urbanbody=$total_applications_received_urbanbody+$applications_received_urbanbody;
            }
            //dd($total_applications_received_urbanbody);

            $total_applications_received_Final=$applications_received_district+$total_applications_received_taluka+$total_applications_received_urbanbody;
            //dd($total_applications_received_Final);
           /********************************end**********************************************/

           /******************************APPLICATIONS VERIFIED************************************/
            
            $applications_verified_district=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('verification_status','=','Verified')->count();
            
            $total_applications_verified_taluka=0;
            foreach ($taluka_codes as $taluka_code)
            {
                $applications_verified_taluka=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('verification_status','=','Verified')->count();
                 //print_r($applications_received);
                $total_applications_verified_taluka=$total_applications_verified_taluka+$applications_verified_taluka;
            }

            //dd($total_applications_received_taluka);

            $total_applications_verified_urbanbody=0;
            foreach ($urban_body_codes as $urban_body_code)
            {
                $applications_verified_urbanbody=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('verification_status','=','Verified')->count();
                 //print_r($applications_received);
                $total_applications_verified_urbanbody=$total_applications_verified_urbanbody+$applications_verified_urbanbody;
            }
            //dd($total_applications_received_urbanbody);

            $total_applications_verified_Final=$applications_verified_district+$total_applications_verified_taluka+$total_applications_verified_urbanbody;
            //dd($total_applications_verified_Final);

            /********************************end**********************************************/


         /******************************APPLICATIONS PENDING FOR VERIFICATION******************/
            
            $applications_pending_for_verification_district=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('verification_status','=','Not Verified')->count();
            
            $total_applications_pending_for_verification_taluka=0;
            foreach ($taluka_codes as $taluka_code)
            {
                $applications_pending_for_verification_taluka=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('verification_status','=','Not Verified')->count();
                 //print_r($applications_received);
                $total_applications_pending_for_verification_taluka=$total_applications_pending_for_verification_taluka+$applications_pending_for_verification_taluka;
            }

            //dd($total_applications_received_taluka);

            $total_applications_pending_for_verification_urbanbody=0;
            foreach ($urban_body_codes as $urban_body_code)
            {
                $applications_pending_for_verification_urbanbody=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('verification_status','=','Not Verified')->count();
                 //print_r($applications_received);
                $total_applications_pending_for_verification_urbanbody=$total_applications_pending_for_verification_urbanbody+$applications_pending_for_verification_urbanbody;
            }
            //dd($total_applications_received_urbanbody);

            $total_applications_pending_for_verification_Final=$applications_pending_for_verification_district+$total_applications_pending_for_verification_taluka+$total_applications_pending_for_verification_urbanbody;
            //dd($total_applications_pending_for_verification_Final);

            /********************************end**********************************************/

            /******************************APPLICATIONS REJECTED AT VERIFICATION******************/
            
            $applications_rejected_at_verification_district=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('verification_status','=','Rejected')->count();
            
            $total_applications_rejected_at_verification_taluka=0;
            foreach ($taluka_codes as $taluka_code)
            {
                $applications_rejected_at_verification_taluka=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('verification_status','=','Rejected')->count();
                 //print_r($applications_received);
                $total_applications_rejected_at_verification_taluka=$total_applications_rejected_at_verification_taluka+$applications_rejected_at_verification_taluka;
            }

            //dd($total_applications_received_taluka);

            $total_applications_rejected_at_verification_urbanbody=0;
            foreach ($urban_body_codes as $urban_body_code)
            {
                $applications_rejected_at_verification_urbanbody=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('verification_status','=','Rejected')->count();
                 //print_r($applications_received);
                $total_applications_rejected_at_verification_urbanbody=$total_applications_rejected_at_verification_urbanbody+$applications_rejected_at_verification_urbanbody;
            }
            //dd($total_applications_received_urbanbody);

            $total_applications_rejected_at_verification_Final=$applications_rejected_at_verification_district+$total_applications_rejected_at_verification_taluka+$total_applications_rejected_at_verification_urbanbody;
            //dd($total_applications_pending_for_verification_Final);

            /********************************end**********************************************/

              /******************************APPLICATIONS APPROVED******************/
            
            $applications_approved_district=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $total_applications_approved_taluka=0;
            foreach ($taluka_codes as $taluka_code)
            {
                $applications_approved_taluka=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('body_code','=',$taluka_code)->where('approval_status','=','Approved')->count();
                 //print_r($applications_received);
                $total_applications_approved_taluka=$total_applications_approved_taluka+
                $applications_approved_taluka;
            }

            //dd($total_applications_received_taluka);

            $total_applications_approved_urbanbody=0;
            foreach ($urban_body_codes as $urban_body_code)
            {
                $applications_approved_urbanbody=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('body_code','=',$urban_body_code)->where('approval_status','=','Approved')->count();
                 //print_r($applications_received);
                $total_applications_approved_urbanbody=$total_applications_approved_urbanbody+$applications_approved_urbanbody;
            }
            //dd($total_applications_received_urbanbody);

            $total_applications_approved_Final=$applications_approved_district+$total_applications_approved_taluka+$total_applications_approved_urbanbody;
            //dd($total_applications_approved_Final);

            /********************************end**********************************************/

              /******************************APPLICATIONS PENDING FOR APPROVAL******************/
            
            $applications_pending_for_approval_district= DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();
            
            $total_applications_pending_for_approval_taluka=0;
            foreach ($taluka_codes as $taluka_code)
            {
                $applications_pending_for_approval_taluka=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();
                 //print_r($applications_received);
                $total_applications_pending_for_approval_taluka=$total_applications_pending_for_approval_taluka+$applications_pending_for_approval_taluka;
            }

            //dd($total_applications_received_taluka);

            $total_applications_pending_for_approval_urbanbody=0;
            foreach ($urban_body_codes as $urban_body_code)
            {
                $applications_pending_for_aproval_urbanbody=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();
                 //print_r($applications_received);
                $total_applications_pending_for_approval_urbanbody=$total_applications_pending_for_approval_urbanbody+
                $applications_pending_for_aproval_urbanbody;
            }
            //dd($total_applications_received_urbanbody);

            $total_applications_pending_for_approavl_Final=$applications_pending_for_approval_district+$total_applications_pending_for_approval_taluka+$total_applications_pending_for_approval_urbanbody;
            //dd($total_applications_pending_for_appoval_Final);

            /********************************end**********************************************/
            
              /******************************APPLICATIONS REJECTED AT APPROVAL******************/
            
            $applications_rejected_at_approval_district= DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();
            
            $total_applications_rejected_at_approval_taluka=0;
            foreach ($taluka_codes as $taluka_code)
            {
                $applications_rejected_at_approval_taluka=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();
                 //print_r($applications_received);
                $total_applications_rejected_at_approval_taluka=$total_applications_rejected_at_approval_taluka+$applications_rejected_at_approval_taluka;
            }

            //dd($total_applications_received_taluka);

            $total_applications_rejected_at_approval_urbanbody=0;
            foreach ($urban_body_codes as $urban_body_code)
            {
                $applications_rejected_at_aproval_urbanbody=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();
                 //print_r($applications_received);
                $total_applications_rejected_at_approval_urbanbody=$total_applications_rejected_at_approval_urbanbody+
                $applications_rejected_at_aproval_urbanbody;
            }
            //dd($total_applications_received_urbanbody);

            $total_applications_rejected_at_approavl_Final=$applications_rejected_at_approval_district+$total_applications_rejected_at_approval_taluka+$total_applications_rejected_at_approval_urbanbody;
            //dd($total_applications_pending_for_appoval_Final);

            /********************************end**********************************************/
                  
            $result=[
            'applications_received'=>$total_applications_received_Final,
            'applications_verified'=>$total_applications_verified_Final,
            'applications_rejected'=>$total_applications_rejected_at_verification_Final,
            'applications_pending_for_verification'=>$total_applications_pending_for_verification_Final,
            'applications_approved'=>$total_applications_approved_Final,
            'applications_pending_for_approval'=>$total_applications_pending_for_approavl_Final,
            'applications_rejected_at_approval'=>$total_applications_rejected_at_approavl_Final
           ];
        
        }elseif($level1=="ULB"){

            $urban_body_code=$level2;


            $applications_received=DB::table('nhm_employee_details')->Where('body_code','=',$urban_body_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('body_code','=',$urban_body_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

            $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
           ];
        }elseif($level1=="Block"){


            $taluka_code=$level2;


            $applications_received=DB::table('nhm_employee_details')->Where('body_code','=',$taluka_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('body_code','=',$taluka_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

            $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
           ];
        }
      

          return response()->json($result);
     
      
    }
    
    public function loadreportafterlevel3(Request $request){

        $level1=$request->level1;
        $level2=$request->level2;
        $level3=$request->level3;


        $constraints = [
            'level1' => $request['level1'],
            'level2' => $request['level2'],
            'level3' => $request['level3'],
            ];

        if($level3=="SPMU"){

            $place=$level1;
            $state_code=$level2;
    

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',1)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

            $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
           ];
        }elseif($level3=="DPMU"){

            $place=$level1;
            $district_code=$level2;

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

            $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
           ];
        }elseif($level3=="CPMU"){
            
            $place=$level1;
            $urban_body_code=$level2;

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();


            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$urban_body_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

            $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
           ];
       }elseif($level3=="BPMU"){

            $place=$level1;
            $taluka_code=$level2;

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$taluka_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

            $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
           ];
       }elseif($level3=="MCH"){
            // dd($level3);
            $place=$level1;
            

            if($place=='State')
            {
                $state_code=$level2;
                $applications_received=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->count();
                //dd($applications_received);
                $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

                $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

                $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

                $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',1)->where('approval_status','=','Approved')->count();
                
                $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

                $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


                 $result=[
                'applications_received'=>$applications_received,
                'applications_verified'=>$applications_verified,
                'applications_rejected'=>$applications_rejected,
                'applications_pending_for_verification'=>$applications_pending_for_verification,
                'applications_approved'=>$applications_approved,
                'applications_pending_for_approval'=>$applications_pending_for_aproval,
                'applications_rejected_at_approval'=>$applications_rejected_at_aproval
               ];
           }
           elseif($place=='District')
           {
                $district_code=$level2;
                $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();
                //dd($applications_received);
                $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

                $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

                $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

                $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
                
                $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

                $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


                 $result=[
                'applications_received'=>$applications_received,
                'applications_verified'=>$applications_verified,
                'applications_rejected'=>$applications_rejected,
                'applications_pending_for_verification'=>$applications_pending_for_verification,
                'applications_approved'=>$applications_approved,
                'applications_pending_for_approval'=>$applications_pending_for_aproval,
                'applications_rejected_at_approval'=>$applications_rejected_at_aproval
               ];

           }

       }elseif($level3=="Hospital"){
        
        $place=$level1;
        $district_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];
       
       }elseif($level3=="UPHC"){

        $place=$level1;
        $urban_body_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

        $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$urban_body_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

         $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];
       
       }elseif($level3=="Subcenter"){

        $place=$level1;
        $taluka_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$taluka_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
            ];
       }elseif($level3=="CHC"){

        $place=$level1;
        
        if($place=='Block'){

        $taluka_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$taluka_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];


        }
        elseif($place=='District'){

        $district_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];

        }
        
       }elseif($level3=="SSH"){

        $place=$level1;
        
        if($place=='State'){

        //$taluka_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',1)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];


        }
        elseif($place=='District'){

        $district_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];

        }
        
       }elseif($level3=="Other Hospital"){

        $place=$level1;
        
        if($place=='State'){

        //$taluka_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',1)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];


        }
        elseif($place=='District'){

        $district_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];

        }
        
       }elseif($level3=="State Drug Store"){

        $place=$level1;
        
        if($place=='State'){

        //$taluka_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',1)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];


        }
        elseif($place=='District'){

        $district_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];

        }
        
       }elseif($level3=="State Institute of Health and Family Welfare"){

        //$place=$level1;       
       //$taluka_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',1)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];
       

       }elseif($level3=="DH"){

        //$place=$level1;       
       $district_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];
       

       }elseif($level3=="SDH"){

        //$place=$level1;       
       $district_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];
        

       }elseif($level3=="ACMOH Office"){

        //$place=$level1;       
       $district_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];
       

       }elseif($level3=="SGH"){

        //$place=$level1;       
       $district_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];
       

       }elseif($level3=="ULB"){

        $place=$level1;
        
        if($place=='District') {
           $district_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];
         
        }elseif($place=='ULB') {
           $urban_body_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$urban_body_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];
         
        }      
      

       }elseif($level3=="PHC"){

        $place=$level1;
        
        if($place=='Block'){

        $taluka_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$taluka_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];


        }
        elseif($place=='District'){

        $district_code=$level2;

        $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->count();

        $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->count();

         $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Rejected')->count();

        $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Not Verified')->count();

        $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
        $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

        $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();


        $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
        ];

        }
        
       }




         return response()->json($result);


    }

    public function loadreportafterlevel4(Request $request){

        $level1=$request->level1;
        $level2=$request->level2;
        $level3=$request->level3;
        $level4=$request->level4;


        $constraints = [
            'level1' => $request['level1'],
            'level2' => $request['level2'],
            'level3' => $request['level3'],
            'level4' => $request['level4']
        ];

        if($level3=="MCH"){

            $place=$level1;
           
            

            if($place=='State'){
             
            $state_code=$level2;
            $posting_place_code=$level4;

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',1)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

            $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];


            }elseif($place=="District"){

            $district_code=$level2;
            $posting_place_code=$level4;
            

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

            $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];


            }
           

        }
        elseif($level3=="Hospital"){

            $place=$level1;
            $district_code=$level2;
            $posting_place_code=$level4;

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

             $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];

        }
         elseif($level3=="UPHC"){

            $place=$level1;
            $urban_body_code=$level2;
            $posting_place_code=$level4;

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();


            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$urban_body_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

            $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$urban_body_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();
        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];

        }elseif($level3=="Subcenter"){

            $place=$level1;
            $taluka_code=$level2;
            $posting_place_code=$level4;

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$taluka_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];
        
        }elseif($level3=="DH"){

            $place=$level1;
            $district_code=$level2;
            $posting_place_code=$level4;

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];
        
        }elseif($level3=="SDH"){

            $place=$level1;
            $district_code=$level2;
            $posting_place_code=$level4;

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];
        
        }elseif($level3=="ACMOH Office"){
            //dd("HI");
            $place=$level1;
            $district_code=$level2;
            $posting_place_code=$level4;

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        //dd($result);
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];
        
        }elseif($level3=="SGH"){

            $place=$level1;
            $district_code=$level2;
            $posting_place_code=$level4;

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];
        
        }elseif($level3=="ULB"){

            $place=$level1;
            $district_code=$level2;
            $posting_place_code=$level4;

            $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];
        
        }elseif($level3=="PHC"){

            $place=$level1;
            if($place=='Block'){

            $taluka_code=$level2;
            $posting_place_code=$level4; 

             $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$taluka_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];  

            }elseif($place=='District'){

            $district_code=$level2;
            $posting_place_code=$level4; 

             $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];  

            }
            

           
        }elseif($level3=="SSH"){

            $place=$level1;
            if($place=='State'){

            //$taluka_code=$level2;
            $posting_place_code=$level4; 

             $applications_received=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',1)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];  

            }elseif($place=='District'){

            $district_code=$level2;
            $posting_place_code=$level4; 

             $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];  

            }
            

           
        }elseif($level3=="Other Hospital"){

            $place=$level1;
            if($place=='State'){

            //$taluka_code=$level2;
            $posting_place_code=$level4; 

             $applications_received=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',1)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];  

            }elseif($place=="District"){

            $district_code=$level2;
            $posting_place_code=$level4; 

             $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];  

            }
            

           
        }elseif($level3=="CHC"){

            $place=$level1;
            if($place=='Block'){

            $taluka_code=$level2;
            $posting_place_code=$level4; 

             $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$taluka_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$taluka_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];  

            }elseif($place=='District'){

            $district_code=$level2;
            $posting_place_code=$level4; 

             $applications_received=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->count();

            $applications_verified=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->count();

            $applications_rejected=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Rejected')->count();

            $applications_pending_for_verification=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Not Verified')->count();

            $applications_approved=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('body_code','=',$district_code)->where('approval_status','=','Approved')->count();
            
            $applications_pending_for_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->count();

             $applications_rejected_at_aproval=DB::table('nhm_employee_details')->where('body_code','=',$district_code)->where('posting_level','=',$level3)->where('posting_place_code','=',$posting_place_code)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->count();

        
             $result=[
            'applications_received'=>$applications_received,
            'applications_verified'=>$applications_verified,
            'applications_rejected'=>$applications_rejected,
            'applications_pending_for_verification'=>$applications_pending_for_verification,
            'applications_approved'=>$applications_approved,
            'applications_pending_for_approval'=>$applications_pending_for_aproval,
            'applications_rejected_at_approval'=>$applications_rejected_at_aproval
             ];  

            }
            

           
        }



         return response()->json($result);
       
    }

    public function loadreport(Request $request){

        $level1=$request->level1;
        $level2=$request->level2;
        $level3=$request->level3;
        $level4=$request->level4;
        $flag=1;
        $constraints = [
            'level1' => $request['level1'],
            'level2' => $request['level2'],
            'level3' => $request['level3'],
            'level4' => $request['level4'],
            ];
        
       //dd($constraints);
        if($level3=='SPMU'){

        	$result=DB::table('nhm_employee_details')->where('body_code','=',1)->count();
        
        }elseif($level3=='DPMU'){
        	$result=DB::table('nhm_employee_details')->where('body_code','=',$level2)->count();
        }elseif($level3=='CPMU'){
        	$result=DB::table('nhm_employee_details')->where('body_code','=',$level2)->count();
        }elseif($level3=='BPMU'){
        	$result=DB::table('nhm_employee_details')->where('body_code','=',$level2)->count();
        }else{
        	$result=DB::table('m_health_facility')->where('facilty_code','=',$level4)->join('nhm_employee_details', 'nhm_employee_details.posting_place_code', '=', 'm_health_facility.facilty_code')->count();
        }

       // $result=DB::table('m_health_facility')->where('facilty_code','=',$level4)->join('nhm_employee_details', 'nhm_employee_details.posting_place_code', '=', 'm_health_facility.facilty_code')->get();
        
        //$result=DB::table('m_health_facility')->where('district_code',$level2);
            // ->join('nhm_employee_details', 'nhm_employee_details.posting_place_code', '=', 'm_health_facility.facilty_code')
            // ->get(['first_name','middle_name','last_name','guardian_name','appointing_authority','posting_level','posting_place','created_at']);

          return response()->json($result);
     
        //dd($result);
        // return redirect("employee-report-drilldown")->with('result', $result);
        
       // return view('employee-report/index', ['employeedatas' => $employeedatas, 'searchingVals' => $constraints])->with('flag',$flag);
    }

    public function showSingleEmployeeReport(Request $request)
    {
        
        $id=$request->id;
        
        $single_employee_details = nhm_employee_details::find($id);
        
        if($single_employee_details->approval_status == "Approved"){
            $single_employee_details = NHMEmployee::where('application_id','=',$id)->first();
        }

        // return Redirect::back()->with(['single_employee_details'=>$single_employee_details,'flag'=>$flag]);
        



        return view('show_single_nhm_employee_details_report', ['single_employee_details' => $single_employee_details]);
    }




    public function loadAppSubmitted(Request $request){
        
        $ApplicationsSubmitted_level1=$request['ApplicationsSubmitted_level1'];
        $ApplicationsSubmitted_level2=$request['ApplicationsSubmitted_level2'];
        $ApplicationsSubmitted_level2d=$request['ApplicationsSubmitted_level2d'];
        $ApplicationsSubmitted_level3=$request['ApplicationsSubmitted_level3'];
        $ApplicationsSubmitted_level4=$request['ApplicationsSubmitted_level4'];

       
        // dd($ApplicationsSubmitted_level1,$ApplicationsSubmitted_level2,$ApplicationsSubmitted_level2d,
        // $ApplicationsSubmitted_level3,$ApplicationsSubmitted_level4);

        if($ApplicationsSubmitted_level1=='State'){
            
            if( $ApplicationsSubmitted_level3==null &&   
                $ApplicationsSubmitted_level4==null && $ApplicationsSubmitted_level2d==null) {

                $applications_submitteds=DB::table('nhm_employee_details')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                //->paginate(40);//get();
                
                $applications_submitted_count=$applications_submitteds->count();
                //$applications_submitted_count=DB::table('nhm_employee_details')->count('id');
                //dd($applications_submitteds);
                if($ApplicationsSubmitted_level2==1){
                    $state_name='West Bengal';
                }
                return view('employee-report-drilldown.linelisting_mass',
                    ['datas' => $applications_submitteds,
                    'count'=>$applications_submitted_count
                    
                    ])->with('level1',$ApplicationsSubmitted_level1)
                      ->with('level2',$ApplicationsSubmitted_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$ApplicationsSubmitted_level2d)
                      ->with('level3',$ApplicationsSubmitted_level3)
                      ->with('level4',$ApplicationsSubmitted_level4)
                      ->with('message','Applications Submitted');

            }elseif($ApplicationsSubmitted_level3!=null && $ApplicationsSubmitted_level4==null && $ApplicationsSubmitted_level2d==null){

                $applications_submitteds=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$ApplicationsSubmitted_level3)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $applications_submitted_count=$applications_submitteds->count();
               
                if($ApplicationsSubmitted_level2==1){
                    $state_name='West Bengal';
                }

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $applications_submitteds,
                    'count'=>$applications_submitted_count
                  
                    ])->with('level1',$ApplicationsSubmitted_level1)
                      ->with('level2',$ApplicationsSubmitted_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$ApplicationsSubmitted_level2d)
                      ->with('level3',$ApplicationsSubmitted_level3)
                      ->with('level4',$ApplicationsSubmitted_level4)
                      ->with('message','Applications Submitted');

            }elseif($ApplicationsSubmitted_level3!=null && $ApplicationsSubmitted_level4!=null && $ApplicationsSubmitted_level2d==null){

                $applications_submitteds=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$ApplicationsSubmitted_level3)->where('posting_place_code','=',$ApplicationsSubmitted_level4)
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $applications_submitted_count=$applications_submitteds->count();
                if($ApplicationsSubmitted_level2==1){
                    $state_name='West Bengal';
                }

                $place_name=DB::table('m_health_facility')->where('facilty_code','=',$ApplicationsSubmitted_level4)->pluck('facility_name')->first();
            
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $applications_submitteds,
                    'count'=>$applications_submitted_count
                   
                    ])->with('level1',$ApplicationsSubmitted_level1)
                      ->with('level2',$ApplicationsSubmitted_level2)
                      ->with('state_name',$state_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$ApplicationsSubmitted_level2d)
                      ->with('level3',$ApplicationsSubmitted_level3)
                      ->with('level4',$ApplicationsSubmitted_level4)
                      ->with('message','Applications Submitted');

            }
        



        }elseif($ApplicationsSubmitted_level1=='District'){

        
        $urban_body_codes = UrbanBody::where('district_code', '=', $ApplicationsSubmitted_level2)->pluck('urban_body_code as body_code');
        $taluka_codes = Taluka::where('district_code', '=', $ApplicationsSubmitted_level2)->pluck('taluka_code as body_code');
        $body_codes = $urban_body_codes->merge($taluka_codes);
        $body_codes=$body_codes->merge($ApplicationsSubmitted_level2);

        //dd($urban_body_codes);

         if( $ApplicationsSubmitted_level2!=null && $ApplicationsSubmitted_level3==null &&  $ApplicationsSubmitted_level4==null && $ApplicationsSubmitted_level2d==null) {
             
                $applications_submitteds=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//->paginate(40);//get();
                
                $applications_submitted_count=$applications_submitteds->count();
               // dd($applications_submitted_count);
            
              $district_name=District::where('district_code','=',$ApplicationsSubmitted_level2)->pluck('district_name')->first();
//dd($district_name);
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $applications_submitteds,
                    'count'=>$applications_submitted_count
                   
                    ])->with('level1',$ApplicationsSubmitted_level1)
                      ->with('level2',$ApplicationsSubmitted_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$ApplicationsSubmitted_level2d)
                      ->with('level3',$ApplicationsSubmitted_level3)
                      ->with('level4',$ApplicationsSubmitted_level4)
                      ->with('message','Applications Submitted');

            

            }elseif($ApplicationsSubmitted_level2!=null && $ApplicationsSubmitted_level3!=null && $ApplicationsSubmitted_level4==null && $ApplicationsSubmitted_level2d==null){

                $applications_submitteds=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('posting_level','=',$ApplicationsSubmitted_level3)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $applications_submitted_count=$applications_submitteds->count();
               // dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$ApplicationsSubmitted_level2)->pluck('district_name')->first();
                


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $applications_submitteds,
                    'count'=>$applications_submitted_count
                    
                    ])->with('level1',$ApplicationsSubmitted_level1)
                      ->with('level2',$ApplicationsSubmitted_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$ApplicationsSubmitted_level2d)
                      ->with('level3',$ApplicationsSubmitted_level3)
                      ->with('level4',$ApplicationsSubmitted_level4)
                      ->with('message','Applications Submitted');

            }elseif($ApplicationsSubmitted_level2!=null && $ApplicationsSubmitted_level3!=null && $ApplicationsSubmitted_level4!=null && $ApplicationsSubmitted_level2d==null){

                $applications_submitteds=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('posting_level','=',$ApplicationsSubmitted_level3)->where('posting_place_code','=',$ApplicationsSubmitted_level4)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
               
                $applications_submitted_count=$applications_submitteds->count();
               
                //dd($applications_submitted_count);
                
                if($ApplicationsSubmitted_level3=='ACMOH Office')
                {
                    $district_name = District::where('district_code','=',$ApplicationsSubmitted_level2)->pluck('district_name')->first();
                    //dd($district_name);
                    $place_name=SubDistrict::where('sub_district_code','=',$ApplicationsSubmitted_level4)->pluck('sub_district_name')->first();
                
                }elseif($ApplicationsSubmitted_level3=='ULB'){
                     $district_name = District::where('district_code','=',$ApplicationsSubmitted_level2)->pluck('district_name')->first();
                     $place_name=UrbanBody::where('urban_body_code','=',$ApplicationsSubmitted_level4)->pluck('urban_body_name')->first();
                }else{
                    $district_name = District::where('district_code','=',$ApplicationsSubmitted_level2)->pluck('district_name')->first();
                    $place_name=DB::table('m_health_facility')->where('facilty_code','=',$ApplicationsSubmitted_level4)->pluck('facility_name')->first();
                   // dd($district_name);
                }
                
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $applications_submitteds,
                    'count'=>$applications_submitted_count,
                  
                    ])->with('level1',$ApplicationsSubmitted_level1)
                      ->with('level2',$ApplicationsSubmitted_level2)
                      ->with('district_name',$district_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$ApplicationsSubmitted_level2d)
                      ->with('level3',$ApplicationsSubmitted_level3)
                      ->with('level4',$ApplicationsSubmitted_level4)
                      ->with('message','Applications Submitted');

            }



        }elseif($ApplicationsSubmitted_level1=='ULB'){

            //dd("HI");
            if($ApplicationsSubmitted_level2d!=null && $ApplicationsSubmitted_level2!=null && $ApplicationsSubmitted_level3==null &&   
                $ApplicationsSubmitted_level4==null ) {

                $applications_submitteds=DB::table('nhm_employee_details')->where('body_code','=',$ApplicationsSubmitted_level2)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $applications_submitted_count=$applications_submitteds->count();
                //dd($applications_submitted_count);
                
                 $district_name=District::where('district_code','=',$ApplicationsSubmitted_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$ApplicationsSubmitted_level2)->pluck('urban_body_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $applications_submitteds,
                    'count'=>$applications_submitted_count
                    
                    ])->with('level1',$ApplicationsSubmitted_level1)
                      ->with('level2',$ApplicationsSubmitted_level2)
                      ->with('level2d',$ApplicationsSubmitted_level2d)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level3',$ApplicationsSubmitted_level3)
                      ->with('level4',$ApplicationsSubmitted_level4)
                      ->with('message','Applications Submitted');

            }elseif($ApplicationsSubmitted_level2d!=null && $ApplicationsSubmitted_level2!=null && 
                $ApplicationsSubmitted_level3!=null && $ApplicationsSubmitted_level4==null){

                $applications_submitteds=DB::table('nhm_employee_details')->where('body_code','=',$ApplicationsSubmitted_level2)->where('posting_level','=',$ApplicationsSubmitted_level3)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $applications_submitted_count=$applications_submitteds->count();
                
                $district_name=District::where('district_code','=',$ApplicationsSubmitted_level2d)->pluck('district_name')->first();
                $urban_body_name=UrbanBody::where('urban_body_code','=',$ApplicationsSubmitted_level2)->pluck('urban_body_name')->first();





                return view('employee-report-drilldown.linelisting',
                    ['datas' => $applications_submitteds,
                    'count'=>$applications_submitted_count
                    
                    ])->with('level1',$ApplicationsSubmitted_level1)
                      ->with('level2',$ApplicationsSubmitted_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level2d',$ApplicationsSubmitted_level2d)
                      ->with('level3',$ApplicationsSubmitted_level3)
                      ->with('level4',$ApplicationsSubmitted_level4)
                      ->with('message','Applications Submitted');

            }elseif($ApplicationsSubmitted_level2d!=null && $ApplicationsSubmitted_level2!=null && $ApplicationsSubmitted_level3!=null && $ApplicationsSubmitted_level4!=null ){

                $applications_submitteds=DB::table('nhm_employee_details')->where('body_code','=',$ApplicationsSubmitted_level2)->where('posting_level','=',$ApplicationsSubmitted_level3)->where('posting_place_code','=',$ApplicationsSubmitted_level4)
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $applications_submitted_count=$applications_submitteds->count();
            
                 $district_name=District::where('district_code','=',$ApplicationsSubmitted_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$ApplicationsSubmitted_level2)->pluck('urban_body_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$ApplicationsSubmitted_level4)->pluck('facility_name')->first();
               
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $applications_submitteds,
                    'count'=>$applications_submitted_count
                   
                    ])->with('level1',$ApplicationsSubmitted_level1)
                      ->with('level2',$ApplicationsSubmitted_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$ApplicationsSubmitted_level2d)
                      ->with('level3',$ApplicationsSubmitted_level3)
                      ->with('level4',$ApplicationsSubmitted_level4)
                      ->with('message','Applications Submitted');

            }
        




        }elseif($ApplicationsSubmitted_level1=='Block'){

            if($ApplicationsSubmitted_level2d!=null && $ApplicationsSubmitted_level2!=null && $ApplicationsSubmitted_level3==null &&   
                $ApplicationsSubmitted_level4==null ) {

                $applications_submitteds=DB::table('nhm_employee_details')->where('body_code','=',$ApplicationsSubmitted_level2)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $applications_submitted_count=$applications_submitteds->count();
                //dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$ApplicationsSubmitted_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$ApplicationsSubmitted_level2)->pluck('taluka_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $applications_submitteds,
                    'count'=>$applications_submitted_count
                    
                    ])->with('level1',$ApplicationsSubmitted_level1)
                      ->with('level2',$ApplicationsSubmitted_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$ApplicationsSubmitted_level2d)
                      ->with('level3',$ApplicationsSubmitted_level3)
                      ->with('level4',$ApplicationsSubmitted_level4)
                      ->with('message','Applications Submitted');

            }elseif($ApplicationsSubmitted_level2d!=null && $ApplicationsSubmitted_level2!=null && 
                $ApplicationsSubmitted_level3!=null && $ApplicationsSubmitted_level4==null){

                $applications_submitteds=DB::table('nhm_employee_details')->where('body_code','=',$ApplicationsSubmitted_level2)->where('posting_level','=',$ApplicationsSubmitted_level3)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $applications_submitted_count=$applications_submitteds->count();
                
                $district_name=District::where('district_code','=',$ApplicationsSubmitted_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$ApplicationsSubmitted_level2)->pluck('taluka_name')->first();
                

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $applications_submitteds,
                    'count'=>$applications_submitted_count
                    
                    ])->with('level1',$ApplicationsSubmitted_level1)
                      ->with('level2',$ApplicationsSubmitted_level2)
                       ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$ApplicationsSubmitted_level2d)
                      ->with('level3',$ApplicationsSubmitted_level3)
                      ->with('level4',$ApplicationsSubmitted_level4)
                      ->with('message','Applications Submitted');

            }elseif($ApplicationsSubmitted_level2d!=null && $ApplicationsSubmitted_level2!=null && $ApplicationsSubmitted_level3!=null && $ApplicationsSubmitted_level4!=null ){

                $applications_submitteds=DB::table('nhm_employee_details')->where('body_code','=',$ApplicationsSubmitted_level2)->where('posting_level','=',$ApplicationsSubmitted_level3)->where('posting_place_code','=',$ApplicationsSubmitted_level4)
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $applications_submitted_count=$applications_submitteds->count();
                

                $district_name=District::where('district_code','=',$ApplicationsSubmitted_level2d)->pluck('district_name')->first();
                 $taluka_name=Taluka::where('taluka_code','=',$ApplicationsSubmitted_level2)->pluck('taluka_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$ApplicationsSubmitted_level4)->pluck('facility_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $applications_submitteds,
                    'count'=>$applications_submitted_count
                   
                    ])->with('level1',$ApplicationsSubmitted_level1)
                      ->with('level2',$ApplicationsSubmitted_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$ApplicationsSubmitted_level2d)
                      ->with('level3',$ApplicationsSubmitted_level3)
                      ->with('level4',$ApplicationsSubmitted_level4)
                       ->with('message','Applications Submitted');

            }
        }

    }



 public function loadPendingVerification(Request $request){
        //dd($request->PendingVerification_level1);
        $PendingVerification_level1=$request['PendingVerification_level1'];
        $PendingVerification_level2=$request['PendingVerification_level2'];
        $PendingVerification_level2d=$request['PendingVerification_level2d'];
        $PendingVerification_level3=$request['PendingVerification_level3'];
        $PendingVerification_level4=$request['PendingVerification_level4'];

       
         // dd($PendingVerification_level1,$PendingVerification_level2,$PendingVerification_level2d,
         // $PendingVerification_level3,$PendingVerification_level4);

        if($PendingVerification_level1=='State'){
            
            if( $PendingVerification_level3==null &&   
                $PendingVerification_level4==null && $PendingVerification_level2d==null) {

                $PendingVerification=DB::table('nhm_employee_details')->where('verification_status','=','Not Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $PendingVerification_count=$PendingVerification->count();
                //$applications_submitted_count=DB::table('nhm_employee_details')->count('id');
                //dd($applications_submitteds);
                if($PendingVerification_level2==1){
                    $state_name='West Bengal';
                }
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingVerification,
                    'count'=>$PendingVerification_count
                    
                    ])->with('level1',$PendingVerification_level1)
                      ->with('level2',$PendingVerification_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$PendingVerification_level2d)
                      ->with('level3',$PendingVerification_level3)
                      ->with('level4',$PendingVerification_level4)
                       ->with('message','Applications Pending For Verification');

            }elseif($PendingVerification_level3!=null && $PendingVerification_level4==null && $PendingVerification_level2d==null){

                $PendingVerification=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$PendingVerification_level3)->where('verification_status','=','Not Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $PendingVerification_count=$PendingVerification->count();
               
                if($PendingVerification_level2==1){
                    $state_name='West Bengal';
                }

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingVerification,
                    'count'=>$PendingVerification_count
                  
                    ])->with('level1',$PendingVerification_level1)
                      ->with('level2',$PendingVerification_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$PendingVerification_level2d)
                      ->with('level3',$PendingVerification_level3)
                      ->with('level4',$PendingVerification_level4)
                       ->with('message','Applications Pending For Verification');

            }elseif($PendingVerification_level3!=null && $PendingVerification_level4!=null && $PendingVerification_level2d==null){

                $PendingVerification=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$PendingVerification_level3)->where('posting_place_code','=',$PendingVerification_level4)->where('verification_status','=','Not Verified')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $PendingVerification_count=$PendingVerification->count();
                if($PendingVerification_level2==1){
                    $state_name='West Bengal';
                }

                $place_name=DB::table('m_health_facility')->where('facilty_code','=',$PendingVerification_level4)->pluck('facility_name')->first();
            
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingVerification,
                    'count'=>$PendingVerification_count
                   
                    ])->with('level1',$PendingVerification_level1)
                      ->with('level2',$PendingVerification_level2)
                      ->with('state_name',$state_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$PendingVerification_level2d)
                      ->with('level3',$PendingVerification_level3)
                      ->with('level4',$PendingVerification_level4)
                       ->with('message','Applications Pending For Verification');

            }
        



        }elseif($PendingVerification_level1=='District'){

        
        $urban_body_codes = UrbanBody::where('district_code', '=', $PendingVerification_level2)->pluck('urban_body_code as body_code');
        $taluka_codes = Taluka::where('district_code', '=', $PendingVerification_level2)->pluck('taluka_code as body_code');
        $body_codes = $urban_body_codes->merge($taluka_codes);
        $body_codes=$body_codes->merge($PendingVerification_level2);

        //dd($urban_body_codes);

         if( $PendingVerification_level2!=null && $PendingVerification_level3==null &&  $PendingVerification_level4==null && $PendingVerification_level2d==null) {
             
                $PendingVerification=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Not Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $PendingVerification_count=$PendingVerification->count();
               // dd($applications_submitted_count);
            
              $district_name=District::where('district_code','=',$PendingVerification_level2)->pluck('district_name')->first();
//dd($district_name);
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingVerification,
                    'count'=>$PendingVerification_count
                   
                    ])->with('level1',$PendingVerification_level1)
                      ->with('level2',$PendingVerification_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$PendingVerification_level2d)
                      ->with('level3',$PendingVerification_level3)
                      ->with('level4',$PendingVerification_level4)
                       ->with('message','Applications Pending For Verification');

            

            }elseif($PendingVerification_level2!=null && $PendingVerification_level3!=null && $PendingVerification_level4==null && $PendingVerification_level2d==null){

                $PendingVerification=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Not Verified')->where('posting_level','=',$PendingVerification_level3)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $PendingVerification_count=$PendingVerification->count();
               // dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$PendingVerification_level2)->pluck('district_name')->first();
                


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingVerification,
                    'count'=>$PendingVerification_count
                    
                    ])->with('level1',$PendingVerification_level1)
                      ->with('level2',$PendingVerification_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$PendingVerification_level2d)
                      ->with('level3',$PendingVerification_level3)
                      ->with('level4',$PendingVerification_level4)
                       ->with('message','Applications Pending For Verification');

            }elseif($PendingVerification_level2!=null && $PendingVerification_level3!=null && $PendingVerification_level4!=null && $PendingVerification_level2d==null){

                $PendingVerification=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Not Verified')->where('posting_level','=',$PendingVerification_level3)->where('posting_place_code','=',$PendingVerification_level4)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
               
                $PendingVerification_count=$PendingVerification->count();
               
                //dd($applications_submitted_count);
                
                if($PendingVerification_level3=='ACMOH Office')
                {
                    $district_name = District::where('district_code','=',$PendingVerification_level2)->pluck('district_name')->first();
                    //dd($district_name);
                    $place_name=SubDistrict::where('sub_district_code','=',$PendingVerification_level4)->pluck('sub_district_name')->first();
                
                }elseif($PendingVerification_level3=='ULB'){
                     $district_name = District::where('district_code','=',$PendingVerification_level2)->pluck('district_name')->first();
                     $place_name=UrbanBody::where('urban_body_code','=',$PendingVerification_level4)->pluck('urban_body_name')->first();
                }else{
                    $district_name = District::where('district_code','=',$PendingVerification_level2)->pluck('district_name')->first();
                    $place_name=DB::table('m_health_facility')->where('facilty_code','=',$PendingVerification_level4)->pluck('facility_name')->first();
                   // dd($district_name);
                }
                
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingVerification,
                    'count'=>$PendingVerification_count,
                  
                    ])->with('level1',$PendingVerification_level1)
                      ->with('level2',$PendingVerification_level2)
                      ->with('district_name',$district_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$PendingVerification_level2d)
                      ->with('level3',$PendingVerification_level3)
                      ->with('level4',$PendingVerification_level4)
                       ->with('message','Applications Pending For Verification');

            }



        }elseif($PendingVerification_level1=='ULB'){
// dd($PendingVerification_level2d,$PendingVerification_level2,$PendingVerification_level3,$PendingVerification_level4);
            
            if($PendingVerification_level2d!=null && $PendingVerification_level2!=null && $PendingVerification_level3==null &&   
                $PendingVerification_level4==null ) {
//dd("HI");
                $PendingVerification=DB::table('nhm_employee_details')->where('body_code','=',$PendingVerification_level2)->where('verification_status','=','Not Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $PendingVerification_count=$PendingVerification->count();
                //dd($applications_submitted_count);
                
                 $district_name=District::where('district_code','=',$PendingVerification_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$PendingVerification_level2)->pluck('urban_body_name')->first();
 //dd($PendingVerification_count);

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingVerification,
                    'count'=>$PendingVerification_count
                    
                    ])->with('level1',$PendingVerification_level1)
                      ->with('level2',$PendingVerification_level2)
                      ->with('level2d',$PendingVerification_level2d)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level3',$PendingVerification_level3)
                      ->with('level4',$PendingVerification_level4)
                       ->with('message','Applications Pending For Verification');

            }elseif($PendingVerification_level2d!=null && $PendingVerification_level2!=null && 
                $PendingVerification_level3!=null && $PendingVerification_level4==null){

                $PendingVerification=DB::table('nhm_employee_details')->where('body_code','=',$PendingVerification_level2)->where('posting_level','=',$PendingVerification_level3)->where('verification_status','=','Not Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $PendingVerification_count=$PendingVerification->count();
                
                $district_name=District::where('district_code','=',$PendingVerification_level2d)->pluck('district_name')->first();
                $urban_body_name=UrbanBody::where('urban_body_code','=',$PendingVerification_level2)->pluck('urban_body_name')->first();





                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingVerification,
                    'count'=>$PendingVerification_count
                    
                    ])->with('level1',$PendingVerification_level1)
                      ->with('level2',$PendingVerification_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level2d',$PendingVerification_level2d)
                      ->with('level3',$PendingVerification_level3)
                      ->with('level4',$PendingVerification_level4)
                       ->with('message','Applications Pending For Verification');

            }elseif($PendingVerification_level2d!=null && $PendingVerification_level2!=null && $PendingVerification_level3!=null && $PendingVerification_level4!=null ){

                $PendingVerification=DB::table('nhm_employee_details')->where('body_code','=',$PendingVerification_level2)->where('posting_level','=',$PendingVerification_level3)->where('posting_place_code','=',$PendingVerification_level4)->where('verification_status','=','Not Verified')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $PendingVerification_count=$PendingVerification->count();
            
                 $district_name=District::where('district_code','=',$PendingVerification_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$PendingVerification_level2)->pluck('urban_body_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$PendingVerification_level4)->pluck('facility_name')->first();
               
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingVerification,
                    'count'=>$PendingVerification_count
                   
                    ])->with('level1',$PendingVerification_level1)
                      ->with('level2',$PendingVerification_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$PendingVerification_level2d)
                      ->with('level3',$PendingVerification_level3)
                      ->with('level4',$PendingVerification_level4)
                     ->with('message','Applications Pending For Verification');

            }
        




        }elseif($PendingVerification_level1=='Block'){

            if($PendingVerification_level2d!=null && $PendingVerification_level2!=null && $PendingVerification_level3==null &&   
                $PendingVerification_level4==null ) {

                $PendingVerification=DB::table('nhm_employee_details')->where('body_code','=',$PendingVerification_level2)->where('verification_status','=','Not Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $PendingVerification_count=$PendingVerification->count();
                //dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$PendingVerification_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$PendingVerification_level2)->pluck('taluka_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingVerification,
                    'count'=>$PendingVerification_count
                    
                    ])->with('level1',$PendingVerification_level1)
                      ->with('level2',$PendingVerification_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$PendingVerification_level2d)
                      ->with('level3',$PendingVerification_level3)
                      ->with('level4',$PendingVerification_level4)
                       ->with('message','Applications Pending For Verification');

            }elseif($PendingVerification_level2d!=null && $PendingVerification_level2!=null && 
                $PendingVerification_level3!=null && $PendingVerification_level4==null){

                $PendingVerification=DB::table('nhm_employee_details')->where('body_code','=',$PendingVerification_level2)->where('posting_level','=',$PendingVerification_level3)->where('verification_status','=','Not Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $PendingVerification_count=$PendingVerification->count();
                
                $district_name=District::where('district_code','=',$PendingVerification_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$PendingVerification_level2)->pluck('taluka_name')->first();
                

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingVerification,
                    'count'=>$PendingVerification_count
                    
                    ])->with('level1',$PendingVerification_level1)
                      ->with('level2',$PendingVerification_level2)
                       ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$PendingVerification_level2d)
                      ->with('level3',$PendingVerification_level3)
                      ->with('level4',$PendingVerification_level4)
                      ->with('message','Applications Pending For Verification');

            }elseif($PendingVerification_level2d!=null && $PendingVerification_level2!=null && $PendingVerification_level3!=null && $PendingVerification_level4!=null ){

                $PendingVerification=DB::table('nhm_employee_details')->where('body_code','=',$PendingVerification_level2)->where('posting_level','=',$PendingVerification_level3)->where('posting_place_code','=',$PendingVerification_level4)->where('verification_status','=','Not Verified')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $PendingVerification_count=$PendingVerification->count();
                

                $district_name=District::where('district_code','=',$PendingVerification_level2d)->pluck('district_name')->first();
                 $taluka_name=Taluka::where('taluka_code','=',$PendingVerification_level2)->pluck('taluka_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$PendingVerification_level4)->pluck('facility_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingVerification,
                    'count'=>$PendingVerification_count
                   
                    ])->with('level1',$PendingVerification_level1)
                      ->with('level2',$PendingVerification_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$PendingVerification_level2d)
                      ->with('level3',$PendingVerification_level3)
                      ->with('level4',$PendingVerification_level4)
                      ->with('message','Applications Pending For Verification');

            }
        }

    }


public function loadApppVerified(Request $request){
        //dd($request->PendingVerification_level1);ApplicationsVerified_level1
        $ApplicationsVerified_level1=$request['ApplicationsVerified_level1'];
        $ApplicationsVerified_level2=$request['ApplicationsVerified_level2'];
        $ApplicationsVerified_level2d=$request['ApplicationsVerified_level2d'];
        $ApplicationsVerified_level3=$request['ApplicationsVerified_level3'];
        $ApplicationsVerified_level4=$request['ApplicationsVerified_level4'];

       
         // dd($PendingVerification_level1,$PendingVerification_level2,$PendingVerification_level2d,
         // $PendingVerification_level3,$PendingVerification_level4);

        if($ApplicationsVerified_level1=='State'){
            
            if( $ApplicationsVerified_level3==null &&   
                $ApplicationsVerified_level4==null && $ApplicationsVerified_level2d==null) {

                $ApplicationsVerified=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $ApplicationsVerified_count=$ApplicationsVerified->count();
                //$applications_submitted_count=DB::table('nhm_employee_details')->count('id');
                //dd($applications_submitteds);
                if($ApplicationsVerified_level2==1){
                    $state_name='West Bengal';
                }
                return view('employee-report-drilldown.linelisting_mass_verified',
                    ['datas' => $ApplicationsVerified,
                    'count'=>$ApplicationsVerified_count
                    
                    ])->with('level1',$ApplicationsVerified_level1)
                      ->with('level2',$ApplicationsVerified_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$ApplicationsVerified_level2d)
                      ->with('level3',$ApplicationsVerified_level3)
                      ->with('level4',$ApplicationsVerified_level4)
                      ->with('message','Applications Verified');

            }elseif($ApplicationsVerified_level3!=null && $ApplicationsVerified_level4==null && $ApplicationsVerified_level2d==null){

                $ApplicationsVerified=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$ApplicationsVerified_level3)->where('verification_status','=','Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $ApplicationsVerified_count=$ApplicationsVerified->count();
               
                if($ApplicationsVerified_level2==1){
                    $state_name='West Bengal';
                }

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApplicationsVerified,
                    'count'=>$ApplicationsVerified_count
                  
                    ])->with('level1',$ApplicationsVerified_level1)
                      ->with('level2',$ApplicationsVerified_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$ApplicationsVerified_level2d)
                      ->with('level3',$ApplicationsVerified_level3)
                      ->with('level4',$ApplicationsVerified_level4)
                      ->with('message','Applications Verified');

            }elseif($ApplicationsVerified_level3!=null && $ApplicationsVerified_level4!=null && $ApplicationsVerified_level2d==null){

                $ApplicationsVerified=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$ApplicationsVerified_level3)->where('posting_place_code','=',$ApplicationsVerified_level4)->where('verification_status','=','Verified')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $ApplicationsVerified_count=$ApplicationsVerified->count();
                if($ApplicationsVerified_level2==1){
                    $state_name='West Bengal';
                }

                $place_name=DB::table('m_health_facility')->where('facilty_code','=',$ApplicationsVerified_level4)->pluck('facility_name')->first();
            
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApplicationsVerified,
                    'count'=>$ApplicationsVerified_count
                   
                    ])->with('level1',$ApplicationsVerified_level1)
                      ->with('level2',$ApplicationsVerified_level2)
                      ->with('state_name',$state_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$ApplicationsVerified_level2d)
                      ->with('level3',$ApplicationsVerified_level3)
                      ->with('level4',$ApplicationsVerified_level4)
                      ->with('message','Applications Verified');

            }
        



        }elseif($ApplicationsVerified_level1=='District'){

        
        $urban_body_codes = UrbanBody::where('district_code', '=', $ApplicationsVerified_level2)->pluck('urban_body_code as body_code');
        $taluka_codes = Taluka::where('district_code', '=', $ApplicationsVerified_level2)->pluck('taluka_code as body_code');
        $body_codes = $urban_body_codes->merge($taluka_codes);
        $body_codes=$body_codes->merge($ApplicationsVerified_level2);

        //dd($urban_body_codes);

         if( $ApplicationsVerified_level2!=null && $ApplicationsVerified_level3==null &&  $ApplicationsVerified_level4==null && $ApplicationsVerified_level2d==null) {
             
                $ApplicationsVerified=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $ApplicationsVerified_count=$ApplicationsVerified->count();
               // dd($applications_submitted_count);
            
              $district_name=District::where('district_code','=',$ApplicationsVerified_level2)->pluck('district_name')->first();
//dd($district_name);
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApplicationsVerified,
                    'count'=>$ApplicationsVerified_count
                   
                    ])->with('level1',$ApplicationsVerified_level1)
                      ->with('level2',$ApplicationsVerified_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$ApplicationsVerified_level2d)
                      ->with('level3',$ApplicationsVerified_level3)
                      ->with('level4',$ApplicationsVerified_level4)
                      ->with('message','Applications Verified');

            

            }elseif($ApplicationsVerified_level2!=null && $ApplicationsVerified_level3!=null && $ApplicationsVerified_level4==null && $ApplicationsVerified_level2d==null){

                $ApplicationsVerified=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Verified')->where('posting_level','=',$ApplicationsVerified_level3)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $ApplicationsVerified_count=$ApplicationsVerified->count();
               // dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$ApplicationsVerified_level2)->pluck('district_name')->first();
                


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApplicationsVerified,
                    'count'=>$ApplicationsVerified_count
                    
                    ])->with('level1',$ApplicationsVerified_level1)
                      ->with('level2',$ApplicationsVerified_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$ApplicationsVerified_level2d)
                      ->with('level3',$ApplicationsVerified_level3)
                      ->with('level4',$ApplicationsVerified_level4)
                      ->with('message','Applications Verified');

            }elseif($ApplicationsVerified_level2!=null && $ApplicationsVerified_level3!=null && $ApplicationsVerified_level4!=null && $ApplicationsVerified_level2d==null){

                $ApplicationsVerified=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Verified')->where('posting_level','=',$ApplicationsVerified_level3)->where('posting_place_code','=',$ApplicationsVerified_level4)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
               
                $ApplicationsVerified_count=$ApplicationsVerified->count();
               
                //dd($applications_submitted_count);
                
                if($ApplicationsVerified_level3=='ACMOH Office')
                {
                    $district_name = District::where('district_code','=',$ApplicationsVerified_level2)->pluck('district_name')->first();
                    //dd($district_name);
                    $place_name=SubDistrict::where('sub_district_code','=',$ApplicationsVerified_level4)->pluck('sub_district_name')->first();
                
                }elseif($ApplicationsVerified_level3=='ULB'){
                     $district_name = District::where('district_code','=',$ApplicationsVerified_level2)->pluck('district_name')->first();
                     $place_name=UrbanBody::where('urban_body_code','=',$ApplicationsVerified_level4)->pluck('urban_body_name')->first();
                }else{
                    $district_name = District::where('district_code','=',$ApplicationsVerified_level2)->pluck('district_name')->first();
                    $place_name=DB::table('m_health_facility')->where('facilty_code','=',$ApplicationsVerified_level4)->pluck('facility_name')->first();
                   // dd($district_name);
                }
                
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApplicationsVerified,
                    'count'=>$ApplicationsVerified_count,
                  
                    ])->with('level1',$ApplicationsVerified_level1)
                      ->with('level2',$ApplicationsVerified_level2)
                      ->with('district_name',$district_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$ApplicationsVerified_level2d)
                      ->with('level3',$ApplicationsVerified_level3)
                      ->with('level4',$ApplicationsVerified_level4)
                      ->with('message','Applications Verified');

            }



        }elseif($ApplicationsVerified_level1=='ULB'){
// dd($PendingVerification_level2d,$PendingVerification_level2,$PendingVerification_level3,$PendingVerification_level4);
            
            if($ApplicationsVerified_level2d!=null && $ApplicationsVerified_level2!=null && $ApplicationsVerified_level3==null &&   
                $ApplicationsVerified_level4==null ) {
//dd("HI");
                $ApplicationsVerified=DB::table('nhm_employee_details')->where('body_code','=',$ApplicationsVerified_level2)->where('verification_status','=','Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $ApplicationsVerified_count=$ApplicationsVerified->count();
                //dd($applications_submitted_count);
                
                 $district_name=District::where('district_code','=',$ApplicationsVerified_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$ApplicationsVerified_level2)->pluck('urban_body_name')->first();
 //dd($PendingVerification_count);

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApplicationsVerified,
                    'count'=>$ApplicationsVerified_count
                    
                    ])->with('level1',$ApplicationsVerified_level1)
                      ->with('level2',$ApplicationsVerified_level2)
                      ->with('level2d',$ApplicationsVerified_level2d)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level3',$ApplicationsVerified_level3)
                      ->with('level4',$ApplicationsVerified_level4)
                      ->with('message','Applications Verified');

            }elseif($ApplicationsVerified_level2d!=null && $ApplicationsVerified_level2!=null && 
                $ApplicationsVerified_level3!=null && $ApplicationsVerified_level4==null){

                $ApplicationsVerified=DB::table('nhm_employee_details')->where('body_code','=',$ApplicationsVerified_level2)->where('posting_level','=',$ApplicationsVerified_level3)->where('verification_status','=','Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $ApplicationsVerified_count=$ApplicationsVerified->count();
                
                $district_name=District::where('district_code','=',$ApplicationsVerified_level2d)->pluck('district_name')->first();
                $urban_body_name=UrbanBody::where('urban_body_code','=',$ApplicationsVerified_level2)->pluck('urban_body_name')->first();





                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApplicationsVerified,
                    'count'=>$ApplicationsVerified_count
                    
                    ])->with('level1',$ApplicationsVerified_level1)
                      ->with('level2',$ApplicationsVerified_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level2d',$ApplicationsVerified_level2d)
                      ->with('level3',$ApplicationsVerified_level3)
                      ->with('level4',$ApplicationsVerified_level4)
                      ->with('message','Applications Verified');

            }elseif($ApplicationsVerified_level2d!=null && $ApplicationsVerified_level2!=null && $ApplicationsVerified_level3!=null && $ApplicationsVerified_level4!=null ){

                $ApplicationsVerified=DB::table('nhm_employee_details')->where('body_code','=',$ApplicationsVerified_level2)->where('posting_level','=',$ApplicationsVerified_level3)->where('posting_place_code','=',$ApplicationsVerified_level4)->where('verification_status','=','Verified')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $ApplicationsVerified_count=$ApplicationsVerified->count();
            
                 $district_name=District::where('district_code','=',$ApplicationsVerified_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$ApplicationsVerified_level2)->pluck('urban_body_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$ApplicationsVerified_level4)->pluck('facility_name')->first();
               
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApplicationsVerified,
                    'count'=>$ApplicationsVerified_count
                   
                    ])->with('level1',$ApplicationsVerified_level1)
                      ->with('level2',$ApplicationsVerified_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$ApplicationsVerified_level2d)
                      ->with('level3',$ApplicationsVerified_level3)
                      ->with('level4',$ApplicationsVerified_level4)
                      ->with('message','Applications Verified');

            }
        




        }elseif($ApplicationsVerified_level1=='Block'){

            if($ApplicationsVerified_level2d!=null && $ApplicationsVerified_level2!=null && $ApplicationsVerified_level3==null &&   
                $ApplicationsVerified_level4==null ) {

                $ApplicationsVerified=DB::table('nhm_employee_details')->where('body_code','=',$ApplicationsVerified_level2)->where('verification_status','=','Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $ApplicationsVerified_count=$ApplicationsVerified->count();
                //dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$ApplicationsVerified_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$ApplicationsVerified_level2)->pluck('taluka_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApplicationsVerified,
                    'count'=>$ApplicationsVerified_count
                    
                    ])->with('level1',$ApplicationsVerified_level1)
                      ->with('level2',$ApplicationsVerified_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$ApplicationsVerified_level2d)
                      ->with('level3',$ApplicationsVerified_level3)
                      ->with('level4',$ApplicationsVerified_level4)
                      ->with('message','Applications Verified');

            }elseif($ApplicationsVerified_level2d!=null && $ApplicationsVerified_level2!=null && 
                $ApplicationsVerified_level3!=null && $ApplicationsVerified_level4==null){

                $ApplicationsVerified=DB::table('nhm_employee_details')->where('body_code','=',$ApplicationsVerified_level2)->where('posting_level','=',$ApplicationsVerified_level3)->where('verification_status','=','Verified')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $ApplicationsVerified_count=$ApplicationsVerified->count();
                
                $district_name=District::where('district_code','=',$ApplicationsVerified_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$ApplicationsVerified_level2)->pluck('taluka_name')->first();
                

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApplicationsVerified,
                    'count'=>$ApplicationsVerified_count
                    
                    ])->with('level1',$ApplicationsVerified_level1)
                      ->with('level2',$ApplicationsVerified_level2)
                       ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$ApplicationsVerified_level2d)
                      ->with('level3',$ApplicationsVerified_level3)
                      ->with('level4',$ApplicationsVerified_level4)
                      ->with('message','Applications Verified');

            }elseif($ApplicationsVerified_level2d!=null && $ApplicationsVerified_level2!=null && $ApplicationsVerified_level3!=null && $ApplicationsVerified_level4!=null ){

                $ApplicationsVerified=DB::table('nhm_employee_details')->where('body_code','=',$PendingVerification_level2)->where('posting_level','=',$PendingVerification_level3)->where('posting_place_code','=',$PendingVerification_level4)->where('verification_status','=','Verified')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $ApplicationsVerified_count=$ApplicationsVerified->count();
                

                $district_name=District::where('district_code','=',$ApplicationsVerified_level2d)->pluck('district_name')->first();
                 $taluka_name=Taluka::where('taluka_code','=',$ApplicationsVerified_level2)->pluck('taluka_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$ApplicationsVerified_level4)->pluck('facility_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApplicationsVerified,
                    'count'=>$ApplicationsVerified_count
                   
                    ])->with('level1',$ApplicationsVerified_level1)
                      ->with('level2',$ApplicationsVerified_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$ApplicationsVerified_level2d)
                      ->with('level3',$ApplicationsVerified_level3)
                      ->with('level4',$ApplicationsVerified_level4)
                      ->with('message','Applications Verified');

            }
        }

    }







public function loadRejectVerification(Request $request){
        //dd($request->PendingVerification_level1);VerificationRejected_level1
        $VerificationRejected_level1=$request['VerificationRejected_level1'];
        $VerificationRejected_level2=$request['VerificationRejected_level2'];
        $VerificationRejected_level2d=$request['VerificationRejected_level2d'];
        $VerificationRejected_level3=$request['VerificationRejected_level3'];
        $VerificationRejected_level4=$request['VerificationRejected_level4'];

       
         // dd($PendingVerification_level1,$PendingVerification_level2,$PendingVerification_level2d,
         // $PendingVerification_level3,$PendingVerification_level4);

        if($VerificationRejected_level1=='State'){
            
            if( $VerificationRejected_level3==null &&   
                $VerificationRejected_level4==null && $VerificationRejected_level2d==null) {

                $VerificationRejected=DB::table('nhm_employee_details')->where('verification_status','=','Rejected')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $VerificationRejected_count=$VerificationRejected->count();
                //$applications_submitted_count=DB::table('nhm_employee_details')->count('id');
                //dd($applications_submitteds);
                if($VerificationRejected_level2==1){
                    $state_name='West Bengal';
                }
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $VerificationRejected,
                    'count'=>$VerificationRejected_count
                    
                    ])->with('level1',$VerificationRejected_level1)
                      ->with('level2',$VerificationRejected_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$VerificationRejected_level2d)
                      ->with('level3',$VerificationRejected_level3)
                      ->with('level4',$VerificationRejected_level4)
                      ->with('message','Applications Rejected At Verification');

            }elseif($VerificationRejected_level3!=null && $VerificationRejected_level4==null && $VerificationRejected_level2d==null){

                $VerificationRejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$VerificationRejected_level3)->where('verification_status','=','Rejected')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $VerificationRejected_count=$VerificationRejected->count();
               
                if($VerificationRejected_level2==1){
                    $state_name='West Bengal';
                }

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $VerificationRejected,
                    'count'=>$VerificationRejected_count
                  
                    ])->with('level1',$VerificationRejected_level1)
                      ->with('level2',$VerificationRejected_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$VerificationRejected_level2d)
                      ->with('level3',$VerificationRejected_level3)
                      ->with('level4',$VerificationRejected_level4)
                      ->with('message','Applications Rejected At Verification');

            }elseif($VerificationRejected_level3!=null && $VerificationRejected_level4!=null && $VerificationRejected_level2d==null){

                $VerificationRejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$VerificationRejected_level3)->where('posting_place_code','=',$VerificationRejected_level4)->where('verification_status','=','Rejected')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $VerificationRejected_count=$VerificationRejected->count();
                if($VerificationRejected_level2==1){
                    $state_name='West Bengal';
                }

                $place_name=DB::table('m_health_facility')->where('facilty_code','=',$VerificationRejected_level4)->pluck('facility_name')->first();
            
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $VerificationRejected,
                    'count'=>$VerificationRejected_count
                   
                    ])->with('level1',$VerificationRejected_level1)
                      ->with('level2',$VerificationRejected_level2)
                      ->with('state_name',$state_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$VerificationRejected_level2d)
                      ->with('level3',$VerificationRejected_level3)
                      ->with('level4',$VerificationRejected_level4)
                      ->with('message','Applications Rejected At Verification');

            }
        



        }elseif($VerificationRejected_level1=='District'){

        
        $urban_body_codes = UrbanBody::where('district_code', '=', $VerificationRejected_level2)->pluck('urban_body_code as body_code');
        $taluka_codes = Taluka::where('district_code', '=', $VerificationRejected_level2)->pluck('taluka_code as body_code');
        $body_codes = $urban_body_codes->merge($taluka_codes);
        $body_codes=$body_codes->merge($VerificationRejected_level2);

        //dd($urban_body_codes);

         if( $VerificationRejected_level2!=null && $VerificationRejected_level3==null &&  $VerificationRejected_level4==null && $VerificationRejected_level2d==null) {
             
                $VerificationRejected=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Rejected')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $VerificationRejected_count=$VerificationRejected->count();
               // dd($applications_submitted_count);
            
              $district_name=District::where('district_code','=',$VerificationRejected_level2)->pluck('district_name')->first();
//dd($district_name);
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $VerificationRejected,
                    'count'=>$VerificationRejected_count
                   
                    ])->with('level1',$VerificationRejected_level1)
                      ->with('level2',$VerificationRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$VerificationRejected_level2d)
                      ->with('level3',$VerificationRejected_level3)
                      ->with('level4',$VerificationRejected_level4)
                      ->with('message','Applications Rejected At Verification');

            

            }elseif($VerificationRejected_level2!=null && $VerificationRejected_level3!=null && $VerificationRejected_level4==null && $VerificationRejected_level2d==null){

                $VerificationRejected=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Rejected')->where('posting_level','=',$VerificationRejected_level3)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $VerificationRejected_count=$VerificationRejected->count();
               // dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$VerificationRejected_level2)->pluck('district_name')->first();
                


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $VerificationRejected,
                    'count'=>$VerificationRejected_count
                    
                    ])->with('level1',$VerificationRejected_level1)
                      ->with('level2',$VerificationRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$VerificationRejected_level2d)
                      ->with('level3',$VerificationRejected_level3)
                      ->with('level4',$VerificationRejected_level4)
                      ->with('message','Applications Rejected At Verification');

            }elseif($VerificationRejected_level2!=null && $VerificationRejected_level3!=null && $VerificationRejected_level4!=null && $VerificationRejected_level2d==null){

                $VerificationRejected=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Rejected')->where('posting_level','=',$VerificationRejected_level3)->where('posting_place_code','=',$VerificationRejected_level4)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
               
                $VerificationRejected_count=$VerificationRejected->count();
               
                //dd($applications_submitted_count);
                
                if($VerificationRejected_level3=='ACMOH Office')
                {
                    $district_name = District::where('district_code','=',$VerificationRejected_level2)->pluck('district_name')->first();
                    //dd($district_name);
                    $place_name=SubDistrict::where('sub_district_code','=',$VerificationRejected_level4)->pluck('sub_district_name')->first();
                
                }elseif($VerificationRejected_level3=='ULB'){
                     $district_name = District::where('district_code','=',$VerificationRejected_level2)->pluck('district_name')->first();
                     $place_name=UrbanBody::where('urban_body_code','=',$VerificationRejected_level4)->pluck('urban_body_name')->first();
                }else{
                    $district_name = District::where('district_code','=',$VerificationRejected_level2)->pluck('district_name')->first();
                    $place_name=DB::table('m_health_facility')->where('facilty_code','=',$VerificationRejected_level4)->pluck('facility_name')->first();
                   // dd($district_name);
                }
                
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $VerificationRejected,
                    'count'=>$VerificationRejected_count,
                  
                    ])->with('level1',$VerificationRejected_level1)
                      ->with('level2',$VerificationRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$VerificationRejected_level2d)
                      ->with('level3',$VerificationRejected_level3)
                      ->with('level4',$VerificationRejected_level4)
                      ->with('message','Applications Rejected At Verification');

            }



        }elseif($VerificationRejected_level1=='ULB'){
// dd($PendingVerification_level2d,$PendingVerification_level2,$PendingVerification_level3,$PendingVerification_level4);
            
            if($VerificationRejected_level2d!=null && $VerificationRejected_level2!=null && $VerificationRejected_level3==null &&   
                $VerificationRejected_level4==null ) {
//dd("HI");
                $VerificationRejected=DB::table('nhm_employee_details')->where('body_code','=',$VerificationRejected_level2)->where('verification_status','=','Rejected')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $VerificationRejected_count=$VerificationRejected->count();
                //dd($applications_submitted_count);
                
                 $district_name=District::where('district_code','=',$VerificationRejected_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$VerificationRejected_level2)->pluck('urban_body_name')->first();
 //dd($PendingVerification_count);

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $VerificationRejected,
                    'count'=>$VerificationRejected_count
                    
                    ])->with('level1',$VerificationRejected_level1)
                      ->with('level2',$VerificationRejected_level2)
                      ->with('level2d',$VerificationRejected_level2d)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level3',$VerificationRejected_level3)
                      ->with('level4',$VerificationRejected_level4)
                      ->with('message','Applications Rejected At Verification');

            }elseif($VerificationRejected_level2d!=null && $VerificationRejected_level2!=null && 
                $VerificationRejected_level3!=null && $VerificationRejected_level4==null){

                $VerificationRejected=DB::table('nhm_employee_details')->where('body_code','=',$VerificationRejected_level2)->where('posting_level','=',$VerificationRejected_level3)->where('verification_status','=','Rejected')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $VerificationRejected_count=$VerificationRejected->count();
                
                $district_name=District::where('district_code','=',$VerificationRejected_level2d)->pluck('district_name')->first();
                $urban_body_name=UrbanBody::where('urban_body_code','=',$VerificationRejected_level2)->pluck('urban_body_name')->first();





                return view('employee-report-drilldown.linelisting',
                    ['datas' => $VerificationRejected,
                    'count'=>$VerificationRejected_count
                    
                    ])->with('level1',$VerificationRejected_level1)
                      ->with('level2',$VerificationRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level2d',$VerificationRejected_level2d)
                      ->with('level3',$VerificationRejected_level3)
                      ->with('level4',$VerificationRejected_level4)
                      ->with('message','Applications Rejected At Verification');

            }elseif($VerificationRejected_level2d!=null && $VerificationRejected_level2!=null && $VerificationRejected_level3!=null && $VerificationRejected_level4!=null ){

                $VerificationRejected=DB::table('nhm_employee_details')->where('body_code','=',$VerificationRejected_level2)->where('posting_level','=',$VerificationRejected_level3)->where('posting_place_code','=',$VerificationRejected_level4)->where('verification_status','=','Rejected')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $VerificationRejected_count=$VerificationRejected->count();
            
                 $district_name=District::where('district_code','=',$VerificationRejected_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$VerificationRejected_level2)->pluck('urban_body_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$VerificationRejected_level4)->pluck('facility_name')->first();
               
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $VerificationRejected,
                    'count'=>$VerificationRejected_count
                   
                    ])->with('level1',$VerificationRejected_level1)
                      ->with('level2',$VerificationRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$VerificationRejected_level2d)
                      ->with('level3',$VerificationRejected_level3)
                      ->with('level4',$VerificationRejected_level4)
                      ->with('message','Applications Rejected At Verification');
            }
        




        }elseif($VerificationRejected_level1=='Block'){

            if($VerificationRejected_level2d!=null && $VerificationRejected_level2!=null && $VerificationRejected_level3==null &&   
                $VerificationRejected_level4==null ) {

                $VerificationRejected=DB::table('nhm_employee_details')->where('body_code','=',$VerificationRejected_level2)->where('verification_status','=','Rejected')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $VerificationRejected_count=$VerificationRejected->count();
                //dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$VerificationRejected_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$VerificationRejected_level2)->pluck('taluka_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $VerificationRejected,
                    'count'=>$VerificationRejected_count
                    
                    ])->with('level1',$VerificationRejected_level1)
                      ->with('level2',$VerificationRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$VerificationRejected_level2d)
                      ->with('level3',$VerificationRejected_level3)
                      ->with('level4',$VerificationRejected_level4)
                      ->with('message','Applications Rejected At Verification');

            }elseif($VerificationRejected_level2d!=null && $VerificationRejected_level2!=null && 
                $VerificationRejected_level3!=null && $VerificationRejected_level4==null){

                $VerificationRejected=DB::table('nhm_employee_details')->where('body_code','=',$VerificationRejected_level2)->where('posting_level','=',$VerificationRejected_level3)->where('verification_status','=','Rejected')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $VerificationRejected_count=$VerificationRejected->count();
                
                $district_name=District::where('district_code','=',$VerificationRejected_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$VerificationRejected_level2)->pluck('taluka_name')->first();
                

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $VerificationRejected,
                    'count'=>$VerificationRejected_count
                    
                    ])->with('level1',$VerificationRejected_level1)
                      ->with('level2',$VerificationRejected_level2)
                       ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$VerificationRejected_level2d)
                      ->with('level3',$VerificationRejected_level3)
                      ->with('level4',$VerificationRejected_level4)
                      ->with('message','Applications Rejected At Verification');

            }elseif($VerificationRejected_level2d!=null && $VerificationRejected_level2!=null && $VerificationRejected_level3!=null && $VerificationRejected_level4!=null ){

                $VerificationRejected=DB::table('nhm_employee_details')->where('body_code','=',$PendingVerification_level2)->where('posting_level','=',$PendingVerification_level3)->where('posting_place_code','=',$PendingVerification_level4)->where('verification_status','=','Rejected')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $VerificationRejected_count=$VerificationRejected->count();
                

                $district_name=District::where('district_code','=',$VerificationRejected_level2d)->pluck('district_name')->first();
                 $taluka_name=Taluka::where('taluka_code','=',$VerificationRejected_level2)->pluck('taluka_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$VerificationRejected_level4)->pluck('facility_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $VerificationRejected,
                    'count'=>$VerificationRejected_count
                   
                    ])->with('level1',$VerificationRejected_level1)
                      ->with('level2',$VerificationRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$VerificationRejected_level2d)
                      ->with('level3',$VerificationRejected_level3)
                      ->with('level4',$VerificationRejected_level4)
                      ->with('message','Applications Rejected At Verification');

            }
        }

    }




public function loadPendingApproval(Request $request){
        //dd($request->PendingVerification_level1);PendingApproval_level1
        $PendingApproval_level1=$request['PendingApproval_level1'];
        $PendingApproval_level2=$request['PendingApproval_level2'];
        $PendingApproval_level2d=$request['PendingApproval_level2d'];
        $PendingApproval_level3=$request['PendingApproval_level3'];
        $PendingApproval_level4=$request['PendingApproval_level4'];

       
         // dd($PendingVerification_level1,$PendingVerification_level2,$PendingVerification_level2d,
         // $PendingVerification_level3,$PendingVerification_level4);

        if($PendingApproval_level1=='State'){
            
            if( $PendingApproval_level3==null &&   
                $PendingApproval_level4==null && $PendingApproval_level2d==null) {

                $PendingApproval=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $PendingApproval_count=$PendingApproval->count();
                //$applications_submitted_count=DB::table('nhm_employee_details')->count('id');
                //dd($applications_submitteds);
                if($PendingApproval_level2==1){
                    $state_name='West Bengal';
                }
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingApproval,
                    'count'=>$PendingApproval_count
                    
                    ])->with('level1',$PendingApproval_level1)
                      ->with('level2',$PendingApproval_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$PendingApproval_level2d)
                      ->with('level3',$PendingApproval_level3)
                      ->with('level4',$PendingApproval_level4)
                      ->with('message','Applications Pending For Approval');;

            }elseif($PendingApproval_level3!=null && $PendingApproval_level4==null && $PendingApproval_level2d==null){

                $PendingApproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$PendingApproval_level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $PendingApproval_count=$PendingApproval->count();
               
                if($PendingApproval_level2==1){
                    $state_name='West Bengal';
                }

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingApproval,
                    'count'=>$PendingApproval_count
                  
                    ])->with('level1',$PendingApproval_level1)
                      ->with('level2',$PendingApproval_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$PendingApproval_level2d)
                      ->with('level3',$PendingApproval_level3)
                      ->with('level4',$PendingApproval_level4)
                      ->with('message','Applications Pending For Approval');

            }elseif($PendingApproval_level3!=null && $PendingApproval_level4!=null && $PendingApproval_level2d==null){

                $PendingApproval=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$PendingApproval_level3)->where('posting_place_code','=',$PendingApproval_level4)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $PendingApproval_count=$PendingApproval->count();
                if($PendingApproval_level2==1){
                    $state_name='West Bengal';
                }

                $place_name=DB::table('m_health_facility')->where('facilty_code','=',$PendingApproval_level4)->pluck('facility_name')->first();
            
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingApproval,
                    'count'=>$PendingApproval_count
                   
                    ])->with('level1',$PendingApproval_level1)
                      ->with('level2',$PendingApproval_level2)
                      ->with('state_name',$state_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$PendingApproval_level2d)
                      ->with('level3',$PendingApproval_level3)
                      ->with('level4',$PendingApproval_level4)
                      ->with('message','Applications Pending For Approval');

            }
        



        }elseif($PendingApproval_level1=='District'){

        
        $urban_body_codes = UrbanBody::where('district_code', '=', $PendingApproval_level2)->pluck('urban_body_code as body_code');
        $taluka_codes = Taluka::where('district_code', '=', $PendingApproval_level2)->pluck('taluka_code as body_code');
        $body_codes = $urban_body_codes->merge($taluka_codes);
        $body_codes=$body_codes->merge($PendingApproval_level2);

        //dd($urban_body_codes);

         if( $PendingApproval_level2!=null && $PendingApproval_level3==null &&  $PendingApproval_level4==null && $PendingApproval_level2d==null) {
             
                $PendingApproval=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $PendingApproval_count=$PendingApproval->count();
               // dd($applications_submitted_count);
            
              $district_name=District::where('district_code','=',$PendingApproval_level2)->pluck('district_name')->first();
//dd($district_name);
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingApproval,
                    'count'=>$PendingApproval_count
                   
                    ])->with('level1',$PendingApproval_level1)
                      ->with('level2',$PendingApproval_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$PendingApproval_level2d)
                      ->with('level3',$PendingApproval_level3)
                      ->with('level4',$PendingApproval_level4)
                      ->with('message','Applications Pending For Approval');

            

            }elseif($PendingApproval_level2!=null && $PendingApproval_level3!=null && $PendingApproval_level4==null && $PendingApproval_level2d==null){

                $PendingApproval=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->where('posting_level','=',$PendingApproval_level3)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $PendingApproval_count=$PendingApproval->count();
               // dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$PendingApproval_level2)->pluck('district_name')->first();
                


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingApproval,
                    'count'=>$PendingApproval_count
                    
                    ])->with('level1',$PendingApproval_level1)
                      ->with('level2',$PendingApproval_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$PendingApproval_level2d)
                      ->with('level3',$PendingApproval_level3)
                      ->with('level4',$PendingApproval_level4)
                      ->with('message','Applications Pending For Approval');

            }elseif($PendingApproval_level2!=null && $PendingApproval_level3!=null && $PendingApproval_level4!=null && $PendingApproval_level2d==null){

                $PendingApproval=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->where('posting_level','=',$PendingApproval_level3)->where('posting_place_code','=',$PendingApproval_level4)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
               
                $PendingApproval_count=$PendingApproval->count();
               
                //dd($applications_submitted_count);
                
                if($PendingApproval_level3=='ACMOH Office')
                {
                    $district_name = District::where('district_code','=',$PendingApproval_level2)->pluck('district_name')->first();
                    //dd($district_name);
                    $place_name=SubDistrict::where('sub_district_code','=',$PendingApproval_level4)->pluck('sub_district_name')->first();
                
                }elseif($PendingApproval_level3=='ULB'){
                     $district_name = District::where('district_code','=',$PendingApproval_level2)->pluck('district_name')->first();
                     $place_name=UrbanBody::where('urban_body_code','=',$PendingApproval_level4)->pluck('urban_body_name')->first();
                }else{
                    $district_name = District::where('district_code','=',$PendingApproval_level2)->pluck('district_name')->first();
                    $place_name=DB::table('m_health_facility')->where('facilty_code','=',$PendingApproval_level4)->pluck('facility_name')->first();
                   // dd($district_name);
                }
                
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingApproval,
                    'count'=>$PendingApproval_count,
                  
                    ])->with('level1',$PendingApproval_level1)
                      ->with('level2',$PendingApproval_level2)
                      ->with('district_name',$district_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$PendingApproval_level2d)
                      ->with('level3',$PendingApproval_level3)
                      ->with('level4',$PendingApproval_level4)
                      ->with('message','Applications Pending For Approval');

            }



        }elseif($PendingApproval_level1=='ULB'){
// dd($PendingVerification_level2d,$PendingVerification_level2,$PendingVerification_level3,$PendingVerification_level4);
            
            if($PendingApproval_level2d!=null && $PendingApproval_level2!=null && $PendingApproval_level3==null &&   
                $PendingApproval_level4==null ) {
//dd("HI");
                $PendingApproval=DB::table('nhm_employee_details')->where('body_code','=',$PendingApproval_level2)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->paginate(40);//get();
                
                $PendingApproval_count=$PendingApproval->count();
                //dd($applications_submitted_count);
                
                 $district_name=District::where('district_code','=',$PendingApproval_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$PendingApproval_level2)->pluck('urban_body_name')->first();
 //dd($PendingVerification_count);

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingApproval,
                    'count'=>$PendingApproval_count
                    
                    ])->with('level1',$PendingApproval_level1)
                      ->with('level2',$PendingApproval_level2)
                      ->with('level2d',$PendingApproval_level2d)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level3',$PendingApproval_level3)
                      ->with('level4',$PendingApproval_level4)
                      ->with('message','Applications Pending For Approval');

            }elseif($PendingApproval_level2d!=null && $PendingApproval_level2!=null && 
                $PendingApproval_level3!=null && $PendingApproval_level4==null){

                $PendingApproval=DB::table('nhm_employee_details')->where('body_code','=',$PendingApproval_level2)->where('posting_level','=',$PendingApproval_level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $PendingApproval_count=$PendingApproval->count();
                
                $district_name=District::where('district_code','=',$PendingApproval_level2d)->pluck('district_name')->first();
                $urban_body_name=UrbanBody::where('urban_body_code','=',$PendingApproval_level2)->pluck('urban_body_name')->first();





                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingApproval,
                    'count'=>$PendingApproval_count
                    
                    ])->with('level1',$PendingApproval_level1)
                      ->with('level2',$PendingApproval_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level2d',$PendingApproval_level2d)
                      ->with('level3',$PendingApproval_level3)
                      ->with('level4',$PendingApproval_level4)
                      ->with('message','Applications Pending For Approval');

            }elseif($PendingApproval_level2d!=null && $PendingApproval_level2!=null && $PendingApproval_level3!=null && $PendingApproval_level4!=null ){

                $PendingApproval=DB::table('nhm_employee_details')->where('body_code','=',$PendingApproval_level2)->where('posting_level','=',$PendingApproval_level3)->where('posting_place_code','=',$PendingApproval_level4)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $PendingApproval_count=$PendingApproval->count();
            
                 $district_name=District::where('district_code','=',$PendingApproval_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$PendingApproval_level2)->pluck('urban_body_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$PendingApproval_level4)->pluck('facility_name')->first();
               
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingApproval,
                    'count'=>$PendingApproval_count
                   
                    ])->with('level1',$PendingApproval_level1)
                      ->with('level2',$PendingApproval_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$PendingApproval_level2d)
                      ->with('level3',$PendingApproval_level3)
                      ->with('level4',$PendingApproval_level4)
                      ->with('message','Applications Pending For Approval');

            }
        




        }elseif($PendingApproval_level1=='Block'){

            if($PendingApproval_level2d!=null && $PendingApproval_level2!=null && $PendingApproval_level3==null &&   
                $PendingApproval_level4==null ) {

                $PendingApproval=DB::table('nhm_employee_details')->where('body_code','=',$PendingApproval_level2)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $PendingApproval_count=$PendingApproval->count();
                //dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$PendingApproval_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$PendingApproval_level2)->pluck('taluka_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingApproval,
                    'count'=>$PendingApproval_count
                    
                    ])->with('level1',$PendingApproval_level1)
                      ->with('level2',$PendingApproval_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$PendingApproval_level2d)
                      ->with('level3',$PendingApproval_level3)
                      ->with('level4',$PendingApproval_level4)
                      ->with('message','Applications Pending For Approval');

            }elseif($PendingApproval_level2d!=null && $PendingApproval_level2!=null && 
                $PendingApproval_level3!=null && $PendingApproval_level4==null){

                $PendingApproval=DB::table('nhm_employee_details')->where('body_code','=',$PendingApproval_level2)->where('posting_level','=',$PendingApproval_level3)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $PendingApproval_count=$PendingApproval->count();
                
                $district_name=District::where('district_code','=',$PendingApproval_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$PendingApproval_level2)->pluck('taluka_name')->first();
                

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingApproval,
                    'count'=>$PendingApproval_count
                    
                    ])->with('level1',$PendingApproval_level1)
                      ->with('level2',$PendingApproval_level2)
                       ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$PendingApproval_level2d)
                      ->with('level3',$PendingApproval_level3)
                      ->with('level4',$PendingApproval_level4)
                      ->with('message','Applications Pending For Approval');

            }elseif($PendingApproval_level2d!=null && $PendingApproval_level2!=null && $PendingApproval_level3!=null && $PendingApproval_level4!=null ){

                $PendingApproval=DB::table('nhm_employee_details')->where('body_code','=',$PendingVerification_level2)->where('posting_level','=',$PendingVerification_level3)->where('posting_place_code','=',$PendingVerification_level4)->where('verification_status','=','Verified')->where('approval_status','=','Not Approved')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $PendingApproval_count=$PendingApproval->count();
                

                $district_name=District::where('district_code','=',$PendingApproval_level2d)->pluck('district_name')->first();
                 $taluka_name=Taluka::where('taluka_code','=',$PendingApproval_level2)->pluck('taluka_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$PendingApproval_level4)->pluck('facility_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $PendingApproval,
                    'count'=>$PendingApproval_count
                   
                    ])->with('level1',$PendingApproval_level1)
                      ->with('level2',$PendingApproval_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$PendingApproval_level2d)
                      ->with('level3',$PendingApproval_level3)
                      ->with('level4',$PendingApproval_level4)
                      ->with('message','Applications Pending For Approval');

            }
        }

    }



public function loadApproved(Request $request){
        //dd($request->PendingVerification_level1);EmployeeCodeGenerated_level1
        $EmployeeCodeGenerated_level1=$request['EmployeeCodeGenerated_level1'];
        $EmployeeCodeGenerated_level2=$request['EmployeeCodeGenerated_level2'];
        $EmployeeCodeGenerated_level2d=$request['EmployeeCodeGenerated_level2d'];
        $EmployeeCodeGenerated_level3=$request['EmployeeCodeGenerated_level3'];
        $EmployeeCodeGenerated_level4=$request['EmployeeCodeGenerated_level4'];

       
         // dd($PendingVerification_level1,$PendingVerification_level2,$PendingVerification_level2d,
         // $PendingVerification_level3,$PendingVerification_level4);

        if($EmployeeCodeGenerated_level1=='State'){
            
            if( $EmployeeCodeGenerated_level3==null &&   
                $EmployeeCodeGenerated_level4==null && $EmployeeCodeGenerated_level2d==null) {

                $EmployeeCodeGenerated=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('approval_status','=','Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $EmployeeCodeGenerated_count=$EmployeeCodeGenerated->count();
                //$applications_submitted_count=DB::table('nhm_employee_details')->count('id');
                //dd($applications_submitteds);
                if($EmployeeCodeGenerated_level2==1){
                    $state_name='West Bengal';
                }
                return view('employee-report-drilldown.linelisting_mass_approved',
                    ['datas' => $EmployeeCodeGenerated,
                    'count'=>$EmployeeCodeGenerated_count
                    
                    ])->with('level1',$EmployeeCodeGenerated_level1)
                      ->with('level2',$EmployeeCodeGenerated_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$EmployeeCodeGenerated_level2d)
                      ->with('level3',$EmployeeCodeGenerated_level3)
                      ->with('level4',$EmployeeCodeGenerated_level4)
                      ->with('message','Applications Approved');

            }elseif($EmployeeCodeGenerated_level3!=null && $EmployeeCodeGenerated_level4==null && $EmployeeCodeGenerated_level2d==null){

                $EmployeeCodeGenerated=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$EmployeeCodeGenerated_level3)->where('verification_status','=','Verified')->where('approval_status','=','Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $EmployeeCodeGenerated_count=$EmployeeCodeGenerated->count();
               
                if($EmployeeCodeGenerated_level2==1){
                    $state_name='West Bengal';
                }

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $EmployeeCodeGenerated,
                    'count'=>$EmployeeCodeGenerated_count
                  
                    ])->with('level1',$EmployeeCodeGenerated_level1)
                      ->with('level2',$EmployeeCodeGenerated_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$EmployeeCodeGenerated_level2d)
                      ->with('level3',$EmployeeCodeGenerated_level3)
                      ->with('level4',$EmployeeCodeGenerated_level4)
                      ->with('message','Applications Approved');

            }elseif($EmployeeCodeGenerated_level3!=null && $EmployeeCodeGenerated_level4!=null && $EmployeeCodeGenerated_level2d==null){

                $EmployeeCodeGenerated=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$EmployeeCodeGenerated_level3)->where('posting_place_code','=',$EmployeeCodeGenerated_level4)->where('verification_status','=','Verified')->where('approval_status','=','Approved')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $EmployeeCodeGenerated_count=$EmployeeCodeGenerated->count();
                if($EmployeeCodeGenerated_level2==1){
                    $state_name='West Bengal';
                }

                $place_name=DB::table('m_health_facility')->where('facilty_code','=',$EmployeeCodeGenerated_level4)->pluck('facility_name')->first();
            
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $EmployeeCodeGenerated,
                    'count'=>$EmployeeCodeGenerated_count
                   
                    ])->with('level1',$EmployeeCodeGenerated_level1)
                      ->with('level2',$EmployeeCodeGenerated_level2)
                      ->with('state_name',$state_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$EmployeeCodeGenerated_level2d)
                      ->with('level3',$EmployeeCodeGenerated_level3)
                      ->with('level4',$EmployeeCodeGenerated_level4)
                      ->with('message','Applications Approved');

            }
        



        }elseif($EmployeeCodeGenerated_level1=='District'){

        
        $urban_body_codes = UrbanBody::where('district_code', '=', $EmployeeCodeGenerated_level2)->pluck('urban_body_code as body_code');
        $taluka_codes = Taluka::where('district_code', '=', $EmployeeCodeGenerated_level2)->pluck('taluka_code as body_code');
        $body_codes = $urban_body_codes->merge($taluka_codes);
        $body_codes=$body_codes->merge($EmployeeCodeGenerated_level2);

        //dd($urban_body_codes);

         if( $EmployeeCodeGenerated_level2!=null && $EmployeeCodeGenerated_level3==null &&  $EmployeeCodeGenerated_level4==null && $EmployeeCodeGenerated_level2d==null) {
             
                $EmployeeCodeGenerated=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Verified')->where('approval_status','=','Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $EmployeeCodeGenerated_count=$EmployeeCodeGenerated->count();
               // dd($applications_submitted_count);
            
              $district_name=District::where('district_code','=',$EmployeeCodeGenerated_level2)->pluck('district_name')->first();
//dd($district_name);
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $EmployeeCodeGenerated,
                    'count'=>$EmployeeCodeGenerated_count
                   
                    ])->with('level1',$EmployeeCodeGenerated_level1)
                      ->with('level2',$EmployeeCodeGenerated_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$EmployeeCodeGenerated_level2d)
                      ->with('level3',$EmployeeCodeGenerated_level3)
                      ->with('level4',$EmployeeCodeGenerated_level4)
                      ->with('message','Applications Approved');

            

            }elseif($EmployeeCodeGenerated_level2!=null && $EmployeeCodeGenerated_level3!=null && $EmployeeCodeGenerated_level4==null && $EmployeeCodeGenerated_level2d==null){

                $EmployeeCodeGenerated=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Verified')->where('approval_status','=','Approved')->where('posting_level','=',$EmployeeCodeGenerated_level3)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $EmployeeCodeGenerated_count=$EmployeeCodeGenerated->count();
               // dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$EmployeeCodeGenerated_level2)->pluck('district_name')->first();
                


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $EmployeeCodeGenerated,
                    'count'=>$EmployeeCodeGenerated_count
                    
                    ])->with('level1',$EmployeeCodeGenerated_level1)
                      ->with('level2',$EmployeeCodeGenerated_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$EmployeeCodeGenerated_level2d)
                      ->with('level3',$EmployeeCodeGenerated_level3)
                      ->with('level4',$EmployeeCodeGenerated_level4)
                      ->with('message','Applications Approved');

            }elseif($EmployeeCodeGenerated_level2!=null && $EmployeeCodeGenerated_level3!=null && $EmployeeCodeGenerated_level4!=null && $EmployeeCodeGenerated_level2d==null){

                $EmployeeCodeGenerated=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Verified')->where('approval_status','=','Approved')->where('posting_level','=',$EmployeeCodeGenerated_level3)->where('posting_place_code','=',$EmployeeCodeGenerated_level4)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
               
                $EmployeeCodeGenerated_count=$EmployeeCodeGenerated->count();
               
                //dd($applications_submitted_count);
                
                if($EmployeeCodeGenerated_level3=='ACMOH Office')
                {
                    $district_name = District::where('district_code','=',$EmployeeCodeGenerated_level2)->pluck('district_name')->first();
                    //dd($district_name);
                    $place_name=SubDistrict::where('sub_district_code','=',$EmployeeCodeGenerated_level4)->pluck('sub_district_name')->first();
                
                }elseif($EmployeeCodeGenerated_level3=='ULB'){
                     $district_name = District::where('district_code','=',$EmployeeCodeGenerated_level2)->pluck('district_name')->first();
                     $place_name=UrbanBody::where('urban_body_code','=',$EmployeeCodeGenerated_level4)->pluck('urban_body_name')->first();
                }else{
                    $district_name = District::where('district_code','=',$EmployeeCodeGenerated_level2)->pluck('district_name')->first();
                    $place_name=DB::table('m_health_facility')->where('facilty_code','=',$EmployeeCodeGenerated_level4)->pluck('facility_name')->first();
                   // dd($district_name);
                }
                
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $EmployeeCodeGenerated,
                    'count'=>$EmployeeCodeGenerated_count,
                  
                    ])->with('level1',$EmployeeCodeGenerated_level1)
                      ->with('level2',$EmployeeCodeGenerated_level2)
                      ->with('district_name',$district_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$EmployeeCodeGenerated_level2d)
                      ->with('level3',$EmployeeCodeGenerated_level3)
                      ->with('level4',$EmployeeCodeGenerated_level4)
                      ->with('message','Applications Approved');

            }



        }elseif($EmployeeCodeGenerated_level1=='ULB'){
// dd($PendingVerification_level2d,$PendingVerification_level2,$PendingVerification_level3,$PendingVerification_level4);
            
            if($EmployeeCodeGenerated_level2d!=null && $EmployeeCodeGenerated_level2!=null && $EmployeeCodeGenerated_level3==null &&   
                $EmployeeCodeGenerated_level4==null ) {
//dd("HI");
                $EmployeeCodeGenerated=DB::table('nhm_employee_details')->where('body_code','=',$EmployeeCodeGenerated_level2)->where('verification_status','=','Verified')->where('approval_status','=','Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $EmployeeCodeGenerated_count=$EmployeeCodeGenerated->count();
                //dd($applications_submitted_count);
                
                 $district_name=District::where('district_code','=',$EmployeeCodeGenerated_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$EmployeeCodeGenerated_level2)->pluck('urban_body_name')->first();
 //dd($PendingVerification_count);

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $EmployeeCodeGenerated,
                    'count'=>$EmployeeCodeGenerated_count
                    
                    ])->with('level1',$EmployeeCodeGenerated_level1)
                      ->with('level2',$EmployeeCodeGenerated_level2)
                      ->with('level2d',$EmployeeCodeGenerated_level2d)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level3',$EmployeeCodeGenerated_level3)
                      ->with('level4',$EmployeeCodeGenerated_level4)
                      ->with('message','Applications Approved');

            }elseif($EmployeeCodeGenerated_level2d!=null && $EmployeeCodeGenerated_level2!=null && 
                $EmployeeCodeGenerated_level3!=null && $EmployeeCodeGenerated_level4==null){

                $EmployeeCodeGenerated=DB::table('nhm_employee_details')->where('body_code','=',$EmployeeCodeGenerated_level2)->where('posting_level','=',$EmployeeCodeGenerated_level3)->where('verification_status','=','Verified')->where('approval_status','=','Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $EmployeeCodeGenerated_count=$EmployeeCodeGenerated->count();
                
                $district_name=District::where('district_code','=',$EmployeeCodeGenerated_level2d)->pluck('district_name')->first();
                $urban_body_name=UrbanBody::where('urban_body_code','=',$EmployeeCodeGenerated_level2)->pluck('urban_body_name')->first();





                return view('employee-report-drilldown.linelisting',
                    ['datas' => $EmployeeCodeGenerated,
                    'count'=>$EmployeeCodeGenerated_count
                    
                    ])->with('level1',$EmployeeCodeGenerated_level1)
                      ->with('level2',$EmployeeCodeGenerated_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level2d',$EmployeeCodeGenerated_level2d)
                      ->with('level3',$EmployeeCodeGenerated_level3)
                      ->with('level4',$EmployeeCodeGenerated_level4)
                      ->with('message','Applications Approved');

            }elseif($EmployeeCodeGenerated_level2d!=null && $EmployeeCodeGenerated_level2!=null && $EmployeeCodeGenerated_level3!=null && $EmployeeCodeGenerated_level4!=null ){

                $EmployeeCodeGenerated=DB::table('nhm_employee_details')->where('body_code','=',$EmployeeCodeGenerated_level2)->where('posting_level','=',$EmployeeCodeGenerated_level3)->where('posting_place_code','=',$EmployeeCodeGenerated_level4)->where('verification_status','=','Verified')->where('approval_status','=','Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $EmployeeCodeGenerated_count=$EmployeeCodeGenerated->count();
            
                 $district_name=District::where('district_code','=',$EmployeeCodeGenerated_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$EmployeeCodeGenerated_level2)->pluck('urban_body_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$EmployeeCodeGenerated_level4)->pluck('facility_name')->first();
               
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $EmployeeCodeGenerated,
                    'count'=>$EmployeeCodeGenerated_count
                   
                    ])->with('level1',$EmployeeCodeGenerated_level1)
                      ->with('level2',$EmployeeCodeGenerated_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$EmployeeCodeGenerated_level2d)
                      ->with('level3',$EmployeeCodeGenerated_level3)
                      ->with('level4',$EmployeeCodeGenerated_level4)
                      ->with('message','Applications Approved');

            }
        




        }elseif($EmployeeCodeGenerated_level1=='Block'){

            if($EmployeeCodeGenerated_level2d!=null && $EmployeeCodeGenerated_level2!=null && $EmployeeCodeGenerated_level3==null &&   
                $EmployeeCodeGenerated_level4==null ) {

                $EmployeeCodeGenerated=DB::table('nhm_employee_details')->where('body_code','=',$EmployeeCodeGenerated_level2)->where('verification_status','=','Verified')->where('approval_status','=','Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $EmployeeCodeGenerated_count=$EmployeeCodeGenerated->count();
                //dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$EmployeeCodeGenerated_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$EmployeeCodeGenerated_level2)->pluck('taluka_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $EmployeeCodeGenerated,
                    'count'=>$EmployeeCodeGenerated_count
                    
                    ])->with('level1',$EmployeeCodeGenerated_level1)
                      ->with('level2',$EmployeeCodeGenerated_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$EmployeeCodeGenerated_level2d)
                      ->with('level3',$EmployeeCodeGenerated_level3)
                      ->with('level4',$EmployeeCodeGenerated_level4)
                      ->with('message','Applications Approved');

            }elseif($EmployeeCodeGenerated_level2d!=null && $EmployeeCodeGenerated_level2!=null && 
                $EmployeeCodeGenerated_level3!=null && $EmployeeCodeGenerated_level4==null){

                $EmployeeCodeGenerated=DB::table('nhm_employee_details')->where('body_code','=',$EmployeeCodeGenerated_level2)->where('posting_level','=',$EmployeeCodeGenerated_level3)->where('verification_status','=','Verified')->where('approval_status','=','Approved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $EmployeeCodeGenerated_count=$EmployeeCodeGenerated->count();
                
                $district_name=District::where('district_code','=',$EmployeeCodeGenerated_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$EmployeeCodeGenerated_level2)->pluck('taluka_name')->first();
                

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $EmployeeCodeGenerated,
                    'count'=>$EmployeeCodeGenerated_count
                    
                    ])->with('level1',$EmployeeCodeGenerated_level1)
                      ->with('level2',$EmployeeCodeGenerated_level2)
                       ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$EmployeeCodeGenerated_level2d)
                      ->with('level3',$EmployeeCodeGenerated_level3)
                      ->with('level4',$EmployeeCodeGenerated_level4)
                      ->with('message','Applications Approved');

            }elseif($EmployeeCodeGenerated_level2d!=null && $EmployeeCodeGenerated_level2!=null && $EmployeeCodeGenerated_level3!=null && $EmployeeCodeGenerated_level4!=null ){

                $EmployeeCodeGenerated=DB::table('nhm_employee_details')->where('body_code','=',$PendingVerification_level2)->where('posting_level','=',$PendingVerification_level3)->where('posting_place_code','=',$PendingVerification_level4)->where('verification_status','=','Verified')->where('approval_status','=','Approved')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $EmployeeCodeGenerated_count=$EmployeeCodeGenerated->count();
                

                $district_name=District::where('district_code','=',$EmployeeCodeGenerated_level2d)->pluck('district_name')->first();
                 $taluka_name=Taluka::where('taluka_code','=',$EmployeeCodeGenerated_level2)->pluck('taluka_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$EmployeeCodeGenerated_level4)->pluck('facility_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $EmployeeCodeGenerated,
                    'count'=>$EmployeeCodeGenerated_count
                   
                    ])->with('level1',$EmployeeCodeGenerated_level1)
                      ->with('level2',$EmployeeCodeGenerated_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$EmployeeCodeGenerated_level2d)
                      ->with('level3',$EmployeeCodeGenerated_level3)
                      ->with('level4',$EmployeeCodeGenerated_level4)
                      ->with('message','Applications Approved');

            }
        }

    }

public function loadRejectApproval(Request $request){
        //dd($request->PendingVerification_level1);ApprovalRejected_level1
        $ApprovalRejected_level1=$request['ApprovalRejected_level1'];
        $ApprovalRejected_level2=$request['ApprovalRejected_level2'];
        $ApprovalRejected_level2d=$request['ApprovalRejected_level2d'];
        $ApprovalRejected_level3=$request['ApprovalRejected_level3'];
        $ApprovalRejected_level4=$request['ApprovalRejected_level4'];

       
         // dd($PendingVerification_level1,$PendingVerification_level2,$PendingVerification_level2d,
         // $PendingVerification_level3,$PendingVerification_level4);

        if($ApprovalRejected_level1=='State'){
            
            if( $ApprovalRejected_level3==null &&   
                $ApprovalRejected_level4==null && $ApprovalRejected_level2d==null) {

                $ApprovalRejected=DB::table('nhm_employee_details')->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $ApprovalRejected_count=$ApprovalRejected->count();
                //$applications_submitted_count=DB::table('nhm_employee_details')->count('id');
                //dd($applications_submitteds);
                if($ApprovalRejected_level2==1){
                    $state_name='West Bengal';
                }
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApprovalRejected,
                    'count'=>$ApprovalRejected_count
                    
                    ])->with('level1',$ApprovalRejected_level1)
                      ->with('level2',$ApprovalRejected_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$ApprovalRejected_level2d)
                      ->with('level3',$ApprovalRejected_level3)
                      ->with('level4',$ApprovalRejected_level4)
                      ->with('message','Applications Rejected At Approval');

            }elseif($ApprovalRejected_level3!=null && $ApprovalRejected_level4==null && $ApprovalRejected_level2d==null){

                $ApprovalRejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$ApprovalRejected_level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $ApprovalRejected_count=$ApprovalRejected->count();
               
                if($ApprovalRejected_level2==1){
                    $state_name='West Bengal';
                }

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApprovalRejected,
                    'count'=>$ApprovalRejected_count
                  
                    ])->with('level1',$ApprovalRejected_level1)
                      ->with('level2',$ApprovalRejected_level2)
                      ->with('state_name',$state_name)
                      ->with('level2d',$ApprovalRejected_level2d)
                      ->with('level3',$ApprovalRejected_level3)
                      ->with('level4',$ApprovalRejected_level4)
                      ->with('message','Applications Rejected At Approval');

            }elseif($ApprovalRejected_level3!=null && $ApprovalRejected_level4!=null && $ApprovalRejected_level2d==null){

                $ApprovalRejected=DB::table('nhm_employee_details')->where('body_code','=',1)->where('posting_level','=',$ApprovalRejected_level3)->where('posting_place_code','=',$ApprovalRejected_level4)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $ApprovalRejected_count=$ApprovalRejected->count();
                if($ApprovalRejected_level2==1){
                    $state_name='West Bengal';
                }

                $place_name=DB::table('m_health_facility')->where('facilty_code','=',$ApprovalRejected_level4)->pluck('facility_name')->first();
            
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApprovalRejected,
                    'count'=>$ApprovalRejected_count
                   
                    ])->with('level1',$ApprovalRejected_level1)
                      ->with('level2',$ApprovalRejected_level2)
                      ->with('state_name',$state_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$ApprovalRejected_level2d)
                      ->with('level3',$ApprovalRejected_level3)
                      ->with('level4',$ApprovalRejected_level4)
                      ->with('message','Applications Rejected At Approval');

            }
        



        }elseif($ApprovalRejected_level1=='District'){

        
        $urban_body_codes = UrbanBody::where('district_code', '=', $ApprovalRejected_level2)->pluck('urban_body_code as body_code');
        $taluka_codes = Taluka::where('district_code', '=', $ApprovalRejected_level2)->pluck('taluka_code as body_code');
        $body_codes = $urban_body_codes->merge($taluka_codes);
        $body_codes=$body_codes->merge($ApprovalRejected_level2);

        //dd($urban_body_codes);

         if( $ApprovalRejected_level2!=null && $ApprovalRejected_level3==null &&  $ApprovalRejected_level4==null && $ApprovalRejected_level2d==null) {
             
                $ApprovalRejected=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $ApprovalRejected_count=$ApprovalRejected->count();
               // dd($applications_submitted_count);
            
              $district_name=District::where('district_code','=',$ApprovalRejected_level2)->pluck('district_name')->first();
//dd($district_name);
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApprovalRejected,
                    'count'=>$ApprovalRejected_count
                   
                    ])->with('level1',$ApprovalRejected_level1)
                      ->with('level2',$ApprovalRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$ApprovalRejected_level2d)
                      ->with('level3',$ApprovalRejected_level3)
                      ->with('level4',$ApprovalRejected_level4)
                      ->with('message','Applications Rejected At Approval');

            

            }elseif($ApprovalRejected_level2!=null && $ApprovalRejected_level3!=null && $ApprovalRejected_level4==null && $ApprovalRejected_level2d==null){

                $ApprovalRejected=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->where('posting_level','=',$ApprovalRejected_level3)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $ApprovalRejected_count=$ApprovalRejected->count();
               // dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$ApprovalRejected_level2)->pluck('district_name')->first();
                


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApprovalRejected,
                    'count'=>$ApprovalRejected_count
                    
                    ])->with('level1',$ApprovalRejected_level1)
                      ->with('level2',$ApprovalRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('level2d',$ApprovalRejected_level2d)
                      ->with('level3',$ApprovalRejected_level3)
                      ->with('level4',$ApprovalRejected_level4)
                      ->with('message','Applications Rejected At Approval');

            }elseif($ApprovalRejected_level2!=null && $ApprovalRejected_level3!=null && $ApprovalRejected_level4!=null && $ApprovalRejected_level2d==null){

                $ApprovalRejected=DB::table('nhm_employee_details')->
                where(function($query) use($body_codes){
                foreach($body_codes as $body_code) {
                     $query->orWhere('body_code', '=', $body_code);
                }
                })->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->where('posting_level','=',$ApprovalRejected_level3)->where('posting_place_code','=',$ApprovalRejected_level4)->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
               
                $ApprovalRejected_count=$ApprovalRejected->count();
               
                //dd($applications_submitted_count);
                
                if($ApprovalRejected_level3=='ACMOH Office')
                {
                    $district_name = District::where('district_code','=',$ApprovalRejected_level2)->pluck('district_name')->first();
                    //dd($district_name);
                    $place_name=SubDistrict::where('sub_district_code','=',$ApprovalRejected_level4)->pluck('sub_district_name')->first();
                
                }elseif($ApprovalRejected_level3=='ULB'){
                     $district_name = District::where('district_code','=',$ApprovalRejected_level2)->pluck('district_name')->first();
                     $place_name=UrbanBody::where('urban_body_code','=',$ApprovalRejected_level4)->pluck('urban_body_name')->first();
                }else{
                    $district_name = District::where('district_code','=',$ApprovalRejected_level2)->pluck('district_name')->first();
                    $place_name=DB::table('m_health_facility')->where('facilty_code','=',$ApprovalRejected_level4)->pluck('facility_name')->first();
                   // dd($district_name);
                }
                
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApprovalRejected,
                    'count'=>$ApprovalRejected_count,
                  
                    ])->with('level1',$ApprovalRejected_level1)
                      ->with('level2',$ApprovalRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$ApprovalRejected_level2d)
                      ->with('level3',$ApprovalRejected_level3)
                      ->with('level4',$ApprovalRejected_level4)
                      ->with('message','Applications Rejected At Approval');

            }



        }elseif($ApprovalRejected_level1=='ULB'){
// dd($PendingVerification_level2d,$PendingVerification_level2,$PendingVerification_level3,$PendingVerification_level4);
            
            if($ApprovalRejected_level2d!=null && $ApprovalRejected_level2!=null && $ApprovalRejected_level3==null &&   
                $ApprovalRejected_level4==null ) {
//dd("HI");
                $ApprovalRejected=DB::table('nhm_employee_details')->where('body_code','=',$ApprovalRejected_level2)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $ApprovalRejected_count=$ApprovalRejected->count();
                //dd($applications_submitted_count);
                
                 $district_name=District::where('district_code','=',$ApprovalRejected_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$ApprovalRejected_level2)->pluck('urban_body_name')->first();
 //dd($PendingVerification_count);

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApprovalRejected,
                    'count'=>$ApprovalRejected_count
                    
                    ])->with('level1',$ApprovalRejected_level1)
                      ->with('level2',$ApprovalRejected_level2)
                      ->with('level2d',$ApprovalRejected_level2d)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level3',$ApprovalRejected_level3)
                      ->with('level4',$ApprovalRejected_level4)
                      ->with('message','Applications Rejected At Approval');

            }elseif($ApprovalRejected_level2d!=null && $ApprovalRejected_level2!=null && 
                $ApprovalRejected_level3!=null && $ApprovalRejected_level4==null){

                $ApprovalRejected=DB::table('nhm_employee_details')->where('body_code','=',$ApprovalRejected_level2)->where('posting_level','=',$ApprovalRejected_level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $ApprovalRejected_count=$ApprovalRejected->count();
                
                $district_name=District::where('district_code','=',$ApprovalRejected_level2d)->pluck('district_name')->first();
                $urban_body_name=UrbanBody::where('urban_body_code','=',$ApprovalRejected_level2)->pluck('urban_body_name')->first();





                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApprovalRejected,
                    'count'=>$ApprovalRejected_count
                    
                    ])->with('level1',$ApprovalRejected_level1)
                      ->with('level2',$ApprovalRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('level2d',$ApprovalRejected_level2d)
                      ->with('level3',$ApprovalRejected_level3)
                      ->with('level4',$ApprovalRejected_level4)
                      ->with('message','Applications Rejected At Approval');

            }elseif($ApprovalRejected_level2d!=null && $ApprovalRejected_level2!=null && $ApprovalRejected_level3!=null && $ApprovalRejected_level4!=null ){

                $ApprovalRejected=DB::table('nhm_employee_details')->where('body_code','=',$ApprovalRejected_level2)->where('posting_level','=',$ApprovalRejected_level3)->where('posting_place_code','=',$ApprovalRejected_level4)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $ApprovalRejected_count=$ApprovalRejected->count();
            
                 $district_name=District::where('district_code','=',$ApprovalRejected_level2d)->pluck('district_name')->first();
                 $urban_body_name=UrbanBody::where('urban_body_code','=',$ApprovalRejected_level2)->pluck('urban_body_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$ApprovalRejected_level4)->pluck('facility_name')->first();
               
                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApprovalRejected,
                    'count'=>$ApprovalRejected_count
                   
                    ])->with('level1',$ApprovalRejected_level1)
                      ->with('level2',$ApprovalRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('urban_body_name',$urban_body_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$ApprovalRejected_level2d)
                      ->with('level3',$ApprovalRejected_level3)
                      ->with('level4',$ApprovalRejected_level4)
                      ->with('message','Applications Rejected At Approval');

            }
        




        }elseif($ApprovalRejected_level1=='Block'){

            if($ApprovalRejected_level2d!=null && $ApprovalRejected_level2!=null && $ApprovalRejected_level3==null &&   
                $ApprovalRejected_level4==null ) {

                $ApprovalRejected=DB::table('nhm_employee_details')->where('body_code','=',$ApprovalRejected_level2)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);//get();
                
                $ApprovalRejected_count=$ApprovalRejected->count();
                //dd($applications_submitted_count);
                $district_name=District::where('district_code','=',$ApprovalRejected_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$ApprovalRejected_level2)->pluck('taluka_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApprovalRejected,
                    'count'=>$ApprovalRejected_count
                    
                    ])->with('level1',$ApprovalRejected_level1)
                      ->with('level2',$ApprovalRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$ApprovalRejected_level2d)
                      ->with('level3',$ApprovalRejected_level3)
                      ->with('level4',$ApprovalRejected_level4)
                      ->with('message','Applications Rejected At Approval');

            }elseif($ApprovalRejected_level2d!=null && $ApprovalRejected_level2!=null && 
                $ApprovalRejected_level3!=null && $ApprovalRejected_level4==null){

                $ApprovalRejected=DB::table('nhm_employee_details')->where('body_code','=',$ApprovalRejected_level2)->where('posting_level','=',$ApprovalRejected_level3)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $ApprovalRejected_count=$ApprovalRejected->count();
                
                $district_name=District::where('district_code','=',$ApprovalRejected_level2d)->pluck('district_name')->first();
                $taluka_name=Taluka::where('taluka_code','=',$ApprovalRejected_level2)->pluck('taluka_name')->first();
                

                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApprovalRejected,
                    'count'=>$ApprovalRejected_count
                    
                    ])->with('level1',$ApprovalRejected_level1)
                      ->with('level2',$ApprovalRejected_level2)
                       ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('level2d',$ApprovalRejected_level2d)
                      ->with('level3',$ApprovalRejected_level3)
                      ->with('level4',$ApprovalRejected_level4)
                      ->with('message','Applications Rejected At Approval');

            }elseif($ApprovalRejected_level2d!=null && $ApprovalRejected_level2!=null && $ApprovalRejected_level3!=null && $ApprovalRejected_level4!=null ){

                $ApprovalRejected=DB::table('nhm_employee_details')->where('body_code','=',$ApprovalRejected_level2)->where('posting_level','=',$ApprovalRejected_level3)->where('posting_place_code','=',$ApprovalRejected_level4)->where('verification_status','=','Verified')->where('approval_status','=','Disapproved')
                    ->select('id','title','first_name','middle_name','last_name','dob','gender','email','mobile_number_1','verification_status','approval_status')->orderby('nhm_employee_details.id')->get();//paginate(40);
                
                $ApprovalRejected_count=$ApprovalRejected->count();
                

                $district_name=District::where('district_code','=',$ApprovalRejected_level2d)->pluck('district_name')->first();
                 $taluka_name=Taluka::where('taluka_code','=',$ApprovalRejected_level2)->pluck('taluka_name')->first();

                 $place_name=DB::table('m_health_facility')->where('facilty_code','=',$ApprovalRejected_level4)->pluck('facility_name')->first();


                return view('employee-report-drilldown.linelisting',
                    ['datas' => $ApprovalRejected,
                    'count'=>$ApprovalRejected_count
                   
                    ])->with('level1',$ApprovalRejected_level1)
                      ->with('level2',$ApprovalRejected_level2)
                      ->with('district_name',$district_name)
                      ->with('taluka_name',$taluka_name)
                      ->with('place_name',$place_name)
                      ->with('level2d',$ApprovalRejected_level2d)
                      ->with('level3',$ApprovalRejected_level3)
                      ->with('level4',$ApprovalRejected_level4)
                      ->with('message','Applications Rejected At Approval');

            }
        }

    }

public function allPosts(Request $request)
    {//DB::enableQueryLog();
        //dd($request);
        $columns = array( 
                            0 =>'id', 
                            1 =>'name',
                            2=> 'dob',
                            3=>'gender',
                            4=>'mobile_number_1',
                            5=>'email',
                            6=>'verification_status',
                            7=>'approval_status'
                        );
  
        $totalData = nhm_employee_details::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
                //$order = (isset($_GET[$request->input('order.0.column')])) ? $request->input('order.0.column') : 0;
        if(empty($request->input('search.value')))
        {   //dd($limit,$order,$dir,$start);
             //dd($request->input('order.0.column'));
            
            $posts =nhm_employee_details::selectRaw("CONCAT(title,' ',first_name,' ',middle_name,' ',last_name) as name,id as id,dob as dob,gender as gender,mobile_number_1 as mobile_number_1,verification_status as verification_status,approval_status as approval_status,email as email")->offset($start)
           ->limit($limit)->orderBy($order,$dir)->get();
            
            //dd(DB::getQueryLog()); 

            // $posts = nhm_employee_details::offset($start)
            //              ->limit($limit)
            //              ->orderBy($order,$dir)
            //              ->get();
                  
        }
        else {
            $search = $request->input('search.value'); 

            
             $posts =nhm_employee_details::where('id','LIKE',"%{$search}%")
                            ->orWhere('title', 'LIKE',"%{$search}%")
                            ->selectRaw("CONCAT(title,' ',first_name,' ',middle_name,' ',last_name) as name,id as id,dob as dob,gender as gender,mobile_number_1 as mobile_number_1,verification_status as verification_status,approval_status as approval_status,email as email")->offset($start)
                            ->limit($limit)->orderBy($order,$dir)->get();


            // $posts =  nhm_employee_details::where('id','LIKE',"%{$search}%")
            //                 ->orWhere('title', 'LIKE',"%{$search}%")
            //                 ->offset($start)
            //                 ->limit($limit)
            //                 ->orderBy($order,$dir)
            //                 ->get();

            $totalFiltered = nhm_employee_details::where('id','LIKE',"%{$search}%")
                             ->orWhere('title', 'LIKE',"%{$search}%")
                             ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                $show =  route('nhmemployee.showSingleEmployeeReport',$post->id);
                //$edit =  route('posts.edit',$post->id);

                $nestedData['id'] = $post->id;
                 $nestedData['name'] =  $post->name;
                // $nestedData['name'] =  $post->title.' '.$post->first_name.' '.$post->middle_name.' '.$post->last_name;
                $nestedData['email'] = $post->email;
                $nestedData['mobile_number_1'] = $post->mobile_number_1;
                $nestedData['verification_status'] = $post->verification_status;
                $nestedData['approval_status'] = $post->approval_status;
                $nestedData['dob'] = $post->dob;
                $nestedData['gender'] = $post->gender;
                $nestedData['button'] = "<a href='{$show}' class='btn btn-info btn-margin' title='View' >View</a>";
                //'<button type="submit" class="btn btn-info btn-margin" >View</button';
                //$post->title;
                //$nestedData['body'] = substr(strip_tags($post->body),0,50)."...";
                //$nestedData['created_at'] = date('j M Y h:i a',strtotime($post->created_at));
               // $nestedData['options'] = "&emsp;<a href='{$show}' title='SHOW' ><span class='glyphicon glyphicon-list'></span></a>
                                          //&emsp;<a href='{$edit}' title='EDIT' ><span class='glyphicon glyphicon-edit'></span></a>";
                $data[] = $nestedData;

            }
            //dd($data);
        }
          
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data); 
        
    }


    public function allPostsapproved(Request $request)
    {
        //dd($request);
        $columns = array( 
                            0 =>'id', 
                            1 =>'name',
                            2=> 'dob',
                            3=>'gender',
                            4=>'mobile_number_1',
                            5=>'email',
                            6=>'verification_status',
                            7=>'approval_status'

                            
                        );
  
        $totalData = nhm_employee_details::where('verification_status','=','Verified')
                                        ->where('approval_status','=','Approved')
                                        ->count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
            
        if(empty($request->input('search.value')))
        {            
             $posts =nhm_employee_details::where('verification_status','=','Verified')
                                        ->where('approval_status','=','Approved')
                                        ->selectRaw("CONCAT(title,' ',first_name,' ',middle_name,' ',last_name) as name,id as id,dob as dob,gender as gender,mobile_number_1 as mobile_number_1,verification_status as verification_status,approval_status as approval_status,email as email")
                                        ->offset($start)
                                        ->limit($limit)
                                        ->orderBy($order,$dir)
                                        ->get();

            // $posts = nhm_employee_details::where('verification_status','=','Verified')
            //              ->where('approval_status','=','Approved')->offset($start)
            //              ->limit($limit)
            //              ->orderBy($order,$dir)
            //              ->get();
        }
        else {
            $search = $request->input('search.value'); 

           $posts =nhm_employee_details::where('verification_status','=','Verified')
                            ->where('approval_status','=','Approved')
                            ->where('id','LIKE',"%{$search}%")
                            ->orWhere('title', 'LIKE',"%{$search}%")
                            ->selectRaw("CONCAT(title,' ',first_name,' ',middle_name,' ',last_name) as name,id as id,dob as dob,gender as gender,mobile_number_1 as mobile_number_1,verification_status as verification_status,approval_status as approval_status,email as email")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            // $posts =  nhm_employee_details::where('verification_status','=','Verified')
            //                 ->where('approval_status','=','Approved')
            //                 ->where('id','LIKE',"%{$search}%")
            //                 ->orWhere('title', 'LIKE',"%{$search}%")
            //                 ->offset($start)
            //                 ->limit($limit)
            //                 ->orderBy($order,$dir)
            //                 ->get();

            $totalFiltered = nhm_employee_details::where('verification_status','=','Verified')
                             ->where('approval_status','=','Approved')
                             ->where('id','LIKE',"%{$search}%")
                             ->orWhere('title', 'LIKE',"%{$search}%")
                             ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                $show =  route('nhmemployee.showSingleEmployeeReport',$post->id);
                //$edit =  route('posts.edit',$post->id);

                $nestedData['id'] = $post->id;
                $nestedData['name'] =  $post->name;
                //$nestedData['name'] =  $post->title.' '.$post->first_name.' '.$post->middle_name.' '.$post->last_name;
                $nestedData['email'] = $post->email;
                $nestedData['mobile_number_1'] = $post->mobile_number_1;
                $nestedData['verification_status'] = $post->verification_status;
                $nestedData['approval_status'] = $post->approval_status;
                $nestedData['dob'] = $post->dob;
                $nestedData['gender'] = $post->gender;
                $nestedData['button'] = "<a href='{$show}' class='btn btn-info btn-margin' title='View' >View</a>";
                //'<button type="submit" class="btn btn-info btn-margin" >View</button';
                //$post->title;
                //$nestedData['body'] = substr(strip_tags($post->body),0,50)."...";
                //$nestedData['created_at'] = date('j M Y h:i a',strtotime($post->created_at));
               // $nestedData['options'] = "&emsp;<a href='{$show}' title='SHOW' ><span class='glyphicon glyphicon-list'></span></a>
                                          //&emsp;<a href='{$edit}' title='EDIT' ><span class='glyphicon glyphicon-edit'></span></a>";
                $data[] = $nestedData;

            }
            //dd($data);
        }
          
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data); 
        
    }



public function allPostsverified(Request $request)
    {
        //dd($request);
        $columns = array( 
                            0 =>'id', 
                            1 =>'name',
                            2=> 'dob',
                            3=>'gender',
                            4=>'mobile_number_1',
                            5=>'email',
                            6=>'verification_status',
                            7=>'approval_status'
                           
                            
                        );
  
        $totalData = nhm_employee_details::where('verification_status','=','Verified')
                                        //->where('approval_status','=','Not Approved')
                                        ->count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
            
        if(empty($request->input('search.value')))
        {            
             $posts =nhm_employee_details::where('verification_status','=','Verified')
                                         //->where('approval_status','=','Not Approved')
                                        ->selectRaw("CONCAT(title,' ',first_name,' ',middle_name,' ',last_name) as name,id as id,dob as dob,gender as gender,mobile_number_1 as mobile_number_1,verification_status as verification_status,approval_status as approval_status,email as email")
                                        ->offset($start)
                                        ->limit($limit)
                                        ->orderBy($order,$dir)
                                         ->get();

                                        // dd($posts);  

            // $posts = nhm_employee_details::where('verification_status','=','Verified')
            //              ->where('approval_status','=','Approved')->offset($start)
            //              ->limit($limit)
            //              ->orderBy($order,$dir)
            //              ->get();
        }
        else {
            $search = $request->input('search.value'); 
            //dd($search);
           $posts =nhm_employee_details::where('verification_status','=','Verified')
                            //->where('approval_status','=','Not Approved')
                            ->where('id','LIKE',"%{{$search}}%")
                            ->orWhere('mobile_number_1', 'LIKE',"%{{$search}}%")
                            ->selectRaw("CONCAT(title,' ',first_name,' ',middle_name,' ',last_name) as name,id as id,dob as dob,gender as gender,mobile_number_1 as mobile_number_1,verification_status as verification_status,approval_status as approval_status,email as email")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            // $posts =  nhm_employee_details::where('verification_status','=','Verified')
            //                 ->where('approval_status','=','Approved')
            //                 ->where('id','LIKE',"%{$search}%")
            //                 ->orWhere('title', 'LIKE',"%{$search}%")
            //                 ->offset($start)
            //                 ->limit($limit)
            //                 ->orderBy($order,$dir)
            //                 ->get();

            $totalFiltered = nhm_employee_details::where('verification_status','=','Verified')
                             //->where('approval_status','=','Not Approved')
                             ->where('id','LIKE',"%{$search}%")
                             ->orWhere('title', 'LIKE',"%{$search}%")
                             ->count();
        }

        $data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                $show =  route('nhmemployee.showSingleEmployeeReport',$post->id);
                //$edit =  route('posts.edit',$post->id);

                $nestedData['id'] = $post->id;
                $nestedData['name'] =  $post->name;
                //$nestedData['name'] =  $post->title.' '.$post->first_name.' '.$post->middle_name.' '.$post->last_name;
                $nestedData['email'] = $post->email;
                $nestedData['mobile_number_1'] = $post->mobile_number_1;
                $nestedData['verification_status'] = $post->verification_status;
                $nestedData['approval_status'] = $post->approval_status;
                $nestedData['dob'] = $post->dob;
                $nestedData['gender'] = $post->gender;
                $nestedData['button'] = "<a href='{$show}' class='btn btn-info btn-margin' title='View' >View</a>";
                //'<button type="submit" class="btn btn-info btn-margin" >View</button';
                //$post->title;
                //$nestedData['body'] = substr(strip_tags($post->body),0,50)."...";
                //$nestedData['created_at'] = date('j M Y h:i a',strtotime($post->created_at));
               // $nestedData['options'] = "&emsp;<a href='{$show}' title='SHOW' ><span class='glyphicon glyphicon-list'></span></a>
                                          //&emsp;<a href='{$edit}' title='EDIT' ><span class='glyphicon glyphicon-edit'></span></a>";
                $data[] = $nestedData;

            }
            //dd($data);
        }
          
        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data); 
        
    }











    
}






    
