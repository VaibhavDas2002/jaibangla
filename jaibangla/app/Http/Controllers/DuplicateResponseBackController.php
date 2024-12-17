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
class DuplicateResponseBackController extends Controller
{
    public function dupResponseBankOAP(Request $request){
        try {
            $bank_code = $request->bank_code;
            $scheme_id = $request->scheme_id;
            $duplicate_oap_bank = DB::select("select id from oap_wcd.beneficiary where (next_level_role_id=0 or next_level_role_id>0 or next_level_role_id is null) and trim(bank_code)='" . $bank_code . "'");
            if($duplicate_oap_bank){
               return response()->json([ 'beneficiary_id'=>$duplicate_oap_bank[0]->id,"is_dup" => true], 200);
               
            }else{
               return response()->json(["is_dup" => false], 200);
            }
       } catch (\Exception $e) {
           // dd($e);
           return response()->json(["is_dup" => false], 500);
       }
    }
    public function dupResponseBankBandhu(Request $request){
        try {
            $bank_code = $request->bank_code;
            $scheme_id = $request->scheme_id;
            $duplicate_bandhu_bank = DB::select("select id from bandhu.beneficiary where (next_level_role_id=0 or next_level_role_id>0 or next_level_role_id is null) and trim(bank_code)='" . $bank_code . "'");
            if($duplicate_bandhu_bank){
               return response()->json([ 'beneficiary_id'=>$duplicate_bandhu_bank[0]->id,"is_dup" => true], 200);
               
            }else{
               return response()->json(["is_dup" => false], 200);
            }
       } catch (\Exception $e) {
           // dd($e);
           return response()->json(["is_dup" => false], 500);
       }
    }
    public function dupResponseBankJohar(Request $request){
        try {
            $bank_code = $request->bank_code;
            $scheme_id = $request->scheme_id;
            $duplicate_johar_bank = DB::select("select id from johar.beneficiary where (next_level_role_id=0 or next_level_role_id>0 or next_level_role_id is null) and trim(bank_code)='" . $bank_code . "'");
            if($duplicate_johar_bank){
               return response()->json([ 'beneficiary_id'=>$duplicate_johar_bank[0]->id,"is_dup" => true], 200);
               
            }else{
               return response()->json(["is_dup" => false], 200);
            }
       } catch (\Exception $e) {
           // dd($e);
           return response()->json(["is_dup" => false], 500);
       }
    }
    public function dupResponseAadharOAP(Request $request){
        try {
            $aadhar_no = $request->aadhar_no;
            $scheme_id = $request->scheme_id;
            $duplicate_oap_aadhar = DB::select("select id from oap_wcd.beneficiary where (next_level_role_id=0 or next_level_role_id>0 or next_level_role_id is null) and trim(aadhar_no)='" . $aadhar_no . "'");
            if($duplicate_oap_aadhar){
               return response()->json([ 'beneficiary_id'=>$duplicate_oap_aadhar[0]->id,"is_dup" => true], 200);
               
            }else{
               return response()->json(["is_dup" => false], 200);
            }
       } catch (\Exception $e) {
           // dd($e);
           return response()->json(["is_dup" => false], 500);
       }
    }
    public function dupResponseAadharBandhu(Request $request){
        try {
            $aadhar_no = $request->aadhar_no;
            $scheme_id = $request->scheme_id;
            $duplicate_bandhu_aadhar = DB::select("select id from bandhu.beneficiary where (next_level_role_id=0 or next_level_role_id>0 or next_level_role_id is null) and trim(aadhar_no)='" . $aadhar_no . "'");
            if($duplicate_bandhu_aadhar){
               return response()->json([ 'beneficiary_id'=>$duplicate_bandhu_aadhar[0]->id,"is_dup" => true], 200);
               
            }else{
               return response()->json(["is_dup" => false], 200);
            }
       } catch (\Exception $e) {
           // dd($e);
           return response()->json(["is_dup" => false], 500);
       }
    }
    public function dupResponseAadharJohar(Request $request){
        try {
            $aadhar_no = $request->aadhar_no;
            $scheme_id = $request->scheme_id;
            $duplicate_johar_aadhar = DB::select("select id from johar.beneficiary where (next_level_role_id=0 or next_level_role_id>0 or next_level_role_id is null) and trim(aadhar_no)='" . $aadhar_no . "'");
            if($duplicate_johar_aadhar){
               return response()->json([ 'beneficiary_id'=>$duplicate_johar_aadhar[0]->id,"is_dup" => true], 200);
               
            }else{
               return response()->json(["is_dup" => false], 200);
            }
       } catch (\Exception $e) {
           // dd($e);
           return response()->json(["is_dup" => false], 500);
       }
    }
}
