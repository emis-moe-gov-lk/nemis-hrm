<?php

namespace App\Livewire\Sleas\Profile;

use Exception;
use App\Models\People;
use Livewire\Component;
use App\Models\SubjectList;
use Illuminate\Support\Collection;
use App\Models\MediumOfInstruction;
use App\Models\EmployerCadreSubject;
use App\Http\Controllers\SleasServiceController;
use App\Models\EducationAdministratorService;
use App\Models\EducationAdministratorServiceSubject;
use App\Models\EducationAdministratorServiceCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SleasData extends Component
{
    use AuthorizesRequests;

    public string $peopleId;
    public ?People $employee = null;
    public ?EducationAdministratorService $sleasData = null;
    public ?EmployerCadreSubject $cadreData = null;

    public ?string $appointmentId = null;

    // Form fields
    public ?string $sleasCategory = null;
    public ?string $sleasSubject = null;
    public ?string $cadreMedium = null;
    public ?string $cadreSubject = null;

    // Dropdowns
    public Collection $sleasCategoriesOption;
    public Collection $sleasSubjectsOption;
    public Collection $subjectOption;
    public Collection $mediumOption;

    private const SERVICE_SLEAS = 'SER005';

    protected function rules(): array
    {
        return [
            'sleasCategory' => ['required', 'exists:education_administrator_service_categories,category_id'],
            'sleasSubject'  => ['required', 'exists:education_administrator_service_subjects,eas_subject_id'],
            'cadreSubject'  => ['nullable', 'exists:subject_lists,subject_id'],
            'cadreMedium'   => ['nullable', 'exists:medium_of_instructions,medium_id'],
        ];
    }

    public function mount(): void
    {
        $this->initializeCollections();

        try {
            $people = People::with(['appointment', 'currentAppointment'])
                ->where('people_id', $this->peopleId)
                ->first();

            if (!$people) return;

            $this->authorize('viewRestrict', $people);
            $this->employee = $people;

            $appointment = $this->employee->appointment;

            if ($appointment && $appointment->service_id === self::SERVICE_SLEAS) {
                $this->appointmentId = $appointment->appointment_id;
                $this->sleasData = EducationAdministratorService::where('appointment_id', $this->appointmentId)->first();
                $this->cadreData = EmployerCadreSubject::where('appointment_id', $this->appointmentId)->first();

                if ($this->sleasData) {
                    $this->sleasCategory = $this->sleasData->category_id;
                    $this->sleasSubject = $this->sleasData->subject;
                }

                if ($this->cadreData) {
                    $this->cadreSubject = $this->cadreData->main_subject;
                    $this->cadreMedium = $this->cadreData->appointment_medium;
                }
            }

            $this->loadOptions();
        } catch (Exception $e) {
            session()->flash('error', 'Error loading SLEAS data: ' . $e->getMessage());
        }
    }

    private function initializeCollections(): void
    {
        $this->sleasCategoriesOption = collect();
        $this->sleasSubjectsOption   = collect();
        $this->subjectOption         = collect();
        $this->mediumOption          = collect();
    }

    private function loadOptions(): void
    {
        $this->sleasCategoriesOption = EducationAdministratorServiceCategory::active()->get();
        $this->sleasSubjectsOption   = EducationAdministratorServiceSubject::active()->get();
        $this->subjectOption         = SubjectList::active()->where('type', '2')->orderBy('name_en', 'asc')->get();
        $this->mediumOption          = MediumOfInstruction::active()->orderBy('id', 'asc')->get();
    }

    public function updated(string $property): void
    {
        $this->validateOnly($property);
    }

    public function save()
    {
        $this->validate();

        try {
            $controller = new SleasServiceController();
            
            $controller->updateSleasService([
                'appointment_id'   => $this->appointmentId,
                'employee_id'      => $this->peopleId,
                'category_id'      => $this->sleasCategory,
                'subject_id'       => $this->sleasSubject,
                'medium_id'        => $this->cadreMedium,
                'cadre_subject_id' => $this->cadreSubject,
            ]);

            session()->flash('success', 'SLEAS service details updated successfully.');
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
        return view('livewire.sleas.profile.sleas-data');
    }
}

