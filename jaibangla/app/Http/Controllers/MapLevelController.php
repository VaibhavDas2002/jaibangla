<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Employee;
use App\Designation;
use App\Schemetype;
use App\Scheme;
use App\MapLavel;
use Illuminate\Support\Facades\DB;

class MapLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mapLavel = MapLavel::paginate(10);
        /*echo "<pre>";
        print_r($mapLavel);
        echo "</pre>";
        die();*/
        return view('mapLevel-mgmt/index', ['mapLavel' => $mapLavel]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $schemes = Scheme::all();
        $designations = Designation::where('id','>',0)->get();
        return view('mapLevel-mgmt/create', ['schemes' => $schemes,'designations' => $designations]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $is_final=false;
         if($request['final_level_approval']==0){
            $is_final=true;
         }
         $designation = Designation::find($request['designation_id']);
         MapLavel::create([
            'scheme_id' =>$request['scheme_id'],
            'role_id' => $request['designation_id'],
            'role_name' => $designation->name ,
            'parent_id' => $request['final_level_approval'],
            'stack_level' => $request['level'],
            'is_final'  => $is_final
        ]);

        return redirect()->intended('maplevel-management')->with('message','Map Leval  Created Succesfully!'); 
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
        $schemes = Scheme::all();
        $designations = Designation::all();
        $mapLavel = MapLavel::find($id);
        return view('mapLevel-mgmt/edit', ['schemes' => $schemes])->with('designations',$designations)->with('mapLavel', $mapLavel);
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
        $mapLavel = MapLavel::findOrFail($id);
        $input = [
            'scheme_id' =>$request['scheme_id'],
            'designation_id' => $request['designation_id'],
            'final_level_approval' => $request['final_level_approval'],
            'level_gp_district_mc_subd' => $request['level']
        ];

        MapLavel::where('id', $id)
            ->update($input);
        
         return redirect()->intended('maplevel-management');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        MapLavel::where('id', $id)->delete();
        return redirect()->intended('maplevel-management');
    }
    
    public function loadParentLevel($schemeId){

         /*$parentroles = MapLavel::where('scheme_id', '=', $schemeId)->where('role_id', '>', 0)->get(['id', "CONCAT('role_name','stack_level') as name"]);
        return response()->json($parentroles);*/
        
        //$parentroles = MapLavel::where('scheme_id', '=', $schemeId)->where('role_id', '>', 0)->get(['id', 'role_name as name']);
        $parentroles = MapLavel::where('scheme_id', '=', $schemeId)->where('role_id', '>', 0)->get(['id', DB::raw('CONCAT(stack_level,role_name) as name')]);

        return response()->json($parentroles);
    }

   
}
