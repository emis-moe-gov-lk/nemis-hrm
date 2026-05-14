<?php

namespace App\Livewire\Offices\Peo;

use Livewire\Component;
use App\Models\ProvincialEducationOffice;
use Illuminate\Support\Facades\Auth;

class PeoOfficesList extends Component
{
    public function render()
    {
        // Load logged user with workplace + officeLevel
        $user = Auth::user()->load('workplace.officeLevel');

        $workplace = $user->workplace;

        if (!$workplace) {
            abort(403, 'No workplace assigned to your account.');
        }

        // FAST hierarchy: get all workplaces user can access
        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        // Show only Provincial Education Offices within allowed workplaces
        $provincialEducationOffices =
            ProvincialEducationOffice::whereIn('workplace_id', $allowedWorkplaceIds)
            ->paginate(50);

        return view(
            'livewire.offices.peo.peo-offices-list',
            compact('provincialEducationOffices')
        );
    }
}
