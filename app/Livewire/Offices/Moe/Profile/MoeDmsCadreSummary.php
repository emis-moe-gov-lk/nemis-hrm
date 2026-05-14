<?php

namespace App\Livewire\Offices\Moe\Profile;

use Livewire\Component;
use App\Models\Authority;
use App\Models\Institution;
use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;
use App\Models\MinistryOfEducationOffice;
use App\Models\ProvincialEducationOffice;
use App\Models\ProvincialMinistryOfEducationOffice;

class MoeDmsCadreSummary extends Component
{
    public $officeId;
    public $staffList = [];
    public $rows = [];
    public $circular;
    public $workplaceId;

    public $authorityOption = [];
    public $institutionOption = [];
    public $provinceOption = [];
    public $zonalOption = [];
    public $divisionOption = [];

    public $authority;
    public $institution;
    public $province;
    public $zonal;
    public $division;

    public function mount($id)
    {
        $this->officeId = $id;

        // Get the workplace_id for the selected PMOE
        $office = MinistryOfEducationOffice::find($this->officeId);
        $this->workplaceId = $office?->workplace_id;

        $this->circular = CadreCirculars::active()->first();

        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId);

        $this->authorityOption = Authority::active()->get();
        $this->provinceOption = ProvincialEducationOffice::active()->get();
    }

    public function updatedAuthority($value)
    {
        if ($value == 'null') {
            $this->authority = null;
        }
        $this->reset(['institution', 'division', 'zonal', 'province']);

        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority);
    }

    public function updatedProvince($value)
    {
        if ($value == 'null') {
            $this->province = null;
        }

        $this->reset(['institution', 'division', 'zonal']);

        $this->zonalOption = ZonalEducationOffice::where('peo_wp_id',  $this->province)->get();

        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority, null, null, null, $this->province);
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
        return view('livewire.offices.moe.profile.moe-dms-cadre-summary');
    }
}
