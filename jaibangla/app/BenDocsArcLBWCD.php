<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcLBWCD extends Model
{
     protected $connection = 'pgsql20';
     protected $table = 'ben_docs_arc';
     protected $primaryKey = 'id';
}
