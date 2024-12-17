<?php

namespace App\Traits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Scheme;
trait TraitLifeCertificateValidate {
 protected $clientID1 = '749d53c4d9347eb7d3cd2e3711437ddb';
 protected $clientSecret1 = '7667dd2955db68e8fe11d83ebf085b7d';
 protected $clientID2 = '5221054a-1025-489a-8d13-6a09494665a8';
 protected $clientSecre2 = '107deddc0bed7533c7debd52d8fb01de37d795fd293334dd11b17bda2b6fff72394229ece8409ec5256da7b6645ae69dd30261514a6279cebda58393fa3b7303';

   public function authiticate(){
        $return_arr=array();
        $httpcode=NULL;$response_text=NULL;$message=NULL; $message=NULL;$tokenRefId=NULL;$token=NULL;
        $is_success=1;
        $post_url = 'https://wbgw.napix.gov.in/wb/food-supplies/wbulc-authenticate';
        $curl = curl_init($post_url);
        $headers = array(
            'Content-Type: application/json',
            'clientID:'.$this->clientID1,
            'clientSecret: '.$this->clientSecret1,

        );
        $data = array("clientId" =>$this->clientID2,"clientSecret" => $this->clientSecre2);
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
          if($post_response->tokenStatus=='Token generated'){
                $tokenRefId=$post_response->tokenRefId;
                $token=$post_response->token;
                Cache::forever('life_certificate_tokenRefId',$tokenRefId);
                Cache::forever('life_certificate_token',$token); 

          }
         
        }
        
        
        
   }
}

   public function validate_life_certificate($aadhar_no=NULL,$ben_fullname=NULL){
    $token = Cache::get('life_certificate_token');
    $tokenRefId = Cache::get('life_certificate_tokenRefId');
    if(empty($token) || empty($tokenRefId)){
        $this->authiticate(); 
    }
    $return_arr=array();
    $httpcode=NULL;$response_text=NULL;$message=NULL; $message=NULL;$code=NULL;$last_biometric=NULL; 
    $is_update=0;
    $is_success=0;
    $match_found=0;
    $time=time();
    $post_url = 'https://wbgw.napix.gov.in/wb/food-supplies/wbulc-lc-info-via-aadhar';
    $curl = curl_init($post_url);
    $headers = array(
                                'Content-Type: application/json',
                                'clientID:'.$this->clientID1,
                                'clientSecret: '.$this->clientSecret1,
                                'Authorization: Bearer '.$token,
                    
    );
    //dd( $headers);
    $data = array("clientId" =>$this->clientID2,"clientTxnNo" =>  $time,"tokenRefId" => $tokenRefId,"uidData" => $aadhar_no);
    $data_string = json_encode($data);
    //header("Access-Control-Allow-Origin: *");
    curl_setopt($curl, CURLOPT_URL, $post_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); //timeout in seconds
    $post_response = curl_exec($curl);
    if (curl_errno($curl)) {
        $response_text = curl_error($curl);
    }
    else{
        ////dd( $curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    //dd( $post_response);
    $post_respons=json_decode($post_response);
    $response_text=json_encode($post_response);
    if($httpcode==200){
        

        $is_success=1;
        if($post_respons->remarks=='Success'){
            if($post_respons->name=='Not found'){
                $code=4; 
                $message='Bioauth from Khadyasathi yet not updated';

            }
            else{
                $ben_fullname = str_replace(' ', '', $ben_fullname);
                $appl_name = str_replace(' ', '', $post_respons->name);
                if(strtoupper($appl_name)==strtoupper($ben_fullname)){
                if(!empty($post_respons->txnTime) && $post_respons->txnTime!='Not found'){
                    $last_biometric=$post_respons->txnTime;
                }
                                        $match_found=1;  
                                        $code=1; 
                                        $message='Bioauth from Khadyasathi has been checked';  

                }
                else{
                    $code=2; 
                    $message='Name not Match';  


                }
            }
        }
        else{
            $code=-200; 
            $message='Bioauth from Khadyasathi yet not updated';
        }
    }
    else{
        $code=$httpcode; 
        if($code==401){
           $this->authiticate(); 
        }
    }
    }
    $return_arr['httpcode']= $httpcode;
    $return_arr['is_success']=$is_success;
    $return_arr['response_text']=$response_text;
    $return_arr['message']=$message;
    $return_arr['tokenRefId']=$tokenRefId;
    $return_arr['code']=$code;
    $return_arr['match_found']=$match_found;
    $return_arr['last_biometric']=$last_biometric;
    return $return_arr;
   }
   public function bioauthcheckInsert($distCode,$beneficiary_id,$scheme_id,$ben_fullname,$ip,$aadhar_no,$blockCode,$user_id){
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
    $ben_fullname=$ben_fullname;
    $session_lb_lifecertificate=array();
    $lifecertificate_validation_arr=array();
    $lifecertificate_validation_arr['m_type']=2;
    $lifecertificate_validation_arr['beneficiary_id']=$beneficiary_id;
    $lifecertificate_validation_arr['scheme_id']=$scheme_id;
    $lifecertificate_validation_arr['created_by_local_body_code']=$blockCode;
    $lifecertificate_validation_arr['api_hit_time']=date('Y-m-d H:i:s', time());
    $lifecertificate_validation_arr['loginid']=$user_id;
    $lifecertificate_validation_arr['ip_address']= $ip;
    $lc_response_validation_arr=$this->validate_life_certificate($aadhar_no = $aadhar_no,$ben_fullname= $ben_fullname); 
    $lifecertificate_validation_arr['httpcode']= $lc_response_validation_arr['httpcode'];
    if($lc_response_validation_arr['is_success']==1){
        DB::beginTransaction();

    $pension_details=array();
    $last_biometric=NULL;
    $c_time1=date('Y-m-d H:i:s', time());
    $lifecertificate_validation_arr['response_text']=$lc_response_validation_arr['response_text'];
    $lifecertificate_validation_arr['api_response_time']= $c_time1;
    $insert=DB::table('pension.ben_lc_api_response_track')->insert($lifecertificate_validation_arr);
    $pension_details['life_certificate_checked'] = 1;
    $pension_details['life_certificate_lastdatetime'] =  $c_time1;
    $pension_details['life_certificate_msg'] =  $lc_response_validation_arr['message'];
    $pension_details['life_certificate_pass'] =  $lc_response_validation_arr['code'];

    if($lc_response_validation_arr['match_found']==1){
        if(!empty($lc_response_validation_arr['last_biometric'])){
            $pension_details['last_biometric']=$lc_response_validation_arr['last_biometric'];
            $last_biometric=date('d/m/Y',strtotime($lc_response_validation_arr['last_biometric']));
        }
        $lc_is_error=0;
    }else{
        $lc_is_error=1;
        $last_biometric=date('d/m/Y',strtotime($c_time1));

   }
   $session_lb_lifecertificate['is_error']=$lc_is_error;
   $session_lb_lifecertificate['message']=$lc_response_validation_arr['message'];
   $session_lb_lifecertificate['last_biometric']=$last_biometric;
   $update=DB::table($schema . '.beneficiary')->where('id', $beneficiary_id)->update($pension_details);
   if($update && $insert){
    DB::commit();
    $session_lb_lifecertificate=$session_lb_lifecertificate;

   }
   else{
    DB::rollback();
    $session_lb_lifecertificate=array();

   }
 }
 return $session_lb_lifecertificate;
}

}