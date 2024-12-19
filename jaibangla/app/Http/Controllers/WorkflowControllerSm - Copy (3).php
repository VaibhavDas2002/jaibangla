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
class WorkflowControllerSm extends Controller
{

    public function __construct()
    {

         $this->scheme_id = 20;
        $this->source_type = 'ss_nfsa';
        $this->ben_status = -97;
        $this->doc_type_id = 6;
        
    }
    public function shemeSelection(Request $request)
    {
      try{
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = AuthChecker::getUserId();
      if ($designation_id_old == 'Verifier' || $designation_id_old == 'Approver') {
        $schemes = DB::select(DB::raw("select id,scheme_name,display_name,is_active from m_scheme where id IN (10) and   id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
        //dd($schemes);
        return view(
          'Sarasori_Mukhyamantri/SchemeSelection',
          [
  
            'scheme_list' => $schemes,
          ]
        );
      } else {
        return redirect("/")->with('danger', 'Not Allowed');
      }
    }catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
    }
    public function list(Request $request)
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
      if ($designation_id_old != 'Verifier') {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      // dd($next_level_role_id_verifier);
      $type_des='Sarasori Mukhyamantri';
     
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
         ->whereNull('is_lb_imported')->where('created_by_dist_code', $district_code)/*->whereraw(" (dup_bank=0 or dup_bank IS NULL) and (dup_aadhar=0 or dup_aadhar IS NULL) and (dup_mobile=0 or dup_mobile IS NULL) and (no_aadhar=0 or no_aadhar IS NULL) and (no_mobile=0 or no_mobile IS NULL)")*/;
          if ($designation_id_old == 'Verifier') {
            $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
            if (!empty($application_type)) {
              if($application_type==1)
               $query = $query->whereNull('sm_flag')->whereNull('sm_mobile_no')->where('next_level_role_id', $next_level_role_id_verifier);
              if($application_type==2)
              $query = $query->whereNotNull('sm_flag')->whereNotNull('sm_mobile_no')->where('next_level_role_id', $next_level_role_id_verifier);
              if($application_type==3)
              $query = $query->whereNotNull('sm_flag')->whereNotNull('sm_mobile_no')->where('next_level_role_id', $next_level_role_id_approver);
              if($application_type==4)
              $query = $query->whereNotNull('sm_flag')->whereNotNull('sm_mobile_no')->where('is_rejected',1);
               
            }
          }
          // dd($query);
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
            $query = $query->whereNotNull('sm_flag')->whereNotNull('sm_mobile_no')->where('next_level_role_id', $next_level_role_id_verifier);
            if($application_type==3)
            $query = $query->whereNotNull('sm_flag')->whereNotNull('sm_mobile_no')->where('next_level_role_id', $next_level_role_id_approver);
            if($application_type==4)
            $query = $query->whereNotNull('sm_flag')->whereNotNull('sm_mobile_no')->where('is_rejected',1);
          }
        
        }
        //  $rawsql = $query->toSql();
        //  dd($rawsql);
        $serachvalue = $request->search['value'];
        if (empty($serachvalue)) {
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
            'id', 'created_by_dist_code', 'dob', 'assembly_name',
                'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag','sm_mobile_no','is_rejected','mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank'
          ]);
          $filterRecords = count($data);
        } else {
          if (is_numeric($serachvalue)) {
            // $ben_id = substr($serachvalue, -7);
            $ben_id=$serachvalue;
            $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
              $query1->where('id', $ben_id);
            });
            $totalRecords = $query->count();
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
              [
                'id', 'created_by_dist_code', 'dob', 'assembly_name',
                'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag','sm_mobile_no','is_rejected','mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank'
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
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag','sm_mobile_no','is_rejected','mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank'
              ]
            );
          }
          $filterRecords = count($data);
        }
        return datatables()->of($data)->setTotalRecords($totalRecords)
          ->setFilteredRecords($filterRecords)
          ->skipPaging()
          ->addColumn('view', function ($data) use ($scheme_id, $designation_id_old, $next_level_role_id_approver,$next_level_role_id_verifier) {
            $action = '<a href="ViewSm?id=' . $data->id  . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> View</a>';

           
              if($designation_id_old=='Verifier'){
                if(is_null($data->sm_flag) && is_null($data->sm_mobile_no) && $data->next_level_role_id==$next_level_role_id_verifier){
                  // echo 1;die;
                  if ($data->no_aadhar == 1 || $data->no_mobile == 1 || $data->dup_aadhar == 1 || $data-> dup_mobile == 1 || $data->dup_bank == 1) {
                    $action = $action. '';
                  }else {
                    $action = $action. '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-'.$data->id.'" value="'.$data->id.'">Mark as Sarasori Mukhyamantri</button>';
                  }
                  $action = $action. '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-warning btn-revert" id="btn-revert-'.$data->id.'" value="'.$data->id.'">Revert</button>';
                    $action = $action. '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-danger btn-sms" id="btn-sms-'.$data->id.'" value="'.$data->id.'">Reject</button>';
                }
                else if(!is_null($data->sm_flag) && !is_null($data->sm_mobile_no) && $data->next_level_role_id==$next_level_role_id_verifier){
                    $action = $action.'&nbsp;&nbsp;&nbsp;&nbsp;Approval Pending';
                }
                else if(!is_null($data->sm_flag) && !is_null($data->sm_mobile_no) && $data->next_level_role_id==$next_level_role_id_approver){
                    $action = $action.'&nbsp;&nbsp;&nbsp;&nbsp;Approved';
                }
              } 
            
              if($designation_id_old=='Approver'){
                
                if(!is_null($data->sm_flag) && !is_null($data->sm_mobile_no) && $data->next_level_role_id==$next_level_role_id_verifier){
                    $action = $action. '<button type="button" class="btn btn-xs btn-primary">Approved</button>';

                }
                else if(!is_null($data->sm_flag) && !is_null($data->sm_mobile_no) && $data->next_level_role_id==$next_level_role_id_approver){
                    $action = $action.'&nbsp;&nbsp;&nbsp;&nbsp;Approved';
                }
              }


            return $action;
          })->addColumn('check', function ($data) use ($designation_id_old) {
            if ($designation_id_old == 'Approver') {
              if ($data->aadhar_edit_role_id == 1) {
                return '<input type="checkbox" name="approvalcheck[]" onClick="controlCheckBox()" value="' . $data->id . '">';
              } else
                return '';
            } else {
              return '';
            }
          })
          ->addColumn('id', function ($data) {
            return $data->id;
          })
          ->addColumn('name', function ($data) {
            return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
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
          })
          ->rawColumns(['view', 'id', 'name', 'mask_aadhaar_no', 'bank_ifsc','bank_code','bank_ifsc','bank_ifsc', 'check'])
          ->make(true);
      }
  
      return view(
        'Sarasori_Mukhyamantri.linelisting',
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
          'scheme_id' => $scheme_id
        ]
      );
    }
    public function ViewSm(Request $request)
    {
      //dd('ok');
    try{
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = AuthChecker::getUserId();
      $id = $request->id;
     // dd($id);
      if (empty($request->id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }
     
      
      $duty_obj = Configduty::where('user_id', $user_id)->first();

      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $scheme_id = $request->scheme_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $type_des='Sarasori Mukhyamantri ';
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
        
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      $query = DB::table($schema . '.beneficiary')
     ->where('created_by_dist_code', $district_code)->where('id',$id)->where('created_by_dist_code', $district_code)->whereraw(" (next_level_role_id=".$next_level_role_id_approver." or next_level_role_id=".$next_level_role_id_verifier.") ");
      $row = $query->first();
      // dd( $row);
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      //dd($row->aadhar_no);
      if( $designation_id_old=='Verifier'){
          if(!empty($row->aadhar_no) && trim($row->aadhar_no)!=''){
          $old_aadhar= $row->aadhar_no;
          $new_aadhar='';
          }
        else{
          $old_aadhar='';
          $new_aadhar='';
        }
      }
      else{
        if(!empty($row->old_aadhar_no)){
          $old_aadhar= $row->old_aadhar_no;
          }
        else{
          $old_aadhar='';
        }
        $new_aadhar= $row->aadhar_no;
        //dd($new_aadhar);
      }
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
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();    
      return view(
        'Sarasori_Mukhyamantri.ViewBeneficiary',
        [
          'designation_id_old' => $designation_id_old,
          'row' => $row,
          'id' => $id,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'docs' => $docs,
          'reject_revert_cause_list' => $reject_revert_cause_list,
          'scheme_id' => $scheme_id,
        ]
      );
    }
    catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
    }
    public function markpost(Request $request)
    {
      try{
        $this->middleware('auth');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = AuthChecker::getUserId();        
        if (empty($request->beneficiary_id)) {
          return redirect("/")->with('danger', 'Beneficiary ID Not Found');
        }
        $duty_obj = Configduty::where('user_id', $user_id)->first();
        if (empty($duty_obj)) {
          return redirect("/")->with('danger', 'Not Allowed...');
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
        $district_code = $duty_obj->district_code;
        if (!empty($scheme_obj->short_code)) {
          $schema = $scheme_obj->short_code;
        } else {
          $schema = "pension";
          
        }
        $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
        $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
        $next_level_role_id_approver = $role_id_approver->parent_id;
        $next_level_role_id_verifier = $role_id_verifier->parent_id;
        $condition = array();
        $condition['id'] = $id;       
        if ($designation_id_old == 'Verifier') {
          if ($duty_obj->mapping_level == "Subdiv") {
            $created_by_local_body_code = $duty_obj->urban_body_code;
          }
          if ($duty_obj->mapping_level == "Block") {
            $created_by_local_body_code = $duty_obj->taluka_code;
          }
          $condition['created_by_local_body_code'] = $created_by_local_body_code;
        }
        $query = DB::table($schema . '.beneficiary')
        ->where($condition)->where('id',$id)->whereraw(" (dup_bank=0 or dup_bank IS NULL) and (dup_aadhar=0 or dup_aadhar IS NULL) and (dup_mobile=0 or dup_mobile IS NULL) and (no_aadhar=0 or no_aadhar IS NULL) and (no_mobile=0 or no_mobile IS NULL)")->where("next_level_role_id",$next_level_role_id_verifier);
  
       
        $row = $query->first();
        if (empty($row)) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        $is_error=0;
        if(empty(trim($request->sm_mobile_no))){
          $errors = array();
          $errorMsg = "Mobile Number is Required";
          array_push($errors, $errorMsg);
        }
        else{
          if (strlen(trim($request->sm_mobile_no)) !=10)  {
            $errors = array();
            $errorMsg = "Mobile Number Invalid";
            array_push($errors, $errorMsg);
          }
          if(!preg_match('/^[0-9]{10}+$/',$request->sm_mobile_no)) {
            $errors = array();
            $errorMsg = "Mobile Number Invalid";
            array_push($errors, $errorMsg);
          } 
          if($request->sm_mobile_no<1000000000) {
            $errors = array();
            $errors = array();
            $errorMsg = "Mobile Number Invalid";
            array_push($errors, $errorMsg);
         }
      }
      
   
        if($is_error==0){


          $c_time = date('Y-m-d H:i:s', time());
          DB::beginTransaction();
        
                
           
            $inputMain=array();
            $inputMain['sm_flag']=1;
            $inputMain['sm_mobile_no']=trim($request->sm_mobile_no);

              $upadated_main = DB::table($schema . '.beneficiary')
              ->where(['id' => $id, 'created_by_local_body_code' => $created_by_local_body_code, 
              'created_by_dist_code' => $district_code])->whereNull('is_lb_imported')->update($inputMain);

            $modelNameAcceptReject=new AcceptRejectInfo;
            $op_type = 'SMMARK';
            $modelNameAcceptReject->scheme_id =  $scheme_id;

            $modelNameAcceptReject->created_at =  $c_time;
            $modelNameAcceptReject->op_type =  $op_type;
            $modelNameAcceptReject->application_id = $request->id;
            $modelNameAcceptReject->user_id = $user_id;
            $modelNameAcceptReject->created_by_dist_code = $district_code;
            $modelNameAcceptReject->created_by_local_body_code = $created_by_local_body_code;
            $modelNameAcceptReject->ip_address = request()->ip();
            $is_accept_reject = $modelNameAcceptReject->save();
            //dump($upadated_main);dump($is_accept_reject);dump($enc_status);dd($is_inserted_arch);
            if($upadated_main && $is_accept_reject){
              DB::commit();
              $errors=array();
              $return_text = 'Beneficiary with  Id:'.$id.' has been marked as Sarasori Mukhyamantri and Sent to Approver for Approval';
              return redirect("mark-sm?scheme_id=".$scheme_id)->with('success', $return_text);

            }
            else{
              DB::rollback();
              $errors=array();
              $errorMsg = 'Error.. Please try different.';
               array_push($errors, $errorMsg);
            }
        }

      
      if(count($errors)>0){
        return redirect("/mark-sm?scheme_id=".$scheme_id)->with('errors', $errorMsg);
      }
    }    
      catch (\Exception $e) {
        //dd($e);
        return redirect("/")->with('danger', 'Not Allowed');
      } 
      
    }

    public function SmReject(Request $request){
      try {
        $this->middleware('auth');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = AuthChecker::getUserId();      
        if (empty($request->beneficiary_id)) {
          return redirect("/")->with('danger', 'Beneficiary ID Not Found');
        }
        $duty_obj = Configduty::where('user_id', $user_id)->first();
        if (empty($duty_obj)) {
          return redirect("/")->with('danger', 'Not Allowed...');
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
        $district_code = $duty_obj->district_code;
        if (!empty($scheme_obj->short_code)) {
          $schema = $scheme_obj->short_code;
        } else {
          $schema = "pension";
        }
        if ($designation_id_old == 'Verifier') {
          if ($duty_obj->mapping_level == "Subdiv") {
            $created_by_local_body_code = $duty_obj->urban_body_code;
          }
          if ($duty_obj->mapping_level == "Block") {
            $created_by_local_body_code = $duty_obj->taluka_code;
          }
        }
        $c_time = date('Y-m-d H:i:s', time());
        DB::beginTransaction();
        $inputMain=array();
        $inputMain['next_level_role_id']=-1;
        $inputMain['is_rejected']=1;
        $inputMain['is_verified']=2;  
        $inputMain['is_approved']=2;
        $inputMain['rejected_by']=$user_id; 
        $inputMain['rejected_date']=$c_time;
        $upadated_main = DB::table($schema . '.beneficiary')
              ->where(['id' => $id, 'created_by_local_body_code' => $created_by_local_body_code, 
              'created_by_dist_code' => $district_code])->whereNull('is_lb_imported')->update($inputMain);
        $modelNameAcceptReject=new AcceptRejectInfo;
        $op_type = 'SMREJECT';
        $modelNameAcceptReject->scheme_id = $scheme_id;
        $modelNameAcceptReject->created_at =  $c_time;
        $modelNameAcceptReject->op_type =  $op_type;
        $modelNameAcceptReject->application_id = $id;
        $modelNameAcceptReject->user_id = $user_id;
        $modelNameAcceptReject->created_by_dist_code = $district_code;
        $modelNameAcceptReject->created_by_local_body_code = $created_by_local_body_code;
        $modelNameAcceptReject->ip_address = request()->ip();
        $is_accept_reject = $modelNameAcceptReject->save();

        if($upadated_main && $is_accept_reject){
          DB::commit();
          $errors=array();
          $return_text = 'Beneficiary with  Id:'.$id.' has been Rejected';
          return redirect("mark-sm?scheme_id=".$scheme_id)->with('success', $return_text);

        }
        else{
          DB::rollback();
          $errors=array();
          $errorMsg = 'Error.. Please try again.';
           array_push($errors, $errorMsg);
        }     

        
      } catch (\Exception $e) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
    }

    public function SmRevert(Request $request) 
    {
      try {
        $this->middleware('auth');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = AuthChecker::getUserId();      
        if (empty($request->beneficiary_id)) {
          return redirect("/")->with('danger', 'Beneficiary ID Not Found');
        }
        $duty_obj = Configduty::where('user_id', $user_id)->first();
        if (empty($duty_obj)) {
          return redirect("/")->with('danger', 'Not Allowed...');
        }
        if (empty($request->scheme_id)) {
          return redirect("/")->with('danger', 'Scheme ID Not Found');
        }
        $scheme_id = $request->scheme_id;
        $id = $request->beneficiary_id;
        // echo $id;die;
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (empty($scheme_obj)) {
          return redirect("/")->with('danger', 'Scheme Not Found');
        }
        $district_code = $duty_obj->district_code;
        if (!empty($scheme_obj->short_code)) {
          $schema = $scheme_obj->short_code;
        } else {
          $schema = "pension";
        }
        if ($designation_id_old == 'Verifier') {
          if ($duty_obj->mapping_level == "Subdiv") {
            $created_by_local_body_code = $duty_obj->urban_body_code;
          }
          if ($duty_obj->mapping_level == "Block") {
            $created_by_local_body_code = $duty_obj->taluka_code;
          }
        }
        $c_time = date('Y-m-d H:i:s', time());
        DB::beginTransaction();
        $inputMain=array();
        $inputMain['next_level_role_id']=null;
        $inputMain['is_reverted']=1;
        $inputMain['is_verified']=0;  
        
        $upadated_main = DB::table($schema . '.beneficiary')
              ->where(['id' => $id, 'created_by_local_body_code' => $created_by_local_body_code, 
              'created_by_dist_code' => $district_code])->whereNull('is_lb_imported')->update($inputMain);
        $modelNameAcceptReject=new AcceptRejectInfo;
        $op_type = 'SMREVERT';
        $modelNameAcceptReject->scheme_id = $scheme_id;
        $modelNameAcceptReject->created_at =  $c_time;
        $modelNameAcceptReject->op_type =  $op_type;
        $modelNameAcceptReject->application_id = $id;
        $modelNameAcceptReject->user_id = $user_id;
        $modelNameAcceptReject->created_by_dist_code = $district_code;
        $modelNameAcceptReject->created_by_local_body_code = $created_by_local_body_code;
        $modelNameAcceptReject->ip_address = request()->ip();
        $is_accept_reject = $modelNameAcceptReject->save();

        if($upadated_main && $is_accept_reject){
          DB::commit();
          $errors=array();
          $return_text = 'Beneficiary with  Id:'.$id.' has been Reverted';
          return redirect("mark-sm?scheme_id=".$scheme_id)->with('success', $return_text);

        }
        else{
          DB::rollback();
          $errors=array();
          $errorMsg = 'Error.. Please try again.';
           array_push($errors, $errorMsg);
        }     

        
      } catch (\Exception $e) {
        // dd($e);
        return redirect("/")->with('danger', 'Not Allowed');
      }  
    }
    public function oapsmdsmark(Request $request)
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
      if(!in_array($designation_id_old, array('Verifier','Approver'))){
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      // dd($next_level_role_id_verifier);
      $type_des='Mark as Duare Sarkar VII Camps';
     
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
         ->whereNull('is_lb_imported')->where('created_by_dist_code', $district_code)/*->whereraw(" (dup_bank=0 or dup_bank IS NULL) and (dup_aadhar=0 or dup_aadhar IS NULL) and (dup_mobile=0 or dup_mobile IS NULL) and (no_aadhar=0 or no_aadhar IS NULL) and (no_mobile=0 or no_mobile IS NULL)")*/;
          if ($designation_id_old == 'Verifier') {
            $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
            if (!empty($application_type)) {
              if($application_type==1)
               $query = $query->whereNull('next_level_role_id')->whereNull('sm_ds_mark');
              if($application_type==2)
              $query = $query->whereNull('next_level_role_id')->where('sm_ds_mark',1)->where('sm_ds_mark_role_id',1);
              if($application_type==3)
              $query = $query->whereNull('next_level_role_id')->where('sm_ds_mark',1)->where('sm_ds_mark_role_id',2);
             
               
            }
          }
          // dd($query);
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
            $query = $query->whereNull('next_level_role_id')->where('sm_ds_mark',1)->where('sm_ds_mark_role_id',1);
            if($application_type==2)
            $query = $query->whereNull('next_level_role_id')->where('sm_ds_mark',1)->where('sm_ds_mark_role_id',2);
            
          }
        
        }
        //  $rawsql = $query->toSql();
        //  dd($rawsql);
        $serachvalue = $request->search['value'];
        if (empty($serachvalue)) {
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
            'id', 'created_by_dist_code', 'dob', 'assembly_name',
                'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag','sm_mobile_no','is_rejected','mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank','sm_ds_mark','sm_ds_mark_role_id'
          ]);
          $filterRecords = count($data);
        } else {
          if (is_numeric($serachvalue)) {
            // $ben_id = substr($serachvalue, -7);
            $ben_id=$serachvalue;
            if(strlen($ben_id)==12){
              $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                $query1->where('aadhar_no', $ben_id);
              });
            }
            else{
            $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
              $query1->where('id', $ben_id);
            });
             }
            $totalRecords = $query->count();
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
              [
                'id', 'created_by_dist_code', 'dob', 'assembly_name',
                'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag','sm_mobile_no','is_rejected','mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank','sm_ds_mark','sm_ds_mark_role_id'
              ]
            );
          } else {
          
            $query = $query->where(function ($query1) use ($serachvalue) {
              $query1->where('ben_fname', 'like', $serachvalue . '%')
                ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                ->orWhere('aadhar_no', 'like', $aadhar_no . '%')
                ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
            });
            $totalRecords = $query->count();
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
              [
                'id', 'created_by_dist_code', 'dob', 'assembly_name',
                'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag','sm_mobile_no','is_rejected','mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank','sm_ds_mark','sm_ds_mark_role_id'
              ]
            );
          }
          $filterRecords = count($data);
        }
        return datatables()->of($data)->setTotalRecords($totalRecords)
          ->setFilteredRecords($filterRecords)
          ->skipPaging()
          ->addColumn('view', function ($data) use ($scheme_id, $designation_id_old, $next_level_role_id_approver,$next_level_role_id_verifier) {
            $action = '<a href="ViewOapsmdsmark?id=' . $data->id  . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> View</a>';

           
              if($designation_id_old=='Verifier'){
                if(is_null($data->sm_ds_mark) && is_null($data->next_level_role_id)){
                  // echo 1;die;
                  
                    $action = $action. '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-'.$data->id.'" value="'.$data->id.'">Mark as Duare Sarkar VII Camps</button>';
                  
                   
                }
                else if($data->sm_ds_mark=1 && is_null($data->next_level_role_id)){
                    $action = $action.'&nbsp;&nbsp;&nbsp;&nbsp;Already Marked as Duare Sarkar VII Camps';
                }
                
              } 
            
              if($designation_id_old=='Approver'){
                
               
                 if($data->sm_ds_mark==1 && $data->sm_ds_mark_role_id==2 && is_null($data->next_level_role_id)){
                    $action = $action.'&nbsp;&nbsp;&nbsp;&nbsp;Approved';
                }
              }


            return $action;
          })->addColumn('check', function ($data) use ($designation_id_old) {
            if ($designation_id_old == 'Approver') {
              if ($data->sm_ds_mark_role_id == 1 && $data->sm_ds_mark==1) {
                return '<input type="checkbox" name="approvalcheck[]" onClick="controlCheckBox()" value="' . $data->id . '">';
              } else
                return '';
            } else {
              return '';
            }
          })
          ->addColumn('id', function ($data) {
            return $data->id;
          })
          ->addColumn('name', function ($data) {
            return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
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
          })
          ->rawColumns(['view', 'id', 'name', 'mask_aadhaar_no', 'bank_ifsc','bank_code','bank_ifsc','bank_ifsc', 'check'])
          ->make(true);
      }
  
      return view(
        'Sarasori_Mukhyamantri.oapsmdsmarklist',
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
          'scheme_id' => $scheme_id
        ]
      );
    }
    public function ViewOapsmdsmark(Request $request)
    {
      //dd('ok');
    try{
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = AuthChecker::getUserId();
      $id = $request->id;
     // dd($id);
      if (empty($request->id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }
     
      
      $duty_obj = Configduty::where('user_id', $user_id)->first();

      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $scheme_id = $request->scheme_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $type_des='Sarasori Mukhyamantri ';
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
        
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      $query = DB::table($schema . '.beneficiary')
     ->where('created_by_dist_code', $district_code)->where('id',$id)->where('created_by_dist_code', $district_code)->whereNull('next_level_role_id');
      $row = $query->first();
      // dd( $row);
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      //dd($row->aadhar_no);
      if( $designation_id_old=='Verifier'){
          if(!empty($row->aadhar_no) && trim($row->aadhar_no)!=''){
          $old_aadhar= $row->aadhar_no;
          $new_aadhar='';
          }
        else{
          $old_aadhar='';
          $new_aadhar='';
        }
      }
      else{
        if(!empty($row->old_aadhar_no)){
          $old_aadhar= $row->old_aadhar_no;
          }
        else{
          $old_aadhar='';
        }
        $new_aadhar= $row->aadhar_no;
        //dd($new_aadhar);
      }
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
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();    
      return view(
        'Sarasori_Mukhyamantri.ViewOapsmdsmark',
        [
          'designation_id_old' => $designation_id_old,
          'row' => $row,
          'id' => $id,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'docs' => $docs,
          'reject_revert_cause_list' => $reject_revert_cause_list,
          'scheme_id' => $scheme_id,
        ]
      );
    }
    catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
    }
    public function oapsmdsmarkPost(Request $request)
    {
      try{
        $this->middleware('auth');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = AuthChecker::getUserId();        
        if (empty($request->beneficiary_id)) {
          return redirect("/")->with('danger', 'Beneficiary ID Not Found');
        }
        $duty_obj = Configduty::where('user_id', $user_id)->first();
        if (empty($duty_obj)) {
          return redirect("/")->with('danger', 'Not Allowed...');
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
        $district_code = $duty_obj->district_code;
        if (!empty($scheme_obj->short_code)) {
          $schema = $scheme_obj->short_code;
        } else {
          $schema = "pension";
          
        }
        $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
        $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
        $next_level_role_id_approver = $role_id_approver->parent_id;
        $next_level_role_id_verifier = $role_id_verifier->parent_id;
        $condition = array();
        $condition['id'] = $id;   
        if ($designation_id_old == 'Verifier') {
          if ($duty_obj->mapping_level == "Subdiv") {
            $created_by_local_body_code = $duty_obj->urban_body_code;
          }
          if ($duty_obj->mapping_level == "Block") {
            $created_by_local_body_code = $duty_obj->taluka_code;
          }
          $condition['created_by_local_body_code'] = $created_by_local_body_code;
        }
        $query = DB::table($schema . '.beneficiary')
        ->where($condition)->where('id',$id)->whereNull("next_level_role_id");
        if ($designation_id_old == 'Approver') {
          $query =$query->where('sm_ds_mark',1)->where('sm_ds_mark_role_id',1);
        }
        if ($designation_id_old == 'Verifier') {
          $query =$query->whereNull('sm_ds_mark');
        }
        $row = $query->first();
        if (empty($row)) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        $is_error=0;
       
        //dd($row);
   
        if($is_error==0){


          $c_time = date('Y-m-d H:i:s', time());
          DB::beginTransaction();
        
          $in_pension_id = 'ARRAY[' . "'$request->beneficiary_id'" . ']';    
          $comments = NULL;
          if ($designation_id_old == 'Approver') {
            $is_inserted_status_arr = DB::select("select ".$schema.".dsmark_for_sm_approve(in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'SMDSMARKAPPROVE', in_custom_comment => '".$comments."')");
          //dd($is_inserted_status_arr);
          $is_inserted_status=$is_inserted_status_arr[0]->dsmark_for_sm_approve;
          }
          if ($designation_id_old == 'Verifier') {
          $is_inserted_status_arr = DB::select("select ".$schema.".dsmark_for_sm(in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'SMDSMARK', in_custom_comment => '".$comments."')");
          //dd($is_inserted_status_arr);
          $is_inserted_status=$is_inserted_status_arr[0]->dsmark_for_sm;
          }
            if($is_inserted_status==1){
              DB::commit();
              $errors=array();
              if ($designation_id_old == 'Approver') {
                $return_text = 'Beneficiary with  Id:'.$id.' Duare Sarkar VII Camps marking request has been sucessfully approved';

              }
              if ($designation_id_old == 'Verifier') {
              $return_text = 'Beneficiary with  Id:'.$id.' has been marked as Duare Sarkar VII Camps';
              }
              return redirect("oapsmdsmark?scheme_id=".$scheme_id)->with('success', $return_text);

            }
            else if($is_inserted_status==10){
              DB::rollback();
              $errors=array();
               $errorMsg = 'Total DS mark  Applications  exceeds the Quota';
               array_push($errors, $errorMsg);
            }
            else{
              DB::rollback();
              $errors=array();
              $errorMsg = 'Error.. Please try different.';
               array_push($errors, $errorMsg);
            }
        }

      
      if(count($errors)>0){
        return redirect("/oapsmdsmark?scheme_id=".$scheme_id)->with('errors', $errors);
      }
    }    
      catch (\Exception $e) {
        //dd($e);
        return redirect("/")->with('danger', 'Not Allowed');
      } 
      
    }
    public function oapsmdsmarkListExcel(Request $request)
    {
      try{
        if (empty($request->scheme_id)) {
            return redirect('/')->with('error', 'Scheme Id Required');
        }
        if (!ctype_digit($request->scheme_id)) {
            return redirect('/')->with('error', 'Scheme Id Invalid');
        }
        $scheme_id = $request->scheme_id;
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $mapping_level = $roleObj['mapping_level'];
                $district_code = $roleObj['district_code'];
                if ($roleObj['is_urban'] == 1) {
                    $urban_body_code = $roleObj['urban_body_code'];
                } else {
                    $urban_body_code = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if ($is_active == 0) {
            return redirect('/')->with('error', 'User not Authorized for this scheme');
        }
        $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
        $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
        $next_level_role_id_approver = $role_id_approver->parent_id;
        $next_level_role_id_verifier = $role_id_verifier->parent_id;
        $condition = array();
        $condition["sm_ds_mark"] = 1;
        $designation_id_old = Auth::user()->designation_id_old;
        if ($designation_id_old == 'Approver') {
            //dd(123);
            $condition["created_by_dist_code"] = $district_code;
        }
        if ($designation_id_old == 'Verifier' || $designation_id_old == 'Operator') {
            //dd(333);
            $condition["created_by_dist_code"] = $district_code;
            $condition["created_by_local_body_code"] = $urban_body_code;
        }
        $scheme_name_row = Scheme::where('id', $scheme_id)->first();
        $scheme_name = $scheme_name_row->scheme_name;
        $schema = $scheme_name_row->short_code;
        $query = DB::connection('pgsql_mis')->table('' . $schema . '.beneficiary')->whereNull('next_level_role_id')->where($condition);
        $data = $query->select(
          'id',
          'scheme_id',
          'created_by_dist_code',
          'ben_fname',
          'ben_mname',
          'ben_lname',
          'father_fname',
          'father_mname',
          'father_lname',
          'mother_fname',
          'mother_mname',
          'mother_lname',
          'mobile_no',
          'dob',
          'ben_age',
          'caste',
          'next_level_role_id',
          'block_ulb_name',
          'gp_ward_name',
          'village_town_city',
          'bank_ifsc',
          'bank_code',
          'house_premise_no',
          'sm_ds_mark',
          'sm_ds_mark_role_id' 
      )->orderBy('ben_fname')->orderBy('gp_ward_name')->get();
      $filename = "OAP_DS_MARK_".$scheme_name . "-" .  "-" . date('d/m/Y') . '-' . time() . ".xls";
      header("Content-Type: application/xls");
      header("Content-Disposition: attachment; filename=" . $filename);
      header("Pragma: no-cache");
      header("Expires: 0");
      echo '<table border="1">';
      echo '<tr><td colspan="9">Mark as Duare Sarkar VII Camps</td></tr>';
      echo '<tr><th>Beneficiary Id</th><th>Full Name</th><th>Mobile No.</th><th>Father Name</th><th>Block/Municipality</th><th>GP/WARD</th><th>Bank IFSC</th><th>Bank Account No.</th><th>Status</th></tr>';
      if (count($data) > 0) {
          foreach ($data as $row) {
              $app_id = $row->id;
              $app_id = "'$app_id'";
              if (!empty($row->ben_fname)) {
                  $ben_fname = trim($row->ben_fname);
              } else {
                  $ben_fname = '';
              }
              if (!empty($row->ben_mname)) {
                  $ben_mname = trim($row->ben_mname);
              } else {
                  $ben_mname = '';
              }
              if (!empty($row->ben_lname)) {
                  $ben_lname = trim($row->ben_lname);
              } else {
                  $ben_lname = '';
              }
              

              //$phase_des = $this->getPhaseDes($row->ds_phase);
              $ben_fullname = $ben_fname . " " . $ben_mname . " " . $ben_lname;
              if (!empty($row->father_fname)) {
                  $father_fname = trim($row->father_fname);
              } else {
                  $father_fname = '';
              }
              if (!empty($row->father_mname)) {
                  $father_mname = trim($row->father_mname);
              } else {
                  $father_mname = '';
              }
              if (!empty($row->father_lname)) {
                  $father_lname = trim($row->father_lname);
              } else {
                  $father_lname = '';
              }
              $father_fullname = $father_fname . " " . $father_mname . " " . $father_lname;
              $bank_code = (string) $row->bank_code;
              if (!empty($bank_code))
                  $f_bank_code = "'$bank_code'";
              else
                  $f_bank_code = $bank_code;
                $status='';
              if ($row->sm_ds_mark_role_id==1)
                      $status = "Marked as DS Phase VII but Approval Pending";
              elseif ($row->sm_ds_mark_role_id==2)
                 $status = "Marked as DS Phase VII and Approved";
              echo "<tr><td>" . $app_id . "</td><td>" . $ben_fullname . "</td><td>" . $row->mobile_no . "</td><td>" . $father_fullname . "</td><td>" . trim($row->block_ulb_name) . "</td><td>" . trim($row->gp_ward_name) . "</td><td>" . trim($row->bank_ifsc) . "</td><td>" . $f_bank_code . "</td><td>" . $status . "</td></tr>";
          }
      } else {
          echo '<tr><td colspan="9">No Records found</td></tr>';
      }
      echo '</table>';
    }catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    } 
        
    }
    public function oapsmdsmarkPostBulkApprove(Request $request)
    {
      try{
      $this->middleware('auth');
      //dd('ok');
      $designation_id_old = Auth::user()->designation_id_old;
      if ($designation_id_old != 'Approver') {
        return redirect("/")->with('error', 'Not Allowed');
      }
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
      $district_code = $duty_obj->district_code;
      $applicationid_arr = array();
      $inputs = request()->input('approvalcheck');
      $c_time = date('Y-m-d H:i:s', time());
      //dd($inputs);
      foreach ($inputs as $input) {
        array_push($applicationid_arr, $input);
      }
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
        
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      DB::beginTransaction();
      $implode_application_arr = implode("','", $applicationid_arr);
      $in_pension_id = 'ARRAY[' . "'$implode_application_arr'" . ']';
      $comments = NULL;
      $is_inserted_status_arr = DB::select("select ".$schema.".dsmark_for_sm_approve(in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'SMDSMARKAPPROVE', in_custom_comment => '".$comments."')");
      $is_inserted_status=$is_inserted_status_arr[0]->dsmark_for_sm_approve;
     if($is_inserted_status==1){
            DB::commit();
            $errors=array();
            
           $return_text = 'Beneficiaries Duare Sarkar VII Camps marking request has been sucessfully approved';

      
           
           return redirect("oapsmdsmark?scheme_id=".$scheme_id)->with('success', $return_text);

          }
          else if($is_inserted_status==10){
            DB::rollback();
            $errors=array();
             $errorMsg = 'Total DS mark  Applications  exceeds the quota';
             array_push($errors, $errorMsg);
             return redirect("oapsmdsmark?scheme_id=".$scheme_id)->with('errors', $errors);

          }
          else{
            DB::rollback();
            $errors=array();
             $errorMsg = 'Error.. Please try different.';
             array_push($errors, $errorMsg);
             return redirect("oapsmdsmark?scheme_id=".$scheme_id)->with('errors', $errors);
          }
    }catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
      
    }
    function oapsmdsmarkoMisReport(Request $request)
    {
      $base_date  = '2020-01-01';
      $c_time = Carbon::now();
      $c_date = $c_time->format("Y-m-d");
      $is_active = 0;
      $roleArray = $request->session()->get('role');
      $designation_id_old = Auth::user()->designation_id_old;
      $district_visible = $is_urban_visible = $block_visible = 1;
      $municipality_visible = 0;
      $gp_ward_visible = 0;
      $muncList = collect([]);
      $gpList = collect([]);
      $userId = Auth::user()->id;
      $scheme_code_in=array();
      $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 and id = 10 order by scheme_name"));
      foreach($scheme_list as $scheme_item){
        array_push($scheme_code_in,$scheme_item->id);

      }
      if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
          $district_visible = $is_urban_visible = $block_visible = 1;
      } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
        $district_code = NULL;
        $is_urban = NULL;
        $blockCode = NULL;
        $scsctvisible = 0;
        foreach ($roleArray as $roleObj) {
          if (in_array($roleObj['scheme_id'], $scheme_code_in)) {
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
      $districts = District::get();
      //$is_urban_visible=0;
      $block_visible=0;
      $municipality_visible=0;
      $gp_ward_visible=0;
      $is_urban_visible=0;
      $block_visible=0;
      return view(
          'Sarasori_Mukhyamantri.oapsmdsmarkoMisReport',
          [
              'scheme_list' => $scheme_list,
              'districts' => $districts,
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
              'muncList' => $muncList,
              'designation_id_old' => $designation_id_old,
          ]
      );
    }
    public function oapsmdsmarkoMisReportPost(Request $request)
    {
        //dd($request->all());
        //$ds_phase_list = Config::get('constants.ds_phase.phaselist');
        $scheme_id = $request->scheme_id;
        $ds_phase = $request->ds_phase;
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        $gp_ward = $request->gp_ward;
        $select_year= $request->select_year;
        $select_month= $request->select_month;
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
            'gp_ward' => 'nullable|integer',
            'from_date'    => 'nullable|date|after_or_equal:' . $base_date . '|before_or_equal:' . $c_date,
            'to_date'      => 'nullable|date|after_or_equal:from_date|before_or_equal:' . $c_date,
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
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $scheme_row=Scheme::where('id',$scheme_id)->first();
            $user_msg = " Duare Sarkar VII Camps Marking Mis Report for the Scheme ". $scheme_row->scheme_name;
            $title = $user_msg;
            //dd($title);

            $data = array();
            $return_status = 1;
            $return_msg = '';
            $heading_msg = '';
            $external = 0;
            $external_arr = array();
            $external_filter = array();
            $from_date=NULL;
            $to_date=NULL;
            $caste=NULL;
            $ds_phase=NULL;
            if (!empty($gp_ward)) {
                if ($urban_code == 1) {
                    $column = "Ward";
                    $heading_msg =  $user_msg . ' of the Ward ' . $gp_ward_name;
                    $data = $this->getWardWiseOapsmDs($scheme_id,$district, $block, $muncid, $gp_ward, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                } else {
                    $column = "GP";
                    $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
                    $data = $this->getGpWiseOapsmDs($scheme_id,$district, $block, NULL, $gp_ward, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                }
            } else if (!empty($muncid)) {
                $column = "Ward";
                $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
                $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
                $data = $this->getWardWiseOapsmDs($scheme_id,$district, $block, $muncid, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
            } else if (!empty($block)) {
                if ($urban_code == 1) {
                    $column = "Municipality";
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getMuncWiseOapsmDs($scheme_id,$district, $block, NULL, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                } else if ($urban_code == 2) {
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getGpWiseOapsmDs($scheme_id,$district, $block, NULL, $gp_ward, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                }
            } else {

                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getSubDivWiseOapsmDs($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getBlockWiseOapsmDs($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getBlockWiseOapsmDs($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                        $data2 = $this->getSubDivWiseOapsmDs($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getDistrictWiseOapsmDs($scheme_id,NULL, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);

                    $external = 0;
                }
            }
            if (!empty($caste)) {
                $heading_msg = $heading_msg . " for the Caste  " . $caste;
            }
            if (!empty($ds_phase)) {
                $heading_msg = $heading_msg . " of the " . $ds_phase_list[$ds_phase];
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
   
  
    
   
    public function getDistrictWiseOapsmDs($scheme_id,$district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL, $select_year= NULL , $select_month=NULL)
    {
        //dd($select_month);
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $month = "";
      if($select_month != ""){
        $month = "AND trim(TO_CHAR(approval_date::date, 'Month')) = '" . $select_month . "'";
      }
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
        $query = "select main.location_id,main.location_name,
        COALESCE(m_cap.capacity,0) as capacity ,
		COALESCE(bp_main.verification_pending,0) as verification_pending,
		COALESCE(bp_main.total_mark,0) as total_mark ,
		COALESCE(bp_main.mark_with_pending,0) as mark_with_pending,
		COALESCE(bp_main.mark_and_pending,0) as mark_and_pending 
        from
        (
        select district_code as location_id,district_name as location_name
        from public.m_district  
        ) as main  LEFT JOIN
        (
              select sum(capacity) as capacity,
              district_code
              from public.m_dsmark_sm_cap where scheme_id=10 
              group by district_code
         )   as m_cap ON main.location_id=m_cap.district_code
		LEFT JOIN
        (
              select 
			    count(1)  as verification_pending,
		      count(1) filter(WHERE sm_ds_mark=1) as total_mark,
		      count(1) filter(WHERE sm_ds_mark=1 and sm_ds_mark_role_id=1) as mark_with_pending,
		      count(1) filter(WHERE sm_ds_mark=1 and sm_ds_mark_role_id=2) as mark_and_pending,

              created_by_dist_code
              from " . $schema . ".beneficiary where next_level_role_id IS NULL 
             
              group by created_by_dist_code
         )  
        as bp_main ON main.location_id=bp_main.created_by_dist_code
         order by main.location_name";
          //echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        // dd($result);
        return $result;
    }
    
}
