<?php

namespace App\Livewire\Offices\Pmoe;

use Livewire\Component;
use App\Models\ProvincialMinistryOfEducationOffice;

class PmoeOfficesProfile extends Component
{
    public $id;
    public function render()
    {
        $provincialEducationMinistry = ProvincialMinistryOfEducationOffice::find($this->id);
        //dd($institution);
        return view('livewire.offices.pmoe.pmoe-offices-profile', compact('provincialEducationMinistry'));
    }
}
