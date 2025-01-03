<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Scheme;
use App\District;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Storage;

class LotCheckController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->check_condition_pre_arr = [
      '5' => array("name" => 'Created By District Code IS NULL', 'isDuplicate' => '0'),
      '10' => array("name" => 'Created By Local Body Code IS NULL', 'isDuplicate' => '0'),
      '15' => array("name" => 'Unexpected Benficiary Name(length>100)', 'isDuplicate' => '0'),
      '20' => array("name" => 'Bank Code or IFSC IS NULL', 'isDuplicate' => '0'),
      '25' => array("name" => 'Bank Code or IFSC INVALID', 'isDuplicate' => '0'),
      '30' => array("name" => 'Scheme Code Invalid', 'isDuplicate' => '0'),
      '35' => array("name" => 'Duplicate Ben Check(Bank Account)', 'isDuplicate' => '1', 'duplicate_column' => 'bank_code'),
      '40' => array("name" => 'Duplicate Ben Check(Ration Card)', 'isDuplicate' => '1', 'duplicate_column' => 'ration_card_cat,ration_card_no'),
      '45' => array("name" => 'Duplicate Ben Check(Voter Card)', 'isDuplicate' => '1', 'duplicate_column' => 'epic_voter_id'),
      '50' => array("name" => 'Duplicate Ben Check(Aadhar Number)', 'isDuplicate' => '1', 'duplicate_column' => 'aadhar_no')
    ];

    $this->check_condition_post_arr = [
      '5' => array("name" => 'Duplicate Ben Check(Pension Id)', 'isDuplicate' => '1', 'duplicate_column' => 'pension_id'),
      /*
      '10' => array("name" => 'Duplicate Ben Check(Bank Account)', 'isDuplicate' => '1', 'duplicate_column' => 'bank_code'),
      '15' => array("name" => 'Check for Repeat Lot Number Is NULL', 'isDuplicate' => '0'),
      '20' => array("name" => 'Check for if pending Ben', 'isDuplicate' => '0'),
      '25' => array("name" => 'Check for newly Ben Count vs Previous Success Count', 'isDuplicate' => '0'),
      '30' => array("name" => 'Check for lot previously successed but not yet repeated', 'isDuplicate' => '0')
      */
    ];
  }
  function get_condion_pre_description($condition)
  {
    $description = array();
    $check_condition_arr =  $this->check_condition_pre_arr;
    $description['name'] = $check_condition_arr[$condition]['name'];
    $description['isDuplicate'] = $check_condition_arr[$condition]['isDuplicate'];
    if (!empty($check_condition_arr[$condition]['isDuplicate']) &&  $check_condition_arr[$condition]['isDuplicate'] == 1) {
      $description['duplicate_column'] = $check_condition_arr[$condition]['duplicate_column'];
    }
    return $description;
  }
  function get_where_pre_condition($condition, $scheme_id)
  {
    $condition_arr = array();
    $where = '';
    $having = '';
    $groupBy = '';
    if ($condition == 5) {
      $where = ' created_by_dist_code IS NULL';
    } else if ($condition == 10) {
      $where = ' created_by_local_body_code IS NULL';
    } else if ($condition == 15) {
      $where = 'length(coalesce(ben_fname,\'\')||coalesce(ben_mname,\'\')||coalesce(ben_lname,\'\'))>100';
    } else if ($condition == 20) {
      $where = ' bank_code IS NULL OR bank_ifsc IS NULL';
    } else if ($condition == 25) {
      $where = " bank_code ~* '[a-z]' OR NOT EXISTS(SELECT ifsc from ifsc.bank_details as b  WHERE  
      trim(a.bank_ifsc) = trim(b.ifsc) 
     )";
    } else if ($condition == 30) {
      $where = ' scheme_id IS NULL or scheme_id!=' . $scheme_id;
    } else if ($condition == 35) {
      $where = ' bank_code IS NOT NULL';
      $groupBy = ' group by trim(bank_code)';
      $having = ' having count(*)>1';
    } else if ($condition == 40) {
      $where = ' concat(ration_card_cat,ration_card_no) IS NOT NULL';
      $groupBy = ' group by trim(concat(ration_card_cat,ration_card_no))';
      $having = ' having count(*)>1';
    } else if ($condition == 45) {
      $where = ' epic_voter_id IS NOT NULL';
      $groupBy = ' group by trim(epic_voter_id)';
      $having = ' having count(*)>1';
    } else if ($condition == 50) {
      $where = ' aadhar_no IS NOT NULL';
      $groupBy = ' group by trim(aadhar_no)';
      $having = ' having count(*)>1';
    }
    $condition_arr['where'] = $where;
    $condition_arr['having'] = $having;
    $condition_arr['groupBy'] = $groupBy;
    return $condition_arr;
  }
  function get_condion_post_description($condition)
  {
    $description = array();
    $check_condition_arr =  $this->check_condition_post_arr;
    $description['name'] = $check_condition_arr[$condition]['name'];
    $description['isDuplicate'] = $check_condition_arr[$condition]['isDuplicate'];
    if (!empty($check_condition_arr[$condition]['isDuplicate']) &&  $check_condition_arr[$condition]['isDuplicate'] == 1) {
      $description['duplicate_column'] = $check_condition_arr[$condition]['duplicate_column'];
    }
    return $description;
  }
  function get_where_post_condition($condition, $scheme_id, $fin_year = NULL, $month = NULL, $pre_payment_mode = NULL, $cur_payment_mode = NULL, $lot_no = NULL)
  {
    $condition_arr = array();
    $where = '';
    $having = '';
    $groupBy = '';
    $where = " A.fin_year='" . $fin_year . "'";
    $where = $where .  " and B.lot_year='" . $fin_year . "'";
    $where =  $where . " and B.lot_month='" . $month . "'";
    $where = $where . " and A.scheme_id=" . $scheme_id;
    $where = $where . " and B.scheme_id=" . $scheme_id;

    if (!empty($lot_no)) {
      $where = $where .  " and B.lot_no='" . $lot_no . "'";
    }
    if ($condition == 5) {
      $now = Carbon::now();
      $cur_year = $now->year;
      $cur_month = $now->format('F');
      if ($cur_month == 'January' || $cur_month == 'February' || $cur_month == 'March') {
        $cur_year = $cur_year - 1;
      }
      $cur_financial_year = $cur_year . '-' . ($cur_year + 1);
      if (!empty($fin_year) && !empty($month)) {
        if ($cur_financial_year == $fin_year && $month == $cur_month) {
          $where =  $where . ' and lot_type_id IN (1,2,3)';
        } else {
          if ($cur_payment_mode == 'ifms') {
            $where =  $where . ' and A.is_active=1 and A.wrongdata_flag=0 and B.ref_no>0';
          } else if ($cur_payment_mode == 'sbi') {
            $where =  $where . ' and A.is_active=1 and A.status_code=\'S00\'';
          }
        }
      }
      $groupBy = ' group by pension_id';
      $having = ' having count(*)>1';
    } else if ($condition == 10) {
      $where = ' created_by_local_body_code IS NULL';
    } else if ($condition == 15) {
      $where = 'length(coalesce(ben_fname,\'\')||coalesce(ben_mname,\'\')||coalesce(ben_lname,\'\'))>100';
    } else if ($condition == 20) {
      $where = ' bank_code IS NULL OR bank_ifsc IS NULL';
    } else if ($condition == 25) {
      $where = " bank_code ~* '[a-z]' OR NOT EXISTS(SELECT ifsc from ifsc.bank_details as b  WHERE  
      trim(a.bank_ifsc) = trim(b.ifsc) 
     )";
    } else if ($condition == 30) {
      $where = ' scheme_id IS NULL or scheme_id!=' . $scheme_id;
    } else if ($condition == 35) {
      $where = ' bank_code IS NOT NULL';
      $groupBy = ' group by trim(bank_code)';
      $having = ' having count(*)>1';
    } else if ($condition == 40) {
      $where = ' concat(ration_card_cat,ration_card_no) IS NOT NULL';
      $groupBy = ' group by trim(concat(ration_card_cat,ration_card_no))';
      $having = ' having count(*)>1';
    } else if ($condition == 45) {
      $where = ' epic_voter_id IS NOT NULL';
      $groupBy = ' group by trim(epic_voter_id)';
      $having = ' having count(*)>1';
    } else if ($condition == 50) {
      $where = ' aadhar_no IS NOT NULL';
      $groupBy = ' group by trim(aadhar_no)';
      $having = ' having count(*)>1';
    }
    $condition_arr['where'] = $where;
    $condition_arr['having'] = $having;
    $condition_arr['groupBy'] = $groupBy;
    return $condition_arr;
  }
  public function preconsolidated(Request $request)
  {
    $check_condition_arr =  $this->check_condition_pre_arr;
    $inValid = 0;
    $inValidMsg = '';
    $chk_cond_key = array_keys($check_condition_arr);
    // dd($chk_cond);
    $isSubmitted = 0;
    $select_scheme_id = '';
    $check_condition = '';
    $searchResult = array();
    $errors = array();
    $schemes_arr = Scheme::where('is_active', 1)->orderBy('scheme_name')->get(['scheme_name as name', 'id as id']);
    if (isset($request->submit)) {
      $rules = array(
        'scheme_id' => 'nullable|numeric',
        'check_condition' => 'required',
      );
      $attributes = [
        'scheme_id' => 'Scheme',
        'check_condition' => 'Check Condition',
      ];
      $messages = [
        'required' => 'The :attribute field is required.',
        'numeric' => 'Only integer allowed for :attribute',
        'in' => 'The :attribute field not valid.',
      ];
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $isSubmitted = 1;
        $select_scheme_id = $request->scheme_id;
        $check_condition = $request->check_condition;
        $chek_condion_details = $this->get_condion_pre_description($check_condition);
        $selected_check_condition = $check_condition;
        $forDuplicate = $chek_condion_details['isDuplicate'];
        if (!empty($request->scheme_id)) {
          $scheme_row = Scheme::select('scheme_name', 'id', 'short_code')->where('is_active', 1)->where('id', $select_scheme_id)->first();
          if (empty($scheme_row->short_code)) {
            return redirect("pre-lot-consolidated-check")->with('error', 'Scheme Schema Not found');
          }
          $schema_name = $scheme_row->short_code;
        } else {
          $schema_name = 'pension';
        }


        $Condition = $this->get_where_pre_condition($selected_check_condition, $select_scheme_id);
        //dd($Condition);
        $whereCondtion = $Condition['where'];
        $havingCondtion = $Condition['having'];
        $groupByCondtion = $Condition['groupBy'];


        $count_query = "select count(id) as cnt from " . $schema_name . ".beneficiary as A where next_level_role_id=0 
        and  $whereCondtion $groupByCondtion  $havingCondtion";
        $count_data = DB::connection('pgsql_mis')->select($count_query);
        if (!empty($count_data[0]->cnt)) {
          $count = $count_data[0]->cnt;
        } else
          $count = 0;

        if ($count > 0) {


          $inValid = 1;
          $inValidMsg = 'There Are ' . $count . ' Beneficiaries where ' . $chek_condion_details['name'];
        } else {
          $inValid = 0;
          $inValidMsg = 'No Data Found with ' . $chek_condion_details['name'];
        }
      } else {
        $errors = $validator->errors()->all();
        //dd($errors);
      }
    }
    return view(
      'LotCheck.preconsolidated',
      [
        'isSubmitted' => $isSubmitted,
        'schemes_arr' => $schemes_arr,
        'select_scheme_id' => $select_scheme_id,
        'selected_check_condition' => $check_condition,
        'check_condition_arr' => $check_condition_arr,
        'errors' => $errors,
        'searchResult' => $searchResult,
        'inValid' => $inValid,
        'inValidMsg' => $inValidMsg
      ]
    );
  }
  public function postconsolidated(Request $request)
  {
    $check_condition_arr =  $this->check_condition_post_arr;
    $fill_value_arr = array();
    $fill_value_arr['lot_no'] = '';
    $inValid = 0;
    $inValidMsg = '';
    $chk_cond_key = array_keys($check_condition_arr);
    // dd($chk_cond);
    $isSubmitted = 0;
    $select_scheme_id = '';
    $check_condition = '';
    $searchResult = array();
    $errors = array();
    $schemes_arr = Scheme::where('is_active', 1)->orderBy('scheme_name')->get(['scheme_name as name', 'id as id']);
    if (isset($request->submit)) {
      $scheme_id = $request->scheme_id;
      if (!empty($request->scheme_id)) {
        $fill_value_arr['scheme_id'] = $scheme_id;
      }
      $fin_year = $request->fin_year;
      if (!empty($request->fin_year)) {
        $fill_value_arr['fin_year'] = $fin_year;
      }
      $month = $request->month;
      if (!empty($request->month)) {
        $fill_value_arr['month'] = $month;
      }
      $pre_payment_mode = $request->pre_payment_mode;
      if (!empty($request->pre_payment_mode)) {
        $fill_value_arr['pre_payment_mode'] = $pre_payment_mode;
      }
      $cur_payment_mode = $request->cur_payment_mode;
      if (!empty($request->cur_payment_mode)) {
        $fill_value_arr['cur_payment_mode'] = $cur_payment_mode;
      }
      $lot_no = $request->lot_no;
      if (!empty($request->lot_no)) {
        $fill_value_arr['lot_no'] = $lot_no;
      }
      $debit_reference = $request->debit_reference;
      if (!empty($request->debit_reference)) {
        $fill_value_arr['debit_reference'] = $debit_reference;
      }
      $check_condition = $request->check_condition;
      if (!empty($request->check_condition)) {
        $fill_value_arr['check_condition'] = $check_condition;
      }
      $rules = array(
        'scheme_id' => 'required|numeric',
        'fin_year' => 'required',
        'month' => 'required',
        'pre_payment_mode' => 'required',
        'cur_payment_mode' => 'required',
        'check_condition' => 'required|numeric',
      );
      $attributes = [
        'scheme_id' => 'Scheme',
        'fin_year' => 'Financial Year',
        'month' => 'Month',
        'pre_payment_mode' => 'Previous Payment Mode',
        'cur_payment_mode' => 'Current Payment Mode',
        'lot_no' => 'Lot Number',
        'debit_reference' => 'Debit Reference',
        'check_condition' => 'Check Condition',
      ];
      $messages = [
        'required' => 'The :attribute field is required.',
        'numeric' => 'Only integer allowed for :attribute',
        'in' => 'The :attribute field not valid.',
      ];
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $isSubmitted = 1;

        $chek_condion_details = $this->get_condion_post_description($check_condition);
        if (!empty($request->cur_payment_mode)) {
          if ($request->cur_payment_mode == 'ifms') {
            $schema_name = 'ifms';
            $lot_column = 'drn_part';
          } else {
            $schema_name = 'sbi';
            $lot_column = 'lot_no';
          }
        } else {
          $schema_name = 'public';
          $lot_column = 'lot_no';
        }


        $Condition = $this->get_where_post_condition($check_condition, $scheme_id, $fin_year, $month, $pre_payment_mode, $cur_payment_mode, $lot_no);
        //dd($Condition);
        $whereCondtion = $Condition['where'];
        $havingCondtion = $Condition['having'];
        $groupByCondtion = $Condition['groupBy'];


        /*$count_query = "select count(A.id) as cnt from " . $schema_name . ".transaction_lot_details as A LEFT JOIN public.lot_master as B 
        ON A.$lot_column=B.lot_no where $whereCondtion $groupByCondtion  $havingCondtion";*/
        $count_query = "select count(cnt) as cnt from
        (
        select distinct(A.pension_id) as cnt from " . $schema_name . ".transaction_lot_details as A LEFT JOIN
        public.lot_master as B ON A.$lot_column=B.lot_no where $whereCondtion $groupByCondtion  $havingCondtion
        ) as K";
        $count_data = DB::connection('pgsql_mis')->select($count_query);
        if (!empty($count_data[0]->cnt)) {
          $count = $count_data[0]->cnt;
        } else
          $count = 0;

        if ($count > 0) {


          $inValid = 1;
          $inValidMsg = 'There Are ' . $count . ' Unquie Beneficiaries found where ' . $chek_condion_details['name'];
        } else {
          $inValid = 0;
          $inValidMsg = 'No Data Found with ' . $chek_condion_details['name'];
        }
      } else {
        $errors = $validator->errors()->all();
        //dd($errors);
      }
    }
    return view(
      'LotCheck.postconsolidated',
      [
        'isSubmitted' => $isSubmitted,
        'schemes_arr' => $schemes_arr,
        'fill_value_arr' => $fill_value_arr,
        'check_condition_arr' => $check_condition_arr,
        'errors' => $errors,
        'searchResult' => $searchResult,
        'inValid' => $inValid,
        'inValidMsg' => $inValidMsg
      ]
    );
  }
  public function lotFileMovementCheckSbi(Request $request)
  {


    $fill_value_arr = array();
    $fill_value_arr['lot_no'] = '';
    $inValid = 0;
    $inValidMsg = '';

    // dd($chk_cond);
    $isSubmitted = 0;
    $select_scheme_id = '';
    $check_condition = '';
    $searchResult = array();
    $errors = array();
    $arr = array();
    $schemes_arr = Scheme::where('is_active', 1)->orderBy('scheme_name')->get(['scheme_name as name', 'id as id']);
    if (isset($request->submit)) {
      $scheme_id = $request->scheme_id;
      if (!empty($request->scheme_id)) {
        $fill_value_arr['scheme_id'] = $scheme_id;
      }
      $fin_year = $request->fin_year;
      if (!empty($request->fin_year)) {
        $fill_value_arr['fin_year'] = $fin_year;
      }
      $month = $request->month;
      if (!empty($request->month)) {
        $fill_value_arr['month'] = $month;
      }

      $lot_no = $request->lot_no;
      if (!empty($request->lot_no)) {
        $fill_value_arr['lot_no'] = $lot_no;
        //$condition['lot_no'] = $lot_no;
      }
      $debit_reference = $request->debit_reference;
      if (!empty($request->debit_reference)) {
        $fill_value_arr['debit_reference'] = $debit_reference;
        // $condition['debit_reference'] = $debit_reference;
      }
      $check_condition = $request->check_condition;
      if (!empty($request->check_condition)) {
        $fill_value_arr['check_condition'] = $check_condition;
      }
      $rules = array(
        'scheme_id' => 'required|numeric',
        'fin_year' => 'required',
        'month' => 'required'
      );
      $attributes = [
        'scheme_id' => 'Scheme',
        'fin_year' => 'Financial Year',
        'month' => 'Month',
        'lot_no' => 'Lot Number',
        'debit_reference' => 'Debit Reference'
      ];
      $messages = [
        'required' => 'The :attribute field is required.',
        'numeric' => 'Only integer allowed for :attribute',
        'in' => 'The :attribute field not valid.',
      ];
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $arr = array();
        $isSubmitted = 1;
        $query = "select lot_no,lot_status,debit_reference from sbi.transaction_lot where scheme_id=" . $scheme_id . " 
        and lot_month='" . $month . "' and lot_year='" . $fin_year . "' ";
        $data = DB::connection('pgsql_mis')->select($query);
        if (!empty($data)) {
          $data_found = 1;
        } else
          $data_found = 0;
        if ($data_found == 1) {
          $i = 0;
          $arr = array();
          foreach ($data as $lot) {
            $exists_toprocess_local = "NA";
            $exists_toprocess_local_picked = "NA";
            $exists_toprocess_server = "NA";
            $exists_ack_local_picked = "NA";
            $exists_ack_server = "NA";
            $exists_response_local_picked = "NA";
            $exists_response_server = "NA";
            $message = '';

            $arr[$i]['is_ok'] = 0;
            $arr[$i]['lot_no'] = $lot->lot_no;
            $arr[$i]['lot_status'] = $lot->lot_status;
            $arr[$i]['debit_reference'] = $lot->debit_reference;
            $filename = $lot->debit_reference . '.xml';
            if ($lot->lot_status == 0) {
              $message = 'Lot has been created but not yet signed';
              $exists_toprocess_local = "NA";
              $exists_toprocess_local_picked = "NA";
              $exists_toprocess_server = "NA";
              $exists_ack_local_picked = "NA";
              $exists_ack_server = "NA";
              $exists_response_local_picked = "NA";
              $exists_response_server = "NA";
            } else if ($lot->lot_status > 0) {
              if (file_exists('/jaibangla/var/sbi/ePay/ToProcess/' . $filename))
                $exists_toprocess_local = "YES";
              else
                $exists_toprocess_local = "NO";
              if (file_exists('/jaibangla/var/sbi/ePay/ToProcess/Picked/' . $filename))
                $exists_toprocess_local_picked = "YES";
              else
                $exists_toprocess_local_picked = "NO";
              if (Storage::disk('sftp_sbi')->exists('ePay/ToProcess/' . $filename))
                $exists_toprocess_server = "YES";
              else
                $exists_toprocess_server = "NO";
              if (file_exists('/jaibangla/var/sbi/ePay/Acknowledgement/Picked/' . $filename))
                $exists_ack_local_picked = "YES";
              else
                $exists_ack_local_picked = "NO";
              if (file_exists('/jaibangla/var/sbi/ePay/Response/Picked/' . $filename))
                $exists_response_local_picked = "YES";
              else
                $exists_response_local_picked = "NO";
              if (Storage::disk('sftp_sbi')->exists('ePay/Response/' . $filename))
                $exists_response_server = "YES";
              else
                $exists_response_server = "NO";

              if ($lot->lot_status == 1) {
                $message = 'Lot has been signed and ready to push';
                $exists_toprocess_local_picked = "NA";
                $exists_toprocess_server = "NA";
                $exists_ack_local_picked = "NA";
                $exists_ack_server = "NA";
                $exists_response_local_picked = "NA";
                $exists_response_server = "NA";
              } else if ($lot->lot_status == 2) {
                $message = 'Lot Pushed to SBI and Waiting for Ack';
              } else if ($lot->lot_status == 3) {
                $message = 'Lot Pushed to SBI,Ack Received and Waiting for Response';
              } else if ($lot->lot_status == 4) {
                $message = 'Lot Pushed to SBI,Ack Received, Response Received but Import Sbi Report Pending';
              } else if ($lot->lot_status == 5) {
                $message = 'Lot Pushed to SBI,Ack Received,Response Received, Import SBI report but Import SBI from DDO end is pending';
              } else if ($lot->lot_status == 6) {
                $message = "Cycle Completed";
              } else {
                $msg = "";
              }
            } else {
              $message = 'Defun Lot';
              $exists_toprocess_local = "NA";
              $exists_toprocess_local_picked = "NA";
              $exists_toprocess_server = "NA";
              $exists_ack_local_picked = "NA";
              $exists_ack_server = "NA";
              $exists_response_local_picked = "NA";
              $exists_response_server = "NA";
            }

            $arr[$i]['message'] = $message;
            $arr[$i]['exists_toprocess_local'] =  $exists_toprocess_local;
            $arr[$i]['exists_toprocess_local_picked'] = $exists_toprocess_local_picked;
            $arr[$i]['exists_toprocess_server'] =  $exists_toprocess_server;
            $arr[$i]['exists_ack_local_picked'] = $exists_ack_local_picked;
            $arr[$i]['exists_ack_server'] =  $exists_ack_server;
            $arr[$i]['exists_response_local_picked'] = $exists_response_local_picked;
            $arr[$i]['exists_response_server'] =  $exists_response_server;
            $i++;
          }
        }
      } else {
        $errors = $validator->errors()->all();
        //dd($errors);
      }
    }
    return view(
      'LotCheck.FileMovementCheckSbi',
      [
        'isSubmitted' => $isSubmitted,
        'schemes_arr' => $schemes_arr,
        'fill_value_arr' => $fill_value_arr,
        'errors' => $errors,
        'searchResult' => $arr,
        'inValid' => $inValid,
        'inValidMsg' => $inValidMsg
      ]
    );
  }
  public function lotCheckExcelDownloadPre(Request $request)
  {
    $scheme_id = $request->scheme_id;
    $check_condition = $request->condition;
    $chek_condion_details = $this->get_condion_pre_description($check_condition);
    $condition = $check_condition;
    $forDuplicate = $chek_condion_details['isDuplicate'];
    if (!empty($request->scheme_id)) {
      $scheme_row = Scheme::select('scheme_name', 'id', 'short_code')->where('is_active', 1)->where('id', $scheme_id)->first();
      if (empty($scheme_row->short_code)) {
        return redirect("pre-lot-consolidated-check")->with('error', 'Scheme Schema Not found');
      }
      $schema_name = $scheme_row->short_code;
      $scheme_name = $scheme_row->scheme_name;
    } else {
      $schema_name = 'pension';
      $scheme_name = 'All Schemes';
    }
    $Condition = $this->get_where_pre_condition($condition, $scheme_id);
    $whereCondtion = $Condition['where'];
    $title = $scheme_name . "_Pre_" . $chek_condion_details['name'];
    $data = array();
    if ($forDuplicate == 0) {
      $query = "select s.scheme_name,d.district_name,block_ulb_name || case when rural_urban_id=1 then ' Municipality' else '' end  \"Block_Municipality\"
                ,a.id as \"Beneficiary_Id\"
                , ben_fname ||' '|| coalesce(ben_mname||' ','') || coalesce(ben_lname,'') as \"Name\"
                , a.bank_code  as \"Account_No\"
                , a.bank_ifsc as \"IFSC\",
                a.ration_card_cat as \"ration_card_cat\",a.ration_card_no as \"ration_card_no\",a.epic_voter_id as \"epic_voter_id\",a.aadhar_no as \"aadhar_no\"
                from " . $schema_name . ".beneficiary a left join
                (
                select district_code,district_name from m_district
                )d on a.created_by_dist_code=d.district_code 
                left join
                (
                select id,scheme_name from m_scheme
                )s on a.scheme_id=s.id 
                where next_level_role_id=0 and $whereCondtion order by ben_fname";
    } else {
      $duplicate_column = $chek_condion_details['duplicate_column'];
      $whereCondtion = $Condition['where'];
      $havingCondtion = $Condition['having'];
      $groupByCondtion = $Condition['groupBy'];
      if ($check_condition == 40)
        $query1 = "select concat(trim(ration_card_cat),trim(ration_card_no)) as code
        , count(*) as ben_no from $schema_name.beneficiary where next_level_role_id=0  
        group by concat(trim(ration_card_cat),trim(ration_card_no)) 
        having count(*)>1  and concat(trim(ration_card_cat),trim(ration_card_no))!=''";
      else
        $query1 = "select distinct(trim(" . $duplicate_column . ")) as code from  $schema_name.beneficiary where next_level_role_id=0  and  $whereCondtion $groupByCondtion  $havingCondtion";
      $data_part1 = DB::connection('pgsql_mis')->select($query1);
      $where_in = array();
      foreach ($data_part1 as $data_1) {
        //$where_in
        array_push($where_in, "'" . $data_1->code . "'");
      }
      if ($check_condition == 40)
        $query = "select s.scheme_name,d.district_name,block_ulb_name || case when rural_urban_id=1 then ' Municipality' else '' end  \"Block_Municipality\"
      ,a.id as \"Beneficiary_Id\"
      , ben_fname ||' '|| coalesce(ben_mname||' ','') || coalesce(ben_lname,'') as \"Name\"
      , a.bank_code  as \"Account_No\"
      , a.bank_ifsc as \"IFSC\",
      a.ration_card_cat as \"ration_card_cat\",a.ration_card_no as \"ration_card_no\",a.epic_voter_id as \"epic_voter_id\",a.aadhar_no as \"aadhar_no\"
      from " . $schema_name . ".beneficiary a left join
      (
      select district_code,district_name from m_district
      )d on a.created_by_dist_code=d.district_code 
      left join
      (
      select id,scheme_name from m_scheme
      )s on a.scheme_id=s.id 
      where next_level_role_id=0 and  concat(trim(ration_card_cat),trim(ration_card_no)) IN (" . implode(',', $where_in) . ") and $whereCondtion order by $duplicate_column,s.scheme_name,d.district_name,a.block_ulb_name,ben_fname";
      else
        $query = "select s.scheme_name,d.district_name,block_ulb_name || case when rural_urban_id=1 then ' Municipality' else '' end  \"Block_Municipality\"
                ,a.id as \"Beneficiary_Id\"
                , ben_fname ||' '|| coalesce(ben_mname||' ','') || coalesce(ben_lname,'') as \"Name\"
                , a.bank_code  as \"Account_No\"
                , a.bank_ifsc as \"IFSC\",
                a.ration_card_cat as \"ration_card_cat\",a.ration_card_no as \"ration_card_no\",a.epic_voter_id as \"epic_voter_id\",a.aadhar_no as \"aadhar_no\"
                from " . $schema_name . ".beneficiary a left join
                (
                select district_code,district_name from m_district
                )d on a.created_by_dist_code=d.district_code 
                left join
                (
                select id,scheme_name from m_scheme
                )s on a.scheme_id=s.id 
                where next_level_role_id=0 and  $duplicate_column IN (" . implode(',', $where_in) . ") and $whereCondtion order by $duplicate_column,s.scheme_name,d.district_name,a.block_ulb_name,ben_fname";
    }
    $data_part = DB::connection('pgsql_mis')->select($query);
    $data = array_merge($data, $data_part);
    //dd($data);
    $excel_data[] = array('Scheme Name', 'District Name', 'Block/Municipality', 'Beneficiary_Id', 'Name', 'IFSC', 'Account_No', 'Epic/Voter', 'Ration Card', 'Aadhar');
    foreach ($data as $row) {
      $excel_data[] = array(
        'Scheme Name'  => $row->scheme_name != '' ? $row->scheme_name : '',
        'District Name'  =>  $row->district_name != '' ? $row->district_name : '',
        'Block/Municipality'  =>  $row->Block_Municipality != '' ? $row->Block_Municipality : '',
        'Name'  =>  $row->Name != '' ? $row->Name : '',
        'Beneficiary_Id'  => $row->Beneficiary_Id,
        'IFSC'  =>  trim($row->IFSC) != '' ? trim($row->IFSC) : '',
        'Account_No'  =>  trim($row->Account_No) != '' ? trim($row->Account_No) : '',
        'Epic/Voter'  =>   trim($row->epic_voter_id) != '' ? trim($row->epic_voter_id) : '',
        'Ration Card'  =>  trim($row->ration_card_cat) != '' ? trim($row->ration_card_cat) : '' . '-' . trim($row->ration_card_no) == '' ? trim($row->ration_card_no) : '',
        'Aadhar'  =>   trim($row->aadhar_no) != '' ? trim($row->aadhar_no) : ''
      );
    }
    Excel::create('' . $title, function ($excel) use ($excel_data, $title, $scheme_name) {
      $excel->setTitle('' . $title);
      $excel->sheet('' . $scheme_name, function ($sheet) use ($excel_data) {
        $sheet->fromArray($excel_data, null, 'A1', false, false);
      });
    })->download('xlsx');
  }
  public function lotCheckExcelDownloadPost(Request $request)
  {
    $scheme_id = $request->scheme_id;
    $fin_year = $request->fin_year;
    $month = $request->month;
    $pre_payment_mode = $request->pre_payment_mode;
    $cur_payment_mode = $request->cur_payment_mode;
    $lot_no = $request->lot_no;
    $check_condition = $request->condition;
    $chek_condion_details = $this->get_condion_post_description($check_condition);

    $scheme_row = Scheme::select('scheme_name', 'id', 'short_code')->where('is_active', 1)->where('id', $scheme_id)->first();
    if (empty($scheme_row->short_code)) {
      return redirect("post-lot-consolidated-check")->with('error', 'Scheme Schema Not found');
    }
    $schema_name = $scheme_row->short_code;
    $scheme_name = $scheme_row->scheme_name;
    $title = $scheme_name . "_Post_" .  $fin_year . "_" . $month . "_" .  $cur_payment_mode . "_" . $chek_condion_details['name'];
    if ($request->cur_payment_mode == 'ifms') {
      $schema_name = 'ifms';
      $lot_column = 'drn_part';
      $ifsc_column = 'ifsc';
      $account_no_column = 'acc_no';
    } else {
      $schema_name = 'sbi';
      $lot_column = 'lot_no';
      $ifsc_column = 'ifsc_code_credit';
      $account_no_column = 'account_credit';
    }
    $data = array();
    if ($check_condition == 5) {
      // dd('ok');
      $Condition = $this->get_where_post_condition($check_condition, $scheme_id, $fin_year, $month, $pre_payment_mode, $cur_payment_mode, $lot_no);
      $whereCondtion = $Condition['where'];

      $query1 = "select distinct(A.pension_id) as pension_id from 
      " . $schema_name . ".transaction_lot_details as A LEFT JOIN
      public.lot_master as B ON  A.$lot_column=B.lot_no where $whereCondtion group by pension_id having count(*)>1";
      $data_part1 = DB::connection('pgsql_mis')->select($query1);
      $where_in = array();
      foreach ($data_part1 as $data_1) {
        //$where_in
        array_push($where_in, "'" . $data_1->pension_id . "'");
      }
      $query = "select s.scheme_name,A.pension_id,trim(A.$lot_column) as lot_no
      , trim(name) as name  
      ,trim(A.$account_no_column)  as account_no
      , trim(A.$ifsc_column) as ifsc
      from " . $schema_name . ".transaction_lot_details as A
      left join
      (
      select id,scheme_name from m_scheme
      )s on A.scheme_id=s.id 
      left join
      (
      select lot_no,lot_year,lot_month,scheme_id ,lot_type_id from lot_master
      )B on A.$lot_column=B.lot_no
      where  A.pension_id IN (" . implode(',', $where_in) . ") and $whereCondtion  order by s.scheme_name,name";
    }
    $data_part = DB::connection('pgsql_mis')->select($query);
    $data = array_merge($data, $data_part);
    $excel_data[] = array('Financial Year', 'Month',  'Lot Number', 'Scheme Name', 'Beneficiary_Id', 'Name', 'IFSC', 'Account_No');
    foreach ($data as $row) {
      $excel_data[] = array(
        'Financial Year'  =>  $fin_year != '' ? $fin_year : '',
        'Month'  =>  $month != '' ? $month : '',
        'Lot Number'  => $row->lot_no != '' ? $row->lot_no : '',
        'Scheme Name'  => $row->scheme_name != '' ? $row->scheme_name : '',
        'Beneficiary_Id'  => $row->pension_id,
        'Name'  =>  $row->name != '' ? $row->name : '',
        'Beneficiary_Id'  => $row->pension_id,
        'IFSC'  =>  trim($row->ifsc) != '' ? trim($row->ifsc) : '',
        'Account_No'  =>  trim($row->account_no) != '' ? trim($row->account_no) : ''
      );
    }
    Excel::create('' . $title, function ($excel) use ($excel_data, $title, $scheme_name) {
      $excel->setTitle('' . $title);
      $excel->sheet('' . $scheme_name, function ($sheet) use ($excel_data) {
        $sheet->fromArray($excel_data, null, 'A1', false, false);
      });
    })->download('xlsx');
  }
}
