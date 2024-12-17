<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\SchemeConfig;
use Illuminate\Http\Request;
class SchemeConfigController extends Controller
{

  public function index()
  {
    $user_id = Auth::user()->id;
    $designation = Auth::user()->designation_id_old;
    $mapObj = DB::connection('pgsql_mis')->table('public.duty_assignement')->where('user_id', $user_id)->where('is_active', 1)->first();
    $scheme = DB::connection('pgsql_mis')->select('select id,scheme_name from public.m_scheme where  id in (select scheme_id from public.duty_assignement where user_id=' . $user_id . ' and is_active=1) and is_active=1 order by scheme_name');
    $schemes = DB::connection('pgsql_mis')->select('select * from public.m_scheme where is_active = 1');
    if (count($scheme) > 0) {
      return view('Scheme-config/index', ['schemes' => $scheme, 'scheme_all' => $schemes]);
    } else {
      return redirect("/")->with('success', 'User disabled. No scheme assign to this user');
    }

  }

  public function store(Request $request)
  {
    $cross_schemes = $this->to_pg_array($request->schemelist);

      // Define validation rules and custom messages
      $rules = [
          'config_type' => 'required|in:0,1',
          'scheme_id' => 'required',
          'schemelist' => 'required_if:config_type,1|array',  // Ensure schemelist is an array if config_type is 1
          'field_type' => 'required|array',
      ];
  
      $messages = [
          'config_type.required' => 'Please select the configuration type.',
          'scheme_id.required' => 'Please select a scheme.',
          'schemelist.required_if' => 'Please select cross schemes when "Cross-Scheme" is selected.',
          'field_type.required' => 'Please select at least one field type.',
      ];
  
      // Validate the incoming request
      $validator = Validator::make($request->all(), $rules, $messages);
  
      if ($validator->fails()) {
          return redirect()->back()->withErrors($validator)->withInput();
      }

      
      
      try {
          // Loop through the selected field types and save configuration
          foreach ($request->field_type as $fieldType) {
              $schemeConfig = new SchemeConfig();
              $schemeConfig->is_cross = $request->config_type;
              $schemeConfig->scheme_id = $request->scheme_id;
              $schemeConfig->cross_scheme = $cross_schemes;
              $schemeConfig->field_type = $fieldType;
              $schemeConfig->save();  // Save the scheme configuration to the database
          }
  
          // Flash success message and redirect
          Session::flash('success', 'Scheme configuration saved successfully!');
          return redirect()->back()->withInput();
      } catch (\Exception $e) {
          // In case of an error, dump the error and redirect
          dd($e);
          Session::flash('message', 'An error occurred while saving the configuration. Please try again.');
          return redirect()->back()->withInput();
      }
  }
  
  
function to_pg_array($set) {
    settype($set, 'array'); // can be called with a scalar or array
    $result = array();
    foreach ($set as $t) {
        if (is_array($t)) {
            $result[] = to_pg_array($t);
        } else {
            $t = str_replace('"', '\\"', $t); // escape double quote
            if (! is_numeric($t)) // quote only non-numeric values
                $t = '"' . $t . '"';
            $result[] = $t;
        }
    }
    return '{' . implode(",", $result) . '}'; // format
}


}
