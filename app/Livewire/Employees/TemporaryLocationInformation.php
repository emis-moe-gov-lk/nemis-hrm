<?php

namespace App\Livewire\Employees;

use Exception;
use App\Models\People;
use Livewire\Component;
use App\Models\DistrictsList;
use Illuminate\Http\Request;
use App\Http\Controllers\PeopleController;
use Illuminate\Support\Facades\DB;

class TemporaryLocationInformation extends Component
{
    public string $peopleId;
    public bool $canEdit = false;
    public People $employee;
    public bool $showModalTemporaryLocationInfo = false; // control modal visibility

    // -------------------------
    // Location Details
    // -------------------------
    public ?string $tAddressLine1 = null;
    public ?string $tAddressLine2 = null;
    public ?string $tAddressLine3 = null;
    public ?string $tPostalCode = null;

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

    public function mount(string $peopleId)
    {
        $this->employee = People::where('people_id', $peopleId)->firstOrFail();
        //$this->canEdit = $canEdit;

        $this->tAddressLine1 = $this->employee->t_address_line1;
        $this->tAddressLine2 = $this->employee->t_address_line2;
        $this->tAddressLine3 = $this->employee->t_address_line3;
        $this->tPostalCode = $this->employee->t_postal_code;
    }

    public function editTemporaryLocationInfo()
    {
        $request = new Request([
            'people_id' => $this->employee->people_id,
            't_address_line1' => $this->tAddressLine1,
            't_address_line2' => $this->tAddressLine2,
            't_address_line3' => $this->tAddressLine3,
            't_postal_code' => $this->tPostalCode,
        ]);

        $controller = new PeopleController();
        $response = $controller->updateTemperoryAddress($request);
        $result = $response->getData();

        if ($result->status === 'success') {
            session()->flash('success', $result->message ?? 'Temporary location updated successfully.');
            $this->showModalTemporaryLocationInfo = false;
            return $this->redirect(url()->previous(), navigate: true);
        } else {
            if ($result->status === 'validation_error') {
                foreach ($result->errors as $field => $messages) {
                    $propertyMap = [
                        't_address_line1' => 'tAddressLine1',
                        't_address_line2' => 'tAddressLine2',
                        't_address_line3' => 'tAddressLine3',
                        't_postal_code' => 'tPostalCode',
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
        return view('livewire.employees.temporary-location-information');
    }
}
