<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginOTP;

class CheckLoginOtpMailSendController extends Controller
{
  public function checkLoginOtpMail(Request $request)
  {
    try {
      $otp='1234';
      $username='gopinathsau';
      $email='sau.gopinath@gmail.com';
      $message='Your OTP for Jai Bangla is '.$otp;
     // $userDetail =  DB::table('users')->select('username', 'email')->where('mobile_no', $number)->where('is_active',1)->first();
			//$bcc_email = 's.mahajan.nic@gmail.com';
			$msg = "Dear ".$username.", ".$message;
			Mail::to( $email)//->bcc($bcc_email)
					->send(new LoginOTP($msg));

      //print 'OTP sent to your mobile no ' . $number . ' and your registered email id '.$email;
    } catch (\Exception $e) {
      dd($e);
    }
  }

  public function initiateSmsActivation($phone_number, $message)
  {
    $SMS_SENDER = "WB_SCOCIALSEC_OTP";
    $RESPONSE_TYPE = 'json';
    $SMS_USERNAME = '9432573344';
    $SMS_PASSWORD = 'Auth$gL22m';
    $SMS_FEEDID = 383807;

    $isError = 0;
    $errorMessage = true;
    $timenow = date('Ymdhi');
    //Preparing post parameters
    $postData = array(
      'feedid' => $SMS_FEEDID,
      'username' => $SMS_USERNAME,
      'password' => $SMS_PASSWORD,
      'To' => $phone_number,
      'Text' => $message,
      'time' => $timenow,
      'senderid' => $SMS_SENDER
    );

    // $url = "https://bulkpush.mytoday.com/BulkSms/SingleMsgApi";


    //$url = "https://bulkpush.mytoday.com/BulkSms/SingleMsgApi?feedid=379522&username=8017072222&password=newAuth\$gL22m&To=".$phone_number."&Text=".urlencode($message)."&time=".$timenow."&senderid=WB_JAIBANGLA" ;


    // $url = "https://bulkpush.mytoday.com/BulkSms/SingleMsgApi?feedid=383807&username=9432573344&password=Auth\$gL22m&To=" . $phone_number . "&Text=" . urlencode($message) . "&time=" . $timenow . "&senderid=WB_SCOCIALSEC_OTP";

    //$url = "https://bulkpush.mytoday.com/BulkSms/SingleMsgApi?feedid=379522&username=8017072222&password=newAuth\$gL22m&To=".$phone_number."&Text=".urlencode($message)."&time=".$timenow."&senderid=WB_JAIBANGLA" ;
    $url = "https://bulkpush.mytoday.com/BulkSms/SingleMsgApi?feedid=383806&username=9748216425&password=newAuth\$gL22m&To=" . $phone_number . "&Text=" . urlencode($message) . "&senderid=WB_SCOCIALSEC_OTP";
    // $url = "https://202.162.232.200/BulkSms/SingleMsgApi?feedid=383806&username=9748216425&password=newAuth\$gL22m&To=" . $phone_number . "&Text=" . urlencode($message) . "&senderid=WB_SCOCIALSEC_OTP";

    Log::info($url);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: text/plain;charset=UTF-8',
      'Connection: Keep-Alive'
    ]);

    $result['content'] = curl_exec($ch);

    //Log::info($url);
    //Log::info($result);
    //Print error if any
    if (curl_errno($ch)) {
      $isError = true;
      $errorMessage = curl_error($ch);
      Log::info($errorMessage);
    }
    curl_close($ch);


    if ($isError) {
      return array('error' => 1, 'message' => $errorMessage);
    } else {
      return array('error' => 0);
    }
  }
}
