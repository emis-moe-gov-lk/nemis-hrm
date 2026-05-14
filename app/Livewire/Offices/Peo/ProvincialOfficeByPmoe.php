<?php

namespace App\Livewire\Offices\Peo;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProvincialEducationOffice;
use App\Models\ProvincialMinistryOfEducationOffice;
use App\Models\Workplaces;

class ProvincialOfficeByPmoe extends Component
{
    use WithPagination;

    public $id;
    public $pmoe;
    public $pmoeWorkplace;

    public function mount($id)
    {
        // Load PMOE office
        $this->pmoe = ProvincialMinistryOfEducationOffice::findOrFail($id);

        // --- IMPORTANT ---
        // Always resolve workplace via manual query,
        // because some PMOE models do not have correct "workplace()" relationship defined.
        $this->pmoeWorkplace = Workplaces::where('workplace_id', $this->pmoe->workplace_id)->first();

        if (!$this->pmoeWorkplace) {
            abort(404, 'Workplace for this PMOE office does not exist in workplaces table.');
        }
    }

    public function render()
    {
        //dd($this->pmoeWorkplace->office()->name);
        // STEP 1: Get ALL allowed workplaces under this Provincial Ministry
        // This uses optimized BFS (no recursion!)
        $allowedWorkplaceIds = $this->pmoeWorkplace->getAllChildWorkplaces();

        // STEP 2: Filter ONLY Provincial Education Offices (PEO)
        // that belong to these workplaces
        $provincialEducationOffices = ProvincialEducationOffice::whereIn(
            'workplace_id',
            $allowedWorkplaceIds
        )->paginate(50);

        return view('livewire.offices.peo.provincial-office-by-pmoe', [
            'provincialEducationOffices' => $provincialEducationOffices,
        ]);
    }
}
