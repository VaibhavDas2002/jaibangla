<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClubbedSBITransactionLotDetails extends Model
{
    protected $connection = 'pgsql8';
    protected $table = 'clubbed_transaction_lot_details';
}
