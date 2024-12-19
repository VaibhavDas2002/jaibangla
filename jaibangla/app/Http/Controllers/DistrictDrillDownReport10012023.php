<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Taluka;
use App\District;
use App\BeneficiaryPensions;
use Auth;
use App\Configduty;
use App\Scheme;
//sayantika 21-03-2020
use App\UrbanBody;
use Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DistrictDrillDownReport extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(200);
  }
  public function index()
  {


    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id', '=', $user_id)->first();
    $schemes = Scheme::where('is_active', 1)->get(['scheme_name as name', 'id as id']);
    //$districts = District::all();
    //$district_code=$duty->district_code;

    //$district_name=District::where('district_code',$district_code)->pluck('district_name')->first();





    //$is_active = $duty->is_active;
    //dd($is_active);
    //if($is_active==1){


    return view('District-Drilldown.index')->with('schemes', $schemes);
    //->with('district_name',$district_name);

    // }if($is_active==0){

    //return redirect("/")->with('success', 'User Disabled');
    //}

  }


  public function getdata(Request $request)
  {
    DB::enableQueryLog();
    $user_id = AuthChecker::getUserId();
    //$duty = Configduty::where('user_id','=',$user_id)->first();


    //$district_code=$duty->district_code;

    $columns = array(
      0 => 'district_name',
      1 => 'applications_submitted',
      2 => 'approval_pending',
      3 => 'approved',

    );

    $scheme_id = $request->level1a;

    $flag = 1;
    $constraints = [
      'level1a' => $request['level1a'],

    ];



    //sayantika 21-03-2020
    $totalData = District::leftJoin(DB::raw("(select dist_code,scheme_id,SUM(application_submitted)
                    as application_submitted,SUM(application_verified)as application_verified ,
                    SUM(application_approved) as application_approved from pension.pension_statistics as pension_statistics where scheme_id=? group by dist_code,scheme_id)t"), 'm_district.district_code', '=', 't.dist_code')->addBinding($scheme_id, 'select')
      ->select('district_name', 'district_code', 'application_submitted', 'application_verified', 'application_approved', 'scheme_id')
      ->get()
      ->count();
    //dd($totalData);


    $totalFiltered = $totalData;
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');


    if (empty($request->input('search.value'))) {

      //sayantika 21-03-2020
      $posts = District::leftJoin(DB::raw("(select dist_code,scheme_id,SUM(application_submitted)as application_submitted,SUM(application_verified)as application_verified ,
                SUM(application_approved) as application_approved from pension.pension_statistics as pension_statistics where scheme_id=? group by dist_code,scheme_id)t"), 'm_district.district_code', '=', 't.dist_code')->addBinding($scheme_id, 'select')
        ->limit($limit)->orderBy($order, $dir)
        ->select('district_name', 'district_code', 'application_submitted', 'application_verified', 'application_approved', 'scheme_id')
        ->get();


      //dd(DB::getQueryLog(),$posts);


    }
    $data = array();
    if (!empty($posts)) { //dd($posts);
      /*  foreach ($posts as $post)
            {
                //$show =  route('nhmemployee.showSingleEmployeeUpdatePosting',$post->id);
                //$edit =  route('posts.edit',$post->id);
                $district_code=$post->district_code;
                $scheme_id=$post->scheme_id;

                $app_submitted=route('district-drill-down-submiited',[$district_code,$scheme_id]);
                $app_verified=route('district-drill-down-verified',[$district_code,$scheme_id]);
                $app_approved=route('district-drill-down-approved',[$district_code,$scheme_id]);

                $app_district=route('district-drill-down-district',[$district_code]);

                if($post->application_submitted==null){
                $application_submitted=0;
                  $nestedData['application_submitted'] ="{$application_submitted}";
                }else{
                  $nestedData['application_submitted'] ="<a href='{$app_submitted}'>$post->application_submitted</a>";  //
                }
                
                if($post->application_verified==null){
                  $application_verified=0;
                  $nestedData['application_verified'] = "{$application_verified}";
                }else{
                  $nestedData['application_verified'] = "<a href='{$app_verified}'>$post->application_verified</a>";
                }
                
                if($post->application_approved==null){
                  $application_approved=0;
                   $nestedData['application_approved'] = "{$application_approved}";

                }else{
                  $nestedData['application_approved'] = "<a href='{$app_approved}'>$post->application_approved</a>";
                }
                

                if($request->level1a==null){
                   $district_name=$post->district_name;
                   $nestedData['district_name'] = "{$district_name}";
                }else{
                  $nestedData['district_name'] = "<a href='{$app_district}'>$post->district_name</a>";//$post->district_name;
                }
               
               
               
                
                

               
                $data[] = $nestedData;

            } */
      //dd($data);
    }

    $json_data = array(
      "draw"            => intval($request->input('draw')),
      "recordsTotal"    => intval($totalData),
      "recordsFiltered" => intval($totalFiltered),
      "data"            => $data
    );

    echo json_encode($json_data);
  }

  public function convertdata($search)
  {
    $converted = (int)$search;
    return $converted;
  }

  public function getlistsubmitted($district_code, $scheme_id)
  {

    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id', '=', $user_id)->first();

    //$district_code=$duty->district_code;
    $district_name = District::where('district_code', '=', $district_code)->pluck('district_name')->first();
    //$block_name=Taluka::where('block_code',$block_code)->pluck('block_name')->first();



    $results = BeneficiaryPensions::where('created_by_dist_code', $district_code)
      ->where('scheme_id', $scheme_id)
      ->orderby('pension.beneficiary.id')->get();

    return view(
      'District-Drilldown.linelisting',
      [
        'results' => $results,
      ]
    )->with('level1a', $scheme_id)
      ->with('district_name', $district_name)
      //->with('block_name',$block_name)
      ->with('message', 'Applications Submitted');
  }


  public function getlistapproved($district_code, $scheme_id)
  {

    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id', '=', $user_id)->first();

    //$district_code=$duty->district_code;
    $district_name = District::where('district_code', '=', $district_code)->pluck('district_name')->first();
    //$block_name=Taluka::where('block_code',$block_code)->pluck('block_name')->first();



    $results = BeneficiaryPensions::where('created_by_dist_code', $district_code)
      ->where('scheme_id', $scheme_id)->where('next_level_role_id', 0)
      ->orderby('pension.beneficiary.id')->get();


    return view(
      'District-Drilldown.linelisting',
      [
        'results' => $results,
      ]
    )->with('level1a', $scheme_id)
      ->with('district_name', $district_name)
      // ->with('block_name',$block_name)
      ->with('message', 'Applications Approved');
  }


  public function getlistverified($district_code, $scheme_id)
  {

    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id', '=', $user_id)->first();

    //$district_code=$duty->district_code;
    $district_name = District::where('district_code', '=', $district_code)->pluck('district_name')->first();
    //$block_name=Taluka::where('block_code',$block_code)->pluck('block_name')->first();



    $results = BeneficiaryPensions::where('created_by_dist_code', $district_code)
      ->where('scheme_id', $scheme_id)->where('next_level_role_id', '>', 0)
      ->get();

    return view(
      'District-Drilldown.linelisting',
      [
        'results' => $results,
      ]
    )->with('level1a', $scheme_id)
      ->with('district_name', $district_name)
      //->with('block_name',$block_name)
      ->with('message', 'Applications Verified');
  }



  public function getblocklist($district_code)
  {

    //return redirect('block-drill-down')->;
    return Redirect::route('block-drill-down-dist', [$district_code]);
  }

  public function showSingleEmployeeReport($id, $s_id)
  {
    //if(Auth::user()->designation_id_old == 'Dashboardviewer'){
    if ($s_id == 3) {
      $ben_table = "PensionSc";
    } else if ($s_id == 1) {
      $ben_table = "PensionSt";
    } elseif ($s_id == 2) {
      $ben_table = "Manabik";
    }
    $user_id = AuthChecker::getUserId();

    $appPrefix = "App";
    $modelName = $appPrefix . "\\" . $ben_table;
    $single_employee_details = $modelName::where('id', '=', $id)->first();

    return view('pension_view_details_display', ['row' => $single_employee_details]);
    //}

  }


  //Payment Drill Down
  public function payment($type)
  {

    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id', '=', $user_id)->first();
    $schemes = Scheme::where('is_active', 1)->get(['scheme_name as name', 'id as id']);

    return view('District-Drilldown.payment')->with('type', $type)->with('schemes', $schemes);
  }

  public function getpaymentdata(Request $request)
  {
    //DB::enableQueryLog();
    if (request()->ajax()) {
      $user_id = AuthChecker::getUserId();
      $scheme_id = $request->level1a;
      $type = $request->type; // TYPE RBI of IFMS

      $flag = 1;
      $constraints = [
        'level1a' => $request['level1a'],
      ];
      $parameter = array();
      if (!is_null($scheme_id)) {
        $parameter['scheme_id'] = $scheme_id;
      }
      $lot_generated = "-1";
      $bank_edited = "1";
      if ($type == 'RBI') {
        $lot_generated = "-2";
        // $bank_edited = "1";
      }
      if ($type == 'SBI') {
        $lot_generated = "-3";
      }
      $parameter['lot_generated'] = $lot_generated;
      $parameter['bank_edited'] = $bank_edited;

      $table_name = 'pension.beneficiary';
      if ($scheme_id == 1) {
        $table_name = 'johar.beneficiary';
      } else if ($scheme_id == 2) {
        $table_name = 'manabik.beneficiary';
      } else if ($scheme_id == 3) {
        $table_name = 'bandhu.beneficiary';
      }

      $query = "select d.district_code,d.district_name, b.type, b.failed, b.rectified from m_district d
        left join 
        (select dist_code, coalesce(count(id) FILTER( where lot_generated=:lot_generated and next_level_role_id=0),0) as failed,
        coalesce(count(id) FILTER(  where bank_edited=:bank_edited and lot_generated=:lot_generated and next_level_role_id=0),0) as rectified, '" .
        $type . "' as type
        from " . $table_name .
        ' group by dist_code';
      if (!is_null($scheme_id)) {
        $query = $query . ', scheme_id having scheme_id=:scheme_id';
      }
      $query = $query . ') b
        on d.district_code=b.dist_code
        order by d.district_name';

      $data = DB::connection('pgsql_mis')->select($query, $parameter);

      return datatables()->of($data)
        ->addColumn('district_name', function ($data) {
          //if($data->district_name!=Null){
          return '<a href="' . route('district-drill-down-payment-district', [$data->district_code, $data->type]) . '">' . $data->district_name . '</a>';
          //return $data->district_name;
          // }
          // else{
          //   return 0;
          // }
        })
        ->addColumn('failed', function ($data) {
          // if($data->failed!=Null){
          //return '<a href="'. route('block-drill-down-payment-submiited', [$data->block_code,$data->scheme_id]) .'">'.$data->application_submitted.'</a>';
          return $data->failed;
          // }
          // else{
          //   return 0;
          // }
        })
        ->addColumn('rectified', function ($data) {
          // if($data->rectified!=Null){
          //return '<a href="'. route('block-drill-down-payment-verified', [$data->block_code,$data->scheme_id]) .'">'.$data->application_verified.'</a>';
          return $data->rectified;
          // }else{
          //     return 0;
          //   }
        })
        ->addColumn('pending', function ($data) {
          //  if($data->rectified!=Null){
          //return '<a href="'. route('block-drill-down-payment-verified', [$data->block_code,$data->scheme_id]) .'">'.$data->application_verified.'</a>';
          return $data->failed - $data->rectified;
          // }else{
          //     return 0;
          //   }
        })
        ->rawColumns(['district_name', 'failed', 'rectified', 'pending'])
        ->make(true);
    }
    return view('District-Drilldown.payment')->with('schemes', $schemes)->with('type', $type);
  }

  public function getblockpaymentlist($district_code, $type)
  {

    //return redirect('block-drill-down')->;
    return Redirect::route('block-drill-down-payment-dist', [$district_code, $type]);
  }


  //Consolidated Report Districtwise
  public function consol_report()
  {
    $c_time = Carbon::now();
    $year = $c_time->year;
    if ($c_time->month == 1 || $c_time->month == 2 || $c_time->month == 3) {
      $first_part = $year - 1;
      $second_part = $year;
    } else {
      $first_part = $year;
      $second_part = $year + 1;
    }
    $select_year = $first_part . '-' . $second_part;
    $monthName = $c_time->format('F');
    //dd($monthName);
    //dd($select_year);
    $user_id = AuthChecker::getUserId();
    $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
    return view('District-Drilldown.district_consolidate_report')->with('schemes', $schemes)->with('selected_year', $select_year)->with('selected_month', $monthName);
  }

  public function getconsol_reportData(Request $request)
  {
    $schemes = array();
    if (request()->ajax()) {
      $user_id = AuthChecker::getUserId();
      $scheme_id = $request->scheme_id;
      $year = $request->fin_year;
      $month = $request->month;
      $payment_option = $request->payment_option;
      $tld_table = 'transaction_lot_details';
      if ($payment_option == 2) {
        $tld_table = 'transaction_lot_details_report';
      }
      $explode_year = explode('-', $year);
      $pension_search_year = $explode_year[0];
      if ($month == 'January' || $month == 'February' || $month == 'March') {
        $pension_search_year = $pension_search_year + 1;
      }

      $parameter = array();
      $table_name = 'pension.beneficiary';
      if (!is_null($scheme_id)) {
        $schemes_arr =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
        $parameter['scheme_id'] = $scheme_id;
        $schema_name =  $schemes_arr->short_code;
        //dd($schema_name);
        if (empty($schema_name))
          $schema_name = 'pension';
        $table_name =  $schema_name . '.beneficiary';
      } else {
        $schemes_in_arr =  Configduty::select('scheme_id')->where('user_id', '=', $user_id)->get();
        $schemes_in = array();
        // dd($schemes_in_arr);
        foreach ($schemes_in_arr  as $schm) {
          array_push($schemes_in, $schm->scheme_id);
        }
        //dd($schemes_in);
      }
      if (!is_null($year)) {
        $parameter['lot_year'] = $year;
      }
      if (!is_null($month)) {
        $parameter['lot_month'] = $month;
      }

      $query = 'select d.district_name,d.district_code ,
          coalesce(count( distinct b.id) FILTER(WHERE date_part(\'year\',b.created_at)=' . $pension_search_year . ' and trim(to_char(b.created_at,\'Month\'))= :lot_month) ,0) as applied,
          coalesce(count( distinct b.id) FILTER(WHERE date_part(\'year\',b.created_at)=' . $pension_search_year . ' and b.next_level_role_id >= 0 and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as verified,
          coalesce(count( distinct b.id) FILTER(WHERE date_part(\'year\',b.created_at)=' . $pension_search_year . ' and b.next_level_role_id = 0 and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as approved,
          coalesce(count( distinct b.id) FILTER(WHERE b.next_level_role_id < 0 and date_part(\'year\',b.created_at)=' . $pension_search_year . ' and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as rejected,
          coalesce(count( distinct b.id) FILTER(WHERE l.wrongdata_flag in (0,1,2) and l.push_to_ifms_status=1),0) as pushed_ifms,
          coalesce(count( distinct b.id) FILTER(WHERE l.wrongdata_flag in (1) and (l.ref_no is not null or l.ref_no <> -1)),0) as ifms_returned,
          coalesce(count( b.id) FILTER(WHERE l.wrongdata_flag in (0,2) and l.ref_no>0 and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as fresh_app_mandate_generated,
	        coalesce(count( b.id) FILTER(WHERE l.wrongdata_flag in (0,2) and l.ref_no>0 and EXTRACT(MONTH FROM b.created_at)< EXTRACT(MONTH FROM to_date(:lot_month, \'Mon\'))),0) as old_app_mandate_generated,
          coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0,2) and l.ref_no>0),0) as amount_booked,
          coalesce(count( b.id) FILTER(WHERE l.wrongdata_flag in (2) and l.ref_no>0),0) as rbi_failed,
          coalesce(count( b.id) FILTER(WHERE l.wrongdata_flag in (0) and l.ref_no>0),0) as rbi_success,
          coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0) and l.ref_no>0),0) as amount_paid,
          coalesce(count(b.id) FILTER(WHERE b.last_paid_yymm < CONCAT((RIGHT(' . $pension_search_year . '::varchar,2)),(LPAD((EXTRACT(MONTH FROM TO_DATE(\'' . $month . '\', \'Month\')))::text, 2, \'0\')))::int and b.next_level_role_id=0),0) as payment_lying
                  FROM m_district d 
                  left join ' . $table_name . ' b on d.district_code = b.dist_code';
      if (!is_null($scheme_id)) {
        $query = $query . ' and b.scheme_id = :scheme_id';
      } else {
        $query = $query . ' and b.scheme_id IN (' . implode(',', $schemes_in) . ')';
      }
      $query = $query . ' left join 
                          (select e.pension_id,e.scheme_id,lm.ref_no, e.wrongdata_flag, e.amount, lm.push_to_ifms_status 
                          from ifms.' . $tld_table . ' e left join lot_master lm 
                          on e.drn_part = lm.lot_no';
      if (!is_null($scheme_id)) {
        $query = $query . ' and lm.scheme_id = :scheme_id';
      } else {
        $query = $query . ' and lm.scheme_id IN (' . implode(',', $schemes_in) . ')';
      }
      if (!is_null($year)) {
        $query = $query . ' and lm.lot_year= :lot_year';
      }
      if (!is_null($month)) {
        $query = $query . ' and lm.lot_month= :lot_month';
      }
      $query = $query . ' and lm.push_to_ifms_status=1) l
                          on b.id = l.pension_id and b.scheme_id = l.scheme_id';

      $query = $query . ' group by d.district_name,d.district_code order by d.district_name';


      ################################################################################
      #                     New Changes on 24 May 2021 IFMS                          #
      #       Transaction Lot & Trnasaction Lot Details both Union Here              #
      ################################################################################
      $union_query="SELECT tld.district_name,tld.district_code,
        MAX(applied) applied,MAX(verified) verified,MAX(approved) approved,MAX(rejected) rejected,
        SUM(pushed_ifms) pushed_ifms,SUM(ifms_returned) ifms_returned,SUM(fresh_app_mANDate_generated) fresh_app_mANDate_generated,
        SUM(old_app_mANDate_generated) old_app_mANDate_generated,SUM(amount_booked) amount_booked,SUM(rbi_success) rbi_success,
        SUM(rbi_failed) rbi_failed,SUM(amount_paid) amount_paid, MAX(payment_lying) payment_lying
        FROM 
        (SELECT d.district_name,d.district_code,
         coalesce(COUNT( distinct b.id) FILTER(WHERE date_part('year',b.created_at)='".$pension_search_year."' AND trim(to_char(b.created_at,'Month'))= '".$month."') ,0) as applied,
        coalesce(COUNT( distinct b.id) FILTER(WHERE date_part('year',b.created_at)='".$pension_search_year."' AND b.next_level_role_id >= 0 AND trim(to_char(b.created_at,'Month'))= '".$month."'),0) as verified,
        coalesce(COUNT( distinct b.id) FILTER(WHERE date_part('year',b.created_at)='".$pension_search_year."' AND b.next_level_role_id = 0 AND trim(to_char(b.created_at,'Month'))= '".$month."'),0) as approved,
        coalesce(COUNT( distinct b.id) FILTER(WHERE b.next_level_role_id < 0 AND date_part('year',b.created_at)='".$pension_search_year."' AND trim(to_char(b.created_at,'Month'))= '".$month."'),0) as rejected,
        coalesce(COUNT( distinct b.id) FILTER(WHERE l.wrongdata_flag in (0,1,2) AND l.push_to_ifms_status=1),0) as pushed_ifms,
        coalesce(COUNT( distinct b.id) FILTER(WHERE l.wrongdata_flag in (1) AND (l.ref_no is not null or l.ref_no <> -1)),0) as ifms_returned,
        coalesce(COUNT( b.id) FILTER(WHERE l.wrongdata_flag in (0,2) AND l.ref_no>0 AND trim(to_char(b.created_at,'Month'))= '".$month."'),0) as fresh_app_mANDate_generated,
        coalesce(COUNT( b.id) FILTER(WHERE l.wrongdata_flag in (0,2) AND l.ref_no>0 AND EXTRACT(MONTH FROM b.created_at)< EXTRACT(MONTH FROM to_date('".$month."', 'Mon'))),0) as old_app_mandate_generated,
        coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0,2) AND l.ref_no>0),0) as amount_booked,
        coalesce(COUNT( b.id) FILTER(WHERE l.wrongdata_flag in (2) AND l.ref_no>0),0) as rbi_failed,
        coalesce(COUNT( b.id) FILTER(WHERE l.wrongdata_flag in (0) AND l.ref_no>0),0) as rbi_success,
        coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0) AND l.ref_no>0),0) as amount_paid,
        coalesce(count(b.id) FILTER(WHERE b.last_paid_yymm < CONCAT((RIGHT(" . $pension_search_year . "::varchar,2)),(LPAD((EXTRACT(MONTH FROM TO_DATE('" . $month . "', 'Month')))::text, 2, '0')))::int and b.next_level_role_id=0),0) as payment_lying
        FROM m_district d 
        LEFT JOIN ".$table_name." b ON d.district_code = b.dist_code AND b.scheme_id = ".$scheme_id." LEFT JOIN 
        (SELECT e.pension_id,e.scheme_id,lm.ref_no, e.wrongdata_flag, e.amount, lm.push_to_ifms_status 
        FROM ifms.transaction_lot_details e LEFT JOIN lot_master lm 
        ON e.drn_part = lm.lot_no 
        AND lm.scheme_id = ".$scheme_id." 
        AND lm.lot_year= '".$year."' 
        AND lm.lot_month= '".$month."' 
        AND lm.push_to_ifms_status=1) l
        ON b.id = l.pension_id AND b.scheme_id = l.scheme_id GROUP BY d.district_name,d.district_code
        UNION ALL
        SELECT d.district_name,d.district_code,
         coalesce(COUNT( distinct b.id) FILTER(WHERE date_part('year',b.created_at)='".$pension_search_year."' AND trim(to_char(b.created_at,'Month'))= '".$month."') ,0) as applied,
        coalesce(COUNT( distinct b.id) FILTER(WHERE date_part('year',b.created_at)='".$pension_search_year."' AND b.next_level_role_id >= 0 AND trim(to_char(b.created_at,'Month'))= '".$month."'),0) as verified,
        coalesce(COUNT( distinct b.id) FILTER(WHERE date_part('year',b.created_at)='".$pension_search_year."' AND b.next_level_role_id = 0 AND trim(to_char(b.created_at,'Month'))= '".$month."'),0) as approved,
        coalesce(COUNT( distinct b.id) FILTER(WHERE b.next_level_role_id < 0 AND date_part('year',b.created_at)='".$pension_search_year."' AND trim(to_char(b.created_at,'Month'))= '".$month."'),0) as rejected,
        coalesce(COUNT( distinct b.id) FILTER(WHERE l.wrongdata_flag in (0,1,2) AND l.push_to_ifms_status=1),0) as pushed_ifms,
        coalesce(COUNT( distinct b.id) FILTER(WHERE l.wrongdata_flag in (1) AND (l.ref_no is not null or l.ref_no <> -1)),0) as ifms_returned,
        coalesce(COUNT( b.id) FILTER(WHERE l.wrongdata_flag in (0,2) AND l.ref_no>0 AND trim(to_char(b.created_at,'Month'))= '".$month."'),0) as fresh_app_mANDate_generated,
        coalesce(COUNT( b.id) FILTER(WHERE l.wrongdata_flag in (0,2) AND l.ref_no>0 AND EXTRACT(MONTH FROM b.created_at)< EXTRACT(MONTH FROM to_date('".$month."', 'Mon'))),0) as old_app_mandate_generated,
        coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0,2) AND l.ref_no>0),0) as amount_booked,
        coalesce(COUNT( b.id) FILTER(WHERE l.wrongdata_flag in (2) AND l.ref_no>0),0) as rbi_failed,
        coalesce(COUNT( b.id) FILTER(WHERE l.wrongdata_flag in (0) AND l.ref_no>0),0) as rbi_success,
        coalesce(sum(l.amount)FILTER(WHERE l.wrongdata_flag in (0) AND l.ref_no>0),0) as amount_paid,
        coalesce(count(b.id) FILTER(WHERE b.last_paid_yymm < CONCAT((RIGHT(" . $pension_search_year . "::varchar,2)),(LPAD((EXTRACT(MONTH FROM TO_DATE('" . $month . "', 'Month')))::text, 2, '0')))::int and b.next_level_role_id=0),0) as payment_lying
        FROM m_district d 
        LEFT JOIN ".$table_name." b ON d.district_code = b.dist_code AND b.scheme_id = ".$scheme_id." LEFT JOIN 
        (SELECT e.pension_id,e.scheme_id,lm.ref_no, e.wrongdata_flag, e.amount, lm.push_to_ifms_status 
        FROM ifms.transaction_lot_details_report e LEFT JOIN lot_master lm 
        ON e.drn_part = lm.lot_no 
        AND lm.scheme_id = ".$scheme_id." 
        AND lm.lot_year= '".$year."' 
        AND lm.lot_month= '".$month."' 
        AND lm.push_to_ifms_status=1) l
        ON b.id = l.pension_id AND b.scheme_id = l.scheme_id GROUP BY d.district_name,d.district_code) tld 
        GROUP BY tld.district_name,tld.district_code
        ORDER BY tld.district_name";

      
      if ($payment_option == 3) {
        $data = DB::connection('pgsql_mis')->select($union_query);
      }
      else {
        $data = DB::connection('pgsql_mis')->select($query, $parameter);
      }  
        

      return datatables()->of($data)
        ->addColumn('district_name', function ($data) {
          if ($data->district_name != Null) {
            return '<a href="' . route('district-drill-down-consol-district', [$data->district_code]) . '">'
              . $data->district_name . '</a>';
          } else {
            return 0;
          }
        })
        ->addColumn('applied', function ($data) {
          return $data->applied;
        })
        ->addColumn('verified', function ($data) {
          return $data->verified;
        })
        ->addColumn('approved', function ($data) {
          return $data->approved;
        })
        ->addColumn('rejected', function ($data) {
          return $data->rejected;
        })
        ->addColumn('pushed_ifms', function ($data) {
          return $data->pushed_ifms;
        })
        ->addColumn('ifms_returned', function ($data) {
          return $data->ifms_returned;
        })
        ->addColumn('fresh_mandate_generated', function ($data) {
          return $data->fresh_app_mandate_generated;
        })
        ->addColumn('old_mandate_generated', function ($data) {
          return $data->old_app_mandate_generated;
        })

        ->addColumn('amount_booked', function ($data) {
          return $data->amount_booked / 100000;
        })
        ->addColumn('rbi_failed', function ($data) {
          return $data->rbi_failed;
        })
        ->addColumn('rbi_success', function ($data) {
          return $data->rbi_success;
        })
        ->addColumn('amount_paid', function ($data) {
          return $data->amount_paid / 100000;
        })
        ->addColumn('payment_lying', function ($data) {
          return $data->payment_lying;
        })
        ->rawColumns(['district_name', 'applied', 'verified', 'approved', 'rejected', 'pushed_ifms', 'ifms_returned', 'fresh_mandate_generated', 'old_mandate_generated', 'amount_booked', 'rbi_failed', 'rbi_success', 'amount_paid', 'payment_lying'])
        ->make(true);
    }
    return view('District-Drilldown.district_consolidate_report')->with('schemes', $schemes);
  }
  public function getblockconsollist($district_code)
  {

    //return redirect('block-drill-down')->;
    return Redirect::route('block-drill-down-consol-dist', [$district_code]);
  }

  public function consol_report_sbi()
  {
    $c_time = Carbon::now();
    $year = $c_time->year;
    if ($c_time->month == 1 || $c_time->month == 2 || $c_time->month == 3) {
      $first_part = $year - 1;
      $second_part = $year;
    } else {
      $first_part = $year;
      $second_part = $year + 1;
    }
    $select_year = $first_part . '-' . $second_part;
    $monthName = $c_time->format('F');
    //dd($monthName);
    //dd($select_year);
    $user_id = AuthChecker::getUserId();
    $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
    return view('District-Drilldown.district_consolidate_report_sbi')->with('schemes', $schemes)->with('selected_year', $select_year)->with('selected_month', $monthName);
  }

  public function getconsol_reportData_sbi(Request $request)
  {
    $schemes = array();
    if (request()->ajax()) {
      $user_id = AuthChecker::getUserId();
      $scheme_id = $request->scheme_id;
      $year = $request->fin_year;
      $month = $request->month;
      $payment_option = $request->payment_option;
      $tld_table = 'transaction_lot_details';
      if ($payment_option == 2) {
        $tld_table = 'transaction_lot_details_report';
      }
      $explode_year = explode('-', $year);
      $pension_search_year = $explode_year[0];
      if ($month == 'January' || $month == 'February' || $month == 'March') {
        $pension_search_year = $pension_search_year + 1;
      }
      $parameter = array();
      $table_name = 'pension.beneficiary';
      if (!is_null($scheme_id)) {
        $schemes_arr =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
        $parameter['scheme_id'] = $scheme_id;
        $schema_name =  $schemes_arr->short_code;
        //dd($schema_name);
        if (empty($schema_name))
          $schema_name = 'pension';
        $table_name =  $schema_name . '.beneficiary';
      } else {
        $schemes_in_arr =  Configduty::select('scheme_id')->where('user_id', '=', $user_id)->get();
        $schemes_in = array();
        // dd($schemes_in_arr);
        foreach ($schemes_in_arr  as $schm) {
          array_push($schemes_in, $schm->scheme_id);
        }
        //dd($schemes_in);
      }
      if (!is_null($year)) {
        $parameter['lot_year'] = $year;
      }
      if (!is_null($month)) {
        $parameter['lot_month'] = $month;
      }

      $query = 'select d.district_name, d.district_code,
          coalesce(count( b.id) FILTER(WHERE date_part(\'year\',b.created_at)=' . $pension_search_year . ' and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as applied,
          coalesce(count( b.id) FILTER(WHERE date_part(\'year\',b.created_at)=' . $pension_search_year . ' and b.next_level_role_id >= 0 and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as verified,
          coalesce(count( b.id) FILTER(WHERE date_part(\'year\',b.created_at)=' . $pension_search_year . ' and b.next_level_role_id = 0 and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as approved,
          coalesce(count( b.id) FILTER(WHERE l.lot_status <=5 and date_part(\'year\',b.created_at)=' . $pension_search_year . ' and trim(to_char(b.created_at,\'Month\'))= :lot_month),0) as current_month_pushed_sbi,
	  coalesce(count( b.id) FILTER(WHERE l.lot_status <=5 and EXTRACT(MONTH FROM b.created_at)< EXTRACT(MONTH FROM to_date(:lot_month, \'Mon\'))),0) as past_months_pushed_sbi,
	  coalesce(count( b.id) FILTER(WHERE  l.lot_status<4),0) as sbi_under_process,
          coalesce(count( b.id) FILTER(WHERE l.status_code=\'S00\' and l.lot_status>3),0) as sbi_success,
          coalesce(count( b.id) FILTER(WHERE l.status_code!=\'S00\' and l.status_code is not null),0) as sbi_failed,
          coalesce(sum(l.credit_amount/100) FILTER(WHERE l.status_code=\'S00\' and l.lot_status>3 ),0) as amount_billed,
          coalesce(count(b.id) FILTER(WHERE b.last_paid_yymm < CONCAT((RIGHT(' . $pension_search_year . '::varchar,2)),(LPAD((EXTRACT(MONTH FROM TO_DATE(\'' . $month . '\', \'Month\')))::text, 2, \'0\')))::int and b.next_level_role_id=0),0) as payment_lying
                  FROM m_district d 
                  left join ' . $table_name . ' b on d.district_code = b.dist_code';
      if (!is_null($scheme_id)) {
        $query = $query . ' and b.scheme_id = :scheme_id';
      } else {
        $query = $query . ' and b.scheme_id IN (' . implode(',', $schemes_in) . ')';
      }
      $query = $query . ' left join 
                          (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code 
                          from sbi.' . $tld_table . ' tld right join sbi.transaction_lot tl
                          on tld.lot_no = tl.lot_no ';
      if (!is_null($scheme_id)) {
        $query = $query . ' and tl.scheme_id = :scheme_id';
      } else {
        $query = $query . ' and tl.scheme_id IN (' . implode(',', $schemes_in) . ')';
      }
      if (!is_null($year)) {
        $query = $query . ' and tl.lot_year= :lot_year';
      }
      if (!is_null($month)) {
        $query = $query . ' and tl.lot_month= :lot_month';
      }
      $query = $query . ') l
                          on b.id = l.pension_id and b.scheme_id = l.scheme_id';

      $query = $query . ' group by d.district_name,d.district_code order by d.district_name';


      ################################################################################
      #                     New Changes on 24 May 2021 SBI                           #
      #       Transaction Lot & Trnasaction Lot Details both Union Here              #
      ################################################################################
      $union_query = "SELECT t.district_name,t.district_code,MAX(applied) applied,MAX(verified) verified,MAX(approved) approved,SUM(current_month_pushed_sbi) current_month_pushed_sbi, SUM(past_months_pushed_sbi) past_months_pushed_sbi, SUM(sbi_under_process) sbi_under_process, SUM(sbi_success) sbi_success, SUM(sbi_failed) sbi_failed, SUM(amount_billed) amount_billed, MAX(payment_lying) payment_lying FROM 
      (select d.district_name, d.district_code,
      coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)='".$pension_search_year."' and trim(to_char(b.created_at,'Month'))= '".$month."'),0) as applied,
      coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)='".$pension_search_year."' and b.next_level_role_id >= 0 and trim(to_char(b.created_at,'Month'))= '".$month."'),0) as verified,
      coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)='".$pension_search_year."' and b.next_level_role_id = 0 and trim(to_char(b.created_at,'Month'))= '".$month."'),0) as approved,
      coalesce(count( b.id) FILTER(WHERE l.lot_status <=5 and date_part('year',b.created_at)='".$pension_search_year."' and trim(to_char(b.created_at,'Month'))= '".$month."'),0) as current_month_pushed_sbi,
      coalesce(count( b.id) FILTER(WHERE l.lot_status <=5 and EXTRACT(MONTH FROM b.created_at)< EXTRACT(MONTH FROM to_date('".$month."', 'Mon'))),0) as past_months_pushed_sbi,
      coalesce(count( b.id) FILTER(WHERE  l.lot_status<4),0) as sbi_under_process,
      coalesce(count( b.id) FILTER(WHERE l.status_code='S00' and l.lot_status>3),0) as sbi_success,
      coalesce(count( b.id) FILTER(WHERE l.status_code!='S00' and l.status_code is not null),0) as sbi_failed,
      coalesce(sum(l.credit_amount/100) FILTER(WHERE l.status_code='S00' and l.lot_status>3 ),0) as amount_billed,
      coalesce(count(b.id) FILTER(WHERE b.last_paid_yymm < CONCAT((RIGHT(" . $pension_search_year . "::varchar,2)),(LPAD((EXTRACT(MONTH FROM TO_DATE('" . $month . "', 'Month')))::text, 2, '0')))::int and b.next_level_role_id=0),0) as payment_lying
      FROM m_district d 
      left join ".$table_name." b on d.district_code = b.dist_code and b.scheme_id = ".$scheme_id."
      left join 
      (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code 
      from sbi.transaction_lot_details tld right join sbi.transaction_lot tl
      on tld.lot_no = tl.lot_no 
      and tl.scheme_id = ".$scheme_id."
      and tl.lot_year= '".$year."'
      and tl.lot_month= '".$month."') l
      on b.id = l.pension_id and b.scheme_id = l.scheme_id
      group by d.district_name,d.district_code
      UNION ALL 
      select d.district_name, d.district_code,
      coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)='".$pension_search_year."' and trim(to_char(b.created_at,'Month'))= '".$month."'),0) as applied,
      coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)='".$pension_search_year."' and b.next_level_role_id >= 0 and trim(to_char(b.created_at,'Month'))= '".$month."'),0) as verified,
      coalesce(count( b.id) FILTER(WHERE date_part('year',b.created_at)='".$pension_search_year."' and b.next_level_role_id = 0 and trim(to_char(b.created_at,'Month'))= '".$month."'),0) as approved,
      coalesce(count( b.id) FILTER(WHERE l.lot_status <=5 and date_part('year',b.created_at)='".$pension_search_year."' and trim(to_char(b.created_at,'Month'))= '".$month."'),0) as current_month_pushed_sbi,
      coalesce(count( b.id) FILTER(WHERE l.lot_status <=5 and EXTRACT(MONTH FROM b.created_at)< EXTRACT(MONTH FROM to_date('".$month."', 'Mon'))),0) as past_months_pushed_sbi,
      coalesce(count( b.id) FILTER(WHERE  l.lot_status<4),0) as sbi_under_process,
      coalesce(count( b.id) FILTER(WHERE l.status_code='S00' and l.lot_status>3),0) as sbi_success,
      coalesce(count( b.id) FILTER(WHERE l.status_code!='S00' and l.status_code is not null),0) as sbi_failed,
      coalesce(sum(l.credit_amount/100) FILTER(WHERE l.status_code='S00' and l.lot_status>3 ),0) as amount_billed,
      coalesce(count(b.id) FILTER(WHERE b.last_paid_yymm < CONCAT((RIGHT(" . $pension_search_year . "::varchar,2)),(LPAD((EXTRACT(MONTH FROM TO_DATE('" . $month . "', 'Month')))::text, 2, '0')))::int and b.next_level_role_id=0),0) as payment_lying
      FROM m_district d 
      left join ".$table_name." b on d.district_code = b.dist_code and b.scheme_id = ".$scheme_id."
      left join 
      (select tld.pension_id,tld.scheme_id,tl.lot_status, tld.credit_amount,tld.status_code 
      from sbi.transaction_lot_details_report tld right join sbi.transaction_lot tl
      on tld.lot_no = tl.lot_no 
      and tl.scheme_id = ".$scheme_id."
      and tl.lot_year= '".$year."'
      and tl.lot_month= '".$month."') l
      on b.id = l.pension_id and b.scheme_id = l.scheme_id
      group by d.district_name,d.district_code) t 
      group by t.district_name,t.district_code
      order by t.district_name";
      
      if ($payment_option == 3) {
        $data = DB::connection('pgsql_mis')->select($union_query, $parameter);
      }
      else {
        $data = DB::connection('pgsql_mis')->select($query, $parameter);
      }
      
      return datatables()->of($data)
        ->addColumn('district_name', function ($data) {
          if ($data->district_name != Null) {
            return '<a href="' . route('district-drill-down-consol-district-sbi', [$data->district_code]) . '">'
              . $data->district_name . '</a>';
          } else {
            return 0;
          }
        })
        ->addColumn('applied', function ($data) {
          return $data->applied;
        })
        ->addColumn('verified', function ($data) {
          return $data->verified;
        })
        ->addColumn('approved', function ($data) {
          return $data->approved;
        })
        ->addColumn('current_month_pushed_sbi', function ($data) {
          return $data->current_month_pushed_sbi;
        })
        ->addColumn('past_months_pushed_sbi', function ($data) {
          return $data->past_months_pushed_sbi;
        })
        ->addColumn('sbi_under_process', function ($data) {
          return $data->sbi_under_process;
        })
        ->addColumn('sbi_failed', function ($data) {
          return $data->sbi_failed;
        })
        ->addColumn('sbi_success', function ($data) {
          return $data->sbi_success;
        })

        ->addColumn('amount_billed', function ($data) {
          return $data->amount_billed / 100000;
        })
        ->addColumn('payment_lying', function ($data) {
          return $data->payment_lying;
        })
        ->rawColumns(['district_name', 'applied', 'verified', 'approved', 'current_month_pushed_sbi', 'past_months_pushed_sbi', 'sbi_under_process', 'sbi_failed', 'sbi_success', 'amount_billed', 'payment_lying'])
        ->make(true);
    }
    return view('District-Drilldown.district_consolidate_report_sbi')->with('schemes', $schemes);
  }

  public function getblockconsollist_sbi($district_code)
  {

    //return redirect('block-drill-down')->;
    return Redirect::route('block-drill-down-consol-dist-sbi', [$district_code]);
  }

  //Consolidated Report Districtwise WCD
  public function wcdconsol_report()
  {
    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id', '=', $user_id)->first();
    $schemes = Scheme::where('is_active', 1)->whereIn('id', [2, 10, 11])->get(['scheme_name as name', 'id as id']);

    return view('District-Drilldown.district_consolidatewcd_report')->with('schemes', $schemes);
  }

  public function getwcdconsol_reportData(Request $request)
  {
    if (request()->ajax()) {
      $user_id = AuthChecker::getUserId();
      $scheme_id = $request->level1a;
      //$pensioner_type = $request->level1c;

      $data = array();

      $condition = '';


      if (($scheme_id == '') || ($scheme_id == 2)) {
        $query = "select s.scheme_name,d.district_name, b.dist_code,
            count(b.id) applied,
            sum(case when b.next_level_role_id>=0 then 1 else 0 end) verified,
            sum(case when b.next_level_role_id=0 then 1 else 0 end) approved,
            sum(case when b.next_level_role_id<0 then 1 else 0 end) rejected
            from manabik.beneficiary b, m_scheme s,m_district d
            where s.id=b.scheme_id and d.district_code=b.dist_code" . $condition .
          " group by s.scheme_name,d.district_name,b.dist_code
            order by d.district_name";

        $data_part = DB::connection('pgsql_mis')->select($query);
        $data = array_merge($data, $data_part);
      }

      if (($scheme_id == '') || ($scheme_id == 10)) {
        $query = "select s.scheme_name,d.district_name, b.dist_code,
            count(b.id) applied,
            sum(case when b.next_level_role_id>=0 then 1 else 0 end) verified,
            sum(case when b.next_level_role_id=0 then 1 else 0 end) approved,
            sum(case when b.next_level_role_id<0 then 1 else 0 end) rejected
            from oap_wcd.beneficiary b, m_scheme s,m_district d
            where s.id=b.scheme_id and d.district_code=b.dist_code" . $condition .
          " group by s.scheme_name,d.district_name,b.dist_code
            order by d.district_name";
        $data_part = DB::connection('pgsql_mis')->select($query);
        $data = array_merge($data, $data_part);
      }

      if (($scheme_id == '') || ($scheme_id == 11)) {
        $query = "select s.scheme_name,d.district_name, b.dist_code,
            count(b.id) applied,
            sum(case when b.next_level_role_id>=0 then 1 else 0 end) verified,
            sum(case when b.next_level_role_id=0 then 1 else 0 end) approved,
            sum(case when b.next_level_role_id<0 then 1 else 0 end) rejected
            from wp_wcd.beneficiary b, m_scheme s,m_district d
            where s.id=b.scheme_id and d.district_code=b.dist_code" . $condition .
          " group by s.scheme_name,d.district_name, b.dist_code
            order by d.district_name";
        $data_part = DB::connection('pgsql_mis')->select($query);
        $data = array_merge($data, $data_part);
      }

      // $data = DB::connection('pgsql_mis')->select($query, $parameter);
      return datatables()->of($data)
        ->addColumn('district_name', function ($data) {
          if ($data->district_name != Null) {
            return '<a href="' . route('district-drill-down-consolwcd-district', [$data->dist_code]) . '">'
              . $data->district_name . '</a>';
          } else {
            return 0;
          }
        })
        ->rawColumns(['district_name'])
        ->make(true);
    }
    return view('District-Drilldown.district_consolidatewcd_report')->with('schemes', $schemes);
  }

  public function getwcdblockconsollist($district_code)
  {

    //return redirect('block-drill-down')->;
    return Redirect::route('block-drill-down-consolwcd-dist', [$district_code]);
  }
}
