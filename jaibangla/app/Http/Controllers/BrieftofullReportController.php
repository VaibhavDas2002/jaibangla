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

class BrieftofullReportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(60);
  }
  function index(Request $request)
  {
    //return redirect('/')->with('error', 'Not Allowed');
    $designationId = Auth::user()->designation_id_old;
    $userId = Auth::user()->id;
    $sceme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (10,11,2) and id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
    $base_date  = '2021-08-16';
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
    } else if ($designation_id_old == 'Approver') {
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
      'BrieftofullReport.index',
      [
        'sceme_list' => $sceme_list,
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
    $base_date  = '2021-08-16';
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
      'from_date'    => 'nullable|date|before_or_equal:' . $c_date,
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
    $attributes['from_date'] = 'From Date';
    $attributes['to_date'] = 'To Date';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $user_msg = "Short Entry Modification Report";
      $scheme_id = $request->scheme_id;
      $scheme_row = Scheme::where('id', $scheme_id)->first();
      $title = $user_msg;
      $data = array();
      $return_status = 1;
      $return_msg = '';
      $heading_msg = '';
      if (!empty($gp_ward)) {
        if ($urban_code == 1) {
          $column = "Ward";
          $heading_msg =  $user_msg . ' of the Ward ' . $gp_ward_name;
          $data = $this->getWardWise($district, $block, $muncid, $gp_ward, $from_date, $to_date, $scheme_row->short_code);
        } else {
          $column = "GP";
          $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
          $data = $this->getGpWise($district, $block, NULL, $gp_ward, $from_date, $to_date, $scheme_row->short_code);
        }
      } else if (!empty($muncid)) {
        $column = "Ward";
        $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
        $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
        $data = $this->getWardWise($district, $block, $muncid, NULL, $from_date, $to_date, $scheme_row->short_code);
      } else if (!empty($block)) {
        if ($urban_code == 1) {
          $column = "Municipality";
          $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
          $data = $this->getMuncWise($district, $block, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);
        } else if ($urban_code == 2) {
          $block_arr = Taluka::where('block_code', '=', $block)->first();
          $column = "GP";
          $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
          $data = $this->getGpWise($district, $block, NULL, $gp_ward, $from_date, $to_date, $scheme_row->short_code);
        }
      } else {

        if (!empty($district)) {
          if ($urban_code == 1) {
            $column = "Sub Division";
            $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $data = $this->getSubDivWise($district, NULL, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);
          } else if ($urban_code == 2) {
            $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block";
            $data = $this->getBlockWise($district, NULL, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);
          } else {
            $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block/Sub Division";
            $data1 = $this->getBlockWise($district, NULL, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);
            $data2 = $this->getSubDivWise($district, NULL, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);
            $data = array_merge($data1, $data2);
          }
        } else {
          $column = "District";
          $heading_msg = 'District Wise ' . $user_msg;
          $data = $this->getDistrictWise(NULL, NULL, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);

          $external = 0;
        }
      }
      if (!empty($scheme_id)) {
        $heading_msg = $heading_msg . " for the Scheme  " . $scheme_row->scheme_name;
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

  public function getGpWise($district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $scheme)
  {
    //$dateFromat = 'DD/MM/YYYY';
    $dateFromat = 'YYYY/MM/DD';
    $whereCon = "where A.created_by_dist_code=" . $district_code;
    $whereCon .= " and to_char(A.created_at,'" . $dateFromat . "')>='2021-08-16'";
    $whereCon .= " and A.created_by_local_body_code=" . $ulb_code;
    $whereMain = "where  district_code=" . $district_code;
    $whereMain .= " and block_code=" . $ulb_code;

    if (!empty($gp_ward_code)) {
      $whereCon .= " and gp_ward_code=" . $gp_ward_code;
      $whereMain .= " and gram_panchyat_code=" . $gp_ward_code;
    }
    if (!empty($fromdate)) {
      $whereCon .= " and to_char(A.created_at,'" . $dateFromat . "')>='" . $fromdate . "'";
    }
    if (!empty($todate)) {
      $whereCon .= " and to_char(A.created_at,'" . $dateFromat . "')<='" . $todate . "'";
    }
    if (!empty($caste)) {
      $whereCon .= " and A.caste='" . $caste . "'";
    }
    $query = "select main.location_id,main.location_name,
    COALESCE(draft.total_applicant,0) as total_applicant,
    COALESCE(draft.pending_for_action,0) as pending_for_action,
    COALESCE(draft.verified,0) as verified,
    COALESCE(draft.approved,0) as approved,
    COALESCE(draft.rejected,0) as rejected,
    COALESCE(draft.faulty,0) as faulty
      from
      (
      select gram_panchyat_code as location_id,gram_panchyat_name as location_name
      from public.m_gp  " . $whereMain . "
      ) as main LEFT JOIN
      (
        select count(1) filter(where ds_registration_no IS NOT NULL) as total_applicant,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id is null) as pending_for_action,
        count(1) filter(where ds_registration_no IS NOT NULL and is_verified=1 and is_approved=0 and is_rejected=0) as verified,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=0) as approved,
        count(1) filter(where ds_registration_no IS NOT NULL and is_rejected=1) as rejected,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=9999) as faulty,
        gp_ward_code
       from " . $scheme . ".beneficiary as A 
      " . $whereCon . " 
      group by gp_ward_code
      ) as draft ON main.location_id=draft.gp_ward_code order by main.location_name";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getMuncWise($district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $scheme)
  {
    //$dateFromat = 'DD/MM/YYYY';
    $dateFromat = 'YYYY/MM/DD';
    $whereCon = "where A.created_by_dist_code=" . $district_code;
    $whereCon .= " and to_char(A.created_at,'" . $dateFromat . "')>='2021-08-16'";
    $whereCon .= " and A.created_by_local_body_code=" . $ulb_code;
    $whereCon .= " and next_level_role_id=0";
    $whereCon .= " and legacy_import=true";
    $whereMain = "where  district_code=" . $district_code;
    $whereMain .= " and sub_district_code=" . $ulb_code;
    if (!empty($fromdate)) {
      $whereCon .= " and to_char(A.created_at,'" . $dateFromat . "')>='" . $fromdate . "'";
    }
    if (!empty($todate)) {
      $whereCon .= " and to_char(A.created_at,'" . $dateFromat . "')<='" . $todate . "'";
    }
    if (!empty($caste)) {
      $whereCon .= " and A.caste='" . $caste . "'";
    }
    $query = "select main.location_id,main.location_name,
    COALESCE(draft.total_applicant,0) as total_applicant,
    COALESCE(draft.pending_for_action,0) as pending_for_action,
    COALESCE(draft.verified,0) as verified,
    COALESCE(draft.approved,0) as approved,
    COALESCE(draft.rejected,0) as rejected,
    COALESCE(draft.faulty,0) as faulty
      from
      (
      select urban_body_code as location_id,urban_body_name as location_name
      from public.m_urban_body  " . $whereMain . "
      ) as main LEFT JOIN
      (
        select count(1) filter(where ds_registration_no IS NOT NULL) as total_applicant,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id is null) as pending_for_action,
        count(1) filter(where ds_registration_no IS NOT NULL and is_verified=1 and is_approved=0 and is_rejected=0) as verified,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=0) as approved,
        count(1) filter(where ds_registration_no IS NOT NULL and is_rejected=1) as rejected,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=9999) as faulty,
       block_ulb_code
      from " . $scheme . ".beneficiary as A 
      " . $whereCon . " 
      group by block_ulb_code
      ) as draft ON main.location_id=draft.block_ulb_code  order by main.location_name";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getBlockWise($district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $scheme)
  {
    // $dateFromat = 'DD/MM/YYYY';
    $dateFromat = 'YYYY/MM/DD';
    $whereCon = "where A.created_by_dist_code=" . $district_code;
    $whereMain = "where  district_code=" . $district_code;
    $whereCon .= " and next_level_role_id=0";
    //$whereCon .= " and legacy_import=true";


    $query = "select main.location_id,main.location_name||'-Block' as location_name,
    COALESCE(draft.total_applicant,0) as total_applicant,
    COALESCE(draft.total_brief,0) as total_brief,
    COALESCE(draft.pending_for_action,0) as pending_for_action,
    COALESCE(draft.verification_pending,0) as verification_pending,
    COALESCE(draft.approval_pending,0) as approval_pending,
    COALESCE(draft.approved,0) as approved
      from
      (
      select block_code as location_id,block_name as location_name
      from public.m_block  " . $whereMain . "
      ) as main LEFT JOIN
      (
        select count(1) as total_applicant,
        count(1) filter(where legacy_import=TRUE) as total_brief,
        count(1) filter(where legacy_import=TRUE and next_level_role_id_edit IS NULL) as pending_for_action,
        count(1) filter(where legacy_import=TRUE and next_level_role_id_edit=999 and unlock_status=1) as verification_pending,
        count(1) filter(where legacy_import=TRUE and next_level_role_id_edit>0 and unlock_status=1 and next_level_role_id_edit!=999) as approval_pending,
        count(1) filter(where legacy_import=TRUE and next_level_role_id_edit=0 and unlock_status IS NULL and caste IS NOT NULL) as approved,
       created_by_local_body_code
      from " . $scheme . ".beneficiary as A 
        " . $whereCon . "  group by A.created_by_local_body_code
      ) as draft ON main.location_id=draft.created_by_local_body_code
       order by main.location_name";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getSubDivWise($district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $scheme)
  {
    //$dateFromat = 'DD/MM/YYYY';
    $dateFromat = 'YYYY/MM/DD';

    $whereCon = "where A.created_by_dist_code=" . $district_code;
    $whereMain = "where  district_code=" . $district_code;
    $whereCon .= " and next_level_role_id=0";
    //$whereCon .= " and legacy_import=true";
    $query = "select main.location_id,main.location_name||'-SubDivision' as location_name,
    COALESCE(draft.total_applicant,0) as total_applicant,
    COALESCE(draft.total_brief,0) as total_brief,
    COALESCE(draft.pending_for_action,0) as pending_for_action,
    COALESCE(draft.verification_pending,0) as verification_pending,
    COALESCE(draft.approval_pending,0) as approval_pending,
    COALESCE(draft.approved,0) as approved
    from
    (
    select sub_district_code as location_id,sub_district_name as location_name
    from public.m_sub_district  " . $whereMain . " 
    ) as main LEFT JOIN
    (
      select count(1) as total_applicant,
      count(1) filter(where legacy_import=TRUE) as total_brief,
      count(1) filter(where legacy_import=TRUE and next_level_role_id_edit IS NULL) as pending_for_action,
      count(1) filter(where legacy_import=TRUE and next_level_role_id_edit=999 and unlock_status=1) as verification_pending,
      count(1) filter(where legacy_import=TRUE and next_level_role_id_edit>0 and unlock_status=1 and next_level_role_id_edit!=999) as approval_pending,
      count(1) filter(where legacy_import=TRUE and next_level_role_id_edit=0 and unlock_status IS NULL and caste IS NOT NULL) as approved,
      created_by_local_body_code
      from " . $scheme . ".beneficiary as A 
        " . $whereCon . "  group by A.created_by_local_body_code
    ) as draft ON main.location_id=draft.created_by_local_body_code
     order by main.location_name";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getDistrictWise($district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $scheme)
  {
    $whereCon = "where 1=1";
    $whereCon .= " and next_level_role_id=0";
    // $whereCon .= " and legacy_import=true";
    $query = "select main.location_id,main.location_name,
    COALESCE(draft.total_applicant,0) as total_applicant,
    COALESCE(draft.total_brief,0) as total_brief,
    COALESCE(draft.pending_for_action,0) as pending_for_action,
    COALESCE(draft.verification_pending,0) as verification_pending,
    COALESCE(draft.approval_pending,0) as approval_pending,
    COALESCE(draft.approved,0) as approved
      from
      (
      select district_code as location_id,district_name as location_name
      from public.m_district  
      ) as main LEFT JOIN
      (
        select count(1) as total_applicant,
        count(1) filter(where legacy_import=TRUE) as total_brief,
        count(1) filter(where legacy_import=TRUE and next_level_role_id_edit IS NULL) as pending_for_action,
        count(1) filter(where legacy_import=TRUE and next_level_role_id_edit=999 and unlock_status=1) as verification_pending,
        count(1) filter(where legacy_import=TRUE and next_level_role_id_edit>0 and unlock_status=1 and next_level_role_id_edit!=999) as approval_pending,
        count(1) filter(where legacy_import=TRUE and next_level_role_id_edit=0 and unlock_status IS NULL and caste IS NOT NULL) as approved,
        created_by_dist_code
      from " . $scheme . ".beneficiary as A  " . $whereCon . "
      group by A.created_by_dist_code
      ) as draft ON main.location_id=draft.created_by_dist_code order by main.location_name";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
}
