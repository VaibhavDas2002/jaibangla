<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcTextile extends Model
{
     protected $connection = 'pgsql10';
     protected $table = 'ben_docs_arc';
     protected $primaryKey='id';

}
