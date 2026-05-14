<?php

namespace App\Livewire\Offices\Pmoe;

use Livewire\Component;
use App\Models\ProvincialMinistryOfEducationOffice;
use Illuminate\Support\Facades\Auth;

class PmoeOfficesList extends Component
{
    public function render()
    {
        $user = Auth::user()->load('workplace.officeLevel');

        $workplace = $user->workplace;

        if (!$workplace) {
            abort(403, 'No workplace assigned to your account.');
        }

        // IMPORTANT: Fetch ALL workplaces user is allowed to access
        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        // Now load offices WHERE workplace_id is in allowed list
        $provincialEducationMinistries =
            ProvincialMinistryOfEducationOffice::whereIn('workplace_id', $allowedWorkplaceIds)
            ->paginate(50);

        return view('livewire.offices.pmoe.pmoe-offices-list',
            compact('provincialEducationMinistries')
        );
    }
}
