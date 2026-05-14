<?php

namespace App\Livewire\Offices\Peo;

use Livewire\Component;
use App\Models\ProvincialEducationOffice;

class PeoOfficesProfile extends Component
{
    public $id;
    public function render()
    {
        $provincialEducationOffice = ProvincialEducationOffice::find($this->id);
        //dd($institution);
        return view('livewire.offices.peo.peo-offices-profile', compact('provincialEducationOffice'));
    }
}
