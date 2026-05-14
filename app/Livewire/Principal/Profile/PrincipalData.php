<?php

namespace App\Livewire\Principal\Profile;

use App\Models\People;
use Livewire\Component;
use App\Models\Principal;
use App\Models\SubjectList;
use App\Models\MediumOfInstruction;
use App\Models\EmployerCadreSubject;
use App\Models\PrincipalRecruitmentCategory;

class PrincipalData extends Component
{
    public $peopleId;
    public $employee;
    public $principalData;
    public $cadreData;

    public $appointmentId;

    // Form fields
    public $principalCategory;
    public $cadreSubject;
    public $cadreMedium;

    // Dropdown options (always collections)
    public $principalCategoriesOption = [];
    public $subjectOption = [];
    public $mediumOption = [];

    public function rules()
    {
        return [
            'principalCategory' => ['required', 'exists:principal_recruitment_categories,category_id'],
            'cadreSubject' => ['required', 'string'],
            'cadreMedium' => ['required', 'string'],
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
        $this->employee = People::with('appointment')->where('people_id', $this->peopleId)->first();
        if (! $this->employee) {
            throw new Exception('Employee not found.');
        }
        $this->appointmentId = $this->employee->appointment->appointment_id;
        $appointmentService = $this->employee->appointment->service_id;

        if ($this->appointmentId && $appointmentService == 'SER004') {
            $this->principalData = Principal::where('appointment_id', $this->appointmentId)->first();
            $this->principalCategory = $this->principalData->recruitment_category ?? '';

            $this->cadreData = EmployerCadreSubject::where('appointment_id', $this->appointmentId)->first();
            $this->cadreSubject = $this->cadreData->main_subject ?? '';
            $this->cadreMedium = $this->cadreData->appointment_medium ?? '';
        }

        $this->principalCategoriesOption = PrincipalRecruitmentCategory::Active()->get();

        

        $this->subjectOption = SubjectList::active()->where('type', '=', '2')->orderBy('name_en', 'asc')->get();
        $this->mediumOption = MediumOfInstruction::active()->orderBy('id', 'asc')->get();
        
    }


    /**
     * Save -> Update job data with error handling
     */
    public function save()
    {
        // Validate incoming data
        $this->validate();

        try {
            Principal::updateOrCreate(
                [
                    // UNIQUE condition (VERY IMPORTANT)
                    'employee_id' => $this->peopleId,
                ],
                [
                    'appointment_id' => $this->appointmentId,
                    'recruitment_category' => $this->principalCategory,
                    'updated_by'           => auth()->user()->people_id ?? null,
                ]
            );

            EmployerCadreSubject::updateOrCreate(
                [
                    // UNIQUE key(s)
                    'appointment_id' => $this->appointmentId,
                ],
                [
                    'employee_id'        => $this->peopleId,
                    'main_subject'       => $this->cadreSubject,
                    'appointment_medium' => $this->cadreMedium,
                    'updated_by'         => auth()->user()->people_id ?? null,
                ]
            );

            session()->flash('success', 'Employment updated successfully.');
            $this->reset('principalCategory');
        } catch (\Exception $e) {

            // Log the error if needed
            // logger()->error($e->getMessage());

            session()->flash('error', 'Something went wrong while updating the record.'. $e->getMessage());
        }

        return $this->redirect(url()->previous(), navigate: true);
    }

    public function resetFields()
    {
        $this->mount();
    }

    public function render()
    {
        return view('livewire.principal.profile.principal-data');
    }
}
