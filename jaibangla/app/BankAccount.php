<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
	protected $connection = 'pgsql8';
    protected $table = 'bank_account';
}
