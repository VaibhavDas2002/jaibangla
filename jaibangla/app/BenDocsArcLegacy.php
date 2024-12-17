<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcLegacy extends Model
{
     protected $connection = 'pgsql_legacy';
     protected $table = 'ben_docs_arc';
     protected $primaryKey='id';
}
