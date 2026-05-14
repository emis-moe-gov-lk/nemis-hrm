<?php

namespace App\Livewire\Offices\Zeo;

use Livewire\Component;
use App\Models\ZonalEducationOffice;

class ZeoOfficesProfile extends Component
{
    public $id;
    public function render()
    {
        $zonalEducationOffice = ZonalEducationOffice::find($this->id);
        //dd($institution);
        return view('livewire.offices.zeo.zeo-offices-profile', compact('zonalEducationOffice'));
    }
}
