<?php

namespace App\Http\Controllers;

use App\Http\Controllers\WPWCDformController;
use Illuminate\Http\Request;
//use App\Http\Controllers\Redirect;
use App\programmeHeadMaster;
use App\majorProgammeHeadMaster;
use App\nhm_employee_details;
use App\designationMaster;
use App\nhm_service_category;
use App\NHMEmployee;
use App\Configduty;
use App\District;
use App\nhm_posting_level;
use App\nhm_level_place;
use App\nhm_health_facility;
use App\UrbanBody;
use App\SubDistrict;
use App\PensionManabikWCD;
use App\PensionOAPWCD;
use App\PensionWPWCD;
//Dynamic Doc
use App\BenDocsManabikWCD;
use App\BenDocsArcManabikWCD;
use App\DocumentType;
use App\SchemeDocMap;
//Dynamic Doc End
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\BankDetails;
use App\User;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use App\Scheme;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Helpers\AuthChecker;

class BriefReportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function schemeSelection(Request $request)
    {
        $report_type = '';
        $type = $request->type;
        //dd($type);
        if ($type) {
            $report_type = $type;
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
        $scheme_list = Config::get('constants.scheme_code_map');
        $mod_list = array_values($scheme_list);
        return view('BriefReport.scheme', ['scheme_list' => $mod_list, 'type' => $type, 'report_type_name' => $report_type_name]);
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
            $condition = array();

            //$report_type N - Total List, V-Verified List, R-Recomender List, A-Approved List, T- Rejected List 
            $report_type = 'N';

            $report_type_name = 'Beneficiary List';
            if ($request->has('type')) {
                $report_type = $request->get('type');
                if ($report_type == 'A') {
                    $report_type_name = 'Approved Brief Data Entry Beneficiary List';
                    $condition['next_level_role_id'] = 0;
                } else if ($report_type == 'T') {
                    $report_type_name = 'Rejected Brief Data Entry Beneficiary List';
                    $condition['next_level_role_id'] = '-1';
                } else {
                    return redirect('/')->with('error', 'Error: Report type invalid');
                }
            } else {
                return redirect('/')->with('error', 'Signature Error: Report Type not selected');
            }
            if (request()->ajax()) {
                $scheme_id = $request->session()->get('scheme_id');
                $user_id = AuthChecker::getUserId();
                //$duty = Configduty::where('user_id', '=', $user_id)->first();
                $role_name = $request->session()->get('role_name');
                $distCode = $request->session()->get('distCode');
                $bodyCode = $request->session()->get('bodyCode');
                $is_urban = $request->session()->get('is_urban');
                // District Filter
                if (!empty($distCode)) {
                    $condition["created_by_dist_code"] = $distCode;
                }
                // Local Body Filter
                if (!empty($is_urban)) {
                    //'Rural'
                    if ($is_urban == 2) {
                        if (!empty($bodyCode)) {
                            $condition["rural_urban_id"] = 2;
                            $condition["created_by_local_body_code"] = $bodyCode;
                        }
                    }
                    //'Urban'
                    if ($is_urban == 1) {
                        if (!empty($bodyCode)) {
                            $condition["rural_urban_id"] = 1;
                            $condition["created_by_local_body_code"] = $bodyCode;
                        }
                    }
                }

                //For Operator
                $created_by = $user_id;
                if ($role_name == 'Operator') {
                    $condition["created_by"] = $created_by;
                }


                $serachvalue = $request->search['value'];
                $limit = $request->input('length');
                $offset = $request->input('start');

                $totalRecords = 0;
                $filterRecords = 0;
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
                if ($report_type == 'V') { //Verified List
                    if ($scheme_id == 17) { //For Purohit
                        $query = $query->where('next_level_role_id', 107);
                    } else {
                        $query = $query->where('is_verified',1)->where('is_approved',0)->where('is_rejected',0);
                    }
                }
                if (empty($serachvalue)) {
                    $totalRecords = $query->count('id');
                    $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                        'id', 'created_by_dist_code',
                        'bank_code',
                        'ben_fname',
                        'ben_mname',
                        'ben_lname',
                        'block_ulb_name',
                        'bank_ifsc',
                        'village_town_city',
                        'scheme_id',
                        'lot_generated',
                        'payment_count',
                        'next_level_role_id','is_verified','is_approved','is_rejected'
                    ]);
                } else {
                    if (is_numeric($serachvalue)) {
                        $ben_id = substr($serachvalue, -7);
                        $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                            $query1->where('id', $ben_id)
                                ->orWhere('bank_code', $serachvalue);
                        });
                        $totalRecords = $query->count('id');
                        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                            'id', 'created_by_dist_code',
                            'bank_code',
                            'ben_fname',
                            'ben_mname',
                            'ben_lname',
                            'block_ulb_name',
                            'bank_ifsc',
                            'village_town_city',
                            'scheme_id',
                            'lot_generated',
                            'payment_count',
                            'next_level_role_id','is_verified','is_approved','is_rejected'
                        ]);
                    } else {
                        $query = $query->where(function ($query1) use ($serachvalue) {
                            $query1->where('ben_fname', 'like', $serachvalue . '%')
                                ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
                                ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
                        });
                        $totalRecords = $query->count('id');
                        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                            'id', 'created_by_dist_code',
                            'bank_code',
                            'ben_fname',
                            'ben_mname',
                            'ben_lname',
                            'block_ulb_name',
                            'bank_ifsc',
                            'village_town_city',
                            'scheme_id',
                            'lot_generated',
                            'payment_count',
                            'next_level_role_id','is_verified','is_approved','is_rejected'
                        ]);
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

                        return  $app_id;
                    })
                    ->addColumn('ben_name', function ($data) {
                        return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                    })
                    ->addColumn('benf_name', function ($data) {
                        return "Father Name";
                    })
                    ->addColumn('bank_ifsc', function ($data) {
                        return $data->bank_ifsc;
                    })
                    ->addColumn('bank_code', function ($data) {
                        return $data->bank_code;
                    })
                    ->addColumn('village_town_city', function ($data) {
                        return $data->village_town_city;
                    })
                    ->addColumn('action', function ($data) use ($scheme_id, $report_type, $role_name) {
                        $val = '<a href="application-details-read_only/' . $data->id . '?scheme_id=' . $scheme_id . '" class="btn btn-primary ben_view_button" role="button" target="_blank">View</a>';
                        if ($report_type == 'C') {
                            if (!isset($data->next_level_role_id)) {
                                $val = $val . '<span class="badge badge-primary">Fresh</span>';
                            } else if ($data->next_level_role_id == 0) {
                                $val = $val . '<span class="badge badge-success">Approved</span>';
                            } else if ($data->next_level_role_id == -1) {
                                $val = $val . '<span class="badge badge-danger">Rejected</span>';
                            } else if ($data->next_level_role_id == 106 && $scheme_id == 17) {
                                $val = $val . '<span class="badge badge-info">Reverted</span>';
                            } else if ($data->is_verified==1 && $data->is_approved==0 && $data->is_rejected==0) {
                                $val = $val . '<span class="badge badge-dark">Verified</span>';
                            }
                        }
                        if ($role_name == 'Approver' || $role_name == 'HOD') {
                            if (($report_type == 'A' || $report_type == 'R') && $data->lot_generated == 0 && $data->payment_count == 0) {
                                $val = $val . '<button class="btn btn-warning ben_reject_button">Reject</button>';
                                $val = $val . '<button class="btn btn-success ben_revert_button">Revert</button>';
                            } else if ($report_type == 'T') {
                                $val = $val . '<button class="btn btn-success ben_revert_button">Revert</button>';
                            }
                        }
                        return $val;
                    })
                    ->rawColumns(['ben_id', 'ben_name', 'bank_ifsc', 'bank_code', 'village_town_city', 'action'])
                    ->make(true);
            } else {
                //Goto view
                return view('BriefReport.index')->with('district_name', 'Test')->with('district_code', $request->session()->get('distCode'))->with('scheme', $request->session()->get('scheme_id'))
                    // ->with('schemetype','$schemetype')
                    ->with('report_type_name', $report_type_name)->with('type', $request->get('type'))
                    ->with('pr1', $request->get('pr1'))
                    ->with('scheme_name', $request->session()->get('scheme_name'));
            }
        } else {
            return redirect('/')->with('error', 'User not Authorized for this scheme');
        }
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
            $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();            foreach ($roleArray as $roleObj) {
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
            return $is_active;
        }
        return -1;
    }
    public function rejectApplication(Request $request)
    {

        $ben_id = $request->ben_id;

        $model_name = $request->session()->get('model_name');

        $role_id = $request->session()->get('role_id');
        $user_id = AuthChecker::getUserId();
        //$reject_reason = $request->reject_reason;
        $reject_reason = 'Rejected by user: ' . $user_id;
        DB::beginTransaction();
        try {

            $input_update = ['is_approved' => 2,'is_verified' => 2,'is_rejected' => 1,'next_level_role_id' => -1, 'comments' => $reject_reason];
            $model_name::where('id', $ben_id)->whereDate('created_at', '>=', '2021-02-02')->where('legacy_import', TRUE)->where('lot_generated', 0)->where('payment_count', 0)->update($input_update);
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
        $user_id = AuthChecker::getUserId();
        //$reject_reason = $request->reject_reason;
        $revert_reason = 'Reverted by user: ' . $user_id;
        DB::beginTransaction();
        try {

            $input_update = ['next_level_role_id' => null, 'comments' => $revert_reason];
            $model_name::where('id', $ben_id)->whereDate('created_at', '>=', '2021-02-02')->where('legacy_import', TRUE)->where('lot_generated', 0)->where('payment_count', 0)->update($input_update);
        } catch (\Exception $e) {
            DB::rollback();
        }
        DB::commit();
    }
    function wcdoapwpreport(Request $request)
    {
        $base_date  = '2020-01-01';
        $c_time = Carbon::now();
        $c_date = $c_time->format("Y-m-d");
        $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                $designation_id = Auth::user()->designation_id;
        $district_visible = $is_urban_visible = $block_visible = 1;
        $scheme_arr = array();
        if ($designation_id == 'Admin' || $designation_id == 'HOD' ||  $designation_id == 'Dashboard') {
            $district_visible = $is_urban_visible = $block_visible = 1;
            $scheme_arr = array(10, 11);
        } else if ($designation_id == 'Approver' || $designation_id == 'Verifier') {
            $district_code = NULL;
            $is_urban = NULL;
            $blockCode = NULL;

            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == 10) {
                    array_push($scheme_arr, 10);
                    $is_urban = $roleObj['is_urban'];
                    $district_code = $roleObj['district_code'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }

            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == 11) {
                    array_push($scheme_arr, 11);
                    $is_urban = $roleObj['is_urban'];
                    $district_code = $roleObj['district_code'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
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
        } else {
            $block_munc_corp_code_fk = NULL;
        }
        $schemes = Scheme::where('is_active', 1)->whereIn('id', $scheme_arr)->get(['scheme_name', 'id']);
        $districts = District::get();
        return view(
            'BriefReport.wcdoapwpreport',
            [
                'schemes' => $schemes,
                'districts' => $districts,
                'district_visible' => $district_visible,
                'district_code_fk' => $district_code_fk,
                'is_urban_visible' => $is_urban_visible,
                'rural_urban_fk' => $rural_urban_fk,
                'block_visible' => $block_visible,
                'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
                'base_date' => $base_date,
                'c_date' => $c_date
            ]
        );
    }
    function wcdoapwpreportPost(Request $request)
    {

        $scheme_code = $request->scheme_code;
        $scheme_row = Scheme::where('is_active', 1)->where('id', $scheme_code)->first();
        $district = $request->district;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $report_type = $request->report_type;
        $base_date  = '2020-01-01';
        $c_time = Carbon::now();
        $c_date = $c_time->format("Y-m-d");
        $heading_msg = '';
        $title = "";
        $district_condition = "";
        //$block_condition = "";
        if (!empty($district)) {
            $district_row = District::where('district_code', $district)->first();
            $district_condition = " and created_by_dist_code=" . $district;
        } else {
            $district_condition = "";
        }
        $urban_code = $request->urban_code;
        $block = $request->block;
        if (!empty($block)) {

            if ($urban_code == 1) {
                $block_ulb = UrbanBody::where('urban_body_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->urban_body_name;
                //$block_condition = " and rural_urban_id=1 and created_by_local_body_code=" . $block;
            } else {
                $block_ulb = Taluka::where('block_code', '=', $block)->first();
                $blk_munc_name = $block_ulb->block_name;
                // $block_condition = " and rural_urban_id=2 and  created_by_local_body_code=" . $block;
            }
        } else {
            // $block_condition = "";
        }

        $districtwise = $blockwise = $muncwise = $gpwise = $wardwise = 0;
        $rules = [
            'scheme_code' => 'required|in:10,11',
            'from_date'    => 'nullable|date|after_or_equal:' . $base_date . '|before_or_equal:' . $c_date,
            'to_date'      => 'nullable|date|after_or_equal:from_date|before_or_equal:' . $c_date,
            'report_type' => 'nullable|in:1,2,3',
        ];
        $data = array();
        $column = "";
        $attributes = array();
        $messages = array();
        $attributes['type_of_penstion'] = 'Select Pension';
        $attributes['from_date'] = 'From Date';
        $attributes['to_date'] = 'To Date';
        $validator = Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->passes()) {
            $from_date_condition = "";
            $to_date_condition = "";
            $legacy_import_condition = "";
            $created_at_date_condition = "";
            $user_msg = "";


            if (empty($report_type)) {
                $legacy_import_condition = "";
                $created_at_date_condition = "";
                $user_msg = " Consolidated Data Entry Report for WCD";
            } else {
                if ($report_type == 1) {
                    $user_msg = " Normal Data Entry Report for WCD";
                    $created_at_date_condition = "";
                    $legacy_import_condition = " and (main.legacy_import!=TRUE)";
                } else if ($report_type == 2) {
                    //  dd('ok');
                    $user_msg = " Brief Data Entry Report for WCD";
                    $created_at_date_condition = " and date(main.created_at)>='2021-02-02'";
                    $legacy_import_condition = " and main.legacy_import=TRUE";
                } else if ($report_type == 3) {
                    $user_msg = " Legacy Data Entry Report for WCD";
                    $created_at_date_condition = "";
                    $legacy_import_condition = " and main.legacy_import=TRUE";
                }
            }
            $title = $user_msg;
            //dd($title);
            if (!empty($from_date)) {
                $form_date_formatted = \Carbon\Carbon::parse($from_date)->format('d-m-Y');
                $user_msg = $user_msg . " from " . $form_date_formatted;
                $from_date_condition = " and date(main.created_at)>='" . $from_date . "'";
            }
            if (!empty($to_date)) {
                $to_date_formatted = \Carbon\Carbon::parse($to_date)->format('d-m-Y');
                $user_msg = $user_msg . " to  " . $to_date_formatted;
                $to_date_condition = " and date(main.created_at)<='" . $to_date . "'";
            }
            if ($scheme_code == 10)
                $table_schema = 'oap_wcd';
            else if ($scheme_code == 11)
                $table_schema = 'wp_wcd';
            $data = array();
            $return_status = 1;
            $return_msg = '';
            $heading_msg = '';
            //dd($legacy_import);
            if (!empty($block)) {
                if ($urban_code == 1) {
                    $query = "select A.*,B.*
                from
                (
                select urban_body_ward_code,urban_body_ward_name as location_name from m_urban_body_ward where urban_body_code=" . $block . "
                order by urban_body_ward_name
                ) as A LEFT JOIN
                (
                    select count(distinct(main.id)) as applied,
coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1)) as rejected,
coalesce(count( distinct main.id) FILTER(WHERE lm.lot_status<=6),0) as pushed_sbi,main.gp_ward_code
                    from " . $table_schema . ".beneficiary as main 
                    LEFT JOIN sbi.transaction_lot_details as tld ON main.id=tld.pension_id
                    LEFT JOIN sbi.transaction_lot as lm ON tld.lot_no = lm.lot_no
                    where main.rural_urban_id=1 and main.block_ulb_code=" . $block . " and main.scheme_id=" . $scheme_code . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "  " . $from_date_condition . "  " . $to_date_condition . "
                    group by main.gp_ward_code
                ) as B ON A.urban_body_ward_code=B.gp_ward_code";
                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                    $column = "Ward";
                    $heading_msg = 'Ward Wise ' . $user_msg . ' of the Municipality ' . $blk_munc_name;
                } else {
                    $query = "select A.*,B.*
                from
                (
                select gram_panchyat_code,gram_panchyat_name as location_name from m_gp where block_code=" . $block . "
                order by gram_panchyat_name
                ) as A LEFT JOIN
                (
                    select count(distinct(main.id)) as applied,
coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1)) as rejected,
coalesce(count( distinct main.id) FILTER(WHERE lm.lot_status<=6),0) as pushed_sbi,main.gp_ward_code
                    from " . $table_schema . ".beneficiary as main 
                    LEFT JOIN sbi.transaction_lot_details as tld ON main.id=tld.pension_id
                    LEFT JOIN sbi.transaction_lot as lm ON tld.lot_no = lm.lot_no
                    where main.rural_urban_id=2 and main.block_ulb_code=" . $block . " and main.scheme_id=" . $scheme_code . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "  " . $from_date_condition . "  " . $to_date_condition . "
                    group by main.gp_ward_code
                ) as B ON A.gram_panchyat_code=B.gp_ward_code";
                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                    $column = "GP";
                    $heading_msg = 'GP Wise ' . $user_msg . ' of the Block ' . $blk_munc_name;
                }
            } else if (!empty($urban_code)) {
                if ($urban_code == 1) {
                    $query = "select A.*,B.*
                from
                (
                select urban_body_code,urban_body_name as location_name from m_urban_body
				where district_code=" . $district . "
                order by urban_body_name
                ) as A LEFT JOIN
                (
                    select count(distinct(main.id)) as applied,
                    coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
                    coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1)) as rejected,
                    coalesce(count( distinct main.id) FILTER(WHERE lm.lot_status<=6),0) as pushed_sbi,
                    main.block_ulb_code
                    from " . $table_schema . ".beneficiary as main 
                    LEFT JOIN sbi.transaction_lot_details as tld ON main.id=tld.pension_id
                    LEFT JOIN sbi.transaction_lot as lm ON tld.lot_no = lm.lot_no
                    where main.rural_urban_id=1 and main.created_by_dist_code=" . $district . " and main.scheme_id=" . $scheme_code . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "  " . $from_date_condition . "  " . $to_date_condition . "
                    group by main.block_ulb_code
                ) as B ON A.urban_body_code=B.block_ulb_code";
                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                    $heading_msg = 'Municipality Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                    $column = "Municipality";
                } else {
                    $query = "select A.*,B.*
                from
                (
                select block_code,block_name as location_name from m_block
				where district_code=" . $district . "
                order by block_name
                ) as A LEFT JOIN
                (
                    select count(distinct(main.id)) as applied,
coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1)) as rejected,
coalesce(count( distinct main.id) FILTER(WHERE lm.lot_status<=6),0) as pushed_sbi,main.created_by_local_body_code
                    from " . $table_schema . ".beneficiary as main 
                    LEFT JOIN sbi.transaction_lot_details as tld ON main.id=tld.pension_id
                    LEFT JOIN sbi.transaction_lot as lm ON tld.lot_no = lm.lot_no
                    where main.rural_urban_id=2 and main.created_by_dist_code=" . $district . " and main.scheme_id=" . $scheme_code . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "  " . $from_date_condition . "  " . $to_date_condition . "
                    group by main.created_by_local_body_code
                ) as B ON A.block_code=B.created_by_local_body_code";
                    $data_part = DB::connection('pgsql_mis')->select($query);
                    $data = array_merge($data, $data_part);
                    $heading_msg = 'Block Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                    $column = "Block";
                }
            } else if (!empty($district)) {
                $query = "select A.*,B.*
                from
                (
                select urban_body_code,urban_body_name||'-M' as location_name from m_urban_body
				where district_code=" . $district . "
                order by urban_body_name
                ) as A LEFT JOIN
                (
                    select count(distinct(main.id)) as applied,
coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1)) as rejected,
coalesce(count( distinct main.id) FILTER(WHERE lm.lot_status<=6),0) as pushed_sbi,main.block_ulb_code
                    from " . $table_schema . ".beneficiary as main 
                    LEFT JOIN sbi.transaction_lot_details as tld ON main.id=tld.pension_id
                    LEFT JOIN sbi.transaction_lot as lm ON tld.lot_no = lm.lot_no
                    where main.rural_urban_id=1 and main.created_by_dist_code=" . $district . " and main.scheme_id=" . $scheme_code . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "  " . $from_date_condition . "  " . $to_date_condition . "
                    group by main.block_ulb_code
                ) as B ON A.urban_body_code=B.block_ulb_code";

                $data_part1 = DB::connection('pgsql_mis')->select($query);
                $data1 = array_merge($data, $data_part1);

                $query = "select A.*,B.*
                from
                (
                select block_code,block_name||'-B' as location_name from m_block
				where district_code=" . $district . "
                order by block_name
                ) as A LEFT JOIN
                (
                    select count(distinct(main.id)) as applied,
coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1)) as rejected,
coalesce(count( distinct main.id) FILTER(WHERE lm.lot_status<=6),0) as pushed_sbi,main.created_by_local_body_code
                    from " . $table_schema . ".beneficiary as main 
                    LEFT JOIN sbi.transaction_lot_details as tld ON main.id=tld.pension_id
                    LEFT JOIN sbi.transaction_lot as lm ON tld.lot_no = lm.lot_no
                    where main.rural_urban_id=2 and main.created_by_dist_code=" . $district . " and main.scheme_id=" . $scheme_code . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "  " . $from_date_condition . "  " . $to_date_condition . "
                    group by main.created_by_local_body_code
                ) as B ON A.block_code=B.created_by_local_body_code";
                $data_part = DB::connection('pgsql_mis')->select($query);
                $data2 = array_merge($data, $data_part);
                $data = array_merge($data1, $data2);
                $heading_msg = 'Block/Munc Wise ' . $user_msg . ' of the District ' . $district_row->district_name;
                $column = "Block/Munc";
            } else {
                $query = "select A.*,B.*
                from
                (
                select district_code,district_name as location_name from m_district
                order by district_name
                ) as A LEFT JOIN
                (
                    select count(distinct(main.id)) as applied,
                    coalesce(count( distinct main.id) FILTER(WHERE next_level_role_id=0)) as approved,
                    coalesce(count( distinct main.id) FILTER(WHERE is_rejected=1)) as rejected,
                    coalesce(count( distinct main.id) FILTER(WHERE lm.lot_status<=6),0) as pushed_sbi,main.created_by_dist_code
                    from " . $table_schema . ".beneficiary as main 
                    LEFT JOIN sbi.transaction_lot_details as tld ON main.id=tld.pension_id
                    LEFT JOIN sbi.transaction_lot as lm ON tld.lot_no = lm.lot_no
                    where main.scheme_id=" . $scheme_code . " " . $created_at_date_condition . " 
                    " . $legacy_import_condition . "  " . $from_date_condition . "  " . $to_date_condition . "
                    group by main.created_by_dist_code
                ) as B ON A.district_code=B.created_by_dist_code";
                $data_part = DB::connection('pgsql_mis')->select($query);
                $data = array_merge($data, $data_part);
                $heading_msg = 'District Wise ' . $user_msg;
                $column = "Disttrict";
            }
            $heading_msg = $heading_msg . ' for ' . $scheme_row->scheme_name;
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
}
