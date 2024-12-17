<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class nhm_health_facility extends Model
{
   protected $table = 'm_health_facility';
   protected $guarded = [];
   protected $primaryKey='id';
   public $timestamps = false;

   public function  District()
    {
        
        return $this->belongsTo('App\District','district_code','district_code');
    }

    public function taluka()
    {
        
        return $this->belongsTo('App\Taluka','taluka_code','taluka_code');
    }

  public function urban_body()
    {
        
        return $this->belongsTo('App\UrbanBody','taluka_code','urban_body_code');
    }
}
