<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\District;
use App\Scheme;
use Redirect;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Validator;
use DateTime;
use Config;
use App\Configduty;
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
use App\BankDetails;
use App\UrbanBody;
use App\Ward;
use App\GP;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\AcceptRejectInfo;
use App\MapLavel;
use App\BenDocs;
use Exception;
use App\Traits\TraitAadharValidate;
use App\Helpers\AuthChecker;

class CmoGrivanceReportController extends Controller
{
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
    }
    function misReport(Request $request)
    {
      $base_date  = '2020-01-01';
      $c_time = Carbon::now();
      $c_date = $c_time->format("Y-m-d");
      $is_active = 0;
      $roleArray = $request->session()->get('role');
      $designation_id_old = Auth::user()->designation_id_old;
      $district_visible = $is_urban_visible = $block_visible = 1;
      $municipality_visible = 0;
      $gp_ward_visible = 0;
      $muncList = collect([]);
      $gpList = collect([]);
      $userId = Auth::user()->id;
      $scheme_code_in=array();
      $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 and id = 10 order by scheme_name"));
      foreach($scheme_list as $scheme_item){
        array_push($scheme_code_in,$scheme_item->id);

      }
      if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
          $district_visible = $is_urban_visible = $block_visible = 1;
      } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
        $district_code = NULL;
        $is_urban = NULL;
        $blockCode = NULL;
        $scsctvisible = 0;
        foreach ($roleArray as $roleObj) {
          if (in_array($roleObj['scheme_id'], $scheme_code_in)) {
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
      //$is_urban_visible=0;
      $block_visible=0;
      $municipality_visible=0;
      $gp_ward_visible=0;
      return view(
          'SmCmo.misreport',
          [
              'scheme_list' => $scheme_list,
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
              'base_date' => $base_date,
              'c_date' => $c_date,
              'gpList' => $gpList,
              'muncList' => $muncList,
              'designation_id_old' => $designation_id_old,
          ]
      );
    }
    public function misReportPostCmo(Request $request)
    {
        //dd($request->all());
        //$ds_phase_list = Config::get('constants.ds_phase.phaselist');
        $scheme_id = $request->scheme_id;
        $ds_phase = $request->ds_phase;
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        $gp_ward = $request->gp_ward;
        $select_year= $request->select_year;
        $select_month= $request->select_month;
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
            'gp_ward' => 'nullable|integer',
            'from_date'    => 'nullable|date|after_or_equal:' . $base_date . '|before_or_equal:' . $c_date,
            'to_date'      => 'nullable|date|after_or_equal:from_date|before_or_equal:' . $c_date,
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
            $scheme_row=Scheme::where('id',$scheme_id)->first();
            $user_msg = " CMO Grievance Mis Report for the Scheme ". $scheme_row->scheme_name.' for Beneficiary';
            $title = $user_msg;
            //dd($title);

            $data = array();
            $return_status = 1;
            $return_msg = '';
            $heading_msg = '';
            $external = 0;
            $external_arr = array();
            $external_filter = array();
            $from_date=NULL;
            $to_date=NULL;
            $caste=NULL;
            $ds_phase=NULL;
            if (!empty($gp_ward)) {
                if ($urban_code == 1) {
                    $column = "Ward";
                    $heading_msg =  $user_msg . ' of the Ward ' . $gp_ward_name;
                    $data = $this->getWardWise($scheme_id,$district, $block, $muncid, $gp_ward, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                } else {
                    $column = "GP";
                    $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
                    $data = $this->getGpWise($scheme_id,$district, $block, NULL, $gp_ward, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                }
            } else if (!empty($muncid)) {
                $column = "Ward";
                $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
                $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
                $data = $this->getWardWise($scheme_id,$district, $block, $muncid, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
            } else if (!empty($block)) {
                if ($urban_code == 1) {
                    $column = "Municipality";
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getMuncWise($scheme_id,$district, $block, NULL, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                } else if ($urban_code == 2) {
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getGpWise($scheme_id,$district, $block, NULL, $gp_ward, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                }
            } else {

                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getSubDivWise($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getBlockWise($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getBlockWise($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                        $data2 = $this->getSubDivWise($scheme_id,$district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getDistrictWise($scheme_id,NULL, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase,$select_year,$select_month);

                    $external = 0;
                }
            }
            if (!empty($caste)) {
                $heading_msg = $heading_msg . " for the Caste  " . $caste;
            }
            if (!empty($ds_phase)) {
                $heading_msg = $heading_msg . " of the " . $ds_phase_list[$ds_phase];
            }
            if (!empty($from_date)) {
                $form_date_formatted = \Carbon\Carbon::parse($from_date)->format('d-m-Y');
                $heading_msg = $heading_msg . " from " . $form_date_formatted;
            }
            if (!empty($to_date)) {
                $to_date_formatted = \Carbon\Carbon::parse($to_date)->format('d-m-Y');
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
   
  
    
    public function getBlockWise($scheme_id,$district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL, $select_year= NULL , $select_month=NULL)
    {
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $month = "";
      if($select_month != ""){
        $month = "AND trim(TO_CHAR(approval_date::date, 'Month')) = '" . $select_month . "'";
      }
      $whereMain = "where  district_code=" . $district_code;
        $query = "select main.location_id,main.location_name,
        COALESCE(bp_main.sm_cmo,0) as sm_cmo
        from
        (
          select block_code as location_id,'Block-'||block_name as location_name
          from public.m_block  " . $whereMain . " 
        ) as main LEFT JOIN
        (
              select count(1)  as sm_cmo,
              created_by_local_body_code
              from " . $schema . ". beneficiary where  next_level_role_id=0 and sm_flag=1 and created_by_dist_code= " . $district_code . "
              ". $month ."
                AND case when (extract(month from approval_date::date)) < 4 then 	
                    cast(extract(year from approval_date::date) -1 as varchar(4)) || '-' || cast(extract(year from approval_date::date) as varchar(4)) 
                    else
                    cast(extract(year from approval_date::date) as varchar(4)) || '-' || cast(extract(year from approval_date::date)+1 as varchar(4))
                    end  = '".$select_year."'
              group by created_by_local_body_code
         )  
        as bp_main ON main.location_id=bp_main.created_by_local_body_code
         order by main.location_name";

        //echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
    public function getSubDivWise($scheme_id,$district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL, $select_year= NULL , $select_month=NULL)
    {
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $whereMain = "where  district_code=" . $district_code;
      $month = "";
      if($select_month != ""){
        $month = "AND trim(TO_CHAR(approval_date::date, 'Month')) = '" . $select_month . "'";
      }

        $query = "select main.location_id,main.location_name,
        COALESCE(bp_main.sm_cmo,0) as sm_cmo 
        from
        (
          select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
          from public.m_sub_district  " . $whereMain . " 
        ) as main LEFT JOIN
        (
            select count(1) as sm_cmo,
              
              created_by_local_body_code
              from " . $schema . ". beneficiary where next_level_role_id=0 and sm_flag=1 and created_by_dist_code= " . $district_code . "
             ". $month ."
              AND case when (extract(month from approval_date::date)) < 4 then 	
                  cast(extract(year from approval_date::date) -1 as varchar(4)) || '-' || cast(extract(year from approval_date::date) as varchar(4)) 
                  else
                  cast(extract(year from approval_date::date) as varchar(4)) || '-' || cast(extract(year from approval_date::date)+1 as varchar(4))
                  end  = '".$select_year."'
              group by created_by_local_body_code
         )  
         as bp_main ON main.location_id=bp_main.created_by_local_body_code
         order by main.location_name";

         //echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        return $result;
    }
    public function getDistrictWise($scheme_id,$district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL, $select_year= NULL , $select_month=NULL)
    {
        //dd($select_month);
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $month = "";
      if($select_month != ""){
        $month = "AND trim(TO_CHAR(approval_date::date, 'Month')) = '" . $select_month . "'";
      }
        $query = "select main.location_id,main.location_name,
        COALESCE(bp_main.sm_cmo,0) as sm_cmo 
        from
        (
        select district_code as location_id,district_name as location_name
        from public.m_district  
        ) as main LEFT JOIN
        (
              select count(1)  as sm_cmo,
              created_by_dist_code
              from " . $schema . ". beneficiary where next_level_role_id=0 and sm_flag=1
              ". $month ."
              AND case when (extract(month from approval_date::date)) < 4 then 	
                  cast(extract(year from approval_date) -1 as varchar(4)) || '-' || cast(extract(year from approval_date::date) as varchar(4)) 
                  else
                  cast(extract(year from approval_date::date) as varchar(4)) || '-' || cast(extract(year from approval_date::date)+1 as varchar(4))
                  end  = '".$select_year."'
              group by created_by_dist_code
         )  
        as bp_main ON main.location_id=bp_main.created_by_dist_code
         order by main.location_name";
          //echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        // dd($result);
        return $result;
    }

    public function index(Request $request)
    {
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        // echo '<pre>'; print_r($roleArray);die();
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = AuthChecker::getUserId();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        $duty = Configduty::where('user_id', '=', $user_id)->first();

        // $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") AND id in(2,10,11,1,3,19) order by rank"));

        $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1) and is_active=1 and id = 10 order by scheme_name"));


        // echo '<pre>';print_r($schemes);die();
        if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
            // echo 1;die();
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                // echo $designation_id_old;die();
                if ($roleObj['scheme_id'] == 2 || $roleObj['scheme_id'] == 10 || $roleObj['scheme_id'] == 11 || $roleObj['scheme_id'] == 1 || $roleObj['scheme_id'] == 3 || $roleObj['scheme_id'] == 19) {
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
        $errormsg = Config::get('constants.errormsg');

        return view(
            'SmCmo/cmoGrivienceList',
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
                'scheme_id' => 10,
                'sessiontimeoutmessage' => $errormsg['sessiontimeOut'],
            ]
        );
    }
    public function benListsm(Request $request)
    {
        //dd($request->district);
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        // $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") AND id in(2,10,11,1,3,19) order by rank"));
        $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1) and is_active=1 and id = 10 order by scheme_name"));

        if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
            $district_code = $request->district;
            $is_urban = NULL;
            $blockCode = NULL;
        } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == 2 || $roleObj['scheme_id'] == 10 || $roleObj['scheme_id'] == 11 || $roleObj['scheme_id'] == 1 || $roleObj['scheme_id'] == 3 || $roleObj['scheme_id'] == 19) {
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
        $scheme_id = $request->scheme_code;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $gp_ward = $request->gp_ward;
        $muncid = $request->muncid;
        $table_name = $this->getSchemaName($scheme_id);
        if ($request->ajax()) {
            $query = "SELECT b.id, 
            CONCAT(trim(b.ben_fname),' ', trim(b.ben_mname),' ',trim(b.ben_lname)) AS name, 
            d.district_name,
            trim(block_ulb_name) AS block_name,
            trim(gp_ward_name) AS gp, 
            b.mobile_no as mobile_no, 
            b.sm_mobile_no as sm_mobile_no, 
            b.payment_count as payment_count, 
            b.lot_generated as lot_generated, 
            b.approval_date::date as approval_date,next_level_role_id
            FROM " . $table_name . " b 
                LEFT JOIN public.m_district d ON b.dist_code = d.district_code
                WHERE b.scheme_id = ".$scheme_id."  AND sm_flag= 1 AND (is_rejected=0 OR is_rejected is NULL)
                and (next_level_role_id=0  or next_level_role_id>0)";
                
            if (!empty($district_code)) {
                $query .= " AND b.created_by_dist_code = " . $district_code;
            }
            if (!empty($urban_code)) {
                $query .= " AND b.rural_urban_id = " . $urban_code;
            }
            if (!empty($block)) {
                $query .= " AND b.created_by_local_body_code = " . $block;
            }
         // echo $query; die;


            $result = DB::connection('pgsql_mis')->select($query);
            return datatables()->of($result)
            ->addColumn('id', function ($data) {


                return $data->id;
            })
            ->addColumn('ben_name', function ($data) {
                // return $data->getName();
                return $data->name;
            })
            ->addColumn('mobile_no', function ($result) {
                return $result->sm_mobile_no;
            })
            ->addColumn('sm_mobile_no', function ($result) {
                return $result->mobile_no;
            })
            ->addColumn('approval_date', function ($result) {
                $convertedDate = date("d-m-Y", strtotime($result->approval_date));
                return $convertedDate;
            })->addColumn('view', function ($data) use ($scheme_id, $designation_id_old) {             
      
                if ($designation_id_old == 'Approver') {
                 $action = '';
                  if (($data->lot_generated == 1) && ($data->payment_count > 0)) {
                    $action = '<span class="badge badge-danger">Payment has been initiated</span>';
                  } 
                  else{
                    $action = '<button type="button"  class="btn btn-xs btn-info ben_revert_button" id="'.$data->id.'">Revert</button>';
                    $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-warning ben_unmark_button">Unmark</button>';

                  }
                }
      
                return $action;
              })->addColumn('check', function ($data) use ($designation_id_old) {
                if ($designation_id_old == 'Approver') {
                    if (($data->lot_generated == 1) && ($data->payment_count > 0)) {
                        return '';
                    }
                    else{
                        return '<input type="checkbox" name="approvalcheck[]" onClick="controlCheckBox()" value="' . $data->id . '">';
                    }
                
                } else {
                  return '';
                }
              })->rawColumns(['view', 'check'])->make(true);
        }
    }

    private function getSchemaName($scheme_id)
    {
        if (!is_null($scheme_id)) {
            $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
            $schema_name =  $sObj->short_code;
            if (empty($schema_name)) {
                $schema_name = 'pension';
            }
            $table_name =  strtolower($schema_name) . '.beneficiary';
        } else {
            $table_name =  'pension.beneficiary';
        }
        return $table_name;
    }




    public function exportExcelcmo(Request $request)
    {
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = AuthChecker::getUserId();
        $scheme_id = $request->scheme_id;
        if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
            $district_code =$request->district;
            $is_urban = NULL;
            $blockCode = NULL;
        } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
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
        $urban_code = $request->urban_code;
        $block = $request->block;
        $gp_ward = $request->gp_ward;
        $muncid = $request->muncid;
        $table_name = $this->getSchemaName($scheme_id);
        $schemeObj = Scheme::where('id',$scheme_id)->first();
        // echo $scheme_id;die();
            $query = "SELECT b.id, 
            CONCAT(trim(b.ben_fname),' ', trim(b.ben_mname),' ',trim(b.ben_lname)) AS name, 
            d.district_name,
            trim(block_ulb_name) AS block_name,
            trim(gp_ward_name) AS gp, 
            b.mobile_no as mobile_no, 
            b.sm_mobile_no as sm_mobile_no, 
            b.approval_date as approval_date
            FROM " . $table_name . " b 
                LEFT JOIN public.m_district d ON b.dist_code = d.district_code
                WHERE b.scheme_id = ".$scheme_id." AND next_level_role_id = 0 AND sm_flag= 1";

        if (!empty($district_code)) {
                    $query .= " AND b.created_by_dist_code = " . $district_code;
        }
        if (!empty($urban_code)) {
            $query .= " AND b.rural_urban_id = " . $urban_code;
        }
        if (!empty($block)) {
                    $query .= " AND b.created_by_local_body_code = " . $block;
        }
        // dd($query);
        $result = DB::connection('pgsql_mis')->select($query);
        // dd($result);
        $excelarr[] = array(
            'Beneficiary ID',  'Beneficiary Name', 'District', 'Block/Municipality', 'GP/Ward', 'Applicant Mobile No', 'CMO Grievance Mobile No', 'Approval Date', 
        );
        
        foreach ($result as $arr) {
            // echo 1;die();
            $excelarr[] = array(
                'Beneficiary Id' => trim($arr->id),
                'Beneficiary Name' => trim($arr->name),
                'District' => trim($arr->district_name),
                'Block/Municipality' => trim($arr->block_name),
                'GP/Ward' => trim($arr->gp),
                'Applicant Mobile No' => trim($arr->mobile_no),
                'CMO Grievance Mobile No' => trim($arr->sm_mobile_no),
                'Approval Date' => trim($arr->approval_date),
            
            );
        }
       // dd($excelarr);die();
        $file_name = $schemeObj->scheme_name . ' ' . date('Y-m-d');
        Excel::create($file_name, function ($excel) use ($excelarr) {
            $excel->setTitle('Jai Bangla Duplicate Report');
            $excel->sheet('Jai Bangla Duplicate Report', function ($sheet) use ($excelarr) {
                $sheet->fromArray($excelarr, null, 'A1', false, false);
            });
        })->download('xlsx');
    }
    public function revertApplication(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $scheme_id = $request->scheme_id;
        $ben_id = $request->ben_id;
        if (empty($request->ben_id)) {
            $return_status = 0;
            $return_text = 'Beneficiary ID Not Found';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        if (!ctype_digit($ben_id)) {
            $return_status = 0;
            $return_text = 'Beneficiary Not Valid';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        if (empty($request->scheme_id)) {
            $return_status = 0;
            $return_text = 'Scheme ID Not Found';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        if (!ctype_digit($scheme_id)) {
            $return_status = 0;
            $return_text = 'Scheme ID Not Valid';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
        $district_code = $duty_obj->district_code;
        if (empty($duty_obj)) {
                $return_status = 0;
                $return_text = 'Not Allowed';
                $return_msg = array("" . $return_text);
                return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
         }
        $schemeObj = Scheme::where('id',$scheme_id)->first();
        if (empty($schemeObj)) {
            $return_status = 0;
            $return_text = 'Scheme Not Found';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        if (!empty($schemeObj->short_code)) {
            $schema = $schemeObj->short_code;
          } else {
            $schema = "pension";
          }
        DB::beginTransaction();
        $c_time = date('Y-m-d H:i:s', time());
        $accept_reject_model = new AcceptRejectInfo;
        $accept_reject_model->created_at = $c_time;
        $accept_reject_model->application_id = $ben_id;
        $accept_reject_model->scheme_id = $scheme_id;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->ip_address = request()->ip();
        $accept_reject_model->op_type = 'AE';
        $revert_reason = 'Reverted by user: ' . $user_id;
        $input_update = ['approval_rejected' => 3, 'next_level_role_id' => null,'is_verified' => 0,'is_approved' => 0, 'comments' => $revert_reason,'is_reverted' =>1];
        $upadated_main = DB::table($schema . '.beneficiary')
        ->where([
          'id' => $ben_id, 'created_by_dist_code' => $district_code
        ])->where('next_level_role_id', 0)->where('sm_flag', 1)->where('lot_generated', 0)->where('payment_count', 0)->where('is_rejected', 0)->update($input_update);
        $is_saved_log = $accept_reject_model->save();
        if( $upadated_main && $is_saved_log){
            DB::commit();
            $return_status = 1;
            $return_text = "Beneficiary with Id:".$ben_id." Successfully Reverted";
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        else{
            DB::rollback();
            $return_status = 0;
            $return_text = 'Error. Please try Again.';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
    }
    public function bulkRevert(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $scheme_id = $request->scheme_id;
       
        if (empty($request->scheme_id)) {
            $return_status = 0;
            $return_text = 'Scheme ID Not Found';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        if (!ctype_digit($scheme_id)) {
            $return_status = 0;
            $return_text = 'Scheme ID Not Valid';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
        $district_code = $duty_obj->district_code;
        if (empty($duty_obj)) {
                $return_status = 0;
                $return_text = 'Not Allowed';
                $return_msg = array("" . $return_text);
                return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
         }
        $schemeObj = Scheme::where('id',$scheme_id)->first();
        if (empty($schemeObj)) {
            $return_status = 0;
            $return_text = 'Scheme Not Found';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        if (!empty($schemeObj->short_code)) {
            $schema = $schemeObj->short_code;
          } else {
            $schema = "pension";
          }
          $applicationid_arr = array();
          $inputs = request()->input('approvalcheck');
          // $backToVerifier = $request->backToVerifier;
          // dd($backToVerifier);
          $c_time = date('Y-m-d H:i:s', time());
          foreach ($inputs as $input) {
            array_push($applicationid_arr, $input);
          }
          $back_url = 'sm-cmoMisReportlist?scheme_id=' . $scheme_id;
        DB::beginTransaction();
      
        $i=0;
        foreach ($applicationid_arr as $application_item) {
            $modelNameAcceptReject = new AcceptRejectInfo;
            $op_type = 'AE';
            $modelNameAcceptReject->created_at =  $c_time;
            $modelNameAcceptReject->op_type =  $op_type;
            $modelNameAcceptReject->application_id = $application_item;
            $modelNameAcceptReject->user_id = $user_id;
            $modelNameAcceptReject->created_by_dist_code = $district_code;
            $modelNameAcceptReject->ip_address = request()->ip();
            $is_accept_reject = $modelNameAcceptReject->save();
            if ($is_accept_reject) {
              $i++;
            }
          }
          if ($i == count($applicationid_arr)) {
            $is_accept_reject = 1;
          } else {
            $is_accept_reject = 0;
          }
          $revert_reason = 'Reverted by user: ' . $user_id;
          $input_update = ['approval_rejected' => 3, 'is_verified' => 0,'is_approved' => 0,'next_level_role_id' => null, 'comments' => $revert_reason,'is_reverted' =>1];
    
          $upadated_main = DB::table($schema . '.beneficiary')->whereIn('id', $applicationid_arr)
            ->where('created_by_dist_code', $district_code)->where('sm_flag', 1)->where('next_level_role_id', 0)->
            where('lot_generated', 0)->where('payment_count', 0)->update($input_update);
            if ($upadated_main && $is_accept_reject) {
              DB::commit();
              return redirect($back_url)->with('message', 'Beneficiaries has been reverted Succesfully!');
            } else {
              DB::rollback();
              return redirect($back_url)->with('error', 'Error! Please try again..');
            }
    }
    public function unamrkApplication(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $scheme_id = $request->scheme_id;
        $ben_id = $request->ben_id;
        if (empty($request->ben_id)) {
            $return_status = 0;
            $return_text = 'Beneficiary ID Not Found';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        if (!ctype_digit($ben_id)) {
            $return_status = 0;
            $return_text = 'Beneficiary Not Valid';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        if (empty($request->scheme_id)) {
            $return_status = 0;
            $return_text = 'Scheme ID Not Found';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        if (!ctype_digit($scheme_id)) {
            $return_status = 0;
            $return_text = 'Scheme ID Not Valid';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
        $district_code = $duty_obj->district_code;
        if (empty($duty_obj)) {
                $return_status = 0;
                $return_text = 'Not Allowed';
                $return_msg = array("" . $return_text);
                return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
         }
        $schemeObj = Scheme::where('id',$scheme_id)->first();
        if (empty($schemeObj)) {
            $return_status = 0;
            $return_text = 'Scheme Not Found';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        if (!empty($schemeObj->short_code)) {
            $schema = $schemeObj->short_code;
          } else {
            $schema = "pension";
          }
        DB::beginTransaction();
        $c_time = date('Y-m-d H:i:s', time());
        $accept_reject_model = new AcceptRejectInfo;
        $accept_reject_model->created_at = $c_time;
        $accept_reject_model->application_id = $ben_id;
        $accept_reject_model->scheme_id = $scheme_id;
        $accept_reject_model->user_id = $user_id;
        $accept_reject_model->ip_address = request()->ip();
        $accept_reject_model->op_type = 'UNMARK';
        $revert_reason = 'Unmark by user: ' . $user_id;
        $input_update = ['approval_rejected' => 3,'sm_flag' => NULL, 'comments' => $revert_reason,'sm_mobile_no' => NULL];
        $upadated_main = DB::table($schema . '.beneficiary')
        ->where([
          'id' => $ben_id, 'created_by_dist_code' => $district_code
        ])->where('sm_flag', 1)->where('lot_generated', 0)->where('payment_count', 0)->where('is_rejected', 0)->update($input_update);
        $is_saved_log = $accept_reject_model->save();
        if( $upadated_main && $is_saved_log){
            DB::commit();
            $return_status = 1;
            $return_text = "Beneficiary with Id:".$ben_id." Successfully Unmarked";
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
        else{
            DB::rollback();
            $return_status = 0;
            $return_text = 'Error. Please try Again.';
            $return_msg = array("" . $return_text);
            return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
        }
    }
    public function cmoindex(Request $request)
    {
        try {
       // dd('ok');
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = AuthChecker::getUserId();
      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
     
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if ($designation_id_old != 'Verifier') {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      // dd($next_level_role_id_verifier);
      $type_des = 'Sarasori Mukhyamantri';
  
      $district_code = $duty_obj->district_code;
      $urban_bodys = collect([]);
      $gps = collect([]);
      $district_list_obj = collect([]);
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      if ($duty_obj->mapping_level == "Subdiv") {
        $created_by_local_body_code = $duty_obj->urban_body_code;
        $is_rural = 1;
        $verifier_type = 'Subdiv';
        $gps = collect([]);
        $urban_body_code = $duty_obj->urban_body_code;
        $urban_bodys = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();
        $urban_body_codes = [];
        $i = 0;
        foreach ($urban_bodys as $urban_body) {
  
          $urban_body_codes[$i] = $urban_body->urban_body_code;
          $i++;
        }
      }
      if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
        $is_rural = 2;
        $verifier_type = 'Block';
        $urban_bodys = collect([]);
        $taluka_code = $duty_obj->taluka_code;
        $gps = collect([]);
      }
      if ($duty_obj->mapping_level == "District") {
        $district_list_obj = District::get();
        $verifier_type = 'District';
        $is_rural = NULL;
        $created_by_local_body_code = NULL;
      }
      $check_permission_row_count = DB::table('pension.cmo_mark_block_subdiv_permission')
      ->where('scheme_id', $scheme_id)->where('block_subdiv_code', $created_by_local_body_code)->where('can_verify',true)->count();
      if (empty($check_permission_row_count) || $check_permission_row_count==0) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      return view(
        'SmCmo.linelisting',
        [
          'designation_id_old' => $designation_id_old,
          'verifier_type' => $verifier_type,
          'created_by_local_body_code' => $created_by_local_body_code,
          'is_rural' => $is_rural,
          'scheme_id' => $scheme_id,
          'scheme_name' => $scheme_obj->scheme_name,
          'gps' => $gps,
          'urban_bodys' => $urban_bodys,
          'gps' => $gps,
          'district_code' => $district_code,
          'type_des' => $type_des,
          'scheme_id' => $scheme_id
        ]
      );
    }catch (\Exception $e) {
        dd($e);
        return redirect("/")->with('danger', 'Not Allowed');
      }
    }
  public function cmolist(Request $request)
  {
    if ($request->ajax()) {
      $designation_id_old = Auth::user()->designation_id_old;
      if (!in_array($designation_id_old, array('Verifier'))) {
        return redirect("/")->with('error', 'Not Allowed');
      }
      $user_id = AuthChecker::getUserId();
      $scheme_id = $request->scheme_id;
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
      $application_type = $request->application_type;
      $district_code = $duty_obj->district_code;
      if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;

      }else if ($duty_obj->mapping_level == "Subdiv") {
        $created_by_local_body_code = $duty_obj->urban_body_code;
      }
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();

      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      if($application_type==1){
        $query="select A.*,B.next_level_role_id,B.is_rejected,B.is_verified,B.is_approved,B.sm_flag from
        (
        select id,ben_name,sm_mobile_no,jb_beneficiary_id,dist_name,block_ulb_name,next_level_role_id as cm_next_level_role_id 
        from pension.cmo_sm_data  where created_by_dist_code=".$district_code." 
        and created_by_local_body_code=".$created_by_local_body_code." and next_level_role_id=1 and scheme_id=".$scheme_id."
        ) as A LEFT JOIN ".$schema .".beneficiary as B ON A.jb_beneficiary_id=B.id";

      } 
      else if($application_type==2){
        $query="select A.*,B.next_level_role_id,B.is_rejected,B.is_verified,B.is_approved,B.sm_flag from
        (
        select id,ben_name,sm_mobile_no,jb_beneficiary_id,dist_name,block_ulb_name,next_level_role_id as cm_next_level_role_id 
        from pension.cmo_sm_data  where created_by_dist_code=".$district_code." 
        and created_by_local_body_code=".$created_by_local_body_code." and next_level_role_id=2 and scheme_id=".$scheme_id."
        ) as A LEFT JOIN ".$schema .".beneficiary as B ON A.jb_beneficiary_id=B.id";

      } 
      else if($application_type==3){
        $query="select A.*,B.next_level_role_id,B.is_rejected,B.is_verified,B.is_approved,B.sm_flag from
        (
        select id,ben_name,sm_mobile_no,jb_beneficiary_id,dist_name,block_ulb_name,next_level_role_id as cm_next_level_role_id 
        from pension.cmo_sm_data  where created_by_dist_code=".$district_code." and 
        created_by_local_body_code=".$created_by_local_body_code."  and scheme_id=".$scheme_id."
        ) as A JOIN ".$schema .".beneficiary as B ON A.jb_beneficiary_id=B.id
        where B.next_level_role_id IS NULL and B.sm_flag=1";

      } 
      else if($application_type==4){
        $query="select A.*,B.next_level_role_id,B.is_rejected,B.is_verified,B.is_approved,B.sm_flag from
        (
        select id,ben_name,sm_mobile_no,jb_beneficiary_id,dist_name,block_ulb_name,next_level_role_id as cm_next_level_role_id 
        from pension.cmo_sm_data  where created_by_dist_code=".$district_code." 
        and created_by_local_body_code=".$created_by_local_body_code." and scheme_id=".$scheme_id."
        ) as A JOIN ".$schema .".beneficiary as B ON A.jb_beneficiary_id=B.id
        where B.is_verified=1 and B.next_level_role_id IS NOT NULL and B.sm_flag=1";

      } 
      else if($application_type==5){
        $query="select A.*,B.next_level_role_id,B.is_rejected,B.is_verified,B.is_approved,B.sm_flag from
        (
        select id,ben_name,sm_mobile_no,jb_beneficiary_id,dist_name,block_ulb_name,next_level_role_id as cm_next_level_role_id 
        from pension.cmo_sm_data  where created_by_dist_code=".$district_code." 
        and created_by_local_body_code=".$created_by_local_body_code."   and scheme_id=".$scheme_id."
        ) as A JOIN ".$schema .".beneficiary as B ON A.jb_beneficiary_id=B.id
        where B.is_approved=1 and B.next_level_role_id=0 and B.sm_flag=1";

      } 
     
      else if($application_type==6){
        $query="select A.*,B.next_level_role_id,B.is_rejected,B.is_verified,B.is_approved,B.sm_flag from
        (
        select id,ben_name,sm_mobile_no,jb_beneficiary_id,dist_name,block_ulb_name,next_level_role_id as cm_next_level_role_id 
        from pension.cmo_sm_data  where created_by_dist_code=".$district_code." 
        and created_by_local_body_code=".$created_by_local_body_code."  and scheme_id=".$scheme_id." 
        ) as A JOIN ".$schema .".beneficiary as B ON A.jb_beneficiary_id=B.id
        where B.is_rejected=1 and B.next_level_role_id<0 and B.sm_flag=1";

      } 
      if (!empty($request->block_ulb_code)) {
        $query .= " AND B.block_ulb_code=" . $request->block_ulb_code . " ";
      }
      $data = DB::select($query);

      //print_r($data);die;
      return datatables()->of($data)
          ->addIndexColumn()
          ->addColumn('action', function ($data) {
            $btn='';
            if (!is_null($data->jb_beneficiary_id) && ($data->is_rejected==1 && !is_null($data->is_rejected))) {
              $btn ='<span class="badge badge-info"Rejected</span>';
            }
            else if (!is_null($data->jb_beneficiary_id) && ($data->next_level_role_id=0 && !is_null($data->next_level_role_id)) && ($data->is_approved==1 && !is_null($data->is_approved))) {
              $btn ='<span class="badge badge-success">Verified and Approved</span>';
            }
            else if (!is_null($data->jb_beneficiary_id) && !is_null($data->next_level_role_id) && ($data->is_approved==0 && is_null($data->is_approved)) && ($data->is_verified==1 && !is_null($data->is_verified))) {
              $btn ='<span class="badge badge-success">Verified but Approval Pending</span>';
            }
            else if (!is_null($data->jb_beneficiary_id) && is_null($data->next_level_role_id)) {
              $btn ='<span class="badge badge-success">Verification and Approval Pending</span>';
            }
            else if (is_null($data->jb_beneficiary_id) && $data->cm_next_level_role_id==2 && is_null($data->next_level_role_id)){
              $btn ='<span class="badge badge-info">Sent request to Operator for New Entry</span>';
            }else if (is_null($data->jb_beneficiary_id) && $data->cm_next_level_role_id==1 && is_null($data->next_level_role_id)){
              $btn = '<button type="button"  search_by_value="'.base64_encode(Crypt::encryptString($data->sm_mobile_no)).'"   value="'.$data->id.'" id="btn-processed-'.$data->id.'" class="btn btn-xs btn-info btn-processed"><i class="glyphicon glyphicon-edit"></i> Proceed</a>';
            }
              return   $btn;
          })
          ->addColumn('id', function ($data) {
              return $data->id;
          })
          ->addColumn('name', function ($data) {
              return $data->ben_name;
          })
          ->addColumn('mobile_no', function ($data) {

              return $data->sm_mobile_no;
          })->addColumn('jb_beneficiary_id', function ($data) {

            return $data->jb_beneficiary_id;
          })
         
          ->rawColumns(['name', 'id','mobile_no','jb_beneficiary_id','action'])
          ->make(true);
  
  }
}
public function checkCmo(Request $request){
    try {
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      if (!in_array($designation_id_old, array('Operator','Verifier'))) {
        return redirect("/")->with('error', 'Not Allowed');
      }
      $user_id = AuthChecker::getUserId();
      $id = $request->cmo_id;
      if (empty($request->cmo_id)) {
        return redirect("/")->with('danger', 'ID Not Found');
      }
      if (empty($request->search_by_key)) {
        return redirect("/")->with('danger', 'Search Key Not Found');
      }
      if (empty($request->search_by_value)) {
        return redirect("/")->with('danger', 'Search Value Not Found');
      }
      $search_by_key = $request->search_by_key;
      if($search_by_key==1){
        $search_by_key_label='Cmo Grievance Mobile Number';
      }
      else if($search_by_key==2){
        $search_by_key_label='Applicant Mobile Number';
      }
      else if($search_by_key==3){
        $search_by_key_label='Applicant Beneficiary Id';
      }
      else if($search_by_key==4){
        $search_by_key_label='Applicant Aadhaar Number';
      }
      else if($search_by_key==5){
        $search_by_key_label='Applicant Bank Account Number';
      }
      else{
        return redirect("/")->with('danger', 'Search Key Not Found');
      }
      $search_by_value=Crypt::decryptString(base64_decode($request->search_by_value));
      $scheme_id = $request->scheme_id;

      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }

      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();

      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }

      $type_des = 'Sarasori Mukhyamantri ';
      $district_code = $duty_obj->district_code;
      if ($duty_obj->mapping_level == "Subdiv") {
        $created_by_local_body_code = $duty_obj->urban_body_code;
      }
      if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
      }
      $check_permission_row_count = DB::table('pension.cmo_mark_block_subdiv_permission')
      ->where('scheme_id', $scheme_id)->where('block_subdiv_code', $created_by_local_body_code)->where('can_verify',true)->count();
      if (empty($check_permission_row_count) || $check_permission_row_count==0) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $query = DB::table('pension.cmo_sm_data')
        ->where('created_by_dist_code', $district_code)->where('id', $id)->where('scheme_id',$scheme_id)->whereNull('jb_beneficiary_id');
      $row = $query->first();
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $match_found=0;
      
      $where_condition="";
      if($request->search_by_key==1){
        $where_condition=" and mobile_no=".$search_by_value;
      }
      else if($request->search_by_key==2){
        $where_condition=" and mobile_no=".$search_by_value;
      }
      else if($request->search_by_key==3){
        $where_condition=" and id=".$search_by_value;
      }
      else if($request->search_by_key==4){
        $where_condition=" and trim(aadhar_no)='".$search_by_value."'";
      }
      else if($request->search_by_key==5){
        $where_condition=" and trim(bank_code)='".$search_by_value."'";
      }
     
       $row_ben_list = DB::select("select A.*,B.district_name,C.block_sub_district_name from
       (
       select created_by_dist_code,created_by_local_body_code,id,ben_fname,ben_mname,ben_lname,mobile_no,aadhar_no,dob,bank_code,
       bank_ifsc,block_ulb_name,
       gp_ward_name from ".$schema .".beneficiary 
       where is_rejected=0 ". $where_condition."
         ) as A Left join
         (
         select district_code,district_name from m_district) as B ON A.created_by_dist_code=B.district_code
         Left join
         (
         select block_code as created_by_local_body_code,'Block '||trim(block_name) as block_sub_district_name from m_block
           UNION
         select sub_district_code as created_by_local_body_code,'Sub Division '||trim(sub_district_name) as block_sub_district_name from m_sub_district
 
           
         ) as C ON A.created_by_local_body_code=C.created_by_local_body_code");
      if(count($row_ben_list)>0){
        //dd($row_ben_list);
        $match_found=1;
      }
      else{
        $row_ben_list=collect([]);
      }
      return view(
        'SmCmo.CheckBeneficiary',
        [
          'designation_id_old' => $designation_id_old,
          'row' => $row,
          'cmo_id' => $id,
          'scheme_id' => $scheme_id,
          'row' => $row,
          'match_ben_list' => $row_ben_list,
          'match_found' => $match_found,
          'created_by_dist_code' => $district_code,
          'created_by_local_body_code' => $created_by_local_body_code,
          'search_by_key' => $search_by_key,
          'search_by_value' => $search_by_value,
          'search_by_key_label' => $search_by_key_label,
        ]
      );
    }catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }

  }
  public function checkCmoEnCode(Request $request){
    try {
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      if (!in_array($designation_id_old, array('Operator','Verifier'))) {
        return redirect("/")->with('error', 'Not Allowed');
      }
      $user_id = AuthChecker::getUserId();
      $id = $request->cmo_id;
      if (empty($request->cmo_id)) {
        return redirect("/")->with('danger', 'ID Not Found');
      }
      if (empty($request->search_by_key)) {
        return redirect("/")->with('danger', 'Search Key Not Found');
      }
      $search_by_key = $request->search_by_key;
      if($search_by_key!=1){
          if (empty($request->search_by_value)) {
            return redirect("/")->with('danger', 'Search Value Not Found');
          }
      }
      $scheme_id = $request->scheme_id;
      if($search_by_key==1){
        $search_by_key_label='Cmo Grievance Mobile Number';
        $query = DB::table('pension.cmo_sm_data')->where('id', $id)->where('scheme_id',$scheme_id)->whereNull('jb_beneficiary_id');
        $row = $query->first();
        if (empty($row)) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        $search_by_value=base64_encode(Crypt::encryptString($row->sm_mobile_no));
      }
      else if($search_by_key==2){
        $search_by_key_label='Applicant Mobile Number';
      }
      else if($search_by_key==3){
        $search_by_key_label='Applicant Beneficiary Id';
      }
      else if($search_by_key==4){
        $search_by_key_label='Applicant Aadhaar Number';
      }
      else if($search_by_key==5){
        $search_by_key_label='Applicant Bank Account Number';
      }
      else{
        return redirect("/")->with('danger', 'Search Key Not Found');
      }
      if($search_by_key!=1){
      $search_by_value=base64_encode(Crypt::encryptString($request->search_by_value));
      }

      return redirect("/checkCmo?scheme_id=".$scheme_id."&cmo_id=".$id."&search_by_key=".$search_by_key."&search_by_value=".$search_by_value)->with('danger', 'Not Allowed');

      
    }catch (\Exception $e) {
     // dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }

  }
  public function SmPostNewEntry(Request $request)
  {
    try{
    $designation_id_old = Auth::user()->designation_id_old;
    if (!in_array($designation_id_old, array('Verifier'))) {
      return redirect("/")->with('error', 'Not Allowed');
    }
    $user_id = AuthChecker::getUserId();
    $scheme_id = $request->scheme_id;
    $cmo_id = $request->cmo_id;
    $mobile_no = trim($request->new_mobile_no);
    $rules = [
        'scheme_id' => 'required|numeric',
        'cmo_id' => 'required|numeric',
        'new_mobile_no' => 'required|numeric|digits:10'
    ];
    $attributes = array();
    $messages = array();
    $attributes['scheme_id'] = 'Scheme Code';
    $attributes['cmo_id'] = 'CMO Id';
    $attributes['new_mobile_no'] = 'Mobile Number';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (empty($scheme_obj)) {
            $errors = array();
            $errorMsg = 'Scheme Not Found';
            array_push($errors, $errorMsg);
            return redirect("/")->with('errors', $errors);
         }
  
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
  
        if (empty($duty_obj)) {
          $errors = array();
          $errorMsg = 'Duty Not Assigned';
          array_push($errors, $errorMsg);
          return redirect("/")->with('errors', $errors);
        }
        $district_code = $duty_obj->district_code;
        if ($duty_obj->mapping_level == "Subdiv") {
          $created_by_local_body_code = $duty_obj->urban_body_code;
        }
        if ($duty_obj->mapping_level == "Block") {
          $created_by_local_body_code = $duty_obj->taluka_code;
        }
        $check_permission_row_count = DB::table('pension.cmo_mark_block_subdiv_permission')
        ->where('scheme_id', $scheme_id)->where('block_subdiv_code', $created_by_local_body_code)->where('can_verify',true)->count();
        if (empty($check_permission_row_count) || $check_permission_row_count==0) {
          $errors = array();
          $errorMsg = 'Verification Not Allowed';
          array_push($errors, $errorMsg);
          return redirect("/mark-cmo-sm?scheme_id=" . $scheme_id)->with('errors', $errors);
        }
        if (!empty($scheme_obj->short_code)) {
          $schema = $scheme_obj->short_code;
        } else {
          $schema = "pension";
        }
        $query = DB::table('pension.cmo_sm_data')
          ->where('created_by_dist_code', $district_code)->where('id', $cmo_id)->where('scheme_id',$scheme_id)->whereNull('jb_beneficiary_id');
        $row = $query->first();
        if (empty($row)) {
          $errors = array();
          $errorMsg = 'Cmo Id Not Allowed';
          array_push($errors, $errorMsg);
          return redirect("/mark-cmo-sm?scheme_id=" . $scheme_id)->with('errors', $errors);
        }
        $count_already = DB::table($schema.'.beneficiary')->where('mobile_no',$mobile_no)->where('is_rejected',0)->count();
       if($count_already>0){
        $search_by_value=base64_encode(Crypt::encryptString($mobile_no));
        return redirect("/checkCmo?scheme_id=".$scheme_id."&cmo_id=".$cmo_id."&search_by_key=2&search_by_value=".$search_by_value);

       }
       
          DB::beginTransaction();
          $c_time = date('Y-m-d H:i:s', time());
          $inputMain = array();
          $inputMain['jb_applicant_mobile_no'] = $mobile_no;
          $inputMain['next_level_role_id'] = 2;

          $upadated_main = DB::table('pension.cmo_sm_data')
          ->where([
            'id' => $cmo_id,'created_by_dist_code' => $district_code,'next_level_role_id' => 1
          ])->whereNull('jb_beneficiary_id')->where('scheme_id',$scheme_id)->update($inputMain);
          $modelNameAcceptReject = new AcceptRejectInfo;
          $op_type = 'SMENTRY';
          $modelNameAcceptReject->scheme_id =  $scheme_id;
          $modelNameAcceptReject->cmo_id =  $cmo_id;
          $modelNameAcceptReject->created_at =  $c_time;
          $modelNameAcceptReject->op_type =  $op_type;
          $modelNameAcceptReject->user_id = $user_id;
          $modelNameAcceptReject->created_by_dist_code = $district_code;
          $modelNameAcceptReject->created_by_local_body_code = $created_by_local_body_code;
          $modelNameAcceptReject->ip_address = request()->ip();
          $is_accept_reject = $modelNameAcceptReject->save();
          //dump($is_accept_reject);dd($upadated_main);
          if($upadated_main && $is_accept_reject){
            DB::commit();
            $return_status = 1;
            $return_text = 'Request has been sent to Operator for New Entry';
            return redirect("/mark-sm-cmo?scheme_id=" . $scheme_id)->with('success', $return_text);

          }
          else{
            DB::rollback();
            $errors = array();
            $errorMsg = 'Error.. Please try different.';
            array_push($errors, $errorMsg);
          }

        
    }
    else {
        $return_status = 0;
        $return_msg = $validator->errors()->all();
        $errors = array();
        array_push($errors, $return_msg);

    }
    if (count($errors) > 0) {
      return redirect("/mark-sm-cmo?scheme_id=" . $scheme_id)->with('errors', $errors);
    }

  }
  catch (\Exception $e) {
           // dd($e);
    }
   
}
public function cmoEntrymark(Request $request)
    {
        try{
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        if ($designation_id_old != 'Operator') {
            return redirect("/")->with('error', 'Not Allowed');
        }
        //dd($request->get('pr1'));
        if ($request->get('scheme_id')) {
            $scheme_id = $request->scheme_id;
            $scheme_row = Scheme::where('is_active', 1)->where('id', $scheme_id)->first();

            if (empty($scheme_row)) {
                return redirect("/")->with('error', 'Parameter not valid');
            }
            // dd($scheme_row->scheme_name);
            $scheme_name = $scheme_row->scheme_name;
            $schema_name = $scheme_row->short_code;
            $scheme_id = $scheme_row->id;
           
        } else {
            return redirect("/")->with('error', 'Parameter not valid');
        }
        $is_active = 0;
        $roleArray = $request->session()->get('role');
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
        if ($is_active == 0) {
            return redirect("/")->with('error', 'User Disabled');
        }
        $check_permission_row_count = DB::table('pension.cmo_mark_block_subdiv_permission')
        ->where('scheme_id', $scheme_id)->where('block_subdiv_code', $blockCode)->where('can_entry',true)->count();
        if (empty($check_permission_row_count) || $check_permission_row_count==0) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        $report_type_name = 'Cmo Mark New Entry Applications';
        if (request()->ajax()) {
            $condition = array();
            $condition["created_by_dist_code"] = $distCode;
            $condition["created_by_local_body_code"] = $blockCode;
            $serachvalue = $request->search['value'];
            $limit = $request->input('length');
            $offset = $request->input('start');
            $totalRecords = 0;
            $filterRecords = 0;
            $data = array();
            $query =  DB::table('pension.cmo_sm_data')->where($condition)->where('next_level_role_id',2);
            



            

            $serachvalue = $request->search['value'];
            // if($blockCode=='2855')
            // {

            //     $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->toSql();
            //     dd($data);

            // }
            if (empty($serachvalue)) {
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
            } else {
                if (is_numeric($serachvalue)) {
                    //$ben_id = substr($serachvalue, -7);
                    $ben_id = $serachvalue;
                    $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                        $query1->where('mobile_no', $ben_id);
                    });
                    $totalRecords = $query->count();
                    $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
                } else {
                    $query = $query->where(function ($query1) use ($serachvalue) {
                        $query1->where('ben_name', 'like', $serachvalue . '%')
                            ->orWhere('block_ulb_name', 'like', $serachvalue . '%');
                    });
                    $totalRecords = $query->count();
                    $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
                }
                $filterRecords = count($data);
            }
            return datatables()
                ->of($data)
                ->setTotalRecords($totalRecords)
                ->setFilteredRecords($filterRecords)
                ->skipPaging()
                ->addColumn('ben_name', function ($data) {
                    // return $data->getName();
                    return $data->ben_name;
                })
                ->addColumn('cmo_mobile_no', function ($data) {
                    return $data->sm_mobile_no;
                })
                ->addColumn('appliant_mobile_no', function ($data) {
                    return $data->jb_applicant_mobile_no;
                })
                ->addColumn('block_ulb_name', function ($data) {
                    return $data->block_ulb_name;
                })
               
                ->addColumn('action', function ($data) use ($scheme_id) {
                    $val='';
                    $val = $val . '<button type="button" class="btn btn-warning btn-update" value="' . $data->id . '">Proceed</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    return $val;
                })
                ->rawColumns(['ben_name', 'cmo_mobile_no', 'appliant_mobile_no', 'block_ulb_name','action'])
                ->make(true);
        } else {

            return view(
                'SmCmo/cmoEntrymark',
                [
                    'district_code' => $distCode,
                    'block_code'=>$blockCode,
                    'scheme_id' => $scheme_id,
                    'scheme_name' => $scheme_name,
                    'report_type_name' => $report_type_name,
                    'is_urban' => $is_urban

                ]
            );
        }
    }
    catch (\Exception $e) {
        //dd($e);
    }
    }
    public function markcmopost(Request $request)
   {
    try {
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      if (!in_array($designation_id_old, array('Operator','Verifier'))) {
        return redirect("/")->with('error', 'Not Allowed');
      }
      $user_id = AuthChecker::getUserId();
      if (empty($request->scheme_id)) {
        return redirect("/")->with('danger', 'Scheme ID Not Found');
      }
      $scheme_id = $request->scheme_id;
      if (empty($request->beneficiary_id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }
      $id = $request->beneficiary_id;
      if (empty($request->cmo_id)) {
        return redirect("/")->with('danger', 'ID Not Found');
      }
      $cmo_id = $request->cmo_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('is_active', 1)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed...');
      }
     

      $district_code = $duty_obj->district_code;
      if ($duty_obj->mapping_level == "Subdiv") {
        $created_by_local_body_code = $duty_obj->urban_body_code;
      }
      if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
      }
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $check_permission_row_count = DB::table('pension.cmo_mark_block_subdiv_permission')
        ->where('scheme_id', $scheme_id)->where('block_subdiv_code', $created_by_local_body_code)->where('can_verify',true)->count();
        if (empty($check_permission_row_count) || $check_permission_row_count==0) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
      $query_cmo = DB::table('pension.cmo_sm_data')
        ->where("id",$cmo_id)->whereNull("jb_beneficiary_id")->where("created_by_dist_code",$district_code)->where("created_by_local_body_code",$created_by_local_body_code);
        $row_cmo = $query_cmo->first();
       
        if (empty($row_cmo)) {
          return redirect("/")->with('danger', 'Cmo id Not Valid');
        }
      $condition = array();
      $condition['id'] = $id;
      $condition['created_by_dist_code'] = $district_code;
      $condition['created_by_local_body_code'] = $created_by_local_body_code;
      $query = DB::table($schema . '.beneficiary')->select('id','mobile_no','ben_fname','ben_mname','ben_lname')
        ->where($condition);
      $row = $query->first();
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
        

        $c_time = date('Y-m-d H:i:s', time());
        DB::beginTransaction();



        $inputMain = array();
        $inputMain['sm_flag'] = 1;
        $inputMain['sm_mobile_no'] = trim($row_cmo->sm_mobile_no);

        $inputMainCmo = array();
        $inputMainCmo['jb_beneficiary_id'] = $row->id;
        $inputMainCmo['jb_name'] = trim($row->ben_fname).' '.trim($row->ben_mname).' '.trim($row->ben_lname);
        $inputMainCmo['jb_applicant_mobile_no'] = $row->mobile_no;
        $inputMainCmo['mark_by'] = $user_id;
        $inputMainCmo['mark_date'] = $c_time;
        $inputMainCmo['next_level_role_id'] = 3;

        $upadated_main = DB::table($schema . '.beneficiary')
          ->where($condition)->update($inputMain);
        $upadated_cmo = DB::table('pension.cmo_sm_data')
        ->where("id",$cmo_id)->whereNull("jb_beneficiary_id")->where("created_by_dist_code",$district_code)->where("created_by_local_body_code",$created_by_local_body_code)->whereNull("jb_beneficiary_id")->update($inputMainCmo);

        $modelNameAcceptReject = new AcceptRejectInfo;
        $op_type = 'SMMARK';
        $modelNameAcceptReject->scheme_id =  $scheme_id;

        $modelNameAcceptReject->created_at =  $c_time;
        $modelNameAcceptReject->op_type =  $op_type;
        $modelNameAcceptReject->application_id = $request->beneficiary_id;
        $modelNameAcceptReject->user_id = $user_id;
        $modelNameAcceptReject->created_by_dist_code = $district_code;
        $modelNameAcceptReject->created_by_local_body_code = $created_by_local_body_code;
        $modelNameAcceptReject->ip_address = request()->ip();
        $is_accept_reject = $modelNameAcceptReject->save();
       // dump($upadated_main);dump($is_accept_reject);dd($upadated_cmo);
        if ($upadated_main && $is_accept_reject && $upadated_cmo) {
          DB::commit();
          $errors = array();
          $return_text = 'Beneficiary with  Id:' . $id . ' has been marked as Sarasori Mukhyamantri';
          if($designation_id_old=='Operator'){
            return redirect("cmoEntrymark?scheme_id=" . $scheme_id)->with('success', $return_text);
          }
          else{
            return redirect("mark-sm-cmo?scheme_id=" . $scheme_id)->with('success', $return_text);
          }
        } else {
          DB::rollback();
          $errors = array();
          $errorMsg = 'Error.. Please try different.';
          array_push($errors, $errorMsg);
        }
      

      if (count($errors) > 0) {
        if($designation_id_old=='Operator'){
          return redirect("cmoEntrymark?scheme_id=" . $scheme_id)->with('errors', $errors);

        }
        else{
          return redirect("mark-sm-cmo?scheme_id=" . $scheme_id)->with('errors', $errors);

        }
      }
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
}
