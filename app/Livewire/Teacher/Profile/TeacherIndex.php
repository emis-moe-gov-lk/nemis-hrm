<?php

namespace App\Livewire\Teacher\Profile;

use App\Models\People;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherIndex extends Component
{
    use AuthorizesRequests;
    
    public $id;
    public $people;
    public $teacher;

    // -------------------------
    // Validation Rules
    // -------------------------
    protected function rules()
    {
        return [];
    }

    // -------------------------
    // Live Validation on Field Update
    // -------------------------
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id)
    {
        $people = People::find($id);

        // 🔐 THIS BLOCKS URL EDITING
        $this->authorize('viewRestrict', $people);

        $this->teacher = $people;
    }

    public function render()
    {
        return view('livewire.teacher.profile.teacher-index');
    }
}
