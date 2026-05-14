<?php

namespace App\Livewire\Slacs\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SlacsEmployment extends Component
{
    use AuthorizesRequests;
    
    /** @var int */
    public $id;

    /** @var \App\Models\People|null */
    public $people;

    /** @var \App\Models\Slacs|null */
    public $slacsAppointment;

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
        return view('livewire.slacs.profile.slacs-employment');
    }
}
