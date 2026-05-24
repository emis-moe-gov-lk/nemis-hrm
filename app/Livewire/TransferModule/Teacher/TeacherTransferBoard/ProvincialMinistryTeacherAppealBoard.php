<?php

namespace App\Livewire\TransferModule\Teacher\TeacherTransferBoard;

use App\Models\TeacherTransferBoard;

class ProvincialMinistryTeacherAppealBoard extends ProvincialMinistryTeacherTransferBoard
{
    protected string $boardType = TeacherTransferBoard::TYPE_APPEAL;
}
