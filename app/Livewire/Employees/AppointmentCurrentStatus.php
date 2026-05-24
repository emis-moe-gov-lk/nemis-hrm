<?php

namespace App\Livewire\Employees;

use Exception;
use App\Models\People;
use Livewire\Component;
use App\Models\Position;
use App\Models\Workplaces;
use App\Models\Institution;
use App\Models\OfficeLevel;
use App\Models\ServiceRank;
use Illuminate\Support\Collection;
use App\Models\InstitutionCategory;
use App\Models\ZonalEducationOffice;
use App\Http\Controllers\PeopleEmploymentController;

class AppointmentCurrentStatus extends Component
{
    // Identity
    public string $peopleId;
    public bool $canEdit = false;
    public ?People $employee = null;

    // Form fields (Typed for PHP 8.x)
    public ?string $appointmentDate = null;
    public ?string $appointmentLetterNo = null;
    public ?string $serviceRank = null;
    public ?string $position = null;
    public ?string $officeLevel = null;
    public ?string $zonalEducationOffice = null;
    public ?string $institutionCategory = null;
    public ?string $workingPlace = null;

    // Dropdown options (Typed as Collections)
    public Collection $ranksOptions;
    public Collection $positionOption;
    public Collection $officeLevelOption;
    public Collection $zonalEducationOfficeOption;
    public Collection $institutionCategoryOption;
    public Collection $workingPlaceOption;

    public function rules(): array
    {
        return [
            'appointmentDate'      => ['required', 'date'],
            'appointmentLetterNo'  => ['nullable', 'string', 'max:255'],
            'serviceRank'          => ['required', 'exists:service_ranks,rank_id'],
            'position'             => ['required', 'exists:positions,position_id'],
            'officeLevel'          => ['required', 'exists:office_levels,office_level_id'],
            'zonalEducationOffice' => ['nullable', 'exists:workplaces,workplace_id'],
            'institutionCategory'  => ['nullable', 'exists:institution_categories,institution_category_id'],
            'workingPlace'         => ['required', 'exists:workplaces,workplace_id'],
        ];
    }

    public function updated(string $property): void
    {
        $this->validateOnly($property);
    }

    /**
     * Load initial state
     */
    public function mount(): void
    {
        $this->initializeCollections();

        try {
            $this->employee = People::with(['currentAppointment', 'appointment'])->where('people_id', $this->peopleId)->first();

            if (!$this->employee) {
                return;
            }

            // Load static options
            $this->officeLevelOption = OfficeLevel::all();
            $this->zonalEducationOfficeOption = ZonalEducationOffice::all();
            $this->institutionCategoryOption = InstitutionCategory::all();

            if ($this->employee->currentAppointment) {
                $a = $this->employee->currentAppointment;
                $mainApp = $this->employee->appointment;

                $this->appointmentDate     = $a->appoint_date?->format('Y-m-d');
                $this->appointmentLetterNo = $a->appointment_letter_no;
                $this->serviceRank         = $a->rank_id;
                $this->position            = $a->position_id;
                $this->officeLevel         = $a->office_level_id;
                $this->workingPlace        = $a->workplace_id;

                // Load dynamic options based on primary appointment service branch
                if ($mainApp) {
                    $this->ranksOptions = ServiceRank::where('service_id', $mainApp->service_id)->get();
                    $this->positionOption = Position::where('service_id', $mainApp->service_id)->get();
                }

                // Restore context if assigned to a school
                if ($a->office_level_id === 'OLID006') {
                    $inst = Institution::where('workplace_id', $a->workplace_id)->first();
                    if ($inst) {
                        $this->zonalEducationOffice = $inst->zeo_wp_id;
                        $this->institutionCategory  = $inst->institution_category_id;
                    }
                }
            } else {
                $this->appointmentDate = now()->format('Y-m-d');
                $this->appointmentLetterNo = '';
            }

            $this->loadWorkingPlaces();
        } catch (Exception $e) {
            session()->flash('error', 'Failed to load employment status: ' . $e->getMessage());
        }
    }

    private function initializeCollections(): void
    {
        $this->ranksOptions = collect();
        $this->positionOption = collect();
        $this->officeLevelOption = collect();
        $this->zonalEducationOfficeOption = collect();
        $this->institutionCategoryOption = collect();
        $this->workingPlaceOption = collect();
    }

    /**
     * Office Level change -> reset dependent fields
     */
    public function updatedOfficeLevel(): void
    {
        $this->zonalEducationOffice = null;
        $this->institutionCategory = null;
        $this->workingPlace = null;
        $this->loadWorkingPlaces();
    }

    public function updatedZonalEducationOffice(): void
    {
        $this->workingPlace = null;
        $this->loadWorkingPlaces();
    }

    public function updatedInstitutionCategory(): void
    {
        $this->workingPlace = null;
        $this->loadWorkingPlaces();
    }

    /**
     * Dynamic workplace loading
     */
    private function loadWorkingPlaces(): void
    {
        $this->workingPlaceOption = collect();

        if (empty($this->officeLevel)) return;

        if ($this->officeLevel !== 'OLID006') {
            $this->workingPlaceOption = Workplaces::where('office_level_id', $this->officeLevel)
                ->get()
                ->sortBy(fn($w) => $w->office_name)
                ->values();
            return;
        }

        // For schools (OLID006), require Zone + Category
        if ($this->zonalEducationOffice && $this->institutionCategory) {
            $this->workingPlaceOption = Workplaces::where('office_level_id', 'OLID006')
                ->whereHas('institution', function ($q) {
                    $q->where('zeo_wp_id', $this->zonalEducationOffice)
                        ->where('institution_category_id', $this->institutionCategory);
                })
                ->get()
                ->sortBy(fn($w) => $w->institution?->name)
                ->values();
        }
    }

    /**
     * Unified Save via Controller
     */
    public function save()
    {
        $this->validate();

        try {
            $controller = new PeopleEmploymentController();
            $controller->updateCurrentStatusOfAppointment([
                'appointment_id' => $this->employee->currentAppointment->appointment_id,
                'appointment_date' => $this->appointmentDate,
                'appointment_letter_no' => $this->appointmentLetterNo,
                'rank_id' => $this->serviceRank,
                'office_level_id' => $this->officeLevel,
                'position_id' => $this->position,
                'workplace_id' => $this->workingPlace,
            ]);

            session()->flash('success', 'Employment status updated successfully.');
            $this->dispatch('close-modal', name: 'current-employment-edit');

            return $this->redirect(url()->previous(), navigate: true);
        } catch (Exception $e) {
            session()->flash('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function resetFields(): void
    {
        $this->mount();
    }

    public function render()
    {
        return view('livewire.employees.appointment-current-status');
    }
}
