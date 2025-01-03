<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\District;
use App\Scheme;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Illuminate\Support\Facades\Config;
use App\Configduty;
use Maatwebsite\Excel\Facades\Excel;
use App\DataSourceCommon;
use App\getModelFunc;
use Illuminate\Support\Facades\Crypt;
use App\RejectRevertReason;
use App\AadharDuplicateTrail;
use App\SubDistrict;
use App\Taluka;
use App\DocumentType;
use Illuminate\Support\Facades\Storage;
use App\SchemeDocMap;
use File;
use App\BankDetails;
use App\UrbanBody;
use App\Ward;
use App\GP;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Helpers\AuthChecker;

class wBPdsController extends Controller
{
  protected $source_type;
  protected $ben_status;
  protected $doc_type_id;

  public function __construct()
  {

    // $this->scheme_id = 20;
    $this->source_type = 'ss_nfsa';
    $this->ben_status = -97;
    $this->doc_type_id = 6;
  }

  function selectscheme(Request $request)
  {
    $this->middleware('auth');
    $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray();
    $designation_id = Auth::user()->designation_id;
    $userId = Auth::user()->id;
    $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (2,10,11,1,3,6,5,7,8,9,13,17,19) and  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
    //dd($scheme_list);
    return view(
      'wbpdsmis.selectScheme',
      [
        'scheme_list' => $scheme_list,
        'designation_id' => $designation_id
      ]
    );
  }
  public function drilldistrictwise(Request $request)
  {
    $this->middleware('auth');
    $scheme_id = $request->scheme_id;
    if (empty($scheme_id)) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    if (!ctype_digit($scheme_id)) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    $designation_id = Auth::user()->designation_id;
    if ($designation_id != 'HOD') {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    $userId = Auth::user()->id;
    $duty_obj = Configduty::where('user_id', $userId)->where('scheme_id', $scheme_id)->first();
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    $r_time = date("l jS \of F Y h:i:s A");
    $c_time = time();
    $whereCon = "where 1=1";
    $query = "select A.location_id,A.location_name,
        COALESCE(C.total_applicant,0) as total_applicant, 
        COALESCE(C.total_sent,0) as total_sent, 
        COALESCE(C.total_not_sent,0) as total_not_sent, 
        COALESCE(C.total_received,0) as total_received,
        COALESCE(C.total_not_received,0) as total_not_received,
        COALESCE(C.total_name_same,0) as total_name_same, 
        COALESCE(C.total_name_differ,0) as total_name_differ
        from(
        select district_code as location_id,district_name as location_name
         from public.m_district 
         )
         as A  
        LEFT JOIN
        (select
                    count(1) as total_applicant,
                    count(1) filter(where wbpds_is_sent=1) as total_sent,
                    count(1) filter(where  (aadhar_no IS NOT NULL and trim(aadhar_no)!='' and length(trim(aadhar_no))=12)) as total_not_sent,
                    count(1) filter(where  wbpds_is_sent=1 and wbpds_response_received=1) as total_received,
                    count(1) filter(where wbpds_is_sent=1 and wbpds_response_received IS NULL) as total_not_received,
                    count(1) filter(where wbpds_is_sent=1 and wbpds_response_received=1 and name_is_match=1) as total_name_same,
                    count(1) filter(where wbpds_is_sent=1 and wbpds_response_received=1 and name_is_match IS NULL) as total_name_differ,
                    created_by_dist_code
                    from " . $schema . ". beneficiaries where  next_level_role_id=0 and scheme_id = " . $scheme_id . " group by created_by_dist_code) as C ON A.location_id=C.created_by_dist_code";

    //echo $query;die;
    $result = DB::connection('pgsql_mis')->select($query);
    $heading_msg = "District wise Aadhaar POC WITH WBPDS for the Scheme " . $scheme_obj->scheme_name;
    return view(
      'wbpdsmis.drilldistrictwise',
      [
        'scheme_name' => $scheme_obj->scheme_name,
        'scheme_id' => $scheme_obj->id,
        'heading_msg' => $heading_msg,
        'result' => $result,
        'designation_id' => $designation_id,
        'r_time' => $r_time,
        'c_time' => $c_time
      ]
    );

  }
  public function drillblksubwise(Request $request)
  {
    $this->middleware('auth');
    $scheme_id = $request->scheme_id;
    $designation_id = Auth::user()->designation_id;
    $userId = Auth::user()->id;
    if (empty($scheme_id)) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    if (!ctype_digit($scheme_id)) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    if ($designation_id == 'HOD') {
      $district_code = $request->district_code;
      $backurl = 'drilldownwbpdsdistrictwise?scheme_id=' . $scheme_id . '&district_code=' . $district_code;
    } else if ($designation_id == 'Approver') {
      return redirect("/")->with('error', 'User Disabled. ');
      $duty_obj = Configduty::where('user_id', $userId)->where('scheme_id', $scheme_id)->first();
      $district_code = $duty_obj->district_code;
      $backurl = 'wbpdsaadharScheme?scheme_id=' . $scheme_id;
    } else {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    if (empty($district_code)) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    if (!ctype_digit($district_code)) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    if ($designation_id == 'HOD') {

    } else {
      $duty_obj = Configduty::where('user_id', $userId)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    $r_time = date("l jS \of F Y h:i:s A");
    $c_time = time();
    $district_row = District::where('district_code', $district_code)->first();
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    $whereMain = "where  district_code=" . $district_code;
    $query1 = "select '2' as type,A.location_id,A.location_name,
        COALESCE(C.total_applicant,0) as total_applicant, 
        COALESCE(C.total_sent,0) as total_sent, 
        COALESCE(C.total_not_sent,0) as total_not_sent, 
        COALESCE(C.total_received,0) as total_received,
        COALESCE(C.total_not_received,0) as total_not_received,
        COALESCE(C.total_name_same,0) as total_name_same, 
        COALESCE(C.total_name_differ,0) as total_name_differ
        from(
            select block_code as location_id,'Block-'||block_name as location_name
            from public.m_block  " . $whereMain . " 
         )
         as A  
        LEFT JOIN
        (select
                    count(1) as total_applicant,
                    count(1) filter(where wbpds_is_sent=1) as total_sent,
                    count(1) filter(where (aadhar_no IS NOT NULL and trim(aadhar_no)!='' and length(trim(aadhar_no))=12)) as total_not_sent,
                    count(1) filter(where  wbpds_is_sent=1 and wbpds_response_received=1) as total_received,
                    count(1) filter(where wbpds_is_sent=1 and wbpds_response_received IS NULL) as total_not_received,
                    count(1) filter(where wbpds_is_sent=1 and wbpds_response_received=1 and name_is_match=1) as total_name_same,
                    count(1) filter(where wbpds_is_sent=1 and wbpds_response_received=1 and name_is_match IS NULL) as total_name_differ,
                    created_by_local_body_code
                    from " . $schema . ". beneficiary where  next_level_role_id=0 
         group by created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";

    // echo $query;die;
    $result1 = DB::connection('pgsql_mis')->select($query1);
    $query2 = "select '1' as type,A.location_id,A.location_name,
        COALESCE(C.total_applicant,0) as total_applicant, 
        COALESCE(C.total_sent,0) as total_sent, 
        COALESCE(C.total_not_sent,0) as total_not_sent, 
        COALESCE(C.total_received,0) as total_received,
        COALESCE(C.total_not_received,0) as total_not_received,
        COALESCE(C.total_name_same,0) as total_name_same, 
        COALESCE(C.total_name_differ,0) as total_name_differ
        from(
            select sub_district_code as location_id,'SubDiv-'||sub_district_name as location_name
            from public.m_sub_district  " . $whereMain . " 
         )
         as A  
        LEFT JOIN
        (select
                    count(1) as total_applicant,
                    count(1) filter(where wbpds_is_sent=1) as total_sent,
                    count(1) filter(where (aadhar_no IS NOT NULL and trim(aadhar_no)!='' and length(trim(aadhar_no))=12)) as total_not_sent,
                    count(1) filter(where  wbpds_is_sent=1 and wbpds_response_received=1) as total_received,
                    count(1) filter(where wbpds_is_sent=1 and wbpds_response_received IS NULL) as total_not_received,
                    count(1) filter(where wbpds_is_sent=1 and wbpds_response_received=1 and name_is_match=1) as total_name_same,
                    count(1) filter(where wbpds_is_sent=1 and wbpds_response_received=1 and name_is_match IS NULL) as total_name_differ,
                    created_by_local_body_code
                    from " . $schema . ". beneficiary where next_level_role_id=0
         group by created_by_local_body_code) as C ON A.location_id=C.created_by_local_body_code";

    // echo $query;die;
    $result2 = DB::connection('pgsql_mis')->select($query2);
    $result = array_merge($result1, $result2);
    $heading_msg = "Block/Sub Division wise Aadhaar POC WITH WBPDS of the District " . $district_row->district_name . " for the Scheme " . $scheme_obj->scheme_name;
    return view(
      'wbpdsmis.drillblksubwise',
      [
        'district_code' => $district_row->district_code,
        'district_name' => $district_row->district_name,
        'scheme_name' => $scheme_obj->scheme_name,
        'scheme_id' => $scheme_obj->id,
        'heading_msg' => $heading_msg,
        'result' => $result,
        'designation_id' => $designation_id,
        'r_time' => $r_time,
        'c_time' => $c_time,
        'backurl' => $backurl
      ]
    );




  }
  public function wbpdsapplicantreport(Request $request)
  {
    $this->middleware('auth');
    $scheme_id = $request->scheme_id;
    $designation_id = Auth::user()->designation_id;
    $userId = Auth::user()->id;
    if (empty($scheme_id)) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    if (!ctype_digit($scheme_id)) {
      return redirect("/")->with('error', 'User Disabled. ');
    }
    //dd($designation_id);
    if ($designation_id == 'HOD') {
      $type = $request->type;
      $code = $request->code;
      // $backurl='drilldownwbpdsdistrictwise?scheme_id='.$scheme_id.'&district_code='.$district_code;
    } else if ($designation_id == 'Approver') {
      return redirect("/")->with('error', 'User Disabled. ');
      $type = $request->type;
      $code = $request->code;
      $backurl = 'drilldownwbpdsbloksubwise?scheme_id=' . $scheme_id;
    } else if ($designation_id == 'Verifier') {
      $duty_obj = Configduty::where('user_id', $userId)->where('scheme_id', $scheme_id)->first();
      if (trim($duty_obj->is_urban) == 2 && trim($duty_obj->mapping_level) == 'Block') {
        $code = $duty_obj->taluka_code;
        $type = 2;
      } else if (trim($duty_obj->is_urban) == 1 && trim($duty_obj->mapping_level) == 'Subdiv') {
        $code = $duty_obj->urban_body_code;
        $type = 1;
      }
      $backurl = 'wbpdsaadharScheme?scheme_id=' . $scheme_id;

    } else {
      return redirect("/")->with('error', 'User Disabled. ');
    }



    if (empty($code)) {

      return redirect("/")->with('error', 'User Disabled. ');
    }
    if (!ctype_digit($code)) {

      return redirect("/")->with('error', 'User Disabled. ');
    }
    if (empty($type)) {

      return redirect("/")->with('error', 'User Disabled. ');
    }


    if ($type == 2) {
      $blksubdivtxt = 'Block';
      $loc_row = Taluka::select('district_code', 'block_code as location_code', 'block_name as location_name')->where('block_code', $code)->first();

    } else if ($type == 1) {
      $blksubdivtxt = 'Sub Division';
      $loc_row = SubDistrict::select('district_code', 'sub_district_code as location_code', 'sub_district_name as location_name')->where('sub_district_code', $code)->first();

    } else {

      return redirect("/")->with('error', 'User Disabled. ');
    }
    $district_row = District::where('district_code', $loc_row->district_code)->first();
    if ($designation_id == 'HOD') {
      $backurl = 'drilldownwbpdsdistrictwise?scheme_id=' . $scheme_id . '&district_code=' . $district_row->district_code;
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    $heading_msg = "Aadhaar POC WITH WBPDS  for the Scheme " . $scheme_obj->scheme_name;
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    if (request()->ajax()) {
      $limit = $request->input('length');
      $offset = $request->input('start');
      $query = DB::connection('pgsql_mis')->table($schema . '.beneficiary')
        ->where('created_by_local_body_code', $code)->where('next_level_role_id', 0)->whereNull('failed_process_type')->where('wbpds_is_sent', 1)
        ->whereraw("(wbpds_response_received IS NULL or name_is_match IS NULL)");

      if (!empty($request->filter_status)) {
        if ($request->filter_status == 5)
          $query = $query->where('name_is_match', 1);
        else if ($request->filter_status == 6)
          $query = $query->whereNull('name_is_match');
        else if ($request->filter_status == 7)
          $query = $query->whereNull('wbpds_response_received');
      }
      $serachvalue = $request->search['value'];
      if (empty($serachvalue)) {
        $totalRecords = $query->count();
        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
        $filterRecords = count($data);
      } else {
        if (is_numeric($serachvalue)) {
          $query = $query->where(function ($query1) use ($serachvalue) {
            $query1->where('id', $serachvalue)
              ->orWhere('aadhar_no', $serachvalue);
          });
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
        } else {
          $query = $query->where(function ($query1) use ($serachvalue) {
            $query1->where('wbpds_name_as_in_aadhar', 'like', $serachvalue . '%')
              ->orWhere('ben_fname', 'like', $serachvalue . '%');
          });
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get();
        }
        $filterRecords = count($data);
      }
      return datatables()->of($data)->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('application_id', function ($data) {

          $app_id = $data->id;

          return $app_id;
        })->addColumn('aadhar_no_f', function ($data) {

          $aadhar_no_f = '********' . substr($data->aadhar_no, -4);

          return $aadhar_no_f;
        })
        ->addColumn('name_as_in_aadhar', function ($data) {
          return $data->wbpds_name_as_in_aadhar;
        })->addColumn('jb_ben_name_new', function ($data) {
          return trim($data->ben_fname) . ' ' . trim($data->ben_mname) . ' ' . trim($data->ben_lname);
        })->addColumn('is_match', function ($data) {
          $is_match_text = '';
          if ($data->name_is_match == 1) {
            $is_match_text = 'YES';
          } else {
            $is_match_text = 'NO';
          }
          return $is_match_text;
        })->addColumn('view', function ($data) use ($scheme_id, $designation_id) {
          if ($designation_id == 'Verifier') {
            if ($data->wbpds_is_sent == 1) {
              if ($data->wbpds_response_received == 1 && $data->name_is_match == 1) {
                $view = '';
              } else
                $view = '<a href="' . route('wbpdsviewreport', ['id' => $data->id, 'scheme_id' => $scheme_id]) . '" class="btn btn-primary">Update</a>';


            } else {
              $view = '';
            }

          } else {
            $view = '';
          }


          return $view;
        })
        ->rawColumns(['application_id', 'aadhar_no_f', 'name_as_in_aadhar', 'jb_ben_name_new', 'view'])
        ->make(true);
    }
    return view(
      'wbpdsmis.wbpdsapplicantreport',
      [
        'type' => $type,
        'blksubdivtxt' => $blksubdivtxt,
        'code' => $code,
        'location_name' => $loc_row->location_name,
        'district_code' => $district_row->district_code,
        'district_name' => $district_row->district_name,
        'scheme_name' => $scheme_obj->scheme_name,
        'scheme_id' => $scheme_obj->id,
        'heading_msg' => $heading_msg,
        'designation_id' => $designation_id,
        'backurl' => $backurl
      ]
    );

  }
  public function wbpdsviewreport(Request $request)
  {
    $designation_id = Auth::user()->designation_id;
    $user_id = AuthChecker::getUserId();

    $scheme_id = $request->scheme_id;
    if (!ctype_digit($scheme_id)) {
      return redirect("/")->with('error', 'Scheme Not Valid');
    }
    if (empty($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Found');
    }
    if (!is_numeric($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (empty($scheme_obj)) {
      return redirect("/")->with('danger', 'Scheme Not Found');
    }
    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }

    $district_code = $duty_obj->district_code;
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    $query = DB::connection('pgsql_mis')->table($schema . '.beneficiary')
      ->where('created_by_dist_code', $district_code)
      ->where('id', $request->id)->whereNull('failed_process_type')->where('wbpds_is_sent', 1)->whereraw("(wbpds_response_received IS NULL or name_is_match IS NULL)");

    $row = $query->first();
    if (empty($row)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $app_id = $row->created_by_dist_code . substr('0' . $row->scheme_id, -$scheme_length) . substr('0000000' . $row->id, -$id_length);
    $row->app_id = $app_id;

    $reject_revert_cause_list = RejectRevertReason::where('status', true)->get();
    if ($row->dist_code != "") {
      $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
      $district_name = $district->district_name;
    }
    $block_name = "";
    if ($row->block_ulb_code != "") {
      if ($row->rural_urban_id == 1) {
        $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
        if (!empty($block)) {
          $block_name = $block->urban_body_name;
        }
      } else {
        if (!empty($row->block_ulb_code)) {
          $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
          if (!empty($block)) {
            $block_name = $block->block_name;
          } else {
            $block_name = '';
          }
        } else {
          $block_name = '';
        }
      }
    }
    $row->block_name = $block_name;
    $gp_name = "";
    if ($row->gp_ward_code != "") {
      if ($row->rural_urban_id == 1) {
        $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
        if (!empty($gp_ward)) {
          $gp_name = $gp_ward->urban_body_ward_name;
        }
      } else {
        $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
        if (!empty($gp)) {
          $gp_name = $gp->gram_panchyat_name;
        }
      }
    }
    $row->gp_name = $gp_name;
    $doc_type_id = $this->doc_type_id;
    $docs = DB::table($schema . '.ben_docs')->where('ben_id', $request->id)->where('doc_type_id', $doc_type_id)->first();
    $doc_man = DocumentType::get(['id', 'doc_name', 'doc_type', 'doc_mime_type', 'doc_size_kb'])->where("id", $doc_type_id)->first()->toArray();
    // dd($docs);
    return view(
      'wbpdsmis.wbpdsviewreport',
      [
        'designation_id' => $designation_id,
        'scheme_id' => $scheme_id,
        'row' => $row,
        'district_name' => $district_name,
        'block_name' => $block_name,
        'gp_name' => $gp_name,
        'doc_man' => $doc_man,
        'docs' => $docs,
        'reject_revert_cause_list' => $reject_revert_cause_list,
      ]
    );
  }
  public function wbpdsviewreportPost(Request $request)
  {
    $doc_type_id = $this->doc_type_id;
    $designation_id = Auth::user()->designation_id;
    $user_id = AuthChecker::getUserId();
    $id = $request->id;

    $scheme_id = $request->scheme_id;
    //dd($scheme_id);
    $first_name = trim($request->first_name);
    $middle_name = trim($request->middle_name);
    $last_name = trim($request->last_name);
    $aadhar_no = trim($request->aadhar_no);
    $new_aadhaar_is_required = trim($request->new_aadhaar_is_required);
    $in_process_type = trim($request->process_type);
    if (!ctype_digit($scheme_id)) {
      return redirect("/")->with('error', 'Scheme Not Valid');
    }
    if (empty($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Found');
    }
    if (!ctype_digit($request->id)) {
      return redirect("/")->with('danger', 'Applicant ID Not Valid');
    }
    if (!in_array($new_aadhaar_is_required, array(1, 0))) {
      return redirect("/")->with('danger', 'Input Not Valid');
    }
    if ($new_aadhaar_is_required == 1) {
      if (!empty($in_process_type)) {
        $process_type = $in_process_type;
      } else {
        $process_type = 4;
      }
    } else {
      $process_type = $in_process_type;
    }
    //dd($process_type);
    if (!empty($process_type)) {
      if (!in_array($process_type, array(1, 2, 3, 4))) {
        return redirect("/")->with('danger', 'Process Id Not Valid');
      }
    }

    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (empty($scheme_obj)) {
      return redirect("/")->with('danger', 'Scheme Not Found');
    }
    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $condition = array();
    $condition['id'] = $request->id;
    $district_code = $duty_obj->district_code;
    if ($designation_id == 'Verifier') {
      if ($duty_obj->mapping_level == "Subdiv") {
        $created_by_local_body_code = $duty_obj->urban_body_code;
      }
      if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
      }
      $condition['created_by_local_body_code'] = $created_by_local_body_code;
    }
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length = $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
      $file_path = $scheme_obj->file_path;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
      $file_path = 'app';
    }
    $query = DB::table($schema . '.beneficiary')
      ->where($condition);
    $row = $query->first();
    //dd($row);
    if (empty($row)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $c_time = date('Y-m-d H:i:s', time());
    if ($new_aadhaar_is_required == 1) {
      if ($this->isAadharValid(trim($request->aadhar_no)) == false) {
        $errors = array();
        $errorMsg = "Aadhaar Number Invalid";
        array_push($errors, $errorMsg);
        return redirect("/wbpdsviewreport?&scheme_id=" . $scheme_id . "&id=" . $request->id)->with('errors', $return_msg);
      }
    }
    $doc_bank = DocumentType::select('id', 'doc_type', 'doc_name', 'doc_size_kb')->where('id', $doc_type_id)->first();

    if ($new_aadhaar_is_required == 1) {
      if ($request->hasFile('doc_' . $doc_type_id)) {
        $doc_file = $request->file('doc_' . $doc_type_id);
        $file_passport = $doc_file->getClientOriginalName();
        $file_type = $doc_file->getClientOriginalExtension();
        $file_profile = "doc_" . $doc_type_id . "_" . rand(10000, 99999) . '_' . time() . '.' . $doc_file->getClientOriginalExtension();
        //$destinationPath = storage_path('app/keep_wcd/');
        $destinationPath = storage_path('app/' . $file_path . '/');
        $fileStore[] = $doc_file->move($destinationPath, $file_profile);
        //array_push($uploaded_doc,$file_profile);
        $uploaded_doc[$doc_type_id] = $file_profile;
      } else {
        $file_passport = null;
      }
      $ag_update = 1;


    } else {
      $ag_update = 0;
      $file_passport = null;
    }
    if (!empty($row->aadhar_no)) {
      $sp_old_aadhar_no = trim($row->aadhar_no);

    } else {
      $sp_old_aadhar_no = NULL;
    }
    $sp_new_aadhar_no = $aadhar_no;
    if (!empty($row->mobile_no)) {
      $sp_old_mobile_no = $row->mobile_no;

    } else {
      $sp_old_mobile_no = 0;
    }
    if ($designation_id == 'Verifier') {
      $inputMain = [
        'failed_process_type' => $process_type,
        'next_level_role_id_aadhar_validation' => 1
      ];
      $inputFailed = [
        'failed_process_type' => $process_type,
        'next_level_role_id_aadhar_validation' => 1
      ];
      $new_value = [];
      $old_value = [];
      $old_value['aadhar_no'] = trim($row->aadhar_no);
      $new_value['aadhar_no'] = $aadhar_no;
      if ($process_type == 1) {
        $inputMain['acc_validated_aadhar'] = 2;
        $inputFailed['acc_validated_new'] = 2;
      } else if ($process_type == 2 || $process_type == 4) {
        if ($aadhar_no == trim($row->aadhar_no)) {
          $inputMain['wbpds_is_sent_new'] = 1;
        } else {
          $inputMain['wbpds_is_sent_new'] = 2;
        }


        $inputMain['acc_validated_aadhar'] = 0;
        $inputFailed['acc_validated_new'] = 0;

      } else if ($process_type == 3) {
        $inputMain['next_level_role_id'] = -201;
        $inputMain['next_level_role_id_aadhar_validation'] = -201;
        $inputFailed['acc_validated_new'] = -201;
      }
      if ($new_aadhaar_is_required == 1 && ($process_type == 2 || $process_type == 4)) {
        $inputFailed['new_aadhar_no'] = $aadhar_no;
      }

      $insertUpdateBenDetails = [
        'old_data' => json_encode($old_value),
        'new_data' => json_encode($new_value),
        'original_application_id' => $id,
        'dist_code' => $district_code,
        'user_id' => $user_id,
        'scheme_id' => $scheme_id,
        'created_at' => $c_time,
        'update_code' => 22
      ];

      $docs_bank_pre_obj = DB::table($schema . '.ben_docs')->where('ben_id', $request->id)->where('doc_type_id', $doc_type_id)->first();

      try {

        $base_url = url('/');
        DB::beginTransaction();
        if ($ag_update == 1) {
          if (trim($row->aadhar_no) == $aadhar_no) {
            $is_inserted_status = 1;

          } else {
            $is_inserted_status_arr = DB::select("select " . $schema . ".dup_adjustment_insert_update(new_aadhar_no => '" . $sp_new_aadhar_no . "',old_aadhar_no => '" . $sp_old_aadhar_no . "')");
            $is_inserted_status = $is_inserted_status_arr[0]->dup_adjustment_insert_update;
          }
        } else {
          $is_inserted_status = 1;
        }
        //dd($is_inserted_status);
        $return_text = '';

        if ($is_inserted_status == 4) {
          // dd('ok3');
          DB::rollback();
          $return_text = 'Duplicate Aadhaar Information.. Please try different.';
          $return_msg = array("" . $return_text);
          return redirect("/wbpdsviewreport?scheme_id=" . $scheme_id . "&id=" . $request->id)->with('errors', $return_msg);

        } else if ($is_inserted_status == 5) {
          //dd('ok3');
          DB::rollback();
          $return_text = 'Aadhaar Information Modification Faild.. Please try different.';
          $return_msg = array("" . $return_text);
          return redirect("/wbpdsviewreport?scheme_id=" . $scheme_id . "&id=" . $request->id)->with('errors', $return_msg);

        } else if ($is_inserted_status == 1) {
          $main_update = DB::table($schema . '.beneficiary')->where(['id' => $id, 'created_by_local_body_code' => $created_by_local_body_code, 'created_by_dist_code' => $district_code, 'scheme_id' => $scheme_id])->update($inputMain);
          if ($main_update) {
            if ($file_passport) {
              $insert_doc_type_arr = array();
              $insert_doc_type_arr_arch = array();
              $de_active_arr = array();
              $i = 0;
              if (!empty($docs_bank_pre_obj)) {
                $filename = basename($docs_bank_pre_obj->doc_name);
                if (file_exists(storage_path('app/' . $file_path . '/') . '//' . $filename)) {
                  rename(storage_path('app/' . $file_path . '/') . '//' . $filename, storage_path('app/' . $file_arc_path . '/') . '//' . $filename);
                }
                $insert_doc_type_arr_arch[$i]['ben_id'] = $id;
                $insert_doc_type_arr_arch[$i]['doc_type_id'] = $docs_bank_pre_obj->doc_type_id;
                $insert_doc_type_arr_arch[$i]['doc_type_name'] = $docs_bank_pre_obj->doc_type_name;
                $insert_doc_type_arr_arch[$i]['doc_name'] = $docs_bank_pre_obj->doc_name;
                $insert_doc_type_arr_arch[$i]['created_at'] = $c_time;
                $doc_arch_inserted = DB::table($schema . '.ben_docs_arc')->insert($insert_doc_type_arr_arch);
                $doc_deactivated = DB::table($schema . '.ben_docs')->where('is_active', TRUE)->where('ben_id', $id)->whereIn('doc_type_id', $aadhar_doc_type_id)->update(
                  [
                    'is_active' => FALSE
                  ]
                );
              } else {
                $insert_arr = array();
                $insert_doc_type_arr[$i]['ben_id'] = $id;
                $insert_doc_type_arr[$i]['is_active'] = TRUE;
                $insert_doc_type_arr[$i]['doc_type_id'] = $doc_type_id;
                $insert_doc_type_arr[$i]['doc_type_name'] = $doc_bank->doc_name;
                $insert_doc_type_arr[$i]['doc_name'] = $base_url . '/images/' . $file_profile;
                $insert_doc_type_arr[$i]['created_at'] = $c_time;
                $doc_inserted = DB::table($schema . '.ben_docs')->insert($insert_arr);
                $doc_arch_inserted = 1;
                $doc_deactivated = 1;
              }
            } else {
              $doc_inserted = 1;
              $doc_arch_inserted = 1;
              $doc_deactivated = 1;
            }
            $failed_update = DB::table('pension.failed_payment_details')->where(['ben_id' => $id, 'validation_type' => 2, 'scheme_id' => $scheme_id])->update($inputFailed);
            if ($process_type == 3) {
              $scheme_dedup_list = Config::get('constants.bank_mob_aadhar_update_check');
              if (in_array($scheme_id, $scheme_dedup_list)) {
                $free_pending_bank_duplicate_arr = DB::select("select " . $schema . ".free_pending_bank_duplicate_data(in_scheme_id => " . $scheme_id . ", in_district_code => " . $district_code . ")");
                $free_pending_bank_duplicate_data = $free_pending_bank_duplicate_arr[0]->free_pending_bank_duplicate_data;
                $reject_dup_adjustment_arr = DB::select("select " . $schema . ".reject_dup_adjustment(
                    in_old_bank_ifsc => '" . $row->bank_ifsc . "', 
                    in_old_bank_code => '" . $row->bank_code . "', 
                    in_old_aadhar_no => '" . $sp_old_aadhar_no . "', 
                    in_old_mobile_no => " . $sp_old_mobile_no . "
                    )");
                $reject_dup_adjustment = $reject_dup_adjustment_arr[0]->reject_dup_adjustment;
              } else {
                $free_pending_bank_duplicate_data = 1;
                $reject_dup_adjustment = 1;
              }
            } else {
              $free_pending_bank_duplicate_data = 1;
              $reject_dup_adjustment = 1;
            }
            //dd($failed_update);
            // dd($doc_inserted);
            //dd($doc_arch_inserted);
            // dd($doc_deactivated);
            if ($failed_update && $doc_inserted && $doc_arch_inserted && $doc_deactivated) {
              $is_saved_log = DB::table('public.update_ben_details')
                ->insert($insertUpdateBenDetails);
              ;
              if ($is_saved_log) {
                DB::commit();
                if ($process_type == 3) {
                  $return_text = 'Beneficiary Rejected Successfully';
                } else
                  $return_text = 'Beneficiary Edited Successfully';
                return redirect("wbpdsapplicantreport?scheme_id=" . $scheme_id)->with('success', $return_text)->with('id', $row->id);
              } else {
                DB::rollback();
                $return_text = 'Error. Please try again123';
                $return_msg = array("" . $return_text);
              }
            } else {
              DB::rollback();
              $return_text = 'Error. Please try again123333';
              $return_msg = array("" . $return_text);
            }
          } else {
            DB::rollback();
            $return_text = 'Error. Please try again23456';
            $return_msg = array("" . $return_text);
          }
          if ($return_text != '') {
            return redirect("/wbpdsviewreport?scheme_id=" . $scheme_id . "&id=" . $request->id)->with('errors', $return_msg);
          }
        }

      } catch (\Exception $e) {
        dd($e);
        DB::rollback();
        $return_text = 'Error. Please try again';
        $return_msg = array("" . $return_text);
        return redirect("/wbpdsviewreport?scheme_id=" . $scheme_id . "&id=" . $request->id)->with('errors', $return_msg);
      }
    } else {
      return redirect("/")->with('danger', 'Not Allowed');
    }

  }
  public function isAadharValid($num)
  {
    settype($num, "string");
    $expectedDigit = substr($num, -1);
    $actualDigit = $this->CheckSumAadharDigit(substr($num, 0, -1));
    return ($expectedDigit == $actualDigit) ? $expectedDigit == $actualDigit : 0;
  }

  function CheckSumAadharDigit($partial)
  {
    $dihedral = array(
      array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
      array(1, 2, 3, 4, 0, 6, 7, 8, 9, 5),
      array(2, 3, 4, 0, 1, 7, 8, 9, 5, 6),
      array(3, 4, 0, 1, 2, 8, 9, 5, 6, 7),
      array(4, 0, 1, 2, 3, 9, 5, 6, 7, 8),
      array(5, 9, 8, 7, 6, 0, 4, 3, 2, 1),
      array(6, 5, 9, 8, 7, 1, 0, 4, 3, 2),
      array(7, 6, 5, 9, 8, 2, 1, 0, 4, 3),
      array(8, 7, 6, 5, 9, 3, 2, 1, 0, 4),
      array(9, 8, 7, 6, 5, 4, 3, 2, 1, 0)
    );
    $permutation = array(
      array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
      array(1, 5, 7, 6, 2, 8, 3, 0, 9, 4),
      array(5, 8, 0, 3, 7, 9, 6, 1, 4, 2),
      array(8, 9, 1, 6, 0, 4, 3, 5, 2, 7),
      array(9, 4, 5, 3, 1, 2, 6, 8, 7, 0),
      array(4, 2, 8, 6, 5, 7, 3, 9, 0, 1),
      array(2, 7, 9, 3, 8, 0, 6, 4, 1, 5),
      array(7, 0, 4, 6, 9, 1, 3, 2, 5, 8)
    );

    $inverse = array(0, 4, 3, 2, 1, 5, 6, 7, 8, 9);
    settype($partial, "string");
    $partial = strrev($partial);
    $digitIndex = 0;
    for ($i = 0; $i < strlen($partial); $i++) {
      $digitIndex = $dihedral[$digitIndex][$permutation[($i + 1) % 8][$partial[$i]]];
    }
    return $inverse[$digitIndex];
  }

}
