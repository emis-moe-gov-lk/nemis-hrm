<?php

namespace App\Livewire\TransferModule\TransferBoard;

use App\Helpers\NicHelper;
use App\Models\People;
use App\Models\SubjectList;
use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferAppeals;
use App\Models\TransferBoard;
use App\Models\TransferBoardMemberAttendances;
use App\Models\TransferBoardMembers;
use App\Models\TransferBoardSubject;
use App\Models\TransferCategory;
use App\Models\TransferPolicy;
use App\Models\Workplaces;
use App\Services\TransferModule\TransferBoardSchoolBalanceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class ProvinceTeacherTransferBoard extends Component
{
    use WithPagination;

    protected string $boardType = TransferBoard::TYPE_TRANSFER;

    public string $boardDate = '';
    public string $boardSearch = '';
    public string $boardStatusFilter = 'open';
    public string $selectedBoardId = '';
    public string $superAdminProvinceWorkplaceId = '';

    public string $applicationSearch = '';
    public string $applicationStatus = '';

    public bool $showCreateBoardFlow = false;
    public int $createBoardStep = 1;
    public string $editingBoardId = '';
    public bool $showManageMembersModal = false;

    public string $createBoardDate = '';
    public string $createBoardName = '';
    public bool $createBoardNameWasEdited = false;
    public string $createPolicyId = '';
    public string $createTransferCategoryId = '';
    public array $selectedSubjectIds = [];

    public string $chairmanNic = '';
    public ?string $chairmanPeopleId = null;
    public array $chairmanCandidate = [];

    public string $secretaryNic = '';
    public ?string $secretaryPeopleId = null;
    public array $secretaryCandidate = [];

    public string $memberNic = '';
    public ?string $memberPeopleId = null;
    public array $memberCandidate = [];
    public array $createMembers = [];

    public ?string $manageMembersBoardId = null;
    public string $manageMemberNic = '';
    public ?string $manageMemberPeopleId = null;
    public array $manageMemberCandidate = [];

    public bool $showAttendanceModal = false;
    public bool $attendanceReadOnly = false;
    public ?string $attendanceBoardId = null;
    public string $attendanceDate = '';
    public array $attendanceRows = [];
    public string $attendanceMemberNic = '';
    public ?string $attendanceMemberPeopleId = null;
    public array $attendanceMemberCandidate = [];

    protected $queryString = [
        'selectedBoardId' => ['except' => ''],
        'superAdminProvinceWorkplaceId' => ['except' => ''],
    ];

    public function mount(): void
    {
        $today = now()->toDateString();

        $this->boardDate = $this->boardDate ?: $today;
        $this->createBoardDate = $today;

        if ($this->isSuperAdmin() && !$this->superAdminProvinceWorkplaceId) {
            $this->superAdminProvinceWorkplaceId = $this->preferredSuperAdminProvinceWorkplaceId();
        }
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['boardSearch', 'boardStatusFilter', 'superAdminProvinceWorkplaceId'], true)) {
            $this->resetPage('boardPage');
        }

        if (in_array($propertyName, ['applicationSearch', 'applicationStatus', 'selectedBoardId'], true)) {
            $this->resetPage('applicationPage');
        }
    }

    public function updatedCreatePolicyId(): void
    {
        $this->createTransferCategoryId = '';
        $this->refreshSuggestedBoardName();
    }

    public function updatedCreateTransferCategoryId(): void
    {
        $this->refreshSuggestedBoardName();
    }

    public function updatedCreateBoardDate(): void
    {
        $this->refreshSuggestedBoardName();
    }

    public function updatedCreateBoardName($value): void
    {
        $this->createBoardNameWasEdited = filled(trim((string) $value));
    }

    public function updatedSuperAdminProvinceWorkplaceId(): void
    {
        $this->selectedBoardId = '';
        $this->applicationSearch = '';
        $this->applicationStatus = '';
        $this->showCreateBoardFlow = false;
        $this->resetCreateBoardForm();
        $this->resetPage('boardPage');
        $this->resetPage('applicationPage');
    }

    public function updatedShowManageMembersModal(bool $value): void
    {
        if (!$value) {
            $this->resetManageMembersState();
        }
    }

    public function updatedShowAttendanceModal(bool $value): void
    {
        if (!$value) {
            $this->resetAttendanceState();
        }
    }

    public function updatedAttendanceDate(): void
    {
        if ($this->showAttendanceModal && $this->attendanceBoardId) {
            $this->loadAttendanceRows();
        }
    }

    protected function isSuperAdmin(): bool
    {
        return (bool) auth()->user()?->hasRole('super admin');
    }

    protected function isAppealBoardType(): bool
    {
        return $this->boardType === TransferBoard::TYPE_APPEAL;
    }

    public function getIsAppealBoardProperty(): bool
    {
        return $this->isAppealBoardType();
    }

    protected function boardNoun(): string
    {
        return $this->isAppealBoardType() ? 'appeal board' : 'transfer board';
    }

    protected function boardNounTitle(): string
    {
        return $this->isAppealBoardType() ? 'Appeal Board' : 'Transfer Board';
    }

    protected function boardPageTitle(): string
    {
        return $this->isAppealBoardType()
            ? $this->boardScopeTitle() . ' Teacher Appeal Board'
            : $this->boardScopeTitle() . ' Teacher Transfer Board';
    }

    protected function boardDecisionReportRoute(): string
    {
        return $this->isAppealBoardType()
            ? 'transfer.transfer-board.appeal-report.download'
            : 'transfer.transfer-board.decision-report.download';
    }

    protected function boardRouteScope(): string
    {
        return 'province';
    }

    protected function boardScopeOfficeLevelId(): string
    {
        return 'OLID003';
    }

    protected function boardScopeRelationName(): string
    {
        return 'provincial';
    }

    protected function boardScopeTitle(): string
    {
        return 'Provincial';
    }

    protected function boardScopeNameLower(): string
    {
        return 'province';
    }

    protected function boardScopeNamePlural(): string
    {
        return 'provinces';
    }

    protected function boardScopeAdjectiveLower(): string
    {
        return strtolower($this->boardScopeTitle());
    }

    protected function currentProvincialWorkplace(): ?Workplaces
    {
        return $this->currentScopeWorkplace();
    }

    protected function currentScopeWorkplace(): ?Workplaces
    {
        if ($this->isSuperAdmin()) {
            $scopeWorkplaceId = $this->superAdminProvinceWorkplaceId;

            if (!$scopeWorkplaceId) {
                return null;
            }

            return Workplaces::with('officeLevel')
                ->where('office_level_id', $this->boardScopeOfficeLevelId())
                ->find($scopeWorkplaceId);
        }

        $userWorkplaceId = auth()->user()?->workplace_id;

        if (!$userWorkplaceId) {
            return null;
        }

        $workplace = Workplaces::with('officeLevel')->find($userWorkplaceId);

        if (!$workplace || $workplace->office_level_id !== $this->boardScopeOfficeLevelId()) {
            return null;
        }

        return $workplace;
    }

    protected function emptyBoardQuery(): Builder
    {
        return TransferBoard::query()->whereIn('id', []);
    }

    protected function emptyApplicationQuery(): Builder
    {
        return TeacherTransferApplication::query()->whereIn('id', []);
    }

    protected function emptyAppealQuery(): Builder
    {
        return TeacherTransferAppeals::query()->whereIn('id', []);
    }

    protected function emptyCategoryQuery(): Builder
    {
        return TransferCategory::query()->whereIn('id', []);
    }

    protected function scopedBoardQuery(): Builder
    {
        $workplace = $this->currentProvincialWorkplace();

        if (!$workplace) {
            return $this->emptyBoardQuery();
        }

        return TransferBoard::query()
            ->ofType($this->boardType)
            ->where('bo_office_level_id', $workplace->office_level_id)
            ->where('bo_workplace_id', $workplace->workplace_id);
    }

    protected function boardListQuery(): Builder
    {
        $search = trim($this->boardSearch);

        $query = $this->scopedBoardQuery()
            ->with([
                'policy',
                'category',
                'chairman',
                'secretary',
                'members.person',
                'subjects',
            ]);

        if ($this->boardStatusFilter === 'closed') {
            $query->closed();
        } elseif ($this->boardStatusFilter !== 'all') {
            $query->onProgress();
        }

        if ($search !== '') {
            $query->where('board_name', 'like', '%' . $search . '%');
        }

        return $query->orderBy('start_date')->latest('created_at');
    }

    protected function scopedApplicationQuery(): Builder
    {
        $workplace = $this->currentProvincialWorkplace();

        if (!$workplace) {
            return $this->emptyApplicationQuery();
        }

        $childWorkplaceIds = $workplace->getAllChildWorkplaces();

        return TeacherTransferApplication::query()
            ->where(function (Builder $query) use ($workplace, $childWorkplaceIds) {
                $query->where('target_province', $workplace->workplace_id)
                    ->orWhereIn('current_workplace', $childWorkplaceIds);
            });
    }

    protected function scopedAppealQuery(): Builder
    {
        $workplace = $this->currentProvincialWorkplace();

        if (!$workplace) {
            return $this->emptyAppealQuery();
        }

        $childWorkplaceIds = $workplace->getAllChildWorkplaces();

        return TeacherTransferAppeals::query()
            ->where('active_status', true)
            ->whereHas('application', function (Builder $query) use ($workplace, $childWorkplaceIds) {
                $query->where(function (Builder $innerQuery) use ($workplace, $childWorkplaceIds) {
                    $innerQuery->where('target_province', $workplace->workplace_id)
                        ->orWhereIn('current_workplace', $childWorkplaceIds);
                });
            });
    }

    public function isIncomingApplication(?TeacherTransferApplication $application): bool
    {
        $workplace = $this->currentWorkplace;

        return $application !== null
            && $workplace !== null
            && (string) $application->target_province === (string) $workplace->workplace_id;
    }

    protected function resolvedBoardDate(): string
    {
        try {
            return Carbon::parse($this->boardDate)->toDateString();
        } catch (\Throwable $exception) {
            return now()->toDateString();
        }
    }

    protected function hasConfiguredCategoriesForWorkplace(Workplaces $workplace): bool
    {
        $query = TransferCategory::active()
            ->forOfficeLevel($workplace->office_level_id);

        return $this->applyCategoryOwnerScope($query, $workplace)->exists();
    }

    protected function allowedCategoryOwnerWorkplaceIds(?Workplaces $workplace = null): array
    {
        $workplace ??= $this->currentProvincialWorkplace();

        if (!$workplace) {
            return [];
        }

        $ownerWorkplaceIds = $workplace->getAllParentWorkplaces();
        $ownerWorkplaceIds[] = 'MOE0000001';

        return array_values(array_unique(array_filter($ownerWorkplaceIds)));
    }

    protected function applyCategoryOwnerScope(Builder $query, ?Workplaces $workplace = null): Builder
    {
        $ownerWorkplaceIds = $this->allowedCategoryOwnerWorkplaceIds($workplace);

        if (empty($ownerWorkplaceIds)) {
            return $query->whereIn('id', []);
        }

        return $query->whereIn('transfer_owner_workplace_id', $ownerWorkplaceIds);
    }

    protected function preferredSuperAdminProvinceWorkplaceId(): string
    {
        $scopes = $this->availableProvincialScopes;

        if ($scopes->isEmpty()) {
            return '';
        }

        $preferredScope = $scopes->first(
            fn (Workplaces $workplace) => $this->hasConfiguredCategoriesForWorkplace($workplace)
        );

        return (string) ($preferredScope?->workplace_id ?? $scopes->first()?->workplace_id ?? '');
    }

    protected function availableCategoryQuery(?string $policyId = null): Builder
    {
        $workplace = $this->currentProvincialWorkplace();

        if (!$workplace || !$policyId) {
            return $this->emptyCategoryQuery();
        }

        $query = TransferCategory::query()
            ->active()
            ->forPolicy($policyId)
            ->forOfficeLevel($workplace->office_level_id);

        return $this->applyCategoryOwnerScope($query, $workplace);
    }

    protected function isNicSearch(string $value): bool
    {
        $normalizedNic = NicHelper::normalize($value);

        return in_array(strlen($normalizedNic), [10, 12], true);
    }

    protected function lookupPersonByNic(string $nic, string $errorKey): ?array
    {
        $this->resetErrorBag($errorKey);

        $validator = Validator::make(
            ['nic' => $nic],
            ['nic' => ['required', 'regex:/^(?:[0-9]{9}[VvXx]|[0-9]{12})$/']],
            [
                'nic.required' => 'NIC number is required.',
                'nic.regex' => 'Please enter a valid NIC number.',
            ]
        );

        if ($validator->fails()) {
            $this->addError($errorKey, $validator->errors()->first('nic'));

            return null;
        }

        $normalizedNic = NicHelper::normalize($nic);

        if (!NicHelper::checkNicValid($normalizedNic)) {
            $this->addError($errorKey, 'Invalid NIC number.');

            return null;
        }

        $person = People::with([
            'currentAppointment.workplace',
            'currentAppointment.officeLevel',
        ])->where('nic_hash', NicHelper::hash($normalizedNic))->first();

        if (!$person) {
            $this->addError($errorKey, 'No person found for the provided NIC number.');

            return null;
        }

        return [
            'people_id' => $person->people_id,
            'full_name' => $person->full_name,
            'nic' => $person->nic,
            'workplace_name' => $person->currentAppointment?->workplace?->office_name ?? 'No active workplace',
            'office_level_name' => $person->currentAppointment?->officeLevel?->office_level_name ?? 'No active office level',
            'appointment_id' => $person->currentAppointment?->appointment_id,
        ];
    }

    protected function mapPersonCandidate(?People $person): array
    {
        if (!$person) {
            return [];
        }

        $person->loadMissing([
            'currentAppointment.workplace',
            'currentAppointment.officeLevel',
        ]);

        return [
            'people_id' => $person->people_id,
            'full_name' => $person->full_name,
            'nic' => $person->nic,
            'workplace_name' => $person->currentAppointment?->workplace?->office_name ?? 'No active workplace',
            'office_level_name' => $person->currentAppointment?->officeLevel?->office_level_name ?? 'No active office level',
            'appointment_id' => $person->currentAppointment?->appointment_id,
        ];
    }

    protected function findScopedBoard(string $boardId, array $with = []): ?TransferBoard
    {
        return $this->scopedBoardQuery()
            ->with($with)
            ->where('board_id', $boardId)
            ->first();
    }

    protected function ensureBoardIsEditable(?TransferBoard $board, ?string $message = null): bool
    {
        if (!$board) {
            session()->flash('error', $message ?? 'The selected ' . $this->boardNoun() . ' is not available in your current scope.');

            return false;
        }

        if ($board->isClosed()) {
            session()->flash('error', 'Closed ' . $this->boardNoun() . 's can only be viewed.');

            return false;
        }

        return true;
    }

    protected function buildBoardName(Workplaces $workplace, TransferCategory $category, string $boardDate): string
    {
        return trim(sprintf(
            '%s %s %s - %s',
            $workplace->office_name,
            $category->transfer_category_name,
            $this->isAppealBoardType() ? 'Appeal Board' : 'Board',
            Carbon::parse($boardDate)->format('Y-m-d')
        ));
    }

    protected function resolvedBoardName(Workplaces $workplace, TransferCategory $category, array $validated): string
    {
        $boardName = trim((string) ($validated['createBoardName'] ?? ''));

        return $boardName !== ''
            ? $boardName
            : $this->buildBoardName($workplace, $category, $validated['createBoardDate']);
    }

    protected function refreshSuggestedBoardName(bool $force = false): void
    {
        if (!$force && $this->createBoardNameWasEdited && filled($this->createBoardName)) {
            return;
        }

        $this->createBoardName = $this->generateSuggestedCreateBoardName();
        $this->createBoardNameWasEdited = false;
    }

    protected function syncBoardConfiguration(TransferBoard $board, array $validated): void
    {
        $workplace = $this->currentProvincialWorkplace();

        if (!$workplace) {
            throw ValidationException::withMessages([
                'createBoardDate' => 'Only ' . $this->boardScopeAdjectiveLower() . ' users can configure ' . $this->boardScopeAdjectiveLower() . ' boards.',
            ]);
        }

        $category = $this->availableCategoryQuery($validated['createPolicyId'])
            ->where('transfer_category_id', $validated['createTransferCategoryId'])
            ->first();

        if (!$category) {
            throw ValidationException::withMessages([
                'createTransferCategoryId' => 'Select a valid transfer category for this ' . $this->boardScopeNameLower() . '.',
            ]);
        }

        $board->fill([
            'policy_id' => $validated['createPolicyId'],
            'transfer_category_id' => $validated['createTransferCategoryId'],
            'bo_office_level_id' => $workplace->office_level_id,
            'bo_workplace_id' => $workplace->workplace_id,
            'board_type' => $this->boardType,
            'board_name' => $this->resolvedBoardName($workplace, $category, $validated),
            'start_date' => $validated['createBoardDate'],
            'end_date' => $validated['createBoardDate'],
            'board_status' => $board->board_status ?: TransferBoard::STATUS_ON_PROGRESS,
            'chairman_id' => $this->chairmanPeopleId,
            'secretary_id' => $this->secretaryPeopleId,
        ]);

        $board->save();

        $board->members()->delete();
        $board->subjectLinks()->delete();

        TransferBoardMembers::create([
            'board_id' => $board->board_id,
            'people_id' => $this->chairmanPeopleId,
            'role' => 'Chairman',
            'active_status' => true,
        ]);

        TransferBoardMembers::create([
            'board_id' => $board->board_id,
            'people_id' => $this->secretaryPeopleId,
            'role' => 'Secretary',
            'active_status' => true,
        ]);

        foreach ($this->createMembers as $member) {
            TransferBoardMembers::create([
                'board_id' => $board->board_id,
                'people_id' => $member['people_id'],
                'role' => 'Member',
                'active_status' => true,
            ]);
        }

        foreach ($validated['selectedSubjectIds'] as $subjectId) {
            TransferBoardSubject::create([
                'board_id' => $board->board_id,
                'subject_id' => $subjectId,
                'active_status' => true,
            ]);
        }
    }

    protected function syncCreateMembers(): void
    {
        $this->createMembers = collect($this->createMembers)
            ->filter(fn ($member) => filled($member['people_id'] ?? null))
            ->unique('people_id')
            ->values()
            ->all();
    }

    protected function resetCreateBoardForm(): void
    {
        $this->resetErrorBag();

        $this->createBoardStep = 1;
        $this->editingBoardId = '';
        $this->createBoardDate = now()->toDateString();
        $this->createBoardName = '';
        $this->createBoardNameWasEdited = false;
        $this->createPolicyId = '';
        $this->createTransferCategoryId = '';
        $this->selectedSubjectIds = [];

        $this->chairmanNic = '';
        $this->chairmanPeopleId = null;
        $this->chairmanCandidate = [];

        $this->secretaryNic = '';
        $this->secretaryPeopleId = null;
        $this->secretaryCandidate = [];

        $this->memberNic = '';
        $this->memberPeopleId = null;
        $this->memberCandidate = [];
        $this->createMembers = [];
    }

    protected function resetManageMembersState(): void
    {
        $this->manageMembersBoardId = null;
        $this->manageMemberNic = '';
        $this->manageMemberPeopleId = null;
        $this->manageMemberCandidate = [];
        $this->resetErrorBag();
    }

    protected function resetAttendanceState(): void
    {
        $this->attendanceReadOnly = false;
        $this->attendanceBoardId = null;
        $this->attendanceDate = '';
        $this->attendanceRows = [];
        $this->attendanceMemberNic = '';
        $this->attendanceMemberPeopleId = null;
        $this->attendanceMemberCandidate = [];
        $this->resetErrorBag();
    }

    protected function resolvedAttendanceDate(): string
    {
        try {
            return Carbon::parse($this->attendanceDate ?: now()->toDateString())->toDateString();
        } catch (\Throwable $exception) {
            return now()->toDateString();
        }
    }

    protected function syncCoreBoardMembersForAttendance(TransferBoard $board): void
    {
        foreach ([['people_id' => $board->chairman_id, 'role' => 'Chairman'], ['people_id' => $board->secretary_id, 'role' => 'Secretary']] as $coreMember) {
            if (!filled($coreMember['people_id'])) {
                continue;
            }

            $member = TransferBoardMembers::query()
                ->where('board_id', $board->board_id)
                ->where('people_id', $coreMember['people_id'])
                ->first();

            if ($member) {
                $roleExistsForAnotherMember = TransferBoardMembers::query()
                    ->where('board_id', $board->board_id)
                    ->where('id', '!=', $member->id)
                    ->whereIn('role', [$coreMember['role'], strtolower($coreMember['role']), strtoupper($coreMember['role'])])
                    ->exists();

                if (!$roleExistsForAnotherMember && ($member->role !== $coreMember['role'] || !$member->active_status)) {
                    $member->update([
                        'role' => $coreMember['role'],
                        'active_status' => true,
                    ]);
                } elseif (!$member->active_status) {
                    $member->update(['active_status' => true]);
                }

                continue;
            }

            try {
                TransferBoardMembers::create([
                    'board_id' => $board->board_id,
                    'people_id' => $coreMember['people_id'],
                    'role' => $coreMember['role'],
                    'active_status' => true,
                ]);
            } catch (ValidationException $exception) {
                // If older data already has conflicting core roles, leave it unchanged and show existing rows.
            }
        }
    }

    protected function loadAttendanceRows(): void
    {
        $board = $this->findAttendanceBoardWithMembers();

        if (!$board) {
            $this->attendanceRows = [];

            return;
        }

        $date = $this->resolvedAttendanceDate();
        $this->attendanceDate = $date;

        $roleOrder = [
            'chairman' => 0,
            'secretary' => 1,
            'member' => 2,
        ];

        $members = $board->members
            ->filter(fn (TransferBoardMembers $member) => $member->active_status && filled($member->tbm_id))
            ->sortBy(fn (TransferBoardMembers $member) => sprintf(
                '%02d-%s',
                $roleOrder[strtolower((string) $member->role)] ?? 9,
                $member->person?->full_name ?? ''
            ))
            ->values();

        $attendances = TransferBoardMemberAttendances::query()
            ->whereIn('tbm_id', $members->pluck('tbm_id')->all())
            ->whereDate('attendance_date', $date)
            ->get()
            ->keyBy('tbm_id');

        $defaultStatus = $this->attendanceReadOnly ? 'not_marked' : 'present';

        $this->attendanceRows = $members
            ->mapWithKeys(function (TransferBoardMembers $member) use ($attendances, $date, $defaultStatus) {
                $attendance = $attendances->get($member->tbm_id);

                return [
                    $member->id => [
                        'member_id' => $member->id,
                        'tbm_id' => $member->tbm_id,
                        'people_id' => $member->people_id,
                        'role' => $member->role,
                        'name' => $member->person?->full_name ?? 'N/A',
                        'nic' => $member->person?->nic ?? 'NIC unavailable',
                        'workplace' => $member->person?->currentAppointment?->workplace?->office_name ?? 'No active workplace',
                        'office_level' => $member->person?->currentAppointment?->officeLevel?->office_level_name ?? 'No active office level',
                        'attendance_date' => $attendance?->attendance_date?->toDateString() ?? $date,
                        'status' => $attendance?->attendance_status ?? $defaultStatus,
                        'remarks' => $attendance?->remarks ?? '',
                        'has_record' => (bool) $attendance,
                    ],
                ];
            })
            ->all();
    }

    protected function findAttendanceBoardWithMembers(): ?TransferBoard
    {
        if (!$this->attendanceBoardId) {
            return null;
        }

        return $this->scopedBoardQuery()
            ->with([
                'policy',
                'category',
                'chairman.currentAppointment.workplace',
                'chairman.currentAppointment.officeLevel',
                'secretary.currentAppointment.workplace',
                'secretary.currentAppointment.officeLevel',
                'members.person.currentAppointment.workplace',
                'members.person.currentAppointment.officeLevel',
                'subjects',
            ])
            ->where('board_id', $this->attendanceBoardId)
            ->first();
    }

    protected function validateCreateBoardStep(int $step): void
    {
        if ($step === 1) {
            $this->validate([
                'createBoardDate' => ['required', 'date'],
                'createBoardName' => ['nullable', 'string', 'max:255'],
                'createPolicyId' => ['required', 'exists:transfer_policies,policy_id'],
                'createTransferCategoryId' => ['required', 'exists:transfer_categories,transfer_category_id'],
            ]);

            $category = $this->availableCategoryQuery($this->createPolicyId)
                ->where('transfer_category_id', $this->createTransferCategoryId)
                ->first();

            if (!$category) {
                $this->addError('createTransferCategoryId', 'Select a valid transfer category for this ' . $this->boardScopeNameLower() . '.');

                throw ValidationException::withMessages([
                    'createTransferCategoryId' => 'Select a valid transfer category for this ' . $this->boardScopeNameLower() . '.',
                ]);
            }

            return;
        }

        if ($step === 2) {
            $this->validate([
                'selectedSubjectIds' => ['required', 'array', 'min:1'],
                'selectedSubjectIds.*' => ['required', 'exists:subject_lists,subject_id'],
            ], [
                'selectedSubjectIds.required' => 'Select at least one subject for the board.',
                'selectedSubjectIds.min' => 'Select at least one subject for the board.',
            ]);

            return;
        }

        if ($step === 3) {
            $this->validate([
                'chairmanPeopleId' => ['required', 'exists:people,people_id'],
                'secretaryPeopleId' => ['required', 'exists:people,people_id'],
            ], [
                'chairmanPeopleId.required' => 'Select a board chairman by NIC.',
                'secretaryPeopleId.required' => 'Select a board secretary by NIC.',
            ]);

            if ($this->chairmanPeopleId === $this->secretaryPeopleId) {
                $this->addError('secretaryNic', 'Chairman and secretary must be different people.');

                throw ValidationException::withMessages([
                    'secretaryNic' => 'Chairman and secretary must be different people.',
                ]);
            }
        }
    }

    protected function validateCreateBoardData(): array
    {
        $this->syncCreateMembers();

        return $this->validate([
            'createBoardDate' => ['required', 'date'],
            'createBoardName' => ['nullable', 'string', 'max:255'],
            'createPolicyId' => ['required', 'exists:transfer_policies,policy_id'],
            'createTransferCategoryId' => ['required', 'exists:transfer_categories,transfer_category_id'],
            'selectedSubjectIds' => ['required', 'array', 'min:1'],
            'selectedSubjectIds.*' => ['required', 'exists:subject_lists,subject_id'],
            'chairmanPeopleId' => ['required', 'exists:people,people_id'],
            'secretaryPeopleId' => ['required', 'exists:people,people_id'],
        ], [
            'selectedSubjectIds.required' => 'Select at least one subject for the board.',
            'selectedSubjectIds.min' => 'Select at least one subject for the board.',
            'chairmanPeopleId.required' => 'Select a board chairman by NIC.',
            'secretaryPeopleId.required' => 'Select a board secretary by NIC.',
        ]);
    }

    public function getBoardsProperty()
    {
        return $this->boardListQuery()->paginate(8, ['*'], 'boardPage');
    }

    public function getSelectedBoardProperty(): ?TransferBoard
    {
        if (!$this->selectedBoardId) {
            return null;
        }

        return $this->scopedBoardQuery()
            ->with([
                'policy',
                'category',
                'chairman',
                'secretary',
                'members.person.currentAppointment.workplace',
                'members.person.currentAppointment.officeLevel',
                'subjects',
                'workplace',
                'officeLevel',
            ])
            ->where('board_id', $this->selectedBoardId)
            ->first();
    }

    public function getApplicationsProperty()
    {
        if ($this->isAppealBoardType()) {
            return $this->appealRecords();
        }

        $selectedBoard = $this->selectedBoard;

        if (!$selectedBoard) {
            return $this->emptyApplicationQuery()->paginate(10, ['*'], 'applicationPage');
        }

        $subjectIds = $selectedBoard->subjects->pluck('subject_id')->filter()->values()->all();
        $search = trim($this->applicationSearch);

        $query = $this->scopedApplicationQuery()
            ->with([
                'employee',
                'currentWorkplace',
                'category',
                'targetProvince',
                'policy',
                'preferences',
                'teacher.appointmentSubject',
                'teacher.mainSubject',
                'teacher.secondarySubject',
                'teacher.currentTeachingSubject',
                'boardRecommendation.recommendationList',
                'boardRecommendation.selectedSchool',
                'boardRecommendation.selectedZone',
            ])
            ->where('policy_id', $selectedBoard->policy_id)
            ->where('transfer_category', $selectedBoard->transfer_category_id);

        if (!empty($subjectIds)) {
            $query->whereHas('teacher', function (Builder $teacherQuery) use ($subjectIds) {
                $teacherQuery->whereIn('main_subject', $subjectIds);
            });
        }

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('transfer_application_id', 'like', '%' . $search . '%');

                if ($this->isNicSearch($search)) {
                    $normalizedNic = NicHelper::normalize($search);

                    if (NicHelper::checkNicValid($normalizedNic)) {
                        $builder->orWhereHas('employee', function (Builder $employeeQuery) use ($normalizedNic) {
                            $employeeQuery->where('nic_hash', NicHelper::hash($normalizedNic));
                        });
                    }
                }
            });
        }

        if ($this->applicationStatus !== '') {
            $query->where('status', $this->applicationStatus);
        }

        return $query->latest()->paginate(10, ['*'], 'applicationPage');
    }

    protected function appealRecords()
    {
        $selectedBoard = $this->selectedBoard;

        if (!$selectedBoard) {
            return $this->emptyAppealQuery()->paginate(10, ['*'], 'applicationPage');
        }

        $subjectIds = $selectedBoard->subjects->pluck('subject_id')->filter()->values()->all();
        $search = trim($this->applicationSearch);

        $query = $this->scopedAppealQuery()
            ->with([
                'application.employee',
                'application.currentWorkplace',
                'application.category',
                'application.targetProvince',
                'application.policy',
                'application.preferences',
                'application.teacher.appointmentSubject',
                'application.teacher.mainSubject',
                'application.teacher.secondarySubject',
                'application.teacher.currentTeachingSubject',
                'application.boardRecommendation.recommendationList',
                'application.boardRecommendation.selectedSchool',
                'application.boardRecommendation.selectedZone',
                'board',
                'selectedZone',
                'selectedSchool',
            ])
            ->where('policy_id', $selectedBoard->policy_id)
            ->whereHas('application', function (Builder $applicationQuery) use ($selectedBoard, $subjectIds) {
                $applicationQuery->where('transfer_category', $selectedBoard->transfer_category_id);

                if (!empty($subjectIds)) {
                    $applicationQuery->whereHas('teacher', function (Builder $teacherQuery) use ($subjectIds) {
                        $teacherQuery->whereIn('main_subject', $subjectIds);
                    });
                }
            });

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('appeal_id', 'like', '%' . $search . '%')
                    ->orWhere('transfer_application_id', 'like', '%' . $search . '%');

                if ($this->isNicSearch($search)) {
                    $normalizedNic = NicHelper::normalize($search);

                    if (NicHelper::checkNicValid($normalizedNic)) {
                        $builder->orWhereHas('application.employee', function (Builder $employeeQuery) use ($normalizedNic) {
                            $employeeQuery->where('nic_hash', NicHelper::hash($normalizedNic));
                        });
                    }
                }
            });
        }

        if ($this->applicationStatus !== '') {
            $query->where('appeal_status', $this->applicationStatus);
        }

        return $query
            ->orderBy('appeal_status')
            ->latest()
            ->paginate(10, ['*'], 'applicationPage');
    }

    public function getManagedBoardProperty(): ?TransferBoard
    {
        if (!$this->manageMembersBoardId) {
            return null;
        }

        return $this->scopedBoardQuery()
            ->with([
                'chairman.currentAppointment.workplace',
                'chairman.currentAppointment.officeLevel',
                'secretary.currentAppointment.workplace',
                'secretary.currentAppointment.officeLevel',
                'members.person.currentAppointment.workplace',
                'members.person.currentAppointment.officeLevel',
                'subjects',
            ])
            ->where('board_id', $this->manageMembersBoardId)
            ->first();
    }

    public function getAttendanceBoardProperty(): ?TransferBoard
    {
        return $this->findAttendanceBoardWithMembers();
    }

    public function getBoardSchoolBalanceProperty(): array
    {
        if ($this->isAppealBoardType() || !$this->selectedBoard) {
            return [
                'activeCircular' => null,
                'subjects' => collect(),
                'needed' => collect(),
                'excess' => collect(),
                'note' => '',
            ];
        }

        return app(TransferBoardSchoolBalanceService::class)->summary($this->selectedBoard);
    }

    public function getAvailablePoliciesProperty()
    {
        return TransferPolicy::active()
            ->orderByDesc('policy_year')
            ->orderBy('title')
            ->get();
    }

    public function getAvailableProvincialScopesProperty()
    {
        $scopeRelation = $this->boardScopeRelationName();

        return Workplaces::query()
            ->with(['officeLevel', $scopeRelation])
            ->where('office_level_id', $this->boardScopeOfficeLevelId())
            ->whereHas($scopeRelation, function ($query) {
                $query->active();
            })
            ->get()
            ->sortBy(function (Workplaces $workplace) {
                return $workplace->office_name;
            })
            ->values();
    }

    public function getAvailableCategoriesProperty()
    {
        if (!$this->createPolicyId) {
            return collect();
        }

        return $this->availableCategoryQuery($this->createPolicyId)
            ->orderBy('transfer_category_name')
            ->get();
    }

    public function getAvailableSubjectsProperty()
    {
        return SubjectList::active()
            ->orderBy('name_en')
            ->get();
    }

    public function getCreateBoardStepsProperty(): array
    {
        return [
            1 => [
                'title' => 'Board Setup',
                'description' => 'Choose the date, active policy, and board category.',
            ],
            2 => [
                'title' => 'Subjects',
                'description' => 'Select one or more subjects for the board.',
            ],
            3 => [
                'title' => 'Core Officers',
                'description' => 'Search and attach the chairman and secretary by NIC.',
            ],
            4 => [
                'title' => 'Members & Review',
                'description' => 'Add optional members and review before creating the board.',
            ],
        ];
    }

    public function getSelectedCreatePolicyProperty(): ?TransferPolicy
    {
        return $this->availablePolicies->firstWhere('policy_id', $this->createPolicyId);
    }

    public function getSelectedCreateCategoryProperty(): ?TransferCategory
    {
        return $this->selectedCreateCategoryForCurrentInput();
    }

    public function getSuggestedCreateBoardNameProperty(): string
    {
        return $this->generateSuggestedCreateBoardName();
    }

    protected function generateSuggestedCreateBoardName(): string
    {
        $workplace = $this->currentProvincialWorkplace();
        $category = $this->selectedCreateCategoryForCurrentInput();

        if (!$workplace || !$category || blank($this->createBoardDate)) {
            return '';
        }

        try {
            return $this->buildBoardName($workplace, $category, $this->createBoardDate);
        } catch (\Throwable) {
            return '';
        }
    }

    protected function selectedCreateCategoryForCurrentInput(): ?TransferCategory
    {
        if (!$this->createPolicyId || !$this->createTransferCategoryId) {
            return null;
        }

        return $this->availableCategoryQuery($this->createPolicyId)
            ->where('transfer_category_id', $this->createTransferCategoryId)
            ->first();
    }

    public function getSelectedCreateSubjectsProperty()
    {
        return $this->availableSubjects
            ->whereIn('subject_id', $this->selectedSubjectIds)
            ->values();
    }

    public function getCurrentWorkplaceProperty(): ?Workplaces
    {
        return $this->currentProvincialWorkplace();
    }

    public function openCreateBoardFlow(): void
    {
        if (!$this->currentProvincialWorkplace()) {
            $this->showCreateBoardFlow = false;
            session()->flash('error', 'Create ' . $this->boardNounTitle() . ' is available only for users with a current ' . $this->boardScopeAdjectiveLower() . ' appointment.');

            return;
        }

        $this->resetCreateBoardForm();
        $this->createBoardDate = $this->resolvedBoardDate();
        $this->refreshSuggestedBoardName(force: true);
        $this->showCreateBoardFlow = true;
    }

    public function startEditBoard(string $boardId): void
    {
        $board = $this->findScopedBoard($boardId, [
            'chairman.currentAppointment.workplace',
            'chairman.currentAppointment.officeLevel',
            'secretary.currentAppointment.workplace',
            'secretary.currentAppointment.officeLevel',
            'members.person.currentAppointment.workplace',
            'members.person.currentAppointment.officeLevel',
            'subjects',
        ]);

        if (!$this->ensureBoardIsEditable($board)) {
            return;
        }

        $this->resetCreateBoardForm();
        $this->editingBoardId = $board->board_id;
        $this->showCreateBoardFlow = true;
        $this->createBoardDate = $board->start_date?->toDateString() ?? now()->toDateString();
        $this->createBoardName = (string) $board->board_name;
        $this->createBoardNameWasEdited = filled($this->createBoardName);
        $this->createPolicyId = $board->policy_id;
        $this->createTransferCategoryId = $board->transfer_category_id;
        $this->selectedSubjectIds = $board->subjects->pluck('subject_id')->filter()->values()->all();

        $this->chairmanCandidate = $this->mapPersonCandidate($board->chairman);
        $this->chairmanPeopleId = $this->chairmanCandidate['people_id'] ?? null;
        $this->chairmanNic = $this->chairmanCandidate['nic'] ?? '';

        $this->secretaryCandidate = $this->mapPersonCandidate($board->secretary);
        $this->secretaryPeopleId = $this->secretaryCandidate['people_id'] ?? null;
        $this->secretaryNic = $this->secretaryCandidate['nic'] ?? '';

        $this->createMembers = $board->members
            ->reject(fn ($member) => in_array(strtolower((string) $member->role), ['chairman', 'secretary'], true))
            ->map(function ($member) {
                return $this->mapPersonCandidate($member->person);
            })
            ->filter(fn ($member) => filled($member['people_id'] ?? null))
            ->values()
            ->all();

        $this->memberNic = '';
        $this->memberPeopleId = null;
        $this->memberCandidate = [];
        $this->createBoardStep = 1;
    }

    public function closeCreateBoardFlow(): void
    {
        $this->showCreateBoardFlow = false;
        $this->resetCreateBoardForm();
    }

    public function nextCreateBoardStep(): void
    {
        if ($this->createBoardStep >= 4) {
            return;
        }

        $this->validateCreateBoardStep($this->createBoardStep);
        $this->createBoardStep++;
    }

    public function previousCreateBoardStep(): void
    {
        if ($this->createBoardStep <= 1) {
            return;
        }

        $this->createBoardStep--;
    }

    public function searchChairmanNic(): void
    {
        $candidate = $this->lookupPersonByNic($this->chairmanNic, 'chairmanNic');

        $this->chairmanCandidate = $candidate ?? [];
        $this->chairmanPeopleId = $candidate['people_id'] ?? null;
    }

    public function searchSecretaryNic(): void
    {
        $candidate = $this->lookupPersonByNic($this->secretaryNic, 'secretaryNic');

        $this->secretaryCandidate = $candidate ?? [];
        $this->secretaryPeopleId = $candidate['people_id'] ?? null;
    }

    public function searchMemberNic(): void
    {
        $candidate = $this->lookupPersonByNic($this->memberNic, 'memberNic');

        $this->memberCandidate = $candidate ?? [];
        $this->memberPeopleId = $candidate['people_id'] ?? null;
    }

    public function addCreateMember(): void
    {
        if (!$this->memberPeopleId || empty($this->memberCandidate)) {
            $this->addError('memberNic', 'Search and select a valid person before adding a board member.');

            return;
        }

        if (in_array($this->memberPeopleId, [$this->chairmanPeopleId, $this->secretaryPeopleId], true)) {
            $this->addError('memberNic', 'Chairman and secretary do not need to be added again as board members.');

            return;
        }

        if (collect($this->createMembers)->pluck('people_id')->contains($this->memberPeopleId)) {
            $this->addError('memberNic', 'This member is already in the board member list.');

            return;
        }

        $this->createMembers[] = $this->memberCandidate;
        $this->syncCreateMembers();

        $this->memberNic = '';
        $this->memberPeopleId = null;
        $this->memberCandidate = [];
        $this->resetErrorBag('memberNic');
    }

    public function removeCreateMember(string $peopleId): void
    {
        $this->createMembers = collect($this->createMembers)
            ->reject(fn ($member) => ($member['people_id'] ?? null) === $peopleId)
            ->values()
            ->all();
    }

    public function saveBoard(): void
    {
        if ($this->editingBoardId !== '') {
            $this->updateBoard();

            return;
        }

        $this->createBoard();
    }

    public function createBoard(): void
    {
        $this->validateCreateBoardStep(1);
        $this->validateCreateBoardStep(2);
        $this->validateCreateBoardStep(3);

        $validated = $this->validateCreateBoardData();
        $workplace = $this->currentProvincialWorkplace();

        if (!$workplace) {
            session()->flash('error', 'Only ' . $this->boardScopeAdjectiveLower() . ' users can create ' . $this->boardScopeAdjectiveLower() . ' boards.');

            return;
        }

        if ($this->chairmanPeopleId === $this->secretaryPeopleId) {
            $this->addError('secretaryNic', 'Chairman and secretary must be different people.');

            return;
        }

        $memberIds = collect($this->createMembers)->pluck('people_id');

        if ($memberIds->contains($this->chairmanPeopleId) || $memberIds->contains($this->secretaryPeopleId)) {
            $this->addError('memberNic', 'Remove chairman or secretary from the optional member list.');

            return;
        }

        $policy = TransferPolicy::active()
            ->where('policy_id', $validated['createPolicyId'])
            ->first();

        if (!$policy) {
            $this->addError('createPolicyId', 'Select an active transfer policy.');

            return;
        }

        $category = $this->availableCategoryQuery($validated['createPolicyId'])
            ->where('transfer_category_id', $validated['createTransferCategoryId'])
            ->first();

        if (!$category) {
            $this->addError('createTransferCategoryId', 'Select a valid transfer category for this ' . $this->boardScopeNameLower() . '.');

            return;
        }

        try {
            $board = DB::transaction(function () use ($validated, $workplace, $category) {
                $board = new TransferBoard([
                    'policy_id' => $validated['createPolicyId'],
                    'transfer_category_id' => $validated['createTransferCategoryId'],
                    'bo_office_level_id' => $workplace->office_level_id,
                    'bo_workplace_id' => $workplace->workplace_id,
                    'board_name' => $this->resolvedBoardName($workplace, $category, $validated),
                    'start_date' => $validated['createBoardDate'],
                    'end_date' => $validated['createBoardDate'],
                    'board_status' => TransferBoard::STATUS_ON_PROGRESS,
                    'chairman_id' => $this->chairmanPeopleId,
                    'secretary_id' => $this->secretaryPeopleId,
                ]);

                $this->syncBoardConfiguration($board, $validated);

                return $board;
            });
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'Unable to create the ' . $this->boardNoun() . '.';
            session()->flash('error', $message);

            return;
        }

        $this->showCreateBoardFlow = false;
        $this->boardDate = $validated['createBoardDate'];
        $this->selectedBoardId = $board->board_id;
        $this->resetCreateBoardForm();
        $this->resetPage('boardPage');
        $this->resetPage('applicationPage');

        session()->flash('success', $this->boardNounTitle() . ' created successfully.');
    }

    public function updateBoard(): void
    {
        $board = $this->findScopedBoard($this->editingBoardId);

        if (!$this->ensureBoardIsEditable($board)) {
            $this->showCreateBoardFlow = false;
            $this->resetCreateBoardForm();

            return;
        }

        $this->validateCreateBoardStep(1);
        $this->validateCreateBoardStep(2);
        $this->validateCreateBoardStep(3);

        $validated = $this->validateCreateBoardData();

        if ($this->chairmanPeopleId === $this->secretaryPeopleId) {
            $this->addError('secretaryNic', 'Chairman and secretary must be different people.');

            return;
        }

        $memberIds = collect($this->createMembers)->pluck('people_id');

        if ($memberIds->contains($this->chairmanPeopleId) || $memberIds->contains($this->secretaryPeopleId)) {
            $this->addError('memberNic', 'Remove chairman or secretary from the optional member list.');

            return;
        }

        try {
            DB::transaction(function () use ($board, $validated) {
                $this->syncBoardConfiguration($board, $validated);
            });
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'Unable to update the ' . $this->boardNoun() . '.';
            session()->flash('error', $message);

            return;
        }

        $this->showCreateBoardFlow = false;
        $this->boardDate = $validated['createBoardDate'];
        $this->selectedBoardId = $board->board_id;
        $this->resetCreateBoardForm();
        $this->resetPage('boardPage');
        $this->resetPage('applicationPage');

        session()->flash('success', $this->boardNounTitle() . ' updated successfully.');
    }

    public function openBoard(string $boardId): void
    {
        $board = $this->findScopedBoard($boardId);

        if (!$board) {
            session()->flash('error', 'The selected ' . $this->boardNoun() . ' is not available in your current scope.');

            return;
        }

        $this->selectedBoardId = $board->board_id;
        $this->showCreateBoardFlow = false;
        $this->applicationSearch = '';
        $this->applicationStatus = '';
        $this->resetPage('applicationPage');
    }

    public function backToBoardList(): void
    {
        $this->selectedBoardId = '';
        $this->applicationSearch = '';
        $this->applicationStatus = '';
        $this->resetPage('applicationPage');
    }

    public function openManageMembersModal(string $boardId): void
    {
        $board = $this->findScopedBoard($boardId);

        if (!$this->ensureBoardIsEditable($board)) {

            return;
        }

        $this->resetManageMembersState();
        $this->manageMembersBoardId = $board->board_id;
        $this->showManageMembersModal = true;
    }

    public function closeManageMembersModal(): void
    {
        $this->showManageMembersModal = false;
        $this->resetManageMembersState();
    }

    public function searchManageMemberNic(): void
    {
        $candidate = $this->lookupPersonByNic($this->manageMemberNic, 'manageMemberNic');

        $this->manageMemberCandidate = $candidate ?? [];
        $this->manageMemberPeopleId = $candidate['people_id'] ?? null;
    }

    public function addManagedMember(): void
    {
        $board = $this->managedBoard;

        if (!$this->ensureBoardIsEditable($board, 'Select a valid board before managing members.')) {

            return;
        }

        if (!$this->manageMemberPeopleId || empty($this->manageMemberCandidate)) {
            $this->addError('manageMemberNic', 'Search and select a valid person before adding a board member.');

            return;
        }

        if (in_array($this->manageMemberPeopleId, [$board->chairman_id, $board->secretary_id], true)) {
            $this->addError('manageMemberNic', 'Chairman and secretary are already part of the board.');

            return;
        }

        try {
            TransferBoardMembers::create([
                'board_id' => $board->board_id,
                'people_id' => $this->manageMemberPeopleId,
                'role' => 'Member',
                'active_status' => true,
            ]);
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'Unable to add the board member.';
            $this->addError('manageMemberNic', $message);

            return;
        }

        $this->manageMemberNic = '';
        $this->manageMemberPeopleId = null;
        $this->manageMemberCandidate = [];
        $this->resetErrorBag('manageMemberNic');

        session()->flash('success', 'Board member added successfully.');
    }

    public function removeManagedMember(int $memberId): void
    {
        $board = $this->managedBoard;

        if (!$this->ensureBoardIsEditable($board, 'Select a valid board before removing a member.')) {

            return;
        }

        $member = $board->members()
            ->where('id', $memberId)
            ->first();

        if (!$member) {
            session()->flash('error', 'Board member not found.');

            return;
        }

        if (strtolower($member->role) !== 'member') {
            session()->flash('error', 'Chairman and secretary are managed from the board configuration.');

            return;
        }

        $member->delete();

        session()->flash('success', 'Board member removed successfully.');
    }

    public function openAttendanceModal(string $boardId, bool $readOnly = false): void
    {
        $board = $this->findScopedBoard($boardId);

        if (!$board) {
            session()->flash('error', 'The selected ' . $this->boardNoun() . ' is not available in your current scope.');

            return;
        }

        if (!$readOnly && !$this->ensureBoardIsEditable($board)) {
            return;
        }

        if (!$readOnly) {
            $this->syncCoreBoardMembersForAttendance($board);
        }

        $this->resetAttendanceState();
        $this->attendanceBoardId = $board->board_id;
        $this->attendanceReadOnly = $readOnly;
        $this->attendanceDate = now()->toDateString();
        $this->loadAttendanceRows();
        $this->showAttendanceModal = true;
    }

    public function closeAttendanceModal(): void
    {
        $this->showAttendanceModal = false;
        $this->resetAttendanceState();
    }

    public function searchAttendanceMemberNic(): void
    {
        $candidate = $this->lookupPersonByNic($this->attendanceMemberNic, 'attendanceMemberNic');

        $this->attendanceMemberCandidate = $candidate ?? [];
        $this->attendanceMemberPeopleId = $candidate['people_id'] ?? null;
    }

    public function addAttendanceMember(): void
    {
        if ($this->attendanceReadOnly) {
            session()->flash('error', 'Open Mark Attendance to add board members.');

            return;
        }

        $board = $this->attendanceBoard;

        if (!$this->ensureBoardIsEditable($board, 'Select a valid board before adding an attendance member.')) {
            return;
        }

        if (!$this->attendanceMemberPeopleId || empty($this->attendanceMemberCandidate)) {
            $this->addError('attendanceMemberNic', 'Search and select a valid person before adding a board member.');

            return;
        }

        if (in_array($this->attendanceMemberPeopleId, [$board->chairman_id, $board->secretary_id], true)) {
            $this->addError('attendanceMemberNic', 'Chairman and secretary are already part of the board.');

            return;
        }

        try {
            TransferBoardMembers::create([
                'board_id' => $board->board_id,
                'people_id' => $this->attendanceMemberPeopleId,
                'role' => 'Member',
                'active_status' => true,
            ]);
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first() ?? 'Unable to add the board member.';
            $this->addError('attendanceMemberNic', $message);

            return;
        }

        $this->attendanceMemberNic = '';
        $this->attendanceMemberPeopleId = null;
        $this->attendanceMemberCandidate = [];
        $this->resetErrorBag('attendanceMemberNic');
        $this->loadAttendanceRows();

        session()->flash('success', 'Board member added. You can now mark attendance for this member.');
    }

    public function saveAttendance(): void
    {
        if ($this->attendanceReadOnly) {
            session()->flash('error', 'Attendance is open in view-only mode.');

            return;
        }

        $board = $this->attendanceBoard;

        if (!$this->ensureBoardIsEditable($board, 'Select a valid board before marking attendance.')) {
            return;
        }

        $validated = $this->validate([
            'attendanceDate' => ['required', 'date'],
            'attendanceRows' => ['required', 'array', 'min:1'],
            'attendanceRows.*.tbm_id' => ['required', 'exists:transfer_board_members,tbm_id'],
            'attendanceRows.*.attendance_date' => ['required', 'date'],
            'attendanceRows.*.status' => ['required', 'in:present,absent,late'],
            'attendanceRows.*.remarks' => ['nullable', 'string', 'max:500'],
        ], [
            'attendanceRows.required' => 'Add at least one board member before marking attendance.',
            'attendanceRows.min' => 'Add at least one board member before marking attendance.',
        ]);

        $validTbmIds = $board->members()
            ->where('active_status', true)
            ->pluck('tbm_id')
            ->filter()
            ->all();

        $rows = collect($validated['attendanceRows']);

        if ($rows->contains(fn (array $row) => !in_array($row['tbm_id'], $validTbmIds, true))) {
            $this->addError('attendanceRows', 'Attendance can only be marked for members of the selected board.');

            return;
        }

        DB::transaction(function () use ($rows) {
            $rows->each(function (array $row) {
                TransferBoardMemberAttendances::updateOrCreate(
                    [
                        'tbm_id' => $row['tbm_id'],
                        'attendance_date' => Carbon::parse($row['attendance_date'])->toDateString(),
                    ],
                    [
                        'attendance_status' => $row['status'],
                        'remarks' => filled($row['remarks'] ?? null) ? trim($row['remarks']) : null,
                        'active_status' => true,
                    ]
                );
            });
        });

        $this->attendanceRows = collect($this->attendanceRows)
            ->mapWithKeys(function (array $currentRow, int|string $key) use ($rows) {
                $validatedRow = $rows->get($key);

                if ($validatedRow) {
                    $currentRow['attendance_date'] = Carbon::parse($validatedRow['attendance_date'])->toDateString();
                    $currentRow['status'] = $validatedRow['status'];
                    $currentRow['remarks'] = $validatedRow['remarks'] ?? '';
                    $currentRow['has_record'] = true;
                }

                return [$key => $currentRow];
            })
            ->all();

        session()->flash('success', 'Attendance saved successfully.');
    }

    public function closeBoard(string $boardId): void
    {
        $board = $this->findScopedBoard($boardId);

        if (!$this->ensureBoardIsEditable($board)) {
            return;
        }

        $board->update([
            'board_status' => TransferBoard::STATUS_CLOSED,
        ]);

        if ($this->manageMembersBoardId === $board->board_id) {
            $this->closeManageMembersModal();
        }

        if ($this->attendanceBoardId === $board->board_id && !$this->attendanceReadOnly) {
            $this->closeAttendanceModal();
        }

        if ($this->editingBoardId === $board->board_id) {
            $this->closeCreateBoardFlow();
        }

        session()->flash('success', $this->boardNounTitle() . ' closed successfully. Closed boards can only be viewed.');
    }

    public function boardStatusBadge(?string $status): array
    {
        return match ($status) {
            TransferBoard::STATUS_CLOSED => ['color' => 'zinc', 'label' => 'Closed'],
            default => ['color' => 'blue', 'label' => 'On Progress'],
        };
    }

    public function statusBadge(string $status): array
    {
        return match ($status) {
            'pending' => ['color' => 'amber', 'label' => 'Pending'],
            'submitted' => ['color' => 'blue', 'label' => 'Submitted'],
            'processing' => ['color' => 'amber', 'label' => 'Processing'],
            'approved' => ['color' => 'green', 'label' => 'Approved'],
            'rejected' => ['color' => 'rose', 'label' => 'Rejected'],
            default => ['color' => 'zinc', 'label' => ucfirst($status)],
        };
    }

    public function render()
    {
        return view('livewire.transfer-module.transfer-board.province-teacher-transfer-board', [
            'boards' => $this->boards,
            'selectedBoard' => $this->selectedBoard,
            'showCreateBoardFlow' => $this->showCreateBoardFlow,
            'applications' => $this->selectedBoard ? $this->applications : null,
            'managedBoard' => $this->managedBoard,
            'attendanceBoard' => $this->attendanceBoard,
            'boardSchoolBalance' => $this->boardSchoolBalance,
            'isSuperAdmin' => $this->isSuperAdmin(),
            'availablePolicies' => $this->availablePolicies,
            'availableProvincialScopes' => $this->availableProvincialScopes,
            'availableCategories' => $this->availableCategories,
            'availableSubjects' => $this->availableSubjects,
            'createBoardSteps' => $this->createBoardSteps,
            'selectedCreatePolicy' => $this->selectedCreatePolicy,
            'selectedCreateCategory' => $this->selectedCreateCategory,
            'selectedCreateSubjects' => $this->selectedCreateSubjects,
            'suggestedCreateBoardName' => $this->suggestedCreateBoardName,
            'currentWorkplace' => $this->currentWorkplace,
            'isAppealBoard' => $this->isAppealBoard,
            'boardPageTitle' => $this->boardPageTitle(),
            'boardDecisionReportRoute' => $this->boardDecisionReportRoute(),
            'boardRouteScope' => $this->boardRouteScope(),
            'boardScopeTitle' => $this->boardScopeTitle(),
            'boardScopeNameLower' => $this->boardScopeNameLower(),
            'boardScopeNamePlural' => $this->boardScopeNamePlural(),
            'boardScopeAdjectiveLower' => $this->boardScopeAdjectiveLower(),
        ]);
    }
}
