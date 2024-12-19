<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DupliacteApproveReject;
use App\Scheme;
use App\District;
use App\UrbanBody;
use App\GP;
use App\BeneficiaryPensions;
use App\PensionSc;
use App\PensionSt;
use App\Manabik;
use App\UpdateBenDetails;
use App\Configduty;
use App\DocumentType;
use App\Helpers\Helper;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\SSH;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Helpers\AuthChecker;



class ValidationLotTransactionController extends Controller
{
    public $sbi_sftp_server;
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
        $sbi_prod_server = Helper::getSBISftpServer();
        $this->sbi_sftp_server = $sbi_prod_server;
    }
    private function getSchemaName($scheme_id)
    {
        if (!is_null($scheme_id)) {
            $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
            //$parameter['scheme_id'] = $scheme_id;
            $schema_name =  $sObj->short_code;
            //dd($schema_name);
            if (empty($schema_name)) {
                $schema_name = 'pension';
            }
            $table_name =  strtolower($schema_name) . '.beneficiary';
        } else {
            $table_name =  'pension.beneficiary';
        }
        return $table_name;
    }

    public function lotMasterValidation()
    {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        // dd($designation_id_old);
        if ($designation_id_old == 'DDO') {
            $schemes = DB::select(DB::raw("select id,scheme_name from m_scheme where id  in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1) and is_active=1 order by scheme_name"));
            return view('av-lot-transaction/av_lot_transaction_index', ['schemes' => $schemes]);
        } else {
            return redirect("/")->with('success', 'User Disabled. ');
        }
    }

    public function reportLotMasterValidation(Request $request)
    {
        $scheme_code = $request->scheme_code;
        $status = $request->status;
        // dd($scheme_code);
        if ($request->ajax()) {
            $query = "";
            if ($status != '') {
                $query = "SELECT * FROM sbi.av_lot_master left join sbi.av_ack_code on sbi.av_lot_master.ack_status=sbi.av_ack_code.code  WHERE scheme_id = " . $scheme_code . " AND status = " . $status . " ";
            } else {
                $query = "SELECT * FROM sbi.av_lot_master left join sbi.av_ack_code on sbi.av_lot_master.ack_status=sbi.av_ack_code.code  WHERE scheme_id = " . $scheme_code . "";
            }
            $query .= " order by  
            CASE 
                    WHEN status = '0' THEN 0
                    WHEN status = '1' THEN 1
                    WHEN status = '2' THEN 2
                
                    WHEN status = '3' THEN 3
                    WHEN status = '4' THEN 4
                    WHEN status = '5' THEN 5
                    ELSE 99 
                END ASC ";
            // echo $query;die();
            $result = DB::connection('pgsql_mis')->select($query);
            // echo '<pre>';print_r($result);die();
            return datatables()->of($result)
                ->addIndexColumn()
                ->addColumn('total_record', function ($result) {
                    $record_btn = '';
                    $record_btn .= '<button class="btn btn-primary">' . $result->total_record . '</button>';
                    return $result->total_record;
                })
                ->addColumn('status', function ($result) {
                    $status = '';
                    $status = trim($result->status);
                    if ($status == 0) {
                        $status = 'Lot Created';
                    } else if ($status == 1) {
                        $status = 'Lot Created on '.date('d-m-Y',strtotime($result->created_at)).' <br>Validation File Created';
                    } else if ($status == 2) {
                        $status = 'Lot Created on '.date('d-m-Y',strtotime($result->created_at)).' <br>Validation File Created <br>File Pushed to SBI server on '.date('d-m-Y', strtotime($result->pushed_at));
                    } else if ($status == 3) {
                        $status = 'Lot Created on '.date('d-m-Y',strtotime($result->created_at)).' <br>Validation File Created <br>File Pushed to SBI server on '.date('d-m-Y', strtotime($result->pushed_at)).' <br>Acknowlegment Received';
                    } else if ($status == 4) {
                        $status = 'Lot Created on '.date('d-m-Y',strtotime($result->created_at)).' <br>Validation File Created <br>File Pushed to SBI server on '.date('d-m-Y', strtotime($result->pushed_at)).'<br>Acknowlegment Received <br>Resposne Received Successfully';
                    } else if ($status == 5) {
                        $status = 'Lot Created on '.date('d-m-Y',strtotime($result->created_at)).' <br>Validation File Created <br>File Pushed to SBI server on '.date('d-m-Y', strtotime($result->pushed_at)).' <br>Acknowlegment Received <br>Resposne Received Successfully <br>File Imported Successfully';
                    } else if ($status < 0) {
                        $status = 'Defunct Lot';
                    }
                    return $status;
                })
                ->addColumn('action', function ($result) {
                    $action_btn = '';
                    if ($result->status == 0) {
                        $value = base64_encode($result->input_file_name) . "_" . base64_encode($result->scheme_id);
                        $action_btn .= '<button value="' . $value . '" class="btn btn-info av_file_generate">Generate File</button>';
                    } else if ($result->status == 1) {
                        $value = base64_encode($result->input_file_name) . "_" . base64_encode($result->scheme_id);
                        $action_btn .= '<button value="' . $value . '" class="btn btn-primary av_push_file_sbi">Push To SBI</button>';
                        // $action_btn = 'Pending file pushed to SBI server';
                    } else if ($result->status == 2) {
                        if (is_null($result->ack_status)) {
                            $value = base64_encode($result->input_file_name) . "_" . base64_encode($result->scheme_id);
                            $action_btn .= '<button value="' . $value . '" class="btn btn-warning av_ack_file_sbi">Receive Ack</button>';
                        } else {
                            $action_btn = 'Acknowledgement Error.';
                        }
                        // $action_btn = 'Pending acknowledgment file from SBI server';
                    } else if ($result->status == 3) {
                        if (is_null($result->response_received_at) || $result->response_count <> $result->total_record) {
                            $value = base64_encode($result->input_file_name) . "_" . base64_encode($result->scheme_id);
                            $action_btn .= '<button value="' . $value . '" class="btn btn-danger av_receive_response_sbi">Receive Response</button>';
                        } else {
                            $action_btn = 'Receive Response, Something wrong!!';
                        }
                        // $action_btn = 'Import file pending from SBI server';
                    } else if ($result->status == 4) {
                        if (!is_null($result->response_received_at) && $result->response_count == $result->total_record) {
                            $value = base64_encode($result->input_file_name) . "_" . base64_encode($result->scheme_id);
                            $action_btn .= '<button value="' . $value . '" class="btn btn-success av_import_response_sbi">Import Response</button>';
                        } else {
                            $action_btn = 'Import Response, Something wrong!!';
                        }
                        // $action_btn = '<i class="glyphicon glyphicon-ok"></i>';
                    } else if ($result->status == 5) {
                        $action_btn = '<i class="glyphicon glyphicon-ok"></i>';
                    } else if ($result->status < 0) {
                        $action_btn = '<i class="glyphicon glyphicon-remove"></i>';
                    }
                    return $action_btn;
                })
                ->addColumn('ack_status', function ($result) {
                    $status = '';
                    $status = $result->description;
                    return $status;
                })
                ->addColumn('success_count', function ($result) {
                    return $result->success_count == '' ? 0 : $result->success_count;
                })
                ->addColumn('failed_count', function ($result) {
                    return $result->failed_count == '' ? 0 : $result->failed_count;
                })
                ->rawColumns(['total_record', 'status', 'action', 'ack_status', 'success_count', 'failed_count'])
                ->make(true);
        }
    }

    public function fileGeneration(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in form submit.');
            return response()->json($response, $statusCode);
        }
        try {
            $scheme_id = (int) base64_decode($request->scheme_id);
            $file_name = base64_decode($request->file_name);
            // $table_name = $this->getSchemaName($scheme_id);

            $query = "SELECT string_agg(tmp.record_data, chr(10)) as record_data FROM (
            SELECT CONCAT(header_id,originator_code,responder_code,file_upload_date,file_ref_no,lpad(trim(to_char(total_record,'999999')), 6, '0'),filler) AS record_data FROM sbi.av_lot_master WHERE input_file_name='" . $file_name . "'
            UNION ALL
            SELECT string_agg(T.ben_data, chr(10)) as record_data FROM (
                SELECT CONCAT(record_id,record_ref_no,ifsc,acc_no,ben_name,unique_ref_no,filler) as ben_data from sbi.av_lot_details WHERE status=0 AND input_file_name='" . $file_name . "'
            ) T
            ) AS tmp";
            $outputContent = DB::connection('pgsql')->select($query);
            $file_content = $outputContent[0]->record_data;
            $storagePath = 'app/sbi/AccountValidation/ToProcess/' . $file_name;
            Storage::put('sbi/AccountValidation/ToProcess/' . $file_name, $file_content);
            // dd($getFile);
            if (Storage::exists('sbi/AccountValidation/ToProcess/' . $file_name)) {
                // dd(Storage::get($storagePath));
                $header = 0;
                $max = 180;
                $issues = false;
                $file = fopen(storage_path($storagePath), 'r');
                // dd(fgets($file));
                if ($file) {
                    while (($line = fgets($file)) !== false) {
                        // reading each line
                        if ($header === 0) {
                            $length = strlen(trim($line));
                            $header++;
                        } else {
                            $length = strlen(trim($line));
                            if ($length !== $max) {
                                $issues = true;
                                break;
                            }
                            $header++;
                        }
                    }
                    fclose($file);
                    if ($issues == false) {
                        $file_generate_status = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['status' => 1, 'file_generated_at' => date('Y-m-d H:i:s')]);

                        if ($file_generate_status) {
                            $response = array(
                                'status' => 1, 'msg' => $file_name . ' generated successfully.',
                                'type' => 'green', 'icon' => 'fa fa-success', 'title' => 'Success!!'
                            );
                        } else {
                            $response = array(
                                'status' => 5, 'msg' => 'Status is not updated for file ' . $file_name,
                                'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                            );
                        }
                    } else {
                        $response = array(
                            'status' => 2, 'msg' => 'Format is not valid for file ' . $file_name,
                            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                        );
                    }
                } else {
                    $response = array(
                        'status' => 3, 'msg' => 'Error opening file - ' . $file_name,
                        'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                    );
                }
            } else {
                $response = array(
                    'status' => 4, 'msg' => 'File not found.',
                    'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Info!!'
                );
            }
        } catch (\Exception $e) {
            // dd($e);
            $response = array(
                'exception' => true,
                // 'exception_message' => 'Something went wrong!',
                'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function pushedSBIAccountValidationFile(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in form submit.');
            return response()->json($response, $statusCode);
        }
        try {
            $scheme_id = (int) base64_decode($request->scheme_id);
            $file_name = base64_decode($request->file_name);
            // $table_name = $this->getSchemaName($scheme_id);
            $storagePath = 'app/sbi/AccountValidation/ToProcess/' . $file_name;
            $data_exists = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->count();
            if ($data_exists > 0) {
                if (file_exists(storage_path('app/sbi/AccountValidation/ToProcess/') . '//' . $file_name)) {
                    // $av_file = Storage::get($storagePath);
                    $av_file_content = file_get_contents(storage_path($storagePath));
                    Storage::disk($this->sbi_sftp_server)->put('AccountValidation/ToProcess/' . $file_name, $av_file_content);  ///////uncomment in production
                    $exists = Storage::disk($this->sbi_sftp_server)->exists('AccountValidation/ToProcess/' . $file_name);  ///////uncomment in production
                    if ($exists) {
                        // Transfer file to Picked
                        rename(storage_path('app/sbi/AccountValidation/ToProcess/') . '//' . $file_name, storage_path('app/sbi/AccountValidation/ToProcess/Picked/') . '//' . $file_name);
                        // Updates in DB
                        $push_to_sbi_status = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->where('status', 1)->update(['status' => 2, 'pushed_at' => date('Y-m-d H:i:s')]);
                        DB::table('sbi.av_transaction_payload')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->delete();
                        DB::table('sbi.av_transaction_payload')->insert(['input_file_name' => $file_name, 'scheme_id' => $scheme_id, 'sent_payload' => $av_file_content]);


                        if ($push_to_sbi_status) {
                            $response = array(
                                'status' => 1, 'msg' => 'File No:- <b>' . $file_name . '</b> has been pushed to SBI successfully.',
                                'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                            );
                        } else {
                            $response = array(
                                'status' => 2, 'msg' => 'Status update error for File - <b>' . $file_name . '</b> Please Try again later.',
                                'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
                            );
                        }
                    } else {
                        $response = array(
                            'status' => 3, 'msg' => 'File <b>' . $file_name . '</b> has not been pushed. Please try after sometime.',
                            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
                        );
                    }
                } else {
                    $response = array(
                        'status' => 4, 'msg' => 'File not found for file - <b>' . $file_name . '</b>',
                        'type' => 'green', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                    );
                }
            } else {
                $response = array(
                    'status' => 4, 'msg' => 'Not found in DB file - <b>' . $file_name . '</b>',
                    'type' => 'green', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                );
            }
        } catch (\Exception $e) {
            // dd($e);
            $response = array(
                'exception' => true,
                'exception_message' => 'Something went wrong!',
                // 'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function receiveAckSBIAccountValidationFile(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in form submit.');
            return response()->json($response, $statusCode);
        }
        try {
            $scheme_id = (int) base64_decode($request->scheme_id);
            $file_name = base64_decode($request->file_name);
            // $table_name = $this->getSchemaName($scheme_id);
            $ackfile_name = str_replace(".txt", "-ACK.xml", $file_name);
            $data_exists = DB::table('sbi.av_lot_master')->select('ack_status')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->whereNull('ack_status')->first();
            if (is_null($data_exists->ack_status)) {
                $exists = Storage::disk($this->sbi_sftp_server)->exists('AccountValidation/Acknowledgement/' . $ackfile_name);  ///////uncomment in production
                if ($exists) {
                    $remote_file = Storage::disk($this->sbi_sftp_server)->get('AccountValidation/Acknowledgement/' . $ackfile_name);  //// uncomment in production
                    Storage::put('sbi/AccountValidation/Acknowledgement/' . $ackfile_name, $remote_file);  //// uncomment in production

                    $remote_xml_file = simplexml_load_string($remote_file);
                    $ack_remarks = $remote_xml_file->ACCOUNT_VALIDATION['ACK_REMARKS'];
                    $ack_status_code = $remote_xml_file->ACCOUNT_VALIDATION['ACK_STATUS_CODE'];
                    $av_ack_file_content = file_get_contents(storage_path('app/sbi/AccountValidation/Acknowledgement/' . $ackfile_name));

                    // Transfer file to Picked
                    rename(storage_path('app/sbi/AccountValidation/Acknowledgement/') . '//' . $ackfile_name, storage_path('app/sbi/AccountValidation/Acknowledgement/Picked/') . '//' . $ackfile_name);

                    if ($ack_status_code == '100') {
                        // Updates in DB
                        $ack_update_status = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->where('status', 2)->update(['status' => 3, 'ack_status' => $ack_status_code]);
                        $ack_payload_update = DB::table('sbi.av_transaction_payload')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['ack_payload_at' => date('Y-m-d H:i:s'), 'ack_payload' => $av_ack_file_content, 'status' => 3]);

                        if ($ack_update_status) {
                            $response = array(
                                'status' => 1, 'msg' => 'File No:- <b>' . $file_name . '</b> acknowledgement has been received from SBI successfully.',
                                'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                            );
                        } else {
                            $response = array(
                                'status' => 2, 'msg' => 'Acknowledgement Status update error. Please Try again later.',
                                'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
                            );
                        }
                    } else {
                        // Updates in DB
                        $ack_update_status = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['ack_status' => $ack_status_code]);
                        $ack_payload_update = DB::table('sbi.av_transaction_payload')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['ack_payload_at' => date('Y-m-d H:i:s'), 'ack_payload' => $av_ack_file_content]);

                        $response = array(
                            'status' => 3, 'msg' => 'Acknowledgement error from SBI for file <b>' . $file_name . '</b> with Remarks - ' . $ack_remarks,
                            'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Acnowledgement Error!!'
                        );
                    }
                } else {
                    $response = array(
                        'status' => 4, 'msg' => 'Response is not generated in SBI server for file - <b>' . $file_name . '</b>',
                        'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Info!!'
                    );
                }
            } else {
                $response = array(
                    'status' => 5, 'msg' => 'Acknowledgement file already came form SBI for file - <b>' . $file_name . '</b>',
                    'type' => 'green', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                );
            }
        } catch (\Exception $e) {
            // dd($e);
            $response = array(
                'exception' => true,
                'exception_message' => 'Something went wrong!',
                // 'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    // Through schedular the responses are coming
    public function receiveAccountValidationSBIResponse(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in form submit.');
            return response()->json($response, $statusCode);
        }
        try {
            $scheme_id = (int) base64_decode($request->scheme_id);
            $file_name = base64_decode($request->file_name);
            $responsefile_name = str_replace("-INP", "-RES", $file_name);
            $data_exists = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->where('ack_status', '100')->where('status', 3)->whereNull('response_received_at')
                ->whereRaw("(total_record != coalesce(response_count, 0))")
                ->get();
            // dd($data_exists);
            $total_records = $data_exists[0]->total_record;
            // $data_exists = 0;
            if (count($data_exists) > 0) {
                $exists = Storage::disk($this->sbi_sftp_server)->exists('AccountValidation/Response/' . $responsefile_name);  ///////uncomment in production
                if ($exists) {
                    $remote_file = Storage::disk($this->sbi_sftp_server)->get('AccountValidation/Response/' . $responsefile_name);  //// uncomment in production
                    Storage::put('sbi/AccountValidation/Response/' . $responsefile_name, $remote_file);  //// uncomment in production

                    if (file_exists(storage_path('app/sbi/AccountValidation/Response/') . '//' . $responsefile_name)) {
                        $fileContent = file_get_contents(storage_path('app/sbi/AccountValidation/Response/' . $responsefile_name));

                        $fileRefNo = substr($fileContent, 32, 10);
                        $lines = explode("\n", $fileContent);
                        $counter = 0;
                        $insert_import_array = array();
                        $ir = 0;
                        foreach ($lines as $line) {
                            if ($counter === 0) {
                                $counter++;
                                continue; // Skip header line
                            }

                            $field2 = substr($line, 2, 15); // Record Ref No
                            $field6 = substr($line, 163, 17); // Consumer ID
                            $field7 = substr($line, 180, 2); // Account Validation Flag
                            $field8 = substr($line, 182, 2); // Name Validation Flag
                            $field9 = substr($line, 184, 200); // Name response fro bank
                            if ($field2 != '' && $field6 != '' && $field7 != '' && $field8 != '' && $field9 != '') {
                                $insert_import_array[$ir]['record_ref_no'] = $field2;
                                $insert_import_array[$ir]['unique_ref_no'] = $field6;
                                $insert_import_array[$ir]['scheme_id'] = $scheme_id;
                                $insert_import_array[$ir]['file_ref_no'] = $fileRefNo;
                                $insert_import_array[$ir]['input_file_name'] = $file_name;
                                $insert_import_array[$ir]['account_status_code'] = $field7;
                                $insert_import_array[$ir]['name_status_code'] = $field8;
                                $insert_import_array[$ir]['name_response'] = $field9;
                                $ir++;
                            }
                            $counter++;
                        }

                        if (count($insert_import_array) == $total_records) {
                            DB::beginTransaction();
                            DB::table('sbi.sbi_acc_validation_status_import')->where('input_file_name', $file_name)->where('file_ref_no', $fileRefNo)->delete();
                            foreach (array_chunk($insert_import_array, 2000) as $inst_chunk) {
                                DB::connection('pgsql')->table('sbi.sbi_acc_validation_status_import')->insert($inst_chunk);
                            }
                            $insert_final = DB::table('sbi.sbi_acc_validation_status_import')->where('input_file_name', $file_name)->where('file_ref_no', $fileRefNo)->count();

                            if (count($insert_import_array) == $insert_final) {
                                $receive_response_status = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->where('status', 3)->update(['response_received_at' => date('Y-m-d H:i:s'), 'response_count' => $insert_final, 'status' => 4]);
                                $ack_payload_update = DB::table('sbi.av_transaction_payload')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['received_payload_at' => date('Y-m-d H:i:s'), 'received_payload' => $fileContent, 'status' => 4]);
                                if ($receive_response_status) {
                                    // Transfer file to Picked
                                    rename(storage_path('app/sbi/AccountValidation/Response/') . '//' . $responsefile_name, storage_path('app/sbi/AccountValidation/Response/Picked/') . '//' . $responsefile_name);
                                    DB::commit();
                                    $response = array(
                                        'status' => 1, 'msg' => 'Imported Successfully for file - <b>' . $file_name . '</b>',
                                        'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                                    );
                                } else {
                                    DB::rollback();
                                    $response = array(
                                        'status' => 2, 'msg' => 'Number of responses are not updated for file - <b>' . $file_name . '</b>',
                                        'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                                    );
                                }
                            } else {
                                DB::rollback();
                                $response = array(
                                    'status' => 3, 'msg' => 'All records are not imported into DB for file - <b>' . $file_name . '</b>',
                                    'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                                );
                            }
                        } else {
                            $response = array(
                                'status' => 4, 'msg' => 'File is incomplete which is fetched from SBI server for file - <b>' . $file_name . '</b>',
                                'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                            );
                        }
                    } else {
                        $response = array(
                            'status' => 5, 'msg' => 'File not fetched from SBI server for file - <b>' . $file_name . '</b>',
                            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                        );
                    }
                }
                else {
                    $response = array(
                        'status' => 6, 'msg' => 'Response is not generated in SBI server for file - <b>' . $file_name . '</b>',
                        'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Info!!'
                    );
                }
            } else {
                $response = array(
                    'status' => 7, 'msg' => 'Data not found in DB for file - <b>' . $file_name . '</b>',
                    'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                );
            }
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            $response = array(
                'exception' => true,
                'exception_message' => 'Something went wrong!',
                // 'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function importAccountValidationSBIResponse(Request $request)
    {
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = array('error' => 'Error occured in form submit.');
            return response()->json($response, $statusCode);
        }
        try {
            $scheme_id = (int) base64_decode($request->scheme_id);
            $file_name = base64_decode($request->file_name);
            // $table_name = $this->getSchemaName($scheme_id);
            $data_exists = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->where('ack_status', '100')->where('status', 4)->whereNotNull('response_received_at')->whereRaw("(total_record = coalesce(response_count, 0))")->get();
            // $data_exists = 0;
            if (count($data_exists) > 0) {
                $av_import_data = DB::table('sbi.sbi_acc_validation_status_import')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->count();
                if ($av_import_data == $data_exists[0]->total_record) {
                    DB::beginTransaction();
                    $is_imported_fun = DB::connection('pgsql')->select("SELECT sbi.av_response_import_lot('" . $file_name . "', " . $scheme_id . ");");
                    $is_imported = $is_imported_fun[0]->av_response_import_lot;

                    $is_archive_details_fun = DB::connection('pgsql')->select("SELECT sbi.av_response_lot_details_archive('" . $file_name . "', " . $scheme_id . ");");
                    $is_archive_details = $is_archive_details_fun[0]->av_response_lot_details_archive;

                    if ($is_imported==1 && $is_archive_details==1) {
                        DB::commit();
                        $response = array(
                            'status' => 1, 'msg' => 'Imported successfully for file - <b>' . $file_name . '</b>',
                            'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                        );
                    } else {
                        DB::rollback();
                        $response = array(
                            'status' => 2, 'msg' => 'File Import failed, something wrong for file - <b>' . $file_name . '</b>',
                            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                        );
                    }
                    
                } else {
                    $response = array(
                        'status' => 3, 'msg' => 'Total beneficiary response is not received for file - <b>' . $file_name . '</b>',
                        'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                    );
                }
            } else {
                $response = array(
                    'status' => 4, 'msg' => 'Data not found in DB for file - <b>' . $file_name . '</b>',
                    'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error!!'
                );
            }
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            $response = array(
                'exception' => true,
                'exception_message' => 'Something went wrong!',
                // 'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }
}
