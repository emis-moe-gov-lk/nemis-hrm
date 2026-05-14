<?php

namespace App\Livewire\CadreDMSApproved;

use Livewire\Component;
use App\Models\Institution;
use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\MediumOfInstruction;

class CadreView extends Component
{
    public $id;

    public $activeCircular;
    public $workplace;
    public $mediums;
    public $approvedCadreList; // no type, will hold a Collection

    public function verify($subjectId, $workplaceId)
    {
        dd($subjectId, $workplaceId);
        $this->approvedCadreList->where('subject_id', $subjectId)->where('workplace_id', $workplaceId)->first()->update(['is_verified' => true]);
    }

    public function confirm($subjectId, $workplaceId)
    {
        $this->approvedCadreList->where('subject_id', $subjectId)->where('workplace_id', $workplaceId)->first()->update(['is_confirmed' => true]);
    }

    public function render()
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

        //dd($this->approvedCadreList);
        return view('livewire.cadre-d-m-s-approved.cadre-view');
    }
}
