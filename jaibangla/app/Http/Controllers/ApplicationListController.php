<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use App\applicationModel;
use App\User;
use App\Policestation;
use App\Designation;
use DB;
use App\Configduty;
use Illuminate\Support\Facades\Log;

class ApplicationListController extends Controller
{
   public function __construct()
   {
        $this->middleware('auth');
   }

   public function index(){


      $users = User::All();
      $applictions = applicationModel::where('is_fee_paid','=','Y')->where('current_status', '=', 'ASSIGNEDTOSI')->paginate(10);
   	 return view('application-list.index')
   	 ->with('applications',$applictions)->with('users',$users);
   }

   public function policestationList(){
      
    $police_application = Policestation::all();         
    return $police_application;
      


   }

   public function saveAssignments(Request $request){
    $users = User::All(); 
    $applications = $request->input('applications');
    $agent = $request->input('agent');
    $val = $request->input('val');
   	$split = explode("^", $applications);
    //Log::info($split);
    //print_r($split);
      for($x = 0; $x < count($split) -1; $x++) {
         $application_id =  $split[$x];
         if(!empty($application_id)){
            $objAppliaction = applicationModel::where('application_id', $application_id)->first();
            $objAppliaction->current_status = "ASSIGNEDTOSI";
            $objAppliaction->police_station_code = $agent;
            $objAppliaction->police_station_name = $val;
            $objAppliaction->save();
         }
      }      
       $applictions = applicationModel::where('is_fee_paid','=','Y')->where('current_status', '=', 'ASSIGNEDTOSI')->paginate(10);
     return view('application-list.index')
     ->with('applications',$applictions)->with('users',$users);
   }
}
