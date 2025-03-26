<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UpdateBenDetails;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Configduty;
use App\getModelFunc;
use App\LotMaster;
use App\LotDetails;
use App\AvLotmaster;
use App\AvLotdetails;
use App\FailedBankDetails;
use App\UrbanBody;
use App\GP;
use App\BankDetails;
use App\DataSourceCommon;
use Maatwebsite\Excel\Facades\Excel;
use App\DocumentType;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;
use Carbon\Carbon;
use App\District;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use App\DsPhase;
use App\Scheme;
use App\RejectRevertReason;
use App\AcceptRejectInfo;
use App\Traits\TraitAadharValidate;
use App\Helpers\AuthChecker;
use App\Helpers\DupCheck;
use DateTime;
class ValidationCorrectionPendingController extends Controller
{
    use TraitAadharValidate;
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        $this->ben_status = -97;
        date_default_timezone_set('Asia/Kolkata');
        $this->failed_table ='payment.failed_payment_details';
        $this->payment_table ='payment.ben_payment_details';
        $this->min_score=0;
        $this->max_score=25;
    }
    private function getSchemaName($scheme_id)
    {
        if (!is_null($scheme_id)) {
            $sObj = Scheme::select('id', 'short_code')
                ->where('id', '=', $scheme_id)
                ->first();
            //$parameter['scheme_id'] = $scheme_id;
            $schema_name = $sObj->short_code;
            //dd($schema_name);
            if (empty($schema_name)) {
                $schema_name = 'pension';
            }
            $table_name = 'pension.beneficiaries';
        } else {
            $table_name = 'pension.beneficiaries';
        }
        return $table_name;
    }
    public function marking(){
        $user_id = AuthChecker::getUserId();
        $designation = Auth::user()->designation_id;
        $mapObj = DB::connection('pgsql_mis')
            ->table('public.duty_assignement')
            ->where('user_id', $user_id)
            ->where('is_active', 1)
            ->first();
        $time_arr = ['11' => '11:00 AM', '12' => '12:00 PM', '13' => '01:00 PM', '14' => '02:00 PM', '15' => '03:00 PM', '16' => '04:00 PM'];
        $scheme = DB::connection('pgsql_mis')->select(
                'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
                    $user_id .
                    ' and is_active=1) and id in(2,10,11) order by scheme_name'
        ); 
        if (AuthChecker::VerifierPermission()) {
            if (count($scheme) > 0) {
                if ($mapObj->is_urban == 1) {
                    
                    $urban_body_code = $mapObj->urban_body_code;
                    $urban_bodys = UrbanBody::where(
                        'sub_district_code',
                        $urban_body_code
                    )
                        ->select('urban_body_code', 'urban_body_name')
                        ->get();
                    return view('validation-correction-pending/marking', [
                        'schemes' => $scheme,
                        'mapLevel' => $mapObj->mapping_level . $designation,
                        'urban_bodys' => $urban_bodys,
                        'local_body_code' => $urban_body_code,
                        'district_code' => $mapObj->district_code,
                        'time' => $time_arr
                    ]);
                } else {
                    $taluka_code = $mapObj->taluka_code;
                    $gps = GP::where('block_code', $taluka_code)
                        ->select('gram_panchyat_code', 'gram_panchyat_name')
                        ->get();
                    return view('validation-correction-pending/marking', [
                        'schemes' => $scheme,
                        'mapLevel' => $mapObj->mapping_level . $designation,
                        'gps' => $gps,
                        'local_body_code' => $taluka_code,
                        'district_code' => $mapObj->district_code,
                        'time' => $time_arr
                    ]);
                }
            } else {
                return redirect('/')->with(
                    'success',
                    'User disabled. No scheme assign to this user'
                );
            }
        }else {
            return redirect('/')->with('success', 'UnAuthorized');
        }    
    }

    public function marking_listing(Request $request){
        //  dd($request->all());
        if ($request->ajax()) {
            $scheme_id = $request->scheme_id;
            $local_body_code = $request->local_body;
            $dist_code = $request->district_code; 
            $mapLevel= $request->mapLevel;
            $operation_type = $request->operation_type;
            $invitation_date = $request->invitation_date;
            $currentDateOnly = Carbon::now()->toDateString();
            // dd($mapLevel);
            if ($mapLevel == 'BlockVerifier') {
                if (!empty($request->filter_1)) {
                $query= "select * from
                (select T.* from
                (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                  edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                 JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                 LEFT JOIN 
                     (
                       select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                         union all
                       select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                      ) bu ON bu.block_ulb_code=b.block_ulb_code  
                LEFT JOIN 
                      (
                        select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                         union all
                        select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                        ) gw ON gw.gp_ward_code=b.gp_ward_code 
                         WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.failed_type = 2 and b.legacy_validation = 1 and f.if_previous_approve=0 and f.matching_score between ".$this->min_score." and ".$this->max_score." and b.gp_ward_code=".$request->filter_1." ";
                }else{
                    $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                     edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                     JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                     LEFT JOIN 
                         (
                           select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                             union all
                           select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                          ) bu ON bu.block_ulb_code=b.block_ulb_code  
                    LEFT JOIN 
                          (
                            select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                             union all
                            select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                            ) gw ON gw.gp_ward_code=b.gp_ward_code 
                             WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and f.if_previous_approve=0 and b.acc_validated = 4 and f.failed_type = 2 and b.legacy_validation =1 and f.matching_score between ".$this->min_score." and ".$this->max_score."";
                }
                //  dd( $data);
            }elseif ($mapLevel == 'SubdivVerifier'){
                if (!empty($request->filter_1) && empty($request->filter_2)) {
                    $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                    edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                     JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                     LEFT JOIN 
                         (
                           select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                             union all
                           select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                          ) bu ON bu.block_ulb_code=b.block_ulb_code  
                    LEFT JOIN 
                          (
                            select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                             union all
                            select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                            ) gw ON gw.gp_ward_code=b.gp_ward_code 
                             WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and f.if_previous_approve=0 and b.acc_validated = 4 and f.failed_type = 2 and b.legacy_validation =1 and f.matching_score between ".$this->min_score." and ".$this->max_score." and  b.block_ulb_code=".$request->filter_1."";
                }elseif (!empty($request->filter_1) && !empty($request->filter_2)){
                    $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                    edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                     JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                     LEFT JOIN 
                         (
                           select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                             union all
                           select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                          ) bu ON bu.block_ulb_code=b.block_ulb_code  
                    LEFT JOIN 
                          (
                            select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                             union all
                            select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                            ) gw ON gw.gp_ward_code=b.gp_ward_code 
                             WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and f.if_previous_approve=0 and b.acc_validated = 4 and f.failed_type = 2 and b.legacy_validation =1 and f.matching_score between ".$this->min_score." and ".$this->max_score." and b.block_ulb_code=".$request->filter_1." and b.gp_ward_code=".$request->filter_2."";
                }else{
                    $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                    edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                     JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                     LEFT JOIN 
                         (
                           select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                             union all
                           select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                          ) bu ON bu.block_ulb_code=b.block_ulb_code  
                    LEFT JOIN 
                          (
                            select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                             union all
                            select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                            ) gw ON gw.gp_ward_code=b.gp_ward_code 
                             WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and f.if_previous_approve=0 and b.acc_validated = 4 and f.failed_type = 2 and b.legacy_validation = 1 and f.matching_score between ".$this->min_score." and ".$this->max_score."";
                }
            }elseif ($mapLevel == 'DistrictApprover' && ($scheme_id == 8 || $scheme_id == 9)){
                $query ="select * from
                (select T.* from
                (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                 JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                LEFT JOIN 
                     (
                       select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                         union all
                       select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                      ) bu ON bu.block_ulb_code=b.block_ulb_code  
                LEFT JOIN 
                      (
                        select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                         union all
                        select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                        ) gw ON gw.gp_ward_code=b.gp_ward_code 
                         WHERE f.scheme_id=".$scheme_id." and f.dist_code=".$dist_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.if_previous_approve=0 and f.failed_type = 2 and b.legacy_validation =1 and f.matching_score between ".$this->min_score." and ".$this->max_score."";
            }else{
                return redirect('/')->with('success', 'UnAuthorized');
            }
            if($operation_type == 1){
                $query .= " and f.visiting_time is not null";
            }
            if($operation_type == 2){
                $query .= " and f.visiting_time is null";
            }
            if(!empty($invitation_date)){
                $query .= " and f.visiting_time::date = '".$invitation_date."'";
            }
            $query .= "  order by gp_ward_name ";
            $data = DB::connection('pgsql_paywrite')->select($query);
            // dd($data);
            return datatables()
            ->of($data)
            ->addColumn('view', function ($data) use($currentDateOnly)  {
                if (!empty($data->visiting_time)) {
                    if (date('Y-m-d', strtotime($data->visiting_time)) <= $currentDateOnly) {
                        return '<button onclick=editFunction(' .
                        $data->ben_id .
                        ',' .
                        $data->scheme_id .
                        ') class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i>Edit</button>';
                    } 
            } else {
                    return '';
                }
            })
            ->addColumn('download', function ($data) use($currentDateOnly) {
                if (!empty($data->visiting_time)) {
                    $html = '<div class="btn-group" role="group" aria-label="Basic example">';
                    $html .= '<button onclick="benDownloadAssignFunction(' . $data->ben_id . ',' . $data->scheme_id . ')" class="btn btn-info btn-xs" style="margin-right: 5px;"><i class="fa fa-download"></i>ILD</button>';
                    if ($data->process_complete == 0) {
                    $html .= '<button onclick="processMark(' . $data->ben_id . ',' . $data->scheme_id . ')" class="btn btn-xs btn-primary btn-xs" style="margin-right: 5px;"><i class="glyphicon glyphicon-edit"></i>PAM</button>';
                    }
                    // Convert visiting_time to date and compare
                    if (date('Y-m-d', strtotime($data->visiting_time)) <= $currentDateOnly) {
                        $html .= '<button onclick="benDownloadFunction(' . $data->ben_id . ',' . $data->scheme_id . ')" class="btn btn-success btn-xs" style="margin-right: 5px;">CNVFD(বাংলা)</button>';
                        $html .= '<button onclick="engDownloadFunction(' . $data->ben_id . ',' . $data->scheme_id . ')" class="btn btn-warning btn-xs">CNVFD(Eng)</button>';
                    }
                    $html .= '</div>';
                    return $html;
                } 
                  
            })
            ->addColumn('ben_id', function ($data) {
                return $data->ben_id;
            })
            ->addColumn('ben_name', function ($data) {
                return $data->ben_name;  
            })
            ->addColumn('mobile_no', function ($data) {
                return $data->mobile_no;  
            })
            // ->addColumn('last_accno', function ($data) {
            //     return $data->last_accno;
            // })
            // ->addColumn('last_ifsc', function ($data) {
            //     return $data->last_ifsc;
            // })
            // ->addColumn('block_ulb_name', function ($data) {
            //     return $data->block_ulb_name;
            // })
            // ->addColumn('visiting_time', function ($data) {
            //     $dateArr = explode(' ', $data->visiting_time);
            //     return date("d/m/Y", strtotime($dateArr[0])).' '.$data->tagging_time;
            // })
            ->addColumn('visiting_time', function ($data) {
                if (!empty($data->visiting_time)) {
                    $dateArr = explode(' ', $data->visiting_time);
                    return date("d/m/Y", strtotime($dateArr[0])).' '.$data->tagging_time;
                }else {
                    return 'Date Not Assigned';
                }
            })
            ->addColumn('gp_ward_name', function ($data) {
                return $data->gp_ward_name;
            })
            ->addColumn('bank_response_name', function ($data) {
                return trim($data->av_name_response);
            })
            ->addColumn('process_type', function ($data) {
                if ($data->process_complete == 1){
                    return 'Bank name may be taken as Beneficiary Name as Bank Name is correct';
                }
                if ($data->process_complete == 2){
                    return 'Passbook Correction Required.';
                }
                if ($data->process_complete == 3){
                    return 'Bank Account is of other Family Members, New Account Number required.';
                }
                if ($data->process_complete == 4){
                    return 'Bank account is of completely of other person out of family. New Account Number required.';
                }
            })
            ->rawColumns(['view','download','ben_id', 'name','visiting_time','bank_response_name'])
                ->make(true);
        }
    }

    public function view(Request $request){
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $id = $request->id;
            $scheme_id = $request->scheme_id;
            $table = $this->getSchemaName($scheme_id);
            $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
            ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
            ->where('f.edited_status',0)->where('ben.is_eligible',true)
            ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)->where('f.failed_type', 2)->where('ben.acc_validated',4)
            ->where('ben.legacy_validation',1)
            ->whereBetween('f.matching_score', [$this->min_score, $this->max_score])
            ->where('f.ben_id', $id)
            ->first();
            $bank_details = BankDetails::where(
                'ifsc',
                trim($ben_details->last_ifsc)
            )
                ->where('is_active', 1)
                ->get(['bank', 'branch', 'bank_code'])
                ->first();
                if ($ben_details == null) {
                    return $response = [
                        'status' => 1,
                        'msg' => 'Somethimg went wrong.',
                        'type' => 'red',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ];
                } else {
                    $av_name_msg = 'NOTE: Beneficiary Name '.trim($ben_details->ben_name).' will be replace with the name response from bank '.trim($ben_details->av_name_response).'';
                    $ben_arr = [
                        'ben_name' => trim($ben_details->ben_name),
                        'id' => $ben_details->ben_id,
                        'scheme_id' => $ben_details->scheme_id,
                        'mobile_no' => $ben_details->mobile_no,
                        'caste' => trim($ben_details->caste),
                        'gender' => trim($ben_details->gender),
                        'bank_code' => trim($ben_details->last_accno),
                        'bank_ifsc' => trim($ben_details->last_ifsc),
                        'bank_name' => trim($bank_details->bank),
                        'branch_name' => trim($bank_details->branch),
                        // 'av_name_response' => (trim($ben_details->av_name_response) != null || trim($ben_details->av_name_response) != '')? trim($ben_details->av_name_response):'No name received from bank',
                        'av_name_response' => trim($ben_details->av_name_response),
                        'av_name_msg' => $av_name_msg,
                        'process_complete' => $ben_details->process_complete,
                    ];
                    if(trim($ben_details->av_status_code) !=NULL || trim($ben_details->av_status_code) != ''){
                        $failed_reason = trim($ben_details->av_status_code);
                    }else{
                        $failed_reason = NULL;
                    }
                    $response = array_merge($ben_arr, [
                        'status' => 2,
                        'failed_reason' => $failed_reason 
                    ]);
                }  
              
                
        } catch (\Exception $e) {
            $response = [
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' =>
                    'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
        }finally {
            return response()->json($response, $statusCode);
        }
    }
    public function verify(Request $request){
            //   dd($request->all());
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
              $rules = [
                 'bank_name' => 'required',
                 'branch_name' => 'required',
                 'bank_code' =>
                     'required|numeric|between:00000000000000000000,9999999999999999999',
                 'bank_ifsc' => 'required|max:20',
                 'remarks' => 'required',
             ];
              $attributes = [
                'bank_name' => 'Bank name',
                'branch_name' => 'Branch name',
                'bank_ifsc' => 'IFSC',
                'bank_code' => 'A/c No',
                'upload_bank_passbook' => 'upload_bank_passbook',
                'remarks' => 'remarks'
             ];
               $messages = [
                 'required' => 'The :attribute field is required.',
                 'integer' => 'Only integer allowed for :attribute',
                 'max' => 'Maximum of :size characters allowed for :attribute',
                 'size' => 'The :attribute must be exactly :size.',
               
             ];
              $validator = Validator::make(
                $request->all(),
                $rules,
                $messages,
                $attributes
             );
            $is_validation =0;
            $process_type=$request->process_type;
            if($process_type == 143 ||  $process_type == 144) 
            {
                if ($validator->passes()) {
                    $is_validation =1;
                }else{
                    $return_status = 0;
                    $return_msg = $validator->errors()->all();
                    $response = [
                        'status' => $return_status,
                        'msg' => $return_msg,
                        'type' => 'red',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ];
                }
            }else{
                $is_validation =1;
            }
            $user_id = AuthChecker::getUserId();
            $mapObj = DB::connection('pgsql_mis')
                ->table('public.duty_assignement')
                ->where('user_id', $user_id)
                ->where('is_active', 1)
                ->first();
            // dd( $mapObj);    
            $district_code = $mapObj->district_code;
            if ($mapObj->mapping_level == "Subdiv") {
                $created_by_local_body_code = $mapObj->urban_body_code;
            }else if ($mapObj->mapping_level == "Block") {
                $created_by_local_body_code = $mapObj->taluka_code;
            }
            $mappingLevel = $mapObj->mapping_level;
            $id = $request->id;
            $scheme_id = $request->scheme_id;
            $remarks = $request->remarks;
            $ip_address = request()->ip();
            $table = $this->getSchemaName($scheme_id);
            $benDetails = DB::connection('pgsql_mis')
                ->table($table)
                ->where('id', $id)
                ->first();  
            $scheme_list = Config::get(
                'constants.duplicate_bank_info_check'
            );
            $payment_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
            ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
            ->where('f.edited_status',0)->where('ben.is_eligible',true)
            ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)->where('f.failed_type', 2)->where('ben.acc_validated',4)
            ->whereBetween('f.matching_score', [$this->min_score, $this->max_score])
            ->where('ben.legacy_validation',1)
            ->where('f.ben_id', $id)
            ->first();
            $new_value = [];
            $old_value = [];
            $failed_payment = [];
            if( $process_type == 143 ||  $process_type == 144){
                $new_bank_ifsc = $request->bank_ifsc;
                $new_bank_code = $request->bank_code;
                $new_bank_name = $request->bank_name;
                $new_branch_name = $request->branch_name;
                $bank_details = BankDetails::where(
                    'ifsc',
                    trim($new_bank_ifsc)
                )
                    ->where('is_active', 1)
                    ->get(['bank', 'branch', 'bank_code'])
                    ->first();
                    if($bank_details == NULL){
                        return $response = [
                            'status' => 1,
                            'msg' => 'IFSC not found',
                            'type' => 'red',
                            'icon' => 'fa fa-warning',
                            'title' => 'Warning!!',
                        ];
                }
                $new_bank_code_npci = $bank_details->bank_code;
                $BankCheck = DupCheck::dupBankCheckSame($scheme_id, $new_bank_code, $id);
                // dd($BankCheck);
                if ($BankCheck) {
                    return $response = [
                        'status' => 3,
                        'msg' => 'Bank account is Duplicate ',
                        'type' => 'blue',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ]; 
                }
                $BankCheckCross = DupCheck::dupBankCheckCross($scheme_id, $new_bank_code, $id);
                if ($BankCheckCross) {
                    return $response = [
                        'status' => 3,
                        'msg' => 'Bank account is Duplicate with Cross Scheme ',
                        'type' => 'blue',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ]; 
                }
                $legacy_validation_dup_count = DB::connection('pgsql_paywrite')->table('payment.validation_failed_modified')->where('ben_id','!=', $id)->where('modified_accno',$new_bank_code)->where('scheme_id',$scheme_id)->count();
                if($legacy_validation_dup_count >0){
                    return $response = [
                        'status' => 3,
                        'msg' => 'Bank Account Number Already Processed within another Beneficiary',
                        'type' => 'red',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ];
                }
                if ($benDetails == null) {
                    return $response = [
                        'status' => 3,
                        'msg' => 'Somethimg went wrong.',
                        'type' => 'red',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ];
                }else{
                    $old_bank_name = $benDetails->bank_name;
                    $old_branch_name = $benDetails->branch_name;
                    $old_bank_ifsc = $benDetails->bank_ifsc;
                    $old_bank_code = $benDetails->bank_code;
                }
                $old_value['old_bank_name'] = trim($old_bank_name);
                $old_value['old_branch_name'] = trim($old_branch_name);
                $old_value['old_bank_ifsc'] = trim($old_bank_ifsc);
                $old_value['old_bank_code'] = trim($old_bank_code);

                $new_value['new_bank_name'] = trim($new_bank_name);
                $new_value['new_branch_name'] = trim($new_branch_name);
                $new_value['new_bank_ifsc'] = trim($new_bank_ifsc);
                $new_value['new_bank_code'] = trim($new_bank_code);
                $new_value['npci_bank_code'] = trim($new_bank_code_npci); 
                $old_data = json_encode($old_value);
                $new_data = json_encode($new_value);
                // $updateBenDetailsData1 = ['old_data'=>json_encode($old_value),'new_data'=>json_encode($new_value)];

            }
            $failed_payment['edited_status'] = 1;
            $failed_payment['updated_at'] = date('Y-m-d H:i:s');

            if($process_type == 141){
                $msg = "Beneficiary name updated with ".$payment_details->av_name_response." & send to Approver for approval";
                $failed_payment['failed_process_type'] = 141; 
                $ben_name['ben_name'] = $payment_details->ben_name;
                $av_name_response['ben_name']= trim($payment_details->av_name_response);
                $failed_payment['updated_details'] = json_encode($av_name_response);
                $old_data = json_encode($ben_name);
                $new_data = json_encode($av_name_response);
                // $updateBenDetailsData1 = ['old_data'=>json_encode($ben_name),'new_data'=>json_encode($av_name_response)];
                // $msg='Beneficiary Name '.$payment_details->ben_name.' will be replace with the name response from bank '.$payment_details->av_name_response.'';
            }else if($process_type == 142){
                $msg = "Passbook uploaded successfully & send to Approver for approval";
                $failed_payment['failed_process_type'] = $process_type; 
            }else if($process_type == 143){
                $msg = "Bank details updated successfully & send to Approver for approval";
                $failed_payment['failed_process_type'] = $process_type; 
                $failed_payment['updated_details'] = json_encode($new_value);
            } else{
                $msg = "Bank details updated successfully & send to Approver for approval";
                $failed_payment['failed_process_type'] = $process_type; 
                $failed_payment['updated_details'] = json_encode($new_value);
            }
            
            $c_time = date('Y-m-d H:i:s', time());
            $accept_reject_model = new AcceptRejectInfo;
            $accept_reject_model->application_id = $id;
            $accept_reject_model->created_at = $c_time;
            $accept_reject_model->scheme_id = $scheme_id;
            if($process_type == 141 ||$process_type == 143 ||$process_type == 144){
                $accept_reject_model->old_data = $old_data;
                $accept_reject_model->new_data = $new_data;
            }
            $accept_reject_model->user_id = $user_id;
            $accept_reject_model->created_by_dist_code = $district_code;
            $accept_reject_model->created_by_local_body_code = $created_by_local_body_code;
            $accept_reject_model->ip_address = request()->ip();
            $accept_reject_model->remarks = $remarks;
            $accept_reject_model->op_type = $process_type;
            $is_document_uploaded = 0;
            $is_upload = 0;
            $bank_passbook = $request->file('upload_bank_passbook');
            $aadhar_card = $request->file('upload_aadhar_card');
            $application_form = $request->file('upload_application_form');
            DB::beginTransaction();
            DB::connection('pgsql_paywrite')->beginTransaction();
            DB::connection('pgsql_encwrite')->beginTransaction();
            $is_saved_log = $accept_reject_model->save();
            if (!empty($bank_passbook) && !empty($aadhar_card) && !empty($application_form)) {
                $is_document_uploaded = 1;
            } else{
                return $response = [
                    'status' => 2,
                    'msg' => 'Please upload Documents.',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Required',
                ];
            }
            if ($is_document_uploaded == 1) {
                $attributes1 = [];
                $doc_query = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->whereIn('id', [6,10,120])->get();
                $required = 'required';
                foreach ($doc_query as $doc_arr) {
                    if ($doc_arr->id == 6) {
                        $field_name = 'upload_aadhar_card';
                    } else if ($doc_arr->id == 10) {
                        $field_name = 'upload_bank_passbook';
                    } else {
                        $field_name = 'upload_application_form';
                    }
                    $attributes1[$field_name] = $doc_arr->doc_name;
                    $rules1[$field_name] = $required . '|mimes:' . $doc_arr->doc_type . '|max:' . $doc_arr->doc_size_kb . ',';
                    $messages1[$field_name.'.max'] = "The file uploaded for " . $doc_arr->doc_name . " size must be less than :max KB";
                    $messages1[$field_name.'.mimes'] = "The file uploaded for " . $doc_arr->doc_name . " must be of type " . $doc_arr->doc_type;
                    $messages1[$field_name.'.required'] = "Document for " . $doc_arr->doc_name . " must be uploaded";
                }
                $validator = Validator::make(
                    $request->all(),
                    $rules1,
                    $messages1,
                    $attributes1
                );
                if ($validator->passes()) {
                    $valid = 1;
                } else {
                    $valid = 0;
                    $return_msg = $validator->errors()->all();
                    $return_status = 0;
                    $response = [
                        'status' => 3,
                        'msg' => $return_msg,
                        'type' => 'red',
                        'icon' => 'fa fa-warning',
                        'title' => 'Error',
                    ];
                }
                if ($valid == 1) {
                    $i=0;
                    foreach ($doc_query as $doc_arr) {
                        if ($doc_arr->id == 6) {
                            $field_name = 'upload_aadhar_card';
                        } else if ($doc_arr->id == 10) {
                            $field_name = 'upload_bank_passbook';
                        } else {
                            $field_name = 'upload_application_form';
                        }
                        $upload_docs = $request->file($field_name);
                        $img_data = file_get_contents($upload_docs);
                        $extension = $upload_docs->getClientOriginalExtension();
                        $mime_type = $upload_docs->getMimeType();
                        $base64 = base64_encode($img_data);
                        $c_datetime = date('Y-m-d H:i:s', time());
                        $fun_call = DB::connection('pgsql_encwrite')->select( "SELECT jb_doc.draft_ben_docs_insert_archive(in_beneficiary_id => " . $id . ",
                            in_scheme_id => " . $scheme_id . ",in_document_type => " .$doc_arr->id .", in_attched_document => '" . $base64 ."',
                            in_created_by_level => '" . $mappingLevel . "',
                            in_created_by => " . $user_id . ",
                            in_ip_address => '" .$ip_address ."',
                            in_document_extension => '" .$extension ."',
                            in_document_mime_type => '" .$mime_type . "',
                            in_created_by_dist_code => " .$benDetails->created_by_dist_code .",
                            in_created_by_local_body_code => " . $benDetails->created_by_local_body_code .",
                            in_doc_type_name => '" .$doc_arr->doc_name . "',
                            in_datetime => '" .$c_datetime ."'
                            );"
                        );
                        $doc_upload = $fun_call[0]->draft_ben_docs_insert_archive;
                        if($doc_upload == 1){
                            $i++;
                        }
                    }
                    if($i == 3){
                       $is_upload = 1;
                    }
                }
            }
            $legacy_validation_failed = [
                'ben_id' => $id,
                'scheme_id' => $scheme_id,
                'failed_id' => 2,
                'status' => 1,
                'modified_accno' => ($process_type == 143 || $process_type == 144)? $new_bank_code:NULL,
                'modified_ifsc' => ($process_type == 143 || $process_type == 144)? $new_bank_ifsc:NULL, 
                'failed_process_type' => $process_type,
                'modify_phase' => 1,
                'verified_at' => date('Y-m-d H:i:s'),
                'verified_by' => Auth::user()->id,
                'is_legacy' => true
            ];
            if($is_validation == 1){
                if($is_upload == 1){
                    // $is_update = UpdateBenDetails::insert($updateBenDetailsData);
                    if($is_saved_log == 1){
                        $legacy_validation_insert = DB::connection('pgsql_paywrite')->table('payment.validation_failed_modified')->insert($legacy_validation_failed);
                        if($legacy_validation_insert == 1){
                            $is_saved = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')
                            ->where('ben_id', $id)
                            ->where('scheme_id', $scheme_id)
                            ->where('edited_status', 0)
                            ->where('failed_type', 2) 
                            ->whereBetween('matching_score', [$this->min_score, $this->max_score])
                            ->update($failed_payment);
                            if($is_saved){
                                DB::commit();
                                DB::connection('pgsql_encwrite')->commit();
                                DB::connection('pgsql_paywrite')->commit();
                                $response = [
                                    'status' => 1,
                                    'msg' => $msg,
                                    'type' => 'green',
                                    'icon' => 'fa fa-check',
                                    'title' => 'Success',
                                ];
                            }else{
                                DB::rollback();
                                DB::connection('pgsql_encwrite')->rollback();
                                DB::connection('pgsql_paywrite')->rollback();
                                $response = [
                                    'status' => 3,
                                    'msg' => '3 Somethimg went wrong!!',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        }else{
                            DB::rollback();
                            DB::connection('pgsql_encwrite')->rollback();
                            DB::connection('pgsql_paywrite')->rollback();
                            $response = [
                                'status' => 2,
                                'msg' => '2 Somethimg went wrong!!',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }
                    }else{
                        DB::rollback();
                        DB::connection('pgsql_encwrite')->rollback();
                        DB::connection('pgsql_paywrite')->rollback();
                        $return_status = 0;
                        $return_text =
                            'Something went wrong, please try another one...';
                        $return_msg = ['' . $return_text];
                        return $response = [
                            'status' => $return_status,
                            'msg' => $return_msg,
                            'type' => 'red',
                            'icon' => 'fa fa-warning',
                            'title' => 'Warning!!',
                        ];
                    }
                }else{
                    DB::rollback();
                        DB::connection('pgsql_encwrite')->rollback();
                        DB::connection('pgsql_paywrite')->rollback();
                        $return_status = 0;
                        $return_text =
                            'Something went wrong, please try another one...';
                        $return_msg = ['' . $return_text];
                        return $response = [
                            'status' => $return_status,
                            'msg' => $return_msg,
                            'type' => 'red',
                            'icon' => 'fa fa-warning',
                            'title' => 'Warning!!',
                    ];
                }
            }    
          
    } catch (\Exception $e) {
        // dd($e);
        $response = [
            'exception' => true,
            'exception_message' => $e->getMessage(),
            // 'exception_message' =>
            //     'Something went wrong. May be session time out logout and login again.',
        ];
        $statusCode = 400;
    }finally {
        return response()->json($response, $statusCode);
    }
    }

    public function applicationFormDownload(Request $request){
            //    dd($request->all());
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $id = $request->id;
            $gp_mun = $request->gp_mun;
            $scheme_id = $request->scheme_id;
            $invitation_date = $request->invitation_date;
            $user_id = AuthChecker::getUserId();
            $designation = Auth::user()->designation_id;
            // $type = $request->type;
            $mapObj = DB::connection('pgsql_mis')
                ->table('public.duty_assignement')
                ->where('user_id', $user_id)
                ->where('is_active', 1)
                ->first();
            // dd($mapObj);    
            if($mapObj->mapping_level == 'Block'){
                $msg = 'BDO';
            }
            if($mapObj->mapping_level =='Subdiv'){
                $msg = 'SDO';
            }
            if($mapObj->mapping_level == 'Block'){
                $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                     edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                     JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                     LEFT JOIN
                        (
                            select district_code,district_name from public.m_district  
                        ) dist ON dist.district_code=b.dist_code
                     LEFT JOIN
                        (
                            select sub_district_code,sub_district_name from public.m_sub_district sd
                                union all
                            select block_code as sub_district_code,block_name as sub_district_name from public.m_block mb
                        ) sub_dist ON sub_dist.sub_district_code=b.local_body_code
                     LEFT JOIN 
                         (
                           select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                             union all
                           select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                          ) bu ON bu.block_ulb_code=b.block_ulb_code  
                    LEFT JOIN 
                          (
                            select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                             union all
                            select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                            ) gw ON gw.gp_ward_code=b.gp_ward_code 
                             WHERE f.scheme_id=".$scheme_id." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.if_previous_approve=0 and f.failed_type = 2 and b.legacy_validation = 1 and f.visiting_time is not null and f.matching_score between ".$this->min_score." and ".$this->max_score." ";
            } else if($mapObj->mapping_level =='Subdiv'){
                $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                    edited_status=0 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                     JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                     LEFT JOIN
                        (
                            select district_code,district_name from public.m_district  
                        ) dist ON dist.district_code=b.dist_code
                    LEFT JOIN
                        (
                            select sub_district_code,sub_district_name from public.m_sub_district sd
                                union all
                            select block_code as sub_district_code,block_name as sub_district_name from public.m_block mb
                        ) sub_dist ON sub_dist.sub_district_code=b.local_body_code
                     LEFT JOIN 
                         (
                           select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                             union all
                           select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                          ) bu ON bu.block_ulb_code=b.block_ulb_code  
                    LEFT JOIN 
                          (
                            select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                             union all
                            select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                            ) gw ON gw.gp_ward_code=b.gp_ward_code 
                             WHERE f.scheme_id=".$scheme_id." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.if_previous_approve=0 and f.failed_type = 2 and b.legacy_validation = 1 and f.visiting_time is not null and f.matching_score between ".$this->min_score." and ".$this->max_score."";
            }
            if($gp_mun != NULL){
                $query .= " and gw.gp_ward_code =".$gp_mun."";
            }
            if($id != NULL){
                $query .= " and f.ben_id=".$id." and b.ben_id = ".$id."";
            }
            if($invitation_date != NULL){
                $query .= " and f.visiting_time::date='".$invitation_date."' ";
            }
            // if($type == 2){
            //     $query .= " and f.process_complete in(1,2,3,4) ";
            // }
            $ben_details = DB::connection('pgsql_paywrite')->select($query);
            //  dd($ben_details);
            $table = $this->getSchemaName($scheme_id);       
                if ($ben_details == null) {
                    return $response = [
                        'status' => 1,
                        'msg' => 'No Data Available',
                        'type' => 'red',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ];
                } else {
                    $data_array = array();
                    foreach ($ben_details as $ben) {
                        $ben_address = DB::connection('pgsql_mis')
                        ->table($table)
                        ->where('id', $ben->ben_id)
                        ->first();
                        $visitingTime = $ben->visiting_time; 
                        if (!empty($visitingTime)) {
                            $carbonDate = Carbon::parse($visitingTime);
                            $date = $carbonDate->format('d-m-Y');  
                            // $time = $carbonDate->format('h:i A');  
                            $time = $ben->tagging_time;
                        } else {
                            $date = null; 
                            $time = null; 
                        }
                        $bank_details = BankDetails::where(
                            'ifsc',
                            trim($ben->ifsc)
                        )
                            ->where('is_active', 1)
                            ->get(['bank', 'branch', 'bank_code'])
                            ->first();

                    $data = array(
                            'beneficiary_id' => $ben->ben_id,
                            'ben_name' => trim($ben->ben_name),
                            'bank_response' => (trim($ben->av_name_response) != null || trim($ben->av_name_response) != '') ? trim($ben->av_name_response) : '',
                            'scheme_id' => $ben->scheme_id,
                            'mobile_no' => (trim($ben->mobile_no) != null || trim($ben->mobile_no) != '') ? trim($ben->mobile_no) : '',
                            'district_name' => $ben->district_name,
                            'block_subdiv_name' => $ben->block_ulb_name,
                            'block_muni_name' => $ben->block_ulb_name,
                            'gp_ward_name' => $ben->gp_ward_name,
                            'bank_code' => trim($ben->last_accno),
                            'bank_ifsc' => trim($ben->last_ifsc),
                            'bank_name' => trim($bank_details->bank),
                            'branch_name' => trim($bank_details->branch),
                            'date' => $date,
                            'time' => $time,
                            'msg' => $msg,
                            "address" => (trim($ben_address->pincode) != null || trim($ben_address->pincode) != '') ? 'Pin Code: ' .trim($ben_address->pincode) : '',
                        );
                        array_push($data_array, $data);
                    }
                    //   dd($data_array);
                    $response = ['status' => 2, 'data_array' => $data_array];
                }
            
        } catch (\Exception $e) {
            $response = [
                'exception' => true,
                'exception_message' => $e->getMessage(),
            ];
            $statusCode = 400;
        }finally {
            //  dd($response);
            return response()->json($response, $statusCode);
        }

    }

    public function approver(){
        $user_id = AuthChecker::getUserId();
        $dutyObj = Configduty::where('user_id', '=', $user_id)
            ->where('is_active', 1)
            ->first();
        $distCode = $dutyObj->district_code;
        $scheme = DB::connection('pgsql_mis')->select(
                    'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
                        $user_id .
                        ' and is_active=1 and scheme_id in(2,10,11)) order by scheme_name'
        );
        if (Auth::user()->designation_id == 'Approver') {
            $levels = [
                2 => 'Rural',
                1 => 'Urban',
            ];
            return view('validation-correction-pending/approver', [
                'levels' => $levels,
                'dist_code' => $distCode,
                'schemes' => $scheme,
            ]);
        } else {
            return redirect('/')->with('success', 'Unauthorized');
        }     
    }
    public function approverList(Request $request){
        //    dd($request->all());
        $user_id = AuthChecker::getUserId();
        $dutyObj = Configduty::where('user_id', '=', $user_id)
            ->where('is_active', 1)
            ->first();
        $distCode = $dutyObj->district_code;
        $rural_urban = $request->filter_1;
        $local_body_code = $request->filter_2;
        $block_ulb_code = $request->block_ulb_code;
        $gp_ward_code = $request->gp_ward_code;
        if ($request->ajax()) {
            $scheme_id = $request->scheme_type;
            $name_validation_type = $request->update_code;
            $table_name = $this->getSchemaName($scheme_id);
            if (Auth::user()->designation_id == 'Approver' && !empty($scheme_id)) {
                if (!empty($rural_urban) && empty($local_body_code)) {
                $query ="select * from
                (select T.* from
                (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                 edited_status=1 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                 JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                 JOIN 
                     (
                       select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                         union all
                       select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                      ) bu ON bu.block_ulb_code=b.block_ulb_code  
                 JOIN 
                      (
                        select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                         union all
                        select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                        ) gw ON gw.gp_ward_code=b.gp_ward_code 
                         WHERE f.scheme_id=".$scheme_id." and f.dist_code=".$distCode." and f.edited_status=1 and b.is_eligible =true and b.is_rejected=0 and b.ben_status=1 and b.rural_urban_id=".$rural_urban." and b.acc_validated = 4 and f.failed_type = 2 and b.legacy_validation = 1 and f.if_previous_approve=0 and f.matching_score between ".$this->min_score." and ".$this->max_score." and f.failed_process_type = ".$name_validation_type." ";
                }elseif(!empty($rural_urban) && !empty($local_body_code)){
                    $query ="select * from
                (select T.* from
                (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                edited_status=1 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                 JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                 JOIN 
                     (
                       select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                         union all
                       select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                      ) bu ON bu.block_ulb_code=b.block_ulb_code  
                 JOIN 
                      (
                        select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                         union all
                        select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                        ) gw ON gw.gp_ward_code=b.gp_ward_code 
                         WHERE f.scheme_id=".$scheme_id." and f.dist_code=".$distCode." and f.edited_status=1 and b.ben_status=1 and b.is_eligible =true and b.is_rejected=0 and b.rural_urban_id=".$rural_urban." and b.local_body_code=".$local_body_code." and b.acc_validated = 4 and f.if_previous_approve=0 and f.failed_type = 2 and b.legacy_validation = 1 and f.matching_score between ".$this->min_score." and ".$this->max_score." and f.failed_process_type = ".$name_validation_type."";
                }else{
                    $query ="select * from
                (select T.* from
                (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                 edited_status=1 group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                 JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                 JOIN 
                     (
                       select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                         union all
                       select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                      ) bu ON bu.block_ulb_code=b.block_ulb_code  
                 JOIN 
                      (
                        select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                         union all
                        select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                        ) gw ON gw.gp_ward_code=b.gp_ward_code 
                         WHERE f.scheme_id=".$scheme_id." and f.dist_code=".$distCode." and f.edited_status=1 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.if_previous_approve=0 and f.failed_type = 2 and b.legacy_validation = 1 and f.matching_score between ".$this->min_score." and ".$this->max_score." and f.failed_process_type = ".$name_validation_type."";
                }
              
                $data = DB::connection('pgsql_paywrite')->select($query);        

            } else {
                $data = collect([]);
            }
            //  dd($data);
            return datatables()
                ->of($data)
                ->addIndexColumn()
                ->addColumn('view', function ($data) {
                        $action =
                        '<button class="btn btn-primary btn-xs ben_view_button" value="' .
                        $data->ben_id .
                        '_' .
                        $data->scheme_id .
                        '"><i class="glyphicon glyphicon-edit"></i>View</button>';
                    
                    return $action;
                })
                ->addColumn('check', function ($data) {
                        return '<input type="checkbox"  name="chkbx" class="all_checkbox"  onclick="controlCheckBox();" value="' .
                        $data->ben_id .
                        '_' .
                        $data->scheme_id .
                        '">';
                })
                ->addColumn('beneficiary_id', function ($data) {
                    return $data->ben_id;
                })
                ->addColumn('name', function ($data) {
                    return $data->ben_name;
                })
                ->addColumn('mobile_no', function ($data) {
                    return $data->mobile_no;
                })
                ->addColumn('last_accno', function ($data) {
                    return $data->last_accno;
                })
                ->addColumn('last_ifsc', function ($data) {
                    return $data->last_ifsc;
                })
                ->addColumn('block_ulb_name', function ($data) {
                    return $data->block_ulb_name;
                })
                ->addColumn('bank_response_name', function ($data) {
                    return $data->av_name_response;
                })
              
                ->rawColumns([
                    'view', 'beneficiary_id', 'name','mobile_no','check'
                ])
                ->make(true);
        }
    }
    public function approverView(Request $request){
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $benid = $request->benid;
            $parts = explode('_', $benid);
            $id = $parts[0];
            $scheme_id = $parts[1];
            $name_validation_type = $request->name_validation_type;
            $table_name = $this->getSchemaName($scheme_id);
            $user_id = AuthChecker::getUserId();
            $dutyObj = Configduty::where('user_id', '=', $user_id)
            ->where('is_active', 1)
            ->first();
            $distCode = $dutyObj->district_code;
            if($name_validation_type ==141){
                $header_msg ='Note:Bank Name may be taken as beneficiary name as bank name is correct.';
            }else if($name_validation_type ==142) {
                $header_msg ='Note:Passbook Corrected by Verifier.';
            }else if($name_validation_type ==143) {
                $header_msg ='Note:Bank account is of other family members, New account modified by verifier.';
            }else if($name_validation_type ==144) {
                $header_msg ='Note:Bank account is of Completely of other person out of family, New account number modified by verifier.';
            }
            $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
            ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
            ->where('f.failed_type',2)->where('f.edited_status',1)->where('ben.is_eligible',true)
            ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)
            ->where('ben.acc_validated',4)
            ->where('ben.legacy_validation',1)
            ->where('f.failed_process_type',$name_validation_type)
            ->whereBetween('f.matching_score', [$this->min_score, $this->max_score])
            ->where('f.ben_id', $id)
            ->first();
            $doc_arr = array();
            $encolserdata = DB::connection('pgsql_encwrite')->table('jb_doc.ben_attach_documents_draft')->select('document_type')->where('beneficiary_id', $id)->where('created_by_dist_code',$distCode)->get()->pluck('document_type')->toArray();
            // dd($ben_details);
            if (count($encolserdata) > 0) {
                foreach ($encolserdata as $en) {
                  array_push($doc_arr, $en);
                }
            }
            if (count($doc_arr) > 0) {
                $encloserdata = DocumentType::select('id', 'doc_name', 'is_profile_pic')->whereIn('id', $doc_arr)->get();
            }
            if (count($encloserdata) > 0) {
                $p = 0;
                $html = '';
                foreach ($encloserdata as $enc) {
                  if ($p == 0 || ($p % 2 == 0)) {
                    $html = $html . '<tr>';
                  }
                  $html = $html . '    
                  <th scope="row">' . $enc->doc_name . '</th>
                  <td  scope="row" class="encView">&nbsp;&nbsp;&nbsp;<a class="btn btn-xs btn-primary" href="javascript:void(0);" onclick="View_encolser_modal(\'' . $enc->doc_name . '\',' . $enc->id . ',' . intval($enc->is_profile_pic) . ',' . $id . ',' . $scheme_id . ')">View</a></td>';
                  if (($p % 2 == 0) && ($p % 2 != 0)) {
                    $html = $html . '</tr>';
                  }
                  $p++;
                }
            }
        
            // dd($encloserdata);
            if ($ben_details == null) {
                return $response = [
                    'status' => 1,
                    'msg' => 'Somethimg went wrong.',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            } else {
                // dd('ok');
                $decodeNewData = json_decode($ben_details->updated_details);
                $bank_details = BankDetails::where(
                    'ifsc',
                    trim($ben_details->ifsc)
                )
                    ->where('is_active', 1)
                    ->get(['bank', 'branch', 'bank_code'])
                    ->first();   
                $newBankCode = (isset($decodeNewData->new_bank_code) && $decodeNewData->new_bank_code != null) ? trim($decodeNewData->new_bank_code) : null;  
                $newBankIfsc = (isset($decodeNewData->new_bank_ifsc) && $decodeNewData->new_bank_ifsc != null) ? trim($decodeNewData->new_bank_ifsc) : null; 
                $newBankName = (isset($decodeNewData->new_bank_name) && $decodeNewData->new_bank_name != null) ? trim($decodeNewData->new_bank_name) : null;
                $newBranchName = (isset($decodeNewData->new_branch_name) && $decodeNewData->new_branch_name != null) ? trim($decodeNewData->new_branch_name) : null;
                $ben_arr = [
                    'ben_name' => $ben_details->ben_name,
                    'id' => $ben_details->ben_id,
                    'scheme_id' => $ben_details->scheme_id,
                    'caste' => trim($ben_details->caste),
                    'gender' => trim($ben_details->gender),
                    'old_bank_code' => trim($ben_details->accno),
                    'old_bank_ifsc' => trim($ben_details->ifsc),
                    'old_branch_name' => trim($bank_details->branch),
                    'old_bank_name' => trim($bank_details->bank),
                    'mobile_no' => trim($ben_details->mobile_no),
                    'new_bank_code' =>trim($newBankCode),
                    'new_bank_ifsc' =>trim($newBankIfsc),
                    'new_bank_name' =>trim($newBankName),
                    'new_branch_name' =>trim($newBranchName),
                    'application_id' => $ben_details->ben_id,
                    'av_name_response' =>trim($ben_details->av_name_response),
                ];
                // dd($html);
                $response = array_merge($ben_arr, [
                    'html' => $html,
                    'status' => 2,
                    'header_msg' => $header_msg
                ]);
            }

        } catch (\Exception $e) {
            $response = [
                'exception' => true,
                'exception_message' => $e->getMessage(),
            ];
            $statusCode = 400;
        }finally {
            return response()->json($response, $statusCode);
        }
    }
    public function approverPost(Request $request){
        //  dd($request->all());
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        $user_id = AuthChecker::getUserId();
        $dutyObj = Configduty::where('user_id', '=', $user_id)
            ->where('is_active', 1)
            ->first();
        $distCode = $dutyObj->district_code;
        $is_bulk = $request->is_bulk;
        $accept_reject_comments = $request->accept_reject_comments;
        $opreation_type = $request->opreation_type;
        $applicant_id = $request->applicantId;
        $name_validation_type =$request->name_validation_type;
        if($name_validation_type == 141){
            $op_type = '151';
        }
        if($name_validation_type == 142){
            $op_type = '152';
        }
        if($name_validation_type == 143){
            $op_type = '153';
        }
        if($name_validation_type == 144){
            $op_type = '154';
        }
        if ($is_bulk == 0) {
            $single_app_id = $request->single_app_id;
            $parts = explode('_', $single_app_id);
            $id = $parts[0];
            $scheme_id = $parts[1];
            if ($opreation_type == 'A') {
                try {
                    $user_id = AuthChecker::getUserId();
                    $ip_address = request()->ip();
                    $table = $this->getSchemaName($scheme_id);
                    $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
                        ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
                        ->where('f.failed_type',2)->where('f.edited_status',1)->where('ben.is_eligible',true)
                        ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)
                        ->where('f.ben_id', $id)
                        ->where('ben.acc_validated',4)
                        ->where('ben.legacy_validation',1)
                        ->where('f.failed_process_type',$name_validation_type)
                        ->whereBetween('f.matching_score', [$this->min_score, $this->max_score])
                        ->where('f.dist_code', $distCode)
                        ->first();

                    $decodeNewbank = json_decode($ben_details->updated_details);
                    if($name_validation_type == 143 || $name_validation_type == 144) 
                    {
                        $new_bank_code = $decodeNewbank->new_bank_code;
                        $new_bank_ifsc = $decodeNewbank->new_bank_ifsc;
                        $new_bank_name = $decodeNewbank->new_bank_name;
                        $new_branch_name = $decodeNewbank->new_branch_name; 
                        $old_bank_code = $ben_details->last_accno;
                        $old_bank_ifsc = $ben_details->last_ifsc;
                        if($decodeNewbank->npci_bank_code){
                            $new_bank_code_npci = $decodeNewbank->npci_bank_code;
                        }
                        $decodeNewbank = json_decode($ben_details->updated_details);
                    }
                    $input = [];
                    $input_new = [];
                    $old_value = [];
                    $updateFailedTable = [];
                    $ben_payment_details = [];
                    $payment_details =[];
                    $message_arr = [
                        1 => 'Updated Successfully',
                        2 => '2 Something went wrong...',
                    ];
                    $return_msg = 'Beneficiary Id - ' . $id . ' approved successfully';
                    $update_beneficiaryTable = [];
                    if($name_validation_type == 141){
                        $update_beneficiaryTable['ben_fname'] = $decodeNewbank->ben_name;
                        $update_beneficiaryTable['ben_mname'] = NULL;
                        $update_beneficiaryTable['ben_lname'] = NULL;
                        $payment_details['acc_validated'] = 0;
                        $payment_details['ben_name'] = $decodeNewbank->ben_name;
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                    } else if($name_validation_type == 143 || $name_validation_type == 144){
                        $update_beneficiaryTable['bank_name'] = $new_bank_name;
                        $update_beneficiaryTable['branch_name'] = $new_branch_name;
                        $update_beneficiaryTable['bank_ifsc'] = trim( $new_bank_ifsc);
                        $update_beneficiaryTable['bank_code'] = trim( $new_bank_code);
                        $update_beneficiaryTable['npci_bank_code'] = $new_bank_code_npci;
                        
                        $payment_details['last_accno'] = trim( $new_bank_code);
                        $payment_details['last_ifsc'] = trim( $new_bank_ifsc);
                        $payment_details['npci_bank_code'] = $new_bank_code_npci;
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                        $payment_details['acc_validated'] = 0;
                    }else if($name_validation_type == 142){
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                        $payment_details['acc_validated'] = 0;
                    }
                    $updateFailedTable['edited_status']= 2;
                    $updateFailedTable['updated_at']= date('Y-m-d H:i:s');
                    if($name_validation_type == 143 || $name_validation_type == 144){
                        // $benDuplicateAcCount = DB::connection('pgsql_paywrite')->select("Select payment.bank_duplicate_check(in_scheme_id => ARRAY[".$scheme_id."]::INTEGER[], in_bank_codes => ARRAY[".$new_bank_code."]::VARCHAR[])"); 
                        $BankCheck = DupCheck::dupBankCheckSame($scheme_id, $new_bank_code, $id);
                        if ($BankCheck) {
                            return $response = [
                                'status' => 3,
                                'msg' => 'Bank account is Duplicate ',
                                'type' => 'blue',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ]; 
                        }
                        $BankCheckCross = DupCheck::dupBankCheckCross($scheme_id, $new_bank_code, $id);
                        if ($BankCheckCross) {
                            return $response = [
                                'status' => 3,
                                'msg' => 'Bank account is Duplicate with Cross Scheme ',
                                'type' => 'blue',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ]; 
                        }
                    }
                    $c_time = date('Y-m-d H:i:s', time());
                    $accept_reject_model = new AcceptRejectInfo;
                    $accept_reject_model->application_id = $id;
                    $accept_reject_model->created_at = $c_time;
                    $accept_reject_model->scheme_id = $scheme_id;
                    $accept_reject_model->user_id = $user_id;
                    $accept_reject_model->created_by_dist_code = $distCode;
                    $accept_reject_model->ip_address = request()->ip();
                    $accept_reject_model->op_type = $op_type;
                    $accept_reject_model->remarks = $accept_reject_comments;
                    $legacy_validation_failed_update = [
                        'status' => 2,
                        'approval_at' => date('Y-m-d H:i:s'),
                        'approval_by' => Auth::user()->id,
                    ];
                    DB::beginTransaction();
                    DB::connection('pgsql_paywrite')->beginTransaction();
                    $is_saved_log = $accept_reject_model->save();
                    if($is_saved_log)
                    {
                        $legacy_validation_update = DB::connection('pgsql_paywrite')->table('payment.validation_failed_modified')->where('ben_id',$id)->where('scheme_id',$scheme_id)->update($legacy_validation_failed_update);
                        $legacy_validation_update = 1;
                        if($legacy_validation_update)
                        {
                            $failed_update = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')
                            ->where('ben_id',$id)
                            ->where('scheme_id',$scheme_id)
                            ->where('edited_status',1)
                            ->where('failed_type',2)
                            ->whereBetween('matching_score', [$this->min_score, $this->max_score])
                            ->where('failed_process_type',$name_validation_type)
                            ->update($updateFailedTable);    
                     
                            if ($failed_update)
                            {
                                $payment_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')
                                ->where('ben_id',$id)
                                ->where('scheme_id',$scheme_id)
                                ->where('ben_status',1)
                                ->where('acc_validated',4)
                                ->where('is_eligible',true)
                                ->update($payment_details);   
                                if($payment_update)
                                {   
                                    if($name_validation_type == 141 ||$name_validation_type == 143 || $name_validation_type == 144){  
                                        $is_saved = DB::table($table)
                                        ->where('id', $id)
                                        ->where('scheme_id', $scheme_id)
                                        ->where('next_level_role_id', 0)
                                        ->where('created_by_dist_code', $distCode)
                                        ->update($update_beneficiaryTable);
                                    }else{
                                        $is_saved =1; 
                                    }
                                    if ($is_saved) {
                                        DB::commit();
                                        DB::connection('pgsql_paywrite')->commit();
                                        $response = [
                                            'status' => 1,
                                            'msg' => $return_msg,
                                            'type' => 'green',
                                            'icon' => 'fa fa-check',
                                            'title' => 'Success',
                                        ];
                                    } else {
                                        DB::rollback();
                                        DB::connection('pgsql_paywrite')->rollback();
                                        $response = [
                                            'status' => 3,
                                            'msg' => '3 Somethimg went wrong!!',
                                            'type' => 'red',
                                            'icon' => 'fa fa-warning',
                                            'title' => 'Warning!!',
                                        ];
                                    }
                                }else 
                                {
                                    DB::rollback();
                                    DB::connection('pgsql_paywrite')->rollback();
                                    $response = [
                                        'status' => 3,
                                        'msg' => '3 Somethimg went wrong!!',
                                        'type' => 'red',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];
                                }
                            } else 
                            {
                                DB::rollback();
                                DB::connection('pgsql_paywrite')->rollback();
                                $response = [
                                    'status' => 2,
                                    'msg' => '2 Somethimg went wrong!',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        }else
                        {
                            DB::rollback();
                            DB::connection('pgsql_paywrite')->rollback();
                            $response = [
                                'status' => 2,
                                'msg' => '2 Somethimg went wrong!!',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        } 
                    }else{
                        DB::rollback();
                            DB::connection('pgsql_paywrite')->rollback();
                            $response = [
                                'status' => 2,
                                'msg' => '2 Somethimg went wrong!!!',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                    }    
                } catch (\Exception $e) {
                    // dd($e);
                    DB::rollback();
                    DB::connection('pgsql_paywrite')->rollback();
                    $response = [
                        'exception' => true,
                        // 'exception_message' => $e->getMessage(),
                        'exception_message' =>
                            'Something went wrong. May be session time out logout and login again.',
                    ];
                    $statusCode = 400;
                }finally {
                    return response()->json($response, $statusCode);
                }
            }elseif ($opreation_type == 'T') {
                $return_msg = 'Beneficiaries Reverted successfully';
                try {
                    $user_id = AuthChecker::getUserId();
                    $ip_address = request()->ip();
                    $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
                        ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
                        ->where('f.failed_type',2)->where('f.edited_status',1)->where('ben.is_eligible',true)
                        ->where('ben.acc_validated',4)
                        ->where('ben.legacy_validation',1)
                        ->where('f.failed_process_type',$name_validation_type)
                        ->whereBetween('f.matching_score', [$this->min_score, $this->max_score])
                        ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)
                        ->where('f.ben_id', $id)
                        ->first();   
                    DB::beginTransaction();
                    DB::connection('pgsql_paywrite')->beginTransaction();
                    $input = [];
                    $input_new = [];
                    $old_value = [];
                    $message_arr = [
                        1 => 'Updated Successfully',
                        2 => '2 Something went wrong...',
                    ];
                    $update_failedTable = [];
                    $update_failedTable['edited_status'] = 0;
                    $update_failedTable['failed_process_type'] = 0;
                    $update_failedTable['updated_details'] = NULL;
                    $update_failedTable['updated_at'] = date('Y-m-d H:i:s');
                    if($name_validation_type == 141){
                        $op_type = '161';
                    }else if($name_validation_type == 142){
                        $op_type = '162';
                    }else if($name_validation_type == 143){
                        $op_type = '163';
                    }else if($name_validation_type == 144){
                        $op_type = '164';
                    }
                    $c_time = date('Y-m-d H:i:s', time());
                    $accept_reject_model = new AcceptRejectInfo;
                    $accept_reject_model->application_id = $id;
                    $accept_reject_model->created_at = $c_time;
                    $accept_reject_model->scheme_id = $scheme_id;
                    $accept_reject_model->user_id = $user_id;
                    $accept_reject_model->created_by_dist_code = $distCode;
                    $accept_reject_model->ip_address = request()->ip();
                    $accept_reject_model->op_type = $op_type;
                    $accept_reject_model->remarks = $accept_reject_comments;
                    $is_saved_log = $accept_reject_model->save();
                    if ($is_saved_log) 
                    {
                        $validation_failed_modified_del = DB::connection('pgsql_paywrite')->table('payment.validation_failed_modified')->where('ben_id',$id)->where('failed_process_type',$name_validation_type)->where('scheme_id',$scheme_id)->where('status',1)->delete();
                        if($validation_failed_modified_del)
                        {
                            $is_saved = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')
                            ->where('ben_id', $id)
                            ->where('scheme_id', $scheme_id)
                            ->where('edited_status', 1)
                            ->where('failed_type', 2)
                            ->where('dist_code', $distCode)
                            ->whereBetween('matching_score', [$this->min_score, $this->max_score])
                            ->where('failed_process_type',$name_validation_type)
                            ->update($update_failedTable);
                            if ($is_saved) 
                            {
                                DB::commit();
                                DB::connection('pgsql_paywrite')->commit();
                                $response = [
                                    'status' => 1,
                                    'msg' =>
                                    'Bank Details Back to Verifier Successfully',
                                    'type' => 'green',
                                    'icon' => 'fa fa-check',
                                    'title' => 'Success',
                                ];
                            } else 
                            {
                                DB::rollback();
                                DB::connection('pgsql_paywrite')->rollback();
                                $response = [
                                    'status' => 3,
                                    'msg' => '3 Somethimg went wrong!!',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        } else 
                        {
                            DB::rollback();
                            DB::connection('pgsql_paywrite')->rollback();
                            $response = [
                                'status' => 2,
                                'msg' => '2 Somethimg went wrong!!',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }
                    }else{
                        DB::rollback();
                        DB::connection('pgsql_paywrite')->rollback();
                        $response = [
                            'status' => 2,
                            'msg' => '2 Somethimg went wrong!!',
                            'type' => 'red',
                            'icon' => 'fa fa-warning',
                            'title' => 'Warning!!',
                        ];
                    }    
                } catch (\Exception $e) {
                        // dd($e);
                    DB::rollback();
                    DB::connection('pgsql_paywrite')->rollback();
                    $response = [
                        'exception' => true,
                        // 'exception_message' => $e->getMessage(),
                        'exception_message' =>
                            'Something went wrong. May be session time out logout and login again.',
                    ];
                    $statusCode = 400;
                } finally {
                    // dd($response);
                    return response()->json($response, $statusCode);
                }
            }
        }if ($is_bulk == 1) {
            //    dd($request->all());
            if ($opreation_type == 'A') {
                $bulk_id_arr = explode(',', $applicant_id);
                // dd($bulk_id_arr);
                // $scheme_id = $request->scheme_id;
                DB::beginTransaction();
                DB::connection('pgsql_paywrite')->beginTransaction();
                try {
                    $count = 0;
                    $i = 0;
                    foreach ($bulk_id_arr as $key => $value) {
                        $count++;
                        $bulk_single_id_arr = explode('_', $value);
                        $beneficiary_id = $bulk_single_id_arr[0];
                        $scheme_id = $bulk_single_id_arr[1];
                        $name_validation_type = $request->name_validation_type;
                        $table = $this->getSchemaName($scheme_id);
                        $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
                        ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
                        ->where('f.failed_type',2)->where('f.edited_status',1)->where('ben.is_eligible',true)
                        ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)
                        ->where('ben.acc_validated',4)
                        ->where('ben.legacy_validation',1)
                        ->whereBetween('f.matching_score', [$this->min_score, $this->max_score])
                        ->where('f.failed_process_type',$name_validation_type)
                        ->where('f.ben_id', $beneficiary_id)
                        ->first();
                        $decodeNewbank = json_decode($ben_details->updated_details);
                        if($name_validation_type == 143 || $name_validation_type == 144) 
                        {
                            $new_bank_code = $decodeNewbank->new_bank_code;
                            $new_bank_ifsc = $decodeNewbank->new_bank_ifsc;
                            $new_bank_name = $decodeNewbank->new_bank_name;
                            $new_branch_name = $decodeNewbank->new_branch_name; 
                            $old_bank_code = $ben_details->last_accno;
                            $old_bank_ifsc = $ben_details->last_ifsc;
                            if($decodeNewbank->npci_bank_code){
                                $new_bank_code_npci = $decodeNewbank->npci_bank_code;
                            }
                            $decodeNewbank = json_decode($ben_details->updated_details);
                        }

                        $input = [];
                        $input_new = [];
                        $old_value = [];
                        $updateFailedTable = [];
                        $ben_payment_details = [];
                        $payment_details =[];
                        $message_arr = [
                            1 => 'Updated Successfully',
                            2 => '2 Something went wrong...',
                        ];
                        $update_beneficiaryTable = [];
                    if($name_validation_type == 141){
                        $update_beneficiaryTable['ben_fname'] = $decodeNewbank->ben_name;
                        $update_beneficiaryTable['ben_mname'] = NULL;
                        $update_beneficiaryTable['ben_lname'] = NULL;
                        $payment_details['acc_validated'] = 0;
                        $payment_details['ben_name'] = $decodeNewbank->ben_name;
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                    } else if($name_validation_type == 143 || $name_validation_type == 144){
                        $update_beneficiaryTable['bank_name'] = $new_bank_name;
                        $update_beneficiaryTable['branch_name'] = $new_branch_name;
                        $update_beneficiaryTable['bank_ifsc'] = trim( $new_bank_ifsc);
                        $update_beneficiaryTable['bank_code'] = trim( $new_bank_code);
                        $update_beneficiaryTable['npci_bank_code'] = $new_bank_code_npci;
                        // $old_value = [
                        //     'old_bank_ifsc' => trim($old_bank_ifsc),
                        //     'old_bank_code' => trim($old_bank_code)
                        // ];
                        $payment_details['last_accno'] = trim( $new_bank_code);
                        $payment_details['last_ifsc'] = trim( $new_bank_ifsc);
                        $payment_details['npci_bank_code'] = $new_bank_code_npci;
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                        $payment_details['acc_validated'] = 0;
                    }else if($name_validation_type == 142){
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                        $payment_details['acc_validated'] = 0;
                    }
                    $updateFailedTable['edited_status']= 2;
                    $updateFailedTable['updated_at']= date('Y-m-d H:i:s');
                    if($name_validation_type == 143 || $name_validation_type == 144){
                        $BankCheck = DupCheck::dupBankCheckSame($scheme_id, $new_bank_code, $beneficiary_id);
                        if ($BankCheck) {
                            return $response = [
                                'status' => 3,
                                'msg' => 'Bank account is Duplicate ',
                                'type' => 'blue',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ]; 
                        }
                        $BankCheckCross = DupCheck::dupBankCheckCross($scheme_id, $new_bank_code, $beneficiary_id);
                        if ($BankCheckCross) {
                            return $response = [
                                'status' => 3,
                                'msg' => 'Bank account is Duplicate with Cross Scheme ',
                                'type' => 'blue',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ]; 
                        }
                          
                    }
                    // $updateBenDetailsData1 = [
                    //     'old_data'=>json_encode($old_value),
                    //     'new_data'=>json_encode($decodeNewbank)
                    // ];
                    // $updateBenDetailsData = [
                    //     'original_application_id' => $ben_details->ben_id,
                    //     'dist_code' => $ben_details->dist_code,
                    //     'scheme_id' => $scheme_id,
                    //     'remarks' => $accept_reject_comments,
                    //     // 'old_data' => json_encode($old_value),
                    //     // 'new_data' =>  json_encode($decodeNewbank),
                    //     'user_id' => Auth::user()->id,
                    //     'update_code' => $name_validation_type,
                    //     'created_at' => date('Y-m-d H:i:s'),
                    //     'updated_at' => date('Y-m-d H:i:s'),
                    //     'ip_address' => request()->ip(),
                    //     'next_level_role_id' => 0,
                    // ];
                    // if($name_validation_type == 143 || $name_validation_type == 144){
                    //     $updateBenDetailsData = array_merge($updateBenDetailsData, $updateBenDetailsData1);
                    // }
                    $c_time = date('Y-m-d H:i:s', time());
                    $accept_reject_model = new AcceptRejectInfo;
                    $accept_reject_model->application_id = $beneficiary_id;
                    $accept_reject_model->created_at = $c_time;
                    $accept_reject_model->scheme_id = $scheme_id;
                    $accept_reject_model->user_id = $user_id;
                    $accept_reject_model->created_by_dist_code = $distCode;
                    $accept_reject_model->ip_address = request()->ip();
                    $accept_reject_model->op_type = $op_type;
                    $accept_reject_model->remarks = $accept_reject_comments;
                    $is_saved_log = $accept_reject_model->save();

                    $legacy_validation_failed_update = [
                        'status' => 2,
                        'approval_at' => date('Y-m-d H:i:s'),
                        'approval_by' => Auth::user()->id,
                    ];
                    // dd($update_beneficiaryTable);
                        // $is_update = UpdateBenDetails::insert($updateBenDetailsData);
                        $legacy_validation_update = DB::connection('pgsql_paywrite')->table('payment.validation_failed_modified')
                        ->where('ben_id',$beneficiary_id)
                        ->where('scheme_id',$scheme_id)
                        ->update($legacy_validation_failed_update);

                        $failed_update = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')
                        ->where('ben_id',$beneficiary_id)
                        ->where('scheme_id',$scheme_id)
                        ->where('edited_status',1)
                        ->where('failed_type',2)
                        ->whereBetween('matching_score', [$this->min_score, $this->max_score])
                        ->update($updateFailedTable);

                        $payment_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')
                        ->where('ben_id',$beneficiary_id)
                        ->where('scheme_id',$scheme_id)
                        ->where('ben_status',1)
                        ->where('acc_validated',4)
                        ->where('is_eligible',true)
                        ->update($payment_details);

                        if ($payment_update) {
                            $is_saved = DB::table($table)
                            ->where('id', $beneficiary_id)
                            ->where('scheme_id', $scheme_id)
                            ->where('next_level_role_id', 0)
                            ->where('created_by_dist_code', $distCode)
                            ->update($update_beneficiaryTable);
                        }
                        if ($is_saved == 1 && $payment_update == 1 && $failed_update == 1 && $legacy_validation_update == 1) {
                            $i++;
                        }
                    }
                    if ($i == $count) {
                        DB::commit();
                        DB::connection('pgsql_paywrite')->commit();
                        $response = [
                            'status' => 1,
                            'msg' => 'Bank Details Updated Successfully',
                            'type' => 'green',
                            'icon' => 'fa fa-check',
                            'title' => 'Success',
                        ];
                    } else {
                        DB::rollback();
                        DB::connection('pgsql_paywrite')->rollback();
                        $response = [
                            'exception' => true,
                            // 'exception_message' => $e->getMessage(),
                            'exception_message' =>
                                'Something went wrong. May be session time out logout and login again.',
                        ];
                    }
                } catch (\Exception $e) {
                        //  dd($e);
                    DB::rollback();
                    DB::connection('pgsql_paywrite')->rollback();
                    $response = [
                        'exception' => true,
                        // 'exception_message' => $e->getMessage(),
                        'exception_message' =>
                            'Something went wrong. May be session time out logout and login again.',
                    ];
                    $statusCode = 400;
                } finally {
                    return response()->json($response, $statusCode);
                }
            }
        }
    }
    public function assigdate(Request $request){
        try {
        $back_url='validation-correction-marking';
        $scheme_id=$request->assign_scheme_id;
        $no_of_applicants=$request->no_of_applicants;
        $arrival_date=$request->arrival_date;
        $tag_time = $request->visiting_time;

        if($scheme_id=='' || is_null($scheme_id)){
            $return_msg = 'Scheme is Required';
            return redirect($back_url)->with('msg1', $return_msg);
        }
        if($no_of_applicants=='' || is_null($no_of_applicants)){
            $return_msg = 'No of Applicants is Required';
            return redirect($back_url)->with('msg1', $return_msg);
        }
        if (!ctype_digit($no_of_applicants)) {
            $return_msg = 'No of Applicants inValid';
            return redirect($back_url)->with('msg1', $return_msg);
        }
        if ($no_of_applicants > 25) {
            $return_msg = 'No of Applicants cannot exceed 25';
            return redirect($back_url)->with('msg1', $return_msg);
        }
        if($arrival_date=='' || is_null($arrival_date)){
            $return_msg = 'Visiting Date is Required';
            return redirect($back_url)->with('msg1', $return_msg);
        }
        
        $date = new DateTime($arrival_date);
        $now = new DateTime();
        
        if($date < $now) {
            $return_msg = 'Visiting Date cannot be Past Date';
            return redirect($back_url)->with('msg1', $return_msg);
        }

        $user_id = AuthChecker::getUserId();
        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
        if (empty($duty_obj)) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        $district_code = $duty_obj->district_code;
        if ($duty_obj->mapping_level == "Subdiv") {
            $created_by_local_body_code = $duty_obj->urban_body_code;
        }else if ($duty_obj->mapping_level == "Block") {
            $created_by_local_body_code = $duty_obj->taluka_code;
        }
        $pending_row_arr = DB::connection('pgsql_paywrite')->select("SELECT count(1) as cnt from payment.failed_payment_details as A 
        JOIN  payment.ben_payment_details as B ON A.ben_id=B.ben_id 
        where A.scheme_id=".$scheme_id." and B.scheme_id=".$scheme_id." 
        and A.dist_code=".$district_code." and  B.dist_code=".$district_code."
	and A.local_body_code=".$created_by_local_body_code ." 
    and B.local_body_code=".$created_by_local_body_code ."  and visiting_time IS NULL  
    and A.edited_status=0 and B.ben_status=1 and B.is_eligible = true and B.is_rejected=0 and B.acc_validated = 4 
    and A.failed_type = 2 and B.legacy_validation = 1 and A.if_previous_approve=0 and A.matching_score between 0 and 25");
    if($pending_row_arr[0]->cnt==0){
        $return_msg = 'No Pending Beneficiary to mark';
        return redirect($back_url)->with('msg1', $return_msg);
    }
        $c_time = date('Y-m-d H:i:s', time());
        
        DB::beginTransaction();
        DB::connection('pgsql_paywrite')->beginTransaction();
           $marking_visiting_time_arr =DB::connection('pgsql_paywrite')->select("select payment.marking_visiting_time(
            in_scheme_id =>" . $scheme_id . ",
            in_district_code =>" . $district_code . ",
            in_local_body_code =>" . $created_by_local_body_code . ",
            in_no_of_applicant =>" . $no_of_applicants . ",
            in_mark_date =>'" . $c_time . "',
            in_mark_time => '".$tag_time."',
            in_arrival_date => '" . $arrival_date . "')");
            $is_inserted_status = $marking_visiting_time_arr[0]->marking_visiting_time;
            $accept_reject_model = new AcceptRejectInfo;
            $accept_reject_model->created_at = $c_time;
            $accept_reject_model->scheme_id = $scheme_id;
            $accept_reject_model->user_id = $user_id;
            $accept_reject_model->created_by_dist_code = $district_code;
            $accept_reject_model->created_by_local_body_code = $created_by_local_body_code;
            $accept_reject_model->ip_address = request()->ip();
            $accept_reject_model->op_type = 'VISITINGMARKING';
            $is_saved_log = $accept_reject_model->save();
            if($is_inserted_status && $is_saved_log){
                DB::commit();
                DB::connection('pgsql_paywrite')->commit();
                $action_msg='Beneficiaries visiting time have been tagged Successfully';    
                return redirect($back_url)->with('success', $action_msg);
            }
            else{
                DB::rollback();
                DB::connection('pgsql_paywrite')->rollback();
                $return_msg = 'Something went wrong, please try again.';
                return redirect($back_url)->with('msg1', $return_msg);
            }
        }
        catch (\Exception $e) {
            dd($e);
          }
    }
    public function markPending(Request $request){
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            //  dd($request->all());
            $scheme_id = $request->scheme_id;
            $id = $request->beneficiary_Id;
            $process_type_id = $request->process_type_id;
            $user_id = AuthChecker::getUserId();
            $dutyObj = Configduty::where('user_id', '=', $user_id)
            ->where('is_active', 1)
            ->first();
            $distCode = $dutyObj->district_code;
            $district_code = $dutyObj->district_code;
            if ($dutyObj->mapping_level == "Subdiv") {
                $created_by_local_body_code = $dutyObj->urban_body_code;
            }else if ($dutyObj->mapping_level == "Block") {
                $created_by_local_body_code = $dutyObj->taluka_code;
            }
            $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
                        ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
                        ->where('f.failed_type',2)->where('f.edited_status',0)->where('ben.is_eligible',true)
                        ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)
                        ->where('ben.acc_validated',4)
                        ->where('ben.legacy_validation',1)
                        ->where('ben.dist_code',$district_code)
                        ->where('ben.local_body_code',$created_by_local_body_code)
                        ->whereBetween('f.matching_score', [$this->min_score, $this->max_score])
                        ->where('f.ben_id', $id)
                        ->first();           
            if (empty($ben_details)) {
                return $response = [
                    'status' => 3,
                    'msg' => 'Something went wrong!!',
                    'type' => 'blue',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ]; 
            }
            $legacyMarkedTbl['ben_id'] = $id;
            $legacyMarkedTbl['process_type'] = $process_type_id;
            $legacyMarkedTbl['created_by'] = Auth::user()->id;
            $legacyMarkedTbl['designation'] = Auth::user()->designation_id;
            $legacyMarkedTbl['created_at'] = date("Y-m-d H:i:s");
            DB::beginTransaction();
            DB::connection('pgsql_paywrite')->beginTransaction();
            $failed_update = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')
                        ->where('ben_id',$id)
                        ->where('scheme_id',$scheme_id)
                        ->where('failed_type',2)
                        ->whereBetween('matching_score', [$this->min_score, $this->max_score])
                        ->update(['process_complete'=>$process_type_id]);
            $insertLegacyMarked = DB::connection('pgsql_paywrite')->table('payment.legacy_failed_process_mark')->insert($legacyMarkedTbl);            
            if($failed_update && $insertLegacyMarked){
                DB::commit();
                DB::connection('pgsql_paywrite')->commit();
                $response = [
                    'status' => 1,
                    'msg' => 'Beneficiary Priliminary Acceptance Marked has been done',
                    'type' => 'green',
                    'icon' => 'fa fa-check',
                    'title' => 'Success',
                ];
            } else {
                DB::rollback();
                DB::connection('pgsql_paywrite')->rollback();
                $response = [
                    'exception' => true,
                    // 'exception_message' => $e->getMessage(),
                    'exception_message' =>
                        'Something went wrong. May be session time out logout and login again.',
                ];
            }
        } catch (\Exception $e) {
            dd( $e);
            DB::rollback();
            DB::connection('pgsql_paywrite')->rollback();
            $response = [
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' =>
                    'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
        }
        finally {
            return response()->json($response, $statusCode);
        }
    }
    
    public function misReport(Request $request)
    {
        // $ds_phase_list = DsPhase::get();
        // $base_date  = '2020-01-01';
        // $c_time = Carbon::now();
        // $c_date = $c_time->format("Y-m-d");
        $is_active = 0;
        $user_id = Auth::user()->id;
        $scheme = DB::select(DB::raw("select id,scheme_name from m_scheme where  id in (select scheme_id from duty_assignement where scheme_id in (2,10,11) and is_active=1 and user_id=" . $user_id . " )"));

        $roleArray = $request->session()->get('role');
        $designation_id = Auth::user()->designation_id;
        $district_visible = $is_urban_visible = $block_visible = 1;
        $municipality_visible = 0;
        $gp_ward_visible = 0;
        $muncList = collect([]);
        $gpList = collect([]);
        if ($designation_id == 'Admin' || $designation_id == 'HOD' || $designation_id == 'HOP' || $designation_id == 'MisState' || $designation_id == 'Dashboard') {
            $district_visible = $is_urban_visible = $block_visible = 1;
        } else if (AuthChecker::ApproverPermission()) {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;
            foreach ($roleArray as $roleObj) {
                //   if ($roleObj['scheme_id'] == $this->scheme_id) {
                $is_urban = $roleObj['is_urban'];
                $district_code = $roleObj['district_code'];
                if ($roleObj['is_urban'] == 1) {
                    $blockCode = $roleObj['urban_body_code'];
                    $muncList = UrbanBody::select('urban_body_code', 'urban_body_name')->where('sub_district_code', $blockCode)->get();
                    $municipality_visible = 1;
                } else {
                    $blockCode = $roleObj['taluka_code'];
                    $gpList = GP::select('gram_panchyat_code', 'gram_panchyat_name')->where('block_code', $blockCode)->get();
                }
                break;
                //   }
            }

            if (empty($district_code))
                return redirect("/")->with('success', 'User Disabled. ');
        } else {
            return redirect("/")->with('success', 'User Disabled. ');
        }
        //dd($district_code);
        if (!empty($district_code)) {
            $district_visible = 0;
            $district_code_fk = $district_code;
        } else {
            $district_code_fk = NULL;
        }
        if (!empty($is_urban)) {
            $is_urban_visible = 0;
            $rural_urban_fk = $is_urban;
        } else {
            $rural_urban_fk = NULL;
        }
        if (!empty($blockCode)) {
            $block_visible = 0;
            $block_munc_corp_code_fk = $blockCode;
            $gp_ward_visible = 1;
        } else {
            $block_munc_corp_code_fk = NULL;
            $gp_ward_visible = 0;
        }
        $districts = District::get();
        // dd($ds_phase_list);
        return view(
            'validation-correction-pending.0_to_25_mis',
            [
                'districts' => $districts,
                'district_visible' => $district_visible,
                'district_code_fk' => $district_code_fk,
                'is_urban_visible' => $is_urban_visible,
                'rural_urban_fk' => $rural_urban_fk,
                'block_visible' => $block_visible,
                'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
                'municipality_visible' => $municipality_visible,
                'gp_ward_visible' => $gp_ward_visible,
                'is_urban_visible' => $is_urban_visible,
                'scheme' => $scheme,
                // 'base_date' => $base_date,
                // 'c_date' => $c_date,
                'gpList' => $gpList,
                'muncList' => $muncList,
                // 'ds_phase_list' => $ds_phase_list
            ]
        );
    }

    public function misPost(Request $request)
    {

        // dd($request->all());
        $district = $request->district;
        $urban_code = $request->urban_code;
        $block = $request->block;
        $muncid = $request->muncid;
        $gp_ward = $request->gp_ward;
        $scheme_id = (int) $request->scheme_id;
        $minor_mismatch = $request->minor_mismatch;
        if ($minor_mismatch == 1) {
            $userMsgTitle = '0% - 25%';
        }
        // dd($gp_ward);
        // $from_date = $request->from_date;
        // $to_date = $request->to_date;
        // $ds_phase_list = DsPhase::get()->pluck('phase_des', 'phase_code');
        // dd($ds_phase);
        // $base_date  = '2025-01-24';
        // $c_time = Carbon::now();
        // $c_date = $c_time->format("Y-m-d");
        $heading_msg = '';
        $title = "";
        //$block_condition = "";
        if (!empty($district)) {
            $district_row = District::where('district_code', $district)->first();
        }

        if (!empty($block)) {

            if ($urban_code == 1) {
                $block_ulb = SubDistrict::where('sub_district_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->sub_district_name;
                //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
            } else {
                $block_ulb = Taluka::where('block_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->block_name;
                // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
            }
        } else {
            // $block_condition = "";
        }
        if (!empty($gp_ward)) {

            if ($urban_code == 1) {
                $gp_ward_row = Ward::where('urban_body_ward_code', '=', $gp_ward)->first();
                $gp_ward_name = $gp_ward_row->urban_body_ward_name;
                //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
            } else {
                $gp_ward_row = GP::where('gram_panchyat_code', '=', $gp_ward)->first();
                $gp_ward_name = $gp_ward_row->gram_panchyat_name;
                // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
            }
        }
        $rules = [
            'district' => 'nullable|integer',
            'urban_code' => 'nullable|integer',
            'block' => 'nullable|integer',
            'muncid' => 'nullable|integer',
            'gp_ward' => 'nullable|integer',
        ];
        $data = array();
        $column = "";
        $attributes = array();
        $messages = array();
        $attributes['district'] = 'District';
        $attributes['urban_code'] = 'Rural/ Urban';
        $attributes['block'] = 'Block/Sub Division';
        $attributes['muncid'] = 'Municipality';
        $attributes['gp_ward'] = 'GP/Ward';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $user_msg = "Minor Mismatch MIS Report(" . $userMsgTitle . ")";
            $title = $user_msg;
            //dd($title);

            $data = array();
            $return_status = 1;
            $return_msg = '';
            $heading_msg = '';
            $external = 0;
            $external_arr = array();
            $external_filter = array();
            $is_address = 0;
            if (!empty($gp_ward)) {
                if ($urban_code == 1) {
                    $is_address = 1;
                    $column = "Ward";
                    $heading_msg = $user_msg . ' of the Ward ' . $gp_ward_name;
                    $data = $this->getMonorMismatch0to25WardWise($scheme_id, $district, $block, $muncid, $gp_ward, $minor_mismatch);
                } else {
                    $is_address = 1;
                    $column = "GP";
                    $heading_msg = $user_msg . ' of the GP ' . $gp_ward_name;
                    $data = $this->getMonorMismatch0to25GpWise($scheme_id, $district, $block, NULL, $gp_ward, $minor_mismatch);
                }
            } else if (!empty($muncid)) {
                $is_address = 1;
                $column = "Ward";
                $municipality_row = UrbanBody::where('urban_body_code', '=', $muncid)->first();
                $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $municipality_row->urban_body_name;
                $data = $this->getMonorMismatch0to25WardWise($scheme_id, $district, $block, $muncid, NULL, $minor_mismatch);
            } else if (!empty($block)) {
                if ($urban_code == 1) {
                    $is_address = 1;
                    $column = "Municipality";
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the Sub Division ' . $blk_munc_name;
                    $data = $this->getMonorMismatch0to25MuncWise($scheme_id, $district, $block, NULL, NULL, $minor_mismatch);
                } else if ($urban_code == 2) {
                    $is_address = 1;
                    $block_arr = Taluka::where('block_code', '=', $block)->first();
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $block_arr->block_name;
                    $data = $this->getMonorMismatch0to25GpWise($scheme_id, $district, $block, NULL, $gp_ward, $minor_mismatch);
                }
            } else {

                if (!empty($district)) {
                    if ($urban_code == 1) {
                        $column = "Sub Division";
                        $heading_msg = 'Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $data = $this->getMonorMismatch0to25SubDivWise($scheme_id, $district, NULL, NULL, NULL, $minor_mismatch);
                    } else if ($urban_code == 2) {
                        $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block";
                        $data = $this->getMonorMismatch0to25BlockWise($scheme_id, $district, NULL, NULL, NULL, $minor_mismatch);
                        //   dump($data);
                        //   die();
                    } else {
                        $heading_msg = 'Block/Sub Division Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                        $column = "Block/Sub Division";
                        $data1 = $this->getMonorMismatch0to25BlockWise($scheme_id, $district, NULL, NULL, NULL, $minor_mismatch);
                        $data2 = $this->getMonorMismatch0to25SubDivWise($scheme_id, $district, NULL, NULL, NULL, $minor_mismatch);
                        $data = array_merge($data1, $data2);
                    }
                } else {
                    $column = "District";
                    $heading_msg = 'District Wise ' . $user_msg;
                    $data = $this->getMonorMismatch0to25DistrictWise($scheme_id, $district, NULL, NULL, NULL, $minor_mismatch);
                    $external = 0;
                }
            }
            if ($is_address == 1) {
                $heading_msg = $heading_msg . "<span class='text-danger'> (According to Applicant’s Address)</span>";
            }
        } else {
            $return_status = 0;
            $return_msg = $validator->errors()->all();
        }

        return response()->json([
            'return_status' => $return_status,
            'return_msg' => $return_msg,
            'row_data' => $data,
            'column' => $column,
            'title' => $title,
            'heading_msg' => $heading_msg
        ]);
    }


    public function getMonorMismatch0to25BlockWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $minor_mismatch)
    {
        $whereCon = "dist_code =" . $district_code;
        $whereMain = " WHERE district_code =" . $district_code;
        // if ($minor_mismatch == 1) {
        //     $Condition = "scheme_id=" . $scheme_id . " and matching_score >= 90 AND matching_score <= 100";
        // }



        $query = "select A.location_id AS created_by_local_body_code,
          A.location_name AS block_subdiv_name,
          A.created_by_dist_code,
          COALESCE(C.total, 0::bigint) AS total,
          COALESCE(C.yet_to_action, 0::bigint) AS yet_to_action,
          COALESCE(C.bank_name_may_be_taken_veri, 0::bigint) AS bank_name_may_be_taken_veri,
          COALESCE(C.passbook_correction_veri, 0::bigint) AS passbook_correction_veri,
          COALESCE(C.bank_account_other_family_embers_veri, 0::bigint) AS bank_account_other_family_embers_veri,
          COALESCE(C.bank_account_completely_other_person_veri, 0::bigint) AS bank_account_completely_other_person_veri,
          COALESCE(C.bank_name_may_be_taken_appr, 0::bigint) AS bank_name_may_be_taken_appr,
          COALESCE(C.passbook_correction_appr, 0::bigint) AS passbook_correction_appr,
          COALESCE(C.bank_account_other_family_embers_appr, 0::bigint) AS bank_account_other_family_embers_appr,
          COALESCE(C.bank_account_completely_other_person_appr, 0::bigint) AS bank_account_completely_other_person_appr,
          COALESCE(C.rejected, 0::bigint) AS rejected
      from(
      SELECT m_block.block_code AS location_id,
          m_block.block_name AS location_name,
          m_block.district_code AS created_by_dist_code
        FROM public.m_block  " . $whereMain . ") as A  
      LEFT JOIN
      (
          SELECT failed.local_body_code,
          COUNT(1)  AS total,
          COUNT(1) FILTER (WHERE edited_status = 0 AND ben.is_eligible = true) AS yet_to_action,
          COUNT(1) FILTER (WHERE edited_status = 1 and failed_process_type = 141 AND ben.is_eligible = true) AS bank_name_may_be_taken_veri,
          COUNT(1) FILTER (WHERE edited_status = 1 and failed_process_type = 142 AND ben.is_eligible = true) AS passbook_correction_veri,
          COUNT(1) FILTER (WHERE edited_status = 1 and failed_process_type = 143 AND ben.is_eligible = true) AS bank_account_other_family_embers_veri,
          COUNT(1) FILTER (WHERE edited_status = 1 and failed_process_type = 144 AND ben.is_eligible = true) AS bank_account_completely_other_person_veri,
          COUNT(1) FILTER (WHERE edited_status = 2 and failed_process_type = 141 AND ben.is_eligible = true) AS bank_name_may_be_taken_appr,
          COUNT(1) FILTER (WHERE edited_status = 2 and failed_process_type = 142 AND ben.is_eligible = true) AS passbook_correction_appr,
          COUNT(1) FILTER (WHERE edited_status = 2 and failed_process_type = 143 AND ben.is_eligible = true) AS bank_account_other_family_embers_appr,
          COUNT(1) FILTER (WHERE edited_status = 2 and failed_process_type = 144 AND ben.is_eligible = true) AS bank_account_completely_other_person_appr,
          COUNT(1) FILTER (WHERE edited_status = 2  AND ben.is_eligible = false ) AS rejected
          from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id where failed.scheme_id=" . $scheme_id . "  AND ben.scheme_id=" . $scheme_id . "   AND failed.failed_type = 2 AND ben.legacy_validation = 1  AND failed.matching_score >= 0 AND failed.matching_score <= 25 AND failed.if_previous_approve = 0 AND ben.ben_status =1 and ben.acc_validated=4
       group by failed.local_body_code) as C ON A.location_id=C.local_body_code
";
        $result = DB::connection('pgsql_paywrite')->select($query);
        return $result;
    }

    public function getMonorMismatch0to25SubDivWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $minor_mismatch)
    {
        $whereCon = "dist_code =" . $district_code;
        $whereMain = " WHERE district_code =" . $district_code;
        if ($minor_mismatch == 1) {
            $Condition = "scheme_id=" . $scheme_id . " AND matching_score >= 90 AND matching_score <= 100";
        }

        $query = "select A.location_id AS created_by_local_body_code,
          A.location_name AS block_subdiv_name,
          A.created_by_dist_code,
          COALESCE(C.total, 0::bigint) AS total,
          COALESCE(C.yet_to_action, 0::bigint) AS yet_to_action,
          COALESCE(C.bank_name_may_be_taken_veri, 0::bigint) AS bank_name_may_be_taken_veri,
          COALESCE(C.passbook_correction_veri, 0::bigint) AS passbook_correction_veri,
          COALESCE(C.bank_account_other_family_embers_veri, 0::bigint) AS bank_account_other_family_embers_veri,
          COALESCE(C.bank_account_completely_other_person_veri, 0::bigint) AS bank_account_completely_other_person_veri,
          COALESCE(C.bank_name_may_be_taken_appr, 0::bigint) AS bank_name_may_be_taken_appr,
          COALESCE(C.passbook_correction_appr, 0::bigint) AS passbook_correction_appr,
          COALESCE(C.bank_account_other_family_embers_appr, 0::bigint) AS bank_account_other_family_embers_appr,
          COALESCE(C.bank_account_completely_other_person_appr, 0::bigint) AS bank_account_completely_other_person_appr,
          COALESCE(C.rejected, 0::bigint) AS rejected
      from(
      SELECT m_sub_district.sub_district_code AS location_id,
              m_sub_district.sub_district_name AS location_name,
              m_sub_district.district_code AS created_by_dist_code
          FROM public.m_sub_district " . $whereMain . " ) as A  
      LEFT JOIN
      (
          SELECT failed.local_body_code,
          COUNT(1)  AS total,
          COUNT(1) FILTER (WHERE edited_status = 0 AND ben.is_eligible = true) AS yet_to_action,
          COUNT(1) FILTER (WHERE edited_status = 1 and failed_process_type = 141 AND ben.is_eligible = true) AS bank_name_may_be_taken_veri,
          COUNT(1) FILTER (WHERE edited_status = 1 and failed_process_type = 142 AND ben.is_eligible = true) AS passbook_correction_veri,
          COUNT(1) FILTER (WHERE edited_status = 1 and failed_process_type = 143 AND ben.is_eligible = true) AS bank_account_other_family_embers_veri,
          COUNT(1) FILTER (WHERE edited_status = 1 and failed_process_type = 144 AND ben.is_eligible = true) AS bank_account_completely_other_person_veri,
          COUNT(1) FILTER (WHERE edited_status = 2 and failed_process_type = 141 AND ben.is_eligible = true) AS bank_name_may_be_taken_appr,
          COUNT(1) FILTER (WHERE edited_status = 2 and failed_process_type = 142 AND ben.is_eligible = true) AS passbook_correction_appr,
          COUNT(1) FILTER (WHERE edited_status = 2 and failed_process_type = 143 AND ben.is_eligible = true) AS bank_account_other_family_embers_appr,
          COUNT(1) FILTER (WHERE edited_status = 2 and failed_process_type = 144 AND ben.is_eligible = true) AS bank_account_completely_other_person_appr,
          COUNT(1) FILTER (WHERE edited_status = 2  AND ben.is_eligible = false ) AS rejected
          from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id where failed.scheme_id=" . $scheme_id . " AND ben.scheme_id=" . $scheme_id . "  AND failed.failed_type = 2 AND ben.legacy_validation = 1  AND failed.matching_score >= 0 AND failed.matching_score <= 25 AND failed.if_previous_approve = 0 AND ben.ben_status =1 and ben.acc_validated=4
       group by failed.local_body_code) as C ON A.location_id=C.local_body_code
";
        $result = DB::connection('pgsql_paywrite')->select($query);
        return $result;
    }

    public function getMonorMismatch0to25DistrictWise($scheme_id, $district_code = NULL, $ulb_code = NULL, $block_ulb_code = NULL, $gp_ward_code = NULL, $minor_mismatch)
    {
        $whereCon = "dist_code =" . $district_code;
        $whereMain = " WHERE district_code =" . $district_code;
       
        $query = "select A.location_id AS created_by_local_body_code,
          A.location_name AS block_subdiv_name,
          A.created_by_dist_code,
          COALESCE(C.total, 0::bigint) AS total,
          COALESCE(C.yet_to_action, 0::bigint) AS yet_to_action,
          COALESCE(C.bank_name_may_be_taken_veri, 0::bigint) AS bank_name_may_be_taken_veri,
          COALESCE(C.passbook_correction_veri, 0::bigint) AS passbook_correction_veri,
          COALESCE(C.bank_account_other_family_embers_veri, 0::bigint) AS bank_account_other_family_embers_veri,
          COALESCE(C.bank_account_completely_other_person_veri, 0::bigint) AS bank_account_completely_other_person_veri,
          COALESCE(C.bank_name_may_be_taken_appr, 0::bigint) AS bank_name_may_be_taken_appr,
          COALESCE(C.passbook_correction_appr, 0::bigint) AS passbook_correction_appr,
          COALESCE(C.bank_account_other_family_embers_appr, 0::bigint) AS bank_account_other_family_embers_appr,
          COALESCE(C.bank_account_completely_other_person_appr, 0::bigint) AS bank_account_completely_other_person_appr,
          COALESCE(C.rejected, 0::bigint) AS rejected
      from(
      select m_district.district_code AS location_id,
                m_district.district_name AS location_name,
                m_district.district_code AS created_by_dist_code
            FROM public.m_district ) as A  
      LEFT JOIN
      (
          SELECT failed.dist_code,
          COUNT(1)  AS total,
          COUNT(1) FILTER (WHERE edited_status = 0 AND ben.is_eligible = true) AS yet_to_action,
          COUNT(1) FILTER (WHERE edited_status = 1 and failed_process_type = 141 AND ben.is_eligible = true) AS bank_name_may_be_taken_veri,
          COUNT(1) FILTER (WHERE edited_status = 1 and failed_process_type = 142 AND ben.is_eligible = true) AS passbook_correction_veri,
          COUNT(1) FILTER (WHERE edited_status = 1 and failed_process_type = 143 AND ben.is_eligible = true) AS bank_account_other_family_embers_veri,
          COUNT(1) FILTER (WHERE edited_status = 1 and failed_process_type = 144 AND ben.is_eligible = true) AS bank_account_completely_other_person_veri,
          COUNT(1) FILTER (WHERE edited_status = 2 and failed_process_type = 141 AND ben.is_eligible = true) AS bank_name_may_be_taken_appr,
          COUNT(1) FILTER (WHERE edited_status = 2 and failed_process_type = 142 AND ben.is_eligible = true) AS passbook_correction_appr,
          COUNT(1) FILTER (WHERE edited_status = 2 and failed_process_type = 143 AND ben.is_eligible = true) AS bank_account_other_family_embers_appr,
          COUNT(1) FILTER (WHERE edited_status = 2 and failed_process_type = 144 AND ben.is_eligible = true) AS bank_account_completely_other_person_appr,
          COUNT(1) FILTER (WHERE edited_status = 2  AND ben.is_eligible = false ) AS rejected
          from payment.failed_payment_details failed join payment.ben_payment_details ben on failed.ben_id=ben.ben_id where failed.scheme_id=" . $scheme_id . " AND ben.scheme_id=" . $scheme_id . "  AND failed.failed_type = 2 AND ben.legacy_validation = 1  AND failed.matching_score >= 0 AND failed.matching_score <= 25 AND failed.if_previous_approve = 0 AND ben.ben_status =1 and ben.acc_validated=4
       group by failed.dist_code) as C ON A.location_id=C.dist_code";
    //    dd($query);
        $result = DB::connection('pgsql_paywrite')->select($query);
        return $result;
    }
    
}
