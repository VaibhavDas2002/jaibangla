<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scheme;
use App\Configduty;
use App\District;
use App\UrbanBody;
use App\SubDistrict;
use App\Taluka;
use App\Ward;
use App\GP;
use App\MapLavel;
use Redirect;
use Auth;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class AadhaarController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function find(Request $request)
    {
        try{
        $fill_array = array();
        $array = array();
        $errorMsg = '';
        $valid = 1;
        $issubmitted = 0;
        $dupAadhaarCheck = $request->session()->get('dupAadhaarCheck');
        if (!empty($dupAadhaarCheck)) {
            $fill_array['aadhar_no'] = $dupAadhaarCheck;
            $request->session()->forget('dupAadhaarCheck');
        } else
            $fill_array['aadhar_no'] = '';
        if (isset($request->btnSubmit)) {
            if (!empty($request->aadhar_no)) {
                $fill_array['aadhar_no'] = $request->aadhar_no;
            }
            $issubmitted = 1;
            $rules = [
                'aadhar_no' => 'required|numeric'
            ];
            $attributes = array();
            $messages = array();
            $attributes['aadhar_no'] = 'Aadhaar No.';
            $validator = Validator::make($request->all(), $rules, $messages, $attributes);
            if ($validator->passes()) {
                $user_id = Auth::user()->id;
                $scheme_id_in = Configduty::where('is_active', 1)->where('user_id', '=', $user_id)->pluck('scheme_id')->toArray();
                if (empty($scheme_id_in) || count($scheme_id_in) == 0) {
                    $errorMsg = 'You have no schme assigned yet.';
                }
                if ($errorMsg == '') {
                    $district = District::get();
                    $aadhar_no = $request->aadhar_no;
                    $query = "select created_by_dist_code,scheme_id,id,ben_age,dob,dist_code,block_ulb_name,gp_ward_name,
                    next_level_role_id,id,
                    verification_rejected,created_at,
                    ben_fname,ben_mname,ben_lname,father_fname,father_mname,father_lname,is_verified,is_approved,is_rejected
                     from pension.beneficiaries where aadhar_no='" . $aadhar_no . "'";
                    $data = DB::connection('pgsql_mis')->select($query);
                    if (count($data) > 0) {
                        $array = array();
                        $i = 0;
                        foreach ($data as $row) {
                            $scheme_length = NULL;
                            $id_length = NULL;
                            $scheme_row = Scheme::where('is_active', 1)->where('id', $row->scheme_id)->first();
                            $scheme_name = $scheme_row->scheme_name;
                            $array[$i]['scheme_name'] = $scheme_name;
                            $scheme_length =  $scheme_row->scheme_length;
                            $id_length = $scheme_row->id_length;
                            $app_id = $row->created_by_dist_code . substr('0' . $row->scheme_id, -$scheme_length) . substr('0000000' . $row->id, -$id_length);
                            if (!empty($row->ben_fname)) {
                                $ben_fname = trim($row->ben_fname);
                            } else {
                                $ben_fname = '';
                            }
                            if (!empty($row->ben_mname)) {
                                $ben_mname = trim($row->ben_mname);
                            } else {
                                $ben_mname = '';
                            }
                            if (!empty($row->ben_lname)) {
                                $ben_lname = trim($row->ben_lname);
                            } else {
                                $ben_lname = '';
                            }
                            $ben_fullname = $ben_fname . " " . $ben_mname . " " . $ben_lname;
                            if (!empty($row->father_fname)) {
                                $father_fname = trim($row->father_fname);
                            } else {
                                $father_fname = '';
                            }
                            if (!empty($row->father_mname)) {
                                $father_mname = trim($row->father_mname);
                            } else {
                                $father_mname = '';
                            }
                            if (!empty($row->father_lname)) {
                                $father_lname = trim($row->father_lname);
                            } else {
                                $father_lname = '';
                            }
                            $father_fullname = $father_fname . " " . $father_mname . " " . $father_lname;
                            $array[$i]['ben_fullname'] = $ben_fullname;
                            $array[$i]['father_fullname'] = $father_fullname;
                            if (!empty($row->dist_code)) {
                                $district_arr = $district->where('district_code', $row->dist_code)->first();
                                $array[$i]['district_name'] = $district_arr->district_name;
                            } else {
                                $array[$i]['district_name'] = '';
                            }
                            $array[$i]['block_ulb_name'] = trim($row->block_ulb_name);
                            $array[$i]['gp_ward_name'] = trim($row->gp_ward_name);
                            $array[$i]['beneficiary_id'] = $row->id;
                            $array[$i]['application_id'] = $app_id;
                            $array[$i]['verification_rejected'] = $row->verification_rejected;
                            $array[$i]['ben_age'] = trim($row->ben_age);
                            if (!empty($row->father_lname)) {
                                $array[$i]['dob'] = trim($row->dob);
                            } else {
                                $array[$i]['dob'] =  '';
                            }

                            $message = '';
                            if ($row->is_rejected==1) {
                                $message = "Rejected";
                            }
                            else if ($row->is_verified==0 && $row->is_approved==0 && $row->is_rejected==0) {
                                $message = "Application submitted but yet to be Verified and Approved";
                            }  else if ($row->is_verified==1 && $row->is_approved==0 && $row->is_rejected==0) {
                                $message = "Verified but yet not Approved";
                            } else if ($row->is_verified==1 && $row->is_approved==1 && $row->is_rejected==0) {
                                $message = "Verified and Approved";
                            }
                            $array[$i]['message'] = $message;
                            $i++;
                        }
                    } else {
                        $errorMsg = 'No Record Found';
                    }
                }
            } else {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        return view('Aadhaar/search', [
            'list_arr'        => $array,
            'fill_array'        => $fill_array,
            'errorMsg'        => $errorMsg,
            'valid'        => $valid,
            'issubmitted'        => $issubmitted,
        ]);
    }
    catch (\Exception $e) {
        dd($e);
      }
    }
}
