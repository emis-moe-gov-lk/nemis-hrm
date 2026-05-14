<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\TransferBoard;
use App\Services\TransferModule\TransferBoardSchoolBalanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use misterspelik\LaravelPdf\Facades\Pdf;

class TransferBoardSchoolBalanceReportPdfController extends Controller
{
    public function download(string $boardId, string $type, TransferBoardSchoolBalanceService $schoolBalanceService)
    {
        abort_unless(in_array($type, ['needed', 'excess'], true), 404);

        $board = TransferBoard::with([
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
