<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Scheme;
use App\District;
use App\Configduty;
use App\DocumentType;
use App\GP;
use App\Taluka;
use App\UrbanBody;
use App\Ward;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\RejectRevertReason;
use App\AcceptRejectInfo;
use Illuminate\Support\Facades\Input;
use App\Helpers\Helper;
use App\BlkUrbanlEntryMapping;
use Carbon\Carbon;
use App\MapLavel;
use App\SchemeDocMap;
use Response;
use App\DsPhase;
use Illuminate\Support\Facades\Storage;
use App\BenDocs;
use App\BankDetails;
use App\Helpers\AuthChecker;
use App\Helpers\PermissionManagement;


class WorkflowLb60Controller extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    date_default_timezone_set('Asia/Kolkata');
    $this->aadhar_doc_type_id = 6;
    $this->supporting_dob_type_id = 258;
    $this->reason_order_type_id = 269;
  }
  public function shemeSelection(Request $request)
  {
    $auth = AuthChecker::ReportChecker();
    if ($auth) {
      $user_id = AuthChecker::getUserId();
      if (AuthChecker::ReportChecker()) {
        $schemes = DB::select(DB::raw("select id,scheme_name,display_name,is_active from m_scheme where id  IN (1,3,10) and  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
        //dd($schemes);
        return view(
          'Lokkhibhandar60/SchemeSelection',
          [
            'scheme_list' => $schemes,
          ]
        );
      } else {
        return redirect("/")->with('danger', 'Not Allowed');
      }
    }
  }
  public function ListView(Request $request)
  {
    try {
      //  dd($request->all());
      $c_time = date('Y-m-d H:i:s', time());
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
      $is_verifier = AuthChecker::VerifierChecker();
      $is_approver = AuthChecker::ApproverChecker();
      $is_hod = AuthChecker::HODChecker();

      $district_list_obj = District::get();
      //dd($duty_obj->mapping_level);
      $district_code = $duty_obj->district_code;
      $urban_bodys = collect([]);
      $gps = collect([]);
      //$district_list_obj = collect([]);
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
      } else if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
        $is_rural = 2;
        $verifier_type = 'Block';
        $urban_bodys = collect([]);
        $taluka_code = $duty_obj->taluka_code;
        $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
      } else if ($duty_obj->mapping_level == "District") {
        $district_list_obj = District::get();
        $verifier_type = 'District';
        $is_rural = NULL;
        $created_by_local_body_code = NULL;
      } else if ($duty_obj->mapping_level == "Department") {
        $verifier_type = 'Department';
        $is_rural = NULL;
        $created_by_local_body_code = NULL;
      }
      if (AuthChecker::VerifierChecker()) {
        // $dup_mark_lb = DB::select("select " . $schema . ".dup_mark_lb(in_c_time => '" . $c_time . "',in_user_id => " . $user_id . ",in_created_by_local_body_code => " . $created_by_local_body_code . ")");     
      }
      if (request()->ajax()) {
        $limit = $request->input('length');
        $offset = $request->input('start');
        $role_arr_verfied = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
        $next_level_role_id_verified = $role_arr_verfied->parent_id;
        if ($request->application_type == 5) {
          if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
            // dd(11);
            $query = DB::table($schema . '.beneficiaries')
              ->where('scheme_id', $scheme_id)
              ->where('created_by_dist_code', $district_code);
          } else {
            $query = DB::table($schema . '.beneficiaries')->where('scheme_id', $scheme_id);
            if (!empty($request->dist_code)) {
              $query = $query->where('created_by_dist_code', $request->dist_code)->where('scheme_id', $scheme_id);
            }
            if (!empty($request->created_by_local_body_code)) {
              $query = $query->where('created_by_local_body_code', $request->created_by_local_body_code);
            }

          }
        } else {
          if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
            $query = DB::table($schema . '.beneficiaries')->where('scheme_id', $scheme_id)
              ->where('is_lb_imported', 1)->where('created_by_dist_code', $district_code);
          } else {
            $query = DB::table($schema . '.beneficiaries')->where('scheme_id', $scheme_id)
              ->where('is_lb_imported', 1);
            //dd($request->dist_code);
            if (!empty($request->dist_code) && isset($request->dist_code) && ($request->dist_code !== 'undefined')) {
              $query = $query->where('created_by_dist_code', $request->dist_code);
            }
            if (!empty($request->created_by_local_body_code) && isset($request->created_by_local_body_code) && ($request->created_by_local_body_code !== 'undefined')) {
              $query = $query->where('created_by_local_body_code', $request->created_by_local_body_code);
            }
          }
        }
        if (AuthChecker::VerifierChecker()) {
          $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
        }
        if (AuthChecker::VerifierChecker()) {
          //$query = $query->whereIn('no_aadhar_mobile_flag', [1, 2, 3]);
        }
        if (AuthChecker::ApproverChecker()) {
          // $query = $query->whereIn('no_aadhar_mobile_flag', [2, 3]);
        }
        if ($duty_obj->mapping_level == "Subdiv") {
          if (!empty($request->block_ulb_code) && isset($request->block_ulb_code) && ($request->block_ulb_code !== 'undefined')) {
            $query = $query->where('block_ulb_code', $request->block_ulb_code);
          }
        }
        if (!empty($request->gp_ward_code) && isset($request->gp_ward_code) && ($request->gp_ward_code !== 'undefined')) {
          $query = $query->where('gp_ward_code', $request->gp_ward_code);
        }
        if (!empty($request->application_type)) {
          if ($request->application_type == 1) {
            //Pending
            if (AuthChecker::ApproverChecker()) {
              $query = $query->whereraw(" (((is_transfer=1 and transfer_finalize IS NULL) or (back_lb=1 and back_lb_finalize IS NULL) or next_level_role_id=" . $next_level_role_id_verified . ") and is_rejected=0)");
            } else
              $query = $query->whereNull('next_level_role_id')->whereNull('transfer_to_scheme_id')->whereNull('is_transfer')->whereNull('back_lb');
          } else if ($request->application_type == 2) {
            //Verified and Yet not Approved
            $query = $query->where('next_level_role_id', $next_level_role_id_verified);
          } else if ($request->application_type == 3) {
            //Verified and Approved

            $query = $query->where('next_level_role_id', 0);
          } else if ($request->application_type == 4) {
            //Rejected
            $query = $query->where('next_level_role_id', -3);
          } else if ($request->application_type == 6) {
            //Received from bandhu
            $query = $query->whereNull('next_level_role_id')->where('transfer_from_scheme_id', 3);
          } else if ($request->application_type == 7) {
            //Transfer to Bandhu
            $query = $query->where('is_transfer', 1)->where('transfer_to_scheme_id', 3);
          } else if ($request->application_type == 9) {
            //Received from Johar
            $query = $query->whereNull('next_level_role_id')->where('transfer_from_scheme_id', 1);
          } else if ($request->application_type == 10) {
            //Transfer to Johar
            $query = $query->where('is_transfer', 1)->where('transfer_to_scheme_id', 1);
          } else if ($request->application_type == 11) {
            //Transfer to OAP
            $query = $query->where('is_transfer', 1)->where('transfer_to_scheme_id', 10);
          } else if ($request->application_type == 12) {
            //Received from Bandhu
            $query = $query->whereNull('next_level_role_id')->where('transfer_from_scheme_id', 3);
          } else if ($request->application_type == 13) {
            //Received from Johar
            $query = $query->whereNull('next_level_role_id')->where('transfer_from_scheme_id', 1);
          } else if ($request->application_type == 14) {
            //Received from Johar
            $query = $query->where('back_lb', 1);
          }
        }
        $application_type = $request->application_type;
        $serachvalue = $request->search['value'];
        if (empty($serachvalue)) {
          $totalRecords = $query->count();
          if ($request->application_type == 5) {
            $data = $query->orderBy(DB::raw('regexp_replace(trim(ben_full_name), \'.*\\s\', \'\')'), 'ASC')->orderBy('dob', 'ASC')->offset($offset)->limit($limit)->get([
              'lb_application_id',
              'lb_beneficiary_id',
              'created_by_dist_code',
              'dob',
              'bank_code',
              'ben_full_name',
              'gender',
              'block_ulb_name',
              'gp_ward_name',
              'bank_ifsc',
              'next_level_role_id',
              'mobile_no',
              'jb_dup_bank',
              'jb_dup_aadhar',
              'jb_dup_caste_certificate_no'
            ]);
          } else {
            $data = $query->orderBy(DB::raw('regexp_replace(trim(ben_full_name), \'.*\\s\', \'\')'), 'ASC')->orderBy('dob', 'ASC')->offset($offset)->limit($limit)->get([
              'id',
              'lb_application_id',
              'lb_beneficiary_id',
              'created_by_dist_code',
              'dob',
              'bank_code',
              'ben_full_name',
              'gender',
              'block_ulb_name',
              'gp_ward_name',
              'bank_ifsc',
              'next_level_role_id',
              'mobile_no',
              'is_rejected',
              'is_transfer',
              'transfer_finalize',
              'transfer_from_scheme_id',
              'transfer_to_scheme_id',
              'back_lb',
              'back_lb_finalize',

            ]);
          }
          $filterRecords = count($data);
        } else {
          if (is_numeric($serachvalue)) {
            $query = $query->where(function ($query1) use ($serachvalue) {
              $query1->where('id', $serachvalue)
                ->orWhere('lb_application_id', $serachvalue)->orWhere('bank_code', $serachvalue);
            });
            $totalRecords = $query->count();
            if ($request->application_type == 5) {
              $data = $query->orderBy(DB::raw('regexp_replace(trim(ben_full_name), \'.*\\s\', \'\')'), 'ASC')->orderBy('dob', 'ASC')->offset($offset)->limit($limit)->get(
                [
                  'id',
                  'lb_application_id',
                  'lb_beneficiary_id',
                  'created_by_dist_code',
                  'dob',
                  'bank_code',
                  'ben_full_name',
                  'gender',
                  'block_ulb_name',
                  'gp_ward_name',
                  'bank_ifsc',
                  'next_level_role_id',
                  'mobile_no',
                  'jb_dup_bank',
                  'jb_dup_aadhar',
                  'jb_dup_caste_certificate_no'
                ]
              );
            } else {
              $data = $query->orderBy(DB::raw('regexp_replace(trim(ben_full_name), \'.*\\s\', \'\')'), 'ASC')->orderBy('dob', 'ASC')->offset($offset)->limit($limit)->get(
                [
                  'id',
                  'lb_application_id',
                  'lb_beneficiary_id',
                  'created_by_dist_code',
                  'dob',
                  'bank_code',
                  'ben_full_name',
                  'gender',
                  'block_ulb_name',
                  'gp_ward_name',
                  'bank_ifsc',
                  'next_level_role_id',
                  'mobile_no',
                  'is_rejected',
                  'is_transfer',
                  'transfer_finalize',
                  'transfer_from_scheme_id',
                  'transfer_to_scheme_id',
                  'back_lb',
                  'back_lb_finalize',
                ]
              );
            }
          } else {
            $query = $query->where(function ($query1) use ($serachvalue) {
              $query1->where('ben_fname', 'like', $serachvalue . '%')
                ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
            });
            $totalRecords = $query->count();
            if ($request->application_type == 5) {
              $data = $query->orderBy(DB::raw('regexp_replace(trim(ben_full_name), \'.*\\s\', \'\')'), 'ASC')->orderBy('dob', 'ASC')->offset($offset)->limit($limit)->get(
                [
                  'id',
                  'lb_application_id',
                  'lb_beneficiary_id',
                  'created_by_dist_code',
                  'dob',
                  'bank_code',
                  'ben_full_name',
                  'gender',
                  'block_ulb_name',
                  'gp_ward_name',
                  'bank_ifsc',
                  'next_level_role_id',
                  'mobile_no',
                  'jb_dup_bank',
                  'jb_dup_aadhar',
                  'jb_dup_caste_certificate_no'
                ]
              );
            } else {
              $data = $query->orderBy(DB::raw('regexp_replace(trim(ben_full_name), \'.*\\s\', \'\')'), 'ASC')->orderBy('dob', 'ASC')->offset($offset)->limit($limit)->get(
                [
                  'id',
                  'lb_application_id',
                  'lb_beneficiary_id',
                  'created_by_dist_code',
                  'dob',
                  'bank_code',
                  'ben_full_name',
                  'gender',
                  'block_ulb_name',
                  'gp_ward_name',
                  'bank_ifsc',
                  'next_level_role_id',
                  'mobile_no',
                  'is_rejected',
                  'is_transfer',
                  'transfer_finalize',
                  'transfer_from_scheme_id',
                  'transfer_to_scheme_id',
                  'back_lb',
                  'back_lb_finalize',
                ]
              );
            }
          }
          $filterRecords = count($data);
        }
        return datatables()->of($data)->setTotalRecords($totalRecords)
          ->setFilteredRecords($filterRecords)
          ->skipPaging()->addColumn('status', function ($data) use ($scheme_id, $application_type, $next_level_role_id_verified) {
            $status_arr = $this->getStatus($data, $application_type, $next_level_role_id_verified, 0);
            $status = $status_arr['status'];
            return $status;
          })
          ->addColumn('application_id', function ($data) {

            $app_id = $data->lb_application_id;

            return $app_id;
          })->addColumn('view', function ($data) use ($scheme_id, $application_type, $next_level_role_id_verified) {
            $status_arr = $this->getStatus($data, $application_type, $next_level_role_id_verified, 0);
            if ($status_arr['can_view'] == true) {
              $action = '<a href="View60lbapplication?id=' . $data->id . '&scheme_id=' . $scheme_id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
            } else {
              $action = '';
            }
            return $action;

          })->addColumn('check', function ($data) use ($application_type, $next_level_role_id_verified) {
            $status_arr = $this->getStatus($data, $application_type, $next_level_role_id_verified, 0);
            if ($status_arr['bulk_approve'] == true) {
              return '<input type="checkbox" name="approvalcheck[]" onClick="controlCheckBox()" value="' . $data->id . '">';
            } else {
              return '';
            }
          })
          ->addColumn('id', function ($data) {
            return $data->lb_application_id;
          })
          ->addColumn('name', function ($data) {
            return $data->ben_full_name;
          })->addColumn('mobile_no', function ($data) {
            if (!empty($data->mobile_no)) {
              $ben_mobile_no = trim($data->mobile_no);
            } else {
              $ben_mobile_no = '';
            }
            return $ben_mobile_no;
          })
          ->rawColumns(['status', 'view', 'id', 'name', 'mask_mobile_no', 'check'])
          ->make(true);
      }
      //dd($district_list_obj);
      return view(
        'Lokkhibhandar60.linelisting',
        [
          'verifier_type' => $verifier_type,
          'created_by_local_body_code' => $created_by_local_body_code,
          'is_rural' => $is_rural,
          'scheme_id' => $scheme_id,
          'gps' => $gps,
          'urban_bodys' => $urban_bodys,
          'district_code' => $district_code,
          'district_list_obj' => $district_list_obj,
          'is_verifier' => $is_verifier,
          'is_approver' => $is_approver,
          'is_hod' => $is_hod,
        ]
      );
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Error');
    }
  }
  public function View60lbapplication(Request $request)
  {
    try {
      $transfer_sc = 0;
      $transfer_st = 0;
      $transfer_oap = 0;
      $fetch_lb = 0;
      $can_verify = 0;
      $can_approve = 0;
      $can_reject = 1;
      $back_to_lb = 1;
      $undo = 0;
      $encloser_list = array();
      $user_id = AuthChecker::getUserId();
      $doc_type_id_arr = array($this->supporting_dob_type_id, $this->reason_order_type_id);
      $supporting = DocumentType::select('id', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->whereIn("id", $doc_type_id_arr)->get();
      $age_supporting = $supporting->where('id', $this->supporting_dob_type_id)->first();
      $reason_order = $supporting->where('id', $this->reason_order_type_id)->first();
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

      $is_verifier = AuthChecker::VerifierChecker();
      $is_approver = AuthChecker::ApproverChecker();
      $is_hod = AuthChecker::HODChecker();
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
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
      if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
        $query = DB::table($schema . '.beneficiaries')
          ->where('created_by_dist_code', $district_code)
          ->where('id', $request->id)->where('is_lb_imported', 1);
      } else {
        $query = DB::table($schema . '.beneficiaries')
          ->where('id', $request->id)->where('is_lb_imported', 1);
      }

      $role_arr_verfied = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_verified = $role_arr_verfied->parent_id;
      // dd($next_level_role_id_verified);
      $row = $query->first();
      //dd($row->next_level_role_id);
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if ($row->next_level_role_id == 0 && !is_null($row->next_level_role_id)) {
        //dd($row->next_level_role_id);
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if (AuthChecker::VerifierChecker()) {
        if (!is_null($row->next_level_role_id)) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
      }
      if (AuthChecker::ApproverChecker()) {
      }
      $row->app_id = $row->lb_application_id;

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
            $gp_name = $gp_ward->urban_body_ward_name;
          }
        } else {
          $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
          if (!empty($gp)) {
            $gp_name = $gp->gram_panchyat_name;
          }
        }
      }
      $row->gp_name = $gp_name;
      if ($row->doc_imported == 1) {
        $doc_list_man_arr = array();
        $doc_list_opt_arr = array();
        $doc_id_list10 = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', 10)->first()->toArray();
        $doc_id_list1 = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', 1)->first()->toArray();
        $doc_id_list3 = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', 3)->first()->toArray();
        //dd($doc_id_list['doc_list_man']);
        if (isset($doc_id_list10['doc_list_man']) && $doc_id_list10['doc_list_man'] != 'null') {
          // dd($doc_id_list);
          $doc_list_man_arr10 = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->selectSub(function ($query) {
            $query->selectRaw('1');
          }, 'is_requied')->whereIn("id", json_decode($doc_id_list10['doc_list_man']))->get()->toArray();
          $doc_list_man_arr = $doc_list_man_arr10;
        }
        if (isset($doc_id_list10['doc_list_opt']) && $doc_id_list10['doc_list_opt'] != 'null') {
          $doc_list_opt_arr10 = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->selectSub(function ($query) {
            $query->selectRaw('0');
          }, 'is_requied')->whereIn("id", json_decode($doc_id_list10['doc_list_opt']))->get()->toArray();
          $doc_list_opt_arr = $doc_list_opt_arr10;
        }

        if (isset($doc_id_list1['doc_list_man']) && $doc_id_list1['doc_list_man'] != 'null') {
          // dd($doc_id_list);
          $doc_list_man1 = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->selectSub(function ($query) {
            $query->selectRaw('1');
          }, 'is_requied')->whereIn("id", json_decode($doc_id_list1['doc_list_man']))->get()->toArray();
          array_merge($doc_list_man_arr, $doc_list_man1);
        }
        if (isset($doc_id_list1['doc_list_opt']) && $doc_id_list1['doc_list_opt'] != 'null') {
          $doc_list_opt1 = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->selectSub(function ($query) {
            $query->selectRaw('0');
          }, 'is_requied')->whereIn("id", json_decode($doc_id_list1['doc_list_opt']))->get()->toArray();
          array_merge($doc_list_opt_arr, $doc_list_opt1);
        }
        if (isset($doc_id_list3['doc_list_man']) && $doc_id_list3['doc_list_man'] != 'null') {
          // dd($doc_id_list);
          $doc_list_man3 = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->selectSub(function ($query) {
            $query->selectRaw('1');
          }, 'is_requied')->whereIn("id", json_decode($doc_id_list3['doc_list_man']))->get()->toArray();
          array_merge($doc_list_man_arr, $doc_list_man3);
        }
        if (isset($doc_id_list3['doc_list_opt']) && $doc_id_list3['doc_list_opt'] != 'null') {
          $doc_list_opt3 = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->selectSub(function ($query) {
            $query->selectRaw('0');
          }, 'is_requied')->whereIn("id", json_decode($doc_id_list3['doc_list_opt']))->get()->toArray();
          array_merge($doc_list_opt_arr, $doc_list_opt3);
        }


        $c = array_merge($doc_list_man_arr10, $doc_list_man1, $doc_list_man3, $doc_list_opt_arr10, $doc_list_opt1, $doc_list_opt3);
        //dd($c);

        $already_arr = array();
        $doc_list1 = array();
        $i = 0;
        // dump($c);
        foreach ($c as $a_item) {

          if (empty($already_arr)) {
            array_push($already_arr, $a_item['id']);

            $doc_list1[$i]['id'] = $a_item['id'];
            $doc_list1[$i]['is_profile_pic'] = $a_item['is_profile_pic'];
            $doc_list1[$i]['doc_size_kb'] = $a_item['doc_size_kb'];
            $doc_list1[$i]['doc_name'] = $a_item['doc_name'];
            $doc_list1[$i]['doc_type'] = $a_item['doc_type'];
            $doc_list1[$i]['doucument_group'] = $a_item['doucument_group'];
            $i++;
          } else {
            if (!in_array($a_item['id'], $already_arr)) {
              $doc_list1[$i]['id'] = $a_item['id'];
              $doc_list1[$i]['is_profile_pic'] = $a_item['is_profile_pic'];
              $doc_list1[$i]['doc_size_kb'] = $a_item['doc_size_kb'];
              $doc_list1[$i]['doc_name'] = $a_item['doc_name'];
              $doc_list1[$i]['doc_type'] = $a_item['doc_type'];
              $doc_list1[$i]['doucument_group'] = $a_item['doucument_group'];
              $i++;
              array_push($already_arr, $a_item['id']);

            }

          }

        }
        //dd($doc_list1);
        $doc_list = $doc_list1;

        $encloser_list = array();
        $i = 0;
        $docs = BenDocs::where('scheme_id', $scheme_id)->where('created_by_dist_code', $district_code)->where('beneficiary_id', $request->id)->get();

        //$docs = DB::table($schema . '.ben_docs')->where('ben_id', $request->id)->get();
        if (count($docs) > 0) {
          $encolserdata = $docs->pluck('document_type')->toarray();
          $encolserCount = 1;
        } else {
          $encolserdata = array();
          $encolserCount = 0;
        }
        //dd(json_decode($doc_id_list10['doc_list_man']));
        $encloser_list = array();
        $i = 0;
        if (count($doc_list) > 0) {
          foreach ($doc_list as $doc_item) {
            //dump($doc_item);
            $encloser_list[$i]['ben_id'] = $request->id;
            $encloser_list[$i]['id'] = $doc_item['id'];
            $encloser_list[$i]['doc_size_kb'] = $doc_item['doc_size_kb'];
            $encloser_list[$i]['doc_name'] = $doc_item['doc_name'];
            $encloser_list[$i]['doc_type'] = $doc_item['doc_type'];

            if ($scheme_id == 10) {
              if (in_array($doc_item['id'], json_decode($doc_id_list10['doc_list_man']))) {
                $encloser_list[$i]['required'] = 1;
              } else {
                $encloser_list[$i]['required'] = 0;
              }
            } else if ($scheme_id == 1) {
              if (in_array($doc_item['id'], json_decode($doc_id_list1['doc_list_man']))) {
                $encloser_list[$i]['required'] = 1;
              } else {
                $encloser_list[$i]['required'] = 0;
              }

            } else if ($scheme_id == 3) {
              if (in_array($doc_item['id'], json_decode($doc_id_list3['doc_list_man']))) {
                $encloser_list[$i]['required'] = 1;
              } else {
                $encloser_list[$i]['required'] = 0;
              }
            }


            //dd($encolserdata);
            if ($encolserCount == 1) {
              if (in_array($doc_item['id'], $encolserdata)) {
                $encloser_list[$i]['can_download'] = 1;
              } else {
                $encloser_list[$i]['can_download'] = 0;
              }
            } else {
              $encloser_list[$i]['can_download'] = 0;
            }

            $i++;
          }
        }
      } else {
        $docs = array();
      }
      //dd($encloser_list);
      if ($scheme_id == 10) {
        $transfer_sc = 0;
        $transfer_st = 0;
        $transfer_oap = 0;
      }
      if ($scheme_id == 1) {
        if ($row->doc_imported == 1) {
          $transfer_sc = 1;
        }
        $transfer_st = 0;
        $transfer_oap = 1;
      }
      if ($scheme_id == 3) {
        $transfer_sc = 0;
        if ($row->doc_imported == 1) {
          $transfer_st = 1;
          $transfer_oap = 1;
        }
        //$transfer_oap = 0;
      }
      if (AuthChecker::VerifierChecker()) {
        if ($row->doc_imported == 1) {
          $can_verify = 1;
        }
        $can_approve = 0;
      } else if (AuthChecker::ApproverChecker()) {
        $can_verify = 0;
        // dd($row->transfer_to_scheme_id);
        $transfer_sc = 0;
        $transfer_st = 0;
        $transfer_oap = 0;
        if ($row->back_lb === 1) {
          $can_approve = 0;
          $back_to_lb = 1;
          $undo = 0;
        } else if ($row->is_transfer == 1) {
          $can_approve = 0;
          $back_to_lb = 0;
          $undo = 0;
          if ($row->transfer_to_scheme_id == 1) {
            $transfer_sc = 0;
            $transfer_st = 1;
            $transfer_oap = 0;
          } else if ($row->transfer_to_scheme_id == 3) {
            $transfer_sc = 1;
            $transfer_st = 0;
            $transfer_oap = 0;
          }
          if ($row->transfer_to_scheme_id == 10) {
            $transfer_sc = 0;
            $transfer_st = 0;
            $transfer_oap = 1;
          }
        } else if ($row->next_level_role_id === $next_level_role_id_verified) {
          $can_approve = 1;
          $back_to_lb = 0;
          $undo = 1;
        } else {
          return redirect("/")->with('danger', 'Not Allowed');
        }
      }
      if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
      } else {
        $fetch_lb = 0;
        $can_verify = 0;
        $back_to_lb = 0;
        $undo = 0;
        $can_reject = 0;
        $transfer_st = 0;
        $transfer_oap = 0;
        $can_approve = 0;
      }
      //$undo=0;
      //$back_to_lb=0;
      //dump($transfer_sc);dump($transfer_st);dump($transfer_oap);dd($transfer_sc);
      return view(
        'Lokkhibhandar60.View60lbapplication',
        [
          'scheme_id' => $scheme_id,
          'row' => $row,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'docs' => $docs,
          'reject_revert_cause_list' => $reject_revert_cause_list,
          'is_verifier' => $is_verifier,
          'is_approver' => $is_approver,
          'is_hod' => $is_hod,
          'doc_list' => $doc_list,
          'encloser_list' => $encloser_list,
          'fetch_lb' => $fetch_lb,
          'can_verify' => $can_verify,
          'back_to_lb' => $back_to_lb,
          'undo' => $undo,
          'can_reject' => $can_reject,
          'transfer_sc' => $transfer_sc,
          'transfer_st' => $transfer_st,
          'transfer_oap' => $transfer_oap,
          'can_approve' => $can_approve,
          'age_supporting' => $age_supporting,
          'reason_order' => $reason_order,
          'district_code' => $district_code
        ]
      );
    } catch (\Exception $e) {
      // dd($e);
      return redirect("/")->with('danger', 'Error');
    }
  }

  public function lbapplicationVerify(Request $request)
  {
    try {
      $user_id = AuthChecker::getUserId();
      if (!AuthChecker::VerifierChecker() || !AuthChecker::ApproverChecker()) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
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
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();

      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if ($duty_obj->is_urban == 1) {
        $created_by_local_body_code = $duty_obj->urban_body_code;
      } else {
        $created_by_local_body_code = $duty_obj->taluka_code;
      }
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_verified = $role->parent_id;
      $condition = array();
      $condition['id'] = $request->id;
      $condition['is_lb_imported'] = 1;
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
      if (AuthChecker::VerifierChecker()) {
        $query = DB::table($schema . '.beneficiaries')
          ->where($condition)->whereNull('next_level_role_id');
      }
      if (AuthChecker::ApproverChecker()) {
        $query = DB::table($schema . '.beneficiaries')
          ->where($condition);
      }

      $row = $query->first();

      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $action_type = $request->action_type;
      //dd($action_type);
      if (in_array($action_type, array(5, 70, 75, 80))) {
        if (empty(trim($row->aadhar_no))) {
          $sp_aadhar_no_old = NULL;
          //dd($request->new_aadhar_no);
          if (!empty($request->new_aadhar_no)) {
            if ($request->new_aadhar_no == $row->aadhar_no) {
              $sp_aadhar_no_new = NULL;
            } else
              $sp_aadhar_no_new = trim($request->new_aadhar_no);
          } else
            $sp_aadhar_no_new = NULL;
          //dd($sp_aadhar_no_new);
        } else {
          $sp_aadhar_no_old = trim($row->aadhar_no);
          if (!empty($request->new_aadhar_no)) {
            if ($request->new_aadhar_no == $row->aadhar_no) {
              $sp_aadhar_no_new = NULL;
              $sp_aadhar_no_old = NULL;
            } else {
              $sp_aadhar_no_old = trim($row->aadhar_no);
              $sp_aadhar_no_new = trim($request->new_aadhar_no);
            }
          } else {
            $sp_aadhar_no_new = NULL;
            $sp_aadhar_no_old = NULL;
          }
        }
        if (empty(trim($row->mobile_no))) {
          $sp_mobile_old = 0;
          if (!empty($request->new_mobile_no)) {
            if ($request->new_mobile_no == $row->mobile_no) {
              $sp_mobile_new = 0;
            } else
              $sp_mobile_new = trim($request->new_mobile_no);
          } else {
            $sp_mobile_new = 0;
          }
        } else {
          $sp_mobile_old = $row->mobile_no;
          if (!empty($request->new_mobile_no)) {
            if ($request->new_mobile_no == $row->mobile_no) {
              $sp_mobile_new = 0;
              $sp_mobile_old = 0;
            } else {
              $sp_mobile_old = trim($row->mobile_no);
              $sp_mobile_new = trim($request->new_mobile_no);
            }
          } else {
            $sp_mobile_new = 0;
            $sp_mobile_old = 0;
          }
        }
        if (empty(trim($request->ration_card_cat)) && empty(trim($request->ration_card_no))) {
          $ration_card_cat = '';
          $ration_card_no = '';
        } else {
          $ration_card_cat = trim($request->ration_card_cat);
          $ration_card_no = trim($request->ration_card_no);
        }
        if (empty(trim($request->epic_voter_id))) {
          $epic_voter_id = NULL;
        } else {
          $epic_voter_id = trim($request->epic_voter_id);
        }
        if (trim($row->caste) == 'SC' || trim($row->caste) == 'ST') {
          if (empty(trim($row->caste_certificate_no))) {
            $sp_caste_certificate_no_old = NULL;
            //dd($request->new_aadhar_no);
            if (!empty($request->caste_certificate_no)) {
              if ($request->caste_certificate_no == $row->caste_certificate_no) {
                $sp_caste_certificate_no_new = NULL;
              } else
                $sp_caste_certificate_no_new = trim($request->caste_certificate_no);
            } else
              $sp_caste_certificate_no_new = NULL;
            //dd($sp_aadhar_no_new);
          } else {
            $sp_caste_certificate_no_old = trim($row->caste_certificate_no);
            if (!empty($request->caste_certificate_no)) {
              if ($request->caste_certificate_no == $row->caste_certificate_no) {
                $sp_caste_certificate_no_new = NULL;
                $sp_caste_certificate_no_old = NULL;
              } else {
                $sp_caste_certificate_no_old = trim($row->caste_certificate_no);
                $sp_caste_certificate_no_new = trim($request->caste_certificate_no);
              }
            } else {
              $sp_caste_certificate_no_new = NULL;
              $sp_caste_certificate_no_old = NULL;
            }
          }
        } else {
          $sp_caste_certificate_no_new = NULL;
          $sp_caste_certificate_no_old = NULL;
        }
        if (!empty($row->bank_code) && !empty($row->bank_ifsc)) {
          $bank_details = BankDetails::where('ifsc', trim($row->bank_ifsc))->where('is_active', 1)->get(['bank', 'branch', 'bank_code'])->first();
          if ($bank_details == NULL) {
            $return_msg = 'IFSC Not Found';
            return redirect("/")->with('danger', 'IFSC Not Found');
          }
          $npci_bank_update = DB::table($schema . '.beneficiaries')->where('id', $request->id)->update(['npci_bank_code' => $bank_details->bank_code]);
        } else {
          $npci_bank_update = 1;
        }
      } else {
        $sp_caste_certificate_no_new = NULL;
        $sp_caste_certificate_no_old = NULL;
      }
      $c_time = date('Y-m-d H:i:s', time());
      $action_msg = $request->action_msg;
      //dd($action_msg);
      if ($action_type == 1) {
        //For Doc Import
        if (AuthChecker::VerifierChecker()) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
      } else if ($action_type == 5) {

        // dd('ok');
        //For Import & Verify
        if (!AuthChecker::VerifierChecker()) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        //dd('ok');
        //dump($sp_aadhar_no_old);dump($sp_aadhar_no_new);dump($sp_mobile_old);dd($sp_caste_certificate_no_new);  
        $isValidarr = $this->validateInput($request, $scheme_id, $row, $action_type, $schema, NULL, $sp_aadhar_no_new, $sp_mobile_new, $sp_caste_certificate_no_new, NULL, NULL, NULL, NULL, NULL);
        //dd($isValidarr);
        if ($isValidarr['is_valid'] == false) {
          return back()->with('errors', $isValidarr['errors'])->withInput(Input::all());
        }
        $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
        $next_level_role_id_verified = $role->parent_id;
        // dd($next_level_role_id_verified );
        DB::beginTransaction();
        //dump($sp_aadhar_no_old);dump($sp_aadhar_no_new);dump($sp_mobile_old);dd($sp_mobile_new);        
        //dd($sp_aadhar_no_new);
        $import_from_lb_arr = DB::select("select " . $schema . ".import_from_lb(
          in_op_type => 'LV',
          in_scheme_id =>" . $scheme_id . ",
          in_verified_next_level_role_id => " . $next_level_role_id_verified . ",
          in_c_time => '" . $c_time . "',
          in_user_id => " . $user_id . ",
          in_ben_id =>" . $request->id . ",
          in_mobile_no =>" . $sp_mobile_new . ",
          in_aadhar_no => '" . $sp_aadhar_no_new . "',
          in_ration_card_cat => '" . $ration_card_cat . "',
          in_ration_card_no => '" . $ration_card_no . "',
          in_epic_voter_id => '" . $epic_voter_id . "',
          in_caste_certificate_no => '" . $sp_caste_certificate_no_new . "')");
        $import_from_lb_status = $import_from_lb_arr[0]->import_from_lb;
        // dd($import_from_lb_status);
        try {
          if ($import_from_lb_status && $npci_bank_update) {
            DB::commit();
            return redirect("workflow-lb60?scheme_id=" . $scheme_id)->with('success', $action_msg)
              ->with('lb_id', $row->lb_application_id);
          } else {
            DB::rollback();
            $return_text = 'Error..Please try after sometimeshhh';
            $return_msg = array("" . $return_text);
            return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
          }
        } catch (\Exception $e) {
          //dd($e);
          return redirect("/")->with('danger', 'Error');
        }

        if (!empty($return_text)) {
          return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
        }
      } else if ($action_type == 7) {

        //Back to LB

        if (AuthChecker::VerifierChecker()) {
          $mydate = date('Y-m-d');
          $max_date = strtotime("-25 year", strtotime($mydate));
          $max_date = date("Y-m-d", $max_date);
          $min_date = strtotime("-60 year", strtotime($mydate));
          $min_date = date("Y-m-d", $min_date);
          $rules = [
            'dob' => 'required|date|before_or_equal:' . $max_date . '|after_or_equal:' . $min_date
          ];
          $attributes = array();
          $messages = array();
          $attributes['dob'] = 'Date of Birth';
          $doc_age_dob = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->where("id", $this->supporting_dob_type_id)->first();
          $image_file = $request->file('doc_' . $doc_age_dob->id);
          //dd($image_file);
          $messages = array();
          $rules['doc_' . $doc_age_dob->id] = 'required|mimes:' . $doc_age_dob->doc_type . '|max:' . $doc_age_dob->doc_size_kb . ',';
          $messages['doc_' . $doc_age_dob['id'] . '.max'] = "The file uploaded for " . $doc_age_dob->doc_name . " size must be less than " . $doc_age_dob->doc_size_kb . " KB";
          $messages['doc_' . $doc_age_dob['id'] . '.mimes'] = "The file uploaded for " . $doc_age_dob->doc_name . " must be of type " . $doc_age_dob->doc_type;
          $messages['doc_' . $doc_age_dob['id'] . '.required'] = "Document for " . $doc_age_dob->doc_name . " must be uploaded";
          $validator = Validator::make($request->all(), $rules, $messages, $attributes);
          if (!$validator->passes()) {
            //dd('ok');
            $error_msg = array();
            foreach ($validator->errors()->all() as $error) {
              array_push($error_msg, $error);
            }

            return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $error_msg);
          }
          try {
            $doc_master = DocumentType::get();
            $image_file = $request->file('doc_' . $doc_age_dob->id);
            $img_data = file_get_contents($image_file);
            $extension = $image_file->getClientOriginalExtension();
            $mime_type = $image_file->getMimeType();
            $base64 = base64_encode($img_data);
            $lb_doc_insert = array();
            $lb_doc_insert['application_id'] = $row->lb_application_id;
            $lb_doc_insert['document_type'] = $doc_age_dob->id;
            $lb_doc_insert['attched_document'] = $base64;
            $lb_doc_insert['document_extension'] = $extension;
            $lb_doc_insert['document_mime_type'] = $mime_type;
            $lb_doc_insert['created_by_level'] = $duty_obj->mapping_level;
            $lb_doc_insert['created_by'] = Auth::user()->id;
            $lb_doc_insert['ip_address'] = $request->ip();
            $lb_doc_insert['created_by_dist_code'] = $duty_obj->district_code;
            if ($scheme_id == 1) {
              $file_path = 'keep_st';
            } else if ($scheme_id == 3) {
              $file_path = 'keep_sc';
            } else if ($scheme_id == 10) {
              $file_path = 'keep_oap';
            }
            $base_url = url('/');


            $serverip = Config::get('constants.lb60server');
            // dd($serverip);
            if (empty($row->is_faulty)) {
              $is_faulty = 0;
              //$doc_attach = 'ben_attach_documents';
              // $doc_profile = 'ben_profile_image';
            } else {
              $is_faulty = $row->is_faulty;
              //$doc_attach = 'faulty_ben_attach_documents';
              //$doc_profile = 'faulty_ben_profile_image';
            }
            $post_url = $serverip . '/api/jbtempdobupdate';
            $curl = curl_init($post_url);
            $headers = array(
              'Content-Type: application/json'
            );

            $data = array("application_id" => $row->lb_application_id, "is_faulty" => $is_faulty, "dob" => $request->dob);
            $data_string = json_encode($data);

            $scheme_id = $row->scheme_id;
            header("Access-Control-Allow-Origin: *");
            curl_setopt($curl, CURLOPT_URL, $post_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $post_response = curl_exec($curl);
            //dd($post_response);
            if ($post_response) {
              $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
              curl_close($curl);
              if ($httpcode == 200) {
                $post_response_lb = json_decode($post_response);
                $is_success = $post_response_lb->is_success;
                if ($is_success) {
                  //dd('ok');
                  DB::beginTransaction();
                  DB::connection('pgsql_lb_encwrite')->beginTransaction();
                  DB::connection('pgsql_encwrite')->beginTransaction();
                  $lb_db_upload = DB::connection('pgsql_lb_encwrite')->table('lb_scheme.ben_attach_documents')->insert($lb_doc_insert);
                  //dd($lb_db_upload);
                  if ($lb_db_upload) {
                    $doc_type_id = $doc_age_dob->id;
                    if ($request->hasFile('doc_' . $doc_age_dob->id)) {
                      $doc_type_name = $doc_master->where('id', $doc_age_dob->id)->first();
                      $fun_call = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                        in_beneficiary_id => " . $request->id . ",
                        in_scheme_id => " . $scheme_id . ",
                        in_document_type => " . $doc_type_id . ",
                        in_attched_document => '" . $base64 . "',
                        in_created_by_level => '" . $duty_obj->mapping_level . "',
                        in_created_by => " . $user_id . ",
                        in_ip_address => '" . $request->ip() . "',
                        in_document_extension => '" . $extension . "',
                        in_document_mime_type => '" . $mime_type . "',
                        in_created_by_dist_code => " . $duty_obj->district_code . ",
                        in_created_by_local_body_code => " . $created_by_local_body_code . ",
                        in_doc_type_name => '" . $doc_type_name->doc_name . "',
                        in_datetime => '" . $c_time . "'
                        );"
                      );
                      $doc_is_insert = $fun_call[0]->ben_docs_insert_archive;
                      //dd($doc_is_insert);
                      if ($doc_is_insert) {
                        $input = [
                          'back_lb' => 1,
                          'lb_dob' => $request->dob
                        ];
                        $back_lb_status = DB::table($schema . '.beneficiaries')->where('id', $request->id)->whereNull('is_transfer')->whereNull('back_lb')->update($input);
                        $accept_reject_model = new AcceptRejectInfo;
                        $accept_reject_model->created_at = $c_time;
                        $accept_reject_model->application_id = $request->id;
                        $accept_reject_model->scheme_id = $scheme_id;
                        $accept_reject_model->user_id = $user_id;
                        $accept_reject_model->ip_address = request()->ip();
                        $accept_reject_model->op_type = 'BACKLBV';
                        $accept_reject_model->action_by = $user_id;
                        $accept_reject_model->action_ip_address = $request->ip();
                        $accept_reject_model->action_type = $request->class_basename(request()->route()->getAction()['controller']) . '@' . 'BACKLBV';
                        $is_saved_log = $accept_reject_model->save();
                        if ($back_lb_status && $is_saved_log) {
                          DB::commit();
                          DB::connection('pgsql_lb_encwrite')->commit();
                          DB::connection('pgsql_encwrite')->commit();
                          return redirect('workflow-lb60?scheme_id=' . $scheme_id)->with('success', $action_msg)->with('lb_id', $row->lb_application_id);
                        } else {
                          DB::rollback();
                          DB::connection('pgsql_encwrite')->rollback();
                          DB::connection('pgsql_lb_encwrite')->rollback();
                          return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('error', 'Error! Please try again.');
                        }
                      } else {
                        DB::rollback();
                        DB::connection('pgsql_encwrite')->rollback();
                        DB::connection('pgsql_lb_encwrite')->rollback();
                        return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('error', 'Error! Please try again.');
                      }


                    } else {
                      $return_status = 0;
                      $return_text = 'Unable to Upload File..';
                      // return redirect("/")->with('error',  $return_text);
                      return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('error', 'Error! Please try again.');

                    }
                  } else {
                    $return_msg = array('Please try again.');
                    //dd( $return_msg);
                    return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
                  }
                } else {
                  $return_msg = array('Please try again.');
                  //dd( $return_msg);
                  return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
                }
              } else {
                $return_msg = array('Please try again.');
                //dd( $return_msg);
                return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
              }
            } else {
              $return_msg = array('Please try again.');
              //dd( $return_msg);
              return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
            }



          } catch (\Exception $e) {
            // dd($e);
            return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('error', 'Error! Please try again.');
          }
        } else if (AuthChecker::ApproverChecker()) {
          // dd('ok');
          try {
            $serverip = Config::get('constants.lb60server');
            // dd($serverip);
            if (empty($row->is_faulty)) {
              $is_faulty = 0;
              //$doc_attach = 'ben_attach_documents';
              // $doc_profile = 'ben_profile_image';
            } else {
              $is_faulty = $row->is_faulty;
              //$doc_attach = 'faulty_ben_attach_documents';
              //$doc_profile = 'faulty_ben_profile_image';
            }
            $post_url = $serverip . '/api/jbmarkwrongdob';
            $curl = curl_init($post_url);
            $headers = array(
              'Content-Type: application/json'
            );

            $data = array("application_id" => $row->lb_application_id, "is_faulty" => $is_faulty);
            $data_string = json_encode($data);

            $scheme_id = $row->scheme_id;
            header("Access-Control-Allow-Origin: *");
            curl_setopt($curl, CURLOPT_URL, $post_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $post_response = curl_exec($curl);
            //dd($post_response);
            if ($post_response) {
              $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
              curl_close($curl);
              if ($httpcode == 200) {
                $post_response_lb = json_decode($post_response);
                $is_success = $post_response_lb->is_success;
                if ($is_success) {
                  DB::beginTransaction();
                  $input = [
                    'back_lb_finalize' => 1,
                    'next_level_role_id' => -36,
                    'is_rejected' => 1
                  ];
                  $lb_update = DB::table($schema . '.beneficiaries')->where('id', $request->id)->update($input);
                  $accept_reject_model = new AcceptRejectInfo;
                  $accept_reject_model->created_at = $c_time;
                  $accept_reject_model->application_id = $request->id;
                  $accept_reject_model->scheme_id = $scheme_id;
                  $accept_reject_model->user_id = $user_id;
                  $accept_reject_model->ip_address = request()->ip();
                  $accept_reject_model->op_type = 'BACKLBA';
                  $accept_reject_model->action_by = $user_id;
                  $accept_reject_model->action_ip_address = $request->ip();
                  $accept_reject_model->action_type = $request->class_basename(request()->route()->getAction()['controller']) . '@' . 'BACKLBA';
                  $is_saved_log = $accept_reject_model->save();
                  if ($lb_update && $is_saved_log) {
                    DB::commit();
                    return redirect('workflow-lb60?scheme_id=' . $scheme_id)->with('message', 'Application with LB Id ' . $row->lb_application_id . ' has been send to LB for DOB modification!');
                  } else {
                    DB::rollback();
                    return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('error', 'Error! Please try again.');
                  }
                }
              } else {
                $return_msg = array('Please try again.');
                //dd( $return_msg);
                return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
              }
            } else {
              $return_msg = array('Please try again.');
              //dd( $return_msg);
              return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
            }
          } catch (\Exception $e) {
            //dd($e);
            // $docs = array();
            $return_msg = array('Error.. Please try agian.');
            return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
          }
        }
      } else if ($action_type == 50) {

        // dd($designation_id_old);
        //For Approve
        if (!AuthChecker::ApproverChecker()) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        $in_pension_id = 'ARRAY[' . "'$request->id'" . ']';
        $back_url = 'View60lbapplication?id=' . $request->id . '&scheme_id=' . $scheme_id;
        //dd($next_level_role_id_verified = $role->parent_id);
        try {
          DB::beginTransaction();
          $is_inserted_status_arr = DB::select("select " . $schema . ".approve_data_bulk_lb(in_verified_next_level_role_id => " . $next_level_role_id_verified . ",in_ben_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'LA')");
          //dd($is_inserted_status_arr);
          $is_inserted_status = $is_inserted_status_arr[0]->approve_data_bulk_lb;
          //dd($is_inserted_status);
          if ($is_inserted_status == 1) {

            DB::commit();
            return redirect('workflow-lb60?scheme_id=' . $scheme_id)->with('message', 'Application with LB Id ' . $row->lb_application_id . ' has been Approved Succesfully!');
          } else {
            DB::rollback();
            return redirect($back_url)->with('error', 'Error! Please try again.');
          }
        } catch (\Exception $e) {
          //dd($e);
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
      } else if ($action_type == 70) {

        //Transfer to Johar
        if (AuthChecker::VerifierChecker()) {
          $isValidarr = $this->validateInput($request, $scheme_id, $row, $action_type, $schema, 'johar', $request->new_aadhar_no, $request->new_mobile_no, $request->caste_certificate_no, trim($row->bank_code), trim($row->bank_ifsc), $row->aadhar_no, $row->mobile_no, $row->caste_certificate_no);
          if ($isValidarr['is_valid'] == false) {
            return back()->with('errors', $isValidarr['errors'])->withInput(Input::all());
          }
          if ($isValidarr['is_valid'] == true) {
            try {

              //dd($request->caste_certificate_no);
              DB::beginTransaction();
              // dump($sp_mobile_new); dump($sp_aadhar_no_new);dump($ration_card_cat);dump($ration_card_no);dd($sp_caste_certificate_no_new);
              $input = [
                'is_transfer' => 1,
                'transfer_from_scheme_id' => $scheme_id,
                'transfer_to_scheme_id' => 1,
                'lb_transfer_mobile_no' => $request->new_mobile_no,
                'lb_transfer_aadhar_no' => trim($request->new_aadhar_no),
                'lb_transfer_ration_card_cat' => trim($request->ration_card_cat),
                'lb_transfer_ration_card_no' => trim($request->ration_card_no),
                'lb_transfer_epic_voter_id' => trim($request->epic_voter_id),
                'lb_transfer_caste_certificate_no' => trim($request->caste_certificate_no)
              ];
              $transfer_to_status = DB::table($schema . '.beneficiaires')->where('id', $request->id)->whereNull('back_lb')->whereNull('is_transfer')->update($input);
              $accept_reject_model = new AcceptRejectInfo;
              $accept_reject_model->created_at = $c_time;
              $accept_reject_model->application_id = $request->id;
              $accept_reject_model->scheme_id = $scheme_id;
              $accept_reject_model->user_id = $user_id;
              $accept_reject_model->ip_address = request()->ip();
              $accept_reject_model->op_type = 'TJOHARV';
              $accept_reject_model->action_by = $user_id;
              $accept_reject_model->action_ip_address = $request->ip();
              $accept_reject_model->action_type = $request->class_basename(request()->route()->getAction()['controller']) . '@' . 'TJOHARV';
              $is_saved_log = $accept_reject_model->save();
              //dd($transfer_to_status);
              if ($transfer_to_status == 1 && $is_saved_log) {
                //dd($action_msg);
                DB::commit();
                return redirect("workflow-lb60?scheme_id=" . $scheme_id)->with('success', $action_msg)->with('lb_id', $row->lb_application_id);
              } else {
                DB::rollback();
                $return_text = 'Error.. Please try again';
                $return_msg = array("" . $return_text);
                return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
              }

            } catch (\Exception $e) {
              //dd($e);
              $return_text = 'Error.. Please try again';
              $return_msg = array("" . $return_text);
              return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
            }
          }
        }
        if (AuthChecker::ApproverChecker()) {
          DB::beginTransaction();
          $transfer_to_arr = DB::select("select pension.transfer_from_bandhu_to_johar_lb(
          in_op_type => 'TJOHARAV',
          in_scheme_id =>" . $scheme_id . ",
          in_transfer_to_scheme_id =>1,
          in_c_time => '" . $c_time . "',
          in_user_id => " . $user_id . ",
          in_ben_id =>" . $request->id . ")");
          $is_inserted_status = $transfer_to_arr[0]->transfer_from_bandhu_to_johar_lb;
          $update_arr = array();
          $update_arr['beneficiary_id'] = $is_inserted_status;
          $update_arr['scheme_id'] = 1;
          $doc_updated_status = BenDocs::where('scheme_id', $scheme_id)->where('created_by_dist_code', $district_code)->where('beneficiary_id', $request->id)->update($update_arr);
          //dd($is_inserted_status);
          if ($is_inserted_status && $doc_updated_status) {
            // $action_msg="";
            DB::commit();
            return redirect("workflow-lb60?scheme_id=" . $scheme_id)->with('success', $action_msg)->with('lb_id', $row->lb_application_id);
          } else {
            DB::rollback();
            $return_text = 'Error.. Please try again';
            $return_msg = array("" . $return_text);
            return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
          }
        }

      } else if ($action_type == 75) {

        //Transfer to Bandhu
        if (AuthChecker::VerifierChecker()) {
          $isValidarr = $this->validateInput($request, $scheme_id, $row, $action_type, $schema, 'bandhu', $request->new_aadhar_no, $request->new_mobile_no, $request->caste_certificate_no, trim($row->bank_code), trim($row->bank_ifsc), $row->aadhar_no, $row->mobile_no, $row->caste_certificate_no);
          if ($isValidarr['is_valid'] == false) {
            return back()->with('errors', $isValidarr['errors'])->withInput(Input::all());
          }
          if ($isValidarr['is_valid'] == true) {
            try {

              DB::beginTransaction();
              // dump($sp_mobile_new); dump($sp_aadhar_no_new);dump($ration_card_cat);dump($ration_card_no);dd($sp_caste_certificate_no_new);

              $input = [
                'is_transfer' => 1,
                'transfer_from_scheme_id' => $scheme_id,
                'transfer_to_scheme_id' => 3,
                'lb_transfer_mobile_no' => $request->new_mobile_no,
                'lb_transfer_aadhar_no' => trim($request->new_aadhar_no),
                'lb_transfer_ration_card_cat' => trim($request->ration_card_cat),
                'lb_transfer_ration_card_no' => trim($request->ration_card_no),
                'lb_transfer_epic_voter_id' => trim($request->epic_voter_id),
                'lb_transfer_caste_certificate_no' => trim($request->caste_certificate_no)
              ];

              $transfer_to_status = DB::table($schema . '.beneficiaires')->where('id', $request->id)->whereNull('is_transfer')->update($input);
              // dump($transfer_to_status);
              if ($transfer_to_status == 1) {
                DB::commit();
                return redirect("workflow-lb60?scheme_id=" . $scheme_id)->with('success', 'Application has been Transfer to Bandhu Succesfully')->with('lb_id', $row->lb_application_id);
              } else {
                DB::rollback();
                $return_text = 'Error.. Please try again';
                $return_msg = array("" . $return_text);
                return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
              }

            } catch (\Exception $e) {
              $return_text = 'Error.. Please try again';
              $return_msg = array("" . $return_text);
              return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
            }
          }
        }
        if (AuthChecker::ApproverChecker()) {
          try {
            DB::beginTransaction();
            DB::connection('pgsql_encwrite')->beginTransaction();
            $transfer_to_arr = DB::select("select pension.transfer_from_johar_to_bandhu_lb(
            in_op_type => 'TBANDHUV',
            in_scheme_id =>" . $scheme_id . ",
            in_transfer_to_scheme_id =>1,
            in_c_time => '" . $c_time . "',
            in_user_id => " . $user_id . ",
            in_ben_id =>" . $request->id . ")");
            $is_inserted_status = $transfer_to_arr[0]->transfer_from_johar_to_bandhu_lb;
            if ($request->id == 12506831) {
              // dd($is_inserted_status);
            }

            $update_arr = array();
            $update_arr['beneficiary_id'] = $is_inserted_status;
            $update_arr['scheme_id'] = 3;
            $doc_updated_status = BenDocs::where('scheme_id', $scheme_id)->where('created_by_dist_code', $district_code)->where('beneficiary_id', $request->id)->update($update_arr);
            if ($is_inserted_status && $doc_updated_status) {
              // $action_msg="";
              DB::commit();
              DB::connection('pgsql_encwrite')->commit();
              return redirect("workflow-lb60?scheme_id=" . $scheme_id)->with('success', $action_msg)->with('lb_id', $row->lb_application_id);
            } else {
              DB::rollback();
              DB::connection('pgsql_encwrite')->rollback();
              $return_text = 'Error.. Please try again';
              $return_msg = array("" . $return_text);
              return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
            }
          } catch (\Exception $e) {
            //dd($e);
            if ($request->id == 12506831) {
              dd($e);
            }
            $return_text = 'Error.. Please try again';
            $return_msg = array("" . $return_text);
            return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
          }

        }
      } else if ($action_type == 80) {

        //Transfer to OAP
        if (AuthChecker::VerifierChecker()) {
          $isValidarr = $this->validateInput($request, $scheme_id, $row, $action_type, $schema, 'oap_wcd', $request->new_aadhar_no, $request->new_mobile_no, NULL, trim($row->bank_code), trim($row->bank_ifsc), $row->aadhar_no, $row->mobile_no, NULL);
          if ($isValidarr['is_valid'] == false) {
            return back()->with('errors', $isValidarr['errors'])->withInput(Input::all());
          }
          //dd($isValidarr['is_valid']);
          if ($isValidarr['is_valid'] == true) {
            try {
              //dd($request);
              $rules = [];
              $attributes = array();
              $messages = array();
              $doc_type_id_arr = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->where("id", $this->reason_order_type_id)->first();
              // dd($doc_type_id_arr->id);
              $image_file = $request->file('doc_' . $doc_type_id_arr->id);
              // dd($image_file);
              $messages = array();
              $rules['doc_' . $doc_type_id_arr->id] = 'required|mimes:' . $doc_type_id_arr->doc_type . '|max:' . $doc_type_id_arr->doc_size_kb . ',';
              $messages['doc_' . $doc_type_id_arr['id'] . '.max'] = "The file uploaded for " . $doc_type_id_arr->doc_name . " size must be less than " . $doc_type_id_arr->doc_size_kb . " KB";
              $messages['doc_' . $doc_type_id_arr['id'] . '.mimes'] = "The file uploaded for " . $doc_type_id_arr->doc_name . " must be of type " . $doc_type_id_arr->doc_type;
              $messages['doc_' . $doc_type_id_arr['id'] . '.required'] = "Document for " . $doc_type_id_arr->doc_name . " must be uploaded";
              $validator = Validator::make($request->all(), $rules, $messages, $attributes);
              //dd($validator);
              if (!$validator->passes()) {
                //dd('ok');
                $error_msg = array();
                foreach ($validator->errors()->all() as $error) {
                  array_push($error_msg, $error);
                }

                return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $error_msg);
              }

              $base_url = url('/');
              $file_path = 'keep_oap';
              $image_file = $request->file('doc_' . $doc_type_id_arr->id);
              $img_data = file_get_contents($image_file);
              $extension = $image_file->getClientOriginalExtension();
              $mime_type = $image_file->getMimeType();
              $base64 = base64_encode($img_data);
              $lb_doc_insert = array();
              $lb_doc_insert['application_id'] = $row->lb_application_id;
              $lb_doc_insert['document_type'] = $doc_type_id_arr->id;
              $lb_doc_insert['attched_document'] = $base64;
              $lb_doc_insert['document_extension'] = $extension;
              $lb_doc_insert['document_mime_type'] = $mime_type;
              $lb_doc_insert['created_by_level'] = $duty_obj->mapping_level;
              $lb_doc_insert['created_by'] = Auth::user()->id;
              $lb_doc_insert['ip_address'] = $request->ip();
              $lb_doc_insert['created_by_dist_code'] = $duty_obj->district_code;
              $doc_master = DocumentType::get();

              DB::beginTransaction();
              DB::connection('pgsql_lb_encwrite')->beginTransaction();
              $lb_db_upload = DB::connection('pgsql_lb_encwrite')->table('lb_scheme.ben_attach_documents')->insert($lb_doc_insert);


              $fun_call = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                  in_beneficiary_id => " . $request->id . ",
                  in_scheme_id => " . $scheme_id . ",
                  in_document_type => " . $doc_type_id_arr->id . ",
                  in_attched_document => '" . $base64 . "',
                  in_created_by_level => '" . $duty_obj->mapping_level . "',
                  in_created_by => " . $user_id . ",
                  in_ip_address => '" . $request->ip() . "',
                  in_document_extension => '" . $extension . "',
                  in_document_mime_type => '" . $mime_type . "',
                  in_created_by_dist_code => " . $duty_obj->district_code . ",
                  in_created_by_local_body_code => " . $created_by_local_body_code . ",
                  in_doc_type_name => '" . $doc_type_id_arr->doc_name . "',
                  in_datetime => '" . $c_time . "'
                  );"
              );
              $doc_is_insert = $fun_call[0]->ben_docs_insert_archive;

              $input = [
                'is_transfer' => 1,
                'transfer_to_scheme_id' => 10,
                'lb_transfer_mobile_no' => $request->new_mobile_no,
                'lb_transfer_aadhar_no' => trim($request->new_aadhar_no),
                'lb_transfer_ration_card_cat' => trim($request->ration_card_cat),
                'lb_transfer_ration_card_no' => trim($request->ration_card_no),
                'lb_transfer_epic_voter_id' => trim($request->epic_voter_id)

              ];
              $transfer_to_status = DB::table($schema . '.beneficiaries')->where('id', $request->id)->whereNull('back_lb')->whereNull('is_transfer')->update($input);
              $accept_reject_model = new AcceptRejectInfo;
              $accept_reject_model->created_at = $c_time;
              $accept_reject_model->application_id = $request->id;
              $accept_reject_model->scheme_id = $scheme_id;
              $accept_reject_model->user_id = $user_id;
              $accept_reject_model->ip_address = request()->ip();
              $accept_reject_model->op_type = 'TOAPV';
              $accept_reject_model->action_by = $user_id;
              $accept_reject_model->action_ip_address = $request->ip();
              $accept_reject_model->action_type = $request->class_basename(request()->route()->getAction()['controller']) . '@' . 'TOAPV';
              $is_saved_log = $accept_reject_model->save();
              //dd($transfer_to_status);
              if ($transfer_to_status == 1 && $is_saved_log) {
                DB::commit();
                DB::connection('pgsql_lb_encwrite')->commit();
                return redirect("workflow-lb60?scheme_id=" . $scheme_id)->with('success', $action_msg)->with('lb_id', $row->lb_application_id);
              } else {
                DB::rollback();
                DB::connection('pgsql_lb_encwrite')->rollback();
                $return_text = 'Error.. Please try again';
                $return_msg = array("" . $return_text);
                return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
              }



            } catch (\Exception $e) {
              //dd($e);
              $return_text = 'Error.. Please try again';
              $return_msg = array("" . $return_text);
              return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
            }
          }
        } else if (AuthChecker::ApproverChecker()) {
          DB::beginTransaction();
          DB::connection('pgsql_encwrite')->beginTransaction();
          if ($scheme_id == 1) {
            $transfer_to_arr = DB::select("select pension.transfer_from_johar_to_oap_lb(
              in_op_type => 'TOAPA',
              in_scheme_id =>" . $scheme_id . ",
              in_transfer_to_scheme_id =>10,
              in_c_time => '" . $c_time . "',
              in_user_id => " . $user_id . ",
              in_ben_id =>" . $request->id . ")");
            $transfer_status = $transfer_to_arr[0]->transfer_from_johar_to_oap_lb;
            $update_arr = array();
            $update_arr['beneficiary_id'] = $transfer_status;
            $update_arr['scheme_id'] = 10;
            $doc_updated_status = BenDocs::where('scheme_id', $scheme_id)->where('created_by_dist_code', $district_code)->where('beneficiary_id', $request->id)->update($update_arr);
          } else if ($scheme_id == 3) {

            $transfer_to_arr = DB::select("select pension.transfer_from_bandhu_to_oap_lb(
              in_op_type => 'TOAPA',
              in_scheme_id =>" . $scheme_id . ",
              in_transfer_to_scheme_id =>10,
              in_c_time => '" . $c_time . "',
              in_user_id => " . $user_id . ",
              in_ben_id =>" . $request->id . ")");
            $transfer_status = $transfer_to_arr[0]->transfer_from_bandhu_to_oap_lb;
            $update_arr = array();
            $update_arr['beneficiary_id'] = $transfer_status;
            $update_arr['scheme_id'] = 10;
            $doc_updated_status = BenDocs::where('scheme_id', $scheme_id)->where('created_by_dist_code', $district_code)->where('beneficiary_id', $request->id)->update($update_arr);
          }
          // dd($import_from_lb_status);
          if ($transfer_status && $doc_updated_status) {
            DB::commit();
            DB::connection('pgsql_encwrite')->commit();
            $logmessage = "Application has been transfer to OAP";
            return redirect("workflow-lb60?scheme_id=" . $scheme_id)->with('success', $logmessage)
              ->with('lb_id', $row->lb_application_id);
          } else {
            DB::rollback();
            DB::connection('pgsql_encwrite')->rollback();
            $return_text = 'Error..Please try after sometimes';
            $return_msg = array("" . $return_text);
            return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
          }
        }
      } else if ($action_type == -100) {
        $back_url = 'View60lbapplication?id=' . $request->id . '&scheme_id=' . $scheme_id;
        $c_time = date('Y-m-d H:i:s', time());
        try {
          DB::beginTransaction();
          $accept_reject_model = new AcceptRejectInfo;
          $accept_reject_model->created_at = $c_time;
          $accept_reject_model->application_id = $request->id;
          $accept_reject_model->scheme_id = $scheme_id;
          $accept_reject_model->user_id = $user_id;

          $accept_reject_model->ip_address = request()->ip();
          //dd($designation_id_old);
          if (AuthChecker::ApproverChecker()) {
            $accept_reject_model->op_type = 'LRA';
            $reject_dup_adjustment = 1;
          } else {
            $accept_reject_model->op_type = 'LRV';
            $reject_dup_adjustment = 1;
          }

          $accept_reject_model->action_by = $user_id;
          $accept_reject_model->action_ip_address = $request->ip();
          $accept_reject_model->action_type = $request->class_basename(request()->route()->getAction()['controller']);
          $is_saved_log = $accept_reject_model->save();
          $input = [
            'next_level_role_id' => -3,
            'is_rejected' => 1,
            'is_approved' => 2,
            'is_verified' => 2,
            'rejected_date' => $c_time,
            'rejected_by' => $user_id,
            'is_clean' => 10
          ];
          $lb_update = DB::table($schema . '.beneficiaries')->where($condition)->update($input);
          if ($is_saved_log && $lb_update) {

            DB::commit();
            return redirect('workflow-lb60?scheme_id=' . $scheme_id)->with('message', 'Application with LB Id ' . $request->id . ' has been Rejected Succesfully!');
          } else {
            DB::rollback();
            return redirect($back_url)->with('error', 'Error! Please try again.');
          }
        } catch (\Exception $e) {
          //dd($e);
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }

      } else if ($action_type == 85) {
        // dd($designation_id_old);
        //For Approve
        if (AuthChecker::ApproverChecker()) {
          return redirect("/")->with('danger', 'Not Allowed');
        }

        $back_url = 'View60lbapplication?id=' . $request->id . '&scheme_id=' . $scheme_id;
        //dd($next_level_role_id_verified = $role->parent_id);
        try {
          DB::beginTransaction();
          $input = [
            'next_level_role_id' => NULL,
            'is_reverted' => 1,
            'is_approved' => 0,
            'is_rejected' => 0,
            'is_verified' => 0
          ];
          $revert_status = DB::table($schema . '.beneficiaires')->where('id', $request->id)->where('created_by_dist_code', $district_code)->update($input);
          $accept_reject_model = new AcceptRejectInfo;
          $accept_reject_model->created_at = $c_time;
          $accept_reject_model->application_id = $request->id;
          $accept_reject_model->scheme_id = $scheme_id;
          $accept_reject_model->user_id = $user_id;
          $accept_reject_model->op_type = 'REVERTLB';
          $accept_reject_model->ip_address = request()->ip();
          $accept_reject_model->created_by_dist_code = $district_code;
          $accept_reject_model->action_by = $user_id;
          $accept_reject_model->action_ip_address = $request->ip();
          $accept_reject_model->action_type = $request->class_basename(request()->route()->getAction()['controller']) . '@' . 'REVERTLB';
          $is_saved_log = $accept_reject_model->save();

          //dd($is_inserted_status);
          if ($is_saved_log && $revert_status) {

            DB::commit();
            return redirect('workflow-lb60?scheme_id=' . $scheme_id)->with('message', 'Application with LB Id ' . $row->lb_application_id . ' has been Reverted Succesfully!');
          } else {
            DB::rollback();
            return redirect($back_url)->with('error', 'Error! Please try again.');
          }
        } catch (\Exception $e) {
          // dd($e);
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
      } else {
        return redirect("/")->with('danger', 'Not Allowed');
      }
    } catch (\Exception $e) {
      //dd($e);
      return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('error', 'Error! Please try again.');
    }
  }
  public function encloserEntry(Request $request)
  {
    try {
      $c_time = date('Y-m-d H:i:s');
      $scheme_id = $request->scheme_id;
      $ben_id = $request->ben_id;
      // dd($ben_id);
      $doc_type_id = $request->document_type;
      if (empty($ben_id)) {
        $return_status = 0;
        $return_text = 'Beneficiary Id Parameter Not Valid';
        //return redirect("/")->with('error',  $return_text);
      }


      if (empty($scheme_id)) {
        $return_status = 0;
        $return_text = 'Scheme Parameter Not Valid';
        //return redirect("/")->with('error',  $return_text);
      }
      if (!ctype_digit($scheme_id)) {
        $return_status = 0;
        $return_text = 'Scheme Id Not Valid';
        //return redirect("/")->with('error',  $return_text);
      }
      if (empty($doc_type_id)) {
        $return_status = 0;
        $return_text = 'doc_type_id Parameter Not Valid';
        //return redirect("/")->with('error',  $return_text);
      }
      if (!ctype_digit($doc_type_id)) {
        $return_status = 0;
        $return_text = 'Document type Not Valid';
        //return redirect("/")->with('error',  $return_text);
      }
      $user_id = AuthChecker::getUserId();
      // $designation_id_old = Auth::user()->designation_id_old;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        $return_status = 0;
        $return_text = 'Scheme Not Valid';
        //return redirect("/")->with('error',  $return_text);
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        $return_status = 0;
        $return_text = 'Not Allowed';
        //return redirect("/")->with('error',  $return_text);
      }
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $condition = array();
      $condition['id'] = $ben_id;
      $condition['created_by_dist_code'] = $duty_obj->district_code;
      if (AuthChecker::VerifierChecker() || AuthChecker::OperatorChecker()) {
        if ($duty_obj->mapping_level == "Subdiv") {
          $created_by_local_body_code = $duty_obj->urban_body_code;
        }
        if ($duty_obj->mapping_level == "Block") {
          $created_by_local_body_code = $duty_obj->taluka_code;
        }
        $condition['created_by_local_body_code'] = $created_by_local_body_code;
      }
      $row = DB::table($schema . '.beneficiaries')->where($condition)->first();
      if (empty($row)) {
        $return_status = 0;
        $return_text = 'Application Id Not Valid';
        return response()->json(['return_status' => $return_status, 'return_msg' => $return_text]);
      }

      $doc_type_arr = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->where('id', $doc_type_id)->first();
      if ($scheme_id == 1) {
        $file_path = 'keep_st';
      } else if ($scheme_id == 3) {
        $file_path = 'keep_sc';
      } else if ($scheme_id == 10) {
        $file_path = 'keep_oap';
      }
      $base_url = url('/');
      if ($request->hasFile('file')) {

        $image_file = $request->file('file');
        $img_data = file_get_contents($image_file);
        $u_extension = $image_file->getClientOriginalExtension();
        $mime_type = $image_file->getMimeType();
        $base64 = base64_encode($img_data);
        if (strtolower($mime_type) == 'image/jpeg') {
          if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
            $extension = $u_extension;
          } else {
            $return_status = 0;
            $return_text = 'You are trying to upload an incorrect file';
            // return redirect("/")->with('error',  $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_text]);
          }
        } else if (strtolower($mime_type) == 'image/png') {
          $extension = 'png';
        } else if (strtolower($mime_type) == 'image/gif') {
          $extension = 'gif';
        } else if (strtolower($mime_type) == 'application/pdf') {
          $extension = 'pdf';
        } else {
          $return_status = 0;
          $return_text = 'You are trying to upload an incorrect file';
          // return redirect("/")->with('error',  $return_text);
          return response()->json(['return_status' => $return_status, 'return_msg' => $return_text]);
        }
        if ($u_extension != $extension) {
          $return_status = 0;
          $return_text = 'You are trying to upload an incorrect file';
          // return redirect("/")->with('error',  $return_text);
          return response()->json(['return_status' => $return_status, 'return_msg' => $return_text]);
        }

        if (AuthChecker::ApproverChecker()) {
          $mapping_level = NULL;
          $created_by_local_body_code = 0;
          //  $row = DB::table($schema . '.beneficiary')->where('id',$ben_id)->first();
          //  $created_by_local_body_code=$row->created_by_local_body_code;

        } else {
          $mapping_level = $duty_obj->mapping_level;



        }
        DB::beginTransaction();
        DB::connection('pgsql_encwrite')->beginTransaction();


        $fun_call = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                        in_beneficiary_id => " . $ben_id . ",
                        in_scheme_id => " . $scheme_id . ",
                        in_document_type => " . $doc_type_id . ",
                        in_attched_document => '" . $base64 . "',
                        in_created_by_level => '" . $mapping_level . "',
                        in_created_by => " . $user_id . ",
                        in_ip_address => '" . $request->ip() . "',
                        in_document_extension => '" . $extension . "',
                        in_document_mime_type => '" . $mime_type . "',
                        in_created_by_dist_code => " . $duty_obj->district_code . ",
                        in_created_by_local_body_code => " . $created_by_local_body_code . ",
                        in_doc_type_name => '" . $doc_type_arr->doc_name . "',
                        in_datetime => '" . $c_time . "'
                        );"
        );
        $doc_is_insert = $fun_call[0]->ben_docs_insert_archive;

        // $doc_is_insert = DB::table($schema . '.ben_docs')->insert($insert_doc_type_arr);
        $accept_reject_model = new AcceptRejectInfo;
        $accept_reject_model->created_at = $c_time;
        $accept_reject_model->application_id = $ben_id;
        $accept_reject_model->scheme_id = $scheme_id;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->op_type = 'DOCUPLOAD';
        $accept_reject_model->action_by = $user_id;
        $accept_reject_model->action_ip_address = $request->ip();
        $accept_reject_model->action_type = $request->class_basename(request()->route()->getAction()['controller']) . '@' . 'DOCUPLOAD';
        $is_saved_log = $accept_reject_model->save();

        //          if($request->ben_id==11457428)
//          {

        //           dd($doc_is_insert);
// dump($doc_is_insert);

        // dd($is_saved_log);

        //          }
        if ($doc_is_insert && $is_saved_log) {
          DB::commit();
          DB::connection('pgsql_encwrite')->commit();
          $return_status = 1;
          return response()->json(['return_status' => 1, 'return_msg' => 'ok']);
        } else {
          DB::rollback();
          DB::connection('pgsql_encwrite')->rollback();
          $return_status = 0;
          $return_text = 'Unable to Upload File..';
          // return redirect("/")->with('error',  $return_text);
          return response()->json(['return_status' => $return_status, 'return_msg' => $return_text]);
        }

        //array_push($uploaded_doc,$file_profile);

      } else {
        $return_status = 0;
        $return_text = 'Unable to Upload File..';
        // return redirect("/")->with('error',  $return_text);
        return response()->json(['return_status' => $return_status, 'return_msg' => $return_text]);
      }
    } catch (\Exception $e) {
      //dd($e);
      $return_text = 'Unable to Upload File..';
      return response()->json(['return_status' => 0, 'return_msg' => $return_text]);
    }
    // return response()->json(['return_status' => $return_status, 'return_msg' => $return_text]);
  }
  public function lbapplicationbulkApprove(Request $request)
  {

    // $designation_id_old = Auth::user()->designation_id_old;
    $user_id = AuthChecker::getUserId();
    if (AuthChecker::ApproverChecker()) {
      //dd('ok');
      $scheme_id = $request->scheme_id;
      // dd($scheme_id);
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('danger', 'Not Allowed');
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
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length = $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }

      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_verified = $role->parent_id;

      $back_url = 'workflow-lb60?scheme_id=' . $scheme_id;
      $applicationid_arr = array();
      $inputs = request()->input('approvalcheck');
      foreach ($inputs as $input) {
        array_push($applicationid_arr, $input);
      }
      $comments = NULL;
      $implode_application_arr = implode("','", $applicationid_arr);
      $in_pension_id = 'ARRAY[' . "'$implode_application_arr'" . ']';
      //dd($in_pension_id);
      try {
        DB::beginTransaction();
        $is_inserted_status_arr = DB::select("select " . $schema . ".approve_data_bulk_lb(in_verified_next_level_role_id => " . $next_level_role_id_verified . ",in_ben_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'LA')");
        //dd($is_inserted_status_arr);
        $is_inserted_status = $is_inserted_status_arr[0]->approve_data_bulk_lb;
        if ($is_inserted_status == 1) {

          DB::commit();
          return redirect('workflow-lb60?scheme_id=' . $scheme_id)->with('message', 'Applications  has been Approved Succesfully!');

        } else {
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
      } catch (\Exception $e) {
        //dd($e);
        DB::rollback();
        return redirect($back_url)->with('error', 'Error! Please try again.');
      }
    }
    if (AuthChecker::VerifierChecker()) {
      $scheme_id = $request->scheme_id;
      $back_url = 'workflow-lb60?scheme_id=' . $scheme_id;
      $c_time = date('Y-m-d H:i:s', time());
      //dd('ok');
      $scheme_id = $request->scheme_id;
      // dd($scheme_id);
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('danger', 'Not Allowed');
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
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length = $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      $applicationid_arr = array();
      $inputs = request()->input('approvalcheck');
      $comments = NULL;
      $implode_application_arr = implode("','", $inputs);
      $in_pension_id = 'ARRAY[' . "'$implode_application_arr'" . ']';
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_verified = $role->parent_id;
      try {
        DB::beginTransaction();
        $is_inserted_status_arr = DB::select("select " . $schema . ".import_from_lb_bulk(
          in_verified_next_level_role_id => " . $next_level_role_id_verified . ",
          in_op_type => 'LV',
          in_ben_id => $in_pension_id,
          in_scheme_id => $scheme_id, 
          in_c_time => '" . $c_time . "',
          in_user_id => " . $user_id . ")");
        //dd($is_inserted_status_arr);
        $is_inserted_status = $is_inserted_status_arr[0]->import_from_lb_bulk;
        if ($is_inserted_status == 1) {

          DB::commit();
          return redirect('workflow-lb60?scheme_id=' . $scheme_id)->with('message', 'Applications Verified Successfully and Submitted to Approver to Approval');

        } else {
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
      } catch (\Exception $e) {
        //dd($e);
        DB::rollback();
        return redirect($back_url)->with('error', 'Error! Please try again.');
      }
    }
  }

  function lb60misreport(Request $request)
  {
    try {
      $this->middleware('auth');
      $base_date = '2020-01-01';
      $c_time_unquie = time();
      date_default_timezone_set('Asia/Kolkata');
      $c_time = Carbon::now();
      $c_date = $c_time->format("Y-m-d");
      $is_active = 0;
      $roleArray = $request->session()->get('role');
      // $designation_id_old = Auth::user()->designation_id_old;
      $userId = Auth::user()->id;
      $district_visible = $is_urban_visible = $block_visible = 1;
      $municipality_visible = 0;
      $gp_ward_visible = 0;
      $muncList = collect([]);
      $gpList = collect([]);
      if (AuthChecker::ReportCheckerCommon()) {
        $district_visible = $is_urban_visible = $block_visible = 1;
        if ($userId == 3378)
          $scsctvisible = 1;
        else
          $scsctvisible = 0;
      } else if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
        $district_code = NULL;
        $is_urban = NULL;
        $blockCode = NULL;
        $scsctvisible = 0;
        foreach ($roleArray as $roleObj) {
          if (in_array($roleObj['scheme_id'], array(3, 2, 10, 11, 8, 9, 17, 19, 1))) {
            if (in_array($roleObj['scheme_id'], array(10))) {
              $scsctvisible = 1;
            }
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
      $gp_ward_visible = 0;
      $municipality_visible = 0;
      $districts = District::get();
      if ($scsctvisible == 1) {
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (1,3,10)  and is_active=1 order by scheme_name"));
      } else {
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (1,3,10) and  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
      }
      return view(
        'Lokkhibhandar60.misreport',
        [
          'districts' => $districts,
          'scheme_list' => $scheme_list,
          'district_visible' => $district_visible,
          'district_code_fk' => $district_code_fk,
          'is_urban_visible' => $is_urban_visible,
          'rural_urban_fk' => $rural_urban_fk,
          'block_visible' => $block_visible,
          'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
          'municipality_visible' => $municipality_visible,
          'gp_ward_visible' => $gp_ward_visible,
          'base_date' => $base_date,
          'c_date' => $c_date,
          'gpList' => $gpList,
          'muncList' => $muncList,
          'c_time_unquie' => $c_time_unquie
        ]
      );
    } catch (\Exception $e) {
      //dd($e);
    }
  }
  public function lb60misreportpost(Request $request)
  {
    $scheme_id = $request->scheme_id;
    $district = $request->district;
    $urban_code = $request->urban_code;
    $block = $request->block;
    $muncid = $request->muncid;
    $gp_ward = $request->gp_ward;
    // dd($gp_ward);
    $caste = $request->caste_category;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    $base_date = '2020-08-16';
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
      'gp_ward' => 'nullable|integer'
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
    $attributes['from_date'] = 'From Date';
    $attributes['to_date'] = 'To Date';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $scheme_row = Scheme::where('id', $scheme_id)->first();
      $user_msg = "LB Beneficiary Imported Report for the Scheme " . $scheme_row->scheme_name;
      $title = $user_msg;
      //dd($title);

      $data = array();
      $return_status = 1;
      $return_msg = '';
      $heading_msg = '';
      $external = 0;
      $external_arr = array();
      $external_filter = array();
      if (!empty($gp_ward)) {
        if ($urban_code == 1) {
          $column = "Ward";
          $heading_msg = $user_msg . ' of the Ward ' . $gp_ward_name;
          $data = $this->getWardWise($district, $block, $muncid, $gp_ward, $from_date, $to_date, $caste);
        } else {
          $column = "GP";
          $heading_msg = $user_msg . ' of the GP ' . $gp_ward_name;
          $data = $this->getGpWise($district, $block, NULL, $gp_ward, $from_date, $to_date, $caste);
        }
      } else if (!empty($muncid)) {
        $column = "Ward";
        $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
        $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
        $data = $this->getWardWise($district, $block, $muncid, NULL, $from_date, $to_date, $caste);
      } else if (!empty($block)) {
        if ($urban_code == 1) {
          $column = "Municipality";
          $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
          $data = $this->getMuncWise($district, $block, NULL, NULL, $from_date, $to_date, $caste);
        } else if ($urban_code == 2) {
          $block_arr = Taluka::where('block_code', '=', $block)->first();
          $column = "GP";
          $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
          $data = $this->getGpWise($district, $block, NULL, $gp_ward, $from_date, $to_date, $caste);
          $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
          $column = "Block";
          $data = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
        }
      } else {

        if (!empty($district)) {
          if ($urban_code == 1) {
            $column = "Sub Division";
            $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $data = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
          } else if ($urban_code == 2) {
            $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block";
            $data = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
          } else {
            $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block/Sub Division";
            $data1 = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
            $data2 = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste);
            $data = array_merge($data1, $data2);
          }
        } else {
          $column = "District";
          $heading_msg = 'District Wise ' . $user_msg;
          $data = $this->getDistrictWise($scheme_id, NULL, NULL, NULL, NULL, $from_date, $to_date);

          $external = 0;
        }
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
  public function getDistrictWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL)
  {
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->where('stack_level', 'District')->first();
    $next_level_role_id_verified = $role->parent_id;
    $next_level_role_id_approved = 0;
    $dateFromat = 'YYYY/MM/DD';
    $whereCon = "where 1=1";
    $query = "select A.location_id,A.location_name,
    COALESCE(C.total_data,0) as total_data,
    COALESCE(C.total_rejected,0) as total_rejected,
    COALESCE(D.total_sys_bank,0) as total_sys_bank,
    COALESCE(D.total_sys_aadhaar,0) as total_sys_aadhaar,
    COALESCE(D.total_sys_caste,0) as total_sys_caste,
    COALESCE(C.total_normal_verified,0) as total_normal_verified,
    COALESCE(C.total_normal_action,0) as total_normal_action,
    COALESCE(C.total_normal_approved,0) as total_normal_approved,
    COALESCE(C.total_backlb_verified,0) as total_backlb_verified,
    COALESCE(C.total_backlb_approved,0) as total_backlb_approved,
    COALESCE(C.total_bandhu_verified,0) as total_bandhu_verified,
    COALESCE(C.total_bandhu_approved,0) as total_bandhu_approved,
    COALESCE(C.total_johar_verified,0) as total_johar_verified,
    COALESCE(C.total_johar_approved,0) as total_johar_approved,
    COALESCE(C.total_oap_verified,0) as total_oap_verified,
    COALESCE(C.total_oap_approved,0) as total_oap_approved
    from(
    select district_code as location_id,district_name as location_name
     from public.m_district 
     )
     as A  
    LEFT JOIN
    (select
                count(1)  as total_data,
                count(1) filter(where is_rejected=1) as total_rejected,
                count(1) filter(where next_level_role_id IS NULL) as total_normal_action,
                count(1) filter(where is_verified=1 and is_approved=0) as total_normal_verified,
                count(1) filter(where  next_level_role_id=" . $next_level_role_id_approved . " ) as total_normal_approved,
                count(1) filter(where back_lb=1 and back_lb_finalize IS NULL) as total_backlb_verified,
                count(1) filter(where back_lb=1 and back_lb_finalize=1) as total_backlb_approved,
                count(1) filter(where is_transfer=1 and transfer_finalize IS NULL and transfer_to_scheme_id=3) as total_bandhu_verified,
                count(1) filter(where is_transfer=1 and transfer_finalize=1 and transfer_to_scheme_id=3) as total_bandhu_approved,
                count(1) filter(where is_transfer=1 and transfer_finalize IS NULL and transfer_to_scheme_id=1) as total_johar_verified,
                count(1) filter(where is_transfer=1 and transfer_finalize=1 and transfer_to_scheme_id=1) as total_johar_approved,
                count(1) filter(where is_transfer=1 and transfer_finalize IS NULL and transfer_to_scheme_id=10) as total_oap_verified,
                count(1) filter(where is_transfer=1 and transfer_finalize=1 and transfer_to_scheme_id=10) as total_oap_approved,
                created_by_dist_code
                from pension.beneficiaries where is_lb_imported=1 and scheme_id=" . $scheme_id . "   
     group by created_by_dist_code) as C ON A.location_id=C.created_by_dist_code
     LEFT JOIN
     (select
                 count(1) filter(where jb_dup_bank=1) as total_sys_bank,
                 count(1) filter(where jb_dup_aadhar=1 and  jb_dup_bank IS NULL and jb_dup_caste_certificate_no IS NULL) as total_sys_aadhaar,
                 count(1) filter(where jb_dup_caste_certificate_no=1 and jb_dup_aadhar IS NULL and jb_dup_bank IS NULL) as total_sys_caste,
                 created_by_dist_code
                 from " . $schema . ". beneficiary_dup_lb where scheme_id=" . $scheme_id . "   
      group by created_by_dist_code) as D ON A.location_id=D.created_by_dist_code";

    //echo $query;    die;
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getSubDivWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL)
  {
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
    $next_level_role_id_verified = $role->parent_id;
    $next_level_role_id_approved = 0;
    $whereMain = "where  district_code=" . $district_code;

    $query = "select A.location_id,A.location_name,
    COALESCE(C.total_data,0) as total_data,
    COALESCE(C.total_rejected,0) as total_rejected,
    COALESCE(D.total_sys_bank,0) as total_sys_bank,
    COALESCE(D.total_sys_aadhaar,0) as total_sys_aadhaar,
    COALESCE(D.total_sys_caste,0) as total_sys_caste,
    COALESCE(C.total_normal_verified,0) as total_normal_verified,
    COALESCE(C.total_normal_action,0) as total_normal_action,
    COALESCE(C.total_normal_approved,0) as total_normal_approved,
    COALESCE(C.total_backlb_verified,0) as total_backlb_verified,
    COALESCE(C.total_backlb_approved,0) as total_backlb_approved,
    COALESCE(C.total_bandhu_verified,0) as total_bandhu_verified,
    COALESCE(C.total_bandhu_approved,0) as total_bandhu_approved,
    COALESCE(C.total_johar_verified,0) as total_johar_verified,
    COALESCE(C.total_johar_approved,0) as total_johar_approved,
    COALESCE(C.total_oap_verified,0) as total_oap_verified,
    COALESCE(C.total_oap_approved,0) as total_oap_approved
    from(
        select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
        from public.m_sub_district  " . $whereMain . " 
     )
     as A  
    LEFT JOIN
    (select 
                count(1)  as total_data,
                count(1) filter(where is_rejected=1) as total_rejected,
                count(1) filter(where next_level_role_id IS NULL) as total_normal_action,
                count(1) filter(where is_verified=1 and is_approved=0) as total_normal_verified,
                count(1) filter(where  next_level_role_id=" . $next_level_role_id_approved . " ) as total_normal_approved,
                count(1) filter(where back_lb=1 and back_lb_finalize IS NULL) as total_backlb_verified,
                count(1) filter(where back_lb=1 and back_lb_finalize=1) as total_backlb_approved,
                count(1) filter(where is_transfer=1 and transfer_finalize IS NULL and transfer_to_scheme_id=3) as total_bandhu_verified,
                count(1) filter(where is_transfer=1 and transfer_finalize=1 and transfer_to_scheme_id=3) as total_bandhu_approved,
                count(1) filter(where is_transfer=1 and transfer_finalize IS NULL and transfer_to_scheme_id=1) as total_johar_verified,
                count(1) filter(where is_transfer=1 and transfer_finalize=1 and transfer_to_scheme_id=1) as total_johar_approved,
                count(1) filter(where is_transfer=1 and transfer_finalize IS NULL and transfer_to_scheme_id=10) as total_oap_verified,
                count(1) filter(where is_transfer=1 and transfer_finalize=1 and transfer_to_scheme_id=10) as total_oap_approved,
                created_by_local_body_code
                from pension.beneficiaries  where is_lb_imported=1 and scheme_id=" . $scheme_id . " and  created_by_dist_code= " . $district_code . "   
     group by created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code
     LEFT JOIN
     (select 
                 count(1) filter(where jb_dup_bank=1) as total_sys_bank,
                 count(1) filter(where jb_dup_aadhar=1 and  jb_dup_bank IS NULL and jb_dup_caste_certificate_no IS NULL) as total_sys_aadhaar,
                 count(1) filter(where jb_dup_caste_certificate_no=1 and jb_dup_aadhar IS NULL and jb_dup_bank IS NULL) as total_sys_caste,

                 created_by_local_body_code
                 from " . $schema . ". beneficiary_dup_lb  where scheme_id=" . $scheme_id . " and  created_by_dist_code= " . $district_code . "   
      group by created_by_local_body_code) as D ON A.location_id=D.created_by_local_body_code";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getBlockWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL)
  {
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
    $next_level_role_id_verified = $role->parent_id;
    $next_level_role_id_approved = 0;
    $whereMain = "where  district_code=" . $district_code;
    $query = "select A.location_id,A.location_name,
    COALESCE(C.total_data,0) as total_data,
    COALESCE(C.total_rejected,0) as total_rejected,
    COALESCE(D.total_sys_bank,0) as total_sys_bank,
    COALESCE(D.total_sys_aadhaar,0) as total_sys_aadhaar,
    COALESCE(D.total_sys_caste,0) as total_sys_caste,
    COALESCE(C.total_normal_verified,0) as total_normal_verified,
    COALESCE(C.total_normal_action,0) as total_normal_action,
    COALESCE(C.total_normal_approved,0) as total_normal_approved,
    COALESCE(C.total_backlb_verified,0) as total_backlb_verified,
    COALESCE(C.total_backlb_approved,0) as total_backlb_approved,
    COALESCE(C.total_bandhu_verified,0) as total_bandhu_verified,
    COALESCE(C.total_bandhu_approved,0) as total_bandhu_approved,
    COALESCE(C.total_johar_verified,0) as total_johar_verified,
    COALESCE(C.total_johar_approved,0) as total_johar_approved,
    COALESCE(C.total_oap_verified,0) as total_oap_verified,
    COALESCE(C.total_oap_approved,0) as total_oap_approved
    from(
        select block_code as location_id,'Block-'||block_name as location_name
       from public.m_block  " . $whereMain . " 
     )
     as A  
    LEFT JOIN
    (select   
                count(1)  as total_data,
                count(1) filter(where is_rejected=1) as total_rejected,
                count(1) filter(where next_level_role_id IS NULL) as total_normal_action,
                count(1) filter(where is_verified=1 and is_approved=0) as total_normal_verified,
                count(1) filter(where  next_level_role_id=" . $next_level_role_id_approved . " ) as total_normal_approved,
                count(1) filter(where back_lb=1 and back_lb_finalize IS NULL) as total_backlb_verified,
                count(1) filter(where back_lb=1 and back_lb_finalize=1) as total_backlb_approved,
                count(1) filter(where is_transfer=1 and transfer_finalize IS NULL and transfer_to_scheme_id=3) as total_bandhu_verified,
                count(1) filter(where is_transfer=1 and transfer_finalize=1 and transfer_to_scheme_id=3) as total_bandhu_approved,
                count(1) filter(where is_transfer=1 and transfer_finalize IS NULL and transfer_to_scheme_id=1) as total_johar_verified,
                count(1) filter(where is_transfer=1 and transfer_finalize=1 and transfer_to_scheme_id=1) as total_johar_approved,
                count(1) filter(where is_transfer=1 and transfer_finalize IS NULL and transfer_to_scheme_id=10) as total_oap_verified,
                count(1) filter(where is_transfer=1 and transfer_finalize=1 and transfer_to_scheme_id=10) as total_oap_approved,
                created_by_local_body_code
                from pension.beneficiaries  where is_lb_imported=1 and scheme_id=" . $scheme_id . " and  created_by_dist_code= " . $district_code . "   
     group by created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code
     LEFT JOIN
     (select   
                 count(1) filter(where jb_dup_bank=1) as total_sys_bank,
                 count(1) filter(where jb_dup_aadhar=1 and  jb_dup_bank IS NULL and jb_dup_caste_certificate_no IS NULL) as total_sys_aadhaar,
                 count(1) filter(where jb_dup_caste_certificate_no=1 and jb_dup_aadhar IS NULL and jb_dup_bank IS NULL) as total_sys_caste,
                 created_by_local_body_code
                 from " . $schema . ". beneficiary_dup_lb  where scheme_id=" . $scheme_id . " and  created_by_dist_code= " . $district_code . "   
      group by created_by_local_body_code) as D ON A.location_id=D.created_by_local_body_code";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
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
  public function applicationListExcel(Request $request)
  {
    try {
      $user_id = AuthChecker::getUserId();
      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      $role_obj = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->where('is_active', 1)->first();
      $next_level_role_id = $role_obj->parent_id;
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
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
      // $designation_id_old = Auth::user()->designation_id_old;
      $scheme_name_row = Scheme::where('id', $scheme_id)->first();
      $scheme_name = $scheme_name_row->scheme_name;
      $report_type = $request->report_type;
      $report_type_name = 'LB Imported Beneficiary List';
      if ($request->application_type == 5) {
        $query = DB::connection('pgsql_mis')->table($schema . '.beneficiary_dup_lb');
      } else
        $query = DB::connection('pgsql_mis')->table($schema . 'beneficiaries')->where('is_lb_imported', 1);
      $role_arr_verfied = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id = $role_arr_verfied->parent_id;
      $application_type = $request->application_type;
      //dd($application_type);
      $dist_code = $request->dist_code;
      $rural_urban_code = $request->rural_urban_code;
      $block_ulb_code = $request->block_ulb_code;
      $gp_ward_code = $request->gp_ward_code;
      $created_by_local_body_code = $request->created_by_local_body_code;
      if (!empty($dist_code) && isset($request->dist_code) && ($request->dist_code !== 'undefined')) {
        $query = $query->where('created_by_dist_code', $dist_code);
      }
      if (!empty($request->rural_urban_code) && isset($request->rural_urban_code) && ($request->rural_urban_code !== 'undefined')) {
        // $condition[$contact_table . ".rural_urban_id"] = $is_urban;
        if ($rural_urban_code == 2) {
          if (!empty($created_by_local_body_code) && isset($request->created_by_local_body_code) && ($request->created_by_local_body_code !== 'undefined')) {
            //$condition["rural_urban_id"] = 2;
            $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
          }
        }
        //'Urban'
        if ($rural_urban_code == 1) {
          if (!empty($created_by_local_body_code) && isset($request->created_by_local_body_code) && ($request->created_by_local_body_code !== 'undefined')) {
            //$condition["rural_urban_id"] = 1;
            $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
          }
          if (!empty($block_ulb_code) && isset($request->block_ulb_code) && ($request->block_ulb_code !== 'undefined')) {
            $query = $query->where('block_ulb_code', $request->block_ulb_code);
          }
        }
      }
      if (!empty($gp_ward_code) && isset($request->gp_ward_code) && ($request->gp_ward_code !== 'undefined')) {
        $query = $query->where('gp_ward_code', $request->gp_ward_code);
      }
      $application_type = $request->application_type;
      if (!empty($request->application_type)) {
        if ($request->application_type == 1) {
          $report_type_name = ' Pending ' . $report_type_name;
          if (AuthChecker::ApproverChecker()) {
            $query = $query->where('next_level_role_id', $next_level_role_id);
          } else {
            $query->whereNull('next_level_role_id')->whereNull('transfer_to_scheme_id')->whereNull('is_transfer')->whereNull('back_lb');
          }
        } else if ($request->application_type == 2) {
          $query = $query->where('next_level_role_id', $next_level_role_id);
          $report_type_name = 'Verified but Approval Pending ' . $report_type_name;
        } else if ($request->application_type == 3) {

          $query = $query->where('next_level_role_id', 0);
          $report_type_name = 'Verified and Approved ' . $report_type_name;
        } else if ($request->application_type == 4) {
          $query = $query->where('next_level_role_id', -3);
          $report_type_name = 'Rejected ' . $report_type_name;
        } else if ($request->application_type == 5) {
          $query = $query->whereraw(" (jb_dup_bank=1 or jb_dup_aadhar=1 or jb_dup_caste_certificate_no=1) ");
          //$report_type_name= $report_type_name.' Probable duplicate list';
          $report_type_name = 'Probable duplicate list ' . $report_type_name;
        } else if ($request->application_type == 6) {
          //Received from bandhu
          $query = $query->whereNull('next_level_role_id')->where('transfer_from_scheme_id', 3);
        } else if ($request->application_type == 7) {
          //Transfer to Bandhu
          $query = $query->where('is_transfer', 1)->where('transfer_to_scheme_id', 3);
        } else if ($request->application_type == 9) {
          //Received from Johar
          $query = $query->whereNull('next_level_role_id')->where('transfer_from_scheme_id', 1);
        } else if ($request->application_type == 10) {
          //Transfer to Johar
          $query = $query->where('is_transfer', 1)->where('transfer_to_scheme_id', 1);
        } else if ($request->application_type == 11) {
          //Transfer to OAP
          $query = $query->where('is_transfer', 1)->where('transfer_to_scheme_id', 10);
        } else if ($request->application_type == 12) {
          //Received from Bandhu
          $query = $query->whereNull('next_level_role_id')->where('transfer_from_scheme_id', 3);
        } else if ($request->application_type == 13) {
          //Received from Johar
          $query = $query->whereNull('next_level_role_id')->where('transfer_from_scheme_id', 1);
        } else if ($request->application_type == 14) {
          //Received from Johar
          $query = $query->where('back_lb', 1);
        }
      }
      // dd($query->tosql());
      if ($request->application_type == 5) {
        $data = $query->orderBy('lb_application_id', 'ASC')->get([
          'lb_application_id',
          'lb_beneficiary_id',
          'created_by_dist_code',
          'dob',
          'bank_code',
          'ben_full_name',
          'gender',
          'ds_phase',
          'block_ulb_name',
          'gp_ward_name',
          'bank_ifsc',
          'next_level_role_id',
          'mobile_no',
          'jb_dup_bank',
          'jb_dup_aadhar',
          'jb_dup_caste_certificate_no'
        ]);
      } else {
        $data = $query->orderBy('lb_application_id', 'ASC')->get([
          'id',
          'lb_application_id',
          'lb_beneficiary_id',
          'created_by_dist_code',
          'dob',
          'bank_code',
          'ben_full_name',
          'gender',
          'block_ulb_name',
          'gp_ward_name',
          'bank_ifsc',
          'next_level_role_id',
          'mobile_no',
          'ds_phase',
          'is_rejected',
          'is_transfer',
          'transfer_finalize',
          'transfer_from_scheme_id',
          'transfer_to_scheme_id',
          'back_lb',
          'back_lb_finalize',
        ]);
      }
      //dd($data->toArray());
      $excel_data[] = array(
        'Beneficiary ID',
        'Beneficiary Name',
        'Mobile No',
        'Block/Municipality',
        'GP/WARD',
        'DS Phase',
        'Status'
      );
      $filename = $scheme_name . "-" . $report_type_name . "-" . date('d/m/Y') . "-" . time() . ".xls";
      header("Content-Type: application/xls");
      header("Content-Disposition: attachment; filename=" . $filename);
      header("Pragma: no-cache");
      header("Expires: 0");
      echo '<table border="1">';
      echo '<tr><td alignment="center" colspan="7">' . $report_type_name . '</td></tr>';
      echo '<tr><th>LB Application Id</th><th>Beneficiary Name</th><th>Mobile No.</th><th>Block/Municipality</th><th>GP/WARD</th><th>DS Phase</th><th>Status</th></tr>';
      if (count($data) > 0) {
        foreach ($data as $row) {

          $mobile_no = (string) $row->mobile_no;
          $ben_name = $row->ben_full_name;
          if (!empty($row->ben_mname)) {
            $ben_name = $ben_name . ' ' . $row->ben_mname;
          }
          if (!empty($row->ben_lname)) {
            $ben_name = $ben_name . ' ' . $row->ben_lname;
          }
          if (!empty($mobile_no))
            $f_mobile_no = "'$mobile_no'";
          else
            $f_mobile_no = '';

          if ($row->ds_phase != '') {
            $phase_des = $this->getPhaseDes($row->ds_phase);
          } else {
            $phase_des = '';
          }
          $status_des = '';
          $status_arr = $this->getStatus($row, $application_type, $next_level_role_id, 1);
          $status_des = $status_arr['status'];

          echo "<tr><td>" . $row->lb_application_id . "</td><td>" . trim($ben_name) . "</td><td>" . $f_mobile_no . "</td><td>" . trim($row->block_ulb_name) . "</td><td>" . trim($row->gp_ward_name) . "</td><td>" . $phase_des . "</td><td>" . $status_des . "</td></tr>";
        }
      } else {
        echo '<tr><td colspan="7">No Records found</td></tr>';
      }
      echo '</table>';
    } catch (\Exception $e) {
      //dd($e);
    }
  }
  public function getPhaseDes($phase_code)
  {
    $phaseArr = DsPhase::where('phase_code', $phase_code)->first();
    //$phaselist = Config::get('constants.ds_phase.phaselist');
    $des = '';
    if (!empty($phaseArr)) {
      $des = $phaseArr->phase_des;
    }
    return $des;
  }

  private function validateInput($request, $scheme_id, $row, $action_type, $schema, $to_schema, $sp_aadhar_no_new, $sp_mobile_new, $sp_caste_certificate_no_new, $bank_code, $bank_ifsc, $old_aadhar_no, $old_mobile_no, $old_caste_certificate_no)
  {
    //dump($request->new_mobile_no); dd($sp_mobile_new);
    $return_arr = array('is_valid' => true, 'errors' => array());
    $list_error = array();
    if (empty($request->new_aadhar_no)) {
      $errorMsg = "Aadhaar Number Required";
      array_push($list_error, $errorMsg);
      $return_arr['is_valid'] = false;
    }
    if (!empty($request->new_aadhar_no)) {
      if ($this->isAadharValid(trim($request->new_aadhar_no)) == false) {
        $errorMsg = "Aadhaar Number Invalid";
        array_push($list_error, $errorMsg);
        $return_arr['is_valid'] = false;
      }
    }
    if (empty($request->new_mobile_no)) {
      $errorMsg = "Mobile Number Required";
      array_push($list_error, $errorMsg);
      $return_arr['is_valid'] = false;
    }
    if (!empty($request->new_mobile_no)) {
      if (strlen($request->new_mobile_no) != 10) {
        $errorMsg = "Mobile Number Invalid";
        array_push($list_error, $errorMsg);
        $return_arr['is_valid'] = false;
      } else if (!preg_match('/^[0-9]{10}+$/', $request->new_mobile_no)) {
        $errorMsg = "Mobile Number Invalid";
        array_push($list_error, $errorMsg);
        $return_arr['is_valid'] = false;
      } else if ($request->new_mobile_no < 1000000000) {
        $errorMsg = "Mobile Number Invalid";
        array_push($list_error, $errorMsg);
        $return_arr['is_valid'] = false;
      }
    }

    if (trim($row->caste) == 'SC' || trim($row->caste) == 'ST') {
      if ($action_type == 80) {

      } else {
        if (empty($request->caste_certificate_no)) {
          $errorMsg = "Caste Certificate No. Required";
          array_push($list_error, $errorMsg);
          $return_arr['is_valid'] = false;
        }
      }
    }
    if ($action_type == 70 || $action_type == 75 || $action_type == 80) {
      if ($action_type == 70) {
        $schema_v = 1;
      } else if ($action_type == 75) {
        $schema_v = 3;
      } else if ($action_type == 80) {
        $schema_v = 10;
      }
    } else {
      $schema_v = $scheme_id;
    }
    // dump( $schema_v);
    $man_doc_list = SchemeDocMap::select('doc_list_man')->where('scheme_code', $schema_v)->first();
    //dd( $man_doc_list);
    if (!empty($man_doc_list) && !empty($man_doc_list->doc_list_man)) {
      $pre_docs = BenDocs::where('scheme_id', $scheme_id)->where('created_by_dist_code', $row->created_by_dist_code)->where('beneficiary_id', $request->id)->get();

      //$pre_docs = DB::table($schema . '.ben_docs')->where('ben_id', $request->id)->get();
      //dump($pre_docs);
      $man_doc_array = json_decode($man_doc_list->doc_list_man);
      //dump($man_doc_array);
      $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb', 'is_profile_pic')->whereIn('id', $man_doc_array)->get();
      //dump($doc_list->toArray());
      $validation_arr = array();
      foreach ($doc_list as $doc_item) {
        if ($action_type == 80 && $doc_item->id == 3) {
          continue;
        }
        if (empty($pre_docs)) {
          $doc_uploaded = NULL;
        } else {
          $doc_uploaded = $pre_docs->where('document_type', $doc_item->id)->first();
          // dump($doc_uploaded);
        }

        if (empty($doc_uploaded)) {
          $return_text = "You Must Upload  " . $doc_item->doc_name;
          array_push($list_error, $return_text);
        }
      }
      // dd($list_error);
      if (count($list_error) > 0) {
        $return_arr['is_valid'] = false;
        $return_arr['errors'] = $list_error;
      }
    }
    if ($return_arr['is_valid'] == false) {
      $return_arr['errors'] = $list_error;
      return $return_arr;
    } else {
      if (is_null($to_schema)) {
        $schema = $schema;
      } else {
        $schema = $to_schema;
      }
      $errormsg = array();
      $is_error = 0;
      if (!empty($bank_code) && !empty($bank_ifsc)) {
        $bank_count = DB::table($schema . '.ben_bank_account_no_unique')->where('bank_code', $bank_code)->where('bank_ifsc', $bank_ifsc)->count('bank_code');
        if ($bank_count > 0) {
          $is_error = 1;
          $return_arr['is_valid'] = false;
          array_push($return_arr['errors'], 'Bank Account Number Already Exist! Please try different.');
        }
      }

      if (!empty($sp_aadhar_no_new)) {
        $aadhar_count = DB::table($schema . '.ben_aadhar_no_unique')->where('aadhar_no', trim($sp_aadhar_no_new))->count('aadhar_no');
        if ($aadhar_count > 0) {
          $is_error = 1;
          $return_arr['is_valid'] = false;
          array_push($return_arr['errors'], 'Aadhaar Number Already Exist! Please try different.');
        }
      } else {
        $aadhar_count = 0;
      }

      if (!empty($old_aadhar_no) && $aadhar_count == 0 && is_null($sp_aadhar_no_new)) {
        $aadhar_count = DB::table($schema . '.ben_aadhar_no_unique')->where('aadhar_no', trim($old_aadhar_no))->count('aadhar_no');
        if ($aadhar_count > 0) {
          $is_error = 1;
          $return_arr['is_valid'] = false;
          array_push($return_arr['errors'], 'Aadhaar Number Already Exist! Please try different.');
        }
      }
      if (!empty($sp_mobile_new)) {
        $mobile_count = DB::table($schema . '.ben_mobile_no_unique')->where('mobile_no', $sp_mobile_new)->count('mobile_no');
        if ($mobile_count > 0) {
          $is_error = 1;
          $return_arr['is_valid'] = false;
          array_push($return_arr['errors'], 'Mobile Number Already Exist! Please try different.');
        }
      } else {
        $mobile_count = 0;
      }
      if (!empty($old_mobile_no) && $old_mobile_no > 0 && $mobile_count == 0 && is_null($sp_mobile_new)) {
        $mobile_count = DB::table($schema . '.ben_mobile_no_unique')->where('mobile_no', $old_mobile_no)->count('mobile_no');
        if ($mobile_count > 0) {
          $is_error = 1;
          $return_arr['is_valid'] = false;
          array_push($return_arr['errors'], 'Mobile Number Already Exist! Please try different.');
        }
      }
      if (trim($row->caste) == 'SC' || trim($row->caste) == 'ST') {
        if ($action_type == 80) {

        } else {




          //dd($sp_caste_certificate_no_new);
          if (!empty($sp_caste_certificate_no_new)) {
            $caste_count = DB::table($schema . '.ben_caste_certificate_no_unique')->where('caste_certificate_no', $sp_caste_certificate_no_new)->count('caste_certificate_no');
            if ($caste_count > 0) {
              $is_error = 1;
              $return_arr['is_valid'] = false;
              array_push($return_arr['errors'], 'Caste Certificate No. Already Exist! Please try different.');
            }
          } else {
            $caste_count = 0;
          }
          if (!empty($old_caste_certificate_no) && $caste_count == 0 && is_null($sp_caste_certificate_no_new)) {
            $caste_count = DB::table($schema . '.ben_caste_certificate_no_unique')->where('caste_certificate_no', $old_caste_certificate_no)->count('caste_certificate_no');
            if ($caste_count > 0) {
              $is_error = 1;
              $return_arr['is_valid'] = false;
              array_push($return_arr['errors'], 'Caste Certificate No. Already Exist! Please try different.');
            }
          }

        }
      }

    }
    return $return_arr;
  }
  private function getStatus($data, $application_type, $next_level_role_id_verified, $is_excel)
  {
    $return_arr = array('can_view' => false, 'bulk_approve' => false, 'status' => '');
    if ($application_type == 5) {
      if ($data->jb_dup_bank == 1)
        $status = 'Probable duplicate list Due to Duplicate Bank Info';
      else if ($data->jb_dup_aadhar == 1 && is_null($data->jb_dup_bank) && is_null($data->jb_dup_caste_certificate_no))
        $status = 'Probable duplicate list Due to Duplicate Aadhaar Info';
      else if ($data->jb_dup_caste_certificate_no == 1 && is_null($data->jb_dup_bank) && is_null($data->jb_dup_aadhar))
        $status = 'Probable duplicate list Due to Caste Certificate Number';
      else
        $status = '';
      $return_arr['status'] = $status;
    } else {
      if ($data->is_rejected == 1) {
        $status = 'Rejected';
      } else {
        if ($data->next_level_role_id == 0 && !is_null($data->next_level_role_id)) {
          $status = 'Verified and Approved';

        } else if ($data->next_level_role_id == $next_level_role_id_verified) {
          if (AuthChecker::ApproverChecker()) {
            $status = 'Verified but Approval Pending';
            $return_arr['can_view'] = true;
            $return_arr['bulk_approve'] = true;
          } else {
            $status = 'Verified but Approval Pending';
          }

        } else if ($data->back_lb == 1) {
          if ($data->back_lb_finalize == 1) {
            $status = 'Back to LB Request has been Approved';
          } else if (is_null($data->back_lb_finalize)) {
            if (AuthChecker::VerifierChecker()) {
              $status = 'Back to LB Request';
              $return_arr['can_view'] = true;
            } else {
              $status = 'Back to LB Request has been send to Approver for Approval';
            }
          }
        } else if ($data->is_transfer == 1) {
          if ($data->transfer_to_scheme_id == 10) {
            $to_scheme = 'Pension';

          } else if ($data->transfer_to_scheme_id == 1) {
            $to_scheme = 'Pension';

          } else if ($data->transfer_to_scheme_id == 3) {
            $to_scheme = 'Pension';

          }
          if ($data->transfer_finalize == 1) {
            $status = 'Transfer Request to ' . $to_scheme . ' has been Approved';
          } else if (is_null($data->transfer_finalize)) {
            if (AuthChecker::ApproverChecker()) {
              $return_arr['can_view'] = true;
              $status = 'Transfer Request to ' . $to_scheme;
            } else {
              $status = 'Transfer Request to ' . $to_scheme . '  has been send to Approver for Approval';
            }
          }
        } else if (!is_null($data->transfer_from_scheme_id) && $data->transfer_from_scheme_id > 0) {
          if ($data->transfer_from_scheme_id == 10) {
            $from_scheme = 'Pension';

          } else if ($data->transfer_from_scheme_id == 1) {
            $from_scheme = 'Pension';

          } else if ($data->transfer_from_scheme_id == 3) {
            $from_scheme = 'Pension';

          }
          $return_arr['can_view'] = true;
          $return_arr['bulk_approve'] = true;
          $status = 'Transfer from ' . $from_scheme;
        } else {
          $return_arr['can_view'] = true;
          $return_arr['bulk_approve'] = true;
          if ($is_excel == 1) {
            $status = 'Pending';
          } else
            $status = '';
        }
      }
      $return_arr['status'] = $status;
    }
    //dd($return_arr);
    return $return_arr;
  }

}
