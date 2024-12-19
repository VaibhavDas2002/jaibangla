<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
Use App\SitrepConfiguration;
use App\Helpers\AuthChecker;


class SitrepConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');        
    }

    public function index()
    {
       return view('sitrep-configuration/ditrictsitrep/create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        return view('sitrep-configuration/ditrictsitrep/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)->first();        
        //$this->validateInput($request);  
        SitrepConfiguration::create([
            'com_dist_id' => $duty->ps_code,
            'report_to' => $request['report_to'],
            'report_from' => $request['report_from'],
            'org_no' => $request['org_no'],
            'ref' => $request['ref'],
            'ref_org_no' => $request['ref_org_no']
        ]);
        return view('sitrep-configuration/ditrictsitrep/create')->with('success','Case Saved successfully!'); 
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
}
