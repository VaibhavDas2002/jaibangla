<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Configduty;
use App\District;
use App\Scheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\UrbanBody;
use App\GP;
use App\RejectRevertReason;
use Carbon\Carbon;
use App\DsPhase;
use App\Workflow;
use App\Helpers\AuthChecker;
use App\SchemeStepRank;
class PensionReportControllerExcel extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->base_dob_chk_date = date('Y-m-d');
    }
    public function generate_excel(Request $request)
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 0);
        try{
       
        if (empty($request->scheme_id)) {
            return redirect('/')->with('error', 'Scheme Id Required');
        }
        if (!ctype_digit($request->scheme_id)) {
            return redirect('/')->with('error', 'Scheme Id Invalid');
        }
        $rural_urban=$request->rural_urban;
        $urban_body_code_app=$request->urban_block_code_app;
        
        if($request->municipality_code=='undefined')
        {
            $municipality='';
        }
        else{
            $municipality=$request->municipality_code;
        }
        if($request->from_date=='undefined')
        {
            $from_date='';
        }
        else{
            $from_date=$request->from_date;
        }
        if($request->to_date=='undefined')
        {
            $to_date='';
        }
        else{
            $to_date=$request->to_date;
        }
        $user_id = AuthChecker::getUserId();
        $scheme_id = $request->scheme_id;

        $duty_obj = Configduty::where('user_id', $user_id)->where('scheme_id', $scheme_id)->where('is_active',1)->first();
        $distCode = $duty_obj->district_code;
        $blockCode = $duty_obj->is_urban == 1 ? $duty_obj->urban_body_code : $duty_obj->taluka_code;
        if(empty($duty_obj)){
            return redirect('/')->with('error', 'User not Authorized for this scheme');
        }
       
        $is_urban = $duty_obj->is_urban;
        $mapping_level = $duty_obj->mapping_level;
        $condition = array();
        $condition["scheme_id"] = $scheme_id;
        $designation_id = Auth::user()->designation_id;
        if ($mapping_level=='District') {
            //dd(123);
            $condition["created_by_dist_code"] = $distCode;
        }
        if ($mapping_level=='Block' || $mapping_level=='Subdiv') {
            //dd(333);
            $condition["created_by_dist_code"] = $distCode;
            $condition["created_by_local_body_code"] = $blockCode;
        }
//////////////////////////Debjit/////////////////////////////////////////

if (!empty($rural_urban)) {
    // $condition[$contact_table . ".rural_urban_id"] = $is_urban;
    if ($rural_urban == 2) {
        //dd(44);
        if (!empty($urban_body_code_app)) {
            //$condition["rural_urban_id"] = 2;
            $condition["created_by_local_body_code"] = $urban_body_code_app;
        }
    }
    //'Urban'
    if ($rural_urban == 1) {
        
        if (!empty($urban_body_code_app)) {
            //dd(44);
            //$condition["rural_urban_id"] = 1;
            $condition["created_by_local_body_code"] = $urban_body_code_app;
            //$download_excel = 1;
        }
        if (!empty($municipality)) {
            $condition["block_ulb_code"] = $municipality;
            //$download_excel = 1;
        }
    }
}

if (!empty($municipality) ) {
    $condition["block_ulb_code"] = $municipality;
    //$download_excel = 1;
}
if (!empty($request->gp_ward_code_app)) {
    $condition["gp_ward_code"] = $request->gp_ward_code_app;
   // $download_excel = 1;
}

// if (!empty($request->phase)) {
//     $condition["ds_phase"] = $request->phase;
//    // $download_excel = 1;
// }
/////////////////////////////Debjit End /////////////////////////////////////


        $scheme_name_row = Scheme::where('id', $scheme_id)->first();
        $scheme_name = $scheme_name_row->scheme_name;

        $report_type = $request->type;
        if ($request->has('type')) {
            $report_type = $request->get('type');
            if ($report_type == 'F') {
                $report_type_name = 'Yet to be Verified and Yet to be Approved Beneficiary List';
                // $condition['next_level_role_id']='is not null';
            } else if ($report_type == 'V') {
                $report_type_name = 'Verified but Yet to be Approved Beneficiary List';
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
            } else if ($report_type == 'NSAP') {
                $report_type_name = 'NSAP Mark Beneficiary List';
            }
            else {
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
            //dd($report_type);
            $query = DB::connection('pgsql_mis')->table('pension.beneficiaries')->where($condition);
            //Report Type Filter
            // dd($query);
            if (!empty($request->phase) && $request->phase_code>0) {
                $query = $query->whereRaw(' (ds_phase=' . $request->phase . ' or  cur_mark_ds_phase=' . $request->phase_code . ') ');
            }
            if(!empty($request->caste)){
                $query = $query->where('caste', $request->caste);
            }
            if ($request->phase_code==-1) {
                $query = $query->whereRaw(' ds_phase IS NULL and sm_ds_mark IS NULL');
            }
            if (!empty($from_date)) {
                $query = $query->whereraw(" date(created_at)>='$from_date'");
            }
            if (!empty($to_date)) {
                $query = $query->whereraw(" date(created_at)<='$to_date'");
            }

            if ($report_type == 'F') { // Fresh List
                $next_level_role_id_operator=SchemeStepRank::getSchemeParentId($scheme_id, 1);
                $query = $query->where('next_level_role_id',$next_level_role_id_operator);
                
            }
            if ($report_type == 'T') {
                $query = $query->where('is_rejected',1);
            }
            if ($report_type == 'V') { //Verified List
                if ($scheme_id == 17) { //For Purohit
                    $query = $query->where('next_level_role_id', 107);
                } else {
                    $query = $query->where('is_verified',1)->where('is_approved',0)->where('is_rejected',0);
                }
            }
            if ($report_type == 'NSAP') {
                $query = $query->where('process_nsap_flag', 1);
            }
           // dd($query->tosql());
            $data = $query->orderBy('ben_fname')->orderBy('gp_ward_name')->get(
                [
                    'id','created_by_dist_code','created_by_local_body_code','scheme_id',
                    'block_ulb_code','gp_ward_code','gp_ward_name','block_ulb_name',
                    'next_level_role_id','is_verified','is_approved','is_rejected',
                    'process_nsap_flag',
                    'ben_fname','ben_mname','ben_lname','mobile_no','dob','caste','aadhar_no','bank_ifsc','bank_code',
                    'father_fname','father_mname','father_lname',
                    'first_payment_pushed_at','first_payment_success_at','sm_ds_mark','mark_ds_phase','village_town_city',
                    'house_premise_no','post_office','pincode','ds_phase','cur_mark_ds_phase'
                ]
            );
            //($data);
             
            $filename = $scheme_name . "-" . $report_type_name . "-" . date('d/m/Y') . '-' . time() . ".xls";
            header("Content-Type: application/xls");
            header("Content-Disposition: attachment; filename=" . $filename);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo '<table border="1">';
            if($report_type == 'NSAP' || $report_type == 'C'){
                echo '<tr><th>Applicant Id</th><th>Applicant Name</th><th>Applicant Mobile No.</th><th>Applicant DOB.</th><th>Age</th><th>Caste</th><th>Aadhaar NO.</th><th>Bank IFSC</th><th>Bank Account No.</th><th>Father\'s Name</th><th>Block/Municipality</th><th>GP/WARD</th><th>Village/Town/City</th><th>House Premise No</th><th>Post Office</th><th>PIN Code</th><th>Status</th></tr>';
            }
            else if($report_type == 'A' && ($scheme_id=='1' || $scheme_id=='3'))
            {
               
     echo '<tr><th>Beneficiary Id</th><th>Applicant Name</th><th>Applicant Mobile No.</th><th>Applicant DOB.</th><th>Age</th><th>Caste</th><th>Aadhaar NO.</th><th>Bank IFSC</th><th>Bank Account No.</th><th>Father\'s Name</th><th>Block/Municipality</th><th>GP/WARD</th><th>Village/Town/City</th><th>House Premise No</th><th>Post Office</th><th>PIN Code</th>
     <th>First Payment Initiated Date</th><th>First Payment Success Date</th>
     <th>Duare Sarkar Phase</th></tr>';

    //  <th>Sent For Account Validation Date</th><th>Account Validation Success Date</th>
                    
            }
            else{
            echo '<tr><th>Beneficiary Id</th><th>Applicant Name</th><th>Applicant Mobile No.</th><th>Applicant DOB.</th><th>Age</th><th>Caste</th><th>Aadhaar NO.</th><th>Bank IFSC</th><th>Bank Account No.</th><th>Father\'s Name</th><th>Block/Municipality</th><th>GP/WARD</th><th>Village/Town/City</th><th>House Premise No</th><th>Post Office</th><th>PIN Code</th><th>Duare Sarkar Phase</th></tr>';
            }
            //  dd($data);
            if (count($data) > 0) {
                foreach ($data as $row) {
                   // $app_id = $row->created_by_dist_code . substr('0' . $row->scheme_id, -$scheme_length) . substr('0000000' . $row->id, -$id_length);
                    $app_id = "'$row->id'";
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
                    // if(!empty($row->acc_validation_pushed_at))
                    // {

                    // $acc_validation_pushed_at=date("d-m-Y", strtotime($row->acc_validation_pushed_at));
                    // }
                    // else{
                    // $acc_validation_pushed_at='';
                    // }
                    // if(!empty($row->acc_validation_success_at))
                    // {

                    // $acc_validation_success_at=date("d-m-Y", strtotime($row->acc_validation_success_at));
                    // }
                    // else{
                    // $acc_validation_success_at='';
                    // }
                    if(!empty($row->first_payment_pushed_at))
                    {

                    $first_payment_pushed_at=date("d-m-Y", strtotime($row->first_payment_pushed_at));
                    }
                    else{
                    $first_payment_pushed_at='';
                    }
                    if(!empty($row->first_payment_success_at))
                    {

                    $first_payment_success_at=date("d-m-Y", strtotime($row->first_payment_success_at));
                    }
                    else{
                    $first_payment_success_at='';
                    }





                    if (!empty($row->ben_lname)) {
                        $ben_lname = trim($row->ben_lname);
                    } else {
                        $ben_lname = '';
                    }
                    // if (!empty($row->ds_phase)) {
                    //     $phase_des = $this->getPhaseDes($row->ds_phase);
                    // } else {
                    //     $phase_des = '';
                    // }




                    if($row->scheme_id==10) {
                        if($row->sm_ds_mark==1)
                        {
                            $phase_des =$this->getPhaseDes($row->cur_mark_ds_phase);
                            $phase_des = $phase_des.' Marked';

                        }
                        else{

                            $phase_des = $this->getPhaseDes($row->ds_phase);
                        }
                    }
                    else if (!empty($row->ds_phase)) {
                            $phase_des = $this->getPhaseDes($row->ds_phase);
                        
                    }
                    else{

                        $phase_des = '';
                    }


                    $ben_fullname = $ben_fname . " " . $ben_mname . " " . $ben_lname;
                    if (!empty($row->mobile_no)) {
                        $ben_mobile_no = $row->mobile_no;
                    
                    } else {
                        $ben_mobile_no = '';
                    }
                    if (!empty($row->dob)) {
                        $ben_dob = $row->dob;
                        $ben_age=$this->ageCalculate($row->dob);
                    } else {
                        $ben_dob = '';
                        $ben_age='';
                    }
                    if(!empty($row->caste)){
                        if ($row->caste == 1 || $row->caste == 2 || $row->caste == 3 || $row->caste == 4 || $row->caste == 5 ||  $row->caste == NULL) {
                            $caste = 'Not Defined';
                        }else {
                            $caste = $row->caste;
                        }
                    }
                    else{
                        $caste ='';
                    }
                    if (!empty($row->aadhar_no)) {
                        $ben_aadhar_no = '********'.substr(trim($row->aadhar_no),0,3);
                    
                    } else {
                        $ben_aadhar_no = '';
                    }
                    if (!empty(trim($row->bank_ifsc)))
                    $f_bank_ifsc = trim($row->bank_ifsc);
                      else
                    $f_bank_ifsc = '';
                    if (!empty(trim($row->bank_code)))
                        $f_bank_code = '********'.substr(trim($row->bank_code),0,3);
                    else
                        $f_bank_code = '';
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
                    if ($report_type == 'NSAP') {
                        if(!is_null($row->next_level_role_id) && $row->next_level_role_id==0){
                            $status = 'Approved';
                           }
                        else if($row->is_verified==1 and $row->is_approved==0 and $row->is_rejected==0){
                            $status = 'Verified';
                        }
                        else if(is_null($row->next_level_role_id)){
                            
                             $status = 'NSAP Marked';  
                            
                        }
                        else if ($row->is_rejected==1) {
                            $status =  'Rejected';
                        } 
                    }
                      if ($report_type == 'C') {
                            
                                if(!is_null($row->next_level_role_id) && $row->next_level_role_id==0){
                                    if($data->dup_bank==1){
                                        $status = 'Approved but due to Duplicate Bank A/c, payment has been stopped';
                                    }
                                    else
                                    $status = 'Approved';
                                   }
                                else  if($row->is_verified==1 and $row->is_approved==0 and $row->is_rejected==0){
                                    $status = 'Verified';
                                }
                             
                                else if ($row->is_rejected==1) {
                                    $status =  'Rejected';
                                }  
                                else{
                                    $status = 'Fresh';
                                }
                           
                        }
                    if($report_type == 'NSAP' || $report_type == 'C'){
                        echo "<tr><td>" . $app_id . "</td><td>" . $ben_fullname . "</td><td>" . $ben_mobile_no . "</td><td>" . $ben_dob . "</td><td>" . $ben_age . "</td><td>" . $caste . "</td><td>" . $ben_aadhar_no . "</td><td>" . $f_bank_ifsc . "</td><td>" . $f_bank_code . "</td><td>" . $father_fullname . "</td><td>" . trim($row->block_ulb_name) . "</td><td>" . trim($row->gp_ward_name) . "</td><td>" . trim($row->village_town_city) . "</td><td>" . trim($row->house_premise_no) . "</td><td>" . trim($row->post_office) . "</td><td>" . trim($row->pincode) . "</td><td>" . $status . "</td></tr>";

                    }
                    else if($report_type == 'A' && ($scheme_id=='1' || $scheme_id=='3')){
             echo "<tr><td>" . $app_id . "</td><td>" . $ben_fullname . "</td><td>" . $ben_mobile_no . "</td><td>" . $ben_dob . "</td><td>" . $ben_age . "</td><td>" . $caste . "</td><td>" . $ben_aadhar_no . "</td><td>" . $f_bank_ifsc . "</td><td>" . $f_bank_code . "</td><td>" . $father_fullname . "</td><td>" . trim($row->block_ulb_name) . "</td><td>" . trim($row->gp_ward_name) . "</td><td>" . trim($row->village_town_city) . "</td><td>" . trim($row->house_premise_no) . "</td><td>" . trim($row->post_office) . "</td><td>" . trim($row->pincode) . "</td><td>" . $first_payment_pushed_at . "</td><td>" . $first_payment_success_at . "</td>
                        <td>" . $phase_des . "</td>
                        </tr>"; 
                        // <td>" . $acc_validation_pushed_at . "</td><td>" . $acc_validation_success_at . "</td>
                        }
                    else{
                    echo "<tr><td>" . $app_id . "</td><td>" . $ben_fullname . "</td><td>" . $ben_mobile_no . "</td><td>" . $ben_dob . "</td><td>" . $ben_age . "</td><td>" . $caste . "</td><td>" . $ben_aadhar_no . "</td><td>" . $f_bank_ifsc . "</td><td>" . $f_bank_code . "</td><td>" . $father_fullname . "</td><td>" . trim($row->block_ulb_name) . "</td><td>" . trim($row->gp_ward_name) . "</td><td>" . trim($row->village_town_city) . "</td><td>" . trim($row->house_premise_no) . "</td><td>" . trim($row->post_office) . "</td><td>" . trim($row->pincode) . "</td><td>" . $phase_des . "</td></tr>"; 
                    }
                }
            } else {
                echo '<tr><td colspan="11">No Records found</td></tr>';
            }
            echo '</table>';
        } else {
            return redirect('/')->with('error', 'Scheme Id Not Found');
        }
    } catch (\Exception $e) {
        dd($e);
    
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

    public function generate_excel_phasewise(Request $request) {
        if (empty($request->scheme_id)) {
            return redirect('/')->with('error', 'Scheme Id Required');
        }
        if (!ctype_digit($request->scheme_id)) {
            return redirect('/')->with('error', 'Scheme Id Invalid');
        }

        $scheme_id = $request->scheme_id;
        $is_active = 0;
        $roleArray = Configduty::where('user_id', Auth::user()->id)->where('is_active', 1)->get()->toArray() ;       
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
        $designation_id = Auth::user()->designation_id;
        if ($designation_id == 'Approver') {
            $condition["created_by_dist_code"] = $district_code;
        }
        if ($designation_id == 'Verifier' || $designation_id == 'Operator') {
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
                $query = $query->where('is_rejected',1);
            }
            if ($report_type == 'V') { //Verified List
                if ($scheme_id == 17) { //For Purohit
                    $query = $query->where('next_level_role_id', 107);
                } else {
                    $query = $query->where('is_verified',1)->where('is_approved',0)->where('is_rejected',0);
                }
            }
            $query = $query->where('wp_phase', 2);
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
                'bank_code',
                'house_premise_no',
                'post_office',
                'pincode' 
            )->orderBy('ben_fname')->orderBy('gp_ward_name')->get();
            // dd($data->toArray());
            $filename = $scheme_name . "-" . $report_type_name . "-" . date('d/m/Y') . '-' . time() . ".xls";
            header("Content-Type: application/xls");
            header("Content-Disposition: attachment; filename=" . $filename);
            header("Pragma: no-cache");
            header("Expires: 0");
            echo '<table border="1">';
            echo '<tr><th>Applicant Id</th><th>Applicant Name</th><th>Applicant Mobile No.</th><th>Father\'s Name</th><th>Age</th><th>Block/Municipality</th><th>GP/WARD</th><th>Village/Town/City</th><th>House Premise No</th><th>Post Office</th><th>PIN Code</th><th>Bank IFSC</th><th>Bank Account No.</th></tr>';
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
                    

                    //$phase_des = $this->getPhaseDes($row->ds_phase);
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

                    echo "<tr><td>" . $app_id . "</td><td>" . $ben_fullname . "</td><td>" . $row->mobile_no . "</td><td>" . $father_fullname . "</td><td>" . $row->ben_age . "</td><td>" . trim($row->block_ulb_name) . "</td><td>" . trim($row->gp_ward_name) . "</td><td>" . trim($row->village_town_city) . "</td><td>" . trim($row->house_premise_no) . "</td><td>" . trim($row->post_office) . "</td><td>" . trim($row->pincode) . "</td><td>" . trim($row->bank_ifsc) . "</td><td>" . $f_bank_code . "</td></tr>";
                }
            } else {
                echo '<tr><td colspan="10">No Records found</td></tr>';
            }
            echo '</table>';
        } else {
            return redirect('/')->with('error', 'Scheme Id Not Found');
        }
    }
    function getPhaseDes($phase_code)
    {
        $phaseArr = DsPhase::where('phase_code', $phase_code)->first();
        //$phaselist = Config::get('constants.ds_phase.phaselist');
        $des = '';
        if (!empty($phaseArr)) {
            $des = $phaseArr->phase_des;
        }
        return $des;
    }
}
