<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

class parijayi_lot_seeder extends Model
{
    protected $connection = 'pgsql_sp';
    protected $table = 'lot_no_seeder';
    public $timestamps = false;

    protected $lot_no_length = 6;   

    public function getLotNumberAttribute($value)
    {
          return substr('00000'.$this->lot_no, -$this->lot_no_length);
    }

}