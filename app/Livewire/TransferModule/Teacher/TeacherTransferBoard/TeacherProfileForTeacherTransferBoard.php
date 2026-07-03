<?php

namespace App\Livewire\TransferModule\Teacher\TeacherTransferBoard;

use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\EmployerCadreSubject;
use App\Models\Institution;
use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferApplicationAchievement;
use App\Models\TeacherTransferApplicationPreferences;
use App\Models\TeacherTransferBoardRecommendation;
use App\Models\TeacherTransferBoardRecommendationList;
use App\Models\TeacherTransferBoard;
use App\Models\Workplaces;
use App\Models\ZonalEducationOffice;
use App\Services\TransferModule\TransferApplicationScoreService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class TeacherProfileForTeacherTransferBoard extends Component
{
    public $application;
    public string $board = 'province';
    public string $selectedBoardId = '';
    public bool $showDecisionModal = false;
    public bool $showPreferenceDistanceMap = false;
    public bool $showScoreDetails = false;
    public array $scoreAchievementRemarks = [];
    public string $decisionOutcome = 'approved';
    public string $decisionPreferenceInstitution = '';
    public string $decisionSchoolSelectionType = 'preferred';
    public string $decisionOtherZoneId = '';
    public string $decisionOtherSchoolId = '';
    public ?string $decisionEffectiveDate = null;
    public string $decisionNote = '';
    public string $decisionRejectionReason = '';
    public array $preferenceDistanceMap = [];
    public array $recommendedNeededSchools = [];
    public string $recommendedNeededSchoolsNote = '';

    public function mount($id)
    {
        $this->board = $this->normalizeBoard(request()->query('board'));
        $this->selectedBoardId = (string) request()->query('selectedBoardId', '');

        $this->application = TeacherTransferApplication::with($this->applicationRelations())
            ->where(function ($query) use ($id) {
                $query->where('transfer_application_id', $id)
                    ->orWhere('id', $id);
            })
            ->firstOrFail();

        $this->refreshPreferenceDistanceMap();
        $this->refreshRecommendedNeededSchools();
        $this->syncScoreAchievementRemarks();
    }

    protected function applicationRelations(): array
    {
        return [
            'policy.steps.officeLevel',
            'employee.title',
            'employee.gender',
            'reason',
            'targetProvince',
            'appointment.service',
            'appointment.rank',
            'appointment.position',
            'currentWorkplace.officeLevel',
            'currentWorkplace.institution.authority',
            'currentWorkplace.institution.zonalEducationOffice.provincialEducationOffice',
            'currentWorkplace.institution.divisionalEducationOffice',
            'category.officeLevel',
            'category.transferSubCategory',
            'transferSubCategory',
            'teacher.teacherCategory',
            'teacher.teacherType',
            'teacher.medium',
            'teacher.appointmentSubject',
            'teacher.mainSubject',
            'teacher.secondarySubject',
            'teacher.currentTeachingSubject',
            'preferences.zonalOffice.zonal.provincialEducationOffice',
            'preferences.institution.institution.authority',
            'preferences.institution.institution.institutionCategory',
            'preferences.institution.institution.institutionType',
            'preferences.institution.institution.zonalEducationOffice',
            'preferences.institution.institution.divisionalEducationOffice',
            'achievements',
            'recommendations.workplace.officeLevel',
            'recommendations.recommendation',
            'recommendations.approver',
            'boardRecommendation.recommendationList',
            'boardRecommendation.board',
            'boardRecommendation.selectedZone',
            'boardRecommendation.selectedSchool',
            'boardRecommendation.creator',
            'boardRecommendation.updater',
            'boardRecommendations.recommendationList',
            'boardRecommendations.board',
            'boardRecommendations.selectedZone',
            'boardRecommendations.selectedSchool',
            'boardRecommendations.creator',
            'boardRecommendations.updater',
        ];
    }

    public function prepareDecisionDraft(): void
    {
        $board = $this->decisionBoard();

        if (!$board) {
            session()->flash('error', 'Open a transfer board before making a decision.');

            return;
        }

        if ($this->isReadOnlyObserver()) {
            session()->flash('error', $this->readOnlyObserverMessage());

            return;
        }

        if ($board->isClosed()) {
            session()->flash('error', 'This transfer board is closed. Decisions can only be viewed.');

            return;
        }

        $decision = $this->currentBoardDecision($board);

        $this->decisionOutcome = $decision?->recommendation_status === 'rejected' ? 'rejected' : 'approved';
        $this->decisionPreferenceInstitution = '';
        $this->decisionSchoolSelectionType = $decision?->school_selection_type ?: ($this->application->preferences->isEmpty() ? 'other' : 'preferred');
        $this->decisionOtherZoneId = '';
        $this->decisionOtherSchoolId = '';
        $this->decisionEffectiveDate = $decision?->transfer_effective_date?->toDateString();
        $this->decisionNote = (string) ($decision?->recommendation_remarks ?? '');
        $this->decisionRejectionReason = (string) ($decision?->rejection_reason ?? '');

        if ($decision?->recommendation_status === 'approved') {
            if ($decision->school_selection_type === 'other') {
                $this->decisionOtherZoneId = (string) $decision->selected_zone_id;
                $this->decisionOtherSchoolId = (string) $decision->selected_school_id;
            } else {
                $this->decisionSchoolSelectionType = 'preferred';
                $this->decisionPreferenceInstitution = (string) $decision->selected_school_id;
            }
        }

        $this->setDefaultDecisionZoneForZonalBoard();
        $this->showDecisionModal = true;
    }

    public function closeDecisionModal(): void
    {
        $this->showDecisionModal = false;
    }

    public function updatedDecisionOutcome(string $value): void
    {
        if ($value === 'approved') {
            $this->decisionRejectionReason = '';
            $this->decisionSchoolSelectionType = $this->decisionSchoolSelectionType ?: ($this->application->preferences->isEmpty() ? 'other' : 'preferred');
            $this->setDefaultDecisionZoneForZonalBoard();

            return;
        }

        $this->decisionPreferenceInstitution = '';
        $this->decisionOtherZoneId = '';
        $this->decisionOtherSchoolId = '';
        $this->decisionEffectiveDate = null;
    }

    public function updatedDecisionSchoolSelectionType(string $value): void
    {
        if ($value === 'preferred') {
            $this->decisionOtherZoneId = '';
            $this->decisionOtherSchoolId = '';

            return;
        }

        $this->decisionPreferenceInstitution = '';
        $this->setDefaultDecisionZoneForZonalBoard();
    }

    public function updatedDecisionOtherZoneId(): void
    {
        $this->decisionOtherSchoolId = '';
    }

    public function submitDecision()
    {
        $board = $this->decisionBoard();

        if (!$board) {
            session()->flash('error', 'Open a transfer board before making a decision.');

            return;
        }

        if ($this->isReadOnlyObserver()) {
            session()->flash('error', $this->readOnlyObserverMessage());

            return;
        }

        if ($board->isClosed()) {
            session()->flash('error', 'This transfer board is closed. Decisions can only be viewed.');

            return;
        }

        $rules = [
            'decisionOutcome' => ['required', 'in:approved,rejected'],
            'decisionNote' => ['nullable', 'string', 'max:2000'],
        ];

        if ($this->decisionOutcome === 'approved') {
            $rules['decisionSchoolSelectionType'] = ['required', 'in:preferred,other'];
            $rules['decisionEffectiveDate'] = ['required', 'date'];

            if ($this->decisionSchoolSelectionType === 'preferred') {
                $rules['decisionPreferenceInstitution'] = ['required', 'string', 'max:10'];
            } else {
                $rules['decisionOtherZoneId'] = ['required', 'string', 'max:10'];
                $rules['decisionOtherSchoolId'] = ['required', 'string', 'max:10'];
            }
        } else {
            $rules['decisionRejectionReason'] = ['required', 'string', 'max:80'];
        }

        $this->validate($rules, [
            'decisionPreferenceInstitution.required' => 'Select a preferred school or choose other school.',
            'decisionOtherZoneId.required' => 'Select the zone for the other school.',
            'decisionOtherSchoolId.required' => 'Select the other school.',
            'decisionEffectiveDate.required' => 'Select the transfer effective date.',
            'decisionRejectionReason.required' => 'Select a rejection reason.',
        ]);

        [$schoolSelectionType, $selectedZoneId, $selectedSchoolId] = $this->resolveDecisionSchoolSelection();
        $status = $this->decisionOutcome === 'rejected' ? 'rejected' : 'approved';
        $decisionListId = $this->resolveDecisionListId($status);

        try {
            DB::transaction(function () use ($board, $decisionListId, $schoolSelectionType, $selectedZoneId, $selectedSchoolId, $status) {
                TeacherTransferBoardRecommendation::updateOrCreate(
                    [
                        'transfer_application_id' => $this->application->transfer_application_id,
                        'transfer_board_id' => $board?->board_id,
                    ],
                    [
                        'ttbr_list_id' => $decisionListId,
                        'recommendation_remarks' => $this->decisionNote,
                        'recommendation_status' => $status,
                        'school_selection_type' => $schoolSelectionType,
                        'selected_zone_id' => $selectedZoneId,
                        'selected_school_id' => $selectedSchoolId,
                        'transfer_effective_date' => $status === 'approved' ? $this->decisionEffectiveDate : null,
                        'rejection_reason' => $status === 'rejected' ? $this->decisionRejectionReason : null,
                        'active_status' => true,
                    ]
                );

                $this->application->update([
                    'status' => $status,
                ]);
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            session()->flash('error', 'Unable to save the transfer board decision right now.');

            return;
        }

        $this->application->refresh();
        $this->application->load($this->applicationRelations());
        $this->refreshPreferenceDistanceMap();
        $this->showDecisionModal = false;

        session()->flash('success', 'Transfer board decision saved successfully.');

        return $this->redirect($this->backRoute, navigate: true);
    }

    protected function resolveDecisionSchoolSelection(): array
    {
        if ($this->decisionOutcome !== 'approved') {
            return [null, null, null];
        }

        if ($this->decisionSchoolSelectionType === 'preferred') {
            $preference = $this->application->preferences
                ->firstWhere('ins_wp_id', $this->decisionPreferenceInstitution);

            if (!$preference) {
                throw ValidationException::withMessages([
                    'decisionPreferenceInstitution' => 'Select a valid preferred school from this application.',
                ]);
            }

            return ['preferred', $preference->zeo_wp_id, $preference->ins_wp_id];
        }

        $zone = $this->decisionZones
            ->firstWhere('workplace_id', $this->decisionOtherZoneId);

        if (!$zone) {
            throw ValidationException::withMessages([
                'decisionOtherZoneId' => 'Select a valid zone within this board scope.',
            ]);
        }

        $school = Institution::active()
            ->where('zeo_wp_id', $zone->workplace_id)
            ->where('workplace_id', $this->decisionOtherSchoolId)
            ->first();

        if (!$school) {
            throw ValidationException::withMessages([
                'decisionOtherSchoolId' => 'Select a valid school under the selected zone.',
            ]);
        }

        return ['other', $zone->workplace_id, $school->workplace_id];
    }

    protected function resolveDecisionListId(string $status): ?string
    {
        $options = TeacherTransferBoardRecommendationList::query()
            ->where('active_status', true)
            ->orderBy('id')
            ->get();

        $preferredTerms = $status === 'approved'
            ? ['approved', 'recommended']
            : ['rejected', 'not recommended', 'reject'];

        foreach ($preferredTerms as $term) {
            $match = $options->first(function (TeacherTransferBoardRecommendationList $option) use ($term) {
                return Str::contains(Str::lower($option->decision), $term);
            });

            if ($match) {
                return $match->ttbr_list_id;
            }
        }

        return null;
    }

    protected function normalizeBoard(?string $board): string
    {
        return in_array($board, ['province', 'zone', 'national', 'pmoe'], true)
            ? $board
            : 'province';
    }

    public function getBackRouteProperty(): string
    {
        return match ($this->board) {
            'zone' => route('transfer-board.zone-teacher-transfer'),
            'pmoe' => route('transfer-board.provincial-ministry-teacher-transfer', array_filter([
                'selectedBoardId' => $this->selectedBoardId,
            ])),
            'national' => route('transfer-board.national-teacher-transfer'),
            default => route('transfer-board.province-teacher-transfer', array_filter([
                'selectedBoardId' => $this->selectedBoardId,
            ])),
        };
    }

    public function getBackLabelProperty(): string
    {
        return match ($this->board) {
            'zone' => 'Back to Zonal Board',
            'pmoe' => 'Back to Provincial Ministry Board',
            'national' => 'Back to National Board',
            default => 'Back to Provincial Board',
        };
    }

    public function getSelectedTeacherTransferBoardProperty(): ?TeacherTransferBoard
    {
        if ($this->selectedBoardId === '') {
            return null;
        }

        $query = TeacherTransferBoard::with(['policy', 'category', 'subjects', 'workplace'])
            ->transfer()
            ->where('board_id', $this->selectedBoardId);

        if ($this->board === 'zone') {
            $query->where('bo_office_level_id', 'OLID004');
        } elseif ($this->board === 'pmoe') {
            $query->where('bo_office_level_id', 'OLID002');
        } elseif ($this->board === 'province') {
            $query->where('bo_office_level_id', 'OLID003');
        }

        $board = $query->first();

        if ($this->board !== 'zone' || !$board) {
            return $board;
        }

        $currentWorkplace = $this->currentUserWorkplace();

        if ($currentWorkplace?->office_level_id === 'OLID004') {
            return $board;
        }

        return $this->canProvinceObserveSelectedZoneBoard($board) ? $board : null;
    }

    protected function currentUserWorkplace(): ?Workplaces
    {
        $userWorkplaceId = auth()->user()?->workplace_id;

        if (!$userWorkplaceId) {
            return null;
        }

        return Workplaces::find($userWorkplaceId);
    }

    protected function canProvinceObserveSelectedZoneBoard(?TeacherTransferBoard $board): bool
    {
        if (!$board || $board->bo_office_level_id !== 'OLID004') {
            return false;
        }

        $currentWorkplace = $this->currentUserWorkplace();

        if (!$currentWorkplace || $currentWorkplace->office_level_id !== 'OLID003') {
            return false;
        }

        $boardWorkplace = $board->workplace ?: Workplaces::find($board->bo_workplace_id);

        if (!$boardWorkplace) {
            return false;
        }

        $parentWorkplaceIds = collect($boardWorkplace->getAllParentWorkplaces())
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->all();

        return in_array((string) $currentWorkplace->workplace_id, $parentWorkplaceIds, true);
    }

    protected function isReadOnlyObserver(): bool
    {
        return $this->board === 'zone'
            && $this->canProvinceObserveSelectedZoneBoard($this->selectedTeacherTransferBoard);
    }

    protected function readOnlyObserverMessage(): string
    {
        return 'This zonal transfer board can only be viewed in read-only mode from the provincial level.';
    }

    public function getIsReadOnlyObserverProperty(): bool
    {
        return $this->isReadOnlyObserver();
    }

    protected function decisionBoard(): ?TeacherTransferBoard
    {
        return $this->selectedTeacherTransferBoard
            ?: $this->currentBoardDecision()?->board;
    }

    protected function currentBoardDecision(?TeacherTransferBoard $board = null): ?TeacherTransferBoardRecommendation
    {
        $board ??= $this->selectedTeacherTransferBoard;

        if ($board) {
            $boardSpecificDecision = $this->application->boardRecommendations
                ->firstWhere('transfer_board_id', $board->board_id);

            if ($boardSpecificDecision) {
                return $boardSpecificDecision;
            }
        }

        return $this->application->boardRecommendation;
    }

    protected function isZonalDecisionBoard(?TeacherTransferBoard $board = null): bool
    {
        $board ??= $this->decisionBoard();

        return $this->board === 'zone'
            || $board?->bo_office_level_id === 'OLID004';
    }

    protected function setDefaultDecisionZoneForZonalBoard(): void
    {
        $board = $this->decisionBoard();

        if (
            $this->decisionOutcome === 'approved'
            && $this->decisionSchoolSelectionType === 'other'
            && $this->isZonalDecisionBoard($board)
            && filled($board?->bo_workplace_id)
            && blank($this->decisionOtherZoneId)
        ) {
            $this->decisionOtherZoneId = (string) $board->bo_workplace_id;
        }
    }

    public function getDecisionZonesProperty(): Collection
    {
        $board = $this->decisionBoard();

        if ($this->isZonalDecisionBoard($board)) {
            $zoneWorkplaceId = (string) $board?->bo_workplace_id;

            if ($zoneWorkplaceId === '') {
                return collect();
            }

            return ZonalEducationOffice::active()
                ->where('workplace_id', $zoneWorkplaceId)
                ->orderBy('name')
                ->get();
        }

        if (($board?->bo_office_level_id ?? null) === 'OLID002') {
            $zoneIds = collect($board?->workplace?->getAllChildWorkplaces() ?? [])
                ->filter()
                ->values()
                ->all();

            if (empty($zoneIds)) {
                return collect();
            }

            return ZonalEducationOffice::active()
                ->whereIn('workplace_id', $zoneIds)
                ->orderBy('name')
                ->get();
        }

        $provinceWorkplaceId = $board?->bo_workplace_id ?: $this->application->target_province;

        if (!$provinceWorkplaceId) {
            return collect();
        }

        return ZonalEducationOffice::active()
            ->where('peo_wp_id', $provinceWorkplaceId)
            ->orderBy('name')
            ->get();
    }

    public function getDecisionSchoolsProperty(): Collection
    {
        if (!$this->decisionOtherZoneId) {
            return collect();
        }

        return Institution::active()
            ->where('zeo_wp_id', $this->decisionOtherZoneId)
            ->orderBy('name')
            ->get();
    }

    public function getSubmittedRecommendationsProperty(): Collection
    {
        return $this->application->recommendations
            ->filter(function ($recommendation) {
                return filled($recommendation->transfer_recommendation_list_id)
                    || filled($recommendation->approved_by)
                    || filled($recommendation->remarks);
            })
            ->sortBy('created_at')
            ->values();
    }

    public function timelineDecisionForStep($step): ?array
    {
        $officeLevelId = (string) ($step->office_level_id ?? '');

        if ($officeLevelId === '') {
            return null;
        }

        $boardDecision = $this->application->boardRecommendations
            ->filter(fn (TeacherTransferBoardRecommendation $decision) => $decision->active_status !== false)
            ->sortByDesc(fn (TeacherTransferBoardRecommendation $decision) => $decision->updated_at?->timestamp ?? $decision->created_at?->timestamp ?? 0)
            ->first(fn (TeacherTransferBoardRecommendation $decision) => $decision->board?->bo_office_level_id === $officeLevelId);

        if ($boardDecision) {
            return $this->formatBoardTimelineDecision($boardDecision);
        }

        $workflowDecision = $this->submittedRecommendations
            ->first(fn ($recommendation) => $recommendation->workplace?->office_level_id === $officeLevelId);

        if ($workflowDecision) {
            return $this->formatWorkflowTimelineDecision($workflowDecision);
        }

        return null;
    }

    protected function formatBoardTimelineDecision(TeacherTransferBoardRecommendation $decision): array
    {
        $status = Str::lower((string) $decision->recommendation_status);
        $listLabel = trim((string) ($decision->recommendationList?->decision ?? ''));

        $label = match ($status) {
            'approved' => __('Board decision: Approved'),
            'rejected' => __('Board decision: Rejected'),
            default => $listLabel !== '' ? $listLabel : __('Board decision recorded'),
        };

        $remarks = trim((string) ($decision->recommendation_remarks ?? ''));

        if ($remarks === '' && filled($decision->rejection_reason)) {
            $remarks = __('Reason: :reason', ['reason' => $decision->rejection_reason]);
        }

        if ($remarks === '') {
            $remarks = __('No remarks captured for this board decision.');
        }

        $schoolName = $decision->selectedSchool?->name;

        if ($status === 'approved' && filled($schoolName)) {
            $remarks = trim($remarks . ' ' . __('Approved school: :school', ['school' => $schoolName]));
        }

        return [
            'label' => $label,
            'remarks' => $remarks,
            'official' => $decision->updater?->name_with_initials
                ?? $decision->creator?->name_with_initials
                ?? __('Board official not recorded'),
            'date' => $decision->updated_at ?? $decision->created_at,
            'color' => $status === 'rejected' ? 'rose' : ($status === 'approved' ? 'green' : 'blue'),
            'source' => __('Board decision'),
        ];
    }

    protected function formatWorkflowTimelineDecision($decision): array
    {
        $label = $decision->recommendation?->decision ?? __('Completed');

        return [
            'label' => $label,
            'remarks' => $decision->remarks ?: __('No remarks captured for this step.'),
            'official' => $decision->approver?->name_with_initials ?? __('Official not recorded'),
            'date' => $decision->updated_at ?? $decision->created_at,
            'color' => ($decision->recommendation?->rejectsApplication() ?? false) ? 'rose' : 'green',
            'source' => __('Workflow recommendation'),
        ];
    }

    public function togglePreferenceDistanceMap(): void
    {
        $this->showPreferenceDistanceMap = !$this->showPreferenceDistanceMap;
    }

    public function toggleScoreDetails(): void
    {
        $this->showScoreDetails = !$this->showScoreDetails;
    }

    public function toggleAchievementInclusion(int $achievementId): void
    {
        if ($this->isReadOnlyObserver()) {
            session()->flash('error', $this->readOnlyObserverMessage());

            return;
        }

        $achievement = $this->application->achievements()
            ->whereKey($achievementId)
            ->first();

        if (!$achievement) {
            session()->flash('error', __('Achievement record not found.'));

            return;
        }

        $achievement->update([
            'is_included' => !$achievement->is_included,
            'reviewed_by' => auth()->user()?->people_id,
            'reviewed_at' => now(),
        ]);

        $this->application->load($this->applicationRelations());
        $this->syncScoreAchievementRemarks();
        session()->flash('success', __('Achievement score inclusion updated.'));
    }

    public function saveAchievementRemark(int $achievementId): void
    {
        if ($this->isReadOnlyObserver()) {
            session()->flash('error', $this->readOnlyObserverMessage());

            return;
        }

        $achievement = $this->application->achievements()
            ->whereKey($achievementId)
            ->first();

        if (!$achievement) {
            session()->flash('error', __('Achievement record not found.'));

            return;
        }

        $achievement->update([
            'review_remarks' => filled($this->scoreAchievementRemarks[$achievementId] ?? null)
                ? trim((string) $this->scoreAchievementRemarks[$achievementId])
                : null,
            'reviewed_by' => auth()->user()?->people_id,
            'reviewed_at' => now(),
        ]);

        $this->application->load($this->applicationRelations());
        $this->syncScoreAchievementRemarks();
        session()->flash('success', __('Achievement review remark saved.'));
    }

    public function getTransferScoreProperty(): array
    {
        return app(TransferApplicationScoreService::class)->score($this->application);
    }

    protected function syncScoreAchievementRemarks(): void
    {
        $this->scoreAchievementRemarks = $this->application->achievements
            ->mapWithKeys(fn (TeacherTransferApplicationAchievement $achievement) => [
                $achievement->id => (string) ($achievement->review_remarks ?? ''),
            ])
            ->all();
    }

    public function getCurrentStepProperty()
    {
        return $this->application->policy?->steps
            ?->firstWhere('step_order', $this->application->current_step);
    }

    public function statusBadge(string $status): array
    {
        return match ($status) {
            'draft' => ['color' => 'zinc', 'label' => 'Draft'],
            'submitted' => ['color' => 'blue', 'label' => 'Submitted'],
            'processing' => ['color' => 'amber', 'label' => 'Processing'],
            'approved' => ['color' => 'green', 'label' => 'Approved'],
            'rejected' => ['color' => 'rose', 'label' => 'Not Recomended'],
            default => ['color' => 'zinc', 'label' => ucfirst($status)],
        };
    }

    protected function refreshPreferenceDistanceMap(): void
    {
        $origin = $this->buildPreferenceMapOrigin();
        $schools = $this->application->preferences
            ->map(fn ($preference) => $this->buildPreferenceMapSchool($preference, $origin))
            ->filter()
            ->values()
            ->all();

        $this->preferenceDistanceMap = [
            'origin' => $origin,
            'schools' => $schools,
            'saved_origin' => filled($origin['lat']) && filled($origin['lng']),
            'has_schools' => !empty($schools),
        ];
    }

    protected function refreshRecommendedNeededSchools(): void
    {
        $this->recommendedNeededSchoolsNote = '';

        $this->recommendedNeededSchools = $this->buildRecommendedNeededSchools()
            ->values()
            ->all();
    }

    protected function buildRecommendedNeededSchools(): Collection
    {
        $board = $this->decisionBoard();
        $teacher = $this->application->teacher;
        $teacherSubjectId = (string) ($teacher?->main_subject ?? '');
        $teacherMediumId = (string) ($teacher?->appointment_medium ?? '');

        if (!$board || $teacherSubjectId === '') {
            $this->recommendedNeededSchoolsNote = __('A selected board and teacher main subject are required before recommended needed schools can be calculated.');

            return collect();
        }

        $boardSubjectIds = $board->subjects
            ->pluck('subject_id')
            ->filter()
            ->values();

        if ($boardSubjectIds->isNotEmpty() && !$boardSubjectIds->contains($teacherSubjectId)) {
            $this->recommendedNeededSchoolsNote = __('This teacher main subject is not linked to the selected board subjects.');

            return collect();
        }

        $scopeWorkplaceId = (string) ($board->bo_workplace_id ?: $this->application->target_province);

        if ($scopeWorkplaceId === '') {
            $this->recommendedNeededSchoolsNote = __('The selected board does not have a workplace scope.');

            return collect();
        }

        $activeCircular = CadreCirculars::active()
            ->orderByDesc('issued_date')
            ->first();

        if (!$activeCircular) {
            $this->recommendedNeededSchoolsNote = __('No active DMS cadre circular is available, so deficit schools cannot be calculated.');

            return collect();
        }

        $scopeWorkplace = Workplaces::find($scopeWorkplaceId);
        $childWorkplaceIds = $scopeWorkplace?->getAllChildWorkplaces() ?? [$scopeWorkplaceId];
        $zoneIds = $this->recommendedNeededSchoolZoneIds($board, $scopeWorkplaceId);

        if ($zoneIds->isEmpty()) {
            $this->recommendedNeededSchoolsNote = __('No zonal offices are linked to this board scope.');

            return collect();
        }

        $preferredInstitutionIds = $this->application->preferences
            ->pluck('ins_wp_id')
            ->filter()
            ->unique()
            ->values();

        $institutions = Institution::active()
            ->with([
                'authority',
                'zonalEducationOffice',
            ])
            ->whereIn('zeo_wp_id', $zoneIds->all())
            ->when(
                $preferredInstitutionIds->isNotEmpty(),
                fn ($query) => $query->whereNotIn('workplace_id', $preferredInstitutionIds->all())
            )
            ->orderBy('name')
            ->get()
            ->keyBy('workplace_id');

        if ($institutions->isEmpty()) {
            $this->recommendedNeededSchoolsNote = __('No additional active schools were found outside this teacher\'s preference list.');

            return collect();
        }

        $institutionIds = $institutions->keys()->values()->all();
        $origin = $this->buildPreferenceMapOrigin();

        $approvedRows = CadreDMSApproved::query()
            ->with('medium')
            ->where('circular_id', $activeCircular->circular_id)
            ->whereIn('workplace_id', $institutionIds)
            ->where('subject_id', $teacherSubjectId)
            ->when(
                $teacherMediumId !== '',
                fn ($query) => $query->where('medium_id', $teacherMediumId)
            )
            ->get();

        if ($approvedRows->isEmpty()) {
            $this->recommendedNeededSchoolsNote = __('No approved cadre rows were found for this teacher subject and medium in the selected board scope.');

            return collect();
        }

        $approvedMap = $approvedRows
            ->groupBy(fn ($row) => $row->workplace_id . '|' . ($row->medium_id ?? ''))
            ->map(function (Collection $rows) {
                $firstRow = $rows->first();

                return [
                    'workplace_id' => (string) $firstRow->workplace_id,
                    'medium_id' => (string) ($firstRow->medium_id ?? ''),
                    'medium_name' => $firstRow->medium?->name ?? __('Medium N/A'),
                    'approved_posts' => (int) $rows->sum('approved_posts'),
                ];
            });

        $filledMap = EmployerCadreSubject::query()
            ->join('employer_current_appointments as eca', 'eca.appointment_id', '=', 'employer_cadre_subjects.appointment_id')
            ->whereIn('eca.workplace_id', $institutionIds)
            ->where('employer_cadre_subjects.main_subject', $teacherSubjectId)
            ->when(
                $teacherMediumId !== '',
                fn ($query) => $query->where('employer_cadre_subjects.appointment_medium', $teacherMediumId)
            )
            ->get([
                'eca.workplace_id as workplace_id',
                'employer_cadre_subjects.appointment_medium as medium_id',
            ])
            ->groupBy(fn ($row) => $row->workplace_id . '|' . ($row->medium_id ?? ''))
            ->map(fn (Collection $rows) => $rows->count());

        $applicantCounts = TeacherTransferApplicationPreferences::query()
            ->with(['application.teacher'])
            ->whereIn('ins_wp_id', $institutionIds)
            ->whereHas('application', function (Builder $query) use ($board, $scopeWorkplaceId, $childWorkplaceIds, $teacherSubjectId, $teacherMediumId) {
                $query->where('policy_id', $board->policy_id)
                    ->where('transfer_category', $board->transfer_category_id)
                    ->where('transfer_sub_category_id', $board->transfer_sub_category_id)
                    ->where('status', '!=', 'draft')
                    ->where(fn (Builder $scopeQuery) => $this->applyBoardScopeToApplicationQuery($scopeQuery, $board, $scopeWorkplaceId, $childWorkplaceIds))
                    ->whereHas('teacher', function (Builder $teacherQuery) use ($teacherSubjectId, $teacherMediumId) {
                        $teacherQuery->where('main_subject', $teacherSubjectId);

                        if ($teacherMediumId !== '') {
                            $teacherQuery->where('appointment_medium', $teacherMediumId);
                        }
                    });
            })
            ->get()
            ->groupBy(function (TeacherTransferApplicationPreferences $preference) {
                $mediumId = (string) ($preference->application?->teacher?->appointment_medium ?? '');

                return (string) $preference->ins_wp_id . '|' . $mediumId;
            })
            ->map(function (Collection $rows) {
                return $rows
                    ->pluck('transfer_application_id')
                    ->filter()
                    ->unique()
                    ->count();
            });

        $approvedTransferMovements = $this->approvedTransferMovementMaps(
            $board,
            $scopeWorkplaceId,
            $childWorkplaceIds,
            $institutionIds,
            $teacherSubjectId,
            $teacherMediumId,
        );

        $subjectName = $teacher?->mainSubject?->name_en
            ?? $teacher?->mainSubject?->name_si
            ?? $teacher?->mainSubject?->name_ta
            ?? __('Subject not assigned');

        $recommendations = $approvedMap
            ->map(function (array $row, string $key) use ($institutions, $filledMap, $applicantCounts, $approvedTransferMovements, $subjectName, $origin) {
                $institution = $institutions->get($row['workplace_id']);

                if (!$institution) {
                    return null;
                }

                $filledPosts = (int) ($filledMap->get($key, 0));
                $approvedIncomingTransfers = (int) $approvedTransferMovements['incoming']->get($key, 0);
                $approvedOutgoingTransfers = (int) $approvedTransferMovements['outgoing']->get($key, 0);
                $adjustedFilledPosts = max($filledPosts + $approvedIncomingTransfers - $approvedOutgoingTransfers, 0);
                $deficitPosts = max($row['approved_posts'] - $adjustedFilledPosts, 0);

                if ($deficitPosts <= 0) {
                    return null;
                }

                $coordinates = $this->normalizeCoordinates(
                    $institution->latitude,
                    $institution->longitude,
                );

                $distanceKm = null;

                if ($coordinates && filled($origin['lat']) && filled($origin['lng'])) {
                    $distanceKm = $this->haversineDistanceKm(
                        (float) $origin['lat'],
                        (float) $origin['lng'],
                        $coordinates['lat'],
                        $coordinates['lng'],
                    );
                }

                $applicantCount = (int) ($applicantCounts->get($key, 0));

                return [
                    'institution_id' => (int) $institution->id,
                    'workplace_id' => (string) $institution->workplace_id,
                    'name' => $institution->name ?? __('Institution unavailable'),
                    'zone' => $institution->zonalEducationOffice?->name ?? __('Zone unavailable'),
                    'authority' => $institution->authority?->authority_name ?? __('N/A'),
                    'census_no' => $institution->census_no ?? __('N/A'),
                    'subject_name' => $subjectName,
                    'medium_name' => $row['medium_name'],
                    'approved_posts' => $row['approved_posts'],
                    'filled_posts' => $filledPosts,
                    'adjusted_filled_posts' => $adjustedFilledPosts,
                    'approved_incoming_transfers' => $approvedIncomingTransfers,
                    'approved_outgoing_transfers' => $approvedOutgoingTransfers,
                    'deficit_posts' => $deficitPosts,
                    'applicant_count' => $applicantCount,
                    'has_applicants' => $applicantCount > 0,
                    'distance_km' => $distanceKm,
                    'distance_label' => $distanceKm !== null ? number_format($distanceKm, 2) . ' km' : null,
                ];
            })
            ->filter()
            ->sort(function (array $left, array $right) {
                $leftDistance = $left['distance_km'] ?? INF;
                $rightDistance = $right['distance_km'] ?? INF;

                return [$right['deficit_posts'], $left['applicant_count'], $leftDistance, $left['name']]
                    <=> [$left['deficit_posts'], $right['applicant_count'], $rightDistance, $right['name']];
            })
            ->values();

        if ($recommendations->isEmpty()) {
            $this->recommendedNeededSchoolsNote = __('There are no extra schools outside this teacher\'s preferences with a matching subject and medium deficit in the selected board scope.');
        }

        return $recommendations;
    }

    protected function approvedTransferMovementMaps(
        TeacherTransferBoard $board,
        string $scopeWorkplaceId,
        array $childWorkplaceIds,
        array $institutionIds,
        string $teacherSubjectId,
        string $teacherMediumId,
    ): array {
        $institutionIdLookup = collect($institutionIds)
            ->map(fn ($institutionId) => (string) $institutionId)
            ->flip();

        $approvedRecommendations = TeacherTransferBoardRecommendation::query()
            ->with(['application.teacher'])
            ->where('recommendation_status', 'approved')
            ->where('active_status', true)
            ->whereNotNull('selected_school_id')
            ->where(function (Builder $query) use ($institutionIds) {
                $query->whereIn('selected_school_id', $institutionIds)
                    ->orWhereHas('application', function (Builder $applicationQuery) use ($institutionIds) {
                        $applicationQuery->whereIn('current_workplace', $institutionIds);
                    });
            })
            ->whereHas('application', function (Builder $query) use ($board, $scopeWorkplaceId, $childWorkplaceIds, $teacherSubjectId, $teacherMediumId) {
                $query->where('policy_id', $board->policy_id)
                    ->where('transfer_category', $board->transfer_category_id)
                    ->where('transfer_sub_category_id', $board->transfer_sub_category_id)
                    ->where(fn (Builder $scopeQuery) => $this->applyBoardScopeToApplicationQuery($scopeQuery, $board, $scopeWorkplaceId, $childWorkplaceIds))
                    ->whereHas('teacher', function (Builder $teacherQuery) use ($teacherSubjectId, $teacherMediumId) {
                        $teacherQuery->where('main_subject', $teacherSubjectId);

                        if ($teacherMediumId !== '') {
                            $teacherQuery->where('appointment_medium', $teacherMediumId);
                        }
                    });
            })
            ->get();

        $incoming = $approvedRecommendations
            ->filter(function (TeacherTransferBoardRecommendation $recommendation) use ($institutionIdLookup) {
                $selectedSchoolId = (string) $recommendation->selected_school_id;
                $currentWorkplaceId = (string) ($recommendation->application?->current_workplace ?? '');

                return $selectedSchoolId !== ''
                    && $selectedSchoolId !== $currentWorkplaceId
                    && $institutionIdLookup->has($selectedSchoolId);
            })
            ->groupBy(function (TeacherTransferBoardRecommendation $recommendation) {
                return (string) $recommendation->selected_school_id
                    . '|'
                    . (string) ($recommendation->application?->teacher?->appointment_medium ?? '');
            })
            ->map(fn (Collection $rows) => $rows->pluck('transfer_application_id')->filter()->unique()->count());

        $outgoing = $approvedRecommendations
            ->filter(function (TeacherTransferBoardRecommendation $recommendation) use ($institutionIdLookup) {
                $selectedSchoolId = (string) $recommendation->selected_school_id;
                $currentWorkplaceId = (string) ($recommendation->application?->current_workplace ?? '');

                return $currentWorkplaceId !== ''
                    && $selectedSchoolId !== $currentWorkplaceId
                    && $institutionIdLookup->has($currentWorkplaceId);
            })
            ->groupBy(function (TeacherTransferBoardRecommendation $recommendation) {
                return (string) ($recommendation->application?->current_workplace ?? '')
                    . '|'
                    . (string) ($recommendation->application?->teacher?->appointment_medium ?? '');
            })
            ->map(fn (Collection $rows) => $rows->pluck('transfer_application_id')->filter()->unique()->count());

        return [
            'incoming' => $incoming,
            'outgoing' => $outgoing,
        ];
    }

    protected function recommendedNeededSchoolZoneIds(TeacherTransferBoard $board, string $scopeWorkplaceId): Collection
    {
        if ($this->isZonalDecisionBoard($board)) {
            return collect([$scopeWorkplaceId])
                ->filter()
                ->values();
        }

        if (($board->bo_office_level_id ?? null) === 'OLID002') {
            return ZonalEducationOffice::active()
                ->whereIn('workplace_id', $board->workplace?->getAllChildWorkplaces() ?? [])
                ->pluck('workplace_id');
        }

        return ZonalEducationOffice::active()
            ->where('peo_wp_id', $scopeWorkplaceId)
            ->pluck('workplace_id');
    }

    protected function applyBoardScopeToApplicationQuery(Builder $query, TeacherTransferBoard $board, string $scopeWorkplaceId, array $childWorkplaceIds): Builder
    {
        if (!empty($childWorkplaceIds)) {
            return $query->whereIn('current_workplace', $childWorkplaceIds);
        }

        return $query->where('current_workplace', $scopeWorkplaceId);
    }

    protected function buildPreferenceMapOrigin(): array
    {
        $savedCoordinates = $this->normalizeCoordinates(
            $this->application->latitude,
            $this->application->longitude,
        ) ?? $this->normalizeCoordinates(
            $this->application->employee?->latitude,
            $this->application->employee?->longitude,
        );

        $address = trim((string) $this->application->permanent_address);

        return [
            'label' => trim(($this->application->employee?->full_name ?? 'Teacher') . ' - Permanent Address'),
            'address' => $address,
            'lat' => $savedCoordinates['lat'] ?? null,
            'lng' => $savedCoordinates['lng'] ?? null,
            'query_candidates' => $this->buildAddressQueryCandidates($address),
        ];
    }

    protected function buildPreferenceMapSchool($preference, array $origin): ?array
    {
        $workplace = $preference->institution;
        $institution = $workplace?->office();

        if (!$workplace && !$institution) {
            return null;
        }

        $coordinates = $this->normalizeCoordinates(
            $institution?->latitude,
            $institution?->longitude,
        );

        $distanceKm = null;

        if ($coordinates && filled($origin['lat']) && filled($origin['lng'])) {
            $distanceKm = $this->haversineDistanceKm(
                (float) $origin['lat'],
                (float) $origin['lng'],
                $coordinates['lat'],
                $coordinates['lng'],
            );
        }

        return [
            'order' => (int) $preference->preference_order,
            'institution_workplace_id' => (string) $preference->ins_wp_id,
            'name' => $institution?->name ?? $workplace?->office_name ?? 'Institution unavailable',
            'zone' => $preference->zonalOffice?->office_name ?? 'Zone unavailable',
            'lat' => $coordinates['lat'] ?? null,
            'lng' => $coordinates['lng'] ?? null,
            'distance_km' => $distanceKm,
            'distance_label' => $distanceKm !== null ? number_format($distanceKm, 2) . ' km' : null,
            'saved_distance_km' => $preference->distance !== null ? (float) $preference->distance : null,
            'saved_distance_label' => $preference->distance !== null ? number_format((float) $preference->distance, 2) . ' km' : null,
        ];
    }

    protected function buildAddressQueryCandidates(string $address): array
    {
        if ($address === '') {
            return [];
        }

        $parts = collect(array_values(array_filter(array_map('trim', explode(',', $address)))));
        $queries = collect();

        $queries->push($address . ', Sri Lanka');

        if ($parts->count() >= 3) {
            $queries->push($parts->slice(-3)->implode(', ') . ', Sri Lanka');
        }

        if ($parts->count() >= 2) {
            $queries->push($parts->slice(-2)->implode(', ') . ', Sri Lanka');
        }

        if ($parts->isNotEmpty()) {
            $queries->push($parts->last() . ', Sri Lanka');
        }

        return $queries
            ->map(fn ($query) => preg_replace('/\s+/', ' ', trim((string) $query)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function normalizeCoordinates($lat, $lng): ?array
    {
        if ($lat === null || $lng === null || $lat === '' || $lng === '') {
            return null;
        }

        $latValue = (float) $lat;
        $lngValue = (float) $lng;

        // Some records are stored as lng/lat. Correct them for Sri Lankan coordinates.
        if ($latValue > 50 && $lngValue < 20) {
            [$latValue, $lngValue] = [$lngValue, $latValue];
        }

        if ($latValue < -90 || $latValue > 90 || $lngValue < -180 || $lngValue > 180) {
            return null;
        }

        return [
            'lat' => round($latValue, 7),
            'lng' => round($lngValue, 7),
        ];
    }

    protected function haversineDistanceKm(float $originLat, float $originLng, float $destinationLat, float $destinationLng): float
    {
        $earthRadiusKm = 6371;

        $deltaLat = deg2rad($destinationLat - $originLat);
        $deltaLng = deg2rad($destinationLng - $originLng);

        $a = sin($deltaLat / 2) ** 2
            + cos(deg2rad($originLat)) * cos(deg2rad($destinationLat)) * sin($deltaLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadiusKm * $c, 2);
    }

    public function render()
    {
        return view('livewire.transfer-module.teacher.teacher-transfer-board.teacher-profile-for-transfer-board');
    }
}
