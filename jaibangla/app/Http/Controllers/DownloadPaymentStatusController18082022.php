<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Configduty;
use App\Scheme;
use App\Taluka;
use App\UrbanBody;
use Auth;
use Excel;

class DownloadPaymentStatusController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        set_time_limit(180);   
    }

    public function index(){
    	$user_id = AuthChecker::getUserId();
        $mapObj = Configduty::where('user_id',$user_id)->where('is_active',1)->get()->pluck('scheme_id');
        $scheme = Scheme::whereIn('id',$mapObj)->get();
        $is_urbanObj = Configduty::where('user_id',$user_id)->where('is_active',1)->first();
        if ($is_urbanObj->is_urban == 1) {
        	$block_ulb = $is_urbanObj->urban_body_code;
        }
        else{
        	$block_ulb = $is_urbanObj->taluka_code;
        }
    	return view('download-payment-status.index',['schemes' => $scheme, 'rural_urban' => $block_ulb, 'is_urban' => $is_urbanObj->is_urban]);
    }

    public function paymentStatusGenerateExcel(Request $request){
    	$this->validate($request, [
            'scheme' => 'required|not-in:0',
            'fin_year' => 'required|not-in:0',
            'month' => 'required|not-in:0'
        ]);

        $scheme = $request->scheme;
        $schemeObj = Scheme::where('id',$scheme)->first();
        $fin_year = $request->fin_year;
        $month = $request->month;
        $rural_urban = $request->rural_urban;
        $table_name = 'pension.beneficiary';
        if ($schemeObj->short_code != '') {
            $table_name = strtolower($schemeObj->short_code).'.beneficiary';
        }

        if ($request->is_urban == 2) {
        	$blockObj = Taluka::where('block_code',$rural_urban)->first();
            $block_ulb_name = $blockObj->block_name;
        }
        else{
        	$ulbObj = UrbanBody::where('sub_district_code',$rural_urban)->first();
            $block_ulb_name = $ulbObj->urban_body_name;
        }

        $ben_success = [];
        $ben_error = [];
        $query = "(select be.pension_id,be.name,lm.lot_month,lm.lot_year,be.acc_no as account_no,be.ifsc as ifsc,be.mobile_no,
            (case when lm.lot_status=0  then 'payment processed' else 'under process' end) process_status,
            (case when be.wrongdata_flag=0 then 'payment success' else 'payment error' end) payment_status,
            be.utr_no,(case when be.wrongdata_flag=0 then null else be.ifms_status end) error_reason 

            from ifms.transaction_lot_details be,lot_master lm
            where lm.lot_no=be.drn_part and lm.lot_year='".$fin_year."' and lm.lot_month='".$month."' and be.pension_id in 
            (select id from " . $table_name . " where created_by_local_body_code=".$rural_urban." and next_level_role_id in (0,-2,-99) and scheme_id=".$scheme.")
            order by be.pension_id,be.name,lm.lot_year,lm.lot_month)
            union
            (select tld.pension_id,tld.name,tl.lot_month,tl.lot_year,tld.account_credit as account_no,tld.ifsc_code_credit as ifsc,null as mobile_no,
            (case when tl.lot_status=6  then 'payment processed' else 'under process' end) process_status,
            (case when tld.status_code='S00' then 'payment success' else (case when tld.status_code is null then 'payment under process' else 'payment error' end)end) payment_status,
            credit_payment_reference as utr_no,(case when tld.status_code is not null and tld.status_code<>'S00' then tc.description end) as error_reason
            
            from sbi.transaction_lot_details tld,sbi.transaction_lot tl,sbi.credit_transaction_code tc
            where tl.lot_no=tld.lot_no and tl.lot_year='".$fin_year."' and tl.lot_month='".$month."' and tld.pension_id in 
            (select id from " . $table_name . " where created_by_local_body_code=".$rural_urban." and next_level_role_id=0 and scheme_id=".$scheme.") and tld.status_code=tc.code 
            order by tld.pension_id,tl.lot_year,tl.lot_month)
            union
            (select be.pension_id,be.name,lm.lot_month,lm.lot_year,be.acc_no as account_no,be.ifsc as ifsc,be.mobile_no,
            (case when lm.lot_status=0  then 'payment processed' else 'under process' end) process_status,
            (case when be.wrongdata_flag=0 then 'payment success' else 'payment error' end) payment_status,
            be.utr_no,(case when be.wrongdata_flag=0 then null else be.ifms_status end) error_reason 

            from ifms.transaction_lot_details_report be,lot_master lm
            where lm.lot_no=be.drn_part and lm.lot_year='".$fin_year."' and lm.lot_month='".$month."' and be.pension_id in 
            (select id from " . $table_name . " where created_by_local_body_code=".$rural_urban." and next_level_role_id in (0,-2,-99) and scheme_id=".$scheme.")
            order by be.pension_id,be.name,lm.lot_year,lm.lot_month)
            union
            (select tld.pension_id,tld.name,tl.lot_month,tl.lot_year,tld.account_credit as account_no,tld.ifsc_code_credit as ifsc,null as mobile_no,
            (case when tl.lot_status=6  then 'payment processed' else 'under process' end) process_status,
            (case when tld.status_code='S00' then 'payment success' else (case when tld.status_code is null then 'payment under process' else 'payment error' end)end) payment_status,
            credit_payment_reference as utr_no,(case when tld.status_code is not null and tld.status_code<>'S00' then tc.description end) as error_reason
            
            from sbi.transaction_lot_details_report tld,sbi.transaction_lot tl,sbi.credit_transaction_code tc
            where tl.lot_no=tld.lot_no and tl.lot_year='".$fin_year."' and tl.lot_month='".$month."' and tld.pension_id in 
            (select id from " . $table_name . " where created_by_local_body_code=".$rural_urban." and next_level_role_id=0 and scheme_id=".$scheme.") and tld.status_code=tc.code 
            order by tld.pension_id,tl.lot_year,tl.lot_month)";

        // print $query;die(); 
        $result = DB::select(DB::raw($query));
    	// $result = DB::connection('pgsql_mis')->select($query);
    	//print_r($result);

    	$ben_success[] = array('Pension Id', 'Name','Month','Year','Account No','IFSC Code','Mobile No','Process Status','Payment Status','UTR');
    	$ben_error[] = array('Pension Id', 'Name','Month','Year','Account No','IFSC Code','Mobile No','Process Status','Payment Status','Error Reason');
    	foreach ($result as $res) {
    		if ($res->payment_status == 'payment success') {
    			$ben_success[] = array(
	    			'Pension Id' => $res->pension_id,
	               	'Name'  => trim($res->name),
	               	'Month'  => $res->lot_month,
	               	'Year'   => $res->lot_year,
                    'Account No' => $res->account_no,
                    'IFSC Code' => $res->ifsc,
                    'Mobile No' => $res->mobile_no,
	               	'Process Status' => $res->process_status,
	               	'Payment Status' => $res->payment_status,
                    'UTR' => $res->utr_no
	              );
    		}
    		else{
    		  $ben_error[] = array(
    			'Pension Id' => $res->pension_id,
               	'Name'  => trim($res->name),
               	'Month'  => $res->lot_month,
               	'Year'   => $res->lot_year,
                'Account No' => $res->account_no,
                'IFSC Code' => $res->ifsc,
                'Mobile No' => $res->mobile_no,
               	'Process Status' => $res->process_status,
               	'Payment Status' => $res->payment_status,
                'Error Reason' => $res->error_reason
              );
            }  
    	}

    	Excel::create($block_ulb_name.' '.$schemeObj->scheme_name.' '.$month.' '.$fin_year.' Payment Status', function($excel) use ($ben_success,$ben_error){
          $excel->setTitle('Payment Status');
          $excel->sheet('Payment Success', function($sheet) use ($ben_success){
           $sheet->fromArray($ben_success, null, 'A1', false, false);
          });
          $excel->sheet('Payment Error', function($sheet) use ($ben_error){
           $sheet->fromArray($ben_error, null, 'A1', false, false);
          });
        })->download('xlsx');
		return redirect('download-payment-status')->with('success','Download Successfully');
    }
}
