<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Sms extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
	 /**
     * The table associated with the model.
     *
     * @var string
     */
     protected $table = 'sms_template';
     
      /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $auditInclude = [
        'title',
        'content',
    ];

     protected $guarded = [];

}
