<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Configduty;
use App\getModelFunc;
use App\LotMaster;
use App\LotDetails;
use App\AvLotmaster;
use App\AvLotdetails;
use App\FailedBankDetails;
use App\UrbanBody;
use App\GP;
use App\BankDetails;
use App\DataSourceCommon;
use Maatwebsite\Excel\Facades\Excel;
use App\DocumentType;
use Validator;
use App\Helpers\Helper;
use Carbon\Carbon;
use App\District;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use App\DsPhase;
use App\Scheme;
use App\RejectRevertReason;
use App\AcceptRejectInfo;
class StopBeneficiaryController  extends Controller
{
    public function __construct()
    {
        set_time_limit(300);
        $this->middleware('auth');
    }
    function selectscheme(Request $request)
    {
        $this->middleware('auth');
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $userId = Auth::user()->id;      
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
        //dd($scheme_list);
        return view(
            'stop-beneficiary.selectScheme',
            [
                'scheme_list' => $scheme_list,
                'designation_id_old' => $designation_id_old,
            ]
        );
    }
    function selectschemehod(Request $request)
    {
        $this->middleware('auth');
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $userId = Auth::user()->id;      
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
        //dd($scheme_list);
        return view(
            'stop-beneficiary.selectschemehod',
            [
                'scheme_list' => $scheme_list,
                'designation_id_old' => $designation_id_old,
            ]
        );
    }
    public function listReport(Request $request)
    {
     
      $designation_id_old = Auth::user()->designation_id_old;
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
      //dd($duty_obj->mapping_level);
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if($duty_obj->mapping_level == "Department"){
        $district_code = $request->dist_code;
        //dd($district_code);
        if(!empty($district_code)){
          //dd($district_code);
        $district_arr=District::where('district_code',$district_code)->first();
        $district_name=trim($district_arr->district_name);
        }
        else{
          $district_name='';
        }
      }
      else{
      $district_code = $duty_obj->district_code;
      $district_name='';
      }
      $urban_bodys = collect([]);
      $gps = collect([]);
      $district_list_obj = collect([]);
      $district_list=District::get();
      $download_excel=1;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      if($duty_obj->mapping_level == "Department"){
        $district_list=$district_list;
        $verifier_type = 'Department';
        $created_by_local_body_code = NULL;
        if(!empty($request->created_by_local_body_code)){
          $created_by_local_body_code=$request->created_by_local_body_code;
        }
        $is_rural = NULL;
        $download_excel=0;
      }
      else if ($duty_obj->mapping_level == "Subdiv") {
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
        $district_list_obj = $district_list;
        $verifier_type = 'District';
        $is_rural = NULL;
        $created_by_local_body_code = NULL;
        if(!empty($request->created_by_local_body_code)){
          $created_by_local_body_code=$request->created_by_local_body_code;
        }
      }
      if (request()->ajax()) {
        $limit = $request->input('length');
        $offset = $request->input('start');
        //dd($district_code);
        $query = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code);
          //dd($created_by_local_body_code);
          if ($created_by_local_body_code!='') {
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
        $report_type = $request->report_type;
        //dd($report_type);
        if ($report_type == 1){
          $query = $query->where('next_level_role_id',0)->where('bank_edited',0)->whereIn('lot_generated', array(-1,-2,-3));
        }
        else if ($report_type == 2){
          $query = $query->where('dup_bank', 1);
        }
        else if ($report_type == 3){
          $query = $query->where('dup_aadhar', 1);
        }
        else if ($report_type == 4){
          $query = $query->where('next_level_role_id',-99);
        }
        else if ($report_type == 5){
          $query = $query->where('next_level_role_id', -53);
        }
        else if ($report_type == 6){
          $query = $query->where('next_level_role_id', -200);
        }else if ($report_type == 7){
          $query = $query->where('next_level_role_id', -57);
        }
        $serachvalue = $request->search['value'];
        if (empty($serachvalue)) {
          $totalRecords = $query->count();
         // dd($query->tosql());
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
            'id', 'mobile_no','created_by_dist_code', 'dob', 'bank_ifsc','bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name',  'village_town_city',
            'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
          ]);
          $filterRecords = count($data);
        } else {
          if (is_numeric($serachvalue)) {
           // $ben_id = substr($serachvalue, -7);
            $ben_id = $serachvalue;
            $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
              $query1->where('id', $ben_id)
                ->orWhere('bank_code', $serachvalue);
            });
            $totalRecords = $query->count();
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
              [
                'id','mobile_no', 'created_by_dist_code', 'dob','bank_ifsc', 'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'village_town_city',
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
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
                'id','mobile_no', 'created_by_dist_code', 'dob', 'bank_ifsc','bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'village_town_city',
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
              ]
            );
          }
          $filterRecords = count($data);
        }
        return datatables()->of($data)->setTotalRecords($totalRecords)
          ->setFilteredRecords($filterRecords)
          ->skipPaging()
          ->addColumn('application_id', function ($data) use ($scheme_id, $scheme_length, $id_length) {
  
           // $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);
  
            return $data->id;
          })->addColumn('id', function ($data) {
            return $data->id;
          })->addColumn('name', function ($data) {
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
          })->addColumn('status', function ($data) use($report_type) {
            $status_des = '';
            if($report_type==1){
             if($data->lot_generated==-1){
              $status_des = 'IFMS Failed.. Pending at Verifier Level';
             }
             else if($data->lot_generated==-2){
              $status_des = 'RBI Failed.. Pending at Verifier Level';
             }else if($data->lot_generated==-3){
              $status_des = 'SBI Failed.. Pending at Verifier Level';
             }
            }
            else if($report_type==2){
              $status_des = 'Duplicate Bank.. Pending at Verifier/Approver Level';
             
            }else if($report_type==3){
              $status_des = 'Duplicate Aadhaar.. Pending at Verifier/Approver Level';
             
            }else if($report_type==4){
              $status_des = 'Deactivated';
             
            }else if($report_type==5){
              $status_des = 'Name Validation Rejection from Bank';
             
            }else if($report_type==6){
              $status_des = 'Duplicate Bank Rejection';
             
            }else if($report_type==7){
              $status_des = 'Name Validation Rejection from PDS';
             
            }
            return $status_des;
          })->addColumn('district_name', function ($data) use($district_name) {
            return $district_name;
          })
          ->rawColumns(['view', 'id', 'name', 'mobile_no', 'bank_code','bank_ifsc','bank_ifsc', 'status', 'district_name'])
          ->make(true);
      }
  
      return view(
        'stop-beneficiary.linelisting',
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
          'download_excel' => $download_excel,
          'district_list' => $district_list
        ]
      );
   
    }
    public function generate_excel(Request $request)
    {
      //dd($request);
     
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
        if($duty_obj->mapping_level == "Department"){
          $district_code = $request->dist_code;
        }
        else
        $district_code = $duty_obj->district_code;
       // dd($district_code);
        if (!empty($scheme_obj->short_code)) {
          $schema = $scheme_obj->short_code;
          $scheme_length =  $scheme_obj->scheme_length;
          $id_length = $scheme_obj->id_length;
        } else {
          $schema = "pension";
          $scheme_length = NULL;
          $id_length = NULL;
        }
        $role_name = Auth::user()->designation_id_old;
        $scheme_name_row = Scheme::where('id', $scheme_id)->first();
        $scheme_name = $scheme_name_row->scheme_name;
        $report_type = $request->report_type;
        if ($report_type == 1) {
          $report_type_name = 'Payment Validation Failed';
        } else if ($report_type == 2) {
          $report_type_name = 'Duplicate Bank';
        } else if ($report_type == 3) {
          $report_type_name = 'Duplicate Aadhaar';
        } else if ($report_type == 4) {
          $report_type_name = 'Deactivated';
        } else if ($report_type == 5) {
          $report_type_name = 'Name Validation Rejection from Bank';
        } else if ($report_type == 6) {
          $report_type_name = 'Duplicate Bank Rejection';
        } else if ($report_type == 7) {
          $report_type_name = 'Name Validation Rejection from PDS';
        }  else {
          $report_type_name = 'Payment Validation Failed';
          $report_type = 1;
        }
        $query = DB::table($schema . '.beneficiary')
        ->where('created_by_dist_code', $district_code);
        $created_by_local_body_code = $request->created_by_local_body_code;
        $is_urban = $request->rural_urbanid;
        $urban_body_code = $request->urban_body_code;
        $block_ulb_code = $request->block_ulb_code;
        $gp_ward_code = $request->gp_ward_code;
        if (!empty($request->created_by_local_body_code) && isset($request->created_by_local_body_code) && ($request->created_by_local_body_code!== 'undefined')) {
          //$condition["ds_phase"] = $request->ds_phase;
          $query = $query->where('created_by_local_body_code', $request->created_by_local_body_code);
        }
        if (!empty($urban_body_code) && isset($request->urban_body_code) && ($request->urban_body_code!== 'undefined')) {
          //$condition["rural_urban_id"] = 2;
          $query = $query->where('created_by_local_body_code', $urban_body_code);
        }
        
        if (!empty($is_urban)) {
          // $condition[$contact_table . ".rural_urban_id"] = $is_urban;
          if ($is_urban == 2) {
            if (!empty($urban_body_code) && isset($request->urban_body_code) && ($request->urban_body_code!== 'undefined')) {
              //$condition["rural_urban_id"] = 2;
              $query = $query->where('created_by_local_body_code', $urban_body_code);
            }
          }
          //'Urban'
          if ($is_urban == 1) {
            if (!empty($urban_body_code) && isset($request->urban_body_code) && ($request->urban_body_code!== 'undefined')) {
              //$condition["rural_urban_id"] = 1;
              $query = $query->where('created_by_local_body_code', $urban_body_code);
            }
            if (!empty($block_ulb_code)) {
              $query = $query->where('block_ulb_code', $request->block_ulb_code);
            }
          }
        }
        if (!empty($gp_ward_code) && isset($request->gp_ward_code) && ($request->gp_ward_code!== 'undefined')) {
          $query = $query->where('gp_ward_code', $request->gp_ward_code);
        }
        $report_type = $request->report_type;
        if ($report_type == 1){
          $query = $query->where('next_level_role_id',0)->where('bank_edited',0)->whereIn('lot_generated', array(-1,-2,-3));
        }
        else if ($report_type == 2){
          $query = $query->where('dup_bank', 1);
        }
        else if ($report_type == 3){
          $query = $query->where('dup_aadhar', 1);
        }
        else if ($report_type == 4){
          $query = $query->where('next_level_role_id',-99);
        }
        else if ($report_type == 5){
          $query = $query->where('next_level_role_id', -53);
        }
        else if ($report_type == 6){
          $query = $query->where('next_level_role_id', -200);
        }else if ($report_type == 7){
          $query = $query->where('next_level_role_id', -57);
        }
        $data = $query->orderBy('id', 'ASC')->get([
          'id', 'created_by_dist_code', 'dob', 'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
          'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id','mobile_no','rural_urban_id','ds_phase'
        ]);
       // dd($data);
        $excel_data[] = array(
          'Beneficiary ID', 'Beneficiary Name', 'Mobile No', 'Block/Municipality', 'GP/WARD', 'DS Phase', 'Status'
        );
        $filename = $scheme_name . "-" . $report_type_name . "-" . date('d/m/Y') .  "-" . time() . ".xls";
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Pragma: no-cache");
        header("Expires: 0");
        echo '<table border="1">';
        echo '<tr><td alignment="center" colspan="7">' . $report_type_name . '</td></tr>';
        echo '<tr><th>Beneficiary Id</th><th>Beneficiary Name</th><th>Mobile No.</th><th>Block/Municipality</th><th>GP/WARD</th><th>DS Phase</th><th>Status</th></tr>';
        if (count($data) > 0) {
          foreach ($data as $row) {
  
            $mobile_no = (string) $row->mobile_no;
            $ben_name=$row->ben_fname;
            if (!empty($row->ben_mname)){
              $ben_name=$ben_name.' '.$row->ben_mname;
            }
            if (!empty($row->ben_lname)){
              $ben_name=$ben_name.' '.$row->ben_lname;
            }
            if (!empty($mobile_no))
              $f_mobile_no = "'$mobile_no'";
            else
              $f_mobile_no = '';
            
            if ($row->ds_phase != '') {
              $phase_des = $this->getPhaseDes($row->ds_phase);
            } else {
              $phase_des = '';
            }
            $status_des = '';
            if($report_type==1){
             if($row->lot_generated==-1){
              $status_des = 'IFMS Failed.. Pending at Verifier Level';
             }
             else if($row->lot_generated==-2){
              $status_des = 'RBI Failed.. Pending at Verifier Level';
             }else if($row->lot_generated==-3){
              $status_des = 'SBI Failed.. Pending at Verifier Level';
             }
            }
            else if($report_type==2){
              $status_des = 'Duplicate Bank.. Pending at Verifier/Approver Level';
             
            }else if($report_type==3){
              $status_des = 'Duplicate Aadhaar.. Pending at Verifier/Approver Level';
             
            }else if($report_type==4){
              $status_des = 'Deactivated';
             
            }else if($report_type==5){
              $status_des = 'Name Validation Rejection from Bank';
             
            }else if($report_type==6){
              $status_des = 'Duplicate Bank Rejection';
             
            }else if($report_type==7){
              $status_des = 'Name Validation Rejection from PDS';
             
            }
            echo "<tr><td>" . $row->id . "</td><td>" . trim($ben_name) . "</td><td>" . $f_mobile_no . "</td><td>" . trim($row->block_ulb_name) . "</td><td>" . trim($row->gp_ward_name) . "</td><td>" . $phase_des . "</td><td>" . $status_des . "</td></tr>";
          }
        } else {
          echo '<tr><td colspan="7">No Records found</td></tr>';
        }
        echo '</table>';
      
    }
  function mishod(Request $request)
  {
   
      $role_name = Auth::user()->designation_id_old;
      //dd( $role_name);
      if ($role_name != 'HOD') {
        return redirect('/')->with('error', 'User not Authorized');
      }
      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $list = array();
      $total = array();
      $total['payment_validation'] = 0;
      $total['dup_bank'] = 0;
      $total['dup_aadhaar'] = 0;
      $total['deactivated'] = 0;
      $total['name_validation_rejection_bank'] = 0;
      $total['dup_bank_rejection'] = 0;
      $total['name_validation_rejection_pds'] = 0;
      $query = "select A.location_id,A.location_name,
      COALESCE(C.payment_validation,0) as payment_validation, 
      COALESCE(C.dup_bank,0) as dup_bank,
      COALESCE(C.dup_aadhaar,0) as dup_aadhaar,
      COALESCE(C.deactivated,0) as deactivated,
      COALESCE(C.name_validation_rejection_bank,0) as name_validation_rejection_bank,
      COALESCE(C.dup_bank_rejection,0) as dup_bank_rejection,
      COALESCE(C.name_validation_rejection_pds,0) as name_validation_rejection_pds
      from(
      select district_code as location_id,district_name as location_name
       from public.m_district 
       )
       as A  
      LEFT JOIN
      (select
                  count(1) filter(where next_level_role_id=0 and bank_edited=0 and lot_generated IN (-1,-2,-3) ) as payment_validation,
                  count(1) filter(where dup_bank=1 ) as dup_bank,
                  count(1) filter(where dup_aadhar=1 ) as dup_aadhaar,
                  count(1) filter(where next_level_role_id=-99) as deactivated,
                  count(1) filter(where next_level_role_id=-53) as name_validation_rejection_bank,
                  count(1) filter(where next_level_role_id=-200) as dup_bank_rejection,
                  count(1) filter(where next_level_role_id=-57) as name_validation_rejection_pds,
                  created_by_dist_code
                  from ".$schema . ".beneficiary group by created_by_dist_code) as C ON A.location_id=C.created_by_dist_code";

      // echo $query;die;
      $result = DB::select($query);
      //dd($list);
      return view('stop-beneficiary.mishod')->with('list', $result);
    
  }
    function getPhaseDes($phase_code)
    {
      $phaseArr = DsPhase::where('phase_code', $phase_code)->first();
      //$phaselist = Config::get('constants.ds_phase.phaselist');
      $des = '';
      if (!empty($phaseArr)) {
        $des = $phaseArr->phase_des;
      }
      return $des;
    }

}
