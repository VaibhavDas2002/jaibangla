<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\APICurl;
use App\Helpers\JWTToken;
use Illuminate\Support\Facades\DB;

class SRSDataPullController extends Controller
{
    public function __construct()
    {
        set_time_limit(0);
        date_default_timezone_set('Asia/Kolkata');
    }

    public function dataSrsPull(Request $request)
    {
        try {
            $scheme_id = $request->scheme_id;
            $limit = $request->limit;
            $query = "select string_agg(aadhar_no,',') AS aadhar_no from 
            (select concat(TRIM(aadhar_no)) as aadhar_no from pension.beneficiary where (next_level_role_id IS null OR next_level_role_id = 0 OR next_level_role_id > 0) AND (dup_aadhar=0 OR dup_aadhar IS NULL) AND LENGTH(TRIM(aadhar_no)) = 12 AND fetch_from_sr IS null AND scheme_id = ".$scheme_id." AND ((TRIM(aadhar_no) IS NOT null OR TRIM(aadhar_no) != '')) limit ".$limit.") as t";
            $aadharData = DB::connection('pgsql')->select($query);
            $aadharArray = explode(',', $aadharData[0]->aadhar_no);
            // dump($aadharArray);

            // dd($updateQuery);
            $post_url = 'https://ifmswbuat.nic.in/srsapi/api/wbsrs/Get-Family-Data'; //164.100.199.136
            $headers = array(
                'Content-Type: application/json'
            );
            $data = array("uid_list" => $aadharArray, "password" => '^Xj8LGf$jwYY&7ebIxUE');
            $data_string = json_encode($data);
            // dump($data);
            // print_r($data_string); 
            // die;
            $api_response = APICurl::callingAPIForSR($post_url, $headers, $data_string);
            // dump($data);
            // print_r($data_string); 
            // print_r($api_response); die;
            if ($api_response['errorCurl']=='') {
                $decodedJson = json_decode($api_response['result'], true);
                $dataArray = $decodedJson['data']['familyDataRecords']; 
                // dump($dataArray);
            $insertArray = array();
            $p=0;
            foreach ($dataArray as $key) {
                $insertArray[$p]['aadhar_no']= $key['uid'];
                $insertArray[$p]['card_type_desc']=$key['cardTypeDesc']; 
                $insertArray[$p]['hof_member_id']=$key['hofMemberId'];
                if (empty($key['nameAsInAadhar'])) {
                    $insertArray[$p]['name_as_in_aadhar']=null;
                } else {
                    $insertArray[$p]['name_as_in_aadhar']=$key['nameAsInAadhar'];
                }
                if (empty($key['memberDob'])) {
                    $insertArray[$p]['member_dob']=null;
                } else {
                    $insertArray[$p]['member_dob']=$key['memberDob'];
                }
                $insertArray[$p]['member_name']=$key['memberName']; 
                $insertArray[$p]['ration_card_member_id']=$key['rationCardMemberId'];
                $insertArray[$p]['gender']=$key['gender'];
                $insertArray[$p]['lgd_district_code']=$key['lgdDistrictCode'];
                $insertArray[$p]['lgd_district_name']=$key['lgdDistrictName'];
                $insertArray[$p]['lgd_block_code']=$key['lgdBlockCode'];
                $insertArray[$p]['lgd_block_name']=$key['lgdBlockName'];
                $insertArray[$p]['lgd_gp_code']=$key['lgdGpCode'];
                $insertArray[$p]['lgd_gp_name']=$key['lgdGpName'];
                $insertArray[$p]['aadhar_flag']=$key['aadharFlag'];
                $insertArray[$p]['fetching_at']=date("Y-m-d H:i:s");
                $insertArray[$p]['scheme_id']=$scheme_id;
                $p++;
            }
            // dd($insertArray);
            $is_insert = DB::connection('pgsql')->table('social_registry.aadhar_ks_api')->insert($insertArray);
            $selectQuery = DB::connection('pgsql')->table('pension.beneficiary')->whereNull('fetch_from_sr')->whereIn('aadhar_no', $aadharArray);
            $whereRaw = "((next_level_role_id IS null OR next_level_role_id = 0 OR next_level_role_id > 0) AND (dup_aadhar=0 OR dup_aadhar IS NULL) AND (LENGTH(TRIM(aadhar_no)) = 12 AND (TRIM(aadhar_no) IS NOT null OR TRIM(aadhar_no) != '')))";
            $is_update = $selectQuery->whereRaw($whereRaw)->update(['fetch_from_sr' => 1]);
            // $updateApplicationId = DB::table('social_registry.aadhar_ks_api as sr')
            //                         ->join('aadhar.ben_aadhar_details as ba', function ($join) {
            //                             $join->on(DB::raw('TRIM(sr.aadhar_no)'), '=', DB::raw('TRIM(ba.decoded_aadhar)'))
            //                                 ->whereColumn('sr.lgd_district_code', 'ba.created_by_dist_code');
            //                         })
            //                         ->where('sr.aadhar_flag', 1)
            //                         ->whereNull('sr.application_id')
            //                         ->update(['application_id' => DB::raw('ba.application_id')]);
            if (isset($is_insert) && isset($is_update)) {
                print $p.' Inserted Successfully';
            } else {
                print 'Something Went Wrong!!';
            }
            } else {
                print_r($api_response['errorCurl']);
            }
        } catch (\Exception $e) {
            print $e->getMessage();
        }
    }
}
