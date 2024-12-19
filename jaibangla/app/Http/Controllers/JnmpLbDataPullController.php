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
use App\Helpers\AuthChecker;

class JnmpLbDataPullController extends Controller
{
    public function index()
    {
        $user_id = AuthChecker::getUserId();
        $designation_id_old = Auth::user()->designation_id_old;
        $schemes = Scheme::where('is_active', 1)->get();
        if ($designation_id_old == 'Admin') {
            return view('jnmp_data_pull_to_jb', ['schemes' => $schemes]);
        } else{
            return redirect("/")->with('success', 'User disabled.');
        }
    }
    public function dataPullLb(Request $request)
    {
        $response = [];
        $statusCode = 200;

        DB::connection('pgsql')->beginTransaction();
        DB::connection('pgsql_lb_mainwrite')->beginTransaction();
        try {
           
            $limit = $request->limit;
            // dd($limit);
            $jnmpInsertArray = array();
            $ir = 0;
            $totalData = DB::connection('pgsql_lb_mainwrite')->table('jnmp.jnmp_data')->where('migrated_to_jb', '=', 0)->count();
            // dd($totalData);
            $JnmpDataPull = DB::connection('pgsql_lb_mainwrite')->table('jnmp.jnmp_data')->where('migrated_to_jb', '=', 0)->limit($limit)->get();
            // dd($JnmpDataPull);
            foreach ($JnmpDataPull as $key) {
                $jnmpInsertArray[$ir]['slno'] = $key->slno;
                $jnmpInsertArray[$ir]['applicationid'] = $key->applicationid;
                $jnmpInsertArray[$ir]['genderdesc'] = $key->genderdesc;
                $jnmpInsertArray[$ir]['deceased_agetypedesc'] = $key->deceased_agetypedesc;
                $jnmpInsertArray[$ir]['deceased_age'] = $key->deceased_age;
                $jnmpInsertArray[$ir]['deceased_firstname'] = $key->deceased_firstname;
                $jnmpInsertArray[$ir]['deceased_middlename'] = $key->deceased_middlename;
                $jnmpInsertArray[$ir]['deceased_lastname'] = $key->deceased_lastname;
                $jnmpInsertArray[$ir]['deceasedfullname'] = $key->deceasedfullname;
                $jnmpInsertArray[$ir]['deceased_idprooftyp'] = $key->deceased_idprooftyp;
                $jnmpInsertArray[$ir]['deceased_idprooftypname'] = $key->deceased_idprooftypname;
                $jnmpInsertArray[$ir]['deceasedkhadyosathicategoryid'] = $key->deceasedkhadyosathicategoryid;
                $jnmpInsertArray[$ir]['deceasedkhadyosathicatdesc'] = $key->deceasedkhadyosathicatdesc;
                $jnmpInsertArray[$ir]['deceased_idproofnumber'] = $key->deceased_idproofnumber;
                $jnmpInsertArray[$ir]['present_districtname'] = $key->present_districtname;
                $jnmpInsertArray[$ir]['present_isblockorulbdesc'] = $key->present_isblockorulbdesc;
                $jnmpInsertArray[$ir]['present_blockmunicipalitydesc'] = $key->present_blockmunicipalitydesc;
                $jnmpInsertArray[$ir]['present_pin'] = $key->present_pin;
                $jnmpInsertArray[$ir]['present_grampanchayatdesc'] = $key->present_grampanchayatdesc;
                $jnmpInsertArray[$ir]['present_villagetowndesc'] = $key->present_villagetowndesc;
                $jnmpInsertArray[$ir]['certificateno'] = $key->certificateno;
                $jnmpInsertArray[$ir]['reportingdate'] = $key->reportingdate;
                $jnmpInsertArray[$ir]['dateofdeath'] = $key->dateofdeath;
                $jnmpInsertArray[$ir]['fetching_time'] = $key->fetching_time;
                $jnmpInsertArray[$ir]['running_id'] = $key->running_id;
                $jnmpInsertArray[$ir]['from_date'] = $key->from_date;
                $jnmpInsertArray[$ir]['to_date'] = $key->to_date;
                $ir++;
            }
            // dd(count($jnmpInsertArray));
            if (count($jnmpInsertArray) == $totalData) {
                foreach (array_chunk($jnmpInsertArray, 2000) as $inst_chunk) {
                    DB::connection('pgsql')->table('jnmp.jnmp_data')->insert($inst_chunk);
                }
                DB::connection('pgsql_lb_mainwrite')->table('jnmp.jnmp_data')->where('migrated_to_jb', '=', 0)->update(['migrated_to_jb' => 2]);

                DB::connection('pgsql')->commit();
                DB::connection('pgsql_lb_mainwrite')->commit();
                $response = array(
                    'status' => 1, 'msg' => 'Inserted Successfully',
                    'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                );
            } else{
                DB::connection('pgsql')->rollback();
                DB::connection('pgsql_lb_mainwrite')->rollback();
                $response = array(
                    'status' => 1, 'msg' => 'Something Went Wrong!',
                    'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                );
            }
        } catch (\Exception $e) {
            dd($e);
            DB::connection('pgsql')->rollback();
            DB::connection('pgsql_lb_mainwrite')->rollback();
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
                // 'exception_message' => 'Somethimg went wrong. May be session timeout, please logout and login again.',
            );
            $statusCode = 400;
        }finally {
            return response()->json($response, $statusCode);
        }
    }

    public function deathMarkInJb(Request $request)
    {
        $response = [];
        $statusCode = 200;

        try {
            $scheme_id = $request->scheme_id;
            // dd($scheme_id);
            $functionMainServer = DB::connection('pgsql')->select("SELECT jnmp.marking_jnmp_data_to_beneficiary_master(in_scheme_id => ".$scheme_id.");");
            $functionPaymentServer = DB::connection('pgsql_paywrite')->select("SELECT payment.marking_jnmp_data_to_payment_master(in_scheme_id => ".$scheme_id.");");
            if ($functionMainServer[0]->functionMainServer > 0 && $functionPaymentServer[0]->functionPaymentServer > 0) {
                $response = array(
                    'status' => 1, 'msg' => 'Marking Done at Jai Bangla Portal',
                    'type' => 'green', 'icon' => 'fa fa-check', 'title' => 'Success'
                );
            } else {
                $response = array(
                    'status' => 1, 'msg' => 'Something Went Wrong.',
                    'type' => 'red', 'icon' => 'fa fa-check', 'title' => 'Error'
                );
            }
        } catch (\Exception $e) {
            dd($e);
            $response = array(
                'exception' => true,
                'exception_message' => $e->getMessage(),
                // 'exception_message' => 'Somethimg went wrong. May be session timeout, please logout and login again.',
            );
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }
}
