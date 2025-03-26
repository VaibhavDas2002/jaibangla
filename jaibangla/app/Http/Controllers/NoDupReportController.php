<?php

namespace App\Http\Controllers;

use App\Configduty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AuthChecker;
use App\UrbanBody;
use App\Scheme;
use App\GP;
use App\District;
use App\Taluka;
use App\SubDistrict;
use App\Ward;
class NoDupReportController extends Controller
{
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
    }
    public function schemeIndex()
    {
        $is_active = 0;
        $user_id = Auth::user()->id;
        $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where  is_active=1 and user_id=" . $user_id . " )"));

        // $roleArray = $request->session()->get('role');
        $roleArray = Configduty::where('user_id', $user_id)->get()->toArray();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        if (AuthChecker::AdminChecker() || AuthChecker::HODChecker() || AuthChecker::HOPChecker() || AuthChecker::MisStateChecker() || AuthChecker::DashboardChecker()) {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if (AuthChecker::ApproverPermission()) {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                // dd($roleObj['district_code']);
                //   if ($roleObj['scheme_id'] == $this->scheme_id) {
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
                //   }
            }

            if (empty($district_code))
                return redirect("/")->with('success', 'User Disabled. ');
        } else {
            return redirect("/")->with('danger', 'User Disabled. ');
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
        // dd($ds_phase_list);
        return view(
            'NoDupReport.scheme-index',
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
                'scheme' => $scheme,
                'gpList' => $gpList,
                'muncList' => $muncList,
            ]
        );
    }

    public function schemereportPost(Request $request)
    {
        // dd($request->all());
        $heading_msg = '';
        $title = "";
        //$block_condition = "";
        $user_msg = "Scheme Based Incomplete Details MIS Report";
        $title = $user_msg;
        //dd($title);
        $data = array();
        $return_status = 1;
        $return_msg = '';
        $heading_msg = '';
        $is_address = 0;
        $is_address = 1;
        $column = "Scheme";
        $heading_msg = $user_msg;
        $user_id = Auth::user()->id;
        $dutyObj = Configduty::where('user_id', $user_id)->where('is_active', 1)->first();
        $district_code = $dutyObj->district_code;
        $data = $this->getSchemeBasedMisReport($scheme_id = (int) $request->scheme_id, $district_code);
        return response()->json([
            'return_status' => $return_status,
            'return_msg' => $return_msg,
            'row_data' => $data,
            'column' => $column,
            'title' => $title,
            'heading_msg' => $heading_msg
        ]);

    }

    public function getSchemeBasedMisReport($scheme_id, $district_code = null)
    {
        if (!empty($scheme_id)) {
            $where_con = ' and id = ' . $scheme_id;
        } else {
            $where_con = '';
        }
        if ($district_code == null) {
            $where_dist = '';
        } else {
            $where_dist = ' where ben.created_by_dist_code= ' . $district_code;
        }
        $query = "select A.scheme_id AS scheme_id ,
          A.scheme_name AS scheme_name,
          COALESCE(C.total_incomplete, 0::bigint) AS total_incomplete,
       COALESCE(C.total_no_aadhar, 0::bigint) AS total_no_aadhar,
        COALESCE(C.total_dup_aadhar, 0::bigint) AS total_dup_aadhar,
       COALESCE(C.total_no_mobile, 0::bigint) AS total_no_mobile,
        COALESCE(C.total_dup_mobile, 0::bigint) AS total_dup_mobile,
         COALESCE(C.total_dup_bank, 0::bigint) AS total_dup_bank,
          COALESCE(C.total_acc_failed, 0::bigint) AS total_acc_failed,
         COALESCE(C.total_name_failed, 0::bigint) AS total_name_failed,
          COALESCE(C.total_sbi_bank, 0::bigint) AS total_sbi_bank,
           COALESCE(C.total_rbi_bank, 0::bigint) AS total_rbi_bank,
            COALESCE(C.total_ifms_bank, 0::bigint) AS total_ifms_bank
        
      from(
      select m_scheme.id AS scheme_id,
                m_scheme.scheme_name AS scheme_name
            FROM public.m_scheme where is_active = 1  $where_con  ) as A  
      LEFT JOIN
      (
          SELECT ben.scheme_id,
          COUNT(1) FILTER (WHERE is_incomplete = 1 ) AS total_incomplete,
          COUNT(1) FILTER (WHERE no_aadhar = 1 ) AS total_no_aadhar,
      COUNT(1) FILTER (WHERE dup_aadhar = 1 ) AS total_dup_aadhar,
      COUNT(1) FILTER (WHERE no_mobile = 1 ) AS total_no_mobile,
      COUNT(1) FILTER (WHERE dup_mobile = 1 ) AS total_dup_mobile,
      COUNT(1) FILTER (WHERE dup_bank = 1 ) AS total_dup_bank,
      COUNT(1) FILTER (WHERE is_bank_failed = 3 ) AS total_acc_failed,
      COUNT(1) FILTER (WHERE is_bank_failed = 2 ) AS total_name_failed,
      COUNT(1) FILTER (WHERE  is_bank_failed = 1 and pay_validated = 3 ) AS total_sbi_bank,
      COUNT(1) FILTER (WHERE  is_bank_failed = 1 and pay_validated = 4 ) AS total_rbi_bank,
      COUNT(1) FILTER (WHERE  is_bank_failed = 1 and pay_validated = 5 ) AS total_ifms_bank
          from pension.beneficiaries ben  $where_dist
       group by ben.scheme_id) as C ON A.scheme_id=C.scheme_id ";
        $result = DB::connection('pgsql')->select($query);
        return $result;
    }


    public function noDupMisIndex(Request $request)
    {
        $is_active = 0;
        $user_id = Auth::user()->id;
        $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where  is_active=1 and user_id=" . $user_id . " )"));

        // $roleArray = $request->session()->get('role');
        $roleArray = Configduty::where('user_id', $user_id)->get()->toArray();
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        if (AuthChecker::AdminChecker() || AuthChecker::HODChecker() || AuthChecker::HOPChecker() || AuthChecker::MisStateChecker() || AuthChecker::DashboardChecker()) {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if (AuthChecker::ApproverPermission() || AuthChecker::VerifierPermission()) {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                // dd($roleObj['district_code']);
                //   if ($roleObj['scheme_id'] == $this->scheme_id) {
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
                //   }
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
        // dd($ds_phase_list);
        $incomplete_list = db::select("select * from m_incomplete_type where is_active = true");
        return view(
            'NoDupReport.incomplete-mis',
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
                'scheme' => $scheme,
                'gpList' => $gpList,
                'muncList' => $muncList,
                'incomplete_list' => $incomplete_list
            ]
        );
    }
    public function noDupMisPost(Request $request)
    {
        // dd($request->all());
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        $gp_ward = $request->gp_ward;
        $scheme_id = (int) $request->scheme_id;
        $incomplete_type = (int) $request->incomplete_type;
        if ($incomplete_type == 10) {
            $falled_type = (int) $request->failed_type;
        } else {
            $falled_type = NULL;
        }

        // $scheme_id = 3; 
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
            'district' => 'nullable|integer',
            'urban_code' => 'nullable|integer',
            'block' => 'nullable|integer',
            'muncid' => 'nullable|integer',
            'gp_ward' => 'nullable|integer',
        ];
        $data = array();
        $column = "";
        $attributes = array();
        $messages = array();
        $attributes['district'] = 'District';
        $attributes['urban_code'] = 'Rural/ Urban';
        $attributes['block'] = 'Block/Sub Division';
        $attributes['muncid'] = 'Municipality';
        $attributes['gp_ward'] = 'GP/Ward';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $user_msg = "Incomplete Details MIS Report";
            $title = $user_msg;
            //dd($title);

            $data = array();
            $return_status = 1;
            $return_msg = '';
            $heading_msg = '';
            $external = 0;
            $external_arr = array();
            $external_filter = array();
            $is_address = 0;
            if (!empty($gp_ward)) {
                if ($urban_code == 1) {
                    $is_address = 1;
                    $column = "Ward";
                    $heading_msg = $user_msg . ' of the Ward ' . $gp_ward_name;
                    $data = $this->getIncompleteReportWardWise($scheme_id, $district, $block, $muncid, $gp_ward, $incomplete_type, $falled_type);
                } else {
                    $is_address = 1;
                    $column = "GP";
                    $heading_msg = $user_msg . ' of the GP ' . $gp_ward_name;
                    $data = $this->getIncompleteReportGpWise($scheme_id, $district, $block, NULL, $gp_ward, $incomplete_type, $falled_type);
                }
            } else if (!empty($muncid)) {
                $is_address = 1;
                $column = "Ward";
                $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
                $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
                $data = $this->getIncompleteReportWardWise($scheme_id, $district, $block, $muncid, NULL, $incomplete_type, $falled_type);
            } else if (!empty($block)) {
                if ($urban_code == 1) {
                    $is_address = 1;
                    $column = "Municipality";
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getIncompleteReportMuncWise($scheme_id, $district, $block, NULL, NULL, $incomplete_type, $falled_type);
                } else if ($urban_code == 2) {
                    $is_address = 1;
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getIncompleteReportGpWise($scheme_id, $district, $block, NULL, $gp_ward, $incomplete_type, $falled_type);
                }
            } else {

                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getMonorMismatch90to100SubDivWise($scheme_id, $district, NULL, NULL, NULL, $incomplete_type, $falled_type);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getIncompleteReportBlockWise($scheme_id, $district, NULL, NULL, NULL, $incomplete_type, $falled_type);
                        //   dump($data);
                        //   die();
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getIncompleteReportBlockWise($scheme_id, $district, NULL, NULL, NULL, $incomplete_type, $falled_type);
                        $data2 = $this->getMonorMismatch90to100SubDivWise($scheme_id, $district, NULL, NULL, NULL, $incomplete_type);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getIncompleteReportDistrictWise($scheme_id, $district, NULL, NULL, NULL, $incomplete_type, $falled_type);
                    $external = 0;
                }
            }
            if ($is_address == 1) {
                $heading_msg = $heading_msg . "<span class='text-danger'> (According to Applicant’s Address)</span>";
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

    public function getIncompleteReportDistrictWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $incomplete_type = NULL, $failed_type = NULL)
    {
        //  dd($incomplete_type);

        // dd($district_code);
        if (!empty($scheme_id)) {
            $where_con = ' scheme_id = ' . $scheme_id;
        } else {
            $where_con = '';
        }
        if ($incomplete_type == 0) {
            $where_incomplete = 'AND is_incomplete = 1';
        } else if ($incomplete_type == 1) {
            $where_incomplete = 'AND dup_aadhar = 1';
        } else if ($incomplete_type == 2) {
            $where_incomplete = 'AND no_aadhar = 1';
        } else if ($incomplete_type == 3) {
            $where_incomplete = 'AND dup_bank = 1';
        } else if ($incomplete_type == 4) {
            $where_incomplete = 'AND dup_mobile = 1';
        } else if ($incomplete_type == 5) {
            $where_incomplete = 'AND no_mobile = 1';
        } else if ($incomplete_type == 10) {
            $where_incomplete = 'AND is_bank_failed IN (1)';
            if ($failed_type == 3) {
                $where_failed = 'AND pay_validated IN (3)';
            } else if ($failed_type == 4) {
                $where_failed = 'AND pay_validated IN (4)';
            } else if ($failed_type == 5) {
                $where_failed = 'AND pay_validated IN (5)';
            } else {
                $where_failed = '';
            }


        } else if ($incomplete_type == 11) {
            $where_incomplete = 'AND is_bank_failed IN (2)';
        } else if ($incomplete_type == 12) {
            $where_incomplete = 'AND is_bank_failed IN (3)';
        } else {
            $where_incomplete = '';
        }
        // dd($scheme_id);
        $query = "select A.location_id AS district_code,
          A.location_name as location_name,
          A.created_by_dist_code,
          COALESCE(C.yet_to_action, 0::bigint) AS yet_to_action,
          COALESCE(C.approval_pending, 0::bigint) AS approval_pending,
          COALESCE(C.approved, 0::bigint) AS approved
      from(
      select m_district.district_code AS location_id,
                m_district.district_name AS location_name,
                m_district.district_code AS created_by_dist_code
            FROM public.m_district ) as A  
      LEFT JOIN
      (
          SELECT ben.created_by_dist_code,
          COUNT(1) FILTER (WHERE next_level_clean_id is NULL ) AS yet_to_action,
          COUNT(1) FILTER (WHERE next_level_clean_id = 2 ) AS approval_pending,
          COUNT(1) FILTER (WHERE next_level_clean_id = 1 ) AS approved
          from pension.beneficiaries ben  where $where_con  $where_incomplete $where_failed
       group by ben.created_by_dist_code) as C ON A.location_id=C.created_by_dist_code";

        //    dd($query);
        $result = DB::connection('pgsql')->select($query);
        return $result;
    }
    public function getIncompleteReportBlockWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $incomplete_type, $failed_type)
    {
        // dd($district_code);
        if (!empty($scheme_id)) {
            $where_con = ' scheme_id = ' . $scheme_id;
        } else {
            $where_con = '';
        }
        $whereMain = " WHERE district_code =" . $district_code;
        if ($incomplete_type == null) {
            $where_incomplete = 'AND dup_bank = 1 OR no_aadhar = 1 OR dup_aadhar = 1 OR no_mobile = 1 OR dup_mobile = 1 OR is_bank_failed IN (1, 2, 3)';
        } else if ($incomplete_type == 0) {
            $where_incomplete = 'AND is_incomplete = 1';
        } else if ($incomplete_type == 1) {
            $where_incomplete = 'AND dup_aadhar = 1';
        } else if ($incomplete_type == 2) {
            $where_incomplete = 'AND no_aadhar = 1';
        } else if ($incomplete_type == 3) {
            $where_incomplete = 'AND dup_bank = 1';
        } else if ($incomplete_type == 4) {
            $where_incomplete = 'AND dup_mobile = 1';
        } else if ($incomplete_type == 5) {
            $where_incomplete = 'AND no_mobile = 1';
        } else if ($incomplete_type == 10) {
            $where_incomplete = 'AND is_bank_failed IN (1)';
            if ($failed_type == 3) {
                $where_failed = 'AND pay_validated IN (3)';
            } else if ($failed_type == 4) {
                $where_failed = 'AND pay_validated IN (4)';
            } else if ($failed_type == 5) {
                $where_failed = 'AND pay_validated IN (5)';
            } else {
                $where_failed = '';
            }
        } else if ($incomplete_type == 11) {
            $where_incomplete = 'AND is_bank_failed IN (2)';
        } else if ($incomplete_type == 12) {
            $where_incomplete = 'AND is_bank_failed IN (3)';
        } else {
            $where_incomplete = '';
        }
        // dd($scheme_id);
        $query = "Select A.location_id AS location_id,
          A.location_name AS location_name,
          A.created_by_local_body_code,
          COALESCE(C.total, 0::bigint) AS total,
          COALESCE(C.yet_to_action, 0::bigint) AS yet_to_action,
          COALESCE(C.approval_pending, 0::bigint) AS approval_pending,
          COALESCE(C.approved, 0::bigint) AS approved
      from(
     SELECT m_block.block_code AS location_id,
          m_block.block_name AS location_name,
          m_block.district_code AS created_by_local_body_code
        FROM public.m_block  $whereMain ) as A  
      LEFT JOIN
      (
          SELECT ben.created_by_local_body_code,
         COUNT(1) AS total,
          COUNT(1) FILTER (WHERE next_level_clean_id is NULL ) AS yet_to_action,
          COUNT(1) FILTER (WHERE next_level_clean_id = 2 ) AS approval_pending,
          COUNT(1) FILTER (WHERE next_level_clean_id = 1 ) AS approved
          from pension.beneficiaries ben  where $where_con $where_incomplete $where_failed
       group by ben.created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";

        $result = DB::connection('pgsql')->select($query);
        return $result;
    }
    public function getMonorMismatch90to100SubDivWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $incomplete_type = NULL, $failed_type = NULL)
    {
        // dd($district_code);
        if (!empty($scheme_id)) {
            $where_con = ' scheme_id = ' . $scheme_id;
        } else {
            $where_con = '';
        }
        $whereMain = " WHERE district_code =" . $district_code;
        if ($incomplete_type == null) {
            $where_incomplete = 'AND dup_bank = 1 OR no_aadhar = 1 OR dup_aadhar = 1 OR no_mobile = 1 OR dup_mobile = 1 OR is_bank_failed IN (1, 2, 3)';
        } else if ($incomplete_type == 0) {
            $where_incomplete = 'AND is_incomplete = 1';
        } else if ($incomplete_type == 1) {
            $where_incomplete = 'AND dup_aadhar = 1';
        } else if ($incomplete_type == 2) {
            $where_incomplete = 'AND no_aadhar = 1';
        } else if ($incomplete_type == 3) {
            $where_incomplete = 'AND dup_bank = 1';
        } else if ($incomplete_type == 4) {
            $where_incomplete = 'AND dup_mobile = 1';
        } else if ($incomplete_type == 5) {
            $where_incomplete = 'AND no_mobile = 1';
        } else if ($incomplete_type == 10) {
            $where_incomplete = 'AND is_bank_failed IN (1)';
            if ($failed_type == 3) {
                $where_failed = 'AND pay_validated IN (3)';
            } else if ($failed_type == 4) {
                $where_failed = 'AND pay_validated IN (4)';
            } else if ($failed_type == 5) {
                $where_failed = 'AND pay_validated IN (5)';
            } else {
                $where_failed = '';
            }
        } else if ($incomplete_type == 11) {
            $where_incomplete = 'AND is_bank_failed IN (2)';
        } else if ($incomplete_type == 12) {
            $where_incomplete = 'AND is_bank_failed IN (3)';
        } else {
            $where_incomplete = '';
        }
        // dd($scheme_id);
        $query = "select A.location_id AS location_id,
          A.location_name AS location_name,
          A.created_by_local_body_code,
          COALESCE(C.total, 0::bigint) AS total,
          COALESCE(C.yet_to_action, 0::bigint) AS yet_to_action,
          COALESCE(C.approval_pending, 0::bigint) AS approval_pending,
          COALESCE(C.approved, 0::bigint) AS approved
      from(
     SELECT m_sub_district.sub_district_code AS location_id,
              m_sub_district.sub_district_name AS location_name,
              m_sub_district.district_code AS created_by_local_body_code
          FROM public.m_sub_district  $whereMain ) as A  
      LEFT JOIN
      (
          SELECT ben.created_by_local_body_code,
         COUNT(1) AS total,
          COUNT(1) FILTER (WHERE next_level_clean_id is NULL ) AS yet_to_action,
          COUNT(1) FILTER (WHERE next_level_clean_id = 2 ) AS approval_pending,
          COUNT(1) FILTER (WHERE next_level_clean_id = 1 ) AS approved
          from pension.beneficiaries ben  where  $where_con $where_incomplete $where_failed 
       group by ben.created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";

        $result = DB::connection('pgsql')->select($query);
        return $result;
    }

    public function getIncompleteReportMuncWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $incomplete_type = NULL, $failed_type = NULL)
    {


        if (!empty($scheme_id)) {
            $where_con = ' scheme_id = ' . $scheme_id;
        } else {
            $where_con = '';
        }
        $whereMain = " WHERE  sub_district_code = $ulb_code AND district_code =" . $district_code;
        if ($incomplete_type == null) {
            $where_incomplete = 'AND dup_bank = 1 OR no_aadhar = 1 OR dup_aadhar = 1 OR no_mobile = 1 OR dup_mobile = 1 OR is_bank_failed IN (1, 2, 3)';
        } else if ($incomplete_type == 0) {
            $where_incomplete = 'AND is_incomplete = 1';
        } else if ($incomplete_type == 1) {
            $where_incomplete = 'AND dup_aadhar = 1';
        } else if ($incomplete_type == 2) {
            $where_incomplete = 'AND no_aadhar = 1';
        } else if ($incomplete_type == 3) {
            $where_incomplete = 'AND dup_bank = 1';
        } else if ($incomplete_type == 4) {
            $where_incomplete = 'AND dup_mobile = 1';
        } else if ($incomplete_type == 5) {
            $where_incomplete = 'AND no_mobile = 1';
        } else if ($incomplete_type == 10) {
            $where_incomplete = 'AND is_bank_failed IN (1)';
            if ($failed_type == 3) {
                $where_failed = 'AND pay_validated IN (3)';
            } else if ($failed_type == 4) {
                $where_failed = 'AND pay_validated IN (4)';
            } else if ($failed_type == 5) {
                $where_failed = 'AND pay_validated IN (5)';
            } else {
                $where_failed = '';
            }
        } else if ($incomplete_type == 11) {
            $where_incomplete = 'AND is_bank_failed IN (2)';
        } else if ($incomplete_type == 12) {
            $where_incomplete = 'AND is_bank_failed IN (3)';
        } else {
            $where_incomplete = '';
        }
        // dd($scheme_id);
        $query = "select A.location_id AS location_id,
         A.location_name AS location_name,
         A.created_by_local_body_code,
         COALESCE(C.total, 0::bigint) AS total,
         COALESCE(C.yet_to_action, 0::bigint) AS yet_to_action,
         COALESCE(C.approval_pending, 0::bigint) AS approval_pending,
         COALESCE(C.approved, 0::bigint) AS approved
     from(
    SELECT m_urban_body.urban_body_code AS location_id,
             m_urban_body.urban_body_name AS location_name,
             m_urban_body.district_code AS district_code,
             m_urban_body.sub_district_code AS created_by_local_body_code
         FROM public.m_urban_body  $whereMain ) as A  
     LEFT JOIN
     (
         SELECT ben.created_by_local_body_code,
        COUNT(1) AS total,
         COUNT(1) FILTER (WHERE next_level_clean_id is NULL ) AS yet_to_action,
         COUNT(1) FILTER (WHERE next_level_clean_id = 2 ) AS approval_pending,
         COUNT(1) FILTER (WHERE next_level_clean_id = 1 ) AS approved
         from pension.beneficiaries ben  where  $where_con $where_incomplete $where_failed 
      group by ben.created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";

        $result = DB::connection('pgsql')->select($query);
        return $result;
    }

    public function getIncompleteReportGpWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $incomplete_type = NULL, $failed_type = NULL)
    {
        // dd($district_code);
        // dump($district_code);
        // dump($ulb_code);
        // dump($block_ulb_code);
        // dump($gp_ward_code);
        // dump($incomplete_type);
        // dump($failed_type);

        if (!empty($scheme_id)) {
            $where_con = ' scheme_id = ' . $scheme_id;
        } else {
            $where_con = '';
        }

        $whereMain = " WHERE block_code = $ulb_code AND district_code =" . $district_code;

        if ($gp_ward_code != null) {
            $whereWard = " AND   gram_panchyat_code =" . $ulb_code;
        } else {
            $whereWard = '';
        }
        if ($incomplete_type == null) {
            $where_incomplete = 'AND dup_bank = 1 OR no_aadhar = 1 OR dup_aadhar = 1 OR no_mobile = 1 OR dup_mobile = 1 OR is_bank_failed IN (1, 2, 3)';
        } else if ($incomplete_type == 0) {
            $where_incomplete = 'AND is_incomplete = 1';
        } else if ($incomplete_type == 1) {
            $where_incomplete = 'AND dup_aadhar = 1';
        } else if ($incomplete_type == 2) {
            $where_incomplete = 'AND no_aadhar = 1';
        } else if ($incomplete_type == 3) {
            $where_incomplete = 'AND dup_bank = 1';
        } else if ($incomplete_type == 4) {
            $where_incomplete = 'AND dup_mobile = 1';
        } else if ($incomplete_type == 5) {
            $where_incomplete = 'AND no_mobile = 1';
        } else if ($incomplete_type == 10) {
            $where_incomplete = 'AND is_bank_failed IN (1)';
            if ($failed_type == 3) {
                $where_failed = 'AND pay_validated IN (3)';
            } else if ($failed_type == 4) {
                $where_failed = 'AND pay_validated IN (4)';
            } else if ($failed_type == 5) {
                $where_failed = 'AND pay_validated IN (5)';
            } else {
                $where_failed = '';
            }
        } else if ($incomplete_type == 11) {
            $where_incomplete = 'AND is_bank_failed IN (2)';
        } else if ($incomplete_type == 12) {
            $where_incomplete = 'AND is_bank_failed IN (3)';
        } else {
            $where_incomplete = '';
        }
        // dd($scheme_id);
        $query = "select A.location_id AS location_id,
     A.location_name AS location_name,
     A.block_code,
     COALESCE(C.total, 0::bigint) AS total,
     COALESCE(C.yet_to_action, 0::bigint) AS yet_to_action,
     COALESCE(C.approval_pending, 0::bigint) AS approval_pending,
     COALESCE(C.approved, 0::bigint) AS approved
 from(
SELECT m_gp.gram_panchyat_code AS location_id,
             m_gp.gram_panchyat_name AS location_name,
             m_gp.block_code AS block_code,
             m_gp.district_code AS district_code
         FROM public.m_gp  $whereMain $whereWard ) as A  
 LEFT JOIN
 (
     SELECT ben.gp_ward_code,
    COUNT(1) AS total,
     COUNT(1) FILTER (WHERE next_level_clean_id is NULL ) AS yet_to_action,
     COUNT(1) FILTER (WHERE next_level_clean_id = 2 ) AS approval_pending,
     COUNT(1) FILTER (WHERE next_level_clean_id = 1 ) AS approved
     from pension.beneficiaries ben  where  $where_con $where_incomplete $where_failed 
  group by ben.gp_ward_code) as C ON A.location_id=C.gp_ward_code";

        $result = DB::connection('pgsql')->select($query);
        return $result;
    }

    public function getIncompleteReportWardWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $incomplete_type = NULL, $failed_type = NULL)
    {
        // dd($district_code);
        if (!empty($scheme_id)) {
            $where_con = ' scheme_id = ' . $scheme_id;
        } else {
            $where_con = '';
        }
        $whereMain = " WHERE urban_body_code = " . $block_ulb_code;

        if ($gp_ward_code != null) {
            $whereWard = " AND   urban_body_ward_code =" . $gp_ward_code;
        } else {
            $whereWard = '';
        }
        if ($incomplete_type == null) {
            $where_incomplete = 'AND dup_bank = 1 OR no_aadhar = 1 OR dup_aadhar = 1 OR no_mobile = 1 OR dup_mobile = 1 OR is_bank_failed IN (1, 2, 3)';
        } else if ($incomplete_type == 0) {
            $where_incomplete = 'AND is_incomplete = 1';
        } else if ($incomplete_type == 1) {
            $where_incomplete = 'AND dup_aadhar = 1';
        } else if ($incomplete_type == 2) {
            $where_incomplete = 'AND no_aadhar = 1';
        } else if ($incomplete_type == 3) {
            $where_incomplete = 'AND dup_bank = 1';
        } else if ($incomplete_type == 4) {
            $where_incomplete = 'AND dup_mobile = 1';
        } else if ($incomplete_type == 5) {
            $where_incomplete = 'AND no_mobile = 1';
        } else if ($incomplete_type == 10) {
            $where_incomplete = 'AND is_bank_failed IN (1)';
            if ($failed_type == 3) {
                $where_failed = 'AND pay_validated IN (3)';
            } else if ($failed_type == 4) {
                $where_failed = 'AND pay_validated IN (4)';
            } else if ($failed_type == 5) {
                $where_failed = 'AND pay_validated IN (5)';
            } else {
                $where_failed = '';
            }
        } else if ($incomplete_type == 11) {
            $where_incomplete = 'AND is_bank_failed IN (2)';
        } else if ($incomplete_type == 12) {
            $where_incomplete = 'AND is_bank_failed IN (3)';
        } else {
            $where_incomplete = '';
        }
        // dd($scheme_id);
        $query = "select A.location_id AS location_id,
     A.location_name AS location_name,
     A.urban_body_code,
     COALESCE(C.total, 0::bigint) AS total,
     COALESCE(C.yet_to_action, 0::bigint) AS yet_to_action,
     COALESCE(C.approval_pending, 0::bigint) AS approval_pending,
     COALESCE(C.approved, 0::bigint) AS approved
 from(
SELECT m_urban_body_ward.urban_body_ward_code AS location_id,
             m_urban_body_ward.urban_body_ward_name AS location_name,
             m_urban_body_ward.urban_body_code AS urban_body_code
         FROM public.m_urban_body_ward  $whereMain $whereWard ) as A  
 LEFT JOIN
 (
     SELECT ben.gp_ward_code,
    COUNT(1) AS total,
     COUNT(1) FILTER (WHERE next_level_clean_id is NULL ) AS yet_to_action,
     COUNT(1) FILTER (WHERE next_level_clean_id = 2 ) AS approval_pending,
     COUNT(1) FILTER (WHERE next_level_clean_id = 1 ) AS approved
     from pension.beneficiaries ben  where  $where_con $where_incomplete $where_failed 
  group by ben.gp_ward_code) as C ON A.location_id=C.gp_ward_code";

        $result = DB::connection('pgsql')->select($query);
        return $result;
    }



}
