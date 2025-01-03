<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
Use App\Taluka;
Use App\GP;
Use App\Ward;
Use App\District;
use App\BeneficiaryPensions;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\Scheme;
use App\UrbanBody;
use App\SubDistrict;
use App\Parijayi;
use App\ParijayiExport;
use App\ParijayiLot;
use App\parijayi_lot_seeder;
use App\ApplicationStatus;
use App\StatusCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\AuthChecker;


class ParijayiMISController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getStateReport(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)->first();
        $districts = District::select('district_code','district_name')->get();
        return view('Parijayi.consolidate_report')->with('districts',$districts);
    }


    // public function getMISData(Request $request){
    //   $district_code=$request->level1a;
    //   $rural_urban=$request->level3;

    //   $query ="";
    //   $data = array();


    //   if($district_code!="" || $district_code != null)
    //   {
    //     if($rural_urban != 'Urban'){ // Not Urban
    //       $localdata = array();
    //       $query = "select rural_urban as level, block_name as levelname, 
    //       count(id) as applied,
    //       count(id) FILTER(where status>1 and status<6) as approved,
    //       count(id) FILTER(where status=4) as mandate,
    //       count(id) FILTER(where status=5) as payment,
    //       count(id) FILTER(where status>5) as rejected
    //       from wbmt_beneficiary where rural_urban='Rural' and block_name is not null and district_code=".$district_code."
    //       group by block_name, rural_urban order by block_name";
    //       $localdata = DB::connection('pgsql_sp')->select($query);
    //       $data = array_merge($data,$localdata);
    //     }
    //     if($rural_urban != 'Rural'){ // Not Urban
    //       $localdata = array();
    //       $query = "select rural_urban as level, municipality_name as levelname, 
    //       count(id) as applied,
    //       count(id) FILTER(where status>1 and status<6) as approved,
    //       count(id) FILTER(where status=4) as mandate,
    //       count(id) FILTER(where status=5) as payment,          
    //       count(id) FILTER(where status>5) as rejected
    //       from wbmt_beneficiary where rural_urban='Urban' and municipality_name is not null and district_code="
    //       .$district_code.
    //       "group by municipality_name, rural_urban order by municipality_name";
    //       $localdata = DB::connection('pgsql_sp')->select($query);
    //       $data = array_merge($data,$localdata);
    //     }
    //   }
    //   else{
    //     $query = "select 'District' as level, district_name as levelname, 
    //     count(id) as applied,
    //     count(id) FILTER(where status>1 and status<6) as approved,
    //     count(id) FILTER(where status=4) as mandate,
    //     count(id) FILTER(where status=5) as payment,        
    //     count(id) FILTER(where status>5) as rejected
    //     from wbmt_beneficiary
    //     group by district_name order by district_name";
    //     $data = DB::connection('pgsql_sp')->select($query);
    //   }  

    //   return datatables()->of($data)
    //           ->make(true);
              
    // }
     
    public function getMISData(Request $request){
      $district_code=$request->level1a;
      $rural_urban=$request->level3;

      $query ="";
      $data = array();


      if($district_code!="" || $district_code != null)
      {
        if($rural_urban != 'Urban'){ // Not Urban
          $localdata = array();
          $query = "select level, levelname, applied, approved, mandate, payment, rejected, process, generated_on
                  from sp_consol_report where district_code=".$district_code." and 
                  level='Rural' order by levelname";
          $localdata = DB::connection('pgsql_sp')->select($query);
          $data = array_merge($data,$localdata);
        }
        if($rural_urban != 'Rural'){ // Not Urban
          $localdata = array();
          $query = "select level, levelname, applied, approved, mandate, payment, rejected, process, generated_on
                  from sp_consol_report where district_code=".$district_code." and 
                  level='Urban' order by levelname";
          $localdata = DB::connection('pgsql_sp')->select($query);
          $data = array_merge($data,$localdata);
        }
      }
      else{

        $query = "select level, levelname, applied, approved, mandate, payment, rejected, process, generated_on 
        from sp_consol_report_state order by levelname";
        // if($rural_urban != '')
        // {
        //   $query = $query." where level='".$rural_urban."'";
        // }
        //$query = $query." order by levelname";
        $data = DB::connection('pgsql_sp')->select($query);
      }  

      return datatables()->of($data)
              ->make(true);
              
    }

     //Duplicate MIS
    public function indexDuplicateMIS()
    {
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)->first();
        $district_code=$duty->district_code;
        $district_name=District::where('district_code',$district_code)->pluck('district_name')->first();
    
        return view('Parijayi.duplicate_mis')->with('district_name',$district_name)->with('district_code',$district_code);

    }
    public function getDuplicateRecord(Request $request)
    {
      $user_id = AuthChecker::getUserId();
      $district_code=$request->level1;
      $district_name=$request->level2;
      $serachvalue = $request->search['value'];

      
      //Urban/Rural
      $level=$request->level3;
      //LocalBody
      $localBody=$request->level1a;

      if($request->ajax()){

        $data = array();
        $limit = $request->input('length');
        $offset = $request->input('start');

        
        
        $mainquery = " select * from wbmt_beneficiary_duplicate where dist_code = ".$district_code;
        
        if(!empty($level)){
          //'Rural'
          if($level == 2){
            if(!empty($localBody)){
              $mainquery = $mainquery." and block_code = ".$localBody;
            }
          }
          //'Urban'
          if($level == 1){
            if(!empty($localBody)){
              $mainquery = $mainquery." and municipality_code = ".$localBody;
            }
          }
        }
    if(empty($serachvalue)){      
        if($limit >= 0){
          $mainquery = $mainquery." limit ".$limit." offset ".$offset;
        }
        $data = DB::connection('pgsql_sp')->select($mainquery);
        
      
        //RecordsCount
        $mainquery = " select count(1) from wbmt_beneficiary_duplicate where dist_code = ".$district_code;
        
        if(!empty($level)){
          //'Rural'
          if($level == 2){
            if(!empty($localBody)){
              $mainquery = $mainquery." and block_code = ".$localBody;
            }
          }
          //'Urban'
          if($level == 1){
            if(!empty($localBody)){
              $mainquery = $mainquery." and municipality_code = ".$localBody;
            }
          }
        }
        $totalRecordsResult = DB::connection('pgsql_sp')->select($mainquery);

        $totalRecords = 0;
        if($totalRecordsResult){
           $totalRecords = $totalRecordsResult[0]->count;
        }
     }else{
        $mainquery = $mainquery." and beneficiary_id = '".$serachvalue."'";
        $data = DB::connection('pgsql_sp')->select($mainquery);
        $totalRecords = count($data);
    }    
        return datatables()
          ->of($data)
          ->setTotalRecords($totalRecords)
          ->setFilteredRecords($totalRecords)
          ->skipPaging()
          ->make(true); 
      }
      return view('Parijayi.duplicate_mis')->with('district_name',$district_name)
                                            ->with('district_code',$district_code);

    } 


     //Duplicate Account No MIS
    public function indexDuplicateAccNoMIS()
    {
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)->first();
        $district_code=$duty->district_code;
        $district_name=District::where('district_code',$district_code)->pluck('district_name')->first();
    
        return view('Parijayi.duplicate_accno_mis')->with('district_name',$district_name)->with('district_code',$district_code);

    }
    public function getDuplicateAccNoRecord(Request $request)
    {
      $user_id = AuthChecker::getUserId();
      $district_code=$request->level1;
      $district_name=$request->level2;
      $serachvalue = $request->search['value'];

      //Urban/Rural
      $level=$request->level3;
      //LocalBody
      $localBody=$request->level1a;

      if($request->ajax()){

        $data = array();
        $limit = $request->input('length');
        $offset = $request->input('start');

      
        
        $mainquery = "select * from wbmt_beneficiary
          where beneficiary_id in(
          select beneficiary_id from 
          (select * from wbmt_beneficiary where status < 3) a
          inner join(
          select account_no, count(1) from wbmt_beneficiary 
          where status < 6  and coalesce(trim(account_no),'') <> '' group by account_no having count(1)>1) b
          on a.account_no=b.account_no) and status < 3 and coalesce(duplicate_status,'')!='R'
          and district_code=".$district_code."order by account_no";
        
        if(!empty($level)){
          //'Rural'
          if($level == 2){
            if(!empty($localBody)){
              $mainquery = $mainquery." and block_code = ".$localBody;
            }
          }
          //'Urban'
          if($level == 1){
            if(!empty($localBody)){
              $mainquery = $mainquery." and municipality_code = ".$localBody;
            }
          }
        }
        
    if(empty($serachvalue)){      
        if($limit >= 0){
          $mainquery = $mainquery." limit ".$limit." offset ".$offset;
        }
        $data = DB::connection('pgsql_sp')->select($mainquery);
        
      
        //RecordsCount
        $mainquery = "select count(1) from wbmt_beneficiary
        where beneficiary_id in(
        select beneficiary_id from 
        (select * from wbmt_beneficiary where status < 3) a
        inner join(
        select account_no, count(1) from wbmt_beneficiary 
        where status < 6  and coalesce(trim(account_no),'') <> '' group by account_no having count(1)>1) b
        on a.account_no=b.account_no) and status < 3 and coalesce(duplicate_status,'')!='R'
        where district_code=".$district_code;
        
        if(!empty($level)){
          //'Rural'
          if($level == 2){
            if(!empty($localBody)){
              $mainquery = $mainquery." and block_code = ".$localBody;
            }
          }
          //'Urban'
          if($level == 1){
            if(!empty($localBody)){
              $mainquery = $mainquery." and municipality_code = ".$localBody;
            }
          }
        }
        $totalRecordsResult = DB::connection('pgsql_sp')->select($mainquery);

        $totalRecords = 0;
        if($totalRecordsResult){
           $totalRecords = $totalRecordsResult[0]->count;
        }
    }else{
        $mainquery = $mainquery." and beneficiary_id = '".$serachvalue."'";
        $data = DB::connection('pgsql_sp')->select($mainquery);
        $totalRecords = count($data);
    }
        return datatables()
          ->of($data)
          ->setTotalRecords($totalRecords)
          ->setFilteredRecords($totalRecords)
          ->skipPaging()
          ->addColumn('case_type', function ($data) {
            if($data->status == 1){
              return '<span class="label label-primary">NEW</span><span class="badge">New</span>';
            }elseif($data->status == 2){
              return '<span class="label label-primary">NEW</span><span class="badge">Approved</span>';
            }elseif($data->status == 3){
              return '<span class="label label-primary">NEW</span><span class="badge">Added to Lot</span>';
            }elseif($data->status == 4){
              return '<span class="label label-primary">NEW</span><span class="badge">Generated Lot</span>';
            }
            })
          ->rawColumns(['case_type'])
          ->make(true); 
      }
      return view('Parijayi.duplicate_accno_mis')->with('district_name',$district_name)
                                            ->with('district_code',$district_code);

    } 
}