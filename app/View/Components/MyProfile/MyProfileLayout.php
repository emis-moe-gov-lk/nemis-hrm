<?php

namespace App\View\Components\MyProfile;

use Closure;
use App\Models\People;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class MyProfileLayout extends Component
{
    public $peopleId;
    public $myprofile;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->myprofile = People::where('people_id', Auth::user()->people_id)->firstOrFail();
        $this->peopleId = $this->myprofile->people_id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.my-profile.my-profile-layout', ['myprofile' => $this->myprofile]);
    }
}
