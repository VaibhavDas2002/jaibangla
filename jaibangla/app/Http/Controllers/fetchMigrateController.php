<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Configduty;
use App\District;
use App\UrbanBody;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use App\Scheme;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\DataSourceCommon;
use App\DocumentType;
use App\getModelFunc;
use App\DsPhase;
use Illuminate\Support\Facades\Storage;

class fetchMigrateController extends Controller
{
  public function __construct()
  {
    // $this->middleware('auth');
    set_time_limit(50000);
  }
  public function index(Request $request)
  {
    try {
      $dist_code = $request->get('dist_code');
      $limit = $request->get('limit');
      $app_id = DB::table('migration_to_jb.beneficiary_lb')->selectRaw('lb_application_id as application_id, trim(caste) as caste, created_by_dist_code, is_faulty')
      // ->where('caste', 'ST')->whereNull('doc_imported')
      ->where('created_by_dist_code', $dist_code)
      ->whereNull('doc_imported')
      //->where('lb_application_id', 100728927)
      ->limit($limit)->get();

      // dd($app_id);


      $doc_type_list = DocumentType::select('id', 'doc_name')->get();
      $total_count = 0;
      $c_time = date('Y-m-d H:i:s', time());
      $filearr = array();
      if (count($app_id) > 0) {
        foreach ($app_id as $app_item) {
          //  dd($app_item);
          $application_id = $app_item->application_id;
          $is_faulty = $app_item->is_faulty;
          $created_by_dist_code = $app_item->created_by_dist_code;
          $caste = $app_item->caste;

          
          array_push($filearr, $application_id);

          //*//********************************* */
          
          //dd($document_list->toArray());
          //return response()->json(["is_success" => true, "doc_list" => $document_list->toArray()], 200);
          $return_arr = collect([]);
          if ($is_faulty == FALSE) {
            $list1 = DB::connection('pgsql_lb_encwrite')->table('ben_profile_image')->select('profile_image as content', 'image_extension as extension', 'image_mime_type as mime_type', 'image_type as type')->where('application_id', $application_id)->get();
            // return response()->json(["is_success" => true, "doc_list" => $list1->toArray()], 200);
            if (count($list1) > 0) {
              $merge1 = $return_arr->merge($list1);
            } else {
              $merge1 = collect([]);
            }
            $list2 = DB::connection('pgsql_lb_encwrite')->table('ben_attach_documents')->select('document_type as type', 'attched_document as content', 'document_extension as extension', 'document_mime_type as mime_type')->where('application_id', $application_id)->get();
            if (count($list2) > 0) {
              $merge2 = $merge1->merge($list2);
            } else {
              $merge2 = collect([]);
            }
            // dd($merge2);
            if (count($merge2) > 0) {
              $i = 0;
              $listarr = array();
              foreach ($merge2 as $itm) {
                $listarr[$i]['content'] = $itm->content;
                $listarr[$i]['extension'] = $itm->extension;
                $listarr[$i]['mime_type'] = $itm->mime_type;
                $listarr[$i]['type'] = $itm->type;
                $type_des_arr = $doc_type_list->where('id', $itm->type)->first();
                if (!empty($type_des_arr)) {
                  $listarr[$i]['type_des'] = $type_des_arr['doc_name'];
                }
                $i++;
              }
              // dd($listarr);
              // return response()->json(["is_success" => 'true', "doc_list" => $listarr], 200);

              //return response()->json(["is_success" => true, "doc_list" => $listarr], 200);
            } else {
              $listarr = array();
              // return response()->json(["is_success" => false, "doc_list" =>$listarr], 400);
              //return response()->json(["is_success" => true, "doc_list" => $listarr], 200);
            }
          } else if ($is_faulty == TRUE) {
            $list1 = DB::connection('pgsql_lb_encwrite')->table('faulty_ben_profile_image')->select('profile_image as content', 'image_extension as extension', 'image_mime_type as mime_type', 'image_type as type')->where('application_id', $application_id)->get();
            // return response()->json(["is_success" => true, "doc_list" => $list1->toArray()], 200);
            if (count($list1) > 0) {
              $merge1 = $return_arr->merge($list1);
            } else {
              $merge1 = collect([]);
            }
            $list2 = DB::connection('pgsql_lb_encwrite')->table('faulty_ben_attach_documents')->select('document_type as type', 'attched_document as content', 'document_extension as extension', 'document_mime_type as mime_type')->where('application_id', $application_id)->get();
            if (count($list2) > 0) {
              $merge2 = $merge1->merge($list2);
            } else {
              $merge2 = collect([]);
            }
            if (count($merge2) > 0) {
              $i = 0;
              $listarr = array();
              foreach ($merge2 as $itm) {
                $listarr[$i]['content'] = $itm->content;
                $listarr[$i]['extension'] = $itm->extension;
                $listarr[$i]['mime_type'] = $itm->mime_type;
                $listarr[$i]['type'] = $itm->type;
                $type_des_arr = $doc_type_list->where('id', $itm->type)->first();
                $listarr[$i]['type_des'] = $type_des_arr['doc_name'];
                $i++;
              }
              // return response()->json(["is_success" => true, "doc_list" => $listarr], 200);
            } else {
              $listarr = array();
              // return response()->json(["is_success" => true, "doc_list" => $listarr], 400);
            }
            // dd($listarr);
          }

          // dd($listarr);

          $base_url = 'https://jaibangla.wb.gov.in';
          DB::beginTransaction(); 
          if (count($listarr) > 0) {
            // $post_response_lb = json_decode($post_response);

            $docs = $listarr;
            // dd($docs);
            $file = 0;
            $i = 0;
            $insert_done = 0;
            $update_app_arr = array();
            
            if (count($docs) > 0) {
              foreach ($docs as $doc_item) {
                //     if (!empty($scheme_obj->file_path)) {
                //       $file_path = $scheme_obj->file_path;
                //       $file_arc_path = $scheme_obj->file_arc_path;
                //     } else {
                //       $file_path = "";
                //       $file_arc_path = "";
                //     }
                //dd($file_path);
                // $file_path = 'doc_list_' . $created_by_dist_code;
                $mime_type = $doc_item['mime_type'];
                $type = $doc_item['type'];

                if ($type == 116) {
                  $type = 199;
                }
                if ($type == 117) {
                  $type = 116;
                }

                if ($caste == 'OTHERS') {
                  $schema_name_docs = 'oap_wcd';
                  $scheme_id = 10;
                  // $file_path = 'keep_oap_' . $created_by_dist_code;
                  $file_path = 'keep_oap';
                } else if ($caste == 'ST') {
                  $schema_name_docs = 'johar';
                  $scheme_id = 1;
                  // $file_path = 'keep_st_' . $created_by_dist_code;
                  $file_path = 'keep_st';
                } else if ($caste == 'SC') {
                  $schema_name_docs = 'bandhu';
                  $scheme_id = 3;
                  // $file_path = 'keep_sc_' . $created_by_dist_code;
                  $file_path = 'keep_sc';
                }
                $unid = uniqid();
                $fileBin=NULL;
                $file_extension = $doc_item['extension'];
                $mime_type = $doc_item['mime_type'];
                if ($mime_type == 'image/jpeg') {
                  $file_extension = 'jpg';
                  $data = 'data:image/' . $file_extension . ';base64,' . $doc_item['content'];
                  $fileBin = file_get_contents($data);
                } else if ($mime_type == 'image/png') {
                  $file_extension = 'png';
                  $data = 'data:image/' . $file_extension . ';base64,' . $doc_item['content'];
                  $fileBin = file_get_contents($data);
                } else if ($mime_type == 'image/gif') {
                  $file_extension = 'gif';
                  $data = 'data:image/' . $file_extension . ';base64,' . $doc_item['content'];
                  $fileBin = file_get_contents($data);
                } else if ($mime_type == 'image/bmp') {
                  $file_extension = 'bmp';
                  $data = 'data:image/' . $file_extension . ';base64,' . $doc_item['content'];
                  $fileBin = file_get_contents($data);
                } else if ($mime_type == 'application/pdf') {
                  $fileBin = base64_decode($doc_item['content']);
                }
                $file_name = 'doc_' . $type . '_' . $application_id . '_' . $unid;
                $file_namedb = 'doc_' . $type . '_' . $application_id . '_' . $unid . '.' . $file_extension;
                $fileName = $file_path . '\\' . $file_name . '.' . $file_extension;
                $doc_type_arr = $doc_type_list->where('id', $type)->first();

                // if ($type == 116) {
                //   $type = 199;
                // }
                // if ($type == 117) {
                //   $type = 116;
                // }

              
                
                
                if (!empty($doc_type_arr) && !empty($fileBin)) {
                  if (Storage::disk('local')->put($fileName, $fileBin)) {
                    // array_push($filearr, $application_id);
                    // $insert_doc_type_arr[$i]['scheme_id'] = $scheme_id;
                    // $insert_doc_type_arr[$i]['is_active'] = FALSE;
                    // $insert_doc_type_arr[$i]['lb_application_id'] = $application_id;
                    // $insert_doc_type_arr[$i]['doc_type_id'] = $type;
                    // $insert_doc_type_arr[$i]['doc_type_name'] = $doc_type_arr->doc_name;
                    if ($scheme_id == 10) {
                      // $insert_doc_type_arr[$i]['doc_name'] = $base_url . '/images_oap/' . $file_namedb;
                      $doc_name_new = $base_url . '/images_oap/' . $file_namedb;
                    } else if ($scheme_id == 1) {
                      // $insert_doc_type_arr[$i]['doc_name'] = $base_url . '/images_st/' . $file_namedb;
                      $doc_name_new = $base_url . '/images_st/' . $file_namedb;
                    } else if ($scheme_id == 3) {
                      // $insert_doc_type_arr[$i]['doc_name'] = $base_url . '/images_sc/' . $file_namedb;
                      $doc_name_new = $base_url . '/images_sc/' . $file_namedb;
                    }
                    // $insert_doc_type_arr[$i]['created_at'] = $c_time;
                    // array_push($update_app_arr, $application_id);
                    $file++;
                    $i++;
                    // $doc_inserted = DB::connection('pgsql_lb_encwrite')->table($schema_name_docs . '.ben_docs')->insert($insert_doc_type_arr);
                    if (Storage::disk('local')->exists($fileName)) {
                      $insertQuery = "INSERT INTO ". $schema_name_docs . ".ben_docs(scheme_id, is_active,lb_application_id, doc_type_id, doc_type_name, doc_name, created_at) VALUES(".$scheme_id.", FALSE, ".$application_id.",".$type.",'".$doc_type_arr->doc_name."','".$doc_name_new."','".$c_time."') ON CONFLICT DO NOTHING";
                      // dd($insertQuery);
                      DB::statement($insertQuery); 
                      $insert_done++; 
                    }
                    
                  }
                }
              }

              if ($file > 0) {
                
                $input = [
                  'doc_imported' => 1
                ];
                if($file == $insert_done) {
                  $lb_update = DB::table('migration_to_jb.beneficiary_lb')->where('lb_application_id', $application_id)->where('created_by_dist_code', $created_by_dist_code)->update($input);
                  $lb_update = 1;
                }
                else {
                  $lb_update = 1;
                }
                // if (count($insert_doc_type_arr) > 0) {
                //   $doc_inserted = DB::connection('pgsql_lb_encwrite')->table($schema_name_docs . '.ben_docs')->insert($insert_doc_type_arr);
                // } else {
                //   $doc_inserted = 1;
                // }
                if ($lb_update) {
                  DB::commit();
                  // $logmessage = "Total " . $file . " data has been migrated";
                  $total_count = $total_count+$file;
                  //dd( $logmessage);
                  // print $logmessage;
                } else {
                  // dump($lb_update);
                  // dd($doc_inserted);
                  DB::rollback();
                  // $return_msg = array('Error.. Please try again.');
                  // print_r($return_msg);
                }
              } 
              // else {
              //   $return_msg = array('No file fetch from LB..please try again.');
              //   //dd( $return_msg);
              //   print $return_msg;
              // }
            } 
            // else {
            //   $return_msg = array('No file fetch from LB..please try again.');
            //   //dd( $return_msg);
            //   print $return_msg;
            // }
          } 
          // else {
          //   $return_msg = array('No file fetch from LB..please try again.');
          //   //dd( $return_msg);
          //   print $return_msg;
          // }
        }
      }
      $app_id_string = implode(', ',$filearr);
      $logmessage = "Total " . $total_count . " data has been migrated  Application ID - ". $app_id_string;
      print $logmessage;
    } catch (\Exception $e) {
      dd($e);
    }
  }
}
