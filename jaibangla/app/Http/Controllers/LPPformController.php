<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Http\Controllers\Redirect;
use App\Configduty;
use App\District;
use App\UrbanBody;
use App\SubDistrict;
use App\PensionLPPRetainer;
use App\PensionLPPPensioner;
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
use App\BenDocs;
use App\AcceptRejectInfo;
use App\BenEntry;
use App\Traits\TraitCasteCertificateValidate;
use App\Traits\TraitLifeCertificateValidate;
use App\Traits\TraitAadharValidate;
use App\Helpers\AuthChecker;
use App\SchemeStepRank;
use Illuminate\Support\Facades\Route;

class LPPformController extends Controller
{
    use TraitCasteCertificateValidate;
    use TraitLifeCertificateValidate;
    use TraitAadharValidate;

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function schemeSelect(Request $request)
    {
        try {
            $user_id = AuthChecker::getUserId();
            if (AuthChecker::ApproverPermission()) {
                $schemes = DB::select(DB::raw("select id,scheme_name,display_name,is_active from m_scheme where id IN (8,9) and   id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
                //dd($schemes);
                return view(
                    'lpp/Schemeselection',
                    [

                        'scheme_list' => $schemes,
                    ]
                );
            } else {
                return redirect("/")->with('danger', 'Not Allowed');
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect("/")->with('danger', 'Not Allowed');
        }
    }
    public function index(Request $request)
    {
        $this->middleware('auth');
        //dd($designation_id);
        $user_id = AuthChecker::getUserId();

        $scheme_id = $request->scheme_id;
        if (!ctype_digit($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Not Valid');
        }
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (empty($scheme_obj)) {
            return redirect("/")->with('danger', 'Scheme Not Found');
        }
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
        $district_code = $duty_obj->district_code;
        if (empty($duty_obj)) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        if (!AuthChecker::ApproverPermission()) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        $district_arr = District::where('district_code', $district_code)->first();
        $assembly_list = Assembly::where('district_code', $district_code)->get();

        //dd($assembly_list);
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
        return view('lpp/pension_details', [
            'scheme_id' => $scheme_id,
            'doc_list_man' => $doc_list_man,
            'doc_list_opt' => $doc_list_opt,
            'profile_img' => $doc_profile_image_id,
            'scheme_details' => $scheme_obj,
            'district_arr' => $district_arr,
            'assembly_list' => $assembly_list
        ]);


    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->middleware('auth');
        $user_id = AuthChecker::getUserId();
        $scheme_id = $request->scheme_id;
        if (!ctype_digit($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Not Valid');
        }
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (empty($scheme_obj)) {
            return redirect("/")->with('danger', 'Scheme Not Found');
        }
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
        $district_code = $duty_obj->district_code;
        if (empty($duty_obj)) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        if (!AuthChecker::ApproverPermission()) {
            return redirect("/")->with('danger', 'Not Allowed');
        }

        $server_ip = $_SERVER['SERVER_ADDR'];
        $base_url = url('/');
        $uploaded_doc = array();
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
        $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
        $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
        $doc_list = array_merge($doc_list_man, $doc_list_opt);

        $isValidarr = $this->validateInput($request, $scheme_id, 1);
        if ($isValidarr['is_valid'] == false) {
            return back()->with('errors', $isValidarr['errors'])->withInput(Input::all());
        }
        if (!empty($request->aadhar_no)) {
            if ($this->isAadharValid(trim($request->aadhar_no)) == false) {
                $errors = array();
                $errorMsg = "Aadhaar Number Invalid";
                array_push($errors, $errorMsg);
                return back()->withErrors($errors)->withInput();
            }
        }

        $ifsc = trim($request->bank_ifsc_code);
        $bank_account_number = trim($request->bank_account_number);
        $bank_branch = trim($request->bank_branch);
        $name_of_bank = trim($request->name_of_bank);
        $row_count_bank = BankDetails::whereraw("trim(branch)='$bank_branch'")->whereraw("trim(ifsc)='$ifsc'")->where('is_active', 1)->whereraw("trim(bank)='$name_of_bank'")->count();
        //$bank_details = BankDetails::whereraw("trim(ifsc)='$ifsc'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
        $bank_details = BankDetails::where('ifsc', trim($ifsc))->where('is_active', 1)->get(['bank', 'branch', 'bank_code'])->first();
        $new_bank_code = $bank_details->bank_code;
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
        if (!preg_match('/^[0-9]{10}+$/', $request->mobile_no)) {
            $errors = array();
            $errorMsg = "Mobile Number Invalid";
            array_push($errors, $errorMsg);
            return back()->with('errors', $errors)->withInput(Input::all());
        }
        if ($request->mobile_no < 1000000000) {
            $errors = array();
            $errorMsg = "Mobile Number Invalid";
            array_push($errors, $errorMsg);
            return back()->with('errors', $errors)->withInput(Input::all());
        }
        $errormsg = array();
        if (!empty($bank_account_number) && !empty($ifsc)) {
            //    $bank_count = DB::table($schema . '.ben_bank_account_no_unique')->where('bank_code',$bank_account_number)->count('bank_code');
            $benDuplicateAcCount1 = DB::table("pension.beneficiaries")->select('id')
                ->whereRaw("trim(bank_code) = trim(" . "'" . $bank_account_number . "'" . ")");

            $bank_count = DB::table("pension.beneficiaries")->select('id')
                ->whereRaw("trim(bank_code) = trim(" . "'" . $bank_account_number . "'" . ")")
                ->union($benDuplicateAcCount1)->get()
                ->count('id');
            if ($bank_count > 0) {
                $is_error = 1;
                array_push($errormsg, 'Bank A/C Already Exist!');
            }
        }
        if (!empty($request->aadhar_no)) {
            $aadhar_count = DB::table('pension.beneficiaries')->where('aadhar_no', trim($request->aadhar_no))->whereIn('is_clean', [1, 2])->where('scheme_id', 3)->count('aadhar_no');
            if ($aadhar_count > 0) {
                $is_error = 1;
                array_push($errormsg, 'Aadhaar Number Already Exist! Please try different.');
            }
        }
        if (!empty($request->mobile_no)) {
            $mobile_count = DB::table('pension.beneficiaries')->where('mobile_no', $request->mobile_no)->whereIn('is_clean', [1, 2])->where('scheme_id', 3)->count('mobile_no');
            if ($mobile_count > 0) {
                $is_error = 1;
                array_push($errormsg, 'Mobile Number Already Exist! Please try different.');
            }
        }
        $mydate = date('Y-m-d');

        if ($scheme_id == 8) {
            $diff = Carbon::parse($request->dob)->diffInYears($mydate);
            if ($diff < 18 || $diff > 60) {
                $is_error = 1;
                array_push($errormsg, 'Dob is Incorrect.');
            }
        }
        if ($scheme_id == 9) {
            $diff = Carbon::parse($request->dob)->diffInYears($mydate);
            if ($diff < 60) {
                array_push($errormsg, 'Dob is Incorrect.');
            }
        }
        if (count($errormsg) > 0) {

            return redirect("lpp?scheme_id=" . $scheme_id)->withInput(Input::all())->with('errors', $errormsg);


        }
        $body = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
        $c_time = date('Y-m-d H:i:s');
       
        $pension_details = new BenEntry();
       
        $pension_details->entry_datetime = $c_time;
        $pension_details->ip_address = $request->ip();
        $pension_details->update_datetime = $c_time;

        //Document Dynamic
        $upload_file = array();
        $i = 0;
        $doc_master = DocumentType::get();
        // dd($request->file);
        foreach ($doc_list as $doc) {
            if ($request->hasFile('doc_' . $doc)) {
                $doc_file = $request->file('doc_' . $doc);
                $img_data = file_get_contents($doc_file);
                $u_extension = $doc_file->getClientOriginalExtension();
                $mime_type = $doc_file->getMimeType();
                $doc_type_name = $doc_master->where('id', $doc)->first();
                if (strtolower($mime_type) == 'image/jpeg') {
                    if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
                        $extension = $u_extension;
                    } else {
                        $errors = array();
                        $errorMsg = "You are trying to upload an incorrect file for " . $doc_type_name->doc_name;
                        array_push($errors, $errorMsg);
                        return back()->with('errors', $errors)->withInput(Input::all());
                    }

                } else if (strtolower($mime_type) == 'image/png') {
                    $extension = 'png';
                } else if (strtolower($mime_type) == 'image/gif') {
                    $extension = 'gif';
                } else if (strtolower($mime_type) == 'application/pdf') {
                    $extension = 'pdf';
                } else {
                    $errors = array();
                    $errorMsg = "You are trying to upload an incorrect file for " . $doc_type_name->doc_name;
                    array_push($errors, $errorMsg);
                    return back()->with('errors', $errors)->withInput(Input::all());
                }
                if ($u_extension != $extension) {
                    $errors = array();
                    $errorMsg = "You are trying to upload an incorrect file for " . $doc_type_name->doc_name;
                    array_push($errors, $errorMsg);
                    return back()->with('errors', $errors)->withInput(Input::all());
                }
                if ($request->urban_code == 1) {
                    $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
                    $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();

                    $pension_details->block_ulb_name = $block_ulb->urban_body_name;
                    $pension_details->gp_ward_name = $gp_ward->urban_body_ward_name;
                    $created_by_local_body_code = $block_ulb->sub_district_code;

                } else {
                    $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
                    $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();

                    $pension_details->block_ulb_name = $block_ulb->block_name;
                    $pension_details->gp_ward_name = $gp_ward->gram_panchyat_name;
                    $created_by_local_body_code = $block_ulb->block_code;
                }
                $base64 = base64_encode($img_data);
                $upload_file[$i]['created_by_dist_code'] = $district_code;
                $upload_file[$i]['created_by_local_body_code'] = $created_by_local_body_code;
                $upload_file[$i]['document_type'] = $doc;
                $upload_file[$i]['scheme_id'] = $scheme_id;
                $upload_file[$i]['created_by_level'] = $duty_obj->mapping_level;
                $upload_file[$i]['created_at'] = $c_time;
                $upload_file[$i]['created_by'] = $user_id;
                $upload_file[$i]['ip_address'] = $request->ip();
                $upload_file[$i]['attched_document'] = $base64;
                $upload_file[$i]['document_mime_type'] = $mime_type;
                $upload_file[$i]['document_extension'] = $extension;
                if (!empty($doc_type_name)) {
                    $upload_file[$i]['doc_type_name'] = $doc_type_name->doc_name;
                }
                $i++;
            }
        }
        //Document Dynamic End



        $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
        $assembly_name = $assembly->ac_name;

        if ($request->receive_pension != "") {
            $receive_pension = implode(',', $request->receive_pension);
            $pension_details->receive_pension = $receive_pension;
        }

        if ($request->social_security_pension != "") {
            $social_security_pension = implode(',', $request->social_security_pension);
            $pension_details->social_security_pension = $social_security_pension;
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
        // $pension_details->fisherman_comm=$request->fisherman_comm;

        $pension_details->marital_status = $request->marital_status;
        $pension_details->mothly_income = $request->monthly_income;

        $pension_details->spouse_fname = $request->spouse_first_name;
        $pension_details->spouse_mname = $request->spouse_middle_name;
        $pension_details->spouse_lname = $request->spouse_last_name;

        $pension_details->ration_card_cat = $request->ration_card_cat;
        $pension_details->ration_card_no = $request->ration_card_no;
        //$pension_details->ahl_tin  = $request->ahl_tin;
        $pension_details->aadhar_no = $request->aadhar_no;
        $pension_details->epic_voter_id = $request->epic_voter_id;
        $pension_details->pan_no = $request->pan_no;
        //$pension_details->bpl_seq_no = $request->bpl_seq_no;
        //$pension_details->bpl_id_no = $request->bpl_id_no;
        // $pension_details->bpl_total_score = $request->bpl_total_score;

        $pension_details->dist_code = $request->district;
        $pension_details->rural_urban_id = $request->urban_code;
        $pension_details->assembly_code = $request->asmb_cons;
        $pension_details->assembly_name = $assembly_name;
        $pension_details->police_station = $request->police_station;
        $pension_details->block_ulb_code = $request->block;
        $pension_details->gp_ward_code = $request->gp_ward;
        $pension_details->village_town_city = $request->village;
        $pension_details->house_premise_no = $request->house;
        $pension_details->post_office = $request->post_office;
        $pension_details->pincode = $request->pin_code;
        $pension_details->residency_period = $request->residency_period;
        $pension_details->mobile_no = $request->mobile_no;
        //$pension_details->email = $request->email;

        $pension_details->bank_name = $request->name_of_bank;
        $pension_details->branch_name = $request->bank_branch;
        $pension_details->bank_code = $request->bank_account_number;
        $pension_details->bank_ifsc = $request->bank_ifsc_code;
        $pension_details->npci_bank_code = $new_bank_code;

        $pension_details->nominate_name = $request->nominate_name;
        $pension_details->nominate_address = $request->nominate_address;
        $pension_details->nominate_relationship = $request->nominate_relationship;

        $pension_details->created_by = Auth::user()->id;
        $pension_details->created_by_level = $duty_obj->mapping_level;
        $pension_details->created_by_dist_code = $district_code;
        $pension_details->created_by_local_body_code = $created_by_local_body_code;
        $pension_details->scheme_id = $request->scheme_id;
       
        $next_level_role_id = SchemeStepRank::getSchemeParentId($scheme_id, 1);
        $pension_details->next_level_role_id = $next_level_role_id;
        DB::beginTransaction();
       
        DB::connection('pgsql_encwrite')->beginTransaction();
        try {

            $is_saved = $pension_details->save();
            $beneficiary_id = $pension_details->id;
            if ($is_saved) {
                foreach ($upload_file as $key => $csm) {
                    $upload_file[$key]['beneficiary_id'] = $beneficiary_id;
                }
                $doc_inserted = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($upload_file);
                if ($doc_inserted) {
                    if ($scheme_id == 8) {
                        DB::commit();
                    } else if ($scheme_id == 9) {
                        DB::commit();
                    }

                    DB::connection('pgsql_encwrite')->commit();
                    $ben_fullname = trim($request->first_name) . ' ' . trim($request->middle_name) . ' ' . trim($request->last_name);

                    // $this->bioauthcheckInsert($request->session()->get('distCode'),$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$created_by_local_body_code,$user_id);
                    // if(($request->caste_category=='SC' || $request->caste_category=='ST') && !empty($request->caste_certificate_no)){
                    //  $this->casteInfoCheckInsert($request->session()->get('distCode'),$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->caste_certificate_no),$created_by_local_body_code,$user_id);
                    // }

                    // $this->RationcheckInsert($request->session()->get('distCode'),$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$created_by_local_body_code,$user_id);

                    try {
                        $this->bioauthcheckInsert($request->session()->get('distCode'), $beneficiary_id, $scheme_id, $ben_fullname, $request->ip(), trim($request->aadhar_no), $created_by_local_body_code, $user_id);
                    } catch (\Exception $e) {
                        $inputMain['life_certificate_checked'] = -1;
                        $upadated_main = DB::table($schema . '.beneficiary')
                            ->where([
                                'id' => $beneficiary_id,
                                'created_by_local_body_code' => $created_by_local_body_code,
                                'created_by_dist_code' => $district_code
                            ])->update($inputMain);
                    }
                    try {
                        if (($request->caste_category == 'SC' || $request->caste_category == 'ST') && !empty($request->caste_certificate_no)) {
                            $this->casteInfoCheckInsert($district_code, $beneficiary_id, $scheme_id, $ben_fullname, $request->ip(), trim($request->caste_certificate_no), $created_by_local_body_code, $user_id);
                        }
                    } catch (\Exception $e) {
                        $inputMain['caste_certificate_checked'] = -1;
                        $upadated_main = DB::table($schema . '.beneficiary')
                            ->where([
                                'id' => $beneficiary_id,
                                'created_by_local_body_code' => $created_by_local_body_code,
                                'created_by_dist_code' => $district_code
                            ])->update($inputMain);
                    }

                    try {
                        $data = $this->RationcheckInsert($district_code, $beneficiary_id, $scheme_id, $ben_fullname, $request->ip(), trim($request->aadhar_no), $created_by_local_body_code, $user_id, $request->dob);
                    } catch (\Exception $e) {
                        $inputMain['aadhaar_no_checked'] = -1;
                        $upadated_main = DB::table($schema . '.beneficiaries')
                            ->where([
                                'id' => $request->id,
                                'created_by_local_body_code' => $created_by_local_body_code,
                                'created_by_dist_code' => $district_code
                            ])->update($inputMain);
                    }

                    $ben_details = DB::table($schema . '.beneficiaries')->where('id', $beneficiary_id)->first();
                    if ($ben_details) {
                        $caste_certificate_checked = $ben_details->caste_certificate_checked;
                        $caste_certificate_validation_message = $ben_details->caste_certificate_validation_message;
                        $caste_certificate_check_lastdatetime = $ben_details->caste_certificate_check_lastdatetime;
                        $caste_matched_with_certificate_no = $ben_details->caste_matched_with_certificate_no;
                        $life_certificate_checked = $ben_details->life_certificate_checked;
                        $life_certificate_pass = $ben_details->life_certificate_pass;
                        $life_certificate_lastdatetime = $ben_details->life_certificate_lastdatetime;
                        $last_biometric = $ben_details->last_biometric;
                        $aadhaar_no_checked = $ben_details->aadhaar_no_checked;
                        $aadhaar_no_checked_lastdatetime = $ben_details->aadhaar_no_checked_lastdatetime;
                        $aadhaar_no_checked_pass = $ben_details->aadhaar_no_checked_pass;
                        $aadhaar_no_validation_msg = $ben_details->aadhaar_no_validation_msg;
                        return redirect("lpp?scheme_id=" . $scheme_id)->with('success', 'Application Submitted Successfully')
                            ->with('id', $beneficiary_id)
                            ->with('caste_certificate_checked', $caste_certificate_checked)
                            ->with('caste_certificate_validation_message', $caste_certificate_validation_message)
                            ->with('caste_certificate_check_lastdatetime', $caste_certificate_check_lastdatetime)
                            ->with('caste_matched_with_certificate_no', $caste_matched_with_certificate_no)
                            ->with('life_certificate_checked', $life_certificate_checked)
                            ->with('life_certificate_pass', $life_certificate_pass)
                            ->with('life_certificate_lastdatetime', $life_certificate_lastdatetime)
                            ->with('last_biometric', $last_biometric)
                            ->with('aadhaar_no_checked', $aadhaar_no_checked)
                            ->with('aadhaar_no_checked_lastdatetime', $aadhaar_no_checked_lastdatetime)
                            ->with('aadhaar_no_checked_pass', $aadhaar_no_checked_pass)
                            ->with('aadhaar_no_validation_msg', $aadhaar_no_validation_msg);
                    }
                } else {
                    $error_found = 1;
                }
            } else {
                $error_found = 1;

            }
            if ($error_found) {
                if ($scheme_id == 8) {
                    DB::rollback();
                } else if ($scheme_id == 9) {
                    DB::rollback();
                }
                DB::connection('pgsql_encwrite')->rollback();

                return redirect("lpp?scheme_id=" . $scheme_id)->withInput(Input::all())->with('errors', array('Some error.Please try again'));
            }

        } catch (\Exception $e) {
            //    dd($e);
            if ($scheme_id == 8) {
                DB::rollback();
            } else if ($scheme_id == 9) {
                DB::rollback();
            }
            DB::connection('pgsql_encwrite')->rollback();

            return redirect("lpp?scheme_id=" . $scheme_id)->withInput(Input::all())->with('errors', array('Some error.Please try again'));
        }
    }





    private function validateInput($request, $scheme_id, $add_edit_code)
    {
        $rules = [
            'first_name' => 'required|string|max:200',
            'middle_name' => 'string|nullable',
            'last_name' => 'required|string|max:200',
            'gender' => 'required',
            // 'dob' => '',
            'txt_age' => 'numeric',

            'father_first_name' => 'required|string|max:200',
            'father_middle_name' => 'string|nullable',
            'father_last_name' => 'required|string|max:200',
            'mother_first_name' => 'string|nullable|max:200',
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
            'epic_voter_id' => 'string|nullable|max:20',
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
            if (in_array($value->id, $in_array)) {
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

    private function validateInputold($request, $scheme_id)
    {
        // print_r($arr);exit;

        $doc_id_list = SchemeDocMap::select('doc_list_man')->where('scheme_code', $scheme_id)->first();

        $in_array = json_decode($doc_id_list->doc_list_man);

        $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->get();


        $singleArray = array();
        $nicenameArray = array();
        $customMessage = array();
        foreach ($doc_list as $key => $value) {

            if (in_array($value->id, $in_array)) {
                $required = 'required';
            } else {
                $required = 'nullable';
            }


            // $multiArray[$val->id]= array('id'=>$val->id,'required'=>$required,'mime'=>$val->doc_type, 'size'=>$val->doc_size_kb);
            $singleArray['doc_' . $value->id] = $required . '|mimes:' . $value->doc_type . '|max:' . $value->doc_size_kb . ',';
            $nicenameArray['doc_' . $value->id] = $value->doc_name . ',';
            $customMessage['doc_' . $value->id . '.max'] = "The file uploaded for :attribute size must be less than :max KB";
            $customMessage['doc_' . $value->id . '.mimes'] = "The file uploaded for :attribute must be of type " . $value->doc_type;
            $customMessage['doc_' . $value->id . '.required'] = "Document for :attribute must be uploaded";
        }

        //echo "<pre>";print_r($singleArray);exit;

        // $singleArray = array();

        // foreach ($multiArray as $key => $value){
        // $singleArray['doc_'.$key] = $value['required'].'|mimes:'.$value['mime'].'|max:'.$value['size'].',';
        // } 



        $this->validate($request, array_merge([
            //'first_name' => 'required|string|max:200',
            'first_name' => 'required|string|max:200',
            'middle_name' => 'string|nullable',
            'last_name' => 'required|string|max:200',
            'gender' => 'required',
            // 'dob' => '',
            'txt_age' => 'numeric',

            'father_first_name' => 'required|string|max:200',
            'father_middle_name' => 'string|nullable',
            'father_last_name' => 'required|string|max:200',
            'mother_first_name' => 'string|nullable|max:200',
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
            'epic_voter_id' => 'string|nullable|max:20',
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



        ], $singleArray), $customMessage, $nicenameArray);
    }
    /**********************************************************/
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
    public function schemelistforUpdateEdit(Request $request)
    {
        $userId = AuthChecker::getUserId();
        // Fetch all schemes assigned to the user
        $assignedSchemes = DB::table('duty_assignement')
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->pluck('scheme_id');

        // Fetch scheme details for the assigned schemes
        $scheme_list = DB::table('m_scheme')
            ->whereIn('id', $assignedSchemes)
            ->where('is_active', 1)
            ->orderBy('rank')
            ->get();

        // Filter scheme names to only include "LPP Retainer" and "LPP Pensioner"
        $allowedSchemeNames = ["LPP Retainer", "LPP Pensioner"];
        $filteredSchemeList = $scheme_list->filter(function ($scheme) use ($allowedSchemeNames) {
            return in_array($scheme->scheme_name, $allowedSchemeNames);
        });

        // Convert the filtered list back to an array
        $filteredSchemeList = $filteredSchemeList->values()->all();

        $data = ['scheme_list' => $filteredSchemeList];
        // dd($data);
        return view('lpp.schemelistforUpdatellp', $data);
    }

    public function applicationdetailsReadOnlyLpp(Request $request)
    {
        try {
            $id = $request->id;
            $scheme_id = $request->scheme_id;
            if (!is_numeric($id)) {
                return redirect("/")->with('danger', 'Applicant ID Not Valid');
            }
            $is_active = 0;
            $user_id = AuthChecker::getUserId();

            $duty = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();

            if ($duty->mapping_level == 'Department') {
                $is_active = 1;
            } else {
                $distCode = $duty->district_code;
                $is_active = $duty->is_active;
            }

            if ($is_active == 0) {
                return redirect("/")->with('danger', 'User Disabled');
            }
            $is_state_login = 0;
            $docs = array();
            $row = null;

            if ($scheme_id == 8) {
                $row = BenEntry::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();
                $schema = 'lokprasar_retainer';
            } else if ($scheme_id == 9) {
                $row = BenEntry::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();
                $schema = 'lokprasar_pensioner';

                $docs = collect([]);
            }
            if (empty($row)) {
                return redirect("/")->with('danger', 'Not Allowed');
            }

            $district_name = "";
            $block_name = "";
            $gp_name = "";

            if ($row->dist_code != "") {
                $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
                $district_name = $district->district_name;
            }

            if ($row->block_ulb_code != "") {
                if ($row->rural_urban_id == 1) {
                    $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
                    $block_name = $block->urban_body_name;
                } else {
                    $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
                    $block_name = $block->block_name;
                }
            }
            if ($row->gp_ward_code != "") {
                if ($row->rural_urban_id == 1) {
                    $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
                    $gp_name = $gp_ward->urban_body_ward_name;
                } else {
                    $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
                    $gp_name = $gp->gram_panchyat_name;
                }
            }
            $doc_profile_image = DocumentType::get()
                ->where("is_profile_pic", true)->first();
            $doc_profile_image_id = 999;
            if ($doc_profile_image) {
                $doc_profile_image_id = $doc_profile_image->id;
            }

            if ($is_state_login) {
                $district_state = District::where('district_code', '=', $row->created_by_dist_code)->get(['district_code', 'district_name'])->first();
                $district_state_name = trim($district_state->district_name);
                $row->district_state_name = $district_state_name;
                if ($row->block_ulb_type == 1) {
                    $sdo_state = SubDistrict::where('sub_district_code', '=', $row->created_by_local_body_code)->get(['sub_district_code', 'sub_district_name'])->first();
                    $block_subdiv_state_name = trim($sdo_state->sub_district_name);
                } else {
                    // dd($row->created_by_local_body_code);
                    $block_state = Taluka::where('block_code', '=', $row->created_by_local_body_code)->first();
                    $block_subdiv_state_name = trim($block_state->block_name);
                }
                $row->block_subdiv_state_name = $block_subdiv_state_name;
            } else {
                $row->district_state_name = '';
                $row->urban_code_state_name = '';
                $row->block_subdiv_state_name = '';
            }

            if ($scheme_id == 8) {
                return view('lpp/pension_view_details_read_only', ['schema' => $schema, 'row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
            } elseif ($scheme_id == 9) {
                return view('lpp/pension_view_details_read_only', ['schema' => $schema, 'row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
            }

        } catch (\Exception $e) {
            //dd($e);
            return redirect("/")->with('error', 'Some error.please try again ......');
        }
    }
    public function editList(Request $request)
    {
        // return redirect("/")->with('error', 'Temporarily Suspended for Development.');
        // dd($request->all());
        $user_id = AuthChecker::getUserId();
        $scheme_id = $request->id;
        $scheme_row = Scheme::where('is_active', 1)->where('id', $scheme_id)->first();

        if (!$scheme_row) {
            // dd('ok');
            return redirect("/")->with('error', 'Parameter not valid'); // Handle missing scheme row gracefully.
        }

        $scheme_name = $scheme_row->scheme_name;
        $schema_name = $scheme_row->short_code;
        $scheme_length = $scheme_row->scheme_length;
        $id_length = $scheme_row->id_length;

        // $is_active = 0;
        $roleArray = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
        $is_active = $roleArray->is_active;
        $is_urban = $roleArray->is_urban;
        $distCode = $roleArray->district_code;
        // dd($is_active);
        if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disabled');
        }
        $whereCon = array();
        $whereCon['scheme_id']=$scheme_id;
        $whereCon['created_by_dist_code']=$distCode;
        $whereCon['is_approved']=0;
        $whereCon['is_rejected']=0;
        
        $is_reverted = $request->is_reverted;
        if($is_reverted==1){
            $whereCon['is_reverted']=1;
        }
        $report_type_name = 'Application List which are not yet verified or approved';
        $data = DB::table('pension.beneficiaries')->where($whereCon)->whereNull('update_datetime')->get(['id',
                    'created_by_dist_code',
                    'bank_code',
                    'ben_fname',
                    'ben_lname',
                    'ben_mname',
                    'gender',
                    'ben_age',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'caste',
                    'is_reverted',
                    'update_datetime']);
                   
       
            // dd('okk');
            return view(
                'lpp/editList_lpp',
                [
                    'district_code' => $distCode,
                    'scheme_id' => $scheme_id,
                    // 'pr1' => $request->pr1,
                    // 'parameter' => $request->parameter,
                    'scheme_name' => $scheme_name,
                    'report_type_name' => $report_type_name,
                    'data'=>$data
                    // 'is_urban' => $is_urban

                ]
            );
        
    }


    public function applicationupdate(Request $request)
    {

        $base_url = url('/');
        $id = (int) $request->id;
        //   dd($id);
        $scheme_id = (int) $request->scheme_id;
        if (!is_int($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Code Not Valid');
        }
        if (!is_numeric($id)) {
            //dd('okkk');
            return redirect("/")->with('error', 'Applicant ID Not Valid');
        }
        $created_by = Auth::user()->id;
        $is_active = 0;
        $mapping_level = NULL;
        $roleArray = Configduty::where('user_id', $created_by)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
        if (empty($roleArray)) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        if (!AuthChecker::ApproverPermission()) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        $distCode = $roleArray->district_code;
        $mapping_level_from_role = $roleArray->mapping_level;

        $is_active = $roleArray->is_active;
        $is_urban = $request->urban_code;
        if ($is_urban == 1) {
            $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
            $blockCode = $block_ulb->sub_district_code;
        } else {
            $blockCode = $request->block;
        }

        if ($is_active == 0) {
            return redirect("/")->with('danger', 'User Disabled');
        }
        if (empty($mapping_level_from_role) || empty($distCode)) {
            return redirect("/")->with('danger', 'User Disabled');
        }

        $checkmodel = ($scheme_id == 8) ? 'App\\PensionLPPRetainer' : (($scheme_id == 9) ? 'App\\PensionLPPPensioner' : null);
        $query = $checkmodel::where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id]);
        if (AuthChecker::ApproverPermission()) {
            $query = $query->whereNull('next_level_role_id');
        }
        $row = $query->first();
        $row->toArray();
        if (empty($row)) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        $isValidarr = $this->validateInput($request, $scheme_id, 2);
        if ($isValidarr['is_valid'] == false) {
            return redirect("/application-edit-lpp?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $isValidarr['errors']);
        }
        $scheme_row = Scheme::where('id', $scheme_id)->first();
        if (empty($scheme_row)) {
            // dd("schemerow");
            return redirect("/")->with('error', 'User Disabled');
        }
        $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
        if (!empty($request->mobile_no)) {
            $mobile_count = $checkmodel::where('mobile_no', trim($request->mobile_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');

            if ($mobile_count > 0) {
                $errors = array();
                $errorMsg = "Mobile Number Already Exist! Please try different.";
                array_push($errors, $errorMsg);
                return redirect("/application-edit-lpp?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
            }
        }
        //--------- Duplicate bank A/C check---------- //
        //   $bankCount = $checkmodel::whereRaw("trim(bank_code)=trim(" . "'" . $request->bank_account_number . "'" . ")")->where('id', '!=', $id)
        //       ->whereRaw("(" . $check_condition_str . ")")
        //       ->count('id');
        $benDuplicateAcCount1 = DB::connection('pgsql_mis')->table("lokprasar_retainer.beneficiary")->select('id')->where('id', '!=', $id)
            ->whereRaw("trim(bank_code) = trim(" . "'" . $request->bank_account_number . "'" . ")");

        $bankCount = DB::connection('pgsql_mis')->table("lokprasar_pensioner.beneficiary")->select('id')->where('id', '!=', $id)
            ->whereRaw("trim(bank_code) = trim(" . "'" . $request->bank_account_number . "'" . ")")
            ->union($benDuplicateAcCount1)->get()
            ->count('id');


        if ($bankCount > 0) {
            $errors = array();
            $errorMsg = "Bank A/C Already Exist!";
            array_push($errors, $errorMsg);
            return redirect("/application-edit-lpp?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
        }
        $count = $checkmodel::where('aadhar_no', trim($request->aadhar_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
        if ($count > 0) {
            $request->session()->put('dupAadhaarCheck', trim($request->aadhar_no));
            $errors = array();
            $errorMsg = "Aadhaar Number Already Exist! Please try different.";
            array_push($errors, $errorMsg);
            return redirect("/application-edit-lpp?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('dupAadhaar', 1)->with('errors', $errors);
        }
        $ifsc = trim($request->bank_ifsc_code);
        $bank_branch = trim($request->bank_branch);
        $name_of_bank = trim($request->name_of_bank);
        $row_count_bank = BankDetails::whereraw("trim(branch)='$bank_branch'")->whereraw("trim(ifsc)='$ifsc'")->where('is_active', 1)->whereraw("trim(bank)='$name_of_bank'")->count();
        //$bank_details = BankDetails::whereraw("trim(ifsc)='$ifsc'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
        $bank_details = BankDetails::where('ifsc', trim($ifsc))->where('is_active', 1)->get(['bank', 'branch', 'bank_code'])->first();
        $new_bank_code = $bank_details->bank_code;
        if ($row_count_bank == 0) {
            $errors = array();
            $errorMsg = "Bank IFSC and Bank Name Not Match!";
            array_push($errors, $errorMsg);
            return redirect("/application-edit-lpp?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
        }
        $mydate = date('Y-m-d');
        $errormsg = array();
        if ($scheme_id == 8) {
            $diff = Carbon::parse($request->dob)->diffInYears($mydate);
            // dd($diff);
            if ($diff < 18 || $diff > 60) {
                $is_error = 1;
                array_push($errormsg, 'Dob is Incorrect.');
            }
        }
        if ($scheme_id == 9) {
            $diff = Carbon::parse($request->dob)->diffInYears($mydate);
            if ($diff < 60) {
                array_push($errormsg, 'Dob is Incorrect.');
            }
        }
        if (count($errormsg) > 0) {

            return redirect("/application-edit-lpp?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errormsg);


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
            $gp_ward_name = $gp_ward->urban_body_ward_name;
        } else {
            $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
            $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();

            $block_ulb_name = $block_ulb->block_name;
            $gp_ward_name = $gp_ward->gram_panchyat_name;
        }
        $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
        $assembly_name = $assembly->ac_name;

        if (trim($request->marital_status) != "Married") {
            $request->spouse_first_name = "";
            $request->spouse_middle_name = "";
            $request->spouse_last_name = "";
        }


        if ($request->urban_code == 1) {
            $state_created_by_local_body_code = $block_ulb->sub_district_code;
        } else {
            $state_created_by_local_body_code = $request->block;
        }
        $urban_code_state = null;
        $state_created_by_dist_code = $distCode;
        $state_created_by_local_body_code = $blockCode;

        $c_time = date('Y-m-d H:i:s');
        $user_id = AuthChecker::getUserId();
        $input = [
            'ben_fname' => $request->first_name,
            'ben_mname' => $request->middle_name,
            'ben_lname' => $request->last_name,
            'gender' => $request->gender,
            'father_fname' => $request->father_first_name,
            'father_mname' => $request->father_middle_name,
            'father_lname' => $request->father_last_name,
            'mother_fname' => $request->mother_first_name,
            'mother_mname' => $request->mother_middle_name,
            'mother_lname' => $request->mother_last_name,
            'caste' => $request->caste_category,
            'marital_status' => $request->marital_status,
            'spouse_fname' => $request->spouse_first_name,
            'spouse_mname' => $request->spouse_middle_name,
            'spouse_lname' => $request->spouse_last_name,
            'mothly_income' => $request->monthly_income,
            'receive_pension' => $receive_pension,
            'social_security_pension' => $social_security_pension,
            'ration_card_cat' => $request->ration_card_cat,
            'ration_card_no' => $request->ration_card_no,
            'ahl_tin' => $request->ahl_tin,
            'epic_voter_id' => $request->epic_voter_id,
            'pan_no' => $request->pan_no,
            'assembly_code' => $request->asmb_cons,
            'dob' => $request->dob,
            'mobile_no' => $request->mobile_no,
            'assembly_code' => $request->asmb_cons,
            'assembly_name' => $assembly_name,
            'police_station' => $request->police_station,
            'block_ulb_name' => $block_ulb_name,
            'gp_ward_name' => $gp_ward_name,
            'village_town_city' => $request->village,
            'house_premise_no' => $request->house,
            'post_office' => $request->post_office,
            'pincode' => $request->pin_code,
            'residency_period' => $request->residency_period,
            'email' => $request->email,
            'aadhar_no' => $request->aadhar_no,
            'bank_name' => $request->name_of_bank,
            'branch_name' => $request->bank_branch,
            'bank_code' => $request->bank_account_number,
            'bank_ifsc' => $request->bank_ifsc_code,
            'npci_bank_code' => $new_bank_code,
            'dist_code' => $distCode,
            'rural_urban_id' => $request->urban_code,
            'block_ulb_code' => $request->block,
            'gp_ward_code' => $request->gp_ward,
            'nominate_name' => $request->nominate_name,
            'nominate_address' => $request->nominate_address,
            'nominate_relationship' => $request->nominate_relationship,
            'av_status' => $request->av_status,
            'receiving_pension_other_source_1' => $request->receiving_pension_other_source_1,
            'receiving_pension_other_source_2' => $request->receiving_pension_other_source_2,
            'created_by' => $created_by,
            'created_by_level' => $mapping_level,
            'created_by_dist_code' => $state_created_by_dist_code,
            'created_by_local_body_code' => $state_created_by_local_body_code,
            'block_ulb_type' => $urban_code_state,
            'updated_at' => $c_time,
            'dup_bank' => 0,
            'dup_aadhar' => 0,
            'dup_mobile' => 0,
            'no_aadhar' => 0,
            'no_mobile' => 0,
            'is_reverted' => NULL,
        ];

        if (!empty(trim($request->caste_certificate_no))) {
            $input['caste_certificate_no'] = $request->caste_certificate_no;
        }
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (empty($scheme_obj)) {
            return redirect("/")->with('danger', 'Scheme Not Found');
        }
        if (!empty($scheme_obj->short_code)) {
            $schema = $scheme_obj->short_code;
            $scheme_length = $scheme_obj->scheme_length;
            $id_length = $scheme_obj->id_length;
        } else {
            $schema = "pension";
        }

        $uploaded_doc = array();
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
        $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
        $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
        $doc_list = array_merge($doc_list_man, $doc_list_opt);

        $doc_master = DocumentType::get();
        $encolserdata = BenDocs::where('scheme_id', $scheme_id)->where('created_by_dist_code', $distCode)->where('beneficiary_id', $request->id)->get();
        $uploaded_by_ben = [];
        foreach ($encolserdata as $record) {
            if (isset($record->document_type)) {
                $uploaded_by_ben[] = $record->document_type;
            }
        }
        $uploading_docs = $request->allFiles();
        $ready_to_upload_docs = array_map(function ($key) {
            return str_replace("doc_", "", $key);
        }, array_keys($uploading_docs));
        $mergedArray = array_merge($ready_to_upload_docs, $uploaded_by_ben);
        $distinctValues = array_unique($mergedArray);
        $missingDocuments = array_diff($doc_list_man, $distinctValues);
        $missdoc = implode(', ', $missingDocuments);
        if (!empty($missingDocuments)) {
            $query = "SELECT doc_name FROM public.m_attached_doc WHERE id IN ($missdoc)";
            $results = DB::select($query);
            $errors = array();
            $missingDocNames = array_column($results, 'doc_name');
            if (!empty($missingDocNames)) {
                $errorMsg = "Please upload the following mandatory documents: ";
                $errorMsg .= "<ul>";
                foreach ($missingDocNames as $docName) {
                    $errorMsg .= "<li>$docName</li>";
                }

                $errorMsg .= "</ul>";

                array_push($errors, $errorMsg);
                return redirect("/application-edit-lpp?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
            }
        }
        $upload_file = array();
        $upload_file_arch = array();
        $delete_array = array();
        $i = 0;
        $j = 0;

        foreach ($doc_list as $doc) {
            if ($request->hasFile('doc_' . $doc)) {
                $doc_type_name = $doc_master->where('id', $doc)->first();
                $doc_file = $request->file('doc_' . $doc);
                $img_data = file_get_contents($doc_file);
                $u_extension = $doc_file->getClientOriginalExtension();
                $mime_type = $doc_file->getMimeType();
                if (strtolower($mime_type) == 'image/jpeg') {
                    if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
                        $extension = $u_extension;
                    } else {
                        $errors = array();
                        $errorMsg = "You are trying to upload an incorrect file for " . $doc_type_name->doc_name;
                        array_push($errors, $errorMsg);
                        return back()->with('errors', $errors)->withInput(Input::all());
                    }
                } else if (strtolower($mime_type) == 'image/png') {
                    $extension = 'png';
                } else if (strtolower($mime_type) == 'image/gif') {
                    $extension = 'gif';
                } else if (strtolower($mime_type) == 'application/pdf') {
                    $extension = 'pdf';
                } else {
                    $errors = array();
                    $errorMsg = "You are trying to upload an incorrect file for " . $doc_type_name->doc_name;
                    array_push($errors, $errorMsg);
                    return back()->with('errors', $errors)->withInput(Input::all());
                }
                if ($u_extension != $extension) {
                    $errors = array();
                    $errorMsg = "You are trying to upload an incorrect file for " . $doc_type_name->doc_name;
                    array_push($errors, $errorMsg);
                    return back()->with('errors', $errors)->withInput(Input::all());
                }
                $base64 = base64_encode($img_data);
                $upload_file[$i]['beneficiary_id'] = $request->id;
                $upload_file[$i]['created_by_dist_code'] = $distCode;
                $upload_file[$i]['created_by_local_body_code'] = $blockCode;
                $upload_file[$i]['document_type'] = $doc;
                $upload_file[$i]['scheme_id'] = $scheme_id;
                $upload_file[$i]['created_by_level'] = $mapping_level;
                $upload_file[$i]['created_at'] = $c_time;
                $upload_file[$i]['created_by'] = $user_id;
                $upload_file[$i]['ip_address'] = $request->ip();
                $upload_file[$i]['attched_document'] = $base64;
                $upload_file[$i]['document_mime_type'] = $mime_type;
                $upload_file[$i]['document_extension'] = $extension;
                if (!empty($doc_type_name)) {
                    $upload_file[$i]['doc_type_name'] = $doc_type_name->doc_name;
                }
                $i++;
                $doc_already = $encolserdata->where('document_type', $doc)->where('created_by_dist_code', $distCode)->where('beneficiary_id', $request->id)->first();
                if (!empty($doc_already)) {
                    array_push($delete_array, $doc);
                    $upload_file_arch[$j]['beneficiary_id'] = $request->id;
                    $upload_file_arch[$j]['created_by_dist_code'] = $doc_already->created_by_dist_code;
                    $upload_file_arch[$j]['created_by_local_body_code'] = $doc_already->created_by_local_body_code;
                    $upload_file_arch[$j]['document_type'] = $doc_already->document_type;
                    $upload_file_arch[$j]['scheme_id'] = $doc_already->scheme_id;
                    $upload_file_arch[$j]['created_by_level'] = $doc_already->created_by_level;
                    $upload_file_arch[$j]['created_at'] = $doc_already->created_at;
                    $upload_file_arch[$j]['created_by'] = $doc_already->created_by;
                    $upload_file_arch[$j]['ip_address'] = $doc_already->ip_address;
                    $upload_file_arch[$j]['attched_document'] = $doc_already->attched_document;
                    $upload_file_arch[$j]['document_mime_type'] = $doc_already->document_mime_type;
                    $upload_file_arch[$j]['document_extension'] = $doc_already->document_extension;
                    $j++;
                }
            }
        }
        DB::beginTransaction();
        DB::connection('pgsql_encwrite')->beginTransaction();

        try {
            $arch_status = DB::statement("INSERT INTO $scheme_schema.arc_beneficiary(id, 
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
               av_status,   legacy_import, old_beneficiary_id, pensioner_type, 
               receiving_pension_other_source_1, receiving_pension_other_source_2
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
               av_status,   legacy_import, old_beneficiary_id, pensioner_type, 
               receiving_pension_other_source_1, receiving_pension_other_source_2 from $scheme_schema.beneficiary where id=" . $id . ")");
            $is_update = DB::table($scheme_schema . '.beneficiary')->where(['id' => $request->id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id])->update($input);
            if (count($upload_file_arch) > 0) {
                $doc_inserted_arch = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_arch')->insert($upload_file_arch);
            } else {
                $doc_inserted_arch = 1;
            }
            if (count($delete_array) > 0) {
                $doc_inserted_del = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $request->id)->whereIn('document_type', $delete_array)->delete();
            } else {
                $doc_inserted_del = 1;
            }
            if (count($upload_file) > 0) {
                $doc_inserted = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($upload_file);
            } else {
                $doc_inserted = 1;
            }
            $accept_reject_model = new AcceptRejectInfo;
            $accept_reject_model->created_at = $c_time;
            $accept_reject_model->application_id = $request->id;
            $accept_reject_model->scheme_id = $request->scheme_id;
            $accept_reject_model->user_id = $user_id;
            $accept_reject_model->op_type = 'APPUPDATE';
            $accept_reject_model->ip_address = $request->ip();
            $accept_reject_model->op_type = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod() . '@APPUPDATE';

            $is_saved_log = $accept_reject_model->save();
            // dump($is_update); dump($doc_inserted_arch); dump($doc_inserted_del); dump($doc_inserted); dd($is_saved_log);
            if ($arch_status && $is_update && $doc_inserted_arch && $doc_inserted_del && $doc_inserted && $is_saved_log) {
                DB::commit();
                DB::connection('pgsql_encwrite')->commit();

                return redirect('/application-list-read-only-edit-lpp?pr1=' . $scheme_schema)->with('id', $request->id)->with('success', 'Application Updated Successfully');
            } else {
                DB::connection('pgsql_encwrite')->rollback();
                DB::rollback();
                // return redirect("/application-edit-lpp?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
                return redirect('/')->with('danger', 'Some error.Please try again');
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            DB::connection('pgsql_encwrite')->rollback();
            //return redirect("/application-edit-lpp?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
            return redirect('/')->with('danger', 'Some error.Please try again');

        }
    }
    public function applicationeditview(Request $request)
    {

        $user_id = AuthChecker::getUserId();
        $id = $request->id;
        $scheme_id = (int) $request->scheme_id;
        if (!is_int($scheme_id)) {
            return redirect("/")->with('danger', 'Scheme Code Not Valid');
        }
        if (!is_numeric($id)) {
            return redirect("/")->with('danger', 'Applicant ID Not Valid');
        }
        $row = array();
        if ($scheme_id == 8) {
            $model_name = 'App\\PensionLPPRetainer';
        } else if ($scheme_id == 9) {
            $model_name = 'App\\PensionLPPPensioner';
        }
        $is_active = 0;

        $roleArray = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
        if (empty($roleArray)) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        if (AuthChecker::ApproverPermission()) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        $distCode = $roleArray->district_code;

        $is_active = $roleArray->is_active;
        if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disabled');
        }
        $query = $model_name::where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id]);
        if (AuthChecker::ApproverPermission()) {
            $query = $query->whereNull('next_level_role_id');
        }
        $row = $query->first();
        $districts = District::where('is_revenue_district', '=', '1')->get(['district_code', 'district_name']);
        $scheme_row = Scheme::where('id', $scheme_id)->first();
        $scheme_name = $scheme_row->scheme_name;
        $assemly_list = collect([]);
        $block_munc_list = collect([]);
        $gp_ward_list = collect([]);
        if (!empty($row->dist_code)) {
            $assemly_list = Assembly::where('district_code', '=', $row->dist_code)->get(['ac_no', 'ac_name']);
            if ($row->rural_urban_id == 1) {
                $block_munc_list = UrbanBody::where('district_code', '=', $row->dist_code)->get(['urban_body_code as code', 'urban_body_name as val']);
                if (!empty($row->block_ulb_code)) {
                    $gp_ward_list = Ward::where('urban_body_code', '=', $row->block_ulb_code)->get(['urban_body_ward_code as code', 'urban_body_ward_name as val']);
                }
            } else {
                $block_munc_list = Taluka::where('district_code', '=', $row->dist_code)->get(['block_code as code', 'block_name as val']);
                if (!empty($row->block_ulb_code)) {
                    $gp_ward_list = GP::where('block_code', '=', $row->block_ulb_code)->get(['gram_panchyat_code as code', 'gram_panchyat_name as val']);
                }
            }
        }
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
        $doc_list_man = DocumentType::get()->whereIn("id", json_decode($doc_id_list[0]['doc_list_man']));
        $doc_list_opt = DocumentType::get()->whereIn("id", json_decode($doc_id_list[0]['doc_list_opt']));
        $doc_profile_image = DocumentType::get()->where("is_profile_pic", true)->first();

        $doc_profile_image_id = 999;
        if ($doc_profile_image) {
            $doc_profile_image_id = $doc_profile_image->id;
        }

        return view('lpp/lpp_edit_form', [
            'assemly_list' => $assemly_list,
            'block_munc_list' => $block_munc_list,
            'gp_ward_list' => $gp_ward_list,
            'scheme_name' => $scheme_name,
            'row' => $row,
            'districts' => $districts,
            'scheme_id' => $scheme_id,
            'doc_list_man' => $doc_list_man,
            'doc_list_opt' => $doc_list_opt,
            'profile_img' => $doc_profile_image_id,
            'doc_list_man' => $doc_list_man,
            'doc_list_opt' => $doc_list_opt,
            'profile_img' => $doc_profile_image_id,

        ]);
    }
    public function applicationReject(Request $request)
    {
        // dd($request->all());
        $user_id = AuthChecker::getUserId();
        $c_time = date('Y-m-d H:i:s', time());
        $accept_reject_model = new AcceptRejectInfo;
        $accept_reject_model->created_at = $c_time;
        $accept_reject_model->application_id = $request->id;
        $accept_reject_model->scheme_id = $request->scheme_id;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->created_by_dist_code = $request->district_code;
        // $accept_reject_model->created_by_local_body_code = $request->block_code;
        $accept_reject_model->ip_address = request()->ip();
        $accept_reject_model->module_name = class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod() .'@AR';
        $accept_reject_model->op_type ='AR';

        $scheme_obj = Scheme::where('id', $request->scheme_id)->where('is_active', 1)->first();
        if (!empty($scheme_obj->short_code)) {
            $schema = $scheme_obj->short_code;

            $scheme_length = $scheme_obj->scheme_length;
            $id_length = $scheme_obj->id_length;
        } else {
            $schema = "pension";
            $scheme_length = NULL;
            $id_length = NULL;
        }
        DB::beginTransaction();

        $is_saved_log = $accept_reject_model->save();
        if($is_saved_log){
            $benEntry_model = BenEntry::where('id', $request->id)->where('scheme_id',  $request->scheme_id)->where('created_by_dist_code', $request->district_code)->whereNotNull('bank_code')->where('is_approved',0)->first();
            $benEntry_model->next_level_role_id = -1;
            $benEntry_model->is_rejected = 1;
            $benEntry_model->is_verified = 2;
            $benEntry_model->is_approved = 2;
            $benEntry_model->rejected_date = $c_time;
            $benEntry_model->rejected_by = $user_id;
            $benEntry_model->is_clean = 10;
            $benEntry_model->action_ip_address = $request->ip();
            $benEntry_model->action_type = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
            $is_update = $benEntry_model->save();
        }
        // dump($is_saved_log);dump($is_update);die();
        if ($is_saved_log && $is_update) {
            DB::commit();
            return redirect("application-list-read-only-edit-lpp?id=".$request->scheme_id)->with('success', 'Rejected Succesfully!')
                ->with('id', $request->id);
        } else {
            DB::rollback();
            return redirect("application-list-read-only-edit-lpp?id=".$request->scheme_id)->with('errors', 'Error! Please try again.');
        }
    }

}
