<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\InstitutionType;
use Illuminate\Validation\Rule;

class MainTablesInstitutionTypes extends Component
{
    public $showModelNewInstitutionType = false; // control modal visibility
    public $showModelEditInstitutionType = false; // control modal visibility

    public $institution_types_id, $institution_types_name, $description;
    public $update_institution_types_id, $update_institution_types_name, $update_description;

    public $editInstitutionTypeId;

    public function editInstitutionType($id)
    {
        $institution_type = InstitutionType::findOrFail($id);

        $this->editInstitutionTypeId = $institution_type->id;
        $this->update_institution_types_id = $institution_type->institution_types_id;
        $this->update_institution_types_name = $institution_type->institution_types_name;
        $this->update_description = $institution_type->description;

        $this->showModelEditInstitutionType = true; // ensure modal is open
    }

    public function updateInstitutionType()
    {
        $this->validate([
            'update_institution_types_id' => [
                'required',
                'string',
                'regex:/^[ITID]{4}\d{2}$/', // Example: ITID12
                Rule::unique('institution_types', 'institution_types_id')->ignore($this->editInstitutionTypeId),
            ],
            'update_institution_types_name' => [
                'required',
                'string',
                'max:255',
            ],
            'update_description' => 'nullable|string|max:500',
        ]);

        InstitutionType::where('id', $this->editInstitutionTypeId)->update([
            'institution_types_id' => $this->update_institution_types_id,
            'institution_types_name' => $this->update_institution_types_name,
            'description' => $this->update_description,
        ]);

        $this->showModelEditInstitutionType = false;

        session()->flash('message', '✅ Institution Type updated successfully!');

        $this->reset(['update_institution_types_id', 'update_institution_types_name', 'update_description', 'editInstitutionTypeId']);
    }


    protected function rules()
    {
        if ($this->editInstitutionTypeId) {
            // ✅ Editing existing record
            return [
                'update_institution_types_id' => [
                    'required',
                    'string',
                    'regex:/^[ITID]{4}\d{2}$/',
                    Rule::unique('institution_types', 'institution_types_id')->ignore($this->editInstitutionTypeId),
                ],
                'update_institution_types_name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'update_description' => 'nullable|string|max:500',
            ];
        }

        return [
            'institution_types_id' => [
                'required',
                'string',
                'regex:/^[ITID]{4}\d{2}$/', // Example: AUID12
                'unique:institution_types,institution_types_id'
            ],
            'institution_types_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewInstitutionType()
    {
        $validated = $this->validate();

        InstitutionType::create($validated);

        session()->flash('message', '✅ New Institution Type added successfully!');
        // ✅ Close the modal
        $this->showModelNewInstitutionType = false;

        $this->reset(['institution_types_id', 'institution_types_name', 'description']);
    }

    public function deleteInstitutionType($id)
    {
        $institution_type = InstitutionType::find($id);

        if ($institution_type) {
            $institution_type->delete();
            session()->flash('message', 'Institution Type deleted successfully!');
        } else {
            session()->flash('message', 'Institution Type not found!');
        }
    }

    public function toggleStatus($id)
    {
        $institution_type = InstitutionType::find($id);

        if ($institution_type) {
            // Toggle between 1 and 0
            $institution_type->active_status = $institution_type->active_status == '1' ? '0' : '1';
            $institution_type->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $institution_type->active_status == '1'
                    ? 'Institution Type activated successfully!'
                    : 'Institution Type deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $institution_types = InstitutionType::orderBy('institution_types_id')->paginate(50);
        return view('livewire.main-tables.main-tables-institution-types', compact('institution_types'));
    }
}
