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
use App\MapLavel;
use Mpdf\Pdf\Protection;
use Barryvdh\DomPDF\Facade\Pdf;
use App\AcceptRejectInfo;
use App\DsPhase;
use PSpell\Config;
use App\Helpers\AuthChecker;
use App\Helpers\PermissionManagement;
use App\Workflow;
use App\Helpers;
use App\SchemeGenSetting;

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
      $designation_id_old = AuthChecker::getdesignation();
      $user_id = AuthChecker::getUserId();

      $configDuty = DB::table('duty_assignement')
        ->select('scheme_id', 'district_code', 'urban_body_code', 'taluka_code', 'is_urban', 'mapping_level')
        ->where('user_id', $user_id)
        ->where('is_active', 1)
        ->get();

      $url = ($designation_id_old === 'Verifier') ? $verifierURL : $approverURL;

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
        ->map(function ($scheme) use ($is_cmo, $not_cmo, $designation_id_old, $cmo_url, $verifierURL, $approverURL, $type) {
          if ($type === 1) {
            if (in_array($scheme->id, $is_cmo)) {
              $scheme->url = $designation_id_old === 'Verifier'
                ? $cmo_url
                : $approverURL;
            } elseif (in_array($scheme->id, $not_cmo)) {
              $scheme->url = $designation_id_old === 'Verifier'
                ? $verifierURL
                : $approverURL;
            } else {
              $scheme->url = $designation_id_old === 'Verifier'
                ? $verifierURL
                : $approverURL;
            }
          } elseif ($type === 2) {
            if (in_array($scheme->id, $not_cmo)) {
              $scheme->url = $designation_id_old === 'Verifier'
                ? $verifierURL
                : $approverURL;
            } else {
              $scheme->url = $designation_id_old === 'Verifier'
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
      $auth = AuthChecker::VerifierChecker();
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
        $designation_id_old = Auth::user()->designation_id_old;
        if ($designation_id_old != 'Verifier') {
          return redirect('/')->with('error', 'Not Allowed...');
        }
        $user_id = Auth::user()->id;
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
    $auth = AuthChecker::ApproverChecker();
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
    $designation_id_old = Auth::user()->designation_id_old;
    dd($scheme_id, $designation_id_old);
    if ($designation_id_old == 'Operator') {
      return redirect('/')->with('error', 'Not Allowded...');
    }
  }


  public function verifierdata(Request $request)
  {
    try {
      $table_name = 'pension.beneficiaries';
      $user_id = Auth::user()->id;
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
          $action = '&nbsp; &nbsp;<a href="' . route('processApplicationDetailsCommon', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';

          if ($data->scheme_id == 17 || $data->scheme_id == 10 || $data->scheme_id == 11 || $data->scheme_id == 2) {
            // $action = $action . '&nbsp; &nbsp;<a href="application-edit?id=' . $data->id . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-warning" target="_blank"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
          }
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
      $user_id = Auth::user()->id;
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

      $workflow = Workflow::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $workflow_id = $workflow->workflow_step_id;
      $parent = DB::table('public.m_scheme_step_rank')
        ->where('id', $workflow_id)
        ->select('parent_id')
        ->first();
      $role_id = $parent ? $parent->parent_id : null;
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
          $action = '<a href="' . route('processApplicationDetailsCommon', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
          if ($data->scheme_id == 17 || $data->scheme_id == 10 || $data->scheme_id == 11 || $data->scheme_id == 2) {
            $action = $action . '<a href="application-edit?id=' . $data->id . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-warning" target="_blank"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
          }
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
    $approveBtnvisible = 1;
    $verifyBtnvisible = 1;
    $user_id = Auth::user()->id;
    $designation_id = Auth::user()->designation_id_old;
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
    //dd($is_dup_msg);
    // dd($approveBtnVisible);
    return view('JBProcessApplication.pension_view_common', [
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
      $accept_reject_model->op_type = class_basename(request()->route()->getAction()['controller']);
      $accept_reject_model->ip_address = request()->ip();
      $workflow = Workflow::where('scheme_id', $scheme_id)->where('designation_id', Auth::user()->designation_id)->first();
      $workflow_id = $workflow->workflow_step_id;
      $parent = DB::table('public.m_scheme_step_rank')
        ->where('id', $workflow_id)
        ->select('parent_id')
        ->first();
      $next_level_role_id = $parent ? $parent->parent_id : null;

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
        $accept_reject_model->op_type = 'AV';


        $input = [
          'is_verified' => 1,
          'next_level_role_id' => $next_level_role_id,
          'comments' => $comments,
          'verification_date' => $c_time,
          'verified_by' => $user_id
        ];

        DB::beginTransaction();

        $is_status_updated = DB::table($schema . '.beneficiaries')->where('id', $id)->where('created_by_dist_code', $created_by_dist_code)->whereraw(" (dup_bank=0 or dup_bank IS NULL) and (dup_aadhar=0 or dup_aadhar IS NULL) and (dup_mobile=0 or dup_mobile IS NULL) and (no_aadhar=0 or no_aadhar IS NULL) and (no_mobile=0 or no_mobile IS NULL)")->whereNotNull('bank_code')->whereNull('next_level_role_id')->update($input);

        $is_saved_log = $accept_reject_model->save();
        //dd($is_status_updated);
        if ($is_status_updated && $is_saved_log) {
          DB::commit();
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Forwarded Succesfully!');
        } else {
          DB::rollback();
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
        }
      } else if ($_POST['submit'] == 'Revert') {

        $accept_reject_model->op_type = 'AREVERT';


        $input = ['next_level_role_id' => NULL, 'is_verified' => 0, 'is_approved' => 0, 'is_reverted' => 1];

        DB::beginTransaction();

        $is_status_updated = DB::table($schema . '.beneficiaries')->where('id', $id)->where('created_by_dist_code', $created_by_dist_code)->update($input);

        $is_saved_log = $accept_reject_model->save();
        //dd($is_status_updated);
        if ($is_status_updated && $is_saved_log) {
          DB::commit();
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Reverted Succesfully!');
        } else {
          DB::rollback();
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
        }
      } else if ($_POST['submit'] == 'Reject') {
        $is_state_login = 0;
        try {
          $accept_reject_model->op_type = 'AR';
          $input = [
            'verification_rejected' => $Rejected,
            'comments' => $comments,
            'next_level_role_id' => -1,
            'is_approved' => 2,
            'is_verified' => 2,
            'is_rejected' => 1,
            'rejected_date' => $c_time,
            'rejected_by' => $user_id,
            'is_clean' => 10
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
            return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Rejected Succesfully!');
          } else {
            DB::rollback();
            return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
          }
        } catch (\Exception $e) {
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
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
      $user_id = Auth::user()->id;
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
      $accept_reject_model->ip_address = request()->ip();
      $user_id = Auth::user()->id;
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

      $workflow = Workflow::where('scheme_id', $scheme_id)
        ->where('designation_id', Auth::user()->designation_id)
        ->first();
      $workflow_id = $workflow->workflow_step_id;
      $parent = DB::table('public.m_scheme_step_rank')->where('id', $workflow_id)->select('parent_id')->first();

      $next_level_role_id = $parent ? $parent->parent_id : null;

      $w_id = DB::select("SELECT id FROM public.m_scheme_step_rank WHERE id = $workflow_id");
      $w_id = $w_id ? $w_id[0]->id : null;

      $row = DB::table($table_name)->where('id', '=', $id)->where('next_level_role_id', '=', $w_id)->first();


      if (empty($row)) {
        return redirect("/")->with('danger', 'Application id Not Found');
      }

      if ($_POST['submit'] == 'Approve') {

        $accept_reject_model->op_type = 'AA';
        if ($scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {
          return redirect("/")->with('error', 'Approval temporary suspended.');
        }
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
          $input = ['is_approved' => 1, 'next_level_role_id' => $next_level_role_id, 'comments' => $comments, 'payment_start_date' => $payment_start_date, 'approval_date' => $c_time, 'approved_by' => $user_id, 'wp_phase' => 2];
        } else
          $input = ['is_approved' => 1, 'next_level_role_id' => $next_level_role_id, 'comments' => $comments, 'payment_start_date' => $payment_start_date, 'approval_date' => $c_time, 'approved_by' => $user_id];
        $appPrefix = "App";

        DB::beginTransaction();

        if ($scheme_id == 10) {
          $is_status_updated = DB::table($table_name)->where('id', $id)->where('created_by_dist_code', $district_code)->whereNull('is_lb_imported')->whereNotNull('bank_code')->whereraw(" (sm_flag=1 or ds_phase=8 or ds_phase=9 or (ds_phase=10 or sm_ds_mark_ix=1))")->update($input);
        } else
          $is_status_updated = DB::table($table_name)->where('id', $id)->where('created_by_dist_code', $district_code)->whereNotNull('bank_code')->update($input);

        $is_saved_log = $accept_reject_model->save();
        if ($is_status_updated && $is_saved_log) {
          DB::commit();
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Approved Succesfully!');
        } else {
          DB::rollback();
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
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
          'is_clean' => 10
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
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Rejected Succesfully!');
        } else {
          DB::rollback();
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
        }
      } else if ($_POST['submit'] == 'Revert') {
        $accept_reject_model->op_type = 'AE';
        $input = [
          'approval_rejected' => 3,
          'comments' => $comments,
          'next_level_role_id' => NULL,
          'is_verified' => 0,
          'is_approved' => 0,
          'is_reverted' => 1
        ];
        $appPrefix = "App";
        DB::beginTransaction();
        $is_status_updated = DB::table($table_name)->where('id', $id)->where('created_by_dist_code', $district_code)->update($input);
        $is_saved_log = $accept_reject_model->save();
        if ($is_status_updated && $is_saved_log) {
          DB::commit();
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->withInput()->with('message', 'Beneficiary with ID:' . $id . ' Reverted Succesfully!');
        } else {
          DB::rollback();
          return redirect('workflow?pr1=' . $scheme_obj->pr1_code)->with('message', 'Error! Please try again.');
        }
      }
    }
  }
}
