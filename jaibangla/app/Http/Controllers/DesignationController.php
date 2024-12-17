<?php

namespace App\Http\Controllers;

use App\Designation;
use Illuminate\Http\Request;
use App\User;

class DesignationController extends Controller
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
        $designations = Designation::paginate(5);

        return view('system-mgmt/designation/index', ['designations' => $designations])->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::All();
        return view('system-mgmt/designation/create')->with('users',$users);
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
         Designation::create([
            'name' => $request['name']
        ]);

        return redirect()->intended('system-management/designation')->with('users',$users);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function show(Designation $designation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::All();
        $designation = Designation::find($id);
        // Redirect to department list if updating department wasn't existed
        if ($designation == null) {
            return redirect()->intended('/system-management/designation');
        }

        return view('system-mgmt/designation/edit', ['designation' => $designation])->with('users',$users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Designation $designation)
    {
        $users = User::All();
        $designation = Designation::findOrFail($id);
        $this->validateInput($request);
        $input = [
            'name' => $request['name']
        ];
        Designation::where('id', $id)
            ->update($input);
        
        return redirect()->intended('system-management/designation')->with('users',$users);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Designation $designation)
    {
         $users = User::All();
        Designation::where('id', $id)->delete();
        return redirect()->intended('system-management/designation')->with('users',$users);
    }

    public function search(Request $request) {
        $users = User::All();
        $constraints = [
            'name' => $request['designationname']
            ];

       $designations = $this->doSearchingQuery($constraints);
       return view('system-mgmt/designation/index', ['designations' => $designations, 'searchingVals' => $constraints])->with('users',$users);
    }

    private function doSearchingQuery($constraints) {
        $query = designation::query();
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
        'name' => 'required|max:60|unique:designation'
    ]);
    }
}
