<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Parijayi extends Model
{
    protected $connection = 'pgsql_sp';
    protected $table = 'wbmt_beneficiary';
    protected $primaryKey='id';

    public function getCreatedAtAttribute($value)
    {
        $createdAt = Carbon::parse($value);
        return $createdAt->format('M d Y');
    }
}