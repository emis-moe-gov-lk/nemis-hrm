<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherTransferScoreCriterion extends Model
{
    protected $table = 'teacher_transfer_score_criteria';

    protected $fillable = [
        'criteria_id',
        'criteria_key',
        'name',
        'description',
        'display_order',
        'active_status',
    ];

    protected $casts = [
        'active_status' => 'boolean',
        'display_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }
}
