<?php

namespace App\View\Components\Offices\Zeo;

use App\Models\ZonalEducationOffice;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ZeoLayout extends Component
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
        $zeo = ZonalEducationOffice::find($this->officeId);
        return view('components.offices.zeo.zeo-layout', ['zeo' => $zeo]);
    }
}
