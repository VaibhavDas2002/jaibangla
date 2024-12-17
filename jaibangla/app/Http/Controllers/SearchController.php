<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Scheme;
use App\PensionSc;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    
    public function index(Request $request)
    {
        $schemes = Scheme::all();
        if($request->scheme){           
            if($request->applicationId){
                $ben = PensionSc::find($request->applicationId);                
                return view('bensearch/index', ['schemes' => $schemes, 'ben' => $ben]);
            }            
        }        
        return view('bensearch/index', ['schemes' => $schemes]);
    }

   
    public function search(Request $request) {
        if(($request['applicationId']!=null)&&(length($request['applicationId'])>= 12)){
            $district_code=sub_str();
        }
    }
    
    private function doSearchingQuery($constraints) {
        $query = City::query();
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
        'name' => 'required|max:60|unique:city'
    ]);
    }
}
