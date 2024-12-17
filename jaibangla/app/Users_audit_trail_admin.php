<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users_audit_trail_admin extends Model
{
    //

    protected $table = 'user_audit_trail_admin';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
