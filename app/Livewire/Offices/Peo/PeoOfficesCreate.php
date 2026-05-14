<?php

namespace App\Livewire\Offices\Peo;

use Livewire\Component;
use App\Models\Workplaces;
use Livewire\WithFileUploads;

use Livewire\Attributes\Validate;
use App\Models\ProvincialMinistryOfEducationOffice;

class PeoOfficesCreate extends Component
{
    #[Validate('required|string|max:10|min:10')]
    public $workplaceId;

    #[Validate('required|string')]
    public $provincialMinistry;

    #[Validate('required|string|max:255')]
    public $peoName;

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

    public $workplaceIdExists = false;

    public $provinceOption = [];

    public function mount(){
        $this->provinceOption = ProvincialMinistryOfEducationOffice::orderBy('short_name', 'asc')->get();
    }

    public function updatedWorkplaceId($value)
    {
        $this->workplaceIdExists = Workplaces::where('workplace_id', $value)->exists();
    }

    public function save(){
        // Validation
        $this->validate();

        // Reset form
        $this->reset();

        // Success message
        session()->flash('success', 'Provincial Education Office created successfully!');
    }

    public function render()
    {
        return view('livewire.offices.peo.peo-offices-create');
    }
}
