<?php

namespace App\Livewire\Employees;

use Exception;
use App\Models\People;
use Livewire\Component;
use App\Models\DistrictsList;
use Illuminate\Support\Facades\DB;

class TemporaryLocationInformation extends Component
{
    public $peopleId;
    public $canEdit;
    public $employee;
    public $showModalTemporaryLocationInfo = false; // control modal visibility

    // -------------------------
    // Location Details
    // -------------------------
    public $tAddressLine1, $tAddressLine2, $tAddressLine3, $tPostalCode;

    // -------------------------
    // Validation Rules
    // -------------------------
    protected function rules()
    {
        return [
            'tAddressLine1' => 'nullable|string|max:255',
            'tAddressLine2' => 'nullable|string|max:255',
            'tAddressLine3' => 'nullable|string|max:255',
            'tPostalCode' => 'nullable|string|max:10',
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

        $this->tAddressLine1 = $this->employee->t_address_line1;
        $this->tAddressLine2 = $this->employee->t_address_line2;
        $this->tAddressLine3 = $this->employee->t_address_line3;
        $this->tPostalCode = $this->employee->t_postal_code;
    }

    public function editTemporaryLocationInfo()
    {
        // Direct validation
        $validated = $this->validate([
            'tAddressLine1' => 'nullable|string|max:255',
            'tAddressLine2' => 'nullable|string|max:255',
            'tAddressLine3' => 'nullable|string|max:255',
            'tPostalCode' => 'nullable|string|max:10',
        ]);

        DB::beginTransaction(); // Start transaction
        try {
            // Update directly
            $this->employee->update([
                't_address_line1' => $this->tAddressLine1,
                't_address_line2' => $this->tAddressLine2,
                't_address_line3' => $this->tAddressLine3,
                't_postal_code' => $this->tPostalCode,
            ]);

            DB::commit(); // Commit only if all operations succeeded

            session()->flash('success', 'Temporary location information updated successfully.');
            // Close modal
            $this->showModalTemporaryLocationInfo = false;
            return $this->redirect(url()->previous(), navigate: true);
        } catch (Exception $e) {
            DB::rollBack(); // Rollback on any failure
            session()->flash('error', 'An error occurred while updating temporary location information: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employees.temporary-location-information');
    }
}
