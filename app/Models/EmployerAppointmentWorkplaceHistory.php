<?php

namespace App\Models;

use App\Traits\Blameable;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class EmployerAppointmentWorkplaceHistory
 *
 * @property string $id
 */
class EmployerAppointmentWorkplaceHistory extends Model
{
    use HasFactory, LogsActivity, Blameable;

    protected $table = 'employer_appointment_workplace_histories';

    protected $fillable = [
        'appointment_id',
        'employee_id',
        'ref_letter_no',
        'workplace_id',
        'office_level_id',
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

    public function officeLevel()
    {
        return $this->belongsTo(OfficeLevel::class, 'office_level_id', 'office_level_id');
    }

    public function workplace()
    {
        return $this->belongsTo(Workplaces::class, 'workplace_id', 'workplace_id');
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
            ->useLogName('employer_appointment_workplace_histories')
            ->dontSubmitEmptyLogs();
    }
}
