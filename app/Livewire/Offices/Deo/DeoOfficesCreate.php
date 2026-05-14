<?php

namespace App\Livewire\Offices\Deo;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

use App\Models\Authority;
use App\Models\Workplaces;
use App\Models\DistrictsList;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;
use App\Models\ProvincialEducationOffice;

class DeoOfficesCreate extends Component
{
    #[Validate('required|string|max:10|min:10')]
    public $workplaceId;

    #[Validate('required|string')]
    public $provincialOffice;

    #[Validate('required|string')]
    public $zonalOffice;

    #[Validate('required|string')]
    public $district;

    #[Validate('required|string|max:255')]
    public $deoName;

    #[Validate('nullable|string|max:255')]
    public $otherName;

    #[Validate('required|string|max:255')]
    public $addressLine1;

    #[Validate('nullable|string|max:255')]
    public $addressLine2;

    #[Validate('required|string|max:255')]
    public $city;

    #[Validate('required|numeric|max:99999')]
    public $postalCode;

    #[Validate('required|email|max:255')]
    public $email;

    #[Validate('required|regex:/^[0-9]{3}-[0-9]{7}$/')]
    public $contactNumber;

    #[Validate('required|numeric|max:9.835|min:5.916')]
    public $latitude;

    #[Validate('required|numeric|max:81.881|min:79.652')]
    public $longitude;

    public $provinceOption = [];
    public $districtOption = [];
    public $zonalOfficeOption = [];

    public $workplaceIdExists = false;

    public function mount(){
        $this->provinceOption = ProvincialEducationOffice::orderBy('short_name', 'asc')->get();
        $this->districtOption = DistrictsList::orderBy('district_id', 'asc')->get();
    }


    public function updatedWorkplaceId($value)
    {
        $this->workplaceIdExists = Workplaces::where('workplace_id', $value)->exists();
    }

    public function updatedProvincialOffice($value){
        $this->zonalOfficeOption = ZonalEducationOffice::where("peo_wp_id", $value)->orderBy('short_name', 'asc')->get();
    }

    public function save(){
        // Validation
        $this->validate();

        // Reset form
        $this->reset();

        // Success message
        session()->flash('success', 'Divisional Education Office created successfully!');
    }

    public function render()
    {
        return view('livewire.offices.deo.deo-offices-create');
    }
}
