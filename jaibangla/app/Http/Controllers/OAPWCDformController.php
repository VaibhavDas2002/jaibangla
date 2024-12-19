<?php

namespace App\Http\Controllers;

use App\Http\Controllers\WPWCDformController;
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
use App\PensionOAPWCD;
//Dynamic Doc
use App\BenDocsOAPWCD;
use App\BenDocsArcOAPWCD;
use App\DocumentType;
use App\SchemeDocMap;
//Dynamic Doc End
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
use App\BenEntry;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Config;
use App\SchemeCapacity;
use App\Scheme;
use Validator;
use Carbon\Carbon;
use App\BankDetails;
use App\Helpers\Helper;
use App\DsPhase;
use App\BlkUrbanlEntryMapping;
use App\OAPMobileUnique;
use App\OAPAdhaarUnique;
use App\OAPBankUnique;
use App\AcceptRejectInfo;
use App\BeneficiaryDupBlank;
use App\BenDocs;
use App\Traits\TraitCasteCertificateValidate;
use App\Traits\TraitLifeCertificateValidate;
use App\Traits\TraitAadharValidate;
use Illuminate\Support\Facades\Session;
use App\Helpers\AuthChecker;

class OAPWCDformController extends Controller
{
    use TraitCasteCertificateValidate;
    use TraitLifeCertificateValidate;
    use TraitAadharValidate;

    public function __construct()
    {
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
        $this->scheme_id = 10;
        $this->base_dob_chk_date = date('Y-m-d');
        $this->max_dob =date('Y-m-d', strtotime('+60 years'));
        //dd($this->max_dob);
        $this->state_login_next_level_role_id_arr = Config::get('constants.state_login_next_level_role_id');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return redirect("/")->with('error', 'Data entry temporary suspended.');
        // $is_active = 0;
        $scheme_id = 10;
        // $is_active = 0;

        // $base_url=url('/');
        // echo $base_url.'/images/';exit;        

        $roleArray = $request->session()->get('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $request->session()->put('level', $roleObj['mapping_level']);
                $request->session()->put('distCode', $roleObj['district_code']);
                $district_code = $roleObj['district_code'];
                $is_state_login = $roleObj['is_state_login'];
                if ($roleObj['is_urban'] == 1) {
                    $block_ulb_code = $roleObj['urban_body_code'];
                    $request->session()->put('blockCode', $roleObj['urban_body_code']);
                } else {
                    $block_ulb_code = $roleObj['taluka_code'];
                    $request->session()->put('blockCode', $roleObj['taluka_code']);
                }
                break;
            }
        }
        if ($is_active == 0) {
            return redirect("/")->with('danger', 'User Disabled');
        }
        if(isset($request->wq)){
            $wq=$request->wq;
        }
        else{
            $wq=0;  
        }
        if(isset($request->cmo_id)){
            $cmo_id=$request->cmo_id;
        }
        else{
            $cmo_id=0;  
        }
        $name_editable=1;
        $mobile_editable=1;
        $district_editable=1;
        $blok_munc_editable=1;
        $row_cmo=collect([]);
        $cmo_f_name = '';
        $cmo_m_name = '';
        $cmo_l_name = '';
        if($wq==0){
                $allowded_arr = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('district_code',  $district_code)->where('block_ulb_code',  $block_ulb_code)->first();
                $main_entry_allowded = intval($allowded_arr->main_entry);
                $entry_cap = intval($allowded_arr->entry_cap);
                if ($main_entry_allowded == 0) {
                    return redirect("/")->with('danger', 'Normal Data entry temporarily suspended.');
                }
                
        }
        if($cmo_id>0){
                    $check_permission_row_count = DB::table('pension.cmo_mark_block_subdiv_permission')
                ->where('scheme_id', $scheme_id)->where('block_subdiv_code', $block_ulb_code)->where('can_entry',true)->count();
                    if (empty($check_permission_row_count) || $check_permission_row_count==0) {
                    return redirect("/")->with('danger', 'Not Allowed');
                    }
            $row_cmo = DB::table('pension.cmo_sm_data')->where('created_by_dist_code', $district_code)->where('id', $cmo_id)
            ->where('scheme_id',$scheme_id)->where('created_by_local_body_code', $block_ulb_code)->whereNull('jb_beneficiary_id')->where('next_level_role_id',2)->first();
            if (empty($row_cmo)) {
                return redirect("/")->with('danger', 'Cmo Id Not Found');
            } 
            $cmo_f_name = '';
            $cmo_m_name = '';
            $cmo_l_name = '';
            $full_name = trim($row_cmo->ben_name);
            $name_explode = explode(' ', trim($row_cmo->ben_name));
            if (count($name_explode) == 1) {
                            $cmo_f_name = $name_explode[0];
                            $cmo_m_name = '';
                            $cmo_l_name = '';
            } else if (count($name_explode) == 2) {
                            $cmo_f_name = $name_explode[0];
                            $cmo_m_name = '';
                            $cmo_l_name = $name_explode[1];
             } else if (count($name_explode) == 3) {
                            $cmo_f_name = $name_explode[0];
                            $cmo_m_name = $name_explode[1];
                            $cmo_l_name = $name_explode[2];
            } 
            else {
                            $cmo_f_name = trim($row_cmo->ben_name);
                            $cmo_m_name = '';
                            $cmo_l_name = '';
            }
            $name_editable=0;
            $mobile_editable=0;
            $district_editable=0;
            $blok_munc_editable=0;    
       }
        if ($is_active == 1) {
            $distCode = $request->session()->get('distCode');
            
            if($wq==0){
                    $scheme_capacity_arr = $this->getCapacityBrief($scheme_id, $district_code);
                    if ($scheme_capacity_arr['visible'] == 1) {
                        if (($scheme_capacity_arr['approved'] + $scheme_capacity_arr['pending']) >= $scheme_capacity_arr['capacity']) {
                            $errorMsg = "Sum of approved(" . $scheme_capacity_arr['approved'] . ")  and pending(" . $scheme_capacity_arr['pending'] . ") applications  exceeds the quota " . $scheme_capacity_arr['capacity'];
                            //dd($errorMsg);
                            return redirect("/")->with('danger', $errorMsg);
                            // return redirect()->back()->withErrors("Total no of applications ".$scheme_capacity_arr['total_data']." exceeds quota ".$scheme_capacity_arr['capacity']);
                        }
                    }
            }
            if($wq==1){
                $scheme_capacity_arr =Helper::getCapacityWtQuota($scheme_id, $distCode,$block_ulb_code);
                if ($scheme_capacity_arr['visible'] == 1) {
                    if (($scheme_capacity_arr['approved'] + $scheme_capacity_arr['pending']) >= $scheme_capacity_arr['capacity']) {
                        $errorMsg = "Sum of approved(" . $scheme_capacity_arr['approved'] . ")  and pending(" . $scheme_capacity_arr['pending'] . ") applications  exceeds the quota " . $scheme_capacity_arr['capacity'];
                        //dd($errorMsg);
                        return redirect("/")->with('danger', $errorMsg);
                        // return redirect()->back()->withErrors("Total no of applications ".$scheme_capacity_arr['total_data']." exceeds quota ".$scheme_capacity_arr['capacity']);
                    }
                }
            }

            $districts = District::all();

            //Document Dynamic
            $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->first()->toArray();
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
                        $heading_msg = "At least one document must be uploaded for ";
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
            $scheme_row = Scheme::where('id', $scheme_id)->first();
            $scheme_name = $scheme_row->scheme_name;
            $allow_ds_entry = intval($scheme_row->allow_ds_entry);
            $allow_normal_entry = intval($scheme_row->allow_normal_entry);
            if($allow_ds_entry==0 &&  $allow_normal_entry==0){
                return redirect("/")->with('danger', ' Data entry temporarily suspended.');  
            }

            if($wq==1){
                $district_arr=District::select('district_code','district_name')->where('district_code',$district_code)->first();
                $assembly_arr=Assembly::select('ac_no','ac_name')->where('district_code',$district_code)->get();
                $rural_urban_arr=Config::get('constants.rural_urban');
                $my_rular_urban_arr=array();
                foreach($rural_urban_arr as $key=>$val){
                    if($key==$roleObj['is_urban']){
                        $my_rular_urban_arr['key']=$key;
                        $my_rular_urban_arr['val']=$val;
                    }
    
                }
               // dd($my_rular_urban_arr);
                if($roleObj['is_urban']==1){
                    $blok_munc_arr=UrbanBody::select('urban_body_code as code','urban_body_name as name')->where('sub_district_code',$request->session()->get('blockCode'))->where('urban_body_code',273007)->first();
                    $gp_ward_arr=Ward::select('urban_body_ward_code as code','urban_body_ward_name as name')->where('urban_body_code',273007)->get();
    
                }
                else{
                    $blok_munc_arr=Taluka::select('block_code as code','block_name as name')->where('block_code',$request->session()->get('blockCode'))->first();
                    $gp_ward_arr=GP::select('gram_panchyat_code as code','gram_panchyat_name as name')->where('block_code',$request->session()->get('blockCode'))->get();
    
                }
            }
            else{
                $district_arr=collect();
                $assembly_arr=collect();
                $my_rular_urban_arr=array();
                $blok_munc_arr=collect();
                $gp_ward_arr=collect();   
            }
            $cur_ds_phase_arr = DsPhase::where('is_current', TRUE)->first();
            $ds_phase_text='';
            if($cur_ds_phase_arr->is_samadhan==TRUE){
                $ds_phase_text='Samasyaa Samadhan Jan Sanjog';
            }
            else{
                $ds_phase_text='Duare Sarkar';
            }
            return view('OAPWCD/pension_details', [
                'districts' => $districts,
                'scheme_id' => $scheme_id,
                'scheme_name' => $scheme_name,
                'document_msg' => $document_msg,
                'doc_list_man' => $doc_list_man,
                'doc_list_opt' => $doc_list_opt,
                'profile_img' => $doc_profile_image_id,
                'is_state_login' => $is_state_login,
                'wq' => $wq,
                'district_code' => $district_code,
                'blok_munc_arr' => $blok_munc_arr,
                'my_rular_urban_arr' => $my_rular_urban_arr,
                'assembly_arr' => $assembly_arr,
                'district_arr' => $district_arr,
                'gp_ward_arr' => $gp_ward_arr,
                'districts' => $districts,
                'allow_ds_entry' => $allow_ds_entry,
                'allow_normal_entry' => $allow_normal_entry,
                'cur_ds_phase' => $cur_ds_phase_arr->phase_code,
                'ds_phase_text' => $ds_phase_text,
                'cmo_id' => $cmo_id,
                'row_cmo' => $row_cmo,
                'name_editable' => $name_editable,
                'district_editable' => $district_editable,
                'blok_munc_editable' => $blok_munc_editable,
                'cmo_f_name' => $cmo_f_name,
                'cmo_m_name' => $cmo_m_name,
                'cmo_l_name' => $cmo_l_name,
                'scheme_capacity_arr' => $scheme_capacity_arr 
                
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
        
        // return redirect("/")->with('error', 'Data entry temporary suspended.');
        try{
        $ds_phase = DsPhase::where('is_current', TRUE)->first();
        $user_id = AuthChecker::getUserId();
        //$server_ip =$_SERVER['SERVER_ADDR'];
        $base_url = url('/');
        $uploaded_doc = array();
        $destinationPath = storage_path('app/keep_oap/');
        $scheme_id = $request->scheme_id;
        $roleArray = $request->session()->get('role');
        $is_active = 0;
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $level = $roleObj['mapping_level'];
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
        if ($is_active == 0) {
            return redirect("/")->with('danger', 'User Disabled');
        }
        if ($is_state_login != $request->is_state_login) {
            return redirect("/")->with('danger', 'User Disabled');
        }
        if ($is_state_login == 0) {
            if (empty($level) || empty($distCode)) {
                return redirect("/")->with('danger', 'User Disabled');
            }
        }
        if(isset($request->wq)){
            $wq=$request->wq;
        }
        else{
            $wq=0;  
        }
        if($wq==0){
                $allowded_arr = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('block_ulb_code',  $blockCode)->where('district_code',  $distCode)->first();
                $main_entry_allowded = intval($allowded_arr->main_entry);
                $entry_cap = intval($allowded_arr->entry_cap);
                if ($main_entry_allowded == 0) {
                    return redirect("/")->with('danger', 'Data entry temporarily suspended.');
                }
               
        }
        if(isset($request->cmo_id)){
            $cmo_id=$request->cmo_id;
        }
        else{
            $cmo_id=0;  
        }
        if($cmo_id>0){
                    $check_permission_row_count = DB::table('pension.cmo_mark_block_subdiv_permission')
                ->where('scheme_id', $scheme_id)->where('block_subdiv_code', $blockCode)->where('can_entry',true)->count();
                    if (empty($check_permission_row_count) || $check_permission_row_count==0) {
                    return redirect("/")->with('danger', 'Not Allowed');
                    }
            $row_cmo = DB::table('pension.cmo_sm_data')->where('created_by_dist_code', $distCode)->where('id', $cmo_id)
            ->where('scheme_id',$scheme_id)->where('created_by_local_body_code', $blockCode)->whereNull('jb_beneficiary_id')->where('next_level_role_id',2)->first();
            if (empty($row_cmo)) {
                return redirect("/")->with('danger', 'Cmo Id Not Found');
            } 
       }
        // $scheme_capacity_arr = array();
       /* if($wq==0){
                    $scheme_capacity_arr = $this->getCapacityBrief($scheme_id, $distCode);
                    if ($scheme_capacity_arr['visible'] == 1) {
                        if (($scheme_capacity_arr['approved'] + $scheme_capacity_arr['pending']) >= $scheme_capacity_arr['capacity']) {
                            $errorMsg = "Sum of approved(" . $scheme_capacity_arr['approved'] . ")  and pending(" . $scheme_capacity_arr['pending'] . ") applications  exceeds quota " . $scheme_capacity_arr['capacity'];
                            return redirect("/")->with('danger', $errorMsg);
                            // return redirect()->back()->withErrors("Total no of applications ".$scheme_capacity_arr['total_data']." exceeds quota ".$scheme_capacity_arr['capacity']);
                        }
                        
                    }
        }
        if($wq==1){
            $scheme_capacity_arr =Helper::getCapacityWtQuota($scheme_id, $distCode,$blockCode);
            if ($scheme_capacity_arr['visible'] == 1) {
                if (($scheme_capacity_arr['approved'] + $scheme_capacity_arr['pending']) >= $scheme_capacity_arr['capacity']) {
                    $errorMsg = "Sum of approved(" . $scheme_capacity_arr['approved'] . ")  and pending(" . $scheme_capacity_arr['pending'] . ") applications  exceeds the quota " . $scheme_capacity_arr['capacity'];
                    //dd($errorMsg);
                    return redirect("/")->with('danger', $errorMsg);
                    // return redirect()->back()->withErrors("Total no of applications ".$scheme_capacity_arr['total_data']." exceeds quota ".$scheme_capacity_arr['capacity']);
                }
            }
        }
        */
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->get();
        $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
        $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
        $doc_list = array_merge($doc_list_man, $doc_list_opt);

        $isValidarr = $this->validateInput($request,  $scheme_id, 0,$cmo_id, 1);
        if ($isValidarr['is_valid'] == false) {
            return back()->with('errors', $isValidarr['errors'])->withInput(Input::all());
        }
        if (!empty($request->aadhar_no)) {
            if ($this->isAadharValid(trim($request->aadhar_no)) == false) {
                $errors = array();
                $errorMsg = "Aadhaar Number Invalid";
                array_push($errors, $errorMsg);
                //return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput(Input::all());
            }
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
                // return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput(Input::all());
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
        if(empty($cmo_id)){
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
        }
        $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
        $ifsc = trim($request->bank_ifsc_code);
        $bank_branch = trim($request->bank_branch);
        $name_of_bank = trim($request->name_of_bank);
        $row_count_bank = BankDetails::whereraw("trim(branch)='$bank_branch'")->whereraw("trim(ifsc)='$ifsc'")->where('is_active',1)->whereraw("trim(bank)='$name_of_bank'")->count();
       // $bank_details = BankDetails::whereraw("trim(ifsc)='$ifsc'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
        $bank_details = BankDetails::where('ifsc', trim($ifsc))->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
        $new_bank_code=$bank_details->bank_code;
        if ($row_count_bank == 0) {
            $errors = array();
            $errorMsg = "Bank IFSC and Bank Name Not Match!";
            array_push($errors, $errorMsg);
            //return back()->withErrors($errors)->withInput();
            return back()->with('errors', $errors)->withInput(Input::all());
        }
        if ($is_state_login) {
            $district_check = District::where('district_code', '=', trim($request->district))->count();
            if ($district_check == 0) {
                $errors = array();
                $errorMsg = "Invalid District";
                array_push($errors, $errorMsg);
                //return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput(Input::all());
            }
            if ($request->urban_code == 1) {
                $check_local_body = UrbanBody::where('district_code', trim($request->district))->where('urban_body_code', '=', trim($request->block))->first();
            } else {
                $check_local_body = Taluka::where('district_code', trim($request->district))->where('block_code', '=', trim($request->block))->first();
            }
            if (empty($check_local_body)) {
                $errors = array();
                $errorMsg = "Invalid Block/Municipality";
                array_push($errors, $errorMsg);
                //return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput(Input::all());
            }
        }
        $ifsc = trim($request->bank_ifsc_code);
        $bank_account_number = trim($request->bank_account_number);
        $bank_branch = trim($request->bank_branch);
        $name_of_bank = trim($request->name_of_bank);
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (!empty($scheme_obj->short_code)) {
            $schema = $scheme_obj->short_code;
          } else {
            $schema = "pension"; 
          }
          $errormsg=array();
          $dup_bank=0; 
          $dup_aadhar=0; 
          $dup_mobile=0; 

          if(!empty($bank_account_number) && !empty($ifsc)){
           $bank_count = DB::table($schema . '.beneficiaries')->where('scheme_id',$scheme_id)->whereIn('is_clean', [1, 2])->where('bank_code',$bank_account_number)->count('bank_code');
           if($bank_count>0){ 
            $dup_bank=1;
            $is_error=1;  
            $request->session()->put('dupBankCheck', trim($bank_account_number));
            array_push($errormsg,'Bank A/C Already Exist!');
            return redirect("oap-wcd")->withInput(Input::all())->with('errors',$errormsg)->with('dupBank', $dup_bank);
 

           } 
        }          
        if(!empty($request->aadhar_no)){
            $aadhar_count = DB::table($schema . '.beneficiaries')->where('scheme_id',$scheme_id)->whereIn('is_clean', [1, 2])->where('aadhar_no',trim($request->aadhar_no))->count('aadhar_no');
            if($aadhar_count>0){ 
                $dup_aadhar=1;
                $is_error=1; 
                $request->session()->put('dupAadhaarCheck', trim($request->aadhar_no)); 
                array_push($errormsg,'Aadhaar Number Already Exist! Please try different.');  
                return redirect("oap-wcd")->withInput(Input::all())->with('errors',$errormsg)->with('dup_aadhar', $dup_aadhar);
   
            } 
         } 
         if(!empty($request->mobile_no)){
            $mobile_count = DB::table($schema . '.beneficiaries')->where('scheme_id',$scheme_id)->whereIn('is_clean', [1, 2])->where('mobile_no',$request->mobile_no)->count('mobile_no');
            if($mobile_count>0){ 
            $dup_mobile=1;
            $is_error=1;  
            $request->session()->put('dupMobileCheck', trim($request->mobile_no));
            array_push($errormsg,'Mobile Number Already Exist! Please try different.');    
            return redirect("oap-wcd")->withInput(Input::all())->with('errors',$errormsg)->with('dup_mobile',$dup_mobile);
          
            } 
        }


        // if(!empty($bank_account_number)){
        //     $DupCheckBankWP = DupCheck::getDupCheckBank(11,$bank_account_number);
        //     if(!empty($DupCheckBankWP)){  
        //         $is_error=1;  
        //         $errorMsg = "Duplicate Bank Account Number present in Widow Pension Scheme with Beneficiary ID- $DupCheckBankWP";
        //         array_push($errormsg,$errorMsg);    
        //         return redirect("oap-wcd")->withInput(Input::all())->with('errors',$errormsg)->with('dupBank', $dup_bank); 
        //     } 
        // } 
        // if(!empty($bank_account_number)){
        //     $DupCheckBanLB = DupCheck::getDupCheckBank(20,$bank_account_number);
        //     if(!empty($DupCheckBanLB)){  
        //         $is_error=1;  
        //         $errorMsg = "Duplicate Bank Account Number present in Lakshmir Bhandar Scheme with Beneficiary ID- $DupCheckBanLB";
        //         array_push($errormsg,$errorMsg);    
        //         return redirect("oap-wcd")->withInput(Input::all())->with('errors',$errormsg)->with('dupBank', $dup_bank); 
        //     } 
        // }        
        // if(!empty($request->aadhar_no)){
        //     $aadhar_count = DB::table($schema . '.beneficiaries')->where('scheme_id',$scheme_id)->where('aadhar_no',trim($request->aadhar_no))->count('aadhar_no');
        //     if($aadhar_count>0){ 
        //         $dup_aadhar=1;
        //         $is_error=1; 
        //         $request->session()->put('dupAadhaarCheck', trim($request->aadhar_no)); 
        //         array_push($errormsg,'Aadhaar Number Already Exist! Please try different.');  
        //         return redirect("oap-wcd")->withInput(Input::all())->with('errors',$errormsg)->with('dup_aadhar', $dup_aadhar);
   
        //     } 
        //  } 

        //  if(!empty($request->aadhar_no)){
        //     $aadharDupCheckWP = DupCheck::getDupCheckAadhar(11,$request->aadhar_no);
        //     if(!empty($aadharDupCheckWP)){
        //         $dup_aadhar=1;
        //         $is_error=1;  
        //         $errorMsg = "Duplicate Aadhaar Number present in Widow Pension Scheme with Beneficiary ID- $aadharDupCheckWP";
        //         array_push($errors, $errorMsg);
        //         return redirect("oap-wcd")->withInput(Input::all())->with('errors',$errormsg)->with('dup_aadhar', $dup_aadhar);
        //     } 
        //  }
        //  if(!empty($request->aadhar_no)){
        //     $aadharDupCheckLB = DupCheck::getDupCheckAadhar(20,$request->aadhar_no);
        //     if(!empty($aadharDupCheckLB)){
        //         $dup_aadhar=1;
        //         $is_error=1;  
        //         $errorMsg = "Duplicate Aadhaar Number present in Lakshmir Bhandar Scheme with Beneficiary ID- $aadharDupCheckLB";
        //         array_push($errors, $errorMsg);
        //         return redirect("oap-wcd")->withInput(Input::all())->with('errors',$errormsg)->with('dup_aadhar', $dup_aadhar);
        //     } 
        //  }

         if(!empty($request->mobile_no)){
            $mobile_count = DB::table($schema . '.beneficiaries')->where('scheme_id',$scheme_id)->whereIn('is_clean', [1, 2])->where('mobile_no',$request->mobile_no)->count('mobile_no');
            if($mobile_count>0){ 
            $dup_mobile=1;
            $is_error=1;  
            $request->session()->put('dupMobileCheck', trim($request->mobile_no));
            array_push($errormsg,'Mobile Number Already Exist! Please try different.');    
            return redirect("oap-wcd")->withInput(Input::all())->with('errors',$errormsg)->with('dup_mobile',$dup_mobile);
          
            } 
        }
         if(count($errormsg)>0){
            if($wq==1){
                return redirect("oap-wcd?wq=1")->withInput(Input::all())->with('errors',$errormsg)->with('dupBank', $dup_bank)->with('dupAadhaar',  $dup_aadhar)->with('dupMobile', $dup_mobile);
            }
            else{
            return redirect("oap-wcd")->withInput(Input::all())->with('errors',$errormsg)->with('dupBank', $dup_bank)->with('dupAadhaar',  $dup_aadhar)->with('dupMobile', $dup_mobile);
            }
           
        }
        //------------End -------------------------//
        $c_time = date('Y-m-d H:i:s', time());
        $pension_details = new BenEntry();
        $pension_details->entry_datetime = $c_time;
        $pension_details->ip_address=$request->ip();
        $allow_ds_entry = intval($scheme_obj->allow_ds_entry);
        $allow_normal_entry = intval($scheme_obj->allow_normal_entry);
        if($allow_ds_entry==0 &&  $allow_normal_entry==0){
                return redirect("/")->with('danger', ' Data entry temporarily suspended.');  
        }
        if ($request->entry_type == 'Form through Duare Sarkar camp') {
            if($scheme_obj->allow_ds_entry==0){
                $errors = array();
                $errorMsg = "Form through  camp temporary suspended";
                array_push($errors, $errorMsg);
                return back()->with('errors', $errors)->withInput(Input::all());
             }
                $pension_details->ds_registration_no = trim($request->ds_registration_no);
                $pension_details->ds_date = trim($request->ds_date);
                if (!empty($ds_phase)) {
                    $pension_details->ds_phase   = $ds_phase->phase_code;
                    if($ds_phase->is_samadhan==TRUE){
                    $pension_details->is_samadhan   = TRUE;
                    }
                    else{
                        $pension_details->is_samadhan   = FALSE;
                    }
                }
        }else if ($request->entry_type == 'Normal Form') {
            if($scheme_obj->allow_normal_entry==0){
                $errors = array();
                $errorMsg = "Normal form Entry temporary suspended";
                array_push($errors, $errorMsg);
                return back()->with('errors', $errors)->withInput(Input::all());
             }
        }
        //Document Dynamic
        $upload_file=array();
        $i=0;
        $doc_master=DocumentType::get();
       // dd($request->file);
        foreach ($doc_list as $doc) {
            if ($request->hasFile('doc_' . $doc)) {
            $doc_file = $request->file('doc_' . $doc);
            $img_data = file_get_contents($doc_file);
            $u_extension_file = $doc_file->getClientOriginalExtension();
            $u_extension=strtolower($u_extension_file);
            $doc_type_name =$doc_master->where('id', $doc)->first() ;
            $mime_type = $doc_file->getMimeType();
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
            $upload_file[$i]['created_by_dist_code']=$distCode;
            $upload_file[$i]['created_by_local_body_code']=$blockCode;
            $upload_file[$i]['document_type']=$doc;
            $upload_file[$i]['scheme_id']=$scheme_id;
            $upload_file[$i]['created_by_level']=$level;
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
       if($cmo_id>0){
        $pension_details->dist_code = $row_cmo->created_by_dist_code;
        $pension_details->rural_urban_id = $row_cmo->is_urban;
        $pension_details->block_ulb_code  = $row_cmo->block_ulb_code;
        $pension_details->block_ulb_name = $row_cmo->block_ulb_name;
        $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->where('district_code',$row_cmo->created_by_dist_code)->first();
        if(empty($assembly)){
            $errors = array();
            $errorMsg = "Invalid Assembly";
            array_push($errors, $errorMsg);
            //return back()->withErrors($errors)->withInput();
            return back()->with('errors', $errors)->withInput(Input::all());
        }
        $assembly_name = $assembly->ac_name;
        $pension_details->assembly_code   =    $request->asmb_cons;
        $pension_details->assembly_name = $assembly_name;
        if($row_cmo->is_urban==1){
            $gp_ward = Ward::where('urban_body_ward_code',$request->gp_ward)->where('urban_body_code',$row_cmo->block_ulb_code)->first();
            if(empty($gp_ward)){
                $errors = array();
                $errorMsg = "Invalid Gp/Ward";
                array_push($errors, $errorMsg);
                //return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput(Input::all());
            }
            $pension_details->gp_ward_code   = $request->gp_ward;
            $pension_details->gp_ward_name   = trim($gp_ward->urban_body_ward_name);
        }
        else if($row_cmo->is_urban==2){
            $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->where('block_code',$row_cmo->block_ulb_code)->first();
            if(empty($gp_ward)){
                $errors = array();
                $errorMsg = "Invalid Gp/Ward";
                array_push($errors, $errorMsg);
                //return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput(Input::all());
            }
            $pension_details->gp_ward_code   = $request->gp_ward;
            $pension_details->gp_ward_name   = trim($gp_ward->gram_panchyat_name);
        }

       }
       else{
        $district_check = District::where('district_code', trim($request->district))->count();
        if ($district_check == 0) {
            $errors = array();
            $errorMsg = "Invalid District";
            array_push($errors, $errorMsg);
            //return back()->withErrors($errors)->withInput();
            return back()->with('errors', $errors)->withInput(Input::all());
        }
        $pension_details->dist_code = $request->district;
        
        $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->where('district_code',$request->district)->first();
        if(empty($assembly)){
            $errors = array();
            $errorMsg = "Invalid Assembly";
            array_push($errors, $errorMsg);
            //return back()->withErrors($errors)->withInput();
            return back()->with('errors', $errors)->withInput(Input::all());
        }
        $assembly_name = $assembly->ac_name;
        $pension_details->assembly_code   =    $request->asmb_cons;
        $pension_details->assembly_name = $assembly_name;
        $urban_code=$request->urban_code;
        $pension_details->rural_urban_id  = $request->urban_code;
        if($urban_code==1){
            $block_ulb = UrbanBody::where('urban_body_code', $request->block)->where('district_code',$request->district)->first();
            if(empty($block_ulb)){
                $errors = array();
                $errorMsg = "Invalid Block/Municipality";
                array_push($errors, $errorMsg);
                //return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput(Input::all());
            }
            $pension_details->block_ulb_code  = $request->block;
            $pension_details->block_ulb_name = $block_ulb->urban_body_name;
            $gp_ward = Ward::where('urban_body_ward_code',$request->gp_ward)->where('urban_body_code',$request->block)->first();
            if(empty($gp_ward)){
                $errors = array();
                $errorMsg = "Invalid Gp/Ward";
                array_push($errors, $errorMsg);
                //return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput(Input::all());
            }
            $pension_details->gp_ward_code   = $request->gp_ward;
            $pension_details->gp_ward_name   = trim($gp_ward->urban_body_ward_name);
        }
        else if($urban_code==2){
            $block_ulb = Taluka::where('block_code',$request->block)->where('district_code',$request->district)->first();
            if(empty($block_ulb)){
                $errors = array();
                $errorMsg = "Invalid Block/Municipality";
                array_push($errors, $errorMsg);
                //return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput(Input::all());
            }
            $pension_details->block_ulb_code  = $request->block;
            $pension_details->block_ulb_name = $block_ulb->block_name;
            $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->where('block_code',$request->block)->first();
            if(empty($gp_ward)){
                $errors = array();
                $errorMsg = "Invalid Gp/Ward";
                array_push($errors, $errorMsg);
                //return back()->withErrors($errors)->withInput();
                return back()->with('errors', $errors)->withInput(Input::all());
            }
            $pension_details->gp_ward_code   = $request->gp_ward;
            $pension_details->gp_ward_name   = trim($gp_ward->gram_panchyat_name);
        }
        else{
            $errors = array();
            $errorMsg = "Invalid Rural/ Urban";
            array_push($errors, $errorMsg);
            //return back()->withErrors($errors)->withInput();
            return back()->with('errors', $errors)->withInput(Input::all());
        }
      }

       

        if ($request->receive_pension != "") {
            $receive_pension = implode(',', $request->receive_pension);
            $pension_details->receive_pension    = $receive_pension;
        }

        if ($request->social_security_pension != "") {
            $social_security_pension = implode(',', $request->social_security_pension);
            $pension_details->social_security_pension   = $social_security_pension;
        }
        $pension_details->wt_special = $wq;
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
        if(!empty(trim($request->caste_certificate_no))){
        $pension_details->caste_certificate_no = $request->caste_certificate_no;
        }
        if(!empty(trim($request->ds_registration_no))){
            $pension_details->ds_registration_no = $request->ds_registration_no;
        }
        //  $pension_details->fisherman_comm=$request->fisherman_comm;
        $pension_details->marital_status = $request->marital_status;
        $pension_details->mothly_income = $request->monthly_income;
        // $pension_details->cast_certificate_no = $request->cast_certificate_no;
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

      
        
        $pension_details->police_station  = $request->police_station;
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
        $pension_details->av_status =  $request->av_status;
        $pension_details->receiving_pension_other_source_1 =  $request->receiving_pension_other_source_1;
        $pension_details->receiving_pension_other_source_2 =  $request->receiving_pension_other_source_2;

        $pension_details->after_091222    = 1;
        $pension_details->is_clean    = 1;
       
        if ($request->is_state_login) {
            $pension_details->created_by_dist_code =  $request->district;
            if ($request->urban_code == 1) {
                $pension_details->created_by_local_body_code =  $block_ulb->sub_district_code;
            } else {
                $pension_details->created_by_local_body_code =  $request->block;
            }
            $pension_details->next_level_role_id =  $this->state_login_next_level_role_id_arr['entry'];
            $pension_details->is_state =  TRUE;
            $pension_details->block_ulb_type = trim($request->urban_code);
        } else {
            $pension_details->created_by_dist_code = $distCode;
            $pension_details->created_by_local_body_code = $blockCode;
            $pension_details->next_level_role_id =  NULL;
            $pension_details->block_ulb_type = NULL;
        }
        if($cmo_id>0){
            $cmo_f_name = '';
            $cmo_m_name = '';
            $cmo_l_name = '';
            $full_name = trim($row_cmo->ben_name);
            $name_explode = explode(' ', trim($row_cmo->ben_name));
            if (count($name_explode) == 1) {
                            $cmo_f_name = $name_explode[0];
                            $cmo_m_name = '';
                            $cmo_l_name = '';
            } else if (count($name_explode) == 2) {
                            $cmo_f_name = $name_explode[0];
                            $cmo_m_name = '';
                            $cmo_l_name = $name_explode[1];
             } else if (count($name_explode) == 3) {
                            $cmo_f_name = $name_explode[0];
                            $cmo_m_name = $name_explode[1];
                            $cmo_l_name = $name_explode[2];
            } 
            else {
                            $cmo_f_name = trim($row_cmo->ben_name);
                            $cmo_m_name = '';
                            $cmo_l_name = '';
            }
            $pension_details->ben_fname = $cmo_f_name;
            $pension_details->ben_mname = $cmo_m_name;
            $pension_details->ben_lname = $cmo_l_name;
            $pension_details->mobile_no  = $row_cmo->sm_mobile_no;
            $pension_details->sm_flag = 1;
            $pension_details->sm_mobile_no = $row_cmo->sm_mobile_no;
            $pension_details->cmo_id = $cmo_id;

            
        }
       
        DB::connection('pgsql5')->beginTransaction();
        DB::connection('pgsql_encwrite')->beginTransaction();
        $is_saved = 0;
      
        try {
            //dd('ok');

            $is_saved = $pension_details->save();
            if($cmo_id>0){
                $inputMainCmo = array();
                $inputMainCmo['jb_beneficiary_id'] = $pension_details->id;
                $inputMainCmo['next_level_role_id'] = 3;
                $upadated_cmo = DB::table('pension.cmo_sm_data')
                ->where([
                    'id' => $cmo_id,'created_by_dist_code' => $distCode,'next_level_role_id' => 2
                ])->whereNull('jb_beneficiary_id')->where('scheme_id',$scheme_id)->update($inputMainCmo);
            }
            else{
                $upadated_cmo=1; 
            }
            if($is_saved && $upadated_cmo)
            {
                $beneficiary_id = $pension_details->id;
                foreach($upload_file as $key => $csm)
                {
                 $upload_file[$key]['beneficiary_id'] = $beneficiary_id;
                }
                $doc_inserted = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($upload_file);
                if( $doc_inserted ){
                  
                    DB::connection('pgsql5')->commit();
                    DB::connection('pgsql_encwrite')->commit();
                    $ben_fullname=trim($request->first_name) . ' ' . trim($request->middle_name) . ' ' . trim($request->last_name);

                    // $this->bioauthcheckInsert($distCode,$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$blockCode,$user_id);
                    // if(($request->caste_category=='SC' || $request->caste_category=='ST') && !empty($request->caste_certificate_no)){
                    //  $this->casteInfoCheckInsert($distCode,$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->caste_certificate_no),$blockCode,$user_id);
                    // }
                    
                    // $this->RationcheckInsert($distCode,$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$blockCode,$user_id,$request->dob);
                    try {
                        $this->bioauthcheckInsert($distCode,$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$blockCode,$user_id);
                    } catch (\Exception $e) {
                        $inputMain['life_certificate_checked'] = -1;
                        $upadated_main = DB::table($schema . '.beneficiary')
                        ->where([
                          'id' => $beneficiary_id, 'created_by_local_body_code' => $blockCode,
                          'created_by_dist_code' => $distCode
                        ])->update($inputMain);
                    }
                    try {
                        if(($request->caste_category=='SC' || $request->caste_category=='ST') && !empty($request->caste_certificate_no)){
                            $this->casteInfoCheckInsert($distCode,$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->caste_certificate_no),$blockCode,$user_id);
                           }
                    } catch (\Exception $e) {
                        $inputMain['caste_certificate_checked'] = -1;
                        $upadated_main = DB::table($schema . '.beneficiary')
                        ->where([
                          'id' => $beneficiary_id, 'created_by_local_body_code' => $blockCode,
                          'created_by_dist_code' => $distCode
                        ])->update($inputMain);
                    }
                    
                    try {
                        $data = $this->RationcheckInsert($distCode,$beneficiary_id,$scheme_id,$ben_fullname,$request->ip(),trim($request->aadhar_no),$blockCode,$user_id,$request->dob);
                      } catch (\Exception $e) {
                        $inputMain['aadhaar_no_checked'] = -1;
                        $upadated_main = DB::table($schema . '.beneficiary')
                        ->where([
                          'id' => $beneficiary_id, 'created_by_local_body_code' => $blockCode,
                          'created_by_dist_code' => $distCode
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
                        $dob_kh=$ben_details->dob_kh;
                        $dob_is_match_kh=$ben_details->dob_is_match_kh;
                        $dob=$ben_details->dob;
                        if($wq==1){
                            return redirect("oap-wcd?wq=1")->with('success', 'Application Submitted Successfully')
                            ->with('id',  $beneficiary_id)->with('caste_certificate_checked',  $caste_certificate_checked)
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
                            ->with('aadhaar_no_validation_msg',$aadhaar_no_validation_msg)
                            ->with('dob_kh',$dob_kh)
                            ->with('dob_is_match_kh',$dob_is_match_kh)
                            ->with('dob',$dob);
                        }
                        else
                        return redirect("oap-wcd")->with('success', 'Application Submitted Successfully')
                        ->with('id',  $beneficiary_id)->with('caste_certificate_checked',  $caste_certificate_checked)
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
                        ->with('aadhaar_no_validation_msg',$aadhaar_no_validation_msg)
                        ->with('dob_kh',$dob_kh)
                        ->with('dob_is_match_kh',$dob_is_match_kh)
                        ->with('dob',$dob);
                    }
                   
                }
                else{
                    DB::connection('pgsql5')->rollback();
                    DB::connection('pgsql_encwrite')->rollback();
                    if($wq==1){
                        return redirect("oap-wcd?wq=1")->withInput(Input::all())->with('errors', array('Some error.Please try again'));
                    }
                    else{
                    return redirect("oap-wcd")->withInput(Input::all())->with('errors', array('Some error.Please try again'));
                    } 
                }
           
            }
            else{
               //dd('ok587');
                DB::connection('pgsql5')->rollback();
                DB::connection('pgsql_encwrite')->rollback();
                if($wq==1){
                    // dd(14);
                    return redirect("oap-wcd?wq=1")->withInput(Input::all())->with('errors', array('Some error.Please try again'));
                }
                else{
                    // dd(24);
                return redirect("oap-wcd")->withInput(Input::all())->with('errors', array('Some error.Please try again'));
                }
            }
          
           
        } catch (\Exception $e) {
            
           //dd($e);
            DB::connection('pgsql5')->rollback();
            DB::connection('pgsql_encwrite')->rollback();
            if($wq==1){
                return redirect("oap-wcd?wq=1")->withInput(Input::all())->with('errors', array('Some error.Please try again'));
            }
            else{
            return redirect("oap-wcd")->withInput(Input::all())->with('errors', array('Some error.Please try again'));
            }
            
        }
    }
    catch (\Exception $e) {
        return redirect("oap-wcd?wq=1")->withInput(Input::all())->with('errors', array('Some error.Please try again'));

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
        // dd('ok');
        // dd($request->all());
          //return redirect("/")->with('error', 'Update temporary suspended.');
          $base_url = url('/');
          $id = $request->id;
          $scheme_id = (int) $request->scheme_id;
          // dd($scheme_id);
          $designation_id_old = Auth::user()->designation_id_old;
          if (!is_int($scheme_id)) {
              return redirect("/")->with('error', 'Scheme Code Not Valid');
          }
          if (!is_numeric($id)) {
              return redirect("/")->with('error', 'Applicant ID Not Valid');
          }
          $created_by = Auth::user()->id;
  
          $is_active = 0;
          $mapping_level = NULL;
          $roleArray = $request->session()->get('role');
          foreach ($roleArray as $roleObj) {
              if ($roleObj['scheme_id'] == $scheme_id) {
                  $is_active = 1;
                  $mapping_level = $roleObj['mapping_level'];
                  $distCode = $roleObj['district_code'];
                  $is_state_login = $roleObj['is_state_login'];
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
              return redirect("/")->with('danger', 'User Disabled');
          }
          if ($is_state_login != $request->is_state_login) {
  
              return redirect("/")->with('danger', 'User Disabled');
          }
          if ($is_state_login == 0) {
              if (empty($mapping_level) || empty($distCode)) {
                  return redirect("/")->with('danger', 'User Disabled');
              }
          }
          if ($is_state_login) {
              $query = PensionOAPWCD::where(['id' => $id, 'is_state' => TRUE, 'scheme_id' => $scheme_id]);
          } else {
              $query = PensionOAPWCD::where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id]);
          }
          if ($designation_id_old == 'Verifier') {
              if ($is_state_login) {
                  $query = $query->where('next_level_role_id', $this->state_login_next_level_role_id_arr['entry']);
              } else {
                  $query = $query->whereNull('next_level_role_id');
              }
          } else if ($designation_id_old == 'Approver') {
              if ($is_state_login) {
                  $query = $query->where('next_level_role_id', $this->state_login_next_level_role_id_arr['verified']);
              } else {
                  $query = $query->where('is_verified',1)->where('is_approved',0)->where('is_rejected',0);
              }
          }
          $row = $query->first();
        //   dd($row->toArray());
          if (empty($row)) {
              return redirect("/")->with('danger', 'Not Allowed');
          }
       
          $isValidarr = $this->validateInput($request,  $scheme_id, 2,NULL, (($row->is_lb_imported == 1) ? 1 : 0));
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
              $mobile_count = PensionOAPWCD::where('mobile_no', trim($request->mobile_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
             // dd( $mobile_count);
              if ($mobile_count > 0) {
                  $errors = array();
                  $errorMsg = "Mobile Number Already Exist! Please try different.";
                  array_push($errors, $errorMsg);
                  return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors',  $errors);
              }
          }
          //--------- Duplicate bank A/C check---------- //
          $bankCount = PensionOAPWCD::whereRaw("trim(bank_code)=trim(" . "'" . $request->bank_account_number . "'" . ")")->where('id', '!=', $id)
              ->whereRaw("(" . $check_condition_str . ")")
              ->count('id');
          
          if ($bankCount > 0) {
              $errors = array();
              $errorMsg = "Bank A/C Already Exist!";
              array_push($errors, $errorMsg);
              return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors',  $errors);
          }
          $count = PensionOAPWCD::where('aadhar_no', trim($request->aadhar_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
          if ($count > 0) {
              $request->session()->put('dupAadhaarCheck', trim($request->aadhar_no));
              $errors = array();
              $errorMsg = "Aadhaar Number Already Exist! Please try different.";
              array_push($errors, $errorMsg);
              return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('dupAadhaar', 1)->with('errors',  $errors);
          }
          $ifsc = trim($request->bank_ifsc_code);
          $bank_branch = trim($request->bank_branch);
          $name_of_bank = trim($request->name_of_bank);
          $row_count_bank = BankDetails::whereraw("trim(branch)='$bank_branch'")->whereraw("trim(ifsc)='$ifsc'")->where('is_active',1)->whereraw("trim(bank)='$name_of_bank'")->count();
          //$bank_details = BankDetails::whereraw("trim(ifsc)='$ifsc'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
          $bank_details = BankDetails::where('ifsc', trim($ifsc))->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
           $new_bank_code=$bank_details->bank_code;
          if ($row_count_bank == 0) {
              $errors = array();
              $errorMsg = "Bank IFSC and Bank Name Not Match!";
              array_push($errors, $errorMsg);
              return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors',  $errors);
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
  
  
          if ($request->is_state_login) {
              $state_created_by_dist_code =  $request->district;
              if ($request->urban_code == 1) {
                  $state_created_by_local_body_code =  $block_ulb->sub_district_code;
              } else {
                  $state_created_by_local_body_code =  $request->block;
              }
              $urban_code_state = trim($request->urban_code);
          } else {
              $state_created_by_dist_code = $distCode;
              $state_created_by_local_body_code = $blockCode;
              $urban_code_state = NULL;
          }
          $c_time=date('Y-m-d H:i:s');
          $user_id = AuthChecker::getUserId();
          $input = [
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
  
  
  
              //'bank_name'  => $request->name_of_bank,
              //'branch_name'   => $request->bank_branch,
              //'bank_code'    => $request->bank_account_number,
              //'bank_ifsc'   => $request->bank_ifsc_code,
              //'npci_bank_code'=> $new_bank_code,
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
              'is_clean' => 1,
          ];
          if(!empty(trim($request->caste_certificate_no))){
            $input['caste_certificate_no']=$request->caste_certificate_no;
          }
          if($row->is_lb_imported == 0 || empty($row->is_lb_imported)){
            $input['ben_fname']=$request->first_name;
            $input['ben_mname']=$request->middle_name;
            $input['ben_lname']=$request->last_name;
            $input['bank_name']=$request->name_of_bank;
            $input['branch_name']=$request->bank_branch;
            $input['bank_code']=$request->bank_account_number;
            $input['bank_ifsc']=$request->bank_ifsc_code;
            $input['npci_bank_code']=$new_bank_code;
          }
          $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
          if (empty($scheme_obj)) {
              return redirect("/")->with('danger', 'Scheme Not Found');
          }
          if (!empty($scheme_obj->short_code)) {
              $schema = $scheme_obj->short_code;
              $scheme_length =  $scheme_obj->scheme_length;
              $id_length = $scheme_obj->id_length;
          } else {
              $schema = "pension";
          }
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
            $doc_type_name =$doc_master->where('id', $doc)->first() ;
            $doc_file = $request->file('doc_' . $doc);
            $img_data = file_get_contents($doc_file);
            $u_extension_file = $doc_file->getClientOriginalExtension();
            $u_extension=strtolower($u_extension_file);
            $mime_type = $doc_file->getMimeType();
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
         
          try {
              
            $arch_status=DB::statement("INSERT INTO oap_wcd.arc_beneficiary(id, 
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
           receiving_pension_other_source_1, receiving_pension_other_source_2 from oap_wcd.beneficiary where id=" . $id . ")");
            $is_update = DB::table($scheme_schema . '.beneficiary')->where(['id' => $request->id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id])->update($input);
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

            // if($request->id==9100885)
            // {
            //     dump($arch_status);dump($is_update); dump($doc_inserted_arch); dump($doc_inserted_del); dump($doc_inserted); dd($is_saved_log);

            // }
           // dump($is_update); dump($doc_inserted_arch); dump($doc_inserted_del); dump($doc_inserted); dd($is_saved_log);
            if($arch_status && $is_update && $doc_inserted_arch && $doc_inserted_del && $doc_inserted && $is_saved_log){
                DB::commit();
                DB::connection('pgsql_encwrite')->commit();
                if ($designation_id_old == 'Operator')
                   return redirect("application-list-read-only-edit?pr1=" . $scheme_schema)->with('success', 'Application Updated Successfully')
                ->with('id',   $row->getBenidAttribute());
              else {
                return redirect('/')->with('success', 'Application Updated Successfully');
                }
            }
            else{
                DB::connection('pgsql_encwrite')->rollback();
                DB::rollback();
                if ($designation_id_old == 'Operator')
                 return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', array('Some error.Please try again'));
                else {
                  return redirect('/')->with('danger', 'Some error.Please try again');
                }
            }
        } catch (\Exception $e) {
           // dd($e);
            DB::rollback();
            DB::connection('pgsql_encwrite')->rollback();
            if ($designation_id_old == 'Operator')
            return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', array('Some error.Please try again'));
            else {
                if($is_nsap==1){
                    return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', array('Some error.Please try again'));

                }else
                return redirect('/')->with('danger', 'Some error.Please try again');
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





    private function validateInput($request, $scheme_id, $add_edit_code,$cmo_id,$is_lb_app)
    {
    //    dd($request->all());
        $today = date("Y-m-d");
        $entry_type_arr = array('Normal Form', 'Form through Duare Sarkar camp', 'Form through Duare Sarkar camp');
        $withoutAadhaarreason_key =  array_keys(Config::get('constants.withoutAadhaarreason'));
        if ($add_edit_code == 1) {
            $entry_type_r = "required";
        } else {
            $entry_type_r = "nullable";
        }
        // if ($request->caste_category == 'SC' || $request->caste_category == 'ST') {
        //     $caste_no = "required";
        // }else{
        //     $caste_no = "nullable";
        // }
        $rules = [
            'entry_type' => $entry_type_r . '|in:' . implode(",", $entry_type_arr),
            'ds_registration_no' => 'required_if:entry_type,==,Form through Duare Sarkar camp|max:25',
            'ds_date' => 'required_if:entry_type,Form through Duare Sarkar camp|nullable|date|before_or_equal:' . $today,
        ];
        // dump ($cmo_id);dump($is_lb_app);die;
        if(!empty($cmo_id) || $is_lb_app == 1){
            $rules['first_name']='string|nullable';
            $rules['middle_name']='string|nullable';
            $rules['last_name']='string|nullable';

        }
        else{
            $rules['first_name']='required|string|max:200';
            $rules['middle_name']='string|nullable';
            $rules['last_name']='required|string|max:200';
        }
        $rules['gender']='required';
        $rules['txt_age']='required|numeric';
        $rules['father_first_name']='required|string|max:200';
        $rules['father_middle_name']='string|nullable';
        $rules['father_last_name']='required|string|max:200';
        $rules['mother_first_name']='required|string|max:200';
        $rules['mother_middle_name']='string|nullable';
        $rules['mother_last_name']='required|string|max:200';
        $rules['caste_category']='required';
        $rules['spouse_first_name']='string|nullable';
        $rules['spouse_middle_name']='string|nullable';
        $rules['spouse_last_name']='string|nullable';
        $rules['bpl_seq_no']='string|nullable|max:12';
        $rules['bpl_id_no']='string|nullable|max:12';
        $rules['bpl_total_score']='integer|nullable';
        $rules['monthly_income']='required|numeric|between: 0.00,999999.99';
        $rules['ration_card_no']='string|nullable|max:11';
        $rules['ahl_tin']='string|nullable|max:100';
        $rules['aadhar_no']='required|numeric|digits:12';
        $rules['epic_voter_id']='string|nullable|max:20';
        $rules['pan_no']='string|nullable|max:12';
        $rules['asmb_cons']='required';
        $rules['police_station']='required|string';
        $rules['village']='required|string|max:300';
        $rules['house']='string|nullable';
        $rules['post_office']='required|string';
        $rules['pin_code']='required|numeric|digits:6';
        $rules['residency_period']='required|integer';
        if(!empty($cmo_id)){
           // $rules['mobile_no']='numeric|nullable';

        }
        else{
            $rules['mobile_no']='required|numeric|digits:10';
        }
        $rules['email']='string|email|nullable';
        $rules['name_of_bank']='required|string|max:200';
        $rules['bank_branch']='required|string|max:200';
        $rules['bank_account_number']='required|numeric';
        $rules['bank_ifsc_code']='required|string';
            
            
        
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
        // $attributes['caste_no'] = 'Caste Certificate Number';
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
    /**********************************************************/
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
    function getCapacityBrief($scheme_id, $district)
    {
        $return_arr = array();
        /*
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
                    ->where('created_by_dist_code', $district)
                    ->first();
            }
            $return_arr['approved'] = intval($total_data->approved);
            $return_arr['pending'] = intval($total_data->pending);
        } else {
            $return_arr['visible'] = 0;
        }
        */
        $return_arr['visible'] = 0;
        return $return_arr;
    }
    public function editList(Request $request)
    {
        // dd($request);
        $scheme_id =  $this->scheme_id;
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        if (!in_array($designation_id_old, array('Operator'))) {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $level = $roleObj['mapping_level'];
                $is_urban = $roleObj['is_urban'];
                $distCode = $roleObj['district_code'];
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
    //    dd($blockCode);
        $scheme_row = Scheme::select('scheme_name')->where('id', $this->scheme_id)->first();
        $scheme_name = $scheme_row->scheme_name;
        $report_type_name = 'Application List ';
        if (request()->ajax()) {
            // dd(request());
            $condition = array();
            $condition["created_by_dist_code"] = $distCode;
            $condition["created_by_local_body_code"] = $blockCode;
           // $condition["next_level_role_id"] = 0;
            $serachvalue = $request->search['value'];
            $limit = $request->input('length');
            $offset = $request->input('start');
            $filter_status = $request->input('filter_status');
            $filter_status_new = $request->input('filter_status_new');
            $totalRecords = 0;
            $filterRecords = 0;
            $data = array();
            $query = BenEntry::where($condition)
            ->where('scheme_id', $scheme_id);

            if (empty($filter_status_new)) {
                $query = $query->whereRaw("((next_level_role_id = 0 AND legacy_import IS TRUE) OR no_aadhar = 1 OR no_mobile = 1 OR unlock_status = 1)");
            }

            if (!empty($filter_status)) {
                if ($filter_status == 1) {
                    $query = $query->whereNull('next_level_role_id_edit');
                } else if ($filter_status == 2) {
                    $query = $query->where('next_level_role_id_edit', 999)->where('unlock_status', 1);
                } else if ($filter_status == 3) {
                    $query = $query->where('next_level_role_id_edit', '!=', 999)->where('unlock_status', 1)->where('next_level_role_id_edit', '>', 0);
                } else if ($filter_status == 4) {
                    $query = $query->whereNull('next_level_role_id_edit')->whereNull('unlock_status')->whereNotNull('caste');
                } else {
                }
            }
            if (!empty($filter_status_new)) {
                if ($filter_status_new == 1) {
                    $query = $query->where('no_aadhar', 1);
                } else if ($filter_status == 2) {
                    $query = $query->where('no_mobile', 1);
                }
            }
            $serachvalue = $request->search['value'];

            if (empty($serachvalue)) {
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                    'unlock_status','legacy_import','no_aadhar','no_mobile','mobile_no','aadhar_no', 'id', 'created_by_dist_code',
                    'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                    'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'next_level_role_id_edit', 'caste'
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
                            'unlock_status','legacy_import', 'no_aadhar','no_mobile','mobile_no','aadhar_no','id', 'created_by_dist_code',
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
                            'ben_lname', 'gender', 'ben_age', 'ben_mname', 'next_level_role_id_edit', 'caste'
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
                            'unlock_status', 'legacy_import','no_aadhar','no_mobile','mobile_no','aadhar_no','id', 'created_by_dist_code',
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
                            'ben_lname', 'gender', 'ben_age', 'ben_mname', 'next_level_role_id_edit', 'caste'
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
                    // return $data->getName();
                    return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                })
                ->addColumn('benf_name', function ($data) {
                    return "Father Name";
                })
                ->addColumn('mobile_no', function ($data) {
                    if(is_null($data->mobile_no) or $data->mobile_no==''){
                        return '';   
                    }
                    else
                    return $data->mobile_no;
                })
                ->addColumn('aadhar_no', function ($data) {
                    if(is_null($data->aadhar_no) or trim($data->aadhar_no)==''){
                        return '';   
                    }
                    else
                    return "**********".substr($data->aadhar_no,-4);
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
                    if ($bank_code!= '') {
                        // echo 1;die;
                        $mask_bank_code = '********' . substr($bank_code, 8, 4);
                        // dd($mask_bank_code);
                    }else{
                        // echo 1;die;
                        $mask_bank_code = $bank_code;
                    }
                    return $mask_bank_code;
                    //return $data->bank_code;
                })
                ->addColumn('village_town_city', function ($data) {
                    return $data->village_town_city;
                })
                ->addColumn('status', function ($data) use ($scheme_id) {
                    $val = '';
                    if ($data->legacy_import==true and $data->next_level_role_id==0 and is_null($data->next_level_role_id_edit) ) {
                        $val =$val. 'Approved Legacy Data.. Need to modify or add aadhaar number and mobile number and also Upload Supporting documents';
                        $val =$val."<br/>";
                    } 
                    if ($data->no_aadhar==1) {
                        if($data->next_level_role_id==0)
                         $next_level_txt='Approved';
                         else{
                            $next_level_txt='Non Approved'; 
                         }
                        $val =$val.  $next_level_txt.' Aadhaar Number Blank or Aadhar number is not 12 Digit';
                        $val =$val."<br/>";
                    } 
                    if ($data->no_mobile==1) {
                        if($data->next_level_role_id==0)
                         $next_level_txt='Approved';
                         else{
                            $next_level_txt='Non Approved'; 
                         }
                        $val =$val.  $next_level_txt.' Mobile Number Blank or Mobile number is not 10 Digit';
                        $val =$val."<br/>";
                    } 
                    return $val;
                }) ->addColumn('action', function ($data) use ($scheme_id) {
                    $val = '';
                    if ($data->next_level_role_id_edit > 0 and $data->unlock_status == 1 and $data->next_level_role_id_edit != 999) {
                        $val = '<span class="label label-info">Alredy Edited and Pending at Approver</span>';
                    } else if ($data->next_level_role_id_edit == 999 and $data->unlock_status == 1) {
                        $val = '<span class="label label-info">Alredy Edited and Pending at Verifier</span>';
                    }
                    else if ($data->no_aadhar == 1 or $data->no_mobile == 1) {
                        $val = '<a href="editOap/' . $data->id . '" class="btn btn-primary ben_view_button" role="button" >Edit</a>';
                    }
                    else if ($data->next_level_role_id == 0 and $data->legacy_import == true and is_null($data->next_level_role_id_edit)) {
                        $val = '<a href="editOap/' . $data->id . '" class="btn btn-primary ben_view_button" role="button" >Edit</a>';
                    }
                    else if ($data->next_level_role_id == 0 and $data->legacy_import == true and $data->next_level_role_id_edit==0) {
                        $val = '<span class="label label-info">Alredy Edited and Approved.</span>';
                    }
                    return $val;
                })
                ->rawColumns(['ben_id', 'ben_name', 'ben_age', 'gender', 'bank_ifsc', 'bank_code', 'village_town_city', 'action','status'])
                ->make(true);
        } else {

            return view(
                'OAPWCD/editList',
                [
                    'district_code' => $distCode,
                    'scheme' => $this->scheme_id,
                    'scheme_name' => $scheme_name,
                    'report_type_name' => $report_type_name,
                    'is_urban' => $is_urban

                ]
            );
        }
    }
    public function editUnlock(Request $request)
    {
        // dd($request->all());
        $scheme_id =  $this->scheme_id;
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        if (!in_array($designation_id_old, array('Operator'))) {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $level = $roleObj['mapping_level'];
                $is_urban = $roleObj['is_urban'];
                $distCode = $roleObj['district_code'];
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                } else {
                    $blockCode = $roleObj['taluka_code'];
                }
                break;
            }
        }
    //    dd($is_active);
        if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disabled');
        }
        $id = $request->id;
        if (empty($id)) {
            return redirect("/")->with('error', 'Application Id Not Found');
        }
        if (!ctype_digit($id)) {
            return redirect("/")->with('error', 'Application Id Valid');
        }
        $condition = array();
        $condition["created_by_dist_code"] = $distCode;
        $condition["created_by_local_body_code"] = $blockCode;
       // $condition["next_level_role_id"] = 0;
        $condition["id"] =  $id;
        $row = BenEntry::where($condition)->whereraw("((next_level_role_id=0 and legacy_import=TRUE) or no_aadhar=1 or no_mobile=1)")->first();
        //dd($row);
        if (empty($row)) {
            return redirect("/")->with('error', 'Application Id Valid');
        }
        $scheme_row = Scheme::select('scheme_name')->where('id', $this->scheme_id)->first();
        $scheme_name = $scheme_row->scheme_name;
        $districts = District::where('is_revenue_district', '=', '1')->get(['district_code', 'district_name']);
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->first();
        if (!empty($doc_id_list->doc_list_man))
            $doc_list_man = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type')->whereIn("id", json_decode($doc_id_list->doc_list_man))->get()->toArray();
        else
            $doc_list_man = array();
        if (!empty($doc_id_list->doc_list_opt))
            $doc_list_opt = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type')->whereIn("id", json_decode($doc_id_list->doc_list_opt))->get()->toArray();
        else
            $doc_list_opt = array();
        if (count($doc_list_man) > 0 || count($doc_list_opt) > 0) {
            $doc_list = array_merge($doc_list_man, $doc_list_opt);
        } else {
            $doc_list = array();
        }
        if (!empty($doc_id_list['doc_list_man_group']))
            $doc_list_man_group = json_decode($doc_id_list['doc_list_man_group']);
        else
            $doc_list_man_group = array();
        if (!empty($doc_list_man_group)) {
            $doc_list = array_merge($doc_list_man, $doc_list_opt);
            $all_doc_id = array();
            foreach ($doc_list as $mDoc) {
                array_push($all_doc_id, $mDoc['id']);
            }
            $document_msg = '';
            if (count($doc_list)) {
                foreach ($doc_list_man_group as $man_group) {
                    $document_msg .= '<div  class="form-group col-md-12" >';
                    $heading_msg = "At least one document must be uploaded for ";
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
            $encloser_list = array();
            $i = 0;
            $encolserdata = BenDocs::select('document_type', 'attched_document')->where('scheme_id',$scheme_id)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->get()->pluck('attched_document','document_type')->toArray();
            $already_id = array_keys($encolserdata);
            $doc_profile_image_id_row = DocumentType::select('id')->where("is_profile_pic", true)->first();
            $doc_profile_image_id = $doc_profile_image_id_row->id;
            $doc_profile_image_val = '';
            if (count($doc_list) > 0) {
                foreach ($doc_list as $doc) {
                    $encloser_list[$i]['id'] = $doc['id'];
                    $encloser_list[$i]['is_profile_pic'] = intval($doc['is_profile_pic']);
                    $encloser_list[$i]['doc_size_kb'] = $doc['doc_size_kb'];
                    $encloser_list[$i]['doc_name'] = $doc['doc_name'];
                    $encloser_list[$i]['doc_type'] = $doc['doc_type'];
                    if (count($encolserdata) > 0) {
                        if (in_array($doc['id'], $already_id)) {
                            if ($doc['is_profile_pic'] == true) {
                                $doc_profile_image_val = $encolserdata[$doc['id']];
                            }
                            $encloser_list[$i]['can_download'] = 1;
                        } else {
                            $encloser_list[$i]['can_download'] = 0;
                        }
                    } else {
                        $encloser_list[$i]['can_download'] = 0;
                    }
                    if (in_array($doc['id'], json_decode($doc_id_list['doc_list_man']))) {
                        if (count($encolserdata) > 0) {
                            //dump($doc['id']);
                            if (in_array($doc['id'], $already_id)) {
                                $encloser_list[$i]['required'] = 0;
                            } else {
                                $encloser_list[$i]['required'] = 1;
                            }
                        } else {
                            $encloser_list[$i]['required'] = 1;
                        }
                        if ($doc['id'] == 116) {
                            $encloser_list[$i]['required'] = 0;
                            $encloser_list[$i]['mandatory'] = 0;
                        } else {
                            $encloser_list[$i]['mandatory'] = 1;
                        }
                    } else {
                        $encloser_list[$i]['required'] = 0;
                        $encloser_list[$i]['mandatory'] = 0;
                    }
    
    
    
    
                    $i++;
                }
            }
        $status='';
        if($row->next_level_role_id==0){
            $next_level_status='Approved';
        }
        else
        $next_level_status='Non Approved';
        $issue_text='Need to provide ';
        if ($row->legacy_import==true and $row->next_level_role_id==0 and is_null($row->next_level_role_id_edit) ) {
            $issue_text =$issue_text. 'Legacy Data';
            //$status =$status."<br/>";
        } 
        if ($row->no_aadhar==1) {
            
            $issue_text =$issue_text.'  Aadhaar Number,';
           // $status =$status."<br/>";
        } 
        if ($row->no_mobile==1) {
            $issue_text =$issue_text.' Mobile Number';
        } 
       //dd($status);
        return view('OAPWCD/pension_edit_unlock', [
            'doc_profile_image_id' => $doc_profile_image_id, 
            'max_dob' => $this->max_dob,  
            'scheme_name' => $scheme_name, 'row' => $row, 
            'document_msg' => $document_msg, 
            'districts' => $districts, 'scheme_id' => $scheme_id, 
            'encloser_list' => $encloser_list, 
            'profile_img' => $doc_profile_image_id,
            'next_level_status' => $next_level_status,
            'issue_text' => $issue_text
          ]);
    }
    function editOapPost(Request $request)
    {
        $scheme_id =  $this->scheme_id;
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        if (!in_array($designation_id_old, array('Operator'))) {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $level = $roleObj['mapping_level'];
                $is_urban = $roleObj['is_urban'];
                $distCode = $roleObj['district_code'];
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
        $id = $request->id;
        if (empty($id)) {
            return redirect("/")->with('error', 'Application Id Not Found');
        }
        if (!ctype_digit($id)) {
            return redirect("/")->with('error', 'Application Id Valid');
        }
        $condition = array();
        $condition["created_by_dist_code"] = $distCode;
        $condition["created_by_local_body_code"] = $blockCode;
       // $condition["next_level_role_id"] = 0;
        $condition["id"] =  $id;
        $row = PensionOAPWCD::where($condition)->whereraw("((next_level_role_id=0 and legacy_import=TRUE) or no_aadhar=1 or no_mobile=1)")->first();
        if (empty($row)) {
            return redirect("/")->with('error', 'Application Id Valid');
        }
        $max_dob = $this->max_dob;
        $caste_key =  array_keys(Config::get('constants.caste'));
        $marital_status_key =  array_keys(Config::get('constants.marital_status'));
        $gender_key =  array_keys(Config::get('constants.gender'));

        $rules = [
            'gender' => 'required|in:' . implode(",", $gender_key),
            'dob' => 'nullable|date|before_or_equal:' . $max_dob,
            'txt_age' => 'required|numeric',
            'father_first_name' => 'required|string|max:200',
            'father_middle_name' => 'string|nullable',
            'father_last_name' => 'required|string|max:200',
            'mother_first_name' => 'required|string|max:200',
            'mother_middle_name' => 'string|nullable',
            'mother_last_name' => 'required|string|max:200',
            'caste_category' => 'required|in:' . implode(",", $caste_key),
            'marital_status' => 'required|in:' . implode(",", $marital_status_key),
            'spouse_first_name' => 'string|nullable',
            'spouse_middle_name' => 'string|nullable',
            'spouse_last_name' => 'string|nullable',
            'bpl_seq_no' => 'string|nullable|max:12',
            'bpl_id_no' => 'string|nullable|max:12',
            'bpl_total_score' => 'integer|nullable',
            'monthly_income' => 'required|numeric|between: 0.00,999999.99',
            // 'ration_card_cat' => 'required|string',
            'ration_card_no' => 'string|nullable|max:11',
            'ahl_tin' => 'string|nullable|max:100',
            'aadhar_no' => 'required|numeric|digits:12',
            'epic_voter_id' => 'required|string|max:20',
            'pan_no' => 'string|nullable|max:12',
            'district' => 'required',
            'urban_code' => 'required',
            'police_station' => 'required',
            'block' => 'required',
            'gp_ward' => 'required',
            'asmb_cons' => 'required|string',
            'police_station' => 'required|string',
            'village' => 'required|string|max:300',
            'house' => 'string|nullable',
            'post_office' => 'required|string',
            'pin_code' => 'required|numeric|digits:6',
            'residency_period' => 'required|integer',
            'mobile_no' => 'required|numeric|digits:10',
            'email' => 'string|email|nullable'
        ];
        
        $attributes = array();
        $messages = array();
        $attributes['first_name'] = 'Beneficiary First Name';
        $attributes['middle_name'] = 'Beneficiary Middle Name';
        $attributes['last_name'] = 'Beneficiary Last Name';
        $attributes['bank_ifsc_code'] = 'IFS Code';
        $attributes['name_of_bank'] = 'Bank Name';
        $attributes['bank_branch'] = 'Bank Branch Name';
        $attributes['bank_account_number'] = 'Bank Account No.';
        $attributes['gender'] = 'Gender';
        $attributes['dob'] = 'Date of Birth';
        $attributes['txt_age'] = 'Age';
        $attributes['father_first_name'] = 'Father First Name';
        $attributes['father_middle_name'] = 'Father Middle Name';
        $attributes['father_last_name'] = 'Father Last Name';
        $attributes['mother_first_name'] = 'Mother First Name';
        $attributes['mother_middle_name'] = 'Mother First Name';
        $attributes['mother_last_name'] = 'Mother First Name';
        $attributes['caste_category'] = 'Caste';
        $attributes['marital_status'] = 'Marital Status';
        $attributes['mobile_no'] = 'Mobile Number';
        $attributes['spouse_first_name'] = 'Spouse First Name';
        $attributes['spouse_middle_name'] = 'Spouse Middle Name';
        $attributes['spouse_last_name'] = 'Spouse Last Name';
        $attributes['monthly_income'] = 'Monthly Family Income(In Rs)';

        $attributes['ration_card_cat'] = 'Digital Ration Card Number';
        $attributes['ration_card_no'] = 'Digital Ration Card Number';
        $attributes['ahl_tin'] = 'AHL TIN';
        $attributes['aadhar_no'] = 'Aadhaar Number';
        $attributes['epic_voter_id'] = 'EPIC/Voter Id number';
        $attributes['pan_no'] = 'PAN';
        $attributes['bpl_seq_no'] = 'BPL Seq Number';
        $attributes['bpl_id_no'] = 'BPL Id Number';
        $attributes['bpl_total_score'] = 'BPL Total Score';

        $attributes['district'] = 'District';
        $attributes['asmb_cons'] = 'Assembly Constituency';
        $attributes['urban_code'] = 'Rural/ Urban';
        $attributes['police_station'] = 'Police Station';
        $attributes['block'] = 'Block/Municipality/Corp';
        $attributes['gp_ward'] = 'GP/Ward No.';
        $attributes['village'] = 'Village/Town/City';
        $attributes['house_premise_no'] = 'House / Premise No.';
        $attributes['post_office'] = 'Post Office';
        $attributes['pin_code'] = 'Pin Code';
        $attributes['residency_period'] = 'Number of years Dwelling in WB';
        $attributes['email'] = 'Email Id';
        $doc_id_list = SchemeDocMap::select('doc_list_man')->where('scheme_code', $scheme_id)->first();
        //dd($request->add_edit_status);
        if (isset($doc_id_list['doc_list_man']) && $doc_id_list['doc_list_man'] != 'null') {

            $in_array = json_decode($doc_id_list->doc_list_man);
        } else
            $in_array = array();
        $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->get();
        if (count($in_array) > 0) {
            foreach ($doc_list as $key => $value) {
                if (in_array($value->id,  $in_array)) {
                    $previus_uploaded =   $request->input('doc_already_' . $value->id);
                    if ($previus_uploaded == 0) {
                        if ($value->id == 116) {
                            $required = 'nullable';
                        } else
                            $required = 'required';
                    } else {
                        $required = 'nullable';
                    }
                } else {
                    $required = 'nullable';
                }
                $rules['doc_' . $value->id] = $required . '|mimes:' . $value->doc_type . '|max:' . $value->doc_size_kb . ',';
                //$rules['doc_' . $value->id] = $value->doc_name . ',';
                $messages['doc_' . $value->id . '.max'] = "The file uploaded for " . $value->doc_name . " size must be less than :max KB";
                $messages['doc_' . $value->id . '.mimes'] = "The file uploaded for " . $value->doc_name . " must be of type " . $value->doc_type;
                $messages['doc_' . $value->id . '.required'] = "Document for " . $value->doc_name . " must be uploaded";
            }
        }
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            if (!empty($request->dob)) {
                $diff = Carbon::parse($request->dob)->diffInYears($this->base_dob_chk_date);
                //dd($request->toArray());
                if ($diff < 60) {
                    $return_text = 'Date of Birth Invalid';
                    $return_msg = array("" . $return_text);
                    return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
                }
                $post_dob = $request->dob;
            } else {
                $post_dob = NULL;
            }
            if ($request->txt_age < 60) {
                $return_text = 'Age Invalid';
                $return_msg = array("" . $return_text);
                return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
            }
            $post_aadhar_no = $request->aadhar_no;
            if ($this->isAadharValid($post_aadhar_no) == false) {
                $return_text = 'Aadhaar Number Invalid';
                $return_msg = array("" . $return_text);
                return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
            }
            if(!preg_match('/^[0-9]{10}+$/',$request->mobile_no)) {
                $return_text = 'Mobile Number Invalid';
                $return_msg = array("" . $return_text);
                return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
             }
             if($request->mobile_no<1000000000) {
                $return_text = 'Mobile Number Invalid';
                $return_msg = array("" . $return_text);
                return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
             }
            $district_list = District::all();
            $sel_district = $request->district;
            $cnt = $district_list->where('district_code', $sel_district)->count();
            if ($cnt == 0) {
                $return_text = 'District Invalid';
                $return_msg = array("" . $return_text);
                return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
            }
            $asmb_cons = $request->asmb_cons;
            $assembly_arr = Assembly::where('district_code', $sel_district)->where('ac_no', $asmb_cons)->first();
            if (empty($assembly_arr)) {
                $return_text = 'Assembly Invalid';
                $return_msg = array("" . $return_text);
                return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
            }
            $assembly_name = $assembly_arr->ac_name;
            $sel_urban_code = $request->urban_code;
            $sel_block = $request->block;
            $sel_gp_ward = $request->gp_ward;
            if ($sel_urban_code == 1) {
                $block_munc_arr = UrbanBody::where('district_code', $sel_district)->where('urban_body_code', $sel_block)->first();
                if (empty($block_munc_arr)) {
                    $return_text = 'Block/Municipality/Corp Invalid';
                    $return_msg = array("" . $return_text);
                    return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
                }
                $block_ulb_name = $block_munc_arr->urban_body_name;
                $gp_ward_arr = Ward::where('urban_body_code', $sel_block)->where('urban_body_ward_code', $sel_gp_ward)->first();
                if (empty($gp_ward_arr)) {
                    $return_text = 'GP/Ward Not Invalid';
                    $return_msg = array("" . $return_text);
                    return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
                }
                $gp_ward_name   = $gp_ward_arr->urban_body_ward_name;
            } else if ($sel_urban_code == 2) {
                $block_munc_arr = Taluka::where('district_code', $sel_district)->where('block_code', $sel_block)->first();
                if (empty($block_munc_arr)) {
                    $return_text = 'Block/Municipality/Corp Invalid';
                    $return_msg = array("" . $return_text);
                    return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
                }
                $block_ulb_name = $block_munc_arr->block_name;
                $gp_ward_arr = GP::where('block_code', $sel_block)->where('gram_panchyat_code', $sel_gp_ward)->first();
                if (empty($gp_ward_arr)) {
                    $return_text = 'GP/Ward Not Invalid';
                    $return_msg = array("" . $return_text);
                    return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
                }
                $gp_ward_name   = $gp_ward_arr->gram_panchyat_name;
            }
            $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->get();
            $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
            $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
            $doc_list = array_merge($doc_list_man, $doc_list_opt);
            $encolserdata = BenDocs::where('scheme_id',$scheme_id)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->get();
            $already_id=array();
            foreach($encolserdata as $enc_item){
                array_push($already_id,$enc_item->document_type);
            }         
            $doc_list_man_group_upload = array();
            $doc_list_man_group_db = array();


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
                } else {
                    if (in_array($doc, $already_id)) {
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
                    return redirect("/editOap/" . $request->id)->with('errors', $errors)->withInput(Input::all());
            }
            $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
            if(!empty($request->aadhar_no)){
                $count_aadhar = PensionOAPWCD::where('aadhar_no', trim($request->aadhar_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
                if ($count_aadhar > 0) {
                    $errors = array();
                    $request->session()->put('dupAadhaarCheck', trim($request->aadhar_no));
                    $errorMsg = "Aadhaar Number Already Exist! Please try different.";
                    array_push($errors, $errorMsg);
                    return redirect("/editOap/" . $request->id)->with('errors', $errors)->withInput(Input::all());

                }
            }
            if(!empty($request->mobile_no)){
                $count_mobile = PensionOAPWCD::where('mobile_no', $request->mobile_no)->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
                if ($count_mobile > 0) {
                    $errors = array();
                    $errorMsg = "Mobile Number Already Exist! Please try different.";
                    array_push($errors, $errorMsg);
                    return redirect("/editOap/" . $request->id)->with('errors', $errors)->withInput(Input::all());
                }
            }
        
            $social_security_pension = "";
            $receive_pension = "";
            if ($request->receive_pension != "") {
                $receive_pension = implode(',', $request->receive_pension);
            }

            if ($request->social_security_pension != "") {
                $social_security_pension = implode(',', $request->social_security_pension);
            }

            $input = [
                'unlock_status' => 1,
                'next_level_role_id_edit' => 999,
                'gender' => trim($request->gender),
                'dob' => $post_dob,
                'ben_age' => $request->txt_age,

                'father_fname' => trim($request->father_first_name),
                'father_mname' => trim($request->father_middle_name),
                'father_lname' => trim($request->father_last_name),
                'mother_fname' => trim($request->mother_first_name),
                'mother_mname' => trim($request->mother_middle_name),
                'mother_lname' => trim($request->mother_last_name),
                'caste' => trim($request->caste_category),
                'marital_status' => trim($request->marital_status),
                'spouse_fname' => trim($request->spouse_first_name),
                'spouse_mname' => trim($request->spouse_middle_name),
                'spouse_lname' => trim($request->spouse_last_name),
                //'bpl_y_n' =>$request->if_bpl,
                'bpl_seq_no' => trim($request->bpl_seq_no),
                'bpl_id_no' => trim($request->bpl_id_no),
                'bpl_total_score' => intval($request->bpl_total_score),
                'mothly_income' => trim($request->monthly_income),

                'receive_pension' => trim($receive_pension),
                'social_security_pension' => trim($social_security_pension),

                'ration_card_cat' => trim($request->ration_card_cat),
                'ration_card_no'  => trim($request->ration_card_no),
                'ahl_tin'  => trim($request->ahl_tin),
                'epic_voter_id'  => trim($request->epic_voter_id),
                'pan_no'  => trim($request->pan_no),



                'dist_code' => $request->district,
                'assembly_code'  => $request->asmb_cons,
                'assembly_name' => trim($assembly_name),
                'rural_urban_id' => $request->urban_code,
                'police_station'  => trim($request->police_station),
                'block_ulb_code'  => $request->block,
                'block_ulb_name' => trim($block_ulb_name),
                'gp_ward_code' => $request->gp_ward,
                'gp_ward_name' => trim($gp_ward_name),
                'village_town_city'  => trim($request->village),
                'house_premise_no'  => trim($request->house),
                'post_office'  => trim($request->post_office),
                'pincode' => trim($request->pin_code),
                'residency_period' => $request->residency_period,
                'email' => trim($request->email),




                'nominate_name' => trim($request->nominate_name),
                'nominate_address' => trim($request->nominate_address),
                'nominate_relationship' => trim($request->nominate_relationship),
                'av_status' => trim($request->av_status),
                'receiving_pension_other_source_1' => trim($request->receiving_pension_other_source_1),
                'receiving_pension_other_source_2' => trim($request->receiving_pension_other_source_2),
                'is_clean' =>1,
            ];
           
            if($row->no_aadhar==1 || $row->no_mobile==1){
                $input['no_aadhar_mobile_flag'] =1;
            }
            

            if(!empty(trim($request->caste_certificate_no))){
                $input['caste_certificate_no']=$request->caste_certificate_no;
            }

            if(!empty(trim($request->aadhar_no))){
                if(trim($row->aadhar_no)==trim($request->aadhar_no)){
                 $sp_aadhar_new=NULL;
                 $sp_aadhar_old=NULL;
                }
                else{
                $input['aadhar_no']=trim($request->aadhar_no);
                $sp_aadhar_new=trim($request->aadhar_no);
                if(empty(trim($row->aadhar_no))){
                    $sp_aadhar_old=NULL;
                 }
                 else{
                    $sp_aadhar_old=trim($row->aadhar_no);
                 }
                }
            }
            if(!empty(trim($request->mobile_no))){
                if($row->mobile_no==trim($request->mobile_no)){
                 $sp_mobile_new=0;
                 $sp_mobile_old=0;
                }
                else{
                $input['mobile_no']=trim($request->mobile_no);
                $sp_mobile_new=trim($request->mobile_no);
                if(empty(trim($row->mobile_no))){
                    $sp_mobile_old=0;
                 }
                 else{
                    $sp_mobile_old=$row->mobile_no;
                 }
                }
            }
            
            $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
            if (!empty($scheme_obj->short_code)) {
                $schema = $scheme_obj->short_code;
            } else {
                $schema = "pension"; 
            }
            $uploaded_doc = array();
            $base_url = url('/');
            $encloser_list = array();
            $i = 0;
            $c_time = date('Y-m-d H:i:s', time());
            $all_document=DocumentType::where('is_active', TRUE)->get();
            $delete_array=array();
            $j=0;
            $upload_file_arch=array();
            $upload_file=array();
            foreach ($doc_list as $doc) {
                if ($request->hasFile('doc_' . $doc)) {
                $doc_type_name =$all_document->where('id', $doc)->first() ;
                $doc_file = $request->file('doc_' . $doc);
                $img_data = file_get_contents($doc_file);
                $u_extension_file = $doc_file->getClientOriginalExtension();
                $u_extension=strtolower($u_extension_file);
                $mime_type = $doc_file->getMimeType();
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
                $upload_file[$i]['created_by_dist_code']=$distCode;
                $upload_file[$i]['created_by_local_body_code']=$blockCode;
                $upload_file[$i]['document_type']=$doc;
                $upload_file[$i]['scheme_id']=$scheme_id;
                $upload_file[$i]['created_by_level']=$level;
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
                $doc_already_edit =$encolserdata->where('document_type',$doc)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->first();
                if (in_array($doc, $already_id)) {
                    array_push($delete_array,$doc);
                    $upload_file_arch[$j]['beneficiary_id']=$request->id;
                    $upload_file_arch[$j]['created_by_dist_code']=$doc_already_edit->created_by_dist_code;
                    $upload_file_arch[$j]['created_by_local_body_code']=$doc_already_edit->created_by_local_body_code;
                    $upload_file_arch[$j]['document_type']=$doc_already_edit->document_type;
                    $upload_file_arch[$j]['scheme_id']=$doc_already_edit->scheme_id;
                    $upload_file_arch[$j]['created_by_level']=$doc_already_edit->created_by_level;
                    $upload_file_arch[$j]['created_at']=$doc_already_edit->created_at;
                    $upload_file_arch[$j]['created_by']=$doc_already_edit->created_by;
                    $upload_file_arch[$j]['ip_address']=$doc_already_edit->ip_address;
                    $upload_file_arch[$j]['attched_document']=$doc_already_edit->attched_document;
                    $upload_file_arch[$j]['document_mime_type']=$doc_already_edit->document_mime_type;
                    $upload_file_arch[$j]['document_extension']=$doc_already_edit->document_extension;
                    $j++;
                }
               }
            }
            //dump($upload_file);dump($delete_array);dd($upload_file_arch);
            $dup_blank_dup_arr=array();
            $dup_blank_dup_arr['action_on']=$c_time;
            if(trim($request->mobile_no)==$row->mobile_no && trim($request->aadhar_no)==trim($row->aadhar_no)){
                $dup_blank_dup_arr['legacy_update']=1;
            }
            $row_dup_blank=BeneficiaryDupBlank::where('id',$id)->first();
            $document_type_list = BenDocs::select('document_type', 'attched_document')->where('scheme_id',$scheme_id)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->get();
            //dd($document_type_list);
            DB::beginTransaction();
            DB::connection('pgsql_encwrite')->beginTransaction();
            try {
                           
                            $is_inserted_status=1;
                           
                            if($is_inserted_status==1){
                                //dd('ok');
                                $arch_status=DB::statement("INSERT INTO oap_wcd.arc_beneficiary(id, 
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
                               receiving_pension_other_source_1, receiving_pension_other_source_2 from oap_wcd.beneficiary where id=" . $id . ")");
                               if($arch_status)
                               {
                                $main_update=DB::table($schema . '.beneficiary')->where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id])->update($input);
                                if($main_update){
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
                                    
                                    
                                    
                                    if($doc_inserted_arch==1 && $doc_inserted_del && $doc_inserted==1){
                                        if(!empty($row_dup_blank)){
                                            $dup_blank_dup_arr['new_mobile_no']=trim($request->new_mobile_no);
                                            $dup_blank_dup_arr['no_mobile_update']=1;
                                            $dup_blank_dup_arr['new_aadhar_no']=trim($request->aadhar_no);
                                            $dup_blank_dup_arr['no_aadhar_update']=1;
                                            $dup_blank_dup_update=BeneficiaryDupBlank::where('id',$row->id)->update($dup_blank_dup_arr);
                                            
                                     }
                                     else{
                                        $dup_blank_dup_update=1;
                                     }
                                     if($dup_blank_dup_update){
                                        $accept_reject_model = new AcceptRejectInfo;
                                        $accept_reject_model->created_at = $c_time;
                                        $accept_reject_model->application_id = $row->id;
                                        $accept_reject_model->scheme_id =  $row->scheme_id;
                                        $accept_reject_model->user_id = $user_id;
                                        $accept_reject_model->user_id = $user_id;
                                        $accept_reject_model->created_by_dist_code = $distCode;
                                        $accept_reject_model->created_by_local_body_code = $blockCode;
                                        $accept_reject_model->op_type = 'SM';
                                        $is_saved_log = $accept_reject_model->save();
                                        if($is_saved_log){
                                            DB::commit();
                                            DB::connection('pgsql_encwrite')->commit();
                                            $return_text = 'Beneficiary Edited Successfully';
                                            return redirect("/editOapList")->with('success', $return_text)->with('id', $row->getBenidAttribute());
                                        }
                                        else{
                                            DB::rollback();
                                            DB::connection('pgsql_encwrite')->rollback();
                                           // dd('ok77777');
                                            $return_text = 'Error. Please try again';
                                            $return_msg = array("" . $return_text);
                                            return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());   
                                        }
                                    }
                                    else{
                                        DB::rollback();
                                        DB::connection('pgsql_encwrite')->rollback();
                                        //dd('ok778');
                                        $return_text = 'Error. Please try again';
                                        $return_msg = array("" . $return_text);
                                        return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());   
                                    }
                                }
                                    else{
                                        DB::rollback();
                                        DB::connection('pgsql_encwrite')->rollback();
                                       // dd('ok778ggg');
                                        $return_text = 'Error. Please try again';
                                        $return_msg = array("" . $return_text);
                                        return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());   
                                    }
                                }
                                else{
                                    DB::rollback();
                                    DB::connection('pgsql_encwrite')->rollback();
                                    $return_text = 'Error. Please try again';
                                    $return_msg = array("" . $return_text);
                                    return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());   
                                }
                               }
                               else{
                                DB::rollback();
                                DB::connection('pgsql_encwrite')->rollback();
                                $return_text = 'Error. Please try again';
                                $return_msg = array("" . $return_text);
                                return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());  
                               }
                           
                            }                 
            else if($is_inserted_status==2){
                // dd('ok3');
                 DB::rollback();
                 DB::connection('pgsql_encwrite')->rollback();
                 $return_text = 'Duplicate Bank Info.. Please try different.';
                 $return_msg = array("" . $return_text);
                 return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());  

             }
            else if($is_inserted_status==3){
                // dd('ok3');
                 DB::rollback();
                 DB::connection('pgsql_encwrite')->rollback();
                 $return_text = 'Duplicate Bank Modification Failed.Please try again.';
                 $return_msg = array("" . $return_text);
                 return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());  

             }
                            else if($is_inserted_status==4){
                               // dd('ok3');
                                DB::rollback();
                                DB::connection('pgsql_encwrite')->rollback();
                                $return_text = 'Duplicate Aadhar Number.. Please try different.';
                                $return_msg = array("" . $return_text);
                                return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());  

                            }
                            else if($is_inserted_status==5){
                                //dd('ok3');
                                DB::rollback();
                                DB::connection('pgsql_encwrite')->rollback();
                                $return_text = 'Aadhar Number Modification Faild.. Please try different.';
                                $return_msg = array("" . $return_text);
                                return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());  

                            }
                            else if($is_inserted_status==6){
                                //dd('ok4');
                                DB::rollback();
                                DB::connection('pgsql_encwrite')->rollback();
                                $return_text = 'Duplicate Mobile Number.. Please try different.';
                                $return_msg = array("" . $return_text);
                                return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());  
                           }
                           else if($is_inserted_status==7){
                            //dd('ok4');
                            DB::rollback();
                            DB::connection('pgsql_encwrite')->rollback();
                            $return_text = 'Mobile Number Modification Faild.. Please try different.';
                            $return_msg = array("" . $return_text);
                            return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());  
                          }
                           else if($is_inserted_status==8){
                            //dd('ok5');
                            DB::rollback();
                            DB::connection('pgsql_encwrite')->rollback();
                            $return_text = 'Error. Please try again';
                            $return_msg = array("" . $return_text);
                            return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());  
                          }
                          else{
                           // dd('ok6');
                          }
            }
            catch (\Exception $e) {
                        //dd($e);
                        DB::rollback();
                        DB::connection('pgsql12')->rollback();
                        $return_text = 'Error. Please try again';
                        $return_msg = array("" . $return_text);
                        return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());  
            }
            
        }     
     
               
        else {
            $return_msg = $validator->errors()->all();
            return redirect("/editOap/" . $request->id)->with('errors', $return_msg)->withInput(Input::all());
        }
    }
    public function dsphaseVIIIMark(Request $request){
        $scheme_id=trim($request->scheme_id);
        if($scheme_id==''){
            $errormsg=array();
            array_push($errormsg,'Scheme Code Not Found');    
            return redirect("/")->with('errors',$errormsg);
        }
        if($scheme_id==2){
            $url='manabik-wcd';
        } else if($scheme_id==10){
            $url='oap-wcd';
        } else if($scheme_id==11){
            $url='wp-wcd';
        }
        else{
            $errormsg=array();
            array_push($errormsg,'Scheme Code Not Found');    
            return redirect("/")->with('errors',$errormsg);
        }
        
        $bank_code=trim($request->bank_code);
        $aadhar_no=trim($request->aadhar_no);
        $mobile_no=trim($request->mobile_no);
        if($aadhar_no=='' && $bank_code=='' && $mobile_no==''){
            $errormsg=array();
            array_push($errormsg,'Neither Bank account number and nor Aadhaar number found');    
            return redirect($url)->with('errors',$errormsg);

        }
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (!empty($scheme_obj->short_code)) {
            $schema = $scheme_obj->short_code;
          } else {
            $schema = "pension"; 
          }
          $in_bank_code=''; 
          $in_aadhar_no=''; 
          $in_mobile_no=0;
          $user_id = AuthChecker::getUserId();

          $duty = Configduty::where('user_id',$user_id)->where('scheme_id',$scheme_id)->where('is_active', 1)->first();
          $district_code = $duty->district_code;

        if(!empty($bank_code)){
          $bank_count = DB::table($schema . '.ben_bank_account_no_unique')->where('bank_code',$bank_code)->count('bank_code');
            if($bank_count>0){  
                $in_bank_code=$bank_code;  
            }
            else{
                $in_bank_code='';  
 
            }
        }
        else if(!empty($aadhar_no)){
            $aadhar_count = DB::table($schema . '.ben_aadhar_no_unique')->where('aadhar_no',trim($request->aadhar_no))->count('aadhar_no');
            if($aadhar_count>0){ 
            $in_aadhar_no=$aadhar_no;  
            }
            else{
                $in_aadhar_no=''; 
            }
        }
        if(!empty($mobile_no)){
            $mobile_count = DB::table($schema . '.ben_mobile_no_unique')->where('mobile_no',$request->mobile_no)->count('mobile_no');
            if($mobile_count>0){ 
            $in_mobile_no=$mobile_no;  
            }
            else{
                $in_mobile_no=0;   
            }
        }
        if($in_bank_code=='' && $in_aadhar_no=='' && $in_mobile_no==0){
            $errormsg=array();
            array_push($errormsg,'Neither Bank account number and nor Aadhaar number found');    
            return redirect($url)->with('errors',$errormsg);

        }
        try {
            DB::beginTransaction();
            $mark_ds_phase_viii_arr = DB::select("select oap_wcd.mark_ds_phase_viii(
                in_created_by_dist_code => " . $district_code . "
                in_bank_code => '" . $in_bank_code . "', 
                in_aadhar_no => '" . $in_aadhar_no . "', 
                in_mobile_no => " . $in_mobile_no . "
                )");
                $mark_ds_phase_viii = $mark_ds_phase_viii_arr[0]->mark_ds_phase_viii;
                if ($mark_ds_phase_viii==1) {
                    DB::commit();
                    return redirect($url)->with('message', 'Application has been Succesfully marked!');
                  } else {
                    DB::rollback();
                    return redirect($url)->with('error', 'Error! Please try again...');
                  }

        }catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            $errormsg=array();
            array_push($errormsg,'Error! Please try again....');    
            return redirect($url)->with('errors',$errormsg);          
        }
       

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
}
