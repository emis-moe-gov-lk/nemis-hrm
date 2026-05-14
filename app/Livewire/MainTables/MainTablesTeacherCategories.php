<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\TeacherCategory;
use Illuminate\Validation\Rule;

class MainTablesTeacherCategories extends Component
{
    public $showModelNewTeacherCategory = false; // control modal visibility
    public $showModelEditTeacherCategory = false; // control modal visibility

    public $categories_id, $name, $description;
    public $update_categories_id, $update_name, $update_description;

    public $editTeacherCategoryId;

    public function editTeacherCategory($id)
    {
        $teacherCategory = TeacherCategory::findOrFail($id);

        $this->editTeacherCategoryId = $teacherCategory->id;
        $this->update_categories_id = $teacherCategory->categories_id;
        $this->update_name = $teacherCategory->name;
        $this->update_description = $teacherCategory->description;

        $this->showModelEditTeacherCategory = true; // ensure modal is open
    }

    public function updateTeacherCategory()
    {
        $this->validate([
            'update_categories_id' => [
                'required',
                'string',
                'regex:/^[TCAT]{4}\d{4}$/', // Example: TCAT1234
                Rule::unique('teacher_categories', 'categories_id')->ignore($this->editTeacherCategoryId),
            ],
            'update_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('teacher_categories', 'name')->ignore($this->editTeacherCategoryId),
            ],
            'update_description' => 'required|string|max:500',
        ]);

        TeacherCategory::where('id', $this->editTeacherCategoryId)->update([
            'categories_id' => $this->update_categories_id,
            'name' => $this->update_name,
            'description' => $this->update_description,
        ]);

        $this->showModelEditTeacherCategory = false;

        session()->flash('message', '✅ Teacher Category updated successfully!');

        $this->reset(['update_categories_id', 'update_name', 'update_description', 'editTeacherCategoryId']);
    }


    protected function rules()
    {
        if ($this->editTeacherCategoryId) {
            // ✅ Editing existing record
            return [
                'update_categories_id' => [
                    'required',
                    'string',
                    'regex:/^[TCAT]{4}\d{4}$/',
                    Rule::unique('teacher_categories', 'categories_id')->ignore($this->editTeacherCategoryId),
                ],
                'update_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('teacher_categories', 'name')->ignore($this->editTeacherCategoryId),
                ],
                'update_description' => 'required|string|max:500',
            ];
        }

        return [
            'categories_id' => [
                'required',
                'string',
                'regex:/^[TCAT]{4}\d{4}$/', // Example: TCAT1234
                'unique:teacher_categories,categories_id'
            ],
            'name' => 'required|string|max:255|unique:teacher_categories,name',
            'description' => 'required|string|max:500',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewTeacherCategory()
    {
        $validated = $this->validate();

        TeacherCategory::create(
            [
                'categories_id' => $validated['categories_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]
        );

        session()->flash('message', '✅ New Teacher Category added successfully!');
        // ✅ Close the modal
        $this->showModelNewTeacherCategory = false;

        $this->reset(['categories_id', 'name', 'description']);
    }

    public function deleteTeacherCategory($id)
    {
        $teacherCategory = TeacherCategory::find($id);

        if ($teacherCategory) {
            $teacherCategory->delete();
            session()->flash('message', 'Teacher Category deleted successfully!');
        } else {
            session()->flash('message', 'Teacher Category not found!');
        }
    }

    public function toggleStatus($id)
    {
        $teacherCategory = TeacherCategory::find($id);

        if ($teacherCategory) {
            // Toggle between 1 and 0
            $teacherCategory->active_status = $teacherCategory->active_status == '1' ? '0' : '1';
            $teacherCategory->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $teacherCategory->active_status == '1'
                    ? 'Teacher Category activated successfully!'
                    : 'Teacher Category deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $teacherCategories = TeacherCategory::orderBy('categories_id')->paginate(50);
        return view('livewire.main-tables.main-tables-teacher-categories', compact('teacherCategories'));
    }
}
