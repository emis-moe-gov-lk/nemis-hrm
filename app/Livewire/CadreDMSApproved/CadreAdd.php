<?php

namespace App\Livewire\CadreDMSApproved;

use Livewire\Component;
use App\Models\Institution;
use App\Models\SubjectList;
use App\Models\CadreCirculars;
use App\Models\CadreDMSApproved;
use App\Models\MediumOfInstruction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CadreAdd extends Component
{
    public $school_id;
    public $circular_id;

    public $institution;
    public $circular;

    public $subjectList = [];
    public $mediums = [];

    // [subject_id => [medium_id => approved_posts]]
    public array $cadre = [];

    /**
     * Use mount to receive route/parent parameters and load initial data.
     */
    public function mount($school_id, $circular_id): void
    {
        $this->school_id   = $school_id;
        $this->circular_id = $circular_id;

        $this->institution = Institution::findOrFail($this->school_id);
        $this->circular    = CadreCirculars::findOrFail($this->circular_id);

        // Subjects offered for the institution's grade span
        $this->subjectList = SubjectList::offeredForGradeRange(
            $this->institution->gradeSpan->start_grade ?? 0,
            $this->institution->gradeSpan->end_grade ?? 0
        )->get();

        // Active mediums
        $this->mediums = MediumOfInstruction::active()->get();

        // Optional: prefill existing DMS values
        $this->prefillCadre();
    }

    /**
     * Load existing approved posts into $cadre so the form shows current values.
     */
    protected function prefillCadre(): void
    {
        $existing = CadreDMSApproved::where('circular_id', $this->circular->circular_id)
            ->where('workplace_id', $this->institution->workplace_id) // adjust if field name differs
            ->get();

        foreach ($existing as $row) {
            $this->cadre[$row->subject_id][$row->medium_id] = $row->approved_posts;
        }
    }

    protected $rules = [
        'cadre.*.*' => 'nullable|integer|min:0',
    ];

    public function save(): void
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Use the properties actually defined in this component
            $circularId  = $this->circular->circular_id;
            $workplaceId = $this->institution->workplace_id; // make sure Institution has workplace_id

            foreach ($this->subjectList as $subject) {
                $subjectId = $subject->subject_id;

                if (!isset($this->cadre[$subjectId])) {
                    continue;
                }

                foreach ($this->mediums as $medium) {
                    $mediumId = $medium->medium_id;
                    $value    = $this->cadre[$subjectId][$mediumId] ?? null;

                    if ($value === null || $value === '') {
                        continue;
                    }

                    if ((int) $value === 0) {
                        // Delete existing record if present
                        CadreDMSApproved::where([
                            'circular_id'  => $circularId,
                            'workplace_id' => $workplaceId,
                            'subject_id'   => $subjectId,
                            'medium_id'    => $mediumId,
                        ])->delete();
                        continue;
                    }

                    CadreDMSApproved::updateOrCreate(
                        [
                            'circular_id'  => $circularId,
                            'workplace_id' => $workplaceId,
                            'subject_id'   => $subjectId,
                            'medium_id'    => $mediumId,
                        ],
                        [
                            'approved_posts' => (int) $value,
                            'approval_type'  => 'manual',
                            'approved_date'  => now(),
                            'active_status'  => 1,
                            'created_by'     => auth()->user()->people_id ?? null,
                            'updated_by'     => auth()->user()->people_id ?? null,
                        ]
                    );
                }
            }

            DB::commit();

            session()->flash('success', 'Approved cadre updated successfully.');

            // Refresh current data if needed
            $this->prefillCadre();

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('CadreDMSApproved save failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            if (app()->environment('local')) {
                session()->flash('error', 'Error: ' . $e->getMessage());
            } else {
                session()->flash('error', 'Failed to update approved cadre. Please try again.');
            }
        }
    }

    public function render()
    {
        // $institution, $circular, $subjectList, $mediums, $cadre are public, so
        // Livewire automatically makes them available in the Blade view.
        return view('livewire.cadre-d-m-s-approved.cadre-add');
    }
}
