<?php

namespace App\Http\Controllers;

use App\Policestation;
use Illuminate\Http\Request;
use App\User;


class PolicestationController extends Controller
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
        //
        $users = User::All();
        $policestations = Policestation::paginate(5);

        return view('system-mgmt/commissionerate_policestation/index', ['policestations' => $policestations])->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $users = User::All();
        return view('system-mgmt/commissionerate_policestation/create')->with('users',$users);
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
        Policestation::create([
            'name' => $request['name']
        ]);

        return redirect()->intended('system-management/commissionerate')->with('users',$users);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Policestation  $policestation
     * @return \Illuminate\Http\Response
     */
    public function show(Policestation $policestation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Policestation  $policestation
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::All();
        $policestation = Policestation::find($id);
        // Redirect to department list if updating department wasn't existed
        if ($policestation == null ) {
            return redirect()->intended('/system-management/commissionerate');
        }

        return view('system-mgmt/policestation/edit', ['policestation' => $policestation])->with('users',$users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Policestation  $policestation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $users = User::All();
        $policestation = Policestation::findOrFail($id);
        $this->validateInput($request);
        $input = [
            'name' => $request['name']
        ];
        Policestation::where('id', $id)
            ->update($input);
        
        return redirect()->intended('system-management/commissionerate')->with('users',$users);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Policestation  $policestation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $users = User::All();
        Policestation::where('id', $id)->delete();
         return redirect()->intended('system-management/commissionerate')->with('users',$users);
    }


    public function search(Request $request) {
        $users = User::All();
        $constraints = [
            'name' => $request['name']
            ];

       $policestations = $this->doSearchingQuery($constraints);
       return view('system-mgmt/commissionerate_policestation/index', ['policestations' => $policestations, 'searchingVals' => $constraints])->with('users',$users);
    }

    private function doSearchingQuery($constraints) {
        $query = policestation::query();
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
        'name' => 'required|max:60|unique:policestation'
    ]);
    }
}
