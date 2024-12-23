<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\District;
use App\Scheme;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Configduty;
use App\RejectRevertReason;
use App\Taluka;
use App\SchemeDocMap;

use App\UrbanBody;
use App\Ward;
use App\GP;
use App\AcceptRejectInfo;
use App\BenDocs;
use App\Helpers\AuthChecker;

class WorkflowLppController extends Controller
{
 private $scheme_id;

  private $source_type;
  private $ben_status;
  private $doc_type_id;

  public function __construct()
  {

    $this->scheme_id = 20;
    $this->source_type = 'ss_nfsa';
    $this->ben_status = -97;
    $this->doc_type_id = 6;

  }
  public function schemeSelect(Request $request)
  {
    try {
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = AuthChecker::getUserId();
      if ($designation_id_old == 'HOD') {
        $schemes = DB::select(DB::raw("select id,scheme_name,display_name,is_active from m_scheme where id IN (8,9) and   id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
        //dd($schemes);
        return view(
          'workflowlpp/schemeSelection',
          [

            'scheme_list' => $schemes,
          ]
        );
      } else {
        return redirect("/")->with('danger', 'Not Allowed');
      }
    } catch (\Exception $e) {
      // dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function list(Request $request)
  {
    $this->middleware('auth');
    $designation_id_old = Auth::user()->designation_id_old;
    //dd($designation_id_old);
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
    if ($designation_id_old != 'HOD') {
      return redirect("/")->with('danger', 'Not Allowed');
    }

    $type_des = 'Applications Yet to Approved';


    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    if (request()->ajax()) {
      $limit = $request->input('length');
      $offset = $request->input('start');
      //dd($process_type);
      $query = DB::table($schema . '.beneficiaries')->whereNull('next_level_role_id')->whereNull('is_reverted')->whereNotNull('entry_datetime');
      $district_code = $request->district_code;
      if (!empty($district_code)) {
        $query = $query->where('created_by_dist_code', $district_code);

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
          'mobile_no'
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
              'mobile_no'
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
              'mobile_no'
            ]
          );
        }
        $filterRecords = count($data);
      }
      return datatables()->of($data)->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('view', function ($data) use ($scheme_id) {
          $action = '<a href="ViewSmLpp?id=' . $data->id . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> View</a>';

          return $action;
        })->addColumn('id', function ($data) {
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
    $district_list = District::get();
    return view(
      'workflowlpp.linelisting',
      [
        'designation_id_old' => $designation_id_old,
        'scheme_id' => $scheme_id,
        'scheme_name' => $scheme_obj->scheme_name,
        'type_des' => $type_des,
        'scheme_id' => $scheme_id,
        'district_list' => $district_list
      ]
    );
  }
  public function ViewSmLpp(Request $request)
  {
    //dd('ok');
    try {
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
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

      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();

      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      // dd($id);
      $type_des = 'Applicant Details';
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";

      }
      // dd($schema);
      $query = DB::table($schema . '.beneficiaries')->where('id', $id)->whereNull('next_level_role_id');
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
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $row->created_by_dist_code)->orderBy('document_type')->get();
      return view(
        'workflowlpp.ViewBeneficiary',
        [
          'designation_id_old' => $designation_id_old,
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
      // dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function forward(Request $request)
  {
    try {

      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = AuthChecker::getUserId();
      if (empty($request->beneficiary_id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }

      if (empty($request->scheme_id)) {
        return redirect("/")->with('danger', 'Scheme ID Not Found');
      }
      $scheme_id = $request->scheme_id;
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed...');
      }
      $id = $request->beneficiary_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";

      }

      $condition = array();
      $condition['id'] = $id;

      $query = DB::table($schema . '.beneficiary')
        ->where($condition)->where('id', $id)->whereNull('next_level_role_id');

      $row = $query->first();
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $is_error = 0;



      if ($is_error == 0) {
        $c_time = date('Y-m-d H:i:s', time());
        if ($request->action_type == 'Reject') {
          $op_type = 'AR';
          $inputMain = array();
          $inputMain['next_level_role_id'] = -1;
          $inputMain['is_approved'] = 2;
          $inputMain['is_verified'] = 2;
          $inputMain['is_rejected'] = 1;
          $inputMain['rejected_date'] = $c_time;
          $inputMain['rejected_by'] = $user_id;
          $inputMain['is_clean'] = 10;
          $op_type = 'AA';
          $msg = "Rejected";
        } else if ($request->action_type == 'Revert') {
          $op_type = 'AE';
          $inputMain = array();
          $inputMain['next_level_role_id'] = NULL;
          $inputMain['is_reverted'] = 1;
          $inputMain['is_verified'] = 0;
          $inputMain['is_approved'] = 0;
          $msg = "Reverted";
        } else if ($request->action_type == 'Approve') {
          $inputMain = array();
          $inputMain['next_level_role_id'] = 0;
          $inputMain['is_approved'] = 1;
          $inputMain['approval_date'] = $c_time;
          $inputMain['approved_by'] = $user_id;
          $op_type = 'AA';
          $msg = "Approved";
        } else {
          return redirect("/")->with('danger', 'Not Allowed');
        }

        DB::beginTransaction();





        $upadated_main = DB::table($schema . '.beneficiary')
          ->where(['id' => $id])->whereNull('next_level_role_id')->update($inputMain);

        $modelNameAcceptReject = new AcceptRejectInfo;
   
        $modelNameAcceptReject->scheme_id = $scheme_id;

        $modelNameAcceptReject->created_at = $c_time;
        $modelNameAcceptReject->op_type = class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod();
        $modelNameAcceptReject->application_id = $id;
        $modelNameAcceptReject->user_id = $user_id;
        $modelNameAcceptReject->ip_address = request()->ip();
        $is_accept_reject = $modelNameAcceptReject->save();
        //dump($upadated_main);dump($is_accept_reject);dump($enc_status);dd($is_inserted_arch);
        if ($upadated_main && $is_accept_reject) {
          DB::commit();
          $errors = array();
          $return_text = 'Beneficiary with  Id:' . $id . ' has been sucessfully ' . $msg;
          if ($request->action_type == 'Approve') {
            return redirect("workflowlpp?scheme_id=" . $scheme_id)->with('success', $return_text);

          } else {
            $errors = array();
            $errorMsg = 'Beneficiary with  Id:' . $id . ' has been sucessfully ' . $msg;
            array_push($errors, $errorMsg);
          }

        } else {
          DB::rollback();
          $errors = array();
          $errorMsg = 'Error.. Please try different.';
          array_push($errors, $errorMsg);
        }
      }


      if (count($errors) > 0) {
        return redirect("/workflowlpp?scheme_id=" . $scheme_id)->with('errors', $errors);
      }
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }

  }




}
