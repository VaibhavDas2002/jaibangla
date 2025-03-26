<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BenIncompleteLog extends Model  implements Auditable

{
     //
     use \OwenIt\Auditing\Auditable;
     // protected $connection = 'pgsql5';
     protected $table = 'pension.ben_incomplete_details_log';
     protected $primaryKey = 'ben_id';
}
