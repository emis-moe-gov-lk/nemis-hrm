<?php

namespace App\Livewire\Offices\Deo\Profile;

use App\Models\Service;
use Livewire\Component;
use App\Models\Workplaces;
use App\Models\Institution;
use App\Models\DivisionalEducationOffice;
use App\Models\EmployerCurrentAppointment;

class DeoOverview extends Component
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

        // Safely load and find DEO
        $office = DivisionalEducationOffice::find($this->officeId);

        if (!$office) {
            abort(404, 'Divisional Education Office not found');
        }

        $this->workplace = $office;
        $workplaceId = $office->workplace_id;

        // Calculate Student Population for current year within this DEO
        $this->studentCount = \App\Models\InstitutionStudentAdmission::where('academic_year', date('Y'))
            ->whereHas('class.grade', function($query) use ($workplaceId) {
                $query->whereIn('institution_id', function($subQuery) use ($workplaceId) {
                    $subQuery->select('id')->from('institutions')
                        ->where('deo_wp_id', $workplaceId);
                });
            })
            ->sum(\Illuminate\Support\Facades\DB::raw('male_count + female_count'));

        // Load institutions under this DEO
        $institutions = Institution::where('deo_wp_id', $workplaceId)->get();
        $institutionIds = $institutions->pluck('workplace_id')->toArray();

        // Combine all descendant workplace IDs including the DEO itself
        $allWorkplaceIds = array_merge([$workplaceId], $institutionIds);

        // Counts
        $this->institutionCount = count($institutions);

        // Service-wise staff counts (OPTIMIZED)
        $services = Service::all();
        foreach ($services as $service) {
            $staffCount = \App\Models\People::whereHas('currentAppointment', function ($q) use ($service, $allWorkplaceIds) {
                $q->whereIn('workplace_id', $allWorkplaceIds)
                  ->whereHas('appointment', fn($sq) => $sq->where('service_id', $service->service_id));
            })->active()->count();

            // Load service details safely
            $this->serviceCounts[] = [
                'service_id' => $service->service_id,
                'name_en' => $service->service_name,
                'description' => $service->description,
                'staff_count' => $staffCount
            ];
        }
    }

    public function render()
    {
        return view('livewire.offices.deo.profile.deo-overview', [
            'officeId' => $this->officeId,
        ]);
    }
}
