<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\UrbanBody;
use App\SubDistrict;

use App\DocumentType;
use App\SchemeDocMap;
//Dynamic Doc End
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Config;
use App\SchemeCapacity;
use App\Scheme;
use Validator;
use Carbon\Carbon;
use App\BankDetails;
use App\Helpers\Helper;
use App\DsPhase;
use App\PensionPurohitMonthlyICAD;
use App\AcceptRejectInfo;
use App\BeneficiaryDupBlank;
use App\District;
use App\BenDocsPurohitMonthlyICAD;
use App\Configduty;
use App\Traits\TraitLifeCertificateValidate;
use Maatwebsite\Excel\Excel;
use App\Helpers\AuthChecker;

class BioAuthLifeCertController extends Controller
{
    use TraitLifeCertificateValidate;
    public function __construct()
    {
    }
    // For Cron Life cetificate 
    public function cron(Request $request)
    {
        try {
        ini_set('max_execution_time', 20);
        // $scheme_id = $this->scheme_id;
        //$user_id = AuthChecker::getUserId();
        $scheme_id = $request->scheme_id;
        $district_code = $request->dist_code;
        $is_faulty = 0;
        $limit = $request->limit;
        $ip_address = request()->ip();
        if (empty($scheme_id)) {
            return response()->json([
                'status' => 400,
                'errors' => 'Scheme is required',
            ]);
        }
        if (empty($district_code)) {
            return response()->json([
                'status' => 400,
                'errors' => 'District is required',
            ]);
        }
        if (empty($limit)) {
            return response()->json([
                'status' => 400,
                'errors' => 'Limit is required',
            ]);
        }

        $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
        if (!empty($scheme_obj->short_code)) {
            $schema = $scheme_obj->short_code;
        } else {
            $schema = "pension";
        }

        $rows = DB::table($schema . '.beneficiaries')
            ->select('ben_fname', 'ben_mname', 'ben_lname', 'id', 'aadhar_no', 'created_by_dist_code', 'created_by_local_body_code', 'scheme_id')
            ->whereNull('life_certificate_checked')->whereNull('life_certificate_pass')
            ->where('created_by_dist_code', $district_code)->whereRaw(" (no_aadhar=0 or no_aadhar is null) ")
            ->whereIn('id', [6042122])
            ->limit($limit)->get();
            // dd($rows);
        if (count($rows) > 0) {
            $i=0;
            foreach ($rows as $row) {
                $c_time = date('Y-m-d H:i:s', time());
                $ben_fname = trim($row->ben_fname);
                $ben_mname = trim($row->ben_mname);
                $ben_lname = trim($row->ben_lname);
                $ben_fullname = $ben_fname;
                //dd($ben_fullname);
                if (!empty($ben_mname)) {
                    $ben_fullname = $ben_fullname . $ben_mname;
                }
                if (!empty($ben_lname)) {
                    $ben_fullname = $ben_fullname . $ben_lname;
                }
                $ben_fullname = str_replace(' ', '', $ben_fullname);
                $insert_arr['api_hit_time'] = date('Y-m-d H:i:s', time());
                $beneficiary_id = $row->id;
                $aadhar_no = trim($row->aadhar_no);
                $distCode = $row->created_by_dist_code;
                $blockCode = $row->created_by_local_body_code;
                $user_id = 1;
                try {
                    $this->bioauthcheckInsert($distCode, $beneficiary_id, $scheme_id, $ben_fullname, $request->ip(), trim($request->aadhar_no), $blockCode, $user_id);
                    $i++;
                } catch (\Exception $e) {
                    $inputMain['life_cert_checked_api_failed'] = -1;
                    $upadated_main = DB::table($schema . '.beneficiaries')
                        ->where([
                            'id' => $beneficiary_id, 'created_by_local_body_code' => $blockCode,
                            'created_by_dist_code' => $distCode
                        ])->update($inputMain);
                }
            }
            return response()->json([
                'status' => 200,
                'message' => 'Total - ' . $i . ' Applications Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => 200,
                'errors' => 'No record found',
            ]);
        }
    } catch (\Exception $e) {
        dd($e);
    }
    }
}
