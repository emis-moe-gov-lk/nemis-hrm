<?php

namespace App\Livewire\TransferModule;

use Livewire\Component;

use App\Models\TransferPolicy;

use App\Models\TeacherTransferApplication;

class IndexModule extends Component
{
    public function render()
    {
        $stats = [
            'total_policies' => TransferPolicy::count(),
            'active_policies' => TransferPolicy::active()->count(),
            'locked_policies' => TransferPolicy::where('is_locked', true)->count(),
            'total_applications' => TeacherTransferApplication::count(),
            'pending_applications' => TeacherTransferApplication::whereIn('status', ['submitted', 'processing'])->count(),
        ];

        return view('livewire.transfer-module.index-module', [
            'stats' => $stats
        ]);
    }
}
