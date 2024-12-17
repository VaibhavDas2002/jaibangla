<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  


class NHMDuplicatedataController extends Controller
{
    public function verified_not_approved(){

    	$results=DB::select(DB::raw("
SELECT nhm_employee_details.id as app_id, 
CASE
WHEN posting_level='SPMU' THEN CONCAT(posting_place)
WHEN posting_level='State Institute of Health and Family Welfare' THEN CONCAT(posting_place)
WHEN posting_level='DPMU' THEN CONCAT(posting_place,',', m_district.district_name )
WHEN posting_level='BPMU' THEN CONCAT(posting_place,',',temp_taluka.taluka_name,',',temp_taluka.district_name) 
WHEN posting_level='ULB' THEN
	CASE 
		 WHEN posting_place = temp_urban_body.urban_body_name THEN CONCAT(posting_place,',',temp_urban_body.district_name)
	     ELSE CONCAT(posting_place,',',temp_health_facility.location_name,', ',temp_health_facility.district_name)
	END

WHEN posting_level='CPMU' THEN CONCAT(posting_place,',',temp_urban_body.urban_body_name,',',temp_urban_body.district_name)
WHEN posting_level='Hospital' THEN CONCAT(posting_place,',',temp_health_facility.location_name,',',temp_health_facility.district_name)
WHEN posting_level='Subcenter' THEN CONCAT(posting_place,',',temp_health_facility.location_name,',',temp_health_facility.district_name)
WHEN posting_level='DH'  THEN CONCAT(posting_place,',',temp_health_facility.location_name,',',temp_health_facility.district_name)
WHEN posting_level='SDH' THEN CONCAT(posting_place,',',temp_health_facility.location_name,',',temp_health_facility.district_name)
WHEN posting_level='SGH' THEN CONCAT(posting_place,',',temp_health_facility.location_name,',',temp_health_facility.district_name)
WHEN posting_level='PHC' THEN CONCAT(posting_place,',',temp_health_facility.location_name,',',temp_health_facility.district_name)
WHEN posting_level='SSH' THEN CONCAT(posting_place,',',temp_health_facility.location_name,',',temp_health_facility.district_name)
WHEN posting_level='UPHC' THEN CONCAT(posting_place,',',temp_health_facility.location_name,',',temp_health_facility.district_name)
WHEN posting_level='Other Hospital' THEN CONCAT(posting_place,',',temp_health_facility.location_name,',',temp_health_facility.district_name)
WHEN posting_level='CHC' THEN CONCAT(posting_place,',',temp_health_facility.location_name,',',temp_health_facility.district_name)
WHEN posting_level='MCH'  THEN CONCAT(posting_place,',',temp_health_facility.location_name,',',temp_health_facility.district_name)
WHEN posting_level='ACMOH Office' THEN
	CASE 
		 WHEN posting_place = temp_sub_district.sub_district_name THEN CONCAT(posting_place,',',temp_sub_district.district_name)
	     ELSE CONCAT(posting_place,',',temp_health_facility.location_name,', ',temp_health_facility.district_name)
	END
WHEN posting_level='State Drug Store' THEN
	CASE 
		 WHEN body_code=1 THEN CONCAT(posting_place,',','State')
	     ELSE CONCAT(posting_place,', ',m_district.district_name)
	END
END AS posting,* 
FROM 

(SELECT * FROM nhm_employee_details WHERE mobile_number_1 IN (
SELECT mobile_number_1 FROM (
SELECT * FROM nhm_employee_details WHERE verification_status = 'Verified' AND approval_status='Not Approved') as t
	GROUP BY mobile_number_1 HAVING COUNT(mobile_number_1)>1)
ORDER BY mobile_number_1) AS nhm_employee_details




LEFT JOIN m_district as m_district ON nhm_employee_details.body_code=m_district.district_code 
AND (nhm_employee_details.posting_level='DPMU' OR nhm_employee_details.posting_level='State Drug Store'
	 OR nhm_employee_details.posting_level='ACMOH Office') 

LEFT JOIN (
select  m_district.district_name as district_name,m_district.district_code as district_code,m_taluka.taluka_name as taluka_name,
	m_taluka.taluka_code as taluka_code
from m_taluka left join m_district on m_taluka.district_code=m_district.district_code  
order by district_name,taluka_name
)temp_taluka ON  nhm_employee_details.body_code=temp_taluka.taluka_code AND nhm_employee_details.posting_level='BPMU'


LEFT JOIN(
select  m_district.district_name as district_name,m_district.district_code as district_code,m_urban_body.urban_body_name as urban_body_name,
	m_urban_body.urban_body_code as urban_body_code
from m_urban_body left join m_district on m_urban_body.district_code=m_district.district_code  
order by district_name,urban_body_name
)temp_urban_body ON nhm_employee_details.posting_place_code=temp_urban_body.urban_body_code OR nhm_employee_details.body_code=temp_urban_body.urban_body_code
AND (nhm_employee_details.posting_level='ULB' OR nhm_employee_details.posting_level='CPMU')

LEFT JOIN(
select  m_district.district_name as district_name,m_district.district_code as district_code,
m_sub_district.sub_district_name as sub_district_name,
m_sub_district.sub_district_code as sub_district_code
from m_sub_district left join m_district on m_sub_district.district_code=m_district.district_code  
order by district_name,sub_district_name
)temp_sub_district ON nhm_employee_details.posting_place_code=temp_sub_district.sub_district_code AND nhm_employee_details.posting_level='ACMOH Office'


LEFT JOIN (
select  district_name,m_district.district_code,facility_name,facilty_code, 
CASE WHEN m_urban_body.urban_body_name IS NOT NULL THEN urban_body_name
     WHEN m_taluka.taluka_name IS NOT NULL THEN m_taluka.taluka_name
END as location_name,
CASE WHEN m_urban_body.urban_body_code IS NOT NULL THEN m_urban_body.urban_body_code 	
	 WHEN m_taluka.taluka_code IS NOT NULL THEN m_taluka.taluka_code
END as location_code
from m_health_facility 
left join m_district on m_health_facility.district_code=m_district.district_code
left join m_urban_body on m_health_facility.taluka_code=m_urban_body.urban_body_code
left join m_taluka on m_health_facility.taluka_code=m_taluka.taluka_code
order by district_name,urban_body_name,taluka_name
)temp_health_facility ON nhm_employee_details.posting_place_code=temp_health_facility.facilty_code AND(

nhm_employee_details.posting_level='Hospital' OR nhm_employee_details.posting_level='Subcenter' OR nhm_employee_details.posting_level='DH'
OR nhm_employee_details.posting_level='SDH' OR nhm_employee_details.posting_level='SGH' OR nhm_employee_details.posting_level='PHC'
OR nhm_employee_details.posting_level='SSH' OR nhm_employee_details.posting_level='UPHC' OR nhm_employee_details.posting_level='ULB'
OR nhm_employee_details.posting_level='Other Hospital'
OR nhm_employee_details.posting_level='CHC'
OR nhm_employee_details.posting_level='MCH'
OR nhm_employee_details.posting_level='ACMOH Office') 


WHERE  posting_level='SPMU' OR posting_level='MCH' OR posting_level='ACMOH Office' OR posting_level='State Drug Store' OR 
posting_level='DPMU' OR posting_level='BPMU' OR posting_level='State Institute of Health and Family Welfare'  
OR  posting_level='ULB' OR posting_level='Hospital'
OR posting_level='Subcenter' OR posting_level='DH' OR posting_level='SDH' or posting_level='SGH' OR posting_level='PHC'
OR posting_level='SSH' OR posting_level='UPHC' OR posting_level='Other Hospital'OR posting_level='CPMU' OR  posting_level='CHC' "));

//dd($results);
	return view('duplicate_verified_not_approved')
    	->with('results',$results)
    	;

    }



}
