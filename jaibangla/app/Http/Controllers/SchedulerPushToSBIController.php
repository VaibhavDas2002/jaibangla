<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scheme;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\SBITransactionLot;
use App\SBITransactionLotDetails;
use App\SBITransactionPayLoad;
use App\Helpers\Helper;

class SchedulerPushToSBIController extends Controller
{
    public $sbi_sftp_server;
    public function __construct()
    {
        set_time_limit(0);
        date_default_timezone_set('Asia/Kolkata');
        $sbi_prod_server = Helper::getSBISftpServer();
        $this->sbi_sftp_server = $sbi_prod_server;
    }
    public function scheduleReceiveResponseSBIPaymentLot()
    {
        $time_array = DB::select(DB::raw("select to_char(now(),'DDMONYYYY-HH24MISS') as datetime"));
        $var_file_name = $time_array[0]->datetime;
        $log_file_name = 'sbi/ePay/ScheduleLog/log_SBI_Payment_Response_' . $var_file_name . '.txt';
        $email_log_file = 'sbi/ePay/DailyLog/daily_sbi_payment_reposne_schedule_job_file.txt';
        Storage::put($log_file_name, 'Function scheduleReceiveResponseSBIPaymentLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A"));
        Storage::put($email_log_file, 'Function scheduleReceiveResponseSBIPaymentLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A"));
        echo '<br/>Function scheduleReceiveResponseSBIPaymentLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A");
        Storage::append($log_file_name, '=====================================================');
        Storage::append($email_log_file, '=====================================================');
        echo '<br/>=====================================================';

        $limit = (date('d') > 15) ? 100 : 100;
        $data_exists = SBITransactionLot::where('lot_status', 3)->where('ack_status_code', '000')
            // ->whereIn('scheme_id', [10,11,13,2])
            // ->where('lot_month', 'March')->where('lot_year', '2023-2024')
            ->orderBy('pushed_at', 'ASC')->limit($limit)->get();

        if (count($data_exists) > 0) {
            $f_count = 0;
            foreach ($data_exists as $item) {
                Storage::append($log_file_name, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
                Storage::append($email_log_file, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
                $debit_ref = $item->debit_reference;
                $scheme_id = $item->scheme_id;
                $lot_no = $item->lot_no;
                $f_count = $f_count + 1;
                Storage::append($log_file_name, '******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                Storage::append($email_log_file, '******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                echo '<br/>******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A");
                Storage::append($log_file_name, 'Receiving Payment Resposne for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' and Lot No :' . $lot_no);
                Storage::append($email_log_file, 'Receiving Payment Resposne for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' and Lot No :' . $lot_no);
                echo '<br/>Receiving Payment Resposne for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' and Lot No :' . $lot_no;

                Storage::append($log_file_name, 'Function receive_response_sbi_payment_lot() is called');
                Storage::append($email_log_file, 'Function receive_response_sbi_payment_lot() is called');
                Storage::append($log_file_name, '------------------------------------------------------');
                Storage::append($email_log_file, '------------------------------------------------------');
                echo '<br/>Function receive_response_sbi_payment_lot() is called';
                echo '<br/>------------------------------------------------------';
                $this->receive_response_sbi_payment_lot($debit_ref, $scheme_id, $lot_no, $log_file_name, $email_log_file);
                Storage::append($log_file_name, '------------------------------------------------------');
                Storage::append($email_log_file, '------------------------------------------------------');
                Storage::append($log_file_name, 'Function receive_response_sbi_payment_lot() is end');
                Storage::append($email_log_file, 'Function receive_response_sbi_payment_lot() is end');
                echo '<br/>------------------------------------------------------';
                echo '<br/>Function receive_response_sbi_payment_lot() is end';
            }
        } else {
            Storage::append($log_file_name, 'Info : No record pending for receive resposne.');
            Storage::append($email_log_file, 'Info : No record pending for receive resposne.');
            echo '<br/>Info : No record pending for receive resposne.';
        }
        Storage::append($log_file_name, '=====================================================');
        Storage::append($email_log_file, '=====================================================');
        echo '<br/>=====================================================';
        Storage::append($log_file_name, 'Function scheduleReceiveResponseSBIPaymentLot() is completed' . ' Date : ' . date("l jS \of F Y h:i:s A"));
        Storage::append($email_log_file, 'Function scheduleReceiveResponseSBIPaymentLot() is completed' . ' Date : ' . date("l jS \of F Y h:i:s A"));
        echo '<br/>Function scheduleReceiveResponseSBIPaymentLot() is completed' . ' Date : ' . date("l jS \of F Y h:i:s A");
        Storage::append($log_file_name, 'Job completed');
        Storage::append($email_log_file, 'Job completed');
        echo '<br/>Job completed';
    }

    public function receive_response_sbi_payment_lot($debit_ref, $scheme_id, $lot_no, $log_file_name, $email_log_file)
    {
        try {
            $debit_ref = $debit_ref;
            $scheme_id = $scheme_id;
            $lot_no = $lot_no;

            $respfile_name = $debit_ref . '_RESP.xml';
            $data_exists = SBITransactionLot::where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->where('debit_reference', $debit_ref)->where('lot_status', 3)->where('ack_status_code', '000')->first();
            if ($data_exists->ack_status_code == '000' && $data_exists->lot_status == 3) {
                $exists = Storage::disk($this->sbi_sftp_server)->exists('ePay/Response/' . $respfile_name);  ///////uncomment in production
                if ($exists) {
                    Storage::append($log_file_name, 'File exists in the SBI server for file : ' . $respfile_name . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                    Storage::append($email_log_file, 'File exists in the SBI server for file : ' . $respfile_name . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                    echo '<br/>File exists in the SBI server for file : ' . $respfile_name . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;

                    $remote_file = Storage::disk($this->sbi_sftp_server)->get('ePay/Response/' . $respfile_name);  //// uncomment in production
                    Storage::put('sbi/ePay/Response/' . $respfile_name, $remote_file);  //// uncomment in production
                    if (file_exists(storage_path('app/sbi/ePay/Response/') . '//' . $respfile_name)) {
                        Storage::append($log_file_name, 'File is fetched successfully in Jai Bangla server for file : ' . $respfile_name . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                        Storage::append($email_log_file, 'File is fetched successfully in Jai Bangla server for file : ' . $respfile_name . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                        echo '<br/>File is fetched successfully in Jai Bangla server for file : ' . $respfile_name . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;

                        $av_resp_file_content = file_get_contents(storage_path('app/sbi/ePay/Response/' . $respfile_name));

                        DB::beginTransaction();
                        $ack_payload_update = SBITransactionPayLoad::where('debit_reference', $debit_ref)->where('scheme_id', $scheme_id)->update(['received_payload' => $av_resp_file_content]);
                        $update_payment_fun = DB::connection('pgsql')->select("SELECT sbi.update_payment_status('" . $debit_ref . "');");
                        $update_payment_status_res = $update_payment_fun[0]->update_payment_status;

                        if ($update_payment_status_res == 0 && $ack_payload_update == 1) {
                            DB::commit();
                            // Transfer file to Picked
                            rename(storage_path('app/sbi/ePay/Response/') . '//' . $respfile_name, storage_path('app/sbi/ePay/Response/Picked/') . '//' . $respfile_name);

                            Storage::append($log_file_name, 'Success : Response has been received from SBI successfully. for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                            Storage::append($email_log_file, 'Success : Response has been received from SBI successfully. for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                            echo '<br/>Success : Response has been received from SBI successfully. for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;

                            $results = SBITransactionLotDetails::where('debit_reference', $debit_ref)->where('scheme_id', $scheme_id)
                                ->selectRaw("SUM(CASE WHEN SUBSTR(status_code, 1, 1) = 'S' THEN 1 ELSE 0 END) as scnt")
                                ->selectRaw("SUM(CASE WHEN SUBSTR(status_code, 1, 1) = 'E' THEN 1 ELSE 0 END) as ercnt")
                                ->first();

                            // Accessing the results
                            $scnt = $results->scnt;
                            $ercnt = $results->ercnt;
                            Storage::append($log_file_name, 'Total Success Resposne : '.$scnt.' | Total Failed Resposne : '. $ercnt);
                            Storage::append($email_log_file, 'Total Success Resposne : '.$scnt.' | Total Failed Resposne : '. $ercnt);
                            echo '<br/>Total Success Resposne : '.$scnt.' | Total Failed Resposne : '. $ercnt;
                        } else {
                            DB::rollback();
                            Storage::append($log_file_name, 'Error : Response Status update error for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                            Storage::append($email_log_file, 'Error : Response Status update error for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                            echo '<br/>Error : Response Status update error for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;
                        }
                    } else {
                        Storage::append($log_file_name, 'Info : File not fetched from SBI server for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                        Storage::append($email_log_file, 'Info : File not fetched from SBI server for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                        echo '<br/>Info : File not fetched from SBI server for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;
                    }
                } else {
                    Storage::append($log_file_name, 'Info : Response file is not generated in SBI server for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                    Storage::append($email_log_file, 'Info : Response file is not generated in SBI server for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                    echo '<br/>Info : Response file is not generated in SBI server for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;
                }
            } else {
                Storage::append($log_file_name, 'Info : Response file already came form SBI for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                Storage::append($email_log_file, 'Info : Response file already came form SBI for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                echo '<br/>Info : Response file already came form SBI for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;
            }
        } catch (\Exception $e) {
            DB::rollback();
            Storage::append($log_file_name, 'Exception : for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no . '  Message : ' . $e->getMessage());
            Storage::append($email_log_file, 'Exception : for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no . '  Message : ' . $e->getMessage());
            echo '<br/>Exception : for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no . '  Message : ' . $e->getMessage();
        }
    }

    public function scheduleImportResponseSBIPaymentLot()
    {
        $time_array = DB::select(DB::raw("select to_char(now(),'DDMONYYYY-HH24MISS') as datetime"));
        $var_file_name = $time_array[0]->datetime;
        $log_file_name = 'sbi/ePay/ScheduleLog/log_SBI_Import_Response_' . $var_file_name . '.txt';
        $email_log_file = 'sbi/ePay/DailyLog/daily_sbi_import_reposne_schedule_job_file.txt';
        Storage::put($log_file_name, 'Function scheduleImportResponseSBIPaymentLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A"));
        Storage::put($email_log_file, 'Function scheduleImportResponseSBIPaymentLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A"));
        echo '<br/>Function scheduleImportResponseSBIPaymentLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A");
        Storage::append($log_file_name, '=====================================================');
        Storage::append($email_log_file, '=====================================================');
        echo '<br/>=====================================================';

        $limit = (date('d') > 15) ? 100 : 100;
        $data_exists = SBITransactionLot::where('lot_status', 4)->limit($limit)->get();

        if (count($data_exists) > 0) {
            $f_count = 0;
            foreach ($data_exists as $item) {
                Storage::append($log_file_name, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
                Storage::append($email_log_file, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
                $debit_ref = $item->debit_reference;
                $scheme_id = $item->scheme_id;
                $lot_no = $item->lot_no;
                $f_count = $f_count + 1;
                Storage::append($log_file_name, '******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                Storage::append($email_log_file, '******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                echo '<br/>******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A");
                Storage::append($log_file_name, 'Importing Payment Resposne for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                Storage::append($email_log_file, 'Importing Payment Resposne for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                echo '<br/>Importing Payment Resposne for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;

                Storage::append($log_file_name, 'Function import_response_sbi_payment_lot() is called');
                Storage::append($email_log_file, 'Function import_response_sbi_payment_lot() is called');
                Storage::append($log_file_name, '------------------------------------------------------');
                Storage::append($email_log_file, '------------------------------------------------------');
                echo '<br/>Function import_response_sbi_payment_lot() is called';
                echo '<br/>------------------------------------------------------';
                $this->import_response_sbi_payment_lot($debit_ref, $scheme_id, $lot_no, $log_file_name, $email_log_file);
                Storage::append($log_file_name, '------------------------------------------------------');
                Storage::append($email_log_file, '------------------------------------------------------');
                Storage::append($log_file_name, 'Function import_response_sbi_payment_lot() is end');
                Storage::append($email_log_file, 'Function import_response_sbi_payment_lot() is end');
                echo '<br/>------------------------------------------------------';
                echo '<br/>Function import_response_sbi_payment_lot() is end';
            }
        } else {
            Storage::append($log_file_name, 'No record pending for import resposne.');
            Storage::append($email_log_file, 'No record pending for import resposne.');
            echo '<br/>No record pending for import resposne.';
        }
        Storage::append($log_file_name, '=====================================================');
        Storage::append($email_log_file, '=====================================================');
        echo '<br/>=====================================================';
        Storage::append($log_file_name, 'Function scheduleImportResponseSBIPaymentLot() is completed');
        Storage::append($email_log_file, 'Function scheduleImportResponseSBIPaymentLot() is completed');
        echo '<br/>Function scheduleImportResponseSBIPaymentLot() is completed';
        Storage::append($log_file_name, 'Job completed');
        Storage::append($email_log_file, 'Job completed');
        echo '<br/>Job completed';
    }

    public function import_response_sbi_payment_lot($debit_ref, $scheme_id, $lot_no, $log_file_name, $email_log_file)
    {
        try {
            $debit_ref = $debit_ref;
            $scheme_id = $scheme_id;
            $lot_no = $lot_no;
            $data_exists = SBITransactionLot::where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->where('debit_reference', $debit_ref)->where('lot_status', 4)->get();
            if (count($data_exists) > 0) {
                DB::beginTransaction();
                $import_fun = DB::connection('pgsql')->select("SELECT sbi.import_sbi_lot_compile_standard(" . $scheme_id . ",'" . $lot_no . "','" . $debit_ref . "');");
                $import_res = $import_fun[0]->import_sbi_lot_compile_standard;
                $import_summary_fun = DB::connection('pgsql')->select("SELECT sbi.update_lot_master_payment_status_summary(" . $scheme_id . ",'" . $lot_no . "');");
                $import_summary_res = $import_summary_fun[0]->update_lot_master_payment_status_summary;

                if ($import_res == 1 && $import_summary_res == 1) {
                    DB::commit();
                    Storage::append($log_file_name, 'Success : Payment status imported successfully for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                    Storage::append($email_log_file, 'Success : Payment status imported successfully for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                    echo '<br/>Success : Payment status imported successfully for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;
                } else {
                    DB::rollback();
                    Storage::append($log_file_name, 'Error : Response Status update error for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                    Storage::append($email_log_file, 'Error : Response Status update error for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                    echo '<br/>Error : Response Status update error for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;
                }
            } else {
                Storage::append($log_file_name, 'Info : Import Payment Response is already completed for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                Storage::append($email_log_file, 'Info : Import Payment Response is already completed for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                echo '<br/>Info : Import Payment Response is already completed for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;
            }
        } catch (\Exception $e) {
            DB::rollback();
            Storage::append($log_file_name, 'Exception : for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no . '  Message : ' . $e->getMessage());
            Storage::append($email_log_file, 'Exception : for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no . '  Message : ' . $e->getMessage());
            echo '<br/>Exception : for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no . '  Message : ' . $e->getMessage();
        }
    }

    public function scheduleReceiveAcknowledgementSBIPaymentLot()
    {
        $time_array = DB::select(DB::raw("select to_char(now(),'DDMONYYYY-HH24MISS') as datetime"));
        $var_file_name = $time_array[0]->datetime;
        $log_file_name = 'sbi/ePay/ScheduleLog/log_SBI_Receive_ACK_' . $var_file_name . '.txt';
        $email_log_file = 'sbi/ePay/DailyLog/daily_sbi_receive_ack_schedule_job_file.txt';
        Storage::put($log_file_name, 'Function scheduleReceiveAcknowledgementSBIPaymentLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A"));
        Storage::put($email_log_file, 'Function scheduleReceiveAcknowledgementSBIPaymentLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A"));
        echo '<br/>Function scheduleReceiveAcknowledgementSBIPaymentLot() is called on ' . $var_file_name . ' Date : ' . date("l jS \of F Y h:i:s A");
        Storage::append($log_file_name, '=====================================================');
        Storage::append($email_log_file, '=====================================================');
        echo '<br/>=====================================================';

        $data_exists = SBITransactionLot::where('lot_status', 2)->limit(100)->get();

        if (count($data_exists) > 0) {
            $f_count = 0;
            foreach ($data_exists as $item) {
                Storage::append($log_file_name, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
                Storage::append($email_log_file, '+++++++++++++++++++++++++++++++++++++++++++++++++++++');
                $debit_ref = $item->debit_reference;
                $scheme_id = $item->scheme_id;
                $lot_no = $item->lot_no;
                $f_count = $f_count + 1;
                Storage::append($log_file_name, '******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                Storage::append($email_log_file, '******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A"));
                echo '<br/>******************** File No. ' . $f_count . ' ********************' . ' Date : ' . date("l jS \of F Y h:i:s A");
                Storage::append($log_file_name, 'Receiving acknowledgement Resposne for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                Storage::append($email_log_file, 'Receiving acknowledgement Resposne for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                echo '<br/>Receiving acknowledgement Resposne for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;

                Storage::append($log_file_name, 'Function receive_ack_sbi_payment_lot() is called');
                Storage::append($email_log_file, 'Function receive_ack_sbi_payment_lot() is called');
                Storage::append($log_file_name, '------------------------------------------------------');
                Storage::append($email_log_file, '------------------------------------------------------');
                echo '<br/>Function receive_ack_sbi_payment_lot() is called';
                echo '<br/>------------------------------------------------------';
                $this->receive_ack_sbi_payment_lot($debit_ref, $scheme_id, $lot_no, $log_file_name, $email_log_file);
                Storage::append($log_file_name, '------------------------------------------------------');
                Storage::append($email_log_file, '------------------------------------------------------');
                Storage::append($log_file_name, 'Function receive_ack_sbi_payment_lot() is end');
                Storage::append($email_log_file, 'Function receive_ack_sbi_payment_lot() is end');
                echo '<br/>------------------------------------------------------';
                echo '<br/>Function receive_ack_sbi_payment_lot() is end';
            }
        } else {
            Storage::append($log_file_name, 'No record pending for receive acknowledgement.');
            Storage::append($email_log_file, 'No record pending for receive acknowledgement.');
            echo '<br/>No record pending for receive acknowledgement.';
        }
        Storage::append($log_file_name, '=====================================================');
        Storage::append($email_log_file, '=====================================================');
        echo '<br/>=====================================================';
        Storage::append($log_file_name, 'Function scheduleReceiveAcknowledgementSBIPaymentLot() is completed');
        Storage::append($email_log_file, 'Function scheduleReceiveAcknowledgementSBIPaymentLot() is completed');
        echo '<br/>Function scheduleReceiveAcknowledgementSBIPaymentLot() is completed';
        Storage::append($log_file_name, 'Job completed');
        Storage::append($email_log_file, 'Job completed');
        echo '<br/>Job completed';
    }

    public function receive_ack_sbi_payment_lot($debit_ref, $scheme_id, $lot_no, $log_file_name, $email_log_file)
    {
        try {
            $debit_ref = $debit_ref;
            $scheme_id = $scheme_id;
            $lot_no = $lot_no;
            $ackfile_name = $debit_ref . '_ACK.xml';
            $data_exists = SBITransactionLot::select('ack_status_code')->where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->where('debit_reference', $debit_ref)->where('lot_status', 2)->whereNull('ack_status_code')->first();
            if (is_null($data_exists->ack_status_code)) {
                $exists = Storage::disk($this->sbi_sftp_server)->exists('ePay/Acknowledgement/' . $ackfile_name);  ///////uncomment in production
                if ($exists) {
                    $remote_file = Storage::disk($this->sbi_sftp_server)->get('ePay/Acknowledgement/' . $ackfile_name);  //// uncomment in production
                    Storage::put('sbi/ePay/Acknowledgement/' . $ackfile_name, $remote_file);  //// uncomment in production

                    $remote_xml_file = simplexml_load_string($remote_file);
                    $ack_remarks = $remote_xml_file->DEBIT_ACCOUNT['ACK_REMARKS'];
                    $file_ack_status_code = $remote_xml_file->DEBIT_ACCOUNT['ACK_STATUS_CODE'];
                    $av_ack_file_content = file_get_contents(storage_path('app/sbi/ePay/Acknowledgement/' . $ackfile_name));
                    // dump($remote_xml_file, $file_ack_status_code, $ack_remarks); die;
                    DB::beginTransaction();
                    if ($file_ack_status_code == '000') {
                        // Updates in DB
                        $ack_update_status = SBITransactionLot::where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->where('debit_reference', $debit_ref)->where('lot_status', 2)->update(['lot_status' => 3, 'ack_status_code' => $file_ack_status_code]);
                        $ack_payload_update = SBITransactionPayLoad::where('debit_reference', $debit_ref)->where('scheme_id', $scheme_id)->update(['updated_at' => date('Y-m-d H:i:s'), 'ack_payload' => $av_ack_file_content, 'status' => 3]);
                    } else {
                        // Updates in DB
                        $ack_update_status = SBITransactionLot::where('debit_reference', $debit_ref)->where('scheme_id', $scheme_id)->update(['ack_status_code' => $file_ack_status_code]);
                        $ack_payload_update = SBITransactionPayLoad::where('debit_reference', $debit_ref)->where('scheme_id', $scheme_id)->update(['updated_at' => date('Y-m-d H:i:s'), 'ack_payload' => $av_ack_file_content]);
                    }

                    if ($ack_update_status && $ack_payload_update) {
                        DB::commit();
                        // Transfer file to Picked
                        rename(storage_path('app/sbi/ePay/Acknowledgement/') . '//' . $ackfile_name, storage_path('app/sbi/ePay/Acknowledgement/Picked/') . '//' . $ackfile_name);
                        if ($file_ack_status_code == '000') {
                            Storage::append($log_file_name, 'Success : Acknowledgement has been received from SBI successfully for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                            Storage::append($email_log_file, 'Success : Acknowledgement has been received from SBI successfully for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                            echo '<br/>Success : Acknowledgement has been received from SBI successfully for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;
                        } else {
                            Storage::append($log_file_name, 'Error : Acknowledgement error from SBI with Remarks - ' . $ack_remarks . ' for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                            Storage::append($email_log_file, 'Error : Acknowledgement error from SBI with Remarks - ' . $ack_remarks . ' for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                            echo '<br/>Error :Acknowledgement error from SBI with Remarks - ' . $ack_remarks . ' for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;
                        }
                    } else {
                        DB::rollback();
                        Storage::append($log_file_name, 'Error : Acknowledgement Status update error. Please Try again later. for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                        Storage::append($email_log_file, 'Error : Acknowledgement Status update error. Please Try again later. for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                        echo '<br/>Error : Acknowledgement Status update error. Please Try again later. for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;
                    }
                } else {
                    Storage::append($log_file_name, 'Info : Acknowledgement file is not generated in SBI server for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                    Storage::append($email_log_file, 'Info : Acknowledgement file is not generated in SBI server for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                    echo '<br/>Info : Acknowledgement file is not generated in SBI server for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;
                }
            } else {
                Storage::append($log_file_name, 'Info : Acknowledgement file already came form SBI for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                Storage::append($email_log_file, 'Info : Acknowledgement file already came form SBI for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no);
                echo '<br/>Info : Acknowledgement file already came form SBI for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no;
            }
        } catch (\Exception $e) {
            DB::rollback();
            Storage::append($log_file_name, 'Exception : for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no . '  Message : ' . $e->getMessage());
            Storage::append($email_log_file, 'Exception : for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no . '  Message : ' . $e->getMessage());
            echo '<br/>Exception : for file : ' . $debit_ref . ' and Scheme ID : ' . $scheme_id . ' Lot No : ' . $lot_no . '  Message : ' . $e->getMessage();
        }
    }
}
