<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Http\Controllers\Redirect;
use App\programmeHeadMaster;
use App\majorProgammeHeadMaster;
use App\nhm_employee_details;
use App\designationMaster;
use App\nhm_service_category;
use App\NHMEmployee;
use App\Configduty;
use App\District;
use App\nhm_posting_level;
use App\nhm_level_place;
use App\nhm_health_facility;
use App\UrbanBody;
use App\SubDistrict;

use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuthChecker;

class NHMEmployeeController extends Controller
{
    
    public function __construct()
    {
          //$this->middleware('auth');
          //$this->middleware('Admin')->only(['index','store','loadprogrammeHead','loadMajorprogrammeHead','loadDesignationList','admingetreports']);
          //$this->middleware('Verifier')->only(['verifydata','showSingleEmployee','verify']);
        
         //$this->middleware('Admin')->only(['index','store','loadprogrammeHead','loadMajorprogrammeHead','loadDesignationList']);
        // $this->middleware('Verifier')->only(['verifydata','showSingleEmployee','verify']);
        
        //$this->middleware('Admin');

       // $this->middleware('Verifier'); 
    }



        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   


        $user_id = AuthChecker::getUserId();
        //$districts = District::all();
        $districts = District::where('is_revenue_district','=','1')->get(['district_code','district_name']);
        $major_programme_heads = majorProgammeHeadMaster::all();
        $service_categorys = nhm_service_category::all();
        $duty = Configduty::where('user_id','=',$user_id)->first();

        $is_active = $duty->is_active;
       //dd($is_active);
        if($is_active==1){
        
        $mappingLevel=$duty->mapping_level;
        if($mappingLevel=="State HQ"){
           $level="State";
           // $facility_type=["MCH"];
           // $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->get(['facility_name','facilty_code']);

        }else if($mappingLevel=="District HQ"){
           $district_code = $duty->district_code;
          
           $level="District";
          // $facility_type=["DH","MCH","SDH","SGH","CH","PHC"];
          
           
          // $postingPlaces = nhm_health_facility::where('district_code', '=', $district_code)->whereIn('facility_type', $facility_type)->get(['facility_name','facilty_code']);

        }else{
            $is_urban=$duty->is_urban;
            if($is_urban==1){
                $district_code = $duty->district_code;
                $urban_body_code = $duty->urban_body_code;
                $level="ULB";
               // $facility_type=["UPHC"];                
               // $postingPlaces = nhm_health_facility::where('district_code', '=', $district_code)->where('taluka_code', '=', $urban_body_code)->whereIn('facility_type', $facility_type)->get(['facility_name','facilty_code']);

 
            }else{
                $level="Block";
                //$facility_type=["SC","PHC"];
                $district_code = $duty->district_code;
                $taluka_code = $duty->taluka_code;
                // $postingPlaces = nhm_health_facility::where('district_code', '=', $district_code)->where('taluka_code', '=', $taluka_code)->whereIn('facility_type', $facility_type)->get(['facility_name','facilty_code']);
            }
            
        }

        $nhm_posting_levels = nhm_posting_level::where('level','=',$level)->get(['name']);;
        //dd($nhm_posting_levels);
        // $programmeHeads = programmeHeadMaster::where('major_programme_head_id', '=', 2)->get(['id', 'name']);
        // return view('employee_details',['major_programme_heads' => $major_programme_heads,'programmeHeads'=>$programmeHeads]);
        return view('employee_details',['major_programme_heads' => $major_programme_heads,
            'service_categorys'=>$service_categorys,'districts' => $districts,
            'nhm_posting_levels' => $nhm_posting_levels]);
    
    }if($is_active==0){

        return redirect("/")->with('success', 'User Disabled');
    }
    
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
        
        $this->validateInput($request);
        $nhm_employee_details=new nhm_employee_details();
        $nhm_employee_details->title=$request->title;
        $nhm_employee_details->first_name=$request->first_name;
        $nhm_employee_details->middle_name=$request->middle_name;
        $nhm_employee_details->last_name=$request->last_name;
        $nhm_employee_details->guardian_relation=$request->guardian_relation;
        $nhm_employee_details->guardian_name=$request->guardian_name;
        $nhm_employee_details->dob=$request->dob;
        $nhm_employee_details->gender=$request->gender;
        $nhm_employee_details->caste_category=$request->caste_category;
        $nhm_employee_details->pwd=$request->pwd;
        $nhm_employee_details->marital_status=$request->marital_status;
        $nhm_employee_details->mobile_number_1=$request->mobile_number_1;
        $nhm_employee_details->mobile_number_2=$request->mobile_number_2;
        $nhm_employee_details->email=$request->email;
        $nhm_employee_details->identification_mark=$request->identification_mark;
        $nhm_employee_details->blood_group=$request->blood_group;
        $nhm_employee_details->person_name_emergency=$request->person_name_emergency;
        $nhm_employee_details->person_emergency_mobile=$request->person_emergency_mobile;
        $nhm_employee_details->present_address_line1=$request->present_address_line1;
        $nhm_employee_details->present_address_district=$request->present_address_district;
        $nhm_employee_details->present_address_police_station=$request->present_address_police_station;
        $nhm_employee_details->present_address_pincode=$request->present_address_pincode;

        $nhm_employee_details->other_district_present_address=$request->other_district_present_address;

        $nhm_employee_details->permanent_address_line1=$request->permanent_address_line1;
        $nhm_employee_details->permanet_address_district=$request->permanent_address_district;
        $nhm_employee_details->permanent_address_poilce_station=$request->permanent_address_police_station;
        $nhm_employee_details->permanent_address_pincode=$request->permanent_address_pincode;

        $nhm_employee_details->other_district_permanent_address=$request->other_district_permanent_address;

        $nhm_employee_details->highest_education=$request->highest_education;
        $nhm_employee_details->technical_qualification=$request->technical_qualification;
        $nhm_employee_details->professional_qualification=$request->professional_qualification;
        $nhm_employee_details->other_professional_qualification=$request->other_professional_qualification;
        $nhm_employee_details->registration=$request->registration;
        $nhm_employee_details->pan=$request->pan;
        $nhm_employee_details->bank_account_number=$request->bank_account_number;
        $nhm_employee_details->name_of_bank=$request->name_of_bank;
        $nhm_employee_details->bank_branch=$request->bank_branch;
        $nhm_employee_details->bank_ifsc_code=$request->bank_ifsc_code;

        $nhm_employee_details->uin_number=$request->uin_number;

        $nhm_employee_details->is_uan_present=$request->is_uan_present;

        $nhm_employee_details->exp_engaged_under_nhm=$request->engaged_or_not_nhm;
        $nhm_employee_details->exp_designation_nhm=$request->e_designation_nhm;
        $nhm_employee_details->exp_duration_from_nhm=$request->e_duration_from_nhm;
        $nhm_employee_details->exp_duration_to_nhm=$request->e_duration_to_Nhm;
        $nhm_employee_details->last_monthly_remuneration_nhm=$request->e_remuneration_nhm;
        $nhm_employee_details->e_remarks_nhm=$request->e_remarks_nhm;
        $nhm_employee_details->experience_year_month_nhm=$request->experience_year_month_nhm;
        
        $nhm_employee_details->exp_engaged_under_hfw=$request->engaged_or_not_hfw;
        $nhm_employee_details->exp_designation_hfw=$request->e_designation_hfw;
        $nhm_employee_details->exp_duration_from_hfw=$request->e_duration_from_hfw;
        $nhm_employee_details->exp_duration_to_hfw=$request->e_duration_to_hfw;
        $nhm_employee_details->e_remarks_hfw=$request->e_remarks_hfw;
        $nhm_employee_details->experience_year_month_hfw=$request->experience_year_month_hfw;
       
        $nhm_employee_details->exp_last_monthly_remuneration_hfw=$request->e_remuneration_hfw;
        $nhm_employee_details->advertisement_number=$request->advertisement_number;
        $nhm_employee_details->appointing_authority=$request->appointing_authority;
        $nhm_employee_details->contractual_employement_under=$request->contractual_employment_under;
        $nhm_employee_details->service_category=$request->service_category;
        $nhm_employee_details->nhm_major_programme_head=$request->contractual_under_nhm;
        $nhm_employee_details->nhm_programme_head=$request->programme_head;
        $nhm_employee_details->designation_list=$request->designation_list;
        $nhm_employee_details->doj_present_designation=$request->date_of_joining;
        $nhm_employee_details->consolidated_remuneration_doj=$request->con_rem_time_joining;
        //$nhm_employee_details->con_monthly_salary_joining=$request->con_monthly_salary_joining;
        $nhm_employee_details->doj_present_posting=$request->date_of_joining_in_posting;
        $nhm_employee_details->monthly_remuneration=$request->monthly_rem;
        $nhm_employee_details->posting_level=$request->posting_level;

        // $keyword=preg_split("~~", $request->posting_place[0]);
        // $nhm_employee_details->posting_place=$keyword[1];
        // $nhm_employee_details->posting_place_code=$keyword[0];

       /* list($posting_place_code,$posting_place)=preg_split("~:~", $request->posting_place);
        // dd($posting_place_code,$posting_place);
        $nhm_employee_details->posting_place_code=$posting_place_code;
        $nhm_employee_details->posting_place=$posting_place;*/

        $posting_level_data=$request->posting_level;
        // dd($posting_level_data);
        if($posting_level_data =="CPMU" || $posting_level_data =="DPMU" || $posting_level_data =="BPMU" ||
            $posting_level_data =="SPMU"|| $posting_level_data=="State Drug Store" || $posting_level_data=="State Institute of Health and Family Welfare"){
             $nhm_employee_details->posting_place_code=Null;
             $nhm_employee_details->posting_place=$request->posting_level;
       
        }else{
             list($posting_place_code,$posting_place)=preg_split("~:~", $request->posting_place);
             $nhm_employee_details->posting_place_code=$posting_place_code;
             $nhm_employee_details->posting_place=$posting_place;
        }

        //list($posting_place_code,$posting_place) = preg_split("[:]",$request->posting_place,1);

        //$data = preg_split('~:~', $request->posting_place,2, PREG_SPLIT_NO_EMPTY);
       
        //$chars = preg_split('~:~', $request->posting_place, -1, PREG_SPLIT_OFFSET_CAPTURE);
       



        // dd($posting_place_code,$posting_place);
      
       // $nhm_employee_details->posting_place_code=preg_split("~~", $request->posting_place[0]);
       // $nhm_employee_details->posting_place=preg_split("~~", $request->posting_place[1]);
        // $nhm_employee_details->casual_leave_availed=$request->casual_leave_availed;
        // $nhm_employee_details->earned_leave_availed=$request->earned_leave_availed;
        $nhm_employee_details->verification_status="Not Verified";
        $nhm_employee_details->approval_status="Not Approved";

        ///$nhm_employee_details->created_at=date("Y-M-D H:I:S");
        //$nhm_employee_details->updated_at=date("Y-M-D H:I:S");
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)->first();
        //dd($duty);
        $mappingLevel=$duty->mapping_level;
        if($mappingLevel=="State HQ"){
            $nhm_employee_details->body_code = 1;
        }else if($mappingLevel=="District HQ"){
            $nhm_employee_details->body_code = $duty->district_code;
        }else{
            $nhm_employee_details->is_urban = $duty->is_urban;
            if($duty->is_urban==1){
                $nhm_employee_details->body_code = $duty->urban_body_code;
            }else{
                $nhm_employee_details->body_code = $duty->taluka_code;
        }
        }

        
        

        $is_saved=$nhm_employee_details->save();
        //print_r($is_saved);
        $id=$nhm_employee_details->id;
        //dd($nhm_employee_details->id);
        // dd($id);
       // echo($id);
        if($is_saved){
            
            return redirect("nhmemployee")->with('success', 'Application Submitted Successfully')->with('id', 
                $id);
        }


        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //$id=> $request['id'];
        // $id=$request->input('id');
        // $single_employee_detail = nhm_employee_details::find($id);
        // return view('show_single_nhm_employee_details', ['single_employee_detail' => $single_employee_detail]);
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
/*******************************SD*************************/
 public function loadprogrammeHead($major_programme_head_id,$service_category) {
    
        $programmeHeads = programmeHeadMaster::where('major_programme_head_id', '=', $major_programme_head_id)->where('service_category_id', '=', $service_category)->get(['id', 'name']);

        //print_r($programmeHeads);
       // $programmeHeads=programmeHeadMaster::all();
      // dump( $programmeHeads);
        //Log::info('Showing user profile for user: '.$programmeHeads);

       return response()->json($programmeHeads);
        //return view('testview',['programmeHeads' => $programmeHeads]);
    }

 public function loadMajorprogrammeHead($major_programme_head_id) {
    
        $major_programme_heads = majorProgammeHeadMaster::all();

        //print_r($programmeHeads);
       // $programmeHeads=programmeHeadMaster::all();
      // dump( $programmeHeads);
        //Log::info('Showing user profile for user: '.$programmeHeads);

       return response()->json($major_programme_heads);
        //return view('testview',['programmeHeads' => $programmeHeads]);
    }



 public function loadDesignationList($programme_head_id,$service_category,$major_programme_head_id) {

        //$id = Auth::guard('api')->id;$id = Auth::guard('api')->user()->id;
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)->first();

        $mappingLevel=$duty->mapping_level;
        
        if($mappingLevel=="State HQ"){
           $level="State";
           
        }else if($mappingLevel=="District HQ"){
           
           $level="District";
           

        }else{
            $is_urban=$duty->is_urban;
            if($is_urban==1){
                               
                $level="ULB";              

            }else{
                $level="Block";
            }
            
        }
       
    $designationLists = designationMaster::where('programme_head_id', '=', $programme_head_id)->
    where('service_category_id', '=', $service_category)->where('major_programme_head_id', '=', 
        $major_programme_head_id)->where('level', '=', 
        $level)->get(['id', 'name']);
    

        //print_r($programmeHeads);
       // $programmeHeads=programmeHeadMaster::all();
      // dump( $programmeHeads);
        //Log::info('Showing user profile for user: '.$programmeHeads);

       return response()->json($designationLists);
        //return view('testview',['programmeHeads' => $programmeHeads]);
    }


    

public function loadPostingPlace($posting_level) {
    
     $user_id = AuthChecker::getUserId();
     $duty = Configduty::where('user_id','=',$user_id)->first();
    
    
      $mappingLevel=$duty->mapping_level;
        if($mappingLevel=="State HQ"){
             if($posting_level=="MCH"){ 
                
                $facility_type=["MCH"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',342)->get(['facility_name as name','facilty_code as code']);
            
            }else if($posting_level=="Other Hospital"){ 
                
                $facility_type=["Others"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',342)->get(['facility_name as name','facilty_code as code']);
                
            }elseif($posting_level=="SPMU"){
               
                $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
            
            }elseif($posting_level=="State Drug Store"){
               
                $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
            
            }elseif($posting_level=="State Institute of Health and Family Welfare"){
               
                $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
            
            }elseif($posting_level=="SSH"){
               
                $facility_type=["SSH"];
                $postingPlaces=nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',342)->get(['facility_name as name','facilty_code as code']);

            }
           

        }
        else if($mappingLevel=="District HQ"){
            
            $district_code = $duty->district_code;
            
            if($posting_level=="ULB"){
                    $postingPlaces =UrbanBody::where('district_code','=',$district_code)->get(['urban_body_code as code','urban_body_name as name']);
                    
                    
            }else if($posting_level=="UPHC"){
               
                $facility_type=["UPHC"];
                $postingPlaces=nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',$district_code)->get(['facility_name as name','facilty_code as code']);
                
            }else if($posting_level=="ACMOH Office"){

                $postingPlaces=SubDistrict::where('district_code','=',$district_code)->get(['sub_district_code as code','sub_district_name as name']);
            
            }else if($posting_level=="DH"){

                 $facility_type=["DH"];
                 $postingPlaces=nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',$district_code)->get(['facility_name as name','facilty_code as code']);
            
            }else if($posting_level=="SDH"){

                 $facility_type=["SDH"];
                 $postingPlaces=nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',$district_code)->get(['facility_name as name','facilty_code as code']);

            }else if($posting_level=="Other Hospital"){

                $facility_type=["Others"];
                $postingPlaces=nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',$district_code)->get(['facility_name as name','facilty_code as code']);
            
            }else if($posting_level=="SGH"){

                $facility_type=["SGH"];
                $postingPlaces=nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',$district_code)->get(['facility_name as name','facilty_code as code']);
            
            }else if($posting_level=="MCH"){


                $facility_type=["MCH"];
                $postingPlaces=nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',$district_code)->get(['facility_name as name','facilty_code as code']);

            }else if($posting_level=="SSH"){

                $facility_type=["SSH"];
                $postingPlaces=nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',$district_code)->get(['facility_name as name','facilty_code as code']);

            }elseif($posting_level=="CHC"){

                $facility_type=["CH"];
                $postingPlaces=nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',$district_code)->get(['facility_name as name','facilty_code as code']);
            
            }elseif($posting_level=="PHC"){

                $facility_type=["PHC"];
                $postingPlaces=nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code','=',$district_code)->get(['facility_name as name','facilty_code as code']);
           
            }elseif($posting_level=="DPMU"){
                
                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
            
            }elseif($posting_level=="State Drug Store"){

                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
            }

        }
        else{
            // $is_urban = $duty->is_urban;
            if($duty->is_urban==1){

                 $urban_body_code = $duty->urban_body_code;
                
                if($posting_level=="UPHC"){
                                      
                    $facility_type=["UPHC"];
                    $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('taluka_code', '=', $urban_body_code)->get(['facility_name as name','facilty_code as code']);
                    
                }elseif($posting_level=="ULB"){
                     
                    //$facility_type=["ULB"];
                    $postingPlaces =UrbanBody::get(['urban_body_code as code','urban_body_name as name']);
                    
                }elseif($posting_level=="CPMU"){
                
                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
            
                }

            }else{

                $taluka_code = $duty->taluka_code;
                
                if($posting_level=="Subcenter"){

                    $facility_type=["SC"];                   
                    $postingPlaces = nhm_health_facility::where('taluka_code', '=', $taluka_code)->whereIn('facility_type',$facility_type)->get(['facility_name as name','facilty_code as code']);
                   
                }elseif($posting_level=="PHC"){
                    
                    $facility_type=["PHC"];                   
                    $postingPlaces = nhm_health_facility::where('taluka_code', '=', $taluka_code)->whereIn('facility_type',$facility_type)->get(['facility_name as name','facilty_code as code']);
                     
                   
                 }elseif($posting_level=="CHC"){
                    
                    $facility_type=["CH"];                   
                    $postingPlaces = nhm_health_facility::where('taluka_code', '=', $taluka_code)->whereIn('facility_type',$facility_type)->get(['facility_name as name','facilty_code as code']);
                     
                   
                 }elseif($posting_level=="BPMU"){
                    
                    $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
                   
                    }

                }
            }


       

       return response()->json($postingPlaces);
      
    }

// public function loadPostingPlacedynamic($posting_level) {
    
//      $user_id = AuthChecker::getUserId();
//      $duty = Configduty::where('user_id','=',$user_id)->first();
    
    
//       $mappingLevel=$duty->mapping_level;
//         if($mappingLevel=="State HQ"){


//             if($posting_level=="SPMU"){

//                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
            
//             }elseif($posting_level=="State Drug Store"){

//                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 

//             }elseif($posting_level=="State Institute of Health and Family Welfare"){
               
//                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
//             }else{

//                 $postingPlaces = nhm_health_facility::whereIn('facility_type', $posting_level)->where('district_code','=',342)->get(['facility_name as name','facilty_code as code']);

//             }
//         }
//         else if($mappingLevel=="District HQ"){
            
//             $district_code = $duty->district_code;
            
//             if($posting_level=="ULB"){
//                     $postingPlaces =UrbanBody::where('district_code','=',$district_code)->get(['urban_body_code as code','urban_body_name as name']);
                    
                    
//             }else if($posting_level=="ACMOH Office"){

//                 $postingPlaces=SubDistrict::where('district_code','=',$district_code)->get(['sub_district_code as code','sub_district_name as name']);
            
//             }elseif($posting_level=="DPMU"){
                
//                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
            
//             }elseif($posting_level=="State Drug Store"){

//                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
            
//             }else{

//                 $postingPlaces=nhm_health_facility::whereIn('facility_type', $posting_level)->where('district_code','=',$district_code)->get(['facility_name as name','facilty_code as code']);
//             }

//          }  
//         else{
//             // $is_urban = $duty->is_urban;
//             if($duty->is_urban==1){

//                  $urban_body_code = $duty->urban_body_code;
                
//                 if($posting_level=="UPSC"){
                                      
//                     $facility_type=["UPHC"];
//                     $postingPlaces = nhm_health_facility::where('taluka_code', '=', $urban_body_code)->get(['facility_name as name','facilty_code as code']);
                    
//                 }elseif($posting_level=="ULB"){
                     
//                     //$facility_type=["ULB"];
//                     $postingPlaces =UrbanBody::get(['urban_body_code as code','urban_body_name as name']);
                    
//                 }

//             }else{

//                 $taluka_code = $duty->taluka_code;
                
//                 if($posting_level=="BPMU"){

//                     $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
                
//                 }else{

                                     
//                     $postingPlaces = nhm_health_facility::where('taluka_code', '=', $taluka_code)->where('facility_type','=',$posting_level)->get(['facility_name as name','facilty_code as code']);
                   
//                 }



//                 }
//             }


       

//        return response()->json($postingPlaces);
      
//     }



public function verify(){
    //DB::enableQueryLog();
    $user_id = AuthChecker::getUserId();
    $dutys = Configduty::where('user_id','=',$user_id)->get();
//dd($duty);

    $i=0;
    $body_codes=[];
    $is_active_status=[];
    foreach($dutys as $duty){

    if($duty->mapping_level=="State HQ"){
        if($duty->is_active==1){
        $body_codes[$i] = 1;
        $is_active_status[$i]=1;
        }
        else
        {
             $body_codes[$i] = null;
             //return redirect("/")->with('success', 'User Disabled');
        }

    }else if($duty->mapping_level=="District HQ"){
        if($duty->is_active==1){
        $body_codes[$i] = $duty->district_code;
        $is_active_status[$i]=1;
        }
        else
        {
             $body_codes[$i] = null;
        }


    }else{
        //$nhm_employee_details->is_urban = $duty->is_urban;
        if($duty->is_urban==1){
            if($duty->is_active==1){
            $body_codes[$i] = $duty->urban_body_code;
            $is_active_status[$i]=1;
            }
            else
            {
             $body_codes[$i] = null;
            }


        }else{
            if($duty->is_active==1){
            $body_codes[$i] = $duty->taluka_code;
            $is_active_status[$i]=1;
            }
            else{
             $body_codes[$i] = null;
            }
    }   
   } 
   $i++;
  }
  //dd($i);
   //dd($body_codes,$i,$is_active_status);
    $nhm_employee_details=DB::table('nhm_employee_details')->where(function ($query) use ($body_codes){
          foreach($body_codes as $body_code) {
             $query->orWhere('body_code', '=', $body_code);
             
          }
       })->orderBy('nhm_employee_details.id')->paginate(10);
//dd(DB::getQueryLog()); 

      


 
  
  //dd($nhm_employee_details);
    //$nhm_employee_details->body_code = $duty->body_code;
   
    //dd($nhm_employee_details);
     //$nhm_employee_details = nhm_employee_details::where('body_code','=',$body_code)->paginate(10);//->get();
     //return view('verify_nhm_employee_details');
     //print_r($nhm_employee_details);
    
     
     if(empty($is_active_status)){
        return redirect("/")->with('success', 'User Disabled');
     }
     else 
     {
     return view('verify_nhm_employee_details',['nhm_employee_details' => $nhm_employee_details]);
     }

}

 public function showSingleEmployee(Request $request)
    {
        //$id=> $request['id'];

        //echo("isnide show single");
        $id=$request->id;
        //$id=$request->input('id');
        // $id=Input::get('id');
        
        //print_r($id);
        $single_employee_details = nhm_employee_details::find($id);
        //print_r($single_employee_details);

        // if($single_employee_details->verification_status == "Verified"){
        //     $single_employee_details = NHMEmployee::where('application_id','=',$id)->first();
        // }
        return view('show_single_nhm_employee_details', ['single_employee_details' => $single_employee_details]);
    }



    public function verifydata(Request $request)
    {
        //$id=> $request['id'];

        //echo("inside Verify data");
        $id=$request->id;
        $Verified="Verified";
        $Rejected="Rejected";
        //$verifysubmit=$request->Verifysubmit;
        //print_r($verifysubmit);
        //$rejectsubmit=$request->Rejectsubmit;
        // print_r($rejectsubmit);
        $comments=$request->comments;

       if ($_POST['submit'] == 'Verify') {
             $input = [
            'verification_status' => $Verified,'comments' => $comments];
            
            $is_status_updated=nhm_employee_details::where('id', $id)
            ->update($input);            
            //$nhm_employee_details = NHMEmployee::where('application_id','=',$id)->first();
             if($is_status_updated){

            return redirect("/")->with('success', 'Employee with Application ID:'.$id.' is verified');
            // return redirect("/")->with('success', 'Employee Verified Successfully with Emp Code '.$nhm_employee_details->emp_code);
            }
        
        } else if ($_POST['submit'] == 'Reject') {
            $input = [
            'verification_status' => $Rejected,'comments' => $comments];
            $is_status_updated=nhm_employee_details::where('id', $id)
            ->update($input);
            if($is_status_updated){
                return redirect("/")->with('success', 'Employee with Application ID:'.$id.' is rejected');
            } 


        // if($verifysubmit!=null){
        //       $input = [
        //     'verification_status' => $verifysubmit,'comments' => $comments];
        // }else{
        //        $input = [
        //     'verification_status' => $rejectsubmit, 'comments' => $comments];
        // }
        //$id=$request->input('id');
        // $id=Input::get('id');
        
        //print_r($id);
        //$single_employee_details = nhm_employee_details::findOrFail($id);
        //print_r($single_employee_details);
        // $input = [
        //     'verification_status' => $request['verification_status']
        // ];
        
        // $is_status_updated=nhm_employee_details::where('id', $id)
        //     ->update($input);

        // $nhm_employee_details = NHMEmployee::where('application_id','=',$id)->first();
        
        // print_r($is_status_updated);
        // print_r("DONE");
        // if($is_status_updated){
        //     return redirect("/")->with('success', 'Employee Verified Successfully with Emp Code '.$nhm_employee_details->emp_code);
        // }       
       
        }
    }

    public function printSingleEmployee(Request $request)
    {
        //$id=> $request['id'];

        //echo("isnide show single");
        $id=$request->id;
        //$id=$request->input('id');
        // $id=Input::get('id');
        
        //print_r($id);
        $single_employee_details = nhm_employee_details::findOrFail($id);
        //print_r($single_employee_details);

        return view('print_single_nhm_employee_details', ['single_employee_details' => $single_employee_details]);

    }

    public function admingetreports()
    {
        
        $created_employee_lists = NHMEmployee::paginate(10);
        //print_r($single_employee_details);

        return view('admingetreports_view', ['created_employee_lists' => $created_employee_lists]);

    }


    public function approve()
    {

    DB::enableQueryLog();
    $flag=false;
    $user_id = AuthChecker::getUserId();
    $dutys = Configduty::where('user_id','=',$user_id)->get();
   //dd($duty);


    $i=0;
    $body_codes=[];
    $is_active_status=[];

    foreach($dutys as $duty){

    if($duty->mapping_level=="State HQ"){
        if($duty->is_active==1){
        $body_codes[$i] = 1;
        $is_active_status[$i]=1;
        }
        else
        {
             $body_codes[$i] = null;
            
        }

    }else if($duty->mapping_level=="District HQ"){
        if($duty->is_active==1){
        $body_codes[$i] = $duty->district_code;
        $is_active_status[$i]=1;
        }
        else
        {
           $body_codes[$i] = null; 
        }
    }else{
        //$nhm_employee_details->is_urban = $duty->is_urban;
        if($duty->is_urban==1){
            if($duty->is_active==1){
            $body_codes[$i] = $duty->urban_body_code;
            $is_active_status[$i]=1;
            }
            else
            {
                $body_codes[$i]=null;
            }


        }else{
            if($duty->is_active==1){
            $body_codes[$i] = $duty->taluka_code;
            $is_active_status[$i]=1;
            }
            else
            {
               $body_codes[$i]=null;  
            }
    }   
   } 
   $i++;
  }
  //dd(($body_codes));

  // $nhm_employee_details=DB::table('nhm_employee_details')->where('nhm_employee_details.body_code','=',$body_code)->where('nhm_employee_details.verification_status','=','Verified')->leftJoin('nhm_employees','nhm_employee_details.id','=','nhm_employees.application_id')->select('nhm_employee_details.*','nhm_employees.emp_code')->paginate(10);

    $nhm_employee_details=DB::table('nhm_employee_details')->where(function ($query) use ($body_codes){
          foreach($body_codes as $body_code) {
             $query->orWhere('nhm_employee_details.body_code', '=', $body_code);
             
          }
       })->where('nhm_employee_details.verification_status','=','Verified')->leftJoin('nhm_employees','nhm_employee_details.id','=','nhm_employees.application_id')->select('nhm_employee_details.*','nhm_employees.emp_code')->orderBy('nhm_employee_details.id')//get()//;
        ->paginate(10);
//dd(DB::getQueryLog()); 
//dd($nhm_employee_details); 
/*********************************************OLD code till 21-01-2020********************/
   //  $flag=false;
   //  $user_id = AuthChecker::getUserId();
   //  $duty = Configduty::where('user_id','=',$user_id)->first();

   //  if($duty->mapping_level=="State HQ"){
   //      $body_code = 1;
   //  }else if($duty->mapping_level=="District HQ"){
   //      $body_code = $duty->district_code;
   //  }else{
        
   //      if($duty->is_urban==1){
   //          $body_code = $duty->urban_body_code;
   //      }else{
   //          $body_code = $duty->taluka_code;
   //  }   
   // } 
    


   //   //$nhm_employee_details = NHMEmployee::where('body_code','=',$body_code)->paginate(10);//
     
   //  $nhm_employee_details=DB::table('nhm_employee_details')->where('nhm_employee_details.body_code','=',$body_code)->where('nhm_employee_details.verification_status','=','Verified')->leftJoin('nhm_employees','nhm_employee_details.id','=','nhm_employees.application_id')->select('nhm_employee_details.*','nhm_employees.emp_code')->paginate(10);


    // $nhm_employee_details = nhm_employee_details::where('body_code','=',$body_code)->paginate(10);
 /********************************************************************************************************/ 
      if(empty($is_active_status)){
        return redirect("/")->with('success', 'User Disabled');
     
     }
     else
     {

        return view('approve_nhm_employee_details',['nhm_employee_details' => $nhm_employee_details,'flag'=>$flag]);

     }




    

    }

    

    public function showSingleEmployeeApproval(Request $request)
    {
        
        $id=$request->id;
        
        $single_employee_details = nhm_employee_details::find($id);
        
        if($single_employee_details->approval_status == "Approved"){
            $single_employee_details = NHMEmployee::where('application_id','=',$id)->first();
        }

        // return Redirect::back()->with(['single_employee_details'=>$single_employee_details,'flag'=>$flag]);
        



        return view('show_single_nhm_employee_details_Approval', ['single_employee_details' => $single_employee_details]);
    }



    public function approvedata(Request $request)
    {
        
        $id=$request->id;
        $Approved="Approved";
        $Disapproved="Disapproved";
       
        $comments=$request->comments;

       if ($_POST['submit'] == 'Approve') {

             $input = [
            'approval_status' => $Approved,'approval_comments' => $comments];
            
            $is_status_updated=nhm_employee_details::where('id', $id)
            ->update($input);
            // dd($is_status_updated);
            $nhm_employee_details = NHMEmployee::where('application_id','=',$id)->first();            
            //$nhm_employee_details = NHMEmployee::where('id','=',$id)->first();
            $mobileNo =$nhm_employee_details->mobile_number_1;
            
             if($is_status_updated){

             $smsObj = new SmsSendController();
             //$smsObj->initiateSmsActivation($mobileNo,"NHM employee Code: ".$nhm_employee_details->emp_code." has been generated. Preserve it for further reference");
            
             $is_sms_sent=[ 'is_sms_sent' => 1]; 

             //$nhm_sms= nhm_employee_details::where('id','=', $id)->update($is_sms_sent);

             $nhm_sms1=NHMEmployee::where('application_id','=',$id)->update($is_sms_sent);
           

             
              return redirect("/")->with('success', 'Employee Approved Successfully with Emp Code '.$nhm_employee_details->emp_code);
            //return redirect("/")->with('success', 'Employee Approved Successfully with Emp Code '.$nhm_employee_details->emp_code);
            }
        
        } else if ($_POST['submit'] == 'Disapprove') {
           // dd('hi');
            $input = [
            'approval_status' => $Disapproved,'approval_comments' => $comments];
            
            $is_status_updated=nhm_employee_details::where('id', $id)
            ->update($input);
            //dd($is_status_updated);
            if($is_status_updated){

                return redirect("/")->with('success', 'Employee with Application ID:'.$id.' is Not Approved');
            } 

    
       
        }
    }

    public function MassEmployeeApproval(Request $request){
        $inputs = request()->input('approvalcheck');
        $Approved="Approved";
        $comments="Bulk Approval";
          $data = [
            'approval_status' => $Approved,'approval_comments' => $comments];
        
        foreach($inputs as $input){
          $is_status_updated=nhm_employee_details::where('id', $input)->update($data);
        }
         if($is_status_updated){
            return redirect("/")->with('success', 'Employee Records Approved Successfully');
            }
        //dd($inputs);
    }





    private function validateInput($request) {
        $this->validate($request, [
            //'title' => 'required',
            //'last_name' => 'required|max:60', 
            'first_name' => 'required|max:60',
            'middle_name' => 'string|nullable',
            'guardian_relation' => 'required',
            'guardian_name' => 'required|max:60',
            'dob' => 'required|date',
            'gender' => 'required',
            'caste_category' => 'required',
            'pwd' => 'required',
            'marital_status' => 'required',
            'mobile_number_1' => 'required|regex:/^\d{10}$/',
            'mobile_number_2' => 'regex:/^\d{10}$/|nullable',
            'email' => 'required|e-mail',
            'identification_mark' => 'max:50',
            'blood_group' => 'required',
            'person_name_emergency' => 'required|max:60',
            'person_emergency_mobile' => 'required|regex:/^\d{10}$/',
            'present_address_line1' => 'required|max:120',
            'present_address_district' => 'required|max:120',
            'present_address_police_station' => 'required|max:120',
            'present_address_pincode' => 'required|regex:/^\d{6}$/',
            'permanent_address_line1' => 'required|max:120',
            'permanent_address_district' => 'required|max:120',
            'permanent_address_police_station' => 'required|max:120',
            'permanent_address_pincode' => 'required|regex:/^\d{6}$/',
            'highest_education' => 'required',
            //'other_professional_qualification' => 'required|max:150',
            // 'other_professional_qualification' => 'requiredIf($request->user()->is_admin),',
            'pan' => 'required',
            'bank_account_number' => 'required|numeric',
            'name_of_bank' => 'required',
            'bank_branch' => 'required',
            'bank_ifsc_code' => 'required',
            'engaged_or_not_nhm' => 'required',
            'engaged_or_not_hfw' => 'required',
            'appointing_authority' => 'required',
            'contractual_employment_under' => 'required',
            'service_category' => 'required',
            'contractual_under_nhm' => 'required',
            'programme_head' => 'required',
            'designation_list' => 'required',
            'date_of_joining' => 'required|date',
            'con_rem_time_joining' => 'required|numeric',
            'date_of_joining_in_posting' => 'required|date',
            'monthly_rem' => 'required|numeric',
            'posting_level' => 'required|max:60',
            
        ]);
    }
/**********************************************************/
}
