<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\OTPUser;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Session;
use Auth;

class PublicLoginController extends Controller
{
	public function index(){
        // if (Session::get('session_mobile') != '') {
        //     return redirect('public-home');
        // }
		return view('public_login');
	}
    public function sendOtp(Request $request){
        $this->validate($request,[
            'mobileno' => 'required|regex:/[0-9]{10}/|digits:10',
           'captcha' => 'required|captcha'
        ]);
        //['captcha.captcha'=>'Invalid captcha code.'];
        
        if($request->input('mobileno') != "" ){
        $otp = rand(100000, 999999);
        $OTPUser = new OTPUser;
        $OTPUser->otp = $otp;
        $OTPUser->mobile = $request->input('mobileno');
        $request->session()->put('session_mobile', $OTPUser->mobile);
        $OTPUser->save();
        $mobileNo = $OTPUser->mobile;
        //$message = "OTP is sent to Mob No ".$mobileNo." OTP is ".$otp;
        $message = "OTP is sent to Mob No ".$mobileNo;
        $smsObj = new SmsSendController();
        $smsObj->initiateSmsActivation($mobileNo,"Your JAI BANGLA Login OTP is ".$OTPUser->otp);
        //return redirect('/publiclogin')->with('message',$message)->with('mobile',$mobileNo);
        return redirect('/publiclogin')->with('message',$message);
     }
    }

    public function verifyOtp(Request $request){
        //$request->session()->forget('session_mobile');
        // $request->session()->forget('application_id');
        $mobile = $request->session()->get('session_mobile');
        // $application = Beneficiary::where('mobile_no','=',$mobile)->first();

        // if($application!=null){
        //     $application_id_number = $application->application_id;
        //     $request->session()->put('application_id', $application_id_number);       
        //     //$application_id = $request->session()->get('application_id');
        // }
       
        //Log::info($mobile);
        $response = array();
        $enteredOtp = $request->input('otp');
        $lastOTP= OTPUser::where('mobile',$mobile)->where('is_verified',0)->orderBy('created_at', 'DESC')->first();
        if(isset($lastOTP)){       
	        if($lastOTP->otp == "" || $lastOTP->otp == null|| $enteredOtp == "" || $enteredOtp == null){
	            echo $message1 = 'OTP is not valid. Please try again.';
	            return redirect('/publiclogin')->with('message1',$message1);
	        }
	    }
	    else{
            echo $message1 = 'Please generate the OTP for login..!';
            return redirect('/publiclogin')->with('message1',$message1);
        }

         if($lastOTP->otp == $enteredOtp)
        {
            $users = OTPUser::all();
            $lastOTP->update(['is_verified' => 1]);

            //$request->session()->forget('session_mobile');
            /*if($objBeneficiary!=null){
                return redirect('/payment/'.$objBeneficiary->application_id);
                //return view('frontend.echalan-payment-details')->with('application_no',$objAppliaction->application_id);  
            }elseif($objStatus!=null){
                Log::info("In this if block");
                
                //return view('frontend.application-view-download')->with('objStatus',$objStatus);
            }*/
            //return view('public_login_home');//->with('application_id',$application_id);
            return redirect('/public-home');
            //return redirect('pensionfront');
            /*else {           
                //return redirect('/application');
                return "new page";
            }*/
        }
        else{
            echo $message1 = 'OTP is not valid. Please try again with the currect OTP.';
            return redirect('/publiclogin')->with('message1',$message1);
        }
    }

    public function pagelogout(Request $request){
           Auth::logout();
           $request->session()->flush();
           $request->session()->regenerate();
           Session::forget('session_mobile');
           return redirect('/publiclogin')->with('message','Logout Successfully..!');
    }

    public function refreshCaptcha()
    {
        //return response()->json(['captcha'=> captcha_img()]);
        return captcha_img('flat');
    }

    public function myCaptcha()
    {
        return view('myCaptcha');
    }
}
