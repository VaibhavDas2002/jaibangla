<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcSt extends Model
{
     protected $connection = 'pgsql3';
     protected $table = 'ben_docs_arc';
     protected $primaryKey='id';
}
