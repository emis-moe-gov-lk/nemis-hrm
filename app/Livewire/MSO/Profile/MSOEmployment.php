<?php

namespace App\Livewire\MSO\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MSOEmployment extends Component
{
    use AuthorizesRequests;
    
    /** @var int */
    public $id;

    /** @var \App\Models\People|null */
    public $people;

    /** @var \App\Models\MSO|null */
    public $msoAppointment;

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
    }

    /**
     * Render the Livewire view
     */
    public function render()
    {
        return view('livewire.m-s-o.profile.m-s-o-employment');
    }
}
