<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CrimeHead;
use App\User;

class CrimeHeadController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('Admin');
    }

    public function index()
    {
        $users = User::All();
        $crimeheads = CrimeHead::paginate(5);

        return view('system-mgmt/crimehead/index', ['crimeheads' => $crimeheads])->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::All();
        return view('system-mgmt/crimehead/create')->with('users',$users);
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
        CrimeHead::create([
            'crime_head' => $request['crime_head']
        ]);

        return redirect()->intended('system-management/crimehead')->with('users',$users);
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
        $crimehead = CrimeHead::find($id);
        // Redirect to country list if updating country wasn't existed
        if ($crimehead == null) {
            return redirect()->intended('/system-management/crimehead');
        }
        return view('system-mgmt/crimehead/edit', ['crimehead' => $crimehead])->with('users',$users);
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
        $crimehead = CrimeHead::findOrFail($id);
        $input = [
            'crime_head' => $request['head_name']
        ];
        $this->validate($request, [
        'crime_head' => 'required|max:30'
        ]);
        CrimeHead::where('id', $id)
            ->update($input);
        
        return redirect()->intended('system-management/crimehead')->with('users',$users);
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
        $users = User::All();
        $constraints = [
            'crime_head' => $request['crime_head']
            ];

       $crimeheads = $this->doSearchingQuery($constraints);
       return view('system-mgmt/crimehead/index', ['crimeheads' => $countries, 'searchingVals' => $constraints])->with('users',$users);
    }

    private function doSearchingQuery($constraints) {
        $query = CrimeHead::query();
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
        'crime_head' => 'required|max:30|unique:crime_head'        
        ]);
    }
}
