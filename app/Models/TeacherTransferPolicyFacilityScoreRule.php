<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherTransferPolicyFacilityScoreRule extends Model
{
    protected $table = 'transfer_policy_facility_score_rules';

    protected $fillable = [
        'policy_id',
        'criteria_key',
        'facilities_id',
        'score_per_year',
    ];

    protected $casts = [
        'score_per_year' => 'decimal:2',
    ];

    public function facility()
    {
        return $this->belongsTo(InstitutionalFacility::class, 'facilities_id', 'facilities_id');
    }
}
