<?php

namespace App\Livewire\Offices\Moe\Profile;

use App\Models\Service;
use Livewire\Component;
use App\Models\Workplaces;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;
use App\Models\MinistryOfEducationOffice;
use App\Models\ProvincialEducationOffice;
use App\Models\EmployerCurrentAppointment;
use App\Models\ProvincialMinistryOfEducationOffice;

class MoeOverview extends Component
{
    public $officeId;
    public $studentCount;
    public $provincialMinistryCount;
    public $provincialDeptCount;
    public $zonalOfficeCount;
    public $divisionCount;
    public $serviceCounts = [];
    public $EducationMinistry;

    public function mount($id)
    {
        $this->officeId = $id;

        // Dummy data (replace with actual relationships if needed)
        // Calculate Nationwide Student Population for current year
        $this->studentCount = \App\Models\InstitutionStudentAdmission::where('academic_year', date('Y'))
            ->sum(\Illuminate\Support\Facades\DB::raw('male_count + female_count'));
        $this->provincialMinistryCount = ProvincialMinistryOfEducationOffice::count();
        $this->provincialDeptCount = ProvincialEducationOffice::count();
        $this->zonalOfficeCount = ZonalEducationOffice::count();
        $this->divisionCount = DivisionalEducationOffice::count();

        // Count staff per service
        $this->serviceCounts = Service::all()->map(function ($service) {
            $count = \App\Models\People::whereHas('currentAppointment', function ($q) use ($service) {
                $q->whereNotNull('workplace_id')
                  ->whereHas('appointment', fn($sq) => $sq->where('service_id', $service->service_id));
            })->active()->count();

            return (object)[
                'service_name' => $service->service_name,
                'staff_count' => $count,
            ];
        });
    }

    public function render()
    {
        $this->EducationMinistry = MinistryOfEducationOffice::find($this->officeId);

        return view('livewire.offices.moe.profile.moe-overview', [
            'officeId' => $this->officeId,
            'serviceCounts' => $this->serviceCounts,
            'EducationMinistry' => $this->EducationMinistry,
        ]);
    }
}
