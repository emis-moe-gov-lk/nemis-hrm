<?php

namespace App\Livewire\Offices\Deo;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\DivisionalEducationOffice;

class DeoOfficesList extends Component
{
    use WithPagination;

    public function render()
    {
        // Load user with workplace + office level
        $user = Auth::user()->load('workplace.officeLevel');

        $workplace = $user->workplace;

        if (!$workplace) {
            abort(403, 'You do not have a registered workplace.');
        }

        // Step 1: Get all workplaces the user has access to (FAST BFS)
        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        // Step 2: Get ONLY DEO under these workplaces
        $divisionalEducationOffices = DivisionalEducationOffice::whereIn(
            'workplace_id',
            $allowedWorkplaceIds
        )->paginate(50);

        return view(
            'livewire.offices.deo.deo-offices-list',
            compact('divisionalEducationOffices')
        );
    }
}
