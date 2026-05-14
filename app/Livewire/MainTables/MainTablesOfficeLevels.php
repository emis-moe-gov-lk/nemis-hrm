<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\OfficeLevel;
use Illuminate\Validation\Rule;

class MainTablesOfficeLevels extends Component
{
    public $showModelNewOfficeLevel = false; // control modal visibility
    public $showModelEditOfficeLevel = false; // control modal visibility

    public $office_level_id, $office_level_name, $office_level_rank;
    public $update_office_level_id, $update_office_level_name, $update_office_level_rank;

    public $editOfficeLevelId;

    public function editOfficeLevel($id)
    {
        $office_level = OfficeLevel::findOrFail($id);

        $this->editOfficeLevelId = $office_level->id;
        $this->update_office_level_id = $office_level->office_level_id;
        $this->update_office_level_name = $office_level->office_level_name;
        $this->update_office_level_rank = $office_level->office_level_rank;

        $this->showModelEditOfficeLevel = true; // ensure modal is open
    }

    public function updateOfficeLevel()
    {
        $this->validate([
            'update_office_level_id' => [
                'required',
                'string',
                'regex:/^[OLID]{4}\d{3}$/', // Example: OLID123
                Rule::unique('office_levels', 'office_level_id')->ignore($this->editOfficeLevelId),
            ],
            'update_office_level_name' => [
                'required',
                'string',
                'max:255',
            ],
            'update_office_level_rank' => 'required|numeric|max:999',
        ]);

        OfficeLevel::where('id', $this->editOfficeLevelId)->update([
            'office_level_id' => $this->update_office_level_id,
            'office_level_name' => $this->update_office_level_name,
            'office_level_rank' => $this->update_office_level_rank,
        ]);

        $this->showModelEditOfficeLevel = false;

        session()->flash('message', '✅ Office Level updated successfully!');

        $this->reset(['update_office_level_id', 'update_office_level_name', 'update_office_level_rank', 'editOfficeLevelId']);
    }


    protected function rules()
    {
        if ($this->editOfficeLevelId) {
            // ✅ Editing existing record
            return [
                'update_office_level_id' => [
                    'required',
                    'string',
                    'regex:/^[OLID]{4}\d{3}$/',
                    Rule::unique('office_levels', 'office_level_id')->ignore($this->editOfficeLevelId),
                ],
                'update_office_level_name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'update_office_level_rank' => 'required|numeric|max:999',
            ];
        }

        return [
            'office_level_id' => [
                'required',
                'string',
                'regex:/^[OLID]{4}\d{3}$/', // Example: OLID123
                'unique:office_levels,office_level_id'
            ],
            'office_level_name' => 'required|string|max:255',
            'office_level_rank' => 'required|numeric|max:999',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewOfficeLevel()
    {
        $validated = $this->validate();

        OfficeLevel::create($validated);

        session()->flash('message', '✅ New Office Level added successfully!');
        // ✅ Close the modal
        $this->showModelNewOfficeLevel = false;

        $this->reset(['office_level_id', 'office_level_name', 'office_level_rank']);
    }

    public function deleteOfficeLevel($id)
    {
        $office_level = OfficeLevel::find($id);

        if ($office_level) {
            $office_level->delete();
            session()->flash('message', 'Office Level deleted successfully!');
        } else {
            session()->flash('message', 'Office Level not found!');
        }
    }

    public function toggleStatus($id)
    {
        $office_level = OfficeLevel::find($id);

        if ($office_level) {
            // Toggle between 1 and 0
            $office_level->active_status = $office_level->active_status == '1' ? '0' : '1';
            $office_level->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $office_level->active_status == '1'
                    ? 'Office Level activated successfully!'
                    : 'Office Level deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $office_levels = OfficeLevel::orderBy('office_level_rank', 'asc')->paginate(50);
        return view('livewire.main-tables.main-tables-office-levels', compact('office_levels'));
    }
}
