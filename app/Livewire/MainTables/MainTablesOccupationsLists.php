<?php

namespace App\Livewire\MainTables;

use Livewire\Component;
use App\Models\OccupationsList;
use Illuminate\Validation\Rule;

class MainTablesOccupationsLists extends Component
{
    public $showModelNewOccupation = false; // control modal visibility
    public $showModelEditOccupation = false; // control modal visibility

    public $occupations_id, $occupations_name_en, $occupations_name_si, $occupations_name_ta;
    public $update_occupations_id, $update_occupations_name_en, $update_occupations_name_si, $update_occupations_name_ta;

    public $editOccupationId;

    public function editOccupation($id)
    {
        $occupation = OccupationsList::findOrFail($id);

        $this->editOccupationId = $occupation->id;
        $this->update_occupations_id = $occupation->occ_id;
        $this->update_occupations_name_en = $occupation->occ_name_en;
        $this->update_occupations_name_si = $occupation->occ_name_si;
        $this->update_occupations_name_ta = $occupation->occ_name_ta;

        $this->showModelEditOccupation = true; // ensure modal is open
    }

    public function updateOccupation()
    {
        $this->validate([
            'update_occupations_id' => [
                'required',
                'string',
                'regex:/^[OCID]{4}\d{3}$/', // Example: OCID123
                Rule::unique('occupations_lists', 'occ_id')->ignore($this->editOccupationId),
            ],
            'update_occupations_name_en' => [
                'required',
                'string',
                'max:255',
            ],
            'update_occupations_name_si' => 'nullable|string|max:255',
            'update_occupations_name_ta' => 'nullable|string|max:255',
        ]);

        OccupationsList::where('id', $this->editOccupationId)->update([
            'occ_id' => $this->update_occupations_id,
            'occ_name_en' => $this->update_occupations_name_en,
            'occ_name_si' => $this->update_occupations_name_si,
            'occ_name_ta' => $this->update_occupations_name_ta,
        ]);

        $this->showModelEditOccupation = false;

        session()->flash('message', '✅ Occupation updated successfully!');

        $this->reset(['update_occupations_id', 'update_occupations_name_en', 'update_occupations_name_si', 'update_occupations_name_ta', 'editOccupationId']);
    }


    protected function rules()
    {
        if ($this->editOccupationId) {
            // ✅ Editing existing record
            return [
                'update_occupations_id' => [
                    'required',
                    'string',
                    'regex:/^[OCID]{4}\d{3}$/',
                    Rule::unique('occupations_lists', 'occ_id')->ignore($this->editOccupationId),
                ],
                'update_occupations_name_en' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'update_occupations_name_si' => 'nullable|string|max:255',
                'update_occupations_name_ta' => 'nullable|string|max:255',
            ];
        }

        return [
            'occupations_id' => [
                'required',
                'string',
                'regex:/^[OCID]{4}\d{3}$/', // Example: OCID123
                'unique:occupations_lists,occ_id'
            ],
            'occupations_name_en' => 'required|string|max:255',
            'occupations_name_si' => 'nullable|string|max:255',
            'occupations_name_ta' => 'nullable|string|max:255',
        ];
    }

    // 🔹 Live validation as user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // 🔹 Submit form
    public function addNewOccupation()
    {
        $validated = $this->validate();

        OccupationsList::create([
            'occ_id'       => $this->occupations_id,
            'occ_name_en'  => $this->occupations_name_en,
            'occ_name_si'  => $this->occupations_name_si,
            'occ_name_ta'  => $this->occupations_name_ta,
        ]);

        session()->flash('message', '✅ New Occupation added successfully!');
        // ✅ Close the modal
        $this->showModelNewOccupation = false;

        $this->reset(['occupations_id', 'occupations_name_en', 'occupations_name_si', 'occupations_name_ta']);
    }

    public function deleteOccupation($id)
    {
        $occupation = OccupationsList::find($id);

        if ($occupation) {
            $occupation->delete();
            session()->flash('message', 'Occupation deleted successfully!');
        } else {
            session()->flash('message', 'Occupation not found!');
        }
    }

    public function toggleStatus($id)
    {
        $occupation = OccupationsList::find($id);

        if ($occupation) {
            // Toggle between 1 and 0
            $occupation->active_status = $occupation->active_status == '1' ? '0' : '1';
            $occupation->save();

            // Send notification to front-end
            $this->dispatch('status-updated', [
                'message' => $occupation->active_status == '1'
                    ? 'Occupation activated successfully!'
                    : 'Occupation deactivated successfully!',
            ]);
        }
    }

    public function render()
    {
        $occupations = OccupationsList::orderBy('occ_id')->paginate(50);
        return view('livewire.main-tables.main-tables-occupations-lists', compact('occupations'));
    }
}
