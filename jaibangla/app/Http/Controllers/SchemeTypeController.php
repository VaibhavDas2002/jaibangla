<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Schemetype;
use Auth;
use App\Helpers\AuthChecker;

class SchemeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth')->only(["index", "create", "store", "edit", "update", "search", "destroy"]);
    }


    public function index(Request $request)
    {
        $user_id = AuthChecker::getUserId();
        //print_r($user_id);
        //dd($request->session()->get('role'));
        $schemetype = Schemetype::paginate(20);

       
        return view('scheme-mgmt/schemetype/index', ['schemetype' => $schemetype]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
        return view('scheme-mgmt/schemetype/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validateInput($request);
         Schemetype::create([
            'scheme_type' => $request['scheme_type']
        ]);

        return redirect()->intended('scheme-management/SchemeType')->with('message','Scheme Type Created Succesfully!'); 
       
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
         $schemes = Schemetype::find($id);
        // Redirect to city list if updating city wasn't existed
        if ($schemes == null ) {
            return redirect()->intended('/scheme-management/SchemeType');
        }

        return view('scheme-mgmt/schemetype/edit', ['schemes' => $schemes]);
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
         $scheme = Schemetype::findOrFail($id);


         $this->validate($request, [
        'scheme_type' => 'required|max:60'
        ]);
        $input = [
            'scheme_type' => $request['scheme_type']
            
        ];
        Schemetype::where('id', $id)
            ->update($input);
        
        return redirect()->intended('scheme-management/SchemeType')->with('message','Scheme Type Updated Succesfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Schemetype::where('id', $id)->delete();
         return redirect()->intended('scheme-management/SchemeType');
    }

    public function search(Request $request) {
        $constraints = [
            'scheme_type' => $request['scheme']
            ];

       $schemetype = $this->doSearchingQuery($constraints);
       return view('scheme-mgmt/schemetype/index', ['schemetype' => $schemetype, 'searchingVals' => $constraints]);
    }
    
    private function doSearchingQuery($constraints) {
        $query = Schemetype::query();
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
        'scheme_type' => 'required|max:60'
    ]);
    }
}
