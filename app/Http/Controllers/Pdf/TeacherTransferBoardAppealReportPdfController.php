<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\TeacherTransferAppeals;
use App\Models\TeacherTransferBoard;
use App\Models\Workplaces;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use misterspelik\LaravelPdf\Facades\Pdf;

class TeacherTransferBoardAppealReportPdfController extends Controller
{
    public function download(string $boardId)
    {
        $board = TeacherTransferBoard::with([
            'policy',
            'category',
            'transferSubCategory',
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

    protected function matchingAppeals(TeacherTransferBoard $board)
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
                    ->where('transfer_sub_category_id', $board->transfer_sub_category_id)
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

    protected function applyBoardScopeToApplicationQuery(Builder $query, TeacherTransferBoard $board, array $childWorkplaceIds): Builder
    {
        if (!empty($childWorkplaceIds)) {
            return $query->whereIn('current_workplace', $childWorkplaceIds);
        }

        return $query->where('current_workplace', $board->bo_workplace_id);
    }

    protected function userCanAccessBoard(TeacherTransferBoard $board): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('super admin')) {
            return true;
        }

        if (
            $user->workplace_id === $board->bo_workplace_id
            && $user->workplace?->office_level_id === $board->bo_office_level_id
        ) {
            return true;
        }

        return $this->canProvinceObserveZonalBoard($user->workplace, $board);
    }

    protected function canProvinceObserveZonalBoard(?Workplaces $userWorkplace, TeacherTransferBoard $board): bool
    {
        if (
            !$userWorkplace
            || $userWorkplace->office_level_id !== 'OLID003'
            || $board->bo_office_level_id !== 'OLID004'
        ) {
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

        return in_array((string) $userWorkplace->workplace_id, $parentWorkplaceIds, true);
    }
}
