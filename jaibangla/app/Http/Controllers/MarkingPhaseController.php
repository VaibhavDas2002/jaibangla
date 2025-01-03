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
use App\AcceptRejectInfo;
use App\MapLavel;
use App\BenDocs;
use App\DsPhase;
use App\Helpers\AuthChecker;

class MarkingPhaseController extends Controller
{
  private $scheme_id;
  private $source_type;
  private $ben_status;
  private $doc_type_id;
  

  public function __construct()
  {

    $this->scheme_id = 20;
    $this->source_type = 'ss_nfsa';
    $this->ben_status = -97;
    $this->doc_type_id = 6;
  }

  public function markdslist(Request $request)
  {
    //return redirect("/")->with('danger', 'Not Allowed');
    $this->middleware('auth');
    $designation_id = Auth::user()->designation_id;
    //dd($designation_id);
    $user_id = AuthChecker::getUserId();

    $scheme_id = $request->scheme_id;
    $type = $request->type;
    $ds_mark_phase = $request->ds_mark_phase;
    if ($type == '') {
      return redirect("/")->with('error', 'Type Not Valid');
    }
    if (!ctype_digit($type)) {
      return redirect("/")->with('error', 'Type Not Valid');
    }
    if ($ds_mark_phase == '') {
      return redirect("/")->with('error', 'Type Not Valid');
    }
    if (!ctype_digit($ds_mark_phase)) {
      return redirect("/")->with('error', 'Type Not Valid');
    }
    if (!in_array($type, array('1', '2', '3', '4'))) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if (!ctype_digit($scheme_id)) {
      return redirect("/")->with('error', 'Scheme Not Valid');
    }
    $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
    if (empty($scheme_obj)) {
      return redirect("/")->with('danger', 'Scheme Not Found');
    }
    $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
    if (empty($duty_obj)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    if ($type == 1) {
      if (!in_array($designation_id, array( 'Special LAO'))) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
    }
    
    
    $ds_phase_arr = DsPhase::where('phase_code', $ds_mark_phase)->first();
    if (empty($ds_phase_arr)) {
      return redirect("/")->with('danger', 'Not Allowed');
    }
    $camp_roman = $ds_phase_arr->phase_des;
    $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
    $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
    $next_level_role_id_approver = $role_id_approver->parent_id;
    $next_level_role_id_verifier = $role_id_verifier->parent_id;
    // dd($next_level_role_id_verifier);
    $type_des = 'Mark as Duare Sarkar ' . $camp_roman . ' Camps';
    //dd($type);

    //dd($type_des);
    $district_code = $duty_obj->district_code;
    $urban_bodys = collect([]);
    $gps = collect([]);
    $district_list_obj = collect([]);
    if (!empty($scheme_obj->short_code)) {
      $schema = $scheme_obj->short_code;
      $scheme_length =  $scheme_obj->scheme_length;
      $id_length = $scheme_obj->id_length;
    } else {
      $schema = "pension";
      $scheme_length = NULL;
      $id_length = NULL;
    }
    
    
    $allow_marking_count = DB::table('pension.ds_mark_can_district')
    ->where('created_by_dist_code',$district_code)->where('ds_phase',$ds_mark_phase)->where('is_allowded',1)
    ->count();
      if($allow_marking_count==0){
        return redirect("/")->with('danger', 'Marking temporarily suspended.');  
      }
    if (request()->ajax()) {
      $limit = $request->input('length');
      $offset = $request->input('start');
      $application_type = $request->application_type;
      $process_type = $request->process_type;

      
      if ($type == 1) {
        $query = DB::table($schema . '.beneficiary')
          ->whereNull('is_lb_imported')->whereNull('ds_phase')->whereRaw(' (next_level_role_id IS NULL or next_level_role_id=' . $next_level_role_id_verifier . ') ')->where('created_by_dist_code', $district_code)->whereNull('sm_flag')->where('is_samadhan', false)/*->whereraw(" (dup_bank=0 or dup_bank IS NULL) and (dup_aadhar=0 or dup_aadhar IS NULL) and (dup_mobile=0 or dup_mobile IS NULL) and (no_aadhar=0 or no_aadhar IS NULL) and (no_mobile=0 or no_mobile IS NULL)")*/;
      } else {
        $query = DB::table($schema . '.beneficiary')
          ->where('is_rejected', 0);
      }
      if (!empty($request->created_by_local_body_code)) {
        $query = $query->where('created_by_local_body_code', $request->created_by_local_body_code);
      }
      if (!empty($application_type)) {
        if ($application_type == 1)
          $query = $query->whereNull('sm_ds_mark');
        if ($application_type == 2)
          $query = $query->where('sm_ds_mark', 1)->where('sm_ds_mark_vii', 1);
      }
      $serachvalue = $request->search['value'];
      if (empty($serachvalue)) {
        $totalRecords = $query->count();
        $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
          'id', 'created_by_dist_code', 'dob', 'assembly_name',
          'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
          'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag', 'sm_mobile_no',
          'is_rejected', 'mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank', 'sm_ds_mark', 'sm_ds_mark_role_id', 'aadhar_no', 'sm_ds_mark_vii', 'sm_ds_mark_viii', 'sm_ds_mark_ix'
        ]);
        $filterRecords = count($data);
      } else {
        if (is_numeric($serachvalue)) {
          $ben_id = $serachvalue;
          if (strlen($ben_id) == 12) {
            $ben_id = (string) $ben_id;
            $query = $query->where('aadhar_no', $ben_id);
          } else {
            $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
              $query1->where('id', $ben_id);
            });
          }
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag', 'sm_mobile_no',
              'is_rejected', 'mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile',
              'dup_bank', 'sm_ds_mark', 'sm_ds_mark_role_id', 'aadhar_no','sm_ds_mark_vii', 'sm_ds_mark_viii','sm_ds_mark_ix'
            ]
          );
        } else {
          $query = $query->where(function ($query1) use ($serachvalue) {
            $query1->where('ben_fname', 'like', $serachvalue . '%')
              ->orWhere('block_ulb_name', 'like', $serachvalue . '%')
              ->orWhere('gp_ward_name', 'like', $serachvalue . '%')
              ->orWhere('bank_ifsc', 'like', $serachvalue . '%');
          });
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
            [
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag', 'sm_mobile_no', 'is_rejected',
              'mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank', 'sm_ds_mark', 'sm_ds_mark_role_id', 'aadhar_no','sm_ds_mark_vii', 'sm_ds_mark_viii','sm_ds_mark_ix'
            ]
          );
        }
        $filterRecords = count($data);
      }
      // dd($data);
      return datatables()->of($data)
        ->setTotalRecords($totalRecords)
        ->setFilteredRecords($filterRecords)
        ->skipPaging()
        ->addColumn('view', function ($data) use ($ds_mark_phase, $camp_roman, $type, $scheme_id, $designation_id, $next_level_role_id_approver, $next_level_role_id_verifier) {
          $action = '<a href="Viewmarkds?ds_mark_phase=' . $ds_mark_phase . '&type=' . $type . '&id=' . $data->id  . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> View</a>';

         
          
            if (is_null($data->sm_ds_mark_vii)) {
              $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-' . $data->id . '" value="' . $data->id . '">Mark as ' . $camp_roman . ' Camps</button>';
            } else if ($data->sm_ds_mark_vii = 1) {
              $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Already Marked as Duare Sarkar ' . $camp_roman . ' Camps';
            }
          




          return $action;
        })->addColumn('check', function ($data) use ($designation_id) {
          return '';
        })
        ->addColumn('id', function ($data) {
          return $data->id;
        })
        ->addColumn('name', function ($data) {
          return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
        })->addColumn('bank_ifsc', function ($data) {
          if (!empty($data->bank_ifsc)) {
            $bank_ifsc = trim($data->bank_ifsc);
          } else {
            $bank_ifsc = '';
          }
          return $bank_ifsc;
        })->addColumn('bank_code', function ($data) {
          if (!empty($data->bank_code)) {
            $bank_code = trim($data->bank_code);
          } else {
            $bank_code = '';
          }
          return $bank_code;
        })->addColumn('mobile_no', function ($data) {
          if (!empty($data->mobile_no)) {
            $ben_mobile_no = trim($data->mobile_no);
          } else {
            $ben_mobile_no = '';
          }
          return $ben_mobile_no;
        })->addColumn('aadhaar_no', function ($data) {
          if (!empty($data->aadhaar_no)) {
            $ben_aadhaar_no = trim($data->aadhaar_no);
          } else {
            $ben_aadhaar_no = '';
          }
          return $ben_aadhaar_no;
        })
        ->rawColumns(['view', 'id', 'name', 'aadhaar_no', 'bank_ifsc', 'bank_code', 'bank_ifsc', 'bank_ifsc', 'check'])
        ->make(true);
    }

    return view(
      'markds.markdslist',
      [
        'designation_id' => $designation_id,
        'created_by_dist_code' => $district_code,
        'scheme_id' => $scheme_id,
        'scheme_name' => $scheme_obj->scheme_name,
        'gps' => $gps,
        'urban_bodys' => $urban_bodys,
        'gps' => $gps,
        'district_code' => $district_code,
        'type_des' => $type_des,
        'scheme_id' => $scheme_id,
        'ds_mark_phase' => $ds_mark_phase,
        'camp_roman' => $camp_roman,
        'type' => $type,

      ]
    );
  }
  public function Viewmarkds(Request $request)
  {
    //return redirect("/")->with('danger', 'Not Allowed');
    try {
      $this->middleware('auth');
      $designation_id = Auth::user()->designation_id;
      $user_id = AuthChecker::getUserId();
      $id = $request->id;
      // dd($id);
      if (empty($request->id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }
      $scheme_id = $request->scheme_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }

      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();

      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $type = $request->type;
      if ($type == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($type)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!in_array($type, array('1', '2', '3', '4'))) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $ds_mark_phase = $request->ds_mark_phase;
      if ($ds_mark_phase == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($ds_mark_phase)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      $ds_phase_arr = DsPhase::where('phase_code', $ds_mark_phase)->first();
      if (empty($ds_phase_arr)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if ($type == 2 || $type == 3 || $type == 4) {
        $allow_ds_entry = intval($scheme_obj->allow_ds_entry);
        if($allow_ds_entry==0){
          return redirect("/")->with('danger', 'Marking temporarily suspended.');  
        }
      }
      if ($type == 1) {
        if (!in_array($designation_id, array( 'Special LAO'))) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
      }
      $camp_roman = $ds_phase_arr->phase_des;
      $type_des = 'Sarasori Mukhyamantri ';
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      if ($type == 1) {
        $query = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)->whereNull('ds_phase')->where('id', $id)->where('created_by_dist_code', $district_code)->whereRaw(' (next_level_role_id IS NULL or next_level_role_id=' . $next_level_role_id_verifier . ') ')->where('is_samadhan', false);
      } else {
        $query = DB::table($schema . '.beneficiary')->where('id', $id)->where('is_rejected', 0);
      }
      $row = $query->first();
      // dd( $row);
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      //dd($row->aadhar_no);


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
            $gp_name =  $gp_ward->urban_body_ward_name;
          }
        } else {
          $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
          if (!empty($gp)) {
            $gp_name =  $gp->gram_panchyat_name;
          }
        }
      }

      $row->gp_name = $gp_name;
      $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $row->created_by_dist_code)->orderBy('document_type')->get();
      return view(
        'markds.Viewmark',
        [
          'designation_id' => $designation_id,
          'row' => $row,
          'id' => $id,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'docs' => $docs,
          'reject_revert_cause_list' => $reject_revert_cause_list,
          'scheme_id' => $scheme_id,
          'ds_mark_phase' => $ds_mark_phase,
          'camp_roman' => $camp_roman,
          'type' => $type,

        ]
      );
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function DsmarkPost(Request $request)
  {
    try {
      //return redirect("/")->with('danger', 'Not Allowed');
      $this->middleware('auth');
      $designation_id = Auth::user()->designation_id;
      $user_id = AuthChecker::getUserId();
      if (empty($request->beneficiary_id)) {
        return redirect("/")->with('danger', 'Beneficiary ID Not Found');
      }

      if (empty($request->scheme_id)) {
        return redirect("/")->with('danger', 'Scheme ID Not Found');
      }
      $scheme_id = $request->scheme_id;
      $id = $request->beneficiary_id;
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed...');
      }
      $type = $request->type;
      if ($type == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($type)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!in_array($type, array('1', '2', '3', '4'))) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $ds_mark_phase = $request->ds_mark_phase;
      if ($ds_mark_phase == '') {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      if (!ctype_digit($ds_mark_phase)) {
        return redirect("/")->with('error', 'Type Not Valid');
      }
      $ds_phase_arr = DsPhase::where('phase_code', $ds_mark_phase)->first();
      if (empty($ds_phase_arr)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if ($type == 1) {
        if (!in_array($designation_id, array( 'Special LAO'))) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
      }
      /*
      if (trim($request->ds_registration_no)=='') {
        return redirect("/")->with('error', 'Camp Registration No. Required');
      }
      if (strlen(trim($request->ds_registration_no))<24) {
        return redirect("/")->with('error', 'Camp Registration No. Not Valid');

      }
      */
      $camp_roman = $ds_phase_arr->phase_des;
      
      $district_code = $duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
      } else {
        $schema = "pension";
      }
      $role_id_approver = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Approver')->first();
      $role_id_verifier = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_approver = $role_id_approver->parent_id;
      $next_level_role_id_verifier = $role_id_verifier->parent_id;
      $condition = array();
      $condition['id'] = $id;

      

      if ($type == 1) {
        $condition['created_by_dist_code'] = $district_code;
        $query = DB::table($schema . '.beneficiary')
          ->where($condition)->whereNull('ds_phase')->where('id', $id)->whereRaw(' (next_level_role_id IS NULL or next_level_role_id=' . $next_level_role_id_verifier . ') ')->where('is_samadhan', false);
      } else {
        $query = DB::table($schema . '.beneficiary')->where('id', $id)->where('is_rejected', 0);
      }
      
      
      $query = $query->whereNull('sm_ds_mark_viii');
     
      $row = $query->first();
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $is_error = 0;

      $allow_marking_count = DB::table('pension.ds_mark_can_district')
    ->where('created_by_dist_code',$district_code)->where('ds_phase',$ds_mark_phase)->where('is_allowded',1)
    ->count();
      if($allow_marking_count==0){
        return redirect("/")->with('danger', 'Marking temporarily suspended.');  
      }

      if ($is_error == 0) {


        $c_time = date('Y-m-d H:i:s', time());
        DB::beginTransaction();

        $in_pension_id = 'ARRAY[' . "'$request->beneficiary_id'" . ']';
        $comments = NULL;
        if(trim($request->ds_registration_no)!=''){
        $ds_registration_no=trim($request->ds_registration_no);
        }
        else{
          $ds_registration_no=NULL; 
        }
        $is_inserted_status_arr = DB::select("select " . $schema . ".dsmark_for_sm(in_ds_mark_phase => $ds_mark_phase,in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'SMDSMARK', in_custom_comment => '" . $comments . "',in_ds_registration_no => '" . $ds_registration_no . "')");
        //dd($is_inserted_status_arr);
        $is_inserted_status = $is_inserted_status_arr[0]->dsmark_for_sm;

        if ($is_inserted_status == 1) {
          DB::commit();
          $errors = array();
          $return_text = 'Beneficiary with  Id:' . $id . ' has been marked as Duare Sarkar ' . $camp_roman . ' Camps';
      
          return redirect("/markdslist?type=" . $type . "&ds_mark_phase=" . $ds_mark_phase . "&scheme_id=" . $scheme_id)->with('success', $return_text);
        } else if ($is_inserted_status == 10) {
          DB::rollback();
          $errors = array();
          $errorMsg = 'Total DS mark  Applications  exceeds the Quota';
          array_push($errors, $errorMsg);
        } else {
          DB::rollback();
          $errors = array();
          $errorMsg = 'Error.. Please try different.';
          array_push($errors, $errorMsg);
        }
      }


      if (count($errors) > 0) {
        return redirect("/markdslist?type=" . $type . "&ds_mark_phase=" . $ds_mark_phase . "&scheme_id=" . $scheme_id)->with('errors', $errors);
      }
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  

}



