<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class APICurl
{
    public static function callingAPI($api_url, $headers, $data_string)
    {   
        $curl = curl_init($api_url);
        header("Access-Control-Allow-Origin: *");
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        $errorCurl = curl_error($curl);

        curl_close($curl);
        $response=array('result'=>$result,'errorCurl'=>$errorCurl);
        return  $response;
    }

    public static function cmoFetchCurl($api_url, $data_string){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        

        $result = curl_exec($ch);
        $errorCurl='';
        if (curl_errno($ch)) {
            $errorCurl = curl_error($ch);
        }
        curl_close($ch);
        $response=array('result'=>$result,'errorCurl'=>$errorCurl);
        // dd($response);
        return  $response;
    }

    public static function callingAPIForSR($api_url, $headers, $data_string)
    {   
        $curl = curl_init($api_url);
        // header("Access-Control-Allow-Origin: *");
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        $errorCurl = curl_error($curl);

        curl_close($curl);
        $response=array('result'=>$result,'errorCurl'=>$errorCurl);
        return  $response;
    }
}
