<?php
namespace App\Traits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Scheme;
trait TraitAadharValidate
{
    protected $clientID1Aadhar = 'c5865e5302fd508e4d89499f1f6d116e';
    protected $clientSecret1Aadhar = '03bce35108f7107ad2810c5a5c5562ef';
    protected $clientID2Aadhar = 'e3db8f38-d149-4f4d-b74b-a641e42354c6';
    protected $clientSecret2Aadhar = '25647cd7c088a0d9da069882808ef41882efe20fffead2b63b68a3c7c30ed07aa5a8b737a6b4068b72aeb43fd6bb1741f1d9f5ca6a7c12d5b4215c1faddcfd53';
    protected $clientID3 = 'c5865e5302fd508e4d89499f1f6d116e';
    protected $clientSecret3 = '03bce35108f7107ad2810c5a5c5562ef';
    protected $clientID4 = 'e3db8f38-d149-4f4d-b74b-a641e42354c6';
    protected $clientSecret4 = '';
    public function authiticated()
    {

        $return_arr = array();
        $httpcode = NULL;
        $response_text = NULL;
        $message = NULL;
        $message = NULL;
        $tokenRefId = NULL;
        $token = NULL;
        $is_success = 1;
        $post_url = 'https://wbgw.napix.gov.in/wb/food-supplies/ksapi-signin';
        $curl = curl_init($post_url);
        $headers = array(
            'Content-Type: application/json',
            'client_id:' . $this->clientID1Aadhar,
            'client_secret: ' . $this->clientSecret1Aadhar,

        );
        $data = array("clientId" => $this->clientID2Aadhar, "clientSecret" => $this->clientSecret2Aadhar);
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
            $is_success = 0;

        } else {
            $post_response = json_decode($post_response);
            $response_text = $post_response;
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($httpcode == 200) {
                if ($post_response->status->msg == 'Success') {
                    $token = $post_response->status->token;
                    // Cache::forever('Aadhar_validate_tokenRefId',$tokenRefId);
                    Cache::forever('Aadhar_validate_token', $token);

                }

            }
        }
    }

    public function validate_Aadhar($aadhar_no = NULL, $ben_fullname = NULL, $application_id = NULL)
    {

        $token = Cache::get('Aadhar_validate_token');
        // $tokenRefId = Cache::get('Aadhar_validate_tokenRefId');

        if (empty($token)) {
            $this->authiticated();

        }
        if ($token) {
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
                $token = Cache::get('Aadhar_validate_token');
                $tokenRefId = Cache::get('Aadhar_validate_tokenRefId');

            }
            // echo "Token Expiration Time: $expirationDateTime";
        } else {
            // echo "Token does not contain an expiration time claim.";
        }
        // $currentTime = time();

        $return_arr = array();
        $httpcode = NULL;
        $response_text = NULL;
        $message = NULL;
        $message = NULL;
        $code = NULL;
        $last_biometric = NULL;
        $aadharListJson = NULL;
        $is_update = 0;
        $is_success = 0;
        $match_found = 0;
        $time = time();
        $post_url = 'https://wbgw.napix.gov.in/wb/food-supplies/khadyasathi-info-via-aadhar';
        $curl = curl_init($post_url);
        $headers = array(
            'Content-Type: application/json',
            'clientID:' . $this->clientID3,
            'clientSecret: ' . $this->clientSecret3,
            'Authorization: Bearer ' . $token,

        );
        //dd( $headers);
        $params = array(array("txn" => $application_id, "PData" => $aadhar_no));

        $data = array("clientId" => $this->clientID4, "param" => $params, "type" => "1");
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
        } else {
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            $post_response = json_decode($post_response);
            $aadharList = $post_response->aadharResult->aadharList;
            $aadharListJson = json_encode($aadharList);
            $response_text = json_encode($post_response);

            if ($httpcode == 200) {
                $is_success = 1;
                if ($post_response->status->msg == 'Success') {
                    if ($post_response->aadharResult->aadharList[0]->NAME_AS_IN_AADHAR == 'Not found') {
                        $code = 4;
                        $message = 'yet not updated';
                    } else {
                        $ben_fullname = str_replace(' ', '', $ben_fullname);
                        $appl_name = str_replace(' ', '', $post_response->aadharResult->aadharList[0]->NAME_AS_IN_AADHAR);
                        if (strtoupper($appl_name) == strtoupper($ben_fullname)) {
                            // if(!empty($post_respons->txnTime) && $post_respons->txnTime!='Not found'){
                            //     $last_biometric=$post_respons->txnTime;
                            // }
                            $match_found = 1;
                            $code = 1;
                            $message = 'Bioauth from Aadhaar Number has been checked';
                        } else {
                            $code = 2;
                            $message = 'Name not Match';
                        }
                    }
                } else {
                    $code = -200;
                    $message = 'Bioauth from Aadhaar Number yet not updated';
                }
            } else {
                $code = $httpcode;
                if ($code == 401) {
                    $this->authiticated();
                }
            }
        }
        $return_arr['httpcode'] = $httpcode;
        $return_arr['is_success'] = $is_success;
        $return_arr['response_text'] = $response_text;
        $return_arr['message'] = $message;
        $return_arr['code'] = $code;
        $return_arr['match_found'] = $match_found;
        $return_arr['aadhar_json'] = $aadharListJson;
        return $return_arr;
    }
    public function RationcheckInsert($distCode, $beneficiary_id, $scheme_id, $ben_fullname, $ip, $aadhar_no, $blockCode, $user_id, $dob)
    {

        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (!empty($scheme_obj->short_code)) {
            $schema = $scheme_obj->short_code;
        } else {
            $schema = "pension";
        }
        $ben_fullname = $ben_fullname;
        $session_lb_aadhaarcard = array();
        $session_lb_rationcard = array();
        $aadhaar_validation_arr = array();
        $aadhaar_validation_arr['application_id'] = $beneficiary_id;
        $aadhaar_validation_arr['aadhar_no'] = $aadhar_no;
        $aadhaar_validation_arr['scheme_id'] = $scheme_id;
        $aadhaar_validation_arr['user_id'] = $user_id;
        $aadhaar_validation_arr['ip_address'] = $ip;
        $Aadhaar_response_validation_arr = $this->validate_Aadhar($aadhar_no = $aadhar_no, $ben_fullname = $ben_fullname, $appliation_id = $beneficiary_id);
        if ($Aadhaar_response_validation_arr['is_success'] == 1) {
            $aadharJson = json_decode($Aadhaar_response_validation_arr["aadhar_json"]);
            $aadhaar_validation_arr['aadhaar_json'] = $Aadhaar_response_validation_arr['aadhar_json'];
            if ($aadharJson[0]->NAME_AS_IN_AADHAR == '') {
                $aadhaar_validation_arr['name_as_in_aadhaar'] = NULL;
            } else {
                $aadhaar_validation_arr['name_as_in_aadhaar'] = $aadharJson[0]->NAME_AS_IN_AADHAR;
            }
            if ($aadharJson[0]->NAME_AS_IN_RC == '') {
                $aadhaar_validation_arr['name_as_in_rc'] = NULL;
            } else {
                $aadhaar_validation_arr['name_as_in_rc'] = $aadharJson[0]->NAME_AS_IN_RC;
            }
            $aadhaar_validation_arr['rationcard_no'] = $aadharJson[0]->RationcardNo;
            $aadhaar_validation_arr['card_status'] = $aadharJson[0]->Card_Status;
            $aadhaar_validation_arr['rc_ks_status'] = 2;
            $aadhaar_validation_arr['family_id'] = $aadharJson[0]->FamilyID;
            $aadhaar_validation_arr['created_by_dist_code'] = $aadharJson[0]->LGD_DistrictCode;
            $aadhaar_validation_arr['created_by_local_body_code'] = $aadharJson[0]->LGD_BlockCode;
            $c_time1 = date('Y-m-d H:i:s', time());
            $aadhaar_validation_arr['api_response_time'] = $c_time1;
            $aadhaar_validation_arr['api_hit_time'] = date('Y-m-d H:i:s', time());
            $aadhaar_validation_arr['response_id'] = $aadharJson[0]->txn;
            $aadhaar_validation_arr['httpcode'] = $Aadhaar_response_validation_arr['httpcode'];
            $aadhaar_validation_arr['dob_kh'] = $aadharJson[0]->DOB;
            $pension_details = array();
            if (!empty($dob) && !empty($aadharJson[0]->DOB)) {
                if ($dob == $aadharJson[0]->DOB) {
                    $aadhaar_validation_arr['dob_is_match_kh'] = 1;
                    $pension_details['dob_is_match_kh'] = 1;
                    $dob_missmatch = 0;
                } else {
                    $dob_missmatch = 1;
                }
            }
            if ($Aadhaar_response_validation_arr['match_found'] == 1) {
                $pension_details['name_is_match'] = 1;
                $aadhaarcard_is_error = 0;
            } else if ($aadharJson[0]->NAME_AS_IN_AADHAR == NULL) {
                $pension_details['name_is_match'] = -2;
                $pension_details['acc_validated_aadhar'] = NULL;
                $aadhaarcard_is_error = 1;
            } else {
                $pension_details['name_is_match'] = -2;
                $pension_details['acc_validated_aadhar'] = -2;
                $aadhaarcard_is_error = 1;
            }

            DB::beginTransaction();
            $insert = DB::table('pds.aadhaar_khadyasathi')->insert($aadhaar_validation_arr);
            $pension_details['aadhaar_no_checked'] = 1;
            $pension_details['aadhaar_no_checked_lastdatetime'] = $c_time1;
            $pension_details['aadhaar_no_validation_msg'] = $Aadhaar_response_validation_arr['message'];
            $pension_details['aadhaar_no_checked_pass'] = $Aadhaar_response_validation_arr['code'];
            $pension_details['wbpds_is_sent'] = 1;
            $pension_details['wbpds_name_as_in_aadhar_sr'] = $aadharJson[0]->NAME_AS_IN_AADHAR;
            $pension_details['wbpds_ration_card_no'] = $aadharJson[0]->RationcardNo;
            $pension_details['wbpds_family_id'] = $aadharJson[0]->FamilyID;
            $pension_details['dob_kh'] = $aadharJson[0]->DOB;
            $session_lb_aadhaarcard['is_error'] = $aadhaarcard_is_error;
            $session_lb_aadhaarcard['message'] = $Aadhaar_response_validation_arr['message'];
            $session_lb_aadhaarcard['lastdatetime'] = date('d/m/Y', strtotime($c_time1));
            // dd($session_lb_rationcard);

            $update = DB::table($schema . '.beneficiary')->where('id', $beneficiary_id)->update($pension_details);
            if ($update && $insert) {
                DB::commit();
                $session_lb_aadhaarcard = $session_lb_aadhaarcard;
            } else {
                DB::rollback();
                $session_lb_aadhaarcard = array();
            }
        }
        // dd($session_lb_rationcard);
        return $session_lb_aadhaarcard;
    }
}