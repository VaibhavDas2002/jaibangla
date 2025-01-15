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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use App\Scheme;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\DataSourceCommon;
use App\getModelFunc;
use App\DsPhase;

class NameAccountValidationMIS extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    function index(Request $request)
    {
        return redirect()->route('no-dup-verified-beneficiaries-list', ['type' => 12]);
        $this->middleware('auth');
      $base_date  = '2020-01-01';
      date_default_timezone_set('Asia/Kolkata');
      $c_time = Carbon::now();
      $c_date = $c_time->format("Y-m-d");
      $is_active = 0;
      $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
            $designation_id = Auth::user()->designation_id;
      $userId = Auth::user()->id;
      $district_visible = $is_urban_visible = $block_visible = 1;
      $municipality_visible = 0;
      $gp_ward_visible = 0;
      $muncList = collect([]);
      $gpList = collect([]);
      if ($designation_id == 'Admin' || $designation_id == 'HOD' || $designation_id == 'HOP' ||  $designation_id == 'Dashboard' || $designation_id == 'MisState' || $designation_id == 'DDO') {
          $district_visible = $is_urban_visible = $block_visible = 1;
      } else if ($designation_id == 'Approver' || $designation_id == 'Verifier') {
          $district_code = NULL;
          $is_urban = NULL;
          $blockCode = NULL;
          foreach ($roleArray as $roleObj) {
              if (in_array($roleObj['scheme_id'], array(3, 2, 10, 11, 8, 9, 17, 19, 1))) {
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
      $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
      //dd($scheme_list);
      return view(
          'NameAccountValidationMIS.index',
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
              'is_urban_visible' => $is_urban_visible,
              'base_date' => $base_date,
              'c_date' => $c_date,
              'gpList' => $gpList,
              'muncList' => $muncList
          ]
      );
    }
    public function getData(Request $request){
        $scheme_id = $request->scheme_id;
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        $gp_ward = $request->gp_ward;
        $failed_type = $request->failed_type;
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
            $scheme_row = Scheme::where('id', $scheme_id)->first();
            if($failed_type == 1){
                $user_msg = "Bank Name Validation MIS Report for the Scheme " . $scheme_row->scheme_name;
            }else{
                $user_msg = "Bank Account Validation MIS Report for the Scheme " . $scheme_row->scheme_name;
            }
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
                    $data = $this->getWardWise($district, $block, $muncid, $gp_ward,$failed_type);
                } else {
                    $column = "GP";
                    $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
                    $data = $this->getGpWise($district, $block, NULL,$failed_type);
                }
            } else if (!empty($muncid)) {
                $column = "Ward";
                $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
                $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
                $data = $this->getWardWise($district, $block, $muncid, NULL,$failed_type);
            } else if (!empty($block)) {
                if ($urban_code == 1) {
                    $column = "Municipality";
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getMuncWise($district, $block, NULL, NULL,$failed_type);
                } else if ($urban_code == 2) {
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getGpWise($district, $block, NULL);
                    $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                    $column = "Block";
                    $data = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL,$failed_type);
                }
            } else {
  
                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL,$failed_type);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL,$failed_type);
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getBlockWise($scheme_id, $district, NULL, NULL, NULL,$failed_type);
                        $data2 = $this->getSubDivWise($scheme_id, $district, NULL, NULL, NULL,$failed_type);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getDistrictWise($scheme_id, NULL, NULL, NULL, NULL,$failed_type);
  
                    $external = 0;
                }
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
            'heading_msg' => $heading_msg,
            'failed_type' => $failed_type
        ]);
    }
    public function getDistrictWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL,$failed_type)
    { 
     if($failed_type == 1){
        $query = "select A.location_id,A.location_name,
      COALESCE(C.total_name_mismatch,0) as total_name_mismatch, 
      COALESCE(C.total_yet_to_be_action_pending,0) as total_yet_to_be_action_pending, 
      COALESCE(C.total_minor_yet_to_approved,0) as total_minor_yet_to_approved,
      COALESCE(C.total_minor_approved,0) as total_minor_approved,
      COALESCE(C.total_bank_yet_to_approved,0) as total_bank_yet_to_approved,
      COALESCE(C.total_bank_approved,0) as total_bank_approved,
      COALESCE(C.total_send_rejection,0) as total_send_rejection,
      COALESCE(C.total_request_approved,0) as total_request_approved,
      COALESCE(C.total_deactivated,0) as total_deactivated
      from(
      select district_code as location_id,district_name as location_name
       from public.m_district ) as A  
      LEFT JOIN
      (select
          count(distinct failed.ben_id)  as total_name_mismatch,
          count(distinct failed.ben_id) filter(WHERE failed.edited_status = 0  AND ben.is_eligible = true and ben.acc_validated = 4) as total_yet_to_be_action_pending,
          count(1) filter(WHERE failed.failed_process_type = 1 AND failed.edited_status = 1 and ben.acc_validated=4) as total_minor_yet_to_approved,
          count(1) filter(WHERE failed.failed_process_type = 1 AND failed.edited_status = 2 and ben.acc_validated=2) as total_minor_approved,
          count(1) filter(WHERE failed.failed_process_type = 2 AND failed.edited_status = 1 and ben.acc_validated=4) as total_bank_yet_to_approved,
          count(1) filter(WHERE failed.failed_process_type = 2 AND failed.edited_status = 2 and ben.acc_validated=0) as total_bank_approved,
          count(1) filter(WHERE failed.failed_process_type = 3 AND failed.edited_status = 1 and ben.acc_validated = 4) as total_send_rejection,
          count(1) filter(WHERE failed.failed_process_type = 3  AND failed.edited_status = 2 and ben.acc_validated=5) as total_request_approved,
          count(1) filter(WHERE failed.edited_status = 0 and ben.is_eligible = false) as total_deactivated,
          failed.dist_code
          from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id where failed.scheme_id=" . $scheme_id . " AND ben.scheme_id=" . $scheme_id . "  AND failed.failed_type = 2 AND ben.legacy_validation = 0  
       group by failed.dist_code) as C ON A.location_id=C.dist_code";
     }
     if($failed_type == 2){
        $query = "select A.location_id,A.location_name,
      COALESCE(C.total_account_mismatch,0) as total_account_mismatch, 
      COALESCE(C.total_yet_to_be_action_pending,0) as total_yet_to_be_action_pending, 
      COALESCE(C.total_yet_to_approved,0) as total_yet_to_approved,
      COALESCE(C.total_approved,0) as total_approved,
      COALESCE(C.total_deactivated,0) as total_deactivated
      from(
      select district_code as location_id,district_name as location_name
       from public.m_district ) as A  
      LEFT JOIN
      (select
            count(distinct failed.ben_id)  as total_account_mismatch,
            count(distinct failed.ben_id) filter(WHERE failed.edited_status = 0 AND ben.is_eligible = true and ben.acc_validated = 3) as total_yet_to_be_action_pending,
            count(1) filter(WHERE  failed.edited_status = 1 and ben.acc_validated = 3) as total_yet_to_approved,
            count(1) filter(WHERE  failed.edited_status = 2 ) as total_approved,
            count(1) filter(WHERE failed.edited_status = 0 and ben.is_eligible = false) as total_deactivated,
          failed.dist_code
          from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id where failed.scheme_id=" . $scheme_id . " AND ben.scheme_id=" . $scheme_id . "  AND failed.failed_type =1 AND ben.legacy_validation=0 
       group by failed.dist_code) as C ON A.location_id=C.dist_code";
     }
    //    echo $query;die;
      $result = DB::connection('pgsql_paywrite')->select($query);
      return $result;
  }
  public function getSubDivWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $failed_type)
  {
      //$whereCon = "where A.dist_code=" . $district_code;
      $whereMain = "where  district_code=" . $district_code;
      if($failed_type == 1) {
        $query = "select A.location_id,A.location_name,
      COALESCE(C.total_name_mismatch,0) as total_name_mismatch, 
      COALESCE(C.total_yet_to_be_action_pending,0) as total_yet_to_be_action_pending, 
      COALESCE(C.total_minor_yet_to_approved,0) as total_minor_yet_to_approved,
      COALESCE(C.total_minor_approved,0) as total_minor_approved,
      COALESCE(C.total_bank_yet_to_approved,0) as total_bank_yet_to_approved,
      COALESCE(C.total_bank_approved,0) as total_bank_approved,
      COALESCE(C.total_send_rejection,0) as total_send_rejection,
      COALESCE(C.total_request_approved,0) as total_request_approved,
      COALESCE(C.total_deactivated,0) as total_deactivated
      from(
          select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
          from public.m_sub_district  " . $whereMain . " 
       )
       as A  
      LEFT JOIN
      (select
            count(distinct failed.ben_id)  as total_name_mismatch,
            count(distinct failed.ben_id) filter(WHERE failed.edited_status = 0  AND ben.is_eligible = true and ben.acc_validated = 4) as total_yet_to_be_action_pending,
            count(1) filter(WHERE failed.failed_process_type = 1 AND failed.edited_status = 1 and ben.acc_validated = 4) as total_minor_yet_to_approved,
            count(1) filter(WHERE failed.failed_process_type = 1 AND failed.edited_status = 2 and ben.acc_validated=2) as total_minor_approved,
            count(1) filter(WHERE failed.failed_process_type = 2 AND failed.edited_status = 1 and ben.acc_validated = 4) as total_bank_yet_to_approved,
            count(1) filter(WHERE failed.failed_process_type = 2 AND failed.edited_status = 2 and ben.acc_validated=0) as total_bank_approved,
            count(1) filter(WHERE failed.failed_process_type = 3 AND failed.edited_status = 1 and ben.acc_validated = 4) as total_send_rejection,
            count(1) filter(WHERE failed.failed_process_type = 3  AND failed.edited_status = 2 and ben.acc_validated=5) as total_request_approved,
            count(1) filter(WHERE failed.edited_status = 0 and ben.is_eligible = false) as total_deactivated,
          failed.local_body_code
          from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id where failed.scheme_id=" . $scheme_id . " AND ben.scheme_id=" . $scheme_id . " and  failed.dist_code= " . $district_code . " AND ben.dist_code= " . $district_code . "  AND failed.failed_type= 2  AND ben.legacy_validation=0 
      group by failed.local_body_code) as C ON A.location_id=C.local_body_code";
    }
    if($failed_type == 2){
        $query = "select A.location_id,A.location_name,
        COALESCE(C.total_account_mismatch,0) as total_account_mismatch, 
        COALESCE(C.total_yet_to_be_action_pending,0) as total_yet_to_be_action_pending, 
        COALESCE(C.total_yet_to_approved,0) as total_yet_to_approved,
        COALESCE(C.total_approved,0) as total_approved,
        COALESCE(C.total_deactivated,0) as total_deactivated
        from(
            select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
            from public.m_sub_district  " . $whereMain . " 
         )
         as A  
        LEFT JOIN
        (select
          count(distinct failed.ben_id)  as total_account_mismatch,
          count(distinct failed.ben_id) filter(WHERE failed.edited_status = 0 AND ben.is_eligible = true and ben.acc_validated = 3) as total_yet_to_be_action_pending,
          count(1) filter(WHERE  failed.edited_status = 1 and ben.acc_validated = 3) as total_yet_to_approved,
          count(1) filter(WHERE  failed.edited_status = 2 ) as total_approved,
	   	  count(1) filter(WHERE failed.edited_status = 0 and ben.is_eligible = false) as total_deactivated,
        failed.local_body_code
        from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id where failed.scheme_id=" . $scheme_id . " AND ben.scheme_id=" . $scheme_id . " and  failed.dist_code= " . $district_code . " AND ben.dist_code= " . $district_code . "  AND failed.failed_type= 1 AND ben.legacy_validation=0  
        group by failed.local_body_code) as C ON A.location_id=C.local_body_code";
    } 
      $result = DB::connection('pgsql_paywrite')->select($query);
      return $result;
  }

  public function getBlockWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $failed_type)
  {
      $whereMain = "where  district_code=" . $district_code;
      if($failed_type == 1) {
        $query = "select A.location_id,A.location_name,
        COALESCE(C.total_name_mismatch,0) as total_name_mismatch, 
        COALESCE(C.total_yet_to_be_action_pending,0) as total_yet_to_be_action_pending, 
        COALESCE(C.total_minor_yet_to_approved,0) as total_minor_yet_to_approved,
        COALESCE(C.total_minor_approved,0) as total_minor_approved,
        COALESCE(C.total_bank_yet_to_approved,0) as total_bank_yet_to_approved,
        COALESCE(C.total_bank_approved,0) as total_bank_approved,
        COALESCE(C.total_send_rejection,0) as total_send_rejection,
        COALESCE(C.total_request_approved,0) as total_request_approved,
        COALESCE(C.total_deactivated,0) as total_deactivated
        from(
            select block_code as location_id,'Block-'||block_name as location_name
            from public.m_block  " . $whereMain . " 
        )
        as A  
        LEFT JOIN
        (select
            count(distinct failed.ben_id)  as total_name_mismatch,
          count(distinct failed.ben_id) filter(WHERE failed.edited_status = 0  AND ben.is_eligible = true and ben.acc_validated=4) as total_yet_to_be_action_pending,
          count(1) filter(WHERE failed.failed_process_type = 1 AND failed.edited_status = 1 and ben.acc_validated=4) as total_minor_yet_to_approved,
          count(1) filter(WHERE failed.failed_process_type = 1 AND failed.edited_status = 2 and ben.acc_validated=2) as total_minor_approved,
          count(1) filter(WHERE failed.failed_process_type = 2 AND failed.edited_status = 1 and ben.acc_validated=4) as total_bank_yet_to_approved,
          count(1) filter(WHERE failed.failed_process_type = 2 AND failed.edited_status = 2 and ben.acc_validated=0) as total_bank_approved,
          count(1) filter(WHERE failed.failed_process_type = 3 AND failed.edited_status = 1 and ben.acc_validated=4) as total_send_rejection,
          count(1) filter(WHERE failed.failed_process_type = 3  AND failed.edited_status = 2 and ben.acc_validated=5) as total_request_approved,
          count(1) filter(WHERE failed.edited_status = 0 and ben.is_eligible = false) as total_deactivated,
        failed.local_body_code
            from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id where failed.scheme_id=" . $scheme_id . " AND ben.scheme_id=" . $scheme_id . " and  failed.dist_code= " . $district_code . " and ben.dist_code= " . $district_code . "  AND failed.failed_type= 2 AND ben.legacy_validation=0   
        group by failed.local_body_code) as C ON A.location_id=C.local_body_code";
      }
      if($failed_type == 2) {
        $query = "select A.location_id,A.location_name,
        COALESCE(C.total_account_mismatch,0) as total_account_mismatch, 
        COALESCE(C.total_yet_to_be_action_pending,0) as total_yet_to_be_action_pending, 
        COALESCE(C.total_yet_to_approved,0) as total_yet_to_approved,
        COALESCE(C.total_approved,0) as total_approved,
        COALESCE(C.total_deactivated,0) as total_deactivated
        from(
            select block_code as location_id,'Block-'||block_name as location_name
            from public.m_block  " . $whereMain . " 
        )
        as A  
        LEFT JOIN
        (select
        count(distinct failed.ben_id)  as total_account_mismatch,
          count(distinct failed.ben_id) filter(WHERE failed.edited_status = 0 AND ben.is_eligible = true and ben.acc_validated = 3) as total_yet_to_be_action_pending,
          count(1) filter(WHERE  failed.edited_status = 1 and ben.acc_validated = 3) as total_yet_to_approved,
          count(1) filter(WHERE  failed.edited_status = 2 ) as total_approved,
	   	  count(1) filter(WHERE  failed.edited_status = 0 and ben.is_eligible = false) as total_deactivated,
        failed.local_body_code
        from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id where failed.scheme_id=" . $scheme_id . " AND ben.scheme_id=" . $scheme_id . " and  failed.dist_code= " . $district_code . " and ben.dist_code= " . $district_code . " AND  failed.failed_type = 1  AND ben.legacy_validation = 0 
        group by failed.local_body_code) as C ON A.location_id=C.local_body_code";
      }

      $result = DB::connection('pgsql_paywrite')->select($query);
      return $result;
  }
  public function index_name(Request $request)
  {
      return redirect()->route('no-dup-verified-beneficiaries-list', ['type' => 11]);
  }

}
