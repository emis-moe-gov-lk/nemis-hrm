<?php

namespace App\Livewire\Teacher\Profile;

use App\Models\People;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TeacherFamily extends Component
{
    use AuthorizesRequests; 
    
    public $id;
    public $teacher;
    public $people;

    public function mount($id)
    {
        $this->id = $id;
        $people = People::find($id);
        $this->authorize('viewRestrict', $people);

        $this->teacher = $people;
    }

    public function render()
    {
        return view('livewire.teacher.profile.teacher-family');
    }
}
