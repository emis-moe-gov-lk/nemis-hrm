<?php

namespace App\Livewire\Alerts;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AlertsOverview extends Component
{
    public $pendingConfirmationCount;
    public $pendingVerificationCount;
    
    public function mount()
    {
        // Get logged user and workplace
        $logged = Auth::user()->load('workplace');
        $workplace = $logged->workplace;

        // If user has no workplace → set counts to 0 and return
        if (! $workplace) {
            $this->pendingConfirmationCount = 0;
            $this->pendingVerificationCount = 0;
            return;
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
        | Main Query - Pending Confirmation
        |--------------------------------------------------------------------------
        */
        if (!empty($allowedServicesConfirm)) {
            $cacheKey = 'alerts:pending-confirmation:' . $workplace->workplace_id . ':' . implode(',', $allowedServicesConfirm);
            $this->pendingConfirmationCount = Cache::remember($cacheKey, 60, fn() => $this->countPendingAlerts($allowedWorkplaceIds, $allowedServicesConfirm, true, false));
        } else {
            $this->pendingConfirmationCount = 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Main Query - Pending Verification
        |--------------------------------------------------------------------------
        */
        if (!empty($allowedServicesVerify)) {
            $cacheKey = 'alerts:pending-verification:' . $workplace->workplace_id . ':' . implode(',', $allowedServicesVerify);
            $this->pendingVerificationCount = Cache::remember($cacheKey, 60, fn() => $this->countPendingAlerts($allowedWorkplaceIds, $allowedServicesVerify, false, false));
        } else {
            $this->pendingVerificationCount = 0;
        }
    }

    private function countPendingAlerts(array $allowedWorkplaceIds, array $allowedServices, bool $isVerified, bool $isConfirmed): int
    {
        return DB::table('employer_current_appointments')
            ->join('employer_appointments', 'employer_appointments.appointment_id', '=', 'employer_current_appointments.appointment_id')
            ->whereIn('employer_current_appointments.workplace_id', $allowedWorkplaceIds)
            ->whereIn('employer_appointments.service_id', $allowedServices)
            ->where('employer_appointments.is_verified', $isVerified ? 1 : 0)
            ->where('employer_appointments.is_confirmed', $isConfirmed ? 1 : 0)
            ->count(DB::raw('distinct employer_current_appointments.employee_id'));
    }

    public function render()
    {
        return view('livewire.alerts.alerts-overview');
    }
}

