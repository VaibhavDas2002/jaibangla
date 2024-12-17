<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ApplicationStatus extends Model
{
    protected $connection = 'pgsql_sp';
    protected $table = 'wbmt_application_status';
    protected $primaryKey='id';
    public $timestamps = false;

    protected $fillable = array('application_id','status_code','by_user');

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

}


