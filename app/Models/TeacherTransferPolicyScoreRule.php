<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherTransferPolicyScoreRule extends Model
{
    protected $table = 'transfer_policy_score_rules';

    protected $fillable = [
        'policy_id',
        'criteria_key',
        'score_per_unit',
        'base_value',
        'active_status',
    ];

    protected $casts = [
        'score_per_unit' => 'decimal:2',
        'base_value' => 'decimal:2',
        'active_status' => 'boolean',
    ];

    public function criterion()
    {
        return $this->belongsTo(TeacherTransferScoreCriterion::class, 'criteria_key', 'criteria_key');
    }
}
