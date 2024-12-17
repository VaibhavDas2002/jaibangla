<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UrbanBody extends Model
{
    protected $table = 'm_urban_body';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
