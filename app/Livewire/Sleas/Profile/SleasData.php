<?php

namespace App\Livewire\Sleas\Profile;

use App\Models\People;
use Livewire\Component;
use App\Models\SubjectList;
use App\Models\MediumOfInstruction;
use App\Models\EmployerCadreSubject;
use App\Models\EducationAdministratorService;
use App\Models\EducationAdministratorServiceSubject;
use App\Models\EducationAdministratorServiceCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SleasData extends Component
{
    use AuthorizesRequests;

    public string $peopleId;

    public $employee = null;

    // Loaded records (can be null if not created yet)
    public $sleasData = null;
    public $cadreData = null;

    // Form fields
    public $sleasCategory = null;
    public $sleasSubject  = null;
    public $cadreSubject  = null;
    public $cadreMedium   = null;

    // Dropdowns
    public $sleasCategoriesOption;
    public $sleasSubjectsOption;
    public $subjectOption = [];
    public $mediumOption  = [];

    private const SERVICE_SLEAS = 'SER005';

    protected function rules(): array
    {
        return [
            'sleasCategory' => ['required', 'exists:education_administrator_service_categories,category_id'],
            'sleasSubject'  => ['required', 'exists:education_administrator_service_subjects,eas_subject_id'],

            // optional, but if one selected, other required
            'cadreSubject'  => ['nullable', 'exists:subject_lists,subject_id', 'required_with:cadreMedium'],
            'cadreMedium'   => ['nullable', 'exists:medium_of_instructions,medium_id', 'required_with:cadreSubject'],
        ];
    }

    public function mount(): void
    {
        $this->loadData();

        // Dropdown data
        $this->sleasCategoriesOption = EducationAdministratorServiceCategory::active()->get();
        $this->sleasSubjectsOption   = EducationAdministratorServiceSubject::active()->get();

        $this->subjectOption = SubjectList::active()
            ->where('type', '=', '2')
            ->orderBy('name_en', 'asc')
            ->get();

        $this->mediumOption = MediumOfInstruction::active()
            ->orderBy('id', 'asc')
            ->get();
    }

    private function loadData(): void
    {
        $people = People::with('appointment')
            ->where('people_id', $this->peopleId)
            ->first();

        $this->authorize('viewRestrict', $people);

        $this->employee = $people;

        if (! $this->employee || ! $this->employee->appointment) {
            session()->flash('error', 'Employee or appointment not found.');
            return;
        }

        if ($this->employee->appointment->service_id !== self::SERVICE_SLEAS) {
            $this->sleasData = null;
            $this->cadreData = null;

            $this->sleasCategory = null;
            $this->sleasSubject  = null;
            $this->cadreSubject  = null;
            $this->cadreMedium   = null;
            return;
        }

        $appointmentId = $this->employee->currentAppointment->appointment_id;

        $this->sleasData = EducationAdministratorService::where('appointment_id', $appointmentId)->first();
        $this->cadreData = EmployerCadreSubject::where('appointment_id', $appointmentId)->first();

        $this->sleasCategory = $this->sleasData?->category_id;
        $this->sleasSubject  = $this->sleasData?->subject;

        $this->cadreSubject  = $this->cadreData?->main_subject;
        $this->cadreMedium   = $this->cadreData?->appointment_medium;
    }

    public function updated($property): void
    {
        $this->validateOnly($property);
    }

    public function save()
    {
        $this->validate();

        try {
            $appointmentId = $this->employee?->currentAppointment?->appointment_id;

            if (! $appointmentId) {
                session()->flash('error', 'Appointment not found.');
                return;
            }

            // ✅ IMPORTANT: this is the value that must go into employer_cadre_subjects.employee_id
            // If your table expects something else (like NIC or an internal employee table id),
            // change this line.
            $employeeId = $this->employee?->people_id;

            if (! $employeeId) {
                session()->flash('error', 'Employee ID not found.');
                return;
            }

            // ✅ Always create/update SLEAS record
            $this->sleasData = EducationAdministratorService::updateOrCreate(
                ['appointment_id' => $appointmentId, 'employee_id' => $employeeId],
                [
                    'category_id' => $this->sleasCategory,
                    'subject'     => $this->sleasSubject,

                    // Ensure employee_id is set on insert/update too
                    'employee_id'        => $employeeId,
                ]
            );

            // ✅ Only create/update cadre row when both values exist
            $hasCadreInputs = ! empty($this->cadreSubject) && ! empty($this->cadreMedium);

            if ($hasCadreInputs) {
                $this->cadreData = EmployerCadreSubject::updateOrCreate(
                    // Use BOTH keys if your table is unique by appointment+employee
                    ['appointment_id' => $appointmentId, 'employee_id' => $employeeId],
                    [
                        'main_subject'       => $this->cadreSubject,
                        'appointment_medium' => $this->cadreMedium,

                        // Ensure employee_id is set on insert/update too
                        'employee_id'        => $employeeId,
                    ]
                );
            }

            session()->flash('success', 'Employment updated successfully.');
            $this->loadData();

        } catch (\Exception $e) {
            // logger()->error($e->getMessage());
            session()->flash('error', 'Something went wrong while updating the record. ' . $e->getMessage());
        }

        return $this->redirect(url()->previous(), navigate: true);
    }

    public function resetFields(): void
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.sleas.profile.sleas-data');
    }
}
