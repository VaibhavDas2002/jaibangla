<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequiredFilds extends Model
{
    protected $table = 'm_fields';
     /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
