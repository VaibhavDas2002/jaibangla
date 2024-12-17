<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class designationMaster extends Model
{
    protected $table = 'designation_master';
    protected $primaryKey='id';

    protected $guarded = [];

    public function nhm_service_category()
    {
        
        return $this->belongsTo('App\nhm_service_category','service_category_id');
    }
    public function majorProgammeHeadMaster()
    {
        
        return $this->belongsTo('App\majorProgammeHeadMaster','major_programme_head_id');
    }
    public function programmeHeadMaster()
    {
        
        return $this->belongsTo('App\programmeHeadMaster','programme_head_id');
    }

}
