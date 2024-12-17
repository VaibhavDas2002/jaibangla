<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sizure extends Model
{
    protected $table = 'sizure_details';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
