<?php

namespace App\Livewire\Subjects;

use Livewire\Component;
use App\Models\ApointedSubject;

class ApointedSubjectIndex extends Component
{
    public function render()
    {
        $apoinmentSubjects = ApointedSubject::active()->paginate(50);
        return view('livewire.subjects.apointed-subject-index', compact('apoinmentSubjects'));
    }
}
