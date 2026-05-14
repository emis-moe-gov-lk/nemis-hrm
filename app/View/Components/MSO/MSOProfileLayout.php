<?php

namespace App\View\Components\MSO;

use Closure;
use App\Models\People;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class MSOProfileLayout extends Component
{
    public $msoid;
    /**
     * Create a new component instance.
     */
    public function __construct($msoid)
    {
        $this->msoid = $msoid;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $mso = People::find($this->msoid);
        
        return view('components.m-s-o.m-s-o-profile-layout', ['mso' => $mso]);
    }
}
