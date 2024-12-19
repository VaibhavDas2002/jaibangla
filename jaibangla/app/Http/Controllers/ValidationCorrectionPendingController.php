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
            $table_name = strtolower($schema_name) . '.beneficiary';
        } else {
            $table_name = 'pension.beneficiary';
        }
        return $table_name;
    }
    public function index(){
        $user_id = AuthChecker::getUserId();
        $designation = Auth::user()->designation_id_old;
        $mapObj = DB::connection('pgsql_mis')
            ->table('public.duty_assignement')
            ->where('user_id', $user_id)
            ->where('is_active', 1)
            ->first();
        $scheme = DB::connection('pgsql_mis')->select(
                'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
                    $user_id .
                    ' and is_active=1) and id in(2,10,11) order by scheme_name'
        ); 
        if (Auth::user()->designation_id_old == 'Verifier') {
            if (count($scheme) > 0) {
                if ($mapObj->is_urban == 1) {
                    
                    $urban_body_code = $mapObj->urban_body_code;
                    $urban_bodys = UrbanBody::where(
                        'sub_district_code',
                        $urban_body_code
                    )
                        ->select('urban_body_code', 'urban_body_name')
                        ->get();
                    return view('validation-correction-pending/index', [
                        'schemes' => $scheme,
                        'mapLevel' => $mapObj->mapping_level . $designation,
                        'urban_bodys' => $urban_bodys,
                        'local_body_code' => $urban_body_code,
                        'district_code' => $mapObj->district_code,
                    ]);
                } else {
                    $taluka_code = $mapObj->taluka_code;
                    $gps = GP::where('block_code', $taluka_code)
                        ->select('gram_panchyat_code', 'gram_panchyat_name')
                        ->get();
                    return view('validation-correction-pending/index', [
                        'schemes' => $scheme,
                        'mapLevel' => $mapObj->mapping_level . $designation,
                        'gps' => $gps,
                        'local_body_code' => $taluka_code,
                        'district_code' => $mapObj->district_code,
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
    public function listing(Request $request){
        
        if ($request->ajax()) {
            $scheme_id = $request->scheme_id;
            $local_body_code = $request->local_body;
            $dist_code = $request->district_code; 
            $mapLevel= $request->mapLevel;
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
                         WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.failed_type = 2 and f.matching_score between ".$this->min_score." and ".$this->max_score." and b.gp_ward_code=".$request->filter_1." ";
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
                             WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.failed_type = 2 and f.matching_score between ".$this->min_score." and ".$this->max_score."";
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
                             WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.failed_type = 2 and f.matching_score between ".$this->min_score." and ".$this->max_score." and  b.block_ulb_code=".$request->filter_1."";
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
                             WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.failed_type = 2 and f.matching_score between ".$this->min_score." and ".$this->max_score." and b.block_ulb_code=".$request->filter_1." and b.gp_ward_code=".$request->filter_2."";
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
                             WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.failed_type = 2 and f.matching_score between ".$this->min_score." and ".$this->max_score."";
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
                         WHERE f.scheme_id=".$scheme_id." and f.dist_code=".$dist_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.failed_type = 2 and f.matching_score between ".$this->min_score." and ".$this->max_score."";
            }else{
                return redirect('/')->with('success', 'UnAuthorized');
            }
            
            $data = DB::connection('pgsql_paywrite')->select($query);
            // dd($data);
            return datatables()
            ->of($data)
            ->addColumn('view', function ($data)  {
                return '<button onclick=editFunction(' .
                    $data->ben_id .
                    ',' .
                    $data->scheme_id .
                    ') class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i>Edit</button>';
                })
                ->addColumn('download', function ($data) {
                    return '<div class="btn-group" role="group" aria-label="Basic example">' .
                        '<button onclick="benDownloadFunction(' . $data->ben_id . ',' . $data->scheme_id . ')" class="btn btn-success btn-xs">বাংলা</button>' .
                        '<button onclick="engDownloadFunction(' . $data->ben_id . ',' . $data->scheme_id . ')" class="btn btn-warning btn-xs">English</button>' .
                    '</div>';
                })
                    // &nbsp;&nbsp;
                    // <button onclick=downloadFunction(' .
                    // $data->ben_id .
                    // ',' .
                    // $data->scheme_id .
                    // ') class="btn btn-xs btn-info"><i class="fa fa-download"></i> Application Form</button>';

//                 <div class="btn-group" role="group" aria-label="Basic example">
//   <button type="button" class="btn btn-secondary">Left</button>
//   <button type="button" class="btn btn-secondary">Middle</button>
//   <button type="button" class="btn btn-secondary">Right</button>
// </div>
           
            // ->addColumn('download', function ($data)  {
            //     return ;
            // })
            ->addColumn('ben_id', function ($data) {
                return $data->ben_id;
            })
            ->addColumn('ben_name', function ($data) {
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
            ->addColumn('gp_ward_name', function ($data) {
                return $data->gp_ward_name;
            })
            ->rawColumns(['view','download','ben_id', 'name'])
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
        // dd($request->all());
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
            if($process_type == 43 ||  $process_type == 44) 
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
            $mappingLevel = $mapObj->mapping_level;
            $id = $request->id;
            $scheme_id = $request->scheme_id;
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
            ->where('f.ben_id', $id)
            ->first();
            $new_value = [];
            $old_value = [];
            $failed_payment = [];
            if( $process_type == 43 ||  $process_type == 44){
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
                // $benDuplicateAcCount = DB::connection('pgsql_mis')
                // ->table($table)
                // ->whereRaw(
                //     'trim(bank_code)=trim(' .
                //         "'" .
                //         $new_bank_code .
                //         "'" .
                //         ')'
                // )
                // ->where('id', '!=', $id)
                // ->where('is_rejected', 0)
                // ->where('scheme_id', $scheme_id)
                // ->count('id');
                $benDuplicateAcCount = DB::connection('pgsql_paywrite')->select("Select payment.bank_duplicate_check(in_scheme_id => ARRAY[".$scheme_id."]::INTEGER[], in_bank_codes => ARRAY[".$new_bank_code."]::VARCHAR[])"); 
                if ($benDuplicateAcCount[0]->bank_duplicate_check != NULL) {
                        $msg =
                            'This Bank A/c - ' .
                            $new_bank_code .
                            ' & IFSC - ' .
                            $new_bank_ifsc .
                            ' already exist in this scheme';
                    return $response = [
                        'status' => 3,
                        'msg' => $msg,
                        'type' => 'blue',
                        'icon' => 'fa fa-warning',
                        'title' => 'Warning!!',
                    ];
                }   
                if ($benDetails == null) {
                    return $response = [
                        'status' => 1,
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
                $updateBenDetailsData1 = ['old_data'=>json_encode($old_value),'new_data'=>json_encode($new_value)];
            }
            $failed_payment['edited_status'] = 1;
            $failed_payment['updated_at'] = date('Y-m-d H:i:s');

            if($process_type == 41){
                $failed_payment['failed_process_type'] = 41; 
                $ben_name['ben_name'] = $payment_details->ben_name;
                $av_name_response['ben_name']= trim($payment_details->av_name_response);
                $failed_payment['updated_details'] = json_encode($av_name_response);
                $updateBenDetailsData1 = ['old_data'=>json_encode($ben_name),'new_data'=>json_encode($av_name_response)];
                // $msg='Beneficiary Name '.$payment_details->ben_name.' will be replace with the name response from bank '.$payment_details->av_name_response.'';
            }else if($process_type == 42){
                $failed_payment['failed_process_type'] = $process_type; 
            }else if($process_type == 43){
                $failed_payment['failed_process_type'] = $process_type; 
                $failed_payment['updated_details'] = json_encode($new_value);
            } else{
                $failed_payment['failed_process_type'] = $process_type; 
                $failed_payment['updated_details'] = json_encode($new_value);
            }
            
            $updateBenDetailsData = [
                'original_application_id' => $id,
                'dist_code' => $benDetails->dist_code,
                'scheme_id' => $benDetails->scheme_id,
                'remarks' => $request->remarks,
                // 'old_data' => json_encode($old_value),
                // 'new_data' => json_encode($new_value),
                'user_id' => Auth::user()->id,
                'update_code' => $process_type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'ip_address' => $ip_address,
                'next_level_role_id' => $process_type,
            ];
            if($process_type == 41 ||$process_type == 43 ||$process_type == 44){
                $updateBenDetailsData = array_merge($updateBenDetailsData, $updateBenDetailsData1);
            }
            $is_document_uploaded = 0;
            $is_upload = 0;
            $bank_passbook = $request->file('upload_bank_passbook');
            $aadhar_card = $request->file('upload_aadhar_card');
            $application_form = $request->file('upload_application_form');
            DB::beginTransaction();
            DB::connection('pgsql_paywrite')->beginTransaction();
            DB::connection('pgsql_encwrite')->beginTransaction();
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
                        $fun_call = DB::connection('pgsql_encwrite')->select( "SELECT jb_doc.ben_docs_insert_archive(in_beneficiary_id => " . $id . ",
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
                        $doc_upload = $fun_call[0]->ben_docs_insert_archive;
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
                'modified_accno' => ($process_type == 43 || $process_type == 44)? $new_bank_code:NULL,
                'modified_ifsc' => ($process_type == 43 || $process_type == 44)? $new_bank_ifsc:NULL, 
                'failed_process_type' => $process_type,
                'modify_phase' => 1,
                'verified_at' => date('Y-m-d H:i:s'),
                'verified_by' => Auth::user()->id,
                'is_legacy' => true
            ];
            if($is_validation == 1){
                if($is_upload == 1){
                    $is_update = UpdateBenDetails::insert($updateBenDetailsData);
                    if($is_update == 1){
                        $legacy_validation_insert = DB::connection('pgsql_paywrite')->table('payment.validation_failed_modified')->insert($legacy_validation_failed);
                        if($legacy_validation_insert == 1){
                            $is_saved = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')
                            ->where('ben_id', $id)
                            ->where('scheme_id', $scheme_id)
                            ->where('edited_status', 0)
                            ->where('failed_type', 2) //Temporary Code
                            ->update($failed_payment);
                            if($is_saved){
                                DB::commit();
                                DB::connection('pgsql_encwrite')->commit();
                                DB::connection('pgsql_paywrite')->commit();
                                $response = [
                                    'status' => 1,
                                    'msg' => 'Bank Details Verified Successfully',
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
        //   dd($request->all());
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
            $user_id = AuthChecker::getUserId();
            $designation = Auth::user()->designation_id_old;
            $mapObj = DB::connection('pgsql_mis')
                ->table('public.duty_assignement')
                ->where('user_id', $user_id)
                ->where('is_active', 1)
                ->first();
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
                             WHERE f.scheme_id=".$scheme_id." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.failed_type = 2 and f.matching_score between ".$this->min_score." and ".$this->max_score." ";
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
                             WHERE f.scheme_id=".$scheme_id." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.acc_validated = 4 and f.failed_type = 2 and f.matching_score between ".$this->min_score." and ".$this->max_score."";
            }
            if($gp_mun != NULL){
                $query .= " and gw.gp_ward_code =".$gp_mun."";
            }else{
                $query .= " and f.ben_id=".$id." and b.ben_id = ".$id."";
            }
            $ben_details = DB::connection('pgsql_paywrite')->select($query);
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
                        // dd($ben_address);
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
                            "address" => (trim($ben_address->pincode) != null || trim($ben_address->pincode) != '') ? 'Pin Code: ' .trim($ben_address->pincode) : '',
                        );
                        array_push($data_array, $data);
                    }
                    // dd($data_array);
                    $response = ['status' => 2, 'data' => $data_array];
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

    public function approver(){
        $user_id = AuthChecker::getUserId();
        $dutyObj = Configduty::where('user_id', '=', $user_id)
            ->where('is_active', 1)
            ->first();
        $distCode = $dutyObj->district_code;
        $scheme = DB::connection('pgsql_mis')->select(
                    'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
                        $user_id .
                        ' and is_active=1) order by scheme_name'
        );
        if (Auth::user()->designation_id_old == 'Approver') {
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
        //   dd($request->all());
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
            if (Auth::user()->designation_id_old == 'Approver' && !empty($scheme_id)) {
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
                         WHERE f.scheme_id=".$scheme_id." and f.dist_code=".$distCode." and f.edited_status=1 and b.ben_status=1 and b.rural_urban_id=".$rural_urban." and b.acc_validated = 4 and f.failed_type = 2 and f.matching_score between ".$this->min_score." and ".$this->max_score." and f.failed_process_type = ".$name_validation_type." ";
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
                         WHERE f.scheme_id=".$scheme_id." and f.dist_code=".$distCode." and f.edited_status=1 and b.ben_status=1 and b.rural_urban_id=".$rural_urban." and b.local_body_code=".$local_body_code." and b.acc_validated = 4 and f.failed_type = 2 and f.matching_score between ".$this->min_score." and ".$this->max_score." and f.failed_process_type = ".$name_validation_type."";
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
                         WHERE f.scheme_id=".$scheme_id." and f.dist_code=".$distCode." and f.edited_status=1 and b.ben_status=1 and b.acc_validated = 4 and f.failed_type = 2 and f.matching_score between ".$this->min_score." and ".$this->max_score." and f.failed_process_type = ".$name_validation_type."";
                }
              
                $data = DB::connection('pgsql_paywrite')->select($query);        

            } else {
                $data = collect([]);
            }
            //  dd($data);
            return datatables()
                ->of($data)
                ->addIndexColumn()
                ->addColumn('view', function ($data) use ($name_validation_type) {
                        $action =
                        '<button class="btn btn-primary btn-xs ben_view_button" value="' .
                        $data->ben_id .
                        '_' .
                        $data->scheme_id .
                        '"><i class="glyphicon glyphicon-edit"></i>View</button>';
                    
                    return $action;
                })
                ->addColumn('check', function ($data) use ($name_validation_type) {
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
            if($name_validation_type ==41){
                $header_msg ='Note:Bank Name may be taken as beneficiary name as bank name is correct.';
            }else if($name_validation_type ==42) {
                $header_msg ='Note:Passbook Corrected by Verifier.';
            }else if($name_validation_type ==43) {
                $header_msg ='Note:Bank account is of other family members, New account modified by verifier.';
            }else if($name_validation_type ==44) {
                $header_msg ='Note:Bank account is of Completely of other person out of family, New account number modified by verifier.';
            }
            $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
            ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
            ->where('f.failed_type',2)->where('f.edited_status',1)->where('ben.is_eligible',true)
            ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)
            ->where('ben.acc_validated',4)
            ->where('f.failed_process_type',$name_validation_type)
            ->where('f.ben_id', $id)
            ->first();
            // dd($ben_details);
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
                $response = array_merge($ben_arr, [
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
        // dd($request->all());
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
                        ->where('f.failed_process_type',$name_validation_type)
                        ->where('f.dist_code', $distCode)
                        ->first();

                    $decodeNewbank = json_decode($ben_details->updated_details);
                    if($name_validation_type == 43 || $name_validation_type == 44) 
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
                    if($name_validation_type == 41){
                        $update_beneficiaryTable['ben_fname'] = $decodeNewbank->ben_name;
                        $payment_details['acc_validated'] = 0;
                        $payment_details['ben_name'] = $decodeNewbank->ben_name;
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                    } else if($name_validation_type == 43 || $name_validation_type == 44){
                        $update_beneficiaryTable['bank_name'] = $new_bank_name;
                        $update_beneficiaryTable['branch_name'] = $new_branch_name;
                        $update_beneficiaryTable['bank_ifsc'] = trim( $new_bank_ifsc);
                        $update_beneficiaryTable['bank_code'] = trim( $new_bank_code);
                        $update_beneficiaryTable['npci_bank_code'] = $new_bank_code_npci;
                        $old_value = [
                            'old_bank_ifsc' => trim($old_bank_ifsc),
                            'old_bank_code' => trim($old_bank_code)
                        ];
                        $payment_details['last_accno'] = trim( $new_bank_code);
                        $payment_details['last_ifsc'] = trim( $new_bank_ifsc);
                        $payment_details['npci_bank_code'] = $new_bank_code_npci;
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                        $payment_details['acc_validated'] = 0;
                    }else if($name_validation_type == 42){
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                        $payment_details['acc_validated'] = 0;
                    }
                    $updateFailedTable['edited_status']= 2;
                    $updateFailedTable['updated_at']= date('Y-m-d H:i:s');
                    if($name_validation_type == 43 || $name_validation_type == 44){
                        $benDuplicateAcCount = DB::connection('pgsql_paywrite')->select("Select payment.bank_duplicate_check(in_scheme_id => ARRAY[".$scheme_id."]::INTEGER[], in_bank_codes => ARRAY[".$new_bank_code."]::VARCHAR[])"); 
                        if ($benDuplicateAcCount[0]->bank_duplicate_check != NULL) { 
                            $msg =
                                    'This Bank A/c - ' .
                                    $new_bank_code .
                                    ' & IFSC - ' .
                                    $new_bank_ifsc .
                                    ' already exist in this scheme';
                                    return $response = [
                                        'status' => 3,
                                        'msg' => $msg,
                                        'type' => 'blue',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];        
                        }   
                    }
                    $updateBenDetailsData1 = [
                        'old_data'=>json_encode($old_value),
                        'new_data'=>json_encode($decodeNewbank)
                    ];
                    $updateBenDetailsData = [
                        'original_application_id' => $ben_details->ben_id,
                        'dist_code' => $ben_details->dist_code,
                        'scheme_id' => $scheme_id,
                        'remarks' => $accept_reject_comments,
                        // 'old_data' => json_encode($old_value),
                        // 'new_data' =>  json_encode($decodeNewbank),
                        'user_id' => Auth::user()->id,
                        'update_code' => $name_validation_type,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'ip_address' => $ip_address,
                        'next_level_role_id' => 0,
                    ];
                    if($name_validation_type == 43 || $name_validation_type == 44){
                        $updateBenDetailsData = array_merge($updateBenDetailsData, $updateBenDetailsData1);
                    }
                    $legacy_validation_failed_update = [
                        'status' => 2,
                        'approval_at' => date('Y-m-d H:i:s'),
                        'approval_by' => Auth::user()->id,
                    ];
                    DB::beginTransaction();
                    DB::connection('pgsql_paywrite')->beginTransaction();
                    $is_update = UpdateBenDetails::insert($updateBenDetailsData);
                    if($is_update)
                    {
                        $legacy_validation_update = DB::connection('pgsql_paywrite')->table('payment.validation_failed_modified')->where('ben_id',$id)->where('scheme_id',$scheme_id)->update($legacy_validation_failed_update);
                        if($legacy_validation_update)
                        {
                            $failed_update = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')->where('ben_id',$id)->where('scheme_id',$scheme_id)->where('edited_status',1)->where('failed_type',2)->where('failed_process_type',$name_validation_type)->update($updateFailedTable);        
                            if ($failed_update)
                            {
                                $payment_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id',$id)->where('scheme_id',$scheme_id)->where('ben_status',1)->where('acc_validated',4)->where('is_eligible',true)->update($payment_details);   
                                if($payment_update)
                                {   
                                    if($name_validation_type == 41 ||$name_validation_type == 43 || $name_validation_type == 44){  
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
                                            'msg' => 'Bank Details Updated Successfully',
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
                                    'msg' => '2 Somethimg went wrong!!',
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
                                'msg' => '2 Somethimg went wrong!!',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                    }    
                } catch (\Exception $e) {
                    dd($e);
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
                        ->where('f.failed_process_type',$name_validation_type)
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
                    if($name_validation_type == 41){
                        $updateBenDetailsData1 = ['update_code'=>51];
                    }else if($name_validation_type == 42){
                        $updateBenDetailsData1 = ['update_code'=>52];
                    }else if($name_validation_type == 43){
                        $updateBenDetailsData1 = ['update_code'=>53];
                    }else if($name_validation_type == 44){
                        $updateBenDetailsData1 = ['update_code'=>54];
                    }
                    $updateBenDetailsData = [
                        'original_application_id' => $ben_details->ben_id,
                        'dist_code' => $ben_details->dist_code,
                        'scheme_id' => $scheme_id,
                        'remarks' => $accept_reject_comments,
                        'user_id' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'ip_address' => $ip_address,
                        // 'next_level_role_id' => 6,
                    ];
                    $updateBenDetailsData = array_merge($updateBenDetailsData, $updateBenDetailsData1);
                    $is_update = UpdateBenDetails::insert($updateBenDetailsData);
                    if ($is_update) 
                    {
                        $validation_failed_modified_del = DB::connection('pgsql_paywrite')->table('payment.validation_failed_modified')->where('ben_id',$id)->where('failed_process_type',$name_validation_type)->where('scheme_id',$scheme_id)->where('status',1)->delete();
                        if($validation_failed_modified_del)
                        {
                            $is_saved = DB::connection('pgsql_paywrite')->table($this->failed_table)
                            ->where('ben_id', $id)
                            ->where('scheme_id', $scheme_id)
                            ->where('edited_status', 1)
                            ->where('failed_type', 2)
                            ->where('dist_code', $distCode)
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
                        ->where('f.failed_process_type',$name_validation_type)
                        ->where('f.ben_id', $beneficiary_id)
                        ->first();
                        $decodeNewbank = json_decode($ben_details->updated_details);
                        if($name_validation_type == 43 || $name_validation_type == 44) 
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
                    if($name_validation_type == 41){
                        $update_beneficiaryTable['ben_fname'] = $decodeNewbank->ben_name;
                        $payment_details['acc_validated'] = 0;
                        $payment_details['ben_name'] = $decodeNewbank->ben_name;
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                    } else if($name_validation_type == 43 || $name_validation_type == 44){
                        $update_beneficiaryTable['bank_name'] = $new_bank_name;
                        $update_beneficiaryTable['branch_name'] = $new_branch_name;
                        $update_beneficiaryTable['bank_ifsc'] = trim( $new_bank_ifsc);
                        $update_beneficiaryTable['bank_code'] = trim( $new_bank_code);
                        $update_beneficiaryTable['npci_bank_code'] = $new_bank_code_npci;
                        $old_value = [
                            'old_bank_ifsc' => trim($old_bank_ifsc),
                            'old_bank_code' => trim($old_bank_code)
                        ];
                        $payment_details['last_accno'] = trim( $new_bank_code);
                        $payment_details['last_ifsc'] = trim( $new_bank_ifsc);
                        $payment_details['npci_bank_code'] = $new_bank_code_npci;
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                        $payment_details['acc_validated'] = 0;
                    }else if($name_validation_type == 42){
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                        $payment_details['acc_validated'] = 0;
                    }
                    $updateFailedTable['edited_status']= 2;
                    $updateFailedTable['updated_at']= date('Y-m-d H:i:s');
                    if($name_validation_type == 43 || $name_validation_type == 44){
                        // $paymentDuplicateAcCount = DB::connection('pgsql_paywrite')
                        //         ->table('payment.ben_payment_details')
                        //         // ->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")
                        //         ->whereRaw(
                        //             'trim(last_accno)=trim(' .
                        //                 "'" .
                        //                 $new_bank_code .
                        //                 "'" .
                        //                 ')'
                        //         )
                        //         ->where('ben_id', '!=', $beneficiary_id)
                        //         ->where('is_rejected', 0)
                        //         // ->where('ben_status',1)
                        //         ->where('is_eligible',true)
                        //         ->where('scheme_id', $scheme_id)
                        //         ->count('ben_id');
                        $benDuplicateAcCount = DB::connection('pgsql_paywrite')->select("Select payment.bank_duplicate_check(in_scheme_id => ARRAY[".$scheme_id."]::INTEGER[], in_bank_codes => ARRAY[".$new_bank_code."]::VARCHAR[])"); 
                        if ($benDuplicateAcCount[0]->bank_duplicate_check != NULL) { 
                            $msg =
                                    'This Bank A/c - ' .
                                    $new_bank_code .
                                    ' & IFSC - ' .
                                    $new_bank_ifsc .
                                    ' already exist in this scheme';
                                    return $response = [
                                        'status' => 3,
                                        'msg' => $msg,
                                        'type' => 'blue',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];        
                        }   
                    }
                    $updateBenDetailsData1 = [
                        'old_data'=>json_encode($old_value),
                        'new_data'=>json_encode($decodeNewbank)
                    ];
                    $updateBenDetailsData = [
                        'original_application_id' => $ben_details->ben_id,
                        'dist_code' => $ben_details->dist_code,
                        'scheme_id' => $scheme_id,
                        'remarks' => $accept_reject_comments,
                        // 'old_data' => json_encode($old_value),
                        // 'new_data' =>  json_encode($decodeNewbank),
                        'user_id' => Auth::user()->id,
                        'update_code' => $name_validation_type,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'ip_address' => request()->ip(),
                        'next_level_role_id' => 0,
                    ];
                    if($name_validation_type == 43 || $name_validation_type == 44){
                        $updateBenDetailsData = array_merge($updateBenDetailsData, $updateBenDetailsData1);
                    }
                    $legacy_validation_failed_update = [
                        'status' => 2,
                        'approval_at' => date('Y-m-d H:i:s'),
                        'approval_by' => Auth::user()->id,
                    ];
                    // dd($update_beneficiaryTable);
                        $is_update = UpdateBenDetails::insert($updateBenDetailsData);
                        $legacy_validation_update = DB::connection('pgsql_paywrite')->table('payment.validation_failed_modified')->where('ben_id',$beneficiary_id)->where('scheme_id',$scheme_id)->update($legacy_validation_failed_update);
                        $failed_update = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')->where('ben_id',$beneficiary_id)->where('scheme_id',$scheme_id)->where('edited_status',1)->update($updateFailedTable);
                        $payment_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id',$beneficiary_id)->where('scheme_id',$scheme_id)->where('ben_status',1)->where('is_eligible',true)->update($payment_details);
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
                    return response()->json($response, $statusCode);
                }
            }
        }
    }
}
