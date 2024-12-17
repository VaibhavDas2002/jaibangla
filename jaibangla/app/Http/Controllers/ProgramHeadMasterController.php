<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\majorProgammeHeadMaster;
use App\programmeHeadMaster;
use App\nhm_service_category;
use App\User;

class ProgramHeadMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('Admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::All();
        // $head_master = DB::table('programme_head_master')
        // ->leftJoin('major_programme_head_master', 'programme_head_master.major_programme_head_id', '=', 'major_programme_head_master.id')
        // ->leftJoin('nhm_service_category', 'programme_head_master.service_category_id', '=', 'nhm_service_category.id')
        // ->select('programme_head_master.id', 'programme_head_master.name', 'major_programme_head_master.name as major_head_name', 'nhm_service_category.name as service_name')
        // ->paginate(5);
        $head_master=programmeHeadMaster::paginate(10);
        return view('system-mgmt/program_head_master/index', ['head_masters' => $head_master])->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $major_head_name = majorProgammeHeadMaster::All();
        $service_category = nhm_service_category::All();
        $users = User::All();
        return view('system-mgmt/program_head_master/create', ['major_head_names' => $major_head_name , 'service_categorys' => $service_category])->with('users',$users);
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
        majorProgammeHeadMaster::findOrFail($request['major_programme_head_id']);
        $this->validateInput($request);
         programmeHeadMaster::create([
            'name' => $request['name'],
            'major_programme_head_id' => $request['major_programme_head_id'],
            'service_category_id' => $request['service_category_id']
        ]);

        return redirect()->intended('system-management/program_head_master')->with('users',$users);
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
    
    /**
     * Search department from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $users = User::All();
        $constraints = [
            'name' => $request['name']
            ];

       $head_master = $this->doSearchingQuery($constraints);
       return view('system-mgmt/program_head_master/index', ['head_masters' => $head_master, 'searchingVals' => $constraints])->with('users',$users);
    }

    private function doSearchingQuery($constraints) {
        $query = programmeHeadMaster::query();
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
    private function validateInput($request) {
        $this->validate($request, [
        'name' => 'required|max:60|unique:programme_head_master',
        'major_programme_head_id'=>'required',
        'service_category_id'=>'required'
    ]);
    }
}
