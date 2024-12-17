<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DSRejecedApplicationSc extends Model
{
    protected $connection = 'pgsql2';
    protected $table = 'ds_reject_application';
    protected $primaryKey = 'id';
}
