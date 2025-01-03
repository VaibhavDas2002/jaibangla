<?php

namespace App\Http\Controllers;

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
use App\PensionSc;
use App\PensionSt;
use App\PensionFisherman;
use App\PensionMSME;
use App\PensionTextile;

use App\PensionManabikWCD;
use App\PensionOAPWCD;
use App\PensionWPWCD;


use App\PensionOAPFarmer;
use App\BenDocsOAPFarmer;
use App\BenDocsArcOAPFarmer;


use App\PensionOAPST;


//Dynamic Doc
use App\BenDocsSc;
use App\BenDocsSt;
use App\BenDocsFisherman;
use App\BenDocsMSME;
use App\BenDocsTextile;

use App\BenDocsManabikWCD;
use App\BenDocsOAPWCD;
use App\BenDocsWPWCD;

use App\BenDocsArcSc;
use App\BenDocsArcSt;
use App\BenDocsArcFisherman;
use App\BenDocsArcMSME;
use App\BenDocsArcTextile;


use App\BenDocsArcManabikWCD;
use App\BenDocsArcOAPWCD;
use App\BenDocsArcWPWCD;

use App\PensionPurohitMonthlyICAD;
use App\BenDocsPurohitMonthlyICAD;
use App\BenDocsArcPurohitMonthlyICAD;

use App\PensionPurohitHousingICAD;
use App\BenDocsPurohitHousingICAD;
use App\BenDocsArcPurohitHousingICAD;

use App\SchemecodeStatic;

use App\DocumentType;
use App\SchemeDocMap;
//Dynamic Doc End
use App\Manabik;
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
use App\BankDetails;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Validator;
use App\DsPhase;
use App\BenDocs;
use Illuminate\Support\Facades\Storage;
use App\AcceptRejectInfo;
use App\BenEntry;
use App\Helpers\AuthChecker;
use App\Traits\TraitAadharValidate;
use App\Traits\TraitCasteCertificateValidate;
use App\Traits\TraitLifeCertificateValidate;
use Illuminate\Support\Facades\Session;
use App\Helpers\DupCheck;
use Illuminate\Support\Facades\Config;


class PensionformController extends Controller
{

    use TraitCasteCertificateValidate;
    use TraitLifeCertificateValidate;
    use TraitAadharValidate;
    protected $monthlySlug;
    protected $monthlySchemeCode;
    protected $monthlyMainTable;
    protected $monthlyDocTable;
    protected $monthlyDocArchTable;
    protected $housingSlug;
    protected $housingSchemeCode;
    protected $housingMainTable;
    protected $housingDocTable;
    protected $housingDocArchTable;
    protected $state_login_next_level_role_id_arr;

    public function __construct()
    {
        $this->middleware('auth');
        $arr = SchemecodeStatic::getpr1ListPurohit();
        // print_r($arr);die;
        $this->monthlySlug = $arr['monthly']['slug'];
        $this->monthlySchemeCode = $arr['monthly']['scheme_code'];
        $this->monthlyMainTable = "App\\" . $arr['monthly']['maintable'];
        $this->monthlyDocTable = "App\\" . $arr['monthly']['doctable'];
        $this->monthlyDocArchTable = "App\\" . $arr['monthly']['docarchtable'];

        $this->housingSlug = $arr['housing']['slug'];
        $this->housingSchemeCode = $arr['housing']['scheme_code'];
        $this->housingMainTable = "App\\" . $arr['housing']['maintable'];
        $this->housingDocTable = "App\\" . $arr['housing']['doctable'];
        $this->housingDocArchTable = "App\\" . $arr['housing']['docarchtable'];
        $this->state_login_next_level_role_id_arr = Config::get('constants.state_login_next_level_role_id');
        date_default_timezone_set('Asia/Kolkata');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->get('pr1')) {
            if ($request->get('pr1') == "sc") {
                $scheme_id = 3;
            } else if ($request->get('pr1') == "st") {
                $scheme_id = 1;
            } else if ($request->get('pr1') == $this->monthlySlug) {
                $scheme_id = $this->monthlySchemeCode;
            } else if ($request->get('pr1') == $this->housingSlug) {
                $scheme_id = $this->housingSchemeCode;
            }
        }
        $is_active = 0;

        // $base_url=url('/');
        // echo $base_url.'/images/';exit;        

        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
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
            //return view('pension_details')->with('districts',$districts); 

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
            return view('pension_details', [
                'districts' => $districts,
                'scheme_id' => $scheme_id,
                'doc_list_man' => $doc_list_man,
                'doc_list_opt' => $doc_list_opt,
                'profile_img' => $doc_profile_image_id
            ]);
        }
        if ($is_active == 0) {
            return redirect("/")->with('success', 'User Disabled');
        } else {
            return redirect("/")->with('success', 'User Disabled');
        }
    }



    public function create()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        if (!in_array($designation_id, array('Operator'))) {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $scheme_id = trim($request->scheme_id);
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
        if (empty($duty_obj)) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        $district_code = $duty_obj->district_code;
        if ($duty_obj->mapping_level == "Block") {
            $created_by_local_body_code = $duty_obj->taluka_code;
        }
        if ($duty_obj->mapping_level == "Subdiv") {
            $created_by_local_body_code = $duty_obj->urban_body_code;
        }
        if (empty($created_by_local_body_code)) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        $caste_category = trim($request->caste_category);


        if ($caste_category == 'SC' && $scheme_id == 3) {
            $scheme_id = 3;
        } else if ($caste_category == 'ST' && $scheme_id == 1) {
            $scheme_id = 1;
        } else {
            return redirect("/")->with('error', 'Something Wrong ..pleas try again.');
        }
        $ds_phase = DsPhase::where('is_current', TRUE)->first();
        $server_ip = $_SERVER['SERVER_ADDR'];
        $base_url = url('/');
        $uploaded_doc = array();

        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
        $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
        $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
        $doc_list = array_merge($doc_list_man, $doc_list_opt);
        $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
        $isValidarr = $this->validateInput($request, $scheme_id, 1, 0);
        if ($isValidarr['is_valid'] == false) {
            return back()->with('errors', $isValidarr['errors'])->withInput(Input::all());
        }
        if (!empty($request->aadhar_no)) {
            if ($this->isAadharValid(trim($request->aadhar_no)) == false) {
                $errors = array();
                $errorMsg = "Aadhaar Number Invalid";
                array_push($errors, $errorMsg);
                return back()->with('errors', $errors)->withInput();
            }
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
            return back()->with('errors', $errors)->withInput();
        }
        $str_caste = strtolower($request->caste_category);
        $ifsc = trim($request->bank_ifsc_code);
        $bank_account_number = trim($request->bank_account_number);
        $bank_branch = trim($request->bank_branch);
        $name_of_bank = trim($request->name_of_bank);
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
        $errormsg = array();
        if (!empty($bank_account_number) && !empty($ifsc)) {
            if ($scheme_id == 1) {
                $bank_count = DB::table($schema . '.beneficiaries')
                    ->where('bank_code', $bank_account_number)
                    ->whereIn('is_clean', [1, 2])  // Use whereIn for the IN clause
                    ->where('scheme_id', 1)
                    ->where('bank_ifsc', $ifsc)
                    ->count('bank_code');

                if ($bank_count > 0) {
                    $is_error = 1;
                    array_push($errormsg, 'Bank A/C Already Exist!');
                }

            } else if ($scheme_id == 3) {
                $bank_count = DB::table($schema . '.beneficiaries')->where('bank_code', $bank_account_number)->whereIn('is_clean', [1, 2])->where('scheme_id', 3)->where('bank_ifsc', $ifsc)->count('bank_code');
                if ($bank_count > 0) {
                    $is_error = 1;
                    array_push($errormsg, 'Bank A/C Already Exist!');
                }
            }
        }
        if ($scheme_id == 1 || $scheme_id == 3) {
            if (!empty($bank_account_number)) {
                $DupChecBankLB = DupCheck::getDupCheckBank(20, $bank_account_number);
                if (!empty($DupChecBankLB)) {
                    $is_error = 1;
                    $errorMsg = "Duplicate Bank Account Number present in Lakshmir Bhandar Scheme with Application ID- $DupChecBankLB";
                    array_push($errormsg, $errorMsg);
                }
            }
            if (!empty($bank_account_number)) {
                $DupChecBankOAP = DupCheck::getDupCheckBank(10, $bank_account_number);
                if (!empty($DupChecBankOAP)) {
                    $is_error = 1;
                    $errorMsg = "Duplicate Bank Account Number present in Old Age Pension Scheme with Beneficiary ID- $DupChecBankOAP";
                    array_push($errormsg, $errorMsg);
                }
            }
        }
        if (!empty($request->aadhar_no)) {
            if ($scheme_id == 1) {
                $aadhar_count = DB::table($schema . '.beneficiaries')->where('aadhar_no', trim($request->aadhar_no))->where('is_clean', '!=', 10)->where('scheme_id', 1)->where('is_clean', '!=', 10)->count('aadhar_no');
                if ($aadhar_count > 0) {
                    $is_error = 1;
                    array_push($errormsg, 'Aadhaar Number Already Exist! Please try different.');
                }
            } elseif ($scheme_id == 3) {
                $aadhar_count = DB::table($schema . '.beneficiaries')->where('aadhar_no', trim($request->aadhar_no))->where('is_clean', '!=', 10)->where('scheme_id', 3)->where('is_clean', '!=', 10)->count('aadhar_no');
                if ($aadhar_count > 0) {
                    $is_error = 1;
                    array_push($errormsg, 'Aadhaar Number Already Exist! Please try different.');
                }
            }
        }
        if ($scheme_id == 1 || $scheme_id == 3) {
            if (!empty($request->aadhar_no)) {
                $aadharDupCheckLB = DupCheck::getDupCheckAadhar(20, $request->aadhar_no);
                if (!empty($aadharDupCheckLB)) {
                    $is_error = 1;
                    $errorMsg = "Duplicate Aadhaar Number present in Lakshmir Bhandar Scheme with Application ID- $aadharDupCheckLB";
                    array_push($errormsg, $errorMsg);
                }
            }
            if (!empty($request->aadhar_no)) {
                $aadharDupCheckOAP = DupCheck::getDupCheckAadhar(10, $request->aadhar_no);
                if (!empty($aadharDupCheckOAP)) {
                    $is_error = 1;
                    $errorMsg = "Duplicate Aadhaar Number present in Old Age Pension Scheme with Beneficiary ID- $aadharDupCheckOAP";
                    array_push($errormsg, $errorMsg);
                }
            }
        }
        if (!empty($request->mobile_no)) {
            if ($scheme_id == 1) {
                $mobile_count = DB::table($schema . '.beneficiaries')->where('mobile_no', $request->mobile_no)->whereIn('is_clean', [1, 2])->where('scheme_id', 1)->count('mobile_no');
                if ($mobile_count > 0) {
                    $is_error = 1;
                    array_push($errormsg, 'Mobile Number Already Exist! Please try different.');
                }
            }
            if ($scheme_id == 3) {
                $mobile_count = DB::table($schema . '.beneficiaries')->where('mobile_no', $request->mobile_no)->whereIn('is_clean', [1, 2])->where('scheme_id', 3)->count('mobile_no');
                if ($mobile_count > 0) {
                    $is_error = 1;
                    array_push($errormsg, 'Mobile Number Already Exist! Please try different.');
                }
            }
        }
        if (!empty($request->caste_certificate_no)) {
            if ($request->scheme_id == 1) {
                $caste_count = DB::table($schema . '.beneficiaries')->where('caste_certificate_no', trim($request->caste_certificate_no))->whereIn('is_clean', [1, 2])->where('scheme_id', 1)->count('caste_certificate_no');
                if ($caste_count > 0) {
                    $is_error = 1;
                    array_push($errormsg, 'Caste Certificate Number Already Exist! Please try different.');
                }
            } else if ($request->scheme_id == 3) {
                $caste_count = DB::table($schema . '.beneficiaries')->where('caste_certificate_no', trim($request->caste_certificate_no))->whereIn('is_clean', [1, 2])->where('scheme_id', 3)->count('caste_certificate_no');
                if ($caste_count > 0) {
                    $is_error = 1;
                    array_push($errormsg, 'Caste Certificate Number Already Exist! Please try different.');
                }
            }
        }
        if (count($errormsg) > 0) {

            return redirect("pensionform?pr1=" . $str_caste)->withInput(Input::all())->with('errors', $errormsg);

        }
        $pension_details_insert = array();
        $pension_details = array();
        $upload_file = array();
        $i = 0;
        $c_time = date('Y-m-d H:i:s');
        $doc_master = DocumentType::get();
        foreach ($doc_list as $doc) {
            if ($request->hasFile('doc_' . $doc)) {
                $doc_file = $request->file('doc_' . $doc);
                $img_data = file_get_contents($doc_file);
                $u_extension_file = $doc_file->getClientOriginalExtension();
                $u_extension = strtolower($u_extension_file);
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

        if ($request->urban_code == 1) {
            $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
            $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();
            $pension_details['block_ulb_name'] = trim($block_ulb->urban_body_name);
            $pension_details['gp_ward_name'] = trim($gp_ward->urban_body_ward_name);
        } else {
            $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
            $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();
            $pension_details['block_ulb_name'] = trim($block_ulb->block_name);
            $pension_details['gp_ward_name'] = trim($gp_ward->gram_panchyat_name);
        }
        $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
        $assembly_name = $assembly->ac_name;
        if ($request->receive_pension != "") {
            $receive_pension = implode(',', $request->receive_pension);
            $pension_details['receive_pension'] = $receive_pension;
        }
        if ($request->social_security_pension != "") {
            $social_security_pension = implode(',', $request->social_security_pension);
            $pension_details['social_security_pension'] = $social_security_pension;
        }
        $pension_details['entry_type'] = trim($request->entry_type);
        if ($request->entry_type == 'Form through Duare Sarkar camp') {
            $pension_details['ds_registration_no'] = trim($request->ds_registration_no);
            $pension_details['ds_date'] = trim($request->ds_date);
            if (!empty($ds_phase)) {
                $pension_details['ds_phase'] = $ds_phase->phase_code;
            }
        }
        $pension_details['ben_fname'] = trim($request->first_name);
        $pension_details['ben_mname'] = trim($request->middle_name);
        $pension_details['ben_lname'] = trim($request->last_name);
        $pension_details['gender'] = trim($request->gender);
        $pension_details['dob'] = trim($request->dob);
        $pension_details['ben_age'] = trim($request->txt_age);
        $pension_details['father_fname'] = trim($request->father_first_name);
        $pension_details['father_mname'] = trim($request->father_middle_name);
        $pension_details['father_lname'] = trim($request->father_last_name);
        $pension_details['mother_fname'] = trim($request->mother_first_name);
        $pension_details['mother_mname'] = trim($request->mother_middle_name);
        $pension_details['mother_lname'] = trim($request->mother_last_name);
        $pension_details['caste'] = trim($request->caste_category);
        $pension_details['caste_certificate_no'] = trim($request->caste_certificate_no);
        $pension_details['marital_status'] = trim($request->marital_status);
        $pension_details['mothly_income'] = trim($request->monthly_income);
        $pension_details['spouse_fname'] = trim($request->spouse_first_name);
        $pension_details['spouse_mname'] = trim($request->spouse_middle_name);
        $pension_details['spouse_lname'] = trim($request->spouse_last_name);
        $pension_details['ration_card_cat'] = trim($request->ration_card_cat);
        $pension_details['ration_card_no'] = trim($request->ration_card_no);
        $pension_details['ahl_tin'] = trim($request->ahl_tin);
        $pension_details['aadhar_no'] = trim($request->aadhar_no);
        $pension_details['epic_voter_id'] = trim($request->epic_voter_id);
        $pension_details['pan_no'] = trim($request->pan_no);
        $pension_details['bpl_seq_no'] = trim($request->bpl_seq_no);
        $pension_details['bpl_id_no'] = trim($request->bpl_id_no);
        $pension_details['bpl_total_score'] = intval($request->bpl_total_score);
        $pension_details['dist_code'] = trim($request->district);
        $pension_details['rural_urban_id'] = trim($request->urban_code);
        $pension_details['assembly_code'] = trim($request->asmb_cons);
        $pension_details['assembly_name'] = trim($assembly_name);
        $pension_details['police_station'] = trim($request->police_station);
        $pension_details['block_ulb_code'] = trim($request->block);
        $pension_details['gp_ward_code'] = trim($request->gp_ward);
        $pension_details['village_town_city'] = trim($request->village);
        $pension_details['house_premise_no'] = trim($request->house);
        $pension_details['post_office'] = trim($request->post_office);
        $pension_details['pincode'] = trim($request->pin_code);
        $pension_details['residency_period'] = trim($request->residency_period);
        $pension_details['mobile_no'] = trim($request->mobile_no);
        $pension_details['email'] = trim($request->email);
        $pension_details['bank_name'] = trim($request->name_of_bank);
        $pension_details['branch_name'] = trim($request->bank_branch);
        $pension_details['bank_code'] = trim($request->bank_account_number);
        $pension_details['npci_bank_code'] = trim($new_bank_code);
        $pension_details['bank_ifsc'] = trim($request->bank_ifsc_code);
        $pension_details['nominate_name'] = trim($request->nominate_name);
        $pension_details['nominate_address'] = trim($request->nominate_address);
        $pension_details['nominate_relationship'] = trim($request->nominate_relationship);
        $pension_details['created_by'] = $user_id;
        $pension_details['created_at'] = date('Y-m-d');
        $pension_details['created_by_level'] = $duty_obj->mapping_level;
        $pension_details['created_by_dist_code'] = $district_code;
        $pension_details['created_by_local_body_code'] = $created_by_local_body_code;
        $pension_details['scheme_id'] = $scheme_id;
        $pension_details['entry_datetime'] = $c_time;
        $pension_details['ip_address'] = $request->ip();
        DB::connection('pgsql_encwrite')->beginTransaction();
        DB::beginTransaction();
        try {
            //dd($pension_details);

            $is_saved = DB::table($schema . '.beneficiaries')->insert($pension_details);
            if ($is_saved) {
                $beneficiary_id = DB::getPdo()->lastInsertId();
                ;
                foreach ($upload_file as $key => $csm) {
                    $upload_file[$key]['beneficiary_id'] = $beneficiary_id;
                }
                $doc_inserted = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($upload_file);
                if ($doc_inserted) {
                    $app_id = $district_code . substr('0' . $scheme_id, -$scheme_length) . substr('0000000' . $beneficiary_id, -$id_length);
                    DB::commit();
                    DB::connection('pgsql_encwrite')->commit();
                    $ben_fullname = trim($request->first_name) . ' ' . trim($request->middle_name) . ' ' . trim($request->last_name);
                    //     $this->bioauthcheckInsert($district_code,$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$created_by_local_body_code,$user_id);
                    //     if(($request->caste_category=='SC' || $request->caste_category=='ST') && !empty($request->caste_certificate_no)){
                    //     $this->casteInfoCheckInsert($district_code,$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->caste_certificate_no),$created_by_local_body_code,$user_id);
                    //   }
                    //     $this->RationcheckInsert($district_code,$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$created_by_local_body_code,$user_id,$request->dob);
                    try {
                        $this->bioauthcheckInsert($district_code, $beneficiary_id, $scheme_id, $ben_fullname, $request->ip(), trim($request->aadhar_no), $created_by_local_body_code, $user_id);
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
                        $upadated_main = DB::table($schema . '.beneficiary')
                            ->where([
                                'id' => $beneficiary_id,
                                'created_by_local_body_code' => $created_by_local_body_code,
                                'created_by_dist_code' => $district_code
                            ])->update($inputMain);
                    }

                    $ben_details = DB::table($schema . '.beneficiaries')->where('id', $beneficiary_id)->first();
                    if ($ben_details) {
                        // $caste_certificate_checked=$ben_details->caste_certificate_checked;
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
                        $dob_kh = $ben_details->dob_kh;
                        $dob_is_match_kh = $ben_details->dob_is_match_kh;
                        $dob = $ben_details->dob;
                        return redirect("pensionform?pr1=" . $str_caste)->with('success', 'Application Submitted Successfully')
                            ->with('id', $app_id)
                            // ->with('caste_certificate_checked',  $caste_certificate_checked)
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
                            ->with('aadhaar_no_validation_msg', $aadhaar_no_validation_msg)
                            ->with('dob_kh', $dob_kh)
                            ->with('dob_is_match_kh', $dob_is_match_kh)
                            ->with('dob', $dob);
                    }
                } else {
                    DB::rollback();
                    DB::connection('pgsql_encwrite')->rollback();
                    return redirect("pensionform?pr1=" . $str_caste)->withInput(Input::all())->with('errors', array('Some error.Please try again'));
                }
            } else {
                //dd('ok587');
                DB::rollback();
                DB::connection('pgsql_encwrite')->rollback();
                return redirect("pensionform?pr1=" . $str_caste)->withInput(Input::all())->with('errors', array('Some error.Please try again'));
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            DB::connection('pgsql_encwrite')->rollback();
            return redirect("pensionform?pr1=" . $str_caste)->withInput(Input::all())->with('errors', array('Some error.Please try again'));
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
    public function approvedlistReadOnly(Request $request)
    {
        //DB::enableQueryLog();

        $user_id = AuthChecker::getUserId();

        if ($request->get('pr1')) {
            if ($request->get('pr1') == "farmer") {
                $scheme_id = 13;
                $rows = PensionOAPFarmer::where(['scheme_id' => $scheme_id, 'created_by' => $user_id])
                    ->where('next_level_role_id', '!=', null)
                    ->orderBy('id', 'desc')
                    ->paginate(10)->appends(request()->query());
            } else if ($request->get('pr1') == "sc") {
                $scheme_id = 3;
                $rows = PensionSc::where(['scheme_id' => $scheme_id, 'created_by' => $user_id])
                    ->where('next_level_role_id', '!=', null)
                    ->orderBy('id', 'desc')
                    ->paginate(10)->appends(request()->query());
            } else if ($request->get('pr1') == "st") {
                $scheme_id = 1;
                $rows = PensionSt::where(['scheme_id' => $scheme_id, 'created_by' => $user_id])
                    ->where('next_level_role_id', '!=', null)
                    ->orderBy('id', 'desc')
                    ->paginate(10)->appends(request()->query());
            } else if ($request->get('pr1') == "wcd") {

                $scheme_id = $request->get('wcd_type');
                if ($scheme_id == 2) {
                    $rows = PensionManabikWCD::where(['scheme_id' => $scheme_id, 'created_by' => $user_id])
                        ->where('next_level_role_id', '!=', null)
                        ->orderBy('id', 'desc')
                        ->paginate(10)->appends(request()->query());
                } else if ($scheme_id == 10) {
                    $rows = PensionOAPWCD::where(['scheme_id' => $scheme_id, 'created_by' => $user_id])
                        ->where('next_level_role_id', '!=', null)
                        ->orderBy('id', 'desc')
                        ->paginate(10)->appends(request()->query());
                } else if ($scheme_id == 11) {
                    $rows = PensionWPWCD::where(['scheme_id' => $scheme_id, 'created_by' => $user_id])
                        ->where('next_level_role_id', '!=', null)
                        ->orderBy('id', 'desc')
                        ->paginate(10)->appends(request()->query());
                }
            } else if ($request->get('pr1') == $this->monthlySlug) {
                $scheme_id = $this->monthlySchemeCode;
                $rows = $this->monthlyMainTable::where(['scheme_id' => $scheme_id, 'created_by' => $user_id])
                    ->where('next_level_role_id', '!=', null)
                    ->orderBy('id', 'desc')
                    ->paginate(10)->appends(request()->query());
            } else if ($request->get('pr1') == $this->housingSlug) {
                $scheme_id = $this->housingSchemeCode;
                // echo $scheme_id;die;
                $rows = $this->housingMainTable::where(['scheme_id' => $scheme_id, 'created_by' => $user_id])
                    ->where('next_level_role_id', '!=', null)
                    ->orderBy('id', 'desc')
                    ->paginate(10)->appends(request()->query());
            } else {
                $rows = array();
            }
        }

        return view('pension_list_read_only', ['nhm_employee_details' => $rows, 'scheme_id' => $scheme_id, 'list_type' => '1']);
    }

    public function applicationlistReadOnly(Request $request)
    {
        //DB::enableQueryLog();

        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        //dd($designation_id);
        if ($designation_id != 'Operator') {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $sucess = $request->get('sucess');
        $id = $request->get('id');

        if ($request->get('pr1')) {
            if ($request->get('pr1') == "farmer") {
                $scheme_id = 13;
                $model_name = 'App\\PensionOAPFarmer';
            } else if ($request->get('pr1') == "sc") {
                $scheme_id = 3;
                $model_name = 'App\\PensionSc';
            } else if ($request->get('pr1') == "st") {
                $scheme_id = 1;
                $model_name = 'App\\PensionSt';
            } else if ($request->get('pr1') == "fisherman") {
                $scheme_id = 5;
                $model_name = 'App\\PensionFisherman';
            } else if ($request->get('pr1') == "msme") {
                $scheme_id = 6;
                $model_name = 'App\\PensionMSME';
            } else if ($request->get('pr1') == "textile") {
                $scheme_id = 7;
                $model_name = 'App\\PensionTextile';
            } else if ($request->get('pr1') == "wcd") {
                $scheme_id = $request->get('wcd_type');
                if ($scheme_id == 2) {
                    $model_name = 'App\\PensionManabikWCD';
                    //$rows = PensionManabikWCD::where(['scheme_id' => $scheme_id, 'created_by' => $user_id, 'next_level_role_id' => null])->orderBy('id', 'desc')->paginate(10)->appends(request()->query());
                } else if ($scheme_id == 10) {
                    $model_name = 'App\\PensionOAPWCD';
                    // $rows = PensionOAPWCD::where(['scheme_id' => $scheme_id, 'created_by' => $user_id, 'next_level_role_id' => null])->orderBy('id', 'desc')->paginate(10)->appends(request()->query());
                } else if ($scheme_id == 11) {
                    $model_name = 'App\\PensionWPWCD';
                    // $rows = PensionWPWCD::where(['scheme_id' => $scheme_id, 'created_by' => $user_id, 'next_level_role_id' => null])->orderBy('id', 'desc')->paginate(10)->appends(request()->query());
                }
            } else if ($request->get('pr1') == $this->monthlySlug) {
                $scheme_id = $this->monthlySchemeCode;
                $model_name = $this->monthlyMainTable;
            } else if ($request->get('pr1') == $this->housingSlug) {
                $scheme_id = $this->housingSchemeCode;
                $model_name = $this->housingMainTable;
            } else {
                return redirect("/")->with('success', 'User Disabled');
            }
            $is_active = 0;
            $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                        foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == $scheme_id) {
                    $is_active = 1;
                    $mapping_level = $roleObj['mapping_level'];
                    $distCode = $roleObj['district_code'];
                    $is_urban = $roleObj['is_urban'];
                    $is_state_login = $roleObj['is_state_login'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }
            if ($is_active == 0) {
                return redirect("/")->with('success', 'User Disabled');
            }
            if ($is_state_login) {
                $rows = $model_name::where(['is_state' => TRUE, 'scheme_id' => $scheme_id, 'next_level_role_id' => $this->state_login_next_level_role_id_arr['entry']])->where(function ($query) use ($user_id) {
                    $query->where('created_by', '=', $user_id)
                        ->orWhereNull('created_by');
                })->orderBy('id', 'desc')
                    ->paginate(10)->appends(request()->query());
            } else {
                $rows = $model_name::where(['rural_urban_id' => $is_urban, 'created_by_dist_code' => $distCode, 'created_by_local_body_code' => $blockCode, 'scheme_id' => $scheme_id, 'next_level_role_id' => null])->where(function ($query) use ($user_id) {
                    $query->where('created_by', '=', $user_id)
                        ->orWhereNull('created_by');
                })->orderBy('id', 'desc')
                    ->paginate(10)->appends(request()->query());
            }
        } else {
            return redirect("/")->with('error', 'Parameter not valid');
        }

        return view('pension_list_read_only', ['nhm_employee_details' => $rows, 'scheme_id' => $scheme_id, 'list_type' => '0', 'sucess' => $sucess, 'id' => $id]);
    }

    public function applicationdetails(Request $request)
    {

        $id = $request->id;

        $row = PensionSc::find($id);
        // echo $row->block_ulb_code;exit;
        // echo "<pre>";print_r($block);exit;

        $district_name = "";
        $block_name = "";
        $gp_name = "";

        if ($row->dist_code != "") {
            $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
            $district_name = $district->district_name;
        }
        if ($row->block_ulb_code != "") {
            $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
            $block_name = $block->block_name;
        }
        if ($row->gp_ward_code != "") {
            $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
            $gp_name = $gp->gram_panchyat_name;
        }





        return view('pension_view_details', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name]);
    }

    public function applicationdetailsReadOnly(Request $request)
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
                $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                                foreach ($roleArray as $roleObj) {
                    if ($roleObj['scheme_id'] == $scheme_id) {
                        $is_active = 1;
                        $mapping_level = $roleObj['mapping_level'];
                        $distCode = $roleObj['district_code'];
                        $is_urban = $roleObj['is_urban'];
                        $is_state_login = $roleObj['is_state_login'];
                        if ($roleObj['is_urban'] == 1) {
                            $blockCode = $roleObj['urban_body_code'];
                        } else {
                            $blockCode = $roleObj['taluka_code'];
                        }
                        break;
                    }
                }
            }

            if ($is_active == 0) {
                return redirect("/")->with('danger', 'User Disabled');
            }
            $is_state_login = 0;
            $docs = array();
            $row = null;
            if ($scheme_id == 13) {
                $row = BenEntry::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();
            } else if ($scheme_id == 3) {
                $row = BenEntry::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();
            } else if ($scheme_id == 1) {
                $row = BenEntry::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();
            } else if ($scheme_id == 5) {
                $row = BenEntry::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();
            } else if ($scheme_id == 6) {
                $row = BenEntry::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();
            } else if ($scheme_id == 7) {
                $row = BenEntry::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();
            } else if ($scheme_id == 2) {
                $row = BenEntry::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                // $docs = BenDocsManabikWCD::where('ben_id', $id)->orderBy('doc_type_id')->get();
                $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();
            } else if ($scheme_id == 10) {
                $row = BenEntry::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();
            } else if ($scheme_id == 11) {
                $row = BenEntry::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();
            } else if ($scheme_id == $this->monthlySchemeCode) {
                $row = $this->monthlyMainTable::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                //$docs = $this->monthlyDocTable::where('ben_id', $id)->orderBy('doc_type_id')->get();
                $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();

            } else if ($scheme_id == $this->housingSchemeCode) {
                $row = $this->housingMainTable::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                $docs = $this->housingDocTable::where('ben_id', $id)->orderBy('doc_type_id')->get();
            } else if ($scheme_id == 19) {
                $row = BenEntry::find($id);
                if ($duty->mapping_level == 'Department') {
                    $distCode = $row->created_by_dist_code;
                }
                $docs = collect([]);
            }
            if (empty($row)) {
                return redirect("/")->with('danger', 'Not Allowed');
            }
            //echo "<pre>";print_r($row);exit;
            // $row = PensionSc::find($id);
            // echo $row->block_ulb_code;exit;
            // echo "<pre>";print_r($block);exit;

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
            if ($scheme_id == 13) {

                return view('farmer/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
            } else if ($scheme_id == 5) {
                return view('fisherman/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
            } else if ($scheme_id == 6) {
                return view('msme/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
            } else if ($scheme_id == 7) {
                return view('textile/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
            } else if ($scheme_id == 2) {
                return view('MANABIKWCD/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
            } else if ($scheme_id == 10) {
                return view('OAPWCD/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
            } else if ($scheme_id == 11) {
                return view('WPWCD/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
            } else if ($scheme_id == $this->monthlySchemeCode) {
                return view('PurohitICAD/pension_view_details_read_only', ['scheme_id' => $scheme_id, 'row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
            } else if ($scheme_id == $this->housingSchemeCode) {
                return view('PurohitICAD/pension_view_details_read_only', ['scheme_id' => $scheme_id, 'row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
            } else if ($scheme_id == 19) {
                return view('fisherman/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
            } else
                return view('pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
        } catch (\Exception $e) {
            // dd($e);
            return redirect("/")->with('error', 'Some error.please try again ......');
        }
    }

    public function applicationeditview(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $id = (int) $request->id;
        $scheme_id = (int) $request->scheme_id;
        $designation_id = Auth::user()->designation_id;

        if (!is_int($scheme_id)) {
            return redirect("/")->with('danger', 'Scheme Code Not Valid');
        }
        if (!is_numeric($id)) {
            return redirect("/")->with('danger', 'Applicant ID Not Valid');
        }
        $row = array();
        if ($scheme_id == 13) {
            // $row = PensionSc::find($id);
            $model_name = 'App\\PensionOAPFarmer';
        } else if ($scheme_id == 3) {
            // $row = PensionSc::find($id);
            $model_name = 'App\\PensionSc';
        } else if ($scheme_id == 1) {
            //$row = PensionSt::find($id);
            $model_name = 'App\\PensionSt';
        } else if ($scheme_id == 5) {
            //  $row = PensionFisherman::find($id);
            $model_name = 'App\\PensionFisherman';
        } else if ($scheme_id == 6) {
            //$row = PensionMSME::find($id);
            $model_name = 'App\\PensionMSME';
        } else if ($scheme_id == 7) {
            // $row = PensionTextile::find($id);
            $model_name = 'App\\PensionTextile';
        } else if ($scheme_id == 2) {
            // $row = PensionManabikWCD::find($id);
            $model_name = 'App\\PensionManabikWCD';
        } else if ($scheme_id == 10) {
            // $row = PensionOAPWCD::find($id);
            $model_name = 'App\\PensionOAPWCD';
        } else if ($scheme_id == 11) {
            // $row = PensionWPWCD::find($id);
            $model_name = 'App\\PensionWPWCD';
        } else if ($scheme_id == $this->monthlySchemeCode) {
            // $row = $this->monthlyMainTable::find($id);
            $model_name = $this->monthlyMainTable;
        } else if ($scheme_id == $this->housingSchemeCode) {
            //$row = $this->housingMainTable::find($id);
            $model_name = $this->housingMainTable;
        }
        $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $mapping_level = $roleObj['mapping_level'];
                $distCode = $roleObj['district_code'];
                $is_urban = $roleObj['is_urban'];
                $is_state_login = $roleObj['is_state_login'];
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                } else {
                    $blockCode = $roleObj['taluka_code'];
                }
                break;
            }
        }
        // $model_name = 'App\\BenEntry';
        //dd($distCode);
        if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disabled');
        }
        if ($scheme_id == 17) {
            $query = $model_name::where(['id' => $id, 'scheme_id' => $scheme_id]);
        } else {
            if ($is_state_login) {
                $query = $model_name::where(['id' => $id, 'is_state' => TRUE, 'scheme_id' => $scheme_id]);
            } else {
                $query = $model_name::where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id]);
            }
        }
        if ($designation_id == 'Verifier') {
            if ($is_state_login) {
                $query = $query->where('next_level_role_id', $this->state_login_next_level_role_id_arr['entry']);
            } else {
                $query = $query->whereNull('next_level_role_id');
            }
        } else if ($designation_id == 'Approver') {
            if ($is_state_login) {
                $query = $query->where('next_level_role_id', $this->state_login_next_level_role_id_arr['verified']);
            } else {
                $query = $query->where('is_verified', 1)->where('is_approved', 0)->where('is_rejected', 0);
            }
        }
        $row = $query->first();
        if (empty($row->bank_code)) {
            return redirect("/")->with('error', 'Applicant Id not found');
        }
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


        //Document Dynamic
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->first();

        if (!empty($doc_id_list->doc_list_man))
            $doc_list_man = DocumentType::get()->whereIn("id", json_decode($doc_id_list->doc_list_man));
        else
            $doc_list_man = collect([]);
        if (!empty($doc_id_list->doc_list_opt))
            $doc_list_opt = DocumentType::get()->whereIn("id", json_decode($doc_id_list->doc_list_opt));
        else
            $doc_list_opt = collect([]);
        $doc_profile_image = DocumentType::get()
            ->where("is_profile_pic", true)->first();

        $doc_profile_image_id = 999;
        if ($doc_profile_image) {
            $doc_profile_image_id = $doc_profile_image->id;
        }

        $document_msg = "";

        if ($scheme_id == 13)
            return view('farmer/pension_edit', ['assemly_list' => $assemly_list, 'block_munc_list' => $block_munc_list, 'gp_ward_list' => $gp_ward_list, 'scheme_name' => $scheme_name, 'row' => $row, 'document_msg' => $document_msg, 'districts' => $districts, 'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id]);
        else if ($scheme_id == 5)
            return view('fisherman/pension_edit', ['assemly_list' => $assemly_list, 'block_munc_list' => $block_munc_list, 'gp_ward_list' => $gp_ward_list, 'scheme_name' => $scheme_name, 'row' => $row, 'districts' => $districts, 'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id]);
        else if ($scheme_id == 6)
            return view('msme/pension_edit', ['assemly_list' => $assemly_list, 'block_munc_list' => $block_munc_list, 'gp_ward_list' => $gp_ward_list, 'scheme_name' => $scheme_name, 'row' => $row, 'districts' => $districts, 'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id]);
        else if ($scheme_id == 7)
            return view('textile/pension_edit', ['assemly_list' => $assemly_list, 'block_munc_list' => $block_munc_list, 'gp_ward_list' => $gp_ward_list, 'scheme_name' => $scheme_name, 'row' => $row, 'districts' => $districts, 'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id]);
        else if ($scheme_id == 2) {
            return view('MANABIKWCD/pension_edit', ['assemly_list' => $assemly_list, 'block_munc_list' => $block_munc_list, 'gp_ward_list' => $gp_ward_list, 'scheme_name' => $scheme_name, 'is_state_login' => $is_state_login, 'row' => $row, 'document_msg' => $document_msg, 'districts' => $districts, 'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id]);
        } else if ($scheme_id == 10) {
            if ($row->wt_special == 1) {
                $district_arr = District::select('district_code', 'district_name')->where('district_code', $distCode)->first();
                $rural_urban_arr = Config::get('constants.rural_urban');
                $my_rular_urban_arr = array();
                foreach ($rural_urban_arr as $key => $val) {
                    if ($key == $roleObj['is_urban']) {
                        $my_rular_urban_arr['key'] = $key;
                        $my_rular_urban_arr['val'] = $val;
                    }
                }
                //dd($row->block_ulb_code);
                if ($row->rural_urban_id == 1) {
                    // dd();
                    $blok_munc_arr = UrbanBody::select('urban_body_code as code', 'urban_body_name as name')->where('sub_district_code', $row->created_by_local_body_code)->where('urban_body_code', trim($row->block_ulb_code))->first();
                    $gp_ward_arr = Ward::select('urban_body_ward_code as code', 'urban_body_ward_name as name')->where('urban_body_code', 273007)->get();
                } else {
                    $blok_munc_arr = Taluka::select('block_code as code', 'block_name as name')->where('block_code', $row->block_ulb_code)->first();
                    //dd($blok_munc_arr);
                    $gp_ward_arr = GP::select('gram_panchyat_code as code', 'gram_panchyat_name as name')->where('block_code', $row->block_ulb_code)->get();
                }
            } else {
                $district_arr = collect();
                $my_rular_urban_arr = array();
                $blok_munc_arr = collect();
                $gp_ward_arr = collect();
            }
            return view('OAPWCD/pension_edit', [
                'assemly_list' => $assemly_list,
                'block_munc_list' => $block_munc_list,
                'gp_ward_list' => $gp_ward_list,
                'scheme_name' => $scheme_name,
                'is_state_login' => $is_state_login,
                'row' => $row,
                'document_msg' => $document_msg,
                'districts' => $districts,
                'scheme_id' => $scheme_id,
                'doc_list_man' => $doc_list_man,
                'doc_list_opt' => $doc_list_opt,
                'profile_img' => $doc_profile_image_id,
                'blok_munc_arr' => $blok_munc_arr,
                'my_rular_urban_arr' => $my_rular_urban_arr,
                'district_arr' => $district_arr,
                'wq' => $row->wt_special,
            ]);
        } else if ($scheme_id == 11) {
            if (!empty($request->is_nsap)) {
                $is_nsap = $request->is_nsap;
            } else {
                $is_nsap = 0;
            }
            return view(
                'WPWCD/pension_edit',
                [
                    'assemly_list' => $assemly_list,
                    'block_munc_list' => $block_munc_list,
                    'gp_ward_list' => $gp_ward_list,
                    'scheme_name' => $scheme_name,
                    'is_state_login' => $is_state_login,
                    'row' => $row,
                    'document_msg' => $document_msg,
                    'districts' => $districts,
                    'scheme_id' => $scheme_id,
                    'doc_list_man' => $doc_list_man,
                    'doc_list_opt' => $doc_list_opt,
                    'profile_img' => $doc_profile_image_id,
                    'is_nsap' => $is_nsap
                ]
            );
        } else if ($scheme_id == $this->monthlySchemeCode) {
            $monthlySlug = $this->monthlySchemeCode;
            $housingSlug = $this->housingSlug;
            $code = $monthlySlug;
            return view('PurohitICAD/pension_edit', ['assemly_list' => $assemly_list, 'block_munc_list' => $block_munc_list, 'gp_ward_list' => $gp_ward_list, 'scheme_name' => $scheme_name, 'monthlySlug' => $monthlySlug, 'housingSlug' => $housingSlug, 'code' => $code, 'id' => $row->id, 'row' => $row, 'row' => $row, 'districts' => $districts, 'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id]);
        } else if ($scheme_id == $this->housingSchemeCode) {
            $monthlySlug = $this->monthlySchemeCode;
            $housingSlug = $this->housingSlug;
            $code = $housingSlug;
            return view('PurohitICAD/pension_edit', ['assemly_list' => $assemly_list, 'block_munc_list' => $block_munc_list, 'gp_ward_list' => $gp_ward_list, 'scheme_name' => $scheme_name, 'monthlySlug' => $monthlySlug, 'housingSlug' => $housingSlug, 'code' => $code, 'id' => $row->id, 'row' => $row, 'districts' => $districts, 'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id]);
        } else
            return view('pension_edit', ['assemly_list' => $assemly_list, 'block_munc_list' => $block_munc_list, 'gp_ward_list' => $gp_ward_list, 'scheme_name' => $scheme_name, 'row' => $row, 'districts' => $districts, 'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id]);
    }


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
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                foreach ($roleArray as $roleObj) {
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

        $scheme_row = Scheme::where('id', $scheme_id)->first();
        if (empty($scheme_row)) {
            return redirect("/")->with('error', 'User Disabled');
        }
        $scheme_schema = $scheme_row->short_code;
        $ben_details = DB::table(strtolower($scheme_schema) . '.beneficiary')->where('id', $id)->first();

        $isValidarr = $this->validateInput($request, $scheme_id, 2, (($ben_details->is_lb_imported == 1) ? 1 : 0));
        if ($isValidarr['is_valid'] == false) {
            //dd(withInput());
            return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $isValidarr['errors']);
            //return back()->withErrors($isValidarr['errors'])->withInput();
        }

        $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
        if ($scheme_id == 1) {
            $count = PensionSt::where('aadhar_no', trim($request->aadhar_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
        } else if ($scheme_id == 3) {
            $count = PensionSc::where('aadhar_no', trim($request->aadhar_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
        } else {
            $errorMsg = "Invalid Input";
            return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errorMsg);
        }
        if ($count > 0) {
            $errors = array();
            $errorMsg = "Aadhaar Number Already Exist! Please try different.";
            array_push($errors, $errorMsg);
            return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
        }
        $ifsc = trim($request->bank_ifsc_code);
        $bank_branch = trim($request->bank_branch);
        $name_of_bank = trim($request->name_of_bank);
        $row_count_bank = BankDetails::whereraw("trim(branch)='$bank_branch'")->whereraw("trim(ifsc)='$ifsc'")->where('is_active', 1)->whereraw("trim(bank)='$name_of_bank'")->count();
        $bank_details = BankDetails::where('ifsc', trim($ifsc))->where('is_active', 1)->get(['bank', 'branch', 'bank_code'])->first();

        $new_bank_code = $bank_details->bank_code;
        if ($row_count_bank == 0) {
            $errors = array();
            $errorMsg = "Bank IFSC and Bank Name Not Match!";
            array_push($errors, $errorMsg);
            return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
        }
        if ($scheme_id == 3) {
            $bankCount = PensionSc::whereRaw("trim(bank_code)=trim(" . "'" . $request->bank_account_number . "'" . ")")->where('id', '!=', $id)
                ->whereRaw("(" . $check_condition_str . ")")
                ->count('id');
            //echo $bankCount; exit;
            if ($bankCount > 0) {
                $errors = array();
                $errorMsg = "Bank A/C Already Exist!";
                array_push($errors, $errorMsg);
                return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
            }
            if (!empty(trim($request->caste_certificate_no))) {
                $count_caste = PensionSc::where('caste_certificate_no', trim($request->caste_certificate_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
                if ($count_caste > 0) {
                    $errors = array();
                    $errorMsg = "Caste Certificate Number Already Exist! Please try different.";
                    array_push($errors, $errorMsg);
                    return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
                }
            }
        }
        if ($scheme_id == 1) {
            $bankCount = PensionSt::whereRaw("trim(bank_code)=trim(" . "'" . $request->bank_account_number . "'" . ")")->where('id', '!=', $id)
                ->whereRaw("(" . $check_condition_str . ")")
                ->count('id');
            //echo $bankCount; exit;
            if ($bankCount > 0) {
                $errors = array();
                $errorMsg = "Bank A/C Already Exist!";
                array_push($errors, $errorMsg);
                return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
            }
            if (!empty(trim($request->caste_certificate_no))) {
                $count_caste = PensionSt::where('caste_certificate_no', trim($request->caste_certificate_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
                if ($count_caste > 0) {
                    $errors = array();
                    $errorMsg = "Caste Certificate Number Already Exist! Please try different.";
                    array_push($errors, $errorMsg);
                    return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
                }
            }
        }

        // dd($ben_details);
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

        // if($request->id=9100885)
        // {

        //     dd(11);
        // }
        $c_time = date('Y-m-d H:i:s');
        $user_id = AuthChecker::getUserId();
        $input = [
            //'name' => $request['name']
            //'ben_fname' => $request->first_name,
            //'ben_mname' => $request->middle_name,
            //'ben_lname' => $request->last_name,
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
            'caste_certificate_no' => trim($request->caste_certificate_no),
            'marital_status' => $request->marital_status,

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
            'ration_card_no' => $request->ration_card_no,
            'ahl_tin' => $request->ahl_tin,
            'aadhar_no' => $request->aadhar_no,
            'epic_voter_id' => $request->epic_voter_id,
            'pan_no' => $request->pan_no,



            'dist_code' => $request->district,
            'assembly_code' => $request->asmb_cons,
            'assembly_name' => $assembly_name,
            'rural_urban_id' => $request->urban_code,
            'police_station' => $request->police_station,
            'block_ulb_code' => $request->block,
            'block_ulb_name' => $block_ulb_name,
            'gp_ward_code' => $request->gp_ward,
            'gp_ward_name' => $gp_ward_name,
            'village_town_city' => $request->village,
            'house_premise_no' => $request->house,
            'post_office' => $request->post_office,
            'pincode' => $request->pin_code,
            'residency_period' => $request->residency_period,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,



            //'bank_name'  => $request->name_of_bank,
            //'branch_name'   => $request->bank_branch,
            //'bank_code'    => $request->bank_account_number,
            //'bank_ifsc'   => $request->bank_ifsc_code,
            //'npci_bank_code'  => $new_bank_code,

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
        if ($ben_details->is_lb_imported == 0 || empty($ben_details->is_lb_imported)) {
            $input['ben_fname'] = $request->first_name;
            $input['ben_mname'] = $request->middle_name;
            $input['ben_lname'] = $request->last_name;
            $input['bank_name'] = $request->name_of_bank;
            $input['branch_name'] = $request->bank_branch;
            $input['bank_code'] = $request->bank_account_number;
            $input['bank_ifsc'] = $request->bank_ifsc_code;
            $input['npci_bank_code'] = $new_bank_code;
        }

        $pr1 = "";
        $uploaded_doc = array();
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
        $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
        $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
        $doc_list = array_merge($doc_list_man, $doc_list_opt);

        $doc_master = DocumentType::get();
        $encolserdata = BenDocs::where('scheme_id', $scheme_id)->where('created_by_dist_code', $distCode)->where('beneficiary_id', $request->id)->get();
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
                $u_extension_file = $doc_file->getClientOriginalExtension();
                $u_extension = strtolower($u_extension_file);
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



        try {
            DB::beginTransaction();
            DB::connection('pgsql_encwrite')->beginTransaction();
            if ($scheme_id == 3) {
                $arch_status = DB::statement("INSERT INTO bandhu.arc_beneficiary(id, dist_code, ben_fname, ben_mname, 
                ben_lname, gender, dob, ben_age, caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
                mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, ration_card_no, ahl_tin, 
                aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, bpl_total_score, assembly_code, 
                assembly_name, police_station, block_ulb_code, block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, 
                village_town_city, house_premise_no, post_office, pincode, residency_period, mobile_no, email, bank_name, 
                branch_name,  bank_ifsc, created_at, updated_at,  
                 created_by, created_by_level, created_by_dist_code, created_by_local_body_code, scheme_id, 
                type_disability, percentage_disability, certifying_auth, next_level_role_id,  nominate_name, nominate_address, 
                nominate_relationship, receive_pension, social_security_pension, ration_card_cat, rural_urban_id, lot_generated,  
                approval_rejected, bank_edited, bank_code, payment_count, last_paid_yymm, payment_error_status, av_status, 
                pension_amount, entry_type, ds_registration_no, rejected_cause, rejected_date, 
                ds_phase, ds_date, rejected_by, verification_date, 
                verified_by, approval_date, approved_by, dept_special, dept_mark, is_verified, is_approved, is_rejected
               ) (SELECT id, dist_code, ben_fname, ben_mname, 
                ben_lname, gender, dob, ben_age, caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
                mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, ration_card_no, ahl_tin, 
                aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, bpl_total_score, assembly_code, 
                assembly_name, police_station, block_ulb_code, block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, 
                village_town_city, house_premise_no, post_office, pincode, residency_period, mobile_no, email, bank_name, 
                branch_name,  bank_ifsc, created_at, updated_at,  
                 created_by, created_by_level, created_by_dist_code, created_by_local_body_code, scheme_id, 
                type_disability, percentage_disability, certifying_auth, next_level_role_id,  nominate_name, nominate_address, 
                nominate_relationship, receive_pension, social_security_pension, ration_card_cat, rural_urban_id, lot_generated,  
                approval_rejected, bank_edited, bank_code, payment_count, last_paid_yymm, payment_error_status, av_status, 
                pension_amount, entry_type, ds_registration_no, rejected_cause, rejected_date, 
                ds_phase, ds_date, rejected_by, verification_date, 
                verified_by, approval_date, approved_by, dept_special, dept_mark, is_verified, is_approved, is_rejected from bandhu.beneficiary where id=" . $id . ")");
            } else if ($scheme_id == 1) {
                $arch_status = DB::statement("INSERT INTO johar.arc_beneficiary(id, dist_code, ben_fname, ben_mname, 
                ben_lname, gender, dob, ben_age, caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
                mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, ration_card_no, ahl_tin, 
                aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, bpl_total_score, assembly_code, 
                assembly_name, police_station, block_ulb_code, block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, 
                village_town_city, house_premise_no, post_office, pincode, residency_period, mobile_no, email, bank_name, 
                branch_name,  bank_ifsc, created_at, updated_at,  
                 created_by, created_by_level, created_by_dist_code, created_by_local_body_code, scheme_id, 
                type_disability, percentage_disability, certifying_auth, next_level_role_id,  nominate_name, nominate_address, 
                nominate_relationship, receive_pension, social_security_pension, ration_card_cat, rural_urban_id, lot_generated,  
                approval_rejected, bank_edited, bank_code, payment_count, last_paid_yymm, payment_error_status, av_status, 
                pension_amount, entry_type, ds_registration_no, rejected_cause, rejected_date, 
                ds_phase, ds_date, rejected_by, verification_date, 
                verified_by, approval_date, approved_by, dept_special, dept_mark, is_verified, is_approved, is_rejected
               ) (SELECT id, dist_code, ben_fname, ben_mname, 
                ben_lname, gender, dob, ben_age, caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
                mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, ration_card_no, ahl_tin, 
                aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, bpl_total_score, assembly_code, 
                assembly_name, police_station, block_ulb_code, block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, 
                village_town_city, house_premise_no, post_office, pincode, residency_period, mobile_no, email, bank_name, 
                branch_name,  bank_ifsc, created_at, updated_at,  
                 created_by, created_by_level, created_by_dist_code, created_by_local_body_code, scheme_id, 
                type_disability, percentage_disability, certifying_auth, next_level_role_id,  nominate_name, nominate_address, 
                nominate_relationship, receive_pension, social_security_pension, ration_card_cat, rural_urban_id, lot_generated,  
                approval_rejected, bank_edited, bank_code, payment_count, last_paid_yymm, payment_error_status, av_status, 
                pension_amount, entry_type, ds_registration_no, rejected_cause, rejected_date, 
                ds_phase, ds_date, rejected_by, verification_date, 
                verified_by, approval_date, approved_by, dept_special, dept_mark, is_verified, is_approved, is_rejected from johar.beneficiary where id=" . $id . ")");
            }

            $is_update = DB::table($scheme_schema . '.beneficiary')->where(['id' => $request->id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id])->whereNull('is_lb_imported')->update($input);
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
            $is_saved_log = $accept_reject_model->save();

            // if($request->id==9100885)
            // {
            //     dump($arch_status);dump($is_update); dump($doc_inserted_arch); dump($doc_inserted_del); dump($doc_inserted); dd($is_saved_log);

            // }
            // dump($arch_status);dump($is_update); dump($doc_inserted_arch); dump($doc_inserted_del); dump($doc_inserted); dd($is_saved_log);
            if ($arch_status && $is_update && $doc_inserted_arch && $doc_inserted_del && $doc_inserted && $is_saved_log) {
                DB::commit();
                DB::connection('pgsql_encwrite')->commit();
                if ($designation_id == 'Operator')
                    return redirect("application-list-read-only-edit?pr1=" . $scheme_schema)->with('success', 'Application Updated Successfully')
                        ->with('id', $id);
                else {
                    return redirect('/')->with('success', 'Application Updated Successfully');
                }
            } else {
                DB::connection('pgsql_encwrite')->rollback();
                DB::rollback();
                if ($designation_id == 'Operator')
                    return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', array('Some error.Please try again'));
                else {
                    return redirect('/')->with('danger', 'Some error.Please try again');
                }
            }

        } catch (\Exception $e) {
            dd($e);

            DB::connection('pgsql_encwrite')->rollback();


            DB::rollback();
            if ($designation_id == 'Operator') {
                return redirect("application-list-read-only-edit?pr1=" . $scheme_schema)->with('error', 'Some error.Please try again');
            } else {
                return redirect('/')->with('error', 'Some error.Please try again');
            }
        }


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
                'verification_status' => $Verified,
                'comments' => $comments
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
                'verification_status' => $Rejected,
                'comments' => $comments
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
                'approval_status' => $Approved,
                'approval_comments' => $comments
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
                'approval_status' => $Disapproved,
                'approval_comments' => $comments
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
            'approval_status' => $Approved,
            'approval_comments' => $comments
        ];

        foreach ($inputs as $input) {
            $is_status_updated = nhm_employee_details::where('id', $input)->update($data);
        }
        if ($is_status_updated) {
            return redirect("/")->with('success', 'Employee Records Approved Successfully');
        }
        //dd($inputs);
    }





    private function validateInput($request, $scheme_id, $add_edit_code, $is_lb_app)
    {


        $rules = [

            'gender' => 'required',
            // 'dob' => '',
            'txt_age' => 'required|numeric',

            'father_first_name' => 'required|string|max:200',
            'father_middle_name' => 'string|nullable',
            'father_last_name' => 'required|string|max:200',
            'mother_first_name' => 'required|string|max:200',
            'mother_middle_name' => 'string|nullable',
            'mother_last_name' => 'required|string|max:200',
            'caste_category' => 'required',
            'caste_certificate_no' => 'required',
            'marital_status' => 'required',

            'spouse_first_name' => 'string|nullable',
            'spouse_middle_name' => 'string|nullable',
            'spouse_last_name' => 'string|nullable',
            // 'if_bpl' => ,
            'bpl_seq_no' => 'string|nullable|max:12',
            'bpl_id_no' => 'string|nullable|max:12',
            'bpl_total_score' => 'integer|nullable',
            'monthly_income' => 'required|numeric|between: 0.00,999999.99',
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
            'bank_ifsc_code' => 'required|string|max:11',
        ];
        if ($scheme_id == 1) {
            $rules['ration_card_cat'] = 'required|string';
            $rules['ration_card_no'] = 'required|string|max:11';
        }
        if ($is_lb_app == 1) {
            $rules['first_name'] = 'string|nullable';
            $rules['middle_name'] = 'string|nullable';
            $rules['last_name'] = 'string|nullable';

        } else {
            $rules['first_name'] = 'required|string|max:200';
            $rules['middle_name'] = 'string|nullable';
            $rules['last_name'] = 'required|string|max:200';
        }
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
    public function editList(Request $request)
    {
        // dd($request->all());
        //dd(123);
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        //dd($designation_id);
        if ($designation_id != 'Operator') {
            // dd('Not Allowed');
            return redirect("/")->with('error', 'Not Allowed');
        }
        //dd($request->get('pr1'));
        if ($request->get('pr1')) {
            $short_code = $request->pr1;
            $scheme_id = $request->id;
            $scheme_row = Scheme::where('is_active', 1)->where('id', $scheme_id)->first();
            // dd($scheme_row);
            if (empty($scheme_row)) {
                return redirect("/")->with('error', 'Parameter not valid');
            }
            // dd($scheme_row->scheme_name);
            $scheme_name = $scheme_row->scheme_name;
            $schema_name = $scheme_row->short_code;
            $scheme_id = $scheme_row->id;
            $scheme_length = $scheme_row->scheme_length;
            $id_length = $scheme_row->id_length;
        } else {
            return redirect("/")->with('error', 'Parameter not valid');
        }
        // $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $level = $roleObj['mapping_level'];
                $is_urban = $roleObj['is_urban'];
                $distCode = $roleObj['district_code'];
                $is_state_login = $roleObj['is_state_login'];
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                } else {
                    $blockCode = $roleObj['taluka_code'];
                }
                break;
            }
        }
        // dd($is_active);
        if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disabled');
        }
        $report_type_name = 'Application List which are not yet verified or approved';
        // dd('ok');
        if (request()->ajax()) {

            // ($request->all());
            $condition = array();
            if ($is_state_login) {
                $condition["is_state"] = TRUE;
                $condition["next_level_role_id"] = $this->state_login_next_level_role_id_arr['entry'];
            } else {
                $condition["created_by_dist_code"] = $distCode;
                $condition["created_by_local_body_code"] = $blockCode;
            }
            $serachvalue = $request->search['value'];
            $limit = $request->input('length');
            $offset = $request->input('start');
            $totalRecords = 0;
            $filterRecords = 0;
            $data = array();
            if ($is_state_login) {
                $query = DB::table($schema_name . '.beneficiaries')->where($condition)->where('scheme_id', $scheme_id);
            } else
                $query = DB::table($schema_name . '.beneficiaries')->where($condition)->where('scheme_id', $scheme_id)->whereNull('next_level_role_id');
            if ($scheme_id == 11) {
                $query = $query->whereNull('process_nsap_flag');
            }
            $is_reverted = $request->is_reverted;
            // dd($query);
            if ($is_reverted == 1) {
                $query = $query->where($condition)->where('is_reverted', 1);
            }
            if ($is_reverted == 0) {
                $query = $query->whereNull('is_reverted');
            }
            $serachvalue = $request->search['value'];

            if (empty($serachvalue)) {
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                    'id',
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
                    'caste'
                ]);
            } else {
                if (is_numeric($serachvalue)) {
                    $ben_id = $serachvalue;
                    $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                        $query1->where('id', $ben_id)
                            ->orWhere('bank_code', $serachvalue);
                    });
                    $totalRecords = $query->count();
                    $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                        [
                            'id',
                            'created_by_dist_code',
                            'bank_code',
                            'ben_fname',
                            'block_ulb_name',
                            'gp_ward_name',
                            'bank_ifsc',
                            'village_town_city',
                            'scheme_id',
                            'lot_generated',
                            'payment_count',
                            'next_level_role_id',
                            'ben_lname',
                            'gender',
                            'ben_age',
                            'ben_mname',
                            'caste'
                        ]
                    );
                } else {
                    $query = $query->where(function ($query1) use ($serachvalue) {
                        $query1->where('ben_fname', 'like', $serachvalue . '%')
                            ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                            ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                            ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                    });
                    $totalRecords = $query->count();
                    $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                        [
                            'id',
                            'created_by_dist_code',
                            'bank_code',
                            'ben_fname',
                            'block_ulb_name',
                            'gp_ward_name',
                            'bank_ifsc',
                            'village_town_city',
                            'scheme_id',
                            'lot_generated',
                            'payment_count',
                            'next_level_role_id',
                            'ben_lname',
                            'gender',
                            'ben_age',
                            'ben_mname',
                            'caste'
                        ]
                    );
                }
                $filterRecords = count($data);
            }
            // dd($data);
            return datatables()
                ->of($data)
                ->setTotalRecords($totalRecords)
                ->setFilteredRecords($filterRecords)
                ->skipPaging()
                ->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {
                    $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

                    return $app_id;
                })
                ->addColumn('ben_name', function ($data) {
                    // return $data->getName();
                    return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                })
                ->addColumn('benf_name', function ($data) {
                    return "Father Name";
                })
                ->addColumn('ben_age', function ($data) {
                    return $data->ben_age;
                })
                ->addColumn('gender', function ($data) {
                    return $data->gender;
                })
                ->addColumn('bank_ifsc', function ($data) {
                    return $data->bank_ifsc;
                })
                ->addColumn('bank_code', function ($data) {
                    return $data->bank_code;
                })
                ->addColumn('village_town_city', function ($data) {
                    return $data->village_town_city;
                })
                ->addColumn('action', function ($data) use ($scheme_id) {
                    $val = '<button type="button" class="btn btn-info btn-view" value="' . $data->id . '">View</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

                    $val = $val . '<button type="button" class="btn btn-warning btn-update" value="' . $data->id . '">Update</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

                    $val = $val . '<button type="button" class="btn btn-danger btn-reject" value="' . $data->id . '">Reject</button>';

                    return $val;
                })
                ->rawColumns(['ben_id', 'ben_name', 'ben_age', 'gender', 'bank_ifsc', 'bank_code', 'village_town_city', 'action'])
                ->make(true);
        } else {

            return view(
                'commonView/editList',
                [
                    'district_code' => $distCode,
                    'block_code' => $blockCode,
                    'scheme' => $scheme_id,
                    'pr1' => $request->pr1,
                    'scheme_name' => $scheme_name,
                    'report_type_name' => $report_type_name,
                    'is_urban' => $is_urban

                ]
            );
        }
    }
    public function schemelistforUpdate(Request $request)
    {
        $auth = AuthChecker::ReportChecker();
        if ($auth) {
            $userId = Auth::user()->id;
            $scheme_list = DB::select(DB::raw("select id,display_name,pr1_code,scheme_name,short_code from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by rank"));
            return view('commonView.schemelistforUpdate', ['scheme_list' => $scheme_list]);
        }

    }
    function replaceNullValueWithEmptyString(&$value)
    {
        $value = $value === null ? "" : $value;
    }

    function applicationreject(Request $request)
    {

        $user_id = AuthChecker::getUserId();
        $c_time = date('Y-m-d H:i:s', time());
        $accept_reject_model = new AcceptRejectInfo;
        $accept_reject_model->created_at = $c_time;
        $accept_reject_model->application_id = $request->id;
        $accept_reject_model->scheme_id = $request->scheme_id;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->created_by_dist_code = $request->district_code;
        $accept_reject_model->created_by_local_body_code = $request->block_code;
        $accept_reject_model->ip_address = request()->ip();
        $accept_reject_model->op_type = 'OR';

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
        $input = ['next_level_role_id' => -4, 'is_rejected' => 1, 'is_verified' => 2, 'is_approved' => 2, 'rejected_date' => $c_time, 'rejected_by' => $user_id];
        DB::beginTransaction();
        $is_saved_log = $accept_reject_model->save();
        if ($is_saved_log) {
            $is_update = DB::table($schema . '.beneficiary')->where('id', $request->id)->where('created_by_dist_code', $request->district_code)->update($input);
        }
        if ($is_saved_log && $is_update) {
            DB::commit();
            return redirect('application-list-read-only-edit?pr1=' . $schema)->with('success', 'Rejected Succesfully!')
                ->with('id', $request->id);
        } else {
            DB::rollback();
            return redirect('application-list-read-only-edit?pr1=' . $schema)->with('errors', 'Error! Please try again.');
        }

    }
}
