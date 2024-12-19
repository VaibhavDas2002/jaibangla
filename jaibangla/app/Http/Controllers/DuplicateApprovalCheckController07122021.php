<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ben_lot_month;
use App\lot_master;
use App\Configduty;
use App\MapLavel;
use App\PensionSc;
use App\PensionSt;
use App\District;
use App\Taluka;
use App\Ward;
use App\UrbanBody;
use App\GP;
use Auth;

use App\BenDocsSc;
use App\BenDocsSt;
use App\DocumentType;

use App\Scheme;
use App\BeneficiaryPensions;
use App\DupliacteApproveReject;
use Config;

class DuplicateApprovalCheckController extends Controller
{
    public function __construct(){
    	$this->middleware('auth');
    }
    public function index(){
    	// $scheme = Scheme::all();
	    $user_id = AuthChecker::getUserId();
      //$schemeObj=Configduty::select('scheme_id')->where('user_id','=',$user_id)->where('is_active',1)->get();
      //$scheme = Scheme::whereIn('id',$schemeObj)->where('is_active',1)->get();
      $desig = Auth::user()->designation_id_old;
      $districts = District::all();
	    $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1) and is_active=1 order by scheme_name"));
    	return view('duplicate-approval/index', ['schemes' => $scheme, 'district' => $districts, 'designation' => $desig]);
    }
    public function duplicateListing(Request $request){
    	$this->validate($request, [
            'scheme' => 'required|not-in:0',
            'filter' => 'required|not-in:0'
        ]);

    	$scheme_id = $request->scheme;
    	$filter = $request->filter;
    	$user_id = AuthChecker::getUserId();
      $dutyObj = Configduty::where('user_id',$user_id)->first();
      //$dist_code = $dutyObj->district_code;
      if (is_null($dutyObj->district_code)) {
        $dist_code = $request->dist_code;
      }
      else {
        $dist_code = $dutyObj->district_code;
      }
      
      $table_name = 'pension.beneficiary';
      if(!is_null($scheme_id)){
        if($scheme_id==1){
          $table_name = 'johar.beneficiary';
        }
        else if($scheme_id==2){
          $table_name = 'manabik.beneficiary';
        }
        else if($scheme_id==3){
          $table_name = 'bandhu.beneficiary';
        }
        else if($scheme_id==5){
          $table_name = 'fisherman_oap.beneficiary';
        }
        else if($scheme_id==6){
          $table_name = 'msme.beneficiary';
        }
        else if($scheme_id==7){
          $table_name = 'textile.beneficiary';
        }
        else if($scheme_id==8){
          $table_name = 'lokprasar_retainer.beneficiary';
        }
        else if($scheme_id==9){
          $table_name = 'lokprasar_pensioner.beneficiary';
        }
        else if($scheme_id==10){
          $table_name = 'oap_wcd.beneficiary';
        }
        else if($scheme_id==11){
          $table_name = 'wp_wcd.beneficiary';
        }
        else if($scheme_id==12){
          $table_name = 'oap_st_wcd.beneficiary';
        }
        else if($scheme_id==13){
          $table_name = 'farmer.beneficiary';
        }
        else if($scheme_id==14){
          $table_name = 'labour_construction.beneficiary';
        }
        else if($scheme_id==15){
          $table_name = 'labour_transport.beneficiary';
        }
        else if($scheme_id==17){
          $table_name = 'purohit_monthly.beneficiary';
        }
        else if($scheme_id==18){
          $table_name = 'purohit_housing.beneficiary';
        }
        else if($scheme_id==19){
          $table_name = 'oap_st.beneficiary';
        }
      }

      if ($filter == 'ration') {
      	// $report = DB::select(DB::raw("select ration_card_no, count(*) as ben_no from ".$table_name." where next_level_role_id=0 and dist_code= ".$dist_code." and scheme_id= ".$scheme_id." group by ration_card_no having count(*)>1"));
        $report = DB::select(DB::raw("select trim(concat(ration_card_cat,' - ',ration_card_no)) as ration_card_no
            , count(*) as ben_no from ".$table_name." where next_level_role_id=0 
            and dist_code=".$dist_code." and scheme_id=".$scheme_id." 
            group by concat(ration_card_cat,' - ',ration_card_no) 
            having count(*)>1  and trim(concat(ration_card_cat,' - ',ration_card_no))!='' order by count(*) desc"));
      }
      elseif ($filter == 'voter') {
      	$report = DB::select(DB::raw("select epic_voter_id, count(*) as ben_no from ".$table_name." where next_level_role_id=0 and dist_code= ".$dist_code." and scheme_id= ".$scheme_id." group by epic_voter_id having count(*)>1 and epic_voter_id!='' order by count(*) desc"));
      }
      elseif($filter == 'bank') {
        $report = DB::select(DB::raw("select trim(concat(trim(bank_ifsc),' - ',trim(bank_code))) as bak_det, count(*) as ben_no from ".$table_name." where next_level_role_id=0 
          and dist_code=".$dist_code." and scheme_id=".$scheme_id." 
          group by concat(trim(bank_ifsc),' - ',trim(bank_code))
          having count(*)>1  and trim(concat(trim(bank_ifsc),' - ',trim(bank_code)))!=''
          order by count(*) desc"));
      }
      else {
   	   	// $report = DB::select(DB::raw("select ration_card_no,epic_voter_id, count(*) as ben_no from ".$table_name." where next_level_role_id=0 and dist_code= ".$dist_code." and scheme_id= ".$scheme_id." group by ration_card_no,epic_voter_id having count(*)>1"));
        $report = DB::select(DB::raw("select trim(concat(ration_card_cat,ration_card_no)) as ration_card_no
            , count(*) as ben_no from ".$table_name." where next_level_role_id=0 
            and dist_code=".$dist_code." and scheme_id=".$scheme_id." 
            group by concat(ration_card_cat,ration_card_no) 
            having count(*)>1  and trim(concat(ration_card_cat,ration_card_no))!=''"));
  	  }
      return view('duplicate-approval/listing_duplicate', ['reports' => $report, 'filters' => $filter, 'scheme_id' => $scheme_id, 'dist_code' => $dist_code]);
    }

    public function acceptOneBen(Request $request){ 
    	$filter = $request->filter;
      $scheme_id = $request->scheme_id;
      $dist_code = $request->dist_code;

      $table_name = 'pension.beneficiary';
      if(!is_null($scheme_id)){
        if($scheme_id==1){
          $table_name = 'johar.beneficiary';
        }
        else if($scheme_id==2){
          $table_name = 'manabik.beneficiary';
        }
        else if($scheme_id==3){
          $table_name = 'bandhu.beneficiary';
        }
        else if($scheme_id==5){
          $table_name = 'fisherman_oap.beneficiary';
        }
        else if($scheme_id==6){
          $table_name = 'msme.beneficiary';
        }
        else if($scheme_id==7){
          $table_name = 'textile.beneficiary';
        }
        else if($scheme_id==8){
          $table_name = 'lokprasar_retainer.beneficiary';
        }
        else if($scheme_id==9){
          $table_name = 'lokprasar_pensioner.beneficiary';
        }
        else if($scheme_id==10){
          $table_name = 'oap_wcd.beneficiary';
        }
        else if($scheme_id==11){
          $table_name = 'wp_wcd.beneficiary';
        }
        else if($scheme_id==12){
          $table_name = 'oap_st_wcd.beneficiary';
        }
        else if($scheme_id==13){
          $table_name = 'farmer.beneficiary';
        }
        else if($scheme_id==14){
          $table_name = 'labour_construction.beneficiary';
        }
        else if($scheme_id==15){
          $table_name = 'labour_transport.beneficiary';
        }
        else if($scheme_id==17){
          $table_name = 'purohit_monthly.beneficiary';
        }
        else if($scheme_id==18){
          $table_name = 'purohit_housing.beneficiary';
        }
        else if($scheme_id==19){
          $table_name = 'oap_st.beneficiary';
        }
      }
    	
    	if ($filter == 'voter') {
    		$id = $request->ration_card;
        //print $id;
			  $ben_report = DB::select(DB::raw("select * from ".$table_name."  where next_level_role_id=0 and epic_voter_id='".$id."' and dist_code= ".$dist_code." and scheme_id= ".$scheme_id.""));
			  $ben_lot_report=DB::select(DB::raw("select pension_id,count(drn_part) from ifms.transaction_lot_details be,lot_master l where pension_id in 
      		(select id from ".$table_name." where epic_voter_id='".$id."')
      		and be.drn_part=l.lot_no and l.voucher_no is null
      		group by pension_id"));
        $ben_lot_report_sbi=DB::select(DB::raw("select pension_id,count(sbi.lot_no) from sbi.transaction_lot_details sbi,sbi.transaction_lot l where pension_id in 
      		(select id from ".$table_name." where epic_voter_id='".$id."')
      		and sbi.lot_no=l.lot_no and l.lot_status<5 
      		group by pension_id"));

    	}
    	else if ($filter == 'ration') {
    		$id = $request->ration_card;
            //print $id;//die();
			  $ben_report = DB::select(DB::raw("select * from ".$table_name." where next_level_role_id=0 and trim(concat(ration_card_cat,ration_card_no))=trim('".$id."') and dist_code= ".$dist_code." and scheme_id= ".$scheme_id.""));
			  $ben_lot_report=DB::select(DB::raw("select pension_id,count(drn_part) from ifms.transaction_lot_details be,lot_master l where pension_id in 
        	(select id from ".$table_name." where concat(ration_card_cat,ration_card_no)='".$id."')
        	and be.drn_part=l.lot_no and l.voucher_no is null
        	group by pension_id"));
        $ben_lot_report_sbi=DB::select(DB::raw("select pension_id,count(sbi.lot_no) from sbi.transaction_lot_details sbi,sbi.transaction_lot l where pension_id in 
      		(select id from ".$table_name." where concat(ration_card_cat,ration_card_no)='".$id."')
      		and sbi.lot_no=l.lot_no and l.lot_status<5
      		group by pension_id"));
    	}
    	else {
    		$id = $request->ration_card;
			  $ben_report = DB::select(DB::raw("select * from ".$table_name." where next_level_role_id=0 and ration_card_no='".$id."' and dist_code= ".$dist_code." and scheme_id= ".$scheme_id.""));
			  $ben_lot_report=DB::select(DB::raw("select pension_id,count(drn_part) from ifms.transaction_lot_details be,lot_master l where pension_id in 
          (select id from ".$table_name." where concat(ration_card_cat,ration_card_no)='".$id."')
          and be.drn_part=l.lot_no and l.voucher_no is null
          group by pension_id"));
        $ben_lot_report_sbi=DB::select(DB::raw("select pension_id,count(sbi.lot_no) from sbi.transaction_lot_details sbi,sbi.transaction_lot l where pension_id in 
          (select id from ".$table_name." where concat(ration_card_cat,ration_card_no)='".$id."')
          and sbi.lot_no=l.lot_no and l.lot_status<5
          group by pension_id"));
		  }	

		  if (count($ben_lot_report) ==0 and count($ben_lot_report_sbi)==0) {
        if (count($ben_report) != 0) {
          return view('duplicate-approval.accept_one_beneficiary', ['ben_reports' => $ben_report, 'card_no' => $id, 'scheme_id' => $scheme_id]);
        }
        else {
          return redirect('duplicate-approval')->with('message', 'Something went wrong, please try after some time!');
        }
      }
		  else {
        return view('duplicate-approval.accept_one_beneficiary', ['ben_reports' => '', 'card_no' => $id, 'scheme_id' => $scheme_id]);
      }	
    }

    public function storeAcceptOneBen(Request $request){
      // Scheme Modal Check
      $scheme_id = $request->scheme_id;
      $scheme_code_map = Config::get('constants.scheme_code_map');
      $scheme_details = '';
      if(array_key_exists($scheme_id, $scheme_code_map)){
        $scheme_details = $scheme_code_map[$scheme_id];
        $scheme_model = 'App\\'.$scheme_details['model_name'];
      }
      else {
        $scheme_model = 'App\\'.'BeneficiaryPensions';
      }

    	//Checked Beneficiary Id
			//$checked_id = $request->check_id;
			$checkarr = explode('-', ($request->check_id));
			$checked_id = $checkarr[0];
    	$all_ben_id = explode(',',$request->ben_id);

      $next_count = $scheme_model::where('scheme_id',$scheme_id)->where('next_level_role_id',0)->whereIn('id',$all_ben_id)->count();

      if ($next_count == count($all_ben_id)) {
        $arr[] = $checked_id;
        //Not Checked Beneficiary Id
        $not_check_id = array_diff($all_ben_id, $arr);
        //Checked Beneficiary Fetch Data
        $benObj = $scheme_model::where('id',$checked_id)->first();
        $pay_count = $benObj->payment_count;
        $last_pay_year_month = $benObj->last_paid_yymm;

        //Payment Count Add to the final Update Beneficiary
        $payment_count_final = $request->form_pay_count - $pay_count;
        $pay_count_final_store = $request->form_pay_count;

        //Date
        $arr = str_split($last_pay_year_month, 2);
        $year = $arr[0];
        $month = $arr[1];
        $final_date = '20'.$year.'-'.$month;

        $date=date_create($final_date);
        $month = ($payment_count_final).' months';
        date_add($date,date_interval_create_from_date_string($month));
        $final_yymm = date_format($date,"ym");

        //Update Final Approved Beneficiary
        $update_input = [
          'payment_count' => $pay_count_final_store,
          'last_paid_yymm' => $final_yymm,
          'lot_generated' => 0
        ];

        DB::beginTransaction();
        try {
          if ($benObj->next_level_role_id == 0) {
            $scheme_model::where('scheme_id',$scheme_id)->where('id',$checked_id)->update($update_input);
            $payment_adjustment_checked=DB::select('SELECT public."payment_adjustment"('.$checked_id.', -1)');                
          }
          //User Id from Users table
          $user_id = AuthChecker::getUserId();
          //All Dupliacte Record Store in to database 
          foreach ($not_check_id as $id) {
            $dupObj = $scheme_model::where('scheme_id',$scheme_id)->where('id',$id)->first();
            if ($dupObj->next_level_role_id == 0) {
              //Update Unchecked beneficiary
              $scheme_model::where('scheme_id',$scheme_id)->where('id',$id)->update(['next_level_role_id' => -2]);
              $payment_adjustment_unchecked=DB::select('SELECT public."payment_adjustment"('.$id.', -2)');
              //Store unchecked Data in different table 
              $duplicateRejectObj = new DupliacteApproveReject();
              $duplicateRejectObj->original_approve_application_id = $checked_id;
              $duplicateRejectObj->original_application_id = $id;
              $duplicateRejectObj->rejected_user_id = $user_id;
              $duplicateRejectObj->dist_code = $dupObj->dist_code;
              $duplicateRejectObj->scheme_id = $dupObj->scheme_id;
              $duplicateRejectObj->ben_fname = $dupObj->ben_fname;
              $duplicateRejectObj->ben_mname = $dupObj->ben_mname;
              $duplicateRejectObj->ben_lname = $dupObj->ben_lname;
              $duplicateRejectObj->father_fname = $dupObj->father_fname;
              $duplicateRejectObj->father_mname = $dupObj->father_mname;
              $duplicateRejectObj->father_lname = $dupObj->father_lname;
              $duplicateRejectObj->ration_card_no = $dupObj->ration_card_no;
              $duplicateRejectObj->ration_card_cat = $dupObj->ration_card_cat;
              $duplicateRejectObj->epic_voter_id = $dupObj->epic_voter_id;
              $duplicateRejectObj->block_ulb_code = $dupObj->block_ulb_code;
              $duplicateRejectObj->block_ulb_name = $dupObj->block_ulb_name;
              $duplicateRejectObj->gp_ward_code = $dupObj->gp_ward_code;
              $duplicateRejectObj->gp_ward_name = $dupObj->gp_ward_name;
              $duplicateRejectObj->rural_urban_id = $dupObj->rural_urban_id;
              $duplicateRejectObj->bank_name = $dupObj->bank_name;
              $duplicateRejectObj->bank_code = $dupObj->bank_code;
              $duplicateRejectObj->bank_ifsc = $dupObj->bank_ifsc;

              $duplicateRejectObj->save();
            }
          }
          DB::commit();
          $notchecked = implode(',',$not_check_id);
          $msg = 'This '.$notchecked.' rejected!';  
          return redirect('duplicate-approval')->with('success', 'Approved Successfully!')->with('id', $checked_id)->with('message', $msg);
        } catch (\Exception $e) {
            $response = array(
              'exception_message' => $e->getMessage(),
              //'exception_message' => 'Oops. Something wrong. Please try agian later.'
            );
            DB::rollback();
            return redirect("duplicate-approval")->with('message', $response['exception_message']);
        }    
      }
      else {
        return redirect('duplicate-approval')->with('message', 'Some beneficiary is already rejected from this list. Please search again..');
      }
    }

    //Report Duplicate Approval
    public function duplicateApprove(Request $request){
      $user_id = AuthChecker::getUserId();
      //$schemeObj=Configduty::select('scheme_id')->where('user_id','=',$user_id)->where('is_active',1)->get();
      //$scheme = Scheme::whereIn('id',$schemeObj)->where('is_active',1)->get();
	$scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1) and is_active=1 order by scheme_name"));
		  $dutyObj = Configduty::where('user_id',$user_id)->first();
      $dist_code = $dutyObj->district_code;
      
      if(request()->ajax()) {  
        if(!empty($request->filter_1) ) {
          $query = "SELECT d.*,m.scheme_name FROM public.duplicate_approve_reject d JOIN public.m_scheme m ON d.scheme_id=m.id WHERE d.dist_code=".$dist_code." AND d.scheme_id=".$request->filter_1;
        }
        else {
          $query = "SELECT d.*,m.scheme_name FROM public.duplicate_approve_reject d JOIN public.m_scheme m ON d.scheme_id=m.id WHERE d.dist_code=".$dist_code;
        }
        $data=DB::connection('pgsql_mis')->select($query);
        return datatables()->of($data)
        ->addColumn('name', function ($data) {
            return $data->ben_fname.' '.$data->ben_mname.' '.$data->ben_lname;
        })
        ->addColumn('ration_card', function ($data) {
            return $data->ration_card_cat.' '.$data->ration_card_no;
        })
        ->rawColumns(['name','ration_card'])
        ->make(true); 
      }
      return view('report-duplicate-approval-reject/index', ['schemes' => $scheme]);
    }
}
