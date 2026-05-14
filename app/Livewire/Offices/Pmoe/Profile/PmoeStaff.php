<?php

namespace App\Livewire\Offices\Pmoe\Profile;

use Livewire\Component;
use App\Models\EmployerCurrentAppointment;
use App\Models\ProvincialMinistryOfEducationOffice;
use App\Models\Teacher;

class PmoeStaff extends Component
{
    public $officeId;
    public $staffList = [];

    public function mount($id)
    {
        $this->officeId = $id;

        // Get the workplace_id for the selected PMOE
        $office = ProvincialMinistryOfEducationOffice::find($this->officeId);
        $workplaceId = $office?->workplace_id;

        if ($workplaceId) {
            $this->staffList = EmployerCurrentAppointment::with(['employee', 'position', 'service', 'rank'])
                ->where('workplace_id', $workplaceId)
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.offices.pmoe.profile.pmoe-staff', [
            'officeId' => $this->officeId,
        ]);
    }
}
