<?php

namespace App\Livewire\Principal\Profile;

use App\Models\People;
use Livewire\Component;

class PrincipalFamily extends Component
{
    public $id;
    public $principal;

    public function mount($id)
    {
        $this->id = $id;
        $this->principal = People::find($id);
    }

    public function render()
    {
        return view('livewire.principal.profile.principal-family');
    }
}
