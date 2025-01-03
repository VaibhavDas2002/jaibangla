<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\District;
use Illuminate\Support\Facades\Auth;
use App\Scheme;
use App\PensionSc;
use App\PensionSt;
use App\UrbanBody;
use App\Taluka;
use App\Ward;
use App\GP;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\DocumentType;
use App\SchemeDocMap;
use App\Assembly;
use App\BenDocsSc;
use App\BenDocsSt;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\DSRejecedApplicationSc;
use App\DSRejecedApplicationSt;
use App\PensionLPPRetainer;
use App\PensionLPPPensioner;
use App\Helpers\Helper;
class LPPShortEntryFormController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->base_dob_chk_date = date("Y-m-d");
  }
  public function index(Request $request)
  {
    return redirect('/')->with('error', 'As directed by Director of Information data entry is suspended.');
    $is_active = 0;
    $pr1 = $request->pr1;
    if (empty($pr1)) {
      return redirect("/")->with('error', 'Input not valid');
    }
    if (!in_array($pr1, array('lokprasar_retainer', 'lokprasar_pensioner'))) {
      return redirect("/")->with('error', 'Input not valid');
    }
    $scheme_row = Scheme::select('id', 'scheme_name')->where('short_code', $pr1)->first();
    if (empty($scheme_row)) {
      return redirect("/")->with('error', 'Input not valid');
    }
    $scheme_id = $scheme_row->id;
    $scheme_name = $scheme_row->scheme_name;
    $is_active = 0;
    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();;
    // dd($roleArray);
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $request->session()->put('level', $roleObj['mapping_level']);
        $request->session()->put('distCode', $roleObj['district_code']);
        $district_code = $roleObj['district_code'];
        $is_urban = $roleObj['is_urban'];
        if ($roleObj['is_urban'] == 1) {
          $request->session()->put('blockCode', $roleObj['urban_body_code']);
          $sel_urban_body_code = $roleObj['urban_body_code'];
        } else if ($roleObj['is_urban'] == 2) {
          $request->session()->put('blockCode', $roleObj['taluka_code']);
          $sel_urban_body_code = $roleObj['taluka_code'];
        }
        break;
      }
    }
    if ($is_active == 0) {
      return redirect("/")->with('success', 'User Disabled for scheme ' . $scheme_name);
    }
    $districts = District::get();
    $district_row =  $districts->where('district_code', $district_code)->first();
    $district_name = $district_row->district_name;
    return view(
      'lpp-shortEntryForm/index',
      [
        'sel_district' => $district_code, 'sel_rural_urban' => $is_urban, 'sel_urban_body_code' => '', 'districts' => $districts, 'district_name' => $district_name, 'scheme_id' => $scheme_id, 'pr1' => $pr1, 'scheme_name' => $scheme_name, 'district_code' => $district_code
      ]
    );
  }

  public function store(Request $request)
  {
    return redirect('/')->with('error', 'As directed by Director of Information data entry is suspended.');
    $is_active = 0;
    $scheme_id = $request->scheme_id;
    if (empty($scheme_id)) {
      return redirect("/")->with('error', 'Input not valid');
    }
    if (!in_array($scheme_id, array(8, 9))) {
      return redirect("/")->with('error', 'Input not valid');
    }
    $scheme_row = Scheme::select('id', 'scheme_name', 'short_code')->where('id', $scheme_id)->first();
    if (empty($scheme_row)) {
      return redirect("/")->with('error', 'Input not valid');
    }
    $scheme_id = $scheme_row->id;
    $scheme_name = $scheme_row->scheme_name;
    $is_active = 0;
    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();;
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $request->session()->put('level', $roleObj['mapping_level']);
        $request->session()->put('distCode', $roleObj['district_code']);
        $mapping_level = $roleObj['mapping_level'];
        $district_code = $roleObj['district_code'];
        if ($roleObj['is_urban'] == 1) {
          $blockCode = $roleObj['urban_body_code'];
          $request->session()->put('blockCode', $roleObj['urban_body_code']);
        } else if ($roleObj['is_urban'] == 2) {
          $request->session()->put('blockCode', $roleObj['taluka_code']);
          $blockCode = $roleObj['taluka_code'];
        }
        break;
      }
    }
    if ($is_active == 0) {
      return redirect("/")->with('success', 'User Disabled for scheme ' . $scheme_name);
    }
    $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
    $bankCount_r = PensionLPPRetainer::whereRaw("trim(bank_code)=trim(" . "'" . $request->bank_account_number . "'" . ")")->whereRaw("trim(bank_ifsc)=trim(" . "'" . $request->bank_ifsc_code . "'" . ")")
      ->whereRaw("(" . $check_condition_str . ")")
      ->count('id');
  
    $bankCount_p = PensionLPPPensioner::whereRaw("trim(bank_code)=trim(" . "'" . $request->bank_account_number . "'" . ")")->whereRaw("trim(bank_ifsc)=trim(" . "'" . $request->bank_ifsc_code . "'" . ")")
      ->whereRaw("(" . $check_condition_str . ")")
      ->count('id');
    if ($bankCount_r > 0 || $bankCount_p > 0) {
      return redirect("/")->with('error', 'Bank A/C Already Exist!');
    }
    $this->validateInputShort($request);
    // dd('Success');
    $scheme_id = $request->scheme_id;
    if ($scheme_id == 8) {
      $pension_details = new PensionLPPRetainer();
      $pr1 = $scheme_row->short_code;
      $pension_details->pensioner_type = 'RETAINER';
    } else if ($scheme_id == 9) {
      $pension_details = new PensionLPPPensioner();
      $pr1 = $scheme_row->short_code;
      $pension_details->pensioner_type = 'PENSIONER';
    } else {
      return redirect("/")->with('success', 'Scheme Not Valid');
    }
    if ($request->urban_code == 1) {
      $pension_details->rural_urban_id = 1;
      if (!empty($request->block)) {
        $pension_details->block_ulb_code = $request->block;
        $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
        $pension_details->block_ulb_name = $block_ulb->urban_body_name;
        $pension_details->created_by_local_body_code = $block_ulb->sub_district_code;
      }
      if (!empty($request->gp_ward)) {
        $pension_details->gp_ward_code = $request->gp_ward;
        $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();
        $pension_details->gp_ward_name   = $gp_ward->urban_body_ward_name;
      }
    } else if ($request->urban_code == 2) {
      $pension_details->rural_urban_id = 2;
      if (!empty($request->block)) {
        $pension_details->block_ulb_code = $request->block;
        $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
        $pension_details->block_ulb_name = $block_ulb->block_name;
        $pension_details->created_by_local_body_code = $request->block;
      }
      if (!empty($request->gp_ward)) {
        $pension_details->gp_ward_code = $request->gp_ward;
        $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();
        $pension_details->gp_ward_name   = $gp_ward->gram_panchyat_name;
      }
    }
    if (!empty($request->mobile_no)) {
      $mobile_no = $request->mobile_no;
    } else {
      $mobile_no = NULL;
    }

    $pension_details->scheme_id = $scheme_id;
    $pension_details->ben_fname = trim($request->first_name);
    if (!empty(trim($request->middle_name)))
      $pension_details->ben_mname = $request->middle_name;
    if (!empty(trim($request->last_name)))
      $pension_details->ben_lname = $request->last_name;
    if (!empty(trim($request->district)))
      $pension_details->dist_code = $request->district;

    $pension_details->mobile_no = $mobile_no;
    $pension_details->gender = $request->gender;
    $pension_details->dob = $request->dob;
    $diff = Carbon::parse($request->dob)->diffInYears($this->base_dob_chk_date);
    $pension_details->ben_age = $diff;
    $pension_details->folk_form = $request->folk_form;
    $pension_details->caste = $request->caste;

    $pension_details->created_by = Auth::user()->id;
    $pension_details->created_by_level = $mapping_level;
    $pension_details->created_by_dist_code = $district_code;
    // $pension_details->created_by_local_body_code = $blockCode;

    $pension_details->bank_code = trim($request->bank_account_number);
    $pension_details->branch_name = trim($request->bank_branch);
    $pension_details->bank_ifsc = trim($request->bank_ifsc_code);
    $pension_details->bank_name = trim($request->name_of_bank);

    $pension_details->ds_phase = NULL;

    try {
      $is_saved = $pension_details->save();
      if ($is_saved) {
        $id = $pension_details->benid;
        return redirect("lppShortEntryForm?pr1=" . $pr1)->with('success', 'Application Submitted Successfully')
          ->with('id',  $id);
      } else {
        return redirect("lppShortEntryForm?pr1=" . $pr1)->with('error', 'Something wrong please try again.');
      }
    } catch (\Exception $e) {
      //dd($e);
      return redirect("lppShortEntryForm?pr1=" . $pr1)->with('error', 'Something wrong please try again.');
    }
  }

  private function validateInputShort($request)
  {
    $singleArray = array();
    $nicenameArray = array();
    $customMessage = array();
    $nicenameArray['first_name'] = 'First Name';
    $nicenameArray['middle_name'] = 'Middle Name';
    $nicenameArray['last_name'] = 'Last Name';
    $nicenameArray['mobile_no'] = 'Mobile Number';
    $nicenameArray['gender'] = 'Gender';
    $nicenameArray['dob'] = 'Date-of-Birth';
    $nicenameArray['folk_form'] = 'Folk Form';
    $nicenameArray['caste'] = 'Caste';

    $nicenameArray['district'] = 'District';
    $nicenameArray['urban_code'] = 'Rural/ Urban';
    $nicenameArray['block'] = 'Block/Municipality/Corp';
    $nicenameArray['gp_ward'] = 'GP/Ward No'; 

    $nicenameArray['name_of_bank'] = 'Bank Name';
    $nicenameArray['bank_branch'] = 'Branch Name';
    $nicenameArray['bank_account_number'] = 'Bank Account Number';
    $nicenameArray['bank_ifsc_code'] = 'Bank IFSC'; 


    $this->validate($request, array_merge([
      'first_name' => 'required|string|max:200',
      'middle_name' => 'string|nullable',
      'last_name' => 'string|nullable|max:200',
      'mobile_no' => 'nullable|size:10',
      'gender' => 'required',
      'dob' => 'required',
      'folk_form' => 'required',
      'caste' => 'nullable',

      'district' => 'required|numeric',
      'urban_code' => 'required|in:1,2',
      'block' => 'required|numeric',
      'gp_ward' => 'nullable|numeric',

      'name_of_bank' => 'required|string|max:200',
      'bank_branch' => 'required|string|max:200',
      'bank_account_number' => 'required|numeric',
      'bank_ifsc_code' => 'required|string|max:11',

    ], $singleArray), $customMessage, $nicenameArray);
  }
}
