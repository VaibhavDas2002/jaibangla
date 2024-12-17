<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\BeneficiaryPensions;
use App\District;
use App\Configduty;
use App\Scheme;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use App\DupliacteApproveReject;
use Auth;

class BeneficiaryPaymentStatusController extends Controller
{
    public function index(){
    	/*IF Auth not found then redirect to login page*/
        if (!isset(Auth::user()->email)) {
            return redirect('/login');
        }
        /**/
        $user_id = Auth::user()->id;
        $dutyObj = Configduty::where('user_id',$user_id)->first();
        $dist_code = $dutyObj->district_code;
        $is_urban = $dutyObj->is_urban;
        if ($is_urban == 2) {
            $block_ulb_code = $dutyObj->taluka_code;
        }
        else{
            $block_ulb_code = $dutyObj->urban_body_code;
        }

        $scheme = Scheme::all();
        return view('ben-payment-status/index_verifier',['schemes' => $scheme, 'dist_code' => $dist_code, 'is_urban' => $is_urban, 'block_ulb_code' => $block_ulb_code]);
    }
    
    public function searchByBenName(Request $request){
    	$this->validate($request, [
            'scheme_type' => 'required|not-in:0'
        ]);  

        $user_id = Auth::user()->id;
        $dutyObj = Configduty::where('user_id',$user_id)->first();
        $dist_code = $dutyObj->district_code;

        $first_name = strtoUpper(trim($request->ben_fname));
        $middle_name = strtoUpper(trim($request->ben_mname));
        $last_name = strtoUpper(trim($request->ben_lname));

        $ben_id = $request->ben_id;

        $scheme_id = $request->scheme_type;
        $rural_urban = $request->is_rural_urban;
        $block_ulb = $request->block_ulb;
        
        //echo $first_name,$middle_name,$last_name;
        /*$result =BeneficiaryPensions::where('dist_code',$dist_code)
        		->where('ben_fname' , 'ILIKE' , $first_name.'%')
				->where('ben_mname','ILIKE',$middle_name.'%')
				->where('ben_lname','ILIKE',$last_name.'%')
				->where('scheme_id',$scheme_id)
				->where('rural_urban_id',$rural_urban)
				->where('block_ulb_code', $block_ulb)->get();*/

        if ($request['ben_id'] != '') {
            $result = DB::select(DB::raw("select * from pension.beneficiary where dist_code = ".$dist_code." and id = ".$ben_id." and scheme_id = ".$scheme_id." and rural_urban_id = ".$rural_urban." and block_ulb_code = ".$block_ulb.""));
        }
        else {
            $result = DB::select(DB::raw("select * from pension.beneficiary where dist_code = ".$dist_code." and ben_fname ILIKE '".$first_name."%' and ben_mname ILIKE '".$middle_name."%' and ben_lname ILIKE '".$last_name."%' and scheme_id = ".$scheme_id." and rural_urban_id = ".$rural_urban." and block_ulb_code = ".$block_ulb.""));
        }
        if (!empty($result)) {
            return view('ben-payment-status/ben_search_details',['results' => $result]);
        }
        else{
            return redirect('ben-payment-status')->with('msg1','No record found! Please provide correct information.');
        }
		
    }
    public function viewStatus($id){
        //print Auth::user()->id;
        $pension_id_arr = [];
        $result = BeneficiaryPensions::where('id',$id)->first();

        if ($result->next_level_role_id == -2) {
            //$rows = DupliacteApproveReject::where('original_application_id',$id)->count();
            $dup_ids = DupliacteApproveReject::where('original_application_id',$id)->first();
            $dup = DupliacteApproveReject::where('original_application_id',$id)->get();
            //print_r($dup);die();
            // foreach ($dup_ids as $key=>$val) {
            //     $pension_arr[] = $val;
            // }
            // $approved_id = implode(',', $pension_arr);
            //print $rejected_id;
            return view('ben-payment-status/view_status_beneficiary',['result' => $result, 'reject_id' => $dup , 'approved' =>$dup_ids]);
        }
        if ($result->payment_count > 0){
            $ben_status = DB::select(DB::raw("select lm.lot_month,lm.lot_year,lm.lot_no,be.pension_id,be.ifsc as ifsc_code,be.acc_no as account_no,
                (case when lm.lot_status=0  then 'Payment processed' else 'Under process' end) process_status,
                (case when be.wrongdata_flag=0 then 'Payment success' else 'Payment error' end) payment_status from ben_export be,lot_master lm
                where lm.lot_no=be.drn_part and be.pension_id =".$id."
                union
                select tl.lot_month,tl.lot_year,tl.lot_no,tld.pension_id,tld.ifsc_code_credit as ifsc_code,tld.account_credit as account_no,
                (case when tl.lot_status=5  then 'Payment processed' else 'Under process' end) process_status,
                (case when tld.status_code='S00' then 'Payment success' else (case when tld.status_code is null then 'Payment under process' else 'Payment error' end)end) payment_status 
                from sbi.transaction_lot_details tld,sbi.transaction_lot tl
                where tl.lot_no=tld.lot_no and tld.pension_id =".$id));
        }

        //print_r($ben_status);
        // For Checking Duplicate approved ids from Table: duplicate_approve_reject
        $rows = DupliacteApproveReject::where('original_approve_application_id',$id)->count();
        //print $rows;
        if ($rows > 0) {
            $duplicate_ids = DupliacteApproveReject::select('original_application_id')->where('original_approve_application_id',$id)->get()->pluck('original_application_id');
            foreach ($duplicate_ids as $key=>$val) {
                $pension_id_arr[] = $val;
            }
            $dupliate_id = implode(',', $pension_id_arr);
            $adjust_date = DupliacteApproveReject::where('original_approve_application_id',$id)->first();

            $ben_status1 = DB::select(DB::raw("
                select lm.lot_month,lm.lot_year,lm.lot_no,be.pension_id,be.ifsc as ifsc_code,be.acc_no as account_no,
                (case when lm.lot_status=0  then 'Payment processed' else 'Under process' end) process_status,
                (case when be.wrongdata_flag=0 then 'Payment success' else 'Payment error' end) payment_status 
                from ben_export be,lot_master lm
                where lm.lot_no=be.drn_part and be.pension_id in(".$dupliate_id.")
                union
                select tl.lot_month,tl.lot_year,tl.lot_no,tld.pension_id,tld.ifsc_code_credit as ifsc_code,tld.account_credit as account_no,
                (case when tl.lot_status=5  then 'Payment processed' else 'Under process' end) process_status,
                (case when tld.status_code='S00' then 'Payment success' else (case when tld.status_code is null then 'Payment under process' else 'Payment error' end)end) payment_status 
                from sbi.transaction_lot_details tld,sbi.transaction_lot tl
                where tl.lot_no=tld.lot_no and tld.pension_id in(".$dupliate_id.")"));
            return view('ben-payment-status/view_status_beneficiary',['result' => $result, 'ben_status' => $ben_status, 'ben_status1' => $ben_status1, 'duplicate_ids' => $dupliate_id, 'adjust' => $adjust_date]);
        }
        if ($result->payment_count > 0) {
            return view('ben-payment-status/view_status_beneficiary',['result' => $result, 'ben_status' => $ben_status]);
        }
        else{
            return view('ben-payment-status/view_status_beneficiary',['result' => $result,]);
        }
    }

    public function statusError($lot_no, $pension_id){
        $results = DB::select(DB::raw("select status_code from sbi.transaction_lot_details where lot_no='".$lot_no."' and pension_id=".$pension_id));
        return response()->json($results);
    }
}
