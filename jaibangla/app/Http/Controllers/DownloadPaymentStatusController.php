<?php

namespace App\Http\Controllers;

use App\Configduty;
use App\District;
use App\GP;
use App\Helpers\AuthChecker;
use App\Taluka;
use App\Scheme;
use App\UrbanBody;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\Helper;
use Carbon\Carbon;
use App\SubDistrict;
use Illuminate\Support\Facades\Validator;
use App\Ward;
use Exception;






class DownloadPaymentStatusController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(600);
  }

  public function index()
  {
    $user_id = AuthChecker::getUserId();
    $mapObj = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->pluck('scheme_id');
    $scheme = Scheme::whereIn('id', $mapObj)->get();
    $is_urbanObj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
    if ($is_urbanObj->is_urban == 1) {
      $block_ulb = $is_urbanObj->urban_body_code;
    } else {
      $block_ulb = $is_urbanObj->taluka_code;
    }
    return view('download-payment-status.index', ['schemes' => $scheme, 'rural_urban' => $block_ulb, 'is_urban' => $is_urbanObj->is_urban]);
  }

  public function paymentStatusGenerateExcel(Request $request)
  {
    dd('Not allowd!!');
    $this->validate($request, [
      'scheme' => 'required|not-in:0',
      'fin_year' => 'required|not-in:0',
      'month' => 'required|not-in:0'
    ]);

    $scheme = $request->scheme;
    $schemeObj = Scheme::where('id', $scheme)->first();
    $fin_year = $request->fin_year;
    $month = $request->month;
    $rural_urban = $request->rural_urban;
    $year_arr = explode('-', $fin_year);
    $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
    $table_name = 'pension.beneficiary';
    if ($schemeObj->short_code != '') {
      $table_name = strtolower($schemeObj->short_code) . '.beneficiary';
    }

    if ($request->is_urban == 2) {
      $blockObj = Taluka::where('block_code', $rural_urban)->first();
      $block_ulb_name = $blockObj->block_name;
    } else {
      $ulbObj = UrbanBody::where('sub_district_code', $rural_urban)->first();
      $block_ulb_name = $ulbObj->urban_body_name;
    }

    $ben_success = [];
    $ben_error = [];
    $query = "(select be.pension_id,be.name,lm.lot_month,lm.lot_year,be.acc_no as account_no,be.ifsc as ifsc,be.mobile_no,
            (case when lm.lot_status=0  then 'payment processed' else 'under process' end) process_status,
            (case when be.wrongdata_flag=0 then 'payment success' else 'payment error' end) payment_status,
            be.utr_no,(case when be.wrongdata_flag=0 then null else be.ifms_status end) error_reason 

            from ifms.transaction_lot_details be,lot_master lm
            where lm.lot_no=be.drn_part and lm.lot_year='" . $fin_year . "' and lm.lot_month='" . $month . "' and be.pension_id in 
            (select id from " . $table_name . " where created_by_local_body_code=" . $rural_urban . " and next_level_role_id in (0,-2,-99) and scheme_id=" . $scheme . ")
            order by be.pension_id,be.name,lm.lot_year,lm.lot_month)
            union
            (select tld.pension_id,tld.name,tl.lot_month,tl.lot_year,tld.account_credit as account_no,tld.ifsc_code_credit as ifsc,null as mobile_no,
            (case when tl.lot_status=6  then 'payment processed' else 'under process' end) process_status,
            (case when tld.status_code='S00' then 'payment success' else (case when tld.status_code is null then 'payment under process' else 'payment error' end)end) payment_status,
            credit_payment_reference as utr_no,(case when tld.status_code is not null and tld.status_code<>'S00' then tc.description end) as error_reason
            
            from sbi.transaction_lot_details tld,sbi.transaction_lot tl,sbi.credit_transaction_code tc
            where tl.lot_no=tld.lot_no and tl.lot_year='" . $fin_year . "' and tl.lot_month='" . $month . "' and tld.pension_id in 
            (select id from " . $table_name . " where created_by_local_body_code=" . $rural_urban . " and next_level_role_id=0 and scheme_id=" . $scheme . ") and tld.status_code=tc.code 
            order by tld.pension_id,tl.lot_year,tl.lot_month)
            union
            (select be.pension_id,be.name,lm.lot_month,lm.lot_year,be.acc_no as account_no,be.ifsc as ifsc,be.mobile_no,
            (case when lm.lot_status=0  then 'payment processed' else 'under process' end) process_status,
            (case when be.wrongdata_flag=0 then 'payment success' else 'payment error' end) payment_status,
            be.utr_no,(case when be.wrongdata_flag=0 then null else be.ifms_status end) error_reason 

            from ifms.transaction_lot_details_report_" . $yyyy_val . " be,lot_master lm
            where lm.lot_no=be.drn_part and lm.lot_year='" . $fin_year . "' and lm.lot_month='" . $month . "' and be.pension_id in 
            (select id from " . $table_name . " where created_by_local_body_code=" . $rural_urban . " and next_level_role_id in (0,-2,-99) and scheme_id=" . $scheme . ")
            order by be.pension_id,be.name,lm.lot_year,lm.lot_month)
            union
            (select tld.pension_id,tld.name,tl.lot_month,tl.lot_year,tld.account_credit as account_no,tld.ifsc_code_credit as ifsc,null as mobile_no,
            (case when tl.lot_status=6  then 'payment processed' else 'under process' end) process_status,
            (case when tld.status_code='S00' then 'payment success' else (case when tld.status_code is null then 'payment under process' else 'payment error' end)end) payment_status,
            credit_payment_reference as utr_no,(case when tld.status_code is not null and tld.status_code<>'S00' then tc.description end) as error_reason
            
            from sbi.transaction_lot_details_report_" . $yyyy_val . " tld,sbi.transaction_lot tl,sbi.credit_transaction_code tc
            where tl.lot_no=tld.lot_no and tl.lot_year='" . $fin_year . "' and tl.lot_month='" . $month . "' and tld.pension_id in 
            (select id from " . $table_name . " where created_by_local_body_code=" . $rural_urban . " and next_level_role_id=0 and scheme_id=" . $scheme . ") and tld.status_code=tc.code 
            order by tld.pension_id,tl.lot_year,tl.lot_month)";

    // print $query;die(); 
    $result = DB::select(DB::raw($query));
    // $result = DB::connection('pgsql_mis')->select($query);
    //print_r($result);

    $ben_success[] = array('Pension Id', 'Name', 'Month', 'Year', 'Account No', 'IFSC Code', 'Mobile No', 'Process Status', 'Payment Status', 'UTR');
    $ben_error[] = array('Pension Id', 'Name', 'Month', 'Year', 'Account No', 'IFSC Code', 'Mobile No', 'Process Status', 'Payment Status', 'Error Reason');
    foreach ($result as $res) {
      if ($res->payment_status == 'payment success') {
        $ben_success[] = array(
          'Pension Id' => $res->pension_id,
          'Name'  => trim($res->name),
          'Month'  => $res->lot_month,
          'Year'   => $res->lot_year,
          'Account No' => $res->account_no,
          'IFSC Code' => $res->ifsc,
          'Mobile No' => $res->mobile_no,
          'Process Status' => $res->process_status,
          'Payment Status' => $res->payment_status,
          'UTR' => $res->utr_no
        );
      } else {
        $ben_error[] = array(
          'Pension Id' => $res->pension_id,
          'Name'  => trim($res->name),
          'Month'  => $res->lot_month,
          'Year'   => $res->lot_year,
          'Account No' => $res->account_no,
          'IFSC Code' => $res->ifsc,
          'Mobile No' => $res->mobile_no,
          'Process Status' => $res->process_status,
          'Payment Status' => $res->payment_status,
          'Error Reason' => $res->error_reason
        );
      }
    }

    Excel::create($block_ulb_name . ' ' . $schemeObj->scheme_name . ' ' . $month . ' ' . $fin_year . ' Payment Status', function ($excel) use ($ben_success, $ben_error) {
      $excel->setTitle('Payment Status');
      $excel->sheet('Payment Success', function ($sheet) use ($ben_success) {
        $sheet->fromArray($ben_success, null, 'A1', false, false);
      });
      $excel->sheet('Payment Error', function ($sheet) use ($ben_error) {
        $sheet->fromArray($ben_error, null, 'A1', false, false);
      });
    })->download('xlsx');
    return redirect('download-payment-status')->with('success', 'Download Successfully');
  }

  /* New Section Date: 17-08-2022 (Only For HOD and DDO) */
  public function getPayeeListIndex(Request $request)
  {
    $userId = AuthChecker::getUserId();
    $sceme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (13, 5,2,10,11) and id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
    // dd($sceme_list);
    $base_date  = '2021-08-16';
    $c_time = Carbon::now();
    $c_date = $c_time->format("Y-m-d");
    $is_active = 0;
    $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();;
    $designation_id = Auth::user()->designation_id;
    $district_visible = $is_urban_visible = $block_visible = 0;
    $municipality_visible = 0;
    $gp_ward_visible = 0;
    $muncList = collect([]);
    $gpList = collect([]);
    if (AuthChecker::AdminChecker() || AuthChecker::HODChecker() ||  AuthChecker::DashboardChecker() ) {
      $district_visible = 1;
    } else if (AuthChecker::ApproverPermission() || AuthChecker::VerifierPermission() || AuthChecker::StatusCheckerDistrictChecker() || AuthChecker::StatusCheckerFieldChecker()) {
      $is_urban_visible = $block_visible = 1;
      $district_code = NULL;
      $is_urban = NULL;
      $blockCode = NULL;
      foreach ($roleArray as $roleObj) {
        if (in_array($roleObj['scheme_id'], array(13, 5,2,10,11))) {
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
    // dd($is_urban_visible);

    return view(
      'download-payment-status.report_index',
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
        'base_date' => $base_date,
        'c_date' => $c_date,
        'gpList' => $gpList,
        'muncList' => $muncList
      ]
    );
  }

  public function getPayeeListGetData(Request $request)
  {
    try {
      ini_set('memory_limit', '512M');
      $scheme_id = $request->scheme_id;
      $lot_year = $request->lot_year;
      $lot_month = $request->lot_month;
      $district = $request->district;
      $block = $request->block;
      $rural_urban_id = $request->urban_code;
      $data = $this->getSuccessData($scheme_id, $lot_year, $lot_month, $district, $block, $rural_urban_id, 0);
      return datatables()->of($data)
        ->addIndexColumn()
        ->make(true);
    } catch (Exception $e) {
      //  dd($e);
      return redirect("/")->with('success', 'Somthing went wrong.. ');
    }
  }

  public function getPayeeListGetDataExcel(Request $request)
  {
    // dd($request->all());
    try {
      ini_set('memory_limit', '512M');
      $scheme_id = $request->scheme_id;
      $lot_year = $request->lot_year;
      $lot_month = $request->lot_month;
      $district = $request->district;
      $block = $request->block;
      $rural_urban_id = $request->urban_code;
      $schemeObj = Scheme::where('id', $scheme_id)->first();
      $is_excel = 1;
      if (!empty($district)) {
        $is_excel = 0;
      }
      $data = $this->getSuccessData($scheme_id, $lot_year, $lot_month, $district, $block, $rural_urban_id, $is_excel);
      // dd($data);
      if ($is_excel == 1) {
        $ben_success[] = array('Pension Id', 'Name', 'Month', 'Year', 'Account No', 'IFSC Code', 'Amount(Rs.)', 'Payment Status');
        // $i = 1;
        foreach ($data as $res) {
          // $maskedAccountNo = (strlen($res->account_no)>=4) ? str_repeat('*', strlen($res->account_no) - 4) . substr($res->account_no, -4) : $res->account_no;
          // $maskedAccountNo = $res->account_no;
          $ben_success[] = array(
            // 'Sl No' => $i++,
            // 'District' => $res->district_name,
            // 'Block/Municipality'=>trim($res->block_ulb_name),
            'Pension Id' => $res->ben_id,
            'Name'  => trim($res->ben_name),
            'Month'  => $res->lot_month,
            'Year'   => $res->fin_year,
            'Account No' => $res->account_no,
            'IFSC Code' => $res->ifsc,
            'Amount(Rs.)' => $res->amount_rs,
            'Payment Status' => $res->payment_status
          );
        }
      } else {
        $ben_success[] = array('District', 'Block/Municipality', 'Pension Id', 'Name', 'Month', 'Year', 'Account No', 'IFSC Code', 'Amount(Rs.)', 'Payment Status');
        // $i = 1;
        foreach ($data as $res) {
          // $maskedAccountNo = (strlen($res->account_no)>=4) ? str_repeat('*', strlen($res->account_no) - 4) . substr($res->account_no, -4) : $res->account_no;
          // $maskedAccountNo = $res->account_no;
          $ben_success[] = array(
            // 'Sl No' => $i++,
            'District' => $res->district_name,
            'Block/Municipality' => trim($res->block_ulb_name),
            'Pension Id' => $res->ben_id,
            'Name'  => trim($res->ben_name),
            'Month'  => $res->lot_month,
            'Year'   => $res->fin_year,
            'Account No' => $res->account_no,
            'IFSC Code' => $res->ifsc,
            'Amount(Rs.)' => $res->amount_rs,
            'Payment Status' => $res->payment_status
          );
        }
      }

      Excel::create($schemeObj->scheme_name . ' ' . $lot_month . ' ' . $lot_year . ' Payment Status', function ($excel) use ($ben_success) {
        $excel->setTitle('Payment Status');
        $excel->sheet('Payment Success', function ($sheet) use ($ben_success) {
          $sheet->fromArray($ben_success, null, 'A1', false, false);
        });
      })->download('xlsx');
    } catch (Exception $e) {
      //  dd($e);
      return redirect("/")->with('success', 'Somthing went wrong.... ');
    }
  }

  public function getSuccessData($scheme_id, $lot_year, $lot_month, $district, $block, $rural_urban_id, $is_excel)
  {
    $year_arr = explode('-', $lot_year);
    $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
    $monthStatus = Helper::getMonthColumn($lot_month);
    // dd($monthStatus);
    // CASE WHEN tld.".$monthStatus['lot_status']." ='S' THEN 'Payment Success' 
    //  WHEN tld.".$monthStatus['lot_status']."='R' THEN 'Payment Not Initiated' 
    //  WHEN tld.".$monthStatus['lot_status']."='P' THEN 'Payment Under Processed' 
    //  WHEN tld.".$monthStatus['lot_status']."='F' THEN 'Payment Failed' 
    //  WHEN tld.".$monthStatus['lot_status']."='E' THEN 'Failed Edited But Payment Not Initiated' 
    //  ELSE ''
    // END
    // 'Payment Success' as payment_status
    if ($is_excel == 1) {
      $query = "select tld.ben_id, b.ben_name, '" . $lot_month . "' AS lot_month,tld.fin_year,b.last_accno  as account_no, b.last_ifsc as ifsc, " . $monthStatus['lot_payment_amount'] . " as amount_rs,
          'Payment Success' as payment_status
          from payment.ben_transaction_details tld 
          JOIN payment.ben_payment_details b ON tld.ben_id=b.ben_id AND tld.scheme_id=b.scheme_id
          where tld.fin_year='" . $lot_year . "' AND tld.scheme_id=" . $scheme_id . " AND tld." . $monthStatus['lot_status'] . " ='S' AND b.scheme_id=" . $scheme_id . " ";
    } else {
      $query = "select md.district_name, t.block_name AS block_ulb_name, tld.ben_id, b.ben_name, '" . $lot_month . "' AS lot_month,tld.fin_year,b.last_accno  as account_no, b.last_ifsc as ifsc, " . $monthStatus['lot_payment_amount'] . " as amount_rs,
          CASE WHEN tld.".$monthStatus['lot_status']." ='S' THEN 'Payment Success' 
            WHEN tld.".$monthStatus['lot_status']."='R' THEN 'Payment Not Initiated' 
            WHEN tld.".$monthStatus['lot_status']."='P' THEN 'Payment Under Processed' 
            WHEN tld.".$monthStatus['lot_status']."='F' THEN 'Payment Failed' 
            WHEN tld.".$monthStatus['lot_status']."='E' THEN 'Failed Edited But Payment Not Initiated' 
            ELSE ''
            END as payment_status
          from payment.ben_transaction_details tld 
          JOIN payment.ben_payment_details b ON tld.ben_id=b.ben_id AND tld.scheme_id=b.scheme_id
          JOIN m_district md ON md.district_code=b.dist_code
          JOIN( SELECT block_code, block_name FROM m_block
              UNION ALL
            SELECT urban_body_code AS block_code, urban_body_name AS block_name FROM m_urban_body
            ) t ON t.block_code = b.block_ulb_code
          where tld.fin_year='" . $lot_year . "' AND tld.scheme_id=" . $scheme_id . " AND tld." . $monthStatus['lot_status'] . " = ANY(ARRAY['S','F']) AND b.scheme_id=" . $scheme_id . " ";
    }
    if (!empty($district)) {
      $query .= " and tld.dist_code=" . $district . " ";
    }
    if (!empty($rural_urban_id)) {
      $query .= " and tld.rural_urban_id=" . $rural_urban_id . " ";
    }
    if (!empty($block)) {
      $query .= " and tld.local_body_code=" . $block . " ";
    }
    $query .= ($is_excel == 0) ? "order by md.district_name " : "";
      // dd($query);
    $data = DB::connection('pgsql_paywrite')->select($query);
    return $data;
  }
}
