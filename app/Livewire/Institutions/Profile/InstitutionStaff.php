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
        ]);

        $availableServices = collect();

        if ($this->institution && $this->institution->workplace_id) {
            $query->where('workplace_id', $this->institution->workplace_id);
            
            if ($this->selectedService) {
                $query->where('service_id', $this->selectedService);
            }

            $availableServices = EmployerCurrentAppointment::where('workplace_id', $this->institution->workplace_id)
                ->whereNotNull('service_id')
                ->select('service_id')
                ->distinct()
                ->with('service')
                ->get();
        } else {
            $query->whereNull('id'); // Ensures an empty result
        }

        $staffList = $query->orderBy('appoint_date', 'asc')->paginate(10);

        return view('livewire.institutions.profile.institution-staff', [
            'staffList' => $staffList,
            'institution'   => $this->institution,
            'availableServices' => $availableServices,
        ]);
    }
}
