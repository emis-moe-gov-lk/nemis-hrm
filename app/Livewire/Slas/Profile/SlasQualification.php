<?php

namespace App\Livewire\Slas\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SlasQualification extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $slas;
    public $people;

    public function mount($id)
    {
        $people = People::find($id);
        $this->authorize('viewRestrict', $people);

        $this->slas = $people;
    }
    
    public function render()
    {
        return view('livewire.slas.profile.slas-qualification');
    }
}
