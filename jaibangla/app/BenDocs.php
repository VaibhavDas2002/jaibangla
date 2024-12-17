<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BenDocs extends Model
{
     protected $connection = 'pgsql_encwrite';
     protected $table = 'jb_doc.ben_attach_documents';
}
