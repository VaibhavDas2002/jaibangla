<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scheme;
use App\District;
use App\UrbanBody;
use App\GP;
use App\BeneficiaryPensions;
use App\PensionSc;
use App\PensionSt;
use App\Manabik;
use App\UpdateBenDetails;
use Maatwebsite\Excel\Facades\Excel;
use App\Configduty;
use App\DocumentType;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AuthChecker;


class AadharBankDupCountComtroller extends Controller
{
    public function __construct()
    {
        set_time_limit(0);
        ini_set('max_execution_time', -1);
    }
    private function getSchemaName($scheme_id)
    {
        if (!is_null($scheme_id)) {
            $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
            //$parameter['scheme_id'] = $scheme_id;
            $schema_name =  $sObj->short_code;
            //dd($schema_name);
            if (empty($schema_name)) {
                $schema_name = 'pension';
            }
            $table_name =  strtolower($schema_name) . '.beneficiaries';
        } else {
            $table_name =  'pension.beneficiaries';
        }
        return $table_name;
    }
    public function index()
    {
        $user_id = AuthChecker::getUserId();
        if (AuthChecker::AdminChecker()) {
            $schemes = Scheme::where('id','<>',20)->where('is_active', 1)->get();
            // dd($schemes);
            return view('duplicate-check/index', ['schemes' => $schemes]);
        } 
        else{
            return redirect('/')->with('success', 'Unauthorized');
        }
    }

    public function checkDuplicate(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
        $statusCode = 400;
        $response = array('error' => 'Error occured in form submit.');
        return response()->json($response, $statusCode);
        }
        try {
            $search_for = $request->search_for;
            $scheme_id = $request->scheme_id;
            $table_name = $this->getSchemaName($scheme_id);
            // echo $table_name; die;
            DB::connection('pgsql')->beginTransaction();
            if ($search_for == 'aadhar_all') {
                $query = "select 
                sum(cnt) as de_dup, now()
                from 
                (	
                    select aadhar_no ,count(1)  as cnt
                    from ".$table_name." where (aadhar_no is not null and char_length(trim(regexp_replace(aadhar_no, '\s+', ' ', 'g'))) = 12) and (next_level_role_id=0 OR next_level_role_id is null OR next_level_role_id>0) and (dup_aadhar=0 or dup_aadhar IS NULL)
                    group by aadhar_no having count(1)>1 
                ) sub";
            } elseif ($search_for == 'aadhar_present_ds') {
                $query = "SELECT 
                sum(cnt) AS de_dup, now()
                FROM 
                (	
                    SELECT aadhar_no ,count(1) AS cnt
                    FROM ".$table_name." WHERE (aadhar_no IS NOT null and char_length(trim(regexp_replace(aadhar_no, '\s+', ' ', 'g'))) = 12) AND (next_level_role_id=0 OR next_level_role_id IS null OR next_level_role_id>0) AND (dup_aadhar=0 or dup_aadhar IS NULL) AND created_at::date >= '2023-09-01'::date AND ds_phase = 8
                    group by aadhar_no having count(1)>1 
                ) sub";
            } elseif ($search_for == 'bank_all') {
                $query = "SELECT 
                sum(cnt) AS de_dup, now()
                FROM
                (
                    SELECT trim(bank_code),trim(bank_ifsc),count(1) AS cnt FROM ".$table_name." WHERE (next_level_role_id=0 OR next_level_role_id is null OR next_level_role_id>0) 
                    AND (dup_bank=0 OR dup_bank is null)
                    GROUP BY trim(bank_code),trim(bank_ifsc) HAVING count(1)>1
                ) bank";
            } elseif ($search_for == 'bank_present_ds') {
                $query = "SELECT 
                sum(cnt) AS de_dup, now()
                FROM
                (
                    SELECT trim(bank_code),trim(bank_ifsc),count(1) AS cnt FROM ".$table_name." WHERE (next_level_role_id=0 OR next_level_role_id is null OR next_level_role_id>0) 
                    AND (dup_bank=0 OR dup_bank is null) AND created_at::date >= '2023-09-01'::date AND ds_phase = 8
                    GROUP BY trim(bank_code),trim(bank_ifsc) HAVING count(1)>1
                ) bank";
            }
            // echo $query;die;
            $result = DB::connection('pgsql')->select($query);
            $dupResult = [
                'dup_count' => $result[0]->de_dup,
                'date' => $result[0]->now,
                'search_type' => $search_for
            ];
            $insert = DB::connection('pgsql')->table('public.dup_check_point')->insert($dupResult);
            DB::connection('pgsql')->commit();
            $response = array(
                'status' => 1, 'msg' => 'Total Duplicate Found'.$result[0]->de_dup,
                'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
            );
        } catch (\Exception $e) {
            dd($e);
            DB::connection('pgsql')->rollback();
            $statusCode = 400;
            $return_text = 'Error. Please try again';
            $return_msg = array("" . $return_text);
            $response = array(
                'status' => $statusCode, 'msg' => $return_msg,
                'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
              );
        }finally{
            return response()->json($response, $statusCode);
        }
    }
}
