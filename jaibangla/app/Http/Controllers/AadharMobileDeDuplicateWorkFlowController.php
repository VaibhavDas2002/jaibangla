<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scheme;
use App\District;
use App\UrbanBody;
use App\GP;
use App\BeneficiaryPensions;
use App\PensionSc;
use App\PensionSt;
use App\Manabik;
use App\UpdateBenDetails;
use Excel;
use App\Configduty;
use App\DocumentType;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AadharMobileDeDuplicateWorkFlowController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    date_default_timezone_set('Asia/Kolkata');
    set_time_limit(180);
  }
  /*
    Get Schema name using the scheme id
  */
  private function getSchemaName($scheme_id)
  {
    if (!is_null($scheme_id)) {
      $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
      //$parameter['scheme_id'] = $scheme_id;
      $schema_name =  $sObj->short_code;
      //dd($schema_name);
      if (empty($schema_name)) {
        $schema_name = 'pension';
      }
      $table_name =  strtolower($schema_name) . '.beneficiaries';
    } else {
      $table_name =  'pension.beneficiaries';
    }
    return $table_name;
  }
  /*
		Get First Landing Page only shown in the verifier end
  */
  public function index()
  {
    $designation_id_old = Auth::user()->designation_id_old;
    if ($designation_id_old == 'Verifier') {
      $is_active = 1;
    } else {
      $is_active = 0;
    }
    if ($is_active == 0) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    $user_id = Auth::user()->id;
    $mapObj = DB::table('public.duty_assignement')->where('user_id', $user_id)->where('is_active', 1)->first();
    $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . " )"));
    if (Auth::user()->designation_id_old == "Verifier") {
      if (count($scheme) > 0) {
        $municipality_visible = 0;
        $gp_ward_visible = 1;
        $muncList = collect([]);
        $gpList = collect([]);
        if ($mapObj->is_urban == 1) {
          $urban_body_code = $mapObj->urban_body_code;
          $muncList = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();
          $municipality_visible = 1;
          return view('DuplicateAadharUpdate/verifier_index', [
            'schemes' => $scheme,
            'mapLevel' => $mapObj->mapping_level . $designation_id_old,
            'muncList' => $muncList,
            'gpList' => $gpList,
            'rural_urban_fk' => $mapObj->is_urban,
            'municipality_visible' => $municipality_visible,
            'block_munc_corp_code_fk' => $urban_body_code,
            'district_code_fk' => $mapObj->district_code,
            'gp_ward_visible' => $gp_ward_visible
          ]);
        } else {
          $taluka_code = $mapObj->taluka_code;
          $gpList = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
          return view('DuplicateAadharUpdate/verifier_index', [
            'schemes' => $scheme,
            'mapLevel' => $mapObj->mapping_level . $designation_id_old,
            'muncList' => $muncList,
            'gpList' => $gpList,
            'rural_urban_fk' => $mapObj->is_urban,
            'block_munc_corp_code_fk' => $taluka_code,
            'municipality_visible' => $municipality_visible,
            'district_code_fk' => $mapObj->district_code,
            'gp_ward_visible' => $gp_ward_visible
          ]);
        }
      } else {
        return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
      }
    } else {
      return redirect("/")->with('success', 'UnAuthorized');
    }
  }
  /*
    Verification get datas
  */
  public function verifyDeDuplicateAadharMobileGetData(Request $request)
  {
    if ($request->ajax()) {
      $scheme_id = $request->scheme_id;
      if (empty($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      $user_id = Auth::user()->id;
      $designation_id_old = Auth::user()->designation_id_old;
      $errormsg = Config::get('constants.errormsg');
      $roleArray = $request->session()->get('role');
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
      if ($designation_id_old == 'Verifier') {
        $is_active = 1;
      } else {
        $is_active = 0;
      }
      if ($is_active == 0 || (empty($district_code) && empty($urban_body_code)) || empty($mapping_level)) {
        // return redirect("/")->with('error', 'User Disabled. ');
        return $response = array(
          'status' => 0, 'msg' => array("User Disabled."),
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }

      $scheme_row = Scheme::where('id', $scheme_id)->first();
      // Get Dynamic Schema Name scheme wise
      $table_name = 'pension.beneficiaries';

      if ($mapping_level == 'Block') {
//         if($scheme_id==3)
//         {
//           $query = "select * from " . $table_name . " where is_rejected=0 and ((dup_aadhar = 1 and dup_aadhar_edit_role_id = 1) or (dup_mobile = 1 and dup_mobile_edit_role_id = 1)) and scheme_id = " . $scheme_id . " and created_by_dist_code = " . $district_code . " and created_by_local_body_code = " . $urban_body_code . " ";
// dd($query);
//         }
        $query = '';
        $query = "select * from " . $table_name . " where is_rejected=0  and ((dup_aadhar = 1 and dup_aadhar_edit_role_id = 1) or (dup_mobile = 1 and dup_mobile_edit_role_id = 1) OR (no_mobile = 1 and no_mobile_edit_role_id = 1)) and scheme_id = " . $scheme_id . " and created_by_dist_code = " . $district_code . " and created_by_local_body_code = " . $urban_body_code . " ";
        if (!empty($request->gp_ward)) {
          $query .= " and gp_ward_code = " . $request->gp_ward . "";
        }
        $query .= " order by id desc";
        $data = DB::select(DB::raw($query));
      } else if ($mapping_level == 'Subdiv') {
        $query = '';
        $query = "select * from " . $table_name . " where is_rejected=0 and ((dup_aadhar = 1 and dup_aadhar_edit_role_id = 1) or (dup_mobile = 1 and dup_mobile_edit_role_id = 1) OR (no_mobile = 1 and no_mobile_edit_role_id = 1)) and scheme_id = " . $scheme_id . " and created_by_dist_code = " . $district_code . " and created_by_local_body_code = " . $urban_body_code . " ";
        if (!empty($request->muncid)) {
          $query .= " and block_ulb_code = " . $request->muncid . "";
        }
        if (!empty($request->gp_ward)) {
          $query .= " and gp_ward_code = " . $request->gp_ward . "";
        }
        $query .= " order by id desc";
        $data = DB::select(DB::raw($query));
      }

      return datatables()->of($data)
        ->addColumn('view', function ($data) {
          $action = '';
          if($data ->payment_suspended == 1 ){
            $action='<b>Mark due to JNMP</b>';
          }{
            if ($data->dup_aadhar == 1 and $data->dup_aadhar_edit_role_id == 1) {
              $action .= '<button onclick=verifyAadharMobileFunction(' . $data->id . ',' . $data->scheme_id . ',"aadhar") class="btn btn-xs btn-primary" title="Update Aadhar Card"><i class="glyphicon glyphicon-edit"></i> Verify Aadhar</button>';
            }
            if ($data->dup_mobile == 1 and $data->dup_mobile_edit_role_id == 1) {
              $action .= '&nbsp; &nbsp; <button onclick=verifyAadharMobileFunction(' . $data->id . ',' . $data->scheme_id . ',"mobile") class="btn btn-xs btn-info" title="Update Mobile Number"><i class="glyphicon glyphicon-edit"></i> Verify Mobile</button>';
            }
            if ($data->no_mobile == 1 and $data->no_mobile_edit_role_id == 1) {
              $action .= '&nbsp; &nbsp; <button onclick=verifyAadharMobileFunction(' . $data->id . ',' . $data->scheme_id . ',"no_mobile") class="btn btn-xs btn-success" title="Update Mobile Number"><i class="glyphicon glyphicon-edit"></i> Verify No Mobile</button>';
            }
          }
          return $action;
        })
        ->addColumn('id', function ($data) {
          return $data->id;
        })
        ->addColumn('aadhar_no', function ($data) {
          $mask_aadhar = '';
          $aadhar = trim($data->aadhar_no);
          if (strlen($aadhar) >= 12 && strlen($aadhar) != '') {
            $mask_aadhar = '********' . substr($aadhar, 8, 4);
          } else {
            $mask_aadhar = $aadhar;
          }
          return $mask_aadhar;
        })
        ->addColumn('mobile_no', function ($data) {
          // $mask_mobile = '';
          // $mobile = trim($data->mobile_no);
          // if (strlen($mobile) >= 10 && strlen($mobile) != '') {
          //   $mask_aadhar = '******' . substr($mobile, 6, 4);
          // } else {
          //   $mask_aadhar = $mobile;
          // }
          // return $mask_aadhar;
          return $data->mobile_no;
        })
        ->addColumn('name', function ($data) {
          return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
        })
        ->rawColumns(['view', 'id', 'name', 'aadhar_no', 'mobile_no'])
        ->make(true);
    }
  }

  public function getVerifyDuplicateBenModalView(Request $request)
  {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $id = $request->id;
      $scheme_id = $request->scheme_id;
      $designation_id_old = Auth::user()->designation_id_old;
      $roleArray = $request->session()->get('role');
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
      if ($designation_id_old == 'Verifier') {
        $is_active = 1;
      } else {
        $is_active = 0;
      }
      if ($is_active == 0 || (empty($district_code) && empty($urban_body_code)) || empty($mapping_level)) {
        // return redirect("/")->with('error', 'User Disabled. ');
        return $response = array(
          'status' => 0, 'msg' => array("User Disabled."),
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }

      $scheme_row = Scheme::where('id', $scheme_id)->first();
      // Get Dynamic Schema Name scheme wise
      $table_name = $this->getSchemaName($scheme_id);

      $ben_details = DB::table($table_name)->where(function ($query) use ($role_id) {
        $query->where('is_rejected', 0);
      })
        ->where('scheme_id', $scheme_id)
        ->where('created_by_dist_code', $district_code)
        ->where('created_by_local_body_code', $urban_body_code)
        ->where('id', $id)
        ->first();

      $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->where('id', 6)->first();
      if($ben_details->payment_suspended == 1){
        return  $response = array(
          'status' => 1, 'msg' => 'Mark due to JNMP.',
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Information!!'
        );
      }
      // print_r($ben_details);die;
      if ($ben_details == null) {
        return  $response = array(
          'status' => 1, 'msg' => 'Somethimg went wrong.',
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
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
        $mask_mobile = $mobile;
        // if (strlen($mobile) >= 10 && strlen($mobile) != '') {
        //   $mask_mobile = '******' . substr($mobile, 6, 4);
        // } else {
        //   $mask_mobile = $mobile;
        // }
        $ben_arr = array(
          'ben_name' => trim($ben_details->ben_fname) . ' ' . trim($ben_details->ben_mname) . ' ' . trim($ben_details->ben_lname), 'id' => $ben_details->id, 'scheme_id' => $ben_details->scheme_id,
          'father_name' => trim($ben_details->father_fname) . ' ' . trim($ben_details->father_mname) . ' ' . trim($ben_details->father_lname),
          'caste' => trim($ben_details->caste), 'gender' => trim($ben_details->gender),
          'dob' => date('d-m-Y', strtotime($ben_details->dob)),
          'bank_code' => trim($ben_details->bank_code), 'bank_ifsc' => trim($ben_details->bank_ifsc),
          'branch_name' => trim($ben_details->branch_name), 'bank_name' => trim($ben_details->bank_name), 'mobile_no' => trim($ben_details->mobile_no), 'application_id' => $ben_details->created_by_dist_code . str_pad($ben_details->scheme_id, 2, 0, STR_PAD_LEFT) . str_pad($ben_details->id, 15, 0, STR_PAD_LEFT), 'aadhar_no' => trim($ben_details->aadhar_no),
          'doc_name' => $doc_list->doc_name, 'doc_id' => $doc_list->id, 'doc_type' => $doc_list->doc_type, 'doc_size_kb' => $doc_list->doc_size_kb, 'mask_aadhar_no' => $mask_aadhar, 'mask_mobile_no' => $mask_mobile
        );
        $response = $ben_arr;
      }
    } catch (\Exception $e) {
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
    Final Verified Update
  */
  public function updateVerifiedDuplicateBen(Request $request)
  {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $rules = array(
        'update_type' => 'required',
        'remarks' => 'max:100'
      );
      $attributes = [
        'update_type' => 'Mobile Number',
        'remarks' => 'Remarks'
      ];
      $messages = [
        'required' => 'The :attribute field is required.',
        'max' => 'Total :max characters allowed for :attribute'
      ];

      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $scheme_id = $request->scheme_id;
        $ben_id = $request->id;
        $update_type = $request->update_type; // Aadhar Update or Mobile Update
        // echo $update_type;die;
        $action_type = $request->action_type; // Verify or Rejected
        $remarks = $request->remarks;
        $designation_id_old = Auth::user()->designation_id_old;
        $roleArray = $request->session()->get('role');
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
        if ($designation_id_old == 'Verifier') {
          $is_active = 1;
        } else {
          $is_active = 0;
        }
        if ($is_active == 0 || (empty($district_code) && empty($urban_body_code)) || empty($mapping_level)) {
          // return redirect("/")->with('error', 'User Disabled. ');
          return $response = array(
            'status' => 0, 'msg' => array("User Disabled."),
            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
          );
        }

        // Dynamically Modal Name Set
        $beneficiary_table = 'pension.beneficiaries';

        // Update Beneficiary Details
        $msg = 'Verified Successfully';
        if ($action_type == 'verify') {
          if ($update_type == 'mobile') {
            $updateBenDetailsData = ['dup_mobile_edit_role_id' => 2];
            if ($remarks != '') {
              $remarksArr = array('dup_mobile_edit_remarks' => $remarks);
              $updateBenDetailsData = array_merge($updateBenDetailsData, $remarksArr);
            }
            $msg = 'Mobile Number Verified Successfully';
          } else if ($update_type == 'aadhar') {
            $updateBenDetailsData = ['dup_aadhar_edit_role_id' => 2];
            if ($remarks != '') {
              $remarksArr = array('dup_aadhar_edit_remarks' => $remarks);
              $updateBenDetailsData = array_merge($updateBenDetailsData, $remarksArr);
            }
            $msg = 'Aadhar Number Verified Successfully';
          } else if ($update_type == 'no_mobile') {
            $updateBenDetailsData = ['no_mobile_edit_role_id' => 2];
            if ($remarks != '') {
              $remarksArr = array('no_mobile_edit_remarks' => $remarks);
              $updateBenDetailsData = array_merge($updateBenDetailsData, $remarksArr);
            }
            $msg = 'No Mobile Number Verified Successfully';
          }
        }

        DB::connection('pgsql')->beginTransaction();
        try {
          if ($update_type == 'mobile') {
            DB::table($beneficiary_table)->where('scheme_id', $scheme_id)->where('id', $ben_id)->where('dup_mobile', 1)->where('dup_mobile_edit_role_id', 1)->where('created_by_dist_code', $district_code)->where('created_by_local_body_code', $urban_body_code)->update($updateBenDetailsData);
          } else if ($update_type == 'aadhar') {
            DB::table($beneficiary_table)->where('scheme_id', $scheme_id)->where('id', $ben_id)->where('dup_aadhar', 1)->where('dup_aadhar_edit_role_id', 1)->where('created_by_dist_code', $district_code)->where('created_by_local_body_code', $urban_body_code)->update($updateBenDetailsData);
          } else if ($update_type == 'no_mobile') {
            DB::table($beneficiary_table)->where('scheme_id', $scheme_id)->where('id', $ben_id)->where('no_mobile', 1)->where('no_mobile_edit_role_id', 1)->where('created_by_dist_code', $district_code)->where('created_by_local_body_code', $urban_body_code)->update($updateBenDetailsData);
          }
          DB::commit();
          $response = array(
            'status' => 1, 'msg' => $msg,
            'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
          );
        } catch (\Exception $e) {
          //  dd($e);
          DB::rollback();
          $return_status = 0;
          $return_text = 'Error. Please try again';
          $return_msg = array("" . $return_text);
          return $response = array(
            'status' => $return_status, 'msg' => $return_msg,
            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
          );
        }
      } else {
        $return_status = 0;
        $return_msg = $validator->errors()->all();
        $response = array(
          'status' => $return_status, 'msg' => $return_msg,
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }
    } catch (\Exception $e) {
      // DB::rollback();
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
    Aadhar card view
  */
  public function viewVerifyDeDupAadharCard(Request $request)
  {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $scheme_id = $request->scheme_id;
      $ben_id = $request->id;
      $update_type = $request->update_type;
      $designation_id_old = Auth::user()->designation_id_old;
      $roleArray = $request->session()->get('role');
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
      if ($designation_id_old == 'Verifier') {
        $is_active = 1;
      } else {
        $is_active = 0;
      }
      if ($is_active == 0 || (empty($district_code) && empty($urban_body_code)) || empty($mapping_level)) {
        // return redirect("/")->with('error', 'User Disabled. ');
        return $response = array(
          'status' => 0, 'msg' => array("User Disabled."),
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }

      // Dynamically Modal Name Set
      $beneficiary_table = $this->getSchemaName($scheme_id);
      $scheme_short_code = Scheme::where('id', $scheme_id)->value('short_code');
      $ben_docs_table = strtolower($scheme_short_code) . '.ben_docs';

      $data = DB::table($ben_docs_table)->where('ben_id', $ben_id)->where('is_active', TRUE)->where('doc_type_id', 6)->first();
      if ($data == null) {
        return  $response = array(
          'status' => 1, 'msg' => 'Somethimg went wrong..!',
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }
      $response = array('doc_name' => $data->doc_name, 'doc_id' => $data->id, 'doc_type_name' => $data->doc_type_name);
    } catch (\Exception $e) {
      // DB::rollback();
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
    ================= Approver end ================
  */
  public function indexApprove()
  {
    $designation_id_old = Auth::user()->designation_id_old;
    if ($designation_id_old == 'Approver') {
      $is_active = 1;
    } else {
      $is_active = 0;
    }
    if ($is_active == 0) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    $user_id = Auth::user()->id;
    $mapObj = DB::table('public.duty_assignement')->where('user_id', $user_id)->where('is_active', 1)->first();
    $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
    if (Auth::user()->designation_id_old == "Approver") {
      if (count($scheme) > 0) {
        return view('DuplicateAadharUpdate/approve_index', [
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
    Verification get datas at approver end
  */
  public function approveDeDuplicateGetData(Request $request)
  {
    if ($request->ajax()) {
      if (empty($request->scheme_id) || empty($request->select_type)) {
        $data = collect([]);
        return datatables()->of($data)->make(true);
      }

      $scheme_id = $request->scheme_id;
      if (empty($scheme_id)) {
        // return redirect("/")->with('error', 'Scheme Not Valid');
        return $response = array(
          'status' => 0, 'msg' => array("Scheme Not Valid."),
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }
      if (!ctype_digit($scheme_id)) {
        // return redirect("/")->with('error', 'Scheme Not Valid');
        return $response = array(
          'status' => 0, 'msg' => array("Scheme Not Valid."),
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }
      $user_id = Auth::user()->id;
      $designation_id_old = Auth::user()->designation_id_old;
      $errormsg = Config::get('constants.errormsg');
      $roleArray = $request->session()->get('role');
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
      if ($designation_id_old == 'Approver') {
        $is_active = 1;
      } else {
        $is_active = 0;
      }
      if ($is_active == 0 || (empty($district_code))) {
        // return redirect("/")->with('error', 'User Disabled. ');
        return $response = array(
          'status' => 0, 'msg' => array("User Disabled."),
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }

      $scheme_row = Scheme::where('id', $scheme_id)->first();
      // Get Dynamic Schema Name scheme wise
      $table_name = $this->getSchemaName($scheme_id);

      $query = '';
      $query = "select * from " . $table_name . " where is_rejected=0  and scheme_id = " . $scheme_id . " and created_by_dist_code = " . $district_code . " ";
      if (!empty($request->select_type)) {
        if ($request->select_type == 1) {
          $query .= " and dup_aadhar = 1 and dup_aadhar_edit_role_id = 2 ";
        } else if ($request->select_type == 2) {
          $query .= " and dup_mobile = 1 and dup_mobile_edit_role_id = 2 ";
        } else if ($request->select_type == 3) {
          $query .= " and no_mobile = 1 and no_mobile_edit_role_id = 2 ";
        }
      }
      if (!empty($request->block)) {
        $query .= " and block_ulb_code = " . $request->block . "";
      }
      if (!empty($request->block)) {
        $query .= " and rural_urban_id = " . $request->urban_code . "";
      }
      $query .= " order by id desc";
      $data = DB::select(DB::raw($query));

      return datatables()->of($data)
        ->addColumn('view', function ($data) use ($request) {
          $action = '<button class="btn btn-primary btn-xs ben_view_button" value="' . $data->id . '_' . $data->scheme_id . '_' . $request->select_type . '"><i class="glyphicon glyphicon-edit"></i> View</button>';
          return $action;
        })
        ->addColumn('check', function ($data) use ($request) {
          return '<input type="checkbox"  name="chkbx" class="all_checkbox" onclick="controlCheckBox();" value="' . $data->id . '_' . $data->scheme_id . '_' . $request->select_type . '">';
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
          // $mask_mobile = '';
          // $mobile = trim($data->mobile_no);
          // if (strlen($mobile) >= 10 && strlen($mobile) != '') {
          //   $mask_aadhar = '******' . substr($mobile, 6, 4);
          // } else {
          //   $mask_aadhar = $mobile;
          // }
          // return $mask_aadhar;
          return $data->mobile_no;
        })
        ->addColumn('name', function ($data) {
          return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
        })
        ->rawColumns(['view', 'id', 'name', 'aadhar_mask', 'mobile_mask', 'check'])
        ->make(true);
    }
  }

  public function approveSingleAadharMobileBenData(Request $request)
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
      $designation_id_old = Auth::user()->designation_id_old;
      $roleArray = $request->session()->get('role');
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
      if ($designation_id_old == 'Approver') {
        $is_active = 1;
      } else {
        $is_active = 0;
      }
      if ($is_active == 0 || (empty($district_code))) {
        // return redirect("/")->with('error', 'User Disabled. ');
        return $response = array(
          'status' => 0, 'msg' => array("User Disabled."),
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }

      $scheme_row = Scheme::where('id', $scheme_id)->first();
      // Get Dynamic Schema Name scheme wise
      $table_name = $this->getSchemaName($scheme_id);

      $ben_details = DB::table($table_name)->where(function ($query) use ($role_id) {
        $query->where('is_rejected', 0);
      })
        ->where('scheme_id', $scheme_id)
        ->where('created_by_dist_code', $district_code)
        ->where('id', $id)
        ->first();
      if($ben_details->payment_suspended == 1){
        return  $response = array(
          'status' => 1, 'msg' => 'Mark due to JNMP.',
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Information!!'
        );
      }
      // print_r($ben_details);die;
      if ($ben_details == null) {
        return  $response = array(
          'status' => 1, 'msg' => 'Somethimg went wrong.',
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
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
        $mask_mobile = $mobile;
        // if (strlen($mobile) >= 10 && strlen($mobile) != '') {
        //   $mask_mobile = '******' . substr($mobile, 6, 4);
        // } else {
        //   $mask_mobile = $mobile;
        // }
        $ben_arr = array(
          'ben_name' => trim($ben_details->ben_fname) . ' ' . trim($ben_details->ben_mname) . ' ' . trim($ben_details->ben_lname), 'id' => $ben_details->id, 'scheme_id' => $ben_details->scheme_id,
          'father_name' => trim($ben_details->father_fname) . ' ' . trim($ben_details->father_mname) . ' ' . trim($ben_details->father_lname),
          'caste' => trim($ben_details->caste), 'gender' => trim($ben_details->gender),
          'dob' => date('d-m-Y', strtotime($ben_details->dob)),
          'bank_code' => trim($ben_details->bank_code), 'bank_ifsc' => trim($ben_details->bank_ifsc),
          'branch_name' => trim($ben_details->branch_name), 'bank_name' => trim($ben_details->bank_name), 'mobile_no' => trim($ben_details->mobile_no), 'application_id' => $ben_details->created_by_dist_code . str_pad($ben_details->scheme_id, 2, 0, STR_PAD_LEFT) . str_pad($ben_details->id, 15, 0, STR_PAD_LEFT), 'aadhar_no' => trim($ben_details->aadhar_no)
        );
        $response = $ben_arr;
      }
    } catch (\Exception $e) {
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
    Final Approve
  */
  public function updateApprovedDeDupBenData(Request $request)
  {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $roleArray = $request->session()->get('role');
      $user_id = Auth::user()->id;
      $designation_id_old = Auth::user()->designation_id_old;
      $errormsg = Config::get('constants.errormsg');
      $duty = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
      if ($duty->isEmpty) {
        return $response = array(
          'status' => 0, 'msg' => array("Unauthorized."),
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }

      if ($designation_id_old == 'Approver') {
        $is_bulk = $request->is_bulk;
        if ($is_bulk == 1) {
          $fg_is_bulk = 1;
        } else {
          $fg_is_bulk = 0;
        }
      } else {
        return $response = array(
          'status' => 0, 'msg' => array("Unauthorized."),
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }
      $remarks = trim($request->accept_reject_comments);
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
      $updateTypeArr =  array_unique($updateTypeArr);

      if (count($schemeIdArr) != 1) {
        return $response = array(
          'status' => 0, 'msg' => array("Something went wrong in the scheme."),
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }
      $scheme_id = implode("", $schemeIdArr);
      $update_type = implode("", $updateTypeArr); // 1 => Aadhar, 2 => Mobile
      $table_name = $this->getSchemaName($scheme_id);
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
          'status' => 0, 'msg' => array("User Disabled."),
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }

      // Update Beneficiary Details
      $msg = 'Approved Successfully';
      if ($operation_type == 'A') {
        if ($update_type == 2) {
          $updateBenDetailsData = ['dup_mobile' => 0, 'dup_mobile_edit_role_id' => 0];
          if ($remarks != '') {
            $remarksArr = array('dup_mobile_edit_remarks' => $remarks);
            $updateBenDetailsData = array_merge($updateBenDetailsData, $remarksArr);
          }
          $msg = 'Mobile Number Approved Successfully';
        } else if ($update_type == 1) {
          $updateBenDetailsData = ['dup_aadhar' => 0, 'dup_aadhar_edit_role_id' => 0];
          if ($remarks != '') {
            $remarksArr = array('dup_aadhar_edit_remarks' => $remarks);
            $updateBenDetailsData = array_merge($updateBenDetailsData, $remarksArr);
          }
          $msg = 'Aadhar Number Approved Successfully';
        } else if ($update_type == 3) {
          $updateBenDetailsData = ['no_mobile' => 0, 'no_mobile_edit_role_id' => 0];
          if ($remarks != '') {
            $remarksArr = array('no_mobile_edit_remarks' => $remarks);
            $updateBenDetailsData = array_merge($updateBenDetailsData, $remarksArr);
          }
          $msg = 'No Mobile Number Approved Successfully';
        }
      }

      DB::connection('pgsql')->beginTransaction();
      try {
        if ($update_type == 2) { // Mobile
          DB::table($table_name)->where('scheme_id', $scheme_id)->whereIn('id', $benIdArr)->where('dup_mobile', 1)->where('dup_mobile_edit_role_id', 2)->where('created_by_dist_code', $district_code)->update($updateBenDetailsData);
        } else if ($update_type == 1) { // Aadhar
          DB::table($table_name)->where('scheme_id', $scheme_id)->whereIn('id', $benIdArr)->where('dup_aadhar', 1)->where('dup_aadhar_edit_role_id', 2)->where('created_by_dist_code', $district_code)->update($updateBenDetailsData);
        } else if ($update_type == 3) { // No Mobile
          DB::table($table_name)->where('scheme_id', $scheme_id)->whereIn('id', $benIdArr)->where('no_mobile', 1)->where('no_mobile_edit_role_id', 2)->where('created_by_dist_code', $district_code)->update($updateBenDetailsData);
        }
        DB::commit();
        $response = array(
          'status' => 1, 'msg' => $msg,
          'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
        );
      } catch (\Exception $e) {
        // dd($e);
        DB::rollback();
        $return_status = 0;
        $return_text = 'Error. Please try again.';
        $return_msg = array("" . $return_text);
        return $response = array(
          'status' => $return_status, 'msg' => $return_msg,
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }
    } catch (\Exception $e) {
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
