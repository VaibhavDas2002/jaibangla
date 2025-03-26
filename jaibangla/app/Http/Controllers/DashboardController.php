<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\applicationModel;
use App\User;
use App\PicUpload;
use App\Policestation;
use Illuminate\Support\Facades\Storage;
use App\Configduty;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\MapLavel;
use App\Scheme;
use App\UserManual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\SchemeStepRank;

class DashboardController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(60);
  }
  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $user_id = Auth::user()->id;
    $designation_id = Auth::user()->designation_id;

    $role = [];
    $duty = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->get();
    foreach ($duty as $dutyObj) {
      if ($dutyObj->is_state_login) {
        if ($designation_id == 'Approver' || $designation_id == 'Delegated Approver') {
          $is_first = 0;
        } else {
          $is_first = 1;
        }
        $newArr = array();
        $newArr['role_name'] = $designation_id;
        $newArr['scheme_id'] = $dutyObj->scheme_id;
        $newArr['district_code'] = 0;
        $newArr['mapping_level'] = trim($dutyObj->mapping_level);
        $newArr['is_urban'] = NULL;
        $newArr['taluka_code'] = NULL;
        $newArr['urban_body_code'] = NULL;
        $newArr['is_state_login'] = 1;
        $newArr['is_first'] = $is_first;
        $newArr['id'] = NULL;
        array_push($role, $newArr);
      } else if ($designation_id == 'StatusCheckerDistrict' || $designation_id == 'StatusCheckerField'  || $designation_id == 'MIS User') {
          $newArr = array();
          $newArr['role_name'] = $designation_id;
          $newArr['scheme_id'] = $dutyObj->scheme_id;
          $newArr['district_code'] = $dutyObj->district_code;;
          $newArr['mapping_level'] = trim($dutyObj->mapping_level);
          $newArr['is_urban'] = $dutyObj->is_urban;
          $newArr['taluka_code'] = $dutyObj->taluka_code;
          $newArr['urban_body_code'] = $dutyObj->urban_body_code;
          $newArr['is_state_login'] = 0;
          $newArr['is_first'] = NULL;
          $newArr['id'] = NULL;
          array_push($role, $newArr);
        }
        else if ($designation_id == 'Delegated Approver') {
          $mapArr = MapLavel::where('scheme_id', $dutyObj->scheme_id)->where('role_name', 'Approver')->where('stack_level', $dutyObj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->toArray();
          if (count($mapArr) > 0) {
            $newArr = array_merge($mapArr[0], ['is_state_login' => 0, 'district_code' => $dutyObj->district_code, 'mapping_level' => $dutyObj->mapping_level, 'taluka_code' => $dutyObj->taluka_code, 'urban_body_code' => $dutyObj->urban_body_code, 'is_urban' => $dutyObj->is_urban]);
            array_push($role, $newArr);
          }
        } 
        else if ($designation_id == 'Delegated Verifier') {
          $mapArr = MapLavel::where('scheme_id', $dutyObj->scheme_id)->where('role_name', 'Verifier')->where('stack_level', $dutyObj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->toArray();
          if (count($mapArr) > 0) {
            $newArr = array_merge($mapArr[0], ['is_state_login' => 0, 'district_code' => $dutyObj->district_code, 'mapping_level' => $dutyObj->mapping_level, 'taluka_code' => $dutyObj->taluka_code, 'urban_body_code' => $dutyObj->urban_body_code, 'is_urban' => $dutyObj->is_urban]);
            array_push($role, $newArr);
          }
        } 
        else {
          $mapArr = MapLavel::where('scheme_id', $dutyObj->scheme_id)->where('role_name', $designation_id)->where('stack_level', $dutyObj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->toArray();
          if (count($mapArr) > 0) {
            $newArr = array_merge($mapArr[0], ['is_state_login' => 0, 'district_code' => $dutyObj->district_code, 'mapping_level' => $dutyObj->mapping_level, 'taluka_code' => $dutyObj->taluka_code, 'urban_body_code' => $dutyObj->urban_body_code, 'is_urban' => $dutyObj->is_urban]);
            array_push($role, $newArr);
          }
        }
      
    }

    /*echo "<pre>";
            //echo json_encode($duty);
            print_r($role);
            echo "</pre>";
            die();*/
    $reports = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1) and is_active=1 order by scheme_name"));
    $s_arr = [];
    foreach ($reports as $k) {
      array_push($s_arr, $k->id);
    }
    $s_id = implode(',', $s_arr);
    $request->session()->put('role', $role);


// if($user_id == 8967){
//   dump($role);
//   dd($request->session()->get('role'));
// }


    if ($designation_id == 'DDO' || $designation_id == 'Corp') {


      $sbi_count = DB::table('sbi.transaction_lot')->where('lot_status', 5)->whereIn('scheme_id', $s_arr)->count();
      if (count($s_arr)>0) {
        $qu = "SELECT count(*) total FROM ifms.transaction_lot tl join m_scheme m on m.id=tl.scheme_id 
                join ifms.temp_lot_master tlm on tl.lot_no=tlm.lot_no where  tl.scheme_id in(" . $s_id . ")
                and tlm.rbi_sent_count=(tlm.rbi_receive_success_count+tlm.rbi_receive_failed_count) and tl.lot_status=4";
      }
      else {
        $qu = "SELECT 0 AS total";
      }
      $ifms_count = DB::connection('pgsql_main_mis')->select($qu);
      return view('ddo_dashboard', ['report' => $reports, 'sbi' => $sbi_count, 'ifms' => $ifms_count]);
    } else if ($designation_id == 'HOD' || $designation_id == 'Dashboard') {
      return view('hod_dashboard', ['report' => $reports]);
    } else {
      return view('dashboard', compact('reports'));
    }
  }

  #################################################################################
  #                    New Changes on Dashboard From 25-May-2021                  #
  #===============================================================================#
  #       (1) Get Pending Beneficiaries Fresh, Adjustment & Error Lot             #
  #       (2) Get Pending For Repeat Lot                                          #
  #       (3) Get Pending For Import SBI Report(Final Compilation from DDO End)   #
  #       (4) Get Pending For Import RBI Report(Final Compilation from DDO End)   #    
  #################################################################################
  public function getPaymentPending(Request $request)
  {
    $selectscheme = $request->selectscheme;
    $selectyear = $request->selectyear;
    $schemeObj = Scheme::where('id', $selectscheme)->first();
    $tablename = $schemeObj->short_code . '.beneficiaries';
    $year = '"Year"';
    $month = '"Month"';
    $query = "";
    $query = "SELECT 
      TRIM(TO_CHAR(TO_DATE (RIGHT(fl.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(fl.yymm::varchar,2) AS year_month, fl.fresh_count AS pending_fresh_lot_ben_count, 
      fl.fr_count AS pending_resumed_lot_ben_count,
      fl.adj_count AS pending_adj_lot_ben_count,
      fl.sbi_error_count AS pending_sbi_err_lot_ben_count,
      fl.rbi_error_count AS pending_rbi_err_lot_ben_count,
      fl.ifms_error_count AS pending_ifms_err_lot_ben_count
      FROM(
        SELECT f.yymm,
        COALESCE (SUM(f.c) FILTER(WHERE f.t='F'),0) fresh_count,
        COALESCE(SUM(f.c) FILTER(WHERE f.t='FR'),0) fr_count,
        COALESCE(SUM(f.c) FILTER(WHERE f.t='A'),0) adj_count,
        COALESCE(SUM(f.c) FILTER(WHERE f.t='SF'),0) sbi_error_count,
        COALESCE(SUM(f.c) FILTER(WHERE f.t='RF'),0) rbi_error_count,
        COALESCE(SUM(f.c) FILTER(WHERE f.t='IF'),0) ifms_error_count
        FROM
        (   
          SELECT 'F' AS t,TO_CHAR(created_at,'yymm')::int AS yymm, COUNT(1) AS c FROM " . $tablename . " 
          WHERE next_level_role_id=0 AND payment_count=0 AND lot_generated=0 AND last_paid_yymm=0 and scheme_id=" . $selectscheme . "
          GROUP BY t,TO_CHAR(created_at,'yymm')
          UNION ALL
          SELECT 'FR' AS t, (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,COUNT(1) AS c FROM " . $tablename . "
          WHERE next_level_role_id=0 AND payment_count=0 AND lot_generated=0 AND last_paid_yymm>0 AND scheme_id=" . $selectscheme . "
          GROUP BY t,to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM')::int
          UNION ALL
          SELECT 'A' AS t, (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,COUNT(1) AS c FROM " . $tablename . " 
          WHERE next_level_role_id=0 AND payment_count>0 AND lot_generated=0 AND scheme_id=" . $selectscheme . "
          GROUP BY t,to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM')::int
          UNION ALL
          SELECT fail.t AS t,fail.yymm AS yymm,SUM(fail.c) AS c FROM
          (
            SELECT 
            CASE 
              WHEN lot_generated=-3 THEN 'SF' 
              WHEN lot_generated=-2 THEN 'RF' 
              WHEN lot_generated=-1 THEN 'IF' 
            END AS t ,
            (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,lot_generated,COUNT(1) AS c FROM " . $tablename . " 
            WHERE next_level_role_id=0 AND payment_count>0 AND lot_generated IN(-1,-2,-3) 
            AND bank_edited=1 AND scheme_id=" . $selectscheme . "
            GROUP BY t,(to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int,lot_generated
            UNION ALL
            SELECT 
            CASE 
              WHEN lot_generated=-3 THEN 'SF' 
              WHEN lot_generated=-2 THEN 'RF' 
              WHEN lot_generated=-1 THEN 'IF' 
            END AS t ,
            TO_CHAR(created_at,'yymm')::int AS yymm,lot_generated,COUNT(1) AS c FROM " . $tablename . " 
            WHERE next_level_role_id=0 AND payment_count=0 AND lot_generated IN(-1,-2,-3) 
            AND bank_edited=1 AND scheme_id=" . $selectscheme . "
            GROUP BY t,TO_CHAR(created_at,'yymm'),lot_generated
          ) fail
          GROUP BY fail.t,fail.yymm
        ) f GROUP BY f.yymm
      )fl";
    // print $query;die();
    $data = DB::connection('pgsql_mis')->select($query);
    // print_r($data);die();          
    return datatables()->of($data)
      ->addColumn('year_month', function ($data) {
        return $data->year_month;
      })
      ->addColumn('pending_f', function ($data) {
        return $data->pending_fresh_lot_ben_count;
      })
      ->addColumn('pending_fr', function ($data) {
        return $data->pending_resumed_lot_ben_count;
      })
      ->addColumn('pending_a', function ($data) {
        return $data->pending_adj_lot_ben_count;
      })
      ->addColumn('pending_i', function ($data) {
        return $data->pending_ifms_err_lot_ben_count;
      })
      ->addColumn('pending_r', function ($data) {
        return $data->pending_rbi_err_lot_ben_count;
      })
      ->addColumn('pending_s', function ($data) {
        return $data->pending_sbi_err_lot_ben_count;
      })

      ->rawColumns(['year_month', 'pending_f', 'pending_a', 'pending_i', 'pending_r', 'pending_s'])
      ->make(true);
  }

  public function getStandardLotPending(Request $request)
  {
    $selectscheme = $request->sd_scheme;
    $schemeObj = Scheme::where('id', $selectscheme)->first();
    $tablename = $schemeObj->short_code . '.beneficiaries';
    $query = "";
    $query = "SELECT 
      TRIM(TO_CHAR(TO_DATE (RIGHT(fl.yymm::varchar,2), 'MM'), 'Month')) || ' - ' || '20' || LEFT(fl.yymm::varchar,2) AS year_month, 
      fl.sd_count AS pending_sd_lot_ben_count
      FROM(
        SELECT f.yymm,
        COALESCE(SUM(f.c) FILTER(WHERE f.t='SD'),0) sd_count
        FROM
        (   
          SELECT 'SD' AS t, (to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM'))::int AS yymm,COUNT(1) AS c FROM " . $tablename . " 
          WHERE next_level_role_id=0 AND payment_count>0 AND lot_generated=2 AND scheme_id=" . $selectscheme . "
          GROUP BY t,to_char((to_date(last_paid_yymm::varchar,'yymm')+ interval '1 month'),'YYMM')::int
        ) f GROUP BY f.yymm
      )fl";
    $data = DB::connection('pgsql_mis')->select($query);
    // print_r($data);die();          
    return datatables()->of($data)
      ->addColumn('year_month', function ($data) {
        return $data->year_month;
      })
      ->addColumn('pending_sd', function ($data) {
        return $data->pending_sd_lot_ben_count;
      })

      ->rawColumns(['year_month', 'pending_sd'])
      ->make(true);
  }


  public function getRepeatPending(Request $request)
  {
    $selectrepeatyear = $request->repeat_year;
    $selectrepeatmonth = $request->repeat_month;
    $arrscheme = array();
    $user_id = Auth::user()->id;
    $report = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1)  and is_active=1 order by scheme_name"));
    foreach ($report as $report) {
      array_push($arrscheme, $report->id);
    }
    $query = "";
    $query = "select m.scheme_name,lot_month,lot_year,lot_no from lot_master,m_scheme m where 
      (repeat_drn_part is null or repeat_drn_part ='0')
      and (repeat_lot is null or repeat_lot ='0')
      and rbi_success_count>0
      and ref_no>0
      and m.id=lot_master.scheme_id
      and scheme_id in ( " . implode(',', $arrscheme) . ")
      and lot_year='" . $selectrepeatyear . "'";
    if (!empty($selectrepeatmonth)) {
      $query .= " and lot_month='" . $selectrepeatmonth . "' ";
    }
    $query .= "group by 
      lot_month,lot_year,m.scheme_name,lot_no order by m.scheme_name, to_date(lot_month,'Month'),lot_year";
    $data = DB::connection('pgsql_mis')->select($query);
    // print_r($data);die();          
    return datatables()->of($data)
      ->addColumn('scheme_name', function ($data) {
        return $data->scheme_name;
      })
      ->addColumn('lot_month', function ($data) {
        return $data->lot_month;
      })
      ->addColumn('lot_year', function ($data) {
        return $data->lot_year;
      })
      ->addColumn('lot_no', function ($data) {
        return $data->lot_no;
      })


      ->rawColumns(['scheme_name', 'lot_month', 'lot_year', 'lot_no'])
      ->make(true);
  }

  public function importSBIReportPending(Request $request)
  {
    $scheme = $request->sbi_scheme;
    $month = $request->sbi_month;
    $year = $request->sbi_year;
    $user_id = Auth::user()->id;
    $scm = Configduty::select('scheme_id')->distinct()->where('user_id', '=', $user_id)->where('is_active', 1)->get();
    $scheme_arr = [];
    foreach ($scm as $k) {
      array_push($scheme_arr, $k->scheme_id);
    }
    $s_id = implode(',', $scheme_arr);
    $query = "";
    $query = "SELECT 
            m.scheme_name,
            tl.lot_month,
            tl.lot_year,
            COUNT(tl.lot_no) AS total_lot  
        FROM sbi.transaction_lot tl 
        JOIN m_scheme m ON m.id=tl.scheme_id
        JOIN sbi.status s ON s.status_code=tl.lot_status
        where tl.lot_status in(5)";
    if (!is_null($scheme)) {
      $query = $query . " AND tl.scheme_id=" . $scheme . "";
    } else {
      $query = $query . " AND tl.scheme_id IN(" . $s_id . ")";
    }
    if (!is_null($month)) {
      $query = $query . " AND tl.lot_month='" . $month . "'";
    }
    if (!is_null($year)) {
      $query = $query . " AND tl.lot_year='" . $year . "'";
    }

    $query = $query . " GROUP BY m.scheme_name,tl.lot_month,tl.lot_year
            ORDER BY m.scheme_name,to_date(tl.lot_month,'Month'),tl.lot_year";

    $data = DB::connection('pgsql_mis')->select($query);
    return datatables()->of($data)
      ->addColumn('scheme_name', function ($data) {
        return $data->scheme_name;
      })
      ->addColumn('lot_month', function ($data) {
        return $data->lot_month;
      })
      ->addColumn('lot_year', function ($data) {
        return $data->lot_year;
      })
      ->addColumn('total_lot', function ($data) {
        return $data->total_lot;
      })

      ->rawColumns(['scheme_name', 'lot_month', 'lot_year', 'total_lot'])
      ->make(true);
  }

  public function importRbiReportPending(Request $request)
  {
    $scheme = $request->ifms_scheme;
    $month = $request->ifms_month;
    $year = $request->ifms_year;
    $user_id = Auth::user()->id;
    $scm = Configduty::select('scheme_id')->distinct()->where('user_id', '=', $user_id)->where('is_active', 1)->get();
    $scheme_arr = [];
    foreach ($scm as $k) {
      array_push($scheme_arr, $k->scheme_id);
    }
    $s_id = implode(',', $scheme_arr);
    $query = "";
    $query = "SELECT m.scheme_name,tl.lot_month,tl.lot_year,count(*) total_lot 
            FROM ifms.transaction_lot tl join m_scheme m on m.id=tl.scheme_id 
            join ifms.temp_lot_master tlm on tl.lot_no=tlm.lot_no where 
            tlm.rbi_sent_count=(tlm.rbi_receive_success_count+tlm.rbi_receive_failed_count) and tl.lot_status=4";
    if (!is_null($scheme)) {
      $query = $query . " AND tl.scheme_id=" . $scheme . "";
    } else {
      $query = $query . " AND tl.scheme_id IN(" . $s_id . ")";
    }
    if (!is_null($month)) {
      $query = $query . " AND tl.lot_month='" . $month . "'";
    }
    if (!is_null($year)) {
      $query = $query . " AND tl.lot_year='" . $year . "'";
    }

    $query = $query . " GROUP BY m.scheme_name,tl.lot_month,tl.lot_year
            ORDER BY m.scheme_name,to_date(tl.lot_month,'Month'),tl.lot_year";

    $data = DB::connection('pgsql_mis')->select($query);
    return datatables()->of($data)
      ->addColumn('scheme_name', function ($data) {
        return $data->scheme_name;
      })
      ->addColumn('lot_month', function ($data) {
        return $data->lot_month;
      })
      ->addColumn('lot_year', function ($data) {
        return $data->lot_year;
      })
      ->addColumn('total_lot', function ($data) {
        return $data->total_lot;
      })

      ->rawColumns(['scheme_name', 'lot_month', 'lot_year', 'total_lot'])
      ->make(true);
  }

  public function getApproverBenARPending(Request $request)
  {
    $scheme = $request->ben_scheme;

    $user_id = Auth::user()->id;
    $year = '"Year"';
    $month = '"Month"';
    $approve = '"Approved"';
    $reject = '"Rejected"';
    $applied = '"Applied"';
    $pending_approved = '"Pending_Approved"';
    $gettablename = Scheme::where('id', $scheme)->value('short_code');
    $dist_code = Configduty::where('scheme_id', $scheme)->where('user_id', $user_id)->value('district_code');
    // $next_level_role_id_Verifier= SchemeStepRank::getSchemeParentId($scheme, 2);
    $query = "";
    $query = "select m.scheme_name,TO_CHAR(pb.created_at,'Month') as " . $month . ",TO_CHAR(pb.created_at,'YYYY') as " . $year . ",
    count(*) as " . $applied . ",";

    if ($scheme == '17') {
      $query .= "sum(case when next_level_role_id = 32  and next_level_role_id!=9999 then 1 else 0 end ) as " . $pending_approved . ",";
    } else {
      $query .= "sum(case when is_verified = 1 and is_approved=0 and is_rejected=0 then 1 else 0 end ) as " . $pending_approved . ",";
    }




    $query .= "sum(case when next_level_role_id =0 then 1 else 0 end ) as " . $approve . ",
    sum(case when is_rejected=1 then 1 else 0 end ) as " . $reject . "
    from " . $gettablename . ".beneficiaries pb,m_scheme m where  m.id=pb.scheme_id and created_by_dist_code=" . $dist_code;
    if (!is_null($scheme)) {
      $query .= "  and scheme_id = " . $scheme;
    }
    // if (!is_null($month)) {
    //   $query = $query . " AND tl.lot_month='" . $month . "'";
    // }
    // if (!is_null($year)) {
    //   $query = $query . " AND tl.lot_year='" . $year . "'";
    // }
    $query .= " group by m.scheme_name,TO_CHAR(pb.created_at,'Month'),TO_CHAR(pb.created_at,'YYYY'),to_char(pb.created_at,'Month YYYY')
    ORDER BY to_char(pb.created_at,'Month')";


    $data = DB::connection('pgsql_mis')->select($query);
    return datatables()->of($data)
      ->addColumn('scheme_name', function ($data) {
        return $data->scheme_name;
      })
      ->addColumn('year_month', function ($data) {
        return $data->Month . " " . $data->Year;
      })
      ->addColumn('applied', function ($data) {
        return $data->Applied;
      })
      ->addColumn('pending_approved', function ($data) {
        return $data->Pending_Approved;
      })
      ->addColumn('approved', function ($data) {
        return $data->Approved;
      })
      ->addColumn('rejected', function ($data) {
        return $data->Rejected;
      })

      ->rawColumns(['year_month', 'pending_approved', 'approved', 'rejected'])
      ->make(true);
  }

  public function getBankEditPending(Request $request)
  {
    $scheme = $request->bank_scheme;
    $level = $request->bank_level;

    $user_id = Auth::user()->id;
    $table_name = Scheme::where('id', $scheme)->value('short_code');
    $dist_code = Configduty::where('scheme_id', $scheme)->where('user_id', $user_id)->value('district_code');
    if ($level == 1) {


      $query = "select b.scheme_name,sub_district_name as block_name, b.ifms_pending, b.ifms_rectified, b.rbi_pending, b.rbi_rectified, b.sbi_pending, b.sbi_rectified
        FROM
        (select m.scheme_name,sub_district_name, 
    sum(case when bank_edited=0 and lot_generated='-1' and next_level_role_id=0 then 1 else 0 end )as ifms_pending,
    sum(case when bank_edited=1 and lot_generated='-1' and next_level_role_id=0 then 1 else 0 end ) as ifms_rectified,
       
    sum(case when  bank_edited=0 and lot_generated='-2' and next_level_role_id=0 then 1 else 0 end )as rbi_pending,
    sum(case when bank_edited=1 and lot_generated='-2' and next_level_role_id=0 then 1 else 0 end ) as rbi_rectified,
       
    sum(case when  bank_edited=0 and lot_generated='-3' and next_level_role_id=0 then 1 else 0 end )as sbi_pending,
    sum(case when bank_edited=1 and lot_generated='-3' and next_level_role_id=0 then 1 else 0 end ) as sbi_rectified
       
        from " . $table_name . ".beneficiaries b,m_scheme m, m_sub_district ub  where rural_urban_id=1 and created_by_dist_code=" . $dist_code . " 
       and b.scheme_id=" . $scheme . " and b.created_by_local_body_code=ub.sub_district_code and m.id=b.scheme_id
       and b.created_by_dist_code=ub.district_code group by m.scheme_name, ub.sub_district_name)b order by sub_district_name";
    } else if ($level == 2) {
      $query = "select b.scheme_name, b.block_name as block_name, b.ifms_pending, b.ifms_rectified,  b.rbi_pending, b.rbi_rectified, b.sbi_pending, b.sbi_rectified
            FROM
            (select m.scheme_name,bl.block_name, 
        sum(case when  bank_edited=0 and lot_generated='-1' and next_level_role_id=0 then 1 else 0 end )as ifms_pending,
        sum(case when bank_edited=1 and lot_generated='-1' and next_level_role_id=0 then 1 else 0 end ) as ifms_rectified,
           
        sum(case when  bank_edited=0 and lot_generated='-2' and next_level_role_id=0 then 1 else 0 end )as rbi_pending,
        sum(case when bank_edited=1 and lot_generated='-2' and next_level_role_id=0 then 1 else 0 end ) as rbi_rectified,
           
        sum(case when  bank_edited=0 and lot_generated='-3' and next_level_role_id=0 then 1 else 0 end )as sbi_pending,
        sum(case when bank_edited=1 and lot_generated='-3' and next_level_role_id=0 then 1 else 0 end ) as sbi_rectified
           
            from " . $table_name . ".beneficiaries b,m_scheme m, m_block bl  where rural_urban_id=2 and created_by_dist_code=" . $dist_code . " 
           and b.scheme_id=" . $scheme . " and  b.created_by_local_body_code=bl.block_code and m.id=b.scheme_id
           and b.created_by_dist_code=bl.district_code group by m.scheme_name,bl.block_name)b order by block_name";
    }


    $data = DB::connection('pgsql_mis')->select($query);
    return datatables()->of($data)
      ->addColumn('scheme_name', function ($data) {
        return $data->scheme_name;
      })
      ->addColumn('block_name', function ($data) {
        return $data->block_name;
      })
      ->addColumn('ifms_pending', function ($data) {
        return $data->ifms_pending;
      })
      ->addColumn('ifms_rectified', function ($data) {
        return $data->ifms_rectified;
      })
      ->addColumn('rbi_pending', function ($data) {
        return $data->rbi_pending;
      })
      ->addColumn('rbi_rectified', function ($data) {
        return $data->rbi_rectified;
      })
      ->addColumn('sbi_pending', function ($data) {
        return $data->sbi_pending;
      })
      ->addColumn('sbi_rectified', function ($data) {
        return $data->sbi_rectified;
      })

      ->rawColumns(['scheme_name', 'block_name', 'ifms_pending', 'ifms_rectified', 'rbi_pending', 'rbi_rectified', 'sbi_pending', 'sbi_rectified'])
      ->make(true);
  }


  public function getApproverDashboardData(Request $request)
  {
    $statusCode = 200;
    $response = [];
    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }



    try {
      //$sessionBankPending=Session::get('sessionBankPending');
      //$sessionApprovePending=Session::get('sessionApprovePending');
      //$sessionDuplicateReject=Session::get('sessionDuplicateReject');

      //  Session::forget('sessionBankPending');
      //   Session::forget('sessionApprovePending');
      //  Session::forget('sessionDuplicateReject');
      $user_id = Auth::user()->id;
      $dist_code = \App\Configduty::where('user_id', $user_id)->value('district_code');


      $totalbankeditpending = DB::connection('pgsql_mis')->table('pension.beneficiaries')->where('bank_edited', 0)->where('next_level_role_id', 0)->whereIn('lot_generated', [-1, -2, -3])->where('created_by_dist_code', $dist_code)->count('id');
      //  $sessionBankPending=Session::put('sessionBankPending',$totalbankeditpending);


      $totalapprovepending = DB::connection('pgsql_mis')->table('pension.beneficiaries')->where('is_verified',1)->where('is_approved',0)->where('is_rejected',0)->where('created_by_dist_code', $dist_code)->count('id');
      // $sessionApprovePending=Session::put('sessionApprovePending',$totalapprovepending);


      $totalduplicatereject = DB::connection('pgsql_mis')->table('public.duplicate_approve_reject')->where('dist_code', $dist_code)->count('id');
      // $sessionDuplicateReject=Session::put('sessionDuplicateReject',$totalduplicatereject);

      // $bankedit=($sessionBankPending=="")? $totalbankeditpending:$sessionBankPending;
      // $approvepending= ($sessionApprovePending=="")?$totalapprovepending:$sessionApprovePending;
      //$duplicatepending= ($sessionDuplicateReject=="")?$totalduplicatereject:$sessionDuplicateReject;
      $response = array('bankedit' => $totalbankeditpending, 'approvepending' => $totalapprovepending, 'duplicatepending' => $totalduplicatereject);
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
}
