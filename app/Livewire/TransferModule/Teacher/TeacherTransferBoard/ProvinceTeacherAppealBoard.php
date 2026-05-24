<?php

namespace App\Livewire\TransferModule\Teacher\TeacherTransferBoard;

use App\Models\TeacherTransferBoard;

class ProvinceTeacherAppealBoard extends ProvinceTeacherTransferBoard
{
    protected string $boardType = TeacherTransferBoard::TYPE_APPEAL;
}
