<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocsArcPurohitMonthlyICAD extends Model
{
     protected $connection = 'pgsqlpurohitmonthly';
     protected $table = 'ben_docs_arc';
     protected $primaryKey='id';

}
