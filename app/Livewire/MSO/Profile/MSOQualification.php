<?php

namespace App\Livewire\MSO\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MSOQualification extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $mso;
    public $people;

    public function mount($id)
    {
        $people = People::find($id);
        $this->authorize('viewRestrict', $people);

        $this->mso = $people;
    }

    public function render()
    {
        return view('livewire.m-s-o.profile.m-s-o-qualification');
    }
}
