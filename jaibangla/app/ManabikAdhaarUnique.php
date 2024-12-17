<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManabikAdhaarUnique extends Model
{
     public $timestamps = false;
     public $incrementing = false;
     protected $connection = 'pgsql4';
     protected $table = 'manabik_ben_aadhar_no_unique';
     protected $primaryKey='aadhar_no';
}
