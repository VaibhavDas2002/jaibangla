<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;
use App\User;


use App\Country;
use App\State;
use App\PicUpload;
use App\Policestation;
use Illuminate\Support\Facades\Storage;
use App\Configduty;
use Illuminate\Support\Facades\Log;
use App\OTPUser;
use Session;
use App\applicationModel;
use DateTime; 
use App\Http\Controllers\SmsSendController;
use App\Helpers\AuthChecker;


class ReportController extends Controller
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

    public function index() {
    $users = User::where('is_active',1)->get();
    $applictions = applicationModel::where('current_status', '=', 'READY')->paginate(10);

    //$users = User::All();
    //$user_id = AuthChecker::getUserId();
    //$duty = Configduty::where('user_id','=',$user_id)->first();
    //$applictions = applicationModel::where('current_status', '=', 'READY')->paginate(10);

    //->where('police_station_code', '=', $duty->ps_code)

    /*echo "<pre>";
    print_r($applictions);
    echo "</pre>";
    die();*/


        return view('system-mgmt/report/index', ['applictions' => $applictions])->with('users',$users);
    }

    public function app_pending() {
    $users = User::where('is_active',1)->get();
    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id','=',$user_id)->first();
    $applictions = applicationModel::where('is_fee_paid','=','Y')->where('current_status', '=', 'APPROVEDBYDCP' || 'ASSIGNEDTOSIDUE')->where('police_station_code', '=', $duty->ps_code)->paginate(10); 

    return view('system-mgmt/report/index', ['applictions' => $applictions])->with('users',$users);
    }

    public function app_processing() {
    $users = User::where('is_active',1)->get();
    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id','=',$user_id)->first();
    $applictions = applicationModel::where('is_fee_paid','=','Y')->where('current_status', '=', 'APPROVEDBYACP' ||'APPROVEDBYDCP')->where('police_station_code', '=', $duty->ps_code)->paginate(10); 

    return view('system-mgmt/report/index', ['applictions' => $applictions])->with('users',$users);
    }
    public function app_rejected() {
    $users = User::where('is_active',1)->get();
    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id','=',$user_id)->first();
    $applictions = applicationModel::where('is_fee_paid','=','Y')->where('is_rejected', '=', 'N' )->where('police_station_code', '=', $duty->ps_code)->paginate(10); 

    return view('system-mgmt/report/index', ['applictions' => $applictions])->with('users',$users);
    }
    


    public function exportExcel(Request $request) {

        $users = User::where('is_active',1)->get();
        $this->prepareExportingData($request)->export('xlsx');
        redirect()->intended('system-management/report')->with('users',$users);
    }

    public function exportPDF(Request $request) {
        $users = User::where('is_active',1)->get();
         $constraints = [
            'from' => $request['from'],
            'to' => $request['to']
        ];
        $employees = $this->getExportingData($constraints);
        $pdf = PDF::loadView('system-mgmt/report/pdf', ['employees' => $employees, 'searchingVals' => $constraints]);
        return $pdf->download('report_from_'. $request['from'].'_to_'.$request['to'].'pdf');
        // return view('system-mgmt/report/pdf', ['employees' => $employees, 'searchingVals' => $constraints]);
    }
    
    private function prepareExportingData($request) {
        $author = Auth::user()->username;
        $employees = $this->getExportingData(['from'=> $request['from'], 'to' => $request['to']]);
        return Excel::create('report_from_'. $request['from'].'_to_'.$request['to'], function($excel) use($employees, $request, $author) {

        // Set the title
        $excel->setTitle('List of hired employees from '. $request['from'].' to '. $request['to']);

        // Chain the setters
        $excel->setCreator($author)
            ->setCompany('HoaDang');

        // Call them separately
        $excel->setDescription('The list of hired employees');

        $excel->sheet('Hired_Employees', function($sheet) use($employees) {

        $sheet->fromArray($employees);
            });
        });
    }

    public function search(Request $request) {
        $users = User::where('is_active',1)->get();
        $constraints = [
            'from' => $request['from'],
            'to' => $request['to']
        ];

        $employees = $this->getHiredEmployees($constraints);
        return view('system-mgmt/report/index', ['employees' => $employees, 'searchingVals' => $constraints])->with('users',$users);
    }

    private function getHiredEmployees($constraints) {
        $employees = Employee::where('date_hired', '>=', $constraints['from'])
                        ->where('date_hired', '<=', $constraints['to'])
                        ->get();
        return $employees;
    }

    private function getExportingData($constraints) {
        return DB::table('employees')
        ->leftJoin('city', 'employees.city_id', '=', 'city.id')
        ->leftJoin('department', 'employees.department_id', '=', 'department.id')
        ->leftJoin('state', 'employees.state_id', '=', 'state.id')
        ->leftJoin('country', 'employees.country_id', '=', 'country.id')
        ->leftJoin('division', 'employees.division_id', '=', 'division.id')
        ->select('employees.firstname', 'employees.middlename', 'employees.lastname', 
        'employees.age','employees.birthdate', 'employees.address', 'employees.zip', 'employees.date_hired',
        'department.name as department_name', 'division.name as division_name')
        ->where('date_hired', '>=', $constraints['from'])
        ->where('date_hired', '<=', $constraints['to'])
        ->get()
        ->map(function ($item, $key) {
        return (array) $item;
        })
        ->all();
    }
}
