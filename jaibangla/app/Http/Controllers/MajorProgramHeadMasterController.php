<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\majorProgammeHeadMaster;
use App\User;

class MajorProgramHeadMasterController extends Controller
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
        //$major_master = DB::table('major_programme_head_master')->paginate(5);
        $major_master = majorProgammeHeadMaster::paginate(5);
        return view('system-mgmt/major_program_head_master/index', ['major_masters' => $major_master])->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::All();
        return view('system-mgmt/major_program_head_master/create')->with('users',$users);
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
         majorProgammeHeadMaster::create([
            'name' => $request['name']
        ]);

        return redirect()->intended('system-management/major_program_head_master')->with('users',$users);
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
    private function validateInput($request) {
        $this->validate($request, [
        'name' => 'required|max:60|unique:major_programme_head_master'
    ]);
    }
}
