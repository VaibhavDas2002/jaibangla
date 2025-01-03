<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\lot_master;
use App\Scheme;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
// use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\AuthChecker;


class DashboardReportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
   	set_time_limit(200);
    date_default_timezone_set('Asia/Kolkata'); 
  }
  public function markDuplicateAccountNumber(Request $request) {
    $statusCode = 200;
    $response = [];
    if (!$request->ajax()) {
        $statusCode = 400;
        $response = array('error' => 'Error occured in ajax call.');
        return response()->json($response, $statusCode);
    }
    try {
      $user_id = AuthChecker::getUserId();
      if ($user_id == 5994) { // For LPP retainer and pensioner, purohit monthly 
        $query = "(WITH Duplicates AS
        (
        SELECT trim(replace(bank_code,chr(160),'')) AS accno,COUNT(1) as total FROM 
        pension.beneficiary WHERE scheme_id in(8,9) and next_level_role_id=0 and (dup_bank=0 or dup_bank IS NULL) 
        GROUP BY trim(replace(bank_code,chr(160),''))  HAVING COUNT(1) > 1
        )

        select 'LPP Retainer & LPP Pensioner' as scheme_name,8 as scheme_id,coalesce(sum(total),0) as total from Duplicates)
        UNION ALL
        (WITH Duplicates AS
        (
        SELECT scheme_id,trim(replace(bank_code,chr(160),'')) AS accno,COUNT(1) as total FROM 
        pension.beneficiary WHERE scheme_id=17 and next_level_role_id=0 and (dup_bank=0 or dup_bank IS NULL)
        GROUP BY scheme_id,trim(replace(bank_code,chr(160),''))  HAVING COUNT(1) > 1
        )

        select m.scheme_name,m.id as scheme_id,coalesce(sum(d.total),0) as total from Duplicates d JOIN m_scheme m ON d.scheme_id=m.id WHERE m.id=17 group by m.scheme_name,m.id)";
      }
      else {
        $reports = DB::connection('pgsql_mis')->select("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1) order by scheme_name");
        $s_arr = [];
        foreach ($reports as $k) {
          array_push($s_arr, $k->id);
        }
        $s_id = implode(',', $s_arr);
        $query = "WITH Duplicates AS
        (
        SELECT scheme_id,trim(replace(bank_code,chr(160),'')) AS accno,COUNT(1) as total FROM 
        pension.beneficiary WHERE scheme_id in(".$s_id.") and next_level_role_id=0 and (dup_bank=0 or dup_bank IS NULL)
        GROUP BY scheme_id,trim(replace(bank_code,chr(160),''))  HAVING COUNT(1) > 1
        )

        select m.scheme_name,m.id as scheme_id,coalesce(sum(d.total),0) as total from Duplicates d JOIN m_scheme m ON d.scheme_id=m.id WHERE m.id IN(2,10,11,3) group by m.scheme_name,m.id";
      }
      $result = DB::select($query); 
      // $logmessage = "";
      // $logmessage .= date('l d-M-Y h:i:s A') . "\n";
      // $logmessage .= "------------------------------" . "\n";
      // $fileLocation = 'deDuplicate/De_duplicate_bank_2.txt';
      // $logmessage .= "Working" . "\n";
      // Storage::append($fileLocation, $logmessage); 
      if(empty($result)){
        $response = array('status' => 2, 'msg' => 'Duplicate Bank Account Not Found.',
          'type' => 'blue','icon'=>'fa fa-check','title'=>'Not Found');
      }
      else {
        $response = array('status' => 1,'datas' => $result,
          'type' => 'green','icon'=>'fa fa-check','title'=>'Success');
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
  public function markDeDuplicateSchemeWise(Request $request) {
    $statusCode = 200;
    $response = [];
    if (!$request->ajax()) {
        $statusCode = 400;
        $response = array('error' => 'Error occured in ajax call.');
        return response()->json($response, $statusCode);
    }
    try {
      $scheme_id = $request->scheme_id;
      $logmessage = "";
      $logmessage .= date('l d-M-Y h:i:s A') . "\n";
      $logmessage .= "------------------------------" . "\n";
      $fileLocation = 'deDuplicate/De_duplicate_bank_' . $scheme_id . '' . '.txt';
      if ($scheme_id == 8) {
        $res = DB::select('SELECT pension.duplicate_bank_account_lpp(ARRAY[8,9])');
        $msg = 'Total ' . $res[0]->duplicate_bank_account_lpp . ' duplicate beneficiaries found in LPP Retainer & LPP Pensioner scheme';
      }
      else {
        $sObj = DB::connection('pgsql_mis')->select('SELECT scheme_name FROM public.m_scheme WHERE id='.$scheme_id);
        $res = DB::select('SELECT pension.duplicate_bank_account('.$scheme_id.')');
        $msg = 'Total ' . $res[0]->duplicate_bank_account . ' duplicate beneficiaries found in ' . $sObj[0]->scheme_name . ' scheme';
      }
      $logmessage .= $msg . "\n";
      Storage::append($fileLocation, $logmessage);
      $response = array('status' => 1,'msg' => $msg,
        'type' => 'green','icon'=>'fa fa-check','title'=>'Success'
      );
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
  public function getLppApprovedCount(Request $request) {
    // print 'Die';
    $user_id = AuthChecker::getUserId();
    $reports = DB::connection('pgsql_mis')->select("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1) order by scheme_name");
    $s_arr = [];
    foreach ($reports as $k) {
      // array_push($s_arr, $k->id);
      $scheme_id = $k->id;
      $table = $this->getSchemaName($scheme_id);
      ${'query_'.$scheme_id} = "Select district_name as district,
      coalesce(sum(total) ,0)::int as count
      from public.m_district d left join
      (
      select created_by_dist_code,
      sum(case when next_level_role_id='0' then 1 else 0 end )   as total 
      FROM ".$table." b where scheme_id = ".$scheme_id."
      group by created_by_dist_code  
      )b  on d.district_code = b.created_by_dist_code group by d.district_name order by district_name";
      ${'result_'.$scheme_id} = DB::connection('pgsql_mis')->select(${'query_'.$scheme_id});
      
      ${'ben_'.$scheme_id}[] = array('District','Count');
      ${'tCount_'.$scheme_id} = 0;
      foreach(${'result_'.$scheme_id} as $arr) {
        ${'ben_'.$scheme_id}[] = array(
          'District' => trim($arr->district), 
          'Count'  => $arr->count
        );
        ${'tCount_'.$scheme_id} = ${'tCount_'.$scheme_id} + $arr->count;
      }
      ${'ben_'.$scheme_id}[] = array(
          'Total' => 'TOTAL', 
          'Count'  => ${'tCount_'.$scheme_id}
        );
      // print_r(${'ben_'.$key->id});
    }
    $date = date('l d-M-Y h-i-s A');
    Excel::create('LPP Retainer Pensioner Purohit Monthly '.$date, function($excel) use ($ben_8, $ben_9, $ben_17){
      $excel->setTitle('Beneficiary Approved Data');
      $excel->sheet('LPP Retainer', function($sheet) use ($ben_8){
        $sheet->fromArray($ben_8, null, 'A1', false, false);
      });
      $excel->sheet('LPP Pensioner', function($sheet) use ($ben_9){
        $sheet->fromArray($ben_9, null, 'A1', false, false);
      });
      $excel->sheet('Purohits', function($sheet) use ($ben_17){
        $sheet->fromArray($ben_17, null, 'A1', false, false);
      });   
    })->download('xlsx'); 
  }
  public function getBeneficiaryPaymentPending(Request $request)
  {
    $selectscheme = $request->selectscheme;
    $selectyear = $request->selectyear;
    $schemeObj = Scheme::where('id', $selectscheme)->first();
    $tablename = $schemeObj->short_code . '.beneficiary';
    $acc_condition = '';
    // $schemeArr = array(2,10,11,8,9,17,13);
    // if (in_array($selectscheme, $schemeArr)) {
    //   $acc_condition = ' and acc_validated_payment=10 ';
    // }
    $payment_field_name = 'created_at';
    if ($selectscheme == 10 || $selectscheme == 11) {
      $payment_field_name = 'payment_start_date';
    }

    $query = "";
    $query = "SELECT 
    TRIM(TO_CHAR(TO_DATE (RIGHT(fl.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(fl.yymm::varchar,2) AS year_month, 
    fl.fresh_count AS pending_1, 
    fl.fr_count AS pending_10,
    fl.adj_count AS pending_3,
    fl.sd_count AS pending_9,
    fl.sbi_error_count AS pending_6,
    fl.rbi_error_count AS pending_5,
    fl.ifms_error_count AS pending_4
    FROM(
      SELECT f.yymm,
      COALESCE (SUM(f.c) FILTER(WHERE f.t='F'),0) fresh_count,
      COALESCE(SUM(f.c) FILTER(WHERE f.t='FR'),0) fr_count,
      COALESCE(SUM(f.c) FILTER(WHERE f.t='A'),0) adj_count,
      COALESCE(SUM(f.c) FILTER(WHERE f.t='SD'),0) sd_count,
      COALESCE(SUM(f.c) FILTER(WHERE f.t='SF'),0) sbi_error_count,
      COALESCE(SUM(f.c) FILTER(WHERE f.t='RF'),0) rbi_error_count,
      COALESCE(SUM(f.c) FILTER(WHERE f.t='IF'),0) ifms_error_count
      FROM
      (   
        SELECT 'F' AS t,TO_CHAR(".$payment_field_name.",'yymm')::int AS yymm, COUNT(1) AS c FROM ".$tablename." 
        WHERE next_level_role_id=0 AND payment_count=0 AND lot_generated=0 AND last_paid_yymm=0 and scheme_id=".$selectscheme." AND payment_suspended IS NULL AND dup_bank=0 and dup_bank_pending=0 ".$acc_condition." 
        GROUP BY t,TO_CHAR(".$payment_field_name.",'yymm')
        UNION ALL
        SELECT 'FR' AS t, (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,COUNT(1) AS c FROM ".$tablename."
        WHERE next_level_role_id=0 AND payment_count=0 AND lot_generated=0 AND last_paid_yymm>0 AND scheme_id=".$selectscheme." AND payment_suspended IS NULL AND dup_bank=0 and dup_bank_pending=0 ".$acc_condition."  
        GROUP BY t,to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM')::int
        UNION ALL
        SELECT 'A' AS t, (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,COUNT(1) AS c FROM ".$tablename." 
        WHERE next_level_role_id=0 AND payment_count>0 AND lot_generated=0 AND scheme_id=".$selectscheme." AND payment_suspended IS NULL AND dup_bank=0 and dup_bank_pending=0 ".$acc_condition."  
        GROUP BY t,to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM')::int
        UNION ALL
        SELECT 'SD' AS t, (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,COUNT(1) AS c FROM ".$tablename." 
        WHERE next_level_role_id=0 AND payment_count>0 AND lot_generated=2 AND scheme_id=".$selectscheme." AND payment_suspended IS NULL AND dup_bank=0 and dup_bank_pending=0 ".$acc_condition."  
        GROUP BY t,to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM')::int
        UNION ALL
        SELECT fail.t AS t,fail.yymm AS yymm,SUM(fail.c) AS c FROM
        (
          SELECT 
          CASE 
            WHEN lot_generated=-3 THEN 'SF' 
            WHEN lot_generated=-2 THEN 'RF' 
            WHEN lot_generated=-1 THEN 'IF' 
          END AS t ,
          (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,lot_generated,COUNT(1) AS c FROM ".$tablename." 
          WHERE next_level_role_id=0 AND payment_count>0 AND lot_generated IN(-1,-2,-3) 
          AND bank_edited=1 AND scheme_id=".$selectscheme." AND payment_suspended IS NULL AND dup_bank=0 and dup_bank_pending=0 ".$acc_condition."  
          GROUP BY t,(to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int,lot_generated
          UNION ALL
          SELECT 
          CASE 
            WHEN lot_generated=-3 THEN 'SF' 
            WHEN lot_generated=-2 THEN 'RF' 
            WHEN lot_generated=-1 THEN 'IF' 
          END AS t ,
          TO_CHAR(".$payment_field_name.",'yymm')::int AS yymm,lot_generated,COUNT(1) AS c FROM ".$tablename." 
          WHERE next_level_role_id=0 AND payment_count=0 AND lot_generated IN(-1,-2,-3) 
          AND bank_edited=1 AND scheme_id=".$selectscheme." AND payment_suspended IS NULL AND dup_bank=0 and dup_bank_pending=0 ".$acc_condition."  
          GROUP BY t,TO_CHAR(".$payment_field_name.",'yymm'),lot_generated
        ) fail
        GROUP BY fail.t,fail.yymm
      ) f GROUP BY f.yymm
    )fl";
    $data = DB::connection('pgsql')->select($query);
    $htmlTable = $this->getTablePendingBeneficiaryLotCreation($data, $selectscheme);
    return response()->json(['html'=>$htmlTable]);
  }
  public function getTablePendingBeneficiaryLotCreation($data, $scheme_id) {
    $total = 0;
    $flag = 0;
    if (count($data) == 0) {
      $flag = 1;
    }
    else {
      $lotQuery = "select * from public.m_lot_type where id in(
        select unnest(lot_type_id) from m_scheme where id=".$scheme_id.")";
      $lotType = DB::connection('pgsql_mis')->select($lotQuery);
      if (count($lotType)==0) {
        $lotType = DB::connection('pgsql_mis')->select("select * from public.m_lot_type where id in(1,3,4,5,6)");
      }
      $return_arr = [];
      $html = '<table id="examplePendingBen" class="table table-bordered table-condensed table-hover table-striped" cellspacing="0" width="100%">
          <thead>
            <tr role="row" class="sorting_asc">
              <th>Month-Year</th>';
      foreach ($lotType as $lt) {
        ${'tCount_'.$lt->id} = 0;
        $html .= '<th>'.$lt->lot_type.'</th>';
      }        
      $html .= '</tr>
          </thead>
          <tbody>';
      foreach ($data as $k) {
        $html .= '<tr><td>'.$k->year_month.'</td>';
        foreach ($lotType as $lt) {
          $field = 'pending_'.$lt->id;
          $html .= '<td>'.$k->$field.'</td>';
          ${'tCount_'.$lt->id} = ${'tCount_'.$lt->id} + $k->$field;
        }
        $html .= '</tr>';
      }
      $html .= '</tbody><tfoot>
            <tr><th>Total</th>';
      foreach ($lotType as $lt) {
        $html .= '<th>'.${'tCount_'.$lt->id}.'</th>';
      }
      $html .= '</tr>
          </tfoot>       
        </table>';
    }
    if($flag == 1) {
      $html = '<div style="font-size: 18px; font-weight: bold; color: firebrick; text-align:center; font-style: italic;">No record found.</div>';
    }  
    return $html;
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
  public function checkSchemeSession($scheme_id) {
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
  public function getInstantReportData(Request $request) {
    $statusCode = 200;
    $response = [];
    if (!$request->ajax()) {
        $statusCode = 400;
        $response = array('error' => 'Error occured in ajax call.');
        return response()->json($response, $statusCode);
    }
    try {
      $scheme_id = $request->scheme_id;
      $table_name = 'pension.beneficiaries';
      $this->checkSchemeSession($scheme_id);
      $mappingLevel = $request->session()->get('level');
      $district_code = $request->session()->get('distCode');
      $is_first = $request->session()->get('is_first');
      $is_urban = $request->session()->get('is_urban');
      $body_code = $request->session()->get('bodyCode');
      $role_id = $request->session()->get('role_id');
      $sObj = DB::connection('pgsql_mis')->select('SELECT scheme_name FROM public.m_scheme WHERE id='.$scheme_id);
      $data = $this->getAllCounts($scheme_id, $table_name, $district_code, $body_code, $sObj[0]->scheme_name);
      $response = array('status' => 1,'data' => $data,
        'type' => 'green','icon'=>'fa fa-check','title'=>'Success'
      );
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
  private function getAllCounts($scheme_id, $table_name, $district_code, $body_code, $scheme_name) { 
    $query = "SELECT 
    COALESCE(count( b.id) ,0) as applied,
    COALESCE(count( b.id) FILTER(WHERE b.next_level_role_id IS NULL ),0) as verification_pending,
    COALESCE(count( b.id) FILTER(WHERE b.is_verified=1 and b.is_approved=0 and b.is_rejected=0),0) as approval_pending,
    COALESCE(count( b.id) FILTER(WHERE b.next_level_role_id = 0 ),0) as approved,
    COALESCE(count( b.id) FILTER(WHERE b.next_level_role_id < 0 ),0) as rejected,
    COALESCE(count( b.id) FILTER(WHERE b.next_level_role_id = 0 AND dup_bank = 1 ),0) as bank_ac_dup
    -- ,COALESCE(count( b.id) FILTER(WHERE b.next_level_role_id = 0 AND lot_generated IN(-1,-2,-3) ),0) as total_failed,
    -- COALESCE(count( b.id) FILTER(WHERE b.next_level_role_id = 0 AND lot_generated IN(-1,-2,-3) AND bank_edited=0),0) as total_failed_not_edited,
    -- COALESCE(count( b.id) FILTER(WHERE b.next_level_role_id = 0 AND lot_generated=2 ),0) as payment_ongoing,
    -- COALESCE(count( b.id) FILTER(WHERE b.next_level_role_id = 0 AND lot_generated=1 ),0) as pending_response,
    -- COALESCE(count( b.id) FILTER(WHERE (next_level_role_id=0 AND lot_generated=0 and dup_bank=0 and dup_bank_pending=0) OR (b.next_level_role_id = 0 AND lot_generated IN(-1,-2,-3) AND bank_edited=1 and dup_bank=0 and dup_bank_pending=0) ),0) as lot_not_generated
    FROM ".$table_name." b WHERE scheme_id=" . $scheme_id . " ";
    // if (!is_null($district_code)) {
    //   $query .= " AND created_by_dist_code=".$district_code;
    // }
    // if (!is_null($body_code)) {
    //   $query .= " AND created_by_local_body_code=".$body_code;
    // }

    $payQuery = "select bp.scheme_id
    , SUM(case WHEN is_eligible=true and is_rejected=0 THEN 1 else 0 end) AS approved_ben
    , SUM(case WHEN ben_status=1 and is_eligible=true and pay_validated=1 and is_rejected=0 and dup_bank=0 THEN 1 else 0 end) AS eligible_for_payment
    , SUM(case WHEN ben_status=1 and is_eligible=true and is_rejected=0 and pay_validated<>1 and dup_bank=0 THEN 1 else 0 end) AS failed_edit_pending
    , SUM(case WHEN ben_status=2 and is_eligible=true and is_rejected=0 and dup_bank=0 THEN 1 else 0 end) AS janma_mrityu_stopped
    , SUM(case WHEN ben_status=3 and is_eligible=true and is_rejected=0 and dup_bank=0 THEN 1 else 0 end) AS pause_payment
    , SUM(case WHEN dup_bank=1 and is_eligible=true and is_rejected=0 THEN 1 else 0 end) AS duplicate_bank
    FROM payment.ben_payment_details bp 
    where bp.scheme_id = " . $scheme_id . " 
    group by bp.scheme_id ";

    $data = DB::connection('pgsql_mis')->select($query);
    $paydata = DB::connection('pgsql_paywrite')->select($payQuery);

    $fHtml ='';
    $fHtml .= '<div class="panel panel-default" style="font-size: 15px;">';
    $fHtml .= '<div style="padding: 5px; border-bottom: 1px solid #ddd; background-color: whitesmoke;"><span style="font-size: 14px; font-style: italic; font-weight:bold; text-align:center;" class="text-success">' . $scheme_name . '</span><span style="font-size: 12px; float: right;">Report Generated On : ' . date('l d-M-Y h:i:s A') . '</span></div>';
    $fHtml .= '<div class="panel-body">';
    $fHtml .= '<div class="row"><div class="col-md-4">';
    $fHtml .= 'Applied : ' . $this->moneyFormatIndia($data[0]->applied) . '</br>';
    $fHtml .= 'Verification Pending : ' . $this->moneyFormatIndia($data[0]->verification_pending) . '</br>';
    $fHtml .= 'Approval Pending : ' . $this->moneyFormatIndia($data[0]->approval_pending) . '</br>';
    $fHtml .= 'Total Rejected : ' . $this->moneyFormatIndia($data[0]->rejected) . '</br>';
    $fHtml .= '</div><div class="col-md-4">';
    $fHtml .= 'Approved : ' . $this->moneyFormatIndia($data[0]->approved) . '</br>';

    $fHtml .= 'Duplicate Bank A/c : ' . $this->moneyFormatIndia($paydata[0]->duplicate_bank) . '</br>';
    $fHtml .= 'Janma-Mrityu Thathya Death Incident : ' . $this->moneyFormatIndia($paydata[0]->janma_mrityu_stopped) . '</br>';
    // $fHtml .= 'Bank Faliure : ' . $data[0]->total_failed . '</br>';
    $fHtml .= 'Bank Failure Rectification Pending : ' . $this->moneyFormatIndia($paydata[0]->failed_edit_pending) . '</br>';
    $fHtml .= '</div><div class="col-md-4">';
    $fHtml .= 'Payment Ongoing : ' . $this->moneyFormatIndia($paydata[0]->eligible_for_payment) . '</br>';
    $schemeArrAfterStandardLot = array(8,9,17);
    if (in_array($scheme_id, $schemeArrAfterStandardLot)) {
      $fHtml .= 'Pause Payment : ' . $this->moneyFormatIndia($paydata[0]->pause_payment) . '</br>';
    }
    // $fHtml .= 'Lot Not Generated : ' . $data[0]->lot_not_generated . '</br>';
    $fHtml .= '</div></div></div></div>';
    return $fHtml;
  }
  public function getInstantPaymentReport(Request $request) {
    $statusCode = 200;
    $response = [];
    if (!$request->ajax()) {
        $statusCode = 400;
        $response = array('error' => 'Error occured in ajax call.');
        return response()->json($response, $statusCode);
    }
    try {
      $scheme_id = $request->scheme_id;
      $year = $request->lot_year;
      $month = $request->lot_month;
      $table_name = $this->getSchemaName($scheme_id);
      $explode_year = explode('-', $year);
      $search_year = $explode_year[0];
      if ($month == 'January' || $month == 'February' || $month == 'March') {
        $search_year = $search_year + 1;
      }
      $getMonthColumn = Helper::getMonthColumn($month);
      $lotQuery = "select bt.scheme_id,
        sum(case when " . $getMonthColumn['lot_status'] . "='S' then 1 else 0 end)        as payment_success,
        sum(case when " . $getMonthColumn['lot_status'] . "='F' then 1 else 0 end)        as payment_failure,
        sum(case when " . $getMonthColumn['lot_status'] . "='M' then 1 else 0 end)        as ifms_returned,
        sum(case when " . $getMonthColumn['lot_payment_amount'] . " <> 0 then " . $getMonthColumn['lot_payment_amount'] . " else 0 end)        as amount_disbursed
      FROM payment.ben_transaction_details bt
      where bt.fin_year= '" . $year . "' and bt.scheme_id=" . $scheme_id . " group by bt.scheme_id";

      // $preQuery = "SELECT COALESCE(count(b.id) FILTER(WHERE b.last_paid_yymm < CONCAT((RIGHT(".$search_year."::varchar,2)),(LPAD((EXTRACT(MONTH FROM TO_DATE('".$month."', 'Month')))::text, 2, '0')))::int and b.next_level_role_id=0),0) as payment_lying 
      // FROM " . $table_name . " b WHERE scheme_id=" . $scheme_id . "";
      $lotData = DB::connection('pgsql_paywrite')->select($lotQuery);
      // $preData = DB::connection('pgsql_mis')->select($preQuery);
      $fHtml = '';
      $fHtml .= '<div style="font-size: 15px; padding-top: 10px;">';
      $fHtml .= 'Total Success : ' . $this->moneyFormatIndia($lotData[0]->payment_success) . '</br>';
      $fHtml .= 'Total Failed : ' . $this->moneyFormatIndia((($lotData[0]->payment_failure)+($lotData[0]->ifms_returned))) . '</br>';
      $fHtml .= 'Total Amount Disbursed(In Lakh.) : ' . $this->moneyFormatIndia($lotData[0]->amount_disbursed) . '</br>';
      // $fHtml .= 'Payment lied on previous months : ' . $preData[0]->payment_lying . '</br>';
      $fHtml .= '</div>';
      $response = array('status' => 1,'data' => $fHtml,
        'type' => 'green','icon'=>'fa fa-check','title'=>'Success'
      );
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

  function moneyFormatIndia($num){
    $explrestunits = "" ;
    if(strlen($num)>3){
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i < sizeof($expunit);  $i++){
            // creates each of the 2's group and adds a comma to the end
            if($i==0)
            {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            }else{
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash; // writes the final format where $currency is the currency symbol.
}
}
