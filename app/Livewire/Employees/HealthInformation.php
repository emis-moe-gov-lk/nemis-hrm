<?php

namespace App\Livewire\Employees;

use Exception;
use App\Models\People;
use Livewire\Component;
use App\Models\BloodGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\PeopleController;


class HealthInformation extends Component
{
    public string $peopleId;
    public bool $canEdit = false;
    public People $employee;
    public bool $showModalHealthInfo = false; // control modal visibility

    // -------------------------
    // Health Details
    // -------------------------
    public ?string $bloodGroup = null;
    public ?string $healthCondition = '';
    public ?string $healthProblem = null;

    // -------------------------
    // Dropdown Options
    // -------------------------
    public array $bloodGroupOptions = [];
    public array $healthConditionOptions = [];

    // -------------------------
    // Validation Rules
    // -------------------------
    protected function rules()
    {
        return [
            'bloodGroup' => 'required|string',
            'healthCondition' => 'required|boolean',
            'healthProblem' => 'required_if:healthCondition,0,false|string|max:1000',
        ];
    }

    public function mount(string $peopleId)
    {
        $this->employee = People::where('people_id', $peopleId)->first();
        //$this->canEdit = $canEdit;

        $this->bloodGroupOptions = BloodGroup::all()->all();
        $this->healthConditionOptions = ['1' => 'Yes', '0' => 'No'];

        $this->bloodGroup = $this->employee->blood_group_id;
        $this->healthCondition = $this->employee->health_condition !== null ? (string)$this->employee->health_condition : '';
        $this->healthProblem = $this->employee->health_problem;
    }

    public function updatedHealthCondition()
    {
        if ($this->healthCondition === '1') {
            $this->healthProblem = null;
        }
    }

    public function editHealthInfo()
    {
        $request = new Request([
            'people_id' => $this->employee->people_id,
            'blood_group_id' => $this->bloodGroup,
            'health_condition' => $this->healthCondition,
            'health_problem' => $this->healthProblem,
        ]);

        $controller = new PeopleController();
        $response = $controller->updateHealth($request);
        $result = $response->getData();

        if ($result?->status === 'success') {
            session()->flash('success', $result?->message ?? 'Health information updated successfully.');
            $this->showModalHealthInfo = false;
            return $this->redirect(url()->previous(), navigate: true);
        } else {
            if ($result?->status === 'validation_error') {
                foreach ($result?->errors as $field => $messages) {
                    $propertyMap = [
                        'blood_group_id' => 'bloodGroup',
                        'health_condition' => 'healthCondition',
                        'health_problem' => 'healthProblem',
                    ];
                    $propertyName = $propertyMap[$field] ?? $field;
                    $this->addError($propertyName, $messages[0]);
                }
            } else {
                session()->flash('error', $result?->message ?? 'An error occurred.');
            }
        }
    }

    public function render()
    {
        return view('livewire.employees.health-information');
    }
}
