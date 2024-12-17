<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Department;
use App\User;

class DepartmentController extends Controller
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
        $department_class="active";
        if(request()->ajax())
        {
            

            $limit = $request->input('length');
            $offset = $request->input('start');
            //$serachvalue = $request->search['value'];
            $department = array();    
            $totalRecords = 0;
            $filterRecords = 0;
            $action="";
       
            if(empty($serachvalue)){   
                $department = Department::orderBy("name")->offset($offset)->limit($limit)->get(['id','name']); 
                $totalRecords = Department::count();
                $filterRecords = count($department);
            }
        
            return datatables()
            ->of($department)
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
          
            ->addColumn('action', function ($department) {
               
                $action ='<button class="btn btn-warning ben_view_button" onClick="editDepartment('.$department->id.')"><i class="fa fa-edit"></i></button>&nbsp;';
                $action .= '<button class="btn btn-danger ben_delete_button" onClick="deleteDepartment('.$department->id.')"><i class="fa fa-trash"></i></button>';
                return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
            
        }
      
       
        return view('system-mgmt/department/index')->with("department_class",$department_class);
        // $users = User::All();
        // $departments = Department::paginate(5);

       // return view('system-mgmt/department/index', ['departments' => $departments])->with('users',$users);
    }
    public function departmentSaveUpdate(Request $request){
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occered in Json call.');
            return response()->json($response, $statusCode);
        }
        $edit_code=$request->edit_code;
        if($edit_code==""){

            $validaton='required|max:60|regex:/^[a-z\s]+$/i|unique:department,name';
        }
        else{
            $validaton='required|max:60|regex:/^[a-z\s]+$/i|unique:department,name,' . $edit_code;
        }
         $this->validate($request, [
             
            'department_name' => $validaton,
           
            
                ], [
            'department_name.required' => 'Please enter department name.',
            'department_name.max' => 'Department name should not cross 60 characters.',
            'department_name.unique' => 'Duplicate Department name not allow.' ,
            'department_name.regex' => 'Department Name can consist of alphabetical characters and spaces only' ,
           
        ]);
        try {
          
            if($edit_code==""){
                $msg='Department Created Succesfully';
                Department::create([
                    'name' => $request['department_name'],
                  
                ]);
            }
         
            else{
                $msg= 'Department Updated Succesfully!';
            $input = [
                'name' => $request['department_name'],
                
            ];
         
            Department::where('id', $edit_code)
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

    public function editDepartment(Request $request){
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occered in Json call.');
            return response()->json($response, $statusCode);
        }
        try {
            $editId=$request->editId;
            $department = Department::select('name',  'id')->where('id', $editId)->first();

            $response = array('status' => 1, 'department' => $department);
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

    public function deleteDepartment(Request $request)
    {
     
        $department_id = $request['item_id'];

     
        Department::where('id', $department_id)->delete();

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
        return view('system-mgmt/department/create')->with('users',$users);
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
         Department::create([
            'name' => $request['name']
        ]);

        return redirect()->intended('system-management/department')->with('users',$users);
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
        $department = Department::find($id);
        // Redirect to department list if updating department wasn't existed
        if ($department == null) {
            return redirect()->intended('/system-management/department');
        }

        return view('system-mgmt/department/edit', ['department' => $department])->with('users',$users);
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
        $department = Department::findOrFail($id);
        $this->validateInput($request);
        $input = [
            'name' => $request['name']
        ];
        Department::where('id', $id)
            ->update($input);
        
        return redirect()->intended('system-management/department')->with('users',$users);
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
        Department::where('id', $id)->delete();
         return redirect()->intended('system-management/department')->with('users',$users);
    }

    /**
     * Search department from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $users = User::All();
        $constraints = [
            'name' => $request['name']
            ];

       $departments = $this->doSearchingQuery($constraints);
       return view('system-mgmt/department/index', ['departments' => $departments, 'searchingVals' => $constraints])->with('users',$users);
    }

    private function doSearchingQuery($constraints) {
        $query = department::query();
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
        'name' => 'required|max:60|unique:department'
    ]);
    }
}
