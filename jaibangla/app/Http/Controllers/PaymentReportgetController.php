<?php

namespace App\Http\Controllers;
use App\Taluka;
use App\District;
use App\BeneficiaryPensions;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\Scheme;
//sayantika 21-03-2020
use App\UrbanBody;
use App\SubDistrict;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Validator;
use Illuminate\Http\Request;
use App\GP;
use App\Helpers\AuthChecker;

class PaymentReportgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
   
    private function getSchemaName($scheme_id) {
        if (!is_null($scheme_id)) {
          $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
          //$parameter['scheme_id'] = $scheme_id;
          $schema_name =  $sObj->short_code;
          //dd($schema_name);
          if (empty($schema_name)){
            $schema_name = 'pension';
          }
          $table_name =  strtolower($schema_name) . '.beneficiary';
        }
        else {
          $table_name =  'pension.beneficiary';
        }
        return $table_name;
      }
        public function index(Request $request){
            $c_time = Carbon::now();
            $c_date = $c_time->format("Y-m-d");
            $is_active = 0;
            $roleArray = $request->session()->get('role');
            $designation_id_old = Auth::user()->designation_id_old;
            $user_id = AuthChecker::getUserId();
            // echo $user_id;die();
            $district_visible = $is_urban_visible = $block_visible = 1;
            $municipality_visible = 0;
            $gp_ward_visible = 0;
            $muncList = collect([]);
            $gpList = collect([]);
            $duty = Configduty::where('user_id', '=', $user_id)->first();
            $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1 )"));
            if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
                $district_visible = $is_urban_visible = $block_visible = 1;
            } else if ($designation_id_old == 'Approver') {
                $district_code = NULL;
                $is_urban = NULL;
                $blockCode = NULL;
                foreach ($roleArray as $roleObj) {
                    // echo $designation_id_old;die();
                    if ($roleObj['scheme_id'] == 2) {
                        // echo $designation_id_old;die();
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
            }else {
                return redirect("/")->with('success', 'User Disabled. ');
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
                $gp_ward_visible = 1;
            } else {
                $block_munc_corp_code_fk = NULL;
                $gp_ward_visible = 0;
            }
            $districts = District::get();
            return view('payment-report.index',
            [
                'schemes' => $schemes,
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
                'gpList' => $gpList,
                'muncList' => $muncList,
                'designation_id_old'=>$designation_id_old,
                'c_date' => $c_date,
            ]
            );
        }

        public function getdataSBI(Request $request)
        {
            $scheme_id=$request->scheme_id;
            $mode=$request->mode;
            $fin_year=$request->fin_year;
            $fin_month=$request->fin_month;
            $lot_month=$request->lot_month;
            $lot_year=$request->lot_year;
            $district=$request->district;
            $urban_code=$request->urban_code;
            $schemeObj = Scheme::where('id', $scheme_id)->first();
            $c_time = Carbon::now();
            $c_date = $c_time->format("Y-m-d");
            $heading_msg = '';
            $title = "";
            if (!empty($district)) {
                $district_row = District::where('district_code', $district)->first();
            }
            $rules = [
                'scheme_id' => 'required|integer',
                'mode' =>'required',
                'district' => 'nullable|integer',
                'urban_code' => 'nullable|integer',
                'fin_year' => 'required',
                'fin_month' => 'required',
                'lot_month' =>'required',
                'lot_year' =>'required'
            ];
            $data = array();
            $column = "";
            $attributes = array();
            $messages = array();
            $attributes['scheme_id'] = 'Scheme';
            $attributes['district'] = 'District';
            $attributes['mode'] = 'Payment Mode';
            $attributes['fin_year'] = 'Finnancial Year';
            $attributes['fin_month'] = 'Finnancial Month';
            $attributes['lot_year'] = 'Lot Year';
            $attributes['lot_month'] = 'Lot Month';
            $attributes['urban_code'] = 'Rural/ Urban';
            $attributes['block'] = 'Block/Sub Division';
            $validator = Validator::make($request->all(), $rules, $messages, $attributes);
            if ($validator->passes()) {
              $explode_year = explode('-', $lot_year);
              $pension_search_year = $explode_year[0];
              if ($lot_month == 'January' || $lot_month == 'February' || $lot_month == 'March') {
                $pension_search_year = $pension_search_year + 1;
              }
              $pushed_date = Carbon::createFromFormat('F Y', $lot_month . ' ' . $pension_search_year);
              $pushed_at = $pushed_date->format('Y-m');
              // dd($pushed_at);
                $scheme_row=Scheme::where('id',$scheme_id)->first();
                $user_msg = $mode.' Payment Report for the Scheme '. $scheme_row->scheme_name;
                $title = $user_msg;
                $data = array();
                $return_status = 1;
                $return_msg = '';
                $heading_msg = '';
                $external = 0;
                $external_arr = array();
                $external_filter = array();
                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getSubDivWise($scheme_id,$fin_year,$fin_month, $district,$pushed_at, NULL, NULL, NULL);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getBlockWise($scheme_id,$fin_year,$fin_month,$district,$pushed_at, NULL, NULL, NULL);
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getBlockWise($scheme_id,$fin_year,$fin_month,$district,$pushed_at, NULL, NULL, NULL);
                        $data2 = $this->getSubDivWise($scheme_id,$fin_year,$fin_month,$district,$pushed_at, NULL, NULL, NULL);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getDistrictWise($scheme_id,$fin_year,$fin_month,$pushed_at,NULL, NULL, NULL, NULL);
                    $external = 0;
                }

            }else {
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

     public function getBlockWise($scheme_id,$fin_year,$fin_month,$district_code = NULL,$pushed_at, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL)
    {
      
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      $year_arr = explode('-', $fin_year);
      $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
      $archive_table = 'transaction_lot_details_report_' . $yyyy_val;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
        $query = "SELECT main.location_name, main.location_id,
        coalesce(count( l.pension_id) FILTER(WHERE l.lot_status <=6),0) as pushed_sbi,
        coalesce(count( l.pension_id) FILTER(WHERE l.lot_status<4),0) as sbi_under_process,
        coalesce(count( l.pension_id) FILTER(WHERE l.status_code='S00' and l.lot_status>3),0) as sbi_success,
        coalesce(count( l.pension_id) FILTER(WHERE l.status_code!='S00' and l.status_code is not null),0) as sbi_failed,
        coalesce(CAST(sum(l.credit_amount/100) FILTER (WHERE l.lot_status <= 6) AS INT), 0) as send_to_bank_amount,
        coalesce(CAST(sum(l.credit_amount/100) FILTER(WHERE l.status_code='S00' and l.lot_status>3)AS INT ),0) as amount_disbursed
        FROM (
              select block_code as location_id,block_name as location_name
              from public.m_block where district_code='" . $district_code . "'
          ) as main
        LEFT JOIN 
        (   
            select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code , tld.created_by_local_body_code
            from 
            (
                SELECT pension_id, scheme_id, credit_amount, status_code, lot_no, created_by_local_body_code FROM sbi.transaction_lot_details WHERE scheme_id=" . $scheme_id . " and ld_lot_month='" . $fin_month . "'  and created_by_dist_code='" . $district_code . "'
                UNION ALL
                SELECT pension_id, scheme_id, credit_amount, status_code, lot_no, created_by_local_body_code FROM sbi.  ".$archive_table."   WHERE scheme_id=" . $scheme_id . " and ld_lot_month='" . $fin_month . "' and created_by_dist_code='" . $district_code . "'
            ) tld 
            RIGHT JOIN sbi.transaction_lot tl
            ON tld.lot_no = tl.lot_no 
            AND tl.scheme_id = " . $scheme_id . "
            AND tl.lot_year= '" . $fin_year. "'
            AND tl.lot_month= '" . $fin_month . "'
            AND TO_CHAR(pushed_at::date, 'YYYY-MM')='". $pushed_at ."'
        ) l
        ON main.location_id = l.created_by_local_body_code
        GROUP BY main.location_name,main.location_id order by main.location_id";

          //  echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }

    public function getSubDivWise($scheme_id,$fin_year,$fin_month,$district_code = NULL,$pushed_at, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL)
    {
      
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      $year_arr = explode('-', $fin_year);
      $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
      $archive_table = 'transaction_lot_details_report_' . $yyyy_val;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
        $query = "SELECT main.location_name, main.location_id,
        coalesce(count( l.pension_id) FILTER(WHERE l.lot_status <=6),0) as pushed_sbi,
        coalesce(count( l.pension_id) FILTER(WHERE l.lot_status<4),0) as sbi_under_process,
        coalesce(count( l.pension_id) FILTER(WHERE l.status_code='S00' and l.lot_status>3),0) as sbi_success,
        coalesce(count( l.pension_id) FILTER(WHERE l.status_code!='S00' and l.status_code is not null),0) as sbi_failed,
        coalesce(CAST(sum(l.credit_amount/100) FILTER (WHERE l.lot_status <= 6) AS INT), 0) as send_to_bank_amount,
        coalesce(CAST(sum(l.credit_amount/100) FILTER(WHERE l.status_code='S00' and l.lot_status>3)AS INT ),0) as amount_disbursed
        FROM (
              select sub_district_code as location_id,sub_district_name as location_name
              from public.m_sub_district where district_code='" . $district_code . "'
          ) as main
        LEFT JOIN 
        (   
            select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code , tld.created_by_local_body_code
            from 
            (
                SELECT pension_id, scheme_id, credit_amount, status_code, lot_no, created_by_local_body_code FROM sbi.transaction_lot_details WHERE scheme_id=" . $scheme_id . " and ld_lot_month='" . $fin_month . "'  and created_by_dist_code='" . $district_code . "'
                UNION ALL
                SELECT pension_id, scheme_id, credit_amount, status_code, lot_no, created_by_local_body_code FROM sbi.  ".$archive_table."   WHERE scheme_id=" . $scheme_id . " and ld_lot_month='" . $fin_month . "' and created_by_dist_code='" . $district_code . "'
            ) tld 
            RIGHT JOIN sbi.transaction_lot tl
            ON tld.lot_no = tl.lot_no 
            AND tl.scheme_id = " . $scheme_id . "
            AND tl.lot_year= '" . $fin_year. "'
            AND tl.lot_month= '" . $fin_month . "'
            AND TO_CHAR(pushed_at::date, 'YYYY-MM')='". $pushed_at ."'
        ) l
        ON main.location_id = l.created_by_local_body_code
        GROUP BY main.location_name,main.location_id order by main.location_id";

        //  echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }

    public function getDistrictWise($scheme_id,$fin_year,$fin_month,$pushed_at,$district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL)
    {
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      $year_arr = explode('-', $fin_year);
      $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
      $archive_table = 'transaction_lot_details_report_' . $yyyy_val;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
        $query = "SELECT main.location_name, main.location_id,
        coalesce(count( l.pension_id) FILTER(WHERE l.lot_status <=6),0) as pushed_sbi,
        coalesce(count( l.pension_id) FILTER(WHERE l.lot_status<4),0) as sbi_under_process,
        coalesce(count( l.pension_id) FILTER(WHERE l.status_code='S00' and l.lot_status>3),0) as sbi_success,
        coalesce(count( l.pension_id) FILTER(WHERE l.status_code!='S00' and l.status_code is not null),0) as sbi_failed,
        coalesce(CAST(sum(l.credit_amount/100) FILTER (WHERE l.lot_status <= 6) AS INT), 0) as send_to_bank_amount,
        coalesce(CAST(sum(l.credit_amount/100) FILTER(WHERE l.status_code='S00' and l.lot_status>3)AS INT ),0) as amount_disbursed
        FROM (
              select district_code as location_id,district_name as location_name,district_order_by as district_order_by
              from public.m_district
          ) as main
        LEFT JOIN 
        (   
            select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code , tld.created_by_dist_code
            from 
            (
                SELECT pension_id, scheme_id, credit_amount, status_code, lot_no, created_by_dist_code FROM sbi.transaction_lot_details WHERE scheme_id=" . $scheme_id . " and ld_lot_month='" . $fin_month . "'
                UNION ALL
                SELECT pension_id, scheme_id, credit_amount, status_code, lot_no, created_by_dist_code FROM sbi.  ".$archive_table."   WHERE scheme_id=" . $scheme_id . " and ld_lot_month='" . $fin_month . "'
            ) tld 
            RIGHT JOIN sbi.transaction_lot tl
            ON tld.lot_no = tl.lot_no 
            AND tl.scheme_id = " . $scheme_id . "
            AND tl.lot_year= '" . $fin_year. "'
            AND tl.lot_month= '" . $fin_month . "'
            AND TO_CHAR(pushed_at::date, 'YYYY-MM')='". $pushed_at ."'
        ) l
        ON main.location_id = l.created_by_dist_code 
        GROUP BY main.location_name,main.location_id order by main.location_id";

          // echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }

    public function getdataIFMS(Request $request)
    {
      
      $scheme_id=$request->scheme_id;
      $mode=$request->mode;
      $district=$request->district;
      $fin_year=$request->fin_year;
      $fin_month=$request->fin_month;
      $lot_month=$request->lot_month;
      $lot_year=$request->lot_year;
      $urban_code=$request->urban_code;
      $schemeObj = Scheme::where('id', $scheme_id)->first();
      $c_time = Carbon::now();
      $c_date = $c_time->format("Y-m-d");
      $heading_msg = '';
      $title = "";
      if (!empty($district)) {
          $district_row = District::where('district_code', $district)->first();
      }
      $rules = [
          'scheme_id' => 'required|integer',
          'mode' =>'required',
          'district' => 'nullable|integer',
          'urban_code' => 'nullable|integer',
          'fin_year' => 'required',
          'fin_month' => 'required',
          'lot_month' =>'required',
          'lot_year' =>'required'

      ];
      $data = array();
      $column = "";
      $attributes = array();
      $messages = array();
      $attributes['scheme_id'] = 'Scheme';
      $attributes['district'] = 'District';
      $attributes['mode'] = 'Payment Mode';
      $attributes['fin_year'] = 'Finnancial Year';
      $attributes['fin_month'] = 'Finnancial Month';
      $attributes['lot_year'] = 'Lot Year';
      $attributes['lot_month'] = 'Lot Month';
      $attributes['urban_code'] = 'Rural/ Urban';
      $attributes['block'] = 'Block/Sub Division';
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $explode_year = explode('-', $lot_year);
        $pension_search_year = $explode_year[0];
        if ($lot_month == 'January' || $lot_month == 'February' || $lot_month == 'March') {
          $pension_search_year = $pension_search_year + 1;
        }
          $pushed_date = Carbon::createFromFormat('F Y', $lot_month . ' ' . $pension_search_year);
          $pushed_at = $pushed_date->format('Y-m');
          $scheme_row=Scheme::where('id',$scheme_id)->first();
          $user_msg = $mode.' Payment Report for the Scheme '. $scheme_row->scheme_name;
          $title = $user_msg;
          $data = array();
          $return_status = 1;
          $return_msg = '';
          $heading_msg = '';
          $external = 0;
          $external_arr = array();
          $external_filter = array();
          if (!empty($district)) {
              if ($urban_code == 1) {
                  $column = "Sub Division";
                  $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                  $data = $this->getSubDivWiseIFMS($scheme_id,$fin_year,$fin_month,$district,$pushed_at, NULL, NULL, NULL);
              } else if ($urban_code == 2) {
                  $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                  $column = "Block";
                  $data = $this->getBlockWiseIFMS($scheme_id,$fin_year,$fin_month,$district,$pushed_at, NULL, NULL, NULL);
              } else {
                  $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                  $column = "Block/Sub Division";
                  $data1 = $this->getBlockWiseIFMS($scheme_id,$fin_year,$fin_month,$district,$pushed_at, NULL, NULL, NULL);
                  $data2 = $this->getSubDivWiseIFMS($scheme_id,$fin_year,$fin_month,$district,$pushed_at, NULL, NULL, NULL);
                  $data = array_merge($data1, $data2);
              }
          } else {
              $column = "District";
              $heading_msg = 'District Wise ' . $user_msg;
              $data = $this->getDistrictWiseIFMS($scheme_id,$fin_year,$fin_month,$pushed_at,NULL, NULL, NULL, NULL);
              $external = 0;
          }

      }else {
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

    public function getBlockWiseIFMS($scheme_id,$fin_year,$fin_month,$district_code = NULL,$pushed_at, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL)
    {
      //  dd('okk');
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      $year_arr = explode('-', $fin_year);
      $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
      $archive_table = 'transaction_lot_details_report_' . $yyyy_val;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
        $query = "SELECT main.location_name, main.location_id,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (0,1,2) AND l.push_to_ifms_status=1),0) as pushed_ifms,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (1) AND l.status_code='IF'),0) as ifms_returned,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (0,2) AND l.ref_no>0),0) as mandate_generated,
        coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0,2) AND l.ref_no>0),0) as amount_booked,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (2) AND l.status_code='F' AND l.ref_no>0),0) as rbi_failed,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (0) AND l.status_code='S' AND l.ref_no>0),0) as rbi_success,
        coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0) AND l.status_code='S' AND l.ref_no>0),0) as amount_paid  
        FROM (
              select block_code as location_id,block_name as location_name
              from public.m_block where district_code='" . $district_code . "'
          ) as main
        LEFT JOIN 
        (   
          SELECT e.pension_id,e.scheme_id,lm.ref_no, e.wrongdata_flag, e.amount, lm.push_to_ifms_status, e.created_by_local_body_code, e.status_code
            from 
            (
              SELECT pension_id, scheme_id, wrongdata_flag, amount, drn_part, created_by_local_body_code,status_code FROM ifms.transaction_lot_details WHERE scheme_id=" . $scheme_id . " and ld_lot_month='" . $fin_month . "'
                UNION ALL
                SELECT pension_id, scheme_id, wrongdata_flag, amount, drn_part, created_by_local_body_code,status_code FROM ifms. ".$archive_table."  WHERE scheme_id=" . $scheme_id . " and ld_lot_month='" . $fin_month . "'
            ) e
            LEFT JOIN lot_master lm
            ON e.drn_part = lm.lot_no 
            AND lm.scheme_id = " . $scheme_id . "
            AND lm.lot_year= '" . $fin_year. "'
            AND lm.lot_month= '" . $fin_month . "'
            AND lm.push_to_ifms_status=1
            AND TO_CHAR(created_at::date, 'YYYY-MM')='". $pushed_at ."'
        ) l
        ON main.location_id = l.created_by_local_body_code 
        GROUP BY main.location_name,main.location_id order by main.location_id";

          //  echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        // dd($result);
        return $result;
    }

    public function getSubDivWiseIFMS($scheme_id,$fin_year,$fin_month,$district_code = NULL,$pushed_at, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL)
    {
     
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      $year_arr = explode('-', $fin_year);
      $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
      $archive_table = 'transaction_lot_details_report_' . $yyyy_val;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
        $query = "SELECT main.location_name, main.location_id,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (0,1,2) AND l.push_to_ifms_status=1),0) as pushed_ifms,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (1) AND l.status_code='IF'),0) as ifms_returned,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (0,2) AND l.ref_no>0),0) as mandate_generated,
        coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0,2) AND l.ref_no>0),0) as amount_booked,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (2) AND l.status_code='F' AND l.ref_no>0),0) as rbi_failed,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (0) AND l.status_code='S' AND l.ref_no>0),0) as rbi_success,
        coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0) AND l.status_code='S' AND l.ref_no>0),0) as amount_paid  
        FROM (
          select sub_district_code as location_id,sub_district_name as location_name
              from public.m_sub_district where district_code='" . $district_code . "'
          ) as main
        LEFT JOIN 
        (   
          SELECT e.pension_id,e.scheme_id,lm.ref_no, e.wrongdata_flag, e.amount, lm.push_to_ifms_status, e.created_by_local_body_code, e.status_code
            from 
            (
              SELECT pension_id, scheme_id, wrongdata_flag, amount, drn_part, created_by_local_body_code,status_code FROM ifms.transaction_lot_details WHERE scheme_id=" . $scheme_id . " and ld_lot_month='" . $fin_month . "'
                UNION ALL
                SELECT pension_id, scheme_id, wrongdata_flag, amount, drn_part, created_by_local_body_code,status_code FROM ifms. ".$archive_table."  WHERE scheme_id=" . $scheme_id . " and ld_lot_month='" . $fin_month . "'
            ) e
            LEFT JOIN lot_master lm
            ON e.drn_part = lm.lot_no 
            AND lm.scheme_id = " . $scheme_id . "
            AND lm.lot_year= '" . $fin_year. "'
            AND lm.lot_month= '" . $fin_month . "'
            AND TO_CHAR(created_at::date, 'YYYY-MM')='". $pushed_at ."' 
            AND lm.push_to_ifms_status=1
        ) l
        ON main.location_id = l.created_by_local_body_code 
        GROUP BY main.location_name,main.location_id order by main.location_id";

        //  echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        // dd($result);
        return $result;
    }

    public function getDistrictWiseIFMS($scheme_id,$fin_year,$fin_month,$pushed_at,$district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL)
    {
     
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      $year_arr = explode('-', $fin_year);
      $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
      $archive_table = 'transaction_lot_details_report_' . $yyyy_val;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
        $query = "SELECT main.location_name, main.location_id,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (0,1,2) AND l.push_to_ifms_status=1),0) as pushed_ifms,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (1) AND l.status_code='IF'),0) as ifms_returned,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (0,2) AND l.ref_no>0),0) as mandate_generated,
        coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0,2) AND l.ref_no>0),0) as amount_booked,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (2) AND l.status_code='F' AND l.ref_no>0),0) as rbi_failed,
        coalesce(COUNT(l.pension_id) FILTER(WHERE l.wrongdata_flag in (0) AND l.status_code='S' AND l.ref_no>0),0) as rbi_success,
        coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0) AND l.status_code='S' AND l.ref_no>0),0) as amount_paid
        FROM (
              select district_code as location_id,district_name as location_name,district_order_by as district_order_by
              from public.m_district
          ) as main
        LEFT JOIN 
        (   
          SELECT e.pension_id,e.scheme_id,lm.ref_no, e.wrongdata_flag, e.amount, lm.push_to_ifms_status, e.created_by_dist_code, e.status_code
            from 
            (
              SELECT pension_id, scheme_id, wrongdata_flag, amount, drn_part, created_by_dist_code,status_code FROM ifms.transaction_lot_details WHERE scheme_id=" . $scheme_id . " and ld_lot_month='" . $fin_month . "'
                UNION ALL
                SELECT pension_id, scheme_id, wrongdata_flag, amount, drn_part, created_by_dist_code,status_code FROM ifms. ".$archive_table."  WHERE scheme_id=" . $scheme_id . " and ld_lot_month='" . $fin_month . "'
            ) e
            LEFT JOIN lot_master lm
            ON e.drn_part = lm.lot_no 
            AND lm.scheme_id = " . $scheme_id . "
            AND lm.lot_year= '" . $fin_year. "'
            AND lm.lot_month= '" . $fin_month . "'
            AND lm.push_to_ifms_status=1
            AND TO_CHAR(created_at::date, 'YYYY-MM')='". $pushed_at ."'
        ) l
        ON main.location_id = l.created_by_dist_code 
        GROUP BY main.location_name,main.location_id order by main.location_id";

          // echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        // dd($result);
        return $result;
    }

}
