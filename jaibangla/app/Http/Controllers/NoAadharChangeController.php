<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\District;
use App\Scheme;
use Redirect;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Validator;
use DateTime;
use Config;
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
use Exception;
use App\Traits\TraitAadharValidate;
use App\Helpers\DupCheck;

class NoAadharChangeController extends Controller
{
  use TraitAadharValidate;
  public function __construct()
  {

    $this->scheme_id = 20;
    $this->source_type = 'ss_nfsa';
    $this->ben_status = -97;
    $this->doc_type_id = 6;
  }
  public function shemeSelection(Request $request)
  {
    try {
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = Auth::user()->id;
      if ($designation_id_old == 'Operator' || $designation_id_old == 'Verifier' || $designation_id_old == 'Approver' || $designation_id_old == 'HOD' || $designation_id_old == 'DASHBOARD') {
        $schemes = DB::select(DB::raw("select id,scheme_name,display_name,is_active from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
        //dd($schemes);
        return view(
          'NoAadhaar/SchemeSelection',
          [

            'scheme_list' => $schemes,
          ]
        );
      } else {
        return redirect("/")->with('danger', 'Not Allowed');
      }
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function list(Request $request)
  {
    $this->middleware('auth');
    $designation_id_old = Auth::user()->designation_id_old;
    //dd($designation_id_old);
    $user_id = Auth::user()->id;

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


    $type_des = 'Approved Beneficiary with No Aadhar';

    //dd($type_des);
    $district_code = $duty_obj->district_code;
    $urban_bodys = collect([]);
    $gps = collect([]);
    $district_list_obj = collect([]);
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length =  $scheme_obj->scheme_length;
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
      $application_type = $request->application_type;
      $process_type = $request->process_type;
      $query = DB::table($schema . '.beneficiaries')
        ->where('no_aadhar', 1)->where('created_by_dist_code', $district_code)->where('next_level_role_id', 0);
      if (($designation_id_old == 'Verifier')) {
        $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
        if (!empty($application_type)) {
          if ($application_type == 1)
            $query = $query->whereNull('aadhar_edit_role_id');
          if ($application_type == 2)
            $query = $query->where('aadhar_edit_role_id', 1);
          if ($application_type == 3)
            $query = $query->where('aadhar_edit_role_id', 2);
        }
      }
      if (($designation_id_old == 'Approver' && ($scheme_id == 8 || $scheme_id == 9))) {

        if (!empty($application_type)) {
          if ($application_type == 1)
            $query = $query->whereNull('aadhar_edit_role_id');

          if ($application_type == 2)
            $query = $query->where('aadhar_edit_role_id', 1);
          if ($application_type == 3)
            $query = $query->where('aadhar_edit_role_id', 2);
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
      if ($designation_id_old == 'Approver' && ($scheme_id != '8' && $scheme_id != '9')) {
        if ($application_type != '') {
          if ($application_type == 1)
            $query = $query->where('aadhar_edit_role_id', 1);
          if ($application_type == 3)
            $query = $query->where('aadhar_edit_role_id', 2);
        }
      }

      $serachvalue = $request->search['value'];
      if (empty($serachvalue)) {
        $totalRecords = $query->count();
        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
          'id', 'created_by_dist_code', 'dob', 'assembly_name',
          'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
          'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'aadhar_edit_role_id','payment_suspended'
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
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'aadhar_edit_role_id','payment_suspended'
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
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'aadhar_edit_role_id','payment_suspended'
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
        })->addColumn('view', function ($data) use ($scheme_id, $designation_id_old) {

          if ($scheme_id == '8' || $scheme_id == '9') {

            $action = '';
            if (is_null($data->aadhar_edit_role_id)) {
                $action = '<a href="Viewnoaadhar?id=' . $data->id  . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
            } else if ($data->aadhar_edit_role_id == 2) {
              $action = 'Approved';
            }
          }
          if ($designation_id_old == 'Verifier') {
            $action = '';
            if (is_null($data->aadhar_edit_role_id)) {
              if($data->payment_suspended == 1){
                $action = '<b>Mark due to JNMP</b>';
              }else{
                $action = '<a href="Viewnoaadhar?id=' . $data->id  . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
              }
            } else if ($data->aadhar_edit_role_id == 1) {
              $action = 'Approval Pending';
            } else if ($data->aadhar_edit_role_id == 2) {
              $action = 'Approved';
            }
          }

          if ($designation_id_old == 'Approver' && ($scheme_id != '8' && $scheme_id != '9')) {

            $action = '';

            if ($data->aadhar_edit_role_id == 1) {
              $action = '<a href="Viewnoaadhar?id=' . $data->id  . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
            } else if ($data->aadhar_edit_role_id == 2) {
              $action = 'Approved';
            }
          }

          return $action;
        })->addColumn('check', function ($data) use ($designation_id_old) {
          if ($designation_id_old == 'Approver') {
            if ($data->aadhar_edit_role_id == 1) {
              return '<input type="checkbox" name="approvalcheck[]" onClick="controlCheckBox()" value="' . $data->id . '">';
            } else
              return '';
          } else {
            return '';
          }
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
      'NoAadhaar.linelisting',
      [
        'designation_id_old' => $designation_id_old,
        'verifier_type' => $verifier_type,
        'created_by_local_body_code' => $created_by_local_body_code,
        'is_rural' => $is_rural,
        'scheme_id' => $scheme_id,
        'scheme_name' => $scheme_obj->scheme_name,
        'gps' => $gps,
        'urban_bodys' => $urban_bodys,
        'gps' => $gps,
        'district_code' => $district_code,
        'type_des' => $type_des,
        'scheme_id' => $scheme_id
      ]
    );
  }
  public function Viewnoaadhar(Request $request)
  {
    //dd('ok');
    try {
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = Auth::user()->id;
      $id = $request->id;
      // dd($id);
      if (empty($request->id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }


      $duty_obj = Configduty::where('user_id', $user_id)->first();
      // dd($duty_obj);
      // if (empty($duty_obj)) {
      //   return redirect("/")->with('danger', 'Not Allowed');
      // }
      $scheme_id = $request->scheme_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $type_des = 'Approved Beneficiary With No Aadhaar';
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $query = DB::table($schema . '.beneficiaries')
        ->where('no_aadhar', 1)->where('created_by_dist_code', $district_code)->where('id', $id)->where('next_level_role_id', 0);
      $row = $query->first();
      if($row->payment_suspended == 1){
        // return redirect("/Viewnoaadhar?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errorMsg);
       
        $errorMsg = 'Mark due to JNMP';
        return redirect("/")->with('danger', $errorMsg);
      }
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      //dd($row->aadhar_no);
      if ($designation_id_old == 'Verifier') {
        if (!empty($row->aadhar_no) && trim($row->aadhar_no) != '') {
          $old_aadhar = $row->aadhar_no;
          $new_aadhar = '';
        } else {
          $old_aadhar = '';
          $new_aadhar = '';
        }
      } else {
        if (!empty($row->old_aadhar_no)) {
          $old_aadhar = $row->old_aadhar_no;
        } else {
          $old_aadhar = '';
        }
        $new_aadhar = $row->aadhar_no;
        //dd($new_aadhar);
      }
      if (($designation_id_old == 'Approver') && ($scheme_id == '8' || $scheme_id == '9')) {
        if (!empty($row->aadhar_no) && trim($row->aadhar_no) != '') {
          $old_aadhar = $row->aadhar_no;
          $new_aadhar = '';
        } else {
          $old_aadhar = '';
          $new_aadhar = '';
        }
      } else {
        if (!empty($row->old_aadhar_no)) {
          $old_aadhar = $row->old_aadhar_no;
        } else {
          $old_aadhar = '';
        }
        $new_aadhar = $row->aadhar_no;
        //dd($new_aadhar);
      }

      $reject_revert_cause_list = RejectRevertReason::where('status', true)->get();
      if ($row->dist_code != "") {
        $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
        $district_name = $district->district_name;
      }
      $block_name = "";
      if ($row->block_ulb_code != "") {
        if ($row->rural_urban_id == 1) {
          $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
          if (!empty($block)) {
            $block_name = $block->urban_body_name;
          }
        } else {
          if (!empty($row->block_ulb_code)) {
            $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
            if (!empty($block)) {
              $block_name = $block->block_name;
            } else {
              $block_name = '';
            }
          } else {
            $block_name = '';
          }
        }
      }
      $row->block_name = $block_name;
      $gp_name = "";
      if ($row->gp_ward_code != "") {
        if ($row->rural_urban_id == 1) {
          $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
          if (!empty($gp_ward)) {
            $gp_name =  $gp_ward->urban_body_ward_name;
          }
        } else {
          $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
          if (!empty($gp)) {
            $gp_name =  $gp->gram_panchyat_name;
          }
        }
      }

      $row->gp_name = $gp_name;
      $doc_type_id = $this->doc_type_id;
      $docs = '';
      $doc_man = DocumentType::get(['id', 'doc_name', 'doc_type', 'doc_mime_type', 'doc_size_kb'])->where("id", $doc_type_id)->first();
      $image = '';
      $row_image = '';
      $image_extension = '';
      $decrypt_aadhar_old = '';
      $decrypt_aadhar_new = '';
      $docs_new = '';
      $docs_new = '';

      $encolserdata = BenDocs::where('beneficiary_id', $id)->where('document_type', $doc_type_id)->first();

      if (!empty($encolserdata)) {
        $mime_type = $encolserdata->document_mime_type;
        $image_extension = $encolserdata->document_extension;
        if ($image_extension != 'png' && $image_extension != 'jpg' && $image_extension != 'jpeg') {
          if ($mime_type == 'image/png') {
            $image_extension = 'png';
          } else if ($mime_type == 'image/jpeg') {
            $image_extension = 'jpg';
          }
        }
        $resultimg = str_replace("data:image/" . $image_extension . ";base64,", "", $encolserdata->attched_document);
        $row_image = "data:image/" . $image_extension . ";base64," . $encolserdata->attched_document;
        $file_name = $encolserdata->document_type . '_' . $encolserdata->application_id;
        $image = base64_decode($row_image);
      }



      return view(
        'NoAadhaar.ViewBeneficiary',
        [
          'designation_id_old' => $designation_id_old,
          'row' => $row,
          'id' => $id,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'doc_man' => $doc_man,
          'docs' => $docs,
          'image' => $row_image,
          'ext' => $image_extension,
          'decrypt_aadhar_old' => $decrypt_aadhar_old,
          'decrypt_aadhar_new' => $decrypt_aadhar_new,
          'docs_new' => $docs_new,
          'reject_revert_cause_list' => $reject_revert_cause_list,
          'old_aadhar' => $old_aadhar,
          'new_aadhar' => $new_aadhar,
          'scheme_id' => $scheme_id,
          'doc_type_id' => $doc_type_id,
        ]
      );
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function noaadharPost(Request $request)
  {
    // echo 1;die;
    try {
      $this->middleware('auth');
      $doc_type_id = $this->doc_type_id;
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = Auth::user()->id;
      if (empty($request->id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }

     
      $scheme_id = $request->scheme_id;
      $id = $request->id;

     

      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id',$scheme_id)->first();

      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $type_des = 'Approved Beneficiary With No Aadhaar';
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $type_des = 'Approved Beneficiary With No Aadhaar';
      $district_code = $duty_obj->district_code;
      $condition = array();
      $condition['id'] = $request->id;
      $district_code = $duty_obj->district_code;

      if ($designation_id_old == 'Verifier') {
        if ($duty_obj->mapping_level == "Subdiv") {
          $created_by_local_body_code = $duty_obj->urban_body_code;
        }
        if ($duty_obj->mapping_level == "Block") {
          $created_by_local_body_code = $duty_obj->taluka_code;
        }
        $condition['created_by_local_body_code'] = $created_by_local_body_code;
      }
      if ($designation_id_old == 'Approver') {
        $condition['created_by_dist_code'] = $district_code;
      }


      $query = DB::table($schema . '.beneficiary')
        ->where('no_aadhar', 1)->where($condition)->where('id', $id)->where('next_level_role_id', 0)->get();


      $row = $query->first();
      $created_by_local_body_code = $row->created_by_local_body_code;
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if (empty(trim($request->aadhaar_no))) {
        $errors = array();
        $errorMsg = "Aadhaar Number Required";
        array_push($errors, $errorMsg);
      } else {
        if (strlen(trim($request->aadhaar_no)) != 12) {
          $errors = array();
          $errorMsg = "Aadhaar Number Invalid";
          array_push($errors, $errorMsg);
        }
        if ($this->isAadharValid(trim($request->aadhaar_no)) == false) {
          $errors = array();
          $errorMsg = "Aadhaar Number Invalid";
          array_push($errors, $errorMsg);
        }
      }

      $doc_row = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->where('id', $doc_type_id)->first();
      $doc_man = DocumentType::get(['id', 'doc_name', 'doc_type', 'doc_mime_type', 'doc_size_kb'])->where("id", $doc_type_id)->first();

      if ($request->file('doc_' . $doc_type_id)) {
        $image_file = $request->file('doc_' . $doc_type_id);
        $img_data = file_get_contents($image_file);
        $image_extension = $image_file->getClientOriginalExtension();
        $mime_type = $image_file->getMimeType();
        $image_size = $image_file->getSize();
        $image_size = $image_size / 1024; // Get file size in KB
        if ($image_size >  $doc_man->doc_size_kb) {
          $errors = array();
          $errorMsg = 'File Size must be.' . $doc_man->doc_size_kb . ' KB';
          array_push($errors, $errorMsg);
        }
        if ($mime_type == 'image/png' || $mime_type == 'image/jpeg' || $mime_type == 'image/jpg' || $mime_type == 'application/pdf') {
          // echo "IF";die;
          $base64 = base64_encode($img_data);
          $is_error = 0;
        } else {
          $errors = array();
          $errorMsg = 'File must be proper format';
          array_push($errors, $errorMsg);
          $is_error = 1;
        }
        if ($is_error == 0) {
          $encolserdata = BenDocs::where('beneficiary_id', $id)->where('document_type', $doc_type_id)->first();

          if (!empty($encolserdata)) {
            $pre_aadhar = 1;
          } else {
            $pre_aadhar = 0;
          }
          //dd($pre_aadhar);
          $c_time = date('Y-m-d H:i:s', time());
          $pension_details_encloser2 = new BenDocs();
          $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
          $count =  DB::table($schema . '.beneficiary')->where('aadhar_no', trim($request->aadhaar_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');

          if ($count > 0) {
            $errors = array();
            $errorMsg = "Aadhaar Number Already Exist in the scheme! Please try different.";
            array_push($errors, $errorMsg);
            return redirect("/Viewnoaadhar?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errors);
          }
          if($scheme_id == 10 || $scheme_id == 11 ||$scheme_id == 1 ||$scheme_id == 3){
            if($scheme_id == 10){
              $aadharDupCheckWP = DupCheck::getDupCheckAadhar(11,$request->aadhaar_no);
              if(!empty($aadharDupCheckWP)){
                $errors = array();
                $errorMsg = "Duplicate Aadhaar Number present in Widow Pension Scheme with Beneficiary ID- $aadharDupCheckWP";
                array_push($errors, $errorMsg);
                return redirect("/Viewnoaadhar?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errors);
              }

              $aadharDupCheckLB = DupCheck::getDupCheckAadhar(20,$request->aadhaar_no);
              if(!empty($aadharDupCheckLB)){
                $errors = array();
                $errorMsg = "Duplicate Aadhaar Number present in Lakshmir Bhandar Scheme with Application ID- $aadharDupCheckLB";
                array_push($errors, $errorMsg);
                return redirect("/Viewnoaadhar?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errors);
              }
            }
            if($scheme_id == 11){
              $aadharDupCheckOAP = DupCheck::getDupCheckAadhar(10,$request->aadhaar_no);
              if(!empty($aadharDupCheckOAP)){
                $errors = array();
                $errorMsg = "Duplicate Aadhaar Number present in Old Age Pension Scheme with Beneficiary ID- $aadharDupCheckOAP";
                array_push($errors, $errorMsg);
                return redirect("/Viewnoaadhar?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errors);
              }
            }
            if($scheme_id == 1 || $scheme_id == 3){
              $aadharDupCheckLB = DupCheck::getDupCheckAadhar(20,$request->aadhaar_no);
              if(!empty($aadharDupCheckLB)){
                $errors = array();
                $errorMsg = "Duplicate Aadhaar Number present in Lakshmir Bhandar Scheme with Application ID- $aadharDupCheckLB";
                array_push($errors, $errorMsg);
                return redirect("/Viewnoaadhar?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errors);
              }
              $aadharDupCheckOAP = DupCheck::getDupCheckAadhar(10,$request->aadhaar_no);
              if(!empty($aadharDupCheckOAP)){
                $errors = array();
                $errorMsg = "Duplicate Aadhaar Number present in Old Age Pension Scheme with Beneficiary ID- $aadharDupCheckOAP";
                array_push($errors, $errorMsg);
                return redirect("/Viewnoaadhar?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errors);
              }
            }
          }
          DB::beginTransaction();
          DB::connection('pgsql_encwrite')->beginTransaction();
          
          if ($pre_aadhar == 1) {

            $is_inserted_arch = DB::connection('pgsql_encwrite')->statement("INSERT INTO jb_doc.ben_attach_documents_arch(
                beneficiary_id, document_type, attched_document, scheme_id,
	created_by_level, created_at, updated_at, deleted_at, created_by, 
	ip_address, document_extension, document_mime_type, created_by_dist_code, 
	created_by_local_body_code)
              SELECT  beneficiary_id, document_type, attched_document,scheme_id,
	created_by_level, created_at, updated_at, deleted_at, created_by, 
	ip_address, document_extension, document_mime_type, created_by_dist_code, 
	created_by_local_body_code FROM jb_doc.ben_attach_documents where document_type='" . $doc_type_id . "' and beneficiary_id='" . $request->id . "'");
            $enc_details = array();

            $enc_details['updated_at'] = $c_time;
            $enc_details['document_type'] = $doc_type_id;
            $enc_details['attched_document'] = $base64;
            $enc_details['document_extension'] = $image_extension;
            $enc_details['document_mime_type'] = $mime_type;

            $enc_status = $pension_details_encloser2
              ->where('scheme_id', $scheme_id)
              ->where('document_type', $doc_type_id)
              ->where('created_by_dist_code', $district_code)
              ->where('beneficiary_id', $request->id)
              ->update($enc_details);
          } else {

            $is_inserted_arch = 1;
            $enc_details = array();
            $enc_details['scheme_id'] =  $scheme_id;
            $enc_details['beneficiary_id'] =  $request->id;
            $enc_details['created_at'] = $c_time;
            $enc_details['document_type'] = $doc_type_id;
            $enc_details['attched_document'] = $base64;
            $enc_details['document_extension'] = $image_extension;
            $enc_details['document_mime_type'] = $mime_type;
            $enc_details['created_by_level'] = $duty_obj->mapping_level;
            $enc_details['created_by'] = $user_id;
            $enc_details['ip_address'] = $request->ip();
            $enc_details['created_by_dist_code'] = $district_code;
            if ($designation_id_old == 'Verifier') {
              $enc_details['created_by_local_body_code'] = $created_by_local_body_code;
            }
            $enc_status = $pension_details_encloser2->insert($enc_details);
          }




          $inputMain = array();
          if ($designation_id_old == 'Verifier') {
            $inputMain['aadhar_edit_role_id'] = 1;
          }
          if ($designation_id_old == 'Approver' && ($scheme_id == 8 || $scheme_id == 9)) {
            $inputMain['aadhar_edit_role_id'] = 2;
            $inputMain['no_aadhar'] = 0;
          }

          $inputMain['aadhar_no'] = trim($request->aadhaar_no);
          if (!empty(trim($row->aadhar_no))) {
            $inputMain['old_aadhar_no'] = trim($row->aadhar_no);
          }

          try {

            $upadated_main = DB::table($schema . '.beneficiary')
              ->where([
                'id' => $request->id, 'created_by_local_body_code' => $created_by_local_body_code,
                'created_by_dist_code' => $district_code
              ])->where('next_level_role_id', 0)->update($inputMain);
          } catch (\Exception $e) {
            // dd($e);
            $errors = array();
            $errorMsg = 'Aadhaar Number already exists.. Please try different.';
            array_push($errors, $errorMsg);
            return redirect("/Viewnoaadhar?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errorMsg);
          }

          $modelNameAcceptReject = new AcceptRejectInfo;
          $op_type = 'NOAADHARUPDATE';
          $modelNameAcceptReject->scheme_id =  $scheme_id;

          $modelNameAcceptReject->created_at =  $c_time;
          $modelNameAcceptReject->op_type =  $op_type;
          $modelNameAcceptReject->application_id = $request->id;
          $modelNameAcceptReject->user_id = $user_id;
          $modelNameAcceptReject->created_by_dist_code = $district_code;
          $modelNameAcceptReject->created_by_local_body_code = $created_by_local_body_code;
          $modelNameAcceptReject->ip_address = request()->ip();
          $is_accept_reject = $modelNameAcceptReject->save();
          $ben_fullname = $request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name;

          // if($request->id==440945)
          // {
    
          // dump($upadated_main);dump($is_accept_reject);dump($enc_status);dd($is_inserted_arch);

          // die;
          // }
          //dump($upadated_main);dump($is_accept_reject);dump($enc_status);dd($is_inserted_arch);
          if ($upadated_main && $is_accept_reject && $enc_status && $is_inserted_arch) {
            DB::commit();
            DB::connection('pgsql_encwrite')->commit();
            try {
              $data = $this->RationcheckInsert($district_code, $request->id, $scheme_id, $ben_fullname, $request->ip(), $request->aadhaar_no, $created_by_local_body_code, $user_id, $request->dob);
            } catch (\Exception $e) {
              $inputMain['aadhaar_no_checked'] = -1;
              $upadated_main = DB::table($schema . '.beneficiary')
              ->where([
                'id' => $request->id, 'created_by_local_body_code' => $created_by_local_body_code,
                'created_by_dist_code' => $district_code
              ])->update($inputMain);
            }
            $ben_details = DB::table($schema . '.beneficiary')->where('id', $request->id)->first();

            if ($ben_details) {
              $aadhaar_no_checked = $ben_details->aadhaar_no_checked;
              $aadhaar_no_checked_lastdatetime = $ben_details->aadhaar_no_checked_lastdatetime;
              $aadhaar_no_checked_pass = $ben_details->aadhaar_no_checked_pass;
              $aadhaar_no_validation_msg = $ben_details->aadhaar_no_validation_msg;
              $errors = array();
              $return_text = 'Beneficiary with  Id:' . $request->id . ' Aadhaar has been changed Successfully and Sent to Approver for Approval';
              return redirect("noaadharlist?scheme_id=" . $scheme_id)->with('success', $return_text)
                ->with('aadhaar_no_checked', $aadhaar_no_checked)
                ->with('aadhaar_no_checked_lastdatetime', $aadhaar_no_checked_lastdatetime)
                ->with('aadhaar_no_checked_pass', $aadhaar_no_checked_pass)
                ->with('aadhaar_no_validation_msg', $aadhaar_no_validation_msg);
            }
          } else {
            DB::rollback();
            DB::connection('pgsql_encwrite')->rollBack();
            $errors = array();
            $errorMsg = 'Aadhaar Information Modification Faild.. Please try different.';
            array_push($errors, $errorMsg);
          }
        }
      } else {
        $errors = array();
        $errorMsg = $doc_row->doc_name . ' Required';
        array_push($errors, $errorMsg);
        return redirect("/Viewnoaadhar?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errorMsg);
      }
      if (count($errors) > 0) {
        return redirect("/Viewnoaadhar?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $errorMsg);
      }
    } catch (\Exception $e) {
      //  dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function bulkApprove(Request $request)
  {
    try {
      //dd('ok');
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      if ($designation_id_old != 'Approver') {
        return redirect("/")->with('error', 'Not Allowed');
      }
      $user_id = Auth::user()->id;
      $duty_obj = Configduty::where('user_id', $user_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $district_code = $duty_obj->district_code;
      $scheme_id = $request->scheme_id;
      $action_type = $request->action_type;
      // die($action_type);
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $applicationid_arr = array();
      $inputs = request()->input('approvalcheck');
      // $backToVerifier = $request->backToVerifier;
      // dd($backToVerifier);
      $c_time = date('Y-m-d H:i:s', time());
      foreach ($inputs as $input) {
        array_push($applicationid_arr, $input);
      }
      $back_url = 'noaadharlist?scheme_id=' . $scheme_id;
      $comments = NULL;
      $i = 0;
      $modelNameAcceptReject = new AcceptRejectInfo;
      $modelBackToVerifier = new AcceptRejectInfo;
      DB::beginTransaction();
      if($action_type == 1){
        foreach ($applicationid_arr as $application_item) {
          $op_type = 'NOAADHARAPPROVE';
          $modelNameAcceptReject->created_at =  $c_time;
          $modelNameAcceptReject->op_type =  $op_type;
          $modelNameAcceptReject->application_id = $application_item;
          $modelNameAcceptReject->user_id = $user_id;
          $modelNameAcceptReject->created_by_dist_code = $district_code;
          $modelNameAcceptReject->ip_address = request()->ip();
          $is_accept_reject = $modelNameAcceptReject->save();
          if ($is_accept_reject) {
            $i++;
          }
        }
        if ($i == count($applicationid_arr)) {
          $is_accept_reject = 1;
        } else {
          $is_accept_reject = 0;
        }
        $inputMain['aadhar_edit_role_id'] = 2;
        $inputMain['no_aadhar'] = 0;
  
        $upadated_main = DB::table($schema . '.beneficiary')->whereIn('id', $applicationid_arr)
          ->where('created_by_dist_code', $district_code)->where('no_aadhar', 1)->where('aadhar_edit_role_id', 1)->update($inputMain);
          if ($upadated_main && $is_accept_reject) {
            DB::commit();
            return redirect($back_url)->with('message', 'Applications Aadhaar information change request has been Approved Succesfully!');
          } else {
            DB::rollback();
            return redirect($back_url)->with('error', 'Error! Please try again..');
          }
      }

      if($action_type == 9){
        foreach ($applicationid_arr as $application_item) {
          $op_type = 'BACKTOVERIFIER';
          $modelNameAcceptReject->created_at =  $c_time;
          $modelNameAcceptReject->op_type =  $op_type;
          $modelNameAcceptReject->application_id = $application_item;
          $modelNameAcceptReject->user_id = $user_id;
          $modelNameAcceptReject->created_by_dist_code = $district_code;
          $modelNameAcceptReject->ip_address = request()->ip();
          $is_accept_reject = $modelNameAcceptReject->save();
          if ($is_accept_reject) {
            $i++;
          }
        }
        if ($i == count($applicationid_arr)) {
          $is_accept_reject = 1;
        } else {
          $is_accept_reject = 0;
        }  
          // die($is_accept_reject_back_to_verifier);
        $inputBackToVerifier['aadhar_edit_role_id'] = null;
        $inputBackToVerifier['no_aadhar'] = 1;
         
            $updated_back_to_verifier = DB::table($schema . '.beneficiary')->where('id', $applicationid_arr)
        ->where('created_by_dist_code', $district_code)->where('aadhar_edit_role_id', 1)->where('no_aadhar', 1)->update($inputBackToVerifier);        
          // dd($updated_back_to_verifier);
        if ($updated_back_to_verifier && $is_accept_reject) {
          DB::commit();
          return redirect($back_url)->with('message', 'Applications Aadhaar information change request has been Back to Verifier Succesfully!');
        }
        else {
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
      }
      
    } catch (\Exception $e) {
      dd($e);
      DB::rollback();
      return redirect($back_url)->with('error', 'Error! Please try again...');
    }
  }



  public function pdf(Request $request)
  {
    try {
      $this->middleware('auth');

      $application_id = $request->application_id;
      $roleArray = $request->session()->get('role');
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = Auth::user()->id;
      //$user_id = Auth::user()->id;
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
        return redirect("/")->with('error',  $return_text);
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
        return redirect("/")->with('error',  $return_text);
      }
    } catch (\Exception $e) {
      $return_text = 'Some error. please try again.';
      return redirect("/")->with('error',  $return_text);
    }
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
  function misReport(Request $request)
  {
    $base_date  = '2020-01-01';
    $c_time = Carbon::now();
    $c_date = $c_time->format("Y-m-d");
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    $designation_id_old = Auth::user()->designation_id_old;
    $district_visible = $is_urban_visible = $block_visible = 1;
    $municipality_visible = 0;
    $gp_ward_visible = 0;
    $muncList = collect([]);
    $gpList = collect([]);
    $userId = Auth::user()->id;
    $scheme_code_in = array();
    $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
    foreach ($scheme_list as $scheme_item) {
      array_push($scheme_code_in, $scheme_item->id);
    }
    if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
      $district_visible = $is_urban_visible = $block_visible = 1;
    } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
      $district_code = NULL;
      $is_urban = NULL;
      $blockCode = NULL;
      $scsctvisible = 0;
      foreach ($roleArray as $roleObj) {
        if (in_array($roleObj['scheme_id'], $scheme_code_in)) {
          $is_urban = $roleObj['is_urban'];
          $district_code = $roleObj['district_code'];
          if ($roleObj['is_urban'] == 1) {
            $blockCode = $roleObj['urban_body_code'];
            $muncList = UrbanBody::select('urban_body_code', 'urban_body_name')->where('sub_district_code', $blockCode)->get();
            $municipality_visible = 1;
          } else {
            $blockCode = $roleObj['taluka_code'];
            $gpList = GP::select('gram_panchyat_code', 'gram_panchyat_name')->where('block_code', $blockCode)->get();
          }
          break;
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
      $gp_ward_visible = 1;
    } else {
      $block_munc_corp_code_fk = NULL;
      $gp_ward_visible = 0;
    }
    $districts = District::get();
    //$is_urban_visible=0;
    $block_visible = 0;
    $municipality_visible = 0;
    $gp_ward_visible = 0;
    return view(
      'NoAadhaar.misreport',
      [
        'scheme_list' => $scheme_list,
        'districts' => $districts,
        'district_visible' => $district_visible,
        'district_code_fk' => $district_code_fk,
        'is_urban_visible' => $is_urban_visible,
        'rural_urban_fk' => $rural_urban_fk,
        'block_visible' => $block_visible,
        'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
        'municipality_visible' => $municipality_visible,
        'gp_ward_visible' => $gp_ward_visible,
        'is_urban_visible' => $is_urban_visible,
        'base_date' => $base_date,
        'c_date' => $c_date,
        'gpList' => $gpList,
        'muncList' => $muncList,
        'designation_id_old' => $designation_id_old,
      ]
    );
  }
  public function misReportPost(Request $request)
  {
    //$ds_phase_list = Config::get('constants.ds_phase.phaselist');
    $scheme_id = $request->scheme_id;
    $ds_phase = $request->ds_phase;
    $district = $request->district;
    $urban_code = $request->urban_code;
    $block = $request->block;
    $muncid = $request->muncid;
    $gp_ward = $request->gp_ward;
    // dd($gp_ward);
    $base_date  = '2020-08-16';
    $c_time = Carbon::now();
    $c_date = $c_time->format("Y-m-d");
    $heading_msg = '';
    $title = "";
    //$block_condition = "";
    if (!empty($district)) {
      $district_row = District::where('district_code', $district)->first();
    }

    if (!empty($block)) {

      if ($urban_code == 1) {
        $block_ulb = SubDistrict::where('sub_district_code', '=', $block)->first();
        $blk_munc_name = $block_ulb->sub_district_name;
        //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
      } else {
        $block_ulb = Taluka::where('block_code', '=', $block)->first();
        $blk_munc_name = $block_ulb->block_name;
        // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
      }
    } else {
      // $block_condition = "";
    }
    if (!empty($gp_ward)) {

      if ($urban_code == 1) {
        $gp_ward_row = Ward::where('urban_body_ward_code', '=', $gp_ward)->first();
        $gp_ward_name = $gp_ward_row->urban_body_ward_name;
        //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
      } else {
        $gp_ward_row = GP::where('gram_panchyat_code', '=', $gp_ward)->first();
        $gp_ward_name = $gp_ward_row->gram_panchyat_name;
        // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
      }
    }
    $rules = [
      'scheme_id' => 'required|integer',
      'district' => 'nullable|integer',
      'urban_code' => 'nullable|integer',
      'block' => 'nullable|integer',
      'muncid' => 'nullable|integer',
      'gp_ward' => 'nullable|integer',
      'from_date'    => 'nullable|date|after_or_equal:' . $base_date . '|before_or_equal:' . $c_date,
      'to_date'      => 'nullable|date|after_or_equal:from_date|before_or_equal:' . $c_date,
    ];
    $data = array();
    $column = "";
    $attributes = array();
    $messages = array();
    $attributes['scheme_id'] = 'Scheme';
    $attributes['district'] = 'District';
    $attributes['urban_code'] = 'Rural/ Urban';
    $attributes['block'] = 'Block/Sub Division';
    $attributes['muncid'] = 'Municipality';
    $attributes['gp_ward'] = 'GP/Ward';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $scheme_row = Scheme::where('id', $scheme_id)->first();
      $user_msg = "No Aadhaar Mis Report for the Scheme " . $scheme_row->scheme_name . ' among Approved Beneficiary';
      $title = $user_msg;
      //dd($title);

      $data = array();
      $return_status = 1;
      $return_msg = '';
      $heading_msg = '';
      $external = 0;
      $external_arr = array();
      $external_filter = array();
      $from_date = NULL;
      $to_date = NULL;
      $caste = NULL;
      $ds_phase = NULL;
      if (!empty($gp_ward)) {
        if ($urban_code == 1) {
          $column = "Ward";
          $heading_msg =  $user_msg . ' of the Ward ' . $gp_ward_name;
          $data = $this->getWardWise($scheme_id, $district, $block, $muncid, $gp_ward, $from_date, $to_date, $caste, $ds_phase);
        } else {
          $column = "GP";
          $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
          $data = $this->getGpWise($scheme_id, $district, $block, NULL, $gp_ward, $from_date, $to_date, $caste, $ds_phase);
        }
      } else if (!empty($muncid)) {
        $column = "Ward";
        $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
        $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
        $data = $this->getWardWise($scheme_id, $district, $block, $muncid, NULL, $from_date, $to_date, $caste, $ds_phase);
      } else if (!empty($block)) {
        if ($urban_code == 1) {
          $column = "Municipality";
          $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
          $data = $this->getMuncWise($scheme_id, $district, $block, NULL, NULL, $from_date, $to_date, $caste, $ds_phase);
        } else if ($urban_code == 2) {
          $block_arr = Taluka::where('block_code', '=', $block)->first();
          $column = "GP";
          $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
          $data = $this->getGpWise($scheme_id, $district, $block, NULL, $gp_ward, $from_date, $to_date, $caste, $ds_phase);
        }
      } else {

        if (!empty($district)) {
          if ($urban_code == 1) {
            $column = "Sub Division";
            $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $data = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase);
          } else if ($urban_code == 2) {
            $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block";
            $data = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase);
          } else {
            $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block/Sub Division";
            $data1 = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase);
            $data2 = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase);
            $data = array_merge($data1, $data2);
          }
        } else {
          $column = "District";
          $heading_msg = 'District Wise ' . $user_msg;
          $data = $this->getDistrictWise($scheme_id, NULL, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase);

          $external = 0;
        }
      }
      if (!empty($caste)) {
        $heading_msg = $heading_msg . " for the Caste  " . $caste;
      }
      if (!empty($ds_phase)) {
        $heading_msg = $heading_msg . " of the " . $ds_phase_list[$ds_phase];
      }
      if (!empty($from_date)) {
        $form_date_formatted = \Carbon\Carbon::parse($from_date)->format('d-m-Y');
        $heading_msg = $heading_msg . " from " . $form_date_formatted;
      }
      if (!empty($to_date)) {
        $to_date_formatted = \Carbon\Carbon::parse($to_date)->format('d-m-Y');
        $heading_msg = $heading_msg . " to  " . $to_date_formatted;
      }
    } else {
      $return_status = 0;
      $return_msg = $validator->errors()->all();
    }
    return response()->json([
      'return_status' => $return_status,
      'return_msg' => $return_msg,
      'row_data' => $data,
      'column' => $column,
      'title' => $title,
      'heading_msg' => $heading_msg
    ]);
  }



  public function getBlockWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL)
  {
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }
    $whereMain = "where  district_code=" . $district_code;
    $query = "select main.location_id,main.location_name,
        COALESCE(bp_main.total,0) as total,
        COALESCE(bp_main.action_pending,0) as action_pending,
        COALESCE(bp_main.approval_pending,0) as approval_pending,
        COALESCE(bp_main.approved,0) as approved,
        COALESCE(bp_main.rejected,0) as rejected
        from
        (
          select block_code as location_id,'Block-'||block_name as location_name
          from public.m_block  " . $whereMain . " 
        ) as main LEFT JOIN
        (
              select count(1)  as total,
              count(1) filter(where aadhar_edit_role_id IS NULL and no_aadhar=1 and is_rejected=0 and next_level_role_id=0) as action_pending,
              count(1) filter(where aadhar_edit_role_id=1 and  no_aadhar=1 and is_rejected=0 and next_level_role_id=0) as approval_pending,
              count(1) filter(where aadhar_edit_role_id=2 and is_rejected=0 and next_level_role_id=0) as approved,
              count(1) filter(where is_rejected=1) as rejected,
              created_by_local_body_code
              from " . $schema . ". beneficiaries where  pre_no_aadhar=1 and  created_by_dist_code= " . $district_code . " AND scheme_id = ".$scheme_id."
              group by created_by_local_body_code
         )  
        as bp_main ON main.location_id=bp_main.created_by_local_body_code
         order by main.location_name";

    // echo $query;die;
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getSubDivWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL)
  {
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }
    $whereMain = "where  district_code=" . $district_code;
    $query = "select main.location_id,main.location_name,
        COALESCE(bp_main.total,0) as total,
        COALESCE(bp_main.action_pending,0) as action_pending,
        COALESCE(bp_main.approval_pending,0) as approval_pending,
        COALESCE(bp_main.approved,0) as approved,
        COALESCE(bp_main.rejected,0) as rejected
        from
        (
          select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
          from public.m_sub_district  " . $whereMain . " 
        ) as main LEFT JOIN
        (
              select count(1)  as total,
              count(1) filter(where aadhar_edit_role_id IS NULL and no_aadhar=1 and is_rejected=0 and next_level_role_id=0) as action_pending,
              count(1) filter(where aadhar_edit_role_id=1 and  no_aadhar=1 and is_rejected=0 and next_level_role_id=0) as approval_pending,
              count(1) filter(where aadhar_edit_role_id=2 and is_rejected=0 and next_level_role_id=0) as approved,
              count(1) filter(where is_rejected=1) as rejected,
              created_by_local_body_code
              from " . $schema . ". beneficiaries where   pre_no_aadhar=1 and  created_by_dist_code= " . $district_code . " AND scheme_id =".$scheme_id."
              group by created_by_local_body_code
         )  
        as bp_main ON main.location_id=bp_main.created_by_local_body_code
         order by main.location_name";

    // echo $query;die;
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getDistrictWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL)
  {
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }
    $query = "select main.location_id,main.location_name,
        COALESCE(bp_main.total,0) as total,
        COALESCE(bp_main.action_pending,0) as action_pending,
        COALESCE(bp_main.approval_pending,0) as approval_pending,
        COALESCE(bp_main.approved,0) as approved,
        COALESCE(bp_main.rejected,0) as rejected
        from
        (
        select district_code as location_id,district_name as location_name
        from public.m_district  
        ) as main LEFT JOIN
        (
              select count(1)  as total,
              count(1) filter(where aadhar_edit_role_id IS NULL and no_aadhar=1 and is_rejected=0 and next_level_role_id=0) as action_pending,
              count(1) filter(where aadhar_edit_role_id=1 and  no_aadhar=1 and is_rejected=0 and next_level_role_id=0) as approval_pending,
              count(1) filter(where aadhar_edit_role_id=2 and is_rejected=0 and next_level_role_id=0) as approved,
              count(1) filter(where is_rejected=1) as rejected,
              created_by_dist_code
              from " . $schema . ". beneficiaries where pre_no_aadhar=1 AND scheme_id =".$scheme_id."
              group by created_by_dist_code
         )  
        as bp_main ON main.location_id=bp_main.created_by_dist_code
         order by main.location_name";

    // echo $query;die;
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }

  public function generate_excel(Request $request)
  {
    try {
      $user_id = Auth::user()->id;
      $scheme_id = $request->scheme_id;
      if (empty($scheme_id)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $district_code = $duty_obj->district_code;
      if (empty($district_code)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $scheme_row = Scheme::where('id', $scheme_id)->first();
      if (!empty($scheme_row->short_code)) {
        $schema = $scheme_row->short_code;
      } else {
        $schema = "pension";
      }
      $designation_id_old = Auth::user()->designation_id_old;
      $condition = " created_by_dist_code=" . $district_code . " ";
      if ($designation_id_old == 'Verifier') {
        if ($duty_obj->mapping_level == "Subdiv") {
          $created_by_local_body_code = $duty_obj->urban_body_code;
        }
        if ($duty_obj->mapping_level == "Block") {
          $created_by_local_body_code = $duty_obj->taluka_code;
        }
        $condition = $condition . " and created_by_local_body_code=" . $created_by_local_body_code . " ";
      }
      $query = "select * from
      (
      select id,is_rejected,ben_fname,ben_mname,ben_lname,dob,father_fname,father_mname,father_lname,mother_fname,mother_mname,mother_lname,
      mobile_no,
      gp_ward_name,block_ulb_name,house_premise_no,village_town_city,aadhar_edit_role_id,no_aadhar from " . $schema . ".beneficiaries  
      where " . $condition . " and pre_no_aadhar=1   and scheme_id = ".$scheme_id."  
        ) as P order by gp_ward_name,block_ulb_name,ben_fname";
      $result = DB::connection('pgsql_mis')->select($query);
      //dd($result);
      $filename = 'NoAadhaar Beneficiary List_' . $district_code;
      if ($designation_id_old == 'Verifier') {
        $filename =  $filename . '_' . $created_by_local_body_code;
      }
      $filename = $filename . "-" . date('d/m/Y') . '-' . time() . ".xls";
      header("Content-Type: application/xls");
      header("Content-Disposition: attachment; filename=" . $filename);
      header("Pragma: no-cache");
      header("Expires: 0");
      echo '<table border="1">';
      echo '<tr><td colspan="10">Jai Bangla No Aadhaar Beneficiary List</td></tr>';
      echo '<tr><th>Beneficiary Id</th><th>Beneficiary Name</th><th>Mobile No.</th><th>DOB.</th><th>Father\'s Name</th><th>Block/Municipality</th><th>GP/WARD</th><th>Village/Town/City</th><th>Status</th></tr>';
      if (count($result) > 0) {
        foreach ($result as $row) {
          if (!empty($row->ben_fname)) {
            $ben_fname = trim($row->ben_fname);
          } else {
            $ben_fname = '';
          }
          if (!empty($row->ben_mname)) {
            $ben_mname = trim($row->ben_mname);
          } else {
            $ben_mname = '';
          }
          if (!empty($row->ben_lname)) {
            $ben_lname = trim($row->ben_lname);
          } else {
            $ben_lname = '';
          }
          $ben_fullname = $ben_fname . " " . $ben_mname . " " . $ben_lname;
          if (!empty($row->mobile_no)) {
            $ben_mobile_no = $row->mobile_no;
          } else {
            $ben_mobile_no = '';
          }
          if (!empty($row->dob)) {
            $ben_dob = $row->dob;
          } else {
            $ben_dob = '';
            $ben_age = '';
          }

          if (!empty($row->father_fname)) {
            $father_fname = trim($row->father_fname);
          } else {
            $father_fname = '';
          }
          if (!empty($row->father_mname)) {
            $father_mname = trim($row->father_mname);
          } else {
            $father_mname = '';
          }
          if (!empty($row->father_lname)) {
            $father_lname = trim($row->father_lname);
          } else {
            $father_lname = '';
          }
          $father_fullname = $father_fname . " " . $father_mname . " " . $father_lname;
          if (!empty($row->block_ulb_name)) {
            $block_ulb_name = trim($row->block_ulb_name);
          } else {
            $block_ulb_name = '';
          }
          if (!empty($row->gp_ward_name)) {
            $gp_ward_name = trim($row->gp_ward_name);
          } else {
            $gp_ward_name = '';
          }
          if (!empty($row->village_town_city)) {
            $village_town_city = trim($row->village_town_city);
          } else {
            $village_town_city = '';
          }
          if (!empty($row->house_premise_no)) {
            $house_premise_no = trim($row->house_premise_no);
          } else {
            $house_premise_no = '';
          }
          $status = '';
          if ($row->is_rejected == 1) {
            $status = 'Rejected';
          } else {
            if (is_null($row->aadhar_edit_role_id) && $row->no_aadhar == 1) {
              $status = 'Yet to be Action';
            } else if ($row->aadhar_edit_role_id == 1 && $row->no_aadhar == 1) {
              $status = 'Verified but Approval Pending';
            } else if ($row->aadhar_edit_role_id == 2 && $row->no_aadhar == 0) {
              $status = 'Verified and Approved';
            }
          }
          echo "<tr><td>" . $row->id . "</td><td>" . $ben_fullname . "</td><td>" . $ben_mobile_no . "</td><td>" . $ben_dob . "</td><td>" . $father_fullname . "</td><td>" . trim($block_ulb_name) . "</td><td>" . trim($gp_ward_name) . "</td><td>" . trim($village_town_city) . "</td><td>" . $status . "</td></tr>";
        }
      } else {
        echo '<tr><td colspan="9">No Records found</td></tr>';
      }
      echo '</table>';
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
}
