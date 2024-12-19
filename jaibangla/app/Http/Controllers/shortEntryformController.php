<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\District;
use Auth;
use App\Scheme;
use App\PensionSc;
use App\PensionSt;
use App\UrbanBody;
use App\Taluka;
use App\Ward;
use App\GP;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\DocumentType;
use App\SchemeDocMap;
use App\Assembly;
use App\BenDocsSc;
use App\BenDocsSt;
use Config;
use Carbon\Carbon;
use App\DSRejecedApplicationSc;
use App\DSRejecedApplicationSt;
use App\Helpers\AuthChecker;


class shortEntryformController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->base_dob_chk_date = date("Y-m-d");
  }
  public function index(Request $request)
  {
    return redirect('/')->with('error', 'Not Allowed');
    $is_active = 0;
    $pr1 = $request->pr1;
    if (empty($pr1)) {
      return redirect("/")->with('error', 'Input not valid');
    }
    if (!in_array($pr1, array('johar', 'bandhu'))) {
      return redirect("/")->with('error', 'Input not valid');
    }
    $scheme_row = Scheme::select('id', 'scheme_name')->where('short_code', $pr1)->first();
    if (empty($scheme_row)) {
      return redirect("/")->with('error', 'Input not valid');
    }
    $scheme_id = $scheme_row->id;
    $scheme_name = $scheme_row->scheme_name;
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $request->session()->put('level', $roleObj['mapping_level']);
        $request->session()->put('distCode', $roleObj['district_code']);
        $district_code = $roleObj['district_code'];
        $is_urban = $roleObj['is_urban'];
        if ($roleObj['is_urban'] == 1) {
          $request->session()->put('blockCode', $roleObj['urban_body_code']);
          $sel_urban_body_code = $roleObj['urban_body_code'];
        } else {
          $request->session()->put('blockCode', $roleObj['taluka_code']);
          $sel_urban_body_code = $roleObj['taluka_code'];
        }
        break;
      }
    }
    if ($is_active == 0) {
      return redirect("/")->with('success', 'User Disabled for scheme ' . $scheme_name);
    }
    $districts = District::get();
    $district_row =  $districts->where('district_code', $district_code)->first();
    $district_name = $district_row->district_name;
    return view(
      'shortEntryForm/index',
      [
        'sel_district' => $district_code, 'sel_rural_urban' => $is_urban, 'sel_urban_body_code' => $sel_urban_body_code, 'districts' => $districts, 'district_name' => $district_name, 'scheme_id' => $scheme_id, 'pr1' => $pr1, 'scheme_name' => $scheme_name, 'district_code' => $district_code
      ]
    );
  }
  public function store(Request $request)
  {
    return redirect('/')->with('error', 'Not Allowed');
    $is_active = 0;
    $scheme_id = $request->scheme_id;
    if (empty($scheme_id)) {
      return redirect("/")->with('error', 'Input not valid');
    }
    if (!in_array($scheme_id, array(1, 3))) {
      return redirect("/")->with('error', 'Input not valid');
    }
    $scheme_row = Scheme::select('id', 'scheme_name', 'short_code')->where('id', $scheme_id)->first();
    if (empty($scheme_row)) {
      return redirect("/")->with('error', 'Input not valid');
    }
    $scheme_id = $scheme_row->id;
    $scheme_name = $scheme_row->scheme_name;
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $request->session()->put('level', $roleObj['mapping_level']);
        $request->session()->put('distCode', $roleObj['district_code']);
        $mapping_level = $roleObj['mapping_level'];
        $district_code = $roleObj['district_code'];
        if ($roleObj['is_urban'] == 1) {
          $blockCode = $roleObj['urban_body_code'];
          $request->session()->put('blockCode', $roleObj['urban_body_code']);
        } else {
          $request->session()->put('blockCode', $roleObj['taluka_code']);
          $blockCode = $roleObj['taluka_code'];
        }
        break;
      }
    }
    if ($is_active == 0) {
      return redirect("/")->with('success', 'User Disabled for scheme ' . $scheme_name);
    }
    $this->validateInputShort($request);
    $scheme_id = $request->scheme_id;
    if ($scheme_id == 1) {
      $pension_details = new PensionSt();
      $pension_details->caste = 'ST';
      $pr1 = $scheme_row->short_code;
    } else if ($scheme_id == 3) {
      $pension_details = new PensionSc();
      $pension_details->caste = 'SC';
      $pr1 = $scheme_row->short_code;
    } else {
      return redirect("/")->with('success', 'Scheme Not Valid');
    }
    if ($request->urban_code == 1) {
      $pension_details->rural_urban_id = 1;
      if (!empty($request->block)) {
        $pension_details->block_ulb_code = $request->block;
        $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
        $pension_details->block_ulb_name = $block_ulb->urban_body_name;
      }
      if (!empty($request->gp_ward)) {
        $pension_details->gp_ward_code = $request->gp_ward;
        $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();
        $pension_details->gp_ward_name   = $gp_ward->urban_body_ward_name;
      }
    } else if ($request->urban_code == 2) {
      $pension_details->rural_urban_id = 2;
      if (!empty($request->block)) {
        $pension_details->block_ulb_code = $request->block;
        $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
        $pension_details->block_ulb_name = $block_ulb->block_name;
      }
      if (!empty($request->gp_ward)) {
        $pension_details->gp_ward_code = $request->gp_ward;
        $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();
        $pension_details->gp_ward_name   = $gp_ward->gram_panchyat_name;
      }
    }
    if (!empty($request->mobile_no)) {
      $mobile_no = $request->mobile_no;
    } else {
      $mobile_no = NULL;
    }
    if (!empty($request->aadhar_no)) {
      $pension_details->aadhar_no   = trim($request->aadhar_no);
    }
    if (!empty($request->ration_card_cat) && !empty($request->ration_card_no)) {
      $pension_details->ration_card_cat   = trim($request->ration_card_cat);
      $pension_details->ration_card_no   = trim($request->ration_card_no);
    }
    if (!empty($request->epic_voter_id)) {
      $pension_details->epic_voter_id   = trim($request->epic_voter_id);
    }
    $pension_details->scheme_id = $scheme_id;
    $pension_details->next_level_role_id = 9999;
    $pension_details->ds_registration_no = trim($request->ds_application_id);
    $pension_details->ben_fname = trim($request->first_name);
    if (!empty(trim($request->middle_name)))
      $pension_details->ben_mname = $request->middle_name;
    if (!empty(trim($request->last_name)))
      $pension_details->ben_lname = $request->last_name;
    if (!empty(trim($request->district)))
      $pension_details->dist_code = $request->district;
    $pension_details->mobile_no = $mobile_no;
    $pension_details->created_by = Auth::user()->id;
    $pension_details->created_by_level = $mapping_level;
    $pension_details->created_by_dist_code = $district_code;
    $pension_details->created_by_local_body_code = $blockCode;

    try {
      $is_saved = $pension_details->save();
      if ($is_saved) {
        $id = $pension_details->benid;
        return redirect("shortEntry?pr1=" . $pr1)->with('success', 'Application Submitted Successfully')
          ->with('id',  $id);
      } else {

        return redirect("shortEntry?pr1=" . $pr1)->with('error', 'Something wrong please try again.');
      }
    } catch (\Exception $e) {
      //dd($e);
      return redirect("shortEntry?pr1=" . $pr1)->with('error', 'Something wrong please try again.');
    }
  }
  public function entryList(Request $request)
  {
    $pr1 = $request->pr1;
    if (empty($pr1)) {
      return redirect("/")->with('error', 'Input not valid');
    }
    if (!in_array($pr1, array('johar', 'bandhu'))) {
      return redirect("/")->with('error', 'Input not valid');
    }
    $scheme_row = Scheme::select('id', 'scheme_name')->where('short_code', $pr1)->first();
    if (empty($scheme_row)) {
      return redirect("/")->with('error', 'Input not valid');
    }
    $scheme_id = $scheme_row->id;
    $scheme_name = $scheme_row->scheme_name;
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $request->session()->put('level', $roleObj['mapping_level']);
        $request->session()->put('distCode', $roleObj['district_code']);
        $district_code = $roleObj['district_code'];
        $is_urban = $roleObj['is_urban'];
        if ($roleObj['is_urban'] == 1) {
          $blockCode = $roleObj['urban_body_code'];
          $request->session()->put('blockCode', $roleObj['urban_body_code']);
        } else {
          $blockCode = $roleObj['taluka_code'];
          $request->session()->put('blockCode', $roleObj['taluka_code']);
        }
        break;
      }
    }
    if ($is_active == 0) {
      return redirect("/")->with('success', 'User Disabled for scheme ' . $scheme_name);
    }
    $report_type_name = 'Duare Sarkar Form Entry List';
    $identification_type_list = Config::get('constants.identification_type');
    if (request()->ajax()) {
      $condition = array();
      $condition["created_by_dist_code"] = $district_code;
      $condition["created_by_local_body_code"] = $blockCode;
      $serachvalue = $request->search['value'];
      $limit = $request->input('length');
      $offset = $request->input('start');

      $totalRecords = 0;
      $filterRecords = 0;
      $data = array();
      if ($scheme_id == 1) {
        $pension_details = new PensionSt();
        $pr1 = $scheme_row->short_code;
      } else if ($scheme_id == 3) {
        $pension_details = new PensionSc();
        $pr1 = $scheme_row->short_code;
      } else {
        return redirect("/")->with('success', 'Scheme Not Valid');
      }
      $query = $pension_details::where($condition)->whereIn('next_level_role_id', array(9999, -9999));

      $serachvalue = $request->search['value'];

      if (empty($serachvalue)) {
        $totalRecords = $query->count();
        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
          'id', 'created_by_dist_code', 'next_level_role_id',
          'ds_registration_no', 'ds_rejected_reason', 'ben_fname', 'ben_lname', 'ben_mname', 'mobile_no', 'block_ulb_name', 'gp_ward_name', 'scheme_id'
        ]);
      } else {
        if (is_numeric($serachvalue)) {
          $ben_id = substr($serachvalue, -7);
          $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
            $query1->where('id', $ben_id);
          });
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'id', 'created_by_dist_code', 'next_level_role_id',
              'ds_registration_no', 'ds_rejected_reason', 'ben_fname', 'ben_lname', 'ben_mname', 'mobile_no', 'block_ulb_name', 'gp_ward_name', 'scheme_id'
            ]
          );
        } else {
          $query = $query->where(function ($query1) use ($serachvalue) {
            $query1->where('ben_fname', 'like', $serachvalue . '%')
              ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
              ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
              ->orWhere('ds_registration_no', 'like', $serachvalue . '%');
          });
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'id', 'created_by_dist_code', 'next_level_role_id',
              'ds_registration_no', 'ds_rejected_reason', 'ben_fname', 'ben_lname', 'ben_mname', 'mobile_no', 'block_ulb_name', 'gp_ward_name', 'scheme_id'
            ]
          );
        }
        $filterRecords = count($data);
      }
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
        ->addColumn('ds_registration_no', function ($data) {
          return $data->ds_registration_no;
        })
        ->addColumn('action', function ($data) use ($scheme_id) {
          if ($data->next_level_role_id == 9999) {
            $val = '<a href="shortEntryView?id=' . $data->id . '&scheme_id=' . $scheme_id . '" class="btn btn-primary ben_view_button" role="button" >Fill Details</a>';
            $val = $val . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-danger ben_reject_button" id="btn_' . $data->id . '">Reject</button>';
          } else {
            $val = '<span class="label label-warning" id="rejReason_' . $data->id . '">Rejected :' . $data->ds_rejected_reason . '</span>';
          }

          return $val;
        })
        ->rawColumns(['ben_id', 'ben_name', 'ds_registration_no',  'action'])
        ->make(true);
    } else {

      return view(
        'shortEntryForm/entryList',
        [
          'district_code' => $district_code,
          'scheme' => $scheme_id,
          'pr1' => $pr1,
          'scheme_name' => $scheme_name,
          'report_type_name' => $report_type_name,
          'is_urban' => $is_urban
        ]
      );
    }
  }
  public function view(Request $request)
  {
    $user_id = AuthChecker::getUserId();serId();
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
    $scheme_row = Scheme::select('id', 'scheme_name')->where('id', $scheme_id)->first();
    if (empty($scheme_row)) {
      return redirect("/")->with('error', 'Input not valid');
    }
    $scheme_id = $scheme_row->id;
    $scheme_name = $scheme_row->scheme_name;
    $row = array();
    if ($scheme_id == 3) {
      // $row = PensionSc::find($id);
      $model_name = 'App\\PensionSc';
    } else if ($scheme_id == 1) {
      //$row = PensionSt::find($id);
      $model_name = 'App\\PensionSt';
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
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
    //dd($distCode);
    if ($is_active == 0) {
      return redirect("/")->with('error', 'User Disabled');
    }
    $condition = array();
    $condition["id"] = $id;
    $condition["scheme_id"] = $scheme_id;
    $condition["created_by_dist_code"] = $distCode;
    $condition["created_by_local_body_code"] = $blockCode;
    $condition["next_level_role_id"] = 9999;
    $query = $model_name::where($condition);

    $row = $query->first();
    $districts = District::where('is_revenue_district', '=', '1')->get(['district_code', 'district_name']);


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

    $doc_profile_image = DocumentType::get()
      ->where("is_profile_pic", true)->first();



    if ($doc_profile_image) {
      $doc_profile_image_id = $doc_profile_image->id;
    }


    return view('shortEntryForm/pension_edit', ['scheme_name' => $scheme_name, 'row' => $row, 'districts' => $districts, 'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id]);
  }
  public function applicationupdate(Request $request)
  {

    $base_url = url('/');
    $id = $request->id;
    $scheme_id = (int) $request->scheme_id;
    //dd($scheme_id);
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

    if ($scheme_id == 3) {
      $pr1 = "bandhu";
    } elseif ($scheme_id == 1) {
      $pr1 = "johar";
    }
    $this->validateInput($request,  $scheme_id);
    if ($request->dob != '') {
      $dob_post = $request->dob;
      $diff = Carbon::parse($request->dob)->diffInYears($this->base_dob_chk_date);
      if ($diff < 60 || $diff > 120) {
        return redirect("shortEntryView?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('error', 'Dob not valid..')->withInput($request->all);
      }
    } else {
      $dob_post = NULL;
      $diff = $request->txt_age;
    }
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

    $input = [
      'next_level_role_id' => NULL,
      'ben_fname' => trim($request->first_name),
      'ben_mname' => trim($request->middle_name),
      'ben_lname' => trim($request->last_name),
      'gender' => $request->gender,
      'dob' => $dob_post,
      'ben_age' => $diff,

      'father_fname' => trim($request->father_first_name),
      'father_mname' => trim($request->father_middle_name),
      'father_lname' => trim($request->father_last_name),
      'mother_fname' => trim($request->mother_first_name),
      'mother_mname' => trim($request->mother_middle_name),
      'mother_lname' => trim($request->mother_last_name),
      'marital_status' => trim($request->marital_status),

      'spouse_fname' => trim($request->spouse_first_name),
      'spouse_mname' => trim($request->spouse_middle_name),
      'spouse_lname' => trim($request->spouse_last_name),
      //'bpl_y_n' =>$request->if_bpl,
      'bpl_seq_no' => trim($request->bpl_seq_no),
      'bpl_id_no' => trim($request->bpl_id_no),
      'bpl_total_score' => intval($request->bpl_total_score),
      'mothly_income' => $request->monthly_income,

      'receive_pension' => $receive_pension,
      'social_security_pension' => $social_security_pension,

      'ration_card_cat' => trim($request->ration_card_cat),
      'ration_card_no'  => trim($request->ration_card_no),
      'ahl_tin'  => trim($request->ahl_tin),
      'aadhar_no'  => trim($request->aadhar_no),
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
      'mobile_no'  => trim($request->mobile_no),
      'email' => trim($request->email),



      'bank_name'  => trim($request->name_of_bank),
      'branch_name'   => trim($request->bank_branch),
      'bank_code'    => trim($request->bank_account_number),
      'bank_ifsc'   => trim($request->bank_ifsc_code),
      'nominate_name' => trim($request->nominate_name),
      'nominate_address' => trim($request->nominate_address),
      'nominate_relationship' => trim($request->nominate_relationship),
      'created_by' => $created_by,
      'created_by_level' => $mapping_level
    ];

    $uploaded_doc = array();
    $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
    $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
    $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
    $doc_list = array_merge($doc_list_man, $doc_list_opt);

    foreach ($doc_list as $doc) {
      if ($request->hasFile('doc_' . $doc)) {
        $doc_file = $request->file('doc_' . $doc);
        $file_passport = $doc_file->getClientOriginalName();
        $file_type = $doc_file->getClientOriginalExtension();
        $file_profile = "doc_" . $doc . "_" . rand(10000, 99999) . '_' . time() . '.' . $doc_file->getClientOriginalExtension();
        $destinationPath = storage_path('app/keep/');
        $fileStore[] = $doc_file->move($destinationPath, $file_profile);
        //array_push($uploaded_doc,$file_profile);
        $uploaded_doc[$doc] = $file_profile;
      } else {
        $file_passport = null;
      }
    }
    DB::beginTransaction();
    try {
      if ($scheme_id == 3) {
        $row_data = PensionSc::select('id', 'scheme_id', 'created_by_dist_code')->where('id', $id)
          ->where('created_by_dist_code', '=', $distCode)
          ->where('next_level_role_id', 9999)
          ->first();
        if (empty($row_data->scheme_id)) {
          return redirect("/")->with('error', 'Not Allowed');
        }
        PensionSc::where(['id' => $id, 'created_by_dist_code' => $distCode, 'created_by_local_body_code' => $blockCode, 'scheme_id' => $scheme_id, 'next_level_role_id' => 9999])
          ->update($input);
        $pr1 = "bandhu";
      } else if ($scheme_id == 1) {
        $row_data = PensionSt::select('id', 'scheme_id', 'created_by_dist_code')->where('id', $id)
          ->where('created_by_dist_code', '=', $distCode)
          ->where('next_level_role_id', 9999)
          ->first();
        if (empty($row_data->scheme_id)) {
          return redirect("/")->with('error', 'Not Allowed');
        }
        PensionSt::where(['id' => $id, 'created_by_dist_code' => $distCode, 'created_by_local_body_code' => $blockCode, 'scheme_id' => $scheme_id, 'next_level_role_id' => 9999])
          ->update($input);
      }


      $i = 0;
      foreach ($uploaded_doc as $doc_type => $doc) {
        if ($scheme_id == 3)
          $ben_docs = new BenDocsSc();
        else if ($scheme_id == 1)
          $ben_docs = new BenDocsSt();

        $ben_docs->ben_id = $id;
        $ben_docs->doc_type_id = $doc_type;
        $ben_docs->doc_name = $base_url . '/images/' . $doc;

        $doc_type_name = DocumentType::where('id', $doc_type)->get();
        $ben_docs->doc_type_name = $doc_type_name[0]['doc_name'];


        $ben_docs->is_active = true;
        $ben_docs->save();
        $i++;
      }
      DB::commit();
      return redirect("shortEntryList?pr1=" . $pr1)->with('success', 'Application Updated Successfully')
        ->with('id',  $row_data->getBenidAttribute());
    } catch (\Exception $e) {
      //dd($e);
      DB::rollback();

      return redirect("shortEntryList?pr1=" . $pr1)->with('error', 'Some error.Please try again')
        ->with('id',  $row_data->getBenidAttribute());
    }
    //dd($pr1);




    //return view('pension_view_details', ['row' => $row]);
  }
  public function rejectApplication(Request $request)
  {
    $return_status = 0;
    $return_msg = '';
    $id = $request->application_id;
    $rejection_cause = $request->rejection_cause;
    $scheme_id = (int) $request->scheme;
    $designation_id_old = Auth::user()->designation_id_old;
    $user_id = AuthChecker::getUserId();
    if ($designation_id_old != 'Operator') {
      $return_status = 0;
      $return_text = 'Not Allowed';
      $return_msg = array("" . $return_text);
      return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    if (empty($scheme_id)) {
      $return_status = 0;
      $return_text = 'Scheme Code Not Valid';
      $return_msg = array("" . $return_text);
      return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    if (empty($id)) {
      $return_status = 0;
      $return_text = 'Applicant ID Not Valid';
      $return_msg = array("" . $return_text);
      return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    if (!is_int($scheme_id)) {
      $return_status = 0;
      $return_text = 'Scheme Code Not Valid';
      $return_msg = array("" . $return_text);
      return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    if (!is_numeric($id)) {
      $return_status = 0;
      $return_text = 'Applicant ID Not Valid';
      $return_msg = array("" . $return_text);
      return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    if (empty($rejection_cause)) {
      $return_status = 0;
      $return_text = 'Rejection Cause Not Valid';
      $return_msg = array("" . $return_text);
      return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
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
    // dd($is_active);
    if ($is_active == 0) {
      $return_status = 0;
      $return_text = 'User Disabled';
      $return_msg = array("" . $return_text);
      return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    $input = [
      'is_reject' =>1,
      'next_level_role_id' => -9999,
      'ds_rejected_reason' => $rejection_cause
    ];
    $is_update = 0;
    DB::beginTransaction();
    try {
      if ($scheme_id == 3) {
        $row_data = PensionSc::select('id', 'scheme_id', 'created_by_dist_code')->where('id', $id)
          ->where('created_by_dist_code', '=', $distCode)
          ->where('next_level_role_id', 9999)
          ->first();
        if (empty($row_data->scheme_id)) {
          $return_status = 0;
          $return_text = 'User Disabled';
          $return_msg = array("" . $return_text);
          return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        $modelNameAcceptReject = new DSRejecedApplicationSc();

        $is_update = PensionSc::where(['id' => $id, 'created_by_dist_code' => $distCode, 'created_by_local_body_code' => $blockCode, 'scheme_id' => $scheme_id, 'next_level_role_id' => 9999])
          ->update($input);
        $pr1 = "bandhu";
      } else if ($scheme_id == 1) {
        $row_data = PensionSt::select('id', 'scheme_id', 'created_by_dist_code')->where('id', $id)
          ->where('created_by_dist_code', '=', $distCode)
          ->where('next_level_role_id', 9999)
          ->first();
        //dd($row_data);
        if (empty($row_data->scheme_id)) {
          $return_status = 0;
          $return_text = 'User Disabled';
          $return_msg = array("" . $return_text);
          return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        $modelNameAcceptReject = new DSRejecedApplicationSt();

        $is_update = PensionSt::where(['id' => $id, 'created_by_dist_code' => $distCode, 'created_by_local_body_code' => $blockCode, 'scheme_id' => $scheme_id, 'next_level_role_id' => 9999])
          ->update($input);
      }
      $modelNameAcceptReject->application_id = $id;
      $modelNameAcceptReject->rejected_cause = $rejection_cause;
      $modelNameAcceptReject->scheme_id = $scheme_id;
      $modelNameAcceptReject->created_by = $user_id;
      $modelNameAcceptReject->created_by_level = trim($mapping_level);
      $modelNameAcceptReject->created_by_dist_code = $distCode;
      $modelNameAcceptReject->created_by_local_body_code = $blockCode;
      $modelNameAcceptReject->ip_address = request()->ip();
      $is_accept_reject = $modelNameAcceptReject->save();
      if ($is_update &&  $is_accept_reject) {
        DB::commit();
        $return_status = 1;
        $return_text = 'Application with Id ' . $row_data->getBenidAttribute() . ' successfully rejected';
        $return_msg = array("" . $return_text);
        return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
      } else {
        DB::rollback();
        $return_status = 0;
        $return_text = 'Error.Please try again.';
        $return_msg = array("" . $return_text);
        return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
      }
    } catch (\Exception $e) {
      $return_status = 0;
      $return_text = 'Error.Please try again.';
      $return_msg = array("" . $return_text);
      return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
  }

  private function validateInputShort($request)
  {
    $singleArray = array();
    $nicenameArray = array();
    $customMessage = array();
    $nicenameArray['ds_application_id'] = 'Duare Sarkar Application Id';
    $nicenameArray['first_name'] = 'First Name';
    $nicenameArray['middle_name'] = 'Middle Name';
    $nicenameArray['last_name'] = 'Last Name';
    $nicenameArray['mobile_no'] = 'Mobile Number';
    $nicenameArray['identification_type'] = 'Beneficiary Identification Document Type';
    $nicenameArray['identification_document_no'] = 'Beneficiary Identification Document Id No.';

    $nicenameArray['district'] = 'District';
    $nicenameArray['urban_code'] = 'Rural/ Urban';
    $nicenameArray['block'] = 'Block/Municipality/Corp';
    $nicenameArray['gp_ward'] = 'GP/Ward No';
    $ration_cat_key =  array_keys(Config::get('constants.ration_cat'));
    $this->validate($request, array_merge([
      //'first_name' => 'required|string|max:200',
      'ds_application_id' => 'required|max:20',
      'first_name' => 'required|string|max:200',
      'middle_name' => 'string|nullable',
      'last_name' => 'string|nullable|max:200',
      'mobile_no' => 'nullable|size:10',
      'ration_card_cat' => 'nullable|in:' . implode(",", $ration_cat_key),
      'ration_card_no' => 'nullable|string|max:11',

      'aadhar_no' => 'nullable|digits:12|nullable',
      'epic_voter_id' => 'nullable|string|max:20',
      'district' => 'nullable|numeric',
      'urban_code' => 'nullable|in:1,2',
      'block' => 'nullable|numeric',
      'gp_ward' => 'nullable|numeric',

    ], $singleArray), $customMessage, $nicenameArray);
  }
  private function validateInput($request, $scheme_id)
  {
    // print_r($arr);exit;

    $doc_id_list = SchemeDocMap::select('doc_list_man')->where('scheme_code', $scheme_id)->first();

    $in_array = json_decode($doc_id_list->doc_list_man);

    $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->get();


    $singleArray = array();
    $nicenameArray = array();
    $customMessage = array();
    foreach ($doc_list as $key => $value) {
      if (in_array($value->id,  $in_array)) {
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
    $marital_status_key =  array_keys(Config::get('constants.marital_status'));
    $ration_cat_key =  array_keys(Config::get('constants.ration_cat'));
    $rural_urban_key =  array_keys(Config::get('constants.rural_urban'));

    $this->validate($request, array_merge([
      'first_name' => 'required|string|max:200',
      'middle_name' => 'string|nullable',
      'last_name' => 'required|string|max:200',
      'gender' => 'required',
      // 'dob' => '',
      'txt_age' => 'required|numeric|min:60|max:120',

      'father_first_name' => 'required|string|max:200',
      'father_middle_name' => 'string|nullable',
      'father_last_name' => 'required|string|max:200',
      'mother_first_name' => 'required|string|max:200',
      'mother_middle_name' => 'string|nullable',
      'mother_last_name' => 'required|string|max:200',
      'caste_category' => 'required',
      'marital_status' => 'required|in:' . implode(",", $marital_status_key),

      'spouse_first_name' => 'string|nullable',
      'spouse_middle_name' => 'string|nullable',
      'spouse_last_name' => 'string|nullable',
      // 'if_bpl' => ,
      'bpl_seq_no' => 'string|nullable|max:12',
      'bpl_id_no' => 'string|nullable|max:12',
      'bpl_total_score' => 'integer|nullable',
      'monthly_income' => 'required|numeric|between: 0.00,999999.99',


      'ration_card_cat' => 'required|in:' . implode(",", $ration_cat_key),
      'ration_card_no' => 'required|string|max:11',

      'ahl_tin' => 'string|nullable|max:100',
      'aadhar_no' => 'numeric|digits:12|nullable',
      'epic_voter_id' => 'required|string|max:20',
      'pan_no' => 'string|nullable|max:12',



      'district' => 'required|integer',
      'asmb_cons' => 'required|integer',
      'urban_code' => 'required|in:' . implode(",", $rural_urban_key),
      'police_station' => 'required|string',
      'block' => 'required|integer',
      'gp_ward' => 'required|integer',
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



    ], $singleArray), $customMessage, $nicenameArray);
  }
  function ajaxgetage(Request $request)
  {
    $diff = 0;
    if ($request->dob != '') {
      $diff = Carbon::parse($request->dob)->diffInYears($this->base_dob_chk_date);
    }
    return $diff;
  }
}
