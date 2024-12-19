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
use App\PensionLegacy;
//Dynamic Doc
use App\BenDocsLegacy;
use App\BenDocsArcLegacy;
use App\DocumentType;
use App\SchemeDocMap;
//Dynamic Doc End
use App\Manabik;
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuthChecker;

class PensionformLegacyController extends Controller
{
    
    public function __construct()
    {
          
    }
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        //LEGACY SCHEDULED CAST FORM 
        $scheme_id=1;
        $is_active=0;

        $roleArray=$request->session()->get('role');        
        foreach ($roleArray as $roleObj) {           
            if($roleObj['scheme_id'] == $scheme_id){
                $is_active=1;
                $request->session()->put('level', $roleObj['mapping_level']);
                $request->session()->put('distCode', $roleObj['district_code']);
                if($roleObj['is_urban']==1){
                    $request->session()->put('blockCode', $roleObj['urban_body_code']);
                }else{
                    $request->session()->put('blockCode', $roleObj['taluka_code']);
                }            
            break;
            }
        }          
        
        if($is_active==1){        
            $districts = District::all();
            //return view('pension_details')->with('districts',$districts); 
            
            //Document Dynamic
            $doc_id_list = SchemeDocMap::select('doc_list_man','doc_list_opt')->where('scheme_code',$scheme_id)->get();
            $doc_list_man = DocumentType::get()->whereIn("id",json_decode($doc_id_list[0]['doc_list_man']));
            $doc_list_opt = DocumentType::get()->whereIn("id",json_decode($doc_id_list[0]['doc_list_opt']));
            $doc_profile_image = DocumentType::get()
                                    ->where("is_profile_pic",true)->first();

            $doc_profile_image_id = 999;
            if($doc_profile_image){
                $doc_profile_image_id = $doc_profile_image->id;
            }                        
            //echo "<pre>";print_r($doc_profile_image_id); echo "</pre>";die();  
            return view('legacy/pension_details',['districts' => $districts,
            'scheme_id'=>$scheme_id,'doc_list_man'=>$doc_list_man,'doc_list_opt'=>$doc_list_opt, 'profile_img'=>$doc_profile_image_id]);      
    
        }if($is_active==0){
            return redirect("/")->with('success', 'User Disabled');
        }else{
            return redirect("/")->with('success', 'User Disabled');
        }
    
    
    }


    
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

        $user_id = AuthChecker::getUserId();
        $users = User::find($user_id);
        $server_ip =$_SERVER['SERVER_ADDR'];
        $base_url= url('/');
        $uploaded_doc = array();
        // $this->validate($request, [
        //     'first_name' => 'required'         
            
        // ]);
        //ONLY ST FORM FOR LEGACY
        $scheme_id=1;
        $doc_id_list = SchemeDocMap::select('doc_list_man','doc_list_opt')->where('scheme_code',$scheme_id)->get();
        $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
        $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
        $doc_list = array_merge($doc_list_man,$doc_list_opt);



        $this->validateInput($request,  $scheme_id); 
        $body = Assembly::where('ac_no', '=', $request->asmb_cons)->first(); 

        //echo "<pre>";print_r($users);exit;

        
        $pension_details = new PensionLegacy();
        

        //Document Dynamic
       

         foreach ($doc_list as $doc) {
            if ($request->hasFile('doc_'.$doc)) {
                $doc_file = $request->file('doc_'.$doc);
                $file_passport = $doc_file->getClientOriginalName();
                $file_type = $doc_file->getClientOriginalExtension();
                $file_profile = "doc_".$doc."_".rand(10000,99999).'_'.time().'.'.$doc_file->getClientOriginalExtension();
                $destinationPath = storage_path('app/keep_legacy/');
                $fileStore[] = $doc_file->move($destinationPath, $file_profile);
                //array_push($uploaded_doc,$file_profile);
                $uploaded_doc[$doc]=$file_profile;

            }       
          else
            {
            $file_passport= null;
            } 
                
         }
        //Document Dynamic End

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
        
        $assembly = Assembly::where('ac_no','=', $request->asmb_cons)->first();
        $assembly_name = $assembly->ac_name;
               

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
        $pension_details->mothly_income =$request->monthly_income;

        $pension_details->spouse_fname =$request->spouse_first_name;
        $pension_details->spouse_mname =$request->spouse_middle_name;
        $pension_details->spouse_lname =$request->spouse_last_name;


        //$pension_details->bpl_y_n =$request->if_bpl;
        
        $pension_details->ration_card_cat = $request->ration_card_cat;
        $pension_details->ration_card_no  =$request->ration_card_no;
        $pension_details->ahl_tin  =$request->ahl_tin;
        $pension_details->aadhar_no  =$request->aadhar_no;
        $pension_details->epic_voter_id  =$request->epic_voter_id;
        $pension_details->pan_no  =$request->pan_no;
        $pension_details->bpl_seq_no =$request->bpl_seq_no;        
        $pension_details->bpl_id_no =$request->bpl_id_no;
        $pension_details->bpl_total_score =$request->bpl_total_score;
       
        $pension_details->dist_code       =      $request->district;
        $pension_details->rural_urban_id     =      $request->urban_code;
        $pension_details->assembly_code   =    $request->asmb_cons;
        $pension_details->assembly_name = $assembly_name;
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

        // $pension_details->passport_image   = $filename;    
        // $pension_details->signature_image   = $filename_sig_img;

        $pension_details->created_by = Auth::user()->id;
        $pension_details->created_by_level = $request->session()->get('level');
        $pension_details->created_by_dist_code = $request->session()->get('distCode');
        $pension_details->created_by_local_body_code = $request->session()->get('blockCode');
        $pension_details->scheme_id =  1;
        $is_saved=0;
        DB::beginTransaction();
        try{   
         
            $is_saved=$pension_details->save(); 

            $id=$pension_details->id;

            $i = 0;
            foreach($uploaded_doc as $doc_type=>$doc){
               
                $ben_docs = new BenDocsLegacy();
              
                $ben_docs->ben_id = $id;
                $ben_docs->doc_type_id = $doc_type;
                $ben_docs->doc_name = $base_url.'/images_legacy/'.$doc;

                $doc_type_name = DocumentType::where('id',$doc_type)->get();
                $ben_docs->doc_type_name = $doc_type_name[0]['doc_name'];

                
                $ben_docs->is_active = true;
                $ben_docs->save();
                $i++;
            }
        }catch(\Exception $e){
            DB::rollback();
        }
        DB::commit();

        //print_r($is_saved);
        $str_caste = strtolower($request->caste_category);
        $id=$pension_details->benid;
       
        if($is_saved){
            
        return redirect("legacy/pensionform")->with('success', 'Application Submitted Successfully')
        ->with('id',  $id);
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
        $user_id = AuthChecker::getUserId();
        $rows = PensionLegacy::orderBy('id', 'desc')->paginate(500);     
        return view('legacy/pension_list',['nhm_employee_details' => $rows]);
     

    }
    public function approvedlistReadOnly(Request $request){
        //DB::enableQueryLog();
    
        $user_id = AuthChecker::getUserId();
    
        if($request->get('pr1')){
            if($request->get('pr1')=="sc"){
                $scheme_id=3;
                 $rows = PensionSc::where(['scheme_id'=>$scheme_id, 'created_by'=>$user_id])
                        ->where('next_level_role_id','!=',null)
                        ->orderBy('id', 'desc')
                        ->paginate(10)->appends(request()->query());     
            }else if($request->get('pr1')=="st"){
                $scheme_id=1;
                 $rows = PensionSt::where(['scheme_id'=>$scheme_id, 'created_by'=>$user_id])
                        ->where('next_level_role_id','!=',null)
                        ->orderBy('id', 'desc')
                        ->paginate(10)->appends(request()->query());  
              
            }else if($request->get('pr1')=="manabik"){
                $scheme_id=2;
               $rows = Manabik::where(['scheme_id'=>$scheme_id, 'created_by'=>$user_id])
                        ->orderBy('id', 'desc')
                        ->paginate(10)->appends(request()->query());  
            }
            else
            {
                $rows =array();
    
            }
        }
             
        return view('pension_list_read_only',['nhm_employee_details' => $rows,'scheme_id'=>$scheme_id, 'list_type'=>'1']);  
    }

    public function applicationlistReadOnly(Request $request){
    //DB::enableQueryLog();

    $user_id = AuthChecker::getUserId();
    $sucess = $request->get('sucess');
    $id = $request->get('id');

    
    $scheme_id=1;
    $rows = PensionLegacy::where(['scheme_id'=>$scheme_id, 'created_by'=>$user_id, 'next_level_role_id'=>null])
            ->orderBy('id', 'desc')
            ->paginate(10)->appends(request()->query());  
          
        
   
   
    return view('legacy/pension_list_read_only',['nhm_employee_details' => $rows,'scheme_id'=>$scheme_id, 'list_type'=>'0', 'sucess'=>$sucess,'id'=>$id]);
     

}

    public function applicationdetails(Request $request)
    {
       
        $id=$request->id; 
           
        $row = PensionLegacy::find($id);
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
           
      
     
   
       
        return view('legacy/pension_view_details', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name]);
    }

    public function applicationdetailsReadOnly(Request $request)
    {
   
        $id=$request->id; 
        $scheme_id=$request->scheme_id;
        $docs = array();

        
        $row = PensionLegacy::find($id);
        $docs = BenDocsLegacy::where('ben_id',$id)->orderBy('doc_type_id')->get();


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
            if($row->rural_urban_id == 1)  
            {
            $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
            $block_name = $block->urban_body_name;
            
            }
            else
            {
            $block= Taluka::where('block_code','=',$row->block_ulb_code)->first();
            $block_name = $block->block_name;

            }
        
        }
        if($row->gp_ward_code !="")
        {
            if($row->rural_urban_id == 1)  
            {
            $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
            $gp_name =  $gp_ward->urban_body_ward_name;
           
            }
            else
            {
            $gp = GP::where('gram_panchyat_code','=',$row->gp_ward_code)->get(['gram_panchyat_code','gram_panchyat_name'])->first();
            $gp_name =  $gp->gram_panchyat_name;

            }
        
        }
        $doc_profile_image = DocumentType::get()
                                    ->where("is_profile_pic",true)->first();
        $doc_profile_image_id = 999;
        if($doc_profile_image){
            $doc_profile_image_id = $doc_profile_image->id;
        }  
        return view('legacy/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
    }

    public function applicationeditview(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $id=$request->id; 
        
        //echo "scheme_id".$scheme_id;die();
        $scheme_id = 1;
        //$row = '';
        
        $row = PensionLegacy::find($id);
           
        
       
        $districts = District::where('is_revenue_district','=','1')->get(['district_code','district_name']);

       // $blocks= Taluka::where('district_code','=',$row->dist_code)->get(['block_code', 'block_name']);

       // $gps = GP::where('block_code','=',$row->block_ulb_code)->get(['gram_panchyat_code','gram_panchyat_name']);

        //Document Dynamic
        $doc_id_list = SchemeDocMap::select('doc_list_man','doc_list_opt')->where('scheme_code',$scheme_id)->get();


        $doc_list_man = DocumentType::get()->whereIn("id",json_decode($doc_id_list[0]['doc_list_man']));
        $doc_list_opt = DocumentType::get()->whereIn("id",json_decode($doc_id_list[0]['doc_list_opt']));
        $doc_profile_image = DocumentType::get()
                                ->where("is_profile_pic",true)->first();

        $doc_profile_image_id = 999;
        if($doc_profile_image){
            $doc_profile_image_id = $doc_profile_image->id;
        }                        
        // echo "<pre>";print_r($row); echo "</pre>";die();  
        
        return view('legacy/pension_edit', ['row' => $row, 'districts' => $districts , 'scheme_id'=>$scheme_id,'doc_list_man'=>$doc_list_man,'doc_list_opt'=>$doc_list_opt, 'profile_img'=>$doc_profile_image_id]);
    }


    public function applicationupdate(Request $request)
    {
       
        $base_url= url('/');
        $id=$request->id; 
        $scheme_id = 1;

        //ho "<pre>";print_r($request->input());exit;
        //exit;
        //$this->validateInput($request, $scheme_id); 
       
        
        $row = PensionLegacy::find($id);   
        
        $social_security_pension="";
        $receive_pension="";
        if($request->receive_pension !="")
        {
            $receive_pension = implode(',', $request->receive_pension);

        }

        if($request->social_security_pension !="")
        {
            $social_security_pension = implode(',', $request->social_security_pension);
        }    
    
        $block_ulb_name="";
        $gp_ward_name="";

        if($request->urban_code == 1){
            $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
            $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();


            $block_ulb_name = $block_ulb->urban_body_name;
            $gp_ward_name   = $gp_ward->urban_body_ward_name;
        }
        else
        {
            $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
            $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();

            $block_ulb_name = $block_ulb->block_name;
            $gp_ward_name   = $gp_ward->gram_panchyat_name;
        }    
        $assembly = Assembly::where('ac_no','=', $request->asmb_cons)->first();
        $assembly_name = $assembly->ac_name;

        if(trim($request->marital_status)!="Married"){
            $request->spouse_first_name="";
            $request->spouse_middle_name="";
            $request->spouse_last_name="";
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
        //'bpl_y_n' =>$request->if_bpl,
        'bpl_seq_no' =>$request->bpl_seq_no,        
        'bpl_id_no' =>$request->bpl_id_no,
        'bpl_total_score' =>$request->bpl_total_score,
        'mothly_income' =>$request->monthly_income,

        'receive_pension'=>$receive_pension,
        'social_security_pension' => $social_security_pension,

        'ration_card_cat' => $request->ration_card_cat,
        'ration_card_no'  =>$request->ration_card_no,
        'ahl_tin'  =>$request->ahl_tin,
        'aadhar_no'  =>$request->aadhar_no,
        'epic_voter_id'  =>$request->epic_voter_id,
        'pan_no'  =>$request->pan_no,



        'dist_code' =>$request->district,
        'assembly_code'  =>$request->asmb_cons,
        'assembly_name' => $assembly_name,
        'rural_urban_id' => $request->urban_code, 
        'police_station'  =>$request->police_station,
        'block_ulb_code'  =>$request->block,
        'block_ulb_name' =>$block_ulb_name,
        'gp_ward_code' =>$request->gp_ward,
        'gp_ward_name' =>$gp_ward_name,
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
        'nominate_name' => $request->nominate_name,
        'nominate_address' => $request->nominate_address,
        'nominate_relationship' => $request->nominate_relationship
        ];
            
        $pr1=""; 
        $uploaded_doc = array();
        $doc_id_list = SchemeDocMap::select('doc_list_man','doc_list_opt')->where('scheme_code',$scheme_id)->get();
        $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
        $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
        $doc_list = array_merge($doc_list_man,$doc_list_opt);

        foreach ($doc_list as $doc) {
            if ($request->hasFile('doc_'.$doc)) {
                $doc_file = $request->file('doc_'.$doc);
                $file_passport = $doc_file->getClientOriginalName();
                $file_type = $doc_file->getClientOriginalExtension();
                $file_profile = "doc_".$doc."_".rand(10000,99999).'_'.time().'.'.$doc_file->getClientOriginalExtension();
                $destinationPath = storage_path('app/keep_legacy/');
                $fileStore[] = $doc_file->move($destinationPath, $file_profile);
                //array_push($uploaded_doc,$file_profile);
                $uploaded_doc[$doc]=$file_profile;
            }       
          else
            {
            $file_passport= null;
            } 
                
         }
        DB::beginTransaction();
        try{
            
            PensionLegacy::where('id', $id)
                ->update($input);    
            $pr1="st";
            

            $i = 0;
            foreach($uploaded_doc as $doc_type => $doc){
                $ben_docs = null;
                $ben_docs_arc = null;
                
                $ben_docs = BenDocsLegacy::where('ben_id',$id)
                        ->where('doc_type_id',$doc_type)->first();
                $ben_docs_arc = new BenDocsArcLegacy();        

                if($ben_docs == null){
                    
                    $ben_docs = new BenDocsLegacy();

                    if($ben_docs != null){
                        $ben_docs->ben_id = $id;
                        $ben_docs->doc_type_id = $doc_type;
                        $doc_type_name = DocumentType::where('id',$doc_type)->get();
                        $ben_docs->doc_type_name = $doc_type_name[0]['doc_name'];
                    }
                }else{
            
                    $filename = basename($ben_docs->doc_name);
                    //Delete existing file while updating
                    // if(file_exists(public_path('document').'//'.$filename)){
                    //     unlink(public_path('document').'//'.$filename);
                    // }
                    //Move existing file while updating
                    if(file_exists(storage_path('app/keep_legacy/').'//'.$filename)){
                        rename(storage_path('app/keep_legacy/').'//'.$filename, storage_path('app/keep_legacy_bak/').'//'.$filename);
                    }
                    $ben_docs_arc->ben_id = $ben_docs->ben_id;
                    $ben_docs_arc->doc_type_id = $ben_docs->doc_type_id;
                    $ben_docs_arc->doc_type_name = $ben_docs->doc_type_name;  
                    $ben_docs_arc->doc_name = $ben_docs->doc_name;
                    $ben_docs_arc->created_at = $ben_docs->created_at;
                    $ben_docs_arc->deleted_at = date('Y-m-d H:i:s', time());
                    $ben_docs_arc->save();  
                }
                if($ben_docs != null){

                    $ben_docs->doc_name = $base_url.'/images_legacy/'.$doc;
                    $ben_docs->is_active = true;
                    $ben_docs->save();    
                }
                  
                $i++;
            }
        }catch(\Exception $e){
            DB::rollback();
        }
        DB::commit();      

        return redirect("legacy/application-list-read-only")->with('success', 'Application Updated Successfully')
        ->with('id',  $id);
       
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
        
        $created_employee_lists = NHMEmployee::paginate(500);
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
        ->paginate(500);
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





     private function validateInput($request,$scheme_id) {
        // print_r($arr);exit;

        $doc_id_list = SchemeDocMap::select('doc_list_man')->where('scheme_code',$scheme_id)->first();

        $in_array = json_decode($doc_id_list->doc_list_man);

        $doc_list = DocumentType::select('id','doc_type','doc_name','doc_size_kb')->get();       

       
        $singleArray = array();
        $nicenameArray = array();
        $customMessage = array();
        foreach($doc_list as $key=> $value)
        {          

            if (in_array($value->id,  $in_array))
            {
               $required='required';
            }
            else
            {
              $required='nullable';
            }
            
            
            // $multiArray[$val->id]= array('id'=>$val->id,'required'=>$required,'mime'=>$val->doc_type, 'size'=>$val->doc_size_kb);
            $singleArray['doc_'.$value->id] = $required.'|mimes:'.$value->doc_type.'|max:'.$value->doc_size_kb.',';
            $nicenameArray['doc_'.$value->id] = $value->doc_name.',';
            $customMessage['doc_'.$value->id.'.max'] = "The file uploaded for :attribute size must be less than :max KB";
            $customMessage['doc_'.$value->id.'.mimes'] = "The file uploaded for :attribute must be of type ".$value->doc_type;
            $customMessage['doc_'.$value->id.'.required'] = "Document for :attribute must be uploaded";

        }

        //echo "<pre>";print_r($singleArray);exit;

        // $singleArray = array();

        // foreach ($multiArray as $key => $value){
        // $singleArray['doc_'.$key] = $value['required'].'|mimes:'.$value['mime'].'|max:'.$value['size'].',';
        // } 

        

        $this->validate($request, array_merge([
            //'first_name' => 'required|string|max:200',
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
            'bpl_seq_no' => 'string|nullable|max:12',        
            'bpl_id_no' => 'string|nullable|max:12',
            'bpl_total_score' => 'integer|nullable',
            'monthly_income' => 'required|numeric|between: 0.00,999999.99',


            'ration_card_cat' => 'required|string',
            'ration_card_no' => 'required|string|max:11',
            
            'ahl_tin' => 'string|nullable|max:100',
            'aadhar_no' => 'numeric|digits:12|nullable',
            'epic_voter_id' => 'required|string|max:20',
            'pan_no' => 'string|nullable|max:12',



          //  'district' => 'string',
            'asmb_cons' => 'required|string',
            'police_station' => 'required|string',
            //'block' => 'max:200',
           // 'gp_ward' => 'max:200',
            'village' => 'required|string|max:300',
            'house' => 'string|nullable',
            'post_office' => 'required|string',
            'pin_code' => 'required|numeric|digits:6',
            'residency_period' => 'required|integer',
            'mobile_no' => 'required|numeric|digits:10',        
            'email' => 'string|email|nullable',



            'name_of_bank' => 'required|string|max:200',
            'bank_branch' => 'required|string|max:200',
            'bank_account_number' => 'required|numeric',
            'bank_ifsc_code' => 'required|string|max:11',         

          
            
        ],$singleArray),$customMessage,$nicenameArray);
    }
/**********************************************************/
}
