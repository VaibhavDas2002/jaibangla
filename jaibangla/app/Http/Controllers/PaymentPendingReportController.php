<?php

namespace App\Http\Controllers;

use App\Scheme;
use App\District;
use App\UrbanBody;
use App\GP;
use App\BeneficiaryPensions;
use App\PensionSc;
use App\PensionSt;
use App\Manabik;
use App\UpdateBenDetails;
use App\Configduty;
use App\Mail\PendingResposneSBI;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Helpers\AuthChecker;


class PaymentPendingReportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(200);
    date_default_timezone_set('Asia/Kolkata');
  }
  public function index()
  {
    // echo 1;die;
    $designation_id = Auth::user()->designation_id;
    $user_id = AuthChecker::getUserId();
    $scheme = Configduty::select('scheme_id')->where('user_id', $user_id)->where('is_active', 1)->whereIn('scheme_id', [2,10,11])->get();
    if ($designation_id == 'HOD') {
      return view('responsePaymentPending/index', ['schemes' => $scheme]);
    }
    else {
      return redirect("/")->with('success', 'User Disabled. ');
    }
    
  }
  public function getDeta(Request $request)
  {
    if ($request->ajax()) {
      $user_id = AuthChecker::getUserId();
      $designation = Auth::user()->designation_id;
      $mapObj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
      $scheme_id = $request->scheme_id;
      $from_date = $request->from_date;
      $to_date = $request->to_date;
      $query = " SELECT lot_no, credit_count,pushed_at::date, response_received_at::date, scheme_id,
      CASE WHEN (response_received_at::date - pushed_at::date)<=7 THEN 1 ELSE 0 END AS pending_7_days_lot, 
      CASE WHEN ((response_received_at::date - pushed_at::date)>7 AND (response_received_at::date - pushed_at::date)<=10) THEN 1 ELSE 0 END AS pending_10_days_lot, 
      CASE WHEN (response_received_at::date - pushed_at::date)>10 THEN 1 ELSE 0 END AS pending_more_than_10_days_lot
      FROM sbi.transaction_lot 
      where lot_status = 6 AND debit_status_code = 'S00' AND pushed_at IS NOT null and scheme_id=".$scheme_id." AND pushed_at::date >='".date('Y-m-d', strtotime(trim(str_replace('/', '-', $from_date))))."'::date AND pushed_at::date <='".date('Y-m-d', strtotime(trim(str_replace('/', '-', $to_date))))."'::date";
      // echo $query;die;
      $data = DB::connection('pgsql_paywrite')->select($query);
      return datatables()->of($data)
        ->addIndexColumn()
        ->addColumn('response_received_at', function($data){
          return date("d-m-Y", strtotime($data->response_received_at));
        })
        ->addColumn('pushed_at', function ($data) {
          return date("d-m-Y", strtotime($data->pushed_at));
        })
        ->addColumn('pending_7_days_lot', function ($data) {
          if ($data->pending_7_days_lot == 0) {
            return $data->pending_7_days_lot;
          } else {
            return $data->pending_7_days_lot;
          }
        })
        ->addColumn('pending_10_days_lot', function ($data) {
          if ($data->pending_10_days_lot == 0) {
            return $data->pending_10_days_lot;
          } else {
            return $data->pending_10_days_lot;
          }
        })
        ->addColumn('pending_more_than_10_days_lot', function ($data) {
          if ($data->pending_more_than_10_days_lot == 0) {
            return $data->pending_more_than_10_days_lot;
          } else {
            return $data->pending_more_than_10_days_lot;
          }
        })
        ->rawColumns(['response_received_at', 'pushed_at', 'pending_7_days_lot', 'pending_10_days_lot', 'pending_more_than_10_days_lot'])
        ->make(true);
    }
  }

  /* Opening Modal Section */
  public function fetchData(Request $request)
  {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $pending_payment_id = $request->op_type;
      $scheme_id = $request->scheme_id;
      $pushed_at = $request->pushed_at;
      $scheme_name = Scheme::where('id', $scheme_id)->value('scheme_name');
      $actual_pushed = date("d-m-Y", strtotime($pushed_at));
      $op_type_name = '';
    
      $query = '';
      $query = "SELECT lot_no,debit_reference,credit_count,pushed_at::date from sbi.transaction_lot where lot_status=any(array[2,3]) AND pushed_at IS NOT null and scheme_id=". $scheme_id ." ";
      if ($pending_payment_id == 7) {
        $op_type_name = 'Pending More than 7 Days';
        $query .= " and pushed_at::date='" . $pushed_at . "'::date and (current_date - '" . $pushed_at . "'::date)>=7 AND (current_date - '" . $pushed_at . "'::date)< 10";
      } else if ($pending_payment_id == 10) {
        $op_type_name = 'Pending More than 10 Days';
        $query .= " and pushed_at::date='" . $pushed_at . "'::date and (current_date - '" . $pushed_at . "'::date)>=10 AND (current_date - '" . $pushed_at . "'::date)< 15";
      } else if ($pending_payment_id == 15) {
        $op_type_name = 'Pending More than 15 Days';
        $query .= " and pushed_at::date='" . $pushed_at . "'::date and (current_date - '" . $pushed_at . "'::date)>=15";
      }
      $data = DB::connection('pgsql_paywrite')->select($query);
      $total_ben_count = 0;
      $htmlLotTable = '';
      if (count($data) > 0) {

        $htmlLotTable .= '<table class="table table-bordered table-condensed table-striped" id="lotTable" cellspacing="0" style="font-size: 14px;" width="100%">  
        <thead>
          <tr role="row">
            <th>Lot no</th>
            <th>Debit Reference</th>
            <th>No. of Beneficiary</th>
            <th>Pushed At To the bank</th>
          </tr>
        </thead>
        <tbody>';
        foreach ($data as $key) {
          $total_ben_count = $total_ben_count + $key->credit_count;
          $htmlLotTable .= '<tr><td>' . $key->lot_no . '</td><td>' . $key->debit_reference . '</td><td>' . $key->credit_count . '</td><td>' . $key->pushed_at . '</td></tr>';
        }
        // $htmlLotTable .= '<tr><td></td><td><b>Total</b></td><td><b>' . $total_ben_count . '</b></td><td></td></tr>';
        $htmlLotTable .= '</tbody>
                        </table>';
      } else {
        $htmlLotTable .= '<table class="table table-bordered display compact" cellspacing="0" style="font-size: 14px;" width="100%">  
        <thead>
          <tr>
            <th style="text-align: center; color: #d9534f;">No data found</th>
          </tr>
        </thead></table>';
      }
      $response = array('htmlLotTable' => $htmlLotTable, 'pushedAt' => $actual_pushed, 'schemeName' => $scheme_name, 'opTypeName' => $op_type_name);
    } catch (\Exception $e) {
      $response = array(
        'exception' => true,
        // 'exception_message' => $e->getMessage(),
        'exception_message' => 'Something went wrong. May be session time out logout and login again.',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }

  /* Sending Mail For Pending Resposne from 7 days in SBI */
  public function mailPendingResponseSBI(Request $request) {
    $msg = "Testing Mail for pending resposne sbi";
			Mail::to('subhankarbisoyee5@gmail.com')
			  ->send(new PendingResposneSBI($msg));
  }
}
