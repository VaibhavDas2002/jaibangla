<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GP;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use App\BeneficiaryPensions;
use App\BenDocsSc;
use App\BenDocsSt;
use App\DocumentType;
use App\Configduty;
use App\lot_master;
use App\MapLavel;
use App\Scheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Validator;
use Elibyy\TCPDF\Facades\TCPDF as PDF;

class TrackApplicationStatusController extends Controller
{
  public function __construct()
  {
    set_time_limit(120);
    $this->middleware('auth');
    date_default_timezone_set('Asia/Kolkata');
  }
  private function getSchemaName($scheme_id)
  {
    if (!is_null($scheme_id)) {
      $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
      //$parameter['scheme_id'] = $scheme_id;
      $schema_name =  $sObj->short_code;
      //dd($schema_name);
      if (empty($schema_name)) {
        $schema_name = 'pension';
      }
      $table_name =  strtolower($schema_name) . '.beneficiary';
    } else {
      $table_name =  'pension.beneficiary';
    }
    return $table_name;
  }
  public function checkSchemeSession($scheme_id)
  {
    $roleArray = Session::get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        Session::put('level', $roleObj['mapping_level']);
        Session::put('distCode', $roleObj['district_code']);
        Session::put('scheme_id', $scheme_id);
        Session::put('is_first', $roleObj['is_first']);
        Session::put('is_urban', $roleObj['is_urban']);
        Session::put('role_id', $roleObj['id']);
        if ($roleObj['is_urban'] == 1) {
          Session::put('bodyCode', $roleObj['urban_body_code']);
        } else {
          Session::put('bodyCode', $roleObj['taluka_code']);
        }
        break;
      }
    }
  }
  /*
		Get First Landing Page
  */
  public function index()
  {
    $user_id = Auth::user()->id;
    $designation = Auth::user()->designation_id_old;
    $mapObj = DB::connection('pgsql_mis')->table('public.duty_assignement')->where('user_id', $user_id)->where('is_active', 1)->first();
    $scheme = DB::connection('pgsql_mis')->select('select id,scheme_name from public.m_scheme where id in (select scheme_id from public.duty_assignement where user_id=' . $user_id . ' and is_active=1) order by scheme_name');
    if (count($scheme) > 0) {
      return view('trackApplicationStatus/index', ['schemes' => $scheme]);
    } else {
      return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
    }
  }
  /*
		Get indivisual beneficiary data
  */
  public function getTrackApplicantDetails(Request $request)
  {
    if ($request->ajax()) {
      $ben_id = $request->pensionId;
      $search_type = $request->searchType;
      $ben_fname = $request->benFname;
      $ben_mname = $request->benMname;
      $ben_lname = $request->benLname;
      $mobile_no = $request->benMob;
      $aadhar_no = $request->benAa;
      $scheme_id = $request->schemeId;
      $this->checkSchemeSession($scheme_id);
      $mappingLevel = $request->session()->get('level');
      $district_code = $request->session()->get('distCode');
      $is_first = $request->session()->get('is_first');
      $is_urban = $request->session()->get('is_urban');
      $body_code = $request->session()->get('bodyCode');
      $role_id = $request->session()->get('role_id');

      // Get Dynamic Schema Name scheme wise
      $table_name = $this->getSchemaName($scheme_id);
      $query = '';
      $query = "select b.*, md.district_name, bl_div.block_subdiv_name, ms.scheme_name,  
        CASE 
					WHEN b.next_level_role_id is null and b.is_reverted is null THEN 'Applied (Verification Pending)' 
					WHEN b.is_verified=1 and b.is_approved=0 and b.is_rejected=0	THEN 'Verified (Approval Pending)'
          WHEN b.is_reverted=1 and b.is_approved=0 and b.is_rejected=0 and b.next_level_role_id is null	THEN 'Reverted the Application (Application will be found in Operator end)'
          WHEN b.next_level_role_id = 0 THEN 
              CASE 
              WHEN  b.dup_bank = 1 THEN 'Approved but due to Duplicate Bank A/c, payment has been stopped..' 
              ELSE 'Approved' 
              END
				 WHEN b.is_rejected=1 THEN 
					  	CASE 
              WHEN  b.next_level_role_id = -98 THEN 'Pause Payment'
              WHEN  b.next_level_role_id = -94 THEN 'Beneficiary Payment has been <br>Suspended due to Death case<br>(As per the data Comes from<br> Janma-Mrityu Portal)' 
              ELSE 'Rejected' 
              END
					    ELSE '' 
				END AS app_status 
				from " . $table_name . " b 
				JOIN public.m_district md ON md.district_code=b.created_by_dist_code 
        JOIN public.m_scheme ms ON ms.id=b.scheme_id  
				JOIN (SELECT block_code AS block_subdiv_code,block_name AS block_subdiv_name FROM public.m_block 
	  			UNION ALL
      		SELECT sub_district_code AS block_subdiv_code, sub_district_name AS block_subdiv_name FROM 		public.m_sub_district
      	) bl_div ON bl_div.block_subdiv_code=b.created_by_local_body_code 
      	where scheme_id = " . $scheme_id . " ";
      if ($search_type == 'benId' && !is_null($ben_id)) {
        if (!is_null($ben_id)) {
          $query .= " and b.id = " . $ben_id . "";
        }
        if (!is_null($district_code)) {
          $query .= " and b.created_by_dist_code = " . $district_code . " ";
        }
        if (!is_null($body_code)) {
          $query .= " and b.created_by_local_body_code = " . $body_code . "";
        }
      } else if ($search_type == 'benName') {
        if (!is_null($ben_fname)) {
          $query .= " and b.ben_fname ILIKE '" . $ben_fname . "%' ";
        }
        if (!is_null($ben_mname)) {
          $query .= " and b.ben_mname ILIKE '" . $ben_mname . "%' ";
        }
        if (!is_null($ben_lname)) {
          $query .= " and b.ben_lname ILIKE '" . $ben_lname . "%' ";
        }
        if (!is_null($district_code)) {
          $query .= " and b.created_by_dist_code = " . $district_code . " ";
        }
        if (!is_null($body_code)) {
          $query .= " and b.created_by_local_body_code = " . $body_code . "";
        }
      } else if ($search_type == 'benMobile' && !is_null($mobile_no)) {
        if (!is_null($mobile_no)) {
          $query .= " and b.mobile_no = " . $mobile_no . "";
        }
        if (!is_null($district_code)) {
          $query .= " and b.created_by_dist_code = " . $district_code . " ";
        }
        if (!is_null($body_code)) {
          $query .= " and b.created_by_local_body_code = " . $body_code . "";
        }
      } else if ($search_type == 'benAadhar' && !is_null($aadhar_no)) {
        if (!is_null($aadhar_no)) {
          $query .= " and b.aadhar_no = '" . $aadhar_no . "' ";
        }
        if (!is_null($district_code)) {
          $query .= " and b.created_by_dist_code = " . $district_code . " ";
        }
        if (!is_null($body_code)) {
          $query .= " and b.created_by_local_body_code = " . $body_code . "";
        }
      } else {
        $query .= ' LIMIT 10';
      }
      $data = DB::connection('pgsql_mis')->select($query);
      // print_r($data);
      return datatables()->of($data)
        // ->addIndexColumn()
        ->addColumn('current_status', function ($data) {
          $action = '';
          $class = '';
          if ($data->next_level_role_id == '0') {
            $class = 'text-success';
          } else if ($data->is_rejected==1) {
            $class = 'text-danger';
          } else if ($data->is_verified==1 and $data->is_approved==0 and $data->is_rejected==0 ) {
            $class = 'text-warning';
          } else {
            $class = 'text-info';
          }
          $action = '<h5><b><span class="' . $class . '">' . $data->app_status . '</span></b></h5>';
          if ($data->lot_generated == '-1' && $data->bank_edited == '0') {
            $action .= '<span class="text-danger" style="font-weight: bold; font-style: italic;">Pending IFMS failed bank rectification <br/>from Block/Sub-division end</span>';
          }
          if ($data->lot_generated == '-2' && $data->bank_edited == '0') {
            $action .= '<span class="text-danger" style="font-weight: bold; font-style: italic;">Pending RBI failed bank rectification <br/>from Block/Sub-division end</span>';
          }
          if ($data->lot_generated == '-3' && $data->bank_edited == '0') {
            $action .= '<span class="text-danger" style="font-weight: bold; font-style: italic;">Pending SBI failed bank rectification <br/>from Block/Sub-division end</span>';
          }
          return $action;
        })
        ->addColumn('scheme_name', function ($data) {
          return $data->scheme_name;
        })
        ->addColumn('name', function ($data) {
          return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
        })
        ->addColumn('father_name', function ($data) {
          return $data->father_fname . ' ' . $data->father_mname . ' ' . $data->father_lname;
        })
        ->addColumn('address', function ($data) {
          $address = '';
          $address = 'District - ' . $data->district_name . '<br>';
          if ($data->rural_urban_id == 1) {
            $address .= 'Sub-division - ' . $data->block_subdiv_name . '<br>';
            $address .= 'Municipality - ' . $data->block_ulb_name . '<br>';
            $address .= 'Ward - ' . $data->gp_ward_name;
          } else {
            $address .= 'Block - ' . $data->block_subdiv_name . '<br>';
            $address .= 'GP - ' . $data->gp_ward_name;
          }
          return $address;
        })
        ->addColumn('bank_info', function ($data) {
          $bank = '';
          if (!is_null($data->bank_name)) {
            $bank .= 'Bank Name - ' . $data->bank_name . '<br>';
          }
          if (!is_null($data->branch_name)) {
            $bank .= 'Branch - ' . $data->branch_name . '<br>';
          }
          $bank .= 'A/c No - ' . $data->bank_code . '<br>';
          $bank .= 'IFSC - ' . $data->bank_ifsc;
          return $bank;
        })
        ->addColumn('payment_info', function ($data) {
          $pay = '';
          $pay .= 'Applied At - ' .  date("d-m-Y", strtotime($data->created_at)) . '<br>';
          $pay .= 'Payment Count - ' . $data->payment_count . '<br>';
          if ($data->last_paid_yymm == 0) {
            $final_date = '0';
          } else {
            $date = $data->last_paid_yymm;
            $arr = str_split($date, 2);
            $year = $arr[0];
            $month = $arr[1];
            $final_date = date('F', mktime(0, 0, 0, $month, 10)) . ' - 20' . $year;
          }
          $pay .= 'Last Paid (Month-Year) - <br>' . $final_date . '<br>';
          return $pay;
        })
        ->addColumn('view_payment_status', function ($data) {
          $payment = '';
          $payment = '<button class="btn btn-info btn-sm" name="view_status" class="view_status" value="' . $data->id . '_' . $data->scheme_id . '" onclick="viewPaymentStatusFunction(this.value);"><i class="fa fa-eye"></i> View</button>';
          return $payment;
        })
        ->rawColumns(['name', 'father_name', 'address', 'bank_info', 'current_status', 'payment_info', 'view_payment_status', 'scheme_name'])
        ->make(true);
    }
  }

  /*
	Get Payment Status report
  */
  public function getPaymentStatusDetails(Request $request)
  {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $ben_id = $request->ben_id;
      $scheme_id = $request->schemeId;
      $fin_year = $request->fin_year;
      $year_arr = explode('-', $fin_year);
      $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
      // Get current financial year
      if (date('m') > 3) {
        $currentFinYear = date('Y') . "-" . (date('Y') + 1);
      } else {
        $currentFinYear = (date('Y') - 1) . "-" . date('Y');
      }
      $schema_name = $this->getSchemaName($scheme_id);
      $ben_details = DB::connection('pgsql_mis')->table($schema_name)->find($ben_id);

      $query = '';
      $query = "select 'ifms' as payment_mode,lm.lot_month,lm.lot_year,lm.lot_no,lm.scheme_id,be.pension_id,be.ifsc as ifsc_code,be.acc_no as account_no, be.updated_at, 
        (case when lm.lot_status=6  then 'Payment processed' else 'Under process' end) process_status,
        (case when (be.wrongdata_flag=0 and lm.lot_status=6) then 'Payment success' when (be.wrongdata_flag=0 and lm.lot_status<6) then 'Payment under process' else 'Payment error' end) payment_status 
        from ifms.transaction_lot_details_report_" . $yyyy_val . " be,ifms.transaction_lot lm
        where lm.lot_no=be.drn_part and lm.lot_status>=0 and be.pension_id = " . $ben_id . "
        union
        select 'sbi' as payment_mode,tl.lot_month,tl.lot_year,tl.lot_no,tl.scheme_id,tld.pension_id,tld.ifsc_code_credit as ifsc_code,tld.account_credit as account_no, tld.updated_at, 
        (case when tl.lot_status=6  then 'Payment processed' else 'Under process' end) process_status,
        (case when tld.status_code='S00' then 'Payment success' else (case when tld.status_code is null then 'Payment under process' else 'Payment error' end)end) payment_status 
        from sbi.transaction_lot_details_report_" . $yyyy_val . " tld,sbi.transaction_lot tl
        where tl.lot_no=tld.lot_no and (debit_status_code is null or debit_status_code='S00') and tl.lot_status>=0 and tld.pension_id = " . $ben_id . " ";

      $query .= "union select 'ifms' as payment_mode,lm.lot_month,lm.lot_year,lm.lot_no,lm.scheme_id,be.pension_id,be.ifsc as ifsc_code,be.acc_no as account_no, be.updated_at, 
        (case when lm.lot_status=6  then 'Payment processed' else 'Under process' end) process_status,
        (case when (be.wrongdata_flag=0 and lm.lot_status=6) then 'Payment success' when (be.wrongdata_flag=0 and lm.lot_status<6) then 'Payment under process' else 'Payment error' end) payment_status 
        from ifms.transaction_lot_details be,ifms.transaction_lot lm
        where lm.lot_no=be.drn_part and lm.lot_status>=0 and lm.lot_year='" . $fin_year . "' and be.pension_id = " . $ben_id . "
        union
        select 'sbi' as payment_mode,tl.lot_month,tl.lot_year,tl.lot_no,tl.scheme_id,tld.pension_id,tld.ifsc_code_credit as ifsc_code,tld.account_credit as account_no, tld.updated_at, 
        (case when tl.lot_status=6  then 'Payment processed' else 'Under process' end) process_status,
        (case when tld.status_code='S00' then 'Payment success' else (case when tld.status_code is null then 'Payment under process' else 'Payment error' end)end) payment_status 
        from sbi.transaction_lot_details tld,sbi.transaction_lot tl
        where tl.lot_no=tld.lot_no and (debit_status_code is null or debit_status_code='S00') and tl.lot_status>=0 and tl.lot_year='" . $fin_year . "' and tld.pension_id = " . $ben_id . " ";

      $query .= " order by lot_no ";

      $payment_details = DB::connection('pgsql')->select($query);

      // HTML table generated
      $payLoop = 1;
      $paymentDetailsHtml = '';
      if (count($payment_details) > 0) {
        $paymentDetailsHtml .= '<table class="table table-bordered display compact paymentStaClass" id="paymentTable" cellspacing="0" style="font-size: 14px;" width="100%">  
            <thead style="font-size: 12px;">
              <tr>
                <th>#No</th>
                <th>Pension Id</th>
                <th>Lot No</th>
                <th>Month</th>
                <th>Year</th>
                <th>IFSC Code</th>
                <th>Account No</th>
                <th>Payment Mode</th>
                <th>Process Status</th>
                <th>Payment Status</th>
                <th>Payment Response <br>Received At</th>
                <th>View</th>
              </tr>
            </thead>
            <tbody>';
        foreach ($payment_details as $key) {
          $paymentDetailsHtml .= "<tr>";
          $paymentDetailsHtml .= "<td>" . $payLoop . "</td>";
          $paymentDetailsHtml .= "<td>" . $key->pension_id . "</td>";
          $paymentDetailsHtml .= "<td>" . $key->lot_no . "</td>";
          $paymentDetailsHtml .= "<td>" . $key->lot_month . "</td>";
          $paymentDetailsHtml .= "<td>" . $key->lot_year . "</td>";
          $paymentDetailsHtml .= "<td>" . $key->ifsc_code . "</td>";
          $paymentDetailsHtml .= "<td>" . $key->account_no . "</td>";
          $paymentDetailsHtml .= "<td>" . $key->payment_mode . "</td>";
          $paymentDetailsHtml .= "<td>" . $key->process_status . "</td>";
          $paymentDetailsHtml .= "<td>" . $key->payment_status . "</td>";
          if ($key->updated_at == '') {
            $paymentDetailsHtml .= "<td></td>";
          } else {
            $paymentDetailsHtml .= "<td>" . date("d-m-Y", strtotime(explode(' ', $key->updated_at)[0])) . "</td>";
          }
          $paymentDetailsHtml .= "<td>";
          if ($key->payment_status == 'Payment error') {
            $paymentDetailsHtml .= '<button class="btn btn-xs btn-danger" class="js-status-error" value="' . $key->lot_no . '_' . $key->pension_id . '_' . $fin_year . '_' . $scheme_id . '" onclick="getStatusUTRAndErrorFun(this.value);">View Error</button>';
          } else if ($key->payment_status == 'Payment under process') {
            $paymentDetailsHtml .= '';
          } else {
            $paymentDetailsHtml .= '<button class="btn btn-xs btn-success" class="js-status-error"  value="' . $key->lot_no . '_' . $key->pension_id . '_' . $fin_year . '_' . $scheme_id . '" onclick="getStatusUTRAndErrorFun(this.value);">View UTR</button>';
          }
          $paymentDetailsHtml .= "</td>";
          $paymentDetailsHtml .= "</tr>";
          $payLoop = $payLoop + 1;
        }
        $paymentDetailsHtml .= '</tbody></table>';
      } else {
        $paymentDetailsHtml .= '<table class="table table-bordered display compact" cellspacing="0" style="font-size: 14px;" width="100%">  
            <thead>
              <tr>
                <th style="text-align: center; color: #d9534f;">No data found in this financial year</th>
              </tr>
            </thead></table>';
      }

      if ($ben_details == null) {
        return  $response = array(
          'status' => 1, 'msg' => 'Somethimg went wrong.',
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      } else {

        $response = array('ben_details' => $ben_details, 'final_payment_table' => $paymentDetailsHtml);
      }
    } catch (\Exception $e) {
      $response = array(
        'exception' => true,
        // 'exception_message' => $e->getMessage(),
        'exception_message' => 'Error! Please try again.',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }

  public function getStatusUTRAndErrorFun(Request $request)
  {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $pension_id = $request->pension_id;
      $scheme_id = $request->schemeId;
      $fin_year = $request->fin_year;
      $lot_no = $request->lot_no;
      $year_arr = explode('-', $fin_year);
      $yyyy_val = substr($year_arr[0], 2, 2) . substr($year_arr[1], 2, 2);
      // Get current financial year
      if (date('m') > 3) {
        $currentFinYear = date('Y') . "-" . (date('Y') + 1);
      } else {
        $currentFinYear = (date('Y') - 1) . "-" . date('Y');
      }

      $lotObj = lot_master::where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->first();
      if ($lotObj->payment_mode == 'IFMS') {
        $results = DB::connection('pgsql_mis')->select(DB::raw("select (case when wrongdata_flag=0 then utr_no else ifms_status end) as status_code from ifms.transaction_lot_details_report_" . $yyyy_val . " where drn_part='" . $lot_no . "' and scheme_id=" . $scheme_id . " and pension_id=" . $pension_id));
        return  $response = array(
          'status' => 1, 'msg' => $results[0]->status_code,
          'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Information'
        );
      } elseif ($lotObj->payment_mode == 'SBI') {
        $results = DB::connection('pgsql')->select(DB::raw("select (case when tld.status_code='S00' then tld.credit_payment_reference else c.description end) as status_code 
			from sbi.credit_transaction_code c,sbi.transaction_lot_details_report_" . $yyyy_val . " tld where tld.lot_no='" . $lot_no . "' and tld.scheme_id=" . $scheme_id . " and tld.pension_id=" . $pension_id . " and tld.status_code=c.code"));
        return  $response = array(
          'status' => 1, 'msg' => $results[0]->status_code,
          'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Information'
        );
      }
    } catch (\Exception $e) {
      $response = array(
        'exception' => true,
        // 'exception_message' => $e->getMessage(),
        'exception_message' => 'Error! Please try again.',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }
}
