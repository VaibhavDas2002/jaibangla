<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MissingDetails extends Model
{
    protected $table = 'missing_details';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
