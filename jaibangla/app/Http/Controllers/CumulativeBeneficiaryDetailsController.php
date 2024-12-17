<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scheme;
use DB;

class CumulativeBeneficiaryDetailsController extends Controller
{
    public function __construct(){
    	set_time_limit(300);
    }

    public function index(Request $request){
    	$scheme = Scheme::all();
    	return view('cumulative_beneficiary_details',['schemes' => $scheme]);
    }

    public function store(Request $request){
    	$cumulative_arr = [];
    	$cumulative_payment_arr = [];
    	$previous_cumulative_arr = [];
    	$all_scheme = Scheme::all();
    	$scheme = $request->scheme;
    	$schemeObj = Scheme::where('id',$scheme)->first();
    	$selected_month = $request->month;
    	if ($selected_month == 'April') {
    		$month = "'"."April"."','"."March"."'";
    	}
    	else{
    		$month = "'".$selected_month."'";
    	}

        if ($scheme == 1) {
            $db_schema = 'johar'; 
        }
        elseif ($scheme == 3) {
            $db_schema = 'bandhu';
        }
        else {
            $db_schema = 'manabik';
        }
    	//print $month;die();
    	$aa = DB::select(DB::raw("select count(id) as total_application from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and scheme_id=".$scheme));
    	$bb = DB::select(DB::raw("select count(id) as current_approved from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id=0 and scheme_id=".$scheme));
    	$cc = DB::select(DB::raw("select count(id) as total_rejection from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id=-1 and scheme_id=".$scheme));
    	$dd = DB::select(DB::raw("select count(id) as total_post_approval_rejection from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id<-1 and scheme_id=".$scheme));
    	$ee = DB::select(DB::raw("select count(id) as still_pending_approver_end  from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and is_verified=1 and is_approved=0 and is_rejected=0 and scheme_id=".$scheme));


    	$ff = DB::select(DB::raw("select count(id) as still_pending_verifier_end from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id is null and scheme_id=".$scheme));
    	$gg = DB::select(DB::raw("select count(id) as payment_done_for_approved_ben from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =0 and (lot_generated=1 or payment_count>0) and scheme_id=".$scheme));
    	$hh = DB::select(DB::raw("select count(id) as lot_not_generated_for_approved_ben from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =0 and lot_generated=0 and payment_count=0 and scheme_id=".$scheme));
    	$ii = DB::select(DB::raw("select count(id) as ifms_error_received_for_approved_ben from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =0 and (lot_generated=-1 ) and scheme_id=".$scheme));	


    	$jj = DB::select(DB::raw("select count(id) as rbi_error_received_for_approved_ben from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =0 and (lot_generated=-2 )and payment_count=0 and scheme_id=".$scheme));
    	$ll = DB::select(DB::raw("select count(id) as sbi_error_received_for_approved_ben from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =0 and (lot_generated=-3 )and payment_count=0 and scheme_id=".$scheme));
    	$mm = DB::select(DB::raw("select count(id) as duplicate_rejection_payment_done from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =-2 and (lot_generated=1 ) and scheme_id=".$scheme));
    	$nn = DB::select(DB::raw("select count(id) as duplicate_rejectionlot_not_generated from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =-2 and (lot_generated=0 ) and scheme_id=".$scheme));


    	$oo = DB::select(DB::raw("select count(id) as duplicate_rejectionlot_ifms_error_received from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =-2 and (lot_generated=-1 ) and scheme_id=".$scheme));
    	$pp = DB::select(DB::raw("select count(id) as duplicate_rejectionlot_rbi_error_received from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =-2 and (lot_generated=-2 ) and scheme_id=".$scheme)); 
    	$qq = DB::select(DB::raw("select count(id) as duplicate_rejectionlot_sbi_error_received from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =-2 and (lot_generated=-3 ) and scheme_id=".$scheme));
    	$rr = DB::select(DB::raw("select count(id) as deactivated_cases_payment_done from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =-99 and (lot_generated=1 ) and scheme_id=".$scheme));


    	$ss = DB::select(DB::raw("select count(id) as deactivated_cases_lot_not_generated from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =-99 and (lot_generated=0 ) and scheme_id=".$scheme));
    	$tt = DB::select(DB::raw("select count(id) as deactivated_cases_lot_ifms_error_received from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =-99 and (lot_generated=-1 ) and scheme_id=".$scheme));
    	$uu = DB::select(DB::raw("select count(id) as deactivated_cases_lot_rbi_error_received from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =-99 and (lot_generated=-2 ) and scheme_id=".$scheme));
    	$vv = DB::select(DB::raw("select count(id) as deactivated_cases_lot_sbi_error_received from ".$db_schema.".beneficiary where trim(to_char(created_at,'Month'))in (".$month.") and next_level_role_id =-99 and (lot_generated=-3 ) and scheme_id=".$scheme));

    	foreach ($aa as $k) { $a = $k->total_application; }
    	foreach ($bb as $k) { $b = $k->current_approved; }
    	foreach ($cc as $k) { $c = $k->total_rejection; }
    	foreach ($dd as $k) { $d = $k->total_post_approval_rejection; }
    	foreach ($ee as $k) { $e = $k->still_pending_approver_end; }

    	foreach ($ff as $k) { $f = $k->still_pending_verifier_end; }
    	foreach ($gg as $k) { $g = $k->payment_done_for_approved_ben; }
    	foreach ($hh as $k) { $h = $k->lot_not_generated_for_approved_ben; }
    	foreach ($ii as $k) { $i = $k->ifms_error_received_for_approved_ben; }

    	foreach ($jj as $k) { $j = $k->rbi_error_received_for_approved_ben; }
    	foreach ($ll as $k) { $l = $k->sbi_error_received_for_approved_ben; }
    	foreach ($mm as $k) { $m = $k->duplicate_rejection_payment_done; }
    	foreach ($nn as $k) { $n = $k->duplicate_rejectionlot_not_generated; }

    	foreach ($oo as $k) { $o = $k->duplicate_rejectionlot_ifms_error_received; }
    	foreach ($pp as $k) { $p = $k->duplicate_rejectionlot_rbi_error_received; }
    	foreach ($qq as $k) { $q = $k->duplicate_rejectionlot_sbi_error_received; }
    	foreach ($rr as $k) { $r = $k->deactivated_cases_payment_done; }

    	foreach ($ss as $k) { $s = $k->deactivated_cases_lot_not_generated; }
    	foreach ($tt as $k) { $t = $k->deactivated_cases_lot_ifms_error_received; }
    	foreach ($uu as $k) { $u = $k->deactivated_cases_lot_rbi_error_received; }
    	foreach ($vv as $k) { $v = $k->deactivated_cases_lot_sbi_error_received; }

    	$cumulative_arr = [
    		'total_application' => $a,
    		'current_approved' => $b,
    		'total_rejection' => $c,
    		'total_post_approval_rejection' => $d,
    		'still_pending_approver_end' => $e,
    		'still_pending_verifier_end' => $f,
    		'payment_done_for_approved_ben' => $g,
    		'lot_not_generated_for_approved_ben' => $h,
    		'ifms_error_received_for_approved_ben' => $i,
    		'rbi_error_received_for_approved_ben' => $j,
    		'sbi_error_received_for_approved_ben' => $l,
    		'duplicate_rejection_payment_done' => $m,
    		'duplicate_rejectionlot_not_generated' => $n,
			'duplicate_rejectionlot_ifms_error_received' => $o,
			'duplicate_rejectionlot_rbi_error_received' => $p,
			'duplicate_rejectionlot_sbi_error_received' => $q,
			'deactivated_cases_payment_done' => $r,
			'deactivated_cases_lot_not_generated' => $s, 
    		'deactivated_cases_lot_ifms_error_received' => $t,
    		'deactivated_cases_lot_rbi_error_received' => $u,
    		'deactivated_cases_lot_sbi_error_received' => $v,
    		'total_beneficiary_payment_done' => $g + $m + $r
    	];
    	 

    	//print_r($cumulative_arr);


    	// Cumulative Payment Details
    	$aaa = DB::select(DB::raw("select count(pension_id) as total_ifms_payment_generated from ifms.transaction_lot_details where drn_part in(select lot_no from lot_master where lot_month='".$selected_month."' and ref_no>0 and scheme_id=".$scheme.")"));
    	$bbb = DB::select(DB::raw("select count(distinct pension_id) as total_beneficiaries_affected_ifms from ifms.transaction_lot_details where drn_part in(select lot_no from lot_master where lot_month='".$selected_month."' and ref_no>0 and scheme_id=".$scheme.")"));
    	$ccc  = DB::select(DB::raw("select count(pension_id) as ifms_payment_failed from ifms.transaction_lot_details where wrongdata_flag=1 and ifms_ref_no=0 and drn_part in(select lot_no from lot_master where lot_month='".$selected_month."' and ref_no>0 and scheme_id=".$scheme.")"));


    	$ddd = DB::select(DB::raw("select count(pension_id) as payment_processed_but_ifms_failed from ifms.transaction_lot_details where wrongdata_flag=1 and ifms_ref_no>0 and drn_part in(select lot_no from lot_master where lot_month='".$selected_month."' and ref_no>0 and scheme_id=".$scheme.")"));
    	$eee = DB::select(DB::raw("select count(pension_id) as rbi_payment_failed from ifms.transaction_lot_details where wrongdata_flag=2 and ifms_ref_no>0 and drn_part in(select lot_no from lot_master where lot_month='".$selected_month."' and ref_no>0 and scheme_id=".$scheme.")"));
    	$fff = DB::select(DB::raw("select count(pension_id) as successful_credit_rbi_a from ifms.transaction_lot_details where wrongdata_flag=0 and ifms_ref_no>0 and drn_part in(select lot_no from lot_master where lot_month='".$selected_month."' and ref_no>0 and scheme_id=".$scheme.")"));


    	$ggg = DB::select(DB::raw("select count(pension_id) as total_sbi_payment_generated from sbi.transaction_lot_details where debit_reference in(select debit_reference from sbi.transaction_lot where lot_month='".$selected_month."' and scheme_id=".$scheme.")"));
    	$hhh = DB::select(DB::raw("select count(distinct pension_id) as total_beneficiaries_affected_sbi from sbi.transaction_lot_details where debit_reference in(select debit_reference from sbi.transaction_lot where lot_month='".$selected_month."' and scheme_id=".$scheme.")"));
    	$iii = DB::select(DB::raw("select count(pension_id) as sbi_payment_failed from sbi.transaction_lot_details where status_code like '%E%' and debit_reference in(select debit_reference from sbi.transaction_lot where lot_month='".$selected_month."' and scheme_id=".$scheme.")"));


    	$jjj = DB::select(DB::raw("select count(pension_id) as successful_credit_sbi_b from sbi.transaction_lot_details where status_code like '%S%' and debit_reference in(select debit_reference from sbi.transaction_lot where lot_month='".$selected_month."' and scheme_id=".$scheme.")"));
    	$kkk = DB::select(DB::raw("select count(pension_id) as pending from sbi.transaction_lot_details where status_code is null and debit_reference in(select debit_reference from sbi.transaction_lot where lot_month='".$selected_month."' and scheme_id=".$scheme.")"));
    	
    	foreach ($aaa as $key) { $a_cnt = $key->total_ifms_payment_generated; }
    	foreach ($bbb as $key) { $b_cnt = $key->total_beneficiaries_affected_ifms; }
    	foreach ($ccc as $key) { $c_cnt = $key->ifms_payment_failed; }
    	
    	foreach ($ddd as $key) { $d_cnt = $key->payment_processed_but_ifms_failed; }
    	foreach ($eee as $key) { $e_cnt = $key->rbi_payment_failed; }
    	foreach ($fff as $key) { $f_cnt = $key->successful_credit_rbi_a; }
    	
    	foreach ($ggg as $key) { $g_cnt = $key->total_sbi_payment_generated; }
    	foreach ($hhh as $key) { $h_cnt = $key->total_beneficiaries_affected_sbi; }
    	foreach ($iii as $key) { $i_cnt = $key->sbi_payment_failed; }
    	
    	foreach ($jjj as $key) { $j_cnt = $key->successful_credit_sbi_b; }
    	foreach ($kkk as $key) { $k_cnt = $key->pending; }

    	$cumulative_payment_arr = [
    		'total_ifms_payment_generated' => $a_cnt,
	    	'total_beneficiaries_affected_ifms' => $b_cnt,
	    	'ifms_payment_failed' => $c_cnt,
	    	'payment_processed_but_ifms_failed' => $d_cnt,
	    	'rbi_payment_failed' => $e_cnt,
	    	'successful_credit_rbi_a' => $f_cnt,
	    	'total_sbi_payment_generated' => $g_cnt,
	    	'total_beneficiaries_affected_sbi' => $h_cnt,
	    	'sbi_payment_failed' => $i_cnt,
	    	'successful_credit_sbi_b' => $j_cnt,
	    	'pending' => $k_cnt,
	    	'total_successful_credit' => $f_cnt + $j_cnt,
    	];

    	//print_r($cumulative_payment_arr);
    	//$ben_arr = $cumulative_arr;
    	//$pay_arr = $cumulative_payment_arr;
    	//$ben_arr = json_decode(json_encode($cumulative_arr));
    	//$pay_arr = json_decode(json_encode($cumulative_payment_arr));
    	//$pre_ben = json_encode($cumulative_arr);
    	//$ben_arr = json_decode($pre_ben);
    	//$pre_pay = json_encode($cumulative_payment_arr);
    	//$pay_arr = json_decode($pre_pay);
        
    	//die();
    	return view('cumulative_beneficiary_details',
    		[
    		'scheme_id' => $scheme,
    		'scheme_name' => $schemeObj->scheme_name,
    		'month' => $selected_month,
    		'schemes' => $all_scheme, 
    		'ben' => $cumulative_arr, 
    		'pay' => $cumulative_payment_arr,
    		'pre_ben' => $previous_cumulative_arr,
    		'total_application' => $a,
    		'current_approved' => $b,
    		'total_rejection' => $c,
    		'total_post_approval_rejection' => $d,
    		'still_pending_approver_end' => $e,
    		'still_pending_verifier_end' => $f,
    		'payment_done_for_approved_ben' => $g,
    		'lot_not_generated_for_approved_ben' => $h,
    		'ifms_error_received_for_approved_ben' => $i,
    		'rbi_error_received_for_approved_ben' => $j,
    		'sbi_error_received_for_approved_ben' => $l,
    		'duplicate_rejection_payment_done' => $m,
    		'duplicate_rejectionlot_not_generated' => $n,
			'duplicate_rejectionlot_ifms_error_received' => $o,
			'duplicate_rejectionlot_rbi_error_received' => $p,
			'duplicate_rejectionlot_sbi_error_received' => $q,
			'deactivated_cases_payment_done' => $r,
			'deactivated_cases_lot_not_generated' => $s, 
    		'deactivated_cases_lot_ifms_error_received' => $t,
    		'deactivated_cases_lot_rbi_error_received' => $u,
    		'deactivated_cases_lot_sbi_error_received' => $v,
    		'total_beneficiary_payment_done' => $g + $m + $r,
    		'total_ifms_payment_generated' => $a_cnt,
	    	'total_beneficiaries_affected_ifms' => $b_cnt,
	    	'ifms_payment_failed' => $c_cnt,
	    	'payment_processed_but_ifms_failed' => $d_cnt,
	    	'rbi_payment_failed' => $e_cnt,
	    	'successful_credit_rbi_a' => $f_cnt,
	    	'total_sbi_payment_generated' => $g_cnt,
	    	'total_beneficiaries_affected_sbi' => $h_cnt,
	    	'sbi_payment_failed' => $i_cnt,
	    	'successful_credit_sbi_b' => $j_cnt,
	    	'pending' => $k_cnt,
	    	'total_successful_credit' => $f_cnt + $j_cnt,
	    	// 'pre_total_application' => $pre_a,
	     //    'pre_current_approved' => $pre_b,
	     //    'pre_total_rejection' => $pre_c,
	     //    'pre_total_post_approval_rejection' => $pre_d,
	     //    'pre_still_pending_approver_end' => $pre_e,
	     //    'pre_still_pending_verifier_end' => $pre_f,
	     //    'pre_payment_done_for_approved_ben' => $pre_g,
	     //    'pre_lot_not_generated_for_approved_ben' => $pre_h,
	     //    'pre_ifms_error_received_for_approved_ben' => $pre_i,
	     //    'pre_rbi_error_received_for_approved_ben' => $pre_j,
	     //    'pre_sbi_error_received_for_approved_ben' => $pre_l,
	     //    'pre_duplicate_rejection_payment_done' => $pre_m,
	     //    'pre_duplicate_rejectionlot_not_generated' => $pre_n,
	     //    'pre_duplicate_rejectionlot_ifms_error_received' => $pre_o,
	     //    'pre_duplicate_rejectionlot_rbi_error_received' => $pre_p,
	     //    'pre_duplicate_rejectionlot_sbi_error_received' => $pre_q,
	     //    'pre_deactivated_cases_payment_done' => $pre_r,
	     //    'pre_deactivated_cases_lot_not_generated' => $pre_s, 
	     //    'pre_deactivated_cases_lot_ifms_error_received' => $pre_t,
	     //    'pre_deactivated_cases_lot_rbi_error_received' => $pre_u,
	     //    'pre_deactivated_cases_lot_sbi_error_received' => $pre_v,
	     //    'pre_total_beneficiary_payment_done' => $pre_g + $pre_m + $pre_r
    	]); 
    }
    public function perviousBenDetails($scheme_id, $month){
    	$selected_month = $month;
    	$scheme = $scheme_id;
        if ($scheme == 1) {
            $db_schema = 'johar'; 
        }
        elseif ($scheme == 3) {
            $db_schema = 'bandhu';
        }
        else {
            $db_schema = 'manabik';
        }
    	// Cumulative Beneficiary Details Previous Months
    	$pre_aa = DB::select(DB::raw("select count(id) as pre_total_application from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and scheme_id=".$scheme));

		$pre_bb = DB::select(DB::raw("select count(id) as pre_current_approved from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id=0 and scheme_id=".$scheme));

		$pre_cc = DB::select(DB::raw("select count(id) as pre_total_rejection from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id=-1 and scheme_id=".$scheme));

		$pre_dd = DB::select(DB::raw("select count(id) as pre_total_post_approval_rejection from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id<-1 and scheme_id=".$scheme));

		$pre_ee = DB::select(DB::raw("select count(id) as pre_still_pending_approver_end  from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and is_verified=1 and is_approved=0 and is_rejected=0 and scheme_id=".$scheme));



		$pre_ff = DB::select(DB::raw("select count(id) as pre_still_pending_verifier_end from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id is null and scheme_id=".$scheme));

		$pre_gg = DB::select(DB::raw("select count(id) as pre_payment_done_for_approved_ben from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =0 and (lot_generated=1 or payment_count>0) and scheme_id=".$scheme));

		$pre_hh = DB::select(DB::raw("select count(id) as pre_lot_not_generated_for_approved_ben from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =0 and lot_generated=0 and payment_count=0 and scheme_id=".$scheme));

		$pre_ii = DB::select(DB::raw("select count(id) as pre_ifms_error_received_for_approved_ben from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =0 and (lot_generated=-1 ) and scheme_id=".$scheme)); 



		$pre_jj = DB::select(DB::raw("select count(id) as pre_rbi_error_received_for_approved_ben from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =0 and (lot_generated=-2 )and payment_count=0 and scheme_id=".$scheme));

		$pre_ll = DB::select(DB::raw("select count(id) as pre_sbi_error_received_for_approved_ben from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =0 and (lot_generated=-3 )and payment_count=0 and scheme_id=".$scheme));

		$pre_mm = DB::select(DB::raw("select count(id) as pre_duplicate_rejection_payment_done from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =-2 and (lot_generated=1 ) and scheme_id=".$scheme));

		$pre_nn = DB::select(DB::raw("select count(id) as pre_duplicate_rejectionlot_not_generated from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =-2 and (lot_generated=0 ) and scheme_id=".$scheme));



		$pre_oo = DB::select(DB::raw("select count(id) as pre_duplicate_rejectionlot_ifms_error_received from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =-2 and (lot_generated=-1 ) and scheme_id=".$scheme));

		$pre_pp = DB::select(DB::raw("select count(id) as pre_duplicate_rejectionlot_rbi_error_received from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =-2 and (lot_generated=-2 ) and scheme_id=".$scheme)); 

		$pre_qq = DB::select(DB::raw("select count(id) as pre_duplicate_rejectionlot_sbi_error_received from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =-2 and (lot_generated=-3 ) and scheme_id=".$scheme));

		$pre_rr = DB::select(DB::raw("select count(id) as pre_deactivated_cases_payment_done from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =-99 and (lot_generated=1 ) and scheme_id=".$scheme));



		$pre_ss = DB::select(DB::raw("select count(id) as pre_deactivated_cases_lot_not_generated from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =-99 and (lot_generated=0 ) and scheme_id=".$scheme));

		$pre_tt = DB::select(DB::raw("select count(id) as pre_deactivated_cases_lot_ifms_error_received from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =-99 and (lot_generated=-1 ) and scheme_id=".$scheme));

		$pre_uu = DB::select(DB::raw("select count(id) as pre_deactivated_cases_lot_rbi_error_received from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =-99 and (lot_generated=-2 ) and scheme_id=".$scheme));

		$pre_vv = DB::select(DB::raw("select count(id) as pre_deactivated_cases_lot_sbi_error_received from ".$db_schema.".beneficiary where EXTRACT(MONTH FROM created_at)< EXTRACT(MONTH FROM to_date('".$selected_month."', 'Mon')) and next_level_role_id =-99 and (lot_generated=-3 ) and scheme_id=".$scheme));

		foreach ($pre_aa as $ky) { $pre_a = $ky->pre_total_application; }
		foreach ($pre_bb as $ky) { $pre_b = $ky->pre_current_approved; }
		foreach ($pre_cc as $ky) { $pre_c = $ky->pre_total_rejection; }
		foreach ($pre_dd as $ky) { $pre_d = $ky->pre_total_post_approval_rejection; }
		foreach ($pre_ee as $ky) { $pre_e = $ky->pre_still_pending_approver_end; }

		foreach ($pre_ff as $ky) { $pre_f = $ky->pre_still_pending_verifier_end; }
		foreach ($pre_gg as $ky) { $pre_g = $ky->pre_payment_done_for_approved_ben; }
		foreach ($pre_hh as $ky) { $pre_h = $ky->pre_lot_not_generated_for_approved_ben; }
		foreach ($pre_ii as $ky) { $pre_i = $ky->pre_ifms_error_received_for_approved_ben; }

		foreach ($pre_jj as $ky) { $pre_j = $ky->pre_rbi_error_received_for_approved_ben; }
		foreach ($pre_ll as $ky) { $pre_l = $ky->pre_sbi_error_received_for_approved_ben; }
		foreach ($pre_mm as $ky) { $pre_m = $ky->pre_duplicate_rejection_payment_done; }
		foreach ($pre_nn as $ky) { $pre_n = $ky->pre_duplicate_rejectionlot_not_generated; }

		foreach ($pre_oo as $ky) { $pre_o = $ky->pre_duplicate_rejectionlot_ifms_error_received; }
		foreach ($pre_pp as $ky) { $pre_p = $ky->pre_duplicate_rejectionlot_rbi_error_received; }
		foreach ($pre_qq as $ky) { $pre_q = $ky->pre_duplicate_rejectionlot_sbi_error_received; }
		foreach ($pre_rr as $ky) { $pre_r = $ky->pre_deactivated_cases_payment_done; }

		foreach ($pre_ss as $ky) { $pre_s = $ky->pre_deactivated_cases_lot_not_generated; }
		foreach ($pre_tt as $ky) { $pre_t = $ky->pre_deactivated_cases_lot_ifms_error_received; }
		foreach ($pre_uu as $ky) { $pre_u = $ky->pre_deactivated_cases_lot_rbi_error_received; }
		foreach ($pre_vv as $ky) { $pre_v = $ky->pre_deactivated_cases_lot_sbi_error_received; }

		$previous_cumulative_arr = [
	        'pre_total_application' => $pre_a,
	        'pre_current_approved' => $pre_b,
	        'pre_total_rejection' => $pre_c,
	        'pre_total_post_approval_rejection' => $pre_d,
	        'pre_still_pending_approver_end' => $pre_e,
	        'pre_still_pending_verifier_end' => $pre_f,
	        'pre_payment_done_for_approved_ben' => $pre_g,
	        'pre_lot_not_generated_for_approved_ben' => $pre_h,
	        'pre_ifms_error_received_for_approved_ben' => $pre_i,
	        'pre_rbi_error_received_for_approved_ben' => $pre_j,
	        'pre_sbi_error_received_for_approved_ben' => $pre_l,
	        'pre_duplicate_rejection_payment_done' => $pre_m,
	        'pre_duplicate_rejectionlot_not_generated' => $pre_n,
	        'pre_duplicate_rejectionlot_ifms_error_received' => $pre_o,
	        'pre_duplicate_rejectionlot_rbi_error_received' => $pre_p,
	        'pre_duplicate_rejectionlot_sbi_error_received' => $pre_q,
	        'pre_deactivated_cases_payment_done' => $pre_r,
	        'pre_deactivated_cases_lot_not_generated' => $pre_s, 
	        'pre_deactivated_cases_lot_ifms_error_received' => $pre_t,
	        'pre_deactivated_cases_lot_rbi_error_received' => $pre_u,
	        'pre_deactivated_cases_lot_sbi_error_received' => $pre_v,
	        'pre_total_beneficiary_payment_done' => $pre_g + $pre_m + $pre_r
	    ];

	    return response()->json(['pre_total_application' => $pre_a,
	        'pre_current_approved' => $pre_b,
	        'pre_total_rejection' => $pre_c,
	        'pre_total_post_approval_rejection' => $pre_d,
	        'pre_still_pending_approver_end' => $pre_e,
	        'pre_still_pending_verifier_end' => $pre_f,
	        'pre_payment_done_for_approved_ben' => $pre_g,
	        'pre_lot_not_generated_for_approved_ben' => $pre_h,
	        'pre_ifms_error_received_for_approved_ben' => $pre_i,
	        'pre_rbi_error_received_for_approved_ben' => $pre_j,
	        'pre_sbi_error_received_for_approved_ben' => $pre_l,
	        'pre_duplicate_rejection_payment_done' => $pre_m,
	        'pre_duplicate_rejectionlot_not_generated' => $pre_n,
	        'pre_duplicate_rejectionlot_ifms_error_received' => $pre_o,
	        'pre_duplicate_rejectionlot_rbi_error_received' => $pre_p,
	        'pre_duplicate_rejectionlot_sbi_error_received' => $pre_q,
	        'pre_deactivated_cases_payment_done' => $pre_r,
	        'pre_deactivated_cases_lot_not_generated' => $pre_s, 
	        'pre_deactivated_cases_lot_ifms_error_received' => $pre_t,
	        'pre_deactivated_cases_lot_rbi_error_received' => $pre_u,
	        'pre_deactivated_cases_lot_sbi_error_received' => $pre_v,
	        'pre_total_beneficiary_payment_done' => $pre_g + $pre_m + $pre_r]);
    }

}
 
/*

 	

 	


*/