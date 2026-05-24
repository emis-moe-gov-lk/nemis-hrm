<?php

namespace App\Livewire\Employees;

use Exception;
use App\Models\People;
use Livewire\Component;
use App\Models\GnDivision;
use App\Models\DistrictsList;
use Illuminate\Support\Facades\DB;
use App\Models\DivisionalSecretariatOffice;
use Illuminate\Http\Request;
use App\Http\Controllers\PeopleController;

class LocationInformation extends Component
{
    public string $peopleId;
    public bool $canEdit = false;
    public People $employee;
    public bool $showModalLocationInfo = false;

    // -------------------------
    // Location Details
    // -------------------------
    public ?string $district = null;
    public ?string $divisionalDecretaryOffice = null;
    public ?string $gnDivision = null;
    public ?string $addressLine1 = null;
    public ?string $addressLine2 = null;
    public ?string $addressLine3 = null;
    public ?string $postalCode = null;
    public ?string $latitude = null;
    public ?string $longitude = null;

    // -------------------------
    // Dropdown Options
    // -------------------------
    public array $districtOption = [];
    public array $divisionalSecretaryofficeOption = [];
    public array $gnDivisionOption = [];

    // -------------------------
    // Validation Rules
    // -------------------------
    protected function rules()
    {
        return [
            'district' => 'required|exists:districts_lists,district_id',
            'divisionalDecretaryOffice' => 'required|exists:divisional_secretariat_offices,dso_id',
            'gnDivision' => 'required|exists:gn_divisions,gn_division_id',
            'addressLine1' => 'required|string|max:255',
            'addressLine2' => 'required|string|max:255',
            'addressLine3' => 'nullable|string|max:255',
            'postalCode' => 'required|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    public function updated(string $property)
    {
        $this->validateOnly($property);
    }

    public function mount(string $peopleId)
    {
        $this->employee = People::where('people_id', $peopleId)->firstOrFail();
        //$this->canEdit = $canEdit;

        // Load stored values
        $this->district = $this->employee->district_id;
        $this->divisionalDecretaryOffice = $this->employee->gnDivision->dso_id ?? '';
        $this->gnDivision = $this->employee->gn_division_id;

        $this->addressLine1 = $this->employee->address_line1;
        $this->addressLine2 = $this->employee->address_line2;
        $this->addressLine3 = $this->employee->address_line3;
        $this->postalCode = $this->employee->postal_code;
        $this->latitude = $this->employee->latitude;
        $this->longitude = $this->employee->longitude;

        // Initial dropdown data
        $this->districtOption = DistrictsList::orderBy('district_name', 'asc')->get()->all();
        $this->divisionalSecretaryofficeOption = DivisionalSecretariatOffice::where('district_id', $this->district)->orderBy('dso_name', 'asc')->get()->all();
        $this->gnDivisionOption = GnDivision::where('dso_id', $this->divisionalDecretaryOffice)->orderBy('gn_division_name', 'asc')->get()->all();
    }

    public function updatedDistrict(mixed $value)
    {
        $this->divisionalSecretaryofficeOption =
            DivisionalSecretariatOffice::where('district_id', $value)->orderBy('dso_name', 'asc')->get()->all();

        // Reset downstream fields
        $this->divisionalDecretaryOffice = '';
        $this->gnDivision = '';
        $this->gnDivisionOption = [];
    }

    public function updatedDivisionalDecretaryOffice(mixed $value)
    {
        $this->gnDivisionOption =
            GnDivision::where('dso_id', $value)->orderBy('gn_division_name')->get()->all();

        $this->gnDivision = '';
    }

    public function editLocationInfo()
    {
        $request = new Request([
            'people_id' => $this->employee->people_id,
            'district_id' => $this->district,
            'gn_division_id' => $this->gnDivision,
            'address_line1' => $this->addressLine1,
            'address_line2' => $this->addressLine2,
            'address_line3' => $this->addressLine3,
            'postal_code' => $this->postalCode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        $controller = new PeopleController();
        $response = $controller->updateResidentialAddress($request);
        $result = $response->getData();

        if ($result->status === 'success') {
            session()->flash('success', $result->message ?? 'Location information updated successfully.');
            $this->showModalLocationInfo = false;
            return $this->redirect(url()->previous(), navigate: true);
        } else {
            if ($result->status === 'validation_error') {
                foreach ($result->errors as $field => $messages) {
                    $propertyMap = [
                        'district_id' => 'district',
                        'gn_division_id' => 'gnDivision',
                        'address_line1' => 'addressLine1',
                        'address_line2' => 'addressLine2',
                        'address_line3' => 'addressLine3',
                        'postal_code' => 'postalCode',
                        'latitude' => 'latitude',
                        'longitude' => 'longitude',
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
        return view('livewire.employees.location-information');
    }
}
