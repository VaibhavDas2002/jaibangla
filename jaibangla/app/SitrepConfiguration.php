<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SitrepConfiguration extends Model
{ 
    protected $table = 'sitrep_config';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
