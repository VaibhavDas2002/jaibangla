<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchemeStepRank extends Model
{
    protected $table = 'm_scheme_step_rank';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function getSchemeParentId($scheme_id, $step_id)
    {
        $workflow_step_row = self::where('scheme_id', $scheme_id)
            ->where('step_id', $step_id)
            ->first();
        return $workflow_step_row->parent_id;
           
    }
}
