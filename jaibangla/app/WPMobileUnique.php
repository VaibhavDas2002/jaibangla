<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WPMobileUnique extends Model
{
     public $timestamps = false;
     public $incrementing = false;
     protected $connection = 'pgsql13';
     protected $table = 'ben_mobile_no_unique';
     protected $primaryKey='mobile_no';
}
