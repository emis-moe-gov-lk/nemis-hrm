<?php

namespace App\Livewire\Offices\Institutions;

use App\Models\DivisionalEducationOffice;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Institution;
use App\Models\Workplaces;

class InstitutionsListByDeo extends Component
{
    use WithPagination;

    public $id;
    public $division;
    public $divisionWorkplace;

    public function mount($id)
    {
        $this->id = $id;

        // Step 1: Load selected DEO
        $this->division = DivisionalEducationOffice::findOrFail($id);

        // Step 2: Resolve DEO ↔ workplace
        $this->divisionWorkplace = Workplaces::where('workplace_id', $this->division->workplace_id)->first();

        if (!$this->divisionWorkplace) {
            abort(404, 'Workplace for this DEO not found in workplaces table.');
        }
    }

    public function render()
    {
        // Step 3: All workplaces under this DEO (FAST BFS)
        $allowedWorkplaceIds = $this->divisionWorkplace->getAllChildWorkplaces();

        // Step 4: Load only schools (Institutions)
        $institutions = Institution::whereIn('workplace_id', $allowedWorkplaceIds)
            ->active()
            ->paginate(20);

        return view(
            'livewire.offices.institutions.institutions-list-by-deo',
            compact('institutions')
        );
    }
}
