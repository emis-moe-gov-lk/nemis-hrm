<?php

namespace App\View\Components\Slacs;

use Closure;
use App\Models\People;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class SlacsProfileLayout extends Component
{
    public $slacsid;
    /**
     * Create a new component instance.
     */
    public function __construct($slacsid)
    {
        $this->slacsid = $slacsid;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $slacs = People::find($this->slacsid);
        
        return view('components.slacs.slacs-profile-layout', ['slacs' => $slacs]);
    }
}
