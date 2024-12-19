<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\GP;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use App\BeneficiaryPensions;
use App\BenDocsSc;
use App\BenDocsSt;
use App\DocumentType;
use App\Configduty;
use App\MapLavel;
use App\Scheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use App\BankDetails;
use App\Helpers\DupCheck;
use App\Helpers\AuthChecker;


class BenAccNameValidationController extends Controller
{
    public function __construct()
    {
        set_time_limit(120);
        $this->middleware('auth');
        $this->ben_status = -97;
        date_default_timezone_set('Asia/Kolkata');
        $this->failed_table ='payment.failed_payment_details';
        $this->payment_table ='payment.ben_payment_details';
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
            $table_name = strtolower($schema_name) . '.beneficiaries';
        } else {
            $table_name = 'pension.beneficiaries';
        }
        return $table_name;
    }
    public function index(){
        // return redirect('/')->with(
        //     'success',
        //     'As per the department instruction validation correction is temporarily suspended'
        // );
        $user_id = AuthChecker::getUserId();
        $designation = Auth::user()->designation_id_old;
        $mapObj = DB::connection('pgsql_mis')
            ->table('public.duty_assignement')
            ->where('user_id', $user_id)
            ->where('is_active', 1)
            ->first();
        //    dd($mapObj);    
        $scheme = DB::connection('pgsql_mis')->select(
            'select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=' .
                $user_id .
                ' and is_active=1) order by scheme_name'
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
                    return view('benNameAccValidation/index', [
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
                    return view('benNameAccValidation/index', [
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
        } elseif (Auth::user()->designation_id_old == 'Approver' ) {
            return view('benNameAccValidation/index', [
                'schemes' => $scheme,
                'mapLevel' => $mapObj->mapping_level . $designation,
                'district_code' => $mapObj->district_code,
            ]);
        } else {
            return redirect('/')->with('success', 'UnAuthorized');
        } 
    }
    public function getData(Request $request){
        if ($request->ajax()) {
            //   dd($request->all());
            $failed_type = $request->payment_mode;
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
                  edited_status=0 and failed_type=".$failed_type." group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
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
                         WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0  and b.legacy_validation = 0 and b.gp_ward_code=".$request->filter_1." ";
                }else{
                    $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                     edited_status=0 and failed_type=".$failed_type." group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
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
                             WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.legacy_validation = 0 ";
                }
                //  dd( $data);
            }elseif ($mapLevel == 'SubdivVerifier'){
                if (!empty($request->filter_1) && empty($request->filter_2)) {
                    $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                    edited_status=0 and failed_type=".$failed_type." group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
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
                             WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.legacy_validation = 0 and  b.block_ulb_code=".$request->filter_1."";
                }elseif (!empty($request->filter_1) && !empty($request->filter_2)){
                    $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                    edited_status=0 and failed_type=".$failed_type." group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
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
                             WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.legacy_validation = 0 and b.block_ulb_code=".$request->filter_1." and b.gp_ward_code=".$request->filter_2."";
                }else{
                    $query = "select * from
                    (select T.* from
                    (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                    edited_status=0 and failed_type=".$failed_type." group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
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
                             WHERE f.scheme_id=".$scheme_id." and f.local_body_code=".$local_body_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.legacy_validation = 0";
                }
            }elseif ($mapLevel == 'DistrictApprover' && ($scheme_id == 8 || $scheme_id == 9)){
                $query ="select * from
                (select T.* from
                (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                edited_status=0 and failed_type=".$failed_type." group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
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
                         WHERE f.scheme_id=".$scheme_id." and f.dist_code=".$dist_code." and f.edited_status=0 and b.ben_status=1 and b.is_eligible = true and b.is_rejected=0 and b.legacy_validation = 0";
            }else{
                return redirect('/')->with('success', 'UnAuthorized');
            }
            if($failed_type == 1){
                $query .= " and b.acc_validated = 3 and f.failed_type = 1 ";
            }
            if($failed_type == 2){
                $query .= " and b.acc_validated = 4 and f.failed_type = 2 ";
            }
            //  dd($query);
            $data = DB::connection('pgsql_paywrite')->select($query);
            return datatables()
            ->of($data)
            ->addColumn('view', function ($data) use ($failed_type) {
                return '<button onclick=editFunction(' .
                    $data->ben_id .
                    ',' .
                    $data->scheme_id .
                    ',"' .
                    $failed_type .
                    '") class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button>';
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
            ->rawColumns(['view', 'ben_id', 'name'])
                ->make(true);
        }
    }
    public function modalView(Request $request){
        
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
            $failed_type = $request->failed_type;
            
            if($failed_type == 1){
                $failed_type_id = 1;
                $acc_validated =3;
            }else{
                $failed_type_id = 2;
                $acc_validated = 4;
            }
            $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
            ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
            ->where('f.edited_status',0)->where('ben.is_eligible',true)
            ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)->where('f.failed_type', $failed_type_id)->where('ben.acc_validated',$acc_validated)
            ->where('f.ben_id', $id)
            ->first();
            //  dd($ben_details);
            $bank_details = BankDetails::where(
                'ifsc',
                trim($ben_details->last_ifsc)
            )
                ->where('is_active', 1)
                ->get(['bank', 'branch', 'bank_code'])
                ->first();
                  
            // if($failed_type_id == 4){
            //     $invalid_status = $ben_details->remarks;
            // }elseif($failed_type_id == 3 || $failed_type_id == 5) {
            //     $remarks = DB::connection('pgsql_paywrite')->table('sbi.credit_transaction_code')->where(
            //         'code',
            //         trim($ben_details->status_code)
            //     )->get(['description'])
            //         ->first();  
            //     $invalid_status = $remarks->description;  
            // } 
            if ($ben_details == null) {
                return $response = [
                    'status' => 1,
                    'msg' => 'Somethimg went wrong.',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            } else {
                $ben_arr = [
                    'failed_table_id' => $ben_details->id,
                    'ben_name' => $ben_details->ben_name,
                    'id' => $ben_details->ben_id,
                    'scheme_id' => $ben_details->scheme_id,
                    'caste' => trim($ben_details->caste),
                    'gender' => trim($ben_details->gender),
                    'bank_code' => trim($ben_details->last_accno),
                    'bank_ifsc' => trim($ben_details->last_ifsc),
                    'bank_name' => trim($bank_details->bank),
                    'branch_name' => trim($bank_details->branch),
                    'av_name_response' => trim($ben_details->av_name_response),
                ];
                //  dd($failed_type);
                $response = array_merge($ben_arr, [
                    'status' => 2,
                    'failed_type' => $failed_type,
                    // 'failed_reason' => $invalid_status,
                ]);
                // dd($response);
            }
        }catch (\Exception $e) {
                //   dd($e);
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
    public function verify(Request $request){
        // return redirect('/')->with(
        //     'success',
        //     'As per the department instruction validation correction is temporarily suspended'
        // );
        //   dd($request->all());
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            
            // $rules = [
            //     'bank_name' => 'required',
            //     'branch_name' => 'required',
            //     'bank_code' =>
            //         'required|numeric|between:00000000000000000000,9999999999999999999',
            //     'bank_ifsc' => 'required|max:20',
            // ];
            // $attributes = [
            //     'bank_name' => 'Bank name',
            //     'branch_name' => 'Branch name',
            //     'bank_ifsc' => 'IFSC',
            //     'bank_code' => 'A/c No',
            //     // 'upload_bank_passbook' => 'upload_bank_passbook'
            // ];
            // $messages = [
            //     'required' => 'The :attribute field is required.',
            //     'integer' => 'Only integer allowed for :attribute',
            //     'max' => 'Maximum of :size characters allowed for :attribute',
            //     'size' => 'The :attribute must be exactly :size.',
               
            // ];
            
            // $validator = Validator::make(
            //     $request->all(),
            //     $rules,
            //     $messages,
            //     $attributes
            // );
            // if ($validator->passes()) {
                $user_id = AuthChecker::getUserId();
                $mapObj = DB::connection('pgsql_mis')
                ->table('public.duty_assignement')
                ->where('user_id', $user_id)
                ->where('is_active', 1)
                ->first();
                $mappingLevel = $mapObj->mapping_level;
                $id = $request->id;
                $scheme_id = $request->scheme_id;
                $new_bank_ifsc = $request->bank_ifsc;
                $new_bank_code = $request->bank_code;
                $new_bank_name = $request->bank_name;
                $new_branch_name = $request->branch_name;
                $upload_bank_passbook = $request->upload_bank_passbook;
                $failed_type = $request->failed_type;
                $process_type = $request->process_type;
                $failed_table_id =$request->failed_table_id;
                $ip_address = request()->ip();
                $table = $this->getSchemaName($scheme_id);
                $benDetails = DB::connection('pgsql_mis')
                    ->table($table)
                    ->where('id', $id)
                    ->first();
                    $scheme_list = Config::get(
                            'constants.duplicate_bank_info_check'
                );
                if($failed_type == 1 || ($failed_type == 2 && $process_type == 1)){
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
                    if (in_array($scheme_id, $scheme_list)) {
                        if ($scheme_id == 8 || $scheme_id == 9) {
                            $benDuplicateAcCount = DB::connection('pgsql_mis')
                                ->table('pension.beneficiary')
                                // ->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")
                                ->whereRaw(
                                    'trim(bank_code)=trim(' .
                                        "'" .
                                        $new_bank_code .
                                        "'" .
                                        ')'
                                )
                                ->where('id', '!=', $id)
                                ->where('is_rejected', 0)
                                ->whereIn('scheme_id', [8, 9])
                                ->count('id');
                            // dump($benDuplicateAcCount);die;
                        } else {
                            $benDuplicateAcCount = DB::connection('pgsql_mis')
                                ->table($table)
                                // ->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")
                                ->whereRaw(
                                    'trim(bank_code)=trim(' .
                                        "'" .
                                        $new_bank_code .
                                        "'" .
                                        ')'
                                )
                                ->where('id', '!=', $id)
                                ->where('is_rejected', 0)
                                ->where('scheme_id', $scheme_id)
                                ->count('id');
                        }
                        if ($benDuplicateAcCount > 0) {
                            if ($scheme_id == 8 || $scheme_id == 9) {
                                $msg =
                                    'This Bank A/c - ' .
                                    $new_bank_code .
                                    ' & IFSC - ' .
                                    $new_bank_ifsc .
                                    ' already exist LPP Retainer or LPP Pensioner scheme';
                            } else {
                                $msg =
                                    'This Bank A/c - ' .
                                    $new_bank_code .
                                    ' & IFSC - ' .
                                    $new_bank_ifsc .
                                    ' already exist in this scheme';
                            }
                            return $response = [
                                'status' => 3,
                                'msg' => $msg,
                                'type' => 'blue',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }
                        if($scheme_id == 10){
                            if(!empty($new_bank_code)){
                                $DupCheckBankWP = DupCheck::getDupCheckBank(11,$new_bank_code);
                                // dd($DupCheckBankWP);
                                if(!empty($DupCheckBankWP)){   
                                  
                                    $msg = "Duplicate Bank Account Number present in Widow Pension Scheme with Beneficiary ID- $DupCheckBankWP";
                                    return $response = [
                                        'status' => 3,
                                        'msg' => $msg,
                                        'type' => 'blue',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];   
                                } 
                                $DupCheckBankLB = DupCheck::getDupCheckBank(20,$new_bank_code);
                                //  $DupCheckBankLB =764764;
                                if(!empty($DupCheckBankLB)){   
                                    $msg = "Duplicate Bank Account Number present in Lakshmir Bhandar Scheme with Application ID- $DupCheckBankLB";
                                    return $response = [
                                        'status' => 3,
                                        'msg' => $msg,
                                        'type' => 'blue',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];   
                                }
                            }
                        }
                        if($scheme_id == 11){
                            if(!empty($new_bank_code)){
                                $DupCheckBankOAP = DupCheck::getDupCheckBank(10,$new_bank_code);
                                if(!empty($DupCheckBankOAP)){   
                                    $msg = "Duplicate Bank Account Number present in Old Age Pension Scheme with Beneficiary ID- $DupCheckBankOAP";
                                    return $response = [
                                        'status' => 3,
                                        'msg' => $msg,
                                        'type' => 'blue',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];   
                                } 
                            }
                        }
                        if($scheme_id == 1 || $scheme_id == 3){
                            if(!empty($new_bank_code)){
                                $DupCheckBankLB = DupCheck::getDupCheckBank(20,$new_bank_code);
                                if(!empty($DupCheckBankLB)){   
                                    $msg = "Duplicate Bank Account Number present in Lakshmir Bhandar Scheme with Application ID- $DupCheckBankLB";
                                    return $response = [
                                        'status' => 3,
                                        'msg' => $msg,
                                        'type' => 'blue',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];   
                                }
                                $DupCheckBankOAP = DupCheck::getDupCheckBank(10,$new_bank_code);
                                if(!empty($DupCheckBankOAP)){   
                                    $msg = "Duplicate Bank Account Number present in Old Age Pension Scheme with Beneficiary ID- $DupCheckBankOAP";
                                    return $response = [
                                        'status' => 3,
                                        'msg' => $msg,
                                        'type' => 'blue',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];   
                                } 
                            }
                        }
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
                }
                DB::beginTransaction();
                DB::connection('pgsql_paywrite')->beginTransaction();
                DB::connection('pgsql_encwrite')->beginTransaction();
                $input = [];
                $new_value = [];
                $old_value = [];
                $failed_payment = [];
                $updateDupTable = [];
                $failed_old_id = [];
                if($failed_type == 1 || ($failed_type == 2 && $process_type == 1)){
                    $old_value['old_bank_name'] = trim($old_bank_name);
                    $old_value['old_branch_name'] = trim($old_branch_name);
                    $old_value['old_bank_ifsc'] = trim($old_bank_ifsc);
                    $old_value['old_bank_code'] = trim($old_bank_code);

                    $new_value['new_bank_name'] = trim($new_bank_name);
                    $new_value['new_branch_name'] = trim($new_branch_name);
                    $new_value['new_bank_ifsc'] = trim($new_bank_ifsc);
                    $new_value['new_bank_code'] = trim($new_bank_code);
                    $new_value['npci_bank_code'] = trim($new_bank_code_npci);
                }
                
                if ($failed_type == 1) {
                    $acc_validate_id = 3;
                    $update_code = 21;
                    $remarks = 'Account Validation Bank Update';
                    // $updateBenDetailsData1 = ['old_data'=>json_encode($old_value)];
                    // $updateBenDetailsData1 = ['new_data'=>json_encode($new_value)];
                    $updateBenDetailsData1 = ['old_data'=>json_encode($old_value),'new_data'=>json_encode($new_value)];
                } elseif ($failed_type == 2) {
                    $acc_validate_id = 4;
                    $update_code = 20;
                    if($process_type == 0){
                        $remarks = 'Name Validation Minor Mismatch';
                    }else if($process_type == 1){
                        $remarks = 'Name Validation Process with New Bank Account';
                    }else if($process_type == 2){
                        $remarks = 'Name Validation Major Mismatch';
                    }
                    
                     $updateBenDetailsData1 = ['next_level_role_id'=>20];
                    if($process_type == 1){
                        $updateBenDetailsData2 = [
                            'old_data'=>json_encode($old_value),
                            'new_data'=>json_encode($new_value)
                    ];
                    }
                }
                // dd($updateBenDetailsData1);
                $updateBenDetailsData = [
                    'original_application_id' => $id,
                    'dist_code' => $benDetails->dist_code,
                    'scheme_id' => $benDetails->scheme_id,
                    'remarks' => $remarks,
                    // 'old_data' => json_encode($old_value),
                    // 'new_data' => json_encode($new_value),
                    'user_id' => Auth::user()->id,
                    'update_code' => $update_code,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'ip_address' => $ip_address,
                ];
                $updateBenDetailsData = array_merge($updateBenDetailsData, $updateBenDetailsData1);
                if($process_type == 1){
                    $updateBenDetailsData = array_merge($updateBenDetailsData, $updateBenDetailsData2);
                }
                // dd($updateBenDetailsData);
                if($failed_type == 1 || ($failed_type == 2 && $process_type == 1)){
                    $failed_payment['updated_details'] = json_encode($new_value);
                    $failed_old_id['updated_details'] = json_encode($new_value);
                }
                $failed_payment['edited_status'] = 1;
                $failed_old_id['edited_status'] = 10;
                $failed_payment['updated_at'] = date('Y-m-d H:i:s');
                $failed_old_id['updated_at'] = date('Y-m-d H:i:s');
                if($failed_type == 2){
                    if($process_type == 0){
                        $failed_payment['failed_process_type'] = 1; 
                        $failed_old_id['failed_process_type'] = 1;
                    }else if($process_type == 1){
                        $failed_payment['failed_process_type'] = 2;
                        $failed_old_id['failed_process_type'] = 2;
                    }else if($process_type == 2){
                        $failed_payment['failed_process_type'] = 3;
                        $failed_old_id['failed_process_type'] = 3;
                    }
                }
                $is_document_uploaded = 0;
                $is_upload = 0;
                // dump($old_bank_ifsc);dump($new_bank_ifsc);dump($old_bank_code);dump($new_bank_code);die;
                if($failed_type == 1 || ($failed_type == 2 && $process_type == 1)){
                    if (trim($old_bank_ifsc) != trim($new_bank_ifsc) || trim($old_bank_code) != trim($new_bank_code)){
                        if (!empty($request->file('upload_bank_passbook'))) {
                            $is_document_uploaded = 1;
                        } else {
                            return $response = [
                                'status' => 2,
                                'msg' => 'Please upload bank passbook copy.',
                                'type' => 'red',
                                'icon' => 'fa fa-warning',
                                'title' => 'Required',
                            ];
                        }
                    }else{
                        if (!empty($request->file('upload_bank_passbook'))) {
                            $is_document_uploaded = 1;
                        } else {
                            $is_upload = 1;
                        }
                    }
                }else{
                    $is_upload = 1;
                }

                if ($is_document_uploaded == 1) {
                    $attributes = [];
                    $doc_arr = DocumentType::select('id','doc_type', 'doc_name','doc_size_kb')
                        ->where('id', 10)
                        ->first();
                    $doc_type = $doc_arr->id;
                    $required = 'required';
                    $rules['upload_bank_passbook'] =$required .'|mimes:' . $doc_arr->doc_type .'|max:' . $doc_arr->doc_size_kb . ',';
                    $messages['upload_bank_passbook.max'] = 'The file uploaded for ' . $doc_arr->doc_name . ' size must be less than :max KB';
                    $messages['upload_bank_passbook.mimes'] = 'The file uploaded for ' . $doc_arr->doc_name .  ' must be of type ' .   $doc_arr->doc_type;
                    $messages['upload_bank_passbook.required'] =   'Document for ' .   $doc_arr->doc_name .   ' must be uploaded';
                    $validator = Validator::make(
                        $request->all(),
                        $rules,
                        $messages,
                        $attributes
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
                        $upload_bank_passbook = $request->file('upload_bank_passbook' );
                        $img_data = file_get_contents($upload_bank_passbook);
                        $extension = $upload_bank_passbook->getClientOriginalExtension();
                        $mime_type = $upload_bank_passbook->getMimeType();
                        $base64 = base64_encode($img_data);
                        $c_datetime = date('Y-m-d H:i:s', time());
                        $fun_call = DB::connection('pgsql_encwrite')->select( "SELECT jb_doc.ben_docs_insert_archive(in_beneficiary_id => " . $id . ",
                            in_scheme_id => " . $scheme_id . ",in_document_type => " .$doc_type .", in_attched_document => '" . $base64 ."',
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
                        $is_upload = $fun_call[0]->ben_docs_insert_archive;
                    }
                }
                //  dd($failed_payment);
                if ($is_upload == 1) { 
                        // dd('okk');
                        $is_update = UpdateBenDetails::insert($updateBenDetailsData);
                        if ($is_update) {
                            $is_saved = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')
                            ->where('ben_id', $id)
                            ->where('scheme_id', $scheme_id)
                            ->where('edited_status', 0)
                            ->where('failed_type', $failed_type) //Temporary Code
                            ->where('id',$failed_table_id)
                            ->update($failed_payment);
                            $is_failed_update = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')
                            ->where('ben_id', $id)
                            ->where('scheme_id', $scheme_id)
                            ->where('edited_status', 0)
                            ->where('failed_type', $failed_type) //Temporary Code
                            ->where('id','<>',$failed_table_id)
                            ->update($failed_old_id);
                            if($is_saved){
                                DB::commit();
                                DB::connection('pgsql_encwrite')->commit();
                                DB::connection('pgsql_paywrite')->commit();
                                $response = [
                                    'status' => 1,
                                    'msg' =>
                                    'Bank Details Forward to Approver Successfully',
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
                
            // }
            // else {
            //     $return_status = 0;
            //     $return_msg = $validator->errors()->all();
            //     $response = [
            //         'status' => $return_status,
            //         'msg' => $return_msg,
            //         'type' => 'red',
            //         'icon' => 'fa fa-warning',
            //         'title' => 'Warning!!',
            //     ];
            // }
        } catch (\Exception $e) {
                    //  dd($e);
            DB::rollback();
            DB::connection('pgsql_encwrite')->rollback();
            DB::connection('pgsql_paywrite')->rollback();
            $response = [
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' =>
                    'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
        }finally {
            // dd($response);
            return response()->json($response, $statusCode);
        }
    }
    public function approvelist(){
        // return redirect('/')->with(
        //     'success',
        //     'As per the department instruction validation correction is temporarily suspended'
        // );
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
            return view('benNameAccValidation/acc-name-validation-approver', [
                'levels' => $levels,
                'dist_code' => $distCode,
                'schemes' => $scheme,
            ]);
        } else {
            return redirect('/')->with('success', 'Unauthorized');
        }    
    }
    public function getFailedBankListapprove(Request $request){
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
            $failed_type = $request->failed_type;
            $name_validation_type = $request->update_code;
            if(empty($name_validation_type)){
                $name_validation_type = NULL;
            }
            $table_name = $this->getSchemaName($scheme_id);
            if (Auth::user()->designation_id_old == 'Approver' && !empty($scheme_id) && !empty($failed_type)) {
                if (!empty($rural_urban) && empty($local_body_code)) {
                $query ="select * from
                (select T.* from
                (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                 edited_status=1 and failed_type=".$failed_type." group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                 JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                 Left JOIN 
                     (
                       select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                         union all
                       select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                      ) bu ON bu.block_ulb_code=b.block_ulb_code  
                 Left JOIN 
                      (
                        select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                         union all
                        select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                        ) gw ON gw.gp_ward_code=b.gp_ward_code 
                         WHERE f.scheme_id=".$scheme_id." and f.dist_code=".$distCode." and f.edited_status=1 and b.ben_status=1 and b.legacy_validation = 0 and b.is_eligible = true and b.rural_urban_id=".$rural_urban." ";
                }elseif(!empty($rural_urban) && !empty($local_body_code)){
                    $query ="select * from
                (select T.* from
                (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                edited_status=1 and failed_type=".$failed_type." group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                 JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                 Left JOIN 
                     (
                       select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                         union all
                       select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                      ) bu ON bu.block_ulb_code=b.block_ulb_code  
                 Left JOIN 
                      (
                        select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                         union all
                        select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                        ) gw ON gw.gp_ward_code=b.gp_ward_code 
                         WHERE f.scheme_id=".$scheme_id." and f.dist_code=".$distCode." and f.edited_status=1 and b.legacy_validation = 0 and b.ben_status=1 and b.is_eligible = true and b.rural_urban_id=".$rural_urban." and b.local_body_code=".$local_body_code."";
                }else{
                    $query ="select * from
                (select T.* from
                (select ben_id, max(created_at)as max_created_at  FROM payment.failed_payment_details where 
                 edited_status=1 and failed_type=".$failed_type." group by ben_id) as S JOIN  payment.failed_payment_details  as T ON  S.ben_id=T.ben_id and S.max_created_at=T.created_at) f
                 JOIN payment.ben_payment_details b ON f.ben_id=b.ben_id
                 Left JOIN 
                     (
                       select urban_body_code as block_ulb_code,urban_body_name as block_ulb_name from public.m_urban_body ub 
                         union all
                       select block_code as block_ulb_code,block_name as block_ulb_name from public.m_block mb
                      ) bu ON bu.block_ulb_code=b.block_ulb_code  
                 Left JOIN 
                      (
                        select gram_panchyat_code as gp_ward_code, gram_panchyat_name as gp_ward_name from public.m_gp 
                         union all
                        select urban_body_ward_code as gp_ward_code, urban_body_ward_name as gp_ward_name from public.m_urban_body_ward
                        ) gw ON gw.gp_ward_code=b.gp_ward_code 
                         WHERE f.scheme_id=".$scheme_id." and f.dist_code=".$distCode." and f.edited_status=1 and b.is_eligible = true and b.legacy_validation = 0 and b.ben_status=1 ";
                }
                if($failed_type == 1){
                    $query .= " and b.acc_validated = 3 and f.failed_type = 1 ";
                }
                if($failed_type == 2){
                    $query .= " and b.acc_validated = 4 and f.failed_type = 2 and failed_process_type =".$name_validation_type."";
                }
                $data = DB::connection('pgsql_paywrite')->select($query);        

            } else {
                $data = collect([]);
            }
            //  dd($data);
            return datatables()
                ->of($data)
                ->addIndexColumn()
                ->addColumn('view', function ($data) use ($name_validation_type,$failed_type) {
                    if($failed_type ==2){
                        $action =
                        '<button class="btn btn-primary btn-xs ben_view_button" value="' .
                        $data->ben_id .
                        '_' .
                        $data->scheme_id .
                        '_' .
                        $data->failed_type .
                        '_' .
                        $name_validation_type .
                        '"><i class="glyphicon glyphicon-edit"></i>View</button>';
                    }else{
                        $action =
                        '<button class="btn btn-primary btn-xs ben_view_button" value="' .
                        $data->ben_id .
                        '_' .
                        $data->scheme_id .
                        '_' .
                        $data->failed_type .
                        '"><i class="glyphicon glyphicon-edit"></i>View</button>';
                    }
                    
                    return $action;
                })
                ->addColumn('check', function ($data) use ($name_validation_type,$failed_type) {
                    if($failed_type == 2)
                    {
                        return '<input type="checkbox"  name="chkbx" class="all_checkbox"  onclick="controlCheckBox();" value="' .
                        $data->ben_id .
                        '_' .
                        $data->scheme_id .
                        '_' .
                        $data->failed_type .
                        '_' .
                        $name_validation_type .
                        '">';
                    }else{
                        return '<input type="checkbox"  name="chkbx" class="all_checkbox"  onclick="controlCheckBox();" value="' .
                        $data->ben_id .
                        '_' .
                        $data->scheme_id .
                        '_' .
                        $data->failed_type .
                        '">';
                    } 
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
    public function modalApproveView(Request $request){
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
            $failed_type_id = $parts[2];
            $table_name = $this->getSchemaName($scheme_id);
            $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
            ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
            ->where('f.failed_type',$failed_type_id)->where('f.edited_status',1)->where('ben.is_eligible',true)
            ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)
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
                // dd($decodeNewData);
                $bank_details = BankDetails::where(
                    'ifsc',
                    trim($ben_details->ifsc)
                )
                    ->where('is_active', 1)
                    ->get(['bank', 'branch', 'bank_code'])
                    ->first();   
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
                    'new_bank_code' =>$decodeNewData != null? trim($decodeNewData->new_bank_code): null,
                    'new_bank_ifsc' =>$decodeNewData != null? trim($decodeNewData->new_bank_ifsc): null,
                    'new_bank_name' =>$decodeNewData != null? trim($decodeNewData->new_bank_name): null,
                    'new_branch_name' =>$decodeNewData != null? trim($decodeNewData->new_branch_name): null,
                    'application_id' => $ben_details->ben_id,
                ];
                // dd($ben_arr);
                $response = array_merge($ben_arr, [
                    'status' => 2,
                    'failed_type' => $failed_type_id,
                ]);
            }
        } catch (\Exception $e) {
            //    dd($e);
            $response = [
                'exception' => true,
                'exception_message' => $e->getMessage(),
                // 'exception_message' =>
                //     'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
        } finally {
            return response()->json($response, $statusCode);
        }
    }
    public function approve(Request $request){
        // return redirect('/')->with(
        //     'success',
        //     'As per the department instruction validation correction is temporarily suspended'
        // );
            //   dd($request->all());
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
        if ($is_bulk == 0) {
            $single_app_id = $request->single_app_id;
            $parts = explode('_', $single_app_id);
            $id = $parts[0];
            $scheme_id = $parts[1];
            $failed_type = $parts[2];
            if($failed_type == 2){
                $name_validation_type = $parts[3];
            }
            if ($opreation_type == 'A') {
                try {
                    $user_id = AuthChecker::getUserId();
                    $ip_address = request()->ip();
                    $table = $this->getSchemaName($scheme_id);
                    $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
                        ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
                        ->where('f.failed_type',$failed_type)->where('f.edited_status',1)->where('ben.is_eligible',true)
                        ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)
                        ->where('f.ben_id', $id)
                        ->where('ben.legacy_validation',0)
                        ->where('f.dist_code', $distCode)
                        ->first();
                    if($failed_type == 1 || ($failed_type == 2 && $name_validation_type == 2)){
                        $decodeNewbank = json_decode($ben_details->updated_details);
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
                   
                    
                    DB::beginTransaction();
                    DB::connection('pgsql_paywrite')->beginTransaction();
                    // DB::connection('pgsql_encwrite')->beginTransaction();
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
                    if($failed_type == 1 || ($failed_type == 2 && $name_validation_type == 2)){
                        $update_beneficiaryTable['bank_name'] = $new_bank_name;
                        $update_beneficiaryTable['branch_name'] = $new_branch_name;
                        $update_beneficiaryTable['bank_ifsc'] = trim( $new_bank_ifsc);
                        $update_beneficiaryTable['bank_code'] = trim( $new_bank_code);
                        $update_beneficiaryTable['npci_bank_code'] = $new_bank_code_npci;
                        $old_value = [
                            'old_bank_ifsc' => trim($old_bank_ifsc),
                            'old_bank_code' => trim($old_bank_code)
                        ];
                    }
                    if($failed_type == 2 && $name_validation_type == 3){
                        $update_beneficiaryTable['next_level_role_id'] = -1;
                        $update_beneficiaryTable['is_rejected'] = 1;
                        $update_beneficiaryTable['is_approved'] = 2;
                        $update_beneficiaryTable['is_verified'] = 2;
                        $update_beneficiaryTable['rejected_date'] = date('Y-m-d H:i:s');
                    }
                    if($failed_type == 2 && $name_validation_type == 1){
                        $update_beneficiaryTable['acc_validated'] = 2;
                    }
                    if ($failed_type == 1) {
                        $payment_details['acc_validated'] = 0;
                        $payment_details['legacy_validation']=0;
                        $payment_details['last_accno'] = trim( $new_bank_code);
                        $payment_details['last_ifsc'] = trim( $new_bank_ifsc);
                        $payment_details['updated_at'] = date('Y-m-d H:i:s');
                        $payment_details['npci_bank_code'] = $new_bank_code_npci;
                        $update_code = 21;
                        $remarks = 'Account Validation Bank Update';
                    } elseif ($failed_type == 2) {
                        if($name_validation_type == 1){
                            $payment_details['acc_validated'] = 2;
                            $payment_details['updated_at'] = date('Y-m-d H:i:s');
                            $remarks = 'Name Validation Minor Mismatch';
                        }else if($name_validation_type == 2){
                            $payment_details['last_accno'] = trim( $new_bank_code);
                            $payment_details['last_ifsc'] = trim( $new_bank_ifsc);
                            $payment_details['npci_bank_code'] = $new_bank_code_npci;
                            $payment_details['updated_at'] = date('Y-m-d H:i:s');
                            $payment_details['acc_validated'] = 0;
                            $payment_details['legacy_validation']=0;
                            $remarks = 'Name Validation Process with New Bank Account';
                        }else if($name_validation_type == 3){
                            $payment_details['is_eligible'] = false;
                            $payment_details['is_rejected'] = 25;
                            $payment_details['rejected_at'] = date('Y-m-d H:i:s');
                            $payment_details['acc_validated'] = 5;
                            $remarks = 'Name Validation Major Mismatch';
                        }
                        $update_code = 20;
                        
                    } 
                    $updateFailedTable['edited_status']= 2;
                    $updateFailedTable['updated_at']= date('Y-m-d H:i:s');

                    if($failed_type == 1 || ($failed_type == 2 && $name_validation_type == 2)){
                        if ($scheme_id == 8 || $scheme_id == 9) {
                            $paymentDuplicateAcCount = DB::connection('pgsql_paywrite')
                                ->table('payment.ben_payment_details')
                                // ->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")
                                ->whereRaw(
                                    'trim(last_accno)=trim(' .
                                        "'" .
                                         $new_bank_code .
                                        "'" .
                                        ')'
                                )
                                ->where('ben_id', '!=', $id)
                                ->where('is_rejected', 0)
                                // ->where('ben_status',1)
                                ->where('is_eligible',true)
                                ->whereIn('scheme_id', [8, 9])
                                ->count('ben_id');
                            // dump($benDuplicateAcCount);die;
                        } else {
                            $paymentDuplicateAcCount = DB::connection('pgsql_paywrite')
                                ->table('payment.ben_payment_details')
                                // ->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")
                                ->whereRaw(
                                    'trim(last_accno)=trim(' .
                                        "'" .
                                        $new_bank_code .
                                        "'" .
                                        ')'
                                )
                                ->where('ben_id', '!=', $id)
                                ->where('is_rejected', 0)
                                // ->where('ben_status',1)
                                ->where('is_eligible',true)
                                ->where('scheme_id', $scheme_id)
                                ->count('ben_id');
                        } if ($paymentDuplicateAcCount > 0) {
                            if ($scheme_id == 8 || $scheme_id == 9) {
                                $msg =
                                    'This Bank A/c - ' .
                                    $new_bank_code .
                                    ' & IFSC - ' .
                                    $new_bank_ifsc .
                                    ' already exist LPP Retainer or LPP Pensioner scheme';
                            } else {
                                $msg =
                                    'This Bank A/c - ' .
                                    $new_bank_code .
                                    ' & IFSC - ' .
                                    $new_bank_ifsc .
                                    ' already exist in this scheme';
                            }
                            return $response = [
                                'status' => 3,
                                'msg' => $msg,
                                'type' => 'blue',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }

                        $updateBenDetailsData1 = [
                            'old_data'=>json_encode($old_value),
                            'new_data'=>json_encode($decodeNewbank)
                        ];
                        // $updateBenDetailsData1 = ['new_data'=>json_encode($decodeNewbank)];
                    }
                    $updateBenDetailsData = [
                        'original_application_id' => $ben_details->ben_id,
                        'dist_code' => $ben_details->dist_code,
                        'scheme_id' => $scheme_id,
                        'remarks' => $accept_reject_comments,
                        // 'old_data' => json_encode($old_value),
                        // 'new_data' =>  json_encode($decodeNewbank),
                        'user_id' => Auth::user()->id,
                        'update_code' => $update_code,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'ip_address' => $ip_address,
                        'next_level_role_id' => 0,
                    ];
                    if($failed_type == 1 || ($failed_type == 2 && $name_validation_type == 2)){
                        $updateBenDetailsData = array_merge($updateBenDetailsData, $updateBenDetailsData1);
                    }
                    $is_update = UpdateBenDetails::insert($updateBenDetailsData);
                    if($is_update)
                    {
                        $failed_update = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')->where('ben_id',$id)->where('scheme_id',$scheme_id)->where('edited_status',1)->update($updateFailedTable);        
                        if ($failed_update)
                        {
                            $payment_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id',$id)->where('scheme_id',$scheme_id)->where('ben_status',1)->where('is_eligible',true)->where('legacy_validation',0)->update($payment_details);   
                            if($payment_update)
                            {     
                                $is_saved = DB::table($table)
                                ->where('id', $id)
                                ->where('scheme_id', $scheme_id)
                                ->where('next_level_role_id', 0)
                                ->where('created_by_dist_code', $distCode)
                                ->update($update_beneficiaryTable);
                                if ($is_saved) {
                                    DB::commit();
                                    // DB::connection('pgsql_encwrite')->commit();
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
                                    // DB::connection('pgsql_encwrite')->rollback();
                                    DB::connection('pgsql_paywrite')->rollback();
                                    $response = [
                                        'status' => 3,
                                        'msg' => '3 Somethimg went wrong!!',
                                        'type' => 'red',
                                        'icon' => 'fa fa-warning',
                                        'title' => 'Warning!!',
                                    ];
                                }
                            }else {
                                DB::rollback();
                                // DB::connection('pgsql_encwrite')->rollback();
                                DB::connection('pgsql_paywrite')->rollback();
                                $response = [
                                    'status' => 3,
                                    'msg' => '3 Somethimg went wrong!!',
                                    'type' => 'red',
                                    'icon' => 'fa fa-warning',
                                    'title' => 'Warning!!',
                                ];
                            }
                        } else {
                            DB::rollback();
                            // DB::connection('pgsql_encwrite')->rollback();
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
                        // DB::connection('pgsql_encwrite')->rollback();
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
                        //   dd($e);
                    DB::rollback();
                    // DB::connection('pgsql_encwrite')->rollback();
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
            } elseif ($opreation_type == 'T') {
                $return_msg = 'Beneficiaries Reverted successfully';
                try {
                    $user_id = AuthChecker::getUserId();
                    $ip_address = request()->ip();
                    $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
                        ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
                        ->where('f.failed_type',$failed_type)->where('f.edited_status',1)->where('ben.is_eligible',true)
                        ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)
                        ->where('ben.legacy_validation',0)
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
                    if($failed_type ==2){
                        $update_failedTable['failed_process_type'] = 0;
                    }

                    if($failed_type == 1){
                        $updateBenDetailsData1 = ['next_level_role_id'=>22];
                    }else if($failed_type == 2){
                        $updateBenDetailsData1 = ['next_level_role_id'=>23];
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
                    if ($is_update) {
                        $is_saved = DB::connection('pgsql_paywrite')->table($this->failed_table)
                            ->where('ben_id', $id)
                            ->where('scheme_id', $scheme_id)
                            ->where('edited_status', 1)
                            ->where('failed_type', $failed_type)
                            ->where('dist_code', $distCode)
                            ->update($update_failedTable);

                        if ($is_saved) {
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
                    } else {
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
                    //   dd($e);
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
        }
        if ($is_bulk == 1) {
            //   dd($request->all());
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
                        $failed_type = $bulk_single_id_arr[2];
                        if($failed_type == 2){
                            $name_validation_type = $bulk_single_id_arr[3];
                        }
                        // dump($beneficiary_id);dump($scheme_id);dump($failed_type);dd($name_validation_type);
                        $table = $this->getSchemaName($scheme_id);
                        $ben_details = DB::connection('pgsql_paywrite')->table(DB::raw($this->failed_table . ' as f'))
                        ->join('payment.ben_payment_details as ben','f.ben_id', '=', 'ben.ben_id')
                        ->where('f.failed_type',$failed_type)->where('f.edited_status',1)->where('ben.is_eligible',true)
                        ->where('ben.ben_status',1)->where('f.scheme_id', $scheme_id)
                        ->where('ben.legacy_validation',0)
                        ->where('f.ben_id', $beneficiary_id)
                        ->first();
                        // dd($ben_details);
                        if($failed_type == 1 || ($failed_type == 2 && $name_validation_type == 2)){
                            $decodeNewbank = json_decode($ben_details->updated_details);
                            $new_bank_code = $decodeNewbank->new_bank_code;
                            $new_bank_ifsc = $decodeNewbank->new_bank_ifsc;
                            $new_bank_name = $decodeNewbank->new_bank_name;
                            $new_branch_name = $decodeNewbank->new_branch_name;
                            $old_bank_code = $ben_details->last_accno;
                            $old_bank_ifsc = $ben_details->last_ifsc;
                            if($decodeNewbank->npci_bank_code){
                                $new_bank_code_npci = $decodeNewbank->npci_bank_code;
                            }
                        }
                        $input = [];
                        $input_new = [];
                        $old_value = [];
                        $updateFailedTable = [];
                        $ben_payment_details = [];
                        $message_arr = [
                            1 => 'Updated Successfully',
                            2 => '2 Something went wrong...',
                        ];
                        $update_beneficiaryTable = [];
                        if($failed_type == 1 || ($failed_type == 2 && $name_validation_type == 2)){
                            $update_beneficiaryTable['bank_name'] = $new_bank_name;
                            $update_beneficiaryTable['branch_name'] = $new_branch_name;
                            $update_beneficiaryTable['bank_ifsc'] = trim( $new_bank_ifsc);
                            $update_beneficiaryTable['bank_code'] = trim( $new_bank_code);
                            $update_beneficiaryTable['npci_bank_code'] = $new_bank_code_npci;
                            $old_value = [
                                'old_bank_ifsc' => trim($old_bank_ifsc),
                                'old_bank_code' => trim($old_bank_code)
                            ];
                        }
                        if($failed_type == 2 && $name_validation_type == 1){
                            $update_beneficiaryTable['acc_validated'] = 2;
                        }
                        if($failed_type == 2 && $name_validation_type == 3){
                            $update_beneficiaryTable['next_level_role_id'] = -1;
                            $update_beneficiaryTable['is_rejected'] = 1;
                            $update_beneficiaryTable['is_approved'] = 2;
                            $update_beneficiaryTable['is_verified'] = 2;
                            $update_beneficiaryTable['rejected_date'] = date('Y-m-d H:i:s');
                        }

                        if($failed_type == 1) {
                            $payment_details['acc_validated'] = 0;
                            $payment_details['last_accno'] = trim( $new_bank_code);
                            $payment_details['last_ifsc'] = trim( $new_bank_ifsc);
                            $payment_details['updated_at'] = date('Y-m-d H:i:s');
                            $payment_details['npci_bank_code'] = $new_bank_code_npci;
                            $update_code = 21;
                            $remarks = 'Account Validation Bank Update';
                        } elseif ($failed_type == 2) {
                            if($name_validation_type == 1){
                                $payment_details['acc_validated'] = 2;
                                $payment_details['updated_at'] = date('Y-m-d H:i:s');
                                $remarks = 'Name Validation Minor Mismatch';
                            }else if($name_validation_type == 2){
                                $payment_details['last_accno'] = trim( $new_bank_code);
                                $payment_details['last_ifsc'] = trim( $new_bank_ifsc);
                                $payment_details['npci_bank_code'] = $new_bank_code_npci;
                                $payment_details['updated_at'] = date('Y-m-d H:i:s');
                                $payment_details['acc_validated'] = 0;
                                $remarks = 'Name Validation Process with New Bank Account';
                            }else if($name_validation_type == 3){
                                $payment_details['is_eligible'] = false;
                                $payment_details['is_rejected'] = 25;
                                $payment_details['rejected_at'] = date('Y-m-d H:i:s');
                                $payment_details['acc_validated'] = 5;
                                $remarks = 'Name Validation Major Mismatch';
                            }
                            $update_code = 20;   
                    } 
                    $updateFailedTable['edited_status']= 2;
                    $updateFailedTable['updated_at']= date('Y-m-d H:i:s');

                    if($failed_type == 1 || ($failed_type == 2 && $name_validation_type == 2)){
                        if ($scheme_id == 8 || $scheme_id == 9) {
                            $paymentDuplicateAcCount = DB::connection('pgsql_paywrite')
                                ->table('payment.ben_payment_details')
                                // ->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")
                                ->whereRaw(
                                    'trim(last_accno)=trim(' .
                                        "'" .
                                         $new_bank_code .
                                        "'" .
                                        ')'
                                )
                                ->where('ben_id', '!=', $beneficiary_id)
                                ->where('is_rejected', 0)
                                // ->where('ben_status',1)
                                ->where('is_eligible',true)
                                ->whereIn('scheme_id', [8, 9])
                                ->count('ben_id');
                            // dump($benDuplicateAcCount);die;
                        } else {
                            $paymentDuplicateAcCount = DB::connection('pgsql_paywrite')
                                ->table('payment.ben_payment_details')
                                // ->whereRaw("trim(bank_ifsc)=trim(" . "'" . $new_bank_ifsc . "'" . ")")
                                ->whereRaw(
                                    'trim(last_accno)=trim(' .
                                        "'" .
                                        $new_bank_code .
                                        "'" .
                                        ')'
                                )
                                ->where('ben_id', '!=', $beneficiary_id)
                                ->where('is_rejected', 0)
                                // ->where('ben_status',1)
                                ->where('is_eligible',true)
                                ->where('scheme_id', $scheme_id)
                                ->count('ben_id');
                        } if ($paymentDuplicateAcCount > 0) {
                            if ($scheme_id == 8 || $scheme_id == 9) {
                                $msg =
                                    'This Bank A/c - ' .
                                    $new_bank_code .
                                    ' & IFSC - ' .
                                    $new_bank_ifsc .
                                    ' already exist LPP Retainer or LPP Pensioner scheme';
                            } else {
                                $msg =
                                    'This Bank A/c - ' .
                                    $new_bank_code .
                                    ' & IFSC - ' .
                                    $new_bank_ifsc .
                                    ' already exist in this scheme';
                            }
                            return $response = [
                                'status' => 3,
                                'msg' => $msg,
                                'type' => 'blue',
                                'icon' => 'fa fa-warning',
                                'title' => 'Warning!!',
                            ];
                        }

                        $updateBenDetailsData1 = [
                            'old_data'=>json_encode($old_value),
                            'new_data'=>json_encode($decodeNewbank)
                        ];
                        // $updateBenDetailsData1 = ['new_data'=>json_encode($decodeNewbank)];
                    }
                
                    $updateBenDetailsData = [
                        'original_application_id' => $ben_details->ben_id,
                        'dist_code' => $ben_details->dist_code,
                        'scheme_id' => $scheme_id,
                        'remarks' => $accept_reject_comments,
                        // 'old_data' => json_encode($old_value),
                        // 'new_data' =>  json_encode($decodeNewbank),
                        'user_id' => Auth::user()->id,
                        'update_code' => $update_code,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'ip_address' => request()->ip(),
                        'next_level_role_id' => 0,
                    ];
                    if($failed_type == 1 || ($failed_type == 2 && $name_validation_type == 2)){
                        $updateBenDetailsData = array_merge($updateBenDetailsData, $updateBenDetailsData1);
                    }
                        $is_update = UpdateBenDetails::insert($updateBenDetailsData);
                        $failed_update = DB::connection('pgsql_paywrite')->table('payment.failed_payment_details')->where('ben_id',$beneficiary_id)->where('scheme_id',$scheme_id)->where('edited_status',1)->update($updateFailedTable);
                        $payment_update = DB::connection('pgsql_paywrite')->table('payment.ben_payment_details')->where('ben_id',$beneficiary_id)->where('scheme_id',$scheme_id)->where('ben_status',1)->where('legacy_validation',0)->where('is_eligible',true)->update($payment_details);
                        if ($payment_update) {
                            $is_saved = DB::table($table)
                            ->where('id', $beneficiary_id)
                            ->where('scheme_id', $scheme_id)
                            ->where('next_level_role_id', 0)
                            ->where('created_by_dist_code', $distCode)
                            ->update($update_beneficiaryTable);
                        }
                        if ($is_saved == 1 && $payment_update == 1 && $failed_update == 1) {
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
                    //    dd($e);
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
