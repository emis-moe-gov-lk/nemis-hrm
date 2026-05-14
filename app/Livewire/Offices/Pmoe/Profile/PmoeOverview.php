<?php

namespace App\Livewire\Offices\Pmoe\Profile;

use App\Models\Service;
use Livewire\Component;
use App\Models\Workplaces;
use App\Models\Institution;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;
use App\Models\ProvincialEducationOffice;
use App\Models\EmployerCurrentAppointment;
use App\Models\People;
use App\Models\ProvincialMinistryOfEducationOffice;

class PmoeOverview extends Component
{
    public $officeId;
    public $workplace;
    public $studentCount;
    public $provincialDeptCount;
    public $zonalOfficeCount;
    public $divisionCount;
    public $institutionCount;
    public $serviceCounts = [];
    public $ProvincialMinistry;

    public function mount($id)
    {
        $this->officeId = $id;

        $this->studentCount = 0; // Placeholder until real logic is needed

        // Optional: get the current workplace if needed in the view
        $this->ProvincialMinistry = ProvincialMinistryOfEducationOffice::find($this->officeId);

        // Get all descendant workplaces
        $peos = ProvincialEducationOffice::where('pmoe_wp_id', $this->ProvincialMinistry->workplace_id)->get();
        $peoIds = $peos->pluck('workplace_id')->toArray();

        $zeos = ZonalEducationOffice::whereIn('peo_wp_id', $peoIds)->get();
        $zeoIds = $zeos->pluck('workplace_id')->toArray();

        $deos = DivisionalEducationOffice::whereIn('zeo_wp_id', $zeoIds)->get();
        $deoIds = $deos->pluck('workplace_id')->toArray();

        $institutions = Institution::whereIn('deo_wp_id', $deoIds)->get();
        $institutionIds = $institutions->pluck('workplace_id')->toArray();

        // Combine all workplace IDs
        $allWorkplaceIds = array_merge($peoIds, $zeoIds, $deoIds, $institutionIds);

        // Counts
        $this->provincialDeptCount = count($peos);
        $this->zonalOfficeCount = count($zeos);
        $this->divisionCount = count($deos);
        $this->institutionCount = count($institutions);

        // Service-wise staff counts
        $services = Service::all();
        foreach ($services as $service) {
            $staffCount = People::whereHas('currentAppointment', function ($q) use ($service, $allWorkplaceIds) {
                $q->where('service_id', $service->service_id)
                    ->whereIn('workplace_id', $allWorkplaceIds);
            })->active()->count();

            $this->serviceCounts[] = [
                'service_id' => $service->service_id,
                'name_en' => $service->service_name,
                'staff_count' => $staffCount
            ];
        }
    }

    public function render()
    {
        return view('livewire.offices.pmoe.profile.pmoe-overview', [
            'officeId' => $this->officeId,
            'ProvincialMinistry' => $this->ProvincialMinistry
        ]);
    }
}
