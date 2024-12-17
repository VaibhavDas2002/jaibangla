<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\applicationModel;
use App\Policestation;
use App\PicUpload;
use App\User;
use DB;
use PDF;
use TCPDF;
use CUSTOMPDF;
use URL;


class CertificateStatusController extends Controller
{
	
    public function certificateStatus(Request $request){
    	$users = User::All();
    	 $this->validate($request, [
        'pccstatus' => 'required|numeric'

    	]);

		$application_no = $request->input('pccstatus');
		

		if($application_no != 'null' ){
		  $application = applicationModel::where('application_id','=',$application_no)->where('valid_upto','>=',date("Y-m-d"))->first();

			//$message_status=;
			//$validupto=$application->valid_upto;
			//$purpose=$application->pcc_purpose;			

		 return view('/checkstatus')->with('applications',$application)
		 ->with('message_status',"valid")->with('status1',"PCC Issued");
		}
		elseif($application_no == '' ){

		return view('/checkstatus')->with('message_status',"valid")->with('status',"PCC Issued");
	    }

	}

	
}
