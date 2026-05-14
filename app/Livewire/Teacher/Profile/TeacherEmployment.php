<?php

namespace App\Livewire\Teacher\Profile;

use Livewire\Component;
use App\Models\People;
use App\Models\Teacher;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherEmployment extends Component
{
    use AuthorizesRequests;
    
    /** @var int */
    public $id;

    /** @var \App\Models\People|null */
    public $people;

    /** @var \App\Models\Teacher|null */
    public $teacherAppointment;

    /**
     * Component initializer
     */
    public function mount($id): void
    {
        $this->id = $id;

        // Load People + Current Appointment Relationship
        $people = People::with('currentAppointment')->find($id);
        $this->authorize('viewRestrict', $people);

        $this->people = $people;

        // If appointment exists, load Teacher record once
        if ($this->people?->currentAppointment?->appointment_id) {
            $this->teacherAppointment = Teacher::where(
                'appointment_id',
                $this->people->currentAppointment->appointment_id
            )->first();
        }
    }

    /**
     * Render the Livewire view
     */
    public function render()
    {
        return view('livewire.teacher.profile.teacher-employment');
    }
}
