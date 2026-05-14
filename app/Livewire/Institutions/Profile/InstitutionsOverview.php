<?php

namespace App\Livewire\Institutions\Profile;

use Livewire\Component;
use App\Models\Institution;
use App\Models\EmployerCurrentAppointment;

class InstitutionsOverview extends Component
{
    public $id;
    public $studentCount;
    public $staffCount;
    public $parentCount;

    public function mount($id)
    {
        $this->id = $id;

        // Fetch institution (existing logic)
        $institution = Institution::find($this->id);

        // Placeholder random student count (replace later with actual relationship)
        $this->studentCount = 0;

        // Count staff linked to this institution
        $this->staffCount = EmployerCurrentAppointment::where('workplace_id', $institution->workplace_id)->count();

        // Placeholder parent count (less than students)
        $this->parentCount = 0;
    }

    public function render()
    {
        $institution = Institution::find($this->id);

        return view('livewire.institutions.profile.institutions-overview', [
            'institution'   => $institution,
            'studentCount'  => $this->studentCount,
            'staffCount'    => $this->staffCount,
            'parentCount'   => $this->parentCount,
        ]);
    }
}
