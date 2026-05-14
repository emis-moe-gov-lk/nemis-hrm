<?php

namespace App\Livewire\Principal\Profile;

use App\Models\People;
use Livewire\Component;
use App\Models\Position;
use App\Models\Principal;
use App\Models\Workplaces;
use App\Models\OfficeLevel;
use App\Models\ServiceRank;
use Illuminate\Validation\Rule;
use App\Models\EmployerAppointment;
use App\Models\InstitutionCategory;
use App\Models\ZonalEducationOffice;
use App\Models\EmployerAppointmentHistory;
use App\Models\EmployerCurrentAppointment;

class PrincipalEmployment extends Component
{
    /** @var int */
    public $id;

    /** @var \App\Models\People|null */
    public $people;

    /** @var \App\Models\Principal|null */
    public $principalAppointment;

    /**
     * Component initializer
     */
    public function mount($id): void
    {
        $this->id = $id;

        // Load People + Current Appointment Relationship
        $this->people = People::with('currentAppointment')->find($id);

        // If appointment exists, load Principal record once
        if ($this->people?->currentAppointment?->appointment_id) {
            $this->principalAppointment = Principal::where(
                'appointment_id',
                $this->people->currentAppointment->appointment_id
            )->first();
        }
    }

    /**
     * Render the Livewire view
     */
    public function render()
    {
        return view('livewire.principal.profile.principal-employment');
    }
}
