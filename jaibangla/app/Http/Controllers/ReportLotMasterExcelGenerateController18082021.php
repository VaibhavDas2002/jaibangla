<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Auth;
use App\Configduty;
use Excel;
use App\lot_master;
use App\Scheme;

class ReportLotMasterExcelGenerateController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth');
        //$this->middleware('Admin');
        set_time_limit(300);
    }

    public function selectYearMonth(){
        $user_id = Auth::user()->id;
        $schemes=Configduty::where('user_id','=',$user_id)->where('is_active',1)->get();
        return view('report-lot-master-all-info-payment-xls/selectYearMonth',['schemes'=>$schemes]);
    }

    public function index(Request $request)
    {
		$scheme_id = $request->scheme_id;//new
		$lot_year = $request->lot_year;//new
		$lot_month = $request->lot_month;//new

		$schemeObj = Scheme::where('id',$scheme_id)->first();
		
        $report = DB::select(DB::raw("select l.scheme_id,l.lot_no,l.lot_year,l.lot_month,l.lot_status,l.push_to_ifms_status,l.dotdone_status,l.ack_status,l.ref_no,
        l.wrongdata_status,l.repeat_lot,l.repeat_drn_part,l.voucher_no, l.ben_count, l.ifms_wrongdata_count, 
        (l.ben_count-(case when l.ifms_wrongdata_count is null then 0 else l.ifms_wrongdata_count end)) pmt_mandate,
        l.rbi_failed_count, l.rbi_success_count, exists (select lot_no from ifms.temp_lot_master where lot_no=l.lot_no and rbi_sent_count=(rbi_receive_success_count+rbi_receive_failed_count)) rbi_flag 
        from lot_master l where scheme_id = ".$scheme_id." and lot_year = '".$lot_year."' and lot_month = '".$lot_month."' order by l.lot_no DESC"));
        
		//$rbi_status = DB::select(DB::raw("select lot_no from temp_lot_master where scheme_id IN (select id from m_scheme where ddo_code IN 
		//(select username from users where mobile_no='".$mobile."' and designation_id_old='DDO')) order by lot_no DESC"));
		
        return view('report-lot-master-all-info-payment-xls/result_lot_master_report', ['reports' => $report, 'scheme_name' => $schemeObj->scheme_name]);
    }

    public function lotWiseGenerateExcelFunction(Request $request){
        $lot_no = $request->lot_no;
		$scheme_id = $request->scheme_id;
        $payModeObj = lot_master::where('lot_no',$lot_no)->first();
        $type = $request->error_type;
        // Payment Mode : IFMS
        /* 
        	Type-E1 : IFMS Payment Initial Screening Error
        	Type-E2 : IFMS Payment Process Error
        	Type-S0 : IFMS Payment Success 
        */
        // ====================
        $query_sbi='';
        $query_ifms='';
        
     
        if ($payModeObj->payment_mode == 'IFMS') {
            $ifms_tld_report=DB::table('ifms.transaction_lot_details_report')->where('drn_part',$lot_no)
            ->where('scheme_id',$scheme_id)->count();
        	if ($type == 'E1') {
                if($ifms_tld_report>0){
                    $query_ifms="select be.pension_id as pension_id, be.name as name, be.acc_no as acc_no, be.ifsc as ifsc, be.mobile_no as mobile_no, be.ifms_status as err_reason, be.utr_no as utr_no  
                    from ifms.transaction_lot_details_report be where wrongdata_flag=1 and ifms_ref_no=0 and drn_part='".$lot_no."' and scheme_id=".$scheme_id.";";

                }
                else{
                    $query_ifms="select be.pension_id as pension_id, be.name as name, be.acc_no as acc_no, be.ifsc as ifsc, be.mobile_no as mobile_no, be.ifms_status as err_reason, be.utr_no as utr_no  
                    from ifms.transaction_lot_details be where wrongdata_flag=1 and ifms_ref_no=0 and drn_part='".$lot_no."' and scheme_id=".$scheme_id.";";
                }

        		$result = DB::select(DB::raw($query_ifms));
				$file_name = 'Lot-'.$lot_no.' Initial Screening Error';
        	}
            elseif ($type == 'E2') {
                // if($scheme_id==6 || $scheme_id==7){
                //     if($scheme_id==6){
                //         $table='msme.beneficiary';
                //     }
                //     else{
                //         $table='weavers.beneficiary';
                //     }
                // if ($scheme_id) {
                  $sObj = Scheme::where('id',$scheme_id)->first();
                  if (!empty($sObj)) {
                    $table = $sObj->short_code.'.beneficiary';
                  }
                  else{
                    $table = 'pension.beneficiary';
                  }
                   if($ifms_tld_report>0){
                    $query_ifms="select m.district_name,pb.block_ulb_name, be.pension_id as pension_id, be.name as name, be.acc_no as acc_no, be.ifsc as ifsc,
                    be.mobile_no as mobile_no, be.ifms_status as err_reason, be.utr_no as utr_no  
                    from ifms.transaction_lot_details_report be  join m_district m
                    on m.district_code=be.dist_code join ".$table." pb 
                    on pb.id=be.pension_id where wrongdata_flag=2 and ifms_ref_no>0 and drn_part='".$lot_no."' and be.scheme_id=".$scheme_id." order by m.district_name;";
                   }
                   else{
                    $query_ifms="select m.district_name,pb.block_ulb_name, be.pension_id as pension_id, be.name as name, be.acc_no as acc_no, be.ifsc as ifsc,
                    be.mobile_no as mobile_no, be.ifms_status as err_reason, be.utr_no as utr_no  
                    from ifms.transaction_lot_details be  join m_district m
                    on m.district_code=be.dist_code join ".$table." pb 
                    on pb.id=be.pension_id where wrongdata_flag=2 and ifms_ref_no>0 and drn_part='".$lot_no."' and be.scheme_id=".$scheme_id." order by m.district_name;";
                   }
                    $result = DB::select(DB::raw($query_ifms));
                    $file_name = 'Lot-'.$lot_no.' Payment Error';
                    $this->generateExcelIFMSError($result, $file_name); 
                // }
                // else{
                //     $result = DB::select(DB::raw("select be.pension_id as pension_id, be.name as name, be.acc_no as acc_no, be.ifsc as ifsc, be.mobile_no as mobile_no, be.ifms_status as err_reason, be.utr_no as utr_no  
                //     from ifms.transaction_lot_details be where wrongdata_flag=2 and ifms_ref_no>0 and drn_part='".$lot_no."' and scheme_id=".$scheme_id.";"));
                //     $file_name = 'Lot-'.$lot_no.' Payment Error';
                // }
            	  
            }
            elseif ($type == 'S0') {
                if($ifms_tld_report>0){
                    $query_ifms="select be.pension_id as pension_id, be.name as name, be.acc_no as acc_no, be.ifsc as ifsc, be.mobile_no as mobile_no, (case when trim(be.ifms_status)='Payment Mandate Generated' then 'SUCCESS' else be.ifms_status end) as err_reason, be.utr_no as utr_no 
				from ifms.transaction_lot_details_report be where wrongdata_flag=0 and ifms_ref_no>0 and drn_part='".$lot_no."' and scheme_id=".$scheme_id.";";
                }
                else{
                    $query_ifms="select be.pension_id as pension_id, be.name as name, be.acc_no as acc_no, be.ifsc as ifsc, be.mobile_no as mobile_no, (case when trim(be.ifms_status)='Payment Mandate Generated' then 'SUCCESS' else be.ifms_status end) as err_reason, be.utr_no as utr_no 
				from ifms.transaction_lot_details be where wrongdata_flag=0 and ifms_ref_no>0 and drn_part='".$lot_no."' and scheme_id=".$scheme_id.";";
                }

            	$result = DB::select(DB::raw($query_ifms));
				$file_name = 'Lot-'.$lot_no.' Payment Success';
            }
            else{
                if($ifms_tld_report>0){
                    $query_ifms= "select be.pension_id as pension_id, be.name as name, be.acc_no as acc_no, be.ifsc as ifsc, be.mobile_no as mobile_no, (case when trim(be.ifms_status)='Payment Mandate Generated' then 'SUCCESS' else be.ifms_status end) as err_reason, be.utr_no as utr_no 
                    from ifms.transaction_lot_details_report be where drn_part='".$lot_no."' and scheme_id=".$scheme_id.";";
                }
                else{
                    $query_ifms="select be.pension_id as pension_id, be.name as name, be.acc_no as acc_no, be.ifsc as ifsc, be.mobile_no as mobile_no, (case when trim(be.ifms_status)='Payment Mandate Generated' then 'SUCCESS' else be.ifms_status end) as err_reason, be.utr_no as utr_no 
                    from ifms.transaction_lot_details be where drn_part='".$lot_no."' and scheme_id=".$scheme_id.";";
                }
            	$result = DB::select(DB::raw($query_ifms));
				$file_name = 'Lot-'.$lot_no.' Total Beneficiary';
            }
            $this->generateExcelIFMS($result, $file_name);   
        }
        // Payment Mode : SBI
        /*
			Type-E1 : Nothing
			Type-E2 : SBI Payment Error
			Type-S0 : SBI Payment Success
        */
        // ===================
        else if ($payModeObj->payment_mode == 'SBI') {
            $sbi_tld_report=DB::table('sbi.transaction_lot_details_report')->where('lot_no',$lot_no)
        ->where('scheme_id',$scheme_id)->count();
            if ($type == 'E1') {
        		print 'Data not found!!';die();
        	}
            elseif ($type == 'E2') {
              $sObj = Scheme::where('id',$scheme_id)->first();
              if (!empty($sObj)) {
                $table = $sObj->short_code.'.beneficiary';
              }
              else{
                $table = 'pension.beneficiary';
              }
             
              if( $sbi_tld_report>0){
                $query_sbi="select m.district_name,pb.block_ulb_name, tld.pension_id as pension_id, tld.name as name, tld.account_credit as acc_no, tld.ifsc_code_credit as ifsc,
                (select error.description as status from sbi.credit_transaction_code as error where error.code=tld.status_code) 
                from sbi.transaction_lot_details_report tld join ".$table." pb 
      on pb.id=tld.pension_id join m_district m
      on m.district_code=pb.created_by_dist_code 
                where status_code like '%E%' and debit_reference in
                (select debit_reference from sbi.transaction_lot where lot_no='".$lot_no."');";
              }
              else{
                $query_sbi="select m.district_name,pb.block_ulb_name, tld.pension_id as pension_id, tld.name as name, tld.account_credit as acc_no, tld.ifsc_code_credit as ifsc,
                (select error.description as status from sbi.credit_transaction_code as error where error.code=tld.status_code) 
                from sbi.transaction_lot_details tld join ".$table." pb 
      on pb.id=tld.pension_id join m_district m
      on m.district_code=pb.created_by_dist_code 
                where status_code like '%E%' and debit_reference in
                (select debit_reference from sbi.transaction_lot where lot_no='".$lot_no."');";
              }
             
             // echo  $query_sbi;die;

             

            	$result = DB::select(DB::raw($query_sbi));
            	$file_name = 'Lot-'.$lot_no.' Payment Error';
              $this->generateExcelSBIError($result, $file_name); 
            }
            elseif ($type == 'S0') {

                if( $sbi_tld_report>0){
                $query_sbi="select tld.pension_id as pension_id, tld.name as name, tld.account_credit as acc_no, tld.ifsc_code_credit as ifsc,
					(select error.description as status from sbi.credit_transaction_code as error where error.code=tld.status_code) 
					from sbi.transaction_lot_details_report tld 
					where status_code like '%S%' and debit_reference in
					(select debit_reference from sbi.transaction_lot where lot_no='".$lot_no."');";
                }
                else{
                    $query_sbi="select tld.pension_id as pension_id, tld.name as name, tld.account_credit as acc_no, tld.ifsc_code_credit as ifsc,
					(select error.description as status from sbi.credit_transaction_code as error where error.code=tld.status_code) 
					from sbi.transaction_lot_details tld 
					where status_code like '%S%' and debit_reference in
					(select debit_reference from sbi.transaction_lot where lot_no='".$lot_no."');";
                }


            	$result = DB::select(DB::raw($query_sbi));
            	$file_name = 'Lot-'.$lot_no.' Payment Success';
            }
            else{

                if( $sbi_tld_report>0){
                    $query_sbi="select tld.pension_id as pension_id, tld.name as name, tld.account_credit as acc_no, tld.ifsc_code_credit as ifsc,
                    (select error.description as status from sbi.credit_transaction_code as error where error.code=tld.status_code) 
                    from sbi.transaction_lot_details_report tld 
                    where debit_reference in
                    (select debit_reference from sbi.transaction_lot where lot_no='".$lot_no."');";
                }
                else{
                    $query_sbi="select tld.pension_id as pension_id, tld.name as name, tld.account_credit as acc_no, tld.ifsc_code_credit as ifsc,
                    (select error.description as status from sbi.credit_transaction_code as error where error.code=tld.status_code) 
                    from sbi.transaction_lot_details tld 
                    where debit_reference in
                    (select debit_reference from sbi.transaction_lot where lot_no='".$lot_no."');";
                }
            	$result = DB::select(DB::raw($query_sbi));


                
				$file_name = 'Lot-'.$lot_no.' Total Beneficiary';
            }
            $this->generateExcelSBI($result, $file_name);
        }
    }

    // Generate Excel IFMS
    public function generateExcelIFMS($result, $file_name){
        $ben[] = array('Pension Id','Name','Bank Acc No','IFSC Code','Mobile No','Status','UTR No');
        foreach($result as $arr)
        {
          $ben[] = array(
            
           'Pension Id' => trim($arr->pension_id), 
           'Name'  => trim($arr->name),
           'Bank Acc No' => trim($arr->acc_no),
           'IFSC Code' => trim($arr->ifsc),
           'Mobile No' => trim($arr->mobile_no),
           'Status' => trim($arr->err_reason),
           'UTR No' => trim($arr->utr_no)
          );
        }
        // print_r($ben);die();
        Excel::create($file_name, function($excel) use ($ben){
          $excel->setTitle('Beneficiary Data');
          $excel->sheet('Beneficiary Data', function($sheet) use ($ben){
           $sheet->fromArray($ben, null, 'A1', false, false);
          });
        })->download('xlsx');
    }
    public function generateExcelIFMSError($result, $file_name){
        $ben[] = array('District Name','Block/Municipality','Pension Id','Name','Bank Acc No','IFSC Code','Mobile No','Status','UTR No');
        foreach($result as $arr)
        {
          $ben[] = array(
            'District Name' => trim($arr->district_name), 
            'Block/Municipality' => trim($arr->block_ulb_name), 
           'Pension Id' => trim($arr->pension_id), 
           'Name'  => trim($arr->name),
           'Bank Acc No' => trim($arr->acc_no),
           'IFSC Code' => trim($arr->ifsc),
           'Mobile No' => trim($arr->mobile_no),
           'Status' => trim($arr->err_reason),
           'UTR No' => trim($arr->utr_no)
          );
        }
        // print_r($ben);die();
        Excel::create($file_name, function($excel) use ($ben){
          $excel->setTitle('Beneficiary Data');
          $excel->sheet('Beneficiary Data', function($sheet) use ($ben){
           $sheet->fromArray($ben, null, 'A1', false, false);
          });
        })->download('xlsx');
    }
    
    // Generate Excel SBI
    public function generateExcelSBI($result, $file_name){
        $ben[] = array('Pension Id','Name','Bank Acc No','IFSC Code','Status');
        foreach($result as $arr)
        {
          $ben[] = array(
           'Pension Id' => trim($arr->pension_id), 
           'Name'  => trim($arr->name),
           'Bank Acc No' => trim($arr->acc_no),
           'IFSC Code' => trim($arr->ifsc),
           'Status' => trim($arr->status)
          );
        }
        // print_r($ben);die();
        Excel::create($file_name, function($excel) use ($ben){
          $excel->setTitle('Beneficiary Data');
          $excel->sheet('Beneficiary Data', function($sheet) use ($ben){
           $sheet->fromArray($ben, null, 'A1', false, false);
          });
        })->download('xlsx');
    }

    public function generateExcelSBIError($result, $file_name){
      $ben[] = array('District Name','Block/Municipality','Pension Id','Name','Bank Acc No','IFSC Code','Status');
      foreach($result as $arr)
      {
        $ben[] = array(
          'District Name' => trim($arr->district_name), 
            'Block/Municipality' => trim($arr->block_ulb_name), 
         'Pension Id' => trim($arr->pension_id), 
         'Name'  => trim($arr->name),
         'Bank Acc No' => trim($arr->acc_no),
         'IFSC Code' => trim($arr->ifsc),
         'Status' => trim($arr->status)
        );
      }
      // print_r($ben);die();
      Excel::create($file_name, function($excel) use ($ben){
        $excel->setTitle('Beneficiary Data');
        $excel->sheet('Beneficiary Data', function($sheet) use ($ben){
         $sheet->fromArray($ben, null, 'A1', false, false);
        });
      })->download('xlsx');
  }
}
