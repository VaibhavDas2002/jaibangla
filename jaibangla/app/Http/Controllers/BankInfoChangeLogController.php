<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\BenEntry;
use TCPDF;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

class BankInfoChangeLogController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // set_time_limit(0);
        // ini_set('max_execution_time', -1);
        // date_default_timezone_set('Asia/Kolkata');
    }
    public function index(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $url = 'bank-info-change-log-ps-master-entry';
            $case_id = (int) $request->case_id;
            if (empty($case_id) || $case_id == '') {
                $return_text = 'Case number not found';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url)->with('errors', $return_msg);
            }
            if (!is_int($case_id)) {
                $return_text = 'Case number not Valid';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url)->with('errors', $return_msg);
            }
            $ps_master_data = DB::select("SELECT  case_no,id from ben_bank_chage_log.m_police_case_no where id=" . $case_id);
            if (count($ps_master_data) == 0) {
                $return_text = 'Case number not found';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url)->with('errors', $return_msg);

            }
            return view(
                'BankChangeLog/index',
                [
                    'ps_master_data' => $ps_master_data[0],
                ]
            );
        } catch (\Exception $e) {
            dd($e);
        }

    }
    public function ps_master_entry()
    {
        try {
            $user_id = Auth::user()->id;
            $ps_data = array();
            $i = 0;
            $ps_master_data = DB::select("SELECT  case_no,id from ben_bank_chage_log.m_police_case_no order by created_at desc ");
            foreach ($ps_master_data as $ps_master_data_item) {
                $ps_data[$i]['id'] = $ps_master_data_item->id;
                $ps_data[$i]['case_no'] = $ps_master_data_item->case_no;
                $ps_data[$i]['no_data'] = 0;
                $cnt_arr = DB::select("SELECT count(distinct(ben_id)) as cnt from ben_bank_chage_log.police_case_details where int_case_no=" . $ps_master_data_item->id);
                $ps_data[$i]['no_data'] = intval($cnt_arr[0]->cnt);

                $i++;

            }
            return view(
                'BankChangeLog/ps_master_entry',
                [
                    'data' => $ps_data,

                ]
            );
        } catch (\Exception $e) {
            dd($e);
        }



    }
    public function ps_master_entry_post(Request $request)
    {
        $user_id = Auth::user()->id;

        try {
            $rules = [
                'ps_case_no' => 'required'
            ];
            $attributes = array();
            $messages = array();
            $attributes['ps_case_no'] = 'Police Case Number';
            $validator = Validator::make($request->all(), $rules, $messages, $attributes);
            if ($validator->passes()) {
                $case_no_ps = trim($request->ps_case_no);
                $count = DB::table('ben_bank_chage_log.m_police_case_no')->where('case_no', $case_no_ps)->count('id');
                if ($count > 0) {
                    $return_text = 'Police Case Number already exists....';
                    $return_msg = array("" . $return_text);
                    return redirect("/bank-info-change-log-ps-master-entry")->with('errors', $return_msg);

                }
                $insert_arr = array();
                $c_time = date('Y-m-d H:i:s');
                $server_ip = $_SERVER['SERVER_ADDR'];
                $insert_arr['case_no'] = $case_no_ps;
                $insert_arr['is_active'] = 1;
                $insert_arr['created_by'] = $user_id;
                $insert_arr['created_at'] = $c_time;
                $insert_arr['ip_address'] = $server_ip;
                $insert = DB::table('ben_bank_chage_log.m_police_case_no')->insert($insert_arr);
                if ($insert) {
                    return redirect("/bank-info-change-log-ps-master-entry")->with('success', 'Police Case Number Added Successfully');
                }
            } else {
                $error_msg = array();
                foreach ($validator->errors()->all() as $error) {
                    array_push($error_msg, $error);
                }
                return redirect("/bank-info-change-log-ps-master-entry")->with('errors', $error_msg);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function downloadlist(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $url = 'bank-info-change-log-ps-master-entry';
            $case_id = (int) $request->case_id;
            if (empty($case_id) || $case_id == '') {
                $return_text = 'Case number not found';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url)->with('errors', $return_msg);
            }
            if (!is_int($case_id)) {
                $return_text = 'Case number not Valid';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url)->with('errors', $return_msg);
            }
            $ps_master_data = DB::select("SELECT  case_no,id from ben_bank_chage_log.m_police_case_no where id=" . $case_id);
            if (count($ps_master_data) == 0) {
                $return_text = 'Case number not found';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url)->with('errors', $return_msg);

            }
            $ps_master_data = DB::select("SELECT  case_no,id from ben_bank_chage_log.m_police_case_no where id=" . $case_id);
            if (count($ps_master_data) == 0) {
                $return_text = 'Case number not found';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url)->with('errors', $return_msg);

            }
            $district_app_arr = DB::select("SELECT distinct(ben_id) as distinct_app_id from ben_bank_chage_log.police_case_details where int_case_no=" . $case_id);
            if (count($district_app_arr) == 0) {
                $return_text = 'No Applicant tagged yet';
                $return_msg = array("" . $return_text);
                return redirect("/bank-info-change-log?case_id=" . $case_id)->with('errors', $return_msg);

            }
            $distinct_app_id_arr = array();
            $i = 0;
            foreach ($district_app_arr as $app_id) {
                array_push($distinct_app_id_arr, $app_id->distinct_app_id);

            }
            //dd($distinct_app_id_arr);
            return view(
                'BankChangeLog/downloadlist',
                [
                    'ps_master_data' => $ps_master_data[0],
                    'distinct_app_id_arr' => $distinct_app_id_arr,

                ]
            );
        } catch (\Exception $e) {
            dd($e);
        }



    }

    public function getdataword(Request $request)
    {
        try {
        $case_id = (int) $request->case_id;
        $ben_id = (int) $request->application_id;
        $url = 'bank-info-change-log-ps-master-entry';
        $user_id = Auth::user()->id;
        if(isset($_POST['movelog'])){
            $count = DB::table('ben_bank_chage_log.pension_log')->where('ben_id', $ben_id)->count('id');
            DB::beginTransaction();
            if($count)
            $del=DB::table('ben_bank_chage_log.pension_log')->where('ben_id', $ben_id)->delete();
            else
            $del=1;
            $insert=DB::statement("insert into ben_bank_chage_log.pension_log (scheme_id, ben_id, change_type, original_data, new_data, action_tstamp)
            select scheme_id, ben_id, change_type, old_data , new_data, action_tstamp from tran_log.pension_log where ben_id=".$ben_id."
            UNION
             select scheme_id, ben_id, change_type, original_data as old_data, new_data, action_tstamp from tran_log.pension_log_back_27092024 where ben_id=".$ben_id."
            ");
            if($del && $insert){
                DB::commit();
                return redirect("/bank-info-change-log?case_id=" . $case_id)->with('success', 'Log has been moved successfully');;
            }
            else{
                
                DB::rollback();
                $return_text = 'Error. Please try again';
                $return_msg = array("" . $return_text);
                return redirect("/bank-info-change-log?case_id=" . $case_id)->with('errors', $return_msg);
            }

        }
        if(isset($_POST['savedata'])){
           // dd('ok2');
            $distinct_ip_list='';
            $ip_found = 0;
            $distinct_ip = '';
            $case_id = (int) $request->case_id;
            // dump($case_id);
            $url = 'bank-info-change-log?case_id=' . $case_id;
            $url1 = 'bank-info-change-log-ps-master-entry';
            if (empty($case_id) || $case_id == '') {
                $return_text = 'Case number not found.';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url1)->with('errors', $return_msg);
            }
            if (!is_int($case_id)) {
                $return_text = 'Case number not Valid..';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url1)->with('errors', $return_msg);
            }
            $ps_master_data = DB::select("SELECT  case_no,id from ben_bank_chage_log.m_police_case_no where id=" . $case_id);
            if (count($ps_master_data) == 0) {
                $return_text = 'Case number not found...';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url1)->with('errors', $return_msg);
            }
    
            $ben_id = (int) $request->application_id;
    
    
            if (empty($ben_id) || $ben_id == '') {
                $return_text = 'Application not found';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url1)->with('errors', $return_msg);
            }
            if (!is_int($ben_id)) {
                $return_text = 'Application not Valid';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url1)->with('errors', $return_msg);
            }
    
            // dump($ben_id);
            $ben_info = BenEntry::where('id', $ben_id)->first();
            // dump($ben_info);
            if (empty($ben_info)) {
                $return_text = 'Application Id Not Found';
                $return_msg = array("" . $return_text);
                return redirect("/" . $url)->with('errors', $return_msg);
            }
    
            $ben_name = $ben_info->ben_fname . ' ' . $ben_info->ben_mname . ' ' . $ben_info->ben_lname;
            $created_by_dist_code = $ben_info->created_by_dist_code;
            $created_by_dist_code_row = DB::table("public.m_district")->where('district_code', $created_by_dist_code)->first();
           // dd(strlen($ben_info->created_by_local_body_code));
            if (strlen($ben_info->created_by_local_body_code) == 6) {
                $created_by_local_body_code_row = DB::table("public.m_sub_district")->where('sub_district_code', $ben_info->created_by_local_body_code)->first();
                $block_sub_div_name = 'SUBDIVISION-' . strtoupper(trim($created_by_local_body_code_row->sub_district_name));
                
            } else {
                $created_by_local_body_code_row = DB::table("public.m_block")->where('block_code', $ben_info->created_by_local_body_code)->first();
                $block_sub_div_name = 'BLOCK-' . strtoupper(trim($created_by_local_body_code_row->block_name));
            }
            // dump($created_by_dist_code_row);
            $beneficiary_details = [
                'ben_id' => $ben_info->id,
                'ben_name' => $ben_name,
                'ben_block' => $block_sub_div_name,
                'ben_district' => strtoupper(trim($created_by_dist_code_row->district_name)),
            ];
            $ps_already_count = DB::select("SELECT  count(1) as cnt from ben_bank_chage_log.police_case_details where ben_id=" . $ben_id . " and int_case_no=" . $case_id);
    
            if ($ps_already_count[0]->cnt == 0) {
                $c_time = date('Y-m-d H:i:s');
                if ($ben_id > 0) {
                    // $is_inserted_status_arr = DB::select("select ben_bank_chage_log.bank_info_change_log_final(in_district_code => $created_by_dist_code,in_created_by_local_body_code => $ben_info->created_by_local_body_code,in_created_by_level => '".$ben_info->created_by_level."', in_action_by => $user_id,in_action_ip_address => '".request()->ip()."',in_action_time => '".$c_time."',in_case_id => " . $case_id . ",in_application_id => " . $application_id . ",in_ben_id => $ben_id)");                       
                    $is_inserted_status_arr = DB::select("select ben_bank_chage_log.bank_info_change_log_final(in_district_code => $created_by_dist_code,in_created_by_local_body_code => $ben_info->created_by_local_body_code,in_created_by_level => '" . $ben_info->created_by_level . "', in_action_by => $user_id,in_action_ip_address => '" . request()->ip() . "',in_action_time => '" . $c_time . "',in_case_id => " . $case_id . ",in_application_id => " . $ben_id . ",in_ben_id => $ben_id)");
    
                    $is_inserted_status = $is_inserted_status_arr[0]->bank_info_change_log_final;
                    //dd($is_inserted_status);
                } else {
                    $is_inserted_status = 1;
                }
            } else {
                $is_inserted_status = 1;
            }
            if($is_inserted_status==1){ 
                $ip_list_row = DB::select("select distinct(ip_address) as ip_list from ben_bank_chage_log.police_case_details where  int_case_no=".$case_id." and ip_address IS NOT NULL and ben_id=".$ben_id);
                $distinct_ip=array();
                if(count($ip_list_row)>0){
                  $ip_found=1; 
                  if(count($ip_list_row)>1){ 
                   foreach($ip_list_row as $ip_row){
                   array_push($distinct_ip,$ip_row->ip_list);
      
                  }
                  $distinct_ip_list=' ('.implode(', ',  $distinct_ip).') ';
                  }
                  else{
                    $distinct_ip_list=' ('.$ip_list_row[0]->ip_list.') ';
                  }
      
      
                 
                }
            
                
                
                
                $applicant_activity_list = DB::select("SELECT created_at, slno, activity_name, op_type, int_case_no,ben_id,user_id,ifsc,accno,trim(old_data->>'bank_ifsc') as old_ifsc,trim(old_data->>'bank_code') as old_accno,trim(new_data->>'bank_ifsc') as new_ifsc,trim(new_data->>'bank_code') as new_accno,status,status_code,av_account_status,name_status
          FROM ben_bank_chage_log.police_case_details  where int_case_no=".$case_id." and ben_id=".$ben_id."   order by slno");
                $applicant_activity=array();
                $i=0;
                //dd($applicant_activity_list);
                foreach($applicant_activity_list as $activity_item){
                  $applicant_activity[$i]['activity_name']= $activity_item->activity_name;
                  $applicant_activity[$i]['activity_time']= $activity_item->created_at;
                  $user_details='Not Applicable';
                  $banking_details='Not Applicable';
                  $acc_validation_details='Not Applicable';
                  $payment_details='Not Applicable';
                  if($activity_item->user_id>0){
                  $user_details=$this->get_user_details($activity_item->int_case_no,$activity_item->ben_id,$activity_item->slno,$activity_item->user_id);
                  }
                  else
                  $user_details='Not Applicable';
                  $applicant_activity[$i]['user_details']= $user_details;
                  $is_bank_change=DB::table('m_update_code')->where('code',$activity_item->op_type)->where('is_bank_chnage',1)->count();           
                  
                  if($is_bank_change>0){
                    $banking_details='Bank Ifsc-'.trim($activity_item->new_ifsc).', Bank A/c No.-'.trim($activity_item->new_accno);
                  }
                  $applicant_activity[$i]['banking_details']= $banking_details;
                  if(in_array($activity_item->op_type, array('AVLC'))){
                    if((is_null($activity_item->status) || trim($activity_item->status)=='') && (is_null($activity_item->av_account_status) || trim($activity_item->av_account_status)=='') && (is_null($activity_item->name_status) || trim($activity_item->name_status)=='')){
                      $acc_validation_details='Response Yet to Receive';
                    }
                    else if($activity_item->status=='N' && $activity_item->av_account_status=='N' && ($activity_item->name_status=='Y')){
                    $acc_validation_details='Account Validation Failed';
                    }
                    else if($activity_item->status=='Y' && $activity_item->av_account_status=='Y' && (is_null($activity_item->name_status) || trim($activity_item->name_status)=='')){
                      $acc_validation_details='Account Validation Success';
                    }
                    else if($activity_item->status=='N' && $activity_item->av_account_status=='N' && (is_null($activity_item->name_status) || trim($activity_item->name_status)=='')){
                      $acc_validation_details='Account Validation Failed';
                    }
                    else if($activity_item->status=='N' && $activity_item->av_account_status=='N' && ($activity_item->name_status=='N')){
                      $acc_validation_details='Account Validation Failed';
                    }
                    else if($activity_item->status=='Y' && $activity_item->av_account_status=='Y' && (is_null($activity_item->name_status) || trim($activity_item->name_status)=='')){
                      $acc_validation_details='Account Validation Success';
                    }
                    else if(($activity_item->status=='Y') && (is_null($activity_item->av_account_status) || trim($activity_item->av_account_status)=='') && (is_null($activity_item->name_status) || trim($activity_item->name_status)=='')){
                        $acc_validation_details='Account Validation Success';
                    }
                    else if($activity_item->status=='Y' && $activity_item->av_account_status=='Y' && ($activity_item->name_status=='Y')){
                      $acc_validation_details='Account Validation Success';
                    }
                    else if($activity_item->status=='Y' && $activity_item->av_account_status=='Y' && ($activity_item->name_status=='N')){
                        $acc_validation_details='Account Validation Success';
                    }
                    else if($activity_item->status=='N' && $activity_item->av_account_status=='Y' && ($activity_item->name_status=='N')){
                          $acc_validation_details='Name Validation Failed';
                    }
                }
                 $applicant_activity[$i]['acc_validation_details']= $acc_validation_details;
                   if(in_array($activity_item->op_type, array('PLC'))){
                    $error_description=$this->get_err_desciption(trim($activity_item->status_code));   
                    $payment_details='Payment Failed due to the reason '.$error_description;              
                   }
                   $applicant_activity[$i]['payment_details']= $payment_details;
                  $i++;
                  
                }
                //dd($applicant_activity);
                $pdfContent = View::make('BankChangeLog/pdf_details', [
                  'beneficiary_details' => $beneficiary_details,
                  'ip_found' => $ip_found,
                  'distinct_ip' => $distinct_ip_list,
                  'applicant_activity' => $applicant_activity,
            
                ])->render();
            
                // Initialize TCPDF object
                $pdf = new TCPDF('P', 'mm', 'A4');  // Set page orientation to Portrait and size to A4
            // $pdf->SetCreator(PDF_CREATOR); // Uncomment if needed
                $pdf->SetAuthor('Jai Bangla');
                $pdf->SetTitle('Applicant Bank Info Change Activity Details');
                $pdf->SetSubject('Applicant Activity PDF');
            
                // Set fonts (uncomment this line if you want to customize the font)
            // $pdf->SetFont('dejavusans', '', 10);
            
                // Add a page and set margins
                $pdf->AddPage();
                $pdf->SetMargins(10, 5, 10);  // Adjusting margins for the page
            
                // Adjust the HTML content to fit within the A4 page
            // Setting 'true' to enable automatic page breaks for long content
                $pdf->writeHTML($pdfContent, true, false, true, false, '');
            
                // Output the PDF as a download (file name format)
                return $pdf->Output('Applicant_Details_'. $ben_id .'_'.time().'.pdf', 'D');
              }
              else{
                  $return_text = 'Something went wrong.. please try after some times';
                  $return_msg = array("" . $return_text);
                  return redirect("/".$url)->with('errors', $return_msg);
              }
    
        }
        

    } catch (\Exception $e) {
        dd($e);
    }



    }
    public function get_user_details($case_no,$ben_id,$slno,$user_id)
    
    {
     
     $user_details='';
     $user_row = DB::table("ben_bank_chage_log.police_case_user_details")->where('int_case_no', $case_no)->where('ben_id', $ben_id)->where('slno', $slno)->where('user_id', $user_id)->first();
     if($user_row->created_by_level=='District'){
       $user_details=$user_row->district_name;
     }
     else{
       $user_details=$user_row->block_sub_div_name;
     }
     if($user_row->created_by_level=='District'){
        $created_by_level='District';
      }
      else{
        if(strlen($user_row->created_by_local_body_code)==6){
            $created_by_level='SubDivision';
        }
        elseif(strlen($user_row->created_by_local_body_code)==4){
            $created_by_level='Block';
        }
        
      }
     $user_details=$user_details." " .$created_by_level." " .$user_row->designation_id." (Mobile Number-".$user_row->mobile_no." , User Name-".$user_row->username.", Email Id-".$user_row->email.")";
    
     return $user_details;
 
    }

}
