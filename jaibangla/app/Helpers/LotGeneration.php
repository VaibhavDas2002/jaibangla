<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Scheme;
use App\LotGenerationFunctionMaster;

class LotGeneration
{
    

    public static function getEnabledMonthForLotCreation($fin_year, $scheme_id) {
        try {
            $results = DB::connection('pgsql_paywrite')->table('payment_master.m_lot_create_month')
            ->select('month', 'month_name')
            ->where('is_active', 1)
            ->where('fin_year', $fin_year)
            ->whereRaw($scheme_id . ' = ANY (scheme_id_array)')
            ->orderByRaw('CASE month 
                WHEN 4 THEN 0 
                WHEN 5 THEN 1 
                WHEN 6 THEN 2 
                WHEN 7 THEN 3 
                WHEN 8 THEN 4 
                WHEN 9 THEN 5 
                WHEN 10 THEN 6 
                WHEN 11 THEN 7 
                WHEN 12 THEN 8 
                WHEN 1 THEN 9 
                WHEN 2 THEN 10 
                WHEN 3 THEN 11 
                ELSE 12 END')
            ->get();
            return $results;
        } catch (\Exception $e) {
            $response = array(
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' => 'Something went wrong when fetching months.',
            );
        }
    }
}
