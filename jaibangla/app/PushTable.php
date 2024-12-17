<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushTable extends Model
{
    //
    protected $table = 'push_table';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
