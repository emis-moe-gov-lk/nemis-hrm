<?php

namespace App\Livewire\Sleas\Profile;

use Livewire\Component;
use App\Models\People;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SleasQualification extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $sleas;
    public $people;

    public function mount($id)
    {
        $people = People::find($id);
        $this->authorize('viewRestrict', $people);

        $this->sleas = $people;
    }

    public function render()
    {
        
        return view('livewire.sleas.profile.sleas-qualification');
    }
}
