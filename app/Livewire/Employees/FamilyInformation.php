<?php

namespace App\Livewire\Employees;

use Exception;
use Livewire\Component;
use App\Models\{
    Title,
    Family,
    People,
    Religion,
    Ethnicity,
    GenderList,
    CivilStatus,
    FamilyMember
};
use App\Rules\UniqueHashedNic;
use App\Rules\UniquePhoneAcrossTables;
use Illuminate\Support\Facades\DB;
use App\Helpers\NicHelper;

class FamilyInformation extends Component
{
    public $peopleId;
    public $canCreate;
    public $canDelete;
    public $employee;

    public $isCheckNIC = false;
    public $isPeopleFound = false;
    public $peopleData = null;

    public $showModalSpouseReg = false;
    public $showModalChildReg = false;

    // spouse fields
    public $nic, $title, $fullName, $gender, $birthday, $religion;
    public $ethnicity, $email, $contact, $marriedDate, $marriedCfNo;

    // child fields
    public $family_id, $childName, $childDob, $childGender, $birthCertificateNo, $childHealthCondition;

    // dropdowns
    public $titleOptions = [], $religionOptions = [], $genderOptions = [];
    public $ethnicityOptions = [], $civilStatusOptions = [];
    public $healthConditionOptions = [];

    protected function rules()
    {
        return [
            'nic' => ['required', 'string', 'regex:/^(\d{9}[vVxX]|\d{12})$/'],
            'title' => 'required|string',
            'fullName' => 'required|string|max:255',
            'gender' => 'required|string',
            'birthday' => 'required|date',
            'religion' => 'required|string',
            'ethnicity' => 'required|string',
            'contact' => ['required', 'string', 'regex:/^0\d{9}$/', new UniquePhoneAcrossTables()],
            'email' => 'required|email|unique:people,email',
            'marriedDate' => 'required|date',
            'marriedCfNo' => 'required|string|max:10',

            'childName' => 'required|string|max:255',
            'childDob' => 'required|date',
            'childGender' => 'required|string',
            'birthCertificateNo' => 'required|string|max:10',
            'childHealthCondition' => 'required|boolean',
        ];
    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function mount($peopleId)
    {
        $this->employee = People::where('people_id', $peopleId)->firstOrFail();
        //$this->canCreate = $canCreate;
        //$this->canDelete = $canDelete;

        $this->titleOptions = Title::all();
        $this->genderOptions = GenderList::all();
        $this->religionOptions = Religion::all();
        $this->ethnicityOptions = Ethnicity::all();
        $this->civilStatusOptions = CivilStatus::all();

        $this->healthConditionOptions = [true => 'Yes', false => 'No'];
        $this->childHealthCondition = true;
    }

    public function updatedNic()
    {
        $this->resetNICState();
    }

    private function resetNICState()
    {
        $this->isCheckNIC = false;
        $this->isPeopleFound = false;
        $this->peopleData = null;
    }

    public function checkNIC()
    {
        $this->resetNICState();

        $this->validateOnly('nic');

        $nic = NicHelper::normalize($this->nic);

        // prevent own NIC
        if (strtoupper($this->employee->nic) === $nic) {
            $this->addError('nic', 'You cannot enter your own NIC.');
            return;
        }

        $this->isCheckNIC = true;

        // search existing People
        $found = People::where('nic_hash', NicHelper::hash($nic))->first();

        if ($found) {
            $this->isPeopleFound = true;
            $this->peopleData = $found;
        }
    }

    /** ------------------ SPOUSE REGISTRATION ------------------ **/
    public function spouseReg()
    {
        if (!$this->isCheckNIC) {
            $this->addError('nic', 'Please click "Check" before registering.');
            return;
        }

        // CASE 1: existing person found by NIC
        if ($this->peopleData) {
            // check existing marriage
            if (
                Family::where('member_a_id', $this->peopleData->people_id)
                ->orWhere('member_b_id', $this->peopleData->people_id)
                ->exists()
            ) {
                $this->addError('nic', 'This person is already registered in a family.');
                return;
            }


            Family::create([
                'member_a_id' => $this->employee->people_id,
                'member_b_id' => $this->peopleData->people_id,
                'married_date' => $this->marriedDate,
                'married_cf_no' => $this->marriedCfNo,
            ]);

            session()->flash('success', 'Spouse linked successfully!');
            $this->showModalSpouseReg = false;
            $this->resetSpouseForm();
            return;
        }

        // CASE 2: new person (not found by NIC)
        $validated = $this->validate(
            [
                'nic' => ['required', 'string', 'regex:/^(\d{9}[vVxX]|\d{12})$/', new UniqueHashedNic()],
                'title' => 'required|string',
                'fullName' => 'required|string|max:255',
                'gender' => 'required|string',
                'birthday' => 'required|date',
                'religion' => 'required|string',
                'ethnicity' => 'required|string',
                'email' => 'required|email|max:255',
                'contact' => 'required|string|max:15',
                'marriedDate' => 'required|date',
                'marriedCfNo' => 'required|string|max:20',
            ]
        );

        DB::beginTransaction();
        try {
            $initials = People::generateInitials($this->fullName);

            $nic = NicHelper::normalize($this->nic);

            $spouse = People::create([
                'nic' => $nic,
                'nic_hash' => NicHelper::hash($nic),
                'title_id' => $this->title,
                'full_name' => ucwords(strtolower($this->fullName)),
                'name_with_initials' => $initials,
                'gender_id' => $this->gender,
                'date_of_birth' => $this->birthday,
                'religion_id' => $this->religion,
                'ethnicity_id' => $this->ethnicity,
                'civil_status_id' => 'C02',
                'email' => strtolower($this->email),
                'phone' => $this->contact,
                'address_line1' => $this->employee->address_line1,
                'address_line2' => $this->employee->address_line2,
                'address_line3' => $this->employee->address_line3,
                'postal_code' => $this->employee->postal_code,
                'profile_picture' => 'default.png',
            ]);

            Family::create([
                'member_a_id' => $this->employee->people_id,
                'member_b_id' => $spouse->people_id,
                'married_date' => $this->marriedDate,
                'married_cf_no' => $this->marriedCfNo,
            ]);

            DB::commit();
            session()->flash('success', 'New spouse created successfully!');
            $this->showModalSpouseReg = false;
            return $this->redirect(url()->previous(), navigate: true);
            $this->resetSpouseForm();
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    private function resetSpouseForm()
    {
        $this->reset([
            'nic',
            'title',
            'fullName',
            'gender',
            'birthday',
            'religion',
            'ethnicity',
            'email',
            'contact',
            'marriedDate',
            'marriedCfNo'
        ]);
        $this->resetNICState();
    }

    /** ------------------ CHILD REGISTRATION ------------------ **/
    public function openChildModal($family_id)
    {
        $this->family_id = $family_id;
        $this->showModalChildReg = true;
    }

    public function childReg()
    {
        $validated = $this->validate([
            'childName' => 'required|string|max:255',
            'childDob' => 'required|date',
            'childGender' => 'required|string',
            'birthCertificateNo' => 'required|string|max:10',
            'childHealthCondition' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            FamilyMember::create([
                'family_id' => $this->family_id,
                'child_name' => $this->childName,
                'date_of_birth' => $this->childDob,
                'gender_id' => $this->childGender,
                'birth_fc_no' => $this->birthCertificateNo,
                'health_condition' => $this->childHealthCondition,
            ]);

            DB::commit();
            session()->flash('success', 'Child added successfully!');
            $this->resetChildForm();
            $this->showModalChildReg = false;
            return $this->redirect(url()->previous(), navigate: true);
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    private function resetChildForm()
    {
        $this->reset(['childName', 'childDob', 'childGender', 'birthCertificateNo', 'childHealthCondition']);
    }



    public function render()
    {
        $familyList = Family::where('member_a_id', $this->employee->people_id)
            ->orWhere('member_b_id', $this->employee->people_id)
            ->get();

        $familyIdList = $familyList->pluck('family_id');
        $familyMemberList = FamilyMember::whereIn('family_id', $familyIdList)->get();

        return view('livewire.employees.family-information', compact('familyList', 'familyMemberList'));
    }

    /** ------------------ DELETE ------------------ **/
    public function deleteSpouse($rowId)
    {
        //dd($rowId);
        Family::where('family_id', $rowId)->delete();
        session()->flash('success', 'Spouse deleted successfully!');
        return $this->redirect(url()->previous(), navigate: true);
    }

    public function deleteChild($rowId)
    {
        FamilyMember::where('id', $rowId)->delete();
        session()->flash('success', 'Child deleted successfully!');
        return $this->redirect(url()->previous(), navigate: true);
    }
}
