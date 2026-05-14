<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TransferPolicy extends Model
{
    use HasFactory, Blameable;

    protected $table = 'transfer_policies';

    protected $fillable = [
        'policy_id',
        'policy_year',
        'circular_number',
        'title',
        'description',
        'min_service_current_school',
        'min_service_total',
        'effective_date',
        'application_start_date',
        'application_end_date',
        'transfer_authority',
        'transfer_type',
        'max_preferences',
        'service_id',
        'is_ns_category_considered',
        'active_status',
        'is_locked',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'policy_year' => 'integer',
        'min_service_current_school' => 'integer',
        'min_service_total' => 'integer',
        'max_preferences' => 'integer',
        'effective_date' => 'date',
        'application_start_date' => 'date',
        'application_end_date' => 'date',
        'active_status' => 'boolean',
        'is_locked' => 'boolean',
        'is_ns_category_considered' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {

            if (!$model->policy_id) {

                $year = now()->year;

                $lastRecord = self::whereYear('created_at', $year)
                    ->orderBy('id', 'desc')
                    ->first();

                $number = $lastRecord
                    ? intval(substr($lastRecord->policy_id, -5)) + 1
                    : 1;

                $model->policy_id = 'TPID-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Authority Workplace
    public function authority()
    {
        return $this->belongsTo(
            Workplaces::class,
            'transfer_authority',
            'workplace_id'
        );
    }

    // Service
    public function service()
    {
        return $this->belongsTo(
            Service::class,
            'service_id',
            'service_id'
        );
    }

    public function steps()
    {
        return $this->hasMany(
            TransferPolicyStep::class,
            'policy_id',
            'policy_id'
        )->orderBy('step_order');
    }

    public function categories()
    {
        return $this->hasMany(
            TransferCategory::class,
            'policy_id',
            'policy_id'
        );
    }

    public function categoriesQuery(): Builder
    {
        return TransferCategory::scopedListQuery($this->policy_id);
    }

    public function scoreRules()
    {
        return $this->hasMany(
            TransferPolicyScoreRule::class,
            'policy_id',
            'policy_id'
        );
    }

    public function facilityScoreRules()
    {
        return $this->hasMany(
            TransferPolicyFacilityScoreRule::class,
            'policy_id',
            'policy_id'
        );
    }

    public function achievementLevelScores()
    {
        return $this->hasMany(
            TransferPolicyAchievementLevelScore::class,
            'policy_id',
            'policy_id'
        );
    }

    public function teacherApplication()
    {
        return $this->hasMany(
            TeacherTransferApplication::class,
            'policy_id',
            'policy_id'
        );
    }

    // Scope Active
    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    // Scope Unlocked
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    // Scope to check if NS category is considered
    public function scopeConsiderNsCategory($query)
    {
        return $query->where('is_ns_category_considered', true);
    }
}
