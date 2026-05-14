<?php

namespace App\View\Components\Sltes;

use Closure;
use App\Models\People;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class SltesProfileLayout extends Component
{
    public $sltesid;
    /**
     * Create a new component instance.
     */
    public function __construct($sltesid)
    {
        $this->sltesid = $sltesid;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $sltes = People::find($this->sltesid);
        
        return view('components.sltes.sltes-profile-layout', ['sltes' => $sltes]);
    }
}
