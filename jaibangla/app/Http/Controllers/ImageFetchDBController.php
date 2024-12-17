<?php

namespace App\Http\Controllers;

use Validator;
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
use Illuminate\Support\Facades\Auth;

class ImageFetchDBController extends Controller
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
        $scheme_id = $request->scheme_id;

        return view(
            'Image-fetch-DB/imageStore',
            [
                'scheme_id' => $scheme_id
            ]
        );
    }
    public function store(Request $request)
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
                $rows = DB::table($schema . '.ben_docs')->whereNull('encript_status')
                ->where('created_by_dist_code', $district_code)->whereNotNull('created_by_dist_code')
                ->where('type', 5)
                ->limit($limit)->get();
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
                        if (file_exists(storage_path('app/keep_sc/') . '//' . $myArray1)) {
                            $file_time=date ("Y-m-d H:i:s", filemtime(storage_path('app/keep_sc/') . '//' . $myArray1));
                            $mime_type = mime_content_type(storage_path('app/keep_sc/') . '//' . $myArray1);
                            $info = pathinfo(storage_path('app/keep_sc/') . '//' . $myArray1);
                            $extension = $info['extension'];
                            $base64 = base64_encode(file_get_contents(storage_path('app/keep_sc/') . '//' . $myArray1));
                            $pension_details = array();
                            $pension_details['beneficiary_id'] = $key->ben_id;
                            $pension_details['scheme_id'] = $scheme_id;
                            $pension_details['document_type'] = $key->doc_type_id;
                            $pension_details['attched_document'] = $base64;
                            $pension_details['document_extension'] = $extension;
                            $pension_details['document_mime_type'] = $mime_type;
                            $pension_details['doc_type_name'] = $key->doc_type_name;
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
                            $pension_details['external_mode'] = -12;
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
                                $update = DB::connection('pgsql')->table($schema . '.ben_docs')->where('ben_id', $exc_ben_id)->where('doc_type_id', $exc_doc_type_id)->update(['exception_happen'=>2]);
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
                        //return redirect($back_url)->with('error', 'Error! Please try again.');
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

    public function totalImage(Request $request)
    {
        $scheme_id = $request->scheme_id;
        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();

        $schema = $scheme_obj->short_code;


        $statusCode = 200;
        $response = [];
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in ajax call.');
            return response()->json($response, $statusCode);
        }
        try {
            $query = "";
            $query1 = "";
            $query2 = "";
            $query = "select count(1) as total_count from " . $schema . ".ben_docs where doc_name is not NULL and created_by_dist_code=" . $this->district_code;

            // $query1="select count(aadhar_no) as remaining_count from pension.ben_docs where encoded_aadhar is  NULL";
            $query2 = "select count(1) as updated_image from jb_doc.ben_attach_documents where scheme_id=" . $scheme_id . " and created_by_dist_code=" . $this->district_code;
            // $getTotalImage=DB::select(DB::raw($query));
            $getTotalImage = DB::connection('pgsql')->select($query);

            // $getRemainAadhar=DB::select(DB::raw($query1));
            // $getUpdatedImage=DB::select(DB::raw($query2));
            $getUpdatedImage = DB::connection('pgsql_encwrite')->select($query2);
            //dd($getTotalAadhar);
            $response = array(
                'status' => 1, 'totalImage' =>   $getTotalImage[0]->total_count, 'updatedImage' => $getUpdatedImage[0]->updated_image,
                'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
            );
            // 'remainingAadhar' => $getRemainAadhar[0]->remaining_count,  
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
