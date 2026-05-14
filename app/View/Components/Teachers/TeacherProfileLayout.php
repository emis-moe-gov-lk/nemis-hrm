<?php

namespace App\View\Components\Teachers;

use Closure;
use App\Models\People;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class TeacherProfileLayout extends Component
{
    public $teacherid;
    /**
     * Create a new component instance.
     */
    public function __construct($teacherid)
    {
        $this->teacherid = $teacherid;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $teacher = People::find($this->teacherid);
        //dd($teacher->myAppointments);   
        return view('components.teachers.teacher-profile-layout', ['teacher' => $teacher]);
    }
}
