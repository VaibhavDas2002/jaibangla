<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait DynamicModelTable
{


   protected $table = null;

   public function bind(string $table)
   {
      //echo $table;die;
      $this->setTable($table);
   }

   public function newInstance($attributes = [], $exists = false)
   {


      $model = parent::newInstance($attributes, $exists);
      $model->setTable($this->table);

      return $model;
   }
}
