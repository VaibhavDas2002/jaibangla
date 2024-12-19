<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\District;
use App\SubDistrict;
use App\Taluka;
use App\UrbanBody;
use App\Scheme;
use App\Department;
use App\Designation;
use Validator;
use App\Helpers\AuthChecker;


class GetOtp extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  public function index(Request $request)
  {
    $this->middleware('auth');
    $user_id = AuthChecker::getUserId();
    $designation_id_old = Auth::user()->designation_id_old;
    if ($designation_id_old != 'Admin') {
      return redirect('/')->with('error', 'Payment Mode Not Valid');
    }
    $district_list = Cache::rememberForever('master_districts', function () {
      return District::select(
        'id',
        'district_code',
        'district_name',
        'rch_district_code',
        'is_revenue_district',
        'state_code',
        'district_status'
      )->get();
    });
    $schemes = Scheme::where('is_active', 1)->get(['scheme_name', 'id']);
    $departments = Department::get(['name', 'id']);
    $designations = Designation::get(['name', 'id']);
    return view(
      'GetOtp.index',
      [
        'designations' => $designations,
        'schemes' => $schemes,
        'departments' => $departments,
        'districts' => $district_list
      ]
    );
  }
  function getData(Request $request)
  {
    $heading_msg = '';
    $user_msg = '';
    $lot_yes = 1;
    $mobile_no = $request->mobile_no;
    $department_id = $request->department_id;
    $designation_id_old = $request->designation_id_old;
    $scheme_id = $request->scheme_id;
    $district = $request->district;
    $urban_code = $request->urban_code;
    $block = $request->block;
    $errors = array();
    $data = array();
    $inValid = 0;
    $return_status = 1;
    $fileter_status = 1;
    $result = array();
    if (!empty($mobile_no)) {
      $mob = "/^[1-9][0-9]*$/";
      if (!preg_match($mob, $mobile_no)) {
        $inValid = 1;
        array_push($errors, "Please Enter Valid Mobile Number");
        $return_status = 0;
      } else {
        $user_msg = $user_msg . ' with Mobile Number:' . $mobile_no;
        $inValid = 0;
        $return_status = 1;
        $fileter_status = 1;
        $query = "select mobile_no,login_otp,username,otp_time,designation_id_old,email from users   where mobile_no='" . $mobile_no . "'";
      }
    } else {
      if (empty($designation_id_old)) {
        $inValid = 1;
        array_push($errors, "Please Select Designation");
        $return_status = 0;
      } else {
        $user_msg = $user_msg . ' Designation:' . $designation_id_old;
        if (empty($department_id)) {
          if (in_array($designation_id_old, array('Operator', 'Verifier'))) {
            if (empty($scheme_id)) {
              $inValid = 1;
              array_push($errors, "Please Select Scheme");
              $return_status = 0;
            } else {
              $scheme_row = Scheme::where('id', $scheme_id)->first();
              $user_msg = $user_msg . ' ,Scheme:' . $scheme_row->scheme_name;
            }
            if (empty($district)) {
              $inValid = 1;
              array_push($errors, "Please Select District");
              $return_status = 0;
            } else {
              $district_row = District::where('district_code', $district)->first();
              $user_msg = $user_msg . ' ,District:' . $district_row->district_name;
            }
            if (empty($urban_code)) {
              $inValid = 1;
              array_push($errors, "Please Select Rural/ Urban");
              $return_status = 0;
            } else {
              if ($urban_code == 1)
                $urban_code_text = "Urban";
              else
                $urban_code_text = "Rural";
              $user_msg = $user_msg . ' ,Rural/ Urban:' . $urban_code_text;
            }
            if (empty($block)) {
              $inValid = 1;
              array_push($errors, "Please Select Block/Subdivision");
              $return_status = 0;
            } else {
              if ($urban_code == 1) {
                $sdo_row = SubDistrict::where('sub_district_code', $block)->first();
                $user_msg = $user_msg . ' ,SubDivision:' . $sdo_row->sub_district_name;
              } else {
                $block_row = Taluka::where('block_code', $block)->first();
                $user_msg = $user_msg . ' ,Block:' . $block_row->block_name;
              }
            }
          } else if ($designation_id_old == 'Approver') {
            if (empty($scheme_id)) {
              $inValid = 1;
              array_push($errors, "Please Select Scheme");
              $return_status = 0;
            } else {
              $scheme_row = Scheme::where('id', $scheme_id)->first();
              $user_msg = $user_msg . ' ,Scheme:' . $scheme_row->scheme_name;
            }
            if (empty($district)) {
              $inValid = 1;
              array_push($errors, "Please Select District");
              $return_status = 0;
            } else {
              $district_row = District::where('district_code', $district)->first();
              $user_msg = $user_msg . ' ,District:' . $district_row->district_name;
            }
          } else {
            $inValid = 0;
          }
        } else {
          $dept_row = Department::where('id', $department_id)->first();
          $user_msg = $user_msg . ' ,Department:' . $dept_row->name;
          $inValid = 0;
        }
      }
      if ($inValid == 0) {
        if (!empty($department_id)) {
          $department_condition = " and department_id=" . $department_id;
        } else
          $department_condition = "";
        if (!empty($scheme_id)) {
          $scheme_condition = " and scheme_id=" . $scheme_id;
        } else
          $scheme_condition = "";
        if (!empty($district)) {
          $district_condition = " and district_code=" . $district;
        } else
          $district_condition = "";
        if (!empty($urban_code)) {
          $urban_condition = " and is_urban=" . $urban_code;
        } else
          $urban_condition = "";
        if (!empty($block)) {
          if ($urban_code == 1) {
            $block_condition = " and urban_body_code=" . $block;
          } else
            $block_condition = " and taluka_code=" . $block;
        } else
          $block_condition = "";
        $query = "select A.mobile_no,A.login_otp,A.username,A.otp_time,A.email,A.designation_id_old,
        B.district_code,B.is_urban,B.urban_body_code,B.taluka_code,B.mapping_level,B.scheme_id,
        C.department_id
        from users as A 
        LEFT JOIN duty_assignement as B ON A.id=B.user_id
        LEFT JOIN employees as C ON A.emp_id=C.id
        where A.designation_id_old='" . $designation_id_old . "' and B.is_active=1 " . $department_condition . " " . $scheme_condition . "" . $district_condition . "" . $urban_condition . "" . $block_condition . "";
      }
    }
    if ($inValid == 0) {
      $data_part = DB::connection('pgsql_mis')->select($query);
      $data = array_merge($data, $data_part);

      $i = 0;
      //dd($data);
      foreach ($data as $row) {
        $result[$i]['mobile_no'] = $row->mobile_no;
        $result[$i]['login_otp'] = $row->login_otp;
        $result[$i]['username'] = $row->username;
        $result[$i]['otp_time'] = $row->otp_time;
        $result[$i]['email'] = $row->email;
        $result[$i]['designation_id_old'] = $row->designation_id_old;
        if ($row->designation_id_old == 'Admin') {
          $result[$i]['scheme_name'] = 'NA';
          $result[$i]['district_name'] = 'NA';
          $result[$i]['rural_urban_name'] = 'NA';
          $result[$i]['block_subdiv_name'] = 'NA';
          $result[$i]['department_name'] = 'NA';
        } else {
          $scheme_row_1 = Scheme::where('id', $row->scheme_id)->first();
          if (!empty($scheme_row_1->scheme_name)) {
            $result[$i]['scheme_name'] = $scheme_row_1->scheme_name;
          } else {
            $result[$i]['scheme_name'] = '';
          }
          if ($row->mapping_level == 'Department') {
            // dd('ok');
            $dept_row_1 = Department::where('id', $row->department_id)->first();
            if (!empty($dept_row_1->name)) {
              $result[$i]['department_name'] = $dept_row_1->name;
            } else {
              $result[$i]['department_name'] = '';
            }
            $result[$i]['district_name'] = 'NA';
            $result[$i]['rural_urban_name'] = 'NA';
            $result[$i]['block_subdiv_name'] = 'NA';
          } else {
            if (strtoupper(trim($row->mapping_level)) == 'DISTRICT') {
              $district_row_1 = District::where('district_code', $row->district_code)->first();
              if (!empty($district_row_1->district_name)) {
                $result[$i]['district_name'] = $district_row_1->district_name;
              } else {
                $result[$i]['district_name'] = '';
              }
              $result[$i]['rural_urban_name'] = 'NA';
              $result[$i]['block_subdiv_name'] = 'NA';
            } else if (strtoupper(trim($row->mapping_level)) == 'BLOCK' || strtoupper(trim($row->mapping_level)) == 'SUBDIV') {
              //dd($row->district_code);
              if ($row->is_urban == 1) {
                $result[$i]['rural_urban_name'] = 'Urban';
              } else
                $result[$i]['rural_urban_name'] = 'Rural';
              $district_row_1 = District::where('district_code', $row->district_code)->first();
              if (!empty($district_row_1->district_name)) {
                $result[$i]['district_name'] = $district_row_1->district_name;
              } else {
                $result[$i]['district_name'] = '';
              }
              if ($row->is_urban == 1) {
                $sdo_row_1 = SubDistrict::where('sub_district_code', $row->urban_body_code)->first();
                if (!empty($sdo_row_1->sub_district_name)) {
                  $result[$i]['block_subdiv_name'] = $sdo_row_1->sub_district_name;
                } else {
                  $result[$i]['block_subdiv_name'] = '';
                }
              } else {
                $block_row_1 = Taluka::where('block_code', $row->taluka_code)->first();
                if (!empty($block_row_1->block_name)) {
                  $result[$i]['block_subdiv_name'] = $block_row_1->block_name;
                } else {
                  $result[$i]['block_subdiv_name'] = '';
                }
              }
            }
            $result[$i]['department_name'] = 'NA';
          }
        }
      }

      $i++;
      $heading_msg = 'List of Users ' . $user_msg;
      $column = "Municipality";
    }
    return response()->json([
      'return_status' => $return_status,
      'return_msg' => $errors,
      'row_data' => $result,
      'heading_msg' => $heading_msg,
      'fileter_status' => $fileter_status
    ]);
  }
}
