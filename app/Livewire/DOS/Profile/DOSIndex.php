<?php

namespace App\Livewire\DOS\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DOSIndex extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $dos;
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

        $this->dos = $people;
    }

    
    public function render()
    {
        return view('livewire.d-o-s.profile.d-o-s-index');
    }
}
