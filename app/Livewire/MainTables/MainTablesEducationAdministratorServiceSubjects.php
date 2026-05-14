<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\EducationAdministratorServiceSubject;

class MainTablesEducationAdministratorServiceSubjects extends Component
{
    public function deleteSleasSubject($id)
    {
        $sleasSubject = EducationAdministratorServiceSubject::find($id);

        if ($sleasSubject) {
            $sleasSubject->delete();
            session()->flash('message', 'SLEAS Category deleted successfully!');
        } else {
            session()->flash('message', 'SLEAS Category not found!');
        }
    }

    public function toggleStatus($id)
    {
        $sleasSubject = EducationAdministratorServiceSubject::find($id);

        if ($sleasSubject) {
            // Toggle between 1 and 0
            $sleasSubject->active_status = $sleasSubject->active_status == '1' ? '0' : '1';
            $sleasSubject->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $sleasSubject->active_status == '1'
                    ? 'SLEAS Subject activated successfully!'
                    : 'SLEAS Subject deactivated successfully!',
            ]);
        }
    }

    public $showModelNewSleasSubject = false;
    public $sleasSubjectId, $sleasSubject;

    // 🔹 Validation rules
    protected function rules()
    {
        if ($this->editSleasSubjectId) {
            // ✅ Editing existing record
            return [
                'updateSleasSubjectId' => [
                    'required',
                    'string',
                    'regex:/^[EAS]{3}\d{3}$/',
                    Rule::unique('education_administrator_service_subjects', 'eas_subject_id')->ignore($this->editSleasSubjectId),
                ],
                'updateSleasSubject' => [
                    'required',
                    'string',
                ],
            ];
        }

        return [
            'sleasSubjectId' => [
                'required',
                'string',
                'regex:/^[EAS]{3}\d{3}$/',
                Rule::unique('education_administrator_service_subjects', 'eas_subject_id'),
            ],
            'sleasSubject' => [
                'required',
                'string',
            ],
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewSleasSubject()
    {
        $validated = $this->validate();

        try{
            EducationAdministratorServiceSubject::create([
                'eas_subject_id' => $this->sleasSubjectId,
                'subject' => $this->sleasSubject,
            ]);

            session()->flash('message', '✅ New SLEAS Subject added successfully!');

            // ✅ Close modal
            $this->showModelNewSleasSubject = false;

            // ✅ Reset form fields (but keep modal control variable)
            $this->reset(['sleasSubjectId', 'sleasSubject']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to save SLEAS Subject data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('SLEAS Subject creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }

    }

    public $showModelEditSleasSubject = false;
    public $editSleasSubjectId, $updateSleasSubjectId, $updateSleasSubject;


    public function editSleasSubject($id)
    {
        $sleasSubject = EducationAdministratorServiceSubject::findOrFail($id);

        $this->editSleasSubjectId = $sleasSubject->id;
        $this->updateSleasSubjectId = $sleasSubject->eas_subject_id;
        $this->updateSleasSubject = $sleasSubject->subject;

        $this->showModelEditSleasSubject = true; // ensure modal is open
    }

    public function updateSleasSubjectList()
    {
        $this->validate([
            'updateSleasSubjectId' => [
                'required',
                'string',
                'regex:/^[EAS]{3}\d{3}$/',
                Rule::unique('education_administrator_service_subjects', 'eas_subject_id')->ignore($this->editSleasSubjectId),
            ],
            'updateSleasSubject' => [
                'required',
                'string',
            ],
        ]);

        try{

            EducationAdministratorServiceSubject::where('id', $this->editSleasSubjectId)->update([
                'eas_subject_id' => $this->updateSleasSubjectId,
                'subject' => $this->updateSleasSubject,
            ]);


            $this->showModelEditSleasSubject = false;

            session()->flash('message', '✅ SLEAS Subject updated successfully!');

            $this->reset(['updateSleasSubjectId', 'updateSleasSubject', 'editSleasSubjectId']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            session()->flash('error', 'Validation error: Please check your input data.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            session()->flash('error', 'Database error: Unable to update SLEAS Subject data.'. $e->getMessage());

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('SLEAS Subject update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'System error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $sleasSubjects = EducationAdministratorServiceSubject::orderBy('eas_subject_id')->paginate(50);
        return view('livewire.main-tables.main-tables-education-administrator-service-subjects', compact('sleasSubjects'));
    }
}
