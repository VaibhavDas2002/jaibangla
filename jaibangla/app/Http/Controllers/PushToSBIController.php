<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon;
use App\Scheme;
use DOMDocument;
use App\lot_master;
use App\ben_lot_month;

use App\Classes\DigestAlgorithmType;
use App\Classes\XmlSigner;

use App\SBITransactionLot;
use App\SBITransactionLotDetails;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\BankAccount;
use App\Helpers\Helper;
use App\SBITransactionPayLoad;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PushToSBIController extends Controller
{
    public $sbi_sftp_server;
    public function __construct()
    {
        $this->middleware('auth');
        set_time_limit(0);
        //$this->middleware('Admin');
        // $this->middleware(['auth','MaintainMiddleware']);
        $sbi_prod_server = Helper::getSBISftpServer();
        $this->sbi_sftp_server = $sbi_prod_server;
    }

    public function index()
    {
        $user_id = Auth::user()->id;
        $report = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
        return view('push-sbi/index', ['reports' => $report]);
    }

    public function lot_listing(Request $request)
    {
        $user_id = Auth::user()->id;
        //$schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->first();
        $lot_master = SBITransactionLot::where('scheme_id', $request->select_scheme)
            ->leftJoin('public.m_scheme', 'public.m_scheme.id', 'transaction_lot.scheme_id')
            ->orderBy('transaction_lot.created_at', 'desc')->paginate(10);
        $bank_accounts = BankAccount::where('scheme_id', $request->select_scheme)->get();

        if (empty($lot_master)) {
            return redirect("/")->with('success', 'PLEASE GENERATE LOT ');
        }

        return view('push-sbi/lot_listing', ['datas' => $lot_master, 'bank_accounts' => $bank_accounts]);
    }

    public function push_single_lot(Request $request)
    {
        //  $user_id = Auth::user()->id;
        // // $schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->first();
        //  $lot_master = SBITransactionLot::where('scheme_id',$request->scheme_id)->where('lot_no',$request->lot_no)
        //              ->leftJoin('public.m_scheme','public.m_scheme.id','transaction_lot.scheme_id')
        //              ->orderBy('transaction_lot.created_at','desc')->paginate(1);
        //  $bank_accounts = BankAccount::where('scheme_id',$request->scheme_id)->get();

        //  if(empty($lot_master)){
        //      return redirect("/")->with('success','PLEASE GENERATE LOT ');
        //  }

        //  return view('push-sbi/lot_listing', ['datas'=>$lot_master,'bank_accounts'=>$bank_accounts]);
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {

            $lot_master = SBITransactionLot::where('scheme_id', $request->scheme_id)
                ->where('lot_no', $request->lot_no)
                ->leftJoin('public.m_scheme', 'public.m_scheme.id', 'transaction_lot.scheme_id')
                ->orderBy('transaction_lot.created_at', 'desc')->paginate(1);
            $bank_accounts = BankAccount::where('scheme_id', $request->scheme_id)->get();
            if (empty($lot_master)) {
                $response = array(
                    'status' => 2, 'msg' => 'Please generate lot.',
                    'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
                );
            } else {
                $response = array(
                    'status' => 1, 'datas' => $lot_master, 'bank_accounts' => $bank_accounts,
                    'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                );
            }
        } catch (\Exception $e) {
            $response = array(
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' => 'Oops. Something wrong. Please try agian later.',
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function showlist(Request $request)
    {
        $lot_no = $request->lot_no;
        $ben_lot_month = ben_lot_month::where('drn_part', $lot_no)->get();
        return view('linelisting_showlist', ['datas' => $ben_lot_month]);
    }


    public function prepareSignXML(Request $request)
    {
        //$statusCode = 400;
        //    $response = array('error' => 'Lot signing is temporarily suspended, please try after some time.');
        //    return response()->json($response, $statusCode);
        //echo 1;die;
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {

            $scheme_id = $request->get('scheme_id');
            $DRN_part = $request->get('lot_no');
            $scheme_name = Scheme::where('id', $scheme_id)->value('passbook_narration');
            //Update Lot Status
            list($bank_account_no, $ifsc_code) = preg_split("~:~", $request->bank_account);
            $input = [
                'ifsc_code_debit' => $ifsc_code,
                'account_debit' => $bank_account_no,
                'lot_status' => 1
            ];

            $is_updated = SBITransactionLot::where('lot_no', $DRN_part)
                ->where('scheme_id', $scheme_id)->update($input);

            $lot_info = SBITransactionLot::where('lot_no', $DRN_part)
                ->where('scheme_id', $scheme_id)->first();

            $xmlFile = new DOMDocument("1.0", 'UTF-8');
            $xmlFile->formatOutput = true;
            $xmlFile->xmlStandalone = false;
            $state_gov_payments = $xmlFile->createElement("STATE_GOVT_PAYMENTS");
            $xmlFile->appendChild($state_gov_payments);
            $debit_account = $xmlFile->createElement("DEBIT_ACCOUNT");
            $debit_account->setAttribute("ACCOUNT_DEBIT", $lot_info->account_debit);
            $debit_account->setAttribute("BANK_NAME", "STATE BANK OF INDIA");
            $debit_account->setAttribute("CREDIT_COUNT", $lot_info->credit_count);
            $debit_account->setAttribute("DEBIT_AMOUNT", $lot_info->debit_amount);
            $debit_account->setAttribute("DEBIT_REFERENCE", $lot_info->debit_reference);
            $debit_account->setAttribute("IFSC_CODE_DEBIT", $lot_info->ifsc_code_debit);
            $debit_account->setAttribute("TRAN_DATE", $lot_info->tran_date);
            $debit_account->setAttribute("AGENCY_DR_REF", $lot_info->agency_dr_ref);
            $debit_account->setAttribute("DEBIT_NARRATION", $lot_info->debit_narration);
            $debit_account->setAttribute("STATE", "WB");
            $debit_account->setAttribute("EMAIL", "finance@gov.in");
            $state_gov_payments->appendChild($debit_account);

            $credit_accounts = $xmlFile->createElement("CREDITACCOUNTS");
            $debit_account->appendChild($credit_accounts);

            $ben_data = SBITransactionLotDetails::where('lot_no', $DRN_part)
                ->where('scheme_id', $scheme_id)
                // ->where('is_active', '!=','-97')
                // ->where('is_active', '=','1')
                ->get();

            //echo count( $ben_data);die;
            foreach ($ben_data as $details) {
                $credit_account = $xmlFile->createElement("CREDIT_ACCOUNT");
                $credit_account->setAttribute("ACCOUNT_CREDIT", trim($details->account_credit));
                $credit_account->setAttribute("CREDIT_AMOUNT", $details->credit_amount);
                $credit_account->setAttribute("CREDIT_REFERENCE", trim($details->credit_reference));
                $credit_account->setAttribute("IFSC_CODE_CREDIT", trim($details->ifsc_code_credit));
                $credit_account->setAttribute("NAME", substr(trim($details->name), 0, 50));
                $credit_account->setAttribute("PAYMENT_MODE", "A");
                $credit_account->setAttribute("NPCI_USER_ID", trim($details->npci_user_id));
                $credit_account->setAttribute("NPCI_USER_NAME", trim($details->npci_user_name));
                $credit_account->setAttribute("AGENCY_CR_REF", trim($details->agency_cr_ref));
                $credit_account->setAttribute("NARRATION", substr(trim($scheme_name), 0, 20));
                $credit_accounts->appendChild($credit_account);
                $credit_account = null;
            }

            //echo "<xmp>".$xmlFile->saveXML()."</xmp>";
            $unsigned_xml_file = storage_path('app/sbi/unsigned/') . $lot_info->debit_reference . ".xml";
            //echo $spath;
            $xmlFile->save($unsigned_xml_file);

            $signed_xml_file = storage_path('app/sbi/signed/') . $lot_info->debit_reference . ".xml";
            $cert = storage_path('app/cert/') . "certificate.pfx";
            //$cert_pem=storage_path('app\\cert\\')."test.pem";
            $xmlSigner = new XmlSigner();
            $xmlSigner->loadPfxFile($cert, 'JB@WBFin#1984');
            // or load a PEM file
            //$xmlSigner->loadPrivateKeyFile($cert_pem, 'test@123');
            // Optional: Set reference URI
            $xmlSigner->setReferenceUri('');
            $xmlSigner->signXmlFile($unsigned_xml_file, $signed_xml_file, DigestAlgorithmType::SHA1);

            /*$xml_response = file_get_contents($signed_xml_file);
            $xml = simplexml_load_string($xml_response);
            //Save Payload
            $payLoad= new SBITransactionPayLoad();
            $payLoad->lot_no = $lot_info->lot_no;
            $payLoad->scheme_id = $lot_info->scheme_id;
            $payLoad->debit_reference = $lot_info->debit_reference;
            $payLoad->sent_payload = $xml;
            $payLoad->save();*/
            //Storage::disk('sftp_sbi')->put('ePay/ToProcess/'.$signed_xml_file);  

            // $shell_file = '/jaibangla/var/sbi/ePay/ToProcess/transfer.sh';
            // $process = new Process('sh ' . $shell_file . ' ' . $lot_info->debit_reference . '.xml');
            // $process->run();

            // New Code for DDO end 29-02-2024
            // Transfer file to Picked
            $file_name = $lot_info->debit_reference . '.xml';
            rename(storage_path('app/sbi/signed/') . '//' . $file_name, storage_path('app/sbi/ePay/ToProcess/') . '//' . $file_name);

            // executes after the command finishes
            // !$process->isSuccessful()
            if (file_exists(storage_path('app/sbi/ePay/ToProcess/') . '//' . $file_name)) {
                $response = array(
                    'status' => 1, 'msg' => 'Files has been signed and will be sent to SBI server in next cycle.',
                    'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                );
            } else {
                $input = [
                    'lot_status' => 10
                ];
                $is_updated = SBITransactionLot::where('lot_no', $DRN_part)
                    ->where('scheme_id', $scheme_id)->update($input);
                $response = array(
                    'status' => 2, 'msg' => 'File not signed.Please Try again later',
                    'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
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
        //echo $process->getOutput();
        // return redirect("report-lot-master-sbi/index")->with('success','File has been signed and will be sent to SBI server in next cycle.');


    }

    public function signXML(Request $request)
    {
        $xml_file = storage_path('app\\sbi\\') . "DBTAABBB141120165801.xml";
        $signed_xml_file = storage_path('app\\sbi\\') . "DBTAABBB141120165801_S2.xml";
        $cert = storage_path('app\\cert\\') . "test.pfx";
        $cert_pem = storage_path('app\\cert\\') . "test.pem";
        $xmlSigner = new XmlSigner();

        $xmlSigner->loadPfxFile($cert, 'test@123');

        // or load a PEM file
        //$xmlSigner->loadPrivateKeyFile($cert_pem, 'test@123');

        // Optional: Set reference URI
        $xmlSigner->setReferenceUri('');

        $xmlSigner->signXmlFile($xml_file, $signed_xml_file, DigestAlgorithmType::SHA256);
    }

    public function prepareSignXML_test(Request $request)
    {
        $lot_no = $request->get('lot_no');
        $scheme_id = $request->get('scheme_id');
        DB::statement("update sbi.transaction_lot set lot_status=1 where lot_no='" . $lot_no . "' and scheme_id=" . $scheme_id);
        return redirect("/report_lot_master_sbi")->with('success', 'Lot No. ' . $lot_no . ' has been pushed to SBI');
    }
    public function receive_sbi_ack_status_test(Request $request)
    {
    }

    public function sbi_payment_status_test(Request $request)
    {
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in ajax call.');
            return response()->json($response, $statusCode);
        }


        $debit_ref = $request->debit_ref;
        $scheme_id = $request->scheme_id;
        $lot_no = $request->lot_no;
        DB::beginTransaction();
        try {
            $fun_return = DB::select("select sbi.update_lot_master_payment_status_summary(" . $scheme_id . ",'" . $lot_no . "')");

            DB::statement("update sbi.transaction_lot set lot_status = 6 where debit_reference='" . $debit_ref . "' and lot_status=5");
            DB::commit();

            $response = array(
                'status' => 1, 'msg' => 'DB compiled successfully. All Actions completed for the lot-' . $request->lot_no,
                'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
            );
        } catch (\Exception $e) {
            $response = array(
                'exception' => true,
                //'exception_message' => $e->getMessage(),
                'exception_message' => 'Oops. Something wrong. Please try agian later.',
            );
            $statusCode = 400;
            DB::rollback();

            //return redirect("/report-lot-master-sbi/index")->with('message', $response['exception_message']);
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    /* New structure for DDO end added on - 29-02-2024 */
    // Send to SBI for payment
    public function pushToSBIPaymentLot(Request $request)
    {
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {
            $debit_ref = $request->debit_ref;
            $scheme_id = $request->scheme_id;
            $lot_no = $request->lot_no;
            $file_name = $debit_ref . '.xml';
            $storagePath = 'app/sbi/ePay/ToProcess/' . $file_name;
            $data_exists = SBITransactionLot::where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->where('debit_reference', $debit_ref)->where('lot_status', 1)->count();
            if ($data_exists > 0) {
                if (file_exists(storage_path('app/sbi/ePay/ToProcess/') . '//' . $file_name)) {
                    // $av_file = Storage::get($storagePath);
                    $payment_file_content = file_get_contents(storage_path($storagePath));
                    Storage::disk($this->sbi_sftp_server)->put('ePay/ToProcess/' . $file_name, $payment_file_content);  ///////uncomment in production
                    $exists = Storage::disk($this->sbi_sftp_server)->exists('ePay/ToProcess/' . $file_name);  ///////uncomment in production
                    if ($exists) {
                        DB::beginTransaction();
                        // Updates in DB
                        $push_to_sbi_status = SBITransactionLot::where('debit_reference', $debit_ref)->where('scheme_id', $scheme_id)->where('lot_no', $lot_no)->where('lot_status', 1)->update(['lot_status' => 2, 'pushed_at' => date('Y-m-d H:i:s')]);
                        SBITransactionPayLoad::where('debit_reference', $debit_ref)->delete();
                        $push_trans_payload = SBITransactionPayLoad::insert(['lot_no' => $lot_no, 'created_at' => date('Y-m-d H:i:s'), 'scheme_id' => $scheme_id, 'status' => 2, 'debit_reference' => $debit_ref, 'sent_payload' => $payment_file_content]);

                        if ($push_to_sbi_status && $push_trans_payload) {
                            DB::commit();
                            // Transfer file to Picked
                            rename(storage_path('app/sbi/ePay/ToProcess/') . '//' . $file_name, storage_path('app/sbi/ePay/ToProcess/Picked/') . '//' . $file_name);
                            $response = array(
                                'status' => 1, 'msg' => 'Lot No:- <b>' . $lot_no . '</b> has been pushed to SBI successfully.',
                                'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                            );
                        } else {
                            DB::rollback();
                            $response = array(
                                'status' => 2, 'msg' => 'Status update error for Lot - <b>' . $lot_no . '</b> Please Try again later.',
                                'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
                            );
                        }
                    } else {
                        $response = array(
                            'status' => 3, 'msg' => 'Lot <b>' . $lot_no . '</b> has not been pushed. Please try after sometime.',
                            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
                        );
                    }
                } else {
                    $response = array(
                        'status' => 4, 'msg' => 'Payment file not found for Lot - <b>' . $lot_no . '</b>',
                        'type' => 'green', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                    );
                }
            } else {
                $response = array(
                    'status' => 4, 'msg' => 'Not found in DB Lot - <b>' . $lot_no . '</b>',
                    'type' => 'green', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                );
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response = array(
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' => 'Please try once agian..',
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function reciveAcknowledgementSBIPaymentLot(Request $request)
    {
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {
            $debit_ref = $request->debit_ref;
            $scheme_id = $request->scheme_id;
            $lot_no = $request->lot_no;
            $ackfile_name = $debit_ref . '_ACK.xml';
            $data_exists = SBITransactionLot::select('ack_status_code')->where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->where('debit_reference', $debit_ref)->where('lot_status', 2)->whereNull('ack_status_code')->first();
            if (is_null($data_exists->ack_status_code)) {
                $exists = Storage::disk($this->sbi_sftp_server)->exists('ePay/Acknowledgement/' . $ackfile_name);  ///////uncomment in production
                if ($exists) {
                    $remote_file = Storage::disk($this->sbi_sftp_server)->get('ePay/Acknowledgement/' . $ackfile_name);  //// uncomment in production
                    Storage::put('sbi/ePay/Acknowledgement/' . $ackfile_name, $remote_file);  //// uncomment in production

                    $remote_xml_file = simplexml_load_string($remote_file);
                    $ack_remarks = $remote_xml_file->DEBIT_ACCOUNT['ACK_REMARKS'];
                    $file_ack_status_code = $remote_xml_file->DEBIT_ACCOUNT['ACK_STATUS_CODE'];
                    $av_ack_file_content = file_get_contents(storage_path('app/sbi/ePay/Acknowledgement/' . $ackfile_name));
                    // dump($remote_xml_file, $file_ack_status_code, $ack_remarks); die;
                    DB::beginTransaction();
                    if ($file_ack_status_code == '000') {
                        // Updates in DB
                        $ack_update_status = SBITransactionLot::where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->where('debit_reference', $debit_ref)->where('lot_status', 2)->update(['lot_status' => 3, 'ack_status_code' => $file_ack_status_code]);
                        $ack_payload_update = SBITransactionPayLoad::where('debit_reference', $debit_ref)->where('scheme_id', $scheme_id)->update(['updated_at' => date('Y-m-d H:i:s'), 'ack_payload' => $av_ack_file_content, 'status' => 3]);
                    } else {
                        // Updates in DB
                        $ack_update_status = SBITransactionLot::where('debit_reference', $debit_ref)->where('scheme_id', $scheme_id)->update(['ack_status_code' => $file_ack_status_code]);
                        $ack_payload_update = SBITransactionPayLoad::where('debit_reference', $debit_ref)->where('scheme_id', $scheme_id)->update(['updated_at' => date('Y-m-d H:i:s'), 'ack_payload' => $av_ack_file_content]);
                    }

                    if ($ack_update_status && $ack_payload_update) {
                        DB::commit();
                        // Transfer file to Picked
                        rename(storage_path('app/sbi/ePay/Acknowledgement/') . '//' . $ackfile_name, storage_path('app/sbi/ePay/Acknowledgement/Picked/') . '//' . $ackfile_name);
                        if ($file_ack_status_code == '000') {
                            $response = array(
                                'status' => 1, 'msg' => 'Lot No:- <b>' . $lot_no . '</b> acknowledgement has been received from SBI successfully.',
                                'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Acknowledgement ' . $ack_remarks
                            );
                        } else {
                            $response = array(
                                'status' => 3, 'msg' => 'Acknowledgement error from SBI for Lot - <b>' . $lot_no . '</b> with Remarks - ' . $ack_remarks,
                                'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Acnowledgement Error!!'
                            );
                        }
                    } else {
                        DB::rollback();
                        $response = array(
                            'status' => 2, 'msg' => 'Acknowledgement Status update error. Please Try again later.',
                            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
                        );
                    }
                } else {
                    $response = array(
                        'status' => 4, 'msg' => 'Acknowledgement file is not generated in SBI server for Lot - <b>' . $lot_no . '</b>',
                        'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Acknowledgement Pending'
                    );
                }
            } else {
                $response = array(
                    'status' => 5, 'msg' => 'Acknowledgement file already came form SBI for Lot - <b>' . $lot_no . '</b>',
                    'type' => 'green', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                );
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
                // 'exception_message' => 'Oops. Something wrong. Please try agian later.',
            );
            $statusCode = 400;
            DB::rollback();
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function reciveResponseSBIPaymentLot(Request $request)
    {
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {
            $debit_ref = $request->debit_ref;
            $scheme_id = $request->scheme_id;
            $lot_no = $request->lot_no;

            $respfile_name = $debit_ref . '_RESP.xml';
            $data_exists = SBITransactionLot::where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->where('debit_reference', $debit_ref)->where('lot_status', 3)->where('ack_status_code', '000')->first();
            if ($data_exists->ack_status_code == '000' && $data_exists->lot_status == 3) {
                $exists = Storage::disk($this->sbi_sftp_server)->exists('ePay/Response/' . $respfile_name);  ///////uncomment in production
                if ($exists) {
                    $remote_file = Storage::disk($this->sbi_sftp_server)->get('ePay/Response/' . $respfile_name);  //// uncomment in production
                    Storage::put('sbi/ePay/Response/' . $respfile_name, $remote_file);  //// uncomment in production
                    if (file_exists(storage_path('app/sbi/ePay/Response/') . '//' . $respfile_name)) {
                        $av_resp_file_content = file_get_contents(storage_path('app/sbi/ePay/Response/' . $respfile_name));

                        DB::beginTransaction();
                        $ack_payload_update = SBITransactionPayLoad::where('debit_reference', $debit_ref)->where('scheme_id', $scheme_id)->update(['received_payload' => $av_resp_file_content]);
                        $update_payment_fun = DB::connection('pgsql')->select("SELECT sbi.update_payment_status('" . $debit_ref . "');");
                        $update_payment_status_res = $update_payment_fun[0]->update_payment_status;

                        if ($update_payment_status_res == 0 && $ack_payload_update == 1) {
                            DB::commit();
                            // Transfer file to Picked
                            rename(storage_path('app/sbi/ePay/Response/') . '//' . $respfile_name, storage_path('app/sbi/ePay/Response/Picked/') . '//' . $respfile_name);
                            $response = array(
                                'status' => 1, 'msg' => 'Lot No:- <b>' . $lot_no . '</b> Response has been received from SBI successfully.',
                                'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Response Complete'
                            );
                        } else {
                            DB::rollback();
                            $response = array(
                                'status' => 2, 'msg' => 'Response Status update error for Lot - <b>' . $lot_no . '. Please Try again later.',
                                'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
                            );
                        }
                    } else {
                        $response = array(
                            'status' => 3, 'msg' => 'File not fetched from SBI server for Lot - <b>' . $lot_no . '</b>',
                            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                        );
                    }
                } else {
                    $response = array(
                        'status' => 4, 'msg' => 'Response file is not generated in SBI server for Lot - <b>' . $lot_no . '</b>',
                        'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Response Pending'
                    );
                }
            } else {
                $response = array(
                    'status' => 5, 'msg' => 'Response file already came form SBI for Lot - <b>' . $lot_no . '</b>',
                    'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Complete'
                );
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response = array(
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' => 'Oops. Something wrong. Please try agian later.',
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function importResponseSBIPaymentLot(Request $request)
    {
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {
            $debit_ref = $request->debit_ref;
            $scheme_id = $request->scheme_id;
            $lot_no = $request->lot_no;
            $data_exists = SBITransactionLot::where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->where('debit_reference', $debit_ref)->where('lot_status', 4)->get();
            if (count($data_exists) > 0) {
                DB::beginTransaction();
                $import_fun = DB::connection('pgsql')->select("SELECT sbi.import_sbi_lot_compile_standard(".$scheme_id.",'".$lot_no."','".$debit_ref."');");
                $import_res = $import_fun[0]->import_sbi_lot_compile_standard;
                $import_summary_fun = DB::connection('pgsql')->select("SELECT sbi.update_lot_master_payment_status_summary(".$scheme_id.",'".$lot_no."');");
                $import_summary_res = $import_summary_fun[0]->update_lot_master_payment_status_summary;

                if ($import_res == 1 && $import_summary_res == 1) {
                    DB::commit();
                    $response = array(
                        'status' => 1, 'msg' => 'Lot No:- <b>' . $lot_no . '</b> Payment status imported successfully.',
                        'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Response Complete'
                    );
                } else {
                    DB::rollback();
                    $response = array(
                        'status' => 2, 'msg' => 'Response Status update error for Lot - <b>' . $lot_no . '. Please Try again later.',
                        'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
                    );
                }
            } else {
                $response = array(
                    'status' => 5, 'msg' => 'Import Payment Response is already completed Lot - <b>' . $lot_no . '</b>',
                    'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Complete'
                );
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response = array(
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' => 'Oops. Something wrong. Please try agian later.',
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }
}
