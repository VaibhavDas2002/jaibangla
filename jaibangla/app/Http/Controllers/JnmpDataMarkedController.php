<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DupliacteApproveReject;
use App\Scheme;
use App\District;
use App\UrbanBody;
use App\GP;
use App\BeneficiaryPensions;
use App\PensionSc;
use App\PensionSt;
use App\Manabik;
use App\UpdateBenDetails;
use App\Configduty;
use App\DocumentType;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class JnmpDataMarkedController extends Controller
{
    public function JnmpMarkedData()
    {
        $time_array = DB::select(DB::raw("select to_char(now(),'MONYYYY') as datetime"));
        $var_file_name = $time_array[0]->datetime;
        $log_file_name = 'jnmp_scheduler_log/log_mark_jnmp_data_beneficiary_' . $var_file_name . '.txt';
        $schemesArr = array(0=>1, 1=>2, 2=>3, 3=>5, 4=>6, 5=>7, 6=>8, 7=>9, 8=>10, 9=>11, 10=>13, 11=>17, 12=>19);
        $iteration = 2;
        Storage::append($log_file_name, 'marking_jnmp_data_to_beneficiary_master() has been started !! time:- ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
        for ($i=0; $i < $iteration; $i++) 
        { 
            foreach ($schemesArr as $schemeKey => $schemeId) 
            {
                // Storage::append($log_file_name, 'marking_jnmp_data_to_beneficiary_master() has been started !! time:- ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
                Storage::append($log_file_name, 'Loop '.$i.' has been started for Scheme: '.$schemeId.'!! time:- ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
                Storage::append($log_file_name, '-----------------------------------------------------');

                $fun_call = "";
                $query = "SELECT jnmp.marking_jnmp_data_to_beneficiary_master(in_scheme_id => ".$schemeId.")";
                $fun_call = DB::connection('pgsql_appwrite')->select($query);
                $result = $fun_call[0]->marking_jnmp_data_to_beneficiary_master;
                // $result = 1;

                Storage::append($log_file_name, ' DB Result : '.$result.' for Scheme: '.$schemeId.' ' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                Storage::append($log_file_name, 'Loop '.$i.' has been ended for Scheme: '.$schemeId.'!! ' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                Storage::append($log_file_name, '-----------------------------------------------------');
                // Storage::append($log_file_name, 'marking_jnmp_data_to_beneficiary_master() has been ended !! time:- ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
            }
        }
        Storage::append($log_file_name, 'marking_jnmp_data_to_beneficiary_master() has been ended !! time:- ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
    }

    public function PaymentSuspended()
    {
        $time_array = DB::select(DB::raw("select to_char(now(),'MONYYYY') as datetime"));
        $var_file_name = $time_array[0]->datetime;
        $log_file_name = 'jnmp_scheduler_log/log_mark_jnmp_data_beneficiary_' . $var_file_name . '.txt';
        $schemesArr = array(0=>1, 1=>2, 2=>3, 3=>5, 4=>6, 5=>7, 6=>8, 7=>9, 8=>10, 9=>11, 10=>13, 11=>17, 12=>19);
        $iteration = 1;
        Storage::append($log_file_name, 'suspended_payment_due_to_jnmp() has been started !! time:- ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
        for ($i=0; $i < $iteration; $i++) 
        { 
            foreach ($schemesArr as $schemeKey => $schemeId) 
            {
                // Storage::append($log_file_name, 'suspended_payment_due_to_jnmp() has been started !! time:- ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
                Storage::append($log_file_name, 'Loop '.$i.' has been started for Scheme: '.$schemeId.'!! time:- ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
                Storage::append($log_file_name, '-----------------------------------------------------');

                $fun_call = "";
                $query = "SELECT jnmp.suspended_payment_due_to_jnmp(in_scheme_id => ".$schemeId.")";
                $fun_call = DB::connection('pgsql_paywrite')->select($query);
                $result = $fun_call[0]->suspended_payment_due_to_jnmp;
                // $result = 1;

                Storage::append($log_file_name, ' DB Result : '.$result.' for Scheme: '.$schemeId.' ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
                Storage::append($log_file_name, 'Loop '.$i.' has been ended for Scheme: '.$schemeId.'!! ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
                Storage::append($log_file_name, '-----------------------------------------------------');
                // Storage::append($log_file_name, 'suspended_payment_due_to_jnmp() has been ended !! time:- ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
            }
        }
        Storage::append($log_file_name, 'suspended_payment_due_to_jnmp() has been ended !! time:- ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
    }
}
