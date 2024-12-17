<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cspolicestation extends Model
{
    protected $table = 'cs_police_station';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
