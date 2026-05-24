<?php

namespace App\Livewire\Teacher\Transfer;

use App\Support\Transfer\TransferAccess;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TeacherTransferPotal extends Component
{
    public function mount(): void
    {
        abort_unless(TransferAccess::canViewTeacherSelfService(Auth::user()), 403);
    }

    public function render()
    {
        $announcements = \App\Models\TransferAnnouncement::where('is_active', true)
            ->latest()
            ->get();

        return view('livewire.teacher.transfer.teacher-transfer-potal', [
            'announcements' => $announcements,
        ]);
    }
}
