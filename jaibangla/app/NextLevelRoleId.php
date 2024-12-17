<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NextLevelRoleId extends Model
{

    protected $table = 'm_next_level_role_id_code';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
