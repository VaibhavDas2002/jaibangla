<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BenReviveRequest extends Model implements Auditable
{
    //
    use \OwenIt\Auditing\Auditable;
    // protected $connection = 'pgsql5';
    protected $table = 'pension.ben_revive_request';
    protected $primaryKey = 'ben_id';
}
