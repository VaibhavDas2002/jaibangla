<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GP;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use App\BeneficiaryPensions;
use App\BenDocsSc;
use App\BenDocsSt;
use App\DocumentType;
use App\Configduty;
use App\MapLavel;
use App\Scheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;

class FailedBankDetailsEditController extends Controller
{
  public function __construct()
  {
    set_time_limit(120);
    $this->middleware('auth');
    date_default_timezone_set('Asia/Kolkata');
  }
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
      $table_name =  strtolower($schema_name) . '.beneficiary';
    } else {
      $table_name =  'pension.beneficiary';
    }
    return $table_name;
  }
  public function checkSchemeSession($scheme_id)
  {
    $roleArray = Session::get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        Session::put('level', $roleObj['mapping_level']);
        Session::put('distCode', $roleObj['district_code']);
        Session::put('scheme_id', $scheme_id);
        Session::put('is_first', $roleObj['is_first']);
        Session::put('is_urban', $roleObj['is_urban']);
        Session::put('role_id', $roleObj['id']);
        if ($roleObj['is_urban'] == 1) {
          Session::put('bodyCode', $roleObj['urban_body_code']);
        } else {
          Session::put('bodyCode', $roleObj['taluka_code']);
        }
        break;
      }
    }
  }
  private function getLotGenerated($payMode)
  {
    if ($payMode == 'SBI') {
      $lot_generated = -3;
    } else if ($payMode == 'RBI') {
      $lot_generated = -2;
    } else if ($payMode == 'IFMS') {
      $lot_generated = -1;
    }
    return $lot_generated;
  }
  /*
		Get First Landing Page
  */
  public function index($payment_mode)
  {
    $payModeArr = array('SBI', 'RBI', 'IFMS');
    if (in_array($payment_mode, $payModeArr)) {
      $user_id = Auth::user()->id;
      $designation = Auth::user()->designation_id;
      $mapObj = DB::connection('pgsql_mis')->table('public.duty_assignement')->where('user_id', $user_id)->where('is_active', 1)->first();
      $scheme = DB::connection('pgsql_mis')->select('select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' . $user_id . ' and is_active=1) order by scheme_name');
      if (Auth::user()->designation_id == "Verifier") {
        if (count($scheme) > 0) {
          if ($mapObj->is_urban == 1) {
            $urban_body_code = $mapObj->urban_body_code;
            $urban_bodys = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();
            return view('failed-bank-details-edit/index', ['schemes' => $scheme, 'mapLevel' => $mapObj->mapping_level . $designation, 'payMode' => $payment_mode, 'urban_bodys' => $urban_bodys]);
          } else {
            $taluka_code = $mapObj->taluka_code;
            $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
            return view('failed-bank-details-edit/index', ['schemes' => $scheme, 'mapLevel' => $mapObj->mapping_level . $designation, 'payMode' => $payment_mode, 'gps' => $gps]);
          }
        } else {
          return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
        }
      } else if (Auth::user()->designation_id == "Approver") {
        return view('failed-bank-details-edit/index', ['schemes' => $scheme, 'mapLevel' => $mapObj->mapping_level . $designation, 'payMode' => $payment_mode]);
      } else {
        return redirect("/")->with('success', 'UnAuthorized');
      }
    } else {
      return redirect("/")->with('success', 'Payment mode is undefined');
    }
  }
  /*
		Get Beneficiary Data
  */
  public function getFailedBankListPaymentModeWise(Request $request)
  {
    if ($request->ajax()) {
      $scheme_id = $request->scheme_id;
      $this->checkSchemeSession($scheme_id);

      $mappingLevel = $request->session()->get('level');
      $district_code = $request->session()->get('distCode');
      $is_first = $request->session()->get('is_first');
      $is_urban = $request->session()->get('is_urban');
      $body_code = $request->session()->get('bodyCode');
      $role_id = $request->session()->get('role_id');

      // Get Lot generated condition accoding to the payment mode wise
      $payMode = $request->payment_mode;
      $lot_generated = $this->getLotGenerated($payMode);
      // Get Dynamic Schema Name scheme wise
      $table_name = $this->getSchemaName($scheme_id);

      if ($mappingLevel == 'Block') {
        if (!empty($request->filter_1)) { // 29-07-2020
          $data = DB::connection('pgsql_mis')->table($table_name)->where(function ($query) use ($role_id) {
            $query->where('next_level_role_id', $role_id)    //29-07-2020
              ->orWhere('next_level_role_id', 0);
          })
            ->where('lot_generated', $lot_generated)
            ->where('scheme_id', $scheme_id)
            ->where('created_by_local_body_code', $body_code)
            ->where('gp_ward_code', $request->filter_1)
            ->where('bank_edited', 0) //Temporary Code
            ->get();
        } else {
          $data = DB::connection('pgsql_mis')->table($table_name)->where(function ($query) use ($role_id) {
            $query->where('next_level_role_id', $role_id)              //29-07-2020
              ->orWhere('next_level_role_id', 0);
          })
            ->where('lot_generated', $lot_generated)
            ->where('scheme_id', $scheme_id)
            ->where('created_by_local_body_code', $body_code)
            ->where('bank_edited', 0) //Temporary Code
            ->get();
        }
      } else if ($mappingLevel == 'Subdiv') {
        if (!empty($request->filter_1)  && empty($request->filter_2)) {
          $data = DB::connection('pgsql_mis')->table($table_name)->where(function ($query) use ($role_id) {
            $query->where('next_level_role_id', $role_id)          //29-07-2020
              ->orWhere('next_level_role_id', 0);
          })
            ->where('lot_generated', $lot_generated)
            ->where('scheme_id', $scheme_id)
            ->where('created_by_local_body_code', $body_code)
            ->where('block_ulb_code', $request->filter_1)
            //->where('gp_ward_code', $request->filter_2)
            ->where('bank_edited', 0) //Temporary Code
            ->orderBy('id', 'desc')
            ->get();
        } elseif (!empty($request->filter_1) && !empty($request->filter_2)) {
          $data = DB::connection('pgsql_mis')->table($table_name)->where(function ($query) use ($role_id) {
            $query->where('next_level_role_id', $role_id)           //29-07-2020
              ->orWhere('next_level_role_id', 0);
          })
            ->where('lot_generated', $lot_generated)
            ->where('scheme_id', $scheme_id)
            ->where('created_by_local_body_code', $body_code)
            ->where('block_ulb_code', $request->filter_1)
            ->where('gp_ward_code', $request->filter_2)
            ->where('bank_edited', 0) //Temporary Code
            ->orderBy('id', 'desc')
            ->get();
        } else {
          $data = DB::connection('pgsql_mis')->table($table_name)->where(function ($query) use ($role_id) {
            $query->where('next_level_role_id', $role_id)           //29-07-2020
              ->orWhere('next_level_role_id', 0);
          })
            ->where('lot_generated', $lot_generated)
            ->where('scheme_id', $scheme_id)
            ->where('created_by_local_body_code', $body_code)
            ->where('bank_edited', 0) //Temporary Code
            ->orderBy('id', 'desc')
            ->get();
        }
      } else if ($mappingLevel == 'District') {
        $data = DB::connection('pgsql_mis')->table($table_name)->where(function ($query) use ($role_id) {
          $query->where('next_level_role_id', $role_id)           //29-07-2020
            ->orWhere('next_level_role_id', 0);
        })
          ->where('lot_generated', $lot_generated)
          ->where('scheme_id', $scheme_id)
          ->where('created_by_dist_code', $district_code)
          ->where('bank_edited', 0) //Temporary Code
          ->orderBy('id', 'desc')
          ->get();
      }
      return datatables()->of($data)
        ->addColumn('view', function ($data) use ($payMode) {
          return '<button onclick=editFunction(' . $data->id . ',' . $data->scheme_id . ',"' . $payMode . '") class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button>';
        })
        ->addColumn('id', function ($data) {
          return $data->created_by_dist_code . str_pad($data->scheme_id, 2, 0, STR_PAD_LEFT) . str_pad($data->id, 15, 0, STR_PAD_LEFT);
        })
        ->addColumn('name', function ($data) {
          return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
        })->addColumn('mobile_no', function ($data) {
          return $data->mobile_no;
        })->addColumn('house_premise_no', function ($data) {
          return trim($data->house_premise_no);
        })
        ->rawColumns(['view', 'id', 'name'])
        ->make(true);
    }
  }
  /*
		Get Modal Data of indivisual beneficiary
  */
  public function getModalDataFailedBankEdit(Request $request)
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
      $this->checkSchemeSession($scheme_id);

      $mappingLevel = $request->session()->get('level');
      $district_code = $request->session()->get('distCode');
      $is_first = $request->session()->get('is_first');
      $is_urban = $request->session()->get('is_urban');
      $body_code = $request->session()->get('bodyCode');
      $role_id = $request->session()->get('role_id');

      // Get Lot generated condition accoding to the payment mode wise
      $payMode = $request->pay_mode;
      $lot_generated = $this->getLotGenerated($payMode);
      // Get Dynamic Schema Name scheme wise
      $table_name = $this->getSchemaName($scheme_id);
      $ben_details = DB::connection('pgsql_mis')->table($table_name)->where('id', '=', $id)
        ->where('scheme_id', $scheme_id)
        ->where(function ($query) use ($role_id) {
          $query->where('next_level_role_id', $role_id)
            ->orWhere('next_level_role_id', 0);
        })
        ->where('lot_generated', $lot_generated)
        ->where('created_by_dist_code', $district_code)
        ->where('bank_edited', 0) //Temporary Code
        ->first();

      // Get Financial Year wise Transaction lot details report table
      $last_paid_yymm = $ben_details->last_paid_yymm;
      $created_at = $ben_details->created_at;

      if ($last_paid_yymm > 0) {
        $month1 = trim((int)substr($last_paid_yymm, 2, 2));
        $year1 = (int)('20' . substr($last_paid_yymm, 0, 2));
        $f_date = $year1 . '/' . $month1 . '/01';
        $time = strtotime($f_date);
        $final = date("ym", strtotime("-1 month", $time));
        $month = trim((int)substr($final, 2, 2));
        $year = (int)('20' . substr($final, 0, 2));
      } else {
        $year = (int)(date("Y", strtotime($created_at)));
        $month = (int)(date("m", strtotime($created_at)));
      }
      if ($month > 3) {
        $fin_year = $year . '-' . ($year + 1);
      } else {
        $fin_year = ($year - 1) . '-' . $year;
      }
      $fyArr = explode('-', $fin_year);
      $report_yyyy = substr($fyArr[0], 2, 2) . substr($fyArr[1], 2, 2);

      // Get Error Reason for failure
      $invalid_status = '';
      if ($payMode == 'SBI') {
        $queryFailedReason = "select  max(lot_no),status_code,c.description from (select max(lot_no) as lot_no,status_code from sbi.transaction_lot_details where pension_id=" . $id . "
		    and scheme_id=" . $scheme_id . " group by status_code
		     union all 
		    select max(lot_no) as lot_no,status_code from sbi.transaction_lot_details_report_" . $report_yyyy . " where pension_id=" . $id . " and scheme_id=" . $scheme_id . " group by status_code) 
		    p,sbi.credit_transaction_code c where p.status_code
		    =c.code group by status_code,c.description order by max(lot_no) desc";
        $query_res = DB::connection('pgsql_mis')->select($queryFailedReason);
        if (!empty($query_res[0]->description)) {
          $invalid_status = $query_res[0]->description;
        }
      } else if ($payMode == 'RBI') {
        $queryFailedReason = "select  max(lot_no),ifms_status from (select max(drn_part) as lot_no,ifms_status from ifms.transaction_lot_details where pension_id=" . $id . " group by ifms_status
        union all 
        select max(drn_part) as lot_no,ifms_status from ifms.transaction_lot_details_report_" . $report_yyyy . " where pension_id=" . $id . " group by ifms_status) 
        p group by ifms_status order by max(lot_no) desc";
        $query_res = DB::connection('pgsql_mis')->select($queryFailedReason);
        if (!empty($query_res[0]->ifms_status)) {
          $invalid_status = $query_res[0]->ifms_status;
        }
      } else if ($payMode == 'IFMS') {
        $queryFailedReason = "select  max(lot_no),ifms_status from (select max(drn_part) as lot_no,ifms_status from ifms.transaction_lot_details where pension_id=" . $id . " group by ifms_status
       	union all 
       	select max(drn_part) as lot_no,ifms_status from ifms.transaction_lot_details_report_" . $report_yyyy . " where pension_id=" . $id . " group by ifms_status) 
       	p group by ifms_status order by max(lot_no) desc";
        $query_res = DB::connection('pgsql_mis')->select($queryFailedReason);
        if (!empty($query_res[0]->ifms_status)) {
          $invalid_status = $query_res[0]->ifms_status;
        }
      }

      if ($ben_details == null) {
        return  $response = array(
          'status' => 1, 'msg' => 'Somethimg went wrong.',
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      } else {
        $ben_arr = array(
          'ben_name' => trim($ben_details->ben_fname) . ' ' . trim($ben_details->ben_mname) . ' ' . trim($ben_details->ben_lname), 'id' => $ben_details->id, 'scheme_id' => $ben_details->scheme_id,
          'father_name' => trim($ben_details->father_fname) . ' ' . trim($ben_details->father_mname) . ' ' . trim($ben_details->father_lname),
          'caste' => trim($ben_details->caste), 'gender' => trim($ben_details->gender),
          'dob' => date('d-m-Y', strtotime($ben_details->dob)),
          'bank_code' => trim($ben_details->bank_code), 'bank_ifsc' => trim($ben_details->bank_ifsc),
          'branch_name' => trim($ben_details->branch_name), 'bank_name' => trim($ben_details->bank_name), 'mobile_no' => trim($ben_details->mobile_no), 'application_id' => $ben_details->created_by_dist_code . str_pad($ben_details->scheme_id, 2, 0, STR_PAD_LEFT) . str_pad($ben_details->id, 15, 0, STR_PAD_LEFT)
        );
        $response = array_merge($ben_arr, array('status' => 2, 'pay_mode' => $payMode, 'failed_reason' => $invalid_status));
      }
    } catch (\Exception $e) {
      $response = array(
        'exception' => true,
        // 'exception_message' => $e->getMessage(),
        'exception_message' => 'Something went wrong. May be session time out logout and login again.',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }
  /*
		Update Bank Details
  */
  public function updateFailedBankDetails(Request $request)
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
        'bank_name' => 'required',
        'branch_name' => 'required',
        'bank_code' => 'required|numeric|between:00000000000000000000,9999999999999999999',
        'bank_ifsc' => 'required|max:20'
      );
      $attributes = [
        'bank_name' => 'Bank name',
        'branch_name' => 'Branch name',
        'bank_ifsc' => 'IFSC',
        'bank_code' => 'A/c No'
      ];
      $messages = [
        'required' => 'The :attribute field is required.',
        'integer' => 'Only integer allowed for :attribute',
        'max' => 'Maximum of :size characters allowed for :attribute',
        'size' => 'The :attribute must be exactly :size.',
      ];
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $scheme_id = $request->scheme_id;
        $table = $this->getSchemaName($scheme_id);
        $this->checkSchemeSession($scheme_id);
        $mappingLevel = $request->session()->get('level');
        $district_code = $request->session()->get('distCode');
        $is_first = $request->session()->get('is_first');
        $is_urban = $request->session()->get('is_urban');
        $body_code = $request->session()->get('bodyCode');
        $role_id = $request->session()->get('role_id');
        // Get Lot generated condition accoding to the payment mode wise
        $payMode = $request->pay_mode;
        $lot_generated = $this->getLotGenerated($payMode);
        $id = $request->id;

        $new_bank_name = $request->bank_name;
        $new_branch_name = $request->branch_name;
        $new_bank_ifsc = $request->bank_ifsc;
        $new_bank_code = $request->bank_code;
        $new_mobile_no = $request->mobile_no;

        $benDetails = DB::connection('pgsql_mis')->table($table)->where('id', $id)->first();

        // Checking Duplicate A/c And IFSC
        $scheme_list = Config::get('constants.duplicate_bank_info_check');
        if (in_array($scheme_id, $scheme_list)) {
          if ($scheme_id == 8 || $scheme_id == 9) {
            $benDuplicateAcCount = DB::connection('pgsql_mis')->table('pension.beneficiary')->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")->whereRaw("trim(bank_code)=trim(" . "'" . $new_bank_code . "'" . ")")
              ->where('id', '!=', $id)
              ->whereRaw('is_rejected',0)
              ->whereIn('scheme_id', [8, 9])->count('id');
          } else {
            $benDuplicateAcCount = DB::connection('pgsql_mis')->table($table)->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")->whereRaw("trim(bank_code)=trim(" . "'" . $new_bank_code . "'" . ")")
              ->where('id', '!=', $id)
              ->where('is_rejected',0)
              ->where('scheme_id', $scheme_id)->count('id');
          }
          if ($benDuplicateAcCount > 0) {
            if ($scheme_id == 8 || $scheme_id == 9) {
              $msg = 'This Bank A/c - ' . $new_bank_code . ' & IFSC - ' . $new_bank_ifsc . ' already exist LPP Retainer or LPP Pensioner scheme';
            } else {
              $msg = 'This Bank A/c - ' . $new_bank_code . ' & IFSC - ' . $new_bank_ifsc . ' already exist in this scheme';
            }
            return $response = array(
              'status' => 3, 'msg' => $msg,
              'type' => 'blue', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
            );
          }
        }

        $old_bank_name = $benDetails->bank_name;
        $old_branch_name = $benDetails->branch_name;
        $old_bank_ifsc = $benDetails->bank_ifsc;
        $old_bank_code = $benDetails->bank_code;
        $old_mobile_no = $benDetails->mobile_no;

        $function_parameter_bank = '';
        $function_parameter_mobile = '';
        if ((trim($new_bank_code) <> trim($old_bank_code)) || (trim($new_bank_ifsc) <> trim($old_bank_ifsc))) {
          $function_parameter_bank = "new_bank_ifsc => '" . trim($new_bank_ifsc) . "', old_bank_ifsc => '" . trim($old_bank_ifsc) . "', new_bank_code => '" . trim($new_bank_code) . "', old_bank_code => '" . trim($old_bank_code) . "', ";
        }
        if ($new_mobile_no <> $old_mobile_no) {
          $function_parameter_mobile = "new_mobile_no => " . trim($new_mobile_no) . ", old_mobile_no => " . trim($old_mobile_no) . ", ";
        }

        DB::beginTransaction();
        // Checking Duplicate Bank A/c & IFSC or Mobile No using Bank Code and Mobile Unique Table
        $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
        if (in_array($scheme_id, $scheme_dedup_list)) {
          $schema_name = Scheme::where('id', $scheme_id)->value('short_code');
          $message_arr = array(
            1 => 'Updated Successfully',
            2 => 'This Bank A/c - ' . $new_bank_code . ' & IFSC - ' . $new_bank_ifsc . ' already exist in this scheme, please try another one..',
            3 => 'Bank A/c - ' . $new_bank_code . ' & IFSC - ' . $new_bank_ifsc . ' updation problem, please try after sometime...',
            6 => 'This Mobile no - ' . $new_mobile_no . ' already exist in this scheme, please try another one....',
            7 => 'Mobile no - ' . $new_mobile_no . ' updation problem, please try after sometime.....',
            8 => '8 Something went wrong...'
          );
          if ($payMode == 'SBI') {
            if ($function_parameter_bank <> '') {
              $parName = $function_parameter_bank;
              $parName = rtrim($parName, ", ");
              // print $parName;die;
              $callFunction = DB::select(DB::raw("SELECT " . strtolower($schema_name) . ".duplicate_bank_mobile_check(in_scheme_id => " . $scheme_id . ", " . $parName . ")"));
            }
          } else if ($payMode == 'RBI') {
            if ($function_parameter_bank <> '' || $function_parameter_mobile <> '') {
              $parName = $function_parameter_bank . $function_parameter_mobile;
              $parName = rtrim($parName, ", ");
              $callFunction = DB::select(DB::raw("SELECT " . strtolower($schema_name) . ".duplicate_bank_mobile_check(in_scheme_id => " . $scheme_id . ", " . $parName . ")"));
            }
          } else if ($payMode == 'IFMS') {
            if ($function_parameter_bank <> '' || $function_parameter_mobile <> '') {
              $parName = $function_parameter_bank . $function_parameter_mobile;
              $parName = rtrim($parName, ", ");
              $callFunction = DB::select(DB::raw("SELECT " . strtolower($schema_name) . ".duplicate_bank_mobile_check(in_scheme_id => " . $scheme_id . ", " . $parName . ")"));
            }
          }
          if (isset($callFunction)) {
            $return_fun = $callFunction[0]->duplicate_bank_mobile_check;
            if ($return_fun <> 1) {
              return $response = array(
                'status' => 3, 'msg' => $message_arr[$return_fun],
                'type' => 'orange', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
              );
            }
          }
        }
        // echo 'All Clear'; die;
        $input = [];
        $input_new = [];
        $old_value = [];

        $old_value['old_bank_name'] = trim($old_bank_name);
        $old_value['old_branch_name'] = trim($old_branch_name);
        $old_value['old_bank_ifsc'] = trim($old_bank_ifsc);
        $old_value['old_bank_code'] = trim($old_bank_code);

        $input_new['new_bank_name'] = $new_bank_name;
        $input_new['new_branch_name'] = $new_branch_name;
        $input_new['new_bank_ifsc'] = $new_bank_ifsc;
        $input_new['new_bank_code'] = $new_bank_code;

        $update_beneficiaryTable = [
          'bank_name' => $new_bank_name,
          'branch_name' => $new_branch_name,
          'bank_ifsc' => trim($new_bank_ifsc),
          'bank_code' => trim($new_bank_code),
          'bank_edited' => 1
        ];
        // Get Update Code and remarks
        if ($payMode == 'SBI') {
          $update_code = 5;
          $remarks = 'SBI Failed Update Bank Details';
          $update_ben = $update_beneficiaryTable;
        } else if ($payMode == 'RBI') {
          $update_code = 6;
          $remarks = 'RBI Failed Update Bank Details';
          $update_ben = array_merge($update_beneficiaryTable, array('mobile_no' => $new_mobile_no));
        } else if ($payMode == 'IFMS') {
          $update_code = 7;
          $remarks = 'IFMS Failed Update Bank Details';
          $update_ben = array_merge($update_beneficiaryTable, array('mobile_no' => $new_mobile_no));
        }
        $updateBenDetailsData = [
          'original_application_id' => $benDetails->id,
          'dist_code' => $benDetails->dist_code,
          'scheme_id' => $benDetails->scheme_id,
          'remarks' => $remarks,
          'old_data' => json_encode($old_value),
          'new_data' => json_encode($input_new),
          'user_id' => Auth::user()->id,
          'update_code' => $update_code,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ];

        /*--- Final Database Opertations ---*/
        // DB::beginTransaction();
        // $is_update=1;
        $is_update = UpdateBenDetails::insert($updateBenDetailsData);
        if ($is_update) {
          // $is_saved=1;
          $is_saved = DB::table($table)->where('id', $id)
            ->where('scheme_id', $scheme_id)
            ->where(function ($query) use ($role_id) {
              $query->where('next_level_role_id', $role_id)
                ->orWhere('next_level_role_id', 0);
            })
            ->where('lot_generated', $lot_generated)
            ->where('created_by_dist_code', $district_code)
            ->where('bank_edited', 0) //Temporary Code
            ->update($update_ben);
          if ($is_saved) {
            DB::commit();
            $response = array(
              'status' => 1, 'msg' => 'Bank Details Updated Successfully',
              'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
            );
          } else {
            DB::rollback();
            $response = array(
              'status' => 3, 'msg' => '3 Somethimg went wrong!!',
              'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
            );
          }
        } else {
          DB::rollback();
          $response = array(
            'status' => 2, 'msg' => '2 Somethimg went wrong!!',
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
      DB::rollback();
      $response = array(
        'exception' => true,
        // 'exception_message' => $e->getMessage(),
        'exception_message' => 'Something went wrong. May be session time out logout and login again.',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }
}
