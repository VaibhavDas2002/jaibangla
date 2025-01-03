<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\Scheme;
use App\Helpers\AuthChecker;

class ReportLotMasterController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth');
      //  $this->middleware(['auth','MaintainMiddleware']);
        //$this->middleware('Admin');
    }

    public function selectYearMonth(){
	return redirect("/")->with('success', 'Payment Lot creation is temporarily suspended due to financial year end migration.');
        $user_id = AuthChecker::getUserId();
        $schemes=DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
      
        return view('report-lot-master/selectYearMonth',['schemes'=>$schemes]);
    }

    public function index(Request $request)
    {
		//$mobile = Auth::user()->mobile_no;
//new      $user_id = AuthChecker::getUserId();
//new      $schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->first();
//new		$scheme_id = $schemes->scheme_id;
		$scheme_id = $request->select_scheme;//new
		$lot_year = $request->lot_year;//new
		$lot_month = $request->lot_month;//new
        $lot_status = $request->lot_status;//new
        $sObj = Scheme::where('id',$scheme_id)->first();
		
        $query="select l.scheme_id,l.lot_no,l.lot_year,l.lot_month,l.lot_status,l.push_to_ifms_status,l.dotdone_status,l.ack_status,l.ref_no,
        l.wrongdata_status,l.repeat_lot,l.repeat_drn_part,l.voucher_no, l.ben_count, l.ifms_wrongdata_count, l.file_name,
        (l.ben_count-(case when l.ifms_wrongdata_count is null then 0 else l.ifms_wrongdata_count end)) pmt_mandate,
        l.rbi_failed_count, l.rbi_success_count, exists (select lot_no from ifms.temp_lot_master where lot_no=l.lot_no and rbi_sent_count=(rbi_receive_success_count+rbi_receive_failed_count)) rbi_flag 
        from lot_master l where scheme_id = ".$scheme_id." and lot_year = '".$lot_year."' and lot_month = '".$lot_month."' and payment_mode='IFMS'  ";
      
       if($lot_status=='0'){
        $query .=" and l.push_to_ifms_status is null ";
       }
       if($lot_status=='1'){
        $query .=" and l.push_to_ifms_status = 1  and l.dotdone_status  is null";
       }
       if($lot_status=='2'){
        $query .=" and l.push_to_ifms_status = 1 and l.dotdone_status = 1  ";
       }
        $query .= " order by l.lot_no DESC";
        $report = DB::select(DB::raw( $query));
        $status = 0;
        if (!empty($report)) {
            $status = 1;
        }
		//$rbi_status = DB::select(DB::raw("select lot_no from temp_lot_master where scheme_id IN (select id from m_scheme where ddo_code IN 
		//(select username from users where mobile_no='".$mobile."' and designation_id='DDO')) order by lot_no DESC"));
		
        return view('report-lot-master/report_lot_master', ['reports' => $report, 'scheme' => $sObj->scheme_name, 'year' => $lot_year, 'month' => $lot_month,'status'=>$status]);
    }

}
