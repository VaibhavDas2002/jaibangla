<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class nhm_employee_details extends Model
{
     protected $table = 'nhm_employee_details';
     protected $primaryKey='id';

    

       
    public function majorProgammeHeadMaster()
    {
        
        return $this->belongsTo('App\majorProgammeHeadMaster','nhm_major_programme_head');
    }

     public function programmeHeadMaster()
    {
        
        return $this->belongsTo('App\programmeHeadMaster','nhm_programme_head');
    }

     public function designationMaster()
    {
        
        return $this->belongsTo('App\designationMaster','designation_list');
    }
     public function Districtpresent()
    {
        
        return $this->belongsTo('App\District','present_address_district','district_code');
    }
     public function Districtpermanent()
    {
        
        return $this->belongsTo('App\District','permanet_address_district','district_code');
    }
}
