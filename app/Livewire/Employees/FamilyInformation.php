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
use App\Http\Controllers\PeopleController;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FamilyInformation extends Component
{
    public string $peopleId;
    public bool $canCreate = false;
    public bool $canDelete = false;
    public ?People $employee = null;

    public bool $isCheckNIC = false;
    public bool $isPeopleFound = false;
    public ?People $peopleData = null;

    public bool $showModalSpouseReg = false;
    public bool $showModalChildReg = false;
    public bool $showModalDivorce = false;

    // spouse fields
    public ?string $nic = null;
    public ?string $title = null;
    public ?string $fullName = null;
    public ?string $gender = null;
    public ?string $birthday = null;
    public ?string $religion = null;
    public ?string $ethnicity = null;
    public ?string $email = null;
    public ?string $contact = null;
    public ?string $marriedDate = null;
    public ?string $marriedCfNo = null;

    // child fields
    public ?string $family_id = null;
    public ?string $childName = null;
    public ?string $childDob = null;
    public ?string $childGender = null;
    public ?string $birthCertificateNo = null;
    public bool $childHealthCondition = true;

    // divorce fields
    public ?string $divorceFamilyId = null;
    public ?string $divorceDate = null;

    // delete state
    public ?string $spouseIdToDelete = null;
    public ?string $childIdToDelete = null;

    // dropdowns
    public array $titleOptions = [];
    public array $religionOptions = [];
    public array $genderOptions = [];
    public array $ethnicityOptions = [];
    public array $civilStatusOptions = [];
    public array $healthConditionOptions = [];

    protected function rules(): array
    {
        return [
            'nic' => [
                'required',
                'string',
                'regex:/^(\d{9}[vVxX]|\d{12})$/',
                function ($attribute, $value, $fail) {
                    if (!NicHelper::isValid($value)) {
                        $fail('The provided NIC is not a valid format.');
                    }
                }
            ],
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

    public function updated(string $property): void
    {
        $this->validateOnly($property);
    }

    public function mount(string $peopleId): void
    {
        $this->employee = People::where('people_id', $peopleId)->firstOrFail();
        $this->titleOptions = Title::all()->all();
        $this->genderOptions = GenderList::all()->all();
        $this->religionOptions = Religion::all()->all();
        $this->ethnicityOptions = Ethnicity::all()->all();
        $this->civilStatusOptions = CivilStatus::all()->all();

        $this->healthConditionOptions = [true => 'Yes', false => 'No'];
        $this->childHealthCondition = true;
    }

    public function updatedNic(): void
    {
        $this->resetNICState();
    }

    private function resetNICState(): void
    {
        $this->isCheckNIC = false;
        $this->isPeopleFound = false;
        $this->peopleData = null;
    }

    public function checkNIC(): void
    {
        $this->resetNICState();
        $this->validateOnly('nic');

        $nic = NicHelper::normalize($this->nic);

        if (strtoupper($this->employee->nic) === $nic) {
            $this->addError('nic', 'You cannot enter your own NIC.');
            return;
        }

        $this->isCheckNIC = true;
        $found = People::where('nic_hash', NicHelper::hash($nic))->first();

        if ($found) {
            $this->isPeopleFound = true;
            $this->peopleData = $found;
        }
    }

    public function spouseReg()
    {
        if (!$this->isCheckNIC) {
            $this->addError('nic', 'Please click "Check" before registering.');
            return;
        }

        $peopleController = new PeopleController();
        DB::beginTransaction();
        try {
            $spouseId = null;

            if ($this->peopleData) {
                $this->validate([
                    'marriedDate' => 'required|date',
                    'marriedCfNo' => 'required|string|max:50',
                ]);
                $spouseId = $this->peopleData->people_id;
            } else {
                $peopleRequest = new Request([
                    'nic' => $this->nic,
                    'title_id' => $this->title,
                    'full_name' => $this->fullName,
                    'gender_id' => $this->gender,
                    'date_of_birth' => $this->birthday,
                    'religion_id' => $this->religion,
                    'ethnicity_id' => $this->ethnicity,
                    'civil_status_id' => 'C02',
                    'email' => $this->email,
                    'phone' => $this->contact,
                    'address_line1' => $this->employee->address_line1,
                    'address_line2' => $this->employee->address_line2,
                    'address_line3' => $this->employee->address_line3,
                    'postal_code' => $this->employee->postal_code,
                ]);

                $response = $peopleController->createPeople($peopleRequest);
                $responseData = json_decode($response->getContent(), true);

                if ($responseData['status'] !== 'success') {
                    if ($responseData['status'] === 'validation_error') {
                        foreach ($responseData['errors'] as $field => $messages) {
                            $this->addError($field, $messages[0]);
                        }
                        return;
                    }
                    throw new Exception($responseData['message'] ?? 'Failed to register spouse');
                }
                $spouseId = $responseData['data']['people_id'];
            }

            $familyRequest = new Request([
                'member_a_id' => $this->employee->people_id,
                'member_b_id' => $spouseId,
                'married_date' => $this->marriedDate,
                'married_cf_no' => $this->marriedCfNo,
            ]);

            $familyResponse = $peopleController->familyCreate($familyRequest);
            $familyResponseData = json_decode($familyResponse->getContent(), true);

            if ($familyResponseData['status'] !== 'success') {
                if ($familyResponseData['status'] === 'error') {
                    $this->addError('nic', $familyResponseData['message']);
                    return;
                }
                throw new Exception($familyResponseData['message'] ?? 'Failed to link family');
            }

            DB::commit();
            session()->flash('success', 'Spouse successfully registered and linked!');
            $this->resetSpouseForm();
            $this->showModalSpouseReg = false;
            return $this->redirect(url()->previous(), navigate: true);

        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    private function resetSpouseForm(): void
    {
        $this->reset([
            'nic', 'title', 'fullName', 'gender', 'birthday', 'religion',
            'ethnicity', 'email', 'contact', 'marriedDate', 'marriedCfNo'
        ]);
        $this->resetNICState();
    }

    public function openDivorceModal(string $family_id): void
    {
        $this->divorceFamilyId = $family_id;
        $this->divorceDate = now()->format('Y-m-d');
        $this->showModalDivorce = true;
    }

    public function recordDivorce()
    {
        $this->validate([
            'divorceDate' => 'required|date|after_or_equal:marriedDate',
        ]);

        try {
            $family = Family::where('family_id', $this->divorceFamilyId)->firstOrFail();
            $family->update([
                'divorce_date' => $this->divorceDate,
                'active_status' => 0,
            ]);

            session()->flash('success', 'Divorce recorded successfully.');
            $this->showModalDivorce = false;
            return $this->redirect(url()->previous(), navigate: true);
        } catch (Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function openChildModal(string $family_id): void
    {
        $this->family_id = $family_id;
        $this->showModalChildReg = true;
    }

    public function childReg()
    {
        $request = new Request([
            'family_id' => $this->family_id,
            'child_name' => $this->childName,
            'date_of_birth' => $this->childDob,
            'gender_id' => $this->childGender,
            'birth_fc_no' => $this->birthCertificateNo,
            'health_condition' => $this->childHealthCondition,
        ]);

        $controller = new PeopleController();
        $response = $controller->childReg($request);
        $result = $response->getData();

        if ($result->status === 'success') {
            session()->flash('success', $result->message ?? 'Child registered successfully.');
            $this->resetChildForm();
            $this->showModalChildReg = false;
            return $this->redirect(url()->previous(), navigate: true);
        } else {
            if ($result->status === 'validation_error') {
                foreach ($result->errors as $field => $messages) {
                    $map = [
                        'child_name' => 'childName',
                        'date_of_birth' => 'childDob',
                        'gender_id' => 'childGender',
                        'birth_fc_no' => 'birthCertificateNo',
                        'health_condition' => 'childHealthCondition'
                    ];
                    $wireField = $map[$field] ?? $field;
                    foreach ($messages as $message) {
                        $this->addError($wireField, $message);
                    }
                }
            } else {
                session()->flash('error', $result->message);
            }
        }
    }

    private function resetChildForm(): void
    {
        $this->reset(['childName', 'childDob', 'childGender', 'birthCertificateNo', 'childHealthCondition']);
    }

    public function render(): View
    {
        $familyList = Family::where('member_a_id', $this->employee->people_id)
            ->orWhere('member_b_id', $this->employee->people_id)
            ->get();

        $familyIdList = $familyList->pluck('family_id');
        $familyMemberList = FamilyMember::whereIn('family_id', $familyIdList)->get();

        return view('livewire.employees.family-information', compact('familyList', 'familyMemberList'));
    }

    public function confirmDeleteSpouse(string $rowId): void
    {
        $this->spouseIdToDelete = $rowId;
        $this->dispatch('modal-show', name: 'delete-spouse-confirmation');
    }

    public function deleteSpouse(): void
    {
        if (!$this->spouseIdToDelete) return;

        $request = new Request([
            'family_id' => $this->spouseIdToDelete,
        ]);

        $controller = new PeopleController();
        $response = $controller->deleteFamily($request);
        $result = $response->getData();

        $this->spouseIdToDelete = null;
        $this->dispatch('modal-close', name: 'delete-spouse-confirmation');

        if ($result->status === 'success') {
            session()->flash('success', $result->message ?? 'Family deleted successfully.');
            $this->redirect(url()->previous(), navigate: true);
        } else {
            session()->flash('error', $result->message ?? 'An error occurred while deleting the family.');
        }
    }

    public function confirmDeleteChild(string $rowId): void
    {
        $this->childIdToDelete = $rowId;
        $this->dispatch('modal-show', name: 'delete-child-confirmation');
    }

    public function deleteChild(): void
    {
        if (!$this->childIdToDelete) return;

        $child = FamilyMember::find($this->childIdToDelete);
        if (!$child) {
            session()->flash('error', 'Child record not found.');
            return;
        }

        $request = new Request([
            'family_id' => $child->family_id,
            'id' => $this->childIdToDelete,
        ]);

        $controller = new PeopleController();
        $response = $controller->removeChild($request);
        $result = $response->getData();

        $this->childIdToDelete = null;
        $this->dispatch('modal-close', name: 'delete-child-confirmation');

        if ($result->status === 'success') {
            session()->flash('success', $result->message ?? 'Child removed successfully.');
            $this->redirect(url()->previous(), navigate: true);
        } else {
            session()->flash('error', $result->message ?? 'An error occurred during child removal.');
        }
    }

    public function terminateRelationship(string $family_id)
    {
        $request = new Request(['family_id' => $family_id]);
        $controller = new PeopleController();
        $response = $controller->terminateFamily($request);
        $result = $response->getData();

        if ($result->status === 'success') {
            session()->flash('success', $result->message ?? 'Relationship terminated successfully.');
            return $this->redirect(url()->previous(), navigate: true);
        } else {
            session()->flash('error', $result->message ?? 'An error occurred.');
        }
    }

    public function reactivateRelationship(string $family_id)
    {
        $request = new Request(['family_id' => $family_id]);
        $controller = new PeopleController();
        $response = $controller->reactivateFamily($request);
        $result = $response->getData();

        if ($result->status === 'success') {
            session()->flash('success', $result->message ?? 'Relationship reactivated successfully.');
            return $this->redirect(url()->previous(), navigate: true);
        } else {
            session()->flash('error', $result->message ?? 'An error occurred.');
        }
    }
}
