<?php

namespace App\Livewire\Subjects;

use App\Models\SubjectList;
use Livewire\Component;

class TeachingSubjectIndex extends Component
{
    public function render()
    {
        $teachingSubjects = SubjectList::active()->paginate(50);
        return view('livewire.subjects.teaching-subject-index', compact('teachingSubjects'));
    }
}
