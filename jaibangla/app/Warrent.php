<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warrent extends Model
{
    //
    protected $table = 'warrent_details';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
