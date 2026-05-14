<?php

namespace App\Livewire\Offices\Peo\Profile;

use Livewire\Component;
use App\Models\Service;
use App\Models\Institution;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;
use App\Models\ProvincialEducationOffice;
use App\Models\EmployerCurrentAppointment;

class PeoOverview extends Component
{
    public int|string $officeId;

    public int $studentCount = 0;
    public int $provincialDeptCount = 0;
    public int $zonalOfficeCount = 0;
    public int $divisionCount = 0;
    public int $institutionCount = 0;

    public array $serviceCounts = [];

    public function mount($id): void
    {
        $this->officeId = $id;

        /** -----------------------------------------
         *  Validate Provincial Education Office
         * ----------------------------------------*/
        $peo = ProvincialEducationOffice::find($this->officeId);

        if (!$peo) {
            // Fail safely (optional: redirect instead)
            abort(404, 'Provincial Education Office not found');
        }

        /** -----------------------------------------
         *  Collect workplace IDs (Hierarchy)
         * ----------------------------------------*/
        $zeoIds = ZonalEducationOffice::where('peo_wp_id', $peo->workplace_id)
            ->pluck('workplace_id')
            ->toArray();

        $deoIds = empty($zeoIds)
            ? []
            : DivisionalEducationOffice::whereIn('zeo_wp_id', $zeoIds)
                ->pluck('workplace_id')
                ->toArray();

        $institutionIds = empty($deoIds)
            ? []
            : Institution::whereIn('deo_wp_id', $deoIds)
                ->pluck('workplace_id')
                ->toArray();

        /** -----------------------------------------
         *  Merge all workplaces for staff lookup
         * ----------------------------------------*/
        $allWorkplaceIds = array_merge($zeoIds, $deoIds, $institutionIds);

        /** -----------------------------------------
         *  Counts
         * ----------------------------------------*/
        $this->zonalOfficeCount = count($zeoIds);
        $this->divisionCount    = count($deoIds);
        $this->institutionCount = count($institutionIds);

        /** -----------------------------------------
         *  Service-wise staff count
         * ----------------------------------------*/
        if (!empty($allWorkplaceIds)) {
            Service::select('service_id', 'service_name')
                // ->orderBy('service_name')
                ->each(function ($service) use ($allWorkplaceIds) {
                    $staffCount = \App\Models\People::whereHas('currentAppointment', function ($q) use ($service, $allWorkplaceIds) {
                        $q->where('service_id', $service->service_id)
                          ->whereIn('workplace_id', $allWorkplaceIds);
                    })->active()->count();

                    $this->serviceCounts[] = [
                        'service_id'  => $service->service_id,
                        'name_en'     => $service->service_name,
                        'staff_count'=> $staffCount,
                    ];
                });
        }
    }

    public function render()
    {
        return view('livewire.offices.peo.profile.peo-overview', [
            'officeId' => $this->officeId,
        ]);
    }
}
