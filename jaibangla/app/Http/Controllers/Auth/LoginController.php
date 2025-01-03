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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
class LoginController extends Controller
{
    /**
     * The user instance.
     *
     * @var \App\User
     */
    protected $user;
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
                // 'captcha' => 'required|captcha'
            ],
            // ['captcha.captcha' => 'Invalid captcha code.']
        );

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        // Get user record
        $current_timestamp = Carbon::now();
        $num = User::where('mobile_no', $request->get('mobile_no'))->where('is_active', 1)->count();
        if ($num > 0) {
            $user = User::where('mobile_no', $request->get('mobile_no'))->where('is_active', 1)->first();
            if ($request->get('mobile_no') == $user->mobile_no) {
                if ($request->get('login_otp') == $user->login_otp) {
                    Auth::login($user);
                    $designation_id = Auth::user()->designation_id;
                    // dd($designation_id);
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
                            $menu_contents_item['menu_name'] = $parent_menu['menu_name'];
                            $menu_contents_item['parent_id'] = $parent_menu['parent_id'];
                            $menu_contents_item['icon'] = $parent_menu['icon'];
                            $menu_contents_item['link_url'] = $parent_menu['link_url'];
                            $menu_contents_item['url_type'] = $parent_menu['url_type'];
                            $menu_contents_item['child_menu'] = $child_menu;

                            array_push($menu_contents, $menu_contents_item);
                        }

                        $json_data = json_encode($menu_contents);
                        Storage::disk('local')->put('menu/' . $designation_id . ".json", $json_data);
                    }
                    return redirect('/');
                } else if ($request->get('login_otp') == $user->login_otp) {
                    Session::put('msg', 'Your OTP has expired. Please re-generate OTP to Login');
                    return back();
                } else {
                    Session::put('msg', 'Please Provide the correct OTP');
                    return back();
                }


            }
        }

        Session::put('msg', 'Your mobile number not match in our system..!!');
        return back();

    }

    public function refreshCaptcha()
    {
        return response()->json(['captcha' => captcha_img()]);
    }
}
