<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryPensions_bak1 extends Model
{
    protected $connection = 'pgsql5';
    protected $table = 'beneficiary';
    protected $primaryKey='id';


    public function getBenidAttribute()
    {
          return "{$this->created_by_dist_code}{$this->scheme_id}{$this->id}";
    }
    protected $guarded = [];
}
