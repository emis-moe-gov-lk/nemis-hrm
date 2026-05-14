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
use Illuminate\Support\Facades\DB;

class PersonalCultural extends Component
{
    public $peopleId;
    public $canEdit;
    public $employee;
    public $showModalPersonalInfo = false; // control modal visibility

    // -------------------------
    // Personal Details
    // -------------------------
    public $nic, $title, $fullName, $gender, $birthday, $religion;
    public $ethnicity, $civilStatus;

     // -------------------------
    // Dropdown Options
    // -------------------------
    public $titleOptions = [], $religionOptions = [], $genderOptions = [], $ethnicityOptions = [], $civilStatusOptions = [];

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
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($peopleId)
    {
        $this->employee = People::where('people_id', $peopleId)->first();
        // $this->canEdit = $canEdit;

        $this->titleOptions = Title::all();
        $this->genderOptions = GenderList::all();
        $this->religionOptions = Religion::all();
        $this->ethnicityOptions = Ethnicity::all();
        $this->civilStatusOptions = CivilStatus::all();

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
        // Direct validation
        $validated = $this->validate([
            'title' => 'required|string',
            'nic' => ['required', 'string', 'regex:/^(\d{9}[vVxX]|\d{12})$/', new UniqueHashedNic($this->employee->people_id)],
            'fullName' => 'required|string|max:255',
            'gender' => 'required|string',
            'birthday' => 'required|date',
            'religion' => 'required|string',
            'ethnicity' => 'required|string',
            'civilStatus' => 'required|string',
        ]);

        DB::beginTransaction(); // Start transaction
        try {
            $nic = NicHelper::normalize($this->nic);
            // Check NIC validity
            if (!NicHelper::checkNicValid($nic)) {
                $this->addError('nic', 'Invalid NIC number');
                return;
            }
            // Update directly
            $this->employee->update([
                'nic' => $nic,
                //'nic_hash' => hash('sha256', strtoupper($this->nic)),
                'title_id' => $this->title,
                'full_name' => $this->fullName,
                'name_with_initials' => People::generateInitials($this->fullName),
                'gender_id' => $this->gender,
                'date_of_birth' => $this->birthday,
                'religion_id' => $this->religion,
                'ethnicity_id' => $this->ethnicity,
                'civil_status_id' => $this->civilStatus,
            ]);

            DB::commit(); // Commit only if all operations succeeded

            session()->flash('success', 'Personal information updated successfully.');
            // Close modal
            $this->showModalPersonalInfo = false;
            return $this->redirect(url()->previous(), navigate: true);
            
        } catch (Exception $e) {
            DB::rollBack(); // Rollback on any failure
            session()->flash('error', 'An error occurred while updating personal information: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.employees.personal-cultural');
    }
}
