<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $table = 'm_workflow';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Define the relationship to the m_scheme_step_rank table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schemeStepRank()
    {
        return $this->belongsTo(
            SchemeStepRank::class,
            'workflow_step_id', // Foreign key on m_workflow
            'id' // Primary key on m_scheme_step_rank
        );
    }

    /**
     * Get the parent_id from the related m_scheme_step_rank record.
     *
     * @param int $scheme_id
     * @param int $designation_id
     * @return int|null
     */
    public static function getParentId($scheme_id, $designation_id)
    {
        $workflow = self::where('scheme_id', $scheme_id)
            ->where('role_name', $designation_id)
            ->first();

        return $workflow && $workflow->schemeStepRank
            ? $workflow->schemeStepRank->parent_id
            : null;
    }

    public static function getID($scheme_id, $designation_id)
    {
        $workflow = self::where('scheme_id', $scheme_id)
            ->where('role_name', $designation_id)
            ->first();

        return $workflow && $workflow->schemeStepRank
            ? $workflow->schemeStepRank->id
            : null;
    }
}
