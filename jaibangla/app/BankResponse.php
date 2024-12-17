<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BankResponse extends Model
{
    protected $connection = 'pgsql_sp';
    protected $table = 'bank_response';
    protected $primaryKey='id';
    protected $fillable = array('lot_no','sr_no','sequence_no','transaction_ref','amount','value_date','sending_branch_ifsc','sender_ac_type','sender_ac_no','sender_ac_name','benf_branch','benf_ac_type','benf_ac_no','benf_ac_name','txn_status','originator_of_remittance','sender_to_receiver_information','reason');


    public function getCreatedAtAttribute($value)
    {
        $createdAt = Carbon::parse($value);
        return $createdAt->format('M d Y');
    }
}