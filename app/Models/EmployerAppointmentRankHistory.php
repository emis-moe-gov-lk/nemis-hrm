<?php

namespace App\Models;

use App\Traits\Blameable;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployerAppointmentRankHistory extends Model
{
    use HasFactory, LogsActivity, Blameable;

    protected $table = 'employer_appointment_rank_histories';

    protected $fillable = [
        'appointment_id',
        'employee_id',
        'ref_letter_no',
        'rank_id',
        'start_date',
        'end_date',
        'is_active',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function appointment()
    {
        return $this->belongsTo(EmployerAppointment::class, 'appointment_id', 'appointment_id');
    }

    public function employee()
    {
        return $this->belongsTo(People::class, 'employee_id', 'people_id');
    }

    public function rank()
    {
        return $this->belongsTo(ServiceRank::class, 'rank_id', 'rank_id');
    }

    public function creator()
    {
        return $this->belongsTo(People::class, 'created_by', 'people_id');
    }

    public function updater()
    {
        return $this->belongsTo(People::class, 'updated_by', 'people_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('employer_appointment_rank_histories')
            ->dontSubmitEmptyLogs();
    }
}
