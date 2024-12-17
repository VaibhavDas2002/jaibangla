<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configduty;
use App\District;
use App\UrbanBody;
use App\Model\SubDistrict;
use App\PensionManabikWCDBSK;
//Dynamic Doc
use App\BenDocsManabikWCDBSK;
use App\BenDocsArcManabikWCD;
use App\DocumentType;
use App\SchemeDocMap;
//Dynamic Doc End
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\SchemeCapacity;
use App\Scheme;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\BankDetails;
use App\Helpers\Helper;
use App\DsPhase;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class BSKPensionFormController extends Controller
{
    public function __construct()
    {
        $this->scheme_id = 2;
        $this->base_dob_chk_date = '2020-01-01';
        $this->middleware(['auth','checkSession']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // print_r(Auth::user());
        // print Auth::user()->mobile_no;
        // die;
        $scheme_id = 2;
        $is_active = 0;
        try {
            // $roleArray = decrypt(base64_decode($request->get('tid')));
            $roleArray = $request->session()->get('bskrole');
        } catch (\Exception $e) {
            return redirect('api/bsk-entry-done')->with(['error' => 'Something went wrong!! Error Message : ' . $e->getMessage(), 'status' => 0]);
        }


        if (isset($roleArray) > 0) {
            $is_active = 1;
            $distCode = $roleArray['district_code'];
            if ($roleArray['is_rural'] == 'N') {
                $blockCode = $roleArray['sub_district_code'];
            } elseif ($roleArray['is_rural'] == 'Y') {
                $blockCode = $roleArray['block_code'];
            }
            $userMobileNo = $roleArray['mobile_no'];
            $ticketNo = $roleArray['ticketNo'];
            $user_id = $roleArray['user_id'];
        }

        if ($userMobileNo == '' || $ticketNo == '') {
            // Session::flash('error', 'User Disabled or session timeout. Back to protal and try once again.');
            return redirect('api/bsk-entry-done')->with(['error' => 'User Disabled or session timeout. Back to protal and try once again.']);
        }
        if ($is_active == 1) {
            $scheme_capacity_arr = array();
            $distCode = $request->session()->get('distCode');
            $scheme_capacity_arr = $this->getCapacityBrief($scheme_id, $distCode);
            if ($scheme_capacity_arr['visible'] == 1) {
                //dd($scheme_capacity_arr);
                if (($scheme_capacity_arr['approved'] + $scheme_capacity_arr['pending']) >= $scheme_capacity_arr['capacity']) {
                    $errorMsg = "Sum of approved(" . $scheme_capacity_arr['approved'] . ")  and pending(" . $scheme_capacity_arr['pending'] . ") applications  exceeds quota " . $scheme_capacity_arr['capacity'];
                    //dd($scheme_capacity_arr);
                    return redirect("/")->with('danger', $errorMsg);
                }
            }
            $districts = District::all();

            //Document Dynamic
            $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->first()->toArray();
            //dd($doc_id_list);
            if (!empty($doc_id_list['doc_list_man']))
                $doc_list_man = DocumentType::select('id', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->whereIn("id", json_decode($doc_id_list['doc_list_man']))->get()->toArray();
            else
                $doc_list_man = array();
            if (!empty($doc_id_list['doc_list_opt']))
                $doc_list_opt = DocumentType::select('id', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->whereIn("id", json_decode($doc_id_list['doc_list_opt']))->get()->toArray();
            else
                $doc_list_opt = array();
            if (!empty($doc_id_list['doc_list_man_group']))
                $doc_list_man_group = json_decode($doc_id_list['doc_list_man_group']);
            else
                $doc_list_man_group = array();


            $document_msg = "";
            if (!empty($doc_list_man_group)) {
                $doc_list = array_merge($doc_list_man, $doc_list_opt);
                $all_doc_id = array();
                foreach ($doc_list as $mDoc) {
                    array_push($all_doc_id, $mDoc['id']);
                }
                // dd($all_doc_id);
                if (count($doc_list)) {
                    foreach ($doc_list_man_group as $man_group) {
                        $document_msg .= '<div  class="form-group col-md-12" >';
                        $heading_msg = "At least one document must be uploaded in ";
                        $doucument_group_name = $this->getGroupName($man_group);
                        $heading_msg .= '<span style="color:red;font-weight:bold">' . $doucument_group_name . '</span>';
                        $document_msg .= "<p style='font-weight:bold;font-size:17px;'>" . $heading_msg . " </p>";
                        $document_msg .= "<ul>";
                        $results = DB::select("SELECT doc_name FROM m_attached_doc where id IN (" . implode(',', $all_doc_id) . ") and $man_group =any(doucument_group)");
                        $results = json_decode(json_encode($results), true);


                        //dd($results);
                        if (count($results) > 0) {
                            $i = 0;
                            foreach ($results as $requiredmsg) {

                                $document_msg .= "<li style='font-weight:bold;'>" . $requiredmsg['doc_name'] . "</li>";
                                $i++;
                            }
                        }


                        $document_msg .= "</ul>";
                        $document_msg .= "</div>";
                    }
                } else
                    $document_msg = "";
            } else
                $document_msg = "";



            $doc_profile_image = DocumentType::get()
                ->where("is_profile_pic", true)->first();

            $doc_profile_image_id = 999;
            if ($doc_profile_image) {
                $doc_profile_image_id = $doc_profile_image->id;
            }


            //echo "<pre>";print_r($doc_profile_image_id); echo "</pre>";die();  
            $scheme_row = Scheme::where('id', $scheme_id)->first();
            $scheme_name = $scheme_row->scheme_name;
            return view('BSKPensionForm/bskPensionForm', [
                'districts' => $districts,
                'scheme_id' => $scheme_id,
                'scheme_name' => $scheme_name,
                'document_msg' => $document_msg,
                'doc_list_man' => $doc_list_man,
                'doc_list_opt' => $doc_list_opt,
                'profile_img' => $doc_profile_image_id,
                'scheme_capacity_arr' => $scheme_capacity_arr,
                'userMobileNo' => $userMobileNo,
                'ticketNo' => $ticketNo
            ]);
        }
        if ($is_active == 0) {
            // Session::flash('error', 'User Disabled or session timeout. Back to protal and try once again.');
            return redirect('api/bsk-entry-done')->with(['error' => 'User Disabled or session timeout. Back to protal and try once again..']);
        } else {
            // Session::flash('error', 'User Disabled or session timeout. Back to protal and try once again.');
            return redirect('api/bsk-entry-done')->with(['error' => 'User Disabled or session timeout. Back to protal and try once again...']);
        }
    }

    public function store(Request $request)
    {
        $ds_phase = DsPhase::where('is_current', TRUE)->first();

        $base_url = url('/');
        $uploaded_doc = array();
        $destinationPath = storage_path('app/keep_wcd/');
        $scheme_id = $request->scheme_id;
        $roleArray = $request->session()->get('bskrole');
        $is_active = 0;
        if (isset($roleArray) > 0) {
            $is_active = 1;
            $distCode = $roleArray['district_code'];
            if ($roleArray['is_rural'] == 'N') {
                $blockCode = $roleArray['sub_district_code'];
                $level = 'Subdiv';
            } elseif ($roleArray['is_rural'] == 'Y') {
                $blockCode = $roleArray['block_code'];
                $level = 'Block';
            }
            $userMobileNo = $roleArray['mobile_no'];
            $ticketNo = $roleArray['ticketNo'];
            $user_id = $roleArray['user_id'];
        }
        if ($is_active == 0) {
            // return redirect("/")->with('danger', 'User Disabled');
            // Session::flash('error', 'User Disabled or session timeout. Back to protal and try once again.');
            return redirect('api/bsk-entry-done')->with(['error' => 'User Disabled or session timeout. Back to protal and try once again.']);
        }
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->get();
        $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
        $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
        $doc_list = array_merge($doc_list_man, $doc_list_opt);
        //dd('ok');
        $isValidarr = $this->validateInput($request,  $scheme_id, 1);
        if ($isValidarr['is_valid'] == false) {
            //return back()->withErrors($isValidarr['errors'])->withInput();
            return back()->with('errors', $isValidarr['errors'])->withInput($request->input());
        }
        $doc_list_man_group_upload = array();
        $doc_list_man_group_db = array();
        // print_r( $doc_id_list[0]['doc_list_man_group']);die;
        if (($doc_id_list[0]['doc_list_man_group']) != '' &&  ($doc_id_list[0]['doc_list_man_group'] != 'null') && ($doc_id_list[0]['doc_list_man_group']) != null) {

            $doc_list_man_group_db = json_decode($doc_id_list[0]['doc_list_man_group']);
        }
        foreach ($doc_list as $doc) {
            if ($request->hasFile('doc_' . $doc)) {
                $doucument_group_id = DocumentType::select('doucument_group')->where('id', $doc)->first();
                if ($doucument_group_id['doucument_group'] != '') {
                    $arr = array();
                    $postgresStr = trim($doucument_group_id['doucument_group'], "{}");
                    $elmts = explode(",", $postgresStr);
                    foreach ($elmts as $myarr) {
                        if (!in_array($myarr, $doc_list_man_group_upload)) {
                            array_push($doc_list_man_group_upload, $myarr);
                        }
                    }
                }
            }
        }
        if (count($doc_list_man_group_db) > 0) {
            $errors = array();
            $i = 0;
            foreach ($doc_list_man_group_db as $group) {
                $doucument_group_name = $this->getGroupName($group);
                if (!in_array($group, $doc_list_man_group_upload)) {
                    $errorMsg = "At least one document must be uploaded for " . $doucument_group_name;
                    array_push($errors, $errorMsg);
                }
            }
            if (count($errors) > 0)
                //return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput($request->input());
        }

        if (!empty($request->aadhar_no)) {
            if ($this->isAadharValid(trim($request->aadhar_no)) == false) {
                $errors = array();
                $errorMsg = "Aadhaar Number Invalid";
                array_push($errors, $errorMsg);
                //return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput($request->input());
            }
        }
        $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
        $count = PensionManabikWCDBSK::where('aadhar_no', trim($request->aadhar_no))->whereRaw("(" . $check_condition_str . ")")->count('id');
        if ($count > 0) {
            $request->session()->put('dupAadhaarCheck', trim($request->aadhar_no));
            $errors = array();
            $errorMsg = "Aadhaar Number Already Exist! Please try different.";
            array_push($errors, $errorMsg);
            // return back()->withErrors($errors)->withInput();
            return back()->with('errors', $errors)->with('dupAadhaar', 1)->withInput($request->input());
        }


        //--------- Duplicate bank A/C check---------- //
        $bankCount = PensionManabikWCDBSK::whereRaw("trim(bank_code)=trim(" . "'" . $request->bank_account_number . "'" . ")")->whereRaw("trim(bank_ifsc)=trim(" . "'" . $request->bank_ifsc_code . "'" . ")")
            ->whereRaw("(" . $check_condition_str . ")")
            ->count('id');

        if ($bankCount > 0) {
            $errors = array();
            $errorMsg = "Bank A/C Already Exist!";
            array_push($errors, $errorMsg);
            //return back()->withErrors($errors)->withInput();
            return back()->with('errors', $errors)->withInput($request->input());
        }

        $ifsc = trim($request->bank_ifsc_code);
        $bank_branch = trim($request->bank_branch);
        $name_of_bank = trim($request->name_of_bank);
        $row_count_bank = BankDetails::whereraw("trim(branch)='$bank_branch'")->whereraw("trim(ifsc)='$ifsc'")->whereraw("trim(bank)='$name_of_bank'")->count();
        if ($row_count_bank == 0) {
            $errors = array();
            $errorMsg = "Bank IFSC and Bank Name Not Match!";
            array_push($errors, $errorMsg);
            //return back()->withErrors($errors)->withInput();
            return back()->with('errors', $errors)->withInput($request->input());
        }
        //------------End -------------------------//



        $pension_details = new PensionManabikWCDBSK();

        $upload_file=array();
        $i=0;
        $doc_master=DocumentType::get();
        //Document Dynamic
        foreach ($doc_list as $doc) {
            if ($request->hasFile('doc_' . $doc)) {
                $doc_file = $request->file('doc_' . $doc);
                // $file_passport = $doc_file->getClientOriginalName();
                // $file_type = $doc_file->getClientOriginalExtension();
                // $file_profile = "doc_" . $doc . "_" . rand(10000, 99999) . '_' . time() . '.' . $doc_file->getClientOriginalExtension();
                // //$destinationPath = storage_path('app/keep_WCD/');
                // $fileStore[] = $doc_file->move($destinationPath, $file_profile);
                // //array_push($uploaded_doc,$file_profile);
                // $uploaded_doc[$doc] = $file_profile;
                $img_data = file_get_contents($doc_file);
                $u_extension = $doc_file->getClientOriginalExtension();
                $mime_type = $doc_file->getMimeType();
                $ip_address = request()->ip();
                $c_datetime = date('Y-m-d H:i:s', time());
                $doc_type_name =$doc_master->where('id', $doc)->first() ;
                if(strtolower($mime_type)=='image/jpeg'){
                    $extension='jpeg';
                }else if(strtolower($mime_type)=='image/jpeg'){
                    $extension='jpg';
                }else if(strtolower($mime_type)=='image/png'){
                    $extension='png';
                }else if(strtolower($mime_type)=='image/gif'){
                    $extension='gif';
                }else if(strtolower($mime_type)=='application/pdf'){
                    $extension='pdf';
                }
                else{
                    echo $u_extension;die;
                    $errors = array();
                    $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
                    array_push($errors, $errorMsg);
                    return back()->with('errors', $errors)->withInput(Input::all());  
                }
                if($u_extension!=$extension){
                    echo $u_extension;die;
                    $errors = array();
                    $errorMsg = "You are trying to upload an incorrect file for ".$doc_type_name->doc_name;
                    array_push($errors, $errorMsg);
                    return back()->with('errors', $errors)->withInput(Input::all());  
                }
                $doc_type_name =$doc_master->where('id', $doc)->first() ;
                $base64 = base64_encode($img_data);
                $upload_file[$i]['created_by_dist_code']=$distCode;
                $upload_file[$i]['created_by_local_body_code']=$blockCode;
                $upload_file[$i]['document_type']=$doc;
                $upload_file[$i]['scheme_id']=$scheme_id;
                $upload_file[$i]['created_by_level']=$level;
                $upload_file[$i]['created_at']=$c_datetime;
                $upload_file[$i]['created_by']=$user_id;
                $upload_file[$i]['ip_address']=$ip_address;
                $upload_file[$i]['attched_document']=$base64;
                $upload_file[$i]['document_mime_type']=$mime_type;
                $upload_file[$i]['document_extension']=$extension;
                if(!empty($doc_type_name)){
                $upload_file[$i]['doc_type_name'] = $doc_type_name->doc_name;
                }
                $i++;
            } else {
                $img_data = null;
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

        $assembly_name = "";
        if ($request->asmb_cons != "") {
            $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
            $assembly_name = $assembly->ac_name;
        }

        if ($request->receive_pension != "") {
            $receive_pension = implode(',', $request->receive_pension);
            $pension_details->receive_pension    = $receive_pension;
        }

        if ($request->social_security_pension != "") {
            $social_security_pension = implode(',', $request->social_security_pension);
            $pension_details->social_security_pension   = $social_security_pension;
        }

        $pension_details->entry_type = $request->entry_type;
        if ($request->entry_type == 'Form through Duare Sarkar camp') {
            $pension_details->ds_registration_no = trim($request->ds_registration_no);
            $pension_details->ds_date = trim($request->ds_date);
            if (!empty($ds_phase)) {
                $pension_details->ds_phase   = $ds_phase->phase_code;
            }
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
        $pension_details->marital_status = $request->marital_status;
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

        $pension_details->nominate_name    = $request->nominate_name;
        $pension_details->nominate_address    = $request->nominate_address;
        $pension_details->nominate_relationship   = $request->nominate_relationship;

        $pension_details->created_by = $user_id;
        $pension_details->created_by_dist_code = $distCode;
        $pension_details->created_by_local_body_code = $blockCode;
        $pension_details->next_level_role_id =  NULL;
        $pension_details->block_ulb_type = NULL;

        $pension_details->created_by_level = $level;
        $pension_details->scheme_id =  $request->scheme_id;
        $pension_details->type_disability =  $request->disablity_type;
        $pension_details->percentage_disability =  $request->disablity_type_percentage;
        $pension_details->certifying_auth =  $request->disablity_type_authority;
        $pension_details->disability_designation =  $request->disability_designation;
        $pension_details->av_status =  $request->av_status;
        $pension_details->receiving_pension_other_source_1 =  $request->receiving_pension_other_source_1;
        $pension_details->receiving_pension_other_source_2 =  $request->receiving_pension_other_source_2;

        $pension_details->is_bsk =  TRUE;
        $pension_details->bsk_ticket_no = $ticketNo;
        $pension_details->bsk_user_id = $user_id;
        $pension_details->bsk_operator_mobile_no = $userMobileNo;

        DB::connection('pgsql4')->beginTransaction();
        DB::connection('pgsql_encwrite')->beginTransaction();
        $is_saved = 0;
        try {
            // $query = "select nextval('pension.beneficiary_id_seq') as nxt";
            // $next_rid = DB::connection('pgsql5')->select($query);
            // $next_squence_id = $next_rid[0]->nxt;
            // $pension_details->id = $next_squence_id;

            $is_saved = $pension_details->save();  // uncomment in production

            $id = $pension_details->id;

            // $i = 0;
            if ($id) {
                foreach ($upload_file as $doc_type => $doc) {
                    $upload_file[$doc_type]['beneficiary_id'] = $id;
                }
                $doc_inserted = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($upload_file);
                if ($doc_inserted) {
                    DB::connection('pgsql4')->commit();
                    DB::connection('pgsql_encwrite')->commit();
                } else {
                    $error_found = 1;
                }
            } else {
                $error_found = 1;
            }
            if ($error_found) {
                DB::connection('pgsql4')->rollback();
                DB::connection('pgsql_encwrite')->rollback();
            }
            
        } catch (\Exception $e) {
            DB::connection('pgsql4')->rollback();
            DB::connection('pgsql_encwrite')->rollback();
            // dd($e);
        }
        // DB::commit();

        //print_r($is_saved);
        $str_caste = strtolower($request->caste_category);
        $id = $pension_details->id;
        $mobile_no = $pension_details->mobile_no;
        $ben_name = $pension_details->ben_fname . ' ' . $pension_details->ben_mname . ' ' . $pension_details->ben_lname;
        $father_name = $pension_details->father_fname . ' ' . $pension_details->father_mname . ' ' . $pension_details->father_lname;
        $address = 'Block/Municipality- ' . $pension_details->block_ulb_name . ', GP/Ward- ' . $pension_details->gp_ward_name . ', P.O- ' . $pension_details->post_office . ', P.S- ' . $pension_details->police_station . ' PIN- ' . $pension_details->pincode;
        $created_date = date("d-m-Y", strtotime($pension_details->created_at));

        // Logout Session After Form Submission
        Auth::logout();
        $request->session()->flush();
        $request->session()->save();
        $request->session()->regenerate(true);

        if ($is_saved) {
            // Session::flash('app_id',  $id);
            // Session::flash('ticketNo', $ticketNo);
            // Session::flash('success', 'Application Submitted Successfully');
            return redirect('api/bsk-entry-done')
                ->with([
                    'application_id' => $id,
                    'ticketNo' => $ticketNo,
                    'mobile_no' => $mobile_no,
                    'address' => $address,
                    'ben_name' => $ben_name,
                    'father_name' => $father_name,
                    'date' => $created_date,
                    'success' => 'Application Submitted Successfully'
                ]);
        } else {
            // Session::flash('app_id',  $id);
            // Session::flash('error', 'Some error.Please try again');
            return redirect('api/bsk-entry-done')
                ->with([
                    'application_id' => $id,
                    'error' => 'Some error.Please try again'
                ]);
        }
    }

    private function validateInput($request, $scheme_id, $add_edit_code)
    {
        $today = date("Y-m-d");
        $entry_type_arr = array('Normal Form', 'Form through Duare Sarkar camp');
        if ($add_edit_code == 1) {
            $entry_type_r = "required";
        } else {
            $entry_type_r = "nullable";
        }
        $rules = [
            'entry_type' => $entry_type_r . '|in:' . implode(",", $entry_type_arr),
            'ds_registration_no' => 'required_if:entry_type,Form through Duare Sarkar camp|max:20',
            'ds_date' => 'required_if:entry_type,Form through Duare Sarkar camp|nullable|date|before_or_equal:' . $today,
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
        $attributes['disablity_type'] = 'Type of Disability';
        $attributes['disablity_type_percentage'] = 'Percentage of Disablity';
        $attributes['disablity_type_authority'] = 'Authority Name';
        $attributes['disability_designation'] = 'Authority Designation';
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

    function getCapacityBrief($scheme_id, $district)
    {
        $return_arr = array();
        $capacity = SchemeCapacity::select('capacity')->where('scheme_id', $scheme_id)->where('district_code', $district)->first();
        if (!empty($capacity->capacity)) {
            $return_arr['visible'] = 1;
            $return_arr['capacity'] = $capacity->capacity;
            $scheme = Scheme::select('id', 'scheme_name', 'short_code')->where('is_active', 1)->where('id', $scheme_id)->first();
            $scheme_schema_name = $scheme->short_code;
            if ($district == 0) {
                $total_data = DB::table($scheme_schema_name . '.beneficiary')
                    ->selectRaw('sum(case when next_level_role_id=0 then 1 else 0 end) approved,
             sum(case when next_level_role_id>0  or next_level_role_id IS NULL then 1 else 0 end) pending')
                    ->where('is_state', TRUE)
                    ->first();
            } else {
                $total_data = DB::table($scheme_schema_name . '.beneficiary')
                    ->selectRaw('sum(case when next_level_role_id=0 then 1 else 0 end) approved,
                 sum(case when next_level_role_id>0  or next_level_role_id IS NULL then 1 else 0 end) pending')
                    ->where('created_by_dist_code', $district)->where('is_state', FALSE)
                    ->first();
            }
            $return_arr['approved'] = intval($total_data->approved);
            $return_arr['pending'] = intval($total_data->pending);
        } else {
            $return_arr['visible'] = 0;
        }
        return $return_arr;
    }

    public function getGroupName($groupId)
    {
        $groupArr = Config::get('constants.document_group');
        $groupDescription = "NA";
        foreach ($groupArr as $key => $value) {
            if ($key == $groupId) {
                $groupDescription = $value;
                break;
            }
        }
        return $groupDescription;
    }
    function ajaxgetage(Request $request)
    {
        $diff = 0;
        if ($request->dob != '') {
            $diff = Carbon::parse($request->dob)->diffInYears($this->base_dob_chk_date);
        }
        return $diff;
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
    public function getBankDetails(Request $request)
    {
        $ifsc = $request->ifsc;
        $bank_details = BankDetails::where('ifsc', $ifsc)->get(['bank', 'branch'])->first();

        return json_encode($bank_details);
    }
}
