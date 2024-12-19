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

use Storage;
use Illuminate\Support\Facades\DB;

use Auth;
use App\Configduty;
use App\BankAccount;
use App\SBITransactionPayLoad;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PushToSBIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('Admin');
        // $this->middleware(['auth','MaintainMiddleware']);
    }

    public function index()
    {
        $user_id = AuthChecker::getUserId();
        $report = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
        return view('push-sbi/index', ['reports' => $report]);
    }

    public function lot_listing(Request $request)
    {
        $user_id = AuthChecker::getUserId();
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
        //  $user_id = AuthChecker::getUserId();
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
            if(empty($lot_master)){
                $response = array('status' => 2, 'msg' => 'Please generate lot.',
                'type' => 'red','icon'=>'fa fa-warning','title'=>'Error');
                 }
                 else{
                    $response = array('status' => 1,'datas' => $lot_master, 'bank_accounts' => $bank_accounts,
                    'type' => 'green','icon'=>'fa fa-check','title'=>'Success');
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
            $scheme_name = Scheme::where('id',$scheme_id)->value('passbook_narration');
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
            $shell_file = '/jaibangla/var/sbi/ePay/ToProcess/transfer.sh';
            $process = new Process('sh ' . $shell_file . ' ' . $lot_info->debit_reference . '.xml');
            $process->run();

            // executes after the command finishes
            if (!$process->isSuccessful()) {
                $input = [
                    'lot_status' => 10
                ];
                $is_updated = SBITransactionLot::where('lot_no', $DRN_part)
                    ->where('scheme_id', $scheme_id)->update($input);
                $response = array('status' => 2, 'msg' => 'File not signed.Please Try again later',
                'type' => 'red','icon'=>'fa fa-warning','title'=>'Error');
            } else {
                $response = array('status' => 1,'msg'=>'Files has been signed and will be sent to SBI server in next cycle.',
                 'type' => 'green','icon'=>'fa fa-check','title'=>'Success');
            }
        } 
        catch (\Exception $e) {
            $response = array(
                'exception' => true,
                //'exception_message' => $e->getMessage(),
                'exception_message' => 'Oops. Something wrong. Please try agian later.',
            );
            $statusCode = 400;
        }
         finally {
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
           
            $response = array('status' => 1,'msg'=>'DB compiled successfully. All Actions completed for the lot-'.$request->lot_no,
            'type' => 'green','icon'=>'fa fa-check','title'=>'Success');
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
}
