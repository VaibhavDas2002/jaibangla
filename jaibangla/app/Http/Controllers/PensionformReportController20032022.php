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

class PensionformReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function schemeSelection(Request $request)
    {
        $report_type = '';

        if ($request->has('type')) {
            $report_type = $request->get('type');
            if ($report_type == 'F') {
                $report_type_name = 'Fresh Beneficiary List Report';
            } else if ($report_type == 'V') {
                $report_type_name = 'Verified Beneficiary List Report';
            } else if ($report_type == 'A') {
                $report_type_name = 'Approved Beneficiary List Report';
            } else if ($report_type == 'R') {
                $report_type_name = 'Recomended Beneficiary List Report';
            } else if ($report_type == 'T') {
                $report_type_name = 'Rejected Beneficiary List Report';
            } else if ($report_type == 'C') {
                $report_type_name = 'Complete Beneficiary List Report';
            } else {
                return redirect('/')->with('error', 'Error: Report type invalid');
            }
        } else {
            return redirect('/')->with('error', 'Signature Error: Report Type not selected');
        }
        $user_id = Auth::user()->id;
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
        // dd($mod_list);
        return view('pensionreport.scheme', ['scheme_list' => $mod_list, 'type' => $report_type, 'report_type_name' => $report_type_name]);
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
            $designation_id = Auth::user()->designation_id;
            // dd($designation_id);
            if ($designation_id == 'HOD') {
                if ($scheme_id == 2 || $scheme_id == 11 || $scheme_id == 13 || $scheme_id == 17 || $scheme_id == 18) {
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

        if (!$request->has('pr1')) {
            return redirect('/')->with('error', 'Signature Error: Scheme Type not selected');
        }

        if ($status = $this->schemeSessionCheck($request)) {
            if ($status == -1) {
                return redirect('/')->with('error', 'Error: Scheme Not Configured');
            }
            $role_name = $request->session()->get('role_name');
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
            if ($role_name == 'Approver' || $role_name == 'StatusCheckerDistrict') {
                $is_urban = $request->rural_urbanid;
                $district_code = $request->session()->get('distCode');
                $urban_body_code = $request->urban_body_code;
                $block_ulb_code = $request->block_ulb_code;
                $is_rural_visible = 1;
                $urban_visible = 1;
                $munc_visible = 1;
                $gp_ward_visible = 1;
                $download_excel = 1;
            } else if ($role_name == 'Verifier' || $role_name == 'Operator' || $role_name == 'StatusCheckerField') {
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
            $report_type = 'N';

            $report_type_name = 'Beneficiary List';
            if ($request->has('type')) {
                $report_type = $request->get('type');
                if ($report_type == 'F') {
                    $report_type_name = 'Fresh Beneficiary List';
                    // $condition['next_level_role_id']='is not null';
                } else if ($report_type == 'V') {
                    $report_type_name = 'Verified Beneficiary List';
                    // $condition['next_level_role_id']='is not null';
                } else if ($report_type == 'A') {
                    $report_type_name = 'Approved Beneficiary List';
                    $condition['next_level_role_id'] = 0;
                } else if ($report_type == 'R') {
                    $report_type_name = 'Recomended Beneficiary List';
                    //Only For Purohit Scheme
                    $condition['next_level_role_id'] = 106;
                } else if ($report_type == 'T') {
                    $report_type_name = 'Rejected Beneficiary List';
                    //      $condition['next_level_role_id'] = '-1';
                } else if ($report_type == 'C') {
                    $report_type_name = 'Complete Beneficiary List';
                } else {
                    return redirect('/')->with('error', 'Error: Report type invalid');
                }
            } else {
                return redirect('/')->with('error', 'Signature Error: Report Type not selected');
            }
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
                            $query = DB::connection('pgsql_mis')->table('' . $table . '.beneficiary')->where($condition);
                            // $query = DB::table::on('pgsql_mis')->where($condition);
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


                //Report Type Filter
                if ($report_type == 'F') { // Fresh List
                    $query = $query->whereNull('next_level_role_id');
                }
                if ($report_type == 'T') {
                    $query = $query->where('next_level_role_id', '<', 0);
                }
                if ($report_type == 'V') { //Verified List
                    if ($scheme_id == 17) { //For Purohit
                        $query = $query->where('next_level_role_id', 107);
                    } else {
                        $query = $query->where('next_level_role_id', '>', 0)->where('next_level_role_id', '!=', 9999);
                    }
                }
                if (empty($serachvalue)) {
                    $totalRecords = $query->count();
                    $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                        'id', 'created_by_dist_code',
                        'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                        'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'is_state'
                    ]);
                } else {
                    if (is_numeric($serachvalue)) {
                        $ben_id = substr($serachvalue, -7);
                        $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                            $query1->where('id', $ben_id)
                                ->orWhere('bank_code', $serachvalue);
                        });
                        $totalRecords = $query->count('id');
                        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                            [
                                'id', 'created_by_dist_code',
                                'bank_code',
                                'ben_fname',
                                'block_ulb_name',
                                'gp_ward_name',
                                'bank_ifsc',
                                'village_town_city',
                                'scheme_id',
                                'lot_generated',
                                'payment_count',
                                'next_level_role_id',
                                'ben_lname', 'gender', 'ben_age', 'ben_mname', 'is_state'
                            ]
                        );
                    } else {
                        $query = $query->where(function ($query1) use ($serachvalue) {
                            if (strtoupper(trim($serachvalue)) == 'STATE') {
                                $query1->where('is_state', TRUE);
                            } else {
                                $query1->where('ben_fname', 'like', $serachvalue . '%')
                                    ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                                    ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
                                    ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                            }
                        });
                        $totalRecords = $query->count('id');
                        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                            [
                                'id', 'created_by_dist_code',
                                'bank_code',
                                'ben_fname',
                                'block_ulb_name',
                                'gp_ward_name',
                                'bank_ifsc',
                                'village_town_city',
                                'scheme_id',
                                'lot_generated',
                                'payment_count',
                                'next_level_role_id',
                                'ben_lname', 'gender', 'ben_age', 'ben_mname', 'is_state'
                            ]
                        );
                    }
                    $filterRecords = count($data);
                }

                return datatables()
                    ->of($data)
                    ->setTotalRecords($totalRecords)
                    ->setFilteredRecords($filterRecords)
                    ->skipPaging()
                    ->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

                        $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

                        return $app_id;
                    })
                    ->addColumn('ben_name', function ($data) {
                        // return $data->getName();
                        return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                    })
                    ->addColumn('benf_name', function ($data) {
                        return "Father Name";
                    })
                    ->addColumn('ben_age', function ($data) {
                        return $data->ben_age;
                    })
                    ->addColumn('gender', function ($data) {
                        return $data->gender;
                    })
                    ->addColumn('bank_ifsc', function ($data) {
                        return $data->bank_ifsc;
                    })
                    ->addColumn('bank_code', function ($data) {
                        return $data->bank_code;
                    })
                    ->addColumn('village_town_city', function ($data) {
                        return trim($data->village_town_city);
                    })->addColumn('is_state_des', function ($data) {
                        if (($data->is_state == TRUE)) {
                            $val = '<span class="badge badge-danger">State Entry</span>';
                        } else {
                            $val = '';
                        }
                        return $val;
                    })
                    ->addColumn('action', function ($data) use ($scheme_id, $report_type, $role_name) {
                        $val = '<a href="application-details-read_only/' . $data->id . '?scheme_id=' . $scheme_id . '" class="btn btn-primary ben_view_button" role="button" target="_blank">View</a>';
                        if (($report_type == 'A') && ($data->lot_generated == 1) && ($data->payment_count > 0)) {
                            $val = $val . '<span class="badge badge-danger">Payment has been initiated</span>';
                        }
                        if ($report_type == 'C') {
                            if (!isset($data->next_level_role_id)) {
                                $val = $val . '<span class="badge badge-primary">Fresh</span>';
                            } else if ($data->next_level_role_id == 0) {
                                $val = $val . '<span class="badge badge-success">Approved</span>';
                            } else if ($data->next_level_role_id == -1) {
                                $val = $val . '<span class="badge badge-danger">Rejected</span>';
                            } else if ($data->next_level_role_id == 106 && $scheme_id == 17) {
                                $val = $val . '<span class="badge badge-info">Reverted</span>';
                            } else if ($data->next_level_role_id > 0) {
                                $val = $val . '<span class="badge badge-dark">Verified</span>';
                            }
                        }
                        if ($role_name == 'Approver' || $role_name == 'HOD') {
                            if (($report_type == 'A' || $report_type == 'R') && ($data->lot_generated == 0) && ($data->payment_count == 0)) {
                                $val = $val . '<button class="btn btn-warning ben_reject_button">Reject</button>';
                                $val = $val . '<button class="btn btn-success ben_revert_button">Revert</button>';
                            } else if ($report_type == 'T') {
                                $val = $val . '<button class="btn btn-success ben_revert_button">Revert</button>';
                            }
                        }
                        return $val;
                    })
                    ->rawColumns(['ben_id', 'ben_name', 'ben_age', 'gender', 'bank_ifsc', 'bank_code', 'village_town_city', 'action', 'is_state_des'])
                    ->make(true);
            } else {
                //Goto view
                return view('pensionreport.index')->with('district_name', 'Test')->with('district_code', $request->session()->get('distCode'))->with('scheme', $request->session()->get('scheme_id'))
                    // ->with('schemetype','$schemetype')
                    ->with('report_type_name', $report_type_name)->with('type', $request->get('type'))
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

    public function rejectApplication(Request $request)
    {
        $ben_id = $request->ben_id;

        $model_name = $request->session()->get('model_name');

        $role_id = $request->session()->get('role_id');
        $scheme_id = $request->session()->get('scheme_id');

        $user_id = Auth::user()->id;
        //$reject_reason = $request->reject_reason;
        $reject_reason = 'Rejected by user: ' . $user_id;
        DB::beginTransaction();
        try {

            $input_update = ['next_level_role_id' => -1, 'comments' => $reject_reason];
            $model_name::where('id', $ben_id)->where('lot_generated', 0)->where('payment_count', 0)->update($input_update);
        } catch (\Exception $e) {
            DB::rollback();
        }
        DB::commit();
    }

    public function revertApplication(Request $request)
    {
        $ben_id = $request->ben_id;

        $model_name = $request->session()->get('model_name');

        $role_id = $request->session()->get('role_id');
        $scheme_id = $request->session()->get('scheme_id');

        $user_id = Auth::user()->id;
        //$reject_reason = $request->reject_reason;
        $revert_reason = 'Reverted by user: ' . $user_id;
        DB::beginTransaction();
        try {

            $input_update = ['next_level_role_id' => null, 'comments' => $revert_reason];
            $model_name::where('id', $ben_id)->where('lot_generated', 0)->where('payment_count', 0)->update($input_update);
        } catch (\Exception $e) {
            DB::rollback();
        }
        DB::commit();
    }

    public function reject_duplicates(Request $request)
    {
        $scheme_code = $request->input('scheme_code');
        $user_id = Auth::user()->id;
        $duty = Configduty::where('user_id', '=', $user_id)->first();
        $dist_code = $duty->district_code;

        $scheme_prefix = "";
        if ($scheme_code == 10)
            $scheme_prefix = "oap_wcd";
        else if ($scheme_code == 11)
            $scheme_prefix = "wp_wcd";
        $query = "update " . $scheme_prefix . ".beneficiary set next_level_role_id=-3, av_status=-3 where id = ANY(
            select id from " . $scheme_prefix . ".mv_beneficiary_duplicate where rk>1 and lot_generated=0 and payment_count=0 
            and created_by_dist_code=" . $dist_code . ") and lot_generated=0 and payment_count=0";

        DB::connection('pgsql')->select($query);

        return "true";
    }

    public function generate_excel(Request $request)
    {
        $scheme_code =  $request->rej_scheme_code;
        $user_id = Auth::user()->id;
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
