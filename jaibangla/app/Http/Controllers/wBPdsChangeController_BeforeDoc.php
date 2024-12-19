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
use App\Helpers\AuthChecker;

class wBPdsChangeController extends Controller
{

    public function __construct()
    {

        // $this->scheme_id = 20;
        $this->source_type = 'ss_nfsa';
        $this->ben_status = -97;
        $this->doc_type_id = 6;
    }
    
   
    function selectschemeOp(Request $request)
    {
        $this->middleware('auth');
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $userId = Auth::user()->id;
        $type = $request->type;
        if(!in_array($type,array(1,2))){
          return redirect("/")->with('danger', 'Input Not Valid');
        }
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (2,10,11) and  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
        //dd($scheme_list);
        return view(
            'wbpds.selectSchemeOp',
            [
                'scheme_list' => $scheme_list,
                'designation_id_old' => $designation_id_old,
                'type' => $type
            ]
        );
    }
    public function namemismatchdlist(Request $request)
    {
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      //dd($designation_id_old);
      $user_id = AuthChecker::getUserId();
  
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
      $type = $request->type;
      if(!in_array($type,array(1,2))){
        return redirect("/")->with('danger', 'Input Not Valid');
      }
      if($type==1){
        $type_des='Beneficiary with name Validation Failed from WBPDS';
      }
      else if($type==2){
        $type_des='Beneficiary with Name Validation Failed from WBPDS';

      }
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
      if (request()->ajax()) {
        $limit = $request->input('length');
        $offset = $request->input('start');
        $application_type=$request->application_type;
        $process_type=$request->process_type;
        //dd($process_type);
        $query = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)->whereIn('next_level_role_id',array(0,-57));
          if ($designation_id_old == 'Verifier') {
            $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
            if (!empty($application_type)) {
              if($application_type==1)
               $query = $query->whereNull('next_level_role_id_aadhar_validation');
              if($application_type==2)
               $query = $query->where('next_level_role_id_aadhar_validation', 1);
              if($application_type==3)
               $query = $query->where('next_level_role_id_aadhar_validation', 0);
               if($application_type==4)
               $query = $query->where('next_level_role_id', -57);
            }
          }
        if ($type == 1) {
          if($application_type!=3){
            $query = $query->where('acc_validated_aadhar', -1);
          }
         
        }
        if ($type == 2) {
          if($application_type!=3){
          $query = $query->where('acc_validated_aadhar', -2);
          }
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
          if ($application_type!='') {
            if($application_type==1)
             $query = $query->where('next_level_role_id_aadhar_validation', 1);
            if($application_type==3)
             $query = $query->where('next_level_role_id_aadhar_validation', 0);
             if($application_type==4)
             $query = $query->where('process_acc_validated_aadhar', -57);
          }
          if (!empty($process_type)) {
              if($process_type==1)
               $query = $query->where('failed_process_type_aadhaar',1);
              if($process_type==2)
              $query = $query->where('failed_process_type_aadhaar',2);
              if($process_type==3)
              $query = $query->where('failed_process_type_aadhaar',3);
             
          }
        }
        
        $serachvalue = $request->search['value'];
        if (empty($serachvalue)) {
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
            'id', 'created_by_dist_code', 'dob', 'assembly_name',
            'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
            'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'next_level_role_id_aadhar_validation', 
            'process_acc_validated_aadhar','mobile_no', 'acc_validated_aadhar','wbpds_name_as_in_aadhar'
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
            'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
            'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'next_level_role_id_aadhar_validation', 
            'process_acc_validated_aadhar','mobile_no', 'acc_validated_aadhar','wbpds_name_as_in_aadhar'
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
                'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'next_level_role_id_aadhar_validation', 
                'process_acc_validated_aadhar','mobile_no', 'acc_validated_aadhar','wbpds_name_as_in_aadhar'
              ]
            );
          }
          $filterRecords = count($data);
        }
        return datatables()->of($data)->setTotalRecords($totalRecords)
          ->setFilteredRecords($filterRecords)
          ->skipPaging()
          ->addColumn('application_id', function ($data) use ($scheme_id, $scheme_length, $id_length) {
  
            $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);
  
            return $app_id;
          })->addColumn('view', function ($data) use ($scheme_id, $designation_id_old,$type) {
           
            if ($designation_id_old == 'Verifier') {
                if($data->process_acc_validated_aadhar==-57){
                 $action ='Rejected';
                }
                else if($data->acc_validated_aadhar==-2 && $data->next_level_role_id_aadhar_validation==1){
                  $action ='Approval Pending';
                 }
                 else if($data->acc_validated_aadhar==-2 && is_null($data->next_level_role_id_aadhar_validation)){
                   $action = '<a href="Viewpdsnamemismatch?id=' . $data->id . '&scheme_id=' . $scheme_id . '&type=' . $type . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
                 }
                 else{
                  $action ='';
                }
              
            }
            if ($designation_id_old == 'Approver') {
              if($data->next_level_role_id_aadhar_validation==-57){
                $action ='Rejected';
               }
               else if($data->next_level_role_id_aadhar_validation==0){
                 $action ='Approved';
                }
                else if($data->acc_validated_aadhar==-2 && $data->next_level_role_id_aadhar_validation==1){
                  $action = '<a href="Viewpdsnamemismatch?id=' . $data->id . '&scheme_id=' . $scheme_id . '&type=' . $type . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
                }
                else{
                  $action ='';
                }
              
            
             }
           
  
            return $action;
          })->addColumn('check', function ($data) use ($designation_id_old) {
            if ($designation_id_old == 'Approver') {
              if ($data->next_level_role_id_aadhar_validation == 1) {
                return '<input type="checkbox" name="approvalcheck[]" onClick="controlCheckBox()" value="' . $data->id . '">';
              } else
                return '';
            } else {
              return '';
            }
          })
          ->addColumn('id', function ($data) {
            return $data->id;
          })->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {
  
            $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);
  
            return $app_id;
          })
          ->addColumn('name', function ($data) {
            return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
          })->addColumn('name_as_in_aadhar', function ($data) {
            if (!empty($data->wbpds_name_as_in_aadhar)) {
              $av_name_response = trim($data->wbpds_name_as_in_aadhar);
            } else {
              $av_name_response = '';
            }
            return $av_name_response;
           
          })->addColumn('bank_ifsc', function ($data) {
            if (!empty($data->bank_ifsc)) {
              $bank_ifsc =trim($data->bank_ifsc);
            } else {
              $bank_ifsc = '';
            }
            return $bank_ifsc;
          })->addColumn('bank_code', function ($data) {
            if (!empty($data->bank_code)) {
              $bank_code =trim($data->bank_code);
            } else {
              $bank_code = '';
            }
            return $bank_code;
          })->addColumn('mobile_no', function ($data) {
            if (!empty($data->mobile_no)) {
              $ben_mobile_no =trim($data->mobile_no);
            } else {
              $ben_mobile_no = '';
            }
            return $ben_mobile_no;
          })->addColumn('failed_type', function ($data) {
            $failed_type = '';
            if (!empty($data->acc_validated_aadhar)) {
             if($data->acc_validated_aadhar=='-2'){
                $failed_type = 'Name';
             } else if($data->acc_validated_aadhar=='-1'){
                $failed_type = 'Aadhaar';
             }
            } else {
              $failed_type = '';
            }
            return $failed_type;
          })
          ->rawColumns(['view', 'id', 'name', 'mask_aadhaar_no', 'bank_ifsc','bank_code','bank_ifsc','bank_ifsc', 'check'])
          ->make(true);
      }
  
      return view(
        'wbpds.linelistingmismatch',
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
          'type' => $type,
          'type_des' => $type_des
        ]
      );
    }
    public function ViewMismatchName(Request $request)
    {
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = AuthChecker::getUserId();
  
      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      if (empty($request->id)) {
        return redirect("/")->with('danger', 'Applicant ID Not Found');
      }
      if (!is_numeric($request->id)) {
        return redirect("/")->with('danger', 'Applicant ID Not Valid');
      }
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $type = $request->type;
      if(!in_array($type,array(1,2))){
        return redirect("/")->with('danger', 'Input Not Valid');
      }
      if($type==1){
        $type_des='Beneficiary with Account Validation Failed';
      }
      else if($type==2){
        $type_des='Beneficiary with Name Validation Failed WBPDS';

      }
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
        $file_path = $scheme_obj->file_path;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
        $file_path = $scheme_obj->file_path;
      }
      $query = DB::table($schema . '.beneficiary')
        ->where('created_by_dist_code', $district_code)
        ->where('id', $request->id);
      if ($type == 1) {
          $query = $query->where('acc_validated_aadhar', -1);
      }
      if ($type == 2) {
          $query = $query->where('acc_validated_aadhar', -2);
      }
  
      $row = $query->first();
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $app_id = $row->created_by_dist_code . substr('0' . $row->scheme_id, -$scheme_length) . substr('0000000' . $row->id, -$id_length);
      $row->app_id = $app_id;
  
      $reject_revert_cause_list = RejectRevertReason::where('status', true)->get();
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
      $doc_type_id = $this->doc_type_id;
      $docs = DB::table($schema . '.ben_docs')->where('ben_id', $request->id)->where('doc_type_id', $doc_type_id)->first();
      if($designation_id_old=='Approver'){
      $docs_new = DB::table($schema . '.ben_docs')->where('ben_id', $request->id)->where('new_doc_type_id', $doc_type_id)->first();
      }
      else{
        $docs_new =collect([]);
      }
      //dd($docs_new);
      $doc_man = DocumentType::get(['id', 'doc_name', 'doc_type', 'doc_mime_type', 'doc_size_kb'])->where("id", $doc_type_id)->first()->toArray();
      //dd($docs_new);
      return view(
        'wbpds.ViewMismatchName',
        [
          'designation_id_old' => $designation_id_old,
          'scheme_id' => $scheme_id,
          'row' => $row,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'doc_man' => $doc_man,
          'docs' => $docs,
          'docs_new' => $docs_new,
          'reject_revert_cause_list' => $reject_revert_cause_list,
          'type' => $type
        ]
      );
    
    }
    public function ViewpdsnamemismatchPost(Request $request)
    {
      $this->middleware('auth');
      //dd('ok2');
      $doc_type_id = $this->doc_type_id;
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = AuthChecker::getUserId();
      $id = $request->id;
      //dd($request->id);
      $scheme_id = $request->scheme_id;
      //dd($request);
      $aadhar_no = trim($request->aadhaar_no);
      //dd($aadhar_no);
      $new_is_required = trim($request->new_is_required);
      $in_process_type = trim($request->process_type);
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      if (empty($request->id)) {
        return redirect("/")->with('danger', 'Applicant ID Not Found');
      }
      if (!ctype_digit($request->id)) {
        return redirect("/")->with('danger', 'Applicant ID Not Valid');
      }
      if(!in_array($new_is_required,array(1,0))){
        return redirect("/")->with('danger', 'Input Not Valid');
      }
      if($new_is_required==1){
        if(!empty($in_process_type)){
          $process_type=$in_process_type;
        }
        else{
          $process_type=4;
        }
      }
      else{
        $process_type=$in_process_type;
      }
      //dd($process_type);
      if(!empty($process_type)){
        if(!in_array($process_type,array(1,2,3,4))){
          return redirect("/")->with('danger', 'Process Id Not Valid');
        }
      }
      $type = $request->type;
      if(!in_array($type,array(1,2))){
        return redirect("/")->with('danger', 'Input Not Valid');
      }
      if($type==1){
        $type_des='Beneficiary with Account Validation Failed';
      }
      else if($type==2){
        $type_des='Beneficiary with Name Validation Failed from WBPDS';

      }
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $condition = array();
      $condition['id'] = $request->id;
      $district_code = $duty_obj->district_code;
      if ($designation_id_old == 'Verifier') {
        if ($duty_obj->mapping_level == "Subdiv") {
          $created_by_local_body_code = $duty_obj->urban_body_code;
        }
        if ($duty_obj->mapping_level == "Block") {
          $created_by_local_body_code = $duty_obj->taluka_code;
        }
        $condition['created_by_local_body_code'] = $created_by_local_body_code;
      }
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
        $file_path = $scheme_obj->file_path;
        $file_arc_path = $scheme_obj->file_arc_path;
        //file_arc_path
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
        $file_path = 'keep';
        $file_arc_path = 'keep_back';
      }
      $query = DB::table($schema . '.beneficiary')
        ->where($condition);
      $row = $query->first();
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $c_time = date('Y-m-d H:i:s', time());
      if($new_is_required==1){
       
        if ($this->isAadharValid(trim($request->aadhar_no)) == false) {
          $errors = array();
          $errorMsg = "Aadhaar Number Invalid";
          array_push($errors, $errorMsg);
          return redirect("/Viewpdsnamemismatch?scheme_id=".$scheme_id."&type=".$type."&id=".$request->id)->with('errors', $return_msg);
      }
      }
      $doc_row = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->where('id', $doc_type_id)->first();

      if($new_is_required==1){
        if ($request->hasFile('doc_' . $doc_type_id)) {
          $doc_file = $request->file('doc_' . $doc_type_id);
          $file_passport = $doc_file->getClientOriginalName();
          $file_type = $doc_file->getClientOriginalExtension();
          $file_profile = "doc_" . $doc_type_id . "_" . rand(10000, 99999) . '_' . time() . '.' . $doc_file->getClientOriginalExtension();
          //$destinationPath = storage_path('app/keep_wcd/');
          $destinationPath = storage_path('app/' . $file_path . '/');
          $fileStore[] = $doc_file->move($destinationPath, $file_profile);
          //array_push($uploaded_doc,$file_profile);
          $uploaded_doc[$doc_type_id] = $file_profile;
        } else {
          $file_passport = null;
        }
        $ag_update=1;
        if (!empty($row->aadhar_no)) {
          $sp_old_aadhar_no = trim($row->aadhar_no);
         
        }
        else{
          $sp_old_aadhar_no=NULL;
        }
        $sp_new_aadhar_no = $aadhar_no;
      }
      else{
        $ag_update=0;
        $file_passport = null;
      }
      if(!empty(trim($row->mobile_no))){
        $sp_mobile=$row->mobile_no;
      }
      else{
        $sp_mobile=0;  
      }
      if ($designation_id_old == 'Verifier') {
        $inputMain = [
          'failed_process_type_aadhaar' => $process_type,
          'failed_process_type_aadhaar' => $process_type,
          'next_level_role_id_aadhar_validation' => 1
        ];
        
        $inputFailed = [
          'failed_process_type_aadhaar' => $process_type,
          'next_level_role_id_aadhar_validation' => 1
        ];
        $new_value = [];
        $old_value = [];
        $old_value['aadhar_no'] = trim($row->aadhar_no);
        $new_value['aadhar_no'] = $aadhar_no;
        if($process_type==1){
          $inputMain['acc_validated_aadhaar_new']=2;
          $inputMain['process_acc_validated_aadhar']=2;
          $inputFailed['acc_validated_aadhaar_new']=2;
          $inputFailed['process_acc_validated_aadhar']=2;
        }else if($process_type==2 || $process_type==4){
          $inputMain['process_acc_validated_aadhar']=0;
          $inputMain['acc_validated_aadhaar_new']=0;
          $inputFailed['acc_validated_aadhaar_new']=0;
          $inputFailed['process_acc_validated_aadhar']=0;
        }else if($process_type==3){
          $inputMain['process_acc_validated_aadhar']=-57;
          $inputMain['acc_validated_aadhaar_new']=-57;
          $inputMain['next_level_role_id_aadhar_validation']=1;
          $inputFailed['acc_validated_aadhaar_new']=-57;
          $inputFailed['process_acc_validated_aadhar']=-57;
        }
        if($new_is_required==1 && ($process_type==2 || $process_type==4)){
          $inputFailed['new_aadhar_no']=$aadhar_no;
          $inputMain['new_aadhar_no']=$aadhar_no;
        
          
        }
        
        $insertUpdateBenDetails = [
          'old_data' => json_encode($old_value),
          'new_data' => json_encode($new_value),
          'original_application_id' => $id,
          'dist_code' => $district_code,
          'user_id' => $user_id,
          'scheme_id' => $scheme_id,
          'created_at' => $c_time,
          'update_code' =>25 
      ];
        
        $docs_bank_pre_obj = DB::table($schema . '.ben_docs')->where('ben_id', $request->id)->where('doc_type_id', $doc_type_id)->first();

        try {
         // dd($ag_update);
          $base_url = url('/');
          DB::beginTransaction();
          if($ag_update==1){
          $is_inserted_status_arr = DB::select("select " . $schema . ".dup_adjustment_insert_update(new_aadhar_no => '" . $sp_new_aadhar_no . "',old_aadhar_no => '" . $sp_old_aadhar_no . "')");                       
          $is_inserted_status = $is_inserted_status_arr[0]->dup_adjustment_insert_update;
          }
          else{
            $is_inserted_status = 1;
          }
          //dd($is_inserted_status);
          $return_text = '';
         
          if ($is_inserted_status == 2) {
            // dd('ok3');
            DB::rollback();
            $return_text = 'Duplicate Aadhaar Information.. Please try different.';
            $return_msg = array("" . $return_text);
            return redirect("/Viewpdsnamemismatch?type=".$type."&scheme_id=".$scheme_id."&id=".$request->id)->with('errors', $return_msg);

          } else if ($is_inserted_status == 3) {
            //dd('ok3');
            DB::rollback();
            $return_text = 'Aadhaar Information Modification Faild.. Please try different.';
            $return_msg = array("" . $return_text);
            return redirect("/Viewpdsnamemismatch?type=".$type."&scheme_id=".$scheme_id."&id=".$request->id)->with('errors', $return_msg);

          }else if ($is_inserted_status == 5) {
            //dd('ok3');
            DB::rollback();
            $return_text = 'Aadhaar Information Modification Faild.. Please try different.';
            $return_msg = array("" . $return_text);
            return redirect("/Viewpdsnamemismatch?type=".$type."&scheme_id=".$scheme_id."&id=".$request->id)->with('errors', $return_msg);

          }else if ($is_inserted_status == 1) {
            $main_update = DB::table($schema . '.beneficiary')->where(['id' => $id, 'created_by_local_body_code' => $created_by_local_body_code, 'created_by_dist_code' => $district_code, 'scheme_id' => $scheme_id])->update($inputMain);
            if ($main_update) {
              //dd($file_passport);
              if ($file_passport) {
                $insert_doc_type_arr = array();
                $insert_doc_type_arr_arch = array();
                $de_active_arr = array();
                $i = 0;
                if (!empty($docs_bank_pre_obj)) {
                  $filename = basename($docs_bank_pre_obj->doc_name);
                  if (file_exists(storage_path('app/' . $file_path . '/') . '//' . $filename)) {
                    rename(storage_path('app/' . $file_path . '/') . '//' . $filename, storage_path('app/' . $file_arc_path . '/') . '//' . $filename);
                  }
                  $insert_doc_type_arr_arch[$i]['ben_id'] = $id;
                  $insert_doc_type_arr_arch[$i]['doc_type_id'] = $docs_bank_pre_obj->doc_type_id;
                  $insert_doc_type_arr_arch[$i]['doc_type_name'] = $docs_bank_pre_obj->doc_type_name;
                  $insert_doc_type_arr_arch[$i]['doc_name'] = $docs_bank_pre_obj->doc_name;
                  $insert_doc_type_arr_arch[$i]['created_at'] = $c_time;
                  $doc_arch_inserted = DB::table($schema . '.ben_docs_arc')->insert($insert_doc_type_arr_arch);
                  $doc_deactivated =1;
                } 
                  $insert_arr = array();
                  $insert_doc_type_arr[$i]['ben_id'] = $id;
                  $insert_doc_type_arr[$i]['is_active'] = FALSE;
                  $insert_doc_type_arr[$i]['new_doc_type_id'] = $doc_type_id;
                  $insert_doc_type_arr[$i]['new_doc_type_name'] = $doc_row->doc_name;
                  $insert_doc_type_arr[$i]['new_doc_name'] = $base_url . '/images_wcd/' . $file_profile;
                  $insert_doc_type_arr[$i]['created_at'] = $c_time;
                  $doc_inserted = DB::table($schema . '.ben_docs')->insert($insert_doc_type_arr);
                  $doc_arch_inserted = 1;
                  $doc_deactivated = 1;
                
              } else {
                $doc_inserted = 1;
                $doc_arch_inserted = 1;
                $doc_deactivated = 1;
              }
              $failed_update = DB::table('pension.failed_payment_details')->where(['validation_type'=>2,'ben_id' => $id,'scheme_id' => $scheme_id])->update($inputFailed);
               if($process_type==3){
                 
                    $free_pending_bank_duplicate_data=1;
                    $reject_dup_adjustment=1;
                   
              }
              else{
                $free_pending_bank_duplicate_data=1;
                $reject_dup_adjustment=1;
              }
              if ($failed_update && $doc_inserted && $doc_arch_inserted && $doc_deactivated) {
                $is_saved_log = DB::table('public.update_ben_details')
                ->insert($insertUpdateBenDetails);;
                if ($is_saved_log) {
                  DB::commit();
                  if($process_type==3){
                  $return_text = 'Beneficiary with  Id:'.$id.' Rejected Successfully and Sent to Approver for Approval';
                  }
                  else{
                    $return_text = 'Beneficiary with  Id:'.$id.' Aadhaar Information Edited Successfully and Sent to Approver for Approval';

                  }
                  return redirect("pdsnamemismatchlist?type=".$type."&scheme_id=" . $scheme_id)->with('success', $return_text)->with('id',  $row->id);
                } else {
                  DB::rollback();
                  $return_text = 'Error. Please try again';
                  $return_msg = array("" . $return_text);
                }
              } else {
                DB::rollback();
                $return_text = 'Error. Please try again';
                $return_msg = array("" . $return_text);
              }
            } else {
              DB::rollback();
              $return_text = 'Error. Please try again';
              $return_msg = array("" . $return_text);
            }
            if ($return_text != '') {
              return redirect("/Viewpdsnamemismatch?type=".$type."&scheme_id=".$scheme_id."&id=".$request->id)->with('errors', $return_msg);
            }
          }
          
        }catch (\Exception $e) {
          //dd($e);
          DB::rollback();
          $return_text = 'Error. Please try again';
          $return_msg = array("" . $return_text);
          return redirect("/Viewpdsnamemismatch?type=".$type."&scheme_id=".$scheme_id."&id=".$request->id)->with('errors', $return_msg);
        }
      }  else {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      
    }
    public function bulkApprove(Request $request)
    {
      $this->middleware('auth');
      //dd('ok');
      $designation_id_old = Auth::user()->designation_id_old;
      if ($designation_id_old!='Approver') {
        return redirect("/")->with('error', 'Not Allowed');
      }
      $user_id = AuthChecker::getUserId();
      $scheme_id = $request->scheme_id;
      $process_type = $request->process_type;
      $action_type = $request->action_type;
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
      $type = $request->type;
      if(!in_array($type,array(1,2))){
        return redirect("/")->with('danger', 'Input Not Valid');
      }
      if(!in_array($action_type,array(1,2,3))){
        return redirect("/")->with('danger', 'Input Not Valid');
      }
      if($type==1){
        $type_des='Beneficiary with Account Validation Failed from WBPDS';
        $check_acc_validate=-1;
      }
      else if($type==2){
        $type_des='Beneficiary with Name Validation Failed from WBPDS';
        $check_acc_validate=-2;

      }
      if(!in_array($process_type,array(1,2))){
        return redirect("/")->with('danger', 'Not Allowed');
      }
      
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      $applicationid_arr = array();
      $inputs = request()->input('approvalcheck');
      $c_time = date('Y-m-d H:i:s', time());
      //dd($inputs);
      foreach ($inputs as $input) {
        array_push($applicationid_arr, $input);
        
      }
      if($process_type==2){
      $rowcount = DB::table($schema . '.beneficiary')->where('acc_validated_aadhar',$check_acc_validate)->where('next_level_role_id_aadhar_validation',1)->where('created_by_dist_code', $district_code)->whereIn('id', $applicationid_arr)->count();
      if($rowcount!=count($applicationid_arr)){
        return redirect("/")->with('danger', 'Not Allowed');
      }
      }
      $implode_application_arr = implode("','", $applicationid_arr);
      $in_pension_id = 'ARRAY[' . "'$implode_application_arr'" . ']';
      $back_url = 'pdsnamemismatchlist?type='.$type.'&scheme_id=' . $scheme_id;
      $comments = NULL;
      try {
        if($action_type==1){
        DB::beginTransaction();
        if($process_type==2){
           $is_inserted_status_arr = DB::select("select ".$schema.".aadhaar_validation_request_bulk_new(in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'DT', in_custom_comment => '".$comments."')");
           $is_inserted_status=$is_inserted_status_arr[0]->aadhaar_validation_request_bulk_new;
         }
         else if($process_type==1){
          $is_inserted_status_arr = DB::select("select ".$schema.".aadhaar_validation_request_bulk_same(in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'DF', in_custom_comment => '".$comments."')");
          $is_inserted_status=$is_inserted_status_arr[0]->aadhaar_validation_request_bulk_same;
        }
       if($is_inserted_status==1){
        DB::commit();
        return redirect($back_url)->with('message', 'Applications Aadhaar information change request has been Approved Succesfully!');
      } else{
        DB::rollback();
        return redirect($back_url)->with('error', 'Error! Please try again.');
      }
      }
      else if($action_type==2){
        //dd('ok');
       // DB::beginTransaction();
        foreach ($applicationid_arr as $appItem) {
          $application_id=$appItem;
          //array_push($applicationid_arr, $input);
          
        }
        $row = DB::table($schema . '.beneficiary')->where('acc_validated_aadhar',$check_acc_validate)->where('next_level_role_id_aadhar_validation',1)->where('created_by_dist_code', $district_code)->where('id', $application_id)->first();

           DB::beginTransaction();
           $reject_dup_adjustment_arr = DB::select("select ".$schema.".reject_dup_adjustment(
            in_old_aadhar_no => '".$row->new_aadhar_no."'
          )");
          $reject_dup_adjustment=$reject_dup_adjustment_arr[0]->reject_dup_adjustment;
         // $is_reverted_status_arr = DB::select("select ".$schema.".bank_validation_request_revert(in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'KR', in_custom_comment => '".$comments."')");
         // $is_reverted_status=$is_reverted_status_arr[0]->bank_validation_request_revert;
         $accept_reject_model = new AcceptRejectInfo;
         $accept_reject_model->created_at = $c_time;
         $accept_reject_model->application_id = $application_id;
         $accept_reject_model->scheme_id = $scheme_id;
         $accept_reject_model->user_id = $user_id;
         $accept_reject_model->op_type = 'DG';
         $is_saved_log = $accept_reject_model->save();
         $inputMain = [
          'failed_process_type_aadhaar' =>NULL, 'next_level_role_id_aadhar_validation' => NULL, 
          'process_acc_validated_aadhar' => NULL, 
          'new_aadhar_no' => NULL, 'acc_validated_aadhaar_new'=> NULL
        ];
        $inputFail = [
          'next_level_role_id_aadhar_validation' =>NULL, 
          'acc_validated_aadhaar_new' => NULL, 
          'failed_process_type_aadhaar' => NULL, 
          'process_acc_validated_aadhar' => NULL
        ];
         $main_update = DB::table($schema . '.beneficiary')
         ->where('created_by_dist_code', $district_code)
         ->where('next_level_role_id_aadhar_validation',1)->where('id', $application_id)->update($inputMain);
         $failed_update = DB::table('pension.failed_payment_details')
         ->where('validation_type', 2)
         ->where('ben_id', $application_id)->update($inputFail);
         if($row->failed_process_type_aadhaar==2){
         $delete_arch = DB::table($schema . '.ben_docs')->where('ben_id', $application_id)->whereNotNull('new_doc_name')->where('new_doc_type_id',10)->where('is_active',FALSE)->delete();
         }
         else
         $delete_arch=1;
         //dump($reject_dup_adjustment); dump($is_saved_log); dump($main_update); dump($failed_update);dd($delete_arch);
          if($reject_dup_adjustment==1 &&  $is_saved_log &&  $main_update &&  $failed_update &&  $delete_arch){
            DB::commit();
            return redirect($back_url)->with('message', 'Applications with id '.$application_id.' Aadhaar information change request has been Reverted Succesfully!');
          } else{
            
            DB::rollback();
            return redirect($back_url)->with('error', 'Error! Please try again.');
          }

      }
      else if($action_type==3){
        foreach ($applicationid_arr as $appItem) {
          $application_id=$appItem;
          //array_push($applicationid_arr, $input);
          
        }
        $row = DB::table($schema . '.beneficiary')->where('acc_validated_aadhar',$check_acc_validate)->where('next_level_role_id_aadhar_validation',1)->where('created_by_dist_code', $district_code)->where('id', $application_id)->first();
        if(!empty(trim($row->mobile_no))){
          $sp_mobile=$row->mobile_no;
        }
        else{
          $sp_mobile=0;  
        }
        DB::beginTransaction();
        $inputMain = [
          'next_level_role_id' => -57,
          'rejected_date' => $c_time, 
          'rejected_by' => $user_id,
          'next_level_role_id_aadhar_validation' => -57,
          'is_approved' => 2,'is_verified' => 2,'is_rejected' => 1,'is_clean' => 10
        ];
        $inputFail = [
          'next_level_role_id' => -57,
          'next_level_role_id_aadhar_validation' => -57
        ];
        $main_update = DB::table($schema . '.beneficiary')->where(['id' => $application_id, 'created_by_dist_code' => $district_code, 'scheme_id' => $scheme_id])->update($inputMain);

        $failed_update = DB::table('pension.failed_payment_details')->where(['validation_type'=>2,'ben_id' => $application_id,'scheme_id' => $scheme_id])->update($inputFail);

        $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
        //$scheme_dedup_list=array(2,10,11);
        if (in_array($scheme_id, $scheme_dedup_list)) {
         $free_pending_bank_duplicate_arr = DB::select("select ".$schema.".free_pending_bank_duplicate_data(in_scheme_id => ".$scheme_id.", in_district_code => ".$district_code.")");
         $free_pending_bank_duplicate_data=$free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
         $reject_dup_adjustment_arr1 = DB::select("select ".$schema.".reject_dup_adjustment(
          in_old_bank_ifsc => '".$row->bank_ifsc."', 
          in_old_bank_code => '".$row->bank_code."', 
          in_old_aadhar_no => '".$row->aadhar_no."', 
          in_old_mobile_no => ".$sp_mobile."
          )");
          $reject_dup_adjustment1=$reject_dup_adjustment_arr1[0]->reject_dup_adjustment;
          if($row->failed_process_type_aadhaar==2){
            $reject_dup_adjustment_arr2 = DB::select("select ".$schema.".reject_dup_adjustment(
              in_old_aadhar_no => '".$row->new_aadhar_no."'
              )");
              $reject_dup_adjustment2=$reject_dup_adjustment_arr2[0]->reject_dup_adjustment;
          }
          else{
            $reject_dup_adjustment2=1;
          }
         }
         else{
          $free_pending_bank_duplicate_data=1;
          $reject_dup_adjustment1=1;
          $reject_dup_adjustment2=1;
         }
         if($main_update && $failed_update && $reject_dup_adjustment1==1  && $reject_dup_adjustment2==1 && $free_pending_bank_duplicate_data==1){
          DB::commit();
          return redirect($back_url)->with('message', 'Application Rejected Succesfully!');
        } else{
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
      }
      }catch (\Exception $e) {
        //dd($e);
        DB::rollback();
        return redirect($back_url)->with('error', 'Error! Please try again.');
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


// Added by ANJAN for Aadhar Name Validation
    
    function aadharNameValidMIS(Request $request)
    {


        $this->middleware('auth');
        $base_date  = '2020-01-01';
        date_default_timezone_set('Asia/Kolkata');
        $c_time = Carbon::now();
        $c_date = $c_time->format("Y-m-d");
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $userId = Auth::user()->id;
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' ||  $designation_id_old == 'Dashboard' || $designation_id_old == 'MisState' || $designation_id_old == 'DDO') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                if (in_array($roleObj['scheme_id'], array(3, 2, 10, 11, 8, 9, 17, 19, 1))) {
                    $is_urban = $roleObj['is_urban'];
                    $district_code = $roleObj['district_code'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                        $muncList = UrbanBody::select('urban_body_code', 'urban_body_name')->where('sub_district_code', $blockCode)->get();
                        $municipality_visible = 1;
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                        $gpList = GP::select('gram_panchyat_code', 'gram_panchyat_name')->where('block_code', $blockCode)->get();
                    }
                    break;
                }
            }

            if (empty($district_code))
                return redirect("/")->with('success', 'User Disabled. ');
        } else {
            return redirect("/")->with('success', 'User Disabled. ');
        }
        //dd($district_code);
        if (!empty($district_code)) {
            $district_visible = 0;
            $district_code_fk = $district_code;
        } else {
            $district_code_fk = NULL;
        }
        if (!empty($is_urban)) {
            $is_urban_visible = 0;
            $rural_urban_fk = $is_urban;
        } else {
            $rural_urban_fk = NULL;
        }
        if (!empty($blockCode)) {
            $block_visible = 0;
            $block_munc_corp_code_fk = $blockCode;
            $gp_ward_visible = 1;
        } else {
            $block_munc_corp_code_fk = NULL;
            $gp_ward_visible = 0;
        }
        $gp_ward_visible = 0;
        $municipality_visible = 0;
        $districts = District::get();
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (3,2,10,11,8,9,17,19,1) and  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
        //dd($scheme_list);
        return view(
            'wbpds.aadharNameValidationReport',
            [
                'districts' => $districts,
                'scheme_list' => $scheme_list,
                'district_visible' => $district_visible,
                'district_code_fk' => $district_code_fk,
                'is_urban_visible' => $is_urban_visible,
                'rural_urban_fk' => $rural_urban_fk,
                'block_visible' => $block_visible,
                'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
                'municipality_visible' => $municipality_visible,
                'gp_ward_visible' => $gp_ward_visible,
                'is_urban_visible' => $is_urban_visible,
                'base_date' => $base_date,
                'c_date' => $c_date,
                'gpList' => $gpList,
                'muncList' => $muncList
            ]
        );
    }



    public function getData(Request $request)
    {
        $scheme_id = $request->scheme_id;
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        $gp_ward = $request->gp_ward;
        // dd($gp_ward);
        $caste = $request->caste_category;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $base_date  = '2020-08-16';
        $c_time = Carbon::now();
        $c_date = $c_time->format("Y-m-d");
        $heading_msg = '';
        $title = "";
        //$block_condition = "";
        if (!empty($district)) {
            $district_row = District::where('district_code', $district)->first();
        }

        if (!empty($block)) {

            if ($urban_code == 1) {
                $block_ulb = SubDistrict::where('sub_district_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->sub_district_name;
                //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
            } else {
                $block_ulb = Taluka::where('block_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->block_name;
                // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
            }
        } else {
            // $block_condition = "";
        }
        if (!empty($gp_ward)) {

            if ($urban_code == 1) {
                $gp_ward_row = Ward::where('urban_body_ward_code', '=', $gp_ward)->first();
                $gp_ward_name = $gp_ward_row->urban_body_ward_name;
                //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
            } else {
                $gp_ward_row = GP::where('gram_panchyat_code', '=', $gp_ward)->first();
                $gp_ward_name = $gp_ward_row->gram_panchyat_name;
                // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
            }
        }
        $rules = [
            'scheme_id' => 'required|integer',
            'district' => 'nullable|integer',
            'urban_code' => 'nullable|integer',
            'block' => 'nullable|integer',
            'muncid' => 'nullable|integer',
            'gp_ward' => 'nullable|integer'
        ];
        $data = array();
        $column = "";
        $attributes = array();
        $messages = array();
        $attributes['scheme_id'] = 'Scheme';
        $attributes['district'] = 'District';
        $attributes['urban_code'] = 'Rural/ Urban';
        $attributes['block'] = 'Block/Sub Division';
        $attributes['muncid'] = 'Municipality';
        $attributes['gp_ward'] = 'GP/Ward';
        $attributes['from_date'] = 'From Date';
        $attributes['to_date'] = 'To Date';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $scheme_row = Scheme::where('id', $scheme_id)->first();
            $user_msg = "Aadhar Name Validation MIS Report for the Scheme " . $scheme_row->scheme_name;
            $title = $user_msg;
            //dd($title);

            $data = array();
            $return_status = 1;
            $return_msg = '';
            $heading_msg = '';
            $external = 0;
            $external_arr = array();
            $external_filter = array();
            if (!empty($gp_ward)) {
                if ($urban_code == 1) {
                    $column = "Ward";
                    $heading_msg =  $user_msg . ' of the Ward ' . $gp_ward_name;
                    $data = $this->getWardWise($district, $block, $muncid, $gp_ward, $from_date, $to_date, $caste);
                } else {
                    $column = "GP";
                    $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
                    $data = $this->getGpWise($district, $block, NULL, $gp_ward, $from_date, $to_date, $caste);
                }
            } else if (!empty($muncid)) {
                $column = "Ward";
                $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
                $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
                $data = $this->getWardWise($district, $block, $muncid, NULL, $from_date, $to_date, $caste);
            } else if (!empty($block)) {
                if ($urban_code == 1) {
                    $column = "Municipality";
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getMuncWise($district, $block, NULL, NULL, $from_date, $to_date, $caste);
                } else if ($urban_code == 2) {
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getGpWise($district, $block, NULL, $gp_ward, $from_date, $to_date, $caste);
                    $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                    $column = "Block";
                    $data = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
                }
            } else {

                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
                        $data2 = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getDistrictWise($scheme_id, NULL, NULL, NULL, NULL, $from_date, $to_date);

                    $external = 0;
                }
            }

            if (!empty($from_date)) {
                $form_date_formatted = \Carbon\Carbon::parse($from_date)->format('d-m-Y');
                $heading_msg = $heading_msg . " from " . $form_date_formatted;
            }
            if (!empty($to_date)) {
                $to_date_formatted = \Carbon\Carbon::parse($to_date)->format('d-m-Y');
                $heading_msg = $heading_msg . " to  " . $to_date_formatted;
            }
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }
        return response()->json([
            'return_status' => $return_status,
            'return_msg' => $return_msg,
            'row_data' => $data,
            'column' => $column,
            'title' => $title,
            'heading_msg' => $heading_msg
        ]);
    }



    public function getDistrictWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL)
    {
        //$dateFromat = 'DD/MM/YYYY';
        //$dateFromat = 'YYYY/MM/DD';
        //$whereCon = "where 1=1";
        $query = "select A.location_id,A.location_name,
        COALESCE(C.total_name_mismatch,0) as total_name_mismatch, 
        COALESCE(C.total_yet_to_be_action_pending,0) as total_yet_to_be_action_pending, 
        COALESCE(C.total_minor_yet_to_approved,0) as total_minor_yet_to_approved,
        COALESCE(C.total_minor_approved,0) as total_minor_approved,
        COALESCE(C.total_aadhar_yet_to_approved,0) as total_aadhar_yet_to_approved,
        COALESCE(C.total_aadhar_approved,0) as total_aadhar_approved,
        COALESCE(C.total_send_rejection,0) as total_send_rejection,
        COALESCE(C.total_request_approved,0) as total_request_approved
        from(
        select district_code as location_id,district_name as location_name
         from public.m_district ) as A  
        LEFT JOIN
        (select
            count(1) as total_name_mismatch,
            count(1) filter(WHERE next_level_role_id_aadhar_validation is NULL) as total_yet_to_be_action_pending,
            count(1) filter(WHERE failed_process_type_aadhaar = 1 AND next_level_role_id_aadhar_validation= 1) as total_minor_yet_to_approved,
            count(1) filter(WHERE failed_process_type_aadhaar = 1 AND next_level_role_id_aadhar_validation= 0) as total_minor_approved,
            count(1) filter(WHERE failed_process_type_aadhaar = 2 AND next_level_role_id_aadhar_validation= 1) as total_aadhar_yet_to_approved,
            count(1) filter(WHERE failed_process_type_aadhaar = 2 AND next_level_role_id_aadhar_validation= 0) as total_aadhar_approved,
            count(1) filter(WHERE failed_process_type_aadhaar = 3 AND next_level_role_id_aadhar_validation= 1) as total_send_rejection,
            count(1) filter(WHERE failed_process_type_aadhaar = 3 AND next_level_role_id = -57) as total_request_approved,
            created_by_dist_code
            from pension.failed_payment_details where scheme_id=" . $scheme_id . " AND acc_validated_aadhaar= -2 AND validation_type= 2  
         group by created_by_dist_code) as C ON A.location_id=C.created_by_dist_code";

         //echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }


    public function getSubDivWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL)
    {
        //$whereCon = "where A.dist_code=" . $district_code;
        $whereMain = "where  district_code=" . $district_code;

        $query = "select A.location_id,A.location_name,
        COALESCE(C.total_name_mismatch,0) as total_name_mismatch,
        COALESCE(C.total_yet_to_be_action_pending,0) as total_yet_to_be_action_pending,  
        COALESCE(C.total_minor_yet_to_approved,0) as total_minor_yet_to_approved,
        COALESCE(C.total_minor_approved,0) as total_minor_approved,
        COALESCE(C.total_aadhar_yet_to_approved,0) as total_aadhar_yet_to_approved,
        COALESCE(C.total_aadhar_approved,0) as total_aadhar_approved,
        COALESCE(C.total_send_rejection,0) as total_send_rejection,
        COALESCE(C.total_request_approved,0) as total_request_approved
        from(
            select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
            from public.m_sub_district  " . $whereMain . " 
         )
         as A  
        LEFT JOIN
        (select
            count(1) as total_name_mismatch,
            count(1) filter(WHERE next_level_role_id_aadhar_validation is NULL) as total_yet_to_be_action_pending,
            count(1) filter(WHERE failed_process_type_aadhaar = 1 AND next_level_role_id_aadhar_validation= 1) as total_minor_yet_to_approved,
            count(1) filter(WHERE failed_process_type_aadhaar = 1 AND next_level_role_id_aadhar_validation= 0) as total_minor_approved,
            count(1) filter(WHERE failed_process_type_aadhaar = 2 AND next_level_role_id_aadhar_validation= 1) as total_aadhar_yet_to_approved,
            count(1) filter(WHERE failed_process_type_aadhaar = 2 AND next_level_role_id_aadhar_validation= 0) as total_aadhar_approved,
            count(1) filter(WHERE failed_process_type_aadhaar = 3 AND next_level_role_id_aadhar_validation= 1) as total_send_rejection,
            count(1) filter(WHERE failed_process_type_aadhaar = 3 AND next_level_role_id = -57) as total_request_approved,
            created_by_local_body_code
            from pension.failed_payment_details where scheme_id=" . $scheme_id . " and  created_by_dist_code= " . $district_code . " AND acc_validated_aadhaar= -2 AND validation_type= 2  
        group by created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";

        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }


    public function getBlockWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL)
    {
        $whereMain = "where  district_code=" . $district_code;
        $query = "select A.location_id,A.location_name,
        COALESCE(C.total_name_mismatch,0) as total_name_mismatch,
        COALESCE(C.total_yet_to_be_action_pending,0) as total_yet_to_be_action_pending,  
        COALESCE(C.total_minor_yet_to_approved,0) as total_minor_yet_to_approved,
        COALESCE(C.total_minor_approved,0) as total_minor_approved,
        COALESCE(C.total_aadhar_yet_to_approved,0) as total_aadhar_yet_to_approved,
        COALESCE(C.total_aadhar_approved,0) as total_aadhar_approved,
        COALESCE(C.total_send_rejection,0) as total_send_rejection,
        COALESCE(C.total_request_approved,0) as total_request_approved
        from(
            select block_code as location_id,'Block-'||block_name as location_name
           from public.m_block  " . $whereMain . " 
         )
         as A  
        LEFT JOIN
        (select
            count(1) as total_name_mismatch,
            count(1) filter(WHERE next_level_role_id_aadhar_validation is NULL) as total_yet_to_be_action_pending,
            count(1) filter(WHERE failed_process_type_aadhaar = 1 AND next_level_role_id_aadhar_validation= 1) as total_minor_yet_to_approved,
            count(1) filter(WHERE failed_process_type_aadhaar = 1 AND next_level_role_id_aadhar_validation= 0) as total_minor_approved,
            count(1) filter(WHERE failed_process_type_aadhaar = 2 AND next_level_role_id_aadhar_validation= 1) as total_aadhar_yet_to_approved,
            count(1) filter(WHERE failed_process_type_aadhaar = 2 AND next_level_role_id_aadhar_validation= 0) as total_aadhar_approved,
            count(1) filter(WHERE failed_process_type_aadhaar = 3 AND next_level_role_id_aadhar_validation= 1) as total_send_rejection,
            count(1) filter(WHERE failed_process_type_aadhaar = 3 AND next_level_role_id = -57) as total_request_approved,
            created_by_local_body_code
            from pension.failed_payment_details where scheme_id=" . $scheme_id . " and  created_by_dist_code= " . $district_code . " AND acc_validated_aadhaar= -2 AND validation_type= 2    
        group by created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";

        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
    
}
