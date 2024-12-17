<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class applicationModel extends Model implements Auditable
{
	use \OwenIt\Auditing\Auditable;
    protected $table = 'pcc_application';
}
