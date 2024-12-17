<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryPensionsReport extends Model
{
    protected $connection = 'pgsql5';
    protected $table = 'beneficiary';
    protected $primaryKey='id';
    protected $fillable = ['ben_fname', 'ben_mname', 'ben_lname','bank_name','branch_name','bank_code','bank_ifsc','next_level_role_id'];


    public function getBenidAttribute()
    {
          return "{$this->created_by_dist_code}{$this->scheme_id}{$this->id}";
    }

    public function getName()
    {
          return "{$this->ben_fname} {$this->ben_mname} {$this->ben_lname}";
    }
public function getFatherName()
    {
          return "{$this->father_fname} {$this->father_mname} {$this->father_lname}";
    }
    public function getFullNameAttribute()
    {
        return "{$this->ben_fname} {$this->ben_mname} {$this->ben_lname}";
    }

    protected $guarded = [];
}
