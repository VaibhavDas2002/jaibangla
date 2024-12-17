<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\OTPUser;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Session;
use Auth;

class PublicHomeController extends Controller
{
 //    public function __construct(){
	// 	$mobile = Session::get('session_mobile');
	// 	if ($mobile == '') {
	// 		return redirect('/publiclogin');
	// 	}
	// }
	public function index(){
		// echo Session::get('session_mobile');
		if (Session::get('session_mobile') == '') {
			return redirect('publiclogin');
		}
		//return view('pension_front_details');
		//return view('public_login_home');
		return view('public_login_home');
		
	}
}
