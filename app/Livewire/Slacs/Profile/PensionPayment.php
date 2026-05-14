<?php

namespace App\Livewire\Slacs\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PensionPayment extends Component
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
        return view('livewire.slacs.profile.pension-payment');
    }
}
