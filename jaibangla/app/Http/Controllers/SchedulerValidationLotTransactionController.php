<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Scheme;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchedulerValidationLotTransactionController extends Controller
{
    public $sbi_sftp_server;
    public function __construct()
    {
        set_time_limit(0);
        date_default_timezone_set('Asia/Kolkata');
        $sbi_prod_server = Helper::getSBISftpServer();
        $this->sbi_sftp_server = $sbi_prod_server;
    }
    public function scheduleReceiveResponseValidationLot()
    {
        $time_array = DB::select(DB::raw("select to_char(now(),'DDMONYYYY-HH24MISS') as datetime"));
        $var_file_name = $time_array[0]->datetime;
        $log_file_name = 'sbi/AccountValidation/ScheduleLog/log_AV_Response_' . $var_file_name . '.txt';
        $email_log_file = 'sbi/AccountValidation/DailyLog/daily_av_reposne_schedule_job_file.txt';
        Storage::put($log_file_name, 'Function scheduleReceiveResponseValidationLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A"));
        Storage::put($email_log_file, 'Function scheduleReceiveResponseValidationLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A"));
        echo '<br/>Function scheduleReceiveResponseValidationLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A");
        Storage::append($log_file_name, '=====================================================');
        Storage::append($email_log_file, '=====================================================');
        echo '<br/>=====================================================';

        $data_exists = DB::table('sbi.av_lot_master')->where('ack_status', '100')->where('status', 3)->whereNull('response_received_at')->whereRaw("(total_record != coalesce(response_count, 0))")
        ->limit(20)
        ->get();

        if (count($data_exists) > 0) {
            $f_count = 0;
            foreach ($data_exists as $item) {
                Storage::append($log_file_name, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
                Storage::append($email_log_file, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
                $file_name = $item->input_file_name;
                $scheme_id = $item->scheme_id;
                $f_count = $f_count + 1;
                Storage::append($log_file_name, '******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                Storage::append($email_log_file, '******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                echo '<br/>******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A");
                Storage::append($log_file_name, 'Receiving Account Validation Resposne for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                Storage::append($email_log_file, 'Receiving Account Validation Resposne for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                echo '<br/>Receiving Account Validation Resposne for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;

                Storage::append($log_file_name, 'Function receive_response_validation_lot() is called');
                Storage::append($email_log_file, 'Function receive_response_validation_lot() is called');
                Storage::append($log_file_name, '------------------------------------------------------');
                Storage::append($email_log_file, '------------------------------------------------------');
                echo '<br/>Function receive_response_validation_lot() is called';
                echo '<br/>------------------------------------------------------';
                $this->receive_response_validation_lot($file_name, $scheme_id, $log_file_name, $email_log_file);
                Storage::append($log_file_name, '------------------------------------------------------');
                Storage::append($email_log_file, '------------------------------------------------------');
                Storage::append($log_file_name, 'Function receive_response_validation_lot() is end');
                Storage::append($email_log_file, 'Function receive_response_validation_lot() is end');
                echo '<br/>------------------------------------------------------';
                echo '<br/>Function receive_response_validation_lot() is end';
            }
        } else {
            Storage::append($log_file_name, 'No record pending for receive resposne.');
            Storage::append($email_log_file, 'No record pending for receive resposne.');
        }
        Storage::append($log_file_name, '=====================================================');
        Storage::append($email_log_file, '=====================================================');
        echo '<br/>=====================================================';
        Storage::append($log_file_name, 'Function scheduleReceiveResponseValidationLot() is completed' . ' Date : ' . date("l jS \of F Y h:i:s A"));
        Storage::append($email_log_file, 'Function scheduleReceiveResponseValidationLot() is completed' . ' Date : ' . date("l jS \of F Y h:i:s A"));
        echo '<br/>Function scheduleReceiveResponseValidationLot() is completed' . ' Date : ' . date("l jS \of F Y h:i:s A");
        Storage::append($log_file_name, 'Job completed');
        Storage::append($email_log_file, 'Job completed');
        echo '<br/>Job completed';
    }

    public function receive_response_validation_lot($file_name, $scheme_id, $log_file_name, $email_log_file)
    {
        try {
            $scheme_id = $scheme_id;
            $file_name = $file_name;
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
                    Storage::append($log_file_name, 'File exists in the SBI server for file : ' . $responsefile_name . ' and Scheme ID : ' . $scheme_id);
                    Storage::append($email_log_file, 'File exists in the SBI server for file : ' . $responsefile_name . ' and Scheme ID : ' . $scheme_id);
                    echo '<br/>File exists in the SBI server for file : ' . $responsefile_name . ' and Scheme ID : ' . $scheme_id;

                    $remote_file = Storage::disk($this->sbi_sftp_server)->get('AccountValidation/Response/' . $responsefile_name);  //// uncomment in production
                    Storage::put('sbi/AccountValidation/Response/' . $responsefile_name, $remote_file);  //// uncomment in production

                    if (file_exists(storage_path('app/sbi/AccountValidation/Response/') . '//' . $responsefile_name)) {
                        Storage::append($log_file_name, 'File is fetched successfully in Jai Bangla server for file : ' . $responsefile_name . ' and Scheme ID : ' . $scheme_id);
                        Storage::append($email_log_file, 'File is fetched successfully in Jai Bangla server for file : ' . $responsefile_name . ' and Scheme ID : ' . $scheme_id);
                        echo '<br/>File is fetched successfully in Jai Bangla server for file : ' . $responsefile_name . ' and Scheme ID : ' . $scheme_id;

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

                        Storage::append($log_file_name, 'Total Records in File : '. $total_records .' , Total Records Import : '. count($insert_import_array) .' for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                        Storage::append($email_log_file, 'Total Records in File : '. $total_records .' , Total Records Import : '. count($insert_import_array) .' for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                        echo '<br/>Total Record in File : '. $total_records .' , Total Records Import : '. count($insert_import_array) .' for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;

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
                                    Storage::append($log_file_name, 'Success : Resposne Received Successfully for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                                    Storage::append($email_log_file, 'Success : Resposne Received Successfully for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                                    echo '<br/>Success : Resposne Received Successfully for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                                } else {
                                    DB::rollback();
                                    Storage::append($log_file_name, 'Error : Number of responses are not updated for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                                    Storage::append($email_log_file, 'Error : Number of responses are not updated for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                                    echo '<br/>Error : Number of responses are not updated for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                                }
                            } else {
                                DB::rollback();
                                Storage::append($log_file_name, 'Error : All records are not imported into DB for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                                Storage::append($email_log_file, 'Error : All records are not imported into DB for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                                echo '<br/>Error : All records are not imported into DB for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                            }
                        } else {
                            Storage::append($log_file_name, 'Info : File is incomplete which is fetched from SBI server for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                            Storage::append($email_log_file, 'Info : File is incomplete which is fetched from SBI server for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                            echo '<br/>Info : File is incomplete which is fetched from SBI server for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                        }
                    } else {
                        Storage::append($log_file_name, 'Info : File not fetched from SBI server for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                        Storage::append($email_log_file, 'Info : File not fetched from SBI server for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                        echo '<br/>Info : File not fetched from SBI server for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                    }
                }
                else {
                    Storage::append($log_file_name, 'Info : Response is not generated in SBI server for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                    Storage::append($email_log_file, 'Info : Response is not generated in SBI server for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                    echo '<br/>Info : Response is not generated in SBI server for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                }
            } else {
                Storage::append($log_file_name, 'Info : Data not found in DB for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                Storage::append($email_log_file, 'Info : Data not found in DB for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                echo '<br/>Info : Data not found in DB for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
            }
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            Storage::append($log_file_name, 'Exception : for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id.'  Message : ' . $e->getMessage());
            Storage::append($email_log_file, 'Exception : for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id.'  Message : ' . $e->getMessage());
            echo '<br/>Exception : for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id.'  Message : ' . $e->getMessage();
        }
    }

    public function scheduleImportResponseValidationLot()
    {
        $time_array = DB::select(DB::raw("select to_char(now(),'DDMONYYYY-HH24MISS') as datetime"));
        $var_file_name = $time_array[0]->datetime;
        $log_file_name = 'sbi/AccountValidation/ScheduleLog/log_AV_Import_Response_' . $var_file_name . '.txt';
        $email_log_file = 'sbi/AccountValidation/DailyLog/daily_av_Import_reposne_schedule_job_file.txt';
        Storage::put($log_file_name, 'Function scheduleImportResponseValidationLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A"));
        Storage::put($email_log_file, 'Function scheduleImportResponseValidationLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A"));
        echo '<br/>Function scheduleImportResponseValidationLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A");
        Storage::append($log_file_name, '=====================================================');
        Storage::append($email_log_file, '=====================================================');
        echo '<br/>=====================================================';

        $data_exists = DB::table('sbi.av_lot_master')->where('ack_status', '100')->where('status', 4)->whereNotNull('response_received_at')->whereRaw("(total_record = coalesce(response_count, 0))")
        ->limit(20)
        ->get();

        if (count($data_exists) > 0) {
            $f_count = 0;
            foreach ($data_exists as $item) {
                Storage::append($log_file_name, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
                Storage::append($email_log_file, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
                $file_name = $item->input_file_name;
                $scheme_id = $item->scheme_id;
                $f_count = $f_count + 1;
                Storage::append($log_file_name, '******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                Storage::append($email_log_file, '******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                echo '<br/>******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A");
                Storage::append($log_file_name, 'Importing Account Validation Resposne for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                Storage::append($email_log_file, 'Importing Account Validation Resposne for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                echo '<br/>Importing Account Validation Resposne for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;

                Storage::append($log_file_name, 'Function import_response_validation_lot() is called');
                Storage::append($email_log_file, 'Function import_response_validation_lot() is called');
                Storage::append($log_file_name, '------------------------------------------------------');
                Storage::append($email_log_file, '------------------------------------------------------');
                echo '<br/>Function import_response_validation_lot() is called';
                echo '<br/>------------------------------------------------------';
                $this->import_response_validation_lot($file_name, $scheme_id, $log_file_name, $email_log_file);
                Storage::append($log_file_name, '------------------------------------------------------');
                Storage::append($email_log_file, '------------------------------------------------------');
                Storage::append($log_file_name, 'Function import_response_validation_lot() is end');
                Storage::append($email_log_file, 'Function import_response_validation_lot() is end');
                echo '<br/>------------------------------------------------------';
                echo '<br/>Function import_response_validation_lot() is end';
            }
        } else {
            Storage::append($log_file_name, 'No record pending for receive resposne.');
            Storage::append($email_log_file, 'No record pending for receive resposne.');
        }
        Storage::append($log_file_name, '=====================================================');
        Storage::append($email_log_file, '=====================================================');
        echo '<br/>=====================================================';
        Storage::append($log_file_name, 'Function scheduleImportResponseValidationLot() is completed');
        Storage::append($email_log_file, 'Function scheduleImportResponseValidationLot() is completed');
        echo '<br/>Function scheduleImportResponseValidationLot() is completed';
        Storage::append($log_file_name, 'Job completed');
        Storage::append($email_log_file, 'Job completed');
        echo '<br/>Job completed';
    }

    public function import_response_validation_lot($file_name, $scheme_id, $log_file_name, $email_log_file) {
        try {
            $scheme_id = $scheme_id;
            $file_name = $file_name;
            $data_exists = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->where('ack_status', '100')->where('status', 4)->whereNotNull('response_received_at')->whereRaw("(total_record = coalesce(response_count, 0))")->get();
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
                        Storage::append($log_file_name, 'Success : Imported successfully for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                        Storage::append($email_log_file, 'Success : Imported successfully for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                        echo '<br/>Success : Imported successfully for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                    } else {
                        DB::rollback();
                        Storage::append($log_file_name, 'Error : File Import failed, something wrong for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                        Storage::append($email_log_file, 'Error : File Import failed, something wrong for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                        echo '<br/>Error : File Import failed, something wrong for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                    }
                    
                } else {
                    Storage::append($log_file_name, 'Info : Total beneficiary response is not received for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                    Storage::append($email_log_file, 'Info : Total beneficiary response is not received for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                    echo '<br/>Info : Total beneficiary response is not received for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                }
            } else {
                Storage::append($log_file_name, 'Info : Data not found in DB for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                Storage::append($email_log_file, 'Info : Data not found in DB for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                echo '<br/>Info : Data not found in DB for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
            }
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            Storage::append($log_file_name, 'Exception : for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id.'  Message : ' . $e->getMessage());
            Storage::append($email_log_file, 'Exception : for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id.'  Message : ' . $e->getMessage());
            echo '<br/>Exception : for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id.'  Message : ' . $e->getMessage();
        }
    }

    public function scheduleReceiveAcknowledgementValidationLot() {
        $time_array = DB::select(DB::raw("select to_char(now(),'DDMONYYYY-HH24MISS') as datetime"));
        $var_file_name = $time_array[0]->datetime;
        $log_file_name = 'sbi/AccountValidation/ScheduleLog/log_AV_Receive_ACK_' . $var_file_name . '.txt';
        $email_log_file = 'sbi/AccountValidation/DailyLog/daily_av_receive_ack_schedule_job_file.txt';
        Storage::put($log_file_name, 'Function scheduleReceiveAcknowledgementValidationLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A"));
        Storage::put($email_log_file, 'Function scheduleReceiveAcknowledgementValidationLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A"));
        echo '<br/>Function scheduleReceiveAcknowledgementValidationLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A");
        Storage::append($log_file_name, '=====================================================');
        Storage::append($email_log_file, '=====================================================');
        echo '<br/>=====================================================';

        $data_exists = DB::table('sbi.av_lot_master')->whereNull('ack_status')->where('status', 2)->limit(20)->get();

        if (count($data_exists) > 0) {
            $f_count = 0;
            foreach ($data_exists as $item) {
                Storage::append($log_file_name, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
                Storage::append($email_log_file, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
                $file_name = $item->input_file_name;
                $scheme_id = $item->scheme_id;
                $f_count = $f_count + 1;
                Storage::append($log_file_name, '******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                Storage::append($email_log_file, '******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                echo '<br/>******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A");
                Storage::append($log_file_name, 'Receiving acknowledgement Account Validation Resposne for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                Storage::append($email_log_file, 'Receiving acknowledgement Account Validation Resposne for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                echo '<br/>Receiving acknowledgement Account Validation Resposne for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;

                Storage::append($log_file_name, 'Function receive_ack_response_validation_lot() is called');
                Storage::append($email_log_file, 'Function receive_ack_response_validation_lot() is called');
                Storage::append($log_file_name, '------------------------------------------------------');
                Storage::append($email_log_file, '------------------------------------------------------');
                echo '<br/>Function receive_ack_response_validation_lot() is called';
                echo '<br/>------------------------------------------------------';
                $this->receive_ack_response_validation_lot($file_name, $scheme_id, $log_file_name, $email_log_file);
                Storage::append($log_file_name, '------------------------------------------------------');
                Storage::append($email_log_file, '------------------------------------------------------');
                Storage::append($log_file_name, 'Function receive_ack_response_validation_lot() is end');
                Storage::append($email_log_file, 'Function receive_ack_response_validation_lot() is end');
                echo '<br/>------------------------------------------------------';
                echo '<br/>Function receive_ack_response_validation_lot() is end';
            }
        } else {
            Storage::append($log_file_name, 'No record pending for receiving acknowledgement.');
            Storage::append($email_log_file, 'No record pending for receiving acknowledgement.');
        }
        Storage::append($log_file_name, '=====================================================');
        Storage::append($email_log_file, '=====================================================');
        echo '<br/>=====================================================';
        Storage::append($log_file_name, 'Function scheduleReceiveAcknowledgementValidationLot() is completed');
        Storage::append($email_log_file, 'Function scheduleReceiveAcknowledgementValidationLot() is completed');
        echo '<br/>Function scheduleReceiveAcknowledgementValidationLot() is completed';
        Storage::append($log_file_name, 'Job completed');
        Storage::append($email_log_file, 'Job completed');
        echo '<br/>Job completed';
    }

    public function receive_ack_response_validation_lot($file_name, $scheme_id, $log_file_name, $email_log_file) {
        try {
            $scheme_id = $scheme_id;
            $file_name = $file_name;
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

                    DB::beginTransaction();
                    if ($ack_status_code == '100') {
                        // Updates in DB
                        $ack_update_status = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->where('status', 2)->update(['status' => 3, 'ack_status' => $ack_status_code]);
                        $ack_payload_update = DB::table('sbi.av_transaction_payload')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['ack_payload_at' => date('Y-m-d H:i:s'), 'ack_payload' => $av_ack_file_content, 'status' => 3]);

                        if ($ack_update_status) {
                            DB::commit();
                            // Transfer file to Picked
                            rename(storage_path('app/sbi/AccountValidation/Acknowledgement/') . '//' . $ackfile_name, storage_path('app/sbi/AccountValidation/Acknowledgement/Picked/') . '//' . $ackfile_name);
                            Storage::append($log_file_name, 'Success : Acknowledgement has been received from SBI successfully for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                            Storage::append($email_log_file, 'Success : Acknowledgement has been received from SBI successfully for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                            echo '<br/>Success : Acknowledgement has been received from SBI successfully for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                        } else {
                            DB::rollback();
                            Storage::append($log_file_name, 'Error : Acknowledgement Status update error. Please Try again later for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                            Storage::append($email_log_file, 'Error : Acknowledgement Status update error. Please Try again later for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                            echo '<br/>Error : Acknowledgement Status update error. Please Try again later for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                        }
                    } else {
                        // Updates in DB
                        $ack_update_status = DB::table('sbi.av_lot_master')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['ack_status' => $ack_status_code]);
                        $ack_payload_update = DB::table('sbi.av_transaction_payload')->where('input_file_name', $file_name)->where('scheme_id', $scheme_id)->update(['ack_payload_at' => date('Y-m-d H:i:s'), 'ack_payload' => $av_ack_file_content]);
                        if ($ack_update_status) { 
                            DB::commit();
                            // Transfer file to Picked
                            rename(storage_path('app/sbi/AccountValidation/Acknowledgement/') . '//' . $ackfile_name, storage_path('app/sbi/AccountValidation/Acknowledgement/Picked/') . '//' . $ackfile_name);
                            Storage::append($log_file_name, 'Success : Acknowledgement has been received from SBI successfully for file : ' . $file_name . ' with Remarks - ' . $ack_remarks . ' and Scheme ID : ' . $scheme_id);
                            Storage::append($email_log_file, 'Success : Acknowledgement has been received from SBI successfully for file : ' . $file_name . ' with Remarks - ' . $ack_remarks . ' and Scheme ID : ' . $scheme_id);
                            echo '<br/>Success : Acknowledgement has been received from SBI successfully for file : ' . $file_name . ' with Remarks - ' . $ack_remarks . ' and Scheme ID : ' . $scheme_id;
                        }
                        else {
                            DB::rollback();
                            Storage::append($log_file_name, 'Error : Acknowledgement Status update error. Please Try again later for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                            Storage::append($email_log_file, 'Error : Acknowledgement Status update error. Please Try again laterfor file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                            echo '<br/>Error : Acknowledgement Status update error. Please Try again laterfor file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                        }
                    }
                } else {
                    Storage::append($log_file_name, 'Info : Response is not generated in SBI server for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                    Storage::append($email_log_file, 'Info : Response is not generated in SBI server for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                    echo '<br/>Info : Response is not generated in SBI server for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
                }
            } else {
                Storage::append($log_file_name, 'Info : Acknowledgement file already came form SBI for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                Storage::append($email_log_file, 'Info : Acknowledgement file already came form SBI for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id);
                echo '<br/>Info : Acknowledgement file already came form SBI for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id;
            }
        } catch (\Exception $e) {
            DB::rollback();
            Storage::append($log_file_name, 'Exception : for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id.'  Message : ' . $e->getMessage());
            Storage::append($email_log_file, 'Exception : for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id.'  Message : ' . $e->getMessage());
            echo '<br/>Exception : for file : ' . $file_name . ' and Scheme ID : ' . $scheme_id.'  Message : ' . $e->getMessage();
        }
    }
}
