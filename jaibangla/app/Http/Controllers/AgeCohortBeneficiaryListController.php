<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;
use Auth;
use App\Configduty;
use App\lot_master;
use App\Scheme;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Excel;

class AgeCohortBeneficiaryListController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(200);
    date_default_timezone_set('Asia/Kolkata'); 
  }
  private function getSchemaName($scheme_id) {
    if (!is_null($scheme_id)) {
      $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
      //$parameter['scheme_id'] = $scheme_id;
      $schema_name =  $sObj->short_code;
      //dd($schema_name);
      if (empty($schema_name)){
        $schema_name = 'pension';
      }
      $table_name =  strtolower($schema_name) . '.beneficiaries';
    }
    else {
      $table_name =  'pension.beneficiaries';
    }
    return $table_name;
  }
  /*Landing Page*/
  public function index(Request $request) {
    $user_id = Auth::user()->id;
    $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1 and scheme_id in(10))"));
    return view('ageCohortBenList.index', ['schemes' => $scheme]);
  }
  /* Get Data */
  public function getAgeCohortBenList(Request $request) {
    if ($request->ajax()) {
      $roleArray = $request->session()->get('role');
      $scheme_id = $request->scheme_id;
      $ageGroup = $request->age_group;
      $data = $this->getCommonDataPull($roleArray, $scheme_id, $ageGroup);
      return datatables()->of($data)
      ->make(true);
    }
  }

  public function getAgeCohortGroupList(Request $request) {
    $response = [];
    $statusCode = 200;
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $scheme_id = $request->scheme_id;
      $ageGroup = Config::get('constants.age_cohort_list.'.$scheme_id);
      $response = $ageGroup;
    } catch(\Exception $e) {
      $response = array(
        'exception' => true,
        'exception_message' => $e->getMessage(),
      );
      $statusCode = 400;
    } finally {
      return response()->json($response, $statusCode);
    }
  }

  public function generateAgeCohortGroupListExcel(Request $request) {
    $roleArray = $request->session()->get('role');
    $scheme_id = $request->scheme_id;
    $ageGroup = $request->age_group;
    $data = $this->getCommonDataPull($roleArray, $scheme_id, $ageGroup);
    // print 1;
    $benarr[] = array('Applicant ID',  'Applicant Name',  'Father\'s Name', 'Mobile No', 'Block/Municipality',  'GP/Ward', 'Age Group');
    foreach($data as $arr) {
      $benarr[] = array(
        'Applicant ID' => $arr->id,  
        'Applicant Name' => trim($arr->name),  
        'Father\'s Name' => trim($arr->father_name), 
        'Mobile No' => trim($arr->mobile_no), 
        'Block/Municipality' => trim($arr->block_ulb_name),  
        'GP/Ward' => trim($arr->gp_ward_name), 
        'Age Group' => $arr->age_group
      );
    }
    $date = date('l d-M-Y h-i-s A');
    Excel::create('Beneficiary Age Group '.$ageGroup.' data'.$date, function($excel) use ($benarr){
      $excel->setTitle('Beneficiary Data');
      $excel->sheet('Age Group Data', function($sheet) use ($benarr){
        $sheet->fromArray($benarr, null, 'A1', false, false);
      });
    })->download('xlsx'); 
  }

  private function getCommonDataPull($roleArray, $scheme_id, $ageGroup) {
      $designation = Auth::user()->designation_id_old;
      foreach ($roleArray as $roleObj) {
        if ($roleObj['scheme_id'] == $scheme_id) {
          $is_active = 1;
          $level = $roleObj['mapping_level'];
          $is_urban = $roleObj['is_urban'];
          $distCode = $roleObj['district_code'];
          $is_state_login = $roleObj['is_state_login'];
          if ($roleObj['is_urban'] == 1) {
            $blockCode = $roleObj['urban_body_code'];
          } else {
            $blockCode = $roleObj['taluka_code'];
          }
          break;
        }
      }
      // dd($is_active);
      if ($is_active == 0) {
        return redirect("/")->with('error', 'User Disabled');
      }
      $table_name = 'pension.beneficiaries';
      $query = "";
      $query = "SELECT 
        CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name, 
        CONCAT(father_fname,' ',father_mname,' ',father_lname) as father_name,
        id, created_by_dist_code, created_by_local_body_code, block_ulb_code, block_ulb_name, gp_ward_name, gp_ward_code, mobile_no,
        CASE 
          WHEN (extract(year from current_date)-extract(year from dob))<60 THEN 'Less then 60 years' 
          WHEN ((extract(year from current_date)-extract(year from dob))>=90 and (extract(year from current_date)-extract(year from dob))<100) THEN 'Between 90 to 99 years'
          WHEN (extract(year from current_date)-extract(year from dob))>=100 THEN 'Above 100 years' 
        END AS age_group 
        FROM ".$table_name." WHERE created_by_dist_code=".$distCode." AND next_level_role_id = 0 AND scheme_id = ".$scheme_id." AND
        (
          (
            (extract(year from current_date)-extract(year from dob))<60
          ) or 
          (
            (extract(year from current_date)-extract(year from dob))>=90 and (extract(year from current_date)-extract(year from dob))<100
          ) or 
          (
            (extract(year from current_date)-extract(year from dob))>=100
          )
        )";
      if ($designation == 'Verifier') {
        $query .= ' AND created_by_local_body_code='.$blockCode;
      }

      if ($ageGroup == '<60') {
        $query .= ' AND (extract(year from current_date)-extract(year from dob))<60';
      }

      else if ($ageGroup == '90-99') {
        $query .= ' AND ((extract(year from current_date)-extract(year from dob))>=90 and (extract(year from current_date)-extract(year from dob))<100)';
      }
      else if ($ageGroup == '>=100') {
        $query .= ' AND (extract(year from current_date)-extract(year from dob))>=100';
      }
      // print $query;die;
      $data = DB::connection('pgsql')->select($query);
      return $data;
  }
}
