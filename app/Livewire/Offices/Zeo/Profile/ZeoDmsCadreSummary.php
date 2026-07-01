<?php

namespace App\Livewire\Offices\Zeo\Profile;

use Livewire\Component;
use App\Models\Authority;
use App\Models\Institution;
use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\ZonalEducationOffice;
use App\Models\DivisionalEducationOffice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use misterspelik\LaravelPdf\Facades\Pdf;

class ZeoDmsCadreSummary extends Component
{
    public $officeId;
    public $office;
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
        $this->office = ZonalEducationOffice::find($this->officeId);
        $this->workplaceId = $this->office?->workplace_id;

        $this->circular = CadreCirculars::active()->first();

        $this->rows = $this->cadreRows();

        $this->authorityOption = Authority::active()->get();
        $this->divisionOption = DivisionalEducationOffice::where('zeo_wp_id',  $this->workplaceId)->get();


    }

    protected function cadreRows()
    {
        return $this->circular
            ? CadreDMSApproved::institutionCadreVsEmployers(
                $this->circular->circular_id,
                $this->workplaceId,
                $this->authority,
                $this->institution,
                $this->division
            )
            : collect();
    }

    public function updatedAuthority($value)
    {
        if ($value == 'null') {
            $this->authority = null;
        }
        $this->reset(['institution', 'division']);

        $this->divisionOption = DivisionalEducationOffice::where('zeo_wp_id',  $this->workplaceId)->get();

        $this->rows = $this->cadreRows();
    }

    public function updatedDivision($value)
    {
        if ($value == 'null') {
            $this->division = null;
        }
        
        $this->reset(['institution']);

        $this->institutionOption = Institution::where('deo_wp_id', $this->division)->get();

        $this->rows = $this->cadreRows();
    }



    public function updatedInstitution($value)
    {
        if ($value == 'null') {
            $this->institution = null;
        }

        $this->rows = $this->cadreRows();
    }

    public function downloadPdf()
    {
        $this->circular = CadreCirculars::active()->first();
        $rows = collect($this->cadreRows());

        $typeLabels = [
            1 => ['label' => 'Teacher / Subjects'],
            2 => ['label' => 'Principal / Designation'],
            3 => ['label' => 'Other'],
        ];

        $filterSummary = [
            'Authority' => $this->authority
                ? (Authority::where('authority_id', $this->authority)->value('authority_name') ?: $this->authority)
                : 'All authorities',
            'Division' => $this->division
                ? (DivisionalEducationOffice::where('workplace_id', $this->division)->value('short_name') ?: $this->division)
                : 'All divisions',
            'Institution' => $this->institution
                ? (Institution::where('workplace_id', $this->institution)->value('name') ?: $this->institution)
                : 'All institutions',
        ];

        $pdf = Pdf::loadView(
            'pdf.zeo-dms-cadre-summary',
            [
                'office'        => $this->office,
                'circular'      => $this->circular,
                'groupedRows'   => $rows->groupBy('subject_type'),
                'typeLabels'    => $typeLabels,
                'grandApproved' => $rows->sum('approved_posts'),
                'grandFilled'   => $rows->sum('filled_posts'),
                'grandDiff'     => $rows->sum('diff'),
                'filterSummary' => $filterSummary,
                'userNic'       => Auth::user()?->nic_hash ?? 'N/A',
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

        $filename = 'zeo-cadre-summary-' . Str::slug($this->office?->short_name ?: $this->officeId) . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function render()
    {
        return view('livewire.offices.zeo.profile.dms-cadre-summary');
    }
}
