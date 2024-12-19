<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use App\PensionOAPWCD;
//Dynamic Doc
use App\BenDocsOAPWCD;
use App\BenDocsArcOAPWCD;
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
use App\DsPhase;
use App\BlkUrbanlEntryMapping;
use App\OAPMobileUnique;
use App\OAPAdhaarUnique;
use App\OAPBankUnique;
use App\AcceptRejectInfo;
use App\BeneficiaryDupBlank;
use App\BenDocs;
use App\Helpers\AuthChecker;
use App\Traits\TraitCasteCertificateValidate;
use App\Traits\TraitLifeCertificateValidate;
use App\Traits\TraitAadharValidate;
use App\MapLavel;
use App\UpdateBenDetails;
use Illuminate\Support\Facades\Session;


class LifeCertificateCheckController extends Controller
{
    use TraitLifeCertificateValidate;
    public function __construct()
    {
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
        $this->base_dob_chk_date = date('Y-m-d');
        $this->max_dob =date('Y-m-d', strtotime('+60 years'));
        //dd($this->max_dob);
        $this->state_login_next_level_role_id_arr = Config::get('constants.state_login_next_level_role_id');
    }
    public function selectSchemeBioAuth(Request $request)
    {
        try{
            $user_id = AuthChecker::getUserId();
              $schemes = DB::select(DB::raw("select id,scheme_name,display_name,is_active from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
              return view(
                'BioAuthLifeCertificate/SchemeSelection',
                [
                  'scheme_list' => $schemes,
                ]
              );
            
          }catch (\Exception $e) {
            return redirect("/")->with('danger', 'Not Allowed');
          }
    }
    public function listBioAuth(Request $request)
    {
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = AuthChecker::getUserId();
      $scheme_id = (int)$request->scheme_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name','Verifier')->first();
      $next_level_role_id=$mapArr->parent_id;
      $type_des='Beneficiary List';
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
      $where_condition='  scheme_id='.$scheme_id;
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
        $where_condition .= " AND created_by_dist_code=".$district_code." AND created_by_local_body_code=" . $created_by_local_body_code . " ";
        //$where_condition .= " AND B.created_by_dist_code=".$district_code." AND B.created_by_local_body_code=" . $created_by_local_body_code . " ";

        if (!empty($request->block_ulb_code)) {
            $where_condition .= " AND block_ulb_code=" . $request->block_ulb_code . " ";
        }
        if (!empty($request->gp_ward_code)) {
            $where_condition .= " AND  gp_ward_code=" . $request->gp_ward_code . "";
        }
      }
      if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
        $is_rural = 2;
        $verifier_type = 'Block';
        $urban_bodys = collect([]);
        $taluka_code = $duty_obj->taluka_code;
        $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
        $where_condition .= " AND created_by_dist_code=".$district_code." AND created_by_local_body_code=" . $created_by_local_body_code . " ";
        //$where_condition .= " AND B.created_by_dist_code=".$district_code." AND B.created_by_local_body_code=" . $created_by_local_body_code . " ";
        if (!empty($request->gp_ward_code)) {
            $where_condition .= " AND  gp_ward_code=" . $request->gp_ward_code . "";
        }
      }
      if ($duty_obj->mapping_level == "District") {
        $district_list_obj = District::get();
        $verifier_type = 'District';
        $is_rural = NULL;
        $created_by_local_body_code = NULL;
        $where_condition .= " AND created_by_dist_code=".$district_code;
        //$where_condition .= " AND B.created_by_dist_code=".$district_code;
      }
      if ($request->filter_1 !='') {
        if($request->filter_1==1){
            $where_condition .= " AND life_certificate_checked=1 and life_certificate_pass=1";

        }
        if($request->filter_1==2){
            $where_condition .= " AND life_certificate_checked=1 and life_certificate_pass IN (2,0,4)";

        }
        if($request->filter_1==3){
            $where_condition .= " AND life_certificate_checked IS NULL and life_certificate_pass IS NULL";


        }
    }
    if (request()->ajax()) {
      $offset = $request->input('start');
      $limit = $request->input('length');
      $query = DB::table($schema . '.beneficiaries')->whereRaw("(".$where_condition.")");
      $serachvalue = $request->search['value'];
      if (empty($serachvalue)) {
         $totalRecords = $query->count();
        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
          'id', 'created_by_dist_code', 'dob', 'assembly_name',
          'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
          'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'aadhar_edit_role_id','life_certificate_checked','life_certificate_pass','mobile_no','last_biometric','life_certificate_lastdatetime'
        ]);

        $filterRecords = count($data);
      } else {
        if (is_numeric($serachvalue)) {
          $ben_id = $serachvalue;
          $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
            $query1->where('id', $ben_id)
              ->orWhere('bank_code', $serachvalue);
          });
           $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'aadhar_edit_role_id','life_certificate_checked','life_certificate_pass','mobile_no','last_biometric','life_certificate_lastdatetime'
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
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'aadhar_edit_role_id','life_certificate_checked','life_certificate_pass','mobile_no','last_biometric','life_certificate_lastdatetime'
            ]
          );
        }
        $filterRecords = count($data);
      }
      // dd($data);
      return datatables()->of($data)
          ->addIndexColumn()
          ->addColumn('action', function ($data) use ($scheme_id, $designation_id_old,$next_level_role_id) {
                $action = '';
                if(is_null($data->life_certificate_checked) && is_null($data->life_certificate_pass)){
           
                $action = '<button type="button" id="validatebtn_'. $data->id.'" value="'. $data->id.'" class="btn btn-xs btn-primary validate">Check</button>&nbsp; &nbsp;';
                }
                else{
                    if($data->life_certificate_checked==1 && $data->life_certificate_pass==1){
                        $action = '<i class="fa fa-check text-success"></i><b>Passed ('.date('d/m/Y',strtotime($data->last_biometric)).')</b> <button type="button" id="validatebtn_'. $data->id.'" value="'. $data->id.'" class="btn btn-xs btn-primary validate">Re-Check</button>&nbsp; &nbsp;<button type="button" id="responsebtn_'. $data->id.'" value="'. $data->id.'" class="btn btn-xs btn-info response">View Response</button>';
                    }
                    elseif($data->life_certificate_checked==1 && $data->life_certificate_pass==2){
                        $action = '<i class="fa fa-close text-danger"></i><b>Not passed ('.date('d/m/Y',strtotime($data->life_certificate_lastdatetime)).')</b><button type="button" id="validatebtn_'. $data->id.'" value="'. $data->id.'" class="btn btn-xs btn-primary validate">Re-Check</button> &nbsp; &nbsp;<button type="button" id="responsebtn_'. $data->id.'" value="'. $data->id.'" class="btn btn-xs btn-info response">View Response</button>';

                    }
                    elseif($data->life_certificate_checked==1 && $data->life_certificate_pass==0){
                        $action = '<i class="fa fa-close text-danger"></i><b>Not passed ('.date('d/m/Y',strtotime($data->life_certificate_lastdatetime)).')</b><button type="button" id="validatebtn_'. $data->id.'" value="'. $data->id.'" class="btn btn-xs btn-primary validate">Re-Check</button> &nbsp; &nbsp;<button type="button" id="responsebtn_'. $data->id.'" value="'. $data->id.'" class="btn btn-xs btn-info response">View Response</button>';

                    } elseif($data->life_certificate_checked==1 && $data->life_certificate_pass==4){
                        $action = '<i class="fa fa-close text-danger"></i><b>Not passed ('.date('d/m/Y',strtotime($data->life_certificate_lastdatetime)).')</b><button type="button" id="validatebtn_'. $data->id.'" value="'. $data->id.'" class="btn btn-xs btn-primary validate">Re-Check</button> &nbsp; &nbsp;<button type="button" id="responsebtn_'. $data->id.'" value="'. $data->id.'" class="btn btn-xs btn-info response">View Response</button>';

                    }
                }
              
            return $action;
          })->addColumn('id', function ($data) {
              return $data->id;
          })
          ->addColumn('name', function ($data) {
              return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
          })
          ->addColumn('block_ulb_name', function ($data) {
            return ''; 
          })
          ->addColumn('gp_ward_name', function ($data) {
            return ''; 
          })
          ->addColumn('aadhar_no', function ($data) {
             
                      return ''; 
                  
          })
      
          ->addColumn('mobile_no', function ($data) {

              return $data->mobile_no;
          })
         
      
          // ->with('completed', $complete)
          ->rawColumns(['id', 'name', 'block_ulb_name', 'gp_ward_name', 'action', 'mobile_no',  'aadhar_no'])
          ->make(true);
        }
        return view(
          'BioAuthLifeCertificate.list',
          [
            'designation_id_old' => $designation_id_old,
            'verifier_type' => $verifier_type,
            'created_by_local_body_code' => $created_by_local_body_code,
            'is_rural' => $is_rural,
            'scheme_id' => $scheme_id,
            'gps' => $gps,
            'urban_bodys' => $urban_bodys,
            'gps' => $gps,
            'district_code' => $district_code,
            'type_des' => $type_des
          ]
        );
    }
    public function LifeCertificateValidatePost(Request $request)
    {
      
      $this->middleware('auth');
      $scheme_id = (int)$request->scheme_id;
      $roleArray = $request->session()->get('role');
      $is_active = 0;
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
      $return_status = 0;
      $return_msg = '';
      if ($is_active == 0 || empty($distCode)) {
          $return_status = 0;
          $return_text = 'User Disabled';
          $return_msg = array("" . $return_text);
          $error_found=1;
      }
      $user_id = AuthChecker::getUserId();
      $beneficiary_id = $request->application_id;
      if (empty($beneficiary_id)) {
        $return_status = 0;
        $return_text = 'Beneficiary Id is Required';
        $return_msg = array("" . $return_text);
        $error_found=1;
    }
    $insert_arr=array();
    $c_time = date('Y-m-d H:i:s', time());
    $scheme_row = Scheme::where('id', $scheme_id)->first();
    if (!empty($scheme_row->short_code)) {
        $schema = $scheme_row->short_code;
    } else {
        $schema = "pension";
    }
    $row = DB::table($schema . '.beneficiaries')->where('id', $beneficiary_id)->select('ben_fname','ben_mname','ben_lname','caste_certificate_no','application_id','aadhar_no','wbpds_ration_card_no')->first();
    // dd($beneficiary_id);
    if (empty($beneficiary_id)) {
      return redirect('LifeCertificateList?scheme_id=' . $scheme_id)->with('error', ' Beneficiary Id Not found in Db');
    }
    $ben_name=trim($row->ben_fname) . ' ' . trim($row->ben_mname) . ' ' . trim($row->ben_lname);
    $ben_fullname = str_replace(' ', '', $ben_name);
    $aadhar_no=$row->aadhar_no;
    $insert_arr['created_by_local_body_code']=$blockCode;
    $insert_arr['api_hit_time']=date('Y-m-d H:i:s', time());
    $insert_arr['loginid']=$user_id;
    $insert_arr['ip_address']= $request->ip();
    // if(!empty($request->module_type)){
    // $insert_arr['module_type']=$request->module_type;
    // }
    if(!empty($beneficiary_id)){
        $insert_arr['beneficiary_id']=$beneficiary_id;
    }
    DB::beginTransaction();
    if (!empty($aadhar_no)) {
    try {
      $insert_arr['api_hit_time']=date('Y-m-d H:i:s', time());
      $validation_arr=$this->validate_life_certificate($aadhar_no=$aadhar_no,$ben_fullname= $ben_fullname); 
        //  dd($validation_arr);
      if($validation_arr['httpcode']==500){
        $return_text = 'No Reponse from Khadyasathi..  Please try after sometimes';
        return redirect('LifeCertificateList?scheme_id=' . $scheme_id)->with('error', $return_text);
    }
    $insert_arr['m_type']=2;
    $insert_arr['response_text']=$validation_arr['response_text'];
    $c_time1=date('Y-m-d H:i:s', time());
    $insert_arr['api_response_time']= $c_time1;
    $insert_arr['httpcode']=$validation_arr['httpcode'];
    $insert=DB::table('pension.ben_lc_api_response_track')->insert($insert_arr);
    if($validation_arr['is_success']==1){
      $return_text=$validation_arr['message'];  
      $update_arr=array();
      $update_arr['life_certificate_checked']=1;
      $update_arr['life_certificate_lastdatetime']=$c_time1;
      $update_arr['life_certificate_pass']=$validation_arr['code'];
      $update_arr['action_by']=$user_id;
      $update_arr['action_ip_address']=$request->ip();
      $update_arr['action_type'] =  class_basename(request()->route()->getAction()['controller']) ;
      if($validation_arr['match_found']==1){
          $is_error=0;
          if(!empty($validation_arr['last_biometric'])){
              $update_arr['last_biometric']=$validation_arr['last_biometric'];
       }
      }
      else{
          $is_error=1;
          $return_text=$return_text;  
          $return_msg = array("" . $return_text);
      }
      $update=DB::table($schema . '.beneficiaries')->where('id',$beneficiary_id)->update($update_arr);
    }
    else{
      $is_error=1;
      $update=1; 
      $return_text=$validation_arr['message'];  
      $return_msg = array("" . $return_text);
    }
    $return_text=$return_text.' of the Beneficiary with Beneficiary Id:'.$beneficiary_id;
    // print $insert;dd($update);
    // dd($return_msg);
    // dd($validation_arr);
    if($insert && $update){
      DB::commit();
      if($validation_arr['match_found']==1){
        return redirect('LifeCertificateList?scheme_id=' . $scheme_id)->with('message',  $return_text);
      }
      else{
        // dd($return_msg);
        return redirect('LifeCertificateList?scheme_id=' . $scheme_id)->with('errors', $return_msg);
      }
    }
    else{
      DB::rollBack();
      $return_text = 'Match Not Found';
      return redirect('LifeCertificateList?scheme_id=' . $scheme_id)->with('error', $return_text);
    }
  } catch (\Exception $e) {
      // dd($e);
      DB::rollBack();
      $return_text = 'Match Not Found';
      return redirect('LifeCertificateList?scheme_id=' . $scheme_id)->with('error', $return_text);
    }
  }
  else{
    return response()->json([
        'status' => 200,
        'errors' => 'No record found',
    ]);
   }
}
public function LifeCertificateGetResponse(Request $request)
{
  $beneficiary_id = $request->application_id;
  if(!empty($beneficiary_id))
  {
    $kh_details = DB::table('pension.ben_lc_api_response_track')->selectRaw("SUBSTRING(REPLACE(response_text, '\', '') FROM 2 FOR LENGTH(REPLACE(response_text, '\', '')) - 2)::jsonb->>'name' as name,SUBSTRING(REPLACE(response_text, '\', '') FROM 2 FOR LENGTH(REPLACE(response_text, '\', '')) - 2)::jsonb->>'txnTime' as txnTime")->where('beneficiary_id', $beneficiary_id)->first();
    return response()->json([
        'name' => $kh_details->name,
        'txntime' => $kh_details->txntime
    ]);
  }else{
    return redirect('LifeCertificateList?scheme_id=' . $scheme_id)->with('error', 'Beneficiary ID not found');
  }

    
}

}
