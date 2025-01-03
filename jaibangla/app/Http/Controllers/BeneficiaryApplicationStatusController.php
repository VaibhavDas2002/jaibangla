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
use Illuminate\Support\Facades\Auth;
use Elibyy\TCPDF\Facades\TCPDF as PDF;
use App\Helpers\AuthChecker;

class BeneficiaryApplicationStatusController extends Controller
{
    public function index()
    {
        $user_id = AuthChecker::getUserId();
        $mapObj = Configduty::where('user_id',$user_id)->where('is_active',1)->first();
        $scheme = Configduty::select('scheme_id')->where('user_id',$user_id)->where('is_active',1)->get();
        return view('ben-application-status/index_app_status',['schemes' => $scheme, 'mapping_level' => $mapObj->mapping_level, 'district_code' => $mapObj->district_code]);
    }
    public function searchResult(Request $request)
    {
        $user_id = AuthChecker::getUserId();
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
            $table_name =  $schema_name . '.beneficiaries';
        }
        else {
            $table_name =  'pension.beneficiaries';
        }
        $ben_id = $request->ben_id;
        $ben_res = "select * from ".$table_name." where id = ".$ben_id;
        if (!is_null($scheme_id)) {
            $ben_res.= " and scheme_id = ".$scheme_id;
        }
        $result = DB::connection('pgsql_mis')->select($ben_res);
        // $update_details = DB::select(DB::raw("select count(1) from public.update_ben_details where original_application_id=".$ben_id));
        $update_details = DB::connection('pgsql_mis')->table('public.update_ben_details')->where('original_application_id',$ben_id)->count();
        $ifms_count = DB::connection('pgsql_mis')->table('ifms.transaction_lot_details')->where('pension_id',$ben_id)->count();
        $ifms_r_count = DB::connection('pgsql_mis')->table('ifms.transaction_lot_details_report')->where('pension_id',$ben_id)->count();
        $ifmsObj = DB::connection('pgsql_mis')->table('ifms.transaction_lot_details')->where('pension_id',$ben_id)->orderBy('id','desc')->first();
        //print_r($ifmsObj);die();
        $sbi_count = DB::connection('pgsql_mis')->table('sbi.transaction_lot_details')->where('pension_id',$ben_id)->count();
        $sbi_r_count = DB::connection('pgsql_mis')->table('sbi.transaction_lot_details_report')->where('pension_id',$ben_id)->count();
        $sbiObj = DB::connection('pgsql_mis')->table('sbi.transaction_lot_details')->where('pension_id',$ben_id)->orderBy('id','desc')->first();
        //print_r($sbiObj);die();
        $duplicate_ben = DB::connection('pgsql_mis')->table('public.duplicate_approve_reject')->where('original_approve_application_id',$ben_id)->count();
        $sbi_lot_count = 0;
        $ifms_lot_count = 0;
        if(!empty($sbiObj)){
            $sbi_lot_count = DB::connection('pgsql_mis')->table('sbi.transaction_lot')->where('lot_no',$sbiObj->lot_no)->count();
        }
        //echo $sbi_lot_count;die();
        if (!empty($ifmsObj)) {
            $ifms_lot_count = DB::connection('pgsql_mis')->table('ifms.transaction_lot')->where('lot_no',$ifmsObj->drn_part)->count();
        }
        $lot_master_count = 1;
        if ($sbi_lot_count == 0 && $ifms_lot_count == 0) {
            $lot_master_count = 0;
        }
        
        return view('ben-application-status/app-result',[
            'results' => $result,
            'update_details' => $update_details,
            'ifms_results' => $ifms_count+$ifms_r_count,
            'sbi_result' => $sbi_count+$sbi_r_count,
            'duplicate_ben' => $duplicate_ben,
            'sbi_lot_count' => $sbi_lot_count,
            'ifms_lot_count' => $ifms_lot_count,
            'lotMasterCount' => $lot_master_count
        ]);    
    }
    public function personalDetails(Request $request)
    {
        $ben_id = $request->ben_id;
        if ($request->ajax()) {
            $ben_res = "select b.*,s.scheme_name, d.district_name from pension.beneficiaries b join public.m_scheme s on b.scheme_id = s.id join public.m_district d on b.dist_code = d.district_code where b.id= ".$ben_id;
            $ben_details = DB::connection('pgsql_mis')->select($ben_res);
            return datatables()->of($ben_details)
            ->addIndexColumn()
            ->addColumn('pension_id', function($ben_details){

                return $ben_details->id;
            })
            ->addColumn('fname', function($ben_details){

                $fname = $ben_details->ben_fname;
                if (!empty($fname)) {
                    return $fname;
                }else{
                    return '';
                }
            })
            ->addColumn('mname', function($ben_details){

                $mname = $ben_details->ben_mname;
                if (!empty($mname)) {
                    return $mname;
                }else{
                    return '';
                }
                //return $ben_details->ben_mname;
            })
            ->addColumn('lname', function($ben_details){

                $lname = $ben_details->ben_lname;
                if (!empty($lname)) {
                    return $lname;
                }else{
                    return '';
                }
                //return $ben_details->ben_lname;
            })
            ->addColumn('father_fname', function($ben_details){

                $father_fname = $ben_details->father_fname;
                if (!empty($father_fname)) {
                    return $father_fname;
                }else{
                    return '';
                }
                //return $ben_details->father_fname;
            })
            ->addColumn('father_mname', function($ben_details){

                $father_mname = $ben_details->father_mname;
                if (!empty($father_mname)) {
                    return $father_mname;
                }else{
                    return '';
                }
                //return $ben_details->father_mname;
            })
            ->addColumn('father_lname', function($ben_details){

                $father_lname = $ben_details->father_lname;
                if (!empty($father_lname)) {
                    return $father_lname;
                }else{
                    return '';
                }
                //return $ben_details->father_lname;
            })
            ->addColumn('voter_id', function($ben_details){
                $voter_id = $ben_details->epic_voter_id;
                if (!empty($voter_id)) {
                    return $voter_id;
                }else{
                    return '';
                }
                // return $ben_details->epic_voter_id;
            })
            ->addColumn('ration_card_cat', function($ben_details){
                $ration = $ben_details->ration_card_cat;
                if (!empty($ration)) {
                    return $ration;
                }else{
                    return '';
                }
                // return $ben_details->ration_card_cat;
            })
            ->addColumn('ration_card', function($ben_details){
                $ration_card = $ben_details->ration_card_no;
                if (!empty($ration_card)) {
                    return $ration_card;
                }else{
                    return '';
                }
                // return $ben_details->ration_card_no;
            })
            ->addColumn('district', function($ben_details){

                return $ben_details->district_name;
            })
            ->addColumn('scheme', function($ben_details){

                return $ben_details->scheme_name;
            })
            ->addColumn('block_municipality', function($ben_details){

                return $ben_details->block_ulb_name;
            })
            ->addColumn('gp_ward', function($ben_details){

                return $ben_details->gp_ward_name;
            })
            ->addColumn('created_at', function($ben_details){

                return date("d-m-Y", strtotime($ben_details->created_at));
            })
            ->addColumn('next_level_role_id', function($ben_details){

                return $ben_details->next_level_role_id;
            })
            ->addColumn('lot_generated', function($ben_details){

                return $ben_details->lot_generated;
            })
            ->addColumn('bank_edited', function($ben_details){

                return $ben_details->bank_edited;
            })
            ->addColumn('payment_count', function($ben_details){

                return $ben_details->payment_count;
            })
            ->addColumn('last_paid_yymm', function($ben_details){

                return $ben_details->last_paid_yymm;
            })
            ->addColumn('bank_name', function($ben_details){

                return $ben_details->bank_name;
            })
            ->addColumn('branch_name', function($ben_details){

                return $ben_details->branch_name;
            })
            ->addColumn('bank_code', function($ben_details){

                return $ben_details->bank_code;
            })
            ->addColumn('ifsc', function($ben_details){

                return $ben_details->bank_ifsc;
            })
            ->rawColumns(['pension_id','fname','mname','lname','father_fname','father_mname','father_lname','voter_id','ration_card_cat','ration_card','district','scheme','block_municipality','gp_ward','created_at','next_level_role_id','lot_generated','bank_edited','payment_count','last_paid_yymm','bank_name','branch_name','bank_code','ifsc'])
            ->make(true);   
        }else{
            return view('ben-application-status/app-result');
        }
    }
    public function updateBenDetails(Request $request)
    {
        $ben_id = $request->ben_id;
        if ($request->ajax()) {
            $update_ben_res = "select ub.*,s.scheme_name, d.district_name,u.username from public.update_ben_details ub join public.m_scheme s on ub.scheme_id = s.id join public.m_district d on ub.dist_code = d.district_code join public.users u on ub.user_id = u.id where ub.original_application_id= ".$ben_id;
            $update_ben_details = DB::connection('pgsql_mis')->select($update_ben_res);
            return datatables()->of($update_ben_details)
            ->addIndexColumn()
            ->addColumn('pension_id', function($update_ben_details){

                return $update_ben_details->original_application_id;
            })
            ->addColumn('district', function($update_ben_details){

                return $update_ben_details->district_name;
            })
            ->addColumn('scheme', function($update_ben_details){

                return $update_ben_details->scheme_name;
            })
            ->addColumn('old_data', function($update_ben_details){

                $oData = json_decode($update_ben_details->old_data);
                if (!empty($oData)) {
                    $oldData = '<ol>';
                    foreach($oData as $key => $val) {
                        $oldData .= '<li>' . $key . ': ' . $val . '</li>';
                    }
                    $oldData .= '</ol>';
                    return $oldData;
                }
            })
            ->addColumn('new_data', function($update_ben_details){

                $nData = json_decode($update_ben_details->new_data);
                if (!empty($nData)) {
                    $newData = '<ol>';
                    foreach($nData as $key => $val) {
                        $newData .= '<li>' . $key . ': ' . $val . '</li>';
                    }
                    $newData .= '</ol>';
                   return $newData;
                }
            })
            ->addColumn('remarks', function($update_ben_details){

                return $update_ben_details->remarks;
            })
            ->addColumn('user_id', function($update_ben_details){

                return $update_ben_details->username;
            })
            ->addColumn('date', function($update_ben_details){

                $created_at = $update_ben_details->created_at;
                $onlyDate = explode(' ', $created_at);
                return date("d-m-Y", strtotime($onlyDate[0]));
            })
            ->rawColumns(['pension_id','district','scheme','old_data','new_data','remarks','user_id','date'])
            ->make(true);   
        }else{
            return view('ben-application-status/app-result');
        }
    }
    public function ifmsPayment(Request $request)
    {
        $ben_id = $request->ben_id;
        if ($request->ajax()) {
            $ifms_payment_res = "select tld.*,s.scheme_name from ifms.transaction_lot_details tld join public.m_scheme s on tld.scheme_id = s.id where tld.pension_id= ".$ben_id." union all select tldr.*,sc.scheme_name from ifms.transaction_lot_details_report tldr join public.m_scheme sc on tldr.scheme_id = sc.id where tldr.pension_id= ".$ben_id;
            $ifms_payment = DB::connection('pgsql_mis')->select($ifms_payment_res);
            return datatables()->of($ifms_payment)
            ->addIndexColumn()
            ->addColumn('drn_part', function($ifms_payment){

                return $ifms_payment->drn_part;
            })
            ->addColumn('pension_id', function($ifms_payment){

                return $ifms_payment->pension_id;
            })
            ->addColumn('name', function($ifms_payment){

                return $ifms_payment->name;
            })
            ->addColumn('scheme', function($ifms_payment){

               return $ifms_payment->scheme_name;
            })
            ->addColumn('acc_no', function($ifms_payment){

               return $ifms_payment->acc_no;
            })
            ->addColumn('ifsc_code', function($ifms_payment){

                return $ifms_payment->ifsc;
            })
            ->addColumn('mobile_no', function($ifms_payment){

                return $ifms_payment->mobile_no;
            })
            ->addColumn('ifms_status', function($ifms_payment){

                return $ifms_payment->ifms_status;
            })
            ->addColumn('is_active', function($ifms_payment){

                return $ifms_payment->is_active;
            })
            ->addColumn('wrongdata_flag', function($ifms_payment){

                return $ifms_payment->wrongdata_flag;
            })
            ->addColumn('utr_no', function($ifms_payment){

                return $ifms_payment->utr_no;
            })
            ->addColumn('paid_yymm', function($ifms_payment){

                return $ifms_payment->paid_yymm;
            })
            ->addColumn('updated_at', function($ifms_payment){

                $updated_at = $ifms_payment->updated_at;
                $onlyDate = explode(' ', $updated_at);
                return date("d-m-Y", strtotime($onlyDate[0]));
            })
            ->rawColumns(['drn_part','pension_id','name','scheme','acc_no','ifsc_code','mobile_no','ifms_status','is_active','wrongdata_flag','utr_no','paid_yymm','updated_at'])
            ->make(true);   
        }else{
            return view('ben-application-status/app-result');
        }
    }
    public function sbiPayment(Request $request)
    {
        $ben_id = $request->ben_id;
        if ($request->ajax()) {
            $sbi_payment_res = "select tld.*,s.scheme_name from sbi.transaction_lot_details tld join public.m_scheme s on tld.scheme_id = s.id where tld.pension_id= ".$ben_id." union all select tldr.*,sc.scheme_name from sbi.transaction_lot_details_report tldr join public.m_scheme sc on tldr.scheme_id = sc.id where tldr.pension_id= ".$ben_id;
            $sbi_payment = DB::connection('pgsql_mis')->select($sbi_payment_res);
            return datatables()->of($sbi_payment)
            ->addIndexColumn()
            ->addColumn('lot_no', function($sbi_payment){

                return $sbi_payment->lot_no;
            })
            ->addColumn('pension_id', function($sbi_payment){

                return $sbi_payment->pension_id;
            })
            ->addColumn('name', function($sbi_payment){

                return $sbi_payment->name;
            })
            ->addColumn('scheme', function($sbi_payment){

               return $sbi_payment->scheme_name;
            })
            ->addColumn('acc_no', function($sbi_payment){

               return $sbi_payment->account_credit;
            })
            ->addColumn('ifsc_code', function($sbi_payment){

                return $sbi_payment->ifsc_code_credit;
            })
            ->addColumn('status_code', function($sbi_payment){

                return $sbi_payment->status_code;
            })
            ->addColumn('is_active', function($sbi_payment){

                return $sbi_payment->is_active;
            })
            ->addColumn('paid_yymm', function($sbi_payment){

                return $sbi_payment->paid_yymm;
            })
            ->addColumn('updated_at', function($sbi_payment){

                $updated_at = $sbi_payment->updated_at;
                $onlyDate = explode(' ', $updated_at);
                return date("d-m-Y", strtotime($onlyDate[0]));
            })
            ->rawColumns(['lot_no','pension_id','name','scheme','acc_no','ifsc_code','status_code','is_active','paid_yymm','updated_at'])
            ->make(true);   
        }else{
            return view('ben-application-status/app-result');
        }
    }
    public function lotMaster(Request $request)
    {
        $ben_id = $request->ben_id;
        if ($request->ajax()) 
        {
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

            $query = "select lm.*,lt.lot_type from lot_master lm join m_lot_type lt on lm.lot_type_id = lt.id where lot_no::int in(";
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
            
            if ((!empty($ifmsObj) && empty($sbiObj)) || (empty($ifmsObj) && !empty($sbiObj)) || (!empty($ifmsObj) && !empty($sbiObj))) 
            {
                $lot_mas = DB::connection('pgsql_mis')->select($query);
                //print_r($lot_mas);die();
                return datatables()->of($lot_mas)
                ->addIndexColumn()
                ->addColumn('lot_no', function($lot_mas){

                    return $lot_mas->lot_no;
                })
                ->addColumn('lot_month', function($lot_mas){

                    return $lot_mas->lot_month;
                })
                ->addColumn('lot_year', function($lot_mas){

                    return $lot_mas->lot_year;
                })
                ->addColumn('rbi_success_count', function($lot_mas){

                   return $lot_mas->rbi_success_count;
                })
                ->addColumn('rbi_failed_count', function($lot_mas){

                   return $lot_mas->rbi_failed_count;
                })
                ->addColumn('ref_no', function($lot_mas){

                    return $lot_mas->ref_no;
                })
                ->addColumn('lot_type_id', function($lot_mas){

                    return $lot_mas->lot_type;
                })
                ->addColumn('payment_mode', function($lot_mas){

                    return $lot_mas->payment_mode;
                })
                ->addColumn('repeat_lot', function($lot_mas){

                    return $lot_mas->repeat_lot;
                })
                ->addColumn('repeat_drn_part', function($lot_mas){

                    return $lot_mas->repeat_drn_part;
                })
                ->rawColumns(['lot_no','lot_month','lot_year','rbi_success_count','rbi_failed_count','ref_no','lot_type_id','payment_mode','repeat_lot','repeat_drn_part'])
                ->make(true);   
            }
        }else{
            return view('ben-application-status/app-result');
        }        
    }
    public function duplicateBeneficiary(Request $request)
    {
        $ben_id = $request->ben_id;
        if ($request->ajax()) {
            $duplicate_ben_res = "select *,b.next_level_role_id,b.lot_generated,b.payment_count,b.last_paid_yymm,b.bank_edited,u.username,s.scheme_name,dist.district_name
            from public.duplicate_approve_reject d 
            join pension.beneficiaries b on b.id=d.original_application_id
            join public.users u on u.id=d.rejected_user_id
            join public.m_scheme s on d.scheme_id = s.id
            join public.m_district dist on dist.district_code = d.dist_code
            where d.original_approve_application_id= ".$ben_id;
            $duplicate_ben = DB::connection('pgsql_mis')->select($duplicate_ben_res);
            return datatables()->of($duplicate_ben)
            ->addIndexColumn()
            ->addColumn('pension_id', function($duplicate_ben){

                return $duplicate_ben->original_application_id;
            })
            ->addColumn('ben_fname', function($duplicate_ben){

                return $duplicate_ben->ben_fname;
            })
            ->addColumn('ben_mname', function($duplicate_ben){

                return $duplicate_ben->ben_mname;
            })
            ->addColumn('ben_lname', function($duplicate_ben){

               return $duplicate_ben->ben_lname;
            })
            ->addColumn('father_fname', function($duplicate_ben){

               return $duplicate_ben->father_fname;
            })
            ->addColumn('father_mname', function($duplicate_ben){

                return $duplicate_ben->father_mname;
            })
            ->addColumn('father_lname', function($duplicate_ben){

                return $duplicate_ben->father_lname;
            })
            ->addColumn('epic_voter_id', function($duplicate_ben){

                return $duplicate_ben->epic_voter_id;
            })
            ->addColumn('ration_card_cat', function($duplicate_ben){

                return $duplicate_ben->ration_card_cat;
            })
            ->addColumn('ration_card_no', function($duplicate_ben){

                return $duplicate_ben->ration_card_no;
            })
            ->addColumn('dist_code', function($duplicate_ben){

                return $duplicate_ben->district_name;
            })
            ->addColumn('scheme', function($duplicate_ben){

                return $duplicate_ben->scheme_name;
            })
            ->addColumn('block_ulb_name', function($duplicate_ben){

                return $duplicate_ben->block_ulb_name;
            })
            ->addColumn('next_level_role_id', function($duplicate_ben){

                return $duplicate_ben->next_level_role_id;
            })
            ->addColumn('lot_generated', function($duplicate_ben){

                return $duplicate_ben->lot_generated;
            })
            ->addColumn('bank_edited', function($duplicate_ben){

                return $duplicate_ben->bank_edited;
            })
            ->addColumn('payment_count', function($duplicate_ben){

                return $duplicate_ben->payment_count;
            })
            ->addColumn('last_paid_yymm', function($duplicate_ben){

                return $duplicate_ben->last_paid_yymm;
            })
            ->addColumn('username', function($duplicate_ben){

                return $duplicate_ben->username;
            })
            ->addColumn('rejected_user_id', function($duplicate_ben){

                return $duplicate_ben->rejected_user_id;
            })
            ->rawColumns(['pension_id','ben_fname','ben_mname','ben_lname','father_fname','father_mname','father_lname','epic_voter_id','ration_card_cat','ration_card_no','dist_code','scheme','block_ulb_name','next_level_role_id','bank_edited','payment_count','last_paid_yymm','username','rejected_user_id'])
            ->make(true);   
        }else{
            return view('ben-application-status/app-result');
        }
    }
    public function sbiTransaction(Request $request)
    {
        $ben_id = $request->ben_id;
        if ($request->ajax()) {
            $sbilotObj = DB::connection('pgsql_mis')->table('sbi.transaction_lot_details')->where('pension_id',$ben_id)->orderBy('id','desc')->first();
            if (empty($sbilotObj)) {
                $sbilotObj = DB::connection('pgsql_mis')->table('sbi.transaction_lot_details_report')->where('pension_id',$ben_id)->orderBy('id','desc')->first();
            }
            $sbi_transaction_lot_res = "select * from sbi.transaction_lot where lot_no= '".$sbilotObj->lot_no."'";
            $sbi_transaction_lot = DB::connection('pgsql_mis')->select($sbi_transaction_lot_res);
            return datatables()->of($sbi_transaction_lot)
            ->addIndexColumn()
            ->addColumn('lot_no', function($sbi_transaction_lot){

                return $sbi_transaction_lot->lot_no;
            })
            ->addColumn('lot_month', function($sbi_transaction_lot){

                return $sbi_transaction_lot->lot_month;
            })
            ->addColumn('lot_year', function($sbi_transaction_lot){

                return $sbi_transaction_lot->lot_year;
            })
            ->addColumn('success_count', function($sbi_transaction_lot){

               return $sbi_transaction_lot->success_count;
            })
            ->addColumn('failed_count', function($sbi_transaction_lot){

               return $sbi_transaction_lot->failed_count;
            })
            ->addColumn('lot_status', function($sbi_transaction_lot){

                return $sbi_transaction_lot->lot_status;
            })
            ->rawColumns(['lot_no','lot_month','lot_year','success_count','failed_count','lot_status'])
            ->make(true);   
        }else{
            return view('ben-application-status/app-result');
        }
    }
    public function ifmsTransaction(Request $request)
    {
        $ben_id = $request->ben_id;
        if ($request->ajax()) {
            $ifmslotObj = DB::connection('pgsql_mis')->table('ifms.transaction_lot_details')->where('pension_id',$ben_id)->orderBy('id','desc')->first();
            if (empty($ifmslotObj)) {
                $ifmslotObj = DB::connection('pgsql_mis')->table('ifms.transaction_lot_details_report')->where('pension_id',$ben_id)->orderBy('id','desc')->first();
            }
            $ifms_transaction_lot_res = "select * from ifms.transaction_lot where lot_no= '".$ifmslotObj->drn_part."'";
            $ifms_transaction_lot = DB::connection('pgsql_mis')->select($ifms_transaction_lot_res);
            return datatables()->of($ifms_transaction_lot)
            ->addIndexColumn()
            ->addColumn('lot_no', function($ifms_transaction_lot){

                return $ifms_transaction_lot->lot_no;
            })
            ->addColumn('lot_month', function($ifms_transaction_lot){

                return $ifms_transaction_lot->lot_month;
            })
            ->addColumn('lot_year', function($ifms_transaction_lot){

                return $ifms_transaction_lot->lot_year;
            })
            ->addColumn('ifms_wrongdata_count', function($ifms_transaction_lot){

               return $ifms_transaction_lot->ifms_wrongdata_count;
            })
            ->addColumn('rbi_success_count', function($ifms_transaction_lot){

               return $ifms_transaction_lot->rbi_success_count;
            })
            ->addColumn('rbi_failed_count', function($ifms_transaction_lot){

                return $ifms_transaction_lot->rbi_failed_count;
            })
            ->addColumn('lot_status', function($ifms_transaction_lot){

                return $ifms_transaction_lot->lot_status;
            })
            ->addColumn('file_name', function($ifms_transaction_lot){

                return $ifms_transaction_lot->file_name;
            })
            ->rawColumns(['lot_no','lot_month','lot_year','ifms_wrongdata_count','rbi_success_count','rbi_failed_count','lot_status','file_name'])
            ->make(true);   
        }else{
            return view('ben-application-status/app-result');
        }
    }
}
