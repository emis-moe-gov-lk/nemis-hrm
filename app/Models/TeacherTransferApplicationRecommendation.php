<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\Blameable;

class TeacherTransferApplicationRecommendation extends Model
{
    use HasFactory, LogsActivity, Blameable;

    protected static ?bool $hasRecommendationStatusColumn = null;

    protected $table = 'teacher_transfer_application_recommendations';

    protected $primaryKey = 'id';

    protected $fillable = [
        'transfer_application_id',
        'workplace_id',
        'approved_by',
        'transfer_recommendation_list_id',
        'remarks',
        'recommendation_status',
        'created_by',
        'updated_by',
        'active_status',
    ];

    protected $casts = [
        'recommendation_status' => 'boolean',
        'active_status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scop
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('active_status', false);
    }

    public function scopeRecommended($query)
    {
        if (static::hasRecommendationStatusColumn()) {
            return $query->where('recommendation_status', true);
        }

        return $query->whereNotNull('transfer_recommendation_list_id');
    }

    public function scopePendingRecommended($query)
    {
        if (static::hasRecommendationStatusColumn()) {
            return $query->where('recommendation_status', false);
        }

        return $query->whereNull('transfer_recommendation_list_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Application
    public function application()
    {
        return $this->belongsTo(
            TeacherTransferApplication::class,
            'transfer_application_id',
            'transfer_application_id'
        );
    }

    // Recommendation Type
    public function recommendation()
    {
        return $this->belongsTo(
            TeacherTransferRecommendationList::class,
            'transfer_recommendation_list_id',
            'transfer_recommendation_list_id'
        );
    }

    // Workplace (Institution / ZEO / Provincial)
    public function workplace()
    {
        return $this->belongsTo(
            Workplaces::class,
            'workplace_id',
            'workplace_id'
        );
    }

    // Approved By Person
    public function approver()
    {
        return $this->belongsTo(
            People::class,
            'approved_by',
            'people_id'
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('teacher_transfer_application_recommendations')
            ->dontSubmitEmptyLogs();
    }

    public static function hasRecommendationStatusColumn(): bool
    {
        return static::$hasRecommendationStatusColumn ??= Schema::hasColumn(
            'teacher_transfer_application_recommendations',
            'recommendation_status'
        );
    }
}
