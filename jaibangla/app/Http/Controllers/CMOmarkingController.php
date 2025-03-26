<?php

namespace App\Http\Controllers;

use App\Helpers\AuthChecker;
use Illuminate\Http\Request;
use App\Scheme;
use Illuminate\Support\Facades\Auth;
use App\Configduty;
use App\SchemeStepRank;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\DsPhase;
use App\BenDocs;
use App\BenEntry;
use App\District;
use App\Taluka;
use App\UrbanBody;
use App\Ward;
use App\GP;
use App\AcceptRejectInfo;

class CMOmarkingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function cmoListView(Request $request)
    {
        $user_id = Auth::user()->id;
        // dd($request->all());
        $auth = AuthChecker::OperatorChecker();
        if ($auth) {
            $scheme_id = $request->scheme_id;
            $type = $request->type;
            $grievance_id = $request->grievance_id;

            if ($type == '') {
                return redirect("/")->with('error', 'Type Not Valid');
            }
            if (!ctype_digit($type)) {
                return redirect("/")->with('error', 'Type Not Valid');
            }
            if (!in_array($type, array('1', '2', '3'))) {
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
            $district_code = $duty_obj->district_code;
            $is_approver = AuthChecker::ApproverPermission();
            $is_operator = AuthChecker::OperatorPermission();
            $is_verifier = AuthChecker::VerifierPermission();


            if ($is_approver) {
                $allow_marking_count = DB::table('cmo.cmo_block_urban_marking')
                    ->where('district_code', $district_code)->where('scheme_id', $scheme_id)->where('cmo_mark', 1)
                    ->count();
                if ($allow_marking_count == 0) {
                    return redirect("/")->with('danger', 'Marking temporarily suspended.');
                }
            }
            if ($is_verifier || $is_operator) {
                $is_urban = $duty_obj->is_urban;
                $blockUlbCode = $is_urban == 1
                    ? $duty_obj->urban_body_code
                    : ($is_urban == 2 ? $duty_obj->taluka_code : null);
                $allow_marking_count = DB::table('cmo.cmo_block_urban_marking')
                    ->where('block_ulb_code', $blockUlbCode)->where('scheme_id', $scheme_id)->where('cmo_mark', 1)
                    ->count();
                if ($allow_marking_count == 0) {
                    return redirect("/")->with('danger', 'Marking temporarily suspended.');
                }
            }


            $district_code = $duty_obj->district_code;
            $type_des = 'CMO Marking ';
            $urban_bodys = collect([]);
            $gps = collect([]);

            return view('cmo-grievance/cmo-entry-mark', [
                'created_by_dist_code' => $district_code,
                'scheme_id' => $scheme_id,
                'scheme_name' => $scheme_obj->scheme_name,
                'gps' => $gps,
                'urban_bodys' => $urban_bodys,
                'district_code' => $district_code,
                'type_des' => $type_des,
                'type' => $type,
                'grievance_id' => $grievance_id
            ]);

        } else {
            return redirect("/")->with('danger', 'Not Allowed');
        }
    }

    public function cmoListAjax(Request $request)
    {
        // dd(session()->all());   
        // dd($request->all());
        if ($request->ajax()) {
            // dd(Session::get('cmo_dup_value'));
            $user_id = Auth::user()->id;
            $scheme_id = $request->scheme_id;
            $type = $request->type;
            $application_type = $request->application_type;
            $is_verifier = 0;
            $is_approver = 0;
            $can_perform = 0;
            if (AuthChecker::VerifierPermission() || AuthChecker::OperatorPermission()) {
                $is_verifier = 1;
                $can_perform = 1;
            }
            if (AuthChecker::ApproverPermission()) {
                $is_approver = 1;
            }
            $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
            $district_code = $duty_obj->district_code;
            $is_urban = $duty_obj->is_urban;
            $where_cond = ' and created_by_dist_code=' . $district_code;
            if ($is_verifier) {
                $blockUlbCode = $is_urban == 1 ? $duty_obj->urban_body_code : ($is_urban == 2 ? $duty_obj->taluka_code : null);
                $where_cond = $where_cond . ' and created_by_local_body_code=' . $blockUlbCode;
            }
            $next_level_role_id_operator = SchemeStepRank::getSchemeParentId($scheme_id, 1);
            $next_level_role_id_verifier = SchemeStepRank::getSchemeParentId($scheme_id, 2);


            if (Session::get('cmo_dup_type') != '') {
                $dup_type = Session::get('cmo_dup_type');
            } else {
                $dup_type = '';
            }
            if (Session::get('cmo_dup_value') != '') {
                $dup_val = Crypt::decryptString(Session::get('cmo_dup_value'));
            } else {
                $dup_val = '';
            }
            if (Session::get('cmo_grievance_id') != '') {
                $cmo_grievance_id = Session::get('cmo_grievance_id');
            } else {
                $cmo_grievance_id = '';
            }
            if ($dup_type == 'bank')
                $wherecond = " where scheme_id=" . $scheme_id . " and is_rejected=0 and bank_code='" . $dup_val . "'";
            else if ($dup_type == 'aadhar')
                $wherecond = " where scheme_id=" . $scheme_id . " and is_rejected=0 and aadhar_no='" . $dup_val . "'";
            else if ($dup_type == 'mobile')
                $wherecond = " where scheme_id=" . $scheme_id . " and is_rejected=0 and mobile_no='" . $dup_val . "'";

            if ($dup_val != '') {

                $query = "select lb_application_id,id, created_by_dist_code,created_by_local_body_code, dob, assembly_name,
              bank_code, ben_fname, ben_lname, ben_mname, gender, ben_age, block_ulb_name, gp_ward_name, bank_ifsc, village_town_city,
              scheme_id, lot_generated, payment_count, next_level_role_id, sm_flag, sm_mobile_no,
              is_rejected, mobile_no, no_aadhar, no_mobile, dup_aadhar, dup_mobile, dup_bank, sm_ds_mark, sm_ds_mark_role_id, is_approved
              aadhar_no,cmo_entry from pension.beneficiaries " . $wherecond;
            } else {
                $query = '';
            }
            // dd($query);

            if ($query != '') {
                $data = DB::select($query);
            } else {
                $data = array();
            }

            // print_r($data);die;
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('view', function ($data) use ($type, $cmo_grievance_id, $application_type, $is_approver, $is_verifier, $district_code, $blockUlbCode) {
                    $action = '<a href="Viewmarkcmo?grievance_id=' . $cmo_grievance_id . '&type=' . $type . '&id=' . $data->id . '&scheme_id=' . $data->scheme_id . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> View</a>';
                    if ($application_type == 1 || $application_type == 3 || $application_type == 4) {
                        if ($application_type == 1) {
                            $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-' . $data->id . '" value="' . $data->id . '">Mark as CMO Entry</button>';
                        }
                        if ($application_type == 4) {
                            $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-' . $data->id . '" value="' . $data->id . '">Mark as CMO Entry</button>';
                        }
                        if ($application_type == 3) {
                            if (!is_null($data->lb_application_id)) {
                                $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;LB 60 Case';
                            } else if ($data->next_level_role_id == 0) {
                                $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Approved Case';
                            } else if ($data->cmo_entry == 1) {
                                $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Already Marked as CMO ENTRY';
                            } else {
                                if ($is_verifier) {
                                    if ($data->created_by_local_body_code == $blockUlbCode) {
                                        $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-' . $data->id . '" value="' . $data->id . '">Mark as CMO Entry</button>';
                                    } else {
                                        $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Related to Other Block/Sub Division';
                                    }
                                }
                                if ($is_approver) {
                                    if ($data->created_by_dist_code == $district_code) {
                                        $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-primary btn-sm" id="btn-sm-' . $data->id . '" value="' . $data->id . '">Mark as CMO Entry</button>';
                                    } else {
                                        $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Related to Other District';
                                    }
                                }
                            }
                        }

                    } else if ($application_type == 2) {
                        $action = $action . '&nbsp;&nbsp;&nbsp;&nbsp;Already Marked as Duare Sarkar  Camps';
                    }
                    return $action;
                })->addColumn('check', function ($data) {
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
    }

    public function ViewmarkCMO(Request $request)
    {
        // dd($request->all());
        // return redirect("/")->with('danger', 'Not Allowed');
        try {
            $designation_id = Auth::user()->designation_id;
            $user_id = Auth::user()->id;
            $is_verifier = 0;
            $is_approver = 0;
            $can_perform = 0;
            if (AuthChecker::VerifierPermission() || AuthChecker::OperatorPermission()) {
                $is_verifier = 1;
                $can_perform = 1;
            }
            if (AuthChecker::ApproverPermission()) {
                $is_approver = 1;
                $can_perform = 1;
            }
            if ($can_perform == 0) {
                return redirect("/")->with('error', 'Not Allowded');
            }

            $scheme_id = $request->scheme_id;
            $type = $request->type;
            $grievance_id = $request->grievance_id;
            $id = $request->id;
            // dd($id);
            if (empty($request->id)) {
                return redirect("/")->with('danger', 'Beneficiary ID Not Found');
            }
            if ($type == '') {
                return redirect("/")->with('error', 'Type Not Valid');
            }
            if (!ctype_digit($type)) {
                return redirect("/")->with('error', 'Type Not Valid');
            }


            if (!in_array($type, array('1', '2', '3', '4'))) {
                return redirect("/")->with('danger', 'Not Allowed');
            }
            if (!ctype_digit($scheme_id)) {
                return redirect("/")->with('error', 'Scheme Not Valid');
            }
            // dd('ok');
            $scheme_obj = Scheme::where('id', $scheme_id)->where('is_active', 1)->first();
            if (empty($scheme_obj)) {
                return redirect("/")->with('danger', 'Scheme Not Found');
            }
            $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active', 1)->first();
            if (empty($duty_obj)) {
                return redirect("/")->with('danger', 'Not Allowed');
            }


            $type_des = 'Mark as CMO Grievance Entry';
            $district_code = $duty_obj->district_code;

            if ($is_approver) {
                $allow_marking_count = DB::table('cmo.cmo_block_urban_marking')
                    ->where('district_code', $district_code)->where('scheme_id', $scheme_id)->where('cmo_mark', 1)
                    ->count();
                if ($allow_marking_count == 0) {
                    return redirect("/")->with('danger', 'Marking temporarily suspended.');
                }
            }
            if ($is_verifier) {
                $is_urban = $duty_obj->is_urban;
                $blockUlbCode = $is_urban == 1
                    ? $duty_obj->urban_body_code
                    : ($is_urban == 2 ? $duty_obj->taluka_code : null);
                $allow_marking_count = DB::table('cmo.cmo_block_urban_marking')
                    ->where('block_ulb_code', $blockUlbCode)->where('scheme_id', $scheme_id)->where('cmo_mark', 1)
                    ->count();
                if ($allow_marking_count == 0) {
                    return redirect("/")->with('danger', 'Marking temporarily suspended.');
                }
            }
            $next_level_role_id_verifier = SchemeStepRank::getSchemeParentId($scheme_id, 2);
            if ($type == 1) {
                $query = DB::table('pension.beneficiaries')
                    ->where('created_by_dist_code', $district_code)->whereNull('ds_phase')->where('id', $id)->where('created_by_dist_code', $district_code)->whereRaw(' (next_level_role_id IS NULL or next_level_role_id=' . $next_level_role_id_verifier . ') ')->where('is_samadhan', false);
            } else {
                $query = DB::table('pension.beneficiaries')->where('scheme_id', $scheme_id)->where('id', $id)->where('is_rejected', 0);
            }
            $row = $query->first();
            // dd( $row);
            if (empty($row)) {
                return redirect("/")->with('danger', 'Not Allowed');
            }
            //dd($row->aadhar_no);
            $already_mark = 0;
            if (!is_null($row->lb_application_id)) {
                $already_mark = 1;
            } else if ($row->created_by_local_body_code != $blockUlbCode) {
                $already_mark = 1;
            } else
                $already_mark = DB::table('cmo.cmo_mark_list')->where('scheme_id', $scheme_id)->where('ben_id', $id)->count();

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
            $docs = BenDocs::where('beneficiary_id', $id)->where('created_by_dist_code', $row->created_by_dist_code)->orderBy('document_type')->get();
            return view(
                'cmo-grievance.Viewmark',
                [
                    'designation_id' => $designation_id,
                    'row' => $row,
                    'id' => $id,
                    'district_name' => $district_name,
                    'block_name' => $block_name,
                    'gp_name' => $gp_name,
                    'docs' => $docs,
                    'scheme_id' => $scheme_id,
                    'type' => $type,
                    'already_mark' => $already_mark,
                    'grievance_id' => $grievance_id,

                ]
            );
        } catch (\Exception $e) {
            dd($e);
            return redirect("/")->with('danger', 'Not Allowed');
        }
    }
    
    public function cmomarkPost(Request $request)
    {
        try {
            $backUrl = 'markcmolist';
            $type = $request->type;
            $user_id = Auth::user()->id;
            $beneficiary_id = $request->beneficiary_id;
            $scheme_id = $request->scheme_id;
            $grievance_id = intval($request->grievance_id);


            $is_approver = AuthChecker::ApproverPermission();
            $is_verifier = AuthChecker::VerifierPermission();
            $is_operator = AuthChecker::OperatorPermission();
            $duty_obj = Configduty::where('user_id', $user_id)->first();
            if (empty($duty_obj)) {
                return redirect("/")->with('danger', 'Not Allowed');
            }
            $district_code = $duty_obj->district_code;
            $mapping_level = $duty_obj->mapping_level;
            if ($duty_obj->mapping_level == "Block") {
                $urban_body_code = $duty_obj->taluka_code;
            }
            if ($duty_obj->mapping_level == "Subdiv") {
                $urban_body_code = $duty_obj->urban_body_code;
            }

            if ($is_approver) {
                $allow_marking_count = DB::table('cmo.cmo_block_urban_marking')
                    ->where('district_code', $district_code)->where('scheme_id', $scheme_id)->where('cmo_mark', 1)
                    ->count();
                if ($allow_marking_count == 0) {
                    return redirect("/")->with('danger', 'Marking temporarily suspended.');
                }
            }
            if ($is_verifier || $is_operator) {
                $is_urban = $duty_obj->is_urban;
                $blockUlbCode = $is_urban == 1
                    ? $duty_obj->urban_body_code
                    : ($is_urban == 2 ? $duty_obj->taluka_code : null);
                $allow_marking_count = DB::table('cmo.cmo_block_urban_marking')
                    ->where('block_ulb_code', $blockUlbCode)->where('scheme_id', $scheme_id)->where('cmo_mark', 1)
                    ->count();
                if ($allow_marking_count == 0) {
                    return redirect("/")->with('danger', 'Marking temporarily suspended.');
                }
            }

            // dd($blockUlbCode);
            // dd($request->all());
            // Validate grievance_id
            if (empty($request->grievance_id) || !is_numeric($request->grievance_id)) {
                return redirect($backUrl)->with('error', 'Grievance Id is invalid or not found');
            }

           
            // Check if grievance_id exists
            $cmo_id_count = DB::table('cmo.cmo_sm_data')->where('grievance_id', $grievance_id)->count();

            if ($cmo_id_count === 0) {
                return redirect($backUrl)->with('error', 'Grievance Id not found');
            }
            if ($cmo_id_count > 1) {
                return redirect($backUrl)->with('error', 'Grievance Id is invalid');
            }

            // Validate beneficiary_id and scheme_id

            if (empty($beneficiary_id)) {
                return redirect($backUrl)->with('error', 'Beneficiary is not found');
            }
            if (empty($scheme_id)) {
                return redirect($backUrl)->with('error', 'Scheme Id is not found');
            }

            // Check if Beneficiary exists
            $BenEntry = BenEntry::find($beneficiary_id);
            if (!$BenEntry) {
                return redirect($backUrl)->with('error', 'Beneficiary is not found');
            }

            $CMOMarkedCount = DB::table('cmo.cmo_mark_list')->where('ben_id', $beneficiary_id)->count();
            if ($CMOMarkedCount > 0) {
                return redirect()->route($backUrl, ['scheme_id' => $scheme_id, 'type' => $type, 'grievance_id' => $grievance_id])->with('error', 'Beneficiary ID has already been marked as CMO ENTRY');
            }

            DB::connection('pgsql')->beginTransaction();

            // Update Beneficiary Entry
            $BenEntry->cmo_entry = 1;
            $BenEntry->cmo_grievance_id = $grievance_id;
            $isBenUpdate = $BenEntry->save();

            // Insert into cmo_mark_list
            $cmomarkarray = [
                'grievance_id' => $grievance_id,
                'ben_id' => $beneficiary_id,
                'scheme_id' => $scheme_id,
                'marked_by' => Auth::user()->id,
                'marked_at' => date('Y-m-d H:i:s'),
                'marked_ip' => $request->ip()
            ];

            $cmoMarklog = DB::table('cmo.cmo_mark_list')->insert($cmomarkarray);

            $accept_reject_info_model = new AcceptRejectInfo;
            $accept_reject_info_model->scheme_id = $scheme_id;
            $accept_reject_info_model->created_by_dist_code = $district_code;
            $accept_reject_info_model->created_by_local_body_code = $urban_body_code;
            $accept_reject_info_model->created_at = date('Y-m-d H:i:s');
            $accept_reject_info_model->updated_at = date('Y-m-d H:i:s');
            $accept_reject_info_model->op_type = 'CMOENTRYMARK';
            $accept_reject_info_model->ip_address = request()->ip();
            $accept_reject_info_model->user_id = $user_id;
            $accept_reject_info_model->application_id = $beneficiary_id;
            $accept_reject_info_model->module_name = class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod();
            $updateBenDetailsAction = $accept_reject_info_model->save();

            if ($isBenUpdate && $cmoMarklog && $updateBenDetailsAction) {
                DB::connection('pgsql')->commit();
                Session::forget('cmo_dup_btn_visible');
                Session::forget('cmo_dup_type');
                Session::forget('cmo_dup_value');
                return redirect()->route($backUrl, ['scheme_id' => $scheme_id, 'type' => $type, 'grievance_id' => $grievance_id])->with('success', "Beneficiary ID '$beneficiary_id has been marked as CMO ENTRY");
            } else {
                DB::connection('pgsql')->rollBack();
                return redirect()->route($backUrl, ['scheme_id' => $scheme_id, 'type' => $type, 'grievance_id' => $grievance_id])->with('error', 'Failed to mark CMO ENTRY');
            }
        } catch (\Exception $e) {
            dd($e);
            DB::connection('pgsql')->rollBack();
            return redirect()->route($backUrl, ['scheme_id' => $scheme_id, 'type' => $type, 'grievance_id' => $grievance_id])->with('error', 'Failed to mark CMO ENTRY: ');
        }
    }
}
