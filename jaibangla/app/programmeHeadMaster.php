<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class programmeHeadMaster extends Model
{
    //
    public $timestamps = false;
    protected $table = 'programme_head_master';
    protected $guarded = [];

      public function nhm_service_category()
    {
        
        return $this->belongsTo('App\nhm_service_category','service_category_id');
    }
    public function majorProgammeHeadMaster()
    {
        
        return $this->belongsTo('App\majorProgammeHeadMaster','major_programme_head_id');
    }
}
