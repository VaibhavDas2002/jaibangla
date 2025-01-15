<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class AcceptRejectInfo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable; 
    //

    protected $table = 'ben_accept_reject_info';
    protected $primaryKey = 'id';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
