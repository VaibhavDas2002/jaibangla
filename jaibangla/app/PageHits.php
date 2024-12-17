<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageHits extends Model
{
    public $timestamps = false;

    protected $table = 'page_hits';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
