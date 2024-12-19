<?php

namespace App\Http\Controllers;

use App\Http\Controllers\WPWCDformController;
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
use App\BenEntry;
//Dynamic Doc
use App\PensionOAPFarmer;
use App\BenDocsOAPFarmer;
use App\BenDocsArcOAPFarmer;

use App\DocumentType;
use App\SchemeDocMap;
//Dynamic Doc End
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
use Config;
use App\SchemeCapacity;
use App\Scheme;
use Validator;
use Carbon\Carbon;
use App\BankDetails;
use App\Helpers\Helper;
use App\MapLavel;
use App\BenDocs;
use App\AcceptRejectInfo;
use App\Traits\TraitCasteCertificateValidate;
use App\Traits\TraitLifeCertificateValidate;
use App\Traits\TraitAadharValidate;
use App\Helpers\AuthChecker;

class OAPFarmerformController extends Controller
{
    use TraitCasteCertificateValidate;
    use TraitLifeCertificateValidate;
    use TraitAadharValidate;

  public function __construct()
  {
    $this->middleware('auth');
    date_default_timezone_set('Asia/Kolkata');
    $this->scheme_id = 13;
    $this->base_dob_chk_date = '2020-01-01';
    $this->max_dob = '1960-01-01';
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    //return redirect("/")->with('error', 'Data entry temporary suspended.');
    $is_active = 0;
    $scheme_id = 13;
    $is_active = 0;


    // $base_url=url('/');
    // echo $base_url.'/images/';exit;        

    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $request->session()->put('level', $roleObj['mapping_level']);
        $request->session()->put('distCode', $roleObj['district_code']);
        if ($roleObj['is_urban'] == 1) {
          $request->session()->put('blockCode', $roleObj['urban_body_code']);
        } else {
          $request->session()->put('blockCode', $roleObj['taluka_code']);
        }
        break;
      }
    }
    if ($is_active == 1) {
      $scheme_capacity_arr = array();
      $distCode = $request->session()->get('distCode');
      $scheme_capacity_arr = $this->getCapacityBrief($scheme_id, $distCode);
      // if ($scheme_capacity_arr['visible'] == 1) {
      //     if (($scheme_capacity_arr['approved'] + $scheme_capacity_arr['pending']) >= $scheme_capacity_arr['capacity']) {
      //         $errorMsg = "Sum of approved(" . $scheme_capacity_arr['approved'] . ")  and pending(" . $scheme_capacity_arr['pending'] . ") applications  exceeds the quota " . $scheme_capacity_arr['capacity'];
      //         return redirect("/")->with('error', $errorMsg);
      //         // return redirect()->back()->withErrors("Total no of applications ".$scheme_capacity_arr['total_data']." exceeds quota ".$scheme_capacity_arr['capacity']);
      //     }
      // }

      $districts = District::all();
      //$doc_list_man = array();
      //$doc_list_opt = array();
      //$doc_list_man_group = array();

      //Document Dynamic
      $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->first()->toArray();
      if (!empty($doc_id_list['doc_list_man']))
        $doc_list_man = DocumentType::select('id', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->whereIn("id", json_decode($doc_id_list['doc_list_man']))->get()->toArray();
      else
        $doc_list_man = array();
      if (!empty($doc_id_list['doc_list_opt']))
        $doc_list_opt = DocumentType::select('id', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->whereIn("id", json_decode($doc_id_list['doc_list_opt']))->get()->toArray();
      else
        $doc_list_opt = array();
      if (!empty($doc_id_list['doc_list_man_group']))
        $doc_list_man_group = json_decode($doc_id_list['doc_list_man_group']);
      else
        $doc_list_man_group = array();

      $document_msg = "";
      if (!empty($doc_list_man_group)) {
        $doc_list = array_merge($doc_list_man, $doc_list_opt);
        $all_doc_id = array();
        foreach ($doc_list as $mDoc) {
          array_push($all_doc_id, $mDoc['id']);
        }
        // dd($all_doc_id);
        if (count($doc_list)) {
          foreach ($doc_list_man_group as $man_group) {
            $document_msg .= '<div  class="form-group col-md-12" >';
            $heading_msg = "At least one document must be uploaded for ";
            $doucument_group_name = $this->getGroupName($man_group);
            $heading_msg .= '<span style="color:red;font-weight:bold">' . $doucument_group_name . '</span>';
            $document_msg .= "<p style='font-weight:bold;font-size:17px;'>" . $heading_msg . " </p>";
            $document_msg .= "<ul>";
            $results = DB::select("SELECT doc_name FROM m_attached_doc where id IN (" . implode(',', $all_doc_id) . ") and $man_group =any(doucument_group)");
            $results = json_decode(json_encode($results), true);


            //dd($results);
            if (count($results) > 0) {
              $i = 0;
              foreach ($results as $requiredmsg) {

                $document_msg .= "<li style='font-weight:bold;'>" . $requiredmsg['doc_name'] . "</li>";
                $i++;
              }
            }


            $document_msg .= "</ul>";
            $document_msg .= "</div>";
          }
        } else
          $document_msg = "";
      } else
        $document_msg = "";






      $doc_profile_image = DocumentType::get()
        ->where("is_profile_pic", true)->first();

      $doc_profile_image_id = 999;
      if ($doc_profile_image) {
        $doc_profile_image_id = $doc_profile_image->id;
      }
      //echo "<pre>";print_r($doc_profile_image_id); echo "</pre>";die();  
      return view('farmer/pension_details', [
        'districts' => $districts,
        'scheme_id' => $scheme_id, 'document_msg' => $document_msg, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id,
        'scheme_capacity_arr' => $scheme_capacity_arr
      ]);
    }
    if ($is_active == 0) {
      return redirect("/")->with('success', 'User Disabled');
    } else {
      return redirect("/")->with('success', 'User Disabled');
    }
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    // return redirect("/")->with('error', 'Data entry temporary suspended.');
    $user_id = AuthChecker::getUserId();
    $users = User::find($user_id);
    //  $server_ip =$_SERVER['SERVER_ADDR'];
    $base_url = url('/');
    $uploaded_doc = array();
    $destinationPath = storage_path('app/keep_farmer/');
    $scheme_id = $request->scheme_id;

    $scheme_capacity_arr = array();
    $distCode = $request->session()->get('distCode');
    $scheme_capacity_arr = $this->getCapacityBrief($scheme_id, $distCode);
    if ($scheme_capacity_arr['visible'] == 1) {
      if (($scheme_capacity_arr['approved'] + $scheme_capacity_arr['pending']) >= $scheme_capacity_arr['capacity']) {
        $errorMsg = "Sum of approved(" . $scheme_capacity_arr['approved'] . ")  and pending(" . $scheme_capacity_arr['pending'] . ") applications  exceeds quota " . $scheme_capacity_arr['capacity'];
        return redirect("/")->with('error', $errorMsg);
        // return redirect()->back()->withErrors("Total no of applications ".$scheme_capacity_arr['total_data']." exceeds quota ".$scheme_capacity_arr['capacity']);
      }
    }

    $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->get();
    $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
    $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
    $doc_list = array_merge($doc_list_man, $doc_list_opt);


    $isValidarr = $this->validateInput($request,  $scheme_id, 1);
    if ($isValidarr['is_valid'] == false) {
      return back()->with('errors', $isValidarr['errors'])->withInput(Input::all());
    }
    $doc_list_man_group_upload = array();
    $doc_list_man_group_db = array();
    // print_r( $doc_id_list[0]['doc_list_man_group']);die;
    if (($doc_id_list[0]['doc_list_man_group']) != '' &&  ($doc_id_list[0]['doc_list_man_group'] != 'null') && ($doc_id_list[0]['doc_list_man_group']) != null) {
      $doc_list_man_group_db = json_decode($doc_id_list[0]['doc_list_man_group']);
    }
    foreach ($doc_list as $doc) {
      if ($request->hasFile('doc_' . $doc)) {
        $doucument_group_id = DocumentType::select('doucument_group')->where('id', $doc)->first();
        if ($doucument_group_id['doucument_group'] != '') {
          $arr = array();
          $postgresStr = trim($doucument_group_id['doucument_group'], "{}");
          $elmts = explode(",", $postgresStr);
          foreach ($elmts as $myarr) {
            if (!in_array($myarr, $doc_list_man_group_upload)) {
              array_push($doc_list_man_group_upload, $myarr);
            }
          }
        }
      }
    }
    if (count($doc_list_man_group_db) > 0) {
      $errors = array();
      $i = 0;
      foreach ($doc_list_man_group_db as $group) {
        $doucument_group_name = $this->getGroupName($group);
        if (!in_array($group, $doc_list_man_group_upload)) {
          $errorMsg = "At least one document must be uploaded for " . $doucument_group_name;
          array_push($errors, $errorMsg);
        }
      }
      if (count($errors) > 0)
        //return back()->withErrors($errors)->withInput();
        return back()->with('errors', $errors)->withInput(Input::all());
    }
    if (!empty($request->aadhar_no)) {
      if ($this->isAadharValid(trim($request->aadhar_no)) == false) {
        $errors = array();
        $errorMsg = "Aadhaar Number Invalid";
        array_push($errors, $errorMsg);
        //return back()->withErrors($errors)->withInput();
        return back()->with('errors', $errors)->withInput(Input::all());
      }
    }
    
    $ifsc = trim($request->bank_ifsc_code);
    $bank_account_number = trim($request->bank_account_number);
    $bank_branch = trim($request->bank_branch);
    $name_of_bank = trim($request->name_of_bank);
    $row_count_bank = BankDetails::whereraw("trim(ifsc)='$ifsc'")->whereraw("trim(branch)='$bank_branch'")->where('is_active',1)->whereraw("trim(bank)='$name_of_bank'")->count();
   // $bank_details = BankDetails::whereraw("trim(ifsc)='$ifsc'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
    $bank_details = BankDetails::where('ifsc', trim($ifsc))->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
    $new_bank_code=$bank_details->bank_code;
    if ($row_count_bank == 0) {
      $errors = array();
      $errorMsg = "Bank IFSC and Bank Name Not Match!";
      array_push($errors, $errorMsg);
      //return back()->withErrors($errors)->withInput();
      return back()->with('errors', $errors)->withInput(Input::all());
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
    } else {
        $schema = "pension"; 
    
    }
    /*
    if(!empty($bank_account_number) && !empty($ifsc)){
     $bank_count = DB::table($schema . '.ben_bank_account_no_unique')->where('bank_code',$bank_account_number)->where('bank_ifsc', $ifsc)->count('bank_code');
     if($bank_count>0){  
      $is_error=1;  
      array_push($errormsg,'Bank A/C Already Exist!');
     } 
  } */  
  if(!preg_match('/^[0-9]{10}+$/',$request->mobile_no)) {
    $errors = array();
    $errorMsg = "Mobile Number Invalid";
    array_push($errors, $errorMsg);
    return back()->with('errors', $errors)->withInput(Input::all());
 }
 if($request->mobile_no<1000000000) {
    $errors = array();
    $errorMsg = "Mobile Number Invalid";
    array_push($errors, $errorMsg);
    return back()->with('errors', $errors)->withInput(Input::all());
 }       
  $errormsg=array();
  if(!empty($bank_account_number) && !empty($ifsc)){
   $bank_count = DB::table($schema . '.beneficiaries')->where('scheme_id',$scheme_id)->where('is_clean', '!=', 10)->where('bank_code',$bank_account_number)->count('bank_code');
   if($bank_count>0){  
    $is_error=1;  
    array_push($errormsg,'Bank A/C Already Exist!');
   } 
}          
if(!empty($request->aadhar_no)){
    $aadhar_count = DB::table($schema . '.beneficiaries')->where('scheme_id',$scheme_id)->where('is_clean', '!=', 10)->where('aadhar_no',trim($request->aadhar_no))->count('aadhar_no');
    if($aadhar_count>0){  
     $is_error=1;  
     array_push($errormsg,'Aadhaar Number Already Exist! Please try different.');     
    } 
 } 
 if(!empty($request->mobile_no)){
    $mobile_count = DB::table($schema . '.beneficiaries')->where('scheme_id',$scheme_id)->where('is_clean', '!=', 10)->where('mobile_no',$request->mobile_no)->count('mobile_no');
    if($mobile_count>0){  
     $is_error=1;  
     array_push($errormsg,'Mobile Number Already Exist! Please try different.');    
    } 
 } 
 if(count($errormsg)>0){
  return redirect("oap-farmer")->withInput(Input::all())->with('errors',$errormsg);
   
}
   

 
    $c_time=date('Y-m-d H:i:s');
    $pension_details = new BenEntry();
    $pension_details->entry_datetime = $c_time;
    $pension_details->ip_address=$request->ip();

    //Document Dynamic
    $upload_file=array();
        $i=0;
        $doc_master=DocumentType::get();
       // dd($request->file);
        foreach ($doc_list as $doc) {
            if ($request->hasFile('doc_' . $doc)) {
            $doc_file = $request->file('doc_' . $doc);
            $img_data = file_get_contents($doc_file);
            $u_extension = $doc_file->getClientOriginalExtension();
            $mime_type = $doc_file->getMimeType();
            $doc_type_name =$doc_master->where('id', $doc)->first() ;
            if(strtolower($mime_type)=='image/jpeg'){
              if($u_extension=='jpg' || $u_extension=='jpeg'){
                $extension=$u_extension;
            }
            else{
            $errors = array();
            $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
            array_push($errors, $errorMsg);
            return back()->with('errors', $errors)->withInput(Input::all());  
            }
            }
            else if(strtolower($mime_type)=='image/png'){
                $extension='png';
            }else if(strtolower($mime_type)=='image/gif'){
                $extension='gif';
            }else if(strtolower($mime_type)=='application/pdf'){
                $extension='pdf';
            }
            else{
                $errors = array();
                $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
                array_push($errors, $errorMsg);
                return back()->with('errors', $errors)->withInput(Input::all());  
            }
            if($u_extension!=$extension){
                $errors = array();
                $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
                array_push($errors, $errorMsg);
                return back()->with('errors', $errors)->withInput(Input::all());  
            }
            $base64 = base64_encode($img_data);
            $upload_file[$i]['created_by_dist_code']=$request->session()->get('distCode');
            $upload_file[$i]['created_by_local_body_code']=$request->session()->get('blockCode');
            $upload_file[$i]['document_type']=$doc;
            $upload_file[$i]['scheme_id']=$scheme_id;
            $upload_file[$i]['created_by_level']=$request->session()->get('level');
            $upload_file[$i]['created_at']=$c_time;
            $upload_file[$i]['created_by']=$user_id;
            $upload_file[$i]['ip_address']=$request->ip();
            $upload_file[$i]['attched_document']=$base64;
            $upload_file[$i]['document_mime_type']=$mime_type;
            $upload_file[$i]['document_extension']=$extension;
            if(!empty($doc_type_name)){
             $upload_file[$i]['doc_type_name'] = $doc_type_name->doc_name;
            }
            $i++;
           }
        }
    //Document Dynamic End

    if ($request->urban_code == 1) {
      $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
      $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();

      $pension_details->block_ulb_name = $block_ulb->urban_body_name;
      $pension_details->gp_ward_name   = $gp_ward->urban_body_ward_name;
    } else {
      $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
      $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();

      $pension_details->block_ulb_name = $block_ulb->block_name;
      $pension_details->gp_ward_name   = $gp_ward->gram_panchyat_name;
    }
    if(!empty($request->asmb_cons)){
    $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
    $assembly_name = $assembly->ac_name;
    }
    if ($request->receive_pension != "") {
      $receive_pension = implode(',', $request->receive_pension);
      $pension_details->receive_pension    = $receive_pension;
    }

    if ($request->social_security_pension != "") {
      $social_security_pension = implode(',', $request->social_security_pension);
      $pension_details->social_security_pension   = $social_security_pension;
    }

    // $pension_details->entry_type            = trim($request->entry_type);

    // if(trim($request->entry_type) =="Form through Duare Sarkar camp")
    // {
    //     $pension_details->ds_registration_no   = trim($request->duare_sarkar_reg_no);
    //     $pension_details->ds_date              = trim($request->duare_sarkar_date);

    // }

    if (trim($request->f_land_array) != '') {

      $f_land_array   = json_decode($request->f_land_array, true);
      $f_land_array   = json_encode($f_land_array);
    } else {

      $f_land_array   = null;
    }

    if (trim($request->f_member_array) != '') {
      $f_member_array = json_decode($request->f_member_array, true);
      $f_member_array = json_encode($f_member_array);
    } else {
      $f_member_array   = null;
    }

    if (trim($request->dob) != '') {
      $request_dob = $request->dob;
    } else {
      $request_dob   = null;
    }

    if (trim($request->mobile_no) != '') {
      $request_mobile_no   = $request->mobile_no;
    } else {
      $request_mobile_no   = null;
    }


    $pension_details->land_json             = $f_land_array;
    $pension_details->member_json           = $f_member_array;

    $pension_details->ben_fname             = trim($request->first_name);
    $pension_details->ben_mname             = trim($request->middle_name);
    $pension_details->ben_lname             = trim($request->last_name);
    $pension_details->gender                = trim($request->gender);
    $pension_details->dob                   = $request_dob;
    $pension_details->ben_age               = trim($request->txt_age);

    $pension_details->father_fname          = trim($request->father_first_name);
    $pension_details->father_mname          = trim($request->father_middle_name);
    $pension_details->father_lname          = trim($request->father_last_name);
    $pension_details->mother_fname          = trim($request->mother_first_name);
    $pension_details->mother_mname          = trim($request->mother_middle_name);
    $pension_details->mother_lname          = trim($request->mother_last_name);
    $pension_details->caste                 = trim($request->caste_category);
    //  $pension_details->fisherman_comm=$request->fisherman_comm;
    $pension_details->marital_status        = trim($request->marital_status);
    $pension_details->mothly_income         = trim($request->monthly_income);

    $pension_details->spouse_fname          = trim($request->spouse_first_name);
    $pension_details->spouse_mname          = trim($request->spouse_middle_name);
    $pension_details->spouse_lname          = trim($request->spouse_last_name);

    $pension_details->ration_card_cat       = trim($request->ration_card_cat);
    $pension_details->ration_card_no        = trim($request->ration_card_no);
    $pension_details->ahl_tin               = trim($request->ahl_tin);
    $pension_details->aadhar_no             = trim($request->aadhar_no);
    $pension_details->epic_voter_id         = trim($request->epic_voter_id);
    $pension_details->pan_no                = trim($request->pan_no);
    $pension_details->bpl_seq_no            = trim($request->bpl_seq_no);
    $pension_details->bpl_id_no             = trim($request->bpl_id_no);
    $pension_details->bpl_total_score       = $request->bpl_total_score;

    $pension_details->dist_code             = trim($request->district);
    $pension_details->rural_urban_id        = trim($request->urban_code);
    if(!empty($request->asmb_cons)){
     $pension_details->assembly_code         = trim($request->asmb_cons);
     $pension_details->assembly_name         = trim($assembly_name);
    }
    $pension_details->police_station        = trim($request->police_station);
    $pension_details->block_ulb_code        = trim($request->block);
    $pension_details->gp_ward_code          = trim($request->gp_ward);
    $pension_details->village_town_city     = trim($request->village);
    $pension_details->house_premise_no      = trim($request->house);
    $pension_details->post_office           = trim($request->post_office);
    $pension_details->pincode               = trim($request->pin_code);
    $pension_details->residency_period      = trim($request->residency_period);
    $pension_details->mobile_no             = $request_mobile_no;
    $pension_details->email                 = trim($request->email);

    $pension_details->bank_name             = trim($request->name_of_bank);
    $pension_details->branch_name           = trim($request->bank_branch);
    $pension_details->bank_code             = trim($request->bank_account_number);
    $pension_details->bank_ifsc             = trim($request->bank_ifsc_code);
    $pension_details->npci_bank_code        = trim($new_bank_code); 

    $pension_details->cultivation_by_applicant     = trim($request->cultivation_by_applicant);
    $pension_details->source_income                = trim($request->source_income);
    $pension_details->any_other_benefitis          = trim($request->any_other_benefitis);

    $pension_details->nominate_name         = trim($request->nominate_name);
    $pension_details->nominate_address      = trim($request->nominate_address);
    $pension_details->nominate_relationship = trim($request->nominate_relationship);

    $pension_details->created_by                        = Auth::user()->id;
    $pension_details->created_by_level                  = $request->session()->get('level');
    $pension_details->created_by_dist_code              = $request->session()->get('distCode');
    $pension_details->created_by_local_body_code        = $request->session()->get('blockCode');
    $pension_details->scheme_id                         =  $request->scheme_id;
    $pension_details->av_status                         =  $request->av_status;
    $pension_details->receiving_pension_other_source_1  =  trim($request->receiving_pension_other_source_1);
    $pension_details->receiving_pension_other_source_2  =  trim($request->receiving_pension_other_source_2);
    DB::connection('pgsql5')->beginTransaction();
    DB::connection('pgsql_encwrite')->beginTransaction();
    $is_saved = 0;
    try {
         
      $is_saved = $pension_details->save();
      $beneficiary_id = $pension_details->id;
      if($beneficiary_id){
          foreach($upload_file as $key => $csm)
          {
           $upload_file[$key]['beneficiary_id'] = $beneficiary_id;
          }
          $doc_inserted = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($upload_file);

          if( $doc_inserted ){
              DB::connection('pgsql5')->commit();
              DB::connection('pgsql_encwrite')->commit();
              $ben_fullname=trim($request->first_name) . ' ' . trim($request->middle_name) . ' ' . trim($request->last_name);
                $this->bioauthcheckInsert($request->session()->get('distCode'),$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$request->session()->get('blockCode'),$user_id);
                if(($request->caste_category=='SC' || $request->caste_category=='ST') && !empty($request->caste_certificate_no)){
                  $this->casteInfoCheckInsert($request->session()->get('distCode'),$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->caste_certificate_no),$request->session()->get('blockCode'),$user_id);
                }
                
                $this->RationcheckInsert($request->session()->get('distCode'),$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$request->session()->get('blockCode'),$user_id);
                $ben_details=DB::table($schema . '.beneficiaries')->where('scheme_id',$scheme_id)->where('id',$beneficiary_id)->first();
                if($ben_details){
                  $caste_certificate_checked=$ben_details->caste_certificate_checked;
                  $caste_certificate_validation_message=$ben_details->caste_certificate_validation_message;
                  $caste_certificate_check_lastdatetime=$ben_details->caste_certificate_check_lastdatetime;
                  $caste_matched_with_certificate_no=$ben_details->caste_matched_with_certificate_no;
                  $life_certificate_checked=$ben_details->life_certificate_checked;
                  $life_certificate_pass=$ben_details->life_certificate_pass;
                  $life_certificate_lastdatetime=$ben_details->life_certificate_lastdatetime;
                  $last_biometric=$ben_details->last_biometric;
                  $aadhaar_no_checked=$ben_details->aadhaar_no_checked;
                  $aadhaar_no_checked_lastdatetime=$ben_details->aadhaar_no_checked_lastdatetime;
                  $aadhaar_no_checked_pass=$ben_details->aadhaar_no_checked_pass;
                  $aadhaar_no_validation_msg=$ben_details->aadhaar_no_validation_msg;
             
              return redirect("oap-farmer")->with('success', 'Application Submitted Successfully')
                  ->with('id',  $beneficiary_id)->with('caste_certificate_checked',  $caste_certificate_checked)
                  ->with('caste_certificate_validation_message',$caste_certificate_validation_message)
                  ->with('caste_certificate_check_lastdatetime',$caste_certificate_check_lastdatetime)
                  ->with('caste_matched_with_certificate_no',$caste_matched_with_certificate_no)
                  ->with('life_certificate_checked',$life_certificate_checked)
                  ->with('life_certificate_pass',$life_certificate_pass)
                  ->with('life_certificate_lastdatetime',$life_certificate_lastdatetime)
                  ->with('last_biometric',$last_biometric)
                  ->with('aadhaar_no_checked',$aadhaar_no_checked)
                  ->with('aadhaar_no_checked_lastdatetime',$aadhaar_no_checked_lastdatetime)
                  ->with('aadhaar_no_checked_pass',$aadhaar_no_checked_pass)
                  ->with('aadhaar_no_validation_msg',$aadhaar_no_validation_msg);
                }
          }
          else{
              $error_found=1;
          }
      }
      else{
          $error_found=1;
          
      }
      if($error_found){
          DB::connection('pgsql5')->rollback();
          DB::connection('pgsql_encwrite')->rollback();
         return redirect("oap-farmer")->withInput(Input::all())->with('errors', array('Some error.Please try again'));
          
      }
     } catch (\Exception $e) {
     dd($e);
     DB::connection('pgsql5')->rollback();
     DB::connection('pgsql_encwrite')->rollback();
     return redirect("oap-farmer")->withInput(Input::all())->with('errors', array('Some error.Please try again'));
     }
    
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */



  public function applicationupdate(Request $request)
  {
    //return redirect("/")->with('error', 'Data entry temporary suspended.');


    $base_url = url('/');
    $id = $request->id;
    $scheme_id = (int) $request->scheme_id;
    // dd($scheme_id);
    $designation_id_old = Auth::user()->designation_id_old;
    if (!is_int($scheme_id)) {
      return redirect("/")->with('error', 'Scheme Code Not Valid');
    }
    if (!is_numeric($id)) {
      return redirect("/")->with('error', 'Applicant ID Not Valid');
    }
    $created_by = Auth::user()->id;

    $is_active = 0;
    $mapping_level = NULL;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $mapping_level = $roleObj['mapping_level'];
        $distCode = $roleObj['district_code'];
        $is_urban = $roleObj['is_urban'];
        if ($roleObj['is_urban'] == 1) {
          $blockCode = $roleObj['urban_body_code'];
        } else {
          $blockCode = $roleObj['taluka_code'];
        }
        break;
      }
    }
    if ($is_active == 0) {
      return redirect("/")->with('error', 'User Disabled');
    }
    $query = PensionOAPFarmer::where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id]);
    if ($designation_id_old == 'Verifier') {
      $query = $query->whereNull('next_level_role_id');
    } else if ($designation_id_old == 'Approver') {
      $query = $query->where('is_verified',1)->where('is_approved',0)->where('is_rejected',0);
    } else {
      $query = $query->whereNull('next_level_role_id');
    }
    $row = $query->first();

    if (empty($row->scheme_id)) {
      return redirect("/")->with('error', 'Not Allowed');
    }
    $isValidarr = $this->validateInput($request,  $scheme_id, 2);
    if ($isValidarr['is_valid'] == false) {
      //dd(withInput());
      return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $isValidarr['errors']);
      //return back()->withErrors($isValidarr['errors'])->withInput();
    }
    $scheme_row = Scheme::where('id', $scheme_id)->first();
    if (empty($scheme_row)) {
      return redirect("/")->with('error', 'User Disabled');
    }
    $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
    if (!empty($request->mobile_no)) {
        $mobile_count = PensionOAPFarmer::where('mobile_no', trim($request->mobile_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
       // dd( $mobile_count);
        if ($mobile_count > 0) {
            $errors = array();
            $errorMsg = "Mobile Number Already Exist! Please try different.";
            array_push($errors, $errorMsg);
            return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors',  $errors);
        }
    }
    //--------- Duplicate bank A/C check---------- //
    $bankCount = PensionOAPFarmer::whereRaw("trim(bank_code)=trim(" . "'" . $request->bank_account_number . "'" . ")")->where('id', '!=', $id)
        ->whereRaw("(" . $check_condition_str . ")")
        ->count('id');
    
    if ($bankCount > 0) {
        $errors = array();
        $errorMsg = "Bank A/C Already Exist!";
        array_push($errors, $errorMsg);
        return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors',  $errors);
    }
    $count = PensionOAPFarmer::where('aadhar_no', trim($request->aadhar_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
    if ($count > 0) {
        $request->session()->put('dupAadhaarCheck', trim($request->aadhar_no));
        $errors = array();
        $errorMsg = "Aadhaar Number Already Exist! Please try different.";
        array_push($errors, $errorMsg);
        return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('dupAadhaar', 1)->with('errors',  $errors);
    }
    $ifsc = trim($request->bank_ifsc_code);
    $bank_branch = trim($request->bank_branch);
    $name_of_bank = trim($request->name_of_bank);
    $row_count_bank = BankDetails::whereraw("trim(branch)='$bank_branch'")->whereraw("trim(ifsc)='$ifsc'")->where('is_active',1)->whereraw("trim(bank)='$name_of_bank'")->count();
   // $bank_details = BankDetails::whereraw("trim(ifsc)='$ifsc'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
    $bank_details = BankDetails::where('ifsc', trim($ifsc))->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
    $new_bank_code=$bank_details->bank_code;
    if ($row_count_bank == 0) {
        $errors = array();
        $errorMsg = "Bank IFSC and Bank Name Not Match!";
        array_push($errors, $errorMsg);
        return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors',  $errors);
    }
    $scheme_schema = $scheme_row->short_code;
    $social_security_pension = "";
    $receive_pension = "";
    if ($request->receive_pension != "") {
      $receive_pension = implode(',', $request->receive_pension);
    }

    if ($request->social_security_pension != "") {
      $social_security_pension = implode(',', $request->social_security_pension);
    }

    $block_ulb_name = "";
    $gp_ward_name = "";

    if ($request->urban_code == 1) {
      $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
      $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();


      $block_ulb_name = $block_ulb->urban_body_name;
      $gp_ward_name   = $gp_ward->urban_body_ward_name;
    } else {
      $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
      $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();

      $block_ulb_name = $block_ulb->block_name;
      $gp_ward_name   = $gp_ward->gram_panchyat_name;
    }
    if(!empty($request->asmb_cons)){
    $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
    $assembly_name = $assembly->ac_name;
    }

    if (trim($request->marital_status) != "Married") {
      $request->spouse_first_name = "";
      $request->spouse_middle_name = "";
      $request->spouse_last_name = "";
    }

    // echo "<pre>"; print_r($request->input());
    // $duare_sarkar_reg_no   = null;
    // $duare_sarkar_date     = null;
    // if(trim($request->entry_type) =="Form through Duare Sarkar camp")
    // {
    //     $duare_sarkar_reg_no   = trim($request->duare_sarkar_reg_no);
    //     $duare_sarkar_date     = trim($request->duare_sarkar_date);
    // }
    // else
    // {
    //     $duare_sarkar_reg_no   = null;
    //     $duare_sarkar_date     = null;
    // }



    if (trim($request->f_land_array) != '') {
      $f_land_array   = json_decode($request->f_land_array, true);
      $f_land_array   = json_encode($f_land_array);
    } else {
      $f_land_array   = null;
    }

    if (trim($request->f_member_array) != '') {
      $f_member_array = json_decode($request->f_member_array, true);
      $f_member_array = json_encode($f_member_array);
    } else {
      $f_member_array   = null;
    }

    if (trim($request->dob) != '') {

      $request_dob = $request->dob;
    } else {

      $request_dob   = null;
    }

    if (trim($request->mobile_no) != '') {
      $request_mobile_no   = $request->mobile_no;
    } else {
      $request_mobile_no   = null;
    }
    $c_time = date('Y-m-d H:i:s', time());
    $input = [
      // 'entry_type'         => trim($request->entry_type),
      // 'ds_registration_no' => $duare_sarkar_reg_no,
      // 'ds_date'            => $duare_sarkar_date,
      'land_json'   =>  $f_land_array,
      'member_json' =>  $f_member_array,

      'ben_fname' => trim($request->first_name),
      'ben_mname' => trim($request->middle_name),
      'ben_lname' => trim($request->last_name),
      'gender' => trim($request->gender),
      'dob' => $request_dob,
      'ben_age' => trim($request->txt_age),

      'father_fname' => trim($request->father_first_name),
      'father_mname' => trim($request->father_middle_name),
      'father_lname' => trim($request->father_last_name),
      'mother_fname' => trim($request->mother_first_name),
      'mother_mname' => trim($request->mother_middle_name),
      'mother_lname' => trim($request->mother_last_name),
      'caste' => trim($request->caste_category),
      'marital_status' => trim($request->marital_status),
      'spouse_fname' => trim($request->spouse_first_name),
      'spouse_mname' => trim($request->spouse_middle_name),
      'spouse_lname' => trim($request->spouse_last_name),
      //'bpl_y_n' =>$request->if_bpl,
      'bpl_seq_no' => trim($request->bpl_seq_no),
      'bpl_id_no' => trim($request->bpl_id_no),
      'bpl_total_score' => $request->bpl_total_score,
      'mothly_income' => trim($request->monthly_income),

      'receive_pension' => trim($receive_pension),
      'social_security_pension' => trim($social_security_pension),

      'ration_card_cat' => trim($request->ration_card_cat),
      'ration_card_no'  => trim($request->ration_card_no),
      'ahl_tin'  => trim($request->ahl_tin),
      'aadhar_no'  => trim($request->aadhar_no),
      'epic_voter_id'  => trim($request->epic_voter_id),
      'pan_no'  => trim($request->pan_no),



      'dist_code' => trim($request->district),
     // 'assembly_code'  => trim($request->asmb_cons),
     // 'assembly_name' => trim($assembly_name),
      'rural_urban_id' => trim($request->urban_code),
      'police_station'  => trim($request->police_station),
      'block_ulb_code'  => trim($request->block),
      'block_ulb_name' => trim($block_ulb_name),
      'gp_ward_code' => trim($request->gp_ward),
      'gp_ward_name' => trim($gp_ward_name),
      'village_town_city'  => trim($request->village),
      'house_premise_no'  => trim($request->house),
      'post_office'  => trim($request->post_office),
      'pincode' => trim($request->pin_code),
      'residency_period' => trim($request->residency_period),
      'mobile_no'  => $request_mobile_no,
      'email' => trim($request->email),



      'bank_name'  => trim($request->name_of_bank),
      'branch_name'   => trim($request->bank_branch),
      'bank_code'    => trim($request->bank_account_number),
      'bank_ifsc'   => trim($request->bank_ifsc_code),
      'npci_bank_code'  =>  trim($new_bank_code), 

      'cultivation_by_applicant'   => trim($request->cultivation_by_applicant),
      'source_income'              => trim($request->source_income),
      'any_other_benefitis'        => trim($request->any_other_benefitis),

      'nominate_name' => trim($request->nominate_name),
      'nominate_address' => trim($request->nominate_address),
      'nominate_relationship' => trim($request->nominate_relationship),
      'av_status' => trim($request->av_status),
      'receiving_pension_other_source_1' => trim($request->receiving_pension_other_source_1),
      'receiving_pension_other_source_2' => trim($request->receiving_pension_other_source_2),
      'created_by' => $created_by,
      'created_by_level' => $mapping_level,
      'updated_at' => $c_time,
      'dup_bank' => 0,
      'dup_aadhar' => 0,
      'dup_mobile' => 0,
      'no_aadhar' => 0,
      'no_mobile' => 0,
      'is_reverted' => NULL
    ];
    if(!empty($request->asmb_cons)){
      $input['assembly_code']=trim($request->asmb_cons);
      $input['assembly_name']=trim($assembly_name);
     // 'assembly_name' => trim($assembly_name),
    }
    else{
      $input['assembly_code']=NULL;
      $input['assembly_name']='';
    }

    //echo "<pre>"; print_r($input);

    $pr1 = "";
    $uploaded_doc = array();
    $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
    $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
    $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
    $doc_list = array_merge($doc_list_man, $doc_list_opt);

    $doc_master=DocumentType::get();
    $encolserdata = BenDocs::where('scheme_id',$scheme_id)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->get();
    $upload_file=array();
    $upload_file_arch=array();
    $delete_array=array();
    $i=0;
    $j=0;
    $c_time=date('Y-m-d H:i:s');
    $user_id = AuthChecker::getUserId();

    foreach ($doc_list as $doc) {
        if ($request->hasFile('doc_' . $doc)) {
        $doc_file = $request->file('doc_' . $doc);
        $img_data = file_get_contents($doc_file);
        $u_extension = $doc_file->getClientOriginalExtension();
        $mime_type = $doc_file->getMimeType();
        $doc_type_name =$doc_master->where('id', $doc)->first() ;
        if(strtolower($mime_type)=='image/jpeg'){
            if($u_extension=='jpg' || $u_extension=='jpeg'){
                $extension=$u_extension;
            }
            else{
            $errors = array();
            $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
            array_push($errors, $errorMsg);
            return back()->with('errors', $errors)->withInput(Input::all());  
            }
        }
        else if(strtolower($mime_type)=='image/png'){
            $extension='png';
        }else if(strtolower($mime_type)=='image/gif'){
            $extension='gif';
        }else if(strtolower($mime_type)=='application/pdf'){
            $extension='pdf';
        }
        else{
            $errors = array();
            $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
            array_push($errors, $errorMsg);
            return back()->with('errors', $errors)->withInput(Input::all());  
        }
        if($u_extension!=$extension){
            $errors = array();
            $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
            array_push($errors, $errorMsg);
            return back()->with('errors', $errors)->withInput(Input::all());  
        }
        $base64 = base64_encode($img_data);
        $upload_file[$i]['beneficiary_id']=$request->id;
        $upload_file[$i]['created_by_dist_code']=$distCode;
        $upload_file[$i]['created_by_local_body_code']=$blockCode;
        $upload_file[$i]['document_type']=$doc;
        $upload_file[$i]['scheme_id']=$scheme_id;
        $upload_file[$i]['created_by_level']=$mapping_level;
        $upload_file[$i]['created_at']=$c_time;
        $upload_file[$i]['created_by']=$user_id;
        $upload_file[$i]['ip_address']=$request->ip();
        $upload_file[$i]['attched_document']=$base64;
        $upload_file[$i]['document_mime_type']=$mime_type;
        $upload_file[$i]['document_extension']=$extension;
        if(!empty($doc_type_name)){
         $upload_file[$i]['doc_type_name'] = $doc_type_name->doc_name;
        }
        $i++;
        $doc_already =$encolserdata->where('document_type',$doc)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->first();
        if(!empty($doc_already)){
            array_push($delete_array,$doc);
            $upload_file_arch[$j]['beneficiary_id']=$request->id;
            $upload_file_arch[$j]['created_by_dist_code']=$doc_already->created_by_dist_code;
            $upload_file_arch[$j]['created_by_local_body_code']=$doc_already->created_by_local_body_code;
            $upload_file_arch[$j]['document_type']=$doc_already->document_type;
            $upload_file_arch[$j]['scheme_id']=$doc_already->scheme_id;
            $upload_file_arch[$j]['created_by_level']=$doc_already->created_by_level;
            $upload_file_arch[$j]['created_at']=$doc_already->created_at;
            $upload_file_arch[$j]['created_by']=$doc_already->created_by;
            $upload_file_arch[$j]['ip_address']=$doc_already->ip_address;
            $upload_file_arch[$j]['attched_document']=$doc_already->attched_document;
            $upload_file_arch[$j]['document_mime_type']=$doc_already->document_mime_type;
            $upload_file_arch[$j]['document_extension']=$doc_already->document_extension;
            $j++;
        }
       }
    }
    DB::beginTransaction();
    DB::connection('pgsql16')->beginTransaction();
    DB::connection('pgsql_encwrite')->beginTransaction();

    try {
     // dd($id);
      $arch_status= DB::statement("INSERT INTO farmer.arc_beneficiary(id, 
      dist_code, ben_fname, ben_mname, ben_lname, gender, dob, ben_age, 
     caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
     mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, 
     ration_card_no, ahl_tin, aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, 
     bpl_total_score, dist_name, assembly_code, assembly_name, police_station, block_ulb_code, 
     block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, village_town_city, house_premise_no, 
     post_office, pincode, residency_period, mobile_no, email, bank_name, 
     branch_name, bank_old_code, bank_ifsc, created_at, updated_at, created_by, created_by_level, 
     created_by_dist_code, created_by_local_body_code, scheme_id, type_disability, 
     percentage_disability, certifying_auth, next_level_role_id, comments, nominate_name, 
     nominate_address, nominate_relationship, receive_pension, social_security_pension, 
     ration_card_cat, rural_urban_id, lot_generated, 
     bank_edited, bank_code, payment_count, last_paid_yymm, 
     av_status, legacy_import, 
     receiving_pension_other_source_1, receiving_pension_other_source_2
     ) (SELECT id, 
      dist_code, ben_fname, ben_mname, ben_lname,gender, dob, ben_age, 
     caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
     mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, 
     ration_card_no, ahl_tin, aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, 
     bpl_total_score, dist_name, assembly_code, assembly_name, police_station, block_ulb_code, 
     block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, village_town_city, house_premise_no, 
     post_office, pincode, residency_period, mobile_no, email, bank_name, 
     branch_name, bank_old_code, bank_ifsc, created_at, updated_at, created_by, created_by_level, 
     created_by_dist_code, created_by_local_body_code, scheme_id, type_disability, 
     percentage_disability, certifying_auth, next_level_role_id, comments, nominate_name, 
     nominate_address, nominate_relationship, receive_pension, social_security_pension, 
     ration_card_cat, rural_urban_id, lot_generated, 
     bank_edited, bank_code, payment_count, last_paid_yymm, 
     av_status, legacy_import, 
     receiving_pension_other_source_1, receiving_pension_other_source_2 from farmer.beneficiary where id=" . $id . ")");

      $is_update=PensionOAPFarmer::where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id, 'next_level_role_id' => null])
        ->update($input);
        if(count($upload_file_arch)>0){
            $doc_inserted_arch = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_arch')->insert($upload_file_arch);
        }
        else{
            $doc_inserted_arch =1; 
        }
        if(count($delete_array)>0){
            $doc_inserted_del = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id',$request->id)->whereIn('document_type',$delete_array)->delete();
        }
        else{
            $doc_inserted_del =1;  
        }
        if(count($upload_file)>0){
            $doc_inserted = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($upload_file);
        }
        else{
            $doc_inserted =1;  
        }
        $accept_reject_model = new AcceptRejectInfo;
        $accept_reject_model->created_at = $c_time;
        $accept_reject_model->application_id = $request->id;
        $accept_reject_model->scheme_id =  $request->scheme_id;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->op_type = 'APPUPDATE';
        $accept_reject_model->ip_address = $request->ip();
        $is_saved_log = $accept_reject_model->save();
        //dump($is_update); dump($doc_inserted_arch); dump($doc_inserted_del); dump($doc_inserted); dd($is_saved_log);
        if($arch_status && $is_update && $doc_inserted_arch && $doc_inserted_del && $doc_inserted && $is_saved_log){
            DB::commit();
            DB::connection('pgsql_encwrite')->commit();
            DB::connection('pgsql16')->commit();
            if ($designation_id_old == 'Operator')
               return redirect("application-list-read-only-edit?pr1=" . $scheme_schema)->with('success', 'Application Updated Successfully')
            ->with('id',   $id);
          else {
            return redirect('/')->with('success', 'Application Updated Successfully');
            }
        }
        else{
            DB::connection('pgsql16')->rollback();
            DB::rollback();
            DB::connection('pgsql_encwrite')->rollback();
            if ($designation_id_old == 'Operator')
             return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', array('Some error.Please try again'));
            else {
              return redirect('/')->with('danger', 'Some error.Please try again');
            }
        }
    } catch (\Exception $e) {
     // dd($e);
      DB::connection('pgsql16')->rollback();
      DB::rollback();
      DB::connection('pgsql_encwrite')->rollback();
      if ($designation_id_old == 'Operator') {
        return redirect("application-list-read-only-edit?pr1=" . $scheme_schema)->with('error', 'Some error.Please try again')
          ->with('id',  $row->getBenidAttribute());
      } else {
        return redirect('/')->with('error', 'Some error.Please try again');
      }
    }
    
    //return view('pension_view_details', ['row' => $row]);
  }

  function getCapacityBrief($scheme_id, $district)
  {
    $return_arr = array();
    $capacity = SchemeCapacity::select('capacity')->where('scheme_id', $scheme_id)->where('district_code', $district)->first();
    if (!empty($capacity->capacity)) {
      $return_arr['visible'] = 1;
      $return_arr['capacity'] = $capacity->capacity;
      $scheme = Scheme::select('id', 'scheme_name', 'short_code')->where('is_active', 1)->where('id', $scheme_id)->first();
      $scheme_schema_name = $scheme->short_code;
      $total_data = DB::table($scheme_schema_name . '.beneficiary')
        ->selectRaw('sum(case when next_level_role_id=0 then 1 else 0 end) approved,
                 sum(case when (is_verified=1 and is_approved=0 and is_rejected=0)  or next_level_role_id IS NULL then 1 else 0 end) pending')
        ->where('created_by_dist_code', $district)
        ->first();
      $return_arr['approved'] = $total_data->approved;
      $return_arr['pending'] = $total_data->pending;
    } else {
      $return_arr['visible'] = 0;
    }
    return $return_arr;
  }






  private function validateInput($request, $scheme_id, $add_edit_code)
  {
    $rules = [
      // 'duare_sarkar_reg_no'  => 'required_if:entry_type,=,Form through Duare Sarkar camp|max:100',
      // 'duare_sarkar_date'    => 'required_if:entry_type,=,Form through Duare Sarkar camp|date|before_or_equal:' . $today,          

      'first_name' => 'required|string|max:200',
      'middle_name' => 'string|nullable',
      'last_name' => 'required|string|max:200',
      'gender' => 'required',
      // 'dob' => '',
      //'txt_age' => 'required|numeric',
      'txt_age' => 'required|numeric',

      'father_first_name' => 'required|string|max:200',
      'father_middle_name' => 'string|nullable',
      'father_last_name' => 'required|string|max:200',
      'mother_first_name' => 'nullable|string|max:200',
      'mother_middle_name' => 'string|nullable',
      'mother_last_name' => 'nullable|string|max:200',
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
      'aadhar_no' => 'required|numeric|digits:12|nullable',
      'epic_voter_id' => 'required|string|max:20',
      'pan_no' => 'string|nullable|max:12',



      //  'district' => 'string',
      //'asmb_cons' => 'required|string',
      'police_station' => 'required|string',
      //'block' => 'max:200',
      // 'gp_ward' => 'max:200',
      'village' => 'required|string|max:300',
      'house' => 'string|nullable',
      'post_office' => 'required|string',
      'pin_code' => 'required|numeric|digits:6',
      'residency_period' => 'required|integer',
      'mobile_no' => 'nullable|numeric|digits:10',
      'email' => 'string|email|nullable',



      'name_of_bank' => 'required|string|max:200',
      'bank_branch' => 'required|string|max:200',
      'bank_account_number' => 'required|numeric',
      'bank_ifsc_code' => 'required|string',

      'cultivation_by_applicant'  => 'required|string',
      'source_income'             => 'string|nullable',
      'any_other_benefitis'       => 'string|nullable',
    ];
    //dd($rules);
    $attributes = array();
    $messages = array();
    $attributes['first_name'] = 'Beneficiary First Name';
    $attributes['middle_name'] = 'Beneficiary Middle Name';
    $attributes['last_name'] = 'Beneficiary Last Name';
    $attributes['gender'] = 'Gender';
    $attributes['dob'] = 'Date of Birth';
    $attributes['txt_age'] = 'Age (as on 01/01/2020)';
    $attributes['father_first_name'] = 'Father First Name';
    $attributes['father_middle_name'] = 'Father Middle Name';
    $attributes['father_last_name'] = 'Father Last Name';
    $attributes['mother_first_name'] = 'Mother First Name';
    $attributes['mother_middle_name'] = 'Mother Middle Name';
    $attributes['mother_last_name'] = 'Mother Last Name';
    $attributes['caste_category'] = 'Caste';
    $attributes['marital_status'] = 'Marital Status';
    $attributes['spouse_first_name'] = 'Spouse First Name';
    $attributes['spouse_middle_name'] = 'Spouse Middle Name';
    $attributes['spouse_last_name'] = 'Spouse Last Name';
    $attributes['monthly_income'] = 'Monthly Family Income (In Rs)';
    $attributes['ration_card_cat'] = 'Digital Ration Card Number';
    $attributes['ration_card_no'] = 'Digital Ration Card Number';
    $attributes['ahl_tin'] = 'AHL TIN';
    $attributes['aadhar_no'] = 'Aadhaar Number';
    $attributes['epic_voter_id'] = 'EPIC/Voter Id number';
    $attributes['pan_no'] = 'PAN';
    $attributes['bpl_seq_no'] = 'BPL Seq Number (if avaiable)';
    $attributes['bpl_id_no'] = 'BPL Id Number (if avaiable)';
    $attributes['bpl_total_score'] = 'BPL Total Score (if avaiable)';
    $attributes['district'] = 'District';
    $attributes['asmb_cons'] = 'Assembly Constituency';
    $attributes['urban_code'] = 'Rural/Urban';
    $attributes['block'] = 'Block/Municipality/Corp';
    $attributes['gp_ward'] = 'GP/Ward No.';
    $attributes['village'] = 'Village/Town/City';
    $attributes['house'] = 'House/Premise Number';
    $attributes['post_office'] = 'Post Office';
    $attributes['pin_code'] = 'Pin Code';
    $attributes['police_station'] = 'Police Station';
    $attributes['residency_period'] = 'Number of years Dwelling in WB';
    $attributes['mobile_no'] = 'Mobile Number';
    $attributes['email'] = 'Email Id';
    $attributes['bank_ifsc_code'] = 'IFS Code';
    $attributes['name_of_bank'] = 'Bank Name';
    $attributes['bank_branch'] = 'Bank Branch Name';
    $attributes['bank_account_number'] = 'Bank Account No.';
    $attributes['cultivation_by_applicant'] = 'Cultivation by Applicant';
    $attributes['source_income'] = 'Source of Present Income';
    $attributes['any_other_benefitis'] = 'Any other Benefits received';
    $doc_id_list = SchemeDocMap::select('doc_list_man')->where('scheme_code', $scheme_id)->first();
    $in_array = json_decode($doc_id_list->doc_list_man);
    $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->get();
    $messages = array();
    foreach ($doc_list as $key => $value) {
      if (in_array($value->id,  $in_array)) {
        if ($add_edit_code == 1) {
          $required = 'required';
        } else {
          $required = 'nullable';
        }
      } else {
        $required = 'nullable';
      }
      $rules['doc_' . $value->id] = $required . '|mimes:' . $value->doc_type . '|max:' . $value->doc_size_kb . ',';
      $messages['doc_' . $value['id'] . '.max'] = "The file uploaded for " . $value->doc_name . " size must be less than " . $value->doc_size_kb . " KB";
      $messages['doc_' . $value['id'] . '.mimes'] = "The file uploaded for " . $value->doc_name . " must be of type " . $value->doc_type;
      $messages['doc_' . $value['id'] . '.required'] = "Document for " . $value->doc_name . " must be uploaded";
    }
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    $return_arr = array('is_valid' => false, 'errors' => array());
    if (!$validator->passes()) {
      $error_msg = array();
      foreach ($validator->errors()->all() as $error) {
        array_push($error_msg, $error);
      }

      //dd($error_msg);
      $return_arr['is_valid'] = false;
      $return_arr['errors'] = $error_msg;
    } else {
      $return_arr['is_valid'] = true;
    }
    return $return_arr;
  }
  /**********************************************************/


  public function editOapFarmerList(Request $request)
  {
    // dd($request->all());
    $scheme_id =  $this->scheme_id;
    $user_id = AuthChecker::getUserId();
    $designation_id_old = Auth::user()->designation_id_old;
    if (!in_array($designation_id_old, array('Operator'))) {
      return redirect("/")->with('error', 'Not Allowed');
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $level = $roleObj['mapping_level'];
        $is_urban = $roleObj['is_urban'];
        $distCode = $roleObj['district_code'];
        if ($roleObj['is_urban'] == 1) {
          $blockCode = $roleObj['urban_body_code'];
        } else {
          $blockCode = $roleObj['taluka_code'];
        }
        break;
      }
    }
    // dd($is_active);
    if ($is_active == 0) {
      return redirect("/")->with('error', 'User Disabled');
    }
    // dd('ok'); 
    $scheme_row = Scheme::select('scheme_name')->where('id', $this->scheme_id)->first();
    $scheme_name = $scheme_row->scheme_name;
    $report_type_name = 'Approved List ';

    //echo $distCode.'/'.$blockCode; exit;
    if (request()->ajax()) {
      $condition = array();
      $condition["created_by_dist_code"] = $distCode;
      $condition["created_by_local_body_code"] = $blockCode;
      $condition["next_level_role_id"] = 0;
      $serachvalue = $request->search['value'];
      $limit = $request->input('length');
      $offset = $request->input('start');
      $filter_status = $request->input('filter_status');
      $totalRecords = 0;
      $filterRecords = 0;
      $data = array();
      $query = BenEntry::where($condition)->where('legacy_import', true);


      //dd($query);
      if (!empty($filter_status)) {
        if ($filter_status == 1) {
          $query = $query->whereNull('next_level_role_id_edit');
        } else if ($filter_status == 2) {
          $query = $query->where('next_level_role_id_edit', 999)->where('unlock_status', 1);
        } else if ($filter_status == 3) {
          $query = $query->where('next_level_role_id_edit', '!=', 999)->where('unlock_status', 1)->where('next_level_role_id_edit', '>', 0);
        } else if ($filter_status == 4) {
          $query = $query->whereNull('next_level_role_id_edit')->whereNull('unlock_status')->whereNotNull('caste');
        } else {
        }
      }
      $serachvalue = $request->search['value'];

      if (empty($serachvalue)) {
        $totalRecords = $query->count();
        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
          'unlock_status', 'id', 'created_by_dist_code',
          'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
          'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'next_level_role_id_edit', 'caste'
        ]);
      } else {
        if (is_numeric($serachvalue)) {
          $ben_id = substr($serachvalue, -7);
          $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
            $query1->where('id', $ben_id)
              ->orWhere('bank_code', $serachvalue);
          });
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'unlock_status', 'id', 'created_by_dist_code',
              'bank_code',
              'ben_fname',
              'block_ulb_name',
              'gp_ward_name',
              'bank_ifsc',
              'village_town_city',
              'scheme_id',
              'lot_generated',
              'payment_count',
              'next_level_role_id',
              'ben_lname', 'gender', 'ben_age', 'ben_mname', 'next_level_role_id_edit', 'caste'
            ]
          );
        } else {
          $query = $query->where(function ($query1) use ($serachvalue) {
            $query1->where('ben_fname', 'like', $serachvalue . '%')
              ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
              ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
              ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
          });
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'unlock_status', 'id', 'created_by_dist_code',
              'bank_code',
              'ben_fname',
              'block_ulb_name',
              'gp_ward_name',
              'bank_ifsc',
              'village_town_city',
              'scheme_id',
              'lot_generated',
              'payment_count',
              'next_level_role_id',
              'ben_lname', 'gender', 'ben_age', 'ben_mname', 'next_level_role_id_edit', 'caste'
            ]
          );
        }
        $filterRecords = count($data);

        //echo "<pre>"; print_r($data); exit;
      }

      return datatables()
        ->of($data)
        ->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('application_id', function ($data) {
          return $data->getBenidAttribute();
        })
        ->addColumn('ben_name', function ($data) {
          // return $data->getName();
          return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
        })
        ->addColumn('benf_name', function ($data) {
          return "Father Name";
        })
        ->addColumn('ben_age', function ($data) {
          return $data->ben_age;
        })
        ->addColumn('gender', function ($data) {
          return $data->gender;
        })
        ->addColumn('bank_ifsc', function ($data) {
          return $data->bank_ifsc;
        })
        ->addColumn('bank_code', function ($data) {
          return $data->bank_code;
        })
        ->addColumn('village_town_city', function ($data) {
          return $data->village_town_city;
        })
        ->addColumn('action', function ($data) use ($scheme_id) {
          $val = '';
          if ($data->next_level_role_id_edit == 0 and $data->unlock_status == '' and trim($data->caste) != '') {
            if (is_null($data->next_level_role_id_edit)) {
              $val = '<a href="editOapFarmer/' . $data->id . '" class="btn btn-primary ben_view_button" role="button" >Edit</a>';
            } else {
              $val = '<span class="label label-success">Alredy Edited and Approved</span>';
            }
          } else if ($data->next_level_role_id_edit > 0 and $data->unlock_status == 1 and $data->next_level_role_id_edit != 999) {
            $val = '<span class="label label-info">Alredy Edited and Pending at Approver</span>';
          } else if ($data->next_level_role_id_edit == 999 and $data->unlock_status == 1) {
            $val = '<span class="label label-info">Alredy Edited and Pending at Verifier</span>';
          } else {
            $val = '<a href="editOapFarmer/' . $data->id . '" class="btn btn-primary ben_view_button" role="button" >Edit</a>';
          }
          return $val;
        })
        ->rawColumns(['ben_id', 'ben_name', 'ben_age', 'gender', 'bank_ifsc', 'bank_code', 'village_town_city', 'action'])
        ->make(true);
    } else {

      return view(
        'farmer/editList',
        [
          'district_code' => $distCode,
          'scheme' => $this->scheme_id,
          'scheme_name' => $scheme_name,
          'report_type_name' => $report_type_name,
          'is_urban' => $is_urban

        ]
      );
    }
  }
  public function editOapFarmer(Request $request)
  {

    $scheme_id =  $this->scheme_id;
    $user_id = AuthChecker::getUserId();
    $designation_id_old = Auth::user()->designation_id_old;
    if (!in_array($designation_id_old, array('Operator'))) {
      return redirect("/")->with('error', 'Not Allowed');
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $level = $roleObj['mapping_level'];
        $is_urban = $roleObj['is_urban'];
        $distCode = $roleObj['district_code'];
        if ($roleObj['is_urban'] == 1) {
          $blockCode = $roleObj['urban_body_code'];
        } else {
          $blockCode = $roleObj['taluka_code'];
        }
        break;
      }
    }
    if ($is_active == 0) {
      return redirect("/")->with('error', 'User Disabled');
    }
    $id = $request->id;
    if (empty($id)) {
      return redirect("/")->with('error', 'Application Id Not Found');
    }
    if (!ctype_digit($id)) {
      return redirect("/")->with('error', 'Application Id Valid');
    }
    $condition = array();
    $condition["created_by_dist_code"] = $distCode;
    $condition["created_by_local_body_code"] = $blockCode;
    $condition["next_level_role_id"] = 0;
    $condition["id"] =  $id;
    $row = PensionOAPFarmer::where($condition)->first();
    if (empty($row)) {
      return redirect("/")->with('error', 'Application Id Valid');
    }
    $scheme_row = Scheme::select('scheme_name')->where('id', $this->scheme_id)->first();
    $scheme_name = $scheme_row->scheme_name;
    $districts = District::where('is_revenue_district', '=', '1')->get(['district_code', 'district_name']);

    //echo $row->district_code;exit;

    $assembly_arr = Assembly::where('district_code', '=', $row->dist_code)->get();
    //dd($assembly_arr->toArray());

    $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->first();
    //echo "<pre>"; print_r($doc_id_list); exit;
    if (!empty($doc_id_list->doc_list_man))
      $doc_list_man = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type')->whereIn("id", json_decode($doc_id_list->doc_list_man))->get()->toArray();
    else
      $doc_list_man = array();
    if (!empty($doc_id_list->doc_list_opt))
      $doc_list_opt = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type')->whereIn("id", json_decode($doc_id_list->doc_list_opt))->get()->toArray();
    else
      $doc_list_opt = array();
    if (count($doc_list_man) > 0 || count($doc_list_opt) > 0) {
      $doc_list = array_merge($doc_list_man, $doc_list_opt);
    } else {
      $doc_list = array();
    }
    if (!empty($doc_id_list['doc_list_man_group']))
      $doc_list_man_group = json_decode($doc_id_list['doc_list_man_group']);
    else
      $doc_list_man_group = array();



    //echo "<pre>"; print_r($doc_list_man_group); exit;
    if (!empty($doc_list_man_group)) {
      $doc_list = array_merge($doc_list_man, $doc_list_opt);
      $all_doc_id = array();
      foreach ($doc_list as $mDoc) {
        array_push($all_doc_id, $mDoc['id']);
      }
      $document_msg = '';
      if (count($doc_list)) {
        foreach ($doc_list_man_group as $man_group) {
          $document_msg .= '<div  class="form-group col-md-12" >';
          $heading_msg = "At least one document must be uploaded for ";
          $doucument_group_name = $this->getGroupName($man_group);
          $heading_msg .= '<span style="color:red;font-weight:bold">' . $doucument_group_name . '</span>';
          $document_msg .= "<p style='font-weight:bold;font-size:17px;'>" . $heading_msg . " </p>";
          $document_msg .= "<ul>";
          $results = DB::select("SELECT doc_name FROM m_attached_doc where id IN (" . implode(',', $all_doc_id) . ") and $man_group =any(doucument_group)");
          $results = json_decode(json_encode($results), true);


          //dd($results);
          if (count($results) > 0) {
            $i = 0;
            foreach ($results as $requiredmsg) {

              $document_msg .= "<li style='font-weight:bold;'>" . $requiredmsg['doc_name'] . "</li>";
              $i++;
            }
          }


          $document_msg .= "</ul>";
          $document_msg .= "</div>";
        }
      } else
        $document_msg = "";
    } else
      $document_msg = "";
      $encloser_list = array();
      $i = 0;
      $encolserdata = BenDocs::select('document_type', 'attched_document')->where('scheme_id',$scheme_id)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->get()->pluck('attched_document','document_type')->toArray();
      $already_id = array_keys($encolserdata);
      $doc_profile_image_id_row = DocumentType::select('id')->where("is_profile_pic", true)->first();
      $doc_profile_image_id = $doc_profile_image_id_row->id;
      $doc_profile_image_val = '';
      if (count($doc_list) > 0) {
          foreach ($doc_list as $doc) {
              $encloser_list[$i]['id'] = $doc['id'];
              $encloser_list[$i]['is_profile_pic'] = intval($doc['is_profile_pic']);
              $encloser_list[$i]['doc_size_kb'] = $doc['doc_size_kb'];
              $encloser_list[$i]['doc_name'] = $doc['doc_name'];
              $encloser_list[$i]['doc_type'] = $doc['doc_type'];
              if (count($encolserdata) > 0) {
                  if (in_array($doc['id'], $already_id)) {
                      if ($doc['is_profile_pic'] == true) {
                          $doc_profile_image_val = $encolserdata[$doc['id']];
                      }
                      $encloser_list[$i]['can_download'] = 1;
                  } else {
                      $encloser_list[$i]['can_download'] = 0;
                  }
              } else {
                  $encloser_list[$i]['can_download'] = 0;
              }
              if (in_array($doc['id'], json_decode($doc_id_list['doc_list_man']))) {
                  if (count($encolserdata) > 0) {
                      //dump($doc['id']);
                      if (in_array($doc['id'], $already_id)) {
                          $encloser_list[$i]['required'] = 0;
                      } else {
                          $encloser_list[$i]['required'] = 1;
                      }
                  } else {
                      $encloser_list[$i]['required'] = 1;
                  }
                  if ($doc['id'] == 116) {
                      $encloser_list[$i]['required'] = 0;
                      $encloser_list[$i]['mandatory'] = 0;
                  } else {
                      $encloser_list[$i]['mandatory'] = 1;
                  }
              } else {
                  $encloser_list[$i]['required'] = 0;
                  $encloser_list[$i]['mandatory'] = 0;
              }




              $i++;
          }
      }
     
    return view('farmer/pension_edit_unlock', ['doc_profile_image_id' => $doc_profile_image_id,   'scheme_name' => $scheme_name, 'row' => $row, 'document_msg' => $document_msg, 'districts' => $districts, 'scheme_id' => $scheme_id, 'encloser_list' => $encloser_list, 'profile_img' => $doc_profile_image_id, 'max_dob' => $this->max_dob, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'assembly_arr' => $assembly_arr]);
  }

  function editOapFarmerPost(Request $request)
  {
    $scheme_id =  $this->scheme_id;
    $user_id = AuthChecker::getUserId();
    $designation_id_old = Auth::user()->designation_id_old;
    if (!in_array($designation_id_old, array('Operator'))) {
      return redirect("/")->with('error', 'Not Allowed');
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $level = $roleObj['mapping_level'];
        $is_urban = $roleObj['is_urban'];
        $distCode = $roleObj['district_code'];
        if ($roleObj['is_urban'] == 1) {
          $blockCode = $roleObj['urban_body_code'];
        } else {
          $blockCode = $roleObj['taluka_code'];
        }
        break;
      }
    }
    if ($is_active == 0) {
      return redirect("/")->with('error', 'User Disabled');
    }
    $id = $request->id;
    if (empty($id)) {
      return redirect("/")->with('error', 'Application Id Not Found');
    }
    if (!ctype_digit($id)) {
      return redirect("/")->with('error', 'Application Id Valid');
    }
    $condition = array();
    $condition["created_by_dist_code"] = $distCode;
    $condition["created_by_local_body_code"] = $blockCode;
    $condition["next_level_role_id"] = 0;
    $condition["id"] =  $id;
    $row = PensionOAPFarmer::where($condition)->first();
    if (empty($row)) {
      return redirect("/")->with('error', 'Application Id Valid');
    }

    $caste_key =  array_keys(Config::get('constants.caste'));
    $marital_status_key =  array_keys(Config::get('constants.marital_status'));
    $gender_key =  array_keys(Config::get('constants.gender'));

    $rules = [
      'gender' => 'required|in:' . implode(",", $gender_key),
      'dob' => 'nullable|date',
      'txt_age' => 'required|numeric',

      'father_first_name' => 'required|string|max:200',
      'father_middle_name' => 'string|nullable',
      'father_last_name' => 'required|string|max:200',
      // 'mother_first_name' => 'required|string|max:200',
      // 'mother_middle_name' => 'string|nullable',
      // 'mother_last_name' => 'required|string|max:200',
      'caste_category' => 'required|in:' . implode(",", $caste_key),
      'marital_status' => 'required|in:' . implode(",", $marital_status_key),
      'spouse_first_name' => 'string|nullable',
      'spouse_middle_name' => 'string|nullable',
      'spouse_last_name' => 'string|nullable',
      'bpl_seq_no' => 'string|nullable|max:12',
      'bpl_id_no' => 'string|nullable|max:12',
      'bpl_total_score' => 'integer|nullable',
      'monthly_income' => 'required|numeric|between: 0.00,999999.99',

      'ration_card_cat' => 'required|string',
      'ration_card_no' => 'required|string|max:11',
      'ahl_tin' => 'string|nullable|max:100',
      'aadhar_no' => 'numeric|digits:12|required',
      'epic_voter_id' => 'required|string|max:20',
      'pan_no' => 'string|nullable|max:12',
      'district' => 'required',
      'urban_code' => 'required',
      'police_station' => 'required',
      'block' => 'required',
      'gp_ward' => 'required',
      //'asmb_cons' => 'required|string',
      'police_station' => 'required|string',
      'village' => 'required|string|max:300',
      'house' => 'string|nullable',
      'post_office' => 'required|string',
      'pin_code' => 'required|numeric|digits:6',
      'residency_period' => 'required|integer',
      'mobile_no' => 'nullable|numeric|digits:10',
      'email' => 'string|email|nullable',
      'cultivation_by_applicant'  => 'required|string',
      'source_income'             => 'string|nullable',
      'any_other_benefitis'       => 'string|nullable',
    ];
    $attributes = array();
    $messages = array();
    $attributes['gender'] = 'Gender';
    $attributes['dob'] = 'Date of Birth';
    $attributes['txt_age'] = 'Age';

    $attributes['father_first_name'] = 'Father First Name';
    $attributes['father_middle_name'] = 'Father Middle Name';
    $attributes['father_last_name'] = 'Father Last Name';
    // $attributes['mother_first_name'] = 'Mother First Name';
    //$attributes['mother_middle_name'] = 'Mother Middle Name';
    // $attributes['mother_last_name'] = 'Mother Last Name';
    $attributes['caste_category'] = 'Caste';
    $attributes['marital_status'] = 'Marital Status';
    $attributes['aadhar_no'] = 'Beneficiary Aadhaar Number';
    $attributes['mobile_no'] = 'Mobile Number';
    $attributes['spouse_first_name'] = 'Spouse First Name';
    $attributes['spouse_middle_name'] = 'Spouse Middle Name';
    $attributes['spouse_last_name'] = 'Spouse Last Name';
    $attributes['monthly_income'] = 'Monthly Family Income(In Rs)';

    $attributes['ration_card_cat'] = 'Digital Ration Card Number';
    $attributes['ration_card_no'] = 'Digital Ration Card Number';
    $attributes['ahl_tin'] = 'AHL TIN';
    $attributes['aadhar_no'] = 'Aadhaar Number';
    $attributes['epic_voter_id'] = 'EPIC/Voter Id number';
    $attributes['pan_no'] = 'PAN';
    $attributes['bpl_seq_no'] = 'BPL Seq Number';
    $attributes['bpl_id_no'] = 'BPL Id Number';
    $attributes['bpl_total_score'] = 'BPL Total Score';


    $attributes['district'] = 'District';
    $attributes['asmb_cons'] = 'Assembly Constituency';
    $attributes['urban_code'] = 'Rural/ Urban';
    $attributes['police_station'] = 'Police Station';
    $attributes['block'] = 'Block/Municipality/Corp';
    $attributes['gp_ward'] = 'GP/Ward No.';
    $attributes['village'] = 'Village/Town/City';
    $attributes['house_premise_no'] = 'House / Premise No.';
    $attributes['post_office'] = 'Post Office';
    $attributes['pin_code'] = 'Pin Code';
    $attributes['residency_period'] = 'Number of years Dwelling in WB';
    $attributes['email'] = 'Email Id';

    $attributes['cultivation_by_applicant'] = 'Cultivation by Applicant';
    $attributes['source_income'] = 'Source Income';
    $attributes['any_other_benefitis'] = 'Any Other Benefitis';
    $doc_id_list = SchemeDocMap::select('doc_list_man')->where('scheme_code', $scheme_id)->first();
    //dd($request->add_edit_status);
    if (isset($doc_id_list['doc_list_man']) && $doc_id_list['doc_list_man'] != 'null') {

      $in_array = json_decode($doc_id_list->doc_list_man);
    } else
      $in_array = array();
    $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->get();
    if (count($in_array) > 0) {
      foreach ($doc_list as $key => $value) {
        if (in_array($value->id,  $in_array)) {
          $previus_uploaded =   $request->input('doc_already_' . $value->id);
          if ($previus_uploaded == 0) {
            $required = 'required';
          } else {
            $required = 'nullable';
          }
        } else {
          $required = 'nullable';
        }
        $rules['doc_' . $value->id] = $required . '|mimes:' . $value->doc_type . '|max:' . $value->doc_size_kb . ',';
        //$rules['doc_' . $value->id] = $value->doc_name . ',';
        $messages['doc_' . $value->id . '.max'] = "The file uploaded for " . $value->doc_name . " size must be less than :max KB";
        $messages['doc_' . $value->id . '.mimes'] = "The file uploaded for " . $value->doc_name . " must be of type " . $value->doc_type;
        $messages['doc_' . $value->id . '.required'] = "Document for " . $value->doc_name . " must be uploaded";
      }
    }
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      if (!empty($request->dob)) {
        $post_dob = $request->dob;
      } else
        $post_dob = NULL;
      $post_aadhar_no = $request->aadhar_no;
      if ($this->isAadharValid($post_aadhar_no) == false) {
        $return_text = 'Aadhaar Number Invalid';
        $return_msg = array("" . $return_text);
        return redirect("/editOapFarmer/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
      }
      $district_list = District::all();
      $sel_district = $request->district;
      $cnt = $district_list->where('district_code', $sel_district)->count();
      if ($cnt == 0) {
        $return_text = 'District Invalid';
        $return_msg = array("" . $return_text);
        return redirect("/editOapFarmer/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
      }
      $asmb_cons = $request->asmb_cons;
      if(!empty($asmb_cons)){
      $assembly_arr = Assembly::where('district_code', $sel_district)->where('ac_no', $asmb_cons)->first();
      if (empty($assembly_arr)) {
        $return_text = 'Assembly Invalid';
        $return_msg = array("" . $return_text);
        return redirect("/editOapFarmer/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
      }
      $assembly_name = $assembly_arr->ac_name;
      }
      else{
        $asmb_cons=NULL;
        $assembly_name ='';
      }
      
      $sel_urban_code = $request->urban_code;
      $sel_block = $request->block;
      $sel_gp_ward = $request->gp_ward;
      if ($sel_urban_code == 1) {
        $block_munc_arr = UrbanBody::where('district_code', $sel_district)->where('urban_body_code', $sel_block)->first();
        if (empty($block_munc_arr)) {
          $return_text = 'Block/Municipality/Corp Invalid';
          $return_msg = array("" . $return_text);
          return redirect("/editOapFarmer/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
        }
        $block_ulb_name = $block_munc_arr->urban_body_name;
        $gp_ward_arr = Ward::where('urban_body_code', $sel_block)->where('urban_body_ward_code', $sel_gp_ward)->first();
        if (empty($gp_ward_arr)) {
          $return_text = 'GP/Ward Not Invalid';
          $return_msg = array("" . $return_text);
          return redirect("/editOapFarmer/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
        }
        $gp_ward_name   = $gp_ward_arr->urban_body_ward_name;
      } else if ($sel_urban_code == 2) {
        $block_munc_arr = Taluka::where('district_code', $sel_district)->where('block_code', $sel_block)->first();
        if (empty($block_munc_arr)) {
          $return_text = 'Block/Municipality/Corp Invalid';
          $return_msg = array("" . $return_text);
          return redirect("/editOapFarmer/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
        }
        $block_ulb_name = $block_munc_arr->block_name;
        $gp_ward_arr = GP::where('block_code', $sel_block)->where('gram_panchyat_code', $sel_gp_ward)->first();
        if (empty($gp_ward_arr)) {
          $return_text = 'GP/Ward Not Invalid';
          $return_msg = array("" . $return_text);
          return redirect("/editOapFarmer/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
        }
        $gp_ward_name   = $gp_ward_arr->gram_panchyat_name;
      }
      $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->get();
      $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
      $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
      $doc_list = array_merge($doc_list_man, $doc_list_opt);
      $encolserdata = BenDocsOAPFarmer::select('doc_type_id', 'doc_name')->where('ben_id', $request->id)->get()->pluck('doc_name', 'doc_type_id')->toArray();
      $already_id = array_keys($encolserdata);

      $doc_list_man_group_upload = array();
      $doc_list_man_group_db = array();


      if (($doc_id_list[0]['doc_list_man_group']) != '' &&  ($doc_id_list[0]['doc_list_man_group'] != 'null') && ($doc_id_list[0]['doc_list_man_group']) != null) {
        $doc_list_man_group_db = json_decode($doc_id_list[0]['doc_list_man_group']);
      }

      foreach ($doc_list as $doc) {
        if ($request->hasFile('doc_' . $doc)) {
          $doucument_group_id = DocumentType::select('doucument_group')->where('id', $doc)->first();
          if ($doucument_group_id['doucument_group'] != '') {
            $arr = array();
            $postgresStr = trim($doucument_group_id['doucument_group'], "{}");
            $elmts = explode(",", $postgresStr);
            foreach ($elmts as $myarr) {
              if (!in_array($myarr, $doc_list_man_group_upload)) {
                array_push($doc_list_man_group_upload, $myarr);
              }
            }
          }
        } else {
          if (in_array($doc, $already_id)) {
            $doucument_group_id = DocumentType::select('doucument_group')->where('id', $doc)->first();
            if ($doucument_group_id['doucument_group'] != '') {
              $arr = array();
              $postgresStr = trim($doucument_group_id['doucument_group'], "{}");
              $elmts = explode(",", $postgresStr);
              foreach ($elmts as $myarr) {
                if (!in_array($myarr, $doc_list_man_group_upload)) {
                  array_push($doc_list_man_group_upload, $myarr);
                }
              }
            }
          }
        }
      }

      if (count($doc_list_man_group_db) > 0) {
        $errors = array();
        $i = 0;
        foreach ($doc_list_man_group_db as $group) {
          $doucument_group_name = $this->getGroupName($group);
          if (!in_array($group, $doc_list_man_group_upload)) {
            $errorMsg = "At least one document must be uploaded for " . $doucument_group_name;
            array_push($errors, $errorMsg);
          }
        }
        if (count($errors) > 0)
          return redirect("/editOapFarmer/" . $request->id)->with('errors', $errors)->withInput(Input::all());
      }
      $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
      $count = PensionOAPFarmer::where('aadhar_no', trim($request->aadhar_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
      if ($count > 0) {
        $return_text = 'Aadhaar Number Already Exist! Please try different.';
        $return_msg = array("" . $return_text);
        return redirect("/editOapFarmer/" . $request->id)->with('errors', $return_msg);
      }
      $social_security_pension = "";
      $receive_pension = "";
      if ($request->receive_pension != "") {
        $receive_pension = implode(',', $request->receive_pension);
      }

      if ($request->social_security_pension != "") {
        $social_security_pension = implode(',', $request->social_security_pension);
      }

      if (trim($request->f_land_array) != '') {
        $f_land_array   = json_decode($request->f_land_array, true);
        $f_land_array   = json_encode($f_land_array);
      } else {
        $f_land_array   = null;
      }

      if (trim($request->f_member_array) != '') {
        $f_member_array = json_decode($request->f_member_array, true);
        $f_member_array = json_encode($f_member_array);
      } else {
        $f_member_array   = null;
      }

      if (trim($request->dob) != '') {
        $request_dob = $request->dob;
      } else {
        $request_dob   = null;
      }

      if (trim($request->mobile_no) != '') {
        $request_mobile_no   = $request->mobile_no;
      } else {
        $request_mobile_no   = null;
      }

      if (trim($request->marital_status) != "Married") {
        $request->spouse_first_name = "";
        $request->spouse_middle_name = "";
        $request->spouse_last_name = "";
      }
      //dd($request->toArray());
      $input = [
        // 'entry_type'         => trim($request->entry_type),
        // 'ds_registration_no' => $duare_sarkar_reg_no,
        // 'ds_date'            => $duare_sarkar_date,
        'unlock_status' => 1,
        'next_level_role_id_edit' => 999,
        'land_json'   =>  $f_land_array,
        'member_json' =>  $f_member_array,

        //'ben_fname' => trim($request->first_name),
        //'ben_mname' => trim($request->middle_name),
        // 'ben_lname' => trim($request->last_name),
        'gender' => trim($request->gender),
        'dob' => $request_dob,  //check
        'ben_age' => trim($request->txt_age),

        'father_fname' => trim($request->father_first_name),
        'father_mname' => trim($request->father_middle_name),
        'father_lname' => trim($request->father_last_name),
        'mother_fname' => trim($request->mother_first_name),
        'mother_mname' => trim($request->mother_middle_name),
        'mother_lname' => trim($request->mother_last_name),
        'caste' => trim($request->caste_category),
        'marital_status' => trim($request->marital_status),
        'spouse_fname' => trim($request->spouse_first_name),
        'spouse_mname' => trim($request->spouse_middle_name),
        'spouse_lname' => trim($request->spouse_last_name),
        //'bpl_y_n' =>$request->if_bpl,
        'bpl_seq_no' => trim($request->bpl_seq_no),
        'bpl_id_no' => trim($request->bpl_id_no),
        'bpl_total_score' => $request->bpl_total_score,
        'mothly_income' => trim($request->monthly_income),

        'receive_pension' => trim($receive_pension),
        'social_security_pension' => trim($social_security_pension),

        'ration_card_cat' => trim($request->ration_card_cat),
        'ration_card_no'  => trim($request->ration_card_no),
        'ahl_tin'  => trim($request->ahl_tin),
        'aadhar_no'  => trim($request->aadhar_no),
        'epic_voter_id'  => trim($request->epic_voter_id),
        'pan_no'  => trim($request->pan_no),



        'dist_code' => trim($request->district),
        'rural_urban_id' => trim($request->urban_code),
        'police_station'  => trim($request->police_station),
        'block_ulb_code'  => trim($request->block),
        'block_ulb_name' => trim($block_ulb_name),
        'gp_ward_code' => trim($request->gp_ward),
        'gp_ward_name' => trim($gp_ward_name),
        'village_town_city'  => trim($request->village),
        'house_premise_no'  => trim($request->house),
        'post_office'  => trim($request->post_office),
        'pincode' => trim($request->pin_code),
        'residency_period' => trim($request->residency_period),
        'mobile_no'  => $request_mobile_no,
        'email' => trim($request->email),



        //'bank_name'  => trim($request->name_of_bank),
        //'branch_name'   => trim($request->bank_branch),
        //'bank_code'    => trim($request->bank_account_number),
        //'bank_ifsc'   => trim($request->bank_ifsc_code),

        'cultivation_by_applicant'   => trim($request->cultivation_by_applicant),
        'source_income'              => trim($request->source_income),
        'any_other_benefitis'        => trim($request->any_other_benefitis),

        'nominate_name' => trim($request->nominate_name),
        'nominate_address' => trim($request->nominate_address),
        'nominate_relationship' => trim($request->nominate_relationship),
        'av_status' => trim($request->av_status),
        'receiving_pension_other_source_1' => trim($request->receiving_pension_other_source_1),
        'receiving_pension_other_source_2' => trim($request->receiving_pension_other_source_2),
      ];
      if(!empty($request->asmb_cons)){
         $input['assembly_code']= $request->asmb_cons;
         $input['assembly_name']= $assembly_name;
      }
      $uploaded_doc = array();
      $base_url = url('/');
      $encloser_list = array();
      $i = 0;
      $c_time = date('Y-m-d H:i:s', time());
      $all_document=DocumentType::where('is_active', TRUE)->get();
      $delete_array=array();
      $j=0;
      $upload_file_arch=array();
      $upload_file=array();
      $encolserdata = BenDocs::where('scheme_id',$scheme_id)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->get();
      $already_id=array();
      foreach($encolserdata as $enc_item){
          array_push($already_id,$enc_item->document_type);
      }  
      foreach ($doc_list as $doc) {
          if ($request->hasFile('doc_' . $doc)) {
          $doc_file = $request->file('doc_' . $doc);
          $img_data = file_get_contents($doc_file);
          $u_extension = $doc_file->getClientOriginalExtension();
          $mime_type = $doc_file->getMimeType();
          $doc_type_name =$all_document->where('id', $doc)->first() ;
          if(strtolower($mime_type)=='image/jpeg'){
            if($u_extension=='jpg' || $u_extension=='jpeg'){
              $extension=$u_extension;
             }
             else{
              $errors = array();
              $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
              array_push($errors, $errorMsg);
              return back()->with('errors', $errors)->withInput(Input::all());  
              }
          }
          else if(strtolower($mime_type)=='image/png'){
              $extension='png';
          }else if(strtolower($mime_type)=='image/gif'){
              $extension='gif';
          }else if(strtolower($mime_type)=='application/pdf'){
              $extension='pdf';
          }
          else{
              $errors = array();
              $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
              array_push($errors, $errorMsg);
              return back()->with('errors', $errors)->withInput(Input::all());  
          }
          if($u_extension!=$extension){
              $errors = array();
              $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
              array_push($errors, $errorMsg);
              return back()->with('errors', $errors)->withInput(Input::all());  
          }
          $base64 = base64_encode($img_data);
          $upload_file[$i]['beneficiary_id']=$request->id;
          $upload_file[$i]['created_by_dist_code']=$distCode;
          $upload_file[$i]['created_by_dist_code']=$distCode;
          $upload_file[$i]['created_by_local_body_code']=$blockCode;
          $upload_file[$i]['document_type']=$doc;
          $upload_file[$i]['scheme_id']=$scheme_id;
          $upload_file[$i]['created_by_level']=$level;
          $upload_file[$i]['created_at']=$c_time;
          $upload_file[$i]['created_by']=$user_id;
          $upload_file[$i]['ip_address']=$request->ip();
          $upload_file[$i]['attched_document']=$base64;
          $upload_file[$i]['document_mime_type']=$mime_type;
          $upload_file[$i]['document_extension']=$extension;
          if(!empty($doc_type_name)){
           $upload_file[$i]['doc_type_name'] = $doc_type_name->doc_name;
          }
          $i++;
          $doc_already_edit =$encolserdata->where('document_type',$doc)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->first();
          if (in_array($doc, $already_id)) {
              array_push($delete_array,$doc);
              $upload_file_arch[$j]['beneficiary_id']=$request->id;
              $upload_file_arch[$j]['created_by_dist_code']=$doc_already_edit->created_by_dist_code;
              $upload_file_arch[$j]['created_by_local_body_code']=$doc_already_edit->created_by_local_body_code;
              $upload_file_arch[$j]['document_type']=$doc_already_edit->document_type;
              $upload_file_arch[$j]['scheme_id']=$doc_already_edit->scheme_id;
              $upload_file_arch[$j]['created_by_level']=$doc_already_edit->created_by_level;
              $upload_file_arch[$j]['created_at']=$doc_already_edit->created_at;
              $upload_file_arch[$j]['created_by']=$doc_already_edit->created_by;
              $upload_file_arch[$j]['ip_address']=$doc_already_edit->ip_address;
              $upload_file_arch[$j]['attched_document']=$doc_already_edit->attched_document;
              $upload_file_arch[$j]['document_mime_type']=$doc_already_edit->document_mime_type;
              $upload_file_arch[$j]['document_extension']=$doc_already_edit->document_extension;
              $j++;
          }
         }
      }
      DB::beginTransaction();
      DB::connection('pgsql16')->beginTransaction();
      DB::connection('pgsql_encwrite')->beginTransaction();
      try {
        //DB::connection('pgsql')->statement
        //DB::statement
        $arch_status=DB::statement("INSERT INTO farmer.arc_beneficiary(id, 
             dist_code, ben_fname, ben_mname, ben_lname, gender, dob, ben_age, 
            caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
            mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, 
            ration_card_no, ahl_tin, aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, 
            bpl_total_score, dist_name, assembly_code, assembly_name, police_station, block_ulb_code, 
            block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, village_town_city, house_premise_no, 
            post_office, pincode, residency_period, mobile_no, email, bank_name, 
            branch_name, bank_old_code, bank_ifsc, created_at, updated_at, created_by, created_by_level, 
            created_by_dist_code, created_by_local_body_code, scheme_id, type_disability, 
            percentage_disability, certifying_auth, next_level_role_id, comments, nominate_name, 
            nominate_address, nominate_relationship, receive_pension, social_security_pension, 
            ration_card_cat, rural_urban_id, lot_generated, 
            bank_edited, bank_code, payment_count, last_paid_yymm, 
            av_status, legacy_import, 
            receiving_pension_other_source_1, receiving_pension_other_source_2
            ) (SELECT id, 
             dist_code, ben_fname, ben_mname, ben_lname,gender, dob, ben_age, 
            caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
            mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, 
            ration_card_no, ahl_tin, aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, 
            bpl_total_score, dist_name, assembly_code, assembly_name, police_station, block_ulb_code, 
            block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, village_town_city, house_premise_no, 
            post_office, pincode, residency_period, mobile_no, email, bank_name, 
            branch_name, bank_old_code, bank_ifsc, created_at, updated_at, created_by, created_by_level, 
            created_by_dist_code, created_by_local_body_code, scheme_id, type_disability, 
            percentage_disability, certifying_auth, next_level_role_id, comments, nominate_name, 
            nominate_address, nominate_relationship, receive_pension, social_security_pension, 
            ration_card_cat, rural_urban_id, lot_generated, 
            bank_edited, bank_code, payment_count, last_paid_yymm, 
            av_status, legacy_import, 
            receiving_pension_other_source_1, receiving_pension_other_source_2 from farmer.beneficiary where id=" . $id . ")");
           $main_update=PensionOAPFarmer::where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id, 'next_level_role_id' => 0])
          ->update($input);
          if($main_update){
            if(count($upload_file_arch)>0){
                $doc_inserted_arch = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_arch')->insert($upload_file_arch);
            }
            else{
                $doc_inserted_arch =1; 
            }
            if(count($delete_array)>0){
                $doc_inserted_del = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id',$request->id)->whereIn('document_type',$delete_array)->delete();
            }
            else{
                $doc_inserted_del =1;  
            }
            if(count($upload_file)>0){
                $doc_inserted = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($upload_file);
            }
            else{
                $doc_inserted =1;  
            }
            $accept_reject_model = new AcceptRejectInfo;
            $accept_reject_model->created_at = $c_time;
            $accept_reject_model->application_id = $row->id;
            $accept_reject_model->scheme_id =  $row->scheme_id;
            $accept_reject_model->user_id = $user_id;
            $accept_reject_model->user_id = $user_id;
            $accept_reject_model->created_by_dist_code = $distCode;
            $accept_reject_model->created_by_local_body_code = $blockCode;
            $accept_reject_model->op_type = 'SM';
            $is_saved_log = $accept_reject_model->save();
            if($doc_inserted_arch==1 && $doc_inserted_del && $doc_inserted==1 && $is_saved_log){
              DB::commit();
              DB::connection('pgsql_encwrite')->commit();
              DB::connection('pgsql16')->commit();
              $return_text = 'Beneficiary Edited Successfully';
              return redirect("/editOapFarmerList")->with('success', $return_text)->with('id', $id);
            }else{
              DB::connection('pgsql16')->rollback();
              DB::rollback();
              DB::connection('pgsql_encwrite')->rollback();
               // dd('ok778gggtyu');
                $return_text = 'Error. Please try again';
                $return_msg = array("" . $return_text);
                return redirect("/editOapFarmer/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());   
            }
          }
        
        else{
          DB::connection('pgsql16')->rollback();
          DB::rollback();
          DB::connection('pgsql_encwrite')->rollback();
           // dd('ok778gggtyu');
            $return_text = 'Error. Please try again';
            $return_msg = array("" . $return_text);
            return redirect("/editOapFarmer/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());   
        }
      } catch (\Exception $e) {
        //dd($e);
        DB::connection('pgsql16')->rollback();
        DB::rollback();
        DB::connection('pgsql_encwrite')->rollback();
        $return_text = 'Error. Please try again';
        $return_msg = array("" . $return_text);
        return redirect("/editOapFarmer/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
      }
    } else {
      $return_msg = $validator->errors()->all();
      return redirect("/editOapFarmer/" . $request->id)->with('errors', $return_msg)->withInput();
    }
  }
  function ajaxgetAgeOapFarmer(Request $request)
  {
    $diff = 0;
    if ($request->dob != '') {
      $diff = Carbon::parse($request->dob)->diffInYears($this->base_dob_chk_date);
    }
    return $diff;
  }


  public function oapFarmerApprovedEdit(Request $request)
  {

    $user_id = AuthChecker::getUserId();
    $scheme_id = 0;
    $ben_table = "";
    if ($request->get('pr1')) {
      if ($request->get('pr1') == "oap_farmer") {
        $ben_table = "Pension";
        $scheme_id = 13;
      }
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    
    //echo "<pre>"; print_r($roleArray ); 
    foreach ($roleArray as $roleObj) {

      // echo  $roleObj['scheme_id'] .'=='. $scheme_id;exit;
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $mappingLevel = $roleObj['mapping_level'];
        $district_code = $roleObj['district_code'];
        $scheme_id = $scheme_id;
        $is_first = $roleObj['is_first'];
        $is_urban = $roleObj['is_urban'];
        $role_id = $roleObj['id'];
        if ($roleObj['is_urban'] == 1) {
          $urban_body_code = $roleObj['urban_body_code'];
          $body_code = $urban_body_code;
        } else {
          $taluka_code = $roleObj['taluka_code'];
          $body_code = $taluka_code;
        }
        break;
      }
    }
    //echo $is_active; exit;
    if ($is_active == 1) {
      $approveBtnvisible = 1;

      if ($is_first) {   // First Level Verifier    
        if ($mappingLevel == "State") {
          $level = "State";
        } else if ($mappingLevel == "District") {
          $appPrefix = "App";
          $modelName = $appPrefix . "\\" . $ben_table;
          $rows = $modelName::where('next_level_role_id', null)->where('created_by_dist_code', $district_code)->orderBy('id', 'desc')->paginate(10);
          return view('processApplicationOapFarmerEdit/pension_list', ['nhm_employee_details' => $rows]);
        } else if ($mappingLevel == "Subdiv") {

          //echo 123; exit;
          if ($is_urban == 1) {
            $duty_level = "SubdivVerifier";
            $urban_bodys = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();
            $urban_body_codes = [];
            $i = 0;
            foreach ($urban_bodys as $urban_body) {

              $urban_body_codes[$i] = $urban_body->urban_body_code;
              $i++;
            }
            if (request()->ajax()) {
              $limit = $request->input('length');
              $offset = $request->input('start');
              if (!empty($request->filter_1)  && empty($request->filter_2)) {
                $appPrefix = "App";
                $modelName = $appPrefix . "\\" . $ben_table;
                $query = $modelName::where('unlock_status', 1)->where('next_level_role_id', 0)->where('next_level_role_id_edit', 999)->where('legacy_import', true)
                  ->where('created_by_local_body_code', $body_code)
                  ->where('block_ulb_code', $request->filter_1);
              } elseif (!empty($request->filter_1) && !empty($request->filter_2)) {
                $appPrefix = "App";
                $modelName = $appPrefix . "\\" . $ben_table;
                $query = $modelName::where('unlock_status', 1)->where('next_level_role_id', 0)->where('next_level_role_id_edit', 999)->where('legacy_import', true)
                  ->where('created_by_local_body_code', $body_code)
                  ->where('block_ulb_code', $request->filter_1)
                  ->where('gp_ward_code', $request->filter_2);
              } else {
                $appPrefix = "App";
                $modelName = $appPrefix . "\\" . $ben_table;

                $query = $modelName::where('unlock_status', 1)->where('next_level_role_id', 0)->where('next_level_role_id_edit', 999)->where('legacy_import', true)
                  ->where('created_by_local_body_code', $body_code);
              }
              $serachvalue = $request->search['value'];
              if (empty($serachvalue)) {
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                  'id', 'created_by_dist_code', 'dob', 'assembly_name',
                  'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                  'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'father_fname', 'father_mname', 'father_lname'
                ]);
                $filterRecords = count($data);
              } else {
                if (is_numeric($serachvalue)) {
                  $ben_id = substr($serachvalue, -7);
                  $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                    $query1->where('id', $ben_id)
                      ->orWhere('bank_code', $serachvalue);
                  });
                  $asd = $query->toSql();
                  //dd($asd);
                  $totalRecords = $query->count();

                  $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                    [
                      'id', 'created_by_dist_code', 'dob', 'assembly_name',
                      'bank_code',
                      'ben_fname', 'ben_lname', 'ben_mname',
                      'block_ulb_name',
                      'gp_ward_name',
                      'bank_ifsc',
                      'village_town_city',
                      'scheme_id',
                      'lot_generated',
                      'payment_count',
                      'next_level_role_id',
                      'ben_lname', 'gender', 'ben_age', 'ben_mname',
                      'father_fname', 'father_mname', 'father_lname'
                    ]
                  );
                } else {
                  $query = $query->where(function ($query1) use ($serachvalue) {
                    $query1->where('ben_fname', 'like', $serachvalue . '%')
                      ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                      ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                      ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                  });
                  $totalRecords = $query->count();
                  $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                    [
                      'id', 'created_by_dist_code', 'dob', 'assembly_name',
                      'bank_code',
                      'ben_fname', 'ben_lname', 'ben_mname',
                      'block_ulb_name',
                      'gp_ward_name',
                      'bank_ifsc',
                      'village_town_city',
                      'scheme_id',
                      'lot_generated',
                      'payment_count',
                      'next_level_role_id',
                      'ben_lname', 'gender', 'ben_age', 'ben_mname',
                      'father_fname', 'father_mname', 'father_lname'
                    ]
                  );
                }
                $filterRecords = count($data);
              }
              return datatables()->of($data)->setTotalRecords($totalRecords)
                ->setFilteredRecords($filterRecords)
                ->skipPaging()
                ->addColumn('view', function ($data) use ($request) {
                  $action = '<a href="benDetailsOapFarmerEdit?id=' . $data->id . '&pr1=' . $request->get('pr1') . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';



                  return $action;
                })
                ->addColumn('id', function ($data) {
                  return $data->getBenidAttribute();
                })
                ->addColumn('name', function ($data) {
                  return $data->getName();
                })->addColumn('father_name', function ($data) {
                  return $data->father_fname . ' ' . $data->father_mname . ' ' . $data->father_lname;
                })
                ->rawColumns(['view', 'id', 'name'])
                ->make(true);
            }

            return view('processApplicationOapFarmerEdit/linelisting_verified_subdiv')->with('duty_level', $duty_level)->with('urban_bodys', $urban_bodys)->with('dist_code', $district_code)->with('pr1', $request->pr1);
          } else {
            $appPrefix = "App";
            $modelName = $appPrefix . "\\" . $ben_table;

            return view('processApplicationOapFarmerEdit/pension_list', ['nhm_employee_details' => $rows]);
          }
        } else if ($mappingLevel == "Block") {


          $duty_level = "BlockVerifier";
          $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
          if (request()->ajax()) {
            $limit = $request->input('length');
            $offset = $request->input('start');
            if (!empty($request->filter_1)) {
              $appPrefix = "App";
              $modelName = $appPrefix . "\\" . $ben_table;

              $query = $modelName::where('unlock_status', 1)->where('next_level_role_id', 0)->where('next_level_role_id_edit', 999)->where('legacy_import', true)
                ->where('created_by_local_body_code', $body_code)
                ->where('gp_ward_code', $request->filter_1);
            } else {
              $appPrefix = "App";
              $modelName = $appPrefix . "\\" . $ben_table;

              $query = $modelName::where('unlock_status', 1)->where('next_level_role_id', 0)->where('next_level_role_id_edit', 999)->where('legacy_import', true)
                ->where('created_by_local_body_code', $body_code);
            }
            $serachvalue = $request->search['value'];
            if (empty($serachvalue)) {
              $totalRecords = $query->count();
              $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                'id', 'created_by_dist_code', 'dob', 'assembly_name',
                'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'father_fname', 'father_mname', 'father_lname'
              ]);
              $filterRecords = count($data);
            } else {
              if (is_numeric($serachvalue)) {
                $ben_id = substr($serachvalue, -7);
                $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                  $query1->where('id', $ben_id)
                    ->orWhere('bank_code', $serachvalue);
                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                  [
                    'id', 'created_by_dist_code', 'dob', 'assembly_name',
                    'bank_code',
                    'ben_fname', 'ben_lname', 'ben_mname',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'ben_lname', 'gender', 'ben_age', 'ben_mname',
                    'father_fname', 'father_mname', 'father_lname'
                  ]
                );
              } else {
                $query = $query->where(function ($query1) use ($serachvalue) {
                  $query1->where('ben_fname', 'ilike', $serachvalue . '%')
                    ->orWhere('block_ulb_name', 'ilike', $serachvalue . '%')
                    ->orWhere('gp_ward_name', 'ilike', $serachvalue . '%')
                    ->orWhere('bank_ifsc', 'ilike', $serachvalue . '%');
                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                  [
                    'id', 'created_by_dist_code', 'dob', 'assembly_name',
                    'bank_code',
                    'ben_fname', 'ben_lname', 'ben_mname',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'ben_lname', 'gender', 'ben_age', 'ben_mname',
                    'father_fname', 'father_mname', 'father_lname'
                  ]
                );
              }
              $filterRecords = count($data);
            }

            //echo "<pre>"; print_r($data); exit;
            return datatables()->of($data)->setTotalRecords($totalRecords)
              ->setFilteredRecords($filterRecords)
              ->skipPaging()
              ->addColumn('view', function ($data) use ($request) {
                $action = '<a href="benDetailsOapFarmerEdit?id=' . $data->id . '&pr1=' . $request->get('pr1') . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
                return $action;
              })
              ->addColumn('id', function ($data) {
                return $data->getBenidAttribute();
              })
              ->addColumn('name', function ($data) {
                return $data->getName();
              })->addColumn('father_name', function ($data) {
                return $data->father_fname . ' ' . $data->father_mname . ' ' . $data->father_lname;
              })
              ->rawColumns(['view', 'id', 'name', 'father_name'])
              ->make(true);
          }

          return view('processApplicationOapFarmerEdit/linelisting_verified')
            ->with('duty_level', $duty_level)->with('gps', $gps)
            ->with('dist_code', $district_code)->with('pr1', $request->pr1);
        }
      } else {
        $approveBtnvisible = 1;

        //Approver_

        if ($mappingLevel == "State") {
          $district_list = Cache::rememberForever('master_districts', function () {
            return District::select(
              'id',
              'district_code',
              'district_name',
              'rch_district_code',
              'is_revenue_district',
              'state_code',
              'district_status'
            )->get();
          });
          $duty_level = "StateApprover";
          // $levels = [
          //   2 => 'Rural',
          //   1 => 'Urban',
          // ];
          if (request()->ajax()) {
            $limit = $request->input('length');
            $offset = $request->input('start');
            $condition = array();
            $condition['next_level_role_id'] = $role_id;
            if (!empty($request->district_code))
              $condition['created_by_dist_code'] = $request->district_code;
            $appPrefix = "App";
            $modelName = $appPrefix . "\\" . $ben_table;
            $query = $modelName::where('next_level_role_id', $role_id)
              ->where($condition);
            //$data->approveBtnvisible = $approveBtnvisible;
            $serachvalue = $request->search['value'];
            if (empty($serachvalue)) {
              $totalRecords = $query->count();
              $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                'id', 'created_by_dist_code', 'dob', 'assembly_name',
                'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
              ]);
              $filterRecords = count($data);
            } else {
              if (is_numeric($serachvalue)) {
                $ben_id = substr($serachvalue, -7);
                $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                  $query1->where('id', $ben_id)
                    ->orWhere('bank_code', $serachvalue);
                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                  [
                    'id', 'created_by_dist_code', 'dob', 'assembly_name',
                    'bank_code',
                    'ben_fname', 'ben_lname', 'ben_mname',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'ben_lname', 'gender', 'ben_age', 'ben_mname',
                    'father_fname', 'father_mname', 'father_lname'
                  ]
                );
              } else {
                $query = $query->where(function ($query1) use ($serachvalue) {
                  $query1->where('ben_fname', 'like', $serachvalue . '%')
                    ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                    ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                    ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                  [
                    'id', 'created_by_dist_code', 'dob', 'assembly_name',
                    'bank_code',
                    'ben_fname', 'ben_lname', 'ben_mname',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'ben_lname', 'gender', 'ben_age', 'ben_mname',
                    'father_fname', 'father_mname', 'father_lname'
                  ]
                );
              }
              $filterRecords = count($data);
            }
            return datatables()->of($data)->setTotalRecords($totalRecords)
              ->setFilteredRecords($filterRecords)
              ->skipPaging()
              ->addColumn('view', function ($data) {
                $action = '<a href="' . route('nhmemployee.showApplicantDetails', $data->id) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';

                if ($data->scheme_id == 17 || $data->scheme_id == 10 || $data->scheme_id == 11 || $data->scheme_id == 2) {
                  $action = $action . '<a href="application-edit?id=' . $data->id . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-warning" target="_blank"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                }

                return $action;
              })
              ->addColumn('check', function ($data) use ($approveBtnvisible) {
                if ($approveBtnvisible)
                  return '<input type="checkbox" name="approvalcheck[]" onchange="document.getElementById(\'bulk_approve\').disabled = !this.checked;" value="' . $data->id . '">';
                else
                  return '';
              })
              ->addColumn('id', function ($data) {
                return $data->getBenidAttribute();
              })
              ->addColumn('name', function ($data) {
                return $data->getName();
              })->addColumn('dist_name', function ($data) use ($district_list) {
                $district_list = $district_list->where('district_code', $data->created_by_dist_code)->first();
                return $district_list->district_name;
              })
              ->rawColumns(['view', 'check', 'id', 'name'])
              ->make(true);
          }
        } else if ($mappingLevel == "District") {
          $duty_level = 'DistrictApprover';
          $levels = [
            2 => 'Rural',
            1 => 'Urban',
          ];
          if (request()->ajax()) {
            $limit = $request->input('length');
            $offset = $request->input('start');
            if (!empty($request->filter_1) && !empty($request->filter_2)) {
              if ($request->filter_1 == '2') {
                $appPrefix = "App";
                $modelName = $appPrefix . "\\" . $ben_table;
                $query = $modelName::where('unlock_status', 1)->where('next_level_role_id', 0)->where('next_level_role_id_edit', $role_id)->where('legacy_import', true)
                  ->where('created_by_dist_code', $district_code)
                  ->where('created_by_local_body_code', $request->filter_2);
              } else {
                $appPrefix = "App";
                $modelName = $appPrefix . "\\" . $ben_table;
                $query = $modelName::where('unlock_status', 1)->where('next_level_role_id', 0)->where('next_level_role_id_edit', $role_id)->where('legacy_import', true)
                  ->where('created_by_dist_code', $district_code)
                  ->where('created_by_local_body_code', $request->filter_2);
              }
            } else {
              $appPrefix = "App";
              $modelName = $appPrefix . "\\" . $ben_table;
              $query = $modelName::where('unlock_status', 1)->where('next_level_role_id', 0)->where('next_level_role_id_edit', $role_id)->where('legacy_import', true)
                ->where('created_by_dist_code', $district_code);
            }
            // $data->approveBtnvisible = $approveBtnvisible;
            $serachvalue = $request->search['value'];
            if (empty($serachvalue)) {
              $totalRecords = $query->count();
              $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                'id', 'created_by_dist_code', 'dob', 'assembly_name',
                'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'father_fname', 'father_mname', 'father_lname'
              ]);
              $filterRecords = count($data);
            } else {
              if (is_numeric($serachvalue)) {
                $ben_id = substr($serachvalue, -7);
                $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                  $query1->where('id', $ben_id)
                    ->orWhere('bank_code', $serachvalue);
                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                  [
                    'id', 'created_by_dist_code', 'dob', 'assembly_name',
                    'bank_code',
                    'ben_fname', 'ben_lname', 'ben_mname',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'ben_lname', 'gender', 'ben_age', 'ben_mname',
                    'father_fname', 'father_mname', 'father_lname'
                  ]
                );
              } else {
                $query = $query->where(function ($query1) use ($serachvalue) {
                  $query1->where('ben_fname', 'ilike', $serachvalue . '%')
                    ->orWhere('block_ulb_name', 'ilike', $serachvalue . '%')
                    ->orWhere('gp_ward_name', 'ilike', $serachvalue . '%')
                    ->orWhere('bank_ifsc', 'ilike', $serachvalue . '%');
                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                  [
                    'id', 'created_by_dist_code', 'dob', 'assembly_name',
                    'bank_code',
                    'ben_fname', 'ben_lname', 'ben_mname',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'ben_lname', 'gender', 'ben_age', 'ben_mname',
                    'father_fname', 'father_mname', 'father_lname'
                  ]
                );
              }
              $filterRecords = count($data);
            }
            return datatables()->of($data)->setTotalRecords($totalRecords)
              ->setFilteredRecords($filterRecords)
              ->skipPaging()
              ->addColumn('view', function ($data) use ($request) {
                $action = '<a href="benDetailsOapFarmerEdit?id=' . $data->id . '&pr1=' . $request->get('pr1') . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';



                return $action;
              })
              ->addColumn('check', function ($data) use ($approveBtnvisible) {
                if ($approveBtnvisible)
                  return '<input type="checkbox" name="approvalcheck[]" onchange="document.getElementById(\'bulk_approve\').disabled = !this.checked;" value="' . $data->id . '">';
                else
                  return '';
              })
              ->addColumn('id', function ($data) {
                return $data->getBenidAttribute();
              })
              ->addColumn('name', function ($data) {
                return $data->getName();
              })->addColumn('father_name', function ($data) {
                return $data->father_fname . ' ' . $data->father_mname . ' ' . $data->father_lname;
              })
              ->rawColumns(['view', 'check', 'id', 'name'])
              ->make(true);
          }

          return view('processApplicationOapFarmerEdit/linelisting_approved')->with('duty_level', $duty_level)
            ->with('levels', $levels)->with('approveBtnvisible', $approveBtnvisible)
            ->with('dist_code', $district_code)->with('pr1', $request->pr1);
        } else {
          if ($is_urban == 1) {
            $duty_level = "ULB";
          } else {
            $duty_level = "Block";
            $appPrefix = "App";
            $modelName = $appPrefix . "\\" . $ben_table;
            $rows = $data = $modelName::where('next_level_role_id', $role_id)->where('created_by_local_body_code', $taluka_code)->orderBy('id', 'desc')->paginate(10);

            return view('processApplicationOapFarmerEdit/linelisting_approved', ['datas' => $rows, 'dist_code' => $district_code]);
          }
        }
      }
    } else {
      return redirect('/')->with('success', 'User Disabled for this scheme');
    }
  }

  public function showApplicantDetails(Request $request)
  {
    if ($request->get('pr1')) {
      if ($request->get('pr1') == "oap_farmer") {
        $ben_table = "PensionOAPFarmer";
        $scheme_id = 13;
      }
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $mappingLevel = $roleObj['mapping_level'];
        $district_code = $roleObj['district_code'];
        $scheme_id = $scheme_id;
        $is_first = $roleObj['is_first'];
        $is_urban = $roleObj['is_urban'];
        $role_id = $roleObj['id'];
        if ($roleObj['is_urban'] == 1) {
          $urban_body_code = $roleObj['urban_body_code'];
          $body_code = $urban_body_code;
        } else {
          $taluka_code = $roleObj['taluka_code'];
          $body_code = $taluka_code;
        }
        break;
      }
    }
    if ($is_active == 1) {
      $id = $request->id;
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      $row = $modelName::where('id', '=', $id)->first();
      $housingrecord = '';
      if ($scheme_id == 13) {
        //$row = Manabik::find($id);          
        $docs = BenDocsOAPFarmer::where('ben_id', $id)->get();
      }
      if ($row->dist_code != "") {
        $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
        $district_name = $district->district_name;
      }
      $block_name = "";
      if ($row->block_ulb_code != "") {
        if ($row->rural_urban_id == 1) {
          $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
          $block_name = $block->urban_body_name;
        } else {
          $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
          $block_name = $block->block_name;
        }
      }
      $gp_name = "";
      if ($row->gp_ward_code != "") {
        if ($row->rural_urban_id == 1) {
          $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
          $gp_name =  $gp_ward->urban_body_ward_name;
        } else {
          $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
          $gp_name =  $gp->gram_panchyat_name;
        }
      }
      $doc_profile_image = DocumentType::get()->where("is_profile_pic", true)->first();
      $doc_profile_image_id = 999;
      if ($doc_profile_image) {
        $doc_profile_image_id = $doc_profile_image->id;
      }
      $approveBtnvisible = 1;
      if ($scheme_id == 13)
        return view('farmer/pension_view_details_edit', [
          'approveBtnvisible' => $approveBtnvisible,
          'pr1' => $request->pr1,
          'row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id
        ]);
    } else {
      return redirect('/')->with('success', 'User Disabled for this scheme');
    }
  }

  public function verifydata(Request $request)
  {

    if ($request->get('pr1')) {
      if ($request->get('pr1') == "oap_farmer") {
        $ben_table = "PensionOAPFarmer";
        $scheme_id = 13;
      }
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $mappingLevel = $roleObj['mapping_level'];
        $district_code = $roleObj['district_code'];
        $scheme_id = $scheme_id;
        $is_first = $roleObj['is_first'];
        $is_urban = $roleObj['is_urban'];
        $role_id = $roleObj['id'];
        if ($roleObj['is_urban'] == 1) {
          $urban_body_code = $roleObj['urban_body_code'];
          $body_code = $urban_body_code;
        } else {
          $taluka_code = $roleObj['taluka_code'];
          $body_code = $taluka_code;
        }
        break;
      }
    }
    if ($is_active == 1) {
      $id = $request->benId;
      if (empty($id)) {
        $return_text = 'Application Id Not Valid';
        $return_msg = array("" . $return_text);
        return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('errors', $return_msg);
      }
      if (!ctype_digit($id)) {
        $return_text = 'Application Id Not Valid';
        $return_msg = array("" . $return_text);
        return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('errors', $return_msg);
      }
      $Verified = "Verified";
      $Rejected = 1;
      $comments = $request->comments;
      $user_id = AuthChecker::getUserId();
      $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id_old)->where('stack_level', $duty->mapping_level)->first();

      if ($_POST['submit'] == 'Verify') {
        $input = ['next_level_role_id_edit' => $role->parent_id, 'comments' => $comments];
        $appPrefix = "App";
        $modelName = $appPrefix . "\\" . $ben_table;
        $condition = array();
        $condition['id'] = $id;
        $condition['scheme_id'] = $scheme_id;
        $condition['created_by_dist_code'] = $district_code;
        $condition['created_by_local_body_code'] = $body_code;
        $condition['next_level_role_id'] = 0;
        $condition['next_level_role_id_edit'] = 999;
        $is_status_updated = $modelName::where($condition)->update($input);
        if ($is_status_updated) {
          return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('message', 'Forwarded Succesfully!');
          // return redirect('workflowwcdEdit?pr1=' . $request->pr1)->with('message', 'Forwarded Succesfully!');
        } else {
          $return_text = 'Error. Please try again';
          $return_msg = array("" . $return_text);
          return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('errors', $return_msg);
          //return redirect()->intended('workflowwcdEdit?pr1=' . $request->pr1)->with('errors', $return_msg);
        }
      } else if ($_POST['submit'] == 'Revert') {
        $input = [
          'unlock_status' => NULL, 'comments' => $comments, 'next_level_role_id_edit' => NULL
        ];
        $appPrefix = "App";
        $modelName = $appPrefix . "\\" . $ben_table;
        $condition = array();
        $condition['id'] = $id;
        $condition['scheme_id'] = $scheme_id;
        $condition['created_by_dist_code'] = $district_code;
        $condition['created_by_local_body_code'] = $body_code;
        $condition['next_level_role_id'] = 0;
        $condition['next_level_role_id_edit'] = 999;
        $is_status_updated = $modelName::where($condition)->update($input);
        //->update($input);
        if ($is_status_updated) {
          return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('message', 'Reverted Succesfully!');

          //return redirect()->intended('workflowwcdEdit?pr1=' . $request->pr1)->with('message', 'Reverted Succesfully!');
        } else {
          $return_text = 'Error. Please try again';
          $return_msg = array("" . $return_text);
          return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('errors', $return_msg);

          //return redirect()->intended('workflowwcdEdit?pr1=' . $request->pr1)->with('errors', $return_msg);
        }
      }
    } else {
      return redirect('/')->with('success', 'User Disabled for this scheme');
    }
  }

  public function approvedata(Request $request)
  {
    if ($request->get('pr1')) {
      if ($request->get('pr1') == "oap_farmer") {
        $ben_table = "PensionOAPFarmer";
        $scheme_id = 13;
      }
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $mappingLevel = $roleObj['mapping_level'];
        $district_code = $roleObj['district_code'];
        $scheme_id = $scheme_id;
        $is_first = $roleObj['is_first'];
        $is_urban = $roleObj['is_urban'];
        $role_id = $roleObj['id'];
        if ($roleObj['is_urban'] == 1) {
          $urban_body_code = $roleObj['urban_body_code'];
          $body_code = $urban_body_code;
        } else {
          $taluka_code = $roleObj['taluka_code'];
          $body_code = $taluka_code;
        }
        break;
      }
    }
    if ($is_active == 1) {
      $id = $request->benId;
      if (empty($id)) {
        $return_text = 'Application Id Not Valid';
        $return_msg = array("" . $return_text);
        return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('errors', $return_msg);
      }
      if (!ctype_digit($id)) {
        $return_text = 'Application Id Not Valid';
        $return_msg = array("" . $return_text);
        return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('errors', $return_msg);
      }
      $Verified = "Verified";
      $Rejected = 1;
      $comments = $request->comments;

      $user_id = AuthChecker::getUserId();
      $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id_old)->where('stack_level', $duty->mapping_level)->first();
      if ($_POST['submit'] == 'Approve') {
        $input = ['unlock_status' => NULL, 'next_level_role_id_edit' => 0, 'comments' => $comments];
        $appPrefix = "App";
        $modelName = $appPrefix . "\\" . $ben_table;
        $condition = array();
        $condition['id'] = $id;
        $condition['scheme_id'] = $scheme_id;
        $condition['created_by_dist_code'] = $district_code;
        $condition['next_level_role_id'] = 0;
        $is_status_updated = $modelName::where($condition)->update($input);
        if ($is_status_updated) {
          return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('message', 'Approved Succesfully!');
          //return redirect()->intended('workflowwcdEdit?pr1=' . $request->pr1)->with('message', 'Approved Succesfully!');
        } else {
          $return_text = 'Error. Please try again';
          $return_msg = array("" . $return_text);
          return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('errors', $return_msg);

          //return redirect()->intended('workflowwcdEdit?pr1=' . $request->pr1)->with('errors', $return_msg);
        }
      } else if ($_POST['submit'] == 'Revert') {
        $input = [
          'unlock_status' => NULL, 'comments' => $comments, 'next_level_role_id_edit' => NULL
        ];
        $appPrefix = "App";
        $modelName = $appPrefix . "\\" . $ben_table;
        $condition = array();
        $condition['id'] = $id;
        $condition['scheme_id'] = $scheme_id;
        $condition['created_by_dist_code'] = $district_code;
        $condition['next_level_role_id'] = 0;
        $is_status_updated = $modelName::where($condition)->update($input);
        //->update($input);
        if ($is_status_updated) {
          return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('message', 'Reverted Succesfully!');

          // return redirect()->intended('workflowwcdEdit?pr1=' . $request->pr1)->with('message', 'Reverted Succesfully!');
        } else {
          $return_text = 'Error. Please try again';
          $return_msg = array("" . $return_text);
          return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('errors', $return_msg);

          //return redirect()->intended('workflowwcdEdit?pr1=' . $request->pr1)->with('errors', $return_msg);
        }
      }
    } else {
      return redirect('/')->with('success', 'User Disabled for this scheme');
    }
  }

  public function MassEmployeeApproval(Request $request)
  {
    //echo $request->get('pr1'); exit;

    if ($request->get('pr1')) {
      if ($request->get('pr1') == "oap_farmer") {
        $ben_table = "PensionOAPFarmer";
        $scheme_id = 13;
      }
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $mappingLevel = $roleObj['mapping_level'];
        $district_code = $roleObj['district_code'];
        $scheme_id = $scheme_id;
        $is_first = $roleObj['is_first'];
        $is_urban = $roleObj['is_urban'];
        $role_id = $roleObj['id'];
        if ($roleObj['is_urban'] == 1) {
          $urban_body_code = $roleObj['urban_body_code'];
          $body_code = $urban_body_code;
        } else {
          $taluka_code = $roleObj['taluka_code'];
          $body_code = $taluka_code;
        }
        break;
      }
    }
    if ($is_active == 1) {
      $id = $request->benId;
      $comments = $request->comments;

      $user_id = AuthChecker::getUserId();
      $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id_old)->where('stack_level', $duty->mapping_level)->first();
      $inputs = request()->input('approvalcheck');
      $in_arr = array();
      foreach ($inputs as $input) {
        array_push($in_arr, $input);
      }
      $input_update = ['next_level_role_id_edit' => 0, 'unlock_status' => NULL];
      $appPrefix = "App";
      $modelName = $appPrefix . "\\" . $ben_table;
      $condition = array();
      $condition['scheme_id'] = $scheme_id;
      $condition['created_by_dist_code'] = $district_code;
      $condition['next_level_role_id'] = 0;
      if (count($in_arr) > 0) {
        $is_pushed = $modelName::whereIn('id', $in_arr)->where($condition)->update($input_update);
      }

      if ($is_pushed == count($inputs)) {
        return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('message', 'Succesfully Approved!');
        //return redirect('workflowwcdEdit?pr1=' . $request->pr1)->with('message', 'Succesfully Approved!');
      } else {
        $return_text = 'Error. Please try again';
        $return_msg = array("" . $return_text);
        return redirect("/oapFarmerApprovedEdit?pr1=" . $request->pr1)->with('errors', $return_msg);

        //return redirect()->intended('workflowwcdEdit?pr1=' . $request->pr1)->with('errors', $return_msg);
      }
    } else {
      return redirect('/')->with('success', 'User Disabled for this scheme');
    }
  }

  public function isAadharValid($num)
  {
    settype($num, "string");
    $expectedDigit = substr($num, -1);
    $actualDigit = $this->CheckSumAadharDigit(substr($num, 0, -1));
    return ($expectedDigit == $actualDigit) ? $expectedDigit == $actualDigit : 0;
  }

  function CheckSumAadharDigit($partial)
  {
    $dihedral = array(
      array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
      array(1, 2, 3, 4, 0, 6, 7, 8, 9, 5),
      array(2, 3, 4, 0, 1, 7, 8, 9, 5, 6),
      array(3, 4, 0, 1, 2, 8, 9, 5, 6, 7),
      array(4, 0, 1, 2, 3, 9, 5, 6, 7, 8),
      array(5, 9, 8, 7, 6, 0, 4, 3, 2, 1),
      array(6, 5, 9, 8, 7, 1, 0, 4, 3, 2),
      array(7, 6, 5, 9, 8, 2, 1, 0, 4, 3),
      array(8, 7, 6, 5, 9, 3, 2, 1, 0, 4),
      array(9, 8, 7, 6, 5, 4, 3, 2, 1, 0)
    );
    $permutation = array(
      array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
      array(1, 5, 7, 6, 2, 8, 3, 0, 9, 4),
      array(5, 8, 0, 3, 7, 9, 6, 1, 4, 2),
      array(8, 9, 1, 6, 0, 4, 3, 5, 2, 7),
      array(9, 4, 5, 3, 1, 2, 6, 8, 7, 0),
      array(4, 2, 8, 6, 5, 7, 3, 9, 0, 1),
      array(2, 7, 9, 3, 8, 0, 6, 4, 1, 5),
      array(7, 0, 4, 6, 9, 1, 3, 2, 5, 8)
    );

    $inverse = array(0, 4, 3, 2, 1, 5, 6, 7, 8, 9);
    settype($partial, "string");
    $partial = strrev($partial);
    $digitIndex = 0;
    for ($i = 0; $i < strlen($partial); $i++) {
      $digitIndex = $dihedral[$digitIndex][$permutation[($i + 1) % 8][$partial[$i]]];
    }
    return $inverse[$digitIndex];
  }
}
