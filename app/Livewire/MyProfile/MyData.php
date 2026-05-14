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
        $this->serviceId = EmployerCurrentAppointment::where('employee_id', $this->peopleId)
            ->value('service_id');
    }

    public function render()
    {
        return view('livewire.my-profile.my-data');
    }
}
