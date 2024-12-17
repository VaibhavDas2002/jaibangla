<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MapLavel extends Model
{
    protected $table = 'm_roles';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


    public function schemename()
    {
        return $this->belongsTo('App\Scheme','scheme_id','id');
    }

    public function designationname()
    {
        return $this->belongsTo('App\Designation','role_id','id');
    }

    public function parentdesignationname()
    {
        return $this->belongsTo('App\MapLavel','parent_id','id');
    }

    public function parent()
    {
        return $this->hasOne('App\MapLavel','id','parent_id');
    }    

}
