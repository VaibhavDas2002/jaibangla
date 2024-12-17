<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WPAdhaarUnique extends Model
{
     public $timestamps = false;
     public $incrementing = false;
     protected $connection = 'pgsql13';
     protected $table = 'ben_aadhar_no_unique';
     protected $primaryKey='aadhar_no';
}
