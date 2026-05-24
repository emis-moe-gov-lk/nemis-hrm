<?php

namespace App\Livewire\TransferModule\Teacher\TeacherTransferBoard;

use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferAppeals;
use App\Models\TeacherTransferBoard;
use App\Models\Workplaces;
use Illuminate\Database\Eloquent\Builder;

class ZoneTeacherTransferBoard extends ProvinceTeacherTransferBoard
{
    protected function supportedSubCategoryCodes(): array
    {
        return [
            \App\Support\Transfer\TransferSubCategoryRules::CODE_INTER_ZONE,
        ];
    }

    protected function boardRouteScope(): string
    {
        return 'zone';
    }

    protected function boardScopeOfficeLevelId(): string
    {
        return 'OLID004';
    }

    protected function createBoardStageForCurrentScope(): string
    {
        return TeacherTransferBoard::STAGE_ZEO;
    }

    protected function boardScopeRelationName(): string
    {
        return 'zonal';
    }

    protected function boardScopeTitle(): string
    {
        return 'Zonal';
    }

    protected function boardScopeNameLower(): string
    {
        return 'zone';
    }

    protected function boardScopeNamePlural(): string
    {
        return 'zones';
    }

    protected function observerScopeWorkplace(): ?Workplaces
    {
        if ($this->selectedBoardId === '') {
            return null;
        }

        $userWorkplaceId = auth()->user()?->workplace_id;

        if (!$userWorkplaceId) {
            return null;
        }

        $userWorkplace = Workplaces::with('officeLevel')->find($userWorkplaceId);

        if (!$userWorkplace || $userWorkplace->office_level_id !== 'OLID003') {
            return null;
        }

        $board = TeacherTransferBoard::query()
            ->ofType($this->boardType)
            ->where('board_id', $this->selectedBoardId)
            ->where('bo_office_level_id', $this->boardScopeOfficeLevelId())
            ->first(['bo_workplace_id']);

        if (!$board || !$board->bo_workplace_id) {
            return null;
        }

        $zonalWorkplace = Workplaces::with('officeLevel')->find($board->bo_workplace_id);

        if (!$zonalWorkplace || $zonalWorkplace->office_level_id !== $this->boardScopeOfficeLevelId()) {
            return null;
        }

        $parentWorkplaceIds = collect($zonalWorkplace->getAllParentWorkplaces())
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->all();

        if (!in_array((string) $userWorkplace->workplace_id, $parentWorkplaceIds, true)) {
            return null;
        }

        return $zonalWorkplace;
    }

    protected function scopedApplicationQuery(): Builder
    {
        $workplace = $this->currentProvincialWorkplace();

        if (!$workplace) {
            return $this->emptyApplicationQuery();
        }

        $childWorkplaceIds = $workplace->getAllChildWorkplaces();

        return TeacherTransferApplication::query()
            ->whereIn('current_workplace', $childWorkplaceIds);
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
                $query->whereIn('current_workplace', $childWorkplaceIds);
            });
    }

    public function isIncomingApplication(?TeacherTransferApplication $application): bool
    {
        $workplace = $this->currentWorkplace;

        if (!$application || !$workplace) {
            return false;
        }

        return (string) $application->currentWorkplace?->zeo_wp_id === (string) $workplace->workplace_id
            || (string) $application->currentWorkplace?->workplace_id === (string) $workplace->workplace_id;
    }
}
