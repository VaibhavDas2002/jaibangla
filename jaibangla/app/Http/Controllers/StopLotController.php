<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;
use Auth;
use App\Configduty;
use App\lot_master;
use App\Scheme;

class StopLotController extends Controller
{
    public function index(Request $request)
    {
    	return view('stop-lot/index');
    }

    public function showStopLotDetails($lot_no){
    	$result = DB::select(DB::raw("select *,(select scheme_name from m_scheme s where s.id=l.scheme_id ),(select lot_type from lot_type_master m where m.id=l.lot_type_id),case when remarks is null then '' else remarks end as rem, 
			case when rbi_success_count is null then '0' else rbi_success_count end as success,
			case when rbi_failed_count is null then '0' else rbi_failed_count end as err 
			from lot_master l where lot_no='".$lot_no."'"));
    	return response()->json(['data'=>$result]);
    }

    // External function for get all pension ids
    public function getAllPensionIds($result)
    {
    	$arr = [];
    	foreach ($result as $key) {
    		$arr[] = $key->pension_id;
    	}
    	$pension_str = implode(',', $arr);
    	return $pension_str;
    }

    public function store(Request $request)
    {
    	/*
    	id  lot_type
    	--	--------
    	1	Fresh Lot
		4	IFMS Error
		5	RBI Error
		6	SBI Errorr
		3	Adjustment Lot
		2	Repeat Lot
		*/
    	$payment_mode = $request->pay_mode;
    	$lot_no = $request->lot_number;
    	$remarks = $request->remarks;
    	$lot_type = $request->lot_type;
    	if ($lot_no == '' or $payment_mode == '' or $lot_type == '') {
    		$msg1 = 'Something wrong in this Lot- '.$lot_no.'. Lot type or Payment mode not defined.';
    		return redirect('stop-lot')->with('success1', $msg1);
    	}
    	$date = date('d/m/Y');

    	// Payment Mode : SBI
    	if ($payment_mode == 'IFMS') {
    		// Fresh Lot
    		if ($lot_type == 1) {
    			DB::statement("UPDATE lot_master SET ref_no = -1, remarks = '".$remarks."'' WHERE lot_no = '".$lot_no."';");
    			// DB::statement("UPDATE ben_export SET ifms_ref_no = -1 WHERE drn_part = '".$lot_no."';");
				// $v_pension_id = DB::statement("UPDATE ifms.transaction_lot_details SET ifms_ref_no = -1 WHERE drn_part = '".$lot_no."' returning pension_id;");
				DB::statement("DELETE FROM ben_export WHERE drn_part = '".$lot_no."';");
				$v_pension_id = DB::statement("DELETE FROM ifms.transaction_lot_details WHERE drn_part = '".$lot_no."' returning pension_id;");
				DB::statement("UPDATE ifms.transaction_lot SET lot_status = -1 WHERE lot_no = '".$lot_no."';");
				$pension_ids = $this->getAllPensionIds($v_pension_id);
				DB::statement("UPDATE pension.beneficiary SET lot_generated = 0 WHERE id IN (".$pension_ids.");");
    		}
    		// Repeat Lot
    		elseif ($lot_type == 2) {
    			$parent_lot = DB::select("UPDATE lot_master SET repeat_lot=0, repeat_drn_part = null WHERE repeat_drn_part = '".$lot_no."' returning lot_no,payment_mode,scheme_id;"); /*modified*/
				DB::statement("UPDATE lot_master SET remarks = '".$remarks.". All parent lot released. Date: ".$date."', ref_no = -1 WHERE lot_no = '".$lot_no."';"); /*modified*/

				// DB::statement("UPDATE ben_export SET ifms_ref_no = -1 WHERE drn_part = '".$lot_no."';"); 
				// DB::statement("UPDATE ifms.transaction_lot_details SET ifms_ref_no = -1 WHERE drn_part = '".$lot_no."';");/*added*/
				DB::statement("DELETE FROM ben_export WHERE drn_part = '".$lot_no."';"); 
				DB::statement("DELETE FROM ifms.transaction_lot_details WHERE drn_part = '".$lot_no."';");
				DB::statement("UPDATE ifms.transaction_lot SET lot_status = -1 WHERE lot_no = '".$lot_no."';");
				$child_scheme_id = lot_master::where('lot_no',$lot_no)->first();
				foreach ($parent_lot as $p_lot) {
					if ($p_lot->payment_mode == 'IFMS' ) {
						DB::statement("update pension.beneficiary b set bank_edited=1 from
						(select tld.pension_id from ifms.transaction_lot_details tld,
						(select acc_no,ifsc,mobile_no,pension_id from ifms.transaction_lot_details where drn_part='".$lot_no."' and scheme_id=".$child_scheme_id->scheme_id.") t
						where (tld.acc_no<>t.acc_no or tld.ifsc<>t.ifsc or tld.mobile_no<>t.mobile_no) and tld.pension_id=t.pension_id and tld.drn_part='".$p_lot->lot_no."' and scheme_id=".$p_lot->scheme_id.")t2
						where b.id=t2.pension_id");
					}
					elseif ($p_lot->payment_mode == 'SBI') {
						DB::statement("update pension.beneficiary b set bank_edited=1 from
						(select tld.pension_id from sbi.transaction_lot_details tld,
						(select pension_id,account_credit,ifsc_code_credit from sbi.transaction_lot_details where lot_no='".$lot_no."' and scheme_id=".$child_scheme_id->scheme_id.") t
						where (tld.account_credit<>t.account_credit or tld.ifsc_code_credit<>t.ifsc_code_credit) and tld.pension_id=t.pension_id and tld.lot_no='".$p_lot->lot_no."' and scheme_id=".$p_lot->scheme_id.")t2
						where b.id=t2.pension_id");
					}
				}
    		}
    		// Adjustment Lot
    		elseif ($lot_type == 3) {
    			DB::statement("UPDATE lot_master SET ref_no = -1 WHERE lot_no = '".$lot_no."';");
				// DB::statement("UPDATE ben_export SET ifms_ref_no = -1 WHERE drn_part = '".$lot_no."';"); 
				// $v_pension_id = DB::select("UPDATE ifms.transaction_lot_details SET ifms_ref_no = -1 WHERE drn_part = '".$lot_no."' returning pension_id;");
				DB::statement("DELETE FROM ben_export WHERE drn_part = '".$lot_no."';"); 
				$v_pension_id = DB::select("DELETE FROM ifms.transaction_lot_details WHERE drn_part = '".$lot_no."' returning pension_id;");/*added*/
				DB::statement("UPDATE ifms.transaction_lot SET lot_status = -1 WHERE lot_no = '".$lot_no."';");
				$pension_ids = $this->getAllPensionIds($v_pension_id);
				DB::statement("UPDATE pension.beneficiary SET lot_generated = 0 WHERE id IN (".$pension_ids.");");
    		}
    		// IFMS Error
    		elseif ($lot_type == 4) {
    			DB::statement("UPDATE lot_master SET ref_no = -1 WHERE lot_no = '".$lot_no."';");
				// DB::statement("UPDATE ben_export SET ifms_ref_no = -1 WHERE drn_part = '".$lot_no."';"); 
				// $v_pension_id = DB::select("UPDATE ifms.transaction_lot_details SET ifms_ref_no = -1 WHERE drn_part = '".$lot_no."' returning pension_id;");
				DB::statement("DELETE FROM ben_export WHERE drn_part = '".$lot_no."';"); 
				$v_pension_id = DB::select("DELETE FROM ifms.transaction_lot_details WHERE drn_part = '".$lot_no."' returning pension_id;");/*added*/
				DB::statement("UPDATE ifms.transaction_lot SET lot_status = -1 WHERE lot_no = '".$lot_no."';");
				$pension_ids = $this->getAllPensionIds($v_pension_id);
				DB::statement("UPDATE pension.beneficiary SET lot_generated = -1, bank_edited = 1 WHERE id IN (".$pension_ids.");");
    		}
    		// RBI Error
    		elseif ($lot_type == 5) {
    			DB::statement("UPDATE lot_master SET ref_no = -1 WHERE lot_no = '".$lot_no."';");
				// DB::statement("UPDATE ben_export SET ifms_ref_no = -1 WHERE drn_part = '".$lot_no."';"); 
				// $v_pension_id = DB::select("UPDATE ifms.transaction_lot_details SET ifms_ref_no = -1 WHERE drn_part = '".$lot_no."' returning pension_id;");
				DB::statement("DELETE FROM ben_export WHERE drn_part = '".$lot_no."';"); 
				$v_pension_id = DB::select("DELETE FROM ifms.transaction_lot_details WHERE drn_part = '".$lot_no."' returning pension_id;");/*added*/
				DB::statement("UPDATE ifms.transaction_lot SET lot_status = -1 WHERE lot_no = '".$lot_no."';");
				$pension_ids = $this->getAllPensionIds($v_pension_id);
				DB::statement("UPDATE pension.beneficiary SET lot_generated = -2, bank_edited = 1 WHERE id IN (".$pension_ids.");");
    		}
    		// SBI Error
    		elseif ($lot_type == 6) {
    			DB::statement("UPDATE lot_master SET ref_no = -1 WHERE lot_no = '".$lot_no."';");
				// DB::statement("UPDATE ben_export SET ifms_ref_no = -1 WHERE drn_part = '".$lot_no."';"); 
				// $v_pension_id = DB::select("UPDATE ifms.transaction_lot_details SET ifms_ref_no = -1 WHERE drn_part = '".$lot_no."' returning pension_id;");
				DB::statement("DELETE FROM ben_export WHERE drn_part = '".$lot_no."';"); 
				$v_pension_id = DB::select("DELETE FROM ifms.transaction_lot_details WHERE drn_part = '".$lot_no."' returning pension_id;");/*added*/
				DB::statement("UPDATE ifms.transaction_lot SET lot_status = -1 WHERE lot_no = '".$lot_no."';");
				$pension_ids = $this->getAllPensionIds($v_pension_id);
				DB::statement("UPDATE pension.beneficiary SET lot_generated = -3, bank_edited = 1 WHERE id IN (".$pension_ids.");");
    		}
    	}
    	// Payment Mode : SBI
    	elseif ($payment_mode == 'SBI') {
    		// Fresh Lot
    		if ($lot_type == 1) {
    			DB::statement("UPDATE lot_master SET ref_no = -1 WHERE lot_no = '".$lot_no."';");
				// $v_pension_id = DB::select("UPDATE sbi.transaction_lot_details SET status_code = -1 WHERE lot_no = '".$lot_no."' returning pension_id;"); /*modified*/
				$v_pension_id = DB::select("DELETE FROM sbi.transaction_lot_details lot_no = '".$lot_no."' returning pension_id;"); /*modified*/
				DB::statement("UPDATE sbi.transaction_lot SET lot_status = -1 WHERE lot_no = '".$lot_no."';");
				$pension_ids = $this->getAllPensionIds($v_pension_id);
				DB::statement("UPDATE pension.beneficiary SET lot_generated = 0 WHERE id IN (".$pension_ids.");");
    		}
    		// Repeat Lot
    		elseif ($lot_type == 2) {
    			$parent_lot = DB::select("UPDATE lot_master SET repeat_lot=0, repeat_drn_part = null WHERE repeat_drn_part = '".$lot_no."' returning lot_no,payment_mode,scheme_id;"); /*modified*/
				DB::statement("UPDATE lot_master SET remarks = '".$remarks.". All parent lot released. Date: ".$date."', ref_no = -1 WHERE lot_no = '".$lot_no."';"); /*modified*/
				// DB::statement("UPDATE sbi.transaction_lot_details SET status_code = -1 WHERE lot_no = '".$lot_no."';");
				DB::statement("DELETE FROM sbi.transaction_lot_details WHERE lot_no = '".$lot_no."';");
				DB::statement("UPDATE sbi.transaction_lot SET lot_status = -1 WHERE lot_no = '".$lot_no."';");
				$child_scheme_id = lot_master::where('lot_no',$lot_no)->first();
				foreach ($parent_lot as $p_lot) {
					if ($p_lot->payment_mode == 'IFMS' ) {
						DB::statement("update pension.beneficiary b set bank_edited=1 from
						(select tld.pension_id from ifms.transaction_lot_details tld,
						(select acc_no,ifsc,mobile_no,pension_id from ifms.transaction_lot_details where drn_part='".$lot_no."' and scheme_id=".$child_scheme_id->scheme_id.") t
						where (tld.acc_no<>t.acc_no or tld.ifsc<>t.ifsc or tld.mobile_no<>t.mobile_no) and tld.pension_id=t.pension_id and tld.drn_part='".$p_lot->lot_no."' and scheme_id=".$p_lot->scheme_id.")t2
						where b.id=t2.pension_id");
					}
					elseif ($p_lot->payment_mode == 'SBI') {
						DB::statement("update pension.beneficiary b set bank_edited=1 from
						(select tld.pension_id from sbi.transaction_lot_details tld,
						(select pension_id,account_credit,ifsc_code_credit from sbi.transaction_lot_details where lot_no='".$lot_no."' and scheme_id=".$child_scheme_id->scheme_id.") t
						where (tld.account_credit<>t.account_credit or tld.ifsc_code_credit<>t.ifsc_code_credit) and tld.pension_id=t.pension_id and tld.lot_no='".$p_lot->lot_no."' and scheme_id=".$p_lot->scheme_id.")t2
						where b.id=t2.pension_id");
					}
				}
    		}
    		// Adjustment Lot
    		elseif ($lot_type == 3) {
    			DB::statement("UPDATE lot_master SET ref_no = -1 WHERE lot_no = '".$lot_no."';");
    			// $v_pension_id = DB::select("UPDATE sbi.transaction_lot_details SET status_code = -1 WHERE lot_no = '".$lot_no."' returning pension_id;"); 
    			$v_pension_id = DB::select("DELETE FROM sbi.transaction_lot_details WHERE lot_no = '".$lot_no."' returning pension_id;"); /*modified*/
				DB::statement("UPDATE sbi.transaction_lot SET lot_status = -1 WHERE lot_no = '".$lot_no."';");
				$pension_ids = $this->getAllPensionIds($v_pension_id);
				DB::statement("UPDATE pension.beneficiary SET lot_generated = 0 WHERE id IN (".$pension_ids.");");
    		}
    		// IFMS Error Lot
    		elseif ($lot_type == 4) {
    			DB::statement("UPDATE lot_master SET ref_no = -1 WHERE lot_no = '".$lot_no."';");
    			// $v_pension_id = DB::select("UPDATE sbi.transaction_lot_details SET status_code = -1 WHERE lot_no = '".$lot_no."' returning pension_id;");
    			$v_pension_id = DB::select("DELETE FROM sbi.transaction_lot_details WHERE lot_no = '".$lot_no."' returning pension_id;"); /*modified*/
				DB::statement("UPDATE sbi.transaction_lot SET lot_status = -1 WHERE lot_no = '".$lot_no."';");
				$pension_ids = $this->getAllPensionIds($v_pension_id);
				DB::statement("UPDATE pension.beneficiary SET lot_generated = -1, bank_edited = 1 WHERE id IN (".$pension_ids.");");
    		}
    		// RBI Error Lot
    		elseif ($lot_type == 5) {
    			DB::statement("UPDATE lot_master SET ref_no = -1 WHERE lot_no = '".$lot_no."';");
    			// $v_pension_id = DB::select("UPDATE sbi.transaction_lot_details SET status_code = -1 WHERE lot_no = '".$lot_no."' returning pension_id;");
    			$v_pension_id = DB::select("DELETE FROM sbi.transaction_lot_details WHERE lot_no = '".$lot_no."' returning pension_id;"); /*modified*/
				DB::statement("UPDATE sbi.transaction_lot SET lot_status = -1 WHERE lot_no = '".$lot_no."';");
				$pension_ids = $this->getAllPensionIds($v_pension_id);
				DB::statement("UPDATE pension.beneficiary SET lot_generated = -2, bank_edited = 1 WHERE id IN (".$pension_ids.");");
    		}
    		// SBI Error Lot
    		elseif ($lot_type == 6) {
    			DB::statement("UPDATE lot_master SET ref_no = -1 WHERE lot_no = '".$lot_no."';");
    			// $v_pension_id = DB::select("UPDATE sbi.transaction_lot_details SET status_code = -1 WHERE lot_no = '".$lot_no."' returning pension_id;");
    			$v_pension_id = DB::select("DELETE FROM sbi.transaction_lot_details WHERE lot_no = '".$lot_no."' returning pension_id;"); /*modified*/
				DB::statement("UPDATE sbi.transaction_lot SET lot_status = -1 WHERE lot_no = '".$lot_no."';");
				$pension_ids = $this->getAllPensionIds($v_pension_id);
				DB::statement("UPDATE pension.beneficiary SET lot_generated = -3, bank_edited = 1 WHERE id IN (".$pension_ids.");");
    		}
    	}

    	$msg = 'Lot '.$lot_no.' Stopped Successfully.';
    	return redirect('stop-lot')->with('success',$msg);
    }

}
