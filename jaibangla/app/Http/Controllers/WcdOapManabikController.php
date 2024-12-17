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
use App\PensionManabikWCD;
use App\PensionOAPWCD;
use App\PensionWPWCD;
//Dynamic Doc
use App\BenDocsManabikWCD;
use App\BenDocsArcManabikWCD;
use App\DocumentType;
use App\SchemeDocMap;
//Dynamic Doc End
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\BankDetails;
use App\User;
use Redirect;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Validator;
use DateTime;
use App\Scheme;

class WcdOapManabikController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$this->middleware('auth');
        $flag_check = $this->flag_check();
        if ($flag_check['suspended'])
            return redirect("/")->with('error', 'Data entry temporary suspended.');

        if ($flag_check['withquota']) {
            $data = $this->get_oap_wp_data();
            if ((($data['oap_approved'] + $data['oap_pending']) >= $data['oap_quota']) && (($data['wp_approved'] + $data['wp_pending']) >= $data['wp_quota'])) {
                return redirect("/")->with('error', 'Quota has been exceed for Both OAP and WP.');
            } else {
                $oap_visible = 1;
                $wp_visible = 1;
                if (($data['oap_approved'] + $data['oap_pending']) >= $data['oap_quota']) {
                    $oap_visible = 0;
                }
                if (($data['wp_approved'] + $data['wp_pending']) >= $data['wp_quota']) {
                    $wp_visible = 0;
                }
            }
        } else {
            $oap_visible = 1;
            $wp_visible = 1;
            $data = array();
        }
        $districts = District::all();
        return view('WCDOAPMANABIK/pension_details', [
            'districts' => $districts,
            'data' => $data,
            'oap_visible' => $oap_visible,
            'wp_visible' => $wp_visible
        ]);
        //return redirect("/")->with('success', 'Employee with Application ID:' . $id . ' is verified');
    }
    public function store(Request $request)
    {
        $flag_check = $this->flag_check();
        if ($flag_check['suspended'])
            return redirect("/")->with('error', 'Data entry temporary suspended.');
        $this->validateInput($request);
        $type_of_penstion = $request->type_of_penstion;
        $scheme_id = 0;
        if ($type_of_penstion == 1) {
            $scheme_id = 10;
            $pension_details = new PensionOAPWCD();
            $schema_name = "oap_wcd";
            $scheme_name = "OAP";
        } else if ($type_of_penstion == 3) {
            $scheme_id = 11;
            $pension_details = new PensionWPWCD();
            $schema_name = "wp_wcd";
            $scheme_name = "WP";
        } else {
            return redirect("/")->with('success', 'Scheme Not Valid');
        }

        $is_active = 0;
        $roleArray = $request->session()->get('role');

        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $mapping_level = $roleObj['mapping_level'];
                $district_code = $roleObj['district_code'];
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                } else {
                    $blockCode = $roleObj['taluka_code'];
                }
                break;
            }
        }

        if ($is_active == 0) {
            return redirect("/")->with('success', 'User Disabled. Please contact approver.');
        }
        if ($flag_check['withquota']) {
            $data = $this->get_oap_or_wp_data($scheme_id, $district_code);
            if (($data['approved'] + $data['pending']) >= $data['quota']) {
                return redirect("wcd_oap_manabik")->with('error', 'Quota has been exceed for ' . $scheme_name);
            }
        }

        $dob = $request->dob;
        $date_diff = 0;
        if (!empty($dob)) {
            $d1 = new DateTime('2020-01-01');
            $d2 = new DateTime($dob);
            $diff = $d2->diff($d1);
            $date_diff = $diff->y;
            if (($date_diff < 60) && ($scheme_id == 10)) {
                $crud_status = 'error';
                return redirect("WcdOapManabikController")->with('error', 'DOB not Valid') > withInput(Input::all());
                // echo $crud_msg;die;
            } else {
                // echo "ok";die;
            }
        }
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
        if (!empty($request->mobile_no)) {
            $mobile_no = $request->mobile_no;
        } else {
            $mobile_no = "1000000000";
        }
        if (!empty($request->dob)) {
            $dob = $request->dob;
            $hidden_age = $request->hidden_age;
        } else {
            $dob = NULL;
            $hidden_age = NULL;
        }
        //$pension_details->pensioner_type = $request->quota_portal;
        $pension_details->ben_fname = $request->first_name;
        $pension_details->ben_mname = $request->middle_name;
        $pension_details->ben_lname = $request->last_name;
        $pension_details->mobile_no  = $mobile_no;
        $pension_details->ben_age = intval($date_diff);
        $pension_details->dob = $dob;
        $pension_details->dist_code       =      $request->district;
        $pension_details->rural_urban_id     =      $request->urban_code;
        $pension_details->block_ulb_code  = $request->block;
        $pension_details->gp_ward_code = $request->gp_ward;

        $pension_details->created_by = Auth::user()->id;
        $pension_details->created_by_level = $mapping_level;
        $pension_details->created_by_dist_code = $district_code;
        $pension_details->created_by_local_body_code = $blockCode;
        if (empty($blockCode))
            $pension_details->created_by_local_body_code = $request->block;
        $pension_details->scheme_id =  $scheme_id;
        $pension_details->legacy_import = true;

        $pension_details->bank_ifsc = $request->bank_ifsc_code;
        $pension_details->bank_name = $request->name_of_bank;
        $pension_details->branch_name = $request->bank_branch;
        $pension_details->bank_code = $request->bank_account_number;
        $pension_details->ben_age = $request->txt_age;
        try {
            $is_saved = $pension_details->save();
            if ($is_saved) {
                $id = $pension_details->benid;
                return redirect("wcd_oap_manabik")->with('success', 'Application Submitted Successfully')
                    ->with('id',  $id);
            } else {

                return redirect("wcd_oap_manabik")->with('error', 'Something wrong please try again.');
            }
        } catch (\Exception $e) {

            return redirect("wcd_oap_manabik")->with('error', 'Something wrong please try again.');
        }
    }
    private function validateInput($request)
    {
        $singleArray = array();
        $nicenameArray = array();
        $customMessage = array();
        $nicenameArray['type_of_penstion'] = 'Type of Pension';
        $nicenameArray['quota_portal'] = 'Quota/ Portal';
        $nicenameArray['first_name'] = 'First Name';
        $nicenameArray['middle_name'] = 'Middle Name';
        $nicenameArray['last_name'] = 'Last Name';
        $nicenameArray['mobile_no'] = 'Mobile Number';
        $nicenameArray['district'] = 'District';
        $nicenameArray['urban_code'] = 'Rural/ Urban';
        $nicenameArray['block'] = 'Block/Municipality/Corp';
        $nicenameArray['gp_ward'] = 'GP/Ward No';
        $nicenameArray['bank_ifsc_code'] = 'IFS Code';
        $nicenameArray['name_of_bank'] = 'Bank Name';
        $nicenameArray['bank_branch'] = 'Bank Branch Name';
        $nicenameArray['bank_account_number'] = 'Bank Account Number';
        $this->validate($request, array_merge([
            //'first_name' => 'required|string|max:200',
            'type_of_penstion' => 'required|in:1,2,3',
            'first_name' => 'required|string|max:200',
            'middle_name' => 'string|nullable',
            'last_name' => 'required|string|max:200',
            'district' => 'required|numeric',
            'urban_code' => 'required|in:1,2',
            'block' => 'required|numeric',
            'gp_ward' => 'required|numeric',
            'name_of_bank' => 'required|string|max:200',
            'bank_branch' => 'required|string|max:200',
            'bank_account_number' => 'required|numeric',
            'bank_ifsc_code' => 'required|string'

        ], $singleArray), $customMessage, $nicenameArray);
    }

    public function bankAccountedit(Request $request)
    {
        $flag_check = $this->flag_check();
        if ($flag_check['suspended'])
            return redirect("/")->with('error', 'Data entry temporary suspended.');
        if ($flag_check['withquota']) {
            $data = $this->get_oap_wp_data();
            if ((($data['oap_approved'] + $data['oap_pending']) >= $data['oap_quota']) && (($data['wp_approved'] + $data['wp_pending']) >= $data['wp_quota'])) {
                return redirect("/")->with('error', 'Quota has been exceed for Both OAP and WP.');
            } else {
                $oap_visible = 1;
                $wp_visible = 1;
                if (($data['oap_approved'] + $data['oap_pending']) >= $data['oap_quota']) {
                    $oap_visible = 0;
                }
                if (($data['wp_approved'] + $data['wp_pending']) >= $data['wp_quota']) {
                    $wp_visible = 0;
                }
            }
        } else {
            $oap_visible = 1;
            $wp_visible = 1;
        }
        $districts = District::all();
        return view('WCDOAPMANABIK/bankAccountEdit', [
            'districts' => $districts,
            'oap_visible' => $oap_visible,
            'wp_visible' => $wp_visible
        ]);
    }

    public function bankAccounteditApplicantSearch(Request $request)
    {
        $flag_check = $this->flag_check();
        $flag_check['suspended'] = 0;
        if ($flag_check['suspended']) {
            $return_status = 0;
            $applicant_row = array();
            $return_text = "Data entry temporary suspended.";
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg, 'applicant_row' => $applicant_row]);
        }
        $applicant_row = array();
        $rules = array(
            'type_of_penstion' => 'required|in:1,2,3',
            'applicant_id' => 'required|numeric',
        );
        $attributes = [
            'type_of_penstion' => 'Type of Pension',
            'applicant_id' => 'Applicant Id',
        ];
        $messages = [
            'required' => 'The :attribute field is required.',
            'numeric' => 'Only integer allowed for :attribute',
            'in' => 'The :attribute field not valid.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $type_of_penstion = $request->type_of_penstion;
            if ($type_of_penstion == 1) {
                $scheme_id = 10;
                $pension_details = 'App\\PensionOAPWCD';
            } else if ($type_of_penstion == 2) {
                $scheme_id = 2;
                $pension_details = 'App\\PensionManabikWCD';
            } else if ($type_of_penstion == 3) {
                $scheme_id = 11;
                $pension_details = 'App\\PensionWPWCD';
            }
            $is_active = 0;
            $roleArray = $request->session()->get('role');
            //dump($roleArray);
            //dump($request->urban_code);
            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == $scheme_id) {
                    $is_active = 1;
                    $mapping_level = $roleObj['mapping_level'];
                    $district_code = $roleObj['district_code'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }
            // dump($mapping_level);
            // dump($district_code);
            // dd($blockCode);
            if ($is_active == 0) {
                $return_status = 0;
                $return_text = "You are not allowed to do this operation";
                $return_msg = array("" . $return_text);
            } else {
                $applicant_id = $request->applicant_id;
                $ben_id = substr($applicant_id, -7);
                try {
                    $row = $pension_details::where('id', $ben_id)
                        ->where('created_by_dist_code', '=', $district_code)
                        ->where('legacy_import', true)
                        ->first();
                    $return_status = 1;
                    $applicant_row['first_name'] = $row->ben_fname;
                    $applicant_row['middle_name'] = $row->ben_mname;
                    $applicant_row['last_name'] = $row->ben_lname;
                    $applicant_row['mobile_no'] = $row->mobile_no;
                    $applicant_row['dob'] = $row->dob;
                    $applicant_row['ben_age'] = $row->ben_age;
                    $applicant_row['district'] = $row->dist_code;
                    $applicant_row['urban_code'] = $row->rural_urban_id;
                    $applicant_row['block'] = $row->block_ulb_code;
                    $applicant_row['gp_ward'] = $row->gp_ward_code;
                    $applicant_row['bank_ifsc_code'] = $row->bank_ifsc;
                    $applicant_row['name_of_bank'] = $row->name_of_bank;
                    $applicant_row['bank_branch'] = $row->bank_branch;
                    $applicant_row['bank_account_number'] = $row->bank_code;
                    $return_text = "Applicant found";
                    $return_msg = array("" . $return_text);
                } catch (\Exception $e) {
                    $return_status = 0;
                    $return_text = "Applicant not found";
                    $return_msg = array("" . $return_text);
                }
            }
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }
        return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg, 'applicant_row' => $applicant_row]);
    }
    public function bankAccounteditApplicantEdit(Request $request)
    {
        $flag_check = $this->flag_check();
        if ($flag_check['suspended']) {
            $return_status = 0;
            $return_text = "Data entry temporary suspended.";
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        $is_active = 0;
        $blockCode = NULL;
        $roleArray = $request->session()->get('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == 10 || $roleObj['scheme_id'] == 11) {
                $district_code = $roleObj['district_code'];
                $is_active = 1;
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                } else {
                    $blockCode = $roleObj['taluka_code'];
                }
                break;
            } else {
                continue;
            }
        }
        $type_of_penstion = $request->type_of_penstion;
        $scheme_id = 0;
        if ($type_of_penstion == 1) {
            $scheme_id = 10;
            $pension_details = new PensionOAPWCD();
            $schema_name = "oap_wcd";
            $scheme_name = "OAP";
        } else if ($type_of_penstion == 3) {
            $scheme_id = 11;
            $pension_details = new PensionWPWCD();
            $schema_name = "wp_wcd";
            $scheme_name = "WP";
        } else {
            return redirect("/")->with('success', 'Scheme Not Valid');
        }
        if ($flag_check['withquota']) {
            $data = $this->get_oap_or_wp_data($scheme_id, $district_code);
            if (($data['approved'] + $data['pending']) >= $data['quota']) {
                $return_status = 0;
                $return_text = 'Quota has been exceed for ' . $scheme_name;
                $return_msg = array("" . $return_text);
                return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
                //return redirect("/")->with('error', 'Quota has been exceed for ' . $scheme_name);
            }
        }



        $rules = [
            'type_of_penstion' => 'required|in:1,2,3',
            'first_name' => 'required|string|max:200',
            'middle_name' => 'string|nullable',
            'last_name' => 'required|string|max:200',
            'district' => 'required|numeric',
            'urban_code' => 'required|in:1,2',
            'block' => 'required|numeric',
            'gp_ward' => 'required|numeric',
            'bank_account_number' => 'required|numeric',
            'bank_ifsc_code' => 'required|string'
        ];
        $attributes = array();
        $messages = array();
        $attributes['type_of_penstion'] = 'Type of Pension';
        $attributes['first_name'] = 'First Name';
        $attributes['middle_name'] = 'Middle Name';
        $attributes['last_name'] = 'Last Name';
        $attributes['mobile_no'] = 'Mobile Number';
        $attributes['district'] = 'District';
        $attributes['urban_code'] = 'Rural/ Urban';
        $attributes['block'] = 'Block/Municipality/Corp';
        $attributes['gp_ward'] = 'GP/Ward No';
        $attributes['bank_ifsc_code'] = 'IFS Code';
        $attributes['bank_account_number'] = 'Bank Account Number';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $bank_ifsc_code = $request->bank_ifsc_code;
            $bank_account_number = $request->bank_account_number;
            $bank_details = BankDetails::where('ifsc', $bank_ifsc_code)->get(['bank', 'branch'])->first();
            if (!empty($bank_details)) {
                $bank_name = $bank_details->bank;
                $bank_branch = $bank_details->branch;

                if ($is_active == 0) {
                    $return_status = 0;
                    $return_text = "You are not allowed to do this operation";
                    $return_msg = array("" . $return_text);
                } else {
                    $applicant_id = $request->applicant_id;
                    $ben_id = substr($applicant_id, -7);

                    try {
                        $row = $pension_details::where('id', $ben_id)
                            ->where('created_by_dist_code', '=', $district_code)
                            ->where('created_by_local_body_code', '=', $blockCode)
                            ->where('legacy_import', true)
                            ->first();
                        $return_status = 1;
                        $return_text = "Applicant found";
                        $return_msg = array("" . $return_text);
                        $first_name = $request->first_name;
                        $middle_name = $request->middle_name;
                        $last_name = $request->last_name;
                        $mobile_no = $request->mobile_no;
                        $district = $request->district;
                        $urban_code = $request->urban_code;
                        $block = $request->block;
                        $gp_ward = $request->gp_ward;
                        $dob = $request->dob;
                        $dob_valid = 0;
                        if (!empty($dob)) {
                            $d1 = new DateTime('2020-01-01');
                            $d2 = new DateTime($dob);
                            $diff = $d2->diff($d1);
                            $date_diff = $diff->y;
                            if ($scheme_id == 10 && $date_diff < 60) {
                                $dob_valid = 0;
                            } else {
                                if ($dob > '2020-01-01') {
                                    $dob_valid = 0;
                                } else
                                    $dob_valid = 1;
                            }
                        } else {
                            $dob = NULL;
                            $date_diff = $request->txt_age;
                            if (!empty($date_diff)) {
                                if ($date_diff < 0) {
                                    $dob_valid = 0;
                                } else {
                                    if ($scheme_id == 10 && $date_diff < 60) {
                                        $dob_valid = 0;
                                    } else
                                        $dob_valid = 1;
                                }
                            } else {
                                $dob_valid = 1;
                            }
                        }
                        if ($dob_valid) {
                            if ($request->urban_code == 1) {
                                $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
                                $gp_ward_arr = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();
                                $n_created_by_local_body_code = $block_ulb->sub_district_code;
                                $block_ulb_name = $block_ulb->urban_body_name;
                                $gp_ward_name   = $gp_ward_arr->urban_body_ward_name;
                            } else {
                                $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
                                $gp_ward_arr = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();
                                $block_ulb_name = $block_ulb->block_name;
                                $gp_ward_name   = $gp_ward_arr->gram_panchyat_name;
                                $n_created_by_local_body_code = $request->block;
                            }
                            if (!empty($request->mobile_no)) {
                                $mobile_no = $request->mobile_no;
                            } else {
                                $mobile_no = "1000000000";
                            }
                            if ($row->created_by_dist_code == $district) {
                                $u_created_by_dist_code = $row->created_by_dist_code;
                            } else {
                                $u_created_by_dist_code = $district;
                            }
                            if ($row->created_by_local_body_code == $n_created_by_local_body_code) {
                                $u_created_by_local_body_code = $row->created_by_local_body_code;
                            } else {
                                $u_created_by_local_body_code = $n_created_by_local_body_code;
                            }
                            $input = [
                                'ben_fname' => $first_name,
                                'ben_mname' => $middle_name,
                                'ben_lname' => $last_name,
                                'mobile_no' => $mobile_no,
                                'dist_code' => $district,
                                'rural_urban_id' => $urban_code,
                                'block_ulb_code' => $block,
                                'block_ulb_name' => $block_ulb_name,
                                'gp_ward_code' => $gp_ward,
                                'gp_ward_name' => $gp_ward_name,
                                'bank_name' => $bank_name,
                                'branch_name' => $bank_branch,
                                'bank_code' => $bank_account_number,
                                'dob' => $dob,
                                'ben_age' => intval($date_diff),
                                'bank_ifsc' => $bank_ifsc_code
                                // 'created_by_dist_code' => $u_created_by_dist_code,
                                // 'created_by_local_body_code' => $u_created_by_local_body_code
                            ];

                            $is_saved = $pension_details::where("id", $ben_id)->update($input);
                            if ($is_saved) {
                                $return_status = 1;
                                $return_text = "Applicant (" . $applicant_id . ") Information Updated  Successfully";
                                $return_msg = array("" . $return_text);
                            } else {
                                $return_status = 0;
                                $return_text = "Applicant not found";
                                $return_msg = array("" . $return_text);
                            }
                        } else {
                            $return_status = 0;
                            $return_text = "Dob Not Valid";
                            $return_msg = array("" . $return_text);
                        }
                    } catch (\Exception $e) {
                        $return_status = 0;
                        $return_text = "Applicant not found";
                        $return_msg = array("" . $return_text);
                    }
                }
            } else {
                $return_status = 0;
                $return_text = "Bank IFSC not found";
                $return_msg = array("" . $return_text);
            }
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }
        return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    function wcdconsol_report20210202(Request $request)
    {
        $consolidate = $request->consolidate;
        //dd($consolidate);
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $district_visible = $is_urban_visible = $block_visible = 1;
        $scheme_arr = array();
        if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD') {
            $district_visible = $is_urban_visible = $block_visible = 1;
            if ($consolidate == 1)
                $scheme_arr = array(10, 11, 2);
            else
                $scheme_arr = array(10, 11);
        } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;

            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == 10) {
                    array_push($scheme_arr, 10);
                    $is_urban = $roleObj['is_urban'];
                    $district_code = $roleObj['district_code'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }

            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == 11) {
                    array_push($scheme_arr, 11);
                    $is_urban = $roleObj['is_urban'];
                    $district_code = $roleObj['district_code'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }
            if ($consolidate == 1) {
                foreach ($roleArray as $roleObj) {
                    if ($roleObj['scheme_id'] == 2) {
                        array_push($scheme_arr, 2);
                        $is_urban = $roleObj['is_urban'];
                        $district_code = $roleObj['district_code'];
                        if ($roleObj['is_urban'] == 1) {
                            $blockCode = $roleObj['urban_body_code'];
                        } else {
                            $blockCode = $roleObj['taluka_code'];
                        }
                        break;
                    }
                }
            }
            if (empty($district_code))
                return redirect("/")->with('success', 'User Disabled. ');
        } else {
            return redirect("/")->with('success', 'User Disabled. ');
        }
        //dd($district_code);
        if (!empty($district_code)) {
            $district_visible = 0;
            $district_code_fk = $district_code;
        } else {
            $district_code_fk = NULL;
        }
        if (!empty($is_urban)) {
            $is_urban_visible = 0;
            $rural_urban_fk = $is_urban;
        } else {
            $rural_urban_fk = NULL;
        }
        if (!empty($blockCode)) {
            $block_visible = 0;
            $block_munc_corp_code_fk = $blockCode;
        } else {
            $block_munc_corp_code_fk = NULL;
        }
        $schemes = Scheme::where('is_active', 1)->whereIn('id', $scheme_arr)->get(['scheme_name', 'id']);
        $districts = District::get();
        return view(
            'WCDOAPMANABIK.wcdconsol_report20210202',
            [
                'consolidate' => $consolidate,
                'schemes' => $schemes,
                'districts' => $districts,
                'district_visible' => $district_visible,
                'district_code_fk' => $district_code_fk,
                'is_urban_visible' => $is_urban_visible,
                'rural_urban_fk' => $rural_urban_fk,
                'block_visible' => $block_visible,
                'block_munc_corp_code_fk' => $block_munc_corp_code_fk
            ]
        );
    }
    function wcdconsol_report20210202post(Request $request)
    {
        $consolidate = $request->consolidate;
        $scheme_code = $request->scheme_code;
        $scheme_row = Scheme::where('is_active', 1)->where('id', $scheme_code)->first();
        $district = $request->district;
        if (!empty($district)) {
            $district_row = District::where('district_code', $district)->first();
        }
        $urban_code = $request->urban_code;
        $block = $request->block;
        if (!empty($block)) {

            if ($urban_code == 1) {
                $block_ulb = UrbanBody::where('urban_body_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->urban_body_name;
            } else {
                $block_ulb = Taluka::where('block_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->block_name;
            }
        }
        $districtwise = $blockwise = $muncwise = $gpwise = $wardwise = 0;
        $rules = [
            'scheme_code' => 'required|in:10,11,2'
        ];
        $data = array();
        $column = "";
        $attributes = array();
        $messages = array();
        $attributes['type_of_penstion'] = 'Select Pension';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            if ($consolidate == 1) {
                if ($scheme_code == 2)
                    $table_schema = 'manabik';
                $created_at_date_condition = "";
                $legacy_import_condition = "";
                $user_msg = " Consolidated Data Entry Report for WCD";
            } else {
                $created_at_date_condition = " and date(created_at)>='2021-02-02'";
                $legacy_import_condition = " and legacy_import=TRUE";
                $user_msg = " Brief Data Entry Report for WCD";
            }
            if ($scheme_code == 10)
                $table_schema = 'oap_wcd';
            else if ($scheme_code == 11)
                $table_schema = 'wp_wcd';
            $data = array();
            $return_status = 1;
            $return_msg = '';
            $heading_msg = '';
            //dd($legacy_import);
            if (!empty($block)) {
                if ($urban_code == 1) {
                    $query = "select A.*,B.*
                from
                (
                select urban_body_ward_code,urban_body_ward_name as location_name from m_urban_body_ward where urban_body_code=" . $block . "
                order by urban_body_ward_name
                ) as A LEFT JOIN
                (
                    select count(*) as applied,
                    sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) verified,
                    sum(case when next_level_role_id=0 then 1 else 0 end) approved,
                    sum(case when is_rejected=1 then 1 else 0 end) rejected,
                    gp_ward_code from " . $table_schema . ".beneficiary 
                    where block_ulb_code=" . $block . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "  group by  gp_ward_code
                ) as B ON A.urban_body_ward_code=B.gp_ward_code";
                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                    $column = "Ward";
                    $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $blk_munc_name;
                } else {
                    $query = "select A.*,B.*
                from
                (
                select gram_panchyat_code,gram_panchyat_name as location_name from m_gp where block_code=" . $block . "
                order by gram_panchyat_name
                ) as A LEFT JOIN
                (
                    select count(*) as applied,
                    sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) verified,
                    sum(case when next_level_role_id=0 then 1 else 0 end) approved,
                    sum(case when is_rejected=1 then 1 else 0 end) rejected,
                    gp_ward_code from " . $table_schema . ".beneficiary  
                    where block_ulb_code=" . $block . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "   group by  gp_ward_code
                ) as B ON A.gram_panchyat_code=B.gp_ward_code";
                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $blk_munc_name;
                }
            } else if (!empty($urban_code)) {
                if ($urban_code == 1) {
                    $query = "select A.*,B.*
                from
                (
                select urban_body_code,urban_body_name as location_name from m_urban_body
				where district_code=" . $district . "
                order by urban_body_name
                ) as A LEFT JOIN
                (
                    select count(*) as applied,
                    sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) verified,
                    sum(case when next_level_role_id=0 then 1 else 0 end) approved,
                    sum(case when is_rejected=1 then 1 else 0 end) rejected,
                    block_ulb_code from " . $table_schema . ".beneficiary 
                    where dist_code=" . $district . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "  group by  block_ulb_code
                ) as B ON A.urban_body_code=B.block_ulb_code";
                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                    $column = "Municipality";
                } else {
                    $query = "select A.*,B.*
                from
                (
                select block_code,block_name as location_name from m_block
				where district_code=" . $district . "
                order by block_name
                ) as A LEFT JOIN
                (
                    select count(*) as applied,
                    sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) verified,
                    sum(case when next_level_role_id=0 then 1 else 0 end) approved,
                    sum(case when is_rejected=1 then 1 else 0 end) rejected,
                    block_ulb_code from " . $table_schema . ".beneficiary  
                    where dist_code=" . $district . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "  group by  block_ulb_code
                ) as B ON A.block_code=B.block_ulb_code";
                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                    $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                    $column = "Block";
                }
            } else if (!empty($district)) {
                $query = "select A.*,B.*
                from
                (
                select urban_body_code,urban_body_name||'-M' as location_name from m_urban_body
				where district_code=" . $district . "
                order by urban_body_name
                ) as A LEFT JOIN
                (
                    select count(*) as applied,
                    sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) verified,
                    sum(case when next_level_role_id=0 then 1 else 0 end) approved,
                    sum(case when is_rejected=1 then 1 else 0 end) rejected,
                    block_ulb_code from " . $table_schema . ".beneficiary  
                    where dist_code=" . $district . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "  group by  block_ulb_code
                ) as B ON A.urban_body_code=B.block_ulb_code";

                $data_part1 = DB::connection('pgsql_mis')->select($query);
                $data1 = array_merge($data, $data_part1);

                $query = "select A.*,B.*
                from
                (
                select block_code,block_name||'-B' as location_name from m_block
				where district_code=" . $district . "
                order by block_name
                ) as A LEFT JOIN
                (
                    select count(*) as applied,
                    sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) verified,
                    sum(case when next_level_role_id=0 then 1 else 0 end) approved,
                    sum(case when is_rejected=1 then 1 else 0 end) rejected,
                    block_ulb_code from " . $table_schema . ".beneficiary 
                    where dist_code=" . $district . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . " group by  block_ulb_code
                ) as B ON A.block_code=B.block_ulb_code";
                $data_part = DB::connection('pgsql_mis')->select($query);
                $data2 = array_merge($data, $data_part);
                $data = array_merge($data1, $data2);
                $heading_msg = 'Block/Munc Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                $column = "Block/Munc";
            } else {
                $query = "select A.*,B.*
                from
                (
                select district_code,district_name as location_name from m_district
                order by district_name
                ) as A LEFT JOIN
                (
                    select count(*) as applied,
                    sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) verified,
                    sum(case when next_level_role_id=0 then 1 else 0 end) approved,
                    sum(case when is_rejected=1 then 1 else 0 end) rejected,
                    dist_code from " . $table_schema . ".beneficiary  
                    where scheme_id=" . $scheme_code . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "  group by  dist_code
                ) as B ON A.district_code=B.dist_code";
                $data_part = DB::connection('pgsql_mis')->select($query);
                $data = array_merge($data, $data_part);
                $heading_msg = 'District Wise ' . $user_msg;
                $column = "Disttrict";
            }
            $heading_msg = $heading_msg . ' for ' . $scheme_row->scheme_name;
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }
        return response()->json([
            'return_status' => $return_status,
            'return_msg' => $return_msg,
            'row_data' => $data,
            'column' => $column,
            'heading_msg' => $heading_msg
        ]);
    }
    function flag_check()
    {
        $suspended = 1;
        $withquota = 0;
        $arr['suspended'] = $suspended;
        $arr['withquota'] = $withquota;
        return $arr;
    }
    function get_oap_wp_data()
    {

        $roleArray = session('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == 10 || $roleObj['scheme_id'] == 11) {
                $district_code = $roleObj['district_code'];
                break;
            } else {
                continue;
            }
        }
        $data = array();
        $total_oap_data = DB::table('oap_wcd.beneficiary')
            ->selectRaw('sum(case when next_level_role_id=0 then 1 else 0 end) oap_approved,
            sum(case when (is_verified=1 and is_approved=0 and is_rejected=0)  or next_level_role_id IS NULL then 1 else 0 end) oap_pending')
            ->where('created_by_dist_code', $district_code)
            ->first();
        $data['oap_approved'] = $total_oap_data->oap_approved;
        $data['oap_pending'] = $total_oap_data->oap_pending;
        $total_wp_data = DB::table('wp_wcd.beneficiary')
            ->selectRaw('sum(case when next_level_role_id=0 then 1 else 0 end) wp_approved,
            sum(case when (is_verified=1 and is_approved=0 and is_rejected=0)  or next_level_role_id IS NULL then 1 else 0 end) wp_pending')
            ->where('created_by_dist_code', $district_code)
            ->first();
        $data['wp_approved'] = $total_wp_data->wp_approved;
        $data['wp_pending'] = $total_wp_data->wp_pending;
        $total_quota_arr = DB::table('m_cap')
            ->selectRaw('sum(case when scheme_id=10 then capacity else 0 end) oap_quota,
            sum(case when scheme_id=11 then capacity else 0 end) wp_quota')->where('district_code', $district_code)->first();
        $data['oap_quota'] = $total_quota_arr->oap_quota;
        $data['wp_quota'] = $total_quota_arr->wp_quota;
        return $data;
    }
    function get_oap_or_wp_data($scheme_code, $district_code)
    {
        if ($scheme_code == 10) {
            $schema_name = "oap_wcd";
        } else if ($scheme_code == 11) {
            $schema_name = "wp_wcd";
        }
        $data = array();
        $total_data = DB::table($schema_name . '.beneficiary')
            ->selectRaw('sum(case when next_level_role_id=0 then 1 else 0 end) approved,
            sum(case when (is_verified=1 and is_approved=0 and is_rejected=0)  or next_level_role_id IS NULL then 1 else 0 end) pending')
            ->where('created_by_dist_code', $district_code)
            ->first();
        $data['approved'] = $total_data->approved;
        $data['pending'] = $total_data->pending;

        $total_quota_arr = DB::table('m_cap')
            ->selectRaw('capacity')->where('district_code', $district_code)->where('scheme_id', $scheme_code)->first();
        $data['quota'] = $total_quota_arr->capacity;
        return $data;
    }
}
