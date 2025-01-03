<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Taluka;
use App\District;
use App\GP;
use App\Ward;
use App\BeneficiaryPensions;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\Scheme;
use App\UrbanBody;
use App\SubDistrict;
use App\PensionLegacy;
use App\LegacyApplicationStatus;
use App\BankDetails;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\AuthChecker;

class LegacyProcessController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  public function index(Request $request, $app_type)
  {
    return redirect("/")->with('error', 'Data entry temporary suspended.');
    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id', '=', $user_id)->first();
    $district_code = $duty->district_code;
    $district_name = District::where('district_code', $district_code)->pluck('district_name')->first();

    $user_level = '';
    $user_local_body_code = $district_name;
    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();;
    foreach ($roleArray as $roleObj) {
      //Check scheme for ST           
      if ($roleObj['scheme_id'] == 1) {
        $user_level = $roleObj['mapping_level'];

        if ($roleObj['is_urban'] == 1) {
          $user_local_body_code = $roleObj['urban_body_code'];
        } else {
          $user_local_body_code = $roleObj['taluka_code'];
        }
        break;
      }
    }


    return view('legacy.legacy_processlist')->with('district_name', $district_name)
      ->with('district_code', $district_code)
      ->with('user_level', $user_level)->with('user_local_body_code', $user_local_body_code)
      ->with('app_type', $app_type);
  }
  public function getData(Request $request)
  {
    //DB::enableQueryLog();
    if (request()->ajax()) {
      $user_id = AuthChecker::getUserId();

      $user_level = '';
      $user_local_body_code = '';
      $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
            foreach ($roleArray as $roleObj) {
        //Check scheme for ST           
        if ($roleObj['scheme_id'] == 1) {
          $user_level = $roleObj['mapping_level'];

          if ($roleObj['is_urban'] == 1) {
            $user_local_body_code = $roleObj['urban_body_code'];
          } else {
            $user_local_body_code = $roleObj['taluka_code'];
          }
          break;
        }
      }


      $district_code = $request->level1;
      $district_name = $request->level2;
      $lottype = $request->lottype;
      $serachvalue = $request->search['value'];
      //Application TYPE - 'F':FRESH, 'R':REJECTED, 'A':APPROVED
      $app_type = $request->application_type;

      if (($app_type == 'A') || ($app_type == 'R') || ($app_type == 'F')) {

        //Urban/Rural
        $level = $request->level3;
        //LocalBody
        $localBody = $request->level1a;

        if (empty($level)) {
          $level = $user_level;
          $localBody = $user_local_body_code;
        }

        $status_fresh_verified = 1;


        if ($user_level == 'District') {
          $status_fresh_verified = 2; //Verified application show at District level
        } else {
          $status_fresh_verified = 1; //Fresh application show at Block/ Subdivision level
        }



        $flag = 1;
        $totalRecords = 0;
        $data = array();

        //if(empty($serachvalue)){      
        $status = "";
        if ($app_type == 'F') { //FRESH APPLICATION
          $status = ' where status = ' . $status_fresh_verified;
        } else if ($app_type == 'A') { //APPROVED APPLICATION
          $status = ' where status > ' . $status_fresh_verified . ' and status <6';
        } else if ($app_type == 'R') { //REJECTED APPLICATION
          $status = ' where status > 5';
        } else {
          $status = ' where 1=1';
        }

        if (empty($serachvalue)) {
          //RecordsCount
          $mainquery = " select count(id) from legacy.beneficiary " . $status;
          if (!empty($district_code)) {
            $mainquery = $mainquery . " and created_by_dist_code = " . $district_code;
          }
          if (!empty($level)) {
            //'Rural'

            if (!empty($localBody)) {
              $mainquery = $mainquery . " and created_by_local_body_code = " . $localBody;
            }
          }
          $totalRecordsResult = DB::select($mainquery);


          if ($totalRecordsResult) {
            $totalRecords = $totalRecordsResult[0]->count;
          }
        }

        // WORKING QUERY

        $limit = $request->input('length');
        $offset = $request->input('start');

        $query = "select b.*, s.message, '" . $app_type . "'as app_type from (";

        $mainquery = " select * from legacy.beneficiary " . $status;
        if (!empty($district_code)) {
          $mainquery = $mainquery . " and created_by_dist_code = " . $district_code;
        }
        if (!empty($level)) {
          //'Rural'

          if (!empty($localBody)) {
            $mainquery = $mainquery . " and created_by_local_body_code = " . $localBody;
          }
        }
        $query = $query . $mainquery;
        if (empty($serachvalue)) {
          if ($limit >= 0) {
            $query = $query . " limit " . $limit . " offset " . $offset;
          }
          $query = $query . ") b left join legacy.legacy_status_code s on b.status = s.code order by b.id";
          $data = DB::select($query);
        } else {
          $query = $query . " and id='" . $serachvalue . "'";

          $query = $query . ") b left join legacy.legacy_status_code s on b.status = s.code";

          $data = DB::select($query);
          $totalRecords = count($data);
        }


        return datatables()
          ->of($data)
          ->setTotalRecords($totalRecords)
          ->setFilteredRecords($totalRecords)
          ->skipPaging()
          ->addColumn('check', function ($data) {
            if ($data->app_type == 'F' || $data->app_type == 'L') {
              return '<input type="checkbox" name="approvalcheck[]" onchange="controlCheckBox();" value="' . $data->id . '">';
            } else {
              return '<input type="checkbox" name="approvalcheck[]" onchange="controlCheckBox();" value="' . $data->id . '" disabled>';
            }
          })
          ->addColumn('ben_id', function ($data) {
            return $data->id;
          })
          ->addColumn('new_ben_id', function ($data) {
            if ($data->is_migrated)
              return $data->new_pension_id;
            else
              return '';
          })
          ->addColumn('ben_name', function ($data) {
            return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
          })
          ->addColumn('ben_father', function ($data) {
            return $data->father_fname . ' ' . $data->father_mname . ' ' . $data->father_lname;
          })
          ->addColumn('dob', function ($data) {
            return $data->dob;
          })
          ->addColumn('gender', function ($data) {
            return $data->gender;
          })
          ->addColumn('mob_no', function ($data) {
            return $data->mobile_no;
          })
          ->addColumn('aadhar', function ($data) {
            return $data->aadhar_no;
          })
          ->addColumn('status', function ($data) use ($status_fresh_verified) {
            if ($data->app_type == 'F') {
              if ($status_fresh_verified == 2) {
                return '<span class="label label-primary">VERIFIED</span>';
              } else {
                return '<span class="label label-primary">NEW</span>';
              }
            } elseif ($data->app_type == 'A') {
              return '<span class="label label-success">' . $data->message . '</span>';
            } elseif ($data->app_type == 'R') {
              return '<span class="label label-danger">' . $data->message . '</span>';
            }
          })
          ->addColumn('action', function ($data) {
            $val = '<div class="btn-group" role="group" >';
            $val = $val . '<a class="btn btn-primary" target="_blank" href="application-details-read_only/' . $data->id . '">View</a>';
            if ($data->app_type == 'F') {
              $val = $val . '<button class="btn btn-warning ben_reject_button">Reject</button>';
            }
            $val = $val . '</div>';
            return $val;
          })
          ->rawColumns(['check', 'ben_id', 'ben_name', 'dob', 'gender', 'mob_no', 'aadhar', 'status', 'action'])
          ->make(true);
      }
      return json_encode(array('data' => ''));
      // return; datatables()
      // ->of("")
      // ->setTotalRecords(0)
      // ->setFilteredRecords(0)
      // ->skipPaging();
    }
    return view('legacy.legacy_processlist')->with('district_name', $district_name)->with('district_code', $district_code);
  }

  public function bulkApprove(Request $request)
  {
    return redirect("/")->with('error', 'Data entry temporary suspended.');
    set_time_limit(0);


    $user_level = '';
    $user_local_body_code = '';
    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();

    foreach ($roleArray as $roleObj) {
      //Check scheme for ST           
      if ($roleObj['scheme_id'] == 1) {
        $user_level = $roleObj['mapping_level'];

        if ($roleObj['is_urban'] == 1) {
          $user_local_body_code = $roleObj['urban_body_code'];
        } else {
          $user_local_body_code = $roleObj['taluka_code'];
        }
        break;
      }
    }

    //For Block Subdivision level
    $approved_status = 2;
    //For District level
    if ($user_level == 'District') {
      $approved_status = 3;
    }

    // DB::transaction(function()
    // {
    $user_id = AuthChecker::getUserId();

    $inputs_json = $request->approvalcheck;
    $inputs = json_decode($inputs_json, true);

    $input_update = ['status' => $approved_status];

    PensionLegacy::whereIn('id', $inputs)->where('status', '<', $approved_status)->update($input_update);

    return count($inputs);
  }

  public function rejectApplication(Request $request)
  {
    return redirect("/")->with('error', 'Data entry temporary suspended.');
    $ben_id = $request->ben_id;
    $reject_reason = $request->reject_reason;

    $roleArray = Configduty::where('user_id', $user_id)->where('is_active', 1)->get()->toArray();;
    foreach ($roleArray as $roleObj) {
      //Check scheme for ST           
      if ($roleObj['scheme_id'] == 1) {
        $user_level = $roleObj['mapping_level'];

        if ($roleObj['is_urban'] == 1) {
          $user_local_body_code = $roleObj['urban_body_code'];
        } else {
          $user_local_body_code = $roleObj['taluka_code'];
        }
        break;
      }
    }

    //For Block Subdivision level
    $reject_permission = 2;
    $verification_rejected = 1;
    $approval_rejected = null;
    //For District level
    if ($user_level == 'District') {
      $reject_permission = 3;
      $approval_rejected = 1;
      $verification_rejected = null;
    }


    $input_update = ['status' => $reject_reason, 'verification_rejected' => $verification_rejected, 'approval_rejected' => $approval_rejected];
    PensionLegacy::where('id', $ben_id)->where('status', '<', $reject_permission)->update($input_update);
  }



  public function getStatusCode(Request $request)
  {
    $statusCode = LegacyApplicationStatus::select('code', 'message')->where('code', '>', 5)->get();
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

  public function getBankDetails(Request $request)
  {
    $ifsc = $request->ifsc;
    // $bank_details = BankDetails::where('ifsc', $ifsc)->get(['bank', 'branch'])->first();
    $bank_details = BankDetails::where('ifsc', $ifsc)->where('is_active',1)->get(['bank', 'branch','bank_code'])->first();

    return json_encode($bank_details);
  }

  //MIS Report

  public function getStateReport(Request $request)
  {
    $user_id = AuthChecker::getUserId();
    $duty = Configduty::where('user_id', '=', $user_id)->first();
    $districts = District::select('district_code', 'district_name')->get();
    return view('legacy.consolidate_report')->with('districts', $districts);
  }

  public function getMISData(Request $request)
  {
    $district_code = $request->level1a;
    $rural_urban = $request->level3;

    $query = "";
    $data = array();


    // if($district_code!="" || $district_code != null)
    // {
    //   if($rural_urban != 'Urban'){ // Not Urban
    //     $localdata = array();
    //     $query = "select level, levelname, applied, approved, mandate, payment, rejected, process, generated_on
    //             from sp_consol_report where district_code=".$district_code." and 
    //             level='Rural' order by levelname";
    //     $localdata = DB::connection('pgsql_legacy')->select($query);
    //     $data = array_merge($data,$localdata);
    //   }
    //   if($rural_urban != 'Rural'){ // Not Urban
    //     $localdata = array();
    //     $query = "select level, levelname, applied, approved, mandate, payment, rejected, process, generated_on
    //             from sp_consol_report where district_code=".$district_code." and 
    //             level='Urban' order by levelname";
    //     $localdata = DB::connection('pgsql_legacy')->select($query);
    //     $data = array_merge($data,$localdata);
    //   }
    // }
    // else{

    $query = "select X.level, D.district_name as levelname,X.applied, X.verified, X.approved, X.rejected
        from
        (select 'District' as level, created_by_dist_code as dist_code,
        count(id) as applied,
        count(id) FILTER(WHERE status=2) as verified,
        count(id) FILTER(WHERE status=3) as approved,
        count(id) FILTER(WHERE status>5) as rejected
        from legacy.beneficiary group by created_by_dist_code) X
        left join m_district D on X.dist_code = D.district_code
        order by D.district_name";

    $data = DB::select($query);
    //  }  

    return datatables()->of($data)
      ->make(true);
  }
}
