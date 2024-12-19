<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configduty;
use App\District;
use App\UrbanBody;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
use Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use DateTime;
use App\Scheme;
use Config;
use Carbon\Carbon;
use App\DataSourceCommon;
use App\getModelFunc;
use App\DsPhase;
use App\Helpers\AuthChecker;

class DifferentAgeCohortReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    function index(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1 and scheme_id in(2,10,11))"));
        $c_time = Carbon::now();
        $c_date = $c_time->format("Y-m-d");
        $scheme_name = '';
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if ($designation_id_old == 'Approver') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            
            $district_code_arr=Configduty::where('user_id', $user_id)->whereNotNull('district_code')->first();
            $district_code=$district_code_arr->district_code;
            //dd($district_code);
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
            'DiffAgeCohortReport.index',
            [
                'districts' => $districts,
                'district_visible' => $district_visible,
                'district_code_fk' => $district_code_fk,
                'is_urban_visible' => $is_urban_visible,
                'rural_urban_fk' => $rural_urban_fk,
                'block_visible' => $block_visible,
                'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
                'municipality_visible' => $municipality_visible,
                'gp_ward_visible' => $gp_ward_visible,
                'c_date' => $c_date,
                'gpList' => $gpList,
                'muncList' => $muncList,
                'scheme_list' => $scheme_list,
                'scheme_name' => $scheme_name
            ]
        );
    }

    public function getData(Request $request)
    {
        //$ds_phase_list = Config::get('constants.ds_phase.phaselist');
        $scheme_id = $request->scheme_id;
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        $gp_ward = $request->gp_ward;
        $caste = $request->caste_category;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $base_date  = '2020-08-16';
        $c_time = Carbon::now();
        $c_date =$c_time->format('M d Y');
        $heading_msg = '';
        $heading_msg2='';
        $title = "";
        $scheme_name='';
        //$block_condition = "";
        if (!empty($district)) {
            $district_row = District::where('district_code', $district)->first();
        }

        if (!empty($block)) {

            if ($urban_code == 1) {
                $block_ulb = SubDistrict::where('sub_district_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->sub_district_name;
                //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
            } else {
                $block_ulb = Taluka::where('block_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->block_name;
                // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
            }
        } else {
            // $block_condition = "";
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
            'scheme_id' => 'required|integer',
            'district' => 'nullable|integer',
            'urban_code' => 'nullable|integer',
            'block' => 'nullable|integer',
            'muncid' => 'nullable|integer',
            'gp_ward' => 'nullable|integer'
        ];
        $data = array();
        $column = "";
        $attributes = array();
        $messages = array();
        $attributes['scheme_id'] = 'Scheme';
        $attributes['district'] = 'District';
        $attributes['urban_code'] = 'Rural/ Urban';
        $attributes['block'] = 'Block/Sub Division';
        $attributes['muncid'] = 'Municipality';
        $attributes['gp_ward'] = 'GP/Ward';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $schemeObj = Scheme::where('id', $scheme_id)->first();
            $tablename = $schemeObj->short_code . '.beneficiaries';
            if(empty($tablename)){
                $tablename ='pension.beneficiaries';
            }
            $scheme_name= $schemeObj->scheme_name;
            $user_msg = "Report on ".$scheme_name." as on ".$c_date;
            $title = $user_msg;
            //dd($schemeObj);
            
            $data = array();
            $return_status = 1;
            $return_msg = '';
            $heading_msg = '';
            $external = 0;
            $external_arr = array();
            $external_filter = array();
             if (!empty($block)) {
                if ($urban_code == 1) {
                    $column = "Sub Division";
                    $heading_msg = 'Sub Division  ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getSubDivWise($scheme_id, $scheme_name,$tablename,$district,$block);
                } else if ($urban_code == 2) {
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "Block";
                    $heading_msg = 'Block  ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getBlockWise($scheme_id, $scheme_name,$tablename,$district,$block);
                }
            } else {

                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getSubDivWise($scheme_id, $scheme_name,$tablename,$district,NULL);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getBlockWise($scheme_id, $scheme_name,$tablename,$district,NULL);
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getBlockWise($scheme_id, $scheme_name,$tablename,$district,NULL);
                        $data2 = $this->getSubDivWise($scheme_id, $scheme_name,$tablename,$district,NULL);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getDistrictWise($scheme_id, $scheme_name,$tablename);

                    $external = 0;
                }
            }
            $heading_msg2 = 'Number of Beneficiaries under '.$scheme_name;

           
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
            'heading_msg1' => $heading_msg,
            'heading_msg2' => $heading_msg2,
            'c_date' => $c_date,
            'scheme_name' => $scheme_name,
            'scheme_id' => $scheme_id
        ]);
    }

    public function getSchemeWiseSelectAgeGroup($scheme_id = NULL, $tablename = NULL)
    {
        if ($scheme_id == 10) {
            $innerPartQuery = "sum(case when (extract(year from current_date)-extract(year from dob))<60  then 1  else 0 end) as age_below_60,
            sum(case when (extract(year from current_date)-extract(year from dob))>=60 and (extract(year from current_date)-extract(year from dob))<70 then 1  else 0 end) as age_60_70,
            sum(case when (extract(year from current_date)-extract(year from dob))>=70 and (extract(year from current_date)-extract(year from dob))<80 then 1  else 0 end) as age_70_80,
            sum(case when (extract(year from current_date)-extract(year from dob))>=80 and (extract(year from current_date)-extract(year from dob))<90 then 1  else 0 end) as age_80_90,
            sum(case when (extract(year from current_date)-extract(year from dob))>=90 and (extract(year from current_date)-extract(year from dob))<100 then 1  else 0 end) as age_90_100,
            sum(case when (extract(year from current_date)-extract(year from dob))>=100  then 1  else 0 end) as age_above_100
             from " . $tablename . " ";
        }
        else if ($scheme_id == 11) {
            $innerPartQuery = "sum(case when (extract(year from current_date)-extract(year from dob))<20  then 1  else 0 end) as age_below_20,
            sum(case when (extract(year from current_date)-extract(year from dob))>=20 and (extract(year from current_date)-extract(year from dob))<30 then 1  else 0 end) as age_20_30,
            sum(case when (extract(year from current_date)-extract(year from dob))>=30 and (extract(year from current_date)-extract(year from dob))<40 then 1  else 0 end) as age_30_40,
            sum(case when (extract(year from current_date)-extract(year from dob))>=40 and (extract(year from current_date)-extract(year from dob))<50 then 1  else 0 end) as age_40_50,
            sum(case when (extract(year from current_date)-extract(year from dob))>=50 and (extract(year from current_date)-extract(year from dob))<60 then 1  else 0 end) as age_50_60,
            sum(case when (extract(year from current_date)-extract(year from dob))>=60 and (extract(year from current_date)-extract(year from dob))<70 then 1  else 0 end) as age_60_70,
            sum(case when (extract(year from current_date)-extract(year from dob))>=70 and (extract(year from current_date)-extract(year from dob))<80 then 1  else 0 end) as age_70_80,
            sum(case when (extract(year from current_date)-extract(year from dob))>=80 and (extract(year from current_date)-extract(year from dob))<90 then 1  else 0 end) as age_80_90,
            sum(case when (extract(year from current_date)-extract(year from dob))>=90 and (extract(year from current_date)-extract(year from dob))<100 then 1  else 0 end) as age_90_100,
            sum(case when (extract(year from current_date)-extract(year from dob))>=100  then 1  else 0 end) as age_above_100
             from " . $tablename . " ";
        }
        else if ($scheme_id == 2) {
            $innerPartQuery = "sum(case when (extract(year from current_date)-extract(year from dob))<10  then 1  else 0 end) as age_below_10,
            sum(case when (extract(year from current_date)-extract(year from dob))>=10 and (extract(year from current_date)-extract(year from dob))<20 then 1  else 0 end) as age_10_20,
            sum(case when (extract(year from current_date)-extract(year from dob))>=20 and (extract(year from current_date)-extract(year from dob))<30 then 1  else 0 end) as age_20_30,
            sum(case when (extract(year from current_date)-extract(year from dob))>=30 and (extract(year from current_date)-extract(year from dob))<40 then 1  else 0 end) as age_30_40,
            sum(case when (extract(year from current_date)-extract(year from dob))>=40 and (extract(year from current_date)-extract(year from dob))<50 then 1  else 0 end) as age_40_50,
            sum(case when (extract(year from current_date)-extract(year from dob))>=50 and (extract(year from current_date)-extract(year from dob))<60 then 1  else 0 end) as age_50_60,
            sum(case when (extract(year from current_date)-extract(year from dob))>=60 and (extract(year from current_date)-extract(year from dob))<70 then 1  else 0 end) as age_60_70,
            sum(case when (extract(year from current_date)-extract(year from dob))>=70 and (extract(year from current_date)-extract(year from dob))<80 then 1  else 0 end) as age_70_80,
            sum(case when (extract(year from current_date)-extract(year from dob))>=80 and (extract(year from current_date)-extract(year from dob))<90 then 1  else 0 end) as age_80_90,
            sum(case when (extract(year from current_date)-extract(year from dob))>=90 and (extract(year from current_date)-extract(year from dob))<100 then 1  else 0 end) as age_90_100,
            sum(case when (extract(year from current_date)-extract(year from dob))>=100  then 1  else 0 end) as age_above_100
             from " . $tablename . " ";
        }
        return $innerPartQuery;
    }

    public function getDistrictWise($scheme_id = NULL,$scheme_name = NULL, $tablename = NULL)
    {
        //$dateFromat = 'DD/MM/YYYY';
        $dateFromat = 'YYYY-MM-DD';
        $whereCon = "where next_level_role_id=0 and scheme_id=".$scheme_id;
        
        $innerPartQuery = $this->getSchemeWiseSelectAgeGroup($scheme_id, $tablename);
        $query = "select main.location_id,main.location_name,pension.*
       
        from
        (
        select district_code as location_id,district_name as location_name
        from public.m_district  
        ) as main LEFT JOIN
        (
            select created_by_dist_code, 
            " . $innerPartQuery . "
            " . $whereCon . "  group by created_by_dist_code
        ) as pension ON main.location_id=pension.created_by_dist_code
        order by main.location_name";

        // echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }

    public function getBlockWise($scheme_id = NULL,$scheme_name = NULL, $tablename = NULL,$district_code,$block_code=NULL)
    {
        $whereCon = "where next_level_role_id=0 and scheme_id=".$scheme_id." and created_by_dist_code=".$district_code;
        $whereMain = "where  district_code=" . $district_code;
        if(!empty($block_code)){
            $whereMain .= " and block_code=".$block_code;
            $whereCon .= " and created_by_local_body_code=".$block_code;
        }

        $innerPartQuery = $this->getSchemeWiseSelectAgeGroup($scheme_id, $tablename);
        $query = "select main.location_id,main.location_name,pension.*
      
       from
       (
        select block_code as location_id,'Block-'||block_name as location_name
        from public.m_block  " . $whereMain . "
       ) as main LEFT JOIN
       (
           select created_by_local_body_code, 
            " . $innerPartQuery . " 
            " . $whereCon . "  group by created_by_local_body_code
       ) as pension ON main.location_id=pension.created_by_local_body_code
       order by main.location_name";

       // echo $query;die;
       $result = DB::connection('pgsql_mis')->select($query);
       return $result;
    }

    public function getSubDivWise($scheme_id = NULL,$scheme_name = NULL, $tablename = NULL,$district_code,$sdo_code=NULL)
    {
        $whereCon = "where next_level_role_id=0 and scheme_id=".$scheme_id." and created_by_dist_code=".$district_code;
        $whereMain = "where  district_code=" . $district_code;
        if(!empty($sdo_code)){
            $whereMain .= " and sub_district_code=".$sdo_code;
            $whereCon .= " and created_by_local_body_code=".$sdo_code;
        }

        $innerPartQuery = $this->getSchemeWiseSelectAgeGroup($scheme_id, $tablename);
        $query = "select main.location_id,main.location_name,pension.*
      
       from
       (
        select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
        from public.m_sub_district  " . $whereMain . " 
       ) as main LEFT JOIN
       (
           select created_by_local_body_code, 
            " . $innerPartQuery . " 
            " . $whereCon . "  group by created_by_local_body_code
       ) as pension ON main.location_id=pension.created_by_local_body_code
       order by main.location_name";

       // echo $query;die;
       $result = DB::connection('pgsql_mis')->select($query);
       return $result;
    }
}
