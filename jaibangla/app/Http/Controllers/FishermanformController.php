<?php

namespace App\Http\Controllers;

use App\BenEntry;
use Illuminate\Http\Request;
//use App\Http\Controllers\Redirect;
use App\programmeHeadMaster;
use App\majorProgammeHeadMaster;
use App\nhm_employee_details;
use App\designationMaster;
use App\nhm_service_category;
use App\NHMEmployee;
use App\Configduty;
use App\District;
use App\nhm_posting_level;
use App\nhm_level_place;
use App\nhm_health_facility;
use App\UrbanBody;
use App\SubDistrict;
use App\PensionFisherman;
//Dynamic Doc
use App\BenDocsFisherman;
use App\BenDocsArcFisherman;
use App\DocumentType;
use App\SchemeDocMap;
//Dynamic Doc End
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Scheme;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\BankDetails;
use App\Helpers\Helper;
use App\DsPhase;
use App\BenDocs;
use App\AcceptRejectInfo;
use App\Traits\TraitCasteCertificateValidate;
use App\Traits\TraitLifeCertificateValidate;
use App\Traits\TraitAadharValidate;
use App\Helpers\AuthChecker;

class FishermanformController extends Controller
{
    use TraitCasteCertificateValidate;
    use TraitLifeCertificateValidate;
    use TraitAadharValidate;

    public function __construct()
    {
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $scheme_id = 5;
        $is_active = 1; // Static (Not Form Database) BY Vaibhav

        // $base_url=url('/');
        // echo $base_url.'/images/';exit;        

        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                // dd($roleArray);
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $request->session()->put('level', $roleObj['mapping_level']);
                $request->session()->put('distCode', $roleObj['district_code']);
                if ($roleObj['is_urban'] == 1) {
                    $request->session()->put('blockCode', $roleObj['urban_body_code']);
                } else {
                    $request->session()->put('blockCode', $roleObj['taluka_code']);
                }
                break;
            }
        }
        if ($is_active == 1) {
            $districts = District::all();

            //Document Dynamic
            $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
            $doc_list_man = DocumentType::get()->whereIn("id", json_decode($doc_id_list[0]['doc_list_man']));
            $doc_list_opt = DocumentType::get()->whereIn("id", json_decode($doc_id_list[0]['doc_list_opt']));
            $doc_profile_image = DocumentType::get()
                ->where("is_profile_pic", true)->first();

            $doc_profile_image_id = 999;
            if ($doc_profile_image) {
                $doc_profile_image_id = $doc_profile_image->id;
            }
            //echo "<pre>";print_r($doc_profile_image_id); echo "</pre>";die();  
            return view('fisherman/pension_details', [
                'districts' => $districts,
                'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id
            ]);
        }
        if ($is_active == 0) {
            return redirect("/")->with('success', 'User Disabled');
        } else {
            return redirect("/")->with('success', 'User Disabled');
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $level = $request->session()->get('level');
        $distCode = $request->session()->get('distCode');
        $blockCode = $request->session()->get('blockCode');
        if (empty($level) || empty($distCode) || empty($blockCode)) {
            return redirect("/")->with('error', 'Something Wrong ..pleas try again.');
        }
        $user_id = AuthChecker::getUserId();
        $users = User::find($user_id);
        $server_ip = $_SERVER['SERVER_ADDR'];
        $base_url = url('/');
        $uploaded_doc = array();

        $scheme_id = $request->scheme_id;
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
        $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
        $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
        $doc_list = array_merge($doc_list_man, $doc_list_opt);

        $isValidarr = $this->validateInput($request,  $scheme_id, 1);
        if ($isValidarr['is_valid'] == false) {
            return back()->with('errors', $isValidarr['errors'])->withInput(Input::all());
        }
        if (!empty($request->aadhar_no)) {
            if ($this->isAadharValid(trim($request->aadhar_no)) == false) {
                $errors = array();
                $errorMsg = "Aadhaar Number Invalid";
                array_push($errors, $errorMsg);
                // return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput(Input::all());
            }
        }
       

        $ifsc = trim($request->bank_ifsc_code);
        $bank_account_number = trim($request->bank_account_number);
        $bank_branch = trim($request->bank_branch);
        $name_of_bank = trim($request->name_of_bank);
        $row_count_bank = BankDetails::whereraw("trim(ifsc)='$ifsc'")->whereraw("trim(branch)='$bank_branch'")->where('is_active',1)->whereraw("trim(bank)='$name_of_bank'")->count();
        $bank_details = BankDetails::where('ifsc', trim($ifsc))->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
        //$bank_details = BankDetails::whereraw("trim(ifsc)='$ifsc'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
        $new_bank_code=$bank_details->bank_code;
        if ($row_count_bank == 0) {
            $errors = array();
            $errorMsg = "Bank IFSC and Bank Name Not Match!";
            array_push($errors, $errorMsg);
            //return back()->withErrors($errors)->withInput();
            return back()->with('errors', $errors)->withInput(Input::all());
        }
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (!empty($scheme_obj->short_code)) {
            $schema = $scheme_obj->short_code;
        } else {
            $schema = "pension"; 
        
        }
        if(!preg_match('/^[0-9]{10}+$/',$request->mobile_no)) {
            $errors = array();
            $errorMsg = "Mobile Number Invalid";
            array_push($errors, $errorMsg);
            return back()->with('errors', $errors)->withInput(Input::all());
         }
         if($request->mobile_no<1000000000) {
            $errors = array();
            $errorMsg = "Mobile Number Invalid";
            array_push($errors, $errorMsg);
            return back()->with('errors', $errors)->withInput(Input::all());
         }       
          $errormsg=array();
          if(!empty($bank_account_number) && !empty($ifsc)){
           $bank_count = DB::table($schema . '.beneficiaries')->whereIn('is_clean', [1, 2])->where('scheme_id', $scheme_id)->where('bank_code',$bank_account_number)->count('bank_code');
           if($bank_count>0){  
            $is_error=1;  
            array_push($errormsg,'Bank A/C Already Exist!');
           } 
        }          
        if(!empty($request->aadhar_no)){
            $aadhar_count = DB::table($schema . '.beneficiaries')->whereIn('is_clean', [1, 2])->where('scheme_id', $scheme_id)->where('aadhar_no',trim($request->aadhar_no))->count('aadhar_no');
            if($aadhar_count>0){  
             $is_error=1;  
             array_push($errormsg,'Aadhaar Number Already Exist! Please try different.');     
            } 
         } 
         if(!empty($request->mobile_no)){
            $mobile_count = DB::table($schema . '.beneficiaries')->whereIn('is_clean', [1, 2])->where('scheme_id', $scheme_id)->where('mobile_no',$request->mobile_no)->count('mobile_no');
            if($mobile_count>0){  
             $is_error=1;  
             array_push($errormsg,'Mobile Number Already Exist! Please try different.');    
            } 
         } 
         
         if(count($errormsg)>0){
           
            return redirect("fisherman")->withInput(Input::all())->with('errors',$errormsg);
            
           
        }
        $body = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
        $c_time=date('Y-m-d H:i:s');
        $pension_details = new BenEntry();
        $pension_details->entry_datetime = $c_time;
        $pension_details->ip_address=$request->ip();

        //Document Dynamic
        $upload_file=array();
        $i=0;
        $doc_master=DocumentType::get();
       // dd($request->file);
        foreach ($doc_list as $doc) {
            if ($request->hasFile('doc_' . $doc)) {
            $doc_file = $request->file('doc_' . $doc);
            $img_data = file_get_contents($doc_file);
            $u_extension = $doc_file->getClientOriginalExtension();
            $mime_type = $doc_file->getMimeType();
            $doc_type_name =$doc_master->where('id', $doc)->first() ;
            if(strtolower($mime_type)=='image/jpeg'){
                if($u_extension=='jpg' || $u_extension=='jpeg'){
                    $extension=$u_extension;
                }
                else{
                $errors = array();
                $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
                array_push($errors, $errorMsg);
                return back()->with('errors', $errors)->withInput(Input::all());  
                }
            }
            else if(strtolower($mime_type)=='image/png'){
                $extension='png';
            }else if(strtolower($mime_type)=='image/gif'){
                $extension='gif';
            }else if(strtolower($mime_type)=='application/pdf'){
                $extension='pdf';
            }
            else{
                $errors = array();
                $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
                array_push($errors, $errorMsg);
                return back()->with('errors', $errors)->withInput(Input::all());  
            }
            if($u_extension!=$extension){
                $errors = array();
                $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
                array_push($errors, $errorMsg);
                return back()->with('errors', $errors)->withInput(Input::all());  
            }
            $base64 = base64_encode($img_data);
            $upload_file[$i]['created_by_dist_code']=$request->session()->get('distCode');
            $upload_file[$i]['created_by_local_body_code']=$request->session()->get('blockCode');
            $upload_file[$i]['document_type']=$doc;
            $upload_file[$i]['scheme_id']=$scheme_id;
            $upload_file[$i]['created_by_level']=$request->session()->get('level');
            $upload_file[$i]['created_at']=$c_time;
            $upload_file[$i]['created_by']=$user_id;
            $upload_file[$i]['ip_address']=$request->ip();
            $upload_file[$i]['attched_document']=$base64;
            $upload_file[$i]['document_mime_type']=$mime_type;
            $upload_file[$i]['document_extension']=$extension;
            if(!empty($doc_type_name)){
             $upload_file[$i]['doc_type_name'] = $doc_type_name->doc_name;
            }
            $i++;
           }
        }
        //Document Dynamic End

        if ($request->urban_code == 1) {
            $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
            $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();

            $pension_details->block_ulb_name = $block_ulb->urban_body_name;
            $pension_details->gp_ward_name   = $gp_ward->urban_body_ward_name;
        } else {
            $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
            $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();

            $pension_details->block_ulb_name = $block_ulb->block_name;
            $pension_details->gp_ward_name   = $gp_ward->gram_panchyat_name;
        }

        $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
        $assembly_name = $assembly->ac_name;

        if ($request->receive_pension != "") {
            $receive_pension = implode(',', $request->receive_pension);
            $pension_details->receive_pension    = $receive_pension;
        }

        if ($request->social_security_pension != "") {
            $social_security_pension = implode(',', $request->social_security_pension);
            $pension_details->social_security_pension   = $social_security_pension;
        }

        $pension_details->ben_fname = $request->first_name;
        $pension_details->ben_mname = $request->middle_name;
        $pension_details->ben_lname = $request->last_name;
        $pension_details->gender = $request->gender;
        $pension_details->dob = $request->dob;
        $pension_details->ben_age = $request->txt_age;

        $pension_details->father_fname = $request->father_first_name;
        $pension_details->father_mname = $request->father_middle_name;
        $pension_details->father_lname = $request->father_last_name;
        $pension_details->mother_fname = $request->mother_first_name;
        $pension_details->mother_mname = $request->mother_middle_name;
        $pension_details->mother_lname = $request->mother_last_name;
        $pension_details->caste = $request->caste_category;
        $pension_details->fisherman_comm = $request->fisherman_comm;
        $pension_details->marital_status = $request->marital_status;
        $pension_details->phy_hadi_status = $request->phy_hadi_status;
        $pension_details->mothly_income = $request->monthly_income;

        $pension_details->spouse_fname = $request->spouse_first_name;
        $pension_details->spouse_mname = $request->spouse_middle_name;
        $pension_details->spouse_lname = $request->spouse_last_name;

        $pension_details->ration_card_cat = $request->ration_card_cat;
        $pension_details->ration_card_no  = $request->ration_card_no;
        $pension_details->ahl_tin  = $request->ahl_tin;
        $pension_details->aadhar_no  = $request->aadhar_no;
        $pension_details->epic_voter_id  = $request->epic_voter_id;
        $pension_details->pan_no  = $request->pan_no;
        $pension_details->bpl_seq_no = $request->bpl_seq_no;
        $pension_details->bpl_id_no = $request->bpl_id_no;
        $pension_details->bpl_total_score = $request->bpl_total_score;

        $pension_details->dist_code       =      $request->district;
        $pension_details->rural_urban_id     =      $request->urban_code;
        $pension_details->assembly_code   =    $request->asmb_cons;
        $pension_details->assembly_name = $assembly_name;
        $pension_details->police_station  = $request->police_station;
        $pension_details->block_ulb_code  = $request->block;
        $pension_details->gp_ward_code = $request->gp_ward;
        $pension_details->village_town_city  = $request->village;
        $pension_details->house_premise_no  = $request->house;
        $pension_details->post_office  = $request->post_office;
        $pension_details->pincode = $request->pin_code;
        $pension_details->residency_period = $request->residency_period;
        $pension_details->mobile_no  = $request->mobile_no;
        $pension_details->email = $request->email;

        $pension_details->bank_name  = $request->name_of_bank;
        $pension_details->branch_name    = $request->bank_branch;
        $pension_details->bank_code    = $request->bank_account_number;
        $pension_details->bank_ifsc   = $request->bank_ifsc_code;
        $pension_details->npci_bank_code   = $new_bank_code;

        $pension_details->nominate_name    = $request->nominate_name;
        $pension_details->nominate_address    = $request->nominate_address;
        $pension_details->nominate_relationship   = $request->nominate_relationship;

        $pension_details->created_by = Auth::user()->id;
        $pension_details->created_by_level = $request->session()->get('level');
        $pension_details->created_by_dist_code = $request->session()->get('distCode');
        $pension_details->created_by_local_body_code = $request->session()->get('blockCode');
        $pension_details->scheme_id =  $request->scheme_id;

        DB::connection('pgsql5')->beginTransaction();
        DB::connection('pgsql_encwrite')->beginTransaction();
       

        try {

            $is_saved = $pension_details->save();
            // DB::connection('pgsql5')->commit();
            $beneficiary_id = $pension_details->id;
            if($is_saved){
                foreach($upload_file as $key => $csm)
                {
                 $upload_file[$key]['beneficiary_id'] = $beneficiary_id;
                }
                $doc_inserted = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($upload_file);

                if( $doc_inserted ){

                    DB::connection('pgsql5')->commit();
                    DB::connection('pgsql_encwrite')->commit();
                    $ben_fullname=trim($request->first_name) . ' ' . trim($request->middle_name) . ' ' . trim($request->last_name);

                    // $this->bioauthcheckInsert($request->session()->get('distCode'),$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$request->session()->get('blockCode'),$user_id);
                    // if(($request->caste_category=='SC' || $request->caste_category=='ST') && !empty($request->caste_certificate_no)){
                    //  $this->casteInfoCheckInsert($request->session()->get('distCode'),$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->caste_certificate_no),$request->session()->get('blockCode'),$user_id);
                    // }
                    // $this->RationcheckInsert($request->session()->get('distCode'),$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$request->session()->get('blockCode'),$user_id);

                    try {
                        $this->bioauthcheckInsert($request->session()->get('distCode'),$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$request->session()->get('blockCode'),$user_id);
                    } catch (\Exception $e) {
                        $inputMain['life_certificate_checked'] = -1;
                        $upadated_main = DB::table($schema . '.beneficiaries')
                        ->where([
                          'id' => $beneficiary_id, 'created_by_local_body_code' => $request->session()->get('blockCode'),
                          'created_by_dist_code' => $request->session()->get('distCode')
                        ])->update($inputMain);
                    }
                    try {
                        if(($request->caste_category=='SC' || $request->caste_category=='ST') && !empty($request->caste_certificate_no)){
                            $this->casteInfoCheckInsert($request->session()->get('distCode'),$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->caste_certificate_no),$request->session()->get('blockCode'),$user_id);
                           }
                    } catch (\Exception $e) {
                        $inputMain['caste_certificate_checked'] = -1;
                        $upadated_main = DB::table($schema . '.beneficiaries')
                        ->where([
                          'id' => $beneficiary_id, 'created_by_local_body_code' => $request->session()->get('blockCode'),
                          'created_by_dist_code' => $request->session()->get('distCode')
                        ])->update($inputMain);
                    }
                    
                    try {
                        $data = $this->RationcheckInsert($request->session()->get('distCode'),$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$request->session()->get('blockCode'),$user_id,$request->dob);
                      } catch (\Exception $e) {
                        $inputMain['aadhaar_no_checked'] = -1;
                        $upadated_main = DB::table($schema . '.beneficiaries')
                        ->where([
                          'id' => $beneficiary_id, 'created_by_local_body_code' => $request->session()->get('blockCode'),
                          'created_by_dist_code' => $request->session()->get('distCode')
                        ])->update($inputMain);
                      }


                    $ben_details=DB::table($schema . '.beneficiaries')->where('id',$beneficiary_id)->first();
                    if($ben_details){
                        $caste_certificate_checked=$ben_details->caste_certificate_checked;
                        $caste_certificate_validation_message=$ben_details->caste_certificate_validation_message;
                        $caste_certificate_check_lastdatetime=$ben_details->caste_certificate_check_lastdatetime;
                        $caste_matched_with_certificate_no=$ben_details->caste_matched_with_certificate_no;
                        $life_certificate_checked=$ben_details->life_certificate_checked;
                        $life_certificate_pass=$ben_details->life_certificate_pass;
                        $life_certificate_lastdatetime=$ben_details->life_certificate_lastdatetime;
                        $last_biometric=$ben_details->last_biometric;
                        $aadhaar_no_checked=$ben_details->aadhaar_no_checked;
                        $aadhaar_no_checked_lastdatetime=$ben_details->aadhaar_no_checked_lastdatetime;
                        $aadhaar_no_checked_pass=$ben_details->aadhaar_no_checked_pass;
                        $aadhaar_no_validation_msg=$ben_details->aadhaar_no_validation_msg;
                        return redirect("fisherman")->with('success', 'Application Submitted Successfully')
                        ->with('id',  $beneficiary_id)
                        ->with('caste_certificate_checked',  $caste_certificate_checked)
                            ->with('caste_certificate_validation_message',$caste_certificate_validation_message)
                            ->with('caste_certificate_check_lastdatetime',$caste_certificate_check_lastdatetime)
                            ->with('caste_matched_with_certificate_no',$caste_matched_with_certificate_no)
                            ->with('life_certificate_checked',$life_certificate_checked)
                            ->with('life_certificate_pass',$life_certificate_pass)
                            ->with('life_certificate_lastdatetime',$life_certificate_lastdatetime)
                            ->with('last_biometric',$last_biometric)
                            ->with('aadhaar_no_checked',$aadhaar_no_checked)
                            ->with('aadhaar_no_checked_lastdatetime',$aadhaar_no_checked_lastdatetime)
                            ->with('aadhaar_no_checked_pass',$aadhaar_no_checked_pass)
                            ->with('aadhaar_no_validation_msg',$aadhaar_no_validation_msg);
                    }
                }
                else{
                    $error_found=1;
                }
            }
            else{
                $error_found=1;
                
            }
            if($error_found){
                // dd($e);
                DB::connection('pgsql5')->rollback();
                DB::connection('pgsql_encwrite')->rollback();
               
                return redirect("fisherman")->withInput(Input::all())->with('errors', array('Some error.Please try again'));
        }  
                
        } catch (\Exception $e) {
            // dd($e);
            DB::connection('pgsql5')->rollback();
            DB::connection('pgsql_encwrite')->rollback();
           
            return redirect("fisherman")->withInput(Input::all())->with('errors', array('Some error.Please try again'));
        }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */



    public function applicationlist()
    {
        //DB::enableQueryLog();
        $user_id = AuthChecker::getUserId();
        $rows = PensionSc::orderBy('id', 'desc')->paginate(500);
        return view('pension_list', ['nhm_employee_details' => $rows]);
    }
    // public function approvedlistReadOnly(Request $request){
    //     //DB::enableQueryLog();

    //     $user_id = AuthChecker::getUserId();

    //     if($request->get('pr1')){
    //         if($request->get('pr1')=="sc"){
    //             $scheme_id=3;
    //              $rows = PensionSc::where(['scheme_id'=>$scheme_id, 'created_by'=>$user_id])
    //                     ->where('next_level_role_id','!=',null)
    //                     ->orderBy('id', 'desc')
    //                     ->paginate(10)->appends(request()->query());     
    //         }else if($request->get('pr1')=="st"){
    //             $scheme_id=1;
    //              $rows = PensionSt::where(['scheme_id'=>$scheme_id, 'created_by'=>$user_id])
    //                     ->where('next_level_role_id','!=',null)
    //                     ->orderBy('id', 'desc')
    //                     ->paginate(10)->appends(request()->query());  

    //         }else if($request->get('pr1')=="manabik"){
    //             $scheme_id=2;
    //            $rows = Manabik::where(['scheme_id'=>$scheme_id, 'created_by'=>$user_id])
    //                     ->orderBy('id', 'desc')
    //                     ->paginate(10)->appends(request()->query());  
    //         }
    //         else
    //         {
    //             $rows =array();

    //         }
    //     }

    //     return view('pension_list_read_only',['nhm_employee_details' => $rows,'scheme_id'=>$scheme_id, 'list_type'=>'1']);  
    // }

    //     public function applicationlistReadOnly(Request $request){
    //     //DB::enableQueryLog();

    //     $user_id = AuthChecker::getUserId();
    //     $sucess = $request->get('sucess');
    //     $id = $request->get('id');

    //     if($request->get('pr1')){
    //         if($request->get('pr1')=="sc"){
    //             $scheme_id=3;
    //              $rows = PensionSc::where(['scheme_id'=>$scheme_id, 'created_by'=>$user_id, 'next_level_role_id'=>null])
    //                     ->orderBy('id', 'desc')
    //                     ->paginate(10)->appends(request()->query());     
    //         }else if($request->get('pr1')=="st"){
    //             $scheme_id=1;
    //              $rows = PensionSt::where(['scheme_id'=>$scheme_id, 'created_by'=>$user_id, 'next_level_role_id'=>null])
    //                     ->orderBy('id', 'desc')
    //                     ->paginate(10)->appends(request()->query());  

    //         }else if($request->get('pr1')=="manabik"){
    //             $scheme_id=2;
    //            $rows = Manabik::where(['scheme_id'=>$scheme_id, 'created_by'=>$user_id])->orderBy('id', 'desc')->paginate(10)->appends(request()->query());  
    //         }
    //         else
    //         {
    //             $rows =array();

    //         }
    //         }


    //     return view('pension_list_read_only',['nhm_employee_details' => $rows,'scheme_id'=>$scheme_id, 'list_type'=>'0', 'sucess'=>$sucess,'id'=>$id]);


    // }

    // public function applicationdetails(Request $request)
    // {

    //     $id=$request->id; 

    //     $row = PensionSc::find($id);
    //      // echo $row->block_ulb_code;exit;
    //      // echo "<pre>";print_r($block);exit;

    //     $district_name = ""; 
    //     $block_name = "";
    //     $gp_name =  "";

    //     if($row->dist_code !="")
    //     {
    //     $district = District::where('district_code','=',$row->dist_code)->get(['district_code','district_name'])->first(); 
    //     $district_name = $district->district_name; 
    //     }
    //     if($row->block_ulb_code !="")
    //     {    
    //     $block= Taluka::where('block_code','=',$row->block_ulb_code)->first();
    //     $block_name = $block->block_name;
    //     }
    //     if($row->gp_ward_code !="")
    //     {
    //     $gp = GP::where('gram_panchyat_code','=',$row->gp_ward_code)->get(['gram_panchyat_code','gram_panchyat_name'])->first();
    //     $gp_name =  $gp->gram_panchyat_name;
    //     }   





    //     return view('pension_view_details', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name]);
    // }

    // public function applicationdetailsReadOnly(Request $request)
    // {

    //     $id=$request->id; 
    //     $scheme_id=$request->scheme_id;
    //     $docs = array();

    //     if($scheme_id==3){
    //      $row = PensionSc::find($id);           
    //      $docs = BenDocsSc::where('ben_id',$id)->orderBy('doc_type_id')->get();   

    //       }else if($scheme_id==1){
    //          $row = PensionSt::find($id);
    //          $docs = BenDocsSt::where('ben_id',$id)->orderBy('doc_type_id')->get();

    //       }else if($scheme_id==2){
    //          $row = Manabik::find($id);          

    //     }
    //     //echo "<pre>";print_r($row);exit;
    //     // $row = PensionSc::find($id);
    //     // echo $row->block_ulb_code;exit;
    //     // echo "<pre>";print_r($block);exit;

    //     $district_name = ""; 
    //     $block_name = "";
    //     $gp_name =  "";

    //     if($row->dist_code !="")
    //     {
    //     $district = District::where('district_code','=',$row->dist_code)->get(['district_code','district_name'])->first(); 
    //     $district_name = $district->district_name; 
    //     }

    //     if($row->block_ulb_code !="")
    //     { 
    //         if($row->rural_urban_id == 1)  
    //         {
    //         $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
    //         $block_name = $block->urban_body_name;

    //         }
    //         else
    //         {
    //         $block= Taluka::where('block_code','=',$row->block_ulb_code)->first();
    //         $block_name = $block->block_name;

    //         }

    //     }
    //     if($row->gp_ward_code !="")
    //     {
    //         if($row->rural_urban_id == 1)  
    //         {
    //         $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
    //         $gp_name =  $gp_ward->urban_body_ward_name;

    //         }
    //         else
    //         {
    //         $gp = GP::where('gram_panchyat_code','=',$row->gp_ward_code)->get(['gram_panchyat_code','gram_panchyat_name'])->first();
    //         $gp_name =  $gp->gram_panchyat_name;

    //         }

    //     }
    //     $doc_profile_image = DocumentType::get()
    //                                 ->where("is_profile_pic",true)->first();
    //     $doc_profile_image_id = 999;
    //     if($doc_profile_image){
    //         $doc_profile_image_id = $doc_profile_image->id;
    //     }  
    //     return view('pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
    // }

    // public function applicationeditview(Request $request)
    // {
    //     $user_id = AuthChecker::getUserId();
    //     $id=$request->id; 

    //     //echo "scheme_id".$scheme_id;die();
    //     $scheme_id = $request->scheme_id;
    //     //$row = '';
    //     $row=array();
    //     if($scheme_id==3){
    //         $row = PensionSc::find($id);

    //     }else if($scheme_id==1){
    //         $row = PensionSt::find($id);

    //     }

    //     $districts = District::where('is_revenue_district','=','1')->get(['district_code','district_name']);

    //    // $blocks= Taluka::where('district_code','=',$row->dist_code)->get(['block_code', 'block_name']);

    //    // $gps = GP::where('block_code','=',$row->block_ulb_code)->get(['gram_panchyat_code','gram_panchyat_name']);

    //     //Document Dynamic
    //     $doc_id_list = SchemeDocMap::select('doc_list_man','doc_list_opt')->where('scheme_code',$scheme_id)->get();


    //     $doc_list_man = DocumentType::get()->whereIn("id",json_decode($doc_id_list[0]['doc_list_man']));
    //     $doc_list_opt = DocumentType::get()->whereIn("id",json_decode($doc_id_list[0]['doc_list_opt']));
    //     $doc_profile_image = DocumentType::get()
    //                             ->where("is_profile_pic",true)->first();

    //     $doc_profile_image_id = 999;
    //     if($doc_profile_image){
    //         $doc_profile_image_id = $doc_profile_image->id;
    //     }                        
    //     // echo "<pre>";print_r($row); echo "</pre>";die();  

    //     return view('pension_edit', ['row' => $row, 'districts' => $districts , 'scheme_id'=>$scheme_id,'doc_list_man'=>$doc_list_man,'doc_list_opt'=>$doc_list_opt, 'profile_img'=>$doc_profile_image_id]);
    // }


    public function applicationupdate(Request $request)
    {

        $base_url = url('/');
        $id = $request->id;
        $scheme_id = (int) $request->scheme_id;
        // dd($scheme_id);
        $designation_id = Auth::user()->designation_id;

        if (!is_int($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
        if (!is_numeric($id)) {
            return redirect("/")->with('error', 'Applicant ID Not Valid');
        }
        $created_by = Auth::user()->id;
        $is_active = 0;
        $mapping_level = NULL;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray()        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $mapping_level = $roleObj['mapping_level'];
                $distCode = $roleObj['district_code'];
                $is_urban = $roleObj['is_urban'];
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                } else {
                    $blockCode = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disabled');
        }
        $query = PensionFisherman::where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id]);
        if ($designation_id == 'Verifier') {
            $query = $query->whereNull('next_level_role_id');
        } else if ($designation_id == 'Approver') {
            $query = $query->where('is_verified',1)->where('is_approved',0)->where('is_rejected',0);
        } else {
            $query = $query->whereNull('next_level_role_id');
        }
        $row = $query->first();

        if (empty($row->scheme_id)) {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $isValidarr = $this->validateInput($request,  $scheme_id, 2);
        if ($isValidarr['is_valid'] == false) {
            //dd(withInput());
            return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $isValidarr['errors']);
            //return back()->withErrors($isValidarr['errors'])->withInput();
        }
        $scheme_row = Scheme::where('id', $scheme_id)->first();
        if (empty($scheme_row)) {
            return redirect("/")->with('error', 'User Disabled');
        }
        $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
        if (!empty($request->mobile_no)) {
            $mobile_count = PensionFisherman::where('mobile_no', trim($request->mobile_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
           // dd( $mobile_count);
            if ($mobile_count > 0) {
                $errors = array();
                $errorMsg = "Mobile Number Already Exist! Please try different.";
                array_push($errors, $errorMsg);
                return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors',  $errors);
            }
        }
        //--------- Duplicate bank A/C check---------- //
        $bankCount = PensionFisherman::whereRaw("trim(bank_code)=trim(" . "'" . $request->bank_account_number . "'" . ")")->where('id', '!=', $id)
            ->whereRaw("(" . $check_condition_str . ")")
            ->count('id');
    
            //$bank_details = BankDetails::whereraw("trim(ifsc)='$request->bank_ifsc_code'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
            $bank_details = BankDetails::where('ifsc', trim($request->bank_ifsc_code))->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
            $new_bank_code=$bank_details->bank_code;
        
        if ($bankCount > 0) {
            $errors = array();
            $errorMsg = "Bank A/C Already Exist!";
            array_push($errors, $errorMsg);
            return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors',  $errors);
        }
        $count = PensionFisherman::where('aadhar_no', trim($request->aadhar_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
        if ($count > 0) {
            $request->session()->put('dupAadhaarCheck', trim($request->aadhar_no));
            $errors = array();
            $errorMsg = "Aadhaar Number Already Exist! Please try different.";
            array_push($errors, $errorMsg);
            return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('dupAadhaar', 1)->with('errors',  $errors);
        }
        $scheme_schema = $scheme_row->short_code;

        $social_security_pension = "";
        $receive_pension = "";
        if ($request->receive_pension != "") {
            $receive_pension = implode(',', $request->receive_pension);
        }

        if ($request->social_security_pension != "") {
            $social_security_pension = implode(',', $request->social_security_pension);
        }

        $block_ulb_name = "";
        $gp_ward_name = "";

        if ($request->urban_code == 1) {
            $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
            $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();


            $block_ulb_name = $block_ulb->urban_body_name;
            $gp_ward_name   = $gp_ward->urban_body_ward_name;
        } else {
            $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
            $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();

            $block_ulb_name = $block_ulb->block_name;
            $gp_ward_name   = $gp_ward->gram_panchyat_name;
        }
        $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
        $assembly_name = $assembly->ac_name;

        if (trim($request->marital_status) != "Married") {
            $request->spouse_first_name = "";
            $request->spouse_middle_name = "";
            $request->spouse_last_name = "";
        }
        $c_time=date('Y-m-d H:i:s');
        $user_id = AuthChecker::getUserId();
        $input = [
            //'name' => $request['name']
            'ben_fname' => $request->first_name,
            'ben_mname' => $request->middle_name,
            'ben_lname' => $request->last_name,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'ben_age' => $request->txt_age,

            'father_fname' => $request->father_first_name,
            'father_mname' => $request->father_middle_name,
            'father_lname' => $request->father_last_name,
            'mother_fname' => $request->mother_first_name,
            'mother_mname' => $request->mother_middle_name,
            'mother_lname' => $request->mother_last_name,
            'caste' => $request->caste_category,
            'marital_status' => $request->marital_status,
            'phy_hadi_status' => $request->phy_hadi_status,
            'fisherman_comm' => $request->fisherman_comm,
            'spouse_fname' => $request->spouse_first_name,
            'spouse_mname' => $request->spouse_middle_name,
            'spouse_lname' => $request->spouse_last_name,
            //'bpl_y_n' =>$request->if_bpl,
            'bpl_seq_no' => $request->bpl_seq_no,
            'bpl_id_no' => $request->bpl_id_no,
            'bpl_total_score' => $request->bpl_total_score,
            'mothly_income' => $request->monthly_income,

            'receive_pension' => $receive_pension,
            'social_security_pension' => $social_security_pension,

            'ration_card_cat' => $request->ration_card_cat,
            'ration_card_no'  => $request->ration_card_no,
            'ahl_tin'  => $request->ahl_tin,
            'aadhar_no'  => $request->aadhar_no,
            'epic_voter_id'  => $request->epic_voter_id,
            'pan_no'  => $request->pan_no,



            'dist_code' => $request->district,
            'assembly_code'  => $request->asmb_cons,
            'assembly_name' => $assembly_name,
            'rural_urban_id' => $request->urban_code,
            'police_station'  => $request->police_station,
            'block_ulb_code'  => $request->block,
            'block_ulb_name' => $block_ulb_name,
            'gp_ward_code' => $request->gp_ward,
            'gp_ward_name' => $gp_ward_name,
            'village_town_city'  => $request->village,
            'house_premise_no'  => $request->house,
            'post_office'  => $request->post_office,
            'pincode' => $request->pin_code,
            'residency_period' => $request->residency_period,
            'mobile_no'  => $request->mobile_no,
            'email' => $request->email,



            'bank_name'  => $request->name_of_bank,
            'branch_name'   => $request->bank_branch,
            'bank_code'    => $request->bank_account_number,
            'bank_ifsc'   => $request->bank_ifsc_code,
            'npci_bank_code'=> $new_bank_code,
            'nominate_name' => $request->nominate_name,
            'nominate_address' => $request->nominate_address,
            'nominate_relationship' => $request->nominate_relationship,
            'created_by' => $created_by,
            'created_by_level' => $mapping_level,
            'updated_at' => $c_time,
            'dup_bank' => 0,
            'dup_aadhar' => 0,
            'dup_mobile' => 0,
            'no_aadhar' => 0,
            'no_mobile' => 0,
            'is_reverted' => NULL
        ];

        $pr1 = "";
        $uploaded_doc = array();
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
        $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
        $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
        $doc_list = array_merge($doc_list_man, $doc_list_opt);

        $doc_master=DocumentType::get();
        $encolserdata = BenDocs::where('scheme_id',$scheme_id)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->get();
        $upload_file=array();
        $upload_file_arch=array();
        $delete_array=array();
        $i=0;
        $j=0;
        

        foreach ($doc_list as $doc) {
            if ($request->hasFile('doc_' . $doc)) {
            $doc_file = $request->file('doc_' . $doc);
            $img_data = file_get_contents($doc_file);
            $u_extension = $doc_file->getClientOriginalExtension();
            $mime_type = $doc_file->getMimeType();
            $doc_type_name =$doc_master->where('id', $doc)->first() ;
            if(strtolower($mime_type)=='image/jpeg'){
                if($u_extension=='jpg' || $u_extension=='jpeg'){
                    $extension=$u_extension;
                }
                else{
                $errors = array();
                $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
                array_push($errors, $errorMsg);
                return back()->with('errors', $errors)->withInput(Input::all());  
                }
            }
            else if(strtolower($mime_type)=='image/png'){
                $extension='png';
            }else if(strtolower($mime_type)=='image/gif'){
                $extension='gif';
            }else if(strtolower($mime_type)=='application/pdf'){
                $extension='pdf';
            }
            else{
                $errors = array();
                $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
                array_push($errors, $errorMsg);
                return back()->with('errors', $errors)->withInput(Input::all());  
            }
            if($u_extension!=$extension){
                $errors = array();
                $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
                array_push($errors, $errorMsg);
                return back()->with('errors', $errors)->withInput(Input::all());  
            }
            $base64 = base64_encode($img_data);
            $upload_file[$i]['beneficiary_id']=$request->id;
            $upload_file[$i]['created_by_dist_code']=$distCode;
            $upload_file[$i]['created_by_local_body_code']=$blockCode;
            $upload_file[$i]['document_type']=$doc;
            $upload_file[$i]['scheme_id']=$scheme_id;
            $upload_file[$i]['created_by_level']=$mapping_level;
            $upload_file[$i]['created_at']=$c_time;
            $upload_file[$i]['created_by']=$user_id;
            $upload_file[$i]['ip_address']=$request->ip();
            $upload_file[$i]['attched_document']=$base64;
            $upload_file[$i]['document_mime_type']=$mime_type;
            $upload_file[$i]['document_extension']=$extension;
            if(!empty($doc_type_name)){
             $upload_file[$i]['doc_type_name'] = $doc_type_name->doc_name;
            }
            $i++;
            $doc_already =$encolserdata->where('document_type',$doc)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->first();
            if(!empty($doc_already)){
                array_push($delete_array,$doc);
                $upload_file_arch[$j]['beneficiary_id']=$request->id;
                $upload_file_arch[$j]['created_by_dist_code']=$doc_already->created_by_dist_code;
                $upload_file_arch[$j]['created_by_local_body_code']=$doc_already->created_by_local_body_code;
                $upload_file_arch[$j]['document_type']=$doc_already->document_type;
                $upload_file_arch[$j]['scheme_id']=$doc_already->scheme_id;
                $upload_file_arch[$j]['created_by_level']=$doc_already->created_by_level;
                $upload_file_arch[$j]['created_at']=$doc_already->created_at;
                $upload_file_arch[$j]['created_by']=$doc_already->created_by;
                $upload_file_arch[$j]['ip_address']=$doc_already->ip_address;
                $upload_file_arch[$j]['attched_document']=$doc_already->attched_document;
                $upload_file_arch[$j]['document_mime_type']=$doc_already->document_mime_type;
                $upload_file_arch[$j]['document_extension']=$doc_already->document_extension;
                $j++;
            }
           }
        }
        DB::beginTransaction();
        DB::connection('pgsql_encwrite')->beginTransaction();
        DB::connection('pgsql6')->beginTransaction();
        try {
            $arch_status=DB::statement("INSERT INTO fisherman_oap.arc_beneficiary(id, 
            dist_code, ben_fname, ben_mname, ben_lname, gender, dob, ben_age, 
           caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
           mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, 
           ration_card_no, ahl_tin, aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, 
           bpl_total_score, dist_name, assembly_code, assembly_name, police_station, block_ulb_code, 
           block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, village_town_city, house_premise_no, 
           post_office, pincode, residency_period, mobile_no, email, bank_name, 
           branch_name, bank_old_code, bank_ifsc, created_at, updated_at, created_by, created_by_level, 
           created_by_dist_code, created_by_local_body_code, scheme_id, type_disability, 
           percentage_disability, certifying_auth, next_level_role_id, comments, nominate_name, 
           nominate_address, nominate_relationship, receive_pension, social_security_pension, 
           ration_card_cat, rural_urban_id, lot_generated, 
           bank_edited, bank_code, payment_count, last_paid_yymm, 
           av_status
           
           ) (SELECT id, 
            dist_code, ben_fname, ben_mname, ben_lname, gender, dob, ben_age, 
           caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
           mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, 
           ration_card_no, ahl_tin, aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, 
           bpl_total_score, dist_name, assembly_code, assembly_name, police_station, block_ulb_code, 
           block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, village_town_city, house_premise_no, 
           post_office, pincode, residency_period, mobile_no, email, bank_name, 
           branch_name, bank_old_code, bank_ifsc, created_at, updated_at, created_by, created_by_level, 
           created_by_dist_code, created_by_local_body_code, scheme_id, type_disability, 
           percentage_disability, certifying_auth, next_level_role_id, comments, nominate_name, 
           nominate_address, nominate_relationship, receive_pension, social_security_pension, 
           ration_card_cat, rural_urban_id, lot_generated, 
           bank_edited, bank_code, payment_count, last_paid_yymm, 
           av_status  
           from fisherman_oap.beneficiary where id=" . $id . ")");
            $is_update=PensionFisherman::where(['id' => $id, 'created_by_dist_code' => $distCode,  'scheme_id' => $scheme_id, 'next_level_role_id' => null])
                ->update($input);
            $pr1 = "fisherman";
            if(count($upload_file_arch)>0){
                $doc_inserted_arch = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_arch')->insert($upload_file_arch);
            }
            else{
                $doc_inserted_arch =1; 
            }
            if(count($delete_array)>0){
                $doc_inserted_del = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id',$request->id)->whereIn('document_type',$delete_array)->delete();
            }
            else{
                $doc_inserted_del =1;  
            }
            if(count($upload_file)>0){
                $doc_inserted = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($upload_file);
            }
            else{
                $doc_inserted =1;  
            }
            $accept_reject_model = new AcceptRejectInfo;
            $accept_reject_model->created_at = $c_time;
            $accept_reject_model->application_id = $request->id;
            $accept_reject_model->scheme_id =  $request->scheme_id;
            $accept_reject_model->user_id = $user_id;
            $accept_reject_model->op_type = 'APPUPDATE';
            $accept_reject_model->ip_address = $request->ip();
            $is_saved_log = $accept_reject_model->save();
            //dump($is_update); dump($doc_inserted_arch); dump($doc_inserted_del); dump($doc_inserted); dd($is_saved_log);
            if($arch_status && $is_update && $doc_inserted_arch && $doc_inserted_del && $doc_inserted && $is_saved_log){
                DB::commit();
                DB::connection('pgsql_encwrite')->commit();
                DB::connection('pgsql6')->commit();
                if ($designation_id == 'Operator')
                   return redirect("application-list-read-only-edit?pr1=" . $scheme_schema)->with('success', 'Application Updated Successfully')
                ->with('id',   $id);
              else {
                return redirect('/')->with('success', 'Application Updated Successfully');
                }
            }
            else{
                DB::connection('pgsql6')->rollback();
                DB::rollback();
                DB::connection('pgsql_encwrite')->rollback();
                if ($designation_id == 'Operator')
                 return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', array('Some error.Please try again'));
                else {
                  return redirect('/')->with('danger', 'Some error.Please try again');
                }
            }
           
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            DB::connection('pgsql_encwrite')->rollback();
            DB::connection('pgsql6')->rollback();
            if ($designation_id == 'Operator') {
                return redirect("application-list-read-only-edit?pr1=" . $scheme_schema)->with('error', 'Some error.Please try again')
                    ->with('id',  $row->getBenidAttribute());
            } else {
                return redirect('/')->with('error', 'Some error.Please try again');
            }
        }
      

        //return view('pension_view_details', ['row' => $row]);
    }



    public function show(Request $request)
    {
        //$id=> $request['id'];
        // $id=$request->input('id');
        // $single_employee_detail = nhm_employee_details::find($id);
        // return view('show_single_nhm_employee_details', ['single_employee_detail' => $single_employee_detail]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    /*******************************SD*************************/
    public function loadprogrammeHead($major_programme_head_id, $service_category)
    {

        $programmeHeads = programmeHeadMaster::where('major_programme_head_id', '=', $major_programme_head_id)->where('service_category_id', '=', $service_category)->get(['id', 'name']);

        //print_r($programmeHeads);
        // $programmeHeads=programmeHeadMaster::all();
        // dump( $programmeHeads);
        //Log::info('Showing user profile for user: '.$programmeHeads);

        return response()->json($programmeHeads);
        //return view('testview',['programmeHeads' => $programmeHeads]);
    }

    public function loadMajorprogrammeHead($major_programme_head_id)
    {

        $major_programme_heads = majorProgammeHeadMaster::all();

        //print_r($programmeHeads);
        // $programmeHeads=programmeHeadMaster::all();
        // dump( $programmeHeads);
        //Log::info('Showing user profile for user: '.$programmeHeads);

        return response()->json($major_programme_heads);
        //return view('testview',['programmeHeads' => $programmeHeads]);
    }



    public function loadDesignationList($programme_head_id, $service_category, $major_programme_head_id)
    {

        //$id = Auth::guard('api')->id;$id = Auth::guard('api')->user()->id;
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();

        $mappingLevel = $duty->mapping_level;

        if ($mappingLevel == "State HQ") {
            $level = "State";
        } else if ($mappingLevel == "District HQ") {

            $level = "District";
        } else {
            $is_urban = $duty->is_urban;
            if ($is_urban == 1) {

                $level = "ULB";
            } else {
                $level = "Block";
            }
        }

        $designationLists = designationMaster::where('programme_head_id', '=', $programme_head_id)->where('service_category_id', '=', $service_category)->where(
            'major_programme_head_id',
            '=',
            $major_programme_head_id
        )->where(
            'level',
            '=',
            $level
        )->get(['id', 'name']);


        //print_r($programmeHeads);
        // $programmeHeads=programmeHeadMaster::all();
        // dump( $programmeHeads);
        //Log::info('Showing user profile for user: '.$programmeHeads);

        return response()->json($designationLists);
        //return view('testview',['programmeHeads' => $programmeHeads]);
    }




    public function loadPostingPlace($posting_level)
    {

        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();


        $mappingLevel = $duty->mapping_level;
        if ($mappingLevel == "State HQ") {
            if ($posting_level == "MCH") {

                $facility_type = ["MCH"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code', '=', 342)->get(['facility_name as name', 'facilty_code as code']);
            } else if ($posting_level == "Other Hospital") {

                $facility_type = ["Others"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code', '=', 342)->get(['facility_name as name', 'facilty_code as code']);
            } elseif ($posting_level == "SPMU") {

                $postingPlaces = [array("code" => 1, "name" => "No Data")];
            } elseif ($posting_level == "State Drug Store") {

                $postingPlaces = [array("code" => 1, "name" => "No Data")];
            } elseif ($posting_level == "State Institute of Health and Family Welfare") {

                $postingPlaces = [array("code" => 1, "name" => "No Data")];
            } elseif ($posting_level == "SSH") {

                $facility_type = ["SSH"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code', '=', 342)->get(['facility_name as name', 'facilty_code as code']);
            }
        } else if ($mappingLevel == "District HQ") {

            $district_code = $duty->district_code;

            if ($posting_level == "ULB") {
                $postingPlaces = UrbanBody::where('district_code', '=', $district_code)->get(['urban_body_code as code', 'urban_body_name as name']);
            } else if ($posting_level == "UPHC") {

                $facility_type = ["UPHC"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code', '=', $district_code)->get(['facility_name as name', 'facilty_code as code']);
            } else if ($posting_level == "ACMOH Office") {

                $postingPlaces = SubDistrict::where('district_code', '=', $district_code)->get(['sub_district_code as code', 'sub_district_name as name']);
            } else if ($posting_level == "DH") {

                $facility_type = ["DH"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code', '=', $district_code)->get(['facility_name as name', 'facilty_code as code']);
            } else if ($posting_level == "SDH") {

                $facility_type = ["SDH"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code', '=', $district_code)->get(['facility_name as name', 'facilty_code as code']);
            } else if ($posting_level == "Other Hospital") {

                $facility_type = ["Others"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code', '=', $district_code)->get(['facility_name as name', 'facilty_code as code']);
            } else if ($posting_level == "SGH") {

                $facility_type = ["SGH"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code', '=', $district_code)->get(['facility_name as name', 'facilty_code as code']);
            } else if ($posting_level == "MCH") {


                $facility_type = ["MCH"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code', '=', $district_code)->get(['facility_name as name', 'facilty_code as code']);
            } else if ($posting_level == "SSH") {

                $facility_type = ["SSH"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code', '=', $district_code)->get(['facility_name as name', 'facilty_code as code']);
            } elseif ($posting_level == "CHC") {

                $facility_type = ["CH"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code', '=', $district_code)->get(['facility_name as name', 'facilty_code as code']);
            } elseif ($posting_level == "PHC") {

                $facility_type = ["PHC"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('district_code', '=', $district_code)->get(['facility_name as name', 'facilty_code as code']);
            } elseif ($posting_level == "DPMU") {

                $postingPlaces = [array("code" => 1, "name" => "No Data")];
            } elseif ($posting_level == "State Drug Store") {

                $postingPlaces = [array("code" => 1, "name" => "No Data")];
            }
        } else {
            // $is_urban = $duty->is_urban;
            if ($duty->is_urban == 1) {

                $urban_body_code = $duty->urban_body_code;

                if ($posting_level == "UPHC") {

                    $facility_type = ["UPHC"];
                    $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->where('taluka_code', '=', $urban_body_code)->get(['facility_name as name', 'facilty_code as code']);
                } elseif ($posting_level == "ULB") {

                    //$facility_type=["ULB"];
                    $postingPlaces = UrbanBody::get(['urban_body_code as code', 'urban_body_name as name']);
                } elseif ($posting_level == "CPMU") {

                    $postingPlaces = [array("code" => 1, "name" => "No Data")];
                }
            } else {

                $taluka_code = $duty->taluka_code;

                if ($posting_level == "Subcenter") {

                    $facility_type = ["SC"];
                    $postingPlaces = nhm_health_facility::where('taluka_code', '=', $taluka_code)->whereIn('facility_type', $facility_type)->get(['facility_name as name', 'facilty_code as code']);
                } elseif ($posting_level == "PHC") {

                    $facility_type = ["PHC"];
                    $postingPlaces = nhm_health_facility::where('taluka_code', '=', $taluka_code)->whereIn('facility_type', $facility_type)->get(['facility_name as name', 'facilty_code as code']);
                } elseif ($posting_level == "CHC") {

                    $facility_type = ["CH"];
                    $postingPlaces = nhm_health_facility::where('taluka_code', '=', $taluka_code)->whereIn('facility_type', $facility_type)->get(['facility_name as name', 'facilty_code as code']);
                } elseif ($posting_level == "BPMU") {

                    $postingPlaces = [array("code" => 1, "name" => "No Data")];
                }
            }
        }




        return response()->json($postingPlaces);
    }

    // public function loadPostingPlacedynamic($posting_level) {

    //      $user_id = AuthChecker::getUserId();
    //      $duty = Configduty::where('user_id','=',$user_id)->first();


    //       $mappingLevel=$duty->mapping_level;
    //         if($mappingLevel=="State HQ"){


    //             if($posting_level=="SPMU"){

    //                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 

    //             }elseif($posting_level=="State Drug Store"){

    //                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 

    //             }elseif($posting_level=="State Institute of Health and Family Welfare"){

    //                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 
    //             }else{

    //                 $postingPlaces = nhm_health_facility::whereIn('facility_type', $posting_level)->where('district_code','=',342)->get(['facility_name as name','facilty_code as code']);

    //             }
    //         }
    //         else if($mappingLevel=="District HQ"){

    //             $district_code = $duty->district_code;

    //             if($posting_level=="ULB"){
    //                     $postingPlaces =UrbanBody::where('district_code','=',$district_code)->get(['urban_body_code as code','urban_body_name as name']);


    //             }else if($posting_level=="ACMOH Office"){

    //                 $postingPlaces=SubDistrict::where('district_code','=',$district_code)->get(['sub_district_code as code','sub_district_name as name']);

    //             }elseif($posting_level=="DPMU"){

    //                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 

    //             }elseif($posting_level=="State Drug Store"){

    //                 $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 

    //             }else{

    //                 $postingPlaces=nhm_health_facility::whereIn('facility_type', $posting_level)->where('district_code','=',$district_code)->get(['facility_name as name','facilty_code as code']);
    //             }

    //          }  
    //         else{
    //             // $is_urban = $duty->is_urban;
    //             if($duty->is_urban==1){

    //                  $urban_body_code = $duty->urban_body_code;

    //                 if($posting_level=="UPSC"){

    //                     $facility_type=["UPHC"];
    //                     $postingPlaces = nhm_health_facility::where('taluka_code', '=', $urban_body_code)->get(['facility_name as name','facilty_code as code']);

    //                 }elseif($posting_level=="ULB"){

    //                     //$facility_type=["ULB"];
    //                     $postingPlaces =UrbanBody::get(['urban_body_code as code','urban_body_name as name']);

    //                 }

    //             }else{

    //                 $taluka_code = $duty->taluka_code;

    //                 if($posting_level=="BPMU"){

    //                     $postingPlaces=[array("code"=>1,"name"=>"No Data")]; 

    //                 }else{


    //                     $postingPlaces = nhm_health_facility::where('taluka_code', '=', $taluka_code)->where('facility_type','=',$posting_level)->get(['facility_name as name','facilty_code as code']);

    //                 }



    //                 }
    //             }




    //        return response()->json($postingPlaces);

    //     }





    public function verifydata(Request $request)
    {
        //$id=> $request['id'];

        //echo("inside Verify data");
        $id = $request->id;
        $Verified = "Verified";
        $Rejected = "Rejected";
        //$verifysubmit=$request->Verifysubmit;
        //print_r($verifysubmit);
        //$rejectsubmit=$request->Rejectsubmit;
        // print_r($rejectsubmit);
        $comments = $request->comments;

        if ($_POST['submit'] == 'Verify') {
            $input = [
                'verification_status' => $Verified, 'comments' => $comments
            ];

            $is_status_updated = nhm_employee_details::where('id', $id)
                ->update($input);
            //$nhm_employee_details = NHMEmployee::where('application_id','=',$id)->first();
            if ($is_status_updated) {

                return redirect("/")->with('success', 'Employee with Application ID:' . $id . ' is verified');
                // return redirect("/")->with('success', 'Employee Verified Successfully with Emp Code '.$nhm_employee_details->emp_code);
            }
        } else if ($_POST['submit'] == 'Reject') {
            $input = [
                'verification_status' => $Rejected, 'comments' => $comments
            ];
            $is_status_updated = nhm_employee_details::where('id', $id)
                ->update($input);
            if ($is_status_updated) {
                return redirect("/")->with('success', 'Employee with Application ID:' . $id . ' is rejected');
            }


            // if($verifysubmit!=null){
            //       $input = [
            //     'verification_status' => $verifysubmit,'comments' => $comments];
            // }else{
            //        $input = [
            //     'verification_status' => $rejectsubmit, 'comments' => $comments];
            // }
            //$id=$request->input('id');
            // $id=Input::get('id');

            //print_r($id);
            //$single_employee_details = nhm_employee_details::findOrFail($id);
            //print_r($single_employee_details);
            // $input = [
            //     'verification_status' => $request['verification_status']
            // ];

            // $is_status_updated=nhm_employee_details::where('id', $id)
            //     ->update($input);

            // $nhm_employee_details = NHMEmployee::where('application_id','=',$id)->first();

            // print_r($is_status_updated);
            // print_r("DONE");
            // if($is_status_updated){
            //     return redirect("/")->with('success', 'Employee Verified Successfully with Emp Code '.$nhm_employee_details->emp_code);
            // }       

        }
    }

    public function printSingleEmployee(Request $request)
    {
        //$id=> $request['id'];

        //echo("isnide show single");
        $id = $request->id;
        //$id=$request->input('id');
        // $id=Input::get('id');

        //print_r($id);
        $details = PensionSc::findOrFail($id);
        //print_r($single_employee_details);

        return view('print_single_nhm_employee_details', ['single_employee_details' => $details]);
    }

    public function admingetreports()
    {

        $created_employee_lists = NHMEmployee::paginate(500);
        //print_r($single_employee_details);

        return view('admingetreports_view', ['created_employee_lists' => $created_employee_lists]);
    }


    public function approve()
    {

        DB::enableQueryLog();
        $flag = false;
        $user_id = AuthChecker::getUserId();
        $dutys = Configduty::where('user_id', '=', $user_id)->get();
        //dd($duty);


        $i = 0;
        $body_codes = [];
        $is_active_status = [];

        foreach ($dutys as $duty) {

            if ($duty->mapping_level == "State HQ") {
                if ($duty->is_active == 1) {
                    $body_codes[$i] = 1;
                    $is_active_status[$i] = 1;
                } else {
                    $body_codes[$i] = null;
                }
            } else if ($duty->mapping_level == "District HQ") {
                if ($duty->is_active == 1) {
                    $body_codes[$i] = $duty->district_code;
                    $is_active_status[$i] = 1;
                } else {
                    $body_codes[$i] = null;
                }
            } else {
                //$nhm_employee_details->is_urban = $duty->is_urban;
                if ($duty->is_urban == 1) {
                    if ($duty->is_active == 1) {
                        $body_codes[$i] = $duty->urban_body_code;
                        $is_active_status[$i] = 1;
                    } else {
                        $body_codes[$i] = null;
                    }
                } else {
                    if ($duty->is_active == 1) {
                        $body_codes[$i] = $duty->taluka_code;
                        $is_active_status[$i] = 1;
                    } else {
                        $body_codes[$i] = null;
                    }
                }
            }
            $i++;
        }
        //dd(($body_codes));

        // $nhm_employee_details=DB::table('nhm_employee_details')->where('nhm_employee_details.body_code','=',$body_code)->where('nhm_employee_details.verification_status','=','Verified')->leftJoin('nhm_employees','nhm_employee_details.id','=','nhm_employees.application_id')->select('nhm_employee_details.*','nhm_employees.emp_code')->paginate(10);

        $nhm_employee_details = DB::table('nhm_employee_details')->where(function ($query) use ($body_codes) {
            foreach ($body_codes as $body_code) {
                $query->orWhere('nhm_employee_details.body_code', '=', $body_code);
            }
        })->where('nhm_employee_details.verification_status', '=', 'Verified')->leftJoin('nhm_employees', 'nhm_employee_details.id', '=', 'nhm_employees.application_id')->select('nhm_employee_details.*', 'nhm_employees.emp_code')->orderBy('nhm_employee_details.id') //get()//;
            ->paginate(500);
        //dd(DB::getQueryLog()); 
        //dd($nhm_employee_details); 
        /*********************************************OLD code till 21-01-2020********************/
        //  $flag=false;
        //  $user_id = AuthChecker::getUserId();
        //  $duty = Configduty::where('user_id','=',$user_id)->first();

        //  if($duty->mapping_level=="State HQ"){
        //      $body_code = 1;
        //  }else if($duty->mapping_level=="District HQ"){
        //      $body_code = $duty->district_code;
        //  }else{

        //      if($duty->is_urban==1){
        //          $body_code = $duty->urban_body_code;
        //      }else{
        //          $body_code = $duty->taluka_code;
        //  }   
        // } 



        //   //$nhm_employee_details = NHMEmployee::where('body_code','=',$body_code)->paginate(10);//

        //  $nhm_employee_details=DB::table('nhm_employee_details')->where('nhm_employee_details.body_code','=',$body_code)->where('nhm_employee_details.verification_status','=','Verified')->leftJoin('nhm_employees','nhm_employee_details.id','=','nhm_employees.application_id')->select('nhm_employee_details.*','nhm_employees.emp_code')->paginate(10);


        // $nhm_employee_details = nhm_employee_details::where('body_code','=',$body_code)->paginate(10);
        /********************************************************************************************************/
        if (empty($is_active_status)) {
            return redirect("/")->with('success', 'User Disabled');
        } else {

            return view('approve_nhm_employee_details', ['nhm_employee_details' => $nhm_employee_details, 'flag' => $flag]);
        }
    }



    public function showSingleEmployeeApproval(Request $request)
    {

        $id = $request->id;

        $single_employee_details = nhm_employee_details::find($id);

        if ($single_employee_details->approval_status == "Approved") {
            $single_employee_details = NHMEmployee::where('application_id', '=', $id)->first();
        }

        // return Redirect::back()->with(['single_employee_details'=>$single_employee_details,'flag'=>$flag]);




        return view('show_single_nhm_employee_details_Approval', ['single_employee_details' => $single_employee_details]);
    }



    public function approvedata(Request $request)
    {

        $id = $request->id;
        $Approved = "Approved";
        $Disapproved = "Disapproved";

        $comments = $request->comments;

        if ($_POST['submit'] == 'Approve') {

            $input = [
                'approval_status' => $Approved, 'approval_comments' => $comments
            ];

            $is_status_updated = nhm_employee_details::where('id', $id)
                ->update($input);
            // dd($is_status_updated);
            $nhm_employee_details = NHMEmployee::where('application_id', '=', $id)->first();
            //$nhm_employee_details = NHMEmployee::where('id','=',$id)->first();
            $mobileNo = $nhm_employee_details->mobile_number_1;

            if ($is_status_updated) {

                $smsObj = new SmsSendController();
                //$smsObj->initiateSmsActivation($mobileNo,"NHM employee Code: ".$nhm_employee_details->emp_code." has been generated. Preserve it for further reference");

                $is_sms_sent = ['is_sms_sent' => 1];

                //$nhm_sms= nhm_employee_details::where('id','=', $id)->update($is_sms_sent);

                $nhm_sms1 = NHMEmployee::where('application_id', '=', $id)->update($is_sms_sent);



                return redirect("/")->with('success', 'Employee Approved Successfully with Emp Code ' . $nhm_employee_details->emp_code);
                //return redirect("/")->with('success', 'Employee Approved Successfully with Emp Code '.$nhm_employee_details->emp_code);
            }
        } else if ($_POST['submit'] == 'Disapprove') {
            // dd('hi');
            $input = [
                'approval_status' => $Disapproved, 'approval_comments' => $comments
            ];

            $is_status_updated = nhm_employee_details::where('id', $id)
                ->update($input);
            //dd($is_status_updated);
            if ($is_status_updated) {

                return redirect("/")->with('success', 'Employee with Application ID:' . $id . ' is Not Approved');
            }
        }
    }

    public function MassEmployeeApproval(Request $request)
    {
        $inputs = request()->input('approvalcheck');
        $Approved = "Approved";
        $comments = "Bulk Approval";
        $data = [
            'approval_status' => $Approved, 'approval_comments' => $comments
        ];

        foreach ($inputs as $input) {
            $is_status_updated = nhm_employee_details::where('id', $input)->update($data);
        }
        if ($is_status_updated) {
            return redirect("/")->with('success', 'Employee Records Approved Successfully');
        }
        //dd($inputs);
    }

    private function validateInput($request, $scheme_id, $add_edit_code)
    {
        $rules = [
            //'first_name' => 'required|string|max:200',
            'first_name' => 'required|string|max:200',
            'middle_name' => 'string|nullable',
            'last_name' => 'string|nullable|max:200',
            'gender' => 'required',
            // 'dob' => '',
            'txt_age' => 'required|numeric',

            'father_first_name' => 'required|string|max:200',
            'father_middle_name' => 'string|nullable',
            'father_last_name' => 'string|nullable|max:200',
            'mother_first_name' => 'required|string|max:200',
            'mother_middle_name' => 'string|nullable',
            'mother_last_name' => 'string|nullable|max:200',
            'caste_category' => 'required',
            'marital_status' => 'required',

            'spouse_first_name' => 'string|nullable',
            'spouse_middle_name' => 'string|nullable',
            'spouse_last_name' => 'string|nullable',
            // 'if_bpl' => ,
            'bpl_seq_no' => 'string|nullable|max:12',
            'bpl_id_no' => 'string|nullable|max:12',
            'bpl_total_score' => 'integer|nullable',
            'monthly_income' => 'required|numeric|between: 0.00,999999.99',


            'ration_card_cat' => 'string|nullable',
            'ration_card_no' => 'string|nullable|max:11',

            'ahl_tin' => 'string|nullable|max:100',
            'aadhar_no' => 'required|numeric|digits:12',
            'epic_voter_id' => 'required|string|max:20',
            'pan_no' => 'string|nullable|max:12',



            //  'district' => 'string',
            'asmb_cons' => 'required|string',
            'police_station' => 'required|string',
            //'block' => 'max:200',
            // 'gp_ward' => 'max:200',
            'village' => 'required|string|max:300',
            'house' => 'string|nullable',
            'post_office' => 'required|string',
            'pin_code' => 'required|numeric|digits:6',
            'residency_period' => 'required|integer',
            'mobile_no' => 'required|numeric|digits:10',
            'email' => 'string|email|nullable',



            'name_of_bank' => 'required|string|max:200',
            'bank_branch' => 'required|string|max:200',
            'bank_account_number' => 'required|numeric',
            'bank_ifsc_code' => 'required|string',
        ];
        //dd($rules);
        $attributes = array();
        $messages = array();
        $attributes['first_name'] = 'Beneficiary First Name';
        $attributes['middle_name'] = 'Beneficiary Middle Name';
        $attributes['last_name'] = 'Beneficiary Last Name';
        $attributes['gender'] = 'Gender';
        $attributes['dob'] = 'Date of Birth';
        $attributes['txt_age'] = 'Age (as on 01/01/2020)';
        $attributes['father_first_name'] = 'Father First Name';
        $attributes['father_middle_name'] = 'Father Middle Name';
        $attributes['father_last_name'] = 'Father Last Name';
        $attributes['mother_first_name'] = 'Mother First Name';
        $attributes['mother_middle_name'] = 'Mother Middle Name';
        $attributes['mother_last_name'] = 'Mother Last Name';
        $attributes['caste_category'] = 'Caste';
        $attributes['marital_status'] = 'Marital Status';
        $attributes['spouse_first_name'] = 'Spouse First Name';
        $attributes['spouse_middle_name'] = 'Spouse Middle Name';
        $attributes['spouse_last_name'] = 'Spouse Last Name';
        $attributes['monthly_income'] = 'Monthly Family Income (In Rs)';
        $attributes['ration_card_cat'] = 'Digital Ration Card Number';
        $attributes['ration_card_no'] = 'Digital Ration Card Number';
        $attributes['ahl_tin'] = 'AHL TIN';
        $attributes['aadhar_no'] = 'Aadhaar Number';
        $attributes['epic_voter_id'] = 'EPIC/Voter Id number';
        $attributes['pan_no'] = 'PAN';
        $attributes['bpl_seq_no'] = 'BPL Seq Number (if avaiable)';
        $attributes['bpl_id_no'] = 'BPL Id Number (if avaiable)';
        $attributes['bpl_total_score'] = 'BPL Total Score (if avaiable)';
        $attributes['district'] = 'District';
        $attributes['asmb_cons'] = 'Assembly Constituency';
        $attributes['urban_code'] = 'Rural/Urban';
        $attributes['block'] = 'Block/Municipality/Corp';
        $attributes['gp_ward'] = 'GP/Ward No.';
        $attributes['village'] = 'Village/Town/City';
        $attributes['house'] = 'House/Premise Number';
        $attributes['post_office'] = 'Post Office';
        $attributes['pin_code'] = 'Pin Code';
        $attributes['police_station'] = 'Police Station';
        $attributes['residency_period'] = 'Number of years Dwelling in WB';
        $attributes['mobile_no'] = 'Mobile Number';
        $attributes['email'] = 'Email Id';
        $attributes['bank_ifsc_code'] = 'IFS Code';
        $attributes['name_of_bank'] = 'Bank Name';
        $attributes['bank_branch'] = 'Bank Branch Name';
        $attributes['bank_account_number'] = 'Bank Account No.';
        $doc_id_list = SchemeDocMap::select('doc_list_man')->where('scheme_code', $scheme_id)->first();
        $in_array = json_decode($doc_id_list->doc_list_man);
        $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->get();
        $messages = array();
        foreach ($doc_list as $key => $value) {
            if (in_array($value->id,  $in_array)) {
                if ($add_edit_code == 1) {
                    $required = 'required';
                } else {
                    $required = 'nullable';
                }
            } else {
                $required = 'nullable';
            }
            $rules['doc_' . $value->id] = $required . '|mimes:' . $value->doc_type . '|max:' . $value->doc_size_kb . ',';
            $messages['doc_' . $value['id'] . '.max'] = "The file uploaded for " . $value->doc_name . " size must be less than " . $value->doc_size_kb . " KB";
            $messages['doc_' . $value['id'] . '.mimes'] = "The file uploaded for " . $value->doc_name . " must be of type " . $value->doc_type;
            $messages['doc_' . $value['id'] . '.required'] = "Document for " . $value->doc_name . " must be uploaded";
        }
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        $return_arr = array('is_valid' => false, 'errors' => array());
        if (!$validator->passes()) {
            $error_msg = array();
            foreach ($validator->errors()->all() as $error) {
                array_push($error_msg, $error);
            }

            //dd($error_msg);
            $return_arr['is_valid'] = false;
            $return_arr['errors'] = $error_msg;
        } else {
            $return_arr['is_valid'] = true;
        }
        return $return_arr;
    }
    public function isAadharValid($num)
    {
        settype($num, "string");
        $expectedDigit = substr($num, -1);
        $actualDigit = $this->CheckSumAadharDigit(substr($num, 0, -1));
        return ($expectedDigit == $actualDigit) ? $expectedDigit == $actualDigit : 0;
    }

    function CheckSumAadharDigit($partial)
    {
        $dihedral = array(
            array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
            array(1, 2, 3, 4, 0, 6, 7, 8, 9, 5),
            array(2, 3, 4, 0, 1, 7, 8, 9, 5, 6),
            array(3, 4, 0, 1, 2, 8, 9, 5, 6, 7),
            array(4, 0, 1, 2, 3, 9, 5, 6, 7, 8),
            array(5, 9, 8, 7, 6, 0, 4, 3, 2, 1),
            array(6, 5, 9, 8, 7, 1, 0, 4, 3, 2),
            array(7, 6, 5, 9, 8, 2, 1, 0, 4, 3),
            array(8, 7, 6, 5, 9, 3, 2, 1, 0, 4),
            array(9, 8, 7, 6, 5, 4, 3, 2, 1, 0)
        );
        $permutation = array(
            array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
            array(1, 5, 7, 6, 2, 8, 3, 0, 9, 4),
            array(5, 8, 0, 3, 7, 9, 6, 1, 4, 2),
            array(8, 9, 1, 6, 0, 4, 3, 5, 2, 7),
            array(9, 4, 5, 3, 1, 2, 6, 8, 7, 0),
            array(4, 2, 8, 6, 5, 7, 3, 9, 0, 1),
            array(2, 7, 9, 3, 8, 0, 6, 4, 1, 5),
            array(7, 0, 4, 6, 9, 1, 3, 2, 5, 8)
        );

        $inverse = array(0, 4, 3, 2, 1, 5, 6, 7, 8, 9);
        settype($partial, "string");
        $partial = strrev($partial);
        $digitIndex = 0;
        for ($i = 0; $i < strlen($partial); $i++) {
            $digitIndex = $dihedral[$digitIndex][$permutation[($i + 1) % 8][$partial[$i]]];
        }
        return $inverse[$digitIndex];
    }
}
