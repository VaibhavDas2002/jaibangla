<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\DupliacteApproveReject;
use App\Scheme;
use App\District;
use App\BeneficiaryPensions;
use App\PensionSc;
use App\PensionSt;
use App\Manabik;
use App\UpdateBenDetails;
use Excel;
use App\Configduty;
use Auth;

class ReportDuplicateStopPaymentBenController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    	set_time_limit(600);
    }
   //  public function index(){
   //  	$scheme = Scheme::all();
   //  	$district = District::all();
   //  	return view('duplicate_and_stop_payment_ben_report',['schemes' => $scheme, 'districts' => $district]);
   //  }

   //  public function showResult($scheme){
   //  	$arr = [];
   //  	// if (request()->ajax()) {
   //  		$schemeObj = Scheme::where('id',$scheme)->first();
   //  		$districts = District::select('district_code')->get();
   //  		// Scheme Selection
    		
   //  		foreach ($districts as $dist) {
			// 	if ($scheme == 3) {
	  //   			$stop_payment = PensionSc::where('dist_code',$dist->district_code)->where('next_level_role_id',-99)->where('scheme_id',$scheme)->count();
	  //   		}
	  //   		elseif ($scheme == 1) {
	  //   			$stop_payment = PensionSt::where('dist_code',$dist->district_code)->where('next_level_role_id',-99)->where('scheme_id',$scheme)->count();
	  //   		}
	  //   		else {
	  //   			$stop_payment = Manabik::where('dist_code',$dist->district_code)->where('next_level_role_id',-99)->where('scheme_id',$scheme)->count();
	  //   		}
			// 	// $stop_payment = $db_schema::where('dist_code',$dist->district_code)->where('next_level_role_id',-99)->where('scheme_id',$scheme)->count();
			// 	$approved = DupliacteApproveReject::select('original_approve_application_id')->distinct()->where('dist_code',$dist->district_code)->where('scheme_id',$scheme)->count('original_approve_application_id');
			// 	$duplicate = DupliacteApproveReject::select('original_application_id')->distinct()->where('dist_code',$dist->district_code)->where('scheme_id',$scheme)->count('original_application_id');
			// 	$arr = [
			// 		'district_code' => $dist->district_code,
			// 		'duplicate' => $duplicate,
			// 		'approved' => $approved,
			// 		'stop_payment' => $stop_payment
			// 	];
			// 	$final_arr[] = $arr;
			// }
   //  		return response()->json(['data' => $final_arr, 'scheme_name' => $schemeObj->scheme_name]);
   //  	// }
   //  }

    public function index1(){
    	if (Auth::user()->designation_id_old == 'HOD') {
    		$user_id = Auth::user()->id;
        	$schemeObj=Configduty::select('scheme_id')->where('user_id','=',$user_id)->where('is_active',1)->get();
        	$scheme = Scheme::whereIn('id',$schemeObj)->get();
    		return view('report-duplicate-stop-payment/index',['schemes' => $scheme]);
    	}
    	else {
    		return redirect("/")->with('success', 'User Disabled');
    	}
    }
    public function linelistingReport(Request $request){
    	$scheme_id = $request->scheme_id;
    	$filter = $request->month;
    	$schemeObj = Scheme::where('id',$scheme_id)->first();
    	if ($filter == 'all') {
    		$date = '2020-12-01';
    	}
    	else{
    		$date = date('Y-m-01');
    	}

    	$report = DB::select(DB::raw("Select d.district_code
			, d.district_name "."District"."
			, COALESCE(stp_pmnt_cnt,0) "."Stop_Payment_Count"."
			, COALESCE(dup_rjct_cnt,0) "."Duplicate_Reject_Count"."
			From( Select dist_code
			, SUM(stp_pmnt_cnt) stp_pmnt_cnt
			, SUM(dup_rjct_cnt) dup_rjct_cnt 
			from(
			select dist_code,count(distinct original_application_id) as stp_pmnt_cnt , 
			0 as dup_rjct_cnt
			from update_ben_details
			where update_code=2 and scheme_id=".$scheme_id." 
			and created_at>=cast('".$date."' as date) and created_at<=now()
			group by dist_code
			union all
			select dist_code,0 as stp_pmnt_cnt , 
			count(distinct original_application_id) as dup_rjct_cnt
			from duplicate_approve_reject
			where scheme_id=".$scheme_id." and created_at>=cast('".$date."' as date) and created_at<=now()
			group by dist_code
			)s Group by dist_code
			)s1
			right join m_district d
			on d.district_code=s1.dist_code
			Order by d.district_name"));
    	
    	return view('report-duplicate-stop-payment/linelisting_report', ['scheme_name' => $schemeObj->scheme_name, 'scheme_id'=>$scheme_id, 'date'=>$date, 'filter'=>$filter, 'reports' =>$report ]);
    }
    public function excelDuplicateReject(Request $request){
    	$district_code = $request->district_code; 
    	$scheme_id = $request->scheme_id; 
    	$date = $request->date;
    	$result = DB::select(DB::raw("select * from duplicate_approve_reject
			where scheme_id=".$scheme_id." and dist_code=".$district_code." and created_at>=cast('".$date."' as date) and created_at<=now()"));
    	$sObj = Scheme::where('id',$scheme_id)->first();
    	$filename = $sObj->scheme_name.' Duplicate Rejected ';
    	// $this->generateExcel($result, $filename);
    	$ben[] = array('Id','Name','Block/Municipality','Date','Approved Id');
        foreach($result as $arr)
        {
          $ben[] = array(
          	'Id' => trim($arr->id),
           	'Name'  => trim($arr->ben_fname).' '.trim($arr->ben_mname).' '.trim($arr->ben_lname),
           	'Block/Municipality' => trim($arr->block_ulb_name),
           	'Date' => trim(date("d-m-Y", strtotime($arr->created_at))),
           	'Approved Id' => trim($arr->original_approve_application_id)
          );
        }
        //print_r($ben);die();
        Excel::create($filename.'Beneficiary List', function($excel) use ($ben){
          $excel->setTitle('List of Beneficiary');
          $excel->sheet('List of Beneficiary', function($sheet) use ($ben){
           $sheet->fromArray($ben, null, 'A1', false, false);
          });
        })->download('xlsx');
    }
    public function excelStopPayment(Request $request){
    	$district_code = $request->district_code; 
    	$scheme_id = $request->scheme_id; 
    	$date = $request->date;
    	$result = DB::select(DB::raw("select * from pension.beneficiary where id in(select distinct original_application_id from update_ben_details where update_code=2 and dist_code=".$district_code." and scheme_id=".$scheme_id." and created_at>=cast('".$date."' as date) and created_at<=now())"));
    	$sObj = Scheme::where('id',$scheme_id)->first();
    	$filename = $sObj->scheme_name.' Stop Payment ';
    	$this->generateExcel($result, $filename);
    }
    // Generate Excel
    public function generateExcel($result, $filename){
        $ben[] = array('Id','Name','Block/Municipality','Date');
        foreach($result as $arr)
        {
          $ben[] = array(
          	'Id' => trim($arr->id),
           	'Name'  => trim($arr->ben_fname).' '.trim($arr->ben_mname).' '.trim($arr->ben_lname),
           	'Block/Municipality' => trim($arr->block_ulb_name),
           	'Date' => trim(date("d-m-Y", strtotime($arr->created_at)))
          );
        }
        //print_r($ben);die();
        Excel::create($filename.'Beneficiary List', function($excel) use ($ben){
          $excel->setTitle('List of Beneficiary');
          $excel->sheet('List of Beneficiary', function($sheet) use ($ben){
           $sheet->fromArray($ben, null, 'A1', false, false);
          });
        })->download('xlsx');
    }
}
