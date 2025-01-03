<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\BeneficiaryPensions;
use App\District;
use App\Configduty;
use App\Scheme;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use App\Helpers\AuthChecker;

class InactiveActiveController extends Controller
{
  protected $ben_id;
  public function __construct()
  {
    $this->middleware('auth');
    date_default_timezone_set('Asia/Kolkata');
    $this->ben_id = 1809540;
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
      $table_name = strtolower($schema_name) . '.beneficiary';
    } else {
      $table_name = 'pension.beneficiary';
    }
    return $table_name;
  }
  public function index(Request $request)
  {
    $designation_id = Auth::user()->designation_id;
    if ($designation_id == 'Approver') {
      $is_active = 1;
    } else {
      $is_active = 0;
    }
    if ($is_active == 0) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    $user_id = AuthChecker::getUserId();
    $mapObj = DB::table('public.duty_assignement')->where('user_id', $user_id)->where('is_active', 1)->first();
    $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . " and scheme_id in(8) )"));
    if (Auth::user()->designation_id == "Approver") {
      if (count($scheme) > 0) {
        return view('Inactive-Active/index', [
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
  public function inactive(Request $request)
  {
    // dd($request->all());
    if ($request->ajax()) {

      if (empty($request->scheme_id) || empty($request->ben_id)) {
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
      $designation_id = Auth::user()->designation_id;
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
      if ($designation_id == 'Approver') {
        $is_active = 1;
      } else {
        $is_active = 0;
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

      $scheme_row = Scheme::where('id', $scheme_id)->first();
      // Get Dynamic Schema Name scheme wise
      $table_name = $this->getSchemaName($scheme_id);

      $limit = $request->input('length');
      $offset = $request->input('start');
      $query = DB::connection('pgsql')->table($table_name)
        ->where('scheme_id', $scheme_id)
        ->where('created_by_dist_code', $district_code);

      if (!empty($request->block)) {
        $query = $query->where('created_by_local_body_code', $request->block);
      }
      $query = $query->where('id', $this->ben_id);
      //  $query=$query->where('next_level_role_id', -99);
      // dd($query->toSql());
      $totalRecords = $query->count();
      $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
      // dd($data);

      $filterRecords = count($data);
      return datatables()->of($data)
        ->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('view', function ($data) {
          // dd($data->next_level_role_id);
          $btn = '';
          if ($data->next_level_role_id == -99) {
            $btn .= '<button class="btn btn-primary btn-xs ben_view_button" value="' . $data->id . '_' . $data->scheme_id . '"><i class="glyphicon glyphicon-edit"></i> View</button>';
            // $btn = 'Activated';
          } else {
            // dd(123);
            $btn .= '<span class="label label-success">Activated</span>';
          }
          return $btn;
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
          if (strlen($mobile) >= 10 && strlen($mobile) != '') {
            $mask_aadhar = '******' . substr($mobile, 6, 4);
          } else {
            $mask_aadhar = $mobile;
          }
          return $mask_aadhar;
        })
        ->addColumn('name', function ($data) {
          return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
        })
        ->rawColumns(['view', 'id', 'name', 'aadhar_mask', 'mobile_mask', 'check'])
        ->make(true);
    }
  }
  public function modalView(Request $request)
  {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $id = $request->ben_id;

      $scheme_id = $request->scheme_id;
      // dd($scheme_id);
      $designation_id = Auth::user()->designation_id;
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
      if ($designation_id == 'Approver') {
        $is_active = 1;
      } else {
        $is_active = 0;
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

      $scheme_row = Scheme::where('id', $scheme_id)->first();
      $table_name = $this->getSchemaName($scheme_id);
      $ben_details = DB::table($table_name)->where(function ($query) use ($role_id) {
        $query->where('next_level_role_id', -99);

      })

        ->where('created_by_dist_code', $district_code)
        ->where('id', $this->ben_id)
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
        ];
      }
    } catch (\Exception $e) {
      // dd($e);
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
  public function activeBen(Request $request)
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

      $remarks = trim($request->accept_reject_comments);
      $scheme_id = $request->scheme_id;
      $table_name = $this->getSchemaName($scheme_id);

      $ben_details = DB::table($table_name)->where('scheme_id', $scheme_id)->where('id', $this->ben_id)->first();
      $insertData = [
        'beneficiary_id' => $this->ben_id,
        'ben_fname' => $ben_details->ben_fname,
        'ben_mname' => $ben_details->ben_mname,
        'ben_lname' => $ben_details->ben_lname,
        'gender' => $ben_details->gender,
        'aadhar_no' => $ben_details->aadhar_no,
        'mobile_no' => $ben_details->mobile_no,
        'created_at' => $ben_details->created_at,
        'updated_at' => $ben_details->updated_at,
        'created_by' => $ben_details->created_by,
        'created_by_level' => $ben_details->created_by_level,
        'created_by_dist_code' => $ben_details->created_by_dist_code,
        'created_by_local_body_code' => $ben_details->created_by_local_body_code,
        'scheme_id' => $ben_details->scheme_id,
        'next_level_role_id' => $ben_details->next_level_role_id,
        'opeation_at' => date('Y-m-d H:i:s', time()),
        'opeation_by' => $user_id,
        'is_rejected' => $ben_details->is_rejected,
        'is_approved' => $ben_details->is_approved,
        'is_verified' => $ben_details->is_verified,
        'remarks' => $remarks
      ];
      // dd($insertData);

      $msg = 'Activated Successfully';
      $updateBenData = ['next_level_role_id' => 0, 'is_verified' => 1, 'is_approved' => 1, 'is_rejected' => 0];
      $updatePaymentdata = ['is_eligible' => true, 'is_rejected' => 0];
      try {
        DB::beginTransaction();
        DB::connection('pgsql_paywrite')->beginTransaction();
        $update = DB::table($table_name)->where('scheme_id', $scheme_id)->where('id', $this->ben_id)->update($updateBenData);
        $insert = DB::table('pension.reactivate_ben_track')->where('scheme_id', $scheme_id)->where('id', $this->ben_id)->insert($insertData);
        $payment_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('scheme_id', $scheme_id)->where('ben_id', $this->ben_id)->update($updatePaymentdata);
        if ($update && $insert && $payment_update) {
          DB::commit();
          DB::connection('pgsql_paywrite')->commit();
          $response = array(
            'status' => 1,
            'msg' => $msg,
            'type' => 'green',
            'icon' => 'fa fa-check',
            'title' => 'Success'
          );
        } else {
          DB::connection('pgsql')->rollback();
          DB::connection('pgsql_paywrite')->rollback();
          $response = array(
            'status' => 0,
            'msg' => array("" . "Somethimg went wrong.."),
            'type' => 'green',
            'icon' => 'fa fa-check',
            'title' => 'Success'
          );
        }
      } catch (\Exception $e) {
        //  dd($e);
        DB::rollback();
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
