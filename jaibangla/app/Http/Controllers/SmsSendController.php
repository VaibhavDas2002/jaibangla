<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SmsSendController extends Controller
{
    //
    private $SMS_SENDER = "WB_JAIBANGLA";
    private $RESPONSE_TYPE = 'json';
    private $SMS_USERNAME = '8017072222';
    private $SMS_PASSWORD = 'newAuth$gL22m';
    private $SMS_FEEDID = 379522;

    public function initiateSmsActivation($phone_number, $message){
	    $isError = 0;
	    $errorMessage = true;
	    $timenow = date('Ymdhi');
	    //Preparing post parameters
	    $postData = array(
	    	'feedid' => $this->SMS_FEEDID,
	        'username' => $this->SMS_USERNAME,
	        'password' => $this->SMS_PASSWORD,
	        'To' => $phone_number,	
	        'Text' => $message,
	        'time' => $timenow,
	        'senderid' => $this->SMS_SENDER        	        
	    );

	    $url = "http://bulkpush.mytoday.com/BulkSms/SingleMsgApi";
	      

	    //$url = "http://bulkpush.mytoday.com/BulkSms/SingleMsgApi?feedid=379522&username=8017072222&password=newAuth\$gL22m&To=".$phone_number."&Text=".urlencode($message)."&time=".$timenow."&senderid=WB_JAIBANGLA" ;

	    $url = "http://bulkpush.mytoday.com/BulkSms/SingleMsgApi?feedid=379523&username=8017072222&password=newAuth\$gL22m&To=".$phone_number."&Text=".urlencode($message)."&time=".$timenow."&senderid=WB_JAIBANGLAOTP" ;
	    



	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		/*curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);*/
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);

		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
    		'Content-Type: text/plain;charset=UTF-8',
    		'Connection: Keep-Alive'
		]);

		$result['content'] = curl_exec($ch);

	    Log::info($url);
	    Log::info($result);
	    //Print error if any
	    if (curl_errno($ch)) {
	        $isError = true;
	        $errorMessage = curl_error($ch);
	        Log::info($errorMessage);
	    }
	    curl_close($ch);


	    if($isError){
	        return array('error' => 1 , 'message' => $errorMessage);
	    }else{
	        return array('error' => 0 );
	    }
	}
}
