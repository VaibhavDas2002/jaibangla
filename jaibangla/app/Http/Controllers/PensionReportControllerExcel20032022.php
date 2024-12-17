<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Configduty;
use App\District;
use App\Scheme;
use Auth;
use Config;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\UrbanBody;
use App\GP;
use App\RejectRevertReason;
use Carbon\Carbon;

class PensionReportControllerExcel extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function generate_excel(Request $request)
    {
        if (empty($request->scheme_id)) {
            return redirect('/')->with('error', 'Scheme Id Required');
        }
        if (!ctype_digit($request->scheme_id)) {
            return redirect('/')->with('error', 'Scheme Id Invalid');
        }

        $scheme_id = $request->scheme_id;
        $is_active = 0;
        $roleArray = $request->session()->get('role');
        foreach ($roleArray as $roleObj) {
            if ($roleObj['scheme_id'] == $scheme_id) {
                $is_active = 1;
                $mapping_level = $roleObj['mapping_level'];
                $district_code = $roleObj['district_code'];
                if ($roleObj['is_urban'] == 1) {
                    $urban_body_code = $roleObj['urban_body_code'];
                } else {
                    $urban_body_code = $roleObj['taluka_code'];
                }
                break;
            }
        }
        if ($is_active == 0) {
            return redirect('/')->with('error', 'User not Authorized for this scheme');
        }
        $condition = array();
        $designation_id_old = Auth::user()->designation_id_old;
        if ($designation_id_old == 'Approver') {
            $condition["created_by_dist_code"] = $district_code;
        }
        if ($designation_id_old == 'Verifier' || $designation_id_old == 'Operator') {
            $condition["created_by_dist_code"] = $district_code;
            $condition["created_by_local_body_code"] = $urban_body_code;
        }
        $scheme_name_row = Scheme::where('id', $scheme_id)->first();
        $scheme_name = $scheme_name_row->scheme_name;

        $report_type = $request->type;
        if ($request->has('type')) {
            $report_type = $request->get('type');
            if ($report_type == 'F') {
                $report_type_name = 'Fresh Beneficiary List';
                // $condition['next_level_role_id']='is not null';
            } else if ($report_type == 'V') {
                $report_type_name = 'Verified Beneficiary List';
                // $condition['next_level_role_id']='is not null';
            } else if ($report_type == 'A') {
                $report_type_name = 'Approved Beneficiary List';
                $condition['next_level_role_id'] = 0;
            } else if ($report_type == 'R') {
                $report_type_name = 'Recomended Beneficiary List';
                //Only For Purohit Scheme
                $condition['next_level_role_id'] = 106;
            } else if ($report_type == 'T') {
                $report_type_name = 'Rejected Beneficiary List';
                //      $condition['next_level_role_id'] = '-1';
            } else if ($report_type == 'C') {
                $report_type_name = 'Complete Beneficiary List';
            } else {
                return redirect('/')->with('error', 'Error: Report type invalid');
            }
        } else {
            return redirect('/')->with('error', 'Signature Error: Report Type not selected');
        }
        $scheme_length = NULL;
        $id_length = NULL;
        if (!empty($scheme_id)) {
            $scheme_row = Scheme::where('id', $scheme_id)->first();
            //dd($scheme_row->toArray());
            if (!empty($scheme_row)) {
                $scheme_schema = $scheme_row->short_code;
                if (!empty($scheme_schema)) {
                    $table = $scheme_schema;
                    // $query = DB::connection('pgsql_mis')->table('' . $table . '.beneficiary')->where($condition);
                    // $query = DB::table::on('pgsql_mis')->where($condition);
                } else {
                    $table = 'pension';
                }
                $scheme_length =  $scheme_row->scheme_length;
                $id_length = $scheme_row->id_length;
            } else {
                $table = 'pension';
            }
            $query = DB::connection('pgsql_mis')->table('' . $table . '.beneficiary')->where($condition);
            //Report Type Filter
            if ($report_type == 'F') { // Fresh List
                $query = $query->whereNull('next_level_role_id');
            }
            if ($report_type == 'T') {
                $query = $query->where('next_level_role_id', '<', 0);
            }
            if ($report_type == 'V') { //Verified List
                if ($scheme_id == 17) { //For Purohit
                    $query = $query->where('next_level_role_id', 107);
                } else {
                    $query = $query->where('next_level_role_id', '>', 0)->where('next_level_role_id', '!=', 9999);
                }
            }
            $data = $query->select(
                'id',
                'scheme_id',
                'created_by_dist_code',
                'ben_fname',
                'ben_mname',
                'ben_lname',
                'father_fname',
                'father_mname',
                'father_lname',
                'mother_fname',
                'mother_mname',
                'mother_lname',
                'mobile_no',
                'dob',
                'ben_age',
                'caste',
                'next_level_role_id',
                'block_ulb_name',
                'gp_ward_name',
                'village_town_city',
                'bank_ifsc',
                'bank_code'
            )->orderBy('ben_fname')->orderBy('gp_ward_name')->get();
            // dd($data->toArray());
            $filename = $scheme_name . "-" . $report_type_name . "-" . date('d/m/Y') . '-' . time() . ".xls";
            header("Content-Type: application/xls");
            header("Content-Disposition: attachment; filename=" . $filename);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo '<table border="1">';
            echo '<tr><th>Applicant Id</th><th>Applicant Name</th><th>Applicant Mobile No.</th><th>Father\'s Name</th><th>Age</th><th>Block/Municipality</th><th>GP/WARD</th><th>Village/Town/City</th><th>Bank IFSC</th><th>Bank Account No.</th></tr>';
            if (count($data) > 0) {
                foreach ($data as $row) {
                    $app_id = $row->created_by_dist_code . substr('0' . $row->scheme_id, -$scheme_length) . substr('0000000' . $row->id, -$id_length);
                    $app_id = "'$app_id'";
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
                    $bank_code = (string) $row->bank_code;
                    if (!empty($bank_code))
                        $f_bank_code = "'$bank_code'";
                    else
                        $f_bank_code = $bank_code;

                    echo "<tr><td>" . $app_id . "</td><td>" . $ben_fullname . "</td><td>" . $row->mobile_no . "</td><td>" . $father_fullname . "</td><td>" . $row->ben_age . "</td><td>" . trim($row->block_ulb_name) . "</td><td>" . trim($row->gp_ward_name) . "</td><td>" . trim($row->village_town_city) . "</td><td>" . trim($row->bank_ifsc) . "</td><td>" . $f_bank_code . "</td></tr>";
                }
            } else {
                echo '<tr><td colspan="10">No Records found</td></tr>';
            }
            echo '</table>';
        } else {
            return redirect('/')->with('error', 'Scheme Id Not Found');
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
