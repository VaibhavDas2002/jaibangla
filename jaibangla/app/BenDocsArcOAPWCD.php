<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcOAPWCD extends Model
{
     protected $connection = 'pgsql12';
     protected $table = 'ben_docs_arc';
     protected $primaryKey='id';

}
