<?php
namespace App\Http\Controllers;
ini_set("display_errors", "1");
 error_reporting(E_ALL);
ini_set('memory_limit', '256M');
use Illuminate\Http\Request;
//use App\Http\Controllers\Redirect;
use App\designationMaster;
use App\Configduty;
use App\District;
use App\UrbanBody;
use App\SubDistrict;

use App\SchemecodeStatic;
use App\DocumentType;
use App\SchemeDocMap;
use App\Scheme;
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
use Auth;
use Config;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\AcceptRejectInfo;
use App\DsPhase;

class queryController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }
    public function querySelection(Request $request)
    {
       

       
       // $user_id = AuthChecker::getUserId();
        // $duty_schemes = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->get()->pluck('scheme_id')->toArray();
        // $scheme_list_constants = Config::get('constants.scheme_code_map');
        // $scheme_list = array();
        // foreach ($scheme_list_constants as $key => $arr) {
        //     $list_arr = array();
        //     if (in_array($key, $duty_schemes)) {
        //         $list_arr['scheme_id'] = $arr['scheme_id'];
        //         $list_arr['model_name'] = $arr['model_name'];
        //         $list_arr['scheme_name'] = $arr['scheme_name'];
        //         array_push($scheme_list, $list_arr);
        //     }
        // }
        // $mod_list = array_values($scheme_list);
        $report_type='A';
        $report_type_name='Query exeqution';
        $results=0;
        // dd($mod_list);
        return view('queryAdmin.scheme', [ 'report_type_name' => $report_type_name],['results' => $results]);
    }
    

   
    public function queryexecutionpost(Request $request)
    {
       //dd('ok');

        $userInput = trim($request->input('query_user'));
        //dd($userInput);
        $report_type_name='Query exeqution';

        // $this->validate($request, [
        //     // 'ben_fname' => 'required|min:3',
        //     'query' => 'required|string',
            
        // ]);
//dd($userInput);
        

        // Return the results to the user module
        try {
            // Use Laravel's DB::select() with parameter binding to execute the raw SQL query
            $results = DB::select(DB::raw($userInput));

          // dd($results);
    
            // Return the results to the user module
            return view('queryAdmin.scheme', ['results' => $results],[ 'report_type_name' => $report_type_name]);
        } catch (\Exception $e) {
           // dd($e);
            // Handle query execution errors
            return response()->json(['error' => 'Error executing the query: ' . $e->getMessage()], 500);
        }
    }
    
    


}
