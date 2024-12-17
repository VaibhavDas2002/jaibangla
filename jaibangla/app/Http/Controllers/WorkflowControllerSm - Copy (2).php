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
      $user_id = Auth::user()->id;
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
      $user_id = Auth::user()->id;
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
        $user_id = Auth::user()->id;        
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
        $user_id = Auth::user()->id;      
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
        $user_id = Auth::user()->id;      
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
      if ($designation_id_old != 'Verifier') {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      // dd($next_level_role_id_verifier);
      $type_des='Mark as Duare Sarkar Phase 7';
     
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
               $query = $query->where('next_level_role_id',$next_level_role_id_verifier);
              if($application_type==2)
              $query = $query->where('sm_ds_mark',1);
             
               
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
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag','sm_mobile_no','is_rejected','mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank','sm_ds_mark'
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
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag','sm_mobile_no','is_rejected','mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank','sm_ds_mark'
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
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag','sm_mobile_no','is_rejected','mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank','sm_ds_mark'
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
                if(is_null($data->sm_ds_mark) && $data->next_level_role_id==$next_level_role_id_verifier){
                  // echo 1;die;
                  
                    $action = $action. '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-'.$data->id.'" value="'.$data->id.'">Mark as Duare Sarkar Phase 7</button>';
                  
                   
                }
                else if(!is_null($data->sm_ds_mark) && $data->next_level_role_id==$next_level_role_id_verifier){
                    $action = $action.'&nbsp;&nbsp;&nbsp;&nbsp;Already Marked as Duare Sarkar Phase 7';
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
      $user_id = Auth::user()->id;
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
        $user_id = Auth::user()->id;        
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
        ->where($condition)->where('id',$id)->where("next_level_role_id",$next_level_role_id_verifier);
        //dd('ok');
      
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
          $is_inserted_status_arr = DB::select("select ".$schema.".dsmark_for_sm(in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'SMDSMARK', in_custom_comment => '".$comments."')");
          //dd($is_inserted_status_arr);
          $is_inserted_status=$is_inserted_status_arr[0]->dsmark_for_sm;
            if($is_inserted_status==1){
              DB::commit();
              $errors=array();
              $return_text = 'Beneficiary with  Id:'.$id.' has been marked as Duare Sarkar Phase 7';
              return redirect("oapsmdsmark?scheme_id=".$scheme_id)->with('success', $return_text);

            }
            else if($is_inserted_status==9){
              DB::rollback();
              $errors=array();
               $errorMsg = 'Total DS mark  Applications  exceeds the quota';
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
        return redirect("/oapsmdsmark?scheme_id=".$scheme_id)->with('errors', $errorMsg);
      }
    }    
      catch (\Exception $e) {
        //dd($e);
        return redirect("/")->with('danger', 'Not Allowed');
      } 
      
    }
    
}
