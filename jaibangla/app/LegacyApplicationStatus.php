<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LegacyApplicationStatus extends Model
{
    protected $connection = 'pgsql_legacy';
    protected $table = 'legacy_status_code';
    protected $primaryKey='id';
    public function getCreatedAtAttribute($value)
    {
        $createdAt = Carbon::parse($value);
        return $createdAt->format('M d Y');
    }

}


