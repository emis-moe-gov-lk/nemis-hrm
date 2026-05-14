<?php

namespace App\Livewire\Employees;

use App\Models\EducationalQualificationGrade;
use App\Models\People;
use Livewire\Component;
use App\Models\EducationQualification;
use App\Models\PeopleEducationQualification;

class EducationalQualification extends Component
{
    public $peopleId;
    public $canCreate;
    public $canDelete;
    public $employee;

    public $educationQualificationList = [];
    public $gradeOption;

    public $qualification, $institution, $effectiveDate, $grade, $description;

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
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public $showModal = false; // control modal visibility

    public function mount($peopleId)
    {
        $this->employee = People::where('people_id', $peopleId)->firstOrFail();
        //$this->canCreate = $canCreate;
        //$this->canDelete = $canDelete;

        $this->educationQualificationList = EducationQualification::Active()->get();

        // Static grade options
        $this->gradeOption = EducationalQualificationGrade::Active()->pluck('grade', 'grade_id');
    }

    public function save()
    {
        $this->validate([
            'qualification' => 'required',
            'institution' => 'required|string|max:255',
            'effectiveDate' => 'required|date',
            'grade' => 'required',
            'description' => 'nullable|string|max:500',
        ]);

        PeopleEducationQualification::create([
            'people_id' => $this->employee->people_id,
            'qualifications_id' => $this->qualification,
            'institution' => $this->institution,
            'effective_date' => $this->effectiveDate,
            'grade' => $this->grade,
            'description' => $this->description,
        ]);

        // Reset form fields
        $this->reset(['qualification', 'institution', 'effectiveDate', 'grade', 'description']);

        // Close modal
        $this->showModal = false;

        session()->flash('success', 'Qualification updated successfully!');
    }

    public function delete($id)
    {
        // Optional: confirm record exists
        $record = PeopleEducationQualification::find($id);

        if ($record) {
            $record->delete();
            session()->flash('success', 'Qualification deleted successfully.');
        } else {
            session()->flash('error', 'Record not found.');
        }
    }
    
    public function render()
    {
        $qualificationList = PeopleEducationQualification::where('active_status', '1')->where('people_id', $this->employee->people_id)->get();
        return view('livewire.employees.educational-qualification', compact('qualificationList'));
    }
}
