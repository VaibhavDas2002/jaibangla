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

class AadharMobileDuplicateBenListController extends Controller
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
    public function index(Request $request)
    {
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        // echo '<pre>'; print_r($roleArray);die();
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = Auth::user()->id;
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
            'duplicate-aadhar-mobile-no/duplicate-aadhar-mobile-ben-details',
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
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = Auth::user()->id;
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
        $table_name = 'pension.beneficiaries';
        // echo $table_name;die();
        if ($request->ajax()) {
            $query = "SELECT b.id, 
            CONCAT(trim(b.ben_fname),' ', trim(b.ben_mname),' ',trim(b.ben_lname)) AS name, 
            d.district_name,
            trim(block_ulb_name) AS block_name,
            trim(gp_ward_name) AS gp, 
            b.aadhar_no as aadhar_no, 
            b.mobile_no as mobile_no, 
            b.bank_code as account_no, 
            b.bank_ifsc as bank_ifsc,
            trim(ration_card_no) AS ration_card_no,
            trim(ration_card_cat) AS ration_card_cat
            FROM " . $table_name . " b 
                LEFT JOIN public.m_district d ON b.dist_code = d.district_code
                WHERE b.scheme_id = ".$scheme_id." AND next_level_role_id = 0";
            if (!empty($district_code)) {
                $query .= " AND b.created_by_dist_code = " . $district_code;
            }
            if (!empty($dist_code)) {
                $query .= " AND b.created_by_dist_code = " . $dist_code;
            }
            if (!empty($block)) {
                $query .= " AND b.created_by_local_body_code = " . $block;
            }
            if (!empty($muncid)) {
                $query .= " AND b.block_ulb_code = " . $muncid;
            }
            if (!empty($gp_ward)) {
                $query .= " AND b.gp_ward_code = " . $gp_ward;
            }
            if (!empty($filter)) {
                if ($filter == 'dup_aadhar') {
                    $query .= " AND b.dup_aadhar = 1 AND dup_aadhar_edit_role_id IS null";
                }
                elseif($filter == 'dup_mobile') {
                    $query .= " AND b.dup_mobile = 1 AND dup_mobile_edit_role_id IS null";
                }
                elseif ($filter == 'no_aadhar') {
                    $query .= " AND b.no_aadhar = 1 AND (b.aadhar_edit_role_id = 1 OR b.aadhar_edit_role_id IS null) AND b.pre_no_aadhar = 1 AND b.is_rejected = 0";
                }
                elseif ($filter == 'no_mobile') {
                    $query .= " AND b.no_mobile = 1 AND next_level_role_id_edit IS null and no_aadhar_mobile_flag IS null";
                } elseif ($filter == 'dup_ration_card') {
                    $query .= " AND b.dup_ration_card = 1";
                }
                elseif($filter == 'no_ration_card') {
                    $query .= " AND b.no_ration_card = 1";
                }
            }
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
            // echo $query;die();
            $result = DB::connection('pgsql_mis_write')->select($query);
            // print_r($result);die();
            return datatables()->of($result)
            ->addColumn('aadhar_no', function ($result) {
                $mask_aadhar = '';
                $aadhar = trim($result->aadhar_no);
                if (strlen($aadhar) >= 12 && strlen($aadhar) != '') {
                  $mask_aadhar = '********' . substr($aadhar, 8, 4);
                } else {
                  $mask_aadhar = $aadhar;
                }
                return $mask_aadhar;
              })
            ->addColumn('mobile_no', function ($result) {
                // $mask_mobile = '';
                // $mobile = trim($result->mobile_no);
                // if (strlen($mobile) >= 10 && strlen($mobile) != '') {
                //     $mask_mobile = '******' . substr($mobile, 6, 4);
                // } else {
                //     $mask_mobile = $mobile;
                // }
                // return $mask_mobile;
                return $result->mobile_no;
            })
            ->addColumn('account_no', function ($result) {
                $mask_bank_code = '';
                $bank_code = trim($result->account_no);
                if (strlen($bank_code) != '') {
                    // echo 1;die;
                    $mask_bank_code = '********' . substr($bank_code, 8, 4);
                    // dd($mask_bank_code);
                }else{
                    // echo 1;die;
                    $mask_bank_code = $bank_code;
                }
                return $mask_bank_code;
            })
            ->addColumn('bank_ifsc', function ($result) {
                $mask_bank_ifsc = '';
                $bank_ifsc = trim($result->bank_ifsc);
                if (strlen($bank_ifsc) != '') {
                    $mask_bank_ifsc = '********' . substr($bank_ifsc, 8, 4);
                    // dd($mask_bank_ifsc);
                }else{
                    $mask_bank_ifsc = $bank_ifsc;
                }
                return $mask_bank_ifsc;
            })->addColumn('khyasathi_card_no', function ($result) {
                $ration_card_no = trim($result->ration_card_no);
                $ration_card_cat = trim($result->ration_card_cat);
                if (!empty($ration_card_no) && !empty($ration_card_cat)) {
                    $khyasathi_card_no = $ration_card_cat.'-'.$ration_card_no;
                    // dd($mask_bank_ifsc);
                }else{
                    $khyasathi_card_no = '';
                }
                return $khyasathi_card_no;
            })
            ->make(true);
        }
    }

    public function exportExcel(Request $request)
    {
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = Auth::user()->id;
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
        if($filter == 'dup_aadhar'){
            $user_msg = 'Duplicate Aadhar Number';
        }
        if($filter == 'dup_mobile'){
            $user_msg = 'Duplicate Mobile Number';
        }
        if ($filter == 'no_aadhar') {
            $user_msg = 'No Aadhar Number';
        }
        if ($filter == 'no_mobile') {
            $user_msg = 'No Mobile Number';
        }
        if ($filter == 'dup_ration_card') {
            $user_msg = 'Dup Ration Card';
        }
        if ($filter == 'no_ration_card') {
            $user_msg = 'No Ration Card';
        }
        // echo $scheme_id;die();
        $query = "SELECT b.id, CONCAT(trim(b.ben_fname),' ', trim(b.ben_mname),' ',trim(b.ben_lname)) AS name, 
        d.district_name,
        trim(block_ulb_name) AS block_name,
        trim(gp_ward_name) AS gp, '********' ||RIGHT(b.aadhar_no::varchar,4) as aadhar_no, 
        b.mobile_no as mobile_no, 
        '********' ||RIGHT(b.bank_code::varchar,4) as account_no, 
        '********' ||RIGHT(b.bank_ifsc::varchar,4) as bank_ifsc,
        trim(ration_card_no) AS ration_card_no,
        trim(ration_card_cat) AS ration_card_cat
        FROM " . $table_name . " b 
                LEFT JOIN public.m_district d ON b.dist_code = d.district_code
                WHERE b.scheme_id = ".$scheme_id ."AND next_level_role_id = 0";
                if (!empty($district_code)) {
                    $query .= " AND b.created_by_dist_code = " . $district_code;
                }
                if (!empty($dist_code)) {
                    $query .= " AND b.created_by_dist_code = " . $dist_code;
                }
                if (!empty($block)) {
                    $query .= " AND b.created_by_local_body_code = " . $block;
                }
                if (!empty($muncid)) {
                    $query .= " AND b.block_ulb_code = " . $muncid;
                }
                if (!empty($gp_ward)) {
                    $query .= " AND b.gp_ward_code = " . $gp_ward;
                }
        if (!empty($filter)) {
            if ($filter == 'dup_aadhar') {
                $query .= " AND b.dup_aadhar = 1 AND dup_aadhar_edit_role_id IS null";
            } 
            if($filter == 'dup_mobile') {
                $query .= " AND b.dup_mobile = 1 AND dup_mobile_edit_role_id IS null";
            }
            if ($filter == 'no_aadhar') {
                $query .= " AND b.no_aadhar = 1 AND next_level_role_id_edit IS null AND (no_aadhar_mobile_flag = 1 OR no_aadhar_mobile_flag IS null)";
            }
            if ($filter == 'no_mobile') {
                $query .= " AND b.no_mobile = 1 AND next_level_role_id_edit IS null and no_aadhar_mobile_flag IS null";
            }
            if ($filter == 'dup_ration_card') {
                $query .= " AND b.dup_ration_card = 1";
            }
            if($filter == 'no_ration_card') {
                $query .= " AND b.no_ration_card = 1";
            }
        }
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
        // echo $query;die();
        $result = DB::connection('pgsql_mis')->select($query);
        $excelarr[] = array(
            'Application ID',  'Beneficiary Name', 'District', 'Block/Municipality', 'GP/Ward', 'Account No', 'Bank IFSC', 'Aadhar Number', 'Mobile Number', 'Ration Card Number', 
        );
        
        foreach ($result as $arr) {
            // echo 1;die();
            $excelarr[] = array(
                'Application Id' => trim($arr->id),
                'Beneficiary Name' => trim($arr->name),
                'District' => trim($arr->district_name),
                'Block/Municipality' => trim($arr->block_name),
                'GP/Ward' => trim($arr->gp),
                'Account No' => trim($arr->account_no),
                'Bank IFSC' => trim($arr->bank_ifsc),
                'Aadhar Number' => trim($arr->aadhar_no),
                'Mobile Number' => trim($arr->mobile_no),
                'Ration Card Number' => trim($arr->ration_card_cat).'-'.trim($arr->ration_card_no),
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
