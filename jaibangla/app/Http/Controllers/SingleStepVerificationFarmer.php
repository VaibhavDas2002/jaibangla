<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\District;
use Auth;
use App\Configduty;

use App\UrbanBody;
use App\Assembly;
use App\Taluka;
use App\Ward;
use App\GP;
use App\User;
use Redirect;
use Illuminate\Support\Facades\Input;

use App\PensionOAPFarmer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Validator;
use App\BankDetails;
use DateTime;

class SingleStepVerificationFarmer extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->scheme_id = 13;
    $this->scheme_name = "Old age Pension for Farmer";
    $this->model_name = "App\PensionOAPFarmer";
    $this->middleware(function ($request, $next) {
      $app_type = $request->app_type;
      //dd($app_type);
      $this->app_type = $app_type;
      $roleArray = $request->session()->get('role');
      if (!empty($roleArray)) {
        foreach ($roleArray as $roleObj) {
          if ($roleObj['scheme_id'] == $this->scheme_id) {
            $is_active = 1;
            $is_urban = $roleObj['is_urban'];
            $mapping_level = $roleObj['mapping_level'];
            $district_code = $roleObj['district_code'];
            if ($roleObj['is_urban'] == 1) {
              $created_by_local_body_code = $roleObj['urban_body_code'];
            } else {
              $created_by_local_body_code = $roleObj['taluka_code'];
            }
            break;
          }
        }



        if (empty($district_code) || empty($created_by_local_body_code)) {
          return redirect('/')->with('error', 'User Disabled for this scheme ' . $this->scheme_name);
        }
        if (empty($is_urban))
          $is_urban = $is_urban;
        $this->district_code = $district_code;
        $this->is_urban = $is_urban;
        $this->created_by_local_body_code = $created_by_local_body_code;
        $designation_id_old = Auth::user()->designation_id_old;
        $this->designation_id_old = $designation_id_old;
        $user_id = Auth::user()->id;
        $this->user_id = $user_id;
      }
      return $next($request);
    });
  }
  public function index()
  {
    $app_type =  $this->app_type;
    $scheme_id =  $this->scheme_id;
    $scheme_name =  $this->scheme_name;
    $designation_id_old = $this->designation_id_old;
    $district_code = $this->district_code;
    $is_urban = $this->is_urban;
    $created_by_local_body_code = $this->created_by_local_body_code;
    $is_subdiv = 0;
    $scheme_name =  $this->scheme_name;
    $designation_id_old = $this->designation_id_old;
    if ($designation_id_old != 'Verifier') {
      return redirect('/')->with('error', 'Not Authorized for the scheme ' . $this->scheme_name);
    }


    if (empty($district_code) || empty($created_by_local_body_code)) {
      return redirect('/')->with('error', 'User Disabled for this scheme ' . $this->scheme_name);
    } else {
      $districts = District::all();

      if ($is_urban == 1) {
        $is_subdiv = 1;
      } else
        $is_subdiv = 0;
    }

    return view('singlestepFarmer.index')
      ->with('district_code', $district_code)
      ->with('scheme_id', $scheme_id)
      ->with('is_subdiv', $is_subdiv)
      ->with('app_type', $app_type)
      ->with('districts', $districts)
      ->with('scheme_name', $scheme_name);
  }


  public function getData(Request $request)
  {
    $app_type =  $this->app_type;
    $scheme_id =  $this->scheme_id;
    $scheme_name =  $this->scheme_name;
    $designation_id_old = $this->designation_id_old;
    $district_code = $this->district_code;
    $is_urban = $this->is_urban;
    $created_by_local_body_code = $this->created_by_local_body_code;
    if ($designation_id_old == 'Verifier' && !empty($district_code) && !empty($created_by_local_body_code)) {
      $serachvalue = $request->search['value'];

      $model_name = $this->model_name;


      $totalRecords = 0;
      $data = array();

      // WORKING QUERY

      $limit = $request->input('length');
      $offset = $request->input('start');
      $condition = array();
      $condition["created_by_dist_code"] = $district_code;
      $condition["created_by_local_body_code"] = $created_by_local_body_code;



      if (empty($serachvalue)) {
        $data = array();
        $data = $model_name::where($condition)->where(function ($query) use ($app_type) {
          if ($app_type == 'P') {
            $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
          } else if ($app_type == "A") {
            $query->where('next_level_role_id', 0);
          } else if ($app_type == "R") {
            $query->where('is_rejected', 1);
          }
        })->orderBy('ben_fname', 'ASC')->offset($offset)->limit($limit)->get([
          'id',
          'bank_code', 'bank_name', 'branch_name', 'ben_fname', 'ben_mname', 'ben_lname', 'father_fname', 'father_mname', 'father_lname', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
          'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
        ]);
        $totalRecords = $model_name::where($condition)->where(function ($query) use ($app_type) {
          if ($app_type == 'P') {
            $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
          } else if ($app_type == "A") {
            $query->where('next_level_role_id', 0);
          } else if ($app_type == "R") {
            $query->where('is_rejected',1);
          }
        })->count();
        $filterRecords = count($data);
      } else {
        $data = array();
        if (is_numeric($serachvalue)) {
          $ben_id = substr($serachvalue, -7);
          $query = $model_name::where(function ($query) use ($app_type) {
            if ($app_type == 'P') {
              $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
            } else if ($app_type == "A") {
              $query->where('next_level_role_id', 0);
            } else if ($app_type == "R") {
              $query->where('is_rejected',1);
            }
          })->where(function ($query1) use ($ben_id, $serachvalue) {
            $query1->where('id', $ben_id)
              ->orWhere('bank_code', $serachvalue);
          })->where($condition);
          $totalRecords =  $query->count();
          $data = $query->orderBy('ben_fname', 'ASC')->offset($offset)->limit($limit)->get([
            'id',
            'bank_code', 'bank_name', 'branch_name', 'ben_fname', 'ben_mname', 'ben_lname', 'father_fname', 'father_mname', 'father_lname', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
            'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
          ]);
          $filterRecords = $totalRecords;
        } else {
          $query = $model_name::where(function ($query) use ($app_type) {
            if ($app_type == 'P') {
              $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
            } else if ($app_type == "A") {
              $query->where('next_level_role_id', 0);
            } else if ($app_type == "R") {
              $query->where('is_rejected', 1);
            }
          })->where(function ($query1) use ($serachvalue) {
            $query1->where('ben_fname', 'like', $serachvalue . '%')
              ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
              ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
              ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
          })->where($condition);
          $totalRecords =  $query->count();
          $data = $query->orderBy('ben_fname', 'ASC')->offset($offset)->limit($limit)->get([
            'id',
            'bank_code', 'bank_name', 'branch_name', 'ben_fname', 'ben_mname', 'ben_lname', 'father_fname', 'father_mname', 'father_lname', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
            'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
          ]);
          $filterRecords = $totalRecords;
        }

        //$filterRecords = $totalRecords = count($data);
        //$data = DB::connection('pgsql2')->select($query);
        //$totalRecords = count($data);
      }
      return datatables()
        ->of($data)
        ->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('check', function ($data) use ($app_type) {
          if ($app_type == "P")
            return '<input type="checkbox" name="approvalcheck[]" onchange="controlCheckBox();" value="' . $data->id . '">';
          else
            return '';
        })
        ->addColumn('application_id', function ($data) {
          return $data->getBenidAttribute();
        })
        ->addColumn('ben_id', function ($data) {
          return $data->id;
        })
        ->addColumn('ben_fullname', function ($data) {
          // dd(trim($data->getName()));
          return $data->getName();
        })
        ->addColumn('benf_name', function ($data) {
          // dd(trim($data->getName()));
          return $data->getFatherName();
        })
        ->addColumn('bank_ifsc', function ($data) {
          return trim($data->bank_ifsc);
        })
        ->addColumn('bank_code', function ($data) {
          return trim($data->bank_code);
        })
        ->addColumn('block_ulb_name', function ($data) {
          return trim($data->block_ulb_name);
        })
        ->addColumn('gp_ward_name', function ($data) {
          return trim($data->gp_ward_name);
        })
        ->addColumn('village_town_city', function ($data) {
          return $data->village_town_city;
        })
        ->addColumn('action', function ($data) use ($app_type) {
          $val = '<button class="btn btn-primary ben_view_button">View</button>';
          if (($app_type == 'P' || $app_type == 'A') && $data->lot_generated == 0)
            $val = $val . '<button class="btn btn-warning ben_reject_button">Reject</button>';
          if (($app_type == 'A') && $data->lot_generated == 0)
            $val = $val . '<button class="btn btn-info ben_revert_button">Revert</button>';
          if ($app_type == 'P'  && $data->lot_generated == 0)
            $val = $val . '<button class="btn btn-success" id="edit_' . $data->id . '" onclick="editApproveModal(' . $data->id . ')">Edit&Approve</button>';
          return $val;
        })
        ->rawColumns(['check', 'ben_id', 'id', 'ben_name', 'old_beneficiary_id', 'bank_ifsc', 'bank_code', 'village_town_city', 'action'])
        ->make(true);
    } else {
      return redirect('/')->with('success', 'User Disabled for this scheme');
    }
  }



  public function bulkApprove(Request $request)
  {
    ini_set('max_execution_time', 180);
    $scheme_id =  $this->scheme_id;
    $scheme_name =  $this->scheme_name;
    $designation_id_old = $this->designation_id_old;
    $district_code = $this->district_code;
    $is_urban = $this->is_urban;
    $created_by_local_body_code = $this->created_by_local_body_code;
    if ($designation_id_old == 'Verifier') {

      $return_status = 0;
      $return_msg = '';
      $inputs_json = $request->approvalcheck;
      $inputs = json_decode($inputs_json, true);
      $id_in = array();
      foreach ($inputs as $input) {
        array_push($id_in, $input);
      }
      $model_name = $this->model_name;
      DB::beginTransaction();
      try {
        $input_update = ['next_level_role_id' => '0'];
        $model_name::where('created_by_dist_code', $district_code)->where('created_by_local_body_code', $created_by_local_body_code)->whereIn('id', $id_in)->where(function ($query) {
          $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
        })->update($input_update);
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
    $app_type =  $this->app_type;
    $scheme_id =  $this->scheme_id;
    $scheme_name =  $this->scheme_name;
    $designation_id_old = $this->designation_id_old;
    $district_code = $this->district_code;
    $is_urban = $this->is_urban;
    $created_by_local_body_code = $this->created_by_local_body_code;
    $return_status = 0;
    $return_msg = '';

    if ($designation_id_old == 'Verifier') {
      $ben_id = $request->ben_id;
      $revert_reject = $request->revert_reject;
      if ($revert_reject == 1)
        $next_level_role_id = -1;
      else if ($revert_reject == 2)
        $next_level_role_id = NULL;
      DB::beginTransaction();
      try {
        $model_name = $this->model_name;
        $input_update = ['next_level_role_id' => $next_level_role_id];
        $model_name::where('created_by_dist_code', $district_code)
          ->where('created_by_local_body_code', $created_by_local_body_code)->where('id', $ben_id)->where(function ($query) use ($app_type) {
            if ($app_type == 'P') {
              $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
            } else if ($app_type == 'A') {
              $query->where('next_level_role_id', 0);
            }
          })->update($input_update);
        DB::commit();
        $return_status = 1;
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










  public function getApplicantRow(Request $request)
  {

    $applicant_row = array();
    $rules = array(
      'applicant_id' => 'required|numeric',
    );
    $attributes = [
      'applicant_id' => 'Applicant Id',
    ];
    $messages = [
      'required' => 'The :attribute field is required.',
      'numeric' => 'Only integer allowed for :attribute',
      'in' => 'The :attribute field not valid.',
    ];
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $scheme_id = $this->scheme_id;
      $pension_details = $this->model_name;
      $is_active = 0;

      $scheme_id =  $this->scheme_id;
      $scheme_name =  $this->scheme_name;
      $designation_id_old = $this->designation_id_old;
      $district_code = $this->district_code;
      $is_urban = $this->is_urban;
      $created_by_local_body_code = $this->created_by_local_body_code;
      if (empty($created_by_local_body_code)) {
        $return_status = 0;
        $return_text = "You are not allowed to do this operation";
        $return_msg = array("" . $return_text);
      } else {
        $applicant_id = $request->applicant_id;
        // dd($applicant_id);

        try {
          $row = $pension_details::where('id', $applicant_id)
            ->where('created_by_dist_code', '=', $district_code)->where('created_by_local_body_code', $created_by_local_body_code)
            ->where(function ($query) {
              $query-whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
            })->first();
          // dd($row);
          $return_status = 1;
          $applicant_row['first_name'] = trim($row->ben_fname);
          $applicant_row['middle_name'] = trim($row->ben_mname);
          $applicant_row['last_name'] = trim($row->ben_lname);
          $applicant_row['mobile_no'] = trim($row->mobile_no);
          $applicant_row['dob'] = $row->dob;
          $applicant_row['ben_age'] = $row->ben_age;
          $applicant_row['district'] = $row->dist_code;
          $applicant_row['urban_code'] = $row->rural_urban_id;
          $applicant_row['block'] = $row->block_ulb_code;
          $applicant_row['gp_ward'] = $row->gp_ward_code;
          $applicant_row['bank_ifsc_code'] = trim($row->bank_ifsc);
          $applicant_row['name_of_bank'] = $row->name_of_bank;
          $applicant_row['bank_branch'] = $row->bank_branch;
          $applicant_row['bank_account_number'] = trim($row->bank_code);
          $return_text = "Applicant found";
          $return_msg = array("" . $return_text);
        } catch (\Exception $e) {
          $return_status = 0;
          $return_text = "Applicant not found";
          $return_msg = array("" . $return_text);
        }
      }
    } else {
      $return_status = 0;
      $return_msg = $validator->errors()->all();
    }
    return response()->json(['return_status' => $return_status, 'return_msg' => $return_msg, 'applicant_row' => $applicant_row]);
  }
  public function farmerApproval(Request $request)
  {
    $rules = [
      'first_name' => 'required|string|max:200',
      'middle_name' => 'string|nullable',
      'last_name' => 'required|string|max:200',
      'father_first_name' => 'required|string|max:200',
      'father_middle_name' => 'string|nullable',
      'father_last_name' => 'required|string|max:200',
      'district' => 'nullable|numeric',
      'urban_code' => 'nullable|in:1,2',
      'block' => 'nullable|numeric',
      'gp_ward' => 'nullable|numeric',
      'bank_account_number' => 'required|numeric',
      'bank_ifsc_code' => 'required|string'
    ];
    $attributes = array();
    $messages = array();
    $attributes['first_name'] = 'First Name';
    $attributes['middle_name'] = 'Middle Name';
    $attributes['last_name'] = 'Last Name';
    $attributes['father_first_name'] = 'Father First Name';
    $attributes['father_middle_name'] = 'Father Middle Name';
    $attributes['father_last_name'] = 'Father Last Name';
    $attributes['mobile_no'] = 'Mobile Number';
    $attributes['district'] = 'District';
    $attributes['urban_code'] = 'Rural/ Urban';
    $attributes['block'] = 'Block/Municipality/Corp';
    $attributes['gp_ward'] = 'GP/Ward No';
    $attributes['bank_ifsc_code'] = 'IFS Code';
    $attributes['bank_account_number'] = 'Bank Account Number';
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);
    if ($validator->passes()) {
      $pension_details = $this->model_name;
      $bank_ifsc_code = $request->bank_ifsc_code;
      $bank_account_number = $request->bank_account_number;
      $bank_details = BankDetails::where('ifsc', $bank_ifsc_code)->get(['bank', 'branch'])->first();
      if (!empty($bank_details)) {
        $bank_name = $bank_details->bank;
        $bank_branch = $bank_details->branch;

        $scheme_id =  $this->scheme_id;
        $scheme_name =  $this->scheme_name;
        $designation_id_old = $this->designation_id_old;
        $district_code = $this->district_code;
        $is_urban = $this->is_urban;
        $created_by_local_body_code = $this->created_by_local_body_code;
        if (empty($created_by_local_body_code)) {
          $return_status = 0;
          $return_text = "You are not allowed to do this operation";
          $return_msg = array("" . $return_text);
        } else {
          $applicant_id = $request->applicant_id;
          // dd($applicant_id);
          try {
            $row = $pension_details::where('id', $applicant_id)
              ->where('created_by_dist_code', '=', $district_code)
              ->where('created_by_local_body_code', '=', $created_by_local_body_code)
              ->where(function ($query) {
                $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
              })->first();
            $return_status = 1;
            $return_text = "Applicant found";
            $return_msg = array("" . $return_text);
            $first_name = $request->first_name;
            $middle_name = $request->middle_name;
            $last_name = $request->last_name;
            $father_first_name = $request->father_first_name;
            $father_middle_name = $request->father_middle_name;
            $father_last_name = $request->father_last_name;
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
              if ($dob > '2020-01-01') {
                $dob_valid = 0;
              } else
                $dob_valid = 1;
            } else {
              $dob = NULL;
              $date_diff = $request->txt_age;
              if (!empty($date_diff)) {
                if ($date_diff < 0) {
                  $dob_valid = 0;
                } else {
                  if ($date_diff < 60) {
                    $dob_valid = 0;
                  } else
                    $dob_valid = 1;
                }
              } else {
                $dob_valid = 1;
              }
            }
            if ($dob_valid) {
              // dd($request->urban_code);
              if (!empty($request->urban_code)) {
                if ($request->urban_code == 1) {
                  if (!empty($request->block)) {
                    $block_ulb = UrbanBody::where('urban_body_code', '=', $request->block)->first();
                    $block_ulb_name = $block_ulb->urban_body_name;
                    $n_created_by_local_body_code = $block_ulb->sub_district_code;
                  } else {
                    $block = NULL;
                    $block_ulb_name = "";
                  }
                  if (!empty($request->gp_ward)) {
                    $gp_ward_arr = Ward::where('urban_body_ward_code', '=', $request->gp_ward)->first();
                    $gp_ward_name   = $gp_ward_arr->urban_body_ward_name;
                  } else {
                    $gp_ward = NULL;
                    $gp_ward_name = "";
                  }
                } else {
                  if (!empty($request->block)) {
                    $block_ulb = Taluka::where('block_code', '=', $request->block)->first();
                    $block_ulb_name = $block_ulb->block_name;
                    $n_created_by_local_body_code = $request->block;
                  } else {
                    $block = NULL;
                    $block_ulb_name = "";
                  }
                  if (!empty($request->gp_ward)) {
                    $gp_ward_arr = GP::where('gram_panchyat_code', '=', $request->gp_ward)->first();
                    $gp_ward_name   = $gp_ward_arr->gram_panchyat_name;
                  } else {
                    $gp_ward = NULL;
                    $gp_ward_name = "";
                  }
                }
              } else {
                $block = NULL;
                $gp_ward = NULL;
                $block_ulb_name = "";
                $gp_ward_name = "";
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
                'ben_fname' => trim($first_name),
                'ben_mname' => trim($middle_name),
                'ben_lname' => trim($last_name),
                'father_fname' => trim($father_first_name),
                'father_mname' => trim($father_middle_name),
                'father_lname' => trim($father_last_name),
                'mobile_no' => $mobile_no,
                'dist_code' => $district,
                'rural_urban_id' => $urban_code,
                'block_ulb_code' => $block,
                'block_ulb_name' => $block_ulb_name,
                'gp_ward_code' => $gp_ward,
                'gp_ward_name' => $gp_ward_name,
                'bank_name' => trim($bank_name),
                'branch_name' => trim($bank_branch),
                'bank_code' => $bank_account_number,
                'dob' => $dob,
                'ben_age' => intval($date_diff),
                'bank_ifsc' => trim($bank_ifsc_code),
                'next_level_role_id' => 0,
                'created_by_dist_code' => $u_created_by_dist_code,
                'created_by_local_body_code' => $u_created_by_local_body_code
              ];

              $is_saved = $pension_details::where('created_by_dist_code', '=', $district_code)
                ->where('created_by_local_body_code', '=', $created_by_local_body_code)
                ->where(function ($query) {
                  $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
                })->where("id", $applicant_id)->update($input);
              if ($is_saved) {
                $return_status = 1;
                $return_text = "Applicant (" . $applicant_id . ") Information Updated and Approved Successfully";
                $return_msg = array("" . $return_text);
              } else {
                $return_status = 0;
                $return_text = "Applicant not found2";
                $return_msg = array("" . $return_text);
              }
            } else {
              $return_status = 0;
              $return_text = "Dob Not Valid";
              $return_msg = array("" . $return_text);
            }
          } catch (\Exception $e) {
            // dd($e);
            $return_status = 0;
            $return_text = "Applicant not found1";
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
  public function approvedlist()
  {
    $scheme_id =  $this->scheme_id;
    $scheme_name =  $this->scheme_name;
    $designation_id_old = $this->designation_id_old;
    $district_code = $this->district_code;
    $is_urban = $this->is_urban;
    $created_by_local_body_code = $this->created_by_local_body_code;
    $is_subdiv = 0;
    $scheme_name =  $this->scheme_name;
    $designation_id_old = $this->designation_id_old;
    if ($designation_id_old != 'Verifier') {
      return redirect('/')->with('error', 'Not Authorized for the scheme ' . $this->scheme_name);
    }


    if (empty($district_code) || empty($created_by_local_body_code)) {
      return redirect('/')->with('error', 'User Disabled for this scheme ' . $this->scheme_name);
    } else {
      $districts = District::all();

      if ($is_urban == 1) {
        $is_subdiv = 1;
      } else
        $is_subdiv = 0;
    }

    return view('singlestepFarmer.approved')
      ->with('district_code', $district_code)
      ->with('scheme_id', $scheme_id)
      ->with('is_subdiv', $is_subdiv)
      ->with('districts', $districts)
      ->with('scheme_name', $scheme_name);
  }
  public function getApprovedData(Request $request)
  {
    $scheme_id =  $this->scheme_id;
    $scheme_name =  $this->scheme_name;
    $designation_id_old = $this->designation_id_old;
    $district_code = $this->district_code;
    $is_urban = $this->is_urban;
    $created_by_local_body_code = $this->created_by_local_body_code;
    if ($designation_id_old == 'Verifier' && !empty($district_code) && !empty($created_by_local_body_code)) {
      $serachvalue = $request->search['value'];

      $model_name = $this->model_name;


      $totalRecords = 0;
      $data = array();

      // WORKING QUERY

      $limit = $request->input('length');
      $offset = $request->input('start');
      $condition = array();
      $condition["next_level_role_id"] = 0;
      $condition["created_by_dist_code"] = $district_code;
      $condition["created_by_local_body_code"] = $created_by_local_body_code;


      if (empty($serachvalue)) {
        $data = array();
        $data = $model_name::where($condition)->where(function ($query) {
          $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
        })->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
          'id',
          'bank_code', 'bank_name', 'branch_name', 'ben_fname', 'ben_mname', 'ben_lname', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
          'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
        ]);
        $totalRecords = $model_name::where($condition)->where(function ($query) {
          $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
        })->count();
        $filterRecords = count($data);
      } else {
        $data = array();
        if (is_numeric($serachvalue)) {
          $ben_id = substr($serachvalue, -7);
          $query = $model_name::where(function ($query) {
            $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
          })->where(function ($query1) use ($ben_id, $serachvalue) {
            $query1->where('id', $ben_id)
              ->orWhere('bank_code', $serachvalue);
          })->where($condition);
          $totalRecords =  $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
            'id',
            'bank_code', 'bank_name', 'branch_name', 'ben_fname', 'ben_mname', 'ben_lname', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
            'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
          ]);
          $filterRecords = $totalRecords;
        } else {
          $query = $model_name::where(function ($query) {
            $query->whereraw("((is_verified=1 and is_approved=0 and is_rejected=0) or next_level_role_id IS NULL)");
          })->where(function ($query1) use ($serachvalue) {
            $query1->where('ben_fname', 'like', $serachvalue . '%')
              ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
              ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
              ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
          })->where($condition);
          $totalRecords =  $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
            'id',
            'bank_code', 'bank_name', 'branch_name', 'ben_fname', 'ben_mname', 'ben_lname', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
            'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
          ]);
          $filterRecords = $totalRecords;
        }

        //$filterRecords = $totalRecords = count($data);
        //$data = DB::connection('pgsql2')->select($query);
        //$totalRecords = count($data);
      }
      return datatables()
        ->of($data)
        ->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('check', function ($data) {
          return '<input type="checkbox" name="approvalcheck[]" onchange="controlCheckBox();" value="' . $data->id . '">';
        })
        ->addColumn('application_id', function ($data) {
          return $data->getBenidAttribute();
        })
        ->addColumn('ben_id', function ($data) {
          return $data->id;
        })
        ->addColumn('ben_fullname', function ($data) {
          // dd(trim($data->getName()));
          return $data->getName();
        })
        ->addColumn('bank_ifsc', function ($data) {
          return trim($data->bank_ifsc);
        })
        ->addColumn('bank_code', function ($data) {
          return trim($data->bank_code);
        })
        ->addColumn('block_ulb_name', function ($data) {
          return trim($data->block_ulb_name);
        })
        ->addColumn('gp_ward_name', function ($data) {
          return trim($data->gp_ward_name);
        })
        ->addColumn('village_town_city', function ($data) {
          return $data->village_town_city;
        })
        ->addColumn('action', function ($data) {
          $val = '<button class="btn btn-primary ben_view_button">View</button>';
          $val = $val . '<button class="btn btn-warning ben_reject_button">Reject</button>';
          // $val = $val . '<button class="btn btn-success ben_edit_approve_button">Edit</button>';
          $val = $val . '<button class="btn btn-success" id="edit_' . $data->id . '" onclick="editApproveModal(' . $data->id . ')">Edit&Approve</button>';
          return $val;
        })
        ->rawColumns(['check', 'ben_id', 'id', 'ben_name', 'old_beneficiary_id', 'bank_ifsc', 'bank_code', 'village_town_city', 'action'])
        ->make(true);
    } else {
      return redirect('/')->with('success', 'User Disabled for this scheme');
    }
  }
}
