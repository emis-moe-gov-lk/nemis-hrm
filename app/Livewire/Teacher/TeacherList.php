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

    public function updatedSelectedProvince($value)
    {
        $this->reset(['selectedZone', 'selectedDivision', 'selectedInstitution', 'query', 'results', 'divisionOption', 'institutionOption']);
        $this->resetPage();

        if (empty($value)) {
            $this->zonalOption = [];
        } else {
            $this->zonalOption = ZonalEducationOffice::where('peo_wp_id', $value)->active()->get();
        }
    }

    public function updatedSelectedZone($value)
    {
        $this->reset(['selectedDivision', 'selectedInstitution', 'query', 'results', 'institutionOption']);
        $this->resetPage();

        if (empty($value)) {
            $this->divisionOption = [];
        } else {
            $this->divisionOption = DivisionalEducationOffice::where('zeo_wp_id', $value)->active()->get();
        }
    }

    public function updatedSelectedDivision($value)
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

    private function getAllowedWorkplaceIds()
    {
        $logged = Auth::user()->load('workplace');
        $workplace = $logged->workplace;

        if (!$workplace) {
            return [];
        }

        $allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        if (!empty($this->selectedInstitution)) {
            $inst = Workplaces::where('workplace_id', $this->selectedInstitution)->first();
            $instIds = $inst ? $inst->getAllChildWorkplaces() : [];
            $allowedWorkplaceIds = array_intersect($allowedWorkplaceIds, $instIds);
        } elseif (!empty($this->selectedDivision)) {
            $div = Workplaces::where('workplace_id', $this->selectedDivision)->first();
            $divIds = $div ? $div->getAllChildWorkplaces() : [];
            $allowedWorkplaceIds = array_intersect($allowedWorkplaceIds, $divIds);
        } elseif (!empty($this->selectedZone)) {
            $zone = Workplaces::where('workplace_id', $this->selectedZone)->first();
            $zoneIds = $zone ? $zone->getAllChildWorkplaces() : [];
            $allowedWorkplaceIds = array_intersect($allowedWorkplaceIds, $zoneIds);
        } elseif (!empty($this->selectedProvince)) {
            $peo = Workplaces::where('workplace_id', $this->selectedProvince)->first();
            $peoIds = $peo ? $peo->getAllChildWorkplaces() : [];
            $allowedWorkplaceIds = array_intersect($allowedWorkplaceIds, $peoIds);
        }

        return $allowedWorkplaceIds;
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

        // base query: restrict to teachers in allowed workplaces
        $peopleQuery = People::query()
            ->active()
            ->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                $q->where('service_id', 'SER001')
                    ->whereIn('workplace_id', $allowedWorkplaceIds);
            });

        // If input looks like a valid NIC -> do exact NIC hash lookup
        if (NicHelper::isValid($raw)) {
            $normalized = NicHelper::normalize($raw);
            $hash = NicHelper::hash($normalized);

            $peopleQuery->where('nic_hash', $hash);
        } else {
            // Otherwise search loose on contact / email / name
            $search = $raw;
            $peopleQuery->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('name_with_initials', 'like', "%{$search}%");
            });
        }

        $this->results = $peopleQuery
            ->limit(10)
            ->get();
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
        $employees = People::with([
            'currentAppointment.workplace',
            'currentAppointment.position',
            'currentAppointment.rank',
            'currentAppointment.service',
            'currentAppointment.workplace.ministry',
            'currentAppointment.workplace.provincialMinistry',
            'currentAppointment.workplace.provincial',
            'currentAppointment.workplace.zonal',
            'currentAppointment.workplace.divisional',
            'currentAppointment.workplace.institution',
        ])
            ->whereHas('currentAppointment', function ($q) use ($allowedWorkplaceIds) {
                $q->where('service_id', 'SER001')       // Teachers
                    ->whereIn('workplace_id', $allowedWorkplaceIds);
            })
            ->active()
            ->paginate(20);

        return view('livewire.teacher.teacher-list', compact('employees'));
    }
}
