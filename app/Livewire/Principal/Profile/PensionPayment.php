<?php

namespace App\Livewire\Principal\Profile;

use App\Models\People;
use Livewire\Component;

class PensionPayment extends Component
{
    public $id;
    public $people;
    
    public function mount($id)
    {
        $this->people = People::find($id);
    }
    
    public function render()
    {
        return view('livewire.principal.profile.pension-payment');
    }
}
