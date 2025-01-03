<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

use App\District;

use App\BeneficiaryPensions;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\Scheme;


use App\ApplicationStatus;
use App\StatusCode;
use App\BankResponse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

use App\UrbanBody;
use App\SubDistrict;
use App\PensionSc;
use App\PensionSt;
use App\PensionFisherman;
//Dynamic Doc
use App\BenDocsSc;
use App\BenDocsSt;
use App\BenDocsFisherman;
use App\BenDocsArcSc;
use App\BenDocsArcSt;
use App\BenDocsArcFisherman;
use App\DocumentType;
use App\SchemeDocMap;
//Dynamic Doc End
use App\Manabik;
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use Illuminate\Support\Facades\Validator;
use DateTime;
use App\BankDetails;
use App\User;
use Redirect;
use Illuminate\Support\Facades\Input;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\Helper;
use App\Helpers\AuthChecker;


class SingleStepVerification extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  public function index(Request $request, $scheme)
  {
    $is_active = 0;

    $districts = District::all();
    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();;
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme) {
        $is_active = 1;
        $request->session()->put('distCode', $roleObj['district_code']);
        break;
      }
    }
    $scheme_name = $this->getSchemeName($scheme);
    $designation_id = Auth::user()->designation_id;
    if ($designation_id != 'Approver') {
      $is_active = 0;
    }
    if ($is_active == 1) {
      $scheme_capacity_arr = array();
      $distCode = $request->session()->get('distCode');
      $scheme_capacity_arr = Helper::getCapacity($scheme, $distCode);
      if ($scheme_capacity_arr['visible'] == 1) {
        if ($scheme_capacity_arr['total_data'] > $scheme_capacity_arr['capacity']) {
          $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
          return redirect("/")->with('error', $errorMsgCap);
        }
      }
      $user_id = AuthChecker::getUserId();
      $district_code = $distCode;
      $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();
      $rural_urban_fk = null;
      $block_munc_corp_code_fk = NULL;
      return view(
        'singlestep.index',
        [
          'district_name' => $district_name,
          'district_code' => $district_code,
          'scheme' => $scheme,
          'scheme_name' => $scheme_name,
          'rural_urban_fk' => $rural_urban_fk,
          'district_code_fk' => $district_code,
          'block_munc_corp_code_fk' => $block_munc_corp_code_fk,
          'districts' => $districts
        ]
      );
    } else if ($is_active == 0) {
      return redirect("/")->with('success', 'User Disabled for scheme ' . $scheme_name);
    } else {
      return redirect("/")->with('success', 'User Disabled for scheme ' . $scheme_name);
    }
  }
  public function indexDistrict(Request $request, $scheme)
  {
    $is_active = 1;
    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();;
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme) {
        $is_active = 1;
        $distCode = $roleObj['district_code'];
        break;
      }
    }
    $scheme_name = $this->getSchemeName($scheme);

    if ($is_active == 1) {
      $scheme_capacity_arr = array();

      $scheme_capacity_arr = Helper::getCapacity($scheme, $distCode);
      if ($scheme_capacity_arr['visible'] == 1) {
        if ($scheme_capacity_arr['total_data'] > $scheme_capacity_arr['capacity']) {
          $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
          return redirect("/")->with('error', $errorMsgCap);
        }
      }
      $user_id = \Auth::user()->id;
      //$duty = Configduty::where('user_id', '=', $user_id)->first();
      // echo "<pre>";print_r($duty);die();
      $district_code = $distCode;
      //dd($district_code);
      $block_list = Taluka::where('district_code', $district_code)->select('block_code', 'block_name')->get()->toArray();
      $urban_body_list = UrbanBody::where('district_code', $district_code)->select('urban_body_code', 'urban_body_name')->get()->toArray();
      $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();
      return view('singlestep.indexdistrict')->with('district_name', $district_name)->with('district_code', $district_code)->with('scheme', $scheme)
        ->with('scheme_name', $scheme_name)->with('blocks', $block_list)->with('urban_bodies', $urban_body_list);
    }
    if ($is_active == 0) {
      return redirect("/")->with('success', 'User Disabled for scheme ' . $scheme_name);
    } else {
      return redirect("/")->with('success', 'User Disabled');
    }
  }
  private function getSchemeName($scheme)
  {
    $scheme_name = "";
    if ($scheme == 2) {
      $scheme_name = "Manabik [WCD]";
    } else if ($scheme == 10) {
      $scheme_name = "Old Age Pension [WCD]";
    } else if ($scheme == 11) {
      $scheme_name = "Widow Pension [WCD]";
    } else if ($scheme == 12) {
      $scheme_name = "Old Age Pension for ST [WCD]";
    } else if ($scheme == 13) {
      $scheme_name = "Old Age Pension for Farmer";
    }

    return $scheme_name;
  }
  private function getModelName($scheme)
  {
    $model_name = "";
    if ($scheme == "2") {
      $model_name = "PensionManabikWCD";
    } else if ($scheme == "10") {
      $model_name = "PensionOAPWCD";
    } else if ($scheme == "11") {
      $model_name = "PensionWPWCD";
    } else if ($scheme == "12") {
      $model_name = "PensionOAPSTWCD";
    } else if ($scheme == "13") {
      $model_name = "PensionOAPFarmer";
    } else {
      $model_name = "BeneficiaryPensionsReport";
    }
    $appPrefix = 'App';
    $modelName = $appPrefix . '\\' . $model_name;

    return $modelName;
  }
  public function getData(Request $request)
  {
    //DB::enableQueryLog();
    if (request()->ajax()) {
      $user_id = AuthChecker::getUserId();
      // $duty = Configduty::where('user_id', '=', $user_id)->first();
      $district_code = $request->district_code;
      $serachvalue = $request->search['value'];
      $scheme = $request->scheme;
      //dd($scheme);
      // $schemetype = $request->schemetype;


      $model_name = $this->getModelName($scheme);

      //Urban/Rural
      //$level=$request->level3;
      $level = $request->urban_code;
      $block = $request->block;
      //LocalBody
      //$localBody=$request->level1a;

      $flag = 1;
      $totalRecords = 0;
      $data = array();

      // WORKING QUERY

      $limit = $request->input('length');
      $offset = $request->input('start');

      $condition = array();
      //$condition["date(created_at)>="] = '2021-02-02';
      $condition["legacy_import"] = TRUE;

      //$query = "";

      //$mainquery = " select * from pension.beneficiary where next_level_role_id!=0";
      if (!empty($district_code)) {
        $condition["created_by_dist_code"] = $district_code;
      }
      if (!empty($level)) {
        //'Rural'
        if ($level == 2) {
          if (!empty($block)) {
            $condition["rural_urban_id"] = 2;
            $condition["block_ulb_code"] = $block;
          }
        }
        //'Urban'
        if ($level == 1) {
          if (!empty($block)) {
            $condition["rural_urban_id"] = 1;
            $condition["block_ulb_code"] = $block;
          }
        }
      }

      if (empty($serachvalue)) {
        $data = $model_name::whereDate('created_at', '>=', '2021-02-02')->where($condition)->where(function ($query) {
          $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)")->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
          'id', 'created_by_dist_code',
          'bank_code', 'bank_name', 'branch_name', 'ben_fname', 'block_ulb_name', 'bank_ifsc', 'village_town_city',
          'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
        ]);
        $totalRecords = $model_name::whereDate('created_at', '>=', '2021-02-02')->where($condition)->where(function ($query) {
          $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
        })->count();
        $filterRecords = count($data);
        })}
         else {
        $data = array();
        if (is_numeric($serachvalue)) {
          $ben_id = substr($serachvalue, -7);
          $query = $model_name::where(function ($query) {
            $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
          })->where(function ($query1) use ($ben_id, $serachvalue) {
            $query1->where('id', $ben_id)
              ->orWhere('bank_code', $serachvalue);
          })->where($condition);
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
            'id', 'created_by_dist_code',
            'bank_code', 'bank_name', 'branch_name', 'ben_fname', 'block_ulb_name', 'bank_ifsc', 'village_town_city',
            'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
          ]);
        } else {
          $query = $model_name::where(function ($query) {
            $query->whereraw(" (is_verified=1 and is_approved=0 and is_rejected=0)) or next_level_role_id IS NULL)");
          })->where(function ($query1) use ($serachvalue) {
            $query1->where('ben_fname', 'like', $serachvalue . '%')
              ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
              ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
          })->where($condition);
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
            'id', 'created_by_dist_code',
            'bank_code', 'bank_name', 'branch_name', 'ben_fname', 'block_ulb_name', 'bank_ifsc', 'village_town_city',
            'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
          ]);
        }

        $filterRecords = $totalRecords = count($data);
        //$data = DB::connection('pgsql2')->select($query);
        //$totalRecords = count($data);
      }
      return datatables()
        ->of($data)
        ->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('check', function ($data) {
          return '<input type="checkbox"  class="checkboxall" name="approvalcheck[]" onchange="controlCheckBox();" value="' . $data->id . '">';
        })
        ->addColumn('application_id', function ($data) {
          return $data->getBenidAttribute();
        })
        ->addColumn('ben_id', function ($data) {
          return $data->id;
        })
        ->addColumn('ben_name', function ($data) {
          return $data->getName();
        })
        ->addColumn('benf_name', function ($data) {
          return $data->getFatherName();
        })
        ->addColumn('old_beneficiary_id', function ($data) {
          return $data->old_beneficiary_id;
        })
        ->addColumn('bank_ifsc', function ($data) {
          return $data->bank_ifsc;
        })
        ->addColumn('bank_code', function ($data) {
          return $data->bank_code;
        })
        ->addColumn('bank_name', function ($data) {
          return $data->bank_name;
        })
        ->addColumn('branch_name', function ($data) {
          return $data->branch_name;
        })
        ->addColumn('village_town_city', function ($data) {
          return $data->village_town_city;
        })
        ->addColumn('action', function ($data) {
          $val = '<button class="btn btn-primary ben_view_button">View</button>';
          $val = $val . '<button class="btn btn-warning ben_reject_button">Reject</button>';
          // $val = $val . '<button class="btn btn-success ben_edit_approve_button">Edit</button>';
          $val = $val . '<button class="btn btn-success" onclick="editApproveModal(' . $data->id . ',' . $data->getBenidAttribute() . ')">Edit&Approve</button>';
          return $val;
        })
        ->rawColumns(['check', 'ben_id', 'id', 'ben_name', 'old_beneficiary_id', 'bank_ifsc', 'bank_code', 'village_town_city', 'action'])
        ->make(true);
    }
  }

  public function getDistrictData(Request $request)
  {
    //DB::enableQueryLog();
    if (request()->ajax()) {
      $user_id = AuthChecker::getUserId();
      $duty = Configduty::where('user_id', '=', $user_id)->first();
      $district_code = $request->level1;
      $district_name = $request->level2;
      $level = $request->level;
      $localBody = $request->localBody;

      $serachvalue = $request->search['value'];
      $scheme = $request->scheme;
      // $schemetype = $request->schemetype;

      $model_name = $this->getModelName($scheme);


      $flag = 1;
      $totalRecords = 0;
      $data = array();

      // WORKING QUERY

      $limit = $request->input('length');
      $offset = $request->input('start');

      $condition = array();

      if (!empty($district_code)) {
        $condition["created_by_dist_code"] = $district_code;
      }
      if (!empty($level)) {
        //'Rural'
        if (!empty($localBody)) {
          $condition["block_ulb_code"] = $localBody;
        }
      }
      //$query = $query.$mainquery;
      if (empty($serachvalue)) {
        $data = $model_name::where($condition)->whereNull('next_level_role_id')->orderBy('id', 'DESC')->offset($offset)->limit($limit)->get([
          'id', 'created_by_dist_code',
          'bank_code',
          'ben_fname',
          'block_ulb_name',
          'bank_ifsc',
          'bank_name',
          'branch_name',
          'village_town_city',
          'scheme_id',
          'lot_generated',
          'payment_count',
          'next_level_role_id'
        ]);
        $totalRecords = $model_name::where($condition)->whereNull('next_level_role_id')->count();
        $filterRecords = count($data);
      } else {
        $query = $model_name::where($condition);
        if (is_numeric($serachvalue)) {
          $ben_id = substr($serachvalue, -7);
          $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
            $query1->where('id', $ben_id)
              ->orWhere('bank_code', $serachvalue);
          });
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'id', 'created_by_dist_code',
              'bank_code',
              'ben_fname',
              'block_ulb_name',
              'bank_ifsc',
              'bank_name',
              'branch_name',
              'village_town_city',
              'scheme_id',
              'lot_generated',
              'payment_count',
              'next_level_role_id'
            ]
          );
        } else {
          $query = $query->where(function ($query1) use ($serachvalue) {
            $query1->where('ben_fname', 'like', $serachvalue . '%')
              ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
              ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
          });
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'id', 'created_by_dist_code',
              'bank_code',
              'ben_fname',
              'block_ulb_name',
              'bank_ifsc',
              'bank_name',
              'branch_name',
              'village_town_city',
              'scheme_id',
              'lot_generated',
              'payment_count',
              'next_level_role_id'
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
        ->addColumn('check', function ($data) {
          return '<input type="checkbox"  class="checkboxall" name="approvalcheck[]" onchange="controlCheckBox();" value="' . $data->id . '">';
        })
        ->addColumn('application_id', function ($data) {
          return $data->getBenidAttribute();
        })
        ->addColumn('ben_id', function ($data) {
          return $data->id;
        })
        ->addColumn('ben_name', function ($data) {
          return $data->getName();
        })
        ->addColumn('ben_fname', function ($data) {
          return $data->getFatherName();
        })
        ->addColumn('old_beneficiary_id', function ($data) {
          return $data->old_beneficiary_id;
        })
        ->addColumn('bank_ifsc', function ($data) {
          return $data->bank_ifsc;
        })
        ->addColumn('branch_name', function ($data) {
          return $data->branch_name;
        })
        ->addColumn('bank_name', function ($data) {
          return $data->bank_name;
        })
        ->addColumn('bank_code', function ($data) {
          return $data->bank_code;
        })
        ->addColumn('village_town_city', function ($data) {
          return $data->village_town_city;
        })
        ->addColumn('action', function ($data) {
          $val = '';
          //'<div class="btn-group" role="group" >';
          $val = $val . '<button class="btn btn-primary ben_view_button">View</button>';
          //$val = $val . '</div>';
          return $val;
        })
        ->rawColumns(['check', 'ben_id', 'id', 'ben_name', 'old_beneficiary_id', 'bank_ifsc', 'bank_code', 'village_town_city', 'action'])
        ->make(true);
    }
    return view('singlestep.indexdistrict')->with('district_name', $district_name)->with('district_code', $district_code);
  }

  public function editBeneficiary(Request $request)
  {
    $rules = [
      'ben_fname' => 'required|string|max:200',
      'ben_mname' => 'string|nullable|max:200',
      'ben_lname' => 'string|nullable|max:200',

      'benf_fname' => 'required|string|max:200',
      'benf_mname' => 'string|nullable|max:200',
      'benf_lname' => 'string|nullable|max:200',

      'ben_bank' => 'required|string|max:200',
      'ben_bank_branch' => 'required|string|max:200',
      'ben_bank_account' => 'required|numeric',
      'ben_bank_ifsc' => 'required|string|max:11',
      'addr_lin1' => 'string|nullable|max:200',
    ];
    $attributes = array();
    $messages = array();
    $attributes['ben_fname'] = 'Beneficiary First Name';
    $attributes['ben_mname'] = 'Beneficiary Middle Name';
    $attributes['ben_lname'] = 'Beneficiary Last Name';
    $attributes['benf_fname'] = 'Father First Name';
    $attributes['benf_mname'] = 'Father Middle Name';
    $attributes['benf_lname'] = 'Father Last Name';
    $attributes['ben_bank'] = 'Bank Name';
    $attributes['ben_bank_branch'] = 'Bank Branch';
    $attributes['ben_bank_account'] = 'Account No';
    $attributes['ben_bank_ifsc'] = 'IFS Code';
    $attributes['addr_lin1'] = 'Address Line 1';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $designation_id = Auth::user()->designation_id;
      if ($designation_id == 'Approver') {
        $scheme_capacity_arr = array();
        $distCode = $request->session()->get('distCode');
        $scheme_capacity_arr = Helper::getCapacity($request->scheme, $distCode);
        if ($scheme_capacity_arr['visible'] == 1) {
          if ($scheme_capacity_arr['total_data'] > $scheme_capacity_arr['capacity']) {
            $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
            return redirect("/")->with('error', $errorMsgCap);
          }
        }
        $input = [
          'ben_fname' => $request->ben_fname,
          'ben_mname' => $request->ben_mname,
          'ben_lname' => $request->ben_lname,

          'father_fname' => $request->benf_fname,
          'father_mname' => $request->benf_mname,
          'father_lname' => $request->benf_lname,

          'village_town_city' => $request->addr_lin1,
          'bank_name'  => $request->ben_bank,
          'branch_name'   => $request->ben_bank_branch,
          'bank_code'    => $request->ben_bank_account,
          'bank_ifsc'   => $request->ben_bank_ifsc,
          'next_level_role_id' => 0,
        ];

        $scheme_id = $request->scheme;
        $ben_id = $request->id;
        $modelName = $this->getModelName($scheme_id);
        $return_status = 0;
        $return_msg = '';
        $district_code = $request->session()->get('distCode');
        if ($modelName::where('created_by_dist_code', $district_code)->where('id', $ben_id)->whereDate('created_at', '>=', '2021-02-02')->where('legacy_import', TRUE)->whereNull('next_level_role_id')->whereNotNull('bank_code')->update($input)) {
          $return_status = 1;
          $return_text = "Beneficiary Detail Successfully Updated and Approved";
          $return_msg = array("" . $return_text);
        } else {
          $return_text = "Error.. Please try again..";
          $return_msg = array("" . $return_text);
        }
      } else {
        $return_status = 0;
        $return_msg = 'Not Authorized';
      }
    } else {
      $return_status = 0;
      $return_msg = $validator->errors()->all();
    }

    return response()->json([
      'return_status' => $return_status,
      'return_msg' => $return_msg
    ]);
  }

  public function bulkApprove(Request $request)
  {
    ini_set('max_execution_time', 180);
    $designation_id = Auth::user()->designation_id;
    if ($designation_id == 'Approver') {
      $user_id = AuthChecker::getUserId();
      $return_status = 0;
      $return_msg = '';
      $inputs_json = $request->approvalcheck;
      $scheme = $request->scheme;
      $inputs = json_decode($inputs_json, true);
      $district_code = $request->session()->get('distCode');
      $scheme_capacity_arr = array();
      $distCode = $request->session()->get('distCode');
      $scheme_capacity_arr = Helper::getCapacity($scheme, $distCode);
      if ($scheme_capacity_arr['visible'] == 1) {
        if ($scheme_capacity_arr['total_data'] > $scheme_capacity_arr['capacity']) {
          $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
          $return_status = 0;
          $return_msg = array("" . $errorMsgCap);
          return response()->json([
            'return_status' => $return_status,
            'return_msg' => $return_msg
          ]);
        } else {
          $total_check = $scheme_capacity_arr['total_data'] + count($inputs);
          if ($total_check > $scheme_capacity_arr['capacity']) {
            $errorMsgCap = "Total no. of Approved applications plus the applications which you have selected is: " . $total_check . " which exceeds the quota " . $scheme_capacity_arr['capacity'];
            $return_status = 0;
            $return_msg = array("" . $errorMsgCap);
            return response()->json([
              'return_status' => $return_status,
              'return_msg' => $return_msg
            ]);
          }
        }
      }
      $id_in = array();
      foreach ($inputs as $input) {
        array_push($id_in, $input);
      }
      $model_name = $this->getModelName($scheme);
      DB::beginTransaction();
      try {
        $input_update = ['next_level_role_id' => '0'];
        $model_name::where('created_by_dist_code', $district_code)->whereIn('id', $id_in)->whereDate('created_at', '>=', '2021-02-02')->where('legacy_import', TRUE)->whereNotNull('bank_code')->update($input_update);
      } catch (\Exception $e) {
        $return_status = 0;
        DB::rollback();
        $return_text = "Error.. Please try again..";
        $return_msg = array("" . $return_text);
        $return_status = 0;
        $return_text = "Error.. Please try again..";
        $return_msg = array("" . $return_text);
        return response()->json([
          'return_status' => $return_status,
          'return_msg' => $return_msg
        ]);
      }
      $return_status = 1;
      DB::commit();
      //});
    } else {
      $return_status = 0;
      $return_text = 'Not Authorized';
      $return_msg = array("" . $return_text);
    }
    return response()->json([
      'return_status' => $return_status,
      'return_msg' => $return_msg
    ]);
  }

  public function rejectApplication(Request $request)
  {
    $designation_id = Auth::user()->designation_id;
    $return_status = 0;
    $return_msg = '';
    if ($designation_id == 'Approver') {
      $ben_id = $request->ben_id;
      $district_code = $request->session()->get('distCode');

      // $scheme_id = $request->session()->get('scheme_id');
      $mappingLevel = $request->session()->get('level');
      $district_code = $request->session()->get('distCode');

      $scheme = $request->scheme;
      $model_name = $this->getModelName($scheme);

      $role_id = $request->session()->get('role_id');
      $user_id = AuthChecker::getUserId();

      //$reject_reason = $request->reject_reason;
      DB::beginTransaction();
      try {
        $input_update = ['next_level_role_id' => -1,'is_rejected' => 1,'is_approved' => 2,'is_verified' => 2];
        $model_name::where('created_by_dist_code', $district_code)->where('id', $ben_id)->whereDate('created_at', '>=', '2021-02-02')->where('legacy_import', TRUE)->update($input_update);
      } catch (\Exception $e) {
        DB::rollback();
        $return_status = 0;
        $return_text = "Error.. Please try again..";
        $return_msg = array("" . $return_text);
        return response()->json([
          'return_status' => $return_status,
          'return_msg' => $return_msg
        ]);
      }
      DB::commit();
      $return_status = 1;
    } else {
      $return_status = 0;
      $return_text = 'Not Authorized';
      $return_msg = array("" . $return_text);
    }
    return response()->json([
      'return_status' => $return_status,
      'return_msg' => $return_msg
    ]);
  }


  public function printSingleBeneficiary(Request $request)
  {
    $ben_id = $request->ben_id;

    $ben = PensionSc::where('id', $ben_id)->first();
    $localBody = "";
    if ($ben->rural_urban == 'Rural') {
      $localBody = GP::where('gram_panchyat_code', $ben->gp_code)->pluck('gram_panchyat_name');
    } else {
      $localBody = Ward::where('urban_body_ward_code', $ben->ward_code)->pluck('urban_body_ward_name');
    }
    return view('singlestep.print_ben_dtl')->with('ben', $ben)
      ->with('localBody', $localBody);
  }

  public function getStatusCode(Request $request)
  {
    $statusCode = StatusCode::select('code', 'message')->where('code', '>', 5)->get();
    return $statusCode;
  }

  //Get Filter Dropdown
  public function getLocalBody(Request $request)
  {
    //UrbanBody/Taluka
    $urban_rural = $request->urban_rural;
    $district_code = $request->district_code;

    if ($urban_rural == 1) {
      $body = UrbanBody::where('district_code', '=', $district_code)->get(['urban_body_code AS id', 'urban_body_name AS name']);
    } else {
      $body = Taluka::where('district_code', '=', $district_code)->get(['block_code AS id', 'block_name AS name']);
    }
    return response()->json($body);
  }

  public function applicationeditview(Request $request)
  {
    $user_id = AuthChecker::getUserId();
    $id = $request->id;

    //echo "scheme_id".$scheme_id;die();
    $scheme_id = $request->scheme_id;
    //$row = '';
    $row = array();
    $modelName = $this->getModelName($scheme_id);

    $row = $modelName::find($id);


    $districts = District::where('is_revenue_district', '=', '1')->get(['district_code', 'district_name']);

    return view('singlestep/pension_edit', ['row' => $row, 'districts' => $districts, 'scheme_id' => $scheme_id]);
  }


  public function applicationupdate(Request $request)
  {

    $base_url = url('/');
    $id = $request->id;
    $scheme_id = $request->scheme_id;
    $scheme_capacity_arr = array();
    $distCode = $request->session()->get('distCode');
    $scheme_capacity_arr = Helper::getCapacity($scheme_id, $distCode);
    if ($scheme_capacity_arr['visible'] == 1) {
      if ($scheme_capacity_arr['total_data'] > $scheme_capacity_arr['capacity']) {
        $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
        return redirect("/")->with('error', $errorMsgCap);
      }
    }
    $this->validateInput($request, $scheme_id);

    $modelName = $this->getModelName($scheme_id);
    $row = $modelName::find($id);

    $social_security_pension = "";
    $receive_pension = "";
    if ($request->receive_pension != "") {
      $receive_pension = implode(',', $request->receive_pension);
    }

    if ($request->social_security_pension != "") {
      $social_security_pension = implode(',', $request->social_security_pension);
    }

    $block_ulb_name = "";
    $gp_ward_name = "";

    if ($request->urban_code == 1) {
      $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
      $gp_ward = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();


      $block_ulb_name = $block_ulb->urban_body_name;
      $gp_ward_name   = $gp_ward->urban_body_ward_name;
    } else {
      $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
      $gp_ward = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();

      $block_ulb_name = $block_ulb->block_name;
      $gp_ward_name   = $gp_ward->gram_panchyat_name;
    }
    $assembly = Assembly::where('ac_no', '=', $request->asmb_cons)->first();
    $assembly_name = $assembly->ac_name;

    if (trim($request->marital_status) != "Married") {
      $request->spouse_first_name = "";
      $request->spouse_middle_name = "";
      $request->spouse_last_name = "";
    }

    $input = [
      //'name' => $request['name']
      'ben_fname' => $request->first_name,
      'ben_mname' => $request->middle_name,
      'ben_lname' => $request->last_name,
      'gender' => $request->gender,
      'dob' => $request->dob,
      'ben_age' => $request->txt_age,

      'father_fname' => $request->father_first_name,
      'father_mname' => $request->father_middle_name,
      'father_lname' => $request->father_last_name,
      'mother_fname' => $request->mother_first_name,
      'mother_mname' => $request->mother_middle_name,
      'mother_lname' => $request->mother_last_name,
      'caste' => $request->caste_category,
      'marital_status' => $request->marital_status,

      'spouse_fname' => $request->spouse_first_name,
      'spouse_mname' => $request->spouse_middle_name,
      'spouse_lname' => $request->spouse_last_name,
      //'bpl_y_n' =>$request->if_bpl,
      'bpl_seq_no' => $request->bpl_seq_no,
      'bpl_id_no' => $request->bpl_id_no,
      'bpl_total_score' => $request->bpl_total_score,
      'mothly_income' => $request->monthly_income,

      'receive_pension' => $receive_pension,
      'social_security_pension' => $social_security_pension,

      'ration_card_cat' => $request->ration_card_cat,
      'ration_card_no'  => $request->ration_card_no,
      'ahl_tin'  => $request->ahl_tin,
      'aadhar_no'  => $request->aadhar_no,
      'epic_voter_id'  => $request->epic_voter_id,
      'pan_no'  => $request->pan_no,



      'dist_code' => $request->district,
      'assembly_code'  => $request->asmb_cons,
      'assembly_name' => $assembly_name,
      'rural_urban_id' => $request->urban_code,
      'police_station'  => $request->police_station,
      'block_ulb_code'  => $request->block,
      'block_ulb_name' => $block_ulb_name,
      'gp_ward_code' => $request->gp_ward,
      'gp_ward_name' => $gp_ward_name,
      'village_town_city'  => $request->village,
      'house_premise_no'  => $request->house,
      'post_office'  => $request->post_office,
      'pincode' => $request->pin_code,
      'residency_period' => $request->residency_period,
      'mobile_no'  => $request->mobile_no,
      'email' => $request->email,



      'bank_name'  => $request->name_of_bank,
      'branch_name'   => $request->bank_branch,
      'bank_code'    => $request->bank_account_number,
      'bank_ifsc'   => $request->bank_ifsc_code,
      'nominate_name' => $request->nominate_name,
      'nominate_address' => $request->nominate_address,
      'nominate_relationship' => $request->nominate_relationship,
      'next_level_role_id' => 0,
    ];


    $modelName::where('id', $id)->update($input);


    return redirect("verify/" . $scheme_id)->with('success', 'Application Updated Successfully')
      ->with('id',  $row->getBenidAttribute());
  }
  private function validateInput($request, $scheme_id)
  {

    $singleArray = array();
    $nicenameArray = array();
    $customMessage = array();


    $this->validate($request, array_merge([
      //'first_name' => 'required|string|max:200',
      'first_name' => 'required|string|max:200',
      'middle_name' => 'string|nullable',
      'last_name' => 'required|string|max:200',
      'gender' => 'required',
      // 'dob' => '',
      'txt_age' => 'required|numeric',

      'father_first_name' => 'required|string|max:200',
      'father_middle_name' => 'string|nullable',
      'father_last_name' => 'required|string|max:200',
      'mother_first_name' => 'required|string|max:200',
      'mother_middle_name' => 'string|nullable',
      'mother_last_name' => 'required|string|max:200',
      'caste_category' => 'required',
      'marital_status' => 'required',

      'spouse_first_name' => 'string|nullable',
      'spouse_middle_name' => 'string|nullable',
      'spouse_last_name' => 'string|nullable',
      // 'if_bpl' => ,
      'bpl_seq_no' => 'string|nullable|max:12',
      'bpl_id_no' => 'string|nullable|max:12',
      'bpl_total_score' => 'integer|nullable',
      'monthly_income' => 'required|numeric|between: 0.00,999999.99',


      'ration_card_cat' => 'required|string',
      'ration_card_no' => 'required|string|max:11',

      'ahl_tin' => 'string|nullable|max:100',
      'aadhar_no' => 'numeric|digits:12|nullable',
      'epic_voter_id' => 'required|string|max:20',
      'pan_no' => 'string|nullable|max:12',



      //  'district' => 'string',
      'asmb_cons' => 'required|string',
      'police_station' => 'required|string',
      //'block' => 'max:200',
      // 'gp_ward' => 'max:200',
      'village' => 'required|string|max:300',
      'house' => 'string|nullable',
      'post_office' => 'required|string',
      'pin_code' => 'required|numeric|digits:6',
      'residency_period' => 'required|integer',
      'mobile_no' => 'required|numeric|digits:10',
      'email' => 'string|email|nullable',



      'name_of_bank' => 'required|string|max:200',
      'bank_branch' => 'required|string|max:200',
      'bank_account_number' => 'required|numeric',
      'bank_ifsc_code' => 'required|string|max:11',



    ], $singleArray), $customMessage, $nicenameArray);
  }


  public function report($scheme, $approved_rejected)
  {
    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id', '=', $user_id)->first();
    // echo "<pre>";print_r($duty);die();
    $district_code = $duty->district_code;
    $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();
    $scheme_name = $this->getSchemeName($scheme);
    $app_rej = 0;
    if ($approved_rejected == 'R') {
      $app_rej = -1;
    }
    return view('singlestep.report')->with('district_name', $district_name)->with('district_code', $district_code)->with('scheme', $scheme)->with('scheme_name', $scheme_name)
      ->with('approved_rejected', $app_rej)->with('schemetype', 'C');
  }
  public function getProcessedData(Request $request)
  {
    $district_code = $request->level1;
    $district_name = $request->level2;
    if (request()->ajax()) {
      $user_id = AuthChecker::getUserId();
      $duty = Configduty::where('user_id', '=', $user_id)->first();

      $serachvalue = $request->search['value'];
      $scheme = $request->scheme;

      $model_name = $this->getModelName($scheme);

      //Urban/Rural
      $level = $duty->is_urban;

      $flag = 1;
      $totalRecords = 0;
      $data = array();

      // WORKING QUERY

      $limit = $request->input('length');
      $offset = $request->input('start');

      //approved_rejected=0 is Approved, -1 is Rejected
      $approved_rejected = $request->approved_rejected;

      //print_r($duty);

      //echo "ModelName: ".$model_name;
      //echo "Approved_Rejected: ".$approved_rejected;

      $condition = array();

      if (!empty($district_code)) {
        $condition["created_by_dist_code"] = $district_code;
      }
      if (!empty($level)) {
        //'Rural'
        if ($level == 2) {
          if (!empty($duty->taluka_code)) {
            $condition["block_ulb_code"] = $duty->taluka_code;
          }
        }
        //'Urban'
        if ($level == 1) {
          if (!empty($duty->urban_body_code)) {
            $condition["created_by_local_body_code"] = $duty->urban_body_code;
          }
        }
      }
      //$query = $query.$mainquery;
      if (empty($serachvalue)) {
        $data = $model_name::whereDate('created_at', '>=', '2021-02-02')->where('legacy_import', TRUE)->where($condition)->where('next_level_role_id', $approved_rejected)->orderBy('id', 'DESC')->offset($offset)->limit($limit)->get();
        $totalRecords = $model_name::where($condition)->whereDate('created_at', '>=', '2021-02-02')->where('legacy_import', TRUE)->where('next_level_role_id', $approved_rejected)->count();
        $filterRecords = count($data);
      } else {
        $query = $model_name::where($condition);
        if (is_numeric($serachvalue)) {
          $ben_id = substr($serachvalue, -7);
          $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
            $query1->where('id', $ben_id)
              ->orWhere('bank_code', $serachvalue);
          })->where('legacy_import', TRUE)->where('next_level_role_id', $approved_rejected);
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
        } else {
          $query = $query->where(function ($query1) use ($serachvalue) {
            $query1->where('ben_fname', 'like', $serachvalue . '%')
              ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
              ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
          })->where('legacy_import', TRUE)->where('next_level_role_id', $approved_rejected);
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
        }

        //$query = $query." and id='".$serachvalue."'";

        // $data = $model_name::whereDate('created_at', '>=', '2021-02-02')->where('legacy_import', TRUE)
        //       ->where('id', $serachvalue)
        //       ->where('next_level_role_id', $approved_rejected)->offset($offset)->limit($limit)->get();
        $filterRecords = $totalRecords = count($data);
        //$data = DB::connection('pgsql2')->select($query);
        //$totalRecords = count($data);
      }
      return datatables()
        ->of($data)
        ->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('application_id', function ($data) {
          return $data->getBenidAttribute();
        })
        ->addColumn('ben_id', function ($data) {
          return $data->id;
        })
        ->addColumn('ben_name', function ($data) {
          return $data->getName();
        })
        ->addColumn('ben_fname', function ($data) {
          return $data->getFatherName();
        })
        ->addColumn('old_beneficiary_id', function ($data) {
          return $data->old_beneficiary_id;
        })
        ->addColumn('bank_ifsc', function ($data) {
          return $data->bank_ifsc;
        })
        ->addColumn('bank_code', function ($data) {
          return $data->bank_code;
        })
        ->addColumn('bank_name', function ($data) {
          return $data->bank_name;
        })
        ->addColumn('branch_name', function ($data) {
          return $data->branch_name;
        })
        ->addColumn('village_town_city', function ($data) {
          return $data->village_town_city;
        })
        ->addColumn('action', function ($data) {
          $val = '';
          //'<div class="btn-group" role="group" >';
          $val = $val . '<button class="btn btn-primary ben_view_button">View</button>';
          // $val = $val . '<button class="btn btn-warning ben_reject_button">Reject</button>';
          return $val;
        })
        ->rawColumns(['ben_id', 'id', 'ben_name', 'old_beneficiary_id', 'bank_ifsc', 'bank_code', 'village_town_city', 'action'])
        ->make(true);
    }
    return view('singlestep.report')->with('district_name', $district_name)->with('district_code', $district_code);
  }





  public function bulkLocationChange(Request $request)
  {
    //set_time_limit(0);
    ini_set('max_execution_time', 180);
    $return_status = 1;
    $return_msg = '';


    $inputs_json = $request->selectedApplication;
    $location_type = $request->location_type;
    $location_code = $request->location_code;
    $location_name = trim($request->location_name);
    $scheme = $request->scheme;

    $level = "";
    if ($location_type == 2) {
      $block_ulb = Taluka::where('block_code', '=', $location_code)->first();
      $n_created_by_local_body_code = $block_ulb->block_code;
      $level = "Block";
    } else if ($location_type == 1) {
      $level = "SubDiv";
      $block_ulb = UrbanBody::where('urban_body_code', '=', $location_code)->first();
      $n_created_by_local_body_code = $block_ulb->sub_district_code;
    }

    $inputs = json_decode($inputs_json, true);
    $modelName = $this->getModelName($scheme);

    if ($modelName::whereIn('id', $inputs)->update([
      'block_ulb_code' => $location_code,
      'block_ulb_name' => $location_name,
      'block_ulb_type' => $location_type,
      'rural_urban_id' => $location_type
      // 'created_by_local_body_code' => $n_created_by_local_body_code
    ])) {
      $return_status = 1;
      $return_msg = 'Location change of beneficiaries successful';
    } else {
      $return_status = 0;
      $return_msg = 'Error Occur..please try again.';
    }

    return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
  }
  public function briefdataApproval(Request $request)
  {
    $rules = [
      'type_of_penstion' => 'required|in:1,2,3',
      'first_name' => 'required|string|max:200',
      'middle_name' => 'string|nullable',
      'last_name' => 'required|string|max:200',
      'district' => 'required|numeric',
      'urban_code' => 'required|in:1,2',
      'block' => 'required|numeric',
      'gp_ward' => 'required|numeric',
      'bank_account_number' => 'required|numeric',
      'bank_ifsc_code' => 'required|string'
    ];
    $attributes = array();
    $messages = array();
    $attributes['type_of_penstion'] = 'Type of Pension';
    $attributes['first_name'] = 'First Name';
    $attributes['middle_name'] = 'Middle Name';
    $attributes['last_name'] = 'Last Name';
    $attributes['mobile_no'] = 'Mobile Number';
    $attributes['district'] = 'District';
    $attributes['urban_code'] = 'Rural/ Urban';
    $attributes['block'] = 'Block/Municipality/Corp';
    $attributes['gp_ward'] = 'GP/Ward No';
    $attributes['bank_ifsc_code'] = 'IFS Code';
    $attributes['bank_account_number'] = 'Bank Account Number';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $type_of_penstion = $request->type_of_penstion;
      $scheme_id = 0;
      if ($type_of_penstion == 1) {
        $scheme_id = 10;
        $pension_details = 'App\\PensionOAPWCD';
      } else if ($type_of_penstion == 3) {
        $scheme_id = 11;
        $pension_details = 'App\\PensionWPWCD';
      } else {
        return redirect("/")->with('success', 'Scheme Not Valid');
      }
      $scheme_capacity_arr = array();
      $distCode = $request->session()->get('distCode');
      $scheme_capacity_arr = Helper::getCapacity($scheme_id, $distCode);
      if ($scheme_capacity_arr['visible'] == 1) {
        if ($scheme_capacity_arr['total_data'] > $scheme_capacity_arr['capacity']) {
          $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
          $return_status = 0;
          $return_msg = array("" . $errorMsgCap);
          return response()->json([
            'return_status' => $return_status,
            'return_msg' => $return_msg
          ]);
        }
      }
      $bank_ifsc_code = $request->bank_ifsc_code;
      $bank_account_number = $request->bank_account_number;
      $bank_details = BankDetails::where('ifsc', $bank_ifsc_code)->get(['bank', 'branch'])->first();
      if (!empty($bank_details)) {
        $bank_name = $bank_details->bank;
        $bank_branch = $bank_details->branch;

        $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
                //dump($roleArray);
        //dump($request->urban_code);
        foreach ($roleArray as $roleObj) {
          if ($roleObj['scheme_id'] == $scheme_id) {
            $is_active = 1;
            $mapping_level = $roleObj['mapping_level'];
            $district_code = $roleObj['district_code'];
            if ($roleObj['is_urban'] == 1) {
              $blockCode = $roleObj['urban_body_code'];
            } else {
              $blockCode = $roleObj['taluka_code'];
            }
            break;
          }
        }
        // dump($mapping_level);
        // dump($district_code);
        // dd($blockCode);
        if ($is_active == 0) {
          $return_status = 0;
          $return_text = "You are not allowed to do this operation";
          $return_msg = array("" . $return_text);
        } else {
          $applicant_id = $request->applicant_id;
          $applicant_id = substr($applicant_id, -7);

          try {
            $row = $pension_details::where('id', $applicant_id)
              ->where('created_by_dist_code', '=', $district_code)
              ->where('legacy_import', true)
              ->first();
            $return_status = 1;
            $return_text = "Applicant found";
            $return_msg = array("" . $return_text);
            $first_name = $request->first_name;
            $middle_name = $request->middle_name;
            $last_name = $request->last_name;
            $mobile_no = $request->mobile_no;
            $district = $request->district;
            $urban_code = $request->urban_code;
            $block = $request->block;
            $gp_ward = $request->gp_ward;
            $dob = $request->dob;
            $dob_valid = 0;
            if (!empty($dob)) {
              $d1 = new DateTime('2020-01-01');
              $d2 = new DateTime($dob);
              $diff = $d2->diff($d1);
              $date_diff = $diff->y;
              if ($scheme_id == 10 && $date_diff < 60) {
                $dob_valid = 0;
              } else {
                if ($dob > '2020-01-01') {
                  $dob_valid = 0;
                } else
                  $dob_valid = 1;
              }
            } else {
              $dob = NULL;
              $date_diff = $request->txt_age;
              if (!empty($date_diff)) {
                if ($date_diff < 0) {
                  $dob_valid = 0;
                } else {
                  if ($scheme_id == 10 && $date_diff < 60) {
                    $dob_valid = 0;
                  } else
                    $dob_valid = 1;
                }
              } else {
                $dob_valid = 1;
              }
            }
            if ($dob_valid) {
              if ($request->urban_code == 1) {
                $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
                $gp_ward_arr = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();
                $n_created_by_local_body_code = $block_ulb->sub_district_code;
                $block_ulb_name = $block_ulb->urban_body_name;
                $gp_ward_name   = $gp_ward_arr->urban_body_ward_name;
              } else {
                $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
                $gp_ward_arr = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();
                $block_ulb_name = $block_ulb->block_name;
                $gp_ward_name   = $gp_ward_arr->gram_panchyat_name;
                $n_created_by_local_body_code = $request->block;
              }
              if (!empty($request->mobile_no)) {
                $mobile_no = $request->mobile_no;
              } else {
                $mobile_no = "1000000000";
              }
              if ($row->created_by_dist_code == $district) {
                $u_created_by_dist_code = $row->created_by_dist_code;
              } else {
                $u_created_by_dist_code = $district;
              }
              if ($row->created_by_local_body_code == $n_created_by_local_body_code) {
                $u_created_by_local_body_code = $row->created_by_local_body_code;
              } else {
                $u_created_by_local_body_code = $n_created_by_local_body_code;
              }
              $input = [
                'ben_fname' => $first_name,
                'ben_mname' => $middle_name,
                'ben_lname' => $last_name,
                'mobile_no' => $mobile_no,
                'dist_code' => $district,
                'rural_urban_id' => $urban_code,
                'block_ulb_code' => $block,
                'block_ulb_name' => $block_ulb_name,
                'gp_ward_code' => $gp_ward,
                'gp_ward_name' => $gp_ward_name,
                'bank_name' => $bank_name,
                'branch_name' => $bank_branch,
                'bank_code' => $bank_account_number,
                'dob' => $dob,
                'ben_age' => intval($date_diff),
                'bank_ifsc' => $bank_ifsc_code,
                'next_level_role_id' => 0
                //'created_by_dist_code' => $u_created_by_dist_code,
                // 'created_by_local_body_code' => $u_created_by_local_body_code
              ];

              $is_saved = $pension_details::whereDate('created_at', '>=', '2021-02-02')->where('legacy_import', TRUE)->where("id", $applicant_id)->update($input);
              if ($is_saved) {
                $return_status = 1;
                $return_text = "Applicant (" .  $row->getBenidAttribute() . ") Information Updated and Approved Successfully";
                $return_msg = array("" . $return_text);
              } else {
                $return_status = 0;
                $return_text = "Applicant not found";
                $return_msg = array("" . $return_text);
              }
            } else {
              $return_status = 0;
              $return_text = "Dob Not Valid";
              $return_msg = array("" . $return_text);
            }
          } catch (\Exception $e) {
            $return_status = 0;
            $return_text = "Applicant not found";
            $return_msg = array("" . $return_text);
          }
        }
      } else {
        $return_status = 0;
        $return_text = "Bank IFSC not found";
        $return_msg = array("" . $return_text);
      }
    } else {
      $return_status = 0;
      $return_msg = $validator->errors()->all();
    }
    return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg]);
  }
}
