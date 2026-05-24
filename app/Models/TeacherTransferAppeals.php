<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Blameable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TeacherTransferAppeals extends Model
{
    use HasFactory, Blameable, LogsActivity;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $table = 'teacher_transfer_appeals';

    protected $primaryKey = 'id';

    protected $fillable = [
        'appeal_id',
        'transfer_application_id',
        'policy_id',
        'number_of_appeals',
        'appeal_board_id',
        'appeal_reason',
        'appeal_remarks',
        'appeal_status',
        'decision_remarks',
        'school_selection_type',
        'selected_zone_id',
        'selected_school_id',
        'transfer_effective_date',
        'rejection_reason',
        'active_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'active_status' => 'boolean',
        'number_of_appeals' => 'integer',
        'transfer_effective_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot (Auto Appeal ID)
    |--------------------------------------------------------------------------
    */



    protected static function booted()
    {
        static::creating(function ($model) {

            // Auto Appeal ID
            if (empty($model->appeal_id)) {
                $model->appeal_id = 'APPL-' . date('Y') . '-' . rand(1000, 9999);
            }

            // Count existing appeals
            $count = self::where('transfer_application_id', $model->transfer_application_id)
                ->count();

            $maxAppeals = 3;

            // Stop if limit reached
            if ($count >= $maxAppeals) {
                throw ValidationException::withMessages([
                    'transfer_application_id' => 'Maximum appeal limit reached'
                ]);
            }

            // Auto increment appeal number
            $model->number_of_appeals = $count + 1;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Transfer Application
    public function application()
    {
        return $this->belongsTo(
            TeacherTransferApplication::class,
            'transfer_application_id',
            'transfer_application_id'
        );
    }

    // Policy
    public function policy()
    {
        return $this->belongsTo(
            TeacherTransferPolicy::class,
            'policy_id',
            'policy_id'
        );
    }

    public function board()
    {
        return $this->belongsTo(
            TeacherTransferBoard::class,
            'appeal_board_id',
            'board_id'
        );
    }

    public function selectedZone()
    {
        return $this->belongsTo(
            ZonalEducationOffice::class,
            'selected_zone_id',
            'workplace_id'
        );
    }

    public function selectedSchool()
    {
        return $this->belongsTo(
            Institution::class,
            'selected_school_id',
            'workplace_id'
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

    public function isPending(): bool
    {
        return $this->appeal_status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->appeal_status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->appeal_status === self::STATUS_REJECTED;
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
            ->useLogName('teacher_transfer_appeals')
            ->dontSubmitEmptyLogs();
    }
}
