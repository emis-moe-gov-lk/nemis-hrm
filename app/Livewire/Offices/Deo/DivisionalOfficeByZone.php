<?php

namespace App\Livewire\Offices\Deo;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\DivisionalEducationOffice;
use App\Models\ZonalEducationOffice;
use App\Models\Workplaces;

class DivisionalOfficeByZone extends Component
{
    use WithPagination;

    public $id;
    public $zone;
    public $zoneWorkplace;

    public function mount($id)
    {
        // Step 1: Load the selected ZEO office
        $this->zone = ZonalEducationOffice::findOrFail($id);

        // Step 2: Get the workplace for this ZEO (critical for hierarchy)
        $this->zoneWorkplace = Workplaces::where('workplace_id', $this->zone->workplace_id)->first();

        if (!$this->zoneWorkplace) {
            abort(404, 'Workplace for this ZEO not found in workplaces table.');
        }
    }

    public function render()
    {
        // Step 3: Get ALL child workplaces under this ZEO (FAST BFS)
        $allowedWorkplaceIds = $this->zoneWorkplace->getAllChildWorkplaces();

        // Step 4: Load DEO offices whose workplace_id is in those children
        $divisionalEducationOffices = DivisionalEducationOffice::whereIn(
            'workplace_id',
            $allowedWorkplaceIds
        )->paginate(50);

        return view('livewire.offices.deo.divisional-office-by-zone', compact('divisionalEducationOffices'));
    }
}
