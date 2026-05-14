<?php

namespace App\Livewire\Offices\Zeo\Profile;

use Livewire\Component;
use App\Models\ZonalEducationOffice;

class ZeoProfile extends Component
{
    public $id;
    public function render()
    {
        $zonalEducationOffice = ZonalEducationOffice::find($this->id);
        //dd($institution);
        return view('livewire.offices.zeo.profile.zeo-profile', compact('zonalEducationOffice'));
    }
}
