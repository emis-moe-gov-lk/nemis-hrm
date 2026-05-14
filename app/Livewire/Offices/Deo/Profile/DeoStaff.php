<?php

namespace App\Livewire\Offices\Deo\Profile;

use App\Models\Teacher;
use Livewire\Component;
use App\Models\DivisionalEducationOffice;
use App\Models\EmployerCurrentAppointment;

class DeoStaff extends Component
{
    public $officeId;
    public $staffList = [];

    public function mount($id)
    {
        $this->officeId = $id;

        // Get the workplace_id for the selected PMOE
        $office = DivisionalEducationOffice::find($this->officeId);
        $workplaceId = $office?->workplace_id;

        if ($workplaceId) {
            $this->staffList = EmployerCurrentAppointment::with(['employee', 'position', 'service', 'rank'])
                ->where('workplace_id', $workplaceId)
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.offices.deo.profile.deo-staff', [
            'officeId' => $this->officeId,
        ]);
    }
}
