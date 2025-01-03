<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\District;
use App\Scheme;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;
use App\DataSourceCommon;
use App\getModelFunc;
use Illuminate\Support\Facades\Crypt;
use App\SubDistrict;
use App\Taluka;
use App\DocumentType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class lb60MigratioController extends Controller
{

    public function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->scheme_id = 20;
        $this->source_type = 'ss_nfsa';
        $this->ben_status = -97;
    }
    function pushtojb(Request $request)
    {
        $logmessage = "";
        $district_code=$request->district_code;
        $scheme_id=$request->scheme_id;
        if(empty($district_code)){
            $district_code=303;
        }
        if(empty($scheme_id)){
            $scheme_id=10;
        }
        if($scheme_id==10){
            $caste='OTHERS';
        }
        if($scheme_id==1){
            $caste='ST';
        }
        if($scheme_id==3){
            $caste='SC';
        }
        $fileLocation = 'lb60Migration/log.txt';
        try {
            $c_time = date('Y-m-d H:i:s', time());
            $logmessage .=  "LB 60 Migration Cron has been started for district ".$district_code." for Caste ".$caste." on " . date("Y-m-d h:i:s") . "." . "\n";
            $query = "select P.*, 
            Q.dist_code, Q.police_station, Q.rural_urban_id, 
            Q.block_ulb_code, Q.block_ulb_name, Q.block_ulb_type, Q.gp_ward_code, Q.gp_ward_name, Q.village_town_city, Q.house_premise_no, 
            Q.post_office, Q.pincode, Q.residency_period,
            R.bank_code, R.bank_name, 
            R.branch_name, R.bank_ifsc,
            S.aadhar_hash as aadhar_hash,
            S.encoded_aadhar as encoded_aadhar,
			S.decoded_aadhar as aadhar_no,
            '0' as is_faulty
            from
            (
            SELECT application_id, beneficiary_id, ben_fname as ss_full_name, gender, dob, 
            caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
            mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, ss_card_no,mobile_no,created_by_dist_code, created_by_level, 
            created_by_local_body_code, created_at,created_by,caste_certificate_no, next_level_role_id, duare_sarkar_registration_no, 
            duare_sarkar_date, email,approval_date,ds_phase, 
              approval_date	FROM lb_scheme.ben_personal_details where is_sent_jb IS NULL and is_aadhar_dup IS NULL and AGE(CURRENT_DATE, dob)>'60 years' and caste=".$caste." and created_by_dist_code=".$district_code."
            ) as P LEFT JOIN lb_scheme.ben_contact_details as Q ON P.application_id=Q.application_id 
            LEFT JOIN lb_scheme.ben_bank_details as R ON P.application_id=R.application_id 
            LEFT JOIN aadhar.ben_aadhar_details as S ON P.application_id=S.application_id where Q.created_by_dist_code=".$district_code." and 
			 R.created_by_dist_code=".$district_code." and S.created_by_dist_code=".$district_code."
            UNION
            select P.*, 
            Q.dist_code, Q.police_station, Q.rural_urban_id, 
            Q.block_ulb_code, Q.block_ulb_name, Q.block_ulb_type, Q.gp_ward_code, Q.gp_ward_name, Q.village_town_city, Q.house_premise_no, 
            Q.post_office, Q.pincode, Q.residency_period,
            R.bank_code, R.bank_name, 
            R.branch_name, R.bank_ifsc,
            S.aadhar_hash as aadhar_hash,
            S.encoded_aadhar as encoded_aadhar,
            S.decoded_aadhar as aadhar_no,
            '1' as is_faulty
            from
            (
            SELECT application_id, beneficiary_id, ben_fname as ss_full_name, gender, dob, 
            caste, marital_status, father_fname, father_mname, father_lname, mother_fname, 
            mother_mname, mother_lname, spouse_fname, spouse_mname, spouse_lname, ss_card_no,mobile_no,created_by_dist_code, created_by_level, 
            created_by_local_body_code, created_at,created_by,caste_certificate_no,  next_level_role_id, duare_sarkar_registration_no, 
            duare_sarkar_date, email,approval_date,ds_phase, 
              approval_date	FROM lb_scheme.faulty_ben_personal_details where is_sent_jb IS NULL and is_aadhar_dup IS NULL and AGE(CURRENT_DATE, dob)>'60 years' and caste=".$caste." and created_by_dist_code=" . $district_code . " 
            ) as P LEFT JOIN lb_scheme.faulty_ben_contact_details as Q ON P.application_id=Q.application_id
            LEFT JOIN lb_scheme.faulty_ben_bank_details as R ON P.application_id=R.application_id
            LEFT JOIN aadhar.ben_aadhar_details as S ON P.application_id=S.application_id";
            $result_set = DB::connection('pgsql_lb_mainwrite')->select($query);
            dd($result_set);
            $i = 0;
            $total_data=count($result_set);
            $update_main_arr=array();
            $update_faulty_arr=array();
            $insert_arr=array();
            if($total_data>0){
            
            $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
            if (!empty($scheme_obj->short_code)) {
                $schema = $scheme_obj->short_code;
            } else {
                $schema = "pension";
            }
            $c_time = date('Y-m-d H:i:s', time());
            DB::beginTransaction();
            DB::connection('pgsql_lb_mainwrite')->beginTransaction();
            foreach ($result_set as $arr) {
                $is_faulty = $arr->is_faulty;
                if ($is_faulty == 1) {
                    array_push($update_faulty_arr, $arr->application_id);
                }else if ($is_faulty == 0) {
                    array_push($update_main_arr, $arr->application_id);
                }
                
              
                    $insert_arr[$i]['lb_application_id']= $arr->application_id;
                    $insert_arr[$i]['beneficiary_id']= $arr->beneficiary_id;
                    if ($is_faulty == 1) {
                     $insert_arr[$i]['ben_full_name']= trim($arr->ben_fname);
                     $insert_arr[$i]['ben_fname']= trim($arr->ben_fname);
                    }
                    else{
                    $name=trim($arr->ben_fname);
                    if(!empty(trim($arr->ben_mname)) && $arr->ben_mname!='NA'){
                        $name=$name.' '.$arr->ben_mname;
                    }
                    if(!empty(trim($arr->ben_lname)) && $arr->ben_mname!='NA'){
                        $name=$name.' '.$arr->ben_lname;
                    }
                    $insert_arr[$i]['ben_full_name']= $name;
                    $insert_arr[$i]['ben_fname']= $name;
                    }
                    $insert_arr[$i]['gender']= 'Female';
                    $insert_arr[$i]['dob']= $arr->dob;
                    $insert_arr[$i]['caste']= $arr->caste;
                    $insert_arr[$i]['marital_status']= trim($arr->marital_status);
                    $insert_arr[$i]['father_fname']= trim($arr->father_fname);
                    $insert_arr[$i]['father_mname']= trim($arr->father_mname);
                    $insert_arr[$i]['father_lname']= trim($arr->father_lname);
                    $insert_arr[$i]['mother_fname']= trim($arr->mother_fname);
                    $insert_arr[$i]['mother_mname']= trim($arr->mother_mname);
                    $insert_arr[$i]['mother_lname']= trim($arr->mother_lname);
                    $insert_arr[$i]['spouse_fname']= trim($arr->spouse_fname);
                    $insert_arr[$i]['spouse_mname']= trim($arr->spouse_mname);
                    $insert_arr[$i]['spouse_lname']= trim($arr->spouse_lname);
                    $insert_arr[$i]['ss_card_no']= $arr->ss_card_no;
                    $insert_arr[$i]['mobile_no']= $arr->mobile_no;
                    $insert_arr[$i]['created_by_dist_code']= $arr->created_by_dist_code;
                    $insert_arr[$i]['created_by_level']= $arr->created_by_level;
                    $insert_arr[$i]['created_by_local_body_code']= $arr->created_by_local_body_code;
                    $insert_arr[$i]['created_at']= $arr->created_at;
                    $insert_arr[$i]['created_by']= $arr->created_by;
                    $insert_arr[$i]['ds_registration_no']=  $arr->duare_sarkar_registration_no;
                    $insert_arr[$i]['ds_date']=  $arr->duare_sarkar_date;
                    $insert_arr[$i]['email']=  $arr->email;
                    $insert_arr[$i]['approval_date']= $arr->approval_date;
                    $insert_arr[$i]['dist_code']= $arr->dist_code;
                    $insert_arr[$i]['police_station']= trim($arr->police_station);
                    $insert_arr[$i]['rural_urban_id']= $arr->rural_urban_id;
                    $insert_arr[$i]['block_ulb_code']= $arr->block_ulb_code;
                    $insert_arr[$i]['block_ulb_name']= trim($arr->block_ulb_name);
                    $insert_arr[$i]['gp_ward_code']= $arr->gp_ward_code;
                    $insert_arr[$i]['gp_ward_name']= trim($arr->gp_ward_name);
                    $insert_arr[$i]['village_town_city']= trim($arr->village_town_city);
                    $insert_arr[$i]['house_premise_no']=trim($arr->house_premise_no);
                    $insert_arr[$i]['post_office']= trim($arr->post_office);
                    $insert_arr[$i]['pincode']= trim($arr->pincode);
                    $insert_arr[$i]['residency_period']= trim($arr->residency_period);
                    $insert_arr[$i]['bank_code']= trim($arr->bank_code);
                    $insert_arr[$i]['bank_name']= trim($arr->bank_name);
                    $insert_arr[$i]['branch_name']= trim($arr->branch_name);
                    $insert_arr[$i]['bank_ifsc']= trim($arr->bank_ifsc);
                    $insert_arr[$i]['caste_certificate_no']= trim($arr->caste_certificate_no);
                    $insert_arr[$i]['ds_phase']= trim($arr->ds_phase);
                    $insert_arr[$i]['is_faulty']= $is_faulty;
                    $insert_arr[$i]['migration_date']= $c_time;
                
                if (!empty($arr->encoded_aadhar)) {
                    $insert_arr[$i]['aadhar_hash']= trim($arr->aadhar_hash);
                    $insert_arr[$i]['encoded_aadhar'] = trim($arr->encoded_aadhar);
                    $insert_arr[$i]['aadhar_no'] = $arr->aadhar_no;
                }
                
                    $i++;
                }
                if(count($insert_arr)>0){
                 $insert_count=DB::table($schema)->insert($insert_arr);
                }
                $update_arr = array();
                $update_arr['is_sent_jb'] = 1;
                if(count($update_main_arr)>0){
                    $update_main_count = DB::connection('pgsql_lb_mainwrite')->table('lb_scheme.ben_personal_details')->where('created_by_dist_code',$district_code)->whereNull('is_sent_jb')->whereIn(implode(',', $update_main_arr))->update($update_arr);
                }
                else{
                    $update_main_count =0;
                }
                if(count($update_faulty_arr)>0){
                    $update_faulty_count = DB::connection('pgsql_lb_mainwrite')->table('lb_scheme.faulty_ben_personal_details')->where('created_by_dist_code',$district_code)->whereNull('is_sent_jb')->whereIn(implode(',', $update_main_arr))->update($update_arr);
                }
                else{
                    $update_faulty_count =0;
                }
                if($insert_count==($update_main_count+$update_faulty_count)){
                    if($insert_count==$total_data){
                        DB::commit();
                        DB::connection('pgsql_lb_mainwrite')->commit();
                        $logmessage .=  "Total ".$total_data." Applicant data has been migrated for district ".$district_code." for ".$caste." Others on " . date("Y-m-d h:i:s") . "." . "\n";

                    }
                    else{
                        DB::rollback();
                        DB::connection('pgsql_lb_mainwrite')->rollback();
                        $logmessage .=  "Something wrong.. for district ".$district_code." for Caste ".$caste." on " . date("Y-m-d h:i:s") . "." . "\n";
                    }

                }
                else{
                    DB::rollback();
                    DB::connection('pgsql_lb_mainwrite')->rollback();
                    $logmessage .=  "Something wrong for district ".$district_code." for Caste ".$caste." on " . date("Y-m-d h:i:s") . "." . "\n";

                }
              
            }
            else{
                $logmessage .=  "No data found for district ".$district_code." for Caste ".$caste." on " . date("Y-m-d h:i:s") . "." . "\n";

            }
        
            $logmessage .=  " JB Migration Cron has been completed on " . date("Y-m-d h:i:s") . "." . "\n";
            Storage::append($fileLocation, $logmessage);
            //dd($result_set);
        } catch (\Exception $e) {
            dd($e);
            $logmessage .= " Exception:- " . $e->getMessage() . " on " . date("Y-m-d h:i:s") . "." . "\n";
            Storage::append($fileLocation, $logmessage);
            //Storage::put($fileLocation);
        }
    }

    function fetchdocument(Request $request)
    {
        try {
           
            $application_id = $request->application_id;
            $is_faulty = $request->is_faulty;
           // return response()->json(["is_success" => false, "errormsg" => $is_faulty], 200);
            if (empty($application_id)) {
                return response()->json(["is_success" => false, "errormsg" => 'Application id not found'], 400);
            }
           
            // $application_id = 300001392;
            $document_list = DocumentType::select('id', 'doc_name')->get();
            //dd($document_list->toArray());
            //return response()->json(["is_success" => true, "doc_list" => $document_list->toArray()], 200);
            $return_arr = collect([]);
            if( $is_faulty==0){
            $list1 = DB::connection('pgsql_encwrite')->table('ben_profile_image')->select('profile_image as content', 'image_extension as extension', 'image_mime_type as mime_type', 'image_type as type')->where('application_id', $application_id)->get();
            // return response()->json(["is_success" => true, "doc_list" => $list1->toArray()], 200);
            if (count($list1) > 0) {
                $merge1 = $return_arr->merge($list1);
            } else {
                $merge1 = collect([]);
            }
            $list2 = DB::connection('pgsql_encwrite')->table('ben_attach_documents')->select('document_type as type', 'attched_document as content', 'document_extension as extension', 'document_mime_type as mime_type')->where('application_id', $application_id)->get();
            if (count($list2) > 0) {
                $merge2 = $merge1->merge($list2);
            } else {
                $merge2 = collect([]);
            }
           // dd($merge2);
            if (count($merge2) > 0) {
                $i = 0;
                $listarr = array();
                foreach ($merge2 as $itm) {
                    $listarr[$i]['content'] = $itm->content;
                    $listarr[$i]['extension'] = $itm->extension;
                    $listarr[$i]['mime_type'] = $itm->mime_type;
                    $listarr[$i]['type'] = $itm->type;
                    $type_des_arr = $document_list->where('id', $itm->type)->first();
                    if(!empty($type_des_arr)){
                    $listarr[$i]['type_des'] = $type_des_arr['doc_name'];
                    }
                    $i++;
                }
                //dd($listarr);
                return response()->json(["is_success" => 'true', "doc_list" => $listarr], 200);

                //return response()->json(["is_success" => true, "doc_list" => $listarr], 200);
            } else {
                $listarr = array();
                return response()->json(["is_success" => false, "doc_list" =>$listarr], 400);
                //return response()->json(["is_success" => true, "doc_list" => $listarr], 200);
            }
        }  
        else if($is_faulty==1){
            $list1 = DB::connection('pgsql_encwrite')->table('faulty_ben_profile_image')->select('profile_image as content', 'image_extension as extension', 'image_mime_type as mime_type', 'image_type as type')->where('application_id', $application_id)->get();
            // return response()->json(["is_success" => true, "doc_list" => $list1->toArray()], 200);
            if (count($list1) > 0) {
                $merge1 = $return_arr->merge($list1);
            } else {
                $merge1 = collect([]);
            }
            $list2 = DB::connection('pgsql_encwrite')->table('faulty_ben_attach_documents')->select('document_type as type', 'attched_document as content', 'document_extension as extension', 'document_mime_type as mime_type')->where('application_id', $application_id)->get();
            if (count($list2) > 0) {
                $merge2 = $merge1->merge($list2);
            } else {
                $merge2 = collect([]);
            }
            if (count($merge2) > 0) {
                $i = 0;
                $listarr = array();
                foreach ($merge2 as $itm) {
                    $listarr[$i]['content'] = $itm->content;
                    $listarr[$i]['extension'] = $itm->extension;
                    $listarr[$i]['mime_type'] = $itm->mime_type;
                    $listarr[$i]['type'] = $itm->type;
                    $type_des_arr = $document_list->where('id', $itm->type)->first();
                    $listarr[$i]['type_des'] = $type_des_arr['doc_name'];
                    $i++;
                }
                return response()->json(["is_success" => true, "doc_list" => $listarr], 200);
            } else {
                $listarr = array();
                return response()->json(["is_success" => true, "doc_list" => $listarr], 400);
            }
            
        }
        else{
                return response()->json(["is_success" => false, "errormsg" => 'Type not Valid'], 400);  
        }
        } catch (\Exception $e) {
            $listarr = array();
            return response()->json(["is_success" => false, "doc_list" => $listarr], 400);
        }
    }
    function lbmarkwrongdob(Request $request)
    {
        $logmessage = "";
        $fileLocation = 'jbMigration/log.txt';
        try {
            
            $c_time = date('Y-m-d H:i:s', time());
            $getModelFunc = new getModelFunc();
            $schemaname = $getModelFunc->getSchemaDetails();
            $Table = $getModelFunc->getTable('', '', 1);
            $TableFaulty = $getModelFunc->getTableFaulty('', '', 1);
            $personal_model = new DataSourceCommon;
            $personal_model->setTable('' . $Table);
            $personal_model_f = new DataSourceCommon;
            $personal_model_f->setTable('' . $TableFaulty);
            $application_id=$request->application_id;
            $is_faulty=$request->is_faulty;
            $logmessage .=  "JB Mark Wrong Dob  has been started on " . date("Y-m-d h:i:s") . "." . "\n";
            $update_arr = array();
            $update_arr['wrong_dob'] = 1;
            if ($is_faulty == 1) {
                $is_status_updated = $personal_model_f->where('application_id', $application_id)->update($update_arr);
            } else if ($is_faulty == 0) {
                $is_status_updated = $personal_model->where('application_id', $application_id)->update($update_arr);
            }
            $logmessage .=  " JB Mark Wrong Dob been completed on " . date("Y-m-d h:i:s") . "." . "\n";
            Storage::append($fileLocation, $logmessage);
            if( $is_status_updated){
            return response()->json(["is_success" => true], 200);
            }
        } catch (\Exception $e) {
           
            $logmessage .= " Exception:- " . $e->getMessage() . " on " . date("Y-m-d h:i:s") . "." . "\n";
            Storage::append($fileLocation, $logmessage);
            return response()->json(["is_success" => false], 400);
        }
    }
    function jbtempdobupdate(Request $request)
    {
        $logmessage = "";
        $fileLocation = 'jbMigration/log.txt';
        try {
            $c_time = date('Y-m-d H:i:s', time());
            $getModelFunc = new getModelFunc();
            $schemaname = $getModelFunc->getSchemaDetails();
            $Table = $getModelFunc->getTable('', '', 1);
            $TableFaulty = $getModelFunc->getTableFaulty('', '', 1);
            $personal_model = new DataSourceCommon;
            $personal_model->setTable('' . $Table);
            $personal_model_f = new DataSourceCommon;
            $personal_model_f->setTable('' . $TableFaulty);
            $application_id=$request->application_id;
            $dob=$request->dob;
            $is_faulty=$request->is_faulty;
            $logmessage .=  "JB Mark Wrong Dob  has been started on " . date("Y-m-d h:i:s") . "." . "\n";
            $logmessage .=  " JB Mark Wrong incoming parameter application_id=".$application_id." ,faulty=".$is_faulty." ,dob=".$dob. "\n";

            $update_arr = array();
            $update_arr['jb_dob'] = $dob;
            $date_parts = explode( '-', $dob );
            //dd($date_parts);
            if(checkdate( $date_parts[1], $date_parts[2], $date_parts[0] )){
                $date = Carbon::parse($dob);
                if ($date->isFuture()) {
                    return response()->json(["is_success" => false], 400);
                }
                if ($is_faulty == 1) {
                    $is_status_updated = $personal_model_f->where('application_id', $application_id)->update($update_arr);
                } else if ($is_faulty == 0) {
                    $is_status_updated = $personal_model->where('application_id', $application_id)->update($update_arr);
                }
            }else{
                return response()->json(["is_success" => false], 400);
            }
           
            $logmessage .=  " JB Mark Wrong Dob been completed on " . date("Y-m-d h:i:s") . "." . "\n";
            Storage::append($fileLocation, $logmessage);
            if( $is_status_updated){
            return response()->json(["is_success" => true], 200);
            }
        } catch (\Exception $e) {
           
            $logmessage .= " Exception:- " . $e->getMessage() . " on " . date("Y-m-d h:i:s") . "." . "\n";
            Storage::append($fileLocation, $logmessage);
            return response()->json(["is_success" => false], 400);
        }
    }
}
