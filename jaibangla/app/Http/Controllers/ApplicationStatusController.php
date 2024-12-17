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
use Auth;

class ApplicationStatusController extends Controller
{
    public function index()
    {
        //$user_id = Auth::user()->id;
        $district = District::all();
        //print_r($district);
        $scheme = Scheme::where('is_active', 1)->get();
        return view('ben-application-status/index', ['schemes' => $scheme, 'districts' => $district]);
    }

    public function searchByBenName(Request $request)
    {
        $this->validate($request, [
            // 'ben_fname' => 'required|min:3',
            'scheme_type' => 'required|not-in:0',
            'dist_code' => 'required|not-in:0',
            'is_rural_urban' => 'required|not-in:0',
            'block_ulb' => 'required|not-in:0'
        ]);

        // $user_id = Auth::user()->id;
        // $dutyObj = Configduty::where('user_id',$user_id)->first();
        // $dist_code = $dutyObj->district_code;

        $first_name = strtoUpper(trim($request->ben_fname));
        $middle_name = strtoUpper(trim($request->ben_mname));
        $last_name = strtoUpper(trim($request->ben_lname));

        $ben_id = $request->ben_id;

        $scheme_id = $request->scheme_type;
        $rural_urban = $request->is_rural_urban;
        $block_ulb = $request->block_ulb;
        $dist_code = $request->dist_code;

        //echo $first_name,$middle_name,$last_name;
        /*$result =BeneficiaryPensions::where('dist_code',$dist_code)
        		->where('ben_fname' , 'ILIKE' , $first_name.'%')
				->where('ben_mname','ILIKE',$middle_name.'%')
				->where('ben_lname','ILIKE',$last_name.'%')
				->where('scheme_id',$scheme_id)
				->where('rural_urban_id',$rural_urban)
				->where('block_ulb_code', $block_ulb)->get();*/
        $schemes_arr =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
        $schema_name =  $schemes_arr->short_code;
        //dd($schema_name);
        if (empty($schema_name))
            $schema_name = 'pension';
        $db_table =  $schema_name . '.beneficiaries';
        if ($request['ben_id'] != '') {
            $result = DB::connection('pgsql_mis')->select(DB::raw("select id,
            ben_fname, ben_lname, ben_mname, father_fname, father_mname, father_lname,block_ulb_name, epic_voter_id, ration_card_cat, ration_card_no, bank_ifsc, 
            bank_ifsc,bank_code,scheme_id, lot_generated, payment_count, next_level_role_id,is_verified,is_approved,is_rejected from " . $db_table . " where dist_code = " . $dist_code . " and id = " . $ben_id . " and scheme_id = " . $scheme_id . " and rural_urban_id = " . $rural_urban . " and block_ulb_code = " . $block_ulb . ""));
        } else {
            $result = DB::connection('pgsql_mis')->select(DB::raw("select id,
            ben_fname, ben_lname, ben_mname, father_fname, father_mname, father_lname,block_ulb_name, epic_voter_id, ration_card_cat, ration_card_no, bank_ifsc, 
            bank_ifsc,bank_code,scheme_id, lot_generated, payment_count, next_level_role_id,is_verified,is_approved,is_rejected from " . $db_table . " where dist_code = " . $dist_code . " and ben_fname ILIKE '" . $first_name . "%' and ben_mname ILIKE '" . $middle_name . "%' and ben_lname ILIKE '" . $last_name . "%' and scheme_id = " . $scheme_id . " and rural_urban_id = " . $rural_urban . " and block_ulb_code = " . $block_ulb . ""));
        }
        if (!empty($result)) {
            return view('ben-application-status/ben_search_details', ['results' => $result]);
        } else {
            return redirect('ben-application-status')->with('msg1', 'No record found! Please provide correct information.');
        }
    }

    public function viewStatus(Request $request)
    {
        //$result = DB::select(DB::raw("select * from pension.beneficiary where id=".$id));
        $id = $request->id;
        $scheme_id = $request->scheme_id;
        $schemes_arr =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
        $schema_name =  $schemes_arr->short_code;
        //dd($schema_name);
        if (empty($schema_name))
            $schema_name = 'pension';
        $db_table =  $schema_name . '.beneficiaries';
        $results = DB::connection('pgsql_mis')->table('' . $db_table)->select(
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
            'village_town_city','is_verified','is_approved','is_rejected'
        )->where('id', $id)->first();
        //$results = BeneficiaryPensions::where('id', $id)->first();
        //print $results->next_level_role_id;
        if ($results->next_level_role_id == 0 and ($results->lot_generated == 0 or $results->lot_generated == 1)) {
            $msg = 'Active Beneficiary';
        } else if ($results->is_approved==0 or $results->is_verified==0 or is_null($results->next_level_role_id)) {
            $msg = 'Under Verification';
        } else if ($results->next_level_role_id == 0 and $results->lot_generated < 0) {
            $msg = 'Under Bank Details Rectification';
        } else if ($results->next_level_role_id == -2) {
            $msg = 'De-active Beneficiary(Rejected)';
        } else if ($results->next_level_role_id == -99) {
            $msg = 'Beneficiary Expired';
        } else {
            $msg = '';
        }
        //print($msg);
        return view('ben-application-status/view_status_beneficiary', ['result' => $results, 'msg' => $msg]);
    }

    public function loadBlockUlb($rural_urban, $district_code)
    {
        if ($rural_urban == 1) {
            //Urban
            $results = UrbanBody::select('urban_body_code as code', 'urban_body_name as name')->where('district_code', $district_code)->get(['urban_body_code as code', 'urban_body_name as name']);
            return response()->json($results);
        } elseif ($rural_urban == 2) {
            //Rural
            $results = Taluka::select('block_code as code', 'block_name as name')->where('district_code', $district_code)->get(['block_code as code', 'block_name as name']);
            return response()->json($results);
        }
    }
}
