<?php

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TeacherTransferBoardSubject extends Model
{
    use HasFactory, Blameable, LogsActivity;

    protected $table = 'teacher_transfer_board_subjects';

    protected $primaryKey = 'id';

    protected $fillable = [
        'board_id',
        'subject_id',
        'active_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'active_status' => 'boolean',
    ];

    public function board()
    {
        return $this->belongsTo(TeacherTransferBoard::class, 'board_id', 'board_id');
    }

    public function subject()
    {
        return $this->belongsTo(SubjectList::class, 'subject_id', 'subject_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(People::class, 'created_by', 'people_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(People::class, 'updated_by', 'people_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('teacher_transfer_board_subjects')
            ->dontSubmitEmptyLogs();
    }
}
