<?php

namespace App\Livewire\Teacher\Profile;

use App\Models\People;
use App\Models\Teacher;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherEmployment extends Component
{
    use AuthorizesRequests;
    
    public int $id;
    public ?People $people = null;
    public ?Teacher $teacherAppointment = null;

    /**
     * Component initializer
     */
    public function mount(int $id): void
    {
        $this->id = $id;

        // Load People + Relationships
        $this->people = People::with(['currentAppointment', 'appointment'])->find($id);
        
        if (!$this->people) {
            abort(404);
        }

        $this->authorize('viewRestrict', $this->people);

        // If current appointment exists, load teacher-specific details
        if ($this->people->currentAppointment) {
            $this->teacherAppointment = Teacher::where(
                'appointment_id',
                $this->people->currentAppointment->appointment_id
            )->first();
        }
    }

    public function render()
    {
        return view('livewire.teacher.profile.teacher-employment');
    }
}
