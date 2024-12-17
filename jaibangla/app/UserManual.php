<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserManual extends Model
{
    protected $table = 'user_manual';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
