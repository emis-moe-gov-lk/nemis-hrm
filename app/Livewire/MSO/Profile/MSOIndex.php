<?php

namespace App\Livewire\MSO\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MSOIndex extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $mso;
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

        $this->mso = $people;
    }
    
    public function render()
    {
        return view('livewire.m-s-o.profile.m-s-o-index');
    }
}
