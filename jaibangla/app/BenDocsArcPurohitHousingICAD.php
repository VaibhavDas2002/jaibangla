<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcPurohitHousingICAD extends Model
{
     protected $connection = 'pgsqlpurohithousing';
     protected $table = 'ben_docs_arc';
     protected $primaryKey='id';

}
