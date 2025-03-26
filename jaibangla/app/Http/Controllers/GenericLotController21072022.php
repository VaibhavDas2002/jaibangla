<?php

namespace App\Http\Controllers;

use App\BeneficiaryPensions;
use App\Configduty;
use App\Helpers\Helper;
use App\lot_master;
use App\LotMaster;
use App\LotTypeMaster;
use App\SchemeDesigPaymentMode;
use App\Scheme;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class GenericLotController extends Controller
{
	public function __construct()
	{
		//	$this->middleware('MaintainMiddleware');
		//set_time_limit(200);
	}
	public function lotGenericIndex()
	{
		$generic_class = "active";
		$user_id = Auth::user()->id;
		$schemearray=array();
		$lottype_master = LotTypeMaster::orderBy('id')->get();
		$report = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1)"));
	
		foreach($report as $reportVal){
		array_push($schemearray, $reportVal->id);
	}
	//print_r($schemearray);die;
		if(array_intersect([2,8,9,17,10,11,13,3,7,6], $schemearray)){
			return view('generic-lot/indexschemelot', ['reports' => $report, 'new_lot_no' => '', 'generic_class' => $generic_class]);
			
		}
		else{
			return view('generic-lot/index', ['reports' => $report, 'new_lot_no' => '', 'lottype_master' => $lottype_master, 'generic_class' => $generic_class]);
		}
	
	
		
	}

	public function viewAppendLotResult(Request $request)
	{
		$schemearray=array();
		$user_id = Auth::user()->id;
		$report = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1)"));
	
		foreach($report as $reportVal){
		array_push($schemearray, $reportVal->id);
	}
	if(array_intersect([2,8,9,17,10,11,13,3,7,6], $schemearray)){
		$statusCode = 200;
		$response = [];
		if (!$request->ajax()) {
			$statusCode = 400;
			$response = array('error' => 'Error occured in form submit.');
			return response()->json($response, $statusCode);
		}
	
	
		$generic_class = "active";
		$this->validate($request, [
			'select_month' => 'required',
			'select_lot_type' => 'required',
			'select_scheme' => 'required ',
			'select_year' => 'required ',
			'lot_size' => 'required_if:select_lot_type,<>,2',
			'select_target_mode' => 'required ',
		]);
		$select_month = $request->select_month;
		$select_year = $request->select_year;
		$scheme_id = $request->select_scheme;
		$select_pmt_mode = $request->select_pmt_mode;
		$select_target_mode = $request->select_target_mode;
		$select_lot_type = $request->select_lot_type;
		$select_category = $request->select_category;
		$lot_size = $request->lot_size;

		try {
			
			$getmonthValue = Helper::getMonthValue($select_month, $select_year);
			
			$chk_paid_yymm = $getmonthValue['chk_paid_yymm'];
				if ($chk_paid_yymm == 2003) {
					$chk_paid_yymm = 0;
				} 
			
				$in_lot_no = "";
				//$check=$this->pendingBenCount($select_lot_type, $select_year,$select_month,	$select_category,$scheme_id );
		
				// if((	$check==10000) ||  ($check>=$lot_size)){
				// 	echo 1;die;
					$new_lot_no = Helper::getLotFunction($in_lot_no, $select_year, $select_month, $scheme_id, $chk_paid_yymm, $select_pmt_mode, $select_target_mode, $select_lot_type, $select_category, $lot_size);
				//$new_lot_no['lotno'] = 222222;
				if (!empty($new_lot_no['exception_message'])) {
					$response = array(
						'status' => 2, 'title' => 'Error', 'type' => 'red', 'icon' => 'fa fa-warning', 'content' => $new_lot_no['exception_message']
					);
				}

				if ($new_lot_no['lotno'] == 0) {
					$response = array(
						'status' => 3, 'title' => 'Error', 'type' => 'red', 'icon' => 'fa fa-warning', 'content' => 'Oops.. Something wrong. Function is not working properly.'
					);
				} else {
					$response = array(
						'status' => 1, 'title' => 'Success', 'type' => 'green', 'icon' => 'fa fa-check', 'content' => 'Lot Generated Successfully. Lot No:-' . $new_lot_no['lotno']
					);
				}
				// }
				// else{
				// 	$response = array(
				// 		'status' => 4, 'title' => 'Error', 'type' => 'red', 'icon' => 'fa fa-warning', 'content' => 'Beneficiary count mismatch.'
				// 	);
				// }
				
			
		} catch (\Exception $e) {
			$response = array(
				'exception' => true,
				'exception_message' => $e->getMessage(),
			);
			$statusCode = 400;
		} finally {
			return response()->json($response, $statusCode);
		}
		

	
		
	}
	else{
		$generic_class = "active";



		$this->validate($request, [
			'select_month' => 'required',
			'select_lot_type' => 'required',
			'select_scheme' => 'required ',
			'select_year' => 'required ',

			'select_target_mode' => 'required ',
		]);
		$select_month = $request->select_month;
		$select_year = $request->select_year;
		$scheme_id = $request->select_scheme;
		$select_pmt_mode = $request->select_pmt_mode;
		$select_target_mode = $request->select_target_mode;
		$select_lot_type = $request->select_lot_type;
		$select_category = $request->select_category;
		$lot_size = $request->lot_size;
		$from_month = $request->from_month;
		$to_month = $request->to_month;
		
		if(Session::has('lotTypeSession')){ //echo 1;die;
			Session::forget('schemeSession');
			Session::forget('yearSession');
			Session::forget('monthSession');
			Session::forget('lotTypeSession');
			Session::forget('sourcePaymentSession');
			Session::forget('sourceTargettSession');
		}
		
		if( $request->select_lot_type==2){
			
			Session::put('schemeSession', $request->select_scheme);
			Session::put('monthSession', $request->select_month);
			Session::put('yearSession', $request->select_year);
			Session::put('lotTypeSession', $request->select_lot_type);
		    Session::put('sourcePaymentSession', $request->select_pmt_mode);
			Session::put('sourceTargettSession', $request->select_target_mode);
		}
		
	

	
		//dd($request->all());


		if ($select_lot_type == "8") {
			$select_month = $from_month;
		}

		$getmonthValue = Helper::getMonthValue($select_month, $select_year);


		$get_parent_financial_year = Helper::getFinanceyear($select_month, $select_year);
		//print_r(	$get_parent_financial_year);die;
		if ($select_lot_type == "2" || $select_lot_type == "8") {
			$scheme_name_obj = Scheme::where('id', $scheme_id)->first();
			$scheme_name = $scheme_name_obj->scheme_name;
			$report = lot_master::orderBy('lot_no')->select(
				'lot_no',
				'lot_month',
				'lot_year',
				'ben_count',
				'scheme_id',
				'ifms_wrongdata_count',
				'rbi_failed_count',
				'rbi_success_count'
			)->where('lot_month', $getmonthValue['month'])->where('scheme_id', $scheme_id)
				->where('lot_year', $get_parent_financial_year)
				->where('rbi_success_count', '>', 0)
				->where('ref_no', '>', 0);
			$report = $report->where(function ($query) {
				$query->where('repeat_lot', '=', 0)
					->orwhereNull('repeat_lot');
			});
			$report = $report->where(function ($query) {
				$query->where('repeat_drn_part', '=', 0)
					->orwhereNull('repeat_drn_part');
			});
			/*suman:added above
			$report = $report->where('lot_month', $getmonthValue['month'])->where('scheme_id', $scheme_id)
				->where('rbi_success_count', '>', 0)
				->where('ref_no', '>', 0);*/
			if (!empty($select_pmt_mode)) {
				$report = $report->where('payment_mode', $select_pmt_mode);
			}


			$report = $report->get();
			if ($select_lot_type == 2) {
				if ($scheme_id == 3) {
					$lotNoArr = array();
					foreach ($report as $k) {
						array_push($lotNoArr,$k->lot_no);
					}
					if (count($lotNoArr) > 0) {
						$lotStr = implode(',', $lotNoArr);
						$sbiLotCount = DB::select("SELECT COUNT(1) AS count FROM sbi.transaction_lot WHERE lot_no::int IN(".$lotStr.") AND lot_status=5");
							// dd($sbiLotCount);
						$totalPendingImport = $sbiLotCount[0]->count;
						if ($totalPendingImport == 0) {
							return view('generic-lot/append_lot', [
								'reports' => $report, 'month' => $getmonthValue['month'], 'lot_month' => $select_month, 'lot_year' => $select_year, 'get_parent_financial_year' => $get_parent_financial_year, 'scheme_id' => $scheme_id, 'scheme_name' => $scheme_name, 'chk_paid_yymm' => $getmonthValue['chk_paid_yymm'], 'pmt_mode' => $select_pmt_mode,
								'target_mode' => $select_target_mode, 'lot_type' => $select_lot_type, 'select_category' => $select_category, 'generic_class' => $generic_class
							]);
						}
						else {
							return redirect("generic-lot")
								->with('error', 'Please first complete all the pending import SBI payment report.');
						}
					}
					else {
						return view('generic-lot/append_lot', [
							'reports' => $report, 'month' => $getmonthValue['month'], 'lot_month' => $select_month, 'lot_year' => $select_year, 'get_parent_financial_year' => $get_parent_financial_year, 'scheme_id' => $scheme_id, 'scheme_name' => $scheme_name, 'chk_paid_yymm' => $getmonthValue['chk_paid_yymm'], 'pmt_mode' => $select_pmt_mode,
							'target_mode' => $select_target_mode, 'lot_type' => $select_lot_type, 'select_category' => $select_category, 'generic_class' => $generic_class
						]);
					}
					
				}
				else {
					return view('generic-lot/append_lot', [
						'reports' => $report, 'month' => $getmonthValue['month'], 'lot_month' => $select_month, 'lot_year' => $select_year, 'get_parent_financial_year' => $get_parent_financial_year, 'scheme_id' => $scheme_id, 'scheme_name' => $scheme_name, 'chk_paid_yymm' => $getmonthValue['chk_paid_yymm'], 'pmt_mode' => $select_pmt_mode,
						'target_mode' => $select_target_mode, 'lot_type' => $select_lot_type, 'select_category' => $select_category, 'generic_class' => $generic_class
					]);
				}
				
			} else {


				foreach (Config::get('constants.monthval') as $key => $val) {

					if ($val == $from_month) {
						$fromMonthVal = $key;
					}
					if ($val == $to_month) {
						$toMonthVal = $key;
					}
				}
				if ($toMonthVal > $fromMonthVal) { //echo $fromMonthVal;die;
					$diffMonthVal = $toMonthVal - $fromMonthVal;
					$finalmonthval = $diffMonthVal + 1;
				} else {
					$diffMonthVal = ((12 + $toMonthVal) - $fromMonthVal);
					$finalmonthval = $diffMonthVal + 1;
				}
				//	echo $finalmonthval;die;
				return view('generic-lot/append_clubbing_lot', [
					'reports' => $report, 'month' => $getmonthValue['month'], 'lot_month' => $select_month, 'lot_year' => $select_year, 'get_parent_financial_year' => $get_parent_financial_year, 'scheme_id' => $scheme_id, 'scheme_name' => $scheme_name, 'chk_paid_yymm' => $getmonthValue['chk_paid_yymm'], 'pmt_mode' => $select_pmt_mode,
					'target_mode' => $select_target_mode, 'lot_type' => $select_lot_type, 'select_category' => $select_category,
					'generic_class' => $generic_class, 'from_month' => $from_month, 'to_month' => $to_month, 'finalmonthval' => $finalmonthval,
				]);
			}
		} else {

			$chk_paid_yymm = $getmonthValue['chk_paid_yymm'];
			if ($chk_paid_yymm == 2003) {
				$chk_paid_yymm = 0;
			}
			$in_lot_no = "";
			$new_lot_no = Helper::getLotFunction($in_lot_no, $select_year, $select_month, $scheme_id, $chk_paid_yymm, $select_pmt_mode, $select_target_mode, $select_lot_type, $select_category, $lot_size);
			//$new_lot_no['lotno']=1;
			if (!empty($new_lot_no['exception_message'])) { 
				echo "<pre>";
				print_r($new_lot_no['exception_message']);die;
				return redirect("generic-lot")
					->with('error', 'Oops.. Something wrong. Exception occured.');
			}
			//	echo "<pre>";print_r($new_lot_no);die;
			if ($new_lot_no['lotno'] == 0) {
				return redirect("generic-lot")
					->with('error', 'Oops.. Something wrong. Function is not working properly.');
			} else {
				return redirect("generic-lot")
					->with('success', 'Lot Generated Successfully')
					->with('id', $new_lot_no['lotno']);
			}
		}
	}
	}

	public function appendLotNumber(Request $request)
	{  
		$generic_class = "active";
		$total_ben_checked = $request->total_ben_checked;
		$lot_no_arr = explode(',', ($request->lot_no_arr));
		//$in_lot_no_array = $request->lot_no_arr;
		$in_lot_no = implode("','", $lot_no_arr);
		$lot_year = $request->lot_year;
		$lot_month = $request->lot_month;
		$scheme_id = $request->scheme_id;
		$chk_paid_yymm = $request->chk_paid_yymm;
		$src_pmt_mode = $request->pmt_mode;
		$select_target_mode = $request->target_mode;
		$lot_type = $request->lot_type;
		$select_category = $request->select_category;
		$lot_size = "";
		$from_month = $request->from_month;
		$to_month = $request->to_month;
		$finalmonthval = $request->finalmonthval;
		echo 'SOURCE:' . $src_pmt_mode;
		$new_lot_no = Helper::getLotFunction(
			$in_lot_no,
			$lot_year,
			$lot_month,
			$scheme_id,
			$chk_paid_yymm,
			$src_pmt_mode,
			$select_target_mode,
			$lot_type,
			$select_category,
			$lot_size
		);
		//         if($src_pmt_mode =='IFMS')
		//         {    

		//       $func_call = DB::statement("select sbi.repeat_lot_from_ifms(".$scheme_id.",ARRAY['".$in_lot_no."'],'".$lot_year."','".$lot_month."','','',".$chk_paid_yymm.")");
		// }
		//         elseif($src_pmt_mode=='SBI')
		//         {
		//               $func_call = DB::statement("select sbi.repeat_lot_from_sbi(".$scheme_id.",ARRAY['".$in_lot_no."'],'".$lot_year."','".$lot_month."','','',".$chk_paid_yymm.")");

		//         }
		/*		$new_lot_data = DB::select(DB::raw("select lot_no,credit_count from sbi.transaction_lot where lot_no in (select repeat_drn_part from lot_master where lot_no in ('" . $in_lot_no . "'))"));
		$user_id = Auth::user()->id;
		$schemeObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
		$report = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1)"));

		return view('generic-lot/index', ['new_lot_no' => $new_lot_data[0]->lot_no, 'ben_count' => $new_lot_data[0]->credit_count, 'reports' => $report, 'generic_class' => $generic_class]);
*/
		if (!empty($new_lot_no['exception_message'])) { //echo $new_lot_no['exception_message'];die;
			return redirect("generic-lot")
				->with('error', 'Oops.. Something wrong. Function might not called.');
		}
		if ($new_lot_no['lotno'] == 0) {
			return redirect("generic-lot")
				->with('error', 'Oops.. Something wrong. Function is not working properly.');
		} else {
			return redirect("generic-lot")
				->with('success', 'Lot Generated Sussceefully')
				->with('id', $new_lot_no['lotno']);
		}
	}

	public function getPendingBeneficiaryCount(Request $request)
	{ //dd($request->all());
		$statusCode = 200;
		$response = [];
		if (!$request->ajax()) {
			$statusCode = 400;
			$response = array('error' => 'Error occured in form submit.');
			return response()->json($response, $statusCode);
		}

		$monthVal = "";
		$select_category = $request->select_category;
		$select_lot_type = $request->select_lot_type;
		$select_scheme = $request->select_scheme;
		$select_month = $request->select_month;
		$select_year = $request->select_year;

		foreach (Config::get('constants.monthval') as $key => $val) {

			if ($val == $select_month) {
				$monthVal = $key;
			}
		}

		try {

			$bencount = "";
			$msg = "";
			$explodeYear = explode('-', $select_year);
			$getmonthValue = Helper::getMonthValue($select_month, $select_year);
			if ($select_lot_type != 2) {
				if ($monthVal <= 3) {
					$yearVal = $explodeYear[1];
				} else {
					$yearVal = $explodeYear[0];
				}


				if ($select_lot_type == 4) {
					$lot_generated = '-1';
				} else if ($select_lot_type == 5) {
					$lot_generated = '-2';
				} else if ($select_lot_type == 6) {
					$lot_generated = '-3';
				}
				if ($select_lot_type == 1) {
					$bencount = BeneficiaryPensions::where('payment_count', 0)->where('next_level_role_id', 0)->where('lot_generated', 0)
						->where('last_paid_yymm', '0');
					if (!empty($select_month)) {
						$bencount = $bencount->whereMonth('created_at', $monthVal);
					}

					if (!empty($select_year)) {
						$bencount = $bencount->whereYear('created_at', $yearVal);
					}
				} else  if ($select_lot_type == 3) {
					$bencount = BeneficiaryPensions::where('payment_count', '>', 0)->where('next_level_role_id', 0)->where('lot_generated', 0);
					$bencount = $bencount->where(function ($query) use ($getmonthValue, $monthVal) {
						$query->where('last_paid_yymm', '=', $getmonthValue['chk_paid_yymm']);
					});
				} else if ($select_lot_type == 4 || $select_lot_type == 5 || $select_lot_type == 6) {
					$bencount = BeneficiaryPensions::where('next_level_role_id', 0)
						->where('bank_edited', 1)->where('lot_generated', $lot_generated);
					if (!empty($getmonthValue)) {
						$bencount = $bencount->where(function ($query) use ($getmonthValue, $monthVal, $yearVal) {
							$query->where('payment_count', '>', 0)
								->where('last_paid_yymm', '=', $getmonthValue['chk_paid_yymm']);
							$query->orwhere(function ($query) use ($monthVal, $yearVal) {
								$query->where('payment_count', '=', 0)
									->whereMonth('created_at', $monthVal)
									->whereYear('created_at', $yearVal);
							});
						});
					}
				}
				else  if ($select_lot_type == 9) {
				$bencount = BeneficiaryPensions::where('payment_count', '>', 0)->where('next_level_role_id', 0)->where('lot_generated', 2)
					->where('last_paid_yymm',$getmonthValue['chk_paid_yymm']);
			
				}
				else  if ($select_lot_type == 10) {
					$bencount = BeneficiaryPensions::where('payment_count', '=', 0)->where('next_level_role_id', 0)->where('lot_generated', 0)
						->where('last_paid_yymm',$getmonthValue['chk_paid_yymm']);
				
					}

				if ($select_category != "ALL") {
					$bencount = $bencount->where('caste', $select_category);
				}
				if (!empty($select_scheme)) {
					$bencount = $bencount->where('scheme_id', $select_scheme);
				}
				$bencount = $bencount->count();
			}




			$response = array(
				'lot' => $select_lot_type, 'bencount' => $bencount
			);
		} catch (\Exception $e) {
			$response = array(
				'exception' => true,
				'exception_message' => $e->getMessage(),
			);
			$statusCode = 400;
		} finally {
			return response()->json($response, $statusCode);
		}
	}

	public function getPaymentMode(Request $request)
	{
		$statusCode = 200;
		$response = [];
		if (!$request->ajax()) {
			$statusCode = 400;
			$response = array('error' => 'Error occured in form submit.');
			return response()->json($response, $statusCode);
		}



		try {
			$sourcecData = "";
			$targetData = "";
			$designation_id = Auth::user()->designation_id;
			$scheme_id = $request->select_scheme;
			$sourcePmtData = SchemeDesigPaymentMode::where('designation_id', $designation_id)
				->where('source_is_active', 1);
			if (!empty($scheme_id)) {
				$sourcePmtData = $sourcePmtData->where('scheme_id', $scheme_id);
			}

			$sourcePmtData = $sourcePmtData->get();
			$targetPmtData = SchemeDesigPaymentMode::where('designation_id', $designation_id)
				->where('target_is_active', 1);

			if (!empty($scheme_id)) {
				$targetPmtData = $targetPmtData->where('scheme_id', $scheme_id);
			}
			$targetPmtData = $targetPmtData->get();
			$sourcecData .= '<option value="">---Select Source Payment Mode---</option>';
			$targetData .= '<option value="">---Select Target Payment Mode---</option>';
			foreach ($sourcePmtData as $sourceval) {
				// if (Session::get('sourcePaymentSession')==$key){ 
				// 	$selected='selected';
				// 	sourceTargettSession
				// }
				// else{
				// 	$selected='';
				// }
				// ;
				$sourcecData .= '<option value="' . $sourceval->pmt_mode . '">' . $sourceval->pmt_mode . '</option>';
			}
			foreach ($targetPmtData as $targetval) {
				$targetData .= '<option value="' . $targetval->pmt_mode . '">' . $targetval->pmt_mode . '</option>';
			}

			$response = array('sourcecData' => $sourcecData, 'targetData' => $targetData);
		} catch (\Exception $e) {
			$response = array(
				'exception' => true,
				'exception_message' => $e->getMessage(),
			);
			$statusCode = 400;
		} finally {
			return response()->json($response, $statusCode);
		}
	}

	public function getMonthData(Request $request)
	{ //echo 4;die;
		$statusCode = 200;
		$response = [];
		if (!$request->ajax()) {
			$statusCode = 400;
			$response = array('error' => 'Error occured in form submit.');
			return response()->json($response, $statusCode);
		}
		try {
			$select_year = $request->select_year;
			$select_month = $request->select_month;

			$monthData = '';
			$currentYear = date("Y");
			$previousYear = date("Y", strtotime('-1 year'));
			$nextYear = date('Y', strtotime('+1 year'));
			$currentMonth = date("F");
			$select_lot_type = $request->select_lot_type;
			$nextMonth = date('F', strtotime('first day of +1 month'));
			$currentmonthnumber=date('n');
			//	echo date('F',strtotime('first day of +1 month'));die;
			// echo $currentYear;
			// echo $nextYear;
			// echo $currentMonth;die; 

			//	echo Session::get('monthSession');die;
		
		
			if ($currentmonthnumber <= 3) {
				$currentFinancialyear = $previousYear . '-' . $currentYear;
			}
			else {
				$currentFinancialyear = $currentYear . '-' . $nextYear;
			}
			$monthData .= '<option value="" >---Select Month---</option>';
			if (!empty($select_year) && !empty($select_lot_type)) {
				if ($select_year == $currentFinancialyear) {


					foreach (Config::get('constants.monthlist') as $key => $month) {
						if (Session::get('monthSession') == $key) {
							$selected = 'selected';
						} else {
							$selected = '';
						}
						$monthData .= '<option value="' . $key . '" ' . $selected . '>' . $month . '</option>';

						if ($currentMonth == $key) {
							if ($select_lot_type == 2 ||  $select_lot_type == 3 ||  $select_lot_type == 9) {
								if (Session::get('monthSession') == ucfirst($nextMonth)) {
									$selectedrepeat = 'selected';
								} else {
									$selectedrepeat = '';
								}
								$monthData .= '<option value="' . ucfirst($nextMonth) . '" ' . $selectedrepeat . '>' . strtoupper($nextMonth) . '</option>';
								//	$monthData .='<option value="'.ucfirst($nextMonth).'" '. (Session::get('monthSession')==ucfirst($nextMonth)) ? "selected" : "".'>'.strtoupper($nextMonth).'</option>';
								//	$monthData .='<option value='.ucfirst($nextMonth).' '. (Session::get('monthSession')==ucfirst($nextMonth)) ? "selected" : "".'>'.strtoupper($nextMonth).'</option>';
							}
							break;
						} else {
							continue;
						}
					}
				} else {
					foreach (Config::get('constants.monthlist') as $key => $month) {
						if (Session::get('monthSession') == $key) {
							$selectednormal = 'selected';
						} else {
							$selectednormal = '';
						}
						$monthData .= '<option value="' . $key . '" ' . $selectednormal . '>' . $month . '</option>';
					}
				}
			}


			$response = array(
				'monthData' => $monthData
			);
		} catch (\Exception $e) {
			$response = array(
				'exception' => true,
				'exception_message' => $e->getMessage(),
			);
			$statusCode = 400;
		} finally {
			return response()->json($response, $statusCode);
		}
	}

	public function getSchemeWiseLot(Request $request)
	{
		$statusCode = 200;
		$response = [];
		if (!$request->ajax()) {
			$statusCode = 400;
			$response = array('error' => 'Error occured in form submit.');
			return response()->json($response, $statusCode);
		}
		try {
			$select_scheme = $request->select_scheme;


			if (!empty($select_scheme)) {
				$query = "select * from m_lot_type where id in(select unnest(lot_type_id) lot_type_id from m_scheme where id=" . $select_scheme . ")";
				$getLotMasters = DB::select($query);
			} else {
				$getLotMasters = [];
			}



			$response = array(
				'getLotMasters' => $getLotMasters
			);
		} catch (\Exception $e) {
			$response = array(
				'exception' => true,
				'exception_message' => $e->getMessage(),
			);
			$statusCode = 400;
		} finally {
			return response()->json($response, $statusCode);
		}
	}

	public function pendingBenCount($select_lot_type,$select_year,$select_month,$select_category,$select_scheme){
		// echo $select_lot_type;echo'<br>';
		// echo $select_year;echo'<br>';
		// echo $select_month;echo'<br>';
		// echo $select_category;echo'<br>';die;
		$monthVal="";
		foreach (Config::get('constants.monthval') as $key => $val) {

			if ($val == $select_month) {
				$monthVal = $key;
			}
		}
		$explodeYear = explode('-', $select_year);
		$getmonthValue = Helper::getMonthValue($select_month, $select_year);
		if ($select_lot_type != 2) {
			if ($monthVal <= 3) {
				$yearVal = $explodeYear[1];
			} else {
				$yearVal = $explodeYear[0];
			}
		
		if ($select_lot_type == 4) {
			$lot_generated = '-1';
		} else if ($select_lot_type == 5) {
			$lot_generated = '-2';
		} else if ($select_lot_type == 6) {
			$lot_generated = '-3';
		}
		if ($select_lot_type == 1) {
			$bencount = BeneficiaryPensions::where('payment_count', 0)->where('next_level_role_id', 0)->where('lot_generated', 0)
				->where('last_paid_yymm', '0');
			if (!empty($select_month)) {
				$bencount = $bencount->whereMonth('created_at', $monthVal);
			}

			if (!empty($select_year)) {
				$bencount = $bencount->whereYear('created_at', $yearVal);
			}
		} else  if ($select_lot_type == 3) {
			$bencount = BeneficiaryPensions::where('payment_count', '>', 0)->where('next_level_role_id', 0)->where('lot_generated', 0);
			$bencount = $bencount->where(function ($query) use ($getmonthValue, $monthVal) {
				$query->where('last_paid_yymm', '=', $getmonthValue['chk_paid_yymm']);
			});
		} else if ($select_lot_type == 4 || $select_lot_type == 5 || $select_lot_type == 6) {
			$bencount = BeneficiaryPensions::where('next_level_role_id', 0)
				->where('bank_edited', 1)->where('lot_generated', $lot_generated);
			if (!empty($getmonthValue)) {
				$bencount = $bencount->where(function ($query) use ($getmonthValue, $monthVal, $yearVal) {
					$query->where('payment_count', '>', 0)
						->where('last_paid_yymm', '=', $getmonthValue['chk_paid_yymm']);
					$query->orwhere(function ($query) use ($monthVal, $yearVal) {
						$query->where('payment_count', '=', 0)
							->whereMonth('created_at', $monthVal)
							->whereYear('created_at', $yearVal);
					});
				});
			}
		}

		else  if ($select_lot_type == 9) {
			$bencount = BeneficiaryPensions::where('payment_count', '>', 0)->where('next_level_role_id', 0)->where('lot_generated', 2)
			->where('last_paid_yymm',$getmonthValue['chk_paid_yymm']);
	
		}

		if ($select_category != "ALL") {
			$bencount = $bencount->where('caste', $select_category);
		}





		if (!empty($select_scheme)) {
			$bencount = $bencount->where('scheme_id', $select_scheme);
		}
		$bencount = $bencount->count();
		return 	$bencount;
	}

}
}
