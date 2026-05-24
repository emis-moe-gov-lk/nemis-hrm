<?php

namespace App\Livewire\Employees;

use Exception;
use App\Models\People;
use Livewire\Component;
use App\Models\DistrictsList;
use Illuminate\Support\Facades\DB;
use App\Rules\UniqueEmailAcrossTables;
use App\Rules\UniquePhoneAcrossTables;
use Illuminate\Http\Request;
use App\Http\Controllers\PeopleController;

class ContactInformation extends Component
{
    public string $peopleId;
    public bool $canEdit = false;
    public People $employee;
    public bool $showModalContactInfo = false; // control modal visibility

    // -------------------------
    // Contact Details
    // -------------------------
    public ?string $contact = null;
    public ?string $email = null;

    // -------------------------
    // Validation Rules
    // -------------------------
    protected function rules()
    {
        return [
            'contact' => [
                'required',
                'string',
                'size:10', // same as min:10 + max:10
                new UniquePhoneAcrossTables($this->employee?->people_id),
            ],

            'email' => [
                'required',
                'email',
                new UniqueEmailAcrossTables($this->employee?->people_id),
            ],
        ];
    }

    // -------------------------
    // Live Validation on Field Update
    // -------------------------
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount(string $peopleId)
    {
        $this->employee = People::where('people_id', $peopleId)->first();
        //$this->canEdit = $canEdit;
    
        $this->contact = $this->employee->phone;
        $this->email = $this->employee->email;
    }

    public function editContactInfo()
    {
        $request = new Request([
            'people_id' => $this->employee->people_id,
            'phone' => $this->contact,
            'email' => $this->email,
        ]);

        $controller = new PeopleController();
        $response = $controller->updateContactDetails($request);
        $result = $response->getData();

        if ($result->status === 'success') {
            session()->flash('success', $result->message ?? 'Contact information updated successfully.');
            $this->showModalContactInfo = false;
            return $this->redirect(url()->previous(), navigate: true);
        } else {
            if ($result->status === 'validation_error') {
                foreach ($result->errors as $field => $messages) {
                    $propertyMap = [
                        'phone' => 'contact',
                        'email' => 'email',
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
        return view('livewire.employees.contact-information');
    }
}
