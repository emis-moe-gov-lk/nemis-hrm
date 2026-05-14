<?php

namespace App\Livewire\Offices\Zeo;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ZonalEducationOffice;
use App\Models\ProvincialEducationOffice;
use App\Models\Workplaces;

class ZonaleOfficeByPeo extends Component
{
    use WithPagination;

    public $id;
    public $province;
    public $provinceWorkplace;

    public function mount($id)
    {
        // Load the PEO
        $this->province = ProvincialEducationOffice::findOrFail($id);

        // Resolve workplace (do NOT rely on model relationship names)
        $this->provinceWorkplace = Workplaces::where('workplace_id', $this->province->workplace_id)->first();

        if (!$this->provinceWorkplace) {
            abort(404, 'Workplace for this PEO not found in workplaces table.');
        }
    }

    public function render()
    {
        // STEP 1: Get all child workplaces under this PEO (fast BFS)
        $allowedWorkplaceIds = $this->provinceWorkplace->getAllChildWorkplaces();

        // STEP 2: Get ONLY the zonal offices under those workplaces
        $zonalEducationOffices = ZonalEducationOffice::whereIn(
            'workplace_id',
            $allowedWorkplaceIds
        )->paginate(50);

        return view('livewire.offices.zeo.zonale-office-by-peo', [
            'zonalEducationOffices' => $zonalEducationOffices,
        ]);
    }
}
