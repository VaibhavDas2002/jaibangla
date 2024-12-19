<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Http\Controllers\Redirect;
use App\designationMaster;
use App\Configduty;
use App\District;
use App\UrbanBody;
use App\SubDistrict;

use App\SchemecodeStatic;
use App\DocumentType;
use App\SchemeDocMap;
use App\Scheme;
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
use Auth;
use Config;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\AcceptRejectInfo;
use App\DsPhase;

class updatenoaadhar extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function schemeSelection(Request $request)
    {
       

       
        $user_id = AuthChecker::getUserId();
        $duty_schemes = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->get()->pluck('scheme_id')->toArray();
        $scheme_list_constants = Config::get('constants.scheme_code_map');
        $scheme_list = array();
        foreach ($scheme_list_constants as $key => $arr) {
            $list_arr = array();
            if (in_array($key, $duty_schemes)) {
                $list_arr['scheme_id'] = $arr['scheme_id'];
                $list_arr['model_name'] = $arr['model_name'];
                $list_arr['scheme_name'] = $arr['scheme_name'];
                array_push($scheme_list, $list_arr);
            }
        }
        $mod_list = array_values($scheme_list);
        $report_type='A';
        $report_type_name='Accept allow No Aadhar';
        // dd($mod_list);
        return view('update-noaadhar.scheme', ['scheme_list' => $mod_list, 'report_type_name' => $report_type_name]);
    }
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
            $roleArray = $request->session()->get('role');
            $designation_id_old = Auth::user()->designation_id_old;
            // dd($designation_id_old);
            if ($designation_id_old == 'HOD') {
                if ($scheme_id == 2 || $scheme_id == 1|| $scheme_id == 11 || $scheme_id == 13 || $scheme_id == 17 || $scheme_id == 18 || $scheme_id == 8 || $scheme_id == 9) {
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

    public function applicationStatusList(Request $request)
    {
        $phase_list = DsPhase::get();

        if (!$request->has('pr1')) {
            return redirect('/')->with('error', 'Signature Error: Scheme Type not selected');
        }

        if ($status = $this->schemeSessionCheck($request)) {
            if ($status == -1) {
                return redirect('/')->with('error', 'Error: Scheme Not Configured');
            }
            $role_name = $request->session()->get('role_name');

                if($role_name!='Operator')
                {
                return redirect('/')->with('error', 'Error: U*ser Not Allow');
                }
            $mappingLevel = $request->session()->get('level');
            $download_excel = 0;
            $district_visible = 0;
            $is_rural_visible = 0;
            $urban_visible = 0;
            $munc_visible = 0;
            $gp_ward_visible = 0;
            $districtList = collect([]);
            $muncList = collect([]);
            $gpwardList = collect([]);
            if ( $role_name == 'StatusCheckerDistrict') {
                $is_urban = $request->rural_urbanid;
                $district_code = $request->session()->get('distCode');
                $urban_body_code = $request->urban_body_code;
                $block_ulb_code = $request->block_ulb_code;
                $is_rural_visible = 1;
                $urban_visible = 1;
                $munc_visible = 1;
                $gp_ward_visible = 1;
                $download_excel = 1;
            } else if ( $role_name == 'Operator' ) {
                $district_code = $request->session()->get('distCode');
                if ($mappingLevel == 'Block') {
                    $block_ulb_code = NULL;
                    $is_rural_visible = 0;
                    $is_urban = 2;
                    $munc_visible = 0;
                    $urban_body_code = $request->session()->get('bodyCode');
                    $block_ulb_code = NULL;
                    $gpwardList = GP::where('block_code', $urban_body_code)->get();
                    $gp_ward_visible = 1;
                } else if ($mappingLevel == 'Subdiv') {
                    $block_ulb_code = $request->block_ulb_code;
                    $urban_body_code = $request->session()->get('bodyCode');
                    $is_rural_visible = 0;
                    $is_urban = 1;
                    $munc_visible = 1;
                    $gp_ward_visible = 1;
                    $muncList = UrbanBody::where('sub_district_code', $urban_body_code)->get();
                    $block_ulb_code = $request->block_ulb_code;
                }
                $download_excel = 1;
            } else {
                $district_visible = 1;
                $is_urban = NULL;
                $districtList = District::get();
                $district_code = $request->district_code;
                $urban_body_code = $request->urban_body_code;
                $block_ulb_code = $request->block_ulb_code;
                $is_rural_visible = 1;
                $munc_visible = 1;
                $gp_ward_visible = 1;
                $urban_visible = 1;
            }
            $condition = array();
            //$download_excel = 0;
            //$report_type N - Total List, V-Verified List, R-Recomender List, A-Approved List, T- Rejected List 
            $report_type='N';
            $report_type_name = 'Accept allow No Aadhar';

            
            
            if (request()->ajax()) {
                $scheme_id = $request->session()->get('scheme_id');
                if (!empty($district_code)) {
                    $condition["created_by_dist_code"] = $district_code;
                }
                if (!empty($is_urban)) {
                    // $condition[$contact_table . ".rural_urban_id"] = $is_urban;
                    if ($is_urban == 2) {
                        if (!empty($urban_body_code)) {
                            //$condition["rural_urban_id"] = 2;
                            $condition["created_by_local_body_code"] = $urban_body_code;
                        }
                    }
                    //'Urban'
                    if ($is_urban == 1) {
                        if (!empty($urban_body_code)) {
                            //$condition["rural_urban_id"] = 1;
                            $condition["created_by_local_body_code"] = $urban_body_code;
                            //$download_excel = 1;
                        }
                        if (!empty($block_ulb_code)) {
                            $condition["block_ulb_code"] = $block_ulb_code;
                            //$download_excel = 1;
                        }
                    }
                }
                if (!empty($request->gp_ward_code)) {
                    $condition["gp_ward_code"] = $request->gp_ward_code;
                    $download_excel = 1;
                }



                $serachvalue = $request->search['value'];
                $limit = $request->input('length');
                $offset = $request->input('start');

                $totalRecords = 0;
                $filterRecords = 0;
                $data = array();
                $scheme_length = NULL;
                $id_length = NULL;
                if (!empty($scheme_id)) {
                    $scheme_row = Scheme::where('id', $scheme_id)->first();
                    //dd($scheme_row->toArray());
                    if (!empty($scheme_row)) {
                        $scheme_schema = $scheme_row->short_code;
                        if (!empty($scheme_schema)) {
                            $table = $scheme_schema;
                            //dd($table);
                            $query = DB::connection('pgsql_mis')->table('' . $table . '.beneficiary')->where($condition);
                         
                        } else {
                            $model_name = $request->session()->get('model_name');
                            $query = $model_name::where($condition);
                        }
                        $scheme_length =  $scheme_row->scheme_length;
                        $id_length = $scheme_row->id_length;
                    } else {
                        $model_name = $request->session()->get('model_name');
                        $query = $model_name::where($condition);
                        $scheme_length = NULL;
                        $id_length = NULL;
                    }
                } else {
                    $model_name = $request->session()->get('model_name');
                    $query = $model_name::where($condition);
                    $scheme_length = NULL;
                    $id_length = NULL;
                }

                
                    $query = $query->whereNull('next_level_role_id')->whereRaw('LENGTH(aadhar_no) = 12')->where('no_aadhar',1)->where('wbpds_is_sent',1);
                   
               
                
                
                if (empty($serachvalue)) {
                    $totalRecords = $query->count();
                    $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
                } else {
                    if (is_numeric($serachvalue)) {
                        $ben_id = $serachvalue;
                        $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                            $query1->where('id', $ben_id)
                                ->orWhere('bank_code', $serachvalue);
                        });
                        $totalRecords = $query->count('id');
                        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
                    } else {
                        $query = $query->where(function ($query1) use ($serachvalue) {
                           
                                $query1->where('ben_fname', 'like', $serachvalue . '%')
                                    ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                                    ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                                    ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                            
                        });
                       
                        $totalRecords = $query->count('id');
                        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
                    }
                    $filterRecords = count($data);
                }

                return datatables()
                    ->of($data)
                    ->setTotalRecords($totalRecords)
                    ->setFilteredRecords($filterRecords)
                    ->skipPaging()
                    ->addColumn('ben_id', function ($data) use ($scheme_length, $id_length) {

                        
                        // $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

                        $app_id =  $data->id;

                        return $app_id;
                    })

                    
                    ->addColumn('ben_name', function ($data) {
                        // return $data->getName();
                        return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                    })
                    ->addColumn('aadhar_no', function ($data) {
                        return '********' . substr($data->aadhar_no, 8, 4);
                    })
                    ->addColumn('aadhar_no_rw', function ($data) {
                        return $data->aadhar_no;
                    })
                  
                    ->addColumn('ben_age', function ($data) {
                        return date('d-m-Y', strtotime($data->dob));
                    })
                    
                    ->addColumn('mobile_no', function ($data) {
                        return trim($data->mobile_no);
                    })
                    

                    
                
                   
                    

                    ->addColumn('action', function ($data) use ($scheme_id, $report_type, $role_name) {
                        

                        $val =  '<button class="btn btn-primary ben_accept_button">Accept as valid aadhaar</button>';

                        return $val;
                    })
                    ->rawColumns(['ben_id', 'ben_name', 'aadhar_no','ben_age',  'mobile_no','action','aadhar_no_rw'])
                    ->make(true);
            } else {
                //Goto view
                return view('update-noaadhar.index')->with('phase_list', $phase_list)->with('district_name', 'Test')->with('district_code', $request->session()->get('distCode'))->with('scheme', $request->session()->get('scheme_id'))
                    // ->with('schemetype','$schemetype')
                    ->with('report_type_name', $report_type_name)
                    ->with('pr1', $request->get('pr1'))
                    ->with('scheme_name', $request->session()->get('scheme_name'))
                    ->with('district_visible', $district_visible)
                    ->with('districtList', $districtList)
                    ->with('is_rural_visible', $is_rural_visible)
                    ->with('is_urban', $is_urban)
                    ->with('urban_visible', $urban_visible)
                    ->with('urban_body_code', $urban_body_code)
                    ->with('urban_visible', $urban_visible)
                    ->with('munc_visible', $munc_visible)
                    ->with('gp_ward_visible', $gp_ward_visible)
                    ->with('muncList', $muncList)
                    ->with('gpwardList', $gpwardList)
                    ->with('mappingLevel', $mappingLevel)
                    ->with('download_excel',  $download_excel);
            }
        } else {
            return redirect('/')->with('error', 'User not Authorized for this scheme');
        }
    }

   
    public function acceptnoaadharApplication(Request $request)
    {
     
        $user_id = AuthChecker::getUserId();
        // $role_id = $request->session()->get('role_id');
        $scheme_id = $request->session()->get('scheme_id');
        $ben_id = $request->accept_beneficiary_id;
        $application_id=$request->accept_ben_id;

        $duty_obj = Configduty::where('user_id', $user_id)->first();
        $district_code = $duty_obj->district_code;
        $designation_id_old = Auth::user()->designation_id_old;
       

        
        if ($designation_id_old == 'Operator') {
            if ($duty_obj->mapping_level == "Subdiv") {
              $created_by_local_body_code = $duty_obj->urban_body_code;
            }
            if ($duty_obj->mapping_level == "Block") {
              $created_by_local_body_code = $duty_obj->taluka_code;
            }
           
          }


        
        // $c_time = date('Y-m-d H:i:s', time());
        // $accept_reject_model = new AcceptRejectInfo;
        // $accept_reject_model->created_at = $c_time;
        // $accept_reject_model->application_id = $ben_id;
        // $accept_reject_model->scheme_id = $scheme_id;
        // $accept_reject_model->user_id = $user_id;
        // $accept_reject_model->ip_address = request()->ip();
        // $accept_reject_model->op_type = 'ACCEPTNOAADHAR';
        // $model_name = $request->session()->get('model_name');

        $scheme_row = Scheme::where('id', $scheme_id)->first();
        $scheme_schema = $scheme_row->short_code;
        $table = $scheme_schema;
        $c_time = date('Y-m-d H:i:s', time());
        $accept_reject = 'ACCEPTNOAADHAR';

       
                    //dd($scheme_row->toArray());
                    
                           
        //$reject_reason = $request->reject_reason;
        // $revert_reason = 'Accept No Aadhar by user: ' . $user_id;

        // $return_msg='AAA';

        $back_url = 'application-noaadhar?pr1='.$scheme_id;

        $inputUpdate = [
            
            'no_aadhar' => null
        ];

        $accept_reject_info = [
            'application_id' => $ben_id,
            'created_by_dist_code' => $district_code,
            'created_by_local_body_code' =>$created_by_local_body_code ,
            'user_id' => $user_id,
            'scheme_id' => $scheme_id,
            'created_at' => $c_time,
            'updated_at'=>$c_time,
            'op_type'=>$accept_reject,
            'ip_address' => $request->ip()
        ];
        DB::beginTransaction();
        try {

           
            
               $main_update = DB::table($table . '.beneficiary')->where('id', $ben_id)->whereNull('next_level_role_id')->where('no_aadhar',1)->update($inputUpdate);

               $is_accept_reject_log = DB::table('public.ben_accept_reject_info')
          ->insert($accept_reject_info );

               if($main_update && $is_accept_reject_log){

                //dd(123);

                // $errors = array();
                // $errorMsg = "Aadhaar Number Already Exist! Please try different.";

                // array_push($errors, $errorMsg);

                $return_text = 'Beneficiary with  Id:'.$ben_id.' Aadhaar Information Accepted Successfully ';

                DB::commit();
                return redirect($back_url)->with('success', $return_text);
                
               }

               else {
                DB::rollback();
                
                $return_text = 'Error. Please try again...';
                return redirect($back_url)->with('error', $return_text);
              }
            
        } catch (\Exception $e) {

           // dd($e);
            
            DB::rollback();
        }
        
    }

  

    public function generate_excel(Request $request)
    {
        $scheme_code =  $request->rej_scheme_code;
        $user_id = AuthChecker::getUserId();
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $dist_code = $duty->district_code;

        $district_name = District::where('district_code', $dist_code)->pluck('district_name')->first();
        $scheme_name_row = Scheme::where('id', $scheme_code)->select('scheme_name', 'short_code')->first();
        $scheme_name = $scheme_name_row->scheme_name;
        $scheme_schema_name = $scheme_name_row->short_code;

        $title = $district_name . "_" . $scheme_name . "_Rejected Duplicates";

        $data = array();

        $query = "select block_ulb_name || case when rural_urban_id=1 then ' Municipality' else '' end  \"Block_Municipality\"
        ,id as \"Beneficiary_Id\"
        , ben_fname ||' '|| coalesce(ben_mname||' ','') || coalesce(ben_lname,'') as \"Name\"
        , b.bank_code  as \"Account_No\"
        , b.bank_ifsc as \"IFSC\"
        from " . $scheme_schema_name . ".beneficiary b where next_level_role_id = -3 and created_by_dist_code=" . $dist_code;

        $data_part = DB::connection('pgsql')->select($query);
        $data = array_merge($data, $data_part);

        $excel_data[] = array('Block/Municipality', 'Beneficiary_Id', 'Name', 'Account_No', 'IFSC');
        foreach ($data as $row) {
            $excel_data[] = array(
                'Block/Municipality'  => $row->Block_Municipality,
                'Beneficiary_Id'  => $row->Beneficiary_Id,
                'Name'  => $row->Name,
                'Account_No'  => $row->Account_No,
                'IFSC'  => $row->IFSC
            );
        }

        Excel::create('' . $title, function ($excel) use ($excel_data, $title, $scheme_name) {
            $excel->setTitle('' . $title);
            $excel->sheet('' . $scheme_name, function ($sheet) use ($excel_data) {
                $sheet->fromArray($excel_data, null, 'A1', false, false);
            });
        })->download('xlsx');
    }
}
