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
use Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\AuthChecker;

class IdentityDrillDownReportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    set_time_limit(200);
  }
  
  public function identity_report()
  {
    $c_time = Carbon::now();
    $year = $c_time->year;
    
    $user_id = AuthChecker::getUserId();
    $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
    return view('Identity-Drilldown.identity_report')->with('schemes', $schemes);
  }

  public function get_identity_report(Request $request)
  {
    $schemes = array();
    if (request()->ajax()) {
      $user_id      = Auth::user()->id;
      $scheme_id    = $request->scheme_id; 
      $identity_no  = $request->identity_no;    
      
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


        $query = "Select d.district_name, d.district_code,
                        sum(total_beneficiary)          as total_beneficiary,
                        sum(identity_count)       as identity_count

                        from public.m_district d left join
                        (
                        select dist_code,
                        count(1)                        as total_beneficiary,
                        count(1) Filter(where ".$identity_no." != '' )         as identity_count

                        FROM ". $table_name. "
                        group by dist_code  
                        )b  on d.district_code = b.dist_code";
      

        $query .= " group by d.district_name,d.district_code order by d.district_name";
        $data = DB::connection('pgsql')->select($query);


      
     
      
      return datatables()->of($data)
        ->addColumn('district_name', function ($data) {

           if ($data->district_name != Null) {
            return '<a href="' . route('block-subdiv-identity-report', [$data->district_code]) . '">'
              . $data->district_name . '</a>';
          } else {
            return 0;
          }
          
        })
        ->addColumn('total_ben', function ($data) {
          return $data->total_beneficiary;
        })
        ->addColumn('identity_count', function ($data) {
          return $data->identity_count;
        })
       
       
        ->rawColumns(['district_name', 'total_ben', 'identity_count'])
        ->make(true);
    }
    return view('Identity-Drilldown.identity_report')->with('schemes', $schemes);
  }

  public function block_subdiv_identity_report($district_code)
  {

     $user_id = AuthChecker::getUserId();
        $district_code = $district_code;
        $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();
        $c_time = Carbon::now();
       
        $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ")"));
        return view('Identity-Drilldown.block_subdiv_identity_report')->with('schemes', $schemes)->with('district_name', $district_name)->with('district_code', $district_code);
  }

   public function get_block_subdiv_identity_report(Request $request)
    {
        //DB::enableQueryLog();
      $schemes = array();
      if (request()->ajax()) {
      $user_id      = Auth::user()->id;
      $scheme_id    = $request->scheme_id; 
      $identity_no  = $request->identity_no; 
      $rural_urban  = $request->rural_urban;
      $dist_code    = $request->district_code;     
      
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
        $data = array();
        //echo $rural_urban; exit;
        if($rural_urban == 'Rural')
        {
        $query1 = "Select block_ulb_name, 'Rural' as rural_urban,                       
                        count(1)    as total_beneficiary,
                        count(1) Filter(where ".$identity_no." != '' )  as identity_count
                        FROM ". $table_name. " where rural_urban_id=2 and dist_code =". $dist_code. "
                        group by block_ulb_name  
                        order by block_ulb_name";
        
        $data = DB::connection('pgsql')->select($query1);
        }
        else if($rural_urban == 'Urban')
        {

        $query2 = "Select block_ulb_name, 'Urban' as rural_urban,                       
                        count(1)    as total_beneficiary,
                        count(1) Filter(where ".$identity_no." != '' )  as identity_count
                        FROM ". $table_name. " where rural_urban_id=1 and dist_code =". $dist_code. "
                        group by block_ulb_name  
                        order by block_ulb_name";
        
        $data = DB::connection('pgsql')->select($query2);
        }
        else
        {

          $query1 = "Select block_ulb_name, 'Rural' as rural_urban,                       
                        count(1)    as total_beneficiary,
                        count(1) Filter(where ".$identity_no." != '' )  as identity_count
                        FROM ". $table_name. " where rural_urban_id=2 and dist_code =". $dist_code. "
                        group by block_ulb_name  
                        order by block_ulb_name";
        
          $data1 = DB::connection('pgsql')->select($query1);

          $query2 = "Select block_ulb_name, 'Urban' as rural_urban,                       
                        count(1)    as total_beneficiary,
                        count(1) Filter(where ".$identity_no." != '' )  as identity_count
                        FROM ". $table_name. " where rural_urban_id=1 and dist_code =". $dist_code. "
                        group by block_ulb_name  
                        order by block_ulb_name";
        
          $data2 = DB::connection('pgsql')->select($query2);

          $data = array_merge($data1,$data2);

        }
      
      return datatables()->of($data)
        ->addColumn('rural_urban', function ($data) {
          return $data->rural_urban;         
          
        })

         ->addColumn('block', function ($data) {
          return $data->block_ulb_name;         
          
        })
        ->addColumn('total_ben', function ($data) {
          return $data->total_beneficiary;
        })
        ->addColumn('identity_count', function ($data) {
          return $data->identity_count;
        })
       
       
        ->rawColumns(['rural_urban','block', 'total_ben', 'identity_count'])
        ->make(true);
    }
       return view('Identity-Drilldown.block_subdiv_identity_report')->with('schemes', $schemes);
    }
}
