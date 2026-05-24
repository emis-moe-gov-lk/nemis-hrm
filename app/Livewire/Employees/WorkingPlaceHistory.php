<?php

namespace App\Livewire\Employees;

use App\Models\EmployerAppointment;
use App\Models\EmployerAppointmentWorkplaceHistory;
use App\Models\Institution;
use App\Models\InstitutionCategory;
use App\Models\OfficeLevel;
use App\Models\People;
use App\Models\Workplaces;
use App\Models\ZonalEducationOffice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Component;

class WorkingPlaceHistory extends Component
{
    public string $peopleId;
    public bool $canCreate = false;
    public bool $canDelete = false;

    // Form fields (Typed)
    public ?string $appointmentId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $refLetterNo = null;
    public ?string $remarks = null;

    // Workplace selection fields (Typed)
    public ?string $officeLevel = null;
    public ?string $zonalEducationOffice = null;
    public ?string $institutionCategory = null;
    public ?string $workingPlace = null;

    // Options (Typed Collections/Arrays)
    public Collection|array $appointments = [];
    public Collection|array $officeLevelOption = [];
    public Collection|array $zonalEducationOfficeOption = [];
    public Collection|array $institutionCategoryOption = [];
    public Collection|array $workingPlaceOption = [];
    public ?int $historyIdToDelete = null;

    public function mount(): void
    {
        $this->officeLevelOption = OfficeLevel::all();
        $this->zonalEducationOfficeOption = ZonalEducationOffice::all();
        $this->institutionCategoryOption = InstitutionCategory::all();

        // Super Admin Bypass
        /** @var User $user */
        $user = Auth::user();
        if ($user && $user->hasRole('super-admin')) {
            $this->canCreate = true;
            $this->canDelete = true;
        }

        $people = People::where('people_id', $this->peopleId)->first();
        if ($people) {
            $this->appointments = EmployerAppointment::where('employee_id', $people->people_id)->get();
            // Default to the first appointment if available
            if (count($this->appointments) > 0) {
                $this->appointmentId = $this->appointments[0]->appointment_id;
            }
        }
    }

    public function updatedOfficeLevel(string $value): void
    {
        $this->zonalEducationOffice = null;
        $this->institutionCategory = null;
        $this->workingPlace = null;
        $this->workingPlaceOption = collect();

        if ($value && $value !== 'OLID006') {
            $this->workingPlaceOption = Workplaces::where('office_level_id', $value)
                ->active()
                ->get();
        }
    }

    public function updatedZonalEducationOffice(?string $value): void
    {
        $this->loadWorkplacesForSchool();
    }

    public function updatedInstitutionCategory(?string $value): void
    {
        $this->loadWorkplacesForSchool();
    }

    private function loadWorkplacesForSchool(): void
    {
        if ($this->officeLevel === 'OLID006' && $this->zonalEducationOffice && $this->institutionCategory) {
            $this->workingPlaceOption = Institution::where('zeo_wp_id', $this->zonalEducationOffice)
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
        $this->workingPlace = null;
    }

    public function saveWorkplaceHistory(): void
    {
        $this->validate([
            'appointmentId' => 'required',
            'officeLevel' => 'required',
            'workingPlace' => 'required',
            'startDate' => 'required|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
            'refLetterNo' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        EmployerAppointmentWorkplaceHistory::create([
            'appointment_id' => $this->appointmentId,
            'employee_id' => $this->peopleId,
            'workplace_id' => $this->workingPlace,
            'office_level_id' => $this->officeLevel,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'ref_letter_no' => $this->refLetterNo,
            'remarks' => $this->remarks,
            'is_active' => false,
        ]);

        $this->reset(['workingPlace', 'officeLevel', 'zonalEducationOffice', 'institutionCategory', 'startDate', 'endDate', 'refLetterNo', 'remarks']);
        $this->dispatch('modal-close', name: 'add-workplace-history');
        session()->flash('success', 'Workplace history added successfully.');
    }

    public function confirmDelete(int $id): void
    {
        $this->historyIdToDelete = $id;
        $this->dispatch('modal-show', name: 'delete-workplace-history-confirmation');
    }

    public function deleteHistory(): void
    {
        if (!$this->historyIdToDelete) return;

        $record = EmployerAppointmentWorkplaceHistory::find($this->historyIdToDelete);
        if ($record && !$record->is_active) {
            $record->delete();
            session()->flash('success', 'History record deleted.');
        } else {
            session()->flash('error', 'Cannot delete active workplace records.');
        }

        $this->historyIdToDelete = null;
        $this->dispatch('modal-close', name: 'delete-workplace-history-confirmation');
    }

    public function render()
    {
        $workplaceHistory = EmployerAppointmentWorkplaceHistory::where('employee_id', $this->peopleId)
            ->with(['workplace', 'officeLevel'])
            ->orderBy('start_date', 'desc')
            ->get();

        return view('livewire.employees.working-place-history', compact('workplaceHistory'));
    }
}
