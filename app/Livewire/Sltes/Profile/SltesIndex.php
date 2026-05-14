<?php

namespace App\Livewire\Sltes\Profile;

use Livewire\Component;
use App\Models\People;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SltesIndex extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $sltes;
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

        $this->sltes = $people;
    }

    public function render()
    {
        return view('livewire.sltes.profile.sltes-index');
    }
}
