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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

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
    }

    public function login(Request $request)
    {
        // Check validation
        $this->validate(
            $request,
            [
                'mobile_no' => 'required|regex:/[0-9]{10}/|digits:10',
                'login_otp' => 'required|regex:/[0-9]{6}/|digits:6',
                'captcha' => 'required|captcha'
            ],
            ['captcha.captcha' => 'Invalid captcha code.']
        );

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        // Get user record
        $current_timestamp = Carbon::now()->setTimezone('Asia/Kolkata');
        $num = User::where('mobile_no', $request->get('mobile_no'))->where('is_active', 1)->count();
        if ($num > 0) {
            $user = User::where('mobile_no', $request->get('mobile_no'))->where('is_active', 1)->first();

            // Block DDO and Crop
            if ($user->designation_id == 'DDO' || $user->designation_id == 'Corp') {
                // Session::put('msg', 'For DDO users the site URL is : https://jaibangla.wb.gov.in/payment/login');
                return back()->with('msg', 'For DDO users the site URL is : https://jaibangla.wb.gov.in/payment/login');
                // return redirect('/')->with('msg', 'For DDO users the site URL is : https://jaibangla.wb.gov.in/payment/login');
            }

            if ($request->get('mobile_no') == $user->mobile_no) {
                if ($request->get('login_otp') == $user->login_otp && ($current_timestamp->diffInMinutes(new Carbon($user->otp_time))) < 60000) {
                    $is_set_password = $user->flag_set_password;
                    $user_enc = Crypt::encrypt($user->id);
                    $token_enc = Crypt::encrypt(time());
                    // dump($token_enc);
                    Session::put('password_otp_token', $token_enc);
                    // $passwardSMsg = ($is_set_password == FALSE) ? '1' : '2';
                    $passwardSMsg = 1;
                    // dd(Session::get('password_otp_token')); 129600
                    if ($is_set_password == FALSE) {
                        return redirect('reset-password?user=' . $user_enc . '&token=' . $token_enc . '&passwardSetMsg=' . $passwardSMsg);
                    } else {
                        Auth::login($user);
                        $designation_id = Auth::user()->designation_id;
                        $this->getMenuList($designation_id);
                        return redirect('/');
                    }
                } else if ($request->get('login_otp') == $user->login_otp) {
                    // Session::put('msg', 'Your OTP has expired. Please re-generate OTP to Login');
                    return back()->with('msg', 'Your OTP has expired. Please re-generate OTP to Login');
                } else {
                    // Session::put('msg', 'Please Provide the correct OTP');
                    return back()->with('msg', 'Please Provide the correct OTP');
                }
            }
        } else {
            // Session::put('msg', 'Your mobile number not match in our system..!!');
            return back()->with('msg', 'Your mobile number not match in our system..!!');
        }
    }

    public function refreshCaptcha()
    {
        return response()->json(['captcha'=> captcha_img('flat')]);
    }

    public function setResetPassword(Request $request)
    {
        $user_enc = $request->get('user');
        $token_enc = $request->get('token');
        $passwardSMsg = $request->get('passwardSetMsg');
        return view('auth/set-password', ['user_in' => $user_enc, 'token_in' => $token_enc, 'passwardSetMsg' => $passwardSMsg]);
    }

    public function setResetPasswordPost(Request $request)
    {
        $user_enc = $request->get('user');
        $token_enc = $request->get('token');
        if ($user_enc != '' && $token_enc != '') {
            try {
                $user_id = Crypt::decrypt($user_enc);
                $token = Crypt::decrypt($token_enc);
                $password_otp_token_verify =  Crypt::decrypt(Session::get('password_otp_token'));
                if ($token == $password_otp_token_verify) {
                    $user_count = User::where('id', $user_id)->where('is_active', 1)->count();
                    if ($user_count > 0) {
                        $user = User::where('id', $user_id)->where('is_active', 1)->first();
                        $is_set_password = $user->flag_set_password;
                        if ($is_set_password == TRUE) {
                            return redirect('/');
                        } else {
                            $auth_e = isset(Auth::user()->id);
                            if (isset(Auth::user()->id)) {
                                return redirect('/');
                            }
                        }

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
                            return back()->with('errors', $validator->errors()->all()); 
                        }

                        $password_hash = bcrypt($request->user_password);
                        $update_user = User::where('id', $user->id)->where('mobile_no', $user->mobile_no)
                        ->update([
                            'password' => $password_hash, 
                            'flag_set_password' => TRUE, 
                            'password_set_time' => Carbon::now()->setTimezone('Asia/Kolkata'), 
                            'password_expires_at' => Carbon::now()->setTimezone('Asia/Kolkata')->addDays(90), 
                            'updated_at' => Carbon::now()
                        ]);
                        if ($update_user) {
                            $request->session()->flush();
                            Auth::login($user);
                            $designation_id = Auth::user()->designation_id;
                            $this->getMenuList($designation_id);
                            return redirect('/');
                        } else {
                            return redirect('/login')->with('msg', 'User password update error!!');
                        }
                    } else {
                        return redirect('/login')->with('msg', 'User is not registered in the portal');
                    }
                } else {
                    // $request->session()->flush();
                    return redirect('/login')->with('msg', 'Token mismatch, please try again');
                }
            } catch (\Exception $th) {
                // dd($th);
                // $request->session()->flush();
                return redirect('/login')->with('msg', 'Something went wrong!!, please try again');
            }
        } else {
            // $request->session()->flush();
            return redirect('/login')->with('msg', 'Token is required, please try again');
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
