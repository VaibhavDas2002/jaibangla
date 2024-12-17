<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ben_lot_month;
use App\lot_master;
use Auth;

class CheckLotDetailsDuplicateController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function checkLotDuplicate(Request $request)
    {
    	//$lot_number = str_pad($id,6,"0",STR_PAD_LEFT);
    	$lot_number = $request->lot_no;
		$ben_count = $request->ben_count;
        $scheme_id = $request->scheme_id;
    	$report = DB::select(DB::raw("select l.lot_no ,count(l.lot_no),l.lot_month,l.lot_status,l.ref_no from ifms.transaction_lot_details be,lot_master l where be.drn_part=l.lot_no and be.scheme_id=l.scheme_id and be.pension_id in(select be.pension_id from ifms.transaction_lot_details be, lot_master l where be.drn_part=l.lot_no and be.scheme_id=l.scheme_id and l.scheme_id=".$scheme_id." and l.lot_no='".$lot_number."') group by l.lot_no,l.lot_month,l.lot_status,l.ref_no"));
    	//print_r($report);
    	return view('check_duplicate_lot', ['reports' => $report, 'lot_no' => $lot_number, 'ben_count' => $ben_count]);
    }

    public function duplicateReject(Request $request){
    	if(isset($_POST['verify'])){
    		$lot_number = $request->lot_no;
    		$msg = $lot_number.' lot verified Successfully!!';
    		//return redirect()->back()->with('lot_status_msg' , $msg);
    		return redirect('lot-verification-selectYearMonth')->with('lot_status_msg',$msg);
    	}

    	if(isset($_POST['reject'])){
    		$lot_number = $request->lot_no;
    		$input = ['lot_status' => -1];
    		lot_master::where('lot_no', $lot_number)->update($input);

    		$ben_input = ['is_active' => -1];
//18nov    		ben_export::where('drn_part', $lot_number)->update($ben_input);
			DB::table('ifms.transaction_lot_details')->where('drn_part', $lot_number)->update($ben_input);

    		$msg = $lot_number.' lot Reject Successfully!!';
    		//return redirect()->back()->with('lot_status_msg' , $msg);
    		return redirect('lot-verification-selectYearMonth')->with('lot_status_msg',$msg);
    	}
    }
}
