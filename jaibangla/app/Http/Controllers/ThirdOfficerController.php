<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\applicationModel;
use App\Policestation;
use App\PicUpload;
use App\User;
use Illuminate\Support\Facades\DB;

class ThirdOfficerController extends Controller
{
	    public function __construct()
        {
            $this->middleware('auth');
            $this->middleware('DCP');

        }
    	public function index(){
          $users = User::All();
      		$applictions = applicationModel::where('current_status', '=', 'APPROVEDBYACP')->get();

         	 //$applictions = applicationModel::all();
         	 return view('application.application-thirdOfficer-checking-status')
         	 ->with('applications',$applictions)->with('users',$users);
    		
    	}
    	public function edit($id){
          $users = User::All();
          $application = applicationModel::where('application_id','=',$id)->where('current_status', '=', 'APPROVEDBYACP')->first();
              $application_images = PicUpload::where('pcc_appliction_id','=',$id)->first();
             
              if ($application == null ) {
                 return redirect()->intended('policeverification');
              }
              return view('application.policeverification.third-officer-edit')
              ->with('application',$application)->with('application_images',$application_images)->with('users',$users);
      }

      public function update(Request $request, $id){

           $users = User::All();
           $application = applicationModel::where('application_id','=',$id)->first();
           $validity=date('Y-m-d', strtotime("+180 days"));
           $input = [
                    'is_rejected' => $request['acce_rej'],
                    'third_level_comment' => $request['policevarificationComment'],
                    'current_status' => 'APPROVEDBYDCP',
                    'valid_upto' => $validity,//+ INTERVAL '''180 DAY''')
                ];
           applicationModel::where('application_id',$id)->update($input);

           return view('application.policeverification.thirdOfficer-message')->with('message',"Update application my DCP and Process for Certificate!")->with('users',$users); 
      }
      
      public function search(Request $request) {
            $users = User::All();
            $application_id = $request->input('application_id');
            $applictions = applicationModel::where('current_status', '=', 'ASSIGNEDTODCP')->get();
            return view('application.policeverification/search', ['searchResults' => $applictions])->with('users',$users);
       }
    
}
