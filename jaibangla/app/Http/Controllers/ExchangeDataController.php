<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configduty;
use App\MapLavel;
use App\PensionSc;
use App\PensionSt;
use App\PensionFisherman;
use App\PensionMSME;
use App\PensionTextile;
use App\District;
use App\Taluka;
use App\Ward;
use App\UrbanBody;
use App\GP;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\BenDocsSc;
use App\BenDocsSt;

use App\BenDocsManabikWCD;
use App\BenDocsOAPWCD;
use App\BenDocsWPWCD;

use App\DocumentType;
use App\BenDocsPrachesta;
use App\BenDocsFisherman;
use App\BenDocsMSME;
use App\BenDocsTextile;
use App\Helpers\AuthChecker;


class ExchangeDataController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }
    public function shemeSelection(Request $request){
      /*echo "<pre>";  
      //print_r($data);
      $bodyCode = $request->session()->get('bodyCode');
      echo $bodyCode;
      echo "</pre>";
      die();*/

      return view('exchange-scheme-selection/main');
    }

    public function shemeSessionCheck(Request $request){

      $user_id = AuthChecker::getUserId();
      $scheme_id=0;
      $ben_table="";
      if($request->get('pr1')){
        if($request->get('pr1')=="wcd"){
            $scheme_id=$request->get('wcd_type');
            if($scheme_id==2){
              $ben_table="PensionManabikWCD";
            }
            else if($scheme_id==10){
              $ben_table="PensionOAPWCD";
            }
           else if($scheme_id==11){
              $ben_table="PensionWPWCD";
            }
        }
        else
        {
            return view('exchange-scheme-selection/main');
        }

      }else{
        return redirect("exchange-scheme-selection/main");
      }

      $is_active=0;
      $roleArray=$request->session()->get('role');    
          
      foreach ($roleArray as $roleObj) {           
        if($roleObj['scheme_id'] == $scheme_id){
          $is_active=1;
          $request->session()->put('level', $roleObj['mapping_level']);
          $request->session()->put('distCode', $roleObj['district_code']); 
          $request->session()->put('scheme_id',$scheme_id);
          $request->session()->put('ben_table',$ben_table);        
          $request->session()->put('is_first',$roleObj['is_first']); 
          $request->session()->put('is_urban',$roleObj['is_urban']); 
          $request->session()->put('role_id',$roleObj['id']);                 
          if($roleObj['is_urban']==1){              
              $request->session()->put('bodyCode', $roleObj['urban_body_code']);
          }else{
              $request->session()->put('bodyCode', $roleObj['taluka_code']);
          }     
          break;
        }

      }

      if($is_active==1){        
          
          return true;
  
      }else{

          return false;
      }
      return view('exchange-scheme-selection/main');
    }

    public function applicationdetails(Request $request){
      //$this->shemeSessionCheck($request);
  if($this->shemeSessionCheck($request)){
        $scheme_id = $request->session()->get('scheme_id');
        $ben_table = $request->session()->get('ben_table');
        $mappingLevel = $request->session()->get('level');
        $district_code = $request->session()->get('distCode');
        $is_first = $request->session()->get('is_first');
        $is_urban = $request->session()->get('is_urban');
        $urban_body_code = $request->session()->get('bodyCode');
        $taluka_code = $request->session()->get('bodyCode');
        $role_id=$request->session()->get('role_id');
      $user_id = AuthChecker::getUserId();

      //$duty = Configduty::where('user_id','=',$user_id)->where('scheme_id',$scheme_id)->first();
      //$role=MapLavel::where('scheme_id',$scheme_id)->where('role_name',Auth::user()->designation_id)->where('stack_level',$duty->mapping_level)->first();
  //dd($role);

      if($is_first){   // First Level Verifier    
 
        //$mappingLevel=$duty->mapping_level;

          if($mappingLevel=="State"){
             $level="State";
          }else if($mappingLevel=="District"){

             //$district_code = $duty->district_code;
               $appPrefix = "App";
               $modelName=$appPrefix . "\\" . $ben_table;
             $rows = $modelName::where('next_level_role_id',null)->where('created_by_dist_code',$district_code)->orderBy('id', 'desc')->paginate(10);
          return view('pension_list',['nhm_employee_details' => $rows]); 

          }else if($mappingLevel=="Subdiv"){
             //dd("i am here");
                //$is_urban=$duty->is_urban;
              if($is_urban==1){

                $duty_level="SubdivVerifier";
                //$district_code=$duty->district_code;
                //$sub_district_code = $duty->urban_body_code;
    
                $urban_bodys=UrbanBody::where('sub_district_code',$urban_body_code)->select('urban_body_code','urban_body_name')->get();

                $urban_body_codes=[];
                $i=0;
                foreach($urban_bodys as $urban_body)
                {
                    
                  $urban_body_codes[$i] = $urban_body->urban_body_code;
                  $i++;
                }

                    if(request()->ajax())
                     {  
                       if(!empty($request->filter_1)  && empty($request->filter_2) )
                        { //dd($urban_body_codes);
                            
                           $body_code = $request->session()->get('bodyCode');
                            $appPrefix = "App";
                            $modelName=$appPrefix . "\\" . $ben_table;
                            $data=$modelName::where('next_level_role_id',null)
                              ->where('created_by_local_body_code',$body_code)
                              ->where('block_ulb_code', $request->filter_1)
                              //->where('gp_ward_code', $request->filter_2)
                              ->orderBy('id', 'desc')
                              //->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")
                              ->get();



                          /****
                            $data = DB::table('pension.beneficiary')
                             ->where('next_level_role_id',null)
                             ->where(function ($query) use ($urban_body_codes){
                              foreach($urban_body_codes as $urban_body_code) {
                                 $query->Where('created_by_local_body_code', '=', $urban_body_code);
                                 
                              }
                            })->where('block_ulb_code', $request->filter_1)
                             //->where('gp_ward_code', $request->filter_2)
                             ->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")
                             ->get();***/
                            
                        }
                        elseif(!empty($request->filter_1) && !empty($request->filter_2))
                        {
                          
                          $body_code = $request->session()->get('bodyCode');
                            $appPrefix = "App";
                            $modelName=$appPrefix . "\\" . $ben_table;
                            $data=$modelName::where('next_level_role_id',null)
                              ->where('created_by_local_body_code',$body_code)
                              ->where('block_ulb_code', $request->filter_1)
                              ->where('gp_ward_code', $request->filter_2)
                              ->orderBy('id', 'desc')
                              //->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")
                              ->get();


                          /****
                          $data = DB::table('pension.beneficiary')
                             ->where('next_level_role_id',null)
                             ->where(function ($query) use ($urban_body_codes){
                              foreach($urban_body_codes as $urban_body_code) {
                                 $query->Where('created_by_local_body_code', '=', $urban_body_code);
                                 
                              }
                            })
                             ->where('block_ulb_code', $request->filter_1)
                             ->where('gp_ward_code', $request->filter_2)
                             ->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")
                             ->get();
                            // dd($data);**/

                        }
                        else
                        {


                            $body_code = $request->session()->get('bodyCode');
                            $appPrefix = "App";
                            $modelName=$appPrefix . "\\" . $ben_table;
                            $data=$modelName::where('next_level_role_id',null)
                              ->where('created_by_local_body_code',$body_code)
                              ->orderBy('id', 'desc')
                              //->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")
                              ->get();



                            // $data=DB::table('pension.beneficiary')->where('next_level_role_id',null)
                            //  ->where(function ($query) use ($urban_body_codes){
                            //   foreach($urban_body_codes as $urban_body_code) {
                            //      $query->Where('created_by_local_body_code', '=', $urban_body_code);
                                 
                            //   }
                            // })->orderBy('id', 'desc')
                            //   ->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")
                            //   ->get();


                         }
                        return datatables()->of($data)
                            ->addColumn('view', function ($data) {
                                return '<a href="'. route('nhmemployee.showApplicantDetails', $data->id) .'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
                                })
                            ->addColumn('id', function ($data) {
                                return $data->getBenidAttribute();
                                })
                            ->addColumn('name', function ($data) {
                                return $data->getName();
                                })
                            // ->addColumn('check', function ($data) {
                            //     return '<input type="checkbox" name="approvalcheck[]" onchange="document.getElementById(\'bulk_approve\').disabled = !this.checked;" value="'.$data->id.'">';
                            //     })
                            ->rawColumns(['view','id','name'])
                            ->make(true); 
                     }
                    return view('linelisting_verified_subdiv')->with('duty_level',$duty_level)->with('urban_bodys',$urban_bodys)->with('dist_code',$district_code);  


                  /*********old code before filer
                  $district_code = $duty->district_code;
                  $urban_body_code = $duty->urban_body_code;
                  $level="Subdiv";
                    $rows = PensionSc::where('next_level_role_id',null)
                        ->where('created_by_local_body_code',$urban_body_code)
                        ->orderBy('id', 'desc')->paginate(10);
                    
                    /*echo "<pre>";
                    print_r($rows);
                    echo "</pre>";
                    die();   */ 

                    
              }else{ 
                                 
                    //$taluka_code = $duty->taluka_code;
                    $appPrefix = "App";
                    $modelName=$appPrefix . "\\" . $ben_table;
                  $rows = $modelName::where('next_level_role_id',null)
                    ->where('created_by_local_body_code',$taluka_code)
                    ->orderBy('id', 'desc')->paginate(10);
                  
                  return view('pension_list',['nhm_employee_details' => $rows]);
                    
              }
              
          }else if($mappingLevel=="Block"){
                    $duty_level="BlockVerifier";
                    //$district_code=$duty->district_code;
                    //$taluka_code = $duty->taluka_code;
    
                    $gps=GP::where('block_code',$taluka_code)->select('gram_panchyat_code','gram_panchyat_name')->get();

                    if(request()->ajax())
                     {  
                          if(!empty($request->filter_1))
                          {

                            $body_code = $request->session()->get('bodyCode');
                            $appPrefix = "App";
                            $modelName=$appPrefix . "\\" . $ben_table;
                            $data=$modelName::where('next_level_role_id',null)
                              ->where('created_by_local_body_code',$body_code)
                              //->where('block_ulb_code', $request->filter_2)
                             ->where('gp_ward_code', $request->filter_1)
                             //->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")
                             ->get();
                           /**
                           $data = DB::table('pension.beneficiary')
                             ->where('next_level_role_id',null)
                             ->where('created_by_local_body_code',$taluka_code)
                             //->where('block_ulb_code', $request->filter_2)
                             ->where('gp_ward_code', $request->filter_1)
                             ->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")
                             ->get();
                             **/
                            
                          }

                        else
                        {   
                            $body_code = $request->session()->get('bodyCode');
                            $appPrefix = "App";
                            $modelName=$appPrefix . "\\" . $ben_table;
                            $data=$modelName::where('next_level_role_id',null)
                                 ->where('created_by_local_body_code',$body_code)->get();
                            //dd($data);
                            // where('next_level_role_id',null)
                            //   ->where('created_by_local_body_code',$body_code)
                            //   ->with(['getBenidAttribute','id'])->get();
                            /*$data=$modelName::where('next_level_role_id',null)
                              ->where('created_by_local_body_code',$body_code)
                              ->orderBy('id', 'desc')
                              ->getBenidAttribute()
                              ->getName()
                             ->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")
                             ->get();**/
                          /****
                            $data=DB::table('pension.beneficiary')->where('next_level_role_id',null)
                            ->where('created_by_local_body_code',$taluka_code)->orderBy('id', 'desc')
                            ->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")
                           ->get();
                          *****/
                         }
                        return datatables()->of($data)
                            ->addColumn('view', function ($data) {
                                return '<a href="'. route('nhmemployee.showApplicantDetails', $data->id) .'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
                                })
                            ->addColumn('id', function ($data) {
                                return $data->getBenidAttribute();
                                })
                            ->addColumn('name', function ($data) {
                                return $data->getName();
                                })
                            // ->addColumn('check', function ($data) {
                            //     return '<input type="checkbox" name="approvalcheck[]" onchange="document.getElementById(\'bulk_approve\').disabled = !this.checked;" value="'.$data->id.'">';
                            //     })
                            ->rawColumns(['view','id','name'])
                            ->make(true); 
                     }
                    return view('linelisting_verified')->with('duty_level',$duty_level)->with('gps',$gps)
                                                ->with('dist_code',$district_code);       

            }

            //return view('pension_list',['nhm_employee_details' => $rows]);
      }else{

        //$mappingLevel=$duty->mapping_level;
          if($mappingLevel=="State"){
             $duty_level="State";
          }else if($mappingLevel=="District"){

            $modelName="App\\" . $ben_table;
                      $data=$modelName::where('next_level_role_id',null) //old $role_id
                               ->where('created_by_dist_code',$district_code)
                               ->where('created_by_local_body_code', 2828)
                              //->where('gp_ward_code', $request->filter_1)
                              //->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")                       
                             ->get();

            /*echo "<pre>";  
                print_r($data);
                echo "</pre>";
                die();*/

            $duty_level='DistrictApprover';

            $levels = [
                         2 => 'Rural',
                         1 => 'Urban',
   
                ];
            //$district_code = $duty->district_code;

                
                
            if(request()->ajax())
            {  

                  if(!empty($request->filter_1) && !empty($request->filter_2))
                  {

                    
                    if($request->filter_1 == '2'){
                      $body_code = $request->session()->get('bodyCode');
                      $appPrefix = "App";
                      $modelName=$appPrefix . "\\" . $ben_table;
                      $data=$modelName::where('next_level_role_id',null) //old $role_id
                               ->where('created_by_dist_code',$district_code)
                               ->where('created_by_local_body_code', $request->filter_2)
                              //->where('gp_ward_code', $request->filter_1)
                              //->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")                       
                             ->get();


                       }
                       else
                       {
                         $body_code = $request->session()->get('bodyCode');
                      $appPrefix = "App";
                      $modelName=$appPrefix . "\\" . $ben_table;
                      $data=$modelName::where('next_level_role_id',null) //old $role_id
                               ->where('created_by_dist_code',$district_code)
                               ->where('created_by_local_body_code', $request->filter_2)->get() ;
                               
                       }  
                   /**** 
                   $data = DB::table('pension.beneficiary')
                     ->where('next_level_role_id',$role->id)
                     ->where('created_by_dist_code',$district_code)
                     ->where('block_ulb_code', $request->filter_2)
                     //->where('gp_ward_code', $request->filter_2)
                     ->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")
                     ->get();
                     //dd($data->id);***/

                    //$show =  route('nhmemployee.showSingleEmployeeReport',1);
                  }

                else
                {

                  
                  $body_code = $request->session()->get('bodyCode');
                  $appPrefix = "App";
                  $modelName=$appPrefix . "\\" . $ben_table;
                  $data=$modelName::where('next_level_role_id',null) //old $role_id
                              ->where('created_by_dist_code',$district_code)
                             ->orderBy('id', 'desc')
                             //->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,benid,dob,gender,dist_name,assembly_name")
                             ->get();
                  /*****
                    $data=DB::table('pension.beneficiary')->where('next_level_role_id',$role->id)
                    ->where('created_by_dist_code',$district_code)->orderBy('id', 'desc')
                    ->selectRaw("CONCAT(ben_fname,' ',ben_mname,' ',ben_lname) as name,id,dob,gender,dist_name,assembly_name")
                   ->get();
                  *****/
                   //$show =  route('nhmemployee.showSingleEmployeeReport',1);



                 }


                return datatables()->of($data)
                    ->addColumn('view', function ($data) {
                        return '<a href="#" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
                        return '<a href="'. route('nhmemployee.showApplicantDetails', $data->id) .'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
                        })
                    ->addColumn('check', function ($data) {/*
                        return '<input type="checkbox" name="approvalcheck[]" onchange="document.getElementById(\'bulk_approve\').disabled = !this.checked;" value="'.$data->id.'">';*/

                        return '<input type="checkbox" id="select_all" name="select_all" onchange="document.getElementById(\'bulk_approve\').disabled = !this.checked;document.getElementById(\'bulk_blkchange\').disabled = !this.checked;">';
                        })
                    ->addColumn('id', function ($data) {
                                return $data->getBenidAttribute();
                                })
                    ->addColumn('name', function ($data) {
                                return $data->getName();
                                })
                    ->rawColumns(['view','check','id','name'])
                    ->make(true); 
             }

                   
            return view('linelisting_exchange')->with('duty_level',$duty_level)->with('levels',$levels)
                                                ->with('dist_code',$district_code)->with('scheme',$scheme_id);


            }else{

              /*echo "<pre>";  
                print_r($data);
                echo "</pre>";
                die();*/
              //$is_urban=$duty->is_urban;
              if($is_urban==1){
                  //$district_code = $duty->district_code;
                  //$urban_body_code = $duty->urban_body_code;
                  $duty_level="ULB";
              }else{
                  $duty_level="Block";
                  //$district_code = $duty->district_code;
                  //$taluka_code = $duty->taluka_code;
                    $appPrefix = "App";
                    $modelName=$appPrefix . "\\" . $ben_table;
                  
                  $rows = $data=$modelName::where('next_level_role_id',null) //old $role_id
                          ->where('created_by_local_body_code',$taluka_code)
                          ->orderBy('id', 'desc')
                          ->paginate(10);
            //return view('pension_list',['nhm_employee_details' => $rows]);
            return view('linelisting_exchange',['datas' => $rows,'dist_code' => $district_code]);
              }
              
          }
          //  return view('linelisting_approved',['datas' => $rows]);
      }
  }else{
          return redirect('/')->with('success', 'User Disabled for this scheme');
      }
         

    }

      
public function loadWard(Request $request,$municipality){
          
  $wards=Ward::where('urban_body_code',$municipality)->get(['urban_body_ward_code as id','urban_body_ward_name as name']);

  return response()->json($wards);
}


public function MassEmployeeApproval(Request $request){

    $this->shemeSessionCheck($request);
        $scheme_id = $request->session()->get('scheme_id');
        $ben_table = $request->session()->get('ben_table');
        $mappingLevel = $request->session()->get('level');
        $district_code = $request->session()->get('distCode');
        $is_first = $request->session()->get('is_first');
        $is_urban = $request->session()->get('is_urban');
        $urban_body_code = $request->session()->get('bodyCode');
        $taluka_code = $request->session()->get('bodyCode');
        $role_id=$request->session()->get('role_id');
      $user_id = AuthChecker::getUserId();

    $id=$request->benId;
        $Verified="Verified";
        $Rejected="Rejected";
        $comments=$request->comments;
        
        //$scheme_id = 3;
      $user_id = AuthChecker::getUserId();      
      $duty = Configduty::where('user_id','=',$user_id)->where('scheme_id',$scheme_id)->first();
      $role=MapLavel::where('scheme_id',$scheme_id)->where('role_name',Auth::user()->designation_id)->where('stack_level',$duty->mapping_level)->first();

        $inputs = request()->input('approvalcheck');
       
        foreach($inputs as $input){


        $input_update = ['next_level_role_id' => $role->parent_id]; 

        $appPrefix = "App";
        $modelName=$appPrefix . "\\" . $ben_table;     
        $is_pushed=$modelName::where('id', $input)->update($input_update);   
      
        }
        
         if($is_pushed){
            return redirect('workflow')->with('message','Succesfull!');
            }
       
    }

//      public function showSingleEmployeeReport(Request $request)
//     {
        
//         $this->shemeSessionCheck($request);
//         $scheme_id = $request->session()->get('scheme_id');
//         $ben_table = $request->session()->get('ben_table');
//         $mappingLevel = $request->session()->get('level');
//         $district_code = $request->session()->get('distCode');
//         $is_first = $request->session()->get('is_first');
//         $is_urban = $request->session()->get('is_urban');
//         $urban_body_code = $request->session()->get('bodyCode');
//         $taluka_code = $request->session()->get('bodyCode');
//         $role_id=$request->session()->get('role_id');
//       $user_id = AuthChecker::getUserId();


//         $id=$request->id;
//         $appPrefix = "App";
//         $modelName=$appPrefix . "\\" . $ben_table;
//         $single_employee_details = $modelName::where('id','=',$id)->first();//find($id);
        
//         // if($single_employee_details->approval_status == "Approved"){
//         //     $single_employee_details = NHMEmployee::where('application_id','=',$id)->first();
//         // }

//         // return Redirect::back()->with(['single_employee_details'=>$single_employee_details,'flag'=>$flag]);
        

// //dd($single_employee_details);

//         return view('pension_view_details_display', ['row' => $single_employee_details]);
//     }


    public function showSingleEmployeeReport(Request $request)
    {
        
        $this->shemeSessionCheck($request);
        $scheme_id = $request->session()->get('scheme_id');
        $ben_table = $request->session()->get('ben_table');
        $mappingLevel = $request->session()->get('level');
        $district_code = $request->session()->get('distCode');
        $is_first = $request->session()->get('is_first');
        $is_urban = $request->session()->get('is_urban');
        $urban_body_code = $request->session()->get('bodyCode');
        $taluka_code = $request->session()->get('bodyCode');
        $role_id=$request->session()->get('role_id');
      $user_id = AuthChecker::getUserId();


        $id=$request->id;
        $appPrefix = "App";
        $modelName=$appPrefix . "\\" . $ben_table;
        $row = $modelName::where('id','=',$id)->first();//find($id);


        if($scheme_id==3){
          //$row = PensionSc::find($id);           
          $docs = BenDocsSc::where('ben_id',$id)->get();   
                
        }else if($scheme_id==1){
            //$row = PensionSt::find($id);
          $docs = BenDocsSt::where('ben_id',$id)->get();
        }else if($scheme_id==4){
              $docs = BenDocsPrachesta::where('ben_id',$id)->get(); 
        }else if($scheme_id==5){
        //$row = Manabik::find($id);
        $docs = BenDocsFisherman::where('ben_id',$id)->get(); 
        }else if($scheme_id==6){
          //$row = Manabik::find($id);
          $docs = BenDocsMSME::where('ben_id',$id)->get(); 
        }else if($scheme_id==7){
          //$row = Manabik::find($id);
          $docs = BenDocsTextile::where('ben_id',$id)->get(); 
        }
        if($row->dist_code !="")
        {
         $district = District::where('district_code','=',$row->dist_code)->get(['district_code','district_name'])->first(); 
         $district_name = $district->district_name; 
         }
 
         if($row->block_ulb_code !="")
         { 
             if($row->rural_urban_id == 1)  
             {
             $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
             $block_name = $block->urban_body_name;
             
             }
             else
             {
             $block= Taluka::where('block_code','=',$row->block_ulb_code)->first();
             $block_name = $block->block_name;
 
             }
         
         }
         if($row->gp_ward_code !="")
         {
             if($row->rural_urban_id == 1)  
             {
             $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
             $gp_name =  $gp_ward->urban_body_ward_name;
            
             }
             else
             {
             $gp = GP::where('gram_panchyat_code','=',$row->gp_ward_code)->get(['gram_panchyat_code','gram_panchyat_name'])->first();
             $gp_name =  $gp->gram_panchyat_name;
 
             }
         
         }
         //change by rajib 19-3 end
         $doc_profile_image = DocumentType::get()
                                     ->where("is_profile_pic",true)->first();
         $doc_profile_image_id = 999;
         if($doc_profile_image){
             $doc_profile_image_id = $doc_profile_image->id;
         } 
         if($scheme_id ==5)
         return view('fisherman/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
         elseif($scheme_id ==6)
         return view('msme/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
         elseif($scheme_id ==7)
         return view('textile/pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
         else
         return view('pension_view_details_read_only', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
        
    
    }


    public function showApplicantDetails(Request $request){
        $this->shemeSessionCheck($request);
        $scheme_id = $request->session()->get('scheme_id');
        $ben_table = $request->session()->get('ben_table');
        $mappingLevel = $request->session()->get('level');
        $district_code = $request->session()->get('distCode');
        $is_first = $request->session()->get('is_first');
        $is_urban = $request->session()->get('is_urban');
        $urban_body_code = $request->session()->get('bodyCode');
        $taluka_code = $request->session()->get('bodyCode');
        $role_id=$request->session()->get('role_id');
        $user_id = AuthChecker::getUserId();

        $id=$request->id;
        $appPrefix = "App";
        $modelName=$appPrefix . "\\" . $ben_table;
        $row = $modelName::where('id','=',$id)->first();

        if($scheme_id==3){
         //$row = PensionSc::find($id);           
         $docs = BenDocsSc::where('ben_id',$id)->get();   
               
          }else if($scheme_id==1){
             //$row = PensionSt::find($id);
             $docs = BenDocsSt::where('ben_id',$id)->get();

          }else if($scheme_id==4){
             //$row = Manabik::find($id);          
          $docs = BenDocsPrachesta::where('ben_id',$id)->get();
        }else if($scheme_id==5){
          //$row = Manabik::find($id);          
          $docs = BenDocsFisherman::where('ben_id',$id)->get();
        }else if($scheme_id==6){
          //$row = Manabik::find($id);          
          $docs = BenDocsMSME::where('ben_id',$id)->get();
        }else if($scheme_id==7){
          //$row = Manabik::find($id);          
          $docs = BenDocsTextile::where('ben_id',$id)->get();
        }
        else if($scheme_id==2){
          //$row = Manabik::find($id);          
          $docs = BenDocsManabikWCD::where('ben_id',$id)->get();
        }
        else if($scheme_id==10){
          //$row = Manabik::find($id);          
          $docs = BenDocsOAPWCD::where('ben_id',$id)->get();
        }
        else if($scheme_id==11){
          //$row = Manabik::find($id);          
          $docs = BenDocsWPWCD::where('ben_id',$id)->get();
        }
        if($row->dist_code !="")
        {
        $district = District::where('district_code','=',$row->dist_code)->get(['district_code','district_name'])->first(); 
        $district_name = $district->district_name; 
        }

        if($row->block_ulb_code !="")
        { 
            if($row->rural_urban_id == 1)  
            {
            $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
            $block_name = $block->urban_body_name;
            
            }
            else
            {
            $block= Taluka::where('block_code','=',$row->block_ulb_code)->first();
            $block_name = $block->block_name;

            }
        
        }
        if($row->gp_ward_code !="")
        {
            if($row->rural_urban_id == 1)  
            {
            $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
            $gp_name =  $gp_ward->urban_body_ward_name;
           
            }
            else
            {
            $gp = GP::where('gram_panchyat_code','=',$row->gp_ward_code)->get(['gram_panchyat_code','gram_panchyat_name'])->first();
            $gp_name =  $gp->gram_panchyat_name;

            }
        
        }
        //change by rajib 19-3 end
        $doc_profile_image = DocumentType::get()
                                    ->where("is_profile_pic",true)->first();
        $doc_profile_image_id = 999;
        if($doc_profile_image){
            $doc_profile_image_id = $doc_profile_image->id;
        } 


        if($scheme_id==5)
        return view('fisherman/pension_view_details', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
        else if($scheme_id==2)
        return view('MANABIKWCD/pension_view_details', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
        else if($scheme_id==10)
        return view('OAPWCD/pension_view_details', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
        else if($scheme_id==11)
        return view('WPWCD/pension_view_details', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
        else if($scheme_id==6)
        return view('msme/pension_view_details', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
        else if($scheme_id==7){
    return view('textile/pension_view_details', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);
        }
  else
        return view('pension_view_details', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name,'docs'=>$docs,'image_id'=>$doc_profile_image_id]);

    }




public function verifydata(Request $request)
    {
        $this->shemeSessionCheck($request);
        $scheme_id = $request->session()->get('scheme_id');
        $ben_table = $request->session()->get('ben_table');
        $mappingLevel = $request->session()->get('level');
        $district_code = $request->session()->get('distCode');
        $is_first = $request->session()->get('is_first');
        $is_urban = $request->session()->get('is_urban');
        $urban_body_code = $request->session()->get('bodyCode');
        $taluka_code = $request->session()->get('bodyCode');
        $role_id=$request->session()->get('role_id');
        $user_id = AuthChecker::getUserId();
        
        $id=$request->benId;
        $Verified="Verified";
        $Rejected=1;
        $comments=$request->comments;
        
        //$scheme_id = 3;
      $user_id = AuthChecker::getUserId();      
      $duty = Configduty::where('user_id','=',$user_id)->where('scheme_id',$scheme_id)->first();
      $role=MapLavel::where('scheme_id',$scheme_id)->where('role_name',Auth::user()->designation_id)->where('stack_level',$duty->mapping_level)->first();

      /*echo "<pre>";
        echo $id;
      //print_r($id);
      echo "</pre>";
      die();*/

        if ($_POST['submit'] == 'Verify') {
            $input = ['next_level_role_id' => $role->parent_id,'comments' => $comments];  
            $appPrefix = "App";
            $modelName=$appPrefix . "\\" . $ben_table;          
            $is_status_updated=$modelName::where('id', $id)->update($input);            
            if($is_status_updated){
              return redirect()->intended('workflow')->with('message','Forwarded Succesfully!');            
            }
        
        }else if ($_POST['submit'] == 'Reject') {
            $input = [
            'verification_rejected' => $Rejected,'comments' => $comments,'next_level_role_id' => -1,];
            $appPrefix = "App";
            $modelName=$appPrefix . "\\" . $ben_table; 
            $is_status_updated=$modelName::where('id', $id)->update($input);  
            //->update($input);
            if($is_status_updated){
               return redirect()->intended('workflow')->with('message','Rejected Succesfully!');
            } 
       
        }
    }
  
  
  public function approvedata(Request $request)
    {

        $this->shemeSessionCheck($request);
        $scheme_id = $request->session()->get('scheme_id');
        $ben_table = $request->session()->get('ben_table');
        $mappingLevel = $request->session()->get('level');
        $district_code = $request->session()->get('distCode');
        $is_first = $request->session()->get('is_first');
        $is_urban = $request->session()->get('is_urban');
        $urban_body_code = $request->session()->get('bodyCode');
        $taluka_code = $request->session()->get('bodyCode');
        $role_id=$request->session()->get('role_id');
      $user_id = AuthChecker::getUserId();
        
        $id=$request->benId;
        $Verified="Verified";
        $Rejected=1;
        $comments=$request->comments;
        
        //$scheme_id = 3;
        $user_id = AuthChecker::getUserId();        
        $duty = Configduty::where('user_id','=',$user_id)->where('scheme_id',$scheme_id)->first();
        $role=MapLavel::where('scheme_id',$scheme_id)->where('role_name',Auth::user()->designation_id)->where('stack_level',$duty->mapping_level)->first();

        /*echo "<pre>";
        echo $id;
        //print_r($id);
        echo "</pre>";
        die();*/

        if ($_POST['submit'] == 'Approve') {
            $input = ['next_level_role_id' => $role->parent_id,'comments' => $comments]; 
            $appPrefix = "App";
            $modelName=$appPrefix . "\\" . $ben_table;            
            $is_status_updated=$modelName::where('id', $id)->update($input);            
            if($is_status_updated){
                return redirect()->intended('workflow')->with('message','Approved Succesfully!');            
            }
        
        }else if ($_POST['submit'] == 'Reject') {
            $input = [
            'approval_rejected' => $Rejected,'comments' => $comments,'next_level_role_id' => -1,'is_rejected' => 1,'is_approved' => 2,'is_verified' => 2];
            $appPrefix = "App";
            $modelName=$appPrefix . "\\" . $ben_table; 
            $is_status_updated=$modelName::where('id', $id)->update($input);  
            //->update($input);
            if($is_status_updated){
               return redirect()->intended('workflow')->with('message','Rejected Succesfully!');
            } 
       
        }
    }
  
  
  
  
  
}
