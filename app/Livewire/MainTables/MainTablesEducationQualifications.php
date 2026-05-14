<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\EducationQualification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainTablesEducationQualifications extends Component
{

    public function toggleStatus($id)
    {
        $staEQLevel = EducationQualification::find($id);

        if ($staEQLevel) {
            // Toggle between 1 and 0
            $staEQLevel->active_status = $staEQLevel->active_status == '1' ? '0' : '1';
            $staEQLevel->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $staEQLevel->active_status == '1'
                    ? 'Education Qualification activated successfully!'
                    : 'Education Qualification deactivated successfully!',
            ]);
        }
    }

    public function deleteEQLevel($id)
    {
        $delEQLevel = EducationQualification::find($id);

        if ($delEQLevel) {
            $delEQLevel->delete();
            session()->flash('message', 'Education Qualification deleted successfully!');
        } else {
            session()->flash('message', 'Education Qualification not found!');
        }
    }

    public $showModelNewEducationQualification = false;
    public $educationQualificationId, $qualificationOrder, $slqfl, $nvql, $educationQualification;

    protected function rules()
    {
        if($this->editeducationQualificationId){
            return [
                'updateEducationQualificationId' => [
                    'required',
                    'string',
                    'regex:/^EQ\d{3}$/', // Matches CT followed by 5 digits (EQ999)
                    'max:5',
                    Rule::unique('education_qualifications', 'qualifications_id')->ignore($this->editeducationQualificationId),

                ],
                'updateSlqfl' => 'required|string|max:6',
                'updateNvql' => 'nullable|string|max:5',
                'updateQualificationOrder' => 'required|integer|max:999',
                'updateEducationQualification' => 'required|string|max:255',
            ];
        }

        return [
            'educationQualificationId' => [
                'required',
                'string',
                'regex:/^EQ\d{3}$/', // Matches CT followed by 5 digits (EQ999)
                'max:5',
                'unique:education_qualifications,qualifications_id',
            ],
            'slqfl' => 'required|string|max:6',
            'nvql' => 'nullable|string|max:5',
            'qualificationOrder' => 'required|integer|max:999',
            'educationQualification' => 'required|string|max:255',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewEducationQualification()
    {
        $validated = $this->validate();

        try{
            EducationQualification::create([
                'qualifications_id' => $this->educationQualificationId,
                'rank' => $this->qualificationOrder,
                'slql' => $this->slqfl,
                'nvql' => $this->nvql,
                'qualification' => $this->educationQualification,
            ]);

            session()->flash('message', '✅ New Education Qualification added successfully!');

            // ✅ Close modal
            $this->showModelNewEducationQualification = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['educationQualificationId', 'qualificationOrder', 'slqfl', 'nvql', 'educationQualification']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save Education Qualification data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Education Qualification creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public $showModelEditEducationQualification = false;
    public $updateEducationQualificationId, $updateSlqfl, $updateNvql, $updateQualificationOrder, $updateEducationQualification;
    public $editeducationQualificationId;

    public function editEQLevel($id)
    {
        $educationQualify = EducationQualification::findOrFail($id);

        $this->editeducationQualificationId = $educationQualify->id;

        $this->updateEducationQualificationId = $educationQualify->qualifications_id;
        $this->updateSlqfl = $educationQualify->slql;
        $this->updateNvql = $educationQualify->nvql;
        $this->updateQualificationOrder = $educationQualify->rank;
        $this->updateEducationQualification = $educationQualify->qualification;

        $this->showModelEditEducationQualification = true; // ensure modal is open
    }

    public function updateEducationQualificationlist()
    {
        try{

            $this->validate([
                'updateEducationQualificationId' => [
                    'required',
                    'string',
                    'regex:/^EQ\d{3}$/', // Matches CT followed by 5 digits (EQ999)
                    'max:5',
                    Rule::unique('education_qualifications', 'qualifications_id')->ignore($this->editeducationQualificationId),
                ],
                'updateSlqfl' => 'required|string|max:6',
                'updateNvql' => 'nullable|string|max:5',
                'updateQualificationOrder' => 'required|integer|max:999',
                'updateEducationQualification' => 'required|string|max:255',
            ]);

            EducationQualification::where('id', $this->editeducationQualificationId)->update([
                'qualifications_id' => $this->updateEducationQualificationId,
                'slql' => $this->updateSlqfl,
                'nvql' => $this->updateNvql,
                'rank' => $this->updateQualificationOrder,
                'qualification' => $this->updateEducationQualification,
            ]);


            $this->showModelEditEducationQualification = false;

            session()->flash('message', '✅ Education Qualification information updated successfully!');

            $this->reset(['updateEducationQualificationId', 'updateSlqfl', 'updateNvql', 'updateQualificationOrder', 'updateEducationQualification','editeducationQualificationId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update Education Qualification data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Education Qualification update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $eqList = EducationQualification::orderBy('rank')->paginate(50);
        return view('livewire.main-tables.main-tables-education-qualifications',compact('eqList'));
    }
}
