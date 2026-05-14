<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\EducationalQualificationGrade;

class MainTablesEducationalQualificationGrades extends Component
{
    public $showModelNewEduQualificationGrade = false;
    public $gradeId, $grade;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editGradeId) {
            // ✅ Editing existing record
            return [
                'updateGradeId' => [
                    'required',
                    'string',
                    'regex:/^[GRD]{3}\d{3}$/',
                    Rule::unique('educational_qualification_grades', 'grade_id')->ignore($this->editGradeId),
                ],
                'updateGrade' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ];
        }

        return [
            'gradeId' => [
                'required',
                'string',
                'regex:/^[GRD]{3}\d{3}$/',
                Rule::unique('educational_qualification_grades', 'grade_id'),
            ],
            'grade' => [
                'required',
                'string',
                'max:50',
            ],
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewEduQualificationGrade()
    {
        $validated = $this->validate();

        try{
            EducationalQualificationGrade::create([
                'grade_id' => $this->gradeId,
                'grade' => $this->grade,
            ]);

            session()->flash('message', '✅ New Educational Qualification Grade added successfully!');

            // ✅ Close modal
            $this->showModelNewEduQualificationGrade = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['grade_id', 'grade']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save educational qualification grade data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Educational qualification grade creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public function deleteEduQualificationGrade($id)
    {
        $eduQualificationGrade = EducationalQualificationGrade::find($id);

        if ($eduQualificationGrade) {
            $eduQualificationGrade->delete();
            session()->flash('message', 'Educational Qualification Grade deleted successfully!');
        } else {
            session()->flash('message', 'Educational Qualification Grade not found!');
        }
    }

    public function toggleStatus($id)
    {
        $eduQualificationGrade = EducationalQualificationGrade::find($id);

        if ($eduQualificationGrade) {
            // Toggle between 1 and 0
            $eduQualificationGrade->active_status = $eduQualificationGrade->active_status == '1' ? '0' : '1';
            $eduQualificationGrade->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $eduQualificationGrade->active_status == '1'
                    ? 'Educational Qualification Grade activated successfully!'
                    : 'Educational Qualification Grade deactivated successfully!',
            ]);
        }
    }

    public $showModelEditEduQualificationGrade = false;
    public $editGradeId, $updateGradeId, $updateGrade;


    public function editEduQualificationGrade($id)
    {
        $eduQualificationGrade = EducationalQualificationGrade::findOrFail($id);

        $this->editGradeId = $eduQualificationGrade->id;
        $this->updateGradeId = $eduQualificationGrade->grade_id;
        $this->updateGrade = $eduQualificationGrade->grade;

        $this->showModelEditEduQualificationGrade = true; // ensure modal is open
    }

    public function updateEduQualificationGrade()
    {
        $this->validate([
            'updateGradeId' => [
                'required',
                'string',
                'regex:/^[GRD]{3}\d{3}$/',
                Rule::unique('educational_qualification_grades', 'grade_id')->ignore($this->editGradeId),
            ],
            'updateGrade' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        try{

            EducationalQualificationGrade::where('id', $this->editGradeId)->update([
                'grade_id' => $this->updateGradeId,
                'grade' => $this->updateGrade,
            ]);


            $this->showModelEditEduQualificationGrade = false;

            session()->flash('message', '✅ Educational Qualification Grade updated successfully!');

            $this->reset(['updateGradeId', 'updateGrade', 'editGradeId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update educational qualification grade data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Educational qualification grade update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $eduQualificationGrade = EducationalQualificationGrade::orderBy('grade_id')->paginate(50);
        return view('livewire.main-tables.main-tables-educational-qualification-grades', compact('eduQualificationGrade'));
    }
}
