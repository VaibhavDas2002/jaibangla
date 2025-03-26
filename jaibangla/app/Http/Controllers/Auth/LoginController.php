<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\User;
use App\Menu_item_master;
use App\Menu_designation_mapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Users_audit_trail;
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

    use SendsPasswordResetEmails;
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';
    public $user;

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */



    protected function hasTooManyLoginAttempts($request)
    {
        $maxLoginAttempts = 2;
        $lockoutTime = 5; // 5 minutes
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $maxLoginAttempts,
            $lockoutTime
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

    public function logout(Request $request)
    {
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
        $this->otp_expire_time =5;
        $this->password_expire_time =90;

    }
    public function forgetPasswordInitial(Request $request)
    {
        return view('auth/forgetPasswordInitial', []);
    }
    public function forgetPasswordInitialPost(Request $request)
    {
       
        try{
        $rules = [
            'mobile_no' => 'required|regex:/[0-9]{10}/|digits:10',
            'captcha' => 'required|captcha'
        ];
        $attributes = array();
        $messages = array();
        $attributes['mobile_no'] = 'Registered Mobile Number';
        $attributes['captcha'] = 'Captcha';
        $messages['captcha.captcha'] = "Invalid captcha";
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        $return_arr = array('is_valid' => false, 'errors' => array());
        if (!$validator->passes()) {
            $error_msg = array();
            foreach ($validator->errors()->all() as $error) {
                array_push($error_msg, $error);
            }
             //dd($error_msg);
            return redirect()->back()->with('errors', $error_msg);
        } else {
            //dd($request->get('mobile_no'));
            $num = User::where('mobile_no', $request->get('mobile_no'))->where('is_active', 1)->count();
            
            if ($num > 0) {
               
                $user = User::where('mobile_no', $request->get('mobile_no'))->where('is_active', 1)->first();
                $otp = rand(111111,999999);
                //$otp = '123456';
                $message = 'Your OTP for Jai Bangla is '.$otp.'.   
Government of West Bengal.';
                $mobile_no=$request->get('mobile_no');
               // $mobile_no='8583035693';
                $send_otp=$this->initiateSmsActivation($mobile_no, $message);
                //$send_otp=1;
               // dd($send_otp);
                if($send_otp){
                    $source_type=1;
                    DB::beginTransaction();
                    $cur_time=Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s');
                    $expire_time=Carbon::now()->setTimezone('Asia/Kolkata')->addMinutes($this->otp_expire_time)->format('Y/m/d H:i:s');
                    $otp_hash=md5($otp);
                    $log_insert=DB::table('public.user_otp')->insert([
                        'otp_hash' =>  $otp_hash, 
                        'mobile' => $mobile_no, 
                        'created_at' => $cur_time,
                        'source_type' =>  $source_type
                    ]);
                    $update_user = User::where('id', $user->id)->where('mobile_no', $user->mobile_no)
                    ->update([
                        'last_otp' =>  $otp_hash, 
                        'flag_sent_otp' =>  $source_type, 
                        'last_otp_generation_time' => $cur_time, 
                        'last_otp_expire_time' =>  $expire_time
                    ]);
                    if ($update_user && $log_insert) {
                        DB::commit();
                        return redirect('check-otp?source_type='.$source_type.'&token_id='.Crypt::encrypt($user->id))->with('msg', 'OTP has Send to your Register mobile Number');
                    }
                    else{
                        DB::rollback();
                        return back()->with('errors', array('Error .Please try again'));
                    }
                    
                   


                }
                
            } else {
                // Session::put('msg', 'Your mobile number not match in our system..!!');
                return redirect('login')->with('errors', array('Your mobile number not match in our system..!!'));
            }
         
                
        }
    }
    catch (\Exception $e) {  
        //dd($e); 
 
        return redirect('login')->with('errors', array('Something went wrong. Please try again.'));
            
        }
        
    }
    public function checkOtp(Request $request)
    {
       
        if(empty($request->get('source_type'))){
            return redirect('login')->with('errors', array('Invalid Signature'));
        }
        $source_type=$request->get('source_type');
        
        if (!in_array($source_type, array(1,2))){
            return redirect('login')->with('errors', array('Invalid Signature'));
        }
        if(empty($request->get('token_id'))){
            return redirect('login')->with('errors', array('Invalid Signature'));
        }
        try{
        $user_id = Crypt::decrypt($request->get('token_id'));
        //dd($user_id);
        if($request->get('source_type') == 1){
            $num = User::where('id', $user_id)->where('is_active', 1)->where('flag_sent_otp',1)->count(); 
        }
        else if($request->get('source_type') == 2){
            $num = User::where('id', $user_id)->where('is_active', 1)->count(); 

        }
        if ($num > 0) {
            if($request->get('source_type') == 1){
                $user = User::where('id', $user_id)->where('is_active', 1)->where('flag_sent_otp',1)->first(); 
            }
            else if($request->get('source_type') == 2){
                $user = User::where('id', $user_id)->where('is_active', 1)->first(); 

            }
            if(empty($user)){
                return redirect('login')->with('errors', array('Invalid Signature'));
            }
    
            if( strtotime(Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s')) > strtotime($user->last_otp_expire_time)){
                return redirect('login')->with('errors', array('Otp has been expired'));
            }
              
            return view('auth/checkOtp', ['token_id' => $request->get('token_id'),'source_type' => $source_type]);
            
           
        } else {
            return redirect('login')->with('errors', array('Invalid Signature'));

        }
       
        }
        catch (\Exception $e) {   
           // dd($e);
 
            return redirect('login')->with('errors', array('Something went wrong. Please try again.'));
                  
              }
    }
    public function checkOtpPost(Request $request)
    {
       
        if($_POST['mybutton'] == 'submit1')
        {
            try{
                $rules = [
                    'token_id' => 'required',
                    'source_type' => 'required',
                    'login_otp' => 'required|regex:/[0-9]{6}/|digits:6',
                    'captcha' => 'required|captcha'
                ];
                $attributes = array();
                $messages = array();
                $attributes['token_id'] = 'Input Signature Not Valid';
                $attributes['source_type'] = 'Input Signature Not Valid';
                $attributes['login_otp'] = 'OTP';
                $attributes['captcha'] = 'Captcha';
                $messages['captcha.captcha'] = 'Invalid Captcha';
                $validator = Validator::make($request->all(), $rules, $messages, $attributes);
                $return_arr = array('is_valid' => false, 'errors' => array());
                if (!$validator->passes()) {
                    $error_msg = array();
                    foreach ($validator->errors()->all() as $error) {
                        array_push($error_msg, $error);
                    }
                    // dd($error_msg);
                    return redirect()->back()->with('errors', $error_msg);
                } else {
                   
                    $user_id = Crypt::decrypt($request->get('token_id'));
                    
                    $num = User::where('id', $user_id)->where('is_active', 1)->count();
                    if ($num > 0) {
                        if($request->get('source_type') == 1){
                            $user = User::where('id', $user_id)->where('is_active', 1)->where('flag_sent_otp',1)->first(); 
                        }
                        else if($request->get('source_type') == 2){
                            $user = User::where('id', $user_id)->where('is_active', 1)->first(); 
        
                        }
                        if(empty($user)){
                            return redirect('login')->with('errors', array('Invalid Signature'));
                        }
                        if( strtotime(Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s')) > strtotime($user->last_otp_expire_time)){
                            return redirect('login')->with('errors', array('Otp has been expired'));
                        }
                        $login_otp_hash = md5($request->login_otp);
                        if($login_otp_hash!=$user->last_otp){
                            return back()->with('errors', array('Otp Not Valid'));
                            
                        }
                        if($login_otp_hash==$user->last_otp){
                            
                        if($request->get('source_type') == 1){
                                        return redirect('reset-password?token_id='.Crypt::encrypt($user->id).'&source_type='.$request->get('source_type'));
                        }
                        if($request->get('source_type') == 2){
                        $update_user = User::where('id', $user->id)
                                        ->update([
                                            'flag_sent_otp' => 0
                                        ]);
                            if($update_user){
                                                $request->session()->flush();
                                                Auth::login($user);
                                                $designation_id = Auth::user()->designation_id;
                                                $this->getMenuList($designation_id);
                                                return redirect('/');
                                }
                         
                        }
                        
                    } else {
                        // Session::put('msg', 'Your mobile number not match in our system..!!');
                        return back()->with('errors', array('Otp Not Valid'));
                    }
                }
                else{
                    return redirect('login')->with('errors', array('Your mobile number not match in our system..!!'));
                }
            }
                 
                        
                
            }
            catch (\Exception $e) {   
         
                return redirect('login')->with('errors', array('Something went wrong. Please try again.'));
                    
                }
        }
        elseif($_POST['mybutton'] == 'submit2')
         {
            if(empty($request->get('source_type'))){
                return redirect('login')->with('errors', array('Invalid Signature'));
            }
            $source_type=$request->get('source_type');
            
            if (!in_array($source_type, array(1,2))){
                return redirect('login')->with('errors', array('Invalid Signature'));
            }
            if(empty($request->get('token_id'))){
                return redirect('login')->with('errors', array('Invalid Signature'));
            }
          
            $user_id = Crypt::decrypt($request->get('token_id'));
            //dd($user_id);
            if($request->get('source_type') == 1){
                $num = User::where('id', $user_id)->where('is_active', 1)->where('flag_sent_otp',1)->count(); 
            }
            else if($request->get('source_type') == 2){
                $num = User::where('id', $user_id)->where('is_active', 1)->count(); 
            }
            if ($num > 0) {
                $user = User::where('id', $user_id)->where('is_active', 1)->first();

                $otp = rand(111111,999999);
                //$otp = '123456';
                $message = 'Your OTP for Jai Bangla is '.$otp.'.   
Government of West Bengal.';
                $mobile_no=$request->get('mobile_no');
                //$mobile_no='8583035693';
                $send_otp=$this->initiateSmsActivation($mobile_no, $message);
                //$send_otp=1;
                if($send_otp){
                    $source_type=$request->get('source_type');
                    DB::beginTransaction();
                    $cur_time=Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s');
                    $expire_time=Carbon::now()->setTimezone('Asia/Kolkata')->addMinutes($this->otp_expire_time)->format('Y/m/d H:i:s');
                    $otp_hash=md5($otp);
                    $log_insert=DB::table('public.user_otp')->insert([
                        'otp_hash' =>  $otp_hash, 
                        'mobile' => $mobile_no, 
                        'created_at' => $cur_time,
                        'source_type' =>  $source_type
                    ]);
                    $update_user = User::where('id', $user->id)->where('mobile_no', $user->mobile_no)
                    ->update([
                        'last_otp' =>  $otp_hash, 
                        'flag_sent_otp' =>  $source_type, 
                        'last_otp_generation_time' => $cur_time, 
                        'last_otp_expire_time' =>  $expire_time
                    ]);
                    if ($update_user && $log_insert) {
                        DB::commit();
                        return redirect('check-otp?source_type='.$source_type.'&token_id='.Crypt::encrypt($user->id))->with('msg', 'OTP has Send to your Register mobile Number');
                    }
                    else{
                        DB::rollback();
                        return back()->with('errors', array('Error .Please try again'));
                    }
                }
                else{
                    return back()->with('errors', array('Error .Please try again'));
                }
            }
            else {
                return redirect('login')->with('errors', array('Invalid Signature'));
    
            }
    
        }
        
        
    }

    public function login(Request $request)
    {
        try{
        if ($request->isMethod('post')){
            $rules = array();
            $messages = array();
            $attributes = array();
            $rules = [
                'mobile_no' => [
                    'required', 
                    'regex:/[0-9]{10}/', 
                    'digits:10'
                ],
                'login_password' => ['required'],
                'captcha' => 'required|captcha'
            ];     
            $attributes = [
                'mobile_no' => 'Mobile Number',
                'login_password' => 'Password',
                'captcha' => 'Captcha'
            ];
            $messages = [
                'captcha.captcha' => 'Invalid captcha code.'
            ];
            $validator = Validator::make($request->all(), $rules, $messages, $attributes);
            if (!$validator->passes()) {
                $error_msg = array();
                foreach ($validator->errors()->all() as $error) {
                    array_push($error_msg, $error);
                }
                 //dd($error_msg);
                return redirect()->back()->with('errors', $error_msg);
            }

     
       }

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        // Get user record
        $current_timestamp = Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s');
        $num = User::where('mobile_no', $request->get('mobile_no'))->where('is_active', 1)->count();
        if ($num > 0) {
            $user = User::where('mobile_no', $request->get('mobile_no'))->where('is_active', 1)->first();

            // Block DDO and Crop
            if ($user->designation_id == 'DDO' || $user->designation_id == 'Corp' || $user->designation_id == 'Delegated DDO' || $user->designation_id == 'PRD_DDO') {
                // Session::put('msg', 'For DDO users the site URL is : https://jaibangla.wb.gov.in/payment/login');
                return back()->with('msg', 'For DDO users the site URL is : https://jaibangla.wb.gov.in/payment/login');
                // return redirect('/')->with('msg', 'For DDO users the site URL is : https://jaibangla.wb.gov.in/payment/login');
            }

            if ($request->get('mobile_no') == $user->mobile_no) {
               //dd(bcrypt($request->login_password));
                //dd('ok');
                if(is_null($user->password) || $user->password==''){
                    return redirect('forget-password-initial')->with('errors', array('Your password yet to set ..please set the passsword'));
                }
                $current_timestamp = Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s');
                //dd(Hash::check($request->login_password, $user->password));
                if ( Hash::check($request->login_password, $user->password) && (strtotime($current_timestamp) < strtotime($user->password_expires_at)) ){
                   // dd('ok1');
                    $otp = rand(111111,999999);
                    $message = 'Your OTP for Jai Bangla is '.$otp.'.   
Government of West Bengal.';
                    $mobile_no=$request->get('mobile_no');
                    //$mobile_no='8583035693';
                    $send_otp=$this->initiateSmsActivation($mobile_no, $message);
                    //$send_otp=1;
                    if($send_otp){
                       
                        DB::beginTransaction();
                        $cur_time=Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s');
                        $expire_time=Carbon::now()->setTimezone('Asia/Kolkata')->addMinutes($this->otp_expire_time)->format('Y/m/d H:i:s');
                        $otp_hash=md5($otp);
                        $log_insert=DB::table('public.user_otp')->insert([
                            'otp_hash' =>  $otp_hash, 
                            'mobile' => $mobile_no, 
                            'created_at' => $cur_time
                        ]);
                        $update_user = User::where('id', $user->id)->where('mobile_no', $user->mobile_no)
                        ->update([
                            'last_otp' =>  $otp_hash, 
                            'flag_sent_otp' => 1, 
                            'last_otp_generation_time' => $cur_time, 
                            'last_otp_expire_time' =>  $expire_time
                        ]);
                        if ($update_user && $log_insert) {
                            //dd('ok');
                            DB::commit();
                            return redirect('check-otp?source_type=2&token_id='.Crypt::encrypt($user->id))->with('msg', 'OTP has been Send to your Register mobile Number');
                        }
                        else{
                            DB::rollback();
                            return back()->with('msg', 'Error .Please try again');
                        }
                        
                       


                    }
                       
                    
                } else if (Hash::check($request->login_password, $user->password) && (strtotime($current_timestamp) > strtotime($user->password_expires_at))) {
                    //dd('ok2');
                    // Session::put('msg', 'Your OTP has expired. Please re-generate OTP to Login');
                    return redirect('forget-password-initial')->with('errors', array('Your password has expired ..please set the new passsword'));
                } else {
                    //dd('ok3');
                    return back()->with('errors', array('Please Provide the correct Password'));
                }
            }
        } else {
            //dd('ok4');
            return back()->with('errors', array('Your mobile number not match in our system..!!'));
        }
    }
    catch (\Exception $e) {   
        //dd($e);
 
        return redirect('login')->with('errors', array('Something went wrong. Please try again.'));
            
        }
    }

    public function refreshCaptcha()
    {
        return response()->json(['captcha'=> captcha_img('flat')]);
    }

    public function setResetPassword(Request $request)
    {
        if(empty($request->get('source_type'))){
            return redirect('login')->with('errors', array('Invalid Signature'));
        }
        $source_type=$request->get('source_type');
        if (!in_array($source_type, array(1,2))){
            return redirect('login')->with('errors', array('Invalid Signature'));
        }
        if(empty($request->get('token_id'))){
            return redirect('login')->with('errors', array('Invalid Signature'));
        }
        try{
        $user_id = Crypt::decrypt($request->get('token_id'));
        if($request->get('source_type') == 1){
            $num = User::where('id', $user_id)->where('is_active', 1)->where('flag_sent_otp',1)->count(); 
        }
        else if($request->get('source_type') == 2){
            $num = User::where('id', $user_id)->where('is_active', 1)->where('flag_sent_otp',2)->count(); 

        }
        if ($num > 0) {

            if($request->get('source_type') == 1){
                $user = User::where('id', $user_id)->where('is_active', 1)->where('flag_sent_otp',1)->first(); 
            }
            else if($request->get('source_type') == 2){
                $user = User::where('id', $user_id)->where('is_active', 1)->first(); 

            }
            if(empty($user)){
                return redirect('login')->with('errors', array('Invalid Signature'));
            }
            if( strtotime(Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s')) > strtotime($user->last_otp_expire_time)){
                return redirect('login')->with('errors', array('Otp has been expired'));
            }
            
            return view('auth/set-password', ['token_id' => $request->get('token_id'),'source_type' => $source_type]);
        } else {
            return redirect('login')->with('errors', array('Invalid Signature'));
        }
       
        }
        catch (\Exception $e) {   
 
            return redirect('login')->with('errors',  array('Something went wrong. Please try again.'));
                  
              }
        
    }

    public function setResetPasswordPost(Request $request)
    {
       
        try{
            $rules = array();
            $messages = array();
            $attributes = array();
            $rules = [
                'user_password' => [
                    'required', 
                    'string', 
                    'min:8', 
                    'regex:/[A-Z]/', // At least one uppercase letter
                    'regex:/[a-z]/', // At least one lowercase letter
                    'regex:/\d/', // At least one digit
                    'regex:/[!@#$%^&*(),.?":{}|<>]/', // At least one special character
                ],
                'confirm_user_password' => ['required', 'same:user_password'],
                'captcha' => 'required|captcha'
            ];
            $messages = [
                'user_password.required' => 'The password is required.',
                'user_password.min' => 'The password must be at least 8 characters.',
                'user_password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.',
                'confirm_user_password.required' => 'The confirmation password is required.',
                'confirm_user_password.same' => 'The confirmation password does not match.',
                'captcha.captcha' => 'Invalid captcha code.'
            ];
            $attributes = [
                'user_password' => 'Password',
                'confirm_user_password' => 'Confirm Password',
                'captcha' => 'Captcha'
            ];
            // Check validation

            $validator = Validator::make($request->all(), $rules, $messages, $attributes);
            if (!$validator->passes()) {
                $error_msg = array();
                foreach ($validator->errors()->all() as $error) {
                    array_push($error_msg, $error);
                }
                 //dd($error_msg);
                return redirect()->back()->with('errors', array('Input Signature Not Valid'));
            } else {
               
                $user_id = Crypt::decrypt($request->get('token_id'));
                
                $num = User::where('id', $user_id)->where('is_active', 1)->count();
                if ($num > 0) {
                    if($request->get('source_type') == 1){
                        $user = User::where('id', $user_id)->where('is_active', 1)->where('flag_sent_otp',1)->first(); 
                    }
                    else if($request->get('source_type') == 2){
                        $user = User::where('id', $user_id)->where('is_active', 1)->where('flag_sent_otp',2)->first(); 
    
                    }
                    if(empty($user)){
                        return redirect('login')->with('errors', array('Invalid Signature'));
                    }
                    if( strtotime(Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s')) > strtotime($user->last_otp_expire_time)){
                        return redirect('login')->with('errors', array('Otp has been expired'));
                    }
                    // Checking that the password is same as previous
                    $previousPassword = $user->password;
                    $password_hash = bcrypt($request->user_password);
                    // print Hash::check($request->user_password, $previousPassword);die;
                    if (Hash::check($request->user_password, $previousPassword)) {
                        return back()->with('errors', ['Your password is the same as the previous one, please try another.']);
                    }
                    

                    $c_time=Carbon::now()->setTimezone('Asia/Kolkata')->format('Y/m/d H:i:s');
                    $password_expires_at= Carbon::now()->setTimezone('Asia/Kolkata')->addDays($this->password_expire_time)->format('Y/m/d H:i:s');
                    DB::beginTransaction();
                   
                    $inserttrail = array(
                        'old_password' => $user->password,
                        'new_password' => $password_hash,
                        'operation_type' => 10,
                        'operate_by' => $user->id,
                        'operate_to_user_id' => $user->id,
                        'ip_address' => request()->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'operation_time' => $c_time
                    );
                    $trailSave = Users_audit_trail::create($inserttrail);
                    $trail_id = $trailSave->id;
                  
                    $update_user = User::where('id', $user->id)->update([
                            'password' => $password_hash, 
                            'flag_sent_otp' => 0, 
                            'password_set_time' => $c_time, 
                            'password_expires_at' => $password_expires_at, 
                            'updated_at' => $c_time
                        ]);
                        
                        if ($update_user && $trail_id) {
                            DB::commit();
                            return redirect('login')->with('msg', 'Password has been updated successfully.');
                        } else {
                            DB::rollback();
                            return back()->with('errors', ['User password update error!!']);
                        }
                   
                 }
            else{
                return redirect('login')->with('errors', array('Your mobile number not match in our system..!!'));
            }
        }
             
                    
            
        }
        catch (\Exception $e) {   
            //dd($e);
     
            return redirect('login')->with('errors', array('Something went wrong. Please try again.'));
                
            }
    }

    private function getMenuList($designation_id)
    {
        if (!Storage::exists('menu/' . $designation_id . ".json")) {
            $parent_menu = array();
            $status = '1';

            $menu_contents = [];
            // For Menu Tree View
            $parent_menu_list = Menu_designation_mapping::where('designation_id', '=', $designation_id)->where('m_menu_designation_mapping.is_active', TRUE)
                ->orderby('m_menu_designation_mapping.rank')
                ->join('m_menu_item_master', 'm_menu_item_master.id', '=', 'm_menu_designation_mapping.menu_id')
                ->whereNull('m_menu_item_master.parent_id')
                ->where('m_menu_item_master.is_active', TRUE)
                ->orderBy('m_menu_item_master.rank')
                ->get([
                    'menu_id',
                    'm_menu_item_master.menu_name',
                    'designation_id',
                    'm_menu_item_master.rank as master_rank',
                    'm_menu_designation_mapping.rank as map_rank',
                    'm_menu_designation_mapping.is_active as map_is_active',
                    'm_menu_item_master.is_active as master_is_active',
                    'parent_id',
                    'menu_name',
                    'url_type',
                    'link_url',
                    'icon',
                    'menu_class'
                ])->toArray();

            foreach ($parent_menu_list as $parent_menu) {
                $menu_contents_item = [];

                $child_menu = Menu_designation_mapping::where('designation_id', '=', $designation_id)->where('m_menu_designation_mapping.is_active', TRUE)
                    ->orderby('m_menu_designation_mapping.rank')
                    ->join('m_menu_item_master', 'm_menu_item_master.id', '=', 'm_menu_designation_mapping.menu_id')
                    ->whereNotNull('m_menu_item_master.parent_id')
                    ->where('m_menu_item_master.parent_id', $parent_menu['menu_id'])
                    ->where('m_menu_item_master.is_active', TRUE)
                    ->orderBy('m_menu_item_master.rank')
                    ->get([
                        'menu_id',
                        'm_menu_item_master.menu_name',
                        'designation_id',
                        'm_menu_item_master.rank as master_rank',
                        'm_menu_designation_mapping.rank as map_rank',
                        'm_menu_designation_mapping.is_active as map_is_active',
                        'm_menu_item_master.is_active as master_is_active',
                        'parent_id',
                        'menu_name',
                        'url_type',
                        'link_url',
                        'icon',
                        'menu_class'
                    ])->toArray();

                $menu_contents_item['id'] = $parent_menu['menu_id'];
                $menu_contents_item['menu_name']  = $parent_menu['menu_name'];
                $menu_contents_item['parent_id']  = $parent_menu['parent_id'];
                $menu_contents_item['icon']  = $parent_menu['icon'];
                $menu_contents_item['link_url']  = $parent_menu['link_url'];
                $menu_contents_item['url_type']  = $parent_menu['url_type'];
                $menu_contents_item['child_menu']  = $child_menu;

                array_push($menu_contents, $menu_contents_item);
            }

            $json_data = json_encode($menu_contents);
            Storage::disk('local')->put('menu/' . $designation_id . ".json", $json_data);
        }
    }
}
