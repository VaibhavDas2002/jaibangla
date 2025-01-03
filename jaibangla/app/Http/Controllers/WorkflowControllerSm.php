<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\District;
use App\Scheme;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Illuminate\Support\Facades\Config;
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
use App\DsPhase;
use App\Helpers\AuthChecker;


class WorkflowControllerSm extends Controller
{

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
      // $designation_id = Auth::user()->designation_id;
      $user_id = AuthChecker::getUserId();
      if (AuthChecker::VerifierChecker() || AuthChecker::ApproverChecker()) {
        $schemes = DB::select(DB::raw("select id,scheme_name,display_name,is_active from m_scheme where id IN (10) and   id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
        //dd($schemes);
        return view(
          'Sarasori_Mukhyamantri/SchemeSelection',
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
    $is_operator = AuthChecker::OperatorChecker();
    $is_verifier = AuthChecker::VerifierChecker();
    $is_approver = AuthChecker::ApproverChecker();
    $is_hod = AuthChecker::HODChecker();
    $user_id = AuthChecker::getUserId();

    $scheme_id = $request->scheme_id;
    if (!ctype_digit($scheme_id)) {
      return redirect("/")->with('error', 'Scheme Not Valid');
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (empty($scheme_obj)) {
      return redirect("/")->with('danger', 'Scheme Not Found');
    }
    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if (!AuthChecker::VerifierChecker()) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
    $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
    $next_level_role_id_approver = $role_id_approver->parent_id;
    $next_level_role_id_verifier = $role_id_verifier->parent_id;
    // dd($next_level_role_id_verifier);
    $type_des = 'Sarasori Mukhyamantri';

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
      //dd($process_type);
      $query = DB::table($schema . '.beneficiaries')
        ->whereNull('is_lb_imported')->where('created_by_dist_code', $district_code)/*->whereraw(" (dup_bank=0 or dup_bank IS NULL) and (dup_aadhar=0 or dup_aadhar IS NULL) and (dup_mobile=0 or dup_mobile IS NULL) and (no_aadhar=0 or no_aadhar IS NULL) and (no_mobile=0 or no_mobile IS NULL)")*/;
      if (AuthChecker::VerifierChecker()) {
        $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
        if (!empty($application_type)) {
          if ($application_type == 1)
            $query = $query->whereNull('sm_flag')->whereNull('sm_mobile_no')->where('next_level_role_id', $next_level_role_id_verifier);
          if ($application_type == 2)
            $query = $query->whereNotNull('sm_flag')->whereNotNull('sm_mobile_no')->where('next_level_role_id', $next_level_role_id_verifier);
          if ($application_type == 3)
            $query = $query->whereNotNull('sm_flag')->whereNotNull('sm_mobile_no')->where('next_level_role_id', $next_level_role_id_approver);
          if ($application_type == 4)
            $query = $query->whereNotNull('sm_flag')->whereNotNull('sm_mobile_no')->where('is_rejected', 1);
        }
      }
      // dd($query);
      if ($duty_obj->mapping_level == "Subdiv") {
        if (!empty($request->block_ulb_code)) {
          $query = $query->where('block_ulb_code', $request->block_ulb_code);
        }
      }
      if (!empty($request->gp_ward_code)) {
        $query = $query->where('gp_ward_code', $request->gp_ward_code);
      }
      if (AuthChecker::ApproverChecker()) {
        if ($application_type != '') {
          if ($application_type == 1)
            $query = $query->whereNotNull('sm_flag')->whereNotNull('sm_mobile_no')->where('next_level_role_id', $next_level_role_id_verifier);
          if ($application_type == 3)
            $query = $query->whereNotNull('sm_flag')->whereNotNull('sm_mobile_no')->where('next_level_role_id', $next_level_role_id_approver);
          if ($application_type == 4)
            $query = $query->whereNotNull('sm_flag')->whereNotNull('sm_mobile_no')->where('is_rejected', 1);
        }
      }
      //  $rawsql = $query->toSql();
      //  dd($rawsql);
      $serachvalue = $request->search['value'];
      if (empty($serachvalue)) {
        $totalRecords = $query->count();
        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
          'id', 'created_by_dist_code', 'dob', 'assembly_name',
          'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
          'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag', 'sm_mobile_no', 'is_rejected', 'mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank','dup_mobile_edit_role_id'
        ]);
        $filterRecords = count($data);
      } else {
        if (is_numeric($serachvalue)) {
          // $ben_id = substr($serachvalue, -7);
          $ben_id = $serachvalue;
          $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
            $query1->where('id', $ben_id);
          });
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag', 'sm_mobile_no', 'is_rejected', 'mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank','dup_mobile_edit_role_id'
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
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag', 'sm_mobile_no', 'is_rejected', 'mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank','dup_mobile_edit_role_id'
            ]
          );
        }
        $filterRecords = count($data);
      }
      return datatables()->of($data)->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('view', function ($data) use ($scheme_id,  $next_level_role_id_approver, $next_level_role_id_verifier) {
          $action = '<a href="ViewSm?id=' . $data->id  . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> View</a>';


          if (AuthChecker::VerifierChecker()) {
            if (is_null($data->sm_flag) && is_null($data->sm_mobile_no) && $data->next_level_role_id == $next_level_role_id_verifier) {
              // echo 1;die;
              if ($data->no_aadhar == 1 || $data->no_mobile == 1 || $data->dup_aadhar == 1 || $data->dup_mobile == 1 || $data->dup_bank == 1) {
                $action = $action . '';
              } else {
                $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-' . $data->id . '" value="' . $data->id . '">Mark as Sarasori Mukhyamantri</button>';
              }
              $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-warning btn-revert" id="btn-revert-' . $data->id . '" value="' . $data->id . '">Revert</button>';
              $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-danger btn-sms" id="btn-sms-' . $data->id . '" value="' . $data->id . '">Reject</button>';
            } else if (!is_null($data->sm_flag) && !is_null($data->sm_mobile_no) && $data->next_level_role_id == $next_level_role_id_verifier) {
              $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Approval Pending';
            } else if (!is_null($data->sm_flag) && !is_null($data->sm_mobile_no) && $data->next_level_role_id == $next_level_role_id_approver) {
              $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Approved';
            }
           
          }

          if (AuthChecker::ApproverChecker()) {

            if (!is_null($data->sm_flag) && !is_null($data->sm_mobile_no) && $data->next_level_role_id == $next_level_role_id_verifier) {
              $action = $action . '<button type="button" class="btn btn-xs btn-primary">Approved</button>';
            } else if (!is_null($data->sm_flag) && !is_null($data->sm_mobile_no) && $data->next_level_role_id == $next_level_role_id_approver) {
              $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Approved';
            }
          }


          return $action;
        })->addColumn('status', function ($data)  {
          $status = '';
          if ($data->dup_mobile ==1) {
            $status=$status.'<span class="text-primary" style="font-weight: bold;">Duplicate Mobile Number.</br></span>';
          }
          if ($data->dup_bank ==1) {
            $status=$status.'<span class="text-primary" style="font-weight: bold;">Duplicate Bank A/C Number.</br></span>';
          }
          if ($data->dup_aadhar ==1) {
            $status=$status.'<span class="text-primary" style="font-weight: bold;">Duplicate Aadhar Number.</br></span>';
          }
          if ($data->no_aadhar ==1) {
            $status=$status.'<span class="text-primary" style="font-weight: bold;">No Aadhar Number.</br></span>';
          }
          if ($data->no_mobile ==1) {
            $status=$status.'<span class="text-primary" style="font-weight: bold;">No Mobile Number.</br></span>';
          }
          return $status;
        })->addColumn('check', function ($data) use ($is_approver) {
          if ($is_approver) {
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
        ->rawColumns(['view', 'id', 'name', 'mask_aadhaar_no', 'bank_ifsc', 'bank_code', 'bank_ifsc', 'status', 'check'])
        ->make(true);
    }

    return view(
      'Sarasori_Mukhyamantri.linelisting',
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
        'is_verifier' => $is_verifier,
        'is_approver' => $is_approver,
      ]
    );
  }
  public function ViewSm(Request $request)
  {
    //dd('ok');
    try {
      $this->middleware('auth');
      $designation_id = Auth::user()->designation_id;
      $user_id = AuthChecker::getUserId();
      $id = $request->id;
      // dd($id);
      if (empty($request->id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }
      $scheme_id = $request->scheme_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }

      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();

      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }

      $type_des = 'Sarasori Mukhyamantri ';
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      $query = DB::table($schema . '.beneficiary')
        ->where('created_by_dist_code', $district_code)->where('id', $id)->where('created_by_dist_code', $district_code)->whereraw(" (next_level_role_id=" . $next_level_role_id_approver . " or next_level_role_id=" . $next_level_role_id_verifier . ") ");
      $row = $query->first();
      // dd( $row);
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      //dd($row->aadhar_no);
      if (AuthChecker::VerifierChecker()) {
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
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();
      return view(
        'Sarasori_Mukhyamantri.ViewBeneficiary',
        [
          'designation_id' => $designation_id,
          'row' => $row,
          'id' => $id,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'docs' => $docs,
          'reject_revert_cause_list' => $reject_revert_cause_list,
          'scheme_id' => $scheme_id,
        ]
      );
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function markpost(Request $request)
  {
    try {
      $this->middleware('auth');
      $designation_id = Auth::user()->designation_id;
      $user_id = AuthChecker::getUserId();
      if (empty($request->beneficiary_id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }
      $scheme_id = $request->scheme_id;
      $id = $request->beneficiary_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('is_active', 1)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed...');
      }
      if (empty($request->scheme_id)) {
        return redirect("/")->with('danger', 'Scheme ID Not Found');
      }

      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      $condition = array();
      $condition['id'] = $id;
      if (AuthChecker::VerifierChecker()) {
        if ($duty_obj->mapping_level == "Subdiv") {
          $created_by_local_body_code = $duty_obj->urban_body_code;
        }
        if ($duty_obj->mapping_level == "Block") {
          $created_by_local_body_code = $duty_obj->taluka_code;
        }
        $condition['created_by_local_body_code'] = $created_by_local_body_code;
      }
      $query = DB::table($schema . '.beneficiary')
        ->where($condition)->where('id', $id)->whereraw(" (dup_bank=0 or dup_bank IS NULL) and (dup_aadhar=0 or dup_aadhar IS NULL) and (dup_mobile=0 or dup_mobile IS NULL) and (no_aadhar=0 or no_aadhar IS NULL) and (no_mobile=0 or no_mobile IS NULL)")->where("next_level_role_id", $next_level_role_id_verifier);


      $row = $query->first();
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $is_error = 0;
      if (empty(trim($request->sm_mobile_no))) {
        $errors = array();
        $errorMsg = "Mobile Number is Required";
        array_push($errors, $errorMsg);
      } else {
        if (strlen(trim($request->sm_mobile_no)) != 10) {
          $errors = array();
          $errorMsg = "Mobile Number Invalid";
          array_push($errors, $errorMsg);
        }
        if (!preg_match('/^[0-9]{10}+$/', $request->sm_mobile_no)) {
          $errors = array();
          $errorMsg = "Mobile Number Invalid";
          array_push($errors, $errorMsg);
        }
        if ($request->sm_mobile_no < 1000000000) {
          $errors = array();
          $errors = array();
          $errorMsg = "Mobile Number Invalid";
          array_push($errors, $errorMsg);
        }
      }


      if ($is_error == 0) {


        $c_time = date('Y-m-d H:i:s', time());
        DB::beginTransaction();



        $inputMain = array();
        $inputMain['sm_flag'] = 1;
        $inputMain['sm_mobile_no'] = trim($request->sm_mobile_no);

        $upadated_main = DB::table($schema . '.beneficiary')
          ->where([
            'id' => $id, 'created_by_local_body_code' => $created_by_local_body_code,
            'created_by_dist_code' => $district_code
          ])->whereNull('is_lb_imported')->update($inputMain);

        $modelNameAcceptReject = new AcceptRejectInfo;
        $op_type = 'SMMARK';
        $modelNameAcceptReject->scheme_id =  $scheme_id;

        $modelNameAcceptReject->created_at =  $c_time;
        $modelNameAcceptReject->op_type =  class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod() . $op_type;
        $modelNameAcceptReject->application_id = $request->beneficiary_id;
        $modelNameAcceptReject->user_id = $user_id;
        $modelNameAcceptReject->created_by_dist_code = $district_code;
        $modelNameAcceptReject->created_by_local_body_code = $created_by_local_body_code;
        $modelNameAcceptReject->ip_address = request()->ip();
        $is_accept_reject = $modelNameAcceptReject->save();
        //dump($upadated_main);dump($is_accept_reject);dump($enc_status);dd($is_inserted_arch);
        if ($upadated_main && $is_accept_reject) {
          DB::commit();
          $errors = array();
          $return_text = 'Beneficiary with  Id:' . $id . ' has been marked as Sarasori Mukhyamantri and Sent to Approver for Approval';
          return redirect("mark-sm?scheme_id=" . $scheme_id)->with('success', $return_text);
        } else {
          DB::rollback();
          $errors = array();
          $errorMsg = 'Error.. Please try different.';
          array_push($errors, $errorMsg);
        }
      }


      if (count($errors) > 0) {
        return redirect("/mark-sm?scheme_id=" . $scheme_id)->with('errors', $errorMsg);
      }
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }

  public function SmReject(Request $request)
  {
    try {
      $this->middleware('auth');
      $designation_id = Auth::user()->designation_id;
      $user_id = AuthChecker::getUserId();
      if (empty($request->beneficiary_id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }
      $scheme_id = $request->scheme_id;
      $id = $request->beneficiary_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed...');
      }
      if (empty($request->scheme_id)) {
        return redirect("/")->with('danger', 'Scheme ID Not Found');
      }

      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      if (AuthChecker::VerifierChecker()) {
        if ($duty_obj->mapping_level == "Subdiv") {
          $created_by_local_body_code = $duty_obj->urban_body_code;
        }
        if ($duty_obj->mapping_level == "Block") {
          $created_by_local_body_code = $duty_obj->taluka_code;
        }
      }
      $c_time = date('Y-m-d H:i:s', time());
      DB::beginTransaction();
      $inputMain = array();
      $inputMain['next_level_role_id'] = -1;
      $inputMain['is_rejected'] = 1;
      $inputMain['is_verified'] = 2;
      $inputMain['is_approved'] = 2;
      $inputMain['rejected_by'] = $user_id;
      $inputMain['rejected_date'] = $c_time;
      $inputMain['is_clean'] = 10;
      $upadated_main = DB::table($schema . '.beneficiary')
        ->where([
          'id' => $id, 'created_by_local_body_code' => $created_by_local_body_code,
          'created_by_dist_code' => $district_code
        ])->whereNull('is_lb_imported')->update($inputMain);
      $modelNameAcceptReject = new AcceptRejectInfo;
      $op_type = 'SMREJECT';
      $modelNameAcceptReject->scheme_id = $scheme_id;
      $modelNameAcceptReject->created_at =  $c_time;
      $modelNameAcceptReject->op_type = class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod().  $op_type;
      $modelNameAcceptReject->application_id = $id;
      $modelNameAcceptReject->user_id = $user_id;
      $modelNameAcceptReject->created_by_dist_code = $district_code;
      $modelNameAcceptReject->created_by_local_body_code = $created_by_local_body_code;
      $modelNameAcceptReject->ip_address = request()->ip();
      $is_accept_reject = $modelNameAcceptReject->save();

      if ($upadated_main && $is_accept_reject) {
        DB::commit();
        $errors = array();
        $return_text = 'Beneficiary with  Id:' . $id . ' has been Rejected';
        return redirect("mark-sm?scheme_id=" . $scheme_id)->with('success', $return_text);
      } else {
        DB::rollback();
        $errors = array();
        $errorMsg = 'Error.. Please try again.';
        array_push($errors, $errorMsg);
      }
    } catch (\Exception $e) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }

  public function SmRevert(Request $request)
  {
    try {
      $this->middleware('auth');
      $designation_id = Auth::user()->designation_id;
      $user_id = AuthChecker::getUserId();
      if (empty($request->beneficiary_id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }
      $scheme_id = $request->scheme_id;
      $id = $request->beneficiary_id;
      // echo $id;die;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed...');
      }
      if (empty($request->scheme_id)) {
        return redirect("/")->with('danger', 'Scheme ID Not Found');
      }

      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      if (AuthChecker::VerifierChecker()) {
        if ($duty_obj->mapping_level == "Subdiv") {
          $created_by_local_body_code = $duty_obj->urban_body_code;
        }
        if ($duty_obj->mapping_level == "Block") {
          $created_by_local_body_code = $duty_obj->taluka_code;
        }
      }
      $c_time = date('Y-m-d H:i:s', time());
      DB::beginTransaction();
      $inputMain = array();
      $inputMain['next_level_role_id'] = null;
      $inputMain['is_reverted'] = 1;
      $inputMain['is_verified'] = 0;
      $inputMain['is_approved'] = 0;
      $inputMain['sm_flag'] = NULL;
      $inputMain['sm_mobile_no'] = NULL;
      $upadated_main = DB::table($schema . '.beneficiary')
        ->where([
          'id' => $id, 'created_by_local_body_code' => $created_by_local_body_code,
          'created_by_dist_code' => $district_code
        ])->whereNull('is_lb_imported')->update($inputMain);
      $modelNameAcceptReject = new AcceptRejectInfo;
      $op_type = 'SMREVERT';
      $modelNameAcceptReject->scheme_id = $scheme_id;
      $modelNameAcceptReject->created_at =  $c_time;
      $modelNameAcceptReject->op_type =  $op_type;
      $modelNameAcceptReject->application_id = $id;
      $modelNameAcceptReject->user_id = $user_id;
      $modelNameAcceptReject->created_by_dist_code = $district_code;
      $modelNameAcceptReject->created_by_local_body_code = $created_by_local_body_code;
      $modelNameAcceptReject->op_type = class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod() .$op_type ;

      $modelNameAcceptReject->ip_address = request()->ip();
      $is_accept_reject = $modelNameAcceptReject->save();

      if ($upadated_main && $is_accept_reject) {
        DB::commit();
        $errors = array();
        $return_text = 'Beneficiary with  Id:' . $id . ' has been Reverted';
        return redirect("mark-sm?scheme_id=" . $scheme_id)->with('success', $return_text);
      } else {
        DB::rollback();
        $errors = array();
        $errorMsg = 'Error.. Please try again.';
        array_push($errors, $errorMsg);
      }
    } catch (\Exception $e) {
      // dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function oapsmdsmark(Request $request)
  {
    return redirect("/")->with('danger', 'Not Allowed');
    $this->middleware('auth');
    $designation_id = Auth::user()->designation_id;
    //dd($designation_id);
    $user_id = AuthChecker::getUserId();

    $scheme_id = $request->scheme_id;
    $type = $request->type;
    $ds_mark_phase = $request->ds_mark_phase;
    if ($type == '') {
      return redirect("/")->with('error', 'Type Not Valid');
    }
    if (!ctype_digit($type)) {
      return redirect("/")->with('error', 'Type Not Valid');
    }
    if ($ds_mark_phase == '') {
      return redirect("/")->with('error', 'Type Not Valid');
    }
    if (!ctype_digit($ds_mark_phase)) {
      return redirect("/")->with('error', 'Type Not Valid');
    }
    if (!in_array($type, array('1', '2', '3', '4'))) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if (!ctype_digit($scheme_id)) {
      return redirect("/")->with('error', 'Scheme Not Valid');
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (empty($scheme_obj)) {
      return redirect("/")->with('danger', 'Scheme Not Found');
    }
    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if ($type == 1) {
      if (!AuthChecker::VerifierChecker() || !AuthChecker::ApproverChecker()) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
    }
    //dd($type);
    if ($type == 2 || $type == 3 || $type == 4) {
      if (!AuthChecker::OperatorChecker()) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      
    }
    $ds_phase_arr = DsPhase::where('phase_code', $ds_mark_phase)->first();
    if (empty($ds_phase_arr)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $camp_roman = $ds_phase_arr->phase_des;
    $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
    $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
    $next_level_role_id_approver = $role_id_approver->parent_id;
    $next_level_role_id_verifier = $role_id_verifier->parent_id;
    // dd($next_level_role_id_verifier);
    $type_des = 'Mark as Duare Sarkar ' . $camp_roman . ' Camps';
    //dd($type);

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
    $allow_marking_count = DB::table('pension.ds_mark_can_sdo_bdo')
    ->where('created_by_local_body_code',$created_by_local_body_code)->where('ds_phase',$ds_mark_phase)->where('is_allowded',1)
    ->count();
      if($allow_marking_count==0){
        return redirect("/")->with('danger', 'Marking temporarily suspended.');  
      }
    if (request()->ajax()) {
      $limit = $request->input('length');
      $offset = $request->input('start');
      $application_type = $request->application_type;
      $process_type = $request->process_type;

      
      if ($type == 1) {
        $query = DB::table($schema . '.beneficiary')
          ->whereNull('is_lb_imported')->whereNull('ds_phase')->whereRaw(' (next_level_role_id IS NULL or next_level_role_id=' . $next_level_role_id_verifier . ') ')->where('created_by_dist_code', $district_code)->whereNull('sm_flag')->where('is_samadhan', false)/*->whereraw(" (dup_bank=0 or dup_bank IS NULL) and (dup_aadhar=0 or dup_aadhar IS NULL) and (dup_mobile=0 or dup_mobile IS NULL) and (no_aadhar=0 or no_aadhar IS NULL) and (no_mobile=0 or no_mobile IS NULL)")*/;
      } else {
        $query = DB::table($schema . '.beneficiary')
          ->where('is_rejected', 0);
      }
      if (AuthChecker::OperatorChecker()) {
        // $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
        //$query = $query->whereNull('sm_ds_mark');
        if ($type == 2) {
          $dupBankCheck = $request->session()->get('dupBankCheck');
          // dd( $dupBankCheck);
          $query = $query->where('bank_code', $dupBankCheck);
        } else if ($type == 3) {
          $dupAadhaarCheck = $request->session()->get('dupAadhaarCheck');
          //dd($dupAadhaarCheck);
          $query = $query->where('aadhar_no', $dupAadhaarCheck);
        } else if ($type == 4) {
          $dupMobileCheck = $request->session()->get('dupMobileCheck');
          $query = $query->where('mobile_no', $dupMobileCheck);
        }
      }
      if (AuthChecker::VerifierChecker()) {
        $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
        if (!empty($application_type)) {
          if ($application_type == 1)
            $query = $query->whereNull('sm_ds_mark');
          if ($application_type == 2)
            $query = $query->where('sm_ds_mark', 1)->where('sm_ds_mark_vii', 1);
        }
      }
      // dd($query);
      if ($duty_obj->mapping_level == "Subdiv") {
        if (!empty($request->block_ulb_code)) {
          $query = $query->where('block_ulb_code', $request->block_ulb_code);
        }
      }
      if (!empty($request->gp_ward_code)) {
        $query = $query->where('gp_ward_code', $request->gp_ward_code);
      }
      if (AuthChecker::ApproverChecker()) {
        if ($application_type != '') {

          if ($application_type == 2)
            $query = $query->where('sm_ds_mark', 1)->where('sm_ds_mark_vii', 1);
        }
      }
      //  $rawsql = $query->toSql();
      //  dd($rawsql);
      $serachvalue = $request->search['value'];
      if (empty($serachvalue)) {
        $totalRecords = $query->count();
        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
          'id', 'created_by_dist_code', 'dob', 'assembly_name',
          'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
          'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag', 'sm_mobile_no',
          'is_rejected', 'mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank', 'sm_ds_mark', 'sm_ds_mark_role_id', 'aadhar_no', 'sm_ds_mark_vii', 'sm_ds_mark_viii', 'sm_ds_mark_ix'
        ]);
        $filterRecords = count($data);
      } else {
        if (is_numeric($serachvalue)) {
          // $ben_id = substr($serachvalue, -7);
          $ben_id = $serachvalue;
          // dump(strlen($ben_id));
          // dump($ben_id);
          // var_dump($ben_id);
          // dump($created_by_local_body_code);

          // $aadhar_no_str = strval($ben_id); 

          // $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
          //   $query1->where('id', $ben_id)
          //   ->orWhere('aadhar_no', DB::raw("CONVERT(".$ben_id.", CHAR)"));
          //   // $query1->whereRaw("( id = ".$ben_id." OR trim(aadhar_no) = '" . $ben_id . "')");
          // });

          if (strlen($ben_id) == 12) {
            $ben_id = (string) $ben_id;
            $query = $query->where('aadhar_no', $ben_id);
          } else {
            $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
              $query1->where('id', $ben_id);
            });
          }

          //dd($query->toSql());
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag', 'sm_mobile_no',
              'is_rejected', 'mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile',
              'dup_bank', 'sm_ds_mark', 'sm_ds_mark_role_id', 'aadhar_no','sm_ds_mark_vii', 'sm_ds_mark_viii','sm_ds_mark_ix'
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
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag', 'sm_mobile_no', 'is_rejected',
              'mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank', 'sm_ds_mark', 'sm_ds_mark_role_id', 'aadhar_no','sm_ds_mark_vii', 'sm_ds_mark_viii','sm_ds_mark_ix'
            ]
          );
        }
        $filterRecords = count($data);
      }
      // dd($data);
      return datatables()->of($data)
        ->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('view', function ($data) use ($ds_mark_phase, $camp_roman, $type, $scheme_id, $designation_id, $next_level_role_id_approver, $next_level_role_id_verifier) {
          $action = '<a href="ViewOapsmdsmark?ds_mark_phase=' . $ds_mark_phase . '&type=' . $type . '&id=' . $data->id  . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> View</a>';

          if ($designation_id == 'Operator') {
            if (is_null($data->sm_ds_mark_ix)) {
              // echo 1;die;

              $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-' . $data->id . '" value="' . $data->id . '">Mark as  JS-SS Camps</button>';
            } else if ($data->sm_ds_mark_ix = 1) {
              $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Already Marked as JS-SS';
            }
          }
          if ($designation_id == 'Verifier') {
            if (is_null($data->sm_ds_mark_vii)) {
              $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-' . $data->id . '" value="' . $data->id . '">Mark as ' . $camp_roman . ' Camps</button>';
            } else if ($data->sm_ds_mark_vii = 1) {
              $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Already Marked as Duare Sarkar ' . $camp_roman . ' Camps';
            }
          }




          return $action;
        })->addColumn('check', function ($data) use ($designation_id) {
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
        })->addColumn('aadhaar_no', function ($data) {
          if (!empty($data->aadhaar_no)) {
            $ben_aadhaar_no = trim($data->aadhaar_no);
          } else {
            $ben_aadhaar_no = '';
          }
          return $ben_aadhaar_no;
        })
        ->rawColumns(['view', 'id', 'name', 'aadhaar_no', 'bank_ifsc', 'bank_code', 'bank_ifsc', 'bank_ifsc', 'check'])
        ->make(true);
    }

    return view(
      'Sarasori_Mukhyamantri.oapsmdsmarklist',
      [
        'designation_id' => $designation_id,
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
        'scheme_id' => $scheme_id,
        'ds_mark_phase' => $ds_mark_phase,
        'camp_roman' => $camp_roman,
        'type' => $type,

      ]
    );
  }
  public function ViewOapsmdsmark(Request $request)
  {
    return redirect("/")->with('danger', 'Not Allowed');
    try {
      $this->middleware('auth');
      $designation_id = Auth::user()->designation_id;
      $user_id = Authchecker::getUserId();
      $id = $request->id;
      // dd($id);
      if (empty($request->id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }
      $scheme_id = $request->scheme_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }

      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();

      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $type = $request->type;
      if ($type == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($type)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!in_array($type, array('1', '2', '3', '4'))) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $ds_mark_phase = $request->ds_mark_phase;
      if ($ds_mark_phase == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($ds_mark_phase)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      $ds_phase_arr = DsPhase::where('phase_code', $ds_mark_phase)->first();
      if (empty($ds_phase_arr)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if ($type == 2 || $type == 3 || $type == 4) {
        $allow_ds_entry = intval($scheme_obj->allow_ds_entry);
        if($allow_ds_entry==0){
          return redirect("/")->with('danger', 'Marking temporarily suspended.');  
        }
      }
      $camp_roman = $ds_phase_arr->phase_des;
      $type_des = 'Sarasori Mukhyamantri ';
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      if ($type == 1) {
        $query = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)->whereNull('ds_phase')->where('id', $id)->where('created_by_dist_code', $district_code)->whereRaw(' (next_level_role_id IS NULL or next_level_role_id=' . $next_level_role_id_verifier . ') ')->where('is_samadhan', false);
      } else {
        $query = DB::table($schema . '.beneficiary')->where('id', $id)->where('is_rejected', 0);
      }
      $row = $query->first();
      // dd( $row);
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      //dd($row->aadhar_no);


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
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $row->created_by_dist_code)->orderBy('document_type')->get();
      return view(
        'Sarasori_Mukhyamantri.ViewOapsmdsmark',
        [
          'designation_id' => $designation_id,
          'row' => $row,
          'id' => $id,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'docs' => $docs,
          'reject_revert_cause_list' => $reject_revert_cause_list,
          'scheme_id' => $scheme_id,
          'ds_mark_phase' => $ds_mark_phase,
          'camp_roman' => $camp_roman,
          'type' => $type,

        ]
      );
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function oapsmdsmarkPost(Request $request)
  {
    try {
      return redirect("/")->with('danger', 'Not Allowed');
      $this->middleware('auth');
      $designation_id = Auth::user()->designation_id;
      $user_id = AuthChecker::getUserId();
      if (empty($request->beneficiary_id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }

      if (empty($request->scheme_id)) {
        return redirect("/")->with('danger', 'Scheme ID Not Found');
      }
      $scheme_id = $request->scheme_id;
      $id = $request->beneficiary_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed...');
      }
      $type = $request->type;
      if ($type == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($type)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!in_array($type, array('1', '2', '3', '4'))) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $ds_mark_phase = $request->ds_mark_phase;
      if ($ds_mark_phase == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($ds_mark_phase)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      $ds_phase_arr = DsPhase::where('phase_code', $ds_mark_phase)->first();
      if (empty($ds_phase_arr)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if (trim($request->ds_registration_no)=='') {
        return redirect("/")->with('error', 'Camp Registration No. Required');
      }
      if (strlen(trim($request->ds_registration_no))<24) {
        return redirect("/")->with('error', 'Camp Registration No. Not Valid');

      }
      $camp_roman = $ds_phase_arr->phase_des;
      
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      $condition = array();
      $condition['id'] = $id;

      if ($duty_obj->mapping_level == "Subdiv") {
        $created_by_local_body_code = $duty_obj->urban_body_code;
      }
      if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
      }

      if ($type == 1) {
        $condition['created_by_local_body_code'] = $created_by_local_body_code;
        $query = DB::table($schema . '.beneficiary')
          ->where($condition)->whereNull('ds_phase')->where('id', $id)->whereRaw(' (next_level_role_id IS NULL or next_level_role_id=' . $next_level_role_id_verifier . ') ')->where('is_samadhan', false);
      } else {
        $query = DB::table($schema . '.beneficiary')->where('id', $id)->where('is_rejected', 0);
      }
      if (AuthChecker::ApproverChecker()) {
        $query = $query->where('sm_ds_mark', 1);
      }
      if (AuthChecker::VerifierChecker()) {
        $query = $query->whereNull('sm_ds_mark_viii');
      }
      if (AuthChecker::OperatorChecker()) {
        $query = $query->whereNull('sm_ds_mark_ix');
      }
      $row = $query->first();
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $is_error = 0;

      $allow_marking_count = DB::table('pension.ds_mark_can_sdo_bdo')
    ->where('created_by_local_body_code',$created_by_local_body_code)->where('ds_phase',$ds_mark_phase)->where('is_allowded',1)
    ->count();
      if($allow_marking_count==0){
        return redirect("/")->with('danger', 'Marking temporarily suspended.');  
      }

      if ($is_error == 0) {


        $c_time = date('Y-m-d H:i:s', time());
        DB::beginTransaction();

        $in_pension_id = 'ARRAY[' . "'$request->beneficiary_id'" . ']';
        $comments = NULL;
        $ds_registration_no=trim($request->ds_registration_no);
        $is_inserted_status_arr = DB::select("select " . $schema . ".dsmark_for_sm(in_ds_mark_phase => $ds_mark_phase,in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'SMDSMARK', in_custom_comment => '" . $comments . "',in_ds_registration_no => '" . $ds_registration_no . "')");
        //dd($is_inserted_status_arr);
        $is_inserted_status = $is_inserted_status_arr[0]->dsmark_for_sm;

        if ($is_inserted_status == 1) {
          DB::commit();
          $errors = array();

          if (AuthChecker::VerifierChecker() || AuthChecker::OperatorChecker()) {
            $return_text = 'Beneficiary with  Id:' . $id . ' has been marked as Duare Sarkar ' . $camp_roman . ' Camps';
          }
          return redirect("/oapsmdsmark?type=" . $type . "&ds_mark_phase=" . $ds_mark_phase . "&scheme_id=" . $scheme_id)->with('success', $return_text);
        } else if ($is_inserted_status == 10) {
          DB::rollback();
          $errors = array();
          $errorMsg = 'Total DS mark  Applications  exceeds the Quota';
          array_push($errors, $errorMsg);
        } else {
          DB::rollback();
          $errors = array();
          $errorMsg = 'Error.. Please try different.';
          array_push($errors, $errorMsg);
        }
      }


      if (count($errors) > 0) {
        return redirect("/oapsmdsmark?type=" . $type . "&ds_mark_phase=" . $ds_mark_phase . "&scheme_id=" . $scheme_id)->with('errors', $errors);
      }
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function oapsmdsmarkListExcel(Request $request)
  {
    try {
      if (empty($request->scheme_id)) {
        return redirect('/')->with('error', 'Scheme Id Required');
      }
      if (!ctype_digit($request->scheme_id)) {
        return redirect('/')->with('error', 'Scheme Id Invalid');
      }
      if (empty($request->ds_mark_phase)) {
        return redirect('/')->with('error', 'Scheme Id Required');
      }
      $ds_mark_phase = $request->ds_mark_phase;
      if ($ds_mark_phase == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($ds_mark_phase)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }

      $scheme_id = $request->scheme_id;
      $is_active = 0;
      $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();      foreach ($roleArray as $roleObj) {
        if ($roleObj['scheme_id'] == $scheme_id) {
          $is_active = 1;
          $mapping_level = $roleObj['mapping_level'];
          $district_code = $roleObj['district_code'];
          if ($roleObj['is_urban'] == 1) {
            $urban_body_code = $roleObj['urban_body_code'];
          } else {
            $urban_body_code = $roleObj['taluka_code'];
          }
          break;
        }
      }
      if ($is_active == 0) {
        return redirect('/')->with('error', 'User not Authorized for this scheme');
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      $condition = array();
      $condition["sm_ds_mark"] = 1;
      $designation_id = Auth::user()->designation_id;
      if (AuthChecker::ApproverChecker()) {
        //dd(123);
        $condition["created_by_dist_code"] = $district_code;
      }
      if (AuthChecker::VerifierChecker() || AuthChecker::OperatorChecker()) {
        if ($ds_mark_phase == 7) {
          //dd(333);
          $condition["created_by_dist_code"] = $district_code;
          $condition["created_by_local_body_code"] = $urban_body_code;
        }
      }
      $scheme_name_row = Scheme::where('id', $scheme_id)->first();
      $scheme_name = $scheme_name_row->scheme_name;
      $schema = $scheme_name_row->short_code;
      $ds_phase_arr = DsPhase::where('phase_code', $ds_mark_phase)->first();
      if ($ds_mark_phase == 8) {
        $condition["sm_ds_mark_vii"] = 1;
        $query = DB::table('' . $schema . '.beneficiary')->whereRaw(' (next_level_role_id IS NULL or next_level_role_id=0 or next_level_role_id=' . $next_level_role_id_verifier . ') ')->where($condition);
      } else {
        $condition["sm_ds_mark_ix"] = 1;

        $query = DB::table('' . $schema . '.beneficiary')->where('is_rejected', 0)->where($condition);
      }
      $data = $query->select(
        'id',
        'scheme_id',
        'created_by_dist_code',
        'ben_fname',
        'ben_mname',
        'ben_lname',
        'father_fname',
        'father_mname',
        'father_lname',
        'mother_fname',
        'mother_mname',
        'mother_lname',
        'mobile_no',
        'dob',
        'ben_age',
        'caste',
        'next_level_role_id',
        'block_ulb_name',
        'gp_ward_name',
        'village_town_city',
        'bank_ifsc',
        'bank_code',
        'house_premise_no',
        'sm_ds_mark',
        'sm_ds_mark_role_id',
        'sm_ds_mark_vii',
        'sm_ds_mark_viii', 'sm_ds_mark_ix'
      )->orderBy('ben_fname')->orderBy('gp_ward_name')->get();
      $filename = "OAP_DS_MARK_" . $scheme_name . "-" .  "-" . date('d/m/Y') . '-' . time() . ".xls";
      header("Content-Type: application/xls");
      header("Content-Disposition: attachment; filename=" . $filename);
      header("Pragma: no-cache");
      header("Expires: 0");
      echo '<table border="1">';
      echo '<tr><td colspan="9">Mark as ' . $ds_phase_arr->phase_des . ' Camps</td></tr>';
      echo '<tr><th>Beneficiary Id</th><th>Full Name</th><th>Mobile No.</th><th>Father Name</th><th>Block/Municipality</th><th>GP/WARD</th><th>Bank IFSC</th><th>Bank Account No.</th><th>Status</th></tr>';
      if (count($data) > 0) {
        foreach ($data as $row) {
          $app_id = $row->id;
          $app_id = "'$app_id'";
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


          //$phase_des = $this->getPhaseDes($row->ds_phase);
          $ben_fullname = $ben_fname . " " . $ben_mname . " " . $ben_lname;
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
          $bank_code = (string) $row->bank_code;
          if (!empty($bank_code))
            $f_bank_code = "'$bank_code'";
          else
            $f_bank_code = $bank_code;
          $status = '';

          $status = "Marked as  " . $ds_phase_arr->phase_des;

          echo "<tr><td>" . $app_id . "</td><td>" . $ben_fullname . "</td><td>" . $row->mobile_no . "</td><td>" . $father_fullname . "</td><td>" . trim($row->block_ulb_name) . "</td><td>" . trim($row->gp_ward_name) . "</td><td>" . trim($row->bank_ifsc) . "</td><td>" . $f_bank_code . "</td><td>" . $status . "</td></tr>";
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
  public function oapsmdsmarkPostBulkApprove(Request $request)
  {
    return redirect("/")->with('danger', 'Not Allowed');
    try {
      return redirect("/")->with('error', 'Not Allowed');
      $this->middleware('auth');
      //dd('ok');
      $designation_id = Auth::user()->designation_id;
      if (!AuthChecker::ApproverChecker()) {
        return redirect("/")->with('error', 'Not Allowed');
      }
      $user_id = AuthChecker::getUserId();
      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $district_code = $duty_obj->district_code;
      $applicationid_arr = array();
      $inputs = request()->input('approvalcheck');
      $c_time = date('Y-m-d H:i:s', time());
      //dd($inputs);
      foreach ($inputs as $input) {
        array_push($applicationid_arr, $input);
      }
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      DB::beginTransaction();
      $implode_application_arr = implode("','", $applicationid_arr);
      $in_pension_id = 'ARRAY[' . "'$implode_application_arr'" . ']';
      $comments = NULL;
      $is_inserted_status_arr = DB::select("select " . $schema . ".dsmark_for_sm_approve(in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'SMDSMARKAPPROVE', in_custom_comment => '" . $comments . "')");
      $is_inserted_status = $is_inserted_status_arr[0]->dsmark_for_sm_approve;
      if ($is_inserted_status == 1) {
        DB::commit();
        $errors = array();

        $return_text = 'Beneficiaries Duare Sarkar VII Camps marking request has been sucessfully approved';



        return redirect("oapsmdsmark?scheme_id=" . $scheme_id)->with('success', $return_text);
      } else if ($is_inserted_status == 10) {
        DB::rollback();
        $errors = array();
        $errorMsg = 'Total DS mark  Applications  exceeds the quota';
        array_push($errors, $errorMsg);
        return redirect("oapsmdsmark?scheme_id=" . $scheme_id)->with('errors', $errors);
      } else {
        DB::rollback();
        $errors = array();
        $errorMsg = 'Error.. Please try different.';
        array_push($errors, $errorMsg);
        return redirect("oapsmdsmark?scheme_id=" . $scheme_id)->with('errors', $errors);
      }
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  function oapsmdsmarkoMisReport(Request $request)
  {
    $base_date  = '2020-01-01';
    $c_time = Carbon::now();
    $c_date = $c_time->format("Y-m-d");
    $is_active = 0;
    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();;
    $designation_id = Auth::user()->designation_id;
    $district_visible = $is_urban_visible = $block_visible = 1;
    $municipality_visible = 0;
    $gp_ward_visible = 0;
    $muncList = collect([]);
    $gpList = collect([]);
    $userId = Auth::user()->id;
    $scheme_code_in = array();
    $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 and id = 10 order by scheme_name"));
    foreach ($scheme_list as $scheme_item) {
      array_push($scheme_code_in, $scheme_item->id);
    }
    if (AuthChecker::ReportCheckerCommon()) {
      $district_visible = $is_urban_visible = $block_visible = 1;
    } else if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
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
    $is_urban_visible = 0;
    $block_visible = 0;
    $district_visible = 0;
    return view(
      'Sarasori_Mukhyamantri.oapsmdsmarkoMisReport',
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
        'designation_id' => $designation_id,
      ]
    );
  }
  public function OapBothSmDsmarkMisReport(Request $request)
  {
    $base_date  = '2020-01-01';
    $c_time = Carbon::now();
    $c_date = $c_time->format("Y-m-d");
    $is_active = 0;
    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();
    $designation_id = Auth::user()->designation_id;
    $district_visible = $is_urban_visible = $block_visible = 1;
    $municipality_visible = 0;
    $gp_ward_visible = 0;
    $muncList = collect([]);
    $gpList = collect([]);
    $userId = Auth::user()->id;
    $scheme_code_in = array();
    $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 and id = 10 order by scheme_name"));
    foreach ($scheme_list as $scheme_item) {
      array_push($scheme_code_in, $scheme_item->id);
    }
    if (AuthChecker::ReportCheckerCommon()) {
      $district_visible = $is_urban_visible = $block_visible = 1;
    } else if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
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
    $is_urban_visible = 0;
    $block_visible = 0;
    $district_visible = 0;
    return view(
      'Sarasori_Mukhyamantri.oapsmbothdsmarkoMisReport',
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
        'designation_id' => $designation_id,
      ]
    );
  }

  public function oapsmdsmarkoMisReportPost(Request $request)
  {
    //dd($request->all());
    //$ds_phase_list = Config::get('constants.ds_phase.phaselist');
    $scheme_id = $request->scheme_id;
    $ds_phase = $request->ds_phase;
    $district = $request->district;
    $urban_code = $request->urban_code;
    $block = $request->block;
    $muncid = $request->muncid;
    $gp_ward = $request->gp_ward;
    $select_year = $request->select_year;
    $select_month = $request->select_month;
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
      $user_msg = " Duare Sarkar VII Camps Marking Mis Report for the Scheme " . $scheme_row->scheme_name;
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
          $data = $this->getWardWiseOapsmDs($scheme_id, $district, $block, $muncid, $gp_ward, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
        } else {
          $column = "GP";
          $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
          $data = $this->getGpWiseOapsmDs($scheme_id, $district, $block, NULL, $gp_ward, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
        }
      } else if (!empty($muncid)) {
        $column = "Ward";
        $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
        $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
        $data = $this->getWardWiseOapsmDs($scheme_id, $district, $block, $muncid, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
      } else if (!empty($block)) {
        if ($urban_code == 1) {
          $column = "Municipality";
          $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
          $data = $this->getMuncWiseOapsmDs($scheme_id, $district, $block, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
        } else if ($urban_code == 2) {
          $block_arr = Taluka::where('block_code', '=', $block)->first();
          $column = "GP";
          $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
          $data = $this->getGpWiseOapsmDs($scheme_id, $district, $block, NULL, $gp_ward, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
        }
      } else {

        if (!empty($district)) {
          if ($urban_code == 1) {
            $column = "Sub Division";
            $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $data = $this->getSubDivWiseOapsmDs($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
          } else if ($urban_code == 2) {
            $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block";
            $data = $this->getBlockWiseOapsmDs($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
          } else {
            $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block/Sub Division";
            $data1 = $this->getBlockWiseOapsmDs($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
            $data2 = $this->getSubDivWiseOapsmDs($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
            $data = array_merge($data1, $data2);
          }
        } else {
          $column = "District";
          $heading_msg = 'District Wise ' . $user_msg;
          $data = $this->getDistrictWiseOapsmDs($scheme_id, NULL, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);

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

  public function OapBothSmDsmarkMisReportPost(Request $request)
  {
    //dd($request->all());
    //$ds_phase_list = Config::get('constants.ds_phase.phaselist');
    $scheme_id = $request->scheme_id;
    $ds_phase = $request->ds_phase;
    $district = $request->district;
    $urban_code = $request->urban_code;
    $block = $request->block;
    $muncid = $request->muncid;
    $gp_ward = $request->gp_ward;
    $select_year = $request->select_year;
    $select_month = $request->select_month;
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
      $user_msg = " Duare Sarkar Camps Marking Mis Report for the Scheme " . $scheme_row->scheme_name;
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
          $data = $this->getWardWiseOapsmBothDs($scheme_id, $district, $block, $muncid, $gp_ward, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
        } else {
          $column = "GP";
          $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
          $data = $this->getGpWiseOapsmBothDs($scheme_id, $district, $block, NULL, $gp_ward, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
        }
      } else if (!empty($muncid)) {
        $column = "Ward";
        $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
        $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
        $data = $this->getWardWiseOapsmBothDs($scheme_id, $district, $block, $muncid, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
      } else if (!empty($block)) {
        if ($urban_code == 1) {
          $column = "Municipality";
          $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
          $data = $this->getMuncWiseOapsmBothDs($scheme_id, $district, $block, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
        } else if ($urban_code == 2) {
          $block_arr = Taluka::where('block_code', '=', $block)->first();
          $column = "GP";
          $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
          $data = $this->getGpWiseOapsmBothDs($scheme_id, $district, $block, NULL, $gp_ward, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
        }
      } else {

        if (!empty($district)) {
          if ($urban_code == 1) {
            $column = "Sub Division";
            $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $data = $this->getSubDivWiseOapsmBothDs($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
          } else if ($urban_code == 2) {
            $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block";
            $data = $this->getBlockWiseOapsmBothDs($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
          } else {
            $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block/Sub Division";
            $data1 = $this->getBlockWiseOapsmBothDs($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
            $data2 = $this->getSubDivWiseOapsmBothDs($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
            $data = array_merge($data1, $data2);
          }
        } else {
          $column = "District";
          $heading_msg = 'District Wise ' . $user_msg;
          $data = $this->getDistrictWiseOapsmBothDs($scheme_id, NULL, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);

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


  public function getDistrictWiseOapsmDs($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL, $select_year = NULL, $select_month = NULL)
  {
    //dd($select_month);
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }
    $month = "";
    if ($select_month != "") {
      $month = "AND trim(TO_CHAR(approval_date::date, 'Month')) = '" . $select_month . "'";
    }
    $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
    $next_level_role_id_verifier = $role_id_verifier->parent_id;
    $query = "select main.location_id,main.location_name,
        COALESCE(bp_main.total_pre_mark,0) as total_pre_mark ,
		COALESCE(bp_main.verification_pending,0) as verification_pending,
		COALESCE(bp_main.total_mark,0) as total_mark ,
		COALESCE(bp_main.mark_with_pending,0) as mark_with_pending,
		COALESCE(bp_main.mark_and_pending,0) as mark_and_pending,
    COALESCE(bp_main.total_v_mark_7,0) as total_v_mark_7 
        from
        (
        select district_code as location_id,district_name as location_name
        from public.m_district  
        ) as main 
		LEFT JOIN
        (
              select 
			    count(1)  as verification_pending,
          count(1) filter(WHERE ds_phase=8 and sm_ds_mark IS NULL) as total_pre_mark,
          
		      count(1) filter(WHERE sm_ds_mark=1 and sm_ds_mark_vii=1) as total_mark,
          count(1) filter(WHERE ((sm_ds_mark=1 and sm_ds_mark_vii=1) or (ds_phase=8 and sm_ds_mark IS NULL)) and is_verified=1 and next_level_role_id>0) as total_v_mark_7,

		      count(1) filter(WHERE sm_ds_mark=1 and sm_ds_mark_role_id=1) as mark_with_pending,
		      count(1) filter(WHERE sm_ds_mark=1 and sm_ds_mark_role_id=2) as mark_and_pending,

              created_by_dist_code
              from " . $schema . ".beneficiary where (next_level_role_id IS NULL or next_level_role_id=0 or next_level_role_id=" . $next_level_role_id_verifier . ")
             
              group by created_by_dist_code
         )  
        as bp_main ON main.location_id=bp_main.created_by_dist_code
         order by main.location_name";
    //echo $query;die;
    $result = DB::connection('pgsql_mis')->select($query);
    // dd($result);
    return $result;
  }

  public function getDistrictWiseOapsmBothDs($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL, $select_year = NULL, $select_month = NULL)
  {
    //dd(123);
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }
    $month = "";
    if ($select_month != "") {
      $month = "AND trim(TO_CHAR(approval_date::date, 'Month')) = '" . $select_month . "'";
    }
    $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
    $next_level_role_id_verifier = $role_id_verifier->parent_id;
    $query = "select main.location_id,main.location_name,
    COALESCE(bp_main.total_ds_phase_8_pre_mark,0) as total_ds_phase_8_pre_mark ,
COALESCE(bp_main.total_ds_phase_9_pre_mark,0) as total_ds_phase_9_pre_mark ,
COALESCE(bp_main.verification_pending,0) as verification_pending,
COALESCE(bp_main.total_mark_ds_phase_8,0) as total_mark_ds_phase_8 ,
COALESCE(bp_main.total_mark_ds_phase_9,0) as total_mark_ds_phase_9 ,
COALESCE(bp_main.mark_with_pending,0) as mark_with_pending,
COALESCE(bp_main.mark_and_pending,0) as mark_and_pending,
COALESCE(bp_main.total_v_mark_7,0) as total_v_mark_7,
COALESCE(bp_main.total_aprove_sm,0) as total_aprove_sm,
COALESCE(bp_main.total_aprove_vii,0) as total_aprove_vii,
COALESCE(bp_main.total_aprove_viii,0) as total_aprove_viii,
COALESCE(bp_main.total_rejected_sm,0) as total_rejected_sm,
COALESCE(bp_main.total_rejected_vii,0) as total_rejected_vii,
COALESCE(bp_main.total_rejected_viii,0) as total_rejected_viii
    from
    (
    select district_code as location_id,district_name as location_name
    from public.m_district  
    ) as main 
LEFT JOIN
    (
          select 
          count(1) filter(WHERE is_rejected=0)as verification_pending,
      count(1) filter(WHERE is_rejected=0 and ds_phase=8 and sm_ds_mark IS NULL) as total_ds_phase_8_pre_mark,
      count(1) filter(WHERE is_rejected=0 and ds_phase=9 and sm_ds_mark IS NULL) as total_ds_phase_9_pre_mark,
      count(1) filter(WHERE is_rejected=0 and sm_ds_mark=1 and sm_ds_mark_vii=1) as total_mark_ds_phase_8,
  count(1) filter(WHERE is_rejected=0 and sm_ds_mark=1 and sm_ds_mark_viii=1) as total_mark_ds_phase_9,
      count(1) filter(WHERE is_rejected=0 and sm_ds_mark=1 and sm_ds_mark_role_id=1) as mark_with_pending,
      count(1) filter(WHERE  is_rejected=0 and sm_ds_mark=1 and sm_ds_mark_role_id=2) as mark_and_pending,
      count(1) filter(WHERE ((sm_ds_mark=1 and sm_ds_mark_vii=1) or (ds_phase=8 and sm_ds_mark IS NULL)) and is_verified=1 and next_level_role_id>0 and is_rejected=0) as total_v_mark_7,
      count(1) filter(WHERE (next_level_role_id=0 and ds_phase IS NULL and sm_ds_mark IS NULL and sm_flag=1)) as total_aprove_sm,
      count(1) filter(WHERE (next_level_role_id=0 and sm_flag IS NULL and   ((sm_ds_mark=1 and sm_ds_mark_vii=1 and sm_ds_mark_viii IS NULL) or ds_phase=8))) as total_aprove_vii,
      count(1) filter(WHERE (next_level_role_id=0 and sm_flag IS NULL and ((sm_ds_mark=1 and sm_ds_mark_viii=1 and sm_ds_mark_vii IS NULL) or ds_phase=9 ))) as total_aprove_viii,
      count(1) filter(WHERE (is_rejected=1 and ds_phase IS NULL and sm_ds_mark IS NULL and sm_flag=1)) as total_rejected_sm,
      count(1) filter(WHERE (is_rejected=1 and sm_flag IS NULL and   ((sm_ds_mark=1 and sm_ds_mark_vii=1 and sm_ds_mark_viii IS NULL) or ds_phase=8))) as total_rejected_vii,
      count(1) filter(WHERE (is_rejected=1 and sm_flag IS NULL and ((sm_ds_mark=1 and sm_ds_mark_viii=1 and sm_ds_mark_vii IS NULL) or ds_phase=9 )))  as total_rejected_viii,


          created_by_dist_code
          from " . $schema . ".beneficiary 
         
          group by created_by_dist_code
     )  
    as bp_main ON main.location_id=bp_main.created_by_dist_code
     order by main.location_name";
     //echo $query;die;
    $result = DB::connection('pgsql_mis')->select($query);
    // dd($result);
    return $result;
  }
  public function smDSEntryMarkReport(Request $request)
  {
    $base_date  = '2020-01-01';
    $c_time = Carbon::now();
    $c_date = $c_time->format("Y-m-d");
    $is_active = 0;
    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();;
    $designation_id = Auth::user()->designation_id;
    $district_visible = $is_urban_visible = $block_visible = 1;
    $municipality_visible = 0;
    $gp_ward_visible = 0;
    $muncList = collect([]);
    $gpList = collect([]);
    $userId = Auth::user()->id;
    $scheme_code_in = array();
    $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 and id = 10 order by scheme_name"));
    foreach ($scheme_list as $scheme_item) {
      array_push($scheme_code_in, $scheme_item->id);
    }
    if (AuthChecker::ReportCheckerCommon()) {
      $district_visible = $is_urban_visible = $block_visible = 1;
    } else if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
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
    $is_urban_visible = 0;
    $block_visible = 0;
    $district_visible = 0;
    return view(
      'Sarasori_Mukhyamantri.smDSEntryMarkReport',
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
        'designation_id' => $designation_id,
      ]
    );
  }
  public function smDSEntryMarkReportPost(Request $request)
  {
    //dd($request->all());
    //$ds_phase_list = Config::get('constants.ds_phase.phaselist');
    $scheme_id = $request->scheme_id;
    $ds_phase = $request->ds_phase;
    $district = $request->district;
    $urban_code = $request->urban_code;
    $block = $request->block;
    $muncid = $request->muncid;
    $gp_ward = $request->gp_ward;
    $select_year = $request->select_year;
    $select_month = $request->select_month;
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
      $user_msg = " Duare Sarkar Camps Marking Mis Report for the Scheme " . $scheme_row->scheme_name;
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
      

        if (!empty($district)) {
          if ($urban_code == 1) {
            $column = "Sub Division";
            $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $data = $this->getSubDivWisesmDSEntryMark($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
          } else if ($urban_code == 2) {
            $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block";
            $data = $this->getBlockWisesmDSEntryMark($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
          } else {
            $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block/Sub Division";
            $data1 = $this->getBlockWisesmDSEntryMark($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
            $data2 = $this->getSubDivWisesmDSEntryMarks($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
            $data = array_merge($data1, $data2);
          }
        } else {
          $column = "District";
          $heading_msg = 'District Wise ' . $user_msg;
          $data = $this->getDistrictWisesmDSEntryMark($scheme_id, NULL, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);

          $external = 0;
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
  public function getDistrictWisesmDSEntryMark($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL, $select_year = NULL, $select_month = NULL)
  {
    $table_heading=array(
      //array('query_result_key' => NULL,'th_lable' => 'Sl No.','rowspan' => 2,'colspan' => NULL,'rank'=>1,'isColspan'=>0),
      array('query_result_key' => 'location_name','th_lable' => 'District','rowspan' => 2,'colspan' => NULL,'rank'=>10,'isColspan'=>0),
      array('query_result_key' => 'pre_entry_vii','th_lable' => 'Entry in Duare Sarkar VII Camps','rowspan' => 2,'colspan' => NULL,'rank'=>20,'isColspan'=>0),
      array('query_result_key' => 'pre_mark_vii','th_lable' => 'Mark as Duare Sarkar VII Camps','rowspan' => 2,'colspan' => NULL,'rank'=>30,'isColspan'=>0),
      array('query_result_key' => 'entry_mark_vii','th_lable' => 'Total Entry Duare Sarkar VII','rowspan' => 2,'colspan' => NULL,'rank'=>40,'isColspan'=>0),
      array('query_result_key' => 'verify_vii','th_lable' => 'Total Verified Duare Sarkar VII','rowspan' => 2,'colspan' => NULL,'rank'=>50,'isColspan'=>0),
      array('query_result_key' => 'pre_entry_viii','th_lable' => 'Entry in Duare Sarkar VIII Camps','rowspan' => 2,'colspan' => NULL,'rank'=>60,'isColspan'=>0),
      array('query_result_key' => 'pre_mark_viii','th_lable' => 'Mark as Duare Sarkar VIII Camps','rowspan' => 2,'colspan' => NULL,'rank'=>70,'isColspan'=>0),
      array('query_result_key' => 'mark_both_vii_viii','th_lable' => 'Mark in Both Duare Sarkar VII & VIII','rowspan' => 2,'colspan' => NULL,'rank'=>80,'isColspan'=>0),
      array('query_result_key' => NULL,'th_lable' => 'Total Approved','rowspan' => NULL,'colspan' => 8,'rank'=>85,'isColspan'=>0),

      array('query_result_key' => 'total_approved','th_lable' => 'Total','rowspan' => NULL,'colspan' => NULL,'rank'=>90,'isColspan'=>1),
      array('query_result_key' => 'sm_approve','th_lable' => 'Only SM Mark','rowspan' => NULL,'colspan' => NULL,'rank'=>100,'isColspan'=>1),
      array('query_result_key' => 'vii_approve','th_lable' => 'Only DS Phase VII','rowspan' => 2,'colspan' => NULL,'rank'=>110,'isColspan'=>1),
      array('query_result_key' => 'viii_approve','th_lable' => 'Only DS Phase VIII','rowspan' => 2,'colspan' => NULL,'rank'=>120,'isColspan'=>1),
      array('query_result_key' => 'sm_vii_approve','th_lable' => 'SM+DS VII','rowspan' => 2,'colspan' => NULL,'rank'=>130,'isColspan'=>1),
      array('query_result_key' => 'sm_viii_approve','th_lable' => 'SM+DS VIII','rowspan' => 2,'colspan' => NULL,'rank'=>140,'isColspan'=>1),
      array('query_result_key' => 'sm_vii_viii_approve','th_lable' => 'SM+DS VII+DS VIII','rowspan' => 2,'colspan' => NULL,'rank'=>150,'isColspan'=>1),
      array('query_result_key' => 'vii_viii_approve','th_lable' => 'DS VII+DS VIII','rowspan' => 2,'colspan' => NULL,'rank'=>160,'isColspan'=>1),

      array('query_result_key' => NULL,'th_lable' => 'Total Rejected','rowspan' => NULL,'colspan' => 8,'rank'=>165,'isColspan'=>0),

      array('query_result_key' => 'total_rejected','th_lable' => 'Total','rowspan' => NULL,'colspan' => NULL,'rank'=>170,'isColspan'=>1),
      array('query_result_key' => 'sm_reject','th_lable' => 'Only SM Mark','rowspan' => NULL,'colspan' => NULL,'rank'=>180,'isColspan'=>1),
      array('query_result_key' => 'vii_reject','th_lable' => 'Only DS Phase VII','rowspan' => NULL,'colspan' => NULL,'rank'=>190,'isColspan'=>1),
      array('query_result_key' => 'viii_reject','th_lable' => 'Only DS Phase VIII','rowspan' => NULL,'colspan' => NULL,'rank'=>200,'isColspan'=>1),
      array('query_result_key' => 'sm_vii_reject','th_lable' => 'SM+DS VII','rowspan' => NULL,'colspan' => NULL,'rank'=>210,'isColspan'=>1),
      array('query_result_key' => 'sm_viii_reject','th_lable' => 'SM+DS VIII','rowspan' => NULL,'colspan' => NULL,'rank'=>220,'isColspan'=>1),
      array('query_result_key' => 'sm_vii_viii_reject','th_lable' => 'SM+DS VII+DS VIII','rowspan' => NULL,'colspan' => NULL,'rank'=>230,'isColspan'=>1),
      array('query_result_key' => 'vii_viii_reject','th_lable' => 'DS VII+DS VIII','rowspan' => NULL,'colspan' => NULL,'rank'=>240,'isColspan'=>1)
  );
  $table_heading_collection = collect($table_heading);
  $table_heading_collection1=$table_heading_collection->filter(function ($table_heading_item) {
   // dump($table_heading_item);
    return $table_heading_item['isColspan'] ==0;
  });
  $table_heading_collection2=$table_heading_collection->filter(function ($table_heading_item) {
    return $table_heading_item['isColspan'] ==1;
  });
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }
    $month = "";
    if ($select_month != "") {
      $month = "AND trim(TO_CHAR(approval_date::date, 'Month')) = '" . $select_month . "'";
    }
    $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
    $next_level_role_id_verifier = $role_id_verifier->parent_id;
    $query = "select main.location_id,main.location_name,
    COALESCE(bp_main.pre_entry_vii,0) as pre_entry_vii ,
COALESCE(bp_main.pre_mark_vii,0) as pre_mark_vii ,
COALESCE(bp_main.pre_entry_vii,0)+COALESCE(bp_main.pre_mark_vii,0) as entry_mark_vii,
COALESCE(bp_main.pre_entry_viii,0) as pre_entry_viii,
COALESCE(bp_main.pre_mark_viii,0) as pre_mark_viii ,
COALESCE(bp_main.mark_both_vii_viii,0) as mark_both_vii_viii ,
COALESCE(bp_main.verify_vii,0) as verify_vii,
COALESCE(bp_main.sm_approve,0)+COALESCE(bp_main.vii_approve,0)
+COALESCE(bp_main.viii_approve,0)+COALESCE(bp_main.sm_vii_approve,0)
+COALESCE(bp_main.sm_viii_approve,0)+COALESCE(bp_main.sm_vii_viii_approve,0)
+COALESCE(bp_main.vii_viii_approve,0)
 as total_approved,
COALESCE(bp_main.sm_approve,0) as sm_approve,
COALESCE(bp_main.vii_approve,0) as vii_approve,
COALESCE(bp_main.viii_approve,0) as viii_approve,
COALESCE(bp_main.sm_vii_approve,0) as sm_vii_approve,
COALESCE(bp_main.sm_viii_approve,0) as sm_viii_approve,
COALESCE(bp_main.sm_vii_viii_approve,0) as sm_vii_viii_approve,
COALESCE(bp_main.vii_viii_approve,0) as vii_viii_approve,

COALESCE(bp_main.sm_reject,0)+COALESCE(bp_main.vii_reject,0)
+COALESCE(bp_main.viii_reject,0)+COALESCE(bp_main.sm_vii_reject,0)
+COALESCE(bp_main.sm_viii_reject,0)+COALESCE(bp_main.sm_vii_viii_reject,0)
+COALESCE(bp_main.vii_viii_reject,0)
 as total_rejected,

COALESCE(bp_main.sm_reject,0) as sm_reject,
COALESCE(bp_main.vii_reject,0) as vii_reject,
COALESCE(bp_main.viii_reject,0) as viii_reject,
COALESCE(bp_main.sm_vii_reject,0) as sm_vii_reject,
COALESCE(bp_main.sm_viii_reject,0) as sm_viii_reject,
COALESCE(bp_main.sm_vii_viii_reject,0) as sm_vii_viii_reject,
COALESCE(bp_main.vii_viii_reject,0) as vii_viii_reject
    from
    (
    select district_code as location_id,district_name as location_name
    from public.m_district  
    ) as main 
LEFT JOIN
    (
      select 
      count(1) filter(WHERE is_rejected=0 and ds_phase=8 and sm_ds_mark IS NULL) as pre_entry_vii,
     count(1) filter(WHERE is_rejected=0 and sm_ds_mark=1 and sm_ds_mark_vii=1) as pre_mark_vii,
     count(1) filter(WHERE is_rejected=0 and ds_phase=9 and sm_ds_mark IS NULL) as pre_entry_viii,
     count(1) filter(WHERE is_rejected=0 and sm_ds_mark=1 and sm_ds_mark_viii=1) as pre_mark_viii,
     count(1) filter(WHERE is_rejected=0 and sm_ds_mark=1 and sm_ds_mark_vii=1 and sm_ds_mark_viii=1) as mark_both_vii_viii,
     count(1) filter(WHERE ((sm_ds_mark=1 and sm_ds_mark_vii=1) or (ds_phase=8 and sm_ds_mark IS NULL)) and is_verified=1 and next_level_role_id=".$next_level_role_id_verifier." and is_rejected=0) as verify_vii,
   count(1) filter(WHERE (next_level_role_id=0 and ds_phase IS NULL and sm_ds_mark_vii IS NULL 
   and sm_ds_mark_viii IS NULL and sm_flag=1)) as sm_approve,
     count(1) filter(WHERE (next_level_role_id=0 and sm_flag IS NULL and   ((sm_ds_mark_vii=1 and sm_ds_mark_viii IS NULL) or ds_phase=8))) as vii_approve,
     count(1) filter(WHERE (next_level_role_id=0 and sm_flag IS NULL and ((sm_ds_mark_viii=1 and sm_ds_mark_vii IS NULL) or ds_phase=9 ))) as viii_approve,
    count(1) filter(WHERE is_rejected=0 and next_level_role_id=0 and sm_flag=1 and sm_ds_mark_vii=1 ) as sm_vii_approve,
    count(1) filter(WHERE is_rejected=0 and next_level_role_id=0 and sm_flag=1 and sm_ds_mark_viii=1 ) as sm_viii_approve,
      count(1) filter(WHERE is_rejected=0 and next_level_role_id=0 and sm_flag=1 and sm_ds_mark_vii=1 and sm_ds_mark_viii=1 ) as sm_vii_viii_approve,
     count(1) filter(WHERE is_rejected=0 and next_level_role_id=0 and sm_ds_mark_vii=1 and sm_ds_mark_viii=1 ) as vii_viii_approve,
   
        count(1) filter(WHERE (is_rejected=1 and ds_phase IS NULL and sm_ds_mark_vii IS NULL and sm_ds_mark_viii IS NULL and sm_flag=1)) as sm_reject,
     count(1) filter(WHERE (is_rejected=1 and sm_flag IS NULL and   
     ((sm_ds_mark=1 and sm_ds_mark_vii=1 and sm_ds_mark_viii IS NULL) or ds_phase=8))) as vii_reject,
     count(1) filter(WHERE (is_rejected=1 and sm_flag IS NULL and ((sm_ds_mark=1 and sm_ds_mark_viii=1 and sm_ds_mark_vii IS NULL) or ds_phase=9 ))) as viii_reject,
    count(1) filter(WHERE is_rejected=1 and sm_flag=1 and sm_ds_mark_vii=1 ) as sm_vii_reject,
    count(1) filter(WHERE is_rejected=1 and sm_flag=1 and sm_ds_mark_viii=1 ) as sm_viii_reject,
      count(1) filter(WHERE is_rejected=1 and sm_flag=1 and sm_ds_mark_vii=1 and sm_ds_mark_viii=1 ) as sm_vii_viii_reject,
     count(1) filter(WHERE is_rejected=1 and sm_ds_mark_vii=1 and sm_ds_mark_viii=1 ) as vii_viii_reject, 
    
      created_by_dist_code
    from " . $schema . ".beneficiary     
  group by created_by_dist_code
     )  
    as bp_main ON main.location_id=bp_main.created_by_dist_code
     order by main.location_name";
     //echo $query;die;
    $result = DB::connection('pgsql_mis')->select($query);
    //dd($result);
    $table='<table id="example" class="table table-striped table-bordered table2excel" style="width:100%">';
    $table=$table.'<thead>';
    if(count($table_heading_collection1)){
      $table=$table.'<tr>';
      foreach($table_heading_collection1 as $th_item1){
        $table=$table.'<th ';
        if($th_item1['rowspan']!=null){
          $table=$table.'rowspan="'.$th_item1['rowspan'].'"';
        }
        if($th_item1['colspan']!=null){
          $table=$table.'class="isColspan" colspan="'.$th_item1['colspan'].'"';
        }
        $table=$table.'>';
      $table=$table.($th_item1['th_lable']);
      $table=$table.'</th>';
      }
      $table=$table.'</tr>';
    }
    if(count($table_heading_collection2)){
      $table=$table.'<tr>';
      foreach($table_heading_collection2 as $th_item2){
      $table=$table.'<th>';
      $table=$table.($th_item2['th_lable']);
      $table=$table.'</th>';
      }
      $table=$table.'</tr>';
    }
    $table=$table.'</thead>';
    $i=1;
    if(count($result)>0){
      $table=$table.'<tbody>';

    foreach($result as $result_item){
      $table=$table.'<tr>';
      foreach($table_heading_collection as $th_item){
        if($th_item['query_result_key']==null){
         
          continue;
        }
        $col='';
        $col=$th_item['query_result_key'];
        $table=$table.'<td>'.$result_item->$col.'</td>';

      }

      $i++;
      $table=$table.'</tr>';


    }
    $table=$table.'</tbody>';
    }
    $table=$table.'<tfoot><tr></tr></tfoot>';

    $table=$table.'</table>';
    return $table;
  }
  public function smDSEntryMarkReportSet2(Request $request)
  {
    $base_date  = '2020-01-01';
    $c_time = Carbon::now();
    $c_date = $c_time->format("Y-m-d");
    $is_active = 0;
    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();;
    $designation_id = Auth::user()->designation_id;
    $district_visible = $is_urban_visible = $block_visible = 1;
    $municipality_visible = 0;
    $gp_ward_visible = 0;
    $muncList = collect([]);
    $gpList = collect([]);
    $userId = Auth::user()->id;
    $scheme_code_in = array();
    $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 and id = 10 order by scheme_name"));
    foreach ($scheme_list as $scheme_item) {
      array_push($scheme_code_in, $scheme_item->id);
    }
    if (AuthChecker::AdminChecker() || AuthChecker::HODChecker() || AuthChecker::HOPChecker() || AuthChecker::MisStateChecker() ||  AuthChecker::DashboardChecker()) {
      $district_visible = $is_urban_visible = $block_visible = 1;
    } else if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
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
    $is_urban_visible = 0;
    $block_visible = 0;
    $district_visible = 0;
    return view(
      'Sarasori_Mukhyamantri.smDSEntryMarkReportSet2',
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
        'designation_id' => $designation_id,
      ]
    );
  }
  public function smDSEntryMarkReportSet2Post(Request $request)
  {
    //dd($request->all());
    //$ds_phase_list = Config::get('constants.ds_phase.phaselist');
    $scheme_id = $request->scheme_id;
    $ds_phase = $request->ds_phase;
    $district = $request->district;
    $urban_code = $request->urban_code;
    $block = $request->block;
    $muncid = $request->muncid;
    $gp_ward = $request->gp_ward;
    $select_year = $request->select_year;
    $select_month = $request->select_month;
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
      $user_msg = " Duare Sarkar Camps Marking Mis Report for the Scheme " . $scheme_row->scheme_name;
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
      

        if (!empty($district)) {
          if ($urban_code == 1) {
            $column = "Sub Division";
            $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $data = $this->getSubDivWisesmDSEntryMark($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
          } else if ($urban_code == 2) {
            $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block";
            $data = $this->getBlockWisesmDSEntryMark($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
          } else {
            $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block/Sub Division";
            $data1 = $this->getBlockWisesmDSEntryMark($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
            $data2 = $this->getSubDivWisesmDSEntryMarks($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
            $data = array_merge($data1, $data2);
          }
        } else {
          $column = "District";
          $heading_msg = 'District Wise ' . $user_msg;
          $data = $this->getDistrictWisesmDSEntryMarkSet2($scheme_id, NULL, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);

          $external = 0;
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
  public function getDistrictWisesmDSEntryMarkSet2($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL, $select_year = NULL, $select_month = NULL)
  {
    $table_heading=array(
      //array('query_result_key' => NULL,'th_lable' => 'Sl No.','rowspan' => 2,'colspan' => NULL,'rank'=>1,'isColspan'=>0),
      array('query_result_key' => 'location_name','th_lable' => 'District','rowspan' => 2,'colspan' => NULL,'rank'=>10,'isColspan'=>0),
      array('query_result_key' => NULL,'th_lable' => 'Total Approved','rowspan' => NULL,'colspan' => 8,'rank'=>85,'isColspan'=>0),

      array('query_result_key' => 'total_approved','th_lable' => 'Total','rowspan' => NULL,'colspan' => NULL,'rank'=>90,'isColspan'=>1),
      array('query_result_key' => 'sm_approve','th_lable' => 'Only SM Mark','rowspan' => NULL,'colspan' => NULL,'rank'=>100,'isColspan'=>1),
      array('query_result_key' => 'vii_approve','th_lable' => 'Only DS Phase VII','rowspan' => 2,'colspan' => NULL,'rank'=>110,'isColspan'=>1),
      array('query_result_key' => 'viii_approve','th_lable' => 'Only DS Phase VIII','rowspan' => 2,'colspan' => NULL,'rank'=>120,'isColspan'=>1),
      array('query_result_key' => 'sm_vii_approve','th_lable' => 'SM+DS VII','rowspan' => 2,'colspan' => NULL,'rank'=>130,'isColspan'=>1),
      array('query_result_key' => 'sm_viii_approve','th_lable' => 'SM+DS VIII','rowspan' => 2,'colspan' => NULL,'rank'=>140,'isColspan'=>1),
      array('query_result_key' => 'sm_vii_viii_approve','th_lable' => 'SM+DS VII+DS VIII','rowspan' => 2,'colspan' => NULL,'rank'=>150,'isColspan'=>1),
      array('query_result_key' => 'vii_viii_approve','th_lable' => 'DS VII+DS VIII','rowspan' => 2,'colspan' => NULL,'rank'=>160,'isColspan'=>1),

      array('query_result_key' => NULL,'th_lable' => 'Total Rejected','rowspan' => NULL,'colspan' => 8,'rank'=>165,'isColspan'=>0),

      array('query_result_key' => 'total_rejected','th_lable' => 'Total','rowspan' => NULL,'colspan' => NULL,'rank'=>170,'isColspan'=>1),
      array('query_result_key' => 'sm_reject','th_lable' => 'Only SM Mark','rowspan' => NULL,'colspan' => NULL,'rank'=>180,'isColspan'=>1),
      array('query_result_key' => 'vii_reject','th_lable' => 'Only DS Phase VII','rowspan' => NULL,'colspan' => NULL,'rank'=>190,'isColspan'=>1),
      array('query_result_key' => 'viii_reject','th_lable' => 'Only DS Phase VIII','rowspan' => NULL,'colspan' => NULL,'rank'=>200,'isColspan'=>1),
      array('query_result_key' => 'sm_vii_reject','th_lable' => 'SM+DS VII','rowspan' => NULL,'colspan' => NULL,'rank'=>210,'isColspan'=>1),
      array('query_result_key' => 'sm_viii_reject','th_lable' => 'SM+DS VIII','rowspan' => NULL,'colspan' => NULL,'rank'=>220,'isColspan'=>1),
      array('query_result_key' => 'sm_vii_viii_reject','th_lable' => 'SM+DS VII+DS VIII','rowspan' => NULL,'colspan' => NULL,'rank'=>230,'isColspan'=>1),
      array('query_result_key' => 'vii_viii_reject','th_lable' => 'DS VII+DS VIII','rowspan' => NULL,'colspan' => NULL,'rank'=>240,'isColspan'=>1)
  );
  $table_heading_collection = collect($table_heading);
  $table_heading_collection1=$table_heading_collection->filter(function ($table_heading_item) {
   // dump($table_heading_item);
    return $table_heading_item['isColspan'] ==0;
  });
  $table_heading_collection2=$table_heading_collection->filter(function ($table_heading_item) {
    return $table_heading_item['isColspan'] ==1;
  });
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }
    $month = "";
    if ($select_month != "") {
      $month = "AND trim(TO_CHAR(approval_date::date, 'Month')) = '" . $select_month . "'";
    }
    $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
    $next_level_role_id_verifier = $role_id_verifier->parent_id;
    $query = "select main.location_id,main.location_name,
COALESCE(bp_main.sm_approve,0)+COALESCE(bp_main.vii_approve,0)
+COALESCE(bp_main.viii_approve,0)+COALESCE(bp_main.sm_vii_approve,0)
+COALESCE(bp_main.sm_viii_approve,0)+COALESCE(bp_main.sm_vii_viii_approve,0)
+COALESCE(bp_main.vii_viii_approve,0)
 as total_approved,
COALESCE(bp_main.sm_approve,0) as sm_approve,
COALESCE(bp_main.vii_approve,0) as vii_approve,
COALESCE(bp_main.viii_approve,0) as viii_approve,
COALESCE(bp_main.sm_vii_approve,0) as sm_vii_approve,
COALESCE(bp_main.sm_viii_approve,0) as sm_viii_approve,
COALESCE(bp_main.sm_vii_viii_approve,0) as sm_vii_viii_approve,
COALESCE(bp_main.vii_viii_approve,0) as vii_viii_approve,

COALESCE(bp_main.sm_reject,0)+COALESCE(bp_main.vii_reject,0)
+COALESCE(bp_main.viii_reject,0)+COALESCE(bp_main.sm_vii_reject,0)
+COALESCE(bp_main.sm_viii_reject,0)+COALESCE(bp_main.sm_vii_viii_reject,0)
+COALESCE(bp_main.vii_viii_reject,0)
 as total_rejected,

COALESCE(bp_main.sm_reject,0) as sm_reject,
COALESCE(bp_main.vii_reject,0) as vii_reject,
COALESCE(bp_main.viii_reject,0) as viii_reject,
COALESCE(bp_main.sm_vii_reject,0) as sm_vii_reject,
COALESCE(bp_main.sm_viii_reject,0) as sm_viii_reject,
COALESCE(bp_main.sm_vii_viii_reject,0) as sm_vii_viii_reject,
COALESCE(bp_main.vii_viii_reject,0) as vii_viii_reject
    from
    (
    select district_code as location_id,district_name as location_name
    from public.m_district  
    ) as main 
LEFT JOIN
    (
      select 
     
   count(1) filter(WHERE (next_level_role_id=0 and ds_phase IS NULL and sm_ds_mark_vii IS NULL 
   and sm_ds_mark_viii IS NULL and sm_flag=1)) as sm_approve,
     count(1) filter(WHERE (next_level_role_id=0 and sm_flag IS NULL and   ((sm_ds_mark_vii=1 and sm_ds_mark_viii IS NULL) or ds_phase=8))) as vii_approve,
     count(1) filter(WHERE (next_level_role_id=0 and sm_flag IS NULL and ((sm_ds_mark_viii=1 and sm_ds_mark_vii IS NULL) or ds_phase=9 ))) as viii_approve,
    count(1) filter(WHERE is_rejected=0 and next_level_role_id=0 and sm_flag=1 and sm_ds_mark_vii=1 ) as sm_vii_approve,
    count(1) filter(WHERE is_rejected=0 and next_level_role_id=0 and sm_flag=1 and sm_ds_mark_viii=1 ) as sm_viii_approve,
      count(1) filter(WHERE is_rejected=0 and next_level_role_id=0 and sm_flag=1 and sm_ds_mark_vii=1 and sm_ds_mark_viii=1 ) as sm_vii_viii_approve,
     count(1) filter(WHERE is_rejected=0 and next_level_role_id=0 and sm_ds_mark_vii=1 and sm_ds_mark_viii=1 ) as vii_viii_approve,
   
        count(1) filter(WHERE (is_rejected=1 and ds_phase IS NULL and sm_ds_mark_vii IS NULL and sm_ds_mark_viii IS NULL and sm_flag=1)) as sm_reject,
     count(1) filter(WHERE (is_rejected=1 and sm_flag IS NULL and   
     ((sm_ds_mark=1 and sm_ds_mark_vii=1 and sm_ds_mark_viii IS NULL) or ds_phase=8))) as vii_reject,
     count(1) filter(WHERE (is_rejected=1 and sm_flag IS NULL and ((sm_ds_mark=1 and sm_ds_mark_viii=1 and sm_ds_mark_vii IS NULL) or ds_phase=9 ))) as viii_reject,
    count(1) filter(WHERE is_rejected=1 and sm_flag=1 and sm_ds_mark_vii=1 ) as sm_vii_reject,
    count(1) filter(WHERE is_rejected=1 and sm_flag=1 and sm_ds_mark_viii=1 ) as sm_viii_reject,
      count(1) filter(WHERE is_rejected=1 and sm_flag=1 and sm_ds_mark_vii=1 and sm_ds_mark_viii=1 ) as sm_vii_viii_reject,
     count(1) filter(WHERE is_rejected=1 and sm_ds_mark_vii=1 and sm_ds_mark_viii=1 ) as vii_viii_reject, 
    
      created_by_dist_code
    from " . $schema . ".beneficiary     
  group by created_by_dist_code
     )  
    as bp_main ON main.location_id=bp_main.created_by_dist_code
     order by main.location_name";
     //echo $query;die;
    $result = DB::connection('pgsql_mis')->select($query);
    //dd($result);
    $table='<table id="example" class="table table-striped table-bordered table2excel" style="width:100%">';
    $table=$table.'<thead>';
    if(count($table_heading_collection1)){
      $table=$table.'<tr>';
      foreach($table_heading_collection1 as $th_item1){
        $table=$table.'<th ';
        if($th_item1['rowspan']!=null){
          $table=$table.'rowspan="'.$th_item1['rowspan'].'"';
        }
        if($th_item1['colspan']!=null){
          $table=$table.'class="isColspan" colspan="'.$th_item1['colspan'].'"';
        }
        $table=$table.'>';
      $table=$table.($th_item1['th_lable']);
      $table=$table.'</th>';
      }
      $table=$table.'</tr>';
    }
    if(count($table_heading_collection2)){
      $table=$table.'<tr>';
      foreach($table_heading_collection2 as $th_item2){
      $table=$table.'<th>';
      $table=$table.($th_item2['th_lable']);
      $table=$table.'</th>';
      }
      $table=$table.'</tr>';
    }
    $table=$table.'</thead>';
    $i=1;
    if(count($result)>0){
      $table=$table.'<tbody>';

    foreach($result as $result_item){
      $table=$table.'<tr>';
      foreach($table_heading_collection as $th_item){
        if($th_item['query_result_key']==null){
         
          continue;
        }
        $col='';
        $col=$th_item['query_result_key'];
        $table=$table.'<td>'.$result_item->$col.'</td>';

      }

      $i++;
      $table=$table.'</tr>';


    }
    $table=$table.'</tbody>';
    }
    $table=$table.'<tfoot><tr></tr></tfoot>';

    $table=$table.'</table>';
    return $table;
  }

}



