<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class lot_master extends Model
{
    protected $table = 'lot_master';
    public $timestamps = false;

    public function Scheme()
    {
        
        return $this->belongsTo('App\Scheme','scheme_id','id');
    }
    // protected $primaryKey='id';

}
