<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Configduty;
use DB;
use Auth;
use App\lot_master;

class ReportMonthwiseReportLotStatusController extends Controller
{
    public function __construct()
  	{
    	$this->middleware('auth');
    	set_time_limit(300);
  	}
    public function index(Request $request){
      if (Auth::user()->designation_id_old == 'DDO' || Auth::user()->designation_id_old == 'HOD') {
        $user_id = Auth::user()->id;
        $schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->get();

        if(request()->ajax())
        { 
          $scheme = $request->scheme;
          $lot_year = $request->lot_year;
          $lot_month = $request->lot_month;
          $pay_mode = $request->pay_mode;
          
          if ($pay_mode == 'IFMS') {
            $query = "select
              s.scheme_name scheme_name,
              l.lot_month lot_month,
              count(*) total,
              sum(case when l.rbi_success_count is null then 1 else 0 end) response_pending,
              sum(case when tld.wrongdata_flag=0 and l.ref_no >0 and l.rbi_success_count is not null then 1 else 0 end ) success,
              sum(case when wrongdata_flag>0 then 1 else 0 end) failed,
              sum (case when tld.wrongdata_flag=0 and tld.is_active>0 and l.ref_no>0 and l.rbi_success_count is not null then 1 else 0 end) eligible_for_next_month,
              sum (case when tld.wrongdata_flag=0 and tld.is_active<0 and l.ref_no>0 and l.rbi_success_count is not null then 1 else 0 end) not_eligible_for_next_month,
              sum (case when l.repeat_drn_part is not null  and tld.wrongdata_flag=0 and tld.is_active>0 and l.rbi_success_count is not null then 1 else 0 end) repeat_lot_done,
              sum (case when l.repeat_drn_part is null and l.ref_no>0 and tld.wrongdata_flag=0 and tld.is_active>0 and l.rbi_success_count is not null and (l.repeat_lot=0 or l.repeat_lot is null) then 1 else 0 end) pending_for_repeat_lot
              from ifms.transaction_lot_details tld, lot_master l, m_scheme s where l.lot_no=tld.drn_part
              and l.lot_month='".$lot_month."' and l.scheme_id=".$scheme." and s.id=l.scheme_id
              group by s.scheme_name,l.lot_month
              order by s.scheme_name,l.lot_month";
          }
          else if ($pay_mode == 'SBI') {
            $query = "select
              s.scheme_name scheme_name,
              l.lot_month lot_month,
              count(*) total,
              sum(case when tld.status_code is null then 1 else 0 end) response_pending,
              sum(case when tld.status_code='S00'  then 1 else 0 end ) success,
              sum(case when tld.status_code ilike 'E%' then 1 else 0 end) failed,
              sum (case when tld.status_code='S00' and tld.is_active>0 then 1 else 0 end) eligible_for_next_month,
              sum (case when tld.status_code='S00' and tld.is_active<0 then 1 else 0 end) not_eligible_for_next_month,
              sum (case when l.repeat_drn_part is not null  and tld.status_code='S00' and tld.is_active>0 then 1 else 0 end) repeat_lot_done,
              sum (case when l.repeat_drn_part is null  and tld.status_code='S00' and tld.is_active>0 then 1 else 0 end) pending_for_repeat_lot
              from sbi.transaction_lot_details tld, lot_master l, m_scheme s where l.lot_no=tld.lot_no
              and l.lot_month='".$lot_month."' and tld.scheme_id=".$scheme." and s.id=l.scheme_id
              group by s.scheme_name,l.lot_month
              order by s.scheme_name,l.lot_month";
          }
          // $data = $data->get();  
          $data = DB::connection('pgsql_mis')->select($query);
          //print_r($query);die();          
          return datatables()->of($data)
            ->addColumn('scheme_name', function ($data){
                return $data->scheme_name;
            })
            ->addColumn('lot_month', function ($data){
                return $data->lot_month;
            })
            ->addColumn('total', function ($data) {
                return $data->total;
            })
            ->addColumn('response_pending', function ($data) {
                return $data->response_pending;
            })
            ->addColumn('get_lot_response_pending', function ($data) use ($scheme, $pay_mode, $lot_year){
              $btn = '';
              if ($data->response_pending != 0) {
                $val="'".$scheme.'_'.$data->lot_month.'_'.$lot_year.'_'.$pay_mode."'";
                $btn .= '<button class="btn btn-info btn-sm" id="get_response_pending" onclick="getResponsePendingLot('.$val.')">Get Lot</button>';
              }
              else {
                $btn = '<span class="label label-success">No Pending</span>';
              }
              return $btn;
            })
            ->addColumn('success', function ($data) {
                return $data->success;
            })
            ->addColumn('failed', function ($data){
                return $data->failed;
            })
            ->addColumn('eligible_for_next_month', function ($data) {
              return $data->eligible_for_next_month;
            })
            ->addColumn('not_eligible_for_next_month', function ($data){
                return $data->not_eligible_for_next_month;
            })
            ->addColumn('repeat_lot_done', function ($data){
                return $data->repeat_lot_done;
            })
            ->addColumn('pending_for_repeat_lot', function ($data){
                return $data->pending_for_repeat_lot;
            })
            ->addColumn('get_lot_pending_for_repeat_lot', function ($data) use ($scheme, $pay_mode, $lot_year){
              $btn = '';
              if ($data->pending_for_repeat_lot != 0) {
                $val="'".$scheme.'_'.$data->lot_month.'_'.$lot_year.'_'.$pay_mode."'";
                $btn .= '<button class="btn btn-warning btn-sm" id="get_repeat_pending" onclick="getRepeatPendingLot('.$val.')">Get Lot</button>';
              }
              else {
                $btn = '<span class="label label-success">No Pending</span>';
              }
              return $btn;
            })
          
          ->rawColumns(['scheme_name','lot_month','total','response_pending','get_lot_response_pending','success','failed','eligible_for_next_month','not_eligible_for_next_month','repeat_lot_done','pending_for_repeat_lot','get_lot_pending_for_repeat_lot'])
          ->make(true);
        }
        return view('monthwise-lot-status-report/consolidated_lot_status', ['schemes'=>$schemes]); 
      }
      else {
        return redirect("/")->with('success', 'User Disabled');
      }
    }
  	// public function index(Request $request){
   //    if (Auth::user()->designation_id_old == 'DDO' || Auth::user()->designation_id_old == 'HOD') {
   //      $user_id = Auth::user()->id;
   //      $schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->get();

   //      if(request()->ajax())
   //      { 
   //        $scheme = $request->scheme;
   //        $lot_year = $request->lot_year;
   //        $lot_month = $request->lot_month;
   //        $pay_mode = $request->pay_mode;

   //        $lot_month_str = '';
   //        foreach ($lot_month as $key) {
   //          $lot_month_str .= "'".$key."',";
   //        }
   //        $month = rtrim($lot_month_str, ",");
          
   //        if ($scheme == 'All') {
   //          $scheme_str = '';
   //          $user_id = Auth::user()->id;
   //          $schemes=Configduty::select('scheme_id')->where('user_id','=',$user_id)->where('is_active',1)->get();
   //          foreach ($schemes as $k) {
   //            $scheme_id = $k->scheme_id;
   //            $scheme_str .= $scheme_id.',';

   //          }
   //          $scheme_id = rtrim($scheme_str, ",");  
   //        }
   //        else {
   //          $scheme_id = $scheme;
   //        }

   //        if ($pay_mode == 'IFMS') {
   //          $query = "select 
   //            m_scheme.scheme_name as scheme_name,
   //            m_scheme.id as scheme_id,
   //            lot_month as lot_month,
   //            ifms.status.status_description as status_description,
   //            ifms.transaction_lot.lot_status as lot_status,
   //            count(*) as lot_count,
   //            sum(ben_count) as ben_count,
   //            coalesce(SUM(rbi_success_count),0) as success_count,
   //            coalesce(SUM(rbi_failed_count + ifms_wrongdata_count),0) as failed_count
   //          from ifms.transaction_lot 
   //          join ifms.status on ifms.status.status_code = ifms.transaction_lot.lot_status
   //          join m_scheme on m_scheme.id=ifms.transaction_lot.scheme_id
   //          where lot_month in(".$month.") and scheme_id in(".$scheme_id.") 
   //          group by m_scheme.scheme_name,lot_month,ifms.status.status_description,m_scheme.id,ifms.transaction_lot.lot_status 
   //          order by m_scheme.scheme_name,to_date(lot_month,'Month')";
   //        }
   //        else if ($pay_mode == 'SBI') {
   //          $query = "select
   //            tl.lot_month as lot_month,
   //            COUNT(tl.lot_no) as lot_count,
   //            s.scheme_name as scheme_name,
   //            s.id as scheme_id,
   //            st.status_description as status_description,
   //            tl.lot_status as lot_status,
   //            SUM(tl.credit_count) as ben_count,
   //            SUM(tl.success_count) as success_count,
   //            SUM(tl.failed_count) as failed_count
   //          FROM sbi.transaction_lot tl 
   //          INNER JOIN m_scheme s on tl.scheme_id=s.id
   //          INNER JOIN sbi.status st on st.status_code=tl.lot_status
   //          WHERE tl.lot_month in(".$month.") and tl.scheme_id in(".$scheme_id.")
   //          GROUP BY tl.scheme_id,tl.lot_status,s.scheme_name,st.status_description,tl.lot_month,s.id,tl.lot_status
   //          ORDER BY tl.scheme_id,to_date(tl.lot_month,'Month')";
   //        }
   //        // $data = $data->get();  
   //        $data = DB::connection('pgsql_mis')->select($query);
   //        //print_r($query);die();          
   //        return datatables()->of($data)
   //          ->addColumn('scheme_name', function ($data){
   //              return $data->scheme_name;
   //          })
   //          ->addColumn('lot_month', function ($data){
   //              return $data->lot_month;
   //          })
   //          ->addColumn('lot_count', function ($data) {
   //              return $data->lot_count;
   //          })
   //          ->addColumn('get_lot', function ($data) use ($pay_mode, $lot_year){
   //            $btn = '';
   //            $val="'".$data->scheme_id.'_'.$data->lot_month.'_'.$data->lot_status.'_'.$pay_mode.'_'.$lot_year."'";
   //            $btn .= '<button class="btn btn-info btn-sm" id="get_lot_btn" value="'.$data->scheme_id.'_'.$data->lot_month.'_'.$data->lot_status.'_'.$pay_mode.'_'.$lot_year.'" onclick="getLotFunction('.$val.')">Get Lot</button>';
   //            return $btn;
   //          })
   //          ->addColumn('status_description', function ($data) use ($pay_mode){
   //              // return $data->status_description;
   //            $status = $data->lot_status;
   //            if ($pay_mode == 'IFMS') {
   //              if ($status == 0) {
   //                $msg = 'Lot pushed to IFMS server pending.';
   //              }
   //              elseif ($status == 1) {
   //                $msg = 'Lot acknowledgement from IFMS server pending.';
   //              }
   //              elseif ($status == 2) {
   //                $msg = 'Lot response from IFMS server pending.';
   //              }
   //              elseif ($status == 3) {
   //                $msg = 'Lot response form IFMS compiled into DB pending.';
   //              }
   //              elseif ($status == 4) {
   //                $msg = 'Lot response from RBI pending.';
   //              }
   //              elseif ($status == 5) {
   //                $msg = 'Lot response from RBI compiled into DB pending.';
   //              }
   //              elseif ($status == 6) {
   //                $msg = 'Payment Completed.';
   //              }
   //              else {
   //                $msg = '';
   //              }
   //            }
   //            elseif ($pay_mode == 'SBI') {
   //              if ($status == 0) {
   //                $msg = 'Lot Signed and will be pushed in next cycle pending.';
   //              }
   //              elseif ($status == 1) {
   //                $msg = 'Lot pushed to SBI server pending.';
   //              }
   //              elseif ($status == 2) {
   //                $msg = 'Lot acknowledgement received from SBI server pending.';
   //              }
   //              elseif ($status == 3) {
   //                $msg = 'Lot response received from SBI server pending.';
   //              }
   //              elseif ($status == 4) {
   //                $msg = 'Lot response compiled into DB pending.';
   //              }
   //              elseif ($status == 5) {
   //                $msg = 'Payment Completed';
   //              }
   //              else {
   //                $msg = '';
   //              }
   //            }

   //            return $msg;
   //          })
   //          ->addColumn('ben_count', function ($data){
   //              return $data->ben_count;
   //          })
   //          ->addColumn('success_count', function ($data) {
   //            if ($data->success_count == 0) {
   //              return 'NA';
   //            }
   //            else {
   //              return $data->success_count;
   //            }
   //          })
   //          ->addColumn('failed_count', function ($data){
   //              if ($data->failed_count == 0) {
   //                return 'NA';
   //              }
   //              else {
   //                return $data->failed_count;
   //              }
   //          })
          
   //        ->rawColumns(['scheme_name','lot_month','lot_count','get_lot','status_description','ben_count','success_count','failed_count'])
   //        ->make(true);
   //      }
   //      return view('monthwise-lot-status-report/index', ['schemes'=>$schemes]); 
   //    }
   //    else {
   //      return redirect("/")->with('success', 'User Disabled');
   //    }
  	// }

    // public function getLotDetails(){
    //   $scheme_id = $_POST['scheme_id'];
    //   $lot_month = $_POST['lot_month'];
    //   $lot_status = $_POST['lot_status'];
    //   $pay_mode = $_POST['pay_mode'];
    //   $lot_year = $_POST['lot_year'];

    //   if ($pay_mode == 'IFMS') {
    //     $table_name = 'ifms.transaction_lot';
    //   }
    //   else if ($pay_mode == 'SBI') {
    //     $table_name = 'sbi.transaction_lot';
    //   }
    //   $query = "select * from ".$table_name." where lot_month ='".$lot_month."' and lot_status=".$lot_status." and scheme_id=".$scheme_id." and lot_year='".$lot_year."'";
    //   $result = DB::connection('pgsql_mis')->select($query);

    //   return response(['data' => $result, 'pay_mode' => $pay_mode]); 
    // }

    public function getReponsePendingLotDetails(){
      $scheme_id = $_POST['scheme_id'];
      $lot_month = $_POST['lot_month'];
      $pay_mode = $_POST['pay_mode'];
      $lot_year = $_POST['lot_year'];

      if ($pay_mode == 'IFMS') {
        $query = "select * from lot_master where rbi_success_count is null and lot_month='".$lot_month."' and scheme_id=".$scheme_id." and ack_status != -1 and ref_no != -1 order by created_at asc";
      }
      else if ($pay_mode == 'SBI') {
        $query = "select lm.lot_no,tl.debit_reference,tl.created_at,lm.ben_count from sbi.transaction_lot tl 
          join lot_master lm on lm.lot_no= tl.lot_no where lm.lot_no in(
            select distinct l.lot_no from sbi.transaction_lot_details tld,lot_master l 
            where tld.status_code is null and l.lot_no=tld.lot_no and l.lot_month='".$lot_month."' and l.scheme_id=".$scheme_id.") order by lm.created_at asc";
      }
      
      $result = DB::connection('pgsql_mis')->select($query);

      return response(['data' => $result, 'pay_mode' => $pay_mode]);
    }

    public function getRepeatPendingLotDetails(){
      $scheme_id = $_POST['scheme_id'];
      $lot_month = $_POST['lot_month'];
      $pay_mode = $_POST['pay_mode'];
      $lot_year = $_POST['lot_year'];

      if ($pay_mode == 'IFMS') {
        $query = "select * from lot_master where lot_no in(
            select distinct l.lot_no from lot_master l,ifms.transaction_lot_details tld where l.repeat_drn_part is null and l.ref_no>0 and (l.repeat_lot=0 or l.repeat_lot is null) and 
            tld.wrongdata_flag=0 and tld.is_active>0 and l.rbi_success_count is not null and 
            l.lot_month='".$lot_month."' and l.scheme_id=".$scheme_id.") order by created_at asc";
      }
      else if ($pay_mode == 'SBI') {
        $query = "select lm.lot_no,tl.debit_reference,tl.created_at,lm.ben_count from sbi.transaction_lot tl 
          join lot_master lm on lm.lot_no= tl.lot_no where lm.lot_no in(
            select distinct l.lot_no from sbi.transaction_lot_details tld,lot_master l 
            where l.repeat_drn_part is null and tld.status_code='S00' and tld.is_active>0 
            and l.lot_no=tld.lot_no and l.lot_month='".$lot_month."' and l.scheme_id=".$scheme_id.") order by lm.created_at asc";
      }
      
      $result = DB::connection('pgsql_mis')->select($query);

      return response(['data' => $result, 'pay_mode' => $pay_mode]);
    }
}
