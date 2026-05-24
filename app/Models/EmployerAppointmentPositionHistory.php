<?php

namespace App\Models;

use App\Traits\Blameable;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class EmployerAppointmentPositionHistory
 *
 * @property string $appointment_id
 * @property string $employee_id
 * @property string|null $position_id
 * @property-read \App\Models\EmployerAppointment|null $appointment
 * @property-read \App\Models\People|null $employee
 * @property-read \App\Models\Position|null $position
 */
class EmployerAppointmentPositionHistory extends Model
{
    use HasFactory, LogsActivity, Blameable;

    protected $table = 'employer_appointment_position_histories';

    protected $fillable = [
        'appointment_id',
        'employee_id',
        'ref_letter_no',
        'position_id',
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

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
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
            ->useLogName('employer_appointment_position_histories')
            ->dontSubmitEmptyLogs();
    }
}
