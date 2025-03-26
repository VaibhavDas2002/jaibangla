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
use Elibyy\TCPDF\Facades\TCPDF as PDF;

class BeneficiaryApplicationStatusController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth');
        set_time_limit(300);
    }
    public function index(){
        $user_id = Auth::user()->id;
        $mapObj = Configduty::where('user_id',$user_id)->where('is_active',1)->first();
        $scheme = Configduty::select('scheme_id')->where('user_id',$user_id)->where('is_active',1)->get();
        return view('ben-application-status/index_app_status',['schemes' => $scheme, 'mapping_level' => $mapObj->mapping_level, 'district_code' => $mapObj->district_code]);
    }
    
    public function searchResult(Request $request){
        $user_id = Auth::user()->id;
        $designation_id = Auth::user()->designation_id;
        $mapObj = Configduty::where('user_id',$user_id)->where('is_active',1)->first();
        $dist_code = $mapObj->district_code;
        if ($mapObj->is_urban == 1) {
            $block_ulb = $mapObj->urban_body_code;
        }
        else {
            $block_ulb = $mapObj->taluka_code;
        }   
        $map_level = $mapObj->mapping_level;

        $ben_id = $request->ben_id;
        $scheme_id = $request->scheme_id;

        if (!is_null($scheme_id)) {
            $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
            //$parameter['scheme_id'] = $scheme_id;
            $schema_name =  $sObj->short_code;
            //dd($schema_name);
            if (empty($schema_name)){
              $schema_name = 'pension';
            }
            $table_name =  $schema_name . '.beneficiary';
        }
	else {
            $table_name =  'pension.beneficiary';
        }

        if ($map_level == 'State' || $map_level == 'Department') {
            // $ben_id = $request->ben_id;
            // $scheme_id = $request->scheme_id;
            // $result = DB::select(DB::raw("select * from ".$table_name." where id = ".$ben_id." and scheme_id = ".$scheme_id));
	    $ben_res = "select * from ".$table_name." where id = ".$ben_id;
            if (!is_null($scheme_id)) {
                $ben_res.= " and scheme_id = ".$scheme_id;
            }
            $result = DB::connection('pgsql_mis')->select($ben_res);
            $ifms_result_r = DB::select(DB::raw("select * from ifms.transaction_lot_details_report where pension_id = ".$ben_id));
            $sbi_result_r = DB::select(DB::raw("select * from sbi.transaction_lot_details_report where pension_id = ".$ben_id));
            $ifms_result = DB::select(DB::raw("select * from ifms.transaction_lot_details where pension_id = ".$ben_id));
            $sbi_result = DB::select(DB::raw("select * from sbi.transaction_lot_details where pension_id = ".$ben_id));
            if (DB::table('ifms.transaction_lot_details')->where('pension_id',$ben_id)->count() == 0) {
                $ifmsObj = DB::table('ifms.transaction_lot_details_report')->where('pension_id',$ben_id)->orderBy('id','desc')->first();
            }
            else {
                $ifmsObj = DB::table('ifms.transaction_lot_details')->where('pension_id',$ben_id)->orderBy('id','desc')->first();
            }

            if (DB::table('sbi.transaction_lot_details')->where('pension_id',$ben_id)->count() == 0) {
                $sbiObj = DB::table('sbi.transaction_lot_details_report')->where('pension_id',$ben_id)->orderBy('id','desc')->first();
            }
            else {
                $sbiObj = DB::table('sbi.transaction_lot_details')->where('pension_id',$ben_id)->orderBy('id','desc')->first();
            }
            
            if (!empty($ifmsObj)) {
                $ifms_lot = DB::table('ifms.transaction_lot')->where('lot_no',$ifmsObj->drn_part)->get();
            }
            if (!empty($sbiObj)) {
                $sbi_lot = DB::table('sbi.transaction_lot')->where('lot_no',$sbiObj->lot_no)->get();
            }

            $query = "select * from lot_master where lot_no::int in(";
            if (!empty($ifmsObj)) {
                $query .= $ifmsObj->drn_part;
            }
            if (!empty($ifmsObj) && !empty($sbiObj)) {
                $query .= ",";
            }
            if (!empty($sbiObj)) {
                $query .= $sbiObj->lot_no;
            }
            $query .= ")";
            
            if ((!empty($ifmsObj) && empty($sbiObj)) || (empty($ifmsObj) && !empty($sbiObj)) || (!empty($ifmsObj) && !empty($sbiObj))) {
                $lot_mas = DB::connection('pgsql_mis')->select($query);
            }

            $duplicate = DB::select(DB::raw("select *,b.next_level_role_id,b.lot_generated,b.payment_count,b.last_paid_yymm,b.bank_edited,u.username 
                from public.duplicate_approve_reject d join pension.beneficiary b on b.id=d.original_application_id
                join public.users u on u.id=d.rejected_user_id
                where d.original_approve_application_id=".$ben_id));
            $update_details = DB::select(DB::raw("select * from public.update_ben_details where original_application_id=".$ben_id));

            if (empty($ifmsObj) && !empty($sbiObj)) {
                return view('ben-application-status/app-result',[
                    'results' => $result, 
                    'ifms' => $ifms_result, 
                    'sbi' => $sbi_result, 
                    'ifms_r' => $ifms_result_r, 
                    'sbi_r' => $sbi_result_r, 
                    'sbi_l' => $sbi_lot, 
                    'lot_m' => $lot_mas, 
                    'mapping_level' => $map_level, 
                    'designation' => $designation_id,
                    'duplicate' => $duplicate,
                    'update_details' => $update_details
                ]);
            }
            else if (!empty($ifmsObj) && empty($sbiObj)) {
                return view('ben-application-status/app-result',[
                    'results' => $result, 
                    'ifms' => $ifms_result, 
                    'sbi' => $sbi_result, 
                    'ifms_r' => $ifms_result_r, 
                    'sbi_r' => $sbi_result_r, 
                    'ifms_l' => $ifms_lot, 
                    'lot_m' => $lot_mas, 
                    'mapping_level' => $map_level, 
                    'designation' => $designation_id,
                    'duplicate' => $duplicate,
                    'update_details' => $update_details
                ]);
            }
            else if (empty($ifmsObj) && empty($sbiObj)) {
                return view('ben-application-status/app-result',[
                    'results' => $result, 
                    'ifms' => $ifms_result, 
                    'sbi' => $sbi_result, 
                    'ifms_r' => $ifms_result_r, 
                    'sbi_r' => $sbi_result_r,
                    'mapping_level' => $map_level, 
                    'designation' => $designation_id,
                    'duplicate' => $duplicate,
                    'update_details' => $update_details
                ]);
            }
            else {
                return view('ben-application-status/app-result',[
                    'results' => $result, 
                    'ifms' => $ifms_result, 
                    'sbi' => $sbi_result, 
                    'ifms_r' => $ifms_result_r, 
                    'sbi_r' => $sbi_result_r, 
                    'sbi_l' => $sbi_lot, 
                    'ifms_l' => $ifms_lot, 
                    'lot_m' => $lot_mas, 
                    'mapping_level' => $map_level, 
                    'designation' => $designation_id,
                    'duplicate' => $duplicate,
                    'update_details' => $update_details
                ]);
            }    
        }
        else if ($map_level == 'District') {
            $type = $request->select_type;
            $first_name = strtoUpper(trim($request->ben_fname));
            $middle_name = strtoUpper(trim($request->ben_mname));
            $last_name = strtoUpper(trim($request->ben_lname));
            $rural_urban = $request->is_rural_urban;
            $block_ulb = $request->block_ulb;
            if ($type == 'b_id' && !is_null($ben_id)) {
                $query = "select * from ".$table_name." where created_by_dist_code = ".$dist_code." ";
                if (!is_null($ben_id)) {
                    $query .= " and id = ".$ben_id."";
                }
                if (!is_null($scheme_id)) {
                    $query .= " and scheme_id = ".$scheme_id." ";
                }
                if (!is_null($block_ulb)) {
                    $query .= " and created_by_local_body_code = ".$block_ulb."";
                }
            }
            else {
                $query = "select * from ".$table_name." where created_by_dist_code = ".$dist_code." ";
                if ($first_name!='') {
                    $query .= " and ben_fname ILIKE '".$first_name."%' ";
                }
                if ($middle_name != '') {
                    $query .= " and ben_mname ILIKE '".$middle_name."%' ";
                }
                if ($last_name != '') {
                    $query .= " and ben_lname ILIKE '".$last_name."%' ";
                }
                if (!is_null($scheme_id)) {
                    $query .= " and scheme_id = ".$scheme_id." ";
                }
                if (!is_null($block_ulb)) {
                    $query .= " and created_by_local_body_code = ".$block_ulb."";
                }   
            }
            // print $query;die();
            $result = DB::connection('pgsql_mis')->select($query);
            return view('ben-application-status/app-result',['results' => $result, 'mapping_level' => $map_level, 'designation' => $designation_id]);
        }
        else if ($map_level == 'Block' || $map_level == 'Subdiv') {
            $type = $request->select_type;
            $first_name = strtoUpper(trim($request->ben_fname));
            $middle_name = strtoUpper(trim($request->ben_mname));
            $last_name = strtoUpper(trim($request->ben_lname));
            if ($type == 'b_id' && !is_null($ben_id)) {
                $query = "select * from ".$table_name." where created_by_dist_code = ".$dist_code." and created_by_local_body_code = ".$block_ulb."";
                if (!is_null($ben_id)) {
                    $query .= " and id = ".$ben_id."";
                }
                if (!is_null($scheme_id)) {
                    $query .= " and scheme_id = ".$scheme_id." ";
                }
            }
            else {
                $query = "select * from ".$table_name." where created_by_dist_code = ".$dist_code."  and created_by_local_body_code = ".$block_ulb."";
                if ($first_name!='') {
                    $query .= " and ben_fname ILIKE '".$first_name."%' ";
                }
                if ($middle_name != '') {
                    $query .= " and ben_mname ILIKE '".$middle_name."%' ";
                }
                if ($last_name != '') {
                    $query .= " and ben_lname ILIKE '".$last_name."%' ";
                }
                if (!is_null($scheme_id)) {
                    $query .= " and scheme_id = ".$scheme_id." ";
                }
            }
            $result = DB::connection('pgsql_mis')->select($query);
            // print $query;die();
            return view('ben-application-status/app-result',['results' => $result, 'mapping_level' => $map_level, 'designation' => $designation_id]);
        } 
        else {
            print 'Something went wrong!!';
        }
    }

    public function savePdf(Request $request){
        $pension_id = $request->id;
        $query = "Select *,b.id as pension_id,d.district_name,s.scheme_name from pension.beneficiary b join public.m_district d on d.district_code=b.created_by_dist_code join public.m_scheme s on s.id=b.scheme_id where b.id=".$pension_id;
        $data = DB::connection('pgsql_mis')->select($query);
        $view = \View::make('ben-application-status/AppStatusDownloadPdf')->with('results', $data);
        $html_content = $view->render();
        //dd($html_content);
        PDF::AddPage('P', 'A4');
        PDF::writeHTML($html_content, true, false, true, false, '');
        $filename = 'Pension Id-'.$pension_id . '.pdf';
        PDF::Output($filename, 'D');
    }
}
