<?php

namespace App\Http\Controllers;

use App\Configduty;
use App\District;
use App\GP;
use App\Taluka;
use App\Scheme;
use App\UrbanBody;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\SubDistrict;
use Illuminate\Support\Facades\Validator;
use App\Ward;

class RejectedReportController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        set_time_limit(600);   
    }
    public function index(Request $request) {
        $designationId = Auth::user()->designation_id_old;
        $userId = Auth::user()->id;
        $sceme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (1) and id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
        $base_date  = '2021-08-16';
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
        if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' ||  $designation_id_old == 'Dashboard' || $designation_id_old == 'DDO') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier' || $designation_id_old == 'StatusCheckerDistrict' || $designation_id_old == 'StatusCheckerField') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                if (in_array($roleObj['scheme_id'], array(1))) {
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

            return view('RejectedReport.rejected_report_index',
            [
                'sceme_list' => $sceme_list,
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
                'muncList' => $muncList
            ]
        );
    }

    public function getAllRejectedDataList(Request $request)
    {
        $scheme_id = $request->scheme_id;
        $district = $request->district;
        if (!is_null($scheme_id)) {
            $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
            //$parameter['scheme_id'] = $scheme_id;
            $schema_name =  $sObj->short_code;
            //dd($schema_name);
            if (empty($schema_name)){
              $schema_name = 'pension';
            }
            $table_name =  $schema_name . '.beneficiary';
          }
          else {
            $table_name =  'pension.beneficiary';
        }

        $data = $this->getSuccessData($scheme_id, $table_name, $district);   
        return datatables()->of($data)
        ->addIndexColumn()
        ->make(true);
    }

    public function getAllRejectedDataListExcelData(Request $request) {
        $scheme_id = $request->scheme_id;
        $district = $request->district;
        if (!is_null($scheme_id)) {
            $sObj =  Scheme::select('scheme_name','id', 'short_code')->where('id', '=', $scheme_id)->first();
            //$parameter['scheme_id'] = $scheme_id;
            $schema_name =  $sObj->short_code;
            //dd($schema_name);
            if (empty($schema_name)){
              $schema_name = 'pension';
            }
            $table_name =  $schema_name . '.beneficiary';
          }
          else {
            $table_name =  'pension.beneficiary';
        }
        
        $data = $this->getSuccessData($scheme_id, $table_name, $district);
        // print_r($data);
        $ben[] = array('Sl No', 'Name', 'Pension Id', 'Father\'s Name','Mother\'s Name','Ration Card No','Voter Id', 'Address', 'Mobile No', 'IFSC','Account No', 'Remarks','Rejection Date');
        $i = 1;
    	foreach ($data as $res) {
          $ben[] = array(
              'Sl No' => $i++,
	    	  'Pension Id' => $res->pension_id,
              'Name'  => trim($res->name),
              'Father\'s Name'  => $res->fathers_name,
              'Mother\'s Name'   => $res->mothers_name,
              'Ration Card No' => $res->ration_card_no,
              'Voter Id' => $res->voter_id,
              'Address' => $res->address,
              'Mobile No' => $res->mobile_no,
              'IFSC' => $res->ifsc,
              'Account No' => $res->account_no,
              'Remarks' => $res->remarks,
              'Rejection Date' => $res->rejection_date,
            );
    	}

    	  Excel::create($sObj->scheme_name.' Rejected Beneficiary List', function($excel) use ($ben){
          $excel->setTitle('Rejected Beneficiary List');
          $excel->sheet('Rejected Beneficiary List', function($sheet) use ($ben){
           $sheet->fromArray($ben, null, 'A1', false, false);
          });
        })->download('xlsx');
    }

    public function getSuccessData($scheme_id, $table_name, $district) {
        $query ='';
        $query = "select distinct
        j.id as pension_id,
        trim(concat(ben_fname,' ', ben_mname,' ',ben_lname)) as name,
        trim(concat(father_fname,' ',father_mname,' ',father_lname)) as fathers_name,
        trim(concat(mother_fname,' ',mother_mname,' ',mother_lname)) as mothers_name,
        trim(ration_card_no) as ration_card_no,
        epic_voter_id as voter_id,
        trim(concat('Block:- ',trim(block_ulb_name),', GP:- ',trim(gp_ward_name),', Village/Town/City:-',trim(village_town_city),', P.O:- ',trim(post_office),', P.S:- ',trim(police_station),', Pincode:- ',pincode)) as address,
        mobile_no as mobile_no,
        trim(bank_ifsc) as ifsc,
        trim(bank_code) as account_no,
        case 
        when next_level_role_id=-2 then 'Duplicate Approve Reject Based on Ration Card/Voter Card'
        when next_level_role_id=-1 then 'Verification Rejected'
        when next_level_role_id=-9999 then j.ds_rejected_reason
        when next_level_role_id=-99 then  remarks 
        else 'NA'	
        end as remarks,
        case 
        when next_level_role_id=-2 then to_char(da.created_at ::date,'dd/mm/yyyy')::text
        when next_level_role_id=-1 then to_char(j.updated_at ::date,'dd/mm/yyyy')::text
        when next_level_role_id=-9999 then 'NA'::text
        when next_level_role_id=-99 then  to_char( u.created_at::date,'dd/mm/yyyy')::text
        else  ('NA')::text	  
        end as rejection_date
        
        
        from ".$table_name." j 
        left join(select original_application_id,remarks,created_at from update_ben_Details where update_code=2 and scheme_id=".$scheme_id." ) u on u.original_application_id=j.id 
        left join(select distinct original_application_id,created_at::date from duplicate_approve_reject where scheme_id=".$scheme_id." ) da on da.original_application_id=j.id
        where is_rejected=1 and scheme_id = ".$scheme_id." ";
        if (!empty($district)) {
            $query .= " and created_by_dist_code=" . $district . " ";
        }
        $data = DB::connection('pgsql')->select($query);
        return $data;
    }
}
