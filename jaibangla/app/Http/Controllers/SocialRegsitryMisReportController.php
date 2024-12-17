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

class SocialRegsitryMisReportController extends Controller
{
    public function srMisReport(Request $request)
    {
        $user_id = Auth::user()->id;
        $designation_id_old = Auth::user()->designation_id_old;
        if ($designation_id_old == 'Admin') {
            return view('sr_mis_report_index');
        } else {
            return redirect("/")->with('success', 'User Disabled. ');
        }
    }

    public function getSrMisReport(Request $request) 
    {
        $date = $request->date;
        $dateArr = explode("-",$date);
        $from_date = date('Y-m-d', strtotime($dateArr[0]));
        $to_date = date('Y-m-d', strtotime($dateArr[1]));
        // echo $from_date.'<br>'.$to_date; die;
        // dd($dateArr[1]);
        if ($request->ajax()) {
            $query = "SELECT 1 scheme_id,'Jai Johar' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_1_caemk_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_1_caemk_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_1_caemk_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_1_caemk_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction  
            UNION ALL
            SELECT 2 scheme_id,'WCD Manbik' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_2_c4qim_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_2_c4qim_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_2_c4qim_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_2_c4qim_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction 
            UNION ALL
            SELECT 3 scheme_id,'Toposili Bandhu(For SC)' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_3_eimq3_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_3_eimq3_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_3_eimq3_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_3_eimq3_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction 
            UNION ALL
            SELECT 5 scheme_id,'Old Aged Pension for Fisherman' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_5_ciyex_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_5_ciyex_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_5_ciyex_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_5_ciyex_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction 
            UNION ALL
            SELECT 6 scheme_id,'MSME Pension' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_6_cjgaq_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_6_cjgaq_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_6_cjgaq_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_6_cjgaq_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction 
            UNION ALL
            SELECT 7 scheme_id,'Textile Pension' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_7_ckagr_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_7_ckagr_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_7_ckagr_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_7_ckagr_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction 
            UNION ALL
            SELECT 8 scheme_id,'LPP Retainer' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_8_c1bdr_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_8_c1bdr_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_8_c1bdr_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_8_c1bdr_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction 
            UNION ALL
            SELECT 9 scheme_id,'LPP Pensioner' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_9_c1bdr_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_9_c1bdr_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_9_c1bdr_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_9_c1bdr_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction 
            UNION ALL
            SELECT 10 scheme_id,'WCD Old Aged Pension' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_10_cesri_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_10_cesri_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_10_cesri_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_10_cesri_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction 
            UNION ALL
            SELECT 11 scheme_id,'WCD Window Pension' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_11_c36ge_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_11_c36ge_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_11_c36ge_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_11_c36ge_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction 
            UNION ALL
            SELECT 13 scheme_id,'Old Aged Pension for Farmer' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_13_ct4pv_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_13_ct4pv_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_13_ct4pv_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_13_ct4pv_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction 
            UNION ALL
            SELECT 17 scheme_id,'State Welfare Scheme for Purohits' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_17_cja07_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_17_cja07_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_17_cja07_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_17_cja07_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction 
            UNION ALL
            SELECT 19 scheme_id,'Legacy Old Aged Pennsion for ST' AS scheme_name,
            (SELECT COUNT(1) FROM social_registry.beneficiary_19_jboapst_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_mapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_19_jboapst_non_verified_data WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS approved_unmapped_beneficiary, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_19_jboapst_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS mapped_transaction, 
            (SELECT COUNT(1) FROM social_registry.beneficiary_19_jboapst_non_verified_transaction WHERE kafka_updation_timestamp::date between '".$from_date."' and '".$to_date."') AS unmapped_transaction";
            $getMisReport = DB::connection('pgsql_mis')->select($query);
            return datatables()->of($getMisReport)
            ->addIndexColumn()
            ->make(true);
        }
        
    }
}
