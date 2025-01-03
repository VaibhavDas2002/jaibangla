<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\Helpers\AuthChecker;

class ReportLotMasterSbiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware(['auth','MaintainMiddleware']);
        //$this->middleware('Admin');
    }

    public function index()
    {
	return redirect("/")->with('success', 'Transaction Lot creation is temporarily suspended due to financial year end migration.');

        //$mobile = Auth::user()->mobile_no;
        $user_id = AuthChecker::getUserId();
        //$schemes = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
        //$scheme_id = $schemes->scheme_id;

        $report = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
        return view('report-lot-master-sbi/index', ['reports' => $report]);
        //echo 'successfully called';


    }

    public function lot_listing(Request $request)
    { //dd($request->all());
        /*$scheme_id = $request->select_scheme;//new
        $lot_year = $request->lot_year;//new
        $lot_month = $request->lot_month;//new
        $report = DB::select(DB::raw("select s.scheme_name, l.scheme_id,l.lot_no,l.lot_year,l.lot_month,l.credit_count,l.lot_status,l.success_count,l.failed_count,l.amount_debit,l.debit_reference 
		from sbi.transaction_lot l,m_scheme s where l.scheme_id = ".$scheme_id." and lot_year = '".$lot_year."' and lot_month = '".$lot_month."' and l.scheme_id=s.id order by l.lot_no DESC"));*/

        /* ----------------29-03-2021----------------- */
        $scheme_id = $request->select_scheme; //new
        $lot_year = $request->lot_year; //new
        $lot_month = $request->lot_month; //new
        $lot_status = $request->lot_status; //new
        $query = "select s.scheme_name, l.scheme_id,l.lot_no,l.lot_year,l.lot_month,l.credit_count,l.lot_status,l.success_count,l.failed_count,l.amount_debit,l.debit_reference, (select description from sbi.credit_transaction_code where code=l.debit_status_code)
        from sbi.transaction_lot l,m_scheme s,lot_master lm where l.scheme_id = " . $scheme_id . " and l.lot_year = '" . $lot_year . "' and l.lot_month = '" . $lot_month . "' and l.scheme_id=s.id and lm.lot_no=l.lot_no ";

        if (!empty($lot_status)) {
            $query .= " and l.lot_status=" . $lot_status;
        }
        $query .= " order by  
        CASE 
                WHEN l.lot_status = '0' THEN 0
                WHEN l.lot_status = '5' THEN 1
                WHEN l.lot_status = '1' THEN 2
               
                WHEN l.lot_status = '2' THEN 3
                WHEN l.lot_status = '3' THEN 4
                WHEN l.lot_status = '4' THEN 5
                ELSE 99 
            END  ASC ,l.lot_no desc ";
       // $query .= " order by lot_status,l.lot_no   asc";
        $report = DB::select(DB::raw($query));
        $status = 0;
        if (!empty($report)) {
            $status = 1;
        }
        //$rbi_status = DB::select(DB::raw("select lot_no from temp_lot_master where scheme_id IN (select id from m_scheme where ddo_code IN 
        //(select username from users where mobile_no='".$mobile."' and designation_id='DDO')) order by lot_no DESC"));

        return view('report-lot-master-sbi/lot_listing', ['reports' => $report,'status'=>$status]);
        //return view('report_lot_master_sbi_test');
    }
}
