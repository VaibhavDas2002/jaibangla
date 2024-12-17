<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BSKdrilldownController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        set_time_limit(200);
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
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = Auth::user()->id;
        // echo $user_id;die();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") AND id = 2 order by rank"));
        // print_r($schemes);die();
        if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
            // echo $designation_id_old;die();
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                // echo $designation_id_old;die();
                if ($roleObj['scheme_id'] == 2) {
                    // echo $designation_id_old;die();
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
        return view(
            'bsk_drilldown_report/index',
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

    public function drillDownReport(Request $request)
    {
        $scheme_id = $request->scheme_id;
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        $gp_ward = $request->gp_ward;
        $search_report = $request->search_for;
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
            'gp_ward' => 'nullable|integer'
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
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $user_msg = $schemeObj->scheme_name . " Report";
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
                    $data = $this->getWardWise($scheme_id, $district, $block, $muncid, $gp_ward);
                } else {
                    $column = "GP";
                    $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
                    $data = $this->getGpWise($scheme_id, $district, $block, NULL, $gp_ward);
                }
            } else if (!empty($muncid)) {
                $column = "Ward";
                $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
                $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
                $data = $this->getWardWise($scheme_id, $district, $block, $muncid, NULL);
            } else if (!empty($block)) {
                if ($urban_code == 1) {
                    $column = "Municipality";
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getMuncWise($scheme_id, $district, $block, NULL, NULL);
                } else if ($urban_code == 2) {
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getGpWise($scheme_id, $district, $block, NULL, $gp_ward);
                }
            } else {
                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL);
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL);
                        $data2 = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getDistrictWise($scheme_id, NULL, NULL, NULL, NULL);

                    $external = 0;
                }
            }
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }
        // print_r($data);die();
        return response()->json([
            'return_status' => $return_status,
            'return_msg' => $return_msg,
            'row_data' => $data,
            'column' => $column,
            'title' => $title,
            'heading_msg' => $heading_msg
        ]);
    }

    public function getDistrictWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL)
    {
        // echo 1;die;
        $table_name = $this->getSchemaName($scheme_id);
        
        $whereCon = "where scheme_id=" . $scheme_id;
        $query = "select main.location_id,main.location_name,
        COALESCE(sum(dup.application_submitted),0) as application_submitted,
        COALESCE(sum(dup.application_approved),0) as application_approved,
        COALESCE(sum(dup.application_verified),0) as application_verified,
        COALESCE(sum(dup.application_rejected),0) as application_rejected,
		COALESCE(sum(dup.application_rejected_by_bsk),0) as application_rejected_by_bsk
        from
        (
            select district_code as location_id,district_name as location_name
            from public.m_district  
        ) as main LEFT JOIN
        (
            select
            count(1) filter(where next_level_role_id is null ) as application_submitted,
            count(1) filter(where next_level_role_id = 0 ) as application_approved,
            count(1) filter(where is_verified=1 and is_approved=0 and is_rejected=0 ) as application_verified,
            count(1) filter(where is_rejected=1 ) as application_rejected,
			0 as application_rejected_by_bsk,
            created_by_dist_code 
            from pension.beneficiaries ".$whereCon." and is_bsk = true 
            group by created_by_dist_code
			
			UNION ALL
			select
            count(1) filter(where next_level_role_id is null ) as application_submitted,
            0 as application_approved,
            0 as application_verified,
            0 as application_rejected,
			count(1) filter(where next_level_role_id = -80 ) as application_rejected_by_bsk,
            created_by_dist_code 
            from manabik.beneficiary_bsk ".$whereCon." and is_bsk = true 
            group by created_by_dist_code
        ) as dup ON main.location_id=dup.created_by_dist_code GROUP BY main.location_id,main.location_name
        order by main.location_name";
        // echo $query;die; 
        $result = DB::connection('pgsql_mis')->select($query);
        // print_r($result);die();
        return $result;
    }
    public function getBlockWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL)
    {
        $table_name = $this->getSchemaName($scheme_id);
        
        $whereCon = "where scheme_id=" . $scheme_id;
        $whereCon .= " and created_by_dist_code=" . $district_code;
        $whereMasterCon = "where district_code=" . $district_code;
        $query = "select main.location_id,main.location_name,
        COALESCE(sum(dup.application_submitted),0) as application_submitted,
        COALESCE(sum(dup.application_approved),0) as application_approved,
        COALESCE(sum(dup.application_verified),0) as application_verified,
        COALESCE(sum(dup.application_rejected),0) as application_rejected,
		COALESCE(sum(dup.application_rejected_by_bsk),0) as application_rejected_by_bsk
        from
        (
        select block_code as location_id,block_name as location_name
        from public.m_block ".$whereMasterCon."
        ) as main LEFT JOIN
        (
            select
            count(1) filter(where next_level_role_id is null ) as application_submitted,
            count(1) filter(where next_level_role_id = 0 ) as application_approved,
            count(1) filter(where is_verified=1 and is_approved=0 and is_rejected=0 ) as application_verified,
            count(1) filter(where is_rejected=1 ) as application_rejected,
			0 as application_rejected_by_bsk,
            created_by_local_body_code 
            from pension.beneficiaries ".$whereCon." and is_bsk = true 
            group by created_by_local_body_code
			
			UNION ALL
			select
            count(1) filter(where next_level_role_id is null ) as application_submitted,
            0 as application_approved,
            0 as application_verified,
            0 as application_rejected,
			count(1) filter(where next_level_role_id = -80 ) as application_rejected_by_bsk,
            created_by_local_body_code 
            from manabik.beneficiary_bsk ".$whereCon." and is_bsk = true 
            group by created_by_local_body_code
        ) as dup ON main.location_id=dup.created_by_local_body_code GROUP BY main.location_id,main.location_name
        order by main.location_name";
        // echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
    public function getSubDivWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL)
    {
        $table_name = $this->getSchemaName($scheme_id);
        
        $whereCon = "where scheme_id=" . $scheme_id;
        $whereCon .= " and created_by_dist_code=" . $district_code;
        $whereMasterCon = "where district_code=" . $district_code;
        $query = "select main.location_id,main.location_name,
        COALESCE(sum(dup.application_submitted),0) as application_submitted,
        COALESCE(sum(dup.application_approved),0) as application_approved,
        COALESCE(sum(dup.application_verified),0) as application_verified,
        COALESCE(sum(dup.application_rejected),0) as application_rejected,
		COALESCE(sum(dup.application_rejected_by_bsk),0) as application_rejected_by_bsk
        from
        (
        select sub_district_code as location_id,sub_district_name as location_name
        from public.m_sub_district " . $whereMasterCon . "
        ) as main LEFT JOIN
        (
            select
            count(1) filter(where next_level_role_id is null ) as application_submitted,
            count(1) filter(where next_level_role_id = 0 ) as application_approved,
            count(1) filter(where is_verified=1 and is_approved=0 and is_rejected=0 ) as application_verified,
            count(1) filter(where is_rejected=1 ) as application_rejected,
			0 as application_rejected_by_bsk,
            created_by_local_body_code 
            from pension.beneficiaries ".$whereCon." and is_bsk = true 
            group by created_by_local_body_code
			
			UNION ALL
			select
            count(1) filter(where next_level_role_id is null ) as application_submitted,
            0 as application_approved,
            0 as application_verified,
            0 as application_rejected,
			count(1) filter(where next_level_role_id = -80 ) as application_rejected_by_bsk,
            created_by_local_body_code 
            from manabik.beneficiary_bsk ".$whereCon." and is_bsk = true 
            group by created_by_local_body_code
        ) as dup ON main.location_id=dup.created_by_local_body_code GROUP BY main.location_id,main.location_name
        order by main.location_name";
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
    public function getWardWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL)
    {
        $table_name = $this->getSchemaName($scheme_id);
        
        $whereCon = "where created_by_dist_code=" . $district_code;
        $whereCon .= " and created_by_local_body_code=" . $ulb_code;
        $whereCon .= " and block_ulb_code=" . $block_ulb_code;
        $whereMain = "where  urban_body_code=" . $block_ulb_code;
        if (!empty($gp_ward_code)) {
            $whereCon .= " and gp_ward_code=" . $gp_ward_code;
            $whereMain .= " and urban_body_ward_code=" . $gp_ward_code;
        }
        $query = "select main.location_id,main.location_name,
        COALESCE(sum(dup.application_submitted),0) as application_submitted,
        COALESCE(sum(dup.application_approved),0) as application_approved,
        COALESCE(sum(dup.application_verified),0) as application_verified,
        COALESCE(sum(dup.application_rejected),0) as application_rejected,
		COALESCE(sum(dup.application_rejected_by_bsk),0) as application_rejected_by_bsk
        from
        (
        select urban_body_ward_code as location_id,urban_body_ward_name as location_name
        from public.m_urban_body_ward " . $whereMain . "
        ) as main LEFT JOIN
        (
            select
            count(1) filter(where next_level_role_id is null ) as application_submitted,
            count(1) filter(where next_level_role_id = 0 ) as application_approved,
            count(1) filter(where is_verified=1 and is_approved=0 and is_rejected=0 ) as application_verified,
            count(1) filter(where is_rejected=1 ) as application_rejected,
			0 as application_rejected_by_bsk,
            gp_ward_code 
            from pension.beneficiaries ".$whereCon." and is_bsk = true 
            group by gp_ward_code
			
			UNION ALL
			select
            count(1) filter(where next_level_role_id is null ) as application_submitted,
            0 as application_approved,
            0 as application_verified,
            0 as application_rejected,
			count(1) filter(where next_level_role_id = -80 ) as application_rejected_by_bsk,
            gp_ward_code 
            from manabik.beneficiary_bsk ".$whereCon." and is_bsk = true 
            group by gp_ward_code
        ) as dup ON main.location_id=dup.gp_ward_code GROUP BY main.location_id,main.location_name
        order by main.location_name";
        echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
    public function getGpWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL)
    {
        $table_name = $this->getSchemaName($scheme_id);
        
        $whereCon = "where created_by_dist_code=" . $district_code;
        $whereCon .= " and created_by_local_body_code=" . $ulb_code;
        $whereMain = "where  district_code=" . $district_code;
        $whereMain .= " and block_code=" . $ulb_code;

        if (!empty($gp_ward_code)) {
            $whereCon .= " and gp_ward_code=" . $gp_ward_code;
            $whereMain .= " and gram_panchyat_code=" . $gp_ward_code;
        }
        $query = "select main.location_id,main.location_name,
        COALESCE(sum(dup.application_submitted),0) as application_submitted,
        COALESCE(sum(dup.application_approved),0) as application_approved,
        COALESCE(sum(dup.application_verified),0) as application_verified,
        COALESCE(sum(dup.application_rejected),0) as application_rejected,
		COALESCE(sum(dup.application_rejected_by_bsk),0) as application_rejected_by_bsk
        from
        (
        select gram_panchyat_code as location_id,gram_panchyat_name as location_name
        from public.m_gp  " . $whereMain . "
        ) as main LEFT JOIN
        (
            select
            count(1) filter(where next_level_role_id is null ) as application_submitted,
            count(1) filter(where next_level_role_id = 0 ) as application_approved,
            count(1) filter(where is_verified=1 and is_approved=0 and is_rejected=0 ) as application_verified,
            count(1) filter(where is_rejected=1 ) as application_rejected,
			0 as application_rejected_by_bsk,
            gp_ward_code 
            from pension.beneficiaries ".$whereCon." and is_bsk = true 
            group by gp_ward_code
			
			UNION ALL
			select
            count(1) filter(where next_level_role_id is null ) as application_submitted,
            0 as application_approved,
            0 as application_verified,
            0 as application_rejected,
			count(1) filter(where next_level_role_id = -80 ) as application_rejected_by_bsk,
            gp_ward_code 
            from manabik.beneficiary_bsk ".$whereCon." and is_bsk = true 
            group by gp_ward_code
        ) as dup ON main.location_id=dup.gp_ward_code GROUP BY main.location_id,main.location_name
        order by main.location_name";
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
    public function getMuncWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL)
    {
        $table_name = $this->getSchemaName($scheme_id);
        
        $whereCon = "where created_by_dist_code=" . $district_code;
        $whereCon .= " and created_by_local_body_code=" . $ulb_code;
        $whereMain = "where  district_code=" . $district_code;
        $whereMain .= " and sub_district_code=" . $ulb_code;
        $query = "select main.location_id,main.location_name,
        COALESCE(sum(dup.application_submitted),0) as application_submitted,
        COALESCE(sum(dup.application_approved),0) as application_approved,
        COALESCE(sum(dup.application_verified),0) as application_verified,
        COALESCE(sum(dup.application_rejected),0) as application_rejected,
		COALESCE(sum(dup.application_rejected_by_bsk),0) as application_rejected_by_bsk
        from
        (
        select urban_body_code as location_id,urban_body_name as location_name
        from public.m_urban_body  " . $whereMain . "
        ) as main LEFT JOIN
        (
            select
            count(1) filter(where next_level_role_id is null ) as application_submitted,
            count(1) filter(where next_level_role_id = 0 ) as application_approved,
            count(1) filter(where is_verified=1 and is_approved=0 and is_rejected=0 ) as application_verified,
            count(1) filter(where is_rejected=1 ) as application_rejected,
			0 as application_rejected_by_bsk,
            block_ulb_code 
            from pension.beneficiaries ".$whereCon." and is_bsk = true 
            group by block_ulb_code
			
			UNION ALL
			select
            count(1) filter(where next_level_role_id is null ) as application_submitted,
            0 as application_approved,
            0 as application_verified,
            0 as application_rejected,
			count(1) filter(where next_level_role_id = -80 ) as application_rejected_by_bsk,
            block_ulb_code 
            from manabik.beneficiary_bsk ".$whereCon." and is_bsk = true 
            group by block_ulb_code
        ) as dup ON main.location_id=dup.block_ulb_code GROUP BY main.location_id,main.location_name
        order by main.location_name";
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
}
