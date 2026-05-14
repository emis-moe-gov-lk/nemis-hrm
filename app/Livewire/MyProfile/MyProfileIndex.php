<?php

namespace App\Livewire\MyProfile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MyProfileIndex extends Component
{
    public $peopleId;
    public $myprofile;

    public function mount(): void
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $this->peopleId = Auth::user()->people_id;

        $this->myprofile = People::where('people_id', $this->peopleId)->first();

        if (!$this->myprofile) {
            abort(404, 'Profile not found');
        }
    }

    public function render()
    {
        return view('livewire.my-profile.my-profile-index');
    }
}
