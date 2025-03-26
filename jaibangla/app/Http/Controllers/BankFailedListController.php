<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GP;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use App\BeneficiaryPensions;
use App\BenDocsSc;
use App\BenDocsSt;
use App\DocumentType;
use App\Configduty;
use App\MapLavel;
use App\Scheme;
use App\District;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use App\BankDetails;
use App\Helpers\DupCheck;
use Maatwebsite\Excel\Facades\Excel;
class BankFailedListController extends Controller
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
            $sObj = Scheme::select('id', 'short_code')
                ->where('id', '=', $scheme_id)
                ->first();
            //$parameter['scheme_id'] = $scheme_id;
            $schema_name = $sObj->short_code;
            //dd($schema_name);
            if (empty($schema_name)) {
                $schema_name = 'pension';
            }
            $table_name = strtolower($schema_name) . '.beneficiary';
        } else {
            $table_name = 'pension.beneficiary';
        }
        return $table_name;
    }
    public function index(Request $request){
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        // echo '<pre>'; print_r($roleArray);die();
        $designation_id = Auth::user()->designation_id;
        $user_id = Auth::user()->id;
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")  order by rank"));
        // echo '<pre>';print_r($schemes);die();
        if ($designation_id == 'Admin' || $designation_id == 'HOD' || $designation_id == 'HOP' || $designation_id == 'MisState' ||  $designation_id == 'Dashboard') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if ($designation_id == 'Approver' || $designation_id == 'Delegated Approver') {
            // echo 1;die();
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                // echo $designation_id;die();
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
            'failed-bank-edit/failed_bank_list',
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
    public function failedGetData(Request $request){
        $user_id = Auth::user()->id;
        $designation = Auth::user()->designation_id;
        $district_code = $request->district;
        $failed_type = $request->failed_type;
        $scheme_id = $request->scheme_code;
        $urban_code = $request->urban_code;
        $block = $request->block;
        // $gp_ward = $request->gp_ward;
        $muncid = $request->muncid;
        $pay_validated = in_array($failed_type, [3, 4, 5]) ? $failed_type : null; 
        if ($request->ajax()) {
            $base_query = " SELECT * FROM (SELECT T.* FROM (
                SELECT ben_id, MAX(created_at) AS max_created_at FROM payment.failed_payment_details 
                WHERE failed_type = $failed_type AND edited_status in(0,1) GROUP BY ben_id
                ) AS S 
                JOIN payment.failed_payment_details AS T ON S.ben_id = T.ben_id AND S.max_created_at = T.created_at
            ) AS f
            JOIN payment.ben_payment_details AS b ON f.ben_id = b.ben_id
            LEFT JOIN (
                SELECT urban_body_code AS block_ulb_code, urban_body_name AS block_ulb_name FROM public.m_urban_body
                UNION ALL
                SELECT block_code AS block_ulb_code, block_name AS block_ulb_name FROM public.m_block
            ) AS bu ON bu.block_ulb_code = b.block_ulb_code
            LEFT JOIN (
            SELECT gram_panchyat_code AS gp_ward_code, gram_panchyat_name AS gp_ward_name FROM public.m_gp
            UNION ALL
            SELECT urban_body_ward_code AS gp_ward_code, urban_body_ward_name AS gp_ward_name FROM public.m_urban_body_ward
        ) AS gw ON gw.gp_ward_code = b.gp_ward_code
        WHERE f.scheme_id = $scheme_id AND f.edited_status in(0,1) AND b.ben_status = 1 AND b.is_eligible = TRUE 
        AND b.pay_validated = $pay_validated
        AND b.is_rejected = 0 
        AND f.failed_type = $failed_type";   
        $extra_condition = "";

        if (empty($block) && !empty($muncid)) {
            $extra_condition = " AND f.local_body_code = $muncid AND b.local_body_code = $muncid";
        } elseif (!empty($block) && empty($muncid)) {
            $extra_condition = " AND f.local_body_code = $block AND b.local_body_code = $block";
        } elseif (empty($block) && empty($muncid) && !empty($urban_code)) {
            $extra_condition = " AND f.dist_code = $district_code AND b.dist_code = $district_code AND b.rural_urban_id = $urban_code";
        }else if(empty($block) && empty($muncid) && empty($urban_code)){
            $extra_condition = " AND f.dist_code = $district_code AND b.dist_code = $district_code";
        }
        $query = $base_query . $extra_condition . " ;"; 
        //   dd($query);      
            $result = DB::connection('pgsql_paywrite')->select($query);
            // print_r($result);die();
            // dd($result);
            return datatables()->of($result)
            ->addColumn('mobile_no', function ($result) {
                return $result->mobile_no;
            })
            ->addColumn('account_no', function ($result) {
                return $result->last_accno;
            })
            ->addColumn('bank_ifsc', function ($result) {
                return $result->last_ifsc;
            })
            ->addColumn('status', function ($result) {
                $status = '';
                if ($result->edited_status == 0) {
                    $status = '<span class="label label-success">Verification Pending</span>';
                } else if ($result->edited_status == 1) {
                    $status = '<span class="label label-danger">Approval Pending</span>';
                }
                return $status;
            })
            ->rawColumns(['mobile_no', 'account_no', 'bank_ifsc', 'status'])
            ->make(true);
        }
    }
    public function excelDownload(Request $request)
    {
        //  dd($request->all());
        $dist_code = $request->district;
        $scheme_id = $request->scheme_id;
        $failed_type = $request->failed_type;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $gp_ward = $request->gp_ward;
        $muncid = $request->muncid;
        $schemeObj = Scheme::where('id',$scheme_id)->first();
        if($failed_type == 3){
            $user_msg = 'SBI Failed List';
        }
        if($failed_type == 4){
            $user_msg = 'RBI Failed List';
        }
        if ($failed_type == 5) {
            $user_msg = 'IFMS Failed List';
        }
        $pay_validated = in_array($failed_type, [3, 4, 5]) ? $failed_type : null; 
        $base_query = " SELECT * FROM (SELECT T.* FROM (
                SELECT ben_id, MAX(created_at) AS max_created_at FROM payment.failed_payment_details 
                WHERE failed_type = $failed_type AND edited_status in(0,1) GROUP BY ben_id
                ) AS S 
                JOIN payment.failed_payment_details AS T ON S.ben_id = T.ben_id AND S.max_created_at = T.created_at
            ) AS f
            JOIN payment.ben_payment_details AS b ON f.ben_id = b.ben_id
            LEFT JOIN (
                SELECT urban_body_code AS block_ulb_code, urban_body_name AS block_ulb_name FROM public.m_urban_body
                UNION ALL
                SELECT block_code AS block_ulb_code, block_name AS block_ulb_name FROM public.m_block
            ) AS bu ON bu.block_ulb_code = b.block_ulb_code
            LEFT JOIN (
            SELECT gram_panchyat_code AS gp_ward_code, gram_panchyat_name AS gp_ward_name FROM public.m_gp
            UNION ALL
            SELECT urban_body_ward_code AS gp_ward_code, urban_body_ward_name AS gp_ward_name FROM public.m_urban_body_ward
        ) AS gw ON gw.gp_ward_code = b.gp_ward_code
        WHERE f.scheme_id = $scheme_id AND f.edited_status in(0,1) AND b.ben_status = 1 AND b.is_eligible = TRUE 
        AND b.pay_validated = $pay_validated
        AND b.is_rejected = 0 
        AND f.failed_type = $failed_type";   
        $extra_condition = "";

        if (empty($block) && !empty($muncid)) {
            $extra_condition = " AND f.local_body_code = $muncid AND b.local_body_code = $muncid";
        } elseif (!empty($block) && empty($muncid)) {
            $extra_condition = " AND f.local_body_code = $block AND b.local_body_code = $block";
        } elseif (empty($block) && empty($muncid) && !empty($urban_code)) {
            $extra_condition = " AND f.dist_code = $dist_code AND b.dist_code = $dist_code AND b.rural_urban_id = $urban_code";
        }else if(empty($block) && empty($muncid) && empty($urban_code)){
            $extra_condition = " AND f.dist_code = $dist_code AND b.dist_code = $dist_code";
        }
        $query = $base_query . $extra_condition . " ;"; 
        $result = DB::connection('pgsql_paywrite')->select($query);
        // dd($result);
        $excelarr[] = array(
            'Beneficiary ID', 'Beneficiary Name','Block/Municipality', 'GP/Ward', 'Account No', 'Bank IFSC', 'Mobile Number','Status'
        );
        
        foreach ($result as $arr) {
            // echo 1;die();
            $excelarr[] = array(
                'Beneficiary ID' => trim($arr->ben_id),
                'Beneficiary Name' => trim($arr->ben_name),
                'Block/Municipality' => trim($arr->block_ulb_name),
                'GP/Ward' => trim($arr->gp_ward_name),
                'Account No' => trim($arr->last_accno),
                'Bank IFSC' => trim($arr->last_ifsc),
                'Mobile Number' => trim($arr->mobile_no),
                'status' => ($arr->edited_status == 0) ? 'Verification Pending' : (($arr->edited_status == 1) ? 'Approval Pending' : '')
            );
        }
        // print_r($excelarr);die();
        $file_name = $schemeObj->scheme_name.' '.$user_msg .' '.  date('d/m/Y');
        Excel::create($file_name, function ($excel) use ($excelarr) {
            $excel->setTitle('Jai Bangla Failed List');
            $excel->sheet('Jai Bangla Failed List', function ($sheet) use ($excelarr) {
                $sheet->fromArray($excelarr, null, 'A1', false, false);
            });
        })->download('xlsx');
    }
}

