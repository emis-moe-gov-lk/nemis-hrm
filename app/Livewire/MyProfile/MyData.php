<?php

namespace App\Livewire\MyProfile;

use Livewire\Component;

use App\Models\EmployerCurrentAppointment;

class MyData extends Component
{
    public $peopleId;
    public $serviceId;

    public function mount()
    {
        $currentAppointment = EmployerCurrentAppointment::query()
            ->with('appointment:appointment_id,service_id')
            ->where('employee_id', $this->peopleId)
            ->first();

        $this->serviceId = $currentAppointment?->appointment?->service_id;
    }

    public function render()
    {
        return view('livewire.my-profile.my-data');
    }
}
