<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\BeneficiaryPensions;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\Scheme;
use Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\District;
use App\SubDistrict;
use App\Taluka;
use App\UrbanBody;
use App\DrillDown_report;
use File;
use DateTime;
use DateTimeZone;
use App\Helpers\AuthChecker;

class DrillDownReport extends Controller
{
  public function __construct()
  {
    //$this->middleware('auth');
  }
  public function district_consol_report(Request $request)
  {
    $this->middleware('auth');
    $fill_value_arr = array();
    $inValid = 0;
    $inValidMsg = '';
    $isSubmitted = 0;
    $heading_msg = '';
    $last_modified = '';
    $searchResult = collect([]);
    $errors = array();
    $c_time = Carbon::now();
    $payment_mode = $request->payment_mode;
    if ($payment_mode != 'IFMS' && $payment_mode != 'SBI') {
      return redirect('/')->with('error', 'Payment Mode Not Valid');
    }
    $year = $c_time->year;
    if ($c_time->month == 1 || $c_time->month == 2 || $c_time->month == 3) {
      $first_part = $year - 1;
      $second_part = $year;
    } else {
      $first_part = $year;
      $second_part = $year + 1;
    }
    $select_year = $first_part . '-' . $second_part;
    $monthName = $c_time->format('F');
    $fill_value_arr['fin_year'] = $select_year;
    $fill_value_arr['month'] = $monthName;
    $fill_value_arr['payment_option'] = 1;
    $user_id = AuthChecker::getUserId();
    if ($payment_mode == 'IFMS')
      $payment_scheme_in = array(1, 5, 6, 7, 19);
    elseif ($payment_mode == 'SBI')
      $payment_scheme_in = array(2, 3, 8, 9, 10, 11, 17);
    $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in ( " . implode(',', $payment_scheme_in) . ") and id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
    if (isset($request->submit)) {
      $isSubmitted = 1;
      $scheme_id = $request->scheme_id;
      $year = $request->fin_year;
      $month = $request->month;
      $payment_option = $request->payment_option;
      $fill_value_arr['scheme_id'] = $scheme_id;
      $fill_value_arr['fin_year'] = $year;
      $fill_value_arr['month'] = $month;
      $fill_value_arr['payment_option'] = $payment_option;
      $rules = [
        'fin_year' => 'required',
        'month' => 'required',
        'payment_mode' => 'required',
        'payment_option' => 'required'
      ];
      $attributes = array();
      $messages = array();
      $attributes['fin_year'] = 'Financial Year';
      $attributes['month'] = 'Month';
      $attributes['payment_mode'] = 'Payment Mode';
      $attributes['payment_option'] = 'Payment Option';
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $inValid = 0;
        $designation_id = Auth::user()->designation_id;
        $heading_msg = 'Districtwise Drill Down Report(' . $payment_mode . ') ';
        if (!is_null($scheme_id)) {
          $file_3_part = $scheme_id;
          $duty_arr =  Configduty::select('district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')->where('user_id', '=', $user_id)->where('scheme_id', '=', $scheme_id)->first();
        } else {
          $file_3_part = $user_id;
          $duty_arr =  Configduty::select('district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')->where('user_id', '=', $user_id)->first();
        }
        if ($designation_id == 'HOD' || $designation_id == 'DDO' || $designation_id == 'CORP' || $designation_id == 'Admin') {
          $file_1_part = '1';
          $file_2_part =  $duty_arr->mapping_level;
        } else if ($designation_id == 'Approver') {
          $file_1_part = $duty_arr->district_code;
          $file_2_part =  $duty_arr->mapping_level;
        } else if ($designation_id == 'Verifier' || $designation_id == 'Operator') {
          if ($duty_arr->is_urban == 1) {
            $file_1_part = $duty_arr->district_code . '-' . $duty_arr->urban_body_code;
            $file_2_part =  $duty_arr->mapping_level;
          } else {
            $file_1_part = $duty_arr->district_code . '-' . $duty_arr->taluka_code;
            $file_2_part =  $duty_arr->mapping_level;
          }
        }
        if (!is_null($year)) {
          $file_4_part = $year;
        }
        if (!is_null($month)) {
          $file_5_part = $month;
        }
        if (!is_null($payment_option)) {
          $file_6_part = $payment_option;
        }
        $file_full_name = $file_1_part . '-' . $file_2_part . '-' . $file_3_part . '-' . $file_4_part . '-' . $file_5_part . '-' . $file_6_part . '-' . $payment_mode;
        $tld_table = 'transaction_lot_details';
        if ($payment_option == 2) {
          $tld_table = 'transaction_lot_details_report';
        }
        $explode_year = explode('-', $year);
        $pension_search_year = $explode_year[0];
        if ($month == 'January' || $month == 'February' || $month == 'March') {
          $pension_search_year = $pension_search_year + 1;
        }
        $file_exist = 0;
        if ($payment_option == 2 && Storage::exists('drilldown-report/' . $file_full_name . '.json')) {
          $path = storage_path() . "/app/drilldown-report/${file_full_name}.json";
          $data = json_decode(file_get_contents($path), true);
          $data = collect($data)->map(function ($objdata) {
            return (object) $objdata;
          });
          $file_exist = 1;
          $lastmodified = File::lastModified($path);
          $lastmodified = DateTime::createFromFormat("U", $lastmodified);
          $last_modified = $lastmodified->setTimezone(new DateTimeZone('Asia/Kolkata'));
          $last_modified = $last_modified->format('F d Y H:i:s');
        } else {
          $c_time = Carbon::now();
          $last_modified = $c_time->format("F d Y H:i:s");
        }
        //dd($last_modified);
        $parameter = array();
        $table_name = 'pension.beneficiary';
        if (!is_null($scheme_id)) {
          $schemes_arr =  Scheme::select('id', 'short_code', 'scheme_name')->where('id', '=', $scheme_id)->first();
          $parameter['scheme_id'] = $scheme_id;
          $schema_name =  $schemes_arr->short_code;
          $heading_msg = $heading_msg . ' Scheme:' . $schemes_arr->scheme_name;
          //dd($schema_name);
          if (empty($schema_name)) {

            $schema_name = 'pension';
            $table_name =  $schema_name . '.beneficiary';
          }
        } else {

          //$heading_msg = $heading_msg . ': Scheme-' . $schemes_arr->scheme_name;
          $schemes_in_arr =  Configduty::select('scheme_id')->where('user_id', '=', $user_id)->whereIn('scheme_id', $payment_scheme_in)->get();
          $schemes_in = array();
          // dd($schemes_in_arr);
          foreach ($schemes_in_arr  as $schm) {
            array_push($schemes_in, $schm->scheme_id);
          }
          $heading_msg = $heading_msg . ' Schemes:All';
        }
        if (!is_null($year)) {
          $parameter['lot_year'] = $year;
          $heading_msg = $heading_msg . ',Financial Year:' . $year;
        }
        if (!is_null($month)) {
          $parameter['lot_month'] = $month;
          $heading_msg = $heading_msg . ',Month:' . $month;
        }
        if ($payment_option == 2) {
          $heading_msg = $heading_msg . ',Payment Option:' . 'Old Payments';
        } else {
          $heading_msg = $heading_msg . ',Payment Option:' . 'Current Payments';
        }

        if ($file_exist == 0) {
          if ($payment_mode == 'IFMS') {
            $query = 'select d.district_name,d.district_code ,
          coalesce(count( distinct b.id) FILTER(WHERE date_part(\'year\',b.created_at)=' . $pension_search_year . ' and trim(to_char(b.created_at,\'Month\'))= :lot_month) ,0) as applied,
          coalesce(count( distinct b.id) FILTER(WHERE date_part(\'year\',b.created_at)=' . $pension_search_year . ' and b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as verified,
          coalesce(count( distinct b.id) FILTER(WHERE date_part(\'year\',b.created_at)=' . $pension_search_year . ' and b.next_level_role_id = 0 and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as approved,
          coalesce(count( distinct b.id) FILTER(WHERE b.is_rejected=1 and date_part(\'year\',b.created_at)=' . $pension_search_year . ' and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as rejected,
          coalesce(count( distinct b.id) FILTER(WHERE l.wrongdata_flag in (0,1,2) and l.push_to_ifms_status=1),0) as pushed_ifms,
          coalesce(count( distinct b.id) FILTER(WHERE l.wrongdata_flag in (1) and (l.ref_no is not null or l.ref_no <> -1)),0) as ifms_returned,
          coalesce(count( b.id) FILTER(WHERE l.wrongdata_flag in (0,2) and l.ref_no>0 and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as fresh_app_mandate_generated,
	        coalesce(count( b.id) FILTER(WHERE l.wrongdata_flag in (0,2) and l.ref_no>0 and EXTRACT(MONTH FROM b.created_at)< EXTRACT(MONTH FROM to_date(:lot_month, \'Mon\'))),0) as old_app_mandate_generated,
          coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0,2) and l.ref_no>0),0) as amount_booked,
          coalesce(count( b.id) FILTER(WHERE l.wrongdata_flag in (2) and l.ref_no>0),0) as rbi_failed,
          coalesce(count( b.id) FILTER(WHERE l.wrongdata_flag in (0) and l.ref_no>0),0) as rbi_success,
          coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0) and l.ref_no>0),0) as amount_paid
                  FROM m_district d 
                  left join ' . $table_name . ' b on d.district_code = b.dist_code';
            if (!is_null($scheme_id)) {
              $query = $query . ' and b.scheme_id = :scheme_id';
            } else {
              $query = $query . ' and b.scheme_id IN (' . implode(',', $schemes_in) . ')';
            }
            $query = $query . ' left join 
                          (select e.pension_id,e.scheme_id,lm.ref_no, e.wrongdata_flag, e.amount, lm.push_to_ifms_status 
                          from ifms.' . $tld_table . ' e left join lot_master lm 
                          on e.drn_part = lm.lot_no';
            if (!is_null($scheme_id)) {
              $query = $query . ' and lm.scheme_id = :scheme_id';
            } else {
              $query = $query . ' and lm.scheme_id IN (' . implode(',', $schemes_in) . ')';
            }
            if (!is_null($year)) {
              $query = $query . ' and lm.lot_year= :lot_year';
            }
            if (!is_null($month)) {
              $query = $query . ' and lm.lot_month= :lot_month';
            }
            $query = $query . ' and lm.push_to_ifms_status=1) l
                          on b.id = l.pension_id and b.scheme_id = l.scheme_id';

            $query = $query . ' group by d.district_name,d.district_code order by d.district_name';
          } else if ($payment_mode == 'SBI') {
            $query = 'select d.district_name, d.district_code,
          coalesce(count( b.id) FILTER(WHERE date_part(\'year\',b.created_at)=' . $pension_search_year . ' and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as applied,
          coalesce(count( b.id) FILTER(WHERE date_part(\'year\',b.created_at)=' . $pension_search_year . ' and b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as verified,
          coalesce(count( b.id) FILTER(WHERE date_part(\'year\',b.created_at)=' . $pension_search_year . ' and b.next_level_role_id = 0 and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as approved,
          coalesce(count( b.id) FILTER(WHERE l.lot_status <=5 and date_part(\'year\',b.created_at)=' . $pension_search_year . ' and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as current_month_pushed_sbi,
	  coalesce(count( b.id) FILTER(WHERE l.lot_status <=5 and EXTRACT(MONTH FROM b.created_at)< EXTRACT(MONTH FROM to_date(:lot_month, \'Mon\'))),0) as past_months_pushed_sbi,
	  coalesce(count( b.id) FILTER(WHERE  l.lot_status<4),0) as sbi_under_process,
          coalesce(count( b.id) FILTER(WHERE l.status_code=\'S00\' and l.lot_status>3),0) as sbi_success,
          coalesce(count( b.id) FILTER(WHERE l.status_code!=\'S00\' and l.status_code is not null),0) as sbi_failed,
          coalesce(sum(l.credit_amount/100) FILTER(WHERE l.status_code=\'S00\' and l.lot_status>3 ),0) as amount_billed
                  FROM m_district d 
                  left join ' . $table_name . ' b on d.district_code = b.dist_code';
            if (!is_null($scheme_id)) {
              $query = $query . ' and b.scheme_id = :scheme_id';
            } else {
              $query = $query . ' and b.scheme_id IN (' . implode(',', $schemes_in) . ')';
            }
            $query = $query . ' left join 
                          (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code 
                          from sbi.' . $tld_table . ' tld right join sbi.transaction_lot tl
                          on tld.lot_no = tl.lot_no ';
            if (!is_null($scheme_id)) {
              $query = $query . ' and tl.scheme_id = :scheme_id';
            } else {
              $query = $query . ' and tl.scheme_id IN (' . implode(',', $schemes_in) . ')';
            }
            if (!is_null($year)) {
              $query = $query . ' and tl.lot_year= :lot_year';
            }
            if (!is_null($month)) {
              $query = $query . ' and tl.lot_month= :lot_month';
            }
            $query = $query . ') l
                          on b.id = l.pension_id and b.scheme_id = l.scheme_id';

            $query = $query . ' group by d.district_name,d.district_code order by d.district_name';
          }
          $data = DB::connection('pgsql_mis')->select($query, $parameter);
          if ($payment_option == 2) {
            if (!Storage::exists($file_full_name . '.json')) {
              Storage::put('drilldown-report/' . $file_full_name . '.json',  json_encode($data));
            }
          }
        }

        $searchResult = $data;
      } else {
        $inValid = 1;
        $errors = $validator->errors()->all();
        //dd($errors);
      }
    }
    return view(
      'Drilldown.district_report',
      [
        'payment_mode' => $payment_mode,
        'isSubmitted' => $isSubmitted,
        'schemes' => $schemes,
        'fill_value_arr' => $fill_value_arr,
        'errors' => $errors,
        'searchResult' => $searchResult,
        'inValid' => $inValid,
        'inValidMsg' => $inValidMsg,
        'heading_msg' => $heading_msg,
        'last_modified' => $last_modified
      ]
    );
  }
  public function block_consol_report(Request $request)
  {
    $this->middleware('auth');
    $district_list = Cache::rememberForever('master_districts', function () {
      return District::select(
        'id',
        'district_code',
        'district_name',
        'rch_district_code',
        'is_revenue_district',
        'state_code',
        'district_status'
      )->get();
    });
    $fill_value_arr = array();
    $inValid = 0;
    $inValidMsg = '';
    $isSubmitted = 0;
    $heading_msg = '';
    $searchResult = collect([]);
    $errors = array();
    $c_time = Carbon::now();
    $payment_mode = $request->payment_mode;
    if ($payment_mode != 'IFMS' && $payment_mode != 'SBI') {
      return redirect('/')->with('error', 'Payment Mode Not Valid');
    }
    $user_id = AuthChecker::getUserId();
    $designation_id = Auth::user()->designation_id;
    $scheme_id = $request->scheme_id;
    if (!is_null($scheme_id)) {
      $file_3_part = $scheme_id;
      $duty_arr =  Configduty::select('district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')->where('user_id', '=', $user_id)->where('scheme_id', '=', $scheme_id)->first();
    } else {
      $file_3_part = $user_id;
      $duty_arr =  Configduty::select('district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')->where('user_id', '=', $user_id)->first();
    }
    if ($designation_id == 'HOD' || $designation_id == 'DDO' || $designation_id == 'CORP' || $designation_id == 'Admin') {
      $district_code = $request->district_code;
    } else {
      $district_code =  $duty_arr->district_code;
    }
    $district_list = $district_list->where('district_code', $district_code)->first();
    $district_name = trim($district_list->district_name);
    $year = $c_time->year;
    if ($c_time->month == 1 || $c_time->month == 2 || $c_time->month == 3) {
      $first_part = $year - 1;
      $second_part = $year;
    } else {
      $first_part = $year;
      $second_part = $year + 1;
    }
    $select_year = $first_part . '-' . $second_part;
    $monthName = $c_time->format('F');
    $fill_value_arr['fin_year'] = $select_year;
    $fill_value_arr['month'] = $monthName;
    $fill_value_arr['payment_option'] = 1;
    $user_id = AuthChecker::getUserId();
    if ($payment_mode == 'IFMS')
      $payment_scheme_in = array(1, 5, 6, 7, 19);
    elseif ($payment_mode == 'SBI')
      $payment_scheme_in = array(2, 3, 8, 9, 10, 11, 17);
    $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in ( " . implode(',', $payment_scheme_in) . ") and id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
    if (count($schemes) == 0) {
      return redirect('/')->with('error', 'Not Applicable for any scheme for the payment mode ' . $payment_mode);
    }
    $rural_urban = $request->rural_urban;
    $isSubmitted = 1;
    $scheme_id = $request->scheme_id;
    $year = $request->fin_year;
    $month = $request->month;
    $payment_option = $request->payment_option;
    $district_code = $request->district_code;
    $fill_value_arr['scheme_id'] = $scheme_id;
    $fill_value_arr['fin_year'] = $year;
    $fill_value_arr['month'] = $month;
    $fill_value_arr['payment_option'] = $payment_option;
    $fill_value_arr['district_code'] = $district_code;
    $fill_value_arr['rural_urban'] = $rural_urban;
    $rules = [
      'fin_year' => 'required',
      'month' => 'required',
      'payment_mode' => 'required',
      'payment_option' => 'required'
    ];
    $attributes = array();
    $messages = array();
    $attributes['fin_year'] = 'Financial Year';
    $attributes['month'] = 'Month';
    $attributes['payment_mode'] = 'Payment Mode';
    $attributes['payment_option'] = 'Payment Option';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $inValid = 0;
      $designation_id = Auth::user()->designation_id;
      $heading_msg = 'Block/Municipality Wise Drill Down Report(' . $payment_mode . ') ';
      if (!is_null($scheme_id)) {
        $file_3_part = $scheme_id;
        $duty_arr =  Configduty::select('district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')->where('user_id', '=', $user_id)->where('scheme_id', '=', $scheme_id)->first();
      } else {
        $file_3_part = $user_id;
        $duty_arr =  Configduty::select('district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')->where('user_id', '=', $user_id)->first();
      }

      $file_1_part = $district_code;
      $file_2_part =  'district';
      if (!is_null($rural_urban)) {
        $rural_urban_text = $rural_urban;
      } else {
        $rural_urban_text = 'all';
      }
      $file_8_part =  $rural_urban_text;
      if (!is_null($year)) {
        $file_4_part = $year;
      }
      if (!is_null($month)) {
        $file_5_part = $month;
      }
      if (!is_null($payment_option)) {
        $file_6_part = $payment_option;
      }
      $file_full_name = $file_1_part . '-' . $file_2_part . '-' .  $file_8_part . '-' . $file_3_part . '-' . $file_4_part . '-' . $file_5_part . '-' . $file_6_part . '-' . $payment_mode;
      $tld_table = 'transaction_lot_details';
      if ($payment_option == 2) {
        $tld_table = 'transaction_lot_details_report';
      }
      $explode_year = explode('-', $year);
      $pension_search_year = $explode_year[0];
      if ($month == 'January' || $month == 'February' || $month == 'March') {
        $pension_search_year = $pension_search_year + 1;
      }
      $file_exist = 0;
      if ($payment_option == 2 && Storage::exists('drilldown-report/' . $file_full_name . '.json')) {
        $path = storage_path() . "/app/drilldown-report/${file_full_name}.json";
        $data = json_decode(file_get_contents($path), true);
        $data = collect($data)->map(function ($objdata) {
          return (object) $objdata;
        });
        $file_exist = 1;
        $lastmodified = File::lastModified($path);
        $lastmodified = DateTime::createFromFormat("U", $lastmodified);
        $last_modified = $lastmodified->setTimezone(new DateTimeZone('Asia/Kolkata'));
        $last_modified = $last_modified->format('F d Y H:i:s');
      } else {
        $c_time = Carbon::now();
        $last_modified = $c_time->format("F d Y H:i:s");
      }
      // dd('not exists');
      $parameter = array();
      $table_name = 'pension.beneficiary';
      if (!is_null($scheme_id)) {
        $schemes_arr =  Scheme::select('id', 'short_code', 'scheme_name')->where('id', '=', $scheme_id)->first();
        $parameter['scheme_id'] = $scheme_id;
        $schema_name =  $schemes_arr->short_code;
        $heading_msg = $heading_msg . ' Scheme:' . $schemes_arr->scheme_name;
        //dd($schema_name);
        if (empty($schema_name)) {
          //$heading_msg = $heading_msg . ' Scheme:' . $schemes_arr->scheme_name;
          $schema_name = 'pension';
          $table_name =  $schema_name . '.beneficiary';
        }
      } else {

        //$heading_msg = $heading_msg . ': Scheme-' . $schemes_arr->scheme_name;
        $schemes_in_arr =  Configduty::select('scheme_id')->where('user_id', '=', $user_id)->whereIn('scheme_id', $payment_scheme_in)->get();
        $schemes_in = array();
        // dd($schemes_in_arr);
        foreach ($schemes_in_arr  as $schm) {
          array_push($schemes_in, $schm->scheme_id);
        }
        $heading_msg = $heading_msg . ' Schemes:All';
      }
      if (!is_null($year)) {
        $parameter['lot_year'] = $year;
        $heading_msg = $heading_msg . ',Financial Year:' . $year;
      }
      if (!is_null($month)) {
        $parameter['lot_month'] = $month;
        $heading_msg = $heading_msg . ',Month:' . $month;
      }
      if ($payment_option == 2) {
        $heading_msg = $heading_msg . ',Payment Option:' . 'Old Payments';
      } else {
        $heading_msg = $heading_msg . ',Payment Option:' . 'Current Payments';
      }

      if ($file_exist == 0) {
        $filter = array();
        $filter['dist_code'] = $district_code;
        if (!is_null($scheme_id)) {
          $filter['scheme_id'] = $scheme_id;
        }
        if (!empty($month)) {
          $filter['lot_month'] = $month;
        }
        if (!empty($year)) {
          $filter['lot_year'] = $year;
        }
        $data = array();
        if ($payment_mode == 'IFMS') {
          if ($rural_urban != 'Rural') {
            $fetcheddata = array();

            $query = "select block_ulb_name, 'Urban' as Level,
    coalesce(count(distinct b.id),0) as applied,
    coalesce(count(distinct b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as verified,
    coalesce(count(distinct b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
    coalesce(count(distinct b.id) FILTER(WHERE l.wrongdata_flag in (0,1,2)),0) as pushed_ifms,
    coalesce(count(distinct b.id) FILTER(WHERE l.wrongdata_flag in (0,2) and l.ref_no>0),0) as mandate_generated
    FROM (select * from " . $table_name . " where rural_urban_id=1";
            $query = $query . ' and dist_code= :dist_code';
            if (!is_null($scheme_id)) {
              $query = $query . ' and scheme_id = :scheme_id';
            } else {
              $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
            }
            $query = $query . ') b left join 
        (select e.pension_id,e.scheme_id,lm.ref_no, e.wrongdata_flag, e.amount 
        from ifms.' . $tld_table . ' e right join lot_master lm 
        on e.drn_part = lm.lot_no';
            if (!is_null($year)) {
              $query = $query . ' and lm.lot_year= :lot_year';
            }
            if (!is_null($month)) {
              $query = $query . ' and lm.lot_month= :lot_month AND lm.ref_no>0 ';
            }
            if (!is_null($scheme_id)) {
              $query = $query . ' and lm.scheme_id = :scheme_id';
            } else {
              $query = $query . ' and lm.scheme_id IN (' . implode(',', $schemes_in) . ')';
            }
            $query = $query . ') l
        on b.id = l.pension_id and b.scheme_id = l.scheme_id
        group by block_ulb_name order by block_ulb_name';
            $fetcheddata = DB::connection('pgsql_mis')->select($query, $filter);
            //return response()->json(['status'=>$query, 'filter' => $filter]);
            $data = array_merge($data, $fetcheddata);
            if ($rural_urban != 'Urban') {
              $fetcheddata = array();

              $query = "select block_ulb_name, 'Rural' as Level,
      coalesce(count(distinct b.id),0) as applied,
      coalesce(count(distinct b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as verified,
      coalesce(count(distinct b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
      coalesce(count(distinct b.id) FILTER(WHERE l.wrongdata_flag=0),0) as pushed_ifms,
      coalesce(count(distinct b.id) FILTER(WHERE l.wrongdata_flag=0 and l.ref_no>0),0) as mandate_generated
      FROM (select * from " . $table_name . " where rural_urban_id=2";
              $query = $query . ' and dist_code= :dist_code';

              if (!is_null($scheme_id)) {
                $query = $query . ' and scheme_id = :scheme_id';
              } else {
                $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
              }
              $query = $query . ') b left join 
          (select e.pension_id,e.scheme_id,lm.ref_no, e.wrongdata_flag, e.amount 
          from ifms.' . $tld_table . ' e right join lot_master lm 
          on e.drn_part = lm.lot_no';
              if (!is_null($year)) {
                $query = $query . ' and lm.lot_year= :lot_year';
              }
              if (!is_null($month)) {
                $query = $query . ' and lm.lot_month= :lot_month';
              }
              if (!is_null($scheme_id)) {
                $query = $query . ' and lm.scheme_id = :scheme_id';
              } else {
                $query = $query . ' and lm.scheme_id IN (' . implode(',', $schemes_in) . ')';
              }
              $query = $query . ') l
          on b.id = l.pension_id and b.scheme_id = l.scheme_id 
          group by block_ulb_name order by block_ulb_name';
              //return response()->json(['status'=>$query, 'filter' => $filter]);
              $fetcheddata = DB::connection('pgsql_mis')->select($query, $filter);
              $data = array_merge($data, $fetcheddata);
            }
            if ($rural_urban != 'Urban') {
              $fetcheddata = array();

              $query = "select block_ulb_name, 'Rural' as Level,
    coalesce(count(distinct b.id),0) as applied,
    coalesce(count(distinct b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as verified,
    coalesce(count(distinct b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
    coalesce(count(distinct b.id) FILTER(WHERE l.wrongdata_flag=0),0) as pushed_ifms,
    coalesce(count(distinct b.id) FILTER(WHERE l.wrongdata_flag=0 and l.ref_no>0),0) as mandate_generated
    FROM (select * from " . $table_name . " where rural_urban_id=2";
              $query = $query . ' and dist_code= :dist_code';

              if (!is_null($scheme_id)) {
                $query = $query . ' and scheme_id = :scheme_id';
              } else {
                $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
              }
              $query = $query . ') b left join 
        (select e.pension_id,e.scheme_id,lm.ref_no, e.wrongdata_flag, e.amount 
        from ifms.' . $tld_table . ' e right join lot_master lm 
        on e.drn_part = lm.lot_no';
              if (!is_null($year)) {
                $query = $query . ' and lm.lot_year= :lot_year';
              }
              if (!is_null($month)) {
                $query = $query . ' and lm.lot_month= :lot_month';
              }
              if (!is_null($scheme_id)) {
                $query = $query . ' and lm.scheme_id = :scheme_id';
              } else {
                $query = $query . ' and lm.scheme_id IN (' . implode(',', $schemes_in) . ')';
              }
              $query = $query . ') l
        on b.id = l.pension_id and b.scheme_id = l.scheme_id 
        group by block_ulb_name order by block_ulb_name';
              //return response()->json(['status'=>$query, 'filter' => $filter]);
              $fetcheddata = DB::connection('pgsql_mis')->select($query, $filter);
              $data = array_merge($data, $fetcheddata);
            }
          }
        } else if ($payment_mode == 'SBI') {
          if ($rural_urban != 'Rural') {
            $fetcheddata = array();

            $query = "select block_ulb_name, 'Urban' as Level,
    coalesce(count( b.id),0) as applied,
    coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null),0) as to_be_verified,
    coalesce(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as to_be_approved,
    coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
    coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)=" . $pension_search_year . " and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_applied,
    coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)=" . $pension_search_year . " and b.next_level_role_id is null and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_verified,
    coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)=" . $pension_search_year . " and b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_approved,
    coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)=" . $pension_search_year . " and b.next_level_role_id = 0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_approved,
    coalesce(count( b.id) FILTER(WHERE l.lot_status<=6),0) as pushed_sbi
    
    FROM (select * from " . $table_name . " where rural_urban_id=1";
            $query = $query . ' and dist_code= :dist_code';
            if (!is_null($scheme_id)) {
              $query = $query . ' and scheme_id = :scheme_id';
            } else {
              $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
            }
            $query = $query . ') b left join 
    (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code 
    from sbi.' . $tld_table . ' tld right join sbi.transaction_lot tl
    on tld.lot_no = tl.lot_no';
            if (!is_null($year)) {
              $query = $query . ' and tl.lot_year= :lot_year';
            }
            if (!is_null($month)) {
              $query = $query . ' and tl.lot_month= :lot_month  ';
            }
            if (!is_null($scheme_id)) {
              $query = $query . ' and tl.scheme_id = :scheme_id';
            } else {
              $query = $query . ' and tl.scheme_id IN (' . implode(',', $schemes_in) . ')';
            }
            $query = $query . ') l
        on b.id = l.pension_id and b.scheme_id = l.scheme_id 
        group by block_ulb_name order by block_ulb_name';
            $fetcheddata = DB::connection('pgsql_mis')->select($query, $filter);
            //return response()->json(['status'=>$query, 'filter' => $filter]);
            $data = array_merge($data, $fetcheddata);
          }
          if ($rural_urban != 'Urban') {
            $fetcheddata = array();

            $query = "select block_ulb_name, 'Rural' as Level,
    coalesce(count(distinct b.id),0) as applied,
    coalesce(count( b.id) FILTER(WHERE b.next_level_role_id is null),0) as to_be_verified,
    coalesce(count( b.id) FILTER(WHEREb.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as to_be_approved,
    coalesce(count( b.id) FILTER(WHERE b.next_level_role_id = 0),0) as approved,
    coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)=" . $pension_search_year . " and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_applied,
    coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)=" . $pension_search_year . " and b.next_level_role_id is null and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_verified,
    coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)=" . $pension_search_year . " and b.is_verified=1 and b.is_approved=0 and b.is_rejected=0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_to_be_approved,
    coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)=" . $pension_search_year . " and b.next_level_role_id = 0 and trim(to_char(b.created_at,'Month'))= :lot_month),0) as current_approved,
    coalesce(count( b.id) FILTER(WHERE l.lot_status<=6),0) as pushed_sbi
    FROM (select * from " . $table_name . " where rural_urban_id=2";
            $query = $query . ' and dist_code= :dist_code';

            if (!is_null($scheme_id)) {
              $query = $query . ' and scheme_id = :scheme_id';
            } else {
              $query = $query . ' and scheme_id IN (' . implode(',', $schemes_in) . ')';
            }
            $query = $query . ') b  left join 
    (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code 
    from sbi.transaction_lot_details tld right join sbi.transaction_lot tl
    on tld.lot_no = tl.lot_no';
            if (!is_null($year)) {
              $query = $query . ' and tl.lot_year= :lot_year';
            }
            if (!is_null($month)) {
              $query = $query . ' and tl.lot_month= :lot_month';
            }
            if (!is_null($scheme_id)) {
              $query = $query . ' and tl.scheme_id = :scheme_id';
            } else {
              $query = $query . ' and tl.scheme_id IN (' . implode(',', $schemes_in) . ')';
            }
            $query = $query . ') l
        on b.id = l.pension_id and b.scheme_id = l.scheme_id 
        group by block_ulb_name order by block_ulb_name';
            //return response()->json(['status'=>$query, 'filter' => $filter]);
            $fetcheddata = DB::connection('pgsql_mis')->select($query, $filter);
            $data = array_merge($data, $fetcheddata);
          }
        }

        if ($payment_option == 2) {
          if (!Storage::exists($file_full_name . '.json')) {
            Storage::put('drilldown-report/' . $file_full_name . '.json',  json_encode($data));
          }
        }
      }

      $searchResult = $data;
      //dd($searchResult);
    } else {
      $inValid = 1;
      $errors = $validator->errors()->all();
      //dd($errors);
    }

    return view(
      'Drilldown.block_report',
      [
        'payment_mode' => $payment_mode,
        'isSubmitted' => $isSubmitted,
        'schemes' => $schemes,
        'fill_value_arr' => $fill_value_arr,
        'errors' => $errors,
        'searchResult' => $searchResult,
        'inValid' => $inValid,
        'inValidMsg' => $inValidMsg,
        'heading_msg' => $heading_msg,
        'district_name' => $district_name,
        'district_code' => $district_code,
        'last_modified' => $last_modified
      ]
    );
  }
  public function cronJob()
  {
    $insert_arr = array();
    $district_list = Cache::rememberForever('master_districts', function () {
      return District::select(
        'id',
        'district_code',
        'district_name',
        'rch_district_code',
        'is_revenue_district',
        'state_code',
        'district_status'
      )->get();
    });
    $sdo_list = Cache::rememberForever('master_subdistricts', function () {
      return SubDistrict::select('id', 'district_code', 'sub_district_code', 'sub_district_name', 'sub_district_status')->get();
    });
    $urban_list = Cache::rememberForever('master_urbanbodies', function () {
      return UrbanBody::select('id', 'district_code', 'urban_body_code', 'urban_body_name', 'sub_district_code', 'urban_body_status')->get();
    });
    $block_list = Cache::rememberForever('master_blocks', function () {
      return Taluka::select(
        'district_code',
        'sub_division_code',
        'block_code',
        'block_name',
        'status',
        'district_id',
        'sub_division_id'
      )->get();
    });
    $mainquery = "select lot_no,lot_month,lot_year,scheme_id,payment_mode from lot_master where lot_no='000100'";
    $maindata = DB::connection('pgsql_mis')->select($mainquery);
    //dd($maindata);
    if (count($maindata) > 0) {
      foreach ($maindata as $item) {
        $lot_no = $item->lot_no;
        $scheme_id = $item->scheme_id;
        $year = $item->lot_year;
        $month = $item->lot_month;
        $payment_mode = $item->payment_mode;
        $schemes_arr =  Scheme::select('id', 'short_code', 'scheme_name')->where('id', '=', $scheme_id)->first();
        $parameter['scheme_id'] = $scheme_id;
        $schema_name =  $schemes_arr->short_code;
        //dd($schema_name);
        if (empty($schema_name))
          $schema_name = 'pension';
        $table_name =  $schema_name . '.beneficiary';
        $explode_year = explode('-', $year);
        $pension_search_year = $explode_year[0];
        if ($month == 'January' || $month == 'February' || $month == 'March') {
          $pension_search_year = $pension_search_year + 1;
        }
        if (strtoupper(trim($payment_mode)) == 'IFMS') {



          $query1 = "select main.total_application,
          main.total_approved,
          main.total_verified,
          main.total_rejected,
          main.total_uploaded_cur_month,
          main.total_verified_cur_month,
          main.total_approved_cur_month,
          main.total_rejected_cur_month,
          sub.total_lotted,
          sub.pushed_ifms,
          sub.ifms_returned,
          sub.fresh_app_mandate_generated,
          sub.old_app_mandate_generated,
          sub.amount_booked,
          sub.rbi_failed,
          sub.rbi_success,
          sub.amount_paid,
          main.block_ulb_code,
          main.rural_urban_id from
         (
         select coalesce(count(A.id),0) as total_application,
         coalesce(count(id) FILTER(WHERE A.next_level_role_id=0),0) as total_approved,
         coalesce(count(id) FILTER(WHERE A.is_verified=1 and A.is_approved=0 and A.is_rejected=0),0) as total_verified,
         coalesce(count(id) FILTER(WHERE A.is_rejected=1),0) as total_rejected,
         coalesce(count(id) FILTER(WHERE date_part('year',A.created_at)=" . $pension_search_year . " and trim(to_char(A.created_at,'Month'))= '" . $month . "') ,0) as total_uploaded_cur_month,
         coalesce(count(id) FILTER(WHERE A.is_verified=1 and A.is_approved=0 and A.is_rejected=0 and date_part('year',A.created_at)=" . $pension_search_year . " and trim(to_char(A.created_at,'Month'))=  '" . $month . "'),0) as total_verified_cur_month,
         coalesce(count(id) FILTER(WHERE A.is_verified=1 and A.is_approved=0 and A.is_rejected=0 and date_part('year',A.created_at)=" . $pension_search_year . " and trim(to_char(A.created_at,'Month'))=  '" . $month . "'),0) as total_approved_cur_month,
         coalesce(count(id) FILTER(WHERE A.is_rejected=1 and date_part('year',A.created_at)=" . $pension_search_year . " and trim(to_char(A.created_at,'Month'))=  '" . $month . "'),0) as total_rejected_cur_month,
         block_ulb_code,rural_urban_id from
         " . $table_name . " as A group by block_ulb_code,rural_urban_id
         ) as main LEFT JOIN
         (
           select coalesce(count(tld.pension_id),0) as total_lotted,
         coalesce(count(distinct tld.pension_id) FILTER(WHERE tld.wrongdata_flag in (0,1,2) and tl.push_to_ifms_status=1),0) as pushed_ifms,
         coalesce(count( distinct ben.id) FILTER(WHERE tld.wrongdata_flag in (1) and (tl.ref_no is not null or tl.ref_no <> -1)),0) as ifms_returned,
         coalesce(count( ben.id) FILTER(WHERE tld.wrongdata_flag in (0,2) and tl.ref_no>0 and trim(to_char(ben.created_at,'Month'))=  '" . $month . "'),0) as fresh_app_mandate_generated,
         coalesce(count( ben.id) FILTER(WHERE tld.wrongdata_flag in (0,2) and tl.ref_no>0 and EXTRACT(MONTH FROM ben.created_at)< EXTRACT(MONTH FROM to_date( '" . $month . "', 'Mon'))),0) as old_app_mandate_generated,
         coalesce(sum(tld.amount)FILTER(WHERE tld.wrongdata_flag in (0,2) and tl.ref_no>0),0) as amount_booked,
         coalesce(count( ben.id) FILTER(WHERE tld.wrongdata_flag in (2) and tl.ref_no>0),0) as rbi_failed,
         coalesce(count( ben.id) FILTER(WHERE tld.wrongdata_flag in (0) and tl.ref_no>0),0) as rbi_success,
         coalesce(sum(tld.amount)FILTER(WHERE tld.wrongdata_flag in (0) and tl.ref_no>0),0) as amount_paid,
         ben.block_ulb_code,ben.rural_urban_id
         from ifms.transaction_lot_details as tld
         JOIN ifms.transaction_lot as tl ON tld.drn_part=tl.lot_no 
         JOIN  " . $table_name . " as ben ON tld.pension_id=ben.id 
         where tld.drn_part= '" . $lot_no . "'
         and tl.lot_no= '" . $lot_no . "' and tl.lot_month= '" . $month . "'
         and ben.scheme_id=" . $scheme_id . " and tl.scheme_id=" . $scheme_id . " and tld.scheme_id=" . $scheme_id . "
         group by ben.block_ulb_code,ben.rural_urban_id
         ) as sub ON main.block_ulb_code=sub.block_ulb_code and main.rural_urban_id=sub.rural_urban_id";
          $data = DB::connection('pgsql_mis')->select($query1);
          // $lot_status = $data[0]->lot_status;
          if (empty($data)) {
            break;
          }
          $i = 0;
          // dd($data);
          foreach ($data as $row) {
            $insert_arr[$i]['scheme_id'] = $scheme_id;
            $insert_arr[$i]['scheme_name'] = $schemes_arr->scheme_name;
            $insert_arr[$i]['fin_year'] = $year;
            $insert_arr[$i]['lot_month'] = $month;
            $insert_arr[$i]['blk_munc_code'] = $row->block_ulb_code;
            $insert_arr[$i]['rural_urban_id'] = $row->rural_urban_id;
            $insert_arr[$i]['payment_mode'] = strtoupper(trim($payment_mode));
            // dd($row->rural_urban_id);
            if (empty($row->block_ulb_code)) {
              $block_ulb_code = NULL;
              $blk_munc_name = NULL;
              $sub_div_code = NULL;
              $sub_div_name = NULL;
            } else {

              if ($row->rural_urban_id == 1) {
                $block_ulb_code = $row->block_ulb_code;
                $urban_list = $urban_list->where('urban_body_code', $block_ulb_code)->first();
                $blk_munc_name = trim($urban_list->urban_body_name);
                $sub_div_code = $urban_list->sub_district_code;
                $insert_arr[$i]['sub_div_code'] = $sub_div_code;
                $sdo_list = $sdo_list->where('sub_district_code', $sub_div_code)->first();
                $sub_div_name = trim($sdo_list->sub_district_name);
                $insert_arr[$i]['total_application'] = $row->total_application;
                $insert_arr[$i]['sub_div_name'] = $sub_div_name;
                $district_code = $urban_list[0]->district_code;
                $insert_arr[$i]['district_code'] = $district_code;
                $district_list = $district_list->where('district_code', $district_code)->first();
                $district_name = trim($district_list->district_name);
                $insert_arr[$i]['district_name'] = $district_name;
              } else {
                $block_ulb_code = $row->block_ulb_code;
                // dd($block_ulb_code);
                $block_list = $block_list->where('block_code', $block_ulb_code)->first();
                //dump($block_list);
                $blk_munc_name = trim($block_list->block_name);

                $insert_arr[$i]['blk_munc_name'] = $blk_munc_name;
                $sub_div_code = NULL;
                $sub_div_name = NULL;
                $district_code = $block_list->district_code;
                $insert_arr[$i]['district_code'] = $district_code;
                $district_list = $sdo_list->where('district_code', $district_code)->first();
                $district_name = trim($district_list->district_name);
                $insert_arr[$i]['district_name'] = $district_name;
              }
            }

            $insert_arr[$i]['total_application'] = $row->total_application;
            $insert_arr[$i]['total_approved'] = $row->total_approved;
            $insert_arr[$i]['total_verified'] = $row->total_verified;
            $insert_arr[$i]['total_rejected'] = $row->total_rejected;
            $insert_arr[$i]['total_uploaded_cur_month'] = $row->total_uploaded_cur_month;
            $insert_arr[$i]['total_verified_cur_month'] = $row->total_verified_cur_month;
            $insert_arr[$i]['total_approved_cur_month'] = $row->total_approved_cur_month;
            $insert_arr[$i]['total_rejected_cur_month'] = $row->total_rejected_cur_month;
            $insert_arr[$i]['push_for_payments_total'] = $row->pushed_ifms;
            $insert_arr[$i]['push_for_payments_new'] = $row->fresh_app_mandate_generated;
            $insert_arr[$i]['push_for_payments_old'] = $row->old_app_mandate_generated;
            $insert_arr[$i]['successfull_payments'] = $row->rbi_success;
            $insert_arr[$i]['failure_payments'] = $row->rbi_failed;
            $insert_arr[$i]['failure_payments_ifms'] = $row->ifms_returned;
            $insert_arr[$i]['amount_booked'] = $row->amount_booked;
            $insert_arr[$i]['amount_paid'] = $row->amount_paid;
            $insert_arr[$i]['added_to_lot'] = $row->total_lotted;
            $i++;
          }
        } else if (strtoupper(trim($payment_mode)) == 'SBI') {
          $insert_arr = array();
          $lotQuery = "select lot_status from sbi.transaction_lot where lot_no='" . $lot_no . "'";
          $lotStatus = DB::connection('pgsql_mis')->select($lotQuery);

          $query1 = "select main.total_application,
          main.total_approved,
          main.total_verified,
          main.total_rejected,
          main.total_uploaded_cur_month,
          main.total_verified_cur_month,
          main.total_approved_cur_month,
          main.total_rejected_cur_month,
          sub.total_lotted,
          sub.current_month_pushed_sbi,
          sub.past_months_pushed_sbi,
          sub.sbi_under_process,
          sub.sbi_success,
          sub.sbi_failed,
          sub.amount_billed,
          main.block_ulb_code,
          main.rural_urban_id from
          (
          select coalesce(count(A.id),0) as total_application,
          coalesce(count(id) FILTER(WHERE A.next_level_role_id=0),0) as total_approved,
          coalesce(count(id) FILTER(WHERE A.is_verified=1 and A.is_approved=0 and A.is_rejected=0),0) as total_verified,
          coalesce(count(id) FILTER(WHERE A.is_rejected=1),0) as total_rejected,
          coalesce(count(id) FILTER(WHERE date_part('year',A.created_at)=" . $pension_search_year . " and trim(to_char(A.created_at,'Month'))= '" . $month . "') ,0) as total_uploaded_cur_month,
          coalesce(count(id) FILTER(WHERE A.is_verified=1 and A.is_approved=0 and A.is_rejected=0 and date_part('year',A.created_at)=" . $pension_search_year . " and trim(to_char(A.created_at,'Month'))=  '" . $month . "'),0) as total_verified_cur_month,
          coalesce(count(id) FILTER(WHERE A.is_verified=1 and A.is_approved=0 and A.is_rejected=0 and date_part('year',A.created_at)=" . $pension_search_year . " and trim(to_char(A.created_at,'Month'))=  '" . $month . "'),0) as total_approved_cur_month,
          coalesce(count(id) FILTER(WHERE A.is_rejected=1 and date_part('year',A.created_at)=" . $pension_search_year . " and trim(to_char(A.created_at,'Month'))=  '" . $month . "'),0) as total_rejected_cur_month,
          block_ulb_code,rural_urban_id from
          " . $table_name . " as A group by block_ulb_code,rural_urban_id
          ) as main LEFT JOIN
          (
          select coalesce(count(tld.pension_id),0) as total_lotted,
          coalesce(count( ben.id) FILTER(WHERE tl.lot_status <=5 and date_part('year',ben.created_at)=" . $pension_search_year . " and trim(to_char(ben.created_at,'Month'))=  '" . $month . "'),0) as current_month_pushed_sbi,
          coalesce(count( ben.id) FILTER(WHERE tl.lot_status <=5 and EXTRACT(MONTH FROM ben.created_at)< EXTRACT(MONTH FROM to_date( '" . $month . "', 'Mon'))),0) as past_months_pushed_sbi,
          coalesce(count( ben.id) FILTER(WHERE  tl.lot_status<4),0) as sbi_under_process,
          coalesce(count( ben.id) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3),0) as sbi_success,
          coalesce(count( ben.id) FILTER(WHERE tld.status_code!='S00' and tld.status_code is not null),0) as sbi_failed,
          coalesce(sum(tld.credit_amount/100) FILTER(WHERE tld.status_code='S00' and tl.lot_status>3 ),0) as amount_billed,
          ben.block_ulb_code,ben.rural_urban_id
          from sbi.transaction_lot_details as tld
          JOIN sbi.transaction_lot as tl ON tld.lot_no=tl.lot_no 
          JOIN  " . $table_name . " as ben ON tld.pension_id=ben.id
          where tld.lot_no= '" . $lot_no . "'
          and tl.lot_no= '" . $lot_no . "' and tl.lot_month= '" . $month . "'
          and ben.scheme_id=" . $scheme_id . " and tl.scheme_id=" . $scheme_id . " and tld.scheme_id=" . $scheme_id . "
          group by ben.block_ulb_code,ben.rural_urban_id
          ) as sub ON main.block_ulb_code=sub.block_ulb_code and main.rural_urban_id=sub.rural_urban_id";
          $data = DB::connection('pgsql_mis')->select($query1);
          dd($data);
          if (empty($data)) {
            break;
          }
          $i = 0;
          foreach ($data as $row) {
            $insert_arr[$i]['scheme_id'] = $scheme_id;
            $insert_arr[$i]['scheme_name'] = $schemes_arr->scheme_name;
            $insert_arr[$i]['fin_year'] = $year;
            $insert_arr[$i]['lot_month'] = $month;
            $insert_arr[$i]['blk_munc_code'] = $row->block_ulb_code;
            $insert_arr[$i]['rural_urban_id'] = $row->rural_urban_id;
            $insert_arr[$i]['payment_mode'] = strtoupper(trim($payment_mode));
            if (empty($row->block_ulb_code)) {
              $block_ulb_code = NULL;
              $blk_munc_name = NULL;
              $sub_div_code = NULL;
              $sub_div_name = NULL;
            } else {

              if ($row->rural_urban_id == 1) {
                $block_ulb_code = $row->block_ulb_code;
                $urban_list = $urban_list->where('urban_body_code', $block_ulb_code)->first();
                $blk_munc_name = trim($urban_list->urban_body_name);
                $sub_div_code = $urban_list->sub_district_code;
                $insert_arr[$i]['sub_div_code'] = $sub_div_code;
                $sdo_list = $sdo_list->where('sub_district_code', $sub_div_code)->first();
                $sub_div_name = trim($sdo_list->sub_district_name);
                $insert_arr[$i]['total_application'] = $row->total_application;
                $insert_arr[$i]['sub_div_name'] = $sub_div_name;
                $district_code = $urban_list->district_code;
                $insert_arr[$i]['district_code'] = $district_code;
                $district_list = $district_list->where('district_code', $district_code)->first();
                $district_name = trim($district_list->district_name);
                $insert_arr[$i]['district_name'] = $district_name;
              } else {
                $block_ulb_code = $row->block_ulb_code;
                dd($block_ulb_code);
                $block_list = $block_list->where('block_code', $block_ulb_code)->first();
                $blk_munc_name = trim($block_list->block_name);
                $insert_arr[$i]['blk_munc_name'] = $blk_munc_name;
                $sub_div_code = NULL;
                $sub_div_name = NULL;
                $district_code = $block_list->district_code;
                $insert_arr[$i]['district_code'] = $district_code;
                $district_list = $sdo_list->where('district_code', $district_code)->first();
                $district_name = trim($district_list->district_name);
                $insert_arr[$i]['district_name'] = $district_name;
              }
            }

            $insert_arr[$i]['total_application'] = $row->total_application;
            $insert_arr[$i]['total_approved'] = $row->total_approved;
            $insert_arr[$i]['total_verified'] = $row->total_verified;
            $insert_arr[$i]['total_rejected'] = $row->total_rejected;
            $insert_arr[$i]['total_uploaded_cur_month'] = $row->total_uploaded_cur_month;
            $insert_arr[$i]['total_verified_cur_month'] = $row->total_verified_cur_month;
            $insert_arr[$i]['total_approved_cur_month'] = $row->total_approved_cur_month;
            $insert_arr[$i]['total_rejected_cur_month'] = $row->total_rejected_cur_month;
            $insert_arr[$i]['push_for_payments_new'] = $row->current_month_pushed_sbi;
            $insert_arr[$i]['push_for_payments_old'] = $row->past_months_pushed_sbi;
            $insert_arr[$i]['successfull_payments'] = $row->sbi_success;
            $insert_arr[$i]['failure_payments'] = $row->sbi_failed;
            $insert_arr[$i]['amount_paid'] = $row->amount_billed;
            $insert_arr[$i]['added_to_lot'] = $row->total_lotted;
            $insert_arr[$i]['under_process'] = $row->sbi_under_process;
            $i++;
          }
        }
      }
      if (count($insert_arr) > 0) {
        DrillDown_report::insert($insert_arr);
      }
    }
  }
}
