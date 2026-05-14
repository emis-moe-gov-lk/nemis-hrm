<?php

namespace App\Livewire\Institutions\Profile;

use Livewire\Component;
use App\Models\Institution;
use App\Models\DistrictsList;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;

class LocationAndAdministration extends Component
{
    public $institution;

    public $districtOption;
    public $zonalEducationOfficeOption;
    public $divisionalEducationOfficeOption;

    public $district;
    public $zonalEducationOffice;
    public $divisionalEducationOffice;
    public $address;
    public $postalCode;
    public $latitude;
    public $longitude;

    public function rules()
    {
        return [
            'district' => 'required',
            'zonalEducationOffice' => 'required',
            'divisionalEducationOffice' => 'required',
            'address' => 'required',
            'postalCode' => 'nullable',
        ];
    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    public function mount($institutionId)
    {
        $this->institution = Institution::find($institutionId);

        $this->district = $this->institution->district_id;
        $this->zonalEducationOffice = $this->institution->zeo_wp_id;
        $this->divisionalEducationOffice = $this->institution->deo_wp_id;
        $this->address = $this->institution->address;
        $this->postalCode = $this->institution->postal_code;
        $this->latitude = $this->institution->latitude;
        $this->longitude = $this->institution->longitude;

        $this->districtOption = DistrictsList::orderBy('district_name', 'asc')->get();
        $this->zonalEducationOfficeOption = ZonalEducationOffice::orderBy('short_name', 'asc')->get();
        $this->divisionalEducationOfficeOption = DivisionalEducationOffice::where('zeo_wp_id', $this->zonalEducationOffice)->orderBy('short_name', 'asc')->get();
    }

    public function updatedZonalEducationOffice($value)
    {
        $this->divisionalEducationOfficeOption = DivisionalEducationOffice::where('zeo_wp_id', $value)->orderBy('short_name', 'asc')->get();
    }

    public function render()
    {
        return view('livewire.institutions.profile.location-and-administration');
    }

    public function updateLocationAndAdministration()
    {
        $this->validate();

        $this->institution->update([
            'district_id' => $this->district,
            'zeo_wp_id' => $this->zonalEducationOffice,
            'deo_wp_id' => $this->divisionalEducationOffice,
            'address' => strtoupper($this->address),
            'postal_code' => $this->postalCode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        session()->flash('success', 'Location and Administration Updated Successfully.');
        return $this->redirect(url()->previous(), navigate: true);
    }

    public function resetForm()
    {
        $this->mount($this->institution->id);
    }
}
