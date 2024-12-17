<?php


namespace App\Helpers;

use App\LotGenerationFunctionMaster;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\SchemeCapacity;
use App\Scheme;
use App\NextLevelRoleId;
use App\BlkUrbanlEntryMapping;
class Helper
{
    public static function getCapacity($scheme_id, $district)
    {
        //dd($scheme_id);
        $return_arr = array();
        $return_arr['visible'] = 0;
        /* $capacity = SchemeCapacity::select('capacity')->where('scheme_id', $scheme_id)->where('district_code', $district)->first();
        if (!empty($capacity->capacity)) {
            $return_arr['visible'] = 1;
            $return_arr['capacity'] = $capacity->capacity;
            $scheme = Scheme::select('id', 'scheme_name', 'short_code')->where('is_active', 1)->where('id', $scheme_id)->first();
            $scheme_schema_name = $scheme->short_code;
            if ($district == 0) {
                $total_data = DB::table($scheme_schema_name . '.beneficiaries')
                    ->selectRaw('count(id) as cnt')
                    ->where('next_level_role_id', '=', 0)
                    ->where('is_state', TRUE)->where(function ($query1) use ($scheme_id) {
                        if($scheme_id==2 || $scheme_id==10 || $scheme_id==11)
                        $query1->where('wt_special', 0);
                    })->first();
            } else {
                if($scheme_id==10){
                    $total_data = DB::table($scheme_schema_name . '.beneficiaries')
                    ->selectRaw('count(id) as cnt')
                    ->where('next_level_role_id', '=', 0)
                    ->where('is_state', FALSE)->whereNull('is_lb_imported')->whereNull('dept_special')
                    ->where('created_by_dist_code', $district)->where(function ($query1) use ($scheme_id) {
                        if($scheme_id==2 || $scheme_id==10 || $scheme_id==11)
                        $query1->where('wt_special', 0);
                    })->first();
                }
                else{
                $total_data = DB::table($scheme_schema_name . '.beneficiaries')
                    ->selectRaw('count(id) as cnt')
                    ->where('next_level_role_id', '=', 0)
                    ->where('is_state', FALSE)
                    ->where('created_by_dist_code', $district)->where(function ($query1) use ($scheme_id) {
                        if($scheme_id==2 || $scheme_id==10 || $scheme_id==11)
                        $query1->where('wt_special', 0);
                    })->first();
                }
            }
            $return_arr['total_data'] = $total_data->cnt;
        } else {
            $return_arr['visible'] = 0;
        }
        */
        return $return_arr;
    }
    public static function getCapacityWtQuota($scheme_id, $district,$ulb_code)
    {
        //dd($ulb_code);
        $return_arr = array();
        $capacity = BlkUrbanlEntryMapping::select('capacity','special_capacity')->where('scheme_id', $scheme_id)->where('district_code', $district)->where('block_ulb_code', $ulb_code)->first();
       // dd($capacity->toArray());
        if (!empty($capacity->special_capacity)) {
            $return_arr['visible'] = 1;
            $return_arr['capacity'] = $capacity->special_capacity;
            $scheme = Scheme::select('id', 'scheme_name', 'short_code')->where('is_active', 1)->where('id', $scheme_id)->first();
            $scheme_schema_name = $scheme->short_code;
            if ($district == 0) {
                $total_data = DB::table($scheme_schema_name . '.beneficiaries')
                    ->selectRaw('count(id) as cnt')
                    ->where('next_level_role_id', '=', 0)
                    ->where('is_state', TRUE)->where('wt_special', 1)
                    ->first();
            } else {
                if($scheme_id==10){
                    $total_data = DB::table($scheme_schema_name . '.beneficiaries')
                    ->selectRaw('sum(case when next_level_role_id=0 then 1 else 0 end) approved,
                 sum(case when next_level_role_id>0  or next_level_role_id IS NULL then 1 else 0 end) pending')
                    ->whereNull('is_lb_imported')->whereNull('dept_special')->where('created_by_dist_code', $district)->where('created_by_local_body_code', $ulb_code)->where('wt_special', 1)
                    ->first();
                }
                else{
                    $total_data = DB::table($scheme_schema_name . '.beneficiaries')
                    ->selectRaw('sum(case when next_level_role_id=0 then 1 else 0 end) approved,
                 sum(case when next_level_role_id>0  or next_level_role_id IS NULL then 1 else 0 end) pending')
                    ->where('created_by_dist_code', $district)->where('created_by_local_body_code', $ulb_code)->where('wt_special', 1)
                    ->first();
                }
                
            }
            $return_arr['approved'] = intval($total_data->approved);
            $return_arr['pending'] = intval($total_data->pending);
        } else {
            $return_arr['visible'] = 0;
        }
        return $return_arr;
    }
    public static function getCapacityWtQuotaDistrict($scheme_id, $district)
    {
        //dd($scheme_id);
        $return_arr = array();
        $sum = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('district_code', $district)->sum('special_capacity');
        if ($sum>0) {
            $return_arr['visible'] = 1;
            $return_arr['capacity'] = $sum;
            $scheme = Scheme::select('id', 'scheme_name', 'short_code')->where('is_active', 1)->where('id', $scheme_id)->first();
            $scheme_schema_name = $scheme->short_code;
            $total_data = DB::table($scheme_schema_name . '.beneficiaries')
                    ->selectRaw('sum(case when next_level_role_id=0 then 1 else 0 end) approved,
                 sum(case when next_level_role_id>0  or next_level_role_id IS NULL then 1 else 0 end) pending')
                    ->where('created_by_dist_code', $district)->where('wt_special', 1)
                    ->first();
            $return_arr['approved'] = intval($total_data->approved);
            $return_arr['pending'] = intval($total_data->pending);
        } else {
            $return_arr['visible'] = 0;
        }
        return $return_arr;
    }
    public static function get_paid_yymm($select_month, $select_year)
    {
        $monthlist = Config::get('constants.month_list');
        foreach ($monthlist as $key => $monthlistVal) {
            if ($select_month == $monthlistVal) {

                if ($monthlistVal == "January" || $monthlistVal == "February" || $monthlistVal == "March") {
                    $paid_yymm = substr($select_year, 7, 2) . $key;
                } else {
                    $paid_yymm = substr($select_year, 2, 2) . $key;
                }
            }
        }
        return $paid_yymm;
    }

    public static function getMonthValue($select_month, $select_year)
    {
        //echo $select_month;die;
        // $select_month = "March";
        $previousValue = "";
        $month_arr = array();
        //  echo $select_year;die;
        $monthlist = Config::get('constants.month_list');
        foreach ($monthlist as $key => $monthlistVal) {

            $ar = $monthlistVal;
            if ($select_month == $monthlistVal) {

                if ($monthlistVal == "February" || $monthlistVal == "March") {
                    $month_arr['month'] = $previousValue;
                    $month_arr['chk_paid_yymm'] = substr($select_year, 7, 2) . $key - 1;
                } else {
                    if ($previousValue == "") {
                        $month_arr['month'] = 'December';
                        $month_arr['chk_paid_yymm'] = substr($select_year, 2, 2) . "12";
                    } else {
                        $month_arr['month'] = $previousValue;
                        $month_arr['chk_paid_yymm'] = substr($select_year, 2, 2) . $key - 1;
                    }
                }

                //array_push($month_arr,$nested);
            }

            $previousValue = $ar;
        }
        return $month_arr;
        // if ($select_month == 'June') {
        // 	$month = 'May';
        // 	$chk_paid_yymm=substr($select_year,2,2).'05';
        // }
        // else if ($select_month == 'July') {
        // 	$month = 'June';
        // 	$chk_paid_yymm=substr($select_year,2,2).'06';
        // }
        // else if ($select_month == 'August') {
        //     $month = 'July'; $chk_paid_yymm=substr($select_year,2,2).'07';
        // }else if ($select_month == 'September') {
        //     $month = 'August'; $chk_paid_yymm=substr($select_year,2,2).'08';
        // }else if ($select_month == 'October') {
        //     $month = 'September'; $chk_paid_yymm=substr($select_year,2,2).'09';
        // }else if ($select_month == 'November') {
        //     $month = 'October'; $chk_paid_yymm=substr($select_year,2,2).'10';
        // }else if ($select_month == 'December') {
        //     $month = 'November'; $chk_paid_yymm=substr($select_year,2,2).'11';
        // }else if ($select_month == 'January') {
        //     $month = 'December'; $chk_paid_yymm=substr($select_year,2,2).'12';
        // }else if ($select_month == 'February') {
        //     $month = 'January'; $chk_paid_yymm=substr($select_year,7,2).'01';
        // }else if ($select_month == 'March') {
        //     $month = 'February'; $chk_paid_yymm=substr($select_year,7,2).'02';
        // }else if ($select_month == 'April') {
        //     $month = 'March'; $chk_paid_yymm=substr($select_year,2,2).'03';
        // }else if ($select_month == 'May') {
        //     $month = 'April'; $chk_paid_yymm=substr($select_year,2,2).'04';
        // }
        //$data=array('month'=>$month,'chk_paid_yymm'=>$chk_paid_yymm);
        // return $data;
    }



    public static function getLotFunction($in_lot_no, $lot_year, $lot_month, $scheme_id, $chk_paid_yymm, $select_pmt_mode, $select_target_mode, $lot_type, $select_category, $lot_size)
    {


        try {
            // $dbfunc = DB::statement('SELECT abc_cmw(?, ?, ?)', [$param1, $param2, $param3]);
            // echo 'SOURCE helper:' . $select_pmt_mode;
            $getFuncName = LotGenerationFunctionMaster::where('lot_type_id', $lot_type)->where('target_payment_mode', $select_target_mode);
            /*suman
  if ($lot_type == "2") {
      $getFuncName = $getFuncName->where('source_payment_mode', $select_pmt_mode);
  }*/

            // foreach($getFuncName->input_parameters){

            // }
            $getFuncName = $getFuncName->first();
            // echo $getFuncName;die;
            //echo 'ok';die;
            if (!empty($getFuncName)) {
                $explodeInputParameters = explode(",", $getFuncName->input_parameters);
                // echo "<pre>";
                // print_r($explodeInputParameters);
                $paramAppend = "";
                foreach ($explodeInputParameters as $key => $param) {
                    // echo 'stage 1';
                    switch ($param) {
                        case "in_source":
                            $paramAppend .= "'" . $select_pmt_mode . "'" . ",";
                            break;
                        case "in_lot_type":
                            $paramAppend .= $lot_type . ",";
                            break;
                        case "in_scheme_id":
                            $paramAppend .= $scheme_id . ",";
                            break;
                        case "in_lot_no":
                            $paramAppend .= 'ARRAY[' . "'$in_lot_no'" . '],';
                            break;

                        case ($param == "in_fin_year"):
                            $paramAppend .= "'" . $lot_year . "'" . ",";
                            break;

                        case ($param == "in_lot_month"):
                            $paramAppend .= "'" . $lot_month . "'" . ",";
                            break;

                        case ($param == "in_chk_paid_yymm"):
                            $paramAppend .=  $chk_paid_yymm  . ",";
                            break;
                        case ($param == "in_category"):
                            $paramAppend .= "'" . $select_category . "'" . ",";
                            break;
                        case ($param == "in_lot_size"):
                            $paramAppend .= $lot_size . ",";
                            break;
                    }
                }
                $finalQuery = rtrim($paramAppend, ",");
                // echo "select " . $getFuncName->function_name . "(" . $finalQuery . ")"; die;
                //$func_call = DB::statement("select " . $getFuncName->function_name . "(" . $finalQuery . ")");


                //$func_call=DB::statement('SELECT public.test(?, ?, ?, ?)', [$lot_year,$lot_month, $scheme_id, $lot_size]);
                //  $func_call= DB::select(DB::raw('SELECT public.test('."$lot_year","$lot_month", $scheme_id, $lot_size.')'));

                $func_call = DB::select(DB::raw("select " . $getFuncName->function_name . "(" . $finalQuery . ")"));
                //echo print_r($func_call);
                $explode_func_name = explode(".", $getFuncName->function_name);

                $func_name = $explode_func_name[1];
                $lotno = $func_call[0]->$func_name;
                $response = array('lotno' => $lotno);
            } else {
                $lotno = 0;
                $response = array('lotno' => $lotno);

                return $response;
            }
        } catch (\Exception $e) {
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
            );
        } finally {
            return $response;
        }
    }
    public static function getFinanceyear($select_month, $select_year)
    {
        $financial_year = '';
        $monthlist = Config::get('constants.monthval');
        foreach ($monthlist as $key => $monthlistVal) {
            if ($select_month == $monthlistVal) {
                $loopkey = $key;
                if ($loopkey == 4) {
                    $explodeyear = explode("-", $select_year);
                    $firstrecord = $explodeyear[0];
                    // $secondrecord= $explodeyear[1];
                    $newfinancial_year = ($firstrecord - 1) . '-' . ($firstrecord);
                    $financial_year = $newfinancial_year;
                }
                // else if($loopkey>4){
                //     $explodeyear=explode("-",$select_year);
                //     $firstrecord= $explodeyear[0];
                //     $secondrecord= $explodeyear[1];
                //     $newfinancial_year=$firstrecord.'-'. ($firstrecord+1);
                //     $financial_year = $newfinancial_year;
                // }
                else {
                    $financial_year = $select_year;
                }
            }
        }
        return $financial_year;
    }

    public static function getConvertedfinYear($temp_year)
    {
        $t_arr = explode('-', $temp_year);
        $table_append_year = '_' . $t_arr[0] . '_' . $t_arr[1];

        return $table_append_year;
    }

    public static function getLastPaidyymm($select_month, $yearVal)
    {
        $date = date_parse($select_month);
        $monthNumber = $date['month'];
        if ($monthNumber < 10) {
            $finalmonthNumber = '0' . $monthNumber;
        } else {
            $finalmonthNumber = $monthNumber;
        }
        $lastpaidyym = (substr($yearVal, 2)) . $finalmonthNumber;
        return  $lastpaidyym;
    }
    public static function getCheckNextLevelRoleIdCon($scheme_id)
    {
        $condition = NextLevelRoleId::where('status', 1)->get();
        $check_condition_str = '';
        $i = 1;
        if (count($condition) > 0) {
            foreach ($condition as $cond) {
                $check_condition_str =  $check_condition_str . $cond->col_name . ' ' . $cond->condition;
                $check_condition_str =  $check_condition_str .  ' ';
                if ($i != count($condition)) {
                    $check_condition_str =  $check_condition_str . 'or' . ' ';
                }
                $i++;
            }
        }

        return $check_condition_str;
    }

    public static function getSBISftpServer() {
        return 'sftp_sbi_pr';
    }

    public static  function getMonthColumn($integerValue)
    { 
        //echo $integerValue;die;
        if (is_numeric($integerValue)) {
            $month_number = intval($integerValue);
            if ($month_number >= 1 && $month_number <= 12) {
                $month_val = $month_number;
            } 
        } else {
            $month_number = date('n', strtotime($integerValue));
            $month_val = $month_number;
        }

        switch ($month_val) {
            case "1":
                $lot_column = 'jan_lot_no';
                $lot_status = 'jan_lot_status';
                $lot_type = 'jan_lot_type';
                $lot_eligible = 'jan_is_eligible';
                $lot_eligible_amount = 'jan_eligible_amount';
                $lot_payment_amount = 'jan_payment_amount';
                break;
            case "2":
                $lot_column = 'feb_lot_no';
                $lot_status = 'feb_lot_status';
                $lot_type = 'feb_lot_type';
                $lot_eligible = 'feb_is_eligible';
                $lot_eligible_amount = 'feb_eligible_amount';
                $lot_payment_amount = 'feb_payment_amount';
                break;
            case "3":
                $lot_column = 'mar_lot_no';
                $lot_status = 'mar_lot_status';
                $lot_type = 'mar_lot_type';
                $lot_eligible = 'mar_is_eligible';
                $lot_eligible_amount = 'mar_eligible_amount';
                $lot_payment_amount = 'mar_payment_amount';
                break;
            case "4":
                $lot_column = 'apr_lot_no';
                $lot_status = 'apr_lot_status';
                $lot_type = 'apr_lot_type';
                $lot_eligible = 'apr_is_eligible';
                $lot_eligible_amount = 'apr_eligible_amount';
                $lot_payment_amount = 'apr_payment_amount';
                break;
            case "5":
                $lot_column = 'may_lot_no';
                $lot_status = 'may_lot_status';
                $lot_type = 'may_lot_type';
                $lot_eligible = 'may_is_eligible';
                $lot_eligible_amount = 'may_eligible_amount';
                $lot_payment_amount = 'may_payment_amount';
                break;
            case "6":
                $lot_column = 'jun_lot_no';
                $lot_status = 'jun_lot_status';
                $lot_type = 'jun_lot_type';
                $lot_eligible = 'jun_is_eligible';
                $lot_eligible_amount = 'jun_eligible_amount';
                $lot_payment_amount = 'jun_payment_amount';
                break;
            case "7":
                $lot_column = 'jul_lot_no';
                $lot_status = 'jul_lot_status';
                $lot_type = 'jul_lot_type';
                $lot_eligible = 'jul_is_eligible';
                $lot_eligible_amount = 'jul_eligible_amount';
                $lot_payment_amount = 'jul_payment_amount';
                break;
            case "8":
                $lot_column = 'aug_lot_no';
                $lot_status = 'aug_lot_status';
                $lot_type = 'aug_lot_type';
                $lot_eligible = 'aug_is_eligible';
                $lot_eligible_amount = 'aug_eligible_amount';
                $lot_payment_amount = 'aug_payment_amount';
                break;
            case "9":
                $lot_column = 'sep_lot_no';
                $lot_status = 'sep_lot_status';
                $lot_type = 'sep_lot_type';
                $lot_eligible = 'sep_is_eligible';
                $lot_eligible_amount = 'sep_eligible_amount';
                $lot_payment_amount = 'sep_payment_amount';
                break;
            case "10":
                $lot_column = 'oct_lot_no';
                $lot_status = 'oct_lot_status';
                $lot_type = 'oct_lot_type';
                $lot_eligible = 'oct_is_eligible';
                $lot_eligible_amount = 'oct_eligible_amount';
                $lot_payment_amount = 'oct_payment_amount';
                break;
            case "11":
                $lot_column = 'nov_lot_no';
                $lot_status = 'nov_lot_status';
                $lot_type = 'nov_lot_type';
                $lot_eligible = 'nov_is_eligible';
                $lot_eligible_amount = 'nov_eligible_amount';
                $lot_payment_amount = 'nov_payment_amount';
                break;
            case "12":
                $lot_column = 'dec_lot_no';
                $lot_status = 'dec_lot_status';
                $lot_type = 'dec_lot_type';
                $lot_eligible = 'dec_is_eligible';
                $lot_eligible_amount = 'dec_eligible_amount';
                $lot_payment_amount = 'dec_payment_amount';
                break;
            case "13":
                $lot_column = 'arrear_lot_no';
                $lot_status = 'arrear_lot_status';
                $lot_type = 'arrear_lot_type';
                $lot_eligible = 'arrear_is_eligible';
                $lot_eligible_amount = 'arrear_eligible_amount';
                $lot_payment_amount = 'arrear_payment_amount';
                break;
            default:
                $lot_column = '';
                $lot_status = '';
                $lot_type = '';
        }
        $response = array('lot_status' => $lot_status, 'lot_column' => $lot_column, 'lot_type' => $lot_type, 'lot_eligible' => $lot_eligible, 'lot_eligible_amount' => $lot_eligible_amount, 'lot_payment_amount' => $lot_payment_amount);
        return $response;
    }

    public static  function getSchemaName($scheme_id)
  {
    if (!is_null($scheme_id)) {
      $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
      //$parameter['scheme_id'] = $scheme_id;
      $schema_name =  $sObj->short_code;
      //dd($schema_name);
      if (empty($schema_name)) {
        $schema_name = 'pension';
      }
      $table_name =  strtolower($schema_name) . '.beneficiaries';
    } else {
      $table_name =  'pension.beneficiaries';
    }
    return $table_name;
  }


  
}
