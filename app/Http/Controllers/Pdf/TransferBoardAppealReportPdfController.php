<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\TeacherTransferAppeals;
use App\Models\TransferBoard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use misterspelik\LaravelPdf\Facades\Pdf;

class TransferBoardAppealReportPdfController extends Controller
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
            ->appeal()
            ->where('board_id', $boardId)
            ->firstOrFail();

        abort_unless($this->userCanAccessBoard($board), 403);

        abort_unless($board->isClosed(), 409, 'Appeal report is available only after closing the appeal board.');

        $appeals = $this->matchingAppeals($board);

        $additionalMembers = $board->members
            ->filter(fn ($member) => $member->active_status && strtolower((string) $member->role) === 'member')
            ->values();

        $summary = [
            'total' => $appeals->count(),
            'approved' => $appeals->where('appeal_status', TeacherTransferAppeals::STATUS_APPROVED)->count(),
            'rejected' => $appeals->where('appeal_status', TeacherTransferAppeals::STATUS_REJECTED)->count(),
        ];
        $summary['pending'] = $appeals->where('appeal_status', TeacherTransferAppeals::STATUS_PENDING)->count();

        $pdf = Pdf::loadView('pdf.transfer-board-appeal-report', [
            'board' => $board,
            'appeals' => $appeals,
            'additionalMembers' => $additionalMembers,
            'summary' => $summary,
            'generatedAt' => now(),
        ]);

        return $pdf->stream('Appeal_Board_Report_' . $board->board_id . '.pdf');
    }

    protected function matchingAppeals(TransferBoard $board)
    {
        $childWorkplaceIds = $board->workplace?->getAllChildWorkplaces() ?? [];
        $subjectIds = $board->subjects->pluck('subject_id')->filter()->values()->all();

        $query = TeacherTransferAppeals::query()
            ->with([
                'board',
                'selectedZone',
                'selectedSchool',
                'application.employee',
                'application.currentWorkplace',
                'application.targetProvince',
                'application.category',
                'application.teacher.appointmentSubject',
                'application.teacher.mainSubject',
                'application.teacher.secondarySubject',
                'application.teacher.currentTeachingSubject',
                'application.boardRecommendation.recommendationList',
                'application.boardRecommendation.selectedZone',
                'application.boardRecommendation.selectedSchool',
            ])
            ->where('policy_id', $board->policy_id)
            ->whereHas('application', function (Builder $query) use ($board, $childWorkplaceIds, $subjectIds) {
                $query->where('transfer_category', $board->transfer_category_id)
                    ->where(fn (Builder $scopeQuery) => $this->applyBoardScopeToApplicationQuery($scopeQuery, $board, $childWorkplaceIds));

                if (!empty($subjectIds)) {
                    $query->whereHas('teacher', function (Builder $teacherQuery) use ($subjectIds) {
                        $teacherQuery->whereIn('main_subject', $subjectIds);
                    });
                }
            });

        return $query
            ->orderBy('number_of_appeals')
            ->orderBy('appeal_id')
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
