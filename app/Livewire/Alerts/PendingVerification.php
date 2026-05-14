<?php

namespace App\Livewire\Alerts;

use App\Models\People;
use Livewire\Component;
use App\Helpers\NicHelper;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class PendingVerification extends Component
{
    use WithPagination;

    public $query = '';
    public $results = [];

    public function updatedQuery()
    {
        $raw = trim($this->query);

        // empty or too short
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

        /*
        |--------------------------------------------------------------------------
        | Determine allowed services by permission
        |--------------------------------------------------------------------------
        */
        $permissionServiceMap = [
            'teacher.profile.verify' => 'SER001',
            'sltes.profile.verify' => 'SER002',
            'sltas.profile.verify' => 'SER003',
            'principal.profile.verify' => 'SER004',
            'sleas.profile.verify' => 'SER005',
            'slas.profile.verify' => 'SER006',
            'dos.profile.verify' => 'SER007',
            'mso.profile.verify' => 'SER008',
        ];

        $allowedServices = collect($permissionServiceMap)
            ->filter(fn ($service, $permission) => auth()->user()->can($permission))
            ->values()
            ->toArray();

        if (empty($allowedServices)) {
            $this->results = [];
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */
        $peopleQuery = People::query()
            ->whereHas('currentAppointment', function ($q) use ($allowedServices, $allowedWorkplaceIds) {
                $q->whereIn('service_id', $allowedServices)
                ->whereIn('workplace_id', $allowedWorkplaceIds);
            });

        /*
        |--------------------------------------------------------------------------
        | Search Logic
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Fetch Results
        |--------------------------------------------------------------------------
        */
        $this->results = $peopleQuery
            ->limit(10)
            ->get();

    }


    public function render()
    {
        // Get logged user and workplace
        $logged = Auth::user()->load('workplace');
        $workplace = $logged->workplace;

        // If user has no workplace → show empty result
        if (! $workplace) {
            $employees = People::where('id', 0)->paginate(10);
            return view('livewire.pending.verification', compact('employees'));
        }

        // Get allowed workplaces (hierarchy)
        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        /*
        |--------------------------------------------------------------------------
        | Map permissions → services
        |--------------------------------------------------------------------------
        */
        $permissionServiceMap = [
            'teacher.profile.verify' => 'SER001',
            'sltes.profile.verify' => 'SER002',
            'sltas.profile.verify' => 'SER003',
            'principal.profile.verify' => 'SER004',
            'sleas.profile.verify' => 'SER005',
            'slas.profile.verify' => 'SER006',
            'dos.profile.verify' => 'SER007',
            'mso.profile.verify' => 'SER008',
        ];

        /*
        |--------------------------------------------------------------------------
        | Detect allowed services for logged user
        |--------------------------------------------------------------------------
        */
        $allowedServices = collect($permissionServiceMap)
            ->filter(fn ($service, $permission) => auth()->user()->can($permission))
            ->values()
            ->toArray();

        // If user has no permission → show empty
        if (empty($allowedServices)) {
            $employees = People::where('id', 0)->paginate(10);
            return view('livewire.pending.verification', compact('employees'));
        }

        /*
        |--------------------------------------------------------------------------
        | Main Query
        |--------------------------------------------------------------------------
        */
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
            ->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds, $allowedServices) {
                $q->whereIn('service_id', $allowedServices)
                ->whereIn('workplace_id', $allowedWorkplaceIds);
            })
            ->whereHas('appointment', function ($q) {
                $q->where('is_verified', 0)
                ->where('is_confirmed', 0);
            })
            ->paginate(10);

        return view('livewire.alerts.pending-verification', compact('employees'));
    }
}
