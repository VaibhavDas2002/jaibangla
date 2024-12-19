<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\UrbanBody;
use App\SubDistrict;

use App\DocumentType;
use App\SchemeDocMap;
//Dynamic Doc End
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
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
use App\PensionPurohitMonthlyICAD;
use App\AcceptRejectInfo;
use App\BeneficiaryDupBlank;
use App\District;
use App\BenDocsPurohitMonthlyICAD;
use App\Configduty;
use App\Helpers\AuthChecker;


class LifeCertificateController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
    $this->scheme_id = 17;
    $this->base_dob_chk_date = date('Y-m-d');
    $this->max_dob = date('Y-m-d', strtotime('+60 years'));
    $this->life_certificate_id = 1001;
    $this->state_login_next_level_role_id_arr = Config::get('constants.state_login_next_level_role_id');
  }

  public function editList(Request $request)
  {

    // dd($request->all());
    $designation_id_old = Auth::user()->designation_id_old;
    $is_operator = AuthChecker::OperatorChecker();
    $is_verifier = AuthChecker::VerifierChecker();
    $is_approver = AuthChecker::ApproverChecker();
    $is_hod = AuthChecker::HODChecker();

    $user_id = AuthChecker::getUserId();
    $scheme_id = $request->scheme_id;

    if (!is_numeric($scheme_id) || $scheme_id != 17) {
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
    $report_type_name = 'Pending List whose Life Certificate Yet Not Uploaded';
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
      $query = DB::table($schema . '.beneficiaries')
        ->where('created_by_dist_code', $district_code)
        ->where("next_level_role_id", 0)
        ->where('scheme_id', $scheme_id);
      if (AuthChecker::VerifierChecker() || AuthChecker::OperatorChecker()) {
        $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
      }
      if ($duty_obj->mapping_level == "Subdiv") {
        if (!empty($request->block_ulb_code)) {
          $query = $query->where('block_ulb_code', $request->block_ulb_code);
        }
      }
      if (!empty($request->gp_ward_code)) {
        $query = $query->where('gp_ward_code', $request->gp_ward_code);
      }
      if (!empty($request->application_type)) {
        if ($request->application_type == 1) {
          if (AuthChecker::OperatorChecker())
            $query = $query->whereNull('next_level_role_id_edit');
          else if (AuthChecker::VerifierChecker())
            $query = $query->where('next_level_role_id_edit', 1);
        }

        if ($request->application_type == 3) {

          $query = $query->where('next_level_role_id_edit', 1);
        }
        if ($request->application_type == 4) {
          $query = $query->where('next_level_role_id_edit', 0);
        }
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
          'unlock_status',
          'next_level_role_id_edit',
          'aadhar_no',
          'mobile_no',
          'no_aadhar_mobile_flag'
        ]);
        $filterRecords = count($data);
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
              'id',
              'created_by_dist_code',
              'dob',
              'assembly_name',
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
              'next_level_role_id',
              'unlock_status',
              'next_level_role_id_edit',
              'aadhar_no',
              'mobile_no',
              'no_aadhar_mobile_flag'
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
              'next_level_role_id',
              'unlock_status',
              'next_level_role_id_edit',
              'aadhar_no',
              'mobile_no',
              'no_aadhar_mobile_flag'
            ]
          );
        }
        $filterRecords = count($data);
      }
      return datatables()->of($data)->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('application_id', function ($data) use ($scheme_id, $scheme_length, $id_length) {

          $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

          return $app_id;
        })->addColumn('view', function ($data) use ($scheme_id, $is_operator, $is_verifier, $is_approver) {

          if ($is_operator) {
            if (is_null($data->next_level_role_id_edit)) {
              $action = '<a href="editLifeCertificate?id=' . $data->id . '&scheme_id=' . $scheme_id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Upload</a>';
            } else if ($data->next_level_role_id_edit == 1) {
              $action = 'Modified but Verify Pending';
            } else if ($data->next_level_role_id_edit == 0) {
              $action = 'Modified and Verified';
            }
          }
          if ($is_verifier) {
            if ($data->next_level_role_id_edit == 1) {
              $action = '<a href="editLifeCertificate?id=' . $data->id . '&scheme_id=' . $scheme_id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
            } else if ($data->no_aadhar_mobile_flag == 0) {
              $action = 'Modified and Verified';
            }
          }

          return $action;
        })->addColumn('check', function ($data) use ($is_approver) {
          if ($is_approver) {
            if ($data->next_level_role_id_edit == 1) {
              return '<input type="checkbox" name="approvalcheck[]" onClick="controlCheckBox()" value="' . $data->id . '">';
            } else
              return '';
          } else {
            return '';
          }
        })
        ->addColumn('id', function ($data) {
          return $data->id;
        })->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

          $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

          return $app_id;
        })
        ->addColumn('name', function ($data) {
          return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
        })->addColumn('mask_aadhaar_no', function ($data) {
          if (!empty($data->aadhar_no)) {
            $ben_aadhar_no = trim($data->aadhar_no);
          } else {
            $ben_aadhar_no = '';
          }
          return $ben_aadhar_no;
        })->addColumn('mask_mobile_no', function ($data) {
          if (!empty($data->mobile_no)) {
            $ben_mobile_no = $data->mobile_no;
          } else {
            $ben_mobile_no = '';
          }
          return $ben_mobile_no;
        })
        ->rawColumns(['view', 'id', 'name', 'mask_aadhaar_no', 'mask_mobile_no', 'check'])
        ->make(true);
    }

    return view(
      'LifeCertificate.linelisting',
      [
        'designation_id_old' => $designation_id_old,
        'verifier_type' => $verifier_type,
        'created_by_local_body_code' => $created_by_local_body_code,
        'is_rural' => $is_rural,
        'scheme_id' => $scheme_id,
        'gps' => $gps,
        'urban_bodys' => $urban_bodys,
        'report_type_name' => $report_type_name,
        'district_code' => $district_code,
        'scheme_name' => $scheme_obj->scheme_name,
        'is_operator' => $is_operator,
        'is_verifier' => $is_verifier,
        'is_approver' => $is_approver,
        'is_hod' => $is_hod
      ]
    );
  }

  public function editUnlock(Request $request)
  {
    $user_id = AuthChecker::getUserId();
    $scheme_id = $request->scheme_id;
    if (!ctype_digit($scheme_id)) {
      return redirect("/")->with('error', 'Scheme Not Valid');
    }
    if (empty($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Found');
    }
    if (!is_numeric($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (empty($scheme_obj)) {
      return redirect("/")->with('danger', 'Scheme Not Found');
    }
    // $designation_id_old = Auth::user()->designation_id_old;
    if (!AuthChecker::OperatorChecker() || AuthChecker::VerifierChecker()) {
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
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    $condition = array();
    $condition["created_by_dist_code"] = $distCode;
    if (AuthChecker::VerifierChecker()) {
      $condition["created_by_local_body_code"] = $blockCode;
    }
    // $condition["next_level_role_id"] = 0;
    $condition["id"] = $id;
    $row = DB::table($schema . '.beneficiaries')->where($condition)->where("next_level_role_id", 0)->first();
    //dd($row);
    if (empty($row)) {
      return redirect("/")->with('error', 'Application Id Valid');
    }
    $scheme_row = Scheme::select('scheme_name')->where('id', $this->scheme_id)->first();
    $scheme_name = $scheme_row->scheme_name;
    $districts = District::where('is_revenue_district', '=', '1')->get(['district_code', 'district_name']);
    $doc_certificate = DocumentType::select('id', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->where("id", $this->life_certificate_id)->first();
    $already_uploaded = 0;
    $encolserdata = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $id)->where('document_type', $this->life_certificate_id)->first();

    if (!empty($encolserdata)) {
      $already_uploaded = 1;
    }
    //dd();
    return view('LifeCertificate/pension_edit_unlock', [
      'scheme_name' => $scheme_name,
      'row' => $row,
      'districts' => $districts,
      'scheme_id' => $scheme_id,
      'doc_certificate' => $doc_certificate,
      'encolserdata' => $encolserdata,
      'already_uploaded' => $already_uploaded,
      // 'designation_id_old' => $designation_id_old,
    ]);
  }
  function editLifeCertificatePost(Request $request)
  {
    $scheme_id = $request->scheme_id;
    $user_id = AuthChecker::getUserId();
    // $designation_id_old = Auth::user()->designation_id_old;
    if (!AuthChecker::OperatorChecker()) {
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
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    $condition = array();
    $condition["created_by_dist_code"] = $distCode;
    $condition["created_by_local_body_code"] = $blockCode;
    // $condition["next_level_role_id"] = 0;
    $condition["id"] = $id;
    $row = DB::table($schema . '.beneficiary')->where($condition)->where("next_level_role_id", 0)->first();
    if (empty($row)) {
      return redirect("/")->with('error', 'Application Id Valid');
    }
    $today = date("Y-m-d");
    //dd($request->life_certificate_ason_date);
    $rules = [
      'life_certificate_ason_date' => 'required|date|before_or_equal:today',
    ];

    $attributes = array();
    $messages = array();
    $attributes['life_certificate_ason_date'] = 'As on Date';
    $doc_certificate = DocumentType::select('id', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->where("id", $this->life_certificate_id)->first();

    $already_uploaded = 0;
    $encolserdata = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $id)->where('document_type', $this->life_certificate_id)->first();

    if (!empty($encolserdata)) {
      $already_uploaded = 1;
      $required = 'nullable';
    } else {
      $required = 'required';
    }
    $rules['doc_' . $doc_certificate->id] = $required . '|mimes:' . $doc_certificate->doc_type . '|max:' . $doc_certificate->doc_size_kb . ',';
    $messages['doc_' . $doc_certificate->id . '.max'] = "The file uploaded for " . $doc_certificate->doc_name . " size must be less than :max KB";
    $messages['doc_' . $doc_certificate->id . '.mimes'] = "The file uploaded for " . $doc_certificate->doc_name . " must be of type " . $doc_certificate->doc_type;
    $messages['doc_' . $doc_certificate->id . '.required'] = "Document for " . $doc_certificate->doc_name . " must be uploaded";

    // dd( $rules);
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $scheme_row = Scheme::where('id', $scheme_id)->first();
      if (!empty($scheme_row->short_code)) {
        $schema = $scheme_row->short_code;
      } else {
        $schema = "pension";
      }
      $base_url = url('/');
      if ($request->hasFile('doc_' . $doc_certificate->id)) {

        $doc_file = $request->file('doc_' . $doc_certificate->id);

        // $file_passport = $doc_file->getClientOriginalName();
        // $file_type = $doc_file->getClientOriginalExtension();
        // $file_profile = "doc_" . $doc_certificate->id . "_" . rand(10000, 99999) . '_' . time() . '.' . $doc_file->getClientOriginalExtension();
        // $destinationPath = storage_path('app/keep_ICAD/');
        // $fileStore = $doc_file->move($destinationPath, $file_profile);
        $file_content = file_get_contents($doc_file);
        $extension = $doc_file->getClientOriginalExtension();
        $mime_type = $doc_file->getMimeType();
        $base64 = base64_encode($file_content);
        $ip_address = request()->ip();
        $c_datetime = date('Y-m-d H:i:s', time());
      }
      $c_time = date("Y-m-d h:i:s");
      DB::beginTransaction();
      $input = ['next_level_role_id_edit' => 1, 'unlock_status' => 1, 'life_certificate_ason_date' => $request->life_certificate_ason_date];
      $is_saved1 = DB::table($schema . '.beneficiary')->where('id', $id)->where('id', $id)->where('created_by_dist_code', $distCode)->update($input);

      // if( $already_uploaded==1){
      //     $input2 = ['is_active'=>FALSE];
      //     $is_saved2 = DB::table($schema . '.ben_docs')->where('ben_id',$id)->where('doc_type_id', $this->life_certificate_id)->where('is_active',TRUE)->update($input2);

      // }
      // else{
      //     $is_saved2=1;  
      // }
      if ($request->hasFile('doc_' . $doc_certificate->id)) {
        // $insert_doc_type_arr=array();
        // $insert_doc_type_arr['ben_id']=$id;
        // $insert_doc_type_arr['is_active']=TRUE;
        // $insert_doc_type_arr['doc_type_id']=$this->life_certificate_id;
        // $insert_doc_type_arr['doc_type_name']=$doc_certificate->doc_name;
        // $insert_doc_type_arr['doc_name']=$base_url . '/images_icad/' . $file_profile;
        // $insert_doc_type_arr['created_at']=$c_time;
        // $is_saved3 = DB::table($schema . '.ben_docs')->insert($insert_doc_type_arr);
        $is_saved2 = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
              in_beneficiary_id => " . $id . ",
              in_scheme_id => " . $scheme_id . ",
              in_document_type => " . $doc_certificate->id . ",
              in_attched_document => '" . $base64 . "',
              in_created_by_level => '" . $level . "',
              in_created_by => " . $user_id . ",
              in_ip_address => '" . $ip_address . "',
              in_document_extension => '" . $extension . "',
              in_document_mime_type => '" . $mime_type . "',
              in_created_by_dist_code => " . $distCode . ",
              in_created_by_local_body_code => " . $blockCode . ",
              in_doc_type_name => '" . $doc_certificate->doc_name . "',
              in_datetime => '" . $c_datetime . "'
              );"
        );
      }
      // else{
      //     $is_saved3=1; 
      // }
      $accept_reject_model = new AcceptRejectInfo;
      $accept_reject_model->created_at = $c_time;
      $accept_reject_model->application_id = $row->id;
      $accept_reject_model->scheme_id = $row->scheme_id;
      $accept_reject_model->user_id = $user_id;
      $accept_reject_model->created_by_dist_code = $distCode;
      $accept_reject_model->created_by_local_body_code = $blockCode;
      $accept_reject_model->op_type = 'WL';
      $accept_reject_model->ip_address = $ip_address;
      $accept_reject_model->action_by = $user_id;
      $accept_reject_model->action_ip_address = $request->ip();
      $accept_reject_model->action_type = $request->class_basename(request()->route()->getAction()['controller']) . '@' . 'WL';
      $is_saved_log = $accept_reject_model->save();
      if ($is_saved1 && $is_saved2 && $is_saved_log) {
        DB::commit();
        $return_text = 'Beneficiary Life Certificate Uploaded Successfully';
        return redirect("/lifeCertificte?scheme_id=" . $scheme_id)->with('success', $return_text)->with('id', $id);
      } else {
        $return_text = 'Error. Please try again';
        $return_msg = array("" . $return_text);
        return redirect("/editLifeCertificate?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
      }

    } else {
      $return_msg = $validator->errors()->all();
      // dd($return_msg);
      return redirect("/editLifeCertificate?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg)->withInput(Input::all());
    }
  }

  public function bulkApproveLifeCertificate(Request $request)
  {
    // $designation_id_old = Auth::user()->designation_id_old;
    $user_id = AuthChecker::getUserId();

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
    if (AuthChecker::VerifierChecker()) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $district_code = $duty_obj->district_code;
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }


    $c_time = date('Y-m-d H:i:s', time());
    $accept_reject_model = new AcceptRejectInfo;
    $accept_reject_model->created_at = $c_time;
    $accept_reject_model->scheme_id = $scheme_id;
    $accept_reject_model->user_id = $user_id;
    $accept_reject_model->created_by_dist_code = $district_code;
    $accept_reject_model->ip_address = request()->ip();
    $accept_reject_model->op_type = 'WG';
    $accept_reject_model->action_by = $user_id;
    $accept_reject_model->action_ip_address = $request->ip();
    $accept_reject_model->action_type = $request->class_basename(request()->route()->getAction()['controller']) . '@' . 'WG';
    $back_url = 'lifeCertificte?scheme_id=' . $scheme_id;

    $inputs = request()->input('approvalcheck');
    $i = 0;
    try {
      DB::beginTransaction();
      foreach ($inputs as $input_id) {
        $query = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)
          ->where('id', $input_id)->where('next_level_role_id_edit', 1)->where('next_level_role_id', 0);
        $row = $query->first();
        $input = [
          'next_level_role_id_edit' => 0
        ];
        $accept_reject_model->application_id = $input_id;
        $is_saved_log = $accept_reject_model->save();
        $update = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)
          ->where('id', $input_id)->update($input);
        $is_saved_log = $accept_reject_model->save();
        if ($update && $is_saved_log) {
          $i++;
        }
      }

    } catch (\Exception $e) {
      DB::rollback();
      return redirect($back_url)->with('error', 'Error! Please try again.');
    }

    if ($i == count($inputs)) {

      DB::commit();
      return redirect($back_url)->with('message', 'Beneficiaries Life Certiciate Changes has been Approved Succesfully!');
    } else {

      DB::rollback();
      return redirect($back_url)->with('error', 'Error! Please try again.');
    }
  }
  public function SingleApproveLifeCertificate(Request $request)
  {
    // $designation_id_old = Auth::user()->designation_id_old;

    $user_id = AuthChecker::getUserId();

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
    $district_code = $duty_obj->district_code;
    if (!AuthChecker::VerifierChecker()) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $id = $request->id;
    if (empty($id)) {
      return redirect("/")->with('error', 'Application Id Not Found');
    }
    if (!ctype_digit($id)) {
      return redirect("/")->with('error', 'Application Id Valid');
    }
    $district_code = $duty_obj->district_code;
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    $c_time = date('Y-m-d H:i:s', time());

    $back_url = 'lifeCertificte?scheme_id=' . $scheme_id;
    if ($request->action_type == 'Back to Operator') {
      $accept_reject_model = new AcceptRejectInfo;
      $accept_reject_model->created_at = $c_time;
      $accept_reject_model->scheme_id = $scheme_id;
      $accept_reject_model->user_id = $user_id;
      $accept_reject_model->created_by_dist_code = $district_code;
      $accept_reject_model->ip_address = request()->ip();
      $accept_reject_model->op_type = 'WG';
      $accept_reject_model->application_id = $id;
      $accept_reject_model->action_by = $user_id;
      $accept_reject_model->action_ip_address = $request->ip();
      $accept_reject_model->action_type = $request->class_basename(request()->route()->getAction()['controller']) . '@' . 'WG';
      $input = ['next_level_role_id_edit' => NULL, 'unlock_status' => 1];
      $is_saved_log = $accept_reject_model->save();
      $is_saved1 = DB::table($schema . '.beneficiary')->where('id', $id)->where('created_by_dist_code', $district_code)->update($input);
      if ($is_saved1 && $is_saved_log) {
        DB::commit();
        $return_text = 'Beneficiaries Life Certiciate Changes has been Back to Operator Succesfully';
        return redirect("/lifeCertificte?scheme_id=" . $scheme_id)->with('success', $return_text)->with('id', $id);
      } else {
        DB::rollback();
        return redirect($back_url)->with('error', 'Error! Please try again.');
      }
    } else if ($request->action_type == 'Verify') {
      $accept_reject_model = new AcceptRejectInfo;
      $accept_reject_model->created_at = $c_time;
      $accept_reject_model->scheme_id = $scheme_id;
      $accept_reject_model->user_id = $user_id;
      $accept_reject_model->created_by_dist_code = $district_code;
      $accept_reject_model->ip_address = request()->ip();
      $accept_reject_model->op_type = 'WG';
      $accept_reject_model->application_id = $id;
      $input = ['next_level_role_id_edit' => 0, 'unlock_status' => 2];
      $is_saved_log = $accept_reject_model->save();
      $is_saved1 = DB::table($schema . '.beneficiary')->where('id', $id)->where('created_by_dist_code', $district_code)->update($input);
      if ($is_saved1 && $is_saved_log) {
        DB::commit();
        $return_text = 'Beneficiaries Life Certiciate Changes has been Verified Succesfully';
        return redirect("/lifeCertificte?scheme_id=" . $scheme_id)->with('success', $return_text)->with('id', $id);
      } else {
        DB::rollback();
        return redirect($back_url)->with('error', 'Error! Please try again.');
      }
    }


  }



}
