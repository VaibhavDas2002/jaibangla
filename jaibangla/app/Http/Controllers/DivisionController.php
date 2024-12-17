<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Division;
use App\User;

class DivisionController extends Controller
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
        $users = User::All();
        $division_class="active";

        if(request()->ajax())
        {
            

            $limit = $request->input('length');
            $offset = $request->input('start');
            //$serachvalue = $request->search['value'];
            $divisions = array();    
            $totalRecords = 0;
            $filterRecords = 0;
            $action="";
            $count=0;
            if(empty($serachvalue)){   
                $divisions = Division::orderBy("name")->offset($offset)->limit($limit)->get(['id','name']); 
                $totalRecords = Division::count();
                $filterRecords = count($divisions);
            }
        
            return datatables()
            ->of($divisions)
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
          
            ->addColumn('action', function ($divisions) {
               
                $action ='<button class="btn btn-warning ben_view_button" onClick="UpdateDivision('.$divisions->id.')"><i class="fa fa-edit"></i></button>&nbsp;';
                $action .= '<button class="btn btn-danger ben_delete_button" onClick="deleteDivision('.$divisions->id.')"><i class="fa fa-trash"></i></button>';
                return $action;
            })
            ->rawColumns(['action','serial_no'])
            ->make(true);
            
        }
      
       
        return view('system-mgmt/division/index')->with("division_class",$division_class);
    }

    public function divisionSaveUpdate(Request $request){
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occered in Json call.');
            return response()->json($response, $statusCode);
        }
         $this->validate($request, [
            'name' => 'required',
           
            
                ], [
            'name.required' => 'Please enter division name',
           
        ]);
        try {
            $edit_code=$request->edit_code;
            if($edit_code==""){
                $msg='Division Created Succesfully';
                Division::create([
                    'name' => $request['name'],
                  
                ]);
            }
         
            else{
                $msg= 'Division Updated Succesfully!';
            $input = [
                'name' => $request['name'],
                
            ];
         
            Division::where('id', $edit_code)
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
    public function editDivision(Request $request){
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occered in Json call.');
            return response()->json($response, $statusCode);
        }
        try {
            $editId=$request->editId;
            $division = Division::select('name',  'id')->where('id', $editId)->first();

            $response = array('status' => 1, 'division' => $division);
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

    public function deleteDivision(Request $request)
    {
     
        $division_id = $request['item_id'];

     
        Division::where('id', $division_id)->delete();

        return "success";
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   $users = User::All();
        return view('system-mgmt/division/create')->with('users',$users);
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
         Division::create([
            'name' => $request['name']
        ]);

        return redirect()->intended('system-management/division')->with('users',$users);
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
        $division = Division::find($id);
        // Redirect to division list if updating division wasn't existed
        if ($division == null ) {
            return redirect()->intended('/system-management/division');
        }

        return view('system-mgmt/division/edit', ['division' => $division])->with('users',$users);
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
        $division = Division::findOrFail($id);
        $this->validateInput($request);
        $input = [
            'name' => $request['name']
        ];
        Division::where('id', $id)
            ->update($input);
        
        return redirect()->intended('system-management/division')->with('users',$users);
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
        Division::where('id', $id)->delete();
         return redirect()->intended('system-management/division')->with('users',$users);
    }

    /**
     * Search division from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $users = User::All();
        $constraints = [
            'name' => $request['name']
            ];

       $divisions = $this->doSearchingQuery($constraints);
       return view('system-mgmt/division/index', ['divisions' => $divisions, 'searchingVals' => $constraints])->with('users',$users);
    }

    private function doSearchingQuery($constraints) {
        $query = Division::query();
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
        'name' => 'required|max:60|unique:division'
    ]);
    }
}
