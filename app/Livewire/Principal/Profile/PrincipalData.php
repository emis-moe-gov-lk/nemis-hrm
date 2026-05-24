<?php

namespace App\Livewire\Principal\Profile;

use Exception;
use App\Models\People;
use Livewire\Component;
use App\Models\Principal;
use App\Models\SubjectList;
use Illuminate\Support\Collection;
use App\Models\MediumOfInstruction;
use App\Models\EmployerCadreSubject;
use App\Models\PrincipalRecruitmentCategory;
use App\Http\Controllers\PrincipalServiceController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PrincipalData extends Component
{
    use AuthorizesRequests;

    public string $peopleId;
    public ?People $employee = null;
    public ?Principal $principalData = null;
    public ?EmployerCadreSubject $cadreData = null;

    public ?string $appointmentId = null;

    // Form fields
    public ?string $principalCategory = null;
    public ?string $cadreSubject = null;
    public ?string $cadreMedium = null;

    // Dropdown options
    public Collection $principalCategoriesOption;
    public Collection $subjectOption;
    public Collection $mediumOption;

    public function rules(): array
    {
        return [
            'principalCategory' => ['required', 'exists:principal_recruitment_categories,category_id'],
            'cadreSubject'      => ['required', 'exists:subject_lists,subject_id'],
            'cadreMedium'       => ['required', 'exists:medium_of_instructions,medium_id'],
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
            $people = People::with('appointment')->where('people_id', $this->peopleId)->first();
            if (!$people) return;

            $this->employee = $people;
            $appointment = $this->employee->appointment;

            if ($appointment && $appointment->service_id == 'SER004') {
                $this->appointmentId = $appointment->appointment_id;
                $this->principalData = Principal::where('appointment_id', $this->appointmentId)->first();
                $this->cadreData = EmployerCadreSubject::where('appointment_id', $this->appointmentId)->first();

                if ($this->principalData) {
                    $this->principalCategory = $this->principalData->recruitment_category;
                }
                
                if ($this->cadreData) {
                    $this->cadreSubject = $this->cadreData->main_subject;
                    $this->cadreMedium = $this->cadreData->appointment_medium;
                }
            }

            $this->loadOptions();
        } catch (Exception $e) {
            session()->flash('error', 'Error loading principal data: ' . $e->getMessage());
        }
    }

    private function initializeCollections(): void
    {
        $this->principalCategoriesOption = collect();
        $this->subjectOption = collect();
        $this->mediumOption = collect();
    }

    private function loadOptions(): void
    {
        $this->principalCategoriesOption = PrincipalRecruitmentCategory::Active()->get();
        $this->subjectOption            = SubjectList::Active()->where('type', '2')->orderBy('name_en', 'asc')->get();
        $this->mediumOption             = MediumOfInstruction::Active()->orderBy('id', 'asc')->get();
    }

    public function save()
    {
        $this->validate();

        try {
            $controller = new PrincipalServiceController();
            
            $controller->updatePrincipalService([
                'appointment_id'        => $this->appointmentId,
                'employee_id'           => $this->peopleId,
                'principal_category_id' => $this->principalCategory,
                'medium_id'             => $this->cadreMedium,
                'subject_id'            => $this->cadreSubject,
            ]);

            session()->flash('success', 'Principal service details updated successfully.');
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
        return view('livewire.principal.profile.principal-data');
    }
}

