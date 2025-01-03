<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configduty;
use App\District;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use App\GP;
use Illuminate\Support\Facades\DB;
use App\Scheme;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\AuthChecker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class accountValidationMISController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    function index(Request $request){
        $user_id = AuthChecker::getUserId();
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1 and scheme_id in(2,10,11))"));
        $c_time = Carbon::now();
        $c_date = $c_time->format("Y-m-d");
        $scheme_name = '';
        $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
        $designation_id = Auth::user()->designation_id;
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        if ($designation_id == 'Admin' || $designation_id == 'HOD' || $designation_id == 'HOP' || $designation_id == 'MisState' ||  $designation_id == 'Dashboard') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if ($designation_id == 'Approver') {
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
            'accountValidationMIS.index',
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
    public function getData(Request $request){
         //$ds_phase_list = Config::get('constants.ds_phase.phaselist');
         $scheme_id = $request->scheme_id;
         $failed_type =$request->failed_type;
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
                     $data = $this->getSubDivWise($scheme_id, $scheme_name,$tablename,$failed_type,$district,$block);
                 } else if ($urban_code == 2) {
                     $block_arr = Taluka::where('block_code', '=', $block)->first();
                     $column = "Block";
                     $heading_msg = 'Block  ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                     $data = $this->getBlockWise($scheme_id, $scheme_name,$tablename,$failed_type,$district,$block);
                 }
             } else {
 
                 if (!empty($district)) {
                     if ($urban_code == 1) {
                         $column = "Sub Division";
                         $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                         $data = $this->getSubDivWise($scheme_id, $scheme_name,$tablename,$failed_type,$district,NULL);
                     } else if ($urban_code == 2) {
                         $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                         $column = "Block";
                         $data = $this->getBlockWise($scheme_id, $scheme_name,$tablename,$failed_type,$district,NULL);
                     } else {
                         $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                         $column = "Block/Sub Division";
                         $data1 = $this->getBlockWise($scheme_id, $scheme_name,$tablename,$failed_type,$district,NULL);
                         $data2 = $this->getSubDivWise($scheme_id, $scheme_name,$tablename,$failed_type,$district,NULL);
                         $data = array_merge($data1, $data2);
                     }
                 } else {
                     $column = "District";
                     $heading_msg = 'District Wise ' . $user_msg;
                     $data = $this->getDistrictWise($scheme_id, $scheme_name,$tablename,$failed_type);
 
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
    public function getDistrictWise($scheme_id = NULL,$scheme_name = NULL, $tablename = NULL,$failed_type)
    {
        if($failed_type == 1){
            $acc_validated = 3;
            $failed_type_id = 1;
        }
        if($failed_type == 2){
            $acc_validated = 4;
            $failed_type_id = 2;
        }
         $whereCon = "where failed.scheme_id=".$scheme_id;
        // $innerPartQuery = $this->getSchemeWiseSelectAgeGroup($scheme_id, $tablename);
        $query = "select main.location_id,main.location_name,COALESCE(dup.pending_below_1,0) as pending_below_1,
        COALESCE(dup.pending_between_1_3,0) as pending_between_1_3,COALESCE(dup.pending_above_3,0) as pending_above_3
        from
        (
        select district_code as location_id,district_name as location_name
        from public.m_district 
        ) as main LEFT JOIN
        (select  COUNT(1) FILTER (WHERE failed.created_at >= CURRENT_DATE - INTERVAL '1 month') AS pending_below_1,
                COUNT(1) FILTER (WHERE failed.created_at >= CURRENT_DATE - INTERVAL '3 months' AND failed.created_at < CURRENT_DATE - INTERVAL '1 month') AS pending_between_1_3,
                COUNT(1) FILTER (WHERE failed.created_at < CURRENT_DATE - INTERVAL '3 months') AS pending_above_3,
                failed.dist_code 
        from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id and failed.scheme_id=ben.scheme_id and ben.acc_validated = ".$acc_validated." and failed.failed_type =".$failed_type_id." and failed.edited_status =0 and ben.is_eligible = true and ben.ben_status = 1 " . $whereCon . "  
        group by failed.dist_code
        ) as dup ON main.location_id=dup.dist_code
        order by main.location_name";

        //   echo $query;die;
        $result = DB::connection('pgsql_paywrite')->select($query);
        return $result;
    }
    public function getBlockWise($scheme_id = NULL,$scheme_name = NULL, $tablename = NULL,$failed_type,$district_code,$block_code=NULL)
    {
        if($failed_type == 1){
            $acc_validated = 3;
            $failed_type_id = 1;
        }
        if($failed_type == 2){
            $acc_validated = 4;
            $failed_type_id = 2;
        }
        $whereCon = "where failed.scheme_id=".$scheme_id." and failed.dist_code=".$district_code;
        $whereMain = "where  district_code=" . $district_code;
        if(!empty($block_code)){
            $whereMain .= " and block_code=".$block_code;
            $whereCon .= " and failed.local_body_code=".$block_code;
        }

        // $innerPartQuery = $this->getSchemeWiseSelectAgeGroup($scheme_id, $tablename);
        $query = "select main.location_id,main.location_name,COALESCE(dup.pending_below_1,0) as pending_below_1,
        COALESCE(dup.pending_between_1_3,0) as pending_between_1_3,COALESCE(dup.pending_above_3,0) as pending_above_3
        from
        (
        select block_code as location_id,'Block-'||block_name as location_name
        from public.m_block " . $whereMain . " 
        ) as main LEFT JOIN
        (select  COUNT(1) FILTER (WHERE failed.created_at >= CURRENT_DATE - INTERVAL '1 month') AS pending_below_1,
                COUNT(1) FILTER (WHERE failed.created_at >= CURRENT_DATE - INTERVAL '3 months' AND failed.created_at < CURRENT_DATE - INTERVAL '1 month') AS pending_between_1_3,
                COUNT(1) FILTER (WHERE failed.created_at < CURRENT_DATE - INTERVAL '3 months') AS pending_above_3,
                failed.local_body_code 
        from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id and failed.scheme_id=ben.scheme_id and ben.acc_validated =".$acc_validated." and failed.failed_type =".$failed_type_id." and failed.edited_status =0 and ben.is_eligible = true and ben.ben_status = 1 " . $whereCon . "
        group by failed.local_body_code
        ) as dup ON main.location_id=dup.local_body_code
        order by main.location_name";
        // echo $query;die;
       $result = DB::connection('pgsql_paywrite')->select($query);
       return $result;
    }
    public function getSubDivWise($scheme_id = NULL,$scheme_name = NULL, $tablename = NULL,$failed_type,$district_code,$sdo_code=NULL)
    {
        if($failed_type == 1){
            $acc_validated = 3;
            $failed_type_id = 1;
        }
        if($failed_type == 2){
            $acc_validated = 4;
            $failed_type_id = 2;
        }
        $whereCon = "where failed.scheme_id=".$scheme_id." and failed.dist_code=".$district_code;
        $whereMain = "where  district_code=" . $district_code;
        if(!empty($sdo_code)){
            $whereMain .= " and sub_district_code=".$sdo_code;
            $whereCon .= " and failed.local_body_code=".$sdo_code;
        }

        // $innerPartQuery = $this->getSchemeWiseSelectAgeGroup($scheme_id, $tablename);
        $query = "select main.location_id,main.location_name,COALESCE(dup.pending_below_1,0) as pending_below_1,
        COALESCE(dup.pending_between_1_3,0) as pending_between_1_3,COALESCE(dup.pending_above_3,0) as pending_above_3
        from
        (
        select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
        from public.m_sub_district " . $whereMain . " 
        ) as main LEFT JOIN
        (select  COUNT(1) FILTER (WHERE failed.created_at >= CURRENT_DATE - INTERVAL '1 month') AS pending_below_1,
                COUNT(1) FILTER (WHERE failed.created_at >= CURRENT_DATE - INTERVAL '3 months' AND failed.created_at < CURRENT_DATE - INTERVAL '1 month') AS pending_between_1_3,
                COUNT(1) FILTER (WHERE failed.created_at < CURRENT_DATE - INTERVAL '3 months') AS pending_above_3,
                failed.local_body_code 
        from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id and failed.scheme_id=ben.scheme_id and ben.acc_validated =".$acc_validated." and failed.failed_type = ".$failed_type_id." and failed.edited_status = 0 and ben.is_eligible = true and ben.ben_status = 1 " . $whereCon . "
        group by failed.local_body_code
        ) as dup ON main.location_id=dup.local_body_code
        order by main.location_name";

        // echo $query;die;
       $result = DB::connection('pgsql_paywrite')->select($query);
       return $result;
    }
    public function downloadExcel(Request $request)
    {
        //   dd($request->all());
        $scheme_id = $request->scheme_id;
        $json_data = $request->beneficiaryList;
        $parts = explode("_", $json_data);
        $type = $parts[0];
        $type_code = $parts[1];
        $failed_type = $parts[2];
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $c_time = Carbon::now();
        $c_date =$c_time->format('M d Y');
        if($failed_type == 1){
            $acc_validated = 3;
            $failed_type_id = 1;
        }
        if($failed_type == 2){
            $acc_validated = 4;
            $failed_type_id = 2;
        }
        $schemes = Scheme::select('id', 'scheme_name')
                ->where('id', '=', $scheme_id)
                ->first();        
        $ben[] = array('Beneficiary Id', 'Beneficiary Name', 'District Name', 'Mobile No.', 'IFSC', 'Account Number', 'Caste', 'Gender');
        if($type == 1){
            $query = "select ben.ben_id,ben.ben_name,d.district_name,ben.mobile_no,ben.last_ifsc,ben.last_accno,ben.caste,ben.gender from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id join m_district d ON failed.dist_code = d.district_code WHERE failed.created_at >= CURRENT_DATE - INTERVAL '1 month' and ben.acc_validated =".$acc_validated." and ben.is_eligible = true and failed.failed_type =".$failed_type_id."  and failed.edited_status = 0 and ben.ben_status = 1 and failed.scheme_id =".$scheme_id;
            $type_msg ="Correction Pending Below 1 Months";
        }else if($type == 2){
            $query = "select ben.ben_id,ben.ben_name,d.district_name,ben.mobile_no,ben.last_ifsc,ben.last_accno,ben.caste,ben.gender from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id join m_district d ON failed.dist_code = d.district_code	 WHERE failed.created_at >= CURRENT_DATE - INTERVAL '3 months' AND failed.created_at < CURRENT_DATE - INTERVAL '1 month' and ben.acc_validated = ".$acc_validated." and ben.is_eligible = true and failed.edited_status =0 and failed.failed_type = ".$failed_type_id." and ben.ben_status = 1 and failed.scheme_id =".$scheme_id;
            $type_msg ="Correction Pending Between 1-3 Months";
        }else if($type == 3){
            $query = "select ben.ben_id,ben.ben_name,d.district_name,ben.mobile_no,ben.last_ifsc,ben.last_accno,ben.caste,ben.gender from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id join m_district d ON failed.dist_code = d.district_code WHERE failed.created_at < CURRENT_DATE - INTERVAL '3 months' and ben.acc_validated = ".$acc_validated." and ben.is_eligible = true and failed.failed_type = ".$failed_type_id." and  ben.ben_status = 1 and failed.edited_status =0 and failed.scheme_id =".$scheme_id;
            $type_msg ="Correction Pending Above 3 Months";
        }
        if($district == NULL){
            $query .= " and ben.dist_code = " . $type_code . "";
            $query .= " and failed.dist_code = " . $type_code . "";
        }else{
            $query .= " and ben.local_body_code = " . $type_code . "";
            $query .= " and failed.local_body_code = " . $type_code . "";

        }
        //   dd($query);
        $result = DB::connection('pgsql_paywrite')->select($query);
        foreach ($result as $data) {
            
            $ben[] = array(
                'Beneficiary Id' => $data->ben_id,
                'Beneficiary Name' => $data->ben_name,
                'District Name' => $data->district_name,
                'Mobile' => $data->mobile_no,
                'IFSC' => $data->last_ifsc,
                'Account Number' => $data->last_accno,
                'Caste' => $data->caste,
                'Gender' => $data->gender,
            );
        }
        $fileName = 'Account_validation_list_beneficiary_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return Excel::create($fileName, function ($excel) use ($ben,$schemes,$c_date,$type_msg) {
            $excel->sheet('Payment Success', function ($sheet) use ($ben,$schemes,$c_date,$type_msg) {
                $message = 'Account Validation/Bank Transaction Failed '.$type_msg.' Report for  of Scheme '.$schemes->scheme_name.' on- '.$c_date.'';
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', $message);
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->fromArray($ben, null, 'A2', false, false);
            });
        })->download('xlsx');
    }
}
