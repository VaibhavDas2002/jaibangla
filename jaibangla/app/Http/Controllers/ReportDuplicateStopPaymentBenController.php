<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DupliacteApproveReject;
use App\Scheme;
use App\District;
use App\BeneficiaryPensions;
use App\PensionSc;
use App\PensionSt;
use App\Manabik;
use App\UpdateBenDetails;
use App\Configduty;
use App\GP;
use App\SubDistrict;
use App\Taluka;
use App\UrbanBody;
use App\Ward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\AuthChecker;


class ReportDuplicateStopPaymentBenController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(600);
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

  public function index1(Request $request)
  {
    $is_active = 0;
    $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();;
    $user_id = AuthChecker::getUserId();
    $designation_id = Auth::user()->designation_id;
    $district_visible = $is_urban_visible = $block_visible = 1;
    $municipality_visible = 0;
    $gp_ward_visible = 0;
    $muncList = collect([]);
    $gpList = collect([]);
    $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . " and scheme_id in(2,10,11) )"));
    if ($designation_id == 'Admin' || $designation_id == 'HOD' ||  $designation_id == 'Dashboard') {
      $district_visible = $is_urban_visible = $block_visible = 1;
    } else if ($designation_id == 'Approver' || $designation_id == 'Verifier') {
      $district_code = NULL;
      $is_urban = NULL;
      $blockCode = NULL;
      foreach ($roleArray as $roleObj) {
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
    return view(
      'report-duplicate-stop-payment/index',
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
        'gpList' => $gpList,
        'muncList' => $muncList
      ]
    );
  }
  public function linelistingReport(Request $request)
  {
    $scheme_id = $request->scheme_id;
    $district = $request->district;
    $urban_code = $request->urban_code;
    $block = $request->block;
    $muncid = $request->muncid;
    $gp_ward = $request->gp_ward;
    $fin_year = $request->fin_year;
    $heading_msg = '';
    $title = "";
    $schemeObj = Scheme::where('id', $scheme_id)->first();
    if (!empty($district)) {
      $district_row = District::where('district_code', $district)->first();
    }

    if (!empty($block)) {
      if ($urban_code == 1) {
        $block_ulb = SubDistrict::where('sub_district_code', '=', $block)->first();
        $blk_munc_name = $block_ulb->sub_district_name;
      } else {
        $block_ulb = Taluka::where('block_code', '=', $block)->first();
        $blk_munc_name = $block_ulb->block_name;
      }
    } else {
    }
    if (!empty($gp_ward)) {

      if ($urban_code == 1) {
        $gp_ward_row = Ward::where('urban_body_ward_code', '=', $gp_ward)->first();
        $gp_ward_name = $gp_ward_row->urban_body_ward_name;
        //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
      } else {
        $gp_ward_row = GP::where('gram_panchyat_code', '=', $gp_ward)->first();
        $gp_ward_name = $gp_ward_row->gram_panchyat_name;
        // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
      }
    }
    $rules = [
      'scheme_code' => 'nullable|integer',
      'district' => 'nullable|integer',
      'urban_code' => 'nullable|integer',
      'block' => 'nullable|integer',
      'muncid' => 'nullable|integer',
      'gp_ward' => 'nullable|integer',
      'fin_year' => 'required'
    ];
    $data = array();
    $column = "";
    $attributes = array();
    $messages = array();
    $attributes['scheme_code'] = 'Scheme';
    $attributes['district'] = 'District';
    $attributes['urban_code'] = 'Rural/ Urban';
    $attributes['block'] = 'Block/Sub Division';
    $attributes['muncid'] = 'Municipality';
    $attributes['gp_ward'] = 'GP/Ward';
    $attributes['fin_year'] = 'Financial Year';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $user_msg = $schemeObj->scheme_name . " Stop Payment Report";
      $title = $user_msg;
      $return_status = 1;
      $return_msg = '';
      $heading_msg = '';
      $external = 0;
      $external_arr = array();
      $external_filter = array();
      if (!empty($gp_ward)) {
        if ($urban_code == 1) {
          $column = "Ward";
          $heading_msg =  $user_msg . ' of the Ward ' . $gp_ward_name;
          $data = $this->getWardWise($scheme_id, $fin_year, $district, $block, $muncid, $gp_ward);
        } else {
          $column = "GP";
          $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
          $data = $this->getGpWise($scheme_id, $fin_year, $district, $block, NULL, $gp_ward);
        }
      } else if (!empty($muncid)) {
        $column = "Ward";
        $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
        $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
        $data = $this->getWardWise($scheme_id, $fin_year, $district, $block, $muncid, NULL);
      } else if (!empty($block)) {
        if ($urban_code == 1) {
          $column = "Municipality";
          $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
          $data = $this->getMuncWise($scheme_id, $fin_year, $district, $block, NULL, NULL);
        } else if ($urban_code == 2) {
          $block_arr = Taluka::where('block_code', '=', $block)->first();
          $column = "GP";
          $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
          $data = $this->getGpWise($scheme_id, $fin_year, $district, $block, NULL, $gp_ward);
        }
      } else {
        if (!empty($district)) {
          if ($urban_code == 1) {
            $column = "Sub Division";
            $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $data = $this->getSubDivWise($scheme_id, $fin_year, $district, NULL, NULL, NULL);
          } else if ($urban_code == 2) {
            $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block";
            $data = $this->getBlockWise($scheme_id, $fin_year, $district, NULL, NULL, NULL);
          } else {
            $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block/Sub Division";
            $data1 = $this->getBlockWise($scheme_id, $fin_year, $district, NULL, NULL, NULL);
            $data2 = $this->getSubDivWise($scheme_id, $fin_year, $district, NULL, NULL, NULL);
            $data = array_merge($data1, $data2);
          }
        } else {
          $column = "District";
          $heading_msg = 'District Wise ' . $user_msg;
          $data = $this->getDistrictWise($scheme_id, $fin_year, NULL, NULL, NULL, NULL);

          $external = 0;
        }
      }
    } else {
      $return_status = 0;
      $return_msg = $validator->errors()->all();
    }
    return response()->json([
      'return_status' => $return_status,
      'return_msg' => $return_msg,
      'row_data' => $data,
      'column' => $column,
      'title' => $title,
      'heading_msg' => $heading_msg.', Report generated on - '.date("l jS \of F Y h:i:s A")
    ]);
  }

  public function getDistrictWise($scheme_id, $fin_year, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL)
  {
    $table_name = $this->getSchemaName($scheme_id);
    $whereCon = "where scheme_id=" . $scheme_id;
    $fin_year_arr = explode('-', $fin_year);
    $monthArr = Config::get('constants.month_list');
    $monthquery = '';
    $query = '';
    $query = "select 
      m.district_name as location_name,  m.district_code as location_id, ";
    foreach ($monthArr as $key=>$value) {
      if ($key >= 4) {
        $monthquery .= "COALESCE(SUM(CASE WHEN to_char(u.created_at, 'YYYY-MM')='".$fin_year_arr[0]."-".$key."' THEN 1 ELSE 0 END),0) as month_".$key.", ";
      }
      else {
        $monthquery .= "COALESCE(SUM(CASE WHEN to_char(u.created_at, 'YYYY-MM')='".$fin_year_arr[1]."-".$key."' THEN 1 ELSE 0 END),0) as month_".$key.", ";
      }
    } 
    $monthquery = rtrim($monthquery, ", ");
    $query .=  $monthquery;
      
    $query .= " from public.update_ben_details u 
      LEFT JOIN " . $table_name . " b on u.original_application_id=b.id 
      left JOIN m_district m on m.district_code=b.created_by_dist_code 
      WHERE u.update_code=2 and u.scheme_id= ".  $scheme_id ." 
      group by m.district_name, m.district_code";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }


  public function excelDuplicateReject(Request $request)
  {
    $district_code = $request->district_code;
    $scheme_id = $request->scheme_id;
    $date = $request->date;
    $result = DB::select(DB::raw("select * from duplicate_approve_reject
			where scheme_id=" . $scheme_id . " and dist_code=" . $district_code . " and created_at>=cast('" . $date . "' as date) and created_at<=now()"));
    $sObj = Scheme::where('id', $scheme_id)->first();
    $filename = $sObj->scheme_name . ' Duplicate Rejected ';
    // $this->generateExcel($result, $filename);
    $ben[] = array('Id', 'Name', 'Block/Municipality', 'Date', 'Approved Id');
    foreach ($result as $arr) {
      $ben[] = array(
        'Id' => trim($arr->id),
        'Name'  => trim($arr->ben_fname) . ' ' . trim($arr->ben_mname) . ' ' . trim($arr->ben_lname),
        'Block/Municipality' => trim($arr->block_ulb_name),
        'Date' => trim(date("d-m-Y", strtotime($arr->created_at))),
        'Approved Id' => trim($arr->original_approve_application_id)
      );
    }
    //print_r($ben);die();
    Excel::create($filename . 'Beneficiary List', function ($excel) use ($ben) {
      $excel->setTitle('List of Beneficiary');
      $excel->sheet('List of Beneficiary', function ($sheet) use ($ben) {
        $sheet->fromArray($ben, null, 'A1', false, false);
      });
    })->download('xlsx');
  }
  public function excelStopPayment(Request $request)
  {
    $district_code = $request->district_code;
    $scheme_id = $request->scheme_id;
    $date = $request->date;
    $result = DB::select(DB::raw("select * from pension.beneficiary where id in(select distinct original_application_id from update_ben_details where update_code=2 and dist_code=" . $district_code . " and scheme_id=" . $scheme_id . " and created_at>=cast('" . $date . "' as date) and created_at<=now())"));
    $sObj = Scheme::where('id', $scheme_id)->first();
    $filename = $sObj->scheme_name . ' Stop Payment ';
    $this->generateExcel($result, $filename);
  }
  // Generate Excel
  public function generateExcel($result, $filename)
  {
    $ben[] = array('Id', 'Name', 'Block/Municipality', 'Date');
    foreach ($result as $arr) {
      $ben[] = array(
        'Id' => trim($arr->id),
        'Name'  => trim($arr->ben_fname) . ' ' . trim($arr->ben_mname) . ' ' . trim($arr->ben_lname),
        'Block/Municipality' => trim($arr->block_ulb_name),
        'Date' => trim(date("d-m-Y", strtotime($arr->created_at)))
      );
    }
    //print_r($ben);die();
    Excel::create($filename . 'Beneficiary List', function ($excel) use ($ben) {
      $excel->setTitle('List of Beneficiary');
      $excel->sheet('List of Beneficiary', function ($sheet) use ($ben) {
        $sheet->fromArray($ben, null, 'A1', false, false);
      });
    })->download('xlsx');
  }
}
