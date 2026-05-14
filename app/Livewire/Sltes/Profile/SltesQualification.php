<?php

namespace App\Livewire\Sltes\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SltesQualification extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $sltes;
    public $people;

    public function mount($id)
    {
        $people = People::find($id);
        $this->authorize('viewRestrict', $people);

        $this->sltes = $people;
    }

    public function render()
    {
        return view('livewire.sltes.profile.sltes-qualification');
    }
}
