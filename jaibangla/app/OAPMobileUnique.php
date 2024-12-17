<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OAPMobileUnique extends Model
{
     public $timestamps = false;
     public $incrementing = false;
     protected $connection = 'pgsql12';
     protected $table = 'oap_ben_mobile_no_unique';
     protected $primaryKey='mobile_no';
}
