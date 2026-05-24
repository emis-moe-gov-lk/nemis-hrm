<?php

namespace App\Livewire\Employees;

use App\Models\EducationalQualificationGrade;
use App\Models\People;
use Livewire\Component;
use App\Models\EducationQualification;
use App\Models\PeopleEducationQualification;
use App\Http\Controllers\PeopleEduQualificationsController;
use Illuminate\Http\Request;

class EducationalQualification extends Component
{
    public string $peopleId;
    public bool $canCreate = false;
    public bool $canDelete = false;
    public People $employee;

    public array $educationQualificationList = [];
    public array $gradeOption = [];

    public ?string $qualification = null;
    public ?string $institution = null;
    public ?string $effectiveDate = null;
    public ?string $grade = null;
    public ?string $description = null;

    public bool $showDeleteModal = false;
    public ?string $qualificationIdToDelete = null;

    // -------------------------
    // Validation Rules
    // -------------------------
    protected function rules()
    {
        return [
            'qualification' => 'required|string',
            'institution' => 'required|string|max:255',
            'effectiveDate' => 'required|date',
            'grade' => 'required|string',
            'description' => 'required|string|max:255',
        ];
    }

    // -------------------------
    // Live Validation on Field Update
    // -------------------------
    public function updated(string $propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public bool $showModal = false; // control modal visibility

    public function mount(string $peopleId)
    {
        $this->employee = People::where('people_id', $peopleId)->firstOrFail();
        //$this->canCreate = $canCreate;
        //$this->canDelete = $canDelete;

        $this->educationQualificationList = EducationQualification::Active()->get()->all();

        // Static grade options
        $this->gradeOption = EducationalQualificationGrade::Active()->pluck('grade', 'grade_id')->all();
    }

    public function save()
    {
        $request = new Request([
            'people_id' => $this->employee->people_id,
            'qualifications_id' => $this->qualification,
            'institution' => $this->institution,
            'effective_date' => $this->effectiveDate,
            'grade' => $this->grade,
            'description' => $this->description,
        ]);

        $controller = new PeopleEduQualificationsController();
        $response = $controller->createQualification($request);
        $result = $response->getData();

        if ($result->status === 'success') {
            session()->flash('success', $result->message ?? 'Qualification added successfully.');
            // Reset form fields
            $this->reset(['qualification', 'institution', 'effectiveDate', 'grade', 'description']);
            // Close modal
            $this->showModal = false;
        } else {
            if ($result->status === 'validation_error') {
                foreach ($result->errors as $field => $messages) {
                    $propertyMap = [
                        'qualifications_id' => 'qualification',
                        'institution' => 'institution',
                        'effective_date' => 'effectiveDate',
                        'grade' => 'grade',
                        'description' => 'description',
                    ];
                    $propertyName = $propertyMap[$field] ?? $field;
                    $this->addError($propertyName, $messages[0]);
                }
            } else {
                session()->flash('error', $result->message ?? 'An error occurred.');
            }
        }
    }

    public function confirmDelete(string $id)
    {
        $this->qualificationIdToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if (!$this->qualificationIdToDelete) return;

        $request = new Request(['id' => $this->qualificationIdToDelete]);

        $controller = new PeopleEduQualificationsController();
        $response = $controller->deleteQualification($request);
        $result = $response->getData();

        if ($result->status === 'success') {
            session()->flash('success', $result->message ?? 'Qualification deleted successfully.');
        } else {
            session()->flash('error', $result->message ?? 'An error occurred.');
        }

        $this->showDeleteModal = false;
        $this->qualificationIdToDelete = null;
    }

    public function render()
    {
        $qualificationList = PeopleEducationQualification::where('active_status', '1')->where('people_id', $this->employee->people_id)->get();
        return view('livewire.employees.educational-qualification', compact('qualificationList'));
    }
}
