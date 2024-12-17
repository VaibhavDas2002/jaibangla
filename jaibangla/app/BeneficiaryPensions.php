<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryPensions extends Model
{
    protected $connection = 'pgsql5';
    protected $table = 'beneficiary';
    protected $primaryKey='id';


    public function getBenidAttribute()
    {
          return "{$this->created_by_dist_code}{$this->scheme_id}{$this->id}";
    }

    public function getName()
    {
          return "{$this->ben_fname} {$this->ben_mname}{$this->ben_lname}";
    }

    public function getFullNameAttribute()
    {
        return "{$this->ben_fname} {$this->ben_mname} {$this->ben_lname}";
    }

    protected $guarded = [];
}
