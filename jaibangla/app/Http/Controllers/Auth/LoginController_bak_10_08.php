<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\User;
use Carbon\Carbon;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

     /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
     

     
    protected function hasTooManyLoginAttempts ($request) {
        $maxLoginAttempts = 2;
        $lockoutTime = 5; // 5 minutes
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), $maxLoginAttempts, $lockoutTime
        );
    }
    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('guest', ['except' => 'logout']);
    // }

    public function logout(Request $request) {
       Auth::logout();
       $request->session()->flush();
       $request->session()->save();       
       $request->session()->regenerate(true);
       return redirect('/backendlogin');
    }

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->user = new User;
    }

    public function login(Request $request)
    {
        // Check validation
        $this->validate($request, [
            'mobile_no' => 'required|regex:/[0-9]{10}/|digits:10',
            'login_otp' => 'required|regex:/[0-9]{6}/|digits:6',
            // 'captcha' => 'required|captcha'
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        // Get user record
        $current_timestamp = Carbon::now();
        $num = User::where('mobile_no', $request->get('mobile_no'))->count();
        if ($num > 0) {
            $user = User::where('mobile_no', $request->get('mobile_no'))->first();
            if($request->get('mobile_no') == $user->mobile_no) {
                if($request->get('login_otp') == $user->login_otp && ($current_timestamp->diffInMinutes(new Carbon($user->otp_time))) < 60000){
                    \Auth::login($user);
                    return redirect('/');    
                }else if($request->get('login_otp') == $user->login_otp){
                    \Session::put('msg', 'Your OTP has expired. Please re-generate OTP to Login');
                    return back();
                }
                else{
                    \Session::put('msg', 'Please Provide the correct OTP');
                    return back();
                }
                
                
            }
        }         
        
        \Session::put('msg', 'Your mobile number not match in our system..!!');
        return back();
        
    }

    public function refreshCaptcha(){
        return response()->json(['captcha' => captcha_img()]);
    }
}
