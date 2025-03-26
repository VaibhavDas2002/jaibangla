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
use App\Helpers\Helper;
use App\Scheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Elibyy\TCPDF\Facades\TCPDF as PDF;
use Illuminate\Support\Facades\Config;
use App\Workflow;
use App\SchemeStepRank;
use App\Helpers\AuthChecker;

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
      $table_name =  strtolower($schema_name) . '.beneficiaries';
    } else {
      $table_name =  'pension.beneficiaries';
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
    $user_id = AuthChecker::getUserId();
    $scheme = DB::connection('pgsql_mis')->select('select id,scheme_name from public.m_scheme where id in (select scheme_id from public.duty_assignement where user_id=' . $user_id . ' and is_active=1) and is_active=1 order by scheme_name');
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
      $bank_accno = $request->benAccNo;
      $scheme_id = $request->schemeId;
      $this->checkSchemeSession($scheme_id);
      $mappingLevel = $request->session()->get('level');
      $district_code = $request->session()->get('distCode');
      $is_first = $request->session()->get('is_first');
      $is_urban = $request->session()->get('is_urban');
      $body_code = $request->session()->get('bodyCode');
      $role_id = $request->session()->get('role_id');

      // Get Dynamic Schema Name scheme wise
      $next_level_role_id_operator=SchemeStepRank::getSchemeParentId($scheme_id, 1);
      $table_name = 'pension.beneficiaries';
      $query = '';
      $query = "select b.*, m.district_name, md.district_name as loc_dist, bl_div.block_subdiv_name, ms.scheme_name,  
        CASE 
					WHEN b.next_level_role_id = ".$next_level_role_id_operator." and b.is_reverted is null THEN 'Applied (Verification Pending)' 
					WHEN b.is_verified=1 and b.is_approved=0 and b.is_rejected=0	THEN 'Verified (Approval Pending)'
          WHEN b.is_reverted=1 and b.is_approved=0 and b.is_rejected=0 and b.next_level_role_id =".$next_level_role_id_operator. "	THEN 'Application is reverted (Application will be found in Operator end)'
          WHEN b.next_level_role_id = 0 THEN 
            CASE 
              WHEN b.dup_bank = 1 THEN 'Approved but due to Duplicate Bank A/c, payment has been stopped..' 
              WHEN (b.dup_bank = 0 OR b.dup_bank IS NULL) AND b.payment_suspended = 1 THEN 'Approved but Beneficiary Payment has been <br>Suspended due to Death case<br>(As per the data Comes from<br> Janma-Mrityu Portal)' 
              ELSE 'Approved' 
            END
          WHEN b.is_rejected=1 THEN 
            CASE 
              WHEN  b.next_level_role_id = -98 THEN 'Pause Payment'
              ELSE 'Rejected' 
            END
					  ELSE '' 
				END AS app_status 
				from " . $table_name . " b 
        LEFT JOIN public.m_district m ON m.district_code=b.dist_code 
				LEFT JOIN public.m_district md ON md.district_code=b.created_by_dist_code 
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
        // if (!is_null($district_code)) {
        //   $query .= " and b.created_by_dist_code = " . $district_code . " ";
        // }
        // if (!is_null($body_code)) {
        //   $query .= " and b.created_by_local_body_code = " . $body_code . "";
        // }
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
        // if (!is_null($district_code)) {
        //   $query .= " and b.created_by_dist_code = " . $district_code . " ";
        // }
        // if (!is_null($body_code)) {
        //   $query .= " and b.created_by_local_body_code = " . $body_code . "";
        // }
      } else if ($search_type == 'benMobile' && !is_null($mobile_no)) {
        if (!is_null($mobile_no)) {
          $query .= " and b.mobile_no = " . $mobile_no . "";
        }
        // if (!is_null($district_code)) {
        //   $query .= " and b.created_by_dist_code = " . $district_code . " ";
        // }
        // if (!is_null($body_code)) {
        //   $query .= " and b.created_by_local_body_code = " . $body_code . "";
        // }
      } else if ($search_type == 'benAadhar' && !is_null($aadhar_no)) {
        if (!is_null($aadhar_no)) {
          $query .= " and b.aadhar_no = '" . $aadhar_no . "' ";
        }
        // if (!is_null($district_code)) {
        //   $query .= " and b.created_by_dist_code = " . $district_code . " ";
        // }
        // if (!is_null($body_code)) {
        //   $query .= " and b.created_by_local_body_code = " . $body_code . "";
        // }
      } else if ($search_type == 'benBankAc' && !is_null($bank_accno)) {
        if (!is_null($bank_accno)) {
          $query .= " and b.bank_code = '" . $bank_accno . "' ";
        }
      } else {
        $query .= ' LIMIT 10';
      }
      $data = DB::connection('pgsql_mis')->select($query);
      // print_r($data);

      $next_level_role_id_operator = SchemeStepRank::getSchemeParentId($scheme_id, 1);
      return datatables()->of($data)
        // ->addIndexColumn()
        ->addColumn('ben_id_text', function ($data) {
          $ben_id_text = $data->id.'<br>';
          if ($data->is_lb_imported == 1) {
            $action_need = $this->getActionNeedToTaken($data);
            return $ben_id_text.'<span style="font-weight: bold;" class="text-info">(LB Migrated<br>Beneficiary)</span>';
          }
          return $ben_id_text;
        })
        ->addColumn('action_needs_to_taken', function ($data) use($next_level_role_id_operator) {
          if ($data->next_level_role_id >= 0 || $data->next_level_role_id == $next_level_role_id_operator) {
            $action_need = $this->getActionNeedToTaken($data);
            return '<span style="font-weight: bold;" class="text-primary">' . $action_need . '</span>';
          }
          return '';
        })
        ->addColumn('current_status', function ($data) {
          $action = '<h5><b>';
          $class = '';
          $action_text = '';
          if ($data->next_level_role_id == 0) {
            $class = 'text-success';
          } else if ($data->is_rejected == 1) {
            $class = 'text-danger';
          } else if ($data->is_verified == 1 and $data->is_approved == 0 and $data->is_rejected == 0) {
            $class = 'text-warning';
          } else {
            $class = 'text-info';
          }

          if ($data->scheme_id == 17) {
            if ($data->next_level_role_id == 106) {
              $action_text .= '<span class="' . $class . '">Recommended (Approval Pending)</span>';
            }
          }

          if ($action_text == '') {
            $action_text .= '<span class="' . $class . '">' . $data->app_status . '</span>';
          }

          $action .= $action_text . '</b></h5>';
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

          $address = (!empty(trim($data->district_name))) ? 'District - ' . trim($data->district_name) . '<br>' : '';
          $address .= (!empty(trim($data->block_ulb_name))) ? 'Block/Municipality - ' . trim($data->block_ulb_name) . '<br>' : '';
          $address .= (!empty(trim($data->gp_ward_name))) ? 'Gp/Ward - ' . trim($data->gp_ward_name) : '';


          // if (!empty(trim($data->district_name)) && !empty(trim($data->block_ulb_name)) && !empty(trim($data->gp_ward_name))) {
          //   $address = 'District - ' . trim($data->district_name) . '<br>';
          //   $address .= 'Block/Municipality - ' . trim($data->block_ulb_name) . '<br>';
          //   $address .= 'Gp/Ward - ' . trim($data->gp_ward_name);
          // } else {
          //   $address = 'District - ' . trim($data->loc_dist) . '<br>';
          //   $address .= 'Block/Sub Division - ' . trim($data->block_subdiv_name) . '<br>';
          // }

          // $address = 'District - ' . $data->district_name . '<br>';
          // if ($data->rural_urban_id == 1) {
          //   $address .= 'Sub-division - ' . $data->block_subdiv_name . '<br>';
          //   $address .= 'Municipality - ' . $data->block_ulb_name . '<br>';
          //   $address .= 'Ward - ' . $data->gp_ward_name;
          // } else {
          //   $address .= 'Block - ' . $data->block_subdiv_name . '<br>';
          //   $address .= 'GP - ' . $data->gp_ward_name;
          // }
          return $address;
        })
        ->addColumn('application_entry_from', function ($data) {
          $address = '';
          $address = 'District - ' . trim($data->loc_dist) . '<br>';
          $address .= 'Block/Sub Division - ' . trim($data->block_subdiv_name) . '<br>';
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
          // $pay .= 'Payment Count - ' . $data->payment_count . '<br>';
          // if ($data->last_paid_yymm == 0) {
          //   $final_date = '0';
          // } else {
          //   $date = $data->last_paid_yymm;
          //   $arr = str_split($date, 2);
          //   $year = $arr[0];
          //   $month = $arr[1];
          //   $final_date = date('F', mktime(0, 0, 0, $month, 10)) . ' - 20' . $year;
          // }
          // $pay .= 'Last Paid (Month-Year) - <br>' . $final_date . '<br>';
          return $pay;
        })
        ->addColumn('view_payment_status', function ($data) {
          $payment = '';
          $payment = '<button class="btn btn-info btn-sm" name="view_status" class="view_status" value="' . $data->id . '_' . $data->scheme_id . '" onclick="viewPaymentStatusFunction(this.value);"><i class="fa fa-eye"></i> View</button>';
          return $payment;
        })
        ->rawColumns(['name', 'father_name', 'address', 'bank_info', 'current_status', 'payment_info', 'view_payment_status', 'scheme_name', 'action_needs_to_taken', 'application_entry_from', 'ben_id_text'])
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

      // $query = '';
      // $query = "SELECT * FROM payment.ben_transaction_details WHERE fin_year = '".$fin_year."' AND ben_id = ".$ben_id." AND scheme_id = ".$scheme_id;
      $payment_details = DB::connection('pgsql_paywrite')->table('payment.ben_transaction_details')
        ->selectRaw("payment_log->0->'january'->0 AS january, payment_log->1->'february'->0 AS february, payment_log->2->'march'->0 AS march, payment_log->3->'april'->0 AS april, payment_log->4->'may'->0 AS may, payment_log->5->'june'->0 AS june, payment_log->6->'july'->0 AS july, payment_log->7->'august'->0 AS august, payment_log->8->'september'->0 AS september, payment_log->9->'october'->0 AS october, payment_log->10->'november'->0 AS november, payment_log->11->'december'->0 AS december, *")
        ->where('fin_year', $fin_year)->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->first();
      $failedPaymentDetails = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->value('lot_no');

      // HTML table generated
      $payLoop = 1;
      $paymentDetailsHtml = '';

      $benPaymentDetails = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->first();
      // dd($benPaymentDetails);

      if (isset($benPaymentDetails)) {
        $benAccSt = '';
        $benValSt = '';
        if ($scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {
          if ($benPaymentDetails->legacy_validation == 0) {
            if ($benPaymentDetails->acc_validated == 0) {
              $benValSt = 'Account validation lot creation pending at DDO end';
            } else if ($benPaymentDetails->acc_validated == 1) {
              $benValSt = 'Account validation lot generated, response pending from bank';
            } else if ($benPaymentDetails->acc_validated == 3) {
              $benValSt = 'Account validation failed, correction pending from verifier/approver end';
            } else if ($benPaymentDetails->acc_validated == 4) {
              $benValSt = 'Name validation failed, correction pending from verifier/approver end';
            } else if ($benPaymentDetails->acc_validated == 5) {
              $benValSt = 'Beneficiary is rejected';
            } else if ($benPaymentDetails->acc_validated == 2) {
              $benValSt = '<span class="text-success">Success <i class="fa fa-check-circle"></i></span>';
            } else {
              $benValSt = '';
            }
          }
        }

        if ($benPaymentDetails->pay_validated == 3) {
          $benAccSt = 'Payment transaction failed (SBI Failed), correction pending at verifier or approver end';
        } else if ($benPaymentDetails->pay_validated == 4) {
          $benAccSt = 'Payment transaction failed (RBI Failed), correction pending at verifier or approver end';
        } else if ($benPaymentDetails->pay_validated == 5) {
          $benAccSt = 'Payment transaction failed (IFMS Failed), correction pending at verifier or approver end';
        } else {
          // $benAccSt = 'Ready For Payment';
          if ($scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {
            if (($benPaymentDetails->legacy_validation == 0 && $benPaymentDetails->acc_validated == 2) || ($benPaymentDetails->legacy_validation == 1)) {
              $benAccSt = 'Ready For Payment';
            }
            else {
              $benAccSt = '<span class="text-warning">Not Ready For Payment</span>';
            }
          } else {
            $benAccSt = 'Ready For Payment';
          }
        }

        $benValSt = ($benValSt != '') ? 'Account Validation Status : ' . $benValSt . '<br>' : '';
        $paymentDetailsHtml .= ($benPaymentDetails->is_rejected == 0 && $benPaymentDetails->is_eligible == TRUE) ? '<span class="text-info" style="font-weight: bold;">' . $benValSt . 'Payment Status : ' . $benAccSt . '</span>' : '';
      } else {
        $paymentDetailsHtml .= '<table class="table table-bordered display compact" cellspacing="0" style="font-size: 14px;" width="100%">  
          <thead>
            <tr>
              <th style="text-align: center; color: #d9534f;">This beneficiary is not yet migarted for payment</th>
            </tr>
          </thead></table>';
      }

      if (isset($payment_details)) {
        $year = (int)('20' . substr($payment_details->start_yymm, 0, -2));
        $month = (int) substr($payment_details->start_yymm, 2);
        if ($month > 3) {
          $year = $year . '-' . ($year + 1);
        } else {
          $year = ($year - 1) . '-' . $year;
        }
        // <th>IFSC</th>
        // <th>Account No</th>
        // <th>Payment Mode</th>
        // <th>Payment Response<br>Received At</th>
        $paymentDetailsHtml .= '<table class="table table-bordered display compact paymentStaClass" id="paymentTable" cellspacing="0" style="font-size: 14px;" width="100%">  
            <thead style="font-size: 12px;">
              <tr>
                <th>#No</th>
                <th>Beneficiary Id</th>
                <th>Lot No</th>
                <th>Month</th>
                <th>Year</th>
                <th>IFSC</th>
                <th>Account No</th>
                <th>Payment Mode</th>
                <th>Payment Status</th>
                <th>Payment Response<br>Received At</th>
                <th>View</th>
              </tr>
            </thead>
            <tbody>';

        // if ($fin_year == $year) {
        //   $startMonth = $month;
        // } else {
        //   $startMonth = 4;
        // }

        // if ($ben_id == 4973866) {
        //   dd($year, $month, $startMonth);
        // }

        $startMonth = 4;
        $endMonth = 3;
        if ($fin_year == '2024-2025') {
          $endMonth = date('n');
        } 

        $finalendmonth = 0;
        $loopcount = '';
        if ($endMonth == 1 || $endMonth == 2 || $endMonth == 3) {
          $finalendmonth += $endMonth;
          $loopcount = (12 + $finalendmonth);
        } else {
          $loopcount = ($endMonth - $startMonth) + 4;
        }
        $count = $startMonth;
        $flag = 0;
        for ($i = $startMonth; $i <= $loopcount; $i++) {
          if ($i == 13) {
            $count = 1;
          }
          // dump($count);
          $getMonthColumn = Helper::getMonthColumn($count);
          // dd($getMonthColumn);
          $lot_status = $getMonthColumn['lot_status'];
          $lot_column = $getMonthColumn['lot_column'];
          $lot_type = $getMonthColumn['lot_type'];
          // echo $payment_details->$lot_status;die();
          if ($payment_details->$lot_status == 'G' || $payment_details->$lot_status == 'P' || $payment_details->$lot_status == 'S' || $payment_details->$lot_status == 'F' || $payment_details->$lot_status == 'H' || $payment_details->$lot_status == 'M') {
            //$lot_no = $benStatusObj->$lot_column;
            $lotStatus = $payment_details->$lot_status;
            // dd($lotStatus);
            $lot_month = Config::get('constants.monthval.' . $count);
            $monthlower = strtolower($lot_month);

            $lotNumber = '';
            if (isset((json_decode($payment_details->$monthlower))->lot_no)) {
              $lotNumber = (json_decode($payment_details->$monthlower))->lot_no;
            }
            $lotNumber = !is_null($payment_details->$lot_column) ? $payment_details->$lot_column : $lotNumber;
            $paymentDetailsHtml .= '  
                  <tr> 
                      <td>' . $payLoop . '</td> 
                      <td>' . $payment_details->ben_id . '</td>
                      <td>' . $lotNumber . '</td>
                      <td>' . $lot_month . '</td><td>' . $payment_details->fin_year . '</td>';
            // dd(isset((json_decode($payment_details->$monthlower))->ifsc));
            if (isset((json_decode($payment_details->$monthlower))->ifsc)) {
              $paymentDetailsHtml .= '<td>' . (json_decode($payment_details->$monthlower))->ifsc . '</td>';
            } else {
              $paymentDetailsHtml .= '<td></td>';
            }
            if (isset((json_decode($payment_details->$monthlower))->accno)) {
              $paymentDetailsHtml .= '<td>' . (json_decode($payment_details->$monthlower))->accno . '</td>';
            } else {
              $paymentDetailsHtml .= '<td></td>';
            }
            if (isset((json_decode($payment_details->$monthlower))->payment_mode)) {
              $paymentDetailsHtml .= '<td>' . (json_decode($payment_details->$monthlower))->payment_mode . '</td>';
            } else {
              $paymentDetailsHtml .= '<td></td>';
            }
            if ($lotStatus == 'S' || $lotStatus == 'F' || $lotStatus == 'M') {
              $paymentDetailsHtml .= '<td>' . Config::get('constants.lot_status.' . $lotStatus) . '</td>';
            } else {
              $paymentDetailsHtml .= '<td>Payment Under Process</td>';
            }
            if (isset((json_decode($payment_details->$monthlower))->response_received_at)) {
              $response = (json_decode($payment_details->$monthlower))->response_received_at;
              $time = explode('T', $response);
              $paymentDetailsHtml .= '<td>' . date("d-m-Y", strtotime($time[0])) . '</td>';
            } else {
              $paymentDetailsHtml .= '<td></td>';
            }
            $paymentDetailsHtml .= '<td>';
            if ($lotStatus == 'F' || $lotStatus == 'M') {
              $paymentDetailsHtml .= '<button class="btn btn-xs btn-danger" class="js-status-error" value="' . $failedPaymentDetails . '_' . $payment_details->ben_id . '_' . $payment_details->fin_year . '_' . $scheme_id . '" onclick="getStatusUTRAndErrorFun(this.value);">View Error</button>';
            } else {
              $paymentDetailsHtml .= '';
            }
            $paymentDetailsHtml .= '</td>';
            $paymentDetailsHtml .= '</tr>';
            $flag = 1;
            $payLoop = $payLoop + 1;
          } else {
          }
          $count++;
        }
        if ($flag == 0) {
          $paymentDetailsHtml .= '<tr><th colspan="11" style="text-align: center; color: #d9534f;">Payment process yet to start.</th></tr>';
        }
        $paymentDetailsHtml .= '</tbody>
                    </table>';
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
        'exception_message' => $e->getMessage(),
        // 'exception_message' => 'Error! Please try again.',
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
      // dd($yyyy_val);
      // Get current financial year
      if (date('m') > 3) {
        $currentFinYear = date('Y') . "-" . (date('Y') + 1);
      } else {
        $currentFinYear = (date('Y') - 1) . "-" . date('Y');
      }

      $lotObj = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')->where('ben_id', $pension_id)->where('scheme_id', $scheme_id)->first();
      if ($lotObj->pmt_mode == 1) {
        $results = DB::connection('pgsql_paywrite')->select(DB::raw("SELECT remarks FROM payment.failed_payment_details WHERE ben_id = " . $pension_id . " AND scheme_id = " . $scheme_id . " AND lot_no = '" . $lot_no . "'"));
        return  $response = array(
          'status' => 1, 'msg' => $results[0]->remarks,
          'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Information'
        );
      } elseif ($lotObj->pmt_mode == 2) {
        $results = DB::connection('pgsql_paywrite')->select(DB::raw("SELECT ct.description FROM payment.failed_payment_details fp JOIN sbi.credit_transaction_code ct ON fp.status_code = ct.code WHERE ben_id = " . $pension_id . " AND scheme_id = " . $scheme_id . " AND lot_no = '" . $lot_no . "'"));
        // dd($results[0]->description);
        return  $response = array(
          'status' => 1, 'msg' => $results[0]->description,
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

  public function getActionNeedToTaken($data)
  {
    $need_act = array();
    try {
      // Duplicate Aadhaar
      if ($data->dup_aadhar == 1 && is_null($data->dup_aadhar_edit_role_id)) {
        array_push($need_act, 'Duplicate Aadhaar correction pending at Verifier/Approver end.');
      } else if ($data->dup_aadhar == 1 && $data->dup_aadhar_edit_role_id == 1) {
        array_push($need_act, 'Duplicate Aadhaar correction verification pending at Verifier end.');
      } else if ($data->dup_aadhar == 1 && $data->dup_aadhar_edit_role_id == 2) {
        array_push($need_act, 'Duplicate Aadhaar correction approval pending at Approver end.');
      }
    } catch (\Exception $e) {
    }

    try {
      // Duplicate Mobile
      if ($data->dup_mobile == 1 && is_null($data->dup_mobile_edit_role_id)) {
        array_push($need_act, 'Duplicate Mobile correction pending at Verifier/Approver end.');
      } else if ($data->dup_mobile == 1 && $data->dup_mobile_edit_role_id == 1) {
        array_push($need_act, 'Duplicate Mobile correction verification pending at Verifier end.');
      } else if ($data->dup_mobile == 1 && $data->dup_mobile_edit_role_id == 2) {
        array_push($need_act, 'Duplicate Mobile correction approval pending at Approver end.');
      }
    } catch (\Exception $e) {
    }

    try {
      // No Aadhaar
      if ($data->no_aadhar == 1 && is_null($data->next_level_role_id_edit) && ($data->no_aadhar_mobile_flag == 1 || is_null($data->no_aadhar_mobile_flag))) {
        array_push($need_act, 'No Aadhaar correction pending at Verifier/Approver end.');
      } else if ($data->no_aadhar == 1 && $data->next_level_role_id_edit == 999 && $data->no_aadhar_mobile_flag == 1) {
        array_push($need_act, 'No Aadhaar correction verification pending at Verifier end.');
      } else if ($data->no_aadhar == 1 && ($data->next_level_role_id_edit > 0 && $data->next_level_role_id_edit != 999) && $data->no_aadhar_mobile_flag == 1) {
        array_push($need_act, 'No Aadhaar correction approval pending at Approver end.');
      }
    } catch (\Exception $e) {
    }

    try {
      // No Mobile
      if ($data->no_mobile == 1 && is_null($data->next_level_role_id_edit) && is_null($data->no_aadhar_mobile_flag)) {
        array_push($need_act, 'No Mobile correction pending at Verifier/Approver end.');
      } else if ($data->no_mobile == 1 && $data->next_level_role_id_edit == 999 && $data->no_aadhar_mobile_flag == 1) {
        array_push($need_act, 'No Mobile correction verification pending at Verifier end.');
      } else if ($data->no_mobile == 1 && $data->next_level_role_id_edit > 0 && $data->no_aadhar_mobile_flag == 1) {
        array_push($need_act, 'No Mobile correction approval pending at Approver end.');
      }
    } catch (\Exception $e) {
    }

    try {
      // Duplicate Bank Account
      if ($data->dup_bank == 1) {
        array_push($need_act, 'Duplicate Bank A/c rectification pending at Verifier or Approver end.');
      }
    } catch (\Exception $e) {
    }

    /*
    try {
      // Payment Failure
      if ($data->lot_generated == '-1' && $data->bank_edited == '0') {
        array_push($need_act, 'Pending IFMS failed bank rectification at Verifier end.');
      }
      if ($data->lot_generated == '-2' && $data->bank_edited == '0') {
        array_push($need_act, 'Pending RBI failed bank rectification at Verifier end.');
      }
      if ($data->lot_generated == '-3' && $data->bank_edited == '0') {
        array_push($need_act, 'Pending SBI failed bank rectification at Verifier end.');
      }
    } catch (\Exception $e) {
    }
    
    try {
      // Sarasari Mukhomatri Marking for Old Aged Pension
      if ($data->scheme_id == 10 && $data->next_level_role_id > 0 && is_null($data->sm_flag)) {
        array_push($need_act, 'Sarasori Mukhyamantri Mark pending at Verifier end.');
      }
    } catch (\Exception $e) {
    }
    */
    $action_need = '';
    $need_count = 1;
    foreach ($need_act as $key) {
      $action_need .= $need_count . '. ' . $key . '<br>';
      $need_count++;
    }
    return $action_need;
  }
}
