<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;
use App\State;
use App\PicUpload;
use App\Policestation;
use DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Storage;
use App\Configduty;
use Illuminate\Support\Facades\Log;
use App\OTPUser;
use Session;
use App\applicationModel;
use DateTime; 
use App\Http\Controllers\SmsSendController;
use Auth;
use Image;
use App\User;


class ApplicationController extends Controller
{

	  public function __construct()
    {
        $this->middleware('auth');
       
        
    }


    //$ses_mobile = null;
    public function index(Request $request)
    {

    	$countries=Country::all();
    	$states=State::all();
    	$policestations=Policestation::all();
    	$mobile = $request->session()->get('session_mobile');
        return view('frontend.application_form')
        ->with('countries',$countries)
        ->with('states',$states)
        ->with('policestations',$policestations)
        ->with('mobile_no_applied',$mobile);
       
    }

    public function checkstatus(Request $request){

		 $users = User::All();

        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();
        //$approvedapplications = applicationModel::where('is_fee_paid','=','Y')->where('current_status', '=', 'READY')->paginate(10); 

        //$approvedapplications = applicationModel::where('current_status', '=', 'READY')->get();
        /*echo "<pre>";
        print_r($approvedapplications);
        echo "</pre>";
        die();*/

       // $user_id = Auth::user()->id;
        //$duty = Configduty::where('user_id','=',$user_id)->first();
        //$pendingapplications = applicationModel::where('is_fee_paid','=','Y')->where('current_status', '=', 'APPROVEDBYDCP')->where('police_station_code', '=', $duty->ps_code)->paginate(10); 

        //$pendingapplications = applicationModel::where('current_status', '!=', 'APPROVEDBYDCP')->get();

        //$processingapp = applicationModel::where('is_fee_paid','=','Y')->where('current_status', '=', 'APPROVEDBYACP' || 'ASSIGNEDTODCP')->where('police_station_code', '=', $duty->ps_code)->paginate(10); 
        //$processingapp = applicationModel::where('current_status', '=', 'ASSIGNEDTOACP' )->get();

        //$rejectedapp = applicationModel::where('is_fee_paid','=','Y')->where('is_rejected', '=', 'N')->paginate(10); 
        

            $approvedapplications = applicationModel::where('is_fee_paid','=','Y')->where('current_status', '=', 'READY')->paginate(10);

            if($approvedapplications) {
                return view('dashboard')
            ->with('approvedapplications',$approvedapplications)
            ->with('users',$users);
            }
             else {
            return view('dashboard')->with('users',$users);
            }

            /*echo "<pre>";
            print_r($approvedapplications);
            echo "</pre>";
            die();*/
        if($duty != null){
            $pendingapplications = applicationModel::where('is_fee_paid','=','Y')->where('current_status', '=', 'APPROVEDBYDCP')->where('police_station_code', '=', $duty->ps_code)->paginate(10); 

            echo "<pre>";
            print_r($pendingapplications);
            echo "</pre>";

            $processingapp = applicationModel::where('is_fee_paid','=','Y')->where('current_status', '=', 'APPROVEDBYACP' || 'ASSIGNEDTODCP')->where('police_station_code', '=', $duty->ps_code)->paginate(10); 

            $rejectedapp = applicationModel::where('is_fee_paid','=','Y')->where('is_rejected', '=', 'N')->paginate(10); 

            return view('dashboard')
            ->with('approvedapplications',$approvedapplications)
           ->with('pendingapplications',$pendingapplications)->with('processingapplications',$processingapp)->with('rejectedapp',$rejectedapp)
            ->with('users',$users);
        }
            
        
        else {
            return view('dashboard')->with('users',$users);

        }
    }

    public function myCaptcha()
    {
        return view('myCaptcha');
    }

    
    // For Sending OTP
    public function sendOtp(Request $request){

    	$this->validate($request,[
            'mobileno' => 'required',
            'captcha' => 'required|captcha'
        ],
        ['captcha.captcha'=>'Invalid captcha code.']);
    	
    	if($request->input('mobileno') != "" ){
	    $otp = rand(100000, 999999);
	    $OTPUser = new OTPUser;
	    $OTPUser->otp = $otp;
	    $OTPUser->mobile = $request->input('mobileno');
	    $request->session()->put('session_mobile', $OTPUser->mobile);
	   
	    $OTPUser->save();
	    $mobileNo = $OTPUser->mobile;
        $message = "OTP is sent to Mob No ".$mobileNo;
        $smsObj = new SmsSendController();
        $smsObj->initiateSmsActivation($mobileNo,"Your PCC Login OTP is ".$OTPUser->otp);
        return redirect('/checkstatus')->with('message',$message)->with('mobile',$mobileNo);
     }
	}

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function refreshCaptcha()
    {
        return response()->json(['captcha'=> captcha_img()]);
    }

	public function verifyOtp(Request $request){

		

		echo  $mobile = $request->session()->get('session_mobile');
		Log::info($mobile);
	    $response = array();
	    $enteredOtp = $request->input('otp');
	    $lastOTP= OTPUser::where('mobile',$mobile)->where('is_verified',0)->orderBy('created_at', 'DESC')->first();



	    $objAppliaction = applicationModel::where('mobile_no', $mobile)->where('current_status','DRAFFTED')->orderBy('created_at', 'DESC')->first();
	    $today=date('Y-m-d');
	    $objStatus = applicationModel::where('mobile_no', $mobile)->where('valid_upto', '>=', $today)->first();//->orWhereNull('valid_upto')
	    
	    if($lastOTP->otp == "" || $lastOTP->otp == null|| $enteredOtp == "" || $enteredOtp == null){
	        echo $message = 'OTP is not valid. Please try again.';
	        return redirect('/checkstatus')->with('message',$message);
	    }
	    if($lastOTP->otp == $enteredOtp)
	    {
	    	$users = OTPUser::all();
		    $lastOTP->update(['is_verified' => 1]);
    	    //$request->session()->forget('session_mobile');

    	    if($objAppliaction!=null){
    	    	return redirect('/payment/'.$objAppliaction->application_id);
    	    	//return view('frontend.echalan-payment-details')->with('application_no',$objAppliaction->application_id);	
    	    }elseif($objStatus!=null){
    	    	Log::info("In this if block");
    	    	return view('frontend.application-view-download')->with('objStatus',$objStatus);
    	    }
    	    else    	    
	    		return redirect('/application');
	    }
	    else{
	    	echo $message = 'OTP is not valid. Please try again with the currect OTP.';
	        return redirect('/checkstatus')->with('message',$message);
	    }
	}
	public function resendOtp(Request $request){
		$mobile = $request->session()->get('session_mobile');
		$otp = rand(100000, 999999);
	    $OTPUser = new OTPUser;
	    $OTPUser->otp = $otp;
	    $OTPUser->mobile = $mobile;
	    //$request->session()->put('session_mobile', $OTPUser->mobile);
	   
	    $OTPUser->save();
	    $mobileNo = $OTPUser->mobile;
        $message_resent = "OTP is re-sent to Mob No ".$mobileNo;
        $smsObj = new SmsSendController();
        $smsObj->initiateSmsActivation($mobileNo,"Your PCC Login OTP is ".$OTPUser->otp);
        return redirect('/checkstatus')->with('message_resent',$message_resent);
	}

	public function checkPolicestation($id){

		$assigned_officer_id = Configduty::where('ps_code','=',$id)->first();
		if($assigned_officer_id=="")
		{
			return "There is no duty officer assign  for pcc ";

		}

		

		
	}

	
    public function applicationSave(Request $request){
    		$this->validateInput($request);
    		$assigned_officer_id = Configduty::where('ps_code',$request->input('in_police_station_name'))->first();

		if($assigned_officer_id !=""){
    		 		
    		$in_first_name = $request->input('in_first_name');
	    	$in_middle_name = $request->input('in_middle_name');
	    	$in_last_name = $request->input('in_last_name');
	    	$in_present_address_line1 = $request->input('in_present_address_line1');
	    	$in_present_address_line2 = $request->input('in_present_address_line2');
	    	$in_present_address_landmark = $request->input('in_present_address_landmark');
	    	$in_present_pincode = $request->input('in_present_pincode');
	    	$in_present_city = $request->input('in_present_city');
	    	$in_present_state = $request->input('in_present_state');
	    	$in_present_country = $request->input('in_present_country');
	    	$in_gender = $request->input('in_gender');
	    	$in_dob = date_format(date_create($request->input('in_dob')), 'Y-m-d');
	    	
	    	$in_nationality = $request->input('in_nationality');
	    	$in_father_name = $request->input('in_father_name');
	    	$in_spouse_name = $request->input('in_spouse_name');
	    	$in_email = $request->input('in_email');
	    	$in_mobile_no = $request->input('in_mobile_no');

	    	$pathname = $request->file('user_img');
		    //$image_profile = $pathname->getClientOriginalName();
		    $image_profile = time().'.'.$pathname->getClientOriginalExtension();

	    	$fromMonth = $request->input('fromMonth');
	    	$fromYear =  $request->input('fromYear');
	    	$formDateMonth  = $fromYear."-".$fromMonth."-"."01";
	    	$in_present_stay_frm_date = date_format(date_create($formDateMonth), 'Y-m-d');

	    	$ToMonth = $request->input('ToMonth');
	    	$ToYear = $request->input('ToYear');
	    	$toDateMonth  = $ToYear."-".$ToMonth."-"."01";
	    	$in_present_stay_to_date = date_format(date_create($toDateMonth), 'Y-m-d');

	    	$date1 = new DateTime($in_present_stay_frm_date);
	    	$date2 = new DateTime($in_present_stay_to_date);

	    	//echo "diffrent". $date2->diff($date1)->format("%d days, %h hours and %i minuts");

	    	$interval = date_diff($date1, $date2);
			$interval->format('%R%a days');

	    	$in_police_station_name = $request->input('in_police_station_name');

	    	$in_permanent_address_line1 = $request->input('in_permanent_address_line1');
	    	$in_permanent_address_line2 = $request->input('in_permanent_address_line2');
	    	$in_permanent_address_landmark = $request->input('in_permanent_address_landmark');
	    	$in_permanent_pincode = $request->input('in_permanent_pincode');
	    	$in_permanent_city = $request->input('in_permanent_city');
	    	$in_permanent_state = $request->input('in_permanent_state');
	    	$in_permanent_country = $request->input('in_permanent_country');


	    	$in_pcc_purpose = $request->input('in_pcc_purpose');
	    	$new_language = $request->input('new_purpose');

	    	if ($request->input('in_pcc_purpose') == 'others') {
			   $in_pcc_purpose = $request->input('new_purpose');
			} else {
			   $in_pcc_purpose = $request->input('in_pcc_purpose');
			}

	    	//$in_pcc_virification_for = $request->input('in_pcc_virification_for');
	    	$in_pcc_virification_for = 'notApplicable';
		    $ps_name=Policestation::find($in_police_station_name); 

    	    
	    	//$objAppliaction->current_status = "ASSIGNEDTOSI";
            //$objAppliaction->currently_with_user_id = $agent;
			

	   	
	     $sql = " INSERT INTO pcc_application (application_id,application_datetime,applicat_user_id,first_name,middle_name,last_name,present_address_line1,present_address_line2,present_address_landmark,present_pincode,present_city,present_state,gender,dob,nationality,father_name,spouse_name,email,mobile_no,present_stay_frm_date,present_stay_to_date,police_station_code,police_station_name,permanent_address_line1,permanent_address_line2,permanent_address_landmark,permanent_pincode,permanent_city,permanent_state,permanent_country,pcc_purpose,pcc_virification_for,remarks,current_status,currently_with_user_id,profile_img)
	     VALUES(get_application_no(1),NOW(),'2','".$in_first_name."','".$in_middle_name."','".$in_last_name."','".$in_present_address_line1."','".$in_present_address_line2."','".$in_present_address_landmark."','".$in_present_pincode."','".$in_present_city."','".$in_present_state."','".$in_gender."',
	     '".$in_dob."','".$in_nationality."','".$in_father_name."','".$in_spouse_name."','".$in_email."','".$in_mobile_no."','".$in_present_stay_frm_date."','".$in_present_stay_to_date."','".$in_police_station_name."','".$ps_name->name."','".$in_permanent_address_line1."','".$in_permanent_address_line2."',
	     '".$in_permanent_address_landmark."','".$in_permanent_pincode."','".$in_permanent_city."','".$in_permanent_state."','".$in_permanent_country."','".$in_pcc_purpose."','".$in_pcc_virification_for."','1','DRAFFTED',".$assigned_officer_id->user_id.",'".$image_profile."') RETURNING application_id";	     	
		     $results = DB::select($sql);
		     $application_id = $results[0]->application_id;
		     $path = $request->file('user_img');
		     $file_profile = $path->getClientOriginalName();
		     $ext_type= $path->getClientOriginalExtension();

		     $picObj = new PicUpload();
		     $picObj->pcc_appliction_id= $application_id;
		     $picObj->stored_file_name= $path;
		     //APPSUBMITTED
		     $destinationPath = storage_path('app\\keep\\').$application_id;
		     $fileStore[] = $path->move($destinationPath, $file_profile);
	   
			if ($request->hasFile('doc_type')) {
			    $files = $request->file('doc_type');
			    $picture = array();
			    $destinationPath = array();
			    foreach($files as $file){
			        $filename = $file->getClientOriginalName();
			        $type= $file->getClientOriginalExtension();
			        $fsize=(string)$file->getClientSize();
			        $picture = $filename;
			        $destinationPath = storage_path('app\\keep\\').$application_id;
			        $fileStore[] = $file->move($destinationPath, $picture);
			        $file_name[] = $filename;
			        $extension[] = $type;
			        $size[] = $fsize;
			    }
			}
			array_push($file_name,$file_profile);
			array_push($extension,$ext_type);

			
			//print_r($file_name);
			//print_r($extension);

		    foreach($request->input('doc_name') as $value) {
	          $doc_name[] = $value;
	        }
	        foreach($request->input('doc_number') as $va) {
	         $doc_number[] = $va;
	        }
			
	        $picObj->stored_file_name=json_encode($file_name);
	        $picObj->extension_type=json_encode($extension);
	        $picObj->document_size=json_encode($size);
	        $picObj->document_type=json_encode($doc_name);
	        $picObj->document_no=json_encode($doc_number);
	        //$fileStore

	        


			
		    $results = $picObj->save();
	   		$status = "Apllication Saved Successfully.";
	  		$grips = "Proceed to GRIPS for payement";
	        if ($results) {
	        	
			return redirect('/payment/'.$application_id);
	        	
	        }else
		    {
		        return redirect()->intended('frontend.application_form')->withErrors($validator);
		    }
		} 
		?>
		<script> 
			alert( "No duty police station selected  ! try it again ");
		</script>
		<?php 

    }

    public function pagelogout(Request $request){
    	   Auth::logout();
           $request->session()->flush();
           $request->session()->regenerate();
           return redirect('/');
    }

    private function validateInput($request) {
	        $this->validate($request, [
	        'in_first_name' => 'required|alpha|max:60',
	        'in_last_name'  => 'required|alpha|max:60',
	        'in_middle_name'=> 'string|nullable',
	        'in_gender'     =>'required',
	        'user_img'      => 'required|mimes:jpeg,png|max:40',
	        'in_nationality'=>'required|alpha',
	        'in_father_name'=>'required',
	        'in_spouse_name'=>'string|nullable',	        
	        'in_email'      => 'sometimes|required|email',
	        'in_mobile_no'  =>'required|numeric',
	        'in_present_address_line1'    => 'required',
	        'in_present_address_line2'    => 'required',
	        'in_present_address_landmark' => 'required',
	        'in_present_pincode'          => 'required|max:6',
	        'in_present_city'             =>   'required|alpha',
	        'in_permanent_address_line1'    => 'required',
	        'in_permanent_address_line2'    => 'required',
	        'in_permanent_address_landmark' => 'required',
	        'in_permanent_pincode'          => 'required|numeric',
	        'in_permanent_city'             => 'required|alpha_dash',
	        'in_permanent_state'            => 'required|alpha_dash',
	        'in_permanent_country'          => 'required|alpha_dash',
	        'doc_name'       => 'required',
	        'doc_type.*' =>'required|mimes:jpg,jpeg,png,pdf|max:500',
	        'doc_number'     => 'required',
	        'in_pcc_purpose' => 'required'

	    ]);
    }

    public function applicationDetailsShow($id){

    	$application = applicationModel::where('application_id','=',$id)->first();
        $application_images = PicUpload::where('pcc_appliction_id','=',$id)->first();

        $present_state = State::where('id','=',$application->present_state)->first();
        $permanent_state = State::where('id','=',$application->permanent_state)->first();
        $permanent_country = Country::where('id','=',$application->permanent_country)->first();

    	return view('frontend.application-details-show')
        ->with('application',$application)
        ->with('application_images',$application_images)
        ->with('present_state',$present_state)
        ->with('permanent_state',$permanent_state)
        ->with('permanent_country',$permanent_country);
    }

    public function editApplication($id){
    	$states = State::all();
    	$countries = Country::all();
    	$policestations = Policestation::all();
    	$application = applicationModel::where('application_id','=',$id)->first();
    	/*echo "<pre>";
    	print_r($application);
    	echo "</pre>";
    	die();*/
    	return view('frontend.edit-application')
    	->with('states',$states)
    	->with('countries',$countries)
    	->with('policestations',$policestations)
    	->with('application',$application);
    }

    public function frontpageImage($user_id, $slug)
    {
        return Image::make(storage_path() . '/app/keep/' . $user_id . '/' . $slug)->response();
         //Log::info($storagePath);
    }

     public function downloadImage(Request $request,$user_id, $slug){
         $mobile = $request->session()->get('session_mobile');
         $objAppliaction = applicationModel::where('mobile_no', $mobile)->where('current_status','READY')->orderBy('created_at', 'DESC')->first();
         return Image::make(storage_path() . '/app/keep/' . $user_id . '/' . $slug)->response();
         Log::info($storagePath);
    }

    

    public function updateApplication(Request $request,$id){
    	$this->validateeditInput($request);
    	$assigned_officer_id = Configduty::where('ps_code',$request->input('in_police_station_name'))->first();
    	if($assigned_officer_id !=""){

    		$pathname = $request->file('user_img');
		    $image_profile = $pathname->getClientOriginalName();

	    	$fromMonth = $request->input('fromMonth');
	    	$fromYear =  $request->input('fromYear');
	    	$formDateMonth  = $fromYear."-".$fromMonth."-"."01";
	    	$in_present_stay_frm_date = date_format(date_create($formDateMonth), 'Y-m-d');

	    	$ToMonth = $request->input('ToMonth');
	    	$ToYear = $request->input('ToYear');
	    	$toDateMonth  = $ToYear."-".$ToMonth."-"."01";
	    	$in_present_stay_to_date = date_format(date_create($toDateMonth), 'Y-m-d');

	    	$date1 = new DateTime($in_present_stay_frm_date);
	    	$date2 = new DateTime($in_present_stay_to_date);

	    	//echo "diffrent". $date2->diff($date1)->format("%d days, %h hours and %i minuts");

	    	$interval = date_diff($date1, $date2);
			$interval->format('%R%a days');

			$in_police_station_name = $request->input('in_police_station_name');
			$in_pcc_virification_for = 'notApplicable';
		    $ps_name=Policestation::find($in_police_station_name); 
		    //echo $ps_name->id;
		    $in_pcc_virification_for = 'notApplicable';
		    //die();
		    

		    $input = [
		    	'first_name' => $request['in_first_name'],
		    	'middle_name'=> $request['in_middle_name'],
		    	'last_name' =>  $request['in_last_name'],
		    	'present_address_line1' => $request['in_present_address_line1'],
		    	'present_address_line2' => $request['in_present_address_line2'],
		    	'present_address_landmark' => $request['in_present_address_landmark'],
		    	'present_pincode' => $request['in_present_pincode'],
		    	'present_city' => $request['in_present_city'],
		    	'present_state' => $request['in_present_state'],
		    	'gender' => $request['in_gender'],
		    	'dob' => date_format(date_create($request->input('in_dob')), 'Y-m-d'),
		    	'nationality' => $request['in_nationality'],
		    	'father_name' => $request['in_father_name'],
		    	'spouse_name' => $request['in_spouse_name'],
		    	'email' => $request['in_email'],
		    	'mobile_no' => $request['in_mobile_no'],
		    	'present_stay_frm_date' => date_format(date_create($formDateMonth), 'Y-m-d'),
		    	'present_stay_to_date' => date_format(date_create($toDateMonth), 'Y-m-d'),
		    	'police_station_code' => $ps_name->id,
		    	'police_station_name' => $ps_name->name,
		    	'permanent_address_line1'=>$request['in_permanent_address_line1'],
		    	'permanent_address_line2'=>$request['in_permanent_address_line2'],
		    	'permanent_address_landmark'=>$request['in_permanent_address_landmark'],
		    	'pcc_purpose'=>$request['in_pcc_purpose'],
		    	'profile_img' => $image_profile,
		    	'pcc_virification_for'=> $in_pcc_virification_for,
		    	'permanent_pincode' => $request['in_permanent_pincode'],
		    	'permanent_city' => $request['in_permanent_city'],
		    	'permanent_state' => $request['in_permanent_state'],
		    	'permanent_country' => $request['in_permanent_country'],
		    ];
		    applicationModel::where('application_id', $id)->update($input);

		     $path = $request->file('user_img');
		     $file_profile = $path->getClientOriginalName();
		     $ext_type= $path->getClientOriginalExtension();


		     $picObj = new PicUpload();
		     $picObj->pcc_appliction_id= $id;
		     $picObj->stored_file_name= $path;

		     $destinationPath = storage_path('app\\keep\\').$id;
		     $fileStore[] = $path->move($destinationPath, $file_profile);

	   
			if ($request->hasFile('doc_type')) {
			    $files = $request->file('doc_type');
			    $picture = array();
			    $destinationPath = array();
			    foreach($files as $file){
			        $filename = $file->getClientOriginalName();
			        $type= $file->getClientOriginalExtension();
			        $fsize=(string)$file->getClientSize();
			        $picture = $filename;

			        $destinationPath = storage_path('app\\keep\\').$id;
			        $fileStore[] = $file->move($destinationPath, $picture);

			        $file_name[] = $filename;
			        $extension[] = $type;
			        $size[] = $fsize;

			    }
			}
			array_push($file_name,$file_profile);
			array_push($extension,$ext_type);

		    foreach($request->input('doc_name') as $value) {
	          $doc_name[] = $value;
	        }
	        foreach($request->input('doc_number') as $va) {
	         $doc_number[] = $va;
	        }
			
	        $picObj->stored_file_name=json_encode($file_name);
	        $picObj->extension_type=json_encode($extension);
	        $picObj->document_size=json_encode($size);
	        $picObj->document_type=json_encode($doc_name);
	        $picObj->document_no=json_encode($doc_number);

		   $results = $picObj->save();

		  
		   	return redirect('/payment/'.$id);
		 
		   

    	}
      
    }

    public function payment_page(){
		return redirect('/payment/'.$application_id);
    } 
    private function validateeditInput($request) {
	        $this->validate($request, [
	        'in_first_name' => 'required|alpha|max:60',
	        'in_last_name'  => 'required|alpha|max:60',
	        'in_middle_name'=> 'string|nullable',
	        'user_img'      => 'required|mimes:jpeg,png|max:40',
	        'in_gender'     =>'required',
	        'in_nationality'=>'required|alpha',
	        'in_father_name'=>'required',
	        'in_spouse_name'=>'string|nullable',	        
	        'in_email'      => 'sometimes|required|email',
	        'in_mobile_no'  =>'required|numeric',
	        'in_present_address_line1'    => 'required',
	        'in_present_address_line2'    => 'required',
	        'in_present_address_landmark' => 'required',
	        'in_present_pincode'          => 'required|max:6',
	        'in_present_city'             =>   'required|alpha',
	        'in_permanent_address_line1'    => 'required',
	        'in_permanent_address_line2'    => 'required',
	        'in_permanent_address_landmark' => 'required',
	        'in_permanent_pincode'          => 'required|numeric',
	        'in_permanent_city'             => 'required|alpha_dash',
	        'in_permanent_state'            => 'required|alpha_dash',
	        'in_permanent_country'          => 'required|alpha_dash',
	        'doc_name'       => 'required',
	        'doc_type.*' =>'required|mimes:jpg,jpeg,png,pdf|max:500',
	        'doc_number'     => 'required',
	        'in_pcc_purpose' => 'required'

	        ]);
	}

   private function validateInputOTP($request) {
        $this->validate($request, [
        'pccstatus' => 'required|numeric|max:10'       
    ]);
    }
    
}


