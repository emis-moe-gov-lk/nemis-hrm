<?php

namespace App\Livewire\Slacs;

use App\Models\People;
use Livewire\Component;
use App\Helpers\NicHelper;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class SlacsList extends Component
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

        // base query: restrict to SLAS in allowed workplaces
        $peopleQuery = People::query()
            ->whereNot('people_id', 'PE2500000001')
            ->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                $q->where('service_id', 'SER009')       // SLAS service
                    ->whereIn('workplace_id', $allowedWorkplaceIds);
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
            return view('livewire.d-o-s.d-o-s-list', compact('employees'));
        }

        // Allowed workplaces based on hierarchy
        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        // Main SLAS query
        $employees = People::with([
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
            ->whereNot('people_id', 'PE2500000001')
            ->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                $q->where('service_id', 'SER009')       // SLAS service
                    ->whereIn('workplace_id', $allowedWorkplaceIds);
            })
            ->paginate(10);
            
        return view('livewire.slacs.slacs-list', compact('employees'));
    }
}
