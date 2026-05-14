<?php

namespace App\Livewire\Sleas\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SleasFamily extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $sleas;
    public $people;

    public function mount($id)
    {
        $this->id = $id;
        $people = People::find($id);
        $this->authorize('viewRestrict', $people);

        $this->sleas = $people;
    }
    
    public function render()
    {
        return view('livewire.sleas.profile.sleas-family');
    }
}
