<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\BeneficiaryPensions;
use App\District;
use App\Configduty;
use App\Scheme;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use App\DupliacteApproveReject;
use App\lot_master;
use Auth;
use App\Helpers\AuthChecker;

class BeneficiaryPaymentStatusController extends Controller
{
//     public function __construct()
//   { 
//     return redirect("/")->with('success', 'This link is moved to another link "Beneficiary Status > Track & Payment Status". ');
//   }
    public function index(Request $request)
    {
        return redirect("/")->with('success', 'This link is moved to another link "Beneficiary Status > Track & Payment Status". ');
        /*IF Auth not found then redirect to login page*/
        if (!isset(Auth::user()->email)) {
            return redirect('/login');
        }
        /**/

        $user_id = AuthChecker::getUserId();
        //$scheme = Scheme::all();
        $dutyObj = Configduty::where('user_id', $user_id)->first();
        $schemes = Configduty::select('scheme_id')->distinct()->where('user_id', $user_id)->where('is_active', 1)->get();
        $scheme = Scheme::whereIn('id', $schemes)->get();

        // State and Department
        if ($dutyObj->mapping_level == 'State' || $dutyObj->mapping_level == 'Department') {
            return view('ben-payment-status/index_verifier', ['schemes' => $scheme, 'is_dept' => 1]);
        }
        // District
        elseif ($dutyObj->mapping_level == 'District') {
            $dist_code = $dutyObj->district_code;
            return view('ben-payment-status/index_verifier', ['schemes' => $scheme, 'dist_code' => $dist_code]);
        }
        // Verifier
        elseif ($dutyObj->mapping_level == 'Block' || $dutyObj->mapping_level == 'Subdiv') {
            $dist_code = $dutyObj->district_code;
            $is_urban = $dutyObj->is_urban;
            if ($is_urban == 2) {
                $block_ulb_code = $dutyObj->taluka_code;
            } else {
                $block_ulb_code = $dutyObj->urban_body_code;
            }

            return view('ben-payment-status/index_verifier', ['schemes' => $scheme, 'dist_code' => $dist_code, 'is_urban' => $is_urban, 'block_ulb_code' => $block_ulb_code]);
        } else {
            print 'Invalid User';
        }
    }

    public function searchByBenName(Request $request)
    {
        $this->validate($request, [
            'scheme_type' => 'required|not-in:0'
        ]);

        $first_name = strtoUpper(trim($request->ben_fname));
        $middle_name = strtoUpper(trim($request->ben_mname));
        $last_name = strtoUpper(trim($request->ben_lname));

        $ben_id = $request->ben_id;
        $dist_code = $request->dist_code;
        $scheme_id = $request->scheme_type;
        $rural_urban = $request->is_rural_urban;
        $block_ulb = $request->block_ulb;
        $is_dept = $request->is_department;
        $schemes_arr =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
        $parameter['scheme_id'] = $scheme_id;
        $schema_name =  $schemes_arr->short_code;
        //dd($schema_name);
        if (empty($schema_name))
            $schema_name = 'pension';
        $db_table =  $schema_name . '.beneficiaries';
        // Department Level
        if ($is_dept != '') {
            $result = DB::connection('pgsql_mis')->select(DB::raw("select *,(select district_name from m_district as m where m.district_code=pension.created_by_dist_code) from " . $db_table . " as pension where id=" . $ben_id . ""));
        }
        // Except Department
        else {
            // District Level
            if ($is_dept == '' && $dist_code != '' && $block_ulb == '') {
                $result = DB::connection('pgsql_mis')->select(DB::raw("select *,(select district_name from m_district as m where m.district_code=pension.created_by_dist_code) from " . $db_table . " as pension where id=" . $ben_id . " and created_by_dist_code = " . $dist_code . ""));
            }
            // Block or Subdiv Level
            if ($is_dept == '' && $dist_code != '' && $block_ulb != '') {
                if ($ben_id != '') {
                    $result = DB::connection('pgsql_mis')->select(DB::raw("select *,(select district_name from m_district as m where m.district_code=pension.created_by_dist_code) from " . $db_table . " as pension where id=" . $ben_id . " and created_by_dist_code = " . $dist_code . " and rural_urban_id = " . $rural_urban . " and created_by_local_body_code = " . $block_ulb . ""));
                } else {
                    $result = DB::connection('pgsql_mis')->select(DB::raw("select *,(select district_name from m_district as m where m.district_code=pension.created_by_dist_code) from " . $db_table . " as pension where created_by_dist_code = " . $dist_code . " and ben_fname ILIKE '" . $first_name . "%' and ben_mname ILIKE '" . $middle_name . "%' and ben_lname ILIKE '" . $last_name . "%' and rural_urban_id = " . $rural_urban . " and created_by_local_body_code = " . $block_ulb . ""));
                }
            }
        }

        if (!empty($result)) {
            return view('ben-payment-status/ben_search_details', ['results' => $result]);
        } else {
            return redirect('ben-payment-status')->with('msg1', 'No record found! Please provide correct information.');
        }
    }
    public function viewStatus(Request $request)
    {
        $id = $request->id;
        $scheme_id = $request->scheme_id;
        $schemes_arr =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
        $schema_name =  $schemes_arr->short_code;
        //dd($schema_name);
        if (empty($schema_name))
            $schema_name = 'pension';
        $db_table =  $schema_name . '.beneficiaries';
        $pension_id_arr = [];
        $result = DB::connection('pgsql_mis')->table('' . $db_table)->select(
            'id',
            'ben_fname',
            'ben_lname',
            'ben_mname',
            'father_fname',
            'father_mname',
            'father_lname',
            'block_ulb_name',
            'epic_voter_id',
            'ration_card_cat',
            'ration_card_no',
            'bank_ifsc',
            'bank_ifsc',
            'bank_code',
            'scheme_id',
            'lot_generated',
            'payment_count',
            'next_level_role_id',
            'created_at',
            'last_paid_yymm',
            'bank_name',
            'branch_name',
            'pincode',
            'post_office',
            'police_station',
            'gp_ward_name',
            'village_town_city'
        )->where('id', $id)->first();

        if ($result->next_level_role_id == -2) {
            //$rows = DupliacteApproveReject::where('original_application_id',$id)->count();
            $dup_ids = DupliacteApproveReject::where('original_application_id', $id)->first();
            $dup = DupliacteApproveReject::where('original_application_id', $id)->get();
            //print_r($dup);die();
            // foreach ($dup_ids as $key=>$val) {
            //     $pension_arr[] = $val;
            // }
            // $approved_id = implode(',', $pension_arr);
            //print $rejected_id;
            return view('ben-payment-status/view_status_beneficiary', ['result' => $result, 'reject_id' => $dup, 'approved' => $dup_ids]);
        }
        if ($result->payment_count > 0) {
            $ben_status = DB::connection('pgsql_mis')->select(DB::raw("select 'ifms' as payment_mode,lm.lot_month,lm.lot_year,lm.lot_no,lm.scheme_id,be.pension_id,be.ifsc as ifsc_code,be.acc_no as account_no,
                (case when lm.lot_status=6  then 'Payment processed' else 'Under process' end) process_status,
                (case when (be.wrongdata_flag=0 and lm.lot_status=6) then 'Payment success' when (be.wrongdata_flag=0 and lm.lot_status<6) then 'Payment under process' else 'Payment error' end) payment_status 
				from ifms.transaction_lot_details be,ifms.transaction_lot lm
                where lm.lot_no=be.drn_part and be.pension_id =" . $id . "
                union
                select 'sbi' as payment_mode,tl.lot_month,tl.lot_year,tl.lot_no,tl.scheme_id,tld.pension_id,tld.ifsc_code_credit as ifsc_code,tld.account_credit as account_no,
                (case when tl.lot_status=6  then 'Payment processed' else 'Under process' end) process_status,
                (case when tld.status_code='S00' then 'Payment success' else (case when tld.status_code is null then 'Payment under process' else 'Payment error' end)end) payment_status 
                from sbi.transaction_lot_details tld,sbi.transaction_lot tl
                where tl.lot_no=tld.lot_no and tld.pension_id =" . $id . " order by lot_no"));
            $ben_status_old = DB::connection('pgsql_mis')->select(DB::raw("select 'ifms' as payment_mode,lm.lot_month,lm.lot_year,lm.lot_no,lm.scheme_id,be.pension_id,be.ifsc as ifsc_code,be.acc_no as account_no,
                 (case when lm.lot_status=6  then 'Payment processed' else 'Under process' end) process_status,
                (case when (be.wrongdata_flag=0 and lm.lot_status=6) then 'Payment success' when (be.wrongdata_flag=0 and lm.lot_status<6) then 'Payment under process' else 'Payment error' end) payment_status 
				from ifms.transaction_lot_details_report be,ifms.transaction_lot lm
                where lm.lot_no=be.drn_part and be.pension_id =" . $id . "
                union
                select 'sbi' as payment_mode,tl.lot_month,tl.lot_year,tl.lot_no,tl.scheme_id,tld.pension_id,tld.ifsc_code_credit as ifsc_code,tld.account_credit as account_no,
                (case when tl.lot_status=6  then 'Payment processed' else 'Under process' end) process_status,
                (case when tld.status_code='S00' then 'Payment success' else (case when tld.status_code is null then 'Payment under process' else 'Payment error' end)end) payment_status 
                from sbi.transaction_lot_details_report tld,sbi.transaction_lot tl
                where tl.lot_no=tld.lot_no and tld.pension_id =" . $id . " order by lot_no"));
        }

        //print_r($ben_status);
        // For Checking Duplicate approved ids from Table: duplicate_approve_reject
        $rows = DupliacteApproveReject::where('original_approve_application_id', $id)->count();
        //print $rows;
        if ($rows > 0) {
            $duplicate_ids = DupliacteApproveReject::select('original_application_id')->where('original_approve_application_id', $id)->get()->pluck('original_application_id');
            foreach ($duplicate_ids as $key => $val) {
                $pension_id_arr[] = $val;
            }
            $dupliate_id = implode(',', $pension_id_arr);
            $adjust_date = DupliacteApproveReject::where('original_approve_application_id', $id)->first();

            $ben_status1 = DB::connection('pgsql_mis')->select(DB::raw("
                select lm.lot_month,lm.lot_year,lm.lot_no,lm.scheme_id,be.pension_id,be.ifsc as ifsc_code,be.acc_no as account_no,
                (case when lm.lot_status=6  then 'Payment processed' else 'Under process' end) process_status,
                (case when (be.wrongdata_flag=0 and lm.lot_status=6) then 'Payment success' when (be.wrongdata_flag=0 and lm.lot_status<6) then 'Payment under process' else 'Payment error' end) payment_status 
                from ifms.transaction_lot_details be,ifms.transaction_lot lm
                where lm.lot_no=be.drn_part and be.pension_id in(" . $dupliate_id . ")
                union
                select tl.lot_month,tl.lot_year,tl.lot_no,tl.scheme_id,tld.pension_id,tld.ifsc_code_credit as ifsc_code,tld.account_credit as account_no,
                (case when tl.lot_status=6  then 'Payment processed' else 'Under process' end) process_status,
                (case when tld.status_code='S00' then 'Payment success' else (case when tld.status_code is null then 'Payment under process' else 'Payment error' end)end) payment_status 
                from sbi.transaction_lot_details tld,sbi.transaction_lot tl
                where tl.lot_no=tld.lot_no and tld.pension_id in(" . $dupliate_id . ") order by pension_id,lot_no"));
            return view('ben-payment-status/view_status_beneficiary', ['result' => $result, 'ben_status' => $ben_status, 'ben_status_old' => $ben_status_old, 'ben_status1' => $ben_status1, 'duplicate_ids' => $dupliate_id, 'adjust' => $adjust_date]);
        }
        if ($result->payment_count > 0) {
            return view('ben-payment-status/view_status_beneficiary', ['result' => $result, 'ben_status' => $ben_status, 'ben_status_old' => $ben_status_old]);
        } else {
            return view('ben-payment-status/view_status_beneficiary', ['result' => $result,]);
        }
    }

    public function paymentStatusErrorMsg($lot_no, $scheme_id, $pension_id, $payment_type)
    {
        if ($payment_type == 2) {
            $tld_table = "transaction_lot_details_report";
        } else
            $tld_table = "transaction_lot_details";
        $lotObj = lot_master::where('lot_no', $lot_no)->where('scheme_id', $scheme_id)->first();
        if ($lotObj->payment_mode == 'IFMS') {
            $results = DB::connection('pgsql_mis')->select(DB::raw("select (case when wrongdata_flag=0 then utr_no else ifms_status end) as status_code from ifms." . $tld_table . " where drn_part='" . $lot_no . "' and scheme_id=" . $scheme_id . " and pension_id=" . $pension_id));
            return response()->json($results);
        } elseif ($lotObj->payment_mode == 'SBI') {
            $results = DB::connection('pgsql_mis')->select(DB::raw("select (case when tld.status_code='S00' then tld.credit_payment_reference else c.description end) as status_code 
			from sbi.credit_transaction_code c,sbi." . $tld_table . " tld where tld.lot_no='" . $lot_no . "' and tld.scheme_id=" . $scheme_id . " and tld.pension_id=" . $pension_id . " and tld.status_code=c.code"));
            return response()->json($results);
        } else {
            //return response()->json(['results'=>'No Data Found!!']);
        }
    }
}
