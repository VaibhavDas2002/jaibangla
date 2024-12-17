<?php

namespace App\Http\Controllers;

use App\Configduty;
use App\District;
use App\GP;
use App\Taluka;
use App\Scheme;
use App\UrbanBody;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\SubDistrict;
use Validator;
use App\Ward;
use App\DsPhase;

class DuareSarkarApplicationphaseController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(60);
    $this->ds_base_date_3 = '2022-02-15';
    $this->selected_phase = 3;
  }

 

  function dsReportphaseCommon(Request $request)
  {
   //dd(123);
    //return redirect('/')->with('error', 'Not Allowed');
    $designationId = Auth::user()->designation_id_old;
    $userId = Auth::user()->id;
    // $phase_list = array('2' => 'Phase II', '3' => 'Phase III');
    // $phase_list = DsPhase::where('phase_code', '>=', 2)->get();
    $phase_list = DsPhase::whereIn('phase_code', [8,9,10])->get();
    // dd($phase_list->toArray());
    return view(
      'ds-report.dsReportCommon',
      [
        'phase_list' => $phase_list
      ]
    );
  }

  public function shemeSessionCheck(Request $request)
  {
    $scheme_id = 0;

    if ($request->get('pr1')) {
      if ($request->get('pr1') == "lb_wcd") {
        $scheme_id = 20;
      } else {
        return redirect("/")->with('error', ' Parameter Invalid');
      }
    } else {
      return redirect("/")->with('error', 'Method is not valid');
    }

    $is_active = 0;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $request->session()->put('level', $roleObj['mapping_level']);
        $distCode = $roleObj['district_code'];
        $request->session()->put('distCode', $roleObj['district_code']);
        $request->session()->put('scheme_id', $scheme_id);
        $request->session()->put('is_first', $roleObj['is_first']);
        $request->session()->put('is_urban', $roleObj['is_urban']);
        $request->session()->put('role_id', $roleObj['id']);
        if ($roleObj['is_urban'] == 1) {
          $request->session()->put('bodyCode', $roleObj['urban_body_code']);
        } else {
          $request->session()->put('bodyCode', $roleObj['taluka_code']);
        }
        break;
      }
    }
    if ($is_active == 1) {

      //  $ben_table = 'dist_' . $distCode . '.beneficiaries';
      return true;
    } else {
      return false;
    }
  }
  function dsReport(Request $request)
  {



    $phase_code = $request->phase_code;
    //dd($phase_code);
    
    if (empty($phase_code)) {
      return redirect("dsreportphaseselect")->with('error', 'Phase Code Not Found');
    }
    if (!ctype_digit($phase_code)) {
      return redirect("dsreportphaseselect")->with('error', 'Phase Code  InValid');
    }
    $phase_arr = DsPhase::where('phase_code', $phase_code)->first();
    if (empty($phase_arr)) {
      return redirect("dsreportphaseselect")->with('error', 'Phase Code  InValid');
    }

 //$phase_arr = DsPhase::get();

// dd($phase_arr);
    //   foreach($phase_arr as $list)
    //   {
    //   $base_date_loop=$list->base_date;
    //   }
    // $phase_list = DsPhase::get();   
    // if (empty($phase_arr)) {
    //   return redirect("dsreportphase")->with('error', 'Phase Code  InValid');
    // }
  
    
    $designationId = Auth::user()->designation_id_old;
    $userId = Auth::user()->id;
    $sceme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (1,2,3,11,10) and id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
    

    // @foreach()
    //$base_date  = '2020-01-01';
    
    $base_date = $phase_arr->base_date;
    
    date_default_timezone_set('Asia/Kolkata');
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
    if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' ||  $designation_id_old == 'Dashboard') {
      $district_visible = $is_urban_visible = $block_visible = 1;
    } else if ($designation_id_old == 'Approver' || $designation_id_old == 'StatusCheckerDistrict' || $designation_id_old=='Verifier') {
      $district_code = NULL;
      $is_urban = NULL;
      $blockCode = NULL;
      foreach ($roleArray as $roleObj) {
        if (in_array($roleObj['scheme_id'], array(1, 2, 3))) {
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

    return view(
      'ds-report.index',
      [
        'sceme_list' => $sceme_list,
       'selected_phase' =>  $phase_code,
        'districts' => $districts,
        'district_visible' => $district_visible,
        'district_code_fk' => $district_code_fk,
        'is_urban_visible' => $is_urban_visible,
        'rural_urban_fk' => $rural_urban_fk,
        'block_visible' => $block_visible,
        'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
        'municipality_visible' => $municipality_visible,
        //'gp_ward_visible' => $gp_ward_visible,
        'is_urban_visible' => $is_urban_visible,
        'base_date' => $base_date,
        'c_date' => $c_date,
        //'gpList' => $gpList,
       'muncList' => $muncList,
        //'phase_list' => $phase_list,
        'phase_arr' => $phase_arr,
      ]
    );
  }


  public function dsgetData(Request $request)
  {
//dd($request->all());
    $scheme_id = $request->scheme_id;
    $ds_phase = $request->ds_phase;
    if (empty($ds_phase)) {
      $ds_phase = $this->selected_phase;
    }
   
    $phase_arr = DsPhase::where('phase_code', $ds_phase)->first();
    $district = $request->district;
    $urban_code = $request->urban_code;
    $block = $request->block;
    $muncid=$request->muncid;
    //$from_date = $request->from_date;
    $to_date = $request->to_date;
    //$base_date  = '2020-01-01';
    $base_date = $phase_arr->base_date;
    $ds_phase_name=$phase_arr->phase_des;
   // dd($base_date);
    
    date_default_timezone_set('Asia/Kolkata');
    $c_time = Carbon::now();
    
    $c_date = $c_time->format("Y-m-d");
    
    $heading_msg = '';
    $title = "";
    $mu_msg = "";

            if (!empty($district)) {
            $district_row = District::where('district_code', $district)->first();
            }

          if (!empty($block)) {
            // dd($block);
          if ($urban_code == 1) {
          //dd($block);
          // $muncList = UrbanBody::where('sub_district_code', $block)->get();
          $block_ulb = UrbanBody::where('sub_district_code', $block)->first();
          // print_r($block_ulb);
          //$blk_munc_name = $block_ulb->urban_body_name;
          //$blk_munc_code = $block_ulb->urban_body_code;
           //dd($blk_munc_code);
          //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
          } else {
          $block_ulb = Taluka::where('block_code', '=', $block)->first();
          $blk_munc_name = $block_ulb->block_name;
          // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
          }
          } else {
          $blk_munc_name = "";
          }

    $rules = [
      'scheme_id' => 'required|integer',
      'ds_phase' => 'required|integer',
      'district' => 'nullable|integer',
      //'from_date'    => 'required|date|after_or_equal:' . $base_date . '|before_or_equal:' . $c_date,
      'to_date'    => 'required|date|after_or_equal:' . $base_date . '|before_or_equal:' . $c_date,
      
      //'to_date'      => 'required|date|after_or_equal:from_date|before_or_equal:' . $c_date,
      //'to_date'      => 'required|date|after_or_equal:from_date|before_or_equal:' . $c_date,
    ];
   

    $data = array();
    $column = "";
    $attributes = array();
    $messages = array();
    $attributes['scheme_id'] = 'Scheme';
    $attributes['ds_phase'] = 'Phase';
    $attributes['district'] = 'District';
    $attributes['urban_code'] = 'Rural/ Urban';
    $attributes['block'] = 'Block/Municipality';
    //$attributes['muncid'] = 'Municipality';
    //$attributes['gp_ward'] = 'GP/Ward';
   // $attributes['from_date'] = 'From Date';
    $attributes['to_date'] = 'To Date';
    
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);

    if ($validator->passes()) {
      //dd(99);
      $user_msg = "Duare Sarkar Report of  " . $ds_phase_name;
      $scheme_id = $request->scheme_id;
      $scheme_row = Scheme::where('id', $scheme_id)->first();
      $title = $user_msg;
      $data = array();
      $return_status = 1;
      $return_msg = '';
      $heading_msg = '';
      $mu_msg= '';


      if (!empty($district)) {

      //dd($urban_code);
            if ($urban_code == 1) {
//dd(333);
            $mu_msg='Note: Municipality Report Base on Applicant Address';
            $column = "Municipality";
            $heading_msg = 'Municipality Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $data = $this->getMuncWise($ds_phase, $district, $block,$muncid, NULL, NULL, $to_date, $scheme_row->short_code,$base_date);
            // 
            } 
            else if ($urban_code == 2) {
             // dd(777);
            $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block";
            $data = $this->getBlockWise($ds_phase, $district, $block, NULL, NULL, $to_date, $scheme_row->short_code,$base_date);
            } else {
            //dd(333);
            // $column = "District";
            // $heading_msg = 'District Wise ' . $user_msg;
            // $data = $this->getDistrictWise($phase_code, $district, NULL, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);

            
            
            $mu_msg='Note: Municipality Report Base on Applicant Address';
            $heading_msg = 'Block and Municipality Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block and Municipality";
            $data1 = $this->getBlockWise($ds_phase, $district, $block, NULL, NULL, $to_date, $scheme_row->short_code,$base_date);
            $data2 = $this->getMuncWise($ds_phase, $district, $block,$muncid, NULL, NULL, $to_date, $scheme_row->short_code,$base_date);
            $data = array_merge($data1, $data2);

           
            // 
            }


            if (!empty($scheme_id)) {
              $heading_msg = $heading_msg . " for the Scheme  " . $scheme_row->scheme_name;
              }
              // if (!empty($from_date)) {
              // $form_date_formatted = \Carbon\Carbon::parse($from_date)->format('d-m-Y');
              // $heading_msg = $heading_msg . " from " . $form_date_formatted;
              // }
              if (!empty($to_date)) {
              $to_date_formatted = \Carbon\Carbon::parse($to_date)->format('d-m-Y');
              $heading_msg = $heading_msg . " till Date " . $to_date_formatted;
              }
      }
      else{

       // dd(123);
        //$mu_msg='Debjit';
        $column = "District";
          $heading_msg = 'District Wise ' . $user_msg;
          $data = $this->getDistrictWise($ds_phase, NULL, NULL, NULL, NULL, $to_date, $scheme_row->short_code,$base_date);

          if (!empty($scheme_id)) {
            $heading_msg = $heading_msg . " for the Scheme  " . $scheme_row->scheme_name;
            }
            // if (!empty($from_date)) {
            // $form_date_formatted = \Carbon\Carbon::parse($from_date)->format('d-m-Y');
            // $heading_msg = $heading_msg . " from " . $form_date_formatted;
            // }
            if (!empty($to_date)) {
            $to_date_formatted = \Carbon\Carbon::parse($to_date)->format('d-m-Y');
            $heading_msg = $heading_msg . " till Date  " . $to_date_formatted;
            }

      }  

    }
    else{
      $return_status = 0;
       $return_msg = $validator->errors()->all();
    }



      // if (!empty($scheme_id)) {
      //   $heading_msg = $heading_msg . " for the Scheme  " . $scheme_row->scheme_name;
      //   }
      //   if (!empty($from_date)) {
      //   $form_date_formatted = \Carbon\Carbon::parse($from_date)->format('d-m-Y');
      //   $heading_msg = $heading_msg . " from " . $form_date_formatted;
      //   }
      //   if (!empty($to_date)) {
      //   $to_date_formatted = \Carbon\Carbon::parse($to_date)->format('d-m-Y');
      //   $heading_msg = $heading_msg . " to  " . $to_date_formatted;
      //   }
      // } else {
      //   $return_status = 0;
      //   $return_msg = $validator->errors()->all();
      // }

      return response()->json([
        'return_status' => $return_status,
        'return_msg' => $return_msg,
        'row_data' => $data,
        'column' => $column,
        'title' => $title,
        'heading_msg' => $heading_msg,
        'mu_msg' => $mu_msg
      ]);

   
    
  }


 
  
  

  
  public function getMuncWise($ds_phase = NULL, $district_code = NULL, $ulb_code = NULL,$muncid= NULL, $block_ulb_code = NULL, $gp_ward_code = NULL,  $todate = NULL, $scheme,$base_date)
  {
    //dd(111);
  //dd($ulb_code);
    //$phase_arr = DsPhase::where('phase_code', $ds_phase)->first();
    //$base_date  = $phase_arr->base_date;
    if($scheme=='oap_wcd' && ($ds_phase==8 || $ds_phase==9 || $ds_phase==10)){
      if($ds_phase==8){
        $whereCon = "where (ds_phase=" . $ds_phase . " or (sm_ds_mark_vii=1 and sm_ds_mark=1)) and A.created_by_dist_code=" . $district_code;

      }
      if($ds_phase==9){
      $whereCon = "where (ds_phase=" . $ds_phase . " or (sm_ds_mark_viii=1 and sm_ds_mark=1)) and A.created_by_dist_code=" . $district_code;
      }
      if($ds_phase==10){
        $whereCon = "where (ds_phase=" . $ds_phase . " or (sm_ds_mark_ix=1 and sm_ds_mark=1)) and A.created_by_dist_code=" . $district_code;
       }
    }
    else
    $whereCon = "where ds_phase=" . $ds_phase . " and A.created_by_dist_code=" . $district_code;
    if($scheme=='oap_wcd' && ($ds_phase==8 || $ds_phase==9 || $ds_phase==10)){
    }
    else{
      $whereCon .= " and date(A.created_at)>='" . $base_date . "'";
    }
    //$whereCon .= " and A.block_ulb_code=" . $ulb_code;
    $whereMain = "where  district_code=" . $district_code;
    // $whereMain .= " and urban_body_code=" . $ulb_code;
    // if (!empty($fromdate)) {
    //   $whereCon .= " and date(A.created_at)>='" . $fromdate . "'";
    // }
    if (!empty($todate)) {
      if($scheme=='oap_wcd' && ($ds_phase==8 || $ds_phase==9)){
      }
      else{
        $whereCon .= " and date(A.created_at)<='" . $todate . "'"; 
      }
     
    }
    
    if(!empty($ulb_code))
    {
      
      // $whereCo_sub = " and  urban_body_code=" . $muncid;
      $whereCo_sub = " and  urban_body_code=" . $ulb_code;
      if(Auth::user()->designation_id_old == 'Verifier') {
        if (!empty($ulb_code) && empty($muncid)) {
          $whereCo_sub = " and  sub_district_code=" . $ulb_code;
        } 
        if(!empty($ulb_code) && !empty($muncid)) {
          $whereCo_sub = " and  urban_body_code=" . $muncid;
        }
      }
    }
    else {
      $whereCo_sub="";
    }
    if($scheme=='oap_wcd' && ($ds_phase==8 || $ds_phase==9 || $ds_phase==10)){
      $query = "select main.location_id,main.location_name||'-Municipality' as location_name ,
      COALESCE(draft.total_applicant,0) as total_applicant,
      COALESCE(draft.application_process,0) as application_process,
      COALESCE(draft.verified,0) as verified,
      COALESCE(draft.approved,0) as approved,
      COALESCE(draft.rejected,0) as rejected
        from
        (
        select urban_body_code as location_id,urban_body_name as location_name
        from public.m_urban_body  " . $whereMain . " ".$whereCo_sub."
        ) as main LEFT JOIN
        (
          select count(1)  as total_applicant,
          count(1) filter(where  ( (is_rejected=0 or is_rejected IS NULL) and  next_level_role_id is NULL)) as application_process,
          count(1) filter(where  ( (is_rejected=0 or is_rejected IS NULL) and  is_verified=1 and next_level_role_id IS NOT NULL
          and (is_approved=0 or is_approved IS NULL))) as verified,
          count(1) filter(where next_level_role_id=0) as approved,
          count(1) filter(where is_rejected=1 and next_level_role_id<0) as rejected,
         block_ulb_code
        from " . $scheme . ".beneficiaries as A 
        " . $whereCon . " 
        group by block_ulb_code
        ) as draft ON main.location_id=draft.block_ulb_code  order by main.location_name";
    }
    else{
    $query = "select main.location_id,main.location_name||'-Municipality' as location_name ,
    COALESCE(draft.total_applicant,0) as total_applicant,
    COALESCE(draft.application_process,0) as application_process,
    COALESCE(draft.verified,0) as verified,
    COALESCE(draft.approved,0) as approved,
    COALESCE(draft.rejected,0) as rejected
      from
      (
      select urban_body_code as location_id,urban_body_name as location_name
      from public.m_urban_body  " . $whereMain . " ".$whereCo_sub."
      ) as main LEFT JOIN
      (
        select count(1) filter(where ds_registration_no IS NOT NULL) as total_applicant,
        count(1) filter(where  ds_registration_no IS NOT NULL and 
        ( (is_rejected=0 or is_rejected IS NULL) and  next_level_role_id is NULL)) as application_process,
        count(1) filter(where ds_registration_no IS NOT NULL and  ( (is_rejected=0 or is_rejected IS NULL) and  is_verified=1 and next_level_role_id IS NOT NULL
        and (is_approved=0 or is_approved IS NULL))) as verified,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=0) as approved,
        count(1) filter(where ds_registration_no IS NOT NULL and is_rejected=1 and next_level_role_id<0) as rejected,
       block_ulb_code
      from " . $scheme . ".beneficiaries as A 
      " . $whereCon . " 
      group by block_ulb_code
      ) as draft ON main.location_id=draft.block_ulb_code  order by main.location_name";
    }

    //  print $query; die;
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getBlockWise($ds_phase = NULL, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $todate = NULL, $scheme,$base_date)
  {
   // dd($ulb_code);
    //$phase_arr = DsPhase::where('phase_code', $ds_phase)->first();
    //$base_date  = $phase_arr->base_date;
    if($scheme=='oap_wcd' && ($ds_phase==8 || $ds_phase==9 || $ds_phase==10)){
      if($ds_phase==8){
      $whereCon = "where (ds_phase=" . $ds_phase . " or (sm_ds_mark_vii=1 and sm_ds_mark=1)) and A.created_by_dist_code=" . $district_code;
      }
      if($ds_phase==9){
        $whereCon = "where (ds_phase=" . $ds_phase . " or (sm_ds_mark_viii=1 and sm_ds_mark=1)) and A.created_by_dist_code=" . $district_code;
      }
      if($ds_phase==10){
        $whereCon = "where (ds_phase=" . $ds_phase . " or (sm_ds_mark_ix=1 and sm_ds_mark=1)) and A.created_by_dist_code=" . $district_code;
      }

    }
    else{
      $whereCon = "where ds_phase=" . $ds_phase . " and A.created_by_dist_code=" . $district_code;

    }
    if($scheme=='oap_wcd' && ($ds_phase==8 || $ds_phase==9 || $ds_phase==10)){
    }
    else{
    $whereCon .= " and date(A.created_at)>='" . $base_date . "'";
    }
    $whereMain = "where  district_code=" . $district_code;


    if($ulb_code!='')
    {
    $whereCo_sub = " and  block_code='" . $ulb_code . "'";
    }
    else{
      $whereCo_sub="";
    }

    // if (!empty($fromdate)) {
    //   $whereCon .= " and date(A.created_at)>='" . $fromdate . "'";
    // }
    if (!empty($todate)) {
      if($scheme=='oap_wcd' && ($ds_phase==8 || $ds_phase==9 || $ds_phase==10)){
      }
      else{
      $whereCon .= " and date(A.created_at)<='" . $todate . "'";
      }
    }
    if($scheme=='oap_wcd' && ($ds_phase==8 || $ds_phase==9 || $ds_phase==10)){
      $query = "select main.location_id,main.location_name||'-Block' as location_name,
      COALESCE(draft.total_applicant,0) as total_applicant,
      COALESCE(draft.application_process,0) as application_process,
      COALESCE(draft.verified,0) as verified,
      COALESCE(draft.approved,0) as approved,
      COALESCE(draft.rejected,0) as rejected
        from
        (
        select block_code as location_id,block_name as location_name
        from public.m_block  " . $whereMain . " ".$whereCo_sub."
        ) as main LEFT JOIN
        (
          select count(1)  as total_applicant,
          count(1) filter(where  ( (is_rejected=0 or is_rejected IS NULL) and  next_level_role_id is NULL)) as application_process,
          count(1) filter(where  ( (is_rejected=0 or is_rejected IS NULL) and  is_verified=1 and next_level_role_id IS NOT NULL
          and (is_approved=0 or is_approved IS NULL))) as verified,
          count(1) filter(where  next_level_role_id=0) as approved,
          count(1) filter(where is_rejected=1 and next_level_role_id<0) as rejected,
        created_by_local_body_code
        from " . $scheme . ".beneficiaries as A 
          " . $whereCon . "  group by A.created_by_local_body_code
        ) as draft ON main.location_id=draft.created_by_local_body_code
         order by main.location_name";
         //dd($query);
    }
    else{
      $query = "select main.location_id,main.location_name||'-Block' as location_name,
      COALESCE(draft.total_applicant,0) as total_applicant,
      COALESCE(draft.application_process,0) as application_process,
      COALESCE(draft.verified,0) as verified,
      COALESCE(draft.approved,0) as approved,
      COALESCE(draft.rejected,0) as rejected
        from
        (
        select block_code as location_id,block_name as location_name
        from public.m_block  " . $whereMain . " ".$whereCo_sub."
        ) as main LEFT JOIN
        (
          select count(1) filter(where ds_registration_no IS NOT NULL) as total_applicant,
          count(1) filter(where  ds_registration_no IS NOT NULL and 
          ( (is_rejected=0 or is_rejected IS NULL) and  next_level_role_id is NULL)) as application_process,
          count(1) filter(where ds_registration_no IS NOT NULL and  ( (is_rejected=0 or is_rejected IS NULL) and  is_verified=1 and next_level_role_id IS NOT NULL
          and (is_approved=0 or is_approved IS NULL))) as verified,
          count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=0) as approved,
          count(1) filter(where ds_registration_no IS NOT NULL and is_rejected=1 and next_level_role_id<0) as rejected,
        created_by_local_body_code
        from " . $scheme . ".beneficiaries as A 
          " . $whereCon . "  group by A.created_by_local_body_code
        ) as draft ON main.location_id=draft.created_by_local_body_code
         order by main.location_name";
    }
  
    $result = DB::connection('pgsql_mis')->select($query);

    //dd($query);
    return $result;
  }
  
  public function getDistrictWise($phase_code , $district_code, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $todate = NULL, $scheme,$base_date)
  {
    //dd($phase_code);
    //$phase_arr = DsPhase::where('phase_code', $phase_code)->first();
    //$base_date  = '2020-01-01';
    //$base_date  = $phase_arr->base_date;
    if($scheme=='oap_wcd' && ($phase_code==8 || $phase_code==9 || $phase_code==10)){
      if($phase_code==8){
      $whereCon = "where (ds_phase=" . $phase_code." or (sm_ds_mark_vii=1 and sm_ds_mark=1)) ";
      }
      if($phase_code==9){
        $whereCon = "where (ds_phase=" . $phase_code." or (sm_ds_mark_viii=1 and sm_ds_mark=1)) ";

      }
      if($phase_code==10){
        $whereCon = "where (ds_phase=" . $phase_code." or (sm_ds_mark_ix=1 and sm_ds_mark=1)) ";

      }

    }
    else{
    $whereCon = "where ds_phase=" . $phase_code;
    }
    if($scheme=='oap_wcd' && ($phase_code==8 || $phase_code==9 || $phase_code==10)){
    }
    else{
    $whereCon .= " and date(A.created_at)>='" . $base_date . "'";
    }

    if($district_code!='')
    {
    $whereCo_sub = " where  district_code='" . $district_code . "'";
    }
    else{
      $whereCo_sub="";
    }
    
    
    // if (!empty($fromdate)) {
    //   $whereCon .= " and date(A.created_at)>='" . $fromdate . "'";
    // }
    if (!empty($todate)) {
      if($scheme=='oap_wcd' && ($phase_code==8 || $phase_code==9 || $phase_code==10)){
      }
      else{
      $whereCon .= " and date(A.created_at)<='" . $todate . "'";
      }
    }
    if($scheme=='oap_wcd' && ($phase_code==8 || $phase_code==9 || $phase_code==10)){
      
      $query = "select main.location_id,main.location_name,
      COALESCE(draft.total_applicant,0) as total_applicant,
      COALESCE(draft.application_process,0) as application_process,
      COALESCE(draft.verified,0) as verified,
      COALESCE(draft.approved,0) as approved,
      COALESCE(draft.rejected,0) as rejected
        from
        (
        select district_code as location_id,district_name as location_name,district_order_by as district_order_by
        from public.m_district  ".$whereCo_sub."
        ) as main LEFT JOIN
        (
          select count(1) as total_applicant,
          count(1) filter(where  ( (is_rejected=0 or is_rejected IS NULL) and  next_level_role_id is NULL)) as application_process,
          count(1) filter(where  ( (is_rejected=0 or is_rejected IS NULL) and  is_verified=1 and next_level_role_id IS NOT NULL
          and (is_approved=0 or is_approved IS NULL))) as verified,
          count(1) filter(where  next_level_role_id=0) as approved,
          count(1) filter(where  is_rejected=1 and next_level_role_id<0) as rejected,
          created_by_dist_code
        from " . $scheme . ".beneficiaries as A  " . $whereCon . "
        group by A.created_by_dist_code
        ) as draft ON main.location_id=draft.created_by_dist_code order by main.district_order_by";

       
    }
    else{
      $query = "select main.location_id,main.location_name,
      COALESCE(draft.total_applicant,0) as total_applicant,
      COALESCE(draft.application_process,0) as application_process,
      COALESCE(draft.verified,0) as verified,
      COALESCE(draft.approved,0) as approved,
      COALESCE(draft.rejected,0) as rejected
        from
        (
        select district_code as location_id,district_name as location_name,district_order_by as district_order_by
        from public.m_district  ".$whereCo_sub."
        ) as main LEFT JOIN
        (
          select count(1) filter(where ds_registration_no IS NOT NULL) as total_applicant,
          count(1) filter(where  ds_registration_no IS NOT NULL and 
          ( (is_rejected=0 or is_rejected IS NULL) and  next_level_role_id is NULL)) as application_process,
          count(1) filter(where ds_registration_no IS NOT NULL and  ( (is_rejected=0 or is_rejected IS NULL) and  is_verified=1 and next_level_role_id IS NOT NULL
          and (is_approved=0 or is_approved IS NULL))) as verified,
          count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=0) as approved,
          count(1) filter(where ds_registration_no IS NOT NULL and is_rejected=1 and next_level_role_id<0) as rejected,
          created_by_dist_code
        from " . $scheme . ".beneficiaries as A  " . $whereCon . "
        group by A.created_by_dist_code
        ) as draft ON main.location_id=draft.created_by_dist_code order by main.district_order_by";
    }
   
     //dd($query);
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  // function dsReportCommon(Request $request)
  // {
  //   //return redirect('/')->with('error', 'Not Allowed');
  //   $designationId = Auth::user()->designation_id_old;
  //   $userId = Auth::user()->id;
  //   // $phase_list = array('2' => 'Phase II', '3' => 'Phase III');
  //   $phase_list = DsPhase::where('phase_code', 3)->get();
  //   //dd($phase_list->toArray());
  //   return view(
  //     'DsReport.dsReportCommon',
  //     [
  //       'phase_list' => $phase_list
  //     ]
  //   );
  // }
  function dsReportCommonPost(Request $request)
  {
    $phase_code = $request->phase_code;
    if (empty($phase_code)) {
      return redirect("/dsReportCommon")->with('error', 'Phase Code Not Found');
    }
    if (!ctype_digit($phase_code)) {
      return redirect("/dsReportCommon")->with('error', 'Phase Code  InValid');
    }
    $phase_arr = DsPhase::where('phase_code', $phase_code)->first();
    if (empty($phase_arr)) {
      return redirect("/dsReportCommon")->with('error', 'Phase Code  InValid');
    }
    if ($phase_code == 2) {
      return redirect("/getDistrictApplicationReport");
    } else {
      return redirect("/dsReport?phase_code=".$phase_code);
    }
  }
}
