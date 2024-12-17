<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcManabikWCD extends Model
{
     protected $connection = 'pgsql4';
     protected $table = 'ben_docs_arc';
     protected $primaryKey='id';

}
