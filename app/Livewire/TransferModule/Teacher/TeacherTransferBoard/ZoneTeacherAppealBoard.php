<?php

namespace App\Livewire\TransferModule\Teacher\TeacherTransferBoard;

use App\Models\TeacherTransferBoard;

class ZoneTeacherAppealBoard extends ZoneTeacherTransferBoard
{
    protected string $boardType = TeacherTransferBoard::TYPE_APPEAL;
}
