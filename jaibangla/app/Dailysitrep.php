<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dailysitrep extends Model
{
    
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'case_details';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
