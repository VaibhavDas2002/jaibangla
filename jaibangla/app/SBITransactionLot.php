<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SBITransactionLot extends Model
{
	protected $connection = 'pgsql8';
    protected $table = 'transaction_lot';
}
