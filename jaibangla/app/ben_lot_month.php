<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ben_lot_month extends Model
{
     //protected $table = 'ben_export';
     //protected $guarded = [];

     //public $timestamps = false;
	protected $connection = 'pgsql_ifms';
    protected $table = 'transaction_lot_details';
}
