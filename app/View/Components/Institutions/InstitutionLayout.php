<?php

namespace App\View\Components\Institutions;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Institution;

class InstitutionLayout extends Component
{
    public $institutionId;
    /**
     * Create a new component instance.
     */
    public function __construct($institutionId)
    {
        $this->institutionId = $institutionId;
    }

    /**
     * Get the view / contents that represent the component.
     */

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $institution = Institution::find($this->institutionId);
        return view('components.institutions.institution-layout', ['institution' => $institution]);
    }
}
