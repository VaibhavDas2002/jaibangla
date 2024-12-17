<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BankDetails extends Model
{
    protected $connection = 'pgsql_ifsc';
    protected $table = 'bank_details';      
    protected $primaryKey='ifsc';
    public $timestamps = false;

}


