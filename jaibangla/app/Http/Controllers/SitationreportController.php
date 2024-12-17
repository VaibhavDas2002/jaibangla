<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sitationreport;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Log;
use App\Configduty;
use Auth;
use App\Policestation;
use App\Dailysitrep;
use DB;
use App\SitrepConfiguration;
use App\ExciseAct;
use App\CompoundSlip;
use App\Warrent;
use App\PreventiveArrest;
use App\Sizure;
use App\PreviousCaseArrest;
use App\Cspolicestation;
use App\CrimeHead;
use App\MissingDetails;

class SitationreportController extends Controller
{
	public function index(){

	}
    public function create(){
    	//$sitationreportData = Sitationreport::where('report_date','=',date("Y-m-d"))->orWhere('status','=',0)->get();

    	 //$sitationreportData = Sitationreport::where('status', 0)->orWhere('report_date', '=', date("Y-m-d"))->get();

    	 $sitationreportData = Sitationreport::where([
		    ['status', '=', 0],
		    ['report_date', '=', date("Y-m-d")]
		])->get();

    	return view('generalCrime')->with('sitationreportData',$sitationreportData);
    }
    public function arrestSeizure(){

    	 $arrestSeizureData = Sitationreport::where([
		    ['status', '=', 0],
		    ['report_date', '=', date("Y-m-d")]
		])->get();
    	return view('arrestSeizure')->with('arrestSeizureData',$arrestSeizureData);
    }
    public function arrestSeizurePost(){


    		$arrestSeizure = Sitationreport::where([
		    ['status', '=', 0],
		    ['report_date', '=', date("Y-m-d")]
		])->get();
    	$user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();
    	$returnObj="";	

    	if ($request['arrestDraft'])
		{
    		foreach($arrestSeizure as $data){
    			$data->id;	
    		}

    			if(count($arrestSeizure) > 0){
		   		$input=[		
    	        'specific_arrest' => $request['specific_arrest'],
	            'preventive_arrest' => $request['preventive_arrest'],
	            'seizure_arms' => $request['seizure_arms'],
	            'seizure_amunation' => $request['seizure_amunation'],
	            'seizure_bomb' => $request['seizure_bomb'],
	            'seizure_explosive' => $request['seizure_explosive'],
	            'seizure_fire_cracker' => $request['seizure_fire_cracker'],
	            'seizure_id_liquor' => $request['seizure_id_liquor'],
	            'seizure_board_money' => $request['seizure_board_money'],
	            'seizure_ficn' => $request['seizure_ficn'],
	            'ndps_ganza' => $request['ndps_ganza'],
	            'ndps_heroin' => $request['ndps_heroin'],
	            'ndps_mixture' => $request['ndps_mixture'],
	            'ndps_oheers' => $request['ndps_oheers']
	            ];
	    		Sitationreport::find($data->id)->update($input);	

	    		}else{
		    		$input=[		
	    	        'specific_arrest' => $request['specific_arrest'],
		            'preventive_arrest' => $request['preventive_arrest'],
		            'seizure_arms' => $request['seizure_arms'],
		            'seizure_amunation' => $request['seizure_amunation'],
		            'seizure_bomb' => $request['seizure_bomb'],
		            'seizure_explosive' => $request['seizure_explosive'],
		            'seizure_fire_cracker' => $request['seizure_fire_cracker'],
		            'seizure_id_liquor' => $request['seizure_id_liquor'],
		            'seizure_board_money' => $request['seizure_board_money'],
		            'seizure_ficn' => $request['seizure_ficn'],
		            'ndps_ganza' => $request['ndps_ganza'],
		            'ndps_heroin' => $request['ndps_heroin'],
		            'ndps_mixture' => $request['ndps_mixture'],
		            'ndps_oheers' => $request['ndps_oheers']
		            ];
	    		}
	    		$report_id=Sitationreport::create($input);
	    }
    }

    public function excise_act(){
    	return view('exciseAct');
    }

    public function missingDetails(){
    	return view('missingDetails');
    }

    public function warrentDetails(){
    	return view('warrentDetails');
    }

     public function slipDetails(){
    	return view('compoundDetails');
    }

    public function preventiveWarrentDetails(){
    	return view('preventiveDetails');
    }

    public function backDateCase(){
        
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();        
        $policestations = Cspolicestation::where('cs_ps_code', '=', $duty->ps_code)->get();  
        $crimeHeads = CrimeHead::all();
        return view('backdate_case_arrest',['policestations' => $policestations , 'crimeHeads' => $crimeHeads]);
        
    }

    public function saveBackDateCaseDetails(Request $request){
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();
        $arrest_date = date('Y-m-d', strtotime("-1 days"));
        $input=[
        'com_id' => $duty->ps_code,
        'ps_id'=>  $request['ps_id'],
        'crime_head_id' => $request['crime_head'],
        'case_no' => $request['case_no'],
        'case_date'=> $request['case_date'],
        'section_of_law'=> $request['section_of_law'],        
        'arrest'=> $request['sizure'],
        'arrest_figure'=> $request['no_of_arrest'],
        'arrest_date'=> $arrest_date        
        ];
        PreviousCaseArrest::create($input);
        return redirect()->intended('backDateCase')->with('success','Case Saved successfully!'); 
    }
    

    public function saveCompoundSlip(Request $request){
    	$user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();        
    	$date = date('Y-m-d', strtotime("-1 days"));
    	$input=[	
    		'com_id' => $duty->ps_code,
    		'report_date' => $date,	   		
	   		'no_of_slips' => $request['no_of_slip_issued'],
            'amount' => $request['slip_issued_amount'],
	    ];
	    CompoundSlip::create($input);
	    return redirect()->intended('slipDetails')->with('success','Item created successfully!');
    }

    public function saveExciseArrest(Request $request){
    	$user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();        
    	$date = date('Y-m-d', strtotime("-1 days"));
    	$input=[	
    		'com_id' => $duty->ps_code,
    		'report_date' => $date,	   		
	   		'no_of_cases' => $request['number_of_cases'],
            'sizure' => $request['excise_seizure'],
            'arrest' => $request['excise_arrest']
	    ];
	    ExciseAct::create($input);
	    return redirect()->intended('excise_act')->with('success','Item created successfully!');
    }

    
    
    public function saveArrestSizure(Request $request){
    	$user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();        
    	$date = date('Y-m-d', strtotime("-1 days"));
    	$input=[	
    		'com_id' => $duty->ps_code,
    		'report_date' => $date,	   		
	   		'seizure_arms' => $request['seizure_arms'],
            'seizure_amunation' => $request['seizure_amunation'],
            'seizure_bomb' => $request['seizure_bomb'],
            'seizure_explosive' => $request['seizure_explosive'],
            'seizure_fire_cracker' => $request['seizure_fire_cracker'],
            'seizure_id_liquor' => $request['seizure_id_liquor'],
            'seizure_board_money' => $request['seizure_board_money'],
            'seizure_ficn' => $request['seizure_ficn'],
            'ndps_ganza' => $request['ndps_ganza'],
            'ndps_heroin' => $request['ndps_heroin'],
            'ndps_mixture' => $request['ndps_mixture'],
            'ndps_oheers' => $request['ndps_oheers']
	    ];
	    Sizure::create($input);
	    return redirect()->intended('arrestSeizure')->with('success','Item created successfully!');
    }


    public function saveWarrent(Request $request){
    	$user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();        
    	$date = date('Y-m-d', strtotime("-1 days"));
    	$input=[	
    		'com_id' => $duty->ps_code,
    		'report_date' => $date,	   		
	   		'warrent_execution' => $request['warrent_execution'],
            'warrent_recalled' => $request['warrent_recalled'],
            'warrent_disposal' => $request['warrent_disposal']
	    ];
	    Warrent::create($input);
	    return redirect()->intended('warrentDetails')->with('success','Item created successfully!');
    }


    public function savePreventiveArrest(Request $request){
    	$user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();        
    	$date = date('Y-m-d', strtotime("-1 days"));
    	$input=[	
    		'com_id' => $duty->ps_code,
    		'report_date' => $date,	   		
	   		'thirt_four_pc' => $request['preventive_34_police_act'],
            'two_ninghty_ipc' => $request['preventive_290_ipc'],
            'one_fifty_one_crpc' => $request['preventive_151_107_crpc'],
            'one_zeo_nine_crpc' => $request['preventive_109_crpc'],
            'one_one_zero_crpc' => $request['preventive_110_crpc'],
            'forty_one_crpc' => $request['preventive_41_crpc'],
            'wbpc_act' => $request['wbgpc_act']
	    ];
	    PreventiveArrest::create($input);
	    return redirect()->intended('preventiveWarrentDetails')->with('success','Item created successfully!');
    }

    public function saveMissingDetails(Request $request){
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();
        $date = date('Y-m-d', strtotime("-1 days"));
        $input=[
        'com_id' => $duty->ps_code,     
        'report_date' => $date,
        'missing_male' => $request['missing_male'],
        'missing_female' => $request['missing_female'],
        'missing_child' => $request['missing_children'],
        'traceout_male' => $request['traceout_male'],
        'traceout_female' => $request['traceout_female'],
        'traceout_child' => $request['traceout_children']      
        
        ];
        $report_id=MissingDetails::create($input);
        return redirect()->intended('missingDetails')->with('success','Item created successfully!');
    }

    public function postData(Request $request){

    	$sitationreportData = Sitationreport::where([
		    ['status', '=', 0],
		    ['report_date', '=', date("Y-m-d")]
		])->get();
    	$user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();
    	$returnObj="";	
    	 
    	 
    	if ($request['saveDraft'])
		{
    		foreach($sitationreportData as $data){
    			$data->id;	
    		}
			//Log::info('Report count '.count($sitationreport));
		   	if(count($sitationreportData) > 0){
		   		$input=[		   		
		   		'total_number_of_cases' => $request['total_number_of_cases'],
	            'dacoity' => $request['dacoity'],
	            'robbery' => $request['robbery'],
	            'burglary' => $request['burglary'],
	            'theft' => $request['theft'],
	            'political_murder' => $request['political_murder'],
	            'general_murder' => $request['general_murder'],
	            'political_clash' => $request['political_clash'],
	            'vandalism_in_hospital' => $request['vandalism_in_hospital'],
	            'commuunal_violence' => $request['commuunal_violence'],
	            'communal_incitement' => $request['communal_incitement'],
	            'caw' => $request['caw'],
	            'aht' => $request['aht'],
	            'rape' => $request['rape'],
	            'pocso' => $request['pocso'],
	            'rioting' => $request['rioting'],
	            'ndps' => $request['ndps'],
	            'ficn' => $request['ficn'],
	            'police_action_in_lo' => $request['police_action_in_lo'],
	            'assault_on_police' => $request['assault_on_police'],
	            'rta' => $request['rta'],
	            'others' => $request['others'],
	            'remarks' => $request['remarks'],
	            
	            ];
	    		Sitationreport::find($data->id)->update($input);	
	    	}else{
	    		$input=[
	    		'com_id' => $duty->ps_code,		
	    		'report_date' => date("Y-m-d"),
	            'total_number_of_cases' => $request['total_number_of_cases'],
	            'dacoity' => $request['dacoity'],
	            'robbery' => $request['robbery'],
	            'burglary' => $request['burglary'],
	            'theft' => $request['theft'],
	            'political_murder' => $request['political_murder'],
	            'general_murder' => $request['general_murder'],
	            'political_clash' => $request['political_clash'],
	            'vandalism_in_hospital' => $request['vandalism_in_hospital'],
	            'commuunal_violence' => $request['commuunal_violence'],
	            'communal_incitement' => $request['communal_incitement'],
	            'caw' => $request['caw'],
	            'aht' => $request['aht'],
	            'rape' => $request['rape'],
	            'pocso' => $request['pocso'],
	            'rioting' => $request['rioting'],
	            'ndps' => $request['ndps'],
	            'ficn' => $request['ficn'],
	            'police_action_in_lo' => $request['police_action_in_lo'],
	            'assault_on_police' => $request['assault_on_police'],
	            'rta' => $request['rta'],
	            'others' => $request['others'],
	            'remarks' => $request['remarks']
	            
	        	];
	    		$report_id=Sitationreport::create($input);
	    	}

		}
		else if ($request->has('submitReport'))  
		{
			foreach($sitationreportData as $data){
    			$data->id;	
    		}

				$input=[
				'com_id' => $duty->ps_code,		
	    		'report_date' => date("Y-m-d"),
	            'total_number_of_cases' => $request['total_number_of_cases'],
	            'dacoity' => $request['dacoity'],
	            'robbery' => $request['robbery'],
	            'burglary' => $request['burglary'],
	            'theft' => $request['theft'],
	            'political_murder' => $request['political_murder'],
	            'general_murder' => $request['general_murder'],
	            'political_clash' => $request['political_clash'],
	            'vandalism_in_hospital' => $request['vandalism_in_hospital'],
	            'commuunal_violence' => $request['commuunal_violence'],
	            'communal_incitement' => $request['communal_incitement'],
	            'caw' => $request['caw'],
	            'aht' => $request['aht'],
	            'rape' => $request['rape'],
	            'pocso' => $request['pocso'],
	            'rioting' => $request['rioting'],
	            'ndps' => $request['ndps'],
	            'ficn' => $request['ficn'],
	            'police_action_in_lo' => $request['police_action_in_lo'],
	            'assault_on_police' => $request['assault_on_police'],
	            'rta' => $request['rta'],
	            'others' => $request['others'],
	            'remarks' => $request['remarks'],
	            'status' => 1
	        	];

	        	if(count($sitationreportData) > 0){
	        	 Sitationreport::find($data->id)->update($input);
    			}
    			else{
    				Sitationreport::create($input);
    			}
		}
		
    	//return view('sitationreport')->with('sitationreportData',$sitationreportData);
    	return redirect()->intended('generalCrime')->with('sitationreportData',$sitationreportData)->with('success','Item created successfully!');
    }

    public function caseReport(){

    	$sitationreport = Sitationreport::all();
    	$policestation  = Policestation::all();
    	$dailysitrep = Dailysitrep::all();
//policestation
// case_summary_report
    	 $dailysitrep = DB::table('case_details')
        ->leftJoin('policestation', 'case_details.id', '=', 'policestation.id')
         ->leftJoin('case_summary_report', 'case_details.id', '=', 'case_summary_report.id')
        ->select(
        	'case_details.ps_id', 
        	'case_summary_report.total_number_of_cases',
        	'case_summary_report.seizure_arms',
        	'case_summary_report.seizure_amunation',
        	'case_summary_report.seizure_bomb',
        	'case_summary_report.seizure_explosive',
        	'case_summary_report.seizure_id_liquor',
        	'case_summary_report.seizure_board_money',
        	'case_summary_report.seizure_ficn',
        	'case_details.case_date',
        	'case_details.arrest', 
        	'policestation.name as name')
        ->paginate(5);



    	return view('districtwise_report',compact('dailysitrep'));
    }

    public function sitrepReport(){

    	$sitationreport = Sitationreport::all();
    	$policestation  = Policestation::all();
    	$dailysitrep = Dailysitrep::all();
//policestation
// case_summary_report
    	 $sitrepREportData = DB::table('case_details')
        ->leftJoin('policestation', 'case_details.id', '=', 'policestation.id')
         ->leftJoin('cs_police_station', 'policestation.id', '=', 'cs_police_station.id')
        ->select(
        	'policestation.id',
        	'policestation.name as Commi_Name',
        	'cs_police_station.id as police_Station_id',
        	'cs_police_station.name as police_Station_Name',
        	'case_details.ps_id', 
        	'case_details.case_no',
        	'case_details.case_date',
        	'case_details.section_of_law',
        	'case_details.gist'
        	)->orderBy('policestation.id')
        ->paginate(50);

    	return view('sitrepRepoprt')->with('sitrepReportData',$sitrepREportData);

    }

    public function viewDailySitrep(){
    	$config=SitrepConfiguration::where('com_dist_id', '=', 1)->first();
    	Log::info($config);
    	return view('district_report')->with('config',$config);
    }

    public function generalCaseSummaryReport(){
        
        //Log::info($config);
        $entities = Policestation::all();
        return view('mis_case_summary')->with('entities',$entities);
    }

    public function arrestSizureReport(){
        
        //Log::info($config);
        $entities = Policestation::all();
        return view('mis_arrest_sizure_report')->with('entities',$entities);
    }


    public function arrestExciseReport(){
        
        //Log::info($config);
        $entities = Policestation::all();
        return view('mis_arrest_excise_act')->with('entities',$entities);
    }
    

    
    
}
