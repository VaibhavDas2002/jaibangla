<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UpdateBenDetails;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Helpers\APICurl;
use App\Helpers\JWTToken;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Configduty;
use App\getModelFunc;
use App\UrbanBody;
use App\GP;
use App\MapLavel;
use Maatwebsite\Excel\Facades\Excel;
use App\DocumentType;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;
use Carbon\Carbon;
use App\District;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use App\DsPhase;
use App\Scheme;
use App\RejectRevertReason;
use App\AcceptRejectInfo;

class DupFetchDetailsController extends Controller
{
    public function dupFetching(){
     
        // $serverip = Config::get('constants.lb60server');
        $serverip = 'http://172.25.154.28';
        $post_url = $serverip.'/api/dupResponseBack';
         $curl = curl_init($post_url);
        $headers = array(
        'Content-Type: application/json'
        );
        $data = array("bank_code" => '765755875', "scheme_id" => '10');
        $data_string = json_encode($data);
            header("Access-Control-Allow-Origin: *");
            curl_setopt($curl, CURLOPT_URL, $post_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $post_response = curl_exec($curl);
            dd($post_response);
        // $curl = curl_init($post_url);
        // header("Access-Control-Allow-Origin: *");
        // curl_setopt($curl, CURLOPT_URL, $post_url);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        // curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        // $result = curl_exec($curl);
        // dd($result);
        // $errorCurl = curl_error($curl);

        // curl_close($curl);
        // $ch = curl_init();

        // curl_setopt($ch, CURLOPT_URL, $post_url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        // $headers = array();
        // $headers[] = 'Content-Type: application/json';
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // $result = curl_exec($ch);
        // dd($result);
        // $errorCurl='';
        // if (curl_errno($ch)) {
        //     $errorCurl = curl_error($ch);
        // }
        // curl_close($ch);

        // dd($result);
            // if ($result) {
            //   $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            //   curl_close($curl);
              
            //   if ($httpcode == 200) {
            //     $post_response_lb = json_decode($result);
            //     dd($post_response_lb);
            //     $is_success = $post_response_lb->is_success;
            //     if($is_success){
                    
            //     }
            //   }
            // }
    }

    public function returnResponse(Request $request){
      try {
        // dd('ok');
        // $bank_code = $request->bank_code;
        // $scheme_id = $request->scheme_id;
        // $duplicate_lb_rows = DB::connection('pgsql_lb_mainwrite')->select("select application_id from lb_scheme.duplicate_bank_view where trim(bank_code)='" . $bank_code . "'");
        // dd($duplicate_lb_rows);
        return response()->json(["is_success" => true], 200);
    } catch (\Exception $e) {
        return response()->json(["is_success" => false], 400);
    }
    }
}
