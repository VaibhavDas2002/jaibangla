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
use Maatwebsite\Excel\Facades\Excel;
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

class UATPushToIfmsController extends Controller
{
    public function uatexportXml(Request $request)
	{
		$statusCode = 200;
		$response = [];
		// if (!$request->ajax()) {
		// 	$statusCode = 400;
		// 	$response = array('error' => 'Error occured in ajax call.');
		// 	return response()->json($response, $statusCode);
		// }
		try {
            // dd(1);
            $scheme_id = 3;
            $DRN_part = '206543';
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
			$ben_data = DB::select("select trim(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(b.ben_fname,'')||' '||COALESCE(b.ben_mname,'')||' '||COALESCE(b.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) as name
            , trim(replace(b.bank_code,chr(160),'')) as acc_no
            , substring(trim(replace(b.bank_ifsc,chr(160),'')),1,11) as ifsc, mobile_no, 1000 as amount, id as ben_id, concat(created_by_dist_code, scheme_id,id) as unique_id, null as  order_no_date 
            from bandhu.beneficiary b where id =any(array[78410, 152097, 137543, 156014, 79643, 205486, 205731, 205989, 205668, 206273]) ");
            // dd($ben_data);

            $totalvalue = 10000;
            $benf_count = 10;
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
                // $remarks = $xmlFile->createElement("REMARKS", trim($schemeName));
                // $beneficiary->appendChild($remarks);
            }
            // print $xmlFile;die;

            // $xmlFile->save("xml_file/pushed/" . $filename . ".xml");
            $xml_File = $xmlFile->saveXML($xmlFile->documentElement);
            // print $xml_File;

            Storage::put('ifms_xml/' . $filename . '.xml', $xml_File);

		} catch (\Exception $e) {
			$response = array(
				'exception' => true,
				//'exception_message' => $e->getMessage(),
				'exception_message' => 'Oops. Connection time out. Please try agian later.',
			);
			$statusCode = 400;
		} 
        // finally {
		// 	return response()->json($response, $statusCode);
		// }
	}
}
