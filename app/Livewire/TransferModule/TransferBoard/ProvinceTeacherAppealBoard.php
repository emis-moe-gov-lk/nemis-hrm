<?php

namespace App\Livewire\TransferModule\TransferBoard;

use App\Models\TransferBoard;

class ProvinceTeacherAppealBoard extends ProvinceTeacherTransferBoard
{
    protected string $boardType = TransferBoard::TYPE_APPEAL;
}
