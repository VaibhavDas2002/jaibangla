<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SBITransactionPayLoad extends Model
{
	protected $connection = 'pgsql8';
    protected $table = 'transaction_payload';
}