<?php

namespace App\Http\Controllers;

use App\Policestation;
use Illuminate\Http\Request;
use App\User;
use App\Cspolicestation;

class CSpolicestationController extends Controller
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
        $policestations = Cspolicestation::paginate(5);

       /* echo "<pre>";
        print_r($policestations);
        echo "</pre>";*/

        return view('system-mgmt/policestation/index', ['policestations' => $policestations])->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::All();
        $comm_policestations = Policestation::all();
        return view('system-mgmt/policestation/create')->with('users',$users)->with('comm_policestations',$comm_policestations);
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
        //$this->validateInput($request);
         Cspolicestation::create([
            'cs_ps_code' => $request['comm_policestation_id'],
            'name' => $request['name']
        ]);

        return redirect()->intended('system-management/policestation')->with('users',$users);
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
        $policestation = Cspolicestation::find($id);
        // Redirect to department list if updating department wasn't existed
        if ($policestation == null ) {
            return redirect()->intended('/system-management/policestation');
        }

        return view('system-mgmt/policestation/edit', ['policestation' => $policestation])->with('users',$users);
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
        $policestation = Cspolicestation::findOrFail($id);

        echo "<pre>";
        print_r($policestation);
        echo "</pre>";

        //$this->validateInput($request);
        $input = [
            'name' => $request['name']
        ];
        Cspolicestation::where('id', $id)
            ->update($input);
        
        return redirect()->intended('system-management/policestation')->with('users',$users);
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
        Cspolicestation::where('id', $id)->delete();
         return redirect()->intended('system-management/policestation')->with('users',$users);
    }

    public function search(Request $request) {
        $users = User::All();
        $constraints = [
            'name' => $request['name']
            ];

       $policestations = $this->doSearchingQuery($constraints);
       return view('system-mgmt/policestation/index', ['policestations' => $policestations, 'searchingVals' => $constraints])->with('users',$users);
    }

    private function doSearchingQuery($constraints) {
        $query = Cspolicestation::query();
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
        'cs_ps_code'=>'required',
        'name' => 'required|max:60|unique:policestation'
    ]);
    }
}
