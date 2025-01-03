<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Configduty;
use App\Scheme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\BlkUrbanlEntryMapping;
use App\GP;
use App\Taluka;
use App\UrbanBody;
use Exception;
use Carbon\Carbon;
use App\RejectRevertReason;
use App\UrbanBodys;
use App\Ward;
use App\DocumentType;
use App\District;
use App\BenDocs;
use Illuminate\Support\Facades\View;
use TCPDF;
use App\AcceptRejectInfo;
use Illuminate\Support\Facades\Config;
use App\Helpers\AuthChecker;
use App\Helpers\PermissionManagement;
use App\Workflow;
use App\Helpers;
use App\Helpers\Helper;
use App\SchemeGenSetting;
use Illuminate\Support\Facades\Route;

use Illuminate\Support;



class JBProcessApplicationController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  public function shemeSelection(Request $request)
  {
    $auth = AuthChecker::ReportChecker();
    if ($auth) {
      $verifierURL = 'ProcessApllicationVerifier';
      $approverURL = 'ProcessApllicationApprover';
      $cmo_url = 'cmo-grievance-workflow';
      $type = (int) $request->type;
      // $designation_id = AuthChecker::getdesignation();
      $user_id = AuthChecker::getUserId();
      // dd($user_id);
      $configDuty = DB::table('duty_assignement')
        ->select('scheme_id', 'district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')
        ->where('user_id', $user_id)
        ->where('is_active', 1)
        ->get();

      $url = ($designation_id === 'Verifier') ? $verifierURL : $approverURL;

      $scheme_id_arr = [];
      $district_code = null;
      $blockUlbCode = null;
      $mapping_level = '';

      foreach ($configDuty as $duty) {
        $district_code = $duty->district_code;
        $is_urban = $duty->is_urban;
        $mapping_level = $duty->mapping_level;

        $blockUlbCode = $is_urban == 1
          ? $duty->urban_body_code
          : ($is_urban == 2 ? $duty->taluka_code : null);

        if (!in_array($duty->scheme_id, $scheme_id_arr)) {
          $scheme_id_arr[] = $duty->scheme_id;
        }
      }

      $schemeIds = DB::table('m_scheme')
        ->where('is_active', 1)
        ->whereIn('id', $scheme_id_arr)
        ->pluck('id')
        ->toArray();

      $falseSchemeIds = [];
      if ($mapping_level === 'Subdiv' || $mapping_level === 'Block') {
        $falseSchemeIds = DB::table('m_block_urban_entry_mapping')
          ->where('district_code', $district_code)
          ->where('block_ulb_code', $blockUlbCode)
          ->where('main_verification', false)
          ->distinct()
          ->pluck('scheme_id')
          ->toArray();
      } elseif ($mapping_level === 'District') {
        $falseSchemeIds = DB::table('m_district_entry_mapping')
          ->where('district_code', $district_code)
          ->where('main_approval', false)
          ->distinct()
          ->pluck('scheme_id')
          ->toArray();
      }



      $is_cmo = [];
      $not_cmo = [];
      $scheme_cmo = SchemeGenSetting::all();

      foreach ($scheme_cmo as $cmo) {
        if ($cmo->allow_cmo) {
          $is_cmo[] = $cmo->scheme_id;
        } else {
          $not_cmo[] = $cmo->scheme_id;
        }
      }
      $schemeIds = array_diff($schemeIds, $falseSchemeIds);
      $return_arr = Scheme::whereIn('id', $schemeIds)
        ->orderBy('id')
        ->get()
        ->map(function ($scheme) use ($is_cmo, $not_cmo, $designation_id, $cmo_url, $verifierURL, $approverURL, $type) {
          if ($type === 1) {
            if (in_array($scheme->id, $is_cmo)) {
              $scheme->url = $designation_id === 'Verifier'
                ? $cmo_url
                : $approverURL;
            } elseif (in_array($scheme->id, $not_cmo)) {
              $scheme->url = $designation_id === 'Verifier'
                ? $verifierURL
                : $approverURL;
            } else {
              $scheme->url = $designation_id === 'Verifier'
                ? $verifierURL
                : $approverURL;
            }
          } elseif ($type === 2) {
            if (in_array($scheme->id, $not_cmo)) {
              $scheme->url = $designation_id === 'Verifier'
                ? $verifierURL
                : $approverURL;
            } else {
              $scheme->url = $designation_id === 'Verifier'
                ? $verifierURL
                : $approverURL;
            }
          } else {
            $scheme->url = null;
          }
          return $scheme;
        });

      return view('JBProcessApplication.scheme-selection', [
        'return_arr' => $return_arr,
        'url' => $url,
        'type' => $type,
      ]);
    }
  }



  public function verifierview(Request $request)
  {
    try {
      $auth = AuthChecker::CheckerPermission();
      if ($auth) {
        $type = (int) $request->type;
        $designation_id = AuthChecker::getDesignation();
        $table_name = 'pension.beneficiaries';
        $scheme_id = (int) $request->scheme_id;
        if (!$request->has('scheme_id')) {
          return redirect('/')->with('error', 'Scheme ID is missing or invalid.');
        }
        if (!intval($scheme_id)) {
          return redirect('/')->with('error', 'Scheme ID is missing or invalid.');
        }
        $designation_id = Auth::user()->designation_id;
        if ($designation_id != 'Verifier') {
          return redirect('/')->with('error', 'Not Allowed...');
        }
        $user_id = AuthChecker::getUserId();
        $configDuty = Configduty::select('scheme_id', 'district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')
          ->where('user_id', $user_id)
          ->where('is_active', 1)
          ->where('scheme_id', $scheme_id)
          ->first();
        if (empty($configDuty)) {
          return redirect('/')->with('error', 'No Duty Assigned');
        }
        $is_urban = $configDuty->is_urban;
        $district_code = $configDuty->district_code;
        $blockUlbCode = null;
        $urban_bodys = null;
        $talukas = null;
        $gps = null;
        if ($is_urban == 1) {
          $blockUlbCode = $configDuty->urban_body_code;
          $urban_bodys = UrbanBody::where('sub_district_code', $blockUlbCode)->select('urban_body_code', 'urban_body_name')->get();
        } elseif ($is_urban == 2) {
          $blockUlbCode = $configDuty->taluka_code;
          $gps = GP::where('block_code', $blockUlbCode)->get();
        }
        $verification_allowded = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('district_code', $district_code)->where('block_ulb_code', $blockUlbCode)->first();
        $normal_verification_allowded = intval($verification_allowded->main_verification);
        if ($normal_verification_allowded == 0) {
          return redirect('/')->with('error', 'Verification Temporarily Suspended ');
        }
        $aadhar_filer_visible = 0;
        if (in_array($scheme_id, [2, 10, 11])) {
          $aadhar_filer_visible = 1;
        }

        $report_name = ($type == 2)
          ? 'Applications whose age exceed 60 years which are received from Lakshmir Bhandar Portal'
          : '';

        return view('JBProcessApplication.verifier', [
          'is_urban' => $is_urban,
          'urban_bodys' => $urban_bodys,
          'dist_code' => $district_code,
          'gps' => $gps,
          'normal_verification_allowed' => $normal_verification_allowded,
          'aadhar_filer_visible' => $aadhar_filer_visible,
          'scheme_id' => $scheme_id,
          'type' => $type,
          'report_name' => $report_name,
          'designation_id' => $designation_id,
        ]);
      }
    } catch (Exception $e) {
      dd($e);
    }
  }


  public function approverview(Request $request)
  {
    $auth = AuthChecker::ApproverPermission();
    if ($auth) {
      $user_id = AuthChecker::getUserId();
      $type = (int) $request->type;
      $designation_id = AuthChecker::getDesignation();
      $scheme_id = (int) $request->scheme_id;
      if (!$request->has('scheme_id')) {
        return redirect('/')->with('error', 'Scheme ID is missing or invalid.');
      }
      if (!intval($scheme_id)) {
        return redirect('/')->with('error', 'Scheme ID is missing or invalid.');
      }
      $configDuty = Configduty::select('scheme_id', 'district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')
        ->where('user_id', $user_id)
        ->where('is_active', 1)
        ->where('scheme_id', $scheme_id)
        ->first();
      // dd($configDuty ->district_code );
      if (empty($configDuty)) {
        return redirect('/')->with('error', 'No Duty Assigned');
      }
      $district_code = $configDuty->district_code;

      $duty_level = 'DistrictApprover';

      $levels = [
        2 => 'Rural',
        1 => 'Urban',
      ];
      // dd($scheme_id);

      $approveBtnvisible = 1;
      $scheme_capacity_arr = array();
      $distCode = $district_code;
      $main_approval_allowded = 1;
      $special_approval_allowded = 1;
      $main_approval_allowded = BlkUrbanlEntryMapping::where('main_approval', TRUE)->where('scheme_id', $scheme_id)->where('district_code', $distCode)->count();
      $special_approval_allowded = BlkUrbanlEntryMapping::where('special_approval', TRUE)->where('scheme_id', $scheme_id)->where('district_code', $distCode)->count();
      if ($main_approval_allowded == 1) {
        $approveBtnvisible = 1;
      }

      $aadhar_filer_visible = 0;
      $pr1 = $request->pr1;
      $wcd_type = $request->wcd_type;
      if ($pr1 == 'wcd' && $wcd_type == 2) {
        $aadhar_filer_visible = 1;
      }
      $report_name = ($type == 2)
        ? 'Applications whose age exceed 60 years which are received from Lakshmir Bhandar Portal'
        : '';

      return view('JBProcessApplication.approver', [
        'duty_level' => $duty_level,
        'levels' => $levels,
        'approveBtnvisible' => $approveBtnvisible,
        'dist_code' => $district_code,
        'main_approval_allowded' => $main_approval_allowded,
        'special_approval_allowded' => $special_approval_allowded,
        'aadhar_filer_visible' => $aadhar_filer_visible,
        'scheme_id' => $scheme_id,
        'type' => $type,
        'report_name' => $report_name,
        'designation_id' => $designation_id,
      ]);

    }
  }

  public function hodview(Request $request)
  {
    $scheme_id = Crypt::decrypt($request->scheme_id);
    $designation_id = Auth::user()->designation_id;
    dd($scheme_id, $designation_id);
    if ($designation_id == 'Operator') {
      return redirect('/')->with('error', 'Not Allowded...');
    }
  }


  public function verifierdata(Request $request)
  {
    try {
      $table_name = 'pension.beneficiaries';
      $user_id = AuthChecker::getUserId();
      $scheme_id = (int) $request->scheme_id;
      $configDuty = Configduty::select('scheme_id', 'district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')
        ->where('user_id', $user_id)
        ->where('is_active', 1)
        ->where('scheme_id', $scheme_id)
        ->first();
      if (empty($configDuty)) {
        return redirect('/')->with('error', 'No Duty Assigned');
      }
      $is_urban = $configDuty->is_urban;
      $district_code = $configDuty->district_code;
      $blockUlbCode = null;
      $urban_bodys = null;
      $talukas = null;
      $gps = null;
      if ($is_urban == 1) {
        $blockUlbCode = $configDuty->urban_body_code;
        $urban_bodys = UrbanBody::where('sub_district_code', $blockUlbCode)->select('urban_body_code', 'urban_body_name')->get();
      } elseif ($is_urban == 2) {
        $blockUlbCode = $configDuty->taluka_code;
        $gps = GP::where('block_code', $blockUlbCode)->get();
      }

      $limit = $request->input('length');
      $offset = $request->input('start');
      $query = DB::table($table_name)->where('next_level_role_id', null)->where('created_by_dist_code', $district_code)
        ->where('scheme_id', $scheme_id)
        ->where('created_by_local_body_code', $blockUlbCode)->whereNull('is_reverted');
      if ($request->munc != '') {
        $query = $query->where('block_ulb_code', $request->munc);
      }
      if ($request->gp_ward != '') {
        $query = $query->where('gp_ward_code', $request->gp_ward);
      }
      if ($request->aadhar_exists == 0) {
        $query = $query->where('aadhar_exits', 0);
      }
      if ($request->aadhar_exists == 1) {
        $query = $query->whereNotNull('aadhar_no');
      }
      $serachvalue = $request->search['value'];

      // dd($query->toSql(), $query->getBindings());

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
          'aadhar_exits',
          'dup_bank',
          'dup_aadhar',
          'dup_mobile',
          'no_aadhar',
          'no_mobile'
        ]);
        // Convert 'dob' to 'dd-mm-yyyy' format
        $data->each(function ($item) {
          if ($item->dob) {
            // Format the 'dob' field to 'dd-mm-yyyy'
            $item->dob = Carbon::parse($item->dob)->format('d-m-Y');
          }
        });

        // return $data;

        $filterRecords = count($data);
      } else {
        if (is_numeric($serachvalue)) {
          $ben_id = $serachvalue;
          $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
            $query1->where('id', $ben_id);
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
              'aadhar_exits',
              'dup_bank',
              'dup_aadhar',
              'dup_mobile',
              'no_aadhar',
              'no_mobile'
            ]
          );
          // Convert 'dob' to 'dd-mm-yyyy' format
          $data->each(function ($item) {
            if ($item->dob) {
              // Format the 'dob' field to 'dd-mm-yyyy'
              $item->dob = Carbon::parse($item->dob)->format('d-m-Y');
            }
          });

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
              'aadhar_exits',
              'dup_bank',
              'dup_aadhar',
              'dup_mobile',
              'no_aadhar',
              'no_mobile'
            ]
          );
          // Convert 'dob' to 'dd-mm-yyyy' format
          $data->each(function ($item) {
            if ($item->dob) {
              // Format the 'dob' field to 'dd-mm-yyyy'
              $item->dob = Carbon::parse($item->dob)->format('d-m-Y');
            }
          });


        }
        $filterRecords = count($data);
      }
      return datatables()->of($data)->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('view', function ($data) {
          $action = '&nbsp; &nbsp;<a href="' . route('processApplicationDetailsCommon', ['id' => $data->id, 'scheme_id' => $data->scheme_id, 'view_type' => 1]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
          if ($data->scheme_id == 10) {
            $action = $action . '&nbsp; &nbsp;<button type="button" id="revertbtn_' . $data->id . '" value="' . $data->id . '" class="btn btn-xs btn-info revert">Revert</button>&nbsp; &nbsp;';
            $action = $action . '&nbsp; &nbsp;<button type="button" id="rejectbtn_' . $data->id . '" value="' . $data->id . '" class="btn btn-xs btn-danger reject">Reject</button>&nbsp; &nbsp;';

          }
          return $action;
        })
        ->addColumn('id', function ($data) {
          return $data->id;
        })
        ->addColumn('name', function ($data) {
          return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
        })->addColumn('dup_no_info', function ($data) {
          $dup_no_info = array();
          if ($data->dup_bank == 1) {
            array_push($dup_no_info, 'Duplicate Bank Account Number');
          }
          if ($data->dup_aadhar == 1) {
            array_push($dup_no_info, 'Duplicate Aadhaar Number.');
          }
          if ($data->dup_mobile == 1) {
            array_push($dup_no_info, 'Duplicate Mobile Number.');
          }
          if ($data->no_aadhar == 1) {
            array_push($dup_no_info, 'Aadhaar Number Incorrect.');
          }
          if ($data->no_mobile == 1) {
            array_push($dup_no_info, 'Mobile Number Incorrect.');
          }
          if (count($dup_no_info) > 0) {
            $comma_separated = implode(",", $dup_no_info);
            return $comma_separated;
          } else {
            return '';
          }
        })
        ->rawColumns(['view', 'id', 'name', 'dup_no_info'])
        ->make(true);
    } catch (Exception $e) {
      dd($e);
    }
  }

  public function approverdata(Request $request)
  {
    try {
      // dd($request->all());
      $table_name = 'pension.beneficiaries';
      $user_id = AuthChecker::getUserId();
      // dd($user_id);
      $scheme_id = (int) $request->scheme_id;
      // dd($scheme_id);
      $configDuty = Configduty::select('scheme_id', 'district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')
        ->where('user_id', $user_id)
        ->where('is_active', 1)
        ->where('scheme_id', $scheme_id)
        ->first();
      // dd($configDuty);
      if (empty($configDuty)) {
        return redirect('/')->with('error', 'No Duty Assigned');
      }
      $district_code = $configDuty->district_code;
      // $role_id = DB::table('m_roles')
      //   ->where('scheme_id', $scheme_id)
      //   ->where('role_name', 'Approver')
      //   ->value('parent_id');

      $role_id = Workflow::getID($scheme_id, Auth::user()->designation_id);
      // dd($role_id);
      // dd($next_level_role_id);

      // $role_id = 1;
      // dd($role_id);
      // dd($role_id);
      $approveBtnvisible = 1;
      $distCode = $district_code;
      $main_approval_allowded = 1;
      $main_approval_allowded = BlkUrbanlEntryMapping::where('main_approval', TRUE)->where('scheme_id', $scheme_id)->where('district_code', $distCode)->count();
      $special_approval_allowded = BlkUrbanlEntryMapping::where('special_approval', TRUE)->where('scheme_id', $scheme_id)->where('district_code', $distCode)->count();
      if ($main_approval_allowded == 1) {
        $approveBtnvisible = 1;
      }
      $limit = $request->input('length');
      $offset = $request->input('start');
      if (!empty($request->filter_1) && !empty($request->filter_2)) {
        if ($request->filter_1 == '2') {
          $query = DB::table($table_name)->where('next_level_role_id', $role_id)->where('is_state', FALSE)
            ->where('scheme_id', $scheme_id)
            ->where('created_by_dist_code', $district_code)
            ->where('created_by_local_body_code', $request->filter_2)->where('is_rejected', 0);
        } else {
          $query = DB::table($table_name)->where('next_level_role_id', $role_id)->where('is_state', FALSE)
            ->where('scheme_id', $scheme_id)
            ->where('created_by_dist_code', $district_code)
            ->where('created_by_local_body_code', $request->filter_2)->where('is_rejected', 0);
        }
      } else {

        $query = DB::table($table_name)->where('next_level_role_id', $role_id)->where('is_state', FALSE)
          ->where('scheme_id', $scheme_id)
          ->where('created_by_dist_code', $district_code)->where('is_rejected', 0);
      }
      if ($request->filter_quota != '') {
        if ($scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {
          $query = $query->where('wt_special', $request->filter_quota);
        }
      }
      if ($scheme_id == 2) {
        if ($request->aadhar_exists == 0) {
          $query = $query->where('aadhar_exits', 0);
        }
        if ($request->aadhar_exists == 1) {
          $query = $query->whereNotNull('aadhar_no');
        }
      }
      if ($scheme_id == 1 || $scheme_id == 3 || $scheme_id == 10) {
        $query = $query->whereNull('is_lb_imported');
      }
      if ($scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {

        return redirect("/")->with('error', 'Approval temporary suspended.');
      }
      if ($scheme_id == 10) {

        $query = $query->whereraw(" (sm_flag=1 or ds_phase=8 or ds_phase=9 or (ds_phase=10 or sm_ds_mark_ix=1))");
        if ($request->sm_ds_flag != '') {
          if ($request->sm_ds_flag == 1) {
            $query = $query->where('sm_flag', 1);
          }
          if ($request->sm_ds_flag == 2) {
            $query = $query->whereraw(" ((sm_ds_mark=1 and mark_ds_phase=8) or ds_phase=8) ");
          }
          if ($request->sm_ds_flag == 3) {
            $query = $query->whereraw(" ((sm_ds_mark=1 and mark_ds_phase=9) or ds_phase=9) ");
          }
          if ($request->sm_ds_flag == 4) {
            $query = $query->whereraw(" (ds_phase=10 or  sm_ds_mark_ix=1) ");
          }
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
          'sm_flag',
          'dup_bank',
          'dup_aadhar',
          'dup_mobile',
          'no_aadhar',
          'no_mobile'
        ]);
        $filterRecords = count($data);
      } else {
        if (is_numeric($serachvalue)) {
          //$ben_id = (int) substr($serachvalue, -10);
          $ben_id = $serachvalue;
          $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
            $query1->where('id', $ben_id);

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
              'sm_flag',
              'dup_bank',
              'dup_aadhar',
              'dup_mobile',
              'no_aadhar',
              'no_mobile'
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
              'dup_bank',
              'dup_aadhar',
              'dup_mobile',
              'no_aadhar',
              'no_mobile'
            ]
          );
        }
        $filterRecords = count($data);
      }
      return datatables()->of($data)->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('view', function ($data) {
          $action = '<a href="' . route('processApplicationDetailsCommon', ['id' => $data->id, 'scheme_id' => $data->scheme_id, 'view_type' => 1 ]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
          return $action;
        })
        ->addColumn('check', function ($data) use ($approveBtnvisible) {
          if ($approveBtnvisible)
            if ($data->dup_bank == 1 || $data->dup_aadhar || $data->dup_mobile || $data->no_aadhar || $data->no_mobile) {
              return '';
            } else {
              return '<input type="checkbox" name="approvalcheck[]" onchange="document.getElementById(\'bulk_approve\').disabled = !this.checked;" value="' . $data->id . '">';

            } else
            return '';
        })
        ->addColumn('id', function ($data) {
          return $data->id;
        })
        ->addColumn('name', function ($data) {
          return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
        })
        ->rawColumns(['view', 'check', 'id', 'name'])
        ->make(true);
    } catch (Exception $e) {
      dd($e);
    }
  }



  public function showApplicantDetailsCommon(Request $request)
  {
    // dd($request->all());
    $view_type = $request->view_type ?? null;
    if (empty($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Found');
    }
    if (!is_numeric($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    if (empty($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Found');
    }
    if (!is_numeric($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Valid');
    }
    $is_verifier = AuthChecker::CheckerPermission();
    $is_approver = AuthChecker::ApproverPermission();
    $approveBtnvisible = 1;
    $verifyBtnvisible = 1;
    $user_id = AuthChecker::getUserId();
    $designation_id = Auth::user()->designation_id;
    $reject_revert_cause_list = RejectRevertReason::where('status', true)->get();
    $id = $request->id;
    $scheme_id = $request->scheme_id;
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    $scheme_name = $scheme_obj->scheme_name;
    if (empty($scheme_obj)) {
      return redirect("/")->with('danger', 'Scheme Not Found');
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
    $condition_arr = array();
    $condition_arr['id'] = $id;
    if ($duty_obj->mapping_level == "Department") {
      $created_by_local_body_code = NULL;
      $created_by_dist_code = NULL;
    } else {
      $condition_arr['created_by_dist_code'] = $duty_obj->district_code;
      $created_by_dist_code = $duty_obj->district_code;
      if ($duty_obj->mapping_level == "Subdiv") {
        $created_by_local_body_code = $duty_obj->urban_body_code;
        $condition_arr['created_by_local_body_code'] = $created_by_local_body_code;

      } else if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
        $condition_arr['created_by_local_body_code'] = $created_by_local_body_code;

      } else if ($duty_obj->mapping_level == "District") {
        $created_by_local_body_code = NULL;
      }
    }
    $row = DB::table($schema . '.beneficiaries')
      ->where($condition_arr)->first();
    if (empty($row)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if ($row->scheme_id != $scheme_id) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $is_state_login = 0;
    $district_state_name = '';
    $urban_code_state_name = '';
    $block_subdiv_state_name = '';



    $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $created_by_dist_code)->orderBy('document_type')->get();

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

    if ($duty_obj->is_urban == 1 || $duty_obj->is_urban == 2) {
      $verification_allowed = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)
        ->where('district_code', $created_by_dist_code)
        ->where('block_ulb_code', $created_by_local_body_code)
        ->first();

      $normal_verification_allowed = intval($verification_allowed->main_verification ?? 0);
      if ($normal_verification_allowed == 0) {
        $verifyBtnVisible = 0;
      }
    }

    if (is_null($duty_obj->is_urban)) {
      $approveBtnVisible = 1;

      $main_approval_allowed = BlkUrbanlEntryMapping::where('main_approval', true)
        ->where('scheme_id', $scheme_id)
        ->where('district_code', $created_by_dist_code)
        ->count();

      $special_approval_allowed = BlkUrbanlEntryMapping::where('special_approval', true)
        ->where('scheme_id', $scheme_id)
        ->where('district_code', $created_by_dist_code)
        ->count();

      if ($main_approval_allowed == 1) {
        $approveBtnVisible = 1;
      }
    }


    $doc_profile_image = DocumentType::get()->where("is_profile_pic", true)->first();
    $doc_profile_image_id = 999;
    if ($doc_profile_image) {
      $doc_profile_image_id = $doc_profile_image->id;
    }
    $scheme_capacity_arr = array();
    $scheme_capacity_arr['visible'] = 0;
    $is_dup_msg = array();
    if ($row->dup_bank == 1) {
      array_push($is_dup_msg, 'Duplicate Bank Account Number..');
      $approveBtnvisible = 0;
      $verifyBtnvisible = 0;
    }
    if ($row->dup_aadhar == 1) {
      array_push($is_dup_msg, 'Duplicate Aadhaar Number.');
      $approveBtnvisible = 0;
      $verifyBtnvisible = 0;
    }
    if ($row->dup_mobile == 1) {
      array_push($is_dup_msg, 'Duplicate Mobile Number.');
      $approveBtnvisible = 0;
      $verifyBtnvisible = 0;
    }
    if ($row->no_aadhar == 1) {
      array_push($is_dup_msg, 'Aadhaar Number Incorrect.');
      $approveBtnvisible = 0;
      $verifyBtnvisible = 0;
    }
    if ($row->no_mobile == 1) {
      array_push($is_dup_msg, 'Mobile Number Incorrect.');
      $approveBtnvisible = 0;
      $verifyBtnvisible = 0;
    }

    $approveBtnvisible = PermissionManagement::ApproveCheker($scheme_id) ? 1 : 0;
    $verifyBtnvisible = PermissionManagement::VerifyCheker($scheme_id) ? 1 : 0;
    // dd($approveBtnVisible);
    return view('pension-details-view/pension_view_common', [
      'designation_id' => $designation_id,
      'is_state_login' => $is_state_login,
      'district_state_name' => $district_state_name,
      'block_subdiv_state_name' => $block_subdiv_state_name,
      'approveBtnvisible' => $approveBtnvisible,
      'verifyBtnvisible' => $verifyBtnvisible,
      'scheme_capacity_arr' => $scheme_capacity_arr,
      'row' => $row,
      'district_name' => $district_name,
      'block_name' => $block_name,
      'gp_name' => $gp_name,
      'docs' => $docs,
      'image_id' => $doc_profile_image_id,
      'reject_revert_cause_list' => $reject_revert_cause_list,
      'is_dup_msg' => $is_dup_msg,
      'scheme_id' => $scheme_id,
      'scheme_name' => $scheme_name,
      'is_verifier' => $is_verifier,
      'is_approver' => $is_approver,
      'view_type' => $view_type,
    ]);
  }



  public function downloadDetails(Request $request)
  {
    // Retrieve parameters from the request
    $benId = $request->benId;
    $scheme_id = (int) $request->scheme_id;

    // Execute the query and fetch the first matching result
    $data = DB::table('pension.beneficiaries')
      ->where('id', $benId)
      ->where('scheme_id', $scheme_id)
      ->first();

    // Check if no data was found
    if (!$data) {
      return redirect("/")->with('danger', 'Not Allowed');
    }

    if ($data->dist_code != "") {
      $district = District::where('district_code', '=', $data->dist_code)->get(['district_code', 'district_name'])->first();
      $district_name = $district->district_name;
    }

    $block_name = "";
    if ($data->block_ulb_code != "") {
      if ($data->rural_urban_id == 1) {
        $block = UrbanBody::where('urban_body_code', '=', $data->block_ulb_code)->first();
        if (!empty($block)) {
          $block_name = $block->urban_body_name;
        }
      } else {
        if (!empty($data->block_ulb_code)) {
          $block = Taluka::where('block_code', '=', $data->block_ulb_code)->first();
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

    $data->block_name = $block_name;
    $gp_name = "";
    if ($data->gp_ward_code != "") {
      if ($data->rural_urban_id == 1) {
        $gp_ward = Ward::where('urban_body_ward_code', '=', $data->gp_ward_code)->first();
        if (!empty($gp_ward)) {
          $gp_name = $gp_ward->urban_body_ward_name;
        }
      } else {
        $gp = GP::where('gram_panchyat_code', '=', $data->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
        if (!empty($gp)) {
          $gp_name = $gp->gram_panchyat_name;
        }
      }
    }
    $data->gp_name = $gp_name;

    $schemes = DB::table('m_scheme')->where('id', $scheme_id)->first();

    // return view('JBProcessApplication.applicant_details', [
    //   'data' => $data,
    //   'district_name' => $district_name,
    //   'block_name' => $block_name,
    //   'gp_name' => $gp_name,
    //   'scheme_id' => $scheme_id,
    //   'scheme_name' => $schemes->scheme_name,

    // ]);

    // Render the Blade view and store it in a variable
    $pdfContent = View::make('JBProcessApplication.applicant_details', [
      'data' => $data,
      'district_name' => $district_name,
      'block_name' => $block_name,
      'gp_name' => $gp_name,
      'scheme_id' => $scheme_id,
      'scheme_name' => $schemes->scheme_name,
    ])->render();

    // Initialize TCPDF object
    $pdf = new TCPDF('P', 'mm', 'A4');  // Set page orientation to Portrait and size to A4
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Jai Bangla');
    $pdf->SetTitle('Beneficiary Details');
    $pdf->SetSubject('Beneficiary PDF');
    // $pdf->SetHeaderData('', 0, 'Jai Bangla', 'Beneficiary Details', [0, 0, 0], [0, 0, 0]);

    // Set fonts
    $pdf->SetFont('dejavusans', '', 10);

    // Add a page and set margins
    $pdf->AddPage();
    $pdf->SetMargins(10, 5, 10);  // Adjusting margins for the page

    // Adjust the HTML content to fit within the A4 page
    $pdf->writeHTML($pdfContent, true, false, true, false, '');

    // Output the PDF as a download
    return $pdf->Output('Beneficiary_Details_' . $benId . '.pdf', 'D');
  }


  public function verifydata(Request $request)
  {
    if (empty($request->benId)) {
      return redirect("/")->with('danger', 'Applicant ID Not Found');
    }
    if (!is_numeric($request->benId)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    if (empty($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Found');
    }
    if (!is_numeric($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Valid');
    }
    $scheme_id = $request->scheme_id;
    $verify = PermissionManagement::VerifyCheker($scheme_id);
    if ($verify) {
      $user_id = AuthChecker::getUserId();
      $id = $request->benId;
      // dd($id);
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }



      $condition_arr = array();
      $condition_arr['id'] = $id;
      if ($duty_obj->mapping_level == "Department") {
        $created_by_local_body_code = NULL;
        $created_by_dist_code = NULL;
      } else {
        $condition_arr['created_by_dist_code'] = $duty_obj->district_code;
        $created_by_dist_code = $duty_obj->district_code;
        if ($duty_obj->mapping_level == "Subdiv") {
          $created_by_local_body_code = $duty_obj->urban_body_code;
          $condition_arr['created_by_local_body_code'] = $created_by_local_body_code;

        } else if ($duty_obj->mapping_level == "Block") {
          $created_by_local_body_code = $duty_obj->taluka_code;
          $condition_arr['created_by_local_body_code'] = $created_by_local_body_code;

        } else if ($duty_obj->mapping_level == "District") {
          $created_by_local_body_code = NULL;
        }
      }
      $row = DB::table($schema . '.beneficiaries')->where('scheme_id', $scheme_id)
        ->where($condition_arr)->first();
      if (empty($row)) {
        return redirect("/")->with('danger', 'Beneficiary does not Exists');
      }
      if ($row->scheme_id != $scheme_id) {
        return redirect("/")->with('danger', 'Not Allowed');
      }

      $c_time = date('Y-m-d H:i:s', time());
      $Verified = "Verified";
      $Rejected = 1;
      $comments = $request->comments;
      $accept_reject_model = new AcceptRejectInfo;
      $accept_reject_model->created_at = $c_time;
      $accept_reject_model->application_id = $id;
      $accept_reject_model->scheme_id = $scheme_id;
      $accept_reject_model->user_id = $user_id;
      $accept_reject_model->comment_message = $comments;
      $accept_reject_model->user_id = $user_id;
      $accept_reject_model->created_by_dist_code = $created_by_dist_code;
      $accept_reject_model->created_by_local_body_code = $created_by_local_body_code;
      $accept_reject_model->ip_address = request()->ip();
      $next_level_role_id = Workflow::getParentId($scheme_id, Auth::user()->designation_id);
      //  dd($next_level_role_id);

      if ($_POST['submit'] == 'Verify') {
        if ($scheme_id == 10 || $scheme_id == 11 || $scheme_id == 2) {
          if ($scheme_id == 10)
            return redirect("/")->with('error', 'Verification temporary suspended.');
          $allowded_arr = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('block_ulb_code', $created_by_local_body_code)->where('district_code', $created_by_dist_code)->first();
          $verification_allowded = intval($allowded_arr->main_verification);
          if ($verification_allowded == 0) {
            return redirect("/")->with('danger', 'Verification is temporarily suspended');
          }
        }
        if ($row->dup_bank == 1) {
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('error', 'Duplicate Bank Account Number..');
        }
        if ($row->dup_aadhar == 1) {
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('error', 'Duplicate Aadhaar Number.');
        }
        if ($row->dup_mobile == 1) {
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('error', 'Duplicate Mobile Number.');
        }
        if ($row->no_aadhar == 1) {
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('error', 'Aadhaar Number Incorrect.');
        }
        if ($row->no_mobile == 1) {
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('error', 'Mobile Number Incorrect.');
        }
        $accept_reject_model->op_type = class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod() . 'AV';


        $input = [
          'is_verified' => 1,
          'next_level_role_id' => $next_level_role_id,
          'comments' => $comments,
          'verification_date' => $c_time,
          'verified_by' => $user_id,
          'action_by' => $user_id,
          'action_ip_address' => $request->ip(),
          'action_type' => class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod()
        ];

        DB::beginTransaction();

        $is_status_updated = DB::table($schema . '.beneficiaries')->where('id', $id)->where('created_by_dist_code', $created_by_dist_code)->whereraw(" (dup_bank=0 or dup_bank IS NULL) and (dup_aadhar=0 or dup_aadhar IS NULL) and (dup_mobile=0 or dup_mobile IS NULL) and (no_aadhar=0 or no_aadhar IS NULL) and (no_mobile=0 or no_mobile IS NULL)")->whereNotNull('bank_code')->whereNull('next_level_role_id')->update($input);

        $is_saved_log = $accept_reject_model->save();
        //dd($is_status_updated);
        if ($is_status_updated && $is_saved_log) {
          DB::commit();
          return redirect('ProcessApllicationVerifier?scheme_id=' . $scheme_id)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Forwarded Succesfully!');
        } else {
          DB::rollback();
          return redirect('ProcessApllicationVerifier?scheme_id=' . $scheme_id)->with('message', 'Error! Please try again.');
        }
      } else if ($_POST['submit'] == 'Revert') {

        $accept_reject_model->op_type = class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod() . 'AREVERT';


        $input = [
          'next_level_role_id' => NULL,
          'is_verified' => 0,
          'is_approved' => 0,
          'is_reverted' => 1,
          'action_by' => $user_id,
          'action_ip_address' => $request->ip(),
          'action_type' => class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod()
        ];

        DB::beginTransaction();

        $is_status_updated = DB::table($schema . '.beneficiaries')->where('id', $id)->where('created_by_dist_code', $created_by_dist_code)->update($input);

        $is_saved_log = $accept_reject_model->save();
        //dd($is_status_updated);
        if ($is_status_updated && $is_saved_log) {
          DB::commit();
          return redirect('ProcessApllicationVerifier?scheme_id=' . $scheme_id)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Reverted Succesfully!');
        } else {
          DB::rollback();
          return redirect('ProcessApllicationVerifier?scheme_id=' . $scheme_id)->with('message', 'Error! Please try again.');
        }
      } else if ($_POST['submit'] == 'Reject') {
        $is_state_login = 0;
        try {
          $accept_reject_model->op_type = class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod() . 'AR';
          $input = [
            'verification_rejected' => $Rejected,
            'comments' => $comments,
            'next_level_role_id' => -1,
            'is_approved' => 2,
            'is_verified' => 2,
            'is_rejected' => 1,
            'rejected_date' => $c_time,
            'rejected_by' => $user_id,
            'is_clean' => 10,
            'action_by' => $user_id,
            'action_ip_address' => $request->ip(),
            'action_type' => class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod()
          ];
          $appPrefix = "App";
          // $modelName = $appPrefix . "\\" . $ben_table;
          DB::beginTransaction();
          if ($is_state_login == 1) {
            $is_status_updated = DB::table($schema . '.beneficiaries')->where('id', $id)->where('is_state', TRUE)->update($input);
          } else {
            $is_status_updated = DB::table($schema . '.beneficiaries')->where('id', $id)->where('created_by_dist_code', $created_by_dist_code)->update($input);
          }
          $is_saved_log = $accept_reject_model->save();
          $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
          if (in_array($scheme_id, $scheme_dedup_list)) {
            $free_pending_bank_duplicate_arr = DB::select("select " . $schema . ".free_pending_bank_duplicate_data(in_scheme_id => " . $scheme_id . ", in_district_code => " . $created_by_dist_code . ")");
            //dd($free_pending_bank_duplicate_arr);
            $free_pending_bank_duplicate_data = $free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
            if (!empty(trim($row->mobile_no))) {
              $sp_mobile = $row->mobile_no;
            } else {
              $sp_mobile = 0;
            }
            $reject_dup_adjustment_arr = DB::select("select " . $schema . ".reject_dup_adjustment(
            in_old_bank_ifsc => '" . $row->bank_ifsc . "', 
            in_old_bank_code => '" . $row->bank_code . "', 
            in_old_aadhar_no => '" . $row->aadhar_no . "', 
            in_old_mobile_no => " . $sp_mobile . "
            )");
            $reject_dup_adjustment = $reject_dup_adjustment_arr[0]->reject_dup_adjustment;
          } else {
            $reject_dup_adjustment = 1;
            $free_pending_bank_duplicate_data = 1;
          }
          if ($is_status_updated && $is_saved_log && $free_pending_bank_duplicate_data && $reject_dup_adjustment) {
            DB::commit();
          
            return redirect('ProcessApllicationVerifier?scheme_id=' . $scheme_id)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Rejected Succesfully!');
          } else {
            DB::rollback();
            return redirect('ProcessApllicationVerifier?scheme_id=' . $scheme_id)->with('message', 'Error! Please try again.');
          }
        } catch (Exception $e) {
          return redirect('ProcessApllicationVerifier?scheme_id=' . $scheme_id)->with('message', 'Error! Please try again.');
        }
      }



    }

  }


  public function approvedata(Request $request)
  {
    if (empty($request->benId)) {
      return redirect("/")->with('danger', 'Applicant ID Not Found');
    }
    if (!is_numeric($request->benId)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    if (empty($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Found');
    }
    if (!is_numeric($request->scheme_id)) {
      return redirect("/")->with('danger', 'Scheme ID Not Valid');
    }
    $scheme_id = $request->scheme_id;
    $approve = PermissionManagement::ApproveCheker($scheme_id);
    if ($approve) {
      $user_id = AuthChecker::getUserId();
      $id = $request->benId;
      $c_time = date('Y-m-d H:i:s', time());
      $table_name = 'pension.beneficiaries';

      $duty = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->first();
      $district_code = $duty->district_code;

      $c_time = date('Y-m-d H:i:s', time());
      $table_name = 'pension.beneficiaries';

      if ($table_name == '') {
        return redirect('/')->with('error', 'Scheme Not Found...');
      }
      $user_id = AuthChecker::getUserId();
      $id = $request->benId;
      $Verified = "Verified";
      $Rejected = 1;
      $comments = $request->comments;
      $accept_reject_model = new AcceptRejectInfo;
      $accept_reject_model->created_at = $c_time;
      $accept_reject_model->application_id = $id;
      $accept_reject_model->scheme_id = $scheme_id;
      $accept_reject_model->user_id = $user_id;
      $accept_reject_model->comment_message = $comments;
      $accept_reject_model->user_id = $user_id;
      $accept_reject_model->created_by_dist_code = $district_code;
      $accept_reject_model->op_type = class_basename(request()->route()->getAction()['controller']);
      $accept_reject_model->ip_address = request()->ip();
      $user_id = AuthChecker::getUserId();
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



      $next_level_role_id = Workflow::getID($scheme_id, Auth::user()->designation_id);

      $row = DB::table($table_name)->where('id', '=', $id)->where('next_level_role_id', '=', $next_level_role_id)->first();


      if (empty($row)) {
        return redirect("/")->with('danger', 'Application id Not Found');
      }

      if ($_POST['submit'] == 'Approve') {

        $accept_reject_model->op_type = 'AA';
        if ($scheme_id == 10 || $scheme_id == 11 || $scheme_id == 2) {

          $allowded_arr = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('block_ulb_code', $row->created_by_local_body_code)->where('district_code', $district_code)->first();
          if ($row->wt_special == 1) {
            $approval_allowded = intval($allowded_arr->special_approval);
            if ($approval_allowded == 0) {
              return redirect("/")->with('danger', 'Special Approval  without Quota  is temporarily suspended');
            }
          } else {
            $approval_allowded = intval($allowded_arr->main_approval);
            if ($approval_allowded == 0) {
              return redirect("/")->with('danger', 'Approval is temporarily suspended');
            }
          }
        }
        if ($row->wt_special == 1) {
          $scheme_capacity_arr = Helper::getCapacityWtQuotaDistrict($scheme_id, $district_code);
          $scheme_capacity_arr['total_data'] = $scheme_capacity_arr['approved'];
          if ($scheme_capacity_arr['visible'] == 1) {
            if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
              $errorMsgCap = "Total no. of Approved applications (Special Quota) " . $scheme_capacity_arr['total_data'] . " exceeds the Special quota " . $scheme_capacity_arr['capacity'];
              return redirect("/")->with('danger', $errorMsgCap);
            }
          }
        } else {
          $scheme_capacity_arr = array();
        }
        $payment_start_date = date('Y-m-d');
        if ($scheme_id == 10 && $row->ds_phase == 10 && $payment_start_date < '2024-04-01') {
          $payment_start_date = '2024-04-01';

        }
        if ($scheme_id == 11) {
          $input = [
            'is_approved' => 1,
            'next_level_role_id' => $next_level_role_id,
            'comments' => $comments,
            'payment_start_date' => $payment_start_date,
            'approval_date' => $c_time,
            'approved_by' => $user_id,
            'wp_phase' => 2,
            'action_by' => $user_id,
            'action_ip_address' => $request->ip(),
            'action_type' => class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod()
          ];
        } else
          $input = [
            'is_approved' => 1,
            'next_level_role_id' => $next_level_role_id,
            'comments' => $comments,
            'payment_start_date' => $payment_start_date,
            'approval_date' => $c_time,
            'approved_by' => $user_id,
            'action_by' => $user_id,
            'action_ip_address' => $request->ip(),
            'action_type' => class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod()
          ];
        $appPrefix = "App";

        DB::beginTransaction();

        if ($scheme_id == 10) {
          $is_status_updated = DB::table($table_name)->where('id', $id)->where('created_by_dist_code', $district_code)->whereNull('is_lb_imported')->whereNotNull('bank_code')->whereraw(" (sm_flag=1 or ds_phase=8 or ds_phase=9 or (ds_phase=10 or sm_ds_mark_ix=1))")->update($input);
        } else
          $is_status_updated = DB::table($table_name)->where('id', $id)->where('created_by_dist_code', $district_code)->whereNotNull('bank_code')->update($input);

        $is_saved_log = $accept_reject_model->save();
        if ($is_status_updated && $is_saved_log) {
          DB::commit();
          return redirect('ProcessApllicationApprover?scheme_id=' . $scheme_id)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Approved Succesfully!');
        } else {
          DB::rollback();
          return redirect('ProcessApllicationApprover?scheme_id=' . $scheme_id)->with('message', 'Error! Please try again.');
        }
      } else if ($_POST['submit'] == 'Reject') {
        $accept_reject_model->op_type = 'AR';
        $input = [
          'approval_rejected' => $Rejected,
          'comments' => $comments,
          'next_level_role_id' => -1,
          'is_approved' => 2,
          'is_verified' => 2,
          'is_rejected' => 1,
          'rejected_date' => $c_time,
          'rejected_by' => $user_id,
          'is_clean' => 10,
          'action_by' => $user_id,
          'action_ip_address' => $request->ip(),
          'action_type' => class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod()
        ];
        $appPrefix = "App";
        DB::beginTransaction();
        $is_status_updated = DB::table($table_name)->where('id', $id)->where('created_by_dist_code', $district_code)->update($input);
        $is_saved_log = $accept_reject_model->save();
        $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
        if (in_array($scheme_id, $scheme_dedup_list)) {
          $free_pending_bank_duplicate_arr = DB::select("select " . $schema . ".free_pending_bank_duplicate_data(in_scheme_id => " . $scheme_id . ", in_district_code => " . $district_code . ")");
          //dd($free_pending_bank_duplicate_arr);
          $free_pending_bank_duplicate_data = $free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
          if (!empty(trim($row->mobile_no))) {
            $sp_mobile = $row->mobile_no;
          } else {
            $sp_mobile = 0;
          }
          $reject_dup_adjustment_arr = DB::select("select " . $schema . ".reject_dup_adjustment(
            in_old_bank_ifsc => '" . $row->bank_ifsc . "', 
            in_old_bank_code => '" . $row->bank_code . "', 
            in_old_aadhar_no => '" . $row->aadhar_no . "', 
            in_old_mobile_no => " . $sp_mobile . "
            )");
          $reject_dup_adjustment = $reject_dup_adjustment_arr[0]->reject_dup_adjustment;
        } else {
          $reject_dup_adjustment = 1;
          $free_pending_bank_duplicate_data = 1;
        }
        if ($is_status_updated && $is_saved_log && $free_pending_bank_duplicate_data && $reject_dup_adjustment) {
          DB::commit();
          return redirect('ProcessApllicationApprover?scheme_id=' . $scheme_id)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Rejected Succesfully!');
        } else {
          DB::rollback();
          return redirect('ProcessApllicationApprover?scheme_id=' . $scheme_id)->with('message', 'Error! Please try again.');
        }
      } else if ($_POST['submit'] == 'Revert') {
        $accept_reject_model->op_type = 'AE';
        $input = [
          'approval_rejected' => 3,
          'comments' => $comments,
          'next_level_role_id' => NULL,
          'is_verified' => 0,
          'is_approved' => 0,
          'is_reverted' => 1,
          'action_by' => $user_id,
          'action_ip_address' => $request->ip(),
          'action_type' => class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod()
        ];
        $appPrefix = "App";
        DB::beginTransaction();
        $is_status_updated = DB::table($table_name)->where('id', $id)->where('created_by_dist_code', $district_code)->update($input);
        $is_saved_log = $accept_reject_model->save();
        if ($is_status_updated && $is_saved_log) {
          DB::commit();
          return redirect('ProcessApllicationApprover?scheme_id=' . $scheme_id)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Reverted Succesfully!');
        } else {
          DB::rollback();
          return redirect('ProcessApllicationApprover?scheme_id=' . $scheme_id)->with('message', 'Error! Please try again.');
        }
      }
    }
  }

  public function applicant_details(Request $request)
  {
    $applicant_details = [
      'app_id' => 6359286,
      'app_name' => 'OLIMA SEKH',
      'app_block' => 'KALIGANJ',
      'app_district' => 'NADIA',
    ];

    $applicant_activity = [
      [
        'activity' => 'Application Submitted',
        'datetime' => '2021-02-06',
        'remarks' => 'Data imported from excel sheet provided by concerned department',
      ],
      [
        'activity' => 'Applicant’s Bank details',
        'datetime' => '',
        'remarks' => 'For the Bank Account Details Bank IFSC - SBIN0003242, Bank A/c - 30451287145',
      ],
      [
        'activity' => 'Bank details updated by Verifier due to IFMS payment failed',
        'datetime' => '2021-07-02 08:17:57',
        'remarks' => 'Old Bank A/c details – 30451287145, Bank IFSC - SBIN0003242. <br/> Modified with remarks – “IFMS Failed Update Bank Details” & Bank Account Details as Bank IFSC - SBIN0003242, Bank A/c – 30451287145, Updated by verifier (Mob No. – 8373050605, User name- bdov@kaliganj, Email ID - bdo_k@yahoo.com)',
      ],
      [
        'activity' => 'Account Validation Lot Creation',
        'datetime' => '2023-02-22 16:25:39',
        'remarks' => 'For the Bank Account Details Bank IFSC- SBIN0003242 Bank A/C- 30451287145, Branch- DEBAGRAM',
      ],
      [
        'activity' => 'Account Validation Lot Response Receive',
        'datetime' => '2023-02-27 12:10:14',
        'remarks' => 'Validation success.',
      ],
      [
        'activity' => 'Bank details updated Approver',
        'datetime' => '2023-04-01 11:30:18',
        'remarks' => 'Old Bank A/c details – 30451287145, Bank IFSC - SBIN0003242.  <br/> Modified with remarks – ‘Ac/Close’ & Bank Account Details as IFSC- PUNB0021720, Bank A/C – 0217010492799 by Nadia district Approver (Mob No. - 8373050635, Username- ADM_ D Nadia, email_id- admd.nadia1@gmail.com)',
      ],
      [
        'activity' => 'Bank details updated Approver',
        'datetime' => '2023-08-21 16:52:24',
        'remarks' => 'Old Bank A/c details – 0217010492799, Bank IFSC - PUNB0021720.  <br/> Modified with remarks – ‘ac close’ & Bank Account Details as IFSC- SBIN0000176, Bank A/C – 35957247985 by Nadia district Approver (Mob No. - 8373050635, Username- ADM_ D Nadia, email_id- admd.nadia1@gmail.com)',
      ],
      [
        'activity' => 'Bank details updated by Approver end',
        'datetime' => '2023-10-04 11:19:07',
        'remarks' => 'Old Bank A/c details – 35957247985, Bank IFSC - SBIN0000176.   <br/>Modified with remarks – ‘BDO Kaliganj Memo No.1996/Klj,Dated.02.08.2023.’ & Bank Account Details as IFSC- SBIN0003242, Bank A/C – 30451287145 by Nadia district Approver (Mob No. - 8373050635, Username- ADM_ D Nadia, email_id- admd.nadia1@gmail.com)',
      ],
      [
        'activity' => 'Bank details updated by Approver',
        'datetime' => '2023-12-21 11:15:53',
        'remarks' => 'Old Bank A/c details – 30451287145, Bank IFSC - SBIN0003242.  <br/> Modified with remarks – ‘AC CLOSE’ & Bank Account Details as IFSC- SBIN0005681, Bank A/C – 34316144772 by Nadia district Approver (Mob No. - 8373050635, Username- ADM_ D Nadia, email_id- admd.nadia1@gmail.com)',
      ],
      [
        'activity' => 'Bank details updated by Approver',
        'datetime' => '2024-01-29 18:44:49',
        'remarks' => 'Old Bank A/c details – 34316144772, Bank IFSC - SBIN0005681.   <br/>Modified with remarks – ‘Changed as per request of BDO Kaliganj vide Memo No. 245/KLJ Dated 25/01/2024’ & Bank Account Details as IFSC- SBIN0003242, Bank A/C – 30451287145 by Nadia district Approver (Mob No. - 8373050635, Username- ADM_ D Nadia, email_id- admd.nadia1@gmail.com)',
      ],
    ];

    // Decode remarks to render HTML tags properly
    foreach ($applicant_activity as &$activity) {
      $activity['remarks'] = htmlspecialchars_decode($activity['remarks']);
    }

    // Render the view content
    $pdfContent = View::make('applicant-details.details', [
      'applicant_details' => $applicant_details,
      'applicant_activity' => $applicant_activity,
    ])->render();

    // Initialize TCPDF object
    $pdf = new TCPDF('P', 'mm', 'A4'); // Set page orientation to Portrait and size to A4
    $pdf->SetAuthor('Jai Bangla');
    $pdf->SetTitle('Applicant Activity Details');
    $pdf->SetSubject('Applicant Activity PDF');

    // Add a page and set margins
    $pdf->AddPage();
    $pdf->SetMargins(10, 5, 10); // Adjusting margins for the page

    // Write the rendered HTML content
    $pdf->writeHTML($pdfContent, true, false, true, false, '');

    // Output the PDF as a download
    return $pdf->Output('Applicant_Details-' . $applicant_details['app_id'] . '-Jai-Bangla.pdf', 'D');
  }



  public function applicant_details_multiple(Request $request)
  {
    // Sample array of multiple applicants
    $applicants = [
      [
        'app_id' => 6353952,
        'app_name' => 'PRABHAT SHIKDAR',
        'app_block' => 'KALIGANJ',
        'app_district' => 'NADIA',
        'activities' => [
          [
            'activity' => 'Application Submitted',
            'datetime' => '2021-02-06',
            'remarks' => 'Data imported from excel sheet provided by concerned department',
          ],
          [
            'activity' => 'Applicant’s Bank details',
            'datetime' => '',
            'remarks' => 'For the Bank Account Details Bank IFSC - SBIN0003242, Bank A/c - 30451287145',
          ],

        ],
      ],
      [
        'app_id' => 8250535,
        'app_name' => 'MD REJAUL HAQUE',
        'app_block' => 'KALIGANJ',
        'app_district' => 'NADIA',
        'activities' => [
          [
            'activity' => 'Application Submitted',
            'datetime' => '2022-08-25',
            'remarks' => 'From the KALIGANJ Block Operator (Mobile_number- 9332393867, Username- klignjopt1, email_id- biswasm36@gmail.com)',
          ],
          [
            'activity' => 'Application Verification',
            'datetime' => '2022-08-25 07:39:54',
            'remarks' => 'From the KALIGANJ Block Verifier (Mobile_number- 8373050605, Username- bdov@kaliganj, email_id- bdo_k@yahoo.com) & ip address 172.20.140.8.',
          ],
          [
            'activity' => 'Application Approval',
            'datetime' => '2022-08-26 11:43:04',
            'remarks' => 'From the KALIGANJ Block Approver (Mobile_number- 8373050635, Username- ADM_ D Nadia, email_id- admd.nadia1@gmail.com) & ip address 172.20.140.8.',
          ],
          [
            'activity' => 'Applicant’s Bank details',
            'datetime' => '',
            'remarks' => 'For the Bank Account Details Bank IFSC - SBIN0001382, Bank A/c – 30249634951.',
          ],
          [
            'activity' => 'Bank details updated by Verifier due to SBI failed',
            'datetime' => '2022-10-11 08:45:36',
            'remarks' => 'Old Bank A/c details – 30249634951, Bank IFSC - SBIN0001382. Modified with remarks – “SBI Failed Update Bank Details” & Bank Account Details as IFSC - PUNB0RRBBGB, Bank A/c – 5414019163977. Update by Verifier (Mob No. – 8373050605, User name- bdov@kaliganj, Email ID - bdo_k@yahoo.com)',
          ],

        ],
      ],


      [
        'app_id' => 6359286,
        'app_name' => 'OLIMA SEKH',
        'app_block' => 'KALIGANJ',
        'app_district' => 'NADIA',
        'activities' => [
          [
            'activity' => 'Application Submitted',
            'datetime' => '2021-02-07',
            'remarks' => 'Data imported from excel sheet provided by concerned department',
          ],
          [
            'activity' => 'Applicant’s Bank details',
            'datetime' => '',
            'remarks' => 'For the Bank Account Details Bank IFSC - UTBI0RRBBGB, Bank A/c - 5123020294314',
          ],
          [
            'activity' => 'Bank details updated Approver',
            'datetime' => '2023-02-07 19:28:35',
            'remarks' => 'Old Bank A/c details – 5123020294314, Bank IFSC - UTBI0RRBBGB. Modified with remarks – “Ac” & Bank Account Details as IFSC - BKID0004103, Bank A/c – 410318210000207 Update by Approver (Mob No. – 8373050635, User name- ADM_ D Nadia, Email ID - admd.nadia1@gmail.com)',
          ],
          [
            'activity' => 'Account Validation Creation',
            'datetime' => '2023-02-25 20:57:19',
            'remarks' => 'For the Bank Account Details Bank IFSC- BKID0004103 Bank A/C- 410318210000207',
          ],
          [
            'activity' => 'Account Validation Response',
            'datetime' => '2023-03-03 11:21:13',
            'remarks' => 'Account Validation success but name validation failed. The name response from bank was OLIMA BIBI SEKH. Whereas the applicant’s name is OLIMA SEKH for the bank account details Bank IFSC- BKID0004103 Bank A/C- 410318210000207.',
          ],
          [
            'activity' => 'Failed Name Validation accepted as Minor Mismatch',
            'datetime' => '2023-04-15 10:01:46',
            'remarks' => 'From KALIGANJ Block Verifier (Mob No. - 8373050605,Username- bdov@kaliganj, email_id- bdo_k@yahoo.com)',
          ],
          [
            'activity' => 'Bank details updated by Approver',
            'datetime' => '2023-08-21 17:21:40',
            'remarks' => 'Old Bank A/c details – 410318210000207, Bank IFSC - BKID0004103. Modified with remarks – ‘ac close’ & Bank Account Details as IFSC- SBIN0000176,  Bank A/C – 35504595155 by Nadia district Approver (Mob No. - 8373050635, Username- ADM_ D Nadia, email_id- admd.nadia1@gmail.com)',
          ],
          [
            'activity' => 'Bank details updated by Approver',
            'datetime' => '2023-09-29 14:31:04',
            'remarks' => 'Old Bank A/c details – 35504595155, Bank IFSC - SBIN0000176. Modified with remarks – ‘Correction of account from BOD Kaliganj Vide Memo No. 3190/Kly Dated- 01.09.2023.’ & Bank Account Details as Bank IFSC- PUNB0RRBBGB Bank A/C – 5123020294314 by Nadia district Approver (Mob No. - 8373050635, Username- ADM_ D Nadia, email_id- admd.nadia1@gmail.com)',
          ]
        ],


      ],
    ];

    // Initialize TCPDF object
    $pdf = new TCPDF('P', 'mm', 'A4');
    $pdf->SetAuthor('Jai Bangla');
    $pdf->SetTitle('Applicant Activity Details');
    $pdf->SetSubject('Applicant Activity PDF');
    $pdf->SetMargins(10, 5, 10);
    $pdf->AddPage();

    foreach ($applicants as $applicant) {
      $pdfContent = View::make('applicant-details.details_multi', [
        'applicant' => $applicant,  // Pass the current applicant
      ])->render();
      $pdf->writeHTML($pdfContent, true, false, true, false, '');

      $pdf->AddPage();
    }

    return $pdf->Output('Applicant_Details-Jai-Bangla.pdf', 'D');
  }


}
