<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Taluka;
use App\District;
use App\BeneficiaryPensions;
// use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\Scheme;
use App\lot_master;
//sayantika 21-03-2020
use App\UrbanBody;
use Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuthChecker;


class lotWiseBenCountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        set_time_limit(200);
        date_default_timezone_set('Asia/Kolkata');
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
            $table_name =  strtolower($schema_name) . '.beneficiary';
        } else {
            $table_name =  'pension.beneficiary';
        }
        return $table_name;
    }

    public function index()
    {
        $user_id = AuthChecker::getUserId();
        $designation_id = Auth::user()->designation_id;
        // echo $designation_id;die;
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        // $schemes = Scheme::where('is_active', 1)->where('id', 5)->get(['scheme_name as name', 'id as id']);
        $schemes = DB::select(DB::raw("select id,scheme_name as name from m_scheme where  id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . " and scheme_id in(5) )"));
        // echo $schemes;die;
        if (count($schemes) > 0) {
            if ($designation_id == 'DDO' || $designation_id == 'Admin' || $designation_id == 'HOD') {
                return view('lot-wise-benficiary-count/lot-wise-ben-count', ['schemes' => $schemes]);
            } else {
                return redirect("/")->with('success', 'User Disabled. ');
            }
        } else {
            return redirect("/")->with('success', 'User UnAuthorized.');
        }
    }

    public function getLot(Request $request)
    {
        // echo 1;die;
        $lot_year = $request->lot_year;
        $lot_month = $request->lot_month;
        $scheme_id = $request->scheme_id;

        $query = lot_master::select('lot_no')->where('lot_year', $lot_year)->where('lot_month', $lot_month)->where('scheme_id', $scheme_id)->get();
        return response()->json($query);
    }

    public function lotWiseBeneficiaryCount(Request $request)
    {
        $scheme_code = $request->scheme_code;
        $scheme_name = lot_master::select('payment_mode')->where('scheme_id', $scheme_code)->first();
        // echo $scheme_name->payment_mode;die;
        if ($scheme_name->payment_mode == 'SBI') {
            $condition = 'lot_no';
        }
        if ($scheme_name->payment_mode == 'IFMS') {
            $condition = 'drn_part';
        }
        $lot_year = $request->lot_year;
        $lotYearArr = explode("-", $lot_year);
        // echo $lotYearArr[1];die;
        $firstYear = substr($lotYearArr[0], 2);
        $secondYear = substr($lotYearArr[1], 2);
        $concateYear = $firstYear . $secondYear;
        // echo $concateYear;die;
        $lot_month = $request->lot_month;
        $lot_no = $request->lot_no;
        if ($request->ajax()) {
            if ($lot_no == 'all') {
                $query = "Select district_name as district,
                coalesce(sum(total) ,0) as count
                from public.m_district d left join
                (
                select dist_code,
                count(1) as total 
                FROM " . strtolower($scheme_name->payment_mode) . ".transaction_lot_details tld JOIN lot_master lm ON tld." . $condition . " = lm.lot_no WHERE tld.scheme_id = " . $scheme_code . " AND lm.lot_month = '" . $lot_month . "' AND lm.lot_year = '" . $lot_year . "'
                group by dist_code  
                UNION ALL
                select dist_code,
                count(1) as total 
                FROM " . strtolower($scheme_name->payment_mode) . ".transaction_lot_details_report_" . $concateYear . " tld JOIN lot_master lm ON tld." . $condition . " = lm.lot_no WHERE tld.scheme_id = " . $scheme_code . " AND lm.lot_month = '" . $lot_month . "' AND lm.lot_year = '" . $lot_year . "'
                group by dist_code
                )b  on d.district_code = b.dist_code group by d.district_name order by district_name";
                // echo $lot_no;die;
            } else {
                $query = "Select district_name as district,
                coalesce(sum(total) ,0) as count
                from public.m_district d left join
                (
                select dist_code,
                count(1) as total 
                FROM " . strtolower($scheme_name->payment_mode) . ".transaction_lot_details tld JOIN lot_master lm ON tld." . $condition . " = lm.lot_no WHERE tld.scheme_id = " . $scheme_code . " AND lm.lot_month = '" . $lot_month . "' AND lm.lot_year = '" . $lot_year . "' AND tld." . $condition . " = '" . $lot_no . "'
                group by dist_code  
                UNION ALL
                select dist_code,
                count(1) as total 
                FROM " . strtolower($scheme_name->payment_mode) . ".transaction_lot_details_report_2122 tld JOIN lot_master lm ON tld." . $condition . " = lm.lot_no WHERE tld.scheme_id = " . $scheme_code . " AND lm.lot_month = '" . $lot_month . "' AND lm.lot_year = '" . $lot_year . "' AND tld." . $condition . " = '" . $lot_no . "'
                group by dist_code
                )b  on d.district_code = b.dist_code group by d.district_name order by district_name";
            }
            // echo $query;die();
            $result = DB::connection('pgsql_mis')->select($query);
            // echo '<pre>';print_r($result);die();
            return datatables()->of($result)
                ->make(true);
        }
    }
}
