<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SchemeDocMap extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'scheme_attached_doc';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
