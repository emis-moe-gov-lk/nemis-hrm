<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Blameable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TransferBoardMemberAttendances extends Model
{
    use HasFactory, Blameable, LogsActivity;

    protected $table = 'transfer_board_member_attendances';

    protected $primaryKey = 'id';

    protected $fillable = [
        'tbm_id',
        'attendance_date',
        'attendance_status',
        'remarks',
        'active_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'active_status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Board Member
    public function member()
    {
        return $this->belongsTo(
            TransferBoardMembers::class,
            'tbm_id',
            'tbm_id'
        );
    }

    // Created By
    public function createdBy()
    {
        return $this->belongsTo(
            People::class,
            'created_by',
            'people_id'
        );
    }

    // Updated By
    public function updatedBy()
    {
        return $this->belongsTo(
            People::class,
            'updated_by',
            'people_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Activity Log
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('transfer_board_member_attendances')
            ->dontSubmitEmptyLogs();
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    // Check if present
    public function isPresent()
    {
        return $this->attendance_status === 'present';
    }

    // Check if absent
    public function isAbsent()
    {
        return $this->attendance_status === 'absent';
    }

    // Check if late
    public function isLate()
    {
        return $this->attendance_status === 'late';
    }
}
