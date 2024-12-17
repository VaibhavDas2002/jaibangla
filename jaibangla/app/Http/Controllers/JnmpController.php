<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DupliacteApproveReject;
use App\Scheme;
use App\District;
use App\UrbanBody;
use App\GP;
use App\BeneficiaryPensions;
use App\PensionSc;
use App\PensionSt;
use App\Manabik;
use App\UpdateBenDetails;
use App\Configduty;
use App\DocumentType;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class JnmpController extends Controller
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

    public function index(Request $request)
    {
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        // echo '<pre>'; print_r($roleArray);die();
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = Auth::user()->id;
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") AND id in (1,2,3,5,6,7,8,9,10,11,13,17,19) order by rank"));
        // echo '<pre>';print_r($schemes);die();
        if ($designation_id_old == 'Admin') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if ($designation_id_old == 'Approver') {
            // echo 1;die();
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                // echo $designation_id_old;die();
                if ($roleObj['scheme_id']) { // == 11 || $roleObj['scheme_id'] == 13
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
        $districts = District::get();
        $reactive_reasons = DB::table('jnmp.reactive_reason')->get();
        return view(
            'aadhar_mapped_with_jnmp_data/index',
            [
                'schemes' => $schemes,
                'districts' => $districts,
                'district_visible' => $district_visible,
                'district_code_fk' => $district_code_fk,
                'is_urban_visible' => $is_urban_visible,
                'rural_urban_fk' => $rural_urban_fk,
                'block_visible' => $block_visible,
                'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
                'municipality_visible' => $municipality_visible,
                'gp_ward_visible' => $gp_ward_visible,
                'is_urban_visible' => $is_urban_visible,
                'gpList' => $gpList,
                'muncList' => $muncList,
                'reactive_reasons' => $reactive_reasons
            ]
        );
    }

    public function jnmpMarkedData(Request $request)
    {
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") AND id in(1,2,3,5,6,7,8,9,10,11,13,17,19) order by rank"));
        if ($designation_id_old == 'Admin') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
        } else if ($designation_id_old == 'Approver') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id']) { // == 11 || $roleObj['scheme_id'] == 13
                    $is_urban = $roleObj['is_urban'];
                    $district_code = $roleObj['district_code'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }

            if (empty($district_code))
                return redirect("/")->with('success', 'User Disabled. ');
        } else {
            return redirect("/")->with('success', 'User Disabled. ');
        }
        $dist_code = $request->district;
        // $filter = $request->search_for;
        $scheme_id = $request->scheme_code;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $gp_ward = $request->gp_ward;
        $muncid = $request->muncid;
        $table_name = $this->getSchemaName($scheme_id);
        if ($request->ajax()) {
            $query = $this->getDataRows($district_code,$blockCode,$block,$gp_ward,$muncid,$table_name,$dist_code);
            // echo $query;die();
            $result = DB::connection('pgsql_mis')->select($query);
            // echo '<pre>';print_r($result);die();
            return datatables()->of($result)
            ->addColumn('aadhar_no', function ($result) {
                $mask_aadhar = '';
                $aadhar = trim($result->aadhar_no);
                if (strlen($aadhar) >= 12 && strlen($aadhar) != '') {
                  $mask_aadhar = '********' . substr($aadhar, 8, 4);
                } else {
                  $mask_aadhar = $aadhar;
                }
                return $mask_aadhar;
              })
            ->addColumn('action', function($result){
                $btn = '';
                $btn .= '<button onclick=viewModalFunction(' . $result->id . ',' . $result->scheme_id . ') class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> Active as alive</button>';
                return $btn;
            })
            ->rawColumns(['aadhar_no', 'action'])
            ->make(true);
        }
    }

    public function modalViewData(Request $request)
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
          $id = $request->id;
          $table_name = $this->getSchemaName($scheme_id);
          // echo $table_name;die();
          $row = DB::table($table_name)->where('jnmp_aadhar_mapped', '=', 1)
          ->where('id', $id)
          ->where('next_level_role_id', '=', 0)
          ->where('payment_suspended', '=', 1)
          ->where('scheme_id', $scheme_id)
          ->first();
        //   print_r($row);die();
          $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->where('id', 117)->first();
          $jnm_data = "SELECT j.deceasedfullname,j.dateofdeath FROM jnmp.jnmp_data j JOIN ".$table_name." b ON b.aadhar_no = j.deceased_idproofnumber
          WHERE j.deceased_idprooftypname = 'Aadhaar' AND b.next_level_role_id = 0 AND b.jnmp_aadhar_mapped = 1 AND payment_suspended = 1 AND b.id = ".$id."";
          $jnm_data_result = DB::connection('pgsql_mis')->select($jnm_data);
        //   echo $jnm_data_result->deceasedfullname;die;
        //   print_r($jnm_data_result);die;
      
          if ($row == null) {
            return  $response = array(
              'status' => 1, 'msg' => 'Somethimg went wrong.',
              'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
            );
          }else{
            $ben_arr = array(
              'ben_name' => trim($row->ben_fname) . ' ' . trim($row->ben_mname) . ' ' . trim($row->ben_lname), 'id' => $row->id, 'scheme_id' => $row->scheme_id,
              'father_name' => trim($row->father_fname) . ' ' . trim($row->father_mname) . ' ' . trim($row->father_lname),
              'caste' => trim($row->caste), 'gender' => trim($row->gender),
              'dob' => date('d-m-Y', strtotime($row->dob)),
              'bank_code' => trim($row->bank_code), 'bank_ifsc' => trim($row->bank_ifsc),
              'branch_name' => trim($row->branch_name), 'bank_name' => trim($row->bank_name), 'mobile_no' => trim($row->mobile_no), 'application_id' => $row->created_by_dist_code . str_pad($row->scheme_id, 2, 0, STR_PAD_LEFT) . str_pad($row->id, 15, 0, STR_PAD_LEFT), 'aadhar_no' => trim($row->aadhar_no),
              'doc_name' => $doc_list->doc_name, 'doc_id' => $doc_list->id, 'doc_type' => $doc_list->doc_type, 'doc_size_kb' => $doc_list->doc_size_kb,
              'jnmp_fullname' => $jnm_data_result[0]->deceasedfullname, 'jnmp_date_of_death' => $jnm_data_result[0]->dateofdeath
            );
           
            $response = $ben_arr;
          }
      } catch (\Exception $e) {
         dd($e);
          $response = array(
              'exception' => true,
              'exception_message' => $e->getMessage(),
          );
          $statusCode = 400;
      } finally {
          return response()->json($response, $statusCode);
      }
    }
    public function activeBeneficiary(Request $request)
    {
        // echo 1;die;
      $response = [];
      $statusCode = 200;
      if (!$request->ajax()) {
        $statusCode = 400;
        $response = array('error' => 'Error occured in form submit.');
        return response()->json($response, $statusCode);
      }
      try{
        $rules = array(
          'remarks' => 'max:100'
        );
        $attributes = [
          'remarks' => 'Remarks'
        ];
        $messages = [
          'required' => 'The :attribute field is required.',
          'max' => 'Total :max characters allowed for :attribute'
        ];
        $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->where('id', 117)->get();
        // echo $doc_list[0]->id;die;
        foreach ($doc_list as $key => $value) {
            // echo $value->doc_size_kb;die;
          $required = 'required';
          $rules['doc_' . $value->id] = $required . '|mimes:' . $value->doc_type . '|max:' . $value->doc_size_kb . ',';
          $messages['doc_' . $value['id'] . '.max'] = "The file uploaded for " . $value->doc_name . " size must be less than " . $value->doc_size_kb . " KB";
          $messages['doc_' . $value['id'] . '.mimes'] = "The file uploaded for " . $value->doc_name . " must be of type " . $value->doc_type;
          $messages['doc_' . $value['id'] . '.required'] = "Document for " . $value->doc_name . " must be uploaded";
        }
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $scheme_id = $request->scheme_id;
            // echo $scheme_id;die;
            $ben_id = $request->id;
            $remarks = $request->remarks;
            $reactive_reason = $request->reactive_reason;
            // echo $remarks;die;
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
            if ($is_active == 0 || (empty($district_code) && empty($urban_body_code)) || empty($mapping_level)) {
                return redirect("/")->with('error', 'User Disabled. ');
            }
            // Dynamically Modal Set Name;
            $scheme_short_code = Scheme::where('id', $scheme_id)->value('short_code');
            $ben_docs_table = strtolower($scheme_short_code) . '.ben_docs';
            // echo $ben_docs_table;die;
            $ben_docs_arc_table = strtolower($scheme_short_code) . '.ben_docs_arc';
            // echo $ben_docs_arc_table;die;
            $beneficiary_table = $this->getSchemaName($scheme_id);
            $beneficiary_arc_table = strtolower($scheme_short_code) . '.arc_beneficiary';

            // Update Ben Details Log
            $benDetails = DB::table($beneficiary_table)->where('id',$ben_id)->first();
            $old_value = [];
            $input = [];
            $old_value['payment_suspended'] = $benDetails->payment_suspended;
            $input['payment_suspended'] = null;

            $updateBenDetailsData = [
            'original_application_id' => $benDetails->id,
            'dist_code' => $benDetails->dist_code,
            'scheme_id' => $benDetails->scheme_id,
            'remarks' => $remarks,
            'old_data' => json_encode($old_value),
            'new_data' => json_encode($input),
            'user_id' => Auth::user()->id,
            'ip_address' => request()->ip(),
            'reactive_reason' => $reactive_reason,
            'update_code' => 17, // For Janma Mrityu Activation.
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
            ];

            // Upload Aadhar Card
            $base_url = url('/');
            $uploaded_doc = array();
            $doc = 117; // Aadhar Card
            if ($request->hasFile('doc_' . $doc)) {
            $doc_file = $request->file('doc_' . $doc);
            $img_data = file_get_contents($doc_file);
            // echo $img_data;die;
            $u_extension = $doc_file->getClientOriginalExtension();
            $mime_type = $doc_file->getMimeType();

            if(strtolower($mime_type)=='image/jpeg'){
                if($u_extension=='jpg' || $u_extension=='jpeg'){
                  $extension=$u_extension;
                }
                else{
                    $errorMsg = "You are trying to upload an incorrect file for ".$doc_list[0]->doc_name;
                    return $response = array(
                        'status' => 0, 'msg' => $errorMsg,
                        'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                    );
                }
              }
            else if(strtolower($mime_type)=='image/png'){
                $extension='png';
            }else if(strtolower($mime_type)=='image/gif'){
                $extension='gif';
            }else if(strtolower($mime_type)=='application/pdf'){
                $extension='pdf';
            }
            else{
                $errorMsg = "You are trying to upload an incorrect file for ".$doc_list[0]->doc_name;
                return $response = array(
                    'status' => 0, 'msg' => $errorMsg,
                    'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                );
            }
            if($u_extension!=$extension){
                $errorMsg = "You are trying to upload an incorrect file for ".$doc_list[0]->doc_name;
                return $response = array(
                    'status' => 0, 'msg' => $errorMsg,
                    'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                );
            }

            $base64 = base64_encode($img_data);
            $ip_address = request()->ip();
            $c_datetime = date('Y-m-d H:i:s', time());
            $user_id = Auth::user()->id;
            // $file_passport = $doc_file->getClientOriginalName();
            // $file_type = $doc_file->getClientOriginalExtension();
            // echo $file_type;die;
            // $file_profile = "doc_" . $doc . "_" . rand(10000, 99999) . '_' . time() . '.' . $doc_file->getClientOriginalExtension();
            // $destinationPath = storage_path('app/investigation_report/');
            // $fileStore[] = $doc_file->move($destinationPath, $file_profile);
            // array_push($uploaded_doc,$file_profile);
            // $uploaded_doc[$doc] = $file_profile;
            } else {
            $img_data = null;
            }

            DB::connection('pgsql')->beginTransaction();
            DB::connection('pgsql_paywrite')->beginTransaction();
            DB::connection('pgsql_encwrite')->beginTransaction();
            try{
                // foreach ($uploaded_doc as $doc_type => $doc) {
                //     $ben_docs = DB::table($ben_docs_table)->where('ben_id', $ben_id)
                //       ->where('doc_type_id', $doc_type)->where('is_active', TRUE)->first();
                //     $doc_type_name = DocumentType::where('id', $doc_type)->first();
                //     $ben_docs_insert = [
                //       'ben_id' => $ben_id,
                //       'doc_type_id' => $doc_type,
                //       'doc_name' => $base_url . "/jaibangla/storage/app/investigation_report/" . $doc,
                //       'doc_type_name' => $doc_type_name->doc_name,
                //       'is_active' => TRUE,
                //       'created_at' => date('Y-m-d H:i:s', time()),
                //       'updated_at' => date('Y-m-d H:i:s', time()),
                //     ];
                //     $is_upload = DB::table($ben_docs_table)->insert($ben_docs_insert);
                    
                //     if ($ben_docs != null) {
                //       $filename = basename($ben_docs->doc_name);
                //       if ($scheme_id == 10 || $scheme_id == 11 || $scheme_id == 2) {
                //         if (file_exists(storage_path('app/investigation_report/') . '//' . $filename)) {
                //             // echo 1;die;
                //           rename(storage_path('app/investigation_report/') . '//' . $filename, storage_path('app/investigation_report_back/') . '//' . $filename);
                //         }
                //       }
                //       $archiveQuery = "INSERT INTO " . $ben_docs_arc_table . "(
                //         ben_id, doc_type_id, doc_name, doc_type_name, created_at, deleted_at)
                //         SELECT ben_id, doc_type_id, doc_name, doc_type_name, created_at,now() FROM ".$ben_docs_table." WHERE ben_id=".$ben_id." AND doc_type_id=".$doc_type." AND is_active=true AND id=".$ben_docs->id;
                //       $is_insert = DB::statement($archiveQuery);
                //       if ($is_insert) {
                //         $is_upload1 = DB::table($ben_docs_table)->where('ben_id', $ben_id)->where('doc_type_id', $doc_type)->where('is_active', TRUE)->where('id', $ben_docs->id)->update(['is_active' => FALSE]);
                //       }
                //     }
                // }
                $fun_call = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                    in_beneficiary_id => ".$ben_id.",
                    in_scheme_id => ".$scheme_id.",
                    in_document_type => ".$doc.",
                    in_attched_document => '".$base64."',
                    in_created_by_level => '".$mapping_level."',
                    in_created_by => ".$user_id.",
                    in_ip_address => '".$ip_address."',
                    in_document_extension => '".$extension."',
                    in_document_mime_type => '".$mime_type."',
                    in_created_by_dist_code => ".$benDetails->created_by_dist_code.",
                    in_created_by_local_body_code => ".$benDetails->created_by_local_body_code.",
                    in_doc_type_name => '".$doc_list[0]->doc_name."',
                    in_datetime => '". $c_datetime ."'
                    );"
                  );
                  $is_upload = $fun_call[0]->ben_docs_insert_archive;
                // Insert Unique Aadhar Table.
                $aadhar_no = DB::table($beneficiary_table)->where('id', $ben_id)->value('aadhar_no');
                $uniqueAadharTableCount = DB::table($scheme_short_code.'.ben_aadhar_no_unique')->where('aadhar_no', $aadhar_no)->count();
                // print_r($insertUniqueAadhar);die;
                // echo $uniqueAadharTable;die;
                if ($uniqueAadharTableCount == 0) {
                    $insertUniqueAadhar = [
                        'aadhar_no' => $aadhar_no,
                        'total_count' => 1
                    ];
                    // echo "IF";die;
                    $insertAadhar = DB::table($scheme_short_code.'.ben_aadhar_no_unique')->insert($insertUniqueAadhar);
                    // echo $insertAadhar;die;
                } 
                else if ($uniqueAadharTableCount == 1) {
                    $insertAadhar = 1;
                } 
                else{
                    $insertAadhar = 0;
                    $error_msg = 'This Aadhar Number is already exist.';
                }

                // Insert Unique Mobile Table.
                $mobile_no = DB::table($beneficiary_table)->where('id', $ben_id)->value('mobile_no');

                if ($scheme_id == 13) {
                    $insertUniqueMobile = [
                        'mobile_no' => $mobile_no,
                        'total_count' => 1
                    ];
                    $insertMobile = DB::table($scheme_short_code.'.ben_mobile_no_unique')->insert($insertUniqueMobile);
                }else {
                    $uniqueMobileTable = DB::table($scheme_short_code.'.ben_mobile_no_unique')->where('mobile_no', $mobile_no)->count();
                    // echo $uniqueAadharTable;die;
                    if ($uniqueMobileTable == 0) {
                        $insertUniqueMobile = [
                            'mobile_no' => $mobile_no,
                            'total_count' => 1
                        ];
                        $insertMobile = DB::table($scheme_short_code.'.ben_mobile_no_unique')->insert($insertUniqueMobile);
                    }
                    else if ($uniqueMobileTable == 1) {
                        $insertMobile = 1;
                    } 
                    else{
                        $insertMobile = 0;
                        $error_msg = 'This Mobile Number is already exist.';
                    }
                }

                // Insert Unique Bank Table.
                $bank_accno = $benDetails->bank_code;
                $bank_ifsc = $benDetails->bank_ifsc;
                // print_r($bank_ifsc);die;
                if ($scheme_id == 13) {
                    $insertUniqueBank = [
                        'bank_code' => $bank_accno,
                        'bank_ifsc' => $bank_ifsc,
                        'total_count' => 1
                    ];
                    $insertBank = DB::table($scheme_short_code.'.ben_bank_account_no_unique')->insert($insertUniqueBank);
                }else{
                    $uniqueBankTable = DB::table($scheme_short_code.'.ben_bank_account_no_unique')->where('bank_code', $bank_accno)->where('bank_ifsc', $bank_ifsc)->count();
                    // echo $uniqueAadharTable;die;
                    if ($uniqueBankTable == 0) {
                        $insertUniqueBank = [
                            'bank_code' => $bank_accno,
                            'bank_ifsc' => $bank_ifsc,
                            'total_count' => 1
                        ];
                        $insertBank = DB::table($scheme_short_code.'.ben_bank_account_no_unique')->insert($insertUniqueBank);
                    } 
                    else if ($uniqueBankTable == 1) { 
                        $insertBank = 1;
                    }
                    else{
                        $insertBank = 0;
                        $error_msg = 'This bank account & IFSC is already exist.';
                    }
                }
                // dump($is_upload); dump($insertAadhar); dump($insertMobile); dump($insertBank); die;
                if ($is_upload == 1 && $insertAadhar == 1 && $insertMobile == 1 && $insertBank == 1) {
                    $updateBenDetailsInsert = UpdateBenDetails::insert($updateBenDetailsData);
                    if ($updateBenDetailsInsert == 1) {
                        $updateBenTable = [
                            'is_rejected' => 0,
                            'is_approved' => 1,
                            'payment_suspended' => null,
                            'is_verified' => 1,
                            'jnmp_remarks' => $remarks,
                            'reactive_reason' => $reactive_reason,
                        ];
                        $updatePaymentTable = [
                            'ben_status' => 1,
                        ];
                        $is_update = DB::table($beneficiary_table)->where('id', $ben_id)
                                    ->where('jnmp_aadhar_mapped', '=', 1)
                                    ->where('payment_suspended', '=', 1)
                                    ->where('next_level_role_id', '=', 0)
                                    ->where('scheme_id', $scheme_id)
                                    ->update($updateBenTable);
                        $payment_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->where('ben_status', '=', 2)->update($updatePaymentTable);
                    }
                    DB::connection('pgsql')->commit();
                    DB::connection('pgsql_paywrite')->commit();
                    DB::connection('pgsql_encwrite')->commit();
                    $response = array(
                        'status' => 1, 'msg' => 'Beneficiary Active Successfully',
                        'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                    );
                }else{
                    $return_text = $error_msg;
                    DB::connection('pgsql')->rollback();
                    DB::connection('pgsql_paywrite')->rollback();
                    DB::connection('pgsql_encwrite')->rollback();
                    
                    $return_msg = array("" . $return_text);
                    $response = array(
                        'status' => 0, 'msg' => $return_msg,
                        'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                    );
                }
                // if($is_update){
                    
                // }else{

                // }
            }catch(\Exception $e){
                dd($e);
                DB::rollback();
                $return_status = 0;
                
                $return_msg = array("" . $return_text);
                return $response = array(
                    'status' => $return_status, 'msg' => $return_msg,
                    'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                );
            }
        }else{
            $return_status = 0;
            $return_msg = $validator->errors()->all();
            $response = array(
                'status' => $return_status, 'msg' => $return_msg,
                'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
            );
        }
      }catch (\Exception $e) {
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

    public function generateExcel(Request $request)
    {
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") AND id in(1,2,3,5,6,7,8,9,10,11,13,17,19) order by rank"));
        if ($designation_id_old == 'Admin') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
        } else if ($designation_id_old == 'Approver') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id']) { // == 11 || $roleObj['scheme_id'] == 13
                    $is_urban = $roleObj['is_urban'];
                    $district_code = $roleObj['district_code'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }

            if (empty($district_code))
                return redirect("/")->with('success', 'User Disabled. ');
        } else {
            return redirect("/")->with('success', 'User Disabled. ');
        }
        $dist_code = $request->district;
        $scheme_id = $request->scheme_id;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $gp_ward = $request->gp_ward;
        $muncid = $request->muncid;
        $table_name = $this->getSchemaName($scheme_id);
        $schemeObj = Scheme::where('id',$scheme_id)->first();
        $user_msg = 'Aadhar Mapped With Janma Mrityu Tathya';

        $query = $this->getDataRows($district_code,$blockCode,$block,$gp_ward,$muncid,$table_name,$dist_code);
        $result = DB::connection('pgsql_mis')->select($query);
        $excelarr[] = array(
            'Name', 'Father Name', 'Block/Municipality', 'GP/Ward', 'Aadhar Number', 'Mobile Number',
        );

        foreach ($result as $arr) {
            $excelarr[] = array(
                'Name' => trim($arr->name),
                'Father Name' => trim($arr->father_name),
                'Block/Municipality' => trim($arr->block_subdiv),
                'GP/Ward' => trim($arr->gp_ward),
                'Aadhar Number' => trim($arr->aadhar_no),
                'Mobile Number' => trim($arr->mobile_no),
            );
        }
        $file_name = $schemeObj->scheme_name.' '.$user_msg .' '.  date('d/m/Y');
        Excel::create($file_name, function ($excel) use ($excelarr) {
            $excel->setTitle('Jai Bangla Duplicate Report');
            $excel->sheet('Jai Bangla Duplicate Report', function ($sheet) use ($excelarr) {
                $sheet->fromArray($excelarr, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    public function getDataRows($district_code,$blockCode,$block,$gp_ward,$muncid,$table_name){
        $data = "SELECT id,scheme_id,jnmp_aadhar_mapped,CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) AS name, CONCAT(father_fname,' ',father_mname,' ',father_lname) AS father_name, '********' ||RIGHT(TRIM(aadhar_no)::varchar,4) AS aadhar_no, mobile_no, TRIM(block_ulb_name) AS block_subdiv, TRIM(gp_ward_name) AS gp_ward FROM ".$table_name." WHERE jnmp_aadhar_mapped = 1 AND payment_suspended = 1 AND next_level_role_id = 0";
            if (!empty($district_code)) {
                $data .= " AND created_by_dist_code = " . $district_code;
            }
            if (!empty($blockCode)) {
                $data .= " AND created_by_local_body_code = " . $blockCode;
            }
            if (!empty($dist_code)) {
                $data .= " AND created_by_dist_code = " . $dist_code;
            }
            if (!empty($block)) {
                $data .= " AND created_by_local_body_code = " . $block;
            }
            if (!empty($gp_ward)) {
                $data .= " AND gp_ward_code = " . $gp_ward;
            }
            if (!empty($muncid)) {
                $data .= " AND block_ulb_code = " . $muncid;
            }
            $data .= " ORDER BY name";
            // echo $data;die();
            return $data;
    }
}
