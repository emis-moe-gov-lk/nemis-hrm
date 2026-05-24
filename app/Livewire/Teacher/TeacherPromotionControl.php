<?php

namespace App\Livewire\Teacher;

use App\Models\Employee;
use Livewire\Component;

class TeacherPromotionControl extends Component
{
    public ?string $employeeID;

    public function render()
    {
        return view('livewire.teacher.teacher-promotion-control');
    }
}
