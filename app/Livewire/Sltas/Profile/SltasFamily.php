<?php

namespace App\Livewire\Sltas\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SltasFamily extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $sltas;
    public $people;

    public function mount($id)
    {
        $this->id = $id;

        $people = People::find($id);
        $this->authorize('viewRestrict', $people);

        $this->sltas = $people;
    }
    
    public function render()
    {
        return view('livewire.sltas.profile.sltas-family');
    }
}
