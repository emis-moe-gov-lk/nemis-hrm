<?php

namespace App\Livewire\Employees;

use Exception;
use App\Models\People;
use Livewire\Component;
use App\Models\BloodGroup;
use Illuminate\Support\Facades\DB;

class HealthInformation extends Component
{
    public $peopleId;
    public $canEdit;
    public $employee;
    public $showModalHealthInfo = false; // control modal visibility

    // -------------------------
    // Personal Details
    // -------------------------
    public $bloodGroup, $healthCondition, $healthProblem;

    // -------------------------
    // Dropdown Options
    // -------------------------
    public $bloodGroupOptions = [], $healthConditionOptions = [];

    // -------------------------
    // Validation Rules
    // -------------------------
    protected function rules()
    {
        return [
            'bloodGroup' => 'required|string',
            'healthCondition' => 'required|boolean',
            'healthProblem' => 'required_if:healthCondition,false|string|max:1000',
        ];
    }

    public function mount($peopleId)
    {
        $this->employee = People::where('people_id', $peopleId)->first();
        //$this->canEdit = $canEdit;

        $this->bloodGroupOptions = BloodGroup::all();
        $this->healthConditionOptions = [true => 'Yes', false => 'No'];

        $this->bloodGroup = $this->employee->blood_group_id;
        $this->healthCondition = $this->employee->health_condition;
        $this->healthProblem = $this->employee->health_problem;
    }

    public function updatedHealthCondition()
    {
        if ($this->healthCondition == true) {
            $this->healthProblem = null;
        }
    }

    public function editHealthInfo()
    {
        // Direct validation
        $validated = $this->validate([
            'bloodGroup' => 'required|string',
            'healthCondition' => 'required|boolean',
            'healthProblem' => 'nullable|string|max:1000',
        ]);


        DB::beginTransaction(); // Start transaction
        try {
            // Update directly
            $this->employee->update([
                'blood_group_id' => $this->bloodGroup,
                'health_condition' => $this->healthCondition,
                'health_problem' => $this->healthProblem,
            ]);

            DB::commit(); // Commit only if all operations succeeded
            session()->flash('success', 'Health information updated successfully.');
            // Close modal
            $this->showModalHealthInfo = false;
            return $this->redirect(url()->previous(), navigate: true);
        } catch (Exception $e) {
            DB::rollBack(); // Rollback on any failure
            session()->flash('error', 'An error occurred while updating health information: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employees.health-information');
    }
}
