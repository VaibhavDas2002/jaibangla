<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BenNameFailedlog extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    // protected $connection = 'pgsql5';
    protected $table = 'pension.ben_name_failed_log';
    // protected $primaryKey = 'ben_id';
}
