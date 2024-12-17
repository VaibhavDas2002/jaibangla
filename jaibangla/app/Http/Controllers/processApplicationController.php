<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\District;
use App\Scheme;
use Redirect;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Validator;
use DateTime;
use Config;
use App\Configduty;
use Maatwebsite\Excel\Facades\Excel;
use App\DataSourceCommon;

use App\getModelFunc;
use Illuminate\Support\Facades\Crypt;
use App\RejectRevertReason;
use App\AadharDuplicateTrail;
use App\SubDistrict;
use App\Taluka;
use App\DocumentType;
use Illuminate\Support\Facades\Storage;
use App\SchemeDocMap;
use File;
use App\BankDetails;
use App\UrbanBody;
use App\Ward;
use App\GP;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\AcceptRejectInfo;
use App\MapLavel;
use App\BenDocs;
use App\BlkUrbanlEntryMapping;

class processApplicationController extends Controller
{

    public function __construct()
    {

         $this->scheme_id = 20;
        $this->source_type = 'ss_nfsa';
        $this->ben_status = -97;
        $this->doc_type_id = 6;
        
    }
    
    public function list(Request $request)
    {
      try{
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      //dd($designation_id_old);
      $user_id = Auth::user()->id;
  
      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
     
     //dd($designation_id_old);
      $type_des='Beneficiary yet to Verified';
     
      //dd($type_des);
      $district_code = $duty_obj->district_code;
      $urban_bodys = collect([]);
      $gps = collect([]);
      $district_list_obj = collect([]);
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      if ($duty_obj->mapping_level == "Subdiv") {
        $created_by_local_body_code = $duty_obj->urban_body_code;
        $is_rural = 1;
        $verifier_type = 'Subdiv';
        $gps = collect([]);
        $urban_body_code = $duty_obj->urban_body_code;
        $urban_bodys = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();
        $urban_body_codes = [];
        $i = 0;
        foreach ($urban_bodys as $urban_body) {
  
          $urban_body_codes[$i] = $urban_body->urban_body_code;
          $i++;
        }
      }
      if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
        $is_rural = 2;
        $verifier_type = 'Block';
        $urban_bodys = collect([]);
        $taluka_code = $duty_obj->taluka_code;
        $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
      }
      if ($duty_obj->mapping_level == "District") {
        $district_list_obj = District::get();
        $verifier_type = 'District';
        $is_rural = NULL;
        $created_by_local_body_code = NULL;
      }
     // $foo = app(DynamicModelFactory::class)->create(PensionCommonModel::class,$schema);
        
        $query = DB::table($schema . '.beneficiary')
          ->whereNull('next_level_role_id')->whereRaw(' (dup_bank=0 or dup_bank IS NULL) and (dup_aadhar=0 or dup_aadhar IS NULL) and (dup_mobile=0 or dup_mobile IS NULL) and (no_aadhar=0 or no_aadhar IS NULL) and (no_mobile=0 or no_mobile IS NULL)');
          if ($designation_id_old == 'Verifier') {
            $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
         
          }
      
        if ($duty_obj->mapping_level == "Subdiv") {
          if (!empty($request->block_ulb_code)) {
            $query = $query->where('block_ulb_code', $request->block_ulb_code);
          }
        }
        if (!empty($request->gp_ward_code)) {
          $query = $query->where('gp_ward_code', $request->gp_ward_code);
        }
        if ($designation_id_old == 'Approver') {  
        
        }
        $data = $query->orderBy('id', 'desc')->paginate(100);
      return view(
        'processApplication.linelisting',
        [
          'designation_id_old' => $designation_id_old,
          'verifier_type' => $verifier_type,
          'created_by_local_body_code' => $created_by_local_body_code,
          'is_rural' => $is_rural,
          'scheme_id' => $scheme_id,
          'scheme_name' => $scheme_obj->scheme_name,
          'gps' => $gps,
          'urban_bodys' => $urban_bodys,
          'gps' => $gps,
          'district_code' => $district_code,
          'type_des' => $type_des,
          'data' => $data
        ]
      );
    }catch (\Exception $e) {
      dd($e);
    }
    }
  public function applicantdetails(Request $request)
   {
    if (empty($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Found');
    }
    if (!is_numeric($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    if (empty($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Found');
    }
    if (!is_numeric($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Valid');
    }
    
    $approveBtnvisible = 0;
    $verifyBtnvisible = 0;
    $user_id = Auth::user()->id;
    $reject_revert_cause_list = RejectRevertReason::where('status', true)->get();
    $id = $request->id;
    $scheme_id = $request->scheme_id;
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (empty($scheme_obj)) {
      return redirect("/")->with('danger', 'Scheme Not Found');
    }
    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
   }
   $condition_arr=array();
   $condition_arr['id']=$id;
   if ($duty_obj->mapping_level == "Department") {
    $created_by_local_body_code = NULL;
    $created_by_dist_code = NULL;
   }
   else{
    $condition_arr['created_by_dist_code']=$duty_obj->district_code;
    $created_by_dist_code = $duty_obj->district_code;
    if ($duty_obj->mapping_level == "Subdiv") {
      $created_by_local_body_code = $duty_obj->urban_body_code;
      $condition_arr['created_by_local_body_code']=$created_by_local_body_code;

    }
    else if ($duty_obj->mapping_level == "Block") {
      $created_by_local_body_code = $duty_obj->taluka_code;
      $condition_arr['created_by_local_body_code']=$created_by_local_body_code;

    }
    else if ($duty_obj->mapping_level == "District") {
      $created_by_local_body_code = NULL;
    }
  }
  $row = DB::table($schema . '.beneficiary')
  ->where($condition_arr)->first();
  if (empty($row)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
  if ($row->scheme_id != $scheme_id) {
      return redirect("/")->with('danger', 'Not Allowed');
  }
    $is_state_login=0;
    $district_state_name = '';
    $urban_code_state_name = '';
    $block_subdiv_state_name = '';
    
   

     $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $created_by_dist_code)->orderBy('document_type')->get();

    if ($row->dist_code != "") {
      $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
      $district_name = $district->district_name;
    }
    $block_name = "";
    if ($row->block_ulb_code != "") {
      if ($row->rural_urban_id == 1) {
        $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
        if (!empty($block)) {
          $block_name = $block->urban_body_name;
        }
      } else {
        if (!empty($row->block_ulb_code)) {
          $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
          if (!empty($block)) {
            $block_name = $block->block_name;
          } else {
            $block_name = '';
          }
        } else {
          $block_name = '';
        }
      }
    }
    $row->block_name=$block_name;
    $gp_name = "";
    if ($row->gp_ward_code != "") {
      if ($row->rural_urban_id == 1) {
        $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
        if (!empty($gp_ward)) {
          $gp_name =  $gp_ward->urban_body_ward_name;
        }
      } else {
        $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
        if (!empty($gp)) {
          $gp_name =  $gp->gram_panchyat_name;
        }
      }
    }
    $row->gp_name=$gp_name;

    $doc_profile_image = DocumentType::get()->where("is_profile_pic", true)->first();
    $doc_profile_image_id = 999;
    if ($doc_profile_image) {
      $doc_profile_image_id = $doc_profile_image->id;
    }
    $scheme_capacity_arr = array();
    $scheme_capacity_arr = Helper::getCapacity($scheme_id, $created_by_dist_code);
      if ($scheme_capacity_arr['visible'] == 1) {
        if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
          $approveBtnvisible = 0;
        } else {
          $approveBtnvisible = 1;
        }
      } else {
        $approveBtnvisible = 1;
      }
    $is_dup_msg=array(); 
    if($row->dup_bank==1){
      array_push($is_dup_msg,'Duplicate Bank Account Number..');   
    }
    if($row->dup_aadhar==1){
      array_push($is_dup_msg,'Duplicate Aadhaar Number.');     
    }
    if($row->dup_mobile==1){
      array_push($is_dup_msg,'Duplicate Mobile Number.');     
    }
    if($row->no_aadhar==1){
      array_push($is_dup_msg,'Aadhaar Number Incorrect.');     
    }
    if($row->no_mobile==1){
      array_push($is_dup_msg,'Mobile Number Incorrect.');     
    }
      return view('processApplication/pension_view_details', [
        'is_state_login' => $is_state_login,
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'verifyBtnvisible' => $verifyBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr, 
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name, 
        'gp_name' => $gp_name, 
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list,
        'is_dup_msg' => $is_dup_msg
      ]); 
  }
  public function verifydata(Request $request)
  {
   // dd($request);
    if (empty($request->benId)) {
      return redirect("/")->with('danger', 'Applicant ID Not Found');
    }
    if (!is_numeric($request->benId)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    if (empty($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Found');
    }
    if (!is_numeric($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Valid');
    }   
    $user_id = Auth::user()->id;
    $reject_revert_cause_list = RejectRevertReason::where('status', true)->get();
    $id = $request->benId;
    $scheme_id = $request->scheme_id;
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (empty($scheme_obj)) {
      return redirect("/")->with('danger', 'Scheme Not Found');
    }
    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
   }
   $condition_arr=array();
   $condition_arr['id']=$id;
   if ($duty_obj->mapping_level == "Department") {
    $created_by_local_body_code = NULL;
    $created_by_dist_code = NULL;
   }
   else{
    $condition_arr['created_by_dist_code']=$duty_obj->district_code;
    $created_by_dist_code = $duty_obj->district_code;
    if ($duty_obj->mapping_level == "Subdiv") {
      $created_by_local_body_code = $duty_obj->urban_body_code;
      $condition_arr['created_by_local_body_code']=$created_by_local_body_code;

    }
    else if ($duty_obj->mapping_level == "Block") {
      $created_by_local_body_code = $duty_obj->taluka_code;
      $condition_arr['created_by_local_body_code']=$created_by_local_body_code;

    }
    else if ($duty_obj->mapping_level == "District") {
      $created_by_local_body_code = NULL;
    }
  }
  $row = DB::table($schema . '.beneficiary')
  ->where($condition_arr)->first();
  if (empty($row)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
  if ($row->scheme_id != $scheme_id) {
      return redirect("/")->with('danger', 'Not Allowed');
  }
    $c_time = date('Y-m-d H:i:s', time());
    $Verified = "Verified";
    $Rejected = 1;
    $comments = $request->comments;
    $accept_reject_model = new AcceptRejectInfo;
    $accept_reject_model->created_at = $c_time;
    $accept_reject_model->application_id = $id;
    $accept_reject_model->scheme_id = $scheme_id;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->comment_message = $comments;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->created_by_dist_code = $created_by_dist_code;
    $accept_reject_model->created_by_local_body_code = $created_by_local_body_code;
    $accept_reject_model->ip_address = request()->ip();
    $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id_old)->where('stack_level', $duty_obj->mapping_level)->first();
    $next_level_role_id = $role->parent_id;
    
    if ($_POST['submit'] == 'Verify') {
      //dd($row->bank_code);
      if ($scheme_id == 10 || $scheme_id == 11 || $scheme_id == 2) {
        $allowded_arr = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('block_ulb_code',   $created_by_local_body_code)->where('district_code',  $created_by_dist_code)->first();
        $verification_allowded = intval($allowded_arr->main_verification);
        //dd($verification_allowded);
        if ($verification_allowded == 0) {
          return redirect("/")->with('danger', 'Verification is temporarily suspended');
        }
      }
    if($row->dup_bank==1){
      return redirect('processApplication?scheme_id='.$scheme_obj->id)->with('error', 'Duplicate Bank Account Number..');
    }
    if($row->dup_aadhar==1){
      return redirect('processApplication?scheme_id='.$scheme_obj->id)->with('error', 'Duplicate Aadhaar Number.');
    }
    if($row->dup_mobile==1){
      return redirect('processApplication?scheme_id='.$scheme_obj->id)->with('error', 'Duplicate Mobile Number.');
    }
    if($row->no_aadhar==1){
      return redirect('processApplication?scheme_id='.$scheme_obj->id)->with('error', 'Aadhaar Number Incorrect.');  
    }if($row->no_mobile==1){
      return redirect('processApplication?scheme_id='.$scheme_obj->id)->with('error', 'Mobile Number Incorrect.');
    }
      $accept_reject_model->op_type = 'AV';
    

      $input = ['is_verified' => 1,'next_level_role_id' => $next_level_role_id, 
      'comments' => $comments,'verification_date' => $c_time,'verified_by' => $user_id];

      DB::beginTransaction();
     
      $is_status_updated = DB::table($schema . '.beneficiary')->where('id', $id)->where('created_by_dist_code', $created_by_dist_code)->whereraw(" (dup_bank=0 or dup_bank IS NULL) and (dup_aadhar=0 or dup_aadhar IS NULL) and (dup_mobile=0 or dup_mobile IS NULL) and (no_aadhar=0 or no_aadhar IS NULL) and (no_mobile=0 or no_mobile IS NULL)")->whereNotNull('bank_code')->whereNull('next_level_role_id')->update($input);
      
      $is_saved_log = $accept_reject_model->save();
      //dump($is_status_updated); dd($is_saved_log);
      if ($is_status_updated &&  $is_saved_log) {
        DB::commit();
        return redirect('processApplication?scheme_id='.$scheme_obj->id)->withInput()->with('message', 'Forwarded Succesfully!');
      } else {
        DB::rollback();
        return redirect('processApplication?scheme_id='.$scheme_obj->id)->with('message', 'Error! Please try again...');
      }
    } else if ($_POST['submit'] == 'Revert') {
      
      $accept_reject_model->op_type = 'AREVERT';
    

      $input = ['next_level_role_id' => NULL,'is_verified' => 0,'is_approved' => 0,'is_reverted' => 1];

      DB::beginTransaction();
     
      $is_status_updated = DB::table($schema . '.beneficiary')->where('id', $id)->where('created_by_dist_code', $created_by_dist_code)->update($input);
      
      $is_saved_log = $accept_reject_model->save();
      //dd($is_status_updated);
      if ($is_status_updated &&  $is_saved_log) {
        DB::commit();
        return redirect('processApplication?scheme_id='.$scheme_obj->id)->withInput()->with('message', 'Reverted Succesfully!');
      } else {
        DB::rollback();
        return redirect('processApplication?scheme_id='.$scheme_obj->id)->with('message', 'Error! Please try again.');
      }
    }else if ($_POST['submit'] == 'Reject') {
      $is_state_login=0;
      try {
      $accept_reject_model->op_type = 'AR';
      $input = [
        'verification_rejected' => $Rejected, 'comments' => $comments, 'next_level_role_id' => -1,'is_approved' => 2,'is_verified' => 2,'is_rejected' => 1,
        'rejected_date' => $c_time,'rejected_by' => $user_id,'is_clean' => 10
      ];
      $appPrefix = "App";
     // $modelName = $appPrefix . "\\" . $ben_table;
      DB::beginTransaction();
      if ($is_state_login == 1) {
        $is_status_updated =  DB::table($schema . '.beneficiary')->where('id', $id)->where('is_state', TRUE)->update($input);
      } else {
        $is_status_updated =  DB::table($schema . '.beneficiary')->where('id', $id)->where('created_by_dist_code', $created_by_dist_code)->update($input);
      }
      $is_saved_log = $accept_reject_model->save();
      $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
        if (in_array($scheme_id, $scheme_dedup_list)) {
        $free_pending_bank_duplicate_arr = DB::select("select ".$schema.".free_pending_bank_duplicate_data(in_scheme_id => ".$scheme_id.", in_district_code => ".$created_by_dist_code.")");
                //dd($free_pending_bank_duplicate_arr);
        $free_pending_bank_duplicate_data=$free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
        if(!empty(trim($row->mobile_no))){
          $sp_mobile=$row->mobile_no;
      }
      else{
          $sp_mobile=0;  
      }
        $reject_dup_adjustment_arr = DB::select("select ".$schema.".reject_dup_adjustment(
          in_old_bank_ifsc => '".$row->bank_ifsc."', 
          in_old_bank_code => '".$row->bank_code."', 
          in_old_aadhar_no => '".$row->aadhar_no."', 
          in_old_mobile_no => ".$sp_mobile."
          )");
          $reject_dup_adjustment=$reject_dup_adjustment_arr[0]->reject_dup_adjustment;
        }
        else{
          $reject_dup_adjustment=1;
          $free_pending_bank_duplicate_data=1;
        }
      if ($is_status_updated && $is_saved_log && $free_pending_bank_duplicate_data && $reject_dup_adjustment) {
        DB::commit();
        return redirect('processApplication?scheme_id='.$scheme_obj->id)->withInput()->with('message', 'Rejected Succesfully!');
      } else {
        DB::rollback();
        return redirect('processApplication?scheme_id='.$scheme_obj->id)->with('message', 'Error! Please try again.');
      }
    }
    catch (\Exception $e) {
      dd($e);
      return redirect('processApplication?scheme_id='.$scheme_obj->id)->with('message', 'Error! Please try again.');
    }
    }
  }
}
