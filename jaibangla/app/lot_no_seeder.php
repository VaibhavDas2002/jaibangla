<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class lot_no_seeder extends Model
{
    protected $table = 'lot_no_seeder';
    public $timestamps = false;
    // protected $primaryKey='id';

    protected $drn_length = 6;   

    public function getDrnAttribute()
    {
          return substr('00000'.$this->lot_no, -$this->drn_length);
    }

}
