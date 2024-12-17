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

class OapPaymentSuspendedController extends Controller
{
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
    }

    public function index()
    {
        $designation_id_old = Auth::user()->designation_id_old;
        if ($designation_id_old == 'HOD' || $designation_id_old == 'Admin') {
            $is_active = 1;
        } else{
            $is_active = 0;
        }
        $distList = District::orderBy('district_name')->get();
        if ($is_active == 1) {
            return view('payment_suspended_oap/linelisting_payment_suspended_oap', [
                'districts' => $distList
            ]);
        } else{
            return redirect("/")->with('success', 'UnAuthorized');
        }
    }

    public function paymentSuspendedList(Request $request)
    {
        if ($request->ajax()) 
        {
            $distCode = $request->district;

            $query = $this->getQueryResult($distCode);
            $result = DB::connection('pgsql_mis')->select($query);
            return datatables()->of($result)
            ->make(true);
        }
    }

    public function paymentSuspendedExcel(Request $request)
    {
        $distCode = $request->district;
        $user_msg = 'Due To Payment Suspended with Widow Pension';
        // dd($crossSchemeType);
        $query = $this->getQueryResult($distCode);
        $result = DB::connection('pgsql_mis')->select($query);
        $excelarr[] = array(
            'Application ID', 'Name', 'Block/Municipality', 'GP/Ward', 'Account No.'
        );
        foreach ($result as $arr) {
            $excelarr[] = array(
                
                'Application ID' => trim($arr->application_id),
                'Name' => trim($arr->fullname),
                'Block/Municipality' => trim($arr->block_ulb_name),
                'GP/Ward' => trim($arr->gp_ward_name),
                'Account No.' => trim($arr->bank_code),
            );
        }
        $file_name = $user_msg .' '.  date('d/m/Y');
        Excel::create($file_name, function ($excel) use ($excelarr) {
            $excel->setTitle('Bank Duplicate List');
            $excel->sheet('Bank Duplicate List', function ($sheet) use ($excelarr) {
                $sheet->fromArray($excelarr, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    private function getQueryResult($distCode)
    {
        if ($distCode) {
            $distCon = " AND created_by_dist_code =".$distCode;
        } else{
            $distCon = "";
        }

        $data = "SELECT 
        id, TRIM(regexp_replace((regexp_replace(COALESCE(REPLACE(COALESCE(ben_fname,'')||' '||COALESCE(ben_mname,'')||' '||COALESCE(ben_lname,''),CHR(160),''),''), '\r|\n|', '', 'g')), '\s+', ' ', 'g')) AS fullname, TRIM(bank_code) bank_code, TRIM(block_ulb_name) AS block_ulb_name, TRIM(gp_ward_name) AS gp_ward_name
        FROM pension.beneficiaries WHERE dup_bank_wp = 1 AND LENGTH(bank_code) > 1". $distCon . "AND scheme_id = 10";
        return $data;
    }
}
