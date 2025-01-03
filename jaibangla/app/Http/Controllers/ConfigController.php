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
use Illuminate\Support\Facades\Log;
use App\Ward;
use App\GP;
use App\Assembly;
use App\SubDistrict;
//sayantika department creation
use App\Department;


class ConfigController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth')->only(["index", "create", "store", "edit", "update", "search", "destroy"]);
        //$this->middleware('Admin');
    }

    public function index(){
        
        $results= Configduty::orderBy('id')->get();
        //dd($results);
        //$final_result=
        //Log::info($results->district_code);
        return view('application.configuration.index')
            ->with('results',$results);       
    }

    public function mapsetting(){
        $schemes=DB::table('m_scheme')->orderby('id')->get();
        $districts = District::orderBy('district_name')->get();
        $policestations = Policestation::orderBy('id')->get();
        $users = User::orderBy('id')->get();
        $departments=Department::all();
        return view('application.configuration.create')
        ->with('districts',$districts)
        ->with('users',$users)->with('schemes',$schemes)
        ->with('departments',$departments);

    }
/*new mapconfig sayantika********/
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
          }else if($request->input('maping_level')=="Department"){
            $Configduty->urban_body_code = $request->input('department');
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
         
            return redirect('/config');
        
      
    }
/********************************/
    // public function mapconfig(Request $request){
    	
    //     /*$policestations = Policestation::all();
    //     $users = User::all();
    //     //$this->validateInput($request);
    //     $Configduty = new Configduty;
    //     if($request->input('con_ps') != null || $request->input('con_user') != null){
    //     $Configduty->ps_code = $request->input('con_ps');
    //     $Configduty->user_id = $request->input('con_user');
    //     $Configduty->is_active = 1;
    //     $Configduty->save();
    //     }*/
    //     $Configduty = new Configduty;
    //     $Configduty->user_id = $request->input('con_user');
        
    //     $Configduty->is_urban = $request->input('urban_code');
    //     $Configduty->mapping_level = $request->input('maping_level');

    //     if($request->input('maping_level')=="State HQ"){
    //         $Configduty->urban_body_code =1;
    //     }else if($request->input('maping_level')=="District HQ"){
    //         $Configduty->urban_body_code = $request->input('dist_code');
    //         $Configduty->district_code = $request->input('dist_code');
    //     }else{
    //         $Configduty->district_code = $request->input('dist_code');
    //         if($request->input('urban_code') == "1"){
    //             $Configduty->urban_body_code = $request->input('body_code');
    //         }else{
    //             $Configduty->taluka_code = $request->input('body_code');
    //         }
    //     }

        
    //     $Configduty->is_active = 1;
    //     $Configduty->save();

    //     return redirect('/config');
    // }
    public function mapconfigEdit($id){
        $policestations = Policestation::all();
        $users = User::all();
        $Configduty = Configduty::find($id);

       
        $sql_ps_name = "SELECT id,name FROM policestation where id = $Configduty->ps_code";
        $sql_user_name = "SELECT id,username FROM users where id = $Configduty->user_id";
        $sql_duty_assignement = "SELECT id,ps_code,user_id FROM duty_assignement where id = $Configduty->id";

        $edit_results = DB::select($sql_ps_name);
        $edit_user_results = DB::select($sql_user_name);
        $duty_assignements = DB::select($sql_duty_assignement);
        return view('application.configuration.edit')
        ->with('policestations',$policestations)
        ->with('users',$users)
        ->with('editResults',$edit_results)
        ->with('editUsers',$edit_user_results)
        ->with('duty_assignements',$duty_assignements);
    }


    public function updateConfig(Request $request,$id){
      
        $configduty = Configduty::findOrFail($id);
         
        $input = [
            'ps_code' => $request['con_ps'],
            'user_id' => $request['con_user']
        ];
        Configduty::where('id', $id)
            ->update($input);
        return redirect('/config');
        
    }

    public function destroyId($id){
        $users = User::all();
        $data = Configduty::where('id', $id)->delete();

       
        return redirect('/config');
    }

    private function validateInput($request) {
        $this->validate($request, [
        'con_ps' => 'required|max:60',
        'con_user' => 'required|max:60'
    ]);
    }


    public function loadLocalBody($body_type,$district_id) { 

      if($body_type == 1){
        $body = UrbanBody::where('district_code', '=', $district_id)->get(['urban_body_code AS id', 'urban_body_name AS name']);
      }else{
        $body = Taluka::where('district_code', '=', $district_id)->get(['block_code AS id', 'block_name AS name']);
     }
     // SubDistrict  loadBlockSubdiv
     //$body = Taluka::where('district_code', '=', $district_id)->get();
     //Log::info($body);  
       return response()->json($body);
    }


    public function loadBlockSubdiv($body_type,$district_id) { 
//dd("Hi");
      if($body_type == 1){
        $body = SubDistrict::where('district_code', '=', $district_id)->get(['sub_district_code AS id', 'sub_district_name AS name']);
      }else{
        $body = Taluka::where('district_code', '=', $district_id)->get(['block_code AS id', 'block_name AS name']);
     }
     //   
     //$body = Taluka::where('district_code', '=', $district_id)->get();
     //Log::info($body);  
       return response()->json($body);
    }

    public function loadAssembly($district_id) { 

      
        $body = Assembly::where('district_code', '=', $district_id)->get(['ac_no AS id', 'ac_name AS name']);
    

     //$body = Taluka::where('district_code', '=', $district_id)->get();
     //Log::info($body);  
       return response()->json($body);
    }


    public function loadGPWard($body_type,$body_code) { 

      if($body_type == 1){
        $body = Ward::where('urban_body_code', '=', $body_code)->get(['urban_body_ward_code AS id', 'urban_body_ward_name AS name']);
      }else{
        $body = GP::where('block_code', '=', $body_code)->get(['gram_panchyat_code AS id', 'gram_panchyat_name AS name']);
     }

     //$body = Taluka::where('district_code', '=', $district_id)->get();
     //Log::info($body);  
       return response()->json($body);
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
        return redirect('/config');
        
    }
}
