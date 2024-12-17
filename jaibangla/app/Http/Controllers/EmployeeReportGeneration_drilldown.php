<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\nhm_employee_details;
use App\District;
use App\UrbanBody;
use App\Taluka;
use App\nhm_health_facility;

class EmployeeReportGeneration extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$report = nhm_employee_details::All();
        $reports = DB::table('nhm_employee_details')->paginate(5);
        //$flag=0;
        return view('employee-report/index', ['reports' => $reports]);

        //return view('employee-report/index')->with('flag',$flag);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function loadlevel2(Request $request, $level_name){
        //dd($level_name);
        if ($level_name=="State"){
             $list=[array("id"=>1,"name"=>"West Bengal")];

        }elseif ($level_name=="District") {
             $list=District::get(['district_code AS id','district_name AS name']);
            
        }elseif($level_name=="ULB"){
             $list=UrbanBody::get(['urban_body_code AS id','urban_body_name AS name']);
            
        }else{
            $list=Taluka::get(['taluka_code AS id','taluka_name AS name']);
             
        }
       

        return response()->json($list);
    }


    public function loadlevel3(Request $request, $level_name){
        //dd($level_name);
        if ($level_name=="State"){
             $list=array("SPMU","MCH");

        }elseif ($level_name=="District") {
             $list=array("DPMU","Hospital");
        }elseif($level_name=="ULB"){
             $list=array("CPMU","UPHC");
            
        }else{
            $list=array("BPMU","Subcenter");
             
        }
       

        return response()->json($list);
    }

     public function loadlevel4(Request $request,$reprotlevel1_data,$reprotlevel2_data,$reprotlevel3_data){
        //dd($level_name);
        $level1=$reprotlevel1_data;
        $level2=$reprotlevel2_data;
        $level3=$reprotlevel3_data;

        if ($level1=="State"){
            if($level3=="SPMU"){            
                $postingPlaces=[array("id"=>1,"name"=>"No Data")]; 
            }else{
                $facility_type=["MCH"];
                $postingPlaces = nhm_health_facility::whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }

        }elseif ($level1=="District") {
            if($level3=="DPMU"){            
                $postingPlaces=[array("id"=>1,"name"=>"No Data")]; 
            }else{

                 $facility_type=["DH","MCH","SDH","SGH"];
                 $postingPlaces = nhm_health_facility::where('district_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }
        }elseif($level1=="ULB"){
             if($level3=="CPMU"){            
                $postingPlaces=[array("id"=>1,"name"=>"No Data")]; 
            }else{
                 $facility_type=["UPHC"];  
                 $postingPlaces = nhm_health_facility::where('taluka_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }
            
        }else{
           
             if($level3=="BPMU"){            
                $postingPlaces=[array("id"=>1,"name"=>"No Data")]; 
            }else{
                 $facility_type=["SC"];
                 $postingPlaces = nhm_health_facility::where('taluka_code', '=', $level2)->whereIn('facility_type', $facility_type)->get(['facility_name as name','facilty_code as id']);
            }
        }
       

        return response()->json($postingPlaces);
    }

    public function loadreportistrict(Request $request){

        $level1=$request->level1;
        $level2=$request->level2;
        $level3=$request->level3;
        $level4=$request->level4;
        $flag=1;
        $constraints = [
            'level1' => $request['level1'],
            'level2' => $request['level2'],
            ];
        
       

        $result=DB::table('m_health_facility')->where('district_code',$level2);
            // ->join('nhm_employee_details', 'nhm_employee_details.posting_place_code', '=', 'm_health_facility.facilty_code')
            // ->get(['first_name','middle_name','last_name','guardian_name','appointing_authority','posting_level','posting_place','created_at']);

        
     
        dd($result);
       
        return view('employee-report/index', ['employeedatas' => $employeedatas, 'searchingVals' => $constraints])->with('flag',$flag);
    }
    
}
