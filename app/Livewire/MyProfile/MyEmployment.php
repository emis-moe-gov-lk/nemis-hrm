<?php

namespace App\Livewire\MyProfile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MyEmployment extends Component
{
    public $peopleId;
    public $people;
    public $myAppointment;

    public function mount(): void
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $this->peopleId = Auth::user()->people_id;

        $this->people = People::with('currentAppointment')
            ->where('people_id', $this->peopleId)
            ->first();

        if (!$this->people) {
            abort(404, 'Profile not found');
        }

        // Assign appointment safely
        $this->myAppointment = $this->people->currentAppointment;
    }

    public function render()
    {
        return view('livewire.my-profile.my-employment');
    }
}
