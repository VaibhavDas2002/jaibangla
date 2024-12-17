<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PensionOAPST extends Model
{
     protected $connection = 'pgsql';
     protected $table = 'oap_st.beneficiary';
     protected $primaryKey='id';

    protected $scheme_length = 2; 
    protected $id_length = 8; 

    

    public function getBenidAttribute()
    {
          return $this->created_by_dist_code.substr('0'.$this->scheme_id, -$this->scheme_length).substr('0000000'.$this->id, -$this->id_length);
    }

     public function getName()
    {
          return "{$this->ben_fname} {$this->ben_mname}{$this->ben_lname}";
    }
       
    // public function majorProgammeHeadMaster()
    // {
        
    //     return $this->belongsTo('App\majorProgammeHeadMaster','nhm_major_programme_head');
    // }

    //  public function programmeHeadMaster()
    // {
        
    //     return $this->belongsTo('App\programmeHeadMaster','nhm_programme_head');
    // }

    //  public function designationMaster()
    // {
        
    //     return $this->belongsTo('App\designationMaster','designation_list');
    // }
    //  public function Districtpresent()
    // {
        
    //     return $this->belongsTo('App\District','present_address_district','district_code');
    // }
    //  public function Districtpermanent()
    // {
        
    //     return $this->belongsTo('App\District','permanet_address_district','district_code');
    // }
}
