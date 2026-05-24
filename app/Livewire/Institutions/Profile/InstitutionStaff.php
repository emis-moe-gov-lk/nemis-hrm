<?php

namespace App\Livewire\Institutions\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Institution;
use App\Models\EmployerCurrentAppointment;

class InstitutionStaff extends Component
{
    use WithPagination;

    public $id;
    public $institution;
    public $selectedService = null;

    public function mount($id)
    {
        $this->institution = Institution::find($id);
    }

    public function updatingSelectedService()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = EmployerCurrentAppointment::with([
            'employee',
            'position',
            'service',
            'rank'
        ])
        ->select('employer_current_appointments.*')
        ->leftJoin('employer_appointments', 'employer_appointments.appointment_id', '=', 'employer_current_appointments.appointment_id')
        ->leftJoin('services', 'services.service_id', '=', 'employer_appointments.service_id')
        ->leftJoin('positions', 'positions.position_id', '=', 'employer_current_appointments.position_id');

        $availableServices = collect();

        if ($this->institution && $this->institution->workplace_id) {
            $query->where('employer_current_appointments.workplace_id', $this->institution->workplace_id);
            
            if ($this->selectedService) {
                $query->where('employer_appointments.service_id', $this->selectedService);
            }

            $availableServices = EmployerCurrentAppointment::where('workplace_id', $this->institution->workplace_id)
                ->whereHas('appointment', function ($q) {
                    $q->whereNotNull('service_id');
                })
                ->with('service')
                ->get()
                ->unique('service_id')
                ->values();
        } else {
            $query->whereNull('employer_current_appointments.id'); // Ensures an empty result
        }

        $staffList = $query
            ->orderByRaw('COALESCE(services.rank, 9999) ASC')
            ->orderByRaw('COALESCE(positions.position_order, 9999) ASC')
            ->orderBy('employer_current_appointments.appoint_date', 'asc')
            ->paginate(10);

        return view('livewire.institutions.profile.institution-staff', [
            'staffList' => $staffList,
            'institution'   => $this->institution,
            'availableServices' => $availableServices,
        ]);
    }
}
