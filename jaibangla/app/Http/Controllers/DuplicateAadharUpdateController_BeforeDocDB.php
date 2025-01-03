<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Scheme;
use App\District;
use App\BeneficiaryPensions;
use App\PensionSc;
use App\PensionSt;
use App\Manabik;
use App\UpdateBenDetails;
use App\Configduty;
use App\DocumentType;
use App\GP;
use App\SchemeDocMap;
use App\SubDistrict;
use App\Taluka;
use App\UrbanBody;
use App\Ward;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\AuthChecker;


class DuplicateAadharUpdateController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    date_default_timezone_set('Asia/Kolkata');
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
		Get First Landing Page only shown in the operator end
  */
  public function index()
  {
    $designation_id = Auth::user()->designation_id;
    if ($designation_id == 'Operator') {
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
    if (Auth::user()->designation_id == "Operator") {
      if (count($scheme) > 0) {
        if ($mapObj->is_urban == 1) {
          $urban_body_code = $mapObj->urban_body_code;
          $urban_bodys = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();
          return view('DuplicateAadharUpdate/index_aadhar', ['schemes' => $scheme, 'mapLevel' => $mapObj->mapping_level . $designation_id, 'urban_bodys' => $urban_bodys]);
        } else {
          $taluka_code = $mapObj->taluka_code;
          $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
          return view('DuplicateAadharUpdate/index_aadhar', ['schemes' => $scheme, 'mapLevel' => $mapObj->mapping_level . $designation_id, 'gps' => $gps]);
        }
      } else {
        return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
      }
    } else {
      return redirect("/")->with('success', 'UnAuthorized');
    }
  }
  /*
		Get Beneficiary Data
  */
  public function getDuplicateAadharListView(Request $request)
  {
    if ($request->ajax()) {
      $scheme_id = $request->scheme_id;
      if (empty($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
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
      if ($designation_id == 'Operator') {
        $is_active = 1;
      } else {
        $is_active = 0;
      }
      if ($is_active == 0 || (empty($district_code) && empty($urban_body_code)) || empty($mapping_level)) {
        return redirect("/")->with('error', 'User Disabled. ');
      }

      $scheme_row = Scheme::where('id', $scheme_id)->first();
      // Get Dynamic Schema Name scheme wise
      $table_name = $this->getSchemaName($scheme_id);

      if ($mapping_level == 'Block') {
        $query = "select * from " . $table_name . " where (is_rejected=0) and ((dup_aadhar = 1 and dup_aadhar_edit_role_id IS NULL ) or (dup_mobile = 1 and dup_mobile_edit_role_id IS NULL)) and scheme_id = " . $scheme_id . " and created_by_dist_code = " . $district_code . " and created_by_local_body_code = " . $urban_body_code . " ";
        if (!empty($request->filter_1)) {
          $query .= " and gp_ward_code = " . $request->filter_1 . "";
        }
        $query .= " order by id desc";
        $data = DB::select(DB::raw($query));
      } else if ($mapping_level == 'Subdiv') {
        $query = '';
        $query = "select * from " . $table_name . " where (is_rejected=0) and ((dup_aadhar = 1 and dup_aadhar_edit_role_id IS NULL) or (dup_mobile = 1 and dup_mobile_edit_role_id IS NULL)) and scheme_id = " . $scheme_id . " and created_by_dist_code = " . $district_code . " and created_by_local_body_code = " . $urban_body_code . " ";
        if (!empty($request->filter_1)) {
          $query .= " and block_ulb_code = " . $request->filter_1 . "";
        }
        if (!empty($request->filter_2)) {
          $query .= " and gp_ward_code = " . $request->filter_2 . "";
        }
        $query .= " order by id desc";
        $data = DB::select(DB::raw($query));
      }

      return datatables()->of($data)
        ->addColumn('view', function ($data) {
          $action = '';
          if ($data->dup_aadhar == 1 and $data->dup_aadhar_edit_role_id == '') {
            $action .= '<button onclick=editAadharFunction(' . $data->id . ',' . $data->scheme_id . ') class="btn btn-xs btn-primary" title="Update Aadhar Card"><i class="glyphicon glyphicon-edit"></i> Edit Aadhar</button>';
          }
          if ($data->dup_mobile == 1 and $data->dup_mobile_edit_role_id == '') {
            $action .= '&nbsp; &nbsp; <button onclick=editMobileFunction(' . $data->id . ',' . $data->scheme_id . ') class="btn btn-xs btn-info" title="Update Mobile Number"><i class="glyphicon glyphicon-edit"></i> Edit Mobile</button>';
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

  /*
    Get One beneficiary edit aadhar card
  */
  public function getDuplicateAadharBenModalView(Request $request)
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
      if ($designation_id == 'Operator') {
        $is_active = 1;
      } else {
        $is_active = 0;
      }
      if ($is_active == 0 || (empty($district_code) && empty($urban_body_code)) || empty($mapping_level)) {
        return redirect("/")->with('error', 'User Disabled. ');
      }

      $scheme_row = Scheme::where('id', $scheme_id)->first();
      // Get Dynamic Schema Name scheme wise
      $table_name = $this->getSchemaName($scheme_id);

      $ben_details = DB::table($table_name)->where(function ($query) use ($role_id) {
        //$query->where('is_rejected', 1);
      })
        ->where(function ($query) {
          $query->where('dup_aadhar', 1)
            ->orWhere('dup_mobile', 1);
        })
        ->where('scheme_id', $scheme_id)
        ->where('created_by_dist_code', $district_code)
        ->where('created_by_local_body_code', $urban_body_code)
        ->where('id', $id)
        ->first();

      $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->where('id', 6)->first();

      // print($ben_details->ben_fname);die;
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
          'branch_name' => trim($ben_details->branch_name), 'bank_name' => trim($ben_details->bank_name), 'mobile_no' => trim($ben_details->mobile_no), 'application_id' => $ben_details->created_by_dist_code . str_pad($ben_details->scheme_id, 2, 0, STR_PAD_LEFT) . str_pad($ben_details->id, 15, 0, STR_PAD_LEFT), 'aadhar_no' => trim($ben_details->aadhar_no),
          'doc_name' => $doc_list->doc_name, 'doc_id' => $doc_list->id, 'doc_type' => $doc_list->doc_type, 'doc_size_kb' => $doc_list->doc_size_kb
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
    Update benenfiicary aadhar no
  */
  public function updateDeDuplicateBenAadharDetails(Request $request)
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
        'new_aadhar_no' => 'required|digits:12',
        'remarks' => 'max:100'
      );
      $attributes = [
        'new_aadhar_no' => 'Aadhar Card No',
        'remarks' => 'Remarks'
      ];
      $messages = [
        'required' => 'The :attribute field is required.',
        'digits' => 'Total :digits number requied for :attribute',
        'max' => 'Total :max characters allowed for :attribute'
      ];
      $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->where('id', 6)->get();
      foreach ($doc_list as $key => $value) {
        $required = 'required';
        $rules['doc_' . $value->id] = $required . '|mimes:' . $value->doc_type . '|max:' . $value->doc_size_kb . ',';
        $messages['doc_' . $value['id'] . '.max'] = "The file uploaded for " . $value->doc_name . " size must be less than " . $value->doc_size_kb . " KB";
        $messages['doc_' . $value['id'] . '.mimes'] = "The file uploaded for " . $value->doc_name . " must be of type " . $value->doc_type;
        $messages['doc_' . $value['id'] . '.required'] = "Document for " . $value->doc_name . " must be uploaded";
      }
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $new_aadhar_no = $request->new_aadhar_no;
        if ($this->isAadharValid($new_aadhar_no) == false) {
          $return_status = 0;
          $return_text = 'Aadhaar Number Invalid';
          $return_msg = array("" . $return_text);
          return $response = array('status' => $return_status, 'msg' => $return_msg, 'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!');
        }
        $scheme_id = $request->scheme_id;
        $ben_id = $request->id;
        $old_aadhar_no = $request->old_aadhar_no;
        $remarks = $request->remarks;
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
        if ($designation_id == 'Operator') {
          $is_active = 1;
        } else {
          $is_active = 0;
        }
        if ($is_active == 0 || (empty($district_code) && empty($urban_body_code)) || empty($mapping_level)) {
          return redirect("/")->with('error', 'User Disabled. ');
        }

        // Dynamically Modal Name Set
        $scheme_short_code = Scheme::where('id', $scheme_id)->value('short_code');
        $ben_docs_table = strtolower($scheme_short_code) . '.ben_docs';
        $ben_docs_arc_table = strtolower($scheme_short_code) . '.ben_docs_arc';
        $beneficiary_table = $this->getSchemaName($scheme_id);
        $beneficiary_arc_table = strtolower($scheme_short_code) . '.arc_beneficiary';

        // Update Ben Details Log
        $benDetails = DB::table($beneficiary_table)->where('id',$ben_id)->first();
        $old_value = [];
        $input = [];
        $old_value['aadhar_no'] = $old_aadhar_no;
        $input['aadhar_no'] = $new_aadhar_no;

        $updateBenDetailsData = [
          'original_application_id' => $benDetails->id,
          'dist_code' => $benDetails->dist_code,
          'scheme_id' => $benDetails->scheme_id,
          'remarks' => $remarks,
          'old_data' => json_encode($old_value),
          'new_data' => json_encode($input),
          'user_id' => Auth::user()->id,
          'update_code' => 15, // For Aadhar De-duplication edit
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ];

        // Upload Aadhar Card
        $base_url = url('/');
        $uploaded_doc = array();
        $doc = 6; // Aadhar Card
        if ($request->hasFile('doc_' . $doc)) {
          $doc_file = $request->file('doc_' . $doc);
          $file_passport = $doc_file->getClientOriginalName();
          $file_type = $doc_file->getClientOriginalExtension();
          $file_profile = "doc_" . $doc . "_" . rand(10000, 99999) . '_' . time() . '.' . $doc_file->getClientOriginalExtension();
          $destinationPath = storage_path('app/keep_wcd/');
          $fileStore[] = $doc_file->move($destinationPath, $file_profile);
          //array_push($uploaded_doc,$file_profile);
          $uploaded_doc[$doc] = $file_profile;
        } else {
          $file_passport = null;
        }

        DB::connection('pgsql')->beginTransaction();
        try {
          DB::statement("INSERT INTO ".$beneficiary_arc_table."(id, 
             dist_code, ben_fname, ben_mname, ben_lname, gender, dob, ben_age, 
            caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
            mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, 
            ration_card_no, ahl_tin, aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, 
            bpl_total_score, dist_name, assembly_code, assembly_name, police_station, block_ulb_code, 
            block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, village_town_city, house_premise_no, 
            post_office, pincode, residency_period, mobile_no, email, bank_name, 
            branch_name, bank_old_code, bank_ifsc, created_at, updated_at, created_by, created_by_level, 
            created_by_dist_code, created_by_local_body_code, scheme_id, type_disability, 
            percentage_disability, certifying_auth, next_level_role_id, comments, nominate_name, 
            nominate_address, nominate_relationship, receive_pension, social_security_pension, 
            ration_card_cat, rural_urban_id, lot_generated, 
            bank_edited, bank_code, payment_count, last_paid_yymm, 
            av_status, legacy_import, 
            receiving_pension_other_source_1, receiving_pension_other_source_2
            ) (SELECT id, 
             dist_code, ben_fname, ben_mname, ben_lname,gender, dob, ben_age, 
            caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
            mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, 
            ration_card_no, ahl_tin, aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, 
            bpl_total_score, dist_name, assembly_code, assembly_name, police_station, block_ulb_code, 
            block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, village_town_city, house_premise_no, 
            post_office, pincode, residency_period, mobile_no, email, bank_name, 
            branch_name, bank_old_code, bank_ifsc, created_at, updated_at, created_by, created_by_level, 
            created_by_dist_code, created_by_local_body_code, scheme_id, type_disability, 
            percentage_disability, certifying_auth, next_level_role_id, comments, nominate_name, 
            nominate_address, nominate_relationship, receive_pension, social_security_pension, 
            ration_card_cat, rural_urban_id, lot_generated, 
            bank_edited, bank_code, payment_count, last_paid_yymm, 
            av_status, legacy_import, 
            receiving_pension_other_source_1, receiving_pension_other_source_2 from ".$beneficiary_table." where id=" . $ben_id . ")");
          foreach ($uploaded_doc as $doc_type => $doc) {
            $ben_docs = DB::table($ben_docs_table)->where('ben_id', $ben_id)
              ->where('doc_type_id', $doc_type)->where('is_active', TRUE)->first();
            $doc_type_name = DocumentType::where('id', $doc_type)->first();
            $ben_docs_insert = [
              'ben_id' => $ben_id,
              'doc_type_id' => $doc_type,
              'doc_name' => $base_url . "/jaibangla/storage/app/keep_wcd/" . $doc,
              'doc_type_name' => $doc_type_name->doc_name,
              'is_active' => TRUE,
              'created_at' => date('Y-m-d H:i:s', time()),
              'updated_at' => date('Y-m-d H:i:s', time()),
            ];
            $is_upload = DB::table($ben_docs_table)->insert($ben_docs_insert);
            
            if ($ben_docs != null) {
              $filename = basename($ben_docs->doc_name);
              if ($scheme_id == 10 || $scheme_id == 11 || $scheme_id == 2) {
                if (file_exists(storage_path('app/keep_wcd/') . '//' . $filename)) {
                  rename(storage_path('app/keep_wcd/') . '//' . $filename, storage_path('app/keep_back_wcd/') . '//' . $filename);
                }
              }
              $archiveQuery = "INSERT INTO " . $ben_docs_arc_table . "(
                ben_id, doc_type_id, doc_name, doc_type_name, created_at, deleted_at)
                SELECT ben_id, doc_type_id, doc_name, doc_type_name, created_at,now() FROM ".$ben_docs_table." WHERE ben_id=".$ben_id." AND doc_type_id=".$doc_type." AND is_active=true AND id=".$ben_docs->id;
              $is_insert = DB::statement($archiveQuery);
              if ($is_insert) {
                $is_upload1 = DB::table($ben_docs_table)->where('ben_id', $ben_id)->where('doc_type_id', $doc_type)->where('is_active', TRUE)->where('id', $ben_docs->id)->update(['is_active' => FALSE]);
              }
            }
          }
          if ($is_upload == 1) {
            // $function_call = DB::select(DB::raw("select ".strtolower($scheme_short_code).".dup_adjustment_update(".$scheme_id.", ".$ben_id.", NULL, NULL, NULL, NULL, '".$new_aadhar_no."', '".$old_aadhar_no."', NULL, NULL);"));
            $beneficiaryUpdate = array();
            $beneficiaryUpdate['aadhar_no'] =$new_aadhar_no;
            $beneficiaryUpdate['dup_aadhar_edit_role_id'] = 1;
            if ($remarks != '') {
              $beneficiaryUpdate['dup_aadhar_edit_remarks'] = $remarks;
            }
            
            $UpdateBenDetailsInsert = UpdateBenDetails::insert($updateBenDetailsData);
            // $update = DB::table($beneficiary_table)->where('id', $ben_id)->update();
            if ($UpdateBenDetailsInsert == 1) {
                DB::table($beneficiary_table)->where('scheme_id', $scheme_id)->where('id', $ben_id)->update($beneficiaryUpdate);
            }
            else {
              DB::rollback();
              $return_status = 0;
              $return_text = 'This Aadhar no already exists, please try another one.';
              $return_msg = array("" . $return_text);
              return $response = array(
                'status' => $return_status, 'msg' => $return_msg,
                'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
              );
            }
          }

          DB::commit();
          $response = array(
            'status' => 1, 'msg' => 'Aadhar Card De-duplicate Successfully',
            'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
          );

        } catch (\Exception $e) {
          // dd($e);
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
        'exception_message' => $e->getMessage(),
        // 'exception_message' => 'Somethimg went wrong. May be session timeout, please logout and login again.',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }

  /*
    Get One beneficiary edit mobile number
  */
  public function getDuplicateMobileBenModalView(Request $request)
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
      if ($designation_id == 'Operator') {
        $is_active = 1;
      } else {
        $is_active = 0;
      }
      if ($is_active == 0 || (empty($district_code) && empty($urban_body_code)) || empty($mapping_level)) {
        return redirect("/")->with('error', 'User Disabled. ');
      }

      $scheme_row = Scheme::where('id', $scheme_id)->first();
      // Get Dynamic Schema Name scheme wise
      $table_name = $this->getSchemaName($scheme_id);

      $ben_details = DB::table($table_name)->where(function ($query) use ($role_id) {
        $query->where('is_rejected', 0);
      })
        ->where('dup_mobile', 1)
        ->where('scheme_id', $scheme_id)
        ->where('created_by_dist_code', $district_code)
        ->where('created_by_local_body_code', $urban_body_code)
        ->where('id', $id)
        ->first();

      // print($ben_details->ben_fname);die;
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
    Update mobile number in the main beneficiary table
  */
  public function updateDeDuplicateBenMobileNoDetails(Request $request) {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $rules = array(
        'new_mobile_no' => 'required|numeric|digits:10',
        'mob_remarks' => 'max:100'
      );
      $attributes = [
        'new_mobile_no' => 'Mobile Number',
        'mob_remarks' => 'Remarks'
      ];
      $messages = [
        'required' => 'The :attribute field is required.',
        'numeric' => 'The :attribute field is allow only numbers.',
        'digits' => 'Total :digits number requied for :attribute',
        'max' => 'Total :max characters allowed for :attribute'
      ];
      
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if ($validator->passes()) {
        $scheme_id = $request->scheme_id;
        $ben_id = $request->id;
        $old_mobile_no = $request->old_mobile_no;
        $new_mobile_no = $request->new_mobile_no;
        $remarks = $request->remarks;
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
        if ($designation_id == 'Operator') {
          $is_active = 1;
        } else {
          $is_active = 0;
        }
        if ($is_active == 0 || (empty($district_code) && empty($urban_body_code)) || empty($mapping_level)) {
          return redirect("/")->with('error', 'User Disabled. ');
        }

        // Dynamically Modal Name Set
        $scheme_short_code = Scheme::where('id', $scheme_id)->value('short_code');
        $beneficiary_table = $this->getSchemaName($scheme_id);
        $beneficiary_arc_table = strtolower($scheme_short_code) . '.arc_beneficiary';

        // Update Ben Details Log
        $benDetails = DB::table($beneficiary_table)->where('id',$ben_id)->first();
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
          'update_code' => 16, // For Mobile No De-duplication edit
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ];

        DB::connection('pgsql')->beginTransaction();
        try {
          $is_insert=  DB::statement("INSERT INTO ".$beneficiary_arc_table."(id, 
             dist_code, ben_fname, ben_mname, ben_lname, gender, dob, ben_age, 
            caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
            mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, 
            ration_card_no, ahl_tin, aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, 
            bpl_total_score, dist_name, assembly_code, assembly_name, police_station, block_ulb_code, 
            block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, village_town_city, house_premise_no, 
            post_office, pincode, residency_period, mobile_no, email, bank_name, 
            branch_name, bank_old_code, bank_ifsc, created_at, updated_at, created_by, created_by_level, 
            created_by_dist_code, created_by_local_body_code, scheme_id, type_disability, 
            percentage_disability, certifying_auth, next_level_role_id, comments, nominate_name, 
            nominate_address, nominate_relationship, receive_pension, social_security_pension, 
            ration_card_cat, rural_urban_id, lot_generated, 
            bank_edited, bank_code, payment_count, last_paid_yymm, 
            av_status, legacy_import, 
            receiving_pension_other_source_1, receiving_pension_other_source_2
            ) (SELECT id, 
             dist_code, ben_fname, ben_mname, ben_lname,gender, dob, ben_age, 
            caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
            mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, mothly_income, 
            ration_card_no, ahl_tin, aadhar_no, epic_voter_id, pan_no, bpl_y_n, bpl_seq_no, bpl_id_no, 
            bpl_total_score, dist_name, assembly_code, assembly_name, police_station, block_ulb_code, 
            block_ulb_name, block_ulb_type, gp_ward_code, gp_ward_name, village_town_city, house_premise_no, 
            post_office, pincode, residency_period, mobile_no, email, bank_name, 
            branch_name, bank_old_code, bank_ifsc, created_at, updated_at, created_by, created_by_level, 
            created_by_dist_code, created_by_local_body_code, scheme_id, type_disability, 
            percentage_disability, certifying_auth, next_level_role_id, comments, nominate_name, 
            nominate_address, nominate_relationship, receive_pension, social_security_pension, 
            ration_card_cat, rural_urban_id, lot_generated, 
            bank_edited, bank_code, payment_count, last_paid_yymm, 
            av_status, legacy_import, 
            receiving_pension_other_source_1, receiving_pension_other_source_2 from ".$beneficiary_table." where id=" . $ben_id . ")");
          
          if ($is_insert) {
            // $function_call = DB::select(DB::raw("select ".strtolower($scheme_short_code).".dup_adjustment_update(".$scheme_id.", ".$ben_id.", NULL, NULL, NULL, NULL, NULL, NULL, '".$new_mobile_no."', '".$old_mobile_no."');"));
            $beneficiaryUpdate = array();
            $beneficiaryUpdate['mobile_no'] =$new_mobile_no;
            $beneficiaryUpdate['dup_mobile_edit_role_id'] = 1;
            if ($remarks != '') {
              $beneficiaryUpdate['dup_mobile_edit_remarks'] = $remarks;
            }
            
            $UpdateBenDetailsInsert = UpdateBenDetails::insert($updateBenDetailsData);
            // $update = DB::table($beneficiary_table)->where('id', $ben_id)->update();
            if ($UpdateBenDetailsInsert == 1) {
                DB::table($beneficiary_table)->where('scheme_id', $scheme_id)->where('id', $ben_id)->update($beneficiaryUpdate);
            }
            else {
              DB::rollback();
              $return_status = 0;
              $return_text = 'This Mobile number already exists, please try another one.';
              $return_msg = array("" . $return_text);
              return $response = array(
                'status' => $return_status, 'msg' => $return_msg,
                'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
              );
            }
          }

          DB::commit();
          $response = array(
            'status' => 1, 'msg' => 'Mobile Number De-duplicate Successfully',
            'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
          );

        } catch (\Exception $e) {
          // dd($e);
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
        'exception_message' => $e->getMessage(),
        // 'exception_message' => 'Somethimg went wrong. May be session timeout, please logout and login again.',
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }

  /*
    Verhoeff algorithm for checking aadhar no
  */
  public function isAadharValid($num)
  {
    settype($num, "string");
    $expectedDigit = substr($num, -1);
    $actualDigit = $this->CheckSumAadharDigit(substr($num, 0, -1));
    return ($expectedDigit == $actualDigit) ? $expectedDigit == $actualDigit : 0;
  }

  function CheckSumAadharDigit($partial)
  {
    $dihedral = array(
      array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
      array(1, 2, 3, 4, 0, 6, 7, 8, 9, 5),
      array(2, 3, 4, 0, 1, 7, 8, 9, 5, 6),
      array(3, 4, 0, 1, 2, 8, 9, 5, 6, 7),
      array(4, 0, 1, 2, 3, 9, 5, 6, 7, 8),
      array(5, 9, 8, 7, 6, 0, 4, 3, 2, 1),
      array(6, 5, 9, 8, 7, 1, 0, 4, 3, 2),
      array(7, 6, 5, 9, 8, 2, 1, 0, 4, 3),
      array(8, 7, 6, 5, 9, 3, 2, 1, 0, 4),
      array(9, 8, 7, 6, 5, 4, 3, 2, 1, 0)
    );
    $permutation = array(
      array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
      array(1, 5, 7, 6, 2, 8, 3, 0, 9, 4),
      array(5, 8, 0, 3, 7, 9, 6, 1, 4, 2),
      array(8, 9, 1, 6, 0, 4, 3, 5, 2, 7),
      array(9, 4, 5, 3, 1, 2, 6, 8, 7, 0),
      array(4, 2, 8, 6, 5, 7, 3, 9, 0, 1),
      array(2, 7, 9, 3, 8, 0, 6, 4, 1, 5),
      array(7, 0, 4, 6, 9, 1, 3, 2, 5, 8)
    );

    $inverse = array(0, 4, 3, 2, 1, 5, 6, 7, 8, 9);
    settype($partial, "string");
    $partial = strrev($partial);
    $digitIndex = 0;
    for ($i = 0; $i < strlen($partial); $i++) {
      $digitIndex = $dihedral[$digitIndex][$permutation[($i + 1) % 8][$partial[$i]]];
    }
    return $inverse[$digitIndex];
  }
}
