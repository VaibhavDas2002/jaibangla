<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BeneficiaryPensions;
use App\BenStatement;
use App\Configduty;
use App\getModelFunc;
use App\Helpers\Helper;
use App\Helpers\LotGeneration;
use App\lot_master;
use App\LotTypeMaster;
use App\SchemeDesigPaymentMode;
use App\Scheme;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;
use App\Helpers\AuthChecker;

class GenericAccValidationLotController extends Controller
{
	public function __construct()
	{
		//	$this->middleware('MaintainMiddleware');
		//set_time_limit(200);
		$this->middleware('auth');
	}

	public function index()
	{
		return redirect("/")->with('success', 'Validation Lot creation is temporarily suspended due to financial year end migration.');
		$user_id = AuthChecker::getUserId();
		$designation_id = Auth::user()->designation_id;
		$report = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1) and id in(2,10,11)"));
		$bank_list = DB::table('sbi.npci_nach_live_banks')->get();
		return view('generic-lot/sbi_validation_lot_index', ['userId' => $user_id, 'reports' => $report, 'bankLists' => $bank_list]);
	}

	public function pendingBankForValidationLot(Request $request) {
		$statusCode = 200;
		$response = [];
		if (!$request->ajax()) {
			$statusCode = 400;
			$response = array('error' => 'Error occured in form submit.');
			return response()->json($response, $statusCode);
		}
		$select_scheme = $request->select_scheme;
		try {
			$bencount = "";
			$schema_name = Scheme::where('id', $select_scheme)->value('short_code');
			$valPendingquery = '';
			$valPendingquery = "select acc.bank_name,ben.npci_bank_code from sbi.npci_nach_live_banks as acc right join
			(select npci_bank_code,count(1) as cnt
			from " . $schema_name . ".beneficiary where next_level_role_id=0 AND bank_ifsc ~ E'^[A-Za-z]{4}0[0-9a-zA-Z]{6}' AND acc_validated=0 AND created_at <current_date AND length(bank_ifsc)=11 AND ''||trim(bank_code)||'' ~ '^[0-9]+$'=true AND regexp_replace(bank_code, '[^0-9]', '', 'g')<>'' and npci_bank_code is not null 
			group by npci_bank_code) as ben on acc.bank_code=ben.npci_bank_code order by ben.cnt desc";

			$data = DB::select(DB::raw($valPendingquery));
			$response = array(
				'banklist' => $data
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

	public function pendingBenAccValidationLot(Request $request)
	{
		//dd($request->all());
		$statusCode = 200;
		$response = [];
		if (!$request->ajax()) {
			$statusCode = 400;
			$response = array('error' => 'Error occured in form submit.');
			return response()->json($response, $statusCode);
		}
		$select_scheme = $request->select_scheme;
		$select_bank = $request->select_bank;
		try {
			$bencount = "";
			$schema_name = Scheme::where('id', $select_scheme)->value('short_code');
			$bencount = DB::table($schema_name . '.beneficiary')->where('next_level_role_id', 0)->where('acc_validated', 0);
			if (!empty($select_bank)) {
				$bencount = $bencount->where(function ($query) use ($select_bank) {
					$query->where('npci_bank_code', $select_bank)->whereRaw("(bank_ifsc ~ E'^[A-Za-z]{4}0[0-9a-zA-Z]{6}' AND length(bank_ifsc)=11 AND trim(bank_code) ~ '^[0-9]+$'=true AND regexp_replace(bank_code, '[^0-9]', '', 'g')<>'' AND created_at <current_date )");
				});
			}
			if (!empty($select_scheme)) {
				$bencount = $bencount->where('scheme_id', $select_scheme);
			}
			// dd($bencount->toSql());
			$bencount = $bencount->count();
			$response = array(
				'bencount' => $bencount
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

	public function storeAccountValidationLot(Request $request)
	{
		//dd($request->all());
		$statusCode = 200;
		$response = [];
		if (!$request->ajax()) {
			$statusCode = 400;
			$response = array('error' => 'Error occured in form submit.');
			return response()->json($response, $statusCode);
		}
		$select_scheme = $request->select_scheme;
		$select_bank = $request->select_bank;
		$lot_size = $request->lot_size;
		try {
			$this->validate(
				$request,
				[
					'select_scheme' => 'required',
					'select_bank' => 'required ',
					'lot_size' => 'required',
				],
				[
					'select_scheme.required' => 'Please select lot.',
					'select_bank.required' => 'Please select lot type.',
					'lot_size.required' => 'Please select scheme.'
				]
			);

			$pendingQuery = "SELECT sbi.generate_account_validation_lot(" . $select_scheme . ", '" . $select_bank . "', " . $lot_size . ")";
			// dd($pendingQuery);
			$result = DB::select(DB::raw($pendingQuery));
			$file_name = $result[0]->generate_account_validation_lot;
			// dd($bencount);

			$response = array(
				'status' => 3, 'msg' => 'Lot Generated Successfully. With File Name: ' . $file_name,
				'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
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

	public function pendingValidationLotCreateLot(Request $request)
	{
		$statusCode = 200;
		$response = [];
		if ($request->ajax()) {
			$select_scheme = $request->scheme_id;
			$this->validate(
				$request,
				[
					'scheme_id' => 'required',
				],
				[
					'scheme_id.required' => 'Please select lot.'
				]
			);
			$schema_name = Scheme::where('id', $select_scheme)->value('short_code');
			$valPendingquery = '';
			$valPendingquery = "select acc.bank_name,ben.npci_bank_code,ben.cnt from sbi.npci_nach_live_banks as acc right join
			(select npci_bank_code,count(1) as cnt
			from " . $schema_name . ".beneficiary where next_level_role_id=0 AND bank_ifsc ~ E'^[A-Za-z]{4}0[0-9a-zA-Z]{6}' AND acc_validated=0 AND created_at <current_date AND length(bank_ifsc)=11 AND ''||trim(bank_code)||'' ~ '^[0-9]+$'=true AND regexp_replace(bank_code, '[^0-9]', '', 'g')<>'' and npci_bank_code is not null 
			group by npci_bank_code) as ben on acc.bank_code=ben.npci_bank_code order by ben.cnt desc";

			$data = DB::select(DB::raw($valPendingquery));

			return datatables()->of($data)
				->addIndexColumn()
				->addColumn('bank_name', function ($data) {
					return $data->bank_name;
				})
				->addColumn('no_of_ben', function ($data) {
					return $data->cnt;
				})
				->rawColumns(['bank_name', 'no_of_ben'])
				->make(true);
		}
	}

	/*
	public function pushToBankAccValidation(Request $request)
	{
		$statusCode = 200;
		$response = [];
		// if (!$request->ajax()) {
		// 	$statusCode = 400;
		// 	$response = array('error' => 'Error occured in form submit.');
		// 	return response()->json($response, $statusCode);
		// }
		try {


			// For generate xml file and save pdf file
			$signed_xml_file = storage_path('app/DBTWB003080120240004.xml');
			$pdf_file = storage_path('app/DBTWB003080120240004.pdf');
			$xmlContent = file_get_contents($signed_xml_file);
			
			try {
				$pdf = app(PDF::class);
				$header_info = '';
				$header_info .= '<p>Date : '.'31-01-2024'.'</br>';
				$header_info .= '<p>Debit Reference : '.'DBTWB003080120240004'.'</br>';
				$header_info .= '<p>Scheme : '.'Old Aged Pension'.'</br>';
				$header_info .= '<p>Lot Month : '.'January'.'</br>';
				$header_info .= '<p>Lot Financial Year : '.'2024-2025'.'</br>';
				$header_info .= '<p>'.'Signed At : '.'</br>';
				$header_info .= '<p>'.'Signed By : '.'</p>';

				// Load HTML with header, footer, and content
				$html = '<html><head><title>DBTWB003080120240004</title></head><body>';
				$html .= '<div>' . $header_info . '</div>';
				$html .= '<hr/>';
				$html .= '<div style="word-wrap: break-word;">' . htmlspecialchars($xmlContent) . '</div>';
				$html .= '</body></html>';

				$pdf->loadHtml($html);
				$pdf->setPaper('A4', 'portrait');
				$pdf->save($pdf_file);

				// Determine MIME type
				$img_data = file_get_contents($pdf_file);
				$mimeType = mime_content_type($pdf_file);
				$extension = pathinfo($pdf_file, PATHINFO_EXTENSION);
				$base64Content = base64_encode($img_data);

				dump($extension, $mimeType, $base64Content);
			} catch(\Exception $e) {
				dd($e);
			}
			// Delete the file
			// unlink($pdf_file);
			// dump($extension, $mimeType, $base64Content);

			print 'Saved';
			// die;

			// $header = 0;
			// $max = 180;
			// $issues = false;
			// $filename = 'C:\Users\user\Desktop\file creation shell script\AV-14524-SBIN-WB003-10022023-000044-INP.txt';
			// $file = fopen($filename, 'r');

			// while (($line = fgets($file)) !== false) {
			// 	$line = trim($line);

			// 	if ($header === 0) {
			// 		$length = mb_strlen($line);
			// 		$header++;
			// 	} else {
			// 		$length = mb_strlen($line);

			// 		if ($length !== $max) {
			// 			$issues = true;
			// 			break;
			// 		}

			// 		$header++;
			// 	}
			// }

			// fclose($file);

			// if ($issues) {
			// 	// Issues found
			// 	// return response()->json(['message' => 'File has issues'], 400);
			// 	echo 'File has issues';
			// } else {
			// 	// All okay
			// 	// return response()->json(['message' => 'File is okay'], 200);
			// 	echo 'File is okay';
			// }

			$response = array(
				'status' => 3, 'msg' => 'Lot Generated Successfully. With File Name: ',
				'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
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
	*/
}
