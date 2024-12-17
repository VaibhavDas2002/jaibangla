<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;
use Auth;
use App\Configduty;
use App\lot_master;
use App\Scheme;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Excel;

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
      $user_id = Auth::user()->id;
      $reports = DB::connection('pgsql_mis')->select("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1) order by scheme_name");
      $s_arr = [];
      foreach ($reports as $k) {
        array_push($s_arr, $k->id);
      }
      $s_id = implode(',', $s_arr);
      $query = "WITH Duplicates AS
      (
      SELECT scheme_id,substring(trim(replace(bank_ifsc,chr(160),'')) ,1,11)  AS ifsc,trim(replace(bank_code,chr(160),'')) AS accno,COUNT(1) as total FROM 
      pension.beneficiary WHERE scheme_id in(".$s_id.") and next_level_role_id=0
      GROUP BY scheme_id,trim(replace(bank_code,chr(160),'')) ,substring(trim(replace(bank_ifsc,chr(160),'')) ,1,11) HAVING COUNT(1) > 1
      )

      select m.scheme_name,m.id as scheme_id,coalesce(sum(d.total),0) as total from Duplicates d JOIN m_scheme m ON d.scheme_id=m.id group by m.scheme_name,m.id";
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
      
      $sObj = DB::connection('pgsql_mis')->select('SELECT scheme_name FROM public.m_scheme WHERE id='.$scheme_id);
      // $v_total = 5;
      $res = DB::select('SELECT pension.duplicate_bank_account('.$scheme_id.')');
      // print_r($res);
      $msg = 'Total ' . $res[0]->duplicate_bank_account . ' duplicate beneficiaries found in ' . $sObj[0]->scheme_name . ' scheme';
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
    $user_id = Auth::user()->id;
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
        SELECT 'F' AS t,TO_CHAR(created_at,'yymm')::int AS yymm, COUNT(1) AS c FROM ".$tablename." 
        WHERE next_level_role_id=0 AND payment_count=0 AND lot_generated=0 AND last_paid_yymm=0 and scheme_id=".$selectscheme."
        GROUP BY t,TO_CHAR(created_at,'yymm')
        UNION ALL
        SELECT 'FR' AS t, (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,COUNT(1) AS c FROM ".$tablename."
        WHERE next_level_role_id=0 AND payment_count=0 AND lot_generated=0 AND last_paid_yymm>0 AND scheme_id=".$selectscheme."
        GROUP BY t,to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM')::int
        UNION ALL
        SELECT 'A' AS t, (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,COUNT(1) AS c FROM ".$tablename." 
        WHERE next_level_role_id=0 AND payment_count>0 AND lot_generated=0 AND scheme_id=".$selectscheme."
        GROUP BY t,to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM')::int
        UNION ALL
        SELECT 'SD' AS t, (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,COUNT(1) AS c FROM ".$tablename." 
        WHERE next_level_role_id=0 AND payment_count>0 AND lot_generated=2 AND scheme_id=".$selectscheme."
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
          AND bank_edited=1 AND scheme_id=".$selectscheme."
          GROUP BY t,(to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int,lot_generated
          UNION ALL
          SELECT 
          CASE 
            WHEN lot_generated=-3 THEN 'SF' 
            WHEN lot_generated=-2 THEN 'RF' 
            WHEN lot_generated=-1 THEN 'IF' 
          END AS t ,
          TO_CHAR(created_at,'yymm')::int AS yymm,lot_generated,COUNT(1) AS c FROM ".$tablename." 
          WHERE next_level_role_id=0 AND payment_count=0 AND lot_generated IN(-1,-2,-3) 
          AND bank_edited=1 AND scheme_id=".$selectscheme."
          GROUP BY t,TO_CHAR(created_at,'yymm'),lot_generated
        ) fail
        GROUP BY fail.t,fail.yymm
      ) f GROUP BY f.yymm
    )fl";
    $data = DB::connection('pgsql_mis')->select($query);
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
}
