<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configduty;
use App\District;
use App\Scheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\DataSourceCommon;
use App\getModelFunc;
use App\UrbanBody;
use App\GP;
use App\RejectRevertReason;
use Carbon\Carbon;
use App\DsPhase;
use App\Helpers\AuthChecker;


class MisReportMinormismatchController extends Controller
{
    protected $source_type;
    protected $base_dob_chk_date;
    protected $max_dob;
    protected $min_dob;
    

    public function __construct()
    {
        $this->middleware('auth');
        //$this->scheme_id = 20;
        $this->source_type = 'ss_nfsa';
        $phaseArr = DsPhase::where('is_current', TRUE)->first();
        $mydate = $phaseArr->base_dob;
        $max_date = strtotime("-25 year", strtotime($mydate));
        $max_date = date("Y-m-d", $max_date);
        $min_date = strtotime("-60 year", strtotime($mydate));
        $min_date = date("Y-m-d", $min_date);
        $this->base_dob_chk_date = $mydate;
        $this->max_dob = $max_date;
        $this->min_dob = $min_date;
    }
    public function schemeSelection(Request $request)
    {
        // echo 1;die;
        // try {
            //code...
            
        
        $report_type = '';

        if ($request->has('type')) {

           // dd(123);
            $report_type = $request->get('type');
            if ($report_type == 'M') {
               // Minor Mis-match Report
                $report_type_name = 'Minor Mismatch Report (Bank) Beneficiary List';
            } else {
                return redirect('/')->with('error', 'Error: Report type invalid');
            }
        } else {
            return redirect('/')->with('error', 'Signature Error: Report Type not selected');
        }
        $user_id = AuthChecker::getUserId();
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id  in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1) and is_active=1 order by scheme_name"));


        $duty_schemes = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->get()->pluck('scheme_id')->toArray();
        
        return view('MisReportMinormismatch.scheme', ['scheme_list' => $scheme_list, 'type' => $report_type, 'report_type_name' => $report_type_name]);
    // } catch (\Exception $th) {
    //     dd($th);
    // }
    }
    // public function schemeSessionCheck(Request $request)
    // {
    //     $scheme_id = $this->scheme_id;
    //     $is_active = 0;
    //     $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();;

    //     $designation_id = Auth::user()->designation_id;

    //     if($designation_id=='HOD')
    //     {
    //         $is_active=1;

    //     }else{

    //     //print_r($roleArray); die;
    //     foreach ($roleArray as $roleObj) {
    //         if ($roleObj['scheme_id'] == $scheme_id) {

               
    //             $is_active = 1;
    //             $request->session()->put('level', $roleObj['mapping_level']);
    //             $distCode = $roleObj['district_code'];
    //             $request->session()->put('distCode', $roleObj['district_code']);
    //             $request->session()->put('scheme_id', $scheme_id);
    //             $request->session()->put('is_first', $roleObj['is_first']);
    //             $request->session()->put('is_urban', $roleObj['is_urban']);
    //             $request->session()->put('role_id', $roleObj['id']);
    //             if ($roleObj['is_urban'] == 1) {
    //                 $request->session()->put('bodyCode', $roleObj['urban_body_code']);
    //             } else {
    //                 $request->session()->put('bodyCode', $roleObj['taluka_code']);
    //             }
    //             break;
    //         }
    //     }
    // }
    //     if ($is_active == 1) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }




    public function schemeSessionCheck(Request $request)
    {
        $scheme_id = 0;
        $ben_table = "";

        $pr1 = $request->get('pr1');

        $scheme_code_map = Config::get('constants.scheme_code_map');
        $scheme_details = '';
        if (array_key_exists($pr1, $scheme_code_map)) {
            $scheme_details = $scheme_code_map[$pr1];

            $scheme_id = $scheme_details['scheme_id'];
            $scheme_row = Scheme::where('id', $scheme_id)->first();
            $scheme_model = 'App\\' . $scheme_details['model_name'];

            $is_active = 0;
            $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                        $designation_id = Auth::user()->designation_id;
            // dd($designation_id);
            if ($designation_id == 'HOD') {
                if ($scheme_id == 2 || $scheme_id == 11 || $scheme_id == 13 || $scheme_id == 17 || $scheme_id == 18 || $scheme_id == 8 || $scheme_id == 9 || $scheme_id == 10) {
                    $request->session()->put('scheme_id', $scheme_id);
                    $request->session()->put('scheme_name', $scheme_row['scheme_name']);
                    $request->session()->put('model_name', $scheme_model);
                    $is_active = 1;
                }
            } else {
                foreach ($roleArray as $roleObj) {
                    if ($roleObj['scheme_id'] == $scheme_id) {
                        $is_active = 1;
                        //dd($roleObj);
                        $request->session()->put('scheme_id', $scheme_id);
                        $request->session()->put('scheme_name', $scheme_row['scheme_name']);
                        $request->session()->put('model_name', $scheme_model);

                        $request->session()->put('is_first', $roleObj['is_first']);
                        $request->session()->put('role_id', $roleObj['id']);
                        $request->session()->put('role_name', $roleObj['role_name']);


                        $request->session()->put('level', $roleObj['mapping_level']);
                        $request->session()->put('distCode', $roleObj['district_code']);
                        $request->session()->put('is_urban', $roleObj['is_urban']);
                        if ($roleObj['is_urban'] == 1) {
                            $request->session()->put('bodyCode', $roleObj['urban_body_code']);
                        } else {
                            $request->session()->put('bodyCode', $roleObj['taluka_code']);
                        }
                        break;
                    }
                }
            }
            return $is_active;
        }
        return -1;
    }


    // public function getData(Request $request)
    // {
    //     dd(333);
    // }
            public function applicationStatusList(Request $request)
            {

            //$phase_list = DsPhase::get();


            if (!$request->has('pr1')) {
            return redirect('/')->with('error', 'Signature Error: Scheme Type not selected');
            }

            if ($this->schemeSessionCheck($request)) {
                
            $pr1 = $request->get('pr1');
            $mappingLevel = $request->session()->get('level');
            $role_name = Auth::user()->designation_id;

            //dd($role_name);
            //$rejection_cause_list = Config::get('constants.rejection_cause');
            $rejection_cause_list = RejectRevertReason::where('status', true)->get()->toArray();
            $is_rural_visible = 0;
            $district_visible =0;
            $urban_visible = 0;
            $munc_visible = 0;
            $gp_ward_visible = 0;
            $is_urban=0;
            $district=0;
            $urban_body_code=0;
            $muncList = collect([]);
            $gpwardList = collect([]);
            // $modelName = new DataSourceCommon;
            // $modelName_payment= new DataSourceCommon;
            // $getModelFunc = new getModelFunc();
            //$caste = $request->caste;

            //$faulty_value=$request->faulty;
            $flage=$request->flage;

            //dd($faulty_value);

            //dd($faulty_value);
            // if(empty($faulty_value))
            // {
            //     $faulty_value=0;

            // }
            // else if($faulty_value==0)
            // {
            //     $faulty_value=0;

            // }
            // else{
            //     $faulty_value=1;
            // }
            //dd($faulty_value);
            $block_ulb_code = $request->block_ulb_code;
            $gp_ward_code = $request->gp_ward_code;
            $download_excel = 1;
            $districts=0;

            //dd($request->rural_urbanid);
            //dd($role_name);
            if ($role_name == 'Approver') {

           
            $type_new='A';

            $district_visible=0;
            $is_urban = $request->rural_urbanid;
            $district_code = $request->session()->get('distCode');
            $urban_body_code = $request->urban_body_code;
            $block_ulb_code = $request->block_ulb_code;
            $is_rural_visible = 1;
            $urban_visible = 1;
            $munc_visible = 1;
            $gp_ward_visible = 1;
            } else if ($role_name == 'Verifier' || $role_name == 'Operator' ) {
            $type_new='V';
            $district_visible=0;
            $district_code = $request->session()->get('distCode');
            if ($mappingLevel == 'Block') {
            $type_new='B';
            $block_ulb_code = NULL;
            //$district_visible=0;
            $is_rural_visible = 0;
            $is_urban = 2;
            $munc_visible = 0;
            $urban_body_code = $request->session()->get('bodyCode');
            $block_ulb_code = NULL;
            $gpwardList = GP::where('block_code', $urban_body_code)->get();
            $gp_ward_visible = 1;
            } else if ($mappingLevel == 'Subdiv') {
            $type_new='S';
            $block_ulb_code = $request->block_ulb_code;
            $urban_body_code = $request->session()->get('bodyCode');
            $is_rural_visible = 0;
            $is_urban = 1;
            $munc_visible = 1;
            $gp_ward_visible = 1;
            $muncList = UrbanBody::where('sub_district_code', $urban_body_code)->get();
            $block_ulb_code = $request->block_ulb_code;
            }

            }

            else{
            $type_new='H';
            $district_visible = 1;

            $is_urban = $request->rural_urbanid;

            if($flage==1)
            {
            $district_code = $request->district_code;

            }
            else{

            $district_code = District::select('district_code','district_name')->get();
            }

            $urban_body_code =$request->urban_body_code;
            $block_ulb_code = $request->block_ulb_code;
            $is_rural_visible = 1;
            $urban_visible = 1;
            $munc_visible = 1;
            $gp_ward_visible = 1;

            }
            $condition = array();


            //     dd(type);
            $report_type = $request->get('type');


            if ($report_type == 'M') {
           
            $is_draft = NULL;
            $report_type_name = 'Minor Mismatch Report (Bank) Beneficiary List';




            $Table = 'pension.failed_payment_details';
            $Tableupdate = 'public.update_ben_details';
            


            //  $Table_payment='lb_main.failed_payment_details';


            $condition[$Table . ".next_level_role_id_validation"] = 0;
            $condition[$Table . ".failed_process_type"] = 1;
            $condition[$Tableupdate . ".update_code"] = 20;
            $condition[$Table . ".acc_validated"] = -2;

            // $condition[$Table . ".next_level_role_id"] = 0;
            // $condition[$Table . ".failed_process_type"] = 1;
            // $condition[$Tableupdate . ".update_code"] = 22;
            // //$condition[$Table . ".acc_validated"] = -2;

           


            //$Table = $getModelFunc->getTable($district_code, $this->source_type, 1, $is_draft);
            // $modelName->setConnection('pgsql_appread');
            // $modelName->setTable('' . $Table);
            // $modelName_payment->setConnection('pgsql_appread');
            // $modelName_payment->setTable('' . $Table_payment);
            $column = 'ben_id';
            $column1 = DB::raw('MAX(public.update_ben_details.created_at) as updated_at');
            $column2 = 'block_ulb_name';
            $column3 = 'gp_ward_name';
            $column4 = 'av_name_response';
            $column5='id';

            if ($role_name == 'Approver') {
            $download_excel = 1;
            } else if ($role_name == 'Verifier' || $role_name == 'Operator') {
            $download_excel = 1;
            } else {
            $download_excel = 1;
            }
           
            } 


            else {


            return redirect('/')->with('error', 'Error: Report type invalid');
            }

            if (request()->ajax()) 
            {
               
            //dd($district_code);
            // District Filter
            $pr1 = $request->get('pr1');
            
            $scheme_id = $request->session()->get('scheme_id');

            //dd($pr1);
            if (!empty($district_code)) {

            // dd(123);
            $condition[$Table . ".created_by_dist_code"] = $district_code;
            }
            else{
            $condition[$Table . ".created_by_dist_code"] = NULL;

            }
            //dd($is_urban);
            if (!empty($is_urban)) {


            if ($is_urban == 2) {
            $condition[$Table . ".rural_urban_id"] = 2;
            if (!empty($urban_body_code)) {

            //dd(123);
            //$condition["rural_urban_id"] = 2;
            $condition[$Table . ".created_by_local_body_code"] = $urban_body_code;
            $condition[$Table . ".rural_urban_id"] = 2;
            }
            }
            //'Urban'
            if ($is_urban == 1) {
            $condition[$Table . ".rural_urban_id"] = 1;
            if (!empty($urban_body_code)) {
            //$condition["rural_urban_id"] = 1;
            $condition[$Table . ".created_by_local_body_code"] = $urban_body_code;
            $condition[$Table . ".rural_urban_id"] = 1;
            }
            if (!empty($block_ulb_code)) {
            $condition[$Table . ".block_ulb_code"] = $block_ulb_code;
            $condition[$Table . ".rural_urban_id"] = 1;
            }
            }
            }

            if (!empty($gp_ward_code)) {
            $condition[$Table . ".gp_ward_code"] = $gp_ward_code;
            $condition[$Table . ".rural_urban_id"] = 2;
            }
            $condition[$Table . ".scheme_id"] = $pr1;
            $serachvalue = $request->search['value'];
                $limit = $request->input('length');
                $offset = $request->input('start');
               // dd($offset);
                

                $totalRecords = 0;
                $filterRecords = 0;
                $data = array();
                // $model_name = $request->session()->get('model_name');
                // $query = DB::connection('pgsql_mis')->table( $Table)->where($condition);

                // $query = $query->join($Tableupdate, $Tableupdate . '.original_application_id', '=', $Table . '.ben_id');
                // if ($report_type == 'M') {
                //     $query = $query->join($contact_table, $contact_table . '.beneficiary_id', '=', $Table . '.beneficiary_id');
                //     $query = $query->leftjoin($update_table, $update_table . '.beneficiary_id', '=', $Table . '.beneficiary_id');
                // }
                
                
                if (empty($serachvalue)) {
                   //dd(123);
                 //$data = $query->orderBy($Tableupdate . '.id','DESC')->orderBy($Table . '.gp_ward_name')->offset($offset)->limit($limit)->distinct($Tableupdate.'.ben_id')->toSql();
                 //dd($data);


                $data1 = DB::connection('pgsql_mis')->table('pension.failed_payment_details')
                ->select(
                'pension.failed_payment_details.ben_id as beneficiary_id',
                
                DB::raw('MAX(public.update_ben_details.created_at) as updated_at')
                )
                ->leftJoin('public.update_ben_details', 'public.update_ben_details.original_application_id', '=', 'pension.failed_payment_details.ben_id')
                ->where($condition)
                ->groupBy(
                'pension.failed_payment_details.ben_id'
                
                )->get();

            $data = DB::connection('pgsql_mis')->table('pension.failed_payment_details')
            ->select(
            'pension.failed_payment_details.ben_id as beneficiary_id',
            'pension.failed_payment_details.gp_ward_name',

            'public.update_ben_details.original_application_id',
            'pension.failed_payment_details.block_ulb_name',
            'pension.failed_payment_details.av_name_response as name_response',
            'pension.failed_payment_details.ben_name as ben_name',
            DB::raw('MAX(public.update_ben_details.created_at) as updated_at')
            )
            ->leftJoin('public.update_ben_details', 'public.update_ben_details.original_application_id', '=', 'pension.failed_payment_details.ben_id')
            ->where($condition)
            ->groupBy(
            'pension.failed_payment_details.ben_id',
            'pension.failed_payment_details.gp_ward_name',
            'public.update_ben_details.original_application_id',
            'pension.failed_payment_details.block_ulb_name',
            'pension.failed_payment_details.av_name_response',
            'pension.failed_payment_details.ben_name'
            )
            ->orderBy('pension.failed_payment_details.gp_ward_name', 'asc')
            ->orderBy('pension.failed_payment_details.ben_name', 'asc')
            ->limit($limit)
            ->offset($offset)
            ->get();

           // dd($data);
            $totalRecords = count($data1);

            //dd($totalRecords);




               

                // dd($data);

                    // $data =  $query->groupBy($Table . '.ben_id')->groupBy($Tableupdate . '.id')->groupBy($Table . '.ben_name')
                    // ->groupBy($Tableupdate . '.created_at')->groupBy($Table . '.block_ulb_name')->groupBy($Tableupdate . '.original_application_id')->groupBy($Table . '.gp_ward_name')->groupBy($Table . '.av_name_response')
                    // ->offset($offset)->limit($limit)->distinct($Tableupdate.'.original_application_id')->get([
                    //     '' . $Table . '.' . $column . ' as beneficiary_id', 
                    //     'ben_name', DB::raw('MAX(public.update_ben_details.created_at) as updated_at'),'' . $Table . '.' . $column2 . ' as block_ulb_name','' . $Table . '.' . $column3 . ' as gp_ward_name','' . $Table . '.' . $column4 . ' as name_response','' . $Tableupdate . '.' . $column5 . ' as id'
                    // ]);
                    

                   
                } else {
                    if (preg_match('/^[0-9]*$/', $serachvalue)) {
                        //dd(33);
                        
                        // $query = $query->where(function ($query1) use ($serachvalue, $Table) {
                        //     if (strlen($serachvalue) < 10) {
                        //         $query1->where($Table . '.ben_id', $serachvalue);
                                
                        //     } else if (strlen($serachvalue) == 10) {
                        //         $query1->where($Table . '.mobile_no', $serachvalue);
                        //     } 
                           
                        //     // else if ($serachvalue) {
                        //     //     $query1->where($Table . '.application_id', $serachvalue);
                        //     // } 
                            
                        // });
                        // $totalRecords = $query->count($Table . '.ben_id');
                        // $data =  $query->orderBy($Table . '.ben_id')->orderBy($Table . '.gp_ward_name')->offset($offset)->limit($limit)->distinct($Tableupdate.'.original_application_id')->get([
                        //     '' . $Table . '.' . $column . ' as beneficiary_id', 
                        //     'ben_name','' . $Tableupdate . '.' . $column1 . ' as updated_at','' . $Table . '.' . $column2 . ' as block_ulb_name','' . $Table . '.' . $column3 . ' as gp_ward_name','' . $Table . '.' . $column4 . ' as name_response','' . $Tableupdate . '.' . $column5 . ' as id'
                        // ]);

                        $data1 = DB::connection('pgsql_mis')->table('pension.failed_payment_details')
                        ->select(
                        'pension.failed_payment_details.ben_id as beneficiary_id',
                        
                        DB::raw('MAX(public.update_ben_details.created_at) as updated_at')
                        )
                        ->leftJoin('public.update_ben_details', 'public.update_ben_details.original_application_id', '=', 'pension.failed_payment_details.ben_id')
                        ->where($condition)
                        ->groupBy(
                        'pension.failed_payment_details.ben_id'
                        
                        )->get();


                $data = DB::connection('pgsql_mis')->table('pension.failed_payment_details')
                ->select(
                'pension.failed_payment_details.ben_id as beneficiary_id',
                'pension.failed_payment_details.gp_ward_name',

                'public.update_ben_details.original_application_id',
                'pension.failed_payment_details.block_ulb_name',
                'pension.failed_payment_details.av_name_response as name_response',
                'pension.failed_payment_details.ben_name as ben_name',
                DB::raw('MAX(public.update_ben_details.created_at) as updated_at')
                )
                ->leftJoin('public.update_ben_details', 'public.update_ben_details.original_application_id', '=', 'pension.failed_payment_details.ben_id')
                ->where($condition)->where($Table . '.ben_id', $serachvalue)->orWhere($Table . '.mobile_no', $serachvalue)
                ->groupBy(
                'pension.failed_payment_details.ben_id',
                'pension.failed_payment_details.gp_ward_name',
                'public.update_ben_details.original_application_id',
                'pension.failed_payment_details.block_ulb_name',
                'pension.failed_payment_details.av_name_response',
                'pension.failed_payment_details.ben_name'
                )
                ->orderBy('pension.failed_payment_details.gp_ward_name', 'asc')
                ->orderBy('pension.failed_payment_details.ben_name', 'asc')
                ->limit($limit)
                ->offset($offset)
                ->get();
                $totalRecords = count($data1);

                       
                } else {
                       // dd("'$serachvalue'");
                        //dd('pension.failed_payment_details like'."'.$serachvalue.%'");
                       //dd(66);
                        // $query = $query->where(function ($query1) use ($serachvalue) {
                        //     $query1->where('ben_name', 'like', $serachvalue . '%');
                        // });
                        // $totalRecords = $query->count($Table . '.ben_id');

                        
                        // $data =  $query->orderBy($Table . '.ben_id')->orderBy($Table . '.gp_ward_name')->offset($offset)->limit($limit)->distinct($Tableupdate.'.original_application_id')->get([
                        //     '' . $Table . '.' . $column . ' as beneficiary_id', 
                        //     'ben_name','' . $Tableupdate . '.' . $column1 . ' as updated_at','' . $Table . '.' . $column2 . ' as block_ulb_name','' . $Table . '.' . $column3 . ' as gp_ward_name','' . $Table . '.' . $column4 . ' as name_response','' . $Tableupdate . '.' . $column5 . ' as id'
                        // ]);

                        //dd($Table.'.ben_name.', 'like', ".'$serachvalue.'" . '%');
                        $data1 = DB::connection('pgsql_mis')->table('pension.failed_payment_details')
                        ->select(
                        'pension.failed_payment_details.ben_id as beneficiary_id',

                        DB::raw('MAX(public.update_ben_details.created_at) as updated_at')
                        )
                        ->leftJoin('public.update_ben_details', 'public.update_ben_details.original_application_id', '=', 'pension.failed_payment_details.ben_id')
                        ->where($condition)
                        ->groupBy(
                        'pension.failed_payment_details.ben_id'

                        )->get();

                        $data = DB::connection('pgsql_mis')->table('pension.failed_payment_details')
                        ->select(
                        'pension.failed_payment_details.ben_id as beneficiary_id',
                        'pension.failed_payment_details.gp_ward_name',
            
                        'public.update_ben_details.original_application_id',
                        'pension.failed_payment_details.block_ulb_name',
                        'pension.failed_payment_details.av_name_response as name_response',
                        'pension.failed_payment_details.ben_name as ben_name',
                        DB::raw('MAX(public.update_ben_details.created_at) as updated_at')
                        )
                        ->leftJoin('public.update_ben_details', 'public.update_ben_details.original_application_id', '=', 'pension.failed_payment_details.ben_id')
                        // ->where($condition)->where($Table .'.ben_name', 'like', "'".$serachvalue . "%'")
                        ->where($condition)->where($Table .'.ben_name', 'like', '%'.$serachvalue . '%')
                                                                                
                        ->groupBy(
                        'pension.failed_payment_details.ben_id',
                        'pension.failed_payment_details.gp_ward_name',
                        'public.update_ben_details.original_application_id',
                        'pension.failed_payment_details.block_ulb_name',
                        'pension.failed_payment_details.av_name_response',
                        'pension.failed_payment_details.ben_name'
                        )
                        ->orderBy('pension.failed_payment_details.gp_ward_name', 'asc')
                        ->orderBy('pension.failed_payment_details.ben_name', 'asc')
                        ->limit($limit)
                        ->offset($offset)
                        ->get();
                        // ->toSql();

                    //   dd($data);
                        $totalRecords = count($data1);

                    }
                    $filterRecords = count($data);

                   // dd($totalRecord);
                }

            return datatables()
            ->of($data)
            ->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
            ->addColumn('beneficiary_id', function ($data) {

            return $data->beneficiary_id;

            })->addColumn('name', function ($data) {
            return ($data->ben_name);

            })
            ->addColumn('updated_at', function ($data) {
            return  date('d-m-Y ', strtotime($data->updated_at));;

            })->addColumn('name_recived', function ($data) {
            return ($data->name_response);

            })
            ->addColumn('block_ulb_name', function ($data) {
            return ($data->block_ulb_name );

            })
            ->addColumn('gp_ward_name', function ($data) {
            return ($data->gp_ward_name );

            })
            // ->addColumn('is_urban', function ($data) use ($is_urban) {
            //     return ($is_urban );

            // })
            //return view('MisReportMinormismatch.index')->with('is_urban', $is_urban);



            ->make(true);
            } else {

                $errormsg = Config::get('constants.errormsg');
                $scheme_name_arr = Scheme::select('scheme_name')->where('id', $request->get('pr1'))->first();
                return view('MisReportMinormismatch.index')
                    ->with('report_type_name', $report_type_name)->with('type', $request->get('type'))
                     ->with('pr1', $request->get('pr1'))
                    ->with('scheme_name', $request->session()->get('scheme_name'))
                    ->with('scheme', $request->session()->get('scheme_id'))
                    // ->with('schemetype','$schemetype')
                    ->with('report_type_name', $report_type_name)
                    ->with('type', $request->get('type'))
                    ->with('type_new', $type_new)
                    // ->with('is_urban_new', $is_urban)
                    ->with('is_rural_visible', $is_rural_visible)
                    ->with('is_urban', $is_urban)
                    ->with('district_code',$district_code)
                    ->with('urban_visible', $urban_visible)
                    ->with('urban_body_code', $urban_body_code)
                    ->with('urban_visible', $urban_visible)
                    ->with('munc_visible', $munc_visible)
                    ->with('district_visible', $district_visible)
                    ->with('gp_ward_visible', $gp_ward_visible)
                    ->with('muncList', $muncList)
                    ->with('gpwardList', $gpwardList)
                    ->with('mappingLevel', $mappingLevel)
                    ->with('sessiontimeoutmessage', $errormsg['sessiontimeOut'])
                    ->with('scheme_name',  $scheme_name_arr->scheme_name)
                   
                    ->with('download_excel',  $download_excel);
            }
            } else {
            return redirect('/')->with('error', 'User not Authorized for this scheme');
            }
        }

    


    public function generate_excel(Request $request)
    {
        //dd(123);
            $district_code=$request->district_code;
            // $faulty_value=$request->faulty_type;
            $scheme_id=$request->scheme_id;
            $gp_ward_code = $request->gp_ward_code;
           

            $mappingLevel = $request->session()->get('level');
            
            $is_active = 0;
            $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
            $is_active = 1;
            $mapping_level = $roleObj['mapping_level'];
            $district_code = $roleObj['district_code'];
            if ($roleObj['is_urban'] == 1) {
            $urban_body_code = $roleObj['urban_body_code'];
            } else {
            $urban_body_code = $roleObj['taluka_code'];
            }
            break;
            }
            }

        // $modelName = new DataSourceCommon;
        // $modelName_payment= new DataSourceCommon;
      
        $report_type = $request->get('type');

        $condition = array();
        $role_name = Auth::user()->designation_id;
        $scheme_name_row = Scheme::where('id', $scheme_id)->first();
        $scheme_name = $scheme_name_row->scheme_name;



        if ($role_name == 'Approver') {

            $type_new='A';
            $is_urban = $request->rural_urbanid;
            $district_code = $request->session()->get('distCode');
            $urban_body_code = $request->urban_body_code;
            $block_ulb_code = $request->block_ulb_code;
            
        } else if ($role_name == 'Verifier' || $role_name == 'Operator' ) {
           $type_new='V';
           
            $district_code = $request->session()->get('distCode');
            if ($mappingLevel == 'Block') {
                $type_new='B';
                $block_ulb_code = NULL;
                
                $is_urban = 2;
                $munc_visible = 0;
                $urban_body_code = $request->session()->get('bodyCode');
                $block_ulb_code = NULL;
                $gpwardList = GP::where('block_code', $urban_body_code)->get();
                
            } else if ($mappingLevel == 'Subdiv') {
              $type_new='S';
                $block_ulb_code = $request->block_ulb_code;
                $urban_body_code = $request->session()->get('bodyCode');
                
                $muncList = UrbanBody::where('sub_district_code', $urban_body_code)->get();
                $block_ulb_code = $request->block_ulb_code;
            }
            
        }

        else{
          //  dd(123);
            $type_new='H';
           
            $is_urban = $request->rural_urbanid;
           
            $district_code = $request->district_code;

            $urban_body_code =$request->urban_body_code;
            
            $block_ulb_code = $request->block_ulb_code;
 

        }


 
            $is_draft = NULL;
            $report_type_name = 'Minor Mismatch Report (Bank) Beneficiary List';
           


           
                $Table = 'pension.failed_payment_details';

                $Tableupdate = 'public.update_ben_details';
            


            //  $Table_payment='lb_main.failed_payment_details';


            
            
                

           
           

            $condition[$Table . ".next_level_role_id_validation"] = 0;
            $condition[$Table . ".failed_process_type"] = 1;
            $condition[$Tableupdate . ".update_code"] = 20;
            $condition[$Table . ".acc_validated"] = -2;

            // $condition[$Table . ".next_level_role_id"] = 0;
            // $condition[$Table . ".failed_process_type"] = 1;
            // $condition[$Tableupdate . ".update_code"] = 22;
            // //$condition[$Table . ".acc_validated"] = -2;


            

            
            //$Table = $getModelFunc->getTable($district_code, $this->source_type, 1, $is_draft);
            // $modelName->setConnection('pgsql_appread');
            // $modelName->setTable('' . $Table);
            // $modelName_payment->setConnection('pgsql_appread');
            // $modelName_payment->setTable('' . $Table_payment);
            $column = 'ben_id';
            $column1 = 'created_at';
            $column2 = 'block_ulb_name';
            $column3 = 'gp_ward_name';
            $column4 = 'av_name_response';
            $column5= 'original_application_id';


            if (!empty($district_code)) {
                $condition[$Table . ".created_by_dist_code"] = $district_code;
            }
            else{
                $condition[$Table . ".created_by_dist_code"] = NULL;

            }
            //dd($is_urban);
            if (!empty($is_urban)) {

              
                if ($is_urban == 2) {
                    $condition[$Table . ".rural_urban_id"] = 2;
                    if (!empty($urban_body_code)) {

                        //dd(123);
                        //$condition["rural_urban_id"] = 2;
                        $condition[$Table . ".created_by_local_body_code"] = $urban_body_code;
                        $condition[$Table . ".rural_urban_id"] = 2;
                    }
                }
                //'Urban'
                if ($is_urban == 1) {
                    $condition[$Table . ".rural_urban_id"] = 1;
                    if (!empty($urban_body_code)) {
                        //$condition["rural_urban_id"] = 1;
                        $condition[$Table . ".created_by_local_body_code"] = $urban_body_code;
                        $condition[$Table . ".rural_urban_id"] = 1;
                    }
                    if (!empty($block_ulb_code)) {
                        $condition[$Table . ".block_ulb_code"] = $block_ulb_code;
                        $condition[$Table . ".rural_urban_id"] = 1;
                    }
                }
            }
            
            if (!empty($gp_ward_code)) {
                $condition[$Table . ".gp_ward_code"] = $gp_ward_code;
                $condition[$Table . ".rural_urban_id"] = 2;
            }
            $condition[$Table . ".scheme_id"] = $scheme_id;
           
      
            $data = array();
            // $model_name = $request->session()->get('model_name');
            // $query = DB::connection('pgsql_mis')->table( $Table)->where($condition);
            // $query = $query->join($Tableupdate, $Tableupdate . '.original_application_id', '=', $Table . '.ben_id');
           




            $data = DB::connection('pgsql_mis')->table('pension.failed_payment_details')
                        ->select(
                        'pension.failed_payment_details.ben_id as beneficiary_id',
                        'pension.failed_payment_details.gp_ward_name',
            
                        'public.update_ben_details.original_application_id',
                        'pension.failed_payment_details.block_ulb_name',
                        'pension.failed_payment_details.av_name_response as name_response',
                        'pension.failed_payment_details.ben_name as ben_name',
                        DB::raw('MAX(public.update_ben_details.created_at) as updated_at')
                        )
                        ->leftJoin('public.update_ben_details', 'public.update_ben_details.original_application_id', '=', 'pension.failed_payment_details.ben_id')
                        ->where($condition)
                        ->groupBy(
                        'pension.failed_payment_details.ben_id',
                        'pension.failed_payment_details.gp_ward_name',
                        'public.update_ben_details.original_application_id',
                        'pension.failed_payment_details.block_ulb_name',
                        'pension.failed_payment_details.av_name_response',
                        'pension.failed_payment_details.ben_name'
                        )
                        ->orderBy('pension.failed_payment_details.gp_ward_name', 'asc')
                        ->orderBy('pension.failed_payment_details.ben_name', 'asc')
                        
                        ->get();
                        $totalRecords = count($data);
           
        
            // $totalRecords = $query->count($Table . '.ben_id');
                
            // $data =  $query->orderBy($Table . '.ben_id')->orderBy($Table . '.gp_ward_name')->distinct($Tableupdate.'.original_application_id')->get([
            //     '' . $Table . '.' . $column . ' as beneficiary_id', 
            //     'ben_name','' . $Tableupdate . '.' . $column1 . ' as updated_at','' . $Table . '.' . $column2 . ' as block_ulb_name','' . $Table . '.' . $column3 . ' as gp_ward_name','' . $Table . '.' . $column4 . ' as name_response','' . $Tableupdate . '.' . $column5 . ' as id'
            // ]);
                
            $filename = $scheme_name . "-" . $report_type_name . "-" . date('d/m/Y') . ".xls";

            //dd($filename);
            header("Content-Type: application/xls");
            header("Content-Disposition: attachment; filename=" . $filename);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo '<table border="1">';
            echo '<tr><th>Beneficiary ID</th><th>Beneficiary Name</th><th>Name Recived from Bank</th>';
            
            //dd($type_new);
            
            if($type_new=='A' || $type_new=='H'){
        
                echo '<th>Block/Municipality Name</th><th>GP/Ward Name</th>';
            }

            if($type_new=='B'){
        
                echo '<th>Block Name</th><th>GP Name</th>';
            }

            if($type_new=='S'){
        
                echo '<th>Municipality Name</th><th>Ward Name</th>';
            }
            echo '<th>Correction Date</th></tr>';
            if (count($data) > 0) {
                foreach ($data as $row) {
                   
                    
                    echo "<tr><td>" . $row->beneficiary_id . "</td><td>" . trim($row->ben_name ) . "</td><td>" . trim($row->name_response) . "</td><td>" . $row->block_ulb_name . "</td><td>" . $row->gp_ward_name . "</td><td>" . date('d-m-Y ', strtotime($row->updated_at)) . "</td></tr>";

                    //  echo "<tr><td>" . $row->beneficiary_id . "</td><td>" . $row->application_id . "</td><td>" . trim($row->ben_fname . ' ' . $row->ben_mname . ' ' . $row->ben_lname) . "</td></tr>";
                }
            } else {
                echo '<tr><td colspan="6">No Records found</td></tr>';
            }
            echo '</table>';
        
    }





    // function ageCalculate($dob)
    // {
    //     $diff = 0;
    //     if ($dob != '') {
    //         // $diff = $this->ageCalculate($dob);
    //         $diff = Carbon::parse($dob)->diffInYears($this->base_dob_chk_date);
    //     }
    //     return $diff;
    // }
}
