<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\BeneficiaryPensions;
use App\District;
use App\Configduty;
use App\Helpers\AuthChecker;
use App\Scheme;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;




class DeactivatedWorkFlowController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    date_default_timezone_set('Asia/Kolkata');
  }
  private function getSchemaName($scheme_id)
  {
    if (!is_null($scheme_id)) {
      $sObj = Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
      //$parameter['scheme_id'] = $scheme_id;
      $schema_name = $sObj->short_code;
      //dd($schema_name);
      if (empty($schema_name)) {
        $schema_name = 'pension';
      }
      $table_name = strtolower($schema_name) . '.beneficiaries';
    } else {
      $table_name = 'pension.beneficiaries';
    }
    return $table_name;
  }

  /*
    Fist landing page at approver end
  */
  public function indexApprove(Request $request)
  {
    if (AuthChecker::ApproverChecker()) {
      $is_active = 1;
    } else {
      $is_active = 0;
    }
    if ($is_active == 0) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    $user_id = AuthChecker::getUserId();
    $mapObj = DB::table('public.duty_assignement')->where('user_id', $user_id)->where('is_active', 1)->first();
    $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . " and scheme_id in(2,10,11) )"));
    if (AuthChecker::ApproverChecker()) {
      if (count($scheme) > 0) {
        return view('update-ben-details/approve_index', [
          'schemes' => $scheme,
          'dist_code' => $mapObj->district_code
        ]);
      } else {
        return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
      }
    } else {
      return redirect("/")->with('success', 'UnAuthorized');
    }
  }

  /*
    Loading datatables data
  */
  public function linelistingApproveStoppedBen(Request $request)
  {
    if ($request->ajax()) {
      // dd('ok');
      if (empty($request->scheme_id) || empty($request->select_type)) {
        $data = collect([]);
        return datatables()->of($data)->make(true);
      }

      $scheme_id = $request->scheme_id;
      if (empty($scheme_id)) {
        // return redirect("/")->with('error', 'Scheme Not Valid');
        return $response = array(
          'status' => 0,
          'msg' => array("Scheme Not Valid."),
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }
      if (!ctype_digit($scheme_id)) {
        // return redirect("/")->with('error', 'Scheme Not Valid');
        return $response = array(
          'status' => 0,
          'msg' => array("Scheme Not Valid."),
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }
      $user_id = AuthChecker::getUserId();
      $is_approver = AuthChecker::ApproverChecker();

      $errormsg = Config::get('constants.errormsg');
      $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
            $district_code = NULL;
      $urban_body_code = NULL;
      $mapping_level = NULL;
      $role_id = NULL;
      foreach ($roleArray as $roleObj) {
        if ($roleObj['scheme_id'] == $scheme_id) {
          $is_active = 1;
          $is_urban = $roleObj['is_urban'];
          $district_code = $roleObj['district_code'];
          $mapping_level = $roleObj['mapping_level'];
          $role_id = $roleObj['id'];
          if ($roleObj['is_urban'] == 1) {
            $urban_body_code = $roleObj['urban_body_code'];
          } else {
            $urban_body_code = $roleObj['taluka_code'];
          }
          break;
        }
      }
      if (AuthChecker::ApproverChecker()) {
        $is_active = 1;
      } else {
        $is_active = 0;
      }
      if ($is_active == 0 || (empty($district_code))) {
        return $response = array(
          'status' => 0,
          'msg' => array("User Disabled."),
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }

      $scheme_row = Scheme::where('id', $scheme_id)->first();
      $table_name = 'pension.beneficiaries';
      $limit = $request->input('length');
      $offset = $request->input('start');
      $query = DB::connection('pgsql')->table($table_name)
        ->where('scheme_id', $scheme_id)
        ->where('created_by_dist_code', $district_code);

      if (!empty($request->block) && $request->block != '') {
        $query = $query->where('created_by_local_body_code', $request->block);
      }
      if (!empty($request->urban_code) && $request->urban_code != '') {
        $query = $query->where('rural_urban_id', $request->urban_code);
      }
      if ($request->select_type == 1) {
        $query = $query->where('next_level_role_id', 0)->where('next_level_stop_payment', 1);
      } else if ($request->select_type == 2) {
        $query = $query->where('next_level_role_id', -99)->where('next_level_stop_payment', 2);
      }

      $serachvalue = $request->search['value'];

      if (empty($serachvalue)) {
        $totalRecords = $query->count();
        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
        $totalRecords = count($data);
      } else {
        if (is_numeric($serachvalue)) {
          $query = $query->where(function ($query1) use ($serachvalue) {
            $query1->where('id', $serachvalue);
          });
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
        }
        $totalRecords = count($data);
      }
      $totalRecords = $query->count();
      $filterRecords = count($data);

      return datatables()->of($data)
        ->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('view', function ($data) use ($request, $is_approver) {
          if ($data->next_level_stop_payment == 1) {
            if ($is_approver) {
              $action = '<button class="btn btn-primary btn-xs ben_view_button" value="' . $data->id . '_' . $data->scheme_id . '_' . $request->select_type . '"><i class="glyphicon glyphicon-edit"></i> View</button>';
            }
            return $action;
          } else if ($data->next_level_stop_payment == 2) {
            return 'Verified and Approved';
          } else {
            return null;
          }
        })
        ->addColumn('check', function ($data) use ($request) {
          if ($data->next_level_stop_payment == 1) {
            return '<input type="checkbox"  name="chkbx" class="all_checkbox" onclick="controlCheckBox();" value="' . $data->id . '_' . $data->scheme_id . '_' . $request->select_type . '">';
          } else {
            return null;
          }
        })
        ->addColumn('id', function ($data) {
          return $data->id;
        })
        ->addColumn('aadhar_mask', function ($data) {
          $mask_aadhar = '';
          $aadhar = trim($data->aadhar_no);
          if (strlen($aadhar) >= 12 && strlen($aadhar) != '') {
            $mask_aadhar = '********' . substr($aadhar, 8, 4);
          } else {
            $mask_aadhar = $aadhar;
          }
          return $mask_aadhar;
        })
        ->addColumn('mobile_mask', function ($data) {
          $mask_mobile = '';
          $mobile = trim($data->mobile_no);
          return $mobile;
        })
        ->addColumn('name', function ($data) {
          return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
        })
        ->rawColumns(['view', 'id', 'name', 'aadhar_mask', 'mobile', 'check'])
        ->make(true);
    }
  }

  /*
    Fetching data for modal view
  */
  public function modalViewApproveStopPayment(Request $request)
  {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $id = $request->benid;
      $scheme_id = $request->scheme_id;
      $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
            $district_code = NULL;
      $urban_body_code = NULL;
      $mapping_level = NULL;
      $role_id = NULL;
      foreach ($roleArray as $roleObj) {
        if ($roleObj['scheme_id'] == $scheme_id) {
          $is_active = 1;
          $is_urban = $roleObj['is_urban'];
          $district_code = $roleObj['district_code'];
          $mapping_level = $roleObj['mapping_level'];
          $role_id = $roleObj['id'];
          if ($roleObj['is_urban'] == 1) {
            $urban_body_code = $roleObj['urban_body_code'];
          } else {
            $urban_body_code = $roleObj['taluka_code'];
          }
          break;
        }
      }
      if (AuthChecker::ApproverChecker()) {
        $is_active = 1;
      } else {
        $is_active = 0;
      }
      if ($is_active == 0 || (empty($district_code))) {
        return $response = array(
          'status' => 0,
          'msg' => array("User Disabled."),
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }

      $scheme_row = Scheme::where('id', $scheme_id)->first();
      $table_name = 'pension.beneficiaries';
      $docs = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_arch')->where('beneficiary_id', $id)->where('document_type', '>=', 100)->first();
      $extension = $docs->document_extension;
      $base64 = $docs->attched_document;
      $imageName = $docs->doc_type_name;


      $ben_details = DB::table($table_name)
        ->where('next_level_stop_payment', 1)->where('next_level_role_id', 0)
        ->where('scheme_id', $scheme_id)
        ->where('created_by_dist_code', $district_code)
        ->where('id', $id)
        ->first();

      // print_r($ben_details);die;
      if ($ben_details == null) {
        return $response = array(
          'status' => 1,
          'msg' => 'Somethimg went wrong.',
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      } else {
        $mask_aadhar = '';
        $aadhar = trim($ben_details->aadhar_no);
        if (strlen($aadhar) >= 12 && strlen($aadhar) != '') {
          $mask_aadhar = '********' . substr($aadhar, 8, 4);
        } else {
          $mask_aadhar = $aadhar;
        }
        $mask_mobile = '';
        $mobile = trim($ben_details->mobile_no);
        if (strlen($mobile) >= 10 && strlen($mobile) != '') {
          $mask_mobile = '******' . substr($mobile, 6, 4);
        } else {
          $mask_mobile = $mobile;
        }
        $ben_arr = array(
          'ben_name' => trim($ben_details->ben_fname) . ' ' . trim($ben_details->ben_mname) . ' ' . trim($ben_details->ben_lname),
          'id' => $ben_details->id,
          'scheme_id' => $ben_details->scheme_id,
          'father_name' => trim($ben_details->father_fname) . ' ' . trim($ben_details->father_mname) . ' ' . trim($ben_details->father_lname),
          'caste' => trim($ben_details->caste),
          'gender' => trim($ben_details->gender),
          'dob' => date('d-m-Y', strtotime($ben_details->dob)),
          'bank_code' => trim($ben_details->bank_code),
          'bank_ifsc' => trim($ben_details->bank_ifsc),
          'branch_name' => trim($ben_details->branch_name),
          'bank_name' => trim($ben_details->bank_name),
          'mobile_no' => trim($ben_details->mobile_no),
          'application_id' => $ben_details->created_by_dist_code . str_pad($ben_details->scheme_id, 2, 0, STR_PAD_LEFT) . str_pad($ben_details->id, 15, 0, STR_PAD_LEFT),
          'aadhar_no' => trim($ben_details->aadhar_no)
        );
        $response = [
          'ben_arr' => $ben_arr,
          'image_name' => $imageName,
          'extension' => $extension,
          'base64' => $base64

        ];
      }
    } catch (\Exception $e) {
      //dd($e);
      $response = array(
        'exception' => true,
        // 'exception_message' => $e->getMessage(),
        'exception_message' => 'Somethimg went wrong. May be session timeout, please logout and login again.',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }

  /*
    Final Approve section at approver end  
  */
  public function approveStopPaymentData(Request $request)
  {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
            $user_id = AuthChecker::getUserId();
      $designation_id = Auth::user()->designation_id;
      $errormsg = Config::get('constants.errormsg');
      $duty = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
      if ($duty->isEmpty) {
        return $response = array(
          'status' => 0,
          'msg' => array("Unauthorized."),
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }

      if (AuthChecker::ApproverChecker()) {
        $is_bulk = $request->is_bulk;
        if ($is_bulk == 1) {
          $fg_is_bulk = 1;
        } else {
          $fg_is_bulk = 0;
        }
      } else {
        return $response = array(
          'status' => 0,
          'msg' => array("Unauthorized."),
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }
      $remarks = trim($request->accept_reject_comments);
      //   dd($remarks);
      $operation_type = $request->opreation_type;
      $bulk_id = $request->applicantId;
      $single_id = $request->single_app_id;


      $benIdArr = array();
      $schemeIdArr = array();
      $updateTypeArr = array();
      if ($fg_is_bulk == 1) {
        $bulk_id_arr = explode(',', $bulk_id);

        foreach ($bulk_id_arr as $key => $value) {
          $tempArr = explode('_', $value);
          array_push($benIdArr, $tempArr[0]);

          array_push($schemeIdArr, $tempArr[1]);
          array_push($updateTypeArr, $tempArr[2]);
        }
      } else {
        $tempArr = explode('_', $single_id);
        array_push($benIdArr, $tempArr[0]);
        array_push($schemeIdArr, $tempArr[1]);

        array_push($updateTypeArr, $tempArr[2]);
      }

      $benIdArr = array_unique($benIdArr);
      $schemeIdArr = array_unique($schemeIdArr);
      $updateTypeArr = array_unique($updateTypeArr);

      //   if (count($schemeIdArr) != 1) {
      //     return $response = array(
      //       'status' => 0, 'msg' => array("Something went wrong in the scheme."),
      //       'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
      //     );
      //   }
      $scheme_id = implode("", $schemeIdArr);
      $update_type = implode("", $updateTypeArr); // 1 => pending
      $table_name = 'pension.beneficiaries';
      $input_json = [];
      $input_json['stop_payment_reason'] = $remarks;
      $input_json[$table_name . '.next_level_role_id'] = '-99';
      $input_json['designation'] = $designation_id;


      $is_active = 0;
      $district_code = NULL;
      foreach ($roleArray as $roleObj) {
        if ($roleObj['scheme_id'] == $scheme_id) {
          $is_active = 1;
          $district_code = $roleObj['district_code'];
          break;
        }
      }
      if ($is_active == 0 || (empty($district_code))) {
        // return redirect("/")->with('error', 'User Disabled. ');
        return $response = array(
          'status' => 0,
          'msg' => array("User Disabled."),
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }
      if ($operation_type == 'A') {
        $update_code = 30;
      } else if ($operation_type == 'B') {
        $update_code = 300;
      }
      $updateLogTable = array();
      if (count($benIdArr) > 0) {
        foreach ($benIdArr as $item) {
          $insertData = array(
            'original_application_id' => $item,
            'dist_code' => $district_code,
            'scheme_id' => $scheme_id,
            'user_id' => $user_id,
            'created_at' => date('Y-m-d H:i:s'),
            'remarks' => $remarks,
            'update_code' => $update_code,
            'new_data' => json_encode($input_json),
            'action_by' => $user_id,
            'action_ip_address' => $request->ip(),
            'action_type' => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod()

          );
          array_push($updateLogTable, $insertData);
        }
      }

      // Update Beneficiary Details
      $msg = 'De-activated Successfully';
      if ($operation_type == 'A') {
        if ($update_type == 1) {
          $updateBenDetailsData = [
            'next_level_role_id' => -99,
            'next_level_stop_payment' => 2,
            'is_approved' => 2,
            'is_verified' => 2,
            'is_rejected' => 1,
            'action_by' => $user_id,
            'action_ip_address' => $request->ip(),
            'action_type' => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod()
          ];
          $msg = 'Beneficiary De-activated Successfully';
        }
      }
      if ($operation_type == 'B') {
        $updateBenDetailsData = [
          'next_level_stop_payment' => NULL,
          'action_by' => $user_id,
          'action_ip_address' => $request->ip(),
          'action_type' => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod()
        ];
        $msg = 'Beneficiary De-activated Request Successfully Reverted';
      }
      DB::beginTransaction();
      DB::connection('pgsql_encwrite')->beginTransaction();
      DB::connection('pgsql_paywrite')->beginTransaction();
      try {

        if ($update_type == 1) {
          $lotTableInsert = UpdateBenDetails::insert($updateLogTable);
          if ($lotTableInsert == 1) {
            DB::table($table_name)->where('scheme_id', $scheme_id)->whereIn('id', $benIdArr)->where('next_level_stop_payment', 1)->where('created_by_dist_code', $district_code)->update($updateBenDetailsData);
            if ($operation_type == 'A') {
              $ben_details = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->whereIN('ben_id', $benIdArr)->where('scheme_id', $scheme_id)->get();
              $benIdArr = implode(',', $benIdArr);
              // dd($benIdArr);
              if ($ben_details) {
                $final_update = DB::connection('pgsql_paywrite')->select("Select payment.reject_update_bank(in_ben_id => ARRAY[" . $benIdArr . "], in_scheme_id => " . $scheme_id . ", in_rejected =>12)");
              }
            }
            DB::commit();
            DB::connection('pgsql_encwrite')->commit();
            DB::connection('pgsql_paywrite')->commit();
            $response = array(
              'status' => 1,
              'msg' => $msg,
              'type' => 'green',
              'icon' => 'fa fa-check',
              'title' => 'Success'
            );
          } else {
            DB::rollback();
            DB::connection('pgsql_encwrite')->rollback();
            DB::connection('pgsql_paywrite')->rollback();
            $response = array(
              'status' => 0,
              'msg' => array("" . "Somethimg went wrong.."),
              'type' => 'green',
              'icon' => 'fa fa-check',
              'title' => 'Success'
            );
          }
        }
      } catch (\Exception $e) {
        dd($e);
        DB::rollback();
        DB::connection('pgsql_encwrite')->rollback();
        DB::connection('pgsql_paywrite')->rollback();
        $return_status = 0;
        $return_text = 'Error. Please try again';
        $return_msg = array("" . $return_text);
        return $response = array(
          'status' => $return_status,
          'msg' => $return_msg,
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }
    } catch (\Exception $e) {
      //  dd($e);
      $response = array(
        'exception' => true,
        // 'exception_message' => $e->getMessage(),
        'exception_message' => 'Somethimg went wrong. May be session timeout, please logout and login again.',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }
}
