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
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\AuthChecker;

class BankduplicateBenListController extends Controller
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
            $table_name =  'pension.beneficiary';
        }
        return $table_name;
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
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")  order by rank"));
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
            'bank-duplicate-list/index',
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
    public function benList(Request $request)
    {
        //  dd($request->all());
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")  order by rank"));
        if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
        } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
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
            $query = "SELECT b.id, 
            CONCAT(trim(b.ben_fname),' ', trim(b.ben_mname),' ',trim(b.ben_lname)) AS name, 
            d.district_name,
            trim(block_ulb_name) AS block_name,
            trim(gp_ward_name) AS gp, 
            b.mobile_no as mobile_no, 
            b.bank_code as account_no, 
            b.bank_ifsc as bank_ifsc,
            b.aadhar_no as aadhar_no,
            b.next_level_role_id,
            b.is_approved
            FROM pension.ben_payment_details_bank_code_dup b 
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
                    $query .= " AND b.next_level_role_id =-97 AND  b.is_approved in(0,2)";
                }
                elseif($filter == '2') {
                    $query .= " AND b.next_level_role_id in(101,200) AND b.is_approved = 0";
                }
                elseif ($filter == '3') {
                    $query .= " AND b.next_level_role_id in(101,200) AND b.is_approved = 1 ";
                }
                elseif ($filter == '4') {
                    $query .= " AND b.next_level_role_id in(-200) AND b.is_approved = 1";
                } 
            }
            $query .= " order by b.bank_code";
            // if (!empty($dist_code)) {
            //     $query .= " AND b.dist_code = " . $dist_code;
            // }
            // if (!empty($block)) {
            //     $query .= " AND b.block_ulb_code = " . $block;
            // }
            // if (!empty($gp_ward)) {
            //     $query .= " AND b.gp_ward_code = " . $gp_ward;
            // }
            // if (!empty($muncid)) {
            //     $query .= " AND b.block_ulb_code = " . $muncid;
            // }
            // $query .= " Limit 1";
            //  echo $query;die();
            $result = DB::connection('pgsql_mis')->select($query);
            // print_r($result);die();
            // dd($result);
            return datatables()->of($result)
            ->addColumn('mobile_no', function ($result) {
                return $result->mobile_no;
            })
            ->addColumn('account_no', function ($result) {
               
               return trim($result->account_no);
               
            })
            ->addColumn('bank_ifsc', function ($result) {
                return trim($result->bank_ifsc);
            })
            ->addColumn('status', function ($result) {
                $html = '';
                if($result->next_level_role_id == 101 && $result->is_approved ==1){
                    $html = '<span class="text-dafault"><b>Process with Different</b></span>';
                }else if($result->next_level_role_id == 200 && $result->is_approved == 1){
                    $html = '<span class="text-dafault"><b>Process with Same</b></span>';
                }else if($result->next_level_role_id == 101 && $result->is_approved == 0){
                    $html = '<span class="text-dafault"><b>Process with Different</b></span>';
                }else if($result->next_level_role_id == 200 && $result->is_approved == 0){
                    $html = '<span class="text-dafault"><b>Process with Same</b></span>';
                }else if($result->next_level_role_id == -200 && $result->is_approved == 1){
                    $html = '<span class="text-dafault"><b>Rejected</b></span>';
                }else if($result->next_level_role_id == -97 && ($result->is_approved == 0 || $result->is_approved == 2)){
                    $html = '<span class="text-dafault"><b> </b></span>';
                }
                return $html;
            })
            ->rawColumns(['status'])
            ->make(true);
        }
    }
    public function duplicateExportExcel(Request $request)
    {
        //  dd($request->all());
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = AuthChecker::getUserId();
        $scheme_id = $request->scheme_id;
        if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
            $district_code = NULL;
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
        $filter = $request->search_for;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $gp_ward = $request->gp_ward;
        $muncid = $request->muncid;
        $table_name = $this->getSchemaName($scheme_id);
        $schemeObj = Scheme::where('id',$scheme_id)->first();
        $user_msg = 'Correction Pending Bank Account De duplication Beneficiary List On';
        // echo $scheme_id;die();
        $query = "SELECT b.id, CONCAT(trim(b.ben_fname),' ', trim(b.ben_mname),' ',trim(b.ben_lname)) AS name, 
        d.district_name,
        trim(block_ulb_name) AS block_name,
        trim(gp_ward_name) AS gp,  
        b.mobile_no as mobile_no, 
        b.bank_code as account_no, 
        b.bank_ifsc as bank_ifsc,
        b.next_level_role_id,
        b.is_approved
        FROM pension.ben_payment_details_bank_code_dup b 
                LEFT JOIN public.m_district d ON b.dist_code = d.district_code
                WHERE b.scheme_id = ".$scheme_id;
        if (!empty($dist_code)) {
                    $query .= " AND b.created_by_dist_code = " . $dist_code;
        }
        if(!empty($urban_code)){
            $query .= " AND ( b.rural_urban_id = " .$urban_code . " or b.rural_urban_id is null)";
            if($urban_code == 1){
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
        if (!empty($filter)) {
            if ($filter == '1') {
                $query .= " AND b.next_level_role_id = -97 AND b.is_approved in(0,2)";
            } 
            if($filter == '2') {
                $query .= " AND b.next_level_role_id in(101,200) AND b.is_approved = 0";
            }
            if ($filter == '3') {
                $query .= " AND b.next_level_role_id in(101,200) AND b.is_approved = 1 ";
            }
            if ($filter == '4') {
                $query .= " AND b.next_level_role_id in(-200) AND b.is_approved = 1";
            }
        }
        $query .= " order by b.bank_code";
        $result = DB::connection('pgsql_mis')->select($query);
        // dd($result);
        $excelarr[] = array(
            'Application ID',  'Beneficiary Name', 'District', 'Block/Municipality', 'GP/Ward', 'Account No', 'Bank IFSC',  'Mobile Number','Process Type' 
        );
        
        foreach ($result as $arr) {
            $status = '';
            if ($arr->next_level_role_id == -97 && ($arr->is_approved == 0 || $arr->is_approved == 2)) {
                $status = '';
            } else  if ($arr->next_level_role_id == 101 && $arr->is_approved == 1 ) {
                $status = 'Process with Different';
            }  else  if ($arr->next_level_role_id == 200 && $arr->is_approved == 1 ) {
                $status = 'Process with Same';
            } else  if ($arr->next_level_role_id == 101 && $arr->is_approved == 0 ) {
               $status = 'Process with Different';
            } else  if ($arr->next_level_role_id == 200 && $arr->is_approved == 0 ) {
                $status = 'Process with Same';
            } else  if ($arr->next_level_role_id == -200 &&  $arr->is_approved == 1 ) {
                $status = 'Rejected';
            }
            // echo 1;die();
            $excelarr[] = array(
                'Application Id' => trim($arr->id),
                'Beneficiary Name' => trim($arr->name),
                'District' => trim($arr->district_name),
                'Block/Municipality' => trim($arr->block_name),
                'GP/Ward' => trim($arr->gp),
                'Account No' => trim($arr->account_no),
                'Bank IFSC' => trim($arr->bank_ifsc),
                'Mobile Number' => trim($arr->mobile_no),
                'Process Type' => $status
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
}
