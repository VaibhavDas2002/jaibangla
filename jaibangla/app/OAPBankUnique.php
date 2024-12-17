<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OAPBankUnique extends Model
{
     public $timestamps = false;
     public $incrementing = false;
     protected $connection = 'pgsql12';
     protected $table = 'oap_ben_bank_account_no_unique';
     protected $primaryKey = ['bank_code', 'bank_ifsc'];
     
}
