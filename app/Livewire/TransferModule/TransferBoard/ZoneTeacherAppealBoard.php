<?php

namespace App\Livewire\TransferModule\TransferBoard;

use App\Models\TransferBoard;

class ZoneTeacherAppealBoard extends ZoneTeacherTransferBoard
{
    protected string $boardType = TransferBoard::TYPE_APPEAL;
}
