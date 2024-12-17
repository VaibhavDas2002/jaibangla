<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class PaymentTransaction extends Model
{
    //
   
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_transaction';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
