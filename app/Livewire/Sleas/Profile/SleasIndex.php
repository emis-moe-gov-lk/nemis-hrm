<?php

namespace App\Livewire\Sleas\Profile;

use Livewire\Component;
use App\Models\People;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SleasIndex extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $sleas;
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
        $people = People::find($id);
        $this->authorize('viewRestrict', $people);

        $this->sleas = $people;
    }

    public function render()
    {
        return view('livewire.sleas.profile.sleas-index');
    }
}
