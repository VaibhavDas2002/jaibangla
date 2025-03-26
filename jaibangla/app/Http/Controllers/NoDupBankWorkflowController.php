<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BenEntry;
use App\District;
use App\Configduty;
use App\GP;
use App\Taluka;
use App\Scheme;
use App\UrbanBody;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Helpers;
use App\AcceptRejectInfo;
use App\BenFailedPaymentDetails;
use App\BenPaymentDetails;

use App\Ward;
use App\BankDetails;
use App\BenDupBankCodePayemntDetails;
use App\Helpers\AuthChecker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\DataInsert;
use App\Helpers\DupCheck;
use App\SchemeGenSetting;
use App\BenRejectRequest;
use App\BenIncompleteLog;
use App\SubDistrict;
use DateTime;
use Carbon\Carbon;
use App\BenDocs;


class NoDupBankWorkflowController extends Controller
{
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
    ini_set('memory_limit', '256M');
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

  public function verifier_index(Request $request)
  {
    $auth = AuthChecker::VerifierPermission();
    if ($auth) {
      $user_id = Auth::user()->id;
      $duty_obj = Configduty::where('user_id', $user_id)->where('is_active', 1)->where('is_active', 1)->first();

      $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where  is_active=1 and user_id=" . $user_id . " )"));
      $incomplete_types = DB::table('public.m_incomplete_type')->where('is_active', true)->get();

      if (empty($duty_obj)) {
        return redirect('/')->with('danger', 'No Duty Assigned');
      }
      $is_urban = $duty_obj->is_urban;
      $district_code = $duty_obj->district_code;
      $blockUlbCode = null;
      $urban_bodys = null;
      $talukas = null;
      $gps = null;
      if ($is_urban == 1) {
        $blockUlbCode = $duty_obj->urban_body_code;
        $urban_bodys = UrbanBody::where('sub_district_code', $blockUlbCode)->select('urban_body_code', 'urban_body_name')->get();
      } elseif ($is_urban == 2) {
        $blockUlbCode = $duty_obj->taluka_code;
        $gps = GP::where('block_code', $blockUlbCode)->get();
      }
      return view('incomplete_details/verifier-index', ['schemes' => $scheme, 'dist_code' => $duty_obj->district_code, 'is_urban' => $is_urban, 'urban_bodys' => $urban_bodys, 'talukas' => $talukas, 'gps' => $gps, 'incomplete_types' => $incomplete_types]);

    } else {
      return redirect("/")->with('danger', 'User Disabled. ');
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
        return redirect("/")->with('danger', 'Scheme Not Valid');
      }

      $user_id = Auth::user()->id;
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
        $query = "SELECT id,is_incomplete,dup_aadhar,no_aadhar,dup_bank,dup_mobile,no_mobile,no_ration_card,no_epic_voter, is_bank_failed,pay_validated,created_by_local_body_code, ben_fname, ben_mname, ben_lname,aadhar_no, scheme_id,block_ulb_name,gp_ward_name,bank_code,bank_ifsc,visiting_time  FROM pension.beneficiaries WHERE scheme_id = " . $scheme_id . " AND created_by_local_body_code = " . $urban_body_code . " AND created_by_dist_code = " . $district_code . "  AND next_level_clean_id = 0 AND is_rejected = 0";

        if ($filter_type == 0) {
          $query .= " AND is_incomplete = 1 ";
        }
        if ($filter_type == 1) {
          $query .= " AND dup_aadhar = 1 ";
        }
        if ($filter_type == 2) {
          $query .= " AND no_aadhar = 1 ";
        }
        if ($filter_type == 3) {
          $query .= " AND dup_bank = 1 ";
        }
        if ($filter_type == 4) {
          $query .= " AND dup_mobile = 1 ";
        }
        if ($filter_type == 5) {
          $query .= " AND no_mobile = 1 ";
        }
        if ($filter_type == 6) {
          $query .= " AND no_ration_card = 1 ";
        }
        if ($filter_type == 8) {
          $query .= " AND no_epic_voter = 1 ";
        }
        if ($filter_type == 11) {
          $query .= " AND is_bank_failed = 2 ";
        }
        if ($filter_type == 12) {
          $query .= " AND is_bank_failed = 3 ";
        }
        // dd($query);
        if ($filter_type == 10 || !empty($pay_validated)) {
          if ($pay_validated == 3) {
            $query .= " AND is_bank_failed = 1 AND pay_validated = 3";
          }
          if ($pay_validated == 4) {
            $query .= " AND is_bank_failed = 1  AND pay_validated = 4";
          }
          if ($pay_validated == 5) {
            $query .= " AND is_bank_failed = 1  AND pay_validated = 5";
          }
        }
        if (!empty($request->blk_ulb_code)) {
          $query .= " AND created_by_local_body_code = " . $request->blk_ulb_code . " ";
        }
        $query .= " ORDER BY created_by_local_body_code";
        $data = DB::connection('pgsql')->select($query);


        return datatables()->of($data)
          ->addColumn('view', function ($data) {
            $view = '<a href="' . route('edit-beneficiary-details', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> View & Update  </a>';
            return $view;
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

  public function viewBeneficiaryDetails(Request $request)
  {
    $auth = AuthChecker::VerifierPermission();
    if ($auth) {
      try {
        $user_id = Auth::user()->id;
        $ben_id = $request->id;
        $scheme_id = $request->scheme_id;
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
        if ($duty_obj->mapping_level == 'Subdiv') {
          $blk_Ulb_code = $duty_obj->urban_body_code;
        } else if ($duty_obj->mapping_level == 'Block') {
          $blk_Ulb_code = $duty_obj->block_code;
        } else {
          return redirect("/")->with('danger', 'Not Allowed.');
        }
        $row = BenEntry::where('scheme_id', $scheme_id)->where('id', $ben_id)->where('created_by_dist_code', $district_code)->first();

        if (empty($row)) {
          return redirect()->route('incomplete-details-verifier-view')->with('error', 'Beneficiary Not Found');
        }

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

        $docs = BenDocs::where('beneficiary_id', $ben_id)->where('created_by_dist_code', $district_code)->orderBy('document_type')->get();


        $getAadharDoc = $this->getDocCount($ben_id, $scheme_id, $district_code, $this->aadhar_doc);
        $getBankDoc = $this->getDocCount($ben_id, $scheme_id, $district_code, $this->bank_doc);
        $getDisabilityDoc = $this->getDocCount($ben_id, $scheme_id, $district_code, $this->disability_doc);
        $getHusbandDoc = $this->getDocCount($ben_id, $scheme_id, $district_code, $this->husband_doc);
        $getCasteDoc = $this->getDocCount($ben_id, $scheme_id, $district_code, $this->caste_doc);
        $getEpicDoc = $this->getDocCount($ben_id, $scheme_id, $district_code, $this->epic_doc);
        $getRationDoc = $this->getDocCount($ben_id, $scheme_id, $district_code, $this->ration_doc);

        $manabik_visible = $this->getFormVisible($ben_id, $scheme_id);
        $widow_visible = $this->getFormVisible($ben_id, $scheme_id);
        $sc_st_visible = $this->getFormVisible($ben_id, $scheme_id);

        $field_array = $this->getIncompleteData($ben_id, $scheme_id);

        $payemntModel = BenPaymentDetails::where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->first();

        if ($payemntModel) {
          $failed_type_id = $payemntModel->pay_validated;
        } else {
          $failed_type_id = null;
        }

        if (in_array($row->is_bank_failed, [1, 2, 3])) {
          $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw('payment.failed_payment_details as f'))
            ->join('payment.ben_payment_details as ben', 'f.ben_id', '=', 'ben.ben_id')
            ->where('f.failed_type', $failed_type_id)->where('f.edited_status', 0)->where('ben.is_eligible', true)
            ->where('ben.ben_status', 1)->where('f.scheme_id', $scheme_id)
            ->where('f.ben_id', $row->id)
            ->orderBy('f.id', 'desc')
            ->first();
          // dd($ben_details);
          if ($failed_type_id == 4 || $failed_type_id == 5) {
            $invalid_status = $ben_details->remarks;
          } elseif ($failed_type_id == 3) {
            $remarks = DB::connection('pgsql_paywrite')->table('sbi.credit_transaction_code')->where('code', trim($ben_details->status_code))->get(['description'])
              ->first();
            $invalid_status = $remarks->description;
          }

          if ($failed_type_id == 2) {
            $av_name_response = $ben_details->av_name_response;
          } else {
            $av_name_response = null;
          }

        } else {
          $ben_details = null;
          $invalid_status = null;
          $av_name_response = null;
        }

        $required_fields = DB::table('public.m_fields')
          ->where('is_active', 1)
          ->pluck('id')
          ->toArray();
        $schemeSetting = SchemeGenSetting::where('scheme_id', $scheme_id)->first();

        $name_opts = $schemeSetting->name_valid_opt;
        $array = explode(',', trim($name_opts, '{}'));
        $array = array_map('intval', $array);
        $name_options = DB::table('public.m_name_valid_option')->wherein('id', $array)->get();

        if ($schemeSetting->allow_bank_failed_update == 1 || $schemeSetting->allow_bank_name_update == 1 || $schemeSetting->allow_bank_ac_update == 1) {
          $canBankupdate = 1;
        } else {
          $canBankupdate = 0;
        }



        // dd($canBankupdate);

        return view('incomplete_details/ben_incomplete_details', [
          'row' => $row,
          'scheme_id' => $scheme_id,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'docs' => $docs,
          'is_verifier' => $auth,
          'getAadharDoc' => $getAadharDoc,
          'getBankDoc' => $getBankDoc,
          'getDisabilityDoc' => $getDisabilityDoc,
          'getHusbandDoc' => $getHusbandDoc,
          'getCasteDoc' => $getCasteDoc,
          'getEpicDoc' => $getEpicDoc,
          'getRationDoc' => $getRationDoc,
          'manabik_visible' => $manabik_visible,
          'widow_visible' => $widow_visible,
          'sc_st_visible' => $sc_st_visible,
          'invalid_status' => $invalid_status,
          'av_name_response' => $av_name_response,
          'canBankupdate' => $canBankupdate,
          'name_options' => $name_options,
          'required_fields' => $required_fields,
          'field_arrays' => $field_array,
          'payemntModel' => $payemntModel
        ]);

      } catch (\Exception $e) {
        dd($e);
        return redirect()->back()->with('error', 'Something Error . Please Try again');
      }
    } else {
      return redirect("/")->with('danger', 'User Disabled. ');
    }
  }

  public function updateBeneficiaryDetails(Request $request)
  {
    $auth = AuthChecker::VerifierPermission();
    if ($auth) {
      try {
        $user_id = Auth::user()->id;
        $ben_id = $request->id;
        $scheme_id = $request->scheme_id;
        if (!ctype_digit($scheme_id)) {
          return redirect("/")->with('danger', 'Scheme Not Valid');
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
        $mapping_level = $duty_obj->mapping_level;
        if ($duty_obj->mapping_level == 'Subdiv') {
          $blk_Ulb_code = $duty_obj->urban_body_code;
        } else if ($duty_obj->mapping_level == 'Block') {
          $blk_Ulb_code = $duty_obj->block_code;
        } else {
          return redirect("/")->with('danger', 'Not Allowed.');
        }

        // dd($request->all());
        $isSameBank = !empty($request->new_bank_code) && ($request->new_bank_code != $request->old_bank_code) ? 0 : 1;
        $isSameIfsc = !empty($request->old_bank_ifsc) && ($request->new_bank_ifsc != $request->old_bank_ifsc) ? 0 : 1;
        $isSameMobile = !empty($request->new_mobile_no) && ($request->new_mobile_no != $request->old_mobile_no) ? 0 : 1;

        $row = BenEntry::where('id', $ben_id)->where('scheme_id', $scheme_id)->where('created_by_dist_code', $district_code)->first();
        if (empty($row)) {
          return redirect()->route('incomplete-details-verifier-view')->with('error', 'Beneficiary Not Found');
        }

        $paymentModel = BenPaymentDetails::where('ben_id', $ben_id)->where('scheme_id', $scheme_id)->first();
        if ($isSameIfsc == 1) {
          $bankDetails = BankDetails::where('ifsc_code', $request->old_bank_ifsc)->where('is_active', 1)->first();
        } else if ($isSameIfsc == 0) {
          $bankDetails = BankDetails::where('bank_code', $request->new_bank_ifsc)->where('is_active', 1)->first();
        }
        if (empty($bankDetails)) {
          return redirect()->back()->with('error', 'Bank Details Not Found');
        }
        $bankDupCount = BenDupBankCodePayemntDetails::where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', -97)->count();
        $benFailedModel = BenFailedPaymentDetails::where('id', $ben_id)->where('scheme_id', $scheme_id)->orderBy('id', 'desc')->first();

        if (!empty($request->new_bank_ifsc)) {
          $bank_ifsc_db = BankDetails::where('ifsc', $request->new_bank_ifsc)
            ->where('is_active', 1)
            ->first();
          if (!$bank_ifsc_db) {
            $return_text = "Bank Details not Available in Jai Bangla Portal";
            return redirect()->route('edit-beneficiary-details', [
              'id' => $ben_id,
              'scheme_id' => $scheme_id
            ])->with('error', $return_text);
          }
        }

        $isValidated = $this->validateInput($row, $request);

        if ($isValidated['is_valid'] == false) {
          return redirect()->route('incomplete-details-verifier-view', ['id' => $ben_id, 'scheme_id' => $scheme_id])
            ->withErrors($isValidated['errors'])
            ->withInput();
        }

        $old_values = [];
        $new_values = [];
        $incomplete_deatils = [];
        $benEntry_model = BenEntry::where('id', $ben_id)->where('created_by_dist_code', $district_code)->where('scheme_id', $scheme_id)->where('is_rejected', 0)->first();


        // Old values
        $old_values['aadhar_no'] = $benEntry_model->aadhar_no;
        $old_values['mobile_no'] = $benEntry_model->mobile_no;
        $old_values['bank_code'] = trim($benEntry_model->bank_code);
        $old_values['bank_ifsc'] = trim($benEntry_model->bank_ifsc);
        $old_values['bank_name'] = trim($benEntry_model->bank_name);
        $old_values['branch_name'] = trim($benEntry_model->branch_name);
        $old_values['caste'] = trim($benEntry_model->caste);
        $old_values['caste_certificate_no'] = trim($benEntry_model->caste_certificate_no);
        $old_values['type_disability'] = trim($benEntry_model->type_disability);
        $old_values['percentage_disability'] = trim($benEntry_model->percentage_disability);
        $old_values['certifying_auth'] = trim($benEntry_model->certifying_auth);
        $old_values['disability_designation'] = trim($benEntry_model->disability_designation);
        $old_values['husband_fname'] = trim($benEntry_model->husband_fname);
        $old_values['husband_mname'] = trim($benEntry_model->husband_mname);
        $old_values['husband_lame'] = trim($benEntry_model->husband_lname);

        // New values
        $new_values['aadhar_no'] = $request->new_aadhar_no;
        $new_values['mobile_no'] = $request->new_mobile_no;
        $new_values['bank_code'] = trim($request->new_bank_code);
        $new_values['bank_ifsc'] = $request->new_bank_ifsc;
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




        /// Update Beneficiary Deatils
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
          $benEntry_model->bank_ifsc = $request->new_ifsc_code;
          $benEntry_model->branch_name = $request->new_bank_branch;
          $benEntry_model->bank_name = $request->new_bank_name;
          if (!empty($request->new_mobile_no) && ($request->old_mobile_no != $request->new_mobile_no)) {
            $benEntry_model->mobile_no = $request->new_mobile_no;
          }
        }

        if ($row->is_bank_failed == 2 && !empty($request->process_type) && ($request->process_type == 2) && !empty($request->new_bank_code) && ($request->old_bank_code != $request->new_bank_code)) {

          $benEntry_model->bank_code = $request->new_bank_code;
          $benEntry_model->bank_ifsc = $request->new_ifsc_code;
          $benEntry_model->branch_name = $request->new_bank_branch;
          $benEntry_model->bank_name = $request->new_bank_name;
        }

        if (($row->is_bank_failed == 3) && !empty($request->new_bank_code) && ($request->new_bank_code != $request->old_bank_code)) {
          $benEntry_model->bank_code = $request->new_bank_code;
          $benEntry_model->bank_ifsc = $request->new_ifsc_code;
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


        $incomplete_deatils['is_incomplete'] = $row->is_incomplete;
        $incomplete_deatils['no_aadhar'] = trim($row->no_aahdar);
        $incomplete_deatils['dup_aadhar'] = trim($row->dup_aadhar);
        $incomplete_deatils['no_mobile'] = trim($row->no_mobile);
        $incomplete_deatils['dup_mobile'] = trim($row->dup_mobile);
        $incomplete_deatils['dup_bank'] = trim($row->dup_bank);
        $incomplete_deatils['is_bank_failed'] = trim($row->is_bank_failed);
        $incomplete_deatils['pay_validated'] = trim($row->pay_validated);
        $incomplete_deatils['bank_code'] = trim($row->bank_code);
        $incomplete_deatils['bank_ifsc'] = trim($row->bank_ifsc);
        $incomplete_deatils['bank_name'] = trim($row->bank_name);
        $incomplete_deatils['branch_name'] = trim($row->branch_name);
        $incomplete_deatils['aadhar_no'] = trim($row->aadhar_no);
        $incomplete_deatils['mobile_no'] = trim($row->mobile_no);
        $incomplete_deatils['husband_fname'] = trim($row->husband_fname);
        $incomplete_deatils['husband_mname'] = trim($row->husband_mname);
        $incomplete_deatils['husband_lname'] = trim($row->husband_lname);
        $incomplete_deatils['caste'] = trim($row->caste);
        $incomplete_deatils['caste_certificate_no'] = trim($row->caste_certificate_no);
        $incomplete_deatils['type_disability'] = trim($row->type_disability);
        $incomplete_deatils['percentage_disability'] = trim($row->percentage_disability);
        $incomplete_deatils['certifying_auth'] = trim($row->certifying_auth);
        $incomplete_deatils['disability_designation'] = trim($row->disability_designation);


        $Incomplete_data = json_encode($incomplete_deatils);

        // dd($Incomplete_data);

        $incomplete_model = New BenIncompleteLog();
        $incomplete_model->ben_id = $row->id;
        $incomplete_model->scheme_id = $row->scheme_id;
        $incomplete_model->next_level_clean_id = $row->next_level_clean_id;
        $incomplete_model->incom_data = $Incomplete_data;
        $incomplete_model->verified_by = $user_id;
        $incomplete_model->verified_at = date('Y-m-d H:i:s');
        $incomplete_model->verified_ip = request()->ip();
        $incomplete_model->new_data = json_encode($new_values);
        $incomplete_model->old_data = json_encode($old_values);
        $isIncompleteLog = $incomplete_model->save();


        $is_bank_upload = 0;
        $is_aadhar_upload = 0;
        $is_caste_upload = 0;
        $is_disability_upload = 0;
        $is_husband_upload = 0;

        $doc_aadhar_arr = DataInsert::getDocumentDetails($this->aadhar_doc);
        $doc_bank_arr = DataInsert::getDocumentDetails($this->bank_doc);
        $doc_caste_arr = DataInsert::getDocumentDetails($this->caste_doc);
        $doc_disability_arr = DataInsert::getDocumentDetails($this->disability_doc);
        $doc_husband_arr = DataInsert::getDocumentDetails($this->husband_doc);

        DB::connection('pgsql')->beginTransaction();
        DB::connection('pgsql_encwrite')->beginTransaction();
        DB::connection('pgsql_paywrite')->beginTransaction();


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
              return redirect()->route('edit-beneficiary-details', [
                'id' => $ben_id,
                'scheme_id' => $scheme_id
              ])->with('error', $errorMsg);

            }
          } else if (strtolower($mime_type) == 'image/png') {
            $extension = 'png';
          } else if (strtolower($mime_type) == 'image/gif') {
            $extension = 'gif';
          } else if (strtolower($mime_type) == 'application/pdf') {
            $extension = 'pdf';
          } else {
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_aadhar_arr['doc_name'];
            return redirect()->route('edit-beneficiary-details', [
              'id' => $ben_id,
              'scheme_id' => $scheme_id
            ])->with('error', $errorMsg);
          }
          if ($u_extension != $extension) {
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_aadhar_arr['doc_name'];
            return redirect()->route('edit-beneficiary-details', [
              'id' => $ben_id,
              'scheme_id' => $scheme_id
            ])->with('error', $errorMsg);
          }

          $base64 = base64_encode($img_data);
          $ip_address = request()->ip();
          $c_datetime = date('Y-m-d H:i:s', time());
          $user_id = Auth::user()->id;

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
                in_created_by_dist_code => " . $district_code . ",
                in_created_by_local_body_code => " . $blk_Ulb_code . ",
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
              return redirect()->route('edit-beneficiary-details', [
                'id' => $ben_id,
                'scheme_id' => $scheme_id
              ])->with('error', $errorMsg);
            }
          } else if (strtolower($mime_type) == 'image/png') {
            $extension = 'png';
          } else if (strtolower($mime_type) == 'image/gif') {
            $extension = 'gif';
          } else if (strtolower($mime_type) == 'application/pdf') {
            $extension = 'pdf';
          } else {
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_bank_arr['doc_name'];
            return redirect()->route('edit-beneficiary-details', [
              'id' => $ben_id,
              'scheme_id' => $scheme_id
            ])->with('error', $errorMsg);
          }
          if ($u_extension != $extension) {
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_bank_arr['doc_name'];
            return redirect()->route('edit-beneficiary-details', [
              'id' => $ben_id,
              'scheme_id' => $scheme_id
            ])->with('error', $errorMsg);
          }

          $base64 = base64_encode($img_data);
          $ip_address = request()->ip();
          $c_datetime = date('Y-m-d H:i:s', time());
          $user_id = Auth::user()->id;


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
                in_created_by_dist_code => " . $district_code . ",
                in_created_by_local_body_code => " . $blk_Ulb_code . ",
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
              return redirect()->route('edit-beneficiary-details', [
                'id' => $ben_id,
                'scheme_id' => $scheme_id
              ])->with('error', $errorMsg);
            }
          } else if (strtolower($mime_type) == 'image/png') {
            $extension = 'png';
          } else if (strtolower($mime_type) == 'image/gif') {
            $extension = 'gif';
          } else if (strtolower($mime_type) == 'application/pdf') {
            $extension = 'pdf';
          } else {
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_caste_arr['doc_name'];
            return redirect()->route('edit-beneficiary-details', [
              'id' => $ben_id,
              'scheme_id' => $scheme_id
            ])->with('error', $errorMsg);
          }
          if ($u_extension != $extension) {
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_caste_arr['doc_name'];
            return redirect()->route('edit-beneficiary-details', [
              'id' => $ben_id,
              'scheme_id' => $scheme_id
            ])->with('error', $errorMsg);
          }

          $base64 = base64_encode($img_data);
          $ip_address = request()->ip();
          $c_datetime = date('Y-m-d H:i:s', time());
          $user_id = Auth::user()->id;


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
                in_created_by_dist_code => " . $district_code . ",
                in_created_by_local_body_code => " . $blk_Ulb_code . ",
                in_doc_type_name => '" . $doc_caste_arr['doc_name'] . "',
                in_datetime => '" . $c_datetime . "'
                );"
          );
          $is_caste_upload = $is_caste_upload[0]->ben_docs_insert_archive;
        }

        //Upload Disability Document
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
              return redirect()->route('edit-beneficiary-details', [
                'id' => $ben_id,
                'scheme_id' => $scheme_id
              ])->with('error', $errorMsg);
            }
          } else if (strtolower($mime_type) == 'image/png') {
            $extension = 'png';
          } else if (strtolower($mime_type) == 'image/gif') {
            $extension = 'gif';
          } else if (strtolower($mime_type) == 'application/pdf') {
            $extension = 'pdf';
          } else {
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_disability_arr['doc_name'];
            return redirect()->route('edit-beneficiary-details', [
              'id' => $ben_id,
              'scheme_id' => $scheme_id
            ])->with('error', $errorMsg);
          }
          if ($u_extension != $extension) {
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_disability_arr['doc_name'];
            return redirect()->route('edit-beneficiary-details', [
              'id' => $ben_id,
              'scheme_id' => $scheme_id
            ])->with('error', $errorMsg);
          }

          $base64 = base64_encode($img_data);
          $ip_address = request()->ip();
          $c_datetime = date('Y-m-d H:i:s', time());
          $user_id = Auth::user()->id;


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
                in_created_by_dist_code => " . $district_code . ",
                in_created_by_local_body_code => " . $blk_Ulb_code . ",
                in_doc_type_name => '" . $doc_disability_arr['doc_name'] . "',
                in_datetime => '" . $c_datetime . "'
                );"
          );

          $is_disability_upload = $is_disability_upload[0]->ben_docs_insert_archive;

        }

        //Husband Death Document
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
              return redirect()->route('edit-beneficiary-details', [
                'id' => $ben_id,
                'scheme_id' => $scheme_id
              ])->with('error', $errorMsg);
            }
          } else if (strtolower($mime_type) == 'image/png') {
            $extension = 'png';
          } else if (strtolower($mime_type) == 'image/gif') {
            $extension = 'gif';
          } else if (strtolower($mime_type) == 'application/pdf') {
            $extension = 'pdf';
          } else {
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_husband_arr['doc_name'];
            return redirect()->route('edit-beneficiary-details', [
              'id' => $ben_id,
              'scheme_id' => $scheme_id
            ])->with('error', $errorMsg);
          }
          if ($u_extension != $extension) {
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_husband_arr['doc_name'];
            return redirect()->route('edit-beneficiary-details', [
              'id' => $ben_id,
              'scheme_id' => $scheme_id
            ])->with('error', $errorMsg);
          }

          $base64 = base64_encode($img_data);
          $ip_address = request()->ip();
          $c_datetime = date('Y-m-d H:i:s', time());
          $user_id = Auth::user()->id;


          $is_husband_upload = DB::connection('pgsql_encwrite')->select("SELECT jb_doc.ben_docs_insert_archive(
                in_beneficiary_id => " . $ben_id . ",
                in_scheme_id => " . $scheme_id . ",
                in_document_type => " . $this->husband_doc . ",
                in_attched_document => '" . $base64 . "',
                in_created_by_level => '" . $mapping_level . "',
                in_created_by => " . Auth::user()->id . ",
                in_ip_address => '" . request()->ip() . "',
                in_document_extension => '" . $extension . "',
                in_document_mime_type => '" . $mime_type . "',
                in_created_by_dist_code => " . $district_code . ",
                in_created_by_local_body_code => " . $blk_Ulb_code . ",
                in_doc_type_name => 'Husband Death Certificate',
                in_datetime => '" . $c_datetime . "'
                );"
          );
          $is_husband_upload = $is_husband_upload[0]->ben_docs_insert_archive;

        }


        $benAadharCheck = 0;
        $benMobileCheck = 0;
        $benBankCheck = 0;
        $benCasteCategoryCheck = 0;
        $benAadharCheckCross = 0;
        $benMobileCheckCross = 0;
        $benBankCheckCross = 0;
        $benCasteCategoryCheckCross = 0;


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



        if ($benAadharCheck > 0) {
          $errorMsg = "This Aadhar No: " . $request->new_aadhar_no . " is already exist.";
          return redirect()->route('incomplete-details-verifier-view', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
        } elseif ($benMobileCheck > 0) {
          $errorMsg = "This Mobile No: " . $request->new_mobile_no . " is already exist.";
          return redirect()->route('incomplete-details-verifier-view', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
        } elseif ($benBankCheck > 0) {
          $errorMsg = "This Bank Account: " . $request->bank_code . " is already exist.";
          return redirect()->route('incomplete-details-verifier-view', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
        } elseif ($benCasteCategoryCheck > 0) {
          $errorMsg = "This Caste Certificate No: " . $request->new_caste_certificate_no . " is already exist.";
          return redirect()->route('incomplete-details-verifier-view', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
        } elseif ($benAadharCheckCross > 0) {
          $errorMsg = "This Aadhar No: " . $request->new_aadhar_no . " is already exist on Cross Scheme.";
          return redirect()->route('incomplete-details-verifier-view', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
        } elseif ($benMobileCheckCross > 0) {
          $errorMsg = "This Mobile No: " . $request->new_mobile_no . " is already exist on Cross Scheme.";
          return redirect()->route('incomplete-details-verifier-view', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
        } elseif ($benBankCheckCross > 0) {
          $errorMsg = "This Bank Account: " . $request->bank_code . " is already exist on Cross Scheme.";
          return redirect()->route('incomplete-details-verifier-view', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
        } elseif ($benCasteCategoryCheckCross > 0) {
          $errorMsg = "This Caste Certificate No: " . $request->new_caste_certificate_no . " is already exist on Cross Scheme.";
          return redirect()->route('incomplete-details-verifier-view', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', $errorMsg);
        }

        if (
          ($benAadharCheck == 0 && $benMobileCheck == 0 && $benBankCheck == 0 && $benCasteCategoryCheck == 0
            && $benAadharCheckCross == 0 && $benMobileCheckCross == 0 && $benBankCheckCross == 0 && $benCasteCategoryCheckCross == 0)
          || ($is_aadhar_upload == 1 || $is_bank_upload == 1 || $is_caste_upload == 1 || $is_disability_upload == 1 || $is_husband_upload == 1)
        ) {
          $updateArray = $this->getUpdateCode($row, $request, 1);
          if (!empty($updateArray)) {
            foreach ($updateArray as $updatecode) {
              $accept_reject_info_model = new AcceptRejectInfo;
              $accept_reject_info_model->scheme_id = $scheme_id;
              $accept_reject_info_model->created_by_dist_code = $district_code;
              $accept_reject_info_model->created_by_local_body_code = $blk_Ulb_code;
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
          } else {
            return redirect('incomplete-details-verifier-view')->with('error', 'No changes made.......');
          }

          if (isset($row->is_bank_failed) && in_array($row->is_bank_failed, [1, 2, 3])) {
            if ($isSameBank == 0 || $isSameIfsc == 0) {
              $paymentModel->last_accno = trim($request->new_bank_code);
              $paymentModel->last_ifsc = $request->bank_ifsc_code;
              $paymentModel->npci_bank_code = $bankDetails->bank_code;
              if ($isSameMobile == 0) {
                $paymentModel->mobile_no = trim($request->new_mobile_no);
              }
              $updateBenPaymentTable = $paymentModel->save();
            } else {
              $updateBenPaymentTable = 1;
            }
          }else{
            $updateBenPaymentTable = 1;
          }

          if (isset($benFailedModel)) {
            if ($row->dup_bank == 1 || in_array($row->is_bank_failed, [1, 2, 3]) || ($row->is_bank_failed == 2 && $request->process_type == 2)) {
              if (in_array($benFailedModel->failed_type, [1, 2, 3, 4, 5])) {
                $getNpciBankCode = BankDetails::where('ifsc', $request->bank_ifsc_code)->first();
                if ($getNpciBankCode) {
                  $newPaymentDetails = [
                    'new_bank_name' => trim($request->new_bank_name),
                    'new_bank_branch' => trim($request->new_bank_branch),
                    'new_bank_ifsc' => $request->bank_ifsc_code,
                    'new_bank_code' => trim($request->new_bank_code),
                    'npci_bank_code' => $getNpciBankCode->bank_code
                  ];
                  $benFailedModel->updated_details = json_encode($newPaymentDetails);
                  $benFailedModel->edited_status = 1;
                  $benFailedModel->updated_at = date('Y-m-d H:i:s');
                  $updateFailedPayment = $benFailedModel->save();
                }
              }
            }
          } else {
            $updateFailedPayment = 1;
          }

          $updateBenTable = $benEntry_model->save();

          if ($bankDupCount > 0) {
            $ben_dup_bank_code_details = BenDupBankCodePayemntDetails::where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', -97)->first();
            if ($ben_dup_bank_code_details) {
              $ben_dup_bank_code_details->bank_code = trim($request->new_bank_code);
              $ben_dup_bank_code_details->bank_name = trim($request->new_bank_name);
              $ben_dup_bank_code_details->branch_name = trim($request->new_bank_branch);
              $ben_dup_bank_code_details->bank_ifsc = $request->bank_ifsc_code;
              $ben_dup_bank_code_details->next_level_role_id = ($isSameBank == 1) ? 200 : 101;
              $updateBankDup = $ben_dup_bank_code_details->save();
            }
          } else {
            $updateBankDup = 1;
          }

          $benRejectLog = 1;
          if (!empty($request->process_type) && $request->process_type == 3) {
            $benRejectRequest = new BenRejectRequest;
            $benRejectRequest->ben_id = $ben_id;
            $benRejectRequest->scheme_id = $scheme_id;
            $benRejectRequest->rejected_by = Auth::user()->id;
            $benRejectRequest->rejected_ip = request()->ip();
            $benRejectRequest->rejected_date = date('Y-m-d H:i:s');
            $benRejectRequest->next_level_clean_id = 2;
            $benRejectLog = $benRejectRequest->save();
          }

          if ($isIncompleteLog && $updateBenDetailsAction && $updateBenTable && $updateBenPaymentTable && $updateBankDup && $updateFailedPayment && $benRejectLog) {
            DB::commit();
            return redirect()->route('incomplete-details-verifier-view')->with('success', "Beneficiary({$row->id}) has successfully verified & forwarded for approval.");
          } else {
            DB::rollback();
            return redirect()->route('incomplete-details-verifier-view')->with('error', "Beneficiary can't be updated. Something went wrong.");
          }
        } else {
          DB::rollback();
          return redirect()->route('incomplete-details-verifier-view', ['id' => $ben_id, 'scheme_id' => $scheme_id])->with('error', "Something went wrong.");
        }



      } catch (\Exception $e) {
        dd($e);
        return redirect()->back()->with('error', 'Something Error..... Please Try again');

      }
    } else {
      return redirect("/")->with('danger', 'User Disabled. ');
    }
  }

  public function aadharDupCheck(Request $request)
  {
    if (!$request->ajax()) {
      return response()->json(['error' => 'Invalid request.'], 400);
    }

    try {
      $aadhar_no = trim($request->aadhar_no);
      $aadharSameVal = $request->aadharSameVal;
      $ben_id = $request->ben_id;

      if ($aadharSameVal == 1) {
        $checkAadharCount = BenEntry::where(DB::raw("TRIM(aadhar_no)"), $aadhar_no)
          ->where('id', '!=', $ben_id)
          ->whereIn('is_clean', [1, 2])
          ->count();

        if ($checkAadharCount > 0) {
          return response()->json([
            'status' => 1,
            'msg' => 'Aadhar number already exists. Please modify or reject the duplicate one.',
            'type' => 'red',
            'icon' => 'fa fa-warning',
            'title' => 'Warning!!'
          ]);
        }
      }

      return response()->json([
        'status' => 0,
        'msg' => 'No Duplicate Found',
        'type' => 'green',
        'icon' => 'fa fa-check-circle',
        'title' => 'Success!!'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'exception' => true,
        'exception_message' => $e->getMessage(),
      ], 400);
    }
  }


  public function bankDupCheck(Request $request)
  {
    if (!$request->ajax()) {
      return response()->json(['error' => 'Invalid request.'], 400);
    }

    try {
      $bank_code = trim($request->bank_code);
      $bank_ifsc = trim($request->bank_ifsc);
      $bankSameVal = $request->bankSameVal;
      $ben_id = $request->ben_id;

      if ($bankSameVal == 1) { // Fixed incorrect comparison value (was 2)
        $checkBankCount = DB::connection('pgsql')
          ->table('pension.beneficiaries')
          ->where(DB::raw("TRIM(bank_code)"), $bank_code)
          ->where(DB::raw("TRIM(bank_ifsc)"), $bank_ifsc)
          ->where('id', '!=', $ben_id) // Exclude current beneficiary ID
          ->whereIn('is_clean', [1, 2])
          ->count();

        if ($checkBankCount > 0) {
          return response()->json([
            'status' => 1,
            'msg' => 'Bank A/c already exists! Please modify or reject the duplicate one.',
            'type' => 'red',
            'icon' => 'fa fa-warning',
            'title' => 'Warning!!',
          ]);
        }
      }

      return response()->json([
        'status' => 0,
        'msg' => 'No Duplicate Found',
        'type' => 'green',
        'icon' => 'fa fa-check-circle',
        'title' => 'Success!!',
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'exception' => true,
        'exception_message' => $e->getMessage(),
      ], 400);
    }
  }


  public function mobileDupCheck(Request $request)
  {
    if (!$request->ajax()) {
      return response()->json(['error' => 'Invalid request.'], 400);
    }

    try {
      $mobile_no = trim($request->mobile_no);
      $mobileSameVal = $request->mobileSameVal;
      $ben_id = $request->ben_id;

      if ($mobileSameVal == 1) { // Fixed incorrect comparison value (was 3)
        $checkMobileCount = DB::connection('pgsql')
          ->table('pension.beneficiaries')
          ->where(DB::raw("TRIM(mobile_no)"), $mobile_no)
          ->where('id', '!=', $ben_id) // Exclude current beneficiary ID
          ->whereIn('is_clean', [1, 2])
          ->count();

        if ($checkMobileCount > 0) {
          return response()->json([
            'status' => 1,
            'msg' => 'Mobile number already exists! Please modify or reject the duplicate one.',
            'type' => 'red',
            'icon' => 'fa fa-warning',
            'title' => 'Warning!!',
          ]);
        }
      }

      return response()->json([
        'status' => 0,
        'msg' => 'No Duplicate Found',
        'type' => 'green',
        'icon' => 'fa fa-check-circle',
        'title' => 'Success!!',
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'exception' => true,
        'exception_message' => $e->getMessage(),
      ], 400);
    }
  }


  private function getDocCount($ben_id, $scheme_id, $dist_code, $doc_type)
  {
    $doc_count = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $ben_id)->where('scheme_id', $scheme_id)->where('document_type', $doc_type)->count();
    if ($doc_count > 0) {
      return $doc_count;
    } else {
      return 0;
    }
  }
  private function getFormVisible($ben_id, $scheme_id)
  {
    $count = BenEntry::where('id', $ben_id)->where('scheme_id', $scheme_id)->where('next_level_role_id', '>=', '0')->where('is_incomplete', 1)->count();
    if ($count > 0) {
      return 1;
    } else {
      return 0;
    }
  }

  private function getIncompleteData($ben_id, $scheme_id)
  {
    $data = BenEntry::where('id', $ben_id)
      ->where('scheme_id', $scheme_id)
      ->first();
    $incomplete_array = [];

    if ($data->dup_aadhar == 1) {
      array_push($incomplete_array, 'Duplicate Aadhar Number');
    }
    if ($data->no_aadhar == 1) {
      array_push($incomplete_array, 'No Aadhar Number');
    }
    if ($data->dup_bank == 1) {
      array_push($incomplete_array, 'Duplicate Bank Account Number');
    }
    if ($data->dup_mobile == 1) {
      array_push($incomplete_array, 'Duplicate Mobile Number');
    }
    if ($data->no_mobile == 1) {
      array_push($incomplete_array, 'No Mobile Number');
    }
    if ($data->is_bank_failed == 1) {
      array_push($incomplete_array, 'Bank Transaction Failed');
    }
    if ($data->is_bank_failed == 2) {
      array_push($incomplete_array, 'Name Validation Failed');
    }
    if ($data->is_bank_failed == 3) {
      array_push($incomplete_array, 'A/c Validation Failed');
    }
    if ($data->no_ration_card == 1) {
      array_push($incomplete_array, 'No Ration Card');
    }
    if ($data->no_epic_voter == 1) {
      array_push($incomplete_array, 'No Epic Voter Card');
    }

    if ($data->is_incomplete == 1) {
      switch ($data->scheme_id) {
        case 1:
        case 3:
          array_push($incomplete_array, 'Caste Category', 'Caste Certificate Number', 'Caste Certificate Document');
          break;
        case 2:
          array_push($incomplete_array, 'Disability Type', 'Percentage of Disability', 'Authority Name', 'Authority Designation', 'Disability Certificate from Appropriate Authority');
          break;
        case 11:
          array_push($incomplete_array, 'Husband Death Document', 'Husband Name');
          break;
      }
    }

    return $incomplete_array;
  }

  private function validateInput($row, $request)
  {
    $bank_doc_count = BenDocs::where('beneficiary_id', $row->id)->where('document_type', $this->bank_doc)->count();
    $doc_aadhar_arr = DataInsert::getDocumentDetails($this->aadhar_doc);
    $doc_bank_arr = DataInsert::getDocumentDetails($this->bank_doc);
    $doc_caste_arr = DataInsert::getDocumentDetails($this->caste_doc);
    $doc_disability_arr = DataInsert::getDocumentDetails($this->disability_doc);
    $doc_epic_arr = DataInsert::getDocumentDetails($this->epic_doc);
    $doc_ration_arr = DataInsert::getDocumentDetails($this->ration_doc);
    $doc_husband_arr = DataInsert::getDocumentDetails($this->husband_doc);

    $rules = array();
    $messages = array();
    $attributes = array();

    if ($row->dup_bank == 1 || in_array($row->is_bank_failed, [1, 3])) {
      $rules['old_bank_code'] = 'required|max:20';
      $rules['old_bank_ifsc'] = 'required|max:11';
      $rules['old_bank_name'] = 'required';
      $rules['old_bank_branch'] = 'required';
      $rules['new_bank_branch'] = 'required';
      $rules['new_bank_name'] = 'required';
      $rules['new_bank_code'] = 'required|max:20';
      $rules['new_bank_ifsc'] = 'required|max:11';
    }

    if ($row->is_bank_failed == 2) {
      $rules['old_bank_code'] = 'required';
      $rules['old_bank_ifsc'] = 'required';
    }

    if ($row->is_bank_failed == 2 && $request->process_type == 2) {
      $rules['old_bank_code'] = 'required|max:20';
      $rules['old_bank_ifsc'] = 'required|max:11';
      $rules['old_bank_name'] = 'required';
      $rules['old_bank_branch'] = 'required';
      $rules['new_bank_branch'] = 'required';
      $rules['new_bank_name'] = 'required';
      $rules['new_bank_code'] = 'required|max:20';
      $rules['new_bank_ifsc'] = 'required|max:11';
    }

    if ($row->no_aadhar == 1) {
      $rules['new_aadhar_no'] = 'required|digits:12';
      $rules['old_aadhar'] = 'nullable|digits:12';
      $rules['new_aadhar_doc'] = 'required';
    }

    if ($row->dup_aadhar == 1) {
      $rules['new_aadhar_no'] = 'required|digits:12';
      $rules['old_aadhar'] = 'nullable|digits:12';
      $rules['new_aadhar_doc'] = 'required';
    }

    if ($row->no_mobile == 1 || $row->is_bank_failed == 1) {
      $rules['new_mobile_no'] = 'required|digits:10';
      $rules['old_mobile_no'] = 'nullable|digits:10';
    }

    if ($row->is_incomplete == 1) {
      if ($row->scheme_id == 1 || $row->scheme_id == 3) {
        $rules['new_caste_category'] = 'required';
        $rules['new_caste_certificate_no'] = 'required';
        $rules['new_caste_certificate_doc'] = 'required';
      } elseif ($row->scheme_id == 2) {
        $rules['new_disability_type'] = 'required';
        $rules['new_disability_type_percentage'] = 'required';
        $rules['new_disability_type_authority'] = 'required';
        $rules['new_disability_designation'] = 'required';
        $rules['new_disability_doc'] = 'required';
      } elseif ($row->scheme_id == 11) {
        $rules['new_husband_first_name'] = 'required';
        $rules['new_husband_middle_name'] = 'nullable';
        $rules['new_husband_last_name'] = 'required';
        $rules['new_husband_death_doc'] = 'required';
      }
    }

    if ($doc_aadhar_arr && ($row->no_aadhar == 1 || $row->dup_aadhar == 1)) {
      $rules['new_aadhar_doc'] = 'required|mimes:' . $doc_aadhar_arr['doc_type'] . '|max:' . $doc_aadhar_arr['doc_size_kb'];
    }

    if ($doc_bank_arr && $bank_doc_count == 0 || ($row->dup_bank == 1 || in_array($row->is_bank_failed, [1, 3]))) {
      $rules['new_bank_doc'] = 'required|mimes:' . $doc_bank_arr['doc_type'] . '|max:' . $doc_bank_arr['doc_size_kb'];
    }

    if ($doc_caste_arr && $row->is_incomplete == 1 && in_array($row->scheme_id, [1, 3])) {
      $rules['new_caste_certificate_doc'] = 'required|mimes:' . $doc_caste_arr['doc_type'] . '|max:' . $doc_caste_arr['doc_size_kb'];
    }

    if ($doc_disability_arr && $row->is_incomplete == 1 && $row->scheme_id == 2) {
      $rules['new_disability_doc'] = 'required|mimes:' . $doc_disability_arr['doc_type'] . '|max:' . $doc_disability_arr['doc_size_kb'];
    }

    if ($doc_husband_arr && $row->is_incomplete == 1 && $row->scheme_id == 11) {
      $rules['new_husband_death_doc'] = 'required|mimes:' . $doc_husband_arr['doc_type'] . '|max:' . $doc_husband_arr['doc_size_kb'];
    }

    $messages = [
      'required' => 'The :attribute field is required.',
      'max' => 'Total :max characters allowed for :attribute.',
      'digits' => 'The :attribute must be exactly :digits digits.',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    $return_arr = ['is_valid' => $validator->passes(), 'errors' => $validator->errors()->all()];

    return $return_arr;
  }

  public function getUpdateCode($row, $request, $role)
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






}
