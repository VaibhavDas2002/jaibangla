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
use App\BankDetails;
use App\Helpers\AuthChecker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class NoDupWorkflowController extends Controller
{
   private $aadhar_doc_type_id;
    private $bank_doc_type_id;
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
        $this->aadhar_doc_type_id = 6;
        $this->bank_doc_type_id = 10;
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
        $table_name =  strtolower($schema_name) . '.beneficiary';
        } else {
        $table_name =  'pension.beneficiary';
        }
        return $table_name;
    }

    public function index()
    {
        // $designation_id = Auth::user()->designation_id;
        if (AuthChecker::VerifierChecker()) {
          $is_active = 1;
        } else {
          $is_active = 0;
        }
        if ($is_active == 0) {
          return redirect("/")->with('error', 'User Disabled. ');
        }
        $user_id = AuthChecker::getUserId();;
        $mapObj = DB::table('public.duty_assignement')->where('user_id', $user_id)->where('is_active', 1)->first();
        $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . " )"));
        if (AuthChecker::VerifierChecker()) {
          if (count($scheme) > 0) {
            $municipality_visible = 0;
            $gp_ward_visible = 1;
            $muncList = collect([]);
            $gpList = collect([]);
            if ($mapObj->is_urban == 1) {
              $urban_body_code = $mapObj->urban_body_code;
              $muncList = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();
              $municipality_visible = 1;
              return view('No-Duplicate-Update/verifier_index', [
                'schemes' => $scheme,
                'mapLevel' => $mapObj->mapping_level ,
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
              return view('No-Duplicate-Update/verifier_index', [
                'schemes' => $scheme,
                'mapLevel' => $mapObj->mapping_level ,
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

    public function getNoDupList(Request $request)
    {
        if ($request->ajax()) {
            $scheme_id = $request->scheme_id;
            $filter_type = $request->filter_type;
            if (empty($scheme_id)) {
                return redirect("/")->with('error', 'Scheme Not Valid');
            }
            if (!ctype_digit($scheme_id)) {
                return redirect("/")->with('error', 'Scheme Not Valid');
            }
            $user_id = AuthChecker::getUserId();;
            $designation_id = Auth::user()->designation_id;
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
            if (AuthChecker::VerifierChecker()) {
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
            if ($mapping_level == 'Block') {
                $query = '';
                $query = "SELECT * FROM pension.beneficiaries WHERE scheme_id = ".$scheme_id." AND is_clean = 2 AND next_level_clean_id IS null AND created_by_local_body_code = ".$urban_body_code." AND created_by_dist_code = ".$district_code." ";
                if ($filter_type == 1) {
                  $query .= "AND dup_aadhar = 1";
                }
                if ($filter_type == 2) {
                  $query .= "AND no_aadhar = 1";
                }
                if ($filter_type == 3) {
                  $query .= "AND dup_bank = 1";
                }
                if ($filter_type == 4) {
                  $query .= "AND dup_mobile = 1";
                }
                if ($filter_type == 5) {
                  $query .= "AND no_mobile = 1";
                }
                if ($filter_type == 6) {
                  $query .= "AND is_bank_failed = 1";
                }
                if ($request->gp_ward) {
                    $query .= " AND gp_ward_code = ".$request->gp_ward."";
                }
                $query .= " ORDER BY id";
                // dd($query);
                $data = DB::connection('pgsql')->select($query);
            } elseif ($mapping_level == 'Subdiv') {
                $query = '';
                $query = "SELECT * FROM pension.beneficiaries WHERE scheme_id = ".$scheme_id." AND is_clean = 2 AND created_by_local_body_code = ".$urban_body_code." AND created_by_dist_code = ".$district_code." ";
                if ($request->muncid) {
                    $query .= " AND gp_ward_code = ".$request->muncid."";
                }
                $query .= " ORDER BY id";
                $data = DB::connection('pgsql')->select($query);
            }
            return datatables()->of($data)
            ->addColumn('view', function($data){
                $btn = '';
                $btn = '<a href="'.route('editApplicantDetails', ['id' => $data->id, 'scheme_id' => $data->scheme_id]).'" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit </a>';
                return $btn;
            })
            ->addColumn('name', function ($data) {
                // return $data->getName();
                return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
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
            ->addColumn('status', function($data){
              $sl = 1;
              // $sl++;
              $status = '';
              if ($data->dup_aadhar == 1) {
                $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. Duplicate Aadhar</b></span> <br>';
              }
              if ($data->dup_mobile == 1) {
                $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. Duplicate Mobile</b></span> <br>';
              }
              if ($data->dup_bank == 1) {
                $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. Duplicate Bank</b></span> <br>';
              }
              if ($data->no_aadhar == 1) {
                $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. No Aadhar</b></span> <br>';
              }
              if ($data->no_mobile == 1) {
                $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. No Mobile</b></span> <br>';
              }
              if ($data->is_bank_failed == 1) {
                if ($data->pay_validated == 3) {
                  $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. Payment Failure SBI</b></span>';
                } elseif ($data->pay_validated == 4) {
                  $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. Payment Failure RBI</b></span>';
                } else {
                  $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. Payment Failure IFMS</b></span>';
                }
              }
              // $sl++;
              return $status;
            })
            ->rawColumns(['view', 'name', 'aadhar_no', 'status'])
            ->make(true);
        }
    }

    public function editApplicantDetails(Request $request)
    {
        try {
          $designation_id = Auth::user()->designation_id;
          $user_id = AuthChecker::getUserId();
          $doc_type_id_arr = array($this->aadhar_doc_type_id,$this->bank_doc_type_id);
          $supporting = DocumentType::select('id', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->whereIn("id", $doc_type_id_arr)->get();
          $user_id = AuthChecker::getUserId();
          $scheme_id = $request->scheme_id;
          $ben_id = $request->id;
          if (!ctype_digit($scheme_id)) {
            return redirect("/")->with('error', 'Scheme Not Valid');
          }
          if (empty($ben_id)) {
            return redirect("/")->with('danger', 'Applicant ID Not Found');
          }
          if (!is_numeric($ben_id)) {
            return redirect("/")->with('danger', 'Applicant ID Not Valid');
          }
          $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
          if (empty($scheme_obj)) {
            return redirect("/")->with('danger', 'Scheme Not Found');
          }
          $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
          if (empty($duty_obj)) {
            return redirect("/")->with('danger', 'Not Allowed.');
          }
          $district_code = $duty_obj->district_code;
          if (AuthChecker::VerifierChecker()) {
            $query = DB::connection('pgsql')->table('pension.beneficiaries')
            ->where('created_by_dist_code', $district_code)
            ->where('id', $ben_id)
            ->where('scheme_id', $scheme_id)
            ->where('is_clean', 2);
          }else {
            return redirect("/")->with('danger', 'Not Allowed..');
          }
          $row = $query->first();
          if (empty($row)) {
            return redirect("/")->with('danger', 'Not Allowed...');
          }
          // Get District Name
          if ($row->dist_code != "") {
            $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
            $district_name = $district->district_name;
          }
          // Get Bank Name
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
          // Get GP Name
          $gp_name = "";
          if ($row->gp_ward_code != "") {
            if ($row->rural_urban_id == 1) {
              $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
              if (!empty($gp_ward)) {
                $gp_name =  $gp_ward->urban_body_ward_name;
              }
            } else {
              $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
              if (!empty($gp)) {
                $gp_name =  $gp->gram_panchyat_name;
              }
            }
          }
          $row->gp_name = $gp_name;
          // dd($row);
          $getAadharDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 6)->count();
          $getBankDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 10)->count();
          // dd($getBankDoc);
            $PaymentErrorType = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->first();
          $enable_validation = array();
          $enableArr = [];
          if($row->dup_bank == 1)
          {
            $enable = 'Bank';
            array_push($enable_validation, $enable);
          }
          if ($row->dup_aadhar == 1 || $row->no_aadhar == 1) {
            $enable = 'aadhar';
            array_push($enable_validation, $enable);
          }
          if ($row->dup_mobile == 1 || $row->no_mobile == 1) {
            $enable = 'Mobile';
            array_push($enable_validation, $enable);
          }
          // print_r($enable_validation[0] == 'Bank');die;
          return view(
            'No-Duplicate-Update.ben_details_update_verifier',
            [
              'scheme_id' => $scheme_id,
              'row' => $row,
              'district_name' => $district_name,
              'block_name' => $block_name,
              'gp_name' => $gp_name,
              'district_code' => $district_code,
              'designation_id' => $designation_id,
              'enable_validation' => $enable_validation,
              'getAadharDoc' => $getAadharDoc,
              'getBankDoc' => $getBankDoc,
              'PaymentErrorType' => $PaymentErrorType,
              // 'encloser_list' => $encloser_list,
              // 'fetch_lb' => $fetch_lb,
              // 'can_verify' => $can_verify,
              // 'back_to_lb' => $back_to_lb,
              // 'undo' => $undo,
              // 'can_reject' => $can_reject,
              // 'transfer_sc' => $transfer_sc,
              // 'transfer_st' => $transfer_st,
              // 'transfer_oap' => $transfer_oap,
              // 'can_approve' => $can_approve,
              // 'age_supporting' => $age_supporting,
              // 'reason_order' => $reason_order,
            ]
          );
        } catch (\Exception $e) {
          dd($e);
          //throw $th;
        }
    }

    public function updateApplicantDetails(Request $request)
    {
      // dd($request->all());
      // dd($request->new_mobile_no);
      try {
        $designation_id = Auth::user()->designation_id;
        $user_id = AuthChecker::getUserId();;
        if ($designation_id != 'Verifier') {
            return redirect("/")->with('error', 'Not Allowded');
        }
        $scheme_id = $request->scheme_id;
        $ben_id = $request->id;
        $bank_checked = $request->bank_checked;
        // dd($bank_checked);
        if ($request->new_bank_code != $request->old_bank_code) {
          $isSame = 0;
        } else {
          $isSame = 1;
        }
        // dd($isSame);
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
        $row = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where('scheme_id', $scheme_id)->where('is_clean', 2)->first();
        $paymentErrorType = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->first();
        $bankCodeRow = DB::connection('pgsql')->table('ifsc.bank_details')->where('ifsc', $request->bank_ifsc_code)->first();
        $bankDupRow = DB::connection('pgsql')->table('pension.ben_payment_details_bank_code_dup')->where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', -97)->count();
        // dd($bankCodeRow);
        $aadhar_doc = 6; // For Aadhar Document
        $bank_doc = 10; // For Bank Document
        $rules = array();
        $attributes = array();
        if ($row->dup_bank == 1) {
          $bankRules = array(            
            'old_bank_code' => 'required|max:20',
            'old_bank_ifsc' => 'required|max:11',
            'old_bank_name' => 'required',
            'old_bank_branch' => 'required',
            'new_bank_branch' => 'required',
            'new_bank_name' => 'required',
            'new_bank_code' => 'required|max:20',
            'bank_ifsc_code' => 'required|max:11',
          );
          $bankAttributes = [
            'old_bank_code' => 'Old Bank Code',
            'old_bank_ifsc' => 'Old Bank IFSC',
            'old_bank_name' => 'Old Bank Name',
            'old_bank_branch' => 'Old Bank Branch',
            // 'bank_branch' => 'New Bank Branch',
            'bank_ifsc_code' => 'Bank IFSC',
            'new_bank_branch' => 'Bank Branch',
            'new_bank_name' => 'Bank Name',
            'new_bank_code' => 'Bank Account',
          ];
          $rules = array_merge($rules, $bankRules);
          $attributes = array_merge($attributes, $bankAttributes);
        }
        if ($row->no_aadhar == 1) {
          $aadharRules = array(
            'new_aadhar_no' => 'required|digits:12',
            'old_aadhar' => 'required|nullable|digits:12',
            'new_aadhar_doc' => 'required',
          );
          $aadharAttributes = [
            'old_aadhar' => 'Old Aadhar Number',
            'new_aadhar_doc' => 'Aadhar Document',
            'new_aadhar_no' => 'New Aadhar No.',
          ];
          $rules = array_merge($rules, $aadharRules);
          $attributes = array_merge($attributes, $aadharAttributes);
        } elseif ($row->dup_aadhar == 1) {
          $aadharRules = array(
            'new_aadhar_no' => 'required|digits:12',
          );
          $aadharAttributes = [
            'new_aadhar_no' => 'New Aadhar Number',
          ];
          $rules = array_merge($rules, $aadharRules);
          $attributes = array_merge($attributes, $aadharAttributes);
        }
        if ($row->dup_mobile == 1) {
          $mobileRules = array(
            'old_mobile' => 'required|digits:10',
            'new_mobile_no' => 'required|digits:10',
          );
          $mobileAttributes = [
            'old_mobile' => 'Old Mobile No.',
            'new_mobile_no' => 'New Mobile No',
          ];
          $rules = array_merge($rules, $mobileRules);
          $attributes = array_merge($attributes, $mobileAttributes);
        } elseif ( $row->no_mobile == 1) {
          $mobileRules = array(
            'new_mobile_no' => 'required|digits:10',
          );
          $mobileAttributes = [
            'new_mobile_no' => 'New Mobile No.',
          ];
          $rules = array_merge($rules, $mobileRules);
          $attributes = array_merge($attributes, $mobileAttributes);
        }
        $messages = [
          'required' => 'The :attribute field is required.',
          'max' => 'Total :max characters allowed for :attribute'
        ];
        $doc_aadhar_arr = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->where("id", $aadhar_doc)->first()->toArray();
        // dd($doc_aadhar_arr);
        $doc_bank_arr = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->where("id", $bank_doc)->first()->toArray();
        // dd($doc_bank_arr);
        $bank_doc_count = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('document_type', $bank_doc)->count();
        // dd($bank_doc_count);
        if ($doc_aadhar_arr && $row->no_aadhar == 1) {
          $required = 'required';
          $rules['new_aadhar_doc'] = $required . '|mimes:' . $doc_aadhar_arr['doc_type'] . '|max:' . $doc_aadhar_arr['doc_size_kb'] . ',';
          $messages['new_aadhar_doc.max'] = "The file uploaded for " . $doc_aadhar_arr['doc_name'] . " size must be less than " . $doc_aadhar_arr['doc_size_kb'] . " KB";
          $messages['new_aadhar_doc.mimes'] = "The file uploaded for " . $doc_aadhar_arr['doc_name'] . " must be of type " . $doc_aadhar_arr['doc_type'];
          $messages['new_aadhar_doc.required'] = "Document for " . $doc_aadhar_arr['doc_name'] . " must be uploaded";
        }
        if ($doc_bank_arr && $bank_doc_count == 0) {
          $required = 'required';
          $rules['new_bank_doc'] = $required . '|mimes:' . $doc_bank_arr['doc_type'] . '|max:' . $doc_bank_arr['doc_size_kb'] . ',';
          $messages['new_bank_doc.max'] = "The file uploaded for " . $doc_bank_arr['doc_name'] . " size must be less than " . $doc_bank_arr['doc_size_kb'] . " KB";
          $messages['new_bank_doc.mimes'] = "The file uploaded for " . $doc_bank_arr['doc_name'] . " must be of type " . $doc_bank_arr['doc_type'];
          $messages['new_bank_doc.required'] = "Document for " . $doc_bank_arr['doc_name'] . " must be uploaded";
        }
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        // dd($validator);
        if (!$validator->passes()) {
          // dd($validator->errors()->all());
          return redirect()->back()
              ->withErrors($validator->errors()->all())
              ->withInput();
        } else {
          // dd('Correct');
          if (AuthChecker::VerifierChecker()) {
            $is_active = 1;
          } else {
            $is_active = 0;
          }
          // dd($is_active);
          if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disabled. ');
          } else {
            //  dd($request->all()); 
            $beneficiaryRow = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where('scheme_id', $scheme_id)->where('is_clean', 2)->first();
            // dd($beneficiaryRow);
            $old_values = [];
            $new_values = [];
            $old_values['aadhar_no'] = $beneficiaryRow->aadhar_no;
            $old_values['mobile_no'] = $beneficiaryRow->mobile_no;
            $old_values['bank_code'] = trim($beneficiaryRow->bank_code);
            $old_values['bank_ifsc'] = trim($beneficiaryRow->bank_ifsc);
            $old_values['bank_name'] = trim($beneficiaryRow->bank_name);
            $old_values['branch_name'] = trim($beneficiaryRow->branch_name);

            $new_values['aadhar_no'] = $request->new_aadhar_no;
            $new_values['mobile_no'] = $request->new_mobile_no;
            $new_values['bank_code'] = trim($request->new_bank_code);
            $new_values['bank_ifsc'] = $request->bank_ifsc_code;
            $new_values['bank_name'] = trim($request->new_bank_name);
            $new_values['branch_name'] = trim($request->new_bank_branch);
            // dd(json_encode($old_values));
            $insertUpdateBenDetails = [
              'original_application_id' => $beneficiaryRow->id,
              'dist_code' => $beneficiaryRow->dist_code,
              'scheme_id' => $beneficiaryRow->scheme_id,
              'is_clean' => $beneficiaryRow->is_clean,
              'old_data' => json_encode($old_values),
              'new_data' => json_encode($new_values),
              'user_id' => AuthChecker::getUserId(),
              'ip_address' => request()->ip(),
              'update_code' => 500, // For No - Dup Verify.
              'created_at' => date('Y-m-d H:i:s'),
            ];
            $updateBenDetails = [
              'next_level_clean_id' => 2,
              'is_clean' => 1,
              'aadhar_no' => $request->new_aadhar_no,
              'mobile_no' => $request->new_mobile_no,
              'bank_code' => trim($request->new_bank_code),
              'bank_ifsc' => $request->bank_ifsc_code,
              'bank_name' => trim($request->new_bank_name),
              'branch_name' => trim($request->new_bank_branch)
            ];
            $benPaymentDetails = [
              'last_accno' => trim($request->new_bank_code),
              'last_ifsc' => $request->bank_ifsc_code,
              // 'acc_validated' => 0,
              'npci_bank_code' => $bankCodeRow->bank_code
              // 'ben_status' => 1
            ];
            $dupBankTable = [
              'bank_code' => trim($request->new_bank_code),
              'bank_name' => trim($request->new_bank_name),
              'branch_name' => trim($request->new_bank_branch),
              'bank_ifsc' => $request->bank_ifsc_code,
              'next_level_role_id' => 101
            ];
            if ($row->is_bank_failed == 1) {
              if ($paymentErrorType->pay_validated == 3 || $paymentErrorType->pay_validated == 4 || $paymentErrorType->pay_validated == 5) {
                $getNpciBankCode = BankDetails::where('ifsc', $request->bank_ifsc_code)->first();
                $failedPaymentArr = [];
                $newPaymentDetails = [];
                $insertUpdateBenDetails = ['next_level_role_id' => 5];
                $newPaymentDetails['new_bank_name'] = trim($request->new_bank_name);
                $newPaymentDetails['new_bank_branch'] = trim($request->new_bank_branch);
                $newPaymentDetails['new_bank_ifsc'] = $request->bank_ifsc_code;
                $newPaymentDetails['new_bank_code'] = trim($request->new_bank_code);
                $newPaymentDetails['npci_bank_code'] = $getNpciBankCode->bank_code;
  
                $failedPaymentArr['updated_details'] = json_encode($newPaymentDetails);
                $failedPaymentArr['edited_status'] = 1;
                $failedPaymentArr['updated_at'] = date('Y-m-d H:i:s');
              }
            }

            // Upload Aadhar Card
            if ($request->hasFile('new_aadhar_doc')) {
              // dd('new_aadhar_doc');
              $base_url = url('/');
              $uploaded_doc = array();
              if ($request->hasFile('new_aadhar_doc')) {
              $doc_file = $request->file('new_aadhar_doc');
              $img_data = file_get_contents($doc_file);
              // echo $img_data;die;
              $u_extension = $doc_file->getClientOriginalExtension();
              $mime_type = $doc_file->getMimeType();
              // dd($mime_type);
              if(strtolower($mime_type)=='image/jpeg'){
                  if($u_extension=='jpg' || $u_extension=='jpeg'){
                    $extension=$u_extension;
                  }
                  else{
                      $errorMsg = "You are trying to upload an incorrect file for ".$doc_aadhar_arr['doc_name'];
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
                  $errorMsg = "You are trying to upload an incorrect file for ".$doc_aadhar_arr['doc_name'];
                  return $response = array(
                      'status' => 0, 'msg' => $errorMsg,
                      'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                  );
              }
              if($u_extension!=$extension){
                  $errorMsg = "You are trying to upload an incorrect file for ".$doc_aadhar_arr['doc_name'];
                  return $response = array(
                      'status' => 0, 'msg' => $errorMsg,
                      'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                  );
              }

              $base64 = base64_encode($img_data);
              $ip_address = request()->ip();
              $c_datetime = date('Y-m-d H:i:s', time());
              $user_id = AuthChecker::getUserId();;
              } else {
              $img_data = null;
              }
            }
            // Upload Bank Document
            if ($request->hasFile('new_bank_doc')) {
              $base_url = url('/');
              $uploaded_doc = array();
              if ($request->hasFile('new_bank_doc')) {
              $doc_file = $request->file('new_bank_doc');
              $img_data = file_get_contents($doc_file);
              // echo $img_data;die;
              $u_extension = $doc_file->getClientOriginalExtension();
              $mime_type = $doc_file->getMimeType();

              if(strtolower($mime_type)=='image/jpeg'){
                  if($u_extension=='jpg' || $u_extension=='jpeg'){
                    $extension=$u_extension;
                  }
                  else{
                      $errorMsg = "You are trying to upload an incorrect file for ".$doc_bank_arr['doc_name'];
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
                  $errorMsg = "You are trying to upload an incorrect file for ".$doc_bank_arr['doc_name'];
                  return $response = array(
                      'status' => 0, 'msg' => $errorMsg,
                      'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                  );
              }
              if($u_extension!=$extension){
                  $errorMsg = "You are trying to upload an incorrect file for ".$doc_bank_arr['doc_name'];
                  return $response = array(
                      'status' => 0, 'msg' => $errorMsg,
                      'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                  );
              }

              $base64 = base64_encode($img_data);
              $ip_address = request()->ip();
              $c_datetime = date('Y-m-d H:i:s', time());
              $user_id = AuthChecker::getUserId();;
              } else {
              $img_data = null;
              }
            }
            DB::connection('pgsql')->beginTransaction();
            DB::connection('pgsql_encwrite')->beginTransaction();
            DB::connection('pgsql_paywrite')->beginTransaction();
            $updateBenDetailsCount = DB::connection('pgsql')->table('public.update_ben_details')->where('original_application_id', $ben_id)->where('is_clean', 2)->where('scheme_id', $scheme_id)->where('update_code', 500)->count();
            $benAadharCheck = DB::connection('pgsql')->table('pension.beneficiaries')->where('is_clean', 1)->where('aadhar_no', $request->new_aadhar)->count();
            $benMobileCheck = DB::connection('pgsql')->table('pension.beneficiaries')->where('is_clean', 1)->where('mobile_no', $request->new_mobile)->count();
            $benBankCheck = DB::connection('pgsql')->table('pension.beneficiaries')->where('is_clean', 1)->where('bank_code', $request->bank_code)->count();
            // dd($updateBenDetailsCount);
            if ($request->hasFile('new_aadhar_doc')) {
              // dd($request->hasFile('new_aadhar_doc'));
              $fun_call = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                in_beneficiary_id => ".$ben_id.",
                in_scheme_id => ".$scheme_id.",
                in_document_type => ".$aadhar_doc.",
                in_attched_document => '".$base64."',
                in_created_by_level => '".$mapping_level."',
                in_created_by => ".AuthChecker::getUserId().",
                in_ip_address => '".request()->ip()."',
                in_document_extension => '".$extension."',
                in_document_mime_type => '".$mime_type."',
                in_created_by_dist_code => ".$beneficiaryRow->created_by_dist_code.",
                in_created_by_local_body_code => ".$beneficiaryRow->created_by_local_body_code.",
                in_doc_type_name => '".$doc_aadhar_arr['doc_name']."',
                in_datetime => '". $c_datetime ."'
                );"
              );
              $is_aadhar_upload = $fun_call[0]->ben_docs_insert_archive;
            }
            if ($request->hasFile('new_bank_doc')) {
              $fun_call = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                in_beneficiary_id => ".$ben_id.",
                in_scheme_id => ".$scheme_id.",
                in_document_type => ".$bank_doc.",
                in_attched_document => '".$base64."',
                in_created_by_level => '".$mapping_level."',
                in_created_by => ".AuthChecker::getUserId().",
                in_ip_address => '".request()->ip()."',
                in_document_extension => '".$extension."',
                in_document_mime_type => '".$mime_type."',
                in_created_by_dist_code => ".$beneficiaryRow->created_by_dist_code.",
                in_created_by_local_body_code => ".$beneficiaryRow->created_by_local_body_code.",
                in_doc_type_name => '".$doc_bank_arr['doc_name']."',
                in_datetime => '". $c_datetime ."'
                );"
              );
              $is_bank_upload = $fun_call[0]->ben_docs_insert_archive;
            }
            // echo $is_aadhar_upload, $is_bank_upload; die;
            if ($row->dup_aadhar == 1 || $row->dup_bank == 1) {
              $is_bank_upload = 1;
              $is_aadhar_upload = 1;
            }
            if ($benAadharCheck > 0) {
              $errorMsg = "This Aadhar No: ".$request->new_aadhar." is already exist.";
              return redirect('editApplicantDetails')->with('error', $errorMsg);
            }
            elseif ($benMobileCheck > 0) {
              $errorMsg = "This Mobile No: ".$request->new_mobile." is already exist.";
              return redirect('editApplicantDetails')->with('error', $errorMsg);
            }
            elseif ($benBankCheck > 0) {
              $errorMsg = "This Bank Account: ".$request->bank_code." is already exist.";
              return redirect('editApplicantDetails')->with('error', $errorMsg);
            } elseif ($benAadharCheck == 0 && $benMobileCheck == 0 && $benBankCheck == 0 && ($is_aadhar_upload == 1 || $is_bank_upload == 1)) {
              if ($updateBenDetailsCount > 0) {
                $updateBenDetailsAction = DB::connection('pgsql')->table('public.update_ben_details')->where('original_application_id', $ben_id)->where('is_clean', 2)->where('scheme_id', $scheme_id)->where('update_code', 500)->update($insertUpdateBenDetails);
              } else {
                $updateBenDetailsAction = DB::connection('pgsql')->table('public.update_ben_details')->insert($insertUpdateBenDetails);
              }
              if ($isSame == 0) {
                $updateBenPaymentTable = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->update($benPaymentDetails);
              } else {
                $updateBenPaymentTable = 1;
              }
              if ($row->is_bank_failed == 1) {
                if ($paymentErrorType->pay_validated == 3 || $paymentErrorType->pay_validated == 4 || $paymentErrorType->pay_validated == 5) {
                  $updateFailedPayment = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->where('edited_status', 0)->whereIn('failed_type', [3,4,5])->update($failedPaymentArr);
                }
              } else {
                  $updateFailedPayment = 1;
              }
              $updateBenTable = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where('is_clean', 2)->where('scheme_id', $scheme_id)->update($updateBenDetails);
              if ($bankDupRow > 0) {
                $updateBankDup = DB::connection('pgsql')->table('pension.ben_payment_details_bank_code_dup')->where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', -97)->update($dupBankTable);
              } else {
                $updateBankDup = 1;
              }

            } else {
              DB::connection('pgsql')->rollback();
              DB::connection('pgsql_encwrite')->rollback();
              DB::connection('pgsql_paywrite')->rollback();
              $return_text = "Something Went Wrong.";
              return redirect('editApplicantDetails')->with('error', $return_text);
            }
            // dump($updateBenDetailsAction);dump($updateBenTable);dump($updateBenPaymentTable);dump($updateBankDup);dump($updateFailedPayment);die;
            if ($updateBenDetailsAction && $updateBenTable && $updateBenPaymentTable && $updateBankDup && $updateFailedPayment) {
              DB::connection('pgsql')->commit();
              DB::connection('pgsql_encwrite')->commit();
              DB::connection('pgsql_paywrite')->commit();
              $return_text = "Beneficiary(".$beneficiaryRow->id.") has successfully verified & forwared for approval.";
              return redirect('no-dup-beneficiaries-list')->with('success', $return_text);
            } else {
              DB::connection('pgsql')->rollback();
              DB::connection('pgsql_encwrite')->rollback();
              DB::connection('pgsql_paywrite')->rollback();
              $return_text = "Beneficiary can't be updated.Something Went Wrong.";
              return redirect('editApplicantDetails')->with('error', $return_text);
            }
          }
        }
      } catch (\Exception $e) {
        dd($e);
        DB::connection('pgsql')->rollback();
        DB::connection('pgsql_encwrite')->rollback();
        DB::connection('pgsql_paywrite')->rollback();
        $return_text = "Something Went Wrong..";
        return redirect('editApplicantDetails')->with('error', $return_text);
      }
    }

    public function aadharDupCheck(Request $request)
    {
      $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
      try {
        $aadhar_no = $request->aadhar_no;
        // $aadhar_no = "828017774867";
        $aadharSameVal = $request->aadharSameVal;
        if ($aadharSameVal == 1) {
          $checkAadharCount = DB::connection('pgsql')->table('pension.beneficiaries')->where(DB::raw("TRIM(aadhar_no)"), $aadhar_no)->whereIn('is_clean', [1,2])->count();
          // dump($aadhar_no);
          // dump($checkAadharCount); die;
          if ($checkAadharCount > 1) {
            return $response = [
              'status' => 1,
              'msg' => 'Aadhar number already exist. Please modify or reject the duplicate one.',
              'type' => 'red',
              'icon' => 'fa fa-warning',
              'title' => 'Warning!!',
            ];
          } else {
            return $response = [
              'status' => 0,
              'msg' => 'No Duplicate Found',
              'type' => 'green',
              'icon' => 'fa fa-success',
              'title' => 'Success!!',
            ];
          }
        }
      } catch (\Exception $e) {
        // dd($e);
        $response = [
          'exception' => true,
          'exception_message' => $e->getMessage(),
          // 'exception_message' =>
          //     'Something went wrong. May be session time out logout and login again.',
        ];
        $statusCode = 400;
      } finally {
        return response()->json($response, $statusCode);
      }
    }

    public function bankDupCheck(Request $request)
    {
      $response = [];
      $statusCode = 200;
      if (!$request->ajax()) {
          $statusCode = 400;
          $response = ['error' => 'Error occured in form submit.'];
          return response()->json($response, $statusCode);
      }
      try {
        $bank_code = $request->bank_code;
        $bank_ifsc = $request->bank_ifsc;
        $bankSameVal = $request->bankSameVal;
        if ($bankSameVal == 2) {
          $checkBankCount = DB::connection('pgsql')->table('pension.beneficiaries')->where(DB::raw("TRIM(bank_code)"), $bank_code)->whereIn('is_clean', [1,2])->count();
          // dump($aadhar_no);
          // dump($checkBankCount); die;
          if ($checkBankCount > 1) {
            return $response = [
              'status' => 1,
              'msg' => 'Bank A/c already exist!! Please modify or reject the duplicate one.',
              'type' => 'red',
              'icon' => 'fa fa-warning',
              'title' => 'Warning!!',
            ];
          } else {
            return $response = [
              'status' => 0,
              'msg' => 'No Duplicate Found',
              'type' => 'green',
              'icon' => 'fa fa-success',
              'title' => 'Success!!',
            ];
          }
        }
      } catch (\Exception $e) {
        // dd($e);
        $response = [
          'exception' => true,
          'exception_message' => $e->getMessage(),
          // 'exception_message' =>
          //     'Something went wrong. May be session time out logout and login again.',
        ];
        $statusCode = 400;
      } finally {
        return response()->json($response, $statusCode);
      }
    }

    public function mobileDupCheck(Request $request)
    {
      $response = [];
      $statusCode = 200;
      if (!$request->ajax()) {
          $statusCode = 400;
          $response = ['error' => 'Error occured in form submit.'];
          return response()->json($response, $statusCode);
      }
      try {
        $mobile_no = $request->mobile_no;
        $mobileSameVal = $request->mobileSameVal;
        if ($mobileSameVal == 3) {
          $checkMobileCount = DB::connection('pgsql')->table('pension.beneficiaries')->where('mobile_no', $mobile_no)->whereIn('is_clean', [1,2])->count();
          // dump($aadhar_no);
          // dump($checkMobileCount); die;
          if ($checkMobileCount > 1) {
            return $response = [
              'status' => 1,
              'msg' => 'Mobile No. already exist!! Please modify or reject the duplicate one.',
              'type' => 'red',
              'icon' => 'fa fa-warning',
              'title' => 'Warning!!',
            ];
          } else {
            return $response = [
              'status' => 0,
              'msg' => 'No Duplicate Found',
              'type' => 'green',
              'icon' => 'fa fa-success',
              'title' => 'Success!!',
            ];
          }
        }
      } catch (\Exception $e) {
        // dd($e);
        $response = [
          'exception' => true,
          'exception_message' => $e->getMessage(),
          // 'exception_message' =>
          //     'Something went wrong. May be session time out logout and login again.',
        ];
        $statusCode = 400;
      } finally {
        return response()->json($response, $statusCode);
      }
    }

    // Approver End
    public function approver_linelisting_index() {
      $user_id = AuthChecker::getUserId();;
      $dutyObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
      $distCode = $dutyObj->district_code;
      $scheme = DB::connection('pgsql_mis')->select(
          'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
              $user_id .
              ' and is_active=1) order by scheme_name'
      );
      if (Auth::user()->designation_id == 'Approver') {
        $levels = [
          2 => 'Rural',
          1 => 'Urban'
        ];
      }
      return view('No-Duplicate-Update.linelisting_approver', ['levels' => $levels, 'schemes'=>$scheme, 'dist_code' => $distCode]);
    }

    public function getNoDupVerifiedList(Request $request)
    {
      $user_id = AuthChecker::getUserId();;
      $dutyObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
      $distCode = $dutyObj->district_code;
      $rural_urban = $request->filter_1;
      $local_body_code = $request->filter_2;
      $scheme_id = $request->scheme_id;
      $filter_type = $request->filter_type;
      $failed_type_id = $request->failed_type_id;
      
      if ($request->ajax()) {
        if (Auth::user()->designation_id == 'Approver' && !empty($scheme_id)) {
          $query = '';
          $query = "SELECT id, ben_fname, ben_mname, ben_lname, block_ulb_name, gp_ward_name, scheme_id, dup_aadhar, dup_mobile, dup_bank, no_aadhar, no_mobile, is_bank_failed, pay_validated FROM pension.beneficiaries WHERE next_level_clean_id = 2 AND is_clean = 1 AND scheme_id = ".$scheme_id."";
          if (!empty($rural_urban)) {
            $query .= " AND rural_urban_id =".$rural_urban."";
          }
          if ($local_body_code) {
            $query .= " AND created_by_local_body_code = ".$local_body_code."";
          }
          if ($filter_type == 1) {
            $query .= " AND dup_aadhar = 1";
          }
          if ($filter_type == 2) {
            $query .= " AND no_aadhar = 1";
          }
          if ($filter_type == 3) {
            $query .= " AND dup_bank = 1";
          }
          if ($filter_type == 4) {
            $query .= " AND dup_mobile = 1";
          }
          if ($filter_type == 5) {
            $query .= " AND no_mobile = 1";
          }
          if ($filter_type == 6) {
            $query .= " AND is_bank_failed = 1";
          }
          // dd($query);
          $data = DB::connection('pgsql_mis')->select($query);
        } else {
          $data = collect([]);
        }
        return datatables()->of($data)
        ->addIndexColumn()
        ->addColumn('name', function($data) {
          $name = $data->ben_fname.' '.$data->ben_mname.' '.$data->ben_lname;
          return $name;
        })
        ->addColumn('view', function ($data) {
            $action = '<button class="btn btn-primary btn-xs ben_view_button" value="' . $data->id . '_' . $data->scheme_id . '"><i class="glyphicon glyphicon-edit"></i>View</button>';
            return $action;
        })
        ->addColumn('check', function ($data) {
            return '<input type="checkbox"  name="chkbx" class="all_checkbox"  onclick="controlCheckBox();" value="' . $data->id . '">';
        })
        ->addColumn('status', function($data){
          $sl = 1;
          // $sl++;
          $status = '';
          if ($data->dup_aadhar == 1) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. Duplicate Aadhar</b></span> <br>';
          }
          if ($data->dup_mobile == 1) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. Duplicate Mobile</b></span> <br>';
          }
          if ($data->dup_bank == 1) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. Duplicate Bank</b></span> <br>';
          }
          if ($data->no_aadhar == 1) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. No Aadhar</b></span> <br>';
          }
          if ($data->no_mobile == 1) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. No Mobile</b></span> <br>';
          }
          if ($data->is_bank_failed == 1) {
            if ($data->pay_validated == 3) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. Payment Failure SBI</b></span>';
            } elseif ($data->pay_validated == 4) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. Payment Failure RBI</b></span>';
            } else {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>'.$sl++.'. Payment Failure IFMS</b></span>';
            }
          }
          // $sl++;
          return $status;
        })
        ->rawColumns(['name', 'view', 'check', 'status'])
        ->make(true);
      }
    }

    public function NoDupModalView(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
          $benid = $request->benid;
          $parts = explode('_', $benid);
          $id = $parts[0];
          $scheme_id = $parts[1];
          
          $query = "SELECT id, ben_fname, ben_mname, ben_lname, TRIM(aadhar_no) AS aadhar_no, mobile_no, TRIM(bank_code) AS bank_code, TRIM(bank_ifsc) AS bank_ifsc, bank_name, branch_name FROM pension.beneficiaries WHERE scheme_id = ".$scheme_id." AND id = ".$id." AND next_level_clean_id = 2 AND is_clean = 2";
          $ben_details = DB::connection('pgsql_mis')->select($query);
          // dd($ben_details);
          if ($ben_details == null) {
            return $response = [
                'status' => 1,
                'msg' => 'Somethimg went wrong.',
                'type' => 'red',
                'icon' => 'fa fa-warning',
                'title' => 'Warning!!',
            ];
            } else {
                $ben_arr = [
                    'ben_name' => $ben_details[0]->ben_fname.' '.$ben_details[0]->ben_mname.' '.$ben_details[0]->ben_lname,
                    'id' => $ben_details[0]->id,
                    'mobile_no' => $ben_details[0]->mobile_no,
                    'aadhar_no' => $ben_details[0]->aadhar_no,
                    'bank_code' => trim($ben_details[0]->bank_code),
                    'bank_ifsc' => trim($ben_details[0]->bank_ifsc),
                    'branch_name' => trim($ben_details[0]->branch_name),
                    'bank_name' => trim($ben_details[0]->bank_name),
                ];
                // dd($ben_arr);
                $response = array_merge($ben_arr, [
                    'status' => 2,
                ]);
            }
        } catch (\Exception $e) {
          // dd($e);
          $response = [
            'exception' => true,
            'exception_message' => $e->getMessage(),
            // 'exception_message' =>
            //     'Something went wrong. May be session time out logout and login again.',
          ];
          $statusCode = 400;
        } finally {
          return response()->json($response, $statusCode);
        }
    }

    public function getDocument(Request $request)
    {
      $return_status = 0;
      $return_msg = '';
      $html = '';
      $scheme_id = $request->scheme_id;
      if (empty($scheme_id)) {
          $return_status = 0;
          $return_msg = 'Scheme Not Valid';
          return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
      }
      if (!ctype_digit($scheme_id)) {
          $return_status = 0;
          $return_msg = 'Scheme Not Valid';
          return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
      }
      $designation_id = Auth::user()->designation_id;
      $distCode=NULL;
      $user_id = AuthChecker::getUserId();;
      $roleArray = $request->session()->get('role');
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
      if ($designation_id == 'Approver' || AuthChecker::VerifierChecker()) {
          $is_active = 1;
      } else {
          $is_active = 0;
      }
      if ($is_active == 0 || empty($distCode)) {
          $return_status = 0;
          $return_msg = 'User Disabled';
          return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
      }
      $doc_type = $request->doc_type;
      // dd($doc_type);
      $application_id = $request->application_id;
      if (empty($doc_type) || !ctype_digit($doc_type)) {
          $return_status = 0;
          $return_msg = 'Document Type Not Valid';
          return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
      }
      if (empty($application_id)) {
          $return_status = 0;
          $return_msg = 'Application Id  Not Valid';
          return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
      }
      $scheme_row = Scheme::where('id', $scheme_id)->first();
      if (!empty($scheme_row->short_code)) {
          $schema = $scheme_row->short_code;
      } else {
          $schema = "pension";
      }
      $docs_uploaded = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('created_by_dist_code',$distCode)->where('document_type', $doc_type)->where('beneficiary_id', $application_id)->select('id', 'beneficiary_id', 'document_type', 'doc_type_name','attched_document','document_extension','document_mime_type')->first();
      // dd($docs_uploaded);
      if (empty($docs_uploaded->attched_document)) {
          $return_status = 0;
          $return_msg = 'No Document Uploaded previuosly.';
          return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
      }
      $file_extension = $docs_uploaded->document_extension;
      $file_content = $docs_uploaded->attched_document;
      $mime_type =$docs_uploaded->document_mime_type;
      // dd($file_extension);

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
          if ((strtoupper($file_extension) == 'PNG' || strtoupper($file_extension) == 'png') || (strtoupper($file_extension) == 'JPG' || strtoupper($file_extension) == 'jpg') || (strtoupper($file_extension) == 'JPEG' || strtoupper($file_extension) == 'jpeg')) {
              $htmlText = '<image id="image" width="100%" height="100%" src="data:image/' . $file_extension . ';base64, ' . $file_content . '">';
              //echo $htmlText;
          } else if (strtoupper($file_extension) == 'PDF' || strtoupper($file_extension) == 'pdf') {
              $htmlText = '<embed type="text/html" width="100%" height="100%" src="data:application/pdf;base64, ' . $file_content . ' ">';
              // echo $htmlText; die;
          }
          $return_status = 1;
          return response()->json(['return_status' => $return_status, 'htmlText' => $htmlText]);
      } catch (\Exception $e) {
          $return_status = 0;
          $return_msg = 'Some error.please try again ......';
          return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
      }
    }

    public function approveNoDupApplicant(Request $request)
    {
      $response = [];
      $statusCode = 200;
      if (!$request->ajax()) {
          $statusCode = 400;
          $response = ['error' => 'Error occured in form submit.'];
          return response()->json($response, $statusCode);
      }
      $is_bulk = $request->is_bulk;
      $applicant_id = $request->applicantId;
      $opreation_type = $request->opreation_type;
      $failed_type = $request->failed_type;
      if ($is_bulk == 0) {
        $single_app_id = $request->single_app_id;
        $parts = explode('_', $single_app_id);
        $id = $parts[0];
        $scheme_id = $parts[1];
        if ($opreation_type == 'A') {
          try {
            $user_id = AuthChecker::getUserId();;
            $ip_address = request()->ip();
            $mappingLevel = $request->session()->get('level');
            $district_code = $request->session()->get('distCode');
            $is_first = $request->session()->get('is_first');
            $is_urban = $request->session()->get('is_urban');
            $body_code = $request->session()->get('bodyCode');
            $role_id = $request->session()->get('role_id');
            $today = date("Y-m-d h:i:s");
            $query = "SELECT id, aadhar_no, mobile_no, next_level_clean_id, is_bank_failed, pay_validated, dup_bank FROM pension.beneficiaries WHERE scheme_id = ".$scheme_id." AND id = ".$id." AND next_level_clean_id = 2 AND is_clean = 1";
            $benDetails = DB::connection('pgsql_mis')->select($query);
            // dd($benDetails);
            if ($benDetails == null) {
              return $response = [
                'status' => 1,
                'msg' => 'No Beneficiary Found.',
                'type' => 'red',
                'icon' => 'fa fa-warning',
                'title' => 'Warning!!',
              ];
            } else {
              $updateBenTable = [];
              $updateBenDetailsTable = [];
              $updateBenPaymentTable = [];
              $updateFailedPaymentTable = [];
              $updateBenTable['next_level_clean_id'] = 1;
              $updateBenTable['dup_bank'] = null;
              $updateBenTable['dup_mobile'] = null;
              $updateBenTable['dup_aadhar'] = null;
              $updateBenTable['no_aadhar'] = null;
              $updateBenTable['no_mobile'] = null;
              if ($benDetails[0]->is_bank_failed == 1 && ($benDetails[0]->pay_validated == 3 || $benDetails[0]->pay_validated == 4 || $benDetails[0]->pay_validated == 5)) {
                $updateBenTable['pay_validated'] = null;
                $updateBenTable['is_bank_failed'] = null;
              }
              if ($benDetails[0]->dup_bank == 1 || $benDetails[0]->is_bank_failed == 1) {
                $updateBenPaymentTable['dup_bank'] = 0;
                $updateBenPaymentTable['ben_status'] = 1;
                $updateBenPaymentTable['acc_validated'] = 0;
              }
              $updateFailedPaymentTable['edited_status'] = 2;

              $updateBenDetailsTable['updated_at'] = date("Y-m-d h:i:s");
              $updateBenDetailsTable['remarks'] = $request->accept_reject_comments;
              $updateBenDetailsTable['is_clean'] = 1;

              DB::connection('pgsql')->beginTransaction();
              DB::connection('pgsql_paywrite')->beginTransaction();
              $is_ben_update = DB::connection('pgsql')->table('pension.beneficiaries')->where('scheme_id', $scheme_id)->where('id', $id)->where('is_clean', 1)->where('next_level_clean_id', 2)->update($updateBenTable);
              $is_update_ben_details = DB::connection('pgsql')->table('public.update_ben_details')->where('original_application_id', $id)->where('scheme_id', $scheme_id)->where('update_code', 500)->where('is_clean', 2)->update($updateBenDetailsTable);
              if ($benDetails[0]->dup_bank == 1 || $benDetails[0]->is_bank_failed == 1) {
                $is_update_ben_payment = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $id)->where('scheme_id', $scheme_id)->update($updateBenPaymentTable);
              } else {
                $is_update_ben_payment = 1;
              }
              if ($benDetails[0]->is_bank_failed == 1) {
                $is_update_failed_payment = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')->where('ben_id', $id)->where('scheme_id', $scheme_id)->update($updateFailedPaymentTable);
              } else {
                $is_update_failed_payment = 1;
              }
              if (($benDetails[0]->is_bank_failed == 1)) {
                $is_final_update = DB::connection('pgsql_paywrite')->select("Select payment.failed_update_bank(in_ben_id => ARRAY[". $id."], in_scheme_id => ".$scheme_id.", in_failed_type_id => ".$failed_type.")");
              } else {
                $is_final_update = 1;
              }
              // if ($is_ben_update && $is_update_ben_details) {
              //   $is_final_update = 1;
              // }       
              if ($is_final_update) {
                DB::connection('pgsql')->commit();
                DB::connection('pgsql_paywrite')->commit();
                return $response = [
                  'status' => 1,
                  'msg' => 'Beneficiary Updated Successfully.',
                  'type' => 'green',
                  'icon' => 'fa fa-success',
                  'title' => 'Success',
                ];
              } else {
                  DB::connection('pgsql')->rollback();
                  DB::connection('pgsql_paywrite')->rollback();
                  return $response = [
                    'status' => 1,
                    'msg' => 'Somethimg went wrong..',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                  ];
              } 
            }
          } catch (\Exception $e) {
            dd($e);
            DB::connection('pgsql')->rollback();
            DB::connection('pgsql_paywrite')->rollback();
            $response = [
              'exception' => true,
              'exception_message' => $e->getMessage(),
            ];
            $statusCode = 400;
          } finally {
            // dd($response);
            return response()->json($response, $statusCode);
          }
        } elseif ($opreation_type == 'T') {
          try {
            $user_id = AuthChecker::getUserId();;
            $ip_address = request()->ip();
            $mappingLevel = $request->session()->get('level');
            $district_code = $request->session()->get('distCode');
            $is_first = $request->session()->get('is_first');
            $is_urban = $request->session()->get('is_urban');
            $body_code = $request->session()->get('bodyCode');
            $role_id = $request->session()->get('role_id');
            $today = date("Y-m-d h:i:s");
            $query = "SELECT id, aadhar_no, mobile_no, next_level_clean_id FROM pension.beneficiaries WHERE scheme_id = ".$scheme_id." AND id = ".$id." AND next_level_clean_id = 2 AND is_clean = 1";
            $benDetails = DB::connection('pgsql_mis')->select($query);
            if ($benDetails == null) {
              return $response = [
                'status' => 1,
                'msg' => 'No Beneficiary Found.',
                'type' => 'red',
                'icon' => 'fa fa-warning',
                'title' => 'Warning!!',
              ];
            } else {
              $updateBenTable = [];
              $updateBenDetailsTable = [];
              $updateBenTable['next_level_clean_id'] = null;
              $updateBenDetailsTable['remarks'] = $request->accept_reject_comments;

              DB::connection('pgsql')->beginTransaction();
              $is_ben_update = DB::connection('pgsql')->table('pension.beneficiaries')->where('scheme_id', $scheme_id)->where('id', $id)->where('is_clean', 1)->where('next_level_clean_id', 2)->update($updateBenTable);
              $is_update_ben_details = DB::connection('pgsql')->table('public.update_ben_details')->where('original_application_id', $id)->where('scheme_id', $scheme_id)->where('update_code', 500)->where('is_clean', 2)->update($updateBenDetailsTable);
              if ($is_ben_update) {
                if ($is_update_ben_details) {
                  DB::connection('pgsql')->commit();
                  return $response = [
                    'status' => 1,
                    'msg' => 'Beneficiary Reverted Successfully.',
                    'type' => 'green',
                    'icon' => 'fa fa-success',
                    'title' => 'Success',
                  ];
                } else {
                    DB::connection('pgsql')->rollback();
                    return $response = [
                      'status' => 1,
                      'msg' => 'Somethimg went wrong..',
                      'type' => 'red',
                      'icon' => 'fa fa-warning',
                      'title' => 'Warning!!',
                    ];
                  } 
              }
            }
          } catch (\Exception $e) {
            DB::connection('pgsql')->rollback();
            $response = [
              'exception' => true,
              'exception_message' => $e->getMessage(),
            ];
            $statusCode = 400;
          } finally {
            return response()->json($response, $statusCode);
          }
        }
      }
      if ($is_bulk == 1) {
        if ($opreation_type == 'A') {
          $bulk_id_arr = explode(',', $applicant_id);
          $scheme_id = $request->scheme_id;

          DB::beginTransaction();
          try {
            $count = 0;
            $i = 0;
            foreach ($bulk_id_arr as $key => $value) {
              $count++;
              $ip_address = request()->ip();
              $today = date("Y-m-d h:i:s");
              $query = '';
              $query = "SELECT id, aadhar_no, mobile_no, next_level_clean_id, is_bank_failed, pay_validated FROM pension.beneficiaries WHERE scheme_id = ".$scheme_id." AND id = ".$value." AND next_level_clean_id = 2 AND is_clean = 1";
              // dd($query);
              $benDetails = DB::connection('pgsql_mis')->select($query);
              if ($benDetails == null) {
                return $response = [
                    'status' => 1,
                    'msg' => 'No Beneficiary Found.',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
              } else {
                $updateBenTable = [];
                $updateBenDetailsTable = [];
                $updateBenPaymentTable = [];
                $updateFailedPaymentTable = [];

                $updateBenTable['next_level_clean_id'] = 1;
                $updateBenTable['is_clean'] = 1;
                $updateBenTable['dup_bank'] = null;
                $updateBenTable['dup_mobile'] = null;
                $updateBenTable['dup_aadhar'] = null;
                $updateBenTable['no_aadhar'] = null;
                $updateBenTable['no_mobile'] = null;
                if ($benDetails[0]->is_bank_failed == 1 && ($benDetails[0]->pay_validated == 3 || $benDetails[0]->pay_validated == 4 || $benDetails[0]->pay_validated == 5)) {
                  $updateBenTable['pay_validated'] = null;
                  $updateBenTable['is_bank_failed'] = null;
                }

                $updateBenPaymentTable['dup_bank'] = 0;
                $updateBenPaymentTable['ben_status'] = 1;
                $updateBenPaymentTable['acc_validated'] = 0;

                $updateFailedPaymentTable['edited_status'] = 0;

                $updateBenDetailsTable['updated_at'] = date("Y-m-d h:i:s");
                $updateBenDetailsTable['remarks'] = $request->accept_reject_comments;
                $updateBenDetailsTable['is_clean'] = 1;

                // DB::connection('pgsql')->beginTransaction();
                $is_ben_update = DB::connection('pgsql')->table('pension.beneficiaries')->where('scheme_id', $scheme_id)->where('id', $value)->where('is_clean', 1)->where('next_level_clean_id', 2)->update($updateBenTable);
                $is_update_ben_details = DB::connection('pgsql')->table('public.update_ben_details')->where('original_application_id', $value)->where('scheme_id', $scheme_id)->where('update_code', 500)->where('is_clean', 2)->update($updateBenDetailsTable);
                $is_update_ben_payment = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $value)->where('scheme_id', $scheme_id)->update($updateBenPaymentTable);
                $is_update_failed_payment = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')->where('ben_id', $value)->where('scheme_id', $scheme_id)->update($updateFailedPaymentTable);
                // dump($is_ben_update); die; //dump($is_update_ben_details); die;
                if ($benDetails[0]->is_bank_failed == 1) {
                  $is_final_update = DB::connection('pgsql_paywrite')->select("Select payment.failed_update_bank(in_ben_id => ARRAY[". $value."], in_scheme_id => ".$scheme_id.", in_failed_type_id => ".$failed_type.")");
                } else {
                  $is_final_update = 1;
                }
                if ($is_final_update) {
                  $i++;
                }
              }
            }
            if ($i == $count) {
              DB::commit();
              $response = [
                'status' => 1,
                'msg' => 'Beneficiaries Approved Successfully',
                'type' => 'green',
                'icon' => 'fa fa-check',
                'title' => 'Success',
              ];
            } else {
              DB::rollback();
              $response = [
                  'exception' => true,
                  // 'exception_message' => $e->getMessage(),
                  'exception_message' =>
                      'Something went wrong. May be session time out logout and login again.',
              ];
            }
          } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            DB::connection('pgsql')->rollback();
              $response = [
                  'exception' => true,
                  // 'exception_message' => $e->getMessage(),
                  'exception_message' =>
                      'Something went wrong. May be session time out logout and login again.',
              ];
              $statusCode = 400;
          } finally {
            return response()->json($response, $statusCode);
        }
        }
      }
    }
}
