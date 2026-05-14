<?php

namespace App\Livewire\Sltas\Profile;

use Livewire\Component;
use App\Models\People;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SltasQualification extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $sltas;
    public $people;

    public function mount($id)
    {
        $people = People::find($id);
        $this->authorize('viewRestrict', $people);

        $this->sltas = $people;
    }

    public function render()
    {
        return view('livewire.sltas.profile.sltas-qualification');
    }
}
