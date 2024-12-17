<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchemeConfig extends Model
{
    protected $table = 'm_scheme_dup_config';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
