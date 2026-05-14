<?php

namespace App\Livewire\Offices\Zeo;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\ZonalEducationOffice;

class ZeoOfficesList extends Component
{
    use WithPagination;

    public function render()
    {
        // Load logged-in user + workplace + office level
        $user = Auth::user()->load('workplace.officeLevel');

        $workplace = $user->workplace;

        if (!$workplace) {
            abort(403, 'You do not have a registered workplace.');
        }

        // Get hierarchy child workplaces (FAST BFS)
        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        // Get ONLY zonal education offices (ZEO)
        $zonalEducationOffices = ZonalEducationOffice::whereIn(
            'workplace_id',
            $allowedWorkplaceIds
        )->paginate(50);

        return view(
            'livewire.offices.zeo.zeo-offices-list',
            compact('zonalEducationOffices')
        );
    }
}
