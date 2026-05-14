<?php

namespace App\Livewire\Offices\Deo\Profile;

use Livewire\Component;
use App\Models\Authority;
use App\Models\Institution;
use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\DivisionalEducationOffice;

class DeoDmsCadreSummary extends Component
{
    public $officeId;
    public $staffList = [];
    public $rows = [];
    public $circular;
    public $workplaceId;

    public $authorityOption = [];
    public $institutionOption = [];

    public $authority;
    public $institution;

    public function mount($id)
    {
        $this->officeId = $id;

        // Get the workplace_id for the selected PMOE
        $office = DivisionalEducationOffice::find($this->officeId);
        $this->workplaceId = $office?->workplace_id;

        $this->circular = CadreCirculars::active()->first();

        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId);

        $this->authorityOption = Authority::active()->get();

    }

    public function updatedAuthority($value)
    {
        if ($value == 'null') {
            $this->authority = null;
        }
        
        $this->institutionOption = Institution::where('authority_id', $value)->where('deo_wp_id', $this->workplaceId)->get();

        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority);
    }

    public function updatedInstitution($value)
    {
        if ($value == 'null') {
            $this->institution = null;
        }
        
        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority, $this->institution);
    }

    public function render()
    {
        return view('livewire.offices.deo.profile.dms-cadre-summary');
    }
}
