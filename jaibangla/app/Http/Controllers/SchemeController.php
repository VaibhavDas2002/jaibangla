<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Schemetype;
use App\Scheme;

class SchemeController extends Controller
{

     public function __construct()
    {
        $this->middleware('auth')->only(["index", "create", "store", "edit", "update", "search", "destroy"]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $schemes = DB::table('m_scheme')
        ->leftJoin('m_scheme_type', 'm_scheme.scheme_type', '=', 'm_scheme_type.id')
        ->select('m_scheme.id','m_scheme.scheme_name', 'm_scheme_type.scheme_type as scheme_type')
        ->paginate(5);

        return view('scheme-mgmt/scheme/index', ['schemes' => $schemes]);
        //return view('scheme-mgmt/scheme/index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $schemetypes = Schemetype::all();
        return view('scheme-mgmt/scheme/create',['schemetypes' => $schemetypes]);
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
         Scheme::create([
            'scheme_name' =>$request['scheme_name'],
            'scheme_type' => $request['scheme_type'],
            'description' => $request['description'],
            'short_code' => $request['shortcode'],
        ]);

        return redirect()->intended('scheme-management/scheme')->with('message','Scheme Created Succesfully!'); 
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
         $schemes = Scheme::find($id);
        // Redirect to city list if updating city wasn't existed
        if ($schemes == null ) {
            return redirect()->intended('scheme-management/scheme');
        }
        $schemetype =Schemetype::all();
      /*  echo "<pre>";
        print_r($schemes);
        echo "</pre>";*/
       

        return view('scheme-mgmt/scheme/edit', ['schemes' => $schemes,'schemetype'=> $schemetype]);
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
        $scheme = Scheme::findOrFail($id);


        $this->validate($request, [
        'scheme_type' => 'required|max:60',
        'scheme_name' => 'required|max:60'
        ]);
        $input = [
            'scheme_name' => $request['scheme_name'],
            'scheme_type' => $request['scheme_type']
            
        ];
        /*echo "<pre>";
        print_r($input);
        echo "</pre>";*/
        Scheme::where('id', $id)
            ->update($input);
        
        return redirect()->intended('scheme-management/scheme')->with('message','Scheme Updated Succesfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Scheme::where('id', $id)->delete();
         return redirect()->intended('scheme-management/scheme');
    }

     public function search(Request $request) {
        $constraints = [
            'scheme' => $request['scheme']
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
        'scheme_name' => 'required|max:60',    
        'scheme_type' => 'required|max:60'
    ]);
    }
}
