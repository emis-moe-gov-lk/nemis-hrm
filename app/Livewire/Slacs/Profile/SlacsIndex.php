<?php

namespace App\Livewire\Slacs\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SlacsIndex extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $slacs;

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

        $this->slacs = $people;
    }

    public function render()
    {
        return view('livewire.slacs.profile.slacs-index');
    }
}
