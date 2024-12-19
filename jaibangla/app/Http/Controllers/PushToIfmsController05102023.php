<?php

namespace App\Http\Controllers;


use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\District;
use App\Taluka;
use App\ben_lot_month;
use App\PensionSc;
use App\lot_no_seeder;
use App\lot_master;
use App\Http\Controllers\ReportLotMasterController;
use Illuminate\Support\Facades\Session;
use Excel;
use DOMDocument;
use Response;
use Carbon;

use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;

use App\Configduty;

use Illuminate\Support\Facades\Mail;
use App\Mail\LoginOTP;
use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PushToIfmsController extends Controller
{
	public function __construct()
	{
		//	$this->middleware('MaintainMiddleware');
	}
	public function index()
	{
		//old code
		$user_id = AuthChecker::getUserId();
		$schemes = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
		//$lot_master = lot_master::paginate(10);
		$lot_master = lot_master::where('scheme_id', $schemes->scheme_id)->orderBy('created_at', 'desc')->paginate(10);
		if (empty($lot_master)) {
			return redirect("/")->with('success', 'PLEASE GENERATE LOT ');
		}

		//dd($lot_master);
		return view('push-ifms/index', ['datas' => $lot_master]);
	}



	public function showlist(Request $request)
	{
		$lot_no = $request->lot_no;
		//$scheme_id=$request->scheme_id;

		$ben_lot_month = ben_lot_month::where('drn_part', $lot_no)->get();
		//$districts = District::all();
		//$flag=0;
		//dd($ben_lot_month);
		return view('linelisting_showlist', ['datas' => $ben_lot_month]);

		// return view('employee-report-drilldown/index');
	}

	public function forward(Request $request)
	{
		$lot_nos1 = $request->lot_numbers;
		$lot_nos = unserialize($request->lot_numbers);
		dd($lot_nos);
		//$scheme_id=$request->scheme_id;

		//$ben_lot_month = ben_lot_month::where('lot_no',$lot_no)->where('scheme_id',$scheme_id)->get();

		return redirect("/")->with('success', 'Pushing to IFMS');

		// return view('employee-report-drilldown/index');
	}

	public function exportXml(Request $request)
	{
		$statusCode = 200;
		$response = [];
		if (!$request->ajax()) {
			$statusCode = 400;
			$response = array('error' => 'Error occured in ajax call.');
			return response()->json($response, $statusCode);
		}
		try {
			//	$getReportLotMaster = new ReportLotMasterController();
			/////////////////new code 26.03.2020/////////
			$DRN_part = $request->get('lot_no');               ////parsed value
			$scheme_id = $request->get('scheme_id');          ////parsed value

			$schemeDetail =  DB::table('m_scheme')->select('ddo_code', 'party_code', 'scheme_name')->where('id', '=', $scheme_id)->first();
			$ddocode = $schemeDetail->ddo_code;
			$partyCode = $schemeDetail->party_code;
			$schemeName = $schemeDetail->scheme_name;

			$mytime = Carbon\Carbon::now();
			$mytime->toDateTimeString();

			$dateToSave = $mytime->format('d/m/Y');
			$dateDate = $mytime->format('d');
			$dateMonth = $mytime->format('m');
			$dateYear = $mytime->format('Y');

			$serialNo = '01';

			$filename = $ddocode . $partyCode . $dateDate . $dateMonth . $dateYear . $serialNo . $DRN_part;
			$DRN_full = $dateYear . $dateMonth . $partyCode . $DRN_part;

			$existing_filename_details =  DB::table('lot_master')->select('file_name')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->first();
			$file_name = $existing_filename_details->file_name;
			if ($file_name == $filename) {
				$response = array(
					'status' => 4, 'msg' => 'File has already been sent to IFMS today with the same name for Lot No. ' . $DRN_part,
					'type' => 'orange', 'icon' => 'fa fa-info', 'title' => 'Complete'
				);
			} else {

				$ben_data = DB::table('ifms.transaction_lot_details')->where('drn_part', '=', $DRN_part)->where('scheme_id', '=', $scheme_id)->where('is_active', '<>', 0)->where('wrongdata_flag', 0)->orderBy('name', 'DESC')->get();
				$totalamount = DB::table('ifms.transaction_lot_details')->select(DB::raw('SUM(amount) as total_amount'))->where('drn_part', '=', $DRN_part)->where('scheme_id', '=', $scheme_id)->where('is_active', '<>', 0)->where('wrongdata_flag', 0)->get();

				$totalvalue = $totalamount[0]->total_amount;
				$benf_count = $ben_data->count();
				$xmlFile = new DOMDocument("1.0", 'UTF-8');
				$xmlFile->formatOutput = true;

				$bulkecs  = $xmlFile->createElement("bulkecs");
				$xmlFile->appendChild($bulkecs);
				$drn  = $xmlFile->createElement("DRN", $DRN_full);
				$bulkecs->appendChild($drn);

				$bulkecs->setAttribute('totalamount', $totalvalue);
				$bulkecs->setAttribute('benfcount', $benf_count);

				foreach ($ben_data as $details) {
					$beneficiary = $xmlFile->createElement("BENEFICIARY");
					$bulkecs->appendChild($beneficiary);
					$name = $xmlFile->createElement("BENF_NAME", substr(trim($details->name), 0, 99));
					$beneficiary->appendChild($name);
					$acc_no = $xmlFile->createElement("ACCOUNT_NO", trim($details->acc_no));
					$beneficiary->appendChild($acc_no);
					$ifsc = $xmlFile->createElement("IFSC_CODE", trim($details->ifsc));
					$beneficiary->appendChild($ifsc);
					$mobile_no = $xmlFile->createElement("MOBILE_NO", trim($details->mobile_no));
					$beneficiary->appendChild($mobile_no);
					$amount = $xmlFile->createElement("AMOUNT", trim($details->amount));
					$beneficiary->appendChild($amount);
					$ben_id = $xmlFile->createElement("ID", trim($details->ben_id));
					$beneficiary->appendChild($ben_id);
					$order_no_date = $xmlFile->createElement("ORDER_NO", trim($details->order_no_date));
					$beneficiary->appendChild($order_no_date);
					$unique_id = $xmlFile->createElement("UNIQUE_ID", trim($details->unique_id));
					$beneficiary->appendChild($unique_id);
					$remarks = $xmlFile->createElement("REMARKS", trim($schemeName));
					$beneficiary->appendChild($remarks);
				}


				$xmlFile->save("xml_file/pushed/" . $filename . ".xml");
				$xml_File = $xmlFile->saveXML($xmlFile->documentElement);

				Storage::disk('sftp_' . $partyCode)->put('ePayment_Files_006/' . $filename . '.xml', $xml_File);         ///////uncomment in production

				$exists = Storage::disk('sftp_' . $partyCode)->exists('ePayment_Files_006/' . $filename . '.xml');         ///////uncomment in production
				if ($exists) {
					Storage::put('ifms_xml/pushed/' . $partyCode . '/' . $filename . '.xml', $xml_File);

					$push_to_ifms_status = DB::table('lot_master')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['lot_status' => 0, 'push_to_ifms_status' => 1, 'file_name' => $filename]);
					DB::table('ifms.transaction_lot')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['lot_status' => 1, 'push_to_ifms_status' => 1, 'file_name' => $filename]);
					DB::table('ifms.transaction_payload')->insert(['lot_no' => $DRN_part, 'scheme_id' => $scheme_id, 'file_name' => $filename/*xml, 'sent_payload' => $xml_File*/]);


					if ($push_to_ifms_status) {
						$response = array(
							'status' => 1, 'msg' => 'Lot No:- ' . $DRN_part . ' has been pushed to IFMS successfully.',
							'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
						);
					} else {
						$response = array(
							'status' => 2, 'msg' => 'Status update error. Please Try again later.',
							'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
						);
					}
				} else {
					$response = array(
						'status' => 3, 'msg' => 'File has not been pushed. Please try after sometime.',
						'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
					);
				}
			}
		} catch (\Exception $e) {
			$response = array(
				'exception' => true,
				//'exception_message' => $e->getMessage(),
				'exception_message' => 'Oops. Connection time out. Please try agian later.',
			);
			$statusCode = 400;
		} finally {
			return response()->json($response, $statusCode);
		}
	}


	public function receive_status(Request $request)
	{


		$statusCode = 200;
		$response = [];
		if (!$request->ajax()) {
			$statusCode = 400;
			$response = array('error' => 'Error occured in ajax call.');
			return response()->json($response, $statusCode);
		}
		try {


			$scheme_id = $request->get('scheme_id');
			$schemeDetail =  DB::table('m_scheme')->select('party_code')->where('id', '=', $scheme_id)->first();
			$partyCode = $schemeDetail->party_code;

			$lot_no = $request->get('lot_no');
			$existing_filename_details =  DB::table('lot_master')->select('file_name')->where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->first();
			$file_name = $existing_filename_details->file_name;

			$DRN_part = substr($file_name, 22);
			$exists = Storage::disk('sftp_' . $partyCode)->exists('ePayment_Files_002/' . $file_name . '.xml.done');

			if ($exists) {

				$update_dotdone_status = DB::table('lot_master')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['dotdone_status' => 1]);
				DB::table('ifms.transaction_lot')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['dotdone_status' => 1, 'lot_status' => 2]);
				if ($update_dotdone_status) {
					$response = array(
						'status' => 1, 'msg' => 'Lot No:- ' . $lot_no . ' has been received by IFMS.',
						'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
					);
				} else {
					$response = array(
						'status' => 2, 'msg' => 'Status update error. Please try again later.',
						'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
					);
				}
			} else {
				$response = array(
					'status' => 3, 'msg' => 'Lot no. ' . $lot_no . ' has not yet been received by IFMS.',
					'type' => 'orange', 'icon' => 'fa fa-info', 'title' => 'Not Received'
				);
			}
		} catch (\Exception $e) {
			$response = array(
				'exception' => true,
				//'exception_message' => $e->getMessage(),
				'exception_message' => 'Oops. Connection time out. Please try agian later.',
			);
			$statusCode = 400;
		} finally {
			return response()->json($response, $statusCode);
		}
	}



	public function ack_status(Request $request)
	{
		$statusCode = 200;
		$response = [];
		if (!$request->ajax()) {
			$statusCode = 400;
			$response = array('error' => 'Error occured in ajax call.');
			return response()->json($response, $statusCode);
		}
		try {

			$scheme_id = $request->get('scheme_id');          ////parsed value

			$schemeDetail =  DB::table('m_scheme')->select('party_code')->where('id', '=', $scheme_id)->first();
			$partyCode = $schemeDetail->party_code;

			$lot_no = $request->get('lot_no');    		     ////parsed value
			$existing_filename_details =  DB::table('lot_master')->select('file_name')->where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->first();
			$file_name = $existing_filename_details->file_name;
			$DRN_part = substr($file_name, 22);


			$exists = Storage::disk('sftp_' . $partyCode)->exists('ePayment_Files_002/ACK' . $file_name . '.xml');

			if ($exists) {

				$lot_ack_status = DB::table('lot_master')->select('ack_status')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->first();
				if (is_null($lot_ack_status->ack_status)) {
					$remote_file = Storage::disk('sftp_' . $partyCode)->get('ePayment_Files_002/ACK' . $file_name . '.xml');
					Storage::put('ifms_xml/ack/' . $partyCode . '/ACK' . $file_name . '.xml', $remote_file);
					$remote_xml_file = simplexml_load_string($remote_file);

					$IFMS_REF_NO = $remote_xml_file->IFMS_REF_NO;

					$ifms_status = 'Payment Mandate Generated';

					$update_ack_status = DB::table('lot_master')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['ack_status' => 1, 'ref_no' => $IFMS_REF_NO, 'updated_at' => DB::raw("now()")]);  /////////add
					DB::table('ifms.transaction_lot')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['ack_status' => 1, 'ref_no' => $IFMS_REF_NO, 'updated_at' => DB::raw("now()"), 'lot_status' => 4]);

					$update_ifms_status = DB::table('ifms.transaction_lot_details')->where('drn_part', $DRN_part)->where('scheme_id', $scheme_id)->where('is_active', '<>', 0)->update(['ifms_status' => $ifms_status, 'ifms_ref_no' => $IFMS_REF_NO, 'updated_at' => DB::raw("now()")]);
					DB::table('ifms.transaction_payload')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->where('file_name', $file_name)->update(['status' => 1,/*xml 'ack_payload' => $remote_file,*/ 'updated_at' => DB::raw("now()")]);

					$wrong_file = $this->wrong_file_status($request);

					if ($wrong_file) {
						$response = array(
							'status' => 1, 'msg' => 'Reference No. ' . $IFMS_REF_NO . ' generated for Lot no. ' . $lot_no . '. ' . $wrong_file,
							'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
						);
					} else {
						$response = array(
							'status' => 2, 'msg' => 'Unable to check wrongdata.',
							'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
						);
					}
				} else {
					$response = array(
						'status' => 3, 'msg' => 'Bill reference no is already generated.',
						'type' => 'orange', 'icon' => 'fa fa-info', 'title' => 'Complete'
					);
				}
			} else {


				$list = Storage::disk('sftp_' . $partyCode)->files('ePayment_Files_003');

				$matches = preg_grep('/^ePayment_Files_003\/' . $file_name . '/', $list);
				$matchescount = count($matches);
				if ($matchescount == 0) {

					$filename = '';
				} else {
					foreach ($matches as $matchitem) {
						$matchescount = $matchescount - 1;
						if ($matchescount == 0) {

							$filename = $matchitem;
						}
					}
				}
				$matchexists = Storage::disk('sftp_' . $partyCode)->exists($filename);
				if ($matchexists) {
					$remote_error_file = Storage::disk('sftp_' . $partyCode)->get($filename);
					if (!empty($remote_error_file)) {
						$count_beneficiary = 0;
						$remote_xml_file_data = simplexml_load_string($remote_error_file);
						if (strpos($filename, 'D_') == true) {
							foreach ($remote_xml_file_data as $key => $value) {
								$count_beneficiary++;
							}
						}
						$beneficiary_count_from_db = DB::table('ifms.transaction_lot_details')->where('drn_part', $lot_no)
							->where('scheme_id', $scheme_id)->where('is_active', 1)->count();
						if ($beneficiary_count_from_db == $count_beneficiary) {
							$wrong_file = $this->wrong_file_status($request);
							if ($wrong_file) {
								$response = array(
									'status' => 4, 'msg' => 'Reference No. not generated for Lot no. ' . $lot_no . ' as all beneficiaries failed at IFMS. ' . $wrong_file,
									'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
								);
							} else {
								$response = array(
									'status' => 5, 'msg' => 'Unable to check wrong data.',
									'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
								);
							}
						} else {
							$response = array(
								'status' => 6, 'msg' => 'IFMS Reference Not Generated.',
								'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
							);
						}
					} else {
						$response = array(
							'status' => 7, 'msg' => 'System Error.',
							'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
						);
					}
				} else {
					$response = array(
						'status' => 8, 'msg' => 'No Acknowledgement, No Error File.',
						'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
					);
				}
			}
		} catch (\Exception $e) {
			$response = array(
				'exception' => true,
				//'exception_message' => $e->getMessage(),
				'exception_message' => 'Oops. Connection time out. Please try agian later.',
			);
			$statusCode = 400;
		} finally {
			return response()->json($response, $statusCode);
		}
	}



	public function wrong_file_status(Request $request)
	{      /////parse scheme_id, lot_no

		$scheme_id = $request->get('scheme_id');          ////parsed value
		$schemeDetail =  DB::table('m_scheme')->select('party_code')->where('id', '=', $scheme_id)->first();
		$partyCode = $schemeDetail->party_code;

		$lot_no = $request->get('lot_no');     		     ////parsed value
		$existing_filename_details =  DB::table('lot_master')->select('file_name')->where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->first();
		$file_name = $existing_filename_details->file_name;
		$DRN_part = substr($file_name, 22);
		$list = Storage::disk('sftp_' . $partyCode)->files('ePayment_Files_003');
		//		print_r($list);
		$matchingFiles = preg_grep('/^ePayment_Files_003\/' . $file_name . '/', $list);
		/*=====changed block===*/
		$count = count($matchingFiles);
		if ($count == 0) {
			//echo $item;
			$filename = '';
		} else {
			foreach ($matchingFiles as $item) {
				$count = $count - 1;
				if ($count == 0) {
					//echo $item;
					$filename = $item;
				}
			}
		}
		/*=====changed block===*/
		//changed$filename = implode("",$matchingFiles);
		//echo $filename;
		$exists = Storage::disk('sftp_' . $partyCode)->exists($filename);
		$return_status = '';
		if ($exists) {
			//echo ' Wrong Data File Received ';

			$remote_file = Storage::disk('sftp_' . $partyCode)->get($filename);
			$fname = substr($filename, 18);
			Storage::put('ifms_xml/ifms_resp/' . $partyCode . $fname, $remote_file);
			$remote_xml_file = simplexml_load_string($remote_file);

			if (strpos($filename, 'D_') == true) {
				//echo 'Wrong Data';
				DB::table('lot_master')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['wrongdata_status' => 1]);
				DB::table('ifms.transaction_lot')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['wrongdata_status' => 1]);
				DB::table('ifms.transaction_payload')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->where('file_name', $file_name)->update(['received_ifms_error_file' => $filename]); //xml(['received_ifms_error_payload' => $remote_file]);

				$i = 0;
				foreach ($remote_xml_file as $key => $value) {
					$j = 0;
					foreach ($value as $key2[$j] => $val2[$j]) {
						//echo $key2[$j].$i.$j.'=';
						//echo $val2[$j].' ';
						if ($j == 3) {
							$acc_no = $val2[$j];
						}
						if ($j == 4) {
							$ifsc = $val2[$j];
						}
						if ($j == 5) {
							$mob_no = $val2[$j];
						}
						if ($j == 7) {
							$id = $val2[$j];
						}
						if ($j == 10) {
							DB::table('ifms.ifms_return_details')->insert(['file_name' => $filename, 'drn_part' => $DRN_part, 'scheme_id' => $scheme_id, 'ben_id' => $id, 'error_reason' => $val2[$j]]);
							$pensionidArray = DB::table('ifms.transaction_lot_details')->select('pension_id')->where('ben_id', $id)/*->where('acc_no', $acc_no)->where('ifsc', $ifsc)->where('mobile_no', $mob_no)*/->where('drn_part', $DRN_part)->where('scheme_id', $scheme_id)->get();
							$pension_id = $pensionidArray[0]->pension_id;

							//echo 'pension_id: '.$pension_id; //echo ' error reason: '.$val2[$j].'</br>';
							DB::table('pension.beneficiary')->where('id', $pension_id)->where('scheme_id', $scheme_id)/*->where('bank_code', $acc_no)->where('bank_ifsc', $ifsc)->where('mobile_no', $mob_no)*/->update(['lot_generated' => -1]);    ////Gaurav Help  substr("3161613",4);
							//18nov						DB::table('ben_export')->where('pension_id', $pension_id)->where('drn_part', $DRN_part)->where('scheme_id', $scheme_id)->where('acc_no', $acc_no)->where('ifsc', $ifsc)->where('mobile_no', $mob_no)->update(['ifms_status' => $val2[$j], 'ifms_ref_no' => 0, 'wrongdata_flag' => 1, 'updated_at' => DB::raw("now()") /*, 'paid_yymm' => 0*/]);
							DB::table('ifms.transaction_lot_details')->where('pension_id', $pension_id)->where('drn_part', $DRN_part)->where('scheme_id', $scheme_id)/*->where('acc_no', $acc_no)->where('ifsc', $ifsc)->where('mobile_no', $mob_no)*/->update(['ifms_status' => $val2[$j], 'ifms_ref_no' => 0, 'wrongdata_flag' => 1, 'updated_at' => DB::raw("now()") /*, 'paid_yymm' => 0*/]);
						}
						$j = $j + 1;
					}

					$i = $i + 1;
				}
				//echo ' Wrong Data Count: '.$i;
				DB::table('lot_master')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['ifms_wrongdata_count' => $i]);
				DB::table('ifms.transaction_lot')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['ifms_wrongdata_count' => $i]);

				$return_status =  $i . ' Wrong Data Collected ';
			} else {
				$REASON = $remote_xml_file->REASON;
				//echo 'REASON: '.$REASON;

				DB::table('lot_master')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['lot_status' => 1]);
				DB::table('ifms.transaction_lot')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['lot_status' => 10]);
				//18nov			DB::table('ben_export')->where('drn_part', $DRN_part)->where('scheme_id', $scheme_id)->where('is_active','<>',0)->update(['ifms_status' => $REASON]);
				DB::table('ifms.transaction_lot_details')->where('drn_part', $DRN_part)->where('scheme_id', $scheme_id)->where('is_active', '<>', 0)->update(['ifms_status' => $REASON]);

				return $REASON;
			}
		} else {
			//echo ' Wrong Data File Not Exist ';
		}
		$updateCount3 = DB::statement("update ifms.temp_lot_master set rbi_sent_count=(select count(*) from ifms.transaction_lot_details where drn_part='" . $lot_no . "' and scheme_id=" . $scheme_id . " and is_active<>0 and wrongdata_flag=0)
							where scheme_id=" . $scheme_id . " and lot_no='" . $lot_no . "'");
		//echo ' update count3 : '.$updateCount3.'</br>';

		if ($return_status) {
			return $return_status;
		} else {
			return 'No Wrong Data File Received ';
		}
	}

	public function log_test()
	{
		$time_array = DB::select(DB::raw("select to_char(now(),'DDMONYYYY-HH24MISS') as datetime")); //DB::raw("select to_char(now(),'DDMONYYYY-HH24MISS')");
		$var_file_name = $time_array[0]->datetime;
		//$var_file_name = $dateDate.$dateMonth.$dateYear.$dateHour;
		echo 'testing...<br/>';
		echo $var_file_name;
		//$i=24;
		$log_file_name = 'rbi_log/test_' . $var_file_name . '.txt';
		$email_log_file = 'email_log/temp_schedule_job_file.txt';
		Storage::put($email_log_file, 'testing..');
		Storage::append($email_log_file, 'more testing..' . $var_file_name);
		//$order="12345 abcdefg";
		//Mail::to('s.mahajan.nic@gmail.com')->send(new OrderShipped($order));

		$phone_number = '9038540488';
		$message = 'This is a test message';
		$this->send_email($phone_number, $message);
		//			$userDetail =  DB::table('users')->select('username', 'email')->where('mobile_no', $phone_number)->first();
		//echo $userDetail->username;
		//$email = strval($userDetail->email);
		//			$bcc_email = 's.mahajan.nic@gmail.com';
		//echo $email;
		//$user_name = $userDetail->username;
		//echo $user_name;
		//			$msg = "Dear ".$userDetail->username.", ".$message.".";
		//$message = "abcdefg";
		//echo $message;
		//Mail::to($email)->bcc('s.mahajan.nic@gmail.com')->send(new LoginOTP($message)); 
		//			Mail::to($userDetail->email)->bcc($bcc_email)->send(new LoginOTP($msg));
		echo 'email sent';
		/*29dec	
		$remote_file = Storage::disk('sftp_028')->get('ePayment_Files_002/ACKCAFTWA0010281510202001100272.xml');
		Storage::put('ifms_xml/pushed/026/testACKCAFTWA0010281510202001100272.xml', $remote_file);
		29dec*/
	}

	public function send_email($phone_number, $message)
	{
		$userDetail =  DB::table('users')->select('username', 'email')->where('mobile_no', $phone_number)->first();
		$bcc_email = 's.mahajan.nic@gmail.com';
		$msg = "Dear " . $userDetail->username . ", " . $message . ".";
		Mail::to($userDetail->email)->bcc($bcc_email)
			->send(new LoginOTP($msg));
		echo 'email sent';
		return;
	}

	public function import_rbi_list()
	{
		$time_array = DB::select(DB::raw("select to_char(now(),'DDMONYYYY-HH24MISS') as datetime")); //DB::raw("select to_char(now(),'DDMONYYYY-HH24MISS')");
		$var_file_name = $time_array[0]->datetime;
		$log_file_name = 'rbi_log/log_RBI_' . $var_file_name . '.txt';
		$email_log_file = 'email_log/temp_schedule_job_file.txt';
		Storage::put($log_file_name, 'Function import_rbi_list() is called on ' . $var_file_name);
		Storage::put($email_log_file, 'Function import_rbi_list() is called on ' . $var_file_name);
		echo '<br/>Function import_rbi_list() is called on ' . $var_file_name;
		Storage::append($log_file_name, '=====================================================');
		Storage::append($email_log_file, '=====================================================');
		echo '<br/>=====================================================';

		$party_code_array =  DB::table('m_scheme')->distinct()->select('party_code')->where('is_active', 1)->whereNotNull('party_code')->whereNotNull('ddo_code')->get();
		foreach ($party_code_array as $item) {
			$partyCode = $item->party_code;
			Storage::append($log_file_name, 'Reading RBI list for party_code : ' . $partyCode);
			Storage::append($email_log_file, 'Reading RBI list for party_code : ' . $partyCode);
			echo '<br/>Reading RBI list for party_code : ' . $partyCode;
			$list = Storage::disk('sftp_' . $partyCode)->files('ePayment_Files_005');
			//print_r($list);
			foreach ($list as $file) {
				$schemeDetail =  DB::table('lot_master')->select('scheme_id')->where('file_name', DB::raw("substring('" . $file . "',23,28)"))->first();
				if ($schemeDetail) {
					//18nov							DB::statement("insert into rbi_return_list (scheme_id, file, processed_flag, lot_no)
					//18nov								values (".$schemeDetail->scheme_id.",'".$file."',0,substring('".$file."',45,6))
					//18nov								ON CONFLICT ON CONSTRAINT rbi_return_list_pk DO NOTHING");
					DB::statement("insert into ifms.rbi_return_list (scheme_id, file, processed_flag, lot_no)
								values (" . $schemeDetail->scheme_id . ",'" . $file . "',0,substring('" . $file . "',45,6))
								ON CONFLICT ON CONSTRAINT rbi_return_list_pk DO NOTHING");
				} else {
					Storage::append($log_file_name, 'Invalid file ' . $file . ' found for party_code : ' . $partyCode);
					Storage::append($email_log_file, 'Invalid file ' . $file . ' found for party_code : ' . $partyCode);
				}
			}
		}
		Storage::append($log_file_name, '=====================================================');
		Storage::append($email_log_file, '=====================================================');
		echo '<br/>=====================================================';
		Storage::append($log_file_name, 'Function import_rbi_list() is completed');
		Storage::append($email_log_file, 'Function import_rbi_list() is completed');
		echo '<br/>Function import_rbi_list() is completed';
		$import_new_rbi_file_return = $this->import_new_rbi_file($log_file_name);
		Storage::append($log_file_name, $import_new_rbi_file_return);
		Storage::append($email_log_file, $import_new_rbi_file_return);
		echo '<br/>' . $import_new_rbi_file_return;
		Storage::append($log_file_name, 'Job completed');
		Storage::append($email_log_file, 'Job completed');
		echo '<br/>Job completed';
	}

	public function import_new_rbi_file($log_file_name)
	{
		$email_log_file = 'email_log/temp_schedule_job_file.txt';
		Storage::append($log_file_name, 'Function import_new_rbi_file() is called');
		Storage::append($email_log_file, 'Function import_new_rbi_file() is called');
		echo '<br/>Function import_new_rbi_file() is called';
		Storage::append($log_file_name, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
		Storage::append($email_log_file, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
		echo '<br/>+++++++++++++++++++++++++++++++++++++++++++++++++++++';
		$new_file_array =  DB::table('ifms.rbi_return_list')->select('scheme_id', 'file')->where('processed_flag', 0)->orderBy('file', 'ASC')->get();
		//print_r($new_file_array);
		$f_count = 0;
		foreach ($new_file_array as $item) {
			$scheme_id = $item->scheme_id;
			$file = $item->file;
			$f_count = $f_count + 1;
			Storage::append($log_file_name, '******************** File No. ' . $f_count . ' ********************');
			Storage::append($email_log_file, '******************** File No. ' . $f_count . ' ********************');
			echo '<br/>******************** File No. ' . $f_count . ' ********************';
			Storage::append($log_file_name, 'Import starting for file : ' . $file);
			Storage::append($email_log_file, 'Import starting for file : ' . $file);
			echo '<br/>Import starting for file : ' . $file;
			$import_file = '';
			$import_file = $this->new_payment_status($scheme_id, $file, $log_file_name);
			if ($import_file) {
				//18nov					DB::table('rbi_return_list')->where('file', $file)->where('scheme_id', $scheme_id)->update(['processed_flag' => 1, 'processed_at' => DB::raw("now()")]);
				DB::table('ifms.rbi_return_list')->where('file', $file)->where('scheme_id', $scheme_id)->update(['processed_flag' => 1, 'processed_at' => DB::raw("now()")]);
				Storage::append($log_file_name, $import_file);
				Storage::append($email_log_file, $import_file);
				echo '<br/>' . $import_file;
			} else {
				//DB::table('rbi_return_list')->where('file', $file)->where('scheme_id', $scheme_id)->update(['processed_flag' => $import_file, 'processed_at' => DB::raw("now()")]);
				Storage::append($log_file_name, 'Error from Function new_payment_status() for file : ' . $file);
				Storage::append($email_log_file, 'Error from Function new_payment_status() for file : ' . $file);
				echo '<br/>Error from Function new_payment_status() for file : ' . $file;
			}
		}
		Storage::append($log_file_name, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
		Storage::append($email_log_file, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
		echo '<br/>+++++++++++++++++++++++++++++++++++++++++++++++++++++';
		return 'Function import_new_rbi_file() completed successfully';
	}

	public function new_payment_status($scheme_id, $filename, $log_file_name)
	{         /////parse scheme_id, lot_no
		$email_log_file = 'email_log/temp_schedule_job_file.txt';
		Storage::append($log_file_name, 'Function new_payment_status() is called');
		Storage::append($email_log_file, 'Function new_payment_status() is called');
		echo '<br/>Function new_payment_status() is called';
		Storage::append($log_file_name, '#####################################################');
		Storage::append($email_log_file, '#####################################################');
		echo '<br/>#####################################################';
		//$scheme_id = $request->get('scheme_id');          ////parsed value
		Storage::append($log_file_name, 'scheme_id = ' . $scheme_id);
		Storage::append($email_log_file, 'scheme_id = ' . $scheme_id);
		echo '<br/>scheme_id = ' . $scheme_id;
		$schemeDetail =  DB::table('m_scheme')->select('party_code')->where('id', $scheme_id)->first();
		$partyCode = $schemeDetail->party_code;

		//$filename = $request->get('file');     			     ////parsed value
		Storage::append($log_file_name, 'file name = ' . $filename);
		Storage::append($email_log_file, 'file name = ' . $filename);
		echo ' file name = ' . $filename;
		$DRN_part = substr($filename, 44, 6);
		Storage::append($log_file_name, 'LOT No. = ' . $DRN_part);
		Storage::append($email_log_file, 'LOT No. = ' . $DRN_part);
		echo ' LOT No. = ' . $DRN_part;
		//$duplicate_count=0;
		/*remove exist $exists = Storage::disk('sftp_'.$partyCode)->exists($filename);
if($exists){
echo ' exist ';*/

		//$remote_file = Storage::disk('sftp_'.$partyCode)->get('ePayment_Files_005/'.$partyCode.$filename.'.xml');
		$remote_file = Storage::disk('sftp_' . $partyCode)->get($filename);
		$fname = substr($filename, 18);
		Storage::put('ifms_xml/rbi_resp/' . $partyCode . $fname, $remote_file);
		$remote_xml_file = simplexml_load_string($remote_file);
		//xml		DB::table('ifms.rbi_return_list')->where('file', $filename)->where('scheme_id', $scheme_id)->update(['return_payload' => $remote_file]);

		/*====================================Payment file code block============================================*/
		Storage::append($log_file_name, '********Reading Payment Data********');
		Storage::append($email_log_file, '********Reading Payment Data********');
		echo '</br>********Reading Payment Data********</br>';
		$DRN = $remote_xml_file->DRN;
		Storage::append($log_file_name, 'DRN: ' . $DRN . ', ');
		Storage::append($email_log_file, 'DRN: ' . $DRN . ', ');
		echo 'DRN: ' . $DRN . ', ';
		$voucherNo = $remote_xml_file->voucherNo;
		Storage::append($log_file_name, 'Voucher No: ' . $voucherNo . ', ');
		Storage::append($email_log_file, 'Voucher No: ' . $voucherNo . ', ');
		echo 'Voucher No: ' . $voucherNo . ', ';
		$voucherDate = $remote_xml_file->voucherDate;
		Storage::append($log_file_name, 'Voucher Date: ' . $voucherDate . ', ');
		Storage::append($email_log_file, 'Voucher Date: ' . $voucherDate . ', ');
		echo 'Voucher Date: ' . $voucherDate . ', ';
		$tokenNo = $remote_xml_file->tokenNo;
		Storage::append($log_file_name, 'Token No: ' . $tokenNo . ', ');
		Storage::append($email_log_file, 'Token No: ' . $tokenNo . ', ');
		echo 'Token No: ' . $tokenNo . ', ';
		$tokenDate = $remote_xml_file->tokenDate;
		Storage::append($log_file_name, 'Token Date: ' . $tokenDate . ', ');
		Storage::append($email_log_file, 'Token Date: ' . $tokenDate . ', ');
		echo 'Token Date: ' . $tokenDate . ', ';

		Storage::append($log_file_name, '********Reading Beneficiary Payment Data********');
		Storage::append($email_log_file, '********Reading Beneficiary Payment Data********');
		echo '</br>********Reading Beneficiary Payment Data********</br>';

		/*=====Working Code====*//*
			$accountNumber = $remote_xml_file->beneficiaryDetail->accountNumber;
			echo ',accountNumber: '.$accountNumber;
			*//*====================*/
		$success_count = 0;
		$failed_count = 0;
		$i = 0;
		foreach ($remote_xml_file as $key => $value) {

			$j = 0;
			foreach ($value as $key2[$j] => $val2[$j]) {
				//					echo $key2[$j].'['.($i-4).']['.$j.']=';
				//					echo $val2[$j].' ';

				if ($j == 0) {
					$acc_no = $val2[$j];
				}
				if ($j == 1) {
					$amount = $val2[$j];
				}
				if ($j == 2) {
					$fileName = $val2[$j];
				}
				if ($j == 3) {
					$ifscCode = $val2[$j];
				}
				if ($j == 4) {
					$paymentDt = $val2[$j];
				}
				if ($j == 5) {
					$reason = $val2[$j];
				}
				if ($j == 6) {
					$refBenfId = $val2[$j];
				}
				if ($j == 7) {
					$refOrdernoDt = $val2[$j];
				}
				if ($j == 8) {
					$referenceNo = $val2[$j];
				}
				if ($j == 9) {
					$status = $val2[$j];
				}
				if ($j == 10) {
					$utrNo = $val2[$j];

					if ($status == 'Success') {
						$success_count = $success_count + 1;
					} elseif ($status == 'Failed') {
						$failed_count = $failed_count + 1;
					} else {
						break;
						Storage::append($log_file_name, ' *** Alert *** : Status received either null or other than Success or Failed !!! ');
						Storage::append($email_log_file, ' *** Alert *** : Status received either null or other than Success or Failed !!! ');
						echo ' *** Alert *** : Status received either null or other than Success or Failed !!! ';
					}
					//18nov							DB::statement("insert into rbi_payment_import (account_no, ifsc, amount, file_name, payment_date, ben_id, order_no_date, ref_no, status, reason, utr_no, lot_no, scheme_id)
					//18nov							values ('".$acc_no."','".$ifscCode."',".$amount.",'".$fileName."',to_date('".$paymentDt."','dd/mm/yyyy'),'".$refBenfId."','".$refOrdernoDt."',".$referenceNo.",'".$status."','".$reason."','".$utrNo."','".$DRN_part."',".$scheme_id.")
					//18nov							ON CONFLICT ON CONSTRAINT rbi_payment_import_pk DO UPDATE SET status=EXCLUDED.status, reason=EXCLUDED.reason, utr_no=EXCLUDED.utr_no");//UPDATE SET status='".$status."' , reason='".$reason."' , utr_no='".$utrNo."' WHERE 
					DB::statement("insert into ifms.rbi_payment_import (account_no, ifsc, amount, file_name, payment_date, ben_id, order_no_date, ref_no, status, reason, utr_no, lot_no, scheme_id)
							values ('" . $acc_no . "','" . $ifscCode . "'," . $amount . ",'" . $fileName . "',to_date('" . $paymentDt . "','dd/mm/yyyy'),'" . $refBenfId . "','" . $refOrdernoDt . "'," . $referenceNo . ",'" . $status . "','" . $reason . "','" . $utrNo . "','" . $DRN_part . "'," . $scheme_id . ")
							ON CONFLICT ON CONSTRAINT rbi_payment_import_pk DO UPDATE SET status=EXCLUDED.status, reason=EXCLUDED.reason, utr_no=EXCLUDED.utr_no");
					/*changed with pgsql upsert for pgsql 9.6 >= 9.5 as insertOrIgnore is not available in laravel 5.4 <= 5.8
							$insert_validator= '';
						$insert_validator = DB::table('rbi_payment_import')->insertOrIgnore(['account_no' => $acc_no,
																 'ifsc' => $ifscCode,
																 'amount' => $amount,
																 'file_name' => $fileName,
																 'payment_date' => DB::raw("to_date('".$paymentDt."','dd/mm/yyyy')"),
																 'ben_id' => $refBenfId,
																 'order_no_date' => $refOrdernoDt,
																 'ref_no' => $referenceNo,
																 'status' => $status,
																 'reason' => $reason,
																 'utr_no' => $utrNo,																 
																 'lot_no' => $DRN_part,
																 'scheme_id' => $scheme_id]);*/
					/*12.05.2020 if(is_null($insert_validator)){
							return ' Table rbi_payment_import Insert Interrupted</br>';
							//return redirect("/report_lot_master")->with('danger','Table rbi_payment_import Insert Interrupted');
							}
						}else{
							echo ' Duplicate data received from RBI for ben_id : '.$refBenfId.'</br>';
							$duplicate_count = $duplicate_count + 1;
						}*/
				}
				$j = $j + 1;

				//if($j==11){echo '</br>';}
			}

			$i = $i + 1;
		}
		Storage::append($log_file_name, ' Total no. of records found in RBI file ' . $filename . ' : ' . ($i - 5));
		Storage::append($email_log_file, ' Total no. of records found in RBI file ' . $filename . ' : ' . ($i - 5));
		Storage::append($log_file_name, ' Total Success count = ' . $success_count . ' || Total Failed count = ' . $failed_count);
		Storage::append($email_log_file, ' Total Success count = ' . $success_count . ' || Total Failed count = ' . $failed_count);
		echo ' Total no. of records found in RBI file ' . $filename . ' : ' . ($i - 5);
		echo '</br>Total Success count = ' . $success_count . ' || Total Failed count = ' . $failed_count . '</br>';
		$ben_export_count = DB::table('ifms.transaction_lot_details')->where(function ($query) {
			$query->where('wrongdata_flag', 0)->orWhere('wrongdata_flag', 2);
		})->where('is_active', '<>', 0)->where('drn_part', $DRN_part)->where('scheme_id', $scheme_id)->count();

		$rbi_receive_success_count = DB::table('ifms.rbi_payment_import')->where('status', 'Success')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->count();
		$rbi_receive_failed_count = DB::table('ifms.rbi_payment_import')->where('status', 'Failed')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->count();

		//18nov			DB::statement("insert into temp_lot_master (lot_no, scheme_id, payment_status, drn, voucher_no, voucher_date, token_no, token_date, rbi_sent_count, rbi_receive_success_count, rbi_receive_failed_count)
		//18nov							values ('".$DRN_part."',".$scheme_id.",1,'".$DRN."',".$voucherNo.",to_date('".$voucherDate."','dd/mm/yyyy'),".$tokenNo.",to_date('".$tokenDate."','dd/mm/yyyy'),".$ben_export_count.",".$rbi_receive_success_count.",".$rbi_receive_failed_count.")
		//18nov							ON CONFLICT ON CONSTRAINT temp_lot_master_pk DO UPDATE SET rbi_receive_success_count=EXCLUDED.rbi_receive_success_count, rbi_receive_failed_count=EXCLUDED.rbi_receive_failed_count");
		DB::statement("insert into ifms.temp_lot_master (lot_no, scheme_id, payment_status, drn, voucher_no, voucher_date, token_no, token_date, rbi_sent_count, rbi_receive_success_count, rbi_receive_failed_count)
							values ('" . $DRN_part . "'," . $scheme_id . ",1,'" . $DRN . "'," . $voucherNo . ",to_date('" . $voucherDate . "','dd/mm/yyyy')," . $tokenNo . ",to_date('" . $tokenDate . "','dd/mm/yyyy')," . $ben_export_count . "," . $rbi_receive_success_count . "," . $rbi_receive_failed_count . ")
							ON CONFLICT ON CONSTRAINT temp_lot_master_pk DO UPDATE SET rbi_receive_success_count=EXCLUDED.rbi_receive_success_count, rbi_receive_failed_count=EXCLUDED.rbi_receive_failed_count");
		/*========================================================================================================*/

		return ' Function new_payment_status() Returned Successfully for file ' . $filename;
		//return $duplicate_count;
	}


	public function rbi_payment_status(Request $request)
	{
		$statusCode = 200;
		$response = [];
		if (!$request->ajax()) {
			$statusCode = 400;
			$response = array('error' => 'Error occured in ajax call.');
			return response()->json($response, $statusCode);
		}
		try {


			$scheme_id = $request->get('scheme_id');          ////parsed value
			$lot_no = $request->get('lot_no');     		     ////parsed value

			$lotDetail =  DB::table('ifms.transaction_lot')->select('lot_month', 'lot_year', 'lot_status')->where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->first();
			$lot_month = $lotDetail->lot_month;
			$lot_year = $lotDetail->lot_year;
			if ($lotDetail->lot_status == 6) {
				$lot_return = DB::select(DB::raw("select ifms.rbi_payment_status_reimport(" . $scheme_id . ",'" . $lot_no . "') as v_return"));
			} else {
				$paid_yymm = Helper::get_paid_yymm($lot_month, $lot_year);
				//echo $get_paid_yymm;
				$schemeArray=[7,6];
				if(in_array($scheme_id,$schemeArray )){
					$lot_return = DB::select(DB::raw("select ifms.rbi_payment_status_new(" . $scheme_id . ",'" . $lot_no . "'," . $paid_yymm . ") as v_return"));
				}
				else{
					$lot_return = DB::select(DB::raw("select ifms.rbi_payment_status(" . $scheme_id . ",'" . $lot_no . "'," . $paid_yymm . ") as v_return"));
				}
				
			}
			$lot_failed_count = $lot_return[0]->v_return;
			if (is_null($lot_failed_count)) {
				$response = array(
					'status' => 1, 'msg' => 'Error while RBI Report Import.',
					'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
				);
			} else {
				$response = array(
					'status' => 2, 'msg' => 'RBI Report Imported Successfully for Lot No. ' . $lot_no . '. ' . $lot_failed_count . ' RBI Failed Data collected.',
					'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
				);
			}
		} catch (\Exception $e) {
			$response = array(
				'exception' => true,
				//'exception_message' => $e->getMessage(),
				'exception_message' => 'Oops. Something wrong. Please try agian later.',
			);
			$statusCode = 400;
		} finally {
			return response()->json($response, $statusCode);
		}
	}


	public static function create_sms_lot()
	{
		$log_file_name = 'sms_log/log_sms.txt';
		$lot_details = DB::table('ifms.transaction_lot_details')->distinct()->select('drn_part', 'scheme_id')->where('sms_sent', 1)->orderBy('drn_part', 'ASC')->get();
		foreach ($lot_details as $row) {
			$scheme_id = $row->scheme_id;
			$lot_no = $row->drn_part;
			Storage::append($log_file_name, 'sms csv generated for lot_no: ' . $lot_no . ' || scheme_id : ' . $scheme_id);
			echo 'sms csv generated for lot_no: ' . $lot_no . ' || scheme_id : ' . $scheme_id;
			DB::table('ifms.transaction_lot_details')->where('drn_part', $lot_no)->where('scheme_id', $scheme_id)
				->where('is_active', '<>', 0)->where('wrongdata_flag', 0)
				->update(['sms_sent' => 9999]);
			//$var = '';
			//$var = $this->create_sms_csv($scheme_id, $lot_no);
			return self::create_sms_csv($scheme_id, $lot_no);
			/*if($var){
				echo 'sms csv generated for lot_no : '.$lot_no.' || scheme_id : '.$scheme_id;
			}*/
		}
		$time_array = DB::select(DB::raw("select to_char(now(),'DDMONYYYY-HH24MISS') as datetime"));
		$var_file_name = $time_array[0]->datetime;
		//Storage::put($log_file_name, 'Function import_rbi_list() is called on '.$var_file_name);
		//echo '<br/>Function import_rbi_list() is called on '.$var_file_name;
		Storage::append($log_file_name, '=====================================================');
		echo '<br/>=====================================================';
		Storage::append($log_file_name, 'sms csv generation complete on ' . $var_file_name);
		echo 'sms csv generation complete on ' . $var_file_name;
	}
	public static function create_sms_csv($scheme_id, $lot_no)
	{ //(Request $request){         /////parse scheme_id, lot_no

		//        $scheme_id = $request->get('scheme_id');          ////parsed value
		//		$lot_no = $request->get('lot_no');     		     ////parsed value
		/*
	$rows = DB::table('ben_export')->select('mobile_no')
	->where('drn_part',$lot_no)->where('scheme_id',$scheme_id)->where('is_active',1)->where('wrongdata_flag',0)
	->orderBy('name','ASC')->get();*/

		//$time_array = DB::select(DB::raw("select to_char(now(),'DDMONYYYY-HH24MISS') as datetime"));
		$rows = DB::select(DB::raw("SELECT mobile_no,(SELECT replace(sms_body,'xxx',repeat('X',char_length(acc_no)-4)||substring(acc_no,char_length(acc_no)-3)) as sms_content FROM sms_template WHERE sms_template.scheme_id=t.scheme_id AND sms_template.sms_month=t.lot_month)
	FROM (SELECT lot_master.scheme_id,lot_master.lot_month,ifms.transaction_lot_details.mobile_no,ifms.transaction_lot_details.acc_no FROM lot_master INNER JOIN ifms.transaction_lot_details ON lot_master.lot_no = ifms.transaction_lot_details.drn_part AND lot_master.scheme_id = ifms.transaction_lot_details.scheme_id
	WHERE ifms.transaction_lot_details.scheme_id=" . $scheme_id . " and ifms.transaction_lot_details.drn_part='" . $lot_no . "' and ifms.transaction_lot_details.is_active<>0 and ifms.transaction_lot_details.wrongdata_flag=0) as t"));

		$callback = function () use ($scheme_id, $lot_no, $rows) {
			$csv_file = 'csv_file/SMS-' . $scheme_id . '-' . $lot_no . '.csv';
			$file = fopen($csv_file, 'w');
			//fputcsv($file, $columnNames);
			foreach ($rows as $row) {
				fputcsv($file, [$row->mobile_no, $row->sms_content]);
			}
			fclose($file);
		};
		//$xmlFile->save("xml_file/".$filename.".xml");
		//$file->save("xml_file/".$fileName);
		return response()->stream($callback, 200 /*, $headers */);
		//response()->stream($callback, 200, $headers);
		//return 'done';
	}

	public function get_wrong_file_status(Request $request)
	{      /////parse scheme_id, lot_no
		//echo 'hi';
		//die;
		$scheme_id = $request->get('scheme_id');          ////parsed value
		$schemeDetail =  DB::table('m_scheme')->select('party_code')->where('id', '=', $scheme_id)->first();
		$partyCode = $schemeDetail->party_code;

		$lot_no = $request->get('lot_no');     		     ////parsed 

		//echo $partyCode;die;
		$existing_filename_details =  DB::table('lot_master')->select('file_name')->where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->first();
		$file_name = $existing_filename_details->file_name;
		$DRN_part = substr($file_name, 22);
		$list = Storage::disk('sftp_' . $partyCode)->files('ePayment_Files_003');
		//		print_r($list);
		$matchingFiles = preg_grep('/^ePayment_Files_003\/' . $file_name . '/', $list);
		/*=====changed block===*/
		$count = count($matchingFiles);
		if ($count == 0) {
			//echo $item;
			$filename = '';
		} else {
			foreach ($matchingFiles as $item) {
				$count = $count - 1;
				if ($count == 0) {
					//echo $item;
					$filename = $item;
				}
			}
		}
		/*=====changed block===*/
		//changed$filename = implode("",$matchingFiles);
		//echo $filename;
		$exists = Storage::disk('sftp_' . $partyCode)->exists($filename);
		$return_status = '';
		if ($exists) {
			//echo ' Wrong Data File Received ';

			$remote_file = Storage::disk('sftp_' . $partyCode)->get($filename);
			$fname = substr($filename, 18);
			Storage::put('ifms_xml/ifms_resp/' . $partyCode . $fname, $remote_file);
			$remote_xml_file = simplexml_load_string($remote_file);

			if (strpos($filename, 'D_') == true) {
				//echo 'Wrong Data';
				DB::table('lot_master')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['wrongdata_status' => 1]);
				DB::table('ifms.transaction_lot')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['wrongdata_status' => 1]);
				DB::table('ifms.transaction_payload')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->where('file_name', $file_name)->update(['received_ifms_error_file' => $filename]); //xml(['received_ifms_error_payload' => $remote_file]);

				$i = 0;
				foreach ($remote_xml_file as $key => $value) {
					$j = 0;
					foreach ($value as $key2[$j] => $val2[$j]) {
						//echo $key2[$j].$i.$j.'=';
						//echo $val2[$j].' ';
						if ($j == 3) {
							$acc_no = $val2[$j];
						}
						if ($j == 4) {
							$ifsc = $val2[$j];
						}
						if ($j == 5) {
							$mob_no = $val2[$j];
						}
						if ($j == 7) {
							$id = $val2[$j];
						}
						if ($j == 10) {
							DB::table('ifms.ifms_return_details')->insert(['file_name' => $filename, 'drn_part' => $DRN_part, 'scheme_id' => $scheme_id, 'ben_id' => $id, 'error_reason' => $val2[$j]]);
							$pensionidArray = DB::table('ifms.transaction_lot_details')->select('pension_id')->where('ben_id', $id)/*->where('acc_no', $acc_no)->where('ifsc', $ifsc)->where('mobile_no', $mob_no)*/->where('drn_part', $DRN_part)->where('scheme_id', $scheme_id)->get();
							$pension_id = $pensionidArray[0]->pension_id;

							//echo 'pension_id: '.$pension_id; //echo ' error reason: '.$val2[$j].'</br>';
							DB::table('pension.beneficiary')->where('id', $pension_id)->where('scheme_id', $scheme_id)/*->where('bank_code', $acc_no)->where('bank_ifsc', $ifsc)->where('mobile_no', $mob_no)*/->update(['lot_generated' => -1]);    ////Gaurav Help  substr("3161613",4);
							//18nov						DB::table('ben_export')->where('pension_id', $pension_id)->where('drn_part', $DRN_part)->where('scheme_id', $scheme_id)->where('acc_no', $acc_no)->where('ifsc', $ifsc)->where('mobile_no', $mob_no)->update(['ifms_status' => $val2[$j], 'ifms_ref_no' => 0, 'wrongdata_flag' => 1, 'updated_at' => DB::raw("now()") /*, 'paid_yymm' => 0*/]);
							DB::table('ifms.transaction_lot_details')->where('pension_id', $pension_id)->where('drn_part', $DRN_part)->where('scheme_id', $scheme_id)/*->where('acc_no', $acc_no)->where('ifsc', $ifsc)->where('mobile_no', $mob_no)*/->update(['ifms_status' => $val2[$j], 'ifms_ref_no' => 0, 'wrongdata_flag' => 1, 'updated_at' => DB::raw("now()") /*, 'paid_yymm' => 0*/]);
						}
						$j = $j + 1;
					}

					$i = $i + 1;
				}
				//echo ' Wrong Data Count: '.$i;
				DB::table('lot_master')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['ifms_wrongdata_count' => $i]);
				DB::table('ifms.transaction_lot')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['ifms_wrongdata_count' => $i]);

				$return_status =  $i . ' Wrong Data Collected ';
			} else {
				$REASON = $remote_xml_file->REASON;
				//echo 'REASON: '.$REASON;

				DB::table('lot_master')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['lot_status' => 1]);
				DB::table('ifms.transaction_lot')->where('lot_no', $DRN_part)->where('scheme_id', $scheme_id)->update(['lot_status' => 10]);
				//18nov			DB::table('ben_export')->where('drn_part', $DRN_part)->where('scheme_id', $scheme_id)->where('is_active','<>',0)->update(['ifms_status' => $REASON]);
				DB::table('ifms.transaction_lot_details')->where('drn_part', $DRN_part)->where('scheme_id', $scheme_id)->where('is_active', '<>', 0)->update(['ifms_status' => $REASON]);

				return $REASON;
			}
		} else {
			//echo ' Wrong Data File Not Exist ';
		}
		$updateCount3 = DB::statement("update ifms.temp_lot_master set rbi_sent_count=(select count(*) from ifms.transaction_lot_details where drn_part='" . $lot_no . "' and scheme_id=" . $scheme_id . " and is_active<>0 and wrongdata_flag=0)
						where scheme_id=" . $scheme_id . " and lot_no='" . $lot_no . "'");
		//echo ' update count3 : '.$updateCount3.'</br>';

		if ($return_status) {
			return $return_status;
		} else {
			return 'No Wrong Data File Received ';
		}
	}
}
