<?php

namespace App\Services\TransferModule;

use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\EmployerCadreSubject;
use App\Models\Institution;
use App\Models\MediumOfInstruction;
use App\Models\SubjectList;
use App\Models\TeacherTransferApplicationPreferences;
use App\Models\TeacherTransferBoardRecommendation;
use App\Models\TransferBoard;
use App\Models\Workplaces;
use App\Models\ZonalEducationOffice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TransferBoardSchoolBalanceService
{
    public function summary(TransferBoard $board): array
    {
        $board->loadMissing(['subjects', 'workplace', 'policy', 'category']);

        $activeCircular = CadreCirculars::active()
            ->orderByDesc('issued_date')
            ->first();

        if (!$activeCircular) {
            return $this->emptySummary(__('No active DMS cadre circular is available.'));
        }

        $subjectIds = $board->subjects
            ->pluck('subject_id')
            ->filter()
            ->unique()
            ->values();

        if ($subjectIds->isEmpty()) {
            return $this->emptySummary(__('No subjects are attached to this board.'), $activeCircular);
        }

        $scopeWorkplaceId = (string) $board->bo_workplace_id;
        $zoneIds = $this->zoneIdsForBoard($board);

        if ($scopeWorkplaceId === '' || $zoneIds->isEmpty()) {
            return $this->emptySummary(__('No school scope could be resolved for this board.'), $activeCircular);
        }

        $institutions = Institution::active()
            ->with(['authority', 'zonalEducationOffice'])
            ->whereIn('zeo_wp_id', $zoneIds->all())
            ->orderBy('name')
            ->get()
            ->keyBy('workplace_id');

        if ($institutions->isEmpty()) {
            return $this->emptySummary(__('No active schools were found inside this board scope.'), $activeCircular);
        }

        $institutionIds = $institutions->keys()->map(fn ($id) => (string) $id)->values()->all();
        $childWorkplaceIds = $this->childWorkplaceIds($board);

        $approvedMap = $this->approvedCadreMap($activeCircular->circular_id, $institutionIds, $subjectIds->all());
        $filledMap = $this->filledCadreMap($institutionIds, $subjectIds->all());
        $applicantMap = $this->applicantMap($board, $scopeWorkplaceId, $childWorkplaceIds, $institutionIds, $subjectIds->all());
        $movementMaps = $this->approvedTransferMovementMaps($board, $scopeWorkplaceId, $childWorkplaceIds, $institutionIds, $subjectIds->all());

        $allKeys = $approvedMap->keys()
            ->merge($filledMap->keys())
            ->merge($movementMaps['incoming']->keys())
            ->merge($movementMaps['outgoing']->keys())
            ->unique()
            ->values();

        $subjectCache = SubjectList::whereIn('subject_id', $subjectIds->all())
            ->get()
            ->keyBy('subject_id');

        $mediumIds = $allKeys
            ->map(fn (string $key) => explode('|', $key)[2] ?? '')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $mediumCache = MediumOfInstruction::whereIn('medium_id', $mediumIds)
            ->get()
            ->keyBy('medium_id');

        $rows = $allKeys
            ->map(function (string $key) use ($institutions, $approvedMap, $filledMap, $applicantMap, $movementMaps, $subjectCache, $mediumCache) {
                [$workplaceId, $subjectId, $mediumId] = array_pad(explode('|', $key), 3, '');
                $institution = $institutions->get($workplaceId);

                if (!$institution || $subjectId === '') {
                    return null;
                }

                $approvedPosts = (int) ($approvedMap->get($key, 0));
                $filledPosts = (int) ($filledMap->get($key, 0));
                $incomingTransfers = (int) ($movementMaps['incoming']->get($key, 0));
                $outgoingTransfers = (int) ($movementMaps['outgoing']->get($key, 0));
                $adjustedFilledPosts = max($filledPosts + $incomingTransfers - $outgoingTransfers, 0);
                $diff = $adjustedFilledPosts - $approvedPosts;

                if ($diff === 0) {
                    return null;
                }

                $subject = $subjectCache->get($subjectId);
                $medium = $mediumCache->get($mediumId);

                return [
                    'workplace_id' => (string) $workplaceId,
                    'school_name' => $institution->name ?? __('Institution unavailable'),
                    'census_no' => $institution->census_no ?? __('N/A'),
                    'zone_name' => $institution->zonalEducationOffice?->name ?? __('Zone unavailable'),
                    'authority' => $institution->authority?->authority_name ?? __('N/A'),
                    'subject_id' => (string) $subjectId,
                    'subject_name' => $subject?->name_en ?? $subject?->name_si ?? $subject?->name_ta ?? $subjectId,
                    'medium_id' => (string) $mediumId,
                    'medium_name' => $medium?->name ?? ($mediumId !== '' ? $mediumId : __('Medium N/A')),
                    'approved_posts' => $approvedPosts,
                    'filled_posts' => $filledPosts,
                    'incoming_transfers' => $incomingTransfers,
                    'outgoing_transfers' => $outgoingTransfers,
                    'adjusted_filled_posts' => $adjustedFilledPosts,
                    'diff' => $diff,
                    'status' => $diff < 0 ? 'needed' : 'excess',
                    'need_count' => $diff < 0 ? abs($diff) : 0,
                    'excess_count' => $diff > 0 ? $diff : 0,
                    'applicant_count' => (int) ($applicantMap->get($key, 0)),
                ];
            })
            ->filter()
            ->values();

        $needed = $this->groupSchoolBalanceRows($rows, 'needed')
            ->where('status', 'needed')
            ->sortBy([
                ['need_count', 'desc'],
                ['applicant_count', 'asc'],
                ['school_name', 'asc'],
            ])
            ->values();

        $excess = $this->groupSchoolBalanceRows($rows, 'excess')
            ->where('status', 'excess')
            ->sortBy([
                ['excess_count', 'desc'],
                ['school_name', 'asc'],
            ])
            ->values();

        return [
            'activeCircular' => $activeCircular,
            'subjects' => $board->subjects,
            'needed' => $needed,
            'excess' => $excess,
            'note' => '',
        ];
    }

    protected function groupSchoolBalanceRows(Collection $rows, string $status): Collection
    {
        return $rows
            ->where('status', $status)
            ->groupBy('workplace_id')
            ->map(function (Collection $schoolRows) use ($status) {
                $firstRow = $schoolRows->first();
                $details = $schoolRows
                    ->sortBy([
                        ['subject_name', 'asc'],
                        ['medium_name', 'asc'],
                    ])
                    ->values()
                    ->all();

                return [
                    'workplace_id' => $firstRow['workplace_id'],
                    'school_name' => $firstRow['school_name'],
                    'census_no' => $firstRow['census_no'],
                    'zone_name' => $firstRow['zone_name'],
                    'authority' => $firstRow['authority'],
                    'subject_id' => $schoolRows->pluck('subject_id')->filter()->unique()->implode(', '),
                    'subject_name' => $schoolRows->pluck('subject_name')->filter()->unique()->implode(', '),
                    'medium_id' => $schoolRows->pluck('medium_id')->filter()->unique()->implode(', '),
                    'medium_name' => $schoolRows->pluck('medium_name')->filter()->unique()->implode(', '),
                    'approved_posts' => (int) $schoolRows->sum('approved_posts'),
                    'filled_posts' => (int) $schoolRows->sum('filled_posts'),
                    'incoming_transfers' => (int) $schoolRows->sum('incoming_transfers'),
                    'outgoing_transfers' => (int) $schoolRows->sum('outgoing_transfers'),
                    'adjusted_filled_posts' => (int) $schoolRows->sum('adjusted_filled_posts'),
                    'diff' => (int) $schoolRows->sum('diff'),
                    'status' => $status,
                    'need_count' => (int) $schoolRows->sum('need_count'),
                    'excess_count' => (int) $schoolRows->sum('excess_count'),
                    'applicant_count' => (int) $schoolRows->sum('applicant_count'),
                    'medium_rows' => $details,
                ];
            })
            ->values();
    }

    protected function emptySummary(string $note, $activeCircular = null): array
    {
        return [
            'activeCircular' => $activeCircular,
            'subjects' => collect(),
            'needed' => collect(),
            'excess' => collect(),
            'note' => $note,
        ];
    }

    protected function zoneIdsForBoard(TransferBoard $board): Collection
    {
        if ($board->bo_office_level_id === 'OLID004') {
            return collect([(string) $board->bo_workplace_id])
                ->filter()
                ->values();
        }

        if ($board->bo_office_level_id === 'OLID003') {
            return ZonalEducationOffice::active()
                ->where('peo_wp_id', $board->bo_workplace_id)
                ->pluck('workplace_id')
                ->map(fn ($id) => (string) $id)
                ->values();
        }

        return collect();
    }

    protected function childWorkplaceIds(TransferBoard $board): array
    {
        $workplace = $board->workplace ?: Workplaces::find($board->bo_workplace_id);
        $ids = $workplace?->getAllChildWorkplaces() ?? [];

        if (empty($ids) && filled($board->bo_workplace_id)) {
            $ids = [(string) $board->bo_workplace_id];
        }

        return collect($ids)
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    protected function approvedCadreMap(string $circularId, array $institutionIds, array $subjectIds): Collection
    {
        return CadreDMSApproved::query()
            ->where('circular_id', $circularId)
            ->where('active_status', true)
            ->whereIn('workplace_id', $institutionIds)
            ->whereIn('subject_id', $subjectIds)
            ->get(['workplace_id', 'subject_id', 'medium_id', 'approved_posts'])
            ->groupBy(fn (CadreDMSApproved $row) => $this->balanceKey($row->workplace_id, $row->subject_id, $row->medium_id))
            ->map(fn (Collection $rows) => (int) $rows->sum('approved_posts'));
    }

    protected function filledCadreMap(array $institutionIds, array $subjectIds): Collection
    {
        return EmployerCadreSubject::query()
            ->join('employer_current_appointments as eca', 'eca.appointment_id', '=', 'employer_cadre_subjects.appointment_id')
            ->whereIn('eca.workplace_id', $institutionIds)
            ->whereIn('employer_cadre_subjects.main_subject', $subjectIds)
            ->whereNotNull('employer_cadre_subjects.main_subject')
            ->whereNotNull('employer_cadre_subjects.appointment_medium')
            ->get([
                'eca.workplace_id as workplace_id',
                'employer_cadre_subjects.main_subject as subject_id',
                'employer_cadre_subjects.appointment_medium as medium_id',
            ])
            ->groupBy(fn ($row) => $this->balanceKey($row->workplace_id, $row->subject_id, $row->medium_id))
            ->map(fn (Collection $rows) => $rows->count());
    }

    protected function applicantMap(TransferBoard $board, string $scopeWorkplaceId, array $childWorkplaceIds, array $institutionIds, array $subjectIds): Collection
    {
        return TeacherTransferApplicationPreferences::query()
            ->with(['application.teacher'])
            ->whereIn('ins_wp_id', $institutionIds)
            ->whereHas('application', function (Builder $query) use ($board, $scopeWorkplaceId, $childWorkplaceIds, $subjectIds) {
                $query->where('policy_id', $board->policy_id)
                    ->where('transfer_category', $board->transfer_category_id)
                    ->where('status', '!=', 'draft')
                    ->where(fn (Builder $scopeQuery) => $this->applyBoardScopeToApplicationQuery($scopeQuery, $board, $scopeWorkplaceId, $childWorkplaceIds))
                    ->whereHas('teacher', fn (Builder $teacherQuery) => $teacherQuery->whereIn('main_subject', $subjectIds));
            })
            ->get()
            ->groupBy(function (TeacherTransferApplicationPreferences $preference) {
                return $this->balanceKey(
                    $preference->ins_wp_id,
                    $preference->application?->teacher?->main_subject,
                    $preference->application?->teacher?->appointment_medium,
                );
            })
            ->map(fn (Collection $rows) => $rows->pluck('transfer_application_id')->filter()->unique()->count());
    }

    protected function approvedTransferMovementMaps(TransferBoard $board, string $scopeWorkplaceId, array $childWorkplaceIds, array $institutionIds, array $subjectIds): array
    {
        $institutionIdLookup = collect($institutionIds)->flip();

        $approvedRecommendations = TeacherTransferBoardRecommendation::query()
            ->with(['application.teacher'])
            ->where('recommendation_status', 'approved')
            ->where('active_status', true)
            ->whereNotNull('selected_school_id')
            ->where(function (Builder $query) use ($institutionIds) {
                $query->whereIn('selected_school_id', $institutionIds)
                    ->orWhereHas('application', fn (Builder $applicationQuery) => $applicationQuery->whereIn('current_workplace', $institutionIds));
            })
            ->whereHas('application', function (Builder $query) use ($board, $scopeWorkplaceId, $childWorkplaceIds, $subjectIds) {
                $query->where('policy_id', $board->policy_id)
                    ->where('transfer_category', $board->transfer_category_id)
                    ->where(fn (Builder $scopeQuery) => $this->applyBoardScopeToApplicationQuery($scopeQuery, $board, $scopeWorkplaceId, $childWorkplaceIds))
                    ->whereHas('teacher', fn (Builder $teacherQuery) => $teacherQuery->whereIn('main_subject', $subjectIds));
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
                return $this->balanceKey(
                    $recommendation->selected_school_id,
                    $recommendation->application?->teacher?->main_subject,
                    $recommendation->application?->teacher?->appointment_medium,
                );
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
                return $this->balanceKey(
                    $recommendation->application?->current_workplace,
                    $recommendation->application?->teacher?->main_subject,
                    $recommendation->application?->teacher?->appointment_medium,
                );
            })
            ->map(fn (Collection $rows) => $rows->pluck('transfer_application_id')->filter()->unique()->count());

        return [
            'incoming' => $incoming,
            'outgoing' => $outgoing,
        ];
    }

    protected function applyBoardScopeToApplicationQuery(Builder $query, TransferBoard $board, string $scopeWorkplaceId, array $childWorkplaceIds): Builder
    {
        if ($board->bo_office_level_id === 'OLID004') {
            return $query->where(function (Builder $scopeQuery) use ($scopeWorkplaceId, $childWorkplaceIds) {
                $scopeQuery->whereHas('preferences', fn (Builder $preferenceQuery) => $preferenceQuery->where('zeo_wp_id', $scopeWorkplaceId))
                    ->orWhereIn('current_workplace', $childWorkplaceIds);
            });
        }

        return $query->where(function (Builder $scopeQuery) use ($scopeWorkplaceId, $childWorkplaceIds) {
            $scopeQuery->where('target_province', $scopeWorkplaceId)
                ->orWhereIn('current_workplace', $childWorkplaceIds);
        });
    }

    protected function balanceKey($workplaceId, $subjectId, $mediumId): string
    {
        return (string) $workplaceId . '|' . (string) $subjectId . '|' . (string) $mediumId;
    }
}
