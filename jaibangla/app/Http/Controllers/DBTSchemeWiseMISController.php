<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Taluka;
use App\District;
use App\BeneficiaryPensions;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\Scheme;
use App\UrbanBody;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\AuthChecker;

class DBTSchemeWiseMISController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $designation_id = Auth::user()->designation_id;
        if ($designation_id == 'OSD' || $designation_id == 'Admin') {
            $c_time = Carbon::now();
            $year = $c_time->year;
            $user_id = AuthChecker::getUserId();
            $schemes = DB::select(DB::raw("select DISTINCT dbt_scheme_code, scheme_name from pds.master_scheme scheme join dbt.dbtconsolidatedata dbt on scheme.dbt_scheme_code=dbt.\"SchemeCode\""));
            $fin_year = DB::select(DB::raw("select DISTINCT \"FinancialYear\" from  dbt.dbtconsolidatedata"));
            $reporting_month = Config::get('constants.monthval');
            return view('DBT-MIS/index')->with('schemes', $schemes)->with('fin_year', $fin_year)->with('months', $reporting_month);
        } else {
            return redirect("/")->with('success', 'User Disabled. ');
        }
    }
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $scheme_code = $request->scheme_id;
            $fin_year = $request->fin_year;
            $fin_month = $request->fin_month;
            $query = "select \"ReportingMonth\",\"TotalBen\",\"TotalBenDigitized\",\"BenAadharSeeded\",\"MobileCaptured\",\"FundTrnsferCash\",\"AmntTrnsCashElectronic\",\"TrnsAadharSeeded\",\"NoDeDuplicated\",\"SavingAmnt\",\"Remarks\" from  dbt.dbtconsolidatedata where \"SchemeCode\" ='" . $scheme_code . "' and \"FinancialYear\" ='" . $fin_year . "'";
            if ($fin_month) {
                $query .= " and \"ReportingMonth\" = '" . $fin_month . "'";
            }
            $data = DB::connection('pgsql_mis')->select($query);
            return datatables()
                ->of($data)
                ->addColumn('ReportingMonth', function ($data) {
                    return Config::get('constants.monthval.' . $data->ReportingMonth);
                })
                ->addColumn('TotalBen', function ($data) {
                    return $data->TotalBen;
                })
                ->addColumn('TotalBenDigitized', function ($data) {
                    return $data->TotalBenDigitized;
                })
                ->addColumn('BenAadharSeeded', function ($data) {
                    return $data->BenAadharSeeded;
                })
                ->addColumn('MobileCaptured', function ($data) {
                    return $data->MobileCaptured;
                })
                ->addColumn('FundTrnsferCash', function ($data) {
                    return $data->FundTrnsferCash;
                })
                ->addColumn('AmntTrnsCashElectronic', function ($data) {
                    return $data->AmntTrnsCashElectronic;
                })
                ->addColumn('TrnsAadharSeeded', function ($data) {
                    return $data->TrnsAadharSeeded;
                })
                ->addColumn('NoDeDuplicated', function ($data) {
                    return $data->NoDeDuplicated;
                })
                ->addColumn('SavingAmnt', function ($data) {
                    return $data->SavingAmnt;
                })
                ->addColumn('Remarks', function ($data) {
                    return $data->Remarks;
                })
                ->rawColumns(['ReportingMonth', 'TotalBen', 'TotalBenDigitized', 'BenAadharSeeded', 'MobileCaptured', 'FundTrnsferCash', 'AmntTrnsCashElectronic', 'TrnsAadharSeeded', 'NoDeDuplicated', 'SavingAmnt', 'Remarks'])
                ->make(true);
        }
    }
}
