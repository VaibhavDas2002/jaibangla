<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\programmeHeadMaster;
use App\majorProgammeHeadMaster;
use App\nhm_employee_details;
use App\designationMaster;
use App\nhm_service_category;
use App\NHMEmployee;
use App\Configduty;
use App\District;
use App\nhm_posting_level;
use App\nhm_level_place;
use App\nhm_health_facility;
use App\UrbanBody;
use App\SubDistrict;
use App\PensionOAPWCD;
//Dynamic Doc
use App\BenDocsOAPWCD;
use App\BenDocsArcOAPWCD;
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
use App\Helpers\DupCheck;
use App\DsPhase;
use App\BlkUrbanlEntryMapping;
use App\OAPMobileUnique;
use App\OAPAdhaarUnique;
use App\OAPBankUnique;
use App\AcceptRejectInfo;
use App\BeneficiaryDupBlank;
use App\BenDocs;
use App\Traits\TraitCasteCertificateValidate;
use App\Traits\TraitLifeCertificateValidate;
use App\Traits\TraitAadharValidate;
use Illuminate\Support\Facades\Session;
class DupcheckController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        date_default_timezone_set('Asia/Kolkata');
    }
    public function index(){
        try {
            $user_id = Auth::user()->id;
            $designation_id_old = Auth::user()->designation_id_old;
            if($designation_id_old == 'Admin'){
                $scheme_id=10;
                $bank_code ='85785785';
                $bankDupCheck =DupCheck::geDupCheck($scheme_id,$bank_code);
            }
        } catch (\Exception $e) {
            dd($e);
        }
       
    }
}
