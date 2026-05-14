<?php

namespace App\Livewire\Offices\Moe;

use Livewire\Component;
use App\Models\MinistryOfEducationOffice;

class MoeOfficesProfile extends Component
{
    public $id;
    public function render()
    {
        $EducationMinistry = MinistryOfEducationOffice::find($this->id);
        //dd($institution);
        return view('livewire.offices.moe.moe-offices-profile', compact('EducationMinistry'));
    }
}
