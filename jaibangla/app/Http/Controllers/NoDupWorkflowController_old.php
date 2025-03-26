<?php

namespace App\Http\Controllers;

use App\AcceptRejectInfo;
use App\BenFailedPaymentDetails;
use App\BenPaymentDetails;
use Illuminate\Http\Request;
use App\Scheme;
use App\District;
use App\UrbanBody;
use App\GP;
use App\Configduty;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use App\BankDetails;
use App\BenDupBankCodePayemntDetails;
use App\BenEntry;
use App\Helpers\AuthChecker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\DataInsert;
use App\Helpers\DupCheck;
use App\SchemeGenSetting;
use App\BenRejectRequest;

class NoDupWorkflowController extends Controller
{
  // private $aadhar_doc_type_id;
  // private $bank_doc_type_id;

  private $aadhar_doc; // For Aadhar Document
  private $bank_doc; // For Bank Document
  private $disability_doc; // For Disability Document
  private $caste_doc; // For caste Document
  private $epic_doc; // For EPIC Document
  private $ration_doc; // For Ration Document
  private $husband_doc; // For Husband Death Certificate Document
  public function __construct()
  {
    set_time_limit(120);
    $this->middleware('auth');
    date_default_timezone_set('Asia/Kolkata');
    $this->aadhar_doc = 6;
    $this->bank_doc = 10;
    $this->disability_doc = 4;
    $this->caste_doc = 3;
    $this->epic_doc = 7;
    $this->ration_doc = 5;
    $this->husband_doc = 105;
  }
  public function index(Request $request)
  {
    // dd($request->all()); 
    // dd($type);
    $type = $request->has('type') ? (int) $request->type : null;
    // var_dump($type);
    // $designation_id = Auth::user()->designation_id;
    if (AuthChecker::VerifierPermission()) {
      $is_active = 1;
    } else {
      $is_active = 0;
    }
    if ($is_active == 0) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    $user_id = AuthChecker::getUserId();
    $mapObj = DB::table('public.duty_assignement')->where('user_id', $user_id)->where('is_active', 1)->first();
    $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . " )"));
    $incomplete_types = DB::table('public.m_incomplete_type')->where('is_active', true)->get();
    // dd($incpmplete_types);
    if (AuthChecker::VerifierPermission()) {
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
            'mapLevel' => $mapObj->mapping_level,
            'muncList' => $muncList,
            'gpList' => $gpList,
            'rural_urban_fk' => $mapObj->is_urban,
            'municipality_visible' => $municipality_visible,
            'block_munc_corp_code_fk' => $urban_body_code,
            'dist_code' => $mapObj->district_code,
            'gp_ward_visible' => $gp_ward_visible,
            'incomplete_types' => $incomplete_types,
            'type_id' => $type
          ]);
        } else {
          $taluka_code = $mapObj->taluka_code;
          $gpList = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
          return view('No-Duplicate-Update/verifier_index', [
            'schemes' => $scheme,
            'mapLevel' => $mapObj->mapping_level,
            'muncList' => $muncList,
            'gpList' => $gpList,
            'rural_urban_fk' => $mapObj->is_urban,
            'block_munc_corp_code_fk' => $taluka_code,
            'municipality_visible' => $municipality_visible,
            'dist_code' => $mapObj->district_code,
            'gp_ward_visible' => $gp_ward_visible,
            'incomplete_types' => $incomplete_types,
            'type_id' => $type
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
      // dd($request->all());
      $scheme_id = $request->scheme_id;
      $filter_type = $request->filter_type;
      $pay_validated = $request->pay_validated;
      // dump($scheme_id); dump($filter_type);dump($pay_validated); die;
      if (empty($scheme_id) || !ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }

      $user_id = AuthChecker::getUserId();
      // dd($user_id);
      $roleArray = Configduty::where('user_id', Auth::user()->id)
        ->where('is_active', 1)
        ->get()
        ->toArray();

      $district_code = null;
      $urban_body_code = null;
      $mapping_level = null;
      $role_id = null;

      foreach ($roleArray as $roleObj) {
        if ($roleObj['scheme_id'] == $scheme_id) {
          $is_active = 1;
          $is_urban = $roleObj['is_urban'];
          $district_code = $roleObj['district_code'];
          $mapping_level = $roleObj['mapping_level'];
          $role_id = $roleObj['id'];
          $urban_body_code = $is_urban == 1 ? $roleObj['urban_body_code'] : $roleObj['taluka_code'];
          break;
        }
      }

      if (AuthChecker::VerifierPermission()) {
        $is_active = 1;
      } else {
        $is_active = 0;
      }
      // dd($is_active);
      if ($is_active == 0 || (empty($district_code) && empty($urban_body_code)) || empty($mapping_level)) {
        return [
          'status' => 0,
          'msg' => ["User Disabled."],
          'type' => 'red',
          'icon' => 'fa fa-warning',
          'title' => 'Warning!!',
        ];
      }

      if ($mapping_level == 'Block' || $mapping_level == 'Subdiv') {
        $query = '';
        $query = "SELECT id,is_incomplete,dup_aadhar,no_aadhar,dup_bank,dup_mobile,no_mobile,no_ration_card,no_epic_voter, is_bank_failed,pay_validated,created_by_local_body_code, ben_fname, ben_mname, ben_lname,aadhar_no, scheme_id,block_ulb_name,gp_ward_name,bank_code,bank_ifsc  FROM pension.beneficiaries WHERE scheme_id = " . $scheme_id . " AND created_by_local_body_code = " . $urban_body_code . " AND created_by_dist_code = " . $district_code . "  AND next_level_clean_id IS NULL AND is_rejected = 0";

        if ($filter_type == 0) {
          $query .= "AND is_incomplete = 1 ";
        }
        if ($filter_type == 1) {
          $query .= "AND dup_aadhar = 1 ";
        }
        if ($filter_type == 2) {
          $query .= "AND no_aadhar = 1 ";
        }
        if ($filter_type == 3) {
          $query .= "AND dup_bank = 1 ";
        }
        if ($filter_type == 4) {
          $query .= "AND dup_mobile = 1 ";
        }
        if ($filter_type == 5) {
          $query .= "AND no_mobile = 1 ";
        }
        if ($filter_type == 6) {
          $query .= "AND no_ration_card = 1 ";
        }
        if ($filter_type == 8) {
          $query .= "AND no_epic_voter = 1 ";
        }
        if ($filter_type == 11) {
          $query .= "AND is_bank_failed = 2 ";
        }
        if ($filter_type == 12) {
          $query .= "AND is_bank_failed = 3 ";
        }
        // dd($query);
        if ($filter_type == 10 || !empty($pay_validated)) {
          // dd('IF');  
          // $query .= "AND id IN (";
          if ($pay_validated == 3) {
            // $subQuery = DB::connection('pgsql_paywrite')->select("SELECT ben_id FROM payment.ben_payment_details WHERE pay_validated = 3");
            // $subQueryIds = array_column($subQuery, 'ben_id');
            // if (!empty($subQueryIds)) {
            //   $query .= implode(',', $subQueryIds);
            // } else {
            //   $query .= "NULL";
            $query .= " AND is_bank_failed = 1 AND pay_validated = 3";
          }
          // }
          if ($pay_validated == 4) {
            // $subQuery = DB::connection('pgsql_paywrite')->select("SELECT ben_id FROM payment.ben_payment_details WHERE pay_validated = 4");
            // $subQueryIds = array_column($subQuery, 'ben_id');
            // if (!empty($subQueryIds)) {
            //   $query .= implode(',', $subQueryIds);
            // } else {
            //   $query .= "NULL";
            // }
            $query .= " AND is_bank_failed = 1  AND pay_validated = 4";
          }
          if ($pay_validated == 5) {
            // $subQuery = DB::connection('pgsql_paywrite')->select("SELECT ben_id FROM payment.ben_payment_details WHERE pay_validated = 5");
            // $subQueryIds = array_column($subQuery, 'ben_id');
            // if (!empty($subQueryIds)) {
            //   $query .= implode(',', $subQueryIds);
            // } else {
            //   $query .= "NULL";
            // }
            $query .= " AND is_bank_failed = 1  AND pay_validated = 5";
          }
          // $query .= ") ";
        }
        if (!empty($request->blk_ulb_code)) {
          $query .= "AND created_by_local_body_code = " . $request->blk_ulb_code . " ";
        }

        $query .= " ORDER BY id";

        // dd($query);
        $data = DB::connection('pgsql')->select($query);
        // dd($data);
        // $data = collect($data)->map(function ($item) use ($scheme_id) {
        //   $pay_validation = DB::connection('pgsql_paywrite')
        //     ->table('payment.ben_payment_details')
        //     ->where('ben_id', $item->id)
        //     ->where('scheme_id', $scheme_id)
        //     ->select('pay_validated')
        //     ->first();

        //   $item->pay_validated = $pay_validation->pay_validated ?? null;
        //   return $item;
        // });

        return datatables()->of($data)
          ->addColumn('view', function ($data) {
            return '<a href="' . route('editApplicantDetails', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit </a>';
          })
          ->addColumn('name', function ($data) {
            return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
          })
          ->addColumn('aadhar_no', function ($data) {
            $aadhar = trim($data->aadhar_no);
            return strlen($aadhar) >= 12 ? '********' . substr($aadhar, 8, 4) : $aadhar;
          })
          ->addColumn('status', function ($data) {
            $sl = 1;
            $status = '';
            if ($data->dup_aadhar == 1) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Duplicate Aadhar</b></span> <br>';
            }
            if ($data->dup_mobile == 1) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Duplicate Mobile</b></span> <br>';
            }
            if ($data->dup_bank == 1) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Duplicate Bank</b></span> <br>';
            }
            if ($data->no_aadhar == 1) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. No Aadhar</b></span> <br>';
            }
            if ($data->no_mobile == 1) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. No Mobile</b></span> <br>';
            }
            if ($data->is_incomplete == 1) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Incomplete Details</b></span> <br>';
            }
            // if ($data->is_bank_failed == 1) {
            //   $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment / Transaction Failure </b></span> <br>';
            // }
            if ($data->is_bank_failed == 1) {
              if ($data->pay_validated == 3) {
                $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment Failure SBI</b></span>';
              } elseif ($data->pay_validated == 4) {
                $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment Failure RBI</b></span>';
              } elseif ($data->pay_validated == 5) {
                $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment Failure IFMS</b></span>';
              } else {
                $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment Failure </b></span>';
              }
            }

            if ($data->is_bank_failed == 2) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Name Validation Failure</b></span> <br>';
            }
            if ($data->is_bank_failed == 3) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. A/c Validation Failure</b></span> <br>';
            }
            return $status;
          })
          ->rawColumns(['view', 'name', 'aadhar_no', 'status'])
          ->make(true);
      }
    }
  }


  public function editApplicantDetails(Request $request)
  {
    try {
      $is_verifier = AuthChecker::VerifierPermission();
      $user_id = AuthChecker::getUserId();
      // $doc_type_id_arr = array($this->aadhar_doc_type_id, $this->bank_doc_type_id);
      // $supporting = DocumentType::select('id', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->whereIn("id", $doc_type_id_arr)->get();
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
      if (AuthChecker::VerifierPermission()) {
        $query = DB::connection('pgsql')->table('pension.beneficiaries')
          ->where('created_by_dist_code', $district_code)
          ->where('id', $ben_id)
          ->where('scheme_id', $scheme_id);
        // ->where('is_clean', 2);
      } else {
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
      // dd($row);
      $getAadharDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 6)->count();
      $getBankDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 10)->count();
      $getDisabilityDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 4)->count();
      $getHusbandDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 105)->count();
      $getCasteDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 3)->count();
      $getEpicDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 7)->count();
      $getRationDoc = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', 5)->count();
      // dd($getBankDoc);

      $PaymentErrorType = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->first();
      // var_dump($PaymentErrorType);
      $enable_validation = array();
      $enableArr = [];
      if ($row->dup_bank == 1) {
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
      $op_type = 1;
      $type = 1;

      $manabilk_visible = 0;
      $wcd_wp_visible = 0;
      $sc_visible = 0;
      $st_visible = 0;
      $farmer_visible = 0;
      $wcd_wp_visible = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where('scheme_id', 10)->where('is_incomplete', 1)->count();
      $manabilk_visible = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where('scheme_id', 2)->where('is_incomplete', 1)->count();
      $sc_visible = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where('scheme_id', 3)->where('is_incomplete', 1)->count();
      $st_visible = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where('scheme_id', 1)->where('is_incomplete', 1)->count();
      // $farmer_visible = DB::connection('pgsql')->table('pension.beneficiaires')->where('id', $ben_id)->where('scheme_id', 13)->where('process_code', 1)->count();

      $field_arrays = array();
      $field_arrays_payment = array();

      $query = "SELECT id, scheme_id, is_incomplete, dup_aadhar, no_aadhar, dup_bank, dup_mobile, no_mobile, is_bank_failed, no_ration_card, no_epic_voter
      FROM pension.beneficiaries 
      WHERE id = ? AND scheme_id = ?";

      // Execute the query with parameterized bindings
      $data = DB::connection('pgsql')->select($query, [$row->id, $row->scheme_id]);
      $data = $data[0];
      if ($data->dup_aadhar == 1) {
        array_push($field_arrays, 'Duplicate Aadhar');
      }
      if ($data->no_aadhar == 1) {
        array_push($field_arrays, 'No Aadhar');
      }
      if ($data->dup_bank == 1) {
        array_push($field_arrays, 'Duplicate Bank Account Number');
      }
      if ($data->dup_mobile == 1) {
        array_push($field_arrays, 'Duplicate Mobile Number');
      }
      if ($data->no_mobile == 1) {
        array_push($field_arrays, 'No Mobile Number');
      }
      if ($data->is_bank_failed == 1) {
        array_push($field_arrays, 'Bank Transaction Failed');
      }
      if ($data->is_bank_failed == 2) {
        array_push($field_arrays, 'Name Validation Failed');
      }
      if ($data->is_bank_failed == 3) {
        array_push($field_arrays, 'A/c Validation Failed');
      }
      if ($data->no_ration_card == 1) {
        array_push($field_arrays, 'No Ration Card');
      }
      if ($data->no_epic_voter == 1) {
        array_push($field_arrays, 'No Epic Voter Card');
      }

      if ($data->is_incomplete == 1 && ($data->scheme_id == 1 || $data->scheme_id == 3)) {
        // Adding fields to the array
        array_push($field_arrays, 'Caste Category');
        array_push($field_arrays, 'Caste Certificate Number');
        array_push($field_arrays, 'Caste Certificate Document');
      }
      if ($data->is_incomplete == 1 && ($data->scheme_id == 2)) {
        array_push($field_arrays, 'Disability Type');
        array_push($field_arrays, 'Percentage of Disability');
        array_push($field_arrays, 'Authority Name');
        array_push($field_arrays, 'Authority Designation');
        array_push($field_arrays, 'Disability Certificate from Appropriate Authority');
      }
      if ($data->is_incomplete == 1 && ($data->scheme_id == 11)) {
        array_push($field_arrays, 'Husband Death Document');
        array_push($field_arrays, 'Husband Name');
      }
      $failed_type_id = 0;
      // dd($row);
      // if($row->is_bank_failed == 2 || $row->is_bank_failed == 3)
      // {
      if ($PaymentErrorType) {
        $failed_type_id = $PaymentErrorType->pay_validated;
      } else {
        $failed_type_id = null;
      }

      $invalid_status = '';

      if ($row->is_bank_failed == 1 || $row->is_bank_failed == 2 || $row->is_bank_failed == 3) {
        $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw('payment.failed_payment_details as f'))
          ->join('payment.ben_payment_details as ben', 'f.ben_id', '=', 'ben.ben_id')
          ->where('f.failed_type', $failed_type_id)->where('f.edited_status', 0)->where('ben.is_eligible', true)
          ->where('ben.ben_status', 1)->where('f.scheme_id', $scheme_id)
          ->where('f.ben_id', $row->id)
          ->first();
        // dd($ben_details);
        // dd($bank_details);

        // dd($ben_details->remarks);
        if ($failed_type_id == 4 || $failed_type_id == 5) {
          $invalid_status = $ben_details->remarks;
        } elseif ($failed_type_id == 3) {
          $remarks = DB::connection('pgsql_paywrite')->table('sbi.credit_transaction_code')->where(
            'code',
            trim($ben_details->status_code)
          )->get(['description'])
            ->first();
          $invalid_status = $remarks->description;
        }
      }
      $av_name_response = null;

      if ($row->is_bank_failed == 2) {
        $response = DB::connection('pgsql_paywrite')
          ->table('payment.failed_payment_details')
          ->where('ben_id', $ben_id)
          ->where('edited_status', 0)
          ->select('av_name_response')
          ->first();

        // Check if response exists and assign the value
        if ($response) {
          $av_name_response = $response->av_name_response;
        }
      }

      $schemeSetting = SchemeGenSetting::where('scheme_id', $scheme_id)->first();
      // dd($av_name_reposnse);
      // }
      // else {
      //   $invalid_status = null;
      // }
      $required_fields = DB::table('public.m_fields')
        ->where('is_active', 1)
        ->pluck('id')
        ->toArray();

      $name_opts = DB::table('public.m_scheme_gen_setting')
        ->where('scheme_id', $scheme_id)
        ->pluck('name_valid_opt');
      $name_opts = $name_opts->first();
      $array = explode(',', trim($name_opts, '{}'));
      $array = array_map('intval', $array);
      // dd($array); 
      $name_options = DB::table('public.m_name_valid_option')->wherein('id', $array)->get();
      // dd($name_options);

      $schemeSetting = SchemeGenSetting::where('scheme_id', $scheme_id)->first();
      $canBankupdate = 0;
      if ($schemeSetting->allow_bank_failed_update == 1 || $schemeSetting->allow_bank_name_update == 1 || $schemeSetting->allow_bank_ac_update == 1) {
        $canBankupdate = 1;
      }


      return view(
        'No-Duplicate-Update.ben_details_update_verifier',
        [
          'scheme_id' => $scheme_id,
          'row' => $row,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'district_code' => $district_code,
          'enable_validation' => $enable_validation,
          'getAadharDoc' => $getAadharDoc,
          'getBankDoc' => $getBankDoc,
          'PaymentErrorType' => $PaymentErrorType,
          'getHusbandDoc' => $getHusbandDoc,
          'getCasteDoc' => $getCasteDoc,
          'getEpicDoc' => $getEpicDoc,
          'getRationDoc' => $getRationDoc,
          'invalid_status' => $invalid_status,
          'name_options' => $name_options,
          'av_name_response' => $av_name_response,
          'canBankupdate' => $canBankupdate,
          // 'fetch_list_st' => $fetch_list_st,
          // 'fetch_list_sc' => $fetch_list_sc,
          // 'fetch_list_oap' => $fetch_list_oap,
          // 'fetch_list_oap_st' => $fetch_list_oap_st,
          // 'fetch_list' => $fetch_list,
          // 'fetch_lb_status' => $fetch_lb_status,
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
          'is_verifier' => $is_verifier,
          'type' => $type,
          'op_type' => $op_type,
          'getDisabilityDoc' => $getDisabilityDoc,
          'field_arrays' => $field_arrays,
          'field_arrays_payment' => $field_arrays_payment,
          'required_fields' => $required_fields,
          'schemeSetting' => $schemeSetting

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
    $auth = AuthChecker::VerifierPermission();
    if ($auth) {
      try {
        $user_id = AuthChecker::getUserId();
        $scheme_id = $request->scheme_id;
        $ben_id = $request->id;
        $old_bank_ifsc = $request->old_bank_ifsc;


        // dd($request->new_bank_code);
        $isSame = !empty($request->new_bank_code) && ($request->new_bank_code != $request->old_bank_code) ? 0 : 1;
        $isSameMobile = !empty($request->new_mobile_no) && ($request->new_mobile_no != $request->old_mobile_no) ? 0 : 1;


        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
        if (empty($duty_obj)) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        $district_code = $duty_obj->district_code;
        $mapping_level = $duty_obj->mapping_level;
        if ($duty_obj->mapping_level == "Block") {
          $urban_body_code = $duty_obj->taluka_code;
        }
        if ($duty_obj->mapping_level == "Subdiv") {
          $urban_body_code = $duty_obj->urban_body_code;
        }

        $bankCodeRow = null;
        $row = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where(
          'scheme_id',
          $scheme_id
        )->first();
        $paymentErrorType = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where(
          'ben_id',
          $ben_id
        )->where('scheme_id', $scheme_id)->first();
        $bankCodeRow = DB::connection('pgsql')->table('ifsc.bank_details')->where('ifsc', $old_bank_ifsc)->first();
        $bankDupRow = DB::connection('pgsql')->table('pension.ben_payment_details_bank_code_dup')->where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', -97)->count();


        if (!empty($request->bank_ifsc_code)) {
          // if ($ben_id == 1030426) {
          //   return response()->json(['message' => 'ok']);
          // }

          $bank_ifsc_db = BankDetails::where('ifsc', $request->bank_ifsc_code)
            ->where('is_active', 1)
            ->first();

          if (!$bank_ifsc_db) {
            $return_text = "Bank Details not Available in Jai Bangla Portal";
            return redirect()->route('editApplicantDetails', [
              'id' => $ben_id,
              'scheme_id' => $scheme_id
            ])->with('error', $return_text);
          }
        }



        // dd($bankDupRow);
        $rules = [];
        $attributes = [];

        if ($row->dup_bank == 1 || in_array($row->is_bank_failed, [1, 3])) {
          $bankRules = [
            'old_bank_code' => 'required|max:20',
            'old_bank_ifsc' => 'required|max:11',
            'old_bank_name' => 'required',
            'old_bank_branch' => 'required',
            'new_bank_branch' => 'required',
            'new_bank_name' => 'required',
            'new_bank_code' => 'required|max:20',
            'bank_ifsc_code' => 'required|max:11',
          ];
          $bankAttributes = [
            'old_bank_code' => 'Old Bank Code',
            'old_bank_ifsc' => 'Old Bank IFSC',
            // 'old_bank_name' => 'Old Bank Name',
            // 'old_bank_branch' => 'Old Bank Branch',
            'new_bank_branch' => 'New Bank Branch',
            'new_bank_name' => 'New Bank Name',
            'new_bank_code' => 'New Bank Code',
            'bank_ifsc_code' => 'Bank IFSC Code',
          ];
          $rules = array_merge($rules, $bankRules);
          $attributes = array_merge($attributes, $bankAttributes);
        }

        if (in_array($row->is_bank_failed, [2])) {
          $bankRules = [
            'old_bank_code' => 'required|max:20',
            'old_bank_ifsc' => 'required|max:11',
            'process_type' => 'required',
            'old_bank_name' => 'required',
            'old_bank_branch' => 'required',
            // 'new_bank_branch' => 'required',
            // 'new_bank_name' => 'required',
            // 'new_bank_code' => 'required|max:20',
            // 'bank_ifsc_code' => 'required|max:11',
          ];
          $bankAttributes = [
            'old_bank_code' => 'Old Bank Code',
            'old_bank_ifsc' => 'Old Bank IFSC',
            'process_type' => 'Process Type',
            'old_bank_name' => 'Old Bank Name',
            'old_bank_branch' => 'Old Bank Branch',
            // 'new_bank_branch' => 'New Bank Branch',
            // 'new_bank_name' => 'New Bank Name',
            // 'new_bank_code' => 'New Bank Code',
            // 'bank_ifsc_code' => 'Bank IFSC Code',
          ];
          $rules = array_merge($rules, $bankRules);
          $attributes = array_merge($attributes, $bankAttributes);
        }



        if ($row->no_aadhar == 1) {
          $aadharRules = [
            'new_aadhar_no' => 'required|digits:12',
            'old_aadhar' => 'nullable|digits:12',
            'new_aadhar_doc' => 'required',
          ];
          $aadharAttributes = [
            'old_aadhar' => 'Old Aadhar Number',
            'new_aadhar_doc' => 'Aadhar Document',
            'new_aadhar_no' => 'New Aadhar Number',
          ];
          $rules = array_merge($rules, $aadharRules);
          $attributes = array_merge($attributes, $aadharAttributes);
        } elseif ($row->dup_aadhar == 1) {
          $aadharRules = [
            'new_aadhar_no' => 'required|digits:12',
          ];
          $aadharAttributes = [
            'new_aadhar_no' => 'New Aadhar Number',
          ];
          $rules = array_merge($rules, $aadharRules);
          $attributes = array_merge($attributes, $aadharAttributes);
        }

        if ($row->dup_mobile == 1 || in_array($row->is_bank_failed, [1])) {
          $mobileRules = [
            'old_mobile_no' => 'required',
            'new_mobile_no' => 'required|digits:10',
          ];
          $mobileAttributes = [
            'old_mobile_no' => 'Old Mobile Number',
            'new_mobile_no' => 'New Mobile Number',
          ];
          $rules = array_merge($rules, $mobileRules);
          $attributes = array_merge($attributes, $mobileAttributes);
        } elseif ($row->no_mobile == 1) {
          $mobileRules = [
            'new_mobile_no' => 'required|digits:10',
          ];
          $mobileAttributes = [
            'new_mobile_no' => 'New Mobile Number',
          ];
          $rules = array_merge($rules, $mobileRules);
          $attributes = array_merge($attributes, $mobileAttributes);
        }

        if ($row->is_incomplete == 1) {
          if (in_array($scheme_id, [1, 3])) {
            $filedRules = [
              'new_caste_category' => 'required',
              'new_caste_certificate_no' => 'required',
            ];
            $filedAttributes = [
              'new_caste_category' => 'Caste Category',
              'new_caste_certificate_no' => 'Caste Certificate Number',
            ];
            $rules = array_merge($rules, $filedRules);
            $attributes = array_merge($attributes, $filedAttributes);
          }
          if (in_array($scheme_id, [2])) {
            $filedRules = [
              'new_disablity_type' => 'required',
              'new_disablity_type_percentage' => 'required',
              'new_disablity_type_authority' => 'required',
              'new_disability_designation' => 'required',
            ];
            $filedAttributes = [
              'new_disablity_type' => 'Disability Type',
              'new_disablity_type_percentage' => 'Disability Percentage',
              'new_disablity_type_authority' => 'Disability Authority',
              'new_disability_designation' => 'Disability Designation',
            ];
            $rules = array_merge($rules, $filedRules);
            $attributes = array_merge($attributes, $filedAttributes);
          }

          if (in_array($scheme_id, [11])) {
            $filedRules = [
              'new_husband_first_name' => 'required',
              'new_husband_last_name' => 'required',
            ];
            $filedAttributes = [
              'new_husband_first_name' => 'Husband First Name',
              'new_husband_last_name' => 'Husband Last Name',
            ];
            $rules = array_merge($rules, $filedRules);
            $attributes = array_merge($attributes, $filedAttributes);
          }

        }
        $messages = [
          'required' => 'The :attribute field is required.',
          'max' => 'Total :max characters allowed for :attribute.',
          'digits' => 'The :attribute must be exactly :digits digits.',
        ];




        // Fetch document details using the helper function
        $bank_doc_count = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('document_type', $this->bank_doc)->count();
        $doc_aadhar_arr = DataInsert::getDocumentDetails($this->aadhar_doc);
        $doc_bank_arr = DataInsert::getDocumentDetails($this->bank_doc);
        $doc_caste_arr = DataInsert::getDocumentDetails($this->caste_doc);
        $doc_disability_arr = DataInsert::getDocumentDetails($this->disability_doc);
        $doc_epic_arr = DataInsert::getDocumentDetails($this->epic_doc);
        $doc_ration_arr = DataInsert::getDocumentDetails($this->ration_doc);
        $doc_husband_arr = DataInsert::getDocumentDetails($this->husband_doc);

        $bank_doc_count = 1;

        if ($doc_aadhar_arr && $row->no_aadhar == 1) {
          $required = 'required';
          $rules['new_aadhar_doc'] = $required . '|mimes:' . $doc_aadhar_arr['doc_type'] . '|max:' .
            $doc_aadhar_arr['doc_size_kb'] . ',';
          $messages['new_aadhar_doc.max'] = "The file uploaded for " . $doc_aadhar_arr['doc_name'] . " size must be less than " .
            $doc_aadhar_arr['doc_size_kb'] . " KB";
          $messages['new_aadhar_doc.mimes'] = "The file uploaded for " . $doc_aadhar_arr['doc_name'] . " must be of type " .
            $doc_aadhar_arr['doc_type'];
          $messages['new_aadhar_doc.required'] = "Document for " . $doc_aadhar_arr['doc_name'] . " must be uploaded";
        }


        if ($doc_bank_arr && $bank_doc_count == 0) {
          $required = 'required';
          $rules['new_bank_doc'] = $required . '|mimes:' . $doc_bank_arr['doc_type'] . '|max:' . $doc_bank_arr['doc_size_kb'] .
            ',';
          $messages['new_bank_doc.max'] = "The file uploaded for " . $doc_bank_arr['doc_name'] . " size must be less than " .
            $doc_bank_arr['doc_size_kb'] . " KB";
          $messages['new_bank_doc.mimes'] = "The file uploaded for " . $doc_bank_arr['doc_name'] . " must be of type " .
            $doc_bank_arr['doc_type'];
          $messages['new_bank_doc.required'] = "Document for " . $doc_bank_arr['doc_name'] . " must be uploaded";
        }


        if ($doc_caste_arr && $row->is_incomplete == 1 && ($scheme_id == 1 || $scheme_id == 3)) {
          $required = 'required';
          $rules['new_caste_certificate_doc'] = $required . '|mimes:' . $doc_caste_arr['doc_type'] . '|max:' .
            $doc_caste_arr['doc_size_kb'] . ',';
          $messages['new_caste_certificate_doc.max'] = "The file uploaded for " . $doc_caste_arr['doc_name'] . " size must be less
          than " . $doc_caste_arr['doc_size_kb'] . " KB";
          $messages['new_caste_certificate_doc.mimes'] = "The file uploaded for " . $doc_caste_arr['doc_name'] . " must be of type
          " . $doc_caste_arr['doc_type'];
          $messages['new_caste_certificate_doc.required'] = "Document for " . $doc_caste_arr['doc_name'] . " must be uploaded";
        }

        if ($doc_disability_arr && $row->is_incomplete == 1 && $scheme_id == 2) {
          $required = 'required';
          $rules['new_disability_doc'] = $required . '|mimes:' . $doc_disability_arr['doc_type'] . '|max:' .
            $doc_disability_arr['doc_size_kb'] . ',';
          $messages['new_disability_doc.max'] = "The file uploaded for " . $doc_disability_arr['doc_name'] . " size must be less
          than " . $doc_disability_arr['doc_size_kb'] . " KB";
          $messages['new_disability_doc.mimes'] = "The file uploaded for " . $doc_disability_arr['doc_name'] . " must be of type
          " . $doc_disability_arr['doc_type'];
          $messages['new_disability_doc.required'] = "Document for " . $doc_disability_arr['doc_name'] . " must be uploaded";
        }

        if ($doc_husband_arr && $row->is_incomplete == 1 && $scheme_id == 11) {
          $required = 'required';
          $rules['new_husband_death_doc'] = $required . '|mimes:' . $doc_husband_arr['doc_type'] . '|max:' .
            $doc_husband_arr['doc_size_kb'] . ',';
          $messages['new_husband_death_doc.max'] = "The file uploaded for " . $doc_husband_arr['doc_name'] . " size must be less
          than " . $doc_husband_arr['doc_size_kb'] . " KB";
          $messages['new_husband_death_doc.mimes'] = "The file uploaded for " . $doc_husband_arr['doc_name'] . " must be of type
          " . $doc_husband_arr['doc_type'];
          $messages['new_husband_death_doc.required'] = "Document for " . $doc_husband_arr['doc_name'] . " must be uploaded";
        }

        if ($doc_bank_arr && in_array($row->is_bank_failed, [1, 3])) {
          $required = 'required';
          $rules['new_bank_doc'] = $required . '|mimes:' . $doc_bank_arr['doc_type'] . '|max:' . $doc_bank_arr['doc_size_kb'] .
            ',';
          $messages['new_bank_doc.max'] = "The file uploaded for " . $doc_bank_arr['doc_name'] . " size must be less than " .
            $doc_bank_arr['doc_size_kb'] . " KB";
          $messages['new_bank_doc.mimes'] = "The file uploaded for " . $doc_bank_arr['doc_name'] . " must be of type " .
            $doc_bank_arr['doc_type'];
          $messages['new_bank_doc.required'] = "Document for " . $doc_bank_arr['doc_name'] . " must be uploaded";
        }




        $updateArray = $this->getUpdateCode($row, $request, 1);

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        // dd($validator);
        if (!$validator->passes()) {
          // dd($validator->errors()->all());
          return redirect()->route('editApplicantDetails', ['id' => $ben_id, 'scheme_id' => $scheme_id])
            ->withErrors($validator->errors()->all())
            ->withInput();

        } else {



          if (AuthChecker::VerifierPermission()) {
            $beneficiaryRow = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where(
              'scheme_id',
              $scheme_id
            )->first();

            $old_values = [];
            $new_values = [];


            $benEntry_model = BenEntry::find($ben_id);

            // Old values
            $old_values['aadhar_no'] = $beneficiaryRow->aadhar_no;
            $old_values['mobile_no'] = $beneficiaryRow->mobile_no;
            $old_values['bank_code'] = trim($beneficiaryRow->bank_code);
            $old_values['bank_ifsc'] = trim($beneficiaryRow->bank_ifsc);
            $old_values['bank_name'] = trim($beneficiaryRow->bank_name);
            $old_values['branch_name'] = trim($beneficiaryRow->branch_name);
            $old_values['caste'] = trim($beneficiaryRow->caste);
            $old_values['caste_certificate_no'] = trim($beneficiaryRow->caste_certificate_no);
            $old_values['type_disability'] = trim($beneficiaryRow->type_disability);
            $old_values['percentage_disability'] = trim($beneficiaryRow->percentage_disability);
            $old_values['certifying_auth'] = trim($beneficiaryRow->certifying_auth);
            $old_values['disability_designation'] = trim($beneficiaryRow->disability_designation);
            $old_values['husband_fname'] = trim($beneficiaryRow->husband_fname);
            $old_values['husband_mname'] = trim($beneficiaryRow->husband_mname);
            $old_values['husband_lame'] = trim($beneficiaryRow->husband_lname);
            // New values
            $new_values['aadhar_no'] = $request->new_aadhar_no;
            $new_values['mobile_no'] = $request->new_mobile_no;
            $new_values['bank_code'] = trim($request->new_bank_code);
            $new_values['bank_ifsc'] = $request->bank_ifsc_code;
            $new_values['bank_name'] = trim($request->new_bank_name);
            $new_values['branch_name'] = trim($request->new_bank_branch);
            $new_values['caste'] = trim($request->new_caste_category);
            $new_values['caste_certificate_no'] = trim($request->new_caste_certificate_no);
            $new_values['type_disability'] = trim($request->new_disablity_type);
            $new_values['percentage_disability'] = trim($request->new_disablity_type_percentage);
            $new_values['certifying_auth'] = trim($request->new_certifying_authority);
            $new_values['disability_designation'] = trim($request->new_disability_designation);
            $new_values['husband_fname'] = trim($request->new_husband_first_name);
            $new_values['husband_mname'] = trim($request->new_husband_middle_name);
            $new_values['husband_lname'] = trim($request->new_husband_last_name);




            // dd($request->all());

            $benEntry_model->next_level_clean_id = 2;
            $benEntry_model->is_clean = 1;
            $benEntry_model->action_by = $user_id;
            $benEntry_model->action_ip_address = request()->ip();
            $benEntry_model->action_type = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();

            if (($row->no_aadhar == 1 || $row->dup_aadhar == 1) && !empty($request->new_aadhar_no) && ($request->old_aadhar_no != $request->new_aadhar_no)) {
              $benEntry_model->aadhar_no = $request->new_aadhar_no;
            }
            if (($row->no_mobile == 1 || $row->dup_mobile == 1) && !empty($request->new_mobile_no) && ($request->old_mobile_no != $request->new_mobile_no)) {
              $benEntry_model->mobile_no = $request->new_mobile_no;

            }
            if (($row->dup_bank == 1) && !empty($request->new_bank_code) && ($request->new_bank_code != $request->old_bank_code)) {
              $benEntry_model->bank_code = $request->new_bank_code;
            }
            if (($row->is_bank_failed == 1) && !empty($request->new_bank_code) && ($request->new_bank_code != $request->old_bank_code)) {
              $benEntry_model->bank_code = $request->new_bank_code;
              $benEntry_model->bank_ifsc = $request->bank_ifsc_code;
              if (!empty($request->new_mobile_no) && ($request->old_mobile_no != $request->new_mobile_no)) {
                $benEntry_model->mobile_no = $request->new_mobile_no;
              }
            }

            if ($row->is_bank_failed == 2 && !empty($request->process_type) && ($request->process_type == 2) && !empty($request->new_bank_code) && ($request->old_bank_code != $request->new_bank_code)) {

              $benEntry_model->bank_code = $request->new_bank_code;
              $benEntry_model->bank_ifsc = $request->bank_ifsc_code;
              $benEntry_model->branch_name = $request->new_bank_branch;
              $benEntry_model->bank_name = $request->new_bank_name;
            }

            if (($row->is_bank_failed == 3) && !empty($request->new_bank_code) && ($request->new_bank_code != $request->old_bank_code)) {
              $benEntry_model->bank_code = $request->new_bank_code;
              $benEntry_model->bank_ifsc = $request->bank_ifsc_code;
              $benEntry_model->branch_name = $request->new_bank_branch;
              $benEntry_model->bank_name = $request->new_bank_name;
            }

            if (($row->is_incomplete == 1) && ($row->scheme_id = 1 || $row->scheme_id = 3) && !empty($request->new_caste_certificate_no) && !empty($request->new_caste_category)) {
              $benEntry_model->caste_certificate_no = $request->new_caste_certificate_no;
              $benEntry_model->caste = $request->new_caste_category;
            }

            if (($row->is_incomplete == 1) && ($row->scheme_id = 2) && !empty($request->new_disablity_type) && !empty($request->new_disablity_type_percentage)) {
              $benEntry_model->type_disability = $request->new_disablity_type;
              $benEntry_model->percentage_disability = $request->new_disablity_type_percentage;
              $benEntry_model->certifying_auth = $request->new_disablity_type_authority;
              $benEntry_model->disability_designation = $request->new_disability_designation;
            }

            if (($row->is_incomplete == 1) && ($row->scheme_id = 1) && !empty($request->new_husband_first_name)) {
              $benEntry_model->husband_fname = $request->new_husband_first_name;
              $benEntry_model->husband_mname = $request->new_husband_middle_name;
              $benEntry_model->husband_lname = $request->new_husband_last_name;
            }
            $ben_failed_payment_details_model = BenFailedPaymentDetails::where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->where('edited_status', 0)->whereIn('failed_type', [1, 2, 3, 4, 5])->first();

            if ($row->is_bank_failed == 1 || $row->is_bank_failed == 2 || $row->is_bank_failed == 3) {
              if (
                $paymentErrorType->pay_validated == 3 || $paymentErrorType->pay_validated == 4 || $paymentErrorType->pay_validated
                == 5 || $paymentErrorType->acc_validated == 3 || $paymentErrorType->acc_validated == 4
              ) {
                $newPaymentDetails = [];
                $getNpciBankCode = BankDetails::where('ifsc', $request->bank_ifsc_code)->first();
                $newPaymentDetails['new_bank_name'] = trim($request->new_bank_name);
                $newPaymentDetails['new_bank_branch'] = trim($request->new_bank_branch);
                $newPaymentDetails['new_bank_ifsc'] = $request->bank_ifsc_code;
                $newPaymentDetails['new_bank_code'] = trim($request->new_bank_code);
                $newPaymentDetails['npci_bank_code'] = $getNpciBankCode->bank_code;
                $ben_failed_payment_details_model->updated_details = json_encode($newPaymentDetails);
                $ben_failed_payment_details_model->edited_status = 1;
                $ben_failed_payment_details_model->updated_at = date('Y-m-d H:i:s');
              }
            }





            $is_bank_upload = 0;
            $is_aadhar_upload = 0;
            $is_caste_upload = 0;
            $is_disability_upload = 0;
            $is_husband_upload = 0;




            DB::connection('pgsql')->beginTransaction();
            DB::connection('pgsql5')->beginTransaction();
            DB::connection('pgsql_encwrite')->beginTransaction();
            DB::connection('pgsql_paywrite')->beginTransaction();


            /////////////////////////////////Document Upload START//////////////////////////////////////////////// 


            if (!empty($request->process_type)) {
              if ($request->process_type == 3) {
                $benRejectRequest = new BenRejectRequest;
                $benRejectRequest->ben_id = $ben_id;
                $benRejectRequest->scheme_id = $scheme_id;
                $benRejectRequest->rejected_by = Auth::user()->id;
                $benRejectRequest->rejected_ip = request()->ip();
                $benRejectRequest->rejected_date = date('Y-m-d H:i:s');
                $benRejectRequest->next_level_clean_id = 2;
                $benRejectLog = $benRejectRequest->save();
              }
              $benRejectLog = 1;
            } else {

              $benRejectLog = 1;
            }

            // Upload Aadhar Card
            if ($request->hasFile('new_aadhar_doc')) {
              $doc_file = $request->file('new_aadhar_doc');
              $img_data = file_get_contents($doc_file);
              // echo $img_data;die;
              $u_extension = $doc_file->getClientOriginalExtension();
              $mime_type = $doc_file->getMimeType();
              // dd($mime_type);
              if (strtolower($mime_type) == 'image/jpeg') {
                if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
                  $extension = $u_extension;
                } else {
                  $errorMsg = "You are trying to upload an incorrect file for " . $doc_aadhar_arr['doc_name'];
                  return $response = array(
                    'status' => 0,
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
                $errorMsg = "You are trying to upload an incorrect file for " . $doc_aadhar_arr['doc_name'];
                return $response = array(
                  'status' => 0,
                  'msg' => $errorMsg,
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!'
                );
              }
              if ($u_extension != $extension) {
                $errorMsg = "You are trying to upload an incorrect file for " . $doc_aadhar_arr['doc_name'];
                return $response = array(
                  'status' => 0,
                  'msg' => $errorMsg,
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!'
                );
              }

              $base64 = base64_encode($img_data);
              $ip_address = request()->ip();
              $c_datetime = date('Y-m-d H:i:s', time());
              $user_id = AuthChecker::getUserId();

              // $is_aadhar_upload = DataInsert::insertBenAttachDocuments($ben_id, $scheme_id, $this->aadhar_doc, $base64, $mapping_level, $user_id, $ip_address, $extension, $mime_type, $beneficiaryRow->created_by_dist_code, $beneficiaryRow->created_by_local_body_code, $doc_aadhar_arr['doc_name'], $c_datetime);
              $is_aadhar_upload = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                in_beneficiary_id => " . $ben_id . ",
                in_scheme_id => " . $scheme_id . ",
                in_document_type => " . $this->aadhar_doc . ",
                in_attched_document => '" . $base64 . "',
                in_created_by_level => '" . $mapping_level . "',
                in_created_by => " . Auth::user()->id . ",
                in_ip_address => '" . request()->ip() . "',
                in_document_extension => '" . $extension . "',
                in_document_mime_type => '" . $mime_type . "',
                in_created_by_dist_code => " . $beneficiaryRow->created_by_dist_code . ",
                in_created_by_local_body_code => " . $beneficiaryRow->created_by_local_body_code . ",
                in_doc_type_name => '" . $doc_aadhar_arr['doc_name'] . "',
                in_datetime => '" . $c_datetime . "'
                );"
              );
              $is_aadhar_upload = $is_aadhar_upload[0]->ben_docs_insert_archive;
            }


            // Upload Bank Document
            if ($request->hasFile('new_bank_doc')) {
              $doc_file = $request->file('new_bank_doc');
              $img_data = file_get_contents($doc_file);
              // echo $img_data;die;
              $u_extension = $doc_file->getClientOriginalExtension();
              $mime_type = $doc_file->getMimeType();

              if (strtolower($mime_type) == 'image/jpeg') {
                if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
                  $extension = $u_extension;
                } else {
                  $errorMsg = "You are trying to upload an incorrect file for " . $doc_bank_arr['doc_name'];
                  return $response = array(
                    'status' => 0,
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
                $errorMsg = "You are trying to upload an incorrect file for " . $doc_bank_arr['doc_name'];
                return $response = array(
                  'status' => 0,
                  'msg' => $errorMsg,
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!'
                );
              }
              if ($u_extension != $extension) {
                $errorMsg = "You are trying to upload an incorrect file for " . $doc_bank_arr['doc_name'];
                return $response = array(
                  'status' => 0,
                  'msg' => $errorMsg,
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!'
                );
              }

              $base64 = base64_encode($img_data);
              $ip_address = request()->ip();
              $c_datetime = date('Y-m-d H:i:s', time());
              $user_id = AuthChecker::getUserId();


              // $is_bank_upload = DataInsert::insertBenAttachDocuments($ben_id, $scheme_id, $this->bank_doc, $base64, $mapping_level, $user_id, $ip_address, $extension, $mime_type, $beneficiaryRow->created_by_dist_code, $beneficiaryRow->created_by_local_body_code, $doc_bank_arr['doc_name'], $c_datetime);
              $is_bank_upload = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                in_beneficiary_id => " . $ben_id . ",
                in_scheme_id => " . $scheme_id . ",
                in_document_type => " . $this->bank_doc . ",
                in_attched_document => '" . $base64 . "',
                in_created_by_level => '" . $mapping_level . "',
                in_created_by => " . Auth::user()->id . ",
                in_ip_address => '" . request()->ip() . "',
                in_document_extension => '" . $extension . "',
                in_document_mime_type => '" . $mime_type . "',
                in_created_by_dist_code => " . $beneficiaryRow->created_by_dist_code . ",
                in_created_by_local_body_code => " . $beneficiaryRow->created_by_local_body_code . ",
                in_doc_type_name => '" . $doc_bank_arr['doc_name'] . "',
                in_datetime => '" . $c_datetime . "'
                );"
              );
              $is_bank_upload = $is_bank_upload[0]->ben_docs_insert_archive;

            }


            // Upload Caste Certificate Document
            if ($request->hasFile('new_caste_certificate_doc')) {
              $doc_file = $request->file('new_caste_certificate_doc');
              $img_data = file_get_contents($doc_file);
              // echo $img_data;die;
              $u_extension = $doc_file->getClientOriginalExtension();
              $mime_type = $doc_file->getMimeType();

              if (strtolower($mime_type) == 'image/jpeg') {
                if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
                  $extension = $u_extension;
                } else {
                  $errorMsg = "You are trying to upload an incorrect file for " . $doc_caste_arr['doc_name'];
                  return $response = array(
                    'status' => 0,
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
                $errorMsg = "You are trying to upload an incorrect file for " . $doc_caste_arr['doc_name'];
                return $response = array(
                  'status' => 0,
                  'msg' => $errorMsg,
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!'
                );
              }
              if ($u_extension != $extension) {
                $errorMsg = "You are trying to upload an incorrect file for " . $doc_caste_arr['doc_name'];
                return $response = array(
                  'status' => 0,
                  'msg' => $errorMsg,
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!'
                );
              }

              $base64 = base64_encode($img_data);
              $ip_address = request()->ip();
              $c_datetime = date('Y-m-d H:i:s', time());
              $user_id = AuthChecker::getUserId();

              // $is_caste_upload = DataInsert::insertBenAttachDocuments($ben_id, $scheme_id, $this->caste_doc, $base64, $mapping_level, $user_id, $ip_address, $extension, $mime_type, $beneficiaryRow->created_by_dist_code, $beneficiaryRow->created_by_local_body_code, $doc_caste_arr['doc_name'], $c_datetime);

              $is_caste_upload = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                in_beneficiary_id => " . $ben_id . ",
                in_scheme_id => " . $scheme_id . ",
                in_document_type => " . $this->caste_doc . ",
                in_attched_document => '" . $base64 . "',
                in_created_by_level => '" . $mapping_level . "',
                in_created_by => " . Auth::user()->id . ",
                in_ip_address => '" . request()->ip() . "',
                in_document_extension => '" . $extension . "',
                in_document_mime_type => '" . $mime_type . "',
                in_created_by_dist_code => " . $beneficiaryRow->created_by_dist_code . ",
                in_created_by_local_body_code => " . $beneficiaryRow->created_by_local_body_code . ",
                in_doc_type_name => '" . $doc_caste_arr['doc_name'] . "',
                in_datetime => '" . $c_datetime . "'
                );"
              );

              $is_caste_upload = $is_caste_upload[0]->ben_docs_insert_archive;
            }


            if ($request->hasFile('new_disability_doc')) {
              $doc_file = $request->file('new_disability_doc');
              $img_data = file_get_contents($doc_file);
              // echo $img_data;die;
              $u_extension = $doc_file->getClientOriginalExtension();
              $mime_type = $doc_file->getMimeType();

              if (strtolower($mime_type) == 'image/jpeg') {
                if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
                  $extension = $u_extension;
                } else {
                  $errorMsg = "You are trying to upload an incorrect file for " . $doc_disability_arr['doc_name'];
                  return $response = array(
                    'status' => 0,
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
                $errorMsg = "You are trying to upload an incorrect file for " . $doc_disability_arr['doc_name'];
                return $response = array(
                  'status' => 0,
                  'msg' => $errorMsg,
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!'
                );
              }
              if ($u_extension != $extension) {
                $errorMsg = "You are trying to upload an incorrect file for " . $doc_disability_arr['doc_name'];
                return $response = array(
                  'status' => 0,
                  'msg' => $errorMsg,
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!'
                );
              }

              $base64 = base64_encode($img_data);
              $ip_address = request()->ip();
              $c_datetime = date('Y-m-d H:i:s', time());
              $user_id = AuthChecker::getUserId();

              // $is_disability_upload = DataInsert::insertBenAttachDocuments($ben_id, $scheme_id, $this->disability_doc, $base64, $mapping_level, $user_id, $ip_address, $extension, $mime_type, $beneficiaryRow->created_by_dist_code, $beneficiaryRow->created_by_local_body_code, $doc_disability_arr['doc_name'], $c_datetime);

              $is_disability_upload = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                in_beneficiary_id => " . $ben_id . ",
                in_scheme_id => " . $scheme_id . ",
                in_document_type => " . $this->disability_doc . ",
                in_attched_document => '" . $base64 . "',
                in_created_by_level => '" . $mapping_level . "',
                in_created_by => " . Auth::user()->id . ",
                in_ip_address => '" . request()->ip() . "',
                in_document_extension => '" . $extension . "',
                in_document_mime_type => '" . $mime_type . "',
                in_created_by_dist_code => " . $beneficiaryRow->created_by_dist_code . ",
                in_created_by_local_body_code => " . $beneficiaryRow->created_by_local_body_code . ",
                in_doc_type_name => '" . $doc_disability_arr['doc_name'] . "',
                in_datetime => '" . $c_datetime . "'
                );"
              );

              $is_disability_upload = $is_disability_upload[0]->ben_docs_insert_archive;

            }


            if ($request->hasFile('new_husband_death_doc')) {
              $doc_file = $request->file('new_husband_death_doc');
              $img_data = file_get_contents($doc_file);
              // echo $img_data;die;
              $u_extension = $doc_file->getClientOriginalExtension();
              $mime_type = $doc_file->getMimeType();

              if (strtolower($mime_type) == 'image/jpeg') {
                if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
                  $extension = $u_extension;
                } else {
                  $errorMsg = "You are trying to upload an incorrect file for " . $doc_husband_arr['doc_name'];
                  return $response = array(
                    'status' => 0,
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
                $errorMsg = "You are trying to upload an incorrect file for " . $doc_husband_arr['doc_name'];
                return $response = array(
                  'status' => 0,
                  'msg' => $errorMsg,
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!'
                );
              }
              if ($u_extension != $extension) {
                $errorMsg = "You are trying to upload an incorrect file for " . $doc_husband_arr['doc_name'];
                return $response = array(
                  'status' => 0,
                  'msg' => $errorMsg,
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!'
                );
              }

              $base64 = base64_encode($img_data);
              $ip_address = request()->ip();
              $c_datetime = date('Y-m-d H:i:s', time());
              $user_id = AuthChecker::getUserId();

              // $is_husband_upload = DataInsert::insertBenAttachDocuments($ben_id, $scheme_id, $this->husband_doc, $base64, $mapping_level, $user_id, $ip_address, $extension, $mime_type, $beneficiaryRow->created_by_dist_code, $beneficiaryRow->created_by_local_body_code, $doc_husband_arr['doc_name'], $c_datetime);

              $is_husband_upload = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                in_beneficiary_id => " . $ben_id . ",
                in_scheme_id => " . $scheme_id . ",
                in_document_type => " . $this->aadhar_doc . ",
                in_attched_document => '" . $base64 . "',
                in_created_by_level => '" . $mapping_level . "',
                in_created_by => " . Auth::user()->id . ",
                in_ip_address => '" . request()->ip() . "',
                in_document_extension => '" . $extension . "',
                in_document_mime_type => '" . $mime_type . "',
                in_created_by_dist_code => " . $beneficiaryRow->created_by_dist_code . ",
                in_created_by_local_body_code => " . $beneficiaryRow->created_by_local_body_code . ",
                in_doc_type_name => 'Husband Death Certificate',
                in_datetime => '" . $c_datetime . "'
                );"
              );
              $is_husband_upload = $is_husband_upload[0]->ben_docs_insert_archive;

            }

            // dd($request->all());
            /////////////////////////////////Document Upload START////////////////////////////////////////////////
            $benAadharCheck = 0;
            $benMobileCheck = 0;
            $benBankCheck = 0;
            $benCasteCategoryCheck = 0;
            $benAadharCheckCross = 0;
            $benMobileCheckCross = 0;
            $benBankCheckCross = 0;
            $benCasteCategoryCheckCross = 0;

            // if($ben_id == 1038987)
            // {
            //   dd($request->new_aadhar_no);
            // }


            // dd($row->dup_bank == 1 || ( $row->is_bank_failed = 1 || $row->is_bank_failed = 2 || $row->is_bank_failed = 3));

            if ($row->no_aadhar == 1 || $row->dup_aadhar == 1 && ($request->old_aadhar != $request->new_aadhar)) {
              $benAadharCheck = DupCheck::dupAadharCheckSame($scheme_id, $request->new_aadhar_no, $ben_id);
              $benAadharCheckCross = DupCheck::dupAadharCheckCross($scheme_id, $request->new_aadhar_no, $ben_id);

            }
            if ($row->no_mobile == 1 || $row->dup_mobile == 1 && ($request->old_mobile_no != $request->new_mobile_no)) {
              $benMobileCheck = DupCheck::dupMobileCheckSame($scheme_id, $request->new_mobile_no, $ben_id);
              $benMobileCheckCross = DupCheck::dupMobileCheckCross($scheme_id, $request->new_mobile_no, $ben_id);

            }
            if ($row->is_incomplete == 1 && in_array($row->scheme_id, [1, 3])) {
              $benCasteCategoryCheck = DupCheck::dupCasteCheckSame($scheme_id, $request->new_caste_certificate_no, $ben_id);
              $benCasteCategoryCheckCross = DupCheck::dupCasteCheckCross($scheme_id, $request->new_caste_certificate_no, $ben_id);

            }
            if ($row->dup_bank == 1 && !empty($request->new_bank_code) && $request->new_bank_code != $request->old_bank_code) {
              $benBankCheck = DupCheck::dupBankCheckSame($scheme_id, $request->new_bank_code, $ben_id);
              $benBankCheckCross = DupCheck::dupBankCheckCross($scheme_id, $request->new_bank_code, $ben_id);
            }

            if ($row->is_bank_failed == 1 && !empty($request->new_bank_code) && $request->new_bank_code != $request->old_bank_code) {
              $benBankCheck = DupCheck::dupBankCheckSame($scheme_id, $request->new_bank_code, $ben_id);
              $benBankCheckCross = DupCheck::dupBankCheckCross($scheme_id, $request->new_bank_code, $ben_id);
            }


            // if ($ben_id == 10100626 ) {
            //   dump($benAadharCheck);
            //   dump($benMobileCheck);
            //   dump($benBankCheck);
            //   dump($benCasteCategoryCheck);
            //   dump($benAadharCheckCross);
            //   dump($benMobileCheckCross);
            //   dump($benBankCheckCross);
            //   dump($benCasteCategoryCheckCross);
            //   dump($benAadharCheck);
            //   dump($benCasteCategoryCheckCross);
            //   dump($is_aadhar_upload);
            //   dump($is_bank_upload);
            //   dump($is_caste_upload);
            //   dump($is_disability_upload);
            //   dump($is_husband_upload);
            //   die;
            // }


            if ($benAadharCheck > 0) {
              $errorMsg = "This Aadhar No: " . $request->new_aadhar_no . " is already exist.";
              return redirect()->route('editApplicantDetails', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
            } elseif ($benMobileCheck > 0) {
              $errorMsg = "This Mobile No: " . $request->new_mobile_no . " is already exist.";
              return redirect()->route('editApplicantDetails', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
            } elseif ($benBankCheck > 0) {
              $errorMsg = "This Bank Account: " . $request->bank_code . " is already exist.";
              return redirect()->route('editApplicantDetails', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
            } elseif ($benCasteCategoryCheck > 0) {
              $errorMsg = "This Caste Certificate No: " . $request->new_caste_certificate_no . " is already exist.";
              return redirect()->route('editApplicantDetails', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
            } elseif ($benAadharCheckCross > 0) {
              $errorMsg = "This Aadhar No: " . $request->new_aadhar_no . " is already exist on Cross Scheme.";
              return redirect()->route('editApplicantDetails', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
            } elseif ($benMobileCheckCross > 0) {
              $errorMsg = "This Mobile No: " . $request->new_mobile_no . " is already exist on Cross Scheme.";
              return redirect()->route('editApplicantDetails', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
            } elseif ($benBankCheckCross > 0) {
              $errorMsg = "This Bank Account: " . $request->bank_code . " is already exist on Cross Scheme.";
              return redirect()->route('editApplicantDetails', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
            } elseif ($benCasteCategoryCheckCross > 0) {
              $errorMsg = "This Caste Certificate No: " . $request->new_caste_certificate_no . " is already exist on Cross Scheme.";
              return redirect()->route('editApplicantDetails', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
            }

            // if($ben_id == 13257480){
//   dd($benAadharCheck == 0 && $benMobileCheck == 0 && $benBankCheck == 0 && $benCasteCategoryCheck == 0
//   && $benAadharCheckCross == 0 && $benMobileCheckCross == 0 && $benBankCheckCross == 0 && $benCasteCategoryCheckCross == 0
//   && $benAadharCheckCross == 0 || ($is_aadhar_upload == 1 || $is_bank_upload == 1 || $is_caste_upload == 1 || $is_disability_upload == 1 || $is_husband_upload == 1));
// }
            // dd(   $benAadharCheck == 0 && $benMobileCheck == 0 && $benBankCheck == 0 && $benCasteCategoryCheck == 0
            // && $benAadharCheckCross == 0 && $benMobileCheckCross == 0 && $benBankCheckCross == 0 && $benCasteCategoryCheckCross == 0
            // && $benAadharCheckCross == 0 && ($is_aadhar_upload == 1 || $is_bank_upload == 1 || $is_caste_upload == 1 || $is_disability_upload == 1 || $is_husband_upload = 1));

            if (
              $benAadharCheck == 0 && $benMobileCheck == 0 && $benBankCheck == 0 && $benCasteCategoryCheck == 0
              && $benAadharCheckCross == 0 && $benMobileCheckCross == 0 && $benBankCheckCross == 0 && $benCasteCategoryCheckCross == 0
              && $benAadharCheckCross == 0 || ($is_aadhar_upload == 1 || $is_bank_upload == 1 || $is_caste_upload == 1 || $is_disability_upload == 1 || $is_husband_upload == 1)
            ) {
              //  dd($updateBenDetailsCount);
              $updateBenDetailsAction = 0;
              // if ($updateBenDetailsCount > 0) {


              // if($ben_id == 12277790)
              // {
              //   dd($request->all());
              // }
              foreach ($updateArray as $updatecode) {
                $accept_reject_info_model = new AcceptRejectInfo;
                $accept_reject_info_model->scheme_id = $scheme_id;
                $accept_reject_info_model->created_by_dist_code = $district_code;
                $accept_reject_info_model->created_by_local_body_code = $urban_body_code;
                $accept_reject_info_model->created_at = date('Y-m-d H:i:s');
                $accept_reject_info_model->updated_at = date('Y-m-d H:i:s');
                $accept_reject_info_model->op_type = $updatecode->code;
                $accept_reject_info_model->ip_address = request()->ip();
                $accept_reject_info_model->user_id = $user_id;
                $accept_reject_info_model->application_id = $ben_id;
                $accept_reject_info_model->old_data = json_encode($old_values);
                $accept_reject_info_model->new_data = json_encode($new_values);
                $accept_reject_info_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
                $accept_reject_info_model->remarks = $updatecode->description;
                $updateBenDetailsAction = $accept_reject_info_model->save();
              }
              // if($ben_id == 12277790){
              //   dd('ok');
              // }

              // dd($updateBenDetailsAction);
              // }

              // dd($isSame);

              // dd($request->all());
              if ($isSame == 0) {
                $ben_payment_details_model = BenPaymentDetails::where('ben_id', $ben_id)
                  ->where('scheme_id', $scheme_id)
                  ->first();
                $ben_payment_details_model->last_accno = trim($request->new_bank_code);
                $ben_payment_details_model->last_ifsc = $request->bank_ifsc_code;
                $ben_payment_details_model->npci_bank_code = $bankCodeRow->bank_code;
                if($isSameMobile == 0){
                  $ben_payment_details_model->mobile_no = trim($request->new_mobile_no);
                }
                $updateBenPaymentTable = $ben_payment_details_model->save();
                // dd($updateBenPaymentTable);
              } else {
                $updateBenPaymentTable = 1;
              }


              // dd($updateBenPaymentTable);

              // dd($row->dup_bank == 1 || in_array($row->is_bank_failed, [1, 2, 3]) );

              // dd($row->dup_bank == 1 || (!empty($request->is_bank_failed) && in_array($request->is_bank_failed, [1, 2, 3])));
              if ($ben_failed_payment_details_model) {
                if ($row->dup_bank == 1 || (!empty($request->is_bank_failed) && in_array($request->is_bank_failed, [1, 2, 3]))) {
                  if (
                    $paymentErrorType->pay_validated == 3 || $paymentErrorType->pay_validated == 4 || $paymentErrorType->pay_validated
                    == 5 || $paymentErrorType->acc_validated == 3 || $paymentErrorType->acc_validated == 4
                  ) {
                    $updateFailedPayment = $ben_failed_payment_details_model->save();
                  }
                } else {

                }
              } else {
                $updateFailedPayment = 1;
              }


              // dd($updateFailedPayment);


              $updateBenTable = $benEntry_model->save();

              // dd($updateBenTable);



              if ($bankDupRow > 0) {
                $ben_dup_bank_code_details = BenDupBankCodePayemntDetails::where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', -97)->first();
                // dd($ben_dup_bank_code_details);
                $ben_dup_bank_code_details->bank_code = trim($request->new_bank_code);
                $ben_dup_bank_code_details->bank_name = trim($request->new_bank_name);
                $ben_dup_bank_code_details->branch_name = trim($request->new_bank_branch);
                $ben_dup_bank_code_details->bank_ifsc = $request->bank_ifsc_code;
                if ($isSame == 1) {
                  $ben_dup_bank_code_details->next_level_role_id = 200;
                } else {
                  $ben_dup_bank_code_details->next_level_role_id = 101;
                }
                $updateBankDup = $ben_dup_bank_code_details->save();
              } else {
                $updateBankDup = 1;
              }

            } else {
              DB::connection('pgsql')->rollback();
              DB::connection('pgsql_encwrite')->rollback();
              DB::connection('pgsql_paywrite')->rollback();

              $return_text = "Something Went Wrong.";
              // $errorMsg = "This Caste Certificate No: " . $request->new_caste_certificate_no . " is already exist on Cross Scheme.";
              return redirect()->route('editApplicantDetails', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $return_text);
              // return redirect()->route('editApplicantDetails')->with('error', $return_text);
            }
            // $updateBenDetailsAction =1 ;
            // dump($updateBenDetailsAction, $updateBenTable, $updateBenPaymentTable, $updateBankDup, $updateFailedPayment);
            // die();
            // $updateBenPaymentTable = 1;
            // if ($ben_id == 581985  ) {
            //   dump($updateBenDetailsAction);
            //   dump($updateBenTable);
            //   dump($updateBenPaymentTable);
            //   dump($updateBankDup);
            //   dump($updateFailedPayment);
            //   dump($benRejectLog);
            //   dd('ok');
            //   die;
            // }
            if ($updateBenDetailsAction && $updateBenTable && $updateBenPaymentTable && $updateBankDup && $updateFailedPayment && $benRejectLog) {
              // if($ben_id == 4183575 )
              // {
              //   dd('ok');
              // }
              DB::connection('pgsql')->commit();
              DB::connection('pgsql_encwrite')->commit();
              DB::connection('pgsql_paywrite')->commit();
              $return_text = "Beneficiary(" . $beneficiaryRow->id . ") has successfully verified & forwared for approval.";
              // dd($return_text);
              return redirect()->route('noDupBeneficiariesList')->with('success', $return_text);
            } else {
              DB::connection('pgsql')->rollback();
              DB::connection('pgsql_encwrite')->rollback();
              DB::connection('pgsql_paywrite')->rollback();
              $return_text = "Beneficiary can't be updated.Something Went Wrong.";
              return redirect()->route('noDupBeneficiariesList')->with('error', $return_text);
            }
          }

        }
      } catch (\Exception $e) {
        // if ($ben_id == 10100626 ) {

        //   dd($e);
        // }
        return redirect()->route('noDupBeneficiariesList')->with('error', 'Something Went Wrong');
      }
    } else {
      return redirect()->route('noDupBeneficiariesList')->with('error', 'Not Allowded');
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
        $checkAadharCount = DB::connection('pgsql')->table('pension.beneficiaries')->where(DB::raw("TRIM(aadhar_no)"), $aadhar_no)->whereIn('is_clean', [1, 2])->count();
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
        $checkBankCount = DB::connection('pgsql')->table('pension.beneficiaries')->where(DB::raw("TRIM(bank_code)"), $bank_code)->whereIn('is_clean', [1, 2])->count();
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
        $checkMobileCount = DB::connection('pgsql')->table('pension.beneficiaries')->where('mobile_no', $mobile_no)->whereIn('is_clean', [1, 2])->count();
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
  public function approver_linelisting_index(Request $request)
  {
    $type = $request->has('type') ? (int) $request->type : null;
    // dd($type);
    $auth = AuthChecker::ApproverPermission();
    if ($auth) {

      $user_id = AuthChecker::getUserId();
      $dutyObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
      $distCode = $dutyObj->district_code;
      $incomplete_types = DB::table('public.m_incomplete_type')->where('is_active', true)->get();
      $scheme = DB::connection('pgsql_mis')->select(
        'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
        $user_id .
        ' and is_active=1) order by scheme_name'
      );
      if (AuthChecker::ApproverPermission()) {
        $levels = [
          2 => 'Rural',
          1 => 'Urban'
        ];
      }
      $incomplete_types = DB::table('public.m_incomplete_type')->where('is_active', true)->get();
      return view('No-Duplicate-Update.linelisting_approver', [
        'levels' => $levels,
        'schemes' => $scheme,
        'dist_code' => $distCode,
        'incomplete_types' => $incomplete_types,
        'type_id' => $type
      ]);
    }

  }

  public function getNoDupVerifiedList(Request $request)
  {
    // dd($request->all());
    $user_id = AuthChecker::getUserId();
    $dutyObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
    $distCode = $dutyObj->district_code;
    $rural_urban = $request->filter_1;
    $local_body_code = $request->filter_2;
    $scheme_id = $request->scheme_id;
    $filter_type = $request->filter_type;
    // $failed_type_id = $request->failed_type_id;
    $pay_validated = $request->failed_type_id;
    if ($request->ajax()) {
      // dd($request->all());
      if (AuthChecker::ApproverPermission() && !empty($scheme_id)) {
        $query = '';
        $query = "SELECT * FROM pension.beneficiaries WHERE created_by_dist_code = '" . $distCode . "' AND  next_level_clean_id = 2 AND is_rejected = 0 AND is_clean = 1 AND scheme_id = " . $scheme_id . "";
        if ($filter_type == 0) {
          $query .= "AND is_incomplete = 1 ";
        } else if ($filter_type == 1) {
          $query .= "AND dup_aadhar = 1 ";
        } else if ($filter_type == 2) {
          $query .= "AND no_aadhar = 1 ";
        } else if ($filter_type == 3) {
          $query .= "AND dup_bank = 1 ";
        } else if ($filter_type == 4) {
          $query .= "AND dup_mobile = 1 ";
        } else if ($filter_type == 5) {
          $query .= "AND no_mobile = 1 ";
        } else if ($filter_type == 6) {
          $query .= "AND no_ration_card = 1 ";
        } else if ($filter_type == 8) {
          $query .= "AND no_epic_voter = 1 ";
        }
        // if ($filter_type == 10) {
        //   $query .= "AND is_bank_failed = 1 ";
        // }
        else if ($filter_type == 11) {
          $query .= "AND is_bank_failed = 2 ";
        } else if ($filter_type == 12) {
          $query .= "AND is_bank_failed = 3 ";
        } else if ($filter_type == 13) {
          $query = '';
          $query = "SELECT a.id as id , a.scheme_id as scheme_id , a.is_incomplete, a.dup_bank, a.is_bank_failed ,a.pay_validated, a.no_aadhar, a.dup_aadhar , a.no_mobile , a.dup_mobile, a.ben_fname, a.ben_mname, a.ben_lname,a.block_ulb_name,a.gp_ward_name, a.created_by_local_body_code , a.created_by_dist_code FROM pension.beneficiaries a join  pension.ben_reject_request b on a.id=b.ben_id 
          WHERE a.created_by_dist_code = '" . $distCode . "' AND  a.next_level_clean_id = 2 and b.next_level_clean_id= 2  AND a.is_rejected = 0 AND a.is_clean in (1,2) AND a.scheme_id = " . $scheme_id . " ";
        }

        if (!empty($rural_urban)) {
          $query .= " AND rural_urban_id =" . $rural_urban . "";
        }
        if (!empty($local_body_code)) {
          $query .= " AND created_by_local_body_code = " . $local_body_code . "";
        }
        // dd($query);
        if ($filter_type == 10 || isset($pay_validated)) {
          $query .= "AND is_bank_failed = 1 ";
          // $query .= "AND id IN (";
          if ($pay_validated == 3) {

            $query .= "AND pay_validated = 3 ";
          }
          if ($pay_validated == 4) {

            $query .= "AND pay_validated = 4 ";
          }
          if ($pay_validated == 5) {

            $query .= "AND pay_validated = 5 ";

          }
          // $query .= ") ";
        }
        // dd($query);

        $data = DB::connection('pgsql_mis')->select($query);

        $data = collect($data)->map(function ($item) use ($scheme_id) {
          $pay_validation = DB::connection('pgsql_paywrite')
            ->table('payment.ben_payment_details')
            ->where('ben_id', $item->id)
            ->where('scheme_id', $scheme_id)
            ->select('pay_validated')
            ->first();

          $item->pay_validated = $pay_validation->pay_validated ?? null;
          return $item;
        });
      } else {
        $data = collect([]);
      }
      return datatables()->of($data)
        ->addIndexColumn()
        ->addColumn('name', function ($data) {
          $name = $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
          return $name;
        })
        ->addColumn('view', function ($data) {
          $action = '<button class="btn btn-primary btn-xs ben_view_button" value="' . $data->id . '_' . $data->scheme_id . '"><i class="glyphicon glyphicon-edit"></i>View</button>';
          return $action;
        })
        ->addColumn('check', function ($data) {
          return '<input type="checkbox"  name="chkbx" class="all_checkbox"  onclick="controlCheckBox();" value="' . $data->id . '">';
        })
        ->addColumn('status', function ($data) {
          $sl = 1;
          // $sl++;
          $status = '';
          if ($data->is_incomplete == 1) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Incomplete Data</b></span> <br>';
          }
          if ($data->dup_aadhar == 1) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Duplicate Aadhar</b></span> <br>';
          }
          if ($data->dup_mobile == 1) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Duplicate Mobile</b></span> <br>';
          }
          if ($data->dup_bank == 1) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Duplicate Bank</b></span> <br>';
          }
          if ($data->no_aadhar == 1) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. No Aadhar</b></span> <br>';
          }
          if ($data->no_mobile == 1) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. No Mobile</b></span> <br>';
          }

          if ($data->is_bank_failed == 1) {
            if ($data->pay_validated == 3) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment Failure SBI</b></span>';
            } elseif ($data->pay_validated == 4) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment Failure RBI</b></span>';
            } elseif ($data->pay_validated == 5) {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment Failure IFMS</b></span>';
            } else {
              $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Payment Failure </b></span>';
            }
          }
          if ($data->is_bank_failed == 2) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. Name Validation Failure</b></span> <br>';
          }
          if ($data->is_bank_failed == 3) {
            $status .= '<span class="text text-info" style="font-size: 12px;"><b>' . $sl++ . '. A/c Validation Failure</b></span> <br>';
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
    // dd($request->all());
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
      $ben_details = BenEntry::where('scheme_id', $scheme_id)->where('id', $id)->where('next_level_clean_id', 2)->first(); //
      // var_dump($ben_details);
      $status_array = $this->benStatus($id, $scheme_id);
      // dd($status);
      // dd($status);
      // dd($status_array);


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
          'ben_name' => $ben_details->ben_fname . ' ' . $ben_details->ben_mname . ' ' . $ben_details->ben_lname,
          'id' => $ben_details->id,
          'mobile_no' => $ben_details->mobile_no,
          'aadhar_no' => $ben_details->aadhar_no,
          'bank_code' => trim($ben_details->bank_code),
          'bank_ifsc' => trim($ben_details->bank_ifsc),
          'branch_name' => trim($ben_details->branch_name),
          'bank_name' => trim($ben_details->bank_name),
          'dup_bank' => trim($ben_details->dup_bank),
          'no_aadhar' => trim($ben_details->no_aadhar),
          'no_mobile' => trim($ben_details->no_mobile),
          'dup_mobile' => trim($ben_details->dup_mobile),
          'dup_aadhar' => trim($ben_details->dup_aadhar),
          'is_bank_failed' => trim($ben_details->is_bank_failed),
          'no_ration_card' => trim($ben_details->no_ration_card),
          'no_epic_voter' => trim($ben_details->no_epic_voter),
          'is_incomplete' => trim($ben_details->is_incomplete),
          'scheme_id' => $scheme_id,
          'caste' => trim($ben_details->caste),
          'caste_certificate_no' => trim($ben_details->caste_certificate_no),
          'husband_name' => trim($ben_details->husband_fname) . '' . trim($ben_details->husband_mname) . '' . trim($ben_details->husband_lname),
          'type_disability' => trim($ben_details->type_disability),
          'percentage_disability' => trim($ben_details->percentage_disability),
          'certifying_auth' => trim($ben_details->certifying_auth),
          'disability_designation' => trim($ben_details->disability_designation),
          'ration_card' => trim($ben_details->ration_card_cat) . ' ' . trim($ben_details->ration_card_no),
          'epic_voter_id' => trim($ben_details->epic_voter_id),
          'status_array' => $status_array



        ];
        // dd($response);
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
    // dd($request->all());
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
    $distCode = NULL;
    $user_id = AuthChecker::getUserId();
    ;


    $dutyObj = Configduty::where('user_id', '=', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
    $distCode = $dutyObj->district_code;


    if (AuthChecker::ApproverPermission() || AuthChecker::VerifierPermission()) {
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
    $docs_uploaded = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('created_by_dist_code', $distCode)->where('document_type', $doc_type)->where('beneficiary_id', $application_id)->select('id', 'beneficiary_id', 'document_type', 'doc_type_name', 'attched_document', 'document_extension', 'document_mime_type')->first();
    // dd($docs_uploaded);
    if (empty($docs_uploaded->attched_document)) {
      $return_status = 0;
      $return_msg = 'No Document Uploaded previuosly.';
      return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
    }
    $file_extension = $docs_uploaded->document_extension;
    $file_content = $docs_uploaded->attched_document;
    $mime_type = $docs_uploaded->document_mime_type;
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
    $auth = AuthChecker::ApproverPermission();
    if ($auth) {
      $response = [];
      $statusCode = 200;
      if (!$request->ajax()) {
        $statusCode = 400;
        $response = ['error' => 'Error occured in form submit.'];
        return response()->json($response, $statusCode);
      }
      // dd($request->all());
      $user_id = AuthChecker::getUserId();
      $is_bulk = $request->is_bulk;
      $applicant_id = $request->applicantId;
      // dd($applicant_id);
      $opreation_type = $request->opreation_type;
      $failed_type = $request->failed_type_id;
      $revart_remarks = $request->accept_reject_comments;
      $c_time = date('Y-m-d H:i:s');
      $dutyObj = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
      $distCode = $dutyObj->district_code;

      if ($is_bulk == 0) {
        $single_app_id = $request->single_app_id;
        $parts = explode('_', $single_app_id);
        $id = $parts[0];
        $scheme_id = $parts[1];
        if ($opreation_type == 'A') {
          try {
            $benDetails = BenEntry::where('scheme_id', $scheme_id)->where('id', $id)->where('next_level_clean_id', 2)->whereIn('is_clean', [1, 2])->first();
            $benRejectDetails = BenRejectRequest::where('ben_id', $id)->where('scheme_id', $scheme_id)->where('next_level_clean_id', 2)->first();
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

              // dd($request->opreation_type);

              $updateArray = $this->getUpdateCode($benDetails, $request, $role = 2);
              // $updateBenDetailsTable['']

              DB::connection('pgsql')->beginTransaction();
              DB::connection('pgsql_paywrite')->beginTransaction();
              $benEntry_Model = BenEntry::where('scheme_id', $scheme_id)->where('id', $id)->whereIn('is_clean', [1, 2])->where('next_level_clean_id', 2)->first();
              if ($benRejectDetails) {
                $benEntry_Model->next_level_clean_id = 1;
                $benEntry_Model->next_level_role_id = -1;
                $benEntry_Model->is_rejected = 1;
                $benEntry_Model->is_clean = 10;

              } else {


                $benEntry_Model->next_level_clean_id = 1;
                $benEntry_Model->is_incomplete = 0;
                $benEntry_Model->dup_bank = 0;
                $benEntry_Model->dup_mobile = 0;
                $benEntry_Model->dup_aadhar = 0;
                $benEntry_Model->no_aadhar = 0;
                $benEntry_Model->no_mobile = 0;
                if ($benEntry_Model->is_bank_failed = 1 && ($benEntry_Model->pay_validated == 3 || $benEntry_Model->pay_validated == 4 || $benEntry_Model->pay_validated == 5)) {

                  $benEntry_Model->is_bank_failed = 0;
                  $benEntry_Model->pay_validated = 0;
                }


              }
              $is_ben_update = $benEntry_Model->save();


              // $is_update_ben_details = DB::connection('pgsql')->table('public.ben_accept_reject_info')->insert($updateBenDetailsTable);

              foreach ($updateArray as $updatecode) {
                $accept_reject_info_model = new AcceptRejectInfo;
                $accept_reject_info_model->scheme_id = $scheme_id;
                $accept_reject_info_model->created_by_dist_code = $distCode;
                $accept_reject_info_model->created_at = date('Y-m-d H:i:s');
                $accept_reject_info_model->updated_at = date('Y-m-d H:i:s');
                $accept_reject_info_model->op_type = $updatecode->code;
                $accept_reject_info_model->ip_address = request()->ip();
                $accept_reject_info_model->user_id = $user_id;
                $accept_reject_info_model->application_id = $id;
                $accept_reject_info_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
                $accept_reject_info_model->remarks = $updatecode->description;
                $accept_reject_info_model->reason = $revart_remarks;
                $is_update_ben_details = $accept_reject_info_model->save();

              }

              if ($benRejectDetails) {
                $ben_Payment_Details = BenPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->first();
                // $ben_Payment_Details->dup_bank = 0;
                // $ben_Payment_Details->ben_status = 0;
                // $ben_Payment_Details->acc_validated = 0;
                $ben_Payment_Details->rejected_at = date('Y-m-d H:i:s');
                $ben_Payment_Details->is_rejected = 1;
                $ben_Payment_Details->is_eligible = false;
                $is_update_ben_payment = $ben_Payment_Details->save();


                $ben_dup_bank_details = BenDupBankCodePayemntDetails::where('id', $id)->where('scheme_id', $scheme_id)->first();
                if ($ben_dup_bank_details) {
                  // $ben_dup_bank_details->revert_remarks = $revart_remarks;
                  $ben_dup_bank_details->is_approved = 2;
                  $ben_dup_bank_details->next_level_role_id = -200;
                  $ben_dup_bank_details->rejected_date = date('Y-m-d H:i:s');
                  $ben_dup_bank_details->rejected_by = Auth::user()->id;
                  $is_ben_dup_bank_update = $ben_dup_bank_details->save();
                } else {
                  $is_ben_dup_bank_update = 1;
                }

              } else {
                if ($benDetails->dup_bank == 1) {

                  $ben_Payment_Details = BenPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->first();
                  $ben_Payment_Details->dup_bank = 0;
                  $ben_Payment_Details->ben_status = 1;
                  $ben_Payment_Details->acc_validated = 0;
                  $ben_Payment_Details->legacy_validation=0;
                  $is_update_ben_payment = $ben_Payment_Details->save();

                  $ben_dup_bank_details = BenDupBankCodePayemntDetails::where('id', $id)->where('scheme_id', $scheme_id)->first();
                  if ($ben_dup_bank_details) {
                    $ben_dup_bank_details->revert_remarks = $revart_remarks;
                    $ben_dup_bank_details->is_approved = 1;
                    $is_ben_dup_bank_update = $ben_dup_bank_details->save();
                  } else {
                    $is_ben_dup_bank_update = 1;
                  }
                } else {
                  $is_update_ben_payment = 1;
                }
              }

              // $is_ben_dup_bank_update = 1;
              if ($benRejectDetails) {
                $is_update_ben_payment = 1;
              } else {
                if ($benDetails->is_bank_failed == 1 || $benDetails->is_bank_failed == 2 || $benDetails->is_bank_failed == 3) {
                  $ben_Payment_Details = BenPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->first();
                  $ben_Payment_Details->dup_bank = 0;
                  $ben_Payment_Details->ben_status = 1;
                  $ben_Payment_Details->acc_validated = 0;
                  $ben_Payment_Details->legacy_validation=0;
                  $is_update_ben_payment = $ben_Payment_Details->save();
                } else {
                  $is_update_ben_payment = 1;
                }

              }



              if (in_array($benDetails->is_bank_failed, [1,2, 3])) {
                $ben_failed_payment_details = BenFailedPaymentDetails::where('ben_id', $id)->where('scheme_id', $scheme_id)->first();
                if(in_array($benDetails->is_bank_failed, [2, 3])){
                  $ben_failed_payment_details->edited_status = 2;
                }
                $is_update_failed_payment = $ben_failed_payment_details->save();
              } else {
                $is_update_failed_payment = 1;
              }

              if ($benRejectDetails) {
                $is_final_update = 1;
                $benRejectDetails->approved_by = Auth::user()->id;
                $benRejectDetails->approved_date = date('Y-m-d H:i:s');
                $benRejectDetails->approved_ip = request()->ip();
                $benRejectDetails->next_level_clean_id = 1;
                $updateBenRejectLog = $benRejectDetails->save();
              } else {
                if (in_array($benDetails->is_bank_failed, [1])) {
                  $failed_type_id = $benDetails->pay_validated;
                  if (!empty($failed_type_id)) {
                    $is_final_update = DB::connection('pgsql_paywrite')->select("Select payment.failed_update_bank(in_ben_id => ARRAY[" . $id . "], in_scheme_id => " . $scheme_id . ", in_failed_type_id => " . $failed_type_id . ")");
                  }
                } else {
                  $is_final_update = 1;
                }
                $updateBenRejectLog = 1;
              }



              // dd($is_final_update & $is_ben_update && $is_update_ben_details && $is_update_ben_payment && $is_update_failed_payment);
              // dump($is_final_update && $is_ben_update && $is_update_ben_details && $is_update_ben_payment && $is_update_failed_payment);
              // die;
              // dump($is_final_update , $is_ben_update , $is_update_ben_details , $is_update_ben_payment , $is_update_failed_payment , $is_ben_dup_bank_update , $updateBenRejectLog);
              // die;

              // if($id== 4815832){
              //   dump($is_final_update);
              //   dump($is_ben_update);
              //   dump($is_update_ben_details);
              //   dump($is_update_ben_payment);
              //   dump($is_update_failed_payment);
              //   dump($is_ben_dup_bank_update);
              //   dump($updateBenRejectLog);
              //   die();
              // }
              if ($is_final_update && $is_ben_update && $is_update_ben_details && $is_update_ben_payment && $is_update_failed_payment && $is_ben_dup_bank_update && $updateBenRejectLog) {
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
            $benDetails = BenEntry::where('scheme_id', $scheme_id)->where('id', $id)->where('next_level_clean_id', 2)->where('is_clean', 1)->first();
            $benRejectDetails = BenRejectRequest::where('ben_id', $id)->where('scheme_id', $scheme_id)->where('next_level_clean_id', 2)->first();

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


              $updateArray = $this->getUpdateCode($benDetails, $request, $role = 2);
              $benEntry_Model = BenEntry::where('scheme_id', $scheme_id)->where('id', $id)->where('is_clean', 1)->where('next_level_clean_id', 2)->first();
              $benEntry_Model->next_level_clean_id = null;
              $benEntry_Model->is_incomplete = 1;
              $is_ben_update = $benEntry_Model->save();


              // $updateBenTable = [];
              // $updateBenDetailsTable = [];
              // $updateBenTable['next_level_clean_id'] = null;
              // $updateBenTable['is_incomplete'] = 1;
              // $updateBenDetailsTable['remarks'] = $request->accept_reject_comments;
              // $updateBenDetailsTable['op_type'] = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod() . '@' . 'T';
              $updateArray = $this->getUpdateCode($benDetails, $request, $role = 2);

              foreach ($updateArray as $updatecode) {
                $accept_reject_info_model = new AcceptRejectInfo;
                $accept_reject_info_model->scheme_id = $scheme_id;
                $accept_reject_info_model->created_by_dist_code = $distCode;
                $accept_reject_info_model->created_at = date('Y-m-d H:i:s');
                $accept_reject_info_model->updated_at = date('Y-m-d H:i:s');
                $accept_reject_info_model->op_type = $updatecode->code;
                $accept_reject_info_model->ip_address = request()->ip();
                $accept_reject_info_model->user_id = $user_id;
                $accept_reject_info_model->application_id = $id;
                $accept_reject_info_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
                $accept_reject_info_model->remarks = $updatecode->description;
                $is_update_ben_details = $accept_reject_info_model->save();

              }

              $ben_dup_bank_details = BenDupBankCodePayemntDetails::where('id', $id)->where('scheme_id', $scheme_id)->first();
              if ($ben_dup_bank_details) {
                $ben_dup_bank_details->revert_remarks = $revart_remarks;
                $ben_dup_bank_details->is_approved = 0;
                $ben_dup_bank_details->next_level_role_id = -97;
                $is_ben_dup_bank_update = $ben_dup_bank_details->save();
              } else {
                $is_ben_dup_bank_update = 1;
              }

              DB::connection('pgsql')->beginTransaction();
              $is_ben_update = $benEntry_Model->save();

              $is_update_ben_details = $accept_reject_info_model->save();
              // $is_update_ben_details = DB::connection('pgsql')->table('public.ben_accept_reject_info')->where('original_application_id', $id)->where('scheme_id', $scheme_id)->where('update_code', 500)->where('is_clean', 2)->update($updateBenDetailsTable);
              $is_reject_log = BenRejectRequest::where('ben_id', $id)->where('scheme_id', $scheme_id)->delete();

              if ($is_ben_update) {
                if ($is_update_ben_details && $is_ben_dup_bank_update && $is_reject_log) {
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


              $benDetails = BenEntry::where('scheme_id', $scheme_id)->where('id', $value)->where('next_level_clean_id', 2)->whereIn('is_clean', [1, 2])->first();
              $benRejectDetails = BenRejectRequest::where('ben_id', $value)->where('scheme_id', $scheme_id)->where('next_level_clean_id', 2)->first();

              if ($benDetails == null) {
                return $response = [
                  'status' => 1,
                  'msg' => 'No Beneficiary Found.',
                  'type' => 'red',
                  'icon' => 'fa fa-warning',
                  'title' => 'Warning!!',
                ];
              } else {

                $updateArray = $this->getUpdateCode($benDetails, $request, $role = 2);


                $benEntry_Model = BenEntry::where('scheme_id', $scheme_id)->where('id', $value)->whereIn('is_clean', [1, 2])->where('next_level_clean_id', 2)->first();
                if ($benRejectDetails) {
                  $benEntry_Model->next_level_clean_id = 1;
                  $benEntry_Model->next_lvel_role_id = -1;
                  $benEntry_Model->is_rejected = 1;
                  $benEntry_Model->is_clean = 10;

                } else {
                  $benEntry_Model->next_level_clean_id = 1;
                  $benEntry_Model->is_incomplete = 0;
                  $benEntry_Model->dup_bank = 0;
                  $benEntry_Model->dup_mobile = 0;
                  $benEntry_Model->dup_aadhar = 0;
                  $benEntry_Model->no_aadhar = 0;
                  $benEntry_Model->no_mobile = 0;
                  if ($benEntry_Model->is_bank_failed = 1 && ($benEntry_Model->pay_validated == 3 || $benEntry_Model->pay_validated == 4 || $benEntry_Model->pay_validated == 5)) {
                    $benEntry_Model->is_bank_failed = 0;
                    $benEntry_Model->pay_validated = 0;
                  }
                }



                $is_ben_update = $benEntry_Model->save();

                // $updateBenPaymentTable['dup_bank'] = 0;
                // $updateBenPaymentTable['ben_status'] = 1;
                // $updateBenPaymentTable['acc_validated'] = 0;
                foreach ($updateArray as $updatecode) {
                  $accept_reject_info_model = new AcceptRejectInfo;
                  $accept_reject_info_model->scheme_id = $scheme_id;
                  $accept_reject_info_model->created_by_dist_code = $distCode;
                  $accept_reject_info_model->created_at = date('Y-m-d H:i:s');
                  $accept_reject_info_model->updated_at = date('Y-m-d H:i:s');
                  $accept_reject_info_model->op_type = $updatecode->code;
                  $accept_reject_info_model->ip_address = request()->ip();
                  $accept_reject_info_model->user_id = $user_id;
                  $accept_reject_info_model->application_id = $value;
                  $accept_reject_info_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
                  $accept_reject_info_model->remarks = $updatecode->description;
                  $is_update_ben_details = $accept_reject_info_model->save();

                }
                ;

                if ($benRejectDetails) {
                  $ben_Payment_Details = BenPaymentDetails::where('ben_id', $value)->where('scheme_id', $scheme_id)->first();
                  $ben_Payment_Details->dup_bank = 0;
                  $ben_Payment_Details->ben_status = 0;
                  $ben_Payment_Details->acc_validated = 0;
                  $ben_Payment_Details->rejected_at = date('Y-m-d H:i:s');
                  $ben_Payment_Details->is_rejected = 1;
                  $ben_Payment_Details->is_eligible = false;
                  $is_update_ben_payment = $ben_Payment_Details->save();


                  $ben_dup_bank_details = BenDupBankCodePayemntDetails::where('id', $value)->where('scheme_id', $scheme_id)->first();
                  if ($ben_dup_bank_details) {
                    $ben_dup_bank_details->revert_remarks = $revart_remarks;
                    $ben_dup_bank_details->is_approved = 2;
                    $ben_dup_bank_details->rejected_date = date('Y-m-d H:i:s');
                    $ben_dup_bank_details->rejected_by = Auth::user()->id;
                    $is_ben_dup_bank_update = $ben_dup_bank_details->save();
                  } else {
                    $is_ben_dup_bank_update = 1;
                  }
                } else {
                  if ($benDetails->dup_bank == 1) {

                    $ben_Payment_Details = BenPaymentDetails::where('ben_id', $value)->where('scheme_id', $scheme_id)->first();
                    $ben_Payment_Details->dup_bank = 0;
                    $ben_Payment_Details->ben_status = 1;
                    $ben_Payment_Details->acc_validated = 0;
                    $is_update_ben_payment = $ben_Payment_Details->save();

                    $ben_dup_bank_details = BenDupBankCodePayemntDetails::where('bank_id', $id)->where('scheme_id', $scheme_id)->first();
                    if ($ben_dup_bank_details) {
                      $ben_dup_bank_details->revert_remarks = $revart_remarks;
                      $ben_dup_bank_details->is_approved = 1;
                      $is_ben_dup_bank_update = $ben_dup_bank_details->save();
                    } else {
                      $is_ben_dup_bank_update = 1;
                    }
                  } else {
                    $is_update_ben_payment = 1;
                  }
                }



                if ($benDetails->is_bank_failed == 1 || $benDetails->is_bank_failed == 2 || $benDetails->is_bank_failed == 3) {

                  $ben_Payment_Details = BenPaymentDetails::where('ben_id', $value)->where('scheme_id', $scheme_id)->first();
                  $ben_Payment_Details->dup_bank = 0;
                  $ben_Payment_Details->ben_status = 1;
                  $ben_Payment_Details->acc_validated = 0;
                  $is_update_ben_payment = $ben_Payment_Details->save();
                } else {
                  $is_update_ben_payment = 1;
                }


                if (in_array($benDetails->is_bank_failed, [1, 2, 3])) {
                  $ben_failed_payment_details = BenFailedPaymentDetails::where('ben_id', $value)->where('scheme_id', $scheme_id)->first();
                  $ben_failed_payment_details->edited_status = 2;
                  $is_update_failed_payment = $ben_failed_payment_details->save();
                } else {
                  $is_update_failed_payment = 1;
                }
                // DB::connection('pgsql')->beginTransaction();
                // $is_update_ben_details = DB::connection('pgsql')->table('public.ben_accept_reject_info')->where('original_application_id', $value)->where('scheme_id', $scheme_id)->where('update_code', 500)->where('is_clean', 2)->update($updateBenDetailsTable);

                // $is_update_ben_payment = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id', $value)->where('scheme_id', $scheme_id)->update($updateBenPaymentTable);
                // $is_update_failed_payment = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')->where('ben_id', $value)->where('scheme_id', $scheme_id)->update($updateFailedPaymentTable);
                // dump($is_ben_update); die; //dump($is_update_ben_details); die;

                if ($benRejectDetails) {
                  $is_final_update = 1;
                } else {
                  if (in_array($benDetails->is_bank_failed, [1])) {
                    $failed_type_id = $benDetails->pay_validated;
                    if (!empty($failed_type_id)) {
                      $is_final_update = DB::connection('pgsql_paywrite')->select("Select payment.failed_update_bank(in_ben_id => ARRAY[" . $value . "], in_scheme_id => " . $scheme_id . ", in_failed_type_id => " . $failed_type_id . ")");
                    }
                  } else {
                    $is_final_update = 1;
                  }
                }



                if ($is_final_update) {
                  $i++;
                }
              }
            }

            // dump($i == $count);
            // die();
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




  public function getNoDupListExcel(Request $request)
  {
    // dd($request->all());

    $scheme_id = $request->excel_scheme_id;
    $filter_type = $request->excel_filter_id;

    if (empty($scheme_id) || !ctype_digit($scheme_id)) {
      return redirect()->route('noDupBeneficiariesList')->with('error', 'Select the Scheme')->withInput();
    }
    if (!isset($filter_type) || $filter_type === '') {
      return redirect()->route('noDupBeneficiariesList')
        ->with('error', 'Select the Operation Filter Type')
        ->withInput();
    }

    $user_id = AuthChecker::getUserId();
    $duty_obj = Configduty::where('user_id', $user_id)->first();
    $district_code = $duty_obj->district_code;

    $mapping_level = $duty_obj->mapping_level;

    $urban_body_code = null;

    if ($mapping_level == 'Block' || $mapping_level == 'Subdiv') {
      if ($mapping_level == 'Block') {
        $urban_body_code = $duty_obj->taluka_code;
      }
      if ($mapping_level == 'Subdiv') {
        $urban_body_code = $duty_obj->urban_body_code;
      }
    }

    $query = DB::connection('pgsql')->table('pension.beneficiaries')->where('scheme_id', $scheme_id)->where('is_rejected', 0);
    // Apply filtering based on mapping level
    if ($mapping_level == 'Block' || $mapping_level == 'Subdiv') {
      $query->where('created_by_local_body_code', $urban_body_code)
        ->where('created_by_dist_code', $district_code);
    }

    // Apply filter types
    switch ($filter_type) {
      case 0:
        $query->where('is_incomplete', 1);
        break;
      case 1:
        $query->where('dup_aadhar', 1);
        break;
      case 2:
        $query->where('no_aadhar', 1);
        break;
      case 3:
        $query->where('dup_bank', 1);
        break;
      case 4:
        $query->where('dup_mobile', 1);
        break;
      case 5:
        $query->where('no_mobile', 1);
        break;
      case 10:
        $query->where('is_bank_failed', 1);
        break;
      case 11:
        $query->where('is_bank_failed', 2);
        break;
      case 12:
        $query->where('is_bank_failed', 3);
        break;
    }

    if ($request->gp_ward) {
      $query->where('gp_ward_code', $request->gp_ward);
    }

    if ($request->muncid) {
      $query->where('muncid_code', $request->muncid);
    }

    $data = $query->orderBy('id')->get(); // Fix: Ensure data is retrieved with ->get()

    $incomplete_arr = [];
    foreach ($data as $item) {
      $statuses = [];
      if ($item->is_incomplete == 1)
        $statuses[] = 'Incomplete Data';
      if ($item->dup_aadhar == 1)
        $statuses[] = 'Duplicate Aadhar';
      if ($item->no_aadhar == 1)
        $statuses[] = 'No Aadhar No.';
      if ($item->dup_bank == 1)
        $statuses[] = 'Duplicate Bank';
      if ($item->dup_mobile == 1)
        $statuses[] = 'Duplicate Mobile';
      if ($item->no_mobile == 1)
        $statuses[] = 'No Mobile No.';
      if ($item->is_bank_failed == 1)
        $statuses[] = 'Payment Failure';
      if ($item->is_bank_failed == 2)
        $statuses[] = 'Name Validation Failure';
      if ($item->is_bank_failed == 3)
        $statuses[] = 'Account Validation Failure';

      $mask_aadhar = strlen($item->aadhar_no) == 12 ? '********' . substr($item->aadhar_no, 8) : $item->aadhar_no;

      $incomplete_arr[] = [
        'Beneficiary Id' => $item->id,
        'Beneficiary Name' => trim("{$item->ben_fname} {$item->ben_mname} {$item->ben_lname}"),
        'Block/Municipality' => trim($item->block_ulb_name),
        'GP/Ward' => trim($item->gp_ward_name),
        'Aadhar No' => $mask_aadhar,
        'Bank A/c' => $item->bank_code,
        'Bank IFSC' => $item->bank_ifsc,
        'Mobile No' => $item->mobile_no,
        'Incomplete Status' => implode(', ', $statuses)
      ];
    }

    $fileName = 'Incomplete_Beneficiary_data_' . date('d_m_Y') . '_' . time() . '.xlsx';
    // Generate and download the Excel file
    return Excel::create($fileName, function ($excel) use ($incomplete_arr) {
      $excel->setTitle('Incomplete_Beneficiary_data');
      $excel->sheet('Incomplete_Beneficiary_data', function ($sheet) use ($incomplete_arr) {
        $sheet->fromArray($incomplete_arr, null, 'A1', false, false);
      });
    })->download('xlsx');

  }


  public function TotalCountExcel(Request $request)
  {

    $user_id = AuthChecker::getUserId();
    $duty_obj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
    $dist_code = $duty_obj ? $duty_obj->district_code : null;
    $schemes = Scheme::where('is_active', 1)->orderBy('id')->get();
    $scheme_id = $request->excel_scheme_id;
    $filter_type = $request->excel_filter_type;

    // Check the value of the submit field
    if ($request->input('submit') == 'excel_total_count_btn') {
      // Prepare data with headers as the first row
      $scheme_data = [];
      $scheme_data[] = [ // Headers for the Excel file
        'Scheme Name',
        'Beneficiaries with No Aadhar Number',
        'Beneficiaries with Bank Failure',
        'Beneficiaries with No Mobile Number',
        'Beneficiaries with Duplicate Mobile Numbers',
        'Beneficiaries With Duplicate Bank Account Numbers',
        'Beneficiaries with Duplicate Aadhar Numbers',
        'Beneficiaries with Incomplete Details'
      ];

      foreach ($schemes as $scheme) {
        $scheme_data[] = [
          trim($scheme->scheme_name),
          BenEntry::where('scheme_id', $scheme->id)
            ->where('dist_code', $dist_code)
            ->where('no_aadhar', 1)
            ->where('is_rejected', 0)
            ->count(),
          BenEntry::where('scheme_id', $scheme->id)
            ->where('dist_code', $dist_code)
            ->where('is_bank_failed', 1)
            ->where('is_rejected', 0)
            ->count(),
          BenEntry::where('scheme_id', $scheme->id)
            ->where('dist_code', $dist_code)
            ->where('no_mobile', 1)
            ->where('is_rejected', 0)
            ->count(),
          BenEntry::where('scheme_id', $scheme->id)
            ->where('dist_code', $dist_code)
            ->where('dup_mobile', 1)
            ->where('is_rejected', 0)
            ->count(),
          BenEntry::where('scheme_id', $scheme->id)
            ->where('dist_code', $dist_code)
            ->where('dup_bank', 1)
            ->where('is_rejected', 0)
            ->count(),
          BenEntry::where('scheme_id', $scheme->id)
            ->where('dist_code', $dist_code)
            ->where('dup_aadhar', 1)
            ->where('is_rejected', 0)
            ->count(),
          BenEntry::where('scheme_id', $scheme->id)
            ->where('dist_code', $dist_code)
            ->where('is_incomplete', 1)
            ->where('is_rejected', 0)
            ->count(),
        ];
      }

      $fileName = 'Incomplete_data_beneficiary_Count_' . date('d/m/Y') . '_' . time() . '.xlsx';
      Excel::create($fileName, function ($excel) use ($scheme_data) {
        $excel->setTitle('Beneficiary Count Report');
        $excel->sheet('Beneficiary Count Report', function ($sheet) use ($scheme_data) {
          $sheet->fromArray($scheme_data, null, 'A1', false, false);
        });
      })->download('xlsx');
    } elseif ($request->input('submit') == 'excel_scheme_based_btn') {
      $scheme_id = $request->excel_scheme_id;
      $sub_divisions = SubDistrict::where('district_code', $dist_code)->select('sub_district_code', 'sub_district_name')->get();
      $blocks = Taluka::where('district_code', $dist_code)->select('block_code', 'block_name')->get();

      $local_body = [];

      // Populate the local_body array with sub-divisions and blocks
      foreach ($sub_divisions as $sub_div) {
        $local_body[] = [
          'code' => $sub_div->sub_district_code,
          'name' => $sub_div->sub_district_name,
        ];
      }

      foreach ($blocks as $block) {
        $local_body[] = [
          'code' => $block->block_code,
          'name' => $block->block_name,
        ];
      }

      // Initialize $scheme_data with the headers
      $scheme_data = [
        [
          'Municipality/Block',
          'Beneficiaries with No Aadhar Number',
          'Beneficiaries with Bank Failure',
          'Beneficiaries with No Mobile Number',
          'Beneficiaries with Duplicate Mobile Numbers',
          'Beneficiaries With Duplicate Bank Account Numbers',
          'Beneficiaries with Duplicate Aadhar Numbers',
          'Beneficiaries with Incomplete Details'
        ]
      ];

      // Loop through the local_body array and populate data
      foreach ($local_body as $local_b) {
        $scheme_data[] = [
          $local_b['name'],
          BenEntry::where('scheme_id', $scheme_id)
            ->where('dist_code', $dist_code)
            ->where('created_by_local_body_code', $local_b['code'])
            ->where('no_aadhar', 1)
            ->where('is_rejected', 0)
            ->count(),
          BenEntry::where('scheme_id', $scheme_id)
            ->where('dist_code', $dist_code)
            ->where('created_by_local_body_code', $local_b['code'])
            ->where('is_bank_failed', 1)
            ->where('is_rejected', 0)
            ->count(),
          BenEntry::where('scheme_id', $scheme_id)
            ->where('dist_code', $dist_code)
            ->where('created_by_local_body_code', $local_b['code'])
            ->where('no_mobile', 1)
            ->where('is_rejected', 0)
            ->count(),
          BenEntry::where('scheme_id', $scheme_id)
            ->where('dist_code', $dist_code)
            ->where('created_by_local_body_code', $local_b['code'])
            ->where('dup_mobile', 1)
            ->where('is_rejected', 0)
            ->count(),
          BenEntry::where('scheme_id', $scheme_id)
            ->where('dist_code', $dist_code)
            ->where('created_by_local_body_code', $local_b['code'])
            ->where('dup_bank', 1)
            ->where('is_rejected', 0)
            ->count(),
          BenEntry::where('scheme_id', $scheme_id)
            ->where('dist_code', $dist_code)
            ->where('created_by_local_body_code', $local_b['code'])
            ->where('dup_aadhar', 1)
            ->where('is_rejected', 0)
            ->count(),
          BenEntry::where('scheme_id', $scheme_id)
            ->where('dist_code', $dist_code)
            ->where('created_by_local_body_code', $local_b['code'])
            ->where('is_incomplete', 1)
            ->where('is_rejected', 0)
            ->count(),
        ];
      }

      // Generate the Excel file and download
      $fileName = 'Incomplete_data_beneficiary_Count_' . date('d/m/Y') . '_' . time() . '.xlsx';
      Excel::create($fileName, function ($excel) use ($scheme_data) {
        $excel->setTitle('Beneficiary Count Report');
        $excel->sheet('Beneficiary Count Report', function ($sheet) use ($scheme_data) {
          $sheet->fromArray($scheme_data, null, 'A1', false, false);
        });
      })->download('xlsx');




    } elseif ($request->input('submit') == 'excel_another_total_btn') {
      // dd($request->all());
      $scheme_id = $request->excel_scheme_id;
      $filter_type = $request->excel_filter_type;
      $mapping_level = $duty_obj->mapping_level;
      $query = DB::table('pension.beneficiaries')->where('scheme_id', $scheme_id);
      $query->where('created_by_dist_code', $dist_code)->where('next_level_clean_id', 2)->where('is_rejected', 0);

      switch ($filter_type) {
        case 0:
          $query->where('is_incomplete', 1);
          break;
        case 1:
          $query->where('dup_aadhar', 1);
          break;
        case 2:
          $query->where('no_aadhar', 1);
          break;
        case 3:
          $query->where('dup_bank', 1);
          break;
        case 4:
          $query->where('dup_mobile', 1);
          break;
        case 5:
          $query->where('no_mobile', 1);
          break;
        case 6:
          $query->where('is_bank_failed', 1);
          break;
      }

      if ($request->gp_ward) {
        $query->where('gp_ward_code', $request->gp_ward);
      }

      if ($request->muncid) {
        $query->where('gp_ward_code', $request->muncid);
      }

      $data = $query->orderBy('id')->get();

      $incomplete_arr = [];

      // Define the headers for the Excel file
      $headers = [
        'Beneficiary Id',
        'Beneficiary Name',
        'Block/Municipality',
        'GP/Ward',
        'Aadhar No',
        'Bank A/c',
        'Bank IFSC',
        'Mobile No',
        'Incomplete Status'
      ];

      // Populate the data array
      foreach ($data as $item) {
        $statuses = [];
        if ($item->is_incomplete == 1)
          $statuses[] = 'Incomplete Data';
        if ($item->dup_aadhar == 1)
          $statuses[] = 'Duplicate Aadhar';
        if ($item->no_aadhar == 1)
          $statuses[] = 'No Aadhar No.';
        if ($item->dup_bank == 1)
          $statuses[] = 'Duplicate Bank';
        if ($item->dup_mobile == 1)
          $statuses[] = 'Duplicate Mobile';
        if ($item->no_mobile == 1)
          $statuses[] = 'No Mobile No.';
        if ($item->is_bank_failed == 1)
          $statuses[] = 'Payment Failure';

        // Masking Aadhar number if it is of length 12
        $mask_aadhar = strlen($item->aadhar_no) == 12 ? '********' . substr($item->aadhar_no, 8) : $item->aadhar_no;

        $incomplete_arr[] = [
          'Beneficiary Id' => $item->id,
          'Beneficiary Name' => trim("{$item->ben_fname} {$item->ben_mname} {$item->ben_lname}"),
          'Block/Municipality' => trim($item->block_ulb_name),
          'GP/Ward' => trim($item->gp_ward_name),
          'Aadhar No' => $mask_aadhar,
          'Bank A/c' => $item->bank_code,
          'Bank IFSC' => $item->bank_ifsc,
          'Mobile No' => $item->mobile_no,
          'Incomplete Status' => implode(', ', $statuses)
        ];
      }

      // File name with current date and time
      $fileName = 'Incomplete_data_beneficiary_Count_' . date('d/m/Y') . '_' . time() . '.xlsx';

      // Create and download the Excel file
      Excel::create($fileName, function ($excel) use ($headers, $incomplete_arr) {
        $excel->setTitle('Beneficiary Count Report');
        $excel->sheet('Beneficiary Count Report', function ($sheet) use ($headers, $incomplete_arr) {
          // Insert the header
          $sheet->row(1, $headers);

          // Insert the data
          $sheet->fromArray($incomplete_arr, null, 'A2', false, false);
        });
      })->download('xlsx');

    }




  }


  public function blkUlbMisReport(Request $request)
  {
    $user_id = AuthChecker::getUserId();
    $duty_obj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
    $district_code = $duty_obj->district_code;

    $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . " )"));


    return view('No-Duplicate-Update/blkulb_mis_report', [
      'schemes' => $schemes,
      'dist_code' => $district_code,
    ]);
  }



  public function blkUlbMisReportPost(Request $request)
  {
    $district_code = $request->dist_code;
    $scheme_id = $request->scheme_id;
    $rural_urban_code = $request->rural_urban_code;



    $sub_divisions = SubDistrict::where('district_code', $district_code)->select('sub_district_code', 'sub_district_name')->get();
    $blocks = Taluka::where('district_code', $district_code)->select('block_code', 'block_name')->get();

    $local_body = [];

    // Populate the local_body array with sub-divisions and blocks
    foreach ($sub_divisions as $sub_div) {
      $local_body[] = [
        'code' => $sub_div->sub_district_code,
        'name' => $sub_div->sub_district_name,
      ];
    }

    foreach ($blocks as $block) {
      $local_body[] = [
        'code' => $block->block_code,
        'name' => $block->block_name,
      ];
    }

    if (!empty($request->blk_ulb_code)) {
      $blk_ulb_code = $request->blk_ulb_code;
    }
    if ($rural_urban_code != null && $blk_ulb_code != null) {
      $sub_divisions = SubDistrict::where('district_code', $district_code)->where('sub_district_code', $blk_ulb_code)->select('sub_district_code', 'sub_district_name')->get();
      $blocks = Taluka::where('district_code', $district_code)->where('block_code', $blk_ulb_code)->select('block_code', 'block_name')->get();

      $local_body = [];

      // Populate the local_body array with sub-divisions and blocks
      foreach ($sub_divisions as $sub_div) {
        $local_body[] = [
          'code' => $sub_div->sub_district_code,
          'name' => $sub_div->sub_district_name,
        ];
      }

      foreach ($blocks as $block) {
        $local_body[] = [
          'code' => $block->block_code,
          'name' => $block->block_name,
        ];
      }
      // dd($local_body);
    }



    // Loop through the local_body array and populate data
    foreach ($local_body as $local_b) {
      $scheme_data[] = [
        'blkUlb_name' => $local_b['name'],
        'no_aadhar' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('no_aadhar', 1)
          ->where('is_rejected', 0)
          ->count(),
        'bank_failure' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_bank_failed', 1)
          ->where('is_rejected', 0)
          ->count(),
        'no_mobile' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('no_mobile', 1)
          ->where('is_rejected', 0)
          ->count(),
        'dup_mobile' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('dup_mobile', 1)
          ->where('is_rejected', 0)
          ->count(),
        'dup_bank' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('dup_bank', 1)
          ->where('is_rejected', 0)
          ->count(),
        'dup_aadhar' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('dup_aadhar', 1)
          ->where('is_rejected', 0)
          ->count(),
        'incomplete_data' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_incomplete', 1)
          ->where('is_rejected', 0)
          ->count(),

        'name_faliure' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_bank_failed', 2)
          ->where('is_rejected', 0)
          ->count(),
        'account_faliure' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_bank_failed', 3)
          ->where('is_rejected', 0)
          ->count(),
      ];
    }
    // dd($scheme_data);
    return datatables()->of($scheme_data)
      ->addIndexColumn()
      ->make(true);

  }



  public function blkUlbMisReportExcel(Request $request)
  {
    // Extract request variables
    $district_code = $request->excel_dist_code;
    $scheme_id = $request->excel_scheme_id;
    $rural_urban_code = $request->excel_filter_1;

    if ($request->excel_filter_1) {
      return redirect()->route('blk_ulb_mis_report')->with('error', 'Select the Scheme')->withInput();
    }

    // Fetch Subdivisions and Blocks
    $sub_divisions = SubDistrict::where('district_code', $district_code)
      ->select('sub_district_code', 'sub_district_name')
      ->get();

    $blocks = Taluka::where('district_code', $district_code)
      ->select('block_code', 'block_name')
      ->get();

    // Combine Subdivisions and Blocks into a single array
    $local_body = [];
    foreach ($sub_divisions as $sub_div) {
      $local_body[] = [
        'code' => $sub_div->sub_district_code,
        'name' => $sub_div->sub_district_name,
      ];
    }

    foreach ($blocks as $block) {
      $local_body[] = [
        'code' => $block->block_code,
        'name' => $block->block_name,
      ];
    }

    // Filter by blk_ulb_code if provided
    if (!empty($request->excel_filter_2)) {
      $blk_ulb_code = $request->excel_filter_2;

      $sub_divisions = SubDistrict::where('district_code', $district_code)
        ->where('sub_district_code', $blk_ulb_code)
        ->select('sub_district_code', 'sub_district_name')
        ->get();

      $blocks = Taluka::where('district_code', $district_code)
        ->where('block_code', $blk_ulb_code)
        ->select('block_code', 'block_name')
        ->get();

      // Rebuild local_body array with filtered results
      $local_body = [];
      foreach ($sub_divisions as $sub_div) {
        $local_body[] = [
          'code' => $sub_div->sub_district_code,
          'name' => $sub_div->sub_district_name,
        ];
      }

      foreach ($blocks as $block) {
        $local_body[] = [
          'code' => $block->block_code,
          'name' => $block->block_name,
        ];
      }
    }

    // Prepare scheme data headers
    $scheme_data = [
      [
        'Sub-Division/Block',
        'Beneficiaries with Incomplete Details',
        'Beneficiaries with No Aadhar Number',
        'Beneficiaries with Duplicate Aadhar Numbers',
        'Beneficiaries with No Mobile Number',
        'Beneficiaries with Duplicate Mobile Numbers',
        'Beneficiaries with Duplicate Bank Account Numbers',
        'Beneficiaries with Bank Transaction Failure',
        'Beneficiaries with Bank Name Validation Failure',
        'Beneficiaries with Bank Account Validation Failure',
      ]
    ];

    foreach ($local_body as $local_b) {
      // Base query definition
      $baseQuery = BenEntry::where('scheme_id', $scheme_id)
        ->where('creat', $district_code)
        ->where('is_rejected', 0)
        ->where('created_by_local_body_code', $local_b['code']);

      $scheme_data[] = [
        $local_b['name'],
        // Count for is_incomplete
        (clone $baseQuery)->where('is_incomplete', 1)->count(),
        // Count for no_aadhar
        (clone $baseQuery)->where('no_aadhar', 1)->count(),
        // Count for dup_aadhar
        (clone $baseQuery)->where('dup_aadhar', 1)->count(),
        // Count for no_mobile
        (clone $baseQuery)->where('no_mobile', 1)->count(),
        // Count for dup_mobile
        (clone $baseQuery)->where('dup_mobile', 1)->count(),
        // Count for dup_bank
        (clone $baseQuery)->where('dup_bank', 1)->count(),
        // Count for is_bank_failed (1, 2, 3)
        (clone $baseQuery)->where('is_bank_failed', 1)->count(),
        (clone $baseQuery)->where('is_bank_failed', 2)->count(),
        (clone $baseQuery)->where('is_bank_failed', 3)->count(),
      ];
    }


    // dd($scheme_data);


    $fileName = 'Block_Sub_Division_Beneficiary_count_' . date('d/m/Y') . '_' . time() . '.xlsx';
    Excel::create($fileName, function ($excel) use ($scheme_data) {
      $excel->setTitle('Beneficiary Count Report');
      $excel->sheet('Beneficiary Count Report', function ($sheet) use ($scheme_data) {
        $sheet->fromArray($scheme_data, null, 'A1', false, false);
      });
    })->download('xlsx');






  }


  public function schemeMisReport(Request $request)
  {
    $user_id = AuthChecker::getUserId();
    $duty_obj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
    $district_code = $duty_obj->district_code;

    $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . " )"));

    return view('No-Duplicate-Update/scheme_mis_report', [
      'schemes' => $schemes,
      'dist_code' => $district_code,
    ]);
  }

  public function schemeMisReportPost(Request $request)
  {

    $user_id = AuthChecker::getUserId();
    $duty_obj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
    $dist_code = $duty_obj ? $duty_obj->district_code : null;
    $schemes = Scheme::where('is_active', 1)->orderBy('id')->get();
    foreach ($schemes as $scheme) {
      $scheme_data[] = [
        'scheme_name' => trim($scheme->scheme_name),
        'no_aadhar' => BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('no_aadhar', 1)
          ->where('is_rejected', 0)
          ->count(),
        'bank_failure' => BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('is_bank_failed', 1)
          ->where('is_rejected', 0)
          ->count(),
        'no_mobile' => BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('no_mobile', 1)
          ->where('is_rejected', 0)
          ->count(),
        'dup_mobile' => BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('dup_mobile', 1)
          ->where('is_rejected', 0)
          ->count(),
        'dup_bank' => BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('dup_bank', 1)
          ->where('is_rejected', 0)
          ->count(),
        'dup_aadhar' => BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('dup_aadhar', 1)
          ->where('is_rejected', 0)
          ->count(),
        'incomplete_data' => BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('is_incomplete', 1)
          ->where('is_rejected', 0)
          ->count(),
      ];
    }
    // dd($scheme_data);
    return datatables()->of($scheme_data)
      ->addIndexColumn()
      ->make(true);
  }

  public function schemeMisReportExcel(Request $request)
  {
    $user_id = AuthChecker::getUserId();
    $duty_obj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
    $dist_code = $duty_obj ? $duty_obj->district_code : null;
    $schemes = Scheme::where('is_active', 1)->orderBy('id')->get();
    $scheme_id = $request->excel_scheme_id;



    $filter_type = $request->excel_filter_type;

    // Check the value of the submit field
    // Prepare data with headers as the first row
    $scheme_data = [];
    $scheme_data[] = [ // Headers for the Excel file
      'Scheme Name',
      'Beneficiaries with Incomplete Details',
      'Beneficiaries with No Aadhar Number',
      'Beneficiaries with Duplicate Aadhar Numbers',
      'Beneficiaries with No Mobile Number',
      'Beneficiaries with Duplicate Mobile Numbers',
      'Beneficiaries With Duplicate Bank Account Numbers',
      'Beneficiaries with Payment Transaction Failure',
      'Beneficiaries with Name Validation Failure',
      'Beneficiaries with Account Validation Failure',
    ];

    foreach ($schemes as $scheme) {
      $scheme_data[] = [
        trim($scheme->scheme_name),
        BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('is_incomplete', 1)
          ->where('is_rejected', 0)
          ->count(),
        BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('no_aadhar', 1)
          ->where('is_rejected', 0)
          ->count(),
        BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('dup_aadhar', 1)
          ->where('is_rejected', 0)
          ->count(),
        BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('no_mobile', 1)
          ->where('is_rejected', 0)
          ->count(),
        BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('dup_mobile', 1)
          ->where('is_rejected', 0)
          ->count(),
        BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('dup_bank', 1)
          ->where('is_rejected', 0)
          ->count(),
        BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('is_bank_failed', 1)
          ->where('is_rejected', 0)
          ->count(),
        BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('is_bank_failed', 2)
          ->where('is_rejected', 0)
          ->count(),
        BenEntry::where('scheme_id', $scheme->id)
          ->where('dist_code', $dist_code)
          ->where('is_bank_failed', 3)
          ->where('is_rejected', 0)
          ->count(),



      ];
    }

    $fileName = 'Scheme_Based_Beneficiary_Count_' . date('d/m/Y') . '_' . time() . '.xlsx';
    Excel::create($fileName, function ($excel) use ($scheme_data) {
      $excel->setTitle('Beneficiary Count Report');
      $excel->sheet('Beneficiary Count Report', function ($sheet) use ($scheme_data) {
        $sheet->fromArray($scheme_data, null, 'A1', false, false);
      });
    })->download('xlsx');
  }


  public function BenMisReportIndex(Request $request)
  {
    $user_id = AuthChecker::getUserId();
    $duty_obj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
    $district_code = $duty_obj->district_code;

    $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . " )"));


    return view('No-Duplicate-Update/ben_mis_report', [
      'schemes' => $schemes,
      'dist_code' => $district_code,
    ]);
  }
  public function BenMisReport(Request $request)
  {
    // dd($request->all());

    $district_code = $request->dist_code;
    $scheme_id = $request->scheme_id;
    $rural_urban_code = $request->filter_1;
    $blk_ulb_code = $request->filter_2;



    $sub_divisions = SubDistrict::where('district_code', $district_code)->select('sub_district_code', 'sub_district_name')->get();
    $blocks = Taluka::where('district_code', $district_code)->select('block_code', 'block_name')->get();

    $local_body = [];

    // Populate the local_body array with sub-divisions and blocks
    foreach ($sub_divisions as $sub_div) {
      $local_body[] = [
        'code' => $sub_div->sub_district_code,
        'name' => $sub_div->sub_district_name,
      ];
    }

    foreach ($blocks as $block) {
      $local_body[] = [
        'code' => $block->block_code,
        'name' => $block->block_name,
      ];
    }

    if ($rural_urban_code != null && $blk_ulb_code != null) {
      $sub_divisions = SubDistrict::where('district_code', $district_code)->where('sub_district_code', $blk_ulb_code)->select('sub_district_code', 'sub_district_name')->get();
      $blocks = Taluka::where('district_code', $district_code)->where('block_code', $blk_ulb_code)->select('block_code', 'block_name')->get();

      $local_body = [];

      // Populate the local_body array with sub-divisions and blocks
      foreach ($sub_divisions as $sub_div) {
        $local_body[] = [
          'code' => $sub_div->sub_district_code,
          'name' => $sub_div->sub_district_name,
        ];
      }

      foreach ($blocks as $block) {
        $local_body[] = [
          'code' => $block->block_code,
          'name' => $block->block_name,
        ];
      }
      // dd($local_body);
    }

    // dd($local_body);


    // Loop through the local_body array and populate data
    foreach ($local_body as $local_b) {
      $scheme_data[] = [
        'blkUlb_name' => $local_b['name'],
        'no_aadhar_p' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('next_level_clean_id', null)
          ->where('no_aadhar', 1)
          ->where('is_rejected', 0)
          ->count(),
        'no_aadhar_v' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('next_level_clean_id', 2)
          ->where('no_aadhar', 1)
          ->where('is_rejected', 0)
          ->count(),
        'no_aadhar_a' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('next_level_clean_id', 1)
          ->where('no_aadhar', 1)
          ->where('is_rejected', 0)
          ->count(),

        'bank_failure_p' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_bank_failed', 1)
          ->where('next_level_clean_id', null)
          ->where('is_rejected', 0)
          ->count(),
        'bank_failure_v' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_bank_failed', 1)
          ->where('next_level_clean_id', 2)
          ->where('is_rejected', 0)
          ->count(),
        'bank_failure_a' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_bank_failed', 1)
          ->where('next_level_clean_id', 1)
          ->where('is_rejected', 0)
          ->count(),


        'no_mobile_p' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('no_mobile', 1)
          ->where('next_level_clean_id', null)
          ->where('is_rejected', 0)
          ->count(),
        'no_mobile_v' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('no_mobile', 1)
          ->where('next_level_clean_id', 2)
          ->where('is_rejected', 0)
          ->count(),
        'no_mobile_a' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('no_mobile', 1)
          ->where('next_level_clean_id', 1)
          ->where('is_rejected', 0)
          ->count(),



        'dup_mobile_p' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('dup_mobile', 1)
          ->where('next_level_clean_id', null)
          ->where('is_rejected', 0)
          ->count(),

        'dup_mobile_v' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('dup_mobile', 1)
          ->where('next_level_clean_id', 2)
          ->where('is_rejected', 0)
          ->count(),

        'dup_mobile_a' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('dup_mobile', 1)
          ->where('next_level_clean_id', 1)
          ->where('is_rejected', 0)
          ->count(),


        'dup_bank_p' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('dup_bank', 1)
          ->where('next_level_clean_id', null)
          ->where('is_rejected', 0)
          ->count(),
        'dup_bank_v' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('dup_bank', 1)
          ->where('next_level_clean_id', 2)
          ->where('is_rejected', 0)
          ->count(),
        'dup_bank_a' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('dup_bank', 1)
          ->where('next_level_clean_id', 1)
          ->where('is_rejected', 0)
          ->count(),


        'dup_aadhar_p' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('dup_aadhar', 1)
          ->where('next_level_clean_id', null)
          ->where('is_rejected', 0)
          ->count(),
        'dup_aadhar_v' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('dup_aadhar', 1)
          ->where('next_level_clean_id', 2)
          ->where('is_rejected', 0)
          ->count(),

        'dup_aadhar_a' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('dup_aadhar', 1)
          ->where('next_level_clean_id', 1)
          ->where('is_rejected', 0)
          ->count(),



        'incomplete_data_p' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_incomplete', 1)
          ->where('next_level_clean_id', null)
          ->where('is_rejected', 0)
          ->count(),
        'incomplete_data_v' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_incomplete', 1)
          ->where('next_level_clean_id', 2)
          ->where('is_rejected', 0)
          ->count(),

        'incomplete_data_a' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_incomplete', 1)
          ->where('next_level_clean_id', 1)
          ->where('is_rejected', 0)
          ->count(),


        'name_failure_p' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_bank_failed', 2)
          ->where('next_level_clean_id', null)
          ->where('is_rejected', 0)
          ->count(),
        'name_failure_v' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_bank_failed', 2)
          ->where('next_level_clean_id', 2)
          ->where('is_rejected', 0)
          ->count(),
        'name_failure_a' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_bank_failed', 2)
          ->where('next_level_clean_id', 1)
          ->where('is_rejected', 0)
          ->count(),

        'ac_failure_p' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_bank_failed', 3)
          ->where('next_level_clean_id', null)
          ->where('is_rejected', 0)
          ->count(),
        'ac_failure_v' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_bank_failed', 3)
          ->where('next_level_clean_id', 2)
          ->where('is_rejected', 0)
          ->count(),
        'ac_failure_a' => BenEntry::where('scheme_id', $scheme_id)
          ->where('dist_code', $district_code)
          ->where('created_by_local_body_code', $local_b['code'])
          ->where('is_bank_failed', 3)
          ->where('next_level_clean_id', 1)
          ->where('is_rejected', 0)
          ->count(),

      ];
    }
    // dd($scheme_data);
    return datatables()->of($scheme_data)
      ->addIndexColumn()
      ->make(true);
  }
  public function BenMisReportExcel(Request $request)
  {
    dd($request->all());
  }


  private function getUpdateCode($row, $request, $role)
  {
    $updateArray = array();



    // Verifier
    if ($row->no_aadhar == 1 && !empty($request->new_aadhar_no) && $request->new_aadhar_no != $row->aadhar_no && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 1)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }

    // Verifier Reject
    if ($row->no_aadhar == 1 && !empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 81)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }


    //Approver Approve
    if ($row->no_aadhar == 1 && $row->next_level_clean_id == 2 && $request->opreation_type == 'A' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 2)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    //Approver Revert
    if ($row->no_aadhar == 1 && $row->next_level_clean_id == 2 && $request->opreation_type == 'T' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 3)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    if ($row->dup_aadhar == 1 && !empty($request->new_aadhar_no) && $request->new_aadhar_no == $row->aadhar_no && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 8)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }

    if ($row->dup_aadhar == 1 && !empty($request->new_aadhar_no) && $request->new_aadhar_no != $row->aadhar_no && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 7)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }

    // Verifier Reject
    if ($row->dup_aadhar == 1 && !empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 82)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }

    //Approver  Approve 
    if ($row->dup_aadhar == 1 && $row->next_level_clean_id == 2 && $request->opreation_type == 'A' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 31)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    //Approver Revert
    if ($row->dup_aadhar == 1 && $row->next_level_clean_id == 2 && $request->opreation_type == 'T' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 32)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }


    if ($row->no_mobile == 1 && !empty($request->new_mobile_no) && $request->new_mobile_no != $row->mobile_no && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 4)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }

    // Verifier Reject
    if ($row->no_mobile == 1 && !empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 83)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }

    //Approver  Approve 
    if ($row->no_mobile == 1 && $row->next_level_clean_id == 2 && $request->opreation_type == 'A' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 5)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    //Approver Revert
    if ($row->no_mobile == 1 && $row->next_level_clean_id == 2 && $request->opreation_type == 'T' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 6)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    if ($row->dup_mobile == 1 && !empty($request->new_mobile_no) && $request->new_mobile_no == $row->mobile_no && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 10)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }

    if ($row->dup_mobile == 1 && !empty($request->new_mobile_no) && $request->new_mobile_no != $row->mobile_no && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 9)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }

    // Verifier Reject
    if ($row->dup_mobile == 1 && !empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 83)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }

    //Approver  Approve 
    if ($row->dup_mobile == 1 && $row->next_level_clean_id == 2 && $request->opreation_type == 'A' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 33)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    //Approver Revert
    if ($row->dup_mobile == 1 && $row->next_level_clean_id == 2 && $request->opreation_type == 'T' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 34)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    if ($row->dup_bank == 1 && !empty($request->new_bank_code) && trim($request->new_bank_code) != trim($row->bank_code) && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 12)
        ->select('code', 'description')
        ->first();
    }

    if ($row->dup_bank == 1 && !empty($request->new_bank_code) && trim($request->new_bank_code) == trim($row->bank_code) && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 11)
        ->select('code', 'description')
        ->first();
    }

    // Verifier Reject
    if ($row->dup_bank == 1 && !empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 75)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }


    //Approver  Approve 
    if ($row->dup_bank == 1 && $row->next_level_clean_id == 2 && $request->opreation_type == 'A' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 35)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    //Approver Revert
    if ($row->dup_bank == 1 && $row->next_level_clean_id == 2 && $request->opreation_type == 'T' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 36)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    $pay_validated = 0;

    // Payment Failed Correction
    $pay_validated = $row->pay_validated;
    // dd($pay_validated);

    // Check for new bank code mismatch and update logic
    if ($row->is_bank_failed == 1 && $role == 1) {
      // dd($pay_validated);
      if ($pay_validated == 3) {
        if (empty($request->op_type)) {
          $updateArray[] = DB::connection('pgsql')
            ->table('m_update_code')
            ->where('id', 13)
            ->select('code', 'description')
            ->first();
        }


        // Verifier Reject
        if (!empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
          $updateArray[] = DB::connection('pgsql')
            ->table('m_update_code')
            ->where('id', 76)
            ->select('code', 'description')
            ->first();
          // ->toArray();
        }

      }

      if ($pay_validated == 4) {
        if (empty($request->op_type)) {
          $updateArray[] = DB::connection('pgsql')
            ->table('m_update_code')
            ->where('id', 14)
            ->select('code', 'description')
            ->first();
        }
        // Verifier Reject
        if (!empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
          $updateArray[] = DB::connection('pgsql')
            ->table('m_update_code')
            ->where('id', 76)
            ->select('code', 'description')
            ->first();
          // ->toArray();
        }
      }
      if ($pay_validated == 5) {
        if (empty($request->op_type)) {
          $updateArray[] = DB::connection('pgsql')
            ->table('m_update_code')
            ->where('id', 77)
            ->select('code', 'description')
            ->first();
        }

        // Verifier Reject
        if (!empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
          $updateArray[] = DB::connection('pgsql')
            ->table('m_update_code')
            ->where('id', 78)
            ->select('code', 'description')
            ->first();
          // ->toArray();
        }
      }
    }

    // if ($row->is_bank_failed == 1 && !empty($request->new_mobile_no) && $request->new_mobile_no != $row->mobile_no && $role == 1) {
    //   $updateArray[] = DB::connection('pgsql')
    //     ->table('m_update_code')
    //     ->where('id', 4)
    //     ->select('code', 'description')
    //     ->first();
    //   // ->toArray();
    // }

    //Approver  Approve 
    if ($row->is_bank_failed == 1 && $row->next_level_clean_id == 2 && $request->opreation_type == 'A' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 55)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    //Approver Revert
    if ($row->is_bank_failed == 1 && $row->next_level_clean_id == 2 && $request->opreation_type == 'T' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 56)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }


    if ($row->is_bank_failed == 2 && !empty($request->process_type) && ($request->process_type == 1) && $role == 1) {

      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 18)
        ->select('code', 'description')
        ->first();


    }

    if ($row->is_bank_failed == 2 && !empty($request->process_type) && ($request->process_type == 2) && $role == 1) {

      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 19)
        ->select('code', 'description')
        ->first();


    }

    if ($row->is_bank_failed == 2 && !empty($request->process_type) && ($request->process_type == 3) && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 20)
        ->select('code', 'description')
        ->first();
    }

    // Verifier Reject
    if ($row->is_bank_failed == 2 && !empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 79)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }




    //Approver  Approve 
    if ($row->is_bank_failed == 2 && $row->next_level_clean_id == 2 && $request->opreation_type == 'A' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 57)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    //Approver Revert
    if ($row->is_bank_failed == 2 && $row->next_level_clean_id == 2 && $request->opreation_type == 'T' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 58)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    if ($row->is_bank_failed == 3 && !empty($request->new_bank_code) && ($request->new_bank_code != $request->old_bank_code) && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 17)
        ->select('code', 'description')
        ->first();
    }

    // Verifier Reject
    if ($row->is_bank_failed == 2 && !empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 80)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }


    //Approver  Approve 
    if ($row->is_bank_failed == 3 && $row->next_level_clean_id == 2 && $request->opreation_type == 'A' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 59)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    //Approver Revert
    if ($row->is_bank_failed == 3 && $row->next_level_clean_id == 2 && $request->opreation_type == 'T' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 60)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }


    if ($row->is_incomplete == 1 && in_array($row->scheme_id, [1, 3]) && !empty($request->new_caste_certificate_no) && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 21)
        ->select('code', 'description')
        ->first();
    }

    // Verifier Reject
    if ($row->is_bank_failed == 2 && !empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 80)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }

    //Approver  Approve 
    if ($row->is_incomplete == 1 && in_array($row->scheme_id, [1, 3]) && $row->next_level_clean_id == 2 && $request->opreation_type == 'A' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 45)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    //Approver Revert
    if ($row->is_incomplete == 1 && in_array($row->scheme_id, [1, 3]) && $row->next_level_clean_id == 2 && $request->opreation_type == 'T' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 46)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    // Verifier Reject
    if ($row->is_incomplete == 1 && in_array($row->scheme_id, [1, 3]) && !empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 85)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }


    if ($row->is_incomplete == 1 && in_array($row->scheme_id, [2]) && !empty($request->new_disablity_type) && !empty($request->new_disablity_type_percentage) && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 22)
        ->select('code', 'description')
        ->first();
    }

    // Verifier Reject
    if ($row->is_incomplete == 1 && in_array($row->scheme_id, [2]) && !empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 86)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }

    //Approver  Approve 
    if ($row->is_incomplete == 1 && in_array($row->scheme_id, [2]) && $row->next_level_clean_id == 2 && $request->opreation_type == 'A' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 47)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    //Approver Revert
    if ($row->is_incomplete == 1 && in_array($row->scheme_id, [2]) && $row->next_level_clean_id == 2 && $request->opreation_type == 'T' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 48)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }


    if ($row->is_incomplete == 1 && in_array($row->scheme_id, [11]) && !empty($request->new_husband_first_name) && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 23)
        ->select('code', 'description')
        ->first();
    }

    // Verifier Reject
    if ($row->is_incomplete == 1 && in_array($row->scheme_id, [11]) && !empty($request->op_type) && $request->op_type == 'R' && $role == 1) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 89)
        ->select('code', 'description')
        ->first();
      // ->toArray();
    }

    //Approver  Approve 
    if ($row->is_incomplete == 1 && in_array($row->scheme_id, [11]) && $row->next_level_clean_id == 2 && $request->opreation_type == 'A' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 49)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }

    //Approver Revert
    if ($row->is_incomplete == 1 && in_array($row->scheme_id, [11]) && $row->next_level_clean_id == 2 && $request->opreation_type == 'T' && $role == 2) {
      $updateArray[] = DB::connection('pgsql')
        ->table('m_update_code')
        ->where('id', 50)
        ->where('role_id', 2)
        ->select('code', 'description')
        ->first();
    }







    return $updateArray;

  }



  private function benStatus($ben_id, $scheme_id)
  {
    // Fetch the data for the given ben_id and scheme_id
    $data = BenEntry::where('id', $ben_id)
      ->where('scheme_id', $scheme_id)
      ->where('next_level_clean_id', 2)
      ->get();

    // Map through the data and add `pay_validated` property
    $data = $data->map(function ($item) use ($scheme_id) {
      $pay_validation = DB::connection('pgsql_paywrite')
        ->table('payment.ben_payment_details')
        ->where('ben_id', $item->id)
        ->where('scheme_id', $scheme_id)
        ->select('pay_validated')
        ->first();

      $item->pay_validated = $pay_validation->pay_validated ?? null;
      return $item;
    });

    // Return no records message if data is empty
    if ($data->isEmpty()) {
      return '<span class="text text-danger" style="font-size: 12px;"><b>No records found</b></span>';
    }

    $status = [];
    $sl = 1;

    foreach ($data as $item) {
      // Check conditions and add status messages
      if (!empty($item->dup_aadhar)) {
        $status[] = $this->formatStatus($sl++, 'Duplicate Aadhar');
      }
      if (!empty($item->dup_mobile)) {
        $status[] = $this->formatStatus($sl++, 'Duplicate Mobile');
      }
      if (!empty($item->dup_bank)) {
        $status[] = $this->formatStatus($sl++, 'Duplicate Bank');
      }
      if (!empty($item->no_aadhar)) {
        $status[] = $this->formatStatus($sl++, 'No Aadhar');
      }
      if (!empty($item->no_mobile)) {
        $status[] = $this->formatStatus($sl++, 'No Mobile');
      }
      if (!empty($item->is_bank_failed) && $item->is_bank_failed == 1) {
        if ($item->pay_validated == 3) {
          $status[] = $this->formatStatus($sl++, 'Payment Failure SBI');
        } elseif ($item->pay_validated == 4) {
          $status[] = $this->formatStatus($sl++, 'Payment Failure RBI');
        } elseif ($item->pay_validated == 5) {
          $status[] = $this->formatStatus($sl++, 'Payment Failure IFMS');
        }
      }
      if (!empty($item->is_bank_failed) && $item->is_bank_failed == 2) {
        $status[] = $this->formatStatus($sl++, 'Name Validation Failed');

      }
      if (!empty($item->is_bank_failed) && $item->is_bank_failed == 3) {
        $status[] = $this->formatStatus($sl++, 'A/c Validation Failed');
      }
    }

    // Join the status messages into a single string
    return implode('', $status);
  }

  /**
   * Helper function to format status message.
   *
   * @param int $sl
   * @param string $message
   * @return string
   */
  private function formatStatus($sl, $message)
  {
    return '<span class="text text-danger" style="font-size: 12px;"><b>' . $sl . '. ' . $message . '</b></span><br>';
  }



  public function rejectApplicantDetails(Request $request)
  {
    // dd('ok');
    // dd($request->all());
    // var_dump($request->all());

    $auth = Authchecker::VerifierPermission();
    if ($auth) {

      try {
        $user_id = Auth::user()->id;
        if (empty($request->scheme_id)) {
          return redirect()->route('noDupBeneficiariesList')->with('error', 'Invalid Scheme');
        }

        if (empty($request->id)) {
          return redirect()->route('noDupBeneficiariesList')->with('error', 'Invalid Beneficiary');
        }
        $ben_id = (int) $request->id;
        $scheme_id = (int) $request->scheme_id;
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();

        if (empty($duty_obj)) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        $district_code = $duty_obj->district_code;
        $mapping_level = $duty_obj->mapping_level;
        if ($duty_obj->mapping_level == "Block") {
          $urban_body_code = $duty_obj->taluka_code;
        }
        if ($duty_obj->mapping_level == "Subdiv") {
          $urban_body_code = $duty_obj->urban_body_code;
        }

        $row = DB::connection('pgsql')->table('pension.beneficiaries')->where('id', $ben_id)->where(
          'scheme_id',
          $scheme_id
        )->first();
        $bankDupRow = DB::table('pension.ben_payment_details_bank_code_dup')->where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', -97)->count();

        $updateArray = $this->getUpdateCode($row, $request, 1);

        //  if($ben_id == 150195)
        //  {
        //   dd('ok');
        //  }

        $benEntry_model = BenEntry::find($ben_id);


        $benEntry_model->next_level_clean_id = 2;
        $benEntry_model->action_by = $user_id;
        $benEntry_model->action_ip_address = request()->ip();
        $benEntry_model->action_type = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();

        DB::connection('pgsql')->beginTransaction();
        DB::connection('pgsql_paywrite')->beginTransaction();

        $updateBenTable = $benEntry_model->save();

        $cnt = 0;
        foreach ($updateArray as $updatecode) {
          $accept_reject_info_model = new AcceptRejectInfo;
          $accept_reject_info_model->scheme_id = $scheme_id;
          $accept_reject_info_model->created_by_dist_code = $district_code;
          $accept_reject_info_model->created_by_local_body_code = $urban_body_code;
          $accept_reject_info_model->created_at = date('Y-m-d H:i:s');
          $accept_reject_info_model->updated_at = date('Y-m-d H:i:s');
          $accept_reject_info_model->op_type = $updatecode->code;
          $accept_reject_info_model->ip_address = request()->ip();
          $accept_reject_info_model->user_id = $user_id;
          $accept_reject_info_model->application_id = $ben_id;
          $accept_reject_info_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
          $accept_reject_info_model->remarks = $updatecode->description;
          $accept_reject_info_model->save();
          $cnt++;
        }


        // dd($updateArray);
        if ($cnt == count($updateArray)) {
          $updateBenDetailsAction = 1;
        } else {
          $updateBenDetailsAction = 0;
        }



        $ben_failed_payment_details_model = BenFailedPaymentDetails::where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->where('edited_status', 0)->whereIn('failed_type', [1, 2, 3, 4, 5])->first();
        if ($ben_failed_payment_details_model) {
          if ($row->is_bank_failed == 1 || $row->is_bank_failed == 2 || $row->is_bank_failed == 3) {
            if ($ben_failed_payment_details_model) {
              $ben_failed_payment_details_model->edited_status = 1;
              $ben_failed_payment_details_model->updated_at = date('Y-m-d H:i:s');
              $updateFailedPayment = $ben_failed_payment_details_model->save();

            }
          }
          $updateFailedPayment = 1;
        } else {
          $updateFailedPayment = 1;
        }



        $benRejectRequest = new BenRejectRequest;
        $benRejectRequest->ben_id = $ben_id;
        $benRejectRequest->scheme_id = $scheme_id;
        $benRejectRequest->rejected_by = Auth::user()->id;
        $benRejectRequest->rejected_ip = request()->ip();
        $benRejectRequest->rejected_date = date('Y-m-d H:i:s');
        $benRejectRequest->next_level_clean_id = 2;
        $benRejectLog = $benRejectRequest->save();



        // if($ben_id == 6745364 )
        // {
        //  dump($updateFailedPayment);
        //  dump($benRejectLog);
        //  dump($updateBenDetailsAction);
        //  dump($updateBenTable);
        //  die();
        // }

        if ($updateFailedPayment && $benRejectLog && $updateBenDetailsAction && $updateBenTable) {

          DB::connection('pgsql')->commit();
          DB::connection('pgsql_paywrite')->commit();
          $return_text = "Beneficiary(" . $row->id . ") has successfully Requested for Rejection & forwared for approval.";
          // dd($return_text);
          return redirect()->route('noDupBeneficiariesList')->with('success', $return_text);

        } else {
          DB::connection('pgsql')->rollBack();
          DB::connection('pgsql_paywrite')->rollBack();
          return redirect()->route('noDupBeneficiariesList')->with('error', 'Error: Failed to reject beneficiary');
        }


      } catch (Exception $e) {
        
        // dd($e);
        DB::connection('pgsql')->rollBack();
        DB::connection('pgsql_paywrite')->rollBack();
        return redirect()->route('noDupBeneficiariesList')->with('error', 'Error: ' . $e->getMessage());
      }

    } else {
      return redirect()->route('noDupBeneficiariesList')->with('error', 'Not Allowded');

    }
  }






}




