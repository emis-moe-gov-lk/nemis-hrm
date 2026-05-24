<?php

namespace App\Livewire\Teacher;

use App\Models\People;
use Livewire\Component;
use App\Models\Workplaces;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NicHelper;
use App\Models\ProvincialEducationOffice;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;
use App\Models\Institution;

class TeacherList extends Component
{
    use WithPagination;

    public $query = '';
    public $results = [];

    public $selectedProvince = '';
    public $selectedZone = '';
    public $selectedDivision = '';
    public $selectedInstitution = '';

    private ?array $allowedWorkplaceIdsCache = null;

    public $isProvinceLocked = false;
    public $isZoneLocked = false;
    public $isDivisionLocked = false;
    public $isInstitutionLocked = false;

    public $provinceOption = [];
    public $zonalOption = [];
    public $divisionOption = [];
    public $institutionOption = [];

    public function mount()
    {
        $this->provinceOption = ProvincialEducationOffice::active()->get();

        $user = Auth::user();
        if ($user && $user->workplace) {
            $parents = Workplaces::whereIn('workplace_id', $user->workplace->getAllParentWorkplaces())->get()->keyBy('office_level_id');

            if ($parents->has('OLID003')) {
                $this->selectedProvince = $parents['OLID003']->workplace_id;
                $this->isProvinceLocked = true;
                $this->updatedSelectedProvince($this->selectedProvince);
            }
            if ($parents->has('OLID004')) {
                $this->selectedZone = $parents['OLID004']->workplace_id;
                $this->isZoneLocked = true;
                $this->updatedSelectedZone($this->selectedZone);
            }
            if ($parents->has('OLID005')) {
                $this->selectedDivision = $parents['OLID005']->workplace_id;
                $this->isDivisionLocked = true;
                $this->updatedSelectedDivision($this->selectedDivision);
            }
            if ($parents->has('OLID006')) {
                $this->selectedInstitution = $parents['OLID006']->workplace_id;
                $this->isInstitutionLocked = true;
            }
        }
    }

    public function updatedSelectedProvince(?string $value)
    {
        $this->reset(['selectedZone', 'selectedDivision', 'selectedInstitution', 'query', 'results', 'divisionOption', 'institutionOption']);
        $this->resetPage();

        if (empty($value)) {
            $this->zonalOption = [];
        } else {
            $this->zonalOption = ZonalEducationOffice::where('peo_wp_id', $value)->active()->get();
        }
    }

    public function updatedSelectedZone(?string $value)
    {
        $this->reset(['selectedDivision', 'selectedInstitution', 'query', 'results', 'institutionOption']);
        $this->resetPage();

        if (empty($value)) {
            $this->divisionOption = [];
        } else {
            $this->divisionOption = DivisionalEducationOffice::where('zeo_wp_id', $value)->active()->get();
        }
    }

    public function updatedSelectedDivision(?string $value)
    {
        $this->reset(['selectedInstitution', 'query', 'results']);
        $this->resetPage();

        if (empty($value)) {
            $this->institutionOption = [];
        } else {
            $this->institutionOption = Institution::where('deo_wp_id', $value)->active()->get();
        }
    }

    public function updatedSelectedInstitution()
    {
        $this->resetPage();
        $this->reset(['query', 'results']);
    }

    private function getAllowedWorkplaceIds(): array
    {
        if ($this->allowedWorkplaceIdsCache !== null) {
            return $this->allowedWorkplaceIdsCache;
        }

        $logged = Auth::user()->load('workplace');
        $workplace = $logged->workplace;

        if (!$workplace) {
            return $this->allowedWorkplaceIdsCache = [];
        }

        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        if (!empty($this->selectedInstitution)) {
            $inst = Workplaces::find($this->selectedInstitution);
            $instIds = $inst ? $inst->getAllChildWorkplaces() : [];
            $allowedWorkplaceIds = array_values(array_intersect($allowedWorkplaceIds, $instIds));
        } elseif (!empty($this->selectedDivision)) {
            $div = Workplaces::find($this->selectedDivision);
            $divIds = $div ? $div->getAllChildWorkplaces() : [];
            $allowedWorkplaceIds = array_values(array_intersect($allowedWorkplaceIds, $divIds));
        } elseif (!empty($this->selectedZone)) {
            $zone = Workplaces::find($this->selectedZone);
            $zoneIds = $zone ? $zone->getAllChildWorkplaces() : [];
            $allowedWorkplaceIds = array_values(array_intersect($allowedWorkplaceIds, $zoneIds));
        } elseif (!empty($this->selectedProvince)) {
            $peo = Workplaces::find($this->selectedProvince);
            $peoIds = $peo ? $peo->getAllChildWorkplaces() : [];
            $allowedWorkplaceIds = array_values(array_intersect($allowedWorkplaceIds, $peoIds));
        }

        return $this->allowedWorkplaceIdsCache = $allowedWorkplaceIds;
    }

    private function getTeacherBaseQuery(array $allowedWorkplaceIds)
    {
        return People::query()
            ->select('people.*')
            ->join('employer_appointments', 'employer_appointments.employee_id', '=', 'people.people_id')
            ->join('employer_current_appointments', 'employer_current_appointments.employee_id', '=', 'people.people_id')
            ->where('employer_appointments.service_id', 'SER001')
            ->whereIn('employer_current_appointments.workplace_id', $allowedWorkplaceIds)
            ->where('people.active_status', 1)
            ->distinct('people.people_id');
    }

    public function updatedQuery()
    {
        $raw = trim($this->query);

        // empty or too short -> no results (adjust min length if you want)
        if ($raw === '' || strlen($raw) < 3) {
            $this->results = [];
            return;
        }

        $allowedWorkplaceIds = $this->getAllowedWorkplaceIds();

        if (empty($allowedWorkplaceIds)) {
            $this->results = [];
            return;
        }

        $peopleQuery = $this->getTeacherBaseQuery($allowedWorkplaceIds)->take(10);

        if (NicHelper::isValid($raw)) {
            $normalized = NicHelper::normalize($raw);
            $hash = NicHelper::hash($normalized);
            $peopleQuery->where('people.nic_hash', $hash);
        } else {
            $search = $raw;
            $peopleQuery->where(function ($q) use ($search) {
                $q->where('people.phone', 'like', "%{$search}%")
                    ->orWhere('people.email', 'like', "%{$search}%")
                    ->orWhere('people.full_name', 'like', "%{$search}%")
                    ->orWhere('people.name_with_initials', 'like', "%{$search}%");
            });
        }

        $this->results = $peopleQuery->get();
    }

    public function render()
    {
        // Allowed workplaces based on hierarchy and filters
        $allowedWorkplaceIds = $this->getAllowedWorkplaceIds();

        if (empty($allowedWorkplaceIds)) {
            $employees = People::where('id', 0)->paginate(10);
            return view('livewire.teacher.teacher-list', compact('employees'));
        }

        // Main teacher query
        $employees = $this->getTeacherBaseQuery($allowedWorkplaceIds)
            ->with([
                'appointment.service',
                'currentAppointment.workplace',
                'currentAppointment.position',
                'currentAppointment.rank',
                'currentAppointment.workplace.ministry',
                'currentAppointment.workplace.provincialMinistry',
                'currentAppointment.workplace.provincial',
                'currentAppointment.workplace.zonal',
                'currentAppointment.workplace.divisional',
                'currentAppointment.workplace.institution',
            ])
            ->paginate(20);

        return view('livewire.teacher.teacher-list', compact('employees'));
    }
}
