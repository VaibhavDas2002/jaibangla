<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\imageFetch;
use App\Scheme;
use Exception;
use Illuminate\Support\Facades\Auth;

class ImageFetchDBControllerSchemewise extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
        // $this->district_code = 303;
    }
    public function SchemeSelect(Request $request)
    {

        $schemes = Scheme::get();
        return view(
            'Image-fetch-DB/scheme',
            [
                'scheme_list' => $schemes,
            ]
        );
    }
    public function view(Request $request)
    {
        // dd(1);
        try{$scheme_id = $request->scheme_id;
        $type = $request->type;
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        $schema = $scheme_obj->short_code;
        $s_name = $scheme_obj->scheme_name;
        $table = $schema . '.ben_docs';
        if ($type == 1) {
            $type_name = 'Beneficiary Documents';
            $table = $schema . '.ben_docs';
            $query = "Select 
            district_code as distict_code,
            district_name as district_name,
            COALESCE(sum(c1),0) as pending_doc_count ,
            COALESCE(sum(c2),0) as doc_transafer_complete_count ,
            COALESCE(sum(c3),0) as doc_inactive_count ,
            COALESCE(sum(c4),0) as doc_not_found_count,
            COALESCE(sum(c5),0) as total_doc ,
            COALESCE(sum(c6),0) as total_inactive_pending ,
            COALESCE(sum(c7),0) as total_inactive_done  
            from public.m_district d left join
            (
            select created_by_dist_code,
                count(1) as c5,
                sum(case when encript_status is null and exception_happen is null  and is_active=true and created_by_dist_code is not null then 1 else 0 end )   as c1,
                sum(case when encript_status =1 and is_active=true and created_by_dist_code is not null then 1 else 0 end )   as c2,
                sum(case when (is_active=false or is_active is null) then 1 else 0 end )   as c3,
                sum(case when encript_status is null and exception_happen is null and created_by_dist_code is not null and (is_active=false or is_active is null) then 1 else 0 end ) as c6,
                sum(case when encript_status =1 and (is_active=false or is_active is null) then 1 else 0 end ) as c7,
                sum(case when exception_happen is not null then 1 else 0 end )   as c4 
            FROM " . $table . " b 
                
            group by created_by_dist_code  
            )b  on d.district_code = b.created_by_dist_code group by d.district_code, d.district_name order by d.district_code";
        } else {
            $type_name = 'Beneficiary Archive Documents';
            $table = $schema . '.ben_docs_arc';
            $query = "Select 
            district_code as distict_code,
            district_name as district_name,
            COALESCE(sum(c1),0) as pending_doc_count ,
            COALESCE(sum(c2),0) as doc_transafer_complete_count ,
            COALESCE(sum(c3),0) as doc_inactive_count ,
            COALESCE(sum(c4),0) as doc_not_found_count,
            COALESCE(sum(c5),0) as total_doc ,
            COALESCE(sum(c6),0) as total_inactive_pending ,
            COALESCE(sum(c7),0) as total_inactive_done  
            from public.m_district d left join
            (
            select created_by_dist_code,
                count(1) as c5,
                sum(case when encript_status is null and exception_happen is null and exception_happen is null and created_by_dist_code is not null then 1 else 0 end )   as c1,
                sum(case when encript_status =1 and created_by_dist_code is not null then 1 else 0 end )   as c2,
                0 as c3,
                0 as c6,
                0 as c7,
                sum(case when exception_happen is not null then 1 else 0 end )   as c4 
            FROM " . $table . " b 
                
            group by created_by_dist_code  
            )b  on d.district_code = b.created_by_dist_code group by d.district_code, d.district_name order by d.district_code";
        }

        $results = DB::connection('pgsql')->select($query);
        // dd($results);
        return view(
            'Image-fetch-DB/imageStore',
            [
                'scheme_id' => $scheme_id,
                'type_name' => $type_name,
                'result' => $results,
                'scheme_name' => $s_name
            ]
        );}
        catch(Exception $e) {
            dd($e);
        }
    }
    public function storeCronDocTransfer(Request $request)
    {
        // echo 1;die;
        // $scheme_id = 2;

        // $filename = 'stop_payment_files.txt';
        // $fetch_file = storage_path('app/stop_payment/'.$filename);
        
        // $searchfor = '1674029888.jpeg';
        // $contents = file_get_contents($fetch_file);
        // $pattern = preg_quote($searchfor, '/');
        // $pattern = "/^.*$pattern.*\$/m";

        // // search, and store all matching occurences in $matches
        // if (preg_match_all($pattern, $contents, $matches))
        // {
        //     $file_path1 = 'stop_payment';
        //     $myArray1 = substr(implode("\n", $matches[0]), 2);
        //     echo "Found matches:\n";
        //     echo $file_path1;
            
        // }
        // else
        // {
        // echo "No matches found";
        // }

        // die();

        $scheme_id = $request->scheme_id;
        $district_code = $request->dist_code;
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        $schema = $scheme_obj->short_code;
        $table = $schema . '.ben_docs';
        $c_time =  date('Y-m-d H:i:s', time());
        $exc_ben_id = NULL;
        $exc_file_name = NULL;
        $exc_doc_type_id = NULL;
        $file_path1='keep_farmer';
        
        /*if($scheme_id==1){
            $file_path1='keep_st';
            $file_path2='keep';
            $file_path3='keep_back';
        }
        else if($scheme_id==2){
            $file_path1='keep_manabik';
            $file_path2='keep_wcd';
            $file_path3='keep_back_wcd';
        }
        else if($scheme_id==3){
            $file_path1='keep_wcd_cap';
            $file_path2='keep';
            $file_path3='keep_back_wcd';
        }
        else if($scheme_id==5 || $scheme_id==6 || $scheme_id==7 || $scheme_id==8 || $scheme_id==9){
            $file_path1='keep';
            $file_path2='keep_back';
            $file_path3='';
        }
        else if($scheme_id==10){
            $file_path1='keep_oap';
            $file_path2='keep_wcd';
            $file_path3='keep_back_wcd';
        }
        else if($scheme_id==11){
            $file_path1='keep_wp';
            $file_path2='keep_wcd';
            $file_path3='keep_back_wcd';
        }
        else if($scheme_id==13){
            $file_path1='keep_farmer';
            $file_path2='keep';
            $file_path3='keep_back_farmer';
        }
        else if($scheme_id==17){
            $file_path1='keep_ICAD';
            $file_path2='keep';
            $file_path3='keep_back_wcd';
        }*/
        try {
            $validator = Validator::make($request->all(), [
                'limit' => 'required',
                'scheme_id' => 'required',
                'dist_code' => 'required'
            ]);
            if ($validator->fails()) {

                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {

                // ini_set('max_execution_time', 20);
                $back_url = 'scheme-select-image';
                $limit = $request->limit;
                $ip_address = request()->ip();
                $rows = DB::table($schema . '.ben_docs')->whereNull('encript_status')->where('created_by_dist_code', $district_code)->whereNotNull('created_by_dist_code')->where('is_active', TRUE)
                // ->whereNull('exception_happen')
                // ->whereNull('lb_application_id')
                ->whereNotNull('ben_id')
                // ->whereNotNull('new_doc_name')
                // ->whereNotNull('new_doc_type_id')
                // ->whereRaw("( doc_name like '%/investigation_report/%' )") 
                // ->whereRaw("(created_at::date>='2022-01-01'::date)")
                // ->whereIn('ben_id', [5050980])
                ->limit($limit)->get();
                // dump($rows);
                if (count($rows) > 0) {
                    $i = 0;
                    $j = 0;
                    $ck = 0;
                    DB::connection('pgsql')->beginTransaction();
                    DB::connection('pgsql_encwrite')->beginTransaction();
                    foreach ($rows as $key) {
                        $exc_ben_id = $key->ben_id;
                        $exc_file_name = $key->doc_name;
                        $exc_doc_type_id = $key->doc_type_id;
                        $ben_id = $key->ben_id;

                        $document_name = $key->doc_name;
                        $myArray = explode('/', $document_name);
                        $myArray1 = trim(last($myArray), " }]");
                        // dump($myArray1);
                        // dump(storage_path('app/'.$file_path1.'/') . '//' . $myArray1);
                        if (file_exists(storage_path('app/'.$file_path1.'/') . '//' . $myArray1)) {
                            $mime_type = mime_content_type(storage_path('app/'.$file_path1.'/') . '//' . $myArray1);
                            $file_time=date ("Y-m-d H:i:s", filemtime(storage_path('app/'.$file_path1.'/') . '//' . $myArray1));
                            $info = pathinfo(storage_path('app/'.$file_path1.'/') . '//' . $myArray1);
                            // dump($info);
                            $extension = $info['extension'];
                            $base64 = base64_encode(file_get_contents(storage_path('app/'.$file_path1.'/') . '//' . $myArray1));
                            $pension_details = array();
                            $pension_details['beneficiary_id'] = $key->ben_id;
                            $pension_details['scheme_id'] = $scheme_id;
                            $pension_details['document_type'] = $key->doc_type_id;
                            $pension_details['attched_document'] = $base64;
                            $pension_details['document_extension'] = $extension;
                            $pension_details['document_mime_type'] = $mime_type;
                            if (!empty($key->created_by_local_body_code)) {
                                $pension_details['created_by_local_body_code'] = $key->created_by_local_body_code;
                            }
                            if (!empty($key->created_by_dist_code)) {
                                $pension_details['created_by_dist_code'] = $key->created_by_dist_code;
                            }
                            if (!empty($key->created_by)) {
                                $pension_details['created_by'] = $key->created_by;
                            }
                            if (!empty($key->created_by_level)) {
                                $pension_details['created_by_level'] = $key->created_by_level;
                            }
                            
                            
                            
                            $pension_details['created_at'] = $key->created_at;
                            $pension_details['updated_at'] = $key->updated_at;
                            $pension_details['ip_address'] = $ip_address;
                            $pension_details['file_last_modified_time'] = $file_time;
                            $pension_details['doc_type_name'] = $key->doc_type_name;

                            $values = '';
                            if (!empty($key->ben_id)) {
                                $values .= "" . $key->ben_id . ",";
                            }
                            else {
                                $values .= "NULL,";
                            }

                            $values .= "".$scheme_id.", ". $key->doc_type_id .", '". $base64 ."', ";

                            if (!empty($key->created_by_level)) {
                                $values .= "'" . $key->created_by_level . "',";
                            }
                            else {
                                $values .= "NULL,";
                            }

                            if (!empty($key->created_at)) {
                                $values .= "'" . $key->created_at . "',";
                            }
                            else {
                                $values .= "NULL,";
                            }

                            if (!empty($key->updated_at)) {
                                $values .= "'" . $key->updated_at . "',";
                            }
                            else {
                                $values .= "NULL,";
                            }

                            if (!empty($key->created_by)) {
                                $values .= "" . $key->created_by . ",";
                            }
                            else {
                                $values .= "NULL,";
                            }
                            $doc_name = str_replace("'", "''", $key->doc_type_name);
                            $values .= "'". $ip_address ."', '" . $extension . "', '" . $mime_type . "', " . $key->created_by_dist_code . ", " . $key->created_by_local_body_code . ", '" . $file_time . "', '" . $doc_name . "'";

                            // $values .= ", -60";
                            // $values .= ",". $key->lb_application_id ."";
                            // dump($values);
                            // dump($key->lb_application_id);

                            if ($key->ben_id && $key->doc_type_id) {
                                // $update = DB::connection('pgsql')->update(DB::raw('UPDATE '.$table.' SET encript_status=1 and upload_at= current_date WHERE ben_id='.$key->ben_id.' and doc_type_id='.$key->doc_type_id.''));
                                try {
                                    // $insert_data = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($pension_details);
                                    $insert_data = DB::connection('pgsql_encwrite')->statement("INSERT INTO jb_doc.ben_attach_documents(
                                        beneficiary_id, scheme_id, document_type, attched_document, created_by_level, created_at, updated_at, created_by, ip_address, document_extension, document_mime_type, created_by_dist_code, created_by_local_body_code, file_last_modified_time, doc_type_name)
                                        VALUES(".$values.") ON CONFLICT DO NOTHING;
                                        ");
                                        // dump($insert_data);
                                    if ($insert_data) {
                                        $i++;
                                        $input = ['encript_status' => 1, 'upload_at' => $c_time, 'exception_happen'=> null];
                                        $update = DB::connection('pgsql')->table($table)->where('ben_id', $key->ben_id)->where('doc_type_id', $key->doc_type_id)->where('is_active', TRUE)->update($input);
                                        // dump($update);
                                        if ($update) {
                                            $j++;
                                        }
                                    }
                                } catch (\Exception $e) {
                                    dump($e);
                                }

                                
                            }
                        }
                        /*else if (file_exists(storage_path('app/'.$file_path2.'/') . '//' . $myArray1)) {
                            $file_time=date ("Y-m-d H:i:s", filemtime(storage_path('app/'.$file_path2.'/') . '//' . $myArray1));
                            $mime_type = mime_content_type(storage_path('app/'.$file_path2.'/') . '//' . $myArray1);
                            $info = pathinfo(storage_path('app/'.$file_path2.'/') . '//' . $myArray1);
                            $extension = $info['extension'];
                            $base64 = base64_encode(file_get_contents(storage_path('app/'.$file_path2.'/') . '//' . $myArray1));
                            $pension_details = array();
                            $pension_details['beneficiary_id'] = $key->ben_id;
                            $pension_details['scheme_id'] = $scheme_id;
                            $pension_details['document_type'] = $key->doc_type_id;
                            $pension_details['attched_document'] = $base64;
                            $pension_details['document_extension'] = $extension;
                            $pension_details['document_mime_type'] = $mime_type;

                            if (!empty($key->created_by_local_body_code)) {
                                $pension_details['created_by_local_body_code'] = $key->created_by_local_body_code;
                            }
                            if (!empty($key->created_by_dist_code)) {
                                $pension_details['created_by_dist_code'] = $key->created_by_dist_code;
                            }
                            if (!empty($key->created_by)) {
                                $pension_details['created_by'] = $key->created_by;
                            }
                            if (!empty($key->created_by_level)) {
                                $pension_details['created_by_level'] = $key->created_by_level;
                            }

                            $pension_details['created_at'] = $key->created_at;
                            $pension_details['updated_at'] = $key->updated_at;
                            $pension_details['ip_address'] = $ip_address;
                            $pension_details['file_last_modified_time'] = $file_time;
                            $pension_details['doc_type_name'] = $key->doc_type_name;
                            if ($key->ben_id && $key->doc_type_id) {
                                // $update = DB::connection('pgsql')->update(DB::raw('UPDATE '.$table.' SET encript_status=1 and upload_at= current_date WHERE ben_id='.$key->ben_id.' and doc_type_id='.$key->doc_type_id.''));


                                $insert_data = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($pension_details);
                                if ($insert_data) {
                                    $i++;
                                    $input = ['encript_status' => 1, 'upload_at' => $c_time];
                                    $update = DB::connection('pgsql')->table($table)->where('ben_id', $key->ben_id)->where('doc_type_id', $key->doc_type_id)->where('is_active', TRUE)->update($input);
                                    if ($update) {
                                        $j++;
                                    }
                                }
                            }
                        }
                        else if (file_exists(storage_path('app/'.$file_path3.'/') . '//' . $myArray1)) {
                            $file_time=date ("Y-m-d H:i:s", filemtime(storage_path('app/'.$file_path3.'/') . '//' . $myArray1));
                            $mime_type = mime_content_type(storage_path('app/'.$file_path3.'/') . '//' . $myArray1);
                            $info = pathinfo(storage_path('app/'.$file_path3.'/') . '//' . $myArray1);
                            $extension = $info['extension'];
                            $base64 = base64_encode(file_get_contents(storage_path('app/'.$file_path3.'/') . '//' . $myArray1));
                            $pension_details = array();
                            $pension_details['beneficiary_id'] = $key->ben_id;
                            $pension_details['scheme_id'] = $scheme_id;
                            $pension_details['document_type'] = $key->doc_type_id;
                            $pension_details['attched_document'] = $base64;
                            $pension_details['document_extension'] = $extension;
                            $pension_details['document_mime_type'] = $mime_type;

                            if (!empty($key->created_by_local_body_code)) {
                                $pension_details['created_by_local_body_code'] = $key->created_by_local_body_code;
                            }
                            if (!empty($key->created_by_dist_code)) {
                                $pension_details['created_by_dist_code'] = $key->created_by_dist_code;
                            }
                            if (!empty($key->created_by)) {
                                $pension_details['created_by'] = $key->created_by;
                            }
                            if (!empty($key->created_by_level)) {
                                $pension_details['created_by_level'] = $key->created_by_level;
                            }

                            $pension_details['created_at'] = $key->created_at;
                            $pension_details['updated_at'] = $key->updated_at;
                            $pension_details['ip_address'] = $ip_address;
                            $pension_details['file_last_modified_time'] = $file_time;
                            $pension_details['doc_type_name'] = $key->doc_type_name;
                            if ($key->ben_id && $key->doc_type_id) {
                                // $update = DB::connection('pgsql')->update(DB::raw('UPDATE '.$table.' SET encript_status=1 and upload_at= current_date WHERE ben_id='.$key->ben_id.' and doc_type_id='.$key->doc_type_id.''));


                                $insert_data = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($pension_details);
                                if ($insert_data) {
                                    $i++;
                                    $input = ['encript_status' => 1, 'upload_at' => $c_time];
                                    $update = DB::connection('pgsql')->table($table)->where('ben_id', $key->ben_id)->where('doc_type_id', $key->doc_type_id)->where('is_active', TRUE)->update($input);
                                    if ($update) {
                                        $j++;
                                    }
                                }
                            }
                        }*/
                        else {
                            try {
                                DB::connection('pgsql')->table($schema . '.ben_docs_exception')->insert([
                                    'ben_id' => $exc_ben_id,
                                    'file_name' => $exc_file_name,
                                    'exception_msg' => 'File Not Found in both folder',
                                    'exception_type' => 2,
                                    'doc_type_id' => $exc_doc_type_id
                                ]);
                                $update = DB::connection('pgsql')->table($schema . '.ben_docs')->where('ben_id', $exc_ben_id)->where('doc_type_id', $exc_doc_type_id)->where('is_active', TRUE)->update(['exception_happen'=>2]);
                                $ck=1;
                            } catch (\Exception $ee) {
                                
                            }
                        }
                    }
                    // dump($i); dump($j); dump($ck); dd('OK');
                    if (($i == $j) && ($i > 0 && $j > 0)) {
                        DB::connection('pgsql')->commit();
                        DB::connection('pgsql_encwrite')->commit();
                        return response()->json([
                            'status' => 200,
                            'i' => $i,
                            'j' => $j,
                            'message' => 'Total - '.$i.' Images Updated Successfully',
                        ]);
                    } else {
                        if ($ck==1) {
                            DB::connection('pgsql')->commit();
                            DB::connection('pgsql_encwrite')->commit();
                            return response()->json([
                                'status' => 200,
                                'message' => 'Total - '.$i.' Images Updated Successfully',
                            ]);
                        } else {
                            DB::connection('pgsql')->rollback();
                            DB::connection('pgsql_encwrite')->rollback();
                            // return redirect($back_url)->with('error', 'Error! Please try again.');
                            return response()->json([
                                'status' => 500,
                                'i' => $i,
                                'j' => $j,
                                'message' => 'Update and insert count not matched',
                            ]);
                        }
                        
                        
                    }
                }
                else {
                    return response()->json([
                        'status' => 200,
                        'message' => 'No Record Found',
                    ]);
                }
            }
        } catch (\Exception $e) {
            if ($e instanceof  \ErrorException) {
                // dump($exc_ben_id);
                // dump($exc_file_name);
                // dump($schema);
                // dump($e->getMessage());
                // dd($e);
                try {
                    DB::connection('pgsql')->table($schema . '.ben_docs_exception')->insert([
                        'ben_id' => $exc_ben_id,
                        'file_name' => $exc_file_name,
                        'exception_msg' => $e->getMessage(),
                        'exception_type' => 1,
                        'doc_type_id' => $exc_doc_type_id
                    ]);
                    $update = DB::connection('pgsql')->table($schema . '.ben_docs')->where('ben_id', $exc_ben_id)->where('doc_type_id', $exc_doc_type_id)->where('is_active', TRUE)->update(['exception_happen'=>1]);
                    DB::connection('pgsql')->commit();
                    DB::connection('pgsql_encwrite')->commit();
                } catch (\Exception $ee) {
                    DB::connection('pgsql')->rollback();
                    DB::connection('pgsql_encwrite')->rollback();
                    return response()->json(['status' => 500,
                'message' => 'Ben Id - ' .$exc_ben_id . ' => '.$ee->getMessage()]);
                }
                
                return response()->json(['status' => 500,
                'message' => 'Ben Id - ' .$exc_ben_id . ' => '.$e->getMessage()]);
            }
            
            dd($e);
        }
    }
    public function storeblkulb(Request $request)
    {
        $scheme_id = $request->scheme_id;
        $district_code = $request->dist_code;
        $created_by_local_body_code = $request->created_by_local_body_code;
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        $schema = $scheme_obj->short_code;
        $table = $schema . '.ben_docs';
        $c_time =  date('Y-m-d H:i:s', time());
        $exc_ben_id = NULL;
        $exc_file_name = NULL;
        $exc_doc_type_id = NULL;
        try {
            $validator = Validator::make($request->all(), [
                'limit' => 'required',
                'scheme_id' => 'required',
                'dist_code' => 'required',
                'created_by_local_body_code' => 'required'
            ]);
            if ($validator->fails()) {

                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {

                ini_set('max_execution_time', 20);
                $back_url = 'scheme-select-image';
                $limit = $request->limit;
                $ip_address = request()->ip();
                $rows = DB::table($schema . '.ben_docs')->whereNull('encript_status')->where('created_by_local_body_code', $created_by_local_body_code)->where('created_by_dist_code', $district_code)->whereNull('exception_happen')->whereNotNull('created_by_dist_code')->where('is_active', TRUE)->limit($limit)->get();
                // dd($rows);
                if (count($rows) > 0) {
                    $i = 0;
                    $j = 0;
                    DB::connection('pgsql')->beginTransaction();
                    DB::connection('pgsql_encwrite')->beginTransaction();
                    foreach ($rows as $key) {
                        $exc_ben_id = $key->ben_id;
                        $exc_file_name = $key->doc_name;
                        $exc_doc_type_id = $key->doc_type_id;
                        $ben_id = $key->ben_id;

                        $document_name = $key->doc_name;
                        $myArray = explode('/', $document_name);
                        $myArray1 = trim(last($myArray), " }]");
                        if (file_exists(storage_path('app/keep_manabik/') . '//' . $myArray1)) {
                            $mime_type = mime_content_type(storage_path('app/keep_manabik/') . '//' . $myArray1);
                            $info = pathinfo(storage_path('app/keep_manabik/') . '//' . $myArray1);
                            $extension = $info['extension'];
                            $base64 = base64_encode(file_get_contents(storage_path('app/keep_manabik/') . '//' . $myArray1));
                            $pension_details = array();
                            $pension_details['beneficiary_id'] = $key->ben_id;
                            $pension_details['scheme_id'] = $scheme_id;
                            $pension_details['document_type'] = $key->doc_type_id;
                            $pension_details['attched_document'] = $base64;
                            $pension_details['document_extension'] = $extension;
                            $pension_details['document_mime_type'] = $mime_type;
                            if (!empty($key->created_by_local_body_code)) {
                                $pension_details['created_by_local_body_code'] = $key->created_by_local_body_code;
                            }
                            if (!empty($key->created_by_dist_code)) {
                                $pension_details['created_by_dist_code'] = $key->created_by_dist_code;
                            }
                            if (!empty($key->created_by)) {
                                $pension_details['created_by'] = $key->created_by;
                            }
                            if (!empty($key->created_by_level)) {
                                $pension_details['created_by_level'] = $key->created_by_level;
                            }
                            
                            
                            
                            $pension_details['created_at'] = $key->created_at;
                            $pension_details['updated_at'] = $key->updated_at;
                            $pension_details['ip_address'] = $ip_address;
                            
                            if ($key->ben_id && $key->doc_type_id) {
                                // $update = DB::connection('pgsql')->update(DB::raw('UPDATE '.$table.' SET encript_status=1 and upload_at= current_date WHERE ben_id='.$key->ben_id.' and doc_type_id='.$key->doc_type_id.''));


                                $insert_data = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($pension_details);
                                if ($insert_data) {
                                    $i++;
                                    $input = ['encript_status' => 1, 'upload_at' => $c_time];
                                    $update = DB::connection('pgsql')->table($table)->where('ben_id', $key->ben_id)->where('doc_type_id', $key->doc_type_id)->where('is_active', TRUE)->update($input);
                                    if ($update) {
                                        $j++;
                                    }
                                }
                            }
                        }
                        else if (file_exists(storage_path('app/keep_wcd/') . '//' . $myArray1)) {
                            $mime_type = mime_content_type(storage_path('app/keep_wcd/') . '//' . $myArray1);
                            $info = pathinfo(storage_path('app/keep_wcd/') . '//' . $myArray1);
                            $extension = $info['extension'];
                            $base64 = base64_encode(file_get_contents(storage_path('app/keep_wcd/') . '//' . $myArray1));
                            $pension_details = array();
                            $pension_details['beneficiary_id'] = $key->ben_id;
                            $pension_details['scheme_id'] = $scheme_id;
                            $pension_details['document_type'] = $key->doc_type_id;
                            $pension_details['attched_document'] = $base64;
                            $pension_details['document_extension'] = $extension;
                            $pension_details['document_mime_type'] = $mime_type;

                            if (!empty($key->created_by_local_body_code)) {
                                $pension_details['created_by_local_body_code'] = $key->created_by_local_body_code;
                            }
                            if (!empty($key->created_by_dist_code)) {
                                $pension_details['created_by_dist_code'] = $key->created_by_dist_code;
                            }
                            if (!empty($key->created_by)) {
                                $pension_details['created_by'] = $key->created_by;
                            }
                            if (!empty($key->created_by_level)) {
                                $pension_details['created_by_level'] = $key->created_by_level;
                            }

                            $pension_details['created_at'] = $key->created_at;
                            $pension_details['updated_at'] = $key->updated_at;
                            $pension_details['ip_address'] = $ip_address;
                            if ($key->ben_id && $key->doc_type_id) {
                                // $update = DB::connection('pgsql')->update(DB::raw('UPDATE '.$table.' SET encript_status=1 and upload_at= current_date WHERE ben_id='.$key->ben_id.' and doc_type_id='.$key->doc_type_id.''));


                                $insert_data = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($pension_details);
                                if ($insert_data) {
                                    $i++;
                                    $input = ['encript_status' => 1, 'upload_at' => $c_time];
                                    $update = DB::connection('pgsql')->table($table)->where('ben_id', $key->ben_id)->where('doc_type_id', $key->doc_type_id)->where('is_active', TRUE)->update($input);
                                    if ($update) {
                                        $j++;
                                    }
                                }
                            }
                        }
                        else {
                            try {
                                DB::connection('pgsql')->table($schema . '.ben_docs_exception')->insert([
                                    'ben_id' => $exc_ben_id,
                                    'file_name' => $exc_file_name,
                                    'exception_msg' => 'File Not Found in both folder',
                                    'exception_type' => 2,
                                    'doc_type_id' => $exc_doc_type_id
                                ]);
                                $update = DB::connection('pgsql')->table($schema . '.ben_docs')->where('ben_id', $exc_ben_id)->where('doc_type_id', $exc_doc_type_id)->where('is_active', TRUE)->update(['exception_happen'=>2]);
                            } catch (\Exception $ee) {
                                
                            }
                        }
                    }
                    
                    if (($i == $j) && ($i > 0 && $j > 0)) {
                        DB::connection('pgsql')->commit();
                        DB::connection('pgsql_encwrite')->commit();
                        return response()->json([
                            'status' => 200,
                            'message' => 'Total - '.$i.' Images Updated Successfully',
                        ]);
                    } else {
                        DB::connection('pgsql')->rollback();
                        DB::connection('pgsql_encwrite')->rollback();
                        return redirect($back_url)->with('error', 'Error! Please try again.');
                    }
                }
                else {
                    return response()->json([
                        'status' => 200,
                        'message' => 'No Record Found',
                    ]);
                }
            }
        } catch (\Exception $e) {
            if ($e instanceof  \ErrorException) {
                // dump($exc_ben_id);
                // dump($exc_file_name);
                // dump($schema);
                // dump($e->getMessage());
                // dd(1);
                try {
                    DB::connection('pgsql')->table($schema . '.ben_docs_exception')->insert([
                        'ben_id' => $exc_ben_id,
                        'file_name' => $exc_file_name,
                        'exception_msg' => $e->getMessage(),
                        'exception_type' => 1,
                        'doc_type_id' => $exc_doc_type_id
                    ]);
                    $update = DB::connection('pgsql')->table($schema . '.ben_docs')->where('ben_id', $exc_ben_id)->where('doc_type_id', $exc_doc_type_id)->where('is_active', TRUE)->update(['exception_happen'=>1]);
                    DB::connection('pgsql')->commit();
                    DB::connection('pgsql_encwrite')->commit();
                } catch (\Exception $ee) {
                    DB::connection('pgsql')->rollback();
                    DB::connection('pgsql_encwrite')->rollback();
                    return response()->json(['status' => 500,
                'message' => 'Ben Id - ' .$exc_ben_id . ' => '.$ee->getMessage()]);
                }
                
                return response()->json(['status' => 500,
                'message' => 'Ben Id - ' .$exc_ben_id . ' => '.$e->getMessage()]);
            }
            
            dd($e);
        }
    }
    public function storeinactive(Request $request)
    {
        $scheme_id = $request->scheme_id;
        $district_code = $request->dist_code;
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        $schema = $scheme_obj->short_code;
        $table = $schema . '.ben_docs';
        $c_time =  date('Y-m-d H:i:s', time());
        $exc_ben_id = NULL;
        $exc_file_name = NULL;
        $exc_doc_type_id = NULL;
        $file_path1='keep_wcd';
        
        
        try {
            $validator = Validator::make($request->all(), [
                'limit' => 'required',
                'scheme_id' => 'required',
                'dist_code' => 'required'
            ]);
            if ($validator->fails()) {

                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {

                // ini_set('max_execution_time', 20);
                $back_url = 'scheme-select-image';
                $limit = $request->limit;
                $ip_address = request()->ip();
                $rows = DB::table($schema . '.ben_docs')->whereNull('encript_status')->where('created_by_dist_code', $district_code)->whereNotNull('created_by_dist_code')->where('is_active', FALSE)
                // ->whereNull('exception_happen')
                // ->whereNotNull('lb_application_id')
                ->whereNotNull('ben_id')
                // ->whereNotNull('doc_name')
                ->whereNull('new_doc_type_id')
                // ->whereRaw("( doc_name like '%/investigation_report/%' )")
                // ->whereRaw("(created_at::date>='2023-01-01'::date)")
                // ->whereIn('ben_id', [4552473])
                ->limit($limit)->get();
                // dump($rows);
                if (count($rows) > 0) {
                    $i = 0;
                    $j = 0;
                    $ck = 0;
                    DB::connection('pgsql')->beginTransaction();
                    DB::connection('pgsql_encwrite')->beginTransaction();
                    foreach ($rows as $key) {
                        $exc_ben_id = $key->ben_id;
                        $exc_file_name = $key->doc_name;
                        $exc_doc_type_id = $key->doc_type_id;
                        $ben_id = $key->ben_id;

                        $document_name = $key->doc_name;
                        $myArray = explode('/', $document_name);
                        $myArray1 = trim(last($myArray), " }]");
                        // dump($myArray1);
                        // dump(storage_path('app/'.$file_path1.'/') . '//' . $myArray1);
                        if (file_exists(storage_path('app/'.$file_path1.'/') . '//' . $myArray1)) {
                            $mime_type = mime_content_type(storage_path('app/'.$file_path1.'/') . '//' . $myArray1);
                            $file_time=date ("Y-m-d H:i:s", filemtime(storage_path('app/'.$file_path1.'/') . '//' . $myArray1));
                            $info = pathinfo(storage_path('app/'.$file_path1.'/') . '//' . $myArray1);
                            // dump($info);
                            $extension = $info['extension'];
                            $base64 = base64_encode(file_get_contents(storage_path('app/'.$file_path1.'/') . '//' . $myArray1));
                            $pension_details = array();
                            $pension_details['beneficiary_id'] = $key->ben_id;
                            $pension_details['scheme_id'] = $scheme_id;
                            $pension_details['document_type'] = $key->doc_type_id;
                            $pension_details['attched_document'] = $base64;
                            $pension_details['document_extension'] = $extension;
                            $pension_details['document_mime_type'] = $mime_type;
                            if (!empty($key->created_by_local_body_code)) {
                                $pension_details['created_by_local_body_code'] = $key->created_by_local_body_code;
                            }
                            if (!empty($key->created_by_dist_code)) {
                                $pension_details['created_by_dist_code'] = $key->created_by_dist_code;
                            }
                            if (!empty($key->created_by)) {
                                $pension_details['created_by'] = $key->created_by;
                            }
                            if (!empty($key->created_by_level)) {
                                $pension_details['created_by_level'] = $key->created_by_level;
                            }
                            
                            
                            
                            $pension_details['created_at'] = $key->created_at;
                            $pension_details['updated_at'] = $key->updated_at;
                            $pension_details['ip_address'] = $ip_address;
                            $pension_details['file_last_modified_time'] = $file_time;
                            $pension_details['doc_type_name'] = $key->doc_type_name;

                            $values = '';
                            if (!empty($key->ben_id)) {
                                $values .= "" . $key->ben_id . ",";
                            }
                            else {
                                $values .= "NULL,";
                            }

                            $values .= "".$scheme_id.", ". $key->doc_type_id .", '". $base64 ."', ";

                            if (!empty($key->created_by_level)) {
                                $values .= "'" . $key->created_by_level . "',";
                            }
                            else {
                                $values .= "NULL,";
                            }

                            if (!empty($key->created_at)) {
                                $values .= "'" . $key->created_at . "',";
                            }
                            else {
                                $values .= "NULL,";
                            }

                            if (!empty($key->updated_at)) {
                                $values .= "'" . $key->updated_at . "',";
                            }
                            else {
                                $values .= "NULL,";
                            }

                            if (!empty($key->created_by)) {
                                $values .= "" . $key->created_by . ",";
                            }
                            else {
                                $values .= "NULL,";
                            }
                            $doc_name = str_replace("'", "''", $key->doc_type_name);
                            $values .= "'". $ip_address ."', '" . $extension . "', '" . $mime_type . "', " . $key->created_by_dist_code . ", " . $key->created_by_local_body_code . ", '" . $file_time . "', '" . $doc_name . "'";

                            // $values .= ", -40";
                            // $values .= ",". $key->lb_application_id ."";
                            // dump($values);
                            // dump($key->lb_application_id);

                            if ($key->ben_id && $key->doc_type_id) {
                                // $update = DB::connection('pgsql')->update(DB::raw('UPDATE '.$table.' SET encript_status=1 and upload_at= current_date WHERE ben_id='.$key->ben_id.' and doc_type_id='.$key->doc_type_id.''));
                                try {
                                    // $insert_data = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->insert($pension_details);
                                    $insert_data = DB::connection('pgsql_encwrite')->statement("INSERT INTO jb_doc.ben_attach_documents_arch(
                                        beneficiary_id, scheme_id, document_type, attched_document, created_by_level, created_at, updated_at, created_by, ip_address, document_extension, document_mime_type, created_by_dist_code, created_by_local_body_code, file_last_modified_time, doc_type_name)
                                        VALUES(".$values.") ON CONFLICT DO NOTHING;
                                        ");
                                        // dump($insert_data);
                                    if ($insert_data) {
                                        $i++;
                                        $input = ['encript_status' => 1, 'upload_at' => $c_time, 'exception_happen'=> null];
                                        $update = DB::connection('pgsql')->table($table)->where('ben_id', $key->ben_id)->where('doc_type_id', $key->doc_type_id)->where('is_active', FALSE)->update($input);
                                        // dump($update);
                                        if ($update) {
                                            $j++;
                                        }
                                    }
                                } catch (\Exception $e) {
                                    dump($e);
                                }

                                
                            }
                        }
                        
                        else {
                            try {
                                DB::connection('pgsql')->table($schema . '.ben_docs_exception')->insert([
                                    'ben_id' => $exc_ben_id,
                                    'file_name' => $exc_file_name,
                                    'exception_msg' => 'File Not Found in both folder',
                                    'exception_type' => 2,
                                    'doc_type_id' => $exc_doc_type_id
                                ]);
                                $update = DB::connection('pgsql')->table($schema . '.ben_docs')->where('ben_id', $exc_ben_id)->where('doc_type_id', $exc_doc_type_id)->where('is_active', FALSE)->update(['exception_happen'=>2]);
                                $ck=1;
                            } catch (\Exception $ee) {
                                
                            }
                        }
                    }
                    // dump($i); dump($j); dump($ck); 
                    // dd('OK');
                    if (($i == $j) && ($i > 0 && $j > 0)) {
                        DB::connection('pgsql')->commit();
                        DB::connection('pgsql_encwrite')->commit();
                        return response()->json([
                            'status' => 200,
                            'i' => $i,
                            'j' => $j,
                            'message' => 'Total - '.$i.' Images Updated Successfully',
                        ]);
                    } else {
                        if ($ck==1) {
                            DB::connection('pgsql')->commit();
                            DB::connection('pgsql_encwrite')->commit();
                            return response()->json([
                                'status' => 200,
                                'message' => 'Total - '.$i.' Images Updated Successfully',
                            ]);
                        } else {
                            DB::connection('pgsql')->rollback();
                            DB::connection('pgsql_encwrite')->rollback();
                            // return redirect($back_url)->with('error', 'Error! Please try again.');
                            return response()->json([
                                'status' => 500,
                                'i' => $i,
                                'j' => $j,
                                'message' => 'Update and insert count not matched',
                            ]);
                        }
                        
                        
                    }
                }
                else {
                    return response()->json([
                        'status' => 200,
                        'message' => 'No Record Found',
                    ]);
                }
            }
        } catch (\Exception $e) {
            if ($e instanceof  \ErrorException) {
                // dump($exc_ben_id);
                // dump($exc_file_name);
                // dump($schema);
                // dump($e->getMessage());
                // dd($e);
                try {
                    DB::connection('pgsql')->table($schema . '.ben_docs_exception')->insert([
                        'ben_id' => $exc_ben_id,
                        'file_name' => $exc_file_name,
                        'exception_msg' => $e->getMessage(),
                        'exception_type' => 1,
                        'doc_type_id' => $exc_doc_type_id
                    ]);
                    $update = DB::connection('pgsql')->table($schema . '.ben_docs')->where('ben_id', $exc_ben_id)->where('doc_type_id', $exc_doc_type_id)->where('is_active', FALSE)->update(['exception_happen'=>1]);
                    DB::connection('pgsql')->commit();
                    DB::connection('pgsql_encwrite')->commit();
                } catch (\Exception $ee) {
                    DB::connection('pgsql')->rollback();
                    DB::connection('pgsql_encwrite')->rollback();
                    return response()->json(['status' => 500,
                'message' => 'Ben Id - ' .$exc_ben_id . ' => '.$ee->getMessage()]);
                }
                
                return response()->json(['status' => 500,
                'message' => 'Ben Id - ' .$exc_ben_id . ' => '.$e->getMessage()]);
            }
            
            dd($e);
        }


        /* $scheme_id = $request->scheme_id;
        $district_code = $request->dist_code;
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        $schema = $scheme_obj->short_code;
        // if($scheme_id==10){
        //     $table = $schema . '.ben_docs_move';
        // }
        // else

        $table = $schema . '.ben_docs_arc';

        $c_time =  date('Y-m-d H:i:s', time());
        $exc_ben_id = NULL;
        $exc_file_name = NULL;
        $exc_doc_type_id = NULL;
        if($scheme_id==1){
         $file_path1='keep_st';
         $file_path2='keep';
         $file_path3='keep_back';
        }
        else if($scheme_id==2){
            $file_path1='keep_manabik';
            $file_path2='keep_wcd';
            $file_path3='keep_back_wcd';
        }
        else if($scheme_id==3){
            $file_path1='keep_sc';
            $file_path2='keep';
            $file_path3='keep_back';
        }
        else if($scheme_id==5 || $scheme_id==6 || $scheme_id==7 || $scheme_id==8 || $scheme_id==9){
            $file_path1='keep';
            $file_path2='keep_back';
            $file_path3='';
        }
        else if($scheme_id==10){
            $file_path1='keep_oap';
            $file_path2='keep_wcd';
            $file_path3='keep_back_wcd';
        }
        else if($scheme_id==11){
            $file_path1='keep_wp';
            $file_path2='keep_wcd';
            $file_path3='keep_back_wcd';
        }
        else if($scheme_id==13){
            $file_path1='keep_farmer';
            $file_path2='keep';
            $file_path3='keep_back_farmer';
        }
        else if($scheme_id==17){
            $file_path1='keep_ICAD';
            $file_path2='keep';
            $file_path3='keep_back_wcd';
        }
        
        try {
            $validator = Validator::make($request->all(), [
                'limit' => 'required',
                'scheme_id' => 'required',
                'dist_code' => 'required'
            ]);
            if ($validator->fails()) {

                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {

                ini_set('max_execution_time', 20);
                $back_url = 'scheme-select-image';
                $limit = $request->limit;
                $ip_address = request()->ip();
                $rows = DB::table($table)->whereNull('encript_status')->where('created_by_dist_code', $district_code)->whereNull('exception_happen')->whereNotNull('doc_type_id')->whereNotNull('doc_name')->whereNotNull('created_by_dist_code')->limit($limit)->get();
                // dd($rows);
               //  dd($rows->toArray());
                if (count($rows) > 0) {
                    $i = 0;
                    $j = 0;
                    $ck=0;
                    DB::connection('pgsql')->beginTransaction();
                    DB::connection('pgsql_encwrite')->beginTransaction();
                    foreach ($rows as $key) {
                        $exc_ben_id = $key->ben_id;
                        $exc_file_name = $key->doc_name;
                        $exc_doc_type_id = $key->doc_type_id;
                        $ben_id = $key->ben_id;

                        $document_name = $key->doc_name;
                        $myArray = explode('/', $document_name);
                        $myArray1 = trim(last($myArray), " }]");
                        if (!empty($file_path3) && file_exists(storage_path('app/'.$file_path3.'/') . '//' . $myArray1)) {
                            //dump('1 found');
                            $mime_type = mime_content_type(storage_path('app/'.$file_path3.'/') . '//' . $myArray1);
                            $info = pathinfo(storage_path('app/'.$file_path3.'/') . '//' . $myArray1);
                            $extension = $info['extension'];
                            $base64 = base64_encode(file_get_contents(storage_path('app/'.$file_path3.'/') . '//' . $myArray1));
                            $file_time=date ("Y-m-d H:i:s", filemtime(storage_path('app/'.$file_path3.'/') . '//' . $myArray1));
                            $pension_details = array();
                            $pension_details['beneficiary_id'] = $key->ben_id;
                            $pension_details['scheme_id'] = $scheme_id;
                            $pension_details['document_type'] = $key->doc_type_id;
                            $pension_details['attched_document'] = $base64;
                            $pension_details['document_extension'] = $extension;
                            $pension_details['document_mime_type'] = $mime_type;
                            if (!empty($key->created_by_local_body_code)) {
                                $pension_details['created_by_local_body_code'] = $key->created_by_local_body_code;
                            }
                            if (!empty($key->created_by_dist_code)) {
                                $pension_details['created_by_dist_code'] = $key->created_by_dist_code;
                            }
                            if (!empty($key->created_by)) {
                                $pension_details['created_by'] = $key->created_by;
                            }
                            if (!empty($key->created_by_level)) {
                                $pension_details['created_by_level'] = $key->created_by_level;
                            }
                            
                            
                            
                            $pension_details['created_at'] = $key->created_at;
                            $pension_details['updated_at'] = $key->updated_at;
                            $pension_details['ip_address'] = $ip_address;
                            $pension_details['doc_type_name'] = $key->doc_type_name;
                            $pension_details['file_last_modified_time'] = $file_time;
                            if ($key->ben_id && $key->doc_type_id) {
                                // $update = DB::connection('pgsql')->update(DB::raw('UPDATE '.$table.' SET encript_status=1 and upload_at= current_date WHERE ben_id='.$key->ben_id.' and doc_type_id='.$key->doc_type_id.''));


                                $insert_data = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_arch')->insert($pension_details);
                                if ($insert_data) {
                                    $i++;
                                    $input = ['encript_status' => 1, 'upload_at' => $c_time];
                                    $update = DB::connection('pgsql')->table($table)->where('ben_id', $key->ben_id)->where('doc_type_id', $key->doc_type_id)->update($input);
                                    if ($update) {
                                        $j++;
                                    }
                                }
                            }
                        }
                        else if (!empty($file_path2) && file_exists(storage_path('app/'.$file_path2.'/') . '//' . $myArray1)) {
                           // dump('2 found');
                            $mime_type = mime_content_type(storage_path('app/'.$file_path2.'/') . '//' . $myArray1);
                            $info = pathinfo(storage_path('app/'.$file_path2.'/') . '//' . $myArray1);
                            $extension = $info['extension'];
                            $base64 = base64_encode(file_get_contents(storage_path('app/'.$file_path2.'/') . '//' . $myArray1));
                            $file_time=date ("Y-m-d H:i:s", filemtime(storage_path('app/'.$file_path2.'/') . '//' . $myArray1));
                            $pension_details = array();
                            $pension_details['beneficiary_id'] = $key->ben_id;
                            $pension_details['scheme_id'] = $scheme_id;
                            $pension_details['document_type'] = $key->doc_type_id;
                            $pension_details['attched_document'] = $base64;
                            $pension_details['document_extension'] = $extension;
                            $pension_details['document_mime_type'] = $mime_type;

                            if (!empty($key->created_by_local_body_code)) {
                                $pension_details['created_by_local_body_code'] = $key->created_by_local_body_code;
                            }
                            if (!empty($key->created_by_dist_code)) {
                                $pension_details['created_by_dist_code'] = $key->created_by_dist_code;
                            }
                            if (!empty($key->created_by)) {
                                $pension_details['created_by'] = $key->created_by;
                            }
                            if (!empty($key->created_by_level)) {
                                $pension_details['created_by_level'] = $key->created_by_level;
                            }

                            $pension_details['created_at'] = $key->created_at;
                            $pension_details['updated_at'] = $key->updated_at;
                            $pension_details['ip_address'] = $ip_address;
                            $pension_details['doc_type_name'] = $key->doc_type_name;
                            $pension_details['file_last_modified_time'] = $file_time;
                            if ($key->ben_id && $key->doc_type_id) {
                                // $update = DB::connection('pgsql')->update(DB::raw('UPDATE '.$table.' SET encript_status=1 and upload_at= current_date WHERE ben_id='.$key->ben_id.' and doc_type_id='.$key->doc_type_id.''));


                                $insert_data = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_arch')->insert($pension_details);
                                if ($insert_data) {
                                    $i++;
                                    $input = ['encript_status' => 1, 'upload_at' => $c_time];
                                    $update = DB::connection('pgsql')->table($table)->where('ben_id', $key->ben_id)->where('doc_type_id', $key->doc_type_id)->update($input);
                                    if ($update) {
                                        $j++;
                                    }
                                }
                            }
                        } else if (!empty($file_path1) && file_exists(storage_path('app/'.$file_path1.'/') . '//' . $myArray1)) {
                            //dump('3 found');
                            $mime_type = mime_content_type(storage_path('app/'.$file_path1.'/') . '//' . $myArray1);
                            $info = pathinfo(storage_path('app/'.$file_path1.'/') . '//' . $myArray1);
                            $extension = $info['extension'];
                            $base64 = base64_encode(file_get_contents(storage_path('app/'.$file_path1.'/') . '//' . $myArray1));
                            $file_time=date ("Y-m-d H:i:s", filemtime(storage_path('app/'.$file_path1.'/') . '//' . $myArray1));
                            $pension_details = array();
                            $pension_details['beneficiary_id'] = $key->ben_id;
                            $pension_details['scheme_id'] = $scheme_id;
                            $pension_details['document_type'] = $key->doc_type_id;
                            $pension_details['attched_document'] = $base64;
                            $pension_details['document_extension'] = $extension;
                            $pension_details['document_mime_type'] = $mime_type;

                            if (!empty($key->created_by_local_body_code)) {
                                $pension_details['created_by_local_body_code'] = $key->created_by_local_body_code;
                            }
                            if (!empty($key->created_by_dist_code)) {
                                $pension_details['created_by_dist_code'] = $key->created_by_dist_code;
                            }
                            if (!empty($key->created_by)) {
                                $pension_details['created_by'] = $key->created_by;
                            }
                            if (!empty($key->created_by_level)) {
                                $pension_details['created_by_level'] = $key->created_by_level;
                            }

                            $pension_details['created_at'] = $key->created_at;
                            $pension_details['updated_at'] = $key->updated_at;
                            $pension_details['ip_address'] = $ip_address;
                            $pension_details['doc_type_name'] = $key->doc_type_name;
                            $pension_details['file_last_modified_time'] = $file_time;
                            if ($key->ben_id && $key->doc_type_id) {
                                // $update = DB::connection('pgsql')->update(DB::raw('UPDATE '.$table.' SET encript_status=1 and upload_at= current_date WHERE ben_id='.$key->ben_id.' and doc_type_id='.$key->doc_type_id.''));


                                $insert_data = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_arch')->insert($pension_details);
                                if ($insert_data) {
                                    $i++;
                                    $input = ['encript_status' => 1, 'upload_at' => $c_time];
                                    $update = DB::connection('pgsql')->table($table)->where('ben_id', $key->ben_id)->where('doc_type_id', $key->doc_type_id)->update($input);
                                    if ($update) {
                                        $j++;
                                    }
                                }
                            }
                        }
                        else {
                            try {
                                DB::connection('pgsql')->table($schema . '.ben_docs_exception')->insert([
                                    'ben_id' => $exc_ben_id,
                                    'file_name' => $exc_file_name,
                                    'exception_msg' => 'File Not Found in both folder',
                                    'exception_type' => 2,
                                    'doc_type_id' => $exc_doc_type_id
                                ]);
                                $update = DB::connection('pgsql')->table($table)->where('ben_id', $exc_ben_id)->where('doc_type_id', $exc_doc_type_id)->update(['exception_happen'=>2]);
                                $ck=1;
                            } catch (\Exception $ee) {
                                
                            }
                        }
                    }
                     //dump($i);dump($j);
                    if (($i == $j) && ($i > 0 && $j > 0)) {
                        DB::connection('pgsql')->commit();
                        DB::connection('pgsql_encwrite')->commit();
                        return response()->json([
                            'status' => 200,
                            'message' => 'Total - '.$i.' Images Updated Successfully',
                        ]);
                    } else {
                        if ($ck==1) {
                            DB::connection('pgsql')->commit();
                            DB::connection('pgsql_encwrite')->commit();
                            return response()->json([
                                'status' => 200,
                                'message' => 'Total - '.$i.' Images Updated Successfully',
                            ]);
                        } else {
                            DB::connection('pgsql')->rollback();
                            DB::connection('pgsql_encwrite')->rollback();
                            // return redirect($back_url)->with('error', 'Error! Please try again.');
                            return response()->json([
                                'status' => 500,
                                'i' => $i,
                                'j' => $j,
                                'message' => 'Update and insert count not matched',
                            ]);
                        }
                        // DB::connection('pgsql')->rollback();
                        // DB::connection('pgsql_encwrite')->rollback();
                        // return response()->json([
                        //     'status' => 200,
                        //     'message' => 'Roolback error',
                        // ]);
                    }
                }
                else {
                    return response()->json([
                        'status' => 200,
                        'message' => 'No Record Found',
                    ]);
                }
            }
        } catch (\Exception $e) {
            if ($e instanceof  \ErrorException) {
                // dump($exc_ben_id);
                // dump($exc_file_name);
                // dump($schema);
                // dump($e->getMessage());
                // dd(1);
                try {
                    DB::connection('pgsql')->table($schema . '.ben_docs_exception')->insert([
                        'ben_id' => $exc_ben_id,
                        'file_name' => $exc_file_name,
                        'exception_msg' => $e->getMessage(),
                        'exception_type' => 1,
                        'doc_type_id' => $exc_doc_type_id
                    ]);
                    $update = DB::connection('pgsql')->table($table)->where('ben_id', $exc_ben_id)->where('doc_type_id', $exc_doc_type_id)->update(['exception_happen'=>1]);
                    DB::connection('pgsql')->commit();
                    DB::connection('pgsql_encwrite')->commit();
                } catch (\Exception $ee) {
                    DB::connection('pgsql')->rollback();
                    DB::connection('pgsql_encwrite')->rollback();
                    return response()->json(['status' => 500,
                'message' => 'Ben Id - ' .$exc_ben_id . ' => '.$ee->getMessage()]);
                }
                
                return response()->json(['status' => 500,
                'message' => 'Ben Id - ' .$exc_ben_id . ' => '.$e->getMessage()]);
            }
            
            dd($e);
        } */
    }

    /*
        For Stop Payment Files
    */
    public function stopPaymentFilesMove(Request $request) {
        // echo 1;die;
        $scheme_id = $request->scheme_id;
        $district_code = $request->dist_code;
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        $schema = $scheme_obj->short_code;
        if($scheme_id==10){
            $table = $schema . '.ben_docs';
        }
        else {
            $table = $schema . '.ben_docs';
        }
        $c_time =  date('Y-m-d H:i:s', time());
        $exc_ben_id = NULL;
        $exc_file_name = NULL;
        $exc_doc_type_id = NULL;
        $file_path1 = 'stop_payment';

        $filename = 'stop_payment_files.txt';
        $fetch_file = storage_path('app/stop_payment/'.$filename);
        
    
        try {
            $validator = Validator::make($request->all(), [
                'limit' => 'required',
                'scheme_id' => 'required',
                'dist_code' => 'required'
            ]);
            if ($validator->fails()) {

                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {

                ini_set('max_execution_time', 20);
                $back_url = 'scheme-select-image';
                $limit = $request->limit;
                $ip_address = request()->ip();

                $rows = DB::table($table)->whereNull('encript_status')
                // ->where('created_by_dist_code', $district_code)
                // ->whereNotNull('exception_happen')
                // ->where('doc_type_id', 101)
                // ->whereNotNull('doc_name')
                // ->whereNotNull('created_by_dist_code')
                // ->where('doc_type_id', '>=', 100)
                ->whereRaw("(doc_name like '%/images_stopped/%' )")
                ->where('ben_id', 50527)
                // ->where('scheme_id', 3)
                ->limit($limit)->get();

                // dd($rows);
                
                // $rows = DB::table($table)->whereNull('encript_status')->where('created_by_dist_code', $district_code)->whereNotNull('exception_happen')->whereNotNull('doc_type_id')->whereNotNull('doc_name')->whereNotNull('created_by_dist_code')->where('doc_type_id', '>=', 100)->whereraw("(is_active=FALSE or is_active IS NULL)")->limit($limit)->get();

                // dd($rows->toArray());
                if (count($rows) > 0) {
                    $i = 0;
                    $j = 0;
                    $ck = 0;
                    DB::connection('pgsql')->beginTransaction();
                    DB::connection('pgsql_encwrite')->beginTransaction();
                    foreach ($rows as $key) {
                        $exc_ben_id = $key->ben_id;
                        $exc_file_name = $key->doc_name;
                        $exc_doc_type_id = $key->doc_type_id;
                        $ben_id = $key->ben_id;

                        $document_name = $key->doc_name;
                        $myArray = explode('/', $document_name);
                        $myArray1 = trim(last($myArray), " }]");

                        // $searchfor = '1674029888.jpeg';
                        $contents = file_get_contents($fetch_file);
                        $pattern = preg_quote($myArray1, '/');
                        $pattern = "/^.*$pattern.*\$/m";

                        // search, and store all matching occurences in $matches
                        if (preg_match_all($pattern, $contents, $matches))
                        {
                            $myArray2 = substr(implode("\n", $matches[0]), 2);
                            // echo "Found matches:\n";
                            // echo $myArray2;
                            if (file_exists(storage_path('app/'.$file_path1.'/') . '//' . $myArray2)) {
                                dump('1 found');
                                $mime_type = mime_content_type(storage_path('app/'.$file_path1.'/') . '//' . $myArray2);
                                $info = pathinfo(storage_path('app/'.$file_path1.'/') . '//' . $myArray2);
                                $extension = $info['extension'];
                                $base64 = base64_encode(file_get_contents(storage_path('app/'.$file_path1.'/') . '//' . $myArray2));
                                $file_time=date ("Y-m-d H:i:s", filemtime(storage_path('app/'.$file_path1.'/') . '//' . $myArray2));
                                $pension_details = array();
                                $pension_details['beneficiary_id'] = $key->ben_id;
                                $pension_details['scheme_id'] = $scheme_id;
                                $pension_details['document_type'] = $key->doc_type_id;
                                $pension_details['attched_document'] = $base64;
                                $pension_details['document_extension'] = $extension;
                                $pension_details['document_mime_type'] = $mime_type;
                                if (!empty($key->created_by_local_body_code)) {
                                    $pension_details['created_by_local_body_code'] = $key->created_by_local_body_code;
                                }
                                if (!empty($key->created_by_dist_code)) {
                                    $pension_details['created_by_dist_code'] = $key->created_by_dist_code;
                                }
                                if (!empty($key->created_by)) {
                                    $pension_details['created_by'] = $key->created_by;
                                }
                                if (!empty($key->created_by_level)) {
                                    $pension_details['created_by_level'] = $key->created_by_level;
                                }
                                
                                
                                
                                $pension_details['created_at'] = $key->created_at;
                                $pension_details['updated_at'] = $key->updated_at;
                                $pension_details['ip_address'] = $ip_address;
                                $pension_details['is_stop_payment_file'] = TRUE;
                                $pension_details['file_last_modified_time'] = $file_time;
                                $pension_details['doc_type_name'] = $key->doc_type_name;
                                dd($pension_details);
                                if ($key->ben_id && $key->doc_type_id) {
                                    // $update = DB::connection('pgsql')->update(DB::raw('UPDATE '.$table.' SET encript_status=1 and upload_at= current_date WHERE ben_id='.$key->ben_id.' and doc_type_id='.$key->doc_type_id.''));
    
    
                                    $insert_data = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_arch')->insert($pension_details);
                                    if ($insert_data) {
                                        $i++;
                                        $input = ['encript_status' => 1, 'upload_at' => $c_time, 'exception_happen' => null];
                                        $update = DB::connection('pgsql')->table($table)->where('ben_id', $key->ben_id)->where('doc_type_id', $key->doc_type_id)->update($input);
                                        if ($update) {
                                            $j++;
                                        }
                                    }
                                }
                            }
                            
                        }
                        /*else
                        {
                        echo "No matches found";
                        }
        
                        if (!empty($file_path1) && file_exists(storage_path('app/'.$file_path1.'/') . '//' . $myArray2)) {
                            //dump('1 found');
                            $mime_type = mime_content_type(storage_path('app/'.$file_path1.'/') . '//' . $myArray2);
                            $info = pathinfo(storage_path('app/'.$file_path1.'/') . '//' . $myArray2);
                            $extension = $info['extension'];
                            $base64 = base64_encode(file_get_contents(storage_path('app/'.$file_path1.'/') . '//' . $myArray2));
                            $pension_details = array();
                            $pension_details['beneficiary_id'] = $key->ben_id;
                            $pension_details['scheme_id'] = $scheme_id;
                            $pension_details['document_type'] = $key->doc_type_id;
                            $pension_details['attched_document'] = $base64;
                            $pension_details['document_extension'] = $extension;
                            $pension_details['document_mime_type'] = $mime_type;
                            if (!empty($key->created_by_local_body_code)) {
                                $pension_details['created_by_local_body_code'] = $key->created_by_local_body_code;
                            }
                            if (!empty($key->created_by_dist_code)) {
                                $pension_details['created_by_dist_code'] = $key->created_by_dist_code;
                            }
                            if (!empty($key->created_by)) {
                                $pension_details['created_by'] = $key->created_by;
                            }
                            if (!empty($key->created_by_level)) {
                                $pension_details['created_by_level'] = $key->created_by_level;
                            }
                            
                            
                            
                            $pension_details['created_at'] = $key->created_at;
                            $pension_details['updated_at'] = $key->updated_at;
                            $pension_details['ip_address'] = $ip_address;
                            $pension_details['is_stop_payment_file'] = TRUE;
                            
                            if ($key->ben_id && $key->doc_type_id) {
                                // $update = DB::connection('pgsql')->update(DB::raw('UPDATE '.$table.' SET encript_status=1 and upload_at= current_date WHERE ben_id='.$key->ben_id.' and doc_type_id='.$key->doc_type_id.''));


                                $insert_data = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_arch')->insert($pension_details);
                                if ($insert_data) {
                                    $i++;
                                    $input = ['encript_status' => 1, 'upload_at' => $c_time, 'exception_happen' => null];
                                    $update = DB::connection('pgsql')->table($table)->where('ben_id', $key->ben_id)->where('doc_type_id', $key->doc_type_id)->update($input);
                                    if ($update) {
                                        $j++;
                                    }
                                }
                            }
                        }*/
                        else {
                            try {
                                DB::connection('pgsql')->table($schema . '.ben_docs_exception')->insert([
                                    'ben_id' => $exc_ben_id,
                                    'file_name' => $exc_file_name,
                                    'exception_msg' => 'File Not Found for stop payment',
                                    'exception_type' => 3,
                                    'doc_type_id' => $exc_doc_type_id
                                ]);
                                $update = DB::connection('pgsql')->table($table)->where('ben_id', $exc_ben_id)->where('doc_type_id', $exc_doc_type_id)->update(['exception_happen'=>3]);
                                $ck = 1;
                            } catch (\Exception $ee) {
                                
                            }
                        }
                    }
                     //dump($i);dump($j);
                    if (($i == $j) && ($i > 0 && $j > 0)) {
                        DB::connection('pgsql')->commit();
                        DB::connection('pgsql_encwrite')->commit();
                        return response()->json([
                            'status' => 200,
                            'message' => 'Total - '.$i.' Images Updated Successfully',
                        ]);
                    } else {
                        if ($ck == 1) {
                            DB::connection('pgsql')->commit();
                            DB::connection('pgsql_encwrite')->commit();
                            return response()->json([
                                'status' => 200,
                                'message' => 'Total - '.$i.' Images Updated Successfully',
                            ]);
                        } else {
                            DB::connection('pgsql')->rollback();
                            DB::connection('pgsql_encwrite')->rollback();
                            return response()->json([
                                'status' => 200,
                                'message' => 'Roolback error',
                            ]);
                        }
                        
                        
                    }
                }
                else {
                    return response()->json([
                        'status' => 200,
                        'message' => 'No Record Found',
                    ]);
                }
            }
        } catch (\Exception $e) {
            if ($e instanceof  \ErrorException) {
                // dump($exc_ben_id);
                // dump($exc_file_name);
                // dump($schema);
                // dump($e->getMessage());
                // dd(1);
                try {
                    DB::connection('pgsql')->table($schema . '.ben_docs_exception')->insert([
                        'ben_id' => $exc_ben_id,
                        'file_name' => $exc_file_name,
                        'exception_msg' => $e->getMessage(),
                        'exception_type' => 3,
                        'doc_type_id' => $exc_doc_type_id
                    ]);
                    $update = DB::connection('pgsql')->table($table)->where('ben_id', $exc_ben_id)->where('doc_type_id', $exc_doc_type_id)->update(['exception_happen'=>3]);
                    DB::connection('pgsql')->commit();
                    DB::connection('pgsql_encwrite')->commit();
                } catch (\Exception $ee) {
                    DB::connection('pgsql')->rollback();
                    DB::connection('pgsql_encwrite')->rollback();
                    return response()->json(['status' => 500,
                'message' => 'Ben Id - ' .$exc_ben_id . ' => '.$ee->getMessage()]);
                }
                
                return response()->json(['status' => 500,
                'message' => 'Ben Id - ' .$exc_ben_id . ' => '.$e->getMessage()]);
            }
            
            dd($e);
        }
    }
    
    public function viewImage(Request $request)
    {
        try{
        $beneficiary_id = $request->beneficiary_id;
        $dist_code = $request->dist_code;
        if(empty($beneficiary_id)){
            $beneficiary_id=7237688;
            $dist_code = 304;
        }
        $doc_master_list=DB::table('public.m_attached_doc')->get();
        $doc_list = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents')->where('created_by_dist_code',$dist_code)->where('beneficiary_id',$beneficiary_id)->get();
        foreach($doc_list as $doc_item){
            $doc_master_item=$doc_master_list->where('id',$doc_item->document_type)->first();
            $mime_type = $doc_item->document_mime_type;
            $image_extension = $doc_item->document_extension;
            if ($image_extension != 'png' && $image_extension != 'jpg' && $image_extension != 'jpeg') {
                if ($mime_type == 'image/png') {
                    $image_extension = 'png';
                } else if ($mime_type == 'image/jpeg') {
                    $image_extension = 'jpg';
                }
            }
            $resultimg = str_replace("data:image/" . $image_extension . ";base64,", "", $doc_item->attched_document);
            $row_image = "data:image/".$image_extension.";base64,".$doc_item->attched_document;
            $image= base64_decode($row_image);
            ?><?php echo $doc_master_item->doc_name;?>
            <img class="example-image" src="<?php echo $row_image;?>" alt="image-1" width="250" height="380" />
            <?php
        }
       }catch (\Exception $e) {
        dd($e);
       }
    }
}
