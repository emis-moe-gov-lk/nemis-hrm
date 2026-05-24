<?php

namespace App\Livewire\Offices\Zeo\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ZonalEducationOffice;
use App\Models\EmployerCurrentAppointment;

class ZeoStaff extends Component
{
    use WithPagination;

    public $officeId;
    public $workplaceId;
    public $selectedService = null;

    public function mount($id)
    {
        $this->officeId = $id;

        $office = ZonalEducationOffice::find($this->officeId);

        if (!$office) {
            abort(404, 'Zonal Education Office not found');
        }

        $this->workplaceId = $office->workplace_id;
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

        if ($this->workplaceId) {
            $query->where('employer_current_appointments.workplace_id', $this->workplaceId);

            if ($this->selectedService) {
                $query->where('employer_appointments.service_id', $this->selectedService);
            }

            $availableServices = EmployerCurrentAppointment::where('workplace_id', $this->workplaceId)
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

        return view('livewire.offices.zeo.profile.zeo-staff', [
            'staffList' => $staffList,
            'officeId' => $this->officeId,
            'availableServices' => $availableServices,
        ]);
    }
}
