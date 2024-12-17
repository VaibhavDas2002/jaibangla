<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcSc extends Model
{
     protected $connection = 'pgsql2';
     protected $table = 'ben_docs_arc';
     protected $primaryKey='id';

}
