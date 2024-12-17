<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\State;
use App\Country;
use App\User;

class StateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only(["index", "create", "store", "edit", "update", "search", "destroy"]);
        $this->middleware('Admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)

    {
        $state_class="active";
        $countries = Country::all();
        if(request()->ajax())
        {
           

            $limit = $request->input('length');
            $offset = $request->input('start');
            //$serachvalue = $request->search['value'];
          //  $state = array();    
            $totalRecords = 0;
            $filterRecords = 0;
            $action="";
       
            if(empty($serachvalue)){   
                $state = State::orderBy("name")
                ->leftJoin('country', 'state.country_id', '=', 'country.id')
       ->select('state.id','state.state_code', 'state.name', 'country.name as country_name', 'country.id as country_id')
       ->offset($offset)->limit($limit)->get(); 
                $totalRecords = State::count();
                $filterRecords = count($state);
            }
        
            return datatables()
            ->of($state)
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
           
            ->addColumn('action', function ($state) {
               
                $action ='<button class="btn btn-warning ben_view_button" onClick="editState('.$state->id.')"><i class="fa fa-edit"></i></button>&nbsp;';
                $action .= '<button class="btn btn-danger ben_delete_button" onClick="deleteState('.$state->id.')"><i class="fa fa-trash"></i></button>';
                return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
            
        }
      
       
       
      
        return view('system-mgmt/state/index',  compact('state_class','countries'));
        // $users = User::All();
        //  $states = DB::table('state')
        // ->leftJoin('country', 'state.country_id', '=', 'country.id')
        // ->select('state.id', 'state.name', 'country.name as country_name', 'country.id as country_id')
        // ->paginate(5);
        // return view('system-mgmt/state/index', ['states' => $states])->with('users',$users);
    }
    public function stateSaveUpdate(Request $request){
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occered in ajax call.');
            return response()->json($response, $statusCode);
        }
        $edit_code=$request->edit_code;
        if($edit_code==""){

            $validatonState='required|max:60|unique:state,name';
           
        }
        else{
            $validatonState='required|max:60|unique:state,name,' . $edit_code;
           
        }
         $this->validate($request, [
             
            
            'country_id' => 'required|integer',
            'state_name' => $validatonState,
            'state_code_val'=>'required|digits_between:1,3'
            
                ], [
            'country_id.required' => 'Please select country.',
            'country_id.integer' => 'Country name should be integer value.',
            'state_name.required' => 'Please enter state name.',
            'state_name.max' => 'State name should not cross 60 characters.' ,
            'state_name.unique' => 'Duplicate state name  not allow.' ,
            'state_code_val.required' => 'Please enter state code.',
            'state_code_val.integer' => 'State code should be numeric value.',
            'state_code_val.digits_between' => 'State code  should not cross 3 characters.',
            
        
           
        ]);
        try {
          
            if($edit_code==""){
                $msg='State Created Succesfully';
                State::create([
                    'name' => $request['state_name'],
                    'country_id' => $request['country_id'],
                    'state_code' => $request['state_code_val']
                ]);
            }
         
            else{
                $msg= 'State Updated Succesfully!';
                $input = [
                    'name' => $request['state_name'],
                    'country_id' => $request['country_id'],
                    'state_code' => $request['state_code_val']
                ];
         
            State::where('id', $edit_code)
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

    public function editState(Request $request){
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occered in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {
            $editId=$request->editId;
            $state = State::select('name','state_code','country_id',  'id')->where('id', $editId)->first();

            $response = array('status' => 1, 'state' => $state);
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

    public function deleteState(Request $request)
    {
     
        $state_id = $request['item_id'];

     
        State::where('id', $state_id)->delete();

        return "success";
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        $countries = Country::all();
        $users = User::All();
        return view('system-mgmt/state/create', ['countries' => $countries])->with('users',$users);
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
        Country::findOrFail($request['country_id']);
        $this->validateInput($request);
         State::create([
            'name' => $request['name'],
            'country_id' => $request['country_id'],
            'state_code' => $request['state_code']
        ]);

        return redirect()->intended('system-management/state')->with('users',$users);
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
    {   $users = User::All();
        $state = State::find($id);
        // Redirect to state list if updating state wasn't existed
        if ($state == null ) {
            return redirect()->intended('/system-management/state');
        }

        $countries = Country::all();
        return view('system-mgmt/state/edit', ['state' => $state, 'countries' => $countries])->with('users',$users);
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
        $state = State::findOrFail($id);
         $this->validate($request, [
        'name' => 'required|max:60'
        ]);
        $input = [
            'name' => $request['name'],
            'country_id' => $request['country_id']
        ];
        State::where('id', $id)
            ->update($input);
        
        return redirect()->intended('system-management/state')->with('users',$users);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   $users = User::All();
        State::where('id', $id)->delete();
         return redirect()->intended('system-management/state')->with('users',$users);
    }

    public function loadStates($countryId) {
        $states = State::where('country_id', '=', $countryId)->get(['id', 'name']);

        return response()->json($states);
    }
    
    /**
     * Search state from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $users = User::All();
        $constraints = [
            'name' => $request['name']
            ];

       $states = $this->doSearchingQuery($constraints);
       return view('system-mgmt/state/index', ['states' => $states, 'searchingVals' => $constraints])->with('users',$users);
    }

    private function doSearchingQuery($constraints) {
        $query = State::query();
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
        'name' => 'required|max:60|unique:state',
        'state_code'=>'required|numeric|max:3'
    ]);
    }
}
