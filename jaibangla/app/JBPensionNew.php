<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JBPensionNew extends Model
{
    protected $connection = 'pgsql5';
      protected $table = 'beneficiaries_new';
      protected $primaryKey = 'id';

      protected $scheme_length = 2;
      protected $id_length = 8;


      public function getBenidAttribute()
      {
            return $this->created_by_dist_code . substr('0' . $this->scheme_id, -$this->scheme_length) . substr('0000000' . $this->id, -$this->id_length);
      }

      public function getName()
      {
            return "{$this->ben_fname} {$this->ben_mname}{$this->ben_lname}";
      }

      public function getFatherName()
      {
            return "{$this->father_fname} {$this->father_mname} {$this->father_lname}";
      }
      public function district()
      {
            return $this->belongsTo('App\District', 'district_code', 'district_code');
      }
      public function urban()
      {
            return $this->belongsTo('App\UrbanBody', 'block_ulb_code', 'urban_body_code');
      }
      public function taluka()
      {
            return $this->belongsTo('App\Taluka', 'block_ulb_code', 'block_code');
      }

      public function gp()
      {
            return $this->belongsTo('App\Taluka', 'block_ulb_code', 'block_code');
      }
      public function ward()
      {
            return $this->belongsTo('App\Taluka', 'block_ulb_code', 'block_code');
      }

      public function Scheme()
      {
            return $this->belongsTo('App\Scheme', 'scheme_id', 'id');
      }

}
