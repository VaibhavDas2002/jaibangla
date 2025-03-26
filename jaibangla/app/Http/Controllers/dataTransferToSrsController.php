<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\District;
use App\Scheme;
use DateTime;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class dataTransferToSrsController extends Controller
{
    public function sendBenData()
    {
        //
    }

    public function sendBenTransData()
    {
        $time_array = DB::select(DB::raw("select to_char(now(),'MONYYYY') as datetime"));
        $var_file_name = $time_array[0]->datetime;
        $log_file_name = 'srs_scheduler_log/log_approved_beneficiary_transaction_send_to_srs_' . $var_file_name . '.txt';
        $fin_year = '2024-2025';
        $monthVals = array ('01' => 'April', '02' => 'May', '03' => 'June', '04' => 'July', '05' => 'August', '06' => 'September', '07' => 'October', '08' => 'November', '09' => 'December', '10' => 'January', '11' => 'February', '12' => 'March');
        $schemesArr = array(0=>1, 1=>2, 2=>3, 3=>5, 4=>6, 5=>7, 6=>8, 7=>9, 8=>10, 9=>11, 10=>13, 11=>17, 12=>19);
        $monthsArr = array(0=>4, 1=>5, 2=>6, 3=>7, 4=>8, 5=>9, 6=>10, 7=>11, 8=>12, 9=>1, 10=>2, 11=>3);
        $currentMonthIndex = array_search(date('n'), $monthsArr);
        $currentDate = date("Y-m-d");
        $currentMonth = date("n", strtotime($currentDate));
        // dd($currentMonthIndex);
        $iteration = 2;
        for ($i=1; $i <= $iteration; $i++) 
        { 
            Storage::append($log_file_name, 'Function send_approved_beneficiary_transaction_data_to_srs() has started on ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
            Storage::append($log_file_name, 'Loop '.$i.' has been started.' );
            Storage::append($log_file_name, '=====================================================');
            foreach ($monthsArr as $monthVal => $monthName)
            {
                $timestamp = mktime(0, 0, 0, $monthName, 1); // Create a timestamp
                $mName = date('F', $timestamp);
                // dd($mName);
                if ($monthVal <= $currentMonthIndex) 
                {
                    foreach ($schemesArr as $schemeArr => $schemeId) 
                    {
                        echo $mName." has been started for Scheme: ".$schemeId."!! " . ' Date : ' . date("l jS \of F Y h:i:s A")."\n";
                        Storage::append($log_file_name, $mName.' has been started for Scheme: '.$schemeId.'!! time:- ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
                        Storage::append($log_file_name, '-----------------------------------------------------');

                        $fun_call = "";
                        $query = "SELECT social_registry.send_approved_beneficiary_transaction_data_to_srs(in_scheme_id => ".$schemeId.",in_ld_lot_month => '".$mName."', in_fin_year => '".$fin_year."')";
                        $fun_call = DB::connection('pgsql_appwrite')->select($query);
                        $result = $fun_call[0]->send_approved_beneficiary_transaction_data_to_srs;
                        // echo $query; $result =1;
                        Storage::append($log_file_name, $mName.' DB Result : '.$result.' for Scheme: '.$schemeId.' ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
                        Storage::append($log_file_name, $mName.' has been ended for Scheme: '.$schemeId.'!! ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
                        Storage::append($log_file_name, '-----------------------------------------------------');
                    }
                }               
            }
        }
        Storage::append($log_file_name, 'Function send_approved_beneficiary_transaction_data_to_srs() has ended on ' . ' Date : ' . date("l jS \of F Y h:i:s A") );
        Storage::append($log_file_name, 'Loop '.$i.' has been ended for Scheme: '.$schemeId.'.' );
        Storage::append($log_file_name, '*****=====================================================*****');
    }
}
