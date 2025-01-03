<?php

namespace App\Http\Controllers;

use App\BenEntry;
use Illuminate\Http\Request;
//use App\Http\Controllers\Redirect;
use App\programmeHeadMaster;
use App\majorProgammeHeadMaster;
use App\nhm_employee_details;
use App\designationMaster;
use App\nhm_service_category;
use App\NHMEmployee;
use App\Configduty;
use App\District;
use App\nhm_posting_level;
use App\nhm_level_place;
use App\nhm_health_facility;
use App\UrbanBody;
use App\SubDistrict;

use App\PensionPurohitHousingICAD;
use App\BenDocsPurohitHousingICAD;
use App\BenDocsArcPurohitHousingICAD;


use App\BenDocsPurohitMonthlyICAD;
use App\BenDocsArcPurohitMonthlyICAD;

use App\SchemecodeStatic;

use App\DocumentType;
use App\SchemeDocMap;
//Dynamic Doc End
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Scheme;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\BankDetails;
use App\Helpers\Helper;
use App\BenDocs;
use App\AcceptRejectInfo;
use App\RejectRevertReason;
use App\MapLavel;
use App\Helpers\AuthChecker;

class PurohitICADformController extends Controller
{
  protected $pr1ListPurohit;

  public function __construct()
  {
    $this->middleware('auth');
    $arr = SchemecodeStatic::getpr1ListPurohit();
    $this->pr1ListPurohit = $arr;
    date_default_timezone_set('Asia/Kolkata');
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $code = '';
    $scheme_id = '';
    $document_msg = '';
    $doc_profile_image_id = '';
    $doc_list_man = array();
    $doc_list_opt = array();
    $districts = array();
    $monthlySlug = $this->pr1ListPurohit['monthly']['slug'];
    $housingSlug = $this->pr1ListPurohit['housing']['slug'];
    $monthlySchemeCode = $this->pr1ListPurohit['monthly']['scheme_code'];
    $housingSchemeCode = $this->pr1ListPurohit['housing']['scheme_code'];
    $is_active = 0;
    $code = $request->code;

    if ($code == $monthlySlug) {
      $scheme_id = $monthlySchemeCode;
    } else if ($code == $housingSlug) {
      $scheme_id = $housingSchemeCode;
    } else
      $scheme_id = $code;
    $valid = true;
    if ($code != '') {
      //echo $housingSlug;die;
      $valid = false;
      if (in_array($code, array($monthlySlug, $housingSlug))) {
        $valid = true;
      }
    }
    if ($valid) {
      if ($code != '') {
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        foreach ($roleArray as $roleObj) {
          if ($roleObj['scheme_id'] == $scheme_id) {
            $is_active = 1;
            $request->session()->put('level', $roleObj['mapping_level']);
            $request->session()->put('distCode', $roleObj['district_code']);
            if ($roleObj['is_urban'] == 1) {
              $request->session()->put('blockCode', $roleObj['urban_body_code']);
            } else {
              $request->session()->put('blockCode', $roleObj['taluka_code']);
            }
            break;
          }
        }
      } else {
        $is_active = 1;
      }
      if ($is_active == 1) {
        if ($code != '') {
          $districts = District::all();
          $document_msg = "";
          $doc_profile_image = DocumentType::get()
            ->where("is_profile_pic", true)->first();

          $doc_profile_image_id = 999;
          if ($doc_profile_image) {
            $doc_profile_image_id = $doc_profile_image->id;
          }
          $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->first()->toArray();
          if (!empty($doc_id_list['doc_list_man']))
            $doc_list_man = DocumentType::select('id', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->whereIn("id", json_decode($doc_id_list['doc_list_man']))->get()->toArray();
          else
            $doc_list_man = array();
          if (!empty($doc_id_list['doc_list_opt']))
            $doc_list_opt = DocumentType::select('id', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->whereIn("id", json_decode($doc_id_list['doc_list_opt']))->get()->toArray();
          else
            $doc_list_opt = array();
          if (!empty($doc_id_list['doc_list_man_group']))
            $doc_list_man_group = json_decode($doc_id_list['doc_list_man_group']);
          else
            $doc_list_man_group = array();
          //dd($doc_list_man_group);
          if (count($doc_list_man_group) > 0) {
            $doc_list = array_merge($doc_list_man, $doc_list_opt);
            $all_doc_id = array();
            foreach ($doc_list as $mDoc) {
              array_push($all_doc_id, $mDoc['id']);
            }
            // dd($all_doc_id);
            if (count($doc_list)) {
              foreach ($doc_list_man_group as $man_group) {
                $document_msg .= '<div  class="form-group col-md-12" >';
                $heading_msg = "At least one document must be uploaded for ";
                $doucument_group_name = $this->getGroupName($man_group);
                $heading_msg .= '<span style="color:red;font-weight:bold">' . $doucument_group_name . '</span>';
                $document_msg .= "<p style='font-weight:bold;font-size:17px;'>" . $heading_msg . " </p>";
                $document_msg .= "<ul>";
                $results = DB::select("SELECT doc_name FROM m_attached_doc where id IN (" . implode(',', $all_doc_id) . ") and $man_group =any(doucument_group)");
                $results = json_decode(json_encode($results), true);


                //dd($results);
                if (count($results) > 0) {
                  $i = 0;
                  foreach ($results as $requiredmsg) {

                    $document_msg .= "<li style='font-weight:bold;'>" . $requiredmsg['doc_name'] . "</li>";
                    $i++;
                  }
                }


                $document_msg .= "</ul>";
                $document_msg .= "</div>";
              }
            } else
              $document_msg = "";
          } else
            $document_msg = "";
        }
        // dd($code);
        return view('PurohitICAD/addForm', [
          'monthlySlug' => $monthlySlug,
          'housingSlug' => $housingSlug,
          'code' => $code,
          'scheme_id' => $scheme_id,
          'districts' => $districts,
          'document_msg' => $document_msg,
          'doc_list_man' => $doc_list_man,
          'doc_list_opt' => $doc_list_opt,
          'profile_img' => $doc_profile_image_id
        ]);
      } else {
        return redirect("/")->with('success', 'User Disabled');
      }
    } else {
      return redirect("/")->with('success', 'Invalid Input');
    }
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $level = $request->session()->get('level');
    $distCode = $request->session()->get('distCode');
    $blockCode = $request->session()->get('blockCode');
    if (empty($level) || empty($distCode) || empty($blockCode)) {
      return redirect("/")->with('error', 'Something Wrong ..pleas try again.');
    }
    $monthlySlug = $this->pr1ListPurohit['monthly']['slug'];
    $monthlySchemeCode = $this->pr1ListPurohit['monthly']['scheme_code'];
    $monthlyMainTable = $this->pr1ListPurohit['monthly']['maintable'];
    $monthlyMainTable = "App\\" . $monthlyMainTable;
    $monthlyDocTable = $this->pr1ListPurohit['monthly']['doctable'];
    $monthlyDocTable = "App\\" . $monthlyDocTable;
    $monthlyDocArcTable = $this->pr1ListPurohit['monthly']['docarchtable'];
    $monthlyDocArcTable = "App\\" . $monthlyDocArcTable;

    $housingSlug = $this->pr1ListPurohit['housing']['slug'];
    $housingSchemeCode = $this->pr1ListPurohit['housing']['scheme_code'];
    $housingMainTable = $this->pr1ListPurohit['housing']['maintable'];
    $housingMainTable = "App\\" . $housingMainTable;
    $housingDocTable = $this->pr1ListPurohit['housing']['doctable'];
    $housingDocTable = "App\\" . $housingDocTable;
    $housingDocArcTable = $this->pr1ListPurohit['housing']['docarchtable'];
    $housingDocArcTable = "App\\" . $housingDocArcTable;

    $user_id = AuthChecker::getUserId();
    $users = User::find($user_id);
    //$server_ip =$_SERVER['SERVER_ADDR'];
    $base_url = url('/');
    $uploaded_doc = array();
    $destinationPath = storage_path('app/keep_ICAD/');
    $scheme_id = $request->scheme_id;
    // echo $scheme_id;die;
    $code = $request->code;
    $application_type = $request->application_type;
    $housing = 0;
    $monthly = 0;
    if ($application_type == $monthlySlug) {
      $monthly = 1;
      $housing = 0;
      $scheme_id = 17;
    }
    if ($application_type == $housingSlug) {
      $housing = 1;
      $monthly = 1;
      $scheme_id = 18;
    }
    $first_name = trim($request->first_name);
    $middle_name = trim($request->middle_name);
    if (empty($middle_name)) {
      $middle_name = '';
    }
    $last_name = trim($request->last_name);
    $gender = trim($request->gender);
    $app_phase = trim($request->app_phase);
    $temple_type = trim($request->temple_type);
    $caste_category = trim($request->caste_category);
    $dob = $request->dob;
    //echo $dob;die;
    $txt_age = trim($request->txt_age);
    $father_first_name = trim($request->father_first_name);
    $father_middle_name = trim($request->father_middle_name);
    if (empty($father_middle_name)) {
      $father_middle_name = NULL;
    }
    $father_last_name = trim($request->father_last_name);
    $mother_first_name = trim($request->mother_first_name);
    $mother_middle_name = trim($request->mother_middle_name);
    if (empty($mother_middle_name)) {
      $mother_middle_name = NULL;
    }
    $mother_last_name = trim($request->mother_last_name);
    $marital_status = trim($request->marital_status);
    if ($marital_status == 'Married') {
      $spouse_first_name = trim($request->spouse_first_name);
      if (empty($spouse_first_name)) {
        $spouse_first_name = NULL;
      }
      $spouse_middle_name = trim($request->spouse_middle_name);
      if (empty($spouse_middle_name)) {
        $spouse_middle_name = NULL;
      }
      $spouse_last_name = trim($request->spouse_last_name);
      if (empty($spouse_last_name)) {
        $spouse_last_name = NULL;
      }
    } else {
      $spouse_first_name = NULL;
      $spouse_middle_name = NULL;
      $spouse_last_name = NULL;
    }
    $ration_card_cat = trim($request->ration_card_cat);
    $ration_card_no = trim($request->ration_card_no);
    $aadhar_no = trim($request->aadhar_no);
    if (empty($aadhar_no)) {
      $aadhar_no = NULL;
    }
    $epic_voter_id = trim($request->epic_voter_id);
    $pan_no = trim($request->pan_no);
    if (empty($pan_no)) {
      $pan_no = NULL;
    }
    $district = trim($request->district);
    $asmb_cons = trim($request->asmb_cons);
    $police_station = trim($request->police_station);
    $urban_code = trim($request->urban_code);
    $block = trim($request->block);
    $gp_ward = trim($request->gp_ward);
    $village = trim($request->village);
    $house = trim($request->house);
    if (empty($house)) {
      $house = NULL;
    }
    $post_office = trim($request->post_office);
    $pin_code = trim($request->pin_code);
    $residency_period = trim($request->residency_period);
    $mobile_no = trim($request->mobile_no);
    $email = trim($request->email);
    if (empty($email)) {
      $email = NULL;
    }
    $cur_per_same = trim($request->cur_per_same);
    //dd($cur_per_same);
    if ($cur_per_same == 1) {
      $district_cur = trim($request->district);
      $asmb_cons_cur = trim($request->asmb_cons);
      $urban_code_cur = trim($request->urban_code);
      $block_cur = trim($request->block);
      $gp_ward_cur = trim($request->gp_ward);
      $village_cur = trim($request->village);
      $house_cur = trim($request->house);
      $post_office_cur = trim($request->post_office);
      $pin_code_cur = trim($request->pin_code);
      $police_station_cur = trim($request->police_station);
    } else {
      $district_cur = trim($request->district_cur);
      $asmb_cons_cur = trim($request->asmb_cons_cur);
      $urban_code_cur = trim($request->urban_code_cur);
      $block_cur = trim($request->block_cur);
      $gp_ward_cur = trim($request->gp_ward_cur);
      $village_cur = trim($request->village_cur);
      $house_cur = trim($request->house_cur);
      $post_office_cur = trim($request->post_office_cur);
      $pin_code_cur = trim($request->pin_code_cur);
      $police_station_cur = trim($request->police_station_cur);
    }
    if (empty($district_cur)) {
      $district_cur = NULL;
    }
    if (empty($asmb_cons_cur)) {
      $asmb_cons_cur = NULL;
    }
    if (empty($urban_code_cur)) {
      $urban_code_cur = NULL;
    }
    if (empty($block_cur)) {
      $block_cur = NULL;
    }
    if (empty($gp_ward_cur)) {
      $gp_ward_cur = NULL;
    }
    if (empty($village_cur)) {
      $village_cur = NULL;
    }
    if (empty($house_cur)) {
      $house_cur = NULL;
    }
    if (empty($post_office_cur)) {
      $post_office_cur = NULL;
    }
    if (empty($pin_code_cur)) {
      $pin_code_cur = NULL;
    }
    if (empty($police_station_cur)) {
      $police_station_cur = NULL;
    }
    $mouza_name = trim($request->mouza_name);
    if (empty($mouza_name)) {
      $mouza_name = NULL;
    }
    $land_jlno = trim($request->land_jlno);
    if (empty($land_jlno)) {
      $land_jlno = NULL;
    }
    $khatian_no = trim($request->khatian_no);
    if (empty($khatian_no)) {
      $khatian_no = NULL;
    }
    $plot_no = trim($request->plot_no);
    if (empty($plot_no)) {
      $plot_no = NULL;
    }
    $land_area = trim($request->land_area);
    if (empty($land_area)) {
      $land_area = NULL;
    }
    $land_holdername = trim($request->land_holdername);
    if (empty($land_holdername)) {
      $land_holdername = NULL;
    }
    $name_of_bank = trim($request->name_of_bank);
    //echo $name_of_bank;die;
    $bank_branch = trim($request->bank_branch);
    $bank_account_number = trim($request->bank_account_number);
    $bank_ifsc_code = trim($request->bank_ifsc_code);
    $ssp_y_n = trim($request->ssp_y_n);
    if (empty($ssp_y_n)) {
      $ssp_y_n = NULL;
    }
    $pucca_house_y_n = $request->pucca_house_y_n;
    $nominate_name = trim($request->nominate_name);
    if (empty($nominate_name)) {
      $nominate_name = NULL;
    }
    $nominate_address = trim($request->nominate_address);
    if (empty($nominate_address)) {
      $nominate_address = NULL;
    }
    $nominate_relationship = trim($request->nominate_relationship);
    if (empty($nominate_relationship)) {
      $nominate_relationship = NULL;
    }

    $av_status = trim($request->av_status);
    if (empty($av_status)) {
      $av_status = NULL;
    }
    if ($request->receive_pension != "") {
      $receive_pension = implode(',', $request->receive_pension);
    } else
      $receive_pension = NULL;
    $receiving_pension_other_source_1 = trim($request->receiving_pension_other_source_1);
    if (empty($receiving_pension_other_source_1)) {
      $receiving_pension_other_source_1 = NULL;
    }
    $receiving_pension_other_source_2 = trim($request->receiving_pension_other_source_2);
    if (empty($receiving_pension_other_source_2)) {
      $receiving_pension_other_source_2 = NULL;
    }
    if ($urban_code == 1) {
      $block_ulb_db = UrbanBody::where('urban_body_code', '=', $block)->first();
      $gp_ward_db = Ward::where('urban_body_ward_code', '=', $gp_ward)->first();
    } else {
      $block_ulb_db = Taluka::where('block_code', '=', $block)->first();
      $gp_ward_db = GP::where('gram_panchyat_code', '=', $gp_ward)->first();
    }
    if ($request->asmb_cons != "") {
      $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
      $assembly_name = $assembly->ac_name;
    } else {
      $assembly_name = null;
    }

    $this->validateInput($request, $scheme_id);
    if (!empty($request->aadhar_no)) {
      if ($this->isAadharValid(trim($request->aadhar_no)) == false) {
        $errors = array();
        $errorMsg = "Aadhaar Number Invalid";

        array_push($errors, $errorMsg);
        //dd($errors);
        return back()->withErrors($errors)->withInput();
      }
    }
    if (!preg_match('/^[0-9]{10}+$/', $request->mobile_no)) {
      $errors = array();
      $errorMsg = "Mobile Number Invalid";

      array_push($errors, $errorMsg);
      //dd($errors);
      return back()->withErrors($errors)->withInput();
    }
    if ($request->mobile_no < 1000000000) {
      $errors = array();
      $errorMsg = "Mobile Number Invalid";

      array_push($errors, $errorMsg);
      //dd($errors);
      return back()->withErrors($errors)->withInput();
    }
    $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
    //dd($this->pr1ListPurohit['monthly']['slug']);
    $count1 = DB::table('pension.beneficiaries')
      ->where('aadhar_no', trim($request->aadhar_no))
      ->where('scheme_id', $scheme_id)
      ->whereRaw("(" . $check_condition_str . ")")
      ->whereIn('is_clean', [1, 2])
      ->count('id');

    if ($count1 > 0) {
      $errors = array();

      $errorMsg = "Aadhaar Number Already Exist! Please try different.";
      array_push($errors, $errorMsg);
      //dd($errors);
      return back()->withErrors($errors)->withInput();
    }
    //--------- Duplicate bank A/C check---------- //
    $bankCount = BenEntry::whereRaw("trim(bank_code)=trim(" . "'" . $request->bank_account_number . "'" . ")")
      ->whereRaw("(" . $check_condition_str . ")")
      ->where('scheme_id', $scheme_id)
      ->count('id');
    if ($bankCount > 0) {
      $errors = array();

      $errorMsg = "Bank A/C Already Exist!";
      array_push($errors, $errorMsg);
      //dd($errors);
      return back()->withErrors($errors)->withInput();
    }
    $count_mobile = DB::table('pension.beneficiaries')->where('scheme_id', $scheme_id)->where('mobile_no', $request->mobile_no)->whereIn('is_clean', [1, 2])->whereRaw("(" . $check_condition_str . ")")->count('id');
    if ($count_mobile > 0) {
      $errors = array();

      $errorMsg = "Mobile Number Already Exist! Please try different.";
      array_push($errors, $errorMsg);
      //dd($errors);
      return back()->withErrors($errors)->withInput();
    }
    $row_count_bank = BankDetails::whereraw("trim(branch)='$bank_branch'")->whereraw("trim(bank)='$name_of_bank'")->where('is_active', 1)->count();

    // $bank_details = BankDetails::whereraw("trim(ifsc)='$bank_ifsc_code'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
    // $new_bank_code=$bank_details->bank_code;

    $bank_details = BankDetails::where('ifsc', trim($bank_ifsc_code))->where('is_active', 1)->get(['bank', 'branch', 'bank_code'])->first();
    $new_bank_code = $bank_details->bank_code;


    if ($row_count_bank == 0) {
      $errors = array();
      $errorMsg = "Bank IFSC and Bank Name Not Match!";

      array_push($errors, $errorMsg);
      //return back()->withErrors($errors)->withInput();
      //dd($errors);
      return back()->withErrors($errors)->withInput();
    }
    $uploaded_doc = array();
    $destinationPath = storage_path('app/keep_ICAD/');
    $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->get();

    if (!empty($doc_id_list[0]['doc_list_man']))
      $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
    else
      $doc_list_man = array();
    if (!empty($doc_id_list[0]['doc_list_opt']))
      $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
    else
      $doc_list_opt = array();
    if (($doc_id_list[0]['doc_list_man_group']) != '' && ($doc_id_list[0]['doc_list_man_group'] != 'null') && ($doc_id_list[0]['doc_list_man_group']) != null) {

      $doc_list_man_group_db = json_decode($doc_id_list[0]['doc_list_man_group']);
    } else {
      $doc_list_man_group_db = array();
    }
    $doc_list = array_merge($doc_list_man, $doc_list_opt);
    // dump($doc_list_man_group_db);
    $doc_list_man_group_upload = array();
    $upload_file = array();
    $i = 0;
    $doc_master = DocumentType::get();
    $c_time = date('Y-m-d H:i:s');
    foreach ($doc_list as $doc) {
      if ($request->hasFile('doc_' . $doc)) {
        $doucument_group_id = DocumentType::select('doucument_group')->where('id', $doc)->first();
        if ($doucument_group_id['doucument_group'] != '') {
          $arr = array();
          $postgresStr = trim($doucument_group_id['doucument_group'], "{}");
          $elmts = explode(",", $postgresStr);
          foreach ($elmts as $myarr) {
            if (!in_array($myarr, $doc_list_man_group_upload)) {
              array_push($doc_list_man_group_upload, $myarr);
            }
          }
        }
        $doc_file = $request->file('doc_' . $doc);
        $img_data = file_get_contents($doc_file);
        $u_extension = $doc_file->getClientOriginalExtension();
        $u_extension = strtolower($u_extension);
        $mime_type = $doc_file->getMimeType();
        $doc_type_name = $doc_master->where('id', $doc)->first();
        if (strtolower($mime_type) == 'image/jpeg') {
          if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
            $extension = $u_extension;
          } else {
            $errors = array();
            $errorMsg = "You are trying to upload an incorrect file for " . $doc_type_name->doc_name;
            array_push($errors, $errorMsg);
            //dd($errors);
            return back()->with('errors', $errors)->withInput(Input::all());
          }
        } else if (strtolower($mime_type) == 'image/png') {
          $extension = 'png';
        } else if (strtolower($mime_type) == 'image/gif') {
          $extension = 'gif';
        } else if (strtolower($mime_type) == 'application/pdf') {
          $extension = 'pdf';
        } else {
          $errors = array();

          $errorMsg = "You are trying to upload an incorrect file for " . $doc_type_name->doc_name;
          array_push($errors, $errorMsg);
          //dd($errors);
          return back()->with('errors', $errors)->withInput(Input::all());
        }
        if ($u_extension != $extension) {
          $errors = array();
          $errorMsg = "You are trying to upload an incorrect file for " . $doc_type_name->doc_name;

          array_push($errors, $errorMsg);
          //dd($errors);
          return back()->with('errors', $errors)->withInput(Input::all());
        }
        $base64 = base64_encode($img_data);
        $upload_file[$i]['created_by_dist_code'] = $request->session()->get('distCode');
        $upload_file[$i]['created_by_local_body_code'] = $request->session()->get('blockCode');
        $upload_file[$i]['document_type'] = $doc;
        $upload_file[$i]['scheme_id'] = $scheme_id;
        $upload_file[$i]['created_by_level'] = $request->session()->get('level');
        $upload_file[$i]['created_at'] = $c_time;
        $upload_file[$i]['created_by'] = $user_id;
        $upload_file[$i]['ip_address'] = $request->ip();
        $upload_file[$i]['attched_document'] = $base64;
        $upload_file[$i]['document_mime_type'] = $mime_type;
        $upload_file[$i]['document_extension'] = $extension;
        if (!empty($doc_type_name)) {
          $upload_file[$i]['doc_type_name'] = $doc_type_name->doc_name;
        }
        $i++;
      } else {
        $file_passport = null;
      }
    }
    //dump($doc_list_man_group_upload);
    // dump($doc_list_man_group_db);
    //  dd();
    if (count($doc_list_man_group_db) > 0) {
      $errors = array();
      $i = 0;
      foreach ($doc_list_man_group_db as $group) {
        $doucument_group_name = $this->getGroupName($group);
        if (!in_array($group, $doc_list_man_group_upload)) {
          $errorMsg = "At least one document must be uploaded for " . $doucument_group_name;
          //dd($errors);
          array_push($errors, $errorMsg);
        }
      }
      if (count($errors) > 0)
        //dd($errors);
        return back()->withErrors($errors)->withInput();
    }
    $c_time = date('Y-m-d H:i:s');
    try {
      DB::connection('pgsql5')->beginTransaction();
      DB::connection('pgsql_encwrite')->beginTransaction();
      if ($housing) {

        $pension_details1 = new BenEntry();
        $pension_details1->entry_datetime = $c_time;
        $pension_details1->ip_address = $request->ip();
        $pension_details1->ben_fname = $first_name;
        if (!empty($middle_name))
          $pension_details1->ben_mname = $middle_name;
        $pension_details1->ben_lname = $last_name;
        $pension_details1->gender = $gender;

        $pension_details1->app_phase = $app_phase;
        $pension_details1->temple_type = $temple_type;

        $pension_details1->caste = $caste_category;
        $pension_details1->dob = $dob;
        $pension_details1->ben_age = $txt_age;
        if (!empty($father_first_name))
          $pension_details1->father_fname = $father_first_name;
        if (!empty($father_middle_name))
          $pension_details1->father_mname = $father_middle_name;
        if (!empty($father_last_name))
          $pension_details1->father_lname = $father_last_name;
        if (!empty($mother_first_name))
          $pension_details1->mother_fname = $mother_first_name;
        if (!empty($mother_middle_name))
          $pension_details1->mother_mname = $mother_middle_name;
        if (!empty($mother_last_name))
          $pension_details1->mother_lname = $mother_last_name;

        $pension_details1->marital_status = $marital_status;
        if (!empty($spouse_first_name))
          $pension_details1->spouse_fname = $spouse_first_name;
        if (!empty($spouse_middle_name))
          $pension_details1->spouse_mname = $spouse_middle_name;
        if (!empty($spouse_last_name))
          $pension_details1->spouse_lname = $spouse_last_name;
        if (!empty($ration_card_cat))
          $pension_details1->ration_card_cat = $ration_card_cat;
        if (!empty($ration_card_no))
          $pension_details1->ration_card_no = $ration_card_no;
        if (!empty($aadhar_no))
          $pension_details1->aadhar_no = $aadhar_no;
        if (!empty($epic_voter_id))
          $pension_details1->epic_voter_id = $epic_voter_id;
        $pension_details1->pan_no = $pan_no;

        $pension_details1->dist_code = $district;
        $pension_details1->rural_urban_id = $urban_code;
        if (!empty($asmb_cons)) {
          $pension_details1->assembly_code = $asmb_cons;
          $pension_details1->assembly_name = $assembly_name;
        }
        $pension_details1->police_station = $police_station;
        $pension_details1->block_ulb_code = $block;
        $pension_details1->gp_ward_code = $gp_ward;
        $pension_details1->village_town_city = $village;
        if (!empty($house))
          $pension_details1->house_premise_no = $house;
        $pension_details1->post_office = $post_office;
        $pension_details1->pincode = $pin_code;
        if (!empty($cur_per_same))
          $pension_details1->cur_per_address_is_equal = $cur_per_same;
        if (!empty($district_cur))
          $pension_details1->dist_code_cur = $district_cur;

        if (!empty($asmb_cons_cur))
          $pension_details1->assembly_code_cur = $asmb_cons_cur;

        if (!empty($urban_code_cur))
          $pension_details1->rural_urban_id_cur = $urban_code_cur;

        if (!empty($block_cur))
          $pension_details1->block_ulb_code_cur = $block_cur;

        if (!empty($gp_ward_cur))
          $pension_details1->gp_ward_code_cur = $gp_ward_cur;

        if (!empty($village_cur))
          $pension_details1->village_town_city_cur = $village_cur;

        if (!empty($house_cur))
          $pension_details1->house_premise_no_cur = $house_cur;

        if (!empty($post_office_cur))
          $pension_details1->post_office_cur = $post_office_cur;

        if (!empty($pin_code_cur))
          $pension_details1->pincode_cur = $pin_code_cur;

        if (!empty($police_station_cur))
          $pension_details1->police_station_cur = $police_station_cur;

        if (!empty($residency_period))
          $pension_details1->residency_period = $residency_period;
        $pension_details1->mobile_no = $mobile_no;
        $pension_details1->email = $email;
        if (!empty($mouza_name))
          $pension_details1->mouza_name = $mouza_name;
        if (!empty($land_jlno))
          $pension_details1->land_jlno = $land_jlno;
        if (!empty($khatian_no))
          $pension_details1->khatian_no = $khatian_no;
        if (!empty($plot_no))
          $pension_details1->plot_no = $plot_no;
        if (!empty($land_area))
          $pension_details1->land_area = $land_area;
        if (!empty($land_holdername))
          $pension_details1->land_holdername = $land_holdername;


        $pension_details1->bank_name = $name_of_bank;
        $pension_details1->branch_name = $bank_branch;
        $pension_details1->bank_code = $bank_account_number;
        $pension_details1->bank_ifsc = $bank_ifsc_code;
        $pension_details1->npci_bank_code = $new_bank_code;
        if (!empty($ssp_y_n))
          $pension_details1->ssp_y_n = $ssp_y_n;
        if (!empty($pucca_house_y_n))
          $pension_details1->pucca_house_y_n = $pucca_house_y_n;
        if (!empty($nominate_name))
          $pension_details1->nominate_name = $nominate_name;
        if (!empty($nominate_address))
          $pension_details1->nominate_address = $nominate_address;
        if (!empty($nominate_relationship))
          $pension_details1->nominate_relationship = $nominate_relationship;
        if (!empty($av_status))
          $pension_details1->av_status = $av_status;
        if (!empty($receive_pension))
          $pension_details1->receive_pension = $receive_pension;
        if (!empty($receiving_pension_other_source_1))
          $pension_details1->receiving_pension_other_source_1 = $receiving_pension_other_source_1;
        if (!empty($receiving_pension_other_source_2))
          $pension_details1->receiving_pension_other_source_2 = $receiving_pension_other_source_2;

        $pension_details1->created_by = Auth::user()->id;
        $pension_details1->created_by_level = $request->session()->get('level');
        $pension_details1->created_by_dist_code = $request->session()->get('distCode');
        $pension_details1->created_by_local_body_code = $request->session()->get('blockCode');
        $pension_details1->scheme_id = $scheme_id;

        if ($urban_code == 1) {
          $pension_details1->block_ulb_name = $block_ulb_db->urban_body_name;
          $pension_details1->gp_ward_name = $gp_ward_db->urban_body_ward_name;
        } else {

          $pension_details1->block_ulb_name = $block_ulb_db->block_name;
          $pension_details1->gp_ward_name = $gp_ward_db->gram_panchyat_name;
        }
        $pension_details1->assembly_name = $assembly_name;
        $is_saved = $pension_details1->save();
        $id = $pension_details1->id;
        $housing_code = $id;
        $housing_insertion_status = $id;
      } else {
        $housing_insertion_status = 1;
        $housing_code = NULL;
      }
      if ($monthly) {
        $scheme_id = $monthlySchemeCode;
        $pension_details2 = new BenEntry();
        $pension_details2->entry_datetime = $c_time;
        $pension_details2->ip_address = $request->ip();
        $pension_details2->ben_fname = $first_name;
        if (!empty($middle_name))
          $pension_details2->ben_mname = $middle_name;
        $pension_details2->ben_lname = $last_name;
        $pension_details2->gender = $gender;

        $pension_details2->app_phase = $app_phase;
        $pension_details2->temple_type = $temple_type;

        $pension_details2->caste = $caste_category;
        $pension_details2->dob = $dob;
        $pension_details2->ben_age = $txt_age;
        if (!empty($father_first_name))
          $pension_details2->father_fname = $father_first_name;
        if (!empty($father_middle_name))
          $pension_details2->father_mname = $father_middle_name;
        if (!empty($father_last_name))
          $pension_details2->father_lname = $father_last_name;
        if (!empty($mother_first_name))
          $pension_details2->mother_fname = $mother_first_name;
        if (!empty($mother_middle_name))
          $pension_details2->mother_mname = $mother_middle_name;
        if (!empty($mother_last_name))
          $pension_details2->mother_lname = $mother_last_name;

        $pension_details2->marital_status = $marital_status;
        if (!empty($spouse_first_name))
          $pension_details2->spouse_fname = $spouse_first_name;
        if (!empty($spouse_middle_name))
          $pension_details2->spouse_mname = $spouse_middle_name;
        if (!empty($spouse_last_name))
          $pension_details2->spouse_lname = $spouse_last_name;
        if (!empty($ration_card_cat))
          $pension_details2->ration_card_cat = $ration_card_cat;
        if (!empty($ration_card_no))
          $pension_details2->ration_card_no = $ration_card_no;
        if (!empty($aadhar_no))
          $pension_details2->aadhar_no = $aadhar_no;
        if (!empty($epic_voter_id))
          $pension_details2->epic_voter_id = $epic_voter_id;
        $pension_details2->pan_no = $pan_no;

        $pension_details2->dist_code = $district;
        $pension_details2->rural_urban_id = $urban_code;
        if (!empty($asmb_cons)) {
          $pension_details2->assembly_code = $asmb_cons;
          $pension_details2->assembly_name = $assembly_name;
        }
        $pension_details2->police_station = $police_station;
        $pension_details2->block_ulb_code = $block;
        $pension_details2->gp_ward_code = $gp_ward;
        $pension_details2->village_town_city = $village;
        if (!empty($house))
          $pension_details2->house_premise_no = $house;
        $pension_details2->post_office = $post_office;
        $pension_details2->pincode = $pin_code;
        if (!empty($cur_per_same))
          $pension_details2->cur_per_address_is_equal = $cur_per_same;
        if (!empty($district_cur))
          $pension_details2->dist_code_cur = $district_cur;

        if (!empty($asmb_cons_cur))
          $pension_details2->assembly_code_cur = $asmb_cons_cur;

        if (!empty($urban_code_cur))
          $pension_details2->rural_urban_id_cur = $urban_code_cur;

        if (!empty($block_cur))
          $pension_details2->block_ulb_code_cur = $block_cur;

        if (!empty($gp_ward_cur))
          $pension_details2->gp_ward_code_cur = $gp_ward_cur;

        if (!empty($village_cur))
          $pension_details2->village_town_city_cur = $village_cur;

        if (!empty($house_cur))
          $pension_details2->house_premise_no_cur = $house_cur;

        if (!empty($post_office_cur))
          $pension_details2->post_office_cur = $post_office_cur;

        if (!empty($pin_code_cur))
          $pension_details2->pincode_cur = $pin_code_cur;

        if (!empty($police_station_cur))
          $pension_details2->police_station_cur = $police_station_cur;

        if (!empty($residency_period))
          $pension_details2->residency_period = $residency_period;
        $pension_details2->mobile_no = $mobile_no;
        $pension_details2->email = $email;
        if (!empty($mouza_name))
          $pension_details2->mouza_name = $mouza_name;
        if (!empty($land_jlno))
          $pension_details2->land_jlno = $land_jlno;
        if (!empty($khatian_no))
          $pension_details2->khatian_no = $khatian_no;
        if (!empty($plot_no))
          $pension_details2->plot_no = $plot_no;
        if (!empty($land_area))
          $pension_details2->land_area = $land_area;
        if (!empty($land_holdername))
          $pension_details2->land_holdername = $land_holdername;


        $pension_details2->bank_name = $name_of_bank;
        $pension_details2->branch_name = $bank_branch;
        $pension_details2->bank_code = $bank_account_number;
        $pension_details2->bank_ifsc = $bank_ifsc_code;
        $pension_details2->npci_bank_code = $new_bank_code;
        if (!empty($ssp_y_n))
          $pension_details2->ssp_y_n = $ssp_y_n;
        if (!empty($pucca_house_y_n))
          $pension_details2->pucca_house_y_n = $pucca_house_y_n;
        if (!empty($nominate_name))
          $pension_details2->nominate_name = $nominate_name;
        if (!empty($nominate_address))
          $pension_details2->nominate_address = $nominate_address;
        if (!empty($nominate_relationship))
          $pension_details2->nominate_relationship = $nominate_relationship;
        if (!empty($av_status))
          $pension_details2->av_status = $av_status;
        if (!empty($receive_pension))
          $pension_details2->receive_pension = $receive_pension;
        if (!empty($receiving_pension_other_source_1))
          $pension_details2->receiving_pension_other_source_1 = $receiving_pension_other_source_1;
        if (!empty($receiving_pension_other_source_2))
          $pension_details2->receiving_pension_other_source_2 = $receiving_pension_other_source_2;


        $pension_details2->created_by = Auth::user()->id;
        $pension_details2->created_by_level = $request->session()->get('level');
        $pension_details2->created_by_dist_code = $request->session()->get('distCode');
        $pension_details2->created_by_local_body_code = $request->session()->get('blockCode');
        $pension_details2->scheme_id = $scheme_id;

        if ($urban_code == 1) {
          $pension_details2->block_ulb_name = $block_ulb_db->urban_body_name;
          $pension_details2->gp_ward_name = $gp_ward_db->urban_body_ward_name;
        } else {
          $pension_details2->block_ulb_name = $block_ulb_db->block_name;
          $pension_details2->gp_ward_name = $gp_ward_db->gram_panchyat_name;
        }

        $pension_details2->assembly_name = $assembly_name;
        $pension_details2->housing_code = $housing_code;
        $is_saved = $pension_details2->save();
        $id = $pension_details2->id;
        $application_id = $pension_details2->getBenidAttribute();
        $monthly_insertion_status = $id;
      } else {
        $monthly_insertion_status = 1;
      }

      if ($id) {
        foreach ($upload_file as $key => $csm) {
          $upload_file[$key]['beneficiary_id'] = $monthly_insertion_status;
        }
        $doc_inserted = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($upload_file);
        // try catch 
        if ($housing_insertion_status && $monthly_insertion_status && $doc_inserted) {
          DB::connection('pgsql5')->commit();
          DB::connection('pgsql_encwrite')->commit();
          $return_status = 'success';
          $msg = "Application Submitted Successfully";
        } else {
          DB::connection('pgsql5')->rollback();
          DB::connection('pgsql_encwrite')->rollback();
          $id = 0;
          $return_status = 'error';
          $errors = array();
          $errorMsg = "Insertion Failed..Please try again." . $housing_insertion_status . " - " . $monthly_insertion_status . " - " . $doc_inserted;

          array_push($errors, $errorMsg);
          //dd($errors);
          return back()->withErrors($errors)->withInput();
        }
      } else {
        $id = 0;
        $return_status = 'error';
        $errors = array();

        $errorMsg = "Insertion Failed..Please try again.";
        array_push($errors, $errorMsg);
        //dd($errors);
        return back()->withErrors($errors)->withInput();
      }
      if ($application_type == $monthlySlug) {
        $msg = $msg . " for Monthly Scheme with Application Id " . $application_id . ".";
      }
      // if($application_type==$housingSlug){
      //   $msg=$msg." for One time Housing Scheme with Application Id ".$housing_insertion_status.".";
      // }
      if ($application_type == $housingSlug) {
        $msg = $msg . " for Both Monthly and One time Housing Scheme with Application Ids " . $monthly_insertion_status . " and " . $housing_insertion_status . " respectively.";
      }
      return redirect("purohit?code=$code")->with($return_status, $msg);
    } catch (\Exception $e) {
      dd($e);
      $return_status = 'error';
      return redirect("purohit?code=$code")->with($return_status, $e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */












  public function applicationupdate(Request $request)
  {
    $monthlySlug = $this->pr1ListPurohit['monthly']['slug'];
    $monthlySchemeCode = $this->pr1ListPurohit['monthly']['scheme_code'];
    $monthlyMainTable = $this->pr1ListPurohit['monthly']['maintable'];
    $monthlyMainTable = "App\\" . $monthlyMainTable;
    $monthlyDocTable = $this->pr1ListPurohit['monthly']['doctable'];
    $monthlyDocTable = "App\\" . $monthlyDocTable;
    $monthlyDocArcTable = $this->pr1ListPurohit['monthly']['docarchtable'];
    $monthlyDocArcTable = "App\\" . $monthlyDocArcTable;

    $housingSlug = $this->pr1ListPurohit['housing']['slug'];
    $housingSchemeCode = $this->pr1ListPurohit['housing']['scheme_code'];
    $housingMainTable = $this->pr1ListPurohit['housing']['maintable'];
    $housingMainTable = "App\\" . $housingMainTable;
    $housingDocTable = $this->pr1ListPurohit['housing']['doctable'];
    $housingDocTable = "App\\" . $housingDocTable;
    $housingDocArcTable = $this->pr1ListPurohit['housing']['docarchtable'];
    $housingDocArcTable = "App\\" . $housingDocArcTable;

    $base_url = url('/');
    $id = $request->id;
    $scheme_id = $request->scheme_id;
    $designation_id = Auth::user()->designation_id;
    $created_by = Auth::user()->id;

    // echo $id;die;
    $valid = false;
    if ((!empty($id)) && is_numeric($id) && (!empty($scheme_id)) && in_array($scheme_id, array($monthlySchemeCode, $housingSchemeCode))) {
      $valid = true;
    }
    if ($valid) {
      $is_active = 0;
      $mapping_level = NULL;
      $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
      foreach ($roleArray as $roleObj) {
        if (($roleObj['scheme_id'] == $monthlySchemeCode) || ($roleObj['scheme_id'] == $housingSchemeCode)) {
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
      if ($is_active == 0) {
        return redirect("/")->with('error', 'User Disabled');
      }
      $valid = false;
      $pension_details = null;
      $housing_details = null;

      $pensionbenDocTable = $monthlyDocTable;
      $pensionbenDocArcTable = $monthlyDocArcTable;
      $housingbenDocTable = $housingDocTable;
      $housingbenDocArcTable = $housingDocArcTable;

      $pension_id = 0;
      $housing_id = 0;

      if ($scheme_id == $monthlySchemeCode) {
        $row = $monthlyMainTable::find($id);
        $pension_details = $row;
        $pension_id = $id;

        if ($pension_details->housing_code != null) {
          $housing_details = $housingMainTable::find($pension_details->housing_code);
          $housing_id = $pension_details->housing_code;
        }

        $pri = $monthlySlug;
      }
      if ($scheme_id == $housingSchemeCode) {
        $row = $housingMainTable::find($id);
        $housing_details = $row;
        $housing_id = $id;

        $pension_details = $monthlyMainTable::where('housing_code', $id)->first();
        $pension_id = $pension_details->id;

        $pri = $housingSlug;
      }
      if ($row->id) {
        $valid = true;
      }
      if ($valid) {
        $first_name = trim($request->first_name);
        $middle_name = trim($request->middle_name);
        if (empty($middle_name)) {
          $middle_name = NULL;
        }
        $last_name = trim($request->last_name);
        $gender = trim($request->gender);
        $app_phase = trim($request->app_phase);
        $temple_type = trim($request->temple_type);
        $caste_category = trim($request->caste_category);
        $dob = $request->dob;
        //echo $dob;die;
        $txt_age = trim($request->txt_age);
        $father_first_name = trim($request->father_first_name);
        $father_middle_name = trim($request->father_middle_name);
        if (empty($father_middle_name)) {
          $father_middle_name = NULL;
        }
        $father_last_name = trim($request->father_last_name);
        $mother_first_name = trim($request->mother_first_name);
        $mother_middle_name = trim($request->mother_middle_name);
        if (empty($mother_middle_name)) {
          $mother_middle_name = NULL;
        }
        $mother_last_name = trim($request->mother_last_name);
        $marital_status = trim($request->marital_status);
        if ($marital_status == 'Married') {
          $spouse_first_name = trim($request->spouse_first_name);
          if (empty($spouse_first_name)) {
            $spouse_first_name = NULL;
          }
          $spouse_middle_name = trim($request->spouse_middle_name);
          if (empty($spouse_middle_name)) {
            $spouse_middle_name = NULL;
          }
          $spouse_last_name = trim($request->spouse_last_name);
          if (empty($spouse_last_name)) {
            $spouse_last_name = NULL;
          }
        } else {
          $spouse_first_name = NULL;
          $spouse_middle_name = NULL;
          $spouse_last_name = NULL;
        }
        $ration_card_cat = trim($request->ration_card_cat);
        $ration_card_no = trim($request->ration_card_no);
        $aadhar_no = trim($request->aadhar_no);
        if (empty($aadhar_no)) {
          $aadhar_no = NULL;
        }
        $epic_voter_id = trim($request->epic_voter_id);
        $pan_no = trim($request->pan_no);
        if (empty($pan_no)) {
          $pan_no = NULL;
        }
        $district = trim($request->district);
        $asmb_cons = trim($request->asmb_cons);
        $police_station = trim($request->police_station);
        $urban_code = trim($request->urban_code);
        $block = trim($request->block);
        $gp_ward = trim($request->gp_ward);
        $village = trim($request->village);
        $house = trim($request->house);
        if (empty($house)) {
          $house = NULL;
        }
        $post_office = trim($request->post_office);
        $pin_code = trim($request->pin_code);

        $cur_per_same = trim($request->cur_per_same);
        //dd($cur_per_same);
        if ($cur_per_same == 1) {
          $district_cur = trim($request->district);
          $asmb_cons_cur = trim($request->asmb_cons);
          $urban_code_cur = trim($request->urban_code);
          $block_cur = trim($request->block);
          $gp_ward_cur = trim($request->gp_ward);
          $village_cur = trim($request->village);
          $house_cur = trim($request->house);
          $post_office_cur = trim($request->post_office);
          $pin_code_cur = trim($request->pin_code);
          $police_station_cur = trim($request->police_station);
        } else {
          $district_cur = trim($request->district_cur);
          $asmb_cons_cur = trim($request->asmb_cons_cur);
          $urban_code_cur = trim($request->urban_code_cur);
          $block_cur = trim($request->block_cur);
          $gp_ward_cur = trim($request->gp_ward_cur);
          $village_cur = trim($request->village_cur);
          $house_cur = trim($request->house_cur);
          $post_office_cur = trim($request->post_office_cur);
          $pin_code_cur = trim($request->pin_code_cur);
          $police_station_cur = trim($request->police_station_cur);
        }
        if (empty($district_cur)) {
          $district_cur = NULL;
        }
        if (empty($asmb_cons_cur)) {
          $asmb_cons_cur = NULL;
        }
        if (empty($urban_code_cur)) {
          $urban_code_cur = NULL;
        }
        if (empty($block_cur)) {
          $block_cur = NULL;
        }
        if (empty($gp_ward_cur)) {
          $gp_ward_cur = NULL;
        }
        if (empty($village_cur)) {
          $village_cur = NULL;
        }
        if (empty($house_cur)) {
          $house_cur = NULL;
        }
        if (empty($post_office_cur)) {
          $post_office_cur = NULL;
        }
        if (empty($pin_code_cur)) {
          $pin_code_cur = NULL;
        }
        if (empty($police_station_cur)) {
          $police_station_cur = NULL;
        }

        $residency_period = trim($request->residency_period);
        $mobile_no = trim($request->mobile_no);
        $email = trim($request->email);
        if (empty($email)) {
          $email = NULL;
        }
        $mouza_name = trim($request->mouza_name);
        if (empty($mouza_name)) {
          $mouza_name = NULL;
        }
        $land_jlno = trim($request->land_jlno);
        if (empty($land_jlno)) {
          $land_jlno = NULL;
        }
        $khatian_no = trim($request->khatian_no);
        if (empty($khatian_no)) {
          $khatian_no = NULL;
        }
        $plot_no = trim($request->plot_no);
        if (empty($plot_no)) {
          $plot_no = NULL;
        }
        $land_area = trim($request->land_area);
        if (empty($land_area)) {
          $land_area = NULL;
        }
        $land_holdername = trim($request->land_holdername);
        if (empty($land_holdername)) {
          $land_holdername = NULL;
        }
        $name_of_bank = trim($request->name_of_bank);
        //echo $name_of_bank;die;
        $bank_branch = trim($request->bank_branch);
        $bank_account_number = trim($request->bank_account_number);
        $bank_ifsc_code = trim($request->bank_ifsc_code);
        $ssp_y_n = trim($request->ssp_y_n);
        if (empty($ssp_y_n)) {
          $ssp_y_n = NULL;
        }
        $pucca_house_y_n = $request->pucca_house_y_n;
        if (empty($pucca_house_y_n)) {
          $pucca_house_y_n = NULL;
        }
        $nominate_name = trim($request->nominate_name);
        if (empty($nominate_name)) {
          $nominate_name = NULL;
        }
        $nominate_address = trim($request->nominate_address);
        if (empty($nominate_address)) {
          $nominate_address = NULL;
        }
        $nominate_relationship = trim($request->nominate_relationship);
        if (empty($nominate_relationship)) {
          $nominate_relationship = NULL;
        }

        $av_status = trim($request->av_status);
        if (empty($av_status)) {
          $av_status = NULL;
        }
        if ($request->receive_pension != "") {
          $receive_pension = implode(',', $request->receive_pension);
        } else
          $receive_pension = NULL;
        $receiving_pension_other_source_1 = trim($request->receiving_pension_other_source_1);
        if (empty($receiving_pension_other_source_1)) {
          $receiving_pension_other_source_1 = NULL;
        }
        $receiving_pension_other_source_2 = trim($request->receiving_pension_other_source_2);
        if (empty($receiving_pension_other_source_2)) {
          $receiving_pension_other_source_2 = NULL;
        }
        //echo $scheme_id;die;

        if ($urban_code == 1) {
          $block_ulb_db = UrbanBody::where('urban_body_code', '=', $block)->first();
          $gp_ward_db = Ward::where('urban_body_ward_code', '=', $gp_ward)->first();
        } else {
          $block_ulb_db = Taluka::where('block_code', '=', $block)->first();
          $gp_ward_db = GP::where('gram_panchyat_code', '=', $gp_ward)->first();
        }
        $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
        $assembly_name = $assembly->ac_name;
        $scheme_row = Scheme::where('id', $scheme_id)->first();
        if (empty($scheme_row)) {
          return redirect("/")->with('error', 'User Disabled');
        }
        $errors = array();
        $row_count_bank = BankDetails::whereraw("trim(branch)='$bank_branch'")->whereraw("trim(ifsc)='$bank_ifsc_code'")->whereraw("trim(bank)='$name_of_bank'")->where('is_active', 1)->count();
        // $bank_details = BankDetails::whereraw("trim(ifsc)='$bank_ifsc_code'")->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();
        // $new_bank_code=$bank_details->bank_code;

        $bank_details = BankDetails::where('ifsc', trim($bank_ifsc_code))->where('is_active', 1)->get(['bank', 'branch', 'bank_code'])->first();
        $new_bank_code = $bank_details->bank_code;
        if ($row_count_bank == 0) {
          $errors = array();
          $errorMsg = "Bank IFSC and Bank Name Not Match!";
          array_push($errors, $errorMsg);
          //return back()->withErrors($errors)->withInput();
          return back()->withErrors($errors)->withInput();
        }
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (!empty($scheme_obj->short_code)) {
          $schema = $scheme_obj->short_code;
        } else {
          $schema = "pension";
        }
        $check_condition_str = Helper::getCheckNextLevelRoleIdCon($scheme_id);
        if (!empty($request->mobile_no)) {
          $mobile_count = BenEntry::where('mobile_no', trim($request->mobile_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
          // dd( $mobile_count);
          if ($mobile_count > 0) {
            $errors = array();
            $errorMsg = "Mobile Number Already Exist! Please try different.";
            array_push($errors, $errorMsg);
            return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
          }
        }
        //--------- Duplicate bank A/C check---------- //
        $bankCount = BenEntry::whereRaw("trim(bank_code)=trim(" . "'" . $request->bank_account_number . "'" . ")")->where('id', '!=', $id)
          ->whereRaw("(" . $check_condition_str . ")")
          ->count('id');

        if ($bankCount > 0) {
          $errors = array();
          $errorMsg = "Bank A/C Already Exist!";
          array_push($errors, $errorMsg);
          return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('errors', $errors);
        }
        $count = BenEntry::where('aadhar_no', trim($request->aadhar_no))->where('id', '!=', $id)->whereRaw("(" . $check_condition_str . ")")->count('id');
        if ($count > 0) {
          $request->session()->put('dupAadhaarCheck', trim($request->aadhar_no));
          $errors = array();
          $errorMsg = "Aadhaar Number Already Exist! Please try different.";
          array_push($errors, $errorMsg);
          return redirect("/application-edit?id=" . $request->id . "&scheme_id=" . $request->scheme_id)->with('dupAadhaar', 1)->with('errors', $errors);
        }

        if (count($errors) > 0) {

          return back()->withErrors($errors)->withInput();

          // return redirect("fisherman")->withInput(Input::all())->with('errors',$errormsg);


        }
        $uploaded_doc = array();
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt')->where('scheme_code', $scheme_id)->get();
        if (!empty($doc_id_list[0]['doc_list_man'])) {
          $doc_list_man = json_decode($doc_id_list[0]['doc_list_man']);
        } else {
          $doc_list_man = array();
        }
        if (!empty($doc_id_list[0]['doc_list_opt'])) {
          $doc_list_opt = json_decode($doc_id_list[0]['doc_list_opt']);
        } else {
          $doc_list_opt = array();
        }
        $doc_list = array_merge($doc_list_man, $doc_list_opt);
        $doc_master = DocumentType::get();
        $encolserdata = BenDocs::where('scheme_id', $scheme_id)->where('created_by_dist_code', $distCode)->where('beneficiary_id', $pension_id)->get();
        $upload_file = array();
        $upload_file_arch = array();
        $delete_array = array();
        $i = 0;
        $j = 0;
        $c_time = date('Y-m-d H:i:s');
        $user_id = AuthChecker::getUserId();
        foreach ($doc_list as $doc) {
          if ($request->hasFile('doc_' . $doc)) {
            $doc_file = $request->file('doc_' . $doc);
            $img_data = file_get_contents($doc_file);
            $u_extension = $doc_file->getClientOriginalExtension();
            $mime_type = $doc_file->getMimeType();
            $doc_type_name = $doc_master->where('id', $doc)->first();
            if (strtolower($mime_type) == 'image/jpeg') {
              if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
                $extension = $u_extension;
              } else {
                $errors = array();
                $errorMsg = "You are trying to upload an incorrect file for " . $doc_type_name->doc_name;
                array_push($errors, $errorMsg);
                return back()->with('errors', $errors)->withInput(Input::all());
              }
            } else if (strtolower($mime_type) == 'image/png') {
              $extension = 'png';
            } else if (strtolower($mime_type) == 'image/gif') {
              $extension = 'gif';
            } else if (strtolower($mime_type) == 'application/pdf') {
              $extension = 'pdf';
            } else {
              $errors = array();
              $errorMsg = "You are trying to upload an incorrect file for " . $doc_type_name->doc_name;
              array_push($errors, $errorMsg);
              return back()->with('errors', $errors)->withInput(Input::all());
            }
            if ($u_extension != $extension) {
              $errors = array();
              $errorMsg = "You are trying to upload an incorrect file for " . $doc_type_name->doc_name;
              array_push($errors, $errorMsg);
              return back()->with('errors', $errors)->withInput(Input::all());
            }
            $base64 = base64_encode($img_data);
            $upload_file[$i]['beneficiary_id'] = $pension_id;
            $upload_file[$i]['created_by_dist_code'] = $distCode;
            $upload_file[$i]['created_by_local_body_code'] = $blockCode;
            $upload_file[$i]['document_type'] = $doc;
            $upload_file[$i]['scheme_id'] = $scheme_id;
            $upload_file[$i]['created_by_level'] = $mapping_level;
            $upload_file[$i]['created_at'] = $c_time;
            $upload_file[$i]['created_by'] = $user_id;
            $upload_file[$i]['ip_address'] = $request->ip();
            $upload_file[$i]['attched_document'] = $base64;
            $upload_file[$i]['document_mime_type'] = $mime_type;
            $upload_file[$i]['document_extension'] = $extension;
            if (!empty($doc_type_name)) {
              $upload_file[$i]['doc_type_name'] = $doc_type_name->doc_name;
            }
            $i++;
            $doc_already = $encolserdata->where('document_type', $doc)->where('created_by_dist_code', $distCode)->where('beneficiary_id', $pension_id)->first();
            if (!empty($doc_already)) {
              array_push($delete_array, $doc);
              $upload_file_arch[$j]['beneficiary_id'] = $pension_id;
              $upload_file_arch[$j]['created_by_dist_code'] = $doc_already->created_by_dist_code;
              $upload_file_arch[$j]['created_by_local_body_code'] = $doc_already->created_by_local_body_code;
              $upload_file_arch[$j]['document_type'] = $doc_already->document_type;
              $upload_file_arch[$j]['scheme_id'] = $doc_already->scheme_id;
              $upload_file_arch[$j]['created_by_level'] = $doc_already->created_by_level;
              $upload_file_arch[$j]['created_at'] = $doc_already->created_at;
              $upload_file_arch[$j]['created_by'] = $doc_already->created_by;
              $upload_file_arch[$j]['ip_address'] = $doc_already->ip_address;
              $upload_file_arch[$j]['attched_document'] = $doc_already->attched_document;
              $upload_file_arch[$j]['document_mime_type'] = $doc_already->document_mime_type;
              $upload_file_arch[$j]['document_extension'] = $doc_already->document_extension;
              $j++;
            }
          }
        }

        if ($housing_details != null) {
          $housing_details->created_by = $created_by;
          $housing_details->created_by_level = $mapping_level;
          $housing_details->ben_fname = $first_name;
          if (!empty($middle_name))
            $housing_details->ben_mname = $middle_name;

          $housing_details->ben_lname = $last_name;
          $housing_details->gender = $gender;
          $housing_details->app_phase = $app_phase;
          $housing_details->temple_type = $temple_type;

          $housing_details->caste = $caste_category;
          $housing_details->dob = $dob;
          $housing_details->ben_age = $txt_age;
          $housing_details->father_fname = $father_first_name;

          if (!empty($father_middle_name))
            $housing_details->father_mname = $father_middle_name;

          $housing_details->father_lname = $father_last_name;

          if (!empty($mother_first_name))
            $housing_details->mother_fname = $mother_first_name;
          if (!empty($mother_middle_name))
            $housing_details->mother_mname = $mother_middle_name;
          if (!empty($mother_last_name))
            $housing_details->mother_lname = $mother_last_name;
          $housing_details->marital_status = $marital_status;
          if (!empty($spouse_first_name))
            $housing_details->spouse_fname = $spouse_first_name;
          if (!empty($spouse_middle_name))
            $housing_details->spouse_mname = $spouse_middle_name;
          if (!empty($spouse_last_name))
            $housing_details->spouse_lname = $spouse_last_name;
          if (!empty($ration_card_cat))
            $housing_details->ration_card_cat = $ration_card_cat;
          if (!empty($ration_card_no))
            $housing_details->ration_card_no = $ration_card_no;
          if (!empty($aadhar_no))
            $housing_details->aadhar_no = $aadhar_no;
          if (!empty($epic_voter_id))
            $housing_details->epic_voter_id = $epic_voter_id;
          $housing_details->pan_no = $pan_no;

          $housing_details->dist_code = $district;
          $housing_details->rural_urban_id = $urban_code;
          if (!empty($middle_name)) {
            $housing_details->assembly_code = $asmb_cons;
            $housing_details->assembly_name = $assembly_name;
          }
          $housing_details->police_station = $police_station;
          $housing_details->block_ulb_code = $block;
          $housing_details->gp_ward_code = $gp_ward;
          $housing_details->village_town_city = $village;
          if (!empty($house))
            $housing_details->house_premise_no = $house;
          $housing_details->post_office = $post_office;
          $housing_details->pincode = $pin_code;
          if (!empty($cur_per_same))
            $housing_details->cur_per_address_is_equal = $cur_per_same;
          if (!empty($district_cur))
            $housing_details->dist_code_cur = $district_cur;

          if (!empty($asmb_cons_cur))
            $housing_details->assembly_code_cur = $asmb_cons_cur;

          if (!empty($urban_code_cur))
            $housing_details->rural_urban_id_cur = $urban_code_cur;

          if (!empty($block_cur))
            $housing_details->block_ulb_code_cur = $block_cur;

          if (!empty($gp_ward_cur))
            $housing_details->gp_ward_code_cur = $gp_ward_cur;

          if (!empty($village_cur))
            $housing_details->village_town_city_cur = $village_cur;

          if (!empty($house_cur))
            $housing_details->house_premise_no_cur = $house_cur;

          if (!empty($post_office_cur))
            $housing_details->post_office_cur = $post_office_cur;

          if (!empty($pin_code_cur))
            $housing_details->pincode_cur = $pin_code_cur;

          if (!empty($police_station_cur))
            $housing_details->police_station_cur = $police_station_cur;

          if (!empty($residency_period))
            $housing_details->residency_period = $residency_period;
          $housing_details->mobile_no = $mobile_no;
          $housing_details->email = $email;
          if (!empty($mouza_name))
            $housing_details->mouza_name = $mouza_name;
          if (!empty($land_jlno))
            $housing_details->land_jlno = $land_jlno;
          if (!empty($khatian_no))
            $housing_details->khatian_no = $khatian_no;
          if (!empty($plot_no))
            $housing_details->plot_no = $plot_no;
          if (!empty($land_area))
            $housing_details->land_area = $land_area;
          if (!empty($land_holdername))
            $housing_details->land_holdername = $land_holdername;



          $housing_details->bank_name = $name_of_bank;
          $housing_details->branch_name = $bank_branch;
          $housing_details->bank_code = $bank_account_number;
          $housing_details->bank_ifsc = $bank_ifsc_code;
          $housing_details->npci_bank_code = $new_bank_code;
          if (!empty($ssp_y_n))
            $housing_details->ssp_y_n = $ssp_y_n;
          if (!empty($pucca_house_y_n))
            $housing_details->pucca_house_y_n = $pucca_house_y_n;
          if (!empty($nominate_name))
            $housing_details->nominate_name = $nominate_name;
          if (!empty($nominate_address))
            $housing_details->nominate_address = $nominate_address;
          if (!empty($nominate_relationship))
            $housing_details->nominate_relationship = $nominate_relationship;
          if (!empty($av_status))
            $housing_details->av_status = $av_status;
          if (!empty($receive_pension))
            $housing_details->receive_pension = $receive_pension;
          if (!empty($receiving_pension_other_source_1))
            $housing_details->receiving_pension_other_source_1 = $receiving_pension_other_source_1;
          if (!empty($receiving_pension_other_source_2))
            $housing_details->receiving_pension_other_source_2 = $receiving_pension_other_source_2;

          // $housing_details->created_by = Auth::user()->id;
          // $housing_details->created_by_level = $request->session()->get('level');
          // $housing_details->created_by_dist_code = $request->session()->get('distCode');
          // $housing_details->created_by_local_body_code = $request->session()->get('blockCode');

          $housing_details->scheme_id = $housingSchemeCode;

          if ($urban_code == 1) {
            $housing_details->block_ulb_name = $block_ulb_db->urban_body_name;
            $housing_details->gp_ward_name = $gp_ward_db->urban_body_ward_name;
          } else {
            $housing_details->block_ulb_name = $block_ulb_db->block_name;
            $housing_details->gp_ward_name = $gp_ward_db->gram_panchyat_name;
          }
          $housing_details->assembly_name = $assembly_name;
        }
        $pension_details->ben_fname = $first_name;
        if (!empty($middle_name))
          $pension_details->ben_mname = $middle_name;
        $pension_details->created_by = $created_by;
        $pension_details->created_by_level = $mapping_level;
        $pension_details->ben_lname = $last_name;
        $pension_details->gender = $gender;
        $pension_details->app_phase = $app_phase;
        $pension_details->temple_type = $temple_type;

        $pension_details->caste = $caste_category;
        $pension_details->dob = $dob;
        $pension_details->ben_age = $txt_age;
        $pension_details->father_fname = $father_first_name;

        if (!empty($father_middle_name))
          $pension_details->father_mname = $father_middle_name;

        $pension_details->father_lname = $father_last_name;

        if (!empty($mother_first_name))
          $pension_details->mother_fname = $mother_first_name;
        if (!empty($mother_middle_name))
          $pension_details->mother_mname = $mother_middle_name;
        if (!empty($mother_last_name))
          $pension_details->mother_lname = $mother_last_name;
        $pension_details->marital_status = $marital_status;
        if (!empty($spouse_first_name))
          $pension_details->spouse_fname = $spouse_first_name;
        if (!empty($spouse_middle_name))
          $pension_details->spouse_mname = $spouse_middle_name;
        if (!empty($spouse_last_name))
          $pension_details->spouse_lname = $spouse_last_name;
        if (!empty($ration_card_cat))
          $pension_details->ration_card_cat = $ration_card_cat;
        if (!empty($ration_card_no))
          $pension_details->ration_card_no = $ration_card_no;
        if (!empty($aadhar_no))
          $pension_details->aadhar_no = $aadhar_no;
        if (!empty($epic_voter_id))
          $pension_details->epic_voter_id = $epic_voter_id;
        $pension_details->pan_no = $pan_no;

        $pension_details->dist_code = $district;
        $pension_details->rural_urban_id = $urban_code;
        if (!empty($middle_name)) {
          $pension_details->assembly_code = $asmb_cons;
          $pension_details->assembly_name = $assembly_name;
        }
        $pension_details->police_station = $police_station;
        $pension_details->block_ulb_code = $block;
        $pension_details->gp_ward_code = $gp_ward;
        $pension_details->village_town_city = $village;
        if (!empty($house))
          $pension_details->house_premise_no = $house;
        $pension_details->post_office = $post_office;
        $pension_details->pincode = $pin_code;
        if (!empty($cur_per_same))
          $pension_details->cur_per_address_is_equal = $cur_per_same;
        if (!empty($district_cur))
          $pension_details->dist_code_cur = $district_cur;

        if (!empty($asmb_cons_cur))
          $pension_details->assembly_code_cur = $asmb_cons_cur;

        if (!empty($urban_code_cur))
          $pension_details->rural_urban_id_cur = $urban_code_cur;

        if (!empty($block_cur))
          $pension_details->block_ulb_code_cur = $block_cur;

        if (!empty($gp_ward_cur))
          $pension_details->gp_ward_code_cur = $gp_ward_cur;

        if (!empty($village_cur))
          $pension_details->village_town_city_cur = $village_cur;

        if (!empty($house_cur))
          $pension_details->house_premise_no_cur = $house_cur;

        if (!empty($post_office_cur))
          $pension_details->post_office_cur = $post_office_cur;

        if (!empty($pin_code_cur))
          $pension_details->pincode_cur = $pin_code_cur;

        if (!empty($police_station_cur))
          $pension_details->police_station_cur = $police_station_cur;

        if (!empty($residency_period))
          $pension_details->residency_period = $residency_period;
        $pension_details->mobile_no = $mobile_no;
        $pension_details->email = $email;
        if (!empty($mouza_name))
          $pension_details->mouza_name = $mouza_name;
        if (!empty($land_jlno))
          $pension_details->land_jlno = $land_jlno;
        if (!empty($khatian_no))
          $pension_details->khatian_no = $khatian_no;
        if (!empty($plot_no))
          $pension_details->plot_no = $plot_no;
        if (!empty($land_area))
          $pension_details->land_area = $land_area;
        if (!empty($land_holdername))
          $pension_details->land_holdername = $land_holdername;



        $pension_details->bank_name = $name_of_bank;
        $pension_details->branch_name = $bank_branch;
        $pension_details->bank_code = $bank_account_number;
        $pension_details->bank_ifsc = $bank_ifsc_code;
        $pension_details->npci_bank_code = $new_bank_code;
        if (!empty($ssp_y_n))
          $pension_details->ssp_y_n = $ssp_y_n;
        if (!empty($pucca_house_y_n))
          $pension_details->pucca_house_y_n = $pucca_house_y_n;
        if (!empty($nominate_name))
          $pension_details->nominate_name = $nominate_name;
        if (!empty($nominate_address))
          $pension_details->nominate_address = $nominate_address;
        if (!empty($nominate_relationship))
          $pension_details->nominate_relationship = $nominate_relationship;
        if (!empty($av_status))
          $pension_details->av_status = $av_status;
        if (!empty($receive_pension))
          $pension_details->receive_pension = $receive_pension;
        if (!empty($receiving_pension_other_source_1))
          $pension_details->receiving_pension_other_source_1 = $receiving_pension_other_source_1;
        if (!empty($receiving_pension_other_source_2))
          $pension_details->receiving_pension_other_source_2 = $receiving_pension_other_source_2;

        // $pension_details->created_by = Auth::user()->id;
        // $pension_details->created_by_level = $request->session()->get('level');
        // $pension_details->created_by_dist_code = $request->session()->get('distCode');
        // $pension_details->created_by_local_body_code = $request->session()->get('blockCode');
        // //$pension_details->scheme_id =  $scheme_id;
        $pension_details->scheme_id = $monthlySchemeCode;

        if ($urban_code == 1) {
          $pension_details->block_ulb_name = $block_ulb_db->urban_body_name;
          $pension_details->gp_ward_name = $gp_ward_db->urban_body_ward_name;
        } else {

          $pension_details->block_ulb_name = $block_ulb_db->block_name;
          $pension_details->gp_ward_name = $gp_ward_db->gram_panchyat_name;
        }
        $pension_details->assembly_name = $assembly_name;
        $pension_details->dup_bank = 0;
        $pension_details->dup_aadhar = 0;
        $pension_details->dup_mobile = 0;
        $pension_details->no_aadhar = 0;
        $pension_details->no_mobile = 0;
        $pension_details->is_reverted = NULL;
        DB::beginTransaction();
        DB::connection('pgsql_encwrite')->beginTransaction();
        DB::connection('pgsqlpurohitmonthly')->beginTransaction();
        $arch_status = DB::statement("INSERT INTO purohit_monthly.arc_beneficiary(id, 
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
           av_status
           
           ) (SELECT id, 
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
           av_status  
           from purohit_monthly.beneficiary where id=" . $id . ")");
        $update_status1 = $pension_details->save();
        if ($housing_details != null) {
          $update_status2 = $housing_details->save();
        } else {
          $update_status2 = 1;
        }
        if ($update_status1 && $update_status2) {
          if (count($upload_file_arch) > 0) {
            $doc_inserted_arch = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_arch')->insert($upload_file_arch);
          } else {
            $doc_inserted_arch = 1;
          }
          if (count($delete_array) > 0) {
            $doc_inserted_del = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('beneficiary_id', $request->id)->whereIn('document_type', $delete_array)->delete();
          } else {
            $doc_inserted_del = 1;
          }
          if (count($upload_file) > 0) {
            $doc_inserted = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($upload_file);
          } else {
            $doc_inserted = 1;
          }
          $i = 0;
          $accept_reject_model = new AcceptRejectInfo;
          $accept_reject_model->created_at = $c_time;
          $accept_reject_model->application_id = $pension_id;
          $accept_reject_model->scheme_id = $request->scheme_id;
          $accept_reject_model->user_id = $user_id;
          $accept_reject_model->op_type = 'APPUPDATE';

          $accept_reject_model->ip_address = $request->ip();
          $is_saved_log = $accept_reject_model->save();
          if ($doc_inserted_arch && $doc_inserted_del && $doc_inserted && $is_saved_log && $update_status1 && $update_status2) {
            DB::commit();
            DB::connection('pgsql_encwrite')->commit();
            DB::connection('pgsqlpurohitmonthly')->commit();
            $return_status = 'success';
            $msg = "Application Updated Successfully.";
          } else {
            DB::connection('pgsqlpurohitmonthly')->rollback();
            DB::rollback();
            DB::connection('pgsql_encwrite')->rollback();
            $id = 0;
            $return_status = 'error';
            $msg = "Updation Failed..Please try again.";
          }
          if ($designation_id == 'Operator') {
            return redirect("application-list-read-only-edit?pr1=" . $scheme_row->short_code)->with($return_status, $msg)
              ->with('id', $row->getBenidAttribute());
          } else {
            return redirect('/')->with('success', 'Application Updated Successfully');
          }
        } else {
          if ($designation_id == 'Operator') {
            return redirect("application-list-read-only-edit?pr1=" . $scheme_row->short_code)->with('error', 'Data Submission Failure.Please try again.')
              ->with('id', $row->getBenidAttribute());
          } else {
            return redirect('/')->with('error', 'Some error.Please try again');
          }
        }
      } else {
        return redirect("mainform")->with('error', 'Something wrong');
      }
    } else {
      return redirect("mainform")->with('error', 'Something wrong');
    }
  }
  public function getGroupName($groupId)
  {
    $groupArr = Config::get('constants.document_group');
    $groupDescription = "NA";
    foreach ($groupArr as $key => $value) {
      if ($key == $groupId) {
        $groupDescription = $value;
        break;
      }
    }
    return $groupDescription;
  }





  private function validateInput($request, $scheme_id)
  {
    //echo "hi";die;
    //echo $scheme_id;die;
    $doc_id_list = SchemeDocMap::select('doc_list_man')->where('scheme_code', $scheme_id)->first();
    if (!empty($doc_id_list->doc_list_man)) {
      $in_array = json_decode($doc_id_list->doc_list_man);

      $doc_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->get();


      $singleArray = array();
      $nicenameArray = array();
      $customMessage = array();
      foreach ($doc_list as $key => $value) {

        if (in_array($value->id, $in_array)) {
          $required = 'required';
        } else {
          $required = 'nullable';
        }


        // $multiArray[$val->id]= array('id'=>$val->id,'required'=>$required,'mime'=>$val->doc_type, 'size'=>$val->doc_size_kb);
        $singleArray['doc_' . $value->id] = $required . '|mimes:' . $value->doc_type . '|max:' . $value->doc_size_kb . ',';
        $nicenameArray['doc_' . $value->id] = $value->doc_name . ',';
        $customMessage['doc_' . $value->id . '.max'] = "The file uploaded for :attribute size must be less than :max KB";
        $customMessage['doc_' . $value->id . '.mimes'] = "The file uploaded for :attribute must be of type " . $value->doc_type;
        $customMessage['doc_' . $value->id . '.required'] = "Document for :attribute must be uploaded";
      }
    } else {
      $singleArray = array();
      $nicenameArray = array();
      $customMessage = array();
    }


    $housingSlug = $this->pr1ListPurohit['housing']['slug'];

    $this->validate($request, array_merge([
      //'first_name' => 'required|string|max:200',
      'first_name' => 'required|string|max:200',
      'middle_name' => 'string|nullable',
      'last_name' => 'required|string|max:200',
      'gender' => 'required',
      'app_phase' => 'required',
      'temple_type' => 'required',
      'caste_category' => 'required',
      // 'dob' => '',
      'txt_age' => 'required|numeric',

      'father_first_name' => 'required|string|max:200',
      'father_middle_name' => 'string|nullable',
      'father_last_name' => 'required|string|max:200',
      'mother_first_name' => 'string|nullable|max:200',
      'mother_middle_name' => 'string|nullable',
      'mother_last_name' => 'string|nullable|max:200',

      'marital_status' => 'required',

      'spouse_first_name' => 'string|nullable',
      'spouse_middle_name' => 'string|nullable',
      'spouse_last_name' => 'string|nullable',


      'ration_card_cat' => 'string|nullable',
      'ration_card_no' => 'string|nullable|max:11',


      'aadhar_no' => 'required|numeric|digits:12',
      'epic_voter_id' => 'string|nullable|max:20',
      'pan_no' => 'string|nullable|max:12',



      //  'district' => 'string',
      'asmb_cons' => 'required|string|nullable',
      'police_station' => 'required|string',
      //'block' => 'max:200',
      // 'gp_ward' => 'max:200',
      'village' => 'required|string|max:300',
      'house' => 'string|nullable',
      'post_office' => 'required|string',
      'pin_code' => 'required|numeric|digits:6',
      'residency_period' => 'nullable|integer',
      'mobile_no' => 'required|numeric|digits:10',
      'email' => 'string|email|nullable',
      'mouza_name' => 'required_if:code,=,$housingSlug',
      'land_jlno' => 'required_if:code,=,$housingSlug',
      'khatian_no' => 'required_if:code,=,$housingSlug',
      'plot_no' => 'required_if:code,=,$housingSlug',
      'land_area' => 'required_if:code,=,$housingSlug',
      'land_holdername' => 'required_if:code,=,$housingSlug',


      'name_of_bank' => 'required|string|max:200',
      'bank_branch' => 'required|string|max:200',
      'bank_account_number' => 'required|numeric',
      'bank_ifsc_code' => 'required|string',



    ], $singleArray), $customMessage, $nicenameArray);
    // echo "ok";die;
  }

  /************************REPORT**********************************/
  public function report($scheme, $approved_rejected)
  {
    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id', '=', $user_id)->first();
    // echo "<pre>";print_r($duty);die();
    $district_code = $duty->district_code;
    $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();
    $scheme_name = '';
    // if($request->get('pr1')){
    if ($scheme == 'monthly') {
      $scheme_name = 'Monthly Financial Scheme';
    } else if ($scheme == 'housing') {
      $scheme_name = 'Both Monthly Financial and One time Housing Scheme';
    }
    // }

    $app_rej = 0;
    if ($approved_rejected == 'T') {
      $app_rej = -1;
    } else if ($approved_rejected == 'F') {
      $app_rej = null;
    } else if ($approved_rejected == 'V') {
      $app_rej = 107;
    } else if ($approved_rejected == 'R') {
      $app_rej = 106;
    }
    return view('PurohitICAD.report')->with('district_name', $district_name)->with('district_code', $district_code)->with('scheme', $scheme)->with('scheme_name', $scheme_name)
      ->with('approved_rejected', $app_rej);
  }
  public function getProcessedData(Request $request)
  {
    //DB::enableQueryLog();
    if (request()->ajax()) {
      $user_id = AuthChecker::getUserId();
      $duty = Configduty::where('user_id', '=', $user_id)->first();
      $district_code = $request->level1;
      $district_name = $request->level2;
      $serachvalue = $request->search['value'];
      $scheme = $request->scheme;

      $model_name = "App\\BenEntry";
      // if($request->get('pr1')){
      if ($scheme == 'monthly') {
        $scheme_id = 17;
        $model_name = "App\\BenEntry";
      } else if ($scheme == 'housing') {
        $scheme_id = 18;
        $model_name = "App\\PensionPurohitHousingICAD";
      }
      // }

      //Urban/Rural
      $level = $duty->is_urban;

      $flag = 1;
      $totalRecords = 0;
      $filterRecords = 0;
      $data = array();

      // WORKING QUERY

      $limit = $request->input('length');
      $offset = $request->input('start');

      //approved_rejected=0 is Approved, -1 is Rejected
      $approved_rejected = $request->approved_rejected;

      $condition = array();
      $condition["next_level_role_id"] = $approved_rejected;
      if (!empty($district_code)) {
        $condition["created_by_dist_code"] = $district_code;
      }
      if (!empty($level)) {
        //'Rural'
        if ($level == 2) {
          if (!empty($duty->taluka_code)) {
            $condition["created_by_local_body_code"] = $duty->taluka_code;
          }
        }
        //'Urban'
        if ($level == 1) {
          if (!empty($duty->urban_body_code)) {
            $condition["created_by_local_body_code"] = $duty->urban_body_code;
          }
        }
      }
      $scheme_length = NULL;
      $id_length = NULL;
      $scheme_row = Scheme::where('id', $scheme_id)->first();
      $scheme_schema = $scheme_row->short_code;
      $scheme_length = $scheme_row->scheme_length;
      $id_length = $scheme_row->id_length;
      if (empty($scheme_schema)) {
        $scheme_schema = 'pension';
      }

      $query = DB::connection('pgsql_mis')->table('' . $scheme_schema . '.beneficiary')->where($condition);
      if (empty($serachvalue)) {
        $totalRecords = $query->count('id');
        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
          'id',
          'created_by_dist_code',
          'bank_code',
          'ben_fname',
          'ben_lname',
          'ben_mname',
          'gender',
          'ben_age',
          'block_ulb_name',
          'gp_ward_name',
          'bank_ifsc',
          'village_town_city',
          'scheme_id',
          'lot_generated',
          'payment_count',
          'next_level_role_id',
          'is_state',
          'app_phase',
          'temple_type'
        ]);
      } else {
        if (is_numeric($serachvalue)) {
          $ben_id = substr($serachvalue, -7);
          $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
            $query1->where('id', $ben_id)
              ->orWhere('bank_code', $serachvalue);
          });
          $totalRecords = $query->count('id');
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'id',
              'created_by_dist_code',
              'bank_code',
              'ben_fname',
              'block_ulb_name',
              'gp_ward_name',
              'bank_ifsc',
              'village_town_city',
              'scheme_id',
              'lot_generated',
              'payment_count',
              'next_level_role_id',
              'ben_lname',
              'gender',
              'ben_age',
              'ben_mname',
              'is_state',
              'app_phase',
              'temple_type'
            ]
          );
        } else {
          $query = $query->where(function ($query1) use ($serachvalue) {
            $query1->where('ben_fname', 'like', $serachvalue . '%')
              ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
              ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
              ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
          });
          $totalRecords = $query->count('id');
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'id',
              'created_by_dist_code',
              'bank_code',
              'ben_fname',
              'block_ulb_name',
              'gp_ward_name',
              'bank_ifsc',
              'village_town_city',
              'scheme_id',
              'lot_generated',
              'payment_count',
              'next_level_role_id',
              'ben_lname',
              'gender',
              'ben_age',
              'ben_mname',
              'is_state',
              'app_phase',
              'temple_type'
            ]
          );
        }
        $filterRecords = count($data);
      }
      return datatables()
        ->of($data)
        ->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {
          $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);
          return $app_id;
        })
        ->addColumn('app_phase', function ($data) {
          return $data->app_phase;
        })
        ->addColumn('ben_id', function ($data) {
          return $data->id;
        })
        ->addColumn('ben_name', function ($data) {
          return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
        })
        ->addColumn('ben_fname', function ($data) {
          return "Father Name";
        })
        ->addColumn('village_town_city', function ($data) {
          return $data->village_town_city;
        })
        ->addColumn('bank_ifsc', function ($data) {
          return $data->bank_ifsc;
        })
        ->addColumn('bank_code', function ($data) {
          return $data->bank_code;
        })
        ->addColumn('action', function ($data) {
          $val = '';
          //'<div class="btn-group" role="group" >';
          $val = $val . '<button class="btn btn-primary ben_view_button">View</button>';
          // $val = $val . '<button class="btn btn-warning ben_reject_button">Reject</button>';
          return $val;
        })
        ->rawColumns(['ben_id', 'id', 'ben_name', 'app_phase', 'village_town_city', 'action'])
        ->make(true);
    }
    return view('PurohitICAD.report')->with('district_name', $district_name)->with('district_code', $district_code);
  }
  public function showApplicantDetails(Request $request)
  {
    if (empty($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Found');
    }
    if (!is_numeric($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    $user_id = AuthChecker::getUserId();
    $reject_revert_cause_list = RejectRevertReason::where('status', true)->get();
    $id = $request->id;
    $appPrefix = "App";
    $scheme_id = 17;
    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed.');
    }
    $district_code = $duty_obj->district_code;
    $mappingLevel = $duty_obj->mapping_level;
    if ($duty_obj->mapping_level == "Subdiv") {
      $created_by_local_body_code = $duty_obj->urban_body_code;
    } else if ($duty_obj->mapping_level == "Block") {
      $created_by_local_body_code = $duty_obj->taluka_code;
    }
    if ($mappingLevel == 'Department' || $mappingLevel == 'State') {
      $row = BenEntry::where('id', $id)->first();
    } else
      $row = BenEntry::where('id', $id)->where('created_by_dist_code', $district_code)->first();

    if (empty($row)) {
      return redirect("/")->with('danger', 'Not Allowed..');
    }
    if ($row->scheme_id != $scheme_id) {
      return redirect("/")->with('danger', 'Not Allowed...');
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    $district_state_name = '';
    $urban_code_state_name = '';
    $block_subdiv_state_name = '';
    $housingrecord = '';
    if ($scheme_id == 17) {
      if ($row->housing_code != '') {
        $code = $row->housing_code;
        $housingrecord = PensionPurohitHousingICAD::where('id', '=', $row->housing_code)->first();
        if ($housingrecord->next_level_role_id == -1) {
          $housingrecord = '';
        }
      }
      // $docs = BenDocsPurohitMonthlyICAD::where('ben_id', $id)->get();
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $row->created_by_dist_code)->where('created_by_local_body_code', $row->created_by_local_body_code)->orderBy('document_type')->get();
      // dd($docs);
    }
    // else if($scheme_id==$this->monthlySchemeCode){       
    //   $docs = $this->monthlyDocTable::where('ben_id',$id)->get();
    // }
    // else if($scheme_id==$this->housingSchemeCode){
    //   //$row = Manabik::find($id);          
    //   $docs = $this->hosingDocTable::where('ben_id',$id)->get();
    // }
    if ($row->dist_code != "") {
      $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
      $district_name = $district->district_name;
    }
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
    $doc_profile_image = DocumentType::get()->where("is_profile_pic", true)->first();
    $doc_profile_image_id = 999;
    $scheme_capacity_arr = Helper::getCapacity($scheme_id, $district_code);
    if ($scheme_capacity_arr['visible'] == 1) {
      if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
        $approveBtnvisible = 0;
      } else {
        $approveBtnvisible = 1;
      }
    } else {
      $approveBtnvisible = 1;
    }
    if ($scheme_id == 17) {
      $role = MapLavel::where('scheme_id', $scheme_id)->where('role_name', Auth::user()->designation_id)->where('stack_level', $duty_obj->mapping_level)->first();
      return view('PurohitICAD/pension_view_details', [
        'district_state_name' => $district_state_name,
        'block_subdiv_state_name' => $block_subdiv_state_name,
        'approveBtnvisible' => $approveBtnvisible,
        'scheme_capacity_arr' => $scheme_capacity_arr,
        'parent_id' => $role->parent_id,
        'scheme_id' => $scheme_id,
        'housingrecord' => $housingrecord,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'docs' => $docs,
        'image_id' => $doc_profile_image_id,
        'reject_revert_cause_list' => $reject_revert_cause_list
      ]);
    }
  }
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
