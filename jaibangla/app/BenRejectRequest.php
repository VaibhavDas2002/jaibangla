<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class BenRejectRequest extends Model implements Auditable
{
    //
    use \OwenIt\Auditing\Auditable;
    // protected $connection = 'pgsql5';
    protected $table = 'pension.ben_reject_request';
    protected $primaryKey = 'id';
}
