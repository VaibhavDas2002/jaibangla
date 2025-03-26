<?php

namespace App\Http\Controllers;

use App\BenPaymentDetails;
use Illuminate\Http\Request;

use App\District;
use App\Scheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Configduty;
use App\RejectRevertReason;
use App\Taluka;
use App\UrbanBody;
use App\Ward;
use App\GP;
use App\BenDocs;
use App\Helpers\AuthChecker;
use App\SchemeStepRank;
use App\BenEntry;
use App\AcceptRejectInfo;
use Illuminate\Support\Facades\Route;
use App\BenReviveRequest;
use Illuminate\Support\Facades\Validator;


class RejectRevivalController extends Controller
{

  protected $doc_type_id;
  public function __construct()
  {
    $this->doc_type_id = 10;


  }
  public function shemeSelection(Request $request)
  {
    return redirect("/")->with('danger', 'Not Allowed');
    try {
      $user_id = Auth::user()->id;
      if (AuthChecker::ApproverPermission()) {
        $schemes = DB::select(DB::raw("select id,scheme_name,display_name,is_active from m_scheme where  id IN (1,3,10) and  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
        // dd($schemes);
        return view(
          'RejectRevival/SchemeSelection',
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
    return redirect("/")->with('danger', 'Not Allowed');
    $this->middleware('auth');
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

    $type_des = 'Rejected LB60 Beneficiary List';

    //dd($type_des);
    $district_code = $duty_obj->district_code;
    $urban_bodys = collect([]);
    $gps = collect([]);
    $district_list_obj = collect([]);





    if (request()->ajax()) {
      $rural_urban_code = $request->rural_urban_code;
      $created_by_local_body_code = $request->created_by_local_body_code;
      $limit = $request->input('length');
      $offset = $request->input('start');
      //dd($process_type);

      $condition = array();
      $condition['is_lb_imported'] = 1;
      $condition['created_by_dist_code'] = $district_code;
      $condition['is_rejected'] = 1;
      if (!empty($rural_urban_code)) {
        $condition["rural_urban_id"] = $rural_urban_code;
      }
      if (!empty($created_by_local_body_code)) {
        $condition["created_by_local_body_code"] = $created_by_local_body_code;
      }
      $query = DB::connection('pgsql_mis')->table('pension.beneficiaries')->where('scheme_id', $scheme_id)->where('next_level_role_id', '<', 0)->where($condition);





      $serachvalue = $request->search['value'];
      if (empty($serachvalue)) {
        $totalRecords = $query->count();
        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
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
          'scheme_id'
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
              'scheme_id'
            ]
          );
        } else {
          $query = $query->where(function ($query1) use ($serachvalue) {
            $query1->where('ben_name', 'like', $serachvalue . '%')
              ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
              ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
              ->orWhere('last_ifsc', 'like', $serachvalue . '%');
          });
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
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
              'scheme_id'
            ]
          );
        }
        $filterRecords = count($data);
      }
      return datatables()->of($data)->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        // ->addColumn('application_id', function ($data) use ($scheme_id, $scheme_length, $id_length) {

        //   $app_id ='';

        //   return $app_id;
        // })
        ->addColumn('view', function ($data) use ($scheme_id) {
          $action = '';
          $action = '<a href="Viewrejectrevival?id=' . $data->id . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> View</a>';
          return $action;
        })
        ->addColumn('id', function ($data) {
          return $data->id;
        })
        ->addColumn('name', function ($data) {
          // return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
          return $data->ben_full_name;
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
        ->rawColumns(['view', 'id', 'name', 'bank_ifsc', 'bank_code'])
        ->make(true);
    }

    return view(
      'RejectRevival.linelisting',
      [
        'scheme_id' => $scheme_id,
        'scheme_name' => $scheme_obj->scheme_name,
        'district_code' => $district_code,
        'type_des' => $type_des,
      ]
    );
  }
  public function View(Request $request)
  {
    return redirect("/")->with('danger', 'Not Allowed');
    $auth = AuthChecker::ApproverPermission();
    if ($auth) {
      try {

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

        $is_approver = AuthChecker::ApproverPermission();
        $is_hod = AuthChecker::HODChecker();
        $user_id = Auth::user()->id;
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
        if (empty($duty_obj)) {
          return redirect("/")->with('danger', 'Not Allowed.');
        }
        if (!AuthChecker::ApproverPermission()) {
          return redirect("/")->with('danger', 'Not Allowed.');
        }
        $district_code = $duty_obj->district_code;
        $condition['is_lb_imported'] = 1;
        $condition['created_by_dist_code'] = $district_code;
        $condition['is_rejected'] = 1;
        $query = DB::connection('pgsql_mis')->table('pension.beneficiaries')->where('scheme_id', $scheme_id)->where('id', $request->id)->where('next_level_role_id', '<', 0)->where($condition);
        $row = $query->first();
        if (empty($row)) {
          return redirect("/")->with('danger', 'Not Allowed');
        }

        $row->app_id = $row->lb_application_id;

        $reject_revert_cause_list = RejectRevertReason::where('status', true)->where('type', 2)->get();
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






        $docs = BenDocs::where('scheme_id', $scheme_id)->where('created_by_dist_code', $district_code)->where('beneficiary_id', $request->id)->get();

        $can_revive = 1;


        return view(
          'RejectRevival.View60lbapplication',
          [
            'scheme_id' => $scheme_id,
            'row' => $row,
            'district_name' => $district_name,
            'block_name' => $block_name,
            'gp_name' => $gp_name,
            'docs' => $docs,
            'reject_revert_cause_list' => $reject_revert_cause_list,
            'is_approver' => $is_approver,
            'can_revive' => $can_revive,

          ]
        );
      } catch (\Exception $e) {
        //dd($e);
        return redirect("/")->with('danger', 'Error');
      }
    } else {
      return redirect("/")->with('error', 'Not Allowed');
    }

  }


  public function revivepost(Request $request)
  {
    return redirect("/")->with('danger', 'Not Allowed');

    $auth = AuthChecker::ApproverPermission();
    if ($auth) {
      try {
        $user_id = Auth::user()->id;
        $ben_id = $request->id;
        $scheme_id = $request->scheme_id;
        $action_type = $request->action_type;
        $remarks = $request->remarks;
        $revival = (int) $request->reject_revert_cause;
        $remarks = $request->remarks;
        $rules = [];
        $attributes = [];
        $filedRules = [
          'reject_revert_cause' => 'required',
          'remarks' => 'required',
        ];
        $filedAttributes = [
          'reject_revert_cause' => 'Revive Cause',
          'remarks' => 'Remarks',
        ];
        $rules = array_merge($rules, $filedRules);
        $attributes = array_merge($attributes, $filedAttributes);
        $messages = [
          'required' => 'The :attribute field is required.',
          'max' => 'Total :max characters allowed for :attribute.',
          'digits' => 'The :attribute must be exactly :digits digits.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if (!$validator->passes()) {
          // dd($validator->errors()->all());
          return redirect()->route('Viewrejectrevival', ['id' => $ben_id, 'scheme_id' => $scheme_id])
            ->withErrors($validator->errors()->all())
            ->withInput();

        } else {

          $reject_casuse = RejectRevertReason::where('status', true)->where('type', 2)->where('id', $revival)->get();

          $c_time = date('Y-m-d H:i:s', time());
          $next_level_role_id_verifier = SchemeStepRank::getSchemeParentId($scheme_id, 1);
          $benEntryModel = BenEntry::where('id', $ben_id)->where('scheme_id', $scheme_id)->first();
          $benPaymentDetails = BenPaymentDetails::where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->first();
          if ($benPaymentDetails) {
            $ben_status = $benPaymentDetails->ben_status;
          }else{
            $ben_status = null;
          }
          if (empty($benEntryModel)) {
            return redirect("/")->with('error', 'Beneficiary Not Found');
          } else {
            $migrated_status = $benEntryModel->migrated_to_payment;
            DB::connection('pgsql')->beginTransaction();
            DB::connection('pgsql_paywrite')->beginTransaction();

            $benEntryModel->is_rejected = 0;
            $benEntryModel->is_approved = 0;
            $benEntryModel->is_verified = 0;
            $benEntryModel->is_reverted = 0;
            $benEntryModel->next_level_role_id = $next_level_role_id_verifier;
            $benEntryModel->is_clean = 1;
            $benEntryModel->action_cause_id = trim($request->reject_revert_cause);
            $benEntryModel->action_remark = trim($request->remarks);
            $is_ben_update = $benEntryModel->save();

            $accept_reject_model = new AcceptRejectInfo;
            $accept_reject_model->created_at = $c_time;
            $accept_reject_model->application_id = $request->id;
            $accept_reject_model->scheme_id = $scheme_id;
            $accept_reject_model->user_id = $user_id;
            $accept_reject_model->ip_address = request()->ip();
            $accept_reject_model->op_type = 'LB60REVIVE';
            $accept_reject_model->remarks = trim($request->remarks);
            $accept_reject_model->rejected_reverted_cause = $reject_casuse[0]->reason;
            $accept_reject_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod() . '@' . 'TJOHARV';
            $is_saved_log = $accept_reject_model->save();

            $benReviveRequest = new BenReviveRequest;
            $benReviveRequest->ben_id = $ben_id;
            $benReviveRequest->scheme_id = $scheme_id;
            $benReviveRequest->revived_by = $user_id;
            $benReviveRequest->migrated_status = $migrated_status;
            $benReviveRequest->payment_ben_status = $ben_status;
            $benReviveRequest->revive_cause = trim($request->reject_revert_cause);
            $benReviveRequest->remarks = trim($request->remarks);
            $is_reveive_saved = $benReviveRequest->save();

            if ($benPaymentDetails) {
              $benPaymentDetails->ben_status = 6;
              $benPaymentDetails->is_eligible = true;
              $benPaymentDetails->is_rejected = 0;
              $benPaymentDetails->rejected_at = null;
              $is_payment_update = $benPaymentDetails->save();
            } else {
              $is_payment_update = true;
            }

            if ($is_reveive_saved && $is_saved_log && $is_ben_update && $is_payment_update) {
              DB::connection('pgsql')->commit();
              DB::connection('pgsql_paywrite')->commit();
              return redirect()->route('rejectrevivallist',['scheme_id' => $scheme_id])->with('success', 'Beneficiary(' . $ben_id . ') Revived Successfully and Send for Verification Process');

            } else {
              DB::connection('pgsql')->rollBack();
              DB::connection('pgsql_paywrite')->rollBack();
              return redirect()->route('rejectrevivallist',[ 'scheme_id' => $scheme_id])->with('error', 'Error in Reviving Beneficiary');
            }
          }

        }
      } catch (\Exception $e) {
        dd($e);
      }
    } else {
      return redirect("/")->with('error', 'Not Allowed');
    }
  }


}
