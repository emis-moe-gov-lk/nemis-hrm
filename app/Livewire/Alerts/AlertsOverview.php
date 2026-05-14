<?php

namespace App\Livewire\Alerts;

use App\Models\People;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AlertsOverview extends Component
{
    public $pendingConfirmationCount;
    public $pendingVerificationCount;
    
    public function mount()
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
        $permissionServiceMapConfirm = [
            'teacher.profile.confirm' => 'SER001',
            'sltes.profile.confirm' => 'SER002',
            'sltas.profile.confirm' => 'SER003',
            'principal.profile.confirm' => 'SER004',
            'sleas.profile.confirm' => 'SER005',
            'slas.profile.confirm' => 'SER006',
            'dos.profile.confirm' => 'SER007',
            'mso.profile.confirm' => 'SER008',
        ];

        $permissionServiceMapVerify = [
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
        $allowedServicesConfirm = collect($permissionServiceMapConfirm)
            ->filter(fn ($service, $permission) => auth()->user()->can($permission))
            ->values()
            ->toArray();

        $allowedServicesVerify = collect($permissionServiceMapVerify)
            ->filter(fn ($service, $permission) => auth()->user()->can($permission))
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | If user has no permission → show empty
        |--------------------------------------------------------------------------
        */
        if (empty($allowedServicesConfirm)) {
            $employees = People::where('id', 0)->paginate(10);
            return view('livewire.pending.verification', compact('employees'));
        }

        if (empty($allowedServicesVerify)) {
            $employees = People::where('id', 0)->paginate(10);
            return view('livewire.pending.verification', compact('employees'));
        }

        /*
        |--------------------------------------------------------------------------
        | Main Query
        |--------------------------------------------------------------------------
        */
        $pendingConfirmationCount = People::whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds, $allowedServicesConfirm) {
            $q->whereIn('service_id', $allowedServicesConfirm)
            ->whereIn('workplace_id', $allowedWorkplaceIds);
        })
        ->whereHas('appointment', function ($q) {
            $q->where('is_verified', 1)
            ->where('is_confirmed', 0);
        })
        ->count();

        $pendingVerificationCount = People::whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds, $allowedServicesVerify) {
            $q->whereIn('service_id', $allowedServicesVerify)
            ->whereIn('workplace_id', $allowedWorkplaceIds);
        })
        ->whereHas('appointment', function ($q) {
            $q->where('is_verified', 0)
            ->where('is_confirmed', 0);
        })
        ->count();

    $this->pendingConfirmationCount = $pendingConfirmationCount;
    $this->pendingVerificationCount = $pendingVerificationCount;

    }
    public function render()
    {
        return view('livewire.alerts.alerts-overview');
    }
}
