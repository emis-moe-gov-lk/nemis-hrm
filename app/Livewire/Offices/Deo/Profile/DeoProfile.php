<?php

namespace App\Livewire\Offices\Deo\Profile;

use Livewire\Component;
use App\Models\DivisionalEducationOffice;

class DeoProfile extends Component
{
    public $id;
    public function render()
    {
        $divisionalEducationOffice = DivisionalEducationOffice::find($this->id);
        //dd($institution);
        return view('livewire.offices.deo.profile.deo-profile', compact('divisionalEducationOffice'));
    }
}
