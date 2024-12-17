<?php

namespace App\Http\Controllers;

use App\Configduty;
use App\District;
use App\GP;
use App\Taluka;
use App\Scheme;
use App\UrbanBody;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\SubDistrict;
use Validator;
use App\Ward;
use App\DsPhase;

class DuareSarkarApplicationController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(60);
    $this->ds_base_date_3 = '2022-02-15';
    $this->selected_phase = 3;
  }

  public function getDistrictApplicationReport()
  {
    //return redirect('/')->with('error', 'Not Allowed');
    $designationId = Auth::user()->designation_id_old;
    $userId = Auth::user()->id;
    $reports = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
    //print_r( $reports);die;
    if ($designationId == 'Approver') {
      $districtCode = Configduty::where('user_id', $userId)->value('district_code');
      $block = Taluka::where('district_code',  $districtCode)->select('block_name', 'block_code')->get();

      return view('Drilldown.block_application_report', compact('block', 'districtCode', 'reports'));
    } else if ($designationId == 'HOD' || $designationId == 'Dashboard') {
      $districts = District::orderBy('district_name')->get();
      return view('Drilldown.district_application_report', compact('districts', 'reports'));
    }
  }
  public function getGpMuniData(Request $request)
  {
    $statusCode = 200;
    $response = [];
    $benid = $request->benid;

    if (!$request->ajax()) {
      $statusCode = 400;
      $response = array('error' => 'Error occured in form submit.');
      return response()->json($response, $statusCode);
    }
    try {
      $blockId = $request->blockid;
      $rural_urbanid = $request->rural_urbanid;
      if ($rural_urbanid == 1) {
        $data = UrbanBody::where('sub_district_code', $blockId)->select('urban_body_code', 'urban_body_name')->get();
      } else {
        $data = GP::where('block_code', $blockId)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
      }

      $response = array('data' => $data);
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



  public function datatableBlockApplicationReport(Request $request)
  {
    //  dd($request->all());
    $muniid = $request->muniid;
    $districtCode = $request->districtCode;
    $blockid = $request->blockid;
    $gpid = $request->gpid;
    $fromdate = $request->fromdate;
    $todate = $request->todate;
    $dateFromat = 'DD/MM/YYYY';
    $rural_urbanid = $request->rural_urbanid;
    $schemeId = $request->schemeId;
    $schemaname = Scheme::where('id', $schemeId)->value('short_code');
    $query = "";
    // NEW 
    if (!empty($fromdate)) {
      $formArr = explode('/', $fromdate);
      $formMonth = $formArr[1];
    }
    if (!empty($todate)) {
      $toArr = explode('/', $todate);
      $toMonth = $toArr[1];
    }

    if ($rural_urbanid == 1) {
      if ($formMonth == $toMonth) {
        $query = "select urban_body_name as bsm,
            coalesce(total_applicant,0) as total_applicant,
            coalesce(pending_for_action,0) as pending_for_action,
            coalesce(approved,0) as approved,
            coalesce(verified,0) as verified,
            coalesce(rejected,0)as rejected,
            coalesce(faulty,0)as faulty
            
            from m_urban_body bl
            left join
            (
            Select b.block_ulb_code,
           

            sum(case when ds_registration_no is not null then 1 else 0 end) as total_applicant,
            sum(case when next_level_role_id is null then 1 else 0 end) as pending_for_action,
            sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) as verified, 
            sum(case when next_level_role_id=0 then 1 else 0 end) as approved,
            sum(case when is_rejected=1 then 1 else 0 end) as rejected,
            sum(case when next_level_role_id=9999 then 1 else 0 end) as faulty 

            FROM " . $schemaname . ".beneficiary b 
            where ds_phase IS NULL and ds_registration_no is not null  and created_by_dist_code=" . $districtCode;



        if (!empty($blockid)) {
          $query .= " and block_ulb_code=" . $blockid;
        }

        if (!empty($fromdate)) {
          $query .= " and to_char(b.created_at,'" . $dateFromat . "')>='" . $fromdate . "'";
        }
        if (!empty($todate)) {
          $query .= " and to_char(b.created_at,'" . $dateFromat . "')<='" . $todate . "'";
        }
        $query .= "  group by b.block_ulb_code
                )x  on bl.urban_body_code=x.block_ulb_code
                where district_code=" . $districtCode;
      } else {
        $query = "select t.bsm,
              sum(total_applicant) as total_applicant,
              sum(pending_for_action) as pending_for_action,
              sum(approved) as approved,
              sum(verified) as verified,
              sum(rejected)as rejected,
              sum(faulty)as faulty from
            (select urban_body_name as bsm,
            coalesce(total_applicant,0) as total_applicant,
            coalesce(pending_for_action,0) as pending_for_action,
            coalesce(approved,0) as approved,
            coalesce(verified,0) as verified,
            coalesce(rejected,0)as rejected,
            coalesce(faulty,0)as faulty

            from m_urban_body bl
            left join
            (
            Select b.block_ulb_code,


            sum(case when ds_registration_no is not null then 1 else 0 end) as total_applicant,
            sum(case when next_level_role_id is null then 1 else 0 end) as pending_for_action,
            sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) as verified, 
            sum(case when next_level_role_id=0 then 1 else 0 end) as approved,
            sum(case when is_rejected=1 then 1 else 0 end) as rejected,
            sum(case when next_level_role_id=9999 then 1 else 0 end) as faulty 

            FROM " . $schemaname . ".beneficiary b 
            where ds_phase IS NULL and ds_registration_no is not null  and created_by_dist_code=" . $districtCode;
        if (!empty($blockid)) {
          $query .= " and block_ulb_code=" . $blockid;
        }
        if (!empty($fromdate)) {
          $query .= " and to_char(b.created_at,'" . $dateFromat . "')>='" . $fromdate . "'";
        }
        if (!empty($todate)) {
          $query .= " and to_char(b.created_at,'" . $dateFromat . "')<='31/08/2021'";
        }
        //and to_char(b.created_at,'DD/MM/YYYY')>='16/08/2021' and to_char(b.created_at,'DD/MM/YYYY')<='31/08/2021'  
        $query .= " group by b.block_ulb_code
            )x  on bl.urban_body_code=x.block_ulb_code
            where district_code=" . $districtCode . "
            UNION ALL
            select urban_body_name as bsm,
            coalesce(total_applicant,0) as total_applicant,
            coalesce(pending_for_action,0) as pending_for_action,
            coalesce(approved,0) as approved,
            coalesce(verified,0) as verified,
            coalesce(rejected,0)as rejected,
            coalesce(faulty,0)as faulty

            from m_urban_body bl
            left join
            (
            Select b.block_ulb_code,


            sum(case when ds_registration_no is not null then 1 else 0 end) as total_applicant,
            sum(case when next_level_role_id is null then 1 else 0 end) as pending_for_action,
            sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) as verified, 
            sum(case when next_level_role_id=0 then 1 else 0 end) as approved,
            sum(case when is_rejected=1 then 1 else 0 end) as rejected,
            sum(case when next_level_role_id=9999 then 1 else 0 end) as faulty 

            FROM " . $schemaname . ".beneficiary b 
            where ds_phase IS NULL and ds_registration_no is not null  and created_by_dist_code= " . $districtCode;
        // and to_char(b.created_at,'DD/MM/YYYY')>='01/09/2021' and to_char(b.created_at,'DD/MM/YYYY')<='29/09/2021'
        if (!empty($blockid)) {
          $query .= " and block_ulb_code=" . $blockid;
        }
        if (!empty($fromdate)) {
          $query .= " and to_char(b.created_at,'" . $dateFromat . "')>='01/09/2021'";
        }
        if (!empty($todate)) {
          $query .= " and to_char(b.created_at,'" . $dateFromat . "')<='" . $todate . "'";
        }
        $query .= " group by b.block_ulb_code
            )x  on bl.urban_body_code=x.block_ulb_code
            where district_code=" . $districtCode . ") t group by t.bsm";
      }

      // echo $query;die;
    } else {
      if ($formMonth == $toMonth) {
        $query = "select block_name as bsm,
            coalesce(total_applicant,0) as total_applicant,
            coalesce(pending_for_action,0) as pending_for_action,
            coalesce(approved,0) as approved,
            coalesce(verified,0) as verified,
            coalesce(rejected,0)as rejected,
            coalesce(faulty,0)as faulty
            from m_block bl
            left join
            (
            Select b.created_by_local_body_code,

            sum(case when ds_registration_no is not null then 1 else 0 end) as total_applicant,
            sum(case when next_level_role_id is null then 1 else 0 end) as pending_for_action,
            sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) as verified, 
            sum(case when next_level_role_id=0 then 1 else 0 end) as approved,
            sum(case when is_rejected=1 then 1 else 0 end) as rejected,
            sum(case when next_level_role_id=9999 then 1 else 0 end) as faulty 

            FROM " . $schemaname . ".beneficiary b 
            where ds_phase IS NULL and ds_registration_no is not null  and created_by_dist_code=" . $districtCode;




        if (!empty($blockid)) {
          $query .= " and created_by_local_body_code=" . $blockid;
        }
        if (!empty($fromdate)) {
          $query .= " and to_char(b.created_at,'" . $dateFromat . "')>='" . $fromdate . "'";
        }
        if (!empty($todate)) {
          $query .= " and to_char(b.created_at,'" . $dateFromat . "')<='" . $todate . "'";
        }
        $query .= "  group by b.created_by_local_body_code
            )x  on bl.block_code=x.created_by_local_body_code
            where district_code=" . $districtCode;
      } else {
        $query = "select t.bsm,
          sum(total_applicant) as total_applicant,
          sum(pending_for_action) as pending_for_action,
          sum(approved) as approved,
          sum(verified) as verified,
          sum(rejected)as rejected,
          sum(faulty)as faulty from
        (select block_name as bsm,
        coalesce(total_applicant,0) as total_applicant,
        coalesce(pending_for_action,0) as pending_for_action,
        coalesce(approved,0) as approved,
        coalesce(verified,0) as verified,
        coalesce(rejected,0)as rejected,
        coalesce(faulty,0)as faulty
        from m_block bl
        left join
        (
        Select b.created_by_local_body_code,

        sum(case when ds_registration_no is not null then 1 else 0 end) as total_applicant,
        sum(case when next_level_role_id is null then 1 else 0 end) as pending_for_action,
        sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) as verified, 
        sum(case when next_level_role_id=0 then 1 else 0 end) as approved,
        sum(case when is_rejected=1 then 1 else 0 end) as rejected,
        sum(case when next_level_role_id=9999 then 1 else 0 end) as faulty 

        FROM " . $schemaname . ".beneficiary b 
        where ds_phase IS NULL and ds_registration_no is not null  and created_by_dist_code=" . $districtCode;
        // and to_char(b.created_at,'DD/MM/YYYY')>='16/08/2021' and to_char(b.created_at,'DD/MM/YYYY')<='31/08/2021'  
        if (!empty($blockid)) {
          $query .= " and created_by_local_body_code=" . $blockid;
        }
        if (!empty($fromdate)) {
          $query .= " and to_char(b.created_at,'" . $dateFromat . "')>='" . $fromdate . "'";
        }
        if (!empty($todate)) {
          $query .= " and to_char(b.created_at,'" . $dateFromat . "')<='31/08/2021'";
        }
        $query .= " group by b.created_by_local_body_code
        )x  on bl.block_code=x.created_by_local_body_code
        where district_code=" . $districtCode . "
        UNION ALL
        select block_name as bsm,
        coalesce(total_applicant,0) as total_applicant,
        coalesce(pending_for_action,0) as pending_for_action,
        coalesce(approved,0) as approved,
        coalesce(verified,0) as verified,
        coalesce(rejected,0)as rejected,
        coalesce(faulty,0)as faulty
        from m_block bl
        left join
        (
        Select b.created_by_local_body_code,

        sum(case when ds_registration_no is not null then 1 else 0 end) as total_applicant,
        sum(case when next_level_role_id is null then 1 else 0 end) as pending_for_action,
        sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) as verified, 
        sum(case when next_level_role_id=0 then 1 else 0 end) as approved,
        sum(case when is_rejected=1 then 1 else 0 end) as rejected,
        sum(case when next_level_role_id=9999 then 1 else 0 end) as faulty 

        FROM " . $schemaname . ".beneficiary b 
        where ds_phase IS NULL and ds_registration_no is not null  and created_by_dist_code=" . $districtCode;
        // and to_char(b.created_at,'DD/MM/YYYY')>='01/09/2021' and to_char(b.created_at,'DD/MM/YYYY')<='29/09/2021'  
        if (!empty($blockid)) {
          $query .= " and created_by_local_body_code=" . $blockid;
        }
        if (!empty($fromdate)) {
          $query .= " and to_char(b.created_at,'" . $dateFromat . "')>='01/09/2021'";
        }
        if (!empty($todate)) {
          $query .= " and to_char(b.created_at,'" . $dateFromat . "')<='" . $todate . "'";
        }
        $query .= " group by b.created_by_local_body_code
        )x  on bl.block_code=x.created_by_local_body_code
        where district_code=305) t group by t.bsm";
      }
      // echo $query;die;
    }



    $data = DB::connection('pgsql')->select($query);
    //  $filterRecords = count($data);
    return datatables()->of($data)
      // ->setTotalRecords($totalRecords)
      // ->setFilteredRecords($filterRecords)
      ->addColumn('bsm', function ($data) use ($rural_urbanid) {

        return $data->bsm;
      })
      ->addColumn('total_applicant', function ($data) {
        if (empty($data->total_applicant)) {
          return 0;
        }
        return $data->total_applicant;
      })
      ->addColumn('pending_for_action', function ($data) {

        return $data->pending_for_action;
      })
      ->addColumn('verified', function ($data) {

        return $data->verified;
      })

      ->addColumn('rejected', function ($data) {

        return $data->rejected;
      })
      ->addColumn('faulty', function ($data) {

        return $data->faulty;
      })
      ->addColumn('approved', function ($data) {

        return $data->approved;
      })
      //   ->addColumn('total_cum_application', function ($data) {
      //     return intval($data->verified) + intval ($data->rejected) + intval($data->approved)  ;

      // })
      ->rawColumns(['bsm', 'total_applicant', 'verified', 'rejected', 'approved', 'faulty', 'pending_for_action'])
      ->make(true);
  }


  public function datatableDistrictApplicationReport(Request $request)
  {
    // dd($request->all());

    $districtCode = $request->districtid;
    $blockid = $request->blockid;
    $schemeId = $request->schemeId;
    $fromdate = $request->fromdate;
    $todate = $request->todate;

    $schemaname = Scheme::where('id', $schemeId)->value('short_code');
    $dateFromat = 'DD/MM/YYYY';

    // NEW 
    $formArr = explode('/', $fromdate);
    $toArr = explode('/', $todate);
    $formMonth = $formArr[1];
    $toMonth = $toArr[1];
    // print $formMonth[1]; print $toMonth[1]; die();
    $query = '';
    if ($formMonth == $toMonth) {
      $query = "select district_name as district_name,
         coalesce(total_applicant,0) as total_applicant,
         coalesce(pending_for_action,0) as pending_for_action,
         coalesce(approved,0) as approved,
         coalesce(verified,0) as verified,
         coalesce(rejected,0)as rejected,
         coalesce(faulty,0)as faulty
         from m_district bl
         left join
         (
         Select b.created_by_dist_code,
         sum(case when ds_registration_no is not null then 1 else 0 end) as total_applicant,
         sum(case when next_level_role_id is null then 1 else 0 end) as pending_for_action,
         sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) as verified, 
         sum(case when next_level_role_id=0 then 1 else 0 end) as approved,
         sum(case when is_rejected=1 then 1 else 0 end) as rejected,
         sum(case when next_level_role_id=9999 then 1 else 0 end) as faulty 
       
         FROM " . $schemaname . ".beneficiary b 
         where ds_phase IS NULL and ds_registration_no is not null ";


      if (!empty($districtCode)) {
        $query .= " and created_by_dist_code=" . $districtCode;
      }
      // if(!empty($fromdate)){
      //   $fromdate=date('Y-m-d', strtotime(trim(str_replace('/', '-', $fromdate))));
      //   $query .= " and b.created_at>='" . $fromdate . "'";
      // }
      // if(!empty($todate)){
      //   $todate=date('Y-m-d', strtotime(trim(str_replace('/', '-', $todate))));
      //   $query .= " and b.created_at<='" . $todate . "'";
      // }
      if (!empty($fromdate)) {
        $query .= " and to_char(b.created_at,'" . $dateFromat . "')>='" . $fromdate . "'";
      }
      if (!empty($todate)) {
        $query .= " and to_char(b.created_at,'" . $dateFromat . "')<='" . $todate . "'";
      }

      $query .= "  group by b.created_by_dist_code
                )x  on bl.district_code=x.created_by_dist_code";
    } else {
      $query = "select t.district_name,sum(total_applicant) as total_applicant, 
        sum(pending_for_action) as pending_for_action, 
        sum(approved) as approved, sum(verified) as verified, 
        sum(rejected)as rejected, sum(faulty)as faulty from 
        (select district_name as district_name, coalesce(total_applicant,0) as total_applicant, 
        coalesce(pending_for_action,0) as pending_for_action, 
        coalesce(approved,0) as approved, coalesce(verified,0) as verified, 
        coalesce(rejected,0)as rejected, coalesce(faulty,0)as faulty from m_district bl 
        left join ( Select b.created_by_dist_code, sum(case when ds_registration_no is not null then 1 else 0 end) as total_applicant, 
               sum(case when next_level_role_id is null then 1 else 0 end) as pending_for_action, 
               sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) as verified, 
               sum(case when next_level_role_id=0 then 1 else 0 end) as approved, 
               sum(case when is_rejected=1 then 1 else 0 end) as rejected, 
               sum(case when next_level_role_id=9999 then 1 else 0 end) as faulty 
               FROM " . $schemaname . ".beneficiary b where ds_phase IS NULL and ds_registration_no is not null ";
      if (!empty($districtCode)) {
        $query .= " and created_by_dist_code=" . $districtCode;
      }
      if (!empty($fromdate)) {
        $query .= " and to_char(b.created_at,'" . $dateFromat . "')>='" . $fromdate . "'";
      }
      if (!empty($todate)) {
        $query .= " and to_char(b.created_at,'" . $dateFromat . "')<='31/08/2021'";
      }
      //and to_char(b.created_at,'DD/MM/YYYY')>='16/08/2021' 
      //and to_char(b.created_at,'DD/MM/YYYY')<='31/08/2021' 
      $query .= " group by b.created_by_dist_code 
              )x on bl.district_code=x.created_by_dist_code
      
          UNION ALL
          select district_name as district_name, coalesce(total_applicant,0) as total_applicant, 
          coalesce(pending_for_action,0) as pending_for_action, 
          coalesce(approved,0) as approved, coalesce(verified,0) as verified, 
          coalesce(rejected,0)as rejected, coalesce(faulty,0)as faulty from m_district bl 
          left join ( Select b.created_by_dist_code, 
                 sum(case when ds_registration_no is not null then 1 else 0 end) as total_applicant, 
                 sum(case when next_level_role_id is null then 1 else 0 end) as pending_for_action, 
                 sum(case when is_verified=1 and is_approved=0 and is_rejected=0 then 1 else 0 end) as verified, 
                 sum(case when next_level_role_id=0 then 1 else 0 end) as approved, 
                 sum(case when is_rejected=1 then 1 else 0 end) as rejected, 
                 sum(case when next_level_role_id=9999 then 1 else 0 end) as faulty 
                 FROM " . $schemaname . ".beneficiary b where ds_phase IS NULL and ds_registration_no is not null ";
      if (!empty($districtCode)) {
        $query .= " and created_by_dist_code=" . $districtCode;
      }
      if (!empty($fromdate)) {
        $query .= " and to_char(b.created_at,'" . $dateFromat . "')>='01/09/2021'";
      }
      if (!empty($todate)) {
        $query .= " and to_char(b.created_at,'" . $dateFromat . "')<='" . $todate . "'";
      }
      // and to_char(b.created_at,'DD/MM/YYYY')>='01/09/2021' 
      // and to_char(b.created_at,'DD/MM/YYYY')<='29/09/2021' 
      $query .= " group by b.created_by_dist_code 
                )x on bl.district_code=x.created_by_dist_code) t group by t.district_name";
    }




    // echo $query;die;
    $data = DB::connection('pgsql')->select($query);

    //  $filterRecords = count($data);
    return datatables()->of($data)
      // ->setTotalRecords($totalRecords)
      // ->setFilteredRecords($filterRecords)
      ->addColumn('district_name', function ($data) {
        //$action = '<a class="block_button" value=' . $data->district_code . '></a>';
        return $data->district_name;
      })
      ->addColumn('pending_for_action', function ($data) {

        return $data->pending_for_action;
      })
      ->addColumn('total_applicant', function ($data) {

        return $data->total_applicant;
      })
      ->addColumn('verified', function ($data) {

        return $data->verified;
      })
      ->addColumn('rejected', function ($data) {

        return $data->rejected;
      })
      ->addColumn('approved', function ($data) {

        return $data->approved;
      })->addColumn('faulty', function ($data) {

        return $data->faulty;
      })->rawColumns(['district_name', 'pending_for_action', 'total_applicant', 'verified', 'rejected', 'approved', 'faulty'])
      ->make(true);
  }



  public function shemeSessionCheck(Request $request)
  {
    $scheme_id = 0;

    if ($request->get('pr1')) {
      if ($request->get('pr1') == "lb_wcd") {
        $scheme_id = 20;
      } else {
        return redirect("/")->with('error', ' Parameter Invalid');
      }
    } else {
      return redirect("/")->with('error', 'Method is not valid');
    }

    $is_active = 0;
    $roleArray = $request->session()->get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        $request->session()->put('level', $roleObj['mapping_level']);
        $distCode = $roleObj['district_code'];
        $request->session()->put('distCode', $roleObj['district_code']);
        $request->session()->put('scheme_id', $scheme_id);
        $request->session()->put('is_first', $roleObj['is_first']);
        $request->session()->put('is_urban', $roleObj['is_urban']);
        $request->session()->put('role_id', $roleObj['id']);
        if ($roleObj['is_urban'] == 1) {
          $request->session()->put('bodyCode', $roleObj['urban_body_code']);
        } else {
          $request->session()->put('bodyCode', $roleObj['taluka_code']);
        }
        break;
      }
    }
    if ($is_active == 1) {

      //  $ben_table = 'dist_' . $distCode . '.beneficiary';
      return true;
    } else {
      return false;
    }
  }
  function dsReport(Request $request)
  {
    $phase_code = $request->phase_code;
    
    if (empty($phase_code)) {
      return redirect("/dsReportCommon")->with('error', 'Phase Code Not Found');
    }
    if (!ctype_digit($phase_code)) {
      return redirect("/dsReportCommon")->with('error', 'Phase Code  InValid');
    }
    $phase_arr = DsPhase::where('phase_code', $phase_code)->first();
    if (empty($phase_arr)) {
      return redirect("/dsReportCommon")->with('error', 'Phase Code  InValid');
    }
    $designationId = Auth::user()->designation_id_old;
    $userId = Auth::user()->id;
    $sceme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (1,2,3) and id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
    $base_date = $phase_arr->base_date;
    date_default_timezone_set('Asia/Kolkata');
    $c_time = Carbon::now();
    $c_date = $c_time->format("Y-m-d");
    $is_active = 0;
    $roleArray = $request->session()->get('role');
    $designation_id_old = Auth::user()->designation_id_old;
    $district_visible = $is_urban_visible = $block_visible = 1;
    $municipality_visible = 0;
    $gp_ward_visible = 0;
    $muncList = collect([]);
    $gpList = collect([]);
    if ($designation_id_old == 'Admin' || $designation_id_old == 'HOD' ||  $designation_id_old == 'Dashboard') {
      $district_visible = $is_urban_visible = $block_visible = 1;
    } else if ($designation_id_old == 'Approver' || $designation_id_old == 'StatusCheckerDistrict') {
      $district_code = NULL;
      $is_urban = NULL;
      $blockCode = NULL;
      foreach ($roleArray as $roleObj) {
        if (in_array($roleObj['scheme_id'], array(1, 2, 3))) {
          $is_urban = $roleObj['is_urban'];
          $district_code = $roleObj['district_code'];
          if ($roleObj['is_urban'] == 1) {
            $blockCode = $roleObj['urban_body_code'];
            $muncList = UrbanBody::select('urban_body_code', 'urban_body_name')->where('sub_district_code', $blockCode)->get();
            $municipality_visible = 1;
          } else {
            $blockCode = $roleObj['taluka_code'];
            $gpList = GP::select('gram_panchyat_code', 'gram_panchyat_name')->where('block_code', $blockCode)->get();
          }
          break;
        }
      }

      if (empty($district_code))
        return redirect("/")->with('success', 'User Disabled. ');
    } else {
      return redirect("/")->with('success', 'User Disabled. ');
    }
    //dd($district_code);
    if (!empty($district_code)) {
      $district_visible = 0;
      $district_code_fk = $district_code;
    } else {
      $district_code_fk = NULL;
    }
    if (!empty($is_urban)) {
      $is_urban_visible = 0;
      $rural_urban_fk = $is_urban;
    } else {
      $rural_urban_fk = NULL;
    }
    if (!empty($blockCode)) {
      $block_visible = 0;
      $block_munc_corp_code_fk = $blockCode;
      $gp_ward_visible = 1;
    } else {
      $block_munc_corp_code_fk = NULL;
      $gp_ward_visible = 0;
    }
    $districts = District::get();

    return view(
      'DsReport.index',
      [
        'sceme_list' => $sceme_list,
        'selected_phase' =>  $phase_code,
        'districts' => $districts,
        'district_visible' => $district_visible,
        'district_code_fk' => $district_code_fk,
        'is_urban_visible' => $is_urban_visible,
        'rural_urban_fk' => $rural_urban_fk,
        'block_visible' => $block_visible,
        'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
        'municipality_visible' => $municipality_visible,
        'gp_ward_visible' => $gp_ward_visible,
        'is_urban_visible' => $is_urban_visible,
        'base_date' => $base_date,
        'c_date' => $c_date,
        'gpList' => $gpList,
        'muncList' => $muncList,
        'phase_arr' => $phase_arr,
      ]
    );
  }
  public function getData(Request $request)
  {
    $scheme_id = $request->scheme_id;
    $ds_phase = $request->ds_phase;
    if (empty($ds_phase)) {
      $ds_phase = $this->selected_phase;
    }
    //dd($ds_phase);
    $phase_arr = DsPhase::where('phase_code', $ds_phase)->first();
    $district = $request->district;
    $urban_code = $request->urban_code;
    $block = $request->block;
    $muncid = $request->muncid;
    $gp_ward = $request->gp_ward;
    // dd($gp_ward);
    $caste = $request->caste_category;
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    $base_date = $phase_arr->base_date;
   // dd( $base_date);
    date_default_timezone_set('Asia/Kolkata');
    $c_time = Carbon::now();
    $c_date = $c_time->format("Y-m-d");
    $heading_msg = '';
    $title = "";
    //$block_condition = "";
    if (!empty($district)) {
      $district_row = District::where('district_code', $district)->first();
    }

    if (!empty($block)) {

      if ($urban_code == 1) {
        $block_ulb = SubDistrict::where('sub_district_code', '=', $block)->first();
        $blk_munc_name = $block_ulb->sub_district_name;
        //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
      } else {
        $block_ulb = Taluka::where('block_code', '=', $block)->first();
        $blk_munc_name = $block_ulb->block_name;
        // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
      }
    } else {
      // $block_condition = "";
    }
    if (!empty($gp_ward)) {

      if ($urban_code == 1) {
        $gp_ward_row = Ward::where('urban_body_ward_code', '=', $gp_ward)->first();
        $gp_ward_name = $gp_ward_row->urban_body_ward_name;
        //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
      } else {
        $gp_ward_row = GP::where('gram_panchyat_code', '=', $gp_ward)->first();
        $gp_ward_name = $gp_ward_row->gram_panchyat_name;
        // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
      }
    }
    $rules = [
      'scheme_id' => 'required|integer',
      'ds_phase' => 'required|integer',
      'district' => 'nullable|integer',
      'urban_code' => 'nullable|integer',
      'block' => 'nullable|integer',
      'muncid' => 'nullable|integer',
      'gp_ward' => 'nullable|integer',
      'from_date'    => 'required|date|after_or_equal:' . $base_date . '|before_or_equal:' . $c_date,
      'to_date'      => 'required|date|after_or_equal:from_date|before_or_equal:' . $c_date,
    ];
    $data = array();
    $column = "";
    $attributes = array();
    $messages = array();
    $attributes['scheme_id'] = 'Scheme';
    $attributes['ds_phase'] = 'Phase';
    $attributes['district'] = 'District';
    $attributes['urban_code'] = 'Rural/ Urban';
    $attributes['block'] = 'Block/Sub Division';
    $attributes['muncid'] = 'Municipality';
    $attributes['gp_ward'] = 'GP/Ward';
    $attributes['from_date'] = 'From Date';
    $attributes['to_date'] = 'To Date';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $user_msg = "Duare Sarkar Report of Phase " . $ds_phase;
      $scheme_id = $request->scheme_id;
      $scheme_row = Scheme::where('id', $scheme_id)->first();
      $title = $user_msg;
      $data = array();
      $return_status = 1;
      $return_msg = '';
      $heading_msg = '';

      if (!empty($gp_ward)) {
        if ($urban_code == 1) {
          $column = "Ward";
          $heading_msg =  $user_msg . ' of the Ward ' . $gp_ward_name;
          $data = $this->getWardWise($ds_phase, $district, $block, $muncid, $gp_ward, $from_date, $to_date, $scheme_row->short_code);
        } else {
          $column = "GP";
          $heading_msg =  $user_msg . ' of the GP ' . $gp_ward_name;
          $data = $this->getGpWise($ds_phase, $district, $block, NULL, $gp_ward, $from_date, $to_date, $scheme_row->short_code);
        }
      } else if (!empty($muncid)) {
        $column = "Ward";
        $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
        $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
        $data = $this->getWardWise($ds_phase, $district, $block, $muncid, NULL, $from_date, $to_date, $scheme_row->short_code);
      } else if (!empty($block)) {
        if ($urban_code == 1) {
          $column = "Municipality";
          $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
          $data = $this->getMuncWise($ds_phase, $district, $block, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);
        } else if ($urban_code == 2) {
          $block_arr = Taluka::where('block_code', '=', $block)->first();
          $column = "GP";
          $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
          $data = $this->getGpWise($ds_phase, $district, $block, NULL, $gp_ward, $from_date, $to_date, $scheme_row->short_code);
        }
      } else {

        if (!empty($district)) {
          if ($urban_code == 1) {
            $column = "Sub Division";
            $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $data = $this->getSubDivWise($ds_phase, $district, NULL, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);
          } else if ($urban_code == 2) {
            $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block";
            $data = $this->getBlockWise($ds_phase, $district, NULL, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);
          } else {
            $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
            $column = "Block/Sub Division";
            $data1 = $this->getBlockWise($ds_phase, $district, NULL, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);
            $data2 = $this->getSubDivWise($ds_phase, $district, NULL, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);
            $data = array_merge($data1, $data2);
          }
        } else {
          $column = "District";
          $heading_msg = 'District Wise ' . $user_msg;
          $data = $this->getDistrictWise($ds_phase, NULL, NULL, NULL, NULL, $from_date, $to_date, $scheme_row->short_code);

          $external = 0;
        }
      }
      if (!empty($scheme_id)) {
        $heading_msg = $heading_msg . " for the Scheme  " . $scheme_row->scheme_name;
      }
      if (!empty($from_date)) {
        $form_date_formatted = \Carbon\Carbon::parse($from_date)->format('d-m-Y');
        $heading_msg = $heading_msg . " from " . $form_date_formatted;
      }
      if (!empty($to_date)) {
        $to_date_formatted = \Carbon\Carbon::parse($to_date)->format('d-m-Y');
        $heading_msg = $heading_msg . " to  " . $to_date_formatted;
      }
    } else {
      $return_status = 0;
      $return_msg = $validator->errors()->all();
    }
    return response()->json([
      'return_status' => $return_status,
      'return_msg' => $return_msg,
      'row_data' => $data,
      'column' => $column,
      'title' => $title,
      'heading_msg' => $heading_msg
    ]);
  }

  public function getGpWise($ds_phase = NULL, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $scheme)
  {
    $phase_arr = DsPhase::where('phase_code', $ds_phase)->first();
    $base_date  = $phase_arr->base_date;
    $whereCon = "where ds_phase=" . $ds_phase . " and A.created_by_dist_code=" . $district_code;
    $whereCon .= " and date(A.created_at)>='" . $base_date . "'";
    $whereCon .= " and A.created_by_local_body_code=" . $ulb_code;
    $whereMain = "where  district_code=" . $district_code;
    $whereMain .= " and block_code=" . $ulb_code;

    if (!empty($gp_ward_code)) {
      $whereCon .= " and gp_ward_code=" . $gp_ward_code;
      $whereMain .= " and gram_panchyat_code=" . $gp_ward_code;
    }
    if (!empty($fromdate)) {
      $whereCon .= " and date(A.created_at)>='" . $fromdate . "'";
    }
    if (!empty($todate)) {
      $whereCon .= " and date(A.created_at)<='" . $todate . "'";
    }
    if (!empty($caste)) {
      $whereCon .= " and A.caste='" . $caste . "'";
    }
    $query = "select main.location_id,main.location_name,
    COALESCE(draft.total_applicant,0) as total_applicant,
    COALESCE(draft.pending_for_action,0) as pending_for_action,
    COALESCE(draft.verified,0) as verified,
    COALESCE(draft.approved,0) as approved,
    COALESCE(draft.rejected,0) as rejected,
    COALESCE(draft.faulty,0) as faulty
      from
      (
      select gram_panchyat_code as location_id,gram_panchyat_name as location_name
      from public.m_gp  " . $whereMain . "
      ) as main LEFT JOIN
      (
        select count(1) filter(where ds_registration_no IS NOT NULL) as total_applicant,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id is null) as pending_for_action,
        count(1) filter(where ds_registration_no IS NOT NULL and is_verified=1 and is_approved=0 and is_rejected=0) as verified,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=0) as approved,
        count(1) filter(where ds_registration_no IS NOT NULL and is_rejected=1) as rejected,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=9999) as faulty,
        gp_ward_code
       from " . $scheme . ".beneficiary as A 
      " . $whereCon . " 
      group by gp_ward_code
      ) as draft ON main.location_id=draft.gp_ward_code order by main.location_name";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getMuncWise($ds_phase = NULL, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $scheme)
  {
    $phase_arr = DsPhase::where('phase_code', $ds_phase)->first();
    $base_date  = $phase_arr->base_date;
    $whereCon = "where ds_phase=" . $ds_phase . " and A.created_by_dist_code=" . $district_code;
    $whereCon .= " and date(A.created_at)>='" . $base_date . "'";
    $whereCon .= " and A.created_by_local_body_code=" . $ulb_code;
    $whereMain = "where  district_code=" . $district_code;
    $whereMain .= " and sub_district_code=" . $ulb_code;
    if (!empty($fromdate)) {
      $whereCon .= " and date(A.created_at)>='" . $fromdate . "'";
    }
    if (!empty($todate)) {
      $whereCon .= " and date(A.created_at)<='" . $todate . "'";
    }
    if (!empty($caste)) {
      $whereCon .= " and A.caste='" . $caste . "'";
    }
    $query = "select main.location_id,main.location_name,
    COALESCE(draft.total_applicant,0) as total_applicant,
    COALESCE(draft.pending_for_action,0) as pending_for_action,
    COALESCE(draft.verified,0) as verified,
    COALESCE(draft.approved,0) as approved,
    COALESCE(draft.rejected,0) as rejected,
    COALESCE(draft.faulty,0) as faulty
      from
      (
      select urban_body_code as location_id,urban_body_name as location_name
      from public.m_urban_body  " . $whereMain . "
      ) as main LEFT JOIN
      (
        select count(1) filter(where ds_registration_no IS NOT NULL) as total_applicant,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id is null) as pending_for_action,
        count(1) filter(where ds_registration_no IS NOT NULL and is_verified=1 and is_approved=0 and is_rejected=0) as verified,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=0) as approved,
        count(1) filter(where ds_registration_no IS NOT NULL and is_rejected=1) as rejected,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=9999) as faulty,
       block_ulb_code
      from " . $scheme . ".beneficiary as A 
      " . $whereCon . " 
      group by block_ulb_code
      ) as draft ON main.location_id=draft.block_ulb_code  order by main.location_name";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getBlockWise($ds_phase = NULL, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $scheme)
  {
    $phase_arr = DsPhase::where('phase_code', $ds_phase)->first();
    $base_date  = $phase_arr->base_date;
    $whereCon = "where ds_phase=" . $ds_phase . " and A.created_by_dist_code=" . $district_code;
    $whereCon .= " and date(A.created_at)>='" . $base_date . "'";
    $whereMain = "where  district_code=" . $district_code;
    if (!empty($fromdate)) {
      $whereCon .= " and date(A.created_at)>='" . $fromdate . "'";
    }
    if (!empty($todate)) {
      $whereCon .= " and date(A.created_at)<='" . $todate . "'";
    }

    $query = "select main.location_id,main.location_name||'-Block' as location_name,
      COALESCE(draft.total_applicant,0) as total_applicant,
      COALESCE(draft.pending_for_action,0) as pending_for_action,
      COALESCE(draft.verified,0) as verified,
      COALESCE(draft.approved,0) as approved,
      COALESCE(draft.rejected,0) as rejected,
      COALESCE(draft.faulty,0) as faulty
      from
      (
      select block_code as location_id,block_name as location_name
      from public.m_block  " . $whereMain . "
      ) as main LEFT JOIN
      (
      select count(1) filter(where ds_registration_no IS NOT NULL) as total_applicant,
      count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id is null) as pending_for_action,
      count(1) filter(where ds_registration_no IS NOT NULL and is_verified=1 and is_approved=0 and is_rejected=0) as verified,
      count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=0) as approved,
      count(1) filter(where ds_registration_no IS NOT NULL and is_rejected=1) as rejected,
      count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=9999) as faulty,
      created_by_local_body_code
      from " . $scheme . ".beneficiary as A 
        " . $whereCon . "  group by A.created_by_local_body_code
      ) as draft ON main.location_id=draft.created_by_local_body_code
       order by main.location_name";

      
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getSubDivWise($ds_phase = NULL, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $scheme)
  {
    $phase_arr = DsPhase::where('phase_code', $ds_phase)->first();
    $base_date  = $phase_arr->base_date;
    $whereCon = "where ds_phase=" . $ds_phase . " and A.created_by_dist_code=" . $district_code;
    $whereCon .= " and date(A.created_at)>='" . $base_date . "'";
    $whereMain = "where  district_code=" . $district_code;
    if (!empty($fromdate)) {
      $whereCon .= " and date(A.created_at)>='" . $fromdate . "'";
    }
    if (!empty($todate)) {
      $whereCon .= " and date(A.created_at)<='" . $todate . "'";
    }
    if (!empty($caste)) {
      $whereCon .= " and A.caste='" . $caste . "'";
    }
    $query = "select main.location_id,main.location_name||'-SubDivision' as location_name,
    COALESCE(draft.total_applicant,0) as total_applicant,
    COALESCE(draft.pending_for_action,0) as pending_for_action,
    COALESCE(draft.verified,0) as verified,
    COALESCE(draft.approved,0) as approved,
    COALESCE(draft.rejected,0) as rejected,
    COALESCE(draft.faulty,0) as faulty
    from
    (
    select sub_district_code as location_id,sub_district_name as location_name
    from public.m_sub_district  " . $whereMain . " 
    ) as main LEFT JOIN
    (
      select count(1) filter(where ds_registration_no IS NOT NULL) as total_applicant,
      count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id is null) as pending_for_action,
      count(1) filter(where ds_registration_no IS NOT NULL and is_verified=1 and is_approved=0 and is_rejected=0) as verified,
      count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=0) as approved,
      count(1) filter(where ds_registration_no IS NOT NULL and is_rejected=1) as rejected,
      count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=9999) as faulty,
      created_by_local_body_code
      from " . $scheme . ".beneficiary as A 
        " . $whereCon . "  group by A.created_by_local_body_code
    ) as draft ON main.location_id=draft.created_by_local_body_code
     order by main.location_name";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  public function getDistrictWise($ds_phase = NULL, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $fromdate = NULL, $todate = NULL, $scheme)
  {
    $phase_arr = DsPhase::where('phase_code', $ds_phase)->first();
    $base_date  = $phase_arr->base_date;
    $whereCon = "where ds_phase=" . $ds_phase;
    $whereCon .= " and date(A.created_at)>='" . $base_date . "'";
    if (!empty($fromdate)) {
      $whereCon .= " and date(A.created_at)>='" . $fromdate . "'";
    }
    if (!empty($todate)) {
      $whereCon .= " and date(A.created_at)<='" . $todate . "'";
    }
    if (!empty($caste)) {
      $whereCon .= " and A.caste='" . $caste . "'";
    }
    $query = "select main.location_id,main.location_name,
    COALESCE(draft.total_applicant,0) as total_applicant,
    COALESCE(draft.pending_for_action,0) as pending_for_action,
    COALESCE(draft.verified,0) as verified,
    COALESCE(draft.approved,0) as approved,
    COALESCE(draft.rejected,0) as rejected,
    COALESCE(draft.faulty,0) as faulty
      from
      (
      select district_code as location_id,district_name as location_name
      from public.m_district  
      ) as main LEFT JOIN
      (
        select count(1) filter(where ds_registration_no IS NOT NULL) as total_applicant,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id is null) as pending_for_action,
        count(1) filter(where ds_registration_no IS NOT NULL and is_verified=1 and is_approved=0 and is_rejected=0) as verified,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=0) as approved,
        count(1) filter(where ds_registration_no IS NOT NULL and is_rejected=1) as rejected,
        count(1) filter(where ds_registration_no IS NOT NULL and next_level_role_id=9999) as faulty,
        created_by_dist_code
      from " . $scheme . ".beneficiary as A  " . $whereCon . "
      group by A.created_by_dist_code
      ) as draft ON main.location_id=draft.created_by_dist_code order by main.location_name";
    $result = DB::connection('pgsql_mis')->select($query);
    return $result;
  }
  function dsReportCommon(Request $request)
  {
    //return redirect('/')->with('error', 'Not Allowed');
    $designationId = Auth::user()->designation_id_old;
    $userId = Auth::user()->id;
    // $phase_list = array('2' => 'Phase II', '3' => 'Phase III');
    $phase_list = DsPhase::where('phase_code', '>=', 2)->get();
    //dd($phase_list->toArray());
    return view(
      'DsReport.dsReportCommon',
      [
        'phase_list' => $phase_list
      ]
    );
  }
  function dsReportCommonPost(Request $request)
  {
    $phase_code = $request->phase_code;
    if (empty($phase_code)) {
      return redirect("/dsReportCommon")->with('error', 'Phase Code Not Found');
    }
    if (!ctype_digit($phase_code)) {
      return redirect("/dsReportCommon")->with('error', 'Phase Code  InValid');
    }
    $phase_arr = DsPhase::where('phase_code', $phase_code)->first();
    if (empty($phase_arr)) {
      return redirect("/dsReportCommon")->with('error', 'Phase Code  InValid');
    }
    if ($phase_code == 2) {
      return redirect("/getDistrictApplicationReport");
    } else {
      return redirect("/dsReport?phase_code=".$phase_code);
    }
  }
}
