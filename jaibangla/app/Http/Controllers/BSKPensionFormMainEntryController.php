<?php

namespace App\Http\Controllers;

use App\AcceptRejectInfo;
use Illuminate\Http\Request;
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
use Carbon\Carbon;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Scheme;
use Illuminate\Support\Facades\Config;
use App\BankDetails;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Validator;
use App\DsPhase;

use App\PensionManabikWCDBSK;
use App\BenDocsManabikWCDBSK;
use App\BenDocsArcManabikWCDBSK;

class BSKPensionFormMainEntryController extends Controller
{
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
  }

  /*
        First Landing page where we can select scheme
    */
  public function schemelistforUpdateBsk(Request $request)
  {
    $arr = SchemecodeStatic::getpr1ListPurohit();
    $monthlySlug = $arr['monthly']['slug'];
    $housingSlug = $arr['housing']['slug'];
    $designationId = Auth::user()->designation_id_old;
    $userId = Auth::user()->id;
    $scheme_list = DB::select(DB::raw("select id,display_name,pr1_code,scheme_name,short_code from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1 and scheme_id=2) and is_active=1 order by rank"));
    // dd($scheme_list);
    return view('BSKcommonView.schemelistforUpdateBsk', ['scheme_list' => $scheme_list, 'monthlySlug' => $monthlySlug, 'housingSlug' => $housingSlug]);
  }

  /*
        After select the scheme then this page comes
    */
  public function editListBsk(Request $request)
  {
    // echo 1;die;
    $user_id = Auth::user()->id;
    $designation_id_old = Auth::user()->designation_id_old;
    //dd($designation_id_old);
    if ($designation_id_old != 'Operator') {
      return redirect("/")->with('error', 'Not Allowed');
    }
    //dd($request->get('pr1'));
    if ($request->get('pr1')) {
      $short_code = $request->pr1;
      $scheme_row = Scheme::where('is_active', 1)->where('id', 2)->first();

      if (empty($scheme_row)) {
        return redirect("/")->with('error', 'Parameter not valid');
      }
      // dd($scheme_row->scheme_name);
      $scheme_name = $scheme_row->scheme_name;
      $schema_name = $scheme_row->short_code;
      $scheme_id = $scheme_row->id;
      $scheme_length =  $scheme_row->scheme_length;
      $id_length = $scheme_row->id_length;
    } else {
      return redirect("/")->with('error', 'Parameter not valid');
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    $is_state_login = NULL;
    $distCode = NULL;
    $blockCode = NULL;
    $is_urban = NULL;
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

    $report_type_name = 'Application List which are comes from Bangla Sahayata Kendra';
    if (request()->ajax()) {
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
        $query =  DB::table($schema_name . '.beneficiary_bsk')->where($condition);
      }   
        $query =  DB::table($schema_name . '.beneficiary_bsk')->where($condition)->whereNull('next_level_role_id');

      $serachvalue = $request->search['value'];

      if (empty($serachvalue)) {
        $totalRecords = $query->count();
        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
          'id', 'created_by_dist_code',
          'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
          'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id',  'caste'
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
              'id', 'created_by_dist_code',
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
              'ben_lname', 'gender', 'ben_age', 'ben_mname', 'caste'
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
              'id', 'created_by_dist_code',
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
              'ben_lname', 'gender', 'ben_age', 'ben_mname', 'caste'
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
          $val = $val . '<button type="button" class="btn btn-warning btn-update" value="' . $data->id . '">Update & Forward</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
          $val = $val . '<button type="button" class="btn btn-danger btn-reject" value="' . $data->id . '">Reject</button>';
          return $val;
        })
        ->rawColumns(['ben_id', 'ben_name', 'ben_age', 'gender', 'bank_ifsc', 'bank_code', 'village_town_city', 'action'])
        ->make(true);
    } else {

      return view(
        'BSKcommonView/editListBsk',
        [
          'district_code' => $distCode,
          'scheme' => $scheme_id,
          'pr1' => $request->pr1,
          'scheme_name' => $scheme_name,
          'report_type_name' => $report_type_name,
          'is_urban' => $is_urban

        ]
      );
    }
  }

  /*
        View Full Application Form Data
    */
  public function applicationdetailsReadOnlyBsk(Request $request)
  {

    $id = $request->id;
    $scheme_id = $request->scheme_id;

    if (!is_numeric($id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    $is_state_login = NULL;
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
    //dd($distCode);
    if ($is_active == 0) {
      return redirect("/")->with('danger', 'User Disabled');
    }
    $docs = array();
    $row = null;
    if ($scheme_id == 2) {
      $row = PensionManabikWCDBSK::find($id);
      // dd($row);
      // $docs = BenDocsManabikWCDBSK::where('ben_id', $id)->orderBy('doc_type_id')->get();
      $docs = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $id)->orderBy('document_type')->get();
      // dd($docs);
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
    $gp_name =  "";

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
        $gp_name =  $gp_ward->urban_body_ward_name;
      } else {
        $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
        $gp_name =  $gp->gram_panchyat_name;
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
    if ($scheme_id == 2) {
      return view('MANABIKWCD/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
    } else {
      return view('pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
    }
  }

  public function applicationeditviewBsk(Request $request)
  {
    $user_id = Auth::user()->id;
    $id = $request->id;
    $scheme_id = (int) $request->scheme_id;
    $designation_id_old = Auth::user()->designation_id_old;

    if (!is_int($scheme_id)) {
      return redirect("/")->with('danger', 'Scheme Code Not Valid');
    }
    if (!is_numeric($id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    $row = array();
    if ($scheme_id == 2) {
      // $row = PensionManabikWCD::find($id);
      $model_name = 'App\\PensionManabikWCDBSK';
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    $is_state_login = NULL;
    $distCode = NULL;
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
    //dd($distCode);
    if ($is_active == 0) {
      return redirect("/")->with('error', 'User Disabled');
    }
    if ($scheme_id == 17) {
      $query = $model_name::where(['id' => $id,  'scheme_id' => $scheme_id]);
    } else {
      if ($is_state_login) {
        $query = $model_name::where(['id' => $id, 'is_state' => TRUE, 'scheme_id' => $scheme_id]);
      } else {
        $query = $model_name::where(['id' => $id, 'created_by_dist_code' => $distCode, 'scheme_id' => $scheme_id]);
      }
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
        $query = $query->where('next_level_role_id', '>', 0);
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
    $dob = Carbon::parse($row->dob);
    $age = $dob->age;
    

    if ($scheme_id == 2) {
      return view('BSKcommonView/pension_edit_bsk', ['assemly_list' => $assemly_list, 'block_munc_list' => $block_munc_list, 'gp_ward_list' => $gp_ward_list, 'scheme_name' => $scheme_name, 'is_state_login' => $is_state_login, 'row' => $row, 'document_msg' => $document_msg, 'districts' => $districts, 'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id,'age'=>$age]);
    }
    // else {
    //     return view('pension_edit', ['assemly_list' => $assemly_list, 'block_munc_list' => $block_munc_list, 'gp_ward_list' => $gp_ward_list, 'scheme_name' => $scheme_name, 'row' => $row, 'districts' => $districts, 'scheme_id' => $scheme_id, 'doc_list_man' => $doc_list_man, 'doc_list_opt' => $doc_list_opt, 'profile_img' => $doc_profile_image_id]);
    // }
  }

  /*
        Application Update
    */
  public function applicationupdateBsk(Request $request)
  {
    $base_url = url('/');
    $id = $request->id;
    $scheme_id = (int) $request->scheme_id;
    // dd($scheme_id);
    $designation_id_old = Auth::user()->designation_id_old;
    $schemeObj = Scheme::where('id', $scheme_id)->first();
    $scheme_schema = $schemeObj->short_code;

    if (!is_int($scheme_id)) {
      return redirect("/")->with('error', 'Scheme Code Not Valid');
    }
    if (!is_numeric($id)) {
      return redirect("/")->with('error', 'Applicant ID Not Valid');
    }
    $user_id = Auth::user()->id;
    $c_datetime = date('Y-m-d H:i:s', time());
    $is_active = 0;
    $mapping_level = NULL;
    $distCode = NULL;
    $blockCode = NULL;
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


    $isValidarr = $this->validateInput($request,  $scheme_id, 2);
    if ($isValidarr['is_valid'] == false) {
      //dd(withInput());
      return redirect("/application-edit-bsk?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $isValidarr['errors']);
      //return back()->withErrors($isValidarr['errors'])->withInput();
    }
    $scheme_row = Scheme::where('id', $scheme_id)->first();
    if (empty($scheme_row)) {
      return redirect("/")->with('error', 'User Disabled');
    }
    $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);

    // Checking aadhar number
    $new_aadhar_no = $request->aadhar_no;
    if ($this->isAadharValid($new_aadhar_no) == false) {
      $return_status = 0;
      $return_text = 'Aadhaar Number Invalid';
      $return_msg = array("" . $return_text);
      return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('error', $return_text);
    }

    $ifsc = trim($request->bank_ifsc_code);
    $bank_branch = trim($request->bank_branch);
    $name_of_bank = trim($request->name_of_bank);
    $row_count_bank = BankDetails::whereraw("trim(branch)='$bank_branch'")->whereraw("trim(ifsc)='$ifsc'")->whereraw("trim(bank)='$name_of_bank'")->count();
    if ($row_count_bank == 0) {
      $errors = array();
      $errorMsg = "Bank IFSC and Bank Name Not Match!";
      array_push($errors, $errorMsg);
      return redirect("/application-edit-bsk?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors',  $errors);
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
      'nominate_name' => $request->nominate_name,
      'nominate_address' => $request->nominate_address,
      'nominate_relationship' => $request->nominate_relationship,
      'created_by' => $user_id,
      'created_by_level' => $mapping_level,
      'updated_at' => $c_datetime
    ];

    $pr1 = "";
    $uploaded_doc = array();
    $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
    $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
    $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
    $doc_list = array_merge($doc_list_man, $doc_list_opt);
    $doc_master=DocumentType::get();
    $encolserdata = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('scheme_id',$scheme_id)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->get();
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
            $upload_file[$i]['created_at']=$c_datetime;
            $upload_file[$i]['created_by']=$user_id;
            $upload_file[$i]['ip_address']=$request->ip();
            $upload_file[$i]['attched_document']=$base64;
            $upload_file[$i]['document_mime_type']=$mime_type;
            $upload_file[$i]['document_extension']=$extension;
            $upload_file[$i]['external_mode'] = 2;
            if(!empty($doc_type_name)){
              $upload_file[$i]['doc_type_name'] = $doc_type_name->doc_name;
            }
            $i++;
            $doc_already =$encolserdata->where('document_type',$doc)->where('created_by_dist_code',$distCode)->where('beneficiary_id', $request->id)->first();
            // dd($doc_already);
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
              $upload_file_arch[$j]['external_mode'] = 2;
              $j++;
          }
        // $doc_file = $request->file('doc_' . $doc);
        // $file_passport = $doc_file->getClientOriginalName();
        // $file_type = $doc_file->getClientOriginalExtension();
        // $file_profile = "doc_" . $doc . "_" . rand(10000, 99999) . '_' . time() . '.' . $doc_file->getClientOriginalExtension();
        // $destinationPath = storage_path('app/keep_wcd/');
        // $fileStore[] = $doc_file->move($destinationPath, $file_profile);
        // array_push($uploaded_doc,$file_profile);
        // $uploaded_doc[$doc] = $file_profile;
      } 
    }

    // Dynamically Modal Name Set
    $scheme_short_code = Scheme::where('id', $scheme_id)->value('short_code');
    $ben_docs_table = strtolower($scheme_short_code) . '.ben_docs_bsk';
    $ben_docs_arc_table = strtolower($scheme_short_code) . '.ben_docs_arc_bsk';

    // Checking duplicate aadhar , mobile, and bank ifsc and bank_code
    $sp_aadhar_new = $request->aadhar_no;
    $sp_mobile_new = $request->mobile_no;
    $sp_bank_code_new = $request->bank_account_number;
    $sp_bank_ifsc_new = $request->bank_ifsc_code;

    DB::beginTransaction();
    DB::connection('pgsql4')->beginTransaction();
    DB::connection('pgsql_encwrite')->beginTransaction();
    try {
      if ($scheme_id == 2) {
        $row_data = PensionManabikWCDBSK::select('id', 'scheme_id', 'created_by_dist_code')->where('id', $id)
          ->where('created_by_dist_code', '=', $distCode)
          ->whereNull('next_level_role_id')
          ->first();
          // dd($row_data);
        if (empty($row_data->scheme_id)) {
          return redirect("/")->with('error', 'Not Allowed');
        }
        $is_update = DB::connection('pgsql4')->table($scheme_schema . '.beneficiary_bsk')->where('id', $id)->where('created_by_dist_code', $distCode)->where('scheme_id', $scheme_id)->update($input);
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
          $accept_reject_model = new AcceptRejectInfo();
          $accept_reject_model->created_at = date('Y-m-d H:i:s');
          $accept_reject_model->application_id = $id;
          $accept_reject_model->scheme_id =  $scheme_id;
          $accept_reject_model->user_id = $user_id;
          $accept_reject_model->user_id = $user_id;
          $accept_reject_model->created_by_dist_code = $distCode;
          $accept_reject_model->created_by_local_body_code = $blockCode;
          $accept_reject_model->op_type = 'MB';
          // dd($accept_reject_model);die;
          $is_saved_log = $accept_reject_model->save();
          // echo $is_update.'<br>';
          // echo $doc_inserted_arch.'<br>';
          // echo $doc_inserted_del.'<br>';
          // echo $doc_inserted.'<br>';
          // echo $is_saved_log.'<br>';
          // die;
          if ($is_update && $doc_inserted_arch && $doc_inserted_del && $doc_inserted && $is_saved_log) {
            // Migration to main table
            $fun_call = DB::connection('pgsql4')->select("SELECT manabik.bsk_entry_to_main_table_entry(" . $id . ", " . $scheme_id . ")"); // uncomment in producation
            
            if ($fun_call[0]->bsk_entry_to_main_table_entry == 1) {
              // $new_app_id = $distCode . str_pad($scheme_id, 2, 0, STR_PAD_LEFT) . str_pad($id, 15, 0, STR_PAD_LEFT);
              $new_app_id = $row_data->getBenidAttribute();
              DB::commit();
              DB::connection('pgsql4')->commit();
              DB::connection('pgsql_encwrite')->commit();
              return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('success', 'Application Updated Successfully')
                ->with('id',  $row_data->getBenidAttribute())->with('new_app_id', $new_app_id);
            } else {
              DB::rollback();
              DB::connection('pgsql4')->rollback();
              DB::connection('pgsql_encwrite')->rollback();
              return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('error', 'Some error.Please try again.');
            }
          } else {
            DB::rollback();
            DB::connection('pgsql4')->rollback();
            DB::connection('pgsql_encwrite')->rollback();
            return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('error', 'Some error.Please try again..');
          }
          // dump($is_inserted_status); dd('11');
          // if ($is_inserted_status == 1) {

          // } else if ($is_inserted_status == 2) {
          //   // dd('ok2');
          //   DB::rollback();
          //   DB::connection('pgsql4')->rollback();
          //   $return_text = 'Duplicate Bank A/c & IFSC Number.. Please try different.';
          //   $return_msg = array("" . $return_text);
          //   return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('error', $return_text);
          // } else if ($is_inserted_status == 3) {
          //   //dd('ok3');
          //   DB::rollback();
          //   DB::connection('pgsql4')->rollback();
          //   $return_text = 'Bank A/c & IFSC Number Modification Faild.. Please try different.';
          //   $return_msg = array("" . $return_text);
          //   return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('error', $return_text);
          // } else if ($is_inserted_status == 4) {
          //   // dd('ok3');
          //   DB::rollback();
          //   DB::connection('pgsql4')->rollback();
          //   $return_text = 'Duplicate Aadhar Number.. Please try different.';
          //   $return_msg = array("" . $return_text);
          //   return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('error', $return_text);
          // } else if ($is_inserted_status == 5) {
          //   //dd('ok3');
          //   DB::rollback();
          //   DB::connection('pgsql4')->rollback();
          //   $return_text = 'Aadhar Number Modification Faild.. Please try different.';
          //   $return_msg = array("" . $return_text);
          //   return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('error', $return_text);
          // } else if ($is_inserted_status == 6) {
          //   //dd('ok4');
          //   DB::rollback();
          //   DB::connection('pgsql4')->rollback();
          //   $return_text = 'Duplicate Mobile Number.. Please try different.';
          //   $return_msg = array("" . $return_text);
          //   return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('error', $return_text);
          // } else if ($is_inserted_status == 7) {
          //   //dd('ok4');
          //   DB::rollback();
          //   DB::connection('pgsql4')->rollback();
          //   $return_text = 'Mobile Number Modification Faild.. Please try different.';
          //   $return_msg = array("" . $return_text);
          //   return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('error', $return_text);
          // } else if ($is_inserted_status == 8) {
          //   //dd('ok5');
          //   DB::rollback();
          //   DB::connection('pgsql4')->rollback();
          //   $return_text = 'Error. Please try again';
          //   $return_msg = array("" . $return_text);
          //   return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('error', $return_text);
          // } else {
          //   // dd('ok6');
          // }
        // } 
      }
    } catch (\Exception $e) {
      // return $e->getMessage();
      dd($e);
      DB::rollback();
      DB::connection('pgsql4')->rollback();
      DB::connection('pgsql_encwrite')->rollback();
      if ($designation_id_old == 'Operator') {
        return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('error', 'Some error.Please try again');
        // ->with('id',  $row_data->getBenidAttribute());
      } else {
        return redirect('/')->with('error', 'Some error.Please try again');
      }
    }
    // DB::commit();
    // return array('ben_docs_bsk'=> $ben_docs_bsk_count, 'ben_docs' => $ben_docs_count);
    // if ($designation_id_old == 'Operator') {
    //     return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('success', 'Application Updated Successfully')
    //         ->with('id',  $row_data->getBenidAttribute())->with('new_app_id', $new_app_id);
    // } else {
    //     return redirect('/')->with('success', 'Application Updated Successfully');
    // }
  }

  /*
        Application Reject
    */
  public function applicationRejectBsk(Request $request)
  {
    $user_id = Auth::user()->id;
    $id = (int) $request->app_id;
    $scheme_id = (int) $request->scheme_id;
    $reamrks = $request->reject_remarks;
    $designation_id_old = Auth::user()->designation_id_old;

    $schemeObj = Scheme::where('id', $scheme_id)->first();
    $scheme_schema = $schemeObj->short_code;

    if (!is_int($scheme_id)) {
      return redirect("/")->with('danger', 'Scheme Code Not Valid');
    }
    if (!is_numeric($id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    $row = array();
    if ($scheme_id == 2) {
      // $row = PensionManabikWCD::find($id);
      $model_name = 'App\\PensionManabikWCDBSK';
    }
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    $mapping_level = NULL;
    $distCode  = NULL;
    $blockCode = NULL;
    $is_urban = NULL;
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
    //dd($distCode);
    if ($is_active == 0) {
      return redirect("/")->with('error', 'User Disabled');
    }
    // dump($id);
    $row = null;
    if ($scheme_id == 2) {
      $row = PensionManabikWCDBSK::find($id);
    }
    // dump($row);
    // dd('123');
    if (empty($row)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }

    DB::beginTransaction();
    DB::connection('pgsql4')->beginTransaction();
    try {
      $row_data = PensionManabikWCDBSK::select('id', 'scheme_id', 'created_by_dist_code')->where('id', $id)
        ->where('created_by_dist_code', '=', $distCode)
        ->whereNull('next_level_role_id')
        ->first();
      if (empty($row_data->scheme_id)) {
        return redirect("/")->with('error', 'Not Allowed');
      }
      // dump($row_data);
      // dump($distCode);
      // dump($blockCode);
      // dump($scheme_id);
      // dd('123');
      PensionManabikWCDBSK::where(['id' => $id, 'created_by_dist_code' => $distCode, 'created_by_local_body_code' => $blockCode, 'scheme_id' => $scheme_id])->whereNull('next_level_role_id')
        ->update(['next_level_role_id' => -80, 'app_rejected_reason' => $reamrks]);
        
      $new_app_id = $row_data->getBenidAttribute();
        DB::commit();
        DB::connection('pgsql4')->commit();
        return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('success', 'Application Rejected Successfully')
          ->with('id',  $row_data->getBenidAttribute())->with('new_app_id', $new_app_id);
    
    } catch (\Exception $e) {
      // return $e->getMessage();
      DB::rollback();
      DB::connection('pgsql4')->rollback();
      if ($designation_id_old == 'Operator') {
        return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('error', 'Some error.Please try again')
          ->with('id',  $row_data->getBenidAttribute());
      } else {
        return redirect('/')->with('error', 'Some error.Please try again');
      }
    }
    DB::commit();
    DB::connection('pgsql4')->commit();
    // if ($designation_id_old == 'Operator') {
    //   return redirect("application-list-read-only-edit-bsk?pr1=" . $scheme_schema)->with('success', 'Application Rejected Successfully')
    //     ->with('id',  $row_data->getBenidAttribute());
    // } else {
    //   return redirect('/')->with('success', 'Application Rejected Successfully');
    // }
  }

  private function validateInput($request, $scheme_id, $add_edit_code)
  {
    $rules = [
      'first_name' => 'required|string|max:200',
      'middle_name' => 'string|nullable',
      'last_name' => 'required|string|max:200',
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


      'ration_card_cat' => 'required|string',
      'ration_card_no' => 'required|string|max:11',

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

  /*
    Verhoeff algorithm for checking aadhar no
  */
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
