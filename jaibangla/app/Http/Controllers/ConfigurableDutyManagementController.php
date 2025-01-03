<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Policestation;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Configduty;
use Illuminate\Support\Facades\Auth;
use App\Designation;
use App\District;
use App\Taluka;
use App\UrbanBody;
use App\Employee;
use App\Ward;
use App\GP;
use App\Helpers\AuthChecker;


class ConfigurableDutyManagementController extends Controller
{
    public function index(){
        
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)->first();
        $is_active = $duty->is_active;

        

        if($is_active==1){

        	$mappingLevel=$duty->mapping_level;
        	if($mappingLevel=="State HQ"){
           		
           		$results=Configduty::where('user_id','!=',$user_id)->get();

		    }elseif($mappingLevel=="District HQ"){
		    	
		    	$district_code=$duty->district_code;

		    	$urban_body_codes=DB::table('m_urban_body')->where('district_code','=',$district_code)->pluck('urban_body_code');
           		$taluka_codes=DB::table('m_taluka')->where('district_code','=',$district_code)->pluck('taluka_code');

           		$results_district=Configduty::where('district_code',$district_code)->where('user_id','!=',$user_id)->get();

           		$results_urban=Configduty::where('district_code',$district_code)->where(function($query) use($urban_body_codes){
                foreach($urban_body_codes as $urban_body_code) {
                     $query->where('urban_body_code',$urban_body_code);
                }
                })->where('user_id','!=',$user_id)->get();

           		
           		$results_taluka=Configduty::where('district_code',$district_code)->where(function($query) use($taluka_codes){
                foreach($taluka_codes as $taluka_code) {
                     $query->where('taluka_code',$taluka_code);
                }
                })->where('user_id','!=',$user_id)->get();

           		$merged = $results_urban->merge($results_taluka);
           		$merged_final =$merged->merge($results_district);
        		$results = $merged_final->all();
		    
		    }else{

		    	$is_urban=$duty->is_urban;
            	if($is_urban==1){
               		 $district_code = $duty->district_code;
                	 $urban_body_code = $duty->urban_body_code;
                     $level="ULB";
               		 
               		 $results=Configduty::where('district_code',$district_code)->where('urban_body_code',$urban_body_code)->where('user_id','!=',$user_id)->get();

 
           		}else{
                	//$level="Block";
                
               		$district_code = $duty->district_code;
                	$taluka_code = $duty->taluka_code;

                	$results=Configduty::where('district_code',$district_code)->where('taluka_code',$taluka_code)->where('user_id','!=',$user_id)->get();
               //dd($results);
            }
		    }



		    return view('application.configurable-duty.index',['results'=>$results]);
		    //->with('results',$results)
		    //->with('schemes',$schemes);  


        }       
    }


     public function mapsetting(){

     	$flag=0;
     	$schemes=DB::table('m_scheme')->orderby('id')->get();
 		//$districts = District::orderBy('district_name')->get();
     	$user_id = AuthChecker::getUserId();
     	//dd($user_id);
        $duty = Configduty::where('user_id','=',$user_id)->first();
        $is_active = $duty->is_active;

        $users = User::orderBy('id')->get();
        
        if($is_active==1){

        	$mappingLevel=$duty->mapping_level;
        	if($mappingLevel=="State HQ"){
           		$flag=1;
           		$mappings = [
				  'State HQ' =>"State",
				  'District HQ' =>"District",
				  'Block'=>"Block/MC"
				   
				];

				$levels = [
   						 0 => 'Rural',
   						 1 => 'Urban',
   
				];
				$districts = District::orderBy('district_name')->get();
       
        		$users = User::orderBy('id')->get();
       			return view('application.configurable-duty.create')
       			->with('districts',$districts)->with('levels',$levels)->with('mappings',$mappings)
        		->with('users',$users)->with('flag',$flag)->with('schemes',$schemes);
 			
		     
		    }elseif($mappingLevel=="District HQ"){
		    	$flag=1;
		    	$district_code=$duty->district_code;
		    	
		    	$mappings = [
		    		'District HQ' =>"District",
				    'Block' =>"Block/MC"
				   
				];

				$levels = [
   						 0 => 'Rural',
   						 1 => 'Urban',
   
				];
 
//$levels->all(); 
//dd($levels);
				//dd($flag);
		    	$districts=District::where('district_code',$district_code)->get();
		    	return view('application.configurable-duty.create',['users' => $users,'districts'=>$districts,'mappings'=>$mappings,'levels'=>$levels,'flag'=>$flag,'schemes'=>$schemes]);
		    	
		   
		    }else{

		    	$is_urban=$duty->is_urban;
            	if($is_urban==1){

            		$mappings = [
				    'Block'=>"MC"				   
					];
               		
               		$levels = [
   						 
   						 1 => 'Urban',
   
					];
 
               		$district_code = $duty->district_code;
               		$districts=District::where('district_code',$district_code)->get();
                	
                	$urban_body_code = $duty->urban_body_code;
                    
                    $urban_bodys=UrbanBody::where('urban_body_code',$urban_body_code)->get(['urban_body_code as code','urban_body_name as name']);
               		 
               		return view('application.configurable-duty.create',['users' => $users,'districts'=>$districts,'mappings'=>$mappings,'levels'=>$levels,'locations'=>$urban_bodys,'flag'=>$flag, 'schemes'=>$schemes]);

 
           		}else{
                	//$level="Block";
                	$mappings = [
				    'Block'=>"Block",
				   
					];

					$levels = [
   						 0 => 'Rural',
   						
   
					];
 
               		$district_code = $duty->district_code;
               		$districts=District::where('district_code',$district_code)->get();
                	
                	$taluka_code = $duty->taluka_code;

                	$talukas=DB::table('m_block')->where('block_code','=',$taluka_code)->get(['block_code as code','block_name as name']);

//dd($taluka_code);
                	return view('application.configurable-duty.create',['users' => $users,'districts'=>$districts,'mappings'=>$mappings,'levels'=>$levels,'locations'=>$talukas,'flag'=>$flag, 'schemes'=>$schemes]);
               
            }
		    }


        }      
        

 }

public function mapconfig(Request $request){
    	
        /*$policestations = Policestation::all();
        $users = User::all();
        //$this->validateInput($request);
        $Configduty = new Configduty;
        if($request->input('con_ps') != null || $request->input('con_user') != null){
        $Configduty->ps_code = $request->input('con_ps');
        $Configduty->user_id = $request->input('con_user');
        $Configduty->is_active = 1;
        $Configduty->save();
        }*/

        
        $scheme_inputs=request()->input('schemelist');

        foreach($scheme_inputs as $input){
         $Configduty = new Configduty;

          $Configduty->user_id = $request->input('con_user');
        
       	  $Configduty->is_urban = $request->input('urban_code');
          $Configduty->mapping_level = $request->input('maping_level');
          
          if($request->input('maping_level')=="State HQ"){
            $Configduty->urban_body_code =1;
       	  }else if($request->input('maping_level')=="District HQ"){
            $Configduty->urban_body_code = $request->input('dist_code');
            $Configduty->district_code = $request->input('dist_code');
          }else{
            $Configduty->district_code = $request->input('dist_code');
            if($request->input('urban_code') == "1"){
                $Configduty->urban_body_code = $request->input('body_code');
            }else{
                $Configduty->taluka_code = $request->input('body_code');
            }
        }

        
        $Configduty->is_active = 1;
        $Configduty->scheme_id=$input;

        $Configduty->save();
 

        }
         
            return redirect('/config-duty-mgmnt');
        
        /************************OLD WORKING CODE WITHOUT SCHEME
        // $Configduty->user_id = $request->input('con_user');
        
        // $Configduty->is_urban = $request->input('urban_code');
        // $Configduty->mapping_level = $request->input('maping_level');



        // if($request->input('maping_level')=="State HQ"){
        //     $Configduty->urban_body_code =1;
        // }else if($request->input('maping_level')=="District HQ"){
        //     $Configduty->urban_body_code = $request->input('dist_code');
        //     $Configduty->district_code = $request->input('dist_code');
        // }else{
        //     $Configduty->district_code = $request->input('dist_code');
        //     if($request->input('urban_code') == "1"){
        //         $Configduty->urban_body_code = $request->input('body_code');
        //     }else{
        //         $Configduty->taluka_code = $request->input('body_code');
        //     }
        // }

        
        // $Configduty->is_active = 1;
        // $Configduty->save();

        // return redirect('/config-duty-mgmnt');
        *********************************************************/
    }


 public function enabledisable(Request $request){
      
        $id=$request->id;
        $configduty = Configduty::findOrFail($id);
        $data=$configduty->is_active;
         
        if($data==1){
        $input = [
            'is_active' =>0            
        ];
       }else{
         $input = [
            'is_active' =>1           
        ];
       }
        
        Configduty::where('id', $id)
            ->update($input);
        return redirect('/config-duty-mgmnt');
        
    }








}
