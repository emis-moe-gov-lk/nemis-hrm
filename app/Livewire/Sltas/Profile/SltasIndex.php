<?php

namespace App\Livewire\Sltas\Profile;

use Livewire\Component;
use App\Models\People;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SltasIndex extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $sltas;
    public $people;

    // -------------------------
    // Validation Rules
    // -------------------------
    protected function rules()
    {
        return [];
    }

    // -------------------------
    // Live Validation on Field Update
    // -------------------------
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id)
    {
        $this->id = $id;

        $people = People::find($id);
        $this->authorize('viewRestrict', $people);

        $this->sltas = $people;
    }

    public function render()
    {
        return view('livewire.sltas.profile.sltas-index');
    }
}
