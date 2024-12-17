<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GP;
use App\UrbanBody;
use App\Taluka;
use App\UpdateBenDetails;
use App\BeneficiaryPensions;
use App\BenDocsSc;
use App\BenDocsSt;
use App\DocumentType;
use App\Configduty;
use App\lot_master;
use App\MapLavel;
use App\Scheme;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Validator;
use Elibyy\TCPDF\Facades\TCPDF as PDF;

class ViewTrackApplicationStatusController extends Controller
{
  private function getSchemaName($scheme_id)
  {
    if (!is_null($scheme_id)) {
      $sObj =  Scheme::select('id', 'short_code')->where('id', '=', $scheme_id)->first();
      //$parameter['scheme_id'] = $scheme_id;
      $schema_name =  $sObj->short_code;
      //dd($schema_name);
      if (empty($schema_name)) {
        $schema_name = 'pension';
      }
      $table_name =  strtolower($schema_name) . '.beneficiary';
    } else {
      $table_name =  'pension.beneficiary';
    }
    return $table_name;
  }
  public function checkSchemeSession($scheme_id)
  {
    $roleArray = Session::get('role');
    foreach ($roleArray as $roleObj) {
      if ($roleObj['scheme_id'] == $scheme_id) {
        $is_active = 1;
        Session::put('level', $roleObj['mapping_level']);
        Session::put('distCode', $roleObj['district_code']);
        Session::put('scheme_id', $scheme_id);
        Session::put('is_first', $roleObj['is_first']);
        Session::put('is_urban', $roleObj['is_urban']);
        Session::put('role_id', $roleObj['id']);
        if ($roleObj['is_urban'] == 1) {
          Session::put('bodyCode', $roleObj['urban_body_code']);
        } else {
          Session::put('bodyCode', $roleObj['taluka_code']);
        }
        break;
      }
    }
  }
  /*
		Get First Landing Page
  */
  public function index(Request $request)
  {
    $ben_id = $request->application_id;
    $mobile_no = $request->mobile_no;
    $designation = 'SpecialStatusCheck';
    // dump($mobile_no);
    $num = User::where('designation_id_old', $designation)->where('mobile_no', $mobile_no)->count();
    
    if ($num > 0) {
      if (!is_null($ben_id)) {
        // Get Dynamic Schema Name scheme wise
        // $table_name = $this->getSchemaName($scheme_id);
        $query = '';
        $query = "select b.*, md.district_name, bl_div.block_subdiv_name, ms.scheme_name,  
          CASE 
            WHEN b.next_level_role_id is null THEN 'Applied (Verification Pending)' 
            WHEN b.next_level_role_id > 0	THEN 'Verified (Approval Pending)'
            WHEN b.next_level_role_id = 0 THEN 
                CASE 
                WHEN  b.dup_bank = 1 THEN 'Approved but due to Duplicate Bank A/c, payment has been stopped..' 
                ELSE 'Approved' 
                END
          WHEN b.next_level_role_id < 0 THEN 
                CASE 
                WHEN  b.next_level_role_id = -98 THEN 'Pause Payment' 
                ELSE 'Rejected' 
                END
                ELSE '' 
          END AS app_status 
          from pension.beneficiary b 
          JOIN public.m_district md ON md.district_code=b.created_by_dist_code 
          JOIN public.m_scheme ms ON ms.id=b.scheme_id  
          JOIN (SELECT block_code AS block_subdiv_code,block_name AS block_subdiv_name FROM public.m_block 
            UNION ALL
            SELECT sub_district_code AS block_subdiv_code, sub_district_name AS block_subdiv_name FROM 		public.m_sub_district
          ) bl_div ON bl_div.block_subdiv_code=b.created_by_local_body_code 
          where b.id=" . $ben_id . " ";
          $data = DB::connection('pgsql_mis')->select($query);
          // print_r($data);
          $finalTable = $this->viewApplicantTable($data);
        return view('trackApplicationStatus/viewTrackStatus', ['finalData' => $finalTable]);

      } else {
        return view('trackApplicationStatus/viewTrackStatus_Unauthrized',['msg' => 'Please send the application id.']);
      }
    } else {
      return view('trackApplicationStatus/viewTrackStatus_Unauthrized',['msg' => 'Access Denied. Unauthorized.']);
    }
  }

  private function viewApplicantTable($data) {
    $benDetailsHtml = '';
      if (count($data) > 0) {
        $benDetailsHtml .= '<table class="table table-bordered display compact paymentStaClass" id="paymentTable" cellspacing="0" style="font-size: 14px;" width="100%">
            <tbody>';
        foreach ($data as $key) {
          $benDetailsHtml .= "";
          $benDetailsHtml .= "<tr><th>Beneficiary ID</th> <td>" . $key->id . "</td></tr>";
          $benDetailsHtml .= "<tr><th>Scheme Name</th> <td>" . $key->scheme_name . "</td></tr>";
          $benDetailsHtml .= "<tr><th>Beneficiary Name</th> <td>" . $key->ben_fname . ' ' . $key->ben_mname . ' ' . $key->ben_lname . "</td></tr>";
          $benDetailsHtml .= "<tr><th>Father's Name</th> <td>" . $key->father_fname . ' ' . $key->father_mname . ' ' . $key->father_lname . "</td></tr>";
          $benDetailsHtml .= "<tr><th>Mobile No</th> <td>" . $key->mobile_no . "</td></tr>";
          $benDetailsHtml .= "<tr><th>Aadhar No</th> <td>" . $key->aadhar_no . "</td></tr>";

          $address = '';
          $address = 'District - ' . $key->district_name . ', ';
          if ($key->rural_urban_id == 1) {
            $address .= 'Sub-division - ' . $key->block_subdiv_name . ', ';
            $address .= 'Municipality - ' . $key->block_ulb_name . ', ';
            $address .= 'Ward - ' . $key->gp_ward_name;
          } else {
            $address .= 'Block - ' . $key->block_subdiv_name . ', ';
            $address .= 'GP - ' . $key->gp_ward_name;
          }
          $benDetailsHtml .= "<tr><th>Address</th> <td>" . $address . "</td></tr>";

          $bank = '';
          if (!is_null($key->bank_name)) {
            $bank .= 'Bank Name - ' . $key->bank_name . ', ';
          }
          if (!is_null($key->branch_name)) {
            $bank .= 'Branch - ' . $key->branch_name . ', ';
          }
          $bank .= 'A/c No - ' . $key->bank_code . ', ';
          $bank .= 'IFSC - ' . $key->bank_ifsc;
          $benDetailsHtml .= "<tr><th>Banking Information</th> <td>" . $bank . "</td></tr>";

          $pay = '';
          $pay .= 'Applied At - ' .  date("d-m-Y", strtotime($key->created_at)) . '<br>';
          $pay .= 'Payment Count - ' . $key->payment_count . '<br>';
          if ($key->last_paid_yymm == 0) {
            $final_date = '0';
          } else {
            $date = $key->last_paid_yymm;
            $arr = str_split($date, 2);
            $year = $arr[0];
            $month = $arr[1];
            $final_date = date('F', mktime(0, 0, 0, $month, 10)) . ' - 20' . $year;
          }
          $pay .= 'Last Paid (Month-Year) - <br>' . $final_date . '<br>';
          $benDetailsHtml .= "<tr><th>Payment Information</th> <td>" . $pay . "</td></tr>";

          $action = '';
          $action = '<span>' . $key->app_status . '</span><br>';
          if ($key->lot_generated == '-1' && $key->bank_edited == '0') {
            $action .= '<span style="font-weight: bold; font-style: italic;">Pending IFMS failed bank rectification <br/>from Block/Sub-division end</span>';
          }
          if ($key->lot_generated == '-2' && $key->bank_edited == '0') {
            $action .= '<span style="font-weight: bold; font-style: italic;">Pending RBI failed bank rectification <br/>from Block/Sub-division end</span>';
          }
          if ($key->lot_generated == '-3' && $key->bank_edited == '0') {
            $action .= '<spanstyle="font-weight: bold; font-style: italic;">Pending SBI failed bank rectification <br/>from Block/Sub-division end</span>';
          }
          $benDetailsHtml .= "<tr><th>Current Status</th> <td>" . $action . "</td></tr>";

        }
        $benDetailsHtml .= '</tbody></table>';
      } else {
        $benDetailsHtml .= '<table class="table table-bordered display compact" cellspacing="0" style="font-size: 14px;" width="100%">  
            <thead>
              <tr>
                <th style="text-align: center; ">No data found</th>
              </tr>
            </thead></table>';
      }
    return $benDetailsHtml;
  }
}
