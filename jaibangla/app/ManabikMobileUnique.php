<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManabikMobileUnique extends Model
{
     public $timestamps = false;
     public $incrementing = false;
     protected $connection = 'pgsql4';
     protected $table = 'manabik_ben_mobile_no_unique';
     protected $primaryKey='mobile_no';
}
