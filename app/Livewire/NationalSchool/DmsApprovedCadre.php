<?php

namespace App\Livewire\NationalSchool;

use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\DivisionalEducationOffice;
use App\Models\Institution;
use App\Models\MinistryOfEducationOffice;
use App\Models\ProvincialEducationOffice;
use App\Models\Workplaces;
use App\Models\ZonalEducationOffice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use misterspelik\LaravelPdf\Facades\Pdf;

class DmsApprovedCadre extends Component
{
    public $officeId;
    public $office;
    public $staffList = [];
    public $rows = [];
    public $circular;
    public $workplaceId;

    public $authorityOption = [];
    public $institutionOption = [];
    public $provinceOption = [];
    public $zonalOption = [];
    public $divisionOption = [];

    public $authority = 'AUID01';
    public $institution;
    public $province;
    public $zonal;
    public $division;

    public function mount()
    {
        $loggedUser = Auth::user();
        $loggedAppointment = $loggedUser->currentAppointment;

        $loggedWorkplace = Workplaces::where(
            'workplace_id',
            $loggedAppointment->workplace_id
        )->first();

        $this->officeId = $loggedWorkplace->id;

        // Get the workplace_id for the selected PMOE
        $this->office = MinistryOfEducationOffice::find($this->officeId);
        $this->workplaceId = $this->office?->workplace_id;

        $this->circular = CadreCirculars::active()->first();

        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority);

        $this->provinceOption = ProvincialEducationOffice::active()->get();
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

        $this->institutionOption = Institution::where('deo_wp_id', $this->division)->where('authority_id', $this->authority)->get();

        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority, null, $this->division);
    }

    public function updatedInstitution($value)
    {
        if ($value == 'null') {
            $this->institution = null;
        }

        $this->rows = CadreDMSApproved::institutionCadreVsEmployers($this->circular->circular_id, $this->workplaceId, $this->authority, $this->institution);
    }

    public function downloadPdf()
    {
        $userNic = Auth::user()?->nic_hash ?? 'N/A';
        $officeName = $this->office?->name ?? 'National School';

        $typeLabels = [
            1 => ['label' => 'Teacher / Subjects'],
            2 => ['label' => 'Principal / Designation'],
            3 => ['label' => 'Other'],
        ];

        $groupedRows = collect($this->rows)->groupBy('subject_type');
        $grandApproved = collect($this->rows)->sum('approved_posts');
        $grandFilled = collect($this->rows)->sum('filled_posts');
        $grandDiff = collect($this->rows)->sum('diff');

        $pdf = Pdf::loadView(
            'pdf.dms-approved-cadre-pdf',
            [
                'officeName'    => $officeName,
                'circular'      => $this->circular,
                'groupedRows'   => $groupedRows,
                'typeLabels'    => $typeLabels,
                'grandApproved' => $grandApproved,
                'grandFilled'   => $grandFilled,
                'grandDiff'     => $grandDiff,
                'userNic'       => $userNic,
            ],
            [],
            [
                'format'        => 'A4-L',
                'margin_left'   => 10,
                'margin_right'  => 10,
                'margin_top'    => 20,
                'margin_bottom' => 20,
            ]
        );

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'national-school-cadre-summary.pdf');
    }

    public function render()
    {
        return view('livewire.national-school.dms-approved-cadre');
    }
}
