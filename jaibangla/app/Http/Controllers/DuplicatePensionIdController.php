<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Scheme;
use App\District;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DuplicatePensionIdController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }


  public function findList(Request $request)
  {
    $showTable = 0;
    $benPensionCount = '';
    $benLastpaidyymm = '';
    $tldSuccessCount = '';
    $tldMaxpaidyymm = '';
    $proposedLastpaidyymm = '';
    $disCripency_found = 0;
    $fill_value_arr = array();
    $fill_value_arr['lot_no'] = '';
    $inValid = 0;
    $inValidMsg = '';
    $isSubmitted = 0;
    $IdFound = 0;
    $check_condition = '';
    $searchResult = array();
    $errors = array();
    $schemes_arr = Scheme::where('is_active', 1)->orderBy('scheme_name')->get(['scheme_name as name', 'id as id']);
    if (isset($request->submit)) {

      $cur_payment_mode = $request->cur_payment_mode;
      if (!empty($request->cur_payment_mode)) {
        $fill_value_arr['cur_payment_mode'] = $cur_payment_mode;
      }
      $pension_id = $request->pension_id;
      if (!empty($request->pension_id)) {
        $fill_value_arr['pension_id'] = $pension_id;
      }

      $rules = array(
        'pension_id' => 'required',
        'cur_payment_mode' => 'required',
      );
      $attributes = [
        'cur_payment_mode' => 'Current Payment Mode',
        'pension_id' => 'Pension Id'
      ];
      $messages = [
        'required' => 'The :attribute field is required.',
        'numeric' => 'Only integer allowed for :attribute',
        'in' => 'The :attribute field not valid.',
      ];
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $isSubmitted = 1;
        $ben_query = "select payment_count,last_paid_yymm,next_level_role_id,date(created_at) as created_at  from pension.beneficiary where id=" . $pension_id;
        $ben_data = DB::connection('pgsql_mis')->select($ben_query);
        if (!empty($ben_data)) {
          $ben_payment_count = $ben_data[0]->payment_count;
          $ben_last_paid_yymm = $ben_data[0]->last_paid_yymm;
          $benPensionCount = $ben_payment_count;
          $benLastpaidyymm = $ben_last_paid_yymm;
          $query1 = "select count(P.id) as cnt,min(paid_yymm) as min_paid_yymm from
    sbi.transaction_lot_details_report as P JOIN sbi.transaction_lot as Q ON P.lot_no=Q.lot_no
    where paid_yymm IS NOT NULL and P.pension_id=" . $pension_id . " and P.status_code='S00' and Q.lot_status>3";
          $data_part1 = DB::connection('pgsql_mis')->select($query1);
          if (!empty($data_part1)) {
            $preCount = $data_part1[0]->cnt;
            $min_paid_yymm = $data_part1[0]->min_paid_yymm;
            $min_date = '20' . substr($data_part1[0]->min_paid_yymm, 0, 2) . '-' . substr($data_part1[0]->min_paid_yymm, -2) . '-01';
          } else {
            $preCount = 0;
            $min_paid_yymm = NULL;
          }
          $query2 = "select count(P.id) as cnt,min(paid_yymm) as min_paid_yymm,max(paid_yymm) as max_paid_yymm from
          sbi.transaction_lot_details as P JOIN sbi.transaction_lot as Q ON P.lot_no=Q.lot_no
          where paid_yymm IS NOT NULL and P.pension_id=" . $pension_id . " and P.status_code='S00' and Q.lot_status>3";
          $data_part2 = DB::connection('pgsql_mis')->select($query2);
          if (!empty($data_part2)) {
            $curCount = $data_part2[0]->cnt;
            $max_paid_yymm = $data_part2[0]->max_paid_yymm;
            $min_date = '20' . substr($data_part1[0]->min_paid_yymm, 0, 2) . '-' . substr($data_part1[0]->min_paid_yymm, -2) . '-01';
          } else {
            $curCount = 0;
            $max_paid_yymm = NULL;
          }
          $tdlCount = $preCount + $curCount;
          $tldSuccessCount = $tdlCount;
          $tdlCounttobeAdded = $tdlCount - 1;
          if (empty($min_paid_yymm)) {
            $checkMinpaidyymm = $data_part2[0]->max_paid_yymm;;
          } else {
            $checkMinpaidyymm = $min_paid_yymm;
          }
          $tldMaxpaidyymm = $checkMinpaidyymm;
          $min_date = '20' . substr($checkMinpaidyymm, 0, 2) . '-' . substr($checkMinpaidyymm, -2) . '-01';
          $new_date = Carbon::parse($min_date)->addMonths($tdlCounttobeAdded);
          $new_last_paidyymm = substr($new_date->year, -2) .  sprintf("%02d", $new_date->month);
          $proposedLastpaidyymm = $new_last_paidyymm;
          // dump('Ben payment Count:' . $ben_payment_count);
          //dump('Tld Count:' . $tdlCount);
          // dump('Ben Last Paid YYMM:' . $ben_last_paid_yymm);
          //dd('Proposed Paid YYMM:' . $new_last_paidyymm);
          if ($ben_payment_count == $tdlCount && $new_last_paidyymm == $ben_last_paid_yymm) {
            $inValid = 0;
            $IdFound = 1;
            $disCripency_found = 0;
            $inValidMsg = 'All are ok for Pension Id: ' . $pension_id;
          } else {
            $inValid = 1;
            $IdFound = 1;
            $disCripency_found = 1;
            $inValidMsg = 'Their are some discripency ..please click on below button to update it ';
          }
        } else {
          $IdFound = 0;
          $inValid = 1;
          $inValidMsg = 'No Beneficiary Found with ID ' . $pension_id;
        }
      } else {
        $errors = $validator->errors()->all();
        //dd($errors);
      }
    }
    return view(
      'DuplicatePensionId.getList',
      [
        'isSubmitted' => $isSubmitted,
        'schemes_arr' => $schemes_arr,
        'fill_value_arr' => $fill_value_arr,
        'errors' => $errors,
        'searchResult' => $searchResult,
        'inValid' => $inValid,
        'IdFound' => $IdFound,
        'showTable' => $showTable,
        'inValidMsg' => $inValidMsg,
        'benPensionCount' => $benPensionCount,
        'benLastpaidyymm' => $benLastpaidyymm,
        'tldSuccessCount' => $tldSuccessCount,
        'proposedLastpaidyymm' => $proposedLastpaidyymm,
        'tldMaxpaidyymm' => $tldMaxpaidyymm,
        'disCripency_found' => $disCripency_found


      ]
    );
  }
  public function updteList(Request $request)
  {
    $pension_id = $request->pension_id;
    if (!empty($request->payment_mode)) {
      if ($request->payment_mode == 'ifms') {
        $schema_name = 'ifms';
        $lot_column = 'drn_part';
      } else if ($request->payment_mode == 'sbi') {
        $schema_name = 'sbi';
        $lot_column = 'lot_no';
      }
    } else {
      $schema_name = 'public';
      $lot_column = 'lot_no';
    }
    $ben_query = "select payment_count,last_paid_yymm,next_level_role_id,date(created_at) as created_at  from pension.beneficiary where id=" . $pension_id;
    $ben_data = DB::connection('pgsql_mis')->select($ben_query);
    $ben_payment_count = $ben_data[0]->payment_count;
    $last_paid_yymm = $ben_data[0]->last_paid_yymm;
    $ben_created_at = $ben_data[0]->created_at;
    dump('Ben created_at:' . $ben_created_at);
    dump('Ben payment count:' . $ben_payment_count);
    dump('Ben last_paid_yymm:' . $last_paid_yymm);
    $query1 = "select count(P.id) as cnt,min(paid_yymm) as min_paid_yymm from
    sbi.transaction_lot_details_report as P JOIN sbi.transaction_lot as Q ON P.lot_no=Q.lot_no
    where paid_yymm IS NOT NULL and P.pension_id=" . $pension_id . " and P.status_code='S00' and Q.lot_status>3";
    $data_part1 = DB::connection('pgsql_mis')->select($query1);
    if (!empty($data_part1)) {
      $preCount = $data_part1[0]->cnt;
      dump('Pre Count:' . $preCount);
      dump('Min Paid YYMM: ' . $data_part1[0]->min_paid_yymm);
      $min_date = '20' . substr($data_part1[0]->min_paid_yymm, 0, 2) . '-' . substr($data_part1[0]->min_paid_yymm, -2) . '-01';
      dump('Min Paid date: ' . $min_date);
    } else {
      $pre_count = 0;
    }
    $query2 = "select count(P.id) as cnt,max(paid_yymm) as paid_yymm from
    sbi.transaction_lot_details as P JOIN sbi.transaction_lot as Q ON P.lot_no=Q.lot_no
    where paid_yymm IS NOT NULL and P.pension_id=" . $pension_id . " and P.status_code='S00' and Q.lot_status>3";
    $data_part2 = DB::connection('pgsql_mis')->select($query2);
    $CurtdlCount = $data_part2[0]->cnt;
    dump('Cur Count:' . $CurtdlCount);
    $tdlCount = $preCount + $CurtdlCount - 1;
    $tldpaid_yymm = $data_part2[0]->paid_yymm;
    dump('Tld Count:' .  ($preCount + $CurtdlCount));
    dump('Tld Last Paid YYMM:' . $tldpaid_yymm);
    dump('Month To Be Added: ' . $tdlCount);
    $new_date = Carbon::parse($min_date)->addMonths($tdlCount);
    dump('After adding month: ' . $new_date);
    //dd($new_date);
    $new_last_paidyymm = substr($new_date->year, -2) .  sprintf("%02d", $new_date->month);;
    dump('After adding month last_paid yymm: ' . $new_last_paidyymm);
    //$new_last_paidyymm=$new_date->year;
    dump('Last paid yymm updated: ' . $new_last_paidyymm);
    dump('Payment Count updated: ' . ($preCount + $CurtdlCount));

    if ($ben_payment_count == ($preCount + $CurtdlCount)) {
      dump('Does not Enter Inside payment count update');
      $payment_count_update = null;
    } else {
      dump('Enter Inside payment count update');
      $payment_count_update = $tdlCount;
    }
    if ($new_last_paidyymm == $last_paid_yymm) {
      dump('Does not Enter Inside last paid yymm update');
      $paid_yymm_update = null;
    } else {
      dump('Enter Inside last paid yymm update');
      $paid_yymm_update = $new_last_paidyymm;
    }
    if ($tldpaid_yymm  == $new_last_paidyymm) {
      $lot_generated = null;
      $is_active = null;
      dump('Does not Enter Inside lot generated and is_active update');
    } else {
      $paid_yymm_update = $new_last_paidyymm;
      dump('Enter Inside lot generated and is_active update');

      //$payment_adjustment_checked = DB::select('SELECT public."payment_adjustment"(' . $pension_id . ', -1)');
    }
    dd('End');
  }
}
