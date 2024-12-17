<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assembly extends Model
{
    protected $table = 'm_assembly';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
