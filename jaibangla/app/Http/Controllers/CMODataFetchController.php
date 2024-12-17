<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\JWTToken;
use App\Helpers\APICurl;
class CMODataFetchController extends Controller
{
    public function dataFetch(){
        ini_set('memory_limit', '-1');
        $header = array(
            "typ" => "JWT",
            "alg" => "HS512"
        );
        $formattedDate = 'y' . date('Y') . 'm' . date('m') . 'd' . date('d');
        $payload = array(
            "username" => "cmo",
            "password" => $formattedDate
        );
        $secret_key = 'CMO@2023';
        $token = JWTToken::getJWTToken($header, $payload, $secret_key);
        $post_url = 'http://172.25.140.14:9091/wcd_push_api';
        $headers = array(
            'Content-Type: application/json'
        );
        $data = array("token" =>$token);
        $data_string = json_encode($data);
        $api_response = APICurl::cmoFetchCurl($post_url, $data_string);
        if ($api_response['errorCurl']=='') {
            $decodedJson = json_decode($api_response['result'], true);
            $dataArray = json_decode($decodedJson['data'], true);
            $insertArray = array();
            DB::beginTransaction();
            $insert = DB::table('cmo.cmo_response_json')->insert(['fetch_request_token'=>$token, 'received_data'=>json_encode($dataArray)]);
            if($insert){
                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Applications Updated Successfully',
                ]);
            }else{
                DB::rollback();
                return response()->json([
                    'status' => 200,
                    'errors' => 'No record found',
                ]);
            }
        }else {
            dd($api_response['errorCurl']);
        }
    }
    public function fetchUpdate(){
      
        $update = DB::select("SELECT insert_data_from_jsonb_array()");
       
    }
}
