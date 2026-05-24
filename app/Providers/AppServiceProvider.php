<?php

namespace App\Providers;

use App\Models\Versions;
use App\Observers\ActivityObserver;
use App\Support\Transfer\TransferAccess;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers()->symbols());

        Activity::observe(ActivityObserver::class);

        View::share('systemVersion', $this->resolveSystemVersion());

        Gate::define('transfer.portal.view', fn ($user) => TransferAccess::canViewPortal($user));
        Gate::define('transfer.teacher-self-service', fn ($user) => TransferAccess::canViewTeacherSelfService($user));
        Gate::define('transfer.admin.view', fn ($user) => TransferAccess::canViewAdminDashboard($user));
        Gate::define('transfer.board.view', fn ($user) => TransferAccess::canViewBoards($user));
        Gate::define('transfer.policy.view', fn ($user) => TransferAccess::canViewPolicies($user));
        Gate::define('transfer.policy.manage', fn ($user) => TransferAccess::canManagePolicies($user));
        Gate::define('transfer.request.view', fn ($user) => TransferAccess::canViewRequestPipeline($user));
        Gate::define('transfer.announcement.view', fn ($user) => TransferAccess::canManageAnnouncements($user));
        Gate::define('transfer.announcement.manage', fn ($user) => TransferAccess::canManageAnnouncements($user));
        Gate::define('transfer.institution-request.view', fn ($user) => TransferAccess::canViewInstitutionRequests($user));
        Gate::define('transfer.zeo-request.view', fn ($user) => TransferAccess::canViewZonalRequests($user));
        Gate::define('transfer.annual.view', fn ($user) => $user->hasRole('super admin') || $user->can('transfer.annual.view'));
        Gate::define('transfer.mutual.view', fn ($user) => $user->hasRole('super admin') || $user->can('transfer.mutual.view'));
        Gate::define('transfer.special.view', fn ($user) => $user->hasRole('super admin') || $user->can('transfer.special.view'));

        $forceHttps = filter_var(
            env('FORCE_HTTPS', app()->environment('production')),
            FILTER_VALIDATE_BOOL
        );

        if ($forceHttps) {
            URL::forceScheme('https');
        }

        // Dynamic counts for pending confirmations and verifications
        View::composer('components.alerts.layout', function ($view) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user) {
                $view->with([
                    'pendingConfirmationCount' => 0,
                    'pendingVerificationCount' => 0,
                ]);
                return;
            }

            // Get logged user's workplace
            $workplace = $user->workplace;
            if (!$workplace) {
                $view->with([
                    'pendingConfirmationCount' => 0,
                    'pendingVerificationCount' => 0,
                ]);
                return;
            }

            $allowedWorkplaceIds = Cache::remember(
                "workplace:child-ids:{$workplace->workplace_id}",
                300,
                fn () => $workplace->getAllChildWorkplaces()
            );

            // Confirmation counts
            $confirmMap = [
                'teacher.profile.confirm' => 'SER001',
                'sltes.profile.confirm' => 'SER002',
                'sltas.profile.confirm' => 'SER003',
                'principal.profile.confirm' => 'SER004',
                'sleas.profile.confirm' => 'SER005',
                'slas.profile.confirm' => 'SER006',
                'dos.profile.confirm' => 'SER007',
                'mso.profile.confirm' => 'SER008',
            ];

            $allowedConfirmServices = collect($confirmMap)
                ->filter(fn ($service, $permission) => Gate::forUser($user)->allows($permission))
                ->values()
                ->toArray();

            $pendingConfirmationCount = 0;
            if (!empty($allowedConfirmServices)) {
                $pendingConfirmationCount = Cache::remember(
                    'alerts:pending-confirmation-count:'. $user->id . ':' . implode(',', $allowedConfirmServices) . ':' . md5(json_encode($allowedWorkplaceIds)),
                    60,
                    fn () => \App\Models\People::whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                        $q->whereIn('workplace_id', $allowedWorkplaceIds);
                    })
                    ->whereHas('appointment', function ($q) use ($allowedConfirmServices) {
                        $q->whereIn('service_id', $allowedConfirmServices)
                        ->where('is_verified', 1)
                        ->where('is_confirmed', 0);
                    })
                    ->count()
                );
            }

            // Verification counts
            $verifyMap = [
                'teacher.profile.verify' => 'SER001',
                'sltes.profile.verify' => 'SER002',
                'sltas.profile.verify' => 'SER003',
                'principal.profile.verify' => 'SER004',
                'sleas.profile.verify' => 'SER005',
                'slas.profile.verify' => 'SER006',
                'dos.profile.verify' => 'SER007',
                'mso.profile.verify' => 'SER008',
            ];

            $allowedVerifyServices = collect($verifyMap)
                ->filter(fn ($service, $permission) => Gate::forUser($user)->allows($permission))
                ->values()
                ->toArray();

            $pendingVerificationCount = 0;
            if (!empty($allowedVerifyServices)) {
                $pendingVerificationCount = Cache::remember(
                    'alerts:pending-verification-count:'. $user->id . ':' . implode(',', $allowedVerifyServices) . ':' . md5(json_encode($allowedWorkplaceIds)),
                    60,
                    fn () => \App\Models\People::whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                        $q->whereIn('workplace_id', $allowedWorkplaceIds);
                    })
                    ->whereHas('appointment', function ($q) use ($allowedVerifyServices) {
                        $q->whereIn('service_id', $allowedVerifyServices)
                        ->where('is_verified', 0)
                        ->where('is_confirmed', 0);
                    })
                    ->count()
                );
            }

            $view->with(compact('pendingConfirmationCount', 'pendingVerificationCount'));
        });
    }

    protected function resolveSystemVersion(): ?string
    {
        try {
            if (!Schema::hasTable('versions')) {
                return null;
            }

            return Versions::query()
                ->where('is_latest', true)
                ->orderByDesc('release_date')
                ->orderByDesc('id')
                ->value('version');
        } catch (\Throwable) {
            return null;
        }
    }
}
