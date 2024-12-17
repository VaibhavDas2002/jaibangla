<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Configduty;
use App\MapLavel;
use App\Scheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Helpers\BanglaSahayataKendraEntry;

class BSKFetchApplicationDataController extends Controller
{
    public function getDatewiseBskEntry(Request $request) {
        $mobile_no = $request->UserId;
        $userObj = User::where('mobile_no', $mobile_no)->first();
        if (isset($userObj)) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $query = "";
            // is_bsk=true AND 
            $query = "select id,bsk_ticket_no,updated_at,payment_count*1000 AS paidamt, 
            CASE WHEN next_level_role_id=0 THEN 'Approved' 
            WHEN next_level_role_id IS NULL THEN 'Applied'
            WHEN is_verified=1 and is_approved=0 and is_rejected=0 THEN 'Verified' 
            WHEN is_rejected=1 THEN 'Rejected' END AS appl_status from manabik.beneficiary where 
	        created_at::date>='".$from_date."'::date and created_at::date<='".$to_date."'::date
            UNION ALL
            select id,bsk_ticket_no,updated_at,payment_count*1000 AS paidamt, 
            CASE WHEN next_level_role_id=0 THEN 'Approved' 
            WHEN next_level_role_id IS NULL THEN 'Applied'
            WHEN next_level_role_id > 0 THEN 'Verified' 
            WHEN next_level_role_id < 0 THEN 'Rejected' END AS appl_status from manabik.beneficiary_bsk 
            where created_at::date>='".$from_date."'::date and created_at::date<='".$to_date."'::date";

            $data = DB::connection('pgsql')->select($query);
            $finalArr = [];
            foreach ($data as $key) {
                $finalArr[]=[
                    'AppNo' => $key->id,
                    'TicktNo' => $key->bsk_ticket_no,
                    'Appl_Status' => $key->appl_status,
                    'UpdatedOn' => $key->updated_at,
                    'deptpayrefno' => '',
                    'txn' => '',
                    'bankrefno' => '',
                    'paidamt' => $key->paidamt,
                ];
            }
            return response()->json(['data' => $finalArr]);
        }
        else {
          return response()->json(['error'=>'User not found in the jai bangla portal']);
        } 
    }

    public function getSingleEntryStatus(Request $request) {
        $ApplNo = $request->ApplNo;
        $query = "";
        $query = "select id,bsk_ticket_no,created_at,updated_at,
        CASE WHEN next_level_role_id=0 THEN 'Approved' 
        WHEN next_level_role_id IS NULL THEN 'Applied'
        WHEN next_level_role_id > 0 THEN 'Verified' 
        WHEN next_level_role_id < 0 THEN 'Rejected' END AS appl_status from manabik.beneficiary_bsk where id=".$ApplNo."
        UNION ALL
        select id,bsk_ticket_no,created_at,updated_at,
        CASE WHEN next_level_role_id=0 THEN 'Approved' 
        WHEN next_level_role_id IS NULL THEN 'Applied'
        WHEN is_verified=1 and is_approved=0 and is_rejected=0 THEN 'Verified' 
        WHEN is_rejected=1 THEN 'Rejected' END AS appl_status from manabik.beneficiary where id=".$ApplNo;
        // return $query;
        $data = DB::connection('pgsql')->select($query);
        if (count($data) > 0) {
            $AppNo = $data[0]->id;
            $TicktNo = $data[0]->bsk_ticket_no;
            $Appl_Status = $data[0]->appl_status;
            $UpdatedOn = $data[0]->updated_at;
            return response()->json(['AppNo' => $AppNo, 'TicktNo' => $TicktNo, 'Appl_Status' => $Appl_Status, 'UpdatedOn' => $UpdatedOn]);
        }
        else {
            return response()->json(['error'=>'Beneficiary not found in the jai bangla portal']);
        }
        
    }
}
