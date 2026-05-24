<?php

namespace App\Livewire\MSO;

use App\Models\People;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use App\Models\Workplaces;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NicHelper;

class MSOList extends Component
{
    use WithPagination;

    public string $query = '';
    public Collection|array $results = [];

    public function updatedQuery(): void
    {
        $raw = trim($this->query);

        if ($raw === '' || strlen($raw) < 3) {
            $this->results = [];
            return;
        }

        $logged = Auth::user()?->load('workplace');
        $workplace = $logged?->workplace;

        if (!$workplace) {
            $this->results = [];
            return;
        }

        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        $peopleQuery = People::query()
            ->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                $q->whereHas('appointment', fn($sq) => $sq->where('service_id', 'SER008')) // management assistant service
                  ->whereIn('workplace_id', $allowedWorkplaceIds);
            });

        if (NicHelper::isValid($raw)) {
            $normalized = NicHelper::normalize($raw);
            $hash = NicHelper::hash($normalized);
            $peopleQuery->where('nic_hash', $hash);
        } else {
            $search = $raw;
            $peopleQuery->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('name_with_initials', 'like', "%{$search}%");
            });
        }

        $this->results = $peopleQuery
            ->with(['currentAppointment.workplace', 'currentAppointment.appointment.service'])
            ->limit(10)
            ->get();
    }

    public function render(): View
    {
        $logged = Auth::user()?->load('workplace');
        $workplace = $logged?->workplace;

        if (!$workplace) {
            $employees = People::whereRaw('1 = 0')->paginate(10);
            return view('livewire.m-s-o.m-s-o-list', compact('employees'));
        }

        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        $employees = People::with([
            'currentAppointment.workplace',
            'currentAppointment.position',
            'currentAppointment.rank',
            'currentAppointment.appointment.service',
            'currentAppointment.workplace.ministry',
            'currentAppointment.workplace.provincialMinistry',
            'currentAppointment.workplace.provincial',
            'currentAppointment.workplace.zonal',
            'currentAppointment.workplace.divisional',
            'currentAppointment.workplace.institution',
        ])
        ->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
            $q->whereHas('appointment', fn($sq) => $sq->where('service_id', 'SER008')) // management assistant service
              ->whereIn('workplace_id', $allowedWorkplaceIds);
        })
        ->paginate(10);

        return view('livewire.m-s-o.m-s-o-list', compact('employees'));
    }
}
