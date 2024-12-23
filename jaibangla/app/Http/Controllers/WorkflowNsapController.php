<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Configduty;
use App\MapLavel;
use App\District;
use App\Taluka;
use App\Ward;
use App\UrbanBody;
use App\GP;
use Auth;
use DB;
use App\Helpers\Helper;
use App\SubDistrict;
use Carbon\Carbon;
use Config;
use App\BlkUrbanlEntryMapping;
use App\RejectRevertReason;
use App\AcceptRejectInfo;
use App\Scheme;
use App\DocumentType;
use Validator;
use App\Helpers\AuthChecker;

class WorkflowNsapController extends Controller
{
  private $base_dob_chk_date;
  public function __construct()
  {
    $this->middleware('auth');
    $this->base_dob_chk_date = date('Y-m-d');
  }

  public function shemeSelectionnsapmarked(Request $request)
  {
    $designation_id_old = Auth::user()->designation_id_old;
    $user_id = AuthChecker::getUserId();
    if ($designation_id_old == 'Verifier') {
      $schemes = DB::select(DB::raw("select id,scheme_name,display_name,is_active from m_scheme where id  IN (11) and  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
      //dd($schemes);
      return view(
        'workflow-nsap/VerificationOptionMarked',
        [

          'scheme_list' => $schemes,
        ]
      );
    } else {
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function showApplicantDetails(Request $request)
  {
    return redirect('/')->with('error', 'Not Allowded');
    $designation_id_old = Auth::user()->designation_id_old;
    //dd($designation_id_old);
    $user_id = AuthChecker::getUserId();
    if ($designation_id_old == 'Verifier' || $designation_id_old == 'Approver') {
      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/scheme-selection-nsap-marked")->with('error', 'Scheme Not Valid');
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
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      $query = DB::table($schema . '.beneficiary')
        ->where('created_by_dist_code', $district_code)
        ->where('id', $request->id);
      if ($designation_id_old == 'Verifier') {
        $query = $query->whereNull('next_level_role_id');
      } else if ($designation_id_old == 'Approver') {
        $query = $query->where('is_verified',1)->where('is_approved',0)->where('is_rejected',0);
      }
      $row = $query->first();
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $app_id = $row->created_by_dist_code . substr('0' . $row->scheme_id, -$scheme_length) . substr('0000000' . $row->id, -$id_length);
      $row->app_id = $app_id;
      $docs = DB::table($schema . '.ben_docs')
        ->where('ben_id', $request->id)->get();
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
      $doc_profile_image = DocumentType::get()->where("is_profile_pic", true)->first();
      $doc_profile_image_id = 999;
      if ($doc_profile_image) {
        $doc_profile_image_id = $doc_profile_image->id;
      }
      return view(
        'workflow-nsap.pension_view_details_nsap',
        [
          'scheme_id' => $scheme_id,
          'row' => $row,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'docs' => $docs,
          'image_id' => $doc_profile_image_id,
          'reject_revert_cause_list' => $reject_revert_cause_list,
          'designation_id_old' => $designation_id_old
        ]
      );
    } else {
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function nsap_marked_list(Request $request)
  {
    return redirect('/')->with('error', 'Not Allowded');
    $designation_id_old = Auth::user()->designation_id_old;
    $user_id = AuthChecker::getUserId();
    if ($designation_id_old == 'Verifier' || $designation_id_old == 'Approver') {
      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/scheme-selection-nsap-marked")->with('error', 'Scheme Not Valid');
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
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      if ($duty_obj->mapping_level == "Subdiv") {
        $urban_body_code = $duty_obj->urban_body_code;
        $urban_bodys = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();
        $urban_body_codes = [];
        $i = 0;
        foreach ($urban_bodys as $urban_body) {

          $urban_body_codes[$i] = $urban_body->urban_body_code;
          $i++;
        }
        if (request()->ajax()) {
          $limit = $request->input('length');
          $offset = $request->input('start');
          $query = DB::table($schema . '.beneficiary')
            ->where('created_by_local_body_code', $urban_body_code)
            ->where('created_by_dist_code', $district_code)
            ->whereraw("((next_level_role_id IS NULL and process_nsap_flag IS NULL) or (is_verified=1 and is_approved=0 and is_rejected=0 and process_nsap_flag=1 ) or (next_level_role_id IS NULL and nsap_flag  IN (3,4) and process_nsap_flag=1) or (nsap_flag=5 and next_level_role_id=-500 and process_nsap_flag=1 ) or (nsap_flag IN (11,12)))");
          if (!empty($request->block_ulb_code)) {
            $query = $query->where('block_ulb_code', $request->block_ulb_code);
          }
          if (!empty($request->gp_ward_code)) {
            $query = $query->where('gp_ward_code', $request->gp_ward_code);
          }
          if (!empty($request->filter_status)) {
            if ($request->filter_status == 1)
              $query = $query->whereNull('next_level_role_id')->whereNull('process_nsap_flag')->whereNull('is_reverted');
            if ($request->filter_status == 2)
              $query = $query->where('is_verified',1)->where('is_approved',0)->where('is_rejected',0)->where('process_nsap_flag', 1);
            if ($request->filter_status == 3)
              $query = $query->whereNull('next_level_role_id')->where('nsap_flag', 3)->where('process_nsap_flag', 1);

            if ($request->filter_status == 5)
              $query = $query->where('nsap_flag', 5)->where('next_level_role_id', -500)->where('process_nsap_flag', 1);
            if ($request->filter_status == 6)
              $query = $query->whereNull('next_level_role_id')->where('nsap_flag', 12)->where('is_reverted',1);
            if ($request->filter_status == 7)
              $query = $query->whereNull('next_level_role_id')->where('nsap_flag', 11)->where('is_reverted',1);
          }
          $serachvalue = $request->search['value'];
          if (empty($serachvalue)) {
            $totalRecords = $query->count();
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'process_nsap_flag', 'nsap_flag','is_reverted'
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
                  'id', 'created_by_dist_code', 'dob', 'assembly_name',
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
                  'ben_lname', 'gender', 'ben_age', 'ben_mname', 'process_nsap_flag', 'nsap_flag','is_reverted'
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
                  'ben_lname', 'gender', 'ben_age', 'ben_mname', 'process_nsap_flag', 'nsap_flag','is_reverted'
                ]
              );
            }
            $filterRecords = count($data);
          }
          return datatables()->of($data)->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
            ->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

              $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

              return $app_id;
            })->addColumn('view', function ($data) {
              if (is_null($data->process_nsap_flag) && is_null($data->is_reverted)) {
                // $action = 'Mark as Secc Pending for Action';
                $action = '<a href="' . route('showapplicantnsap', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Action</a>&nbsp; &nbsp;<a href="' . route('pensionform.application-edit-view', ['id' => $data->id, 'scheme_id' => $data->scheme_id, 'is_nsap' => 1]) . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Update</a>';
              } else if ($data->next_level_role_id > 0 && $data->process_nsap_flag == 1 && $data->nsap_flag == 2) {
                $action = '<span class="badge badge-danger">Verified ..Waiting for Approval</span>';
              } else if (is_null($data->next_level_role_id) && $data->process_nsap_flag == 1 && $data->nsap_flag == 3) {
                $action = '<span class="badge badge-danger">NSAP Marked</span>';
              } else if ($data->process_nsap_flag == 1 && $data->next_level_role_id == -500 && $data->nsap_flag == 5) {
                $action = '<span class="badge badge-danger">Rejected</span>';
              } else if ($data->process_nsap_flag == 1 && $data->next_level_role_id == 0 && !is_null($data->next_level_role_id)) {
                $action = '<span class="badge badge-danger">Approved</span>';
              }else if (is_null($data->next_level_role_id) && $data->is_reverted==1 && $data->nsap_flag == 11) {
                $action = '<span class="badge badge-danger">Reverted to Operator</span>';
              }
              else if (is_null($data->next_level_role_id) && $data->is_reverted==1 && $data->nsap_flag == 12) {
                // $action = 'Mark as Secc Pending for Action';
                $action = '<a href="' . route('showapplicantnsap', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Action</a>&nbsp; &nbsp;<a href="' . route('pensionform.application-edit-view', ['id' => $data->id, 'scheme_id' => $data->scheme_id, 'is_nsap' => 1]) . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Update</a>';
              }



              return $action;
            })
            ->addColumn('id', function ($data) {
              return $data->id;
            })->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

              $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

              return $app_id;
            })
            ->addColumn('name', function ($data) {
              return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
            })
            ->rawColumns(['view', 'id', 'name'])
            ->make(true);
        }
        return view(
          'workflow-nsap.linelisting_marked_subdiv',
          [
            'scheme_id' => $scheme_id,
            'urban_bodys' => $urban_bodys,
            'district_code' => $district_code
          ]
        );
      }
      if ($duty_obj->mapping_level == "Block") {
        $taluka_code = $duty_obj->taluka_code;
        $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
        if (request()->ajax()) {
          $limit = $request->input('length');
          $offset = $request->input('start');
          $query = DB::table($schema . '.beneficiary')
            ->where('created_by_local_body_code', $taluka_code)
            ->where('created_by_dist_code', $district_code)
            ->whereraw("((next_level_role_id IS NULL and process_nsap_flag IS NULL) or (is_verified=1 and is_approved=0 and is_rejected=0 and process_nsap_flag=1 ) or (next_level_role_id IS NULL and nsap_flag  IN (3,4) and process_nsap_flag=1) or (nsap_flag=5 and next_level_role_id=-500 and process_nsap_flag=1 ) or (nsap_flag IN (11,12) ) )");
          if (!empty($request->gp_code)) {
            $query = $query->where('gp_ward_code', $request->gp_code);
          }
          if (!empty($request->filter_status)) {
            if ($request->filter_status == 1)
              $query = $query->whereNull('next_level_role_id')->whereNull('process_nsap_flag')->whereNull('is_reverted');
            if ($request->filter_status == 2)
              $query = $query->where('is_verified',1)->where('is_approved',0)->where('is_rejected',0)->where('process_nsap_flag', 1);
            if ($request->filter_status == 3)
              $query = $query->whereNull('next_level_role_id')->where('nsap_flag', 3)->where('process_nsap_flag', 1);

            if ($request->filter_status == 5)
              $query = $query->where('nsap_flag', 5)->where('next_level_role_id', -500)->where('process_nsap_flag', 1);
            if ($request->filter_status == 6)
              $query = $query->whereNull('next_level_role_id')->where('nsap_flag', 12)->where('is_reverted',1);
            if ($request->filter_status == 7)
              $query = $query->whereNull('next_level_role_id')->where('nsap_flag', 11)->where('is_reverted',1);
          }
          $serachvalue = $request->search['value'];
          if (empty($serachvalue)) {
            $totalRecords = $query->count();
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'process_nsap_flag', 'nsap_flag','is_reverted','is_verified', 'is_approved', 'is_rejected'
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
                  'id', 'created_by_dist_code', 'dob', 'assembly_name',
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
                  'ben_lname', 'gender', 'ben_age', 'ben_mname', 'process_nsap_flag', 'nsap_flag','is_reverted','is_verified', 'is_approved', 'is_rejected'
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
                  'ben_lname', 'gender', 'ben_age', 'ben_mname', 'process_nsap_flag', 'nsap_flag','is_reverted','is_verified', 'is_approved', 'is_rejected'
                ]
              );
            }
            $filterRecords = count($data);
          }
          return datatables()->of($data)->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
            ->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

              $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

              return $app_id;
            })->addColumn('view', function ($data) {
              if (is_null($data->process_nsap_flag) && is_null($data->is_reverted)) {
                // $action = 'Mark as Secc Pending for Action';
                $action = '<a href="' . route('showapplicantnsap', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Action</a>&nbsp; &nbsp;<a href="' . route('pensionform.application-edit-view', ['id' => $data->id, 'scheme_id' => $data->scheme_id, 'is_nsap' => 1]) . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Update</a>';
              } else if ($data->is_verified==1 && $data->is_approved==0 && $data->is_rejected==0 && $data->process_nsap_flag == 1 && $data->nsap_flag == 2) {
                $action = '<span class="badge badge-danger">Verified ..Waiting for Approval</span>';
              } else if (is_null($data->next_level_role_id) && $data->process_nsap_flag == 1 && $data->nsap_flag == 3) {
                $action = '<span class="badge badge-danger">NSAP Marked</span>';
              } else if ($data->process_nsap_flag == 1 && $data->next_level_role_id == -500 && $data->nsap_flag == 5) {
                $action = '<span class="badge badge-danger">Rejected</span>';
              } else if ($data->process_nsap_flag == 1 && $data->next_level_role_id == 0 && !is_null($data->next_level_role_id)) {
                $action = '<span class="badge badge-danger">Approved</span>';
              }
              else if (is_null($data->next_level_role_id) && $data->is_reverted==1 && $data->nsap_flag == 11) {
                $action = '<span class="badge badge-danger">Reverted to Operator</span>';
              }
              else if (is_null($data->next_level_role_id) && $data->is_reverted==1 && $data->nsap_flag == 12) {
                // $action = 'Mark as Secc Pending for Action';
                $action = '<a href="' . route('showapplicantnsap', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Action</a>&nbsp; &nbsp;<a href="' . route('pensionform.application-edit-view', ['id' => $data->id, 'scheme_id' => $data->scheme_id, 'is_nsap' => 1]) . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Update</a>';
              }


              return $action;
            })
            ->addColumn('id', function ($data) {
              return $data->id;
            })->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

              $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

              return $app_id;
            })
            ->addColumn('name', function ($data) {
              return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
            })
            ->rawColumns(['view', 'id', 'name'])
            ->make(true);
        }
        return view(
          'workflow-nsap.linelisting_marked',
          [
            'scheme_id' => $scheme_id,
            'gps' => $gps,
            'district_code' => $district_code
          ]
        );
      }
      if ($duty_obj->mapping_level == "District") {
        $allowded_arr_cnt = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('district_code',  $district_code)->where('main_approval',  TRUE)->count();
        //  $district_list_obj = District::get();
        if ($allowded_arr_cnt == 0) {
          return redirect("/")->with('danger', 'Approval is temporarily suspended');
        }
        if (request()->ajax()) {
          $limit = $request->input('length');
          $offset = $request->input('start');
          $query = DB::table($schema . '.beneficiary')
            ->where('created_by_dist_code', $district_code);
          if (!empty($request->created_by_local_body_code)) {
            $query = $query->where('created_by_local_body_code', $request->created_by_local_body_code);
          }
          if (!empty($request->filter_status)) {
            if ($request->filter_status == 1)
              $query = $query->where('next_level_role_id',48);
            if ($request->filter_status == 2)
              $query = $query->where('next_level_role_id', 0);
            if ($request->filter_status == 3)
              $query = $query->whereNull('next_level_role_id')->where('nsap_flag', 3)->where('process_nsap_flag', 1);

            if ($request->filter_status == 5)
              $query = $query->where('is_rejected', 1);
            if ($request->filter_status == 6)
             $query = $query->whereNull('next_level_role_id')->where('nsap_flag', 12);

          }
          $serachvalue = $request->search['value'];
          if (empty($serachvalue)) {
            $totalRecords = $query->count();
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'process_nsap_flag', 'nsap_flag','is_reverted','is_verified', 'is_approved', 'is_rejected'
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
                  'id', 'created_by_dist_code', 'dob', 'assembly_name',
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
                  'ben_lname', 'gender', 'ben_age', 'ben_mname', 'process_nsap_flag', 'nsap_flag','is_reverted','is_verified', 'is_approved', 'is_rejected'
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
                  'ben_lname', 'gender', 'ben_age', 'ben_mname', 'process_nsap_flag', 'nsap_flag','is_reverted','is_verified', 'is_approved', 'is_rejected'
                ]
              );
            }
            $filterRecords = count($data);
          }
          return datatables()->of($data)->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
            ->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

              $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

              return $app_id;
            })->addColumn('view', function ($data) {
              if ($data->next_level_role_id ==48 && $data->nsap_flag!=12) {
                // $action = 'Mark as Secc Pending for Action';
                $action = '<a href="' . route('showapplicantnsap', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Action</a>';
              } else if ($data->next_level_role_id = 0) {
                $action = '<span class="badge badge-danger">Verified and Approved</span>';
              } else if (is_null($data->next_level_role_id) && $data->process_nsap_flag == 1 && $data->nsap_flag == 3) {
                $action = '<span class="badge badge-danger">NSAP Marked</span>';
              } else if ($data->is_rejected==1) {
                $action = '<span class="badge badge-danger">Rejected</span>';
              } else if ($data->is_reverted==1 && $data->nsap_flag == 12) {
                $action = '<span class="badge badge-danger">Reverted to Block/SubDivision</span>';
              }
              else {
                                $action = '';
              }




              return $action;
            })
            ->addColumn('id', function ($data) {
              return $data->id;
            })->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

              $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

              return $app_id;
            })
            ->addColumn('name', function ($data) {
              return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
            })->addColumn('check', function ($data) {
              if ($data->next_level_role_id == 0) {
                return '';
              } else if ($data->next_level_role_id < 0) {
                return '';
              } else if (is_null($data->next_level_role_id)) {
                return '';
              } else if ($data->next_level_role_id ==48) {


                return '<input type="checkbox" name="approvalcheck[]" onchange="controlCheckBox()" value="' . $data->id . '">';
              }
            })
            ->rawColumns(['view', 'id', 'name', 'check'])
            ->make(true);
        }
        return view(
          'workflow-nsap.linelisting_approved',
          [
            'scheme_id' => $scheme_id,
            'district_code' => $district_code
          ]
        );
      }
    } else {
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function verifydata(Request $request)
  {
    return redirect('/')->with('error', 'Not Allowded');
    $designation_id_old = Auth::user()->designation_id_old;
    $user_id = AuthChecker::getUserId();
    if ($designation_id_old == 'Verifier' || $designation_id_old == 'Approver') {
      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/scheme-selection-nsap-marked")->with('error', 'Scheme Not Valid');
      }
      if ($scheme_id != 11) {
        return redirect("/scheme-selection-nsap-marked")->with('error', 'Scheme Not Valid');
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
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      $query = DB::table($schema . '.beneficiary')
        ->where('created_by_dist_code', $district_code)->where('id', $request->id);
      if ($designation_id_old == 'Verifier') {
        $query =  $query->whereNull('next_level_role_id');
      } else if ($designation_id_old == 'Approver') {
        $query = $query->where('next_level_role_id', 48);
      }
      $row = $query->first();
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $c_time = date('Y-m-d H:i:s', time());
      $comments = trim($request->comments);
      $accept_reject_model = new AcceptRejectInfo;
      $accept_reject_model->created_at = $c_time;
      $accept_reject_model->application_id = $request->id;
      $accept_reject_model->scheme_id = $scheme_id;
      $accept_reject_model->user_id = $user_id;
      $accept_reject_model->comment_message = $comments;
      $accept_reject_model->created_by_dist_code = $district_code;
      $accept_reject_model->created_by_local_body_code = $row->created_by_local_body_code;
      $accept_reject_model->ip_address = request()->ip();
      $accept_reject_model->op_type = class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod();

      $back_page = $request->basePage;

      $back_url = 'nsap-marked-list?scheme_id=' . $scheme_id;

      if ($request->action_type == 'NSAP Marked') {
        $is_reverted = trim($request->is_reverted);
        $nsap_rhs_id = trim($request->nsap_rhs_id);
        $nsap_member_id = trim($request->nsap_member_id);
        $input = [
          'process_nsap_flag' => 1, 'nsap_flag' => 3, 'comments' => $comments
        ];
        if (!empty($nsap_rhs_id)) {
          if (!is_numeric($nsap_rhs_id)) {
            return redirect("/nsap-marked-list?scheme_id=" . $scheme_id)->with('error', 'Please Enter Last 4 digit of RHS ID');
          }
          $input['nsap_rhs_id'] = $nsap_rhs_id;
        }
        if (!empty($nsap_member_id)) {
          if (!is_numeric($nsap_member_id)) {
            return redirect("/nsap-marked-list?scheme_id=" . $scheme_id)->with('error', 'Please Enter Member Id');
          }
          $input['nsap_member_id'] = $nsap_member_id;
        }


        DB::beginTransaction();
        $accept_reject_model->op_type = 'NM';
        if($is_reverted==12){
          $update = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)
          ->whereNull('next_level_role_id')->where('id', $request->id)->update($input);
        }
        else
        $update = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)
          ->whereNull('next_level_role_id')->whereNull('process_nsap_flag')->where('id', $request->id)->update($input);
        //dd($update);
        $is_saved_log = $accept_reject_model->save();
        if ($update && $is_saved_log) {
          DB::commit();
          return redirect($back_url)->with('message', 'Application with Id ' . $request->id . ' has been marked as NSAP Succesfully!');
        } else {
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
      } else if ($request->action_type == 'Verify') {
        //dd('ok');
        $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name', $designation_id_old)->where('stack_level', $duty_obj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->first();
        if (empty($mapArr)) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        $scheme_capacity_arr = Helper::getCapacity($scheme_id, $district_code);
        if ($scheme_capacity_arr['visible'] == 1) {
          if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
            $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
            return redirect("/")->with('danger', $errorMsgCap);
          }
        }
        $is_reverted = trim($request->is_reverted);
        //dd($is_reverted);
        DB::beginTransaction();
        $accept_reject_model->op_type = 'SV';
        $input = [
          'verification_date' => $c_time, 'verified_by' => $user_id, 'next_level_role_id' => $mapArr->parent_id, 'process_nsap_flag' => 1, 'nsap_flag' => 2, 'comments' => $comments
        ];
        if($is_reverted==12){
          $update = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)
          ->whereNull('next_level_role_id')->where('id', $request->id)->update($input);
        }
        else
        $update = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)
          ->whereNull('next_level_role_id')->whereNull('process_nsap_flag')->where('id', $request->id)->update($input);
        //dd($update);
        $is_saved_log = $accept_reject_model->save();
        if ($update && $is_saved_log) {
          DB::commit();
          return redirect($back_url)->with('message', 'Application with Id ' . $request->id . ' has been Verified Succesfully!');
        } else {
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
      } else if ($request->action_type == 'Approve') {
        //dd('ok');
        $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name', $designation_id_old)->where('stack_level', $duty_obj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->first();
        if (empty($mapArr)) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        $allowded_arr_cnt = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('district_code',  $district_code)->where('main_approval',  TRUE)->count();
        //  $district_list_obj = District::get();
        if ($allowded_arr_cnt == 0) {
          return redirect("/")->with('danger', 'Approval is temporarily suspended');
        }
        $in_pension_id = 'ARRAY[' . "'$request->id'" . ']';
        try {
          DB::beginTransaction();
          $is_inserted_status_arr = DB::select("select ".$schema.".approve_data_bulk(in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'SA', in_custom_comment => '".$comments."')");
          //dd($is_inserted_status_arr);
          $is_inserted_status=$is_inserted_status_arr[0]->approve_data_bulk;
          //dd($is_inserted_status);
          if($is_inserted_status==10){
            DB::rollback();
            $errorMsgCap = "Total no. of Approved applications  exceeds the quota";
            return redirect("/")->with('danger', $errorMsgCap);

        }
        else if($is_inserted_status==1){
          DB::commit();
          return redirect($back_url)->with('message', 'Application with Id ' . $request->id . ' has been Approved Succesfully!');
        }
        else{
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
        }catch (\Exception $e) {
          //dd($e);
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
        
        
      } else if ($request->action_type == 'Reject') {

        $next_level_role_id_reject = -500;

        DB::beginTransaction();
        $accept_reject_model->op_type = 'SR';
        $input = [
          'process_nsap_flag' => 1, 'nsap_flag' => 5, 'comments' => $comments, 'next_level_role_id' => $next_level_role_id_reject,
          'rejected_date' => $c_time, 'rejected_by' => $user_id,'is_rejected' => 1,'is_approved' => 2,'is_verified' => 2,'is_clean' => 10
        ];
        $update = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)
          ->where('id', $request->id)->update($input);
        $is_saved_log = $accept_reject_model->save();
        $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
        if (in_array($scheme_id, $scheme_dedup_list)) {
        $free_pending_bank_duplicate_arr = DB::select("select ".$schema.".free_pending_bank_duplicate_data(in_scheme_id => ".$scheme_id.", in_district_code => ".$district_code.")");
                //dd($free_pending_bank_duplicate_arr);
        $free_pending_bank_duplicate_data=$free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
        if(!empty(trim($row->mobile_no))){
          $sp_mobile=$row->mobile_no;
      }
      else{
          $sp_mobile=0;  
      }
        $reject_dup_adjustment_arr = DB::select("select ".$schema.".reject_dup_adjustment(
          in_old_bank_ifsc => '".$row->bank_ifsc."', 
          in_old_bank_code => '".$row->bank_code."', 
          in_old_aadhar_no => '".$row->aadhar_no."', 
          in_old_mobile_no => ".$sp_mobile."
          )");
          $reject_dup_adjustment=$reject_dup_adjustment_arr[0]->reject_dup_adjustment;
        }
        else{
          $reject_dup_adjustment=1;
          $free_pending_bank_duplicate_data=1;
        }
        if ($update && $is_saved_log && $free_pending_bank_duplicate_data && $reject_dup_adjustment) {
          DB::commit();
          return redirect($back_url)->with('message', 'Application with Id ' . $request->id . ' has been Rejected Succesfully!');
        } else {
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
      }
      else if ($request->action_type == 'Revert') {
      
        $input = [
          'comments' => $comments, 'is_reverted' => 1
        ];
        if($designation_id_old=='Verifier'){
          $input['next_level_role_id']=NULL;
          $input['process_nsap_flag']=NULL;
          $input['nsap_flag']=11;
        }
        else if($designation_id_old=='Approver'){
          $input['next_level_role_id']=NULL;
          $input['process_nsap_flag']=NULL;
          $input['nsap_flag']=12;

        }
        DB::beginTransaction();
        $accept_reject_model->op_type = 'AE';

        $update = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)
         ->where('id', $request->id)->update($input);
        //dd($update);
        $is_saved_log = $accept_reject_model->save();
        if ($update && $is_saved_log) {
          DB::commit();
          return redirect($back_url)->with('message', 'Application with Id ' . $request->id . ' has been Reverted Succesfully!');
        } else {
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
      }
    } else {
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function bulkapprove(Request $request)
  {
    return redirect('/')->with('error', 'Not Allowded');
    $designation_id_old = Auth::user()->designation_id_old;
    $user_id = AuthChecker::getUserId();
    if ($designation_id_old == 'Approver') {
      //dd('ok');
      $scheme_id = $request->scheme_id;
      // dd($scheme_id);
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if ($scheme_id != 11) {
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
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      $allowded_arr_cnt = BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('district_code',  $district_code)->where('main_approval',  TRUE)->count();
      //  $district_list_obj = District::get();
      if ($allowded_arr_cnt == 0) {
        return redirect("/")->with('danger', 'Approval is temporarily suspended');
      }
      $back_url = 'nsap-marked-list?scheme_id=' . $scheme_id;
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
        $is_inserted_status_arr = DB::select("select ".$schema.".approve_data_bulk(in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'SA', in_custom_comment => '".$comments."')");
          //dd($is_inserted_status_arr);
        $is_inserted_status=$is_inserted_status_arr[0]->approve_data_bulk;
        if($is_inserted_status==10){
          DB::rollback();
          $errorMsgCap = "Total no. of Approved applications  exceeds the quota";
          return redirect("/")->with('danger', $errorMsgCap);

      }
      else if($is_inserted_status==1){
        DB::commit();
        return redirect($back_url)->with('message', 'Applications  has been Approved Succesfully!');
      } else{
        DB::rollback();
        return redirect($back_url)->with('error', 'Error! Please try again.');
      }
      }catch (\Exception $e) {
        //dd($e);
        DB::rollback();
        return redirect($back_url)->with('error', 'Error! Please try again.');
      }
      
    } else {
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function generate_excel(Request $request)
  {
    $designation_id_old = Auth::user()->designation_id_old;
    $user_id = AuthChecker::getUserId();
    if (empty($request->scheme_id)) {
      return redirect('/')->with('error', 'Scheme Id Required');
    }
    if (!ctype_digit($request->scheme_id)) {
      return redirect('/')->with('error', 'Scheme Id Invalid');
    }

    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $request->scheme_id)->first();
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $district_code = $duty_obj->district_code;
    if ($duty_obj->mapping_level == "Block") {
      $created_by_local_body_code = $duty_obj->taluka_code;
    }
    if ($duty_obj->mapping_level == "Subdiv") {
      $created_by_local_body_code = $duty_obj->urban_body_code;
    }
    $condition = array();
    if ($designation_id_old == 'Approver') {
      $condition["created_by_dist_code"] = $district_code;
    }
    if ($designation_id_old == 'Verifier' || $designation_id_old == 'Operator') {
      $condition["created_by_dist_code"] = $district_code;
      $condition["created_by_local_body_code"] = $created_by_local_body_code;
    }
    $scheme_name_row = Scheme::where('id', $request->scheme_id)->first();
    $scheme_name = $scheme_name_row->scheme_name;
    $report_type_name = 'SECC Marked Application List';

    $scheme_length = NULL;
    $id_length = NULL;
    if (!empty($request->scheme_id)) {
      $scheme_row = Scheme::where('id', $request->scheme_id)->first();
      //dd($scheme_row->toArray());
      if (!empty($scheme_row)) {
        $scheme_schema = $scheme_row->short_code;
        if (!empty($scheme_schema)) {
          $table = $scheme_schema;
          // $query = DB::connection('pgsql_mis')->table('' . $table . '.beneficiary')->where($condition);
          // $query = DB::table::on('pgsql_mis')->where($condition);
        } else {
          $table = 'pension';
        }
        $scheme_length =  $scheme_row->scheme_length;
        $id_length = $scheme_row->id_length;
      } else {
        $table = 'pension';
      }
      $query = DB::connection('pgsql_mis')->table('' . $table . '.beneficiary')->where($condition);
      $query = $query->where('is_nsap', 1);
      if (!empty($request->block_ulb_code_excel)) {
        $query = $query->where('block_ulb_code', $request->block_ulb_code_excel);
      }
      if (!empty($request->gp_ward_code_excel)) {
        $query = $query->where('gp_ward_code', $request->gp_ward_code_excel);
      }
      if (!empty($request->filter_status_excel)) {
        if ($request->filter_status_excel == 1)
          $query = $query->whereNull('next_level_role_id');
        if ($request->filter_status_excel == 2)
          $query = $query->where('next_level_role_id', -502);
        if ($request->filter_status_excel == 3)
          $query = $query->where('next_level_role_id', -501);
        if ($request->filter_status_excel == 4)
          $query = $query->where('is_verified',1)->where('is_approved',0)->where('is_rejected',0);
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
        'post_office',
        'pincode',
        'aadhar_no',
        'is_nsap'
      )->orderBy('ben_fname')->orderBy('gp_ward_name')->get();
      // dd($data->toArray());
      $filename = $scheme_name . "-" . $report_type_name . "-" . date('d/m/Y') . '-' . time() . ".xls";
      header("Content-Type: application/xls");
      header("Content-Disposition: attachment; filename=" . $filename);
      header("Pragma: no-cache");
      header("Expires: 0");
      echo '<table border="1">';

      echo '<tr><th>Applicant Id</th><th>Applicant Name</th><th>Applicant Mobile No.</th><th>Applicant DOB.</th><th>Age</th><th>Aadhaar NO.</th><th>Bank IFSC</th><th>Bank Account No.</th><th>Father\'s Name</th><th>Block/Municipality</th><th>GP/WARD</th><th>Village/Town/City</th><th>House Premise No</th><th>Post Office</th><th>PIN Code</th><th>Status</th></tr>';

      if (count($data) > 0) {
        foreach ($data as $row) {
          $app_id = $row->created_by_dist_code . substr('0' . $row->scheme_id, -$scheme_length) . substr('0000000' . $row->id, -$id_length);
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
          $ben_fullname = $ben_fname . " " . $ben_mname . " " . $ben_lname;
          if (!empty($row->mobile_no)) {
            $ben_mobile_no = $row->mobile_no;
          } else {
            $ben_mobile_no = '';
          }
          if (!empty($row->dob)) {
            $ben_dob = $row->dob;
            $ben_age = $this->ageCalculate($row->dob);
          } else {
            $ben_dob = '';
            $ben_age = '';
          }
          if (!empty($row->aadhar_no)) {
            $ben_aadhar_no = '********' . substr(trim($row->aadhar_no), 0, 3);
          } else {
            $ben_aadhar_no = '';
          }
          if (!empty(trim($row->bank_ifsc)))
            $f_bank_ifsc = trim($row->bank_ifsc);
          else
            $f_bank_ifsc = '';
          if (!empty(trim($row->bank_code)))
            $f_bank_code = '********' . substr(trim($row->bank_code), 0, 3);
          else
            $f_bank_code = '';
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

          if (is_null($row->next_level_role_id) && $row->is_nsap == 1) {
            $status = 'Mark as Secc Pending for Action';
          } else if ($row->is_verified==1 && $row->is_approved==0 && $row->is_rejected==0 && $row->is_nsap == 1) {
            $status = 'Verified ..Waiting for Approval';
          } else if ($row->next_level_role_id == -502 && $row->is_nsap == 1) {
            $status = 'NSAP Covered';
          } else if (($row->next_level_role_id == -500 || $row->next_level_role_id == -501) && $row->is_nsap == 1) {
            $status = 'Rejected';
          } else if ($row->next_level_role_id == 0 && $row->is_nsap == 1) {
            $status = 'Approved';
          }



          echo "<tr><td>" . $app_id . "</td><td>" . $ben_fullname . "</td><td>" . $ben_mobile_no . "</td><td>" . $ben_dob . "</td><td>" . $ben_age . "</td><td>" . $ben_aadhar_no . "</td><td>" . $f_bank_ifsc . "</td><td>" . $f_bank_code . "</td><td>" . $father_fullname . "</td><td>" . trim($row->block_ulb_name) . "</td><td>" . trim($row->gp_ward_name) . "</td><td>" . trim($row->village_town_city) . "</td><td>" . trim($row->house_premise_no) . "</td><td>" . trim($row->post_office) . "</td><td>" . trim($row->pincode) . "</td><td>" . $status . "</td></tr>";
        }
      } else {
        echo '<tr><td colspan="11">No Records found</td></tr>';
      }
      echo '</table>';
    } else {
      return redirect('/')->with('error', 'Scheme Id Not Found');
    }
  }
  function nsapMis(Request $request)
  {
    $this->middleware('auth');
    date_default_timezone_set('Asia/Kolkata');
    $c_time = Carbon::now();
    $cp_time = time();
    $c_date = $c_time->format("Y-m-d");
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    $designation_id_old = Auth::user()->designation_id_old;
    $userId = Auth::user()->id;
    $district_visible = $is_urban_visible = $block_visible = 1;
    $municipality_visible = 0;
    $gp_ward_visible = 0;
    $muncList = collect([]);
    $gpList = collect([]);
    if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' ||  $designation_id_old == 'Dashboard' || $designation_id_old == 'MisState' || $designation_id_old == 'DDO') {
      $district_visible = $is_urban_visible = $block_visible = 1;
    } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
      $district_code = NULL;
      $is_urban = NULL;
      $blockCode = NULL;
      foreach ($roleArray as $roleObj) {
        if (in_array($roleObj['scheme_id'], array(11))) {
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
    $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (11) and  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
    //dd($scheme_list);
    return view(
      'workflow-nsap.nsapMis',
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
        'is_urban_visible' => $is_urban_visible,
        'c_date' => $c_date,
        'cp_time' => $cp_time,
        'gpList' => $gpList,
        'muncList' => $muncList
      ]
    );
  }
  public function nsapMisPost(Request $request)
  {
    $scheme_id = $request->scheme_id;
    $district = $request->district;
    $urban_code = $request->urban_code;
    $block = $request->block;

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
      'block' => 'nullable|integer'
    ];
    $data = array();
    $column = "";
    $attributes = array();
    $messages = array();
    $attributes['scheme_id'] = 'Scheme';
    $attributes['district'] = 'District';
    $attributes['urban_code'] = 'Rural/ Urban';
    $attributes['block'] = 'Block/Sub Division';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $scheme_row = Scheme::where('id', $scheme_id)->first();
      $user_msg = "NSAP Mark Mis Report for the Scheme " . $scheme_row->scheme_name;
      $title = $user_msg;
      //dd($title);

      $data = array();
      $return_status = 1;
      $return_msg = '';
      $heading_msg = '';
      $external = 0;
      $external_arr = array();
      $external_filter = array();


      if (!empty($district)) {
        if ($urban_code == 1) {
          $column = "Sub Division";
          $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
          $data = $this->getSubDivWise($scheme_id, $district);
        } else if ($urban_code == 2) {
          $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
          $column = "Block";
          $data = $this->getBlockWise($scheme_id, $district);
        } else {
          $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
          $column = "Block/Sub Division";
          $data1 = $this->getBlockWise($scheme_id, $district);
          $data2 = $this->getSubDivWise($scheme_id, $district);
          $data = array_merge($data1, $data2);
        }
      } else {
        $column = "District";
        $heading_msg = 'District Wise ' . $user_msg;
        $data = $this->getDistrictWise($scheme_id);

        $external = 0;
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
  public function getDistrictWise($scheme_id)
  {
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }

    $query = "select A.location_id,A.location_name,
        COALESCE(C.pending,0) as pending, 
        COALESCE(C.verified,0) as verified,
        COALESCE(C.nsap_mark,0) as nsap_mark,
        COALESCE(C.rejected,0) as rejected,
        COALESCE(C.approved,0) as approved
        from(
        select district_code as location_id,district_name as location_name
         from public.m_district 
         )
         as A  
        LEFT JOIN
        (select
                    count(1) filter(where next_level_role_id IS NULL and process_nsap_flag IS NULL ) as pending,
                    count(1) filter(where is_verified=1 and is_approved=0 and is_rejected=0 and process_nsap_flag=1  and nsap_flag=2) as verified,
                    count(1) filter(where next_level_role_id IS NULL and process_nsap_flag=1 and nsap_flag=3) as nsap_mark,
                    count(1) filter(where next_level_role_id=-500 and process_nsap_flag=1 and nsap_flag=5) as rejected,
                    count(1) filter(where next_level_role_id=0 and process_nsap_flag=1) as approved,
                    created_by_dist_code
                    from " . $schema . ".beneficiary 
         group by created_by_dist_code) as C ON A.location_id=C.created_by_dist_code";

    // echo $query;die;
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getSubDivWise($scheme_id, $district_code = NULL)
  {
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }
    $whereMain = "where  district_code=" . $district_code;

    $query = "select A.location_id,A.location_name,
        COALESCE(C.pending,0) as pending, 
        COALESCE(C.verified,0) as verified,
        COALESCE(C.nsap_mark,0) as nsap_mark,
        COALESCE(C.rejected,0) as rejected,
        COALESCE(C.approved,0) as approved
        from(
            select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
            from public.m_sub_district  " . $whereMain . " 
         )
         as A  
        LEFT JOIN
        (select 
        count(1) filter(where next_level_role_id IS NULL and process_nsap_flag IS NULL ) as pending,
        count(1) filter(where is_verified=1 and is_approved=0 and is_rejected=0 and process_nsap_flag=1  and nsap_flag=2) as verified,
        count(1) filter(where next_level_role_id IS NULL and process_nsap_flag=1 and nsap_flag=3) as nsap_mark,
        count(1) filter(where next_level_role_id=-500 and process_nsap_flag=1 and nsap_flag=5) as rejected,
        count(1) filter(where next_level_role_id=0 and process_nsap_flag=1) as approved,
                    created_by_local_body_code
                    from " . $schema . ".beneficiary   where  created_by_dist_code= " . $district_code . "   
         group by created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getBlockWise($scheme_id, $district_code = NULL)
  {
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }
    $whereMain = "where  district_code=" . $district_code;
    $query = "select A.location_id,A.location_name,
        COALESCE(C.pending,0) as pending, 
        COALESCE(C.verified,0) as verified,
        COALESCE(C.nsap_mark,0) as nsap_mark,
        COALESCE(C.rejected,0) as rejected,
        COALESCE(C.approved,0) as approved
        from(
            select block_code as location_id,'Block-'||block_name as location_name
           from public.m_block  " . $whereMain . " 
         )
         as A  
        LEFT JOIN
        (select   
        count(1) filter(where next_level_role_id IS NULL and process_nsap_flag IS NULL ) as pending,
        count(1) filter(where is_verified=1 and is_approved=0 and is_rejected=0 and process_nsap_flag=1  and nsap_flag=2) as verified,
        count(1) filter(where next_level_role_id IS NULL and process_nsap_flag=1 and nsap_flag=3) as nsap_mark,
        count(1) filter(where next_level_role_id=-500 and process_nsap_flag=1 and nsap_flag=5) as rejected,
        count(1) filter(where next_level_role_id=0 and process_nsap_flag=1) as approved,
                    created_by_local_body_code
                    from " . $schema . ".beneficiary   where   created_by_dist_code= " . $district_code . "   
         group by created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  function ageCalculate($dob)
  {
    $diff = 0;
    if ($dob != '') {
      //$diff = $this->ageCalculate($dob);
      $diff = Carbon::parse($dob)->diffInYears($this->base_dob_chk_date);
    }
    return $diff;
  }
}
