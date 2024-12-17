<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manabik extends Model
{
     protected $connection = 'pgsql4';
     protected $table = 'beneficiary';
     protected $primaryKey='id';

    protected $scheme_length = 2; 
    protected $id_length = 8; 
    

    public function getBenidAttribute()
    {
          return $this->created_by_dist_code.substr('0'.$this->scheme_id, -$this->scheme_length).substr('0000000'.$this->id, -$this->id_length);
    }

    public function getName()
    {
          return "{$this->ben_fname} {$this->ben_mname}{$this->ben_lname}";
    }  
    
}
