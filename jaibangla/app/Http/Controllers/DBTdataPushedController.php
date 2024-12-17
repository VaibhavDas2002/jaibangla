<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Configduty;
use App\District;
use App\UrbanBody;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
// use Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use DateTime;
use App\Scheme;
use Carbon\Carbon;
use App\DataSourceCommon;
use App\getModelFunc;
use App\DsPhase;
use Illuminate\Support\Facades\Auth;
use App\Traits\TraitDBTloginValidate;
class DBTdataPushedController extends Controller
{
    use TraitDBTloginValidate;
    public function __construct()
    {
        set_time_limit(0);
        date_default_timezone_set('Asia/Kolkata');
    }
    public function index()
    {
        $user_id = Auth::user()->id;
        $designation_id_old = Auth::user()->designation_id_old;
        if ($designation_id_old == 'Admin') {
            $schemes = Scheme::where('is_active', 1)->get();
            $monthVals = Config::get('constants.monthval');
            $finYears = Config::get('constants.academic_year');
            // dd($finYears);
            return view('data_transfer_to_dbt', ['schemes' => $schemes, 'monthVals' => $monthVals, 'finYears' => $finYears]);
        }else {
            return redirect("/")->with('success', 'User Disabled. ');
        }
    }
    // public function jbDataPushedToDbt(Request $request)
    // {
    //     $response = [];
    //     $statusCode = 200;
    //     // if (!$request->ajax()) {
    //     //     $statusCode = 400;
    //     //     $response = ['error' => 'Error occured in form submit.'];
    //     //     return response()->json($response, $statusCode);
    //     // }
    //     DB::connection('pgsql')->beginTransaction();
    //     try {
    //         $finYear = $request->fin_year;
    //         $month = $request->month;
    //         $scheme_id = $request->scheme_id;
    //         if ($month == 4) {
    //             // dd('April');
    //             $fun_call = DB::connection('pgsql')->select("SELECT dbt.initial_dbt_consolidated_data(".$scheme_id.",".$finYear.",".$month.");"
    //             );
    //             // $data = $this->authValidated();
    //             $dataPushed = $fun_call[0]->initial_dbt_consolidated_data;
    //             // dd($dataPushed);
    //         } else {
    //             // dd('154');
    //             $fun_call = DB::connection('pgsql')->select("SELECT dbt.incremental_dbt_consolidated_data(".$scheme_id.",".$finYear.",".$month.");"
    //             );
    //             // $data = $this->authValidated();
    //             $dataPushed = $fun_call[0]->incremental_dbt_consolidated_data;
    //         }
    //         // dd($dataPushed);
    //         if ($dataPushed == 1) {
    //             // dd()
    //             $send_data = $this->send_to_dbt($scheme_id, $finYear, $month);
    //             DB::connection('pgsql')->commit();
    //             $response = array(
    //                 'status' => 1, 'msg' => 'Data pushed successfully',
    //                 'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
    //             );
    //         } else {
    //             DB::connection('pgsql')->rollback();
    //             $response = array(
    //                 'status' => 0, 'msg' => 'Something went wrong.',
    //                 'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
    //             );
    //         }
    //     } catch (\Exception $e) {
    //         dd($e);
    //         DB::connection('pgsql')->rollback();
    //         $response = array(
    //             'exception' => true,
    //             'exception_message' => $e->getMessage(),
    //             // 'exception_message' => 'Somethimg went wrong. May be session timeout, please logout and login again.',
    //         );
    //         $statusCode = 400;
    //     } finally {
    //         return response()->json($response, $statusCode);
    //     }
    //     // dd($finYear);
    // }

    public function jbDataPushedToDbt(Request $request)
    {
        $response = [];
        $statusCode = 200;

        DB::connection('pgsql')->beginTransaction();
        DB::connection('pgsql_paywrite')->beginTransaction();
        try {
            $finYear = $request->fin_year;
            $month = $request->month;
            $scheme_id = $request->scheme_id;

            // Generate Financial Year
            if ($month >= 4 && $month <= 12) {
                $fin_year = $finYear.'-'.($finYear + 1);
            } else {
                $fin_year = ($finYear - 1).'-'.$finYear;
            }
            // Generate Academic Year
            if ($month == 1 || $month == 2 || $month == 3) {
                $acdemicYear = $finYear + 1;
            } else {
                $acdemicYear = $finYear;
            }
            // Financial Year Code
            if ($fin_year == '2023-2024') {
                $finYearCode = '107';
            }
            if ($fin_year == '2024-2025') {
                $finYearCode = '108';
            }
            if ($scheme_id == 2 || $scheme_id == 3 || $scheme_id == 8 || $scheme_id == 9 || $scheme_id == 10 || $scheme_id == 11 || $scheme_id == 13 || $scheme_id == 17) {
                $tableName = 'sbi.transaction_lot';
                $success = 'success_count';
                $failed = 'failed_count';
            } elseif ($scheme_id == 1 || $scheme_id == 5 || $scheme_id == 6 || $scheme_id == 7 || $scheme_id == 19) {
                $tableName = 'ifms.transaction_lot';
                $success = 'rbi_success_count';
                $failed = 'rbi_failed_count';
            }
            // dd($acdemicYear);
            $dbtSchemeCode = DB::connection('pgsql')->table('pds.master_scheme')->where('scheme_id', $scheme_id)->value('dbt_scheme_code');
            // dd($dbtSchemeCode);
            if ($month == 4) {
                if ($scheme_id == 8 || $scheme_id == 9) {
                    $query = "SELECT SUM(total_ben) AS total_ben, SUM(total_ben_digitalized) AS total_ben_digitalized, SUM(ben_aadhar_seeded) AS ben_aadhar_seeded, SUM(trans_aadhar_seeded) AS trans_aadhar_seeded, SUM(mobile_captured) AS mobile_captured, 'OK' AS remarks FROM
                    (
                    SELECT count(1) AS total_ben, count(1) AS total_ben_digitalized,
                    count(1) filter(where trim(aadhar_no)!='' and length(aadhar_no)=12) AS ben_aadhar_seeded,
                    count(1) filter(where trim(aadhar_no)!='' and length(aadhar_no)=12) AS trans_aadhar_seeded,
                    count(1) filter(where mobile_no IS NOT null and mobile_no>1000000000) mobile_captured
                     FROM pension.beneficiary WHERE next_level_role_id = 0 AND scheme_id = 8
                    UNION ALL
                    SELECT count(1) AS total_ben, count(1) AS total_ben_digitalized,
                    count(1) filter(where trim(aadhar_no)!='' and length(aadhar_no)=12) AS ben_aadhar_seeded,
                    count(1) filter(where trim(aadhar_no)!='' and length(aadhar_no)=12) AS trans_aadhar_seeded,
                    count(1) filter(where mobile_no IS NOT null and mobile_no>1000000000) mobile_captured
                     FROM pension.beneficiary WHERE next_level_role_id = 0 AND scheme_id = 9
                    ) t";
                    $benCount = DB::connection('pgsql')->select($query);
                } else {
                    $benCount = DB::connection('pgsql')->table('pension.beneficiary')->selectRaw("count(1) AS total_ben, count(1) AS total_ben_digitalized,
                    count(1) filter(where trim(aadhar_no)!='' and length(aadhar_no)=12) AS ben_aadhar_seeded,
                    count(1) filter(where trim(aadhar_no)!='' and length(aadhar_no)=12) AS trans_aadhar_seeded,
                    count(1) filter(where mobile_no IS NOT null and mobile_no>1000000000) mobile_captured,
                    'OK' AS remarks")->where('next_level_role_id', '=', 0)->where('scheme_id', $scheme_id)->first();
                }
               
            } else {
                if ($scheme_id == 8 || $scheme_id ==9) {
                    $query = "SELECT SUM(total_ben) AS total_ben, SUM(total_ben_digitalized) AS total_ben_digitalized, SUM(ben_aadhar_seeded) AS ben_aadhar_seeded, SUM(trans_aadhar_seeded) AS trans_aadhar_seeded, SUM(mobile_captured) AS mobile_captured, 'OK' AS remarks FROM
                    (
                    SELECT count(1) AS total_ben, count(1) AS total_ben_digitalized,
                    count(1) filter(where trim(aadhar_no)!='' and length(aadhar_no)=12) AS ben_aadhar_seeded,
                    count(1) filter(where trim(aadhar_no)!='' and length(aadhar_no)=12) AS trans_aadhar_seeded,
                    count(1) filter(where mobile_no IS NOT null and mobile_no>1000000000) mobile_captured
                     FROM pension.beneficiary WHERE next_level_role_id = 0 AND scheme_id = 8 AND extract(year from approval_date) = ".$acdemicYear." AND extract(month from approval_date) = ".$month."
                    UNION ALL
                    SELECT count(1) AS total_ben, count(1) AS total_ben_digitalized,
                    count(1) filter(where trim(aadhar_no)!='' and length(aadhar_no)=12) AS ben_aadhar_seeded,
                    count(1) filter(where trim(aadhar_no)!='' and length(aadhar_no)=12) AS trans_aadhar_seeded,
                    count(1) filter(where mobile_no IS NOT null and mobile_no>1000000000) mobile_captured
                     FROM pension.beneficiary WHERE next_level_role_id = 0 AND scheme_id = 9 AND extract(year from approval_date) = ".$acdemicYear." AND extract(month from approval_date) = ".$month."
                    ) t";
                    $benCount = DB::connection('pgsql')->select($query);
                    // dd($benCount[0]->total_ben);
                } else {
                    $benCount = DB::connection('pgsql')->table('pension.beneficiary')->selectRaw("count(1) AS total_ben, count(1) AS total_ben_digitalized,
                    count(1) filter(where trim(aadhar_no)!='' and length(aadhar_no)=12) AS ben_aadhar_seeded,
                    count(1) filter(where trim(aadhar_no)!='' and length(aadhar_no)=12) AS trans_aadhar_seeded,
                    count(1) filter(where mobile_no IS NOT null and mobile_no>1000000000) mobile_captured,
                    'OK' AS remarks")->whereRaw('extract(year from approval_date) = '.$acdemicYear.'')->whereRaw('extract(month from approval_date) = '.$month.'')->where('next_level_role_id', '=', 0)->where('scheme_id', $scheme_id)->first();
                }
            }
            if ($scheme_id == 8 || $scheme_id == 9) {
                $query = "SELECT SUM(fund_transfer_cash) AS fund_transfer_cash, 0 AS fund_cash_eletronics_apb, 0 AS no_trans_cash_electronic_apb, SUM(no_trans_cash_electronic) AS no_trans_cash_electronic, SUM(amnt_trans_cash_electronic) AS amnt_trans_cash_electronic, 0 AS no_trans_cash_other, 0 AS amnt_trans_cash_other FROM
                (
                SELECT SUM(success_count)*1000 AS fund_transfer_cash, 0 AS fund_cash_eletronics_apb, 0 AS no_trans_cash_electronic_apb, SUM(success_count) AS no_trans_cash_electronic, SUM(success_count)*1000 AS amnt_trans_cash_electronic, 0 AS no_trans_cash_other, 0 AS amnt_trans_cash_other FROM sbi.transaction_lot WHERE scheme_id = 8 AND lot_year = '".$fin_year."' AND success_count IS NOT null AND EXTRACT(MONTH FROM TO_DATE(lot_month, 'Month')) = ".$month."
                UNION ALL
                SELECT SUM(success_count)*1000 AS fund_transfer_cash, 0 AS fund_cash_eletronics_apb, 0 AS no_trans_cash_electronic_apb, SUM(success_count) AS no_trans_cash_electronic, SUM(success_count)*1000 AS amnt_trans_cash_electronic, 0 AS no_trans_cash_other, 0 AS amnt_trans_cash_other FROM sbi.transaction_lot WHERE scheme_id = 9 AND lot_year = '".$fin_year."' AND success_count IS NOT null AND EXTRACT(MONTH FROM TO_DATE(lot_month, 'Month')) = ".$month."
                ) t";
                $paymentRecord = DB::connection('pgsql_paywrite')->select($query);
                // dd($paymentRecord[0]->no_trans_cash_electronic);
            } else {
                $paymentRecord = DB::connection('pgsql_paywrite')->table($tableName)->selectRaw("SUM(".$success.")*1000 AS fund_transfer_cash, 0 AS fund_cash_eletronics_apb, 0 AS no_trans_cash_electronic_apb, SUM(".$success.") AS no_trans_cash_electronic, SUM(".$success.")*1000 AS amnt_trans_cash_electronic, 0 AS no_trans_cash_other, 0 AS amnt_trans_cash_other")->where('scheme_id', $scheme_id)->where('lot_year', $fin_year)->whereRaw($success." IS NOT null AND EXTRACT(MONTH FROM TO_DATE(lot_month, 'Month')) = ".$month."")->first();
                // dd($paymentRecord);
            }
            if ($scheme_id == 8 || $scheme_id == 9) {
                $query = "SELECT SUM(no_deduplicated) AS no_deduplicated, SUM(no_ghost) AS no_ghost, SUM(other_savings) AS other_savings, SUM(saving_amnt) AS saving_amnt FROM
                (SELECT COUNT(1) AS no_deduplicated, 0 AS no_ghost, 0 AS other_savings, COUNT(1)*1000 AS saving_amnt
                FROM pension.ben_payment_details_bank_code_dup WHERE scheme_id = 8 AND is_approved = 0 AND extract(year from m_date) = ".$acdemicYear." AND extract(month from m_date) = ".$month."
                UNION ALL
                SELECT COUNT(1) AS no_deduplicated, 0 AS no_ghost, 0 AS other_savings, COUNT(1)*1000 AS saving_amnt
                FROM pension.ben_payment_details_bank_code_dup WHERE scheme_id = 9 AND is_approved = 0 AND extract(year from m_date) = ".$acdemicYear." AND extract(month from m_date) = ".$month.") t";
                $duplicateBenCount = DB::connection('pgsql')->select($query);
                // dd($duplicateBenCount[0]->no_deduplicated);
            } else {
                $duplicateBenCount = DB::connection('pgsql')->table('pension.ben_payment_details_bank_code_dup')->selectRaw("COUNT(1) AS no_deduplicated, 0 AS no_ghost, 0 AS other_savings, COUNT(1)*1000 AS saving_amnt")->where('scheme_id', $scheme_id)->where('is_approved', '=', 0)->whereRaw('extract(year from m_date) = '.$acdemicYear.' AND extract(month from m_date) = '.$month.'')->first();
                // dd($duplicateBenCount);
            }

            $insertDbt = array();
            $sendToDbt = array();

            $insertDbt['EntryLevel'] = 0;
            $insertDbt['DistrictCode'] = 0;
            $insertDbt['SchemeCode'] = $dbtSchemeCode;
            $insertDbt['FinYrCode'] = $finYearCode;
            $insertDbt['FinancialYear'] = $fin_year;
            $insertDbt['ReportingMonth'] = $month;
            $insertDbt['TotalBenX'] = 0;
            $insertDbt['BenStateAdditional'] = 0;
            $insertDbt['CloseingCount'] = 0;
            $insertDbt['AdditionalBen'] = 0;
            $insertDbt['ActualEntry'] = 0;
            if ($scheme_id == 8 || $scheme_id ==9) {
                $insertDbt['TotalBen'] = $benCount[0]->total_ben;
            } else {
                $insertDbt['TotalBen'] = $benCount->total_ben;
            }
            $insertDbt['TotalBenWithBank'] = 0;
            if ($scheme_id == 8 || $scheme_id == 9) {
                $insertDbt['TotalBenDigitized'] = $benCount[0]->total_ben_digitalized;
                $insertDbt['BenAadharSeeded'] = $benCount[0]->ben_aadhar_seeded;
                $insertDbt['MobileCaptured'] = $benCount[0]->mobile_captured;
            } else {
                $insertDbt['TotalBenDigitized'] = $benCount->total_ben_digitalized;
                $insertDbt['BenAadharSeeded'] = $benCount->ben_aadhar_seeded;
                $insertDbt['MobileCaptured'] = $benCount->mobile_captured;
            }
            $insertDbt['NumberOfGroupSHG'] = 0;
            $insertDbt['BenefitType'] = 0;
            $insertDbt['BenefitType'] = 1;
            $insertDbt['FundCashCentre'] = 0;
            $insertDbt['FundCashState'] = 0;
            $insertDbt['FundCashStateAdditional'] = 0;
            $insertDbt['FundCashStateY'] = 0;
            if ($scheme_id == 8 || $scheme_id == 9) {
                $insertDbt['FundTrnsferCash'] = $paymentRecord[0]->fund_transfer_cash;
            } else {
                $insertDbt['FundTrnsferCash'] = $paymentRecord->fund_transfer_cash;
            }
            $insertDbt['FundCashElectronicCentre'] = 0;
            $insertDbt['FundCashElectronicState'] = 0;
            $insertDbt['FundCashElectronicAdditional'] = 0;
            $insertDbt['FundCashElectronicStateY'] = 0;
            if ($scheme_id == 8 || $scheme_id == 9) {
                $insertDbt['FundCashElectronicApb'] = $paymentRecord[0]->fund_cash_eletronics_apb;
            } else {
                $insertDbt['FundCashElectronicApb'] = $paymentRecord->fund_cash_eletronics_apb;
            }
            $insertDbt['FundCashElectronicNonApb'] = 0;
            $insertDbt['ExpenditureKindCentre'] = 0;
            $insertDbt['ExpenditureKindState'] = 0;
            $insertDbt['ExpenditureKindAdditional'] = 0;
            $insertDbt['ExpenditureKindStateY'] = 0;
            $insertDbt['ExpenditureKind'] = 0;
            $insertDbt['ExpenditureKindAuthenticatedCentre'] = 0;
            $insertDbt['ExpenditureKindAuthenticatedState'] = 0;
            $insertDbt['ExpenditureKindAuthenticatedAdditional'] = 0;
            $insertDbt['ExpenditureKindAuthenticatedStateY'] = 0;
            if ($scheme_id == 8 || $scheme_id == 9) {
                $insertDbt['NoTrnsCashElectronicApb'] = $paymentRecord[0]->no_trans_cash_electronic_apb;
            } else {
                $insertDbt['NoTrnsCashElectronicApb'] = $paymentRecord->no_trans_cash_electronic_apb;
            }
            $insertDbt['NoTrnsCashElectronicNonApb'] = 0;
            if ($scheme_id == 8 || $scheme_id == 9) {
                $insertDbt['NoTrnsCashElectronic'] = $paymentRecord[0]->no_trans_cash_electronic;
                $insertDbt['AmntTrnsCashElectronic'] = $paymentRecord[0]->amnt_trans_cash_electronic;
                $insertDbt['NoTrnsCashOther'] = $paymentRecord[0]->no_trans_cash_other;
                $insertDbt['AmntTrnsCashOther'] = $paymentRecord[0]->amnt_trans_cash_other;
                $insertDbt['TrnsAadharSeeded'] = $benCount[0]->trans_aadhar_seeded;
            } else {
                $insertDbt['NoTrnsCashElectronic'] = $paymentRecord->no_trans_cash_electronic;
                $insertDbt['AmntTrnsCashElectronic'] = $paymentRecord->amnt_trans_cash_electronic;
                $insertDbt['NoTrnsCashOther'] = $paymentRecord->no_trans_cash_other;
                $insertDbt['AmntTrnsCashOther'] = $paymentRecord->amnt_trans_cash_other;
                $insertDbt['TrnsAadharSeeded'] = $benCount->trans_aadhar_seeded;
            }
            $insertDbt['UnitKind'] = 0;
            $insertDbt['QtyTransferedKind'] = 0;
            $insertDbt['AadharTransKind'] = 0;
            if ($scheme_id == 8 || $scheme_id == 9) {
                $insertDbt['NoDeDuplicated'] = $duplicateBenCount[0]->no_deduplicated;
                $insertDbt['NoGhost'] = $duplicateBenCount[0]->no_ghost;
                $insertDbt['OtherSavings'] = $duplicateBenCount[0]->other_savings;
                $insertDbt['SavingAmnt'] = $duplicateBenCount[0]->saving_amnt;
                $insertDbt['Remarks'] = $benCount[0]->remarks;
            } else {
                $insertDbt['NoDeDuplicated'] = $duplicateBenCount->no_deduplicated;
                $insertDbt['NoGhost'] = $duplicateBenCount->no_ghost;
                $insertDbt['OtherSavings'] = $duplicateBenCount->other_savings;
                $insertDbt['SavingAmnt'] = $duplicateBenCount->saving_amnt;
                $insertDbt['Remarks'] = $benCount->remarks;
            }
            $insertDbt['CreatedOn'] = DB::raw("now()");
            $insertDbt['ModifiedOn'] = DB::raw("now()");
            $insertDbt['Status'] = 0;
            $insertDbt['StateProcessOn'] = DB::raw("now()");
            $insertDbt['DeptCode'] = 0;
            $insertDbt['submition_flag'] = 0;
            if ($scheme_id == 8 || $scheme_id == 9) {
                $insertDbt['totalBenIncremental'] = $benCount[0]->total_ben;
                $insertDbt['benDigitizedIncremental'] = $benCount[0]->total_ben_digitalized;
                $insertDbt['benAadharSeededIncremental'] = $benCount[0]->ben_aadhar_seeded;
                $insertDbt['mobileCapturedIncremental'] = $benCount[0]->mobile_captured;
            } else {
                $insertDbt['totalBenIncremental'] = $benCount->total_ben;
                $insertDbt['benDigitizedIncremental'] = $benCount->total_ben_digitalized;
                $insertDbt['benAadharSeededIncremental'] = $benCount->ben_aadhar_seeded;
                $insertDbt['mobileCapturedIncremental'] = $benCount->mobile_captured;
            }
            $insertDbt['benWithBankIncremental'] = 0;
            $insertDbt['is_sent_dbt'] = 1;

            $sendToDbt['scheme_code'] = $dbtSchemeCode;
            $sendToDbt['fin_year'] = $fin_year;
            $sendToDbt['month'] = $month;
            $sendToDbt['send_to_dbt_at'] = DB::raw("now()");

            $is_insert = DB::connection('pgsql')->table('dbt.dbtconsolidatedata')->insert($insertDbt);
            $is_insert_log = DB::connection('pgsql')->table('dbt.monthwise_data_send_to_dbt')->insert($sendToDbt);

            if ($is_insert && $is_insert_log) {
                $send_data = $this->send_to_dbt($scheme_id, $finYear, $month);
                DB::connection('pgsql')->commit();
                DB::connection('pgsql_paywrite')->commit();
                $response = array(
                    'status' => 1, 'msg' => 'Data pushed successfully',
                    'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                );
            } else {
                DB::connection('pgsql')->rollback();
                DB::connection('pgsql_paywrite')->rollback();
                $response = array(
                    'status' => 1, 'msg' => 'Something Went Wrong',
                    'type' => 'red', 'icon' => 'fa fa-check', 'title' => 'Error'
                );
            }
        } catch (\Exception $e) {
            dd($e);
            DB::connection('pgsql')->rollback();
            DB::connection('pgsql_paywrite')->rollback();
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
                // 'exception_message' => 'Somethimg went wrong. May be session timeout, please logout and login again.',
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }
}
