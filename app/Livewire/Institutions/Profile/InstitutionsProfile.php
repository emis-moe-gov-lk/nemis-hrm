<?php

namespace App\Livewire\Institutions\Profile;

use Livewire\Component;
use App\Models\Institution;

class InstitutionsProfile extends Component
{
    public $id;
    //public $institution;

    public function render()
    {
        $institution = Institution::find($this->id);
        return view('livewire.institutions.profile.institutions-profile', compact('institution'));
    }
}
