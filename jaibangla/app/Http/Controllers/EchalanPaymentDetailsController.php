<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PicUpload;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Storage;
use App\applicationModel;
use App\User;

class EchalanPaymentDetailsController extends Controller
{

   
    public function index($ref_no){
      $users = User::All();
    	return view('frontend.echalan-payment-details')->with('application_no',$ref_no)->with('users',$users);
    }
   	 public function echalanSubmit(Request $request){
        $users = User::All();
   	 		$application_id = $request->input('application_no');
   	 		$grn = $request->input('grn');
   	 		$payement_mode = $request->input('paymentMode');
   	 		$grn_date = $request->input('GRNDate');
   	 		$bank_code = $request->input('bankCode');
   	 		$brn = $request->input('brn');
   	 		$brn_date = $request->input('brnDateTime');
        /*start added section*/
        $path = $request->file('echallan');
        $file_profile = $path->getClientOriginalName();
        $ext_type = $path->getClientOriginalExtension();
        //$picObj->stored_file_name= $path;
        //$destinationPath = base_path() . '\public'."\\".$application_id;
        //$destinationPath = storage_path('app\\keep\\').$application_id;
        $destinationPath = storage_path('app/keep/').$application_id;
        $fileStore[] = $path->move($destinationPath, $file_profile);
        $file_name = array();
        //array_push($file_name,$file_profile);

        /*echo "<pre>";
        print_r($file_name);
        echo "</pre>";*/
        /* end added section */


   	 		//$path = $request->file('echallan')->store($application_id);

   	 		$objAppliaction = applicationModel::where('application_id', $application_id)->first();
   	 		$objAppliaction->grn = $grn;
   	 		$objAppliaction->payement_mode = $payement_mode;
   	 		$objAppliaction->bank_code = $bank_code;
   	 		$objAppliaction->brn = $brn;
   	 		$objAppliaction->brn_date = $brn_date;
   	 		$objAppliaction->is_fee_paid = 'Y';
   	 		$objAppliaction->fee_amount = 300;    		
    		$objAppliaction->current_status = "ASSIGNEDTOSI";
        $objAppliaction->e_challan_image = $file_profile;
        $objAppliaction->e_challan_type = $ext_type;
        $objAppliaction->is_rejected = 'Y';
        $objAppliaction->save();

    		$picObj = new PicUpload();
		    $picObj->pcc_appliction_id= $application_id;

        /*start added section*/
        //$picObj->stored_file_name=json_encode($file_name);
        //array_push($picObj->stored_file_name, $file_name);
        /* end added section */



         /*echo "<pre>";
        print_r($picObj->stored_file_name);
        echo "</pre>";*/

        
		    /*$picObj->extension_type= "PDF";
		    $picObj->document_type= "ECHALLAN";
		    $picObj->document_no= 10; 
		    $picObj->document_size= 1000; 
		    //$picObj->stored_file_name= $path;*/
		    $picObj->save();

		    $status="eChallan Uploaded Successfully";
		    return view('frontend.message')
        	->with('status',$status)        	
        	->with('ref_no',$application_id)
          ->with('users',$users);


    		
    }
}
