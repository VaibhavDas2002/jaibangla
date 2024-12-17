<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcMSME extends Model
{
     protected $connection = 'pgsql9';
     protected $table = 'ben_docs_arc';
     protected $primaryKey='id';

}
