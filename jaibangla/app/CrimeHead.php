<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrimeHead extends Model
{
    // use \OwenIt\Auditing\Auditable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crime_head';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
