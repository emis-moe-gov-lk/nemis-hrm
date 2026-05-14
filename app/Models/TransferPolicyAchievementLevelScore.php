<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferPolicyAchievementLevelScore extends Model
{
    protected $table = 'transfer_policy_achievement_level_scores';

    protected $fillable = [
        'policy_id',
        'achievement_level',
        'score_per_achievement',
    ];

    protected $casts = [
        'score_per_achievement' => 'decimal:2',
    ];
}
