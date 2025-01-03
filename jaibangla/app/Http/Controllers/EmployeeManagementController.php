<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use App\Employee;
use App\City;
use App\State;
use App\Country;
use App\Department;
use App\Division;
use App\Designation;
use App\User;

class EmployeeManagementController extends Controller
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
    public function index()
    {
        $users = User::All();
        $employees = DB::table('employees')
        ->leftJoin('city', 'employees.city_id', '=', 'city.id')
        ->leftJoin('department', 'employees.department_id', '=', 'department.id')
        ->leftJoin('state', 'employees.state_id', '=', 'state.id')
        ->leftJoin('country', 'employees.country_id', '=', 'country.id')
        ->leftJoin('division', 'employees.division_id', '=', 'division.id')
        ->leftJoin('designation', 'employees.designation_id', '=', 'designation.id')
        ->select('employees.*', 'department.name as department_name', 'department.id as department_id', 'division.name as division_name', 'division.id as division_id')
        ->get();

        return view('employees-mgmt/index', ['employees' => $employees])->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $cities = City::all();
        // $states = State::all();
        $users = User::All();
        $countries = Country::all();
        $departments = Department::all();
        $divisions = Division::all();
        $states = State::all();
        $city = City::All();
        $designations = Designation::all();



        return view('employees-mgmt/create', ['countries' => $countries,
        'departments' => $departments, 'divisions' => $divisions, 'designations'=>$designations])->with('users',$users)->with('states',$states)->with('cities',$city);
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
        // Upload image  ->store('avatars')
         $keys = ['lastname', 'firstname', 'middlename', 'address', 'city_id', 'state_id', 'country_id', 'zip','age', 'birthdate', 'date_hired', 'department_id', 'department_id', 'division_id', 'designation_id'];
        $input = $this->createQueryInput($keys, $request);
        if ($request->file('picture')) {
            $path = $request->file('picture')->store('avatars');
            $input['picture'] = $path;
            //echo $path;
        }
        // Not implement yet
        // $input['company_id'] = 0;
        Employee::create($input);

        return redirect()->intended('/employee-management');
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
        $employee = Employee::find($id);
        // Redirect to state list if updating state wasn't existed
        if ($employee == null ) {
            return redirect()->intended('/employee-management');
        }
        $users = User::All();
        $cities = City::all();
        $states = State::all();
        $countries = Country::all();
        $departments = Department::all();
        $divisions = Division::all();
        $designations = Designation::all();
        return view('employees-mgmt/edit', ['employee' => $employee, 'cities' => $cities, 'states' => $states, 'countries' => $countries,
        'departments' => $departments, 'divisions' => $divisions, 'designations' => $designations])->with('users',$users);
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
        $var_path='11212';
        $users = User::All();
        $employee = Employee::findOrFail($id);
        $this->validateInput($request);
        // Upload image
        $keys = ['lastname', 'firstname', 'middlename', 'address', 'city_id', 'state_id', 'country_id', 'zip',
        'age', 'birthdate', 'date_hired', 'department_id', 'department_id', 'division_id', 'designation_id'];
        $input = $this->createQueryInput($keys, $request);
        if ($request->file('picture')) {
            $path = $request->file('picture')->store('avatars');
            $input['picture'] = $path;
            echo $path;
        }

        Employee::where('id', $id)
            ->update($input);

        return redirect()->intended('/employee-management')->with('users',$users);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {    $users = User::All();
         Employee::where('id', $id)->delete();
         return redirect()->intended('/employee-management')->with('users',$users);
    }

    /**
     * Search state from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $users = User::All();
        $name=$request[trim('employeename')];
        //list($firstname, $middlename, $lastname) = explode(' ', $name);
        $constraints = [
            'firstname' => $name,
            //'middlename' => $middlename,
            //'lastname' => $lastname,
            //'department.name' => $request[trim('department_name')],
            
            ];
            //dd($constraints);
        $employees = $this->doSearchingQuery($constraints,$name);
        //$constraints['department_name'] = $request['department_name'];
        return view('employees-mgmt/index', ['employees' => $employees, 'searchingVals' => $constraints])->with('users',$users);
    }

    private function doSearchingQuery($constraints,$name) {
        $query = DB::table('employees')
        ->leftJoin('city', 'employees.city_id', '=', 'city.id')
        ->leftJoin('department', 'employees.department_id', '=', 'department.id')
        ->leftJoin('state', 'employees.state_id', '=', 'state.id')
        ->leftJoin('country', 'employees.country_id', '=', 'country.id')
        ->leftJoin('division', 'employees.division_id', '=', 'division.id')
         ->select('employees.firstname as employee_name', 'employees.*','department.name as department_name', 'department.id as department_id', 'division.name as division_name', 'division.id as division_id');

        $newConstratint = strtolower(str_replace(' ','',$name));
        //dd($newConstratint);

        $result = Employee::where(DB::raw('concat(firstname,middlename,lastname)') , 'ILIKE' , $newConstratint.'%')->select('employees.firstname','employees.middlename','employees.lastname','employees.address','employees.department_id','employees.*')->get();

       
       
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'ilike', '%'.strtolower($constraint).'%');
                //$query = $query->whereRaw($fields[$index]'like',['%'.strtolower($constraint).'%']);
            }

            $index++;
        }//dd($query->paginate(5));
        return $result;//->paginate(5);
    }

     /**
     * Load image resource.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function load($name) {
         $path = storage_path().'/app/avatars/'.$name;
        if (file_exists($path)) {
            return Response::download($path);
        }
    }

    private function validateInput($request) {
        $this->validate($request, [
            'lastname' => 'required|max:60',
            'firstname' => 'required|max:60',
            'middlename' => 'string|nullable',
            'address' => 'nullable|max:120',
            // 'country_id' => 'required',
            // 'zip' => 'required|max:10',
            // 'age' => 'required',
            // 'birthdate' => 'required',
            // 'date_hired' => 'required',
            'department_id' => 'required',
            'designation_id' => 'required',
            //'division_id' => 'required'
        ]);
    }

    private function createQueryInput($keys, $request) {
        $queryInput = [];
        for($i = 0; $i < sizeof($keys); $i++) {
            $key = $keys[$i];
            $queryInput[$key] = $request[$key];
        }

        return $queryInput;
    }
}
