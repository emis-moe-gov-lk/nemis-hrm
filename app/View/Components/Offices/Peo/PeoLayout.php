<?php

namespace App\View\Components\Offices\Peo;

use App\Models\ProvincialEducationOffice;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PeoLayout extends Component
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
        $peo = ProvincialEducationOffice::find($this->officeId);
        return view('components.offices.peo.peo-layout', ['peo' => $peo]);
    }
}
