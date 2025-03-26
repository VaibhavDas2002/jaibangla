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

class ValidationLotTransactionController extends Controller
{
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
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
        $user_id = Auth::user()->id;
        $designation_id = Auth::user()->designation_id;
        // dd($designation_id);
        if ($designation_id == 'DDO') {
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
                $query = "SELECT * FROM sbi.av_lot_master WHERE scheme_id = " . $scheme_code . " AND status = " . $status . " ";
            } else {
                $query = "SELECT * FROM sbi.av_lot_master WHERE scheme_id = " . $scheme_code . "";
            }
            $query .= " order by  
            CASE 
                    WHEN status = '0' THEN 0
                    WHEN status = '1' THEN 1
                    WHEN status = '2' THEN 2
                
                    WHEN status = '3' THEN 3
                    WHEN status = '4' THEN 4
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
                        $status = 'Lot Created <br>Validation File Created on ' . date('m/d/Y', strtotime($result->created_at));
                    } else if ($status == 2) {
                        $status = 'Lot Created <br>Validation File Created <br>File Pushed to SBI server on ' . date('m/d/Y', strtotime($result->pushed_at));
                    } else if ($status == 3) {
                        $status = 'Lot Created <br>Validation File Created <br>File Pushed to SBI server <br>Acknowlegment Received';
                    } else if ($status == 4) {
                        $status = 'Lot Created <br>Validation File Created <br>File Pushed to SBI server <br>Acknowlegment Received <br>File Imported Successfully';
                    } else if ($status < 0) {
                        $status = 'Defunc Lot';
                    }
                    return $status;
                })
                ->addColumn('action', function ($result) {
                    $action_btn = '';
                    if ($result->status == 0) {
                        $value = base64_encode($result->input_file_name) . "_" . base64_encode($result->scheme_id);
                        $action_btn .= '<button value="' . $value . '" class="btn btn-warning av_file_generate">Generate File</button>';
                    } else if ($result->status == 1) {
                        $value = base64_encode($result->input_file_name) . "_" . base64_encode($result->scheme_id);
                        $action_btn .= '<button value="' . $value . '" class="btn btn-primary av_push_file_sbi">Push To SBI</button>';
                        // $action_btn = 'Pending file pushed to SBI server';
                    } else if ($result->status == 2) {
                        if(is_null($result->ack_status)) {
                            $value = base64_encode($result->input_file_name) . "_" . base64_encode($result->scheme_id);
                            $action_btn .= '<button value="' . $value . '" class="btn btn-success av_ack_file_sbi">Receive Ack</button>';
                        }
                        else {
                            $action_btn = 'Acknowledgement Error.';
                        }
                        // $action_btn = 'Pending acknowledgment file from SBI server';
                    } else if ($result->status == 3) {
                        $action_btn = 'Import file pending from SBI server';
                    } else if ($result->status == 4) {
                        $action_btn = '<i class="glyphicon glyphicon-ok"></i>';
                    } else if ($result->status < 0) {
                        $action_btn = '<i class="glyphicon glyphicon-remove"></i>';
                    }
                    return $action_btn;
                })
                ->addColumn('ack_status', function ($result) {
                    $status = '';
                    $status = $result->ack_status;
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
            Storage::put($storagePath, $file_content);
            // dd($getFile);
            if (Storage::exists($storagePath)) {
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
                                'type' => 'red', 'icon' => 'fa fa-danger', 'title' => 'Error!!'
                            );
                        }
                    } else {
                        $response = array(
                            'status' => 2, 'msg' => 'Format is not valid for file ' . $file_name,
                            'type' => 'red', 'icon' => 'fa fa-danger', 'title' => 'Error!!'
                        );
                    }
                } else {
                    $response = array(
                        'status' => 3, 'msg' => 'Error opening file - ' . $file_name,
                        'type' => 'red', 'icon' => 'fa fa-danger', 'title' => 'Error!!'
                    );
                }
            } else {
                $response = array(
                    'status' => 4, 'msg' => 'File not found.',
                    'type' => 'green', 'icon' => 'fa fa-danger', 'title' => 'Error!!'
                );
            }
        } catch (\Exception $e) {
            // dd($e);
            $response = array(
                'exception' => true,
                //   'exception_message' => 'Something went wrong!',
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
            if($data_exists > 0) {
                if (file_exists(storage_path('app/sbi/AccountValidation/ToProcess/') . '//' . $file_name)) {
                    // $av_file = Storage::get($storagePath);
                    $av_file_content = file_get_contents(storage_path($storagePath));
                    Storage::disk('sftp_sbi')->put('AccountValidation/ToProcess/' . $file_name, $av_file_content);  ///////uncomment in production
				    $exists = Storage::disk('sftp_sbi')->exists('AccountValidation/ToProcess/' . $file_name);  ///////uncomment in production
                    if ($exists) {
                        // Transfer file to Picked
                        rename(storage_path('app/sbi/AccountValidation/ToProcess/') . '//' . $file_name, storage_path('app/sbi/AccountValidation/ToProcess/Picked/') . '//' . $file_name);
                        // Updates in DB
                        $push_to_sbi_status = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['status' => 2, 'pushed_at' => date('Y-m-d H:i:s')]);
                        DB::table('sbi.av_transaction_payload')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->delete();
                        DB::table('sbi.av_transaction_payload')->insert(['input_file_name' => $file_name, 'scheme_id' => $scheme_id, 'sent_payload' => $av_file_content]);
    
    
                        if ($push_to_sbi_status) {
                            $response = array(
                                'status' => 1, 'msg' => 'File No:- ' . $file_name . ' has been pushed to SBI successfully.',
                                'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                            );
                        } else {
                            $response = array(
                                'status' => 2, 'msg' => 'Status update error. Please Try again later.',
                                'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
                            );
                        }
                    } else {
                        $response = array(
                            'status' => 3, 'msg' => 'File '.$file_name.' has not been pushed. Please try after sometime.',
                            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
                        );
                    }
                } else {
                    $response = array(
                        'status' => 4, 'msg' => 'File not found.' . $file_name,
                        'type' => 'green', 'icon' => 'fa fa-danger', 'title' => 'Error!!'
                    );
                }
            }
            else {
                $response = array(
                    'status' => 4, 'msg' => 'Not found in DB file ' . $file_name,
                    'type' => 'green', 'icon' => 'fa fa-danger', 'title' => 'Error!!'
                );
            }
        } catch (\Exception $e) {
            // dd($e);
            $response = array(
                'exception' => true,
                //   'exception_message' => 'Something went wrong!',
                'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }

    public function receiveAckSBIAccountValidationFile(Request $request) {
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
            if(is_null($data_exists->ack_status)) {
                $remote_file = Storage::disk('sftp_sbi')->get('AccountValidation/Acknowledgement/' . $ackfile_name);  //// uncomment in production
                Storage::put('sbi/AccountValidation/Acknowledgement/' . $ackfile_name, $remote_file);  //// uncomment in production

                $remote_xml_file = simplexml_load_string($remote_file);
                $ack_remarks = $remote_xml_file->ACCOUNT_VALIDATION['ACK_REMARKS'];
                $ack_status_code = $remote_xml_file->ACCOUNT_VALIDATION['ACK_STATUS_CODE'];
                $av_ack_file_content = file_get_contents(storage_path('app/sbi/AccountValidation/Acknowledgement/' . $ackfile_name));

                // Transfer file to Picked
                rename(storage_path('app/sbi/AccountValidation/Acknowledgement/') . '//' . $ackfile_name, storage_path('app/sbi/AccountValidation/Acknowledgement/Picked/') . '//' . $ackfile_name);

                if($ack_status_code == '100') {
                    // Updates in DB
                    $ack_update_status = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['status' => 3, 'ack_status' => $ack_status_code]);
                    $ack_payload_update = DB::table('sbi.av_transaction_payload')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['ack_payload_at' => date('Y-m-d H:i:s'), 'ack_payload' => $av_ack_file_content, 'status' => 3]);

                    if ($ack_update_status) {
                        $response = array(
                            'status' => 1, 'msg' => 'File No:- ' . $file_name . ' acknowledgement has been received from SBI successfully.',
                            'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                        );
                    } else {
                        $response = array(
                            'status' => 2, 'msg' => 'Acknowledgement Status update error. Please Try again later.',
                            'type' => 'red', 'icon' => 'fa fa-warning', 'title' => 'Error'
                        );
                    }
                }
                else {
                    // Updates in DB
                    $ack_update_status = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['ack_status' => $ack_status_code]);
                    $ack_payload_update = DB::table('sbi.av_transaction_payload')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['ack_payload_at' => date('Y-m-d H:i:s'), 'ack_payload' => $av_ack_file_content]);

                    $response = array(
                        'status' => 3, 'msg' => 'Acknowledgement error from SBI for file ' . $file_name. ' with Remarks - '. $ack_remarks,
                        'type' => 'blue', 'icon' => 'fa fa-info', 'title' => 'Acnowledgement Error!!'
                    );
                }
            }
            else {
                $response = array(
                    'status' => 4, 'msg' => 'Acknowledgement file already came form SBI for file ' . $file_name,
                    'type' => 'green', 'icon' => 'fa fa-danger', 'title' => 'Error!!'
                );
            }
        } catch (\Exception $e) {
            // dd($e);
            $response = array(
                'exception' => true,
                //   'exception_message' => 'Something went wrong!',
                'exception_message' => $e->getMessage(),
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }
}
