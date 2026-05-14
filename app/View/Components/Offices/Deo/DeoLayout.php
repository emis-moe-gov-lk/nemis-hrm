<?php

namespace App\View\Components\Offices\Deo;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\DivisionalEducationOffice;

class DeoLayout extends Component
{
    public $officeId;
    /**
     * Create a new component instance.
     */
    public function __construct($officeId)
    {
        $this->officeId = $officeId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $deo = DivisionalEducationOffice::findOrFail($this->officeId);
        return view('components.offices.deo.deo-layout', compact('deo'));
    }
}
