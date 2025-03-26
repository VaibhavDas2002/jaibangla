<?php

namespace App\Traits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use Carbon\Carbon;
use App\Scheme;
use App\Helpers\JWTToken;
use App\Helpers\APICurl;
trait TraitCMOValidate {

    public function generateOTP(){
        $is_success=1;
        $post_url = 'https://cmo.wb.gov.in/cmosvc/user/generateotp/';
        // $headers = array(
        //     'Content-Type: application/json'
        // );
        $data = array("user_name" =>"9559000099");
        $data_string = json_encode($data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $post_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $headers = array();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $errorCurl = curl_error($curl);
        $post_response = curl_exec($curl);
        // dd($post_response);
        if (curl_errno($curl)) {
            $response_text = curl_error($curl);
            $is_success=0;
            return -1;
        }else{
           // dd('345');
            $post_response=json_decode($post_response);
            if($post_response->Exception == false && $post_response->Errors == null){
                cache(['cmo_otp' => 'value'], Carbon::now()->addSeconds(600));
                return 1;
            }else{
                return 0;
            }
            
        }
    }
    public function authiticated(){
        $return_arr=array();
        $httpcode=NULL;$response_text=NULL;$message=NULL; $message=NULL;$tokenRefId=NULL;$token=NULL;
        $is_success=1;
        $post_url = 'https://cmo.wb.gov.in/cmosvc/user/login/';
        // $headers = array(
        //     'Content-Type: application/json'
        // );
        $data = array("user_name" =>"9559000099","otp" => "191232","login_as_position" => "14556");
        $data_string = json_encode($data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $post_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $errorCurl = curl_error($curl);
        $post_response = curl_exec($curl);
        if (curl_errno($curl)) {
            $response_text = curl_error($curl);
            $is_success=0;
        }
        else{
            $post_response = json_decode($post_response, true);
            $response_text = $post_response; 
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if($httpcode==200){
                if (isset($response_text['Token'])) {
                    $token = $response_text['Token'];
                   return $token;
                }
            }    
       }
    }
    public function pullNewCmo($from_date,$to_date){
        // dump($from_date);dump($to_date);die;
          $token='';
           $otp_return_status= $this->generateOTP();
        //    dd($otp_return_status);
           if($otp_return_status==1){
            $token= $this->authiticated();
           }
           else{
            $token=$this->authiticated();
           }
        if($token!=''){
            $tokenParts = explode('.', $token);
            $tokenPayload = base64_decode($tokenParts[1]);
            $payloadData = json_decode($tokenPayload);
        }
        if (isset($payloadData->exp)) {
            $tokenExpirationTime = $payloadData->exp;
            $currentTime = time();
            $expirationDateTime = date('Y-m-d H:i:s', $tokenExpirationTime);
            if ($tokenExpirationTime <= $currentTime) {
                // Token is about to expire, renew it
                $this->authiticated();
                $token = Cache::get('CMO_validate_token');
            }
        } else {
            // echo "Token does not contain an expiration time claim.";
        }
        $return_arr=array();
        $httpcode=NULL;$response_text=NULL;$message=NULL; $message=NULL;$code=NULL;$last_biometric=NULL;$aadharListJson=NULL; 
        $is_update=0;
        $is_success=0;
        $match_found=0;
        $time=time();
        $post_url = 'https://cmo.wb.gov.in/cmosvc/shared/wcdpullgriev/';
        $curl = curl_init($post_url);
        $headers = array(
            'Content-Type: application/json',
            'Authorization: '.$token,
        );
        // $data = array("from_date_time" =>"2024-11-01 10:00:00","to_date_time"=>"2024-11-25 10:00:00","status"=>"3"); 
        $data = array("from_date_time" => $from_date,"to_date_time"=> $to_date,"grievance_category"=> [43,44,45],"status"=>"3"); 
        $data_string = json_encode($data);
        curl_setopt($curl, CURLOPT_URL, $post_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); //timeout in seconds
        $post_response = curl_exec($curl);
        if (curl_errno($curl)) {
            $response_text = curl_error($curl);
            return response()->json([
                'status' => 500,
                // 'message' => 'Data Fetch Successfully',
            ]);
        }else{
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            $post_response=json_decode($post_response);
            // dd($post_response);
            if ($post_response->Exception == false && $post_response->Errors==null) {
                $insertArray = array();
                DB::beginTransaction();
                $insert = DB::table('cmo.cmo_response_json')->insert(['fetch_request_token'=>$token, 'received_data'=>json_encode($post_response->Data->details),'from_date'=>$from_date,'to_date'=>$to_date]);
                if($insert){
                    DB::commit();
                    return response()->json([
                        'status' => 200,
                    ]);
                }else{
                    DB::rollback();
                    return response()->json([
                        'status' => 400,
                    ]);
                }
            }else {
                if (isset($post_response->Errors->Business_Errors) && !empty($post_response->Errors->Business_Errors)) {
                    $message = $post_response->Errors->Business_Errors[0]->Message;
                } else {
                    $message = 'Something Went Wrong! please try again';
                }
                return response()->json([
                    'status' => 300,
                    'message' => $message,
                ]);
            }
        }
       
    }
    public function submitNewATR($data){
        $token='';
           $otp_return_status= $this->generateOTP();
           if($otp_return_status==1){
            $token= $this->authiticated();
           }
           else{
            $token=$this->authiticated();
           }
        if($token!=''){
            $tokenParts = explode('.', $token);
            $tokenPayload = base64_decode($tokenParts[1]);
            $payloadData = json_decode($tokenPayload);
        }
        if (isset($payloadData->exp)) {
            $tokenExpirationTime = $payloadData->exp;
            $currentTime = time();
            $expirationDateTime = date('Y-m-d H:i:s', $tokenExpirationTime);
            if ($tokenExpirationTime <= $currentTime) {
                $this->authiticated();
                $token = Cache::get('CMO_validate_token');
            }
            // echo "Token Expiration Time: $expirationDateTime";
        } else {
            // echo "Token does not contain an expiration time claim.";
        }
        $return_arr=array();
        $httpcode=NULL;$response_text=NULL;$message=NULL; $message=NULL;$code=NULL;$last_biometric=NULL;$aadharListJson=NULL; 
        $is_update=0;
        $is_success=0;
        $match_found=0;
        $time=time();
        $post_url = 'https://cmo.wb.gov.in/cmosvc/shared/wcdpushgrievatr/';
        $curl = curl_init($post_url);
        $headers = array(
            'Content-Type: application/json',
            'Authorization:'.$token,
        );
        $json_data = json_encode($data);
        curl_setopt($curl, CURLOPT_URL, $post_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); //timeout in seconds
        $post_response = curl_exec($curl);
        // dd($post_response);
        if ($post_response === false) {
            $response_text = curl_error($curl);
           return [
                'status' =>500,
                'message' => $response_text
            ];
        }
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $post_response=json_decode($post_response);
        if ($post_response === null) {
            return [
                'status' =>500,
                'message' => 'Invalid JSON response from server'
                ];
        }
        if (isset($post_response->Data->Message)) {
            $message = $post_response->Data->Message;
            $exception = $post_response->Exception ?? null; // Handle missing exception safely
        } else {
            $message = 'No Message';
            $exception = null;
        }
        return [
        'status' =>200,
        'message' => $message,
        'exception' => $exception
        ];
    }
}