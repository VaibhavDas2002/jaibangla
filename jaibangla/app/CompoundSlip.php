<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompoundSlip extends Model
{
    protected $table = 'compound_slip_details';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
