<?php

namespace App\Livewire\Employees;

use Exception;
use Carbon\Carbon;
use App\Models\Title;
use App\Models\People;
use Livewire\Component;
use App\Models\Religion;
use App\Models\Ethnicity;
use App\Helpers\NicHelper;
use App\Models\GenderList;
use App\Models\CivilStatus;
use App\Rules\UniqueHashedNic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PeopleController;

class PersonalCultural extends Component
{
    public string $peopleId;
    public bool $canEdit = false;
    public People $employee;
    public bool $showModalPersonalInfo = false; // control modal visibility

    // -------------------------
    // Personal Details
    // -------------------------
    public ?string $nic = null;
    public ?string $title = null;
    public ?string $fullName = null;
    public ?string $gender = null;
    public ?string $birthday = null;
    public ?string $religion = null;
    public ?string $ethnicity = null;
    public ?string $civilStatus = null;

    // -------------------------
    // Dropdown Options
    // -------------------------
    public array $titleOptions = [];
    public array $religionOptions = [];
    public array $genderOptions = [];
    public array $ethnicityOptions = [];
    public array $civilStatusOptions = [];

    // -------------------------
    // Validation Rules
    // -------------------------
    protected function rules()
    {
        return [
            'nic' => ['required', 'string', 'min:10', 'max:12', new UniqueHashedNic()],
            'title' => 'required|string',
            'fullName' => 'required|string|max:255',
            'gender' => 'required|string',
            'birthday' => 'required|date',
            'religion' => 'required|string',
            'ethnicity' => 'required|string',
            'civilStatus' => 'required|string',
        ];
    }

    // 🔹 Live validation as user types
    public function updated(string $propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount(string $peopleId)
    {
        $this->employee = People::where('people_id', $peopleId)->first();
        // $this->canEdit = $canEdit;

        $this->titleOptions = Title::active()->get()->all();
        $this->genderOptions = GenderList::active()->get()->all();
        $this->religionOptions = Religion::active()->get()->all();
        $this->ethnicityOptions = Ethnicity::active()->get()->all();
        $this->civilStatusOptions = CivilStatus::active()->get()->all();

        $this->nic = $this->employee->nic;
        $this->title = $this->employee->title_id;
        $this->fullName = $this->employee->full_name;
        $this->gender = $this->employee->gender_id;
        $this->birthday = Carbon::parse($this->employee->date_of_birth)->format('Y-m-d');
        $this->religion = $this->employee->religion_id;
        $this->ethnicity = $this->employee->ethnicity_id;
        $this->civilStatus = $this->employee->civil_status_id;
    }

    public function editPersonalInfo()
    {
        $request = new Request([
            'people_id' => $this->employee->people_id,
            'title_id' => $this->title,
            'nic' => $this->nic,
            'full_name' => $this->fullName,
            'gender_id' => $this->gender,
            'date_of_birth' => $this->birthday,
            'religion_id' => $this->religion,
            'ethnicity_id' => $this->ethnicity,
            'civil_status_id' => $this->civilStatus,
        ]);

        $controller = new PeopleController();
        $response = $controller->updatePersonal($request);
        $result = $response->getData();

        if ($result->status === 'success') {
            session()->flash('success', $result->message ?? 'Personal information updated successfully.');
            $this->showModalPersonalInfo = false;
            return $this->redirect(url()->previous(), navigate: true);
        } else {
            if ($result->status === 'validation_error') {
                foreach ($result->errors as $field => $messages) {
                    $propertyMap = [
                        'full_name' => 'fullName',
                        'gender_id' => 'gender',
                        'date_of_birth' => 'birthday',
                        'religion_id' => 'religion',
                        'ethnicity_id' => 'ethnicity',
                        'civil_status_id' => 'civilStatus',
                        'title_id' => 'title',
                    ];
                    $propertyName = $propertyMap[$field] ?? $field;
                    $this->addError($propertyName, $messages[0]);
                }
            } else {
                session()->flash('error', $result->message ?? 'An error occurred.');
            }
        }
    }

    public function render()
    {
        return view('livewire.employees.personal-cultural');
    }
}
