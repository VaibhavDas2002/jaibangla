<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cspolicestation;
use App\CrimeHead;
use App\Dailysitrep;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\Helpers\AuthChecker;



class DailySitRepController extends Controller
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
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)->first();        
        $policestations = Cspolicestation::where('cs_ps_code', '=', $duty->ps_code)->get();        
        $crimeHeads = CrimeHead::all();
        return view('daily_sitrep',['policestations' => $policestations , 'crimeHeads' => $crimeHeads]);
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
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id','=',$user_id)->first();
        //Log::info('Comm id '.$duty->ps_code);
        $date = date('Y-m-d', strtotime("-1 days"));
        $input=[
        'com_id' => $duty->ps_code,
        'ps_id'=>  $request['ps_id'],
        'case_no' => $request['case_no'],
        'case_date'=> $date,
        'section_of_law'=> $request['section_of_law'],
        'gist'=> $request['gist'],
        'arrest_figure'=> $request['no_of_arrest'],
        'arrest'=> $request['sizure']
        ];
        Dailysitrep::create($input);
        return redirect()->intended('dailysitrep')->with('success','Case Saved successfully!'); 
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
