<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherTransferApplicationAchievement extends Model
{
    protected $table = 'teacher_transfer_application_achievements';

    protected $fillable = [
        'transfer_application_id',
        'achievement_type',
        'achievement_level',
        'title',
        'event_name',
        'achievement_date',
        'details',
        'contribution_details',
        'is_included',
        'review_remarks',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'achievement_date' => 'date',
        'is_included' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(TeacherTransferApplication::class, 'transfer_application_id', 'transfer_application_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(People::class, 'reviewed_by', 'people_id');
    }
}
