<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\District;
use App\Scheme;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use DateTime;
use App\Configduty;
use Maatwebsite\Excel\Facades\Excel;
use App\DataSourceCommon;

use App\getModelFunc;
use Illuminate\Support\Facades\Crypt;
use App\RejectRevertReason;
use App\AadharDuplicateTrail;
use App\SubDistrict;
use App\Taluka;
use App\DocumentType;
use Illuminate\Support\Facades\Storage;
use App\SchemeDocMap;
use File;
use App\BankDetails;
use App\UrbanBody;
use App\Ward;
use App\GP;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\AcceptRejectInfo;
use App\MapLavel;
use App\BenDocs;
use App\Assembly;
use App\Helpers\AuthChecker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
class ICADController extends Controller
{
    protected $base_dob_chk_date;
    protected $max_dob;
    protected $min_dob;
    protected $doc_type_id;

    public function __construct()
    {
        return redirect("/")->with('danger', 'User Disabled');

        $this->base_dob_chk_date = date('Y-m-d');
        $this->max_dob = date('Y-m-d', strtotime('+60 years'));
    }
    public function shemeSelection(Request $request)
    {
        try {
            $user_id = AuthChecker::getUserId();
            if (AuthChecker::ApproverPermission()) {
                $schemes = DB::select(DB::raw("select id,scheme_name,display_name,is_active from m_scheme where  id IN (8,9,17) and  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
                //dd($schemes);
                return view(
                    'ICAD/SchemeSelection',
                    [
                        'scheme_list' => $schemes,
                    ]
                );
            } else {
                return redirect("/")->with('danger', 'Not Allowed');
            }
        } catch (\Exception $e) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
    }
    public function pendinglist(Request $request)
    {
        $this->middleware('auth');
        $user_id = AuthChecker::getUserId();
        $is_approver = AuthChecker::ApproverPermission();
        $scheme_id = $request->scheme_id;
        if (!ctype_digit($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Not Valid');
        }
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (empty($scheme_obj)) {
            return redirect("/")->with('danger', 'Scheme Not Found');
        }
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
        if (empty($duty_obj)) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        $type_des = 'Approved Beneficiary List with Legacy Data(Previously Excel Import)';
        $district_code = $duty_obj->district_code;
        $urban_bodys = collect([]);
        $gps = collect([]);
        $district_list_obj = collect([]);
        if (!empty($scheme_obj->short_code)) {
            $schema = $scheme_obj->short_code;
            $scheme_length = $scheme_obj->scheme_length;
            $id_length = $scheme_obj->id_length;
        } else {
            $schema = "pension";
            $scheme_length = NULL;
            $id_length = NULL;
        }
        if ($duty_obj->mapping_level == "Subdiv") {
            $created_by_local_body_code = $duty_obj->urban_body_code;
            $is_rural = 1;
            $verifier_type = 'Subdiv';
            $gps = collect([]);
            $urban_body_code = $duty_obj->urban_body_code;
            $urban_bodys = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();
            $urban_body_codes = [];
            $i = 0;
            foreach ($urban_bodys as $urban_body) {

                $urban_body_codes[$i] = $urban_body->urban_body_code;
                $i++;
            }
        }
        if ($duty_obj->mapping_level == "Block") {
            $created_by_local_body_code = $duty_obj->taluka_code;
            $is_rural = 2;
            $verifier_type = 'Block';
            $urban_bodys = collect([]);
            $taluka_code = $duty_obj->taluka_code;
            $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
        }
        if ($duty_obj->mapping_level == "District") {
            $district_list_obj = District::get();
            $verifier_type = 'District';
            $is_rural = NULL;
            $created_by_local_body_code = NULL;
        }
        if (request()->ajax()) {
            $limit = $request->input('length');
            $offset = $request->input('start');
            //dd($process_type);
            $query = DB::table($schema . '.beneficiaries')
                ->where('legacy_import', true)->whereNull('next_level_role_id_edit')->where('created_by_dist_code', $district_code)->where('next_level_role_id', 0);
            if (AuthChecker::VerifierPermission()) {
                $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
                if (!empty($application_type)) {
                }
            }
            if ($duty_obj->mapping_level == "Subdiv") {
                if (!empty($request->block_ulb_code)) {
                    $query = $query->where('block_ulb_code', $request->block_ulb_code);
                }
            }
            if (!empty($request->gp_ward_code)) {
                $query = $query->where('gp_ward_code', $request->gp_ward_code);
            }
            if (AuthChecker::ApproverPermission()) {


            }
            $serachvalue = $request->search['value'];
            if (empty($serachvalue)) {
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                    'id',
                    'created_by_dist_code',
                    'dob',
                    'assembly_name',
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
                    'aadhar_edit_role_id'
                ]);
                $filterRecords = count($data);
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
                            'dob',
                            'assembly_name',
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
                            'aadhar_edit_role_id'
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
                            'dob',
                            'assembly_name',
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
                            'aadhar_edit_role_id'
                        ]
                    );
                }
                $filterRecords = count($data);
            }
            return datatables()->of($data)->setTotalRecords($totalRecords)
                ->setFilteredRecords($filterRecords)
                ->skipPaging()
                ->addColumn('application_id', function ($data) use ($scheme_id, $scheme_length, $id_length) {
                    $app_id = '';
                    return $app_id;
                })->addColumn('view', function ($data) use ($scheme_id, $is_approver) {
                    $action = '';
                    $action = '<a href="ViewlicadBriefPending?id=' . $data->id . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                    return $action;
                })->addColumn('check', function ($data) use ($is_approver) {
                    return '';
                })
                ->addColumn('id', function ($data) {
                    return $data->id;
                })
                ->addColumn('name', function ($data) {
                    return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                })->addColumn('bank_ifsc', function ($data) {
                    if (!empty($data->bank_ifsc)) {
                        $bank_ifsc = trim($data->bank_ifsc);
                    } else {
                        $bank_ifsc = '';
                    }
                    return $bank_ifsc;
                })->addColumn('bank_code', function ($data) {
                    if (!empty($data->bank_code)) {
                        $bank_code = trim($data->bank_code);
                    } else {
                        $bank_code = '';
                    }
                    return $bank_code;
                })->addColumn('mobile_no', function ($data) {
                    if (!empty($data->mobile_no)) {
                        $ben_mobile_no = trim($data->mobile_no);
                    } else {
                        $ben_mobile_no = '';
                    }
                    return $ben_mobile_no;
                })
                ->rawColumns(['view', 'id', 'name', 'mask_aadhaar_no', 'bank_ifsc', 'bank_code', 'bank_ifsc', 'bank_ifsc', 'check'])
                ->make(true);
        }

        return view(
            'ICAD.linelisting',
            [
                'verifier_type' => $verifier_type,
                'created_by_local_body_code' => $created_by_local_body_code,
                'is_rural' => $is_rural,
                'scheme_id' => $scheme_id,
                'scheme_name' => $scheme_obj->scheme_name,
                'gps' => $gps,
                'urban_bodys' => $urban_bodys,
                'district_code' => $district_code,
                'type_des' => $type_des,
                'is_approver' => $is_approver,
            ]
        );
    }
    public function editUnlock(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        if (!AuthChecker::ApproverPermission()) {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $scheme_id = $request->scheme_id;
        if (!ctype_digit($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Not Valid');
        }
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (empty($scheme_obj)) {
            return redirect("/")->with('danger', 'Scheme Not Found');
        }
        $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
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
        // dd($blockCode);
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
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
        if (empty($duty_obj)) {
            return redirect("/")->with('danger', 'Not Allowed');
        }
        if (!empty($scheme_obj->short_code)) {
            $schema = $scheme_obj->short_code;
        } else {
            $schema = "pension";
        }
        $condition = array();
        $condition["created_by_dist_code"] = $distCode;
        $condition["next_level_role_id"] = 0;
        $condition["legacy_import"] = TRUE;
        $condition["id"] = $id;
        $row = DB::table($schema . '.beneficiaries')->where($condition)->whereNull('next_level_role_id_edit')->first();
        if (empty($row)) {
            return redirect("/")->with('error', 'Application Id Valid');
        }
        $scheme_name = $scheme_obj->scheme_name;
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
        $encolserdata = BenDocs::select('document_type', 'attched_document')->where('scheme_id', $scheme_id)->where('created_by_dist_code', $distCode)->where('beneficiary_id', $request->id)->get()->pluck('attched_document', 'document_type')->toArray();
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
        $status = '';



        //dd($status);
        return view('ICAD/pension_edit_unlock', [
            'doc_profile_image_id' => $doc_profile_image_id,
            'max_dob' => $this->max_dob,
            'scheme_name' => $scheme_name,
            'row' => $row,
            'document_msg' => $document_msg,
            'districts' => $districts,
            'scheme_id' => $scheme_id,
            'encloser_list' => $encloser_list,
            'profile_img' => $doc_profile_image_id
        ]);
    }
    function editicadPost(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        if (!AuthChecker::ApproverPermission()) {
            return redirect("/")->with('error', 'Not Allowed');
        }
        $scheme_id = $request->scheme_id;
        if (!ctype_digit($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Not Valid');
        }
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (empty($scheme_obj)) {
            return redirect("/")->with('danger', 'Scheme Not Found');
        }
        $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
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
        if (!empty($scheme_obj->short_code)) {
            $schema = $scheme_obj->short_code;
        } else {
            $schema = "pension";
        }
        $condition["created_by_dist_code"] = $distCode;
        $condition["next_level_role_id"] = 0;
        $condition["legacy_import"] = TRUE;
        $condition["id"] = $id;
        $row = DB::table($schema . '.beneficiaries')->where($condition)->whereNull('next_level_role_id_edit')->first();
        if (empty($row)) {
            return redirect("/")->with('error', 'Application Id Valid');
        }
        $max_dob = $this->max_dob;
        $caste_key = array_keys(Config::get('constants.caste'));
        $marital_status_key = array_keys(Config::get('constants.marital_status'));
        $gender_key = array_keys(Config::get('constants.gender'));

        $rules = [
            'gender' => 'required|in:' . implode(",", $gender_key),
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
            'ration_card_cat' => 'required|string',
            'ration_card_no' => 'string|nullable|max:11',
            'ahl_tin' => 'string|nullable|max:100',
            'aadhar_no' => 'required|numeric|digits:12',
            'epic_voter_id' => 'required|string|max:20',
            'pan_no' => 'string|nullable|max:12',
            'district' => 'required',
            'urban_code' => 'required',
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
        if (isset($doc_id_list['doc_list_man']) && $doc_id_list['doc_list_man'] != 'null') {

            $in_array = json_decode($doc_id_list->doc_list_man);
        } else
            $in_array = array();
        $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->get();
        if (count($in_array) > 0) {
            foreach ($doc_list as $key => $value) {
                if (in_array($value->id, $in_array)) {
                    $previus_uploaded = $request->input('doc_already_' . $value->id);
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


            $post_aadhar_no = $request->aadhar_no;
            if ($this->isAadharValid($post_aadhar_no) == false) {
                $return_text = 'Aadhaar Number Invalid';
                $return_msg = array("" . $return_text);
                return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
            }
            if (!preg_match('/^[0-9]{10}+$/', $request->mobile_no)) {
                $return_text = 'Mobile Number Invalid';
                $return_msg = array("" . $return_text);
                return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
            }
            if ($request->mobile_no < 1000000000) {
                $return_text = 'Mobile Number Invalid';
                $return_msg = array("" . $return_text);
                return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
            }
            $district_list = District::all();
            $sel_district = $request->district;
            $cnt = $district_list->where('district_code', $sel_district)->count();
            if ($cnt == 0) {
                $return_text = 'District Invalid';
                $return_msg = array("" . $return_text);
                return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
            }
            $asmb_cons = $request->asmb_cons;
            $assembly_arr = Assembly::where('district_code', $sel_district)->where('ac_no', $asmb_cons)->first();
            if (empty($assembly_arr)) {
                $return_text = 'Assembly Invalid';
                $return_msg = array("" . $return_text);
                return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
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
                    return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
                }
                $block_ulb_name = $block_munc_arr->urban_body_name;
                $gp_ward_arr = Ward::where('urban_body_code', $sel_block)->where('urban_body_ward_code', $sel_gp_ward)->first();
                if (empty($gp_ward_arr)) {
                    $return_text = 'GP/Ward Not Invalid';
                    $return_msg = array("" . $return_text);
                    return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
                }
                $gp_ward_name = $gp_ward_arr->urban_body_ward_name;
            } else if ($sel_urban_code == 2) {
                $block_munc_arr = Taluka::where('district_code', $sel_district)->where('block_code', $sel_block)->first();
                if (empty($block_munc_arr)) {
                    $return_text = 'Block/Municipality/Corp Invalid';
                    $return_msg = array("" . $return_text);
                    return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
                }
                $block_ulb_name = $block_munc_arr->block_name;
                $gp_ward_arr = GP::where('block_code', $sel_block)->where('gram_panchyat_code', $sel_gp_ward)->first();
                if (empty($gp_ward_arr)) {
                    $return_text = 'GP/Ward Not Invalid';
                    $return_msg = array("" . $return_text);
                    return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
                }
                $gp_ward_name = $gp_ward_arr->gram_panchyat_name;
            }
            $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->get();
            $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
            $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
            $doc_list = array_merge($doc_list_man, $doc_list_opt);
            $encolserdata = BenDocs::where('scheme_id', $scheme_id)->where('created_by_dist_code', $distCode)->where('beneficiary_id', $request->id)->get();
            $already_id = array();
            foreach ($encolserdata as $enc_item) {
                array_push($already_id, $enc_item->document_type);
            }
            $doc_list_man_group_upload = array();
            $doc_list_man_group_db = array();


            if (($doc_id_list[0]['doc_list_man_group']) != '' && ($doc_id_list[0]['doc_list_man_group'] != 'null') && ($doc_id_list[0]['doc_list_man_group']) != null) {
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
                    return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errors)->withInput(Input::all());
            }
            $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
            if (!empty($request->aadhar_no)) {
                $count_aadhar = DB::table($schema . '.beneficiaries')->where('aadhar_no', trim($request->aadhar_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
                if ($count_aadhar > 0) {
                    $errors = array();
                    $errorMsg = "Aadhaar Number Already Exist! Please try different.";
                    array_push($errors, $errorMsg);
                    return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errors)->withInput(Input::all());

                }
            }
            if (!empty($request->mobile_no)) {
                $count_mobile = DB::table($schema . '.beneficiaries')->where('mobile_no', $request->mobile_no)->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
                if ($count_mobile > 0) {
                    $errors = array();
                    $errorMsg = "Mobile Number Already Exist! Please try different.";
                    array_push($errors, $errorMsg);
                    return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errors)->withInput(Input::all());
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
                'next_level_role_id_edit' => 0,
                'gender' => trim($request->gender),
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
                'ration_card_no' => trim($request->ration_card_no),
                'ahl_tin' => trim($request->ahl_tin),
                'epic_voter_id' => trim($request->epic_voter_id),
                'pan_no' => trim($request->pan_no),



                'dist_code' => $request->district,
                'assembly_code' => $request->asmb_cons,
                'assembly_name' => trim($assembly_name),
                'rural_urban_id' => $request->urban_code,
                'police_station' => trim($request->police_station),
                'block_ulb_code' => $request->block,
                'block_ulb_name' => trim($block_ulb_name),
                'gp_ward_code' => $request->gp_ward,
                'gp_ward_name' => trim($gp_ward_name),
                'village_town_city' => trim($request->village),
                'house_premise_no' => trim($request->house),
                'post_office' => trim($request->post_office),
                'pincode' => trim($request->pin_code),
                'residency_period' => $request->residency_period,
                'email' => trim($request->email),




                'nominate_name' => trim($request->nominate_name),
                'nominate_address' => trim($request->nominate_address),
                'nominate_relationship' => trim($request->nominate_relationship),
                'av_status' => trim($request->av_status),
                'receiving_pension_other_source_1' => trim($request->receiving_pension_other_source_1),
                'receiving_pension_other_source_2' => trim($request->receiving_pension_other_source_2),
                'dup_aadhar' => 0,
                'dup_mobile' => 0,
                'no_aadhar' => 0,
                'no_mobile' => 0,
                'action_by' => $user_id,
                'action_ip_address' => $request->ip(),
                'action_type' => class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod()

            ];




            if (!empty(trim($request->caste_certificate_no))) {
                $input['caste_certificate_no'] = $request->caste_certificate_no;
            }

            if (!empty(trim($request->aadhar_no))) {
                if (trim($row->aadhar_no) == trim($request->aadhar_no)) {
                    $sp_aadhar_new = NULL;
                    $sp_aadhar_old = NULL;
                } else {
                    $input['aadhar_no'] = trim($request->aadhar_no);
                    $sp_aadhar_new = trim($request->aadhar_no);
                    if (empty(trim($row->aadhar_no))) {
                        $sp_aadhar_old = NULL;
                    } else {
                        $sp_aadhar_old = trim($row->aadhar_no);
                    }
                }
            }
            if (!empty(trim($request->mobile_no))) {
                if ($row->mobile_no == trim($request->mobile_no)) {
                    $sp_mobile_new = 0;
                    $sp_mobile_old = 0;
                } else {
                    $input['mobile_no'] = trim($request->mobile_no);
                    $sp_mobile_new = trim($request->mobile_no);
                    if (empty(trim($row->mobile_no))) {
                        $sp_mobile_old = 0;
                    } else {
                        $sp_mobile_old = $row->mobile_no;
                    }
                }
            }


            $uploaded_doc = array();
            $base_url = url('/');
            $encloser_list = array();
            $i = 0;
            $c_time = date('Y-m-d H:i:s', time());
            $all_document = DocumentType::where('is_active', TRUE)->get();
            $delete_array = array();
            $j = 0;
            $upload_file_arch = array();
            $upload_file = array();
            foreach ($doc_list as $doc) {
                if ($request->hasFile('doc_' . $doc)) {
                    $doc_type_name = $all_document->where('id', $doc)->first();
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
                    $upload_file[$i]['created_by_dist_code'] = $distCode;
                    $upload_file[$i]['created_by_local_body_code'] = $blockCode;
                    $upload_file[$i]['document_type'] = $doc;
                    $upload_file[$i]['scheme_id'] = $scheme_id;
                    $upload_file[$i]['created_by_level'] = $level;
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
                    $doc_already_edit = $encolserdata->where('document_type', $doc)->where('created_by_dist_code', $distCode)->where('beneficiary_id', $request->id)->first();
                    if (in_array($doc, $already_id)) {
                        array_push($delete_array, $doc);
                        $upload_file_arch[$j]['beneficiary_id'] = $request->id;
                        $upload_file_arch[$j]['created_by_dist_code'] = $doc_already_edit->created_by_dist_code;
                        $upload_file_arch[$j]['created_by_local_body_code'] = $doc_already_edit->created_by_local_body_code;
                        $upload_file_arch[$j]['document_type'] = $doc_already_edit->document_type;
                        $upload_file_arch[$j]['scheme_id'] = $doc_already_edit->scheme_id;
                        $upload_file_arch[$j]['created_by_level'] = $doc_already_edit->created_by_level;
                        $upload_file_arch[$j]['created_at'] = $doc_already_edit->created_at;
                        $upload_file_arch[$j]['created_by'] = $doc_already_edit->created_by;
                        $upload_file_arch[$j]['ip_address'] = $doc_already_edit->ip_address;
                        $upload_file_arch[$j]['attched_document'] = $doc_already_edit->attched_document;
                        $upload_file_arch[$j]['document_mime_type'] = $doc_already_edit->document_mime_type;
                        $upload_file_arch[$j]['document_extension'] = $doc_already_edit->document_extension;
                        $j++;
                    }
                }
            }


            $document_type_list = BenDocs::select('document_type', 'attched_document')->where('scheme_id', $scheme_id)->where('created_by_dist_code', $distCode)->where('beneficiary_id', $request->id)->get();
            DB::beginTransaction();
            DB::connection('pgsql_encwrite')->beginTransaction();
            try {

                $is_inserted_status = 1;

                if ($is_inserted_status == 1) {
                    //dd('ok');
                    $arch_status = DB::statement("INSERT INTO " . $schema . ".arc_beneficiary(id, 
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
                               av_status,   legacy_import, old_beneficiary_id, pensioner_type
                               
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
                               av_status,   legacy_import, old_beneficiary_id, pensioner_type
                                 from " . $schema . ".beneficiaries where id=" . $id . ")");
                    //dd($arch_status);
                    if ($arch_status) {
                        $main_update = DB::table($schema . '.beneficiaries')->where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id])->update($input);
                        // dd($main_update);
                        if ($main_update) {
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
                            if ($doc_inserted_arch == 1 && $doc_inserted_del && $doc_inserted == 1) {


                                $accept_reject_model = new AcceptRejectInfo;
                                $accept_reject_model->created_at = $c_time;
                                $accept_reject_model->application_id = $row->id;
                                $accept_reject_model->scheme_id = $row->scheme_id;
                                $accept_reject_model->user_id = $user_id;
                                $accept_reject_model->user_id = $user_id;
                                $accept_reject_model->created_by_dist_code = $distCode;
                                $accept_reject_model->created_by_local_body_code = $blockCode;
                                $accept_reject_model->op_type = 'SM';
                                $accept_reject_model->op_type = class_basename(request()->route()->getAction()['controller']) . '@' . 'SM';

                                $is_saved_log = $accept_reject_model->save();
                                if ($is_saved_log) {
                                    DB::commit();
                                    DB::connection('pgsql_encwrite')->commit();
                                    $return_text = 'Beneficiary Edited Successfully';
                                    return redirect("/icadpendingbrieflist?scheme_id=" . $scheme_id)->with('success', $return_text)->with('id', $row->id);
                                } else {
                                    DB::rollback();
                                    DB::connection('pgsql_encwrite')->rollback();
                                    // dd('ok77777');
                                    $return_text = 'Error. Please try again';
                                    $return_msg = array("" . $return_text);
                                    return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
                                }
                            }

                        } else {
                            DB::rollback();
                            DB::connection('pgsql_encwrite')->rollback();
                            $return_text = 'Error. Please try again';
                            $return_msg = array("" . $return_text);
                            return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
                        }
                    } else {
                        DB::rollback();
                        DB::connection('pgsql_encwrite')->rollback();
                        $return_text = 'Error. Please try again';
                        $return_msg = array("" . $return_text);
                        return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
                    }

                }

            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                DB::connection('pgsql_encwrite')->rollback();
                $return_text = 'Error. Please try again';
                $return_msg = array("" . $return_text);
                return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
            }

        } else {
            $return_msg = $validator->errors()->all();
            return redirect("/ViewlicadBriefPending?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
        }
    }



    public function pdf(Request $request)
    {
        try {
            $this->middleware('auth');

            $application_id = $request->application_id;
            $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                        $user_id = AuthChecker::getUserId();
            //$user_id = AuthChecker::getUserId();
            $duty_obj = Configduty::where('user_id', $user_id)->first();
            $district_code = $duty_obj->district_code;
            $getModelFunc = new getModelFunc();
            $DraftPfImageTable = new DataSourceCommon;
            // $Table = $getModelFunc->getTable($district_code, $this->source_type, 11, 1);
            $Table = 'lb_scheme.ben_attach_documents';

            $DraftPfImageTable->setConnection('pgsql_encwrite');
            $DraftPfImageTable->setTable('' . $Table);

            $condition = array();
            $condition['created_by_dist_code'] = $district_code;
            //$condition['created_by_local_body_code'] = $blockCode;
            //$profileImagedata = DB::table('lb_scheme.ben_attach_documents_temp')->where('application_id', $app_id)->where($condition)->first();
            //$profileImagedata = $DraftPfImageTable->where('application_id', $app_id)->where($condition)->first();
            $profileImagedata = $DraftPfImageTable->where('document_type', $this->doc_type_id)->where('application_id', $application_id)->where($condition)->first();
            if (empty($profileImagedata->application_id)) {


                $return_text = 'Parameter Not Valid';
                return redirect("/")->with('error', $return_text);
            }


            $mime_type = $profileImagedata->document_mime_type;
            $image_extension = $profileImagedata->document_extension;

            try {
                if (strtoupper($image_extension) == 'PDF') {
                    $decoded = base64_decode($profileImagedata->attched_document);
                    $file_name = $profileImagedata->document_type . '_' . $profileImagedata->application_id . '.pdf';
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: attachment; filename=' . $file_name);
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . strlen($decoded));
                    ob_clean();
                    flush();
                    echo $decoded;
                    exit;
                }
            } catch (\Exception $e) {
                $return_text = 'Some error. please try again.';
                return redirect("/")->with('error', $return_text);
            }
        } catch (\Exception $e) {
            $return_text = 'Some error. please try again.';
            return redirect("/")->with('error', $return_text);
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
}
