<?php

namespace App\Livewire\Teacher\Transfer;

use App\Support\Transfer\TransferAccess;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TeacherTransferGuidelines extends Component
{
    public function mount(): void
    {
        abort_unless(TransferAccess::canViewTeacherSelfService(Auth::user()), 403);
    }

    public function render()
    {
        return view('livewire.teacher.transfer.teacher-transfer-guidelines');
    }
}
