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

class TrackApplicantPublicController extends Controller
{
  public function __construct()
  {
    set_time_limit(120);
    //$this->middleware('auth');
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
 
  /*
		Get First Landing Page
  */
  public function index(Request $request)
  {
    ini_set('max_execution_time', 300); 
    $form_submitted=0;
    $sel_scheme_code=NULL;
    $sel_select_type=NULL;
    $sel_applicant_id=NULL;
    $is_error=NULL;
    $is_succes=NULL;
    $error_msg=array();
    $row_list=array();
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
      $sel_scheme_code=$request->scheme_code;
      $sel_select_type=$request->select_type;
      $sel_applicant_id=$request->applicant_id;
      $whereCondition="where 1=1";
      $whereCondition=$whereCondition.' and scheme_id='.$sel_scheme_code;
      $applicant_id_rules = "required";
      $select_type_rules = "required";
      if(count($error_msg)==0){
        $rules = [
          'scheme_code' => 'required|numeric',
          'select_type' => $select_type_rules,
          'applicant_id' => $applicant_id_rules,
          'captcha' => 'required|captcha',
        ];
        $form_submitted=1;
        $attributes = array();
        $messages = array();
        $messages['captcha.captcha'] = "Invalid captcha code";
        $attributes['scheme_code'] = 'Scheme';
        $attributes['select_type'] = 'Search Using';
        $attributes['applicant_id'] = 'Beneficiary Id/Mobile No./Aadhaar No.';

        $attributes['captcha'] = 'Captcha code';
        if($sel_select_type==1)
        {
          $attributes['applicant_id'] = 'Beneficiary Id';
          $whereCondition=$whereCondition." and b.id=".$sel_applicant_id;

        }
        else if($sel_select_type==2)
        {
          $rules['applicant_id'] = $applicant_id_rules.'|numeric|digits:10';   
          $attributes['applicant_id'] = 'Mobile No.';
          $whereCondition=$whereCondition." and b.mobile_no=".$sel_applicant_id;
        }
        else if($sel_select_type==3)
        {
          $rules['applicant_id'] =  $applicant_id_rules.'|numeric|digits:12';   
          $attributes['applicant_id'] = 'Aadhaar No.'; 
          $whereCondition=$whereCondition." and b.aadhar_no='".$sel_applicant_id."'";

        }
        else{
          $rules['applicant_id'] =  $applicant_id_rules;   
        }
       
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
          
            if(!in_array($sel_select_type,array(1,2,3))){
                array_push( $error_msg,'Code not Valid');
            }
              $scheme_row=Scheme::where('id',$sel_scheme_code)->first();
              if(is_null($scheme_row)){
                array_push( $error_msg,'Scheme Not Valid');
              }
              if(count($error_msg)==0){
              if($scheme_row->id){
                $is_succes = 1;
                if (!empty($scheme_row->short_code)) {
                  $schema = $scheme_row->short_code;
                } else {
                  $schema = "pension";
                 }
                 $query = "select b.id,b.is_rejected,b.is_verified,b.is_approved,b.created_at,b.approval_date,b.scheme_id,b.next_level_role_id,b.ben_fname,b.ben_mname,b.ben_lname,b.mobile_no,b.aadhar_no,b.gp_ward_name,b.block_ulb_name,b.bank_ifsc, 
                 b.bank_name, b.branch_name, b.bank_code, b.payment_count, b.last_paid_yymm,b.scheme_id,b.created_by_dist_code,
                 b.created_by_local_body_code, 
                 md.district_name, md1.district_name as loc_dist, bl_div.block_subdiv_name, ms.scheme_name,  
                 CASE 
                   WHEN b.next_level_role_id is null and b.is_reverted is null THEN 'Applied (Verification Pending)' 
                   WHEN b.is_verified=1 and b.is_approved=0 and b.is_rejected=0	THEN 'Verified (Approval Pending)'
                   WHEN b.is_reverted=1 and b.is_approved=0 and b.is_rejected=0 and b.next_level_role_id is null	THEN 'Application is reverted (Application will be found in Operator end)'
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
                 from " . $schema . ".beneficiary b 
                 LEFT JOIN public.m_district md ON md.district_code=b.dist_code 
                 LEFT JOIN public.m_district md1 ON md1.district_code=b.created_by_dist_code 
                 JOIN public.m_scheme ms ON ms.id=b.scheme_id  
                 JOIN (SELECT block_code AS block_subdiv_code,block_name AS block_subdiv_name FROM public.m_block 
                   UNION ALL
                   SELECT sub_district_code AS block_subdiv_code, sub_district_name AS block_subdiv_name FROM 		public.m_sub_district
                 ) bl_div ON bl_div.block_subdiv_code=b.created_by_local_body_code ".$whereCondition;
                 //dd($query);
                $row_list_data = DB::connection('pgsql_mis')->select($query);
                $i=0;
                foreach( $row_list_data as  $row_item){
                  $row_list[$i]['id']=$row_item->id;
                  $row_list[$i]['scheme_id']=$row_item->scheme_id;
                  $row_list[$i]['is_rejected']=$row_item->is_rejected;
                  if (!empty($row_item->ben_fname)) {
                    $ben_fname = trim($row_item->ben_fname);
                    } else {
                        $ben_fname = '';
                    }
                    if (!empty($row_item->ben_mname)) {
                        $ben_mname = trim($row_item->ben_mname);
                    } else {
                        $ben_mname = '';
                    }
                    if (!empty($row_item->ben_lname)) {
                        $ben_lname = trim($row_item->ben_lname);
                    } else {
                        $ben_lname = '';
                    }
                  $ben_fullname = $ben_fname . " " . $ben_mname . " " . $ben_lname;
                  $row_list[$i]['name']=$ben_fullname;
                  $address = '';
                  if (!empty(trim($row_item->district_name)) && !empty(trim($row_item->block_ulb_name)) && !empty(trim($row_item->gp_ward_name))) {
                      $address = 'District - ' . trim($row_item->district_name) . '<br>';
                      $address .= 'Block/Municipality - ' . trim($row_item->block_ulb_name) . '<br>';
                      $address .= 'Gp/Ward - ' . trim($row_item->gp_ward_name);
                  }
                  else{
                    $address = 'District - ' . trim($row_item->loc_dist) . '<br>';
                    $address .= 'Block/Sub Division - ' . trim($row_item->block_subdiv_name) . '<br>';
                  }
                  $row_list[$i]['address']=$address;
                  $bank = '';
                  if (!is_null($row_item->bank_name)) {
                    $bank .= 'Bank Name - ' . trim($row_item->bank_name) . '<br>';
                  }
                  if (!is_null($row_item->branch_name)) {
                    $bank .= 'Branch - ' . trim($row_item->branch_name) . '<br>';
                  }
                  $bank .= 'A/c No - ' . trim($row_item->bank_code) . '<br>';
                  $bank .= 'IFSC - ' . trim($row_item->bank_ifsc);
                  $row_list[$i]['bank_info']=$bank;
                  $created_at = '';
                  $created_at .= 'Applied At - ' .  date("d-m-Y", strtotime($row_item->created_at));
                  $row_list[$i]['created_at']=$created_at;
                  $approval_date = '';
                  $approval_date .= 'Approved At - ' .  date("d-m-Y", strtotime($row_item->approval_date));
                  $row_list[$i]['approval_date']=$approval_date;
                  $action = '<h5><b>';
                  $class = '';
                  $action_text = '';
                  if ($row_item->next_level_role_id == 0) {
                    $class = 'text-success';
                  } else if ($row_item->is_rejected == 1) {
                    $class = 'text-danger';
                  } else if ($row_item->is_verified == 1 and $row_item->is_approved == 0 and $row_item->is_rejected == 0) {
                    $class = 'text-warning';
                  } else {
                    $class = 'text-info';
                  }

                  if ($row_item->scheme_id == 17) {
                    if ($row_item->next_level_role_id == 106) {
                      $action_text .= '<span class="' . $class . '">Recommended (Approval Pending)</span>';
                    }
                  }

                  if ($action_text == '') {
                    $action_text .= '<span class="' . $class . '">' . $row_item->app_status . '</span>';
                  }
                  $action .= $action_text . '</b></h5>';
                  $row_list[$i]['current_status']=$action_text;
                  $i++;
                }
                //dd($row_list);
              }
            }
              
        }
        else{
              $is_error = 1;
              $error_msg = $validator->errors()->all();
        }
      }
     
    }
    
    $errormsg = Config::get('constants.errormsg');
    $scheme = DB::connection('pgsql_mis')->select('select id,scheme_name from public.m_scheme where is_active=1 order by scheme_name');
    if (count($scheme) > 0) {
      return view('publicView/publicApplicationTrack', 
      ['schemes' => $scheme,
      'is_public' => 1,
      'form_submitted' => $form_submitted,
      'sel_scheme_code' => $sel_scheme_code,
      'sel_select_type' => $sel_select_type,
      'sel_applicant_id' => $sel_applicant_id,
      'is_error' => $is_error,
      'is_succes' => $is_succes,
      'error_msg' => $error_msg,
      'row_list'=> $row_list,
      'sessiontimeoutmessage' => $errormsg['sessiontimeOut']]
     );
    } 
  }
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
      ->where('fin_year', $fin_year)->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->first();
      $failedPaymentDetails = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->value('lot_no');
    
      // HTML table generated
      $payLoop = 1;
      $paymentDetailsHtml = '';

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
            <th>Beneficiary Id</th>
            <th>Financial Year</th>
            <th>Month</th>
            <th>Payment Status</th>
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

            $paymentDetailsHtml .= '  
                  <tr> 
                      <td>' . $payment_details->ben_id . '</td>
                      <td>' . $payment_details->fin_year . '</td>
                      <td>'.$lot_month.'</td>';
                      // dd(isset((json_decode($payment_details->$monthlower))->ifsc));
          
            if ($lotStatus == 'S' || $lotStatus == 'F' || $lotStatus == 'M') {
              $paymentDetailsHtml .= '<td>' . Config::get('constants.lot_status.' . $lotStatus) . '';
            } else {
              $paymentDetailsHtml .= '<td>Payment Under Process';
            }
            
           /* $paymentDetailsHtml .= '<td>';*/
            if ($lotStatus == 'F' || $lotStatus == 'M') {
              $paymentDetailsHtml .= '&nbsp;&nbsp;<button class="btn btn-xs btn-danger" class="js-status-error" value="' . $failedPaymentDetails . '_' . $payment_details->ben_id . '_' . $payment_details->fin_year . '_' . $scheme_id . '" onclick="getStatusUTRAndErrorFun(this.value);">View Error</button>';
            } else {
              $paymentDetailsHtml .= '';
            }
            $paymentDetailsHtml .= '</td>';
            $paymentDetailsHtml .= '</tr>';
            $flag = 1;
          } else {
          }
          $count++;
          
        }
        if ($flag == 0) {
          $paymentDetailsHtml .= '<tr><th colspan="3" style="text-align: center; color: #d9534f;">Payment process yet to start.</th></tr>';
        }
        $paymentDetailsHtml .= '</tbody>
                    </table>';
      }
      else {
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
        $results = DB::connection('pgsql_paywrite')->select(DB::raw("SELECT remarks FROM payment.failed_payment_details WHERE ben_id = ".$pension_id." AND scheme_id = ".$scheme_id." AND lot_no = '".$lot_no."'"));
        return  $response = array(
          'status' => 1, 'msg' => $results[0]->remarks,
          'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Information'
        );
      } elseif ($lotObj->pmt_mode == 2) {
        $results = DB::connection('pgsql_paywrite')->select(DB::raw("SELECT ct.description FROM payment.failed_payment_details fp JOIN sbi.credit_transaction_code ct ON fp.status_code = ct.code WHERE ben_id = ".$pension_id." AND scheme_id = ".$scheme_id." AND lot_no = '".$lot_no."'"));
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
  
}
