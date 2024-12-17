<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManabikBankUnique extends Model
{
     public $timestamps = false;
     public $incrementing = false;
     protected $connection = 'pgsql4';
     protected $table = 'manabik_ben_bank_account_no_unique';
     protected $primaryKey = ['bank_code', 'bank_ifsc'];
     
}
