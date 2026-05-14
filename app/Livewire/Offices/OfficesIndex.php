<?php

namespace App\Livewire\Offices;

use App\Models\OfficeLevel;
use Livewire\Component;

class OfficesIndex extends Component
{
    public function render()
    {
        // Fetch all office levels with workplace counts
        $officeHierarchy = OfficeLevel::withCount('workplaces')
            ->orderBy('office_level_rank')
            ->get();

        return view('livewire.offices.offices-index', compact('officeHierarchy'));
    }
}
