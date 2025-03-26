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
use App\DsPhase;
use App\Helpers\AuthChecker;
use App\Helpers\PermissionManagement;
use App\Workflow;
use App\SchemeStepRank;
use Session;
class MarkingPhaseController extends Controller
{

  public function __construct()
  {

    $this->scheme_id = 20;
    $this->source_type = 'ss_nfsa';
    $this->ben_status = -97;
    $this->doc_type_id = 6;
    $this->middleware('auth');
  }

  public function markdslist(Request $request)
  {
    //dd('ok');
    // return redirect("/")->with('danger', 'Not Allowed');
  
    $designation_id = Auth::user()->designation_id;
    $user_id = Auth::user()->id;
    $is_operator=0;
    $is_verifier=0;
    $is_approver=0;
    $can_perform=0;
    if(AuthChecker::OperatorPermission()){
      $is_operator=1;
      $can_perform=1;
    }
    if(AuthChecker::VerifierPermission()){
      $is_verifier=1;
      $can_perform=1;
    }
    if(AuthChecker::ApproverPermission()){
      $is_approver=1;
      $can_perform=1;
    }
    if($can_perform==0){
      return redirect("/")->with('error', 'Not Allowded');
    }
    $urban_bodys = collect([]);
    $gps = collect([]);
    $scheme_id = $request->scheme_id;
    $type = $request->type;
    $ds_mark_phase = $request->ds_mark_phase;
    if ($type == '') {
      return redirect("/")->with('error', 'Type Not Valid');
    }
    if (!ctype_digit($type)) {
      return redirect("/")->with('error', 'Type Not Valid');
    }
    if ($ds_mark_phase == '') {
      return redirect("/")->with('error', 'Type Not Valid');
    }
    if (!ctype_digit($ds_mark_phase)) {
      return redirect("/")->with('error', 'Type Not Valid');
    }
    if (!in_array($type, array('1', '2', '3'))) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if (!ctype_digit($scheme_id)) {
      return redirect("/")->with('error', 'Scheme Not Valid');
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (empty($scheme_obj)) {
      return redirect("/")->with('danger', 'Scheme Not Found');
    }
    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $ds_phase_arr = DsPhase::where('phase_code', $ds_mark_phase)->first();
    if (empty($ds_phase_arr)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $camp_roman = $ds_phase_arr->phase_des;
    $type_des = 'Mark as Duare Sarkar ' . $camp_roman . ' Camps';
    $district_code = $duty_obj->district_code;
    
    // if($is_approver)
    // {
    // $allow_marking_count = DB::table('pension.ds_mark_can_district')
    // ->where('created_by_dist_code1',$district_code)->where('ds_phase',$ds_mark_phase)->where('is_allowded',1)
    // ->count();
    //   if($allow_marking_count==0){
    //     return redirect("/")->with('danger', 'Marking temporarily suspended.');  
    //   }
    // }
    // if($is_verifier || $is_operator)
    // {
    //   $is_urban = $duty_obj->is_urban;
    //   $blockUlbCode = $is_urban == 1
    //       ? $duty_obj->urban_body_code
    //       : ($is_urban == 2 ? $duty_obj->taluka_code : null);
    // $allow_marking_count = DB::table('pension.ds_mark_can_sdo_bdo')
    // ->where('created_by_local_body_code',$blockUlbCode)->where('ds_phase',$ds_mark_phase)->where('is_allowded',1)
    // ->count();
    //   if($allow_marking_count==0){
    //     return redirect("/")->with('danger', 'Marking temporarily suspended.');  
    //   }
    // }
    // dd('ok');

    return view(
      'markds.index',
      [
        'designation_id' => $designation_id,
        'created_by_dist_code' => $district_code,
        'scheme_id' => $scheme_id,
        'scheme_name' => $scheme_obj->scheme_name,
        'gps' => $gps,
        'urban_bodys' => $urban_bodys,
        'gps' => $gps,
        'district_code' => $district_code,
        'type_des' => $type_des,
        'scheme_id' => $scheme_id,
        'ds_mark_phase' => $ds_mark_phase,
        'camp_roman' => $camp_roman,
        'type' => $type,

      ]
    );
  }
  public function markdslistajax(Request $request)
  {
    if ($request->ajax()) {
      $designation_id = Auth::user()->designation_id;
      $user_id = Auth::user()->id;
      $scheme_id = $request->scheme_id;
      $type = $request->type;
      $ds_mark_phase = $request->ds_mark_phase;
      $application_type = $request->application_type;
      $ds_phase_arr = DsPhase::where('phase_code', $ds_mark_phase)->first();
      $is_verifier=0;
      $is_approver=0;
      $can_perform=0;
      $camp_roman = $ds_phase_arr->phase_des;
      if(AuthChecker::VerifierPermission() || AuthChecker::OperatorPermission()){
        $is_verifier=1;
        $can_perform=1;
      }
      if(AuthChecker::ApproverPermission()){
        $is_approver=1;
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id',$scheme_id)->where('is_active', 1)->first();
      $district_code = $duty_obj->district_code;
      $is_urban = $duty_obj->is_urban;
      $where_cond=' and created_by_dist_code='.$district_code;
      if($is_verifier){
        $blockUlbCode = $is_urban == 1? $duty_obj->urban_body_code: ($is_urban == 2 ? $duty_obj->taluka_code : null);
        $where_cond=$where_cond.' and created_by_local_body_code='.$blockUlbCode;
      }
      $next_level_role_id_operator=SchemeStepRank::getSchemeParentId($scheme_id, 1);
      $next_level_role_id_verifier=SchemeStepRank::getSchemeParentId($scheme_id, 2);
      if($application_type==1){
        $query = " select lb_application_id,is_approved,id, created_by_dist_code, dob, assembly_name,
        bank_code, ben_fname, ben_lname, ben_mname, gender, ben_age, block_ulb_name, gp_ward_name, bank_ifsc, village_town_city,
        scheme_id, lot_generated, payment_count, next_level_role_id, sm_flag, sm_mobile_no,
        is_rejected, mobile_no, no_aadhar, no_mobile, dup_aadhar, dup_mobile, dup_bank, sm_ds_mark, sm_ds_mark_role_id, aadhar_no,sm_ds_mark_vii,sm_ds_mark_viii   from pension.beneficiaries where  scheme_id=".$scheme_id."  ".$where_cond ." and is_rejected=0 and id IN(
    select id from pension.beneficiaries where scheme_id=".$scheme_id."  ".$where_cond ." and lb_application_id IS NULL and next_level_role_id IN (".$next_level_role_id_operator.",".$next_level_role_id_verifier.") 
except
select id from
(

select id from pension.beneficiaries where scheme_id=".$scheme_id." and ds_phase=".$ds_mark_phase." and is_rejected=0
UNION
select  beneficiary_id as id from pension.ds_phase_mark_list where scheme_id=".$scheme_id." and ds_phase=".$ds_mark_phase."
) as P)";

      }
      else if($application_type==2){
        $query = "select lb_application_id,is_approved,id, created_by_dist_code, dob, assembly_name,
        bank_code, ben_fname, ben_lname, ben_mname, gender, ben_age, block_ulb_name, gp_ward_name, bank_ifsc, village_town_city,
        scheme_id, lot_generated, payment_count, next_level_role_id, sm_flag, sm_mobile_no,
        is_rejected, mobile_no, no_aadhar, no_mobile, dup_aadhar, dup_mobile, dup_bank, sm_ds_mark, sm_ds_mark_role_id, aadhar_no,sm_ds_mark_vii,sm_ds_mark_viii   
        from pension.beneficiaries 
        where  scheme_id=".$scheme_id."  ".$where_cond ." 
        and (ds_phase=".$ds_mark_phase."  or cur_mark_ds_phase=".$ds_mark_phase.")";
      }
      else if($application_type==3){
        if(Session::get('dup_type')!=''){
        $dup_type=Session::get('dup_type');
        }
        else{
          $dup_type='';  
        }
        if(Session::get('dup_value')!=''){
        $dup_val=Crypt::decryptString(Session::get('dup_value'));
        }
        else{
          $dup_val='';
        }
        if($dup_type=='bank')
        $wherecond=" where scheme_id=".$scheme_id." and is_rejected=0 and bank_code='".$dup_val."'";
        else if($dup_type=='aadhar')
        $wherecond=" where scheme_id=".$scheme_id." and is_rejected=0 and aadhar_no='".$dup_val."'";
        else if($dup_type=='mobile')
        $wherecond=" where scheme_id=".$scheme_id." and is_rejected=0 and mobile_no='".$dup_val."'";
        Session::forget('dup_btn_visible');
        Session::forget('dup_type');
        Session::forget('dup_value');
        if($dup_val!=''){

        $query = "select lb_application_id,id, created_by_dist_code,created_by_local_body_code, dob, assembly_name,
        bank_code, ben_fname, ben_lname, ben_mname, gender, ben_age, block_ulb_name, gp_ward_name, bank_ifsc, village_town_city,
        scheme_id, lot_generated, payment_count, next_level_role_id, sm_flag, sm_mobile_no,
        is_rejected, mobile_no, no_aadhar, no_mobile, dup_aadhar, dup_mobile, dup_bank, sm_ds_mark, sm_ds_mark_role_id, is_approved
		aadhar_no, cur_mark_ds_phase as mark_ds_phase from pension.beneficiaries ".$wherecond;
        }
        else{
          $query ='';
        }
    //dd($query);
      }
      else if($application_type==4){
        $query = " select lb_application_id,is_approved,id, created_by_dist_code, dob, assembly_name,
        bank_code, ben_fname, ben_lname, ben_mname, gender, ben_age, block_ulb_name, gp_ward_name, bank_ifsc, village_town_city,
        scheme_id, lot_generated, payment_count, next_level_role_id, sm_flag, sm_mobile_no,
        is_rejected, mobile_no, no_aadhar, no_mobile, dup_aadhar, dup_mobile, dup_bank, sm_ds_mark, sm_ds_mark_role_id, aadhar_no,sm_ds_mark_vii,sm_ds_mark_viii   from pension.beneficiaries where  scheme_id=".$scheme_id."  ".$where_cond ." and is_rejected=0 and id IN(
    select id from pension.beneficiaries where scheme_id=".$scheme_id."  ".$where_cond ." and lb_application_id IS NULL and next_level_role_id IN (".$next_level_role_id_operator.") 
except
select id from
(

select id from pension.beneficiaries where scheme_id=".$scheme_id." and ds_phase=".$ds_mark_phase." and is_rejected=0
UNION
select  beneficiary_id as id from pension.ds_phase_mark_list where scheme_id=".$scheme_id." and ds_phase=".$ds_mark_phase."
) as P)";
 $query='';

      }
       if($query!=''){  
      $data = DB::select($query);
       }
       else{
        $data = array();
       }

      //print_r($data);die;
      return datatables()->of($data)
          ->addIndexColumn()
          ->addColumn('view', function ($data) use ($ds_mark_phase, $camp_roman, $type,$application_type,$is_approver,$is_verifier,$district_code,$blockUlbCode) {
            $action = '<a href="Viewmarkds?ds_mark_phase=' . $ds_mark_phase . '&type=' . $type . '&id=' . $data->id  . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> View</a>';
  
           
            
              if ($application_type==1 || $application_type==3 || $application_type==4) {
                if($application_type==1){
                  $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-' . $data->id . '" value="' . $data->id . '">Mark as ' . $camp_roman . ' Camps</button>';  
                }
                if($application_type==4){
                  $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-' . $data->id . '" value="' . $data->id . '">Mark as ' . $camp_roman . ' Camps</button>';  
                }
                if($application_type==3){
                  if(!is_null($data->lb_application_id)){
                    $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;LB 60 Case';
                  }
                  else if($data->next_level_role_id==0){
                    $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Approved Case';
                  }
                  else if($data->mark_ds_phase==$ds_mark_phase){
                    $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Already Marked as Duare Sarkar ' . $camp_roman . ' Camps';
                  }
                  else {
                    if($is_verifier){
                      if($data->created_by_local_body_code==$blockUlbCode){
                        $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-' . $data->id . '" value="' . $data->id . '">Mark as ' . $camp_roman . ' Camps</button>';  
                      }
                      else{
                        $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Related to Other Block/Sub Division'; 
                      }

                    }
                    if($is_approver){
                      if($data->created_by_dist_code==$district_code){
                        $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-' . $data->id . '" value="' . $data->id . '">Mark as ' . $camp_roman . ' Camps</button>';  
                      }
                      else{
                        $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Related to Other District'; 
                      }

                    }
                    
                  }
                }
              
              } else if ($application_type==2) {
                $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Already Marked as Duare Sarkar ' . $camp_roman . ' Camps';
              }
            
  
  
  
  
            return $action;
          })->addColumn('check', function ($data) use ($designation_id) {
            return '';
          })
          ->addColumn('id', function ($data) {
            return $data->id;
          })
          ->addColumn('name', function ($data) {
            return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
          })->addColumn('bank_ifsc', function ($data) {
            if (!empty($data->bank_ifsc)) {
              $bank_ifsc = trim($data->bank_ifsc);
            } else {
              $bank_ifsc = '';
            }
            return $bank_ifsc;
          })->addColumn('bank_code', function ($data) {
            if (!empty($data->bank_code)) {
              $bank_code = trim($data->bank_code);
            } else {
              $bank_code = '';
            }
            return $bank_code;
          })->addColumn('mobile_no', function ($data) {
            if (!empty($data->mobile_no)) {
              $ben_mobile_no = trim($data->mobile_no);
            } else {
              $ben_mobile_no = '';
            }
            return $ben_mobile_no;
          })->addColumn('aadhaar_no', function ($data) {
            if (!empty($data->aadhaar_no)) {
              $ben_aadhaar_no = trim($data->aadhaar_no);
            } else {
              $ben_aadhaar_no = '';
            }
            return $ben_aadhaar_no;
          })
          ->rawColumns(['view', 'id', 'name', 'aadhaar_no', 'bank_ifsc', 'bank_code', 'bank_ifsc', 'bank_ifsc', 'check'])
          ->make(true);
        
      }
        
        
  
  }
  public function Viewmarkds(Request $request)
  {
    // return redirect("/")->with('danger', 'Not Allowed');
    try {
      $designation_id = Auth::user()->designation_id;
      $user_id = Auth::user()->id;
      $is_verifier=0;
      $is_approver=0;
      $can_perform=0;
      if(AuthChecker::VerifierPermission() || AuthChecker::OperatorPermission()){
        $is_verifier=1;
        $can_perform=1;
      }
      if(AuthChecker::ApproverPermission()){
        $is_approver=1;
        $can_perform=1;
      }
      if($can_perform==0){
        return redirect("/")->with('error', 'Not Allowded');
      }
      
      $scheme_id = $request->scheme_id;
      $type = $request->type;
      $ds_mark_phase = $request->ds_mark_phase;
      $id = $request->id;
      // dd($id);
      if (empty($request->id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }
      if ($type == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($type)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if ($ds_mark_phase == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($ds_mark_phase)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!in_array($type, array('1', '2', '3', '4'))) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $ds_phase_arr = DsPhase::where('phase_code', $ds_mark_phase)->first();
      if (empty($ds_phase_arr)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $camp_roman = $ds_phase_arr->phase_des;
      $type_des = 'Mark as Duare Sarkar ' . $camp_roman . ' Camps';
      $district_code = $duty_obj->district_code;
      
      if($is_approver)
      {
      $allow_marking_count = DB::table('pension.ds_mark_can_district')
      ->where('created_by_dist_code',$district_code)->where('ds_phase',$ds_mark_phase)->where('is_allowded',1)
      ->count();
        if($allow_marking_count==0){
          return redirect("/")->with('danger', 'Marking temporarily suspended.');  
        }
      }
      if($is_verifier)
      {
        $is_urban = $duty_obj->is_urban;
        $blockUlbCode = $is_urban == 1
            ? $duty_obj->urban_body_code
            : ($is_urban == 2 ? $duty_obj->taluka_code : null);
      $allow_marking_count = DB::table('pension.ds_mark_can_sdo_bdo')
      ->where('created_by_local_body_code',$blockUlbCode)->where('ds_phase',$ds_mark_phase)->where('is_allowded',1)
      ->count();
        if($allow_marking_count==0){
          return redirect("/")->with('danger', 'Marking temporarily suspended.');  
        }
      }
      $next_level_role_id_verifier=SchemeStepRank::getSchemeParentId($scheme_id, 2);
      if ($type == 1) {
        $query = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)->whereNull('ds_phase')->where('id', $id)->where('created_by_dist_code', $district_code)->whereRaw(' (next_level_role_id IS NULL or next_level_role_id=' . $next_level_role_id_verifier . ') ')->where('is_samadhan', false);
      } else {
        $query = DB::table('pension.beneficiaries')->where('scheme_id',$scheme_id)->where('id', $id)->where('is_rejected', 0);
      }
      $row = $query->first();
      // dd( $row);
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      //dd($row->aadhar_no);
      $already_mark =0;
      if(!is_null($row->lb_application_id)){
        $already_mark =1;
      }
      else if($row->created_by_local_body_code!=$blockUlbCode){
        $already_mark =1;
      }

      else
      $already_mark = DB::table('pension.ds_phase_mark_list')->where('scheme_id',$scheme_id)->where('beneficiary_id', $id)->where('ds_phase',$ds_mark_phase)->count('beneficiary_id');

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
      $row->block_name = $block_name;
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

      $row->gp_name = $gp_name;
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $row->created_by_dist_code)->orderBy('document_type')->get();
      return view(
        'markds.Viewmark',
        [
          'designation_id' => $designation_id,
          'row' => $row,
          'id' => $id,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'docs' => $docs,
          'scheme_id' => $scheme_id,
          'ds_mark_phase' => $ds_mark_phase,
          'camp_roman' => $camp_roman,
          'type' => $type,
          'already_mark' => $already_mark,

        ]
      );
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function DsmarkPost(Request $request)
  {
    // return redirect("/")->with('error', 'Not Allowded');
    try {
      //dd($request);
      $designation_id = Auth::user()->designation_id;
      $user_id = Auth::user()->id;
      $is_verifier=0;
      $is_approver=0;
      $can_perform=0;
      if(AuthChecker::VerifierPermission() || AuthChecker::OperatorPermission()){
        $is_verifier=1;
        $can_perform=1;
      }
      if(AuthChecker::ApproverPermission()){
        $is_approver=1;
        $can_perform=1;
      }
      if($can_perform==0){
        return redirect("/")->with('error', 'Not Allowded');
      }
      if (empty($request->beneficiary_id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }

      if (empty($request->scheme_id)) {
        return redirect("/")->with('danger', 'Scheme ID Not Found');
      }
      $scheme_id = $request->scheme_id;
      $id = $request->beneficiary_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed...');
      }
      $type = $request->type;
      if ($type == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($type)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!in_array($type, array('1', '2', '3', '4'))) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $ds_mark_phase = $request->ds_mark_phase;
      if ($ds_mark_phase == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($ds_mark_phase)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      $ds_phase_arr = DsPhase::where('phase_code', $ds_mark_phase)->first();
      if (empty($ds_phase_arr)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if ($type == 1) {
        if (!in_array($designation_id, array( 'Special LAO'))) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
      }
      $url="/markdslist?scheme_id=".$scheme_id."&type=".$type."&ds_mark_phase=".$ds_mark_phase;
      
      if (trim($request->ds_registration_no)=='') {
        return redirect($url)->with('error', 'Camp Registration No. Required');
      }
      if (strlen(trim($request->ds_registration_no))<24) {
        return redirect($url)->with('error', 'Camp Registration No. Not Valid');

      }
      if (trim($request->ds_date)=='') {
        return redirect($url)->with('error', 'Camp Date Required');
      }
      $startDateObj = strtotime(date('Y-m-d', strtotime($request->ds_date) ));
      $currentDateObj = strtotime($request->ds_date);
      if($startDateObj > $currentDateObj) {
        return redirect($url)->with('error', 'Camp Date Not Valid');

      }
      //dd('ok');
      
      $camp_roman = $ds_phase_arr->phase_des;
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $is_urban = $duty_obj->is_urban;
      if($is_verifier){
        $blockUlbCode = $is_urban == 1? $duty_obj->urban_body_code: ($is_urban == 2 ? $duty_obj->taluka_code : null);
      }
      else{
        $blockUlbCode=NULL;
      }
      $next_level_role_id_operator=SchemeStepRank::getSchemeParentId($scheme_id, 1);
      $next_level_role_id_verifier=SchemeStepRank::getSchemeParentId($scheme_id, 2);
      $condition = array();
      $condition['id'] = $id;

      

   
      $is_error = 0;
      if($is_approver)
      {
      $allow_marking_count = DB::table('pension.ds_mark_can_district')
      ->where('created_by_dist_code',$district_code)->where('ds_phase',$ds_mark_phase)->where('is_allowded',1)
      ->count();
        if($allow_marking_count==0){
          return redirect("/")->with('danger', 'Marking temporarily suspended.');  
        }
      }
      if($is_verifier)
      {
        $is_urban = $duty_obj->is_urban;
        $blockUlbCode = $is_urban == 1
            ? $duty_obj->urban_body_code
            : ($is_urban == 2 ? $duty_obj->taluka_code : null);
      $allow_marking_count = DB::table('pension.ds_mark_can_sdo_bdo')
      ->where('created_by_local_body_code',$blockUlbCode)->where('ds_phase',$ds_mark_phase)->where('is_allowded',1)
      ->count();
        if($allow_marking_count==0){
          return redirect("/")->with('danger', 'Marking temporarily suspended.');  
        }
      }
    




        $c_time = date('Y-m-d H:i:s', time());
        DB::beginTransaction();

        $in_pension_id = 'ARRAY[' . "'$request->beneficiary_id'" . ']';
        $comments = NULL;
        if(trim($request->ds_registration_no)!=''){
        $ds_registration_no=trim($request->ds_registration_no);
        }
        else{
          $ds_registration_no=NULL; 
        }
        if(trim($request->ds_date)!=''){
          $ds_date=trim($request->ds_date);
          }
          else{
            $ds_date=NULL; 
          }
        // dd($id);
        $is_inserted_status_arr = DB::select("select pension.dsmark_for_sm(in_next_level_role_id_operator => $next_level_role_id_operator,in_next_level_role_id_verifier => $next_level_role_id_verifier,in_ds_mark_phase => $ds_mark_phase,in_application_id => $request->beneficiary_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_ip_address => '" . $request->ip() . "',in_op_type => 'SMDSMARK', in_custom_comment => '" . $comments . "',in_ds_registration_no => '" . $ds_registration_no . "',in_ds_date => '" . $ds_date . "')");
        //dd($is_inserted_status_arr);
        $is_inserted_status = $is_inserted_status_arr[0]->dsmark_for_sm;
       // dd($is_inserted_status);
        if ($is_inserted_status == 1) {
          DB::commit();
          $errors = array();
          $return_text = 'Beneficiary with  Id:' . $id . ' has been marked as Duare Sarkar ' . $camp_roman . ' Camps';
          if($type==3){
            return redirect("/jb-pension?type=1&scheme_id='" . encrypt($scheme_id)."'" )->with('success', $return_text);

          }
          else
          return redirect("/markdslist?type=" . $type . "&ds_mark_phase=" . $ds_mark_phase . "&scheme_id=" . $scheme_id)->with('success', $return_text);
        } else if ($is_inserted_status == 10) {
          DB::rollback();
          $errors = array();
          $errorMsg = 'Total DS mark  Applications  exceeds the Quota';
          array_push($errors, $errorMsg);
        } else {
          DB::rollback();
          $errors = array();
          $errorMsg = 'Error.. Please try different.';
          array_push($errors, $errorMsg);
        }
      


     
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  function selectscheme(Request $request)
  {
    $user_id = AuthChecker::getUserId();
    $phase_list = DsPhase::whereIn('phase_code', [11])->get();
    $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where  is_active=1 and id IN (2,10,11) and id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
    return view(
      'markds.selectScheme',
      [
        'phase_list' => $phase_list,
        'scheme_list' => $schemes
      ]
    );
  }

}



