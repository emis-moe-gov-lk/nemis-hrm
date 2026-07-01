<?php

namespace App\Livewire\Institutions\Profile;

use Livewire\Component;
use App\Models\Institution;
use Livewire\WithPagination;
use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use misterspelik\LaravelPdf\Facades\Pdf;

class CadreDMSApprove extends Component
{
    use WithPagination;

    public $id;
    public $institution;

    public function mount($id)
    {
        $this->id = $id;
        $this->institution = Institution::findOrFail($id);
    }

    protected function getCadreRows($circular)
    {
        return $circular
            ? CadreDMSApproved::institutionCadreVsEmployersWithList(
                $circular->circular_id,
                $this->institution->workplace_id
            )
            : collect();
    }

    public function downloadPdf()
    {
        $circular = CadreCirculars::active()->first();
        $rows = $this->getCadreRows($circular);

        $typeLabels = [
            1 => ['label' => 'Teacher / Subjects'],
            2 => ['label' => 'Principal / Designation'],
            3 => ['label' => 'Other'],
        ];

        $pdf = Pdf::loadView(
            'pdf.institution-cadre-dms-approved',
            [
                'institution'    => $this->institution,
                'circular'       => $circular,
                'groupedRows'    => $rows->groupBy('subject_type'),
                'typeLabels'     => $typeLabels,
                'grandApproved'  => $rows->sum('approved_posts'),
                'grandFilled'    => $rows->sum('filled_posts'),
                'grandDiff'      => $rows->sum('diff'),
                'userNic'        => Auth::user()?->nic_hash ?? 'N/A',
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

        $filename = 'institution-cadre-summary-' . Str::slug($this->institution->census_no ?: $this->institution->workplace_id) . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function render()
    {
        $workplaceId = $this->institution->workplace_id;
        $circular = CadreCirculars::active()->first();

        $rows = $this->getCadreRows($circular);

        return view('livewire.institutions.profile.cadre-d-m-s-approved', compact('rows', 'workplaceId', 'circular'));
    }
}
