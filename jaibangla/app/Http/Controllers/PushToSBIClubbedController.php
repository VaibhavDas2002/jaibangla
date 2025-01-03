<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon;
use App\Scheme;
use DOMDocument;
use App\lot_master;
use App\ben_lot_month;
use App\Helpers\Helper;

use App\Classes\DigestAlgorithmType;
use App\Classes\XmlSigner;

use App\SBITransactionLot;
use App\SBITransactionLotDetails;

use App\ClubbedSBITransactionLot;
use App\ClubbedSBITransactionLotDetails;

use Storage;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\BankAccount;
use App\SBITransactionPayLoad;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Helpers\AuthChecker;


class PushToSBIClubbedController extends Controller
{
	public function __construct() 
    {
       // $this->middleware('auth');
	$this->middleware(['auth','MaintainMiddleware']);
        set_time_limit(200);
        //$this->middleware('Admin');
    }
    /* Clubbed Lot Transaction SBI */
    public function clubbedIndex(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->first();
        $scheme_id = $schemes->scheme_id;
        
        $report = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=".$user_id.")"));
        return view('report-lot-master-sbi/clubbed_index',['reports' => $report]);
    }

    public function clubbedLotListing(Request $request)
    {
        $scheme_id = $request->select_scheme;//new
        $lot_year = $request->lot_year;//new
        $lot_month = $request->lot_month;//new
        /*$report = DB::select(DB::raw("select s.scheme_name, l.scheme_id,l.lot_no,l.lot_year,l.lot_month,l.credit_count,l.lot_status,l.success_count,l.failed_count,l.amount_debit,l.debit_reference 
        from sbi.clubbed_transaction_lot l,m_scheme s where l.scheme_id = ".$scheme_id." and lot_year = '".$lot_year."' and lot_month = '".$lot_month."' and l.scheme_id=s.id order by l.lot_no DESC"));*/
        /* ------------- 29-03-2021------------------ */ 
        $report = DB::select(DB::raw("select s.scheme_name,tl.scheme_id,tl.lot_no,tl.lot_year,
            tl.lot_month,tl.credit_count,tl.lot_status,tl.success_count,
            tl.failed_count,tl.amount_debit,tl.debit_reference,
            (select 
            array_agg('[' || l.lot_no || ',' || l.lot_month || ',' || case when l.repeat_drn_part is null then 'Not Repeated ' else 'Repeated ' end || ']')
            from lot_master l where l.lot_no::int in
            (select unnest(ctl.child_lot_group) from sbi.clubbed_transaction_lot ctl where ctl.lot_no=tl.lot_no)) as child_lot 
            from sbi.clubbed_transaction_lot tl, m_scheme s 
            where tl.scheme_id = ".$scheme_id." and tl.lot_year = '".$lot_year."' and tl.lot_month = '".$lot_month."' and tl.scheme_id=s.id order by tl.lot_no "));

        return view('report-lot-master-sbi/clubbed_lot_listing', ['reports' => $report]);
    }

    /* 
        Push To SBI Here Bank Account Also Selected => Clubbed Lot
     */
    public function clubbed_push_single_lot(Request $request)
    {
        $user_id = AuthChecker::getUserId();
       // $schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->first();
        $lot_master = ClubbedSBITransactionLot::where('scheme_id',$request->scheme_id)->where('lot_no',$request->lot_no)
                    ->leftJoin('public.m_scheme','public.m_scheme.id','clubbed_transaction_lot.scheme_id')
                    ->orderBy('clubbed_transaction_lot.created_at','desc')->paginate(1);
        $bank_accounts = BankAccount::where('scheme_id',$request->scheme_id)->get();
        
        if(empty($lot_master)){
            return redirect("/")->with('success','PLEASE GENERATE LOT ');
        }
        
        return view('push-sbi/clubbed_push_lot_listing', ['datas'=>$lot_master,'bank_accounts'=>$bank_accounts]);   
    }

    /*
        End Push To SBI Clubbed Lot
    */

    public function showlist(Request $request)
    {
        $lot_no=$request->lot_no;
        $ben_lot_month = ClubbedSBITransactionLotDetails::where('lot_no',$lot_no)->get();
        return view('linelisting_showlist', ['datas'=>$ben_lot_month]);
    }

    /*
        -- Marged Lot Signed and Pushed To SBI
    */
    public function clubbed_prepareSignXML(Request $request){
        $scheme_id = $request->get('scheme_id');        
        $DRN_part = $request->get('lot_no');        
        //$party = Scheme::find($scheme_id)->first();
        //Update Lot Status
        list($bank_account_no,$ifsc_code)=preg_split("~:~", $request->bank_account);
        $input = [
            'ifsc_code_debit' => $ifsc_code,
            'account_debit' => $bank_account_no,
            'lot_status' => 1
        ];
        $is_updated=ClubbedSBITransactionLot::where('lot_no',$DRN_part)
                ->where('scheme_id',$scheme_id)->update($input); 

        $lot_info=ClubbedSBITransactionLot::where('lot_no',$DRN_part)
                ->where('scheme_id',$scheme_id)->first();

        $xmlFile = new DOMDocument("1.0",'UTF-8');
        $xmlFile->formatOutput=true;
        $xmlFile->xmlStandalone = false;
        $state_gov_payments = $xmlFile->createElement("STATE_GOVT_PAYMENTS");
        $xmlFile->appendChild($state_gov_payments);
        $debit_account = $xmlFile->createElement("DEBIT_ACCOUNT");
        $debit_account->setAttribute("ACCOUNT_DEBIT", $lot_info->account_debit);
        $debit_account->setAttribute("BANK_NAME", "STATE BANK OF INDIA");
        $debit_account->setAttribute("CREDIT_COUNT", $lot_info->credit_count);
        $debit_account->setAttribute("DEBIT_AMOUNT",$lot_info->debit_amount);
        $debit_account->setAttribute("DEBIT_REFERENCE",$lot_info->debit_reference);
        $debit_account->setAttribute("IFSC_CODE_DEBIT",$lot_info->ifsc_code_debit);
        $debit_account->setAttribute("TRAN_DATE",$lot_info->tran_date);
        $debit_account->setAttribute("AGENCY_DR_REF",$lot_info->agency_dr_ref);
        $debit_account->setAttribute("DEBIT_NARRATION",$lot_info->debit_narration);
        $debit_account->setAttribute("STATE","WB");
        $debit_account->setAttribute("EMAIL","finance@gov.in");        
        $state_gov_payments->appendChild($debit_account);

        $credit_accounts= $xmlFile->createElement("CREDITACCOUNTS");
        $debit_account->appendChild($credit_accounts);

        $ben_data= ClubbedSBITransactionLotDetails::where('lot_no',$DRN_part)
                    ->where('scheme_id',$scheme_id)->get();
        foreach($ben_data as $details){
            $credit_account= $xmlFile->createElement("CREDIT_ACCOUNT");
            $credit_account->setAttribute("ACCOUNT_CREDIT", trim($details->account_credit));
            $credit_account->setAttribute("CREDIT_AMOUNT", $details->credit_amount );
            $credit_account->setAttribute("CREDIT_REFERENCE", trim($details->credit_reference));
            $credit_account->setAttribute("IFSC_CODE_CREDIT", trim($details->ifsc_code_credit));
            $credit_account->setAttribute("NAME", substr(trim($details->name),0,50));
            $credit_account->setAttribute("PAYMENT_MODE", "A");            
            $credit_account->setAttribute("NPCI_USER_ID", trim($details->npci_user_id));
            $credit_account->setAttribute("NPCI_USER_NAME", trim($details->npci_user_name));
            $credit_account->setAttribute("AGENCY_CR_REF", trim($details->agency_cr_ref));            
            $credit_accounts->appendChild($credit_account);
            $credit_account=null;           
        }

        //echo "<xmp>".$xmlFile->saveXML()."</xmp>";
        $unsigned_xml_file=storage_path('app/sbi/unsigned/').$lot_info->debit_reference.".xml";
        //echo $spath;
        $xmlFile->save($unsigned_xml_file);

        $signed_xml_file=storage_path('app/sbi/signed/').$lot_info->debit_reference.".xml";
        $cert=storage_path('app/cert/')."certificate.pfx";
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
        $shell_file='/jaibangla/var/sbi/ePay/ToProcess/transfer.sh';
        $process = new Process('sh '.$shell_file.' '.$lot_info->debit_reference.'.xml');
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            $input = [
               'lot_status' => 10
            ];
            $is_updated=ClubbedSBITransactionLot::where('lot_no',$DRN_part)
                ->where('scheme_id',$scheme_id)->update($input);
            return redirect("/")->with('success','File not signed.Please Try again later.');
            //throw new ProcessFailedException($process);
        }      

        //echo $process->getOutput();
        return redirect("/")->with('success','File has been signed and will be sent to SBI server in next cycle.');

    }
    /*
        --  End Marged Lot Signed and Pushed To SBI
    */

    public function prepareSignXML_test(Request $request){ 
		$lot_no = $request->get('lot_no');
		$scheme_id = $request->get('scheme_id');
		DB::statement("update sbi.clubbed_transaction_lot set lot_status=1 where lot_no='".$lot_no."' and scheme_id=".$scheme_id);
		return redirect("/clubbed-report-lot-master-sbi/index")->with('success','Lot No. '.$lot_no.' has been pushed to SBI');
	}
	
	/*
        Final Response Compile into DB => Clubbed Lot
    */
    public function clubbed_sbi_payment_status_test(Request $request){
        $debit_ref=$request->debit_ref;
        $lot_month = $request->lot_month;
        $lot_year=$request->lot_year;
        $scheme_id = $request->scheme_id;
        $paid_yymm = $this->get_paid_yymm(trim($lot_month), trim($lot_year));
        $paid_count = ClubbedSBITransactionLot::where('debit_reference',$debit_ref)->first();

        /* Get Finacial Year For Append */
        $table_year = Helper::getConvertedfinYear($paid_count->lot_year);
        /*
		Update Clubbed Transaction Lot (Success, Failed) From Clubbed Transaction Lot Details
        */
        DB::statement("update sbi.clubbed_transaction_lot tl set success_count=t1.scnt, failed_count=t1.ercnt,amount_debit=t1.scnt*100000*".$paid_count->no_of_month."
                        from (select debit_reference,
                                sum(case when substr(status_code,1,1)='S' then 1 else 0 end) scnt,
                                sum(case when substr(status_code,1,1)='E' then 1 else 0 end) ercnt
                                from sbi.clubbed_transaction_lot_details where debit_reference='".$debit_ref."' group by debit_reference) t1
                        where tl.debit_reference=t1.debit_reference and tl.scheme_id=".$scheme_id." and lot_no='".$request->lot_no."' ");
        $status=0;
        $status=DB::table('sbi.clubbed_transaction_lot')->select('lot_no')->where('lot_no',$request->lot_no)->where('lot_status',4)->get();
        if($status->count()>0)
        {	
        	/* 
        	1. Update Paid YYMM at the Clubbed transaction Lot Details 
			2. Change Lot Status = 5 in the Clubbed Transaction Lot
			3. Update Lot Master According to the Clubbed Transaction Lot
        	*/
            DB::statement("update sbi.clubbed_transaction_lot_details set paid_yymm = ".$paid_yymm." where debit_reference='".$debit_ref."' and paid_yymm>0 and substr(status_code,1,1)='S'");
            DB::statement("update sbi.clubbed_transaction_lot set lot_status = 5 where debit_reference='".$debit_ref."' and lot_status=4");
            DB::statement("update lot_master lm set ref_no=t.lot_status,rbi_success_count=t.success_count,rbi_failed_count=t.failed_count from (select lot_no,lot_status,success_count,failed_count from sbi.clubbed_transaction_lot where debit_reference='".$debit_ref."') t where t.lot_no=lm.lot_no");
        }

        /*
        Calculate Group Child Lot Number of Clubbed Lot from Clubbed Transaction Lot
        */
        $child_group = $paid_count->child_lot_group;
    	$a = explode('{', $child_group);
    	$b = explode('}', $a[1]);
    	$child = explode(',', $b[0]);
    	$child_lot = '';
    	foreach ($child as $k) {
    		$child_lot .= "'".$k."',";
    	}
    	$group_child_no = rtrim($child_lot, ",");

    	/* 
    	Update Chile Lot Number from The Clubbed Lot Number at 
    	Clubbed Transaction Lot => Transactin Lot 
    	*/
        DB::statement("update sbi.transaction_lot tl set 
        	success_count=t1.success_count, failed_count=t1.failed_count,
			amount_debit=t1.success_count*100000, debit_reference=t1.debit_reference, lot_status=t1.lot_status, ifsc_code_debit=t1.ifsc_code_debit, account_debit=t1.account_debit, debit_status_code=t1.debit_status_code, debit_journal=t1.debit_journal, ack_status_code=t1.ack_status_code
			from (select * from sbi.clubbed_transaction_lot tl where tl.lot_no='".$request->lot_no."')t1
			where tl.lot_no in (".$group_child_no.")");
        
        /*
		1. Update Transaction Lot Details From Clubbed Transaction Lot Details
		Chile Paid YYMM is taken From the Lot Master Month, Year
		2. Final Update in the Lot Master all the Child Lot Number from the transaction Lot
        */
        foreach ($child as $key) {
    		$lotObj = lot_master::where('lot_no',$key)->where('scheme_id',$scheme_id)->first();
            $child_paid_yymm = $this->get_paid_yymm(trim($lotObj->lot_month), trim($lotObj->lot_year));
    		DB::statement("update sbi.transaction_lot_details".$table_year." tl set status_code=t1.status_code,paid_yymm=".$child_paid_yymm."
				from (select * from sbi.clubbed_transaction_lot_details ctl, sbi.clubbed_transaction_lot tl
				 where ctl.lot_no=tl.lot_no and tl.lot_no='".$request->lot_no."')t1
				where tl.lot_no='".$key."' and t1.pension_id=tl.pension_id");

    	}
    	/*
		1. Update Success Beneficiary Payment Count and Last paid YYMM
		2. Update Failed Beneficiary Lot Generated = -3 (SBI Error)
		3. Update Lot Master (Type - Child Lot(7)) From Lot Master (Type - Clubbed Lot(8))
    	*/
    	DB::statement("update lot_master lm set ref_no=t.ref_no,rbi_success_count=t.rbi_success_count,rbi_failed_count=t.rbi_failed_count 
			from (select lot_no,ref_no,rbi_success_count,rbi_failed_count from lot_master where lot_no='".$request->lot_no."') t
			where lm.lot_no in (".$group_child_no.")");
        DB::statement("update pension.beneficiary set last_paid_yymm = ".$child_paid_yymm." ,payment_count=payment_count+".$paid_count->no_of_month." where id in (select pension_id from sbi.clubbed_transaction_lot_details where debit_reference='".$debit_ref."' and substr(status_code,1,1)='S')");
        DB::statement("update pension.beneficiary set lot_generated=-3 where id in (select pension_id from sbi.clubbed_transaction_lot_details where debit_reference='".$debit_ref."' and substr(status_code,1,1)='E')");

        return redirect("/clubbed-report-lot-master-sbi/index")->with('success','DB compiled successfully. All Actions completed for the lot-'.$request->lot_no);
    }
    /*
        End Reponse Clubbed Lot
    */

    public function get_paid_yymm($lot_month,$lot_year)
    {
    	// $paid_yymm = 0;
        if ($lot_month == 'June') {
            $paid_yymm=substr($lot_year,2,2).'06';
        }
        else if ($lot_month == 'July') {
            $paid_yymm=substr($lot_year,2,2).'07';
        }
        else if ($lot_month == 'August') {
            $paid_yymm=substr($lot_year,2,2).'08';
        }else if ($lot_month == 'September') {
            $paid_yymm=substr($lot_year,2,2).'09';
        }else if ($lot_month == 'October') {
            $paid_yymm=substr($lot_year,2,2).'10';
        }else if ($lot_month == 'November') {
            $paid_yymm=substr($lot_year,2,2).'11';
        }else if ($lot_month == 'December') {
            $paid_yymm=substr($lot_year,2,2).'12';
        }else if ($lot_month == 'January') {
            $paid_yymm=substr($lot_year,7,2).'01';
        }else if ($lot_month == 'February') {
            $paid_yymm=substr($lot_year,7,2).'02';
        }else if ($lot_month == 'March') {
            $paid_yymm=substr($lot_year,7,2).'03';
        }else if ($lot_month == 'April') {
            $paid_yymm=substr($lot_year,2,2).'04';
        }else if ($lot_month == 'May') {
            $paid_yymm=substr($lot_year,2,2).'05';
        }

        return $paid_yymm;
    }
    
}
