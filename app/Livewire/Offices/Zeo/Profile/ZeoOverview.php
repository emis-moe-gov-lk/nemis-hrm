<?php

namespace App\Livewire\Offices\Zeo\Profile;

use Livewire\Component;
use App\Models\Service;
use App\Models\Institution;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;
use App\Models\EmployerCurrentAppointment;

class ZeoOverview extends Component
{
    public $officeId;
    public $workplace;

    public $studentCount = 0;
    public $divisionCount = 0;
    public $institutionCount = 0;

    public $serviceCounts = [];

    public function mount($id)
    {
        $this->officeId = $id;

        // Load ZEO safely
        $this->workplace = ZonalEducationOffice::findOrFail($this->officeId);

        /*
        |--------------------------------------------------------------------------
        | Collect workplace IDs
        |--------------------------------------------------------------------------
        */

        // ZEO workplace ID (single value to array)
        $zeoWorkplaceIds = [$this->workplace->workplace_id];

        // Divisional offices under this ZEO
        $deoWorkplaceIds = DivisionalEducationOffice::where(
            'zeo_wp_id',
            $this->workplace->workplace_id
        )->pluck('workplace_id')->toArray();

        // Institutions under those DEOs
        $institutionWorkplaceIds = Institution::whereIn(
            'deo_wp_id',
            $deoWorkplaceIds
        )->pluck('workplace_id')->toArray();

        // Merge all workplace IDs
        $allWorkplaceIds = array_merge(
            $zeoWorkplaceIds,
            $deoWorkplaceIds,
            $institutionWorkplaceIds
        );

        /*
        |--------------------------------------------------------------------------
        | Counts
        |--------------------------------------------------------------------------
        */
        $this->divisionCount    = count($deoWorkplaceIds);
        $this->institutionCount = count($institutionWorkplaceIds);

        /*
        |--------------------------------------------------------------------------
        | Service-wise staff counts (OPTIMIZED)
        |--------------------------------------------------------------------------
        */
        $this->serviceCounts = Service::select('service_id', 'service_name', 'description')
            ->get()
            ->map(function ($service) use ($allWorkplaceIds) {

                $staffCount = \App\Models\People::whereHas('currentAppointment', function ($q) use ($service, $allWorkplaceIds) {
                    $q->where('service_id', $service->service_id)
                      ->whereIn('workplace_id', $allWorkplaceIds);
                })->active()->count();

                return [
                    'service_id'  => $service->service_id,
                    'name'        => $service->service_name,
                    'description' => $service->description,
                    'staff_count' => $staffCount,
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.offices.zeo.profile.zeo-overview', [
            'officeId' => $this->officeId,
        ]);
    }
}
