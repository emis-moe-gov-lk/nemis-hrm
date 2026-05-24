<?php

namespace App\Livewire\Employees;

use App\Models\EmployerAttachmentAppointment;
use App\Models\EmployerAppointment;
use App\Models\People;
use App\Models\Workplaces;
use App\Models\Position;
use App\Models\OfficeLevel;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NicHelper;
use Illuminate\Support\Facades\Cache;
use App\Models\Service;
use App\Models\ZonalEducationOffice;
use App\Models\InstitutionCategory;
use App\Models\Institution;

use Livewire\Component;

class EmployeeAttachments extends Component
{
    public ?string $serviceID;

    public array $allowedWorkplaceIds = [];
    public ?Service $services = null;
    
    public $search = '';

    // Attach Employee Properties
    public $searchNic = '';
    public $attachEmployeeName = '';
    public $attachEmployeeId = null;
    public $attachAppointmentId = null;

    // Cascading Workplace Selector
    public ?string $officeLevel = null;
    public ?string $zonalEducationOffice = null;
    public ?string $institutionCategory = null;
    public ?string $workingPlace = null;

    public $officeLevelOption = [];
    public $zonalEducationOfficeOption = [];
    public $institutionCategoryOption = [];
    public $workingPlaceOption = [];

    public $attachPositionId = '';
    public $attachStartDate = '';
    public $attachEndDate = '';

    public function mount(?string $serviceID = null)
    {
        $this->serviceID = $serviceID;
        $this->services = Service::where('service_id', $serviceID)->active()->first();

        /** @var \App\Models\User $logged */
        $logged = Auth::user();
        $workplace = $logged ? $logged->workplace : null;

        if (!$workplace) {
            abort(403, 'No workplace assigned to the logged-in user. You do not have permission to access this page.');
        }

        $this->allowedWorkplaceIds = $workplace->getAllChildWorkplaces();

        $this->officeLevelOption = OfficeLevel::active()->orderBy('office_level_rank')->get();
        $this->zonalEducationOfficeOption = ZonalEducationOffice::active()->orderBy('short_name')->get();
        $this->institutionCategoryOption = InstitutionCategory::active()->orderBy('institution_category_name')->get();
        $this->workingPlaceOption = collect();
    }

    public function searchEmployee()
    {
        $this->resetErrorBag('searchNic');

        if (empty($this->searchNic)) {
            $this->addError('searchNic', 'Please enter a NIC number.');
            return;
        }

        $nicHash = NicHelper::hash($this->searchNic);
        $person = People::where('nic_hash', $nicHash)->first();
        if (!$person) {
            $this->addError('searchNic', 'Employee not found with this NIC.');
            $this->reset(['attachEmployeeName', 'attachEmployeeId', 'attachAppointmentId']);
            return;
        }

        $appointment = EmployerAppointment::where('employee_id', $person->people_id)
            ->where('active_status', 1)
            ->first();

        if (!$appointment) {
            $this->addError('searchNic', 'No active appointment found for this employee.');
            $this->reset(['attachEmployeeName', 'attachEmployeeId', 'attachAppointmentId']);
            return;
        }

        if (!in_array($appointment->workplace_id, $this->allowedWorkplaceIds)) {
            $this->addError('searchNic', 'This employee is not within your allowed workplaces.');
            $this->reset(['attachEmployeeName', 'attachEmployeeId', 'attachAppointmentId']);
            return;
        }

        $this->attachEmployeeId = $person->people_id;
        $this->attachAppointmentId = $appointment->appointment_id;
        $this->attachEmployeeName = $person->name_with_initials;
    }

    public function updatedOfficeLevel(): void
    {
        $this->zonalEducationOffice = null;
        $this->institutionCategory  = null;
        $this->workingPlace         = null;
        $this->refreshWorkplacesOptions();
    }

    public function updatedZonalEducationOffice(): void
    {
        $this->institutionCategory = null;
        $this->workingPlace        = null;
        $this->refreshWorkplacesOptions();
    }

    public function updatedInstitutionCategory(): void
    {
        $this->workingPlace = null;
        $this->refreshWorkplacesOptions();
    }

    protected function refreshWorkplacesOptions(): void
    {
        if (!$this->officeLevel) {
            $this->workingPlaceOption = collect();
            return;
        }

        $ids = $this->allowedWorkplaceIds;

        if ($this->officeLevel === 'OLID006') {
            if ($this->zonalEducationOffice && $this->institutionCategory) {
                $this->workingPlaceOption = Institution::whereIn('workplace_id', $ids)
                    ->where('zeo_wp_id', $this->zonalEducationOffice)
                    ->where('institution_category_id', $this->institutionCategory)
                    ->orderBy('name', 'asc')
                    ->get()
                    ->map(function ($item) {
                        return (object) [
                            'workplace_id' => $item->workplace_id,
                            'office_name' => $item->census_no . ' - ' . $item->name
                        ];
                    });
            } else {
                $this->workingPlaceOption = collect();
            }
        } else {
            $this->workingPlaceOption = Workplaces::whereIn('workplace_id', $ids)
                ->where('office_level_id', $this->officeLevel)
                ->with(['zonal', 'divisional'])
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'workplace_id' => $item->workplace_id,
                        'office_name' => $item->office_name
                    ];
                })
                ->sortBy('office_name')
                ->values();
        }
    }

    public function openAttachModal()
    {
        $this->resetAttachForm();
        $this->dispatch('modal-show', name: 'attach-employee-modal');
    }

    public function attachEmployee()
    {
        $this->validate([
            'attachEmployeeId' => 'required',
            'attachAppointmentId' => 'required',
            'workingPlace' => 'required',
            'officeLevel' => 'required',
            'attachPositionId' => 'required',
            'attachStartDate' => 'required|date',
            'attachEndDate' => 'nullable|date|after_or_equal:attachStartDate',
        ]);

        EmployerAttachmentAppointment::create([
            'employee_id' => $this->attachEmployeeId,
            'appointment_id' => $this->attachAppointmentId,
            'workplace_id' => $this->workingPlace,
            'position_id' => $this->attachPositionId,
            'office_level_id' => $this->officeLevel,
            'appoint_date' => $this->attachStartDate,
            'end_date' => $this->attachEndDate ?: null,
            'active_status' => 1,
            'created_by' => Auth::id(),
        ]);

        $this->resetAttachForm();
        $this->dispatch('modal-close', name: 'attach-employee-modal');
        $this->dispatch('notify', ['message' => 'Employee attached successfully', 'type' => 'success']);
    }

    public function resetAttachForm()
    {
        $this->reset([
            'searchNic',
            'attachEmployeeName',
            'attachEmployeeId',
            'attachAppointmentId',
            'officeLevel',
            'zonalEducationOffice',
            'institutionCategory',
            'workingPlace',
            'attachPositionId',
            'attachStartDate',
            'attachEndDate'
        ]);
        $this->workingPlaceOption = collect();
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = EmployerAttachmentAppointment::with(['workplace', 'position', 'officeLevel', 'employee'])
            ->whereHas('appointment', function ($q) {
                if (!empty($this->serviceID)) {
                    $q->where('service_id', $this->serviceID);
                }
            })
            ->orderBy('appoint_date', 'desc');

        if (!empty($this->allowedWorkplaceIds)) {
            $query->whereIn('workplace_id', $this->allowedWorkplaceIds);
        } else {
            $query->whereRaw('1 = 0');
        }

        if (!empty($this->search)) {
            $searchStr = trim($this->search);
            $nicHash = NicHelper::hash($searchStr);
            
            $query->whereHas('employee', function ($q) use ($searchStr, $nicHash) {
                $q->where('people_id', 'like', '%' . $searchStr . '%')
                  ->orWhere('phone', 'like', '%' . $searchStr . '%');
                  
                if (!empty($nicHash)) {
                    $q->orWhere('nic_hash', $nicHash);
                }
            });
        }

        $attachments = $query->get();

        $childWorkplaces = !empty($this->allowedWorkplaceIds)
            ? Workplaces::whereIn('workplace_id', $this->allowedWorkplaceIds)->get()
            : collect();
        $positions = Position::where('active_status', 1)->get();
        $officeLevels = OfficeLevel::where('active_status', 1)->get();

        return view('livewire.employees.employee-attachments', compact('attachments', 'childWorkplaces', 'positions', 'officeLevels'));
    }
}
