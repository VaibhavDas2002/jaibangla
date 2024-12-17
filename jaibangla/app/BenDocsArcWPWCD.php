<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcWPWCD extends Model
{
     protected $connection = 'pgsql13';
     protected $table = 'ben_docs_arc';
     protected $primaryKey='id';

}
