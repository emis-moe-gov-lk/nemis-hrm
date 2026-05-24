<?php

namespace App\Livewire\Employees;

use App\Models\{
    EmployerAppointment,
    EmployerAppointmentHistory,
    EmployerAppointmentRankHistory,
    EmployerAppointmentWorkplaceHistory,
    EmployerAppointmentPositionHistory,
    Institution,
    InstitutionCategory,
    OfficeLevel,
    People,
    Position,
    Service,
    ServiceRank,
    Teacher,
    Workplaces,
    ZonalEducationOffice
};
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PreviousServicesReg extends Component
{
    // -------------------------------------------------
    // Public properties (Typed)
    // -------------------------------------------------
    public string $peopleId;
    public bool $canCreate = false;
    public bool $canDelete = false;
    public ?People $people = null;
    public mixed $teacherAppointment = null;

    // Dropdown options (Typed)
    public Collection|array $servicesOptions = [];
    public Collection|array $ranksOptions = [];
    public Collection|array $positionOption = [];
    public Collection|array $officeLevelOption = [];
    public Collection|array $zonalEducationOfficeOption = [];
    public Collection|array $institutionCategoryOption = [];
    public Collection|array $workingPlaceOption = [];
    public Collection|array $subjectOption = [];
    public Collection|array $currentInstitutionOption = [];

    // Selected dropdown values / input fields
    public ?string $recordType = null;
    public ?string $service = null;
    public ?string $rank = null;
    public ?string $position = null;
    public ?string $officeLevel = null;
    public ?string $institutionCategory = null;
    public ?string $zonalEducationOffice = null;
    public ?string $workingPlace = null;
    public ?string $firstAppointmentDate = null;
    public ?string $appointmentLetterNo = null;

    // Rank History Form Fields
    public ?string $selectedAppointmentId = null;
    public ?string $historyRankId = null;
    public ?string $historyStartDate = null;
    public ?string $historyEndDate = null;
    public ?string $historyRemarks = null;
    public bool $historyIsActive = false;
    public bool $showRankModal = false;

    // Delete state
    public ?int $serviceIdToDelete = null;
    public ?int $rankIdToDelete = null;

    protected function rules(): array
    {
        return [
            'recordType' => ['required', Rule::in(['0', '1', '2', '3'])],
            'service' => ['required'],
            'rank' => ['required', 'exists:service_ranks,rank_id'],
            'position' => ['required', 'exists:positions,position_id'],
            'officeLevel' => ['required', 'exists:office_levels,office_level_id'],
            'zonalEducationOffice' => ['nullable', 'exists:zonal_education_offices,workplace_id'],
            'institutionCategory' => ['nullable', 'exists:institution_categories,institution_category_id'],
            'workingPlace' => ['required', 'exists:workplaces,workplace_id'],
            'firstAppointmentDate' => ['required', 'date', 'before_or_equal:today'],
            'appointmentLetterNo' => ['required', 'string', 'max:255'],
        ];
    }

    public function updated(string $propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function mount(string $peopleId): void
    {
        $this->peopleId = $peopleId;
        $this->people = People::with('currentAppointment')->where('people_id', $this->peopleId)->first();
        if ($this->people) {
            $this->servicesOptions = Service::govService()->get();
        }

        $this->officeLevelOption = OfficeLevel::all();
        $this->zonalEducationOfficeOption = ZonalEducationOffice::all();
        $this->institutionCategoryOption = InstitutionCategory::all();
    }

    public function updatedService(string $value): void
    {
        $this->ranksOptions = ServiceRank::where('service_id', $value)->get();
        $this->positionOption = Position::where('service_id', $value)->get();
    }

    public function updatedOfficeLevel(string $value): void
    {
        if ($value === 'OLID006') {
            $this->workingPlaceOption = collect();
            $this->workingPlace = '';
        } else {
            $this->workingPlaceOption = Workplaces::where('office_level_id', $value)->get();
            $this->workingPlace = '';
        }
    }

    public function updatedZonalEducationOffice(?string $value): void
    {
        if ($value && $this->institutionCategory) {
            $this->workingPlaceOption = Workplaces::where('office_level_id', 'OLID006')
                ->whereHas('institution', function ($query) use ($value) {
                    $query->where('zeo_wp_id', $value)
                        ->where('institution_category_id', $this->institutionCategory);
                })
                ->with(['institution' => function ($q) {
                    $q->orderBy('name', 'asc');
                }])
                ->get()
                ->sortBy('institution.name')
                ->values();
        } else {
            $this->workingPlaceOption = collect();
        }

        $this->workingPlace = '';
    }

    public function updatedInstitutionCategory(?string $value): void
    {
        if ($value && $this->zonalEducationOffice) {
            $this->workingPlaceOption = Workplaces::where('office_level_id', 'OLID006')
                ->whereHas('institution', function ($query) use ($value) {
                    $query->where('institution_category_id', $value)
                        ->where('zeo_wp_id', $this->zonalEducationOffice);
                })
                ->with(['institution' => function ($q) {
                    $q->orderBy('name', 'asc');
                }])
                ->get()
                ->sortBy('institution.name')
                ->values();
        } else {
            $this->workingPlaceOption = collect();
        }

        $this->workingPlace = '';
    }

    public function render()
    {
        $employeeServiceList = collect();

        if ($this->people && $this->people->currentAppointment) {
            $employeeServiceList = EmployerAppointment::where('employee_id', $this->people->currentAppointment->employee_id)
                ->with(['position', 'service', 'rank', 'workplace', 'rankHistory.rank'])
                ->orderBy('active_status', 'desc')
                ->orderBy('first_appointment_date', 'desc')
                ->get();
        }

        return view('livewire.employees.previous-services-reg', compact('employeeServiceList'));
    }

    public function saveServiceRecord()
    {
        $this->validate([
            'service' => ['required'],
            'rank' => ['required', 'exists:service_ranks,rank_id'],
            'position' => ['required', 'exists:positions,position_id'],
            'officeLevel' => ['required', 'exists:office_levels,office_level_id'],
            'zonalEducationOffice' => ['nullable', 'exists:zonal_education_offices,workplace_id'],
            'institutionCategory' => ['nullable', 'exists:institution_categories,institution_category_id'],
            'workingPlace' => ['required', 'exists:workplaces,workplace_id'],
            'firstAppointmentDate' => ['required', 'date', 'before_or_equal:today'],
            'appointmentLetterNo' => ['required', 'string', 'max:255'],
        ]);

        try {
            $retirementDate = Carbon::parse($this->people->date_of_birth)->addYears(55);
            $appointmentId = EmployerAppointment::generateAppointmentId($this->firstAppointmentDate);

            EmployerAppointment::create([
                'appointment_id' => $appointmentId,
                'employee_id' => $this->people->people_id,
                'first_appointment_date' => $this->firstAppointmentDate,
                'retirement_date' => $retirementDate,
                'service_id' => $this->service,
                'rank_id' => $this->rank,
                'position_id' => $this->position,
                'office_level_id' => $this->officeLevel,
                'workplace_id' => $this->workingPlace,
                'appointment_letter_no' => $this->appointmentLetterNo,
                'appointment_letter' => 'default_letter.pdf',
                'w_op_no' => null,
                'active_status' => '0',
            ]);

            session()->flash('success', 'New service record added successfully!');
            return $this->redirect(url()->previous(), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while saving the record: Duplicate entry or invalid data.');
            return $this->redirect(url()->previous(), navigate: true);
        }
    }

    public function confirmDeleteService(int $id): void
    {
        $this->serviceIdToDelete = $id;
        $this->dispatch('modal-show', name: 'delete-service-confirmation');
    }

    public function deleteServiceRecord(): void
    {
        if (!$this->serviceIdToDelete) return;

        $record = EmployerAppointment::find($this->serviceIdToDelete);

        if ($record && $record->active_status != 1 && !$record->currentAppointment()->exists() && !$record->appointmentHistory()->exists()) {
            $record->delete();
            session()->flash('success', 'Record deleted successfully.');
        } else {
            session()->flash('error', 'Cannot delete this record. It may be active or have associated history.');
        }

        $this->serviceIdToDelete = null;
        $this->dispatch('modal-close', name: 'delete-service-confirmation');
        $this->redirect(url()->previous(), navigate: true);
    }

    public function confirmDeleteRank(int $id): void
    {
        $this->rankIdToDelete = $id;
        $this->dispatch('modal-show', name: 'delete-rank-confirmation');
    }

    public function deleteRankHistoryRecord(): void
    {
        if (!$this->rankIdToDelete) return;

        $record = EmployerAppointmentRankHistory::find($this->rankIdToDelete);

        if ($record && !$record->is_active) {
            $record->delete();
            session()->flash('success', 'Rank history record deleted successfully.');
        } else {
            session()->flash('error', 'Active rank records cannot be deleted.');
        }

        $this->rankIdToDelete = null;
        $this->dispatch('modal-close', name: 'delete-rank-confirmation');
        $this->redirect(url()->previous(), navigate: true);
    }

    public function setAppointmentForHistory(string $appointmentId): void
    {
        $this->selectedAppointmentId = $appointmentId;

        $appointment = EmployerAppointment::where('appointment_id', $appointmentId)->first();
        if ($appointment) {
            $this->ranksOptions = ServiceRank::where('service_id', $appointment->service_id)->get();
        }

        $this->reset(['historyRankId', 'historyStartDate', 'historyEndDate', 'historyRemarks', 'historyIsActive']);
    }

    public function saveRankHistory()
    {
        $this->validate([
            'historyRankId' => ['required', 'exists:service_ranks,rank_id'],
            'historyStartDate' => ['required', 'date'],
            'historyEndDate' => ['nullable', 'date', 'after_or_equal:historyStartDate'],
            'historyRemarks' => ['nullable', 'string', 'max:500'],
        ]);

        $appointment = EmployerAppointment::where('appointment_id', $this->selectedAppointmentId)->first();

        if (!$appointment) {
            session()->flash('error', 'Appointment not found.');
            return;
        }

        EmployerAppointmentRankHistory::create([
            'appointment_id' => $this->selectedAppointmentId,
            'employee_id' => $this->people->people_id,
            'rank_id' => $this->historyRankId,
            'start_date' => $this->historyStartDate,
            'end_date' => $this->historyEndDate,
            'is_active' => $this->historyIsActive,
            'remarks' => $this->historyRemarks,
        ]);

        session()->flash('success', 'Rank history record added successfully!');
        return $this->redirect(url()->previous(), navigate: true);
    }
}
