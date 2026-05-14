<?php

namespace App\Livewire\Offices\Moe\Profile;

use Livewire\Component;
use App\Models\MinistryOfEducationOffice;

class Moeprofile extends Component
{

    public $officeId;

    public function mount($id)
    {
        $this->officeId = $id;
    }

    public function render()
    {
        $EducationMinistry = MinistryOfEducationOffice::find($this->officeId);

        //dd($institution);

        return view('livewire.offices.moe.profile.moeprofile', compact('EducationMinistry'));
        // 'officeId' => $this->id
    }
}

