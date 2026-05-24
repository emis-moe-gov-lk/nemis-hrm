<?php

namespace App\Livewire\Employees;

use Exception;
use App\Models\People;
use App\Models\Service;
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

class FirstAppointment extends Component
{
    // Identity
    public string $peopleId;
    public bool $canEdit = false;
    public ?People $employee = null;

    // Form fields (Typed for PHP 8.x)
    public ?string $appointmentDate = null;
    public ?string $appointmentLetterNo = null;
    public ?string $service = null;
    public ?string $serviceRank = null;
    public ?string $position = null;
    public ?string $officeLevel = null;
    public ?string $zonalEducationOffice = null;
    public ?string $institutionCategory = null;
    public ?string $workingPlace = null;

    // Dropdown options (Typed as Collections)
    public Collection $userServicesOptions;
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
            'service'              => ['required', 'exists:services,service_id'],
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

    public function mount(): void
    {
        $this->initializeCollections();

        try {
            $this->employee = People::with('appointment')->where('people_id', $this->peopleId)->first();

            // Load static options
            $this->userServicesOptions = Service::all();
            $this->officeLevelOption = OfficeLevel::all();
            $this->zonalEducationOfficeOption = ZonalEducationOffice::all();
            $this->institutionCategoryOption = InstitutionCategory::all();

            if ($this->employee && $this->employee->appointment) {
                $a = $this->employee->appointment;

                $this->appointmentDate     = $a->first_appointment_date?->format('Y-m-d');
                $this->appointmentLetterNo = $a->appointment_letter_no;
                $this->service             = $a->service_id;
                $this->serviceRank         = $a->rank_id;
                $this->position            = $a->position_id;
                $this->officeLevel         = $a->office_level_id;
                $this->workingPlace        = $a->workplace_id;

                $this->loadDependentOptions();

                // Restore school context
                if ($a->office_level_id === 'OLID006') {
                    $inst = Institution::where('workplace_id', $a->workplace_id)->first();
                    if ($inst) {
                        $this->zonalEducationOffice = $inst->zeo_wp_id;
                        $this->institutionCategory  = $inst->institution_category_id;
                    }
                }
            } else {
                $this->appointmentDate = now()->format('Y-m-d');
            }

            $this->loadWorkingPlaces();
        } catch (Exception $e) {
            session()->flash('error', 'Failed to load initial data: ' . $e->getMessage());
        }
    }

    private function initializeCollections(): void
    {
        $this->userServicesOptions = collect();
        $this->ranksOptions = collect();
        $this->positionOption = collect();
        $this->officeLevelOption = collect();
        $this->zonalEducationOfficeOption = collect();
        $this->institutionCategoryOption = collect();
        $this->workingPlaceOption = collect();
    }

    private function loadDependentOptions(): void
    {
        if ($this->service) {
            $this->ranksOptions = ServiceRank::where('service_id', $this->service)->get();
            $this->positionOption = Position::where('service_id', $this->service)->get();
        }
    }

    public function updatedService(): void
    {
        $this->serviceRank = null;
        $this->position = null;
        $this->loadDependentOptions();
    }

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

    public function save()
    {
        $this->validate();

        try {
            $controller = new PeopleEmploymentController();

            if ($this->employee && $this->employee->appointment) {
                // Update Existing
                $controller->updateAppointment([
                    'appointment_id' => $this->employee->appointment->appointment_id,
                    'first_appointment_date' => $this->appointmentDate,
                    'appointment_letter_no' => $this->appointmentLetterNo,
                    'service_id' => $this->service,
                    'rank_id' => $this->serviceRank,
                    'position_id' => $this->position,
                    'office_level_id' => $this->officeLevel,
                    'workplace_id' => $this->workingPlace,
                ]);
            } else {
                // Create New
                $controller->createNewOrExistingEmployment([
                    'people_id' => $this->peopleId,
                    'first_appointment_date' => $this->appointmentDate,
                    'appointment_letter_no' => $this->appointmentLetterNo,
                    'service_id' => $this->service,
                    'rank_id' => $this->serviceRank,
                    'position_id' => $this->position,
                    'office_level_id' => $this->officeLevel,
                    'workplace_id' => $this->workingPlace,
                    'is_fresh_appointment' => true,
                ]);
            }

            session()->flash('success', 'First appointment details saved successfully.');
            $this->dispatch('close-modal', name: 'first-appointment-modal');
            return $this->redirect(url()->previous(), navigate: true);
        } catch (Exception $e) {
            session()->flash('error', 'Operation failed: ' . $e->getMessage());
        }
    }

    public function resetFields(): void
    {
        $this->mount();
    }

    public function render()
    {
        return view('livewire.employees.first-appointment');
    }
}
