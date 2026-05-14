<?php

namespace App\View\Components\Principals;

use Closure;
use App\Models\People;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class PrincipalProfileLayout extends Component
{
    public $principalid;
    /**
     * Create a new component instance.
     */
    public function __construct($principalid)
    {
        $this->principalid = $principalid;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $principal = People::find($this->principalid);
        
        return view('components.principals.principal-profile-layout', ['principal' => $principal]);
    }
}
