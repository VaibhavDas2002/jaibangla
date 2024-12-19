<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\District;
use Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use DateTime;
use App\Scheme;
use Config;
use Carbon\Carbon;
use App\Configduty;
use App\Helpers\AuthChecker;

class MonthlyPaymentStatus extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }


  function index(Request $request)
  {

    $roleArray = $request->session()->get('role');
    $designation_id_old = Auth::user()->designation_id_old;
    $district_visible = $is_urban_visible = $block_visible = $scheme_visible = 1;
    $scheme_arr = array();
    $user_id = AuthChecker::getUserId();
    $payment_mode = $request->payment_mode;
    if ($payment_mode != 'IFMS' && $payment_mode != 'SBI') {
      return redirect('/')->with('error', 'Payment Mode Not Valid');
    }
    if ($payment_mode == 'IFMS')
      $payment_scheme_in = array(1, 5, 6, 7, 19);
    elseif ($payment_mode == 'SBI')
      $payment_scheme_in = array(2, 3, 8, 9, 10, 11, 17);
    if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'DDO' || $designation_id_old == 'Corp') {
      $district_visible = $is_urban_visible = $block_visible = 1;
      $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in ( " . implode(',', $payment_scheme_in) . ") and id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
    } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier' || $designation_id_old == 'Operator') {
      $district_code = NULL;
      $is_urban = NULL;
      $blockCode = NULL;
      $district_visible = 0;

      foreach ($roleArray as $roleObj) {
        if ($roleObj['scheme_id'] == 10) {
          array_push($scheme_arr, 10);
          $is_urban = $roleObj['is_urban'];
          $district_code = $roleObj['district_code'];
          if ($roleObj['is_urban'] == 1) {
            $blockCode = $roleObj['urban_body_code'];
          } else {
            $blockCode = $roleObj['taluka_code'];
          }
          break;
        }
      }
      if ($designation_id_old == 'Verifier' || $designation_id_old == 'Operator') {
        $scheme_visible = 0;
        $is_urban_visible = 0;
        $schemes = array();
      } else {
        $scheme_visible = 1;
        $schemes = Scheme::where('is_active', 1)->whereIn('id', $scheme_arr)->whereIn('scheme_id', $payment_scheme_in)->get(['scheme_name', 'id']);
      }
    }
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
    } else {
      $block_munc_corp_code_fk = NULL;
    }
    $districts = District::get();
    $block_visible = 0;
    // dd($payment_mode);
    return view(
      'MonthlyPaymentStatus.index',
      [
        'schemes' => $schemes,
        'scheme_visible' => $scheme_visible,
        'districts' => $districts,
        'district_visible' => $district_visible,
        'district_code_fk' => $district_code_fk,
        'is_urban_visible' => $is_urban_visible,
        'rural_urban_fk' => $rural_urban_fk,
        'block_visible' => $block_visible,
        'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
        'payment_mode' => $payment_mode
      ]
    );
  }
  function getData(Request $request)
  {
    $heading_msg = '';
    $user_msg = '';
    $lot_yes = 1;
    $scheme_code = $request->scheme_code;
    $district = $request->district;

    $rules = [
      'payment_mode' => 'required',
      'lot_year' => 'required',
      'lot_month' => 'required'
    ];
    $data = array();
    $column = "";
    $attributes = array();
    $messages = array();
    $attributes['payment_mode'] = 'Select Payment Mode';
    $attributes['lot_year'] = 'Select Financial Year';
    $attributes['lot_month'] = 'Select Month';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $user_id = AuthChecker::getUserId();
      $lot_year = $request->lot_year;
      $lot_month = $request->lot_month;
      $payment_mode = $request->payment_mode;
      $urban_code = $request->urban_code;
      $year_explode = explode('-', $lot_year);
      if ($lot_month == 1 || $lot_month == 2 || $lot_month == 3) {
        $acd_year = $year_explode[1];
      } else {
        $acd_year = $year_explode[0];
      }
      if ($lot_month != '') {
        $lot_month_val = '';
        foreach (Config::get('constants.month_list') as $key => $val) {
          if ($lot_month == $val) {
            $lot_month_val = $key;
            break;
          }
        }
      }
      $first_date_month = $acd_year . '-' . $lot_month_val . '-' . '01';

      $data = array();
      $return_status = 1;
      $return_msg = '';
      $heading_msg = '';
      $lot_yes = 1;
      $user_msg = $user_msg . ' Financial Year-' . $lot_year . ',Month-' . $lot_month;
      if (empty($urban_code)) {
        $urban_code_condition = " and rural_urban_id IN (1,2)";
        $user_msg = $user_msg . ' Rural/ Urban=ALL';
      } else {
        if ($urban_code == 1) {
          $urban_code_text = 'Urban';
        } else {
          $urban_code_text = 'Rural';
        }
        $urban_code_condition = " and rural_urban_id=" . $urban_code;
        $user_msg = $user_msg . ' Rural/ Urban=' . $urban_code_text;
      }
      if (empty($district)) {
        $district_condition = "";
        $lot_yes = 1;
      } else {
        $district_condition = " and created_by_dist_code=" . $district;
        $district_row = District::where('district_code', $district)->first();
        $user_msg = $user_msg . ' District=' . $district_row->district_name;
        $lot_yes = 0;
      }
      $main_table_name = 'pension.beneficiary';
      if ($payment_mode == 'IFMS')
        $payment_scheme_in = array(1, 5, 6, 7, 19);
      elseif ($payment_mode == 'SBI')
        $payment_scheme_in = array(2, 3, 8, 9, 10, 11, 17);
      if (!is_null($scheme_code)) {
        $schemes_arr =  Scheme::select('id', 'short_code', 'scheme_name')->where('id', '=', $scheme_code)->first();
        if (empty($schema_name)) {
          $heading_msg = $heading_msg . ' Scheme:' . $schemes_arr->scheme_name;
          $schema_name = 'pension';
          $main_table_name =  $schema_name . '.beneficiary';
        }
        $user_msg = $user_msg . ' Scheme-' .  $schemes_arr->scheme_name;
      } else {
        $schemes_in_arr =  Configduty::select('scheme_id')->where('user_id', '=', $user_id)->whereIn('scheme_id', $payment_scheme_in)->get();
        $schemes_in = array();
        // dd($schemes_in_arr);
        foreach ($schemes_in_arr  as $schm) {
          array_push($schemes_in, $schm->scheme_id);
        }
      }

      $designation_id_old = Auth::user()->designation_id_old;

      if ($designation_id_old == 'Verifier' || $designation_id_old == 'Operator') {
        if ($payment_mode == 'IFMS') {
          $query = "select A.*,B.*
          from
          (
          select id,scheme_name as location_name from m_scheme where id in ( " . implode(',', $schemes_in) . ") and id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")
          order by scheme_name
          ) as A LEFT JOIN
          (
          select 
          K.scheme_id,
          ben.total_ben,
          K.total_beneficiary_under_lot,
          ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
          K.no_of_lots_created,
          K.no_of_lots_pushed_for_payments,
          K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
          K.no_of_lots_response_received,
          K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
          K.success as successfull_payments,
          K.failed as failed_payments,
          ben.total_ben-(K.success+K.failed) as pending_beneficiary
          from
          (
          select
          BB.scheme_id,
          coalesce(count(AA.id),0) as total_beneficiary_under_lot,
          count(distinct(BB.lot_no)) as no_of_lots_created,
          count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
          count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1 and BB.lot_status=6) as no_of_lots_response_received,
          coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=0 and BB.ref_no>0),0) as success,
          coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=2 and BB.ref_no>0),0) as failed
          from ifms.transaction_lot_details_report as AA
          JOIN ifms.transaction_lot as BB ON AA.drn_part=BB.lot_no
          JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
          where BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "'
          group by BB.scheme_id
          UNION
          select
          BB.scheme_id,
          coalesce(count(AA.id),0) as total_beneficiary_under_lot,
          count(distinct(BB.lot_no)) as no_of_lots_created,
          count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
          count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1 and BB.lot_status=6) as no_of_lots_response_received,
          coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=0 and BB.ref_no>0),0) as success,
          coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=2 and BB.ref_no>0),0) as failed
          from ifms.transaction_lot_details as AA
          JOIN ifms.transaction_lot as BB ON AA.drn_part=BB.lot_no
          JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
          where BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "'
          group by BB.scheme_id
          ) as K
          LEFT JOIN
          (
          select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
          as total_ben,
          scheme_id
          from pension.beneficiary where next_level_role_id=0
          group by scheme_id
          ) as ben ON K.scheme_id=ben.scheme_id
          ) as B ON A.id=B.scheme_id";
        } else if ($payment_mode == 'SBI') {
          $query = "select A.*,B.*
          from
          (
          select id,scheme_name as location_name from m_scheme where id in ( " . implode(',', $schemes_in) . ") and id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")
          order by scheme_name
          ) as A LEFT JOIN
          (
          select 
          K.scheme_id,
          ben.total_ben,
          K.total_beneficiary_under_lot,
          ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
          K.no_of_lots_created,
          K.no_of_lots_pushed_for_payments,
          K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
          K.no_of_lots_response_received,
          K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
          K.success as successfull_payments,
          K.failed as failed_payments,
          ben.total_ben-(K.success+K.failed) as pending_beneficiary
          from
          (
          select
          BB.scheme_id,
          coalesce(count(AA.id),0) as total_beneficiary_under_lot,
          count(distinct(BB.lot_no)) as no_of_lots_created,
          count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >0) as no_of_lots_pushed_for_payments,
          count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >3) as no_of_lots_response_received,
          coalesce(count(AA.id) FILTER(WHERE AA.status_code='S00' and BB.lot_status>3),0) as success,
          coalesce(count(AA.id) FILTER(WHERE AA.status_code!='S00' and AA.status_code is not null),0) as failed
          from sbi.transaction_lot_details_report as AA
          JOIN sbi.transaction_lot as BB ON AA.lot_no=BB.lot_no
          JOIN public.lot_master as CC ON AA.lot_no=CC.lot_no
          where BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "'
          group by BB.scheme_id
          UNION
          select
          BB.scheme_id,
          coalesce(count(AA.id),0) as total_beneficiary_under_lot,
          count(distinct(BB.lot_no)) as no_of_lots_created,
          count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >0) as no_of_lots_pushed_for_payments,
          count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >3) as no_of_lots_response_received,
          coalesce(count(AA.id) FILTER(WHERE AA.status_code='S00' and BB.lot_status>3),0) as success,
          coalesce(count(AA.id) FILTER(WHERE AA.status_code!='S00' and AA.status_code is not null),0) as failed
          from sbi.transaction_lot_details as AA
          JOIN sbi.transaction_lot as BB ON AA.lot_no=BB.lot_no
          JOIN public.lot_master as CC ON AA.lot_no=CC.lot_no
          where BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "'
          group by BB.scheme_id
          ) as K
          LEFT JOIN
          (
          select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
          as total_ben,
          scheme_id
          from pension.beneficiary where next_level_role_id=0
          group by scheme_id
          ) as ben ON K.scheme_id=ben.scheme_id
          ) as B ON A.id=B.scheme_id";
        }

        $data_part = DB::connection('pgsql_mis')->select($query);
        $data = array_merge($data, $data_part);
        $heading_msg = 'Scheme Wise Monthly Payment Status (' . $payment_mode . ') ' . $user_msg;
        $column = "Scheme";
      } else if ($designation_id_old == 'Approver') {
        if (empty($scheme_code)) {
          if (empty($urban_code)) {
            $urban_code_condition = " and rural_urban_id IN (1,2)";
          } else {
            $urban_code_condition = " and rural_urban_id=" . $urban_code;
          }
          if ($payment_mode == 'IFMS') {
            $query = "select A.*,B.*
            from
            (
            select id,scheme_name as location_name from m_scheme where id in ( " . implode(',', $schemes_in) . ") and id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")
            order by scheme_name
            ) as A LEFT JOIN
            (
            select 
            K.scheme_id,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select
            BB.scheme_id,
            coalesce(count(AA.id),0) as total_beneficiary_under_lot,
            count(distinct(BB.lot_no)) as no_of_lots_created,
            count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
            count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1 and BB.lot_status=6) as no_of_lots_response_received,
            coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=0 and BB.ref_no>0),0) as success,
            coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=2 and BB.ref_no>0),0) as failed
            from ifms.transaction_lot_details_report as AA
            JOIN ifms.transaction_lot as BB ON AA.drn_part=BB.lot_no
            JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
            where BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "'
            group by BB.scheme_id
            UNION
            select
            BB.scheme_id,
            coalesce(count(AA.id),0) as total_beneficiary_under_lot,
            count(distinct(BB.lot_no)) as no_of_lots_created,
            count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
            count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1 and BB.lot_status=6) as no_of_lots_response_received,
            coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=0 and BB.ref_no>0),0) as success,
            coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=2 and BB.ref_no>0),0) as failed
            from ifms.transaction_lot_details as AA
            JOIN ifms.transaction_lot as BB ON AA.drn_part=BB.lot_no
            JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
            where BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "'
            group by BB.scheme_id
            ) as K
            LEFT JOIN
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            scheme_id
            from pension.beneficiary where next_level_role_id=0 " . $urban_code_condition . "
            group by scheme_id
            ) as ben ON K.scheme_id=ben.scheme_id
            ) as B ON A.id=B.scheme_id";
          } else if ($payment_mode == 'SBI') {
            $query = "select A.*,B.*
            from
            (
            select id,scheme_name as location_name from m_scheme where id in ( " . implode(',', $schemes_in) . ") and id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")
            order by scheme_name
            ) as A LEFT JOIN
            (
            select 
            K.scheme_id,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select
            BB.scheme_id,
            coalesce(count(AA.id),0) as total_beneficiary_under_lot,
            count(distinct(BB.lot_no)) as no_of_lots_created,
            count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >0) as no_of_lots_pushed_for_payments,
            count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >3) as no_of_lots_response_received,
            coalesce(count(AA.id) FILTER(WHERE AA.status_code='S00' and BB.lot_status>3),0) as success,
            coalesce(count(AA.id) FILTER(WHERE AA.status_code!='S00' and AA.status_code is not null),0) as failed
            from sbi.transaction_lot_details_report as AA
            JOIN sbi.transaction_lot as BB ON AA.lot_no=BB.lot_no
            JOIN public.lot_master as CC ON AA.lot_no=CC.lot_no
            where BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "'
            group by BB.scheme_id
            UNION
            select
            BB.scheme_id,
            coalesce(count(AA.id),0) as total_beneficiary_under_lot,
            count(distinct(BB.lot_no)) as no_of_lots_created,
            count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >0) as no_of_lots_pushed_for_payments,
            count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >3) as no_of_lots_response_received,
            coalesce(count(AA.id) FILTER(WHERE AA.status_code='S00' and BB.lot_status>3),0) as success,
            coalesce(count(AA.id) FILTER(WHERE AA.status_code!='S00' and AA.status_code is not null),0) as failed
            from sbi.transaction_lot_details as AA
            JOIN sbi.transaction_lot as BB ON AA.lot_no=BB.lot_no
            JOIN public.lot_master as CC ON AA.lot_no=CC.lot_no
            where BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "'
            group by BB.scheme_id
            ) as K
            LEFT JOIN
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            scheme_id
            from pension.beneficiary where next_level_role_id=0  " . $urban_code_condition . "
            group by scheme_id
            ) as ben ON K.scheme_id=ben.scheme_id
            ) as B ON A.id=B.scheme_id";
          }
          $data_part = DB::connection('pgsql_mis')->select($query);
          $data = array_merge($data, $data_part);
          $heading_msg = 'Scheme Wise Monthly Payment Status (' . $payment_mode . ') ' . $user_msg;
          $column = "Scheme";
        } else {
          if ($urban_code == 1) {
            if ($payment_mode == 'IFMS') {
              $query = "select A.*,B.*
              from
              (
                select urban_body_code as code,urban_body_name||'-M' as location_name from m_urban_body
                where district_code=" . $district . "
                order by urban_body_name
              ) as A LEFT JOIN
              (
                select
                DD.block_ulb_code,
                coalesce(count(AA.id),0) as total_beneficiary_under_lot,
                count(distinct(BB.lot_no)) as no_of_lots_created,
                count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
                count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1 and BB.lot_status=6) as no_of_lots_response_received,
                coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=0 and BB.ref_no>0),0) as success,
                coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=2 and BB.ref_no>0),0) as failed
                from " . $main_table_name . " as DD LEFT JOIN 
                ifms.transaction_lot_details_report as AA ON DD.id=AA.pension_id
                JOIN ifms.transaction_lot as BB ON AA.drn_part=BB.lot_no
                JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
                where BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
                and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
                and DD.rural_urban_id=1
                group by DD.block_ulb_code
                UNION
                select
                DD.block_ulb_code,
                coalesce(count(AA.id),0) as total_beneficiary_under_lot,
                count(distinct(BB.lot_no)) as no_of_lots_created,
                count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
                count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1 and BB.lot_status=6) as no_of_lots_response_received,
                coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=0 and BB.ref_no>0),0) as success,
                coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=2 and BB.ref_no>0),0) as failed
                from " . $main_table_name . " as DD LEFT JOIN 
                ifms.transaction_lot_details as AA ON DD.id=AA.pension_id
                JOIN ifms.transaction_lot as BB ON AA.drn_part=BB.lot_no
                JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
                where  BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
                and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
                and DD.rural_urban_id=1
                group by DD.block_ulb_code
              ) as B ON A.code=B.block_ulb_code";
            } else if ($payment_mode == 'SBI') {
              $query = "select A.*,B.*
              from
              (
                select urban_body_code as code,urban_body_name||'-M' as location_name from m_urban_body
                where district_code=" . $district . "
                order by urban_body_name
              ) as A LEFT JOIN
              (
                select
                DD.block_ulb_code,
                coalesce(count(AA.id),0) as total_beneficiary_under_lot,
                count(distinct(BB.lot_no)) as no_of_lots_created,
                count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >0) as no_of_lots_pushed_for_payments,
                count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >3) as no_of_lots_response_received,
                coalesce(count(AA.id) FILTER(WHERE AA.status_code='S00' and BB.lot_status>3),0) as success,
                coalesce(count(AA.id) FILTER(WHERE AA.status_code!='S00' and AA.status_code is not null),0) as failed
                from " . $main_table_name . " as DD LEFT JOIN 
                sbi.transaction_lot_details_report as AA ON DD.id=AA.pension_id
                JOIN sbi.transaction_lot as BB ON AA.drn_part=BB.lot_no
                JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
                where BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
                and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
                and DD.rural_urban_id=1
                group by DD.block_ulb_code
                UNION
                select
                DD.block_ulb_code,
                coalesce(count(AA.id),0) as total_beneficiary_under_lot,
                count(distinct(BB.lot_no)) as no_of_lots_created,
                count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >0) as no_of_lots_pushed_for_payments,
                count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >3) as no_of_lots_response_received,
                coalesce(count(AA.id) FILTER(WHERE AA.status_code='S00' and BB.lot_status>3),0) as success,
                coalesce(count(AA.id) FILTER(WHERE AA.status_code!='S00' and AA.status_code is not null),0) as failed
                from " . $main_table_name . " as DD LEFT JOIN 
                sbi.transaction_lot_details as AA ON DD.id=AA.pension_id
                JOIN sbi.transaction_lot as BB ON AA.drn_part=BB.lot_no
                JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
                where  BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
                and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
                and DD.rural_urban_id=1
                group by DD.block_ulb_code
              ) as B ON A.code=B.block_ulb_code";
            }

            $data_part = DB::connection('pgsql_mis')->select($query);
            $data = array_merge($data, $data_part);
            $heading_msg = 'Municipality Wise Monthly Payment Status (' . $payment_mode . ') ' . $user_msg;
            $column = "Municipality";
          } else if ($urban_code == 2) {
            if ($payment_mode == 'IFMS') {
              $query = "select A.*,B.*
              from
              (
                select block_code as code,block_name||'-B' as location_name from m_block
            where district_code=" . $district . "
            order by block_name
              ) as A LEFT JOIN
              (
                select
                DD.block_ulb_code,
                coalesce(count(AA.id),0) as total_beneficiary_under_lot,
                count(distinct(BB.lot_no)) as no_of_lots_created,
                count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
                count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1 and BB.lot_status=6) as no_of_lots_response_received,
                coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=0 and BB.ref_no>0),0) as success,
                coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=2 and BB.ref_no>0),0) as failed
                from " . $main_table_name . " as DD LEFT JOIN 
                ifms.transaction_lot_details_report as AA ON DD.id=AA.pension_id
                JOIN ifms.transaction_lot as BB ON AA.drn_part=BB.lot_no
                JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
                where  BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
                and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
                and DD.rural_urban_id=2
                group by DD.block_ulb_code
                UNION
                select
                DD.block_ulb_code,
                coalesce(count(AA.id),0) as total_beneficiary_under_lot,
                count(distinct(BB.lot_no)) as no_of_lots_created,
                count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
                count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1 and BB.lot_status=6) as no_of_lots_response_received,
                coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=0 and BB.ref_no>0),0) as success,
                coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=2 and BB.ref_no>0),0) as failed
                from " . $main_table_name . " as DD LEFT JOIN 
                ifms.transaction_lot_details as AA ON DD.id=AA.pension_id
                JOIN ifms.transaction_lot as BB ON AA.drn_part=BB.lot_no
                JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
                where  BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
                and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
                and DD.rural_urban_id=2
                group by DD.block_ulb_code
              ) as B ON A.code=B.block_ulb_code";
            } else if ($payment_mode == 'SBI') {
              $query = "select A.*,B.*
              from
              (
                select block_code as code,block_name||'-B' as location_name from m_block
            where district_code=" . $district . "
            order by block_name
              ) as A LEFT JOIN
              (
                select
                DD.block_ulb_code,
                coalesce(count(AA.id),0) as total_beneficiary_under_lot,
                count(distinct(BB.lot_no)) as no_of_lots_created,
                count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >0) as no_of_lots_pushed_for_payments,
                count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >3) as no_of_lots_response_received,
                coalesce(count(AA.id) FILTER(WHERE AA.status_code='S00' and BB.lot_status>3),0) as success,
                coalesce(count(AA.id) FILTER(WHERE AA.status_code!='S00' and AA.status_code is not null),0) as failed
                from " . $main_table_name . " as DD LEFT JOIN 
                sbi.transaction_lot_details_report as AA ON DD.id=AA.pension_id
                JOIN sbi.transaction_lot as BB ON AA.drn_part=BB.lot_no
                JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
                where  BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
                and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
                and DD.rural_urban_id=2
                group by DD.block_ulb_code
                UNION
                select
                DD.block_ulb_code,
                coalesce(count(AA.id),0) as total_beneficiary_under_lot,
                count(distinct(BB.lot_no)) as no_of_lots_created,
                count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >0) as no_of_lots_pushed_for_payments,
                count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >3) as no_of_lots_response_received,
                coalesce(count(AA.id) FILTER(WHERE AA.status_code='S00' and BB.lot_status>3),0) as success,
                coalesce(count(AA.id) FILTER(WHERE AA.status_code!='S00' and AA.status_code is not null),0) as failed
                from " . $main_table_name . " as DD LEFT JOIN 
                sbi.transaction_lot_details as AA ON DD.id=AA.pension_id
                JOIN sbi.transaction_lot as BB ON AA.drn_part=BB.lot_no
                JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
                where  BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
                and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
                and DD.rural_urban_id=2
                group by DD.block_ulb_code
              ) as B ON A.code=B.block_ulb_code";
            }
            $data_part = DB::connection('pgsql_mis')->select($query);
            $data = array_merge($data, $data_part);
            $heading_msg = 'Block Wise Monthly Payment Status (' . $payment_mode . ') ' . $user_msg;
            $column = "Block";
          } else {
            if ($payment_mode == 'IFMS') {
              $query = "select A.*,B.*
            from
            (
              select urban_body_code as code,urban_body_name||'-M' as location_name from m_urban_body
              where district_code=" . $district . "
              order by urban_body_name
            ) as A LEFT JOIN
            (
              select
              DD.block_ulb_code,
              coalesce(count(AA.id),0) as total_beneficiary_under_lot,
              count(distinct(BB.lot_no)) as no_of_lots_created,
              count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
              count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1 and BB.lot_status=6) as no_of_lots_response_received,
              coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=0 and BB.ref_no>0),0) as success,
              coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=2 and BB.ref_no>0),0) as failed
              from " . $main_table_name . " as DD LEFT JOIN 
              ifms.transaction_lot_details_report as AA ON DD.id=AA.pension_id
              JOIN ifms.transaction_lot as BB ON AA.drn_part=BB.lot_no
              JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
              where BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
              and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
              and DD.rural_urban_id=1
              group by DD.block_ulb_code
              UNION
              select
              DD.block_ulb_code,
              coalesce(count(AA.id),0) as total_beneficiary_under_lot,
              count(distinct(BB.lot_no)) as no_of_lots_created,
              count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
              count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1 and BB.lot_status=6) as no_of_lots_response_received,
              coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=0 and BB.ref_no>0),0) as success,
              coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=2 and BB.ref_no>0),0) as failed
              from " . $main_table_name . " as DD LEFT JOIN 
              ifms.transaction_lot_details as AA ON DD.id=AA.pension_id
              JOIN ifms.transaction_lot as BB ON AA.drn_part=BB.lot_no
              JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
              where  BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
              and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
              and DD.rural_urban_id=1
              group by DD.block_ulb_code
            ) as B ON A.code=B.block_ulb_code
            UNION
            select A.*,B.*
            from
            (
              select block_code as code,block_name||'-B' as location_name from m_block
          where district_code=" . $district . "
          order by block_name
            ) as A LEFT JOIN
            (
              select
              DD.block_ulb_code,
              coalesce(count(AA.id),0) as total_beneficiary_under_lot,
              count(distinct(BB.lot_no)) as no_of_lots_created,
              count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
              count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1 and BB.lot_status=6) as no_of_lots_response_received,
              coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=0 and BB.ref_no>0),0) as success,
              coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=2 and BB.ref_no>0),0) as failed
              from " . $main_table_name . " as DD LEFT JOIN 
              ifms.transaction_lot_details_report as AA ON DD.id=AA.pension_id
              JOIN ifms.transaction_lot as BB ON AA.drn_part=BB.lot_no
              JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
              where  BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
              and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
              and DD.rural_urban_id=2
              group by DD.block_ulb_code
              UNION
              select
              DD.block_ulb_code,
              coalesce(count(AA.id),0) as total_beneficiary_under_lot,
              count(distinct(BB.lot_no)) as no_of_lots_created,
              count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
              count(distinct(BB.lot_no)) FILTER(WHERE CC.push_to_ifms_status=1 and BB.lot_status=6) as no_of_lots_response_received,
              coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=0 and BB.ref_no>0),0) as success,
              coalesce(count(AA.id) FILTER(WHERE AA.wrongdata_flag=2 and BB.ref_no>0),0) as failed
              from " . $main_table_name . " as DD LEFT JOIN 
              ifms.transaction_lot_details as AA ON DD.id=AA.pension_id
              JOIN ifms.transaction_lot as BB ON AA.drn_part=BB.lot_no
              JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
              where  BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
              and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
              and DD.rural_urban_id=2
              group by DD.block_ulb_code
            ) as B ON A.code=B.block_ulb_code";
            } else if ($payment_mode == 'SBI') {
              $query = "select A.*,B.*
            from
            (
              select urban_body_code as code,urban_body_name||'-M' as location_name from m_urban_body
              where district_code=" . $district . "
              order by urban_body_name
            ) as A LEFT JOIN
            (
              select
              DD.block_ulb_code,
              coalesce(count(AA.id),0) as total_beneficiary_under_lot,
              count(distinct(BB.lot_no)) as no_of_lots_created,
              count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >0) as no_of_lots_pushed_for_payments,
              count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >3) as no_of_lots_response_received,
              coalesce(count(AA.id) FILTER(WHERE AA.status_code='S00' and BB.lot_status>3),0) as success,
              coalesce(count(AA.id) FILTER(WHERE AA.status_code!='S00' and AA.status_code is not null),0) as failed
              from " . $main_table_name . " as DD LEFT JOIN 
              sbi.transaction_lot_details_report as AA ON DD.id=AA.pension_id
              JOIN sbi.transaction_lot as BB ON AA.drn_part=BB.lot_no
              JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
              where BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
              and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
              and DD.rural_urban_id=1
              group by DD.block_ulb_code
              UNION
              select
              DD.block_ulb_code,
              coalesce(count(AA.id),0) as total_beneficiary_under_lot,
              count(distinct(BB.lot_no)) as no_of_lots_created,
              count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >0) as no_of_lots_pushed_for_payments,
              count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >3) as no_of_lots_response_received,
              coalesce(count(AA.id) FILTER(WHERE AA.status_code='S00' and BB.lot_status>3),0) as success,
              coalesce(count(AA.id) FILTER(WHERE AA.status_code!='S00' and AA.status_code is not null),0) as failed
              from " . $main_table_name . " as DD LEFT JOIN 
              sbi.transaction_lot_details as AA ON DD.id=AA.pension_id
              JOIN sbi.transaction_lot as BB ON AA.drn_part=BB.lot_no
              JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
              where  BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
              and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
              and DD.rural_urban_id=1
              group by DD.block_ulb_code
            ) as B ON A.code=B.block_ulb_code
            UNION
            select A.*,B.*
            from
            (
              select block_code as code,block_name||'-B' as location_name from m_block
          where district_code=" . $district . "
          order by block_name
            ) as A LEFT JOIN
            (
              select
              DD.block_ulb_code,
              coalesce(count(AA.id),0) as total_beneficiary_under_lot,
              count(distinct(BB.lot_no)) as no_of_lots_created,
              count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >0) as no_of_lots_pushed_for_payments,
              count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >3) as no_of_lots_response_received,
              coalesce(count(AA.id) FILTER(WHERE AA.status_code='S00' and BB.lot_status>3),0) as success,
              coalesce(count(AA.id) FILTER(WHERE AA.status_code!='S00' and AA.status_code is not null),0) as failed
              from " . $main_table_name . " as DD LEFT JOIN 
              sbi.transaction_lot_details_report as AA ON DD.id=AA.pension_id
              JOIN sbi.transaction_lot as BB ON AA.drn_part=BB.lot_no
              JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
              where  BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
              and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
              and DD.rural_urban_id=2
              group by DD.block_ulb_code
              UNION
              select
              DD.block_ulb_code,
              coalesce(count(AA.id),0) as total_beneficiary_under_lot,
              count(distinct(BB.lot_no)) as no_of_lots_created,
              count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >0) as no_of_lots_pushed_for_payments,
              count(distinct(BB.lot_no)) FILTER(WHERE BB.lot_status >3) as no_of_lots_response_received,
              coalesce(count(AA.id) FILTER(WHERE AA.status_code='S00' and BB.lot_status>3),0) as success,
              coalesce(count(AA.id) FILTER(WHERE AA.status_code!='S00' and AA.status_code is not null),0) as failed
              from " . $main_table_name . " as DD LEFT JOIN 
              sbi.transaction_lot_details as AA ON DD.id=AA.pension_id
              JOIN sbi.transaction_lot as BB ON AA.drn_part=BB.lot_no
              JOIN public.lot_master as CC ON AA.drn_part=CC.lot_no
              where  BB.lot_year= '" . $lot_year . "' and BB.lot_month= '" . $lot_month . "' and AA.scheme_id=" . $scheme_code . " 
              and BB.scheme_id=" . $scheme_code . " and CC.scheme_id=" . $scheme_code . " and DD.created_by_local_body_code=" . $district . " 
              and DD.rural_urban_id=2
              group by DD.block_ulb_code
            ) as B ON A.code=B.block_ulb_code";
            }
            $data_part = DB::connection('pgsql_mis')->select($query);
            $data = array_merge($data, $data_part);
            $heading_msg = 'Block/Munc Wise Monthly Payment Status (' . $payment_mode . ') ' . $user_msg;
            $column = "Block/Munc";
          }
        }
      } else if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'DDO' || $designation_id_old == 'Corp') {
        $schemewise = $districrwise = $blockwise = $muncwise = $blkmuncwise = 0;
        if (empty($scheme_code)) {
          $schemewise = 1;
          $lot_yes = 1;
          $districrwise = $blockwise = $muncwise = $blkmuncwise = 0;
        } else {
          $schemewise = 0;
          $lot_yes = 0;
          if (empty($district)) {
            $districrwise = 1;
            $blockwise = $muncwise = $blkmuncwise = 0;
          } else {
            $districrwise = 0;
            if (empty($urban_code)) {
              $blkmuncwise = 1;
              $blockwise = $muncwise = 0;
            } else {
              if ($urban_code == 1) {
                $muncwise = 1;
                $blockwise = 0;
              } else {
                $muncwise = 0;
                $blockwise = 1;
              }
            }
          }
        }
        if ($schemewise == 1) {
          if ($payment_mode == 'IFMS') {
            $query = "select A.location_name,B.*
            from
            (
            select id,scheme_name as location_name from m_scheme where id in ( " . implode(',', $schemes_in) . ") and id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")
            order by scheme_name
            ) as A LEFT JOIN
            (
            select K.scheme_id,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            scheme_id
            from pension.beneficiary where scheme_id in ( " . implode(',', $schemes_in) . ")
            and next_level_role_id=0 " . $urban_code_condition . " " . $district_condition . "
            group by scheme_id
            ) as ben LEFT JOIN
            (
              select scheme_id,
                sum(total_beneficiary_under_lot) as total_beneficiary_under_lot,
                sum(no_of_lots_created) as no_of_lots_created,
                sum(no_of_lots_pushed_for_payments) as no_of_lots_pushed_for_payments,
                sum(no_of_lots_response_received) as no_of_lots_response_received,
                sum(success) as success,
                sum(failed) as failed
              from(
                  select 
                  P.scheme_id,
                 coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                 count(distinct(tl.lot_no)) as no_of_lots_created,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1 and tl.lot_status=6) as no_of_lots_response_received,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=0 and tl.ref_no>0),0) as success,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=2 and tl.ref_no>0),0) as failed
                 from ifms.transaction_lot_details_report  as tld 
                  JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no
                  JOIN pension.beneficiary as P ON P.id=tld.pension_id
                  where P.scheme_id in ( " . implode(',', $schemes_in) . ") and tld.scheme_id in ( " . implode(',', $schemes_in) . ")
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                  group by P.scheme_id
                  UNION
                  select 
                  P.scheme_id,
                 coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                 count(distinct(tl.lot_no)) as no_of_lots_created,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1 and tl.lot_status=6) as no_of_lots_response_received,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=0 and tl.ref_no>0),0) as success,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=2 and tl.ref_no>0),0) as failed
                 from ifms.transaction_lot_details  as tld 
                  JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no
                  JOIN pension.beneficiary as P ON P.id=tld.pension_id
                  where P.scheme_id in ( " . implode(',', $schemes_in) . ") and tld.scheme_id in ( " . implode(',', $schemes_in) . ")
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                  group by P.scheme_id
            ) as U group by scheme_id
            ) as K ON ben.scheme_id=K.scheme_id
            ) as B ON A.id=B.scheme_id";
          } else if ($payment_mode == 'SBI') {
            $query = "select A.location_name,B.*
            from
            (
            select id,scheme_name as location_name from m_scheme where id in ( " . implode(',', $schemes_in) . ") and id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")
            order by scheme_name
            ) as A LEFT JOIN
            (
            select K.scheme_id,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            scheme_id
            from pension.beneficiary where scheme_id in ( " . implode(',', $schemes_in) . ")
            and next_level_role_id=0 " . $urban_code_condition . " " . $district_condition . "
            group by scheme_id
            ) as ben LEFT JOIN
            (
              select scheme_id,
                sum(total_beneficiary_under_lot) as total_beneficiary_under_lot,
                sum(no_of_lots_created) as no_of_lots_created,
                sum(no_of_lots_pushed_for_payments) as no_of_lots_pushed_for_payments,
                sum(no_of_lots_response_received) as no_of_lots_response_received,
                sum(success) as success,
                sum(failed) as failed
              from(
                  select 
                  P.scheme_id,
                  coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                        count(distinct(tl.lot_no)) as no_of_lots_created,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >0) as no_of_lots_pushed_for_payments,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >3) as no_of_lots_response_received,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as success,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as failed
                  from sbi.transaction_lot_details_report  as tld 
                  JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no
                  JOIN pension.beneficiary as P ON P.id=tld.pension_id
                  where P.scheme_id in ( " . implode(',', $schemes_in) . ") and tld.scheme_id in ( " . implode(',', $schemes_in) . ")
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                  group by P.scheme_id
                  UNION
                  select 
                  P.scheme_id,
                  coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                        count(distinct(tl.lot_no)) as no_of_lots_created,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >0) as no_of_lots_pushed_for_payments,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >3) as no_of_lots_response_received,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as success,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as failed
                  from sbi.transaction_lot_details  as tld 
                  JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no
                  JOIN pension.beneficiary as P ON P.id=tld.pension_id
                  where P.scheme_id in ( " . implode(',', $schemes_in) . ") and tld.scheme_id in ( " . implode(',', $schemes_in) . ") 
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                  group by P.scheme_id
            ) as U group by scheme_id
            ) as K ON ben.scheme_id=K.scheme_id
            ) as B ON A.id=B.scheme_id";
          }
          $data_part = DB::connection('pgsql_mis')->select($query);
          $data = array_merge($data, $data_part);
          $heading_msg = 'Scheme Wise Monthly Payment Status (' . $payment_mode . ') ' . $user_msg;
          $column = "Scheme";
        } else if ($districrwise == 1) {
          if ($payment_mode == 'IFMS') {
            $query = "select A.location_name,B.*
            from
            (
            select district_code,district_name as location_name from m_district order by district_name) as A 
            LEFT JOIN
            (
            select K.created_by_dist_code,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            created_by_dist_code
            from " . $main_table_name . " where  scheme_id=" . $scheme_code . " and next_level_role_id=0 " . $urban_code_condition . " " . $district_condition . "
            group by created_by_dist_code
            ) as ben LEFT JOIN
            (
              select created_by_dist_code,
                sum(total_beneficiary_under_lot) as total_beneficiary_under_lot,
                sum(no_of_lots_created) as no_of_lots_created,
                sum(no_of_lots_pushed_for_payments) as no_of_lots_pushed_for_payments,
                sum(no_of_lots_response_received) as no_of_lots_response_received,
                sum(success) as success,
                sum(failed) as failed
              from(
                select 
                P.created_by_dist_code,
               coalesce(count(tld.id),0) as total_beneficiary_under_lot,
               count(distinct(tl.lot_no)) as no_of_lots_created,
               count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
               count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1 and tl.lot_status=6) as no_of_lots_response_received,
               coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=0 and tl.ref_no>0),0) as success,
               coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=2 and tl.ref_no>0),0) as failed
               from ifms.transaction_lot_details_report  as tld 
                JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no
                JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                where P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                group by P.created_by_dist_code
                  UNION
                  select 
                  P.created_by_dist_code,
                 coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                 count(distinct(tl.lot_no)) as no_of_lots_created,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1 and tl.lot_status=6) as no_of_lots_response_received,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=0 and tl.ref_no>0),0) as success,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=2 and tl.ref_no>0),0) as failed
                 from ifms.transaction_lot_details  as tld 
                  JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                  group by P.created_by_dist_code
            ) as U group by created_by_dist_code
            ) as K ON ben.created_by_dist_code=K.created_by_dist_code
            ) as B ON A.district_code=B.created_by_dist_code";
          } else if ($payment_mode == 'SBI') {
            $query = "select A.location_name,B.*
            from
            (
              select district_code,district_name as location_name from m_district order by district_name) as A 
             LEFT JOIN
            (
            select K.created_by_dist_code,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            created_by_dist_code
            from " . $main_table_name . " where  scheme_id=" . $scheme_code . " and next_level_role_id=0 " . $urban_code_condition . " 
            group by created_by_dist_code
            ) as ben LEFT JOIN
            (
              select created_by_dist_code,
                sum(total_beneficiary_under_lot) as total_beneficiary_under_lot,
                sum(no_of_lots_created) as no_of_lots_created,
                sum(no_of_lots_pushed_for_payments) as no_of_lots_pushed_for_payments,
                sum(no_of_lots_response_received) as no_of_lots_response_received,
                sum(success) as success,
                sum(failed) as failed
              from(
                  select 
                  P.created_by_dist_code,
                  coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                        count(distinct(tl.lot_no)) as no_of_lots_created,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >0) as no_of_lots_pushed_for_payments,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >3) as no_of_lots_response_received,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as success,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as failed
                  from sbi.transaction_lot_details_report  as tld 
                  JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " 
                  group by P.created_by_dist_code
                  UNION
                  select 
                  P.created_by_dist_code,
                  coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                        count(distinct(tl.lot_no)) as no_of_lots_created,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >0) as no_of_lots_pushed_for_payments,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >3) as no_of_lots_response_received,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as success,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as failed
                  from sbi.transaction_lot_details  as tld 
                  JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . " 
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " 
                  group by P.created_by_dist_code
            ) as U group by created_by_dist_code
            ) as K ON ben.created_by_dist_code=K.created_by_dist_code
            ) as B ON A.district_code=B.created_by_dist_code";
          }
          $data_part = DB::connection('pgsql_mis')->select($query);
          $data = array_merge($data, $data_part);
          $heading_msg = 'District Wise Monthly Payment Status (' . $payment_mode . ') ' . $user_msg;
          $column = "District";
        } else if ($blkmuncwise == 1) {
          if ($payment_mode == 'IFMS') {
            $query1 = "select A.location_name,B.*
            from
            (
              select block_code,block_name||'-B' as location_name from m_block
              where district_code=" . $district . "
              order by block_name) as A 
            LEFT JOIN
            (
            select K.block_ulb_code,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            block_ulb_code
            from " . $main_table_name . " where  scheme_id=" . $scheme_code . " and next_level_role_id=0 " . $urban_code_condition . " " . $district_condition . "
            group by block_ulb_code
            ) as ben LEFT JOIN
            (
              select block_ulb_code,
                sum(total_beneficiary_under_lot) as total_beneficiary_under_lot,
                sum(no_of_lots_created) as no_of_lots_created,
                sum(no_of_lots_pushed_for_payments) as no_of_lots_pushed_for_payments,
                sum(no_of_lots_response_received) as no_of_lots_response_received,
                sum(success) as success,
                sum(failed) as failed
              from(
                select 
                P.block_ulb_code,
               coalesce(count(tld.id),0) as total_beneficiary_under_lot,
               count(distinct(tl.lot_no)) as no_of_lots_created,
               count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
               count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1 and tl.lot_status=6) as no_of_lots_response_received,
               coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=0 and tl.ref_no>0),0) as success,
               coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=2 and tl.ref_no>0),0) as failed
               from ifms.transaction_lot_details_report  as tld 
                JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no
                JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                where P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                group by P.block_ulb_code
                  UNION
                  select 
                  P.block_ulb_code,
                 coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                 count(distinct(tl.lot_no)) as no_of_lots_created,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1 and tl.lot_status=6) as no_of_lots_response_received,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=0 and tl.ref_no>0),0) as success,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=2 and tl.ref_no>0),0) as failed
                 from ifms.transaction_lot_details  as tld 
                  JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                  group by P.block_ulb_code
            ) as U group by block_ulb_code
            ) as K ON ben.block_ulb_code=K.block_ulb_code
            ) as B ON A.block_code=B.block_ulb_code";
            $query2 = "select A.location_name,B.*
            from
            (
              select urban_body_code,urban_body_name||'-M' as location_name from m_urban_body
              where district_code=" . $district . "
              order by urban_body_name) as A 
            LEFT JOIN
            (
            select K.block_ulb_code,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            block_ulb_code
            from " . $main_table_name . " where  scheme_id=" . $scheme_code . " and next_level_role_id=0 " . $urban_code_condition . " " . $district_condition . "
            group by block_ulb_code
            ) as ben LEFT JOIN
            (
              select block_ulb_code,
                sum(total_beneficiary_under_lot) as total_beneficiary_under_lot,
                sum(no_of_lots_created) as no_of_lots_created,
                sum(no_of_lots_pushed_for_payments) as no_of_lots_pushed_for_payments,
                sum(no_of_lots_response_received) as no_of_lots_response_received,
                sum(success) as success,
                sum(failed) as failed
              from(
                select 
                P.block_ulb_code,
               coalesce(count(tld.id),0) as total_beneficiary_under_lot,
               count(distinct(tl.lot_no)) as no_of_lots_created,
               count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
               count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1 and tl.lot_status=6) as no_of_lots_response_received,
               coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=0 and tl.ref_no>0),0) as success,
               coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=2 and tl.ref_no>0),0) as failed
               from ifms.transaction_lot_details_report  as tld 
                JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no
                JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                where P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                group by P.block_ulb_code
                  UNION
                  select 
                  P.block_ulb_code,
                 coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                 count(distinct(tl.lot_no)) as no_of_lots_created,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1 and tl.lot_status=6) as no_of_lots_response_received,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=0 and tl.ref_no>0),0) as success,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=2 and tl.ref_no>0),0) as failed
                 from ifms.transaction_lot_details  as tld 
                  JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                  group by P.block_ulb_code
            ) as U group by block_ulb_code
            ) as K ON ben.block_ulb_code=K.block_ulb_code
            ) as B ON A.urban_body_code=B.block_ulb_code";
          } else if ($payment_mode == 'SBI') {
            $query1 = "select A.location_name,B.*
            from
            (
              select block_code,block_name||'-B' as location_name from m_block
              where district_code=" . $district . "
              order by block_name) as A 
             LEFT JOIN
            (
            select K.block_ulb_code,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            block_ulb_code
            from " . $main_table_name . " where  created_by_dist_code=" . $district . " and scheme_id=" . $scheme_code . " and next_level_role_id=0 " . $urban_code_condition . " 
            group by block_ulb_code
            ) as ben LEFT JOIN
            (
              select block_ulb_code,
                sum(total_beneficiary_under_lot) as total_beneficiary_under_lot,
                sum(no_of_lots_created) as no_of_lots_created,
                sum(no_of_lots_pushed_for_payments) as no_of_lots_pushed_for_payments,
                sum(no_of_lots_response_received) as no_of_lots_response_received,
                sum(success) as success,
                sum(failed) as failed
              from(
                  select 
                  P.block_ulb_code,
                  coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                        count(distinct(tl.lot_no)) as no_of_lots_created,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >0) as no_of_lots_pushed_for_payments,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >3) as no_of_lots_response_received,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as success,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as failed
                  from sbi.transaction_lot_details_report  as tld 
                  JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.created_by_dist_code=" . $district . " and P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " 
                  group by P.block_ulb_code
                  UNION
                  select 
                  P.block_ulb_code,
                  coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                        count(distinct(tl.lot_no)) as no_of_lots_created,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >0) as no_of_lots_pushed_for_payments,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >3) as no_of_lots_response_received,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as success,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as failed
                  from sbi.transaction_lot_details  as tld 
                  JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.created_by_dist_code=" . $district . " and P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . " 
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " 
                  group by P.block_ulb_code
            ) as U group by block_ulb_code
            ) as K ON ben.block_ulb_code=K.block_ulb_code
            ) as B ON A.block_code=B.block_ulb_code";
            $query2 = "select A.location_name,B.*
            from
            (
              select urban_body_code,urban_body_name||'-M' as location_name from m_urban_body
              where district_code=" . $district . "
              order by urban_body_name) as A 
             LEFT JOIN
            (
            select K.block_ulb_code,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            block_ulb_code
            from " . $main_table_name . " where  created_by_dist_code=" . $district . " and scheme_id=" . $scheme_code . " and next_level_role_id=0 " . $urban_code_condition . " 
            group by block_ulb_code
            ) as ben LEFT JOIN
            (
              select block_ulb_code,
                sum(total_beneficiary_under_lot) as total_beneficiary_under_lot,
                sum(no_of_lots_created) as no_of_lots_created,
                sum(no_of_lots_pushed_for_payments) as no_of_lots_pushed_for_payments,
                sum(no_of_lots_response_received) as no_of_lots_response_received,
                sum(success) as success,
                sum(failed) as failed
              from(
                  select 
                  P.block_ulb_code,
                  coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                        count(distinct(tl.lot_no)) as no_of_lots_created,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >0) as no_of_lots_pushed_for_payments,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >3) as no_of_lots_response_received,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as success,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as failed
                  from sbi.transaction_lot_details_report  as tld 
                  JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.created_by_dist_code=" . $district . " and P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " 
                  group by P.block_ulb_code
                  UNION
                  select 
                  P.block_ulb_code,
                  coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                        count(distinct(tl.lot_no)) as no_of_lots_created,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >0) as no_of_lots_pushed_for_payments,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >3) as no_of_lots_response_received,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as success,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as failed
                  from sbi.transaction_lot_details  as tld 
                  JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.created_by_dist_code=" . $district . " and P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . " 
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " 
                  group by P.block_ulb_code
            ) as U group by block_ulb_code
            ) as K ON ben.block_ulb_code=K.block_ulb_code
            ) as B ON A.urban_body_code=B.block_ulb_code";
          }
          $data_part1 = DB::connection('pgsql_mis')->select($query1);
          $data_part2 = DB::connection('pgsql_mis')->select($query2);
          $data = array_merge($data, $data_part1);
          $data = array_merge($data, $data_part2);
          $heading_msg = 'Block/Munc Wise Monthly Payment Status (' . $payment_mode . ') ' . $user_msg;
          $column = "Block/Munc";
        } else if ($blockwise == 1) {
          if ($payment_mode == 'IFMS') {
            $query = "select A.location_name,B.*
            from
            (
              select block_code,block_name||'-B' as location_name from m_block
              where district_code=" . $district . "
              order by block_name) as A 
            LEFT JOIN
            (
            select K.block_ulb_code,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            block_ulb_code
            from " . $main_table_name . " where  scheme_id=" . $scheme_code . " and next_level_role_id=0 " . $urban_code_condition . " " . $district_condition . "
            group by block_ulb_code
            ) as ben LEFT JOIN
            (
              select block_ulb_code,
                sum(total_beneficiary_under_lot) as total_beneficiary_under_lot,
                sum(no_of_lots_created) as no_of_lots_created,
                sum(no_of_lots_pushed_for_payments) as no_of_lots_pushed_for_payments,
                sum(no_of_lots_response_received) as no_of_lots_response_received,
                sum(success) as success,
                sum(failed) as failed
              from(
                select 
                P.block_ulb_code,
               coalesce(count(tld.id),0) as total_beneficiary_under_lot,
               count(distinct(tl.lot_no)) as no_of_lots_created,
               count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
               count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1 and tl.lot_status=6) as no_of_lots_response_received,
               coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=0 and tl.ref_no>0),0) as success,
               coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=2 and tl.ref_no>0),0) as failed
               from ifms.transaction_lot_details_report  as tld 
                JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no
                JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                where P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                group by P.block_ulb_code
                  UNION
                  select 
                  P.block_ulb_code,
                 coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                 count(distinct(tl.lot_no)) as no_of_lots_created,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1 and tl.lot_status=6) as no_of_lots_response_received,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=0 and tl.ref_no>0),0) as success,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=2 and tl.ref_no>0),0) as failed
                 from ifms.transaction_lot_details  as tld 
                  JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                  group by P.block_ulb_code
            ) as U group by block_ulb_code
            ) as K ON ben.block_ulb_code=K.block_ulb_code
            ) as B ON A.block_code=B.block_ulb_code";
          } else if ($payment_mode == 'SBI') {
            $query = "select A.location_name,B.*
            from
            (
              select block_code,block_name||'-B' as location_name from m_block
              where district_code=" . $district . "
              order by block_name) as A 
             LEFT JOIN
            (
            select K.block_ulb_code,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            block_ulb_code
            from " . $main_table_name . " where  created_by_dist_code=" . $district . " and scheme_id=" . $scheme_code . " and next_level_role_id=0 " . $urban_code_condition . " 
            group by block_ulb_code
            ) as ben LEFT JOIN
            (
              select block_ulb_code,
                sum(total_beneficiary_under_lot) as total_beneficiary_under_lot,
                sum(no_of_lots_created) as no_of_lots_created,
                sum(no_of_lots_pushed_for_payments) as no_of_lots_pushed_for_payments,
                sum(no_of_lots_response_received) as no_of_lots_response_received,
                sum(success) as success,
                sum(failed) as failed
              from(
                  select 
                  P.block_ulb_code,
                  coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                        count(distinct(tl.lot_no)) as no_of_lots_created,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >0) as no_of_lots_pushed_for_payments,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >3) as no_of_lots_response_received,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as success,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as failed
                  from sbi.transaction_lot_details_report  as tld 
                  JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.created_by_dist_code=" . $district . " and P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " 
                  group by P.block_ulb_code
                  UNION
                  select 
                  P.block_ulb_code,
                  coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                        count(distinct(tl.lot_no)) as no_of_lots_created,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >0) as no_of_lots_pushed_for_payments,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >3) as no_of_lots_response_received,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as success,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as failed
                  from sbi.transaction_lot_details  as tld 
                  JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.created_by_dist_code=" . $district . " and P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . " 
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " 
                  group by P.block_ulb_code
            ) as U group by block_ulb_code
            ) as K ON ben.block_ulb_code=K.block_ulb_code
            ) as B ON A.block_code=B.block_ulb_code";
          }
          $data_part = DB::connection('pgsql_mis')->select($query);
          $data = array_merge($data, $data_part);
          $heading_msg = 'Block Wise Monthly Payment Status (' . $payment_mode . ') ' . $user_msg;
          $column = "Block";
        } else if ($muncwise == 1) {
          if ($payment_mode == 'IFMS') {
            $query = "select A.location_name,B.*
            from
            (
              select urban_body_code,urban_body_name||'-M' as location_name from m_urban_body
              where district_code=" . $district . "
              order by urban_body_name) as A 
            LEFT JOIN
            (
            select K.block_ulb_code,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            block_ulb_code
            from " . $main_table_name . " where  scheme_id=" . $scheme_code . " and next_level_role_id=0 " . $urban_code_condition . " " . $district_condition . "
            group by block_ulb_code
            ) as ben LEFT JOIN
            (
              select block_ulb_code,
                sum(total_beneficiary_under_lot) as total_beneficiary_under_lot,
                sum(no_of_lots_created) as no_of_lots_created,
                sum(no_of_lots_pushed_for_payments) as no_of_lots_pushed_for_payments,
                sum(no_of_lots_response_received) as no_of_lots_response_received,
                sum(success) as success,
                sum(failed) as failed
              from(
                select 
                P.block_ulb_code,
               coalesce(count(tld.id),0) as total_beneficiary_under_lot,
               count(distinct(tl.lot_no)) as no_of_lots_created,
               count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
               count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1 and tl.lot_status=6) as no_of_lots_response_received,
               coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=0 and tl.ref_no>0),0) as success,
               coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=2 and tl.ref_no>0),0) as failed
               from ifms.transaction_lot_details_report  as tld 
                JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no
                JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                where P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                group by P.block_ulb_code
                  UNION
                  select 
                  P.block_ulb_code,
                 coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                 count(distinct(tl.lot_no)) as no_of_lots_created,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1) as no_of_lots_pushed_for_payments,
                 count(distinct(tl.lot_no)) FILTER(WHERE tl.push_to_ifms_status=1 and tl.lot_status=6) as no_of_lots_response_received,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=0 and tl.ref_no>0),0) as success,
                 coalesce(count(tld.id) FILTER(WHERE tld.wrongdata_flag=2 and tl.ref_no>0),0) as failed
                 from ifms.transaction_lot_details  as tld 
                  JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " " . $district_condition . "
                  group by P.block_ulb_code
            ) as U group by block_ulb_code
            ) as K ON ben.block_ulb_code=K.block_ulb_code
            ) as B ON A.urban_body_code=B.block_ulb_code";
          } else if ($payment_mode == 'SBI') {
            $query = "select A.location_name,B.*
            from
            (
              select urban_body_code,urban_body_name||'-M' as location_name from m_urban_body
              where district_code=" . $district . "
              order by urban_body_name) as A 
             LEFT JOIN
            (
            select K.block_ulb_code,
            ben.total_ben,
            K.total_beneficiary_under_lot,
            ben.total_ben-(K.total_beneficiary_under_lot) as pending_beneficiary_yet_to_be_lotted,
            K.no_of_lots_created,
            K.no_of_lots_pushed_for_payments,
            K.no_of_lots_created-K.no_of_lots_pushed_for_payments as no_lots_yet_to_be_pushed,
            K.no_of_lots_response_received,
            K.no_of_lots_pushed_for_payments-K.no_of_lots_response_received as no_lots_yet_to_be_response_received,
            K.success as successfull_payments,
            K.failed as failed_payments,
            ben.total_ben-(K.success+K.failed) as pending_beneficiary
            from
            (
            select coalesce(count(id) FILTER(WHERE created_at<='" . $first_date_month . "'),0)
            as total_ben,
            block_ulb_code
            from " . $main_table_name . " where  created_by_dist_code=" . $district . " and scheme_id=" . $scheme_code . " and next_level_role_id=0 " . $urban_code_condition . " 
            group by block_ulb_code
            ) as ben LEFT JOIN
            (
              select block_ulb_code,
                sum(total_beneficiary_under_lot) as total_beneficiary_under_lot,
                sum(no_of_lots_created) as no_of_lots_created,
                sum(no_of_lots_pushed_for_payments) as no_of_lots_pushed_for_payments,
                sum(no_of_lots_response_received) as no_of_lots_response_received,
                sum(success) as success,
                sum(failed) as failed
              from(
                  select 
                  P.block_ulb_code,
                  coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                        count(distinct(tl.lot_no)) as no_of_lots_created,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >0) as no_of_lots_pushed_for_payments,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >3) as no_of_lots_response_received,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as success,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as failed
                  from sbi.transaction_lot_details_report  as tld 
                  JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.created_by_dist_code=" . $district . " and P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . "
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " 
                  group by P.block_ulb_code
                  UNION
                  select 
                  P.block_ulb_code,
                  coalesce(count(tld.id),0) as total_beneficiary_under_lot,
                        count(distinct(tl.lot_no)) as no_of_lots_created,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >0) as no_of_lots_pushed_for_payments,
                        count(distinct(tl.lot_no)) FILTER(WHERE tl.lot_status >3) as no_of_lots_response_received,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as success,
                        coalesce(count(tl.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as failed
                  from sbi.transaction_lot_details  as tld 
                  JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no
                  JOIN " . $main_table_name . " as P ON P.id=tld.pension_id
                  where P.created_by_dist_code=" . $district . " and P.scheme_id=" . $scheme_code . " and tld.scheme_id=" . $scheme_code . " 
                  and tl.lot_year= '" . $lot_year . "' and tl.lot_month= '" . $lot_month . "' " . $urban_code_condition . " 
                  group by P.block_ulb_code
            ) as U group by block_ulb_code
            ) as K ON ben.block_ulb_code=K.block_ulb_code
            ) as B ON A.urban_body_code=B.block_ulb_code";
          }
          $data_part = DB::connection('pgsql_mis')->select($query);
          $data = array_merge($data, $data_part);
          $heading_msg = 'Municipality Wise Monthly Payment Status (' . $payment_mode . ') ' . $user_msg;
          $column = "Municipality";
        }
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
      'lot_yes' => $lot_yes,
      'heading_msg' => $heading_msg
    ]);
  }
  function getschemeonPaymentMode(Request $request)
  {

    $payment_mode = $request->payment_mode;
    $rules = [
      'payment_mode' => 'required|in:IFMS,SBI'
    ];
    $data = array();
    $attributes = array();
    $messages = array();
    $attributes['payment_mode'] = 'Select Payment Mode';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $return_status = 1;
      if ($payment_mode == 'IFMS')
        $data = array(1, 5, 6, 7, 19);
      elseif ($payment_mode == 'SBI')
        $data = array(2, 3, 8, 9, 10, 11, 17);
      $return_msg = '';
    } else {
      $return_status = 0;
      $return_msg = $validator->errors()->all();
    }
    return response()->json([
      'return_status' => $return_status,
      'return_msg' => $return_msg,
      'row_data' => $data
    ]);
  }
}
