<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Configduty;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\lot_master;

use App\Scheme;
use App\Helpers\AuthChecker;

class ClubbedLotReportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(300);
  }
  public function selectYearMonth()
  {
    $consolitated = 0;
    $user_id = AuthChecker::getUserId();
    $schemes = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->get();
    return view('clubbed-lot-report/selectYearMonth', ['schemes' => $schemes, 'consolitated' => $consolitated]);
  }
  public function result(Request $request)
  {
    $consolitated = 0;
    $scheme_id = $request->scheme_id;
    $lot_year = $request->lot_year;
    $lot_month = $request->lot_month;

    // $today = 'December'; // 2012-01-30
    // $next_month = date("F", strtotime("$today +1 month"));
    // print $next_month;

    // $scheme_id = 3;
    // $lot_year = '2020-2021';
    // $lot_month = 'April';

    $schemeObj = Scheme::where('id', $scheme_id)->first();

    $final_arr = DB::select(DB::raw("select string_agg(distinct l.payment_mode,', ') as parent_payment_mode,
    string_agg(l.lot_no,', ') parent_lot_no,
    sum(l.ben_count) as parent_total,
    sum(l.ifms_wrongdata_count) as ifms_failed,
    sum(l.ben_count) as parent_total, 
    sum(l.rbi_success_count) as a, sum(l.rbi_failed_count) as err,sum(l.success_adjusted) 
    as b,
    sum(l.success_duplicate) as c,sum(l.success_deactivated)as d from lot_master l
    where lot_type_id=8 and l.lot_month='" . $lot_month . "' and l.lot_year='" . $lot_year . "' and 
    l.scheme_id=" . $scheme_id . " group by l.lot_no "));


    //print_r($final_arr);die();
    return view('clubbed-lot-report/result_lot_master_report', ['consolitated' => $consolitated, 'results' => $final_arr, 'scheme_name' => $schemeObj->scheme_name, 'year' => $lot_year, 'month' => $lot_month]);
  }
  public function index(Request $request)
  {
    $consolitated = 1;
    if (Auth::user()->designation_id == 'DDO' || Auth::user()->designation_id == 'HOD') {
      $user_id = AuthChecker::getUserId();
      $schemes = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->get();

      if (request()->ajax()) {
        $scheme = $request->scheme;
        $lot_year = $request->lot_year;
        $lot_month = $request->lot_month;
        $pay_mode = $request->pay_mode;
        $tlt_table = 'clubbed_transaction_lot_details';

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
              from ifms." . $tlt_table . " tld, lot_master l, m_scheme s where l.lot_type_id=8 and lot_no=tld.drn_part
              and l.lot_month='" . $lot_month . "' and l.scheme_id=" . $scheme . " and s.id=l.scheme_id
              group by s.scheme_name,l.lot_month
              order by s.scheme_name,l.lot_month";
        } else if ($pay_mode == 'SBI') {
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
              from sbi." . $tlt_table . " tld, lot_master l, m_scheme s where l.lot_type_id=8 and l.lot_no=tld.lot_no
              and l.lot_month='" . $lot_month . "' and tld.scheme_id=" . $scheme . " and s.id=l.scheme_id
              group by s.scheme_name,l.lot_month
              order by s.scheme_name,l.lot_month";
        }
        // $data = $data->get();  
        $data = DB::connection('pgsql_mis')->select($query);
        //print_r($query);die();          
        return datatables()->of($data)
          ->addColumn('scheme_name', function ($data) {
            return $data->scheme_name;
          })
          ->addColumn('lot_month', function ($data) {
            return $data->lot_month;
          })
          ->addColumn('total', function ($data) {
            return $data->total;
          })
          ->addColumn('response_pending', function ($data) {
            return $data->response_pending;
          })
          ->addColumn('get_lot_response_pending', function ($data) use ($scheme, $pay_mode, $lot_year) {
            $btn = '';
            if ($data->response_pending != 0) {
              $val = "'" . $scheme . '_' . $data->lot_month . '_' . $lot_year . '_' . $pay_mode . "'";
              $btn .= '<button class="btn btn-info btn-sm" id="get_response_pending" onclick="getResponsePendingLot(' . $val . ')">Get Lot</button>';
            } else {
              $btn = '<span class="label label-success">No Pending</span>';
            }
            return $btn;
          })
          ->addColumn('success', function ($data) {
            return $data->success;
          })
          ->addColumn('failed', function ($data) {
            return $data->failed;
          })
          ->addColumn('eligible_for_next_month', function ($data) {
            return $data->eligible_for_next_month;
          })
          ->addColumn('not_eligible_for_next_month', function ($data) {
            return $data->not_eligible_for_next_month;
          })
          ->addColumn('repeat_lot_done', function ($data) {
            return $data->repeat_lot_done;
          })
          ->addColumn('pending_for_repeat_lot', function ($data) {
            return $data->pending_for_repeat_lot;
          })
          ->addColumn('get_lot_pending_for_repeat_lot', function ($data) use ($scheme, $pay_mode, $lot_year) {
            $btn = '';
            if ($data->pending_for_repeat_lot != 0) {
              $val = "'" . $scheme . '_' . $data->lot_month . '_' . $lot_year . '_' . $pay_mode . "'";
              $btn .= '<button class="btn btn-warning btn-sm" id="get_repeat_pending" onclick="getRepeatPendingLot(' . $val . ')">Get Lot</button>';
            } else {
              $btn = '<span class="label label-success">No Pending</span>';
            }
            return $btn;
          })

          ->rawColumns(['scheme_name', 'lot_month', 'total', 'response_pending', 'get_lot_response_pending', 'success', 'failed', 'eligible_for_next_month', 'not_eligible_for_next_month', 'repeat_lot_done', 'pending_for_repeat_lot', 'get_lot_pending_for_repeat_lot'])
          ->make(true);
      }
      return view('clubbed-lot-report/consolidated_report', ['schemes' => $schemes, 'consolitated' => $consolitated]);
    } else {
      return redirect("/")->with('success', 'User Disabled');
    }
  }


  public function getReponsePendingLotDetails()
  {
    $scheme_id = $_POST['scheme_id'];
    $lot_month = $_POST['lot_month'];
    $pay_mode = $_POST['pay_mode'];
    $lot_year = $_POST['lot_year'];
    $tlt_table = 'clubbed_transaction_lot_details';

    if ($pay_mode == 'IFMS') {
      $query = "select * from lot_master where lot_type_id=8 and rbi_success_count is null and lot_month='" . $lot_month . "' and scheme_id=" . $scheme_id . " and ack_status != -1 and ref_no != -1 order by created_at asc";
    } else if ($pay_mode == 'SBI') {
      $query = "select lm.lot_no,tl.debit_reference,tl.created_at,lm.ben_count from sbi.clubbed_transaction_lot tl 
          join lot_master lm on lm.lot_no= tl.lot_no where lm.lot_type_id=8 and lm.lot_no in(
            select distinct l.lot_no from sbi." . $tlt_table . " tld,lot_master l 
            where l.lot_type_id=8 and tld.status_code is null and l.lot_no=tld.lot_no and l.lot_month='" . $lot_month . "' and l.scheme_id=" . $scheme_id . ") order by lm.created_at asc";
    }

    $result = DB::connection('pgsql_mis')->select($query);

    return response(['data' => $result, 'pay_mode' => $pay_mode]);
  }

  public function getRepeatPendingLotDetails()
  {
    $scheme_id = $_POST['scheme_id'];
    $lot_month = $_POST['lot_month'];
    $pay_mode = $_POST['pay_mode'];
    $lot_year = $_POST['lot_year'];
    $tlt_table = 'clubbed_transaction_lot_details';
    if ($pay_mode == 'IFMS') {
      $query = "select * from lot_master where lot_type_id=8 and lot_no in(
            select distinct l.lot_no from lot_master l,ifms.clubbed_transaction_lot tld where  l.lot_type_id=8 and l.repeat_drn_part is null and l.ref_no>0 and (l.repeat_lot=0 or l.repeat_lot is null) and 
            tld.wrongdata_flag=0 and tld.is_active>0 and l.rbi_success_count is not null and 
            l.lot_month='" . $lot_month . "' and l.scheme_id=" . $scheme_id . ") order by created_at asc";
    } else if ($pay_mode == 'SBI') {
      $query = "select lm.lot_no,tl.debit_reference,tl.created_at,lm.ben_count from sbi.clubbed_transaction_lot tl 
          join lot_master lm on lm.lot_no= tl.lot_no where lm.lot_type_id=8 lm.lot_no in(
            select distinct l.lot_no from sbi." . $tlt_table . " tld,lot_master l 
            where l.lot_type_id=8 and l.repeat_drn_part is null and tld.status_code='S00' and tld.is_active>0 
            and l.lot_no=tld.lot_no and l.lot_month='" . $lot_month . "' and l.scheme_id=" . $scheme_id . ") order by lm.created_at asc";
    }

    $result = DB::connection('pgsql_mis')->select($query);

    return response(['data' => $result, 'pay_mode' => $pay_mode]);
  }
  function showParentLotRemarks($child_lot_no)
  {
    $final_arr = DB::select(DB::raw("select *,(case when e=(a-b-c-d)  then 'OK' else (case when e=(parent_total-ifms_failed) then 'Parallel Lot' else 'Not OK' end) end) as rem from (
			select string_agg(concat((l.lot_no),' : ',(l.remarks)),'. ') as remark, string_agg(l.lot_no,', ') parent_lot_no, string_agg(distinct l.payment_mode,', ') as parent_payment_mode,
			sum(l.ifms_wrongdata_count) as ifms_failed,
			sum(l.ben_count) as parent_total, sum(l.rbi_success_count) as a, sum(l.rbi_failed_count) as err,sum(l.success_adjusted) as b,
			sum(l.success_duplicate) as c,sum(l.success_deactivated)as d,l.repeat_drn_part,m.lot_no as child_lot_no,
			m.ben_count e, m.payment_mode as child_payment, m.lot_month as child_lot_month	
			from lot_master l, lot_master m
			where l.lot_type_id=8 and l.repeat_drn_part=m.lot_no
			and l.repeat_drn_part='" . $child_lot_no . "' and l.ref_no<>-1
			group by l.repeat_drn_part,m.lot_no,m.ben_count,m.payment_mode,m.lot_month) q"));

    return response()->json(['data' => $final_arr]);
  }
}
