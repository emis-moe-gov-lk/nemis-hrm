<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Traits\Blameable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TeacherTransferApplication extends Model
{
    use HasFactory, Blameable, LogsActivity;

    protected $table = 'teacher_transfer_applications';

    protected $primaryKey = 'id';

    protected $fillable = [
        'transfer_application_id',
        'policy_id',
        'employee_id',
        'appointment_id',
        'first_appointment_date',
        'current_workplace',
        'current_workplace_join_date',
        'cwp_facilities_id',
        'ns_cat',
        'permanent_address',
        'latitude',
        'longitude',
        'temporary_address',
        'temp_latitude',
        'temp_longitude',
        'transfer_type',
        'reason_category',
        'has_disciplinary_actions',
        'disciplinary_actions_details',
        'transfer_category',
        'target_province',
        'is_declared',
        'created_by',
        'updated_by',
        'status',
        'current_step',
    ];

    protected $casts = [
        'first_appointment_date' => 'date',
        'current_workplace_join_date' => 'date',
        'has_disciplinary_actions' => 'boolean',
        'is_declared' => 'boolean',
        'current_step' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {

            if (empty($model->transfer_application_id)) {

                $year = now()->year;

                $lastRecord = self::whereYear('created_at', $year)
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = $lastRecord
                    ? intval(substr($lastRecord->transfer_application_id, -5)) + 1
                    : 1;

                $model->transfer_application_id = 'TTA-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function policy()
    {
        return $this->belongsTo(TransferPolicy::class, 'policy_id', 'policy_id');
    }

    public function appointment()
    {
        return $this->belongsTo(EmployerAppointment::class, 'appointment_id', 'appointment_id');
    }

    public function employee()
    {
        return $this->belongsTo(People::class, 'employee_id', 'people_id');
    }

    public function targetProvince()
    {
        return $this->belongsTo(ProvincialEducationOffice::class, 'target_province', 'workplace_id');
    }

    public function reason()
    {
        return $this->belongsTo(TransferReason::class, 'reason_category', 'reason_id');
    }

    public function preferences()
    {
        return $this->hasMany(TeacherTransferApplicationPreferences::class, 'transfer_application_id', 'transfer_application_id')
            ->orderBy('preference_order');
    }

    public function achievements()
    {
        return $this->hasMany(TeacherTransferApplicationAchievement::class, 'transfer_application_id', 'transfer_application_id')
            ->orderBy('achievement_level')
            ->orderBy('achievement_date');
    }

    public function recommendations()
    {
        return $this->hasMany(TeacherTransferApplicationRecommendation::class, 'transfer_application_id', 'transfer_application_id')
            ->orderBy('created_at');
    }

    public function boardRecommendation()
    {
        return $this->hasOne(TeacherTransferBoardRecommendation::class, 'transfer_application_id', 'transfer_application_id');
    }

    public function appeals()
    {
        return $this->hasMany(TeacherTransferAppeals::class, 'transfer_application_id', 'transfer_application_id')
            ->orderBy('number_of_appeals');
    }

    public function latestAppeal()
    {
        return $this->hasOne(TeacherTransferAppeals::class, 'transfer_application_id', 'transfer_application_id')
            ->latestOfMany('number_of_appeals');
    }

    public function currentWorkplace()
    {
        return $this->belongsTo(Workplaces::class, 'current_workplace', 'workplace_id');
    }

    public function category()
    {
        return $this->belongsTo(TransferCategory::class, 'transfer_category', 'transfer_category_id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'employee_id', 'employee_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsEditableAttribute()
    {
        if ($this->status === 'draft') {
            return true;
        }

        // Allow editing submitted applications if the deadline hasn't passed
        if ($this->status === 'submitted' && $this->policy?->application_end_date) {
            return now()->lt($this->policy->application_end_date);
        }

        return false;
    }

    public function getTotalServiceYearsAttribute()
    {
        if (!$this->first_appointment_date || !$this->policy?->application_end_date) {
            return 'N/A';
        }

        $diff = $this->first_appointment_date->diff($this->policy->application_end_date);

        return "{$diff->y} Years, {$diff->m} Months";
    }

    public function getCurrentWorkplaceServiceYearsAttribute()
    {
        if (!$this->current_workplace_join_date || !$this->policy?->application_end_date) {
            return 'N/A';
        }

        $diff = $this->current_workplace_join_date->diff($this->policy->application_end_date);

        return "{$diff->y} Years, {$diff->m} Months";
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('teacher_transfer_applications')
            ->dontSubmitEmptyLogs();
    }
}
