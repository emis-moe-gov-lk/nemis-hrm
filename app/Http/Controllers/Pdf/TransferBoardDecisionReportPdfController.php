<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\TeacherTransferApplication;
use App\Models\TeacherTransferBoardRecommendation;
use App\Models\TransferBoard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use misterspelik\LaravelPdf\Facades\Pdf;

class TransferBoardDecisionReportPdfController extends Controller
{
    public function download(string $boardId)
    {
        $board = TransferBoard::with([
            'policy',
            'category',
            'chairman.currentAppointment.workplace',
            'secretary.currentAppointment.workplace',
            'members.person.currentAppointment.workplace',
            'subjects',
            'workplace',
            'officeLevel',
        ])
            ->where('board_id', $boardId)
            ->firstOrFail();

        abort_unless($this->userCanAccessBoard($board), 403);

        abort_unless($board->isClosed(), 409, 'Decision report is available only after closing the transfer board.');

        $applications = $this->matchingApplications($board);

        $decisions = TeacherTransferBoardRecommendation::with([
            'recommendationList',
            'selectedZone',
            'selectedSchool',
            'creator',
            'updater',
        ])
            ->where('transfer_board_id', $board->board_id)
            ->whereIn('transfer_application_id', $applications->pluck('transfer_application_id'))
            ->get()
            ->keyBy('transfer_application_id');

        $additionalMembers = $board->members
            ->filter(fn ($member) => $member->active_status && strtolower((string) $member->role) === 'member')
            ->values();

        $summary = [
            'total' => $applications->count(),
            'approved' => $decisions->where('recommendation_status', 'approved')->count(),
            'rejected' => $decisions->where('recommendation_status', 'rejected')->count(),
        ];
        $summary['pending'] = max(0, $summary['total'] - $decisions->count());

        $pdf = Pdf::loadView('pdf.transfer-board-decision-report', [
            'board' => $board,
            'applications' => $applications,
            'decisions' => $decisions,
            'additionalMembers' => $additionalMembers,
            'summary' => $summary,
            'generatedAt' => now(),
        ]);

        return $pdf->stream('Transfer_Board_Decision_Report_' . $board->board_id . '.pdf');
    }

    protected function matchingApplications(TransferBoard $board)
    {
        $childWorkplaceIds = $board->workplace?->getAllChildWorkplaces() ?? [];
        $subjectIds = $board->subjects->pluck('subject_id')->filter()->values()->all();

        $query = TeacherTransferApplication::query()
            ->with([
                'employee',
                'currentWorkplace',
                'targetProvince',
                'teacher.appointmentSubject',
                'teacher.mainSubject',
                'teacher.secondarySubject',
                'teacher.currentTeachingSubject',
            ])
            ->where('policy_id', $board->policy_id)
            ->where('transfer_category', $board->transfer_category_id)
            ->where(fn (Builder $query) => $this->applyBoardScopeToApplicationQuery($query, $board, $childWorkplaceIds));

        if (!empty($subjectIds)) {
            $query->whereHas('teacher', function (Builder $teacherQuery) use ($subjectIds) {
                $teacherQuery->whereIn('main_subject', $subjectIds);
            });
        }

        return $query
            ->orderBy('transfer_application_id')
            ->get();
    }

    protected function applyBoardScopeToApplicationQuery(Builder $query, TransferBoard $board, array $childWorkplaceIds): Builder
    {
        if ($board->bo_office_level_id === 'OLID004') {
            return $query->where(function (Builder $scopeQuery) use ($board, $childWorkplaceIds) {
                $scopeQuery->whereHas('preferences', function (Builder $preferenceQuery) use ($board) {
                    $preferenceQuery->where('zeo_wp_id', $board->bo_workplace_id);
                })
                    ->orWhereIn('current_workplace', $childWorkplaceIds);
            });
        }

        return $query->where(function (Builder $scopeQuery) use ($board, $childWorkplaceIds) {
            $scopeQuery->where('target_province', $board->bo_workplace_id)
                ->orWhereIn('current_workplace', $childWorkplaceIds);
        });
    }

    protected function userCanAccessBoard(TransferBoard $board): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('super admin')) {
            return true;
        }

        return $user->workplace_id === $board->bo_workplace_id
            && $user->workplace?->office_level_id === $board->bo_office_level_id;
    }
}
