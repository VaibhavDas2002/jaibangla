<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Country;
use App\User;

class CountryController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       

        $country_class="active";
        if(request()->ajax())
        {
            

            $limit = $request->input('length');
            $offset = $request->input('start');
            //$serachvalue = $request->search['value'];
            $country = array();    
            $totalRecords = 0;
            $filterRecords = 0;
            $action="";
       
            if(empty($serachvalue)){   
                $country = Country::orderBy("country_code")->offset($offset)->limit($limit)->get(['id','name','country_code']); 
                $totalRecords = Country::count();
                $filterRecords = count($country);
            }
        
            return datatables()
            ->of($country)
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
          
            ->addColumn('action', function ($country) {
               
                $action ='<button class="btn btn-warning ben_view_button" onClick="editCountry('.$country->id.')"><i class="fa fa-edit"></i></button>&nbsp;';
                $action .= '<button class="btn btn-danger ben_delete_button" onClick="deleteCountry('.$country->id.')"><i class="fa fa-trash"></i></button>';
                return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
            
        }
      
       
        
        return view('system-mgmt/country/index', compact('country_class'));
        //$users = User::All();
        //$countries = Country::paginate(5);
       // return view('system-mgmt/country/index', ['countries' => $countries])->with('users',$users);
    }
    public function countrySaveUpdate(Request $request){
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occered in ajax call.');
            return response()->json($response, $statusCode);
        }
        $edit_code=$request->edit_code;
        if($edit_code==""){

            $validatonCountry='required|max:60|unique:country,name';
            $validatonCountryCode='required|max:3|unique:country,country_code';
        }
        else{
            $validatonCountry='required|max:60|unique:country,name,' . $edit_code;
            $validatonCountryCode='required|max:3|unique:country,country_code,' . $edit_code;
        }
         $this->validate($request, [
             
            
            'country_name' =>$validatonCountry,
            'country_code' =>$validatonCountryCode 
            
                ], [
            'country_name.required' => 'Please enter country name.',
            'country_name.max' => 'Country name should not cross 60 characters.',
            'country_name.unique' => 'Duplicate country name  not allow.' ,
            'country_code.required' => 'Please enter country code.' ,
            'country_code.max' => 'Country code should not cross 3 characters.',
            'country_code.unique' => 'Duplicate country code  not allow.' ,
           
        ]);
        try {
          
            if($edit_code==""){
                $msg='Country Created Succesfully';
                Country::create([
                    'name' => $request['country_name'],
                    'country_code' => $request['country_code'],
                ]);
            }
         
            else{
                $msg= 'Country Updated Succesfully!';
            $input = [
                'name' => $request['country_name'],
                'country_code' => $request['country_code'],
                
            ];
         
            Country::where('id', $edit_code)
                ->update($input);
            }
            $response = array('status' => 1, 'msg' => $msg);
        } catch (\Exception $e) {
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function editCountry(Request $request){
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occered in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {
            $editId=$request->editId;
            $country = Country::select('name','country_code',  'id')->where('id', $editId)->first();

            $response = array('status' => 1, 'country' => $country);
        } catch (\Exception $e) {
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function deleteCountry(Request $request)
    {
     
        $country_id = $request['item_id'];

     
        Country::where('id', $country_id)->delete();

        return "success";
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::All();
        return view('system-mgmt/country/create')->with('users',$users);
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
         Country::create([
            'name' => $request['name'],
            'country_code' => $request['country_code']
        ]);

        return redirect()->intended('system-management/country')->with('users',$users);
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
        $country = Country::find($id);
        // Redirect to country list if updating country wasn't existed
        if ($country == null) {
            return redirect()->intended('/system-management/country');
        }

        return view('system-mgmt/country/edit', ['country' => $country])->with('users',$users);
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
        $country = Country::findOrFail($id);
        $input = [
            'name' => $request['name'],
            'country_code' => $request['country_code']
        ];
        $this->validate($request, [
        'name' => 'required|max:60'
        ]);
        Country::where('id', $id)
            ->update($input);
        
        return redirect()->intended('system-management/country')->with('users',$users);
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
        Country::where('id', $id)->delete();
         return redirect()->intended('system-management/country')->with('users',$users);
    }

    /**
     * Search country from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $users = User::All();
        $constraints = [
            'name' => $request['name'],
            'country_code' => $request['country_code']
            ];

       $countries = $this->doSearchingQuery($constraints);
       return view('system-mgmt/country/index', ['countries' => $countries, 'searchingVals' => $constraints])->with('users',$users);
    }

    private function doSearchingQuery($constraints) {
        $query = country::query();
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
        'name' => 'required|max:60|unique:country',
        'country_code' => 'required|max:3|unique:country'
    ]);
    }
}
