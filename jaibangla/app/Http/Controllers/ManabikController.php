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

use App\PensionSc;
use App\PensionSt;
use App\Manabik;
use App\Assembly;
use App\Taluka;
use App\ward;


use App\GP;
use App\User;

use Redirect;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class ManabikController extends Controller
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
    public function index(Request $request)
    {   

        /*//$caste = $_GET['pr1']; 
        $user_id = Auth::user()->id;

         $users = User::find($user_id);

        //echo "<pre>";print_r($users);exit;

        //$districts = District::all();
        $districts = District::where('is_revenue_district','=','1')->get(['district_code','district_name']);
        $major_programme_heads = majorProgammeHeadMaster::all();
        $service_categorys = nhm_service_category::all();
        $duty = Configduty::where('user_id','=',$user_id)->first();

        $is_active = $duty->is_active;
        
       //dd($is_active);
        if($is_active==1)
        {
          $request->session()->put('level', $duty->mapping_level);
            if($duty->mapping_level=="Block"){
                $request->session()->put('distCode', $duty->district_code);
                $request->session()->put('blockCode', $duty->taluka_code);

            }

            //echo $users->user_scheme_id;exit;

            if($users->user_scheme_id == 2)
            {
               return view('manabik_form_details')->with('districts',$districts);

            }
            else
            {
               return redirect("/")->with('success', 'Invalid Scheme');
            }       
    
        }
        elseif($is_active==0)
        {
        return redirect("/")->with('success', 'User Disabled');
        }
        else
        {
        return redirect("/")->with('success', 'User Disabled');
        }*/
        $scheme_id  = 2;
        $is_active=0;
        $roleArray=$request->session()->get('role');        
        foreach ($roleArray as $roleObj) {           
          if($roleObj['scheme_id'] == $scheme_id){
            $is_active=1;
            $request->session()->put('level', $roleObj['mapping_level']);
            $request->session()->put('distCode', $roleObj['district_code']);
            $request->session()->put('blockCode', $roleObj['taluka_code']);
            break;
          }
        }
        if($is_active==1){        
            $districts = District::all();
            //return view('manabik_form_details')->with('districts',$districts);

            return view('manabik_form_details',['districts' => $districts,
            'scheme_id'=>$scheme_id]);
    
        }if($is_active==0){

            return redirect("/")->with('success', 'User Disabled');
        }else{

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


        $user_id = Auth::user()->id;
        $users = User::find($user_id);
        $this->validateInput($request); 
        $body = Assembly::where('ac_no', '=', $request->asmb_cons)->first(); 

        // $this->validate($request, [
        // 'first_name' => 'required'
        // ]);

         //echo "<pre>";print_r($users);exit;
        $pension_details = new Manabik();

        if($request->urban_code == 1){
        $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
        $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();


        $pension_details->block_ulb_name = $block_ulb->urban_body_name;
        $pension_details->gp_ward_name   = $gp_ward->urban_body_ward_name;
        }
        else
        {
        $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
        $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();

        $pension_details->block_ulb_name = $block_ulb->block_name;
        $pension_details->gp_ward_name   = $gp_ward->gram_panchyat_name;
        }
        

        if ($request->hasFile('passport_image')) {
        $profileImg = $request->file('passport_image');
        $file_passport = $profileImg->getClientOriginalName();
        $file_type = $profileImg->getClientOriginalExtension();
        $image_profile = "pass_".rand(10000,99999).'_'.time().'.'.$profileImg->getClientOriginalExtension();
        $destinationPath = storage_path('app\\keep\\');
        $fileStore[] = $profileImg->move($destinationPath, $image_profile);
        $pension_details->passport_image = $image_profile;
        }       
        else
        {
        $file_passport= null;
        } 

        if ($request->hasFile('signature_image')) {
        $profileImg = $request->file('signature_image');
        $file_signature = $profileImg->getClientOriginalName();
        $file_type = $profileImg->getClientOriginalExtension();
        $image_profile = "sign_".rand(10000,99999).'_'.time().'.'.$profileImg->getClientOriginalExtension();
        $destinationPath = storage_path('app\\keep\\');
        $fileStore[] = $profileImg->move($destinationPath, $image_profile);
        $pension_details->signature_image = $image_profile;
        }       
        else
        {
        $file_signature= null;
        }  

        if ($request->hasFile('cast_certificate_file')) {
        $profileImg = $request->file('cast_certificate_file');
        $file_signature = $profileImg->getClientOriginalName();
        $file_type = $profileImg->getClientOriginalExtension();
        $image_profile = "sign_".rand(10000,99999).'_'.time().'.'.$profileImg->getClientOriginalExtension();
        $destinationPath = storage_path('app\\keep\\');
        $fileStore[] = $profileImg->move($destinationPath, $image_profile);
        $pension_details->cast_certificate_file = $image_profile;
        }       
        else
        {
        $cast_certificate_file= null;
        }  

        if($request->hasFile('disability_certificate_file')) {
        $profileImg = $request->file('disability_certificate_file');
        $file_signature = $profileImg->getClientOriginalName();
        $file_type = $profileImg->getClientOriginalExtension();
        $image_profile = "sign_".rand(10000,99999).'_'.time().'.'.$profileImg->getClientOriginalExtension();
        $destinationPath = storage_path('app\\keep\\');
        $fileStore[] = $profileImg->move($destinationPath, $image_profile);
        $pension_details->disability_certificate_file = $image_profile;
        }       
        else
        {
        $disability_certificate_file= null;
        }  

        if($request->hasFile('digital_ration_card_file')) {
        $profileImg = $request->file('digital_ration_card_file');
        $file_signature = $profileImg->getClientOriginalName();
        $file_type = $profileImg->getClientOriginalExtension();
        $image_profile = "sign_".rand(10000,99999).'_'.time().'.'.$profileImg->getClientOriginalExtension();
        $destinationPath = storage_path('app\\keep\\');
        $fileStore[] = $profileImg->move($destinationPath, $image_profile);
        $pension_details->digital_ration_card_file = $image_profile;
        }       
        else
        {
        $digital_ration_card_file= null;
        }  
      
        if($request->hasFile('aadhar_card_file')) {
        $profileImg = $request->file('aadhar_card_file');
        $file_signature = $profileImg->getClientOriginalName();
        $file_type = $profileImg->getClientOriginalExtension();
        $image_profile = "sign_".rand(10000,99999).'_'.time().'.'.$profileImg->getClientOriginalExtension();
        $destinationPath = storage_path('app\\keep\\');
        $fileStore[] = $profileImg->move($destinationPath, $image_profile);
        $pension_details->aadhar_card_file = $image_profile;
        }       
        else
        {
        $aadhar_card_file= null;
        } 

        if($request->hasFile('voter_id_file')) {
        $profileImg = $request->file('voter_id_file');
        $file_signature = $profileImg->getClientOriginalName();
        $file_type = $profileImg->getClientOriginalExtension();
        $image_profile = "sign_".rand(10000,99999).'_'.time().'.'.$profileImg->getClientOriginalExtension();
        $destinationPath = storage_path('app\\keep\\');
        $fileStore[] = $profileImg->move($destinationPath, $image_profile);
        $pension_details->voter_id_file = $image_profile;
        }       
        else
        {
        $voter_id_file= null;
        }  

        if($request->hasFile('residential_certificate_file')) {
        $profileImg = $request->file('residential_certificate_file');
        $file_signature = $profileImg->getClientOriginalName();
        $file_type = $profileImg->getClientOriginalExtension();
        $image_profile = "sign_".rand(10000,99999).'_'.time().'.'.$profileImg->getClientOriginalExtension();
        $destinationPath = storage_path('app\\keep\\');
        $fileStore[] = $profileImg->move($destinationPath, $image_profile);
        $pension_details->residential_certificate_file = $image_profile;
        }
        else
        {
        $residential_certificate_file= null;
        }  

        if($request->hasFile('income_certificate_file')) {
        $profileImg = $request->file('income_certificate_file');
        $file_signature = $profileImg->getClientOriginalName();
        $file_type = $profileImg->getClientOriginalExtension();
        $image_profile = "sign_".rand(10000,99999).'_'.time().'.'.$profileImg->getClientOriginalExtension();
        $destinationPath = storage_path('app\\keep\\');
        $fileStore[] = $profileImg->move($destinationPath, $image_profile);
        $pension_details->income_certificate_file = $image_profile;
        }
        else
        {
        $income_certificate_file= null;
        }  

        if($request->hasFile('bank_passbook_file')) {
        $profileImg = $request->file('bank_passbook_file');
        $file_signature = $profileImg->getClientOriginalName();
        $file_type = $profileImg->getClientOriginalExtension();
        $image_profile = "sign_".rand(10000,99999).'_'.time().'.'.$profileImg->getClientOriginalExtension();
        $destinationPath = storage_path('app\\keep\\');
        $fileStore[] = $profileImg->move($destinationPath, $image_profile);
        $pension_details->bank_passbook_file = $image_profile;
        }
        else
        {
        $bank_passbook_file= null;
        }  

        if($request->hasFile('other_file')) {
        $profileImg = $request->file('other_file');
        $file_signature = $profileImg->getClientOriginalName();
        $file_type = $profileImg->getClientOriginalExtension();
        $image_profile = "sign_".rand(10000,99999).'_'.time().'.'.$profileImg->getClientOriginalExtension();
        $destinationPath = storage_path('app\\keep\\');
        $fileStore[] = $profileImg->move($destinationPath, $image_profile);
        $pension_details->other_file = $image_profile;
        }
        else
        {
        $other_file= null;
        } 
           //echo "<pre>";print_r($request->input());
           // echo $request->type_disability_hidden;  
           // echo $request->type_disability;        

        //$request->file('fileUpload');

        $type_dis = implode(',', $request->type_disability);

        if($request->receive_pension !="")
        {
        $receive_pension = implode(',', $request->receive_pension);
        $pension_details->receive_pension    =$receive_pension;
        }

        if($request->social_security_pension !="")
        {
        $social_security_pension = implode(',', $request->social_security_pension);
        $pension_details->social_security_pension   =$social_security_pension;        

        }

        
        
        
        $pension_details->ben_fname =$request->first_name;
        $pension_details->ben_mname =$request->middle_name;
        $pension_details->ben_lname =$request->last_name;
        $pension_details->gender=$request->gender;
        $pension_details->dob=$request->dob;
        $pension_details->ben_age=$request->txt_age;

        $pension_details->father_fname =$request->father_first_name;
        $pension_details->father_mname =$request->father_middle_name;
        $pension_details->father_lname =$request->father_last_name;
        $pension_details->mother_fname =$request->mother_first_name;
        $pension_details->mother_mname =$request->mother_middle_name;
        $pension_details->mother_lname =$request->mother_last_name;
        $pension_details->caste=$request->caste_category;
        $pension_details->marital_status=$request->marital_status;

        $pension_details->spouse_fname =$request->spouse_first_name;
        $pension_details->spouse_mname =$request->spouse_middle_name;
        $pension_details->spouse_lname =$request->spouse_last_name;
        $pension_details->bpl_y_n =$request->if_bpl;
        $pension_details->bpl_seq_no =$request->bpl_seq_no;        
        $pension_details->bpl_id_no =$request->bpl_id_no;
        $pension_details->bpl_total_score =$request->bpl_total_score;
        $pension_details->mothly_income =$request->monthly_income;

        $pension_details->ration_card_no  =$request->ration_card_no;
        $pension_details->ahl_tin  =$request->ahl_tin;
        $pension_details->aadhar_no  =$request->aadhar_no;
        $pension_details->epic_voter_id  =$request->epic_voter_id;
        $pension_details->pan_no  =$request->pan_no;

        $pension_details->dist_code       =      $request->district;
        $pension_details->rural_urban_id     =      $request->urban_code;
        $pension_details->assembly_name   =    $body->ac_name;
        $pension_details->police_station  =$request->police_station;
        $pension_details->block_ulb_code  =$request->block;
        $pension_details->gp_ward_code =$request->gp_ward;
        $pension_details->village_town_city  =$request->village;
        $pension_details->house_premise_no  =$request->house;
        $pension_details->post_office  =$request->post_office;
        $pension_details->pincode =$request->pin_code;
        $pension_details->residency_period =$request->residency_period;
        $pension_details->mobile_no  =$request->mobile_no;        
        $pension_details->email =$request->email;

        $pension_details->bank_name  =$request->name_of_bank;
        $pension_details->branch_name    =$request->bank_branch;
        $pension_details->bank_code    =$request->bank_account_number;
        $pension_details->bank_ifsc   =$request->bank_ifsc_code; 

        $pension_details->nominate_name    =$request->nominate_name;
        $pension_details->nominate_address    =$request->nominate_address;
        $pension_details->nominate_relationship   =$request->nominate_relationship; 

        $pension_details->type_disability   =$type_dis; 
        $pension_details->percentage_disability         =$request->percentage_disability; 
        $pension_details->certifying_auth          =$request->certifying_authority;                  

        $pension_details->created_by = Auth::user()->id;
        $pension_details->created_by_level = $request->session()->get('level');
        $pension_details->created_by_dist_code = $request->session()->get('distCode');
        $pension_details->created_by_local_body_code = $request->session()->get('blockCode');
        $pension_details->scheme_id =  $request->scheme_id;

        $is_saved=$pension_details->save();      

        //print_r($is_saved);
        $id=$pension_details->benid;
       
        if($is_saved){
            
        return redirect("manabik")->with('success', 'Application Submitted Successfully')->with('id', 
                $id);
        }


        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */



    public function applicationlist(){
    //DB::enableQueryLog();
    $user_id = Auth::user()->id;
  //   $dutys = Configduty::where('user_id','=',$user_id)->get();


  //   $i=0;
  //   $body_codes=[];
  //   $is_active_status=[];
  //   foreach($dutys as $duty){

  //   if($duty->mapping_level=="State HQ"){
  //       if($duty->is_active==1){
  //       $body_codes[$i] = 1;
  //       $is_active_status[$i]=1;
  //       }
  //       else
  //       {
  //            $body_codes[$i] = null;
            
  //       }

  //   }else if($duty->mapping_level=="District HQ"){
  //       if($duty->is_active==1){
  //       $body_codes[$i] = $duty->district_code;
  //       $is_active_status[$i]=1;
  //       }
  //       else
  //       {
  //            $body_codes[$i] = null;
  //       }


  //   }else{
        
  //       if($duty->is_urban==1){
  //           if($duty->is_active==1){
  //           $body_codes[$i] = $duty->urban_body_code;
  //           $is_active_status[$i]=1;
  //           }
  //           else
  //           {
  //            $body_codes[$i] = null;
  //           }


  //       }else{
  //           if($duty->is_active==1){
  //           $body_codes[$i] = $duty->taluka_code;
  //           $is_active_status[$i]=1;
  //           }
  //           else{
  //            $body_codes[$i] = null;
  //           }
  //   }   
  //  } 
  //  $i++;
  // }
 
  //   $nhm_employee_details=DB::table('nhm_employee_details')->where(function ($query) use ($body_codes){
  //         foreach($body_codes as $body_code) {
  //            $query->orWhere('body_code', '=', $body_code);
             
  //         }
  //      })->orderBy('nhm_employee_details.id')->paginate(10);




    $rows = PensionSc::orderBy('id', 'desc')->paginate(10);

    //echo "<pre>";print_r($rows);

    
     
     
     
    return view('pension_list',['nhm_employee_details' => $rows]);
     

}

 public function applicationdetails(Request $request)
    {
       
        $id=$request->id;        
        $row = PensionSc::find($id);
        // echo $row->block_ulb_code;exit;
        // echo "<pre>";print_r($block);exit;

        $district_name = ""; 
        $block_name = "";
        $gp_name =  "";

        if($row->dist_code !="")
        {
        $district = District::where('district_code','=',$row->dist_code)->get(['district_code','district_name'])->first(); 
        $district_name = $district->district_name; 
        }
        if($row->block_ulb_code !="")
        {    
        $block= Taluka::where('block_code','=',$row->block_ulb_code)->first();
        $block_name = $block->block_name;
        }
        if($row->gp_ward_code !="")
        {
        $gp = GP::where('gram_panchyat_code','=',$row->gp_ward_code)->get(['gram_panchyat_code','gram_panchyat_name'])->first();
        $gp_name =  $gp->gram_panchyat_name;
        } 
       
        return view('pension_view_details', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name]);
    }

    public function applicationeditview(Request $request)
    {
        
        $id=$request->id; 
        $row = PensionSc::find($id);  
        //echo "<pre>";print_r($row);exit;

        //echo $row->block_ulb_code;exit;
        $districts = District::where('is_revenue_district','=','1')->get(['district_code','district_name']);

        $blocks= Taluka::where('district_code','=',$row->dist_code)->get(['block_code', 'block_name']);

        $gps = GP::where('block_code','=',$row->block_ulb_code)->get(['gram_panchyat_code','gram_panchyat_name']);
        
        return view('pension_edit', ['row' => $row, 'districts' => $districts , 'blocks' => $blocks,  'gps' => $gps]);
    }


    public function applicationupdate(Request $request)
    {
       
       
        $id=$request->id; 

        //echo "<pre>";print_r($request->input());exit;
        //exit;
       
        $row = PensionSc::find($id);

        if ($request->file('passport_image')) 
        {

        $path = $request->file('passport_image')->store('pension');
           
        $file = $request->file('passport_image');
        $filename = rand().time().$file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $upload = $file->move(public_path('upload'), $filename);
        }
        else
        {
        $filename= $row->passport_image;
        } 

        if ($request->file('signature_image')) {
           
        $path_sig_img = $request->file('signature_image')->store('pension');
       
        $file_sig_img = $request->file('signature_image');
        $filename_sig_img = rand().time().$file_sig_img->getClientOriginalName();
        $extension_sig_img = $file_sig_img->getClientOriginalExtension();
        $upload_sig_img = $file_sig_img->move(public_path('upload'), $filename_sig_img);
        }
        else
        {
        $filename_sig_img= $row->signature_image;
        }



        $input = [
            //'name' => $request['name']
        'ben_fname' =>$request->first_name,
        'ben_mname' =>$request->middle_name,
        'ben_lname' =>$request->last_name,
        'gender'=>$request->gender,
        'dob'=>$request->dob,
        'ben_age'=>$request->txt_age,

        'father_fname' =>$request->father_first_name,
        'father_mname' =>$request->father_middle_name,
       'father_lname' =>$request->father_last_name,
        'mother_fname' =>$request->mother_first_name,
        'mother_mname' =>$request->mother_middle_name,
        'mother_lname' =>$request->mother_last_name,
        'caste'=>$request->caste_category,
        'marital_status'=>$request->marital_status,

       'spouse_fname' =>$request->spouse_first_name,
        'spouse_mname' =>$request->spouse_middle_name,
        'spouse_lname' =>$request->spouse_last_name,
        'bpl_y_n' =>$request->if_bpl,
        'bpl_seq_no' =>$request->bpl_seq_no,        
        'bpl_id_no' =>$request->bpl_id_no,
        'bpl_total_score' =>$request->bpl_total_score,
        'mothly_income' =>$request->monthly_income,



        'ration_card_no'  =>$request->ration_card_no,
        'ahl_tin'  =>$request->ahl_tin,
        'aadhar_no'  =>$request->aadhar_no,
        'epic_voter_id'  =>$request->epic_votar_id,
        'pan_no'  =>$request->pan_no,



        'dist_code' =>$request->district,
        'assembly_name'  =>$request->asmb_cons,
        'police_station'  =>$request->police_station,
        'block_ulb_code'  =>$request->block,
        'gp_ward_code' =>$request->gp_ward,
        'village_town_city'  =>$request->village,
        'house_premise_no'  =>$request->house,
        'post_office'  =>$request->post_office,
        'pincode' =>$request->pin_code,
        'residency_period' =>$request->residency_period,
        'mobile_no'  =>$request->mobile_no,
        'email' =>$request->email,



        'bank_name'  =>$request->name_of_bank,
        'branch_name'   =>$request->bank_branch,
        'bank_code'    =>$request->bank_account_number,
        'bank_ifsc'   =>$request->bank_ifsc_code, 
        'passport_image'   => $filename,    
        'signature_image'   => $filename_sig_img,
        ];
        PensionSc::where('id', $id)
            ->update($input);

        return redirect("application-list");
       
        //return view('pension_view_details', ['row' => $row]);
    }



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
        $user_id = Auth::user()->id;
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
    
     $user_id = Auth::user()->id;
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
    
//      $user_id = Auth::user()->id;
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
        $details = PensionSc::findOrFail($id);
        //print_r($single_employee_details);

        return view('print_single_nhm_employee_details', ['single_employee_details' => $details]);

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
    $user_id = Auth::user()->id;
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
   //  $user_id = Auth::user()->id;
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
            'first_name' => 'required|string|max:200',
            'middle_name' => 'string|nullable',
            'last_name' => 'required|string|max:200',
            'gender' => 'required',
           // 'dob' => '',
            'txt_age' => 'required|numeric',

            'father_first_name' => 'required|string|max:200',
            'father_middle_name' => 'string|nullable',
            'father_last_name' => 'required|string|max:200',
            'mother_first_name' => 'required|string|max:200',
            'mother_middle_name' => 'string|nullable',
            'mother_last_name' => 'required|string|max:200',
            'caste_category' => 'required',
            'marital_status' => 'required',

            'spouse_first_name' => 'string|nullable',
            'spouse_middle_name' => 'string|nullable',
            'spouse_last_name' => 'string|nullable',
            // 'if_bpl' => ,
            'bpl_seq_no' => 'string|nullable',        
            'bpl_id_no' => 'string|nullable',
            'bpl_total_score' => 'string|numeric|nullable',
            'monthly_income' => 'required|numeric',


            'ration_card_cat' => 'required|string',
            'ration_card_no' => 'required|numeric',
            
            'ahl_tin' => 'string|nullable',
            'aadhar_no' => 'numeric|digits:12|nullable',
            'epic_voter_id' => 'required|string',
            'pan_no' => 'string|nullable',



          //  'district' => 'string',
            'asmb_cons' => 'required|string',
            'police_station' => 'required|string',
            //'block' => 'max:200',
           // 'gp_ward' => 'max:200',
            'village' => 'required|string|max:300',
            'house' => 'string|nullable',
            'post_office' => 'required|string',
            'pin_code' => 'required|numeric|digits:6',
            'residency_period' => 'required|numeric',
            'mobile_no' => 'required|numeric|digits:10',        
            'email' => 'string|email|nullable',



            'name_of_bank' => 'required|string|max:200',
            'bank_branch' => 'required|string|max:200',
            'bank_account_number' => 'required|numeric',
            'bank_ifsc_code' => 'required|string',

            'percentage_disability' => 'required|numeric',  
            'certifying_authority' => 'required|string|max:255',  

            'passport_image' => 'required|mimes:jpg,jpeg,png,gif|max:2048',            
            'signature_image' => 'mimes:jpeg,jpg,png,gif,pdf|max:2048|nullable',
            'cast_certificate_file' => 'mimes:jpeg,jpg,png,gif,pdf|max:2048|nullable',
            'disability_certificate_file' => 'mimes:jpeg,jpg,png,gif,pdf|max:2048|nullable',
            'digital_ration_card_file' => 'mimes:jpeg,jpg,png,gif,pdf|max:2048|nullable',
            'aadhar_card_file' => 'mimes:jpeg,jpg,png,gif,pdf|max:2048|nullable',
            'voter_id_file' => 'mimes:jpeg,jpg,png,gif,pdf|max:2048|nullable',
            'residential_certificate_file' => 'mimes:jpeg,jpg,png,gif,pdf|max:2048|nullable',
            'income_certificate_file' => 'mimes:jpeg,jpg,png,gif,pdf|max:2048|nullable',
            'bank_passbook_file' => 'mimes:jpeg,jpg,png,gif,pdf|max:2048|nullable',
            'other_file' => 'mimes:jpeg,jpg,png,gif,pdf|max:2048|nullable',
            
        ]);
    }

  
/**********************************************************/
}
