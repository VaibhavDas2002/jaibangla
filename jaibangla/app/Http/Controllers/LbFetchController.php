<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Scheme;
use App\DocumentType;
use Illuminate\Support\Facades\Storage;
class LbFetchController extends Controller
{
  public function __construct()
  {
    date_default_timezone_set('Asia/Kolkata');
  }

  /*
        First Landing page where we can select scheme
    */
  public function fetch(Request $request)
  {
    try {
      $caste = $request->caste;
      $c_time = date('Y-m-d H:i:s', time());
      $input = array(
        "lb_application_id" => $request->lb_application_id,
        "lb_beneficiary_id" => $request->lb_beneficiary_id,
        "ben_fname" => trim($request->ben_fname),
        "ben_full_name" => trim($request->ben_fname),
        "next_level_role_id" => 1,
        "bank_code" => trim($request->bank_code),
        "bank_ifsc" => trim($request->bank_ifsc),
        "created_by_dist_code" => $request->created_by_dist_code,
        "created_by_local_body_code" => $request->created_by_local_body_code,
        "migration_date" => $c_time,
      );
      if (!empty($request->caste_certificate_no)) {
        $input['caste_certificate_no'] = trim($request->caste_certificate_no);
      }
      if (!empty($request->epic_voter_id)) {
        $input['epic_voter_id'] = trim($request->epic_voter_id);
      }
      if (!empty($request->ration_card_no)) {
        $input['ration_card_no'] = trim($request->ration_card_no);
      }
      if (!empty($request->ration_card_cat)) {
        $input['ration_card_cat'] = trim($request->ration_card_cat);
      }
      if (!empty($request->gender)) {
        $input['gender'] = trim($request->gender);
      }
      if (!empty($request->dob)) {
        $input['dob'] = $request->dob;
      }
      if (!empty($request->caste)) {
        $input['caste'] = trim($request->caste);
      }
      if (!empty($request->marital_status)) {
        $input['marital_status'] = trim($request->marital_status);
      }
      if (!empty($request->father_fname)) {
        $input['father_fname'] = trim($request->father_fname);
      }
      if (!empty($request->father_mname)) {
        $input['father_mname'] = trim($request->father_mname);
      }
      if (!empty($request->father_lname)) {
        $input['father_lname'] = trim($request->father_lname);
      }
      if (!empty($request->mother_fname)) {
        $input['mother_fname'] = trim($request->mother_fname);
      }
      if (!empty($request->mother_mname)) {
        $input['mother_mname'] = trim($request->mother_mname);
      }
      if (!empty($request->mother_lname)) {
        $input['mother_lname'] = trim($request->mother_lname);
      }
      if (!empty($request->spouse_fname)) {
        $input['spouse_fname'] = trim($request->spouse_fname);
      }
      if (!empty($request->spouse_mname)) {
        $input['spouse_mname'] = trim($request->spouse_mname);
      }
      if (!empty($request->spouse_lname)) {
        $input['spouse_lname'] = $request->spouse_lname;
      }
      if (!empty($request->ss_card_no)) {
        $input['ss_card_no'] = trim($request->ss_card_no);
      }
      if (!empty($request->created_by_level)) {
        $input['created_by_level'] = trim($request->created_by_level);
      }
     
      if (!empty($request->ds_registration_no)) {
        $input['ds_registration_no'] = trim($request->ds_registration_no);
      }
      if (!empty($request->ds_date)) {
        $input['ds_date'] = $request->ds_date;
      }
      if (!empty($request->email)) {
        $input['email'] = trim($request->email);
      }
      if (!empty($request->residency_period)) {
        $input['residency_period'] = $request->residency_period;
      }

      if (!empty($request->dist_code)) {
        $input['dist_code'] = $request->dist_code;
      }
      if (!empty($request->police_station)) {
        $input['police_station'] = trim($request->police_station);
      }
      if (!empty($request->rural_urban_id)) {
        $input['rural_urban_id'] = trim($request->rural_urban_id);
      }
      if (!empty($request->block_ulb_code)) {
        $input['block_ulb_code'] = trim($request->block_ulb_code);
      }
      if (!empty($request->block_ulb_name)) {
        $input['block_ulb_name'] = trim($request->block_ulb_name);
      }
      if (!empty($request->gp_ward_code)) {
        $input['gp_ward_code'] = trim($request->gp_ward_code);
      }
      if (!empty($request->village_town_city)) {
        $input['village_town_city'] = trim($request->village_town_city);
      }
      if (!empty($request->house_premise_no)) {
        $input['house_premise_no'] = trim($request->house_premise_no);
      }
      if (!empty($request->post_office)) {
        $input['post_office'] = trim($request->post_office);
      }
      if (!empty($request->pincode)) {
        $input['pincode'] = trim($request->pincode);
      }
      if (!empty($request->bank_name)) {
        $input['bank_name'] = trim($request->bank_name);
      }
      if (!empty($request->branch_name)) {
        $input['branch_name'] = trim($request->branch_name);
      }
      if (!empty($request->aadhar_hash)) {
        $input['aadhar_hash'] = trim($request->aadhar_hash);
      }
      if (!empty($request->encoded_aadhar)) {
        $input['encoded_aadhar'] = $request->encoded_aadhar;
      }
      if (!empty($request->aadhar_no)) {
        $input['aadhar_no'] = $request->aadhar_no;
      }
      if (!empty($request->mobile_no)) {
        $input['mobile_no'] = $request->mobile_no;
      }
      // if (!empty($request->created_at)) {
      //   $input['created_at'] = date('Y-m-d', $request->created_at);
      // }
      if (!empty($request->ds_phase)) {
        $input['ds_phase'] = $request->ds_phase;
      }
      if (!empty($request->approval_date)) {
        $input['approval_date'] = $request->approval_date;
      }
      if ($request->is_faulty != '') {
        $input['is_faulty'] = $request->is_faulty;
      }
      if ($request->caste == 'OTHERS') {
        $scheme_id = 10;
        $input['scheme_id'] =  $scheme_id;
      } else if ($request->caste == 'SC') {
        $scheme_id = 3;
        $input['scheme_id'] =  $scheme_id;
      } else if ($request->caste == 'ST') {
        $scheme_id = 1;
        $input['scheme_id'] =  $scheme_id;
      }
      $sObj =  Scheme::select('id', 'short_code')->where('id', $scheme_id)->first();
      //$parameter['scheme_id'] = $scheme_id;
      $schema_name =  $sObj->short_code;
      //dd($schema_name);
      if (empty($schema_name)) {
        $schema_name = 'pension';
      }
      $main_insert = DB::table($schema_name . '.beneficiary_lb')->insert($input);
      if ($main_insert)
        return response()->json(["is_success" => true], 200);
    } catch (\Exception $e) {
      return response()->json(["is_success" => false], 200);
    }
  }

  function fetchlbdocument(Request $request)
    {
        $logmessage = "";
        $fileLocation = 'LbMigration/logdoc.txt';
        try {
            $c_time = date('Y-m-d H:i:s', time());
            $logmessage .=  "LB Doc Migration Cron has been started on " . date("Y-m-d h:i:s") . "." . "\n";
            $query = "select doc_imported,lb_application_id,scheme_id from pension.beneficiary_lb where doc_imported IS NULL 
            and jb_dup_bank IS NULL and jb_dup_aadhar IS NULL limit 10";
            $result_set = DB::connection('pgsql_mis')->select($query);
            $insert_doc_type_arr=array();
            $update_app_arr=array();
           // dd($result_set);
           $base_url = url('/');
           $scheme_list = Scheme::where('is_active', 1)->get();
           $doc_type_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->get();
           $i=0;
            if(count($result_set)>0){
             
                  foreach ($result_set as $row) {
                    $scheme_obj = $scheme_list->where('id', $row->scheme_id)->first();
                    if (!empty($scheme_obj->short_code)) {
                      $schema = $scheme_obj->short_code;
                      $file_path = $scheme_obj->file_path;
                      $file_arc_path = $scheme_obj->file_arc_path;
                    } else {
                      $schema = "pension";
                      $file_path = "";
                      $file_arc_path = "";
                    }
                    if(empty($row->is_faulty)){
                      $is_faulty=0;
                     }
                     else{
                      $is_faulty=$row->is_faulty;
                     }
                    $data = array(
                      "application_id" => $row->lb_application_id,
                      "is_faulty" => $is_faulty
                    );
                 
                  
                  $data_string = json_encode($data);
                  $post_url = "http://174.24.8.11/api/fetchdocument";
                  $curl = curl_init($post_url);
                  $headers = array(
                    'Content-Type: application/json'
                  );
                  //curl_setopt($curl, CURLOPT_USERPWD, "username":"Password");
                  curl_setopt($curl, CURLOPT_URL, $post_url);
                  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                  curl_setopt($curl, CURLOPT_POST, true);
                  //curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($post_data) );
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
                  $post_response = curl_exec($curl);
                  $post_response = json_decode($post_response);
                  if ($post_response->is_success) {
                    $docs = $post_response->doc_list;
                    if(count($docs)>0){
                      foreach ($docs as $doc_item) {
                        if (!empty($scheme_obj->file_path)) {
                          $file_path = $scheme_obj->file_path;
                          $file_arc_path = $scheme_obj->file_arc_path;
                        } else {
                          $file_path = "";
                          $file_arc_path = "";
                        }
                        $mime_type = $doc_item->mime_type;
                        $type = $doc_item->type;
                        if($type==116){
                          $type=199;
                        }
                        if($type==117){
                          $type=116;
                        }
                        $unid=uniqid();
                        $file_extension = $doc_item->extension;
                        $file_name='doc_'.$type.'_'. $request->id.'_'. $unid;
                        $file_namedb='doc_'.$type.'_'. $request->id.'_'. $unid.'.'.$file_extension;
                        $fileName = $file_path.'\\'.$file_name.'.'.$file_extension;
                        $doc_type_arr=$doc_type_list->where('id', $type)->first();
                        if (strtoupper($file_extension) == 'PNG' || strtoupper($file_extension) == 'JPG' || strtoupper($file_extension) == 'JPEG') {
                            $data = 'data:image/'.$file_extension.';base64,'.$doc_item->content;
                            $fileBin = file_get_contents($data);
                           
                        }else if (strtoupper($file_extension) == 'PDF') {
                          $fileBin = base64_decode($doc_item->content);
    
                        }
                        if(Storage::disk('local')->put($fileName, $fileBin)){
                          $logmessage .="Doc with id ".$type." has been write in JB App server for the LB Application Id ".$row->lb_application_id." ". "\n";
                          $insert_doc_type_arr[$i]['scheme_id']=$row->scheme_id;
                          $insert_doc_type_arr[$i]['is_active']=FALSE;
                          $insert_doc_type_arr[$i]['lb_application_id']=$row->lb_application_id;
                          $insert_doc_type_arr[$i]['doc_type_id']=$type;
                          $insert_doc_type_arr[$i]['doc_type_name']=$doc_type_arr->doc_name;
                          if(in_array($row->scheme_id,array(2,10,11))){
                            $insert_doc_type_arr[$i]['doc_name']=$base_url . '/images_wcd/' . $file_namedb;
                          }
                          else{
                            $insert_doc_type_arr[$i]['doc_name']=$base_url . '/images/' . $file_namedb;
                          }
                          $insert_doc_type_arr[$i]['created_at']=$c_time;
                          array_push($update_app_arr,$row->lb_application_id);
                          $i++;
                        }
                      }
                    }
                    

                  } else {
                    $docs = array();
                  }
                }
                //dump($update_app_arr);
                DB::beginTransaction();
                if(count($update_app_arr)>0){
                  $input = [
                    'doc_imported' => 1
                  ];
                  $lb_update = DB::table($schema .'.beneficiary_lb')->whereIn('lb_application_id',$update_app_arr)->update($input);

                }
                else{
                  $lb_update=1;
                }
                if(count($insert_doc_type_arr)>0){
                  $doc_inserted = DB::table($schema .'.ben_docs')->insert($insert_doc_type_arr);
                }
                else{
                  $doc_inserted=1;
                }
                if ($lb_update && $doc_inserted) {
                  DB::commit();
                  $logmessage .="Total ".$i." data has been migrated". "\n";
                }
                else{
                  DB::rollback();
                  $logmessage .=" Roolback error". "\n";
                }
          }
          else{
            dd('No beneficiary found');
            $logmessage .="Total ".$i." data has been migrated". "\n";
          }
            
            $logmessage .=  "LB Migration Cron for Document has been completed on " . date("Y-m-d h:i:s") . "." . "\n";
            Storage::append($fileLocation, $logmessage);
            //dd($result_set);
        } catch (\Exception $e) {
            dd($e);
            $logmessage .= " Exception:- " . $e->getMessage() . " on " . date("Y-m-d h:i:s") . "." . "\n";
            Storage::append($fileLocation, $logmessage);
            //Storage::put($fileLocation);
        }
    }
    function fetchlbdocumenttestapi(Request $request)
    {
      
      try {
        
        $district_code = 320;
        $server_address=$_SERVER['SERVER_ADDR'];
        $base_url = url('/');
        $c_time = date('Y-m-d H:i:s', time());
        $update_app_arr=array();
        $scheme_id = $request->scheme_id;
        $scheme_obj = Scheme::where('id', $scheme_id)->first();
        if (!empty($scheme_obj->short_code)) {
          $schema = $scheme_obj->short_code;
        } else {
          $schema = "pension";
        }
        $query = DB::table($schema . '.beneficiary_lb')
        ->where('created_by_dist_code', $district_code)
        ->where('lb_application_id', $request->id);
        $row = $query->first();
        if(empty($row->is_faulty)){
          $is_faulty=0;
         }
         else{
          $is_faulty=$row->is_faulty;
         }
        
        $doc_type_list = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->get();
        $headers = array(
          'Content-Type: application/json'
        );
       $post_url = "http://172.24.8.11:80/api/fetchdocument";
       $curl = curl_init($post_url);
      

        $data = array("application_id" =>$request->id ,"is_faulty" => $is_faulty);
        $data_string = json_encode($data);

        $scheme_id=$row->scheme_id;
            header("Access-Control-Allow-Origin: *");
            curl_setopt($curl, CURLOPT_URL, $post_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET"); 
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $post_response = curl_exec($curl);
            
            if($post_response){
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if($httpcode==200){
              $post_response_lb=json_decode($post_response);
             
              $docs = $post_response_lb->doc_list;
             // dd($docs);
              $file=0;
              $i=0;
              if(count($docs)>0){
                foreach ($docs as $doc_item) {
                  if (!empty($scheme_obj->file_path)) {
                    $file_path = $scheme_obj->file_path;
                    $file_arc_path = $scheme_obj->file_arc_path;
                  } else {
                    $file_path = "";
                    $file_arc_path = "";
                  }
                  //dd($file_path);
                  $mime_type = $doc_item->mime_type;
                  $type = $doc_item->type;
                  if($type==116){
                    $type=199;
                  }
                  if($type==117){
                    $type=116;
                  }
                  if($scheme_id == 1){
                      $file_path = 'keep_st_test';
                  }
                  else if($scheme_id == 3) {
                        $file_path = 'keep_sc_test';
                  }
                  else if($scheme_id == 10) {
                        $file_path = 'keep_oap_test';
                  }
                  $unid=uniqid();
                  $file_extension = $doc_item->extension;
                  $file_name='doc_'.$type.'_'. $request->id.'_'. $unid;
                  $file_namedb='doc_'.$type.'_'. $request->id.'_'. $unid.'.'.$file_extension;
                  $fileName = $file_path.'\\'.$file_name.'.'.$file_extension;
                  $doc_type_arr=$doc_type_list->where('id', $type)->first();
                  //dd($fileName);
                  if (strtoupper($file_extension) == 'PNG' || strtoupper($file_extension) == 'JPG' || strtoupper($file_extension) == 'JPEG') {
                      $data = 'data:image/'.$file_extension.';base64,'.$doc_item->content;
                      $fileBin = file_get_contents($data);
                     
                  }else if (strtoupper($file_extension) == 'PDF') {
                    $fileBin = base64_decode($doc_item->content);

                  }
                  if(!empty($doc_type_arr)){
                  if(Storage::disk('local')->put($fileName, $fileBin)){
                    $insert_doc_type_arr[$i]['scheme_id']=$scheme_id;
                    $insert_doc_type_arr[$i]['is_active']=FALSE;
                    $insert_doc_type_arr[$i]['lb_application_id']= $request->id;
                    $insert_doc_type_arr[$i]['doc_type_id']=$type;
                    $insert_doc_type_arr[$i]['doc_type_name']=$doc_type_arr->doc_name;
                    if($scheme_id == 10){
                      $insert_doc_type_arr[$i]['doc_name']=$base_url . '/images_oap/' . $file_namedb;
                    }
                    else if($scheme_id == 1){
                      $insert_doc_type_arr[$i]['doc_name']=$base_url . '/images_st/' . $file_namedb;
                    }
                    else if($scheme_id == 3){
                      $insert_doc_type_arr[$i]['doc_name']=$base_url . '/images_sc/' . $file_namedb;
                    }
                    $insert_doc_type_arr[$i]['created_at']=$c_time;
                    array_push($update_app_arr, $request->id);
                    $file++;
                    $i++;
                  }
                }
                }
                if($file>0){
                  DB::beginTransaction();
                  $input = [
                    'doc_imported' => 1
                  ];
                  $lb_update = DB::table($schema .'.beneficiary_lb')->where('lb_application_id',$request->id)->update($input);
                  if(count($insert_doc_type_arr)>0){
                    $doc_inserted = DB::table($schema .'.ben_docs')->insert($insert_doc_type_arr);
                  }
                  else{
                    $doc_inserted=1;
                  }
                  if ($lb_update && $doc_inserted) {
                    DB::commit();
                    $logmessage ="Total ".$file." data has been migrated";
                    dd( $logmessage);
                    //return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('success', $logmessage)->with('id',  $request->id);
  
                  }
                  else{
                    DB::rollback();
                    $logmessage='Error.. Please try agian.';
                    dd( $logmessage);
                   // return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
                  }
                }
                else{
                  $return_msg=array('No file fetch from LB..try try again.');
                  dd( $return_msg);
                //return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg); 
                }

              }
              else{
                $return_msg=array('No file fetch from LB..try try again.');
                dd( $return_msg);
               // return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg); 
              }
            }
            else{
              $return_msg=array('No file fetch from LB..try try again.');
              dd( $return_msg);
              //return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg); 
            }
            }
            else{
              $return_msg=array('Unable to try LB..please try again.');
              dd( $return_msg);
             // return redirect("View60lbapplication?id=" . $request->id . "&scheme_id=" . $scheme_id)->with('errors', $return_msg);
            }
            

            
            
           
           
            
        }  
        catch(\Exception $e) {
          dd($e);
        }
  }  
           
  function lbdbtest(Request $request)
  {
    try{
    $data1 = DB::connection('pgsql_lb_mainwrite')->table('lb_scheme.ben_personal_details')->where('application_id',100341452)->first();
    dump($data1);
    $data2 = DB::connection('pgsql_lb_encwrite')->table('lb_scheme.ben_attach_documents')->where('application_id',100341452)->first();
    dd($data2);
    }catch (\Exception $e) {
      dd($e);
    }
  }
    
}
