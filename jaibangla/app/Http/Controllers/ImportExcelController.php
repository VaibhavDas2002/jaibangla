<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Excel;
use DOMDocument;
use Response;
use Carbon;
use Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;

class ImportExcelController extends Controller
{
    public function index(){
    	$data = DB::table('ben_import')->orderBy('name','DESC')->get();
    	return view('import_excel',compact('data'));
    }
    public function import(Request $request){
    	$this->validate($request,[
    		'select_file' => 'required|mimes:xls,xlsx'
    	]);
    	$path = $request->file('select_file')->getRealPath();
    	$data = Excel::load($path)->get();

        
    	if($data->count()>0){
    		foreach($data->toArray() as $key => $value){
    			foreach($value as $row){
    				$insert_data[] =array(
    					'name' => $row['beneficiary_name'],
    					'acc_no' => $row['beneficiary_account_number'],
    					'ifsc'  => $row['beneficiary_ifsc_code'],
    					'amount'  => $row['beneficiary_amount'],
    					'ben_id'  => $row['beneficiary_id'],
    					'order_no_date'  => $row['order_no_and_date'],
    					'unique_id' => $row['unique_id'],
    					'mobile_no' => $row['mobile_number']
    				);
    			}
    		}
    		if(!empty($insert_data)){
    			DB::table('ben_import')->insert($insert_data);
    		}
    	}
    	return back()->with('success','Excel Data Imported successfully');

    }

	
    public function exportXml(Request $request){
/////////////////////new code 26.03.2020/////////
		$DRN_part = $request->get('lot_no');               ////parsed value
		$partyCode = $request->get('party_code');          ////parsed value
		if($partyCode == '026')
		{
			$ddocode = 'CAFSCA001';//'NPC000047';//'NPCCAD001';//
		}/*else if ($partyCode == '027')
		{
			$ddocode = 'NPC000047';//'NPCCAD001';//
		}*/else if ($partyCode == '028')
		{
			$ddocode = 'CAFTWA001';//'NPC000047';//'NPCCAD001';//
		}else {
			return;
		}
////////////new code ends///////////
		
        $ben_data = DB::table('ben_export')->where('drn_part','=',$DRN_part)->orderBy('name','DESC')->get();
        $totalamount = DB::table('ben_export')->select( DB::raw('SUM(amount) as total_amount'))->where('drn_part','=',$DRN_part)->get();

        //$totalvalue = $totalamount[0]->total_amount;
        $benf_count = $ben_data->count();
        $mytime = Carbon\Carbon::now();
        $mytime->toDateTimeString();

        $dateToSave =$mytime->format('d/m/Y');
        $dateDate = $mytime->format('d');
        $dateMonth = $mytime->format('m');
        $dateYear = $mytime->format('Y');
        
        $serialNo ='01';

        $filename = $ddocode.$partyCode.$dateDate.$dateMonth.$dateYear.$serialNo.$DRN_part;

        $DRN_full = $dateYear.$dateMonth.$partyCode.$DRN_part;


        $xmlFile = new DOMDocument("1.0",'UTF-8');
        $xmlFile->formatOutput=true;

        $bulkecs  = $xmlFile->createElement("bulkecs"); 
        $xmlFile->appendChild($bulkecs);
        $drn  = $xmlFile->createElement("DRN",$DRN_full); 
        $bulkecs->appendChild($drn);
        
        $bulkecs->setAttribute('totalamount', $totalamount);
        $bulkecs->setAttribute('benfcount', $benf_count);
        
        foreach($ben_data as $details){
            $beneficiary = $xmlFile->createElement("BENEFICIARY");
            $bulkecs->appendChild($beneficiary);
            $name = $xmlFile->createElement("BENF_NAME",trim($details->name));
            $beneficiary->appendChild($name);
            $acc_no = $xmlFile->createElement("ACCOUNT_NO",trim($details->acc_no));
            $beneficiary->appendChild($acc_no);
            $ifsc = $xmlFile->createElement("IFSC_CODE",trim($details->ifsc));
            $beneficiary->appendChild($ifsc);
            $mobile_no = $xmlFile->createElement("MOBILE_NO",trim($details->mobile_no));
            $beneficiary->appendChild($mobile_no);
            $amount = $xmlFile->createElement("AMOUNT",trim($details->amount));
            $beneficiary->appendChild($amount);
            $ben_id = $xmlFile->createElement("ID",trim($details->ben_id));
            $beneficiary->appendChild($ben_id);
            $order_no_date = $xmlFile->createElement("ORDER_NO",trim($details->order_no_date));
            $beneficiary->appendChild($order_no_date);
            $unique_id = $xmlFile->createElement("UNIQUE_ID",trim($details->unique_id));
            $beneficiary->appendChild($unique_id);
            
        }

        echo "<xmp>".$xmlFile->saveXML()."</xmp>";
        $xmlFile->save("xml_file/".$filename.".xml");
		$xml_File = $xmlFile->saveXML($xmlFile->documentElement);
		
//        Storage::disk('sftp_'.$partyCode)->put('ePayment_Files_006/'.$filename.'.xml', $xml_File);          ///////uncomment in production
		
//////////new code for file read 26.03.2020////////
//		$exists = Storage::disk('sftp_'.$partyCode)->exists('ePayment_Files_006/'.$filename);                 ///////uncomment in production
/*if($exists){
		
		echo 'exist';
        
		
		
		}else{
		echo 'not exist';
		}*/

///////////new code ends//////////		
		

    }	
	
}
