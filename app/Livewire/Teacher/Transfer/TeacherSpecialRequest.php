<?php

namespace App\Livewire\Teacher\Transfer;

use App\Support\Transfer\TransferAccess;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class TeacherSpecialRequest extends Component
{
    public function mount(): void
    {
        abort_unless(TransferAccess::canViewTeacherSelfService(Auth::user()) && Gate::allows('transfer.special.view'), 403);
    }

    public function render()
    {
        return view('livewire.teacher.transfer.teacher-special-request');
    }
}
