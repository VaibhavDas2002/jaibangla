<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DatabaseInstanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        set_time_limit(200);
        date_default_timezone_set('Asia/Kolkata');
    }
    public function index()
    {
        $designation = Auth::user()->designation_id;
        if ($designation == 'Admin') {
            return view('database_instance.db_ins');
        } else {
            return redirect("/")->with('success', 'User Unautharized!!');
        }
        
    }

    public function showSchema(Request $request)
    {

        $getSchemaName = DB::select(DB::raw("select s.nspname as table_schema,
                s.oid as schema_id,
                u.usename as owner
        from pg_catalog.pg_namespace s
        join pg_catalog.pg_user u on u.usesysid = s.nspowner
        where nspname not in ('information_schema', 'pg_catalog')
            and nspname not like 'pg_toast%'
            and nspname not like 'pg_temp_%'
        order by table_schema"));
        //  echo "<pre>"; print_r($getSchemaName);die();
        return response()->json(['getSchemaName' => $getSchemaName]);
    }

    public function showTable(Request $request)
    {
        $schemaname = $request->schamaName;
        $getTableName = DB::select(DB::raw(" SELECT table_schema, table_name FROM information_schema.tables 
        WHERE table_schema = '" . $schemaname . "'"));
        return response()->json(['getTableName' => $getTableName]);
    }
    public function showColumn(Request $request)
    {
        $tableNames = $request->tableName;
        $schemaname = $request->schamaName;
        $getColumnName = DB::select(DB::raw(" SELECT column_name,data_type,character_maximum_length,column_default,is_nullable
        FROM information_schema.columns
       WHERE table_schema = '" . $schemaname . "'
         AND table_name   = '" . $tableNames . "'"));
        return response()->json(['getColumnName' =>  $getColumnName]);
    }

    public function showFunction(Request $request)
    {
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {
            $schemaname = $request->schamaName;
            $query = "";
            $query = "select n.nspname as function_schema,
                    p.proname as function_name,
                    l.lanname as function_language,
                    case when l.lanname = 'internal' then p.prosrc
                        else pg_get_functiondef(p.oid)
                        end as definition,
                    pg_get_function_arguments(p.oid) as function_arguments,
                    t.typname as return_type
            from pg_proc p
            left join pg_namespace n on p.pronamespace = n.oid
            left join pg_language l on p.prolang = l.oid
            left join pg_type t on t.oid = p.prorettype 
            where n.nspname not in ('pg_catalog', 'information_schema') and n.nspname='" . $schemaname . "'
            order by function_schema,
                    function_name;";
            $getFunctionNameResult = DB::select(DB::raw($query));
            $response = array(
                'status' => 1, 'getFunctionName' => $getFunctionNameResult,
                'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
            );
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

    /*
        Get Query result
    */
    public function queryResult(Request $request)
    {
        // dd('OK');
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in form submit.');
            return response()->json($response, $statusCode);
        }
        try {
            $str = base64_decode($request->stringQuery);
            $selectServer = $request->selectServer;
            if (preg_match("/^\s*SELECT\s+/i", $str) && strcasecmp(substr(trim($str), 0, 6), 'SELECT') === 0) {
                // $result = array();
                $result = DB::connection($selectServer)->select($str);
                // dd($result);
                $table_columns = array_keys((array)$result[0]);
                $html = '';
                $html .= '<table id="example" class="table table-bordered" cellspacing="0" width="100%" style="font-size: 13px;">
                <thead>
                    <tr role="row" class="sorting_asc">';

                foreach ($table_columns as $col_name) {
                    $html .= '<th>' . $col_name . '</th>';
                }

                $html .= '</tr>
                </thead>
                <tbody>';
                foreach ($result as $key) {
                    $html .= '<tr>';
                    foreach ($table_columns as $col_name) {
                        $html .= '<td>' . $key->$col_name . '</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tbody></table>';
                $response = array(
                    'data' => $html
                );
            } else {
                // It's not a SELECT statement
                throw new \Exception("Only SELECT statements are allowed.");
            }
            // return response()->json(['data'=>$html]);
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

    // Get Sequence Response
    public function showSequence(Request $request)
    {
        // echo 1;die();
        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {
            $sequenceName = $request->sequenceName;
            $query = "";
            $query = "SELECT sequence_schema, sequence_name 
            FROM information_schema.sequences WHERE sequence_schema='" . $sequenceName . "'
            ORDER BY sequence_name";
            $getSequenceNameResult = DB::select(DB::raw($query));
            $response = array(
                'status' => 1, 'getSequenceName' => $getSequenceNameResult,
                'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
            );
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
}
