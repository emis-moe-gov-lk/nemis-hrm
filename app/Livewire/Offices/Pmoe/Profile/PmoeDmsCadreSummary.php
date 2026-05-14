<?php

namespace App\Livewire\Offices\Pmoe\Profile;

use Livewire\Component;
use App\Models\Authority;
use App\Models\Institution;
use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;
use App\Models\ProvincialEducationOffice;

class PmoeDmsCadreSummary extends Component
{
    public $officeId;
    public $staffList = [];
    public $rows = [];
    public $circular;
    public $workplaceId;

    public $authorityOption = [];
    public $institutionOption = [];
    public $zonalOption = [];
    public $divisionOption = [];

    public $authority;
    public $institution;
    public $zonal;
    public $division;

    public function mount($id)
    {
        $this->officeId = $id;

        // Get the workplace_id for the selected PMOE
        $office = ProvincialEducationOffice::find($this->officeId);
        $this->workplaceId = $office?->workplace_id;

        $this->circular = CadreCirculars::active()->first();

        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId);

        $this->authorityOption = Authority::active()->get();
        $this->zonalOption = ZonalEducationOffice::where('peo_wp_id',  $this->workplaceId)->get();


    }

    public function updatedAuthority($value)
    {
        if ($value == 'null') {
            $this->authority = null;
        }
        $this->reset(['institution', 'division', 'zonal']);

        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority);
    }

    public function updatedZonal($value)
    {
        if ($value == 'null') {
            $this->zonal = null;
        }

        $this->reset(['institution', 'division']);

        $this->divisionOption = DivisionalEducationOffice::where('zeo_wp_id',  $this->zonal)->get();

        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority, null, null, $this->zonal);
    }

    public function updatedDivision($value)
    {
        if ($value == 'null') {
            $this->division = null;
        }
        
        $this->reset(['institution']);

        $this->institutionOption = Institution::where('deo_wp_id', $this->division)->get();

        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority, null, $this->division);
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
        return view('livewire.offices.pmoe.profile.pmoe-dms-cadre-summary');
    }
}
