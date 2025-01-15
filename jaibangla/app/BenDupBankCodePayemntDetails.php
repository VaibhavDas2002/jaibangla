<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class BenDupBankCodePayemntDetails extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    //   protected $connection = 'pgsql5';
    protected $table = 'pension.ben_payment_details_bank_code_dup';
    protected $primaryKey = 'id';
}
