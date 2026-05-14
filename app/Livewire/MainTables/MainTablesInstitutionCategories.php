<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\InstitutionCategory;

class MainTablesInstitutionCategories extends Component
{
    public $showModelNewInsCategory = false; // control modal visibility
    public $showModelEditInsCategory = false; // control modal visibility

    public $institution_category_id, $institution_category_name, $description;
    public $update_institution_category_id, $update_institution_category_name, $update_description;

    public $editCategoryId;

    public function editInsCategory($id)
    {
        $inscategory = InstitutionCategory::findOrFail($id);

        $this->editCategoryId = $inscategory->id;
        $this->update_institution_category_id = $inscategory->institution_category_id;
        $this->update_institution_category_name = $inscategory->institution_category_name;
        $this->update_description = $inscategory->description;

        $this->showModelEditInsCategory = true; // ensure modal is open
    }

    public function updateInsCategory()
    {
        $this->validate([
            'update_institution_category_id' => [
                'required',
                'string',
                'regex:/^[CICD]{4}\d{3}$/', // Example: CICD1234
                Rule::unique('institution_categories', 'institution_category_id')->ignore($this->editCategoryId),
            ],
            'update_institution_category_name' => [
                'required',
                'string',
                'max:255',
            ],
            'update_description' => 'nullable|string|max:500',
        ]);

        InstitutionCategory::where('id', $this->editCategoryId)->update([
            'institution_category_id' => $this->update_institution_category_id,
            'institution_category_name' => $this->update_institution_category_name,
            'description' => $this->update_description,
        ]);

        $this->showModelEditInsCategory = false;

        session()->flash('message', '✅ Institution Category updated successfully!');

        $this->reset(['update_institution_category_id', 'update_institution_category_name', 'update_description', 'editCategoryId']);
    }


    protected function rules()
    {
        if ($this->editCategoryId) {
            // ✅ Editing existing record
            return [
                'update_institution_category_id' => [
                    'required',
                    'string',
                    'regex:/^[CICD]{4}\d{3}$/',
                    Rule::unique('institution_categories', 'institution_category_id')->ignore($this->editCategoryId),
                ],
                'update_institution_category_name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'update_description' => 'nullable|string|max:500',
            ];
        }

        return [
            'institution_category_id' => [
                'required',
                'string',
                'regex:/^[CICD]{4}\d{3}$/', // Example: CICD1234
                'unique:institution_categories,institution_category_id'
            ],
            'institution_category_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewInsCategory()
    {
        $validated = $this->validate();

        InstitutionCategory::create($validated);

        session()->flash('message', '✅ New Institution Category added successfully!');
        // ✅ Close the modal
        $this->showModelNewInsCategory = false;

        $this->reset(['institution_category_id', 'institution_category_name', 'description']);
    }

    public function deleteInsCategory($id)
    {
        $inscategory = InstitutionCategory::find($id);

        if ($inscategory) {
            $inscategory->delete();
            session()->flash('message', 'Institution Category deleted successfully!');
        } else {
            session()->flash('message', 'Category not found!');
        }
    }

    public function toggleStatus($id)
    {
        $inscategory = InstitutionCategory::find($id);

        if ($inscategory) {
            // Toggle between 1 and 0
            $inscategory->active_status = $inscategory->active_status == '1' ? '0' : '1';
            $inscategory->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $inscategory->active_status == '1'
                    ? 'Institution Category activated successfully!'
                    : 'Institution Category deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $inscategories = InstitutionCategory::orderBy('institution_category_id', 'asc')->paginate(50);
        return view('livewire.main-tables.main-tables-institution-categories', compact('inscategories'));
    }
}
