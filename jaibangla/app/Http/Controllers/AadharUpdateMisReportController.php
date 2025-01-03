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
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\AuthChecker;

class AadharUpdateMisReportController extends Controller
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
            $table_name =  strtolower($schema_name) . '.beneficiaries';
        } else {
            $table_name =  'pension.beneficiaries';
        }
        return $table_name;
    }

    public function countListIndex(Request $request)
    {
        // echo 1;die;
        $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        // echo '<pre>'; print_r($roleArray);die();
        $designation_id = Auth::user()->designation_id;
        $user_id = AuthChecker::getUserId();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") and is_active = 1 order by rank"));
        // echo '<pre>';print_r($schemes);die();
        if ($designation_id == 'Admin') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if ($designation_id == 'Approver') {
            // echo 1;die();
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                // echo $designation_id;die();
                // if ($roleObj['scheme_id']) {
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
                // }
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
            'aadhar-update/count_list_aadhar_update_approver',
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
                // 'ds_phase_list' => $ds_phase_list
            ]
        );
    }

    public function aadharUpdateCountListApprover(Request $request)
    {
        // echo 1;die;
        //$ds_phase_list = Config::get('constants.ds_phase.phaselist');
        $scheme_id = $request->scheme_id;
        $table_name = $this->getSchemaName($scheme_id);
        // echo $table_name;die;
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        // echo $muncid;die;
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
            if (!empty($gp_ward)) {
                if ($urban_code == 1) {
                    $column = "Ward";
                    $heading_msg =  $user_msg . ' of the Ward ' . $gp_ward_name;
                    $data = $this->getWardWise($scheme_id, $district, $block, $table_name, $muncid, $gp_ward);
                } else {
                    $column = "GP";
                    $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
                    $data = $this->getGpWise($scheme_id, $district, $block, $table_name, NULL, $gp_ward);
                }
            } 
            // else if (!empty($muncid)) {
            //     $column = "Ward";
            //     $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
            //     $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
            //     $data = $this->getWardWise($scheme_id, $district, $block, $table_name, $muncid, NULL);
            // } 
            else if (!empty($block)) {
                if ($urban_code == 1) {
                    $column = "Municipality";
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getMuncWise($scheme_id, $district, $block, $table_name, $muncid, NULL);
                } else if ($urban_code == 2) {
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getGpWise($scheme_id, $district, $block, $table_name, NULL, $gp_ward);
                }
            } else {
                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getSubDivWise($scheme_id, $district, $table_name,NULL, NULL, NULL);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getBlockWise($scheme_id, $district,NULL, NULL, $table_name);
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getBlockWise($scheme_id, $district,NULL, NULL, $table_name);
                        $data2 = $this->getSubDivWise($scheme_id, $district, $table_name,NULL, NULL, NULL);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getDistrictWise($scheme_id, NULL, NULL, NULL, NULL);

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
            'scheme_name' => $scheme_name
        ]);
    }

    public function getBlockWise($scheme_id = NULL, $district_code, $scheme_name = NULL, $block_code=NULL, $table_name)
    {
        $whereCon = "where next_level_role_id=0 and scheme_id=".$scheme_id." and created_by_dist_code=".$district_code;
        $whereMain = "where  district_code=" . $district_code;
        if(!empty($block_code)){
            $whereMain .= " and block_code=".$block_code;
            $whereCon .= " and created_by_local_body_code=".$block_code;
        }

        $query = "select main.location_id,main.location_name,pension.*     
        from
        (
        select block_code as location_id,'Block-'||block_name as location_name
        from public.m_block ".$whereMain."
        ) as main LEFT JOIN
        (
            select 
            count(b.id) total_approved,
            sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
            sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
            sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
            sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
            sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved,
            created_by_local_body_code 
            from ".$table_name." b 
            JOIN m_scheme s ON s.id=b.scheme_id
            ".$whereCon."
            group by created_by_local_body_code
        ) as pension ON main.location_id=pension.created_by_local_body_code
        order by main.location_name";
        // echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }

    public function getSubDivWise($scheme_id = NULL, $district_code, $table_name, $scheme_name = NULL, $sdo_code=NULL, $schema_name = NULL)
    {
        $whereCon = "where next_level_role_id=0 and scheme_id=".$scheme_id." and created_by_dist_code=".$district_code;
        $whereMain = "where district_code=" . $district_code;
        if(!empty($sdo_code)){
            $whereMain .= " and sub_district_code=".$sdo_code;
            $whereCon .= " and created_by_local_body_code=".$sdo_code;
        }

        $query = "select main.location_id,main.location_name,pension.*     
        from
        (
        select sub_district_code as location_id,'Sub District-'||sub_district_name as location_name
        from public.m_sub_district ".$whereMain."
        ) as main LEFT JOIN
        (
            select 
            count(b.id) total_approved,
            sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
            sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
            sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
            sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
            sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved,
            created_by_local_body_code 
            from ".$table_name." b 
            JOIN m_scheme s ON s.id=b.scheme_id
            ".$whereCon."
            group by created_by_local_body_code
        ) as pension ON main.location_id=pension.created_by_local_body_code
        order by main.location_name";
        // echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }

    public function getGpWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $table_name, $block_ulb_code = NULL, $gp_ward_code)
    {
        $whereCon = "where district_code=" . $district_code ." and block_code=" . $ulb_code;
        $whereMain = "where next_level_role_id = 0 and scheme_id= ".$scheme_id." and created_by_dist_code= ".$district_code;
        
        if (!empty($gp_ward_code)) {
            $whereCon .= " and gram_panchyat_code =" . $gp_ward_code;
            $whereMain .= " and gp_ward_code =" . $gp_ward_code;
        }

        $query = "select main.location_id,main.location_name,pension.*     
        from
        (
        select gram_panchyat_code as location_id,'GP-'||gram_panchyat_name as location_name
        from public.m_gp ".$whereCon."
        ) as main LEFT JOIN
        (
            select 
            count(b.id) total_approved,
            sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
            sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
            sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
            sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
            sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved,
            gp_ward_code 
            from ".$table_name." b 
            JOIN m_scheme s ON s.id=b.scheme_id
            ".$whereMain."
            group by gp_ward_code
        ) as pension ON main.location_id=pension.gp_ward_code
        order by main.location_name";
        // echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }

    public function getMuncWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $table_name, $muncid, $gp_ward_code = NULL)
    {
        $whereCon = "where next_level_role_id=0 and scheme_id= ".$scheme_id." and created_by_dist_code=" . $district_code ." and created_by_local_body_code=". $ulb_code;
        $whereMain = "where  district_code=" . $district_code ." and sub_district_code=" . $ulb_code;

        if (!empty($muncid)) {
            // echo 1;die;
            $whereMain .= " AND urban_body_code =" .$muncid;
            $whereCon .= " AND block_ulb_code =" .$ulb_code;
        }

        $query = "select main.location_id,main.location_name,pension.*     
        from
        (
        select urban_body_code as location_id,'Municipality-'||urban_body_name as location_name
        from public.m_urban_body ".$whereMain."
        ) as main LEFT JOIN
        (
            select 
            count(b.id) total_approved,
            sum(case when b.aadhar_no is null then 1 else 0 end) blank_aadhar_count,
            sum(case when length(b.aadhar_no)<12 then 1 else 0 end) aadhar_no_less_than_12,
            sum(case when b.aadhar_edit_role_id = 1 then 1 else 0 end) edited,
            sum(case when b.aadhar_edit_role_id = 2 then 1 else 0 end) verified,
            sum(case when b.aadhar_edit_role_id = 3 then 1 else 0 end) approved,
            block_ulb_code 
            from ".$table_name." b 
            JOIN m_scheme s ON s.id=b.scheme_id
            ".$whereCon."
            group by block_ulb_code
        ) as pension ON main.location_id=pension.block_ulb_code
        order by main.location_name";
        // echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
}
