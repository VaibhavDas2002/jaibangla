<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchemeCapacity extends Model
{
    protected $table = 'm_cap';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
