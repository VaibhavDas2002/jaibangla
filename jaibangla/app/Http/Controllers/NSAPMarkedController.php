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
class NSAPMarkedController extends Controller
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
        // echo '<pre>'; print_r($roleArray);die();
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = Auth::user()->id;
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") AND id = 11 order by rank"));
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
                if ($roleObj['scheme_id'] == 11) {
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
            'nsap-marked-beneficiary-list/nsap-marked-ben-details',
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

    public function nsapBenDetails(Request $request)
    {
        // echo 1;die;
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $schemes = DB::select(DB::raw("select id,scheme_name,pr1_code,entry_url,display_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") AND id = 11 order by rank"));
        if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
        } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == 11) {
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
        if ($request->ajax()) {
            $query = "SELECT b.id AS ID,CONCAT(TRIM(b.ben_fname),' ',TRIM(b.ben_mname),' ',TRIM(b.ben_lname)) AS name,trim(
                concat(
                    'Block:- ',
                    trim(block_ulb_name),
                    ', GP:- ',
                    trim(gp_ward_name),
                    ', Village/Town/City:-',
                    trim(village_town_city),
                    ', P.O:- ',
                    trim(post_office),
                    ', P.S:- ',
                    trim(police_station),
                    ', Pincode:- ',
                    pincode
                )
            ) as address,b.gender AS gender,b.ben_age AS age,
            CONCAT(TRIM(b.father_fname),' ',TRIM(b.father_mname),' ',TRIM(b.father_lname)) AS father_name,d.district_name AS district_name,bu.block_subdiv_name AS block_name,
            gp_ward_name AS gp,TRIM(b.nsap_bank_code) AS account_no,TRIM(b.nsap_bank_ifsc) AS bank_ifsc,TRIM(b.aadhar_no) AS aadhar_no,b.mobile_no AS mobile_no
            FROM ".$table_name." b 
            JOIN m_district d ON b.created_by_dist_code = d.district_code
            JOIN (select sub_district_code as block_subdiv_code,sub_district_name as block_subdiv_name from public.m_sub_district ub 
            union all
            select block_code as block_subdiv_code,block_name as block_subdiv_name from public.m_block mb
            ) bu ON bu.block_subdiv_code=b.created_by_local_body_code
            WHERE b.scheme_id = ".$scheme_id." AND b.is_nsap_dup_bank = true AND (b.next_level_role_id = -99 OR b.next_level_role_id = 0)";
            if (!empty($district_code)) {
                $query .= " AND b.created_by_dist_code = " . $district_code;
            }
            if (!empty($blockCode)) {
                $query .= " AND b.created_by_local_body_code = " . $blockCode;
            }
            if (!empty($dist_code)) {
                $query .= " AND b.created_by_dist_code = " . $dist_code;
            }
            if (!empty($block)) {
                $query .= " AND b.created_by_local_body_code = " . $block;
            }
            if (!empty($gp_ward)) {
                $query .= " AND b.gp_ward_code = " . $gp_ward;
            }
            if (!empty($muncid)) {
                $query .= " AND b.block_ulb_code = " . $muncid;
            }
            $query .= " ORDER BY name";
            // echo $query;die();
            $result = DB::connection('pgsql_mis')->select($query);
            // echo '<pre>';print_r($result);die();
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
            // ->addColumn('mobile_no', function ($result) {
            //     $mask_mobile = '';
            //     $mobile = trim($result->mobile_no);
            //     if (strlen($mobile) >= 10 && strlen($mobile) != '') {
            //         $mask_mobile = '******' . substr($mobile, 6, 4);
            //     } else {
            //         $mask_mobile = $mobile;
            //     }
            //     return $mask_mobile;
            // })
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
            })
            ->make(true);
        }
    }

    public function exportExcel(Request $request)
    {
        $dist_code = $request->district;
        // $filter = $request->search_for;
        $scheme_id = $request->scheme_id;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $gp_ward = $request->gp_ward;
        $muncid = $request->muncid;
        $table_name = $this->getSchemaName($scheme_id);
        $schemeObj = Scheme::where('id',$scheme_id)->first();
        $user_msg = "With Duplicate NSAP Data";
        // echo $scheme_id;die();
        $query = "SELECT b.id AS ID,CONCAT(TRIM(b.ben_fname),' ',TRIM(b.ben_mname),' ',TRIM(b.ben_lname)) AS name,b.gender AS gender,b.ben_age AS age,
        CONCAT(TRIM(b.father_fname),' ',TRIM(b.father_mname),' ',TRIM(b.father_lname)) AS father_name,trim(
                concat(
                    'Block:- ',
                    trim(block_ulb_name),
                    ', GP:- ',
                    trim(gp_ward_name),
                    ', Village/Town/City:-',
                    trim(village_town_city),
                    ', P.O:- ',
                    trim(post_office),
                    ', P.S:- ',
                    trim(police_station),
                    ', Pincode:- ',
                    pincode
                )
            ) as address,d.district_name AS district_name,bu.block_subdiv_name AS block_name,
        gp_ward_name AS gp,'********' ||RIGHT(TRIM(b.bank_code)::varchar,4) as account_no,'********' ||RIGHT(TRIM(b.bank_ifsc)::varchar,4) as bank_ifsc,'********' ||RIGHT(TRIM(b.aadhar_no)::varchar,4) as aadhar_no,b.mobile_no as mobile_no
        FROM ".$table_name." b 
        JOIN m_district d ON b.created_by_dist_code = d.district_code
        JOIN (select sub_district_code as block_subdiv_code,sub_district_name as block_subdiv_name from public.m_sub_district ub 
        union all
        select block_code as block_subdiv_code,block_name as block_subdiv_name from public.m_block mb
        ) bu ON bu.block_subdiv_code=b.created_by_local_body_code
        WHERE b.scheme_id = ".$scheme_id." AND b.is_nsap_dup_bank = true AND (b.next_level_role_id = -99 OR b.next_level_role_id = 0)";
        if (!empty($dist_code)) {
            $query .= " AND b.created_by_dist_code = " . $dist_code;
        }
        if (!empty($block)) {
            $query .= " AND b.created_by_local_body_code = " . $block;
        }
        if (!empty($gp_ward)) {
            $query .= " AND b.gp_ward_code = " . $gp_ward;
        }
        if (!empty($muncid)) {
            $query .= " AND b.block_ulb_code = " . $muncid;
        }
        $query .= " ORDER BY name";
        // echo $query;die();
        $result = DB::connection('pgsql_mis')->select($query);
        $excelarr[] = array(
            'Application ID', 'Beneficiary Name', 'Father Name', 'Address', 'Age', 'District', 'Block/Municipality', 'GP/Ward', 'Account No', 'Bank IFSC', 'Aadhar Number', 'Mobile Number', 
        );
        
        foreach ($result as $arr) {
            // echo 1;die();
            $excelarr[] = array(
                'Application Id' => trim($arr->id),
                'Beneficiary Name' => trim($arr->name),
                'Father Name' => trim($arr->father_name),
                'Address' => trim($arr->address),
                'Age' => trim($arr->age),
                'District' => trim($arr->district_name),
                'Block/Municipality' => trim($arr->block_name),
                'GP/Ward' => trim($arr->gp),
                'Account No' => trim($arr->account_no),
                'Bank IFSC' => trim($arr->bank_ifsc),
                'Aadhar Number' => trim($arr->aadhar_no),
                'Mobile Number' => trim($arr->mobile_no),
            );
        }
        // print_r($excelarr);die();
        $file_name = $schemeObj->scheme_name.' '.$user_msg .' '.  date('d/m/Y');
        Excel::create($file_name, function ($excel) use ($excelarr) {
            $excel->setTitle('Duplicate NSAP Data');
            $excel->sheet('Duplicate NSAP Data', function ($sheet) use ($excelarr) {
                $sheet->fromArray($excelarr, null, 'A1', false, false);
            });
        })->download('xlsx');
    }
}
