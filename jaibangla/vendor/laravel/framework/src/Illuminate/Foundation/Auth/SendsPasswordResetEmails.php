<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\User;
use Carbon\Carbon;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginOTP;

trait SendsPasswordResetEmails
{

    private $SMS_SENDER = "WB_SCOCIALSEC_OTP";
    private $RESPONSE_TYPE = 'json';
    private $SMS_USERNAME = '9432573344';
    private $SMS_PASSWORD = 'Auth$gL22m';
    private $SMS_FEEDID = 383807;

    public function initiateSmsActivation($phone_number, $message)
    {
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


    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Check validation
        $this->validate($request, [
            'mobile_no' => 'required|regex:/[0-9]{10}/|digits:10'
        ]);
        //, ['captcha.captcha' => 'Invalid captcha code.']
        // Get user record
        $user = User::where('mobile_no', $request->get('mobile_no'))->count();
        if ($user > 0) {
            $otp = rand(111111, 999999);
            $number = $request->get('mobile_no');

            /*-----OTP------*/
            // Your Account SID and Auth Token from twilio.com/console
            // $sid    = env( 'TWILIO_SID' );
            // $token  = env( 'TWILIO_TOKEN' );
            // $client = new Client( $sid, $token );
            // $client->messages->create(
            //     $number,
            //     [
            //         'from' => env( 'TWILIO_FROM' ),
            //         'body' => ' Your OTP is= '.$otp,
            //     ]
            // );        
            /*------*/

            $current_timestamp = Carbon::now()->toDateTimeString();
            User::where('mobile_no', '=', $request->get('mobile_no'))->update(['login_otp' => $otp, 'otp_time' => $current_timestamp]);

            //$message = 'Your JAI BANGLA Login OTP is '.$otp.'. OTP will be valid for 20 hrs.';
            // $message = 'Your OTP for Lakshmir Bhandar scheme login is ' . $otp . '.
            // Lakshmir Bhandar, Govt of WB';
            $message = 'Your OTP for Lakshmir Bhandar scheme login is ' . $otp . ' . Lakshmir Bhandar, Govt of WB.';
            $this->initiateSmsActivation($number, $message);
            $this->send_email($number, $message);
            return redirect('/login')->with('otp', 'OTP sent to your mobile no ' . $number . ' and your registered email id.');
            //return redirect('/login')->with('otp','OTP sent to your mobile no '.$number." OTP is = ".$otp);
        } else {
            \Session::put('status', 'Please enter correct mobile number..!!');
            return back();
        }

        //$current_timestamp = Carbon::now()->timestamp;
        //$current_timestamp = Carbon::now()->toDateTimeString();
        //User::where('mobile_no','=',$request->get('mobile_no'))->update(['login_otp' => '555555','otp_time' => $current_timestamp]);
        //return redirect('/login');
        //print 'send otp';
        //die();

        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Validate the email for the given request.
     *
     * @param \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkResponse($response)
    {
        return back()->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()->withErrors(
            ['email' => trans($response)]
        );
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    public function send_email($number, $message)
    {
        $userDetail =  DB::table('users')->select('username', 'email')->where('mobile_no', $number)->first();
        $bcc_email = 's.mahajan.nic@gmail.com';
        $msg = "Dear " . $userDetail->username . ", " . $message;
        Mail::to($userDetail->email)->bcc($bcc_email)->send(new LoginOTP($msg));
        //echo 'email sent';
        return;
    }
}
