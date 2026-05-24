<?php

namespace App\Livewire\Offices\Zeo\Profile;

use Livewire\Component;
use App\Models\Authority;
use App\Models\Institution;
use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;

class ZeoDmsCadreSummary extends Component
{
    public $officeId;
    public $staffList = [];
    public $rows = [];
    public $circular;
    public $workplaceId;

    public $authorityOption = [];
    public $institutionOption = [];
    public $divisionOption = [];

    public $authority;
    public $institution;
    public $division;

    public function mount($id)
    {
        $this->officeId = $id;

        // Get the workplace_id for the selected PMOE
        $office = ZonalEducationOffice::find($this->officeId);
        $this->workplaceId = $office?->workplace_id;

        $this->circular = CadreCirculars::active()->first();

        $this->rows = $this->circular
            ? CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId)
            : [];

        $this->authorityOption = Authority::active()->get();
        $this->divisionOption = DivisionalEducationOffice::where('zeo_wp_id',  $this->workplaceId)->get();


    }

    public function updatedAuthority($value)
    {
        if ($value == 'null') {
            $this->authority = null;
        }
        $this->reset(['institution', 'division']);

        $this->divisionOption = DivisionalEducationOffice::where('zeo_wp_id',  $this->workplaceId)->get();

        $this->rows = $this->circular
            ? CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority)
            : [];
    }

    public function updatedDivision($value)
    {
        if ($value == 'null') {
            $this->division = null;
        }
        
        $this->reset(['institution']);

        $this->institutionOption = Institution::where('deo_wp_id', $this->division)->get();

        $this->rows = $this->circular
            ? CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority, null, $this->division)
            : [];
    }



    public function updatedInstitution($value)
    {
        if ($value == 'null') {
            $this->institution = null;
        }

        $this->rows = $this->circular
            ? CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority, $this->institution)
            : [];
    }

    public function render()
    {
        return view('livewire.offices.zeo.profile.dms-cadre-summary');
    }
}
