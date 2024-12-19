<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\PermissionManagement;
use App\Helpers\AuthChecker;
use App\District;
use App\DocumentType;
use App\SchemeDocMap;
use App\SchemeGenSetting;
use App\Scheme;
use App\DsPhase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\BankDetails;
use App\Configduty;
use App\UrbanBody;
use App\Ward;
use App\GP;
use App\Taluka;
use App\Assembly;
use App\BenEntry;
use App\Helpers\DupCheck;
use App\JBPensionNew;
use App\AcceptRejectInfo;
use Illuminate\Support\Facades\Crypt;
use App\BenDocs;


class JBPensionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    public function index(Request $request)
    {
        // dd($request->all());
        $op_type = 0;
        $encryptedSchemeId = request('scheme_id');
        $type = (int) $request->type;
        $issue_text = '';
        $next_level_status = '';
        $scheme_id = Crypt::decrypt($encryptedSchemeId);
        $readonly = [];
        $auth = AuthChecker::OperatorChecker();
        if ($auth) {
            $entry_type = PermissionManagement::EntryChecker($scheme_id);
            if ($entry_type) {
                $mandatory = [];
                $already_inserted = [];
                $user_id = AuthChecker::getUserId();
                $designation_id_old = Auth::user()->designation_id;
                $districts = District::all();
                $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')
                    ->where('scheme_code', $scheme_id)->get();
                if (!$doc_id_list->isEmpty()) {
                    $doc_list_man = DocumentType::get()
                        ->whereIn("id", json_decode($doc_id_list[0]['doc_list_man']));
                    $doc_list_opt = DocumentType::get()
                        ->whereIn("id", json_decode($doc_id_list[0]['doc_list_opt']));
                } else {
                    $doc_list_man = collect();
                    $doc_list_opt = collect();
                }

                $doc_profile_image = DocumentType::get()
                    ->where("is_profile_pic", true)->first();
                foreach ($doc_list_man as $doc_man) {
                    array_push($mandatory, $doc_man->id);
                }

                $doc_profile_image_id = $doc_profile_image ? $doc_profile_image->id : 999;

                $scheme_setting = SchemeGenSetting::where('scheme_id', $scheme_id)->first();
                if ($scheme_setting) {
                    $ds_allow = $scheme_setting->allow_ds_entry;
                    $normal_entry = $scheme_setting->allow_normal_entry;
                } else {
                    $ds_allow = $normal_entry = 0;
                }

                $scheme = Scheme::where('id', $scheme_id)->first();
                $scheme_name = $scheme ? $scheme->scheme_name : '';

                $cur_ds_phase_arr = DsPhase::where('is_current', true)->first();
                $ds_phase_text = $cur_ds_phase_arr && $cur_ds_phase_arr->is_samadhan
                    ? 'Samasyaa Samadhan Jan Sanjog'
                    : 'Duare Sarkar';

                $required = [
                    'first_name',
                    'last_name',
                    'gender',
                    'dob',
                    'txt_age',
                    'father_first_name',
                    'father_last_name',
                    'mother_first_name',
                    'mother_last_name',
                    'caste_category',
                    'caste_certificate_no',
                    'marital_status',
                    'monthly_income',
                ];

                $row = collect();
                $assemly_list = collect([]);
                $block_munc_list = collect([]);
                $gp_ward_list = collect([]);
                $document_msg = "";
                $docs = collect([]);

                if ($type == 2 || $type == 3) {

                    if ($type == 3) {
                        $readonly = ['first_name', 'middle_name', 'last_name', 'bank_ifsc_code', 'name_of_bank', 'bank_branch', 'bank_account_number'];
                    }


                    $id = $request->app_id;
                    $model_name = 'App\\BenEntry';
                    $roleArray = $request->session()->get('role');
                    $op_type = $type;

                    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
                    $distCode = $duty_obj->district_code;
                    $blockCode = $duty_obj->is_urban == 1 ? $duty_obj->urban_body_code : $duty_obj->taluka_code;

                    $query = $model_name::where([
                        'id' => $id,
                        'created_by_dist_code' => $distCode,
                        'scheme_id' => $scheme_id
                    ]);

                    if ($designation_id_old == 'Verifier') {
                        $query = $query->whereNull('next_level_role_id');
                    } elseif ($designation_id_old == 'Approver') {
                        $query = $query->where('is_verified', 1)
                            ->where('is_approved', 0)
                            ->where('is_rejected', 0);
                    }

                    $row = $query->first();
                    if (empty($row)) {
                        return redirect("/")->with('error', 'Applicant Id not found');
                    }

                    if (!empty($row->dist_code)) {
                        $assemly_list = Assembly::where('district_code', $row->dist_code)
                            ->get(['ac_no', 'ac_name']);

                        if ($row->rural_urban_id == 1) {
                            $block_munc_list = UrbanBody::where('district_code', $row->dist_code)
                                ->get(['urban_body_code as code', 'urban_body_name as val']);
                            if (!empty($row->block_ulb_code)) {
                                $gp_ward_list = Ward::where('urban_body_code', $row->block_ulb_code)
                                    ->get(['urban_body_ward_code as code', 'urban_body_ward_name as val']);
                            }
                        } else {
                            $block_munc_list = Taluka::where('district_code', $row->dist_code)
                                ->get(['block_code as code', 'block_name as val']);
                            if (!empty($row->block_ulb_code)) {
                                $gp_ward_list = GP::where('block_code', $row->block_ulb_code)
                                    ->get(['gram_panchyat_code as code', 'gram_panchyat_name as val']);
                            }
                        }
                    }

                    $docs = BenDocs::where('beneficiary_id', $id)
                        ->where('created_by_dist_code', $distCode)
                        ->orderBy('document_type')
                        ->get();

                    foreach ($docs as $doc) {
                        array_push($already_inserted, $doc->document_type);
                    }
                }

                $required_doc = array_diff($mandatory, $already_inserted);
                if ($type == 3) {
                    if ($row->next_level_role_id == 0) {
                        $next_level_status = 'Approved';
                    } else
                        $next_level_status = 'Non Approved';
                    $issue_text = 'Need to provide ';
                    if ($row->legacy_import == true and $row->next_level_role_id == 0 and is_null($row->next_level_role_id_edit)) {
                        $issue_text = $issue_text . 'Legacy Data';
                    }
                    if ($row->no_aadhar == 1) {
                        $issue_text = $issue_text . '  Aadhaar Number,';
                    }
                    if ($row->no_mobile == 1) {
                        $issue_text = $issue_text . ' Mobile Number';
                    }
                }

                return view('JBformEntry.Entry', [
                    'issue_text' => $issue_text,
                    'next_level_status' => $next_level_status,
                    'docs' => $docs,
                    'required_doc' => $required_doc,
                    'type' => $type,
                    'op_type' => $op_type,
                    'row' => $row,
                    'ds_allow' => $ds_allow,
                    'normal_entry' => $normal_entry,
                    'scheme_id' => $scheme_id,
                    'districts' => $districts,
                    'doc_list_man' => $doc_list_man,
                    'doc_list_opt' => $doc_list_opt,
                    'profile_img' => $doc_profile_image_id,
                    'entry_type' => $entry_type,
                    'scheme_name' => $scheme_name,
                    'ds_phase_text' => $ds_phase_text,
                    'assemly_list' => $assemly_list,
                    'block_munc_list' => $block_munc_list,
                    'gp_ward_list' => $gp_ward_list,
                    'document_msg' => $document_msg,
                    'readonly' => $readonly,
                ]);
            }
        }
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $scheme_id = $request->scheme_id;
        $Auth = AuthChecker::OperatorChecker();
        if ($Auth) {
            $user_id = AuthChecker::getUserId();
            $entry_type = PermissionManagement::EntryChecker($scheme_id);
            if ($entry_type) {
                // dd($request->all());
                $type = $request->type;
                $id = $request->app_id ?? null;
                $formType = $request->entry_type;
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
                $ds_phase = DsPhase::where('is_current', TRUE)->first();
                $isValidarr = $this->validateInput($request, $scheme_id, $type, $formType);
                if ($isValidarr['is_valid'] == false) {
                    if ($type == 1) {
                        return redirect('jb-pension?scheme_id=' . encrypt($scheme_id) . '&type=' . $type . '&app_id=' . $id)
                            ->with('errors', $isValidarr['errors'])
                            ->withInput($request->all());
                    } else {

                        return redirect('jb-pension?scheme_id=' . encrypt($scheme_id) . '&type=' . $type . '&app_id=' . $id)
                            ->with('errors', $isValidarr['errors']);

                    }
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
                    $scheme_name = $scheme_obj->scheme_name;
                } else {
                    $schema = "pension";
                }

                $BankCheck = DupCheck::dupBankCheckSame($scheme_id, $bank_account_number, $id);
                if ($BankCheck) {
                    $errors[] = "Bank account is Duplicate ";
                }
                $AadharCheck = DupCheck::dupAadharCheckSame($scheme_id, $request->aadhar_no, $id);
                if ($AadharCheck) {
                    $errors[] = "Aadhar Number is Duplicate ";
                }
                $MobileCheck = DupCheck::dupMobileCheckSame($scheme_id, $request->mobile_no, $id);
                if ($MobileCheck) {
                    $errors[] = "Mobile number is Duplicate ";
                }
                $CasteCheck = DupCheck::dupCasteCheckSame($scheme_id, $request->caste_certificate_no, $id);
                if ($CasteCheck) {
                    $errors[] = "Caste Certificate number is Duplicate ";
                }
                $BankCheckCross = DupCheck::dupBankCheckCross($scheme_id, $bank_account_number, $id);
                if ($BankCheckCross) {
                    $errors[] = "Bank account is Duplicate with Cross Scheme";
                }
                $MobileCheckCross = DupCheck::dupMobileCheckCross($scheme_id, $request->mobile_no, $id);
                if ($MobileCheckCross) {
                    $errors[] = "Mobile no is Duplicate with Cross Scheme";
                }
                $AadharCheckCross = DupCheck::dupAadharCheckCross($scheme_id, $request->aadhar_no, $id);
                if ($AadharCheckCross) {
                    $errors[] = "Aadhar no is Duplicate with Cross Scheme";
                }
                $CasteCheckCross = DupCheck::dupCasteCheckCross($scheme_id, $request->caste_certificate_no, $id);
                if ($CasteCheckCross) {
                    $errors[] = "Caste Certificate is Duplicate with Cross Scheme ";
                }

                if (!empty($errors)) {
                    return back()->with('errors', $errors)->withInput();
                }

                // dd($id);
                $JBPension = $id ? BenEntry::find($id) : new BenEntry();
                $i = 0;
                $c_time = date('Y-m-d H:i:s');

                $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
                $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
                $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
                $doc_list = array_merge($doc_list_man, $doc_list_opt);
                // dd($doc_list);
                $upload_file = array();
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
                // dd($upload_file);

                if ($request->urban_code == 1) {
                    $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
                    $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();
                    if (!empty($request->urban_body_name))
                        $JBPension->block_ulb_name = trim($block_ulb->urban_body_name);
                    if (!empty($request->urban_body_ward_name))
                        $JBPension->gp_ward_name = trim($gp_ward->urban_body_ward_name);
                } else {
                    $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
                    $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();
                    if (!empty($request->block_name))
                        $JBPension->block_ulb_name = trim($block_ulb->block_name);
                    if (!empty($request->gram_panchyat_name))
                        $JBPension->gp_ward_name = trim($gp_ward->gram_panchyat_name);
                }
                $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
                $assembly_name = $assembly->ac_name;

                if (!empty($request->entry_type))
                    $JBPension->entry_type = trim($request->entry_type);
                if ($request->entry_type == 'Form through Duare Sarkar camp') {
                    if (!empty($request->ds_registration_no))
                        $JBPension->ds_registration_no = trim($request->ds_registration_no);
                    if (!empty($request->ds_date))
                        $JBPension->ds_date = trim($request->ds_date);
                    if (!empty($ds_phase)) {
                        $JBPension->ds_phase = $ds_phase->phase_code;
                    }
                }

                // Personal Details
                if (!empty($request->first_name))
                    $JBPension->ben_fname = trim($request->first_name);
                if (!empty($request->middle_name))
                    $JBPension->ben_mname = trim($request->middle_name);
                if (!empty($request->last_name))
                    $JBPension->ben_lname = trim($request->last_name);
                if (!empty($request->gender))
                    $JBPension->gender = trim($request->gender);
                if (!empty($request->dob))
                    $JBPension->dob = trim($request->dob);
                if (!empty($request->txt_age))
                    $JBPension->ben_age = trim($request->txt_age);
                if (!empty($request->father_first_name))
                    $JBPension->father_fname = trim($request->father_first_name);
                if (!empty($request->father_middle_name))
                    $JBPension->father_mname = trim($request->father_middle_name);
                if (!empty($request->father_last_name))
                    $JBPension->father_lname = trim($request->father_last_name);
                if (!empty($request->mother_first_name))
                    $JBPension->mother_fname = trim($request->mother_first_name);
                if (!empty($request->mother_middle_name))
                    $JBPension->mother_mname = trim($request->mother_middle_name);
                if (!empty($request->mother_last_name))
                    $JBPension->mother_lname = trim($request->mother_last_name);
                if (!empty($request->caste_category))
                    $JBPension->caste = trim($request->caste_category);
                if (!empty($request->caste_certificate_no))
                    $JBPension->caste_certificate_no = trim($request->caste_certificate_no);
                if (!empty($request->marital_status))
                    $JBPension->marital_status = trim($request->marital_status);
                if (!empty($request->monthly_income))
                    $JBPension->mothly_income = trim($request->monthly_income);
                if (!empty($request->spouse_first_name))
                    $JBPension->spouse_fname = trim($request->spouse_first_name);
                if (!empty($request->spouse_middle_name))
                    $JBPension->spouse_mname = trim($request->spouse_middle_name);
                if (!empty($request->spouse_last_name))
                    $JBPension->spouse_lname = trim($request->spouse_last_name);

                if ($scheme_id == 5) {
                    if (!empty($request->fisherman_comm))
                        $JBPension->fisherman_comm = trim($request->fisherman_comm);
                    if (!empty($request->phy_hadi_status))
                        $JBPension->phy_hadi_status = trim($request->phy_hadi_status);

                }
                if ($scheme_id == 11) {
                    if (!empty($request->husband_first_name))
                        $JBPension->husband_fname = trim($request->husband_first_name);
                    if (!empty($request->husband_middle_name))
                        $JBPension->husband_mname = trim($request->husband_middle_name);
                    if (!empty($request->husband_last_name))
                        $JBPension->husband_lname = trim($request->husband_last_name);
                }

                if ($scheme_id == 17) {
                    if (!empty($request->app_phase))
                        $JBPension->app_phase = $request->app_phase;
                    if (!empty($request->temple_type))
                        $JBPension->temple_type = $request->temple_type;

                }

                if ($scheme_id == 2) {
                    if (!empty($request->disablity_type))
                        $JBPension->type_disability = $request->disablity_type;
                    if (!empty($request->disablity_type_percentage))
                        $JBPension->percentage_disability = $request->disablity_type_percentage;
                    if (!empty($request->disablity_type_authority))
                        $JBPension->certifying_auth = $request->disablity_type_authority;
                    if (!empty($request->disability_designation))
                        $JBPension->disability_designation = $request->disability_designation;
                }
                // Personal Indentification Details 
                if (!empty($request->ration_card_cat))
                    $JBPension->ration_card_cat = trim($request->ration_card_cat);
                if (!empty($request->ration_card_no))
                    $JBPension->ration_card_no = trim($request->ration_card_no);
                if (!empty($request->ahl_tin))
                    $JBPension->ahl_tin = trim($request->ahl_tin);
                if (!empty($request->aadhar_no))
                    $JBPension->aadhar_no = trim($request->aadhar_no);
                if (!empty($request->epic_voter_id))
                    $JBPension->epic_voter_id = trim($request->epic_voter_id);
                if (!empty($request->pan_no))
                    $JBPension->pan_no = trim($request->pan_no);
                if (!empty($request->bpl_seq_no))
                    $JBPension->bpl_seq_no = trim($request->bpl_seq_no);
                if (!empty($request->bpl_id_no))
                    $JBPension->bpl_id_no = trim($request->bpl_id_no);
                if (!empty($request->bpl_total_score))
                    $JBPension->bpl_total_score = intval($request->bpl_total_score);


                if ($scheme_id == 2) {
                    if (trim($request->aadhar_exits) == 1) {
                        if (!empty($request->aadhar_no))
                            $JBPension->aadhar_no = $request->aadhar_no;
                    } else {
                        if (!empty($request->withoutaadhar_cause))
                            $JBPension->withoutaadhar_cause_code = $request->withoutaadhar_cause;
                        if (trim($request->withoutaadhar_cause) == 'Others') {
                            if (!empty($request->withoutaadhar_cause_other))
                                $JBPension->withoutaadhar_cause = trim($request->withoutaadhar_cause_other);
                        } else {
                            if (!empty($request->withoutaadhar_cause))
                                $JBPension->withoutaadhar_cause = $request->withoutaadhar_cause;
                        }
                    }
                }


                //Contact Details 
                if (!empty($request->district))
                    $JBPension->dist_code = trim($request->district);
                if (!empty($request->urban_code))
                    $JBPension->rural_urban_id = trim($request->urban_code);
                if (!empty($request->asmb_cons))
                    $JBPension->assembly_code = trim($request->asmb_cons);
                if (!empty($request->assembly_name))
                    $JBPension->assembly_name = trim($assembly_name);
                if (!empty($request->police_station))
                    $JBPension->police_station = trim($request->police_station);
                if (!empty($request->block))
                    $JBPension->block_ulb_code = trim($request->block);
                if (!empty($request->gp_ward))
                    $JBPension->gp_ward_code = trim($request->gp_ward);
                if (!empty($request->village))
                    $JBPension->village_town_city = trim($request->village);
                if (!empty($request->house))
                    $JBPension->house_premise_no = trim($request->house);
                if (!empty($request->post_office))
                    $JBPension->post_office = trim($request->post_office);
                if (!empty($request->pin_code))
                    $JBPension->pincode = trim($request->pin_code);
                if (!empty($request->residency_period))
                    $JBPension->residency_period = trim($request->residency_period);
                if (!empty($request->mobile_no))
                    $JBPension->mobile_no = trim($request->mobile_no);
                if (!empty($request->email))
                    $JBPension->email = trim($request->email);
                if ($scheme_id == 17) {
                    if (!empty($request->district_cur))
                        $JBPension->dist_code_cur = $request->district_cur;

                    if (!empty($request->asmb_cons_cur))
                        $JBPension->assembly_code_cur = $request->asmb_cons_cur;

                    if (!empty($request->urban_code_cur))
                        $JBPension->rural_urban_id_cur = $request->urban_code_cur;

                    if (!empty($request->block_cur))
                        $JBPension->block_ulb_code_cur = $request->block_cur;

                    if (!empty($request->gp_ward_cur))
                        $JBPension->gp_ward_code_cur = $request->gp_ward_cur;

                    if (!empty($request->village_cur))
                        $JBPension->village_town_city_cur = $request->village_cur;

                    if (!empty($request->house_cur))
                        $JBPension->house_premise_no_cur = $request->house_cur;

                    if (!empty($request->post_office_cur))
                        $JBPension->post_office_cur = $request->post_office_cur;

                    if (!empty($request->pin_code_cur))
                        $JBPension->pincode_cur = $request->pin_code_cur;

                    if (!empty($request->police_station_cur))
                        $JBPension->police_station_cur = $request->police_station_cur;


                }

                // Bank Details
                if (!empty($request->name_of_bank))
                    $JBPension->bank_name = trim($request->name_of_bank);
                if (!empty($request->bank_branch))
                    $JBPension->branch_name = trim($request->bank_branch);
                if (!empty($request->bank_account_number))
                    $JBPension->bank_code = trim($request->bank_account_number);
                $JBPension->npci_bank_code = trim($new_bank_code);
                if (!empty($request->bank_ifsc_code))
                    $JBPension->bank_ifsc = trim($request->bank_ifsc_code);

                if ($scheme_id == 13) {
                    //Land Details 
                    if (trim($request->f_land_array) != '') {

                        $f_land_array = json_decode($request->f_land_array, true);
                        $f_land_array = json_encode($f_land_array);
                    } else {
                        $f_land_array = null;
                    }
                    if (!empty($f_land_array))
                        $JBPension->land_json = $f_land_array;

                    if (!empty($request->first_name))
                        $JBPension->cultivation_by_applicant = trim($request->cultivation_by_applicant);
                    if (!empty($request->first_name))
                        $JBPension->source_income = trim($request->source_income);
                    if (!empty($request->first_name))
                        $JBPension->any_other_benefitis = trim($request->any_other_benefitis);
                    //Family Details
                    if (trim($request->f_member_array) != '') {
                        $f_member_array = json_decode($request->f_member_array, true);
                        $f_member_array = json_encode($f_member_array);
                    } else {
                        $f_member_array = null;
                    }
                    if (!empty($f_member_array))
                        $JBPension->member_json = $f_member_array;

                }

                if ($scheme_id == 17) {
                    if (!empty($request->mouza_name))
                        $JBPension->mouza_name = $request->mouza_name;
                    if (!empty($request->land_jlno))
                        $JBPension->land_jlno = $request->land_jlno;
                    if (!empty($request->khatian_no))
                        $JBPension->khatian_no = $request->khatian_no;
                    if (!empty($request->plot_no))
                        $JBPension->plot_no = $request->plot_no;
                    if (!empty($request->land_area))
                        $JBPension->land_area = $request->land_area;
                    if (!empty($request->land_holdername))
                        $JBPension->land_holdername = $request->land_holdername;
                }



                //Self Decleration Details
                if (!empty($request->nominate_name))
                    $JBPension->nominate_name = trim($request->nominate_name);
                if (!empty($request->nominate_address))
                    $JBPension->nominate_address = trim($request->nominate_address);
                if (!empty($request->nominate_relationship))
                    $JBPension->nominate_relationship = trim($request->nominate_relationship);
                if ($request->social_security_pension != "") {
                    $social_security_pension = implode(',', $request->social_security_pension);
                    $JBPension->social_security_pension = $social_security_pension;
                }
                if ($request->receive_pension != "") {
                    $receive_pension = implode(',', $request->receive_pension);
                    $JBPension->receive_pension = $receive_pension;
                }
                if (!empty($request->av_status))
                    $JBPension->av_status = trim($request->av_status);
                if (!empty($request->receiving_pension_other_source_1))
                    $JBPension->receiving_pension_other_source_1 = trim($request->receiving_pension_other_source_1);
                if (!empty($request->receiving_pension_other_source_2))
                    $JBPension->receiving_pension_other_source_2 = trim($request->receiving_pension_other_source_2);

                if ($scheme_id == 17) {
                    if (!empty($request->ssp_y_n))
                        $JBPension->ssp_y_n = $request->ssp_y_n;
                    if (!empty($request->pucca_house_y_n))
                        $JBPension->pucca_house_y_n = $request->pucca_house_y_n;
                }

                //Additional Details
                $JBPension->created_by = $user_id;
                $JBPension->created_at = date('Y-m-d');
                $JBPension->created_by_level = $duty_obj->mapping_level;
                $JBPension->created_by_dist_code = $district_code;
                $JBPension->created_by_local_body_code = $created_by_local_body_code;
                $JBPension->scheme_id = $scheme_id;
                $JBPension->entry_datetime = $c_time;
                $JBPension->ip_address = $request->ip();
                $JBPension->action_by = $user_id;
                $JBPension->action_ip_address = $request->ip();
                $JBPension->action_type = $request->class_basename(request()->route()->getAction()['controller']) . '@' . $type;
                DB::beginTransaction();
                DB::connection('pgsql_encwrite')->beginTransaction();
                try {
                    $doc_inserted = false;
                    $is_saved = $JBPension->save();

                    if ($is_saved) {
                        $beneficiary_id = $JBPension->id;

                        if (!empty($upload_file)) { // Check if $upload_file is not empty
                            foreach ($upload_file as $up_file) {
                                $query = "
                                    SELECT jb_doc.ben_docs_insert_archive(
                                        in_beneficiary_id => :beneficiary_id,
                                        in_scheme_id => :scheme_id,
                                        in_document_type => :document_type,
                                        in_attched_document => :attched_document,
                                        in_created_by_level => :created_by_level,
                                        in_created_by => :created_by,
                                        in_ip_address => :ip_address,
                                        in_document_extension => :document_extension,
                                        in_document_mime_type => :document_mime_type,
                                        in_created_by_dist_code => :created_by_dist_code,
                                        in_created_by_local_body_code => :created_by_local_body_code,
                                        in_doc_type_name => :doc_type_name,
                                        in_datetime => :created_at
                                    )
                                ";

                                $params = [
                                    'beneficiary_id' => $beneficiary_id,
                                    'scheme_id' => $scheme_id,
                                    'document_type' => $up_file['document_type'],
                                    'attched_document' => $up_file['attched_document'],
                                    'created_by_level' => $up_file['created_by_level'],
                                    'created_by' => $up_file['created_by'],
                                    'ip_address' => $up_file['ip_address'],
                                    'document_extension' => $up_file['document_extension'],
                                    'document_mime_type' => $up_file['document_mime_type'],
                                    'created_by_dist_code' => $up_file['created_by_dist_code'],
                                    'created_by_local_body_code' => $up_file['created_by_local_body_code'],
                                    'doc_type_name' => $up_file['doc_type_name'],
                                    'created_at' => $c_time,
                                ];

                                // Execute the query with bound parameters
                                $doc_inserted = DB::connection('pgsql_encwrite')->select($query, $params);
                            }
                        } else {
                            if ($type == 2 || $type == 3) {
                                $doc_inserted = true;
                            } else {
                                $doc_inserted = false;
                            }
                        }

                        if ($doc_inserted) {
                            DB::commit();
                            DB::connection('pgsql_encwrite')->commit();
                            $is_inserted = true;
                        } else {
                            DB::rollBack();
                            DB::connection('pgsql_encwrite')->rollBack();
                            $is_inserted = false;
                        }

                        if ($is_inserted) {
                            $accept_reject_model = new AcceptRejectInfo();
                            $accept_reject_model->created_at = $c_time;
                            $accept_reject_model->application_id = $beneficiary_id;
                            $accept_reject_model->scheme_id = $scheme_id;
                            $accept_reject_model->user_id = $user_id;
                            $accept_reject_model->created_by_dist_code = $district_code;
                            $accept_reject_model->created_by_local_body_code = $created_by_local_body_code;
                            $accept_reject_model->op_type = class_basename(request()->route()->getAction()['controller']) . '@' . $type;
                            $accept_reject_model->ip_address = $request->ip();
                            $accept_reject_model->save();

                            $return_status = 'success';
                            $msg = "Application Submitted Successfully on Scheme Name " . $scheme_name .
                                " and Beneficiary ID " . $district_code . "0" . $scheme_id . "0000" . $beneficiary_id;
                            return redirect('jb-pension?scheme_id=' . encrypt($scheme_id) . '&type=' . $type . '&app_id=' . $id)
                                ->with($return_status, $msg);
                        } else {
                            // Initialize $errors as an array
                            $errors = [];
                            $return_status = 'error';
                            $errorMsg = "Insertion Failed..Please try again.";
                            array_push($errors, $errorMsg);

                            return redirect('jb-pension?scheme_id=' . encrypt($scheme_id) . '&type=' . $type . '&app_id=' . $id)
                                ->with('errors', $errors)
                                ->withInput($request->all());
                        }
                    }
                } catch (\Exception $e) {
                    dd([
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    $return_status = 'error';
                    return redirect('jb-pension?scheme_id=' . encrypt($scheme_id) . '&type=' . $type . '&app_id=' . $id)->with($return_status, $e->getMessage());
                }

            }

        }

    }



    private function validateInput($request, $scheme_id, $add_edit_code, $formType)
    {
        $caste_key = array_keys(Config::get('constants.caste'));
        $marital_status_key = array_keys(Config::get('constants.marital_status'));
        $gender_key = array_keys(Config::get('constants.gender'));
        $today = date("Y-m-d");
        $entry_type_arr = array('Normal', 'Form through Duare Sarkar camp');
        if ($add_edit_code == 1) {
            $entry_type_r = "required";
        } else if ($add_edit_code == 2 || $add_edit_code == 3) {
            $entry_type_r = "nullable";
        } else {
            $entry_type_r = "nullable";
        }
        $rules = [
            'entry_type' => $entry_type_r . '|in:' . implode(",", $entry_type_arr),

            'first_name' => 'required|string|max:200',
            'middle_name' => 'string|nullable',
            'last_name' => 'required|string|max:200',
            'gender' => 'required',
            'txt_age' => 'required|numeric',
            'father_first_name' => 'required|string|max:200',
            'father_middle_name' => 'string|nullable',
            'father_last_name' => 'required|string|max:200',
            'mother_first_name' => 'required|string|max:200',
            'mother_middle_name' => 'string|nullable',
            'mother_last_name' => 'required|string|max:200',
            'caste_category' => 'required',

            'marital_status' => 'required',
            'spouse_first_name' => 'string|nullable',
            'spouse_middle_name' => 'string|nullable',
            'spouse_last_name' => 'string|nullable',
            'monthly_income' => 'required|numeric|between: 0.00,999999.99',


            'bpl_seq_no' => 'string|nullable|max:12',
            'bpl_id_no' => 'string|nullable|max:12',
            'bpl_total_score' => 'integer|nullable',
            'ahl_tin' => 'string|nullable|max:100',

            'pan_no' => 'string|nullable|max:12',

            'district' => 'required|string',
            'asmb_cons' => 'required|string',
            'block' => 'required|string',
            'gp_ward' => 'required|string',
            'police_station' => 'required|string',
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
        if (in_array($scheme_id, [1, 8, 9, 17])) {
            $rules['ration_card_cat'] = 'required|string';
            $rules['ration_card_no'] = 'required|string|max:11';
        }
        if (in_array($scheme_id, [1, 3, 5, 8, 9, 10, 11, 17])) {
            $rules['epic_voter_id'] = 'required|string|max:20';

        }
        if (in_array($scheme_id, [1, 3, 19])) {
            $rules['caste_certificate_no'] = 'required';

        }

        if ($scheme_id == 17) {
            $rules = [
                'mouza_name' => 'required_if:code,=, $housingSlug',
                'land_jlno' => 'required_if:code,=,$housingSlug',
                'khatian_no' => 'required_if:code,=,$housingSlug',
                'plot_no' => 'required_if:code,=$housingSlug',
                'land_area' => 'required_if:code,=,$housingSlug',
                'land_holdername' => 'required_if:code,=,$housingSlug',
            ];
        }

        if ($scheme_id == 2) {
            $rules['aadhar_exits'] = 'required|in:0,1';
            if (trim($request->aadhar_exits) == 1) {
                $rules['aadhar_no'] = 'required|numeric|digits:12';
            } else {
                $rules['withoutaadhar_cause'] = 'required';
                if (trim($request->withoutaadhar_cause) == 'Others') {
                    $rules['withoutaadhar_cause_other'] = 'required';
                }
            }
            $rules['disablity_type'] = 'required';
            $rules['disablity_type_percentage'] = 'required';
            $rules['disablity_type_authority'] = 'required';
            $rules['disability_designation'] = 'required';

            if (trim($request->aadhar_exits) == 1) {
                $rules['aadhar_no'] = 'required|numeric|digits:12';
            } else {
                $rules['withoutaadhar_cause'] = 'required';
                if (trim($request->withoutaadhar_cause) == 'Others') {
                    $rules['withoutaadhar_cause_other'] = 'required';
                }
            }
        } else {
            $rules['aadhar_no'] = 'required|numeric|digits:12';
        }
        if ($formType == 'Form through Duare Sarkar camp') {
            $rules['ds_registration_no'] = 'required_if:entry_type,==,Form through Duare Sarkar camp|max:25';
            $rules['ds_date'] = 'required_if:entry_type,Form through Duare Sarkar camp|nullable|date|before_or_equal:' . $today;
        }
        if ($scheme_id == 13) {
            $rules['cultivation_by_applicant'] = 'required|string';
        }




        //dd($rules);
        $attributes = array();
        $messages = array();
        $attributes['entry_type'] = 'Application Type';
        $attributes['ds_registration_no'] = 'Duare Sarkar Registration No.';
        $attributes['ds_date'] = 'Duare Sarkar Date';
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
        $attributes['disablity_type'] = 'Type of Disability';
        $attributes['disablity_type_percentage'] = 'Percentage of Disablity';
        $attributes['disablity_type_authority'] = 'Authority Name';
        $attributes['disability_designation'] = 'Authority Designation';
        $attributes['monthly_income'] = 'Monthly Family Income (In Rs)';

        $attributes['ration_card_cat'] = 'Digital Ration Card Number';
        $attributes['ration_card_no'] = 'Digital Ration Card Number';
        $attributes['ahl_tin'] = 'AHL TIN';
        $attributes['withoutaadhar_cause'] = 'Reason for Which Aadhaar Cannot be Generated';
        $attributes['withoutaadhar_cause_other'] = 'Specify Other Reason';
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
        $attributes['mouza_name'] = 'Mouza Name';
        $attributes['land_jlno'] = 'Land JL No.';
        $attributes['khatian_no'] = 'Khatian No.';
        $attributes['plot_no'] = 'Plot No.';
        $attributes['land_area'] = 'Land Area (in sq. ft.)';
        $attributes['land_holdername'] = 'Land Holder Name';

        $attributes['cultivation_by_applicant'] = 'Cultivation by Applicant';

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


    public function list_view(Request $request)
    {
        $auth = AuthChecker::OperatorChecker();
        if ($auth) {
            $user_id = AuthChecker::getUserId();
            $scheme_id = $request->id;
            $scheme_row = Scheme::where('is_active', 1)->where('id', $scheme_id)->first();
            if (empty($scheme_row)) {
                return redirect("/")->with('error', 'Parameter not valid');
            }
            $scheme_name = $scheme_row->scheme_name;
            $scheme_id = $scheme_row->id;

            $configDuty = Configduty::select('scheme_id', 'district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')
                ->where('user_id', $user_id)
                ->where('is_active', 1)
                ->where('scheme_id', $scheme_id)
                ->first();
            if (empty($configDuty)) {
                return redirect('/')->with('error', 'No Duty Assigned');
            }
            $is_urban = $configDuty->is_urban;
            $district_code = $configDuty->district_code;
            $blockUlbCode = null;
            $urban_bodys = null;
            $talukas = null;
            $gps = null;
            if ($is_urban == 1) {
                $blockUlbCode = $configDuty->urban_body_code;
                $urban_bodys = UrbanBody::where('sub_district_code', $blockUlbCode)->select('urban_body_code', 'urban_body_name')->get();
            } elseif ($is_urban == 2) {
                $blockUlbCode = $configDuty->taluka_code;
                $gps = GP::where('block_code', $blockUlbCode)->get();
            }

            // dd($is_active);
            $report_type_name = 'Application List which are not yet verified or approved';

            return view(
                'JBupdate/editlist',
                [
                    'district_code' => $district_code,
                    'block_code' => $blockUlbCode,
                    'scheme' => $scheme_id,
                    'scheme_name' => $scheme_name,
                    'report_type_name' => $report_type_name,
                    'is_urban' => $is_urban

                ]
            );

        }
    }

    public function ben_list(Request $request)
    {
        $scheme_id = $request->scheme;
        $schema_name = Scheme::where('id',$scheme_id)->value('short_code');
        $user_id = AuthChecker::getUserId();

        // Ensure $dutyObj is fetched properly and is not null
        $dutyObj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
        if (!$dutyObj) {
            return response()->json(['error' => 'Duty configuration not found'], 404);
        }

        $distCode = $dutyObj['district_code'] ?? null;
        $blockCode = $dutyObj['is_urban'] == 1
            ? ($dutyObj['urban_body_code'] ?? null)
            : ($dutyObj['taluka_code'] ?? null);

        $condition = [
            "created_by_dist_code" => $distCode,
            "created_by_local_body_code" => $blockCode,
        ];

        $query = DB::table($schema_name . '.beneficiaries')
            ->where($condition)
            ->where('scheme_id', $scheme_id)
            ->whereNull('next_level_role_id');

        if ($scheme_id == 11) {
            $query->whereNull('process_nsap_flag');
        }

        if ($request->is_reverted == 1) {
            $query->where('is_reverted', 1);
        } elseif ($request->is_reverted == 0) {
            $query->whereNull('is_reverted');
        }

        $serachvalue = $request->search['value'];
        $limit = $request->input('length', 20);
        $offset = $request->input('start', 0);

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
                $query->where(function ($q) use ($serachvalue) {
                    $q->where('id', $serachvalue)
                        ->orWhere('bank_code', $serachvalue);
                });
            } else {
                $query->where(function ($q) use ($serachvalue) {
                    $q->where('ben_fname', 'like', $serachvalue . '%')
                        ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                        ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                        ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                });
            }
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
        }

        $filterRecords = count($data);

        return datatables()
            ->of($data)
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
            ->addColumn('application_id', function ($data) {
                return $data->created_by_dist_code . substr('0' . $data->scheme_id, -6) . substr('0000000' . $data->id, -6);
            })
            ->addColumn('ben_name', function ($data) {
                return trim($data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname);
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
                $val = '<button type="button" class="btn btn-info btn-view" value="' . $data->id . '" data-scheme = '.$scheme_id.'>View</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

                $val = $val . '<button type="button" class="btn btn-warning btn-update" value="' . $data->id . '">Update</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

                $val = $val . '<button type="button" class="btn btn-danger btn-reject" value="' . $data->id . '">Reject</button>';

                return $val;
            })
            ->rawColumns(['action'])
            ->make(true);
    }





    public function update(Request $request)
    {
        dd($request->all());
    }



    public function wcdlist(Request $request, $scheme_id)
    {
        $scheme_id = (int) $scheme_id;
        $report_type_name = 'Application List ';
        $auth = AuthChecker::ReportChecker();
        if ($auth) {
            $scheme = Scheme::find($scheme_id);
            $user_id = AuthChecker::getUserId();
            $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
            if (empty($duty_obj)) {
                return redirect("/")->with('danger', 'Not Allowed');
            }
            // dd($duty_obj);
            $district_code = $duty_obj->district_code;
            $is_urban = $duty_obj->is_urban;
            if ($is_urban == 1) {
                $blk_ulb_code = $duty_obj->urban_body_code;
            } else if ($is_urban == 2) {
                $blk_ulb_code = $duty_obj->taluka_code;
            } else {
                return redirect("/")->with('danger', 'Not Allowed');
            }
            $scheme_name = $scheme->scheme_name;
            return view('wcd-report-list.list', [
                'district_code' => $district_code,
                'scheme' => $scheme_id,
                'scheme_name' => $scheme_name,
                'report_type_name' => $report_type_name,
                'is_urban' => $is_urban

            ]);

        }

    }
    public function getData(Request $request)
    {
        // dd($request->all());
        $scheme_id = $request->scheme;
        $user_id = AuthChecker::getUserId();
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
        if (empty($duty_obj)) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        // dd($duty_obj);
        $district_code = $duty_obj->district_code;
        $is_urban = $duty_obj->is_urban;
        if ($is_urban == 1) {
            $blk_ulb_code = $duty_obj->urban_body_code;
        } else if ($is_urban == 2) {
            $blk_ulb_code = $duty_obj->taluka_code;
        } else {
            return redirect("/")->with('danger', 'Not Allowed');
        }

        $condition = array();
        $condition["created_by_dist_code"] = $district_code;
        $condition["created_by_local_body_code"] = $blk_ulb_code;
        // $condition["next_level_role_id"] = 0;
        $serachvalue = $request->search['value'];
        $limit = $request->input('length');
        $offset = $request->input('start');
        $filter_status = $request->input('filter_status');
        $filter_status_new = $request->input('filter_status_new');
        $totalRecords = 0;
        $filterRecords = 0;
        $data = array();
        $process_status = $request->filter_status;
        $condition["process_code"] = $process_status;
        // dd($condition);
        $ben_id = array();
        $ib_ben_id = DB::table('pension.mandatory_field')->where($condition)->distinct()->get(['ben_id'])->toArray();
        foreach ($ib_ben_id as $ids) {
            $ben_id[] = $ids->ben_id;
        }
        $query = BenEntry::whereIn('id', $ben_id)->where('scheme_id', $scheme_id);
        $serachvalue = $request->search['value'];
        if (empty($serachvalue)) {
            $totalRecords = $query->count();
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                'unlock_status',
                'legacy_import',
                'no_aadhar',
                'no_mobile',
                'mobile_no',
                'aadhar_no',
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
                'next_level_role_id_edit',
                'caste'
            ]);
        } else {
            if (is_numeric($serachvalue)) {
                $ben_id = substr($serachvalue, -7);
                $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                    $query1->where('id', $ben_id)
                        ->orWhere('bank_code', $serachvalue);
                });
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                    [
                        'unlock_status',
                        'legacy_import',
                        'no_aadhar',
                        'no_mobile',
                        'mobile_no',
                        'aadhar_no',
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
                        'next_level_role_id_edit',
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
                        'unlock_status',
                        'legacy_import',
                        'no_aadhar',
                        'no_mobile',
                        'mobile_no',
                        'aadhar_no',
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
                        'next_level_role_id_edit',
                        'caste'
                    ]
                );
            }
            $filterRecords = count($data);
        }
        // dd($data)
        return datatables()
            ->of($data)
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
            ->addColumn('application_id', function ($data) {
                return $data->getBenidAttribute();
            })
            ->addColumn('ben_name', function ($data) {
                return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
            })
            ->addColumn('benf_name', function ($data) {
                return "Father Name";
            })
            ->addColumn('mobile_no', function ($data) {
                if (is_null($data->mobile_no) || $data->mobile_no == '') {
                    return '';
                } else {
                    return $data->mobile_no;
                }
            })
            ->addColumn('aadhar_no', function ($data) {
                if (is_null($data->aadhar_no) || trim($data->aadhar_no) == '') {
                    return '';
                } else {
                    return "**********" . substr($data->aadhar_no, -4);
                }
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
                $mask_bank_code = '';
                $bank_code = trim($data->bank_code);
                if ($bank_code != '') {
                    $mask_bank_code = '********' . substr($bank_code, 8, 4);
                } else {
                    $mask_bank_code = $bank_code;
                }
                return $mask_bank_code;
            })
            ->addColumn('village_town_city', function ($data) {
                return $data->village_town_city;
            })
            ->addColumn('status', function ($data) use ($scheme_id) {
                $val = '';
                if ($data->legacy_import && $data->next_level_role_id == 0 && is_null($data->next_level_role_id_edit)) {
                    $val .= 'Approved Legacy Data. Need to modify or add Aadhaar number and mobile number, and also upload supporting documents.';
                    $val .= "<br/>";
                }
                if ($data->no_aadhar == 1) {
                    $next_level_txt = ($data->next_level_role_id == 0) ? 'Approved' : 'Non Approved';
                    $val .= $next_level_txt . ' Aadhaar Number Blank or Aadhaar number is not 12 digits.';
                    $val .= "<br/>";
                }
                if ($data->no_mobile == 1) {
                    $next_level_txt = ($data->next_level_role_id == 0) ? 'Approved' : 'Non Approved';
                    $val .= $next_level_txt . ' Mobile Number Blank or Mobile number is not 10 digits.';
                    $val .= "<br/>";
                }
                return $val;
            })
            ->addColumn('action', function ($data) use ($scheme_id) {
                return '<a href="' . route('jb-pension', ['scheme_id' => encrypt($scheme_id), 'type' => 3, 'app_id' => $data->id]) . '" class="btn btn-primary ben_view_button" role="button">Edit</a>';

            })
            ->rawColumns(['ben_id', 'ben_name', 'ben_age', 'gender', 'bank_ifsc', 'bank_code', 'village_town_city', 'action', 'status'])
            ->make(true);
    }
}

