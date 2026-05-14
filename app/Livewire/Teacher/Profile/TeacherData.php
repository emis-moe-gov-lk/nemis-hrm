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
use App\Models\MediumOfInstruction;
use App\Models\EmployerCadreSubject;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TeacherData extends Component
{
    use AuthorizesRequests;
    
    public $people;
    public $peopleId;
    public $employee;
    public $teacherData;

    // Form fields
    public $teacherType;
    public $teacherCategory;
    public $appointmentMedium;
    public $appointmentSubject;
    public $mainSubject;
    public $secondarySubject;
    public $currentTeachingSubject;

    // Dropdown options (always collections)
    public $teacherTypeOptions;
    public $teacherCategoryOptions;
    public $mediumOptions;
    public $appointmentSubjectOptions;
    public $subjectOptions;

    public function rules()
    {
        return [
            'teacherType' => ['required', 'exists:teacher_types,teacher_types_id'],
            'teacherCategory' => ['required', 'exists:teacher_categories,categories_id'],
            'appointmentMedium'      => ['required', 'exists:medium_of_instructions,medium_id'],
            'appointmentSubject' => ['required', 'exists:apointed_subjects,a_subject_id'],
            'mainSubject' => ['required', 'exists:subject_lists,subject_id'],
            'secondarySubject' => ['required', 'exists:subject_lists,subject_id'],
            'currentTeachingSubject' => ['required', 'exists:subject_lists,subject_id'],
        ];
    }

    // Live validation per-field
    public function updated($property)
    {
        $this->validateOnly($property);
    }

    /**
     * MOUNT → Load initial data + current appointment information
     */
    public function mount()
    {
        $people = People::with('appointment')->where('people_id', $this->peopleId)->first();
        $this->authorize('viewRestrict', $people);

        $this->employee = $people;
        if (! $this->employee) {
            throw new Exception('Employee not found.');
        }
        $appointmentId = $this->employee->appointment->appointment_id;
        $appointmentService = $this->employee->appointment->service_id;

        if ($appointmentId && $appointmentService == 'SER001') {
            $this->teacherData = Teacher::where('appointment_id', $appointmentId)->first();
            $this->cadreData = EmployerCadreSubject::where('appointment_id', $appointmentId)->first();
            $this->teacherType = $this->teacherData->teacher_type ?? '';
            $this->teacherCategory = $this->teacherData->teacher_category ?? '';
            $this->appointmentMedium = $this->teacherData->appointment_medium ?? '';
            $this->appointmentSubject = $this->teacherData->appointment_subject ?? '';
            $this->mainSubject = $this->teacherData->main_subject ?? '';
            $this->secondarySubject = $this->teacherData->secondary_subject ?? '';
            $this->currentTeachingSubject = $this->teacherData->current_teaching_subject ?? '';
        }

        $this->teacherTypeOptions = TeacherType::Active()->get();
        $this->teacherCategoryOptions = TeacherCategory::Active()->get();
        $this->mediumOptions = MediumOfInstruction::Active()->get();
        $this->appointmentSubjectOptions = ApointedSubject::Active()->get();
        $this->subjectOptions = SubjectList::Active()->get();
    }


    /**
     * Save -> Update job data with error handling
     */
    public function save()
    {
        // Validate incoming data
        $this->validate();

        try {
            // Update the teacher's employment data
            $this->teacherData->update([
                'teacher_type'             => $this->teacherType,
                'teacher_category'         => $this->teacherCategory,
                'appointment_medium'       => $this->appointmentMedium,
                'appointment_subject'      => $this->appointmentSubject,
                'main_subject'             => $this->mainSubject,
                'secondary_subject'        => $this->secondarySubject,
                'current_teaching_subject' => $this->currentTeachingSubject,
            ]);

            // Update the cadre subject data
            $this->cadreData->update([
                'appointment_medium'       => $this->appointmentMedium,
                'main_subject'             => $this->mainSubject,
            ]);

            session()->flash('success', 'Employment updated successfully.');
            $this->reset('teacherType', 'teacherCategory', 'appointmentMedium', 'appointmentSubject', 'mainSubject', 'secondarySubject', 'currentTeachingSubject');
        } catch (\Exception $e) {

            // Log the error if needed
            // logger()->error($e->getMessage());

            session()->flash('error', 'Something went wrong while updating the record.');
        }

        return $this->redirect(url()->previous(), navigate: true);
    }

    public function resetFields()
    {
        $this->mount();
    }


    public function render()
    {
        return view('livewire.teacher.profile.teacher-data');
    }
}
