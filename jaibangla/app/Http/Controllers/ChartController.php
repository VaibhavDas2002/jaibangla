<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scheme;
use App\Configduty;
use App\District;
use App\UrbanBody;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use App\GP;
use App\MapLavel;
use Redirect;
use Auth;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class ChartController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      // dd($request->all());
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = Auth::user()->id;
      if($designation_id_old!='HOD'){
        return redirect("/")->with('danger', 'Not Allowed');

      }
      $duty_obj = Configduty::where('user_id', $user_id)->whereIn('scheme_id', [2,10,11])->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
        $data1=array();
        $designation_id_old = Auth::user()->designation_id_old;
        $userId = Auth::user()->id;
        $selected_scheme=2;
        $sceme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (2,10,11) and id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
        $scheme_row = Scheme::where('id', $selected_scheme)->first();
        if (!empty($scheme_row->short_code)) {
            $schema = $scheme_row->short_code;
            $scheme_name = $scheme_row->scheme_name;
          } else {
            $schema = "pension";
          }
        $query = "select A.location_id,A.location_name,
        COALESCE(C.approved,0) as approved, 
        COALESCE(C.aadhar_capture,0) as aadhar_capture
        from(
        select district_code as location_id,district_name as location_name
         from public.m_district 
         )
         as A  
        LEFT JOIN
        (select
                    count(1) filter(where next_level_role_id=0) as approved,
                    count(1) filter(where next_level_role_id=0 and (no_aadhar IS NULL or no_aadhar=0)) as aadhar_capture,
                    created_by_dist_code
                    from " . $schema . ".beneficiaries 
         group by created_by_dist_code) as C ON A.location_id=C.created_by_dist_code";

    // echo $query;die;
      $result1 = DB::connection('pgsql_mis')->select($query);
      $bar_chart1_labels=["OAP", "WP", "Manabik"];
      $result_oap = DB::connection('pgsql_mis')->select('select count(1) filter(where next_level_role_id IS NULL) as yet_to_verified,
      count(1) filter(where is_verified=1 and is_approved=0 and is_rejected=0) as yet_to_approved, count(1) filter(where next_level_role_id=0) as approved,
      count(1) filter(where next_level_role_id=0 and (no_aadhar IS NULL or no_aadhar=0)) as aadhar_capture from pension.beneficiaries where scheme_id = 13');
      $result_wp = DB::connection('pgsql_mis')->select('select count(1) filter(where next_level_role_id IS NULL) as yet_to_verified,
      count(1) filter(where next_level_role_id>0) as yet_to_approved, 
      count(1) filter(where next_level_role_id=0) as approved,
      count(1) filter(where next_level_role_id=0 and (no_aadhar IS NULL or no_aadhar=0)) as aadhar_capture from pension.beneficiaries where scheme_id = 11');
      $result_manabik = DB::connection('pgsql_mis')->select('select count(1) filter(where next_level_role_id IS NULL) as yet_to_verified,
      count(1) filter(where is_verified=1 and is_approved=0 and is_rejected=0) as yet_to_approved,  
      count(1) filter(where next_level_role_id=0) as approved,
      count(1) filter(where next_level_role_id=0 and (no_aadhar IS NULL or no_aadhar=0)) as aadhar_capture from pension.beneficiaries where scheme_id = 2');
      if(empty($result_oap)){
         $oap_yet_to_verified_cnt=0;
         $oap_yet_to_approved_cnt=0;
         $oap_approve_cnt=0;
         $oap_aadhar_capture=0;
         $oap_percent=100;
      }
      else{
        $oap_yet_to_verified_cnt=$result_oap[0]->yet_to_verified;
        $oap_yet_to_approved_cnt=$result_oap[0]->yet_to_approved;
        $oap_approve_cnt=$result_oap[0]->approved;
        $oap_aadhar_capture=$result_oap[0]->aadhar_capture;
        if($result_oap[0]->aadhar_capture==$result_oap[0]->approved){
          $oap_percent=100;

        }
        else
        $oap_percent=round($result_oap[0]->aadhar_capture*100/$result_oap[0]->approved,2);
      }
      if(empty($result_wp)){
        $wp_yet_to_verified_cnt=0;
        $wp_yet_to_approved_cnt=0;
        $wp_approve_cnt=0;
        $wp_aadhar_capture=0;
        $wp_percent=100;
     }
     else{
       $wp_yet_to_verified_cnt=$result_wp[0]->yet_to_verified;
       $wp_yet_to_approved_cnt=$result_wp[0]->yet_to_approved;
       $wp_approve_cnt=$result_wp[0]->approved;
       $wp_aadhar_capture=$result_wp[0]->aadhar_capture;
       if($result_wp[0]->aadhar_capture==$result_wp[0]->approved){
        $wp_percent=100;

      }
      else
      $wp_percent=round($result_wp[0]->aadhar_capture*100/$result_wp[0]->approved,2);
     }
     if(empty($result_manabik)){
      $manabik_yet_to_verified_cnt=0;
      $manabik_yet_to_approved_cnt=0;
      $manabik_approve_cnt=0;
      $manabik_aadhar_capture=0;
      $manabik_percent=100;
     }
     else{
        $manabik_yet_to_verified_cnt=$result_manabik[0]->yet_to_verified;
        $manabik_yet_to_approved_cnt=$result_manabik[0]->yet_to_approved;
        $manabik_approve_cnt=$result_manabik[0]->approved;
        $manabik_aadhar_capture=$result_manabik[0]->aadhar_capture;
        if($result_manabik[0]->aadhar_capture==$result_manabik[0]->approved){
          $manabik_percent=100;

        }
        else
        $manabik_percent=round($result_manabik[0]->aadhar_capture*100/$result_manabik[0]->approved,2);
      }
      $pieChart = [
        [
            'value' => $manabik_yet_to_verified_cnt,
            'color' => '#f1c232',
            'highlight' => '#f1c232',
            'label' => $scheme_name.' Yet to Verified List',
        ],
        [
          'value' => $manabik_yet_to_approved_cnt,
          'color' => '#A77682',
          'highlight' => '#A77682',
          'label' => $scheme_name.' Yet to Approved List',
        ],
        [
          'value' => $manabik_approve_cnt,
          'color' => '#145889',
          'highlight' => '#145889',
          'label' => $scheme_name.' Approved List',
        ],
        
    ];
    $AadharCapturePercent=[$oap_percent, $wp_percent, $manabik_percent];
    $approveData=[$oap_approve_cnt, $wp_approve_cnt, $manabik_approve_cnt];
    $aadharCaptureData=[$oap_aadhar_capture, $wp_aadhar_capture, $manabik_aadhar_capture];
    return view('Chart/index', [
            'sceme_list'        => $sceme_list,
            'selected_scheme'        => $selected_scheme,
            'result1'        => $result1,
            'bar_chart1_labels'        => $bar_chart1_labels,
            'pieChart'        => $pieChart,
            'AadharCapturePercent'        => $AadharCapturePercent,
            'approveData'        => $approveData,
            'aadharCaptureData'        => $aadharCaptureData,
        ]);
    }
    function distaadharcapture(Request $request){
      $selected_scheme=$request->scheme_id;
      $scheme_row = Scheme::where('id', $selected_scheme)->first();
      if (!empty($scheme_row->short_code)) {
          $schema = $scheme_row->short_code;
          $scheme_name = $scheme_row->scheme_name;
        } else {
          $schema = "pension";
        }
      $query = "select A.location_id,A.location_name,
        COALESCE(C.yet_to_verified,0) as yet_to_verified, 
        COALESCE(C.yet_to_approved,0) as yet_to_approved, 
        COALESCE(C.approved,0) as approved, 
        COALESCE(C.aadhar_capture,0) as aadhar_capture
        from(
        select district_code as location_id,district_name as location_name
         from public.m_district 
         )
         as A  
        LEFT JOIN
        (select
                    count(1) filter(where next_level_role_id IS NULL) as yet_to_verified,
                    count(1) filter(where is_verified=1 and is_approved=0 and is_rejected=0) as yet_to_approved, 
                    count(1) filter(where next_level_role_id=0) as approved,
                    count(1) filter(where next_level_role_id=0 and (no_aadhar IS NULL or no_aadhar=0)) as aadhar_capture,
                    created_by_dist_code
                    from " . $schema . ".beneficiary 
         group by created_by_dist_code) as C ON A.location_id=C.created_by_dist_code";

    // echo $query;die;
      $result1 = DB::connection('pgsql_mis')->select($query);
      $return_arr=array();
      $tr='';
      $approved=0;
      $yet_to_verified=0;
      $yet_to_approved=0;
      if(count($result1)>0){
      foreach($result1 as $arr1){
      $yet_to_verified=$yet_to_verified+intval($arr1->yet_to_verified);
      $yet_to_approved=$yet_to_approved+intval($arr1->yet_to_approved);
      $approved=$approved+intval($arr1->approved);
      $percent=0;
      if($arr1->aadhar_capture==$arr1->approved){
        $percent=100;

      }
      else
      $percent=round($arr1->aadhar_capture*100/$arr1->approved,2);
      if($percent==100)
      $class='success';
      else if($percent>50 and $percent<100)
      $class='info';
      else if($percent<=50)
      $class='danger';
      $tr=$tr.'<tr class="'.$class.'" ><td>'.$arr1->location_name.'</td><td>'.$arr1->approved.'</td><td>'.$arr1->aadhar_capture.'</td><td>'.$percent.'</td></tr>';
      }
     }
     $pieChart = [
      [
          'value' => $yet_to_verified,
          'color' => '#f1c232',
          'highlight' => '#f1c232',
          'label' => $scheme_name.' Yet to Verified List',
      ],
      [
        'value' => $yet_to_approved,
        'color' => '#A77682',
        'highlight' => '#A77682',
        'label' => $scheme_name.' Yet to Approved List',
      ],
      [
        'value' => $approved,
        'color' => '#145889',
        'highlight' => '#145889',
        'label' => $scheme_name.' Approved List',
      ],
      
    ];
      $return_arr['tr1']=$tr;
      $return_arr['pieChart']=$pieChart;
      return $return_arr;
    }
  
}
