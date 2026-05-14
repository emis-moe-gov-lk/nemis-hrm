<?php

namespace App\View\Components\DOS;

use Closure;
use App\Models\People;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class DOSProfileLayout extends Component
{
    public $dosid;
    /**
     * Create a new component instance.
     */
    public function __construct($dosid)
    {
        $this->dosid = $dosid;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $dos = People::find($this->dosid);
        
        return view('components.d-o-s.d-o-s-profile-layout', ['dos' => $dos]);
    }
}
