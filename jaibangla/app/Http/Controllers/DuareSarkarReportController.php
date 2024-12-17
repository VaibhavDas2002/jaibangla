<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Configduty;
use Excel;
use App\District;
use App\UrbanBody;
use App\Taluka;
use App\Scheme;
use Carbon\Carbon;
use App\MapLavel;
use App\Ward;
use App\GP;
use Validator;
use App\DsPhase;
class DuareSarkarReportController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth');
        set_time_limit(300);
    }
    public function index(){
    	$user_id = Auth::user()->id;
        $duty = Configduty::select('scheme_id')->where('user_id','=',$user_id)->where('is_active',1)->get();
        $schemeObj = Scheme::whereIn('id',$duty)->get();

        if ($schemeObj->count() == 1) {
        	$sObj = Scheme::whereIn('id',$duty)->first();
        	// Toposili Bandhu(For SC)
        	if ($sObj->id == 3) {
        		$result = DB::select(DB::raw("select distinct report_name,report_date from ds.bandhu_report where report_name is not null group by report_name,report_date;"));
        	}
        	// Jai Johar(For ST)
        	elseif ($sObj->id == 1) {
        		$result = DB::select(DB::raw("select distinct report_name,report_date from ds.johar_report where report_name is not null group by report_name,report_date;"));
        	}
        	else{
        		return redirect("/")->with('success', 'User Invalid Scheme');
        	}
        }
    	else {
        	$result = DB::select(DB::raw("select distinct report_name,report_date from ds.bandhu_report where report_name is not null group by report_name,report_date union
				select distinct report_name,report_date from ds.johar_report where report_name is not null group by report_name,report_date;"));
        }
    	return view('duare-sarkar-report/duare_sarkar_report', ['report'=>$result, 'scheme'=>$schemeObj]);
    }
    public function generateReport(Request $request){

        if (Auth::user()->designation_id_old === 'Dashboard') {
        	$scheme = $request->scheme;
        	$yesterday = date('Y-m-d',strtotime("-1 days"));
        	// Toposili Bandhu(For SC)
        	if ($scheme == 3) {
        		// For generate unique report file name
        		$nameObj = DB::select(DB::raw("select distinct report_name from ds.bandhu_report where report_name is not null"));
        		$file_increment = 0;
        		foreach ($nameObj as $n) {
        			$temp1 = explode('.', $n->report_name);
        			$temp = explode('_', $temp1[0]);
    				if(end($temp) > $file_increment) {
    					$file_increment = end($temp);
    				}
        		}
        		$file_increment++;
        		$filename = 'Duare_Sarkar_Taposili_Report_'.$file_increment.'.xlsx';
        		$fname = 'Duare_Sarkar_Taposili_Report_'.$file_increment;
        		
        		//Step 1: Insert New data
        		$max_app_id = DB::table('ds.bandhu_report')->max('application_id');
        		$max_id = DB::table('ds.bandhu_report')->max('id');

        		DB::statement("INSERT INTO ds.bandhu_report(id, dept_name, scheme, dist, rural_urban, block_ulb_name, gp_ward_name, ben_name, father_name, mobile_no, id_type, epic_no, application_date, application_id, bank_ifsc, bank_code, ration_card_cat, ration_card_no, aadhar_no, assembly_name, village_town_city, payment_count, last_paid_yymm, created_at_system, gender, community)

					select row_number() OVER ()+".$max_id." as id,'Backward Classes Welfare Department'::character varying AS dept_name,
					 'Toposili Bandhu'::character varying AS scheme, trim((SELECT ds_district_name FROM m_district WHERE district_code=b.dist_code)) AS dist,
					CASE WHEN b.rural_urban_id=2 THEN 'R' ELSE 'U' END AS rural_urban,
                                        CASE WHEN b.rural_urban_id=2 THEN trim((SELECT ds_block_name FROM m_block WHERE block_code=b.block_ulb_code)) ELSE trim((SELECT ds_urban_body_name FROM m_urban_body WHERE urban_body_code=b.block_ulb_code)) END AS block_ulb_name,
					CASE WHEN b.rural_urban_id=2 THEN trim((SELECT ds_gp FROM m_gp WHERE gram_panchyat_code=b.gp_ward_code and block_code=b.block_ulb_code)) ELSE trim((SELECT ds_ward FROM m_urban_body_ward WHERE urban_body_ward_code=b.gp_ward_code and urban_body_code=b.block_ulb_code)) END AS gp_ward_name,
                                        trim(COALESCE(b.ben_fname,'')||' '||COALESCE(b.ben_mname,'')||' '||COALESCE(b.ben_lname,'')) AS ben_name,
					trim(COALESCE(b.father_fname,'')||' '||COALESCE(b.father_lname,'')) AS father_name,b.mobile_no AS mobile_no, 
					'E'::character AS id_type,trim(epic_voter_id) AS epic_no, to_char(b.created_at,'dd/mm/yyyy') AS application_date,
					b.id AS application_id,bank_ifsc,bank_code,ration_card_cat,ration_card_no,aadhar_no,assembly_name,village_town_city,payment_count,last_paid_yymm,
					b.created_at,CASE WHEN lower(gender)='male' THEN 'M' WHEN lower(gender)='female' THEN 'F' ELSE 'N' END as gender,caste
					from bandhu.beneficiary b
					where id > ".$max_app_id."");

        		//Step 2: Update payment status
				DB::statement("update ds.bandhu_report br set service_delivery_date= to_char(t.created_at,'dd/mm/yyyy'), service_delivery_date_system= t.created_at, service_delivery_id= t.lot_no from (select tld.pension_id, tl.created_at, tld.lot_no from sbi.transaction_lot_details tld, sbi.transaction_lot tl where tld.lot_no=tl.lot_no and tl.lot_month in ('December','January','February')) t where br.application_id=t.pension_id and br.service_delivery_date is null");

        		//Step 3: For Main Report
        		$result = DB::select(DB::raw("select *,case when gender='Male' then 'M' else 'F' end AS gender_short from ds.bandhu_report
					where service_delivery_date is not null and report_name is null"));

				if (count($result) != 0) {
					$ben[] = array('Department Name','Scheme Name','District Name (as per LGD)','(U)rban / (R)ural','Block/LB Name (as per LGD)','GP Name/Ward No. (as per LGD)','Beneficiary Name','Mobile No','Beneficiary Identification Document  Type: (A)adhar / (E)pic / (K)hadyaSathi','Beneficiary Identification Document Id. No.','Application Date (DD/MM/YYYY)','Application Id. No.','Service Delivery Date (DD/MM/YYYY)','Service Delivery Id. No','Gender','Community');
    	
			        foreach($result as $arr)
			        {
			          $ben[] = array( 
			            'Department Name' => trim($arr->dept_name),
			            'Scheme Name' => trim($arr->scheme),
			            'District Name (as per LGD)' => trim($arr->dist),
			            '(U)rban / (R)ural' => trim($arr->rural_urban),
			            'Block/LB Name (as per LGD)' => trim($arr->block_ulb_name),
			            'GP Name/Ward No. (as per LGD)' => trim($arr->gp_ward_name),
			            'Beneficiary Name' => trim($arr->ben_name),
			            'Mobile No' => trim($arr->mobile_no),
			            'Beneficiary Identification Document Type: (A)adhar / (E)pic / (K)hadyaSathi' => trim($arr->id_type),
			            'Beneficiary Identification Document Id. No.' => trim($arr->epic_no),
			            'Application Date (DD/MM/YYYY)' => trim($arr->application_date),
			            'Application Id. No.' => trim($arr->application_id),
			            'Service Delivery Date (DD/MM/YYYY)' => trim($arr->service_delivery_date),
			            'Service Delivery Id. No' => trim($arr->service_delivery_id),
			            'Gender'=> trim($arr->gender_short),
			            'Community'=> trim($arr->community)
			          );
			        }
					
					//Step 4: Update report status
					DB::statement("update ds.bandhu_report set report_generated=1, report_date=current_date, report_name='".$filename."'where service_delivery_date is not null and report_name is null");

					Excel::create($fname, function($excel) use ($ben){
			          $excel->setTitle('Duare Sarkar Taposili Report');
			          $excel->sheet('Duare Sarkar Taposili Report', function($sheet) use ($ben){
			           $sheet->fromArray($ben, null, 'A1', false, false);
			          });
			        })->download('xlsx');
				}
				else{
					return redirect("duare-sarkar-report")->with('msg1','No new data found for report!!');
				}
				        		
        	}
        	// Jai Johar(For ST)
        	elseif ($scheme == 1) {
        		// For generate unique report file name
        		$nameObj = DB::select(DB::raw("select distinct report_name from ds.johar_report where report_name is not null"));
        		$file_increment = 0;
        		foreach ($nameObj as $n) {
        			$temp1 = explode('.', $n->report_name);
        			$temp = explode('_', $temp1[0]);
    				if(end($temp) > $file_increment) {
    					$file_increment = end($temp);
    				}
        		}
        		$file_increment++;
        		$filename = 'Duare_Sarkar_Johar_Report_'.$file_increment.'.xlsx';
        		$fname = 'Duare_Sarkar_Johar_Report_'.$file_increment;

        		//Step 1: Insert New Data
        		$max_app_id = DB::table('ds.johar_report')->max('application_id');
        		$max_id = DB::table('ds.johar_report')->max('id');
        		
        		DB::statement("INSERT INTO ds.johar_report(id, dept_name, scheme, dist, rural_urban, block_ulb_name, gp_ward_name, ben_name, father_name, mobile_no, id_type, epic_no, application_date, application_id, bank_ifsc, bank_code, ration_card_cat, ration_card_no, aadhar_no, assembly_name, village_town_city, payment_count, last_paid_yymm, created_at_system, gender, community)
					select row_number() OVER ()+".$max_id."as id,'Backward Classes Welfare Department'::character varying AS dept_name,
					 'Jai Johar'::character varying AS scheme, trim((SELECT ds_district_name FROM m_district WHERE district_code=b.dist_code)) AS dist,
					CASE WHEN b.rural_urban_id=2 THEN 'R' ELSE 'U' END AS rural_urban,
                                        CASE WHEN b.rural_urban_id=2 THEN trim((SELECT ds_block_name FROM m_block WHERE block_code=b.block_ulb_code)) ELSE trim((SELECT ds_urban_body_name FROM m_urban_body WHERE urban_body_code=b.block_ulb_code)) END AS block_ulb_name,
					CASE WHEN b.rural_urban_id=2 THEN trim((SELECT ds_gp FROM m_gp WHERE gram_panchyat_code=b.gp_ward_code and block_code=b.block_ulb_code)) ELSE trim((SELECT ds_ward FROM m_urban_body_ward WHERE urban_body_ward_code=b.gp_ward_code and urban_body_code=b.block_ulb_code)) END AS gp_ward_name,
                                        trim(COALESCE(b.ben_fname,'')||' '||COALESCE(b.ben_mname,'')||' '||COALESCE(b.ben_lname,'')) AS ben_name,
					trim(COALESCE(b.father_fname,'')||' '||COALESCE(b.father_lname,'')) AS father_name,b.mobile_no AS mobile_no, 
					'E'::character AS id_type,trim(epic_voter_id) AS epic_no, to_char(b.created_at,'dd/mm/yyyy') AS application_date,
					b.id AS application_id,bank_ifsc,bank_code,ration_card_cat,ration_card_no,aadhar_no,assembly_name,village_town_city,payment_count,last_paid_yymm,
					b.created_at,CASE WHEN lower(gender)='male' THEN 'M' WHEN lower(gender)='female' THEN 'F' ELSE 'N' END as gender,caste
					from johar.beneficiary b
					where id > ".$max_app_id."");

        		//Step 2: Update payment status
					DB::statement("update ds.johar_report br set service_delivery_date= to_char(t.created_at,'dd/mm/yyyy'),
						service_delivery_date_system= t.created_at,
						service_delivery_id= t.drn_part
						from (select tld.pension_id, tl.created_at, tld.drn_part
							  from ifms.transaction_lot_details tld, lot_master tl
						      where tld.drn_part=tl.lot_no and tl.lot_month in ('December','Januray','February')) t
						where br.application_id=t.pension_id and report_name is null");

        		//Step 3: For Main Report
        		$result = DB::select(DB::raw("select *,case when gender='Male' then 'M' else 'F' end AS gender_short from ds.johar_report
					where service_delivery_date is not null and report_name is null"));

        		if (count($result) != 0) {
        			$ben[] = array('Department Name','Scheme Name','District Name (as per LGD)','(U)rban / (R)ural','Block/LB Name (as per LGD)','GP Name/Ward No. (as per LGD)','Beneficiary Name','Mobile No','Beneficiary Identification Document  Type: (A)adhar / (E)pic / (K)hadyaSathi','Beneficiary Identification Document Id. No.','Application Date (DD/MM/YYYY)','Application Id. No.','Service Delivery Date (DD/MM/YYYY)','Service Delivery Id. No','Gender','Community');
    	
			        foreach($result as $arr)
			        {
			          $ben[] = array( 
			            'Department Name' => trim($arr->dept_name),
			            'Scheme Name' => trim($arr->scheme),
			            'District Name (as per LGD)' => trim($arr->dist),
			            '(U)rban / (R)ural' => trim($arr->rural_urban),
			            'Block/LB Name (as per LGD)' => trim($arr->block_ulb_name),
			            'GP Name/Ward No. (as per LGD)' => trim($arr->gp_ward_name),
			            'Beneficiary Name' => trim($arr->ben_name),
			            'Mobile No' => trim($arr->mobile_no),
			            'Beneficiary Identification Document Type: (A)adhar / (E)pic / (K)hadyaSathi' => trim($arr->id_type),
			            'Beneficiary Identification Document Id. No.' => trim($arr->epic_no),
			            'Application Date (DD/MM/YYYY)' => trim($arr->application_date),
			            'Application Id. No.' => trim($arr->application_id),
			            'Service Delivery Date (DD/MM/YYYY)' => trim($arr->service_delivery_date),
			            'Service Delivery Id. No' => trim($arr->service_delivery_id),
			            'Gender' => trim($arr->gender_short),
			            'Community'=> trim($arr->community)
			          );
			        }
					
					//Step 4: Update report status
					DB::statement("update ds.johar_report set report_generated=1, report_date=current_date, report_name='".$filename."' where service_delivery_date is not null and report_name is null");

					Excel::create($fname, function($excel) use ($ben){
			          $excel->setTitle('Duare Sarkar Johar Report');
			          $excel->sheet('Duare Sarkar Johar Report', function($sheet) use ($ben){
			           $sheet->fromArray($ben, null, 'A1', false, false);
			          });
			        })->download('xlsx');
				}
				else{
					return redirect("duare-sarkar-report")->with('msg1','No new data found for report!!');
				}
        	}
        	else{
        		return redirect("duare-sarkar-report")->with('msg1','Invalid Scheme');
        	}
        }
        else{
        	return redirect("/")->with('success', 'User Disabled');
        }
    }
    public function reportDatewise($name){
    	$temp1 = explode('_', $name);
    	if ($temp1[2] == 'Taposili') {
    		$result = DB::select(DB::raw("select *,case when gender='Male' then 'M' else 'F' end AS gender_short from ds.bandhu_report where report_name='".$name."';"));
    	}
    	elseif ($temp1[2] == 'Johar') {
    		$result = DB::select(DB::raw("select *,case when gender='Male' then 'M' else 'F' end AS gender_short from ds.johar_report where report_name='".$name."';"));
    	}
    	else{
    		return redirect("duare-sarkar-report")->with('msg1','Something went wrong.');
    	}
    	$temp = explode('.', $name);
    	$filename = $temp[0];
    	$this->generateExcel($result, $filename);
    }
    public function generateExcel($result, $filename){
    	$ben[] = array('Department Name','Scheme Name','District Name (as per LGD)','(U)rban / (R)ural','Block/LB Name (as per LGD)','GP Name/Ward No. (as per LGD)','Beneficiary Name','Mobile No','Beneficiary Identification Document  Type: (A)adhar / (E)pic / (K)hadyaSathi','Beneficiary Identification Document Id. No.','Application Date (DD/MM/YYYY)','Application Id. No.','Service Delivery Date (DD/MM/YYYY)','Service Delivery Id. No','Gender','Community');
    	
        foreach($result as $arr)
        {
          $ben[] = array( 
            'Department Name' => trim($arr->dept_name),
            'Scheme Name' => trim($arr->scheme),
            'District Name (as per LGD)' => trim($arr->dist),
            '(U)rban / (R)ural' => trim($arr->rural_urban),
            'Block/LB Name (as per LGD)' => trim($arr->block_ulb_name),
            'GP Name/Ward No. (as per LGD)' => trim($arr->gp_ward_name),
            'Beneficiary Name' => trim($arr->ben_name),
            'Mobile No' => trim($arr->mobile_no),
            'Beneficiary Identification Document Type: (A)adhar / (E)pic / (K)hadyaSathi' => trim($arr->id_type),
            'Beneficiary Identification Document Id. No.' => trim($arr->epic_no),
            'Application Date (DD/MM/YYYY)' => trim($arr->application_date),
            'Application Id. No.' => trim($arr->application_id),
            'Service Delivery Date (DD/MM/YYYY)' => trim($arr->service_delivery_date),
            'Service Delivery Id. No' => trim($arr->service_delivery_id),
            'Gender' => trim($arr->gender_short),
			'Community'=> trim($arr->community)
          );
        }
        
        Excel::create($filename, function($excel) use ($ben){
          $excel->setTitle('Duare Sarkar Report');
          $excel->sheet('Duare Sarkar Report', function($sheet) use ($ben){
           $sheet->fromArray($ben, null, 'A1', false, false);
          });
        })->download('xlsx');
    }
  public function ds_simplified_mis(Request $request)
  {
    $base_date  = '2020-01-01';
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
    $userId = Auth::user()->id;
    $scheme_code_in = array();
    $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 and id = 10 order by scheme_name"));
    foreach ($scheme_list as $scheme_item) {
      array_push($scheme_code_in, $scheme_item->id);
    }
    if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' || $designation_id_old == 'HOP' || $designation_id_old == 'MisState' ||  $designation_id_old == 'Dashboard') {
      $district_visible = $is_urban_visible = $block_visible = 1;
    } else if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier') {
      $district_code = NULL;
      $is_urban = NULL;
      $blockCode = NULL;
      $scsctvisible = 0;
      foreach ($roleArray as $roleObj) {
        if (in_array($roleObj['scheme_id'], $scheme_code_in)) {
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
    //$is_urban_visible=0;
    $block_visible = 0;
    $municipality_visible = 0;
    $gp_ward_visible = 0;
    $is_urban_visible = 0;
    $block_visible = 0;
    $district_visible = 0;
	//dd($scheme_list);
    return view(
      'ds-report.ds_simplified',
      [
        'scheme_list' => $scheme_list,
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
        'muncList' => $muncList,
        'designation_id_old' => $designation_id_old,
      ]
    );
  }
  public function ds_simplified_mis_post(Request $request)
  {
    //dd($request->all());
    //$ds_phase_list = Config::get('constants.ds_phase.phaselist');
    $scheme_id = $request->scheme_id;
    $ds_phase = $request->ds_phase;
    $district = $request->district;
    $urban_code = $request->urban_code;
    $block = $request->block;
    $muncid = $request->muncid;
    $gp_ward = $request->gp_ward;
    $select_year = $request->select_year;
    $select_month = $request->select_month;
    $base_date  = '2020-08-16';
    $c_time = Carbon::now();
    $c_date = $c_time->format("Y-m-d");
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
	  'ds_phase' => 'required|integer',
      'scheme_id' => 'required|integer',
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
	$attributes['ds_phase'] = 'Duare Sarkar Phase';
    $attributes['scheme_id'] = 'Scheme';
    $attributes['district'] = 'District';
    $attributes['urban_code'] = 'Rural/ Urban';
    $attributes['block'] = 'Block/Sub Division';
    $attributes['muncid'] = 'Municipality';
    $attributes['gp_ward'] = 'GP/Ward';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $scheme_row = Scheme::where('id', $scheme_id)->first();
	  $ds_row = DsPhase::where('phase_code', $ds_phase)->first();
      $user_msg = " Duare Sarkar Simplified Mis Report for the Scheme " . $scheme_row->scheme_name.' of the '.$ds_row->phase_des;
      $title = $user_msg;
      //dd($title);

      $data = array();
      $return_status = 1;
      $return_msg = '';
      $heading_msg = '';
      $external = 0;
      $external_arr = array();
      $external_filter = array();
      $from_date = NULL;
      $to_date = NULL;
      $caste = NULL;
      

        if (!empty($district)) {
          if ($urban_code == 1) {
            $column = "Sub Division";
            $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $data = $this->getSubDivWisesmDSEntryMark($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
          } else if ($urban_code == 2) {
            $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block";
            $data = $this->getBlockWisesmDSEntryMark($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
          } else {
            $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block/Sub Division";
            $data1 = $this->getBlockWisesmDSEntryMark($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
            $data2 = $this->getSubDivWisesmDSEntryMarks($scheme_id, $district, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);
            $data = array_merge($data1, $data2);
          }
        } else {
          $column = "District";
          $heading_msg = 'District Wise ' . $user_msg;
          $data = $this->getDistrictWisesmDSEntryMarkSet2($scheme_id, NULL, NULL, NULL, NULL, $from_date, $to_date, $caste, $ds_phase, $select_year, $select_month);

          $external = 0;
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
  public function getDistrictWisesmDSEntryMarkSet2($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $caste = NULL, $ds_phase = NULL, $select_year = NULL, $select_month = NULL)
  {
    $table_heading=array(
      //array('query_result_key' => NULL,'th_lable' => 'Sl No.','rowspan' => 2,'colspan' => NULL,'rank'=>1,'isColspan'=>0),
      array('query_result_key' => 'location_name','th_lable' => 'District'),
      array('query_result_key' => 'total','th_lable' => 'Applications Recevied at camp'),
      array('query_result_key' => 'pending','th_lable' => 'Applications under process for verification/other process'),
      array('query_result_key' => 'verified','th_lable' => 'Verified'),
      array('query_result_key' => 'approved','th_lable' => 'Applications Accepted'),
      array('query_result_key' => 'rejected','th_lable' => 'Applications Rejected'),
	  array('query_result_key' => 'disposal_percentage','th_lable' => 'Disposal Percentage(%)')
  );
  $table_heading_collection = collect($table_heading);
 

    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
    } else {
      $schema = "pension";
    }
    $ds_phase = $ds_phase;
    if ($ds_phase != "") {
		if($ds_phase==8){
			$whereCon = "where (ds_phase=" . $ds_phase." or (sm_ds_mark_vii=1 and sm_ds_mark=1)) ";

		}
		if($ds_phase==9){
			$whereCon = "where (ds_phase=" . $ds_phase." or (sm_ds_mark_viii=1 and sm_ds_mark=1)) ";

		}
    }
    $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
    $next_level_role_id_verifier = $role_id_verifier->parent_id;
    $query = "select main.location_id,main.location_name,
COALESCE(bp_main.pending,0)+COALESCE(bp_main.verified,0)
+COALESCE(bp_main.approved,0)+COALESCE(bp_main.rejected,0)
 as total,
COALESCE(bp_main.pending,0) as pending,
COALESCE(bp_main.verified,0) as verified,
COALESCE(bp_main.approved,0) as approved,
COALESCE(bp_main.rejected,0) as rejected,
CASE WHEN (COALESCE(bp_main.pending,0)+COALESCE(bp_main.verified,0)
    +COALESCE(bp_main.approved,0)+COALESCE(bp_main.rejected,0))>0 THEN ROUND(
    (COALESCE(bp_main.approved,0)+COALESCE(bp_main.rejected,0))*100/(COALESCE(bp_main.pending,0)+COALESCE(bp_main.verified,0)
    +COALESCE(bp_main.approved,0)+COALESCE(bp_main.rejected,0))::numeric,2) ELSE 0.00 END as disposal_percentage

    from
    (
    select district_code as location_id,district_name as location_name
    from public.m_district  
    ) as main 
LEFT JOIN
    (
      select 
     
	  count(1) filter(where  ( (is_rejected=0 or is_rejected IS NULL) and  next_level_role_id is NULL)) as pending,
	  count(1) filter(where  ( (is_rejected=0 or is_rejected IS NULL) and  is_verified=1 and next_level_role_id IS NOT NULL
	  and (is_approved=0 or is_approved IS NULL))) as verified,
	  count(1) filter(where  next_level_role_id=0) as approved,
	  count(1) filter(where  is_rejected=1 and next_level_role_id<0) as rejected,
      created_by_dist_code
    from " . $schema . ".beneficiary " . $whereCon . "    
  group by created_by_dist_code
     )  
    as bp_main ON main.location_id=bp_main.created_by_dist_code
     order by main.location_name";
     //echo $query;die;
    $result = DB::connection('pgsql_mis')->select($query);
    //dd($result);
    $table='<table id="example" class="table table-striped table-bordered table2excel" style="width:100%">';
    $table=$table.'<thead>';
    if(count($table_heading_collection)){
      $table=$table.'<tr>';
      foreach($table_heading_collection as $th_item1){
        $table=$table.'<th ';
        
        $table=$table.'>';
      $table=$table.($th_item1['th_lable']);
      $table=$table.'</th>';
      }
      $table=$table.'</tr>';
    }
 
    $table=$table.'</thead>';
    $i=1;
    if(count($result)>0){
      $table=$table.'<tbody>';

    foreach($result as $result_item){
      $table=$table.'<tr>';
      foreach($table_heading_collection as $th_item){
        if($th_item['query_result_key']==null){
         
          continue;
        }
        $col='';
        $col=$th_item['query_result_key'];
        $table=$table.'<td>'.$result_item->$col.'</td>';

      }

      $i++;
      $table=$table.'</tr>';


    }
    $table=$table.'</tbody>';
    }
    $table=$table.'<tfoot><tr></tr></tfoot>';

    $table=$table.'</table>';
    return $table;
  }
}
