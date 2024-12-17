<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Employee;
use App\Designation;
use App\Schemetype;
use App\Scheme;

class UserManagementController extends Controller
{
       /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/user-management';

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
    public function index()
    {


        $users = User::All();
        $designation = Designation::All();
        $employees = Employee::All();
        //$query ="";
       // $query_data = DB::select('SELECT u.id,u.username,u.designation_id_old,u.email,e.lastname,e.firstname,e.middlename  FROM users as u, employees as e WHERE u.emp_id= e.id');

        $query_data = DB::table('employees')->join('users', 'employees.id', '=', 'users.emp_id')->get();
     
       
        return view('users-mgmt/index', ['users' => $users,'designation' => $designation,'employees'=> $employees])->with('querydatas',$query_data);
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::All();
        $users = User::All();
        $designation = Designation::All();
        $schemes = Scheme::all();
        return view('users-mgmt/create')
        ->with('employees',$employees)
        ->with('users',$users)
        ->with('designations',$designation)
        ->with('schemes', $schemes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

          $constraints = [
            'username'=> 'required|max:60',
            'email' => 'required|max:60|unique:users',
            'password' =>'required|min:6',
            'employee_id' => 'required',
            'designation_id_old' =>'required',
            'scheme_name' =>'required',
            'mobile_no' =>'required'
            ];
        $input = [
            
            'username' => $request['username'],
            'email' => $request['email'],
            'emp_id' => $request['employee_id'],
            'designation_id_old' => $request['designation_id_old'],
            'user_scheme_id' => $request['scheme_name'],
            'mobile_no' => $request['mobile_no'],
        ];

        if ($request['password'] != null && strlen($request['password']) > 0) {
            $constraints['password'] = 'required|min:6|confirmed';
            $input['password'] =  bcrypt($request['password']);
        }

        $this->validate($request, $constraints);
        User::create($input);
        return redirect()->intended('/user-management');
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
        $user = User::find($id);
        $employees = Employee::All();
        $users = User::All();
        $designations = Designation::All();
        $schemes = Scheme::all();
        // Redirect to user list if updating user wasn't existed
        if ($user == null ) {
            return redirect()->intended('/user-management');
        }
        return view('users-mgmt/edit', ['user' => $user])->with('users',$users)
        ->with('employees',$employees)->with('designations',$designations)->with('schemes', $schemes);
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
        $user = User::findOrFail($id);


        $constraints = [
            'employee_id' => 'required',
            'username'=> 'required|max:60',
            'email' => 'required|max:60',
            'password'=>'required|min:6',
            'designation_id_old' =>'required'
            ];
        $input = [
            'emp_id' => $request['employee_id'],
            'username' => $request['username'],
            'email' => $request['email'],
            'designation_id_old'=> $request['designation_id_old'],
        ];
        if ($request['password'] != null && strlen($request['password']) > 0) {
            $constraints['password'] = 'required|min:6|confirmed';
            $input['password'] =  bcrypt($request['password']);
        }
        $this->validate($request, $constraints);
        User::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/user-management');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::where('id', $id)->delete();
         return redirect()->intended('/user-management');
    }

    /**
     * Search user from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {

        $username=$request[trim('username')];
        $name=$request[trim('employeename')];

        $constraints = [
            'username' => $request[trim('username')],
            'name'=>$request[trim('employeename')]
            /*'firstname' => $request['firstname'],
            'lastname' => $request['lastname'],
            'department' => $request['department']*/
            ];

       $querydatas = $this->doSearchingQuery($constraints,$username,$name);
       return view('users-mgmt/index', ['querydatas' => $querydatas, 'searchingVals' => $constraints]);
    }

 private function doSearchingQuery($constraints,$username,$name) {
        
        $newConstratint=strtolower(str_replace(' ', '', $username));
        $newConstratint1=strtolower(str_replace(' ','',$name));
        //dd($newConstratint,$newConstratint1);
        // $result = Employee::select(DB::raw('concat(firstname,middlename,lastname) AS name'))->
        // leftjoin('users', 'employees.id', '=', 'users.emp_id')->where('name','ilike',$newConstratint1.'%')->orWhere('username','ilike',$newConstratint.'%');

        $result=DB::table('employees')->leftjoin('users', 'employees.id', '=', 'users.emp_id')->where(DB::raw('concat(firstname,middlename,lastname)') , 'ILIKE' , $newConstratint1.'%')->where('users.username','ilike',$newConstratint.'%')->get();//select('employees.id as id','employees.firstname','employees.middlename','employees.lastname','users.username','users.designation_id_old','users.email')->get();
        //dd($result);
        //  $query = DB::table('employees')
        // ->leftJoin('users', 'employees.id', '=', 'users.emp_id')->selectRaw("CONCAT(employees.firstname,employees.middlename,employees.lastname) AS name,users.username,users.designation_id_old,users.email");
        // ->select('employees.id','employees.firstname','employees.middlename','employees.lastname','users.username','users.designation_id_old','users.email');

         //print_r($query);

         //$newConstratints = ['username'=>strtolower(str_replace(' ','',$username)),'name'=>strtolower(str_replace(' ','',$name))];
          //dd($newConstratints);

        // $fields = array_keys($newConstratints);
        // $index = 0;
        // foreach ($newConstratints as $newConstratint) {
        //     if ($newConstratint != null) {
        //         $query = $query->where( $fields[$index], 'ilike',$newConstratint.'%');
        //     }

        //     $index++;
        // }
       
        //dd($query);
        //$query=$query->join('employees', 'query.emp_id', '=', 'employees.id')->select('employees.first_name','employees.middle_name','employees.last_name','query.username','query.designation_id_old','email');
        return $result;
    }
    // private function doSearchingQuery($constraints) {
    //     $query = User::query();
    //     $fields = array_keys($constraints);
    //     $index = 0;
    //     foreach ($constraints as $constraint) {
    //         if ($constraint != null) {
    //             $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
    //         }

    //         $index++;
    //     }
    //     return $query->paginate(5);
    // }
    private function validateInput($request) {
        $this->validate($request, [
        'username' => 'required|max:20',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|min:6|confirmed',
        'employee_id' => 'required',
        'designation_id_old'=> 'required',
        'mobile_no'=>'required'
    ]);
    }

    public function findEmployee(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $tags = Employee::search($term)->limit(5)->get();
        $formatted_tags = [];
        foreach ($tags as $tag) {
            $formatted_tags[] = ['id' => $tag->id, 'text' => $tag->name];
        }
        return \Response::json($formatted_tags);
    }
}
