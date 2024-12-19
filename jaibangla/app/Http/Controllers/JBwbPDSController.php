<?php

namespace App\Http\Controllers;
use App\Helpers\AuthChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Scheme;
use App\Configduty;
use App\UrbanBody;
use App\GP;





class JBwbPDSController extends Controller
{
    public function schemeSelection(Request $request)
    {
        $auth = AuthChecker::VerifierChecker();
        if ($auth) {
            $userId = AuthChecker::getUserId();
            $designation_id = AuthChecker::getDesignationId();
            $scheme_list = DB::select(DB::raw("select id,scheme_name from m_scheme where id  in (select scheme_id from duty_assignement where user_id=" . $userId . " and is_active=1) and is_active=1 order by scheme_name"));
            return view('jbwbpds.schemeSelection', [
                'designation_id' => $designation_id,
                'scheme_list' => $scheme_list
            ]);
        }
    }

    public function namemismatchdlist(Request $request)
    {
        $auth = AuthChecker::ReportChecker();
        if ($auth) {
            $user_id = AuthChecker::getUserId();
            $designation_id_old = AuthChecker::getDesignationId();
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
            $type = $request->type;
            if (!in_array($type, array(1, 2))) {
                return redirect("/")->with('danger', 'Input Not Valid');
            }
            if ($type == 1) {
                $type_des = 'Beneficiary with name Validation Failed from WBPDS';
            } else if ($type == 2) {
                $type_des = 'Beneficiary with Name Validation Failed from WBPDS';
            }
            $district_code = $duty_obj->district_code;
            $urban_bodys = collect([]);
            $gps = collect([]);
            if (!empty($scheme_obj->short_code)) {
                $schema = $scheme_obj->short_code;
                $scheme_length = $scheme_obj->scheme_length;
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
            if ($duty_obj->mapping_level == "Block") {
                $created_by_local_body_code = $duty_obj->taluka_code;
                $is_rural = 2;
                $verifier_type = 'Block';
                $urban_bodys = collect([]);
                $taluka_code = $duty_obj->taluka_code;
                $gps = GP::where('block_code', $taluka_code)->select('gram_panchyat_code', 'gram_panchyat_name')->get();
            }
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
            $application_type = $request->application_type;
            $process_type = $request->process_type;
            // dd($process_type);
            $query = DB::table($schema . '.beneficiaries')
                ->where('created_by_dist_code', $district_code)->where('scheme_id', $scheme_id)->whereIn('next_level_role_id', array(0, -57))->whereRaw(" (freezing_modify_aadhar=0 OR freezing_modify_aadhar IS NULL) ");
            if (AuthChecker::VerifierChecker()) {
                $query = $query->where('created_by_local_body_code', $created_by_local_body_code);
                if (!empty($application_type)) {
                    if ($application_type == 1)
                        $query = $query->whereNull('next_level_role_id_aadhar_validation');
                    if ($application_type == 2)
                        $query = $query->where('next_level_role_id_aadhar_validation', 1);
                    if ($application_type == 3)
                        $query = $query->where('next_level_role_id_aadhar_validation', 0);
                    if ($application_type == 4)
                        $query = $query->where('next_level_role_id', -57);
                }
            }
            if ($type == 1) {
                if ($application_type != 3) {
                    $query = $query->where('acc_validated_aadhar', -1);
                }
            }
            if ($type == 2) {
                if ($application_type != 3) {
                    $query = $query->where('acc_validated_aadhar', -2);
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
            if (AuthChecker::ApproverChecker()) {
                // dd($process_type);
                if ($application_type != '') {
                    if ($application_type == 1)
                        $query = $query->where('next_level_role_id_aadhar_validation', 1);
                    if ($application_type == 3)
                        $query = $query->where('next_level_role_id_aadhar_validation', 0);
                    if ($application_type == 4)
                        $query = $query->where('process_acc_validated_aadhar', -57);
                }
                if (!empty($process_type)) {
                    if ($process_type == 1)
                        $query = $query->where('failed_process_type_aadhaar', 1);
                    if ($process_type == 2)
                        $query = $query->where('failed_process_type_aadhaar', 2);
                    if ($process_type == 3)
                        $query = $query->where('failed_process_type_aadhaar', 3);
                }
            }
            //  $rawsql = $query->toSql();
            //  dd($rawsql);
            $serachvalue = $request->search['value'];
            if (empty($serachvalue)) {
                $totalRecords = $query->count();
                $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get([
                    'id',
                    'created_by_dist_code',
                    'dob',
                    'assembly_name',
                    'bank_code',
                    'ben_fname',
                    'ben_lname',
                    'ben_mname',
                    'gender',
                    'ben_age',
                    'block_ulb_name',
                    'gp_ward_name',
                    'bank_ifsc',
                    'village_town_city',
                    'scheme_id',
                    'lot_generated',
                    'payment_count',
                    'next_level_role_id',
                    'next_level_role_id_aadhar_validation',
                    'process_acc_validated_aadhar',
                    'mobile_no',
                    'acc_validated_aadhar',
                    'wbpds_name_as_in_aadhar_sr',
                    'payment_suspended'
                ]);
                $filterRecords = count($data);
            } else {
                if (is_numeric($serachvalue)) {
                    $ben_id = (int) $serachvalue;
                    $query = $query->where(function ($query1) use ($ben_id, $serachvalue) {
                        $query1->where('id', $ben_id)
                            ->orWhere('bank_code', $serachvalue);
                    });
                    $totalRecords = $query->count();
                    $data = $query->orderBy('id', 'ASC')->offset($offset)->limit($limit)->get(
                        [
                            'id',
                            'created_by_dist_code',
                            'dob',
                            'assembly_name',
                            'bank_code',
                            'ben_fname',
                            'ben_lname',
                            'ben_mname',
                            'gender',
                            'ben_age',
                            'block_ulb_name',
                            'gp_ward_name',
                            'bank_ifsc',
                            'village_town_city',
                            'scheme_id',
                            'lot_generated',
                            'payment_count',
                            'next_level_role_id',
                            'next_level_role_id_aadhar_validation',
                            'process_acc_validated_aadhar',
                            'mobile_no',
                            'acc_validated_aadhar',
                            'wbpds_name_as_in_aadhar_sr',
                            'payment_suspended'
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
                            'id',
                            'created_by_dist_code',
                            'dob',
                            'assembly_name',
                            'bank_code',
                            'ben_fname',
                            'ben_lname',
                            'ben_mname',
                            'gender',
                            'ben_age',
                            'block_ulb_name',
                            'gp_ward_name',
                            'bank_ifsc',
                            'village_town_city',
                            'scheme_id',
                            'lot_generated',
                            'payment_count',
                            'next_level_role_id',
                            'next_level_role_id_aadhar_validation',
                            'process_acc_validated_aadhar',
                            'mobile_no',
                            'acc_validated_aadhar',
                            'wbpds_name_as_in_aadhar_sr',
                            'payment_suspended'
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
                })->addColumn('view', function ($data) use ($scheme_id, $designation_id_old, $type) {

                    if (AuthChecker::VerifierChecker()) {
                        if ($data->process_acc_validated_aadhar == -57) {
                            $action = 'Rejected';
                        } else if ($data->acc_validated_aadhar == -2 && $data->next_level_role_id_aadhar_validation == 1) {
                            $action = 'Approval Pending';
                        } else if ($data->acc_validated_aadhar == -2 && is_null($data->next_level_role_id_aadhar_validation)) {
                            if ($data->payment_suspended == 1) {
                                $action = '<b>Mark due to JNMP</b>';
                            } else {
                                $action = '<a href="Viewpdsnamemismatch?id=' . $data->id . '&scheme_id=' . $scheme_id . '&type=' . $type . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
                            }
                        } else {
                            $action = '';
                        }
                    }
                    if (AuthChecker::ApproverChecker()) {
                        if ($data->next_level_role_id_aadhar_validation == -57) {
                            $action = 'Rejected';
                        } else if ($data->next_level_role_id_aadhar_validation == 0) {
                            $action = 'Approved';
                        } else if ($data->acc_validated_aadhar == -2 && $data->next_level_role_id_aadhar_validation == 1) {
                            if ($data->payment_suspended == 1) {
                                $action = '<b>Mark due to JNMP</b>';
                            } else {
                                $action = '<a href="Viewpdsnamemismatch?id=' . $data->id . '&scheme_id=' . $scheme_id . '&type=' . $type . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> View</a>';
                            }
                        } else {
                            $action = '';
                        }
                    }
                    return $action;
                })->addColumn('check', function ($data) use ($designation_id_old) {
                    if (AuthChecker::ApproverChecker()) {
                        if ($data->next_level_role_id_aadhar_validation == 1) {
                            return '<input type="checkbox" name="approvalcheck[]" onClick="controlCheckBox()" value="' . $data->id . '">';
                        } else
                            return '';
                    } else {
                        return '';
                    }
                })
                ->addColumn('id', function ($data) {
                    return $data->id;
                })->addColumn('application_id', function ($data) use ($scheme_length, $id_length) {

                    $app_id = $data->created_by_dist_code . substr('0' . $data->scheme_id, -$scheme_length) . substr('0000000' . $data->id, -$id_length);

                    return $app_id;
                })
                ->addColumn('name', function ($data) {
                    return $data->ben_fname . ' ' . $data->ben_mname . ' ' . $data->ben_lname;
                })->addColumn('name_as_in_aadhar', function ($data) {
                    if (!empty($data->wbpds_name_as_in_aadhar_sr)) {
                        $av_name_response = trim($data->wbpds_name_as_in_aadhar_sr);
                    } else {
                        $av_name_response = '';
                    }
                    return $av_name_response;
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
                })->addColumn('failed_type', function ($data) {
                    $failed_type = '';
                    if (!empty($data->acc_validated_aadhar)) {
                        if ($data->acc_validated_aadhar == '-2') {
                            $failed_type = 'Name';
                        } else if ($data->acc_validated_aadhar == '-1') {
                            $failed_type = 'Aadhaar';
                        }
                    } else {
                        $failed_type = '';
                    }
                    return $failed_type;
                })
                ->rawColumns(['view', 'id', 'name', 'mask_aadhaar_no', 'bank_ifsc', 'bank_code', 'bank_ifsc', 'bank_ifsc', 'check'])
                ->make(true);
        }

        return view(
            'wbpds.linelistingmismatch',
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
                'type' => $type,
                'type_des' => $type_des
            ]
        );
    }

}
