<?php

namespace App\Http\Controllers;

use App\Helpers\AuthChecker;
use App\Scheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;



class JBtoSocialRegistryController extends Controller
{
  public function index()
  {
    $auth = AuthChecker::AdminChecker();
    if ($auth) {
      $schemes = Scheme::where('is_active', 1)->orderBy('id')->get();
      return view('Social_Registry/index', ['schemes' => $schemes]);
    }
  }

  public function socialRegistryPost(Request $request)
  {
    try {
      // dd($request->all());
      $scheme_id = (int) $request->scheme_id;
      $fin_year = $request->fin_year;
      $month = $request->month;

      $rules = [];
      $attributes = [];
      $filedRules = [
        'scheme_id' => 'required',
        'fin_year' => 'required',
        'month' => 'required',
      ];
      $filedAttributes = [
        'scheme_id' => 'Scheme ',
        'fin_year' => 'Financial Year',
        'month' => 'Month',
      ];
      $rules = array_merge($rules, $filedRules);
      $attributes = array_merge($attributes, $filedAttributes);
      $messages = [
        'required' => 'The :attribute field is required.',
        'max' => 'Total :max characters allowed for :attribute.',
        'digits' => 'The :attribute must be exactly :digits digits.',
      ];
      $validator = Validator::make($request->all(), $rules, $messages, $attributes);
      if (!$validator->passes()) {
        // dd($validator->errors()->all());
        return redirect()->route('jb-social-registry')
          ->withErrors($validator->errors()->all())
          ->withInput();

      } else {

        $is_fun_call = DB::connection('pgsql_paywrite')->select("SELECT social_registry.send_approved_beneficiary_transaction_data_to_srs(in_scheme_id => " . $scheme_id . ", in_ld_lot_month => " . $month . ", in_fin_year => " . $fin_year . ")");
        $is_fun_call = $is_fun_call[0]->send_approved_beneficiary_transaction_data_to_srs;
        // $is_fun_call = 1 ; 
        if ($is_fun_call > 0) {
          $return_txt = "Total (" . $is_fun_call . ") Data sent to Social Registry Successfully.";
          $response = [
            'status' => 1,
            'msg' => $return_txt,
            'type' => 'green',
            'icon' => 'fa fa-check',
            'title' => 'Success',
          ];
          return response()->json($response);
          // return redirect()->route('social-registry')->with('success', $return_txt);
        } else {
          return response()->json([
            'status' => 0,
            'msg' => 'Data not sent to Social Registry',
            'type' => 'red',
            'icon' => 'fa fa-times',
            'title' => 'Error',
          ]);
          // return redirect()->route('social-registry')->with('error', 'Data not sent to Social Registry');
        }
      }
    } catch (Exception $e) {
      // dd($e);
      return response()->json([
        'status' => 0,
        'msg' => 'Something went wrong ... please try again',
        'type' => 'red',
        'icon' => 'fa fa-times',
        'title' => 'Error',
      ]);
    }



  }
}
