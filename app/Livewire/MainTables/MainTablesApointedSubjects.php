<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ApointedSubject;
use Illuminate\Validation\Rule;

class MainTablesApointedSubjects extends Component
{
    use WithPagination;

    public $showModelNewApointedSubject = false; // control modal visibility
    public $showModelEditApointedSubject = false; // control modal visibility

    public $apointed_subjects_id, $apointed_subjects_name_en, $apointed_subjects_name_si, $apointed_subjects_name_ta;
    public $update_apointed_subjects_id, $update_apointed_subjects_name_en, $update_apointed_subjects_name_si, $update_apointed_subjects_name_ta;

    public $editApointedSubjectId;

    public function editApointedSubject($id)
    {
        $apointed_subject = ApointedSubject::findOrFail($id);

        $this->editApointedSubjectId = $apointed_subject->id;
        $this->update_apointed_subjects_id = $apointed_subject->a_subject_id;
        $this->update_apointed_subjects_name_en = $apointed_subject->name_en;
        $this->update_apointed_subjects_name_si = $apointed_subject->name_si;
        $this->update_apointed_subjects_name_ta = $apointed_subject->name_ta;

        $this->showModelEditApointedSubject = true; // ensure modal is open
    }

    public function updateApointedSubject()
    {
        $this->validate([
            'update_apointed_subjects_id' => [
                'required',
                'string',
                'regex:/^[ASUB]{4}\d{4}$/', // Example: ASUB1234
                Rule::unique('apointed_subjects', 'a_subject_id')->ignore($this->editApointedSubjectId),
            ],
            'update_apointed_subjects_name_en' => [
                'required',
                'string',
                'max:255',
            ],
            'update_apointed_subjects_name_si' => 'nullable|string|max:255',
            'update_apointed_subjects_name_ta' => 'nullable|string|max:255',
        ]);

        $this->resetPage();

        ApointedSubject::where('id', $this->editApointedSubjectId)->update([
            'a_subject_id' => $this->update_apointed_subjects_id,
            'name_en' => $this->update_apointed_subjects_name_en,
            'name_si' => $this->update_apointed_subjects_name_si,
            'name_ta' => $this->update_apointed_subjects_name_ta,
        ]);

        $this->showModelEditApointedSubject = false;

        session()->flash('message', '✅ Apointed Subject updated successfully!');

        $this->reset(['update_apointed_subjects_id', 'update_apointed_subjects_name_en', 'update_apointed_subjects_name_si', 'update_apointed_subjects_name_ta', 'editApointedSubjectId']);
    }


    protected function rules()
    {
        if ($this->editApointedSubjectId) {
            // ✅ Editing existing record
            return [
                'update_apointed_subjects_id' => [
                    'required',
                    'string',
                    'regex:/^[ASUB]{4}\d{4}$/',
                    Rule::unique('apointed_subjects', 'a_subject_id')->ignore($this->editApointedSubjectId),
                ],
                'update_apointed_subjects_name_en' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'update_apointed_subjects_name_si' => 'nullable|string|max:255',
                'update_apointed_subjects_name_ta' => 'nullable|string|max:255',
            ];
        }

        return [
            'apointed_subjects_id' => [
                'required',
                'string',
                'regex:/^[ASUB]{4}\d{4}$/', // Example: ASUB1234
                'unique:apointed_subjects,a_subject_id'
            ],
            'apointed_subjects_name_en' => 'required|string|max:255',
            'apointed_subjects_name_si' => 'nullable|string|max:255',
            'apointed_subjects_name_ta' => 'nullable|string|max:255',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->resetPage();
    }

    // 🔹 Submit form
    public function addNewApointedSubject()
    {
        $validated = $this->validate();
        $this->resetPage();

        ApointedSubject::create([
            'a_subject_id'       => $this->apointed_subjects_id,
            'name_en'  => $this->apointed_subjects_name_en,
            'name_si'  => $this->apointed_subjects_name_si,
            'name_ta'  => $this->apointed_subjects_name_ta,
        ]);

        session()->flash('message', '✅ New Apointed Subject added successfully!');
        // ✅ Close the modal
        $this->showModelNewApointedSubject = false;

        $this->reset(['apointed_subjects_id', 'apointed_subjects_name_en', 'apointed_subjects_name_si', 'apointed_subjects_name_ta']);
    }

    public function deleteApointedSubject($id)
    {
        $apointed_subject = ApointedSubject::find($id);

        if ($apointed_subject) {
            $apointed_subject->delete();
            session()->flash('message', 'Apointed Subject deleted successfully!');
        } else {
            session()->flash('message', 'Apointed Subject not found!');
        }
    }

    public function toggleStatus($id)
    {
        $apointed_subject = ApointedSubject::find($id);

        if ($apointed_subject) {
            // Toggle between 1 and 0
            $apointed_subject->active_status = $apointed_subject->active_status == '1' ? '0' : '1';
            $apointed_subject->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $apointed_subject->active_status == '1'
                    ? 'Apointed Subject activated successfully!'
                    : 'Apointed Subject deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $apointed_subjects = ApointedSubject::orderBy('a_subject_id')->paginate(50);
        return view('livewire.main-tables.main-tables-apointed-subjects', compact('apointed_subjects'));
    }
}
