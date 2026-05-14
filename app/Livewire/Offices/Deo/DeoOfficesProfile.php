<?php

namespace App\Livewire\Offices\Deo;

use Livewire\Component;
use App\Models\DivisionalEducationOffice;

class DeoOfficesProfile extends Component
{
    public $id;
    public function render()
    {
        $divisionalEducationOffice = DivisionalEducationOffice::find($this->id);
        //dd($institution);
        return view('livewire.offices.deo.deo-offices-profile', compact('divisionalEducationOffice'));
    }
}
