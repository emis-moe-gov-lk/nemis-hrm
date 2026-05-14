<?php

namespace App\Livewire\Teacher\Transfer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Workplaces;
use App\Models\Institution;
use App\Models\ZonalEducationOffice;
use App\Models\ProvincialEducationOffice;
use App\Models\TransferPolicy;
use App\Models\TransferReason;
use App\Models\TeacherTransferApplication as TeacherTransferApplicationModel;
use App\Models\TeacherTransferApplicationAchievement;
use App\Models\TeacherTransferApplicationPreferences;
use App\Models\TeacherTransferApplicationRecommendation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\EmployerAppointment;
use App\Models\People;
use App\Models\TransferCategory;

class TeacherTransferApplication extends Component
{
    private const PROVINCE_SELECT_CATEGORY_KEYWORDS = [
        'inter province',
        'other province',
    ];

    private const NATIONAL_SCHOOL_CATEGORY_KEYWORDS = [
        'national school',
    ];

    public $step = 1;
    public $applicationId = null;
    public $isEditMode = false;

    // Profile info (read-only)
    public $teacherName = '';
    public $teacherNameWithInitials = '';
    public $dateOfBirth = '';
    public $gender = '';
    public $nic = '';
    public $employeeId = '';
    public $currentWorkplaceId = '';
    public $currentWorkplaceName = '';
    public $currentWorkplaceAddress = '';
    public $currentProvinceId = '';
    public $currentZoneId = '';
    public $firstAppointmentDate = '';
    public $currentWorkplaceJoinDate = '';
    public $firstWorkplaceName = '';
    public $firstWorkplaceAddress = '';
    public $appointmentId = '';
    public $serviceTotal = '';
    public $currentServiceStationTotal = '';
    public $permanentAddress = '';
    public $temporaryAddress = '';
    public $permanentLatitude = '';
    public $permanentLongitude = '';
    public $temporaryLatitude = '';
    public $temporaryLongitude = '';
    public $cwpFacilitiesId = '';
    public $nsCat = '';

    // Application Form
    public $policyId = '';
    public $transferCategoryId = '';
    public $transferReasonTypeId = '';
    public $transferReason = '';
    public $hasDisciplinaryActions = false;
    public $disciplinaryDetails = '';

    public $transferReasonTypes = [];

    // Dynamic Location Selections
    public $selectedProvinceId = '';
    public $selectedZoneId = '';

    // Preferences (array-based, dynamically sized from policy)
    public $maxPreferences = 1;
    public $selectedZones = [];
    public $preferences = [];
    public $distanceInKm = [];
    public $institutionsLists = [];
    public array $achievements = [];
    public array $achievementTypes = [
        'student' => 'Student Achievement',
        'teacher' => 'Teacher Achievement',
    ];
    public array $achievementLevels = [
        'zonal' => 'Zonal',
        'district' => 'District',
        'provincial' => 'Provincial',
        'national' => 'National',
    ];

    // Declarations
    public $declarationTrue = false;

    public $transferPolicies = [];
    public $transferCatagory = [];
    public $provincialEducationOffices;
    public $zonalEducationOffices;

    public function mount($id = null)
    {
        $this->applicationId = $id;
        $this->provincialEducationOffices = collect();
        $this->zonalEducationOffices = collect();

        if (Auth::check()) {
            $user = Auth::user();
            $people = People::with(['title', 'appointment.workplace', 'currentAppointment.workplace'])->where('people_id', $user->people_id)->first();

            if ($people) {
                $this->loadTeacherProfile($people);
            }

            $this->loadMetadata();

            if ($id) {
                $this->loadExistingApplication($id);
            } else {
                $this->initPreferences();
                $this->initAchievements();
            }
        }
    }

    private function loadTeacherProfile($people)
    {
        $this->teacherName = ($people->title->title_name ?? '') . ' ' . $people->full_name;
        $this->teacherNameWithInitials = $people->name_with_initials;
        $this->dateOfBirth = $people->date_of_birth;
        $this->gender = $people->gender->gender_name ?? '';
        $this->nic = $people->nic;
        $this->employeeId = $people->people_id;

        if ($people->appointment && $people->appointment->workplace) {
            $this->appointmentId = $people->appointment->appointment_id;
            $this->firstWorkplaceName = $people->appointment->workplace->office_name;
            $this->firstWorkplaceAddress = $people->appointment->workplace->address;
            $this->serviceTotal = $people->appointment->service_years;
            $this->firstAppointmentDate = $people->appointment->first_appointment_date?->format('Y-m-d');
        }

        if ($people->currentAppointment && $people->currentAppointment->workplace) {
            $currentWorkplace = $people->currentAppointment->workplace;
            $hierarchy = $this->resolveCurrentWorkplaceHierarchy($currentWorkplace);

            $this->currentWorkplaceId = $currentWorkplace->workplace_id;
            $this->currentWorkplaceName = $currentWorkplace->office_name;
            $this->currentWorkplaceAddress = $currentWorkplace->address;
            $this->currentProvinceId = $hierarchy['province_id'];
            $this->currentZoneId = $hierarchy['zone_id'];
            $this->currentServiceStationTotal = $people->currentAppointment->service_years;
            $this->currentWorkplaceJoinDate = $people->currentAppointment->appoint_date?->format('Y-m-d');

            // Fetch facilities ID if it's an institution
            $institution = $currentWorkplace->institution;
            if ($institution) {
                $this->cwpFacilitiesId = $institution->facilities_id;
                $this->nsCat = $institution->ns_cat;
            }
        }

        $this->permanentAddress = collect([$people->address_line1, $people->address_line2, $people->address_line3, $people->postal_code])->filter()->implode(', ');
        $this->temporaryAddress = collect([$people->t_address_line1, $people->t_address_line2, $people->t_address_line3, $people->t_postal_code])->filter()->implode(', ');
        $this->permanentLatitude = filled($people->latitude) ? (string) $people->latitude : '';
        $this->permanentLongitude = filled($people->longitude) ? (string) $people->longitude : '';
        $this->temporaryLatitude = '';
        $this->temporaryLongitude = '';
    }

    private function loadMetadata()
    {
        $this->transferPolicies = TransferPolicy::active()->where('service_id', 'SER001')->orderByDesc('policy_year')->pluck('title', 'policy_id');
        $this->transferReasonTypes = TransferReason::active()->orderBy('display_order')->get()->map(fn($r) => ['id' => $r->reason_id, 'name' => $r->title, 'category' => $r->category])->toArray();
        $this->provincialEducationOffices = ProvincialEducationOffice::active()->get();
    }

    private function resolveCurrentWorkplaceHierarchy(Workplaces $workplace): array
    {
        $hierarchyWorkplaceIds = $workplace->getAllParentWorkplaces();
        $hierarchyWorkplaces = Workplaces::query()
            ->whereIn('workplace_id', $hierarchyWorkplaceIds)
            ->get(['workplace_id', 'office_level_id'])
            ->keyBy('office_level_id');

        return [
            'province_id' => $workplace->peo_wp_id ?: $hierarchyWorkplaces->get('OLID003')?->workplace_id,
            'zone_id' => $workplace->zeo_wp_id ?: $hierarchyWorkplaces->get('OLID004')?->workplace_id,
        ];
    }

    private function loadExistingApplication($id)
    {
        $app = TeacherTransferApplicationModel::with(['preferences', 'policy', 'achievements'])->where('transfer_application_id', $id)->firstOrFail();

        // Security check
        if ($app->employee_id !== Auth::user()->people_id) {
            abort(403);
        }

        $this->isEditMode = true;
        $this->policyId = $app->policy_id;
        $this->transferCategoryId = $app->transfer_category;
        $this->transferReasonTypeId = $app->reason_category;
        $this->transferReason = $app->reason_details; // Assuming this field exists or check names
        $this->hasDisciplinaryActions = $app->has_disciplinary_actions;
        $this->disciplinaryDetails = $app->disciplinary_actions_details;
        $this->selectedProvinceId = $app->target_province;
        $this->declarationTrue = $app->is_declared;
        $this->permanentAddress = filled($app->permanent_address) ? trim((string) $app->permanent_address) : $this->permanentAddress;
        $this->temporaryAddress = $this->normalizeTemporaryAddress($app->temporary_address);
        $this->permanentLatitude = filled($app->latitude) ? (string) $app->latitude : $this->permanentLatitude;
        $this->permanentLongitude = filled($app->longitude) ? (string) $app->longitude : $this->permanentLongitude;
        $this->temporaryLatitude = filled($app->temp_latitude) ? (string) $app->temp_latitude : '';
        $this->temporaryLongitude = filled($app->temp_longitude) ? (string) $app->temp_longitude : '';

        // Trigger updates for dependent selects
        $this->updatedPolicyId($this->policyId);
        $this->updatedSelectedProvinceId($this->selectedProvinceId);

        // Load preferences
        $this->initPreferences();
        foreach ($app->preferences as $pref) {
            $this->selectedZones[$pref->preference_order] = $pref->zeo_wp_id;
            $this->institutionsLists[$pref->preference_order] = $this->fetchInstitutionsForZone($pref->zeo_wp_id);
            $this->preferences[$pref->preference_order] = $pref->ins_wp_id;
            $this->distanceInKm[$pref->preference_order] = $pref->distance;
        }

        $this->achievements = $app->achievements
            ->map(fn ($achievement) => [
                'achievement_type' => $achievement->achievement_type,
                'achievement_level' => $achievement->achievement_level,
                'title' => $achievement->title,
                'event_name' => $achievement->event_name,
                'achievement_date' => $achievement->achievement_date?->format('Y-m-d'),
                'details' => $achievement->details,
                'contribution_details' => $achievement->contribution_details,
                'is_included' => (bool) $achievement->is_included,
            ])
            ->values()
            ->all();

        $this->initAchievements();
    }

    private function initPreferences()
    {
        $policy = TransferPolicy::where('policy_id', $this->policyId)->first();
        $this->maxPreferences = $policy->max_preferences ?? 5;

        for ($i = 1; $i <= $this->maxPreferences; $i++) {
            if (!isset($this->selectedZones[$i])) $this->selectedZones[$i] = '';
            if (!isset($this->preferences[$i])) $this->preferences[$i] = '';
            if (!isset($this->distanceInKm[$i])) $this->distanceInKm[$i] = '';
            if (!isset($this->institutionsLists[$i])) $this->institutionsLists[$i] = collect();
        }
    }

    public function addAchievement(): void
    {
        $this->achievements[] = $this->blankAchievementRow();
    }

    public function removeAchievement(int $index): void
    {
        unset($this->achievements[$index]);
        $this->achievements = array_values($this->achievements);
        $this->initAchievements();
    }

    private function initAchievements(): void
    {
        if (empty($this->achievements)) {
            $this->achievements[] = $this->blankAchievementRow();
        }
    }

    private function blankAchievementRow(): array
    {
        return [
            'achievement_type' => '',
            'achievement_level' => '',
            'title' => '',
            'event_name' => '',
            'achievement_date' => '',
            'details' => '',
            'contribution_details' => '',
            'is_included' => true,
        ];
    }

    public function updatedPolicyId($value)
    {
        if ($value) {
            $policy = TransferPolicy::where('policy_id', $value)->first();
            $this->maxPreferences = $policy->max_preferences ?? 5;
            $this->transferCatagory = TransferCategory::scopedListQuery($value)
                ->orderBy('transfer_category_name')
                ->get()
                ->map(fn($c) => [
                    'id' => $c->transfer_category_id,
                    'name' => $c->transfer_category_name,
                    'office_level_id' => $c->office_level_id,
                    'targets_national_schools' => $this->categoryNameContains($c->transfer_category_name, self::NATIONAL_SCHOOL_CATEGORY_KEYWORDS),
                    'requires_province_selection' => $this->categoryNameContains($c->transfer_category_name, self::PROVINCE_SELECT_CATEGORY_KEYWORDS),
                ])
                ->toArray();
        } else {
            $this->transferCatagory = [];
        }

        if (!collect($this->transferCatagory)->contains('id', $this->transferCategoryId)) {
            $this->transferCategoryId = '';
        }

        $this->syncTargetProvinceForCategory();
        $this->resetPreferenceSelections();
        $this->initPreferences();
    }

    public function updatedTransferCategoryId($value)
    {
        $this->syncTargetProvinceForCategory();
        $this->refreshInstitutionLists();
    }

    public function updatedSelectedProvinceId($value)
    {
        $this->zonalEducationOffices = ZonalEducationOffice::active()->where('peo_wp_id', $value)->get();
        $this->resetPreferenceSelections();
        $this->initPreferences();
    }

    public function updated($property, $value)
    {
        if (str_starts_with($property, 'selectedZones.')) {
            $index = (int) explode('.', $property)[1];
            $this->preferences[$index] = '';
            $this->distanceInKm[$index] = '';
            $this->institutionsLists[$index] = $this->fetchInstitutionsForZone($value);
        }

        if (str_starts_with($property, 'preferences.')) {
            $index = (int) explode('.', $property)[1];
            $this->calculatePreferenceDistance($index);
        }

        if (in_array($property, ['permanentLatitude', 'permanentLongitude'], true)) {
            $this->calculateAllPreferenceDistances();
        }
    }

    private function fetchInstitutionsForZone($zoneId)
    {
        if (empty($zoneId)) return collect();

        return $this->selectedCategoryTargetsNationalSchools()
            ? Institution::active()->where('zeo_wp_id', $zoneId)->national()->get()
            : Institution::active()->where('zeo_wp_id', $zoneId)->provincial()->get();
    }

    public function rules()
    {
        $rules = [
            'policyId' => 'required',
            'transferCategoryId' => 'required',
            'transferReasonTypeId' => 'required',
            'permanentAddress' => 'required|string|max:255',
            'temporaryAddress' => 'nullable|string|max:255|required_with:temporaryLatitude,temporaryLongitude',
            'permanentLatitude' => 'nullable|numeric|between:-90,90|required_with:permanentLongitude',
            'permanentLongitude' => 'nullable|numeric|between:-180,180|required_with:permanentLatitude',
            'temporaryLatitude' => 'nullable|numeric|between:-90,90|required_with:temporaryLongitude',
            'temporaryLongitude' => 'nullable|numeric|between:-180,180|required_with:temporaryLatitude',
            'selectedProvinceId' => 'required|exists:provincial_education_offices,workplace_id',
            'preferences.1' => 'required',
            'distanceInKm.1' => 'required|numeric|min:0',
            'declarationTrue' => 'accepted',
        ];

        for ($i = 2; $i <= $this->maxPreferences; $i++) {
            if (!empty($this->preferences[$i])) {
                $rules["distanceInKm.{$i}"] = 'required|numeric|min:0';
            }
        }

        foreach ($this->achievements as $index => $achievement) {
            if (!$this->achievementRowHasData($achievement)) {
                continue;
            }

            $rules["achievements.{$index}.achievement_type"] = 'required|in:student,teacher';
            $rules["achievements.{$index}.achievement_level"] = 'required|in:zonal,district,provincial,national';
            $rules["achievements.{$index}.title"] = 'required|string|max:255';
            $rules["achievements.{$index}.event_name"] = 'nullable|string|max:255';
            $rules["achievements.{$index}.achievement_date"] = 'nullable|date';
            $rules["achievements.{$index}.details"] = 'nullable|string|max:2000';
            $rules["achievements.{$index}.contribution_details"] = ($achievement['achievement_type'] ?? '') === 'student'
                ? 'required|string|max:2000'
                : 'nullable|string|max:2000';
        }

        return $rules;
    }

    public function saveDraft()
    {
        try {
            $this->persistApplication('draft');
            session()->flash('success', __('Application saved as draft successfully.'));

            return redirect()->route('my-transfer.teacher-annual-transfer');
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', __('Unable to save the transfer application right now.'));
        }
    }

    public function submitApplication()
    {
        $this->validate();
        $this->step = 2;
    }

    public function goBack()
    {
        $this->step = 1;
    }

    public function confirmSubmission()
    {
        $this->validate();

        try {
            $this->persistApplication('submitted');
            session()->flash('success', __('Transfer Application submitted successfully.'));

            return redirect()->route('my-transfer.teacher-annual-transfer');
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', __('Unable to submit the transfer application right now.'));
        }
    }

    private function persistApplication($status)
    {
        try {
            DB::beginTransaction();
            $this->syncTargetProvinceForCategory();
            $transferType = TransferPolicy::where('policy_id', $this->policyId)->value('transfer_type') ?? 'annual';

            $data = [
                'policy_id' => $this->policyId,
                'appointment_id' => $this->appointmentId,
                'employee_id' => $this->employeeId,
                'current_workplace' => $this->currentWorkplaceId,
                'first_appointment_date' => $this->firstAppointmentDate,
                'current_workplace_join_date' => $this->currentWorkplaceJoinDate,
                'permanent_address' => trim((string) $this->permanentAddress),
                'latitude' => $this->normalizeCoordinate($this->permanentLatitude),
                'longitude' => $this->normalizeCoordinate($this->permanentLongitude),
                'temporary_address' => filled(trim((string) $this->temporaryAddress))
                    ? trim((string) $this->temporaryAddress)
                    : null,
                'temp_latitude' => $this->normalizeCoordinate($this->temporaryLatitude),
                'temp_longitude' => $this->normalizeCoordinate($this->temporaryLongitude),
                'transfer_type' => $transferType,
                'reason_category' => $this->transferReasonTypeId,
                'has_disciplinary_actions' => $this->hasDisciplinaryActions,
                'disciplinary_actions_details' => filled($this->disciplinaryDetails) ? $this->disciplinaryDetails : null,
                'transfer_category' => $this->transferCategoryId,
                'target_province' => $this->selectedProvinceId ?: $this->currentProvinceId,
                'is_declared' => $this->declarationTrue,
                'cwp_facilities_id' => filled($this->cwpFacilitiesId) ? $this->cwpFacilitiesId : null,
                'ns_cat' => filled($this->nsCat) ? $this->nsCat : null,
                'status' => $status,
            ];

            if ($this->isEditMode) {
                $application = TeacherTransferApplicationModel::where('transfer_application_id', $this->applicationId)->firstOrFail();
                $application->update($data);
                $application->preferences()->delete();
            } else {
                $data['current_step'] = 1;
                $application = TeacherTransferApplicationModel::create($data);
                $this->setupWorkflow($application);
            }

            foreach ($this->preferences as $order => $insId) {
                if ($insId) {
                    TeacherTransferApplicationPreferences::create([
                        'transfer_application_id' => $application->transfer_application_id,
                        'preference_order' => $order,
                        'zeo_wp_id' => $this->selectedZones[$order] ?? null,
                        'ins_wp_id' => $insId,
                        'distance' => !empty($this->distanceInKm[$order]) ? $this->distanceInKm[$order] : null,
                    ]);
                }
            }

            $application->achievements()->delete();
            foreach ($this->normalizedAchievementRows() as $achievement) {
                TeacherTransferApplicationAchievement::create([
                    'transfer_application_id' => $application->transfer_application_id,
                    'achievement_type' => $achievement['achievement_type'],
                    'achievement_level' => $achievement['achievement_level'],
                    'title' => $achievement['title'],
                    'event_name' => $achievement['event_name'],
                    'achievement_date' => $achievement['achievement_date'],
                    'details' => $achievement['details'],
                    'contribution_details' => $achievement['contribution_details'],
                    'is_included' => $achievement['is_included'],
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function setupWorkflow($application)
    {
        $policy = TransferPolicy::with('steps')->where('policy_id', $this->policyId)->first();
        if ($policy && $policy->steps->isNotEmpty()) {
            $currentWorkplace = Workplaces::find($this->currentWorkplaceId);
            $parentIds = $currentWorkplace ? $currentWorkplace->getAllParentWorkplaces() : [];
            $workplacesInHierarchy = Workplaces::whereIn('workplace_id', $parentIds)->get();
            $recommendationsData = [];
            foreach ($policy->steps->sortBy('step_order') as $step) {
                $targetId = ($step->office_level_id === 'OLID006') ? $this->currentWorkplaceId : $workplacesInHierarchy->firstWhere('office_level_id', $step->office_level_id)?->workplace_id;
                if (!$targetId && $step->office_level_id === 'OLID001') $targetId = $policy->transfer_authority;
                if ($targetId) $recommendationsData[] = ['workplace_id' => $targetId, 'recommendation_status' => false, 'active_status' => true];
            }
            if (!empty($recommendationsData)) $application->recommendations()->createMany($recommendationsData);
        }
    }

    public function render()
    {
        return view('livewire.teacher.transfer.teacher-transfer-application');
    }

    public function getSelectedTransferCategoryProperty(): ?array
    {
        return $this->selectedCategoryData();
    }

    public function getSelectedTransferCategoryNameProperty(): string
    {
        return $this->selectedCategoryData()['name'] ?? '';
    }

    public function getShouldChooseTargetProvinceProperty(): bool
    {
        return $this->shouldChooseTargetProvinceForSelectedCategory();
    }

    public function getResolvedTargetProvinceNameProperty(): string
    {
        $targetProvinceId = $this->selectedProvinceId ?: $this->currentProvinceId;

        return $this->provincialEducationOffices
            ->where('workplace_id', $targetProvinceId)
            ->first()
            ->name ?? 'N/A';
    }

    public function getResolvedTemporaryAddressProperty(): string
    {
        return filled(trim((string) $this->temporaryAddress))
            ? trim((string) $this->temporaryAddress)
            : __('Same as permanent address');
    }

    public function getResolvedPermanentCoordinatesProperty(): string
    {
        return $this->formatCoordinatePair($this->permanentLatitude, $this->permanentLongitude);
    }

    public function getResolvedTemporaryCoordinatesProperty(): string
    {
        return $this->formatCoordinatePair($this->temporaryLatitude, $this->temporaryLongitude);
    }

    private function selectedCategoryTargetsNationalSchools(): bool
    {
        return (bool) ($this->selectedCategoryData()['targets_national_schools'] ?? false);
    }

    private function syncTargetProvinceForCategory(): void
    {
        if (!$this->transferCategoryId) {
            return;
        }

        if (!$this->shouldChooseTargetProvinceForSelectedCategory() && $this->currentProvinceId) {
            $this->selectedProvinceId = $this->currentProvinceId;
        }

        if ($this->selectedProvinceId) {
            $this->zonalEducationOffices = ZonalEducationOffice::active()
                ->where('peo_wp_id', $this->selectedProvinceId)
                ->get();
        }
    }

    private function resetPreferenceSelections(): void
    {
        $this->selectedZones = [];
        $this->preferences = [];
        $this->distanceInKm = [];
        $this->institutionsLists = [];
    }

    private function refreshInstitutionLists(): void
    {
        foreach ($this->selectedZones as $index => $zoneId) {
            $this->institutionsLists[$index] = $this->fetchInstitutionsForZone($zoneId);
        }
    }

    private function selectedCategoryData(): ?array
    {
        return collect($this->transferCatagory)->firstWhere('id', $this->transferCategoryId);
    }

    private function shouldChooseTargetProvinceForSelectedCategory(): bool
    {
        return !$this->currentProvinceId || (bool) ($this->selectedCategoryData()['requires_province_selection'] ?? false);
    }

    private function categoryNameContains(?string $categoryName, array $needles): bool
    {
        $categoryName = strtolower((string) $categoryName);

        foreach ($needles as $needle) {
            if (str_contains($categoryName, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeTemporaryAddress(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '' || $value === __('Same as permanent address')) {
            return '';
        }

        return $value;
    }

    private function achievementRowHasData(array $achievement): bool
    {
        return collect([
            $achievement['achievement_type'] ?? '',
            $achievement['achievement_level'] ?? '',
            $achievement['title'] ?? '',
            $achievement['event_name'] ?? '',
            $achievement['achievement_date'] ?? '',
            $achievement['details'] ?? '',
            $achievement['contribution_details'] ?? '',
        ])->contains(fn ($value) => filled(trim((string) $value)));
    }

    private function normalizedAchievementRows(): array
    {
        return collect($this->achievements)
            ->filter(fn ($achievement) => $this->achievementRowHasData($achievement))
            ->map(fn ($achievement) => [
                'achievement_type' => $achievement['achievement_type'],
                'achievement_level' => $achievement['achievement_level'],
                'title' => trim((string) $achievement['title']),
                'event_name' => filled($achievement['event_name'] ?? null) ? trim((string) $achievement['event_name']) : null,
                'achievement_date' => filled($achievement['achievement_date'] ?? null) ? $achievement['achievement_date'] : null,
                'details' => filled($achievement['details'] ?? null) ? trim((string) $achievement['details']) : null,
                'contribution_details' => filled($achievement['contribution_details'] ?? null) ? trim((string) $achievement['contribution_details']) : null,
                'is_included' => (bool) ($achievement['is_included'] ?? true),
            ])
            ->values()
            ->all();
    }

    private function normalizeCoordinate($value): ?float
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '' || !is_numeric($value)) {
            return null;
        }

        return round((float) $value, 6);
    }

    private function calculateAllPreferenceDistances(): void
    {
        foreach (array_keys($this->preferences) as $index) {
            $this->calculatePreferenceDistance((int) $index);
        }
    }

    private function calculatePreferenceDistance(int $index): void
    {
        $schoolWorkplaceId = $this->preferences[$index] ?? null;

        if (!$schoolWorkplaceId) {
            $this->distanceInKm[$index] = '';

            return;
        }

        $origin = $this->coordinatePair($this->permanentLatitude, $this->permanentLongitude);

        if (!$origin) {
            return;
        }

        $institution = Institution::query()
            ->where('workplace_id', $schoolWorkplaceId)
            ->first(['workplace_id', 'latitude', 'longitude']);

        $destination = $this->coordinatePair($institution?->latitude, $institution?->longitude);

        if (!$destination) {
            return;
        }

        $this->distanceInKm[$index] = number_format(
            $this->roadDistanceKm($origin, $destination),
            2,
            '.',
            ''
        );
    }

    private function coordinatePair($latitude, $longitude): ?array
    {
        $latitude = $this->normalizeCoordinate($latitude);
        $longitude = $this->normalizeCoordinate($longitude);

        if ($latitude === null || $longitude === null) {
            return null;
        }

        if ($latitude > 50 && $longitude < 20) {
            [$latitude, $longitude] = [$longitude, $latitude];
        }

        return [
            'lat' => round($latitude, 7),
            'lng' => round($longitude, 7),
        ];
    }

    private function roadDistanceKm(array $origin, array $destination): float
    {
        try {
            $baseUrl = rtrim((string) config('services.osrm.url', 'https://router.project-osrm.org'), '/');
            $url = sprintf(
                '%s/route/v1/driving/%s,%s;%s,%s',
                $baseUrl,
                $origin['lng'],
                $origin['lat'],
                $destination['lng'],
                $destination['lat']
            );

            $response = Http::timeout($this->osrmTimeout())
                ->connectTimeout($this->osrmConnectTimeout())
                ->withOptions(['verify' => $this->osrmVerifySsl()])
                ->acceptJson()
                ->get($url, [
                    'overview' => 'false',
                    'alternatives' => 'false',
                ]);

            $meters = $response->json('routes.0.distance');

            if ($response->ok() && is_numeric($meters)) {
                return round(((float) $meters) / 1000, 2);
            }
        } catch (\Throwable $exception) {
            $this->logOsrmFailure($exception);
        }

        return $this->haversineDistanceKm($origin['lat'], $origin['lng'], $destination['lat'], $destination['lng']);
    }

    private function osrmTimeout(): int
    {
        return max(1, (int) config('services.osrm.timeout', 5));
    }

    private function osrmConnectTimeout(): int
    {
        return max(1, (int) config('services.osrm.connect_timeout', 3));
    }

    private function osrmVerifySsl(): bool
    {
        return filter_var(config('services.osrm.verify_ssl', true), FILTER_VALIDATE_BOOLEAN);
    }

    private function logOsrmFailure(\Throwable $exception): void
    {
        if (!filter_var(config('services.osrm.log_failures', false), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }

        Log::warning('OSRM road distance lookup failed; using straight-line fallback.', [
            'message' => $exception->getMessage(),
        ]);
    }

    private function haversineDistanceKm(float $originLat, float $originLng, float $destinationLat, float $destinationLng): float
    {
        $earthRadiusKm = 6371;
        $deltaLat = deg2rad($destinationLat - $originLat);
        $deltaLng = deg2rad($destinationLng - $originLng);

        $a = sin($deltaLat / 2) ** 2
            + cos(deg2rad($originLat)) * cos(deg2rad($destinationLat)) * sin($deltaLng / 2) ** 2;

        return round($earthRadiusKm * (2 * atan2(sqrt($a), sqrt(1 - $a))), 2);
    }

    private function formatCoordinatePair($latitude, $longitude): string
    {
        $latitude = $this->normalizeCoordinate($latitude);
        $longitude = $this->normalizeCoordinate($longitude);

        if ($latitude === null || $longitude === null) {
            return __('Not pinned');
        }

        return number_format($latitude, 6, '.', '') . ', ' . number_format($longitude, 6, '.', '');
    }
}
