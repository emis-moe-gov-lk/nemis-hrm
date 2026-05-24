<?php

namespace App\Livewire\Teacher\Profile;

use Exception;
use App\Models\People;
use App\Models\Teacher;
use Livewire\Component;
use App\Models\SubjectList;
use App\Models\TeacherType;
use App\Models\ApointedSubject;
use App\Models\TeacherCategory;
use Illuminate\Support\Collection;
use App\Models\MediumOfInstruction;
use App\Models\EmployerCadreSubject;
use App\Http\Controllers\TeacherServiceController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherData extends Component
{
    use AuthorizesRequests;
    
    // Identity
    public string $peopleId;
    public ?People $employee = null;
    public ?Teacher $teacherData = null;
    public ?EmployerCadreSubject $cadreData = null;

    // Form fields (Typed)
    public ?string $teacherType = null;
    public ?string $teacherCategory = null;
    public ?string $appointmentMedium = null;
    public ?string $appointmentSubject = null;
    public ?string $mainSubject = null;
    public ?string $secondarySubject = null;
    public ?string $currentTeachingSubject = null;

    // Dropdown options (Typed Collections)
    public Collection $teacherTypeOptions;
    public Collection $teacherCategoryOptions;
    public Collection $mediumOptions;
    public Collection $appointmentSubjectOptions;
    public Collection $subjectOptions;

    public function rules(): array
    {
        return [
            'teacherType'            => ['required', 'exists:teacher_types,teacher_types_id'],
            'teacherCategory'        => ['required', 'exists:teacher_categories,categories_id'],
            'appointmentMedium'      => ['required', 'exists:medium_of_instructions,medium_id'],
            'appointmentSubject'     => ['required', 'exists:apointed_subjects,a_subject_id'],
            'mainSubject'            => ['required', 'exists:subject_lists,subject_id'],
            'secondarySubject'       => ['nullable', 'exists:subject_lists,subject_id'],
            'currentTeachingSubject' => ['required', 'exists:subject_lists,subject_id'],
        ];
    }

    /**
     * Live validation
     */
    public function updated(string $property): void
    {
        $this->validateOnly($property);
    }

    /**
     * Initial data load
     */
    public function mount(): void
    {
        $this->initializeCollections();

        try {
            $people = People::with('appointment')->where('people_id', $this->peopleId)->first();
            
            if (!$people) return;
            
            $this->authorize('viewRestrict', $people);
            $this->employee = $people;

            $appointment = $this->employee->appointment;

            // Only load teacher data if they are in the Teaching Service branch
            if ($appointment && $appointment->service_id == 'SER001') {
                $this->teacherData = Teacher::where('appointment_id', $appointment->appointment_id)->first();
                $this->cadreData = EmployerCadreSubject::where('appointment_id', $appointment->appointment_id)->first();

                if ($this->teacherData) {
                    $this->teacherType            = $this->teacherData->teacher_type;
                    $this->teacherCategory        = $this->teacherData->teacher_category;
                    $this->appointmentMedium      = $this->teacherData->appointment_medium;
                    $this->appointmentSubject     = $this->teacherData->appointment_subject;
                    $this->mainSubject            = $this->teacherData->main_subject;
                    $this->secondarySubject       = $this->teacherData->secondary_subject;
                    $this->currentTeachingSubject = $this->teacherData->current_teaching_subject;
                }
            }

            $this->loadOptions();
        } catch (Exception $e) {
            session()->flash('error', 'Error loading teacher data: ' . $e->getMessage());
        }
    }

    private function initializeCollections(): void
    {
        $this->teacherTypeOptions = collect();
        $this->teacherCategoryOptions = collect();
        $this->mediumOptions = collect();
        $this->appointmentSubjectOptions = collect();
        $this->subjectOptions = collect();
    }

    private function loadOptions(): void
    {
        $this->teacherTypeOptions        = TeacherType::Active()->get();
        $this->teacherCategoryOptions    = TeacherCategory::Active()->get();
        $this->mediumOptions             = MediumOfInstruction::Active()->get();
        $this->appointmentSubjectOptions = ApointedSubject::Active()->get();
        $this->subjectOptions            = SubjectList::Active()->get();
    }

    /**
     * Save details via TeacherServiceController
     */
    public function save()
    {
        $this->validate();

        try {
            $controller = new TeacherServiceController();
            
            // Map component fields to controller keys
            $request = new \Illuminate\Http\Request([
                'appointment_id'           => $this->employee->appointment->appointment_id,
                'employee_id'              => $this->employee->people_id,
                'teacher_category_id'      => $this->teacherCategory,
                'teacher_type_id'          => $this->teacherType,
                'appointment_medium_id'    => $this->appointmentMedium,
                'appointment_subject_id'   => $this->appointmentSubject,
                'main_teaching_subject_id' => $this->mainSubject,
                'secondary_subject_id'     => $this->secondarySubject,
            ]);

            $controller->updateTeacherService($request);

            session()->flash('success', 'Teacher service details updated successfully.');
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
        return view('livewire.teacher.profile.teacher-data');
    }
}
