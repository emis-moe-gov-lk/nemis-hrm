<?php

namespace App\Livewire\Offices\Moe\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmployerCurrentAppointment;

class MoeStaff extends Component
{
    use WithPagination;

    public $officeId;

    public function mount($id)
    {
        $this->officeId = $id;
    }

    public function render()
    {
        $staffList = EmployerCurrentAppointment::with([
            'employee',
            'position',
            'service',
            'rank'
        ])
        ->where('workplace_id', $this->officeId)
        ->paginate(10);

        return view('livewire.offices.moe.profile.moe-staff', [
            'staffList' => $staffList,
            'officeId' => $this->officeId,
        ]);
    }
}
