<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Configduty;
use App\MapLavel;
use App\District;
use App\Taluka;
use App\Ward;
use App\UrbanBody;
use App\GP;
use Auth;
use DB;
use App\Helpers\Helper;
use App\SubDistrict;
use Carbon\Carbon;
use Config;
use App\BlkUrbanlEntryMapping;
use App\RejectRevertReason;
use App\AcceptRejectInfo;
use App\Scheme;
use App\DocumentType;
use Validator;
use App\Helpers\AuthChecker;

class WorkflowDeptSpecialController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->base_dob_chk_date = date('Y-m-d');
  }

  public function shemeSelection(Request $request)
  {
         $this->middleware('auth');
        $roleArray = $request->session()->get('role');
        $designation_id_old = Auth::user()->designation_id_old;
        $userId = Auth::user()->id;       
        $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id IN (10) and  id in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
        //dd($scheme_list);
        return view(
            'DeptSpecial.selectSchemeOp',
            [
                'scheme_list' => $scheme_list,
                'designation_id_old' => $designation_id_old,
            ]
        );
  }
  public function showApplicantDetails(Request $request)
  {
    return redirect('/')->with('error', 'Not Allowded');
    $designation_id_old = Auth::user()->designation_id_old;
   // dd($designation_id_old);
    $user_id = AuthChecker::getUserId();
    if ($designation_id_old == 'Verifier' || $designation_id_old == 'Approver') {
      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/scheme-selection-nsap-marked")->with('error', 'Scheme Not Valid');
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
      $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name','Verifier')->first();
      $next_level_role_id=$mapArr->parent_id;
      $query = DB::table($schema . '.beneficiary')
        ->where('created_by_dist_code', $district_code)
        ->where('id', $request->id);
      if ($designation_id_old == 'Verifier') {
        $query = $query->whereNull('next_level_role_id');
      } else if ($designation_id_old == 'Approver') {
        $query = $query->where('next_level_role_id', $next_level_role_id);
      }
      $row = $query->first();
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $app_id = $row->created_by_dist_code . substr('0' . $row->scheme_id, -$scheme_length) . substr('0000000' . $row->id, -$id_length);
      $row->app_id = $app_id;
      $docs = DB::table($schema . '.ben_docs')
        ->where('ben_id', $request->id)->get();
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
      $doc_profile_image = DocumentType::get()->where("is_profile_pic", true)->first();
      $doc_profile_image_id = 999;
      if ($doc_profile_image) {
        $doc_profile_image_id = $doc_profile_image->id;
      }
      return view(
        'DeptSpecial.pension_view_details',
        [
          'scheme_id' => $scheme_id,
          'row' => $row,
          'district_name' => $district_name,
          'block_name' => $block_name,
          'gp_name' => $gp_name,
          'docs' => $docs,
          'image_id' => $doc_profile_image_id,
          'reject_revert_cause_list' => $reject_revert_cause_list,
          'designation_id_old' => $designation_id_old,
          'next_level_role_id' => $next_level_role_id
        ]
      );
    } else {
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  public function marked_list(Request $request)
  {
    return redirect('/')->with('error', 'Not Allowded');
      $this->middleware('auth');
      $designation_id_old = Auth::user()->designation_id_old;
      //dd($designation_id_old);
      $user_id = AuthChecker::getUserId();
  
      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
      if (empty($scheme_obj)) {
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->first();
      //dd($duty_obj);
      if (empty($duty_obj)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      
      $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name','Verifier')->first();
      $next_level_role_id=$mapArr->parent_id;
      $type_des='Department Special Cases';
      
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
      if ($duty_obj->mapping_level == "Subdiv") {
        $created_by_local_body_code = $duty_obj->urban_body_code;
        $allowded_arr= BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('block_ulb_code', $created_by_local_body_code)->first();
        //  $district_list_obj = District::get();
        if (empty($allowded_arr) || intval($allowded_arr->dept_special_quota) == 0) {
          return redirect("/")->with('danger', 'Verficiation is temporarily suspended');
        }
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
      if ($duty_obj->mapping_level == "Block") {
        $created_by_local_body_code = $duty_obj->taluka_code;
        $allowded_arr= BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('block_ulb_code', $created_by_local_body_code)->first();
       //dd()
        if (empty($allowded_arr) || intval($allowded_arr->dept_special_quota) == 0) {
          return redirect("/")->with('danger', 'Verficiation is temporarily suspended');
        }
       // dd( $created_by_local_body_code);
        $is_rural = 2;
        $verifier_type = 'Block';
        $urban_bodys = collect([]);
        $taluka_code = $duty_obj->taluka_code;
        $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
      }
      if ($duty_obj->mapping_level == "District") {
        $district_list_obj = District::get();
        $verifier_type = 'District';
        $is_rural = NULL;
        $created_by_local_body_code = NULL;
      }
      if (request()->ajax()) {
        $limit = $request->input('length');
        $offset = $request->input('start');
        $application_type=$request->application_type;
        //dd($process_type);
        $query = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)->where('dept_mark',1);
          if ($designation_id_old == 'Verifier') {
            $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
            if (!empty($application_type)) {
              if($application_type==1)
               $query = $query->whereNull('next_level_role_id');
              if($application_type==2)
               $query = $query->where('next_level_role_id', $next_level_role_id)->where('dept_special',1);
              if($application_type==3)
               $query = $query->where('next_level_role_id', 0)->where('dept_special',1);
               
            }
          }
       
        if ($duty_obj->mapping_level == "Subdiv") {
          if (!empty($request->block_ulb_code)) {
            $query = $query->where('block_ulb_code', $request->block_ulb_code);
          }
        }
        if (!empty($request->gp_ward_code)) {
          $query = $query->where('gp_ward_code', $request->gp_ward_code);
        }
        if ($designation_id_old == 'Approver') {
         // dd($next_level_role_id);
          if ($application_type!='') {
            if($application_type==1)
            $query = $query->where('next_level_role_id', $next_level_role_id)->where('dept_special',1);
            if($application_type==3)
             $query = $query->where('next_level_role_id', 0)->where('dept_special',1);
             
          }
          
        }
        
        $serachvalue = $request->search['value'];
        if (empty($serachvalue)) {
          $totalRecords = $query->count();
          $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
            'id', 'created_by_dist_code', 'dob', 'assembly_name',
            'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
            'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'
          ]);
          $filterRecords = count($data);
        } else {
          if (is_numeric($serachvalue)) {
            $ben_id = substr($serachvalue, -7);
            $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
              $query1->where('id', $ben_id)
                ->orWhere('bank_code', $serachvalue);
            });
            $totalRecords = $query->count();
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
              [
                'id', 'created_by_dist_code', 'dob', 'assembly_name',
            'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
            'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id'             ]
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
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 
              ]
            );
          }
          $filterRecords = count($data);
        }
        return datatables()->of($data)->setTotalRecords($totalRecords)
          ->setFilteredRecords($filterRecords)
          ->skipPaging()
          ->addColumn('application_id', function ($data) use ($scheme_id, $scheme_length, $id_length) {
  
            $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);
  
            return $app_id;
          })->addColumn('view', function ($data) use ($scheme_id, $designation_id_old,$next_level_role_id) {
           
            if ($designation_id_old == 'Verifier') {
              if(is_null($data->next_level_role_id)){
                $action = '<a href="' . route('showapplicantdeptspecial', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>&nbsp; &nbsp;';
               } else if($data->next_level_role_id==$next_level_role_id){
                $action ='Approval Pending';
               }
                else if($data->next_level_role_id==0){
                 $action ='Approved';
                }
                 else{
                  $action ='';
                }
              
            }
            if ($designation_id_old == 'Approver') {
              if($data->next_level_role_id==0){
                $action ='Approved';
               }
               
                else if($data->next_level_role_id==$next_level_role_id){
                  $action = '<a href="' . route('showapplicantdeptspecial', ['id' => $data->id, 'scheme_id' => $data->scheme_id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>&nbsp; &nbsp;';
                }
                else{
                  $action ='';
                }
              
            
             }
           
  
            return $action;
          })
          ->addColumn('id', function ($data) {
            return $data->id;
          })->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {
  
  
            return $data->id;
          })
          ->addColumn('name', function ($data) {
            return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
          })->addColumn('bank_ifsc', function ($data) {
            if (!empty($data->bank_ifsc)) {
              $bank_ifsc =trim($data->bank_ifsc);
            } else {
              $bank_ifsc = '';
            }
            return $bank_ifsc;
          })->addColumn('bank_code', function ($data) {
            if (!empty($data->bank_code)) {
              $bank_code =trim($data->bank_code);
            } else {
              $bank_code = '';
            }
            return $bank_code;
          })->addColumn('mobile_no', function ($data) {
            if (!empty($data->mobile_no)) {
              $ben_mobile_no =trim($data->mobile_no);
            } else {
              $ben_mobile_no = '';
            }
            return $ben_mobile_no;
          })
          ->rawColumns(['view', 'id', 'name', 'mask_aadhaar_no', 'bank_ifsc','bank_code','bank_ifsc','bank_ifsc', 'check'])
          ->make(true);
      }
  
      return view(
        'DeptSpecial.linelisting',
        [
          'designation_id_old' => $designation_id_old,
          'verifier_type' => $verifier_type,
          'created_by_local_body_code' => $created_by_local_body_code,
          'is_rural' => $is_rural,
          'scheme_id' => $scheme_id,
          'scheme_name' => $scheme_obj->scheme_name,
          'gps' => $gps,
          'urban_bodys' => $urban_bodys,
          'gps' => $gps,
          'district_code' => $district_code,
          'type_des' => $type_des
        ]
      );
  }
  public function verifydata(Request $request)
  {
    return redirect('/')->with('error', 'Not Allowded');
    $designation_id_old = Auth::user()->designation_id_old;
    //dd( $designation_id_old);
    $user_id = AuthChecker::getUserId();
    if ($designation_id_old == 'Verifier' || $designation_id_old == 'Approver') {
      $scheme_id = $request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/scheme-selection-dept-special")->with('error', 'Scheme Not Valid');
      }
      if ($scheme_id != 10) {
        return redirect("/scheme-selection-dept-special")->with('error', 'Scheme Not Valid');
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
      $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name', 'Verifier')->first();
      $next_level_role_id=$mapArr->parent_id;
      //dd($next_level_role_id);
      $query = DB::table($schema . '.beneficiary')
        ->where('created_by_dist_code', $district_code)->where('id', $request->id)->where('dept_mark',1);
      if ($designation_id_old == 'Verifier') {
        $query =  $query->whereNull('next_level_role_id');
      } else if ($designation_id_old == 'Approver') {
        $query = $query->where('next_level_role_id', $next_level_role_id);
      }
      $row = $query->first();
     // dd( $row);
      if (empty($row)) {
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $c_time = date('Y-m-d H:i:s', time());
      $comments = trim($request->comments);
      $accept_reject_model = new AcceptRejectInfo;
      $accept_reject_model->created_at = $c_time;
      $accept_reject_model->application_id = $request->id;
      $accept_reject_model->scheme_id = $scheme_id;
      $accept_reject_model->user_id = $user_id;
      $accept_reject_model->comment_message = $comments;
      $accept_reject_model->created_by_dist_code = $district_code;
      $accept_reject_model->created_by_local_body_code = $row->created_by_local_body_code;
      $accept_reject_model->ip_address = request()->ip();
      $back_page = $request->basePage;

      $back_url = 'dept-special-marked-list?scheme_id=' . $scheme_id;

      if ($request->action_type == 'Verify') {
       // dd('ok');
        $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name', $designation_id_old)->where('stack_level', $duty_obj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->first();
        if (empty($mapArr)) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        $scheme_capacity_arr = Helper::getCapacity($scheme_id, $district_code);
        if ($scheme_capacity_arr['visible'] == 1) {
          if ($scheme_capacity_arr['total_data'] >= $scheme_capacity_arr['capacity']) {
            $errorMsgCap = "Total no. of Approved applications " . $scheme_capacity_arr['total_data'] . " exceeds the quota " . $scheme_capacity_arr['capacity'];
            return redirect("/")->with('danger', $errorMsgCap);
          }
        }
        $allowded_arr= BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('block_ulb_code', $row->created_by_local_body_code)->first();
        //  $district_list_obj = District::get();
        if (empty($allowded_arr) || intval($allowded_arr->dept_special_quota) == 0) {
          return redirect("/")->with('danger', 'Verficiation is temporarily suspended');
        }
        $total_data = DB::table($schema . '.beneficiary')
        ->selectRaw('count(id) as cnt')
        ->where(function ($query1) use ($next_level_role_id) {
          $query1->where('next_level_role_id', 0);
            $query1->Orwhere('next_level_role_id', $next_level_role_id);
        })->where('dept_special',1)->first();
        if(intval($total_data->cnt) >= intval($allowded_arr->dept_special_quota)){
          return redirect("/")->with('danger', 'Special Quota Exceed');
        }
        DB::beginTransaction();
        $accept_reject_model->op_type = 'MV';
        $input = [
         'dept_special'=>1, 'verification_date' => $c_time, 'verified_by' => $user_id, 'next_level_role_id' => $mapArr->parent_id, 'comments' => $comments
        ];
        
        $update = DB::table($schema . '.beneficiary')
          ->where('created_by_dist_code', $district_code)
          ->whereNull('next_level_role_id')->where('id', $request->id)->update($input);
        //dd($update);
        $is_saved_log = $accept_reject_model->save();
        if ($update && $is_saved_log) {
          DB::commit();
          return redirect($back_url)->with('message', 'Application with Id ' . $request->id . ' has been Verified Succesfully!');
        } else {
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
      } else if ($request->action_type == 'Approve') {
        //dd('ok');
        $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name', $designation_id_old)->where('stack_level', $duty_obj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->first();
        if (empty($mapArr)) {
          return redirect("/")->with('danger', 'Not Allowed');
        }
        $allowded_arr= BlkUrbanlEntryMapping::where('scheme_id', $scheme_id)->where('block_ulb_code', $row->created_by_local_body_code)->first();
        //  $district_list_obj = District::get();
        if (empty($allowded_arr) || intval($allowded_arr->dept_special_quota) == 0) {
          return redirect("/")->with('danger', 'Approval is temporarily suspended');
        }
        $total_data = DB::table($schema . '.beneficiary')
        ->selectRaw('count(id) as cnt')
        ->where(function ($query1) use ($next_level_role_id) {
          $query1->where('next_level_role_id', 0);
        })->where('dept_special',1)->first();
        if(intval($total_data->cnt) >= intval($allowded_arr->dept_special_quota)){
          return redirect("/")->with('danger', 'Special Quota Exceed');
        }
        $in_pension_id = 'ARRAY[' . "'$request->id'" . ']';
        try {
          DB::beginTransaction();
          $is_inserted_status_arr = DB::select("select ".$schema.".approve_data_bulk(in_application_id => $in_pension_id,in_scheme_id => $scheme_id,in_district_code => $district_code,in_user_id => $user_id,in_op_type => 'MA', in_custom_comment => '".$comments."')");
          //dd($is_inserted_status_arr);
          $is_inserted_status=$is_inserted_status_arr[0]->approve_data_bulk;
          //dd($is_inserted_status);
          if($is_inserted_status==10){
            DB::rollback();
            $errorMsgCap = "Total no. of Approved applications  exceeds the quota";
            return redirect("/")->with('danger', $errorMsgCap);

        }
        else if($is_inserted_status==1){
          DB::commit();
          return redirect($back_url)->with('message', 'Application with Id ' . $request->id . ' has been Approved Succesfully!');
        }
        else{
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
        }catch (\Exception $e) {
          //dd($e);
          DB::rollback();
          return redirect($back_url)->with('error', 'Error! Please try again.');
        }
        
        
      } 
     
    } else {
      return redirect("/")->with('danger', 'Not Allowed');
    }
  }
  
  function ageCalculate($dob)
  {
    $diff = 0;
    if ($dob != '') {
      //$diff = $this->ageCalculate($dob);
      $diff = Carbon::parse($dob)->diffInYears($this->base_dob_chk_date);
    }
    return $diff;
  }
}
