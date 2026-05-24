<?php

namespace App\Livewire\Teacher\Transfer;

use App\Support\Transfer\TransferAccess;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class TeacherMutualTransfer extends Component
{
    public function mount(): void
    {
        abort_unless(TransferAccess::canViewTeacherSelfService(Auth::user()) && Gate::allows('transfer.mutual.view'), 403);
    }

    public function render()
    {
        return view('livewire.teacher.transfer.teacher-mutual-transfer');
    }
}
