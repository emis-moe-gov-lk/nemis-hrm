<?php

namespace App\Livewire\TransferModule\TransferBoard;

use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferAppeals;
use Illuminate\Database\Eloquent\Builder;

class ZoneTeacherTransferBoard extends ProvinceTeacherTransferBoard
{
    protected function boardRouteScope(): string
    {
        return 'zone';
    }

    protected function boardScopeOfficeLevelId(): string
    {
        return 'OLID004';
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

    protected function scopedApplicationQuery(): Builder
    {
        $workplace = $this->currentProvincialWorkplace();

        if (!$workplace) {
            return $this->emptyApplicationQuery();
        }

        $childWorkplaceIds = $workplace->getAllChildWorkplaces();

        return TeacherTransferApplication::query()
            ->where(function (Builder $query) use ($workplace, $childWorkplaceIds) {
                $query->whereHas('preferences', function (Builder $preferenceQuery) use ($workplace) {
                    $preferenceQuery->where('zeo_wp_id', $workplace->workplace_id);
                })
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
                    $innerQuery->whereHas('preferences', function (Builder $preferenceQuery) use ($workplace) {
                        $preferenceQuery->where('zeo_wp_id', $workplace->workplace_id);
                    })
                        ->orWhereIn('current_workplace', $childWorkplaceIds);
                });
            });
    }

    public function isIncomingApplication(?TeacherTransferApplication $application): bool
    {
        $workplace = $this->currentWorkplace;

        if (!$application || !$workplace) {
            return false;
        }

        if ($application->relationLoaded('preferences')) {
            return $application->preferences
                ->contains(fn ($preference) => (string) $preference->zeo_wp_id === (string) $workplace->workplace_id);
        }

        return $application->preferences()
            ->where('zeo_wp_id', $workplace->workplace_id)
            ->exists();
    }
}
