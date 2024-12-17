<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DSOtpTrack extends Model
{

    protected $table = 'ds_otp_track';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
