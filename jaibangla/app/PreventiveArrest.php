<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreventiveArrest extends Model
{
    protected $table = 'preventive_arrest_details';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
