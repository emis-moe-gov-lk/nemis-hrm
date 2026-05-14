<?php

namespace App\View\Components\Sleas;

use Closure;
use App\Models\People;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class SleasProfileLayout extends Component
{
    public $sleasid;
    /**
     * Create a new component instance.
     */
    public function __construct($sleasid)
    {
        $this->sleasid = $sleasid;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $sleas = People::find($this->sleasid);
        
        return view('components.sleas.sleas-profile-layout', ['sleas' => $sleas]);
    }
}
