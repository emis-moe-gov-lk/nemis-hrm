<?php

namespace App\View\Components\Slas;

use Closure;
use App\Models\People;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class SlasProfileLayout extends Component
{
    public $slasid;
    /**
     * Create a new component instance.
     */
    public function __construct($slasid)
    {
        $this->slasid = $slasid;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $slas = People::find($this->slasid);
        
        return view('components.slas.slas-profile-layout', ['slas' => $slas]);
    }
}
