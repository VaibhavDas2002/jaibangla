<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\designationMaster;
use App\nhm_service_category;
use App\programmeHeadMaster;
use App\majorProgammeHeadMaster;
use App\User;
use Illuminate\Support\Facades\DB;

class NHMDesignationListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $users = User::All();
        
        $nhm_designation_lists = designationMaster::All();//paginate(20);
        // $nhm_designation_lists= DB::table('designation_master')->get();
        return view('system-mgmt/nhmdesignationlist/index', ['nhm_designation_lists' => $nhm_designation_lists])->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
          $users = User::All();
         $nhm_service_categorys = nhm_service_category::All();
         $programmeHeadMasters = programmeHeadMaster::All();
         $majorProgammeHeadMasters = majorProgammeHeadMaster::All();

           return view('system-mgmt/nhmdesignationlist/create', ['nhm_service_categorys' => $nhm_service_categorys,'programmeHeadMasters' =>$programmeHeadMasters,'majorProgammeHeadMasters' =>$majorProgammeHeadMasters])->with('users',$users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $users = User::All();
        $this->validateInput($request);
         designationMaster::create([
            'service_category_id' => $request['service_category'],
            'major_programme_head_id' => $request['major_programme_head'],
            
            'programme_head_id' => $request['programme_head'],
            'level' => $request['level'],
            'name' => $request['designation_name'],
        ]);

        return redirect()->intended('nhmDesignationList')->with('users',$users);
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
        $designationMasters = designationMaster::find($id);
        $nhm_service_categorys = nhm_service_category::All();
         $programmeHeadMasters = programmeHeadMaster::All();
         $majorProgammeHeadMasters = majorProgammeHeadMaster::All();
        // Redirect to department list if updating department wasn't existed
        if ($designationMasters == null) {
            return redirect()->intended('nhmDesignationList');
        }

        return view('system-mgmt/nhmdesignationlist/edit', ['designationMasters' => $designationMasters,'nhm_service_categorys'=>$nhm_service_categorys,'programmeHeadMasters'=>$programmeHeadMasters,'majorProgammeHeadMasters'=>$majorProgammeHeadMasters])->with('users',$users);
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
        $users = User::All();
        $designationMaster = designationMaster::findOrFail($id);
        $this->validateInput($request);
        $input = [
            'service_category_id' => $request['service_category'],
            'major_programme_head_id' => $request['major_programme_head'],
            
            'programme_head_id' => $request['programme_head'],
            'level' => $request['level'],
            'name' => $request['designation_name'],
        ];
        designationMaster::where('id', $id)
            ->update($input);
        
        return redirect()->intended('nhmDesignationList')->with('users',$users);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $users = User::All();
        designationMaster::where('id', $id)->delete();
        return redirect()->intended('nhmDesignationList')->with('users',$users);
    }

    public function search(Request $request) {
        $name=$request[trim('designationname')];
        $users = User::All();
        $constraints = [
            'name' => $request['designationname'],
            ];
            //dd( $constraints );
       $nhm_designation_lists = $this->doSearchingQuery($constraints,$name);
       return view('system-mgmt/nhmdesignationlist/index', ['nhm_designation_lists' => $nhm_designation_lists, 'searchingVals' => $constraints])->with('users',$users);
    }

     private function doSearchingQuery($constraints,$name) {
        $query = designationMaster::query();
        $fields = array_keys($constraints);
        $index = 0;

        //$newConstratint = strtolower(str_replace(' ','',$name));
        $newConstratint = strtolower($name);
        //dd($newConstratint);
        $result = designationMaster::where(DB::raw('name') , 'ILIKE' , $newConstratint.'%')->select('designation_master.*')->get();
       

        // foreach ($constraints as $constraint) {
        //     if ($constraint != null) {
        //         $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
        //     }

        //     $index++;
        // }//dd($query->paginate(5));
       // return $query;//->paginate(5);
        return $result;
    }

      private function validateInput($request) {
        $this->validate($request, [
        'service_category' => 'required|max:60',
        'major_programme_head' => 'required|max:60',
        'programme_head' => 'required|max:60',
        'level' => 'required|max:60',
        'designation_name' => 'required|max:60'
    ]);
    }


    public function loadprogrammeHead($major_programme_head_id,$service_category) {
    
        $programmeHeads = programmeHeadMaster::where('major_programme_head_id', '=', $major_programme_head_id)->where('service_category_id', '=', $service_category)->get(['id', 'name']);

        //print_r($programmeHeads);
       // $programmeHeads=programmeHeadMaster::all();
      // dump( $programmeHeads);
        //Log::info('Showing user profile for user: '.$programmeHeads);

       return response()->json($programmeHeads);
        //return view('testview',['programmeHeads' => $programmeHeads]);
    }

 public function loadMajorprogrammeHead($major_programme_head_id) {
    
        $major_programme_heads = majorProgammeHeadMaster::all();

        //print_r($programmeHeads);
       // $programmeHeads=programmeHeadMaster::all();
      // dump( $programmeHeads);
        //Log::info('Showing user profile for user: '.$programmeHeads);

       return response()->json($major_programme_heads);
        //return view('testview',['programmeHeads' => $programmeHeads]);
    }



}



