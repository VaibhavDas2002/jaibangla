<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Scheme;
use App\District;
use App\Configduty;
use App\DocumentType;
use App\GP;
use App\Taluka;
use App\UrbanBody;
use App\Ward;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\RejectRevertReason;
use App\AcceptRejectInfo;
use Illuminate\Support\Facades\Input;
use App\Helpers\Helper;
use App\BlkUrbanlEntryMapping;
use Carbon\Carbon;
use App\MapLavel;
use App\SchemeDocMap;
use Response;
use App\DsPhase;
use Illuminate\Support\Facades\Storage;
use App\BenDocs;
class JBDocUploadController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    date_default_timezone_set('Asia/Kolkata');
    $this->aadhar_doc_type_id = 6;
    $this->supporting_dob_type_id = 258;
    $this->reason_order_type_id = 269;
  }
  public function shemeSelection(Request $request)
  {
    try {
    $designation_id_old = Auth::user()->designation_id_old;
    $user_id = Auth::user()->id;
    if ($designation_id_old == 'Operator' || $designation_id_old == 'Verifier' || $designation_id_old == 'Approver') {
      $schemes = DB::select(DB::raw("select id,scheme_name,display_name,is_active from m_scheme where id  in (select scheme_id from duty_assignement where is_active=1 and user_id=" . $user_id . ") order by rank"));
      //dd($schemes);
      return view(
        'jbdocupload/SchemeSelection',
        [

          'scheme_list' => $schemes,
        ]
      );
    } else {
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  catch (\Exception $e) {
    dd($e);
    return redirect("/")->with('danger', 'Error');
  }
  }
  public function ListView(Request $request)
  {
    // dd($request->all());
    try {
      $c_time = date('Y-m-d H:i:s', time());
      $designation_id_old = Auth::user()->designation_id_old;
      $user_id = Auth::user()->id;

      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $district_list_obj = District::get();
      //dd($duty_obj->mapping_level);
      $district_code = $duty_obj->district_code;
      $urban_bodys = collect([]);
      $gps = collect([]);
      //$district_list_obj = collect([]);
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }

      if ($duty_obj->mapping_level == "Subdiv") {
        $created_by_local_body_code = $duty_obj->urban_body_code;
        $is_rural = 1;
        $verifier_type = 'Subdiv';
        $gps = collect([]);
        $urban_body_code = $duty_obj->urban_body_code;
        $urban_bodys = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();
        $urban_body_codes = [];
        $i = 0;
        foreach ($urban_bodys as $urban_body) {

          $urban_body_codes[$i] = $urban_body->urban_body_code;
          $i++;
        }
      }
     else if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
        $is_rural = 2;
        $verifier_type = 'Block';
        $urban_bodys = collect([]);
        $taluka_code = $duty_obj->taluka_code;
        $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
      }
      else if ($duty_obj->mapping_level == "District") {
        $district_list_obj = District::get();
        $verifier_type = 'District';
        $is_rural = NULL;
        $created_by_local_body_code = NULL;
      }
      else if ($duty_obj->mapping_level == "Department") {
        $verifier_type = 'Department';
        $is_rural = NULL;
        $created_by_local_body_code = NULL;
      }
      if ($designation_id_old == 'Verifier') {
        // $dup_mark_lb = DB::select("select " . $schema . ".dup_mark_lb(in_c_time => '" . $c_time . "',in_user_id => " . $user_id . ",in_created_by_local_body_code => " . $created_by_local_body_code . ")");     
      }
      if (request()->ajax()) {
        $limit = $request->input('length');
        $offset = $request->input('start');
        $role_arr_verfied = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
        $next_level_role_id_verified = $role_arr_verfied->parent_id;
       
    
       
            $query = DB::table($schema . '.beneficiaries')
            ->where('is_rejected',0)->where('scheme_id',$scheme_id);
            // dd($request->dist_code);
            if (!empty($request->dist_code) && isset($request->dist_code) && ($request->dist_code !== 'undefined')) {
              $query = $query->where('created_by_dist_code', $request->dist_code);
            }
           
          
        
        if ($designation_id_old == 'Verifier' || $designation_id_old == 'Operator') {
          $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
        }
        if ($designation_id_old == 'Verifier') {
          //$query = $query->whereIn('no_aadhar_mobile_flag', [1, 2, 3]);
        }
        if ($designation_id_old == 'Approver') {
          // $query = $query->whereIn('no_aadhar_mobile_flag', [2, 3]);
        }
        if ($duty_obj->mapping_level == "Subdiv") {
          if (!empty($request->block_ulb_code) && isset($request->block_ulb_code) && ($request->block_ulb_code !== 'undefined')) {
            $query = $query->where('block_ulb_code', $request->block_ulb_code);
          }
        }
        if (!empty($request->gp_ward_code) && isset($request->gp_ward_code) && ($request->gp_ward_code !== 'undefined')) {
          $query = $query->where('gp_ward_code', $request->gp_ward_code);
        }
        $serachvalue = $request->search['value'];
        if (empty($serachvalue)) {
          $totalRecords = $query->count();
       
          $data = $query->orderBy('dob', 'ASC')->offset($offset)->limit($limit)->get([
            'id','lb_application_id', 'lb_beneficiary_id', 'created_by_dist_code', 'dob',
            'bank_code', 'ben_fname','ben_mname','ben_lname', 'gender', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'next_level_role_id', 'mobile_no',
            'is_rejected'
            
          ]);
         
          $filterRecords = count($data);
        } else {
          if (is_numeric($serachvalue)) {
            $query = $query->where(function ($query1) use ($serachvalue) {
              $query1->where('id', $serachvalue);
            });
            $totalRecords = $query->count();
           
            $data = $query->orderBy('dob', 'ASC')->offset($offset)->limit($limit)->get(
              [
                'id','lb_application_id', 'lb_beneficiary_id', 'created_by_dist_code', 'dob',
            'bank_code', 'ben_fname','ben_mname','ben_lname', 'gender', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'next_level_role_id', 'mobile_no',
            'is_rejected'
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
        
            $data = $query->orderBy('dob', 'ASC')->offset($offset)->limit($limit)->get(
              [
                'id','lb_application_id', 'lb_beneficiary_id', 'created_by_dist_code', 'dob',
            'bank_code', 'ben_fname','ben_mname','ben_lname', 'gender', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'next_level_role_id', 'mobile_no',
            'is_rejected'
              ]
            );
            
          }
          $filterRecords = count($data);
        }
        return datatables()->of($data)->setTotalRecords($totalRecords)
          ->setFilteredRecords($filterRecords)
          ->skipPaging()
          ->addColumn('status', function ($data) {
            $status='';
            if(is_null($data->next_level_role_id)){
              $status='Yet to be Verified and Approved';
            }
            else if($data->next_level_role_id==0){
              $status='Verified and Approved';
            }
            else{
              $status='Verified but yet to be Approved';
            }
            return $status;
          })->addColumn('view', function ($data) use ($scheme_id, $designation_id_old,$next_level_role_id_verified) {
           
            
              $action = '<a href="Viewjbdocupload?id=' . $data->id . '&scheme_id=' . $scheme_id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
            
              return $action;
                       
          })
          ->addColumn('id', function ($data) {
            return $data->id;
          })
          ->addColumn('name', function ($data) {
            return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
          })->addColumn('mobile_no', function ($data) {
            if (!empty($data->mobile_no)) {
              $ben_mobile_no = trim($data->mobile_no);
            } else {
              $ben_mobile_no = '';
            }
            return $ben_mobile_no;
          })
          ->rawColumns(['status','view', 'id', 'name', 'mask_mobile_no', 'check'])
          ->make(true);
      }
    //dd($district_list_obj);
      return view(
        'jbdocupload.linelisting',
        [
          'designation_id_old' => $designation_id_old,
          'verifier_type' => $verifier_type,
          'created_by_local_body_code' => $created_by_local_body_code,
          'is_rural' => $is_rural,
          'scheme_id' => $scheme_id,
          'gps' => $gps,
          'urban_bodys' => $urban_bodys,
          'gps' => $gps,
          'district_code' => $district_code,
          'district_list_obj' => $district_list_obj
        ]
      );
    } catch (\Exception $e) {
      //dd($e);
      return redirect("/")->with('danger', 'Error');
    }
  }
  public function View(Request $request)
  {
    try {
      $designation_id_old = Auth::user()->designation_id_old;
      $transfer_sc = 0;
      $transfer_st = 0;
      $transfer_oap = 0;
      $fetch_lb = 0;
      $can_verify = 0;
      $can_approve = 0;
      $can_reject = 1;
      $back_to_lb = 1;
      $undo = 0;
      $encloser_list = array();
      $user_id = Auth::user()->id;
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
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      if ($designation_id_old == 'Approver' || $designation_id_old == 'Verifier'){
      $query = DB::table($schema . '.beneficiaries')
        ->where('created_by_dist_code', $district_code)
        ->where('id', $request->id)->where('is_rejected',0);
      }
      else{
        $query = DB::table($schema . '.beneficiaries')
        ->where('id', $request->id)->where('is_rejected',0);
      }

      $role_arr_verfied = MapLavel::where('scheme_id', $scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id_verified = $role_arr_verfied->parent_id;
     // dd($next_level_role_id_verified);
      $row = $query->first();
      //dd($row->next_level_role_id);
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      
      
     

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
     
        $doc_list_man_arr = array();
        $doc_list_opt_arr = array();
        $doc_id_list = SchemeDocMap::select('doc_list_man', 'doc_list_opt', 'doc_list_man_group')->where('scheme_code', $scheme_id)->first()->toArray();
        //dd($doc_id_list['doc_list_man']);
        if (isset($doc_id_list['doc_list_man']) && $doc_id_list['doc_list_man'] != 'null') {
          // dd($doc_id_list);
          $doc_list_man_arr = DocumentType::select('id', 'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->selectSub(function ($query) {
            $query->selectRaw('1');
        }, 'is_requied')->whereIn("id", json_decode($doc_id_list['doc_list_man']))->get()->toArray();
        $doc_list_man_arr=$doc_list_man_arr;
        } 
        if (isset($doc_id_list['doc_list_opt']) && $doc_id_list['doc_list_opt'] != 'null') {
          $doc_list_opt_arr = DocumentType::select('id',  'is_profile_pic', 'doc_size_kb', 'doc_name', 'doc_type', 'doucument_group')->selectSub(function ($query) {
            $query->selectRaw('0');
        }, 'is_requied')->whereIn("id", json_decode($doc_id_list['doc_list_opt']))->get()->toArray();
        $doc_list_opt_arr=$doc_list_opt_arr;
        } 
        $c=array_merge($doc_list_man_arr,$doc_list_opt_arr );
         $already_arr=array();
         $doc_list1=array();
         $i=0;
        // dump($c);
         foreach($c as $a_item){

          if(empty($already_arr)){
            array_push($already_arr,$a_item['id']);

            $doc_list1[$i]['id']=$a_item['id'];
            $doc_list1[$i]['is_profile_pic']=$a_item['is_profile_pic'];
            $doc_list1[$i]['doc_size_kb']=$a_item['doc_size_kb'];
            $doc_list1[$i]['doc_name']=$a_item['doc_name'];
            $doc_list1[$i]['doc_type']=$a_item['doc_type'];
            $doc_list1[$i]['doucument_group']=$a_item['doucument_group'];
            $i++;
          }
          else{
            if(!in_array($a_item['id'], $already_arr)){
            $doc_list1[$i]['id']=$a_item['id'];
            $doc_list1[$i]['is_profile_pic']=$a_item['is_profile_pic'];
            $doc_list1[$i]['doc_size_kb']=$a_item['doc_size_kb'];
            $doc_list1[$i]['doc_name']=$a_item['doc_name'];
            $doc_list1[$i]['doc_type']=$a_item['doc_type'];
            $doc_list1[$i]['doucument_group']=$a_item['doucument_group'];
            $i++;
            array_push($already_arr,$a_item['id']);

          }
          
        }
         
         }
         //dd($doc_list1);
        
         
       
      $doc_list = $doc_list1;
        
       
        $encloser_list = array();
        $i = 0;
        $docs = BenDocs::where('scheme_id',$scheme_id)->where('created_by_dist_code',$district_code)->where('beneficiary_id', $request->id)->get();

        //$docs = DB::table($schema . '.ben_docs')->where('ben_id', $request->id)->get();
        if (count($docs) > 0) {
          $encolserdata = $docs->pluck('document_type')->toarray();
          $encolserCount = 1;
        } else {
          $encolserdata = array();
          $encolserCount = 0;
        }
        //dd(json_decode($doc_id_list['doc_list_man']));
        $encloser_list = array();
        $i = 0;
        if (count($doc_list) > 0) {
          foreach ($doc_list as $doc_item) {
            //dump($doc_item);
            $encloser_list[$i]['ben_id'] = $request->id;
            $encloser_list[$i]['id'] = $doc_item['id'];
            $encloser_list[$i]['doc_size_kb'] = $doc_item['doc_size_kb'];
            $encloser_list[$i]['doc_name'] = $doc_item['doc_name'];
            $encloser_list[$i]['doc_type'] = $doc_item['doc_type'];
           
                if (in_array($doc_item['id'], json_decode($doc_id_list['doc_list_man']))) {
                  $encloser_list[$i]['required'] = 1;
                } else {
                  $encloser_list[$i]['required'] = 0;
                }
              
          
           
            if ($encolserCount == 1) {
              if (in_array($doc_item['id'], $encolserdata)) {
                $encloser_list[$i]['can_download'] = 1;
              } else {
                $encloser_list[$i]['can_download'] = 0;
              }
            } else {
              $encloser_list[$i]['can_download'] = 0;
            }

            $i++;
          }
        }
       // dd($encloser_list);
      return view(
        'jbdocupload.View',
        [
          'scheme_id' => $scheme_id,
          'row' => $row,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'docs' => $docs,
          'reject_revert_cause_list' => $reject_revert_cause_list,
          'designation_id_old' => $designation_id_old,
          'doc_list' => $doc_list,
          'encloser_list' => $encloser_list
        ]
          
      );
    } catch (\Exception $e) {
      dd($e);
      return redirect("/")->with('danger', 'Error');
    }
  }
 
}
