<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scheme;
use App\District;
use App\UrbanBody;
use App\GP;
use App\BeneficiaryPensions;
use App\PensionSc;
use App\PensionSt;
use App\Manabik;
use App\UpdateBenDetails;
use Excel;
use App\Configduty;
use App\DocumentType;
use App\SBITransactionLot;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentReportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(180);
    date_default_timezone_set('Asia/Kolkata');
  }
  /*
    Payment Lot Report Calender date month wise
  */
  /*
    Payment Lot Report Calender date month wise
  */
  public function calenderPaymentIndex(Request $request)
  {
    $designation_id_old = Auth::user()->designation_id_old;
    $user_id = Auth::user()->id;
    $mapObj = DB::table('public.duty_assignement')->where('user_id', $user_id)->where('is_active', 1)->first();
    $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . " )"));
    if (count($scheme) > 0) {
      return view('payment-lot-report/index_date_wise_lot_report', ['schemes' => $scheme]);
    } else {
      return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
    }
  }

  public function calenderPaymentIndexGetData(Request $request)
  {
    if ($request->ajax()) {
      $scheme_id = $request->scheme_id;
      $from_date = $request->from_date;
      $to_date = $request->to_date;
      $fin_year = $request->fin_year;

      $query = "SELECT 
        'SBI' as payment_mode,
        pushed_at, 
        tl.lot_month, tl.lot_year,scheme_id, tl.no_of_lot, tl.total_ben_in_lots, tl.success_count, tl.amount_debit, tl.failed_count , (tl.total_ben_in_lots - (tl.success_count + tl.failed_count)) as response_pending FROM 
      (SELECT 
        pushed_at::date as pushed_at,
        lot_month,lot_year, scheme_id,
        COUNT(1) as no_of_lot,
        SUM(credit_count) as total_ben_in_lots,
        SUM(success_count) as success_count,
        SUM(amount_debit) as amount_debit,
        SUM(failed_count) as failed_count
        
      FROM sbi.transaction_lot WHERE scheme_id=" . $scheme_id . " AND lot_status in(2,3,4,5,6) AND pushed_at is not null ";
      if (!empty($fin_year)) {
        $query .= " and lot_year='" . $fin_year ."' ";
      }
      if (!empty($from_date)) {
        $query .= " and pushed_at::date >='" . date('Y-m-d', strtotime(trim(str_replace('/', '-', $from_date)))) . "'::date";
      }
      if (!empty($to_date)) {
        $query .= " and pushed_at::date <='" . date('Y-m-d', strtotime(trim(str_replace('/', '-', $to_date)))) . "'::date";
      }
      $query .= " GROUP BY pushed_at::date,lot_month,lot_year,scheme_id ORDER BY pushed_at) tl";
      $query .= " UNION ALL ";
      $query .= "SELECT 'IFMS' as payment_mode,
      pushed_at, 
      tl.lot_month, tl.lot_year,scheme_id, tl.no_of_lot, tl.total_ben_in_lots, tl.success_count, tl.amount_debit, tl.failed_count , (tl.total_ben_in_lots - (tl.success_count + tl.failed_count)) as response_pending FROM 
    (SELECT 
      (SUBSTRING(file_name, 17, 4) ||'-'|| SUBSTRING(file_name, 15, 2) ||'-'|| SUBSTRING(file_name, 13, 2))::date as pushed_at,
          lot_month,lot_year,scheme_id,
          COUNT(1) as no_of_lot,
          SUM(ben_count) as total_ben_in_lots,
          SUM(rbi_success_count) as success_count,
          SUM(rbi_success_count)*1000 as amount_debit,
          SUM(rbi_failed_count)+SUM(ifms_wrongdata_count) as failed_count
          
        FROM ifms.transaction_lot WHERE scheme_id=" . $scheme_id . " and push_to_ifms_status=1 and lot_status <> '-1'";
      if (!empty($fin_year)) {
        $query .= " and lot_year='" . $fin_year ."' ";
      }
      if (!empty($from_date)) {
        $query .= " and (SUBSTRING(file_name, 17, 4) ||'-'|| SUBSTRING(file_name, 15, 2) ||'-'|| SUBSTRING(file_name, 13, 2))::date >= '" . date('Y-m-d', strtotime(trim(str_replace('/', '-', $from_date)))) . "'::date ";
      }
      if (!empty($to_date)) {
        $query .= " and (SUBSTRING(file_name, 17, 4) ||'-'|| SUBSTRING(file_name, 15, 2) ||'-'|| SUBSTRING(file_name, 13, 2))::date <='" . date('Y-m-d', strtotime(trim(str_replace('/', '-', $to_date)))) . "'::date ";
      }

      $query .= " GROUP BY (SUBSTRING(file_name, 17, 4) ||'-'|| SUBSTRING(file_name, 15, 2) ||'-'|| SUBSTRING(file_name, 13, 2))::date, lot_month, lot_year,scheme_id ORDER BY pushed_at) tl";
      // echo $query;
      // die;
      $data = DB::connection('pgsql_mis')->select($query);

      return datatables()->of($data)
        ->addIndexColumn()
        ->addColumn('pushed_at', function ($data) {
          return date("d-m-Y", strtotime($data->pushed_at));
        })
        ->addColumn('success_amount', function ($data) {
          return ($data->amount_debit) / 100;
        })
        ->addColumn('no_of_lots', function ($data) {
          $btn = '';
          $btn = '<button class="btn btn-link lot_view" value="' . $data->pushed_at . '_' . $data->lot_month . '_' . $data->lot_year . '_' . $data->payment_mode . '_'.$data->scheme_id.'"><b>' . $data->no_of_lot . '</b></button>';
          return $btn;
        })
        ->rawColumns(['pushed_at', 'amount_debit', 'no_of_lots'])
        ->make(true);
    }
  }
  public function calenderPaymentGetDataLotwise(Request $request)
  {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $pushed_at = $request->pushed_at;
      $lot_month = $request->lot_month;
      $lot_year = $request->lot_year;
      $payment_mode = $request->payment_mode;
      $scheme_id = $request->scheme_id;
      if ($payment_mode == 'SBI') {
        $lotMasterObj = DB::table('sbi.transaction_lot')->selectRaw('lot_no as lot_no, lot_year as lot_year, lot_month, credit_count as total_ben, success_count, failed_count')->whereRaw("pushed_at::date='" . $pushed_at . "'::date")->where('lot_month', $lot_month)->where('lot_year', $lot_year)->whereIn('lot_status', [2,3,4,5,6])->whereNotNull('pushed_at')->where('scheme_id', $scheme_id)->get();
      } else {
        $lotMasterObj = DB::table('ifms.transaction_lot')->selectRaw('lot_no as lot_no, lot_year as lot_year, lot_month, ben_count as total_ben, rbi_success_count as success_count, rbi_failed_count as failed_count, ifms_wrongdata_count')->whereRaw("(SUBSTRING(file_name, 17, 4) ||'-'|| SUBSTRING(file_name, 15, 2) ||'-'|| SUBSTRING(file_name, 13, 2))::date='" . $pushed_at . "'::date")->where('lot_month', $lot_month)->where('lot_year', $lot_year)->where('push_to_ifms_status', 1)->where('lot_status', '<>', '-1')->where('scheme_id', $scheme_id)->get();
      }

      if (count($lotMasterObj) == 0) {
        return  $response = array(
          'status' => 0, 'msg' => 'No record found.',
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      } else {

        $html = '';
        $html = '<table class="table table-bordered display compact paymentStaClass" id="exampleLotTable" cellspacing="0" style="font-size: 14px; width: 100%;" width="100%">
          <thead>
            <tr role="row">
              <th>Lot No</th><th>Lot Year</th><th>Lot Month</th><th>Beneficiary in the Lot</th><th>Success Count</th><th>Failed Count</th>';
        if ($payment_mode == 'IFMS') {
          $html .= '<th>IFMS Wrong Data Count</th>';
        }
        $html .= '</tr>
          </thead>
          <tbody>';
        foreach ($lotMasterObj as $k) {
          $html .= '<tr><td>' . $k->lot_no . '</td><td>' . $k->lot_year . '</td><td>' . $k->lot_month . '</td><td>' . $k->total_ben . '</td><td>' . $k->success_count . '</td><td>' . $k->failed_count . '</td>';
          if ($payment_mode == 'IFMS') {
            $html  .= '<td>' . $k->ifms_wrongdata_count . '</td>';
          }
          $html .= '</tr>';
        }
        $html .= '</tbody> 
        </table>';
        return  $response = array('status' => 1, 'htmlTable' => $html);
      }
    } catch (\Exception $e) {
      $response = array(
        'exception' => true,
        'exception_message' => $e->getMessage(),
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }
}
