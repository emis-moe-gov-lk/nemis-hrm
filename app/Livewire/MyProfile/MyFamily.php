<?php

namespace App\Livewire\MyProfile;

use App\Models\People;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyFamily extends Component
{
    public $peopleId;
    public $people;

    public function mount(): void
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $this->peopleId = Auth::user()->people_id;

        $this->people = People::where('people_id', $this->peopleId)->first();

        if (!$this->people) {
            abort(404, 'Profile not found');
        }
    }

    public function render()
    {
        return view('livewire.my-profile.my-family');
    }
}
