<?php

namespace App\Livewire\Slas\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SlasIndex extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $slas;
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

        $this->slas = $people;
    }
    
    public function render()
    {
        return view('livewire.slas.profile.slas-index');
    }
}
