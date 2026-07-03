<?php

namespace App\Support\Transfer;

use App\Models\Institution;
use App\Models\TeacherTransferApplication as TeacherTransferApplicationModel;
use App\Models\TeacherTransferPolicy;
use App\Models\User;
use App\Models\Workplaces;
use Illuminate\Database\Eloquent\Builder;

class TransferAccess
{
    public const SLTS_SERVICE_ID = 'SER001';

    private const POLICY_PERMISSIONS = [
        'office.moe.profile.overview.view',
        'office.pmoe.profile.overview.view',
        'office.peo.profile.overview.view',
    ];

    private const REQUEST_PIPELINE_PERMISSIONS = [
        'office.moe.profile.overview.view',
        'office.pmoe.profile.overview.view',
        'office.peo.profile.overview.view',
        'office.zeo.profile.overview.view',
    ];

    private const ANNOUNCEMENT_PERMISSIONS = [
        'office.moe.profile.overview.view',
        'office.pmoe.profile.overview.view',
        'office.peo.profile.overview.view',
    ];

    private const INSTITUTION_REQUEST_PERMISSIONS = [
        'institution.profile.overview.view',
        'office.institution.profile.overview.view',
    ];

    public static function canViewAdminDashboard(?User $user): bool
    {
        return static::canManagePolicies($user)
            || static::canViewRequestPipeline($user)
            || static::canManageAnnouncements($user)
            || static::canViewBoards($user);
    }

    public static function canViewPortal(?User $user): bool
    {
        return static::canViewAdminDashboard($user);
    }

    public static function canViewTeacherSelfService(?User $user): bool
    {
        return static::isSltsEmployee($user);
    }

    public static function isSltsEmployee(?User $user): bool
    {
        if ($user === null || !static::isActiveUser($user)) {
            return false;
        }

        $currentAppointment = $user->relationLoaded('currentAppointment')
            ? $user->currentAppointment
            : $user->currentAppointment()->with('appointment')->first();

        $currentServiceId = $currentAppointment?->appointment?->service_id;

        if (filled($currentServiceId)) {
            return $currentServiceId === self::SLTS_SERVICE_ID;
        }

        $person = $user->relationLoaded('people')
            ? $user->people
            : $user->people()->with('appointment')->first();

        return $person?->appointment?->service_id === self::SLTS_SERVICE_ID;
    }

    public static function shouldUseTeacherSelfServiceDashboard(?User $user): bool
    {
        return static::canViewTeacherSelfService($user)
            && !static::canManagePolicies($user)
            && !static::canViewBoards($user)
            && !static::canViewRequestPipeline($user)
            && !static::canViewInstitutionRequests($user)
            && !static::canViewZonalRequests($user);
    }

    public static function canViewBoards(?User $user): bool
    {
        if (static::isSuperAdmin($user)) {
            return true;
        }

        return in_array(static::officeLevelId($user), ['OLID001', 'OLID002', 'OLID003', 'OLID004'], true);
    }

    public static function canViewPolicies(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return static::canManagePolicies($user)
            || static::isSltsEmployee($user)
            || $user->can('transfer.policy.view');
    }

    public static function canViewPolicy(?User $user, ?TeacherTransferPolicy $policy = null): bool
    {
        if ($user === null) {
            return false;
        }

        if (static::canManagePolicy($user, $policy)) {
            return true;
        }

        return $policy !== null && static::policyWithinUserScope($user, $policy);
    }

    public static function applyPolicyViewScope(Builder $query, ?User $user): Builder
    {
        $authorityIds = static::policyAuthorityIdsForUser($user);

        if ($authorityIds === null) {
            return $query;
        }

        if ($authorityIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('transfer_authority', $authorityIds);
    }

    public static function canManagePolicies(?User $user): bool
    {
        if (static::isSuperAdmin($user)) {
            return true;
        }

        // Policy management is a province/ministry-level function. Do not let
        // an accidentally broad role permission grant policy access to ZEO or
        // institution users.
        return in_array(static::officeLevelId($user), ['OLID001', 'OLID002', 'OLID003'], true)
            && static::hasAnyPermission($user, self::POLICY_PERMISSIONS);
    }

    public static function canManagePolicy(?User $user, ?TeacherTransferPolicy $policy = null): bool
    {
        if ($user === null) {
            return false;
        }

        if (static::isSuperAdmin($user)) {
            return true;
        }

        if (!static::canManagePolicies($user)) {
            return false;
        }

        if ($policy === null) {
            return true;
        }

        return filled($policy->transfer_authority)
            && filled($user->workplace_id)
            && $policy->transfer_authority === $user->workplace_id;
    }

    public static function canViewRequestPipeline(?User $user): bool
    {
        return static::isSuperAdmin($user)
            || static::hasAnyPermission($user, self::REQUEST_PIPELINE_PERMISSIONS);
    }

    public static function canManageAnnouncements(?User $user): bool
    {
        if (static::isSuperAdmin($user)) {
            return true;
        }

        // Announcements are policy-cycle notices, so keep publishing authority
        // at ministry/province levels even if a zonal role has broad profile
        // permissions from legacy role data.
        return in_array(static::officeLevelId($user), ['OLID001', 'OLID002', 'OLID003'], true)
            && static::hasAnyPermission($user, self::ANNOUNCEMENT_PERMISSIONS);
    }

    public static function canStartPolicyApplication(?User $user, ?TeacherTransferPolicy $policy): bool
    {
        if ($user === null || $policy === null) {
            return false;
        }

        if (!static::canViewPolicy($user, $policy)) {
            return false;
        }

        if (!$policy->active_status || $policy->is_locked) {
            return false;
        }

        $now = now();

        if ($policy->application_start_date && $now->lt($policy->application_start_date->copy()->startOfDay())) {
            return false;
        }

        if ($policy->application_end_date && $now->gt($policy->application_end_date->copy()->endOfDay())) {
            return false;
        }

        return true;
    }

    public static function canViewInstitutionRequests(?User $user, ?Institution $institution = null): bool
    {
        if (static::isSuperAdmin($user)) {
            return true;
        }

        if ($user !== null && $user->hasRole('principal')) {
            return $institution === null || $institution->workplace_id === $user->workplace_id;
        }

        if (
            $user !== null
            && $institution !== null
            && $institution->workplace_id === $user->workplace_id
            && $user->hasRole('principal')
        ) {
            return true;
        }

        if (!static::hasAnyPermission($user, self::INSTITUTION_REQUEST_PERMISSIONS)) {
            return false;
        }

        return $institution === null || $institution->workplace_id === $user?->workplace_id;
    }

    public static function canViewZonalRequests(?User $user, ?Workplaces $office = null): bool
    {
        if (static::isSuperAdmin($user)) {
            return true;
        }

        if (!$user?->can('office.zeo.profile.overview.view')) {
            return false;
        }

        return $office === null || $office->workplace_id === $user->workplace_id;
    }

    public static function canViewTeacherTransferApplication(?User $user, TeacherTransferApplicationModel $application): bool
    {
        if (!$user) {
            return false;
        }

        if (static::isSuperAdmin($user)) {
            return true;
        }

        if ($user->people_id === $application->employee_id) {
            return true;
        }

        if (static::canViewApplicationFromInstitutionQueue($user, $application)) {
            return true;
        }

        if (static::canViewRequestPipeline($user) && static::applicationWithinUserHierarchy($user, $application)) {
            return true;
        }

        return static::isAuthorizedForCurrentStep($user, $application);
    }

    public static function recommendationRedirectRoute(User $user): string
    {
        $workplace = $user->workplace;

        if (!$workplace) {
            return route('dashboard');
        }

        return match ($workplace->office_level_id) {
            'OLID006' => static::canViewInstitutionRequests($user)
                ? static::institutionRequestRoute($workplace)
                : route('dashboard'),
            'OLID004' => static::canViewZonalRequests($user)
                ? static::zonalRequestRoute($workplace)
                : (static::canViewRequestPipeline($user) ? route('transfer.teacher-transfer-request') : route('dashboard')),
            default => static::canViewRequestPipeline($user)
                ? route('transfer.teacher-transfer-request')
                : route('dashboard'),
        };
    }

    private static function institutionRequestRoute(Workplaces $workplace): string
    {
        $institutionId = $workplace->institution?->id;

        return $institutionId
            ? route('offices.institutions.institution-transfer-requests', $institutionId)
            : route('dashboard');
    }

    private static function zonalRequestRoute(Workplaces $workplace): string
    {
        $zoneId = $workplace->zonal?->id;

        return $zoneId
            ? route('offices.zeo.profile.teachers-transfer-requests', $zoneId)
            : route('dashboard');
    }

    private static function isAuthorizedForCurrentStep(User $user, TeacherTransferApplicationModel $application): bool
    {
        $currentStep = $application->policy?->steps?->firstWhere('step_order', $application->current_step);
        $userWorkplace = $user->workplace;

        if (!$currentStep || !$userWorkplace || $userWorkplace->office_level_id !== $currentStep->office_level_id) {
            return false;
        }

        return match ($currentStep->office_level_id) {
            'OLID006' => $userWorkplace->workplace_id === $application->current_workplace,
            'OLID004' => static::matchesZone($userWorkplace, $application),
            'OLID003' => static::matchesProvince($userWorkplace, $application),
            default => true,
        };
    }

    private static function applicationWithinUserHierarchy(User $user, TeacherTransferApplicationModel $application): bool
    {
        $userWorkplace = $user->workplace;

        if (!$userWorkplace || !$application->current_workplace) {
            return false;
        }

        return in_array($application->current_workplace, $userWorkplace->getAllChildWorkplaces(), true);
    }

    private static function canViewApplicationFromInstitutionQueue(User $user, TeacherTransferApplicationModel $application): bool
    {
        if (blank($application->current_workplace)) {
            return false;
        }

        $institution = Institution::query()
            ->where('workplace_id', $application->current_workplace)
            ->first();

        return $institution !== null && static::canViewInstitutionRequests($user, $institution);
    }

    private static function matchesZone(Workplaces $userWorkplace, TeacherTransferApplicationModel $application): bool
    {
        $currentWorkplace = $application->currentWorkplace;

        if (!$currentWorkplace) {
            return false;
        }

        if (filled($currentWorkplace->zeo_wp_id)) {
            return $currentWorkplace->zeo_wp_id === $userWorkplace->workplace_id;
        }

        $parent = $currentWorkplace->parent;

        if (!$parent) {
            return false;
        }

        if ($parent->office_level_id === 'OLID004') {
            return $parent->workplace_id === $userWorkplace->workplace_id;
        }

        return $parent->parent_workplace_id === $userWorkplace->workplace_id;
    }

    private static function matchesProvince(Workplaces $userWorkplace, TeacherTransferApplicationModel $application): bool
    {
        $currentWorkplace = $application->currentWorkplace;

        if (!$currentWorkplace) {
            return false;
        }

        $provinceId = $currentWorkplace->peo_wp_id ?: static::findAncestorWorkplaceId($currentWorkplace, 'OLID003');

        return filled($provinceId) && $provinceId === $userWorkplace->workplace_id;
    }

    private static function policyWithinUserScope(User $user, TeacherTransferPolicy $policy): bool
    {
        $authorityIds = static::policyAuthorityIdsForUser($user);

        if ($authorityIds === null) {
            return true;
        }

        if ($authorityIds === [] || blank($policy->transfer_authority)) {
            return false;
        }

        return in_array($policy->transfer_authority, $authorityIds, true);
    }

    private static function policyAuthorityIdsForUser(?User $user): ?array
    {
        if (static::isSuperAdmin($user)) {
            return null;
        }

        if ($user === null || !static::isActiveUser($user)) {
            return [];
        }

        $workplace = $user->workplace;

        if (!$workplace) {
            return [];
        }

        if ($workplace->office_level_id === 'OLID001') {
            return null;
        }

        return collect($workplace->getAllParentWorkplaces())
            ->merge($workplace->getAllChildWorkplaces())
            ->unique()
            ->values()
            ->all();
    }

    private static function findAncestorWorkplaceId(?Workplaces $workplace, string $officeLevelId): ?string
    {
        while ($workplace) {
            if ($workplace->office_level_id === $officeLevelId) {
                return $workplace->workplace_id;
            }

            $workplace = $workplace->parent;
        }

        return null;
    }

    private static function hasAnyPermission(?User $user, array $permissions): bool
    {
        return $user !== null && $user->canAny($permissions);
    }

    private static function isSuperAdmin(?User $user): bool
    {
        return (bool) $user?->hasRole('super admin');
    }

    private static function isActiveUser(User $user): bool
    {
        return (bool) $user->active_status;
    }

    private static function officeLevelId(?User $user): ?string
    {
        return $user?->workplace?->office_level_id;
    }
}
