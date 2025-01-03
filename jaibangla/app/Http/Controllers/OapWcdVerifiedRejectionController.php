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
use App\PensionOAPWCD;
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

class OapWcdVerifiedRejectionController extends Controller
{
    public function __construct()
  {

    $this->scheme_id = 20;
    $this->source_type = 'ss_nfsa';
    $this->ben_status = -97;
    $this->doc_type_id = 6;
  }
  public function index(Request $request){
    
    $user_id = AuthChecker::getUserId();
        $dutyObj = Configduty::where('user_id', '=', $user_id)
            ->where('is_active', 1)
            ->first();  
        $distCode = $dutyObj->district_code;
        $scheme_id=$request->wcd_type;
        if ((Auth::user()->designation_id == 'Approver') && ($scheme_id==10))  {
            $levels = [
                2 => 'Rural',
                1 => 'Urban',
            ];
            return view('oap-wcd-verified-reject/index', [
                'levels' => $levels,
                'dist_code' => $distCode,
                'scheme_id' => $scheme_id,
            ]);
        } else {
            return redirect('/')->with('success', 'Unauthorized');
        }
    }

    public function list(Request $request){
        // dd($request->all());
      $user_id = AuthChecker::getUserId();
      $dutyObj = Configduty::where('user_id', '=', $user_id)
          ->where('is_active', 1)
          ->first();
      $limit = $request->input('length');
      $offset = $request->input('start');
      $distCode = $dutyObj->district_code;
      $rural_urban = $request->filter_1;
      $local_body_code = $request->filter_2;
      $scheme_id=$request->scheme_type;
      $caste = $request->caste;
      if ($request->ajax()) 
      {
        $scheme_id = 10;
        if (Auth::user()->designation_id == 'Approver' && ($scheme_id==10)) 
        {
            if(!empty($rural_urban) && !empty($local_body_code))
            {
                $query = DB::table('oap_wcd.beneficiary')
                ->where('next_level_role_id',43)->where('created_by_dist_code',$distCode)->where('created_by_local_body_code',$local_body_code);
            }
            if(!empty($caste)){
                if($caste == 'SC'){
                    $query = $query->where(trim('caste'),'=','SC');
                }else if($caste == 'ST'){
                    $query = $query->where(trim('caste'),'=','ST');
                }else if($caste == 'General'){
                    $query = $query->where(trim('caste'),'=','General');
                }
            }
        }
      } 
      $serachvalue = $request->search['value'];
        if (empty($serachvalue)) 
        {
            $totalRecords = $query->count();
            // dd($totalRecords);
            $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                'id', 'created_by_dist_code', 'dob', 'assembly_name',
                'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag', 'sm_mobile_no','caste',
                'is_rejected', 'mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank', 'sm_ds_mark',  'aadhar_no',
            ]);
                
                $filterRecords = count($data);
        } else 
        {
            if (is_numeric($serachvalue)) 
            {
                // $ben_id = substr($serachvalue, -7);
                $ben_id = $serachvalue;
                if (strlen($ben_id) >= 9) {
                    $ben_id = (string) $ben_id;
                    $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                        $query1->where('bank_code', $ben_id)->orWhere('mobile_no', $ben_id);
                    });
                } else {
                $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                    $query1->where('id', $ben_id);
                });
                }
                  //dd($query->toSql());
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                [
                      'id', 'created_by_dist_code', 'dob', 'assembly_name',
                      'bank_code', 'ben_fname', 'ben_lname', 'ben_mname', 'gender', 'ben_age', 'block_ulb_name', 'gp_ward_name', 'bank_ifsc', 'village_town_city',
                      'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag', 'sm_mobile_no','caste',
                      'is_rejected', 'mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile',
                      'dup_bank', 'sm_ds_mark', 'aadhar_no'
                ]
                );
            } else 
            {
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
                    'scheme_id', 'lot_generated', 'payment_count', 'next_level_role_id', 'sm_flag', 'sm_mobile_no', 'is_rejected','caste',
                    'mobile_no', 'no_aadhar', 'no_mobile', 'dup_aadhar', 'dup_mobile', 'dup_bank', 'sm_ds_mark', 'aadhar_no'
                ]
                );
            }
                $filterRecords = count($data);
        }
        return datatables()->of($data)
                ->setTotalRecords($totalRecords)
                ->setFilteredRecords($filterRecords)
                ->skipPaging()
                ->addColumn('view', function ($data) use ($scheme_id) {
                    $action = '<div style="display: flex; gap: 10px;">';
                    $action .= '<button class="btn btn-primary btn-xs ben_view_details" value="' . $data->id . '"><i class="glyphicon glyphicon-edit"></i>View</button>';
                    $action .= '<button class="btn btn-danger btn-xs ben_view_button" value="' . $data->id . '_' . $data->scheme_id . '"><i class="glyphicon glyphicon-edit"></i>Reject</button>';
                    $action .= '</div>';
                    // $action = '<button class="btn btn-danger btn-xs ben_view_button" value="' . $data->id . '_' . $data->scheme_id . '"><i class="glyphicon glyphicon-edit"></i>Reject</button>';
                    return $action;
                              
            })
            ->addColumn('beneficiary_id', function ($data) {
                  return $data->id;
            })
            ->addColumn('name', function ($data) {
            return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
            })
            ->addColumn('mobile_no', function ($data) {
                return $data->mobile_no;
            })
            ->addColumn('caste', function ($data) {
                $caste = '';
                if ($data->caste == 1 || $data->caste == 2 || $data->caste == 3 || $data->caste == 4 || $data->caste == 5 ||  $data->caste == NULL) {
                    $caste = 'Not Defined';
                }else {
                    $caste = $data->caste;
                }
                return $caste;
            })
            ->addColumn('last_accno', function ($data) {
                $mask_bank_code = '';
                $bank_code = trim($data->bank_code);
                if (strlen($bank_code) != '') {
                    $mask_bank_code = '********' . substr($bank_code, 8, 4);
                }else{
                    $mask_bank_code = $bank_code;
                }
                return $mask_bank_code;
            })
            ->addColumn('last_ifsc', function ($data) {
                return $data->bank_ifsc;
            })
            ->addColumn('block_ulb_name', function ($data) {
                return $data->block_ulb_name;
            })
            ->addColumn('gp_ward_name', function ($data) {
            return $data->gp_ward_name;
            })
            ->rawColumns([
                  'view', 'beneficiary_id', 'name','mobile_no'
            ])
            ->make(true);
    }
  
  public function view(Request $request){
    $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $ben_details = $request->benid;
            $parts = explode('_', $ben_details);
            $id = $parts[0];
            $scheme_id = $parts[1];
            $response = array_merge( [
                'id' => $id,
                'scheme_id' => $scheme_id
            ]);
        } catch (\Exception $e) {
            $response = [
                'exception' => true,
                'exception_message' => $e->getMessage(),
                // 'exception_message' =>
                //     'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
        }finally {
            return response()->json($response, $statusCode);
        }
    }
    public function rejectPost(Request $request){
        //  dd($request->all());
        $response = [];
        $statusCode = 200;
        if (!$request->ajax()) {
            $statusCode = 400;
            $response = ['error' => 'Error occured in form submit.'];
            return response()->json($response, $statusCode);
        }
        try {
            $user_id = AuthChecker::getUserId();
            $mapObj = DB::connection('pgsql_mis')
                ->table('public.duty_assignement')
                ->where('user_id', $user_id)
                ->where('is_active', 1)
                ->first();
            $c_time = date('Y-m-d H:i:s', time());
            $ben_id= $request->applicantId;
            $scheme_id = $request->scheme_id;
            $accept_reject_comments = $request->accept_reject_comments;
            $district_code = $mapObj->district_code;
            // $block_code = $request->block_code;
            $accept_reject_model = new AcceptRejectInfo;
            $accept_reject_model->created_at = $c_time;
            $accept_reject_model->application_id = $ben_id;
            $accept_reject_model->scheme_id = $scheme_id;
            $accept_reject_model->user_id = $user_id;
            $accept_reject_model->created_by_dist_code = $district_code;
            // $accept_reject_model->created_by_local_body_code = $block_code;
            $accept_reject_model->ip_address = request()->ip();
            $accept_reject_model->comment_message = $accept_reject_comments;
            $accept_reject_model->op_type = 'AR';
            $scheme_obj = Scheme::where('id', $request->scheme_id)->where('is_active', 1)->first();
            if (!empty($scheme_obj->short_code)) {
                $schema = $scheme_obj->short_code;
                $scheme_length =  $scheme_obj->scheme_length;
                $id_length = $scheme_obj->id_length;
            } else {
                $schema = "pension";
                $scheme_length = NULL;
                $id_length = NULL;
            }
            $input = ['next_level_role_id' => -1,'is_rejected' =>1,'is_verified'=>2,'is_approved'=>2,'rejected_date'=>$c_time,'rejected_by'=>$user_id,'is_clean' => 10];
            DB::beginTransaction();
            $is_saved_log = $accept_reject_model->save();
            if($is_saved_log){
                $is_update = DB::table($schema . '.beneficiary')->where('id',$ben_id)->where('created_by_dist_code',$district_code)->where('next_level_role_id',43)->where('scheme_id',10)->update($input);
            }
            if($is_saved_log  && $is_update )
            {
                DB::commit();
                $response = [
                    'status' => 1,
                    'msg' => 'Beneficiary Rejected Successfully',
                    'type' => 'green',
                    'icon' => 'fa fa-check',
                    'title' => 'Success',
                ];
            } else {
                DB::rollback();
                $response = [
                    'status' => 3,
                    'msg' => '3 Somethimg went wrong!!',
                    'type' => 'red',
                    'icon' => 'fa fa-warning',
                    'title' => 'Warning!!',
                ];
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response = [
                'exception' => true,
                // 'exception_message' => $e->getMessage(),
                'exception_message' =>
                    'Something went wrong. May be session time out logout and login again.',
            ];
            $statusCode = 400;
            //throw $th;
        }finally {
            // dd($response);
            return response()->json($response, $statusCode);
        }
    }
    public function benView(Request $request){
        try {
            $id = $request->benid;
            $scheme_id=10;
            if (!is_numeric($id)) {
                return redirect("/")->with('danger', 'Applicant ID Not Valid');
            }
            $user_id = AuthChecker::getUserId();
            $duty = Configduty::where('user_id', '=', $user_id)->where('is_active', 1)->first();
            $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray()            // dd($roleArray);
            $is_active = 0;
            foreach ($roleArray as $roleObj) {
                if ($roleObj['scheme_id'] == $scheme_id) {
                    $is_active = 1;
                    $mapping_level = $roleObj['mapping_level'];
                    $distCode = $roleObj['district_code'];
                    $is_urban = $roleObj['is_urban'];
                    $is_state_login = $roleObj['is_state_login'];
                    if ($roleObj['is_urban'] == 1) {
                        $blockCode = $roleObj['urban_body_code'];
                    } else {
                        $blockCode = $roleObj['taluka_code'];
                    }
                    break;
                }
            }
            if ($is_active == 0) {
                return redirect("/")->with('danger', 'User Disabled');
            }
            $docs = array();
            $row = null;
            $row = PensionOAPWCD::find($id);
            $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $distCode)->orderBy('document_type')->get();
            if (empty($row)) {
                return redirect("/")->with('danger', 'Not Allowed');
            }
            $district_name = "";
            $block_name = "";
            $gp_name =  "";

            if ($row->dist_code != "") {
                $district = District::where('district_code', '=', $row->dist_code)->get(['district_code', 'district_name'])->first();
                $district_name = $district->district_name;
            }
            if ($row->block_ulb_code != "") {
                if ($row->rural_urban_id == 1) {
                    $block = UrbanBody::where('urban_body_code', '=', $row->block_ulb_code)->first();
                    $block_name = $block->urban_body_name;
                } else {
                    $block = Taluka::where('block_code', '=', $row->block_ulb_code)->first();
                    $block_name = $block->block_name;
                }
            }
            if ($row->gp_ward_code != "") {
                if ($row->rural_urban_id == 1) {
                    $gp_ward = Ward::where('urban_body_ward_code', '=', $row->gp_ward_code)->first();
                    $gp_name =  $gp_ward->urban_body_ward_name;
                } else {
                    $gp = GP::where('gram_panchyat_code', '=', $row->gp_ward_code)->get(['gram_panchyat_code', 'gram_panchyat_name'])->first();
                    $gp_name =  $gp->gram_panchyat_name;
                }
            }
            $doc_profile_image = DocumentType::get()
                ->where("is_profile_pic", true)->first();
            $doc_profile_image_id = 999;
            if ($doc_profile_image) {
                $doc_profile_image_id = $doc_profile_image->id;
            }
            if ($is_state_login) {
                $district_state = District::where('district_code', '=', $row->created_by_dist_code)->get(['district_code', 'district_name'])->first();
                $district_state_name = trim($district_state->district_name);
                $row->district_state_name = $district_state_name;
                if ($row->block_ulb_type == 1) {
                    $sdo_state = SubDistrict::where('sub_district_code', '=', $row->created_by_local_body_code)->get(['sub_district_code', 'sub_district_name'])->first();
                    $block_subdiv_state_name = trim($sdo_state->sub_district_name);
                } else {
                    // dd($row->created_by_local_body_code);
                    $block_state = Taluka::where('block_code', '=', $row->created_by_local_body_code)->first();
                    $block_subdiv_state_name = trim($block_state->block_name);
                }
                $row->block_subdiv_state_name = $block_subdiv_state_name;
            } else {
                $row->district_state_name = '';
                $row->urban_code_state_name = '';
                $row->block_subdiv_state_name = '';
            }
            //  dd($row);
            return view('oap-wcd-verified-reject/ben_details_view', ['row' => $row, 'district_name' => $district_name, 'block_name' => $block_name, 'gp_name' => $gp_name, 'docs' => $docs, 'image_id' => $doc_profile_image_id]);
        }catch (\Exception $e) {
            // dd($e);
             return redirect("/")->with('error',  'Some error.please try again ......');
         }
    }
   public function benReject(Request $request){
    try {
        //  dd($request->all());
        $user_id = AuthChecker::getUserId();
            $mapObj = DB::connection('pgsql_mis')
                ->table('public.duty_assignement')
                ->where('user_id', $user_id)
                ->where('is_active', 1)
                ->first();
            $c_time = date('Y-m-d H:i:s', time());
            $ben_id= $request->id;
            $scheme_id = $request->scheme_id;
            $accept_reject_comments = $request->accept_reject_comments;
            $district_code = $mapObj->district_code;
            // $block_code = $request->block_code;
            $accept_reject_model = new AcceptRejectInfo;
            $accept_reject_model->created_at = $c_time;
            $accept_reject_model->application_id = $ben_id;
            $accept_reject_model->scheme_id = $scheme_id;
            $accept_reject_model->user_id = $user_id;
            $accept_reject_model->created_by_dist_code = $district_code;
            // $accept_reject_model->created_by_local_body_code = $block_code;
            $accept_reject_model->ip_address = request()->ip();
            $accept_reject_model->comment_message = $accept_reject_comments;
            $accept_reject_model->op_type = 'AR';
            // dd($accept_reject_model);
            $scheme_obj = Scheme::where('id', $request->scheme_id)->where('is_active', 1)->first();
            if (!empty($scheme_obj->short_code)) {
                $schema = $scheme_obj->short_code;
                $scheme_length =  $scheme_obj->scheme_length;
                $id_length = $scheme_obj->id_length;
            } else {
                $schema = "pension";
                $scheme_length = NULL;
                $id_length = NULL;
            }
            $input = ['next_level_role_id' => -1,'is_rejected' =>1,'is_verified'=>2,'is_approved'=>2,'rejected_date'=>$c_time,'rejected_by'=>$user_id ];
            // dd($input);
            DB::beginTransaction();
            $is_saved_log = $accept_reject_model->save();
            if($is_saved_log){
                $is_update = DB::table($schema . '.beneficiary')->where('id',$ben_id)->where('created_by_dist_code',$district_code)->where('next_level_role_id',43)->where('scheme_id',10)->update($input);
            }
            // dump($is_saved_log);dd($is_update);
            if($is_saved_log  && $is_update ){
                DB::commit();
                return redirect("oap-wcd-verified-rejection?pr1=wcd&wcd_type=10")->with('success', 'Beneficiary Rejected Successfully')
                ->with('id',$ben_id);
            } else{
                DB::rollback();
                 return redirect("oap-wcd-verified-rejection?pr1=wcd&wcd_type=10")->with('errors', array('Some error.Please try again'));
            }
    } catch (\Exception $e) {
        DB::rollback();
        return redirect("/")->with('error',  'Some error.please try again ......');
    }
   }

}