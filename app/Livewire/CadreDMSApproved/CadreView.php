<?php

namespace App\Livewire\CadreDMSApproved;

use App\Models\Institution;
use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\MediumOfInstruction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use misterspelik\LaravelPdf\Facades\Pdf;

class CadreView extends Component
{
    public $id;

    public $activeCircular;
    public $workplace;
    public $mediums;
    public $approvedCadreList; // no type, will hold a Collection

    protected function loadCadreData(): void
    {
        $this->activeCircular = CadreCirculars::active()->first();
        $this->workplace      = Institution::where('id', $this->id)->firstOrFail();
        $this->mediums        = MediumOfInstruction::active()->get();

        $this->approvedCadreList = $this->activeCircular
            ? CadreDMSApproved::approvedCadreSummary(
                $this->activeCircular->circular_id,
                $this->workplace->workplace_id
            )
            : collect();
    }

    public function verify($subjectId, $workplaceId)
    {
        dd($subjectId, $workplaceId);
        $this->approvedCadreList->where('subject_id', $subjectId)->where('workplace_id', $workplaceId)->first()->update(['is_verified' => true]);
    }

    public function confirm($subjectId, $workplaceId)
    {
        $this->approvedCadreList->where('subject_id', $subjectId)->where('workplace_id', $workplaceId)->first()->update(['is_confirmed' => true]);
    }

    public function downloadPdf()
    {
        $this->loadCadreData();

        $mediumSums = [];
        foreach ($this->mediums as $medium) {
            $mediumSums[$medium->medium_id] = 0;
        }

        foreach ($this->approvedCadreList as $row) {
            foreach ($this->mediums as $medium) {
                $mediumSums[$medium->medium_id] += $row['medium_totals'][$medium->medium_id] ?? 0;
            }
        }

        $typeLabels = [
            1 => ['label' => 'Teacher / Subjects'],
            2 => ['label' => 'Principal / Designation'],
            3 => ['label' => 'Other'],
        ];

        $pdf = Pdf::loadView(
            'pdf.cadre-dms-approved-institution-pdf',
            [
                'workplace'   => $this->workplace,
                'circular'    => $this->activeCircular,
                'mediums'     => $this->mediums,
                'groupedRows' => $this->approvedCadreList->groupBy(fn ($row) => $row['subject']->type ?? 0),
                'typeLabels'  => $typeLabels,
                'mediumSums'  => $mediumSums,
                'grandTotal'  => $this->approvedCadreList->sum('total'),
                'userNic'     => Auth::user()?->nic_hash ?? 'N/A',
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

        $filename = 'dms-approved-cadre-' . Str::slug($this->workplace->census_no ?: $this->workplace->workplace_id) . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function render()
    {
        $this->loadCadreData();

        //dd($this->approvedCadreList);
        return view('livewire.cadre-d-m-s-approved.cadre-view');
    }
}
