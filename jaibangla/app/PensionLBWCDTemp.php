<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PensionLBWCDTemp extends Model
{
    protected $connection = 'pgsqllbtemp';
    protected $table = 'beneficiary_temp';
    protected $primaryKey = 'id';
    protected $casts = [
        'hof' => 'boolean',
        'pdf_is_download' => 'boolean',
        'isprocessed' => 'boolean'
    ];
    protected $fillable = ['ben_fname', 'ben_mname', 'ben_lname', 'bank_name', 'branch_name', 'bank_code', 'bank_ifsc', 'next_level_role_id'];
}
