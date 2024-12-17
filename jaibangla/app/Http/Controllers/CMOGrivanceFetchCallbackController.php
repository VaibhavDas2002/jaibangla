<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\APICurl;
use App\Helpers\JWTToken;
use Illuminate\Support\Facades\DB;

class CMOGrivanceFetchCallbackController extends Controller
{
    public function fetchCMOGrivanceData() {
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
        // dd($token);
        $post_url = 'http://202.61.117.23:9091/wcd_push_api';
        $headers = array(
            'Content-Type: application/json'
        );
        $data = array("token" =>$token);
        $data_string = json_encode($data);
        $api_response = APICurl::callingAPI($post_url, $headers, $data_string);
        dd($api_response);
        if ($api_response['errorCurl']=='') {
            $decodedJson = json_decode($api_response['result'], true);
            $dataArray = json_decode($decodedJson['data'], true);
            
            $insertArray = array();
            $p=0;
            foreach ($dataArray as $key) {
                $insertArray[$p]['gid']= $key['gid'];
                $insertArray[$p]['dcode']=$key['dcode']; 
                $insertArray[$p]['dname']=$key['dname'];
                $insertArray[$p]['bcode']=$key['bcode'];
                $insertArray[$p]['bname']=$key['bname'];
                $insertArray[$p]['gpcode']=$key['gpcode']; 
                $insertArray[$p]['gpname']=$key['gpname'];
                $insertArray[$p]['pscode']=$key['pscode'];
                $insertArray[$p]['psname']=$key['psname'];
                $insertArray[$p]['doc']=$key['doc'];
                $insertArray[$p]['comname']=$key['comname'];
                $insertArray[$p]['comadd']=$key['comadd'];
                $insertArray[$p]['gdes']=$key['gdes'];
                $insertArray[$p]['mob_no']=$key['Mob_No'];
                $insertArray[$p]['usbid']=$key['usbid'];
                $insertArray[$p]['lgd_dist']=$key['lgd_dist'];
                $insertArray[$p]['lgd_block']=$key['lgd_block'];
                $insertArray[$p]['age']=$key['age'];
                $insertArray[$p]['gender']=$key['gender'];
                $insertArray[$p]['btype']=$key['btype'];
                $p++;
            }

            DB::table('cmo.cmo_grivance_details')->insert($insertArray);
        } else {
            dd($api_response['errorCurl']);
        }
    }

    public function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
