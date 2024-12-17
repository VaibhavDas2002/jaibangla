<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Scheme;
use App\District;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\DSOtpTrack;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;

class DuareSarkarStatusCheckController extends Controller
{
	use SendsPasswordResetEmails;
	public function __construct()
	{
	}
	public function index(Request $request)
	{

		$scheme_id = $request->scheme_id;
		if (!in_array($scheme_id, array(1, 2, 3))) {
			return redirect("/")->with('error', 'Scheme Not Found');
		}
		DB::table('public.ds_status_check_log')->insert([
			'scheme_id' => $scheme_id,
			'change_type' => 'DS_STATUS_CHECK_1',
			'ip_address' => $request->ip()
		]);
		return view(
			'DuareSarkarStatusCheck.index',
			['scheme_id' => $scheme_id]
		);
	}
	public function ds_status_check_sendotp(Request $request)
	{
		$rules = [
			'scheme_id' => 'required|in:1, 2, 3',
			'mobile_no' => 'required|integer|digits:10',
		];
		$attributes = array();
		$messages = array();
		$attributes['scheme_id'] = 'Scheme';
		$attributes['mobile_no'] = 'Mobile No.';
		$validator = Validator::make($request->all(), $rules, $messages, $attributes);
		if (!$validator->passes()) {
			$error_msg = array();
			foreach ($validator->errors()->all() as $error) {
				array_push($error_msg, $error);
			}
			return back()->with('errors', $error_msg)->withInput(Input::all());
		}

		$scheme_id = $request->scheme_id;
		$mobile_no = trim($request->mobile_no);
		$scheme_row = Scheme::where('is_active', 1)->where('id', $scheme_id)->first();
		if (empty($scheme_row)) {
			$errors = array();
			$errorMsg = "Scheme Not Found!";
			array_push($errors, $errorMsg);
			return back()->with('errors', $errors);
		}
		if (!empty($scheme_row->short_code)) {
			$table_schema =   $scheme_row->short_code;
		} else {
			$table_schema = 'pension';
		}
		DB::table('public.ds_status_check_log')->insert([
			'scheme_id' => $scheme_id,
			'change_type' => 'DS_STATUS_CHECK_2',
			'ip_address' => $request->ip(),
			'mobile_no' => $mobile_no
		]);
		$scheme_name = $scheme_row->scheme_name;
		$query = "select count(distinct(main.id)) as count
			from " . $table_schema . ".beneficiary as main 
			where mobile_no='" . $mobile_no . "'";
		$data = DB::connection('pgsql_mis')->select($query);
		//dd($data[0]->count);
		if ($data[0]->count > 0) {
			$otp = rand(111111, 999999);
			//$otp = '123456';
			$message = 'Your OTP for Jai Bangla is ' . $otp . '.   
			Government of West Bengal.';
			$sms_is_sent = $this->initiateSmsActivation($mobile_no, $message);
			//$sms_is_sent = 1;
			if ($sms_is_sent) {
				//dd($scheme_code);

				$currentDateTime = Carbon::now();
				$newDateTime = Carbon::now()->addMinutes(2);
				$model1 = new DSOtpTrack();
				$model1->mobile_no = $mobile_no;
				$model1->scheme_id = $scheme_id;
				$model1->otp_creation_time = $currentDateTime;
				$model1->otp_expire_time = $newDateTime;
				$model1->otp = $otp;
				$is_saved = $model1->save();
				//dd($is_saved);
				if ($is_saved) {
					Session::put('ds_mobile_no', Crypt::encryptString($mobile_no));
					return redirect('/ds_status_check_otp?scheme_id=' . $scheme_id)->with('success', 'OTP has been Successfully sent to Registered Mobile Numner');
				} else {
					$errors = array();
					$errorMsg = "Error..please try after sometime.";
					array_push($errors, $errorMsg);
					return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
				}
			} else {
				$errors = array();
				$errorMsg = "Error..please try after sometime.";
				array_push($errors, $errorMsg);
				return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
			}
		} else {
			$errors = array();
			$errorMsg = "Mobile Number Not Registered";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
	}
	public function ds_status_check_resendotp(Request $request)
	{
		$scheme_id = $request->scheme_id;
		if (empty($scheme_id)) {
			return redirect("/")->with('error', 'Scheme Not Found');
		}
		if (!in_array($scheme_id, array(1, 2, 3))) {
			return redirect("/")->with('error', 'Scheme Not Found');
		}
		if (empty(Session::get('ds_mobile_no'))) {
			$errors = array();
			$errorMsg = "Please Provide Mobile Number";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
		$mobile_no = Crypt::decryptString(Session::get('ds_mobile_no'));
		//dd($mobile_no);
		if (empty($mobile_no)) {
			$errors = array();
			$errorMsg = "Please Provide Mobile Number";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
		DB::table('public.ds_status_check_log')->insert([
			'scheme_id' => $scheme_id,
			'change_type' => 'DS_STATUS_CHECK_3',
			'ip_address' => $request->ip(),
			'mobile_no' => $mobile_no
		]);
		$otp = rand(111111, 999999);
		$otp = '123456';
		$message = 'Your OTP for Jai Bangla is ' . $otp . '.   
			Government of West Bengal.';
		//$sms_is_sent = $this->initiateSmsActivation($mobile_no, $message);
		$sms_is_sent = 1;
		if ($sms_is_sent) {
			//dd($scheme_code);

			$currentDateTime = Carbon::now();
			$newDateTime = Carbon::now()->addMinutes(2);
			$model1 = new DSOtpTrack();
			$model1->mobile_no = $mobile_no;
			$model1->scheme_id = $scheme_id;
			$model1->otp_creation_time = $currentDateTime;
			$model1->otp_expire_time = $newDateTime;
			$model1->otp = $otp;
			$is_saved = $model1->save();
			//dd($is_saved);
			if ($is_saved) {
				return redirect('/ds_status_check_otp?scheme_id=' . $scheme_id)->with('success', 'OTP has been Successfully sent to Registered Mobile Numner');
			} else {
				$errors = array();
				$errorMsg = "Error..please try after sometime.";
				array_push($errors, $errorMsg);
				return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
			}
		} else {
			$errors = array();
			$errorMsg = "Error..please try after sometime.";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
	}
	public function ds_status_check_otp(Request $request)
	{

		$scheme_id = $request->scheme_id;
		if (empty($scheme_id)) {
			return redirect("/")->with('error', 'Scheme Not Found');
		}
		if (!in_array($scheme_id, array(1, 2, 3))) {
			return redirect("/")->with('error', 'Scheme Not Found');
		}
		if (empty(Session::get('ds_mobile_no'))) {
			$errors = array();
			$errorMsg = "Please Provide Mobile Number";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
		$mobile_no = Crypt::decryptString(Session::get('ds_mobile_no'));
		//dd($mobile_no);
		if (empty($mobile_no)) {
			$errors = array();
			$errorMsg = "Please Provide Mobile Number";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
		DB::table('public.ds_status_check_log')->insert([
			'scheme_id' => $scheme_id,
			'change_type' => 'DS_STATUS_CHECK_4',
			'ip_address' => $request->ip(),
			'mobile_no' => $mobile_no
		]);
		return view(
			'DuareSarkarStatusCheck.ds_check_otpcheck',
			['scheme_id' => $scheme_id]
		);
	}
	public function ds_status_check_otp_Post(Request $request)
	{
		//dd('ok');
		$scheme_id = $request->scheme_id;
		//dd($scheme_code);
		$otp = trim($request->login_otp);
		if (empty($scheme_id)) {
			return redirect("/")->with('error', 'Scheme Not Found');
		}
		if (!in_array($scheme_id, array(1, 2, 3))) {
			return redirect("/")->with('error', 'Scheme Not Found');
		}
		if (empty(Session::get('ds_mobile_no'))) {
			$errors = array();
			$errorMsg = "Please Provide Mobile Number";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
		$mobile_no = Crypt::decryptString(Session::get('ds_mobile_no'));
		if (empty($mobile_no)) {
			$errors = array();
			$errorMsg = "Please Provide Mobile Number";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
		$rules = [
			'scheme_id' => 'required|in:1, 2, 3',
			'login_otp' => 'required|integer|digits:6',
		];
		$attributes = array();
		$messages = array();
		$attributes['scheme_id'] = 'Scheme';
		$attributes['login_otp'] = 'OTP';
		$validator = Validator::make($request->all(), $rules, $messages, $attributes);
		if (!$validator->passes()) {
			$error_msg = array();
			foreach ($validator->errors()->all() as $error) {
				array_push($error_msg, $error);
			}
			return back()->with('errors', $error_msg);
		}
		DB::table('public.ds_status_check_log')->insert([
			'scheme_id' => $scheme_id,
			'change_type' => 'DS_STATUS_CHECK_5',
			'ip_address' => $request->ip(),
			'mobile_no' => $mobile_no
		]);
		$query = "select * from public.ds_otp_track where scheme_id=" . $scheme_id . " and 
			mobile_no='" . $mobile_no . "' and otp='" . $otp . "' order by id desc limit 1";
		$data = DB::connection('pgsql_mis')->select($query);

		if (!empty($data)) {
			if (($data[0]->otp_expire_time) < Carbon::now()->toDateTimeString()) {
				$errors = array();
				$errorMsg = "Otp has been expired.Please regenarate the OTP.";
				array_push($errors, $errorMsg);
				return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
			}
			Session::put('ds_otp', Crypt::encryptString($otp));
			return redirect('/ds_status_check_report?scheme_id=' . $scheme_id);
		} else {
			//dd($data);
			$errors = array();
			$errorMsg = "Wrong Otp..Please try again.";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
	}
	public function ds_status_check_report(Request $request)
	{
		$scheme_id = $request->scheme_id;
		if (empty($scheme_id)) {
			return redirect("/")->with('error', 'Scheme Not Found');
		}
		if (!in_array($scheme_id, array(1, 2, 3))) {
			return redirect("/")->with('error', 'Scheme Not Found');
		}
		if (empty(Session::get('ds_mobile_no'))) {
			$errors = array();
			$errorMsg = "Please Provide Mobile Number";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
		$mobile_no = Crypt::decryptString(Session::get('ds_mobile_no'));
		if (empty($mobile_no)) {
			$errors = array();
			$errorMsg = "Please Provide Mobile Number";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
		if (empty(Session::get('ds_otp'))) {
			$errors = array();
			$errorMsg = "Please Provide OTP";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
		$ds_otp = Crypt::decryptString(Session::get('ds_otp'));
		if (empty($ds_otp)) {
			$errors = array();
			$errorMsg = "Please Provide OTP";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check/" . $scheme_id)->with('errors', $errors);
		}
		$scheme_length = NULL;
		$id_length = NULL;
		$scheme_row = Scheme::where('is_active', 1)->where('id', $scheme_id)->first();
		if (empty($scheme_row)) {
			$errors = array();
			$errorMsg = "Scheme Not Found!";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check_sendotp")->with('errors', $errors);
		}
		if (!empty($scheme_row->short_code)) {
			$table_schema =   $scheme_row->short_code;
		} else {
			$table_schema = 'pension';
		}
		DB::table('public.ds_status_check_log')->insert([
			'scheme_id' => $scheme_id,
			'change_type' => 'DS_STATUS_CHECK_6',
			'ip_address' => $request->ip(),
			'mobile_no' => $mobile_no
		]);
		$scheme_name = $scheme_row->scheme_name;
		$scheme_length =  $scheme_row->scheme_length;
		$id_length = $scheme_row->id_length;
		$query = "select created_by_dist_code,scheme_id,id,ben_age,ben_fname,ben_mname,ben_lname,father_fname,father_mname,
		father_lname,dist_code,block_ulb_name,gp_ward_name,next_level_role_id,id,verification_rejected,created_at,is_verified,is_approved,is_rejected
			from " . $table_schema . ".beneficiary 	where mobile_no='" . $mobile_no . "'";
		$data = DB::connection('pgsql_mis')->select($query);
		//dd($data[0]->count);
		if (empty($data)) {
			$errors = array();
			$errorMsg = "Scheme Not Found!";
			array_push($errors, $errorMsg);
			return redirect("/ds_status_check_sendotp")->with('errors', $errors);
		}
		$array = array();
		$i = 0;
		$district = District::get();
		foreach ($data as $row) {
			$app_id = $row->created_by_dist_code . substr('0' . $row->scheme_id, -$scheme_length) . substr('0000000' . $row->id, -$id_length);
			if (!empty($row->ben_fname)) {
				$ben_fname = trim($row->ben_fname);
			} else {
				$ben_fname = '';
			}
			if (!empty($row->ben_mname)) {
				$ben_mname = trim($row->ben_mname);
			} else {
				$ben_mname = '';
			}
			if (!empty($row->ben_lname)) {
				$ben_lname = trim($row->ben_lname);
			} else {
				$ben_lname = '';
			}
			$ben_fullname = $ben_fname . " " . $ben_mname . " " . $ben_lname;
			if (!empty($row->father_fname)) {
				$father_fname = trim($row->father_fname);
			} else {
				$father_fname = '';
			}
			if (!empty($row->father_mname)) {
				$father_mname = trim($row->father_mname);
			} else {
				$father_mname = '';
			}
			if (!empty($row->father_lname)) {
				$father_lname = trim($row->father_lname);
			} else {
				$father_lname = '';
			}
			$father_fullname = $father_fname . " " . $father_mname . " " . $father_lname;
			$array[$i]['ben_fullname'] = $ben_fullname;
			$array[$i]['father_fullname'] = $father_fullname;
			if (!empty($row->dist_code)) {
				$district_arr = $district->where('district_code', $row->dist_code)->first();
				$array[$i]['district_name'] = $district_arr->district_name;
			} else {
				$array[$i]['district_name'] = '';
			}

			$array[$i]['block_ulb_name'] = trim($row->block_ulb_name);
			$array[$i]['gp_ward_name'] = trim($row->gp_ward_name);
			$array[$i]['beneficiary_id'] = $row->id;
			$array[$i]['application_id'] = $app_id;
			$array[$i]['verification_rejected'] = $row->verification_rejected;
			$array[$i]['ben_age'] = trim($row->ben_age);
			$message = '';
			if ($row->is_rejected==1) {
				$message = "Rejected";
			} else if ($row->is_verified==1 && $row->is_approved==1 && $row->is_rejected==0) {
				$message = "Verified but yet not Approved";
			} else if ($row->next_level_role_id == 0) {
				$message = "Verified and Approved";
			}
			$array[$i]['message'] = $message;
			$i++;
		}
		//dd($array);
		Session::forget('ds_mobile_no');
		Session::forget('ds_otp');
		return view(
			'DuareSarkarStatusCheck.statusreport',
			[
				'scheme_id' => $scheme_id,
				'scheme_name' => $scheme_name,
				'list_arr' => $array
			]
		);
	}
}
