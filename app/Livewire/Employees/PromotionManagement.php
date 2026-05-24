<?php

namespace App\Livewire\Employees;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmployerAppointment;
use App\Models\People;
use App\Models\Institution;
use App\Models\ServiceRank;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NicHelper;

class PromotionManagement extends Component
{
    use WithPagination;

    public ?string $search = '';
    public ?string $serviceID = null;
    public array $allowedWorkplaceIds = [];
    public ?Service $services = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount(?string $serviceID)
    {
        $this->serviceID = $serviceID;
        $this->services = Service::where('service_id', $serviceID)->active()->first();

        /** @var \App\Models\User $logged */
        $logged = Auth::user();
        $workplace = $logged ? $logged->workplace : null;

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
            'employee.title',
            'rank'
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

        return view('livewire.employees.promotion-management', [
            'employeesList' => $employeesList,
        ]);
    }
}
