<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\TeacherTransferBoard;
use App\Models\Workplaces;
use App\Services\TransferModule\TeacherTransferBoardSchoolBalanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use misterspelik\LaravelPdf\Facades\Pdf;

class TeacherTransferBoardSchoolBalanceReportPdfController extends Controller
{
    public function download(string $boardId, string $type, TeacherTransferBoardSchoolBalanceService $schoolBalanceService)
    {
        abort_unless(in_array($type, ['needed', 'excess'], true), 404);

        $board = TeacherTransferBoard::with([
            'policy',
            'category',
            'subjects',
            'workplace',
            'officeLevel',
            'chairman',
            'secretary',
        ])
            ->transfer()
            ->where('board_id', $boardId)
            ->firstOrFail();

        abort_unless($this->userCanAccessBoard($board), 403);

        $summary = $schoolBalanceService->summary($board);
        $rows = $type === 'needed' ? $summary['needed'] : $summary['excess'];

        $pdf = Pdf::loadView('pdf.transfer-board-school-balance-report', [
            'board' => $board,
            'type' => $type,
            'rows' => $rows,
            'summary' => $summary,
            'generatedAt' => now(),
        ], [], [
            'format' => 'A4-L',
        ]);

        return $pdf->stream(Str::headline($type) . '_School_List_' . $board->board_id . '.pdf');
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
