<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\nhm_employee_details;
use App\District;
use App\UrbanBody;
use App\Taluka;
use App\nhm_health_facility;

class SubmittedApprovecController extends Controller
{
    public function approvalResult(){

    	


$statues_result = DB::select( DB::raw("SELECT (SELECT COUNT(*) FROM nhm_employee_details) AS Application_submitted,
(SELECT COUNT(*) FROM nhm_employee_details WHERE body_code= m_district.district_code AND verification_status='Verified') AS district_level_Verified,
(SELECT COUNT(*) FROM nhm_employee_details WHERE body_code= m_district.district_code AND verification_status='Not Verified') AS district_level_Not_Verified,
(SELECT COUNT(*) FROM nhm_employee_details WHERE body_code= m_district.district_code AND verification_status='Rejected') AS district_level_Not_Rejected,
(SELECT COUNT(*) FROM nhm_employee_details WHERE body_code= m_district.district_code AND approval_status='Not Approved' AND verification_status='Verified') AS district_level_Approval_pending,
(SELECT COUNT(*) FROM nhm_employee_details WHERE body_code= m_district.district_code AND approval_status= 'Approved' AND verification_status='Verified') AS district_level_Approved,
(SELECT COUNT(*) FROM nhm_employee_details WHERE body_code= m_district.district_code AND approval_status= 'Disapproved' AND verification_status='Verified') AS district_level_Disapproved,

(SELECT COUNT(*) FROM m_taluka  INNER JOIN nhm_employee_details ON nhm_employee_details.body_code= m_taluka.taluka_code AND m_taluka.district_code=m_district.district_code AND verification_status='Verified') AS block_level_Verified,
(SELECT COUNT(*) FROM m_taluka  INNER JOIN nhm_employee_details ON nhm_employee_details.body_code= m_taluka.taluka_code AND m_taluka.district_code=m_district.district_code AND verification_status='Not Verified') AS block_Not_Verified,
(SELECT COUNT(*) FROM m_taluka  INNER JOIN nhm_employee_details ON nhm_employee_details.body_code= m_taluka.taluka_code AND m_taluka.district_code=m_district.district_code AND verification_status='Rejected') AS block_level_Rejected,
(SELECT COUNT(*) FROM m_taluka  INNER JOIN nhm_employee_details ON nhm_employee_details.body_code= m_taluka.taluka_code AND m_taluka.district_code=m_district.district_code AND approval_status='Not Approved' AND verification_status='Verified') AS block_Approval_pending,
(SELECT COUNT(*) FROM m_taluka  INNER JOIN nhm_employee_details ON nhm_employee_details.body_code= m_taluka.taluka_code AND m_taluka.district_code=m_district.district_code AND approval_status= 'Approved' AND verification_status='Verified') AS block_level_Approved,
(SELECT COUNT(*) FROM m_taluka  INNER JOIN nhm_employee_details ON nhm_employee_details.body_code= m_taluka.taluka_code AND m_taluka.district_code=m_district.district_code AND approval_status= 'Disapproved' AND verification_status='Verified') AS block_level_Disapproved,

(SELECT COUNT(*) FROM m_urban_body  INNER JOIN nhm_employee_details ON nhm_employee_details.body_code= m_urban_body.urban_body_code AND m_urban_body.district_code=m_district.district_code AND verification_status='Verified') AS ulb_level_Verified,
(SELECT COUNT(*) FROM m_urban_body  INNER JOIN nhm_employee_details ON nhm_employee_details.body_code= m_urban_body.urban_body_code AND m_urban_body.district_code=m_district.district_code AND verification_status='Not Verified') AS ulb_level_Not_Verified,
(SELECT COUNT(*) FROM m_urban_body  INNER JOIN nhm_employee_details ON nhm_employee_details.body_code= m_urban_body.urban_body_code AND m_urban_body.district_code=m_district.district_code AND verification_status='Rejected') AS ulb_level_level_Rejected,
(SELECT COUNT(*) FROM m_urban_body  INNER JOIN nhm_employee_details ON nhm_employee_details.body_code= m_urban_body.urban_body_code AND m_urban_body.district_code=m_district.district_code AND approval_status='Not Approved' AND verification_status='Verified') AS ulb_level_Approval_pending,
(SELECT COUNT(*) FROM m_urban_body  INNER JOIN nhm_employee_details ON nhm_employee_details.body_code= m_urban_body.urban_body_code AND m_urban_body.district_code=m_district.district_code AND approval_status= 'Approved' AND verification_status='Verified') AS ulb_level_Approved,
(SELECT COUNT(*) FROM m_urban_body  INNER JOIN nhm_employee_details ON nhm_employee_details.body_code= m_urban_body.urban_body_code AND m_urban_body.district_code=m_district.district_code AND approval_status= 'Disapproved' AND verification_status='Verified') AS ulb_level_Disapproved,
district_name
 FROM m_district where district_name not in('Others')  ORDER BY district_name;"));



    	
    	return view('submitted_approved_report')
    	->with('statues_result',$statues_result)
    	;

    }
}


        	