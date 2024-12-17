<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\BeneficiaryPensionsReport;
use App\BenEntry;
use App\District;
use App\Configduty;
use App\Scheme;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use App\SchemeCapacity;
use Auth;
use Excel;
use App\PensionOAPWCD;
use App\PensionWPWCD;
use App\PensionOAPSTWCD;
use App\PensionManabikWCD;

class DistrictwiseStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }
    public function index(){
    	$district = District::all();
    	$scheme = Scheme::all();
    	return view('districtwise-statistics/index',['schemes' => $scheme, 'districts' => $district]);
    }
    public function lineListDistBlockUlb(Request $request){
    	//print 'LINELISTING';
    	$this->validate($request, [
            'scheme_type' => 'required|not-in:0',
            'level' => 'required|not-in:0' 
        ]);
        $scheme_id = $request->scheme_type;
        $level = $request->level;
        $sObj = Scheme::where('id',$scheme_id)->first();
        $request->session()->put('scheme_id', $scheme_id);
        $request->session()->put('scheme_name', $sObj->scheme_name);
        // State
        if ($level == 'S') {
        	$scheme = Scheme::where('id',$scheme_id)->get();
        	$msg = 'Download report of '.$sObj->scheme_name.' scheme:';
        	return view('districtwise-statistics/linelist_dist_block_ulb',['scheme' => $sObj->scheme_name, 'level' => $level, 'msg' => $msg, 'linelist' => $scheme]);
        }
        // District
        if ($level == 'D') {
        	$msg = 'Download report of '.$sObj->scheme_name.' scheme:';
        	$district = District::all();
        	return view('districtwise-statistics/linelist_dist_block_ulb',['scheme' => $sObj->scheme_name, 'level' => $level, 'msg' => $msg, 'linelist' => $district]);
        }
        // Block/Municipllity
        else{
        	$this->validate($request, [
	            'district' => 'required|not-in:0',
	            'is_rural_urban' => 'required|not-in:0'
	        ]);
	        //print 'Block/Municipality';
            $dist_code = $request->district;
            $distObj = District::where('district_code',$dist_code)->first();
            $is_rural_urban = $request->is_rural_urban;
            // Rural Area
            if ($is_rural_urban == '2') {
            	$msg = 'Download report for '.$distObj->district_name.' in the '.$sObj->scheme_name.' scheme (Only for Rural area-Block):';
                $rural_body = Taluka::where('district_code',$dist_code)->get();
                //print_r($rural_body);
                return view('districtwise-statistics/linelist_dist_block_ulb',['scheme' => $sObj->scheme_name, 'block_ulb' => $is_rural_urban, 'level' => $level, 'msg' => $msg, 'linelist' => $rural_body]);
            }
            // Urban Area
            else{
            	$msg = 'Download report for '.$distObj->district_name.' in the '.$sObj->scheme_name.' scheme (Only for Urban area-Municiplity):';
            	$urban_body = UrbanBody::where('district_code',$dist_code)->get();
            	return view('districtwise-statistics/linelist_dist_block_ulb',['scheme' => $sObj->scheme_name, 'block_ulb' => $is_rural_urban, 'level' => $level, 'msg' => $msg, 'linelist' => $urban_body]);
            }
        }
    }
    // Generate Excel
    public function generateExcel(Request $request, $str){
    	parse_str($str, $filter1);
    	$level = $filter1['aParam']['level'];
    	$filter = $filter1['aParam']['filter'];
    	$ben = [];
    	$scheme_id = $request->session()->get('scheme_id', '0');
    	$scheme_name = $request->session()->get('scheme_name', 'na');
    	// State
    	if ($level == 'S') {
    		print 'Something wrong!!Go back to dashboard.';
    	}
    	// District
    	elseif ($level == 'D') {
    		$distObj = District::where('district_code',$filter)->first();
    		$dist_name = $distObj->district_name;
    		if($scheme_id == 2){
    			$ben_array = BenEntry::where('dist_code',$filter)
    					 ->where('scheme_id',$scheme_id)
    					 ->get();    			
    		}else if($scheme_id == 10){
    			$ben_array = BenEntry::where('dist_code',$filter)
    					 ->where('scheme_id',$scheme_id)
    					 ->get();
    		}else if($scheme_id == 11){
    			$ben_array = BenEntry::where('dist_code',$filter)
    					 ->where('scheme_id',$scheme_id)
    					 ->get();
    			
    		}else if($scheme_id == 12){
    			$ben_array = BenEntry::where('dist_code',$filter)
    					 ->where('scheme_id',$scheme_id)
    					 ->get();
    			
    		}else{
    			$ben_array = BenEntry::where('dist_code',$filter)
    					 ->where('scheme_id',$scheme_id)
    					 ->get();
    		}

    		if(($scheme_id == 2)||($scheme_id == 10)||($scheme_id == 11)||($scheme_id == 12)){
    			$ben[] = array('Ben Code','Name', 'Fathers Name','Block/Municipality','Village/Town','Post office','PIN Code','Bank Acc No','IFSC Code','Old Ben Code');
    		
	    		foreach($ben_array as $arr)
			    {
			      $ben[] = array(
			       'Ben Code'  => $arr->getBenidAttribute(),
			       'Name'  => $arr->ben_fname.' '.$arr->ben_mname.' '.$arr->ben_lname,
			       'Fathers Name'  => $arr->father_fname.' '.$arr->father_mname.' '.$arr->father_lname,		       
			       'Block/Municipality' => $arr->block_ulb_name,     			       
			       'Village/Town' => $arr->village_town_city,
			       'Post Office' => $arr->post_office,
			       'PIN Code' => $arr->pin_code,
			       'Bank Acc No' => $arr->bank_code,
			       'IFSC Code' => $arr->bank_ifsc,
			       'Old Ben Code' => $arr->old_beneficiary_id
			      );
			    }
    		}else{

    			$ben[] = array('Name', 'Fathers Name','Ration Card No','Block/Municipality','Assembly','GP/Ward','Village/Town','Post office','PIN Code','Bank Acc No','IFSC Code');
    		
	    		foreach($ben_array as $arr)
			    {
			      $ben[] = array(
			       'Name'  => $arr->ben_fname.' '.$arr->ben_mname.' '.$arr->ben_lname,
			       'Fathers Name'  => $arr->father_fname.' '.$arr->father_mname.' '.$arr->father_lname,
			       'Ration Card No'   => $arr->ration_card_cat.'-'.$arr->ration_card_no,
			       'Block/Municipality' => $arr->block_ulb_name,
			       'Assembly' => $arr->assembly_name,
			       'GP/Ward' => $arr->gp_ward_name,
			       'Village/Town' => $arr->village_town_city,
			       'Post Office' => $arr->post_office,
			       'PIN Code' => $arr->pin_code,
			       'Bank Acc No' => $arr->bank_code,
			       'IFSC Code' => $arr->bank_ifsc
			      );
			    }

    		}

    		
    		
		    //print_r($ben);die();
		    Excel::create($scheme_name."-".$dist_name, function($excel) use ($ben){
		      $excel->setTitle('Beneficiary Data');
		      $excel->sheet('Beneficiary Data', function($sheet) use ($ben){
		       $sheet->fromArray($ben, null, 'A1', false, false);
		      });
		    })->download('xlsx');
    	}
    	// Block/Municiplity
    	else{
    		//print_r($filter);die();
    		$block_ulb = $filter1['aParam']['block_ulb'];
    		// Block
    		if ($block_ulb == 2) {
    			$blockObj = Taluka::where('block_code',$filter)->first();
    			$block_name = $blockObj->block_name;
    			/*$ben_array = BeneficiaryPensionsReport::where('block_ulb_code',$filter)
    						->where('scheme_id',$scheme_id)
    						->get();*/
    			if($scheme_id == 2){
    			$ben_array = BenEntry::where('block_ulb_code',$filter)
    					 ->where('scheme_id',$scheme_id)
    					 ->get();    			
	    		}else if($scheme_id == 10){
	    			$ben_array = BenEntry::where('block_ulb_code',$filter)
	    					 ->where('scheme_id',$scheme_id)
	    					 ->get();
	    		}else if($scheme_id == 11){
	    			$ben_array = BenEntry::where('block_ulb_code',$filter)
	    					 ->where('scheme_id',$scheme_id)
	    					 ->get();
	    			
	    		}else if($scheme_id == 12){
	    			$ben_array = BenEntry::where('block_ulb_code',$filter)
	    					 ->where('scheme_id',$scheme_id)
	    					 ->get();
	    			
	    		}else{
	    			$ben_array = BenEntry::where('block_ulb_code',$filter)
	    					 ->where('scheme_id',$scheme_id)
	    					 ->get();
	    		}			
	    		if(($scheme_id == 2)||($scheme_id == 10)||($scheme_id == 11)||($scheme_id == 12)){
    			$ben[] = array('Ben Code','Name', 'Fathers Name','Block/Municipality','Village/Town','Post office','PIN Code','Bank Acc No','IFSC Code','Old Ben Code');
    		
	    		foreach($ben_array as $arr)
			    {
			      $ben[] = array(
			       'Ben Code'  => $arr->getBenidAttribute(),
			       'Name'  => $arr->ben_fname.' '.$arr->ben_mname.' '.$arr->ben_lname,
			       'Fathers Name'  => $arr->father_fname.' '.$arr->father_mname.' '.$arr->father_lname,		       
			       'Block/Municipality' => $arr->block_ulb_name,     			       
			       'Village/Town' => $arr->village_town_city,
			       'Post Office' => $arr->post_office,
			       'PIN Code' => $arr->pin_code,
			       'Bank Acc No' => $arr->bank_code,
			       'IFSC Code' => $arr->bank_ifsc,
			       'Old Ben Code' => $arr->old_beneficiary_id
			      );
			    }
    		}else{

    			$ben[] = array('Name', 'Fathers Name','Ration Card No','Block/Municipality','Assembly','GP/Ward','Village/Town','Post office','PIN Code','Bank Acc No','IFSC Code');
    		
	    		foreach($ben_array as $arr)
			    {
			      $ben[] = array(
			       'Name'  => $arr->ben_fname.' '.$arr->ben_mname.' '.$arr->ben_lname,
			       'Fathers Name'  => $arr->father_fname.' '.$arr->father_mname.' '.$arr->father_lname,
			       'Ration Card No'   => $arr->ration_card_cat.'-'.$arr->ration_card_no,
			       'Block/Municipality' => $arr->block_ulb_name,
			       'Assembly' => $arr->assembly_name,
			       'GP/Ward' => $arr->gp_ward_name,
			       'Village/Town' => $arr->village_town_city,
			       'Post Office' => $arr->post_office,
			       'PIN Code' => $arr->pin_code,
			       'Bank Acc No' => $arr->bank_code,
			       'IFSC Code' => $arr->bank_ifsc
			      );
			    }

    		}
			    //print_r($ben);die();
			    Excel::create($scheme_name."-".$block_name, function($excel) use ($ben){
			      $excel->setTitle('Beneficiary Data');
			      $excel->sheet('Beneficiary Data', function($sheet) use ($ben){
			       $sheet->fromArray($ben, null, 'A1', false, false);
			      });
			    })->download('xlsx');
    		}
    		// Municipality
    		else{
    			$blockObj = UrbanBody::where('urban_body_code',$filter)->first();
    			$block_name = $blockObj->urban_body_name;
    			/*$ben_array = BeneficiaryPensionsReport::where('block_ulb_code',$filter)
    						->where('scheme_id',$scheme_id)
    						->get();*/
	    		if($scheme_id == 2){
    			$ben_array = BenEntry::where('block_ulb_code',$filter)
    					 ->where('scheme_id',$scheme_id)
    					 ->get();    			
	    		}else if($scheme_id == 10){
	    			$ben_array = BenEntry::where('block_ulb_code',$filter)
	    					 ->where('scheme_id',$scheme_id)
	    					 ->get();
	    		}else if($scheme_id == 11){
	    			$ben_array = BenEntry::where('block_ulb_code',$filter)
	    					 ->where('scheme_id',$scheme_id)
	    					 ->get();
	    			
	    		}else if($scheme_id == 12){
	    			$ben_array = BenEntry::where('block_ulb_code',$filter)
	    					 ->where('scheme_id',$scheme_id)
	    					 ->get();
	    			
	    		}else{
	    			$ben_array = BenEntry::where('block_ulb_code',$filter)
	    					 ->where('scheme_id',$scheme_id)
	    					 ->get();
	    		}
	    		if(($scheme_id == 2)||($scheme_id == 10)||($scheme_id == 11)||($scheme_id == 12)){
    			$ben[] = array('Ben Code','Name', 'Fathers Name','Block/Municipality','Village/Town','Post office','PIN Code','Bank Acc No','IFSC Code','Old Ben Code');
    		
	    		foreach($ben_array as $arr)
			    {
			      $ben[] = array(
			       'Ben Code'  => $arr->getBenidAttribute(),
			       'Name'  => $arr->ben_fname.' '.$arr->ben_mname.' '.$arr->ben_lname,
			       'Fathers Name'  => $arr->father_fname.' '.$arr->father_mname.' '.$arr->father_lname,		       
			       'Block/Municipality' => $arr->block_ulb_name,     			       
			       'Village/Town' => $arr->village_town_city,
			       'Post Office' => $arr->post_office,
			       'PIN Code' => $arr->pin_code,
			       'Bank Acc No' => $arr->bank_code,
			       'IFSC Code' => $arr->bank_ifsc,
			       'Old Ben Code' => $arr->old_beneficiary_id
			      );
			    }
    		}else{

    			$ben[] = array('Name', 'Fathers Name','Ration Card No','Block/Municipality','Assembly','GP/Ward','Village/Town','Post office','PIN Code','Bank Acc No','IFSC Code');
    		
	    		foreach($ben_array as $arr)
			    {
			      $ben[] = array(
			       'Name'  => $arr->ben_fname.' '.$arr->ben_mname.' '.$arr->ben_lname,
			       'Fathers Name'  => $arr->father_fname.' '.$arr->father_mname.' '.$arr->father_lname,
			       'Ration Card No'   => $arr->ration_card_cat.'-'.$arr->ration_card_no,
			       'Block/Municipality' => $arr->block_ulb_name,
			       'Assembly' => $arr->assembly_name,
			       'GP/Ward' => $arr->gp_ward_name,
			       'Village/Town' => $arr->village_town_city,
			       'Post Office' => $arr->post_office,
			       'PIN Code' => $arr->pin_code,
			       'Bank Acc No' => $arr->bank_code,
			       'IFSC Code' => $arr->bank_ifsc
			      );
			    }

    		}
			    //print_r($ben);die();
			    Excel::create($scheme_name."-".$block_name, function($excel) use ($ben){
			      $excel->setTitle('Beneficiary Data');
			      $excel->sheet('Beneficiary Data', function($sheet) use ($ben){
			       $sheet->fromArray($ben, null, 'A1', false, false);
			      });
			    })->download('xlsx');
    		}
    	}
    }
}
