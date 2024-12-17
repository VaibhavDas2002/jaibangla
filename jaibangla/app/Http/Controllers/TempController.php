<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class TempController extends Controller
{

  public function index(Request $request)
  {
    try {

      $row_list = DB::table('temp_schema.sc_st_ben_list')->whereNull('created_at')->limit(300)->get();

      foreach ($row_list as $row_item) {

        $c_time1 = date("Y-m-d H:i:s", $row_item->timestamp_1);
        $c_time = date("Y-m-d", $row_item->timestamp_1);
        $input = [
          'created_at' => $c_time
        ];
        $input1 = [
          'created_at' => $c_time1
        ];
        if ($row_item->scheme_id == 1) {
          $update1 = DB::table('johar.beneficiary')->whereNull('created_at')->where('id', $row_item->id)->update($input);
          $update2 = DB::table('johar.ben_docs')->whereNull('created_at')->where('ben_id', $row_item->id)->update($input1);
        }
        if ($row_item->scheme_id == 3) {
          $update1 = DB::table('bandhu.beneficiary')->whereNull('created_at')->where('id', $row_item->id)->update($input);
          $update2 = DB::table('bandhu.ben_docs')->whereNull('created_at')->where('ben_id', $row_item->id)->update($input1);
        }
        dump($c_time);
      }
    } catch (\Exception $e) {
      dd($e);
    }
  }

  public function testDocImageServer() {
    
    try {
      $row_list = DB::connection('pgsql_13')->table('temp_schema.test_table200224')->get();
      dump($row_list);
      $input = array();
      $input['name']='Gopinath';
      $main_insert = DB::connection('pgsql_13')->table('temp_schema.test_table200224')->insert($input);
       $row_list = DB::connection('pgsql_mis')->table('temp_schema.test_table200224')->get();
      dd($row_list);
     // print '**************<br>';
    } catch (\Exception $e) {
      dd($e);
    }
  }
}
