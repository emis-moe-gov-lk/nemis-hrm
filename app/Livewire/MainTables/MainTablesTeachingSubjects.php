<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\SubjectList;
use Livewire\WithPagination;
use App\Models\ApointedSubject;
use Illuminate\Validation\Rule;

class MainTablesTeachingSubjects extends Component
{
    use WithPagination;

    public $showModelNewTeachingSubject = false; // control modal visibility
    public $showModelEditTeachingSubject = false; // control modal visibility

    public $teaching_subject_id, $teaching_subject_code, $teaching_subject_name_en, $teaching_subject_name_si, $teaching_subject_name_ta;
    public $subject_type = 1;
    public $grade_mask = '0000000000000';
    public $language_mask = '0000';
    public $category_mask = '0000000000000';

    public $update_teaching_subject_id, $update_teaching_subject_code, $update_teaching_subject_name_en, $update_teaching_subject_name_si, $update_teaching_subject_name_ta;
    public $update_subject_type;
    public $update_grade_mask;
    public $update_language_mask;
    public $update_category_mask;

    public $editTeachingSubjectId;

    public function editTeachingSubject($id)
    {
        $teaching_subject = SubjectList::findOrFail($id);

        $this->editTeachingSubjectId = $teaching_subject->id;
        $this->update_teaching_subject_id = $teaching_subject->subject_id;
        $this->update_teaching_subject_code = $teaching_subject->subject_code;
        $this->update_teaching_subject_name_en = $teaching_subject->name_en;
        $this->update_teaching_subject_name_si = $teaching_subject->name_si;
        $this->update_teaching_subject_name_ta = $teaching_subject->name_ta;
        $this->update_subject_type = $teaching_subject->type;
        $this->update_grade_mask = $teaching_subject->grade_mask;
        $this->update_language_mask = $teaching_subject->language_mask;
        $this->update_category_mask = $teaching_subject->category_mask;

        $this->showModelEditTeachingSubject = true; // ensure modal is open
    }

    public function updateTeachingSubject()
    {
        $this->validate([
            'update_teaching_subject_id' => [
                'required',
                'string',
                'regex:/^[SUB]{3}\d{4}$/', // Example: SUB1234
                Rule::unique('subject_lists', 'subject_id')->ignore($this->editTeachingSubjectId),
            ],
            'update_teaching_subject_code' => [
                'nullable',
                'numeric',
            ],
            'update_teaching_subject_name_en' => [
                'required',
                'string',
                'max:255',
            ],
            'update_teaching_subject_name_si' => 'nullable|string|max:255',
            'update_teaching_subject_name_ta' => 'nullable|string|max:255',
            'update_subject_type' => 'required|integer',
            'update_grade_mask' => 'required|regex:/^[0-2]{13}$/',
            'update_language_mask' => 'required|regex:/^[0-1]{4}$/',
            'update_category_mask' => 'required|regex:/^[0-7]{13}$/',
        ]);

        $this->resetPage();

        SubjectList::where('id', $this->editTeachingSubjectId)->update([
            'subject_id' => $this->update_teaching_subject_id,
            'subject_code' => $this->update_teaching_subject_code,
            'name_en' => $this->update_teaching_subject_name_en,
            'name_si' => $this->update_teaching_subject_name_si,
            'name_ta' => $this->update_teaching_subject_name_ta,
            'type' => $this->update_subject_type,
            'grade_mask' => $this->update_grade_mask,
            'language_mask' => $this->update_language_mask,
            'category_mask' => $this->update_category_mask,
        ]);

        $this->showModelEditTeachingSubject = false;

        session()->flash('message', '✅ Teaching Subject updated successfully!');

        $this->reset(['update_teaching_subject_id', 'update_teaching_subject_code', 'update_teaching_subject_name_en', 'update_teaching_subject_name_si', 'update_teaching_subject_name_ta', 'update_subject_type', 'update_grade_mask', 'update_language_mask', 'update_category_mask', 'editTeachingSubjectId']);
    }


    protected function rules()
    {
        if ($this->editTeachingSubjectId) {
            // ✅ Editing existing record
            return [
                'update_teaching_subject_id' => [
                    'required',
                    'string',
                    'regex:/^[SUB]{3}\d{4}$/',
                    Rule::unique('subject_lists', 'subject_id')->ignore($this->editTeachingSubjectId),
                ],
                'update_teaching_subject_code' => [
                    'nullable',
                    'numeric',
                ],
                'update_teaching_subject_name_en' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'update_teaching_subject_name_si' => 'nullable|string|max:255',
                'update_teaching_subject_name_ta' => 'nullable|string|max:255',
                'update_subject_type' => 'required|integer|in:1,2,3',
                'update_grade_mask' => 'required|regex:/^[0-2]{13}$/',
                'update_language_mask' => 'required|regex:/^[0-1]{4}$/',
                'update_category_mask' => 'required|regex:/^[0-7]{13}$/',
            ];
        }

        return [
            'teaching_subject_id' => [
                'required',
                'string',
                'regex:/^[SUB]{3}\d{4}$/', // Example: SUB1234
                'unique:subject_lists,subject_id'
            ],
            'teaching_subject_code' => 'nullable|numeric',
            'teaching_subject_name_en' => 'required|string|max:255',
            'teaching_subject_name_si' => 'nullable|string|max:255',
            'teaching_subject_name_ta' => 'nullable|string|max:255',
            'subject_type' => 'required|integer|in:1,2,3',
            'grade_mask' => 'required|regex:/^[0-2]{13}$/',
            'language_mask' => 'required|regex:/^[0-1]{4}$/',
            'category_mask' => 'required|regex:/^[0-7]{13}$/',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->resetPage();
    }

    // 🔹 Submit form
    public function addNewTeachingSubject()
    {
        $validated = $this->validate();
        $this->resetPage();

        SubjectList::create([
            'subject_id'       => $this->teaching_subject_id,
            'subject_code'  => $this->teaching_subject_code,
            'name_en'  => $this->teaching_subject_name_en,
            'name_si'  => $this->teaching_subject_name_si,
            'name_ta'  => $this->teaching_subject_name_ta,
            'type' => $this->subject_type,
            'grade_mask' => $this->grade_mask,
            'language_mask' => $this->language_mask,
            'category_mask' => $this->category_mask,
        ]);

        session()->flash('message', '✅ New Teaching Subject added successfully!');
        // ✅ Close the modal
        $this->showModelNewTeachingSubject = false;

        $this->reset(['teaching_subject_id', 'teaching_subject_code', 'teaching_subject_name_en', 'teaching_subject_name_si', 'teaching_subject_name_ta', 'subject_type', 'grade_mask', 'language_mask', 'category_mask']);
    }

    public function deleteTeachingSubject($id)
    {
        $teaching_subject = SubjectList::find($id);

        if ($teaching_subject) {
            $teaching_subject->delete();
            session()->flash('message', 'Teaching Subject deleted successfully!');
        } else {
            session()->flash('message', 'Teaching Subject not found!');
        }
    }

    public function toggleStatus($id)
    {
        $teaching_subject = SubjectList::find($id);

        if ($teaching_subject) {
            // Toggle between 1 and 0
            $teaching_subject->active_status = $teaching_subject->active_status == '1' ? '0' : '1';
            $teaching_subject->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $teaching_subject->active_status == '1'
                    ? 'Teaching Subject activated successfully!'
                    : 'Teaching Subject deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $teaching_subjects = SubjectList::orderBy('subject_id')->paginate(50);
        return view('livewire.main-tables.main-tables-teaching-subjects', compact('teaching_subjects'));
    }
}
