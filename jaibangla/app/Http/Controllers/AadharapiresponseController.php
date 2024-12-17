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

class AadharapiresponseController extends Controller
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
        
                //$scheme_id = $request->scheme_id;
                $district_code = $request->dist_code;

                
               // $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
                //$schema = $scheme_obj->short_code;

               

                


                $token = Cache::get('Aadhar_validate_token');
                
               // $tokenRefId = Cache::get('aadhar_tokenRefId');
                $limit = $request->limit;
                if(empty($token)){
                    $this->authiticate(); 
                }

                // if($limit>100)
                // {

                //     $is_success=0;
                //     // DB::connection('pgsql_encwrite')->rollback();
                //     $code=400; 
                //     $response_text='Limit size max 100';

                // }

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
                

               
              
            $query = "SELECT json_agg(t) as set FROM (SELECT aadhar_no AS \"PData\", application_id::text AS txn FROM pds.aadhaar_khadyasathi_api WHERE LENGTH(aadhar_no) = 12 
            And rc_ks_status=0   AND created_by_dist_code=$district_code order by sl_no  LIMIT  $limit ) AS t
            ";

// $query = "SELECT json_agg(t) as set FROM (SELECT aadhar_no AS \"PData\", application_id::text AS txn FROM pds.aadhaar_khadyasathi_api WHERE LENGTH(aadhar_no) = 12 
// And rc_ks_status=0 and scheme_id=$scheme_id  and created_by_dist_code=$district_code order by sl_no  LIMIT  $limit ) AS t
// ";


            $aadhar_no_set = DB::connection('pgsql')->select($query);
            $aadhar_no=$aadhar_no_set[0]->set;

            //dd($aadhar_no);

            // $query_check = "SELECT application_id FROM pds.aadhaar_khadyasathi_api 
            // WHERE LENGTH(aadhar_no) = 12 
            // AND rc_ks_status = 0 
            // AND scheme_id = $scheme_id  
            // AND created_by_dist_code = $district_code 
            // ORDER BY application_id ASC 
            // LIMIT $limit";

            // $query_set = DB::connection('pgsql')->select($query_check);

            // $applicationid_arr = array();

            // foreach ($query_set as $item_check) {
            // array_push($applicationid_arr, (int)$item_check->application_id);
            // }

            // $status = 0;
            // $update_rc_ks_status = [
            // 'rc_ks_status' => 1,
            // ];

            // $update_status = DB::table('pds.aadhaar_khadyasathi_api')
            // ->whereIn('application_id', $applicationid_arr)
            // ->where('scheme_id', $scheme_id)
            // ->where('rc_ks_status', $status)
            // ->where('created_by_dist_code', $district_code)
            // ->update($update_rc_ks_status);


            $c_time1=date('Y-m-d H:i:s', time());
            $update_status = DB::select("
            WITH ben_id AS (
                SELECT application_id FROM pds.aadhaar_khadyasathi_api 
                WHERE LENGTH(aadhar_no) = 12 
                AND rc_ks_status = 0 AND created_by_dist_code = '" . $district_code . "' 
                ORDER BY sl_no 
                LIMIT " . $limit . "
            )
            UPDATE pds.aadhaar_khadyasathi_api b
            SET rc_ks_status = 1,api_hit_time='".$c_time1."'
            FROM ben_id p
            WHERE b.application_id = p.application_id
            
        ");

                // dump(json_decode($aadhar_no));

               //dd($update_status);

               // $update = DB::connection('pgsql')->update($update_status);

               

            
                
              
                
                if(empty($aadhar_no))

                {
                    $is_success=0;
                    // DB::connection('pgsql_encwrite')->rollback();
                    $code=400; 
                    $response_text='No data Found';

                }
                else{
                    
                    

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
                    // }

                    // else{
                    // $code=400; 
                    // $response_text='Data sent to API failed';
                    // $is_success=0;
                    // }
                if (curl_errno($curl))
                 {

                $response_text = curl_error($curl);
                $is_success=0;

                }
                else
                {
                    
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
                                $applicante_value['Ekyc_Mode']=$item['Ekyc_Mode'];
                                $applicante_value['CreatedOn']=$item['CreatedOn'];
                                $applicante_value['AADHAAR_VERIFIED']=$item['AADHAAR_VERIFIED'];
                                $applicante_value['EKYC_DONE']=$item['EKYC_DONE'];
                                $applicante_value['Father_SpouseName']=$item['Father_SpouseName'];

                                
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

                               

                                $c_time1=date('Y-m-d H:i:s', time());

                                
                
                                    $insert_data = [
                                        'aadhaar_json' =>json_encode($applicante_value),
                                        'name_as_in_aadhaar' =>$applicante_value['NAME_AS_IN_AADHAR'],
                                        'name_as_in_rc' =>$applicante_value['NAME_AS_IN_RC'],
                                        'rationcard_no' =>$item['RationcardNo'],
                                        'rc_ks_status'=>2,
                                        'family_id'=>$item['FamilyID'],
                                        'response_id' =>$item['txn'],
                                        'card_status' =>$item['Card_Status'],
                                        'dob_kh'=>$item['DOB'],
                                        'ekyc_mode'=>$item['Ekyc_Mode'],
                                       'api_response_time'=> $c_time1,
                                    //    'api_hit_time'=>date('Y-m-d H:i:s', time()),
                                       'created_on'=>$item['CreatedOn'],
                                       'aadhar_verified'=>$item['AADHAAR_VERIFIED'],
                                       'card_catagory'=>$item['CardCategory'],
                                       'ekyc_done'=>$item['EKYC_DONE'],
                                       'father_spousename'=>$item['Father_SpouseName'],
                                       'gender'=>$item['GENDERID'],
                                       'lgd_block_code'=>$item['LGD_BlockCode'],
                                       'lgd_block_name'=>$item['LGD_BlockName'],
                                       'lgd_district_code'=>$item['LGD_DistrictCode'],
                                       'lgd_district_name'=>$item['LGD_DistrictName'],
                                       'lgd_gp_ward_code'=>$item['LGD_GP_Ward_Code'],
                                       'lgd_gp_ward_name'=>$item['LGD_GP_Ward_Name'],
                                       
                                      
                                      
                                       
        
                                        ];
                                       




                                try {


                                    
                                    
                                  
                                    $inser_bulk = DB::table('pds.aadhaar_khadyasathi_api')->where('application_id', $application_id)
                                    // ->where('scheme_id', $scheme_id)
                                    ->where('rc_ks_status', 1)
                                    ->where('created_by_dist_code', $district_code)
                                    ->update($insert_data);

                                        if ($inser_bulk ) {
                                            //dd(555);
                                        
                                            $i++;
                                            //$j++;

                                       
                                           
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

                        if ( ($i > 0 )) {

                        DB::commit();

                        $code=200; 
                        $response_text='Total - '.$i.' Aadhaar update Successfully';
                        $is_success=1;
                        }
                        else {
                            DB::rollback();
                            // DB::connection('pgsql')->rollback();
                           
                            // return redirect($back_url)->with('error', 'Error! Please try again.');
                        }
                        
                        ////// i

                    }
                     else
                    {

                        

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