<?php

namespace App\Livewire\Employees;

use Exception;
use App\Models\People;
use Livewire\Component;
use App\Models\DistrictsList;
use Illuminate\Support\Facades\DB;
use App\Rules\UniqueEmailAcrossTables;
use App\Rules\UniquePhoneAcrossTables;

class ContactInformation extends Component
{
    public $peopleId;
    public $canEdit;
    public $employee;
    public $showModalContactInfo = false; // control modal visibility

    // -------------------------
    // Contact Details
    // -------------------------
    public $contact, $email;

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

    public function mount($peopleId)
    {
        $this->employee = People::where('people_id', $peopleId)->first();
        // $this->canEdit = $canEdit;

        $this->contact = $this->employee->phone;
        $this->email = $this->employee->email;
    }

    public function editContactInfo()
    {
        // Direct validation
        $validated = $this->validate([
            'contact' => [
                'required',
                'string',
                'min:10',
                'max:10',
                new UniquePhoneAcrossTables($this->employee?->people_id),
            ],
            'email' => [
                'required',
                'email',
                new UniqueEmailAcrossTables($this->employee?->people_id),
            ],
        ]);

        DB::beginTransaction(); // Start transaction
        try {
            // Update directly
            $this->employee->update([
                'phone' => $this->contact,
                'email' => $this->email,
            ]);

            DB::commit(); // Commit only if all operations succeeded

            session()->flash('success', 'Contact information updated successfully.');
            // Close modal
            $this->showModalContactInfo = false;
            return $this->redirect(url()->previous(), navigate: true);
        } catch (Exception $e) {
            DB::rollBack(); // Rollback on any failure
            session()->flash('error', 'An error occurred while updating contact information: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employees.contact-information');
    }
}
