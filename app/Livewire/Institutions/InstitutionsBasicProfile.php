<?php

namespace App\Livewire\Institutions;

use App\Models\Institution;
use Livewire\Component;

class InstitutionsBasicProfile extends Component
{
    public $id;
    public function render()
    {
        $institution = Institution::find($this->id);
        //dd($institution);
        return view('livewire.institutions.institutions-basic-profile', compact('institution'));
    }
}
