<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Scheme extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'm_scheme';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
