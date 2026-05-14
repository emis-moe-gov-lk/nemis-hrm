<?php

namespace App\Livewire\Employees;

use Exception;
use App\Models\People;
use Livewire\Component;
use App\Models\GnDivision;
use App\Models\DistrictsList;
use Illuminate\Support\Facades\DB;
use App\Models\DivisionalSecretariatOffice;

class LocationInformation extends Component
{
    public $peopleId;
    public $canEdit;
    public $employee;
    public $showModalLocationInfo = false;

    // -------------------------
    // Location Details
    // -------------------------
    public $district;
    public $divisionalDecretaryOffice;
    public $gnDivision;
    public $addressLine1;
    public $addressLine2;
    public $addressLine3;
    public $postalCode;
    public $latitude;
    public $longitude;

    // -------------------------
    // Dropdown Options
    // -------------------------
    public $districtOption = [];
    public $divisionalSecretaryofficeOption = [];
    public $gnDivisionOption = [];

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

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function mount($peopleId)
    {
        $this->employee = People::where('people_id', $peopleId)->firstOrFail();
        //$this->canEdit = $canEdit;

        // Load stored values
        $this->district = $this->employee->district_id;
        $this->divisionalDecretaryOffice = $this->employee->gnDivision->dso_id;
        $this->gnDivision = $this->employee->gn_division_id;

        $this->addressLine1 = $this->employee->address_line1;
        $this->addressLine2 = $this->employee->address_line2;
        $this->addressLine3 = $this->employee->address_line3;
        $this->postalCode = $this->employee->postal_code;
        $this->latitude = $this->employee->latitude;
        $this->longitude = $this->employee->longitude;

        // Initial dropdown data
        $this->districtOption = DistrictsList::orderBy('district_name', 'asc')->get();
        $this->divisionalSecretaryofficeOption = DivisionalSecretariatOffice::where('district_id', $this->district)->orderBy('dso_name', 'asc')->get();
        $this->gnDivisionOption = GnDivision::where('dso_id', $this->divisionalDecretaryOffice)->orderBy('gn_division_name', 'asc')->get();
    }

    public function updatedDistrict($value)
    {
        $this->divisionalSecretaryofficeOption =
            DivisionalSecretariatOffice::where('district_id', $value)->orderBy('dso_name', 'asc')->get();

        // Reset downstream fields
        $this->divisionalDecretaryOffice = '';
        $this->gnDivision = '';
        $this->gnDivisionOption = collect();
    }

    public function updatedDivisionalDecretaryOffice($value)
    {
        $this->gnDivisionOption =
            GnDivision::where('dso_id', $value)->orderBy('gn_division_name')->get();

        $this->gnDivision = '';
    }

    public function editLocationInfo()
    {
        $validated = $this->validate();

        DB::beginTransaction();

        try {
            $this->employee->update([
                'district_id'      => $this->district,
                'dso_id'           => $this->divisionalDecretaryOffice,
                'gn_division_id'   => $this->gnDivision,
                'address_line1'    => $this->addressLine1,
                'address_line2'    => $this->addressLine2,
                'address_line3'    => $this->addressLine3,
                'postal_code'      => $this->postalCode,
                'latitude'         => $this->latitude,
                'longitude'        => $this->longitude,
            ]);

            DB::commit();

            session()->flash('success', 'Location information updated successfully.');
            $this->showModalLocationInfo = false;

            return $this->redirect(url()->previous(), navigate: true);

        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employees.location-information');
    }
}
