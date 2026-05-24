<?php

namespace App\Livewire\Institutions\Profile;

use Livewire\Component;
use App\Models\Institution;
use App\Models\EmployerCurrentAppointment;
use Illuminate\Support\Facades\DB;

class InstitutionsOverview extends Component
{
    public $id;
    public $studentCount;
    public $staffCount;
    public $parentCount;

    public function mount($id)
    {
        $this->id = $id;

        // Fetch institution (existing logic)
        $institution = Institution::find($this->id);

        // Fetch real student count from admissions
        $this->studentCount = \App\Models\InstitutionStudentAdmission::whereHas('class.grade', function($query) {
            $query->where('institution_id', $this->id)
                  ->where('academic_year', date('Y'));
        })
        ->where('academic_year', date('Y'))
        ->sum(DB::raw('male_count + female_count'));

        // Count staff linked to this institution
        $this->staffCount = EmployerCurrentAppointment::where('workplace_id', $institution->workplace_id)->count();

        // Placeholder parent count (less than students)
        $this->parentCount = (int)($this->studentCount * 0.85); // Estimated for now
    }

    public function render()
    {
        $institution = Institution::find($this->id);

        return view('livewire.institutions.profile.institutions-overview', [
            'institution'   => $institution,
            'studentCount'  => $this->studentCount,
            'staffCount'    => $this->staffCount,
            'parentCount'   => $this->parentCount,
        ]);
    }
}
