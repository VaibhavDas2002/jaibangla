<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\District;
use App\Scheme;
use Redirect;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Validator;
use DateTime;
use Config;
use App\Configduty;
use Maatwebsite\Excel\Facades\Excel;
use App\DataSourceCommon;

use App\getModelFunc;
use Illuminate\Support\Facades\Crypt;
use App\RejectRevertReason;
use App\AadharDuplicateTrail;
use App\SubDistrict;
use App\Taluka;
use App\DocumentType;
use Illuminate\Support\Facades\Storage;
use App\SchemeDocMap;
use File;
use App\BankDetails;
use App\UrbanBody;
use App\Ward;
use App\GP;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\AcceptRejectInfo;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Illuminate\Support\Facades\Cache;

class NapixAadharValidateCeontroller extends Controller
{
    public $clientID1;
    public $clientSecret1;
    public $clientID2;
    public $clientSecre2;
    public $clientID3;
    

    public function __construct()
    {

        $this->clientID1 = 'c5865e5302fd508e4d89499f1f6d116e';
        $this->clientSecret1 = '03bce35108f7107ad2810c5a5c5562ef';
        $this->clientID2 = 'ea638a77-f9a0-4159-b5bf-e3bf5c1924c2';
        // $this->clientSecre2 = '534e80d115b3577dac1d3bea1ba5a2f9ceb576c0d8a9fa5f4f112fbb2db77712dc6f791aaf301dbd7cd5c14677c0bf0ab0b56a9cd62730b6de97ceea5d3957c6';
        $this->clientSecre2 = '25647cd7c088a0d9da069882808ef41882efe20fffead2b63b68a3c7c30ed07aa5a8b737a6b4068b72aeb43fd6bb1741f1d9f5ca6a7c12d5b4215c1faddcfd53';
        $this->clientID3 = 'e3db8f38-d149-4f4d-b74b-a641e42354c6';

       
        // $this->clientID2Aadhar = 'e3db8f38-d149-4f4d-b74b-a641e42354c6';
        // $this->clientSecret2Aadhar = '25647cd7c088a0d9da069882808ef41882efe20fffead2b63b68a3c7c30ed07aa5a8b737a6b4068b72aeb43fd6bb1741f1d9f5ca6a7c12d5b4215c1faddcfd53';


        // $this->clientID1Aadhar = 'c5865e5302fd508e4d89499f1f6d116e';
        // $this->clientSecret1Aadhar = '03bce35108f7107ad2810c5a5c5562ef';
        // $this->clientID2Aadhar = 'e3db8f38-d149-4f4d-b74b-a641e42354c6';
        // $this->clientSecret2Aadhar = '25647cd7c088a0d9da069882808ef41882efe20fffead2b63b68a3c7c30ed07aa5a8b737a6b4068b72aeb43fd6bb1741f1d9f5ca6a7c12d5b4215c1faddcfd53';
        // $this->clientID3 = 'c5865e5302fd508e4d89499f1f6d116e';
        // $this->clientSecret3= '03bce35108f7107ad2810c5a5c5562ef';
        // $this->clientID4= 'e3db8f38-d149-4f4d-b74b-a641e42354c6';
        // $this->clientSecret4='';
       
        
    }
    
   

    public function authiticated(){
    
        $return_arr=array();
        $httpcode=NULL;$response_text=NULL;$message=NULL; $message=NULL;$tokenRefId=NULL;$token=NULL;
        $is_success=1;
        $post_url = 'https://wbgw.napix.gov.in/wb/food-supplies/ksapi-signin';
        $curl = curl_init($post_url);
        $headers = array(
            'Content-Type: application/json',
            'clientID:'.$this->clientID1,
             'clientSecret: '.$this->clientSecret1,
    
        );
        $data = array("clientId" =>$this->clientID3,"clientSecret" => $this->clientSecre2);
        $data_string = json_encode($data);
        header("Access-Control-Allow-Origin: *");
        curl_setopt($curl, CURLOPT_URL, $post_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $post_response = curl_exec($curl);
        if (curl_errno($curl)) {
            $response_text = curl_error($curl);
            $is_success=0;
    
        }
        else{
            $post_response=json_decode($post_response);
            $response_text=$post_response;
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if($httpcode==200){
              if($post_response->status->msg=='Success'){
                    $token=$post_response->status->token;
                    // Cache::forever('Aadhar_validate_tokenRefId',$tokenRefId);
                    Cache::forever('Aadhar_validate_token',$token); 
    
              }
             
            }    
       }
     }


    public function check(Request $request)
    {
        try {
            //code...
        
                $scheme_id = $request->scheme_id;
                $district_code = $request->dist_code;

                
                $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
                $schema = $scheme_obj->short_code;

               

                


                $token = Cache::get('Aadhar_validate_token');
                
               // $tokenRefId = Cache::get('aadhar_tokenRefId');
                $limit = $request->limit;
                if(empty($token)){
                    $this->authiticate(); 
                }

                //dd($token);
               // $token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjbGllbnRJZCI6ImUzZGI4ZjM4LWQxNDktNGY0ZC1iNzRiLWE2NDFlNDIzNTRjNiIsIm5iZiI6MTY5MzkwNTQ5NywiZXhwIjoxNjkzOTkxODk3LCJpYXQiOjE2OTM5MDU0OTd9.ozb8_3c1KpgY_mq4-vgQD9Ikz3cTqqwGitRlQZ7HMUE';

                $return_arr=array();
                $httpcode=NULL;$response_text=NULL;$message=NULL; $message=NULL;$code=NULL;$last_biometric=NULL; 
                $is_update=0;
                $is_success=0;
                $match_found=0;
                $time=time();
                $post_url = 'https://wbgw.napix.gov.in/wb/food-supplies/khadyasathi-info-via-aadhar';
                $curl = curl_init($post_url);
                $headers = array(
                    'Content-Type: application/json',
                    'clientID:'.$this->clientID1,
                    'clientSecret: '.$this->clientSecret1,
                    'Authorization: Bearer '.$token,

                );


            
               
                //dd()

                $inputupdate_main=[
                    'rc_ks_status'=>1
                ];
                

                // foreach ($rows as $key) {
                   
                //     $application_id = $key->id;
                //     // dd($application_id);

                //     $insert_data = [
                       
                //         'application_id' =>$application_id,
                //         'aadhar_no' =>$key->aadhar_no,
                //         'rc_ks_status'=>1,
                //         'scheme_id' =>$scheme_id
                        

                //         ];
                //         try {

                //         $inser_bulk = DB::table('pds.aadhaar_khadyasathi')
                //         ->insert($insert_data);
                //         }
                //         catch (\Exception $e) {
                //             dd($e);
                //         }
                // }
                
              
                $query = "SELECT json_agg(t) as set FROM (SELECT aadhar_no AS \"PData\", application_id::text AS txn FROM pds.aadhaar_khadyasathi WHERE LENGTH(aadhar_no) = 12 
                And rc_ks_status=4 and scheme_id=$scheme_id  and created_by_dist_code=$district_code order by application_id ASC LIMIT  $limit ) AS t
                ";
               //dd($query);
    
                $aadhar_no_set = DB::connection('pgsql')->select($query);
                $aadhar_no=$aadhar_no_set[0]->set;
               //dd($aadhar_no);
                
                if(empty($aadhar_no))

                {
                    $is_success=0;
                    // DB::connection('pgsql_encwrite')->rollback();
                    $code=400; 
                    $response_text='No data Found';

                }
                else{
                // dump($aadhar_no);
                // dump(json_decode($aadhar_no));

                $data = array("clientId" =>$this->clientID3,"param"=> json_decode($aadhar_no), "type"=>"1");
                // dump($data);
                $data_string = json_encode($data);
                // dd($data_string);
                curl_setopt($curl, CURLOPT_URL, $post_url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
                $post_response = curl_exec($curl);
                if (curl_errno($curl))
                 {

                $response_text = curl_error($curl);
                $is_success=0;

                }
                else{
                    //dd(123);
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);
                    $array= json_decode($post_response, true);

                    //dd($array);
                    $response_text=json_encode($post_response);
                    $is_success=1;
                  //dd($post_response);
                   if($array["status"]["msg"]=='Token expiry')
                   {

                    $code=400; 
                    $response_text='Token expiry';
                    $is_success=0;

                   }
                else
                {



                  

                    if($array["status"]["msg"]=='Success')
                    {
                        $i = 0;
                        $j = 0;
                        DB::beginTransaction();
                        foreach ($array["aadharResult"]["aadharList"] as $item) 
                        {

                            $application_id = $item['txn'];


                                $applicante_value = [];

                                $insert = array();
                                $inputupdate=array();
                                $applicante_value['txn']=$application_id;
                                $applicante_value['AADHAAR_NO']=$item['AADHAAR_NO'];
                                $applicante_value['RationcardNo']=$item['RationcardNo'];
                                $applicante_value['CardCategory']=$item['CardCategory'];
                                // $applicante_value['NAME_AS_IN_AADHAR']=$item['NAME_AS_IN_AADHAR'];
                                // $applicante_value['NAME_AS_IN_RC']=$item['NAME_AS_IN_RC'];
                                $applicante_value ['Card_Status']=$item['Card_Status'];
                                $applicante_value ['DOB']=$item['DOB'];
                                $applicante_value['GENDERID']=$item['GENDERID'];
                                $applicante_value ['LGD_DistrictCode']=$item['LGD_DistrictCode'];
                                $applicante_value['LGD_DistrictName']=$item['LGD_DistrictName'];
                                $applicante_value['LGD_BlockCode']=$item['LGD_BlockCode'];
                                $applicante_value['LGD_BlockName']=$item['LGD_BlockName'];
                                $applicante_value['LGD_GP_Ward_Code']=$item['LGD_GP_Ward_Code'];
                                $applicante_value['LGD_GP_Ward_Name']=$item['LGD_GP_Ward_Name'];
                                $applicante_value['Father_SpouseName']=$item['Father_SpouseName'];
                                $applicante_value['FamilyID']=$item['FamilyID'];
                                
                                if($item['NAME_AS_IN_AADHAR']=='')
                                {
                                $applicante_value['NAME_AS_IN_AADHAR']=NULL;
                                }
                                else{
                                $applicante_value['NAME_AS_IN_AADHAR']=$item['NAME_AS_IN_AADHAR'];
                                }
                                if($item['NAME_AS_IN_RC']=='')
                                {
                                $applicante_value['NAME_AS_IN_RC']=NULL;
                                }
                                else{
                                $applicante_value['NAME_AS_IN_RC']=$item['NAME_AS_IN_RC'];
                                }

                                $row_fetch= DB::table($schema . '.beneficiary')->where('id', $application_id)->where('created_by_dist_code',$district_code)->first();

                                $c_time1=date('Y-m-d H:i:s', time());
                
                                $ben_fullname = $row_fetch->ben_fname . " " . $row_fetch->ben_mname . " " . $row_fetch->ben_lname;
                               // dd($ben_fullname);

                                $ben_fullname = str_replace(' ', '', $ben_fullname);

                                 $appl_name = str_replace(' ', '', $applicante_value['NAME_AS_IN_AADHAR']);
                                 $dob=$row_fetch->dob;

                                if (!empty($dob) &&  !empty($item['DOB']))
                                {
                                if($dob==$item['DOB']){

                                $dob_missmatch=1;
                                }else
                                {
                                $dob_missmatch=0;
                                }
                                }
                                else{
                                    $dob_missmatch=0;

                                }

                                
                    if(strtoupper($appl_name)==strtoupper($ben_fullname))
                    {
                        //dd(123);
                    $message='Bioauth from Aadhaar Number has been checked'; 
                    $inputupdate['name_is_match']=1;

                    }
                    else if($applicante_value['NAME_AS_IN_AADHAR']==NULL)
                    {
                        //dd(333);
                        $inputupdate['name_is_match']=-2;
                        $message='Name not Match'; 
                    }
                    else
                    {
                        //dd(444);
                    $inputupdate['name_is_match']=-2;
                    $inputupdate['acc_validated_aadhar']=-2;
                    $message='Name not Match'; 

                    }
                               
                    //dd(123);
                                    $insert_data = [
                                        'aadhaar_json' =>json_encode($applicante_value),
                                        'name_as_in_aadhaar' =>$applicante_value['NAME_AS_IN_AADHAR'],
                                        'name_as_in_rc' =>$applicante_value['NAME_AS_IN_RC'],
                                        'rationcard_no' =>$item['RationcardNo'],
                                        'rc_ks_status'=>5,
                                        'family_id'=>$item['FamilyID'],
                                        'response_id' =>$item['txn'],
                                        'card_status' =>$item['Card_Status'],
                                        'dob_kh'=>$item['DOB'],
                                        'next_level_role_id'=>$row_fetch->next_level_role_id,
                                        'created_by_local_body_code'=>$row_fetch->created_by_local_body_code,
                                       'api_response_time'=> $c_time1,
                                       'api_hit_time'=>date('Y-m-d H:i:s', time()),
                                       'dob_is_match_kh'=>$dob_missmatch,
                                       
        
                                        ];
                                       

                                        

                $inputupdate['wbpds_name_as_in_aadhar_sr']=$applicante_value['NAME_AS_IN_AADHAR'];
                $inputupdate['wbpds_is_sent']=1;
                $inputupdate['wbpds_response_received']=1;
                $inputupdate['wbpds_family_id']=$item['FamilyID'];
                $inputupdate['wbpds_ration_card_no']=$item['RationcardNo'];
                $inputupdate['dob_is_match_kh']=$dob_missmatch;
                $inputupdate['aadhaar_no_validation_msg']= $message;
                $inputupdate['dob_kh']=$item['DOB'];
                $inputupdate['aadhaar_no_checked']=1;
                $inputupdate['aadhaar_no_checked_lastdatetime']=$c_time1;
                
                
                
                

                
                               



                                try {


                                    
                                    

                                    $inser_bulk = DB::table('pds.aadhaar_khadyasathi')->where(['application_id' => $application_id,'scheme_id'=> $scheme_id,'created_by_dist_code'=>$district_code])
                                    ->update($insert_data);

                                    $update= DB::table($schema . '.beneficiary')->where('id', $application_id)->where('created_by_dist_code',$district_code)->update($inputupdate);

                        //             $queryInsert = DB::insert("INSERT INTO pension.failed_payment_details(
                        //                 ben_id, scheme_id, created_at, created_by, next_level_role_id, ben_name, mobile_no, created_by_dist_code, created_by_local_body_code, dist_code, rural_urban_id, block_ulb_code, block_ulb_name, gp_ward_code, gp_ward_name, village_town_city, house_premise_no, bank_ifsc, bank_code, 
                        //                 validation_type, 
                        //                 wbpds_name_as_in_aadhar, aadhar_no,acc_validated_aadhaar)
                        //                 select  
                        // id, scheme_id, created_at, created_by, next_level_role_id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(ben_fname,'')||' '||COALESCE(ben_mname,'')||' '||COALESCE(ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) as ben_name, mobile_no, created_by_dist_code, created_by_local_body_code, dist_code, rural_urban_id, block_ulb_code, block_ulb_name, gp_ward_code, gp_ward_name, village_town_city, house_premise_no, bank_ifsc, bank_code,
                        // 2 as validation_type, wbpds_name_as_in_aadhar, aadhar_no,acc_validated_aadhar
                        // from " . $schema . ".beneficiary where id=" . $application_id . "");

                                        if ($inser_bulk && $update ) {
                                            //dd(555);
                                        
                                            $i++;
                                             $j++;

                                        //  $mainupdate = DB::table($schema.'.beneficiary')->where(['id' => $application_id])->update($inputupdate);
                                        //     if ($mainupdate) {
                                        //         $j++;
                                        //     }
                                           
                                        }
                                        else{

                                            DB::rollback();
                                            // DB::connection('pgsql_encwrite')->rollback();
                                            $code=400; 
                                            $response_text='Aadhaar insertion Failed in DB';
                                            $is_success=0;

                                        }

                                    }

                                    catch (\Exception $e) {
                                    dd($e);
                                        DB::rollback();
                                        $is_success=0;
                                        // DB::connection('pgsql_encwrite')->rollback();
                                        $code=400; 
                                        $response_text='Aadhaar insertion Failed';

                                 }


                        }

                        if (($i == $j) && ($i > 0 && $j > 0)) {

                        DB::commit();

                        $code=200; 
                        $response_text='Total - '.$i.' Aadhaar Insert and update Successfully';
                        $is_success=1;
                        }
                        else {
                            DB::rollback();
                            // DB::connection('pgsql')->rollback();
                           
                            // return redirect($back_url)->with('error', 'Error! Please try again.');
                        }
                        
                        ////// i

                    }
                    else{
                    $code=400; 
                    $response_text='Response Not recive';
                    $is_success=0;
                    }
                }
                
            

               
            }
            }

                // $return_arr['httpcode']= $httpcode;
                $return_arr['is_success']=$is_success;
                $return_arr['response_text']=$response_text;
                // $return_arr['message']=$message;
                $return_arr['code']=$code;
                // $return_arr['match_found']=$match_found;
                return $return_arr;

            } catch (\Exception $e) {
                dd($e);
            }             
       }

}