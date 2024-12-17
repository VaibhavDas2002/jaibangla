<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DupliacteApproveReject extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'duplicate_approve_reject';
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
