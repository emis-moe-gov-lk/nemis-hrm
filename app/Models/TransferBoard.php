<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Blameable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TransferBoard extends Model
{
    use HasFactory, Blameable, LogsActivity;

    public const TYPE_TRANSFER = 'transfer';
    public const TYPE_APPEAL = 'appeal';
    public const STATUS_ON_PROGRESS = 'on_progress';
    public const STATUS_CLOSED = 'closed';

    protected $table = 'transfer_boards';

    protected $primaryKey = 'id';

    protected $fillable = [
        'board_id',
        'policy_id',
        'transfer_category_id',
        'bo_office_level_id',
        'bo_workplace_id',
        'board_type',
        'board_name',
        'start_date',
        'end_date',
        'board_status',
        'chairman_id',
        'secretary_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot (Auto Board ID)
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::creating(function ($model) {

            if (empty($model->board_id)) {
                $model->board_id = 'TB-' . date('Y') . '-' . rand(1000, 9999);
            }

            if (empty($model->board_type)) {
                $model->board_type = self::TYPE_TRANSFER;
            }

            if (empty($model->board_status)) {
                $model->board_status = self::STATUS_ON_PROGRESS;
            }
        });
    }

    public function scopeOfType($query, string $boardType)
    {
        return $query->where('board_type', $boardType);
    }

    public function scopeTransfer($query)
    {
        return $query->where('board_type', self::TYPE_TRANSFER);
    }

    public function scopeAppeal($query)
    {
        return $query->where('board_type', self::TYPE_APPEAL);
    }

    public function scopeOnProgress($query)
    {
        return $query->where('board_status', self::STATUS_ON_PROGRESS);
    }

    public function scopeClosed($query)
    {
        return $query->where('board_status', self::STATUS_CLOSED);
    }

    public function isClosed(): bool
    {
        return $this->board_status === self::STATUS_CLOSED;
    }

    public function isOnProgress(): bool
    {
        return $this->board_status === self::STATUS_ON_PROGRESS;
    }

    public function isTransferBoard(): bool
    {
        return $this->board_type === self::TYPE_TRANSFER;
    }

    public function isAppealBoard(): bool
    {
        return $this->board_type === self::TYPE_APPEAL;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Policy
    public function policy()
    {
        return $this->belongsTo(
            TransferPolicy::class,
            'policy_id',
            'policy_id'
        );
    }

    // Transfer Category
    public function category()
    {
        return $this->belongsTo(
            TransferCategory::class,
            'transfer_category_id',
            'transfer_category_id'
        );
    }

    // Office Level
    public function officeLevel()
    {
        return $this->belongsTo(
            OfficeLevel::class,
            'bo_office_level_id',
            'office_level_id'
        );
    }

    // Workplace
    public function workplace()
    {
        return $this->belongsTo(
            Workplaces::class,
            'bo_workplace_id',
            'workplace_id'
        );
    }

    // Chairman
    public function chairman()
    {
        return $this->belongsTo(
            People::class,
            'chairman_id',
            'people_id'
        );
    }

    // Secretary
    public function secretary()
    {
        return $this->belongsTo(
            People::class,
            'secretary_id',
            'people_id'
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

    // Board Members (IMPORTANT)
    public function members()
    {
        return $this->hasMany(
            TransferBoardMembers::class,
            'board_id',
            'board_id'
        );
    }

    public function subjectLinks()
    {
        return $this->hasMany(
            TransferBoardSubject::class,
            'board_id',
            'board_id'
        );
    }

    public function subjects()
    {
        return $this->belongsToMany(
            SubjectList::class,
            'transfer_board_subjects',
            'board_id',
            'subject_id',
            'board_id',
            'subject_id'
        )->wherePivot('active_status', true)
            ->withTimestamps();
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
            ->useLogName('transfer_boards')
            ->dontSubmitEmptyLogs();
    }
}
