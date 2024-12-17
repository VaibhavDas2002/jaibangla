<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ParijayiExport extends Model
{
    protected $connection = 'pgsql_sp';
    protected $table = 'ben_export';
    protected $primaryKey='id';
}