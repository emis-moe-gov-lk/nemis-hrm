<?php

namespace App\View\Components\Sltas;

use Closure;
use App\Models\People;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class SltasProfileLayout extends Component
{
    public $sltasid;
    /**
     * Create a new component instance.
     */
    public function __construct($sltasid)
    {
        $this->sltasid = $sltasid;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $sltas = People::find($this->sltasid);
        
        return view('components.sltas.sltas-profile-layout', ['sltas' => $sltas]);
    }
}
