<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RejectRevertReason extends Model
{
    protected $table = 'm_reject_revert_reason_master';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
