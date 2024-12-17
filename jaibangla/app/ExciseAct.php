<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExciseAct extends Model
{
    //
    protected $table = 'excise_act_details';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
