<?php

namespace App\Livewire\Offices\Pmoe\Profile;

use Livewire\Component;
use App\Models\ProvincialMinistryOfEducationOffice;

class PmoeProfile extends Component
{
    public $id;
    public function render()
    {
        $provincialEducationMinistry = ProvincialMinistryOfEducationOffice::find($this->id);
        //dd($institution);
        return view('livewire.offices.pmoe.profile.pmoe-profile', compact('provincialEducationMinistry'));
    }
}
