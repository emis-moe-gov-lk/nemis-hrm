<?php

namespace App\Livewire\Offices\Moe;

use Livewire\Component;
use App\Models\MinistryOfEducationOffice;
use Illuminate\Support\Facades\Auth;
use App\Models\Workplaces;

class MoeOfficesList extends Component
{
    public function render()
    {
        $user = Auth::user()->load('workplace.officeLevel');

        $workplace = $user->workplace;  
        $officeLevel = $workplace?->office_level_id;

        if (!$officeLevel) {
            abort(403, 'No office level assigned to your workplace.');
        }

        // Step 1: All workplaces under same office level
        $workplaceIds = Workplaces::where('office_level_id', $officeLevel)
            ->pluck('workplace_id');

        // Step 2: Get all Ministry of Education offices for those workplaces
        $educationMinistries = MinistryOfEducationOffice::whereIn('workplace_id', $workplaceIds)
            ->paginate(50);

        return view('livewire.offices.moe.moe-offices-list', compact('educationMinistries'));
    }

}
