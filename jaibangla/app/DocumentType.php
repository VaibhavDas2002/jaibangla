<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DocumentType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'm_attached_doc';


    protected $fillable = ['doc_name','doc_type','doc_size_kb','doucument_group','is_active','is_profile_pic'];

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
