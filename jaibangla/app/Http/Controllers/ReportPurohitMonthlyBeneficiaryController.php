<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use Maatwebsite\Excel\Facades\Excel;
use App\District;
use App\UrbanBody;
use App\Taluka;

class ReportPurohitMonthlyBeneficiaryController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth');
        //$this->middleware('Admin');
        set_time_limit(300);
    }

    public function index(){
    	$user_id = AuthChecker::getUserId();
        $schemes=Configduty::where('user_id','=',$user_id)->first();
        $dist_code = $schemes->district_code;
        if (Auth::user()->designation_id == 'Approver') {
        	$block_ulb = DB::select(DB::raw("select distinct block_ulb_code ,p.block_ulb_name|| (case when p.block_ulb_code<10000 then ' Block' else ' Municipality' end) block_ulb_name 
				from pension.beneficiaries p where dist_code=".$dist_code.";"));
	        $result = DB::select(DB::raw("select p.dist_code dist_code,p.block_ulb_code block_ulb_code,d.district_name as district_name,p.block_ulb_name|| (case when p.block_ulb_code<10000 then ' Block' else ' Municipality' end) as block_ulb_name ,p.app_phase as app_phase,
				count(*) total_ben,
				sum(case when next_level_role_id is null then 1 else 0 end) pending_verifier,
				sum(case when next_level_role_id =107 then 1 else 0 end) pending_recommendation,
				sum(case when next_level_role_id =106 then 1 else 0 end) pending_approval,
				sum(case when next_level_role_id =0 then 1 else 0 end) approved,
				sum(case when next_level_role_id =-1 then 1 else 0 end) rejected
				from pension.beneficiaries p,  m_district d 
				where d.district_code=p.dist_code and p.dist_code=".$dist_code."
				and p.scheme_id = 17 
				group by d.district_name,dist_code,p.block_ulb_name,p.block_ulb_code,p.app_phase
				order by d.district_name,dist_code,p.block_ulb_name,p.app_phase;"));
	    	return view('purohit_monthly_report', ['block_ulb_list'=>$block_ulb, 'report'=>$result, 'district_code' => $dist_code]);
        }
        elseif (Auth::user()->designation_id == 'HOD') {
        	$result = DB::select(DB::raw("select p.dist_code dist_code,d.district_name as district_name, p.app_phase as app_phase,count(*) total_ben,
				sum(case when next_level_role_id is null then 1 else 0 end) pending_verifier,
				sum(case when next_level_role_id =107 then 1 else 0 end) pending_recommendation,
				sum(case when next_level_role_id =106 then 1 else 0 end) pending_approval,
				sum(case when next_level_role_id =0 then 1 else 0 end) approved,
				sum(case when next_level_role_id =-1 then 1 else 0 end) rejected
				from pension.beneficiaries p,  m_district d 
				where d.district_code=p.dist_code
				and p.scheme_id = 17  
				group by d.district_name,dist_code,p.app_phase
				order by d.district_name,dist_code,p.app_phase;"));
        	return view('purohit_monthly_report', ['report'=>$result]);
        }
        else{
        	return redirect("/")->with('success', 'User Disabled');
        }
        
    }

    public function filterBlockUlb(Request $request){
    	$block_ulb = $request->block_ulb;
    	$dist_code = $request->dist_code;

    	$block_ulb_l = DB::select(DB::raw("select distinct block_ulb_code ,p.block_ulb_name|| (case when p.block_ulb_code<10000 then ' Block' else ' Municipality' end) block_ulb_name 
			from pension.beneficiaries p where dist_code=".$dist_code.";"));
        $result = DB::select(DB::raw("select p.dist_code dist_code,p.block_ulb_code block_ulb_code,d.district_name as district_name,p.block_ulb_name|| (case when p.block_ulb_code<10000 then ' Block' else ' Municipality' end) as block_ulb_name ,p.app_phase as app_phase,
			count(*) total_ben,
			sum(case when next_level_role_id is null then 1 else 0 end) pending_verifier,
			sum(case when next_level_role_id =107 then 1 else 0 end) pending_recommendation,
			sum(case when next_level_role_id =106 then 1 else 0 end) pending_approval,
			sum(case when next_level_role_id =0 then 1 else 0 end) approved,
			sum(case when next_level_role_id =-1 then 1 else 0 end) rejected
			from pension.beneficiaries p,  m_district d 
			where d.district_code=p.dist_code and p.dist_code=".$dist_code." and p.block_ulb_code=".$block_ulb."
			group by d.district_name,p.block_ulb_name,p.block_ulb_code,p.app_phase
			order by d.district_name,p.block_ulb_name,p.app_phase;"));
    	return view('report-purohit-monthly/purohit_monthly_report', ['block_ulb_list'=>$block_ulb_l, 'report'=>$result, 'district_code' => $dist_code]);
    }

    public function generateExcelPurohit($dist_code, $block_ulb_code){
    	$result = DB::select(DB::raw("select * from pension.beneficiaries p where p.created_by_dist_code=".$dist_code." and p.created_by_local_body_code=".$block_ulb_code." and p.next_level_role_id = 0;"));
    	$distObj = District::where('district_code', $dist_code)->first();
    	if ($block_ulb_code < 10000) {
    		$blockObj = Taluka::where('block_code', $block_ulb_code)->first();
    		$msg = $blockObj->block_name.' Block';
    	}
    	else{
    		$urbanBodyObj = UrbanBody::where('urban_body_code', $block_ulb_code)->first();
    		$msg = $urbanBodyObj->urban_body_name.' Municipality';
    	}
    	$name = $distObj->district_name.' '.$msg.' Purohit Monthly Beneficiary Report';
    	$this->generateExcel($result, $name);
    }
    public function generateExcelPurohitHOD($dist_code){
    	$result = DB::select(DB::raw("select * from pension.beneficiaries p where p.created_by_dist_code=".$dist_code." and p.next_level_role_id = 0;"));
    	$distObj = District::where('district_code', $dist_code)->first();
    	$name = $distObj->district_name.' Purohit Monthly Financial Assistance Beneficiary Report';
    	$this->generateExcel($result, $name);
    }

    public function generateExcel($result, $filename){
    	$ben[] = array('ID','Name',"Father's Name",'Block/Muni','Bank Acc No','IFSC Code','Mobile No');
    	
        foreach($result as $arr)
        {
          $ben[] = array( 
	   'ID'=> trim($arr->id),
           'Name'  => trim($arr->ben_fname).' '.trim($arr->ben_mname).' '.trim($arr->ben_lname),
           "Father's Name"  => trim($arr->father_fname).' '.trim($arr->father_mname).' '.trim($arr->father_lname),
           'Block/Muni'=>trim($arr->block_ulb_name),
           'Bank Acc No' => trim($arr->bank_code),
           'IFSC Code' => trim($arr->bank_ifsc),
           'Mobile No' => trim($arr->mobile_no)
          );
        }
        
        Excel::create($filename, function($excel) use ($ben){
          $excel->setTitle('Purohit Monthly Ben');
          $excel->sheet('Purohit Monthly Ben', function($sheet) use ($ben){
           $sheet->fromArray($ben, null, 'A1', false, false);
          });
        })->download('xlsx');
    }
}
