<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\applicationModel;
use App\Policestation;
use App\PicUpload;
use DB;
use APP\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Configduty;
use Auth;

class PoliceVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('SI');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::All();
	    $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id','=',$user_id)->first();
        $applictions = applicationModel::where('is_fee_paid','=','Y')->where('current_status', '=', 'ASSIGNEDTOSI')->where('police_station_code', '=', $duty->ps_code)->paginate(10); 


        /*echo"<pre>";
        print_r($applictions);
        echo"</pre>";
        die();*/
        
        //foreach($applictions as $appliction){

        $stored_file_name_file = "SELECT stored_file_name  from pcc_application_image  order by pcc_appliction_id  ";
            //$application_images = PicUpload::where('pcc_appliction_id','=',$appliction->application_id);
        //}
        $stored_file_name = DB::select($stored_file_name_file);

        return view('application.policeverification.index')->with('applications',$applictions)
        ->with('stored_file_names',$stored_file_name)->with('users',$users);
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
        $users = User::All();
        $application = applicationModel::where('application_id','=',$id)->first();
        $application_images = PicUpload::where('pcc_appliction_id','=',$id)->first();

       
       
        /*$stored_file_name= (json_decode($application_images->stored_file_name)) ;
        $stored_file_name_one = $stored_file_name[0];
        $stored_file_name_two = $stored_file_name[1];*/

       
        if ($application == null ) {
           return redirect()->intended('policeverification');
        }
        return view('application.policeverification.edit')
        ->with('application',$application)->with('application_images',$application_images)->with('users',$users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
       $users = User::All();
       $application = applicationModel::where('application_id','=',$id)->first();
       
          

        if($request['acce_rej'] == 'P'){

             $this->validate($request, [            
            'in_pcc_virification_for'      => 'required',
            ]); 

            if ($request->hasFile('txt_doc_insp_level')) {  
                $file = $request->file('txt_doc_insp_level');
                $File_Name = $file->getClientOriginalName();
                $ext_type= $file->getClientOriginalExtension();
                //$destinationPath = storage_path('app\\keep\\').$application->application_id;
                $destinationPath = storage_path('app/keep/').$application->application_id;
                $file->move($destinationPath,$file->getClientOriginalName());
            }

            $input = [
                'is_rejected' => $request['acce_rej'],
                'first_level_comment' => $request['policevarificationComment'],
                'current_status' => 'ASSIGNEDTOSI',
                'pcc_virification_for' => $request['in_pcc_virification_for']
            ];

            if ($request->hasFile('txt_doc_insp_level')) {
               $input['doc_insp_level'] =  $file->getClientOriginalName();
               $input['doc_insp_level_type'] =  $file->getClientOriginalExtension();
            }
             
            applicationModel::where('application_id',$id)->update($input);
            return view('application.policeverification.message')->with('message',"Panding application for document check next process to SI!")->with('users',$users); 
        }

        if($request['acce_rej'] == 'N'){

            $this->validate($request, [            
            'txt_doc_insp_level'        => 'required|image|mimes:jpeg,png,jpg,gif,pdf|max:10000',
            'policevarificationComment' => 'required',
             'in_pcc_virification_for'  => 'required',
            ]); 

            if ($request->hasFile('txt_doc_insp_level')) {  
                $file = $request->file('txt_doc_insp_level');
                $File_Name = $file->getClientOriginalName();
                $ext_type= $file->getClientOriginalExtension();
                //$destinationPath = storage_path('app\\keep\\').$application->application_id;
                $destinationPath = storage_path('app/keep/').$application->application_id;
                $file->move($destinationPath,$file->getClientOriginalName());
            }

            $input = [
                'is_rejected' => $request['acce_rej'],
                'first_level_comment' => $request['policevarificationComment'],
                'current_status' => 'REJECTED',
                'pcc_virification_for' => $request['in_pcc_virification_for']
            ];

            applicationModel::where('application_id',$id)->update($input);
            return view('application.policeverification.message')->with('message'," Application Rejected ! ")->with('users',$users); 
        }

        if($request['acce_rej'] == 'Y'){
            $this->validate($request, [            
              'certificate_accept_for'  => 'required',
              'in_pcc_virification_for'      => 'required',
              'policevarificationComment'=> 'required',
            ]); 

            if ($request->hasFile('txt_doc_insp_level')) {  
                $file = $request->file('txt_doc_insp_level');
                $File_Name = $file->getClientOriginalName();
                $ext_type= $file->getClientOriginalExtension();
                $destinationPath = storage_path('app/keep/').$application->application_id;
                $file->move($destinationPath,$file->getClientOriginalName());
            }
          
            $input = [
                'is_rejected' => $request['acce_rej'],
                'first_level_comment' => $request['policevarificationComment'],
                'current_status' => 'ASSIGNEDTOACP',
                'pcc_virification_for' => $request['in_pcc_virification_for'],
                'certificate_accept_for' => $request['certificate_accept_for']
            ];


            if ($request->hasFile('txt_doc_insp_level')) {
               $input['doc_insp_level'] =  $file->getClientOriginalName();
               $input['doc_insp_level_type'] =  $file->getClientOriginalExtension();
            }
           applicationModel::where('application_id',$id)->update($input);
           return view('application.policeverification.message')->with('message',"Update application for next process ACP!")->with('users',$users); 
           }
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

    public function search(Request $request) {
        //$constraints = [
        $users = User::All();
            $application_id = $request->input('application_id');
            //];
        $query = "SELECT * FROM pcc_application WHERE application_id = ".$application_id." AND current_status = 'ASSIGNEDTOSI'  ";
        $searchResult = DB::select($query);

        return view('application.policeverification/search', ['searchResults' => $searchResult])->with('users',$users);
    }

    private function doSearchingQuery($constraints) {
        $query = applicationModel::query();
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
            }
            $index++;
        }
        return $query->paginate(5);
    }

}
