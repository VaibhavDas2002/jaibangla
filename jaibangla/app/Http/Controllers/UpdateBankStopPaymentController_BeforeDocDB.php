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
  /*
		Get First Landing Page
  */
  public function index()
  {
    $user_id = Auth::user()->id;
    $designation = Auth::user()->designation_id_old;
    if ($designation == 'Approver' || $designation == 'Verifier') {
      $mapObj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
      if ($designation == 'Approver') {
        $scheme = Configduty::select('scheme_id')->where('user_id', $user_id)->where('is_active', 1)->get();
      } else if ($designation == 'Verifier') {
        $scheme = Configduty::select('scheme_id')->distinct()->where('user_id', $user_id)->where('is_active', 1)->whereIn('scheme_id', array(2,10,11,3))->get();
      }

      if (count($scheme) > 0) {
        return view('update-ben-details/index', ['schemes' => $scheme, 'mapping_level' => $mapObj->mapping_level, 'designation'=>$designation]);
      } else {
        return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
      }
    } else {
      return redirect("/")->with('success', 'Unauthorized.');
    }
  }

  public function searchByBenName(Request $request)
  {
    if ($request->ajax()) {
      $user_id = Auth::user()->id;
      $designation = Auth::user()->designation_id_old;
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
        $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
        //$parameter['scheme_id'] = $scheme_id;
        $schema_name =  $sObj->short_code;
        //dd($schema_name);
        if (empty($schema_name)) {
          $schema_name = 'pension';
        }
        $table_name =  $schema_name . '.beneficiary';
      } else {
        $table_name =  'pension.beneficiary';
      }

      if ($designation == 'Approver' || $designation == 'Verifier') {
        $type = $request->select_type;
        $first_name = strtoUpper(trim($request->ben_fname));
        $middle_name = strtoUpper(trim($request->ben_mname));
        $last_name = strtoUpper(trim($request->ben_lname));
        // $rural_urban = $request->is_rural_urban;
        // $block_ulb = $request->block_ulb;
        if ($type == 'b_id' && !is_null($ben_id)) {
          $query = "select * from " . $table_name . " where created_by_dist_code = " . $dist_code . " ";
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
        // print $query;die();
        $data = DB::connection('pgsql_mis')->select($query);
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
            if ($data->next_level_role_id == '0' && is_null($data->next_level_stop_payment)) {
              // <option value="update_mobile">Mobile Number</option>
              $options = '';
              if ($designation == 'Verifier') {
                if ($scheme_id == 3 || $scheme_id == 2 || $scheme_id == 10 || $scheme_id == 11) {
                  $options = '<option value="stop_payment">Stop Payment</option>';
                }
              }
              else if ($designation == 'Approver') {
                $options = '<option value="stop_payment">Stop Payment</option>
                <option value="bank">Update Bank Details</option>';
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
                <button class="btn btn-info btn-block btn-sm" name="ben_edit" class="ben_edit" value="' . $data->id . '" onclick="editFunction(' . $data->id . ','. $scheme_id .');">Edit</button>
              </div>';
            } else if ($data->is_rejected==1) {
              $html = '<h5><label class="label label-danger"><b>Inactive Beneficiary</b></lavel></h5>';
            } else if (($data->is_verified==1 and $data->is_approved==0 and $data->is_rejected==0) || (is_null($data->next_level_role_id))) {
              $html = '<h5><label class="label label-warning"><b>Under Approval</b></label></h5>';
            } else if ($data->next_level_role_id == '0' && $data->next_level_stop_payment==1) {
              $html = '<h5><label class="label label-warning"><b>Request has been send for approval</b></label></h5>';
            }
            else {
            }
            return $html;
          })
          ->addColumn('action', function ($data) {
            $html = '';
            if ($data->next_level_role_id == -98) {
              $html = '<button class="btn btn-info resume_button" value="' . $data->id . '_' . $data->lot_generated . '_' . $data->scheme_id . '">Resume</button>';
              // $html = '<h5><label class="label label-warning"><b>Under Maintenance</b></label></h5>';
            } else if ($data->next_level_role_id == 0 and ($data->scheme_id == 8 or $data->scheme_id == 9 or $data->scheme_id == 17)) {
              $html = '<button class="btn btn-success pause_button" value="' . $data->id . '_' . $data->scheme_id . '">Pause</button>';
              // $html = '<h5><label class="label label-warning"><b>Under Maintenance</b></label></h5>';
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
  //dd($request->all());
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
      $scheme_id=$request->scheme_id;

      $table = $this->getSchemaName($scheme_id);
      $ben_details = DB::connection('pgsql_mis')->table($table)->find($id);
      if ($ben_details == null) {
        return  $response = array(
          'status' => 1, 'msg' => 'Somethimg went wrong .',
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      } else {
        $ben_arr = array(
          'ben_name' => trim($ben_details->ben_fname) . ' ' . trim($ben_details->ben_mname) . ' ' . trim($ben_details->ben_lname), 'id' => $ben_details->id, 'scheme_id' => $ben_details->scheme_id,
          'father_name' => trim($ben_details->father_fname) . ' ' . trim($ben_details->father_mname) . ' ' . trim($ben_details->father_lname),
          'caste' => trim($ben_details->caste), 'gender' => trim($ben_details->gender),
          'dob' => date('d-m-Y', strtotime($ben_details->dob)),
          'bank_code' => trim($ben_details->bank_code), 'bank_ifsc' => trim($ben_details->bank_ifsc),
          'branch_name' => trim($ben_details->branch_name), 'bank_name' => trim($ben_details->bank_name), 'mobile_no' => trim($ben_details->mobile_no)
        );
        if ($op_type == 'bank') {
          // For Lot generated < 0 Date:15/09/2020 --- re updated on 12-11-2020 upon request to give edit power to Approver.
          // if ($ben_details->lot_generated < -10) {
          // $response = array('status' => 4, 'ben_data' => $ben_details, 'type' => $op_type, 'lot_msg' => 'This beneficiary under payment modification');
          // }
          $response = array_merge($ben_arr, array('status' => 2, 'type' => $op_type));
        } else if ($op_type == 'stop_payment') {
          $query = "select * from public.m_attached_doc where id>=100";
          $doc_type = DB::connection('pgsql_mis')->select($query);
          $response = array_merge($ben_arr, array('status' => 3, 'doc_type' => $doc_type, 'type' => $op_type));
        } else if ($op_type == 'update_mobile') {
          $response = array_merge($ben_arr, array('status' => 4, 'type' => $op_type));
        }
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
  public function updateBenBankDetails(Request $request)
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
        'bank_name' => 'required|max:200',
        'branch_name' => 'required|max:200',
        'bank_ifsc' => 'required|max:11',
        'bank_code' => 'required|max:20',
        'mobile_no' => 'required|max:10',
        'remarks' => 'required|max:200'
      );
      $attributes = [
        'bank_name' => 'Bank name',
        'branch_name' => 'Branch name',
        'bank_ifsc' => 'IFSC',
        'bank_code' => 'A/c No',
        'mobile_no' => 'Mobile No',
        'remarks' => 'Remarks'
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
        $id = $request->id;

        $new_bank_name = $request->bank_name;
        $new_branch_name = $request->branch_name;
        $new_bank_ifsc = $request->bank_ifsc;
        $new_bank_code = $request->bank_code;
        $new_mobile_no = $request->mobile_no;

        $remarks = $request->remarks;
        $benDetails = DB::connection('pgsql_mis')->table($table)->where('id', $id)->first();
        // Checking Duplicate A/c And IFSC
        $scheme_list = Config::get('constants.duplicate_bank_info_check');
       
        if (in_array($scheme_id, $scheme_list)) {
          if ($scheme_id == 8 || $scheme_id == 9) {
            // $benDuplicateAcCount = DB::connection('pgsql_mis')->table($table)->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")->whereRaw("trim(bank_code)=trim(" . "'" . $new_bank_code . "'" . ")")
            //   ->where('is_rejected',1)
            //   ->whereIn('scheme_id', [8, 9])->count('id');
            $benDuplicateAcCount1 = DB::connection('pgsql_mis')->table("lokprasar_retainer.beneficiary")->select('id')->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")->whereRaw("trim(bank_code)=trim(" . "'" . $new_bank_code . "'" . ")")
            ->where('is_rejected',1);
            $benDuplicateAcCount= DB::connection('pgsql_mis')->table("lokprasar_pensioner.beneficiary")->select('id')->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")->whereRaw("trim(bank_code)=trim(" . "'" . $new_bank_code . "'" . ")")
            ->where('is_rejected',1)->union($benDuplicateAcCount1)->count('id');
            
            // $TotalRecord="select id,trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . "),trim(bank_code)=trim(" . "'" . $new_bank_code . "'" . ") from lokprasar_retainer.beneficiary where is_rejected=1
            // union 
            // select id,trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . "),trim(bank_code)=trim(" . "'" . $new_bank_code . "'" . ") from lokprasar_pensioner.beneficiary where is_rejected=1";
            // $benDuplicateAcCount = DB::connection('pgsql_mis')->select($TotalRecord)->count('id');

          } else {
            $benDuplicateAcCount = DB::connection('pgsql_mis')->table($table)->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")->whereRaw("trim(bank_code)=trim(" . "'" . $new_bank_code . "'" . ")")
              ->where('is_rejected',1)
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
          if ($function_parameter_bank <> '') {
            $parName = $function_parameter_bank;
          }
          if ($function_parameter_mobile <> '') {
            $parName .= $function_parameter_mobile;
          }
          $parName = rtrim($parName, ", ");
          // print $parName;die;
          $callFunction = DB::select(DB::raw("SELECT " . strtolower($schema_name) . ".duplicate_bank_mobile_check(in_scheme_id => " . $scheme_id . ", " . $parName . ")"));
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
        // print $parName;
        // echo 'All Clear'; die;

        $old_value = [];
        $input = [];
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

        $updateBenDetailsData = [
          'original_application_id' => $benDetails->id,
          'dist_code' => $benDetails->dist_code,
          'scheme_id' => $benDetails->scheme_id,
          'remarks' => $remarks,
          'old_data' => json_encode($old_value),
          'new_data' => json_encode($input),
          'user_id' => Auth::user()->id,
          'update_code' => 1,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ];
        $update_ben = [
          'bank_name' => $new_bank_name,
          'branch_name' => $new_branch_name,
          'bank_ifsc' => trim($new_bank_ifsc),
          'bank_code' => trim($new_bank_code),
          'mobile_no' => trim($new_mobile_no),
          'bank_edited' => 1
        ];

        /*--- Final Database Opertations ---*/
        // DB::beginTransaction();
        // $is_update=1;
        $is_update = UpdateBenDetails::insert($updateBenDetailsData);
        if ($is_update) {
          // $is_saved=1;
          $table = $this->getSchemaName($scheme_id);
          $is_saved = DB::table($table)->where('id', $id)->update($update_ben);
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
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $scheme_id = $request->scheme_id;
        $designation = Auth::user()->designation_id_old;
        // dd($scheme_id);
        $table = $this->getSchemaName($scheme_id);
        $schemaarr=explode('.', $table);
        $schema=$schemaarr[0];
        

        $user_id = Auth::user()->id;
        
        $id = $request->id;
        $remarks = $request->stop_remarks;
        $stop_payment_reason = $request->stop_payment_reason;
        $doc_types = DB::connection('pgsql_mis')->select("select * from public.m_attached_doc where id=" . $stop_payment_reason);
        $stop_details = DB::connection('pgsql_mis')->table($table)->where('id', $id)->first();
        $input_json = [];
        $input_json['stop_payment_reason'] = $stop_payment_reason;
        $input_json[$table.'.next_level_role_id'] = '-99';
        $input_json['designation'] = $designation;

        DB::beginTransaction();
        if ($request->hasFile('file_stop_payment')) {
          $input = [];
          $image = $request->file('file_stop_payment');
          $input['imagename'] = time() . '.' . $image->getClientOriginalExtension();
          $destinationPath = storage_path('app/stop_payment/' . $stop_details->dist_code . '/scheme_id_' . $stop_details->scheme_id);
          $image->move($destinationPath, $input['imagename']);

          // $lotDetailsUpdate=1;
          $lotDetailsUpdate = DB::select('SELECT public."payment_adjustment"(' . $id . ', -99)');
          if ($lotDetailsUpdate) {
        
            $logUpdate = DB::insert("INSERT INTO " . $schema . ".ben_docs(ben_id, doc_type_id, doc_name, doc_type_name, is_active, created_at)
            VALUES ( " . $id . "," . $doc_types[0]->id . ",'https://jaibangla.wb.gov.in/images_stopped/" . $input['imagename'] . "','" . str_replace("'","''",$doc_types[0]->doc_name) . "',true, now());");

            if ($logUpdate) {
              // $updateBenDetails=1;
              $updateBenDetails = DB::insert("INSERT INTO public.update_ben_details(original_application_id, dist_code, scheme_id, user_id, created_at,remarks, update_code, new_data)
              VALUES (" . $id . "," . $stop_details->dist_code . "," . $stop_details->scheme_id . "," . $user_id . ",now(),'" . $remarks . "',2,'" . json_encode($input_json) . "' );");
              
              if ($updateBenDetails) {
                if( ($scheme_id =='2' ||$scheme_id =='10' ||$scheme_id =='11') && $designation=='Verifier' ) {
                  $update_array=['next_level_stop_payment' =>1];
                  $message= 'Beneficiary Stopped Payment Request Send For Approval';
                } else {
                  $update_array=['next_level_role_id' => -99,'is_approved' => 2,'is_verified' => 2,'is_rejected' =>1];
                  $message= 'Beneficiary Stopped Successfully';
                }
                
                // $is_update=1;
                $is_update = DB::table($table)->where('id', $id)->update($update_array);

                
                if ($is_update) {
                  DB::commit();
                  $response = array(
                    'status' => 1, 'msg' => $message,
                    'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                  );
                } else {
                  DB::rollback();
                  $response = array(
                    'status' => 3, 'msg' => '3 Somethimg went wrong..',
                    'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                  );
                }

              } else {
                DB::rollback();
                $response = array(
                  'status' => 4, 'msg' => '4 Somethimg went wrong..',
                  'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                );
              }
            } else {
              DB::rollback();
              $response = array(
                'status' => 5, 'msg' => '5 Somethimg went wrong..',
                'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
              );
            }
          } else {
            DB::rollback();
            $response = array(
              'status' => 6, 'msg' => '6 Somethimg went wrong..',
              'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
            );
          }
        } else {
          $response = array(
            'status' => 2, 'msg' => 'Please upload one document for stop payment',
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
       dd($e);
      DB::rollback();
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
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $user_id = Auth::user()->id;
      $id = $request->id;
      $scheme_id = $request->scheme_id;
      $table = $this->getSchemaName($scheme_id);
      $stop_details = DB::connection('pgsql_mis')->table($table)->where('id', $id)->first();
      $input_json = [];
      $input_json[$table.'.next_level_role_id'] = '-98';

      DB::beginTransaction();
      // $is_paused=1;
      $is_paused = DB::select('SELECT public."payment_adjustment"(' . $id . ', -98)');
      if ($is_paused) {
        // $updatebenUpdate=1;
        $updatebenUpdate = DB::insert("INSERT INTO public.update_ben_details(original_application_id, dist_code, scheme_id, user_id, created_at,remarks, update_code, new_data)
        VALUES (" . $id . "," . $stop_details->dist_code . "," . $stop_details->scheme_id . "," . $user_id . ",now(),'Pause Payment',3,'" . json_encode($input_json) . "' );");
        if ($updatebenUpdate) {
          // $is_saved=1;
          $is_saved = DB::table($table)->where('id', $id)->update(['is_approved' => 2,'is_verified' => 2,'is_rejected' => 1,'next_level_role_id' => -98]);
          if ($is_saved) {
            DB::commit();
            $response = array(
              'status' => 1, 'msg' => 'Beneficiary Paused Successfully',
              'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
            );
          } else {
            DB::rollback();
            $response = array(
              'status' => 2, 'msg' => '2 Somethimg went wrong..',
              'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
            );
          }
        } else {
          DB::rollback();
          $response = array(
            'status' => 3, 'msg' => '3 Somethimg went wrong..',
            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
          );
        }
      } else {
        DB::rollback();
        $response = array(
          'status' => 4, 'msg' => '4 Somethimg went wrong..',
          'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
        );
      }
    } catch (\Exception $e) {
      DB::rollback();
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
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $user_id = Auth::user()->id;
      $id = $request->ben_id;
      $scheme_id = $request->scheme_id;
      $table = $this->getSchemaName($scheme_id);
      $last_yymm = $request->resume_month;
      $stop_details = DB::connection('pgsql_mis')->table($table)->where('id', $id)->first();

      DB::beginTransaction();
      if ($request->lot_generate_no < 0) {
        $input = [
          'next_level_role_id' => 0,
          'last_paid_yymm' => $last_yymm,
          'is_approved' => 1,
          'is_verified' => 1,
          'is_rejected' => 0
        ];
        $input_json = [];
        $input_json[$table.'.next_level_role_id'] = '0';
        $input_json[$table.'.last_paid_yymm'] = $last_yymm;

        // $updatebenDe =1;
        $updatebenDe = DB::insert("INSERT INTO public.update_ben_details(original_application_id, dist_code, scheme_id, user_id, created_at,remarks, update_code, new_data)
          VALUES (" . $id . "," . $stop_details->dist_code . "," . $stop_details->scheme_id . "," . $user_id . ",now(),'Resume Payment',4,'" . json_encode($input_json) . "' );");
        if ($updatebenDe) {
          // $is_saved=1;
          $is_saved = DB::table($table)->where('id', $id)->update($input);
          if ($is_saved) {
            DB::commit();
            $response = array(
              'status' => 1, 'msg' => 'Resume Successfully. This beneficiary is under modification. Please contact concerned Block Verifier.',
              'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
            );
          } else {
            DB::rollback();
            $response = array(
              'status' => 2, 'msg' => '2 Somethimg went wrong..',
              'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
            );
          }
        } else {
          DB::rollback();
          $response = array(
            'status' => 3, 'msg' => '3 Somethimg went wrong..',
            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
          );
        }
      } else {
        $input = [
          'next_level_role_id' => 0,
          'lot_generated' => 0,
          'last_paid_yymm' => $last_yymm,
          'is_approved' => 1,
          'is_verified' => 1,
          'is_rejected' => 0
        ];
        $input_json = [];
        $input_json[$table.'.next_level_role_id'] = '0';
        $input_json[$table.'.lot_generated'] = '0';
        $input_json[$table.'.last_paid_yymm'] = $last_yymm;

        // $updatebenDe=1;
        $updatebenDe = DB::insert("INSERT INTO public.update_ben_details(original_application_id, dist_code, scheme_id, user_id, created_at,remarks, update_code, new_data)
          VALUES (" . $id . "," . $stop_details->dist_code . "," . $stop_details->scheme_id . "," . $user_id . ",now(),'Resume Payment',4,'" . json_encode($input_json) . "' );");
        if ($updatebenDe) {
          // $is_saved =1; 
          $is_saved = DB::table($table)->where('id', $id)->update($input);
          if ($is_saved) {
            DB::commit();
            $response = array(
              'status' => 1, 'msg' => 'Resume Successfully.',
              'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
            );
          } else {
            DB::rollback();
            $response = array(
              'status' => 2, 'msg' => '2 Somethimg went wrong..',
              'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
            );
          }
        } else {
          DB::rollback();
          $response = array(
            'status' => 3, 'msg' => '3 Somethimg went wrong..',
            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
          );
        }
      }
    } catch (\Exception $e) {
      DB::rollback();
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
      $new_mobile_no = $request->mobile_no;
      $benDetails = DB::connection('pgsql_mis')->table($table)->where('id', $id)->first();
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
        'updated_at' => date('Y-m-d H:i:s')
      ];
      $update_ben = [
        'mobile_no' => trim($new_mobile_no)
      ];

      /*--- Final Database Opertations ---*/
      DB::beginTransaction();
      // $is_update=1;
      $is_update = UpdateBenDetails::insert($updateBenDetailsData);
      if ($is_update) {
        // $is_saved=1;
        $is_saved = DB::table($table)->where('id', $id)->update($update_ben);
        if ($is_saved) {
          DB::commit();
          $response = array(
            'status' => 1, 'msg' => 'Mobile No. Updated Successfully',
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

  
}
