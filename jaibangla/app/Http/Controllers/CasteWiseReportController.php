<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\District;
use App\Scheme;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;
use App\DataSourceCommon;
use App\getModelFunc;
use Illuminate\Support\Facades\Crypt;
use App\RejectRevertReason;
use App\AadharDuplicateTrail;
use App\SubDistrict;
use App\Taluka;
use App\DocumentType;
use Illuminate\Support\Facades\Storage;
use App\SchemeDocMap;
use File;
use App\UpdateBenDetails;
use App\BankDetails;
use App\Configduty;
use App\UrbanBody;
use App\Ward;
use App\GP;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Helpers\AuthChecker;

class CasteWiseReportController extends Controller
{
    public $source_type;
    public $ben_status;
    public function __construct()
    {
        // $this->scheme_id = 20;
        $this->source_type = 'ss_nfsa';
        $this->ben_status = -97;
        //return redirect("/")->with('error', '');
    }
    public function CasteReport(Request $request){
        $this->middleware('auth');
        $base_date  = '2020-01-01';
        date_default_timezone_set('Asia/Kolkata');
        $c_time = Carbon::now();
        $c_date = $c_time->format("Y-m-d");
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        // $designation_id_old = Auth::user()->designation_id_old;
        $userId = AuthChecker::getUserId();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        if (AuthChecker::AdminChecker() || AuthChecker::HODChecker() || AuthChecker::HODChecker() ||  AuthChecker::DashboardChecker() || AuthChecker::MisStateChecker() || AuthChecker::DDOChecker()) {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                if (in_array($roleObj['scheme_id'],array(3,2,10,11,8,9,17,19,1))) {
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
        $gp_ward_visible = 0;
        $municipality_visible = 0;
        $districts = District::get();
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 and id in(2,10,11) order by scheme_name"));
        //dd($scheme_list);
        return view(
            'caste-wise-report.index',
            [
                'districts' => $districts,
                'scheme_list' => $scheme_list,
                'district_visible' => $district_visible,
                'district_code_fk' => $district_code_fk,
                'is_urban_visible' => $is_urban_visible,
                'rural_urban_fk' => $rural_urban_fk,
                'block_visible' => $block_visible,
                'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
                'municipality_visible' => $municipality_visible,
                'gp_ward_visible' => $gp_ward_visible,
                'base_date' => $base_date,
                'c_date' => $c_date,
                'gpList' => $gpList,
                'muncList' => $muncList
            ]
        );
    }
    public function CasteWiseGetData(Request $request){
        //  dd($request->all());
        $scheme_id = $request->scheme_id;
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        $gp_ward = $request->gp_ward;
        $process_type = $request->process_type;
        // dd($gp_ward);
        $caste = $request->caste_category;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $base_date  = '2020-08-16';
        $c_time = Carbon::now();
        $c_date = $c_time->format("Y-m-d");
        $heading_msg = '';
        $title = "";
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
        $attributes['from_date'] = 'From Date';
        $attributes['to_date'] = 'To Date';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $scheme_row=Scheme::where('id',$scheme_id)->first();
            $user_msg = "Caste wise Beneficiary Report for the Scheme ". $scheme_row->scheme_name;
            $title = $user_msg;
            //dd($title);

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
                    $data = $this->getWardWise($district, $block, $muncid, $gp_ward, $from_date, $to_date, $caste,$process_type);
                } else {
                    $column = "GP";
                    $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
                    $data = $this->getGpWise($district, $block, NULL, $gp_ward, $from_date, $to_date, $caste,$process_type);
                }
            } else if (!empty($muncid)) {
                $column = "Ward";
                $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
                $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
                $data = $this->getWardWise($district, $block, $muncid, NULL, $from_date, $to_date, $caste,$process_type);
            } else if (!empty($block)) {
                if ($urban_code == 1) {
                    $column = "Municipality";
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getMuncWise($district, $block, NULL, NULL, $from_date, $to_date, $caste,$process_type);
                } else if ($urban_code == 2) {
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getGpWise($district, $block, NULL, $gp_ward, $from_date, $to_date, $caste,$process_type);
                    $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                    $column = "Block";
                    $data = $this->getBlockWise($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste,$process_type);
                }
            } else {

                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getSubDivWise($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste,$process_type);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getBlockWise($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste,$process_type);
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getBlockWise($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste,$process_type);
                        $data2 = $this->getSubDivWise($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste,$process_type);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getDistrictWise( $scheme_id,NULL, NULL, NULL, NULL, $from_date, $to_date,$process_type);

                    $external = 0;
                }
            }
         
            if (!empty($from_date)) {
                $form_date_formatted = Carbon::parse($from_date)->format('d-m-Y');
                $heading_msg = $heading_msg . " from " . $form_date_formatted;
            }
            if (!empty($to_date)) {
                $to_date_formatted = Carbon::parse($to_date)->format('d-m-Y');
                $heading_msg = $heading_msg . " to  " . $to_date_formatted;
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
            'heading_msg' => $heading_msg
        ]);
    }
    public function getDistrictWise($scheme_id,$district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL,$process_type)
    {
        $table_name = $this->getSchemaName($scheme_id);
        //$dateFromat = 'DD/MM/YYYY';
        $dateFromat = 'YYYY/MM/DD';
        $whereCon = "where 1=1";
        $process_query = "and scheme_id = $scheme_id";
        
        if($process_type == 1){
            $process_query = "where next_level_role_id is null ";
        }else if($process_type == 2){
            $process_query = "where next_level_role_id >0 ";
        }else if($process_type == 3){
            $process_query = "where next_level_role_id =0 ";
        }else{
            $process_query = "where (next_level_role_id is null or next_level_role_id>0 or next_level_role_id = 0)";
        }
        $query = "select A.location_id,A.location_name,
        COALESCE(C.total_st,0) as total_st, 
        COALESCE(C.total_sc,0) as total_sc, 
        COALESCE(C.total_general,0) as total_general,
        COALESCE(C.total_others,0) as total_others,
        COALESCE(C.total_no_defined,0) as total_no_defined
        from(
        select district_code as location_id,district_name as location_name
         from public.m_district ) as A  
        LEFT JOIN
        (select
            count(1) filter(WHERE trim(caste) = 'ST') as total_st,
            count(1) filter(WHERE trim(caste) = 'SC') as total_sc,
            count(1) filter(WHERE trim(caste) = 'General') as total_general,
            count(1) filter(WHERE trim(caste) = 'OTHERS') as total_others,
            count(1) filter(WHERE (trim(caste) = '1' OR trim(caste) = '2' OR trim(caste) = '3' OR trim(caste) = '4' OR trim(caste) = '5' or caste is NULL or trim(caste) = '')) as total_no_defined,
            created_by_dist_code
            from $table_name ".$process_query."  group by created_by_dist_code) as C ON A.location_id=C.created_by_dist_code;";

        //  echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
    public function getSubDivWise($scheme_id,$district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL,$process_type)
    {
        //$whereCon = "where A.dist_code=" . $district_code;
        $table_name = $this->getSchemaName($scheme_id);
        $whereMain = "where  district_code=" . $district_code;
         $process_query = "and scheme_id = $scheme_id";

        if($process_type == 1){
            $process_query = "where next_level_role_id is null ";
        }else if($process_type == 2){
            $process_query = "where next_level_role_id >0 ";
        }else if($process_type == 3){
            $process_query = "where next_level_role_id =0 ";
        }else{
            $process_query = "where (next_level_role_id is null or next_level_role_id>0 or next_level_role_id = 0)";
        }
        $query = "select A.location_id,A.location_name, 
         COALESCE(C.total_st,0) as total_st,
         COALESCE(C.total_sc,0) as total_sc,
         COALESCE(C.total_general,0) as total_general,
		 COALESCE(C.total_others,0) as total_others,
        COALESCE(C.total_no_defined,0) as total_no_defined
        from(
            select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
            from public.m_sub_district  " . $whereMain . " 
         )
         as A  
        LEFT JOIN
        (select 
                    count(1) filter(WHERE caste = 'ST') as total_st,
					count(1) filter(WHERE caste = 'SC') as total_sc,
					count(1) filter(WHERE caste = 'General') as total_general,
					count(1) filter(WHERE caste = 'OTHERS') as total_others,
					count(1) filter(WHERE (trim(caste) = '1' OR trim(caste) = '2' OR trim(caste) = '3' OR trim(caste) = '4' OR trim(caste) = '5' or caste is NULL or trim(caste) = '')) as total_no_defined,
                    created_by_local_body_code
                    from $table_name ".$process_query."  AND created_by_dist_code= " . $district_code . " 
         group by created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";
         $result = DB::connection('pgsql_mis')->select($query);
         return $result;
    }
    public function getBlockWise($scheme_id,$district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL,$process_type)
    {
        //  dd('ok2');
         $process_query = "and scheme_id = $scheme_id";
        $whereMain = "where  district_code=" . $district_code;
        $table_name = $this->getSchemaName($scheme_id);
        if($process_type == 1){
            $process_query = "where next_level_role_id is null ";
        }else if($process_type == 2){
            $process_query = "where next_level_role_id >0 ";
        }else if($process_type == 3){
            $process_query = "where next_level_role_id =0 ";
        }else{
            $process_query = "where (next_level_role_id is null or next_level_role_id>0 or next_level_role_id = 0)";
        }
        $query = "select A.location_id,A.location_name,
        COALESCE(C.total_st,0) as total_st, 
        COALESCE(C.total_sc,0) as total_sc, 
        COALESCE(C.total_general,0) as total_general,
        COALESCE(C.total_others,0) as total_others,
        COALESCE(C.total_no_defined,0) as total_no_defined
        from(
            select block_code as location_id,'Block-'||block_name as location_name
           from public.m_block  " . $whereMain . " 
         )
         as A  
        LEFT JOIN
        (select   
                    count(1) filter(WHERE caste = 'ST') as total_st,
					count(1) filter(WHERE caste = 'SC') as total_sc,
					count(1) filter(WHERE caste = 'General') as total_general,
					count(1) filter(WHERE caste = 'OTHERS') as total_others,
					count(1) filter(WHERE (trim(caste) = '1' OR trim(caste) = '2' OR trim(caste) = '3' OR trim(caste) = '4' OR trim(caste) = '5' or caste is NULL or trim(caste) = '')) as total_no_defined,
                    created_by_local_body_code
                    from $table_name ".$process_query."  and  created_by_dist_code= " . $district_code . "   
         group by created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";
         $result = DB::connection('pgsql_mis')->select($query);
         return $result;
    }

    // Approver End
    private function getSchemaName($scheme_id)
    {
        if (!is_null($scheme_id)) {
            $sObj = Scheme::select('id', 'short_code')
                ->where('id', '=', $scheme_id)
                ->first();
            //$parameter['scheme_id'] = $scheme_id;
            $schema_name = $sObj->short_code;
            //dd($schema_name);
            if (empty($schema_name)) {
                $schema_name = 'pension';
            }
            $table_name = strtolower($schema_name) . '.beneficiaries';
        } else {
            $table_name = 'pension.beneficiaries';
        }
        return $table_name;
    }

    public function index(Request $request){
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        // echo '<pre>'; print_r($roleArray);die();
        $user_id = AuthChecker::getUserId();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") and id in(2,10,11) order by rank"));
        // echo '<pre>';print_r($schemes);die();
        if (AuthChecker::AdminChecker() || AuthChecker::HODChecker() || AuthChecker::HOPChecker() || AuthChecker::MisStateChecker() ||  AuthChecker::DashboardChecker()) {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
            // echo 1;die();
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                // echo $designation_id_old;die();
                if (in_array($roleObj['scheme_id'],array(3,2,10,11,8,9,17,19,1))) {
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
            'caste-wise-report/list',
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
    public function benList(Request $request){
        // dd($request->all());
        $roleArray = $request->session()->get('role');
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")  order by rank"));
        if (AuthChecker::AdminChecker() || AuthChecker::HODChecker() || AuthChecker::HOPChecker() || AuthChecker::MisStateChecker() ||  AuthChecker::DashboardChecker()) {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
        } else if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                if (in_array($roleObj['scheme_id'],array(3,2,10,11,8,9,17,19,1))) {
                    $is_urban = $roleObj['is_urban'];
                    $district_code = $roleObj['district_code'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }

            if (empty($district_code))
                return redirect("/")->with('success', 'User Disabled. ');
        } else {
            return redirect("/")->with('success', 'User Disabled. ');
        }
        $dist_code = $request->district;
        $filter = $request->search_for;
        $scheme_id = $request->scheme_code;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $gp_ward = $request->gp_ward;
        $muncid = $request->muncid;
        $table_name = $this->getSchemaName($scheme_id);
        // echo $table_name;die();
        if ($request->ajax()) {
           $result = $this->getCasteWiseData($scheme_id,$dist_code,$urban_code,$block,$muncid,$gp_ward,$table_name,$filter);
            // print_r($result);die();
            // dd($result);
            return datatables()->of($result)
            ->addColumn('mobile_no', function ($result) {
                return $result->mobile_no;
            })
            ->addColumn('caste', function ($result) {
                if($result->caste == 'ST'){
                    return 'ST';
                }else if($result->caste == 'SC'){
                    return 'SC';
                }else if($result->caste == 'General'){
                    return 'General';
                }else if($result->caste == 'OTHERS'){
                    return 'OTHERS';
                }else{
                    return 'Not Defined';
                }
            })
            ->make(true);
        }
    }
    public function castewiseExportExcel(Request $request){
        $roleArray = $request->session()->get('role');
        $user_id = AuthChecker::getUserId();
        $scheme_id = $request->scheme_id;
        if (AuthChecker::AdminChecker() || AuthChecker::HODChecker() || AuthChecker::HODChecker() || AuthChecker::MisStateChecker() ||  AuthChecker::MisStateChecker()) {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
        } else if (AuthChecker::ApproverChecker() || AuthChecker::VerifierChecker()) {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == $scheme_id) {
                    $is_urban = $roleObj['is_urban'];
                    $district_code = $roleObj['district_code'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }

            if (empty($district_code))
                return redirect("/")->with('success', 'User Disabled. ');
        } else {
            return redirect("/")->with('success', 'User Disabled. ');
        }
        $dist_code = $request->district;
        $filter = $request->search_for;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $gp_ward = $request->gp_ward;
        $muncid = $request->muncid;
        $table_name = $this->getSchemaName($scheme_id);
        $schemeObj = Scheme::where('id',$scheme_id)->first();
        $user_msg = 'Caste Wise Beneficiary List On';
        // echo $scheme_id;die();
        $result = $this->getCasteWiseData($scheme_id,$dist_code,$urban_code,$block,$muncid,$gp_ward,$table_name,$filter);
        // dd($result);
        $excelarr[] = array(
            'Application ID',  'Beneficiary Name', 'District', 'Block/Municipality', 'GP/Ward', 'Mobile Number','Caste' 
        );
        
        foreach ($result as $arr) {
            $status = '';
            if ($arr->caste == 'ST') {
                $status = 'ST';
            } else  if ($arr->caste == 'SC' ) {
                $status = 'ST';
            }  else  if ($arr->caste == 'General') {
                $status = 'General';
            } else  if ($arr->caste == 'OTHERS' ) {
               $status = 'OTHERS';
            } else{
                $status = 'Not Defined';
            } 


            // echo 1;die();
            $excelarr[] = array(
                'Application Id' => trim($arr->id),
                'Beneficiary Name' => trim($arr->name),
                'District' => trim($arr->district_name),
                'Block/Municipality' => trim($arr->block_name),
                'GP/Ward' => trim($arr->gp),
                'Mobile Number' => trim($arr->mobile_no),
                'Caste' => $status
            );
        }
        // print_r($excelarr);die();
        $file_name = $schemeObj->scheme_name.' '.$user_msg .' '.  date('d/m/Y');
        Excel::create($file_name, function ($excel) use ($excelarr) {
            $excel->setTitle('Jai Bangla Duplicate Report');
            $excel->sheet('Jai Bangla Duplicate Report', function ($sheet) use ($excelarr) {
                $sheet->fromArray($excelarr, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    private function getCasteWiseData($scheme_id,$dist_code,$urban_code,$block,$muncid,$gp_ward,$table_name,$filter){
        $query = "SELECT b.id, 
        CONCAT(trim(b.ben_fname),' ', trim(b.ben_mname),' ',trim(b.ben_lname)) AS name, 
        d.district_name,
        trim(block_ulb_name) AS block_name,
        trim(gp_ward_name) AS gp, 
        b.mobile_no as mobile_no, 
        trim(b.caste) as caste, 
        b.next_level_role_id
        FROM $table_name b 
            LEFT JOIN public.m_district d ON b.dist_code = d.district_code
            WHERE b.scheme_id = ".$scheme_id." ";
        if (!empty($dist_code)) {
            $query .= " AND b.created_by_dist_code = " . $dist_code;
        }
        if(!empty($urban_code)){

            $query .= " AND ( b.rural_urban_id = " .$urban_code . " or b.rural_urban_id is null)";
            if($urban_code == 1){
                if(!empty($block)){
                    $query .= " AND b.created_by_local_body_code = " .$block;
                }
                if(!empty($muncid)){
                    $query .= " AND b.block_ulb_code = " .$muncid;
                }
            }else if($urban_code == 2){
                if(!empty($block)){
                    $query .= " AND b.created_by_local_body_code = " .$block;
                }
            }
            if(!empty($gp_ward)){
                $query .= " AND b.gp_ward_code = " .$gp_ward;
            }
        }
        // if (!empty($block)) {
        //     $query .= " AND b.created_by_local_body_code = " . $block;
        // }
        if (!empty($filter)) {
            if ($filter == '1') {
                $query .= " AND b.next_level_role_id is null";
            }
            elseif($filter == '2') {
                $query .= " AND b.next_level_role_id >0";
            }
            elseif ($filter == '3') {
                $query .= " AND b.next_level_role_id = 0 ";
            } 
        }else{
            $query .= " AND (b.next_level_role_id is null or b.next_level_role_id>0 or  b.next_level_role_id=0)";
        }
        $query .= " order by block_ulb_name,gp_ward_name";
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }

}
