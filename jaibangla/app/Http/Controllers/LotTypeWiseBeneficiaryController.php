<?php

namespace App\Http\Controllers;

use App\BeneficiaryPensions;
use App\Configduty;
use App\Helpers\Helper;
use App\lot_master;
use App\LotMaster;
use App\LotTypeMaster;
use App\SchemeDesigPaymentMode;
use App\Scheme;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LotTypeWiseBeneficiaryController extends Controller
{
    public function __construct()
    {
        //	$this->middleware('MaintainMiddleware');
        //set_time_limit(200);
        date_default_timezone_set('Asia/Kolkata');
    }

    public function index()
    {
        $user_id = Auth::user()->id;
        // dd($user_id);
        $schemearray = array();
        $lottype_master = LotTypeMaster::orderBy('id')->get();
        // $scheme_master = Scheme::orderBy('id')
        $schemes = DB::select(DB::raw("select id,scheme_name,lot_type_id from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1)"));
        // dd($schemes);
        // echo $schemes[0]->lot_type_id;die;
        // if ($user_id == 'DDO') {
        return view('lot-wise-beneficiary/index', ['schemes' => $schemes, 'lottype_master' => $lottype_master]);
        // }
    }

    public function lotTypeWiseBeneficiary(Request $request)
    {
        $lot_type = $request->lot_type;
        // dd($lot_type);
        $scheme_id = $request->scheme_code;
        $schemeObj = Scheme::where('id', $scheme_id)->first();
        $tablename = $schemeObj->short_code . '.beneficiary';
        // echo $tablename;die;
        if ($request->ajax()) {
            $query = "SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, 
            t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) AS lot_month_year FROM
            (SELECT TO_CHAR(created_at,'yymm')::int AS yymm,* FROM " . $tablename . "
            WHERE next_level_role_id=0";
            if ($lot_type == 1) {
                $query .= " AND payment_count=0 AND lot_generated=0 AND last_paid_yymm=0 and scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
            LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code";
            } elseif ($lot_type == 2) {
                $query = "Repeat Lot";
            } elseif ($lot_type == 3) {
                $query .= " AND payment_count>0 AND lot_generated=0 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
            LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code";
            }elseif ($lot_type == 9) {
                $query .= " AND payment_count>0 AND lot_generated=2 AND scheme_id=".$scheme_id." AND dup_bank=0 and dup_bank_pending=0) t
                LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code";
            } elseif ($lot_type == 10) {
                $query .= " AND payment_count=0 AND lot_generated=0 AND last_paid_yymm>0 AND scheme_id=".$scheme_id." AND dup_bank=0 and dup_bank_pending=0) t
                LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code";
            }
            elseif ($lot_type == 4) {
                $query = "SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) FROM
            (SELECT (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS lot_month_year,* FROM " . $tablename . "
            WHERE next_level_role_id=0 AND (payment_count > 0 OR payment_count = 0) AND bank_edited = 1 AND lot_generated=-1 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
            LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code
            
            UNION ALL
            
            SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) FROM
            (SELECT TO_CHAR(created_at,'yymm')::int AS lot_month_year,* FROM " . $tablename . "
            WHERE next_level_role_id=0 AND (payment_count > 0 OR payment_count = 0) AND bank_edited = 1 AND lot_generated=-1 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
            LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code";
            } elseif ($lot_type == 5) {
                $query = "SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) FROM
            (SELECT (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,* FROM " . $tablename . "
            WHERE next_level_role_id=0 AND (payment_count > 0 OR payment_count = 0) AND bank_edited = 1 AND lot_generated=-2 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
            LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code
            
            UNION ALL
            
            SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) FROM
            (SELECT TO_CHAR(created_at,'yymm')::int AS yymm,* FROM " . $tablename . "
            WHERE next_level_role_id=0 AND (payment_count > 0 OR payment_count = 0) AND bank_edited = 1 AND lot_generated=-2 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
            LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code";
            } elseif ($lot_type == 6) {
                $query = "SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) AS lot_month_year FROM
            (SELECT (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,* FROM " . $tablename . "
            WHERE next_level_role_id=0 AND (payment_count > 0 OR payment_count = 0) AND bank_edited = 1 AND lot_generated=-3 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
            LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code
            
            UNION ALL
            
            SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) AS lot_month_year FROM
            (SELECT TO_CHAR(created_at,'yymm')::int AS yymm,* FROM " . $tablename . "
            WHERE next_level_role_id=0 AND (payment_count > 0 OR payment_count = 0) AND bank_edited = 1 AND lot_generated=-3 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
            LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code";
            }
            // print $query;die;
            $result = DB::connection('pgsql_mis')->select($query);
            // print_r($result);die;
            return datatables()->of($result)
            ->addColumn('created_at', function ($result) {
                $created_at = date('d-m-Y', strtotime(trim(str_replace('/', '-', $result->created_at))));
                return $created_at;
            })
            ->addColumn('last_paid_yymm', function($result){
                $last_paid_yymm = '';
                if ($result->last_paid_yymm > 0) {
                    $month = substr($result->last_paid_yymm,-2);
                    $year = substr($result->last_paid_yymm, 0, 2);
                    $monthName = date("F", mktime(0, 0, 0, $month, 10));
                    $last_paid_yymm = $monthName.' - 20'.$year;
                    // echo $last_paid_yymm;die;
                } else {
                    $last_paid_yymm = $result->last_paid_yymm;
                }
                return $last_paid_yymm;
            })
            ->rawColumns(['created_at', 'last_paid_yymm'])
            ->make(true);
        }
    }

    public function lotWiseBenExcel(Request $request)
    {
        $lot_type = $request->lot_type;
        // dd($lot_type);
        $scheme_id = $request->scheme_id;
        $schemeObj = Scheme::where('id', $scheme_id)->first();
        $tablename = $schemeObj->short_code . '.beneficiary';

        $query = "SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) AS lot_month_year FROM
        (SELECT TO_CHAR(created_at,'yymm')::int AS yymm,* FROM " . $tablename . "
        WHERE next_level_role_id=0";
        if ($lot_type == 1) {
            $query .= " AND payment_count=0 AND lot_generated=0 AND last_paid_yymm=0 and scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
        LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code";
        } elseif ($lot_type == 2) {
            $query = "Repeat Lot";
        } elseif ($lot_type == 3) {
            $query .= " AND payment_count>0 AND lot_generated=0 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
        LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code";
        } elseif ($lot_type == 4) {
            $query = "SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) FROM
        (SELECT (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS lot_month_year,* FROM " . $tablename . "
        WHERE next_level_role_id=0 AND (payment_count > 0 OR payment_count = 0) AND bank_edited = 1 AND lot_generated=-1 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
        LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code
        
        UNION ALL
        
        SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) FROM
        (SELECT TO_CHAR(created_at,'yymm')::int AS lot_month_year,* FROM " . $tablename . "
        WHERE next_level_role_id=0 AND (payment_count > 0 OR payment_count = 0) AND bank_edited = 1 AND lot_generated=-1 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
        LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code";
        } elseif ($lot_type == 5) {
            $query = "SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) FROM
        (SELECT (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,* FROM " . $tablename . "
        WHERE next_level_role_id=0 AND (payment_count > 0 OR payment_count = 0) AND bank_edited = 1 AND lot_generated=-2 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
        LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code
        
        UNION ALL
        
        SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) FROM
        (SELECT TO_CHAR(created_at,'yymm')::int AS yymm,* FROM " . $tablename . "
        WHERE next_level_role_id=0 AND (payment_count > 0 OR payment_count = 0) AND bank_edited = 1 AND lot_generated=-2 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
        LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code";
        } elseif ($lot_type == 6) {
            $query = "SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) FROM
        (SELECT (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,* FROM " . $tablename . "
        WHERE next_level_role_id=0 AND (payment_count > 0 OR payment_count = 0) AND bank_edited = 1 AND lot_generated=-3 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
        LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code
        
        UNION ALL
        
        SELECT t.id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(t.ben_fname,'')||' '||COALESCE(t.ben_mname,'')||' '||COALESCE(t.ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, d.district_name, t.block_ulb_name, t.created_at, t.bank_code, t.bank_ifsc, t.payment_count, t.last_paid_yymm, TRIM(TO_CHAR(TO_DATE (RIGHT(t.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(t.yymm::varchar,2) FROM
        (SELECT TO_CHAR(created_at,'yymm')::int AS yymm,* FROM " . $tablename . "
        WHERE next_level_role_id=0 AND (payment_count > 0 OR payment_count = 0) AND bank_edited = 1 AND lot_generated=-3 AND scheme_id=" . $scheme_id . " AND dup_bank=0 and dup_bank_pending=0) t 
        LEFT JOIN m_district d ON d.district_code = t.created_by_dist_code";
        }
        //    echo $query;die;
        $result = DB::connection('pgsql_mis')->select($query);
        $excelarr[] = array(
            'Application ID',  'Name', 'District', 'Block/Municipality', 'Created Date', 'Account No', 'Bank IFSC', 'Payment Count', 'Last Payment Date', 'Lot Month-Year',
        );

        foreach ($result as $arr) {
            // echo 1;die();
            $excelarr[] = array(
                'Application Id' => trim($arr->id),
                'Name' => trim($arr->fullname),
                'District' => trim($arr->district_name),
                'Block/Municipality' => trim($arr->block_ulb_name),
                'Created Date' => trim($arr->created_at),
                'Account No' => trim($arr->bank_code),
                'Bank IFSC' => trim($arr->bank_ifsc),
                'Payment Count' => trim($arr->payment_count),
                'Last Payment Date' => trim($arr->last_paid_yymm),
                'Lot Month-Year' => trim($arr->lot_month_year),
            );
        }
        $file_name = $schemeObj->scheme_name . ' ' .  date('d/m/Y');
        Excel::create($file_name, function ($excel) use ($excelarr) {
            $excel->setTitle('Lot Wise Beneficiary List');
            $excel->sheet('Lot Wise Beneficiary List', function ($sheet) use ($excelarr) {
                $sheet->fromArray($excelarr, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    public function schemeWiseLotType(Request $request)
    {
        // echo $scheme_id;die;
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in form submit.');
            return response()->json($response, $statusCode);
        }
        try {
            $scheme_id = $request->scheme_id;
            $query = "SELECT * FROM m_lot_type WHERE id IN(SELECT UNNEST(lot_type_id) lot_type_id FROM m_scheme WHERE id=".$scheme_id.") AND id != 9";
            $lotTypeName = DB::select($query);
            // dd($lotTypeName);
            if ($lotTypeName == null) {
                return  $response = array(
                    'status' => 1, 'msg' => 'No Lot Type Available For This Scheme!!',
                    'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Warning!!'
                );
            } else {
                $response = $lotTypeName;
            }
            // dd($response);
        }catch (\Exception $e) {
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        }finally {
            return response()->json($response, $statusCode);
        }
    }   
}
