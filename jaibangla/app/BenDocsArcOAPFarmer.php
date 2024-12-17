<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcOAPFarmer extends Model
{
     protected $connection = 'pgsql16';
     protected $table = 'ben_docs_arc';
     protected $primaryKey='id';

}
