<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BenPaymentDetails extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $primaryKey  = 'ben_id';
    protected $connection = 'pgsql_paywrite';
    protected $table = 'payment.ben_payment_details';
}
