<?php

namespace App\Livewire\Alerts;

use App\Models\People;
use Livewire\Component;
use App\Helpers\NicHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\WithPagination;

class PendingConfirmation extends Component
{
    use WithPagination;

    public $query = '';
    public $results = [];

    private function getAllowedWorkplaceIds(): array
    {
        $user = Auth::user();
        if (! $user || ! $user->workplace) {
            return [];
        }

        return Cache::remember(
            "workplace:child-ids:{$user->workplace->workplace_id}",
            300,
            fn () => $user->workplace->getAllChildWorkplaces()
        );
    }

    private function getAllowedConfirmServices(): array
    {
        return collect([
                'teacher.profile.confirm' => 'SER001',
                'sltes.profile.confirm' => 'SER002',
                'sltas.profile.confirm' => 'SER003',
                'principal.profile.confirm' => 'SER004',
                'sleas.profile.confirm' => 'SER005',
                'slas.profile.confirm' => 'SER006',
                'dos.profile.confirm' => 'SER007',
                'mso.profile.confirm' => 'SER008',
            ])
            ->filter(fn ($service, $permission) => Auth::user()->can($permission))
            ->values()
            ->toArray();
    }

    private function getPendingConfirmationQuery()
    {
        $allowedWorkplaceIds = $this->getAllowedWorkplaceIds();
        $allowedServices = $this->getAllowedConfirmServices();

        if (empty($allowedWorkplaceIds) || empty($allowedServices)) {
            return People::where('id', 0);
        }

        return People::query()
            ->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                $q->whereIn('workplace_id', $allowedWorkplaceIds);
            })
            ->whereHas('appointment', function ($q) use ($allowedServices) {
                $q->whereIn('service_id', $allowedServices)
                    ->where('is_verified', 1)
                    ->where('is_confirmed', 0);
            });
    }

    public function updatedQuery()
    {
        $raw = trim($this->query);

        if ($raw === '' || strlen($raw) < 3) {
            $this->results = [];
            return;
        }

        $peopleQuery = $this->getPendingConfirmationQuery();

        if (NicHelper::isValid($raw)) {
            $normalized = NicHelper::normalize($raw);
            $hash = NicHelper::hash($normalized);
            $peopleQuery->where('nic_hash', $hash);
        } else {
            $peopleQuery->where(function ($q) use ($raw) {
                $q->where('phone', 'like', "%{$raw}%")
                    ->orWhere('email', 'like', "%{$raw}%")
                    ->orWhere('full_name', 'like', "%{$raw}%")
                    ->orWhere('name_with_initials', 'like', "%{$raw}%");
            });
        }

        $this->results = $peopleQuery
            ->with(['appointment.service', 'title', 'currentAppointment.appointment', 'currentAppointment.service'])
            ->limit(10)
            ->get();
    }

    public function render()
    {
        $employees = $this->getPendingConfirmationQuery()
            ->with([
                'title',
                'appointment.service',
                'currentAppointment.appointment',
                'currentAppointment.service',
                'currentAppointment.position',
                'currentAppointment.rank',
                'currentAppointment.workplace',
                'currentAppointment.workplace.ministry',
                'currentAppointment.workplace.provincialMinistry',
                'currentAppointment.workplace.provincial',
                'currentAppointment.workplace.zonal',
                'currentAppointment.workplace.divisional',
                'currentAppointment.workplace.institution',
            ])
            ->paginate(10);

        return view('livewire.alerts.pending-confirmation', compact('employees'));
    }
}

