<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configduty;
use App\MapLavel;
use App\District;
use App\Taluka;
use App\Ward;
use App\UrbanBody;
use App\GP;
use Illuminate\Support\Facades\Auth;
use App\DocumentType;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\RejectRevertReason;
use App\AcceptRejectInfo;
use App\Scheme;
use App\BenDocs;
use App\Helpers\AuthChecker;

class WorkflowControllerWcdEdit extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
 
 
  public function applicationdetails(Request $request)
  {
   
    $user_id = AuthChecker::getUserId();
  
      $scheme_id=$request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      $scheme_obj=Scheme::where('id',$scheme_id)->where('is_active',1)->first();
      if(empty($scheme_obj)){
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj=Configduty::where('user_id',$user_id)->where('scheme_id',$scheme_id)->first();
      if(empty($duty_obj)){
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $district_code=$duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }

      $schema = "pension";
      if ($duty_obj->mapping_level == "Subdiv") {
        $urban_body_code=$duty_obj->urban_body_code;
        $urban_bodys = UrbanBody::where('sub_district_code', $urban_body_code)->select('urban_body_code', 'urban_body_name')->get();
        $urban_body_codes = [];
        $i = 0;
        foreach ($urban_bodys as $urban_body) {

          $urban_body_codes[$i] = $urban_body->urban_body_code;
          $i++;
        }
        if (request()->ajax()) {
          $limit = $request->input('length');
          $offset = $request->input('start');
          $query = DB::table($schema . '.beneficiaries')
          ->where('created_by_local_body_code', $urban_body_code)
          ->where('created_by_dist_code', $district_code)
          ->where('unlock_status', 1)->where('next_level_role_id_edit', 999)
          ->where('scheme_id',$scheme_id);
          if (!empty($request->block_ulb_code)) {
            $query = $query->where('block_ulb_code',$request->block_ulb_code);
          }
          if (!empty($request->gp_ward_code)) {
            $query = $query->where('gp_ward_code',$request->gp_ward_code);
          }
          if (!empty($request->filter_status_new)) {
            if($request->filter_status_new==1)
             $query = $query->where('no_aadhar',1);
            if($request->filter_status_new==2)
             $query = $query->where('no_mobile',1);
          }
          $serachvalue = $request->search['value'];
          if (empty($serachvalue)) {
            $totalRecords = $query->count();
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'next_level_role_id_edit', 'aadhar_no','mobile_no'
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
                  'ben_lname', 'gender', 'ben_age', 'ben_mname', 'next_level_role_id', 'next_level_role_id_edit', 'aadhar_no','mobile_no'
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
                  'ben_lname', 'gender', 'ben_age', 'ben_mname','next_level_role_id', 'next_level_role_id_edit', 'aadhar_no','mobile_no'
                ]
              );
            }
            $filterRecords = count($data);
          }
          return datatables()->of($data)->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
            ->addColumn('application_id', function ($data) use ($scheme_id,$scheme_length, $id_length) {

              $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

              return $app_id;
          })->addColumn('view', function ($data) use ($scheme_id) {
              $action = '<a href="benDetailsWcdEdit?id='.$data->id.'&scheme_id='.$scheme_id.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';

             
              

              return $action;
            })
            ->addColumn('id', function ($data) {
              return $data->id;
            }) ->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

                        $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

                        return $app_id;
                    })
            ->addColumn('name', function ($data) {
              return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
            }) ->addColumn('mask_aadhaar_no', function ($data) {
              if (!empty($data->aadhar_no)) {
                  $ben_aadhar_no = '********'.substr(trim($data->aadhar_no),0,3);
              
              } else {
                  $ben_aadhar_no = '';
              }
              return $ben_aadhar_no;
            }) ->addColumn('mask_mobile_no', function ($data) {
                if (!empty($data->mobile_no)) {
                  $ben_mobile_no = trim($data->mobile_no);
                
                } else {
                    $ben_mobile_no = '';
                }
              return $ben_mobile_no;
            })
            ->rawColumns(['view', 'id', 'name','mask_aadhaar_no','mask_mobile_no'])
            ->make(true);
        }
        return view(
          'processApplicationWcdEdit.linelisting_verified_subdiv',
          [
              'scheme_id' => $scheme_id,
              'urban_bodys' => $urban_bodys,
              'district_code' => $district_code
          ]
       );
      }
      if ($duty_obj->mapping_level == "Block") {
        $taluka_code=$duty_obj->taluka_code;
        $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
        if (request()->ajax()) {
          $limit = $request->input('length');
          $offset = $request->input('start');
          $query = DB::table($schema . '.beneficiaries')
          ->where('created_by_local_body_code', $taluka_code)
          ->where('created_by_dist_code', $district_code)
          ->where('unlock_status', 1)->where('next_level_role_id_edit', 999);
          if (!empty($request->gp_code)) {
            $query = $query->where('gp_ward_code',$request->gp_code);
          }
          if (!empty($request->filter_status_new)) {
            if($request->filter_status_new==1)
             $query = $query->where('no_aadhar',1);
            if($request->filter_status_new==2)
             $query = $query->where('no_mobile',1);
          }
          $serachvalue = $request->search['value'];
          if (empty($serachvalue)) {
            $totalRecords = $query->count();
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'next_level_role_id_edit', 'aadhar_no','mobile_no'
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
                  'ben_lname', 'gender', 'ben_age', 'ben_mname', 'next_level_role_id', 'next_level_role_id_edit', 'aadhar_no','mobile_no'
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
                  'ben_lname', 'gender', 'ben_age', 'ben_mname', 'next_level_role_id', 'next_level_role_id_edit', 'aadhar_no','mobile_no'
                ]
              );
            }
            $filterRecords = count($data);
          }
          return datatables()->of($data)->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
            ->addColumn('application_id', function ($data) use ($scheme_id,$scheme_length, $id_length) {

              $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

              return $app_id;
          })->addColumn('view', function ($data) use ($scheme_id) {
            $action = '<a href="benDetailsWcdEdit?id='.$data->id.'&scheme_id='.$scheme_id.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';

             
              

              return $action;
            })
            ->addColumn('id', function ($data) {
              return $data->id;
            }) ->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

                        $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

                        return $app_id;
                    })
            ->addColumn('name', function ($data) {
              return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
            })->addColumn('mask_aadhaar_no', function ($data) {
              if (!empty($data->aadhar_no)) {
                  $ben_aadhar_no = '********'.substr(trim($data->aadhar_no),0,3);
              
              } else {
                  $ben_aadhar_no = '';
              }
              return $ben_aadhar_no;
            }) ->addColumn('mask_mobile_no', function ($data) {
                if (!empty($data->mobile_no)) {
                  $ben_mobile_no = trim($data->mobile_no);
                
                } else {
                    $ben_mobile_no = '';
                }
              return $ben_mobile_no;
            })
            ->rawColumns(['view', 'id', 'name','mask_aadhaar_no','mask_mobile_no'])
            ->make(true);
        }
        return view(
          'processApplicationWcdEdit.linelisting_verified',
          [
              'scheme_id' => $scheme_id,
              'gps' => $gps,
              'district_code' => $district_code
          ]
       );
      }
      if ($duty_obj->mapping_level == "District") {
        $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name', $designation_id)->where('stack_level', $duty_obj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->first();
        $next_level_role_id_cond=$mapArr->id;
        if (request()->ajax()) {
          $limit = $request->input('length');
          $offset = $request->input('start');
          $query = DB::table($schema . '.beneficiaries')->where('created_by_dist_code', $district_code)
          ->where('unlock_status', 1)->where('next_level_role_id_edit', $next_level_role_id_cond);
          if (!empty($request->created_by_local_body_code)) {
            $query = $query->where('created_by_local_body_code',$request->created_by_local_body_code);
          }
          else{
            if (!empty($request->rural_urban_code)) {
              $query = $query->where('rural_urban_id',$request->rural_urban_code);
            }
          }
          
         
          if (!empty($request->filter_status_new)) {
            if($request->filter_status_new==1)
             $query = $query->where('no_aadhar',1);
            if($request->filter_status_new==2)
             $query = $query->where('no_mobile',1);
          }
          $serachvalue = $request->search['value'];
          if (empty($serachvalue)) {
            $totalRecords = $query->count();
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
              'id', 'created_by_dist_code', 'dob', 'assembly_name',
              'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
              'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'next_level_role_id_edit', 'aadhar_no','mobile_no'
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
                  'ben_lname', 'gender', 'ben_age', 'ben_mname', 'next_level_role_id', 'next_level_role_id_edit', 'aadhar_no','mobile_no'
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
                  'ben_lname', 'gender', 'ben_age', 'ben_mname', 'next_level_role_id', 'next_level_role_id_edit', 'aadhar_no','mobile_no'
                ]
              );
            }
            $filterRecords = count($data);
          }
          return datatables()->of($data)->setTotalRecords($totalRecords)
            ->setFilteredRecords($filterRecords)
            ->skipPaging()
            ->addColumn('application_id', function ($data) use ($scheme_id,$scheme_length, $id_length) {

              $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

              return $app_id;
          })->addColumn('view', function ($data) use ($scheme_id) {
            $action = '<a href="benDetailsWcdEdit?id='.$data->id.'&scheme_id='.$scheme_id.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';

             
              

              return $action;
            })
            ->addColumn('id', function ($data) {
              return $data->id;
            }) ->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

                        $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

                        return $app_id;
                    })
            ->addColumn('name', function ($data) {
              return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
            })
            ->addColumn('check', function ($data) {
             
                return '<input type="checkbox" name="approvalcheck[]" onClick="controlCheckBox()" value="' . $data->id . '">';
             
            })->addColumn('mask_aadhaar_no', function ($data) {
              if (!empty($data->aadhar_no)) {
                  $ben_aadhar_no = '********'.substr(trim($data->aadhar_no),0,3);
              
              } else {
                  $ben_aadhar_no = '';
              }
              return $ben_aadhar_no;
            }) ->addColumn('mask_mobile_no', function ($data) {
                if (!empty($data->mobile_no)) {
                    $ben_mobile_no = trim($data->mobile_no);
                
                } else {
                    $ben_mobile_no = '';
                }
                return $ben_mobile_no;
            })
            ->rawColumns(['view', 'id', 'name','check','mask_aadhaar_no','mask_mobile_no'])
            ->make(true);
        }
        return view(
          'processApplicationWcdEdit.linelisting_approved',
          [
              'scheme_id' => $scheme_id,
              'district_code' => $district_code
          ]
       );
      }
    
  
  }
  public function showApplicantDetails(Request $request)
  {
    $user_id = AuthChecker::getUserId();
    
      $scheme_id=$request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      if (empty($request->id)) {
        return redirect("/")->with('danger', 'Applicant ID Not Found');
      }
      if (!is_numeric($request->id)) {
        return redirect("/")->with('danger', 'Applicant ID Not Valid');
      }
      $scheme_obj=Scheme::where('id',$scheme_id)->where('is_active',1)->first();
      if(empty($scheme_obj)){
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj=Configduty::where('user_id',$user_id)->where('scheme_id',$scheme_id)->first();
      if(empty($duty_obj)){
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $district_code=$duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      $query = DB::table($schema . '.beneficiaries')
          ->where('created_by_dist_code', $district_code)
          ->where('id',$request->id);
      if (AuthChecker::VerifierPermission()) {
        $query =$query->where('next_level_role_id_edit',999);
      }
      if (AuthChecker::ApproverPermission()) {
        $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name', $designation_id)->where('stack_level', $duty_obj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->first();
        $next_level_role_id_cond=$mapArr->id;
        
        
         $query =$query->where('next_level_role_id_edit',$next_level_role_id_cond);
      }
      $row=$query->first();
      
      if(empty($row)){
            return redirect("/")->with('danger', 'Not Allowed');
      }
      $app_id = $row->created_by_dist_code . substr('0' . $row->scheme_id, -$scheme_length) . substr('0000000' . $row->id, -$id_length);
      $row->app_id=$app_id;
     // $docs = DB::table($schema . '.ben_docs')->where('ben_id',$request->id)->get();
     $docs = BenDocs::where('scheme_id',$scheme_id)->where('created_by_dist_code',$district_code)->where('beneficiary_id', $request->id)->get();

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
      $row->block_name=$block_name;
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
      $row->gp_name=$gp_name;
      $doc_profile_image = DocumentType::get()->where("is_profile_pic", true)->first();
      $doc_profile_image_id = 999;
      if ($doc_profile_image) {
        $doc_profile_image_id = $doc_profile_image->id;
      }
      return view(
        'processApplicationWcdEdit.pension_view_details_edit',
        [
            'scheme_id' => $scheme_id,
            'row' => $row, 
            'district_name' => $district_name,
            'block_name' => $block_name, 
            'gp_name' => $gp_name, 
            'docs' => $docs, 
            'image_id' => $doc_profile_image_id,
            'reject_revert_cause_list' => $reject_revert_cause_list
        ]
     );
  
   
  }
  public function verifydata(Request $request)
  {
    $user_id = AuthChecker::getUserId();
   
      $scheme_id=$request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      if (empty($request->id)) {
        return redirect("/")->with('danger', 'Applicant ID Not Found');
      }
      if (!is_numeric($request->id)) {
        return redirect("/")->with('danger', 'Applicant ID Not Valid');
      }
      $scheme_obj=Scheme::where('id',$scheme_id)->where('is_active',1)->first();
      if(empty($scheme_obj)){
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj=Configduty::where('user_id',$user_id)->where('scheme_id',$scheme_id)->first();
      if(empty($duty_obj)){
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $district_code=$duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      
      $query = DB::table($schema . '.beneficiaries')
          ->where('created_by_dist_code', $district_code)
          ->where('id',$request->id);
      if (AuthChecker::VerifierPermission()) {
        $query =$query->where('next_level_role_id_edit',999);
      }
      if (AuthChecker::ApproverPermission()) {
        $query =$query->where('next_level_role_id_edit','>',0);
      }
      $row =$query->first();
      if(empty($row)){
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
      $back_url='workflowwcdEdit?scheme_id='.$scheme_id;
      if ($_POST['submit'] == 'Verify') {
        //dd('ok');
        $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name', $designation_id)->where('stack_level', $duty_obj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->first();
        if(empty($mapArr)){
          return redirect("/")->with('danger', 'Not Allowed');
        }
        DB::beginTransaction();
        $accept_reject_model->op_type =class_basename(Route::current()->controller) .'@'. Route::getCurrentRoute()->getActionMethod(). '@SV';
        
        $input = [
          'next_level_role_id_edit' => $mapArr->parent_id, 'comments' => $comments
        ];
         $update = DB::table($schema . '.beneficiaries')
          ->where('created_by_dist_code', $district_code)
          ->where('next_level_role_id_edit',999)->where('id',$request->id)->update($input);
          //dd($update);
          $is_saved_log = $accept_reject_model->save();
          if($update && $is_saved_log){
            DB::commit();
            return redirect($back_url)->with('message', 'Application has been Verified Succesfully!');
          }
          else{
            DB::rollback();
            return redirect($back_url)->with('error', 'Error! Please try again.');
          }
      }
      if ($_POST['submit'] == 'Approve') {
        //dd('ok');
        $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name', $designation_id)->where('stack_level', $duty_obj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->first();
        if(empty($mapArr)){
          return redirect("/")->with('danger', 'Not Allowed');
        }
        
        DB::beginTransaction();
        $accept_reject_model->op_type = 'SA';
        $input = [
          'next_level_role_id_edit' =>  $mapArr->parent_id, 'comments' => $comments
        ];
        if($row->no_aadhar==1){
          if(!empty($row->aadhar_no)){
            $input['no_aadhar']=0;
          }
        }
        if($row->no_mobile==1){
          if(!empty($row->mobile_no)){
            $input['no_mobile']=0;
          }
          
        }
         $update = DB::table($schema . '.beneficiaries')
          ->where('created_by_dist_code', $district_code)
          ->where('id',$request->id)->update($input);
          //dd($update);
          $is_saved_log = $accept_reject_model->save();
          if($update && $is_saved_log){
            DB::commit();
            return redirect($back_url)->with('message', 'Application has been Approved Succesfully!');
          }
          else{
            DB::rollback();
            return redirect($back_url)->with('error', 'Error! Please try again.');
          }
      }
      
    
  }
  public function MassEmployeeApproval(Request $request)
  {
    $user_id = AuthChecker::getUserId();
   
      $scheme_id=$request->scheme_id;
      if (!ctype_digit($scheme_id)) {
        return redirect("/")->with('error', 'Scheme Not Valid');
      }
      
     
      $scheme_obj=Scheme::where('id',$scheme_id)->where('is_active',1)->first();
      if(empty($scheme_obj)){
        return redirect("/")->with('danger', 'Scheme Not Found');
      }
      $duty_obj=Configduty::where('user_id',$user_id)->where('scheme_id',$scheme_id)->first();
      if(empty($duty_obj)){
        return redirect("/")->with('danger', 'Not Allowed');
      }
      if(!AuthChecker::ApproverPermission()){
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $district_code=$duty_obj->district_code;
      if (!empty($scheme_obj->short_code)) {
        $schema = $scheme_obj->short_code;
        $scheme_length =  $scheme_obj->scheme_length;
        $id_length = $scheme_obj->id_length;
      } else {
        $schema = "pension";
        $scheme_length = NULL;
        $id_length = NULL;
      }
      
      $mapArr = MapLavel::where('scheme_id', $duty_obj->scheme_id)->where('role_name', $designation_id)->where('stack_level', $duty_obj->mapping_level)->get(['id', 'role_name', 'scheme_id', 'parent_id', 'is_final', 'stack_level', 'is_first', 'role_id'])->first();
      if(empty($mapArr)){
        return redirect("/")->with('danger', 'Not Allowed');
      }
      $c_time = date('Y-m-d H:i:s', time());
      $comments = trim($request->comments);
      
      $back_url='workflowwcdEdit?scheme_id='.$scheme_id;
      
      $inputs = request()->input('approvalcheck');
      $i=0;
      try{
      DB::beginTransaction();
      foreach ($inputs as $input_id) {
        $query = DB::table($schema . '.beneficiaries')
          ->where('created_by_dist_code', $district_code)
          ->where('id',$input_id)->where('next_level_role_id_edit','>',0);
           $row =$query->first();
           $input = [
            'next_level_role_id_edit' =>  $mapArr->parent_id, 'comments' => $comments
          ];
          if($row->no_aadhar==1){
            if(!empty($row->aadhar_no)){
              $input['no_aadhar']=0;
            }
          }
          if($row->no_mobile==1){
            if(!empty($row->mobile_no)){
              $input['no_mobile']=0;
            }
            
          }
          $is_saved_log = $accept_reject_model->save();
          $update = DB::table($schema . '.beneficiaries')
          ->where('created_by_dist_code', $district_code)
          ->where('id',$input_id)->update($input);
          $accept_reject_model = new AcceptRejectInfo;
          $accept_reject_model->created_at = $c_time;
          $accept_reject_model->scheme_id = $scheme_id;
          $accept_reject_model->user_id = $user_id;
          $accept_reject_model->comment_message = $comments;
          $accept_reject_model->created_by_dist_code = $district_code;
          $accept_reject_model->created_by_local_body_code = $district_code;
          $accept_reject_model->ip_address = request()->ip();
          $accept_reject_model->application_id = $input_id;
          $accept_reject_model->op_type = 'SA';
          $is_saved_log = $accept_reject_model->save();
          if($update && $is_saved_log){
            $i++;
          }
      }
      
    }
  
      catch (\Exception $e) {
       // dd($e);
        DB::rollback();
        return redirect($back_url)->with('error', 'Error! Please try again.');
      }  
        
    if($i==count($inputs)){
     
            DB::commit();
            return redirect($back_url)->with('message', 'Applications has been Approved Succesfully!');
    }
    else{
      
            DB::rollback();
            return redirect($back_url)->with('error', 'Error! Please try again.');
    }
      
      
    
  }
}
