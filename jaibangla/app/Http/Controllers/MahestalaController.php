<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Carbon;
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
use App\BankDetails;
use App\BenEntry;
use App\Helpers\AuthChecker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\DataInsert;
use App\Helpers\DupCheck;
use Hamcrest\Core\IsSame;

class MahestalaController extends Controller
{



    public function mahestala1(Request $request)
    {
        $app_list = DB::table('bank_info_change_log.mahestala_ps_application_id')->get();
        foreach ($app_list as $list) {
            // $file_name = '';
            // $file_name = 'Applicant_Details_' . $list->application_id;
            // $file_path = public_path() . '/storage/app/Mahestala/' . $file_name . '.pdf';
            // if (fnmatch("$file_name*", $file_name)) {
            //     echo 'true';
            // }


            print_r(glob(public_path() . "/storage/app/Mahestala/*.pdf"));

        }

        $filereg = '/bfile[1-9]?\d+\.pdf/i';
        $pathstr = '/storage/app/Mahestala/';

        if (is_dir($pathstr)) {
            if ($dir = opendir($pathstr)) {
                $found = false;

                while (($file = readdir($dir)) !== false ) {
                    // if (preg_match($filereg, $file) > 0) {
                    echo $file . '<br />';
                    // $found = true;
                    // }
                }

                closedir($dir);
            }
        }

    }
    public function mahestala(Request $request)
    {
        $app_list = DB::table('bank_info_change_log.mahestala_ps_application_id')->get();
        if ($app_list->isEmpty()) {
            return response()->json(['message' => 'No applications found'], 404);
        }
    
        $files = []; 
    
        foreach ($app_list as $list) {
            $base_file_name = 'Applicant_Details_' . $list->application_id . '_';
            $file_pattern =storage_path('app/Mahestala/' . $base_file_name . '*') . '.pdf';
            $matching_files = glob($file_pattern); 
    
            if (!empty($matching_files)) {
                if(count($matching_files) > 1) {
                    dump("Duplicate files found for application ID: {$list->application_id}");
                  
                }
              
                $files = array_merge($files, $matching_files);
            } else {
                // Log or handle missing file scenario
                dump("No files found for application ID: {$list->application_id}");
            }
        }

        // dump($files);
    
        if (!empty($files)) {
            return response()->json(['files' => $files], 200);
        } else {
            return response()->json(['message' => 'No files found'], 404);
        }
    }
    

}
