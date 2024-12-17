<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DSRejecedApplicationSt extends Model
{
    protected $connection = 'pgsql3';
    protected $table = 'ds_reject_application';
    protected $primaryKey = 'id';
}
