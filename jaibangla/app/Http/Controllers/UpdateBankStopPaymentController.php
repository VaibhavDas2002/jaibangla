<?php

namespace App\Http\Controllers;

use App\AcceptRejectInfo;
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
use Illuminate\Support\Facades\Route;
use App\BankDetails;
use App\BenEntry;
use App\BenPaymentDetails;
use App\DocumentType;
use App\Helpers\AuthChecker;
use App\Helpers\DupCheck;
class UpdateBankStopPaymentController extends Controller
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
    Get First Landing Page
  */
  public function index()
  {
    $user_id = AuthChecker::getUserId();
    // $designation = Auth::user()->designation_id;
    $is_verifier = AuthChecker::VerifierPermission();
    $is_approver = AuthChecker::ApproverPermission();
    if (AuthChecker::ApproverPermission() || AuthChecker::VerifierPermission()) {
      $mapObj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
      if (AuthChecker::ApproverPermission()) {
        $scheme = Configduty::select('scheme_id')->where('user_id', $user_id)->where('is_active', 1)->get();
      } else if (AuthChecker::VerifierPermission()) {
        $scheme = Configduty::select('scheme_id')->distinct()->where('user_id', $user_id)->where('is_active', 1)->whereIn('scheme_id', array(2, 10, 11, 3, 1, 19))->get();
      }

      if (count($scheme) > 0) {
        return view('update-ben-details/index', ['schemes' => $scheme, 'mapping_level' => $mapObj->mapping_level, 'is_verifier' => $is_verifier , 'is_approver' => $is_approver]);
      } else {
        return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
      }
    } else {
      return redirect("/")->with('success', 'Unauthorized.');
    }
  }

  public function searchByBenName(Request $request)
  {
    // dd($request);
    if ($request->ajax()) {
      $user_id = AuthChecker::getUserId();
      $designation = Auth::user()->designation_id;
      $is_verifier = AuthChecker::VerifierPermission();
      $is_approver = AuthChecker::ApproverPermission();
      $mapObj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
      $dist_code = $mapObj->district_code;

      if ($mapObj->is_urban == 1) {
        $map_block_ulb = $mapObj->urban_body_code;
      } else {
        $map_block_ulb = $mapObj->taluka_code;
      }
      $map_level = $mapObj->mapping_level;

      //$ben_id = $request->ben_id;
      /*Application Id*/
      if (strlen($request->ben_id) == 20) {
        $str = substr($request->ben_id, -14);
        $ben_id = ltrim($str, "0");
      } else {
        $ben_id = $request->ben_id;
      }
      //print $ben_id;die();
      $scheme_id = $request->scheme_id;

      if (!is_null($scheme_id)) {
        $sObj = Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
        //$parameter['scheme_id'] = $scheme_id;
        $schema_name = $sObj->short_code;
        //dd($schema_name);
        if (empty($schema_name)) {
          $schema_name = 'pension';
        }
        $table_name = $schema_name . '.beneficiaries';
      } else {
        $table_name = 'pension.beneficiaries';
      }

      if (AuthChecker::VerifierPermission() || AuthChecker::ApproverPermission()) {
        $type = $request->select_type;
        $first_name = strtoUpper(trim($request->ben_fname));
        $middle_name = strtoUpper(trim($request->ben_mname));
        $last_name = strtoUpper(trim($request->ben_lname));
        // $rural_urban = $request->is_rural_urban;
        // $block_ulb = $request->block_ulb;
        if ($type == 'b_id' && !is_null($ben_id)) {
          $query = "select * from " . $table_name . " where created_by_dist_code = " . $dist_code . "and scheme_id = " . $scheme_id . " ";
          if (!is_null($ben_id)) {
            $query .= " and id = " . $ben_id . "";
          }
          if (!is_null($scheme_id)) {
            $query .= " and scheme_id = " . $scheme_id . " ";
          }
          if (!is_null($map_block_ulb)) {
            $query .= " and created_by_local_body_code = " . $map_block_ulb . "";
          }
        } else {
          $query = "select * from " . $table_name . " where created_by_dist_code = " . $dist_code . " ";
          if ($first_name != '') {
            $query .= " and ben_fname ILIKE '" . $first_name . "%' ";
          }
          if ($middle_name != '') {
            $query .= " and ben_mname ILIKE '" . $middle_name . "%' ";
          }
          if ($last_name != '') {
            $query .= " and ben_lname ILIKE '" . $last_name . "%' ";
          }
          if (!is_null($scheme_id)) {
            $query .= " and scheme_id = " . $scheme_id . " ";
          }
          if (!is_null($map_block_ulb)) {
            $query .= " and created_by_local_body_code = " . $map_block_ulb . "";
          }

        }
        // dump( $query);
        // die();
        $data = DB::connection('pgsql5')->select($query);
        // print_r($result);die();
        return datatables()->of($data)
          ->addColumn('ben_name', function ($data) {
            return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
          })
          ->addColumn('f_name', function ($data) {
            return $data->father_fname . ' ' . $data->father_mname . ' ' . $data->father_lname;
          })
          ->addColumn('ration_card', function ($data) {
            return $data->ration_card_cat . ' ' . $data->ration_card_no;
          })
          ->addColumn('bank_details', function ($data) {
            $html = '';
            $html = '<div align="center" class="text-success"><b>IFSC: ' . $data->bank_ifsc . ' </b></div>
                <div align="center" style="border: 1px solid #000;padding: 5px;border-radius: 5px; background-color: #fffaeb;"><b>Acc No: ' . $data->bank_code . '</b></div>
                <div align="center" class="text-danger"><i><b>';
            if ($data->lot_generated == -1 && $data->bank_edited == 0) {
              $html .= 'Under IFMS Modification from Block/Sub-Division';
            } else if ($data->lot_generated == -2 && $data->bank_edited == 0) {
              $html .= 'Under RBI Modification from Block/Sub-Division';
            } else if ($data->lot_generated == -3 && $data->bank_edited == 0) {
              $html .= 'Under SBI Modification from Block/Sub-Division';
            } else {
            }

            $html .= '</b></i></div';
            return $html;
          })
          ->addColumn('edit', function ($data) use ($designation, $scheme_id) {
            $html = '';
            //  && is_null($data->payment_suspended)
            if ($data->next_level_role_id == '0' && is_null($data->next_level_stop_payment) && $data->dup_bank == '0') {
              // <option value="update_mobile">Mobile Number</option>
              $options = '';
              if ($designation == 'Verifier' || $designation == 'Delegated Verifier') {
                if ($scheme_id == 1 || $scheme_id == 3 || $scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11 || $scheme_id == 19) {
                  $options = '<option value="stop_payment">Stop Payment</option>';
                }
              } else if ($designation == 'Approver' || $designation == 'Delegated Approver') {
                if ($data->payment_suspended == 1) {
                  $options = '<option value="stop_payment">Stop Payment</option>';
                } else {
                  $options = '<option value="stop_payment">Stop Payment</option>
                  <option value="bank">Update Bank Details</option>';
                }
              }
              $html = '
              <input type="hidden" name="select_item" class="itemUpdate" value="' . $data->id . '">
              <div>
                  <select class="form-control" name="select_item_update" id="select_item_update_' . $data->id . '" required>
                      <option value="">-- Select --</option>
                      ' . $options . '
                      
                  </select>
                  <span id="text_error" class="text-danger"></span>
              </div>
              <div align="center" style="margin-top:5px;">
                <button class="btn btn-info btn-block btn-sm" name="ben_edit" class="ben_edit" value="' . $data->id . '" onclick="editFunction(' . $data->id . ',' . $scheme_id . ');">Edit</button>
              </div>';
            } else if ($data->is_rejected == 1) {
              $html = '<span class="text-danger" style="font-weight: bold;">Inactive Beneficiary</span>';
            } else if (($data->is_verified == 1 && $data->is_approved == 0 && $data->is_rejected == 0) || ($data->is_verified == 0 && $data->is_approved == 0 && $data->is_rejected == 0)) {
              $html = '<span class="text-warning" style="font-weight: bold;">Under Approval</span>';
            } else if ($data->next_level_role_id == '0' && $data->next_level_stop_payment == 1) {
              $html = '<span class="text-warning" style="font-weight: bold;">Request has been send for approval</span>';
            } else if ($data->dup_bank == 1) {
              $html = '<span class="text-info" style="font-weight: bold;">It is under bank de-duplication process.<br>Please update/stop from bank de-duplication menu.</span>';
            }
            // else if ($data->payment_suspended == 1) {
            //   $html = '<span class="text-info" style="font-weight: bold;">Beneficiary Payment has been <br>Suspended due to Death case<br>(As per the data Comes from<br> Janma-Mrityu Portal).</span>';
            // }
            else {
            }
            return $html;
          })
          ->addColumn('action', function ($data) {
            $html = '';
            if ($data->next_level_role_id == -98) {
              $html = '<button class="btn btn-info resume_button" value="' . $data->id . '_' . $data->scheme_id . '">Resume</button>';
              //  $html = '<h5><label class="label label-warning"><b>Under Maintenance</b></label></h5>';
            } else if ($data->next_level_role_id == 0 and ($data->scheme_id == 8 or $data->scheme_id == 9 or $data->scheme_id == 17)) {
              if (is_null($data->payment_suspended)) {
                $html = '<button class="btn btn-success pause_button" value="' . $data->id . '_' . $data->scheme_id . '">Pause</button>';
              } else {
                $html = '';
              }

              //  $html = '<button class="btn btn-success pause_button" value="' . $data->id . '_' . $data->scheme_id . '">Pause</button>';
              //  $html = '<h5><label class="label label-warning"><b>Under Maintenance</b></label></h5>';
            } else {
            }
            return $html;
          })
          ->rawColumns(['ben_name', 'f_name', 'ration_card', 'bank_details', 'edit', 'action'])
          ->make(true);
      }

    }
  }
  /*
  Get Modal Opening Data
  */
  public function getModalDataUpdateStop(Request $request)
  {
    // dd($request->all());
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $id = $request->id;
      $op_type = $request->op_type;
      $scheme_id = $request->scheme_id;

      // $table = $this->getSchemaName($scheme_id);
      // $table = 'pension.beneficiaries';
      // $ben_details = DB::connection('pgsql')->table($table)->find($id);
      $ben_details = BenEntry::where('id', $id)->first();      // dd($ben_details);
      if ($ben_details == null) {
        return $response = array(
          'status' => 1,
          'msg' => 'Somethimg went wrong .',
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      } else {
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
          'mobile_no' => trim($ben_details->mobile_no)
        );
        // dd($ben_arr);
        if ($op_type == 'bank') {
          // For Lot generated < 0 Date:15/09/2020 --- re updated on 12-11-2020 upon request to give edit power to Approver.
          // if ($ben_details->lot_generated < -10) {
          // $response = array('status' => 4, 'ben_data' => $ben_details, 'type' => $op_type, 'lot_msg' => 'This beneficiary under payment modification');
          // }
          $response = array_merge($ben_arr, array('status' => 2, 'type' => $op_type));
        } else if ($op_type == 'stop_payment') {
          $query = "select * from public.m_attached_doc where id>=100";
          $doc_type = DB::connection('pgsql')->select($query);
          $response = array_merge($ben_arr, array('status' => 3, 'doc_type' => $doc_type, 'type' => $op_type));
        } else if ($op_type == 'update_mobile') {
          $response = array_merge($ben_arr, array('status' => 4, 'type' => $op_type));
        }
      }
    } catch (\Exception $e) {
      // dd($e);
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
  public function updateBenBankDetails(Request $request)
  {
    $user_id = AuthChecker::getUserId();
    $scheme_id = $request->scheme_id;
    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();
    ;
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
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $doc_arr = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->where('id', 10)->first();
      $doc_type = $doc_arr->id;
      $rules = array(
        'bank_name' => 'required|max:200',
        'branch_name' => 'required|max:200',
        'bank_ifsc' => 'required|max:11',
        'bank_code' => 'required|max:20',
        'mobile_no' => 'required|max:10',
        'remarks' => 'required|max:200',
        'upload_bank_passbook' => 'required|mimes:' . $doc_arr->doc_type . '|max:' . $doc_arr->doc_size_kb . ','
      );
      $attributes = [
        'bank_name' => 'Bank name',
        'branch_name' => 'Branch name',
        'bank_ifsc' => 'IFSC',
        'bank_code' => 'A/c No',
        'mobile_no' => 'Mobile No',
        'remarks' => 'Remarks',
        'upload_bank_passbook' => 'upload_bank_passbook'
      ];
      $messages = [
        'required' => 'The :attribute field is required.',
        'integer' => 'Only integer allowed for :attribute',
        'max' => 'Maximum of :size characters allowed for :attribute',
        'size' => 'The :attribute must be exactly :size.',
        'upload_bank_passbook.max' => "The file uploaded for " . $doc_arr->doc_name . " size must be less than :max KB",
        'upload_bank_passbook.mimes' => "The file uploaded for " . $doc_arr->doc_name . " must be of type " . $doc_arr->doc_type,
        'upload_bank_passbook.required' => "Document for " . $doc_arr->doc_name . " must be uploaded"
      ];
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $table = $this->getSchemaName($scheme_id);
        $id = $request->id;
        $ip_address = request()->ip();
        $new_bank_name = $request->bank_name;
        $new_branch_name = $request->branch_name;
        $new_bank_ifsc = $request->bank_ifsc;
        $new_bank_code = $request->bank_code;
        $new_mobile_no = $request->mobile_no;
        $remarks = $request->remarks;
        $benDetails = DB::connection('pgsql')->table($table)->where('id', $id)->first();
        $failed_count = DB::connection('pgsql')->table($table)->where('id', $id)->whereIn('is_bank_failed',[1,2,3])->count();
        $failed_details = DB::connection('pgsql')->table($table)->where('id', $id)->whereIn('is_bank_failed',[1,2,3])->first();
        $ben_details = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('scheme_id', $scheme_id)->where('ben_id', $id)->first();
        if ($ben_details == NULL) {
          return $response = [
            'status' => 1,
            'msg' => 'That beneficiary not migrated yet.',
            'type' => 'red',
            'icon' => 'fa fa-warning',
            'title' => 'Warning!!',
          ];
        }
        
          // if($failed_count>0){
          //   if($ben_details->legacy_validation == 0 )
          //   {
          //     $failed_type = $failed_details->is_bank_failed;
          //     if($failed_type == 1){
          //       $msg = 'Payment Transaction Failed';
          //     }
          //     if($failed_type == 2){
          //       $msg = 'Name Validation Failed';
          //     }
          //     if($failed_type == 3){
          //       $msg = 'Account Validation Failed';
          //     }
          //     return $response = array(
          //       'status' => 3,
          //       'msg' => 'This Beneficiary is '.$msg.'.Please correct failed details from Verifier end.',
          //       'type' => 'blue',
          //       'icon' => 'fa fa-warning',
          //       'title' => 'Warning!!'
          //     );
          //   }
          //   if($ben_details->legacy_validation == 1 )
          //   {
          //     $failed_type = $failed_details->is_bank_failed;
          //     if($failed_type == 1){
          //       $msg = 'Payment Transaction Failed';
          //       return $response = array(
          //         'status' => 3,
          //         'msg' => 'This Beneficiary is '.$msg.'.Please correct failed details from Verifier end.',
          //         'type' => 'blue',
          //         'icon' => 'fa fa-warning',
          //         'title' => 'Warning!!'
          //       );
          //     }
          //   }
          // }
          
        // Checking Duplicate A/c And IFSC
        $scheme_list = Config::get('constants.duplicate_bank_info_check');
        if (in_array($scheme_id, $scheme_list)) {
          if ($scheme_id == 8 || $scheme_id == 9) {
            $benDuplicateAcCount1 = DB::connection('pgsql')->table("pension.beneficiaries")->select('id')
              ->whereRaw("trim(bank_code)=trim(" . "'" . $new_bank_code . "'" . ") and (is_rejected=0 or is_rejected IS NULL)");
            $benDuplicateAcCount = DB::connection('pgsql')->table("pension.beneficiaries")->select('id')
              ->whereRaw("trim(bank_code)=trim(" . "'" . $new_bank_code . "'" . ") and (is_rejected=0 or is_rejected IS NULL)")
              ->union($benDuplicateAcCount1)->count('id');
          } else {
            $benDuplicateAcCount = DB::connection('pgsql')->table($table)
              // ->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")
              ->whereRaw("trim(bank_code)=trim(" . "'" . $new_bank_code . "'" . ") and (is_rejected=0 or is_rejected IS NULL)")
              ->where('scheme_id', $scheme_id)->count('id');
          }
          if ($benDuplicateAcCount > 0) {
            if ($scheme_id == 8 || $scheme_id == 9) {
              $msg = 'This Bank A/c - ' . $new_bank_code . ' & IFSC - ' . $new_bank_ifsc . ' already exist LPP Retainer or LPP Pensioner scheme';
            } else {
              $msg = 'This Bank A/c - ' . $new_bank_code . ' & IFSC - ' . $new_bank_ifsc . ' already exist in this scheme';
            }
            return $response = array(
              'status' => 3,
              'msg' => $msg,
              'type' => 'blue',
              'icon' => 'fa fa-warning',
              'title' => 'Warning!!'
            );
          }
        }

        if ($scheme_id == 10) {
          if (!empty($new_bank_code)) {
            $DupCheckBankWP = DupCheck::getDupCheckBank(11, $new_bank_code);
            if (!empty($DupCheckBankWP)) {
              $msg = 'Duplicate Bank Account Number present in Widow Pension Scheme with Beneficiary ID- ' . $DupCheckBankWP . '';
              return $response = array(
                'status' => 3,
                'msg' => $msg,
                'type' => 'blue',
                'icon' => 'fa fa-warning',
                'title' => 'Warning!!'
              );
            }
            $DupCheckBankLB = DupCheck::getDupCheckBank(20, $new_bank_code);
            if (!empty($DupCheckBankLB)) {
              $msg = 'Duplicate Bank Account Number present in Lakshmir Bhandar Scheme with Application ID- ' . $DupCheckBankLB . '';
              return $response = array(
                'status' => 3,
                'msg' => $msg,
                'type' => 'blue',
                'icon' => 'fa fa-warning',
                'title' => 'Warning!!'
              );
            }
          }
        }
        if ($scheme_id == 11) {
          if (!empty($new_bank_code)) {
            $DupCheckBankOAP = DupCheck::getDupCheckBank(10, $new_bank_code);
            if (!empty($DupCheckBankOAP)) {
              $msg = 'Duplicate Bank Account Number present in Old Age Pension Scheme with Beneficiary ID- ' . $DupCheckBankOAP . '';
              return $response = array(
                'status' => 3,
                'msg' => $msg,
                'type' => 'blue',
                'icon' => 'fa fa-warning',
                'title' => 'Warning!!'
              );
            }
          }
        }
        if ($scheme_id == 1 || $scheme_id == 3) {
          if (!empty($new_bank_code)) {
            $DupCheckBankLB = DupCheck::getDupCheckBank(20, $new_bank_code);
            if (!empty($DupCheckBankLB)) {
              $msg = 'Duplicate Bank Account Number present in Lakshmir Bhandar Scheme with Application ID- ' . $DupCheckBankLB . '';
              return $response = array(
                'status' => 3,
                'msg' => $msg,
                'type' => 'blue',
                'icon' => 'fa fa-warning',
                'title' => 'Warning!!'
              );
            }
            $DupCheckBankOAP = DupCheck::getDupCheckBank(10, $new_bank_code);
            if (!empty($DupCheckBankOAP)) {
              $msg = 'Duplicate Bank Account Number present in Old Age Pension Scheme with Beneficiary ID- ' . $DupCheckBankOAP . '';
              return $response = array(
                'status' => 3,
                'msg' => $msg,
                'type' => 'blue',
                'icon' => 'fa fa-warning',
                'title' => 'Warning!!'
              );
            }
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
        DB::connection('pgsql_encwrite')->beginTransaction();
        DB::connection('pgsql_paywrite')->beginTransaction();
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
          if ($function_parameter_bank <> '') {
            $parName = $function_parameter_bank;
          }
          if ($function_parameter_mobile <> '') {
            $parName .= $function_parameter_mobile;
          }
          $parName = rtrim($parName, ", ");
          // print $parName;die;
          // $callFunction = DB::select(DB::raw("SELECT " . strtolower($schema_name) . ".duplicate_bank_mobile_check(in_scheme_id => " . $scheme_id . ", " . $parName . ")"));
          // if (isset($callFunction)) {
          //   $return_fun = $callFunction[0]->duplicate_bank_mobile_check;

          //   if ($return_fun <> 1) {
          //     return $response = array(
          //       'status' => 3, 'msg' => $message_arr[$return_fun],
          //       'type' => 'orange', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
          //     );
          //   }
          // }
        }
        $bank_details = BankDetails::where('ifsc', trim($new_bank_ifsc))->where('is_active', 1)->get(['bank', 'branch', 'bank_code'])->first();
        if ($bank_details == NULL) {
          return $response = [
            'status' => 1,
            'msg' => 'IFSC not found',
            'type' => 'red',
            'icon' => 'fa fa-warning',
            'title' => 'Warning!!',
          ];
        }
        $new_bank_code_npci = $bank_details->bank_code;
        $old_value = [];
        $input = [];
        $ben_payment = [];
        $old_value['bank_name'] = $old_bank_name;
        $input['bank_name'] = $new_bank_name;
        $old_value['branch_name'] = $old_branch_name;
        $input['branch_name'] = $new_branch_name;
        $old_value['bank_ifsc'] = $old_bank_ifsc;
        $input['bank_ifsc'] = $new_bank_ifsc;
        $old_value['bank_code'] = $old_bank_code;
        $input['bank_code'] = $new_bank_code;

        if ($new_mobile_no != $old_mobile_no) {
          $old_value['mobile_no'] = $old_mobile_no;
          $input['mobile_no'] = $new_mobile_no;
        }

        // $updateBenDetailsData = [
        //   'original_application_id' => $benDetails->id,
        //   'dist_code' => $benDetails->dist_code,
        //   'scheme_id' => $benDetails->scheme_id,
        //   'remarks' => $remarks,
        //   'old_data' => json_encode($old_value),
        //   'new_data' => json_encode($input),
        //   'user_id' => Auth::user()->id,
        //   'update_code' => 1,
        //   'created_at' => date('Y-m-d H:i:s'),
        //   'updated_at' => date('Y-m-d H:i:s'),
        //   'ip_address' => $ip_address,
        //   'action_by' => $user_id,
        //   'action_ip_address' => $request->ip(),
        //   'action_type' => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod()

        // ];
        // $update_ben = [
        //   'bank_name' => $new_bank_name,
        //   'branch_name' => $new_branch_name,
        //   'bank_ifsc' => trim($new_bank_ifsc),
        //   'bank_code' => trim($new_bank_code),
        //   'mobile_no' => trim($new_mobile_no),
        //   'bank_edited' => 1,
        //   'acc_validated' => 0,
        //   'lb_acc_validated' => 0,
        //   'npci_bank_code' => $new_bank_code_npci
        // ];
        // $ben_payment['last_accno'] = trim($new_bank_code);
        // $ben_payment['last_ifsc'] = trim($new_bank_ifsc);
        // $ben_payment['npci_bank_code'] = trim($new_bank_code_npci);
        // $ben_payment['mobile_no'] = trim($new_mobile_no);
        // $ben_payment['acc_validated'] = 0;
        // $ben_payment['legacy_validation'] = 0;
        // $ben_payment['dup_bank'] = 0;
        // $ben_payment['updated_at'] = date('Y-m-d H:i:s');
        // dd($ben_payment);
        
        if (!empty($request->file('upload_bank_passbook'))) {
          $upload_bank_passbook = $request->file('upload_bank_passbook');
          $img_data = file_get_contents($upload_bank_passbook);
          $extension = $upload_bank_passbook->getClientOriginalExtension();
          $mime_type = $upload_bank_passbook->getMimeType();
          $base64 = base64_encode($img_data);
          $c_datetime = date('Y-m-d H:i:s', time());
          $fun_call = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
              in_beneficiary_id => " . $id . ",
              in_scheme_id => " . $scheme_id . ",
              in_document_type => " . $doc_type . ",
              in_attched_document => '" . $base64 . "',
              in_created_by_level => '" . $mapping_level . "',
              in_created_by => " . $user_id . ",
              in_ip_address => '" . $ip_address . "',
              in_document_extension => '" . $extension . "',
              in_document_mime_type => '" . $mime_type . "',
              in_created_by_dist_code => " . $benDetails->created_by_dist_code . ",
              in_created_by_local_body_code => " . $benDetails->created_by_local_body_code . ",
              in_doc_type_name => '" . $doc_arr->doc_name . "',
              in_datetime => '" . $c_datetime . "'
              );"
          );
          $is_upload = $fun_call[0]->ben_docs_insert_archive;
          if ($is_upload == 1) {
            // $is_update = UpdateBenDetails::insert($updateBenDetailsData);
            $update_log = new AcceptRejectInfo;
            $update_log->application_id = $benDetails->id;
            $update_log->op_type = '1';
            $update_log->created_by_dist_code = $benDetails->dist_code;
            $update_log->scheme_id = $benDetails->scheme_id;
            $update_log->remarks = $remarks;
            $update_log->old_data = json_encode($old_value);
            $update_log->new_data = json_encode($input);
            $update_log->user_id = Auth::user()->id;
            $update_log->created_at = date('Y-m-d H:i:s');
            $update_log->updated_at = date('Y-m-d H:i:s');
            $update_log->ip_address = $ip_address;
            $update_log->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
            $is_update = $update_log->save();
            if($failed_count >0){
              $is_failed_update = DB::connection('pgsql_paywrite')
              ->table('payment.failed_payment_details')
              ->where('scheme_id', $scheme_id)
              ->where('ben_id', $id)
              ->whereIn('edited_status', [0, 1])
              ->update(['approve_edited_status' => 1,'edited_status' => 2]);
            }

            if ($is_update) {
              $table = $this->getSchemaName($scheme_id);
              // $is_saved = DB::table($table)->where('id', $id)->update($update_ben);
              $ben_entry_module = BenEntry::where('id', $id)->first();

              $ben_entry_module->bank_name = $new_bank_name;
              $ben_entry_module->branch_name = $new_branch_name;
              $ben_entry_module->bank_ifsc = trim($new_bank_ifsc);
              $ben_entry_module->bank_code = trim($new_bank_code);
              $ben_entry_module->mobile_no = trim($new_mobile_no);
              $ben_entry_module->bank_edited = 1;
              $ben_entry_module->is_bank_failed = 0;
              $ben_entry_module->pay_validated = 0;
              $ben_entry_module->is_clean = 1;
              $ben_entry_module->next_level_clean_id = 1;
              $ben_entry_module->acc_validated = 0;
              $ben_entry_module->lb_acc_validated = 0;
              $ben_entry_module->npci_bank_code = trim($new_bank_code_npci);
              $is_saved = $ben_entry_module->save();
              if ($is_saved) {
                // $is_failed_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $id)->where('scheme_id', $scheme_id)->update($ben_payment);
                $ben_payment_model = BenPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->first();
                $ben_payment_model->last_accno = trim($new_bank_code);
                $ben_payment_model->last_ifsc = trim($new_bank_ifsc);
                $ben_payment_model->npci_bank_code = trim($new_bank_code_npci);
                $ben_payment_model->mobile_no = trim($new_mobile_no);
                $ben_payment_model->acc_validated = 0;
                $ben_payment_model->pay_validated = 1;
                $ben_payment_model->legacy_validation = 0;
                $ben_payment_model->dup_bank = 0;
                $ben_payment_model->updated_at = date('Y-m-d H:i:s');
                $is_failed_update = $ben_payment_model->save();
                if ($is_failed_update) {
                  DB::commit();
                  DB::connection('pgsql_encwrite')->commit();
                  DB::connection('pgsql_paywrite')->commit();
                  $response = array(
                    'status' => 1,
                    'msg' => 'Bank Details Updated Successfully',
                    'type' => 'green',
                    'icon' => 'fa fa-check',
                    'title' => 'Success'
                  );
                } else {
                  DB::rollback();
                  DB::connection('pgsql_encwrite')->rollback();
                  DB::connection('pgsql_paywrite')->rollback();
                  $response = array(
                    'status' => 3,
                    'msg' => '3 Somethimg went wrong!!',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!'
                  );
                }
              } else {
                DB::rollback();
                DB::connection('pgsql_encwrite')->rollback();
                DB::connection('pgsql_paywrite')->rollback();
                $response = array(
                  'status' => 3,
                  'msg' => '3 Somethimg went wrong!!',
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!'
                );
              }
            } else {
              DB::rollback();
              DB::connection('pgsql_encwrite')->rollback();
              DB::connection('pgsql_paywrite')->rollback();
              $response = array(
                'status' => 2,
                'msg' => '2 Somethimg went wrong!!',
                'type' => 'red',
                'icon' => 'fa fa-warning',
                'title' => 'Warning!!'
              );
            }
          } else {
            DB::connection('pgsql')->rollback();
            DB::connection('pgsql_encwrite')->rollback();
            DB::connection('pgsql_paywrite')->rollback();
            $return_status = 0;
            $return_text = 'Something went wrong, please try another one...';
            $return_msg = array("" . $return_text);
            return $response = array(
              'status' => $return_status,
              'msg' => $return_msg,
              'type' => 'red',
              'icon' => 'fa fa-warning',
              'title' => 'Warning!!'
            );
          }
        } else {
          $response = array(
            'status' => 2,
            'msg' => 'Please upload bank passbook copy.',
            'type' => 'red',
            'icon' => 'fa fa-warning',
            'title' => 'Required'
          );
        }
      } else {
        $return_status = 0;
        $return_msg = $validator->errors()->all();
        $response = array(
          'status' => $return_status,
          'msg' => $return_msg,
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }
    } catch (\Exception $e) {
      // dd($e);
      // if ($id == 582196) {
      //   dd($e);
      // }
      DB::rollback();
      DB::connection('pgsql_encwrite')->rollback();
      DB::connection('pgsql_paywrite')->rollback();
      $response = array(
        'exception' => true,
        // 'exception_message' => $e->getMessage(),
        'exception_message' => 'Something went wrong. May be session time out logout and login again..',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }
  /*
    Stop Payment
  */
  public function stopPaymentBenDetails(Request $request)
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
        'stop_remarks' => 'required|max:200',
        'stop_payment_reason' => 'required|max:200',
        'file_stop_payment' => 'mimes:pdf,jpg,jpeg,png|max:500|nullable'
      );
      $attributes = [
        'stop_remarks' => 'Remarks',
        'stop_payment_reason' => 'Stop reason',
        'file_stop_payment' => 'File'
      ];
      $messages = [
        'required' => 'The :attribute field is required.',
        'mimes' => 'Only :attribute allowed',
        'max' => 'Maximum of :max kb(for file)/characters allowed for :attribute',
        'size' => 'The :attribute must be exactly :size.',
      ];

      // dd('ok');
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $scheme_id = $request->scheme_id;
        $designation = Auth::user()->designation_id;
        $user_id = AuthChecker::getUserId();
        // dd($scheme_id);
        $table = $this->getSchemaName($scheme_id);
        $schemaarr = explode('.', $table);
        $schema = $schemaarr[0];


        $user_id = AuthChecker::getUserId();
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get();
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

        $id = $request->id;
        $remarks = $request->stop_remarks;
        $stop_payment_reason = $request->stop_payment_reason;
        $doc_types = DB::connection('pgsql')->select("select * from public.m_attached_doc where id=" . $stop_payment_reason);
        $stop_details = DB::connection('pgsql')->table($table)->where('id', $id)->first();
        $input_json = [];
        $input_json['stop_payment_reason'] = $stop_payment_reason;
        $input_json[$table . '.next_level_role_id'] = '-99';
        $input_json['designation'] = $designation;

        DB::beginTransaction();
        DB::connection('pgsql_encwrite')->beginTransaction();
        DB::connection('pgsql_paywrite')->beginTransaction();
        if ($request->hasFile('file_stop_payment')) {
          $input = [];
          $image = $request->file('file_stop_payment');
          // $input['imagename'] = time() . '.' . $image->getClientOriginalExtension();
          // $destinationPath = storage_path('app/stop_payment/' . $stop_details->dist_code . '/scheme_id_' . $stop_details->scheme_id);
          // $image->move($destinationPath, $input['imagename']);

          // dd($doc_file);
          $img_data = file_get_contents($image);
          $u_extension = $image->getClientOriginalExtension();
          $u_extension = strtolower($u_extension);
          $mime_type = $image->getMimeType();

          if (strtolower($mime_type) == 'image/jpeg') {
            if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
              $extension = $u_extension;
            } else {
              $errorMsg = "You are trying to upload an incorrect file for " . $doc_types[0]->doc_name;
              return $response = array(
                'status' => 8,
                'msg' => $errorMsg,
                'type' => 'red',
                'icon' => 'fa fa-warning',
                'title' => 'Warning!!'
              );
            }
          } else if (strtolower($mime_type) == 'image/png') {
            $extension = 'png';
          } else if (strtolower($mime_type) == 'image/gif') {
            $extension = 'gif';
          } else if (strtolower($mime_type) == 'application/pdf') {
            $extension = 'pdf';
          } else {
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_types[0]->doc_name;
            return $response = array(
              'status' => 9,
              'msg' => $errorMsg,
              'type' => 'red',
              'icon' => 'fa fa-warning',
              'title' => 'Warning!!'
            );
          }
          if ($u_extension != $extension) {
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_types[0]->doc_name;
            return $response = array(
              'status' => 10,
              'msg' => $errorMsg,
              'type' => 'red',
              'icon' => 'fa fa-warning',
              'title' => 'Warning!!'
            );
          }

          $base64 = base64_encode($img_data);
          $ip_address = request()->ip();
          $c_datetime = date('Y-m-d H:i:s', time());

          $arch_parameters = array();
          $arch_parameters['beneficiary_id'] = $id;
          $arch_parameters['scheme_id'] = $scheme_id;
          $arch_parameters['document_type'] = $doc_types[0]->id;
          $arch_parameters['attched_document'] = $base64;
          $arch_parameters['created_by_level'] = $mapping_level;
          $arch_parameters['created_by'] = $user_id;
          $arch_parameters['ip_address'] = $ip_address;
          $arch_parameters['document_extension'] = $extension;
          $arch_parameters['document_mime_type'] = $mime_type;
          $arch_parameters['created_by_dist_code'] = $stop_details->created_by_dist_code;
          $arch_parameters['created_by_local_body_code'] = $stop_details->created_by_local_body_code;
          $arch_parameters['doc_type_name'] = $doc_types[0]->doc_name;
          $arch_parameters['is_stop_payment_file'] = TRUE;
          $arch_parameters['created_at'] = $c_datetime;
          $arch_parameters['updated_at'] = $c_datetime;

          // $lotDetailsUpdate=1;
          $lotDetailsUpdate = DB::select('SELECT public."payment_adjustment"(' . $id . ', -99)');
          if ($lotDetailsUpdate) {

            // $logUpdate = DB::insert("INSERT INTO " . $schema . ".ben_docs(ben_id, doc_type_id, doc_name, doc_type_name, is_active, created_at)
            // VALUES ( " . $id . "," . $doc_types[0]->id . ",'https://jaibangla.wb.gov.in/images_stopped/" . $input['imagename'] . "','" . str_replace("'","''",$doc_types[0]->doc_name) . "',true, now());");

            $logUpdate = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_arch')->insert($arch_parameters);

            if ($logUpdate) {
              // $updateBenDetails=1;
              // $updateBenDetails = DB::insert("INSERT INTO public.update_ben_details(original_application_id, dist_code, scheme_id, user_id, created_at,remarks, update_code, new_data, ip_address)
              // VALUES (" . $id . "," . $stop_details->dist_code . "," . $stop_details->scheme_id . "," . $user_id . ",now(),'" . $remarks . "',2,'" . json_encode($input_json) . "', '" . $ip_address . "' );");

              $accept_reject_model = new AcceptRejectInfo();
              $accept_reject_model->application_id = $id;
              $accept_reject_model->created_by_dist_code = $stop_details->dist_code;
              $accept_reject_model->scheme_id = $stop_details->scheme_id;
              $accept_reject_model->user_id = $user_id;
              $accept_reject_model->created_at = date('Y-m-d H:i:s');
              $accept_reject_model->remarks = $remarks;
              $accept_reject_model->op_type = '2';
              $accept_reject_model->new_data = json_encode($input_json);
              $accept_reject_model->ip_address = $ip_address;
              $updateBenDetails = $accept_reject_model->save();
              if ($updateBenDetails) {
                if (($scheme_id == '2' || $scheme_id == '10' || $scheme_id == '11') && ($designation == 'Verifier' || $designation == 'Delegated Verifier')) {
                  // $update_array = ['next_level_stop_payment' => 1];
                  $message = 'Beneficiary Stopped Payment Request Send For Approval';
                  // $is_update = DB::table($table)->where('id', $id)->update($update_array);
                  $ben_entry_model = BenEntry::where('id', $id)->first();
                  $ben_entry_model->next_level_stop_payment = 1;

                  $is_update = $ben_entry_model->save();
                  $final_update = 1;
                } else {
                  // $update_array = ['next_level_role_id' => -99, 'is_approved' => 2, 'is_verified' => 2, 'is_rejected' => 1];
                  $message = 'Beneficiary Stopped Successfully';
                  $ben_details = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $id)->where('scheme_id', $scheme_id)->count();
                  if ($ben_details > 0) {
                    $final_update = DB::connection('pgsql_paywrite')->select("Select payment.reject_update_bank(in_ben_id => ARRAY[" . $id . "], in_scheme_id => " . $scheme_id . ", in_rejected =>12)");
                  } else {
                    $final_update = 1;
                  }
                  $ben_entry_model = BenEntry::where('id', $id)->first();
                  $ben_entry_model->next_level_role_id = -99;
                  $ben_entry_model->is_approved = 2;
                  $ben_entry_model->is_verified = 2;
                  $ben_entry_model->is_rejected = 1;
                  $ben_entry_model->is_clean = 10;
                  $is_update = $ben_entry_model->save();
                  // $is_update = DB::table($table)->where('id', $id)->update($update_array);
                }

                // dd($is_update, $final_update);
                // die();
                if ($is_update && $final_update) {
                  DB::commit();
                  DB::connection('pgsql_encwrite')->commit();
                  DB::connection('pgsql_paywrite')->commit();
                  $response = array(
                    'status' => 1,
                    'msg' => $message,
                    'type' => 'green',
                    'icon' => 'fa fa-check',
                    'title' => 'Success'
                  );
                } else {
                  DB::rollback();
                  DB::connection('pgsql_encwrite')->rollback();
                  DB::connection('pgsql_paywrite')->rollback();
                  $response = array(
                    'status' => 3,
                    'msg' => '3 Somethimg went wrong..',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!'
                  );
                }

              } else {
                DB::rollback();
                DB::connection('pgsql_encwrite')->rollback();
                DB::connection('pgsql_paywrite')->rollback();
                $response = array(
                  'status' => 4,
                  'msg' => '4 Somethimg went wrong..',
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!'
                );
              }
            } else {
              DB::rollback();
              DB::connection('pgsql_encwrite')->rollback();
              DB::connection('pgsql_paywrite')->rollback();
              $response = array(
                'status' => 5,
                'msg' => '5 Somethimg went wrong..',
                'type' => 'red',
                'icon' => 'fa fa-warning',
                'title' => 'Warning!!'
              );
            }
          } else {
            DB::rollback();
            DB::connection('pgsql_encwrite')->rollback();
            DB::connection('pgsql_paywrite')->rollback();
            $response = array(
              'status' => 6,
              'msg' => '6 Somethimg went wrong..',
              'type' => 'red',
              'icon' => 'fa fa-warning',
              'title' => 'Warning!!'
            );
          }
        } else {
          $response = array(
            'status' => 2,
            'msg' => 'Please upload one document for stop payment',
            'type' => 'red',
            'icon' => 'fa fa-warning',
            'title' => 'Warning!!'
          );
        }
      } else {
        $return_status = 0;
        $return_msg = $validator->errors()->all();
        $response = array(
          'status' => $return_status,
          'msg' => $return_msg,
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }
    } catch (\Exception $e) {
      //  dd($e);
      DB::rollback();
      DB::connection('pgsql_encwrite')->rollback();
      DB::connection('pgsql_paywrite')->rollback();
      $response = array(
        'exception' => true,
        // 'exception_message' => $e->getMessage(),
        'exception_message' => 'Something went wrong. May be session time out logout and login again...',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }

  public function lppPausePaymentDetails(Request $request)
  {
    // dd($request->all());
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $user_id = AuthChecker::getUserId();
      $id = $request->id;
      $scheme_id = $request->scheme_id;
      $ip_address = request()->ip();
      $table = $this->getSchemaName($scheme_id);
      $stop_details = DB::connection('pgsql')->table($table)->where('id', $id)->first();
      $input_json = [];
      $input_json[$table . '.next_level_role_id'] = '-98';
      $input_json['payment.ben_payment_details.ben_status'] = '3';
      DB::beginTransaction();
      DB::connection('pgsql_paywrite')->beginTransaction();
      $ben_details = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('scheme_id', $scheme_id)->where('ben_id', $id)->count();
      if ($ben_details == 1) {
        $ben_payment_details = BenPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->first();
        $ben_payment_details->ben_status = 3;
        $ben_payment_details->updated_at = date('Y-m-d H:i:s');
        $final_update = $ben_payment_details->save();
      } else {
        $final_update = 1;
      }
      // $is_paused = DB::select('SELECT public."payment_adjustment"(' . $id . ', -98)');
      // dd($final_update);
      $is_paused = 1;
      if ($is_paused) {
        // $updatebenUpdate=1;
        // $updatebenUpdate = DB::insert("INSERT INTO public.update_ben_details(original_application_id, dist_code, scheme_id, user_id, created_at,remarks, update_code, new_data, ip_address)
        // VALUES (" . $id . "," . $stop_details->dist_code . "," . $stop_details->scheme_id . "," . $user_id . ",now(),'Pause Payment',3,'" . json_encode($input_json) . "', '" . $ip_address . "' );");

        $accept_reject_model = new AcceptRejectInfo();
        $accept_reject_model->application_id = $id;
        $accept_reject_model->created_by_dist_code = $stop_details->dist_code;
        $accept_reject_model->scheme_id = $stop_details->scheme_id;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->created_at = date('Y-m-d H:i:s');
        $accept_reject_model->remarks = 'Pause Payment';
        $accept_reject_model->op_type = '3';
        $accept_reject_model->new_data = json_encode($input_json);
        $accept_reject_model->ip_address = $ip_address;
        $updatebenUpdate = $accept_reject_model->save();

        if ($updatebenUpdate) {
          // $is_saved=1;
          // $is_saved = DB::table($table)->where('id', $id)->update(['is_approved' => 2, 'is_verified' => 2, 'is_rejected' => 1, 'next_level_role_id' => -98]);
          $ben_entry_model = BenEntry::where('id', $id)->first();
          $ben_entry_model->is_approved = 2;
          $ben_entry_model->is_verified = 2;
          $ben_entry_model->is_rejected = 1;
          $ben_entry_model->next_level_role_id = -98;
          $ben_entry_model->is_clean = 10;
          $is_saved = $ben_entry_model->save();
          if ($is_saved && $final_update) {
            DB::commit();
            DB::connection('pgsql_paywrite')->commit();
            $response = array(
              'status' => 1,
              'msg' => 'Beneficiary Paused Successfully',
              'type' => 'green',
              'icon' => 'fa fa-check',
              'title' => 'Success'
            );
          } else {
            DB::rollback();
            DB::connection('pgsql_paywrite')->rollback();
            $response = array(
              'status' => 2,
              'msg' => '2 Somethimg went wrong..',
              'type' => 'red',
              'icon' => 'fa fa-warning',
              'title' => 'Warning!!'
            );
          }
        } else {
          DB::rollback();
          DB::connection('pgsql_paywrite')->rollback();
          $response = array(
            'status' => 3,
            'msg' => '3 Somethimg went wrong..',
            'type' => 'red',
            'icon' => 'fa fa-warning',
            'title' => 'Warning!!'
          );
        }
      } else {
        DB::rollback();
        DB::connection('pgsql_paywrite')->rollback();
        $response = array(
          'status' => 4,
          'msg' => '4 Somethimg went wrong..',
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }
    } catch (\Exception $e) {
      DB::rollback();
      DB::connection('pgsql_paywrite')->rollback();
      $response = array(
        'exception' => true,
        // 'exception_message' => $e->getMessage(),
        'exception_message' => 'Something went wrong. May be session time out logout and login again....',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }

  public function lppResumePaymentDetails(Request $request)
  {
    //  dd($request->all());
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $user_id = AuthChecker::getUserId();
      $id = $request->id;
      $scheme_id = $request->scheme_id;
      $ip_address = request()->ip();
      $table = $this->getSchemaName($scheme_id);
      $last_yymm = $request->resume_month;
      $stop_details = DB::connection('pgsql')->table($table)->where('id', $id)->first();
      $ben_details = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('scheme_id', $scheme_id)->where('ben_id', $id)->count();
      DB::beginTransaction();
      DB::connection('pgsql_paywrite')->beginTransaction();
      // $input = [
      //   'next_level_role_id' => 0,
      //   'is_approved' => 1,
      //   'is_verified' => 1,
      //   'is_rejected' => 0
      // ];
      $input_json = [];
      $input_json[$table . '.next_level_role_id'] = '0';
      $input_json['payment.ben_payment_details.ben_status'] = '1';
      if ($ben_details == 1) {
        // $final_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $id)->where('scheme_id', $scheme_id)->update(['ben_status' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
        $ben_payment_details = BenPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->first();
        $ben_payment_details->ben_status = 1;
        $ben_payment_details->updated_at = date('Y-m-d H:i:s');
        $final_update = $ben_payment_details->save();
      } else {
        $final_update = 1;
      }
      // $updatebenDe = DB::insert("INSERT INTO public.update_ben_details(original_application_id, dist_code, scheme_id, user_id, created_at,remarks, update_code, new_data, ip_address)
      //     VALUES (" . $id . "," . $stop_details->dist_code . "," . $stop_details->scheme_id . "," . $user_id . ",now(),'Resume Payment',4,'" . json_encode($input_json) . "' , '" . $ip_address . "');");

      $accept_reject_model = new AcceptRejectInfo();
      $accept_reject_model->application_id = $id;
      $accept_reject_model->created_by_dist_code = $stop_details->dist_code;
      $accept_reject_model->scheme_id = $stop_details->scheme_id;
      $accept_reject_model->user_id = $user_id;
      $accept_reject_model->created_at = date('Y-m-d H:i:s');
      $accept_reject_model->remarks = 'Resume Payment';
      $accept_reject_model->op_type = '4';
      $accept_reject_model->new_data = json_encode($input_json);
      $accept_reject_model->ip_address = $ip_address;
      $updatebenDe = $accept_reject_model->save();


      if ($updatebenDe) {
        // $is_saved =1; 
        // $is_saved = DB::table($table)->where(column: 'id', $id)->update($input);
        $ben_entry_model = BenEntry::where('id', $id)->first();
        $ben_entry_model->next_level_role_id = 0;
        $ben_entry_model->is_approved = 1;
        $ben_entry_model->is_verified = 1;
        $ben_entry_model->is_rejected = 0;
        $is_saved = $ben_entry_model->save();
        if ($is_saved && $final_update) {
          DB::commit();
          DB::connection('pgsql_paywrite')->commit();
          $response = array(
            'status' => 1,
            'msg' => 'Resume Successfully.',
            'type' => 'green',
            'icon' => 'fa fa-check',
            'title' => 'Success'
          );
        } else {
          DB::rollback();
          DB::connection('pgsql_paywrite')->rollback();
          $response = array(
            'status' => 2,
            'msg' => '2 Somethimg went wrong..',
            'type' => 'red',
            'icon' => 'fa fa-warning',
            'title' => 'Warning!!'
          );
        }
      } else {
        DB::rollback();
        DB::connection('pgsql_paywrite')->rollback();
        $response = array(
          'status' => 3,
          'msg' => '3 Somethimg went wrong..',
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }

    } catch (\Exception $e) {
      //  dd($e);
      DB::rollback();
      DB::connection('pgsql_paywrite')->rollback();
      $response = array(
        'exception' => true,
        // 'exception_message' => $e->getMessage(),
        'exception_message' => 'Something went wrong. May be session time out logout and login again.....',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }

  /*
    Update Mobile No
  */
  public function updateMobileBenDetails(Request $request)
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
      $table = $this->getSchemaName($scheme_id);
      $id = $request->id;
      $ip_address = request()->ip();
      $new_mobile_no = $request->mobile_no;
      $benDetails = DB::connection('pgsql')->table($table)->where('id', $id)->first();
      $remarks = $request->mobile_remarks;
      $old_mobile_no = $benDetails->mobile_no;

      $old_value = [];
      $input = [];
      $old_value['mobile_no'] = $old_mobile_no;
      $input['mobile_no'] = $new_mobile_no;

      $updateBenDetailsData = [
        'original_application_id' => $benDetails->id,
        'dist_code' => $benDetails->dist_code,
        'scheme_id' => $benDetails->scheme_id,
        'remarks' => $remarks,
        'old_data' => json_encode($old_value),
        'new_data' => json_encode($input),
        'user_id' => Auth::user()->id,
        'update_code' => 10,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'ip_address' => $ip_address
      ];
      // $update_ben = [
      //   'mobile_no' => trim($new_mobile_no)
      // ];

      /*--- Final Database Opertations ---*/
      DB::beginTransaction();
      // $is_update=1;
      // $is_update = UpdateBenDetails::insert($updateBenDetailsData);
      $ben_accept_model = new AcceptRejectInfo;
      $ben_accept_model->application_id = $benDetails->id;
      $ben_accept_model->created_by_dist_code = $benDetails->dist_code;
      $ben_accept_model->scheme_id = $benDetails->scheme_id;
      $ben_accept_model->user_id = Auth::user()->id;
      $ben_accept_model->old_data = json_encode($old_value);
      $ben_accept_model->new_data = json_encode($input);
      $ben_accept_model->op_type = '10';
      $ben_accept_model->created_at = date('Y-m-d H:i:s');
      $ben_accept_model->updated_at = date('Y-m-d H:i:s');
      $ben_accept_model->ip_address = $ip_address;
      $ben_accept_model->remarks = $remarks;
      $is_update = $ben_accept_model->save();
      if ($is_update) {
        // $is_saved=1;
        $ben_entry_model = BenEntry::where('id', $id)->first();
        $ben_entry_model->mobile_no = trim($new_mobile_no);
        $is_saved = $ben_entry_model->save();
        if ($is_saved) {
          DB::commit();
          $response = array(
            'status' => 1,
            'msg' => 'Mobile No. Updated Successfully',
            'type' => 'green',
            'icon' => 'fa fa-check',
            'title' => 'Success'
          );
        } else {
          DB::rollback();
          $response = array(
            'status' => 3,
            'msg' => '3 Somethimg went wrong!!',
            'type' => 'red',
            'icon' => 'fa fa-warning',
            'title' => 'Warning!!'
          );
        }
      } else {
        DB::rollback();
        $response = array(
          'status' => 2,
          'msg' => '2 Somethimg went wrong!!',
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!'
        );
      }
    } catch (\Exception $e) {
      DB::rollback();
      $response = array(
        'exception' => true,
        // 'exception_message' => $e->getMessage(),
        'exception_message' => 'Something went wrong. May be session time out logout and login again......',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }
  public function ajaxViewPassbook(Request $request)
  {
    $scheme_id = $request->scheme_id;
    $is_active = 0;
    $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $mapping_level = $roleObj['mapping_level'];
        $distCode = $roleObj['district_code'];
        $is_urban = $roleObj['is_urban'];
        if ($roleObj['is_urban'] == 1) {
          $blockCode = $roleObj['urban_body_code'];
        } else {
          $blockCode = $roleObj['taluka_code'];
        }
        break;
      }
    }
    if ($is_active == 0 || empty($distCode)) {
      return redirect("/")->with('error', 'User Disabled');
    }


    if (!empty($request->is_profile_pic))
      $is_profile_pic = $request->is_profile_pic;
    else
      $is_profile_pic = 0;
    $doc_type = $request->doc_type;
    $id = $request->id;
    if (empty($doc_type) || !ctype_digit($doc_type)) {
      $return_text = 'Parameter Not Valid1';
      return redirect("/")->with('error', $return_text);
    }
    if (!in_array($is_profile_pic, array(0, 1))) {
      $return_text = 'Parameter Not Valid2';
      return redirect("/")->with('error', $return_text);
    }
    if (empty($id)) {
      $return_text = 'Parameter Not Valid3';
      return redirect("/")->with('error', $return_text);
    }
    $user_id = AuthChecker::getUserId();
    $encolserData = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('document_type', $request->doc_type)->where('beneficiary_id', $id)->first();
    // dd($encolserData);
    if (empty($encolserData->beneficiary_id)) {
      $htmlText = 'No Document Found..';
      echo $htmlText;
      $return_text = 'Parameter Not Valid5';
      // return redirect("/")->with('error',  $return_text);
    } else {
      $file_extension = $encolserData->document_extension;
      $mime_type = $encolserData->document_mime_type;
      if ($file_extension != 'png' && $file_extension != 'jpg' && $file_extension != 'jpeg' && $file_extension != 'pdf') {
        if ($mime_type == 'image/png') {
          $file_extension = 'png';
        } else if ($mime_type == 'image/jpeg') {
          $file_extension = 'jpg';
        } else if ($mime_type == 'application/pdf') {
          $file_extension = 'pdf';
        }
      }
      try {
        if (strtoupper($file_extension) == 'PNG' || strtoupper($file_extension) == 'JPG' || strtoupper($file_extension) == 'JPEG') {
          $htmlText = '<image id="image" width="100%" height="100%" src="data:image/' . $file_extension . ';base64, ' . $encolserData->attched_document . '">';
          echo $htmlText;
        } else if (strtoupper($file_extension) == 'PDF') {
          $htmlText = '<embed type="text/html" width="100%" height="100%" src="data:application/pdf;base64, ' . $encolserData->attched_document . ' ">';
          echo $htmlText;
        }
      } catch (\Exception $e) {
        // dd($e);
        return redirect("/")->with('error', 'Some error.please try again ......');
      }
    }

  }
}
