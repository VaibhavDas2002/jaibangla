<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcFisherman extends Model
{
     protected $connection = 'pgsql6';
     protected $table = 'ben_docs_arc';
     protected $primaryKey='id';

}
