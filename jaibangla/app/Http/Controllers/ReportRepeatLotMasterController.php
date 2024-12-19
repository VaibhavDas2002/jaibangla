<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;
use Auth;
use App\Configduty;
use App\lot_master;
use App\Scheme;
use App\Helpers\AuthChecker;

class ReportRepeatLotMasterController extends Controller
{
    public function __construct() 
    {
        //$this->middleware('auth');
        //$this->middleware('Admin');
        set_time_limit(300);
    }

    public function selectYearMonth(){
        $user_id = AuthChecker::getUserId();
        $schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->get();
        return view('report-repeat-lot-master/selectYearMonth',['schemes'=>$schemes]);
    }

    public function index(Request $request)
    {
		$scheme_id = $request->scheme_id;
		$lot_year = $request->lot_year;
		$lot_month = $request->lot_month;

		// $today = 'December'; // 2012-01-30
		// $next_month = date("F", strtotime("$today +1 month"));
		// print $next_month;
		
		// $scheme_id = 3;
		// $lot_year = '2020-2021';
		// $lot_month = 'April';

		$schemeObj = Scheme::where('id',$scheme_id)->first();
		
		$final_arr = DB::select(DB::raw("select *,(case when e=(a-b-c-d)  then 'OK' else (case when e=(parent_total-ifms_failed) then 'Parallel Lot' else 'Not OK' end) end) as rem from (
			select string_agg(concat((l.lot_no),' : ',(l.remarks)),'. ') as remark, string_agg(concat((l.lot_no),' : ',(l.remarks)),', ') as remark, string_agg(l.lot_no,', ') parent_lot_no, string_agg(distinct l.payment_mode,', ') as parent_payment_mode,
			sum(l.ifms_wrongdata_count) as ifms_failed,
			sum(l.ben_count) as parent_total, sum(l.rbi_success_count) as a, sum(l.rbi_failed_count) as err,sum(l.success_adjusted) as b,
			sum(l.success_duplicate) as c,sum(l.success_deactivated)as d,l.repeat_drn_part,m.lot_no as child_lot_no,
			m.ben_count e, m.payment_mode as child_payment, m.lot_month as child_lot_month	
			from lot_master l, lot_master m
			where l.repeat_drn_part=m.lot_no
			and l.lot_month='".$lot_month."' and l.lot_year='".$lot_year."' and l.scheme_id=".$scheme_id." and l.ref_no<>-1
			group by l.repeat_drn_part,m.lot_no,m.ben_count,m.payment_mode,m.lot_month) q"));

		
		//print_r($final_arr);die();
        return view('report-repeat-lot-master/result_lot_master_report',['results' => $final_arr, 'scheme_name' => $schemeObj->scheme_name, 'year' => $lot_year, 'month' => $lot_month]);
    }

    function showParentLotRemarks($child_lot_no){
    	$final_arr = DB::select(DB::raw("select *,(case when e=(a-b-c-d)  then 'OK' else (case when e=(parent_total-ifms_failed) then 'Parallel Lot' else 'Not OK' end) end) as rem from (
			select string_agg(concat((l.lot_no),' : ',(l.remarks)),'. ') as remark, string_agg(l.lot_no,', ') parent_lot_no, string_agg(distinct l.payment_mode,', ') as parent_payment_mode,
			sum(l.ifms_wrongdata_count) as ifms_failed,
			sum(l.ben_count) as parent_total, sum(l.rbi_success_count) as a, sum(l.rbi_failed_count) as err,sum(l.success_adjusted) as b,
			sum(l.success_duplicate) as c,sum(l.success_deactivated)as d,l.repeat_drn_part,m.lot_no as child_lot_no,
			m.ben_count e, m.payment_mode as child_payment, m.lot_month as child_lot_month	
			from lot_master l, lot_master m
			where l.repeat_drn_part=m.lot_no
			and l.repeat_drn_part='".$child_lot_no."' and l.ref_no<>-1
			group by l.repeat_drn_part,m.lot_no,m.ben_count,m.payment_mode,m.lot_month) q"));

    	return response()->json(['data'=>$final_arr]);

    }
}
