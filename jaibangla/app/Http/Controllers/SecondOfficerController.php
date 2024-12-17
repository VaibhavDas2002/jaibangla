<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\applicationModel;
use App\Policestation;
use App\PicUpload;
use App\User;
use DB;

class SecondOfficerController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth')->only(["index", "create", "store", "edit", "update", "search", "destroy"]);
        $this->middleware('ACP');
    }
	public function index(){
    $users = User::All();
		$applictions = applicationModel::where('current_status', '=', 'ASSIGNEDTOACP')->orwhere('current_status', '=', 'REJECTED')->get();

    /*echo "<pre>";
    print_r($applictions);
    echo "</pre>";
    die();*/

   	 //$applictions = applicationModel::all();
   	 return view('application.application-secondOfficer-checking-status')
   	 ->with('applications',$applictions)->with('users',$users);
		
	}
	  public function edit($id){
      $users = User::All();
		  $application = applicationModel::where('application_id','=',$id)->where('current_status', '=', 'ASSIGNEDTOACP')->orwhere('current_status', '=', 'REJECTED')->first();
        $application_images = PicUpload::where('pcc_appliction_id','=',$id)->first();
        if ($application == null ) {
           return redirect()->intended('policeverification');
    }

        
      return view('application.policeverification.second-officer-edit')
        ->with('application',$application)->with('application_images',$application_images)->with('users',$users);
	}
  public function update(Request $request, $id){
       $users = User::All();
       $this->validateInput($request);
       $application = applicationModel::where('application_id','=',$id)->first();



       if($request['acce_rej'] != NULL && $request['policevarificationComment'] != NULL){

        if($request['acce_rej'] == 'N'){

          $input = [
                'is_rejected' => $request['acce_rej'],
                'second_level_comment' => $request['policevarificationComment'],
                'current_status' => 'REJECTEDBYACP'
            ];
        applicationModel::where('application_id',$id)->update($input);

        }
          if($request['acce_rej'] == 'Y'){
          $input = [
                  'is_rejected' => $request['acce_rej'],
                  'second_level_comment' => $request['policevarificationComment'],
                  'current_status' => 'APPROVEDBYACP'
              ];
          applicationModel::where('application_id',$id)->update($input);

         }
     }

        return view('application.policeverification.secondOfficer-message')->with('message',"Update application for next process DCP!")->with('users',$users); 
  }

  private function validateInput($request) {
        $this->validate($request, [
        'acce_rej' => 'required',
        'policevarificationComment' => 'required'

    ]);
  }

  public function search(Request $request) {
        $users = User::All();
        $application_id = $request->input('application_id');
        $applictions = applicationModel::where('current_status', '=', 'ASSIGNEDTOACP')->get();
       return view('application.policeverification/search', ['searchResults' => $applictions])->with('users',$users);
  }
    
}
