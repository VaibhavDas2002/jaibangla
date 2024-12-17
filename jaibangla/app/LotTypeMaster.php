<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LotTypeMaster extends Model
{
    protected $table='m_lot_type';
    protected $guarded = [];
   
    protected $fillable =['id','lot_type'];
}
