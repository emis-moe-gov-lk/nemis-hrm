<?php

namespace App\Livewire\Sltes\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EditRequest extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $people;

    public function mount($id)
    {
        $people = People::find($id);
        $this->authorize('viewRestrict', $people);

        $this->people = $people;
    }
    
    public function render()
    {
        return view('livewire.sltes.profile.edit-request');
    }
}
