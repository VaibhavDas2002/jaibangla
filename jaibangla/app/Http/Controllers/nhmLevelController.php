<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\nhm_posting_level;
use App\User;


class nhmLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   

        $users = User::All();
        $nhm_posting_levels = nhm_posting_level::paginate(5);
        return view('system-mgmt/nhmlevel/index', ['nhm_posting_levels' => $nhm_posting_levels])->with('users',$users);
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::All();
        return view('system-mgmt/nhmlevel/create')->with('users',$users);
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
         nhm_posting_level::create([
            'level' => $request['level'],
            'name' => $request['level_name']
        ]);

        return redirect()->intended('system-management/nhmLevel')->with('users',$users);
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
        $users = User::All();
        $nhm_posting_level = nhm_posting_level::find($id);
        // Redirect to department list if updating department wasn't existed
        if ($nhm_posting_level == null) {
            return redirect()->intended('/system-management/nhmLevel');
        }

        return view('system-mgmt/nhmlevel/edit', ['nhm_posting_level' => $nhm_posting_level])->with('users',$users);
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
        $nhm_posting_level = nhm_posting_level::findOrFail($id);
        $this->validateInput($request);
        $input = [
           'level' => $request['level'],
           'name'=> $request['level_name']
        ];
        nhm_posting_level::where('id', $id)
            ->update($input);
        
        return redirect()->intended('system-management/nhmLevel')->with('users',$users);
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
        nhm_posting_level::where('id', $id)->delete();
        return redirect()->intended('system-management/nhmLevel')->with('users',$users);
    }

     public function search(Request $request) {
        $users = User::All();
        $constraints = [
            'name' => $request['name']
            ];

       $nhm_posting_levels = $this->doSearchingQuery($constraints);
       return view('system-mgmt/nhmlevel/index', ['nhm_posting_levels' => $nhm_posting_levels, 'searchingVals' => $constraints])->with('users',$users);
    }

        private function doSearchingQuery($constraints) {
        $query = nhm_posting_level::query();
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
        'level' => 'required|max:60',
        'level_name' => 'required|max:60'
    ]);
    }
}   

