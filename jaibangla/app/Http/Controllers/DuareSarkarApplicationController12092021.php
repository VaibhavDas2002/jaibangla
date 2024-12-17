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

class DuareSarkarApplicationController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    
    }

    public function getDistrictApplicationReport()
    {
    
      $designationId=Auth::user()->designation_id_old;
      $userId=Auth::user()->id;
      $reports = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
     //print_r( $reports);die;
      if($designationId=='Approver'){
        $districtCode=Configduty::where('user_id',$userId)->value('district_code');
        $block=Taluka::where('district_code',  $districtCode)->select('block_name','block_code')->get();
      
        return view('Drilldown.block_application_report',compact('block','districtCode','reports'));

      }
      else if($designationId=='HOD' || $designationId=='Dashboard'){
        $districts=District::orderBy('district_name')->get();
        return view('Drilldown.district_application_report',compact('districts','sessiontimeoutmessage','reports'));
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
        $blockId=$request->blockid;
        $rural_urbanid=$request->rural_urbanid;
        if($rural_urbanid==1){
          $data = UrbanBody::where('sub_district_code', $blockId)->select('urban_body_code', 'urban_body_name')->get();
          
        }
        else{
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
    
   

    public function datatableBlockApplicationReport(Request $request){
   //  dd($request->all());
        $muniid=$request->muniid;
        $districtCode=$request->districtCode;
        $blockid=$request->blockid;
        $gpid=$request->gpid;
        $fromdate=$request->fromdate;
        $todate=$request->todate;
        $dateFromat='DD/MM/YYYY';
        $rural_urbanid=$request->rural_urbanid;
        $schemeId=$request->schemeId;
        $schemaname=Scheme::where('id',$schemeId)->value('short_code');
        $query="";
    
          if($rural_urbanid==1){
            $query="select urban_body_name as bsm,total_applicant,verified,approved,rejected,faulty,fresh_application from m_urban_body bl
            left join
            (
            Select b.block_ulb_code,
            sum(case when ds_registration_no is not null and next_level_role_id is null then 1 else 0 end) as fresh_application,
            sum(case when ds_registration_no is not null then 1 else 0 end) as total_applicant,
            sum(case when ds_registration_no is not null and next_level_role_id>0 and next_level_role_id!=9999  then 1 else 0 end) as verified,
            sum(case when ds_registration_no is not null and next_level_role_id=0 then 1 else 0 end) as approved,
            sum(case when ds_registration_no is not null and next_level_role_id<0 then 1 else 0 end) as rejected,
            sum(case when ds_registration_no is not null and next_level_role_id=9999 then 1 else 0 end) as faulty
            FROM ".$schemaname.".beneficiary b 
            where ds_registration_no is not null  and created_by_dist_code=".$districtCode;
           
            
        
            if(!empty($blockid)){
              $query .=" and block_ulb_code=".$blockid;
            }
           
            if(!empty($fromdate)){
              $query .=" and to_char(b.created_at,'".$dateFromat."')>='".$fromdate."'";
            }
            if(!empty($todate)){
              $query .=" and to_char(b.created_at,'".$dateFromat."')<='".$todate."'";
            }
          
            $query .="  group by b.block_ulb_code
            )x  on bl.urban_body_code=x.block_ulb_code
            where district_code=".$districtCode;
         //  echo $query;die;
          }
      
          else{
            $query="select block_name as bsm,total_applicant,verified,approved,rejected,faulty,fresh_application from m_block bl
            left join
            (
            Select b.created_by_local_body_code,
            sum(case when ds_registration_no is not null and next_level_role_id is null then 1 else 0 end) as fresh_application,
             sum(case when ds_registration_no is not null then 1 else 0 end) as total_applicant,
            sum(case when ds_registration_no is not null and next_level_role_id>0 and next_level_role_id!=9999  then 1 else 0 end) as verified,
            sum(case when ds_registration_no is not null and next_level_role_id=0 then 1 else 0 end) as approved,
            sum(case when ds_registration_no is not null and next_level_role_id<0 then 1 else 0 end) as rejected,
            sum(case when ds_registration_no is not null and next_level_role_id=9999 then 1 else 0 end) as faulty
            FROM ".$schemaname.".beneficiary b 
            where ds_registration_no is not null  and created_by_dist_code=".$districtCode;
           
            
           
            
            if(!empty($blockid)){
              $query .=" and created_by_local_body_code=".$blockid;
            }
            if(!empty($fromdate)){
              $query .=" and to_char(b.created_at,'".$dateFromat."')>='".$fromdate."'";
            }
            if(!empty($todate)){
              $query .=" and to_char(b.created_at,'".$dateFromat."')<='".$todate."'";
            }
          
            $query .="  group by b.created_by_local_body_code
            )x  on bl.block_code=x.created_by_local_body_code
            where district_code=".$districtCode;
           }
    
     //  echo $query;die;
    
        $data = DB::connection('pgsql')->select($query);
    //  $filterRecords = count($data);
      return datatables()->of($data)
     // ->setTotalRecords($totalRecords)
     // ->setFilteredRecords($filterRecords)
      ->addColumn('bsm', function ($data) use($rural_urbanid) {
       
        return $data-> bsm;
        
      

      })
      ->addColumn('total_applicant', function ($data) {
        if(empty($data->total_applicant)){
          return 0;
        }
       return $data->total_applicant;
      })
      ->addColumn('fresh_application', function ($data) {
        if(empty($data->fresh_application)){
          return 0;
        }
       return $data->fresh_application;
      })
      ->addColumn('verified', function ($data) {
        if(empty($data->verified)){
          return 0;
        }
        return $data->verified;
      })
   
      ->addColumn('rejected', function ($data) {
        if(empty($data->rejected)){
          return 0;
        }
        return $data->rejected;
      })
      ->addColumn('faulty', function ($data) {
        if(empty($data->faulty)){
          return 0;
        }
        return $data->faulty;
      })
     ->addColumn('approved', function ($data) {
      if(empty($data->approved)){
        return 0;
      }
      return $data->approved;
    })
  //   ->addColumn('total_cum_application', function ($data) {
  //     return intval($data->verified) + intval ($data->rejected) + intval($data->approved)  ;
     
  // })
  ->rawColumns(['bsm', 'total_applicant', 'rejected','approved','verified','faulty','fresh_application'])
      ->make(true);
    }


    public function datatableDistrictApplicationReport(Request $request){
      //  dd($request->all());
    
         $districtCode=$request->districtid;
           $blockid=$request->blockid;
           $schemeId=$request->schemeId;
           $fromdate=$request->fromdate;
           $todate=$request->todate;
           $schemaname=Scheme::where('id',$schemeId)->value('short_code');
           $dateFromat='DD/MM/YYYY';
         $query="select district_name,total_applicant,verified,approved,rejected,faulty,fresh_application from m_district bl
         left join
         (
         Select b.created_by_dist_code,
         sum(case when ds_registration_no is not null and next_level_role_id is null then 1 else 0 end) as fresh_application,
         sum(case when ds_registration_no is not null then 1 else 0 end) as total_applicant,
         sum(case when ds_registration_no is not null and next_level_role_id>0 and next_level_role_id!=9999  then 1 else 0 end) as verified,
         sum(case when ds_registration_no is not null and next_level_role_id=0 then 1 else 0 end) as approved,
         sum(case when ds_registration_no is not null and next_level_role_id<0 then 1 else 0 end) as rejected,
         sum(case when ds_registration_no is not null and next_level_role_id=9999 then 1 else 0 end) as faulty
         FROM ".$schemaname.".beneficiary b 
         where ds_registration_no is not null ";	
         
   
          if(!empty($districtCode)){
            $query .=" where created_by_dist_code=".$districtCode;
          }
        
         if(!empty($fromdate)){
           $query .=" and to_char(b.created_at,'".$dateFromat."')>='".$fromdate."'";
         }
         if(!empty($todate)){
           $query .=" and to_char(b.created_at,'".$dateFromat."')<='".$todate."'";
         }
         
         $query .="  group by b.created_by_dist_code
            )x  on bl.district_code=x.created_by_dist_code";
           

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
         ->addColumn('fresh_application', function ($data) {
          if(empty($data->fresh_application)){
            return 0;
          }
           return $data->fresh_application;
         })
         ->addColumn('total_applicant', function ($data) {
          if(empty($data->total_applicant)){
            return 0;
          }
           return $data->total_applicant;
         })
         ->addColumn('verified', function ($data) {
          if(empty($data->verified)){
            return 0;
          }
           return $data->verified;
         })
        ->addColumn('rejected', function ($data) {
          if(empty($data->rejected)){
            return 0;
          }
           return $data->rejected;
         })
        ->addColumn('approved', function ($data) {
          if(empty($data->approved)){
            return 0;
          }
         return $data->approved;
      
        })->addColumn('faulty', function ($data) {
          if(empty($data->faulty)){
            return 0;
          }
       return $data->faulty;
     })->rawColumns(['district_name','fresh_application', 'total_applicant', 'rejected','approved','faulty','verified'])
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
}
