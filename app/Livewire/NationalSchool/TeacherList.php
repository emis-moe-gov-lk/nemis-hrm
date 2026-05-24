<?php

namespace App\Livewire\NationalSchool;

use App\Models\People;
use Livewire\Component;
use App\Models\Workplaces;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NicHelper;

class TeacherList extends Component
{
    use WithPagination;

    public $query = '';
    public $results = [];

    public function updatedQuery()
    {
        $raw = trim($this->query);

        // empty or too short -> no results (adjust min length if you want)
        if ($raw === '' || strlen($raw) < 3) {
            $this->results = [];
            return;
        }

        // load logged user's workplace
        $logged = Auth::user()->load('workplace');
        $workplace = $logged->workplace;

        if (! $workplace) {
            $this->results = [];
            return;
        }

        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        // base query: restrict to teachers in allowed workplaces
        $peopleQuery = People::query()
            ->active()
            ->whereHas('appointment', function ($q) {
                $q->where('service_id', 'SER001');
            })
            ->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                $q->whereIn('workplace_id', $allowedWorkplaceIds)
                    ->whereHas('workplace.institution', function ($instQ) {
                        $instQ->where('authority_id', 'AUID01');
                    });
            });

        // If input looks like a valid NIC -> do exact NIC hash lookup
        if (NicHelper::isValid($raw)) {
            $normalized = NicHelper::normalize($raw);
            $hash = NicHelper::hash($normalized);

            $peopleQuery->where('nic_hash', $hash);
        } else {
            // Otherwise search loose on contact / email / name
            $search = $raw;
            $peopleQuery->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('name_with_initials', 'like', "%{$search}%");
            });
        }

        $this->results = $peopleQuery
            ->limit(10)
            ->get();
    }

    public function render()
    {
        // Get logged user and workplace
        $logged = Auth::user()->load('workplace');

        $workplace = $logged->workplace;

        // If user has no workplace (rare) → show nothing
        if (!$workplace) {
            $employees = People::where('id', 0)->paginate(10);
            return view('livewire.teacher.teacher-list', compact('employees'));
        }

        // Allowed workplaces based on hierarchy
        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        // Main teacher query
        $employees = People::with([
            'appointment.service',
            'currentAppointment.workplace',
            'currentAppointment.position',
            'currentAppointment.rank',
            'currentAppointment.service',
            'currentAppointment.workplace.ministry',
            'currentAppointment.workplace.provincialMinistry',
            'currentAppointment.workplace.provincial',
            'currentAppointment.workplace.zonal',
            'currentAppointment.workplace.divisional',
            'currentAppointment.workplace.institution',
        ])
            ->whereHas('appointment', function ($q) {
                $q->where('service_id', 'SER001');       // Teachers
            })
            ->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                $q->whereIn('workplace_id', $allowedWorkplaceIds)
                    ->whereHas('workplace.institution', function ($instQ) {
                        $instQ->where('authority_id', 'AUID01');
                    });
            })
            ->active()
            ->paginate(20);

        return view('livewire.national-school.teacher-list', compact('employees'));
    }
}
