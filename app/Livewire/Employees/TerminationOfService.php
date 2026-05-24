<?php

namespace App\Livewire\Employees;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Service;
use App\Models\EmployerAppointment;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NicHelper;

class TerminationOfService extends Component
{
    use WithPagination;

    public ?string $serviceID = null;
    public ?Service $services = null;
    public array $allowedWorkplaceIds = [];
    public $search = '';
    
    // Modal State
    public bool $showModalTermination = false;
    public $selectedTeacherId = null;
    public $teacher = null;
    public $reasons = [];
    public $termination_reason = '';
    public $termination_effect_date = '';

    public function mount(?string $serviceID)
    {
        $this->serviceID = $serviceID;
        $this->services = Service::where('service_id', $serviceID)->active()->first();

        $logged = Auth::user()->load('workplace');
        $workplace = $logged->workplace;

        if (!$workplace) {
            abort(403, 'No workplace assigned to the logged-in user. You do not have permission to access this page.');
        }

        $this->allowedWorkplaceIds = $workplace->getAllChildWorkplaces();
    }

    public function render()
    {
        $query = EmployerAppointment::with([
            'currentAppointment.workplace.ministry',
            'currentAppointment.workplace.provincialMinistry',
            'currentAppointment.workplace.provincial',
            'currentAppointment.workplace.zonal',
            'currentAppointment.workplace.divisional',
            'currentAppointment.workplace.institution',
            'currentAppointment.position',
            'currentAppointment.rank',
            'employee.title'
        ]);

        // Safe workplace filter
        $query->whereHas('currentAppointment', function ($q) {
            if (!empty($this->allowedWorkplaceIds)) {
                $q->whereIn('workplace_id', $this->allowedWorkplaceIds);
            }
        });

        // FIXED service filter
        if (!empty($this->serviceID)) {
            $query->where('service_id', $this->serviceID);
        }

        // Search
        if (!empty($this->search)) {
            $rawSearch = trim($this->search);

            $query->where(function ($q) use ($rawSearch) {

                // search employee_id
                $q->where('employee_id', 'like', "%{$rawSearch}%");

                // search related People
                $q->orWhereHas('employee', function ($q) use ($rawSearch) {

                    if (NicHelper::isValid($rawSearch)) {

                        $hash = NicHelper::hash(
                            NicHelper::normalize($rawSearch)
                        );

                        $q->where('nic_hash', $hash);
                    } else {

                        $q->where('phone', 'like', "%{$rawSearch}%")
                            ->orWhere('email', 'like', "%{$rawSearch}%");
                    }
                });
            });
        }

        // Active filter
        $query->where('active_status', 1);

        $employeesList = $query->paginate(15);

        return view('livewire.employees.termination-of-service', [
            'employees' => $employeesList
        ]);
    }

    public function pensionTeacher($id)
    {
        $this->selectedTeacherId = $id;
        // Logic to load teacher and reasons will go here
        $this->showModalTermination = true;
    }
}
