<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\District;
use App\getModelFunc;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Helpers\Helper;
use App\Helpers\LotGeneration;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\AuthChecker;



class LotReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        set_time_limit(180);
        date_default_timezone_set('Asia/Kolkata');
    }

    public function getLotCreateEnabledMonthList(Request $request) {
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in form submit.');
            return response()->json($response, $statusCode);
        }
        try {
            $scheme_id = $request->select_scheme;
            $fin_year = $request ->lot_year;
            $monthLists = LotGeneration::getEnabledMonthForLotCreation($fin_year, $scheme_id);
            $MonthData = '';
            $MonthData .= '<option value="" >--Select Month--</option>';
            foreach($monthLists as $index)
            {
                $MonthData .= '<option value="' . $index->month_name . '">' . strtoupper($index->month_name) . '</option>';
            }
            $response = array(
                'monthData' => $MonthData
            );
        } catch (\Exception $e) {
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    // ###############  Function for validation lot report ###########################
    public function index_validation_lot(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme = DB::select(DB::raw("select id,scheme_name from public.m_scheme where id in (select scheme_id from public.duty_assignement where user_id=" . $user_id . " and is_active=1)"));
        if (count($scheme) > 0) {
            return view('lot_report/index_report_validation_lot', [
                'schemes' => $scheme,
            ]);
        } else {
            return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
        }
    }

    public function reportValidationLot(Request $request)
    {
        if ($request->ajax()) {
            $scheme_id = $request->selectScheme;
            $query = "Select d.district_name, d.district_code, 
            coalesce(sum(total_beneficiary),0) 				as total_beneficiary,
            coalesce(sum(validation_initiated),0)			as validation_initiated,
            coalesce(sum(validation_not_inititated),0)		as validation_not_inititated,
            coalesce(sum(validation_complete),0)			as validation_complete,
            coalesce(sum(validation_success),0)				as validation_success,
            coalesce(sum(acc_validation_failed),0) 			as acc_validation_failed,
            coalesce(sum(name_validation_failed),0) 		as name_validation_failed,
            coalesce(sum(acc_validation_failed+name_validation_failed),0) 		as validation_failed,
            coalesce(sum(total_beneficiary-(validation_success+acc_validation_failed+name_validation_failed)),0) as validation_pending
            
            from public.m_district d left join
            (
                select dist_code,
                count(1) 												as total_beneficiary,
                sum(case when acc_validated!='0' then 1 else 0 end ) 	as validation_initiated,
                sum(case when (acc_validated='0' and ben_status=1 and is_eligible=true) then 1 else 0 end )	as validation_not_inititated,
                sum(case when acc_validated in('2','3','4') then 1 else 0 end ) as validation_complete,
                sum(case when acc_validated='2' then 1 else 0 end)          as validation_success,
                sum(case when acc_validated='3' then 1 else 0 end)      as acc_validation_failed,
                sum(case when acc_validated='4' then 1 else 0 end)      as name_validation_failed
                FROM payment.ben_payment_details ";

            if (!empty($scheme_id)) {
                $query .= " where scheme_id =" . $scheme_id;
            }
            $query .= " group by dist_code	
            )b  on d.district_code = b.dist_code";

            $query .= " group by district_name,district_code order by district_name,district_code";

            //echo $query;die;
            $data = DB::connection('pgsql_paywrite')->select($query);

            return datatables()->of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    // ###############  Function for payment lot report ###########################
    public function index_payment_lot(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme = DB::select(DB::raw("select id,scheme_name from public.m_scheme where id in (select scheme_id from public.duty_assignement where user_id=" . $user_id . " and is_active=1)"));
        if (count($scheme) > 0) {
            return view('lot_report/index_report_payment_lot', [
                'schemes' => $scheme,
            ]);
        } else {
            return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
        }
    }

    public function reportPaymentLot(Request $request)
    {
        if ($request->ajax()) {
            $scheme_id = $request->selectScheme;
            $lot_year  = $request->lot_year;
            $month = $request->lot_month;

            $lot_month = array_search($month, Config::get('constants.monthval'));
            $getMonthColumn = Helper::getMonthColumn($lot_month);
            // New 30-09-2021
            $yearArr = explode('-', $lot_year);
            if ($lot_month >= 4 and  $lot_month <= 12) {
                $final_yymm = substr($yearArr[0], 2) . str_pad($lot_month, 2, "0", STR_PAD_LEFT);
            } else {
                $final_yymm = substr($yearArr[1], 2) . str_pad($lot_month, 2, "0", STR_PAD_LEFT);
            }

            // dump($getMonthColumn, $final_yymm);
            // die;
            $dupBankCondition = '';
            if ($scheme_id == 1 || $scheme_id == 19) {
                $dupBankCondition = '';
            } else {
                $dupBankCondition = ' and dup_bank=0';
            }

            // After new account number given without account validation payment will not happen
            $accountValidationCondition = '';
            if ($scheme_id == 10 || $scheme_id == 11 || $scheme_id == 2) {
                $accountValidationCondition = ' and ((acc_validated=2 and legacy_validation=0) or legacy_validation=1) ';
            } else {
                $accountValidationCondition = '';
            }

            $query = " Select district_name as district_name,
            district_code                   as district_code,
            sum(total_beneficiary)          as total_beneficiary,
            sum(lot_generated)              as lot_generated,
            sum(lot_not_generated)          as lot_not_generated,
            sum(push_to_bank)        		as push_to_bank,
            sum(push_to_bank_amount)        as push_to_bank_amount,
            sum(response_received)          as response_received,
            sum(payment_success)            as payment_success,
            sum(payment_failure)            as payment_failure,
            sum(ifms_returned)              as ifms_returned
            ,SUM(bank_edited)               AS bank_edited
            ,SUM(amount_disbursed)          AS amount_disbursed
            ,SUM(deactivate_ben)            AS deactivate_ben
            
            from public.m_district d left join
            (
                select bp.dist_code,
                count(1) as total_beneficiary,
                sum(case when " . $getMonthColumn['lot_status'] . " <> 'R'   then 1 else 0 end )  as lot_generated,
                sum(case when " . $getMonthColumn['lot_status'] . " = any(array['R','E']) and ben_status=1 and is_eligible=true and pay_validated=1 and is_rejected=0 ".$dupBankCondition." and start_yymm<= " . $final_yymm . " and " . $getMonthColumn['lot_eligible'] . " = true ". $accountValidationCondition ." then 1 else 0 end )  as lot_not_generated,
                sum(case when " . $getMonthColumn['lot_status'] . " != all(array['R','G','H'])  then 1 else 0 end )   as push_to_bank,
                sum(case when " . $getMonthColumn['lot_status'] . " not in('R','G','H')  then " . $getMonthColumn['lot_eligible_amount'] . " else 0 end )   as push_to_bank_amount,
                sum(case when " . $getMonthColumn['lot_status'] . " = any(array['S','F','M','E','H']) then 1 else 0 end)  as response_received,
                sum(case when " . $getMonthColumn['lot_status'] . "='S' then 1 else 0 end)        as payment_success,
                sum(case when " . $getMonthColumn['lot_status'] . "='F' then 1 else 0 end)        as payment_failure,
                sum(case when " . $getMonthColumn['lot_status'] . "='M' then 1 else 0 end)        as ifms_returned,
                sum(case when " . $getMonthColumn['lot_status'] . "='E' then 1 else 0 end)        as bank_edited,
                sum(case when " . $getMonthColumn['lot_payment_amount'] . " <> 0 then " . $getMonthColumn['lot_payment_amount'] . " else 0 end)        as amount_disbursed,
                SUM(case WHEN is_eligible=false and is_rejected>0 THEN 1 else 0 end) AS deactivate_ben
                
                FROM payment.ben_payment_details bp
                left join payment.ben_transaction_details bt on bp.ben_id=bt.ben_id and bp.scheme_id=bt.scheme_id  
                where bp.scheme_id=" . $scheme_id . " and bt.fin_year= '" . $lot_year . "' group by bp.dist_code  
            )b  on d.district_code = b.dist_code group by district_name, district_code
            order by district_name, district_code";

            // return $query;

            $data = DB::connection('pgsql_paywrite')->select($query);

            return datatables()->of($data)
                // ->addIndexColumn()
                ->make(true);
        } 
    }

    public function index_pending_payment_lot(Request $request) {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme = DB::select(DB::raw("select id,scheme_name from public.m_scheme where id in (select scheme_id from public.duty_assignement where user_id=" . $user_id . " and is_active=1)"));
        if (count($scheme) > 0) {
            return view('lot_report/index_payment_lot_create_pending', [
                'schemes' => $scheme,
            ]);
        } else {
            return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
        }
    }

    function getFinancialYearMonths() {
        $months = array();
        // Start from April (month 4) and end in March (month 3) of the following year
        for ($month = 4; $month <= 15; $month++) {
            $month_number = $month % 12; // Convert to a valid month number (1 to 12)
            if ($month_number === 0) {
                $month_number = 12; // December
            }
            $month_name = date('F', mktime(0, 0, 0, $month_number, 1));
            $months[] = $month_name;
        }
        return $months;
    }

    public function reportPendingPaymentLotpost(Request $request) {
        if ($request->ajax()) {
            $scheme_id = $request->selectScheme;
            $lot_year  = $request->lot_year;
            $query = "SELECT payment.beneficiary_ready_for_lot(" .$scheme_id. ", '" .$lot_year. "')";
            $data = DB::connection('pgsql_paywrite')->select($query);
            $result_set = $data[0]->beneficiary_ready_for_lot;
            // dump($result_set);
            // dump(json_decode($result_set, true));
            $result_set = json_decode($result_set, true);
            // Get all months in the financial year
            $financial_year_months = $this->getFinancialYearMonths();
            // dump($financial_year_months);
            // Print the months
            $result_set_array = array();
            foreach ($financial_year_months as $month) {
                $pending_ben_month = strtolower(substr($month, 0, 3)).'_ben';
                $pending_amount_month = strtolower(substr($month, 0, 3)).'_amount';
                if (isset($result_set[0][$pending_ben_month])) {
                    $result_set_array[] = [
                        'month_name' => $month,
                        'pending_ben' => $result_set[0][$pending_ben_month],
                        'pending_amount' => $result_set[0][$pending_amount_month]
                    ];
                }
            }
            // dd($result_set_array);
            return datatables()->of($result_set_array)
                ->addIndexColumn()
                ->make(true);
        } 
    }

    public function index_pending_lot_report(Request $request) {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme = DB::select(DB::raw("select id,scheme_name from public.m_scheme where id in (select scheme_id from public.duty_assignement where user_id=" . $user_id . " and is_active=1)"));
        if (count($scheme) > 0) {
            if ($designation_id_old == 'DDO' || $designation_id_old == 'Corp' || $designation_id_old == 'Admin') {
                return view('lot_report/index_pending_lot_report', [
                    'schemes' => $scheme,
                ]);
            } else {
                return redirect("/")->with('success', 'User disabled.');
            }           
        } else {
            return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
        }
    }

    public function pendingLotReport(Request $request) {
        if ($request->ajax()) 
        {
            $selectScheme = $request->selectScheme;
            $payment_mode = $request->payment_mode;
            // dd($selectScheme);
            if ($payment_mode == 1) {
                $query = "SELECT lot_year, lot_month, COUNT(1) AS lot_count, lot_status
                FROM ifms.transaction_lot WHERE lot_status = ANY(ARRAY[0,1,2,4]) AND scheme_id = ".$selectScheme." GROUP BY lot_year, lot_month, lot_status ORDER BY lot_status";
            }
            if ($payment_mode == 2) {
                $query = "SELECT lot_year, lot_month, COUNT(1) AS lot_count, lot_status
                FROM sbi.transaction_lot WHERE lot_status = ANY(ARRAY[0,1,2,3]) AND scheme_id = ".$selectScheme." GROUP BY lot_year, lot_month, lot_status ORDER BY lot_status";
            }
            $data = DB::connection('pgsql_paywrite')->select($query);

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('lot_status', function($data) use($payment_mode){
                    $btn = '';
                    if ($payment_mode == 2) {
                        if($data->lot_status == 0) {
                            $btn = '<span class="text-primary"><b>Lot signing</b></span>';
                        }
                        if($data->lot_status == 1) {
                            $btn = '<span class="text-success"><b>Pushed To SBI</b></span>';
                        }
                        if($data->lot_status == 2) {
                            $btn = '<span class="text-warning"><b>Acknowledgement Receive</b></span>';
                        }
                        if($data->lot_status == 3) {
                            $btn = '<span class="text-danger"><b>Import SBI Report</b></span>';
                        }
                    } else {
                        if($data->lot_status == 0) {
                            $btn = '<span class="text-primary"><b>Push To IFMS</b></span>';
                        }
                        if($data->lot_status == 1) {
                            $btn = '<span class="text-success"><b>IFMS Received</b></span>';
                        }
                        if($data->lot_status == 2) {
                            $btn = '<span class="text-warning"><b>Submitted To Treasury</b></span>';
                        }
                        if($data->lot_status == 4) {
                            $btn = '<span class="text-danger"><b>Import RBI Report</b></span>';
                        }
                    }                   
                    return $btn;
                })
                ->rawColumns(['lot_status'])
                ->make(true);
        }
    }

    public function index_monthly_disbursement(Request $request) {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme = DB::select(DB::raw("select id,scheme_name from public.m_scheme where id in (select scheme_id from public.duty_assignement where user_id=" . $user_id . " and is_active=1)"));
        if (count($scheme) > 0) {
            if ($designation_id_old == 'DDO' || $designation_id_old == 'Corp' || $designation_id_old == 'Admin') {
                return view('lot_report/index_monthly_disbursement', [
                    'schemes' => $scheme,
                ]);
            } else {
                return redirect("/")->with('success', 'User disabled.');
            }           
        } else {
            return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
        }
    }

    public function monthlyDisbursementReport(Request $request) {
        if ($request->ajax()) {
            $selectScheme = $request->selectScheme;
            $payment_mode = $request->payment_mode;
            $fin_year = $request->fin_year;
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            if ($payment_mode == 1) {
                $ben_count = "tl.ben_count";
                $tableName = "ifms.transaction_lot";
                $success_count = "tl.rbi_success_count";
                $failed_count = "tl.rbi_failed_count";
                $lot_no = "tl.lot_no";
                $debit_amount = "debit_amount";
                $amount_debit = "amount_debit";
            } else {
                $ben_count = "credit_count";
                $tableName = "sbi.transaction_lot";
                $success_count = "tl.success_count";
                $failed_count = "tl.failed_count";
                $lot_no = "tl.lot_no";
                $debit_amount = "debit_amount/100";
                $amount_debit = "amount_debit/100";
            }
            $query = "
            SELECT COALESCE(TO_CHAR(t.pushed_at, 'DD-MM-YYYY'), 'Lot not pushed yet') as pushed_date, 
            TRIM(TO_CHAR(TO_DATE(TO_CHAR(t.pushed_at, 'MM')::text, 'MM'), 'Month')) || '-' || LEFT(t.pushed_at::varchar,4) as month_year, 
            SUM(ben_count) AS ben_count,
            SUM(".$amount_debit.")::int AS debit_amount, 
            SUM(success_count) AS success_count, 
            SUM(".$debit_amount.") AS success_amount, 
            SUM(failed_count) AS failed_count, 
            SUM(failed_count*1000) as failed_amount, 
            (SUM(ben_count)-(SUM(success_count)+SUM(failed_count))) as pending_ben_count 
            FROM (  
            SELECT pushed_at::date,--lot_category,
            SUM(".$ben_count.") AS ben_count,
            SUM(debit_amount) AS debit_amount, 
            SUM(".$success_count.") AS success_count, 
            SUM(amount_debit) AS amount_debit, 
            SUM(".$failed_count.") AS failed_count, 
            COUNT(1) AS total_lot
            FROM payment.lot_master l JOIN ".$tableName." tl ON l.lot_no = ".$lot_no."
            WHERE tl.lot_year = '".$fin_year."' AND tl.scheme_id = ".$selectScheme." AND tl.lot_status >= 0";
            if(!empty($from_date)){
                $query .= " and pushed_at::date >='".date('Y-m-d', strtotime(trim(str_replace('/', '-', $from_date))))."'::date";
            }
            if(!empty($to_date)){
                $query .= " and pushed_at::date <='".date('Y-m-d', strtotime(trim(str_replace('/', '-', $to_date))))."'::date";
            }
            $query .= " GROUP BY pushed_at::date ORDER BY pushed_at::date DESC
            ) t GROUP BY pushed_at ORDER BY pushed_at::date DESC";
            // print $query;die;
            $data = DB::connection('pgsql_paywrite')->select($query);
            // print_r($data);die();
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('pushed_date', function ($data) {
                    return $data->pushed_date;
                })
                ->rawColumns([ 'pushed_date'])
                ->make(true);
        }

    }

    public function index_date_wise_lot_report(Request $request) {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $scheme = DB::select(DB::raw("select id,scheme_name from public.m_scheme where id in (select scheme_id from public.duty_assignement where user_id=" . $user_id . " and is_active=1)"));
        if (count($scheme) > 0) {
            if ($designation_id_old == 'DDO' || $designation_id_old == 'Corp' || $designation_id_old == 'Admin') {
                return view('lot_report/index_date_wise_lot_report', [
                    'schemes' => $scheme,
                ]);
            } else {
                return redirect("/")->with('success', 'User disabled.');
            }           
        } else {
            return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
        }
    }

    public function datewiseLotReport(Request $request) {
        if ($request->ajax()) {
            $selectScheme = $request->selectScheme;
            $payment_mode = $request->payment_mode;
            $fin_year = $request->fin_year;
            $from_date = $request->from_date;
            $to_date = $request->to_date;

            if ($payment_mode == 1) {
                $lot_no = "tl.lot_no";
                $ben_count = "tl.ben_count";
                $success_count = "tl.rbi_success_count";
                $tableName = "ifms.transaction_lot";
                $debit_amount = "debit_amount";
                $amount_debit = "amount_debit";
            } else {
                $lot_no = "tl.lot_no";
                $ben_count = "tl.credit_count";
                $success_count = "tl.success_count";
                $tableName = "sbi.transaction_lot";
                $debit_amount = "debit_amount/100";
                $amount_debit = "amount_debit/100";
            }

            $query = "SELECT 
                tl.created_at::date,
                to_char( tl.created_at::date, 'DD/MM/YYYY') as creation_date, 
                COUNT(1) as total_lot,
                SUM(".$debit_amount.")::int as total_debit_amount,
                SUM(".$ben_count.") as total_bencount, 
                SUM(".$success_count.") as total_success,
                SUM(".$amount_debit.") as total_amount_debit 
            FROM payment.lot_master l JOIN ".$tableName." tl ON l.lot_no = ".$lot_no."
            WHERE tl.scheme_id = ".$selectScheme." AND tl.lot_year = '".$fin_year."'";
            if(!empty($from_date)){
                $query .= " and tl.created_at::date >='".date('Y-m-d', strtotime(trim(str_replace('/', '-', $from_date))))."'::date";
            }
            if(!empty($to_date)){
                $query .= " and tl.created_at::date <='".date('Y-m-d', strtotime(trim(str_replace('/', '-', $to_date))))."'::date";
            }
            $query .=" GROUP BY to_char(tl.created_at::date, 'DD/MM/YYYY') , tl.created_at::date ORDER BY  tl.created_at::date DESC";
            // print $query;die;
            $data = DB::connection('pgsql_paywrite')->select($query);
            // print_r($data);die();
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('creation_date', function ($data) {
                    return $data->creation_date;
                })
                ->addColumn('total_lot', function ($data) {
                    return $data->total_lot;
                })
                ->addColumn('total_bencount', function ($data) {
                    return $data->total_bencount;
                })
                ->addColumn('total_amount_debit', function ($data) {
                    return $data->total_amount_debit;
                })
                ->rawColumns([ 'creation_date', 'total_lot', 'total_bencount', 'total_amount_debit'])
                ->make(true);
        }
    }
}
