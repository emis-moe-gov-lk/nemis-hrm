<?php

namespace App\Livewire\Institutions\Profile;

use Livewire\Component;
use App\Models\Institution;
use App\Models\SubjectList;
use Livewire\WithPagination;
use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\EmployerCurrentAppointment;

class CadreDMSApprove extends Component
{
    use WithPagination;

    public $id;
    public $institution;

    public function mount($id)
    {
        $this->institution = Institution::findOrFail($id);
    }

    public function render()
    {
        $workplaceId = $this->institution->workplace_id;
        $circular = CadreCirculars::active()->first();

        $rows = $circular
            ? CadreDMSApproved::institutionCadreVsEmployersWithList($circular->circular_id, $workplaceId)
            : collect();

        return view('livewire.institutions.profile.cadre-d-m-s-approved', compact('rows', 'workplaceId', 'circular'));
    }
}
