<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BenFailedPaymentDetails extends Model implements Auditable
{
 
    use \OwenIt\Auditing\Auditable;
    protected $connection = 'pgsql_paywrite';
    protected $table = 'payment.failed_payment_details';

}
