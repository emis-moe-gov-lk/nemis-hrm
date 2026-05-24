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

        // Calculate Student Population for current year within this PMOE
        $this->studentCount = \App\Models\InstitutionStudentAdmission::where('academic_year', date('Y'))
            ->whereHas('class.grade', function($query) use ($id) {
                $query->whereIn('institution_id', function($subQuery) use ($id) {
                    $subQuery->select('id')->from('institutions')
                        ->whereIn('deo_wp_id', function($deoQuery) use ($id) {
                            $deoQuery->select('workplace_id')->from('divisional_education_offices')
                                ->whereIn('zeo_wp_id', function($zeoQuery) use ($id) {
                                    $zeoQuery->select('workplace_id')->from('zonal_education_offices')
                                        ->whereIn('peo_wp_id', function($peoQuery) use ($id) {
                                            $peoQuery->select('workplace_id')->from('provincial_education_offices')
                                                ->where('pmoe_wp_id', \App\Models\ProvincialMinistryOfEducationOffice::find($id)->workplace_id);
                                        });
                                });
                        });
                });
            })
            ->sum(\Illuminate\Support\Facades\DB::raw('male_count + female_count'));

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
                $q->whereIn('workplace_id', $allWorkplaceIds)
                    ->whereHas('appointment', fn($sq) => $sq->where('service_id', $service->service_id));
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
