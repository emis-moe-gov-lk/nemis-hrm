<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\GradeSpan;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesInstitutionGradeSpans extends Component
{
    public $showModelNewInsGradeSpans = false;
    public $grade_span_id, $grade_span;
    public $start_grade, $end_grade;

    public $showModelEditInsGradeSpans = false;
    public $editGradeSpansId, $updateGradeSpanId, $updateGradeSpan, $updateStartGrade, $updateEndGrade;

    protected function rules()
    {
        // Create mode
        if (! $this->editGradeSpansId) {
            return [
                'grade_span_id' => [
                    'required',
                    'string',
                    'regex:/^GSID\d{2}$/',
                    Rule::unique('grade_spans', 'grade_span_id'),
                ],
                'grade_span' => [
                    'required',
                    'string',
                    'max:50',
                ],
                'start_grade' => [
                    'required',
                    'numeric',
                    'min:1',
                    'max:13',
                ],
                'end_grade' => [
                    'required',
                    'numeric',
                    'min:1',
                    'max:13',
                    'gte:start_grade',
                ],
            ];
        }

        // Edit mode (not actually used because update method has its own rules,
        // but kept here in case you call $this->validate() in edit mode later)
        return [
            'updateGradeSpanId' => [
                'required',
                'string',
                'regex:/^GSID\d{2}$/',
                Rule::unique('grade_spans', 'grade_span_id')
                    ->ignore($this->editGradeSpansId),
            ],
            'updateGradeSpan' => [
                'required',
                'string',
                'max:50',
            ],
            'updateStartGrade' => [
                'required',
                'numeric',
                'min:1',
                'max:13',
            ],
            'updateEndGrade' => [
                'required',
                'numeric',
                'min:1',
                'max:13',
                'gte:updateStartGrade',
            ],
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function addNewInsGradeSpans()
    {
        $validated = $this->validate(); // uses create rules

        try {
            GradeSpan::create([
                'grade_span_id'   => $this->grade_span_id,
                'grade_span_name' => $this->grade_span,
                'start_grade'     => $this->start_grade,
                'end_grade'       => $this->end_grade,
            ]);

            session()->flash('message', 'New Grade Span added successfully.');

            $this->showModelNewInsGradeSpans = false;

            // Only reset form fields
            $this->reset(['grade_span_id', 'grade_span', 'start_grade', 'end_grade']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Database error: Unable to save grade span data. ' . $e->getMessage());

        } catch (\Throwable $e) {
            Log::error('Grade span creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function deleteInsGradeSpans($id)
    {
        $insGradeSpans = GradeSpan::find($id);

        if ($insGradeSpans) {
            $insGradeSpans->delete();
            session()->flash('message', 'Grade Span deleted successfully.');
        } else {
            session()->flash('error', 'Grade Span not found.');
        }
    }

    public function toggleStatus($id)
    {
        $insGradeSpans = GradeSpan::find($id);

        if ($insGradeSpans) {
            $insGradeSpans->active_status = $insGradeSpans->active_status == '1' ? '0' : '1';
            $insGradeSpans->save();

            $this->dispatch('status-updated', [
                'message' => $insGradeSpans->active_status == '1'
                    ? 'Grade Span activated successfully.'
                    : 'Grade Span deactivated successfully.',
            ]);
        }
    }

    public function editInsGradeSpans($id)
    {
        $insGradeSpans = GradeSpan::findOrFail($id);

        $this->editGradeSpansId   = $insGradeSpans->id;
        $this->updateGradeSpanId  = $insGradeSpans->grade_span_id;
        $this->updateGradeSpan    = $insGradeSpans->grade_span_name;
        $this->updateStartGrade   = $insGradeSpans->start_grade;
        $this->updateEndGrade     = $insGradeSpans->end_grade;

        $this->showModelEditInsGradeSpans = true;
    }

    public function updateInsGradeSpansList()
    {
        $this->validate([
            'updateGradeSpanId' => [
                'required',
                'string',
                'regex:/^GSID\d{2}$/',
                Rule::unique('grade_spans', 'grade_span_id')
                    ->ignore($this->editGradeSpansId),
            ],
            'updateGradeSpan' => [
                'required',
                'string',
                'max:50',
            ],
            'updateStartGrade' => [
                'required',
                'numeric',
                'min:1',
                'max:13',
            ],
            'updateEndGrade' => [
                'required',
                'numeric',
                'min:1',
                'max:13',
                'gte:updateStartGrade',
            ],
        ]);

        try {
            GradeSpan::where('id', $this->editGradeSpansId)->update([
                'grade_span_id'   => $this->updateGradeSpanId,
                'grade_span_name' => $this->updateGradeSpan,
                'start_grade'     => $this->updateStartGrade,
                'end_grade'       => $this->updateEndGrade,
            ]);

            $this->showModelEditInsGradeSpans = false;

            session()->flash('message', 'Grade Span updated successfully.');

            $this->reset([
                'editGradeSpansId',
                'updateGradeSpanId',
                'updateGradeSpan',
                'updateStartGrade',
                'updateEndGrade',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('error', 'Database error: Unable to update grade span data. ' . $e->getMessage());

        } catch (\Throwable $e) {
            Log::error('Grade span update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $insgradespans = GradeSpan::orderBy('grade_span_id')->paginate(50);

        return view('livewire.main-tables.main-tables-institution-grade-spans', compact('insgradespans'));
    }
}
