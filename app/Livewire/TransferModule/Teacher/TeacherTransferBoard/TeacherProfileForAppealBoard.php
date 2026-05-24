<?php

namespace App\Livewire\TransferModule\Teacher\TeacherTransferBoard;

use App\Models\Institution;
use App\Models\TeacherTransferAppeals;
use App\Models\TeacherTransferBoard;
use App\Models\Workplaces;
use App\Models\ZonalEducationOffice;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class TeacherProfileForAppealBoard extends Component
{
    public $appeal;
    public $application;
    public string $board = 'province';
    public string $selectedBoardId = '';
    public bool $showDecisionModal = false;
    public string $decisionOutcome = 'approved';
    public string $decisionPreferenceInstitution = '';
    public string $decisionSchoolSelectionType = 'preferred';
    public string $decisionOtherZoneId = '';
    public string $decisionOtherSchoolId = '';
    public ?string $decisionEffectiveDate = null;
    public string $decisionNote = '';
    public string $decisionRejectionReason = '';

    public function mount($id): void
    {
        $this->board = $this->normalizeBoard(request()->query('board'));
        $this->selectedBoardId = (string) request()->query('selectedBoardId', '');

        $this->appeal = TeacherTransferAppeals::with([
            'selectedZone',
            'selectedSchool',
            'board',
            'application' => fn ($query) => $query->with($this->applicationRelations()),
        ])
            ->where(function ($query) use ($id) {
                $query->where('appeal_id', $id)
                    ->orWhere('id', $id);
            })
            ->firstOrFail();

        $this->application = $this->appeal->application;
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
            'boardRecommendation.recommendationList',
            'boardRecommendation.board',
            'boardRecommendation.selectedZone',
            'boardRecommendation.selectedSchool',
        ];
    }

    public function prepareDecisionDraft(): void
    {
        $board = $this->decisionBoard();

        if (!$board) {
            session()->flash('error', 'Open an appeal board before making a decision.');

            return;
        }

        if ($this->isReadOnlyObserver()) {
            session()->flash('error', $this->readOnlyObserverMessage());

            return;
        }

        if ($board->isClosed()) {
            session()->flash('error', 'This appeal board is closed. Decisions can only be viewed.');

            return;
        }

        $this->decisionOutcome = $this->appeal->appeal_status === TeacherTransferAppeals::STATUS_REJECTED ? 'rejected' : 'approved';
        $this->decisionPreferenceInstitution = '';
        $this->decisionSchoolSelectionType = $this->appeal->school_selection_type ?: ($this->application->preferences->isEmpty() ? 'other' : 'preferred');
        $this->decisionOtherZoneId = '';
        $this->decisionOtherSchoolId = '';
        $this->decisionEffectiveDate = $this->appeal->transfer_effective_date?->toDateString();
        $this->decisionNote = (string) ($this->appeal->decision_remarks ?? '');
        $this->decisionRejectionReason = (string) ($this->appeal->rejection_reason ?? '');

        if ($this->appeal->appeal_status === TeacherTransferAppeals::STATUS_APPROVED) {
            if ($this->appeal->school_selection_type === 'other') {
                $this->decisionOtherZoneId = (string) $this->appeal->selected_zone_id;
                $this->decisionOtherSchoolId = (string) $this->appeal->selected_school_id;
            } else {
                $this->decisionSchoolSelectionType = 'preferred';
                $this->decisionPreferenceInstitution = (string) $this->appeal->selected_school_id;
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
            session()->flash('error', 'Open an appeal board before making a decision.');

            return;
        }

        if ($this->isReadOnlyObserver()) {
            session()->flash('error', $this->readOnlyObserverMessage());

            return;
        }

        if ($board->isClosed()) {
            session()->flash('error', 'This appeal board is closed. Decisions can only be viewed.');

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
        $status = $this->decisionOutcome === 'rejected'
            ? TeacherTransferAppeals::STATUS_REJECTED
            : TeacherTransferAppeals::STATUS_APPROVED;

        try {
            DB::transaction(function () use ($board, $schoolSelectionType, $selectedZoneId, $selectedSchoolId, $status) {
                $this->appeal->update([
                    'appeal_board_id' => $board->board_id,
                    'appeal_status' => $status,
                    'decision_remarks' => $this->decisionNote,
                    'school_selection_type' => $schoolSelectionType,
                    'selected_zone_id' => $selectedZoneId,
                    'selected_school_id' => $selectedSchoolId,
                    'transfer_effective_date' => $status === TeacherTransferAppeals::STATUS_APPROVED ? $this->decisionEffectiveDate : null,
                    'rejection_reason' => $status === TeacherTransferAppeals::STATUS_REJECTED ? $this->decisionRejectionReason : null,
                ]);

                $this->application->update([
                    'status' => $this->resolvedApplicationStatusAfterAppeal($status),
                ]);
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            session()->flash('error', 'Unable to save the appeal board decision right now.');

            return;
        }

        $this->appeal->refresh();
        $this->appeal->load([
            'selectedZone',
            'selectedSchool',
            'board',
            'application' => fn ($query) => $query->with($this->applicationRelations()),
        ]);
        $this->application = $this->appeal->application;
        $this->showDecisionModal = false;

        session()->flash('success', 'Appeal board decision saved successfully.');

        return $this->redirect($this->backRoute, navigate: true);
    }

    protected function resolvedApplicationStatusAfterAppeal(string $appealStatus): string
    {
        if ($appealStatus === TeacherTransferAppeals::STATUS_APPROVED) {
            return 'approved';
        }

        $originalStatus = $this->application->boardRecommendation?->recommendation_status;

        if (in_array($originalStatus, ['approved', 'rejected'], true)) {
            return $originalStatus;
        }

        return (string) ($this->application->status ?: 'submitted');
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

    protected function normalizeBoard(?string $board): string
    {
        return in_array($board, ['province', 'zone', 'pmoe'], true)
            ? $board
            : 'province';
    }

    public function getBackRouteProperty(): string
    {
        return match ($this->board) {
            'zone' => route('transfer-board.zone-teacher-appeal', array_filter([
                'selectedBoardId' => $this->selectedBoardId,
            ])),
            'pmoe' => route('transfer-board.provincial-ministry-teacher-appeal', array_filter([
                'selectedBoardId' => $this->selectedBoardId,
            ])),
            default => route('transfer-board.province-teacher-appeal', array_filter([
                'selectedBoardId' => $this->selectedBoardId,
            ])),
        };
    }

    public function getBackLabelProperty(): string
    {
        return match ($this->board) {
            'zone' => 'Back to Zonal Appeal Board',
            'pmoe' => 'Back to Provincial Ministry Appeal Board',
            default => 'Back to Provincial Appeal Board',
        };
    }

    public function getSelectedAppealBoardProperty(): ?TeacherTransferBoard
    {
        if ($this->selectedBoardId === '') {
            return null;
        }

        $query = TeacherTransferBoard::with(['policy', 'category', 'subjects', 'workplace'])
            ->appeal()
            ->where('board_id', $this->selectedBoardId);

        if ($this->board === 'zone') {
            $query->where('bo_office_level_id', 'OLID004');
        } elseif ($this->board === 'pmoe') {
            $query->where('bo_office_level_id', 'OLID002');
        } else {
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
            && $this->canProvinceObserveSelectedZoneBoard($this->selectedAppealBoard);
    }

    protected function readOnlyObserverMessage(): string
    {
        return 'This zonal appeal board can only be viewed in read-only mode from the provincial level.';
    }

    public function getIsReadOnlyObserverProperty(): bool
    {
        return $this->isReadOnlyObserver();
    }

    protected function decisionBoard(): ?TeacherTransferBoard
    {
        return $this->selectedAppealBoard
            ?: $this->appeal->board;
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

    public function statusBadge(string $status): array
    {
        return match ($status) {
            'pending' => ['color' => 'amber', 'label' => 'Pending'],
            'approved' => ['color' => 'green', 'label' => 'Approved'],
            'rejected' => ['color' => 'rose', 'label' => 'Rejected'],
            default => ['color' => 'zinc', 'label' => ucfirst($status)],
        };
    }

    public function render()
    {
        return view('livewire.transfer-module.teacher.teacher-transfer-board.teacher-profile-for-appeal-board');
    }
}
