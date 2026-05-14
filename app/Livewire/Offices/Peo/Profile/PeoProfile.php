<?php

namespace App\Livewire\Offices\Peo\Profile;

use Livewire\Component;
use App\Models\ProvincialEducationOffice;

class PeoProfile extends Component
{
    public $id;
    public function render()
    {
        $provincialEducationOffice = ProvincialEducationOffice::find($this->id);
        //dd($institution);
        return view('livewire.offices.peo.profile.peo-profile', compact('provincialEducationOffice'));
    }
}
