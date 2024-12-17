<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OAPAdhaarUnique extends Model
{
     public $timestamps = false;
     public $incrementing = false;
     protected $connection = 'pgsql12';
     protected $table = 'oap_ben_aadhar_no_unique';
     protected $primaryKey='aadhar_no';
}
