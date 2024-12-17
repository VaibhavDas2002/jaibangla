<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;
use App\PaymentTransaction;
use App\applicationModel;
use App\Http\Controllers\SmsSendController;

use App\PicUpload;
use App\Policestation;
use App\Country;
use App\State;


class BillDeskController extends Controller
{
    
    /*private $MerchantID = "XBSWBBNPCC";
    private $TxnAmount = 2.00;
    private $CurrencyType = 'INR';
    private $SecurityID = 'xbswbbnpcc';
    private $RU = "http://pccbidhannagarpolice.nic.in/response_payment";
    private $CHECKSUM_KEY = "sy9FebdkQ7pd";
    private $URL = "https://pgi.billdesk.com/pgidsk/PGIMerchantPayment";
        */


    public function MakePayment(Request $request,$id) {

        $application = applicationModel::where('application_id','=',$id)->first();
        $application_images = PicUpload::where('pcc_appliction_id','=',$id)->orderBy('created_at', 'desc')->first();
        /*echo "<pre>";
        print_r($application_images);
        echo "</pre>";
        die();*/
        $present_state = State::where('id','=',$application->present_state)->first();
        $permanent_state = State::where('id','=',$application->permanent_state)->first();
        $permanent_country = Country::where('id','=',$application->permanent_country)->first();

       

     $MerchantID = "XBSWBBNPCC";
     $TxnAmount = 300.00;
     $CurrencyType = 'INR';
     $SecurityID = 'xbswbbnpcc';
     $RU = "http://pccbidhannagarpolice.nic.in/response_payment";
     $CHECKSUM_KEY = "sy9FebdkQ7pd";
     $URL = "https://pgi.billdesk.com/pgidsk/PGIMerchantPayment";

    	$mobile = $request->session()->get('session_mobile');
    	$CustomerID = date('ymd').rand(1000, 9999);

    	//MerchantID|CustomerID|NA|TxnAmount|NA|NA|NA|CurrencyType|NA|TypeField1|SecurityID|NA|NA|TypeField2|NA|NA|NA|NA|NA|NA|NA|RU|Checksum        

        $str = $MerchantID.'|'.$CustomerID.'|NA|'.$TxnAmount.'|NA|NA|NA|'.$CurrencyType.'|NA|R|'.$SecurityID.'|NA|NA|F|'.$mobile.'|test@test.com|NA|NA|NA|NA|NA|'.$RU;

		$checksum = hash_hmac('sha256',$str,$CHECKSUM_KEY, false);
		$checksum = strtoupper($checksum);
		//Log::info($checksum); 
		$str.='|'.$checksum;
		//Log::info($str); 

		PaymentTransaction::create([
            'txn' => $CustomerID,
            'application_no' => $id,
            'mobile_no' => $mobile,
            'request_msg' => $str
        ]);

         return view('frontend.application-details-show')
        ->with('application',$application)
        ->with('application_images',$application_images)
        ->with('present_state',$present_state)
        ->with('permanent_state',$permanent_state)
        ->with('permanent_country',$permanent_country)
        ->with('txn_msg',$str)
        ->with('application_no',$id);

		//return view('frontend.payment')->with('txn_msg',$str)->with('application_no',$id);
        //return view('frontend.application-details-show')->with('txn_msg',$str)->with('application_no',$id);

        /*

        $postData = array(
            'msg' => $str
        );       

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
        ));


        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        //get response
        $output = curl_exec($ch);


        //Print error if any
        if (curl_errno($ch)) {
            $isError = true;
            $errorMessage = curl_error($ch);
        }
        curl_close($ch);


        if($isError){
            return array('error' => 1 , 'message' => $errorMessage);
        }else{
            return array('error' => 0 );
        }*/
    }

    public function PaymentResponse(Request $request) {
    	$resp = $request['msg'];
    	//Log::info($resp);
    	//MerchantID|CustomerID|TxnReferenceNo|BankReferenceNo|TxnAmount|BankID|BankMerchantID|TxnType|CurrencyName|ItemCode|SecurityType|SecurityID|SecurityPassword|TxnDate|AuthStatus|SettlementType|AdditionalInfo1|AdditionalInfo2|AdditionalInfo3|AdditionalInfo4|AditionalInfo5|AdditionalInfo6|AdditionalInfo7|ErrorStatus|ErrorDescription|CheckSum
    	$msg_arr = explode('|', $resp);
    	$cust_id = $msg_arr[1];
    	$txn_ref = $msg_arr[2];
    	$amount = $msg_arr[4];
    	$status = $msg_arr[14];
        $payObj = PaymentTransaction::where('txn',$cust_id)->first();
        $payObj->auth_status = $status;
        $payObj->response_msg = $resp;
        $payObj->save();
        $objAppliaction = applicationModel::where('application_id', $payObj->application_no)->first();
        if($status == "0300" ){ 
            
            $objAppliaction->grn = $txn_ref; // Grn = TxnReferenceNo
            $objAppliaction->payement_mode = $msg_arr[9];
            $objAppliaction->bank_code = $msg_arr[6];
            $objAppliaction->brn = $msg_arr[3];
            $objAppliaction->brn_date = $msg_arr[13];
            $objAppliaction->is_fee_paid = 'Y';
            $objAppliaction->fee_amount = $amount;          
            $objAppliaction->current_status = "ASSIGNEDTOSI";            
            $objAppliaction->is_rejected = 'Y';
            $objAppliaction->save();            
            
        }
        $smsObj = new SmsSendController();
        $smsObj->initiateSmsActivation($payObj->mobile_no," Your payement status is ".$msg_arr[24]." and your PCC Application no is ".$payObj->application_no." and Transaction ref no ".$txn_ref);
	
	//$state = State::find($objAppliaction->present_state);
        //$status="eChallan Uploaded Successfully";
        return view('frontend.receipt')
            ->with('status',$msg_arr[24])            
            ->with('txnRefNo',$txn_ref)
            ->with('amount',$amount)
            ->with('application',$objAppliaction);

    } 

    public function reprocessPayment(Request $request){
        $repayments = DB::table('payment_transaction')
         ->leftJoin('pcc_application', 'payment_transaction.application_no', '=', 'pcc_application.application_id')
            ->where('payment_transaction.response_msg', '=', NULL)
             ->orderBy('payment_transaction.created_at', 'desc')->paginate(10);
        return view('payment-reprocess.index')->with('repayments',$repayments);


    }

    public function PaymentQuery(Request $request,$id) {
        $MerchantID = "XBSWBBNPCC";  
        $CustomerID =$request['txnid'];     
        $timenow = date('Ymdhis');
        $SecurityID = 'xbswbbnpcc';
        $CHECKSUM_KEY = "sy9FebdkQ7pd";
        $URL = "https://www.billdesk.com/pgidsk/PGIQueryController";
        $str = '0122|'.$MerchantID.'|'.$CustomerID.'|'.$timenow;
        $checksum = hash_hmac('sha256',$str,$CHECKSUM_KEY, false);
        $checksum = strtoupper($checksum);
        Log::info($checksum); 
        $str.='|'.$checksum;
        Log::info($str); 
        $data1 = [
            'msg' => $str            
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,            
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data1,
            CURLOPT_SSL_VERIFYPEER => false           
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            //print_r(json_decode($response));
            echo $response;
            $msg_arr = explode('|', $response);            
            $cust_id = $msg_arr[2];
            $txn_ref = $msg_arr[3];
            $amount = $msg_arr[4];
            $status = $msg_arr[14];
            $payObj = PaymentTransaction::where('txn',$cust_id)->first();
            $payObj->auth_status = $status;
            $payObj->response_msg = $resp;
            $payObj->save();
            $objAppliaction = applicationModel::where('application_id', $payObj->application_no)->first();
            if($status == "0300" ){ 
                
                $objAppliaction->grn = $txn_ref; // Grn = TxnReferenceNo
                $objAppliaction->payement_mode = $msg_arr[9];
                $objAppliaction->bank_code = $msg_arr[6];
                $objAppliaction->brn = $msg_arr[3];
                $objAppliaction->brn_date = $msg_arr[13];
                $objAppliaction->is_fee_paid = 'Y';
                $objAppliaction->fee_amount = $amount;          
                $objAppliaction->current_status = "ASSIGNEDTOSI";            
                $objAppliaction->is_rejected = 'Y';
                $objAppliaction->save();            
                
            }

            /*
            RequestType
            |
            MerchantID
            |
            CustomerID
            |
            TxnReferenceNo
            |BankReferenceNo|
            TxnAmount
            |Bank
            ID|BankMerchantID|TxnType|CurrencyName|ItemCode|SecurityType|SecurityID|SecurityPa
            ssword|TxnDate|
            AuthStatus
            |SettlementType|
            AdditionalInfo1
            |
            AdditionalInfo2
            |Additional
            Info
            3|AdditionalInfo4|AdditionalInfo5|AdditionalInfo6|AdditionalInfo7|ErrorStatus|ErrorDescripti
            on|
            Filler1
            |
            RefundStatus
            |
            TotalRefundAmount
            |
            LastRefundDate
            |
            LastRefundRefNo
            |
            QueryStatus
            |
            CheckSum


            */
        }
    }
}
